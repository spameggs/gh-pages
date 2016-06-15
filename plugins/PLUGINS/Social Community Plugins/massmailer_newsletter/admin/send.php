<?php
require_once('../../../includes/config.inc.php');
require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
require_once(RL_LIBS . 'system.lib.php');
$reefless->loadClass('Mail');
$reefless->loadClass('Json');
$id         = (int) $_GET['id'];
$index      = (int) $_GET['index'];
$massmailer = $rlDb->fetch('*', array(
    'ID' => $id
), " AND `Status` <> 'trash'", 1, 'massmailer', 'row');
if (!$massmailer) {
    echo false;
    exit;
}
if ($_SESSION['massmailer_sending']) {
    $emails = $_SESSION['massmailer_sending'];
} else {
    if (!empty($massmailer['Recipients_accounts'])) {
        $rlDb->setTable('accounts');
        $email_stack[] = $rlDb->fetch(array(
            'Mail',
            'First_name',
            'Last_name',
            'Date',
            'Username`, 1 AS `MN_module'
        ), array(
            'Status' => 'active',
            'Subscribe' => 1
        ), "AND FIND_IN_SET(`Type`, '{$massmailer['Recipients_accounts']}') > 0 AND `Mail` <> ''");
    }
    if ($massmailer['Recipients_newsletter']) {
        $rlDb->setTable('subscribers');
        $email_stack[] = $rlDb->fetch(array(
            'Mail',
            'Name',
            'ID',
            'Date`, 2 AS `MN_module'
        ), array(
            'Status' => 'active'
        ), "AND `Mail` <> ''");
    }
    if ($massmailer['Recipients_contact_us']) {
        $rlDb->setTable('contacts');
        $email_stack[] = $rlDb->fetch(array(
            'Email` as `Mail',
            'Name',
            'Date`, 3 AS `MN_module'
        ), array(
            'Subscribe' => 1
        ), "AND `Status` <> 'trash' AND `Email` <> ''");
    }
    foreach ($email_stack as $stack) {
        if ($stack) {
            foreach ($stack as $item) {
                if ($item['Username']) {
                    $item['Name'] = $item['First_name'] || $item['Last_name'] ? trim($item['First_name'] . ' ' . $item['Last_name']) : $item['Username'];
                }
                $emails[] = $item;
            }
        }
    }
    $_SESSION['massmailer_sending'] = $emails;
}
$send_to        = $emails[$index];
$items['count'] = count($emails);
if ($send_to) {
    $find                = array(
        '{username}',
        '{site_name}',
        '{site_url}',
        '{site_email}'
    );
    $replace             = array(
        $send_to['Name'],
        $lang['pages+title+home'],
        RL_URL_HOME,
        $config['site_main_email']
    );
    $mail_tpl['subject'] = str_replace($find, $replace, $massmailer['Subject']);
    $mail_tpl['body']    = str_replace($find, $replace, $massmailer['Body']);
    $path                = $rlDb->getOne('Path', "`Key` = 'massmailer_newsletter_unsubscribe'", 'pages');
    $unsubscribe_link    = RL_URL_HOME;
    $unsubscribe_link .= $config['mod_rewrite'] ? $path . '.html?' : 'index.php?page=' . $path . '&';
    $unsubscribe_link .= 'hash=' . $send_to['MN_module'] . md5($send_to['Mail']) . md5($send_to['Date']);
    $unsubscribe_link = '<a href="' . $unsubscribe_link . '">$1</a>';
    $mail_tpl['body'] .= '<br /><br /><small>';
    $mail_tpl['body'] .= preg_replace('/\[(.*)\]/', $unsubscribe_link, $lang['massmailer_newsletter_unsubscribe_text']);
    $mail_tpl['body'] .= '</small>';
    $rlMail->send($mail_tpl, $send_to['Mail'], $config['owner_name'], $massmailer['From']);
    $items['data'] = $send_to;
    echo $rlJson->encode($items);
    if ($index == count($emails) - 1) {
        unset($_SESSION['massmailer_sending']);
    }
} else {
    $items['send'] = 0;
    unset($_SESSION['massmailer_sending']);
    echo $rlJson->encode($items);
}