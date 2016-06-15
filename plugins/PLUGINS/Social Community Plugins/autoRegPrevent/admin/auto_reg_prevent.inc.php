<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $id         = (int) $_GET['id'];
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'reg_prevent');
        exit;
    }
    $limit = (int) $_GET['limit'];
    $start = (int) $_GET['start'];
    $sql   = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `ID`, `Username`, `Mail`, `IP`, `Reason`, `Date`, `Status` ";
    $sql .= "FROM `" . RL_DBPREFIX . "reg_prevent` ORDER BY `Date` DESC ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang['autoRegPrevent_status_' . $value['Status']];
    }
    $count           = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $output['total'] = $count['count'];
    $output['data']  = $data;
    $reefless->loadClass('Json');
    echo $rlJson->encode($output);
} else {
    $reefless->loadClass('AutoRegPrevent', false, 'autoRegPrevent');
    $rlXajax->registerFunction(array(
        'addSpamers',
        $rlAutoRegPrevent,
        'ajaxAddSpamers'
    ));
}