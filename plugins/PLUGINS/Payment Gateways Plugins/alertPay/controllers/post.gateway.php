<?php
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_INC . 'control.inc.php');
    $config = $rlConfig->allConfig();
    $reefless->loadClass('Valid');
    $receivedSecurityCode         = $_POST['ap_securitycode'];
    $receivedMerchantEmailAddress = $_POST['ap_merchant'];
    $transactionStatus            = $_POST['ap_status'];
    $testModeStatus               = $_POST['ap_test'];
    $purchaseType                 = $_POST['ap_purchasetype'];
    $totalAmountReceived          = $_POST['ap_totalamount'];
    $feeAmount                    = $_POST['ap_feeamount'];
    $netAmount                    = $_POST['ap_netamount'];
    $txn_id                       = $_POST['ap_referencenumber'];
    $currency                     = $_POST['ap_currency'];
    $transactionDate              = $_POST['ap_transactiondate'];
    $transactionType              = $_POST['ap_transactiontype'];
    $customerFirstName            = $_POST['ap_custfirstname'];
    $customerLastName             = $_POST['ap_custlastname'];
    $customerAddress              = $_POST['ap_custaddress'];
    $customerCity                 = $_POST['ap_custcity'];
    $customerState                = $_POST['ap_custstate'];
    $customerCountry              = $_POST['ap_custcountry'];
    $customerZipCode              = $_POST['ap_custzip'];
    $customerEmailAddress         = $_POST['ap_custemailaddress'];
    $itemName                     = $_POST['ap_itemname'];
    $myItemCode                   = $_POST['apc_1'];
    $myItemDescription            = $_POST['ap_description'];
    $myItemQuantity               = $_POST['ap_quantity'];
    $total                        = $_POST['ap_amount'];
    $additionalCharges            = $_POST['ap_additionalcharges'];
    $shippingCharges              = $_POST['ap_shippingcharges'];
    $taxAmount                    = $_POST['ap_taxamount'];
    $discountAmount               = $_POST['ap_discountamount'];
    if ($receivedMerchantEmailAddress != $config['alertPay_account_email']) {
        $rlDebug->logger("Payza: Exit since payza account email doesn't match returned account email");
        exit;
    }
    if ($receivedSecurityCode != $config['alertPay_secure_code']) {
        $rlDebug->logger("Payza: Exit since payza security code doesn't match returned security code");
        exit;
    }
    if ($transactionStatus == "Success") {
        $items           = explode('|', base64_decode(urldecode($myItemCode)));
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
        if (strcmp($crypted_sum, crypt(sprintf("%.2f", $total), $config['alertPay_secure_code'])) != 0) {
            $rlDebug->logger("Payza: Exit since crypted sum is invalid");
            exit;
        }
        if ($callback_plugin) {
            $reefless->loadClass(str_replace('rl', '', $callback_class), null, $callback_plugin);
        } else {
            $reefless->loadClass(str_replace('rl', '', $callback_class));
        }
        $$callback_class->$callback_method($item_id, $plan_id, $account_id, $txn_id, 'Payza', $total);
    } else {
        $rlDebug->logger("Payza: Exit since payza returned status other than Success");
    }
}