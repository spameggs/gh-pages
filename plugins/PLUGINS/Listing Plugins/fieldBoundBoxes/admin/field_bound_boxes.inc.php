<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
        $type  = $rlValid->xSql($_GET['type']);
        $field = $rlValid->xSql($_GET['field']);
        $value = $rlValid->xSql(nl2br($_GET['value']));
        $id    = $rlValid->xSql($_GET['id']);
        $key   = $rlValid->xSql($_GET['key']);
        $box   = $rlValid->xSql($_GET['box']);
        if ($box) {
            $updateData = array(
                'fields' => array(
                    $field => $value
                ),
                'where' => array(
                    'ID' => $id
                )
            );
            $rlActions->updateOne($updateData, 'field_bound_items');
            $rlFieldBoundBoxes->updateBoxContent($box);
            exit;
        } else {
            $key = $rlDb->getOne("Key", "`ID` = '{$id}'", "field_bound_boxes");
            if ($field == 'Status') {
                $rlDb->query("UPDATE `" . RL_DBPREFIX . "field_bound_boxes` SET `Status` = '{$value}' WHERE `Key` = '{$key}'");
            }
            $updateData = array(
                'fields' => array(
                    $field => $value
                ),
                'where' => array(
                    'Key' => $key
                )
            );
            $rlActions->updateOne($updateData, 'blocks');
            $rlFieldBoundBoxes->updateBoxContent($key);
            exit;
        }
    }
    $limit   = (int) $_GET['limit'];
    $start   = (int) $_GET['start'];
    $sort    = $rlValid->xSql($_GET['sort']);
    $sortDir = $rlValid->xSql($_GET['dir']);
    $box     = $rlValid->xSql($_GET['box']);
    if ($box) {
        $parent = $rlDb->getOne('ID', "`Key` = '{$box}'", 'field_bound_boxes');
        $sql    = "SELECT SQL_CALC_FOUND_ROWS `T1`.*, `T3`.`Value` AS `name`, `T2`.`Icons` ";
        $sql .= "FROM `" . RL_DBPREFIX . "field_bound_items` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "field_bound_boxes` AS `T2` ON `T2`.`ID` = `T1`.`Box_ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T3` ON `T1`.`pName` = `T3`.`Key` AND `T3`.`Code` = '" . RL_LANG_CODE . "' ";
        $sql .= "WHERE `T1`.`Status` <> 'trash' AND `T1`.`Box_ID` = {$parent} ";
        if ($sort) {
            $sortField = $sort == 'name' ? "`T3`.`Value`" : "`T1`.`{$sort}`";
            $sql .= "ORDER BY {$sortField} {$sortDir} ";
        }
        $sql .= "LIMIT {$start},{$limit}";
    } else {
        $sql = "SELECT SQL_CALC_FOUND_ROWS `T4`.`Value` AS `Field_name`, `T2`.*, `T3`.`Value` AS `name`, `T1`.`Key` AS `Key`, `T1`.`ID` AS `ID` ";
        $sql .= "FROM `" . RL_DBPREFIX . "field_bound_boxes` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T2` ON `T1`.`Key` = `T2`.`Key` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T3` ON CONCAT('blocks+name+',`T2`.`Key`) = `T3`.`Key` AND `T3`.`Code` = '" . RL_LANG_CODE . "' ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T4` ON CONCAT('listing_fields+name+',`T1`.`Field_key`) = `T4`.`Key` AND `T4`.`Code` = '" . RL_LANG_CODE . "' ";
        $sql .= "WHERE `T1`.`Status` <> 'trash' ";
        if ($sort) {
            $sortField = $sort == 'name' ? "`T2`.`Value`" : "`T1`.`{$sort}`";
            $sql .= "ORDER BY {$sortField} {$sortDir} ";
        }
        $sql .= "LIMIT {$start},{$limit}";
    }
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang[$value['Status']];
    }
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    switch ($_GET['action']) {
        case 'add':
            $bcAStep = $lang['fb_add'];
            break;
        case 'edit':
            $bcAStep = $lang['fb_edit'] . " " . $lang['blocks+name+' . $_GET['box']];
            break;
        case 'edit_icon':
            $bcAStep[0]['name']       = $lang['blocks+name+' . $_GET['box']];
            $bcAStep[0]['Controller'] = $controller;
            $bcAStep[0]['Vars']       = 'box=' . $_GET['box'];
            $bcAStep[1]['name']       = $lang['fb_manage_icon'];
            break;
    }
    if ($_GET['box']) {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
    }
    if ($_GET['action'] == 'edit_item' && $_GET['item']) {
        $item_key = $_GET['item'];
        $box      = $_GET['box'];
        if (!$_POST['fromPost']) {
            $descriptions = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'field_bound_items+des+' . $item_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($descriptions as $nKey => $nVal) {
                $_POST['description_' . $descriptions[$nKey]['Code']] = $descriptions[$nKey]['Value'];
            }
            $meta_description = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'field_bound_items+meta_description+' . $item_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_description as $nKey => $nVal) {
                $_POST['meta_description'][$meta_description[$nKey]['Code']] = $meta_description[$nKey]['Value'];
            }
            $meta_keywords = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'field_bound_items+meta_keywords+' . $item_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_keywords as $nKey => $nVal) {
                $_POST['meta_keywords'][$meta_keywords[$nKey]['Code']] = $meta_keywords[$nKey]['Value'];
            }
        }
        foreach ($allLangs as $key => $value) {
            if (!empty($_POST['description_' . $allLangs[$key]['Code']])) {
                $c_description = $rlDb->fetch(array(
                    'ID'
                ), array(
                    'Key' => 'field_bound_items+des+' . $item_key,
                    'Code' => $allLangs[$key]['Code']
                ), null, null, 'lang_keys', 'row');
                if (!empty($c_description)) {
                    $lang_keys_name[] = array(
                        'where' => array(
                            'Code' => $allLangs[$key]['Code'],
                            'Key' => 'field_bound_items+des+' . $item_key
                        ),
                        'fields' => array(
                            'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                        )
                    );
                } else {
                    $lang_keys_des = array(
                        'Code' => $allLangs[$key]['Code'],
                        'Module' => 'common',
                        'Plugin' => 'fieldBoundBoxes',
                        'Status' => 'active',
                        'Key' => 'field_bound_items+des+' . $item_key,
                        'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                    );
                    $rlActions->insertOne($lang_keys_des, 'lang_keys');
                }
            } else {
                $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'field_bound_items+des+{$item_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
            }
            if (!empty($_POST['meta_description'][$allLangs[$key]['Code']])) {
                $meta_description = $rlDb->fetch(array(
                    'ID'
                ), array(
                    'Key' => 'field_bound_items+meta_description+' . $item_key,
                    'Code' => $allLangs[$key]['Code']
                ), null, null, 'lang_keys', 'row');
                if (!empty($meta_description)) {
                    $lang_keys_name[] = array(
                        'where' => array(
                            'Code' => $allLangs[$key]['Code'],
                            'Key' => 'field_bound_items+meta_description+' . $item_key
                        ),
                        'fields' => array(
                            'value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                        )
                    );
                } else {
                    $lang_keys_des = array(
                        'Code' => $allLangs[$key]['Code'],
                        'Module' => 'common',
                        'Plugin' => 'fieldBoundBoxes',
                        'Status' => 'active',
                        'Key' => 'field_bound_items+meta_description+' . $item_key,
                        'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                    );
                    $rlActions->insertOne($lang_keys_des, 'lang_keys');
                }
            } else {
                $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'field_bound_items+meta_description+{$item_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
            }
            if (!empty($_POST['meta_keywords'][$allLangs[$key]['Code']])) {
                $meta_keywords = $rlDb->fetch(array(
                    'ID'
                ), array(
                    'Key' => 'field_bound_items+meta_keywords+' . $item_key,
                    'Code' => $allLangs[$key]['Code']
                ), null, null, 'lang_keys', 'row');
                if (!empty($meta_keywords)) {
                    $lang_keys_name[] = array(
                        'where' => array(
                            'Code' => $allLangs[$key]['Code'],
                            'Key' => 'field_bound_items+meta_keywords+' . $item_key
                        ),
                        'fields' => array(
                            'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                        )
                    );
                } else {
                    $lang_keys_des = array(
                        'Code' => $allLangs[$key]['Code'],
                        'Module' => 'common',
                        'Plugin' => 'fieldBoundBoxes',
                        'Status' => 'active',
                        'Key' => 'field_bound_items+meta_keywords+' . $item_key,
                        'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                    );
                    $rlActions->insertOne($lang_keys_des, 'lang_keys');
                }
            } else {
                $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'field_bound_items+meta_keywords+{$item_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
            }
        }
        $GLOBALS['rlActions']->update($lang_keys_name, 'lang_keys');
        $box_info  = $rlDb->fetch("*", array(
            "Key" => $box
        ), null, null, "field_bound_boxes", "row");
        $item_info = $rlDb->fetch(array(
            'Box_ID',
            'Icon',
            'ID'
        ), array(
            'Key' => $item_key,
            'Box_ID' => $box_info['ID']
        ), null, null, 'field_bound_items', 'row');
        $rlSmarty->assign("box_info", $box_info);
        $rlSmarty->assign("item_id", $item_info['ID']);
        $_POST['icon'] = $item_info['Icon'];
        $_POST['key']  = $item_key;
        if (!empty($_FILES['icon'])) {
            $reefless->loadClass('Actions');
            $reefless->loadClass('Resize');
            $reefless->loadClass('Crop');
            $file_ext     = explode('.', $_FILES['icon']['name']);
            $file_ext     = array_reverse($file_ext);
            $file_ext     = '.' . $file_ext[0];
            $tmp_location = RL_UPLOAD . "tmp_fb_icon_" . $item_key . '_' . mt_rand() . time() . $file_ext;
            $reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
            if ($rlFieldBoundBoxes->isImage($_FILES['icon']['tmp_name'])) {
                if (move_uploaded_file($_FILES['icon']['tmp_name'], $tmp_location)) {
                    chmod($tmp_location, 0777);
                    $icon_dir       = "fieldBoundBoxes/" . $box_info['Key'] . "/";
                    $icon_name      = $item_key . "_icon_" . mt_rand() . time() . $file_ext;
                    $icon_file_name = $icon_dir . $icon_name;
                    if (RL_DS != '/') {
                        $icon_dir = str_replace('/', RL_DS, $icon_dir);
                    }
                    $reefless->rlMkdir(RL_FILES . $icon_dir);
                    $icon_file = RL_FILES . $icon_file_name;
                    if ($box_info['Icons_width'] && $box_info['Icons_height']) {
                        $rlCrop->loadImage($tmp_location);
                        $rlCrop->cropBySize($box_info['Icons_width'], $box_info['Icons_height'], ccCENTER);
                        $rlCrop->saveImage($icon_file, $GLOBALS['config']['img_quality']);
                        $rlCrop->flushImages();
                        $GLOBALS['rlResize']->resize($icon_file, $icon_file, 'C', array(
                            $box_info['Icons_width'],
                            $box_info['Icons_height']
                        ), null, false);
                    } else {
                        copy($tmp_location, $icon_file);
                    }
                }
                unlink($tmp_location);
            }
            if (is_readable($icon_file)) {
                chmod($icon_file, 0644);
                $update_data = array(
                    'fields' => array(
                        'Icon' => $icon_file_name
                    ),
                    'where' => array(
                        'ID' => $item_info['ID']
                    )
                );
                if ($GLOBALS['rlActions']->updateOne($update_data, 'field_bound_items')) {
                    $reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
                    $rlFieldBoundBoxes->updateBoxContent(null, $box_info['ID']);
                    $redirect = true;
                }
            }
        }
        if ($redirect || $_POST['fromPost']) {
            $message = $lang['item_edited'];
            $aUrl    = array(
                "controller" => $controller,
                "box" => $box_info['Key']
            );
            $reefless->loadClass('Notice');
            $rlNotice->saveNotice($message);
            $reefless->redirect($aUrl);
        }
    } elseif ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        $fields = $rlDb->fetch(array(
            'Key'
        ), array(
            'Status' => 'active',
            'Type' => 'select'
        ), "AND `Condition` != 'years' AND `Key` != 'Category_ID' AND `Key` NOT REGEXP 'level[0-9]'", null, 'listing_fields');
        $fields = $rlLang->replaceLangKeys($fields, 'listing_fields', 'name', RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('fields', $fields);
        $sections = $rlCategories->getCatTree(0, false, true);
        $rlSmarty->assign_by_ref('sections', $sections);
        $pages = $rlDb->fetch(array(
            'ID',
            'Key'
        ), array(
            'Tpl' => 1
        ), "AND `Status` = 'active' ORDER BY `Key`", null, 'pages');
        $pages = $rlLang->replaceLangKeys($pages, 'pages', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('pages', $pages);
        $rlXajax->registerFunction(array(
            'getCatLevel',
            $rlCategories,
            'ajaxGetCatLevel'
        ));
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $f_key                   = $rlValid->xSql($_GET['box']);
            $item_info               = $rlDb->fetch('*', array(
                'Key' => $f_key
            ), "AND `Status` <> 'trash'", null, 'field_bound_boxes', 'row');
            $block_info              = $rlDb->fetch(array(
                'Side',
                'Tpl',
                'Sticky',
                'Cat_sticky',
                'Subcategories',
                'Category_ID',
                'Page_ID'
            ), array(
                'Key' => $f_key
            ), "AND `Status` <> 'trash'", null, 'blocks', 'row');
            $_POST['key']            = $item_info['Key'];
            $_POST['field']          = $item_info['Field_key'];
            $_POST['listing_type']   = $item_info['Listing_type'];
            $_POST['columns']        = $item_info['Columns'];
            $_POST['page_columns']   = $item_info['Page_columns'];
            $_POST['path']           = $item_info['Path'];
            $_POST['postfix']        = $item_info['Postfix'];
            $_POST['show_count']     = $item_info['Show_count'];
            $_POST['show_empty']     = $item_info['Show_empty'];
            $_POST['icons']          = $item_info['Icons'];
            $_POST['icons_position'] = $item_info['Icons_position'];
            $_POST['icons_width']    = $item_info['Icons_width'];
            $_POST['icons_height']   = $item_info['Icons_height'];
            $_POST['status']         = $item_info['Status'];
            unset($_SESSION['categories']);
            $_POST['side']          = $block_info['Side'];
            $_POST['tpl']           = $block_info['Tpl'];
            $_POST['show_on_all']   = $block_info['Sticky'];
            $_POST['cat_sticky']    = $block_info['Cat_sticky'];
            $_POST['subcategories'] = $block_info['Subcategories'];
            $_POST['categories']    = explode(',', $block_info['Category_ID']);
            $m_pages                = explode(',', $block_info['Page_ID']);
            foreach ($m_pages as $page_id) {
                $_POST['pages'][$page_id] = $page_id;
            }
            unset($m_pages);
            $names = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'blocks+name+' . $f_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($names as $nKey => $nVal) {
                $_POST['name'][$names[$nKey]['Code']] = $names[$nKey]['Value'];
            }
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_key = $_POST['key'];
            if (!utf8_is_ascii($f_key)) {
                $f_key = utf8_to_ascii($f_key);
            }
            $f_key = $rlValid->str2key($f_key);
            if ($_GET['action'] == 'add') {
                if (strlen($f_key) < 2) {
                    $errors[]       = $lang['incorrect_phrase_key'];
                    $error_fields[] = 'key';
                }
                $exist_key = $rlDb->fetch(array(
                    'Key'
                ), array(
                    'Key' => $f_key
                ), null, null, 'field_bound_boxes');
                if (!$exist_key) {
                    $exist_key = $rlDb->fetch(array(
                        'Key'
                    ), array(
                        'Key' => $f_key
                    ), null, null, 'blocks');
                }
                if (!empty($exist_key)) {
                    $errors[]       = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_key_exist']);
                    $error_fields[] = 'key';
                }
            }
            $f_name = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$allLangs[$lkey]['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$lval['Code']}]";
                }
            }
            if ($_GET['action'] == 'add') {
                $field_key = $_POST['field'];
                if (empty($field_key)) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['fb_field'] . "</b>", $lang['notice_field_empty']);
                    $error_fields[] = "field";
                }
            }
            if (!utf8_is_ascii($_POST['path']) && $_POST['path']) {
                $_POST['path'] = utf8_to_ascii($_POST['path']);
            }
            $path       = $_POST['path'] ? $_POST['path'] : $rlValid->str2path($f_key);
            $exist_path = $rlDb->fetch(array(
                'Key'
            ), array(
                'Path' => $path
            ), null, null, 'field_bound_boxes', 'row');
            if (!empty($exist_path) && $exist_path['Key'] != $f_key && $_GET['action'] == 'add') {
                $errors[]       = str_replace('{key}', "<b>\"" . $path . "\"</b>", $lang['fb_notice_path_exist']);
                $error_fields[] = 'key';
            }
            $f_side = $_POST['side'];
            if (empty($f_side)) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['block_side'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'side';
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Key' => $f_key,
                        'Status' => $_POST['status'],
                        'Field_key' => $field_key,
                        'Listing_type' => $_POST['listing_type'],
                        'Columns' => $_POST['columns'],
                        'Page_columns' => $_POST['page_columns'],
                        'Path' => $path,
                        'Postfix' => $_POST['postfix'],
                        'Show_count' => $_POST['show_count'],
                        'Show_empty' => $_POST['show_empty'],
                        'Icons' => $_POST['icons'],
                        'Icons_position' => $_POST['icons_position'],
                        'Icons_width' => $_POST['icons_width'],
                        'Icons_height' => $_POST['icons_height']
                    );
                    if ($action = $rlActions->insertOne($data, 'field_bound_boxes')) {
                        $box_id        = mysql_insert_id();
                        $tmp_fields[0] = $rlDb->fetch('*', array(
                            'Key' => $field_key
                        ), null, null, 'listing_fields', 'row');
                        $tmp_values    = $rlCommon->fieldValuesAdaptation($tmp_fields, 'listing_fields');
                        $pos           = 1;
                        foreach ($tmp_values[0]['Values'] as $key => $value) {
                            $insert_values[$key]['Key']      = $field_key == 'posted_by' ? $value['ID'] : $value['Key'];
                            $insert_values[$key]['pName']    = $value['pName'];
                            $insert_values[$key]['Box_ID']   = $box_id;
                            $insert_values[$key]['Position'] = $pos;
                            $pos++;
                        }
                        unset($tmp_values, $tmp_fields);
                        $rlActions->insert($insert_values, 'field_bound_items');
                        $position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                        $reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
                        $rlFieldBoundBoxes->recount();
                        $block_content = $rlFieldBoundBoxes->generateBoxContent($box_id);
                        $block_data    = array(
                            'Key' => $f_key,
                            'Status' => $_POST['status'],
                            'Position' => $position['max'] + 1,
                            'Side' => $f_side,
                            'Type' => 'php',
                            'Tpl' => $_POST['tpl'],
                            'Page_ID' => implode(',', $_POST['pages']),
                            'Category_ID' => implode(',', $_POST['categories']),
                            'Subcategories' => empty($_POST['subcategories']) ? 0 : 1,
                            'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                            'Cat_sticky' => empty($_POST['cat_sticky']) ? 0 : 1,
                            'Plugin' => 'fieldBoundBoxes',
                            'Content' => $block_content
                        );
                        if ($action = $rlActions->insertOne($block_data, 'blocks')) {
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+' . $f_key,
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                            $message = $lang['notice_item_added'];
                            $aUrl    = array(
                                "controller" => $controller
                            );
                        } else {
                            trigger_error("Can't add new data format (MYSQL problems)", E_WARNING);
                            $rlDebug->logger("Can't add new data format (MYSQL problems)");
                        }
                    } else {
                        trigger_error("Can't add new data format (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new data format (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_data       = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Columns' => $_POST['columns'],
                            'Page_columns' => $_POST['page_columns'],
                            'Postfix' => $_POST['postfix'],
                            'Show_count' => $_POST['show_count'],
                            'Show_empty' => $_POST['show_empty'],
                            'Path' => $path,
                            'Listing_type' => $_POST['listing_type'],
                            'Icons' => $_POST['icons'],
                            'Icons_position' => $_POST['icons_position'],
                            'Icons_width' => $_POST['icons_width'],
                            'Icons_height' => $_POST['icons_height']
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    $update_block_data = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Side' => $f_side,
                            'Tpl' => $_POST['tpl'],
                            'Page_ID' => implode(',', $_POST['pages']),
                            'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                            'Category_ID' => $_POST['cats_sticky'] ? '' : implode(',', $_POST['categories']),
                            'Subcategories' => empty($_POST['subcategories']) ? 0 : 1,
                            'Cat_sticky' => empty($_POST['cat_sticky']) ? 0 : 1
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    $box_id            = $rlDb->getOne('ID', "`Key` = '{$f_key}'", 'field_bound_boxes');
                    $GLOBALS['rlActions']->updateOne($update_data, 'field_bound_boxes');
                    $reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
                    $block_content                          = $rlFieldBoundBoxes->generateBoxContent($box_id);
                    $update_block_data['fields']['Content'] = $block_content;
                    $action                                 = $GLOBALS['rlActions']->updateOne($update_block_data, 'blocks');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'blocks+name+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_names = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'blocks+name+' . $f_key
                                )
                            );
                            $rlActions->updateOne($update_names, 'lang_keys');
                        } else {
                            $insert_names = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'blocks+name+' . $f_key,
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_names, 'lang_keys');
                        }
                    }
                    $message = $lang['notice_item_edited'];
                    $aUrl    = array(
                        "controller" => $controller
                    );
                }
                if ($action) {
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                } else {
                    trigger_error("Can't edit datafomats (MYSQL problems)", E_WARNING);
                    $rlDebug->logger("Can't edit datafomats (MYSQL problems)");
                }
            }
        }
    }
    $reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
    $rlXajax->registerFunction(array(
        'deleteBox',
        $rlFieldBoundBoxes,
        'ajaxDeleteBox'
    ));
    $rlXajax->registerFunction(array(
        'deleteFieldBoundItem',
        $rlFieldBoundBoxes,
        'ajaxDeleteFieldBoundItem'
    ));
    $rlXajax->registerFunction(array(
        'deleteIcon',
        $rlFieldBoundBoxes,
        'ajaxDeleteIcon'
    ));
    $rlXajax->registerFunction(array(
        'rebuildItems',
        $rlFieldBoundBoxes,
        'ajaxRebuildItems'
    ));
}