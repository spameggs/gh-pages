<?php
if ($_GET['q'] == 'ext') {
    require_once('../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
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
        $rlHook->load('apExtAdminsUpdate');
        $rlActions->updateOne($updateData, 'admins');
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $rlHook->load('apExtAdminsSql');
    $rlDb->setTable('admins');
    $data = $rlDb->fetch('*', null, "WHERE `Status` <> 'trash' ORDER BY `User`", array(
        $start,
        $limit
    ));
    $rlDb->resetTable();
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
    }
    $rlHook->load('apExtAdminsData');
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "admins` WHERE `Status` <> 'trash'");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $rlHook->load('apPhpAdminsTop');
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_admin'] : $lang['edit_admin'];
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $admin_id        = $rlValid->xSql($_GET['admin']);
            $admin_info      = $rlDb->fetch('*', array(
                'ID' => $admin_id
            ), "AND `Status` <> 'trash'", null, 'admins', 'row');
            $_POST['login']  = $admin_info['User'];
            $_POST['name']   = $admin_info['Name'];
            $_POST['email']  = $admin_info['Email'];
            $_POST['status'] = $admin_info['Status'];
            $_POST['type']   = $admin_info['Type'];
            $_POST['rights'] = unserialize($admin_info['Rights']);
            $rlHook->load('apPhpAdminsPost');
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_login       = $_POST['login'];
            $f_pass        = $_POST['password'];
            $f_pass_repeat = $_POST['password_repeat'];
            if ($_GET['action'] == 'add') {
                if (!utf8_is_ascii($f_login)) {
                    $errors[] = $lang['incorrect_username'];
                }
                if (empty($f_login)) {
                    $errors[] = str_replace('{field}', '<b>' . $lang['username'] . '</b>', $lang['notice_field_empty']);
                }
                if (empty($f_pass)) {
                    $errors[] = str_replace('{field}', '<b>' . $lang['password'] . '</b>', $lang['notice_field_empty']);
                }
                if (empty($f_pass_repeat)) {
                    $errors[] = str_replace('{field}', '<b>' . $lang['password_repeat'] . '</b>', $lang['notice_field_empty']);
                }
                $exist_login = $rlDb->fetch(array(
                    'User'
                ), array(
                    'User' => $f_login
                ), null, null, 'admins');
                if (!empty($exist_login) && !empty($f_login)) {
                    $errors[] = str_replace('{username}', "<b>\"" . $f_login . "\"</b>", $lang['notice_admin_exist']);
                }
            }
            if ($_GET['action'] == 'add') {
                if ($f_pass != $f_pass_repeat) {
                    $errors[] = $lang['notice_pass_bad'];
                }
            } elseif ($_GET['action'] == 'edit' && !empty($f_pass)) {
                if ($f_pass != $f_pass_repeat) {
                    $errors[] = $lang['notice_pass_bad'];
                }
            }
            $f_email = $_POST['email'];
            if (!empty($f_email) && !$rlValid->isEmail($f_email)) {
                $errors[] = str_replace('{field}', '<b>"' . $lang['mail'] . '"</b>', $lang['notice_field_incorrect']);
            }
            $rlHook->load('apPhpAdminsValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'User' => $f_login,
                        'Pass' => md5($f_pass),
                        'Name' => $_POST['name'],
                        'Email' => $f_email,
                        'Status' => $_POST['status'],
                        'Type' => $_POST['type'],
                        'Rights' => serialize($_POST['rights'])
                    );
                    $rlHook->load('apPhpAdminsBeforeAdd');
                    if ($action = $rlActions->insertOne($data, 'admins')) {
                        $rlHook->load('apPhpAdminsAfterAdd');
                        $message = $lang['admin_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new administrator (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new administrator (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_date = array(
                        'fields' => array(
                            'User' => $f_login,
                            'Name' => $_POST['name'],
                            'Email' => $f_email,
                            'Status' => $_POST['status'],
                            'Type' => $_POST['type'],
                            'Rights' => serialize($_POST['rights'])
                        ),
                        'where' => array(
                            'ID' => $_GET['admin']
                        )
                    );
                    if ($_SESSION['sessAdmin']['user_id'] == $_GET['admin']) {
                        $_SESSION['sessAdmin']['rights'] = $_POST['rights'];
                        $_SESSION['sessAdmin']['type']   = $_POST['type'];
                    }
                    if (!empty($f_pass)) {
                        $update_date['fields']['Pass'] = md5($f_pass);
                    }
                    $rlHook->load('apPhpAdminsBeforeEdit');
                    $action = $GLOBALS['rlActions']->updateOne($update_date, 'admins');
                    $rlHook->load('apPhpAdminsAfterEdit');
                    $message = $lang['admin_edited'];
                    $aUrl    = array(
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
    }
    $reefless->loadClass('Admin', 'admin');
    $rlXajax->registerFunction(array(
        'deleteAdmin',
        $rlAdmin,
        'ajaxDeleteAdmin'
    ));
}