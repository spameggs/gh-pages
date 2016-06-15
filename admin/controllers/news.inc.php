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
        $rlHook->load('apExtNewsUpdate');
        $rlActions->updateOne($updateData, 'news');
        exit;
    }
    $limit   = $rlValid->xSql($_GET['limit']);
    $start   = $rlValid->xSql($_GET['start']);
    $sort    = $rlValid->xSql($_GET['sort']);
    $sortDir = $rlValid->xSql($_GET['dir']);
    $sql     = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T1`.`ID` AS `Key`, `T2`.`Value` AS `title` ";
    $sql .= "FROM `" . RL_DBPREFIX . "news` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('news+title+',`T1`.`ID`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "WHERE `T1`.`Status` <> 'trash' ";
    if ($sort) {
        $sortField = $sort == 'title' ? "`T2`.`Value`" : "`T1`.`{$sort}`";
        $sql .= "ORDER BY {$sortField} {$sortDir} ";
    }
    $sql .= "LIMIT {$start}, {$limit}";
    $rlHook->load('apExtNewsSql');
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $rlHook->load('apExtNewsData');
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $rlHook->load('apPhpNewsTop');
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_news'] : $lang['edit_news'];
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit') {
            $id = (int) $_GET['news'];
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $news_info       = $rlDb->fetch('*', array(
                'ID' => $id
            ), "AND `Status` <> 'trash'", 1, 'news', 'row');
            $_POST['status'] = $news_info['Status'];
            $_POST['path']   = $news_info['Path'];
            $_POST['date']   = $news_info['Date'];
            $e_titles        = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'news+title+' . $id
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($e_titles as $nKey => $nVal) {
                $_POST['name'][$e_titles[$nKey]['Code']] = $e_titles[$nKey]['Value'];
            }
            $meta_description = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'news+meta_description+' . $id
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_description as $tKey => $tVal) {
                $_POST['meta_description'][$meta_description[$tKey]['Code']] = $meta_description[$tKey]['Value'];
            }
            $meta_keywords = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'news+meta_keywords+' . $id
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($meta_keywords as $tKey => $tVal) {
                $_POST['meta_keywords'][$meta_keywords[$tKey]['Code']] = $meta_keywords[$tKey]['Value'];
            }
            $e_content = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'news+content+' . $id
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($e_content as $nKey => $nVal) {
                $_POST['content_' . $e_content[$nKey]['Code']] = $e_content[$nKey]['Value'];
            }
            $rlHook->load('apPhpNewsPost');
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_title = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_title[$allLangs[$lkey]['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['title'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$lval['Code']}]";
                }
            }
            foreach ($allLangs as $lkey => $lval) {
                if (empty($_POST['content_' . $allLangs[$lkey]['Code']])) {
                    $errors[] = str_replace('{field}', "<b>" . $lang['content'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                } else {
                    $f_content[$allLangs[$lkey]['Code']] = $_POST['content_' . $allLangs[$lkey]['Code']];
                }
            }
            $f_path = $_POST['path'];
            if (!utf8_is_ascii($f_path)) {
                $f_path = utf8_to_ascii($f_path);
            }
            $f_path = $rlValid->str2path($f_path);
            if (strlen($f_path) < 3) {
                $errors[]       = $lang['incorrect_page_address'];
                $error_fields[] = 'path';
            }
            $exist_path = $rlDb->getOne('ID', "`Path` = '{$f_path}' AND `ID` <> '{$id}'", 'news');
            if ($exist_path) {
                $errors[] = str_replace('{path}', "<b>{$f_path}</b>", $lang['notice_page_path_exist']);
            }
            $rlHook->load('apPhpNewsValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Status' => $_POST['status'],
                        'Path' => $f_path,
                        'Date' => 'NOW()'
                    );
                    $rlHook->load('apPhpNewsBeforeAdd');
                    if ($action = $rlActions->insertOne($data, 'news')) {
                        $news_id = mysql_insert_id();
                        $rlHook->load('apPhpNewsAfterAdd');
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'news+title+' . $news_id,
                                'Value' => $f_title[$allLangs[$key]['Code']]
                            );
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'news+content+' . $news_id,
                                'Value' => $f_content[$allLangs[$key]['Code']]
                            );
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'news+meta_description+' . $news_id,
                                'Value' => $_POST['meta_description'][$allLangs[$key]['Code']]
                            );
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'news+meta_keywords+' . $news_id,
                                'Value' => $_POST['meta_keywords'][$allLangs[$key]['Code']]
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $message = $lang['news_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new news article (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new news article (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_date = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Date' => $_POST['date'],
                            'Path' => $f_path
                        ),
                        'where' => array(
                            'ID' => $id
                        )
                    );
                    $rlHook->load('apPhpNewsBeforeEdit');
                    $action = $GLOBALS['rlActions']->updateOne($update_date, 'news');
                    $rlHook->load('apPhpNewsAfterEdit');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'news+title+{$id}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $lang_phrase[] = array(
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'news+title+' . $id
                                ),
                                'fields' => array(
                                    'Value' => $f_title[$allLangs[$key]['Code']]
                                )
                            );
                        } else {
                            $insert_title = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'news+title+' . $id,
                                'Value' => $f_title[$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_title, 'lang_keys');
                        }
                        if ($rlDb->getOne('ID', "`Key` = 'news+content+{$id}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $lang_phrase[] = array(
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'news+content+' . $id
                                ),
                                'fields' => array(
                                    'Value' => $f_content[$allLangs[$key]['Code']]
                                )
                            );
                        } else {
                            $insert_contents = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'news+content+' . $id,
                                'Value' => $f_content[$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_contents, 'lang_keys');
                        }
                        $exist_meta_description = $rlDb->fetch(array(
                            'ID'
                        ), array(
                            'Key' => 'news+meta_description+' . $id,
                            'Code' => $allLangs[$key]['Code']
                        ), null, null, 'lang_keys', 'row');
                        if (!empty($exist_meta_description)) {
                            $lang_keys_meta_description['where']  = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'news+meta_description+' . $id
                            );
                            $lang_keys_meta_description['fields'] = array(
                                'Value' => $_POST['meta_description'][$allLangs[$key]['Code']]
                            );
                            $GLOBALS['rlActions']->updateOne($lang_keys_meta_description, 'lang_keys');
                        } else {
                            $lang_keys_meta_description = array(
                                'Value' => $_POST['meta_description'][$allLangs[$key]['Code']],
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'news+meta_description+' . $id
                            );
                            $GLOBALS['rlActions']->insertOne($lang_keys_meta_description, 'lang_keys');
                        }
                        $exist_meta_keywords = $rlDb->fetch(array(
                            'ID'
                        ), array(
                            'Key' => 'news+meta_keywords+' . $id,
                            'Code' => $allLangs[$key]['Code']
                        ), null, null, 'lang_keys', 'row');
                        if (!empty($exist_meta_keywords)) {
                            $exist_meta_keywords['where']  = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'news+meta_keywords+' . $id
                            );
                            $exist_meta_keywords['fields'] = array(
                                'Value' => $_POST['meta_keywords'][$allLangs[$key]['Code']]
                            );
                            $GLOBALS['rlActions']->updateOne($exist_meta_keywords, 'lang_keys');
                        } else {
                            $exist_meta_keywords = array(
                                'Value' => $_POST['meta_keywords'][$allLangs[$key]['Code']],
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'news+meta_keywords+' . $id
                            );
                            $GLOBALS['rlActions']->insertOne($exist_meta_keywords, 'lang_keys');
                        }
                    }
                    $rlActions->update($lang_phrase, 'lang_keys');
                    $message = $lang['news_edited'];
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
    }
    $reefless->loadClass('Categories');
    $rlXajax->registerFunction(array(
        'deleteNews',
        $rlAdmin,
        'ajaxDeleteNews'
    ));
    $rlHook->load('apPhpNewsBottom');
}