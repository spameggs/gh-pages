<?php
if (!defined('IS_LOGIN')) {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (true === $res = $rlAccount->login($username, $password)) {
            $rlHook->load('loginSuccess');
            $reefless->referer();
        } else {
            if ($page_info['Prev'] == 'login') {
                if ($rlAccount->messageType == 'error') {
                    $rlSmarty->assign_by_ref('errors', $res);
                } else {
                    $rlSmarty->assign_by_ref('pAlert', $res[0]);
                }
            } else {
                $reefless->loadClass('Notice');
                $rlNotice->saveNotice($res, 'error');
                if ($page_info['prev'] == 'home') {
                    $url = SEO_BASE;
                    $url .= $config['mod_rewrite'] ? $pages['login'] . '.html' : '?page=' . $pages['login'];
                    $reefless->redirect(null, $url);
                } else {
                    $reefless->referer();
                }
            }
        }
    }
    if (isset($_GET['logout'])) {
        $rlHook->load('logOut');
        $rlSmarty->assign('pNotice', $lang['notice_logged_out']);
    }
} else {
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        $rlAccount->logOut();
    }
}