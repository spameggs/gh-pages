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
        $rlHook->load('apExtListingTypesUpdate');
        $rlActions->updateOne($updateData, 'listing_types');
        $type_key = $rlDb->getOne('Key', "`ID` = '{$id}'", 'listing_types');
        if ($field == 'Status') {
            $reefless->loadClass('ListingTypes');
            $rlListingTypes->activateComponents($type_key, $value);
            $rlListingTypes->get();
            $rlCache->updateListingStatistics();
        } elseif ($field == 'Admin_only') {
            $reefless->loadClass('ListingTypes');
            $rlListingTypes->adminOnly($type_key, $value ? 'trash' : 'active');
        }
        exit;
    }
    $limit   = $rlValid->xSql($_GET['limit']);
    $start   = $rlValid->xSql($_GET['start']);
    $sort    = $rlValid->xSql($_GET['sort']);
    $sortDir = $rlValid->xSql($_GET['dir']);
    $sql     = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T2`.`Value` AS `name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listing_types` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('listing_types+name+',`T1`.`Key`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "WHERE `T1`.`Status` <> 'trash' ";
    if ($sort) {
        $sortField = $sort == 'name' ? "`T2`.`Value`" : "`T1`.`{$sort}`";
        $sql .= "ORDER BY {$sortField} {$sortDir} ";
    }
    $sql .= "LIMIT {$start}, {$limit}";
    $rlHook->load('apExtListingTypesSql');
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    foreach ($data as $key => $value) {
        $data[$key]['Status']     = $GLOBALS['lang'][$data[$key]['Status']];
        $data[$key]['Admin_only'] = $data[$key]['Admin_only'] ? $lang['yes'] : $lang['no'];
    }
    $rlHook->load('apExtListingTypesData');
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $rlHook->load('apPhpListingTypesTop');
    $reefless->loadClass('Categories');
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_type'] : $lang['edit_type'];
    } else {
        $rlXajax->registerFunction(array(
            'prepareDeleting',
            $rlListingTypes,
            'ajaxPrepareDeleting'
        ));
        $rlXajax->registerFunction(array(
            'deleteListingType',
            $rlListingTypes,
            'ajaxDeletingType'
        ));
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
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
        $sql = "SELECT DISTINCT `T1`.`Key`, `T1`.`Type`, IF(`T1`.`Type` = 'bool', '1,0', `Values`) AS `Values` FROM `" . RL_DBPREFIX . "listing_fields` AS `T1` ";
        $sql .= "WHERE `T1`.`Type` IN ('radio', 'select', 'bool', 'checkbox') AND `T1`.`Status` = 'active' ";
        $sql .= "AND `T1`.`Key` <> 'year' AND `T1`.`Condition` = '' ";
        $sql .= "AND ( ";
        $sql .= "(( LENGTH(`T1`.`Values`) - LENGTH(REPLACE(`T1`.`Values`, ',', '')) + 1) BETWEEN 2 AND 3 AND `T1`.`Values` <> '') OR ";
        $sql .= "`T1`.`Type` = 'bool' ";
        $sql .= ") ";
        $fields = $rlDb->getAll($sql);
        $fields = $rlLang->replaceLangKeys($fields, 'listing_fields', 'name');
        foreach ($fields as $fField) {
            $tmpFields[$fField['Key']] = $fField;
        }
        $fields = $tmpFields;
        unset($tmpFields);
        $rlSmarty->assign_by_ref('fields', $fields);
        $category_order_types = array(
            array(
                'name' => $lang['order'],
                'key' => 'position'
            ),
            array(
                'name' => $lang['alphabetic'],
                'key' => 'alphabetic'
            )
        );
        $rlSmarty->assign_by_ref('category_order_types', $category_order_types);
        $search_form_types = array(
            array(
                'name' => $lang['content_and_block'],
                'key' => 'content_and_block'
            ),
            array(
                'name' => $lang['block_only'],
                'key' => 'block_only'
            )
        );
        $rlSmarty->assign_by_ref('search_form_types', $search_form_types);
        $refine_search_types = array(
            array(
                'name' => 'POST',
                'key' => 'post'
            ),
            array(
                'name' => 'GET',
                'key' => 'get'
            )
        );
        $rlSmarty->assign_by_ref('refine_search_types', $refine_search_types);
        $cat_positions = array(
            array(
                'name' => $lang['hide'],
                'key' => 'hide'
            ),
            array(
                'name' => $lang['top'],
                'key' => 'top'
            ),
            array(
                'name' => $lang['bottom'],
                'key' => 'bottom'
            )
        );
        $rlSmarty->assign_by_ref('cat_positions', $cat_positions);
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        $p_key = $rlValid->xSql($_GET['key']);
        if ($p_key) {
            $type_info = $rlDb->fetch('*', array(
                'Key' => $p_key
            ), null, null, 'listing_types', 'row');
        }
        if ($_GET['action'] == 'edit') {
            $reefless->loadClass('Categories');
            $category_titles = $rlCategories->getCatTitles($p_key);
            $rlSmarty->assign_by_ref('category_titles', $category_titles);
            $rlSmarty->assign('cpTitle', $lang['listing_types+name+' . $type_info['Key']]);
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['key']                          = $type_info['Key'];
            $_POST['page']                         = $type_info['Page'];
            $_POST['photo']                        = $type_info['Photo'];
            $_POST['video']                        = $type_info['Video'];
            $_POST['admin']                        = $type_info['Admin_only'];
            $_POST['general_cat']                  = $type_info['Cat_general_cat'];
            $_POST['cat_position']                 = $type_info['Cat_position'];
            $_POST['cat_columns_number']           = $type_info['Cat_columns_number'];
            $_POST['cat_visible_number']           = $type_info['Cat_visible_number'];
            $_POST['display_counter']              = $type_info['Cat_listing_counter'];
            $_POST['html_postfix']                 = $type_info['Cat_postfix'];
            $_POST['category_order']               = $type_info['Cat_order_type'];
            $_POST['allow_subcategories']          = $type_info['Cat_custom_adding'];
            $_POST['display_subcategories']        = $type_info['Cat_show_subcats'];
            $_POST['subcategories_number']         = $type_info['Cat_subcat_number'];
            $_POST['scrolling_in_box']             = $type_info['Cat_scrolling'];
            $_POST['ablock_pages']                 = explode(',', $type_info['Ablock_pages']);
            $_POST['ablock_position']              = $type_info['Ablock_position'];
            $_POST['ablock_columns_number']        = $type_info['Ablock_columns_number'];
            $_POST['ablock_visible_number']        = $type_info['Ablock_visible_number'];
            $_POST['ablock_display_subcategories'] = $type_info['Ablock_show_subcats'];
            $_POST['ablock_subcategories_number']  = $type_info['Ablock_subcat_number'];
            $_POST['ablock_scrolling_in_box']      = $type_info['Ablock_scrolling'];
            $_POST['search_form']                  = $type_info['Search'];
            $_POST['search_home']                  = $type_info['Search_home'];
            $_POST['search_page']                  = $type_info['Search_page'];
            $_POST['advanced_search']              = $type_info['Advanced_search'];
            $_POST['display_form_in']              = $type_info['Search_display'];
            $_POST['refine_search_type']           = $type_info['Submit_method'];
            $_POST['featured_blocks']              = $type_info['Featured_blocks'];
            $_POST['random_featured']              = $type_info['Random_featured'];
            $_POST['random_featured_type']         = $type_info['Random_featured_type'];
            $_POST['random_featured_number']       = $type_info['Random_featured_number'];
            $_POST['arrange_field']                = $type_info['Arrange_field'];
            $_POST['is_arrange_search']            = $type_info['Arrange_search'];
            $_POST['is_arrange_featured']          = $type_info['Arrange_featured'];
            $_POST['is_arrange_statistics']        = $type_info['Arrange_stats'];
            $_POST['search_multi_categories']      = $type_info['Search_multi_categories'];
            $_POST['search_multicat_levels']       = $type_info['Search_multicat_levels'];
            $_POST['status']                       = $type_info['Status'];
            $names                                 = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'listing_types+name+' . $p_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($names as $pKey => $pVal) {
                $_POST['name'][$names[$pKey]['Code']] = $names[$pKey]['Value'];
            }
            $rlListingTypes->simulate($type_info['Arrange_field']);
            $rlHook->load('apPhpListingTypesPost');
        }
        if (isset($_POST['submit'])) {
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_key = $_POST['key'];
            if ($_GET['action'] == 'add') {
                if (!utf8_is_ascii($f_key)) {
                    $f_key = utf8_to_ascii($f_key);
                }
                if (strlen($f_key) < 2) {
                    $errors[]       = $lang['incorrect_phrase_key'];
                    $error_fields[] = 'key';
                }
                $exist_key = $rlDb->fetch(array(
                    'Key'
                ), array(
                    'Key' => $f_key
                ), null, null, 'listing_types');
                if (!empty($exist_key)) {
                    $errors[]       = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_key_exist']);
                    $error_fields   = array();
                    $error_fields[] = 'key';
                }
            }
            $f_key  = $rlValid->str2key($f_key);
            $f_name = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$allLangs[$lkey]['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$allLangs[$lkey]['Code']}]";
                }
                $f_names[$allLangs[$lkey]['Code']] = $f_name[$allLangs[$lkey]['Code']];
            }
            if ($_POST['page']) {
                if (empty($_POST['cat_columns_number'])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['number_of_columns'] . "</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'cat_columns_number';
                }
                if (empty($_POST['cat_position'])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['position'] . "</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'cat_position';
                }
                if (!empty($_POST['ablock_pages']) && empty($_POST['ablock_position'])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['position'] . "</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'ablock_position';
                }
                if (!empty($_POST['ablock_pages']) && empty($_POST['ablock_columns_number'])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['number_of_columns'] . "</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'ablock_columns_number';
                }
            }
            $rlHook->load('apPhpListingTypesValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $order            = $rlDb->getRow("SELECT MAX(`Order`) AS `max` FROM `" . RL_DBPREFIX . "listing_types`");
                    $data             = array(
                        'Key' => $f_key,
                        'Order' => $order['max'] + 1,
                        'Page' => (int) $_POST['page'],
                        'Photo' => (int) $_POST['photo'],
                        'Video' => (int) $_POST['video'],
                        'Admin_only' => (int) $_POST['admin'],
                        'Cat_general_cat' => (int) $_POST['general_cat'],
                        'Cat_position' => $_POST['cat_position'],
                        'Cat_columns_number' => (int) $_POST['cat_columns_number'],
                        'Cat_visible_number' => (int) $_POST['cat_visible_number'],
                        'Cat_listing_counter' => (int) $_POST['display_counter'],
                        'Cat_postfix' => (int) $_POST['html_postfix'],
                        'Cat_order_type' => $_POST['category_order'],
                        'Cat_custom_adding' => (int) $_POST['allow_subcategories'],
                        'Cat_show_subcats' => (int) $_POST['display_subcategories'],
                        'Cat_subcat_number' => (int) $_POST['subcategories_number'],
                        'Cat_scrolling' => (int) $_POST['scrolling_in_box'],
                        'Ablock_pages' => implode(',', $_POST['ablock_pages']),
                        'Ablock_position' => $_POST['ablock_position'],
                        'Ablock_columns_number' => (int) $_POST['ablock_columns_number'],
                        'Ablock_visible_number' => (int) $_POST['ablock_visible_number'],
                        'Ablock_show_subcats' => (int) $_POST['ablock_display_subcategories'],
                        'Ablock_subcat_number' => (int) $_POST['ablock_subcategories_number'],
                        'Ablock_scrolling' => (int) $_POST['ablock_scrolling_in_box'],
                        'Search' => (int) $_POST['search_form'],
                        'Search_home' => (int) $_POST['search_home'],
                        'Search_page' => (int) $_POST['search_page'],
                        'Advanced_search' => (int) $_POST['advanced_search'],
                        'Search_display' => $_POST['display_form_in'],
                        'Submit_method' => $_POST['refine_search_type'],
                        'Featured_blocks' => (int) $_POST['featured_blocks'],
                        'Random_featured' => (int) $_POST['random_featured'],
                        'Random_featured_type' => $_POST['random_featured_type'],
                        'Random_featured_number' => $_POST['random_featured_number'],
                        'Arrange_field' => $_POST['arrange_field'],
                        'Arrange_values' => $fields[$_POST['arrange_field']]['Values'],
                        'Arrange_search' => (int) $_POST['is_arrange_search'],
                        'Arrange_featured' => (int) $_POST['is_arrange_featured'],
                        'Arrange_stats' => (int) $_POST['is_arrange_statistics'],
                        'Search_multi_categories' => (int) $_POST['search_multi_categories'],
                        'Search_multicat_levels' => (int) $_POST['search_multicat_levels'],
                        'Status' => $_POST['status']
                    );
                    $update_cache_key = $f_key;
                    $rlHook->load('apPhpListingTypesBeforeAdd');
                    if ($action = $rlActions->insertOne($data, 'listing_types')) {
                        $rlHook->load('apPhpListingTypesAfterAdd');
                        $rlActions->enumAdd('search_forms', 'Type', $f_key);
                        $rlActions->enumAdd('categories', 'Type', $f_key);
                        $rlActions->enumAdd('account_types', 'Abilities', $f_key);
                        $rlActions->enumAdd('saved_search', 'Listing_type', $f_key);
                        $rlDb->query("UPDATE `" . RL_DBPREFIX . "account_types` SET `Abilities` = TRIM(BOTH ',' FROM CONCAT(`Abilities`, ',{$f_key}'))");
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'listing_types+name+' . $f_key,
                                'Value' => $f_name[$allLangs[$key]['Code']]
                            );
                            if ($_POST['page']) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+name+lt_' . $f_key,
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+title+lt_' . $f_key,
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            if (!(int) $_POST['admin']) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+name+my_' . $f_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['my_listings_pattern'])
                                );
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+title+my_' . $f_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['my_listings_pattern'])
                                );
                            }
                            if (!empty($_POST['ablock_pages'])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+ltcb_' . $f_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['categories_block_pattern'])
                                );
                            }
                            if ($_POST['featured_blocks']) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+ltfb_' . $f_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['featured_block_pattern'])
                                );
                            }
                            if (!empty($_POST['search_form'])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'search_forms+name+' . $f_key . '_quick',
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            if (!empty($_POST['advanced_search'])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'search_forms+name+' . $f_key . '_advanced',
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            if ($_POST['search_form'] && $_POST['page']) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+ltsb_' . $f_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['refine_search_pattern'])
                                );
                            }
                            if (!$_POST['page']) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+name+' . $f_key . '_listings',
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        if ($_POST['page']) {
                            $page_position   = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "pages`");
                            $individual_page = array(
                                'Parent_ID' => 0,
                                'Page_type' => 'system',
                                'Login' => 0,
                                'Key' => 'lt_' . $f_key,
                                'Position' => $page_position['max'] + 1,
                                'Path' => $rlValid->str2path($f_key),
                                'Controller' => 'listing_type',
                                'Tpl' => 1,
                                'Menus' => 1,
                                'Modified' => 'NOW()',
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($individual_page, 'pages');
                            $page_id = mysql_insert_id();
                        } else {
                            $category_insert = array(
                                'Position' => 1,
                                'Path' => $rlValid->str2path($f_key) . '-listings',
                                'Level' => 0,
                                'Tree' => 1,
                                'Parent_ID' => 0,
                                'Type' => $f_key,
                                'Key' => $f_key . '_listings',
                                'Status' => 'active'
                            );
                            $rlActions->insertOne($category_insert, 'categories');
                        }
                        if (!(int) $_POST['admin']) {
                            $page_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "pages`");
                            $my_page       = array(
                                'Parent_ID' => 0,
                                'Page_type' => 'system',
                                'Login' => 1,
                                'Key' => 'my_' . $f_key,
                                'Position' => $page_position['max'] + 1,
                                'Path' => 'my-' . $rlValid->str2path($f_key),
                                'Controller' => 'my_listings',
                                'Tpl' => 1,
                                'Menus' => 2,
                                'Modified' => 'NOW()',
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($my_page, 'pages');
                        }
                        if (!empty($_POST['search_form'])) {
                            $search_form = array(
                                'Key' => $f_key . '_quick',
                                'Type' => $f_key,
                                'Mode' => 'quick',
                                'Groups' => 0,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($search_form, 'search_forms');
                        }
                        if (!empty($_POST['advanced_search'])) {
                            $search_form = array(
                                'Key' => $f_key . '_advanced',
                                'Type' => $f_key,
                                'Mode' => 'advanced',
                                'Groups' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($search_form, 'search_forms');
                        }
                        if (!empty($_POST['ablock_pages'])) {
                            $cat_block_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $category_block     = array(
                                'Page_ID' => implode(',', $_POST['ablock_pages']),
                                'Sticky' => 0,
                                'Key' => 'ltcb_' . $f_key,
                                'Position' => $cat_block_position['max'] + 1,
                                'Side' => $_POST['ablock_position'],
                                'Type' => 'smarty',
                                'Content' => $f_key,
                                'Tpl' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($category_block, 'blocks');
                        }
                        if ($_POST['featured_blocks']) {
                            $f_block_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $featured_block   = array(
                                'Page_ID' => $page_id ? $page_id : 1,
                                'Sticky' => 0,
                                'Key' => 'ltfb_' . $f_key,
                                'Position' => $f_block_position['max'] + 1,
                                'Side' => 'left',
                                'Type' => 'smarty',
                                'Content' => '{include file=\'blocks\'|cat:$smarty.const.RL_DS|cat:\'featured.tpl\' listings=$featured_' . $f_key . ' type=\'' . $f_key . '\'}',
                                'Tpl' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($featured_block, 'blocks');
                        }
                        if ($_POST['search_form'] && $_POST['page']) {
                            $s_block_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $search_block     = array(
                                'Page_ID' => $page_id ? $page_id : 1,
                                'Sticky' => 0,
                                'Key' => 'ltsb_' . $f_key,
                                'Position' => $f_block_position['max'] + 1,
                                'Side' => 'left',
                                'Type' => 'smarty',
                                'Content' => '{include file=$refine_block_controller}',
                                'Tpl' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($search_block, 'blocks');
                        }
                        $rlListingTypes->arrange($_POST['arrange_field']);
                        $message = $lang['listing_type_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new lisitng type (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new lisitng type (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_date      = array(
                        'fields' => array(
                            'Page' => (int) $_POST['page'],
                            'Photo' => (int) $_POST['photo'],
                            'Video' => (int) $_POST['video'],
                            'Admin_only' => (int) $_POST['admin'],
                            'Cat_general_cat' => (int) $_POST['general_cat'],
                            'Cat_position' => $_POST['cat_position'],
                            'Cat_columns_number' => (int) $_POST['cat_columns_number'],
                            'Cat_visible_number' => (int) $_POST['cat_visible_number'],
                            'Cat_listing_counter' => (int) $_POST['display_counter'],
                            'Cat_postfix' => (int) $_POST['html_postfix'],
                            'Cat_order_type' => $_POST['category_order'],
                            'Cat_custom_adding' => (int) $_POST['allow_subcategories'],
                            'Cat_show_subcats' => (int) $_POST['display_subcategories'],
                            'Cat_subcat_number' => (int) $_POST['subcategories_number'],
                            'Cat_scrolling' => (int) $_POST['scrolling_in_box'],
                            'Ablock_pages' => implode(',', $_POST['ablock_pages']),
                            'Ablock_position' => $_POST['ablock_position'],
                            'Ablock_columns_number' => (int) $_POST['ablock_columns_number'],
                            'Ablock_visible_number' => (int) $_POST['ablock_visible_number'],
                            'Ablock_show_subcats' => (int) $_POST['ablock_display_subcategories'],
                            'Ablock_subcat_number' => (int) $_POST['ablock_subcategories_number'],
                            'Ablock_scrolling' => (int) $_POST['ablock_scrolling_in_box'],
                            'Search' => (int) $_POST['search_form'],
                            'Search_home' => (int) $_POST['search_home'],
                            'Search_page' => (int) $_POST['search_page'],
                            'Advanced_search' => (int) $_POST['advanced_search'],
                            'Search_display' => $_POST['display_form_in'],
                            'Submit_method' => $_POST['refine_search_type'],
                            'Featured_blocks' => (int) $_POST['featured_blocks'],
                            'Random_featured' => (int) $_POST['random_featured'],
                            'Random_featured_type' => $_POST['random_featured_type'],
                            'Random_featured_number' => $_POST['random_featured_number'],
                            'Arrange_field' => $_POST['arrange_field'],
                            'Arrange_values' => $fields[$_POST['arrange_field']]['Values'],
                            'Arrange_search' => (int) $_POST['is_arrange_search'],
                            'Arrange_featured' => (int) $_POST['is_arrange_featured'],
                            'Arrange_stats' => (int) $_POST['is_arrange_statistics'],
                            'Search_multi_categories' => (int) $_POST['search_multi_categories'],
                            'Search_multicat_levels' => (int) $_POST['search_multicat_levels'],
                            'Status' => $_POST['status']
                        ),
                        'where' => array(
                            'Key' => $p_key
                        )
                    );
                    $update_cache_key = $p_key;
                    $rlHook->load('apPhpListingTypesBeforeEdit');
                    $action = $GLOBALS['rlActions']->updateOne($update_date, 'listing_types');
                    $rlHook->load('apPhpListingTypesAfterEdit');
                    $page_id   = $rlDb->getOne('ID', "`Key` = 'lt_{$p_key}'", 'pages');
                    $cat_block = array(
                        'fields' => array(
                            'Page_ID' => implode(',', $_POST['ablock_pages']),
                            'Side' => $_POST['ablock_position']
                        ),
                        'where' => array(
                            'Key' => 'ltcb_' . $p_key
                        )
                    );
                    $rlActions->updateOne($cat_block, 'blocks');
                    if ($_POST['status'] != $type_info['Status']) {
                        $rlListingTypes->activateComponents($p_key, $_POST['status']);
                    }
                    if ($type_info['Page'] && !(int) $_POST['page']) {
                        $suspend_page = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'lt_' . $p_key
                            )
                        );
                        $rlActions->updateOne($suspend_page, 'pages');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'pages+name+lt_' . $p_key
                            )
                        );
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'pages+title+lt_' . $p_key
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                        if (!$rlDb->getOne('ID', "`Key` = '{$f_key}_listings'", 'categories')) {
                            $category_insert = array(
                                'Position' => 1,
                                'Path' => $rlValid->str2path($f_key) . '-listings',
                                'Level' => 0,
                                'Tree' => 1,
                                'Parent_ID' => 0,
                                'Type' => $f_key,
                                'Key' => $f_key . '_listings',
                                'Status' => 'active'
                            );
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'categories+name+' . $f_key . '_listings',
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                        }
                    } else if (!$type_info['Page'] && (int) $_POST['page']) {
                        if (!$rlDb->getOne('ID', "`Key` = 'lt_{$p_key}'", 'pages')) {
                            $page_position   = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "pages`");
                            $individual_page = array(
                                'Parent_ID' => 0,
                                'Page_type' => 'system',
                                'Login' => 0,
                                'Key' => 'lt_' . $p_key,
                                'Position' => $page_position['max'] + 1,
                                'Path' => $rlValid->str2path($f_key),
                                'Controller' => 'listing_type',
                                'Tpl' => 1,
                                'Menus' => 1,
                                'Modified' => 'NOW()',
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($individual_page, 'pages');
                            $page_id = mysql_insert_id();
                            $rlActions->insertOne($category_insert, 'categories');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+name+lt_' . $p_key,
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+title+lt_' . $p_key,
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_page = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'lt_' . $p_key
                                )
                            );
                            $rlActions->updateOne($activate_page, 'pages');
                            $page_id            = $rlDb->getOne('ID', "`Key` = 'lt_{$p_key}'", 'pages');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'pages+name+lt_' . $p_key
                                )
                            );
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'pages+title+lt_' . $p_key
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    }
                    if ($type_info['Admin_only'] != (int) $_POST['admin']) {
                        $rlListingTypes->adminOnly($p_key, (int) $_POST['admin'] ? 'trash' : 'active');
                    }
                    if (!$type_info['Admin_only'] && (int) $_POST['admin']) {
                        $suspend_page = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'my_' . $p_key
                            )
                        );
                        $rlActions->updateOne($suspend_page, 'pages');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'pages+name+my_' . $p_key
                            )
                        );
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'pages+title+my_' . $p_key
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                    } else if ($type_info['Admin_only'] && !(int) $_POST['admin']) {
                        if (!$rlDb->getOne('ID', "`Key` = 'my_{$p_key}'", 'pages')) {
                            $page_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "pages`");
                            $my_page       = array(
                                'Parent_ID' => 0,
                                'Page_type' => 'system',
                                'Login' => 1,
                                'Key' => 'my_' . $p_key,
                                'Position' => $page_position['max'] + 1,
                                'Path' => 'my-' . $rlValid->str2path($f_key),
                                'Controller' => 'my_listings',
                                'Tpl' => 1,
                                'Menus' => 2,
                                'Modified' => 'NOW()',
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($my_page, 'pages');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+name+my_' . $p_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['my_listings_pattern'])
                                );
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'pages+title+my_' . $p_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['my_listings_pattern'])
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_page = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'my_' . $p_key
                                )
                            );
                            $rlActions->updateOne($activate_page, 'pages');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'pages+name+my_' . $p_key
                                )
                            );
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'pages+title+my_' . $p_key
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    }
                    if (empty($type_info['Ablock_pages']) && !empty($_POST['ablock_pages'])) {
                        if (!$rlDb->getOne('ID', "`Key` = 'ltcb_{$p_key}'", 'blocks')) {
                            $cat_block_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $category_block     = array(
                                'Page_ID' => implode(',', $_POST['ablock_pages']),
                                'Sticky' => 0,
                                'Key' => 'ltcb_' . $p_key,
                                'Position' => $cat_block_position['max'] + 1,
                                'Side' => $_POST['ablock_position'],
                                'Type' => 'smarty',
                                'Content' => $p_key,
                                'Tpl' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($category_block, 'blocks');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+ltcb_' . $p_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['categories_block_pattern'])
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_block = array(
                                'fields' => array(
                                    'Status' => 'active',
                                    'Page_ID' => implode(',', $_POST['ablock_pages'])
                                ),
                                'where' => array(
                                    'Key' => 'ltcb_' . $p_key
                                )
                            );
                            $rlActions->updateOne($activate_block, 'blocks');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'blocks+name+ltcb_' . $p_key
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    } elseif (!empty($type_info['Ablock_pages']) && empty($_POST['ablock_pages']) && !in_array($_POST['ablock_position'], array(
                        'top',
                        'bottom'
                    ))) {
                        $suspend_block = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'ltcb_' . $p_key
                            )
                        );
                        $rlActions->updateOne($suspend_block, 'blocks');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'blocks+name+ltcb_' . $p_key
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                    }
                    if ($type_info['Ablock_pages'] != implode(',', $_POST['ablock_pages'])) {
                        $update_block = array(
                            'fields' => array(
                                'Page_ID' => implode(',', $_POST['ablock_pages'])
                            ),
                            'where' => array(
                                'Key' => 'ltcb_' . $p_key
                            )
                        );
                        $rlActions->updateOne($update_block, 'blocks');
                    }
                    if ($type_info['Featured_blocks'] && !(int) $_POST['featured_blocks']) {
                        $suspend_block = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'ltfb_' . $p_key
                            )
                        );
                        $rlActions->updateOne($suspend_block, 'blocks');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'blocks+name+ltfb_' . $p_key
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                    } elseif (!$type_info['Featured_blocks'] && (int) $_POST['featured_blocks']) {
                        if (!$rlDb->getOne('ID', "`Key` = 'ltfb_{$p_key}'", 'blocks')) {
                            $cat_block_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $category_block     = array(
                                'Page_ID' => $page_id,
                                'Sticky' => 0,
                                'Key' => 'ltfb_' . $p_key,
                                'Position' => $cat_block_position['max'] + 1,
                                'Side' => $_POST['ablock_position'],
                                'Type' => 'smarty',
                                'Content' => '{include file=\'blocks\'|cat:$smarty.const.RL_DS|cat:\'featured.tpl\' listings=$featured_' . $p_key . ' type=\'' . $p_key . '\'}',
                                'Tpl' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($category_block, 'blocks');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+ltfb_' . $p_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['featured_block_pattern'])
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_block = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'ltfb_' . $p_key
                                )
                            );
                            $rlActions->updateOne($activate_block, 'blocks');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'blocks+name+ltfb_' . $p_key
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    }
                    if (($type_info['Page'] && !(int) $_POST['page']) || ($type_info['Search'] && !(int) $_POST['search_form'])) {
                        $suspend_block = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'ltsb_' . $p_key
                            )
                        );
                        $rlActions->updateOne($suspend_block, 'blocks');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'blocks+name+ltsb_' . $p_key
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                    } elseif ((!$type_info['Page'] && (int) $_POST['page']) || (!$type_info['Search'] && (int) $_POST['search_form'])) {
                        if (!$rlDb->getOne('ID', "`Key` = 'ltsb_{$p_key}'", 'blocks')) {
                            $block_position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $search_block   = array(
                                'Page_ID' => $page_id,
                                'Sticky' => 0,
                                'Key' => 'ltsb_' . $p_key,
                                'Position' => $block_position['max'] + 1,
                                'Side' => 'left',
                                'Type' => 'smarty',
                                'Content' => '{include file=$refine_block_controller}',
                                'Tpl' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($search_block, 'blocks');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+ltsb_' . $p_key,
                                    'Value' => str_replace('{type}', $f_name[$allLangs[$key]['Code']], $lang['refine_search_pattern'])
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_block = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'ltsb_' . $p_key
                                )
                            );
                            $rlActions->updateOne($activate_block, 'blocks');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'blocks+name+ltsb_' . $p_key
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    }
                    if ($type_info['Search'] && !(int) $_POST['search_form']) {
                        $suspend_form = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => $p_key . '_quick'
                            )
                        );
                        $rlActions->updateOne($suspend_form, 'search_forms');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'search_forms+name+' . $p_key . '_quick'
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                    } elseif (!$type_info['Search'] && (int) $_POST['search_form']) {
                        if (!$rlDb->getOne('ID', "`Key` = '{$p_key}_quick'", 'search_forms')) {
                            $search_form = array(
                                'Key' => $f_key . '_quick',
                                'Type' => $f_key,
                                'Mode' => 'quick',
                                'Groups' => 0,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($search_form, 'search_forms');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'search_forms+name+' . $f_key . '_quick',
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_form = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => $p_key . '_quick'
                                )
                            );
                            $rlActions->updateOne($activate_form, 'search_forms');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'search_forms+name+' . $p_key . '_quick'
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    }
                    if ($type_info['Advanced_search'] && !(int) $_POST['advanced_search']) {
                        $suspend_form = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => $p_key . '_advanced'
                            )
                        );
                        $rlActions->updateOne($suspend_form, 'search_forms');
                        $suspend_phrases[] = array(
                            'fields' => array(
                                'Status' => 'trash'
                            ),
                            'where' => array(
                                'Key' => 'search_forms+name+' . $p_key . '_advanced'
                            )
                        );
                        $rlActions->update($suspend_phrases, 'lang_keys');
                    } elseif (!$type_info['Advanced_search'] && (int) $_POST['advanced_search']) {
                        if (!$rlDb->getOne('ID', "`Key` = '{$p_key}_advanced'", 'search_forms')) {
                            $advanced_form = array(
                                'Key' => $f_key . '_advanced',
                                'Type' => $f_key,
                                'Mode' => 'advanced',
                                'Groups' => 1,
                                'Status' => 'active',
                                'Readonly' => 1
                            );
                            $rlActions->insertOne($advanced_form, 'search_forms');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'search_forms+name+' . $f_key . '_advanced',
                                    'Value' => $f_name[$allLangs[$key]['Code']]
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $activate_form = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => $p_key . '_advanced'
                                )
                            );
                            $rlActions->updateOne($activate_form, 'search_forms');
                            $activate_phrases[] = array(
                                'fields' => array(
                                    'Status' => 'active'
                                ),
                                'where' => array(
                                    'Key' => 'search_forms+name+' . $p_key . '_advanced'
                                )
                            );
                            $rlActions->update($activate_phrases, 'lang_keys');
                        }
                    }
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'listing_types+name+{$p_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_phrases = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'listing_types+name+' . $p_key
                                )
                            );
                            $rlActions->updateOne($update_phrases, 'lang_keys');
                        } else {
                            $insert_phrases = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'listing_types+name+' . $p_key,
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_phrases, 'lang_keys');
                        }
                    }
                    $rlListingTypes->arrange($_POST['arrange_field']);
                    $message = $lang['listing_type_edited'];
                    $aUrl    = array(
                        "controller" => $controller
                    );
                }
                if ($action) {
                    $rlListingTypes->get();
                    $rlCache->updateListingStatistics($update_cache_key);
                    $rlCache->updateCategories();
                    $rlCache->updateSearchForms();
                    $rlCache->updateSearchFields();
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                }
            }
        }
    }
    $rlHook->load('apPhpListingTypesBottom');
}