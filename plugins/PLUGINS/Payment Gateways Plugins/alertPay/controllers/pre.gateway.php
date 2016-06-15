<?php
$crypted_price = crypt(sprintf("%.2f", $price), $config['alertPay_secure_code']);
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$data          = base64_encode($data);
$alertPay_url  = $config['alertPay_sandbox'] ? 'https://sandbox.Payza.com/sandbox/payprocess.aspx' : 'https://secure.payza.com/checkout';
$notify_url    = RL_PLUGINS_URL . 'alertPay/controllers/post.gateway.php';
?>
<form name="payment_form" action="<?php echo $alertPay_url; ?>" method="post">
	<input name="ap_purchasetype" type="hidden" value="item-goods" />
	<input name="ap_merchant" type="hidden" value="<?php echo $config['alertPay_account_email']; ?>" />
	<input name="ap_securitycode" type="hidden" value="<?php echo $config['alertPay_secure_code']; ?>" />
	<input name="ap_itemname" type="hidden" value="<?php echo $item_name; ?>" />
	<input name="ap_amount" type="hidden" value="<?php echo $price; ?>" />
	<input name="ap_currency" type="hidden" value="<?php echo $config['alertPay_currency_code']; ?>" />
	<input name="apc_1" type="hidden" value="<?php echo $data; ?>" />
	<input name="ap_quantity" type="hidden" value="1" />
	<?php if ( $account_info['First_name'] ) { ?>
	<input name="ap_fname" type="hidden" value="<?php echo $account_info['First_name']; ?>" />
	<?php } ?>
	<?php if ( $account_info['Last_name'] ) { ?>
	<input name="ap_lname" type="hidden" value="<?php echo $account_info['Last_name']; ?>" />
	<?php } ?>
	<?php if ( $account_info['Mail'] ) { ?>
	<input name="ap_contactemail" type="hidden" value="<?php echo $account_info['Mail']; ?>" />
	<?php } ?>
	<input name="ap_image" src="<?php echo RL_TPL_BASE; ?>img/logo.png" type="hidden" />
	<!--input type="hidden" name="ap_ipnversion" value="2" /-->
	<input type="hidden" name="ap_returnurl" value="<?php echo $success_url; ?>"/>
	<input type="hidden" name="ap_cancelurl" value="<?php echo $cancel_url; ?>"/>
	<input type="hidden" name="ap_alerturl" value="<?php echo $notify_url; ?>"/>
</form>