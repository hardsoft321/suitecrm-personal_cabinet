<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author  Evgeny Pervushin <pea@lab321.ru>
 */
function post_install() {
    require_once 'modules/Configurator/Configurator.php';
    $configuratorObj = new Configurator();
    $configuratorObj->loadConfig();

    $newConfig = array(
        'dedicated_bean_role_default' => findIdByName('ACLRoles', 'default'),
        'default_theme' => 'SuiteR-PC',
        'securitysuite_inherit_creator' => true,
        'securitysuite_user_popup' => false,
        'calculate_response_time' => false,
    );
    foreach($newConfig as $name => $value) {
        $configuratorObj->config[$name] = $value;
    }

    require_once 'include/SugarTheme/SugarTheme.php';
    $disabled_themes = empty($configuratorObj->config['disabled_themes']) ? array() : explode(',', $configuratorObj->config['disabled_themes']);
    foreach(SugarThemeRegistry::availableThemes() as $theme => $v) {
        if(substr($theme, -3) != '-PC') {
            $disabled_themes[] = $theme;
        }
    }
    $configuratorObj->config['disabled_themes'] = implode(',', $disabled_themes);

    $configuratorObj->saveConfig();
}

function findIdByName($module, $name)
{
    $beans = BeanFactory::newBean($module)->get_full_list("", "name = '$name'");
    if(empty($beans)) {
        sugar_die("Не найдена запись $module с именем '$name'");
    }
    if(count($beans) > 1) {
        sugar_die("Найдено несколько записей $module с именем '$name'");
    }
    $bean = reset($beans);
    return $bean->id;
}
