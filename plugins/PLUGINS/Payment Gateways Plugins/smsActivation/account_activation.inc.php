<?php
if (defined('IS_LOGIN') && IS_LOGIN === true) {
    $login = SEO_BASE;
    $login .= $config['mod_rewrite'] ? "{$pages['my_profile']}.html" : "?page={$pages['my_profile']}";
    $reefless->redirect(null, $login);
}
$reefless->loadClass('SmsActivation', null, 'smsActivation');
$rlXajax->registerFunction(array(
    'smsActivationCheck',
    $rlSmsActivation,
    'ajax_check'
));
if (!$_REQUEST['xjxfun']) {
    $sms_username = $_SESSION['smsActication_username'];
    $reefless->loadClass('Account');
    $account_id       = $rlDb->getOne('ID', "`Username` = '{$sms_username}'", 'accounts');
    $sms_account_info = $rlAccount->getProfile((int) $account_id);
    if ($sms_account_info['smsActivation_code'] == 'done') {
        $login = SEO_BASE;
        $login .= $config['mod_rewrite'] ? "{$pages['login']}.html" : "?page={$pages['login']}";
        $reefless->redirect(null, $login);
    }
    $code_exist   = $sms_account_info['smsActivation_code'];
    $sms_code     = $code_exist ? $code_exist : rand(str_repeat(1, $config['sms_activation_code_length']), str_repeat(9, $config['sms_activation_code_length']));
    $phone_fields = explode(',', $config['sms_activation_phone_field']);
    $rlDb->query("UPDATE `" . RL_DBPREFIX . "accounts` SET `smsActivation` = '0', `smsActivation_code` = '{$sms_code}' WHERE `ID` = '{$account_id}' LIMIT 1");
    if (empty($phone_fields[0])) {
        $contact_us = SEO_BASE;
        $contact_us .= $config['mod_rewrite'] ? "{$pages['contact_us']}.html" : "?page={$pages['contact_us']}";
        $errors[] = $lang['smsActivation_phone_fields_doesnot_exist'];
        $notice   = preg_replace('/\[(.*)\]/', '<a href="' . $contact_us . '">$1</a>', $lang['smsActivation_account_approved']);
        $rlDebug->logger("smsActivation plugin | Phone fields does not exist.");
    }
    foreach ($phone_fields as $phone_field) {
        if (!empty($sms_account_info[$phone_field])) {
            $sms_phone_number = $sms_account_info[$phone_field];
            $sms_phone_field  = $phone_field;
            break;
        }
    }
    $rlSmarty->assign('success_code', str_replace(array(
        '{number}',
        '{phone}'
    ), array(
        $config['sms_activation_code_length'],
        $sms_phone_number
    ), $lang['smsActication_meesage_sent_text']));
    if (!$sms_phone_number) {
        $errors[] = $lang['smsActivation_no_phone_error'];
        $notice   = preg_replace('/(\[(.*)\])/', '<b>$1</b>', $lang['smsActivation_phone_value_doesnot_exist']);
        $rlSmarty->assign('login_form', true);
    }
    if (false !== strpos($sms_phone_number, 'c:')) {
        $sms_phone_number = $reefless->parsePhone($sms_phone_number, $reefless->fetch(array(
            'Opt1'
        ), array(
            'Key' => $sms_phone_field
        ), null, 1, 'account_fields', 'row'));
    }
    $sms_phone_number = str_replace(array(
        '+',
        '-',
        '(',
        ')',
        ' '
    ), '', $sms_phone_number);
    $rlSmarty->assign_by_ref('notice', $notice);
    if ($errors) {
        $rlSmarty->assign_by_ref('errors', $errors);
    } else {
        if (empty($account_info['smsActivation_code'])) {
            $sms_text       = urlencode(str_replace('{code}', $sms_code, $lang['smsActivation_message_text']));
            $clickatell_url = 'http://api.clickatell.com/http/sendmsg';
            $request        = 'user=' . $config['sms_activation_username'] . '&password=' . $config['sms_activation_password'] . '&api_id=' . $config['sms_activation_api_id'];
            $request .= '&to=' . $sms_phone_number . '&text=' . $sms_text;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $clickatell_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            if (!is_numeric(strpos($response, 'ID:'))) {
                $contact_us = SEO_BASE;
                $contact_us .= $config['mod_rewrite'] ? "{$pages['contact_us']}.html" : "?page={$pages['contact_us']}";
                $errors[] = str_replace('{error}', $response, $lang['smsActivation_sending_fail']);
                $notice   = preg_replace('/\[(.*)\]/', '<a href="' . $contact_us . '">$1</a>', $lang['smsActivation_sending_fail_notice']);
                $rlSmarty->assign('login_form', true);
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                $reefless->loadClass('Notice');
                $rlNotice->saveNotice($lang['smsActication_meesage_sent']);
            }
        }
    }
}