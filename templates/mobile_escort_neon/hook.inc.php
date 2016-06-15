<?php
global $page_info, $lang, $bread_crumbs, $rlSmarty, $account_menu, $mobile_account_menu_deny, $config;
$config['listing_feilds_position'] = 1;
$config['img_crop_interface']      = 0;
if ($page_info['Key'] == 'login' && defined('IS_LOGIN') && IS_LOGIN) {
    $page_info['name']        = $lang['blocks+name+account_area'];
    $bread_crumbs[1]['name']  = $lang['blocks+name+account_area'];
    $bread_crumbs[1]['title'] = $lang['blocks+name+account_area'];
}
$account_area_pages = array(
    'my_profile',
    'payment',
    'add_listing',
    'my_messages',
    'my_listings',
    'my_packages',
    'payment_history'
);
if (in_array($page_info['Key'], $account_area_pages)) {
    $bread_crumbs   = array_reverse($bread_crumbs);
    $last           = array_pop($bread_crumbs);
    $bread_crumbs[] = array(
        'name' => $lang['blocks+name+account_area'],
        'title' => $lang['blocks+name+account_area'],
        'path' => 'login'
    );
    $bread_crumbs[] = $last;
    $bread_crumbs   = array_reverse($bread_crumbs);
    unset($last);
}
$mobile_account_menu_deny = array(
    'remote_adverts',
    'saved_search',
    'invoices'
);
if (defined('IS_LOGIN') && IS_LOGIN) {
    foreach ($account_menu as $account_menu_key => $account_menu_item) {
        if (in_array($account_menu_item['Key'], $mobile_account_menu_deny)) {
            unset($account_menu[$account_menu_key]);
        }
    }
}