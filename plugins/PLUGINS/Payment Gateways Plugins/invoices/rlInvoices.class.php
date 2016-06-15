<?php
class rlInvoices extends reefless
{
    function rlInvoices()
    {
    }
    function completeTransaction($item_id, $plan_id = false, $account_id = false, $txn_id = null, $gateway = null, $total = false)
    {
        $this->loadClass('Actions');
        $this->loadClass('Mail');
        $txn_id       = mysql_real_escape_string($txn_id);
        $gateway      = mysql_real_escape_string($gateway);
        $item_id      = (int) $item_id;
        $invoice_info = $this->fetch('*', array(
            'ID' => $item_id
        ), null, 1, 'invoices', 'row');
        if (!empty($invoice_info)) {
            $invoice_update = array(
                'fields' => array(
                    'pStatus' => 'paid',
                    'Pay_date' => 'NOW()',
                    'IP' => $_SERVER['REMOTE_ADDR']
                ),
                'where' => array(
                    'ID' => $item_id
                )
            );
            if ($GLOBALS['rlActions']->updateOne($invoice_update, 'invoices')) {
                $account_info     = $this->fetch(array(
                    'Username',
                    'First_name',
                    'Last_name',
                    'Mail'
                ), array(
                    'ID' => $account_id
                ), null, 1, 'accounts', 'row');
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
                    'Service' => 'invoice',
                    'Item_ID' => $item_id,
                    'Account_ID' => $account_id,
                    'Plan_ID' => 0,
                    'Txn_ID' => $invoice_info['Txn_ID'],
                    'Total' => $total,
                    'Gateway' => $gateway,
                    'Date' => 'NOW()'
                );
                $GLOBALS['rlActions']->insertOne($transaction, 'transactions');
            }
        }
        return true;
    }
    function generate($txn_tpl = 'INV*******')
    {
        $txn_length = substr_count($txn_tpl, '*');
        if ($txn_length > 2) {
            $tmp_hash = $this->generateHash($txn_length, 'upper');
        } else {
            $tmp_hash = $this->generateHash(5, 'upper');
        }
        $mask = str_replace("*", "", $txn_tpl);
        $txn  = $mask . $tmp_hash;
        unset($tmp_hash, $mask);
        if ($this->getOne("Txn_ID", "`Txn_ID` = '{$txn}'", 'invoices')) {
            return $this->generate($txn_tpl);
        } else {
            return $txn;
        }
    }
    function ajaxDeleteItem($id = false)
    {
        global $_response, $lang;
        if (!$id)
            return $_response;
        $delete = "DELETE FROM `" . RL_DBPREFIX . "invoices` WHERE `ID` = '{$id}' LIMIT 1";
        $this->query($delete);
        $_response->script("
			invoicesGrid.reload();
			printMessage('notice', '{$lang['item_deleted']}');
		");
        return $_response;
    }
}