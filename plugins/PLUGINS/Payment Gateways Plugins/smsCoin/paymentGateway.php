<?php
global $rlSmarty, $page_info;
require_once(RL_PLUGINS . 'smsCoin' . RL_DS . 'countries.php');
if ($GLOBALS['config']['smscoin_module']) {
    $key           = strtolower(str_replace(" ", "_", $_SESSION['GEOLocationData']->Country_name));
    $country_price = (float) $sc_countries[$key];
    $GLOBALS['rlSmarty']->assign('country_price', $country_price);
    if ($page_info['Controller'] == 'add_listing') {
        global $plan_info;
        $GLOBALS['rlSmarty']->assign('plan_price', $plan_info['Price']);
    }
    $GLOBALS['rlSmarty']->display(RL_ROOT . 'plugins' . RL_DS . 'smsCoin' . RL_DS . 'smscoin_payment_block.tpl');
}