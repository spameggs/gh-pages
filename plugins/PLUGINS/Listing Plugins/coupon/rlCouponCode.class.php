<?php
class rlCouponCode extends reefless
{
    function ajaxCheckCouponCode($coupon, $plan_id, $diffuse = false, $renew = false)
    {
        global $_response, $lang, $pages, $config;
        if ($diffuse) {
            if (!empty($renew)) {
                $link = SEO_BASE;
                $link .= $config['mod_rewrite'] ? $pages['payment'] . '.html' : 'index.php?page=' . $pages['Path'];
                $_response->script("$('[name=\"payment\"]').attr('action','" . $link . "');");
            }
            unset($_SESSION['coupon_code']);
            $_response->script("$('#coupon_code_info').hide();$('#coupon_code').show();");
        } else {
            $plan_price   = $this->getOne('Price', "`Status` = 'active' AND `ID` = '{$plan_id}'", 'listing_plans');
            $sql          = "SELECT *, UNIX_TIMESTAMP(`Date_from`) AS `Date_from`, UNIX_TIMESTAMP(`Date_to`) AS `Date_to` FROM `" . RL_DBPREFIX . "coupon_code` WHERE `Code` = '{$coupon}'";
            $coupon_info  = $this->getRow($sql);
            $account_info = $_SESSION['account'];
            unset($_SESSION['coupon_code']);
            if (!empty($coupon_info)) {
                $checkup = $this->fetch(array(
                    'Coupon_ID',
                    'Account_ID'
                ), array(
                    'Coupon_ID' => $coupon_info['ID'],
                    'Account_ID' => $account_info['ID']
                ), null, null, 'coupon_users');
                if ($plan_price > 0 && $coupon_info['Using_limit'] > count($checkup) || $coupon_info['Using_limit'] == '0') {
                    if ($coupon_info['Account_or_type'] == 'type' && !in_array($account_info['Type'], explode(',', $coupon_info['Account_type'])) || $coupon_info['Account_or_type'] == 'account' && $account_info['Username'] != $coupon_info['Username']) {
                        $error = $lang['coupon_not_account'];
                    } elseif ($coupon_info['Sticky'] == 0 && !in_array($plan_id, explode(',', $coupon_info['Plan_ID']))) {
                        $error = $lang['coupon_not_plan'];
                    } elseif ($coupon_info['Used_date'] == 'yes' && ($coupon_info['Date_from'] >= mktime() || mktime() >= $coupon_info['Date_to'])) {
                        $error = $lang['coupon_expired'];
                    }
                } elseif ($coupon_info['Using_limit'] <= count($checkup) && $coupon_info['Using_limit'] != '0') {
                    $error = $lang['your_coupon_limit_is_over'];
                } else {
                    $error = $lang['coupon_code_is_incorrect'];
                }
            } else {
                $error = $lang['coupon_not_found'];
            }
            if ($error) {
                $_response->script("printMessage('error', '{$error}');");
            } else {
                if ($coupon_info['Type'] == 'cost') {
                    $total    = $plan_price - $coupon_info['Discount'];
                    $discount = $coupon_info['Discount'];
                } elseif ($coupon_info['Type'] == 'persent') {
                    $total    = $plan_price - (($plan_price / 100) * $coupon_info['Discount']);
                    $discount = $coupon_info['Discount'] . '%';
                }
                if ($total < 0) {
                    $total = 0;
                }
                $coupon_price_info['price']    = $plan_price;
                $coupon_price_info['discount'] = $discount;
                $coupon_price_info['total']    = $total;
                $_SESSION['coupon_code']       = $coupon;
                $GLOBALS['rlSmarty']->assign_by_ref('coupon_price_info', $coupon_price_info);
                $GLOBALS['rlSmarty']->assign_by_ref('coupon_code', $coupon);
                $tpl = RL_PLUGINS . 'coupon' . RL_DS . 'coupon_price_info.tpl';
                if (!empty($renew) && $total == 0) {
                    $referor = $_SERVER['HTTP_REFERER'];
                    $_response->script("$('[name=\"payment\"]').attr('action','" . $referor . "');");
                }
                $_response->script("$('#coupon_code').hide();$('#coupon_code_info').show();");
                $_response->assign('coupon_code_info', 'innerHTML', $GLOBALS['rlSmarty']->fetch($tpl, null, null, false));
            }
            $_response->script("$('#check_coupon').val('{$lang['apply']}');");
        }
        return $_response;
    }
    function editPrice($plan_price, $coupon)
    {
        $price = $plan_price['Price'];
        if (!empty($coupon)) {
            $coupon_info = $this->fetch('*', array(
                'Code' => $coupon,
                'Status' => 'active'
            ), "AND ( `Used_date` = 'no' OR UNIX_TIMESTAMP(`Date_from`) < UNIX_TIMESTAMP(NOW()) AND UNIX_TIMESTAMP(`Date_to`) > UNIX_TIMESTAMP(NOW()))", 1, 'coupon_code', 'row');
            if ($coupon_info) {
                $checkup = $this->fetch(array(
                    'Coupon_ID',
                    'Account_ID'
                ), array(
                    'Plan_ID' => $plan_price['ID'],
                    'Coupon_ID' => $coupon_info['ID'],
                    'Account_ID' => $account_info['ID']
                ), null, null, 'coupon_users');
                if ($price > 0) {
                    if ($coupon_info['Type'] == 'cost') {
                        $price = $price - $coupon_info['Discount'];
                    } elseif ($coupon_info['Type'] == 'persent') {
                        $price = $price - (($price * $coupon_info['Discount']) / 100);
                    }
                }
                if ($price < 0) {
                    $price = 0;
                }
            }
        }
        return $price;
    }
    function ajaxDeleteCoupon($coupon_id = false)
    {
        global $_response;
        $coupon_id = (int) $coupon_id;
        if ($this->checkSessionExpire() === false) {
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php?action=session_expired');
            return $_response;
        }
        if (!$coupon_id) {
            return $_response;
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "coupon_code` WHERE `ID` = '{$coupon_id}' LIMIT 1");
        $_response->script("
				CouponCodeGrid.reload();
				printMessage('notice', '{$GLOBALS['lang']['item_deleted']}')
			");
        return $_response;
    }
}