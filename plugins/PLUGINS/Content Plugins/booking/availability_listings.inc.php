<?php
$data['check_availability'] = $_SESSION['booking_availability'] = $_POST['availability'];
$reefless->loadClass('Search');
$reefless->loadClass('Listings');
$reefless->loadClass('Booking', false, 'booking');
$pInfo['current'] = (int) $_GET['pg'];
$sorting          = array(
    'type' => array(
        'name' => $lang['listing_type'],
        'field' => 'Listing_type',
        'Key' => 'Listing_type',
        'Type' => 'select'
    ),
    'category' => array(
        'name' => $lang['category'],
        'field' => 'Category_ID',
        'Key' => 'Category_ID',
        'Type' => 'select'
    ),
    'post_date' => array(
        'name' => $lang['join_date'],
        'field' => 'Date',
        'Key' => 'Date'
    )
);
$rlSmarty->assign_by_ref('sorting', $sorting);
$sort_by = $_SESSION['booking_search_sort_by'] = empty($_REQUEST['sort_by']) ? $_SESSION['booking_search_sort_by'] : $_REQUEST['sort_by'];
if (!empty($sorting[$sort_by])) {
    $data['sort_by'] = $sort_by;
    $rlSmarty->assign_by_ref('sort_by', $sort_by);
}
$sort_type = $_SESSION['booking_search_sort_type'] = empty($_REQUEST['sort_type']) ? $_SESSION['booking_search_sort_type'] : $_REQUEST['sort_type'];
if ($sort_type) {
    $data['sort_type'] = $sort_type = in_array($sort_type, array(
        'asc',
        'desc'
    )) ? $sort_type : false;
    $rlSmarty->assign_by_ref('sort_type', $sort_type);
}
$rlSearch->fields['check_availability'] = array(
    'Key' => 'check_availability',
    'Type' => 'date',
    'Default' => 'single'
);
$listings                               = $rlSearch->search($data, $rlBooking->bookingType, $pInfo['current'], $config['listings_per_page']);
$rlSmarty->assign_by_ref('listings', $listings);
$pInfo['calc'] = $rlSearch->calc;
$rlSmarty->assign_by_ref('pInfo', $pInfo);
if ($listings) {
    $page_info['name'] = str_replace(array(
        '{number}',
        '{type}'
    ), array(
        $pInfo['calc'],
        'Availability Listings'
    ), $lang['listings_found']);
}
$rlXajax->registerFunction(array(
    'addToFavorite',
    $rlListings,
    'ajaxAddToFavorite'
));
if (defined('IS_LOGIN')) {
    $rlXajax->registerFunction(array(
        'restoreFavorite',
        $rlListings,
        'ajaxRestoreFavorite'
    ));
}