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
        $rlHook->load('apExtListingGroupsUpdate');
        $rlActions->updateOne($updateData, 'listing_groups');
        $reefless->loadClass('Cache');
        $rlCache->updateForms();
        exit;
    }
    $limit = $rlValid->xSql($_GET['limit']);
    $start = $rlValid->xSql($_GET['start']);
    $rlHook->load('apExtListingGroupsSql');
    $rlDb->setTable('listing_groups');
    $data = $rlDb->fetch('*', null, "WHERE `Status` <> 'trash' ORDER BY `Key` ASC", array(
        $start,
        $limit
    ));
    $data = $rlLang->replaceLangKeys($data, 'listing_groups', array(
        'name'
    ), RL_LANG_CODE, 'admin');
    $rlDb->resetTable();
    foreach ($data as $key => $value) {
        $data[$key]['Display'] = $data[$key]['Display'] ? $GLOBALS['lang']['yes'] : $GLOBALS['lang']['no'];
        $data[$key]['Status']  = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $rlHook->load('apExtListingGroupsData');
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "listing_groups` WHERE `Status` <> 'trash'");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $rlHook->load('apPhpListingGroupsTop');
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_group'] : $lang['edit_group'];
    }
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $s_key            = $rlValid->xSql($_GET['group']);
            $group_info       = $rlDb->fetch('*', array(
                'Key' => $s_key
            ), "AND `Status` <> 'trash'", null, 'listing_groups', 'row');
            $_POST['key']     = $group_info['Key'];
            $_POST['status']  = $group_info['Status'];
            $_POST['display'] = $group_info['Display'];
            $s_names          = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'listing_groups+name+' . $s_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($s_names as $nKey => $nVal) {
                $_POST['name'][$s_names[$nKey]['Code']] = $s_names[$nKey]['Value'];
            }
            $rlHook->load('apPhpListingGroupsPost');
        }
        if (isset($_POST['submit'])) {
            $errors = array();
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
                $f_key     = $rlValid->str2key($f_key);
                $exist_key = $rlDb->fetch(array(
                    'Key'
                ), array(
                    'Key' => $f_key
                ), null, null, 'listing_groups');
                if (!empty($exist_key)) {
                    $errors[]       = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_group_exist']);
                    $error_fields[] = 'key';
                }
            }
            $f_name = $_POST['name'];
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$allLangs[$lkey]['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'name[' . $lval['Code'] . ']';
                }
                $f_names[$allLangs[$lkey]['Code']] = $f_name[$allLangs[$lkey]['Code']];
            }
            $rlHook->load('apPhpListingGroupsValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Key' => $f_key,
                        'Status' => $_POST['status'],
                        'Display' => $_POST['display']
                    );
                    $rlHook->load('apPhpListingGroupsBeforeAdd');
                    if ($action = $rlActions->insertOne($data, 'listing_groups')) {
                        $rlHook->load('apPhpListingGroupsAfterAdd');
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'listing_groups+name+' . $f_key,
                                'Value' => $f_name[$allLangs[$key]['Code']]
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $message = $lang['group_added'];
                        $aUrl    = array(
                            "controller" => $controller,
                            "action" => "add"
                        );
                    } else {
                        trigger_error("Can't add new lisitng field's group (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new lisitng field's group (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_date = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Display' => $_POST['display']
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    $rlHook->load('apPhpListingGroupsBeforeEdit');
                    $action = $GLOBALS['rlActions']->updateOne($update_date, 'listing_groups');
                    $rlHook->load('apPhpListingGroupsAfterEdit');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'listing_groups+name+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_names = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'listing_groups+name+' . $f_key
                                )
                            );
                            $rlActions->updateOne($update_names, 'lang_keys');
                        } else {
                            $insert_names = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'listing_groups+name+' . $f_key,
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_names, 'lang_keys');
                        }
                    }
                    $rlCache->updateForms();
                    $message = $lang['group_edited'];
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
    $rlHook->load('apPhpListingGroupsBottom');
    $reefless->loadClass('Categories');
    $rlXajax->registerFunction(array(
        'deleteFGroup',
        $rlCategories,
        'ajaxDeleteFGroup'
    ));
}