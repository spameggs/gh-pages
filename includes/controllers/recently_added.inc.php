<?php
$rlXajax->registerFunction(array(
    'loadRecentlyAdded',
    $rlListings,
    'ajaxloadRecentlyAdded'
));
foreach ($rlListingTypes->types as $type) {
    $default_type = !$default_type ? $type['Key'] : $default_type;
    if (isset($_GET[$type['Key']])) {
        $requested_type = $type['Key'];
        break;
    }
}
$default                         = $_SESSION['recently_added_type'] ? $_SESSION['recently_added_type'] : $default_type;
$requested_type                  = $requested_type ? $requested_type : $default;
$_SESSION['recently_added_type'] = $requested_type;
$rlSmarty->assign_by_ref('requested_type', $requested_type);
$pInfo['current'] = (int) $_GET['pg'];
if ($pInfo['current'] > 1) {
    $bc_page        = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
    $bread_crumbs[] = array(
        'title' => $page_info['name'] . $bc_page,
        'name' => $lang['listing_types+name+' . $requested_type] . $bc_page
    );
}
$listings = $rlListings->getRecentlyAdded($pInfo['current'], $config['listings_per_page'], $requested_type);
$rlSmarty->assign_by_ref('listings', $listings);
$pInfo['calc'] = $rlListings->calc;
$rlSmarty->assign_by_ref('pInfo', $pInfo);
$rss = array(
    'title' => $lang['pages+name+listings']
);
$rlSmarty->assign_by_ref('rss', $rss);
$rlHook->load('listingsBottom');