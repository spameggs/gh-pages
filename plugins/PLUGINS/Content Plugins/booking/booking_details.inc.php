<?php
$listing_id = (int) $_GET['id'];
$sql        = "SELECT `T1`.`Account_ID`, `T1`.`Plan_ID`,`T2`.`Type` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
$sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
$sql .= " WHERE `T1`.`ID` = '{$listing_id}' LIMIT 1";
$listingInfo = $rlDb->getRow($sql);
if ($listingInfo['Account_ID'] == $_SESSION['id']) {
    if ($config['booking_binding_plans']) {
        if (!in_array($listingInfo['Plan_ID'], explode(',', $config['booking_plans']))) {
            $sError = true;
        }
    }
    $sql = "SELECT `T2`.`ID` AS `Request_ID`, IF(`Renter_ID` > 0, CONCAT(`T4`.`First_name`, ' ', `T4`.`Last_name`), CONCAT(`T2`.`first_name`, ' ', `T2`.`last_name`)) AS `Author`, ";
    $sql .= "`T1`.`ID` AS `Req_ID`,`T1`.`Status` AS `Req_status`,`T3`.*, `T5`.`Path`, `T5`.`Type` FROM `" . RL_DBPREFIX . "listings_book` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "booking_requests` AS `T2` ON `T1`.`ID`=`T2`.`Book_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T3` ON `T1`.`Listing_ID`=`T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T4` ON `T4`.`ID`=`T2`.`Renter_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T5` ON `T3`.`Category_ID`=`T5`.`ID` ";
    $sql .= "WHERE `T2`.`Owner_ID`='{$_SESSION['id']}' AND `T3`.`ID` = '{$listing_id}' ";
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
    $rlSmarty->assign_by_ref('requests', $ListRequests);
    $myPage             = $rlListingTypes->types[$listingInfo['Type']]['My_key'];
    $bread_crumbs[1]    = array(
        'title' => $lang['pages+title+' . $myPage],
        'name' => $lang['pages+name+' . $myPage],
        'path' => $pages[$myPage]
    );
    $bread_crumbs[2]    = array(
        'title' => $lang['pages+title+booking_details'],
        'name' => $lang['pages+name+booking_details']
    );
    $page_info['title'] = $lang['pages+title+' . $myPage];
    $reefless->loadClass('Booking', null, 'booking');
    $rlBooking->getRateRange($listing_id, true);
    $rlXajax->registerFunction(array(
        'saveDesc',
        $rlBooking,
        'ajaxSaveDesc'
    ));
    $rlXajax->registerFunction(array(
        'saveRateRange',
        $rlBooking,
        'ajaxSaveRateRange'
    ));
    $rlXajax->registerFunction(array(
        'deleteRateRange',
        $rlBooking,
        'ajaxDeleteRateRange'
    ));
    $rlXajax->registerFunction(array(
        'deleteRequest',
        $rlBooking,
        'ajaxDeleteRequest'
    ));
    if ($config['booking_bind_checkin_checkout']) {
        $massDays     = array(
            'mon' => $lang['booking_monday'],
            'tue' => $lang['booking_tuesday'],
            'wed' => $lang['booking_wednesday'],
            'thu' => $lang['booking_thursday'],
            'fri' => $lang['booking_friday'],
            'sat' => $lang['booking_saturday'],
            'sun' => $lang['booking_sunday']
        );
        $binding_days = $rlDb->fetch('*', array(
            'Listing_ID' => $listing_id,
            'Status' => 'active'
        ), null, null, 'booking_bindings', 'row');
        $rlSmarty->assign_by_ref('mass_days', $massDays);
        $rlSmarty->assign_by_ref('binding_days', $binding_days);
        $rlXajax->registerFunction(array(
            'saveBindingDays',
            $rlBooking,
            'ajaxSaveBindingDays'
        ));
    }
} else {
    $sError = true;
}