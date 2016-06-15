<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $field = $rlValid->xSql($_GET['field']);
        $value = $rlValid->xSql(nl2br($_GET['value']));
        $id    = (int) $_GET['id'];
        $key   = $rlDb->getOne('Key', "`ID` = '{$id}' ", 'listing_status');
        if ($field == 'Status') {
            if ($value != 'active') {
                $updateBl = array(
                    'fields' => array(
                        $field => $value
                    ),
                    'where' => array(
                        'Key' => 'lb_' . $key
                    )
                );
                $rlActions->updateOne($updateBl, 'blocks');
            }
            $updateData = array(
                'fields' => array(
                    $field => $value
                ),
                'where' => array(
                    'ID' => $id
                )
            );
            $rlActions->updateOne($updateData, 'listing_status');
            exit;
        } else {
            $reefless->loadClass('ListingStatus', null, 'listing_status');
            $field_replace = array(
                $field,
                $value,
                $id
            );
            $dataContent   = $rlListingStatus->checkContentBlock(false, false, false, false, false, $field_replace);
            $updateDatas   = array(
                'fields' => array(
                    'Content' => $dataContent
                ),
                'where' => array(
                    'Key' => 'lb_' . $key
                )
            );
            $rlActions->updateOne($updateDatas, 'blocks');
            $updateData = array(
                'fields' => array(
                    $field => $value
                ),
                'where' => array(
                    'ID' => $id
                )
            );
            $rlActions->updateOne($updateData, 'listing_status');
            exit;
        }
    }
    $limit = (int) $_GET['limit'];
    $start = (int) $_GET['start'];
    $sql   = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T2`.`Value` AS `name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listing_status` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('lsl_',`T1`.`Key`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "ORDER BY `T1`.`ID` DESC ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
    }
    $count           = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $output['total'] = $count['count'];
    $output['data']  = $data;
    $reefless->loadClass('Json');
    echo $rlJson->encode($output);
    unset($output);
} else {
    $reefless->loadClass('ListingStatus', null, 'listing_status');
    $rlSmarty->assign_by_ref('listing_types', $rlListingTypes->types);
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['ls_add_label'] : $lang['ls_edit_label'];
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $sections = $rlCategories->getCatTree(0, false, true);
        $rlSmarty->assign_by_ref('sections', $sections);
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        $pages = $rlDb->fetch(array(
            'ID',
            'Key'
        ), array(
            'Tpl' => 1
        ), "AND `Status` <> 'trash' ORDER BY `Key`", null, 'pages');
        $pages = $rlLang->replaceLangKeys($pages, 'pages', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('pages', $pages);
        $b_key       = $rlValid->xSql($_GET['block']);
        $status_info = $rlDb->fetch('*', array(
            'ID' => $b_key
        ), null, null, 'listing_status', 'row');
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['id']         = $status_info['ID'];
            $_POST['key']        = $status_info['Key'];
            $_POST['count']      = $status_info['Count'];
            $_POST['days']       = $status_info['Days'];
            $_POST['type']       = explode(',', $status_info['Type']);
            $_POST['used_block'] = $status_info['Used_block'];
            $_POST['delete']     = $status_info['Delete'] == 'simple' ? '' : $status_info['Delete'];
            $_POST['status']     = $status_info['Status'];
            $_POST['order']      = $status_info['Order'];
            $names               = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'lsl_' . $status_info['Key']
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($names as $nKey => $nVal) {
                $_POST['name'][$names[$nKey]['Code']]           = $names[$nKey]['Value'];
                $_POST['watermark'][$names[$nKey]['Code']]      = $status_info['watermark_' . $names[$nKey]['Code']];
                $_POST['watermarkLarge'][$names[$nKey]['Code']] = $status_info['watermarkLarge_' . $names[$nKey]['Code']];
            }
            if ($status_info['Used_block'] == '1') {
                $block_info = $rlDb->fetch(array(
                    'Side',
                    'Tpl',
                    'Status',
                    'Sticky',
                    'Cat_sticky',
                    'Subcategories',
                    'Category_ID',
                    'Page_ID'
                ), array(
                    'Key' => 'lb_' . $status_info['Key']
                ), null, null, 'blocks', 'row');
                $bnames     = $rlDb->fetch(array(
                    'Code',
                    'Value'
                ), array(
                    'Key' => 'blocks+name+lb_' . $status_info['Key']
                ), "AND `Status` <> 'trash'", null, 'lang_keys');
                foreach ($bnames as $nKey => $nVal) {
                    $_POST['block_name'][$bnames[$nKey]['Code']] = $bnames[$nKey]['Value'];
                }
                $_POST['side']          = $block_info['Side'];
                $_POST['tpl']           = $block_info['Tpl'];
                $_POST['block_status']  = $block_info['Status'];
                $_POST['show_on_all']   = $block_info['Sticky'];
                $_POST['cat_sticky']    = $block_info['Cat_sticky'];
                $_POST['subcategories'] = $block_info['Subcategories'];
                $_POST['categories']    = explode(',', $block_info['Category_ID']);
                $m_pages                = explode(',', $block_info['Page_ID']);
                foreach ($m_pages as $page_id) {
                    $_POST['pages'][$page_id] = $page_id;
                }
                unset($m_pages);
            }
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            $f_key  = $_POST['key'];
            if (!$_POST['fromPost']) {
                if (empty($f_key)) {
                    $errors[]       = str_replace('{field}', "<b>\"" . $lang['key'] . "\"</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'key';
                } elseif ($rlDb->getOne('Key', "`Key` = '{$f_key}' ", 'listing_status')) {
                    $errors[]       = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_key_exist']);
                    $error_fields[] = 'key';
                }
            }
            $f_name           = $_POST['name'];
            $f_watermark      = $_FILES['watermark']['name'];
            $f_watermarkLarge = $_FILES['watermarkLarge']['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$allLangs[$lkey]['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$lval['Code']}]";
                }
                $f_names[$allLangs[$lkey]['Code']] = $f_name[$allLangs[$lkey]['Code']];
                if (!$_POST['watermark'][$allLangs[$lkey]['Code']]) {
                    $ext = explode('.', $f_watermark[$allLangs[$lkey]['Code']]);
                    if (empty($f_watermark[$allLangs[$lkey]['Code']]) || !$rlValid->isImage($ext[1]) || !$rlListingStatus->isImage($_FILES['watermark']['tmp_name'][$allLangs[$lkey]['Code']])) {
                        $errors[]       = str_replace('{field}', "<b>" . $lang['ls_watermark'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                        $error_fields[] = "name[{$lval['Code']}]";
                    }
                }
                if (!$_POST['watermarkLarge'][$allLangs[$lkey]['Code']]) {
                    $ext = explode('.', $f_watermarkLarge[$allLangs[$lkey]['Code']]);
                    if (empty($f_watermarkLarge[$allLangs[$lkey]['Code']]) || !$rlValid->isImage($ext[1]) || !$rlListingStatus->isImage($_FILES['watermarkLarge']['tmp_name'][$allLangs[$lkey]['Code']])) {
                        $errors[]       = str_replace('{field}', "<b>" . $lang['ls_label_large'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                        $error_fields[] = "name[{$lval['Code']}]";
                    }
                }
            }
            $f_days = $_POST['days'];
            if ($_POST['delete']) {
                if (empty($f_days)) {
                    $errors[]       = str_replace('{field}', "<b>\"" . $lang['days'] . "\"</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'days';
                }
            }
            $f_count = $_POST['count'];
            if (empty($f_count)) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['listing_number'] . "\"</b>", $lang['notice_field_empty']);
                $error_fields[] = 'count';
            } elseif ($f_count > 30 && !empty($f_count)) {
                $errors[]       = $lang['ls_more_listings'];
                $error_fields[] = 'count';
            }
            $f_type = $_POST['type'];
            if (empty($f_type)) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['listing_type'] . "\"</b>", $lang['notice_field_empty']);
                $error_fields[] = 'type';
            }
            if ($_POST['used_block'] == '1') {
                $f_block_name = $_POST['block_name'];
                foreach ($allLangs as $lkey => $lval) {
                    if (empty($f_block_name[$allLangs[$lkey]['Code']])) {
                        $errors[]       = str_replace('{field}', "<b>" . $lang['ls_block_name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                        $error_fields[] = "block_name[{$lval['Code']}]";
                    }
                }
                $f_side = $_POST['side'];
                if (empty($f_side)) {
                    $errors[]       = str_replace('{field}', "<b>\"" . $lang['block_side'] . "\"</b>", $lang['notice_select_empty']);
                    $error_fields[] = 'side';
                }
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                $fb_key = 'lb_' . $f_key;
                if ($_GET['action'] == 'add') {
                    $data_status = array(
                        'Key' => $f_key,
                        'Count' => $f_count,
                        'Days' => $f_days,
                        'Order' => $_POST['order'],
                        'Type' => implode(',', $f_type),
                        'Delete' => $_POST['delete'] ? $_POST['delete'] : 'simple',
                        'Used_block' => $_POST['used_block'],
                        'Status' => $_POST['status']
                    );
                    foreach ($allLangs as $key => $value) {
                        $lang_keys[] = array(
                            'Code' => $allLangs[$key]['Code'],
                            'Module' => 'common',
                            'Status' => 'active',
                            'Key' => 'lsl_' . $f_key,
                            'Value' => $f_name[$allLangs[$key]['Code']],
                            'Plugin' => 'listing_status'
                        );
                        $lang_keys[] = array(
                            'Code' => $allLangs[$key]['Code'],
                            'Module' => 'common',
                            'Status' => 'active',
                            'Key' => 'ls_notice_' . $f_key,
                            'Value' => str_replace('[status]', $f_name[$allLangs[$key]['Code']], $lang['ls_notice_all']),
                            'Plugin' => 'listing_status'
                        );
                        if ($_FILES) {
                            foreach ($_FILES as $keys => $tmp_files) {
                                if (!empty($tmp_files['tmp_name'][$allLangs[$key]['Code']])) {
                                    $file_ext = explode('.', $tmp_files['name'][$allLangs[$key]['Code']]);
                                    $file_ext = array_reverse($file_ext);
                                    $file_ext = '.' . $file_ext[0];
                                    if ($keys == 'watermarkLarge') {
                                        $photo_name = $f_key . '_large_' . $allLangs[$key]['Code'] . $file_ext;
                                    } else {
                                        $photo_name = $f_key . '_' . $allLangs[$key]['Code'] . $file_ext;
                                    }
                                    $photo_file = RL_FILES . 'watermark' . RL_DS . $photo_name;
                                    if (move_uploaded_file($tmp_files['tmp_name'][$allLangs[$key]['Code']], $photo_file)) {
                                        if ($keys == 'watermarkLarge') {
                                            $data_status['watermarkLarge_' . $allLangs[$key]['Code']] = $photo_name;
                                        } else {
                                            $data_status['watermark_' . $allLangs[$key]['Code']] = $photo_name;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($action = $rlActions->insertOne($data_status, 'listing_status')) {
                        $rlActions->enumAdd('listings', 'Sub_status', $f_key);
                        if ($_POST['used_block'] == '1') {
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+' . $fb_key,
                                    'Value' => $f_block_name[$allLangs[$key]['Code']],
                                    'Plugin' => 'listing_status'
                                );
                            }
                            $position        = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $data            = array(
                                'Key' => $fb_key,
                                'Status' => $_POST['status'],
                                'Position' => $position['max'] + 1,
                                'Side' => $f_side,
                                'Type' => 'php',
                                'Tpl' => $_POST['tpl'],
                                'Readonly' => '1',
                                'Page_ID' => implode(',', $_POST['pages']),
                                'Category_ID' => implode(',', $_POST['categories']),
                                'Subcategories' => empty($_POST['subcategories']) ? 0 : 1,
                                'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                                'Cat_sticky' => empty($_POST['cat_sticky']) ? 0 : 1,
                                'Plugin' => 'listing_status'
                            );
                            $data['Content'] = $rlListingStatus->checkContentBlock(implode(',', $f_type), $f_days, $f_count, $f_key, $_POST['order']);
                            $rlActions->insertOne($data, 'blocks');
                        }
                        if ($action) {
                            $rlActions->insert($lang_keys, 'lang_keys');
                            $message = $lang['ls_block_add'];
                            $aUrl    = array(
                                "controller" => $controller
                            );
                        } else {
                            trigger_error("Can't add new block (MYSQL problems)", E_WARNING);
                            $rlDebug->logger("Can't add new block (MYSQL problems)");
                        }
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $data_status = array(
                        'fields' => array(
                            'Count' => $f_count,
                            'Days' => $f_days,
                            'Order' => $_POST['order'],
                            'Type' => implode(',', $f_type),
                            'Delete' => $_POST['delete'] ? $_POST['delete'] : 'simple',
                            'Used_block' => $_POST['used_block'],
                            'Status' => $_POST['status']
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'lsl_{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_names = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'lsl_' . $f_key
                                )
                            );
                            $rlActions->updateOne($update_names, 'lang_keys');
                        } else {
                            $insert_names = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'lsl_' . $f_key,
                                'Plugin' => 'listing_status',
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_names, 'lang_keys');
                        }
                        if ($_FILES) {
                            foreach ($_FILES as $keys => $tmp_files) {
                                if (!empty($tmp_files['tmp_name'][$allLangs[$key]['Code']])) {
                                    $file_ext = explode('.', $tmp_files['name'][$allLangs[$key]['Code']]);
                                    $file_ext = array_reverse($file_ext);
                                    $file_ext = '.' . $file_ext[0];
                                    if ($keys == 'watermarkLarge') {
                                        $photo_name = $f_key . '_large_' . $allLangs[$key]['Code'] . $file_ext;
                                    } else {
                                        $photo_name = $f_key . '_' . $allLangs[$key]['Code'] . $file_ext;
                                    }
                                    $photo_file = RL_FILES . 'watermark' . RL_DS . $photo_name;
                                    if (move_uploaded_file($tmp_files['tmp_name'][$allLangs[$key]['Code']], $photo_file)) {
                                        if ($keys == 'watermarkLarge') {
                                            $data_status['fields']['watermarkLarge_' . $allLangs[$key]['Code']] = $photo_name;
                                        } else {
                                            $data_status['fields']['watermark_' . $allLangs[$key]['Code']] = $photo_name;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($action = $GLOBALS['rlActions']->updateOne($data_status, 'listing_status')) {
                        $block_id = $rlDb->getOne('ID', "`Key` = '{$fb_key}'", 'blocks');
                        if ($_POST['used_block'] == '1') {
                            if ($block_id) {
                                $update_data                      = array(
                                    'fields' => array(
                                        'Status' => $_POST['block_status'],
                                        'Side' => $f_side,
                                        'Tpl' => $_POST['tpl'],
                                        'Page_ID' => implode(',', $_POST['pages']),
                                        'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                                        'Category_ID' => $_POST['cats_sticky'] ? '' : implode(',', $_POST['categories']),
                                        'Subcategories' => empty($_POST['subcategories']) ? 0 : 1,
                                        'Cat_sticky' => empty($_POST['cat_sticky']) ? 0 : 1
                                    ),
                                    'where' => array(
                                        'Key' => $fb_key
                                    )
                                );
                                $update_data['fields']['Content'] = $rlListingStatus->checkContentBlock(implode(',', $f_type), $f_days, $f_count, $f_key, $_POST['order']);
                                $GLOBALS['rlActions']->updateOne($update_data, 'blocks');
                            } else {
                                $position        = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                                $data            = array(
                                    'Key' => $fb_key,
                                    'Status' => $_POST['status'],
                                    'Position' => $position['max'] + 1,
                                    'Side' => $f_side,
                                    'Type' => 'php',
                                    'Tpl' => $_POST['tpl'],
                                    'Readonly' => '1',
                                    'Page_ID' => implode(',', $_POST['pages']),
                                    'Category_ID' => implode(',', $_POST['categories']),
                                    'Subcategories' => empty($_POST['subcategories']) ? 0 : 1,
                                    'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                                    'Cat_sticky' => empty($_POST['cat_sticky']) ? 0 : 1,
                                    'Plugin' => 'listing_status'
                                );
                                $data['Content'] = $rlListingStatus->checkContentBlock(implode(',', $f_type), $f_days, $f_count, $f_key, $_POST['order']);
                                $rlActions->insertOne($data, 'blocks');
                            }
                            foreach ($allLangs as $key => $value) {
                                if ($rlDb->getOne('ID', "`Key` = 'blocks+name+{$fb_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                                    $update_names = array(
                                        'fields' => array(
                                            'Value' => $f_block_name[$allLangs[$key]['Code']]
                                        ),
                                        'where' => array(
                                            'Code' => $allLangs[$key]['Code'],
                                            'Key' => 'blocks+name+' . $fb_key
                                        )
                                    );
                                    $rlActions->updateOne($update_names, 'lang_keys');
                                } else {
                                    $insert_names = array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Module' => 'common',
                                        'Key' => 'blocks+name+' . $fb_key,
                                        'Plugin' => 'listing_status',
                                        'Value' => $f_block_name[$allLangs[$key]['Code']]
                                    );
                                    $rlActions->insertOne($insert_names, 'lang_keys');
                                }
                            }
                        } else {
                            if ($block_id) {
                                $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = '{$fb_key}' LIMIT 1");
                                $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'blocks+name+{$fb_key}'");
                            }
                        }
                        $message = $lang['block_edited'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    }
                }
                if ($action) {
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                }
            }
        }
        $rlXajax->registerFunction(array(
            'getCatLevel',
            $rlCategories,
            'ajaxGetCatLevel'
        ));
        $rlXajax->registerFunction(array(
            'openTree',
            $rlCategories,
            'ajaxOpenTree'
        ));
    }
    $rlXajax->registerFunction(array(
        'deleteStatusBlock',
        $rlListingStatus,
        'ajaxDeleteStatusBlock'
    ));
    $rlXajax->registerFunction(array(
        'deleteWatermark',
        $rlListingStatus,
        'ajaxDeleteWatermark'
    ));
}