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
        $rlActions->updateOne($updateData, 'testimonials');
        exit;
    }
    $limit = (int) $_GET['limit'];
    $start = (int) $_GET['start'];
    $sql   = "SELECT SQL_CALC_FOUND_ROWS `T1`.* ";
    $sql .= "FROM `" . RL_DBPREFIX . "testimonials` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T2` ON `T1`.`Account_ID` = `T2`.`ID` ";
    $sql .= "ORDER BY `T1`.`ID` DESC ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `testimonials`");
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
    }
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $reefless->loadClass('Testimonials', null, 'testimonials');
    $rlXajax->registerFunction(array(
        'deleteTestimonial',
        $rlTestimonials,
        'ajaxDelete'
    ));
}