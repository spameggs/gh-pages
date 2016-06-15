<?php
$key = $rlValid->xSql($_GET['key']);
$sql = "SELECT `T1`.`ID`, `T1`.`Status`, `T1`.`Username`, `T1`.`Password`, `T1`.`Password_tmp`, `T1`.`First_name`, `T1`.`Last_name`, `T1`.`Mail`, ";
$sql .= "`T2`.`Email_confirmation`, `T2`.`Admin_confirmation`, `T2`.`Auto_login` ";
$sql .= "FROM `" . RL_DBPREFIX . "accounts` AS `T1` ";
$sql .= "LEFT JOIN `" . RL_DBPREFIX . "account_types` AS `T2` ON `T1`.`Type` = `T2`.`Key` ";
$sql .= "WHERE `T1`.`Confirm_code` = '{$key}' LIMIT 1";
$rlHook->load('confirmSql', $sql);
$account = $rlDb->getRow($sql);
if (empty($account) || empty($key)) {
    $sError = true;
} else {
    $login_link = $config['mod_rewrite'] ? SEO_BASE . $pages['login'] . '.html' : RL_URL_HOME . '?page=' . $pages['login'];
    if ($account['Status'] == 'incomplete') {
        $reefless->loadClass('Account');
        $reefless->loadClass('Mail');
        $rlHook->load('confirmPreConfirm');
        $rlAccount->confirmAccount($account['ID'], $account);
        if ($account['Auto_login'] && !$account['Admin_confirmation']) {
            $rlAccount->login($account['Username'], $account['Password_tmp']);
            $reefless->loadClass('Notice');
            $rlNotice->saveNotice($lang['account_confirmed_auto_login']);
            $url = SEO_BASE;
            $url .= $config['mod_rewrite'] ? $pages['login'] . '.html' : '?page=' . $pages['login'];
            $reefless->redirect(null, $url);
        }
        if ($account['Admin_confirmation'] && $account['Status'] != 'active') {
            $message = $lang['account_confirmed_pending'];
        } else {
            $message = str_replace(array(
                '{link}'
            ), array(
                "<a href=\"{$login_link}\">{$lang['here']}</a>"
            ), $lang['account_confirmed']);
        }
    } else {
        if ($account['Admin_confirmation']) {
            $message = $lang['account_already_confirmed_pending'];
        } else {
            $message = str_replace(array(
                '{link}'
            ), array(
                "<a href=\"{$login_link}\">{$lang['here']}</a>"
            ), $lang['account_already_confirmed']);
        }
    }
    $rlSmarty->assign_by_ref('message', $message);
}