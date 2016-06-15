<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $reefless->loadClass('Account');
        $field = $rlValid->xSql($_GET['field']);
        $value = $rlValid->xSql(nl2br($_GET['value']));
        $id    = (int) $_GET['id'];
        $sql   = "SELECT `T1`.`Account_ID`, `T1`.`Date_to`, `T2`.`Plan_Type`, `T2`.`Period`, `T2`.`Price`, `T1`.`Status` FROM `" . RL_DBPREFIX . "banners` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "banner_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
        $sql .= "WHERE `T1`.`ID` = '{$id}'";
        $bannerInfo = $rlDb->getRow($sql);
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $pay_date   = $field == 'Pay_date' ? $value : date('Y-m-d');
        list($year, $month, $day) = explode('-', $pay_date, 3);
        $updateData['fields']['Pay_date'] = mktime(0, 0, 0, $month, rtrim($day, 'T00:00:00'), $year);
        $force_update_pay_date            = ($field == 'Status' && $value == 'active' && $bannerInfo['Date_to'] == 0);
        if (($field == 'Pay_date' || $force_update_pay_date) && $bannerInfo['Period'] != 0) {
            $date_to                           = $bannerInfo['Plan_Type'] == 'period' ? time() + ($bannerInfo['Period'] * 86400) : $bannerInfo['Date_to'] + $bannerInfo['Period'];
            $updateData['fields']['Date_to']   = $date_to;
            $updateData['fields']['Last_step'] = '';
        }
        $action = $rlActions->updateOne($updateData, 'banners');
        if ($action && $field == 'Status') {
            $accountInfo = $rlAccount->getProfile((int) $bannerInfo['Account_ID']);
            $reefless->loadClass('Mail');
            $mail_tpl         = $rlMail->getEmailTemplate($value == 'active' ? 'banners_banner_activated' : 'banners_banner_deactivated');
            $mail_tpl['body'] = str_replace('{username}', $accountInfo['Full_name'], $mail_tpl['body']);
            $rlMail->send($mail_tpl, $accountInfo['Mail']);
        }
        exit;
    }
    $limit      = (int) $_GET['limit'];
    $start      = (int) $_GET['start'];
    $sort       = $rlValid->xSql($_GET['sort']);
    $sortDir    = $rlValid->xSql($_GET['dir']);
    $sortByBox  = $rlValid->xSql($_GET['box']);
    $sortByPlan = (int) $_GET['plan'];
    $sortByID   = (int) $_GET['filter'];
    $sql        = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.`ID`, `T1`.`Account_ID`, `T1`.`Html`, `T1`.`Image` AS `thumbnail`, `T1`.`Type`, `T1`.`Date_release`, `T1`.`Status`, `T1`.`Last_show`, `T1`.`Shows`, ";
    $sql .= "`T1`.`Pay_date`, `T2`.`Value` AS `name`, `T3`.`Username`, `T4`.`Key` AS `Plan_key`, `T4`.`Price`, `T4`.`Period`, `T4`.`Plan_Type`, `T5`.`Banners` ";
    $sql .= "FROM `" . RL_DBPREFIX . "banners` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('banners+name+',`T1`.`ID`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T3` ON `T1`.`Account_ID` = `T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "banner_plans` AS `T4` ON `T1`.`Plan_ID` = `T4`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T5` ON `T1`.`Box` = `T5`.`Key` AND `T1`.`Type` = 'flash' ";
    $sql .= "WHERE `T1`.`Status` <> 'trash' ";
    if ($sortByPlan) {
        $sql .= "AND `T1`.`Plan_ID` = '{$sortByPlan}' ";
    }
    if ($sortByBox) {
        $sql .= "AND `T1`.`Box` = '{$sortByBox}' ";
    }
    if ($sortByID) {
        $sql .= "AND `T1`.`ID` = '{$sortByID}' ";
    }
    if ($sort) {
        switch ($sort) {
            case 'name':
                $sortField = "`T2`.`Value`";
                break;
            default:
                $sortField = "`T1`.`{$sort}`";
                break;
        }
        $sql .= "ORDER BY {$sortField} {$sortDir} ";
    }
    $sql .= "LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Banners', null, 'banners');
    foreach ($data as $key => $value) {
        if ($data[$key]['Type'] == 'html') {
            $data[$key]['thumbnail'] = $data[$key]['Html'];
        } else {
            $src = RL_FILES_URL . 'banners/' . $data[$key]['thumbnail'];
            if (file_exists(RL_FILES . 'banners/' . $data[$key]['thumbnail'])) {
                if ($data[$key]['Type'] == 'image') {
                    $data[$key]['thumbnail'] = '<a title="' . $data[$key]['name'] . '" href="' . $src . '"><img style="border: 2px white solid;" alt="' . $data[$key]['name'] . '" title="" src="' . $src . '" /></a>';
                } else if ($data[$key]['Type'] == 'flash') {
                    $boxInfo                 = unserialize($data[$key]['Banners']);
                    $data[$key]['thumbnail'] = '
						<object width="' . $boxInfo['width'] . '" height="' . $boxInfo['height'] . '" data="' . $src . '" type="application/x-shockwave-flash">
							<param value="' . $src . '" name="movie">
							<param value="opaque" name="wmode">
							<param value="direct_link=true" name="flashvars">
							<embed width="' . $boxInfo['width'] . '" height="' . $boxInfo['height'] . '" flashvars="direct_link=true" wmode="opaque" src="' . $src . '">
						</object>';
                }
            } else {
                $data[$key]['thumbnail'] = '';
            }
        }
        $data[$key]['Status']    = $lang[$data[$key]['Status']];
        $data[$key]['Type']      = $lang['banners_bannerType_' . $data[$key]['Type']];
        $data[$key]['Plan_name'] = $lang['banner_plans+name+' . $data[$key]['Plan_key']];
        $price                   = empty($data[$key]['Price']) ? '<span style=color:#3cb524;>' . $lang['free'] . '</span>' : $data[$key]['Price'];
        $plan_info               = "
		<table class='info'>
		<tr><td>{$lang['price']}:</td><td> <b>{$price}</b><br /></td></tr>
		<tr><td>" . ($data[$key]['Plan_Type'] == 'period' ? $lang['days'] : $lang['banners_liveTypeViews']) . ":</td><td> <b>" . ($data[$key]['Period'] ? $data[$key]['Period'] : $lang['unlimited']) . "</b></td></tr></table>";
        $data[$key]['Plan_info'] = $plan_info;
    }
    $out['total'] = $count['count'];
    $out['data']  = $data;
    $reefless->loadClass('Json');
    echo $rlJson->encode($out);
    exit;
}
$reefless->loadClass('Banners', null, 'banners');
if (isset($_GET['action'])) {
    $allLangs = $GLOBALS['languages'];
    $rlSmarty->assign_by_ref('allLangs', $allLangs);
}
if (isset($_GET['module'])) {
    if (is_file(RL_PLUGINS . 'banners' . RL_DS . 'admin' . RL_DS . $_GET['module'] . '.inc.php')) {
        require_once(RL_PLUGINS . 'banners' . RL_DS . 'admin' . RL_DS . $_GET['module'] . '.inc.php');
    } else {
        $sError = true;
    }
} else {
    $bcAStep = $lang['banners_listOfBanners'];
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $bcAStep = $_GET['action'] == 'add' ? $lang['banners_addBanner'] : $lang['banners_editBanner'];
        $plans   = $rlBanners->getBannerPlans();
        if (empty($plans)) {
            $rlSmarty->assign('alerts', $lang['banners_bannerPlansEmptyCP']);
        } else {
            $maxFileSize = str_replace('M', '', ini_get('upload_max_filesize'));
            $rlSmarty->assign_by_ref('max_file_size', $maxFileSize);
            if ($_SESSION['edit_banner_id']) {
                unset($_SESSION['banner_id'], $_SESSION['edit_banner_id']);
            }
            $_SESSION['banner_id'] = $_SESSION['banner_id'] ? $_SESSION['banner_id'] : false;
            $rlSmarty->assign_by_ref('plans', $plans);
            $rlBanners->getJQueryVersion();
            $plansInfo = $plansJson = array();
            foreach ($plans as $key => $entry) {
                $plansInfo[$entry['ID']] = array(
                    'type' => $entry['Plan_Type'],
                    'period' => (int) $entry['Period'],
                    'price' => (double) $entry['Price']
                );
                $boxes                   = explode(',', $entry['Boxes']);
                foreach ($boxes as $box) {
                    $boxInfo = $rlDb->getRow("SELECT `Side`, `Banners` FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = '{$box}' AND `Plugin` = 'banners'");
                    $boxSide = $boxInfo['Side'];
                    $boxInfo = unserialize($boxInfo['Banners']);
                    if ($boxInfo['box_type'] == 'btc') {
                        $boxSide = 'banners_boxType_betweenCategories';
                    }
                    $plans[$key]['boxes'][] = array(
                        'Key' => $box,
                        'side' => $lang[$boxSide],
                        'name' => $lang['blocks+name+' . $box],
                        'width' => $boxInfo['width'],
                        'height' => $boxInfo['height']
                    );
                }
                unset($boxes);
                $types = explode(',', $entry['Types']);
                foreach ($types as $type) {
                    $plans[$key]['types'][] = array(
                        'Key' => $type,
                        'name' => $lang['banners_bannerType_' . $type]
                    );
                }
                unset($types);
                $plansJson[$key] = $plans[$key];
                unset($plansJson[$key]['name'], $plansJson[$key]['des']);
            }
            $reefless->loadClass('Json');
            $rlSmarty->assign('plansJson', $rlJson->encode($plansJson));
            unset($plansJson);
            if ($_GET['action'] == 'edit' && !$_POST['postSubmit']) {
                $bannerId = (int) $_GET['id'];
                $sql      = "SELECT `T1`.`Account_ID`, `T1`.`Plan_ID`, `T1`.`Box`, `T1`.`Type`, `T1`.`Link`, `T1`.`Follow`, ";
                $sql .= "`T1`.`Html`, `T1`.`Status`, `T2`.`Username`, `T1`.`Image` ";
                $sql .= "FROM `" . RL_DBPREFIX . "banners` AS `T1` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T2` ON `T1`.`Account_ID` = `T2`.`ID` AND `T2`.`Status` = 'active' ";
                $sql .= "WHERE `T1`.`ID` = '{$bannerId}'";
                $savedBannerInfo       = $rlDb->getRow($sql);
                $_POST['username_tmp'] = $savedBannerInfo['Username'];
                $_POST['username']     = $savedBannerInfo['Account_ID'];
                $_POST['status']       = $savedBannerInfo['Status'];
                $_POST['plan']         = $savedBannerInfo['Plan_ID'];
                $_POST['banner_box']   = $savedBannerInfo['Box'];
                $_POST['banner_type']  = $savedBannerInfo['Type'];
                $_POST['link']         = $savedBannerInfo['Type'] == 'image' ? $savedBannerInfo['Link'] : '';
                $_POST['nofollow']     = $savedBannerInfo['Type'] == 'image' ? $savedBannerInfo['Follow'] : 0;
                if ($savedBannerInfo['Type'] == 'html') {
                    $_POST['html'] = $savedBannerInfo['Html'];
                } else {
                    $_POST['image'] = $savedBannerInfo['Image'];
                }
                if (count($allLangs) > 1) {
                    $names = $rlDb->fetch(array(
                        'Value',
                        'Code'
                    ), array(
                        'Key' => "banners+name+{$bannerId}"
                    ), "AND `Status` <> 'trash'", null, 'lang_keys');
                    foreach ($names as $lKey => $entry) {
                        $_POST['name'][$entry['Code']] = $entry['Value'];
                    }
                } else {
                    $_POST['name'] = $lang['banners+name+' . $bannerId];
                }
                $_SESSION['banner_id'] = $_SESSION['edit_banner_id'] = $bannerId;
            }
            if ($_POST['submit']) {
                $errors    = $error_fields = array();
                $postData  = $rlValid->xSql($_POST);
                $username  = (int) $postData['username'];
                $accountId = $username > 0 ? $username : (int) $rlDb->getOne('ID', "`Username` = '{$postData['username']}'", 'accounts');
                if (empty($postData['username'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['set_owner']}\"</b>", $lang['notice_field_empty']));
                    array_push($error_fields, 'username');
                } elseif (!empty($postData['username']) && $accountId == 0) {
                    array_push($errors, $lang['banners_ownerDoesNotExistsNotice']);
                    array_push($error_fields, 'username');
                }
                if (!empty($postData['link']) && !$rlValid->isUrl($postData['link'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerLink']}\"</b>", $lang['notice_field_incorrect']));
                    array_push($error_fields, 'link');
                }
                if (empty($postData['banner_type'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerType']}\"</b>", $lang['notice_select_empty']));
                    array_push($error_fields, 'banner_type');
                }
                if (empty($postData['banner_box'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerBox']}\"</b>", $lang['notice_select_empty']));
                    array_push($error_fields, 'banner_box');
                }
                if ($postData['banner_type'] == 'html' && empty($postData['html'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerType_html']}\"</b>", $lang['notice_field_empty']));
                    array_push($error_fields, 'html');
                }
                $f_name = $_POST['name'];
                if ((count($allLangs) > 1 && empty($f_name[$config['lang']])) || empty($f_name)) {
                    $langName = count($allLangs) > 1 ? "{$lang['name']}({$allLangs[$config['lang']]['name']})" : $lang['name'];
                    array_push($errors, str_replace('{field}', "<b>{$langName}</b>", $lang['notice_field_empty']));
                    array_push($error_fields, count($allLangs) > 1 ? "name[{$config['lang']}]" : "name");
                }
                if (!empty($errors)) {
                    $rlSmarty->assign_by_ref('errors', $errors);
                } else {
                    $planInfo               = array(
                        'ID' => (int) $postData['plan'],
                        'Plan_Type' => $plansInfo[$postData['plan']]['type'],
                        'Period' => (int) $plansInfo[$postData['plan']]['period'],
                        'Price' => $plansInfo[$postData['plan']]['price']
                    );
                    $postData['account_id'] = intVal($postData['username']) > 0 ? $postData['username'] : $rlDb->getOne('ID', "`Username` = '{$postData['username']}'", 'accounts');
                    $bannerId               = $_GET['action'] == 'add' ? (int) $_SESSION['banner_id'] : (int) $_GET['id'];
                    if ($_GET['action'] == 'add' && !$_SESSION['banner_id']) {
                        $rlBanners->create($planInfo, $postData);
                    } else {
                        $rlBanners->edit($bannerId, $planInfo, $postData);
                    }
                    $reefless->loadClass('Notice');
                    $message = $_GET['action'] == 'add' ? $lang['banners_bannerAdded'] : $lang['banners_bannerEdited'];
                    $rlNotice->saveNotice($message);
                    $reefless->redirect(array(
                        'controller' => $controller
                    ));
                }
            }
        }
        $rlXajax->registerFunction(array(
            'bannersRemoveFlash',
            $rlBanners,
            'ajaxRemoveFlash'
        ));
    } else {
        unset($_SESSION['banner_id'], $_SESSION['edit_banner_id']);
    }
    $rlXajax->registerFunction(array(
        'deleteBanner',
        $rlBanners,
        'ajaxDeleteBanner'
    ));
    $rlXajax->registerFunction(array(
        'massActions',
        $rlBanners,
        'ajaxBannersMassActions'
    ));
}