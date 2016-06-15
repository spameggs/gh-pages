<?php
if (isset($_POST['a_config'])) {
    $dConfig = $_POST['config'];
    if (isset($_POST['config']['template']) && ($_POST['config']['template']['value'] != $config['template'])) {
        $compile = $reefless->scanDir(RL_TMP . 'compile' . RL_DS);
        foreach ($compile as $file) {
            unlink(RL_TMP . 'compile' . RL_DS . $file);
        }
        $reefless->flTouch();
    }
    if (isset($_POST['config']['cache']) && $_POST['config']['cache']['value'] && !$config['cache']) {
        $config['cache'] = 1;
        $rlCache->update();
    }
    $update = array();
    foreach ($dConfig as $key => $value) {
        if ($value['d_type'] == 'int') {
            $value['value'] = (int) $value['value'];
        }
        $rlValid->sql($value['value']);
        $row['where']['Key']      = $key;
        $row['fields']['Default'] = $value['value'];
        array_push($update, $row);
    }
    $reefless->loadClass('Actions');
    $rlHook->load('apPhpConfigBeforeUpdate');
    if ($rlActions->update($update, 'config')) {
        $rlHook->load('apPhpConfigAfterUpdate');
        $reefless->loadClass('Notice');
        $aUrl = array(
            "controller" => $controller
        );
        if ($_POST['group_id']) {
            $aUrl['group'] = $_POST['group_id'];
        }
        $rlNotice->saveNotice($lang['config_saved']);
        $reefless->redirect($aUrl);
    }
}
$g_sql = "SELECT `T1`.*, `T2`.`Status` AS `Plugin_status` FROM `" . RL_DBPREFIX . "config_groups` AS `T1` ";
$g_sql .= "LEFT JOIN `" . RL_DBPREFIX . "plugins` AS `T2` ON `T1`.`Plugin` = `T2`.`Key` ";
$configGroups = $rlDb->getAll($g_sql);
$configGroups = $rlLang->replaceLangKeys($configGroups, 'config_groups', 'name', RL_LANG_CODE, 'admin');
$rlSmarty->assign_by_ref('configGroups', $configGroups);
foreach ($configGroups as $key => $value) {
    $groupIDs[] = $value['ID'];
}
$configsLsit = $rlDb->fetch('*', null, "WHERE `Group_ID` = '" . implode("' OR `Group_ID` = '", $groupIDs) . ")' ORDER BY `Position`", null, 'config');
$configsLsit = $rlLang->replaceLangKeys($configsLsit, 'config', array(
    'name',
    'des'
), RL_LANG_CODE, 'admin');
$rlAdmin->mixSpecialConfigs($configsLsit);
foreach ($configsLsit as $key => $value) {
    $configs[$value['Group_ID']][] = $value;
}
$rlSmarty->assign_by_ref('configs', $configs);
unset($configGroups, $configsLsit);
$rlHook->load('apPhpConfigBottom');