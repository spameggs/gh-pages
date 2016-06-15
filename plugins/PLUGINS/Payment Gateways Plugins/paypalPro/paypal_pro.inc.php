<?php
$reefless->loadClass('Categories');
$reefless->loadClass('Cache');
$reefless->loadClass('PaypalPro', null, 'paypalPro');
if ($_POST['form']) {
    $data = $_POST['dpp'];
    if (empty($data['country'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_country'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if (empty($data['card_number'][1]) || empty($data['card_number'][2]) || empty($data['card_number'][3]) || empty($data['card_number'][4])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_card_number'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    } else {
        $data['card_number'] = $data['card_number'][1] . $data['card_number'][2] . $data['card_number'][3] . $data['card_number'][4];
        if (strlen($data['card_number']) < 16) {
            $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_card_number'] . "</b>", $GLOBALS['lang']['notice_field_incorrect']);
        }
    }
    if (empty($data['csc'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_csc'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if ($data['month'] > 12 || $data['month'] < 1 || (int) $data['year'] < (int) date('Y')) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_expiration_date'] . "</b>", $GLOBALS['lang']['notice_field_incorrect']);
    }
    if (empty($data['first_name'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_first_name'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if (empty($data['last_name'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_last_name'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if (empty($data['address_1'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_address_1'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if (empty($data['phone'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_phone'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if (empty($data['email'])) {
        $errors[] = str_replace('{field}', "<b>" . $GLOBALS['lang']['dpp_email'] . "</b>", $GLOBALS['lang']['notice_field_empty']);
    }
    if (empty($data['state']) && !empty($data['us_state'])) {
        $data['state'] = $data['us_state'];
    }
    if (!$errors) {
        $payment       = $_SESSION['complete_payment'];
        $price         = (float) $payment['plan_info']['Price'];
        $http_response = $rlPaypalPro->post($data, $price);
        if ($GLOBALS['config']['dpp_test_mode']) {
            $file = fopen(RL_PLUGINS . 'paypalPro/errors.log', 'a');
            if ($file) {
                $line = "\n\n" . date('Y.m.d H:i:s') . ":\n";
                fwrite($file, $line);
                foreach ($http_response as $r_key => $r_val) {
                    $line = "{$r_key} => {$r_val}\n";
                    fwrite($file, $line);
                }
                fclose($file);
            }
        }
        $plan_id         = $payment['plan_info']['ID'];
        $item_id         = $payment['item_id'];
        $account_id      = $payment['account_id'];
        $callback_class  = $payment['callback']['class'];
        $callback_method = $payment['callback']['method'];
        $callback_plugin = $payment['callback']['plugin'];
        $cancel_url      = $payment['callback']['cancel_url'];
        $success_url     = $payment['callback']['success_url'];
        unset($_SESSION['complete_payment']);
        if ($http_response['ACK'] == 'Success') {
            $reefless->loadClass(str_replace('rl', '', $callback_class), null, $callback_plugin);
            $GLOBALS[$callback_class]->$callback_method($item_id, $plan_id, $account_id, $http_response['TRANSACTIONID'], 'directPaypal', $price);
            $reefless->redirect(false, str_replace('&amp;', '&', $success_url));
        } else {
            $reefless->redirect(false, str_replace('&amp;', '&', $cancel_url));
        }
        exit;
    } else {
        $rlSmarty->assign_by_ref('errors', $errors);
    }
}
$card_types = array(
    'Visa' => $GLOBALS['lang']['dpp_visa'],
    'Mastercard' => $GLOBALS['lang']['dpp_mastercard'],
    'Discover' => $GLOBALS['lang']['dpp_discover'],
    'Amex' => $GLOBALS['lang']['dpp_amex']
);
$rlSmarty->assign_by_ref('card_types', $card_types);
$countries = $rlDb->fetch(array(
    'iso',
    'printable_name'
), false, "ORDER BY `printable_name` ", false, 'iso_countries');
$rlSmarty->assign_by_ref('countries', $countries);
$us_states = $rlDb->fetch(array(
    'iso',
    'name'
), false, "ORDER BY `name` ", false, 'iso_states');
$rlSmarty->assign_by_ref('us_states', $us_states);
if (empty($_SESSION['complete_payment']['service']) || $_SESSION['complete_payment']['service'] == 'listing' || $_SESSION['complete_payment']['service'] == 'package') {
    $reefless->loadClass('Plan');
    $listing   = $rlListings->getShortDetails($_SESSION['complete_payment']['item_id'], $plan_info = true);
    $plan_info = $rlPlan->getPlan($_SESSION['complete_payment']['plan_info']['ID']);
    $rlSmarty->assign_by_ref('listing', $listing);
    $rlSmarty->assign_by_ref('plan_info', $plan_info);
}
$months = array(
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    10,
    11,
    12
);
$rlSmarty->assign_by_ref('months', $months);
$year_start = date('Y') + 10;
$year_end   = date('Y') - 3;
for ($i = $year_start; $i > $year_end; $i--) {
    $years[] = $i;
}
$rlSmarty->assign_by_ref('years', $years);