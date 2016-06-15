<?php
$bread_crumbs[1] = array(
    'name' => $lang['pages+name+my_banners'],
    'path' => $pages['my_banners']
);
$bread_crumbs[2] = array(
    'name' => $lang['pages+name+banners_edit_banner'],
    'path' => $pages['banners_edit_banner']
);
$bannerId        = (int) $_GET['id'];
if ($bannerId) {
    $bannerData = $rlDb->fetch('*', array(
        'ID' => $bannerId
    ), null, 1, 'banners', 'row');
    if (!empty($bannerData) && $account_info['ID'] == $bannerData['Account_ID']) {
        $reefless->loadClass('Banners', false, 'banners');
        $rlSmarty->assign_by_ref('bannerData', $bannerData);
        $b_box              = $bannerData['Box'];
        $boxInfo            = $rlDb->getRow("SELECT `Side`, `Banners` FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = '{$b_box}' AND `Plugin` = 'banners'");
        $boxSide            = $boxInfo['Side'];
        $boxInfo            = unserialize($boxInfo['Banners']);
        $bannerData['Box']  = array(
            'side' => $lang[$boxSide],
            'name' => $lang['blocks+name+' . $b_box],
            'width' => $boxInfo['width'],
            'height' => $boxInfo['height']
        );
        $b_type             = $bannerData['Type'];
        $bannerData['Type'] = array(
            'key' => $b_type,
            'name' => $lang['banners_bannerType_' . $b_type]
        );
        if ($bannerData['Type']['key'] != 'html') {
            $reefless->loadClass('Json');
            $maxFileSize = ini_get('upload_max_filesize');
            $rlSmarty->assign('max_file_size', trim($maxFileSize, 'M'));
            if ($bannerData['Type']['key'] == 'flash') {
                $rlXajax->registerFunction(array(
                    'bannersRemoveFlash',
                    $rlBanners,
                    'ajaxRemoveFlash'
                ));
            }
            $_SESSION['edit_banner']['banner_id'] = $bannerId;
        }
        $allLangs = $GLOBALS['languages'];
        if (!$_POST['submit_form']) {
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
            $_POST['banner_type'] = $bannerData['Type']['key'];
            $_POST['link']        = $bannerData['Link'];
            if ($bannerData['Type']['key'] == 'html') {
                $_POST['html'] = $bannerData['Html'];
            }
        } else {
            $errors   = $error_fields = array();
            $postData = $rlValid->xSql($_POST);
            if (count($allLangs) > 1) {
                foreach ($allLangs as $lkey => $lval) {
                    if (empty($postData['name'][$allLangs[$lkey]['Code']])) {
                        array_push($errors, str_replace('{field}', "<b>{$lang['name']}({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']));
                        array_push($error_fields, "name[{$lval['Code']}]");
                    }
                }
            } else {
                if (empty($postData['name'])) {
                    array_push($errors, str_replace('{field}', "<b>{$lang['name']}</b>", $lang['notice_field_empty']));
                    array_push($error_fields, "name");
                }
            }
            if ($postData['banner_type'] == 'html' && empty($postData['html'])) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerType_html']}\"</b>", $lang['notice_field_empty']));
                array_push($error_fields, 'html');
            }
            if (empty($bannerData['Image'])) {
                if ($postData['banner_type'] == 'flash' && empty($_FILES['flash_file']['tmp_name'])) {
                    array_push($errors, str_replace('{field}', "<b>\"{$lang['file']}\"</b>", $lang['notice_field_empty']));
                } elseif ($postData['banner_type'] == 'flash' && $_FILES['flash_file']['type'] != 'application/x-shockwave-flash') {
                    array_push($errors, $lang['banners_errorFormatFlashFile']);
                }
            }
            if (!empty($postData['link']) && !$rlValid->isUrl($postData['link'])) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerLink']}\"</b>", $lang['notice_field_incorrect']));
                array_push($error_fields, 'link');
            }
            $error_fields = implode(',', $error_fields);
            if (empty($errors)) {
                $reefless->loadClass('Actions');
                $rlBanners->update($bannerId, $postData);
                $reefless->loadClass('Notice');
                $rlNotice->saveNotice($lang['banners_notice_banner_edited']);
                $redirect = SEO_BASE;
                $redirect .= $config['mod_rewrite'] ? $pages['my_banners'] . '.html' : '?page=' . $pages['my_banners'];
                $reefless->redirect(null, $redirect);
            }
        }
    } else {
        $sError = true;
    }
} else {
    $sError = true;
}