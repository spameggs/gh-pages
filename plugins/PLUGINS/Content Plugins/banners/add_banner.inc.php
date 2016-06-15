<?php
$reefless->loadClass('Banners', null, 'banners');
$bSteps = $rlBanners->getSteps();
$rlSmarty->assign('show_step_caption', false);
$rlSmarty->assign_by_ref('bSteps', $bSteps);
$request     = explode('/', $_GET['rlVareables']);
$requestStep = array_pop($request);
$getStep     = $requestStep ? $requestStep : $_GET['step'];
if (!isset($_GET['edit']) && !$getStep) {
    unset($_SESSION['add_banner']);
    unset($_SESSION['complete_payment']);
    unset($_SESSION['done']);
}
$reefless->loadClass('Plan');
$reefless->loadClass('Actions');
$bread_crumbs[1] = array(
    'name' => $lang['pages+name+my_banners'],
    'path' => $pages['my_banners']
);
$bread_crumbs[2] = array(
    'name' => $lang['pages+name+add_banner'],
    'path' => $pages['add_banner']
);
if (!$getStep) {
    $url = SEO_BASE;
    $url .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $bSteps['plan']['path'] . '.html' : '?page=' . $page_info['Path'] . '&step=' . $bSteps['plan']['path'];
    $reefless->redirect(null, $url);
} else {
    $curStep = $rlPlan->stepByPath($getStep, $bSteps);
    $rlSmarty->assign_by_ref('curStep', $curStep);
    if ($_SESSION['done'] && $curStep && $curStep != 'done') {
        $url = SEO_BASE;
        $url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html' : '?page=' . $page_info['Path'];
        $reefless->redirect(null, $url);
    }
    $planId   = $_POST['plan'] ? (int) $_POST['plan'] : $_SESSION['add_banner']['plan_id'];
    $bannerId = $_SESSION['add_banner']['banner_id'] ? (int) $_SESSION['add_banner']['banner_id'] : false;
    if ($bannerId) {
        $bannerData = $rlDb->fetch('*', array(
            'ID' => $bannerId
        ), null, 1, 'banners', 'row');
        $rlSmarty->assign_by_ref('bannerData', $bannerData);
    }
    $returnLink = SEO_BASE;
    $returnLink .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $bSteps['plan']['path'] . '.html' : '?page=' . $page_info['Path'] . '&step=' . $bSteps['plan']['path'];
    $rlSmarty->assign('returnLink', $returnLink);
    if (!$planId && $curStep != 'plan') {
        $reefless->redirect(null, $returnLink);
    }
    $plans    = array();
    $tmpPlans = $rlBanners->getBannerPlans();
    foreach ($tmpPlans as $key => $entry) {
        $plans[$entry['ID']] = $tmpPlans[$key];
    }
    unset($tmpPlans);
    $rlSmarty->assign_by_ref('plans', $plans);
    $planInfo = $plans[$planId];
    $rlSmarty->assign_by_ref('planInfo', $planInfo);
    if (!$planInfo['Price']) {
        unset($bSteps['checkout']);
    }
    if ($curStep) {
        $bread_crumbs[] = array(
            'name' => $bSteps[$curStep]['name']
        );
        if ($bannerId && !in_array($curStep, array(
            'plan',
            'done'
        ))) {
            $updateStep = array(
                'fields' => array(
                    'Last_step' => $curStep
                ),
                'where' => array(
                    'ID' => $bannerId
                )
            );
            $rlActions->updateOne($updateStep, 'banners');
        }
    }
    if ($_POST['banner_type'] == 'html' || ($curStep == 'checkout' && $bannerData['Type'] == 'html')) {
        unset($bSteps['media']);
    }
    $tmp_steps = $bSteps;
    foreach ($tmp_steps as $t_key => $t_step) {
        if ($t_key != $curStep) {
            next($bSteps);
        } else {
            break;
        }
    }
    unset($tmp_steps);
    $nextStep = next($bSteps);
    prev($bSteps);
    $prevStep = prev($bSteps);
    $rlSmarty->assign('next_step', $nextStep);
    $rlSmarty->assign('prev_step', $prevStep);
    $errors = $error_fields = array();
    switch ($curStep) {
        case 'plan':
            if (!$_POST['plan'] && $_SESSION['add_banner']['plan_id']) {
                $_POST['plan'] = $_SESSION['add_banner']['plan_id'];
            }
            if (empty($plans)) {
                array_push($errors, $lang['banners_bannerPlansEmpty']);
                $rlSmarty->assign('no_access', true);
            }
            if ($_POST['step'] == 'plan') {
                if (!$planId) {
                    array_push($errors, $lang['notice_listing_plan_does_not_chose']);
                }
                if (empty($errors)) {
                    $_SESSION['add_banner']['plan_id'] = $planId;
                    $url                               = SEO_BASE;
                    $url .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $bSteps['form']['path'] . '.html' : '?page=' . $page_info['Path'] . '&step=' . $bSteps['form']['path'];
                    $reefless->redirect(null, $url);
                }
            }
            break;
        case 'form':
            $boxes = explode(',', $planInfo['Boxes']);
            foreach ($boxes as $box) {
                $boxInfo             = $rlDb->getRow("SELECT `Side`, `Banners` FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = '{$box}' AND `Plugin` = 'banners'");
                $boxSide             = $boxInfo['Side'];
                $boxInfo             = unserialize($boxInfo['Banners']);
                $planInfo['boxes'][] = array(
                    'Key' => $box,
                    'side' => $lang[$boxSide],
                    'name' => $lang['blocks+name+' . $box],
                    'width' => $boxInfo['width'],
                    'height' => $boxInfo['height']
                );
            }
            unset($boxes);
            $types = explode(',', $planInfo['Types']);
            foreach ($types as $type) {
                $planInfo['types'][] = array(
                    'Key' => $type,
                    'name' => $lang['banners_bannerType_' . $type]
                );
            }
            unset($types);
            $allLangs = $GLOBALS['languages'];
            if ($bannerId && !$_POST['step']) {
                if (count($allLangs) > 1) {
                    $names = $rlDb->fetch(array(
                        'Value',
                        'Code'
                    ), array(
                        'Key' => "banners+name+{$bannerId}",
                        'Plugin' => 'banners'
                    ), null, null, 'lang_keys');
                    foreach ($names as $lKey => $entry) {
                        $_POST['name'][$entry['Code']] = $entry['Value'];
                    }
                } else {
                    $_POST['name'] = $lang["banners+name+{$bannerId}"];
                }
                $_POST['banner_box']  = $bannerData['Box'];
                $_POST['banner_type'] = $bannerData['Type'];
                $_POST['link']        = $bannerData['Link'];
                if ($bannerData['Type'] == 'html') {
                    $_POST['html'] = $bannerData['Html'];
                }
            }
            if ($_POST['step'] == 'form') {
                $postData = $rlValid->xSql($_POST);
                if (count($allLangs) > 1) {
                    if (empty($postData['name'][$config['lang']])) {
                        array_push($errors, str_replace('{field}', "<b>{$lang['name']}({$allLangs[$config['lang']]['name']})</b>", $lang['notice_field_empty']));
                        array_push($error_fields, "name[{$config['lang']}]");
                    }
                } else {
                    if (empty($postData['name'])) {
                        array_push($errors, str_replace('{field}', "<b>{$lang['name']}</b>", $lang['notice_field_empty']));
                        array_push($error_fields, "name");
                    }
                }
                if (empty($postData['banner_box'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerBox']}\"</b>", $lang['notice_select_empty']));
                    array_push($error_fields, 'banner_box');
                }
                if (empty($postData['banner_type'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerType']}\"</b>", $lang['notice_select_empty']));
                    array_push($error_fields, 'banner_type');
                }
                if ($postData['banner_type'] == 'html' && empty($postData['html'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerType_html']}\"</b>", $lang['notice_field_empty']));
                    array_push($error_fields, 'html');
                }
                if (!empty($postData['link']) && !$rlValid->isUrl($postData['link'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerLink']}\"</b>", $lang['notice_field_incorrect']));
                    array_push($error_fields, 'link');
                }
                $error_fields = implode(',', $error_fields);
                if (empty($errors)) {
                    if ($bannerId) {
                        $rlBanners->edit($bannerId, $planInfo, $postData);
                    } else {
                        $postData['account_id'] = (int) $account_info['ID'];
                        if (false !== $bannerId = $rlBanners->create($planInfo, $postData)) {
                            $_SESSION['add_banner']['banner_id'] = $bannerId;
                        }
                    }
                    $redirect = SEO_BASE;
                    $redirect .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $nextStep['path'] . '.html' : '?page=' . $page_info['Path'] . '&step=' . $nextStep['path'];
                    $reefless->redirect(null, $redirect);
                }
            }
            break;
        case 'media':
            if ($_POST['step'] == 'media') {
                if ($_POST['type'] == 'flash') {
                    if ($_FILES['flash_file']['type'] != 'application/x-shockwave-flash') {
                        array_push($errors, $lang['banners_errorFormatFlashFile']);
                    } else {
                        $rlBanners->uploadFlash($_SESSION['add_banner']['banner_id']);
                    }
                }
                if (empty($errors)) {
                    $redirect = SEO_BASE;
                    $redirect .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $nextStep['path'] . '.html' : '?page=' . $page_info['Path'] . '&step=' . $nextStep['path'];
                    $reefless->redirect(null, $redirect);
                }
            } else {
                $boxInfo         = $rlDb->getOne('Banners', "`Key` = '{$bannerData['Box']}'", 'blocks');
                $boxInfo         = unserialize($boxInfo);
                $boxInfo['type'] = $bannerData['Type'];
                $rlSmarty->assign('boxInfo', $boxInfo);
                $reefless->loadClass('Json');
                $maxFileSize = ini_get('upload_max_filesize');
                $rlSmarty->assign('max_file_size', trim($maxFileSize, 'M'));
                if ($bannerData['Type'] == 'flash') {
                    $rlXajax->registerFunction(array(
                        'bannersRemoveFlash',
                        $rlBanners,
                        'ajaxRemoveFlash'
                    ));
                }
                $rlBanners->getJQueryVersion();
            }
            break;
        case 'checkout':
            if ($_POST['step'] == 'checkout') {
                $gateway = $_POST['gateway'];
                if (!$gateway) {
                    $errors[] = $lang['notice_payment_gateway_does_not_chose'];
                } else {
                    $bannerTitle = $lang['banners+name+' . $bannerData['ID']];
                    $itemName    = $lang['banners_planType'];
                    $cancel_url  = SEO_BASE;
                    $cancel_url .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $getStep . '.html?canceled' : '?page=' . $page_info['Path'] . '&step=' . $getStep . '&canceled';
                    $success_url = SEO_BASE;
                    $success_url .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $nextStep['path'] . '.html' : '?page=' . $page_info['Path'] . '&step=' . $nextStep['path'];
                    $complete_payment_info        = array(
                        'item_name' => $itemName . ' #' . $bannerData['ID'] . ' (' . $bannerTitle . ')',
                        'category_id' => 0,
                        'plan_info' => $planInfo,
                        'item_id' => $bannerData['ID'],
                        'account_id' => $bannerData['Account_ID'],
                        'gateway' => $gateway,
                        'callback' => array(
                            'plugin' => 'banners',
                            'class' => 'rlBanners',
                            'method' => 'upgradeBanner',
                            'cancel_url' => $cancel_url,
                            'success_url' => $success_url
                        )
                    );
                    $_SESSION['complete_payment'] = $complete_payment_info;
                    $rlHook->load('addBannerCheckoutPreRedirect');
                    $redirect = SEO_BASE;
                    $redirect .= $config['mod_rewrite'] ? $pages['payment'] . '.html' : '?page=' . $pages['payment'];
                    $reefless->redirect(null, $redirect);
                }
            }
            break;
        case 'done':
            if ($_SESSION['done'])
                continue;
            $updateStatus = array(
                'fields' => array(
                    'Status' => $config['banners_auto_approval'] ? 'active' : 'pending',
                    'Pay_date' => time()
                ),
                'where' => array(
                    'ID' => (int) $bannerData['ID']
                )
            );
            $rlActions->updateOne($updateStatus, 'banners');
            $updateStep = array(
                'fields' => array(
                    'Last_step' => ''
                ),
                'where' => array(
                    'ID' => (int) $bannerData['ID']
                )
            );
            if ($rlActions->updateOne($updateStep, 'banners')) {
                $reefless->loadClass('Mail');
                $mail_tpl         = $rlMail->getEmailTemplate('banners_admin_banner_added');
                $m_find           = array(
                    '{username}',
                    '{link}',
                    '{date}',
                    '{status}'
                );
                $m_replace        = array(
                    $account_info['Username'],
                    '<a href="' . RL_URL_HOME . ADMIN . '/index.php?controller=banners&amp;filter=' . $bannerData['ID'] . '">' . $lang['banners+name+' . $bannerData['ID']] . '</a>',
                    date(str_replace(array(
                        'b',
                        '%'
                    ), array(
                        'M',
                        ''
                    ), RL_DATE_FORMAT)),
                    $lang[$config['banners_auto_approval'] ? 'active' : 'pending']
                );
                $mail_tpl['body'] = str_replace($m_find, $m_replace, $mail_tpl['body']);
                $rlMail->send($mail_tpl, $config['notifications_email']);
            }
            if ($bannerData['Type'] == 'html') {
                $reefless->deleteDirectory(RL_FILES . 'banners' . RL_DS . date('m-Y', $bannerData['Date_release']) . RL_DS . "b{$bannerData['ID']}" . RL_DS);
            }
            $_SESSION['done'] = true;
            break;
    }
}