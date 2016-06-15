<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    $reefless->loadClass('Polls', null, 'polls');
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
        $rlActions->updateOne($updateData, 'polls');
        $languages = $rlLang->getLanguagesList();
        if ($field == 'Status') {
            $current_poll = $rlDb->getRow("SELECT `Random` FROM `" . RL_DBPREFIX . "polls` WHERE `ID` = {$id} LIMIT 1");
            $id           = $current_poll['Random'] == 1 ? 'all' : $id;
            $tmp_polls    = $rlPolls->get($id);
            if (!empty($tmp_polls)) {
                $polls          = serialize($tmp_polls);
                $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
            } else {
                $insert_content = $rlPolls->empty_content;
            }
            $polls_key = $current_poll['Random'] == 1 ? 'polls' : 'polls_' . $id;
            if (empty($tmp_polls) && $current_poll['Random'] == 1) {
                $update_data = array(
                    'fields' => array(
                        'Content' => $insert_content,
                        'Status' => $value
                    ),
                    'where' => array(
                        'Key' => $polls_key
                    )
                );
            } elseif (!empty($tmp_polls) && $current_poll['Random'] == 1) {
                $update_data = array(
                    'fields' => array(
                        'Content' => $insert_content,
                        'Status' => 'active'
                    ),
                    'where' => array(
                        'Key' => $polls_key
                    )
                );
            } else {
                $update_data = array(
                    'fields' => array(
                        'Content' => $insert_content,
                        'Status' => $value
                    ),
                    'where' => array(
                        'Key' => $polls_key
                    )
                );
            }
            $action = $GLOBALS['rlActions']->updateOne($update_data, 'blocks');
        }
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $sql   = "SELECT *, `ID` AS `Key` FROM `" . RL_DBPREFIX . "polls` WHERE `Status` <> 'trash' LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $data  = $rlLang->replaceLangKeys($data, 'polls', array(
        'name'
    ), RL_LANG_CODE, 'admin');
    $rlDb->resetTable();
    foreach ($data as $key => $value) {
        $data[$key]['Tpl']    = $data[$key]['Tpl'] ? $lang['yes'] : $lang['no'];
        $data[$key]['Random'] = $data[$key]['Random'] ? $lang['no'] : $lang['yes'];
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "polls` WHERE `Status` <> 'trash'");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    if ($_GET['action']) {
        switch ($_GET['action']) {
            case 'add':
                $bcAStep = $lang['add'];
                break;
            case 'edit':
                $bcAStep = $lang['edit'];
                break;
            case 'results':
                $bcAStep = $lang['view_results'];
                break;
        }
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $reefless->loadClass('Polls', null, 'polls');
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit') {
            $id        = (int) $_GET['poll'];
            $poll_info = $rlDb->fetch(array(
                'Status',
                'Side',
                'Tpl',
                'Random'
            ), array(
                'ID' => $id
            ), "AND `Status` <> 'trash'", 1, 'polls', 'row');
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['status'] = $poll_info['Status'];
            $_POST['side']   = $poll_info['Side'];
            $_POST['tpl']    = $poll_info['Tpl'];
            $_POST['random'] = $poll_info['Random'];
            $e_titles        = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'polls+name+' . $id
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($e_titles as $nKey => $nVal) {
                $_POST['name'][$e_titles[$nKey]['Code']] = $e_titles[$nKey]['Value'];
            }
            $rlDb->setTable('polls_items');
            $e_items = $rlDb->fetch(array(
                'ID',
                'Color'
            ), array(
                'Poll_ID' => $id
            ), "ORDER BY `ID`");
            $rlDb->resetTable();
            foreach ($e_items as $nKey => $nVal) {
                foreach ($allLangs as $lkey => $lval) {
                    $phrase                                                          = $rlDb->fetch(array(
                        'Value'
                    ), array(
                        'Key' => 'polls_items+name+' . $e_items[$nKey]['ID'],
                        'Code' => $allLangs[$lkey]['Code']
                    ), "AND `Status` <> 'trash'", null, 'lang_keys', 'row');
                    $_POST['items'][$e_items[$nKey]['ID']][$allLangs[$lkey]['Code']] = $phrase['Value'];
                }
                $_POST['color'][] = $e_items[$nKey]['Color'];
            }
        }
        if (isset($_POST['submit'])) {
            $errors  = array();
            $f_title = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_title[$allLangs[$lkey]['Code']])) {
                    $errors[] = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                }
            }
            $f_items  = $_POST['items'];
            $f_colors = $_POST['color'];
            if (!empty($f_items)) {
                foreach ($f_items as $key => $value) {
                    foreach ($allLangs as $lkey => $lval) {
                        if (empty($f_items[$key][$allLangs[$lkey]['Code']])) {
                            $errors[] = str_replace('{field}', "<b>" . $lang['vote_items'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                        }
                    }
                    break;
                }
            }
            $f_side = $_POST['side'];
            if (empty($f_side) && !$_POST['random']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['block_side'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'side';
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Side' => $f_side,
                        'Tpl' => $_POST['tpl'],
                        'Random' => $_POST['random'],
                        'Status' => $_POST['status'],
                        'Date' => 'NOW()'
                    );
                    if ($action = $rlActions->insertOne($data, 'polls')) {
                        $poll_id   = mysql_insert_id();
                        $polls_key = 'polls_' . $poll_id;
                        foreach ($f_items as $key => $val) {
                            $poll_item = array(
                                'Poll_ID' => $poll_id,
                                'Color' => $f_colors[$key - 1]
                            );
                            $rlActions->insertOne($poll_item, 'polls_items');
                            $items_id[$key] = mysql_insert_id();
                        }
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Plugin' => 'polls',
                                'Status' => 'active',
                                'Key' => 'polls+name+' . $poll_id,
                                'Value' => $f_title[$allLangs[$key]['Code']]
                            );
                            foreach ($f_items as $ikey => $ival) {
                                if (!empty($f_items[$ikey][$allLangs[$key]['Code']])) {
                                    $lang_keys[] = array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Module' => 'common',
                                        'Plugin' => 'polls',
                                        'Status' => 'active',
                                        'Key' => 'polls_items+name+' . $items_id[$ikey],
                                        'Value' => $f_items[$ikey][$allLangs[$key]['Code']]
                                    );
                                }
                            }
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        if (!$_POST['random']) {
                            $polls          = serialize($rlPolls->get($poll_id));
                            $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
                            $position       = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                            $data_block     = array(
                                'Sticky' => '1',
                                'Key' => $polls_key,
                                'Side' => $f_side,
                                'Type' => 'php',
                                'Position' => $position['max'] + 1,
                                'Content' => $insert_content,
                                'Tpl' => $_POST['tpl'],
                                'Plugin' => 'polls',
                                'Status' => $_POST['status'],
                                'Readonly' => '0'
                            );
                            $rlActions->insertOne($data_block, 'blocks');
                            foreach ($allLangs as $key => $value) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Plugin' => 'polls',
                                    'Status' => 'active',
                                    'Key' => 'blocks+name+' . $polls_key,
                                    'Value' => $f_title[$allLangs[$key]['Code']]
                                );
                            }
                            $rlActions->insert($lang_keys, 'lang_keys');
                        } else {
                            $polls          = serialize($rlPolls->get('all'));
                            $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
                            $update_block   = array(
                                'fields' => array(
                                    'Content' => $insert_content,
                                    'Status' => $_POST['status']
                                ),
                                'where' => array(
                                    'Key' => 'polls'
                                )
                            );
                            $action         = $GLOBALS['rlActions']->updateOne($update_block, 'blocks');
                        }
                        $message = $lang['item_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can not add new poll (MySQL problems)", E_WARNING);
                        $rlDebug->logger("Can not add new poll (MySQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $id          = (int) $_GET['poll'];
                    $polls_key   = 'polls_' . $id;
                    $update_date = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Side' => $f_side,
                            'Random' => $_POST['random'],
                            'Tpl' => $_POST['tpl']
                        ),
                        'where' => array(
                            'ID' => $id
                        )
                    );
                    $action      = $GLOBALS['rlActions']->updateOne($update_date, 'polls');
                    $rlDb->setTable('polls_items');
                    $items_id_tmp = $rlDb->fetch(array(
                        'ID'
                    ), array(
                        'Poll_ID' => $id
                    ), "ORDER BY `ID`");
                    $rlDb->resetTable();
                    foreach ($items_id_tmp as $key => $val) {
                        $items_id[$items_id_tmp[$key]['ID']] = $items_id_tmp[$key];
                        if (!isset($f_items[$items_id_tmp[$key]['ID']])) {
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "polls_items` WHERE `ID` = '{$items_id_tmp[$key]['ID']}'");
                            $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'polls_items+name+{$items_id_tmp[$key]['ID']}' AND `Plugin` = 'polls' ");
                        } else {
                            $rlDb->query("UPDATE `" . RL_DBPREFIX . "polls_items` SET `Color` = '{$f_colors[$key]}' WHERE `ID` = {$val['ID']}");
                        }
                    }
                    $i = 0;
                    foreach ($f_items as $key => $value) {
                        if (isset($items_id[$key])) {
                            foreach ($allLangs as $lkey => $lval) {
                                $lang_phrase[] = array(
                                    'where' => array(
                                        'Code' => $allLangs[$lkey]['Code'],
                                        'Key' => 'polls_items+name+' . $key
                                    ),
                                    'fields' => array(
                                        'Value' => $f_items[$key][$allLangs[$lkey]['Code']]
                                    )
                                );
                            }
                        } else {
                            if (!empty($f_items[$key][$allLangs[$lkey]['Code']])) {
                                unset($insert);
                                $insert = array(
                                    'Poll_ID' => $id,
                                    'Color' => $f_colors[$i]
                                );
                                $rlActions->insertOne($insert, 'polls_items');
                                $new_item = mysql_insert_id();
                                foreach ($allLangs as $lkey => $lval) {
                                    $lang_phrase_insert[] = array(
                                        'Code' => $allLangs[$lkey]['Code'],
                                        'Module' => 'common',
                                        'Plugin' => 'polls',
                                        'Status' => 'active',
                                        'Key' => 'polls_items+name+' . $new_item,
                                        'Value' => $f_items[$key][$allLangs[$lkey]['Code']]
                                    );
                                }
                            }
                        }
                        $i++;
                    }
                    if (!empty($lang_phrase_insert)) {
                        $rlActions->insert($lang_phrase_insert, 'lang_keys');
                    }
                    foreach ($allLangs as $key => $value) {
                        $lang_phrase[] = array(
                            'where' => array(
                                'Code' => $allLangs[$key]['Code'],
                                'Key' => 'polls+name+' . $id
                            ),
                            'fields' => array(
                                'Value' => $f_title[$allLangs[$key]['Code']]
                            )
                        );
                    }
                    $GLOBALS['rlActions']->update($lang_phrase, 'lang_keys');
                    if (!$_POST['random'] && !$poll_info['Random']) {
                        $tmp_polls = $rlPolls->get($id);
                        if ($tmp_polls) {
                            $polls          = serialize($tmp_polls);
                            $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
                        } else {
                            $insert_content = $rlPolls->empty_content;
                        }
                        $update_block = array(
                            'fields' => array(
                                'Side' => $f_side,
                                'Tpl' => $_POST['tpl'],
                                'Content' => $insert_content,
                                'Status' => $_POST['status'],
                                'Plugin' => 'polls'
                            ),
                            'where' => array(
                                'Key' => $polls_key
                            )
                        );
                        $action       = $GLOBALS['rlActions']->updateOne($update_block, 'blocks');
                        foreach ($allLangs as $key => $value) {
                            $lang_phrases[] = array(
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'blocks+name+' . $polls_key,
                                    'Plugin' => 'polls'
                                ),
                                'fields' => array(
                                    'Value' => $f_title[$allLangs[$key]['Code']]
                                )
                            );
                        }
                        $rlActions->update($lang_phrases, 'lang_keys');
                    } elseif ($_POST['random'] && !$poll_info['Random']) {
                        $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = '{$polls_key}' LIMIT 1");
                        $sql = "DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'blocks+name+{$polls_key}' AND `Plugin` = 'polls'";
                        $rlDb->query($sql);
                        $polls = serialize($rlPolls->get('all'));
                        if (!empty($polls)) {
                            $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
                        } else {
                            $insert_content = $rlPolls->empty_content;
                        }
                        if (!empty($polls)) {
                            $update_block = array(
                                'fields' => array(
                                    'Content' => $insert_content,
                                    'Status' => 'active',
                                    'Plugin' => 'polls'
                                ),
                                'where' => array(
                                    'Key' => 'polls'
                                )
                            );
                        } else {
                            $update_block = array(
                                'fields' => array(
                                    'Content' => $insert_content,
                                    'Status' => $_POST['status'],
                                    'Plugin' => 'polls'
                                ),
                                'where' => array(
                                    'Key' => 'polls'
                                )
                            );
                        }
                        $action = $GLOBALS['rlActions']->updateOne($update_block, 'blocks');
                    } elseif (!$_POST['random'] && $poll_info['Random']) {
                        $tmp_polls = $rlPolls->get($id);
                        if ($tmp_polls) {
                            $polls          = serialize($tmp_polls);
                            $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
                        } else {
                            $insert_content = $rlPolls->empty_content;
                        }
                        $position   = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                        $data_block = array(
                            'Sticky' => '1',
                            'Key' => $polls_key,
                            'Side' => $f_side,
                            'Type' => 'php',
                            'Position' => $position['max'] + 1,
                            'Content' => $insert_content,
                            'Tpl' => $_POST['tpl'],
                            'Plugin' => 'polls',
                            'Status' => $_POST['status'],
                            'Readonly' => '0'
                        );
                        $rlActions->insertOne($data_block, 'blocks');
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Plugin' => 'polls',
                                'Status' => 'active',
                                'Key' => 'blocks+name+' . $polls_key,
                                'Value' => $f_title[$allLangs[$key]['Code']]
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $tmp_polls_all = $rlPolls->get('all');
                        if ($tmp_polls_all) {
                            $polls_all      = serialize($tmp_polls_all);
                            $insert_content = str_replace('{$polls_replace}', $polls_all, $rlPolls->content);
                        } else {
                            $insert_content = $rlPolls->empty_content;
                        }
                        $update_block = array(
                            'fields' => array(
                                'Content' => $insert_content,
                                'Plugin' => 'polls'
                            ),
                            'where' => array(
                                'Key' => 'polls'
                            )
                        );
                        $action       = $GLOBALS['rlActions']->updateOne($update_block, 'blocks');
                    } elseif ($_POST['random'] && $poll_info['Random']) {
                        $tmp_polls = $rlPolls->get('all');
                        if ($tmp_polls) {
                            $polls          = serialize($tmp_polls);
                            $insert_content = str_replace('{$polls_replace}', $polls, $rlPolls->content);
                        } else {
                            $insert_content = $rlPolls->empty_content;
                        }
                        $update_block = array(
                            'fields' => array(
                                'Content' => $insert_content,
                                'Status' => $_POST['status'],
                                'Plugin' => 'polls'
                            ),
                            'where' => array(
                                'Key' => 'polls'
                            )
                        );
                        $action       = $GLOBALS['rlActions']->updateOne($update_block, 'blocks');
                    }
                    $message = $lang['item_edited'];
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
    } elseif ($_GET['action'] == 'results') {
        $poll_id = (int) $_GET['poll'];
        $poll    = $rlDb->fetch(array(
            'ID`, `ID` AS `Key'
        ), array(
            'ID' => $poll_id
        ), null, null, 'polls', 'row');
        $poll    = $rlLang->replaceLangKeys($poll, 'polls', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('poll_info', $poll);
        $total_votes = $rlDb->getRow("SELECT SUM(`Votes`) AS `sum` FROM `" . RL_DBPREFIX . "polls_items` WHERE `Poll_ID` = '{$poll_id}' LIMIT 1");
        $total_votes = $total_votes['sum'];
        $rlSmarty->assign_by_ref('total_votes', $total_votes);
        $poll_items = $rlDb->fetch(array(
            'ID` AS `Key`, `Votes',
            'Color'
        ), array(
            'Poll_ID' => $poll_id
        ), null, null, 'polls_items');
        foreach ($poll_items as $key => $value) {
            $poll_items[$key]['percent'] = floor(((int) $poll_items[$key]['Votes'] * 100) / $total_votes);
            $poll_items[$key]['width']   = floor(((int) $poll_items[$key]['Votes'] * 100) / $total_votes) * 3;
        }
        $poll_items = $rlLang->replaceLangKeys($poll_items, 'polls_items', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        $rlSmarty->assign_by_ref('poll_items', $poll_items);
    }
    $reefless->loadClass('Polls', null, 'polls');
    $rlXajax->registerFunction(array(
        'deletePoll',
        $rlPolls,
        'ajaxDeletePoll'
    ));
}