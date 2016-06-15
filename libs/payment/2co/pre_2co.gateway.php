<?php
$crypted_price = crypt(sprintf("%.2f", $price), str_replace('http://', '', RL_URL_HOME));
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . $cancel_url . '|' . $success_url . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$item          = urlencode(base64_encode($data));
$order_id      = mt_rand() . time();
$notify_url    = RL_LIBS_URL . 'payment/2co/post_2co.gateway.php';
?>
<form name="payment_form" action="https://www.2checkout.com/checkout/purchase" method="post">
	<input type="hidden" name="sid" value="<?php echo $GLOBALS['config']['2co_id']; ?>" />
	<input type="hidden" name="cart_order_id" value="<?php echo $order_id; ?>" />
	<input type="hidden" name="item_number" value="<?php echo $item; ?>" />
	<input type="hidden" name="product_id" value="<?php echo $item_id; ?>" />
	<input type="hidden" name="total" value="<?php echo $price; ?>" />
	<input type="hidden" name="quantity" value="1" />
	<input type="hidden" name="credit_card_processed" value="Y" />
	<input type="hidden" name="x_receipt_link_url" value="<?php echo $notify_url; ?>" />
	<input type="hidden" name="c_name" value="<?php echo $item_name; ?>" >
	<input type="hidden" name="c_description" value="<?php echo $item_name; ?>" />
	<?php if ( $config['2co_testmode'] ) { ?>
	<input type="hidden" name="demo" value="Y" />
	<?php } ?>
</form>