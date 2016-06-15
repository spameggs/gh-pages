<?php
if ($_GET['q'] == 'ext') {
    require_once('../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $type          = $rlValid->xSql($_GET['type']);
        $field         = $rlValid->xSql($_GET['field']);
        $value         = $rlValid->xSql(nl2br($_GET['value']));
        $id            = $rlValid->xSql($_GET['id']);
        $key           = $rlValid->xSql($_GET['key']);
        $category_info = $rlDb->fetch(array(
            'Status',
            'Type'
        ), array(
            'ID' => $id
        ), null, 1, 'categories', 'row');
        $updateData    = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlHook->load('apExtCategoriesUpdate');
        $rlActions->updateOne($updateData, 'categories');
        if ($field == 'Status' && $value != $category_info['Status']) {
            $reefless->loadClass('Controls', 'admin');
            $rlControls->ajaxRecountListings(false, true);
            $rlCache->updateSearchForms();
        } else {
            $rlCache->updateCategories();
        }
        exit;
    }
    $reefless->loadClass('Common');
    $limit    = $rlValid->xSql($_GET['limit']);
    $start    = $rlValid->xSql($_GET['start']);
    $sort     = $rlValid->xSql($_GET['sort']);
    $sortDir  = $rlValid->xSql($_GET['dir']);
    $langCode = $rlValid->xSql($_GET['lang_code']);
    $phrase   = $rlValid->xSql($_GET['phrase']);
    $sql      = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T2`.`Key` AS `Parent_key`, `T3`.`Value` AS `name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "categories` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Parent_ID` = `T2`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T3` ON CONCAT('categories+name+',`T1`.`Key`) = `T3`.`Key` AND `T3`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "WHERE `T1`.`Status` <> 'Trash' ";
    if ($_GET['action'] == 'search') {
        $search_fields = array(
            'Name',
            'Type',
            'Lock',
            'Parent_ID',
            'Status'
        );
        foreach ($search_fields as $item) {
            if ($_GET[$item] != '') {
                $s_value = $rlValid->xSql($_GET[$item]);
                switch ($item) {
                    case 'Name':
                        $sql .= "AND `T3`.`Value` LIKE '%{$s_value}%' ";
                        break;
                    case 'Parent_ID':
                        $sql .= "AND FIND_IN_SET('{$s_value}', `T1`.`Parent_IDs`) > 0 ";
                        break;
                    default:
                        $sql .= "AND `T1`.`{$item}` = '{$s_value}' ";
                        break;
                }
            }
        }
    }
    if ($sort) {
        switch ($sort) {
            case 'name':
                $sortField = "`T3`.`Value`";
                break;
            case 'Parent':
                $sortField = "`T2`.`Key`";
                break;
            default:
                $sortField = "`T1`.`{$sort}`";
        }
        $sql .= "ORDER BY {$sortField} {$sortDir} ";
    }
    $sql .= "LIMIT {$start}, {$limit}";
    $rlHook->load('apExtCategoriesSql');
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    foreach ($data as $key => $value) {
        $data[$key]['Parent'] = $data[$key]['Parent_key'] ? $GLOBALS['lang']['categories+name+' . $data[$key]['Parent_key']] : $lang['no_parent'];
        $data[$key]['Type']   = $rlListingTypes->types[$data[$key]['Type']]['name'];
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
        $data[$key]['Lock']   = $data[$key]['Lock'] ? $lang['yes'] : $lang['no'];
    }
    $rlHook->load('apExtCategoriesData');
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $rlHook->load('apPhpCategoriesTop');
    if ($cInfo['prev'] == 'browse') {
        $_SESSION['categories_redirect_mode'] = 'browse';
        $_SESSION['categories_redirect_ID']   = $_GET['redirect_id'];
    } elseif (!in_array($cInfo['prev'], array(
        'browse',
        'categories'
    ))) {
        unset($_SESSION['categories_redirect_mode'], $_SESSION['categories_redirect_ID']);
    }
    if ($_GET['action']) {
        if ($_GET['action'] == 'add') {
            $bcAStep = $lang['add_category'];
        } elseif ($_GET['action'] == 'edit') {
            $bcAStep = $lang['edit_category'];
        }
    }
    $reefless->loadClass('Categories');
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs =& $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit') {
            $t_key         = $rlValid->xSql($_GET['key']);
            $category_info = $rlDb->fetch('*', array(
                'Key' => $t_key
            ), "AND `Status` <> 'trash'", null, 'categories', 'row');
            $rlSmarty->assign('cpTitle', $lang['categories+name+' . $t_key]);
            $deny_tree_categories[] = $category_info['ID'];
            $rlSmarty->assign_by_ref('deny_tree_categories', $deny_tree_categories);
            if (!$category_info) {
                $sError = true;
            }
            $reefless->loadClass('Builder', 'admin');
            $fields   = $rlBuilder->getAvailableFields($category_info['ID']);
            $add_cond = "AND(`ID` = '" . implode("' OR `ID` = '", $fields) . "') ";
            $fields   = $rlDb->fetch(array(
                'ID',
                'Key',
                'Type',
                'Status'
            ), null, "WHERE `Status` <> 'trash' {$add_cond}", null, 'listing_fields');
            $fields   = $rlLang->replaceLangKeys($fields, 'listing_fields', array(
                'name'
            ), RL_LANG_CODE, 'admin');
            $rlSmarty->assign('fields', $fields);
        }
        if ($_POST['type']) {
            $listing_type = $rlListingTypes->types[$_POST['type']];
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['key']            = $category_info['Key'];
            $_POST['path']           = $category_info['Path'];
            $_POST['status']         = $category_info['Status'];
            $_POST['type']           = $category_info['Type'];
            $_POST['lock']           = $category_info['Lock'];
            $_POST['parent_id']      = $category_info['Parent_ID'];
            $_POST['allow_children'] = $category_info['Add'];
            $_POST['subcategories']  = $category_info['Add_sub'];
            $t_names                 = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+name+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($t_names as $nKey => $nVal) {
                $_POST['name'][$t_names[$nKey]['Code']] = $t_names[$nKey]['Value'];
            }
            $t_titles = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+title+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($t_titles as $nKey => $nVal) {
                $_POST['title'][$t_titles[$nKey]['Code']] = $t_titles[$nKey]['Value'];
            }
            $descriptions = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+des+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($descriptions as $nKey => $nVal) {
                $_POST['description_' . $descriptions[$nKey]['Code']] = $descriptions[$nKey]['Value'];
            }
            $meta_description = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+meta_description+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_description as $nKey => $nVal) {
                $_POST['meta_description'][$meta_description[$nKey]['Code']] = $meta_description[$nKey]['Value'];
            }
            $meta_keywords = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+meta_keywords+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_keywords as $nKey => $nVal) {
                $_POST['meta_keywords'][$meta_keywords[$nKey]['Code']] = $meta_keywords[$nKey]['Value'];
            }
            $meta_description = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+listing_meta_description+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_description as $nKey => $nVal) {
                $_POST['listing_meta_description'][$meta_description[$nKey]['Code']] = $meta_description[$nKey]['Value'];
            }
            $meta_keywords = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+listing_meta_keywords+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_keywords as $nKey => $nVal) {
                $_POST['listing_meta_keywords'][$meta_keywords[$nKey]['Code']] = $meta_keywords[$nKey]['Value'];
            }
            $meta_title = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'categories+listing_meta_title+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_title as $nKey => $nVal) {
                $_POST['listing_meta_title'][$meta_title[$nKey]['Code']] = $meta_title[$nKey]['Value'];
            }
            if (!empty($category_info['Parent_ID'])) {
                $rlSmarty->assign_by_ref('parent_id', $category_info['Parent_ID']);
            }
            $rlHook->load('apPhpCategoriesPost');
        }
        if ($_REQUEST['type'] || $_POST['type']) {
            $parent_cats_list = $rlCategories->getCatTitles($_REQUEST['type'] ? $_REQUEST['type'] : $_POST['type']);
            $rlSmarty->assign_by_ref('parent_cats_list', $parent_cats_list);
            $categories = $rlCategories->getCatTree(0, $_POST['type']);
            $rlSmarty->assign_by_ref('categories', $categories);
            $rlSmarty->assign_by_ref('type', $_POST['type']);
        }
        if ($_GET['parent_id']) {
            $_POST['type']    = $rlDb->getOne('Type', "`ID` = '{$_GET['parent_id']}'", 'categories');
            $parent_cats_list = $rlCategories->getCatTitles($_POST['type']);
            $rlSmarty->assign_by_ref('parent_cats_list', $parent_cats_list);
            $categories = $rlCategories->getCatTree(0, $_POST['type']);
            $rlSmarty->assign_by_ref('categories', $categories);
        }
        if ($_REQUEST['parent_id'] || $_POST['parent_id']) {
            $parent_id = $_POST['parent_id'] ? $_POST['parent_id'] : $_REQUEST['parent_id'];
            $rlCategories->parentPoints(array(
                $parent_id
            ));
            $rlSmarty->assign_by_ref('parent_id', $parent_id);
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_key = $rlValid->xSql($_POST['key']);
            if ($_GET['action'] == 'add') {
                if (!utf8_is_ascii($f_key)) {
                    $f_key = utf8_to_ascii($f_key);
                }
                if (strlen($f_key) < 2) {
                    $errors[]       = $lang['incorrect_phrase_key'];
                    $error_fields[] = 'key';
                }
                $exist_key = $rlDb->fetch(array(
                    'Key',
                    'Status'
                ), array(
                    'Key' => $f_key
                ), null, 1, 'categories', 'row');
                if (!empty($exist_key)) {
                    $exist_error = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_category_exist']);
                    if ($exist_key['Status'] == 'trash') {
                        $exist_error .= " <b>(" . $lang['in_trash'] . ")</b>";
                    }
                    $error_fields[] = 'key';
                    $errors[]       = $exist_error;
                }
            }
            $orig_key = $f_key;
            if ($_GET['action'] == 'add') {
                $f_key = $rlValid->str2key($f_key);
            }
            $f_type = $_POST['type'];
            if (empty($f_type)) {
                $errors[]       = $lang['notice_type_empty'];
                $error_fields[] = 'type';
            }
            $f_path = $_POST['path'];
            if (!utf8_is_ascii($f_path) && !empty($f_path)) {
                $f_path = utf8_to_ascii($f_path);
            }
            $replace_mode = $_GET['action'] == 'add' ? false : true;
            $f_path       = empty($f_path) ? $rlValid->str2path($orig_key) : $rlValid->str2path($f_path, $replace_mode);
            $path_where   = $_GET['action'] == 'edit' ? "AND `Key` <> '{$f_key}'" : null;
            $exist_path   = $rlDb->fetch(array(
                'Path',
                'Status'
            ), array(
                'Path' => $f_path
            ), $path_where, 1, 'categories', 'row');
            if (!empty($exist_path)) {
                if ($exist_path['Status'] != 'trash') {
                    $errors[] = str_replace('{path}', "<b>" . $f_path . "</b>", $lang['notice_path_exist']);
                } else {
                    $errors[] = str_replace('{path}', "<b>" . $f_path . "</b>", $lang['notice_path_exist_droped']);
                }
                $error_fields[] = 'path';
            }
            preg_match('/\-[0-9]+$/', $f_path, $matches);
            if (!empty($matches)) {
                $errors[]       = $lang['category_url_listing_logic'];
                $error_fields[] = "path";
            }
            if ($_GET['action'] == 'add' || empty($_POST['path'])) {
                $f_relate_path = $rlCategories->getCatPath($parent_id);
                $f_path        = $f_relate_path . $f_path;
            }
            if ($_GET['action'] == 'edit' && $category_info['Path'] != $f_path) {
                $edit_cat_path = $rlDb->getOne('Path', "`ID` = '{$_POST['parent_id']}'", 'categories');
                if ($edit_cat_path) {
                    $exploded_original_path = explode('/', trim($edit_cat_path, '/'));
                    $exploded_new_path      = explode('/', trim($f_path, '/'));
                    if (count($exploded_original_path) + 1 != count($exploded_new_path)) {
                        $errors[]       = $lang['remove_slashes_warning'];
                        $error_fields[] = 'path';
                    } else {
                        array_pop($exploded_new_path);
                        if (implode('/', $exploded_new_path) != implode('/', $exploded_original_path)) {
                            $errors[]       = $lang['category_path_warning'];
                            $error_fields[] = 'path';
                        }
                    }
                }
                if (!$errors) {
                    $replace_path_sql = "UPDATE `" . RL_DBPREFIX . "categories` SET `Path` = REPLACE(`Path`, '{$category_info['Path']}', '{$f_path}') WHERE `Path` LIKE '{$category_info['Path']}%'";
                    $rlDb->query($replace_path_sql);
                }
            }
            $f_tree = $rlDb->getOne('Tree', "`ID` = '{$parent_id}'", 'categories');
            $f_name = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$lval['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$lval['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$lval['Code']}]";
                }
                $f_names[$lval['Code']] = $f_name[$lval['Code']];
            }
            $f_title = $_POST['title'];
            $rlHook->load('apPhpCategoriesDataValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                $level     = $_POST['parent_id'] ? $rlDb->getOne('Level', "`ID` = '{$_POST['parent_id']}'", 'categories') + 1 : 0;
                $parent_id = $parent_id ? $parent_id : 0;
                if ($_GET['action'] == 'add') {
                    $position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "categories` WHERE `Parent_ID` = {$parent_id}");
                    $position = $position['max'] + 1;
                    if ($parent_id) {
                        $parent_ids[] = $parent_id;
                        if ($parents = $rlCategories->getParentIDs($parent_id)) {
                            $parent_ids = array_merge($parent_ids, $parents);
                        }
                        $parent_ids = implode(',', $parent_ids);
                    }
                    $data = array(
                        'Key' => $f_key,
                        'Path' => $f_path,
                        'Status' => $_POST['status'],
                        'Type' => $_POST['type'],
                        'Lock' => $_POST['lock'],
                        'Parent_ID' => $parent_id,
                        'Parent_IDs' => $parent_ids,
                        'Position' => $position,
                        'Level' => $level,
                        'Modified' => 'NOW()',
                        'Tree' => $f_tree . '.' . $position
                    );
                    if ($listing_type['Cat_custom_adding']) {
                        $data['Add']     = $_POST['allow_children'] ? 1 : 0;
                        $data['Add_sub'] = empty($_POST['subcategories']) ? 0 : 1;
                    }
                    $rlHook->load('apPhpCategoriesBeforeAdd');
                    if ($action = $rlActions->insertOne($data, 'categories')) {
                        $rlCache->updateCategories();
                        $category_id = mysql_insert_id();
                        $rlHook->load('apPhpCategoriesAfterAdd');
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'categories+name+' . $f_key,
                                'Value' => trim($f_name[$allLangs[$key]['Code']])
                            );
                            if (!empty($f_title[$value['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+title+' . $f_key,
                                    'Value' => trim($f_title[$value['Code']])
                                );
                            }
                            if (!empty($_POST['description_' . $allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+des+' . $f_key,
                                    'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['meta_description'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+meta_description+' . $f_key,
                                    'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['meta_keywords'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+meta_keywords+' . $f_key,
                                    'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['listing_meta_description'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+listing_meta_description+' . $f_key,
                                    'Value' => trim($_POST['listing_meta_description'][$allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['listing_meta_keywords'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+listing_meta_keywords+' . $f_key,
                                    'Value' => trim($_POST['listing_meta_keywords'][$allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['listing_meta_title'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+listing_meta_title+' . $f_key,
                                    'Value' => trim($_POST['listing_meta_title'][$allLangs[$key]['Code']])
                                );
                            }
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $message = $lang['category_added'];
                        if ($_SESSION['categories_redirect_mode']) {
                            $aUrl = array(
                                "controller" => "browse",
                                "id" => $_POST['parent_id']
                            );
                        } else {
                            $reefless->loadClass('Builder', 'admin');
                            $relations = $rlBuilder->getRelations($category_id);
                            $aUrl      = array(
                                "controller" => $controller
                            );
                            if (empty($relations) && !$listing_type['Cat_general_cat']) {
                                $aUrl['request'] = 'build';
                                $aUrl['key']     = $f_key;
                            }
                        }
                    } else {
                        trigger_error("Can't add new lisitng type (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new lisitng type (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_data = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Path' => $f_path,
                            'Type' => $_POST['type'],
                            'Lock' => $_POST['lock'],
                            'Parent_ID' => $_POST['parent_id'],
                            'Level' => $level,
                            'Modified' => 'NOW()'
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    if ($parent_id && $category_info['Parent_ID'] != $parent_id) {
                        $parent_ids[] = $parent_id;
                        if ($parents = $rlCategories->getParentIDs($parent_id)) {
                            $parent_ids = array_merge($parent_ids, $parents);
                        }
                        $update_data['fields']['Parent_IDs'] = implode(',', $parent_ids);
                    }
                    if ($listing_type['Cat_custom_adding']) {
                        $update_data['fields']['Add']     = $_POST['allow_children'] ? 1 : 0;
                        $update_data['fields']['Add_sub'] = empty($_POST['subcategories']) ? 0 : 1;
                    }
                    $rlHook->load('apPhpCategoriesBeforeEdit');
                    $action = $rlActions->updateOne($update_data, 'categories');
                    $rlHook->load('apPhpCategoriesAfterEdit');
                    $rlCache->updateCategories();
                    $category_id = $rlDb->getOne("ID", "`Key` = '{$f_key}'", 'categories');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'categories+name+{$f_key}' AND `Code` = '{$value['Code']}'", 'lang_keys')) {
                            $lang_keys_name[] = array(
                                'where' => array(
                                    'Code' => $value['Code'],
                                    'Key' => 'categories+name+' . $f_key
                                ),
                                'fields' => array(
                                    'Value' => $_POST['name'][$value['Code']]
                                )
                            );
                        } else {
                            $insert_phrases = array(
                                'Code' => $value['Code'],
                                'Module' => 'common',
                                'Key' => 'categories+name+' . $f_key,
                                'Value' => $_POST['name'][$value['Code']]
                            );
                            $rlActions->insertOne($insert_phrases, 'lang_keys');
                        }
                        if ($rlDb->getOne('ID', "`Key` = 'categories+title+{$f_key}' AND `Code` = '{$value['Code']}'", 'lang_keys')) {
                            $lang_keys_name[] = array(
                                'where' => array(
                                    'Code' => $value['Code'],
                                    'Key' => 'categories+title+' . $f_key
                                ),
                                'fields' => array(
                                    'Value' => $_POST['title'][$value['Code']]
                                )
                            );
                        } else {
                            $insert_phrases = array(
                                'Code' => $value['Code'],
                                'Module' => 'common',
                                'Key' => 'categories+title+' . $f_key,
                                'Value' => $_POST['name'][$value['Code']]
                            );
                            $rlActions->insertOne($insert_phrases, 'lang_keys');
                        }
                        if (!empty($_POST['description_' . $allLangs[$key]['Code']])) {
                            $c_description = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'categories+des+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($c_description)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'categories+des+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+des+' . $f_key,
                                    'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'categories+des+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['meta_description'][$allLangs[$key]['Code']])) {
                            $meta_description = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'categories+meta_description+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($meta_description)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'categories+meta_description+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+meta_description+' . $f_key,
                                    'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'categories+meta_description+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['meta_keywords'][$allLangs[$key]['Code']])) {
                            $meta_keywords = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'categories+meta_keywords+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($meta_keywords)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'categories+meta_keywords+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+meta_keywords+' . $f_key,
                                    'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'categories+meta_keywords+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['listing_meta_description'][$allLangs[$key]['Code']])) {
                            $listing_meta_description = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'categories+listing_meta_description+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($listing_meta_description)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'categories+listing_meta_description+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['listing_meta_description'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+listing_meta_description+' . $f_key,
                                    'Value' => trim($_POST['listing_meta_description'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'categories+listing_meta_description+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['listing_meta_keywords'][$allLangs[$key]['Code']])) {
                            $listing_meta_keywords = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'categories+listing_meta_keywords+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($listing_meta_keywords)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'categories+listing_meta_keywords+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['listing_meta_keywords'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+listing_meta_keywords+' . $f_key,
                                    'Value' => trim($_POST['listing_meta_keywords'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'categories+listing_meta_keywords+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['listing_meta_title'][$allLangs[$key]['Code']])) {
                            $listing_meta_title = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'categories+listing_meta_title+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($listing_meta_title)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'categories+listing_meta_title+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['listing_meta_title'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+listing_meta_title+' . $f_key,
                                    'Value' => trim($_POST['listing_meta_title'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'categories+listing_meta_title+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                    }
                    $GLOBALS['rlActions']->update($lang_keys_name, 'lang_keys');
                    $message = $lang['category_edited'];
                    if ($_SESSION['categories_redirect_mode'] && $_SESSION['categories_redirect_ID']) {
                        $aUrl = array(
                            "controller" => "browse",
                            "id" => $_SESSION['categories_redirect_ID']
                        );
                    } else {
                        $aUrl = array(
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
    } elseif ($_GET['action'] == 'build') {
        $category_key  = $rlValid->xSql($_GET['key']);
        $category_info = $rlDb->fetch(array(
            'ID',
            'Key'
        ), array(
            'Key' => $category_key
        ), "AND `Status` <> 'trash'", null, 'categories', 'row');
        $category_info = $rlLang->replaceLangKeys($category_info, 'categories', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('category_info', $category_info);
        if (!$category_info) {
            $sError = true;
        } else {
            $rlSmarty->assign('cpTitle', $category_info['name']);
            $reefless->loadClass('Builder', 'admin');
            if ($_GET['form'] == 'submit_form') {
                $bcAStep[] = array(
                    'name' => $lang['submit_form_builder']
                );
                $rlSmarty->assign('cpTitle', $category_info['name']);
                $relations = $rlBuilder->getRelations($category_info['ID']);
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
                $groups = $rlDb->fetch(array(
                    'ID',
                    'Key',
                    'Status'
                ), null, "WHERE `Status` <> 'trash'", null, 'listing_groups');
                $groups = $rlLang->replaceLangKeys($groups, 'listing_groups', array(
                    'name'
                ), RL_LANG_CODE, 'admin');
                if (!empty($no_groups)) {
                    foreach ($groups as $grKey => $grVal) {
                        if (false !== array_search($groups[$grKey]['Key'], $no_groups)) {
                            $groups[$grKey]['hidden'] = true;
                        }
                    }
                }
                $rlSmarty->assign_by_ref('groups', $groups);
                $deny_fields = array(
                    'Category_ID',
                    'keyword_search',
                    'posted_by'
                );
                $fields      = $rlDb->fetch(array(
                    'ID',
                    'Key',
                    'Type',
                    'Status'
                ), null, "WHERE `Status` <> 'trash' AND `Key` <> '" . implode("' AND `Key` <> '", $deny_fields) . "'", null, 'listing_fields');
                $fields      = $rlLang->replaceLangKeys($fields, 'listing_fields', array(
                    'name'
                ), RL_LANG_CODE, 'admin');
                if (!empty($no_fields)) {
                    foreach ($fields as $fKey => $fVal) {
                        if (false !== array_search($fields[$fKey]['Key'], $no_fields)) {
                            $fields[$fKey]['hidden'] = true;
                        }
                    }
                }
                $rlSmarty->assign_by_ref('fields', $fields);
            } else {
                $rlSmarty->assign('no_groups', true);
                switch ($_GET['form']) {
                    case 'short_form':
                        $rlBuilder->rlBuildTable = 'short_forms';
                        $rlBuilder->rlBuildField = 'Field_ID';
                        $bcAStep                 = $lang['short_form_builder'];
                        break;
                    case 'listing_title':
                        $rlBuilder->rlBuildTable = 'listing_titles';
                        $rlBuilder->rlBuildField = 'Field_ID';
                        $bcAStep                 = $lang['listing_title_builder'];
                        break;
                    case 'featured_form':
                        $rlBuilder->rlBuildTable = 'featured_form';
                        $rlBuilder->rlBuildField = 'Field_ID';
                        $bcAStep                 = $lang['featured_form_builder'];
                        break;
                }
                $a_fields = $rlBuilder->getAvailableFields($category_info['ID']);
                if ($_GET['form'] == 'submit_form') {
                    $relations = $rlBuilder->getRelations($category_info['ID']);
                } else {
                    $relations = $rlBuilder->getFormRelations($category_info['ID']);
                }
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
                if (!empty($a_fields)) {
                    $add_cond = "AND(`ID` = '" . implode("' OR `ID` = '", $a_fields) . "') ";
                    $fields   = $rlDb->fetch(array(
                        'ID',
                        'Key',
                        'Type',
                        'Status'
                    ), null, "WHERE `Status` <> 'trash' {$add_cond}", null, 'listing_fields');
                    $fields   = $rlLang->replaceLangKeys($fields, 'listing_fields', array(
                        'name'
                    ), RL_LANG_CODE, 'admin');
                    if (!empty($no_fields)) {
                        foreach ($fields as $fKey => $fVal) {
                            if (false !== array_search($fields[$fKey]['Key'], $no_fields)) {
                                $fields[$fKey]['hidden'] = true;
                            }
                        }
                    }
                    $rlSmarty->assign_by_ref('fields', $fields);
                }
            }
            $rlHook->load('apPhpCategoriesBuild');
            $rlXajax->registerFunction(array(
                'buildForm',
                $rlBuilder,
                'ajaxBuildForm'
            ));
        }
    } else {
        $parent_cats_list = $rlCategories->getCatTitles(0);
        $rlSmarty->assign_by_ref('parent_cats_list', $parent_cats_list);
    }
    $rlHook->load('apPhpCategoriesBottom');
    $reefless->loadClass('Categories');
    $reefless->loadClass('Controls', 'admin');
    $rlXajax->registerFunction(array(
        'prepareDeleting',
        $rlCategories,
        'ajaxPrepareDeleting'
    ));
    $rlXajax->registerFunction(array(
        'deleteCategory',
        $rlCategories,
        'ajaxDeleteCategory'
    ));
    $rlXajax->registerFunction(array(
        'getCatLevel',
        $rlCategories,
        'ajaxGetCatLevel'
    ));
    $rlXajax->registerFunction(array(
        'loadType',
        $rlCategories,
        'ajaxLoadType'
    ));
}