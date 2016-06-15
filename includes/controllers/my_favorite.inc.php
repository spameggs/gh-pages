<?php
$reefless->loadClass('Listings');
$reefless->loadClass('Actions');
$pInfo['current'] = (int) $_GET['pg'];
$sorting          = array(
    'category' => array(
        'name' => $lang['category'],
        'field' => 'Category_ID'
    ),
    'featured' => array(
        'name' => $lang['featured'],
        'field' => 'Featured'
    )
);
$rlSmarty->assign_by_ref('sorting', $sorting);
$sort_by = empty($_GET['sort_by']) ? $_SESSION['fl_sort_by'] : $_GET['sort_by'];
if (!empty($sorting[$sort_by])) {
    $order_field = $sorting[$sort_by]['field'];
}
$_SESSION['fl_sort_by'] = $sort_by;
$rlSmarty->assign_by_ref('sort_by', $sort_by);
$sort_type                = empty($_GET['sort_type']) ? $_SESSION['fl_sort_type'] : $_GET['sort_type'];
$sort_type                = ($sort_type == 'asc' || $sort_type == 'desc') ? $sort_type : false;
$_SESSION['fl_sort_type'] = $sort_type;
$rlSmarty->assign_by_ref('sort_type', $sort_type);
$listings = $rlListings->getMyFavorite($order_field, $sort_type, $pInfo['current'], $config['listings_per_page']);
$rlSmarty->assign_by_ref('listings', $listings);
$pInfo['calc'] = $rlListings->calc;
$rlSmarty->assign_by_ref('pInfo', $pInfo);
$rlHook->load('myFavoriteBottom');