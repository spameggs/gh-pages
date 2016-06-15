<?php
global $rlDb, $rlSmarty, $pages, $lang, $config, $account_info;
$sql      = "SELECT * FROM `" . RL_DBPREFIX . "invoices` WHERE `Account_ID` = '{$account_info['ID']}' AND `pStatus` = 'unpaid' ORDER BY `Date` DESC LIMIT 5";
$invoices = $rlDb->getAll($sql);
$calc     = count($invoices);
$link     = SEO_BASE;
$link .= $config['mod_rewrite'] ? $pages['invoices'] . '/' . $invoices[0]['Txn_ID'] . '.html' : 'index.php?page=' . $pages['invoices'] . '&item=' . $invoices[0]['ID'];
$invoice_link = '<a href="' . $link . '">' . $lang['here'] . '</a>';
$GLOBALS['rlSmarty']->assign('unpaid_invoices', $calc);
$GLOBALS['rlSmarty']->assign('invoice_link', $invoice_link);
$GLOBALS['rlSmarty']->display(RL_ROOT . 'plugins' . RL_DS . 'invoices' . RL_DS . 'tplFooter.tpl');