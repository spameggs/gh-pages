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
                'Key' => 'categoryFilter_' . $id
            )
        );
        $rlActions->updateOne($updateData, 'blocks');
        exit;
    }
    $limit = (int) $_GET['limit'];
    $start = (int) $_GET['start'];
    $sql   = "SELECT SQL_CALC_FOUND_ROWS `T1`.`ID`, `T1`.`Category_IDs`, `T1`.`Mode`, `T1`.`Type`, `T2`.`Status`, `T2`.`Tpl`, `T2`.`Side` ";
    $sql .= "FROM `" . RL_DBPREFIX . "category_filter` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T2` ON CONCAT('categoryFilter_', `T1`.`ID`) = `T2`.`Key` ";
    $sql .= "ORDER BY `T1`.`ID` ASC ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
        $data[$key]['Tpl']    = $data[$key]['Tpl'] ? $lang['yes'] : $lang['no'];
        $data[$key]['Side']   = $lang[$data[$key]['Side']];
        $data[$key]['Name']   = $lang['blocks+name+categoryFilter_' . $value['ID']];
        $data[$key]['Mode']   = $value['Mode'] == 'type' ? $lang['listing_type'] : $lang['categoryFilter_filter_mode_category'];
        if ($value['Mode'] == 'category') {
            if ($value['Category_IDs']) {
                $sql = "SELECT SQL_CALC_FOUND_ROWS `Key` FROM `" . RL_DBPREFIX . "categories` ";
                $sql .= "WHERE FIND_IN_SET(`ID`, '{$value['Category_IDs']}') > 0 LIMIT 5";
                $category_keys    = $rlDb->getAll($sql);
                $categories_count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
                if ($category_keys) {
                    foreach ($category_keys as $category_key) {
                        $data[$key]['Categories'] .= $lang['categories+name+' . $category_key['Key']] . ', ';
                    }
                    $data[$key]['Categories'] = rtrim($data[$key]['Categories'], ', ');
                    if ($categories_count['count'] > 5) {
                        $data[$key]['Categories'] = $data[$key]['Categories'] . '...';
                    }
                }
            } else {
                $data[$key]['Categories'] = $lang['not_available'];
            }
        } elseif ($value['Mode'] == 'type') {
            $data[$key]['Categories'] = $rlListingTypes->types[$value['Type']]['name'];
        }
    }
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $reefless->loadClass('CategoryFilter', null, 'categoryFilter');
    if ($_GET['action']) {
        switch ($_GET['action']) {
            case 'add':
                $bcAStep = $lang['categoryFilter_add_filter_box'];
                break;
            case 'edit':
                $bcAStep = $lang['categoryFilter_edit_filter_box'];
                break;
            case 'build':
                $bcAStep = $lang['categoryFilter_build_filter_box'];
                break;
            case 'config':
                $bcAStep[] = array(
                    'name' => $lang['categoryFilter_build_filter_box'],
                    'Controller' => 'categoryFilter&action=build&item=' . $_GET['item'] . '&form',
                    'Plugin' => 'categoryFilter'
                );
                $bcAStep[] = array(
                    'name' => $lang['categoryFilter_configure_filter']
                );
                break;
        }
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $sections = $rlCategories->getCatTree(0, false, true);
        $rlSmarty->assign_by_ref('sections', $sections);
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        $item_id = (int) $_GET['item'];
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $sql = "SELECT `T1`.`ID`, `T1`.`Category_IDs`, `T1`.`Mode`, `T1`.`Type`, `T2`.`Status`, `T2`.`Tpl`, `T2`.`Side` ";
            $sql .= "FROM `" . RL_DBPREFIX . "category_filter` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T2` ON CONCAT('categoryFilter_', `T1`.`ID`) = `T2`.`Key` ";
            $sql .= "WHERE `T1`.`ID` = '{$item_id}' ";
            $sql .= "LIMIT 1";
            $item_info           = $rlDb->getRow($sql);
            $_POST['status']     = $item_info['Status'];
            $_POST['mode']       = $item_info['Mode'];
            $_POST['type']       = $item_info['Type'];
            $_POST['categories'] = explode(',', $item_info['Category_IDs']);
            $_POST['side']       = $item_info['Side'];
            $_POST['tpl']        = $item_info['Tpl'];
            $names               = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'blocks+name+categoryFilter_' . $item_info['ID']
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($names as $name_phrase) {
                $_POST['name'][$name_phrase['Code']] = $name_phrase['Value'];
            }
        }
        if ($_POST['categories']) {
            $rlCategories->parentPoints($_POST['categories']);
        }
        if (isset($_POST['submit'])) {
            $box_mode = $_POST['mode'];
            if (!$box_mode) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['categoryFilter_filter_for'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'mode';
            }
            if ($box_mode == 'category') {
                $categories = $_POST['categories'];
                if (!$categories) {
                    $errors[] = $lang['categoryFilter_no_category_selected'];
                } else {
                    $add_where = $item_id ? "AND `T1`.`ID` <> '{$item_id}'" : '';
                    foreach ($categories as $category) {
                        $sql = "SELECT `T2`.`Key` FROM `" . RL_DBPREFIX . "category_filter` AS `T1` ";
                        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T2`.`ID` = {$category} ";
                        $sql .= "WHERE FIND_IN_SET('{$category}', `Category_IDs`) > 0 " . $add_where;
                        $exists = $rlDb->getRow($sql);
                        if ($exists) {
                            $categories_names_exists[] = $lang['categories+name+' . $exists['Key']];
                        }
                    }
                    if ($categories_names_exists) {
                        $errors[] = str_replace('{categories}', '<b>' . implode(', ', $categories_names_exists) . '</b>', $lang['categoryFilter_filter_exists_for']);
                    }
                }
            } elseif ($box_mode == 'type') {
                $box_listing_type = $_POST['type'];
                if (!$box_listing_type) {
                    $errors[]       = str_replace('{field}', "<b>\"" . $lang['listing_type'] . "\"</b>", $lang['notice_select_empty']);
                    $error_fields[] = 'type';
                } else {
                    $sql = "SELECT `ID` FROM `" . RL_DBPREFIX . "category_filter` ";
                    $sql .= "WHERE `Mode` = 'type' AND `Type` = '{$box_listing_type}'";
                    if ($item_id) {
                        $sql .= "AND `ID` <> '{$item_id}'";
                    }
                    $exists = $rlDb->getRow($sql);
                    if ($exists) {
                        $errors[] = str_replace('{type}', '<b>' . implode(', ', $rlListingTypes->types[$box_listing_type]['name']) . '</b>', $lang['categoryFilter_filter_exists_for_type']);
                    }
                }
            }
            $name_post = $_POST['name'];
            foreach ($allLangs as $lang_item) {
                if (empty($name_post[$lang_item['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$lang_item['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$lang_item['Code']}]";
                }
                $names[$lang_item['Code']] = $name_post[$lang_item['Code']];
            }
            $box_side = $_POST['side'];
            if (empty($box_side)) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['block_side'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'side';
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($box_mode == 'category') {
                    $sql = "SELECT `T3`.`ID` FROM `" . RL_DBPREFIX . "categories` AS `T1` ";
                    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_types` AS `T2` ON `T1`.`Type` = `T2`.`Key` ";
                    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "pages` AS `T3` ON CONCAT('lt_', `T2`.`Key`) = `T3`.`Key` ";
                    $sql .= "WHERE FIND_IN_SET(`T1`.`ID`, '" . implode(',', $categories) . "') > 0 ";
                    $sql .= "GROUP BY `T1`.`Type` ";
                    $type_keys = $rlDb->getAll($sql);
                    foreach ($type_keys as $lt_key) {
                        $page_ids[] = $lt_key['ID'];
                    }
                } else {
                    $page_ids[] = $rlDb->getOne('ID', "`Key` = 'lt_{$box_listing_type}'", 'pages');
                }
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Mode' => $box_mode,
                        'Type' => $box_mode == 'type' ? $box_listing_type : '',
                        'Category_IDs' => $box_mode == 'category' ? implode(',', $categories) : ''
                    );
                    if ($action = $rlActions->insertOne($data, 'category_filter')) {
                        $filter_id  = mysql_insert_id();
                        $filter_key = 'categoryFilter_' . $filter_id;
                        foreach ($allLangs as $lang_item) {
                            $lang_keys[] = array(
                                'Code' => $lang_item['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'blocks+name+' . $filter_key,
                                'Value' => $names[$lang_item['Code']],
                                'plugin' => 'categoryFilter'
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $box = array(
                            'Key' => $filter_key,
                            'Status' => $_POST['status'],
                            'Position' => 1,
                            'Side' => $box_side,
                            'Type' => 'php',
                            'Tpl' => $_POST['tpl'],
                            'Page_ID' => implode(',', $page_ids),
                            'Category_ID' => $box_mode == 'category' ? implode(',', $categories) : '',
                            'Sticky' => 0,
                            'Cat_sticky' => $box_mode == 'category' ? 0 : 1,
                            'Plugin' => 'categoryFilter',
                            'Content' => 'echo "' . $lang['categoryFilter_no_fields_added'] . '";'
                        );
                        $rlActions->insertOne($box, 'blocks');
                        $message = $lang['categoryFilter_filter_box_added'];
                        $aUrl    = array(
                            "controller" => $controller . '#build'
                        );
                    } else {
                        $rlDebug->logger("Can't add new lisitng field's group (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_date = array(
                        'fields' => array(
                            'Mode' => $box_mode,
                            'Type' => $box_mode == 'type' ? $box_listing_type : '',
                            'Category_IDs' => $box_mode == 'category' ? implode(',', $categories) : ''
                        ),
                        'where' => array(
                            'ID' => $item_id
                        )
                    );
                    $action      = $rlActions->updateOne($update_date, 'category_filter');
                    foreach ($allLangs as $lang_item) {
                        if ($rlDb->getOne('ID', "`Key` = 'blocks+name+categoryFilter_{$item_id}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys')) {
                            $update_names = array(
                                'fields' => array(
                                    'Value' => $name_post[$lang_item['Code']]
                                ),
                                'where' => array(
                                    'Code' => $lang_item['Code'],
                                    'Key' => 'blocks+name+categoryFilter_' . $item_id
                                )
                            );
                            $rlActions->updateOne($update_names, 'lang_keys');
                        } else {
                            $insert_names = array(
                                'Code' => $lang_item['Code'],
                                'Module' => 'common',
                                'Key' => 'listing_groups+name+' . $f_key,
                                'Key' => 'blocks+name+categoryFilter_' . $item_id,
                                'plugin' => 'categoryFilter'
                            );
                            $rlActions->insertOne($insert_names, 'lang_keys');
                        }
                    }
                    $update_box = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Side' => $box_side,
                            'Tpl' => $_POST['tpl'],
                            'Page_ID' => implode(',', $page_ids),
                            'Category_ID' => implode(',', $categories)
                        ),
                        'where' => array(
                            'Key' => 'categoryFilter_' . $item_id
                        )
                    );
                    $rlCategoryFilter->compile($item_id);
                    $rlActions->updateOne($update_box, 'blocks');
                    $message = $lang['categoryFilter_filter_box_edited'];
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
    } else if ($_GET['action'] == 'build') {
        $item_id = (int) $_GET['item'];
        $sql     = "SELECT `T1`.`ID`, CONCAT('categoryFilter_', `T1`.`ID`) AS `Key`, `T1`.`Category_IDs`, `T1`.`Mode`, `T1`.`Type`, `T2`.`Status`, `T2`.`Tpl`, `T2`.`Side` ";
        $sql .= "FROM `" . RL_DBPREFIX . "category_filter` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T2` ON CONCAT('categoryFilter_', `T1`.`ID`) = `T2`.`Key` ";
        $sql .= "WHERE `T1`.`ID` = '{$item_id}' ";
        $sql .= "LIMIT 1";
        $box_info = $rlDb->getRow($sql);
        $rlSmarty->assign_by_ref('category_info', $box_info);
        if ($box_info['Mode'] == 'category') {
            $sql = "SELECT SQL_CALC_FOUND_ROWS `Key` FROM `" . RL_DBPREFIX . "categories` ";
            $sql .= "WHERE FIND_IN_SET(`ID`, '{$box_info['Category_IDs']}') > 0 LIMIT 5";
            $category_keys    = $rlDb->getAll($sql);
            $categories_count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
            if ($category_keys) {
                foreach ($category_keys as $category_key) {
                    $caption_categories .= $lang['categories+name+' . $category_key['Key']] . ', ';
                }
                $caption_categories = rtrim($caption_categories, ', ');
                if ($categories_count['count'] > 5) {
                    $caption_categories = $data[$key]['Categories'] . '...';
                }
            }
            $rlSmarty->assign('cpTitle', $lang['blocks+name+categoryFilter_' . $item_id] . " ({$caption_categories})");
        } else {
            $rlSmarty->assign('cpTitle', $lang['blocks+name+categoryFilter_' . $item_id] . " ({$rlListingTypes -> types[$box_info['Type']]['name']})");
        }
        $deny_fields_keys  = array(
            'Category_ID',
            'keyword_search'
        );
        $deny_fields_types = array(
            'textarea',
            'phone',
            'date',
            'image',
            'file',
            'accept'
        );
        foreach ($l_types as $l_type_key => $l_type_name) {
            if (in_array($l_type_key, $deny_fields_types)) {
                unset($l_types[$l_type_key]);
            }
        }
        $fields = $rlDb->fetch(array(
            'ID',
            'Key',
            'Type',
            'Status'
        ), null, "WHERE `Status` <> 'trash' AND `Details_page` = '1' AND `Multilingual` = '0' AND `Key` <> '" . implode("' AND `Key` <> '", $deny_fields_keys) . "' AND `Type` <> '" . implode("' AND `Type` <> '", $deny_fields_types) . "'", null, 'listing_fields');
        $fields = $rlLang->replaceLangKeys($fields, 'listing_fields', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('fields', $fields);
        $reefless->loadClass('Builder', 'admin');
        $rlBuilder->rlBuildTable = 'category_filter_relation';
        $rlBuilder->rlBuildField = 'Fields';
        $relations               = $rlBuilder->getRelations($box_info['ID']);
        $rlSmarty->assign_by_ref('relations', $relations);
        foreach ($relations as $rKey => $rValue) {
            $no_groups[] = $relations[$rKey]['Key'];
            $f_fields    = $relations[$rKey]['Fields'];
            if ($relations[$rKey]['Group_ID']) {
                foreach ($f_fields as $fKey => $fValue) {
                    $no_fields[] = $f_fields[$fKey]['Key'];
                }
            } else {
                $no_fields[] = $relations[$rKey]['Fields']['Key'];
            }
        }
        if (!empty($no_fields)) {
            foreach ($fields as $fKey => $fVal) {
                if (false !== array_search($fields[$fKey]['Key'], $no_fields)) {
                    $fields[$fKey]['hidden'] = true;
                }
            }
        }
        $rlXajax->registerFunction(array(
            'buildForm',
            $rlBuilder,
            'ajaxBuildForm'
        ));
    } elseif ($_GET['action'] == 'config') {
        $rlXajax->registerFunction(array(
            'removeRow',
            $rlCategoryFilter,
            'ajaxRemoveRow'
        ));
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        $reefless->loadClass('Categories');
        $box_id   = (int) $_GET['item'];
        $field_id = (int) $_GET['field'];
        $sql      = "SELECT `T1`.`Items`, `T1`.`Item_names`, `T1`.`Items_display_limit`, `T1`.`Mode`, `T1`.`Status`, ";
        $sql .= "`T2`.`Key`, `T2`.`Type`, `T2`.`Values`, `T2`.`Condition` ";
        $sql .= "FROM `" . RL_DBPREFIX . "category_filter_field` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_fields` AS `T2` ON `T1`.`Field_ID` = `T2`.`ID` ";
        $sql .= "WHERE `T1`.`Box_ID` = '{$box_id}' AND `T1`.`Field_ID` = '{$field_id}' ";
        $sql .= "LIMIT 1";
        $field_info               = $rlDb->getRow($sql);
        $field_info['pName']      = 'listing_fields+name+' . $field_info['Key'];
        $field_info['Type_pName'] = 'type_' . $field_info['Type'];
        if (!$field_info)
            $sError = true;
        $rlSmarty->assign_by_ref('field_info', $field_info);
        $rlSmarty->assign('cpTitle', str_replace('{field}', $lang[$field_info['pName']], $lang['categoryFilter_title_field']));
        if (in_array($field_info['Type'], array(
            'number',
            'price',
            'mixed'
        )) || $field_info['Condition'] == 'years') {
            $sql = "SELECT MIN(ROUND(`T1`.`{$field_info['Key']}`)) AS `min`, MAX(ROUND(`T1`.`{$field_info['Key']}`)) AS `max` ";
            $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
            $sql .= "WHERE (UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0) ";
            $sql .= "AND `T1`.`{$field_info['Key']}` <> '' AND `T1`.`{$field_info['Key']}` NOT LIKE '%+%' ";
            $sql .= "AND `T1`.`Status` = 'active' AND `T3`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
            $field_stat        = $rlDb->getRow($sql);
            $field_info['min'] = $field_stat['min'];
            $field_info['max'] = $field_stat['max'];
            $rlSmarty->assign('min_max_stat', str_replace(array(
                '{min}',
                '{max}'
            ), array(
                $field_info['min'],
                $field_info['max']
            ), $lang['categoryFilter_min_max_stat']));
        }
        if (($field_info['Values'] || $field_info['Condition'] || in_array($field_info['Type'], array(
            'bool',
            'price'
        ))) && !$_POST['action']) {
            if ($field_info['Condition'] == 'years' && $field_info['Mode'] == 'group') {
                $field_info['Type'] = 'number';
            }
            switch ($field_info['Type']) {
                case 'number':
                case 'mixed':
                case 'price':
                    if ($field_info['Item_names']) {
                        $items = unserialize($field_info['Item_names']);
                        foreach ($items as $item) {
                            preg_match('/category\_filter\+name\+[0-9]+\_[0-9]+\_([0-9]+)?([\<\-\>]+)?([0-9]+)?/', $item, $matches);
                            $item_key = $matches[1] . $matches[2] . $matches[3];
                            switch ($matches[2]) {
                                case '-':
                                    $_POST['sign'][$item_key] = 'between';
                                    $_POST['from'][$item_key] = $matches[1];
                                    $_POST['to'][$item_key]   = $matches[3];
                                    break;
                                case '<':
                                    $_POST['sign'][$item_key] = 'less';
                                    $_POST['to'][$item_key]   = $matches[3];
                                    break;
                                case '>':
                                    $_POST['sign'][$item_key] = 'greater';
                                    $_POST['from'][$item_key] = $matches[1];
                                    break;
                            }
                            foreach ($allLangs as $lang_item) {
                                if ($value = $rlDb->getOne('Value', "`Key` = '{$item}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys')) {
                                    $_POST['items'][$item_key][$lang_item['Code']] = $value;
                                }
                            }
                        }
                    }
                    break;
                case 'bool':
                    $items = array(
                        1,
                        0
                    );
                    foreach ($items as $item) {
                        $custom_key = 'category_filter+name+' . $box_id . '_' . $field_id . '_' . $item;
                        $real_key   = $item ? 'yes' : 'no';
                        foreach ($allLangs as $lang_item) {
                            if ($value = $rlDb->getOne('Value', "`Key` = '{$custom_key}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys')) {
                                $_POST['items'][$item][$lang_item['Code']] = $value;
                            } else {
                                $_POST['items'][$item][$lang_item['Code']] = $rlDb->getOne('Value', "`Key` = '{$real_key}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys');
                            }
                        }
                    }
                    break;
                case 'radio':
                case 'select':
                case 'checkbox':
                    if ($field_info['Values'] && !$field_info['Condition']) {
                        $items = explode(',', $field_info['Values']);
                        foreach ($items as $item) {
                            $custom_key = 'category_filter+name+' . $box_id . '_' . $field_id . '_' . $item;
                            $real_key   = 'listing_fields+name+' . $field_info['Key'] . '_' . $item;
                            foreach ($allLangs as $lang_item) {
                                if ($value = $rlDb->getOne('Value', "`Key` = '{$custom_key}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys')) {
                                    $_POST['items'][$item][$lang_item['Code']] = $value;
                                } else {
                                    $_POST['items'][$item][$lang_item['Code']] = $rlDb->getOne('Value', "`Key` = '{$real_key}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys');
                                }
                            }
                        }
                    } elseif ($field_info['Condition']) {
                        $items = $rlCategories->getDF($field_info['Condition']);
                        foreach ($items as $item) {
                            $custom_key   = 'category_filter+name+' . $box_id . '_' . $field_id . '_' . $item['Key'];
                            $real_key     = 'data_formats+name+' . $field_info['Condition'] . '_' . $item['Key'];
                            $real_alt_key = 'data_formats+name+' . $item['Key'];
                            foreach ($allLangs as $lang_item) {
                                if ($value = $rlDb->getOne('Value', "`Key` = '{$custom_key}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys')) {
                                    $_POST['items'][$item['Key']][$lang_item['Code']] = $value;
                                } else {
                                    $_POST['items'][$item['Key']][$lang_item['Code']] = $rlDb->getOne('Value', "(`Key` = '{$real_key}' OR `Key` = '{$real_alt_key}') AND `Code` = '{$lang_item['Code']}'", 'lang_keys');
                                }
                            }
                        }
                    }
                    break;
            }
        }
        $modes = array(
            'auto' => $lang['categoryFilter_mode_auto'],
            'group' => $lang['categoryFilter_mode_group'],
            'slider' => $lang['categoryFilter_mode_slider']
        );
        $rlSmarty->assign_by_ref('modes', $modes);
        if (!$_POST['action']) {
            $_POST['mode']                = $field_info['Mode'];
            $_POST['items_display_limit'] = $field_info['Items_display_limit'];
            $_POST['status']              = $field_info['Status'];
        }
        if ($_POST['action'] == 'config') {
            if (!$_POST['items_display_limit']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['categoryFilter_visible_items_limit'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'items_display_limit';
            }
            if (!$errors) {
                $update = array(
                    'fields' => array(
                        'Items_display_limit' => (int) $_POST['items_display_limit'],
                        'Status' => $_POST['status']
                    ),
                    'where' => array(
                        'Box_ID' => $box_id,
                        'Field_ID' => $field_id
                    )
                );
                if (in_array($field_info['Type'], array(
                    'number',
                    'price',
                    'mixed'
                )) || $field_info['Condition'] == 'years') {
                    $update['fields']['Mode'] = $_POST['mode'];
                }
                $item_names = $_POST['items'];
                if ($item_names) {
                    foreach ($item_names as $item_key => $item_name) {
                        if ($_POST['sign']) {
                            switch ($_POST['sign'][$item_key]) {
                                case 'less':
                                    $new_item_key = '<' . (int) $_POST['to'][$item_key];
                                    break;
                                case 'greater':
                                    $new_item_key = (int) $_POST['from'][$item_key] . '>';
                                    break;
                                case 'between':
                                    $new_item_key = (int) $_POST['from'][$item_key] . '-' . (int) $_POST['to'][$item_key];
                                    break;
                            }
                        }
                        if ($new_item_key && !$_POST['exist'][$item_key]) {
                            $item_key = $new_item_key;
                        }
                        $phrase_key = $orig_key = 'category_filter+name+' . $box_id . '_' . $field_id . '_' . $item_key;
                        if ($new_item_key != $item_key && $_POST['exist'][$item_key]) {
                            $phrase_key = 'category_filter+name+' . $box_id . '_' . $field_id . '_' . $new_item_key;
                            $item_key   = $new_item_key;
                        }
                        foreach ($allLangs as $lang_item) {
                            if (!$item_name[$config['lang']])
                                continue;
                            if ($rlDb->getOne('ID', "`Key` = '{$orig_key}' AND `Code` = '{$lang_item['Code']}'", 'lang_keys')) {
                                $update_names = array(
                                    'fields' => array(
                                        'Value' => $item_name[$lang_item['Code']] ? $item_name[$lang_item['Code']] : $item_name[$config['lang']],
                                        'Key' => $phrase_key
                                    ),
                                    'where' => array(
                                        'Code' => $lang_item['Code'],
                                        'Key' => $orig_key
                                    )
                                );
                                $rlActions->updateOne($update_names, 'lang_keys');
                            } else {
                                $insert_names = array(
                                    'Code' => $lang_item['Code'],
                                    'Module' => 'common',
                                    'Key' => $phrase_key,
                                    'Plugin' => 'categoryFilter',
                                    'Value' => $item_name[$lang_item['Code']] ? $item_name[$lang_item['Code']] : $item_name[$config['lang']]
                                );
                                $rlActions->insertOne($insert_names, 'lang_keys');
                            }
                        }
                        $item_names_write[$item_key] = $phrase_key;
                    }
                    $update['fields']['Item_names'] = serialize($item_names_write);
                }
                if ($_POST['mode'] != 'group' && (in_array($field_info['Type'], array(
                    'number',
                    'mixed',
                    'price'
                )) || $field_info['Condition'] == 'years')) {
                    $update['fields']['Item_names'] = '';
                }
                $rlActions->updateOne($update, 'category_filter_field');
                $rlCategoryFilter->update($box_id);
                $rlCategoryFilter->compile($box_id);
                $aUrl = array(
                    'controller' => $controller,
                    'action' => 'config',
                    'item' => $box_id,
                    'field' => $field_id
                );
                $reefless->loadClass('Notice');
                $rlNotice->saveNotice($lang['categoryFilter_changes_saved']);
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
    $rlXajax->registerFunction(array(
        'deleteBox',
        $rlCategoryFilter,
        'ajaxDeleteBox'
    ));
}