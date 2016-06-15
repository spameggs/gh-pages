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
        if ($field == 'Status') {
            $cur_status = $rlDb->getOne('Status', "`ID` = '{$id}'", 'search_forms');
            if ($cur_status != $value) {
                $update_cache = true;
            }
        }
        $rlHook->load('apExtSearchFormsUpdate');
        $rlActions->updateOne($updateData, 'search_forms');
        if ($update_cache) {
            $rlCache->updateSearchForms();
            $rlCache->updateSearchFields();
        }
        exit;
    }
    $limit     = $rlValid->xSql($_GET['limit']);
    $start     = $rlValid->xSql($_GET['start']);
    $langCode  = $rlValid->xSql($_GET['lang_code']);
    $phrase    = $rlValid->xSql($_GET['phrase']);
    $condition = "WHERE `Status` <> 'trash'";
    $rlHook->load('apExtSearchFormsSql');
    $rlDb->setTable('search_forms');
    $data = $rlDb->fetch('*', null, $condition, array(
        $start,
        $limit
    ));
    $data = $rlLang->replaceLangKeys($data, 'search_forms', array(
        'name',
        'des'
    ), RL_LANG_CODE, 'admin');
    $rlDb->resetTable();
    $reefless->loadClass('ListingTypes');
    foreach ($data as $key => $value) {
        $data[$key]['Type'] = $rlListingTypes->types[$data[$key]['Type']]['name'];
        $data[$key]['Type'] .= $value['In_tab'] ? ' <b>' . $lang['in_tab'] . '</b>' : '';
        $data[$key]['Mode']   = $lang[$data[$key]['Mode']];
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
        $data[$key]['Groups'] = $data[$key]['Groups'] ? $lang['yes'] : $lang['no'];
    }
    $rlHook->load('apExtSearchFormsData');
    $count = $rlDb->getRow("SELECT COUNT(`ID`) AS `count` FROM `" . RL_DBPREFIX . "search_forms` WHERE `Status` <> 'trash'");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    if ($_GET['action']) {
        if ($_GET['action'] == 'add') {
            $bcAStep = $lang['add_form'];
        } elseif ($_GET['action'] == 'edit') {
            $bcAStep = $lang['edit_form'];
        } elseif ($_GET['action'] == 'build') {
            $bcAStep = $lang['build_form'];
        }
    }
    $rlHook->load('apPhpSearchFormsTop');
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $allLangs = $GLOBALS['languages'];
        $rlSmarty->assign_by_ref('allLangs', $allLangs);
        if ($_GET['action'] == 'edit') {
            $s_key             = $rlValid->xSql($_GET['form']);
            $form_info         = $rlDb->fetch('*', array(
                'Key' => $s_key
            ), "AND `Status` <> 'trash'", null, 'search_forms', 'row');
            $_POST['readonly'] = $form_info['Readonly'];
            $rlSmarty->assign('cpTitle', $lang['search_forms+name+' . $s_key]);
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['key']    = $form_info['Key'];
            $_POST['status'] = $form_info['Status'];
            $_POST['type']   = $form_info['Type'];
            $_POST['groups'] = $form_info['Groups'];
            $i_names         = $rlDb->fetch(array(
                'Code',
                'Value'
            ), array(
                'Key' => 'search_forms+name+' . $s_key
            ), "AND `Status` <> 'trash'", null, 'lang_keys');
            foreach ($i_names as $nKey => $nVal) {
                $_POST['name'][$i_names[$nKey]['Code']] = $i_names[$nKey]['Value'];
            }
            $rlHook->load('apPhpSearchFormsPost');
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
                    $errors[] = $lang['incorrect_phrase_key'];
                }
                $exist_key = $rlDb->fetch(array(
                    'Key'
                ), array(
                    'Key' => $f_key
                ), null, null, 'search_forms');
                if (!empty($exist_key)) {
                    $errors[] = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_form_key_exist']);
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
                $errors[] = $lang['notice_no_type_chose'];
            }
            $f_groups = $_POST['groups'];
            if ($f_groups == '') {
                $errors[] = str_replace('{field}', "<b>\"" . $lang['use_groups'] . "\"</b>", $lang['field_value_does_not_selected']);
            }
            $rlHook->load('apPhpSearchFormsValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Key' => $f_key,
                        'Status' => $_POST['status'],
                        'Type' => $f_type,
                        'Groups' => (int) $f_groups
                    );
                    $rlHook->load('apPhpSearchFormsBeforeAdd');
                    if ($action = $rlActions->insertOne($data, 'search_forms')) {
                        $rlHook->load('apPhpSearchFormsAfterAdd');
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'search_forms+name+' . $f_key,
                                'Value' => $f_name[$allLangs[$key]['Code']]
                            );
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $message = $lang['form_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new search forms (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new search forms (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $update_date = array(
                        'fields' => array(
                            'Status' => $_POST['status'],
                            'Groups' => (int) $f_groups
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    if (!$form_info['Readonly']) {
                        $update_date['fields']['Type'] = $f_type;
                    }
                    $rlHook->load('apPhpSearchFormsBeforeEdit');
                    $action = $GLOBALS['rlActions']->updateOne($update_date, 'search_forms');
                    $rlHook->load('apPhpSearchFormsAfterEdit');
                    foreach ($allLangs as $key => $value) {
                        if ($rlDb->getOne('ID', "`Key` = 'search_forms+name+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                            $update_phrases = array(
                                'fields' => array(
                                    'Value' => $_POST['name'][$allLangs[$key]['Code']]
                                ),
                                'where' => array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Key' => 'search_forms+name+' . $f_key
                                )
                            );
                            $rlActions->updateOne($update_phrases, 'lang_keys');
                        } else {
                            $insert_phrases = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Key' => 'search_forms+name+' . $f_key,
                                'Value' => $_POST['name'][$allLangs[$key]['Code']]
                            );
                            $rlActions->insertOne($insert_phrases, 'lang_keys');
                        }
                    }
                    $message = $lang['form_edited'];
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
    } elseif ($_GET['action'] == 'build') {
        $form_key = $rlValid->xSql($_GET['form']);
        $form     = $rlDb->fetch('*', array(
            'Key' => $form_key
        ), null, 1, 'search_forms', 'row');
        if (!$form) {
            $sError = true;
        } else {
            $form = $rlLang->replaceLangKeys($form, 'search_forms', array(
                'name'
            ), RL_LANG_CODE, 'admin');
            $rlSmarty->assign_by_ref('form_info', $form);
            $rlSmarty->assign_by_ref('cpTitle', $form['name']);
            $reefless->loadClass('Builder', 'admin');
            $rlBuilder->rlBuildTable = 'search_forms_relations';
            $relations               = $rlBuilder->getRelations($form['ID']);
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
            if ($form['Groups']) {
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
            }
            $fields = $rlDb->fetch(array(
                'ID',
                'Key',
                'Type',
                'Status'
            ), null, "WHERE `Status` <> 'trash' AND `Type` <> 'textarea' AND `Type` <> 'file' AND `Type` <> 'image' AND `Type` <> 'accept'", null, 'listing_fields');
            $fields = $rlLang->replaceLangKeys($fields, 'listing_fields', array(
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
            $rlXajax->registerFunction(array(
                'buildForm',
                $rlBuilder,
                'ajaxBuildForm'
            ));
            $rlHook->load('apPhpSearchFormsBuild');
        }
    }
    $rlXajax->registerFunction(array(
        'deleteSearchForm',
        $rlAdmin,
        'ajaxDeleteSearchForm'
    ));
    $rlHook->load('apPhpSearchFormsBottom');
}