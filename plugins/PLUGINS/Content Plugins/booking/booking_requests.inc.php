<?php
$reefless->loadClass('Listings');
$reefless->loadClass('Lang');
if ($config['mod_rewrite']) {
    $var = $_GET['nvar_1'];
    preg_match('/(.*)-r([0-9]+)/', $var, $id);
    $request_id         = (int) $id[2];
    $_GET['request_id'] = $request_id;
} else {
    $request_id = (int) $_GET['id'];
}
if ($request_id) {
    $fields = $rlDb->fetch('*', array(
        'Status' => 'active'
    ), null, null, 'booking_fields');
    $fields = $rlLang->replaceLangKeys($fields, 'booking_fields', array(
        'name'
    ));
    $sql    = "SELECT `T1`.`ID` AS `Req_ID`, `T3`.`ID` AS `Listing_ID`, `T1`.`Amount`, `T1`.`Status` AS `Stat`, `T2`.`Status` AS `Req_status`,`T3`.*,`T1`.`From`,`T1`.`To`,`T2`.*, `T4`.`Type` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listings_book` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "booking_requests` AS `T2` ON `T1`.`ID`=`T2`.`Book_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T3` ON `T1`.`Listing_ID`=`T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T4` ON `T3`.`Category_ID`=`T4`.`ID` ";
    $sql .= "WHERE `T2`.`Owner_ID`='{$_SESSION['id']}' AND `T1`.`ID`='{$request_id}'";
    $requests = $rlDb->getRow($sql);
    if (empty($requests)) {
        $sError = true;
    }
    $requests['Stat']  = str_replace(array(
        'process',
        'booked',
        'refused'
    ), array(
        $GLOBALS['lang']['booking_processed'],
        $GLOBALS['lang']['booking_accepted'],
        $GLOBALS['lang']['booking_refused']
    ), $requests['Stat']);
    $requests['title'] = $rlListings->getListingTitle($requests['Category_ID'], $requests, $requests['Type']);
    foreach ($fields as $key => $field) {
        if (!empty($requests[$field['Key']])) {
            $requests['fields'][$key]          = $field;
            $requests['fields'][$key]['value'] = $requests[$field['Key']];
        }
    }
    $bread_crumbs[] = array(
        'title' => $requests['title'],
        'name' => $requests['title']
    );
    $rlSmarty->assign_by_ref('requests', $requests);
    $reefless->loadClass('Booking', null, 'booking');
    $rlXajax->registerFunction(array(
        'ownerResult',
        $rlBooking,
        'ajaxOwnerResult'
    ));
    $rlBooking->getRateRange($requests['Listing_ID'], true);
} else {
    $sql = "SELECT `T2`.`ID` AS `Request_ID`, IF(`Renter_ID` > 0, CONCAT(`T4`.`First_name`, ' ', `T4`.`Last_name`), CONCAT(`T2`.`first_name`, ' ', `T2`.`last_name`)) AS `Author`, ";
    $sql .= "`T1`.`ID` AS `Req_ID`,`T1`.`Status` AS `Req_status`,`T3`.*, `T5`.`Path`, `T5`.`Type` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listings_book` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "booking_requests` AS `T2` ON `T1`.`ID`=`T2`.`Book_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T3` ON `T1`.`Listing_ID`=`T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T4` ON `T4`.`ID`=`T2`.`Renter_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T5` ON `T3`.`Category_ID`=`T5`.`ID` ";
    $sql .= "WHERE `T2`.`Owner_ID`='{$_SESSION['id']}' ";
    $sql .= "ORDER BY `T2`.`Status`, `T2`.`Date` DESC";
    $requests = $rlDb->getAll($sql);
    foreach ($requests as $key => $value) {
        $ListRequests[$value['Req_ID']]['ltype']      = $value['Type'];
        $ListRequests[$value['Req_ID']]['Path']       = $value['Path'];
        $ListRequests[$value['Req_ID']]['Listing_ID'] = $value['ID'];
        $ListRequests[$value['Req_ID']]['title']      = $rlListings->getListingTitle($value['Category_ID'], $value, $value['Type']);
        $ListRequests[$value['Req_ID']]['status']     = $value['Req_status'];
        $ListRequests[$value['Req_ID']]['Author']     = $value['Author'];
        $ListRequests[$value['Req_ID']]['ID']         = $value['Request_ID'];
        if ($aHooks['ref'] == 1) {
            $ListRequests[$value['Req_ID']]['ref'] = $value['ref_number'];
        }
    }
    unset($requests);
    $rlSmarty->assign_by_ref('requests', $ListRequests);
}
$reefless->loadClass('Booking', null, 'booking');
$rlXajax->registerFunction(array(
    'deleteRequest',
    $rlBooking,
    'ajaxDeleteRequest'
));