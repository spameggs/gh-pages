<?php
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_INC . 'control.inc.php');
    $config = $rlConfig->allConfig();
    $req    = 'cmd=_notify-validate';
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }
    $host = $config['paypal_sandbox'] ? 'sandbox.paypal.com' : 'www.paypal.com';
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp               = fsockopen('ssl://' . $host, 443, $errno, $errstr, 30);
    $item_name        = $_POST['item_name'];
    $item_number      = $_POST['item_number'];
    $payment_status   = $_POST['payment_status'];
    $mc_gross         = $_POST['mc_gross'];
    $payment_gross    = $_POST['payment_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id           = $_POST['txn_id'];
    $receiver_email   = $_POST['receiver_email'];
    $payer_email      = $_POST['payer_email'];
    if ($fp) {
        fputs($fp, $header . $req);
        while (!feof($fp)) {
            $res .= fgets($fp, 1024);
        }
        fclose($fp);
        $arr = explode("\r\n\r\n", $res);
        if (strcmp($arr[1], 'VERIFIED') != 0) {
            $rlDebug->logger("PayPal: Exit since PayPal returned status other than VERIFIED");
            exit;
        }
        if (!in_array(trim(strtolower($payment_status)), array(
            'completed',
            'pending'
        ))) {
            $rlDebug->logger("PayPal: Exit since payment status is not Completed or Pending");
            exit;
        }
        $items           = explode('|', base64_decode(urldecode($item_number)));
        $plan_id         = $items[0];
        $item_id         = $items[1];
        $account_id      = $items[2];
        $crypted_sum     = $items[3];
        $callback_class  = $items[4];
        $callback_method = $items[5];
        $lang_code       = $items[6];
        $callback_plugin = $items[7] ? $items[7] : false;
        define('RL_LANG_CODE', $lang_code);
        define('RL_DATE_FORMAT', $rlDb->getOne('Date_format', "`Code` = '{$config['lang']}'", 'languages'));
        $lang            = $rlLang->getLangBySide('frontEnd', RL_LANG_CODE);
        $GLOBALS['lang'] = $lang;
        $total           = !empty($payment_gross) ? $payment_gross : $mc_gross;
        if (strcmp($crypted_sum, crypt(sprintf("%.2f", $total), $config['paypal_secret_word'])) != 0) {
            $rlDebug->logger("PayPal: Exit since crypted sum is invalid");
            exit;
        }
        if ($callback_plugin) {
            $reefless->loadClass(str_replace('rl', '', $callback_class), null, $callback_plugin);
        } else {
            $reefless->loadClass(str_replace('rl', '', $callback_class));
        }
        $$callback_class->$callback_method($item_id, $plan_id, $account_id, $txn_id, 'PayPal', $total);
    }
}