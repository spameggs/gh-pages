<?php
class rlSmsActivation extends reefless
{
    var $access = false;
    function rlSmsActivation()
    {
        global $config, $account_info, $rlSmarty, $page_info, $lang, $errors, $pages;
        if (defined('IS_LOGIN') && $config['sms_activation_module'] && $config['sms_activation_username'] && $config['sms_activation_api_id'] && $config['sms_activation_password']) {
            if (!$account_info['smsActivation']) {
                $this->access = true;
                $allowed      = array(
                    'my_profile',
                    'my_messages'
                );
                $account_menu = $rlSmarty->get_template_vars('account_menu');
                foreach ($account_menu as $key => $account_menu_item) {
                    $account_meny_keys[] = $account_menu_item['Key'];
                    if (!in_array($account_menu_item['Key'], $allowed)) {
                        unset($account_menu[$key]);
                    }
                }
                $rlSmarty->assign_by_ref('account_menu', $account_menu);
                if (!in_array($page_info['Key'], $allowed) && in_array($page_info['Key'], $account_meny_keys)) {
                    $page_info['Controller'] = '404';
                    $activate_href           = SEO_BASE;
                    $activate_href .= $config['mod_rewrite'] ? "{$pages['my_profile']}.html" : "?page={$pages['my_profile']}";
                    $errors[] = preg_replace('/\[(.*)\]/', '<a href="' . $activate_href . '">$1</a>', $lang['smsActivation_access_deny']);
                }
            }
        }
    }
    function profileTab()
    {
        global $tabs, $lang, $rlSmarty, $account_info, $config, $rlXajax;
        if ($this->access) {
            unset($tabs['password']);
            $tabs['account']['active'] = false;
            $tabs['profile']['active'] = false;
            $tabs_back                 = $tabs;
            $tabs                      = array();
            $tabs['smsActivation']     = array(
                'key' => 'smsActivation',
                'name' => $lang['smsActivation_tab_caption'],
                'active' => true
            );
            $tabs                      = array_merge($tabs, $tabs_back);
            $phone_fields              = explode(',', $config['sms_activation_phone_field']);
            foreach ($phone_fields as $phone_field) {
                if (!empty($account_info[$phone_field])) {
                    $sms_phone_number = $account_info[$phone_field];
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
            ), $lang['smsActivation_profile_text']));
            $rlXajax->registerFunction(array(
                'smsActivationCheckp',
                $GLOBALS['rlSmsActivation'],
                'ajax_checkp'
            ));
            $rlXajax->registerFunction(array(
                'smsActivationSendCode',
                $GLOBALS['rlSmsActivation'],
                'ajax_sendCode'
            ));
        }
    }
    function ajax_check($code = false)
    {
        global $_response, $config, $pages, $lang, $account_info;
        $sms_username = $_SESSION['smsActication_username'];
        if (empty($sms_username)) {
            $mess = $lang['smsActivation_sesseion_expired'];
            $_response->script("printMessage('error', '{$mess}')");
            $tpl = RL_PLUGINS . 'smsActivation' . RL_DS . 'sesExpired.tpl';
            $_response->script("$('#smsActivation_container').next().remove()");
            $_response->assign('smsActivation_container', 'innerHTML', $GLOBALS['rlSmarty']->fetch($tpl, null, null, false));
        } else {
            $code = (int) $code;
            $res  = $this->getOne('ID', "`Username` = '{$sms_username}' AND `smsActivation_code` = {$code}", 'accounts');
            if ($res) {
                $this->query("UPDATE `" . RL_DBPREFIX . "accounts` SET `smsActivation` = '1', `smsActivation_code` = 'done' WHERE `ID` = '{$res}' LIMIT 1");
                $account_info['smsActivation']        = 1;
                $_SESSION['account']['smsActivation'] = 1;
                $_response->script("printMessage('notice', '{$lang['smsActivation_activated']}')");
                $tpl = RL_PLUGINS . 'smsActivation' . RL_DS . 'completed.tpl';
                $_response->script("$('#smsActivation_container').next().remove()");
                $_response->assign('smsActivation_container', 'innerHTML', $GLOBALS['rlSmarty']->fetch($tpl, null, null, false));
                unset($_SESSION['smsActication_username']);
            } else {
                $mess = $lang['smsActivation_code_is_wrong'];
                $_response->script("
					printMessage('error', '{$mess}');
					$('input[name=sms_submit]').val('{$lang['smsActivation_confirm']}');
				");
            }
        }
        return $_response;
    }
    function ajax_checkp($code = false)
    {
        global $_response, $config, $pages, $lang, $account_info;
        $code = (int) $code;
        $res  = $this->getOne('ID', "`ID` = '{$account_info['ID']}' AND `smsActivation_code` = {$code}", 'accounts');
        if ($res) {
            $this->query("UPDATE `" . RL_DBPREFIX . "accounts` SET `smsActivation` = '1', `smsActivation_code` = 'done' WHERE `ID` = '{$res}' LIMIT 1");
            $account_info['smsActivation']        = 1;
            $_SESSION['account']['smsActivation'] = 1;
            $this->loadClass('Notice');
            $GLOBALS['rlNotice']->saveNotice($lang['smsActivation_activated_aa']);
            $link = SEO_BASE;
            $link .= $config['mod_rewrite'] ? "{$pages['my_profile']}.html" : "?page={$pages['my_profile']}";
            $_response->redirect($link);
        } else {
            $_response->script("
				printMessage('error', '{$lang['smsActivation_code_is_wrong']}');
				$('input[name=sms_submit]').val('{$lang['smsActivation_confirm']}');
			");
        }
        $_response->script("$('#sms_loading').fadeOut()");
        return $_response;
    }
    function ajax_sendCode($code = false)
    {
        global $_response, $config, $pages, $lang, $rlAccount, $account_info;
        $phone_fields = explode(',', $config['sms_activation_phone_field']);
        if (empty($phone_fields[0])) {
            $_response->script("printMessage('error', '{$lang['smsActivation_phone_fields_doesnot_exist']}')");
            $error = true;
        }
        foreach ($phone_fields as $phone_field) {
            if (!empty($account_info[$phone_field])) {
                $sms_phone_number = $account_info[$phone_field];
                break;
            }
        }
        if (!$sms_phone_number) {
            $_response->script("printMessage('error', '{$lang['smsActivation_no_phone_error']}')");
            $error = true;
        }
        if (false !== strpos($sms_phone_number, 'c:')) {
            $sms_phone_number = $this->parsePhone($sms_phone_number, $reefless->fetch(array(
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
        if (!$error) {
            $sms_code       = rand(str_repeat(1, $config['sms_activation_code_length']), str_repeat(9, $config['sms_activation_code_length']));
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
            if (false === strpos($response, 'ID:')) {
                $mess = str_replace('{error}', $response, $lang['smsActivation_sending_fail']);
                $_response->script("
					printMessage('error', '{$mess}');
					$('input[name=new_code]').val('{$lang['smsActivation_get_code']}');
				");
            } else {
                $this->query("UPDATE `" . RL_DBPREFIX . "accounts` SET `smsActivation` = '0', `smsActivation_code` = '{$sms_code}' WHERE `ID` = '{$account_info['ID']}' LIMIT 1");
                $mess = str_replace('{number}', $sms_phone_number, $lang['smsActivation_regenerated']);
                $_response->script("printMessage('notice', '{$mess}')");
            }
        }
        return $_response;
    }
}