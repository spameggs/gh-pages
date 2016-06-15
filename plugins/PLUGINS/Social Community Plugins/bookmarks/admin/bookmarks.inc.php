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
        $id         = (int) $_GET['id'];
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $bookmark   = $rlDb->fetch(array(
            'Services',
            'Align',
            'Color',
            'Type',
            'Key'
        ), array(
            'ID' => $id
        ), null, 1, 'bookmarks', 'row');
        $content    = '{include file=$smarty.const.RL_PLUGINS|cat:$smarty.const.RL_DS|cat:"bookmarks"|cat:$smarty.const.RL_DS|cat:"buttons.tpl" type="' . $bookmark['Type'] . '" align="' . $value . '" color="' . $bookmark['Color'] . '" services="' . $bookmark['Services'] . '"}';
        switch ($field) {
            case 'Align':
                $updateBlock = array(
                    'fields' => array(
                        'Content' => $content
                    ),
                    'where' => array(
                        'Key' => 'bookmark_' . $bookmark['Key']
                    )
                );
                $rlActions->updateOne($updateBlock, 'blocks');
                break;
            case 'Status':
            case 'Tpl':
                $updateBlock = array(
                    'fields' => array(
                        $field => $value
                    ),
                    'where' => array(
                        'Key' => 'bookmark_' . $bookmark['Key']
                    )
                );
                $rlActions->updateOne($updateBlock, 'blocks');
                break;
        }
        $rlActions->updateOne($updateData, 'blocks');
        exit;
    }
    $reefless->loadClass('Bookmarks', null, 'bookmarks');
    $limit    = $rlValid->xSql($_GET['limit']);
    $start    = $rlValid->xSql($_GET['start']);
    $langCode = $rlValid->xSql($_GET['lang_code']);
    $phrase   = $rlValid->xSql($_GET['phrase']);
    $sql      = "SELECT SQL_CALC_FOUND_ROWS `T2`.`Key`, `T2`.`Status`, `T1`.`Type`, `T1`.`Align`, `T2`.`Tpl`, `T1`.`ID` ";
    $sql .= "FROM `" . RL_DBPREFIX . "bookmarks` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T2` ON CONCAT('bookmark_', `T1`.`Key`) = `T2`.`Key` ";
    $sql .= "ORDER BY `T2`.`ID` ASC ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Name']   = $lang['blocks+name+' . $value['Key']];
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
        $data[$key]['Type']   = $lang[$rlBookmarks->bookmarks[$data[$key]['Type']]['Name']];
        $data[$key]['Align']  = $lang['bookmark_' . $data[$key]['Align']];
        $data[$key]['Tpl']    = $data[$key]['Tpl'] ? $lang['yes'] : $lang['no'];
    }
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    require_once(RL_LIBS . 'system.lib.php');
    $reefless->loadClass('Bookmarks', null, 'bookmarks');
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $bcAStep = $_GET['action'] == 'add' ? $lang['bsh_add_bookmark_block'] : $lang['bsh_edit_bookmark_block'];
        $rlSmarty->assign_by_ref('bookmarks', $rlBookmarks->bookmarks);
        $rlSmarty->assign_by_ref('services', $rlBookmarks->services);
        $aligns = array(
            'left' => $lang['bookmark_left'],
            'center' => $lang['bookmark_center'],
            'right' => $lang['bookmark_right']
        );
        $rlSmarty->assign_by_ref('aligns', $aligns);
        $view_modes = array(
            'large' => $lang['bookmark_mode_large'],
            'medium' => $lang['bookmark_mode_medium'],
            'small' => $lang['bookmark_mode_small']
        );
        $rlSmarty->assign_by_ref('view_modes', $view_modes);
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
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
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $b_id      = (int) $_GET['item'];
            $block_sql = "SELECT `T1`.*, `T2`.`Tpl`, `T2`.`Side`, `T2`.`Sticky`, `T2`.`Page_ID` ";
            $block_sql .= "FROM `" . RL_DBPREFIX . "bookmarks` AS `T1` ";
            $block_sql .= "LEFT JOIN `" . RL_DBPREFIX . "blocks` AS `T2` ON CONCAT('bookmark_', `T1`.`Key`) = `T2`.`Key` ";
            $block_sql .= "WHERE `T1`.`Status` <> 'trash' AND `T1`.`ID` = '{$b_id}' LIMIT 1";
            $block_info = $rlDb->getRow($block_sql);
            $rlSmarty->assign_by_ref('block', $block_info);
            if ($block_info['Type'] == 'floating_bar' && $block_info['Side'] == 'middle_right') {
                $post_side = 'right';
            } elseif ($block_info['Type'] == 'floating_bar' && $block_info['Side'] == 'middle_left') {
                $post_side = 'left';
            } else {
                $post_side = $block_info['Side'];
            }
            $_POST['key']         = $block_info['Key'];
            $_POST['status']      = $block_info['Status'];
            $_POST['side']        = $post_side;
            $_POST['tpl']         = $block_info['Tpl'];
            $_POST['show_on_all'] = $block_info['Sticky'];
            $_POST['pages']       = explode(',', $block_info['Page_ID']);
            $_POST['type']        = $block_info['Type'];
            $_POST['align']       = $block_info['Align'];
            $_POST['color']       = $block_info['Color'];
            $_POST['view_mode']   = $block_info['View_mode'];
            $_POST['services']    = explode(',', $block_info['Services']);
            $names                = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'blocks+name+bookmark_' . $block_info['Key']
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($names as $nKey => $nVal) {
                $_POST['name'][$names[$nKey]['Code']] = $names[$nKey]['Value'];
            }
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            $f_key  = $_POST['key'];
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            if ($_GET['action'] == 'add') {
                if (!utf8_is_ascii($f_key)) {
                    $f_key = utf8_to_ascii($f_key);
                }
                if (strlen($f_key) < 2) {
                    $errors[] = $lang['incorrect_phrase_key'];
                }
                $exist_key = $rlDb->fetch(array(
                    'Key'
                ), array(
                    'Key' => $f_key
                ), null, null, 'blocks');
                if (!empty($exist_key)) {
                    $errors[] = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_block_exist']);
                }
            }
            $f_key  = $rlValid->str2key($f_key);
            $f_name = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$allLangs[$lkey]['Code']])) {
                    $errors[] = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                }
                $f_names[$allLangs[$lkey]['Code']] = $f_name[$allLangs[$lkey]['Code']];
            }
            $f_type = $_POST['type'];
            if (empty($f_type)) {
                $errors[] = str_replace('{field}', "<b>\"" . $lang['bsh_bookmark_type'] . "\"</b>", $lang['notice_select_empty']);
            }
            $f_services = $_POST['services'];
            if ($rlBookmarks->bookmarks[$f_type]['Services'] && empty($f_services)) {
                $errors[] = str_replace('{field}', "<b>\"" . $lang['bookmarks_services'] . "\"</b>", $lang['notice_select_empty']);
            }
            if ($f_type == 'floating_bar' && $_POST['side'] == 'left') {
                $f_side = 'middle_left';
            } else if ($f_type == 'floating_bar' && $_POST['side'] == 'right') {
                $f_side = 'middle_right';
            } else {
                $f_side = $_POST['side'];
            }
            if (empty($f_side)) {
                $errors[] = str_replace('{field}', "<b>\"" . $lang['block_side'] . "\"</b>", $lang['notice_select_empty']);
            }
            $tpl = $f_type == 'floating_bar' ? 0 : $_POST['tpl'];
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                    $data     = array(
                        'Key' => 'bookmark_' . $f_key,
                        'Status' => $_POST['status'],
                        'Position' => $position['max'] + 1,
                        'Side' => $f_side,
                        'Type' => 'smarty',
                        'Tpl' => $tpl,
                        'Page_ID' => implode(',', $_POST['pages']),
                        'Subcategories' => 1,
                        'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                        'Cat_sticky' => 1,
                        'Plugin' => 'bookmarks',
                        'Content' => '{include file=$smarty.const.RL_PLUGINS|cat:$smarty.const.RL_DS|cat:"bookmarks"|cat:$smarty.const.RL_DS|cat:"buttons.tpl" type="' . $f_type . '" align="' . $_POST['align'] . '" color="' . $_POST['color'] . '" view_mode="' . $_POST['view_mode'] . '" services="' . implode(',', $f_services) . '"}'
                    );
                    if ($action = $rlActions->insertOne($data, 'blocks')) {
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'blocks+name+bookmark_' . $f_key,
                                'Value' => $f_name[$allLangs[$key]['Code']],
                                'Plugin' => 'bookmarks'
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $bookmark_data = array(
                            'Key' => $f_key,
                            'Type' => $f_type,
                            'Services' => implode(',', $f_services),
                            'Align' => $_POST['align'],
                            'Color' => $_POST['color'],
                            'View_mode' => $_POST['view_mode'],
                            'Status' => $_POST['status']
                        );
                        $rlActions->insertOne($bookmark_data, 'bookmarks');
                        $message = $lang['bsh_block_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new bookmark block (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new bookmark block (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_data   = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Side' => $f_side,
                            'Tpl' => $tpl,
                            'Page_ID' => implode(',', $_POST['pages']),
                            'Sticky' => empty($_POST['show_on_all']) ? 0 : 1,
                            'Content' => '{include file=$smarty.const.RL_PLUGINS|cat:$smarty.const.RL_DS|cat:"bookmarks"|cat:$smarty.const.RL_DS|cat:"buttons.tpl" type="' . $f_type . '" align="' . $_POST['align'] . '" color="' . $_POST['color'] . '" view_mode="' . $_POST['view_mode'] . '" services="' . implode(',', $f_services) . '"}'
                        ),
                        'where' => array(
                            'Key' => 'bookmark_' . $f_key
                        )
                    );
                    $action        = $GLOBALS['rlActions']->updateOne($update_data, 'blocks');
                    $bookmark_data = array(
                        'fields' => array(
                            'Type' => $f_type,
                            'Services' => implode(',', $f_services),
                            'Align' => $_POST['align'],
                            'Color' => $_POST['color'],
                            'View_mode' => $_POST['view_mode'],
                            'Status' => $_POST['status']
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    $rlActions->updateOne($bookmark_data, 'bookmarks');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'blocks+name+bookmark_{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_names = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'blocks+name+bookmark_' . $f_key
                                )
                            );
                            $rlActions->updateOne($update_names, 'lang_keys');
                        } else {
                            $insert_names = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'blocks+name+bookmark_' . $f_key,
                                'plugin' => 'bookmarks',
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_names, 'lang_keys');
                        }
                    }
                    $message = $lang['bsh_block_edited'];
                    $aUrl    = array(
                        "controller" => $controller
                    );
                }
                if ($action) {
                    unset($_SESSION['categories']);
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                }
            }
        }
    }
    $rlXajax->registerFunction(array(
        'deleteBookmark',
        $rlBookmarks,
        'ajaxDeleteBookmark'
    ));
}