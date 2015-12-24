<?php
global $sugar_config;
$sugarVersion = isset($sugar_config['suitecrm_version']) ? 'Suite'.$sugar_config['suitecrm_version'] : $sugar_config['sugar_version'];
$manifest = array(
    'name' => 'personal_cabinet',
    'acceptable_sugar_versions' => array(),
    'acceptable_sugar_flavors' => array('CE'),
    'author' => 'hardsoft321',
    'description' => 'Модификация SuiteCRM до личного кабинета',
    'is_uninstallable' => true,
    'published_date' => '2015-12-11',
    'type' => 'module',
    'dependencies' => array(
        array(
            'id_name' => 'pc_roles',
            'version' => '1',
        ),
    ),
    'version' => '1.0.0',
);
$installdefs = array(
    'id' => 'personal_cabinet',
    'copy' => array(
        array(
            'from' => '<basepath>/source/copy',
            'to' => '.'
        ),
        array (
           'from' => "<basepath>/source/notupgradesafe/{$sugarVersion}/",
           'to' => '.',
        ),
    ),
    'entrypoints' => array(
        array(
            'from' => '<basepath>/source/entrypoints/entry_point_registry.Registration.php',
            'to_module' => 'application',
        ),
    ),
    'language' => array(
        array (
            'from' => '<basepath>/source/language/application/ru_ru.lang.php',
            'to_module' => 'application',
            'language' => 'ru_ru',
        ),
        array (
            'from' => '<basepath>/source/language/application/en_us.lang.php',
            'to_module' => 'application',
            'language' => 'en_us',
        ),
        array (
            'from' => '<basepath>/source/language/application/es_es.lang.php',
            'to_module' => 'application',
            'language' => 'es_es',
        ),
        array (
            'from' => '<basepath>/source/language/Users/ru_ru.personal_cabinet.php',
            'to_module' => 'Users',
            'language' => 'ru_ru',
        ),
        array (
            'from' => '<basepath>/source/language/Users/en_us.personal_cabinet.php',
            'to_module' => 'Users',
            'language' => 'en_us',
        ),
        array (
            'from' => '<basepath>/source/language/Users/es_es.personal_cabinet.php',
            'to_module' => 'Users',
            'language' => 'es_es',
        ),
    ),
    'linkdefs' => array(
        array(
            'from'=>'<basepath>/source/linkdefs/personal_cabinet.php',
        ),
    ),
    'logic_hooks' => array(
        array(
            'module' => 'Users',
            'hook' => 'after_save',
            'order' => 100,
            'description' => 'Add default role',
            'file' => 'custom/modules/Users/DefaultRole.php',
            'class' => 'DefaultRole',
            'function' => 'addToUser',
        ),
    ),
    'vardefs' => array(
        array(
            'from' => '<basepath>/source/vardefs/Users/personal_cabinet.php',
            'to_module' => 'Users',
        ),
    ),
);
