<?php
if ($_REQUEST['item']) {
    require_once('../../../includes/config.inc.php');
    require_once(RL_INC . 'control.inc.php');
    $config = $rlConfig->allConfig();
    $errors = false;
    if (!empty($_GET['item']) && $_GET['flsessid'] == session_id()) {
        $items           = explode('|', base64_decode(urldecode($_GET['item'])));
        $plan_id         = $items[0];
        $item_id         = $items[1];
        $account_id      = $items[2];
        $crypted_sum     = $items[3];
        $callback_class  = $items[4];
        $callback_method = $items[5];
        $cancel_url      = $items[6];
        $success_url     = $items[7];
        $lang_code       = $items[8];
        $callback_plugin = $items[9] ? $items[9] : false;
        define('RL_LANG_CODE', $lang_code);
        define('RL_DATE_FORMAT', $rlDb->getOne('Date_format', "`Code` = '{$config['lang']}'", 'languages'));
        $seo_base = RL_URL_HOME;
        $seo_base .= $lang_code == $config['lang'] ? '' : $lang_code;
        $lang            = $rlLang->getLangBySide('frontEnd', RL_LANG_CODE);
        $GLOBALS['lang'] = $lang;
        $total           = $_GET['total'];
        if (strcmp($crypted_sum, crypt(sprintf("%.2f", $total), str_replace('http://', '', RL_URL_HOME))) != 0) {
            $errors = true;
        }
        if (empty($item_id) || empty($plan_id) || empty($total)) {
            $errors = true;
        }
        if (!$errors) {
            $txn_id = 'Access restricted';
            if ($callback_plugin) {
                $reefless->loadClass(str_replace('rl', '', $callback_class), null, $callback_plugin);
            } else {
                $reefless->loadClass(str_replace('rl', '', $callback_class));
            }
            $$callback_class->$callback_method($item_id, $plan_id, $account_id, $txn_id, 'googleCheckout', $total);
            $reefless->redirect(null, $success_url);
        } else {
            $reefless->redirect(null, $cancel_url);
        }
    } else {
        $rlDebug->logger('Google Checkout Error: the item data is empty or sesison lost');
    }
}