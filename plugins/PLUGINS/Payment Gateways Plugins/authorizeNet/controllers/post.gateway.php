<?php
$file = fopen('error.log', 'a');
if ($file) {
    $line = "\n\n" . date('Y.m.d H:i:s') . ":\n";
    fwrite($file, $line);
    foreach ($_POST as $p_key => $p_val) {
        $line = "{$p_key} => {$p_val}\n";
        fwrite($file, $line);
    }
    fclose($file);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'post') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_INC . 'control.inc.php');
    $reefless->loadClass('Cache');
    $config = $rlConfig->allConfig();
    if (!empty($_POST['item_number'])) {
        $items           = explode('|', base64_decode(urldecode($_POST['item_number'])));
        $plan_id         = $items[0];
        $item_id         = $items[1];
        $account_id      = $items[2];
        $crypted_sum     = $items[3];
        $callback_class  = $items[4];
        $callback_method = $items[5];
        $cancel_url      = $items[6];
        $success_url     = $items[7];
        $lang_code       = $items[8];
        $total           = $_POST['x_amount'];
        $txn_id          = $_POST['x_invoice_num'];
        define('RL_LANG_CODE', $lang_code);
        $seo_base = RL_URL_HOME;
        $seo_base .= $lang_code == $config['lang'] ? '' : $lang_code . '/';
        $lang            = $rlLang->getLangBySide('frontEnd', RL_LANG_CODE);
        $GLOBALS['lang'] = $lang;
        if (empty($item_id) || empty($plan_id) || empty($total)) {
            $errors = true;
        }
        if (!$errors) {
            $reefless->loadClass(str_replace('rl', '', $callback_class));
            $$callback_class->$callback_method($item_id, $plan_id, $account_id, $txn_id, 'authorizeNet', $total);
            $reefless->redirect(false, str_replace('&amp;', '&', $success_url));
        } else {
            $reefless->redirect(false, str_replace('&amp;', '&', $cancel_url));
        }
    }
}