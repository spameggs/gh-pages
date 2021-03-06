<?php
if (!defined('IS_LOGIN')) {
    $sError = true;
} else {
    $reefless->loadClass('Plan');
    $pInfo['current'] = (int) $_GET['pg'];
    $page             = $pInfo['current'] ? $pInfo['current'] - 1 : 0;
    $from             = $page * $config['transactions_per_page'];
    $limit            = $config['transactions_per_page'];
    $sql              = "SELECT SQL_CALC_FOUND_ROWS DISTINCT * ";
    $sql .= "FROM `" . RL_DBPREFIX . "transactions` ";
    $sql .= "WHERE  `Account_ID` = '{$account_info['ID']}' AND `Status` = 'active' ";
    $rlHook->load('paymentHistorySqlWhere', $sql);
    $sql .= "ORDER BY `Date` LIMIT {$from}, {$limit}";
    $transactions  = $rlDb->getAll($sql);
    $calc          = $rlDb->getRow("SELECT FOUND_ROWS() AS `calc`");
    $pInfo['calc'] = $calc['calc'];
    $rlSmarty->assign_by_ref('pInfo', $pInfo);
    foreach ($transactions as $key => &$item) {
        if (array_key_exists($item['Service'], $l_plan_types)) {
            if (in_array($item['Service'], array(
                'listing',
                'featured',
                'package'
            ))) {
                $plan                            = $rlPlan->getPlan($item['Plan_ID']);
                $transactions[$key]['plan_info'] = $plan['name'] . ' (' . $lang[$item['Service'] . '_plan'] . ')';
                $item_details                    = $rlListings->getListing($item['Item_ID'], true);
                $transactions[$key]['item_info'] = $item_details ? $item_details['listing_title'] : false;
                $transactions[$key]['link']      = $item_details ? $item_details['listing_link'] : false;
            } else {
                $rlHook->load('phpPaymentHistoryDefault', $item);
            }
        } else {
            $rlHook->load('phpPaymentHistoryLoop', $item);
        }
        unset($plan_info, $item_details);
    }
    $rlHook->load('phpPaymentHistoryBottom');
    $rlSmarty->assign_by_ref('transactions', $transactions);
}