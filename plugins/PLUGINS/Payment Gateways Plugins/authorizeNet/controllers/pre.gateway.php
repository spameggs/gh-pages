<?php
$crypted_price = crypt(sprintf("%.2f", $price), $config['alertPay_secure_code']);
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . $cancel_url . '|' . $success_url . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$data          = base64_encode($data);
$aNet_url      = $config['aNet_sandbox'] ? 'https://test.authorize.net/gateway/transact.dll' : 'https://secure.authorize.net/gateway/transact.dll';
$notify_url    = RL_PLUGINS_URL . 'authorizeNet/controllers/post.gateway.php';
$invoice       = date(YmdHis);
$sequence      = rand(1, 1000);
$timeStamp     = time();
if (phpversion() >= '5.1.2') {
    $x_fp_hash = hash_hmac("md5", $config['aNet_account_id'] . "^" . $sequence . "^" . $timeStamp . "^" . $price . "^", $config['aNet_transaction_key']);
} else {
    $x_fp_hash = bin2hex(mhash(MHASH_MD5, $config['aNet_account_id'] . "^" . $sequence . "^" . $timeStamp . "^" . $price . "^", $config['aNet_transaction_key']));
}
?>
<form name="payment_form" action="<?php echo $aNet_url; ?>" method="post">
	<input type='hidden' name='x_login' value="<?php echo $config['aNet_account_id']; ?>" />
	<input type='hidden' name='x_amount' value="<?php echo $price; ?>" />
	<input type='hidden' name='x_description' value="<?php echo $item_name; ?>" />
	<input type='hidden' name='x_invoice_num' value="<?php echo $invoice; ?>" />
	<input type='hidden' name='x_fp_sequence' value="<?php echo $sequence; ?>" />
	<input type='hidden' name='x_fp_timestamp' value="<?php echo $timeStamp; ?>" />
	<input type='hidden' name='x_fp_hash' value="<?php echo $x_fp_hash; ?>" />
	<input type='hidden' name='x_receipt_link_url' value="<?php echo $notify_url; ?>" />
	<input type='hidden' name='x_receipt_link_method' value="POST" />
	<input type='hidden' name='x_receipt_link_text' value="Return to <?php echo $config['site_name']; ?>" />	
	<input type='hidden' name='x_test_request' value="<?php echo $config['aNet_sandbox']; ?>" />
	<input type='hidden' name='item_name' value="<?php echo $item_name; ?>" />
	<input type='hidden' name='item_number' value="<?php echo $data; ?>" />
	<input type='hidden' name='x_show_form' value="PAYMENT_FORM" />
</form>