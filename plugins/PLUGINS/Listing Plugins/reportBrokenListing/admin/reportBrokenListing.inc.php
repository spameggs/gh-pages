<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $reefless->loadClass('Listings');
    $reefless->loadClass('Common');
    $reefless->loadClass('ListingTypes');
    $reefless->loadClass('Json');
    $sql = "SELECT SQL_CALC_FOUND_ROWS `T1`.`ID`, `T1`.`Message`, `T1`.`Listing_ID`, `T1`.`Account_ID`, `T1`.`Date`, ";
    $sql .= "`T2`.`Username`, `T2`.`First_name`, `T2`.`Last_name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "report_broken_listing` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T2` ON `T1`.`Account_ID` = `T2`.`ID` ";
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Name'] = $value['First_name'] || $value['Last_name'] ? trim($value['First_name'] . ' ' . $value['Last_name']) : $value['Username'];
        unset($data[$key]['Username'], $data[$key]['First_name'], $data[$key]['Last_name']);
    }
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $reefless->loadClass('ReportBrokenListing', null, 'reportBrokenListing');
    $rlXajax->registerFunction(array(
        'deletereportBrokenListing',
        $rlReportBrokenListing,
        'ajaxDeletereportBrokenListing'
    ));
    $rlXajax->registerFunction(array(
        'deleteListing',
        $rlReportBrokenListing,
        'ajaxDeleteListing'
    ));
}