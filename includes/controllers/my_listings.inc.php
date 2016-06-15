<?php
if (defined('IS_LOGIN')) {
    $reefless->loadClass('Listings');
    $reefless->loadClass('Actions');
    $rlXajax->registerFunction(array(
        'deleteListing',
        $rlListings,
        'ajaxDeleteListing'
    ));
    if (!$_POST['xjxfun']) {
        unset($_SESSION['ml_deleted']);
    }
    $listings_type = $rlListingTypes->types[substr($page_info['Key'], 3)];
    $rlSmarty->assign_by_ref('listings_type', $listings_type);
    $rlSmarty->assign('page_key', 'lt_' . $listings_type['Key']);
    if (isset($_GET['incomplete'])) {
        $id                                      = (int) $_GET['incomplete'];
        $step                                    = $_GET['step'];
        $listing_info                            = $rlDb->fetch(array(
            'Plan_ID',
            'Category_ID',
            'Featured_ID',
            'Crossed',
            'Last_type'
        ), array(
            'ID' => $id
        ), null, 1, 'listings', 'row');
        $category_path                           = $rlDb->getOne('Path', "`ID` = {$listing_info['Category_ID']}", 'categories');
        $_SESSION['add_listing']['plan_id']      = $listing_info['Plan_ID'];
        $_SESSION['add_listing']['listing_id']   = $id;
        $_SESSION['add_listing']['listing_type'] = $rlDb->getOne('Type', "`ID` = '{$listing_info['Category_ID']}'", 'categories');
        $_SESSION['add_listing']['crossed']      = $listing_info['Crossed'] ? explode(',', $listing_info['Crossed']) : false;
        $url                                     = SEO_BASE;
        $url .= $config['mod_rewrite'] ? $pages['add_listing'] . '/' . $category_path . '/' . $steps[$step]['path'] . '.html' : '?page=' . $pages['add_listing'] . '&id=' . $listing_info['Category_ID'] . '&step=' . $steps[$step]['path'];
        $reefless->redirect(null, $url);
        exit;
    }
    $add_listing_href = $config['mod_rewrite'] ? SEO_BASE . $pages['add_listing'] . '.html' : RL_URL_HOME . 'index.php?page=' . $pages['add_listing'];
    $rlSmarty->assign_by_ref('add_listing_href', $add_listing_href);
    $pInfo['current'] = (int) $_GET['pg'];
    $sorting          = array(
        'category' => array(
            'name' => $lang['category'],
            'field' => 'Category_ID'
        ),
        'status' => array(
            'name' => $lang['status'],
            'field' => 'Status'
        ),
        'expire_date' => array(
            'name' => $lang['expire_date'],
            'field' => 'Plan_expire'
        )
    );
    $rlSmarty->assign_by_ref('sorting', $sorting);
    $sort_by = empty($_GET['sort_by']) ? $_SESSION['ml_sort_by'] : $_GET['sort_by'];
    if (!empty($sorting[$sort_by])) {
        $order_field = $sorting[$sort_by]['field'];
    }
    $_SESSION['ml_sort_by'] = $sort_by;
    $rlSmarty->assign_by_ref('sort_by', $sort_by);
    $sort_type                = empty($_GET['sort_type']) ? $_SESSION['ml_sort_type'] : $_GET['sort_type'];
    $sort_type                = in_array($sort_type, array(
        'asc',
        'desc'
    )) ? $sort_type : false;
    $_SESSION['ml_sort_type'] = $sort_type;
    $rlSmarty->assign_by_ref('sort_type', $sort_type);
    $rlHook->load('myListingsPreSelect');
    if ($pInfo['current'] > 1) {
        $bc_page = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
        $bread_crumbs[1]['title'] .= $bc_page;
    }
    $reefless->loadClass('Plan');
    $available_plans = $rlPlan->getPlanByCategory(0, $account_info['Type'], true);
    $rlSmarty->assign_by_ref('available_plans', $available_plans);
    $listings = $rlListings->getMyListings($listings_type['Key'], $order_field, $sort_type, $pInfo['current'], $config['listings_per_page']);
    $rlSmarty->assign_by_ref('listings', $listings);
    $pInfo['calc'] = $rlListings->calc;
    $rlSmarty->assign_by_ref('pInfo', $pInfo);
}