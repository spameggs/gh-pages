<?php
$reefless->loadClass('Notice');
$reefless->loadClass('Actions');
$reefless->loadClass('Invoices', null, 'invoices');
$invoice_id = $_GET['nvar_1'] ? $_GET['nvar_1'] : $_REQUEST['item'];
$rlValid->sql($invoice_id);
if (!empty($invoice_id)) {
    $sql               = "SELECT * FROM `" . RL_DBPREFIX . "invoices` WHERE `Account_ID` = '{$account_info['ID']}' AND `Txn_ID` = '{$invoice_id}' LIMIT 1";
    $invoice_info      = $rlDb->getRow($sql);
    $page_info['name'] = $invoice_info['Subject'];
    $bread_crumbs[]    = array(
        'name' => $invoice_info['Txn_ID']
    );
    $rlSmarty->assign_by_ref('invoice_info', $invoice_info);
    if (!empty($invoice_info)) {
        if ($_POST['submit']) {
            $gateway = $_POST['gateway'];
            if (empty($gateway)) {
                $errors[] = $lang['notice_payment_gateway_does_not_chose'];
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                $cancel_url = SEO_BASE;
                $cancel_url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html?canceled' : 'index.php?page=' . $page_info['Path'] . '&amp;canceled';
                $cancel_url .= '&item=' . $invoice_id;
                $success_url = SEO_BASE;
                $success_url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html?completed' : 'index.php?page=' . $page_info['Path'] . '&amp;completed';
                $success_url .= '&item=' . $invoice_id;
                $plan_info                    = array(
                    'Price' => $invoice_info['Total']
                );
                $complete_payment_info        = array(
                    'item_name' => $invoice_info['Subject'] . ' (#' . $invoice_id . ')',
                    'gateway' => $gateway,
                    'item_id' => $invoice_id,
                    'plan_info' => $plan_info,
                    'account_id' => $account_info['ID'],
                    'callback' => array(
                        'class' => 'rlInvoices',
                        'method' => 'completeTransaction',
                        'cancel_url' => $cancel_url,
                        'success_url' => $success_url,
                        'plugin' => "Invoices"
                    )
                );
                $_SESSION['complete_payment'] = $complete_payment_info;
                $redirect                     = SEO_BASE;
                $redirect .= $config['mod_rewrite'] ? $pages['payment'] . '.html' : 'index.php?page=' . $pages['payment'];
                $reefless->redirect(null, $redirect);
                exit;
            }
        }
    } else {
        $sError = true;
    }
} else {
    if (isset($_GET['canceled']) || isset($_GET['completed'])) {
        if (isset($_GET['completed'])) {
            $rlNotice->saveNotice($lang['invoices_payment_completed']);
        }
        if (isset($_GET['canceled'])) {
            $errors[] = $lang['invoices_payment_canceled'];
            $rlSmarty->assign_by_ref('errors', $errors);
        }
    }
    $sql = "SELECT `T1`.*, `T2`.`Gateway`  ";
    $sql .= "FROM `" . RL_DBPREFIX . "invoices` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "transactions` AS `T2` ON `T1`.`Txn_ID` = `T2`.`Txn_ID` ";
    $sql .= "WHERE `T1`.`Account_ID` = '{$_SESSION['id']}' ORDER BY `T1`.`Date` DESC";
    $invoices = $rlDb->getAll($sql);
    $rlSmarty->assign_by_ref('invoices', $invoices);
}