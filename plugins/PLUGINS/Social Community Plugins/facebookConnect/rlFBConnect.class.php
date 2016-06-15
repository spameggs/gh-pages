<?php
class rlFBConnect extends reefless
{
    var $fUser;
    var $token = false;
    function createFBConnectButton()
    {
        global $rlSmarty, $account_info;
        if (defined('IS_LOGIN') && empty($account_info['facebook_ID']) || $_POST['xjxfun'])
            return false;
        if (!isset($_COOKIE['need_login_through_fb']))
            return false;
        require_once(RL_PLUGINS . 'facebookConnect/base_facebook.php');
        require_once(RL_PLUGINS . 'facebookConnect/facebook.php');
        $facebook = new Facebook(array(
            'appId' => $GLOBALS['config']['facebookConnect_appid'],
            'secret' => $GLOBALS['config']['facebookConnect_secret'],
            'cookie' => false
        ));
        if (isset($_GET['token'])) {
            $this->token = $_SESSION['fb_token'] = $_GET['token'];
            $this->redirect(false, RL_URL_HOME);
        } else if (isset($_SESSION['fb_token'])) {
            $this->token = $_SESSION['fb_token'];
        }
        if ($this->token) {
            $facebook->setAccessToken($this->token);
        }
        $fUser       = $facebook->getUser();
        $userProfile = false;
        if ($fUser) {
            try {
                $userProfile = $facebook->api('/me');
            }
            catch (FacebookApiException $e) {
                $fUser = null;
            }
        }
        if ($userProfile['id'] && $fUser) {
            $this->fUser = $userProfile;
            $rlSmarty->assign_by_ref('fbconnect_me', $userProfile);
            $this->loginToFlynax($userProfile);
        }
    }
    function loginToFlynax(&$facebookUser)
    {
        global $deny_pages, $lang, $pages, $config, $rlXajax, $rlSmarty, $rlAccount, $rlListingTypes, $errors;
        $user_exist = $this->fetch(array(
            'Username',
            'Password',
            'Status'
        ), array(
            'facebook_ID' => $facebookUser['id']
        ), "AND `Status` <> 'trash'", 1, 'accounts', 'row');
        if ($user_exist) {
            if ($user_exist['Status'] == 'active') {
                $this->login($user_exist['Username'], $user_exist['Password']);
            } else {
                $rlSmarty->assign('fb_status', $user_exist['Status']);
                return false;
            }
        } else {
            $fID               = $facebookUser['id'];
            $fUsername         = $this->facebookIdToUsername($facebookUser, true);
            $fPassword         = $this->generateHash(10, 'password', true);
            $fFirstName        = $facebookUser['first_name'];
            $fLastName         = $facebookUser['last_name'];
            $fMail             = $facebookUser['email'];
            $allowRegistration = true;
            if ($config['facebookConnect_autoRegPrevent']) {
                $arPrevent = $this->getOne('Status', "`Key` = 'autoRegPrevent'", 'plugins');
                if ($arPrevent == 'active') {
                    $this->loadClass('AutoRegPrevent', false, 'autoRegPrevent');
                    $form              = array(
                        'username' => $fUsername,
                        'mail' => $fMail
                    );
                    $allowRegistration = $GLOBALS['rlAutoRegPrevent']->check($form);
                }
            }
            if ($allowRegistration) {
                $email_exist = $this->getOne('Mail', "`Mail` = '{$fMail}'", 'accounts');
                if (!$email_exist) {
                    loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
                    if (!utf8_is_ascii($fUsername)) {
                        $fUsername = utf8_to_ascii($fUsername);
                    }
                    $ownAddress   = $rlSmarty->str2path($fUsername);
                    $confirm_code = '';
                    $status       = $config['facebookConnect_accountAdminConfirmation'] ? 'pending' : 'active';
                    if ($config['facebookConnect_accountEmailConfirmation']) {
                        $confirm_code = md5(mt_rand());
                        $status       = 'incomplete';
                    }
                    $rlSmarty->assign('fb_status', $status);
                    $sql = "INSERT INTO `" . RL_DBPREFIX . "accounts` ( `facebook_ID`, `Username`, `Own_address`, `Password`, `Type`, `Date`, `First_name`, `Last_name`, `Mail`, `Confirm_code`, `Status` ) VALUES ";
                    $sql .= "( '{$fID}', '{$fUsername}', '{$ownAddress}', MD5('{$fPassword}'), '{$config['facebookConnect_account_type']}', NOW(), '{$fFirstName}', '{$fLastName}', '{$fMail}', '{$confirm_code}', '{$status}' )";
                    $this->query($sql);
                    $account_id = mysql_insert_id();
                    $name       = $fFirstName || $fLastName ? trim($fFirstName . ' ' . $fLastName) : $fUsername;
                    $this->loadClass('Mail');
                    if ($config['facebookConnect_accountEmailConfirmation']) {
                        $activation_link = SEO_BASE;
                        $activation_link .= $config['mod_rewrite'] ? "{$pages['confirm']}.html?key=" : "index.php?page={$pages['confirm']}&amp;key=";
                        $activation_link .= $confirm_code;
                        $activation_link  = '<a href="' . $activation_link . '">' . $activation_link . '</a>';
                        $mail_tpl         = $GLOBALS['rlMail']->getEmailTemplate('account_created_incomplete');
                        $find             = array(
                            '{activation_link}',
                            '{name}'
                        );
                        $replace          = array(
                            $activation_link,
                            $name
                        );
                        $mail_tpl['body'] = str_replace($find, $replace, $mail_tpl['body']);
                    } else {
                        $mail_tpl          = $GLOBALS['rlMail']->getEmailTemplate($config['facebookConnect_accountAdminConfirmation'] ? 'account_created_pending' : 'account_created_active');
                        $account_area_link = SEO_BASE;
                        $account_area_link .= $config['mod_rewrite'] ? $pages['login'] . '.html' : '?page=' . $pages['login'];
                        $account_area_link = '<a href="' . $account_area_link . '">' . $lang['blocks+name+account_area'] . '</a>';
                        $find              = array(
                            '{username}',
                            '{password}',
                            '{name}',
                            '{account_area}'
                        );
                        $replace           = array(
                            $fUsername,
                            $fPassword,
                            $name,
                            $account_area_link
                        );
                        $mail_tpl['body']  = str_replace($find, $replace, $mail_tpl['body']);
                    }
                    $GLOBALS['rlMail']->send($mail_tpl, $fMail);
                    $mail_tpl         = $GLOBALS['rlMail']->getEmailTemplate('account_created_admin');
                    $details_link     = RL_URL_HOME . ADMIN . '/index.php?controller=accounts&amp;action=view&amp;userid=' . $account_id;
                    $details_link     = '<a href="' . $details_link . '">' . $details_link . '</a>';
                    $find             = array(
                        '{first_name}',
                        '{last_name}',
                        '{username}',
                        '{join_date}',
                        '{status}',
                        '{details_link}'
                    );
                    $replace          = array(
                        empty($fFirstName) ? 'Not specified' : $fFirstName,
                        empty($fLastName) ? 'Not specified' : $fLastName,
                        $fUsername,
                        date(str_replace(array(
                            'b',
                            '%'
                        ), array(
                            'M',
                            ''
                        ), RL_DATE_FORMAT)),
                        $lang[$status],
                        $details_link
                    );
                    $mail_tpl['body'] = str_replace($find, $replace, $mail_tpl['body']);
                    if ($config['facebookConnect_accountAdminConfirmation']) {
                        $activation_link  = RL_URL_HOME . ADMIN . '/index.php?controller=accounts&amp;action=remote_activation&amp;id=' . $account_id . '&amp;hash=' . md5($this->getOne('Date', "`ID` = '{$account_id}'", 'accounts'));
                        $activation_link  = '<a href="' . $activation_link . '">' . $activation_link . '</a>';
                        $mail_tpl['body'] = preg_replace('/(\{if activation is enabled\})(.*)(\{activation_link\})(.*)(\{\/if\})/', '$2 ' . $activation_link . ' $4', $mail_tpl['body']);
                    } else {
                        $mail_tpl['body'] = preg_replace('/\{if activation is enabled\}(.*)\{\/if\}/', '', $mail_tpl['body']);
                    }
                    $GLOBALS['rlMail']->send($mail_tpl, $config['site_main_email']);
                    if ($status == 'active') {
                        $this->login($fUsername, md5($fPassword));
                    }
                } else {
                    $rlSmarty->assign('fb_email', $fMail);
                    $rlXajax->registerFunction(array(
                        'fConnect',
                        $this,
                        'ajaxCheckPassword'
                    ));
                    return false;
                }
            } else {
                $url      = RL_URL_HOME . ($config['mod_rewrite'] ? "{$pages['contact_us']}.html" : "index.php?page={$pages['contact_us']}");
                $link     = '<a class="navigator" href="' . $url . '" title="$1">$1</a>';
                $errors[] = preg_replace('/\[(.*)\]/', $link, $lang['autoRegPrevent_detected']);
                $rlSmarty->assign('autoRegPreventDetected', true);
                return false;
            }
        }
        $account_info = $_SESSION['account'];
        if ($account_info) {
            foreach ($rlListingTypes->types as $listingType) {
                if (in_array($listingType['Key'], $account_info['Abilities'])) {
                    $fIndex = array_search($listingType['Key'], $deny_pages);
                    unset($deny_pages[$fIndex]);
                }
                $admin_only_types += $listingType['Admin_only'] ? 1 : 0;
            }
            if (!empty($account_info['Abilities']) || !empty($rlListingTypes->types) || $admin_only_types != count($rlListingTypes->types)) {
                foreach ($deny_pages as $index => $dPage) {
                    if (in_array($dPage, array(
                        'add_listing',
                        'payment_history',
                        'my_packages'
                    ))) {
                        unset($deny_pages[$index]);
                    }
                }
            }
            $this->buildAccountMenu();
            $rlSmarty->assign('isLogin', $_SESSION['username']);
            define('IS_LOGIN', true);
        }
    }
    function buildAccountMenu()
    {
        global $account_info, $deny_pages, $rlLang, $rlSmarty;
        $fields = array(
            'ID',
            'Page_type',
            'Key',
            'Path',
            'Get_vars',
            'Controller',
            'No_follow',
            'Menus',
            'Deny'
        );
        $this->setTable('pages');
        $menus       = $this->fetch($fields, array(
            'Status' => 'active'
        ), "AND FIND_IN_SET('2', `Menus`) > 0 ORDER BY `Position`");
        $menus       = $rlLang->replaceLangKeys($menus, 'pages', array(
            'name',
            'title'
        ));
        $accountMenu = array();
        foreach ($menus as $key => $entry) {
            if ((!in_array($account_info['Type_ID'], explode(',', $entry['Deny'])) || !$account_info['Type_ID']) && (!in_array($entry['Key'], $deny_pages) || !$deny_pages)) {
                array_push($accountMenu, $entry);
            }
        }
        $rlSmarty->assign_by_ref('account_menu', $accountMenu);
    }
    function login($username = false, $password = false)
    {
        if ($username === false || $password === false)
            return false;
        $this->loadClass('Account');
        $GLOBALS['rlAccount']->login($username, $password, true);
    }
    function ajaxCheckPassword($password = false)
    {
        global $_response, $lang, $rlValid;
        if (empty($password)) {
            $link  = '<a onclick="fConnect_force_prompt();" href="javascript:void(0)" class="static">' . $lang['fConnect_try_again'] . '</a>';
            $error = str_replace('{try_again}', $link, $lang['fConnect_enter_password']);
            $_response->script("printMessage('error', '{$error}');");
        } else {
            $md5_password   = md5($password);
            $password_match = $this->getOne('Username', "`Password` = '{$md5_password}' AND `Mail` = '" . $this->fUser['email'] . "'", 'accounts');
            if ($password_match) {
                $this->login($password_match, $md5_password);
                $_response->redirect();
            } else {
                $link  = '<a onclick="fConnect_force_prompt();" href="javascript:void(0)" class="static">' . $lang['fConnect_try_again'] . '</a>';
                $error = str_replace('{try_again}', $link, $lang['fConnect_wrong_password']);
                $_response->script("printMessage('error', '{$error}');");
            }
        }
        return $_response;
    }
    function facebookIdToUsername(&$facebookSession, $registration = false)
    {
        global $rlValid;
        if ($facebookSession === false)
            return false;
        $flUsername     = $rlValid->xSql(strtolower($facebookSession['name']));
        $sql            = "SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "accounts` WHERE `Username` LIKE '{$flUsername}%' LIMIT 1";
        $usernameExists = $this->getRow($sql);
        if ($usernameExists['count'] != '0') {
            $set_count  = $usernameExists['count'] + 1;
            $flUsername = $flUsername . '-' . $set_count;
        }
        return $flUsername;
    }
}