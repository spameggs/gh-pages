<?php
if ($_GET['q'] == 'ext') {
    require_once('../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $type  = $rlValid->xSql($_GET['type']);
        $field = $rlValid->xSql($_GET['field']);
        $value = $rlValid->xSql(nl2br($_GET['value']));
        $id    = $rlValid->xSql($_GET['id']);
        $key   = $rlValid->xSql($_GET['key']);
        if ($field == 'Default') {
            $value = ($value == 'true') ? '1' : '0';
        }
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlHook->load('apExtMapAmenitiesUpdate');
        $rlActions->updateOne($updateData, 'map_amenities');
        exit;
    }
    $limit     = $rlValid->xSql($_GET['limit']);
    $start     = $rlValid->xSql($_GET['start']);
    $condition = "WHERE `Status` <> 'trash' ORDER BY `Key` ASC";
    $rlHook->load('apExtMapAmenitiesSql');
    $rlDb->setTable('map_amenities');
    $data = $rlDb->fetch('*', null, $condition, array(
        $start,
        $limit
    ));
    $data = $rlLang->replaceLangKeys($data, 'map_amenities', array(
        'name'
    ), RL_LANG_CODE, 'admin');
    $rlDb->resetTable();
    foreach ($data as $key => $value) {
        $data[$key]['Status']  = $GLOBALS['lang'][$data[$key]['Status']];
        $data[$key]['Default'] = (bool) $data[$key]['Default'];
    }
    $rlHook->load('apExtMapAmenitiesData');
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "map_amenities` WHERE `Status` <> 'trash'");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $allLangs = $GLOBALS['languages'];
    $rlSmarty->assign_by_ref('allLangs', $allLangs);
    $rlXajax->registerFunction(array(
        'addAmenity',
        $rlAdmin,
        'ajaxAddAmenity'
    ));
    $rlXajax->registerFunction(array(
        'editAmenity',
        $rlAdmin,
        'ajaxEditAmenity'
    ));
    $rlXajax->registerFunction(array(
        'deleteAmenity',
        $rlAdmin,
        'ajaxDeleteAmenity'
    ));
    $rlHook->load('apPhpMapAmenitiesTop');
}