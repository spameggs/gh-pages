<?php
$reefless->loadClass('Account');
$reefless->loadClass('Actions');
$rlXajax->registerFunction(array(
    'delProfileThumbnail',
    $rlAccount,
    'ajaxDelProfileThumbnail'
));
$rlXajax->registerFunction(array(
    'delAccountFile',
    $rlAccount,
    'ajaxDelAccountFile'
));
$rlXajax->registerFunction(array(
    'changePass',
    $rlAccount,
    'ajaxChangePass'
));
if (defined('IS_LOGIN')) {
    if ($_GET['key']) {
        $confirm_key = $_GET['key'];
        $confirm     = $rlDb->getOne('ID', "`Confirm_code` = '{$confirm_key}' AND `Mail_tmp` <> '' AND `ID` = '{$account_info['ID']}'", 'accounts');
        if ($confirm) {
            $update_sql = "UPDATE `" . RL_DBPREFIX . "accounts` SET `Mail` = `Mail_tmp`, `Mail_tmp` = '', `Confirm_code` = '' WHERE `ID` = '{$account_info['ID']}' LIMIT 1";
            $rlDb->query($update_sql);
            $account_info['Mail'] = $rlDb->getOne('Mail', "`ID` = '{$account_info['ID']}'", 'accounts');
            $redirect_url         = SEO_BASE;
            $redirect_url         = $config['mod_rewrite'] ? "{$pages['my_profile']}.html" : "index.php?page={$pages['my_profile']}";
            $reefless->loadClass('Notice');
            $rlNotice->saveNotice($lang['account_edit_email_confirmed']);
            $reefless->redirect(null, $redirect_url);
        } else {
            $errors[] = $lang['account_edit_email_confirmation_fail'];
        }
    }
    $tabs = array(
        'profile' => array(
            'key' => 'profile',
            'name' => $lang['profile_information']
        ),
        'account' => array(
            'key' => 'account',
            'name' => $lang['account_information']
        ),
        'password' => array(
            'key' => 'password',
            'name' => $lang['manage_password']
        )
    );
    if ($_REQUEST['info'] == 'account') {
        $tabs['account']['active'] = true;
    } else {
        $tabs['profile']['active'] = true;
    }
    $rlSmarty->assign_by_ref('tabs', $tabs);
    $domain = $rlValid->getDomain(RL_URL_HOME, true);
    $rlSmarty->assign_by_ref('domain', $domain);
    $profile_info = $rlAccount->getProfile((int) $account_info['ID'], true);
    $rlSmarty->assign_by_ref('profile_info', $profile_info);
    if (empty($profile_info['Fields'])) {
        unset($tabs['account']);
    }
    if (!$_POST['fromPost_profile']) {
        $_POST['profile']['mail']          = $profile_info['Mail'];
        $_POST['profile']['display_email'] = $profile_info['Display_email'];
    }
    if (!$_POST['fromPost_account']) {
        foreach ($profile_info['Fields'] as $key => $value) {
            switch ($value['Type']) {
                case 'phone':
                    $_POST['account'][$value['Key']] = $value['value'];
                    break;
                case 'checkbox':
                    $_POST['account'][$value['Key']] = explode(',', $profile_info[$value['Key']]);
                    break;
                case 'text':
                case 'textarea':
                    if ($value['Multilingual'] && count($GLOBALS['languages']) > 1) {
                        $_POST['account'][$value['Key']] = $reefless->parseMultilingual($profile_info[$value['Key']]);
                    } else {
                        $_POST['account'][$value['Key']] = $profile_info[$value['Key']];
                    }
                    break;
                default:
                    $_POST['account'][$value['Key']] = $profile_info[$value['Key']];
                    break;
            }
        }
    }
    $reefless->loadClass('Categories');
    $rlHook->load('profileController');
    if ($_POST['info'] == 'profile') {
        $profile_data = $_POST['profile'];
        $e_mail_error = false;
        if (!$rlValid->isEmail($profile_data['mail'])) {
            $errors[] = $lang['notice_bad_email'];
            $error_fields .= 'profile[mail]';
            $e_mail_error = true;
        }
        $email_exist = $rlDb->getOne('Mail', "`Mail` = '{$profile_data['mail']}' AND `ID` <> '{$account_info['ID']}'", 'accounts');
        if ($email_exist) {
            $errors[] = str_replace('{email}', '<span class="field_error">"' . $profile_data['mail'] . '"</span>', $lang['notice_account_email_exist']);
            $error_fields .= 'profile[mail]';
            $e_mail_error = true;
        }
        if ($config['account_edit_email_confirmation'] && $account_info['Mail'] != $profile_data['mail'] && !$e_mail_error) {
            $rlDb->query("UPDATE `" . RL_DBPREFIX . "accounts` SET `Mail_tmp` = '{$profile_data['mail']}' WHERE `ID` = '{$account_info['ID']}' LIMIT 1");
            $rlAccount->sendEditEmailNotification($account_info['ID'], $profile_data['mail']);
            $profile_data['mail'] = $profile_info['Mail'];
        }
        if (!$account_info['Own_address']) {
            $location      = trim($profile_data['location']);
            $wildcard_deny = explode(',', $config['account_wildcard_deny']);
            $rlDb->setTable('pages');
            $deny_pages_tmp = $rlDb->fetch(array(
                'Path'
            ), null, "WHERE `Path` <> ''");
            foreach ($deny_pages_tmp as $deny_page) {
                $wildcard_deny[] = $deny_page['Path'];
            }
            unset($deny_pages_tmp);
            preg_match('/[\W]+/', $location, $matches);
            if (empty($location) || !empty($matches)) {
                $errors[] = $lang['personal_address_error'];
                $error_fields .= 'profile[location]';
            } else if (in_array($location, $wildcard_deny) || $rlDb->getOne('ID', "`Own_address` = '{$location}'", 'accounts')) {
                $errors[] = $lang['personal_address_in_use'];
                $error_fields .= 'profile[location]';
            }
        }
        $rlHook->load('profileEditProfileValidate');
        if (empty($errors)) {
            if ($rlAccount->editProfile($profile_data)) {
                $rlHook->load('profileEditProfileDone');
                $reefless->loadClass('Notice');
                $rlNotice->saveNotice($lang['notice_profile_edited']);
                $reefless->refresh();
            }
        }
    }
    if ($_POST['info'] == 'account') {
        $account_data = $_POST['account'];
        if ($account_data) {
            if ($back_errors = $rlCommon->checkDynamicForm($account_data, $profile_info['Fields'], 'account')) {
                foreach ($back_errors as $error) {
                    $errors[] = $error;
                    $rlSmarty->assign('fixed_message', true);
                }
                if ($rlCommon->error_fields) {
                    $error_fields           = $rlCommon->error_fields;
                    $rlCommon->error_fields = false;
                }
            } else {
                if ($rlAccount->editAccount($account_data, $profile_info['Fields'])) {
                    $rlHook->load('profileEditAccountValidate');
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($lang['notice_account_edited']);
                    $aUrl = array(
                        'info' => 'account'
                    );
                    $reefless->redirect($aUrl);
                }
            }
        }
    }
}