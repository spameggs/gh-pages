<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $id         = $rlValid->xSql($_GET['id']);
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'tag_cloud');
        exit;
    }
    $limit  = $rlValid->xSql($_GET['limit']);
    $start  = $rlValid->xSql($_GET['start']);
    $status = $rlValid->xSql($_GET['status']);
    $sql    = "SELECT SQL_CALC_FOUND_ROWS DISTINCT *, `Tag` AS `name` FROM `" . RL_DBPREFIX . "tag_cloud` ";
    $sql .= "WHERE `Status` <> 'trash'  ";
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "tag_cloud`");
    if ($_GET['action'] == 'search') {
        $search_fields = array(
            'Name',
            'Status'
        );
        foreach ($search_fields as $item) {
            if ($_GET[$item] != '') {
                $s_value = $rlValid->xSql($_GET[$item]);
                switch ($item) {
                    case 'Name':
                        $sql .= "AND `Tag` LIKE '%{$s_value}%' ";
                        break;
                    default:
                        $sql .= "AND `{$item}` = '{$s_value}' ";
                        break;
                }
            }
        }
    }
    $sql .= "LIMIT {$start},{$limit}";
    $data = $rlDb->getAll($sql);
    $reefless->loadClass('Json');
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    if ($_GET['action']) {
        if ($_GET['action'] == 'add') {
            $bcAStep = $lang['add'];
        } elseif ($_GET['action'] == 'edit') {
            $bcAStep = $lang['edit'];
        } elseif ($_GET['action'] == 'defaults') {
            $bcAStep = $lang['tc_defaults'];
        }
    }
    $reefless->loadClass('TagCloud', null, 'tag_cloud');
    if ($_GET['action'] == 'defaults') {
        $allLangs =& $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if (!$_POST['fromPost']) {
            $t_key    = 'tags_defaults';
            $t_titles = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+title+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($t_titles as $nKey => $nVal) {
                $_POST['title'][$t_titles[$nKey]['Code']] = $t_titles[$nKey]['Value'];
            }
            $descriptions = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+des+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($descriptions as $nKey => $nVal) {
                $_POST['description_' . $descriptions[$nKey]['Code']] = $descriptions[$nKey]['Value'];
            }
            $meta_description = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+meta_description+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_description as $nKey => $nVal) {
                $_POST['meta_description'][$meta_description[$nKey]['Code']] = $meta_description[$nKey]['Value'];
            }
            $meta_keywords = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+meta_keywords+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_keywords as $nKey => $nVal) {
                $_POST['meta_keywords'][$meta_keywords[$nKey]['Code']] = $meta_keywords[$nKey]['Value'];
            }
        }
        if (isset($_POST['submit'])) {
            $f_key = 'tags_defaults';
            foreach ($allLangs as $key => $value) {
                if ($rlDb->getOne('ID', "`Key` = 'tag_cloud+title+{$f_key}' AND `Code` = '{$value['Code']}'", 'lang_keys')) {
                    $lang_keys_name[] = array(
                        'where' => array(
                            'Code' => $value['Code'],
                            'Key' => 'tag_cloud+title+' . $f_key
                        ),
                        'fields' => array(
                            'Value' => $_POST['title'][$value['Code']]
                        )
                    );
                } else {
                    $insert_phrases = array(
                        'Code' => $value['Code'],
                        'Module' => 'common',
                        'Plugin' => 'tag_cloud',
                        'Key' => 'tag_cloud+title+' . $f_key,
                        'Value' => $_POST['title'][$value['Code']]
                    );
                    $rlActions->insertOne($insert_phrases, 'lang_keys');
                }
                if (!empty($_POST['description_' . $allLangs[$key]['Code']])) {
                    $c_description = $rlDb->fetch(array(
                        'ID'
                    ), array(
                        'Key' => 'tag_cloud+des+' . $f_key,
                        'Code' => $allLangs[$key]['Code']
                    ), null, null, 'lang_keys', 'row');
                    if (!empty($c_description)) {
                        $lang_keys_name[] = array(
                            'where' => array(
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'tag_cloud+des+' . $f_key
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
                            'Key' => 'tag_cloud+des+' . $f_key,
                            'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                        );
                        $rlActions->insertOne($lang_keys_des, 'lang_keys');
                    }
                } else {
                    $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'tag_cloud+des+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                }
                if (!empty($_POST['meta_description'][$allLangs[$key]['Code']])) {
                    $meta_description = $rlDb->fetch(array(
                        'ID'
                    ), array(
                        'Key' => 'tag_cloud+meta_description+' . $f_key,
                        'Code' => $allLangs[$key]['Code']
                    ), null, null, 'lang_keys', 'row');
                    if (!empty($meta_description)) {
                        $lang_keys_name[] = array(
                            'where' => array(
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'tag_cloud+meta_description+' . $f_key
                            ),
                            'fields' => array(
                                'value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                            )
                        );
                    } else {
                        $lang_keys_des = array(
                            'Code' => $allLangs[$key]['Code'],
                            'Module' => 'common',
                            'Plugin' => 'tag_cloud',
                            'Status' => 'active',
                            'Key' => 'tag_cloud+meta_description+' . $f_key,
                            'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                        );
                        $rlActions->insertOne($lang_keys_des, 'lang_keys');
                    }
                } else {
                    $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'tag_cloud+meta_description+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                }
                if (!empty($_POST['meta_keywords'][$allLangs[$key]['Code']])) {
                    $meta_keywords = $rlDb->fetch(array(
                        'ID'
                    ), array(
                        'Key' => 'tag_cloud+meta_keywords+' . $f_key,
                        'Code' => $allLangs[$key]['Code']
                    ), null, null, 'lang_keys', 'row');
                    if (!empty($meta_keywords)) {
                        $lang_keys_name[] = array(
                            'where' => array(
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'tag_cloud+meta_keywords+' . $f_key
                            ),
                            'fields' => array(
                                'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                            )
                        );
                    } else {
                        $lang_keys_des = array(
                            'Code' => $allLangs[$key]['Code'],
                            'Module' => 'common',
                            'Plugin' => 'tag_cloud',
                            'Status' => 'active',
                            'Key' => 'tag_cloud+meta_keywords+' . $f_key,
                            'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                        );
                        $rlActions->insertOne($lang_keys_des, 'lang_keys');
                    }
                } else {
                    $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'tag_cloud+meta_keywords+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                }
            }
            if ($GLOBALS['rlActions']->update($lang_keys_name, 'lang_keys')) {
                $aUrl = array(
                    "controller" => $controller
                );
                $rlTagCloud->updateBox();
                $reefless->loadClass('Notice');
                $rlNotice->saveNotice($lang['tc_defaults_updated']);
                $reefless->redirect($aUrl);
            }
        }
    } elseif ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs =& $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit') {
            $t_key    = $rlValid->xSql($_GET['key']);
            $tag_info = $rlDb->fetch('*', array(
                'Key' => $t_key
            ), null, null, 'tag_cloud', 'row');
            if (!$tag_info) {
                $sError = true;
            }
        }
        if ($_POST['type']) {
            $listing_type = $rlListingTypes->types[$_POST['type']];
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['key']    = $tag_info['Key'];
            $_POST['path']   = $tag_info['Path'];
            $_POST['status'] = $tag_info['Status'];
            $_POST['tag']    = $tag_info['Tag'];
            $t_titles        = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+title+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($t_titles as $nKey => $nVal) {
                $_POST['title'][$t_titles[$nKey]['Code']] = $t_titles[$nKey]['Value'];
            }
            $descriptions = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+des+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($descriptions as $nKey => $nVal) {
                $_POST['description_' . $descriptions[$nKey]['Code']] = $descriptions[$nKey]['Value'];
            }
            $meta_description = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+meta_description+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_description as $nKey => $nVal) {
                $_POST['meta_description'][$meta_description[$nKey]['Code']] = $meta_description[$nKey]['Value'];
            }
            $meta_keywords = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'tag_cloud+meta_keywords+' . $t_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_keywords as $nKey => $nVal) {
                $_POST['meta_keywords'][$meta_keywords[$nKey]['Code']] = $meta_keywords[$nKey]['Value'];
            }
        }
        if ($_REQUEST['type'] || $_POST['type']) {
            $parent_cats_list = $rlCategories->getCatTitles($_REQUEST['type'] ? $_REQUEST['type'] : $_POST['type']);
            $rlSmarty->assign_by_ref('type', $_POST['type']);
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_key = $_POST['key'] ? $_POST['key'] : $_POST['tag'];
            if (!utf8_is_ascii($f_key)) {
                $f_key = utf8_to_ascii($f_key);
            }
            $f_key = $rlValid->str2key($f_key);
            if ($_GET['action'] == 'add') {
                if (strlen($f_key) < $config['tc_tag_min_length']) {
                    $errors[]       = $lang['incorrect_phrase_key'];
                    $error_fields[] = 'key';
                }
                $exist_key = $rlDb->fetch(array(
                    'Key',
                    'Status'
                ), array(
                    'Key' => $f_key
                ), null, 1, 'tag_cloud', 'row');
                if (!empty($exist_key)) {
                    $exist_error    = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['tc_notice_tag_exist']);
                    $error_fields[] = 'key';
                    $errors[]       = $exist_error;
                }
            }
            $orig_key = $f_key;
            if ($_GET['action'] == 'add') {
                $f_key = $rlValid->str2key($f_key);
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
                $errors[]       = str_replace('[path]', "<b>" . $f_path . "</b>", $lang['tc_notice_path_exist']);
                $error_fields[] = 'path';
            }
            preg_match('/\-[0-9]+$/', $f_path, $matches);
            if (!empty($matches)) {
                $errors[]       = $lang['tc_url_listing_logic'];
                $error_fields[] = "path";
            }
            $f_title = $_POST['title'];
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Key' => $f_key,
                        'Path' => $f_path,
                        'Tag' => $_POST['tag'],
                        'Status' => $_POST['status'],
                        'Type' => $_POST['type'],
                        'Count' => 0,
                        'Modified' => 'NOW()',
                        'Date' => 'NOW()'
                    );
                    if ($action = $rlActions->insertOne($data, 'tag_cloud')) {
                        foreach ($allLangs as $key => $value) {
                            if (!empty($f_title[$value['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+title+' . $f_key,
                                    'Value' => trim($f_title[$value['Code']])
                                );
                            }
                            if (!empty($_POST['description_' . $allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+des+' . $f_key,
                                    'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['meta_description'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+meta_description+' . $f_key,
                                    'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                                );
                            }
                            if (!empty($_POST['meta_keywords'][$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+meta_keywords+' . $f_key,
                                    'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                                );
                            }
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $message = $lang['tc_tag_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_data = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Tag' => $_POST['tag'],
                            'Path' => $f_path,
                            'Type' => $_POST['type'],
                            'Modified' => 'NOW()'
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    $action      = $rlActions->updateOne($update_data, 'tag_cloud');
                    $category_id = $rlDb->getOne("ID", "`Key` = '{$f_key}'", 'tag_cloud');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'tag_cloud+title+{$f_key}' AND `Code` = '{$value['Code']}'", 'lang_keys')) {
                            $lang_keys_name[] = array(
                                'where' => array(
                                    'Code' => $value['Code'],
                                    'Key' => 'tag_cloud+title+' . $f_key
                                ),
                                'fields' => array(
                                    'Value' => $_POST['title'][$value['Code']]
                                )
                            );
                        } else {
                            $insert_phrases = array(
                                'Code' => $value['Code'],
                                'Module' => 'common',
                                'Plugin' => 'tag_cloud',
                                'Key' => 'tag_cloud+title+' . $f_key,
                                'Value' => $_POST['title'][$value['Code']]
                            );
                            $rlActions->insertOne($insert_phrases, 'lang_keys');
                        }
                        if (!empty($_POST['description_' . $allLangs[$key]['Code']])) {
                            $c_description = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'tag_cloud+des+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($c_description)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'tag_cloud+des+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+des+' . $f_key,
                                    'Value' => trim($_POST['description_' . $allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'tag_cloud+des+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['meta_description'][$allLangs[$key]['Code']])) {
                            $meta_description = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'tag_cloud+meta_description+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($meta_description)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'tag_cloud+meta_description+' . $f_key
                                    ),
                                    'fields' => array(
                                        'value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+meta_description+' . $f_key,
                                    'Value' => trim($_POST['meta_description'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'tag_cloud+meta_description+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                        }
                        if (!empty($_POST['meta_keywords'][$allLangs[$key]['Code']])) {
                            $meta_keywords = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'tag_cloud+meta_keywords+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($meta_keywords)) {
                                $lang_keys_name[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'tag_cloud+meta_keywords+' . $f_key
                                    ),
                                    'fields' => array(
                                        'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                                    )
                                );
                            } else {
                                $lang_keys_des = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'tag_cloud',
                                    'Status' => 'active',
                                    'Key' => 'tag_cloud+meta_keywords+' . $f_key,
                                    'Value' => trim($_POST['meta_keywords'][$allLangs[$key]['Code']])
                                );
                                $rlActions->insertOne($lang_keys_des, 'lang_keys');
                            }
                        } else {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'tag_cloud+meta_keywords+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
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
                    $rlTagCloud->updateBox();
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                }
            }
        }
    }
    $rlXajax->registerFunction(array(
        'deleteTag',
        $rlTagCloud,
        'ajaxDeleteTag'
    ));
    $rlXajax->registerFunction(array(
        'importTags',
        $rlTagCloud,
        'ajaxImportTags'
    ));
    $tags_page_path = $rlDb->getOne("Path", "`Key` = 'tags'", "pages");
    $rlSmarty->assign('tags_page_path', $tags_page_path);
}