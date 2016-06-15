<?php
$crypted_price = crypt(sprintf("%.2f", $price), $config['paypal_secret_word']);
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$data          = base64_encode($data);
$notify_url    = RL_PLUGINS_URL . 'paypal_subscription/controllers/post.gateway.php';
$paypal_url    = $config['paypal_subscription_test_mode'] ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
?>
<form name="payment_form" action="<?php echo $paypal_url;?>" method="post">
	<input type="hidden" name="cmd" value="_xclick-subscriptions" />
	<input type="hidden" name="image_url" value="<?php echo RL_TPL_BASE ?>img/logo.png">
	<input type="hidden" name="item_number" value="<?php echo $data; ?>" />
	<input type="hidden" name="currency_code" value="<?php echo $config['paypal_subscription_currency_code']; ?>" />
	<input type="hidden" name="business" value="<?php echo $config['paypal_subscription_account_email']; ?>" />

	<input type="hidden" name="no_shipping" value="1" />
	<input type="hidden" name="item_name" value="<?php echo $item_name;?>" />
	<input type="hidden" name="return" value="<?php echo $success_url;?>" />
	<input type="hidden" name="cancel_return" value="<?php echo $cancel_url;?>" />
	<input type="hidden" name="notify_url" value="<?php echo $notify_url;?>" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="a3" value="<?php echo $price;?>" />
	<input type="hidden" name="p3" value="<?php echo $time;?>" />
	<input type="hidden" name="t3" value="<?php echo $time_period;?>" />
	<input type="hidden" name="src" value="1" />
	<input type="hidden" name="sra" value="1" />
	<input type="hidden" name="no_note" value="1" />
	<input type="hidden" name="image_url" value="<?php echo RL_TPL_BASE ?>img/logo.png" />
	
	<?php if ( RL_LANG_CODE != 'en' ) { ?>
	<input type="hidden" name="lc" value="<?php echo RL_LANG_CODE; ?>" />
	<?php } ?>
</form>