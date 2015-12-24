<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.

 * SuiteCRM is an extension to SugarCRM Community Edition developed by Salesagility Ltd.
 * Copyright (C) 2011 - 2014 Salesagility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 ********************************************************************************/

/*********************************************************************************

 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$user = BeanFactory::newBean('Users');
$account = BeanFactory::newBean('Accounts');
$errors = array();
$admin = new Administration();
$admin->retrieveSettings('captcha');

if(!empty($_POST['register'])) {
    if(!empty($_POST['first_name'])) {
        $user->first_name = $_POST['first_name'];
    }

    $last_name = trim($_POST['last_name']);
    if(!empty($last_name)) {
        $user->last_name = $last_name;
    }
    else {
        $errors[] = translate('ERR_MISSING_REQUIRED_FIELDS').' '.translate('LBL_LAST_NAME', 'Users');
    }

    $org_name = trim($_POST['org_name']);
    if(!empty($org_name)) {
        $account->name = $org_name;
    }
    else {
        $errors[] = translate('ERR_MISSING_REQUIRED_FIELDS').' '.translate('LBL_ORG_NAME', 'Users');
    }

    $email = trim($_POST['email']);
    if(!empty($email)) {
        $user->user_name = $email;
        $user->email1 = $email;
    }
    else {
        $errors[] = translate('ERR_MISSING_REQUIRED_FIELDS').' '.translate('LBL_EMAIL_ADDRESS_PRIMARY');
    }

    if(isset($admin->settings['captcha_on'])&& $admin->settings['captcha_on']=='1' && !empty($admin->settings['captcha_private_key']) && !empty($admin->settings['captcha_public_key'])) {
        $captcha_verified = false;
        if(!empty($_POST['g-recaptcha-response'])) {
            if($curl = curl_init()) {
                curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=".$admin->settings['captcha_private_key']."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER["REMOTE_ADDR"]);
                $out = curl_exec($curl);
                if($out) {
                    $res = json_decode($out, true);
                    if($res && !empty($res['success'])) {
                        $captcha_verified = true;
                    }
                }
                curl_close($curl);
            }
        }
        if(!$captcha_verified) {
            $errors[] = translate('ERR_CAPTCHA_FAILED', 'Users');
        }
    }

    $user2 = BeanFactory::newBean('Users')->retrieve_by_string_fields(array('user_name' => $user->user_name));
    if($user2) {
        $errors[] = translate('ERR_EMAIL_USER_EXISTS', 'Users');
    }

    if(empty($errors)) {
        $user->status = 'Active';
        $user->employee_status = 'Active';
        $user->save();

        $account->assigned_user_id = $user->id;
        $account->save();

        if($user->load_relationship('SecurityGroups')) {
            $group = BeanFactory::newBean('SecurityGroups');
            $group->name = 'USER_'.$user->id;
            $group->save();
            $user->SecurityGroups->add($group);
            $user->save();
            if($account->load_relationship('SecurityGroups')) {
                $account->SecurityGroups->add($group);
                $account->save();
            }
        }

        $result = $user->sendEmailForPassword($GLOBALS['sugar_config']['passwordsetting']['generatepasswordtmpl'], array(
            'link' => false,
            'password' => User::generatePassword(),
        ));
        if ($result['status'] == false && $result['message'] != '') {
            sugar_die($result['message']);
        }
        SugarApplication::redirect('index.php?module=Users&action=Login&loginErrorMessage=LBL_NEW_USER_PASSWORD_REG&default_user_name='.$user->user_name);
        return;
    }
}

$authController = new AuthenticationController();
$authController->authController->pre_login(); //редирект, если залогинен

require_once('include/MVC/View/SugarView.php');
$view= new SugarView();
$view->init();
$view->action = 'Login'; //чтобы отключить лишнее
$view->displayHeader();

global $current_language, $mod_strings, $app_strings;
if(isset($_REQUEST['login_language'])){
    $lang = $_REQUEST['login_language'];
    $_REQUEST['ck_login_language_20'] = $lang;
        $current_language = $lang;
    $_SESSION['authenticated_user_language'] = $lang;
    $mod_strings = return_module_language($lang, "Users");
    $app_strings = return_application_language($lang);
}
$sugar_smarty = new Sugar_Smarty();
echo '<link rel="stylesheet" type="text/css" media="all" href="'.getJSPath('modules/Users/login.css').'">';
echo '<script type="text/javascript" src="'.getJSPath('modules/Users/login.js').'"></script>';
global $app_language, $sugar_config;
//we don't want the parent module's string file, but rather the string file specifc to this subpanel
global $current_language;

// Get the login page image
if ( sugar_is_file('custom/include/images/sugar_md.png') ) {
    $login_image = '<IMG src="custom/include/images/sugar_md.png" alt="Sugar" width="340" height="25">';
}
else {
    $login_image = '<IMG src="include/images/sugar_md_open.png" alt="Sugar" width="340" height="25" style="margin: 5px 0;">';
}

$login_image_url = SugarThemeRegistry::current()->getImageURL('company_logo.png');
$login_image = '<IMG src="'.$login_image_url.'" alt="SuiteCRM" style="margin: 5px 0;">';


$sugar_smarty->assign('LOGIN_IMAGE',$login_image);

// See if any messages were passed along to display to the user.
if(isset($_COOKIE['loginErrorMessage'])) {
    if ( !isset($_REQUEST['loginErrorMessage']) ) {
        $_REQUEST['loginErrorMessage'] = $_COOKIE['loginErrorMessage'];
    }
    SugarApplication::setCookie('loginErrorMessage', '', time()-42000, '/');
}
if(isset($_REQUEST['loginErrorMessage'])) {
    if (isset($mod_strings[$_REQUEST['loginErrorMessage']])) {
        echo "<p align='center' class='error' > ". $mod_strings[$_REQUEST['loginErrorMessage']]. "</p>";
    } else if (isset($app_strings[$_REQUEST['loginErrorMessage']])) {
        echo "<p align='center' class='error' > ". $app_strings[$_REQUEST['loginErrorMessage']]. "</p>";
    }
}

if(!empty($_POST['register'])) {
    $sugar_smarty->assign('REG_FIRST_NAME', $_REQUEST['first_name']);
    $sugar_smarty->assign('REG_LAST_NAME', $_REQUEST['last_name']);
    $sugar_smarty->assign('REG_ORG_NAME', $_REQUEST['org_name']);
    $sugar_smarty->assign('REG_EMAIL', $_REQUEST['email']);
}
$sugar_smarty->assign('USER_FIELD_DEFS', $user->getFieldDefinitions());
$sugar_smarty->assign('ACC_FIELD_DEFS', $account->getFieldDefinitions());

$mod_strings['VLD_ERROR'] = $GLOBALS['app_strings']["\x4c\x4f\x47\x49\x4e\x5f\x4c\x4f\x47\x4f\x5f\x45\x52\x52\x4f\x52"];

$sugar_smarty->assign('REG_ERROR', empty($errors) ? '' : implode('<br/>', $errors));

if (isset($_REQUEST['ck_login_language_20'])) {
        $display_language = $_REQUEST['ck_login_language_20'];
} else {
        $display_language = $sugar_config['default_language'];
}

if (empty($GLOBALS['sugar_config']['passwordsetting']['forgotpasswordON']))
        $sugar_smarty->assign('DISPLAY_FORGOT_PASSWORD_FEATURE','none');

$the_languages = get_languages();
if ( count($the_languages) > 1 )
    $sugar_smarty->assign('SELECT_LANGUAGE', get_select_options_with_id($the_languages, $display_language));
$the_themes = SugarThemeRegistry::availableThemes();
if ( !empty($logindisplay) )
        $sugar_smarty->assign('LOGIN_DISPLAY', $logindisplay);;

// RECAPTCHA
// if the admin set the captcha stuff, assign javascript and div
if(isset($admin->settings['captcha_on'])&& $admin->settings['captcha_on']=='1' && !empty($admin->settings['captcha_private_key']) && !empty($admin->settings['captcha_public_key'])){
    $captcha_publickey = $admin->settings['captcha_public_key'];
    $sugar_smarty->assign('CAPTCHA_PUBLICKEY', $captcha_publickey);
    $langParts = explode('_', $display_language);
    $sugar_smarty->assign('CAPTCHA_LANG', reset($langParts));
}

if (file_exists('themes/'.SugarThemeRegistry::current().'/tpls/register.tpl')) {
        echo $sugar_smarty->display('themes/'.SugarThemeRegistry::current().'/tpls/register.tpl');
} else {
        echo $sugar_smarty->display('custom/modules/Users/register.tpl');
}

$view->displayFooter();
