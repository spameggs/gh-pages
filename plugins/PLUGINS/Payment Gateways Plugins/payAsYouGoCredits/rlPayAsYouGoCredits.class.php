<?php
class rlPayAsYouGoCredits extends reefless
{
    function rlPayAsYouGoCredits()
    {
    }
    function completeTransaction($item_id, $plan_id = false, $account_id = false, $txn_id = null, $gateway = null, $total = false)
    {
        $this->loadClass('Actions');
        $this->loadClass('Mail');
        $txn_id       = mysql_real_escape_string($txn_id);
        $gateway      = mysql_real_escape_string($gateway);
        $item_id      = (int) $item_id;
        $account_info = $this->fetch('*', array(
            'ID' => $account_id
        ), null, 1, 'accounts', 'row');
        $credit_info  = $this->fetch('*', array(
            'ID' => $item_id
        ), null, 1, 'credits_manager', 'row');
        if (!empty($account_info) && !empty($credit_info)) {
            $total          = $account_info['Total_credits'] + $credit_info['Credits'];
            $account_update = array(
                'fields' => array(
                    'Total_credits' => (float) $total,
                    'paygc_pay_date' => 'NOW()'
                ),
                'where' => array(
                    'ID' => $account_id
                )
            );
            if ($GLOBALS['rlActions']->updateOne($account_update, 'accounts')) {
                $account_name     = $account_info['First_name'] || $account_info['Last_name'] ? $account_info['First_name'] . ' ' . $account_info['Last_name'] : $account_info['Username'];
                $search           = array(
                    '{username}',
                    '{gateway}',
                    '{txn}',
                    '{item}',
                    '{price}',
                    '{date}'
                );
                $replace          = array(
                    $account_name,
                    $gateway,
                    $txn_id,
                    $invoice_info['Subject'],
                    $total,
                    date(str_replace(array(
                        'b',
                        '%'
                    ), array(
                        'M',
                        ''
                    ), RL_DATE_FORMAT))
                );
                $mail_tpl         = $GLOBALS['rlMail']->getEmailTemplate('payment_accepted');
                $mail_tpl['body'] = str_replace($search, $replace, $mail_tpl['body']);
                $GLOBALS['rlMail']->send($mail_tpl, $account_info['Mail']);
                $mail_tpl         = $GLOBALS['rlMail']->getEmailTemplate('admin_listing_paid');
                $search           = array(
                    '{id}',
                    '{username}',
                    '{gateway}',
                    '{txn}',
                    '{item}',
                    '{price}',
                    '{date}'
                );
                $replace          = array(
                    $item_id,
                    $account_info['Username'],
                    $gateway,
                    $txn_id,
                    $invoice_info['Subject'],
                    $total,
                    date(str_replace(array(
                        'b',
                        '%'
                    ), array(
                        'M',
                        ''
                    ), RL_DATE_FORMAT))
                );
                $mail_tpl['body'] = str_replace($search, $replace, $mail_tpl['body']);
                $GLOBALS['rlMail']->send($mail_tpl, $GLOBALS['config']['notifications_email']);
                $transaction = array(
                    'Service' => 'credits',
                    'Item_ID' => $item_id,
                    'Account_ID' => $account_id,
                    'Plan_ID' => 0,
                    'Txn_ID' => $txn_id,
                    'Total' => $total,
                    'Gateway' => $gateway,
                    'Date' => 'NOW()'
                );
                $GLOBALS['rlActions']->insertOne($transaction, 'transactions');
            }
        }
        return true;
    }
    function ajaxDeleteCreditItem($id = false)
    {
        global $_response, $lang;
        if (!$id)
            return $_response;
        $delete = "DELETE FROM `" . RL_DBPREFIX . "credits_manager` WHERE `ID` = '{$id}' LIMIT 1";
        $this->query($delete);
        $sql_update = "UPDATE `" . RL_DBPREFIX . "config` SET `Default` = ROUND((SELECT MAX(@Price_one:=`Price`/`Credits`) AS `MaxPriceCredit` FROM `" . RL_DBPREFIX . "credits_manager` LIMIT 1), 2) WHERE `Key` = 'paygc_rate_hide'";
        $this->query($sql_update);
        $_response->script("
			creditsGrid.reload();
			printMessage('notice', '{$lang['item_deleted']}');
		");
        return $_response;
    }
}