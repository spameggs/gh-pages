<?php
$price       = round(($price * 100), 2);
$data        = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $price . '|' . $callback_class . '|' . $callback_method . '|' . $cancel_url . '|' . $success_url . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$data        = base64_encode($data);
$success_url = $success_url . '&amp;item_number=' . $data;
$reference   = $reefless->generateHash(8, 'upper');
$signature   = md5($GLOBALS['config']['paytpv_account'] . $GLOBALS['config']['paytpv_usercode'] . $GLOBALS['config']['paytpv_terminal'] . 1 . $reference . $price . $GLOBALS['config']['paytpv_currency'] . md5($GLOBALS['config']['paytpv_password']));
$notify_url  = RL_PLUGINS_URL . 'paytpv/controllers/post.gateway.php?item_number=' . $data . '&amp;ref=' . $reference . '&amp;s=' . $signature;
$payment_url = 'https://www.paytpv.com/gateway/fsgateway.php';
?>
<form name="payment_form" action="<?php echo $payment_url; ?>" method="post"> 
	<input type="hidden" name="ACCOUNT" value="<?php echo $GLOBALS['config']['paytpv_account']; ?>" />
	<input type="hidden" name="USERCODE" value="<?php echo $GLOBALS['config']['paytpv_usercode'];; ?>">
	<input type="hidden" name="TERMINAL" value="<?php echo $GLOBALS['config']['paytpv_terminal']; ?>" /> 
	<input type="hidden" name="OPERATION" value="1" /> 
	<input type="hidden" name="REFERENCE" value="<?php echo $reference; ?>" />  
	<input type="hidden" name="AMOUNT" value="<?php echo $price; ?>" />  
	<input type="hidden" name="CURRENCY" value="<?php echo $GLOBALS['config']['paytpv_currency']; ?>" />
	<input type="hidden" name="SIGNATURE" value="<?php echo $signature; ?>" />
	<input type="hidden" name="CONCEPT" value="<?php echo $item_name; ?>" />
	<input type="hidden" name="URLOK" value="<?php echo $notify_url; ?>" />
	<input type="hidden" name="URLKO" value="<?php echo $cancel_url; ?>" />
</form>