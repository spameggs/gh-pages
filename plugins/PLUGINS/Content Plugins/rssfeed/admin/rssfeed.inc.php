<?php
$content_tpl = '{include file=$smarty.const.RL_PLUGINS|cat:$smarty.const.RL_DS|cat:"rssfeed"|cat:$smarty.const.RL_DS|cat:"block.tpl" number={number} url="{url}" }';
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
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'rss_feed');
        if (in_array($field, array(
            'Side',
            'Tpl'
        ))) {
            $updateData_blocks = array(
                'fields' => array(
                    $field => $value
                ),
                'where' => array(
                    'Key' => 'rssfeed_' . $id
                )
            );
            $rlActions->updateOne($updateData_blocks, 'blocks');
        } else if ($field == 'Article_num') {
            $url               = $rlDb->getOne('Url', "`ID` = $id", 'rss_feed');
            $updateData_blocks = array(
                'fields' => array(
                    'Content' => str_replace(array(
                        '{number}',
                        '{url}'
                    ), array(
                        $value,
                        $url
                    ), $content_tpl)
                ),
                'where' => array(
                    'Key' => 'rssfeed_' . $id
                )
            );
            $rlActions->updateOne($updateData_blocks, 'blocks');
        }
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $sql   = "SELECT SQL_CALC_FOUND_ROWS DISTINCT *, CONCAT('rssfeed_', `ID`) AS `Key` FROM `" . RL_DBPREFIX . "rss_feed` WHERE `Status` <> 'trash' LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $data  = $rlLang->replaceLangKeys($data, 'blocks', array(
        'name'
    ), RL_LANG_CODE, 'admin');
    $rlDb->resetTable();
    foreach ($data as $key => $value) {
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
        $data[$key]['Tpl']    = $data[$key]['Tpl'] ? $lang['yes'] : $lang['no'];
        $data[$key]['Side']   = $GLOBALS['lang'][$data[$key]['Side']];
    }
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    if ($_GET['action']) {
        switch ($_GET['action']) {
            case 'add':
                $bcAStep = $lang['add_new_rss'];
                break;
            case 'edit':
                $bcAStep = $lang['edit_rss'];
                break;
        }
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $id                   = (int) $_GET['id'];
            $item_info            = $rlDb->fetch(array(
                'Status',
                'Url',
                'Side',
                'Tpl',
                'Article_num'
            ), array(
                'ID' => $id
            ), "AND `Status` <> 'trash'", 1, 'rss_feed', 'row');
            $_POST['status']      = $item_info['Status'];
            $_POST['url']         = $item_info['Url'];
            $_POST['side']        = $item_info['Side'];
            $_POST['tpl']         = $item_info['Tpl'];
            $_POST['article_num'] = $item_info['Article_num'];
            $names                = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'blocks+name+rssfeed_' . $id
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($names as $nKey => $nVal) {
                $_POST['name'][$nVal['Code']] = $names[$nKey]['Value'];
            }
        }
        if (isset($_POST['submit'])) {
            $post_names = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($post_names[$lval['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$lval['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = "name[{$lval['Code']}]";
                }
            }
            $f_side = $_POST['side'];
            if (empty($f_side)) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['block_side'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'side';
            }
            $url = $_POST['url'];
            if (empty($url) || $url == 'http://' || !$rlValid->isUrl($url)) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['link'] . "\"</b>", $lang['notice_field_incorrect']);
                $error_fields[] = "url";
            }
            $number = (int) $_POST['article_num'];
            if (!$number) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['article_num'] . "\"</b>", $lang['notice_field_incorrect']);
                $error_fields[] = "article_num";
            }
            if (empty($errors)) {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Side' => $f_side,
                        'Tpl' => $_POST['tpl'],
                        'Url' => $url,
                        'Article_num' => $number,
                        'Status' => $_POST['status'],
                        'Date' => 'NOW()'
                    );
                    if ($action = $rlActions->insertOne($data, 'rss_feed')) {
                        $rss_feed_key = 'rssfeed_' . mysql_insert_id();
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $value['Code'],
                                'Module' => 'common',
                                'Plugin' => 'rss_feed',
                                'Status' => 'active',
                                'Key' => 'blocks+name+' . $rss_feed_key,
                                'Value' => $post_names[$value['Code']]
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $position   = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "blocks`");
                        $data_block = array(
                            'Sticky' => '1',
                            'Key' => $rss_feed_key,
                            'Side' => $f_side,
                            'Type' => 'smarty',
                            'Position' => $last_position['max'] + 1,
                            'Content' => str_replace(array(
                                '{number}',
                                '{url}'
                            ), array(
                                $number,
                                $url
                            ), $content_tpl),
                            'Tpl' => $_POST['tpl'],
                            'Plugin' => 'rssfeed',
                            'Status' => $_POST['status'],
                            'Readonly' => '0'
                        );
                        $rlActions->insertOne($data_block, 'blocks');
                        $message = $lang['item_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        $rlDebug->logger("Can't add new rssfeed (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $id           = (int) $_GET['id'];
                    $rss_feed_key = 'rssfeed_' . $id;
                    $update_feed  = array(
                        'fields' => array(
                            'Url' => $_POST['url'],
                            'Side' => $f_side,
                            'Tpl' => $_POST['tpl'],
                            'Status' => $_POST['status'],
                            'Article_num' => $number,
                            'Date' => 'NOW()'
                        ),
                        'where' => array(
                            'ID' => $id
                        )
                    );
                    $action       = $GLOBALS['rlActions']->updateOne($update_feed, 'rss_feed');
                    $update_block = array(
                        'fields' => array(
                            'Side' => $f_side,
                            'Tpl' => $_POST['tpl'],
                            'Content' => str_replace(array(
                                '{number}',
                                '{url}'
                            ), array(
                                $number,
                                $url
                            ), $content_tpl),
                            'Status' => $_POST['status']
                        ),
                        'where' => array(
                            'Key' => $rss_feed_key
                        )
                    );
                    $action       = $GLOBALS['rlActions']->updateOne($update_block, 'blocks');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'blocks+name+{$rss_feed_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_names = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'blocks+name+' . $rss_feed_key
                                )
                            );
                            $rlActions->updateOne($update_names, 'lang_keys');
                        } else {
                            $insert_names = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'blocks+name+' . $rss_feed_key,
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_names, 'lang_keys');
                        }
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
    }
    $reefless->loadClass('RssFeed', null, 'rssfeed');
    $rlXajax->registerFunction(array(
        'deleteRssFeed',
        $rlRssFeed,
        'ajaxDeleteRss'
    ));
}