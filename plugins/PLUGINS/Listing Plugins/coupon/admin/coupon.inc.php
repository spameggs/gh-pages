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
        $rlActions->updateOne($updateData, 'coupon_code');
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $sql   = "SELECT *, `ID` AS `Key`, ";
    $sql .= "IF(UNIX_TIMESTAMP(`Date_to`) < UNIX_TIMESTAMP(NOW()), 0, `Date_to`) AS `Date_to`, ";
    $sql .= "IF(UNIX_TIMESTAMP(`Date_from`) > UNIX_TIMESTAMP(NOW()), 0, `Date_from`) AS `Date_from` ";
    $sql .= "FROM `" . RL_DBPREFIX . "coupon_code` WHERE `Status` <> 'trash' LIMIT {$start}, {$limit}";
    $data = $rlDb->getAll($sql);
    $rlDb->resetTable();
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "coupon_code` WHERE `Status` <> 'trash'");
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    $reefless->loadClass('Json');
    echo $rlJson->encode($output);
    unset($output);
} else {
    $reefless->loadClass('CouponCode', null, 'coupon');
    $reefless->loadClass('Plan');
    $reefless->loadClass('Account');
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_coupon'] : $lang['edit_coupon'];
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $plans = $rlPlan->getPlans();
        $rlSmarty->assign_by_ref('plans', $plans);
        $account_types = $rlAccount->getAccountTypes();
        $rlSmarty->assign_by_ref('account_types', $account_types);
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $id                            = (int) $_GET['coupon'];
            $coupon_info                   = $rlDb->fetch('*', array(
                'ID' => $id
            ), "AND `Status` <> 'trash'", 1, 'coupon_code', 'row');
            $_POST['account_or_type']      = $coupon_info['Account_or_type'];
            $_POST['account_type']         = explode(',', $coupon_info['Account_type']);
            $_POST['username']             = $coupon_info['Username'];
            $_POST['used_date']            = $coupon_info['Used_date'];
            $_POST['date_from']            = $coupon_info['Date_from'];
            $_POST['date_to']              = $coupon_info['Date_to'];
            $_POST['type']                 = $coupon_info['Type'];
            $_POST['using_limit']          = $coupon_info['Using_limit'];
            $_POST['coupon_discount']      = $coupon_info['Discount'];
            $_POST['generate_coupon_code'] = $coupon_info['Code'];
            $_POST['show_on_all']          = $coupon_info['Sticky'];
            $_POST['plan']                 = explode(',', $coupon_info['Plan_ID']);
        }
        if (isset($_POST['submit'])) {
            $errors      = array();
            $coupon_code = $_POST['generate_coupon_code'];
            if (empty($coupon_code)) {
                $errors[]       = str_replace('{field}', "<b>{$lang['coupon_code']}</b>", $lang['notice_field_empty']);
                $error_fields[] = 'generate_coupon_code';
            } elseif (strlen($coupon_code) < 2) {
                $errors[]       = $lang['coupon_code_limit'];
                $error_fields[] = 'generate_coupon_code';
            }
            $used_date = $_POST['used_date'];
            if ($used_date == 'yes') {
                $date_from = $_POST['date_from'];
                $date_to   = $_POST['date_to'];
                if (empty($date_from) || empty($date_to)) {
                    $errors[]       = str_replace('{field}', "<b>{$lang['available_coupone']}</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'date_from';
                }
            }
            if ($_POST['using_limit'] == '') {
                $errors[]       = str_replace('{field}', "<b>{$lang['using_limit']}</b>", $lang['notice_field_empty']);
                $error_fields[] = 'using_limit';
            }
            $discount = $_POST['coupon_discount'];
            if (empty($discount)) {
                $errors[]       = str_replace('{field}', "<b>{$lang['coupon_discount']}</b>", $lang['notice_field_empty']);
                $error_fields[] = 'coupon_discount';
            }
            $account_or_type = $_POST['account_or_type'];
            if ($account_or_type == 'type') {
                $account_type = $_POST['account_type'];
                if (empty($account_type)) {
                    $errors[]       = str_replace('{field}', "<b>{$lang['account_type']}</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'account_type';
                }
            } elseif ($account_or_type == 'account') {
                $username       = $_POST['username'];
                $isset_username = $rlDb->getOne('Username', "`Username` = '{$username}'", 'accounts');
                if (empty($username)) {
                    $errors[]       = str_replace('{field}', "<b>{$lang['username']}</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'username';
                } elseif (empty($isset_username)) {
                    $errors[]       = $lang['coupon_not_found'];
                    $error_fields[] = 'username';
                }
            }
            $plan_val = $_POST['plan'];
            if (empty($plan_val) && empty($_POST['show_on_all'])) {
                $errors[] = str_replace('{field}', "<b>{$lang['plan']}</b>", $lang['notice_field_empty']);
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $insert_data = array(
                        'Code' => $coupon_code,
                        'Plan_ID' => implode(',', $plan_val),
                        'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                        'Used_date' => $used_date,
                        'Date_from' => $date_from,
                        'Date_to' => $date_to,
                        'Using_limit' => $_POST['using_limit'],
                        'Discount' => $discount,
                        'Type' => $_POST['type'],
                        'Account_or_type' => $_POST['account_or_type'],
                        'Account_type' => !empty($account_type) ? implode(',', $account_type) : '',
                        'Username' => $username ? $username : '',
                        'Status' => $_POST['status'],
                        'Date_release' => 'NOW()'
                    );
                    if ($action = $rlActions->insertOne($insert_data, 'coupon_code')) {
                        $message = $lang['item_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new poll (MYSQL problems)", E_WARNING);
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $id          = (int) $_GET['coupon'];
                    $update_data = array(
                        'fields' => array(
                            'Code' => $coupon_code,
                            'Plan_ID' => implode(',', $plan_val),
                            'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                            'Used_date' => $used_date,
                            'Date_from' => $date_from,
                            'Date_to' => $date_to,
                            'Using_limit' => $_POST['using_limit'],
                            'Discount' => $discount,
                            'Type' => $_POST['type'],
                            'Account_or_type' => $_POST['account_or_type'],
                            'Account_type' => !empty($account_type) ? implode(',', $account_type) : '',
                            'Username' => $username ? $username : '',
                            'Status' => $_POST['status'],
                            'Date_release' => 'NOW()'
                        ),
                        'where' => array(
                            'ID' => $id
                        )
                    );
                    $action      = $GLOBALS['rlActions']->updateOne($update_data, 'coupon_code');
                    $message     = $lang['item_edited'];
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
    }
    $rlXajax->registerFunction(array(
        'deleteCoupon',
        $rlCouponCode,
        'ajaxDeleteCoupon'
    ));
}