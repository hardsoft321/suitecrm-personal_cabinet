{*
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

 ********************************************************************************/
*}
{literal}
<style>
#loginform.regform {max-width:500px;}
</style>
{/literal}
<!-- Start login container -->
<div class="container">
    <div id="loginform" class="regform">
    <form class="form-signin" role="form" action="index.php?entryPoint=register" method="post" name="DetailView" id="form">
        <div class="companylogo"><a href="index.php">{$LOGIN_IMAGE}</a></div>
        <span class="error" id="browser_warning" style="display:none">
            {sugar_translate label="WARN_BROWSER_VERSION_WARNING"}
        </span>
        <span class="error" id="ie_compatibility_mode_warning" style="display:none">
        {sugar_translate label="WARN_BROWSER_IE_COMPATIBILITY_MODE_WARNING"}
        </span>
        {if $REG_ERROR !=''}
            <span class="error">{$REG_ERROR}</span>
        {else}
            <span id='post_error' class="error"></span>
        {/if}
        <br>
        {if !empty($SELECT_LANGUAGE)}
                {sugar_translate module="Users" label="LBL_LANGUAGE"}:
                <select name='login_language' onchange="switchLanguage(this.value)">{$SELECT_LANGUAGE}</select>
        {/if}
        <br>
        <br>
        <div class="input-group">
            <span class="input-group-addon logininput glyphicon glyphicon-user"></span>
            <input type="text" class="form-control" placeholder="{sugar_translate module="Users" label="LBL_FIRST_NAME"}"
                title="{sugar_translate module="Users" label="LBL_FIRST_NAME"}" autofocus tabindex="1"
                id="first_name" name="first_name" value='{$REG_FIRST_NAME}' maxlength="{$USER_FIELD_DEFS.first_name.len}" />
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon logininput glyphicon glyphicon-user"></span>
            <input type="text" class="form-control" placeholder="{sugar_translate module="Users" label="LBL_LAST_NAME"}"
                title="{sugar_translate module="Users" label="LBL_LAST_NAME"}" required tabindex="2"
                id="last_name" name="last_name" value='{$REG_LAST_NAME}' maxlength="{$USER_FIELD_DEFS.last_name.len}" />
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon logininput glyphicon glyphicon-home"></span>
            <input type="text" class="form-control" placeholder="{sugar_translate module="Users" label="LBL_ORG_NAME"}"
                title="{sugar_translate module="Users" label="LBL_ORG_NAME"}" required tabindex="3"
                id="org_name" name="org_name" value='{$REG_ORG_NAME}' maxlength="{$ACC_FIELD_DEFS.name.len}" />
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon logininput glyphicon glyphicon-envelope"></span>
            <input type="email" class="form-control" placeholder="{sugar_translate module="Users" label="LBL_EMAIL"}"
                title="{sugar_translate module="Users" label="LBL_EMAIL"}" required tabindex="4"
                id="email" name="email" value='{$REG_EMAIL}' maxlength="255" />
        </div>
        <br>
        {if !empty($CAPTCHA_PUBLICKEY)}
        <script src="https://www.google.com/recaptcha/api.js?hl={$CAPTCHA_LANG}" async defer></script>
        <div class="g-recaptcha" data-sitekey="{$CAPTCHA_PUBLICKEY}" data-tabindex="5"></div>
        {/if}
        <br>
        <input id="bigbutton" class="btn btn-lg btn-primary btn-block" type="submit" tabindex="6" name="register" value="{sugar_translate module="Users" label="LBL_REGISTER_BTN"}" />
    </form>
    </div>
</div>
<!-- End login container -->
