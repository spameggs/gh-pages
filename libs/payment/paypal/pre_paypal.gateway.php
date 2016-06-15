<?php
$crypted_price = crypt(sprintf("%.2f", $price), $config['paypal_secret_word']);
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$item          = base64_encode($data);
$notify_url    = RL_LIBS_URL . 'payment/paypal/post_paypal.gateway.php';
$host          = $config['paypal_sandbox'] ? 'sandbox.paypal.com' : 'www.paypal.com';
?>
<form name="payment_form" action="https://<?php echo $host; ?>/cgi-bin/webscr" method="post">
	<input type="hidden" name="item_number" value="<?php echo str_replace(' ', '+', $item); ?>" />
	<input type="hidden" name="currency_code" value="<?php echo $config['paypal_currency_code']; ?>" />
	<input type="hidden" name="business" value="<?php echo $config['paypal_account_email']; ?>" />
	<input type="hidden" name="cmd" value="_xclick" />
	<input type="hidden" name="no_shipping" value="1" />
	<input type="hidden" name="item_name" value="<?php echo $item_name; ?>" />
	<input type="hidden" name="amount" value="<?php echo $price; ?>" />
	<input type="hidden" name="return" value="<?php echo $success_url;?>" />
	<input type="hidden" name="cancel_return" value="<?php echo $cancel_url;?>" />
	<input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="charset" value="utf-8">
	<input type="hidden" name="image_url" value="<?php echo RL_TPL_BASE ?>img/logo.png">
</form>