<?php
$rlAdmin->assignBlocks();
$rlAdmin->getStatistics();
$rlSmarty->assign_by_ref('plugin_statistics', $plugin_statistics);
foreach ($languages as $key => $value) {
    $sett_languages[$key] = array(
        'Key' => $value['Code'],
        'name' => $value['name']
    );
}
$desktop_settings = array(
    array(
        'Key' => 'lang',
        'Type' => 'select',
        'Name' => $lang['language'],
        'Default' => RL_LANG_CODE,
        'Values' => $sett_languages,
        'Deny' => true
    ),
    array(
        'Key' => 'admin_hide_denied_items',
        'Type' => 'bool',
        'Name' => $lang['config+name+admin_hide_denied_items'],
        'Default' => $config['admin_hide_denied_items']
    )
);
$rlSmarty->assign_by_ref('desktop_settings', $desktop_settings);
$rlHook->load('apPhpHome');
$rlXajax->registerFunction(array(
    'saveConfig',
    $rlAdmin,
    'ajaxSaveConfig'
));
$rlXajax->registerFunction(array(
    'install',
    $rlPlugin,
    'ajaxInstall'
));