<?php
$merchant_id   = $config['googleCheckout_merchant_id'];
$currency      = $config['googleCheckout_currency'];
$test_mode     = $config['googleCheckout_test_mode'];
$crypted_price = crypt(sprintf("%.2f", $price), str_replace('http://', '', RL_URL_HOME));
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . str_replace('&amp;', '&', $cancel_url) . '|' . str_replace('&amp;', '&', $success_url) . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$item          = urlencode(base64_encode($data));
$continue_url  = $config['mod_rewrite'] ? SEO_BASE : RL_URL_HOME;
$notify_url    = RL_PLUGINS_URL . 'googleCheckout/controllers/post.gateway.php?item=' . $item . '&flsessid=' . session_id() . '&total=' . $price;
if ($test_mode) {
    $host = 'https://sandbox.google.com/checkout/api/checkout/v2/checkout/Merchant/' . $merchant_id;
} else {
    $host = 'https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/' . $merchant_id;
}
?>
<form method="post" name="payment_form" action="<?php echo $host; ?>" accept-charset="utf-8">
	<input type="hidden" name="item_name_1" value="<?php echo $item_name; ?>" />
	<input type="hidden" name="item_description_1" value="<?php echo str_replace('{domain}', $rlValid -> getDomain(RL_URL_HOME), $lang['googleCheckout_order_description']); ?>" />
	<input type="hidden" name="item_price_1" value="<?php echo $price; ?>" />
	<input type="hidden" name="item_currency_1" value="<?php echo $currency; ?>" />
	<input type="hidden" name="item_quantity_1" value="1" />
	<input type="hidden" name="continue_url" value="<?php echo $continue_url; ?>" />
	<input type="hidden" name="shopping-cart.items.item-1.digital-content.display-disposition" value="OPTIMISTIC" />
	<input type="hidden" name="shopping-cart.items.item-1.digital-content.description" value="<?php echo $lang['googleCheckout_order_page_notice']; ?>" />
	<input type="hidden" name="shopping-cart.items.item-1.digital-content.url" value="<?php echo $notify_url; ?>" />
	<input type="hidden" name="_charset_" />
</form>