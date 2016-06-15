<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Invoices', null, 'invoices');
        $reefless->loadClass('Actions');
        $type       = $rlValid->xSql($_GET['type']);
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $id         = $rlValid->xSql($_GET['id']);
        $key        = $rlValid->xSql($_GET['key']);
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $sql   = "SELECT SQL_CALC_FOUND_ROWS `T1`.*, `T2`.`Item_ID`, `T1`.`Account_ID`, `T3`.`Username`, `T3`.`Last_name`,  `T3`.`First_name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "invoices` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "transactions` AS `T2` ON `T1`.`Txn_ID` = `T2`.`Txn_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T3` ON `T1`.`Account_ID` = `T3`.`ID` ";
    $sql .= "WHERE `T1`.`pStatus` <> 'trash' ";
    $sql .= "ORDER BY `T2`.`Date` DESC LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    foreach ($data as $key => $val) {
        $data[$key]['pStatus'] = $lang[$val['pStatus']];
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    $reefless->loadClass('Json');
    echo $rlJson->encode($output);
    exit();
}
$reefless->loadClass('Invoices', null, 'invoices');
if (isset($_GET['action'])) {
    $allLangs = $GLOBALS['languages'];
    $rlSmarty->assign_by_ref('allLangs', $allLangs);
    $bcAStep[] = array(
        'name' => $_GET['action'] == 'add' ? $lang['invoices_add_item'] : $lang['invoices_edit_item']
    );
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $reefless->loadClass('Account');
        $reefless->loadClass('Invoices', null, 'invoices');
        if (isset($_GET['item'])) {
            $id           = (int) $_GET['item'];
            $invoice_info = $rlDb->fetch('*', array(
                'ID' => $id
            ), null, null, 'invoices', 'row');
            $rlSmarty->assign_by_ref('invoice_info', $invoice_info);
        }
        if (isset($_POST['submit'])) {
            $errors = $error_fields = array();
            if (empty($_POST['account'])) {
                array_push($errors, str_replace('{field}', "<b>{$lang['username']}</b>", $lang['notice_field_empty']));
                array_push($error_fields, "account");
            }
            if (empty($_POST['subject'])) {
                array_push($errors, str_replace('{field}', "<b>{$lang['subject']}</b>", $lang['notice_field_empty']));
                array_push($error_fields, "subject");
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $account_info = $rlDb->getRow("SELECT `ID`, `Username`, `Mail`, `First_name`, `Last_name` FROM `" . RL_DBPREFIX . "accounts` WHERE `Username` = '{$_POST['account']}'");
                    $data         = array(
                        'Account_ID' => $account_info['ID'],
                        'Total' => $_POST['total'],
                        'Txn_ID' => $rlInvoices->generate($config['invoices_txn_tpl']),
                        'Subject' => $_POST['subject'],
                        'Description' => $_POST['description'],
                        'Date' => 'NOW()'
                    );
                    if ($action = $rlActions->insertOne($data, 'invoices', array(
                        'Description'
                    ))) {
                        $invoice_id = mysql_insert_id();
                        $reefless->loadClass('Mail');
                        $mail_tpl                  = $rlMail->getEmailTemplate('create_invoice');
                        $account_info['Full_name'] = !empty($account_info['First_name']) && !empty($account_info['Last_name']) ? $account_info['First_name'] . ' ' . $account_info['Last_name'] : $account_info['Username'];
                        $link                      = RL_URL_HOME;
                        $link .= $config['mod_rewrite'] ? $pages['invoices'] . '/' . $data['Txn_ID'] . '.html' : '?page=' . $pages['invoices'] . '&amp;item=' . $invoice_id;
                        $find                = array(
                            '{username}',
                            '{link}',
                            '{subject}',
                            '{amount}',
                            '{date}',
                            '{description}'
                        );
                        $replace             = array(
                            $account_info['Full_name'],
                            '<a href="' . $link . '">' . $link . '</a>',
                            $data['Subject'],
                            $data['Total'],
                            date(str_replace(array(
                                'b',
                                '%'
                            ), array(
                                'M',
                                ''
                            ), RL_DATE_FORMAT)),
                            $data['Description']
                        );
                        $mail_tpl['body']    = str_replace($find, $replace, $mail_tpl['body']);
                        $mail_tpl['subject'] = str_replace($find, $replace, $mail_tpl['subject']);
                        $rlMail->send($mail_tpl, $account_info['Mail']);
                        $message = $lang['invoices_item_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new banner plan (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new banner plan (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_data = array(
                        'fields' => array(
                            'Total' => $_POST['total'],
                            'Subject' => $_POST['subject'],
                            'Description' => $_POST['description']
                        ),
                        'where' => array(
                            'ID' => $id
                        )
                    );
                    $action      = $rlActions->updateOne($update_data, 'invoices', array(
                        'Description'
                    ));
                    $message     = $lang['invoices_item_edited'];
                    $aUrl        = array(
                        "controller" => $controller
                    );
                }
                if ($action) {
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                }
            }
        }
    } elseif ($_GET['action'] == 'view') {
        $bcAStep = $lang['invoice_view_details'];
        $sql     = "SELECT `T1`.*, `T2`.`Item_ID`, `T2`.`Plan_ID`, `T2`.`Service`, `T3`.`Username`, `T3`.`Last_name`,  `T3`.`First_name` ";
        $sql .= "FROM `" . RL_DBPREFIX . "invoices` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "transactions` AS `T2` ON `T1`.`Txn_ID` = `T2`.`Txn_ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T3` ON `T1`.`Account_ID` = `T3`.`ID` ";
        $sql .= "WHERE `T1`.`ID` = '{$_GET['item']}' ";
        $sql .= "LIMIT 1";
        $invoice_info = $rlDb->getRow($sql);
        $rlSmarty->assign_by_ref('invoice_info', $invoice_info);
    }
}
if (isset($_GET['module'])) {
    if (is_file(RL_PLUGINS . 'bankWireTransfer' . RL_DS . 'admin' . RL_DS . $_GET['module'] . '.inc.php')) {
        require_once(RL_PLUGINS . 'bankWireTransfer' . RL_DS . 'admin' . RL_DS . $_GET['module'] . '.inc.php');
    } else {
        $sError = true;
    }
} else {
    $rlXajax->registerFunction(array(
        'deleteItem',
        $rlInvoices,
        'ajaxDeleteItem'
    ));
}