<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
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
        $rlActions->updateOne($updateData, 'massmailer');
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $sql   = "SELECT SQL_CALC_FOUND_ROWS * FROM `" . RL_DBPREFIX . "massmailer` WHERE `Status` <> 'trash' LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} elseif ($_GET['q'] == 'ext2') {
    require_once('../../../includes/config.inc.php');
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
        $rlActions->updateOne($updateData, 'subscribers');
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $sql   = "SELECT SQL_CALC_FOUND_ROWS * FROM `" . RL_DBPREFIX . "subscribers` WHERE `Status` <> 'trash' LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    if ($_GET['action']) {
        switch ($_GET['action']) {
            case 'add':
                $bcAStep = $lang['massmailer_newsletter_add_massmailer'];
                break;
            case 'edit':
                $bcAStep = $lang['massmailer_newsletter_edit_massmailer'];
                break;
            case 'send':
                $bcAStep = $lang['massmailer_newsletter_send'];
                break;
        }
    } else if ($_GET['page'] == 'newsletter') {
        $bcAStep = $lang['massmailer_newsletter_newsletter'];
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        $rlDb->setTable('account_types');
        $account_types = $rlDb->fetch(array(
            'Key'
        ), array(
            'Status' => 'active'
        ), "AND `Key` <> 'visitor'");
        $account_types = $rlLang->replaceLangKeys($account_types, 'account_types', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('account_types', $account_types);
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $massmailer                    = $rlDb->fetch('*', array(
                'ID' => $_GET['massmailer']
            ), " AND `Status` <> 'trash'", 1, 'massmailer', 'row');
            $_POST['subject']              = $massmailer['Subject'];
            $_POST['body']                 = $massmailer['Body'];
            $_POST['type']                 = explode(',', $massmailer['Recipients_accounts']);
            $_POST['massmailer_key']       = $massmailer['Key'];
            $_POST['status']               = $massmailer['Status'];
            $_POST['from_mail']            = $massmailer['From'];
            $_POST['site_accounts']        = $massmailer['Recipients_accounts'];
            $_POST['newsletters_accounts'] = $massmailer['Recipients_newsletter'];
            $_POST['contact_us']           = $massmailer['Recipients_contact_us'];
        }
        if (isset($_POST['submit'])) {
            if ($_GET['action'] == 'add') {
                $massmailer_key = $rlValid->str2key($_POST['massmailer_key']);
                if (empty($massmailer_key)) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['key'] . "</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'massmailer_key';
                }
            }
            $from_mail = $_POST['from_mail'];
            if (!$rlValid->isEmail($from_mail)) {
                $errors[]       = $lang['notice_bad_email'];
                $error_fields[] = 'from_mail';
            }
            if (empty($_POST['subject'])) {
                $errors[]       = str_replace('{field}', "<b>" . $lang['massmailer_newsletter_subject'] . "</b>", $lang['notice_field_empty']);
                $error_fields[] = 'subject';
            }
            if (empty($_POST['body'])) {
                $errors[] = str_replace('{field}', "<b>" . $lang['massmailer_newsletter_body'] . "</b>", $lang['notice_field_empty']);
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data    = array(
                        'Key' => $massmailer_key,
                        'From' => $from_mail,
                        'Status' => $_POST['status'],
                        'Date' => 'NOW()',
                        'Subject' => trim($_POST['subject']),
                        'Body' => trim($_POST['body']),
                        'Recipients_newsletter' => $_POST['newsletters_accounts'] ? 1 : 0,
                        'Recipients_accounts' => implode(',', $_POST['type']),
                        'Recipients_contact_us' => $_POST['contact_us'] ? 1 : 0
                    );
                    $action  = $rlActions->insertOne($data, 'massmailer');
                    $message = $lang['massmailer_newsletter_added'];
                    $aUrl    = array(
                        "controller" => $controller
                    );
                    if ($action) {
                        $reefless->loadClass('Notice');
                        $rlNotice->saveNotice($message);
                        $reefless->redirect($aUrl);
                    }
                } elseif ($_GET['action'] == 'edit') {
                    if (empty($_POST['subject'])) {
                        $errors[] = str_replace('{field}', "<b>" . $lang['massmailer_newsletter_subject'] . "</b>", $lang['notice_field_empty']);
                    }
                    if (empty($_POST['body'])) {
                        $errors[] = str_replace('{field}', "<b>" . $lang['massmailer_newsletter_body'] . "</b>", $lang['notice_field_empty']);
                    }
                    $from_mail = $_POST['from_mail'];
                    if (!$rlValid->isEmail($from_mail)) {
                        $errors[] = $lang['notice_bad_email'];
                    }
                    if (!empty($errors)) {
                        $rlSmarty->assign_by_ref('errors', $errors);
                    } else {
                        $update_data = array(
                            'fields' => array(
                                'From' => $from_mail,
                                'Status' => $_POST['status'],
                                'Date' => 'NOW()',
                                'Subject' => trim($_POST['subject']),
                                'Body' => trim($_POST['body']),
                                'Recipients_newsletter' => $_POST['newsletters_accounts'] ? 1 : 0,
                                'Recipients_accounts' => implode(',', $_POST['type']),
                                'Recipients_contact_us' => $_POST['contact_us'] ? 1 : 0
                            ),
                            'where' => array(
                                'Key' => $_POST['massmailer_key']
                            )
                        );
                        $action      = $rlActions->updateOne($update_data, 'massmailer');
                        $message     = $lang['massmailer_newsletter_edited'];
                        $aUrl        = array(
                            "controller" => $controller
                        );
                        if ($action) {
                            $reefless->loadClass('Notice');
                            $rlNotice->saveNotice($message);
                            $reefless->redirect($aUrl);
                        }
                    }
                }
            }
        }
    } elseif ($_GET['action'] == 'send') {
        $massmailer = $rlDb->fetch('*', array(
            'ID' => $_GET['massmailer']
        ), " AND `Status` <> 'trash'", 1, 'massmailer', 'row');
        if ($massmailer['Recipients_newsletter']) {
            $massmailer['Recipients_newsletter_count'] = $rlDb->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "subscribers` WHERE `Status` = 'active'");
        }
        if ($massmailer['Recipients_contact_us']) {
            $massmailer['Recipients_contact_us_count'] = $rlDb->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "contacts` WHERE `Status` <> 'trash' AND `Subscribe` = '1'");
        }
        if (!empty($massmailer['Recipients_accounts'])) {
            $account_types = explode(",", $massmailer['Recipients_accounts']);
            foreach ($account_types as $key => $val) {
                $account_type[]['Key']                         = $val;
                $count                                         = $rlDb->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "accounts` WHERE `Status` = 'active' AND `Type` = '{$val}' AND `Subscribe` = '1'");
                $massmailer['Recipients_accounts_count'][$val] = $count['Count'];
            }
            $account_types                     = $rlLang->replaceLangKeys($account_type, 'account_types', array(
                'name'
            ), RL_LANG_CODE, 'admin');
            $massmailer['Recipients_accounts'] = $account_types;
        }
        $rlSmarty->assign_by_ref('massmailer_form', $massmailer);
    }
    $reefless->loadClass('MassmailerNewsletter', null, 'massmailer_newsletter');
    $rlXajax->registerFunction(array(
        'deleteMassmailerNewsletter',
        $rlMassmailerNewsletter,
        'ajaxDeleteMassmailerNewsletter'
    ));
    $rlXajax->registerFunction(array(
        'deleteNewsletter',
        $rlMassmailerNewsletter,
        'ajaxDeleteNewsletter'
    ));
    $rlXajax->registerFunction(array(
        'massmailerSave',
        $rlMassmailerNewsletter,
        'ajaxMassmailerSave'
    ));
}