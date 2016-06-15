<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $type       = $rlValid->xSql($_GET['type']);
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $id         = $rlValid->xSql($_GET['id']);
        $key        = $rlValid->xSql($_GET['key']);
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'currency_rate');
        $reefless->loadClass('CurrencyConverter', null, 'currencyConverter');
        $rlCurrencyConverter->updateHook();
        exit;
    }
    $limit    = $rlValid->xSql($_GET['limit']);
    $start    = $rlValid->xSql($_GET['start']);
    $langCode = $rlValid->xSql($_GET['lang_code']);
    $phrase   = $rlValid->xSql($_GET['phrase']);
    $rlDb->setTable('currency_rate');
    $data = $rlDb->fetch('*', null, "ORDER BY `ID` ASC", array(
        $start,
        $limit
    ));
    $rlDb->resetTable();
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "currency_rate`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_group'] : $lang['edit_group'];
    }
    $reefless->loadClass('CurrencyConverter', null, 'currencyConverter');
    $rlXajax->registerFunction(array(
        'updateRate',
        $rlCurrencyConverter,
        'ajaxUpdateRate'
    ));
    $rlXajax->registerFunction(array(
        'addCurrency',
        $rlCurrencyConverter,
        'ajaxAddCurrency'
    ));
}