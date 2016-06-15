<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'get') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_INC . 'control.inc.php');
    $reefless->loadClass('Cache');
    $config = $rlConfig->allConfig();
    if (!empty($_GET['item_number'])) {
        $items           = explode('|', base64_decode(urldecode($_GET['item_number'])));
        $plan_id         = $items[0];
        $item_id         = $items[1];
        $account_id      = $items[2];
        $total           = $items[3];
        $callback_class  = $items[4];
        $callback_method = $items[5];
        $cancel_url      = $items[6];
        $success_url     = $items[7];
        $lang_code       = $items[8];
        $plugin          = $items[9];
        $txn_id          = $_GET['ref'];
        $signature       = md5($GLOBALS['config']['paytpv_account'] . $GLOBALS['config']['paytpv_usercode'] . $GLOBALS['config']['paytpv_terminal'] . 1 . $txn_id . $total . $GLOBALS['config']['paytpv_currency'] . md5($GLOBALS['config']['paytpv_password']));
        define('RL_LANG_CODE', $lang_code);
        $seo_base = RL_URL_HOME;
        $seo_base .= $lang_code == $config['lang'] ? '' : $lang_code . '/';
        $lang            = $rlLang->getLangBySide('frontEnd', RL_LANG_CODE);
        $GLOBALS['lang'] = $lang;
        if (empty($item_id) || empty($total)) {
            $errors = true;
        }
        if ($signature != trim($_GET['s'])) {
            $errors = true;
        }
        if (!$errors) {
            $reefless->loadClass(str_replace('rl', '', $callback_class), null, $plugin);
            $$callback_class->$callback_method($item_id, $plan_id, $account_id, $txn_id, 'PayTPV', $total);
            $reefless->redirect(false, str_replace('&amp;', '&', $success_url));
        } else {
            $reefless->redirect(false, str_replace('&amp;', '&', $cancel_url));
        }
    }
}