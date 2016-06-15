<?php
$price         = number_format($price, 2);
$crypted_price = crypt(sprintf("%.2f", $price), $config['alertPay_secure_code']);
$data          = $plan_id . '|' . $item_id . '|' . $account_id . '|' . $crypted_price . '|' . $callback_class . '|' . $callback_method . '|' . $cancel_url . '|' . $success_url . '|' . RL_LANG_CODE . '|' . $callback_plugin;
$data          = base64_encode($data);
if (!empty($plan_id)) {
    $plan_info = $rlDb->fetch(array(
        'ID',
        'ccbill_allowedTypes'
    ), array(
        'ID' => $plan_id
    ), null, 1, 'listing_plans', 'row');
}
$allowedTypes = !empty($plan_info['ccbill_allowedTypes']) ? $plan_info['ccbill_allowedTypes'] : 0.00;
if ($config['ccbill_test_mode']) {
    $html = '<input type="hidden" name="testMode" value="' . $config['ccbill_test_mode'] . '" />';
}
?>
<form name="payment_form" method="post" action="https://bill.ccbill.com/jpost/signup.cgi">
	<input  type="hidden"  name="clientAccnum"  value="<? echo $config['ccbill_clientAccnum']; ?>" />
	<input  type="hidden"  name="clientSubacc"  value="<? echo $config['ccbill_clientSubacc']; ?>" />
	<input  type="hidden"  name="formName"  value="<? echo $config['ccbill_formName']; ?>" />
	<input  type="hidden"  name="item_number"  value="<? echo $data; ?>" />
	<input  type="hidden"  name="language" value="<?php echo $config['ccbill_language']; ?>" />
	<input  type="hidden"  name="allowedTypes" value="<?php echo $allowedTypes; ?>" />
	<?php echo $html; ?>
</form>