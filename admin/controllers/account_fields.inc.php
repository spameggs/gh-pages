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
        $rlHook->load('apExtAccountFieldsUpdate');
        $rlActions->updateOne($updateData, 'account_fields');
        exit;
    }
    $limit    = $rlValid->xSql($_GET['limit']);
    $start    = $rlValid->xSql($_GET['start']);
    $sort     = $rlValid->xSql($_GET['sort']);
    $sortDir  = $rlValid->xSql($_GET['dir']);
    $langCode = $rlValid->xSql($_GET['lang_code']);
    $phrase   = $rlValid->xSql($_GET['phrase']);
    $sql      = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T2`.`Value` AS `name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "account_fields` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('account_fields+name+',`T1`.`Key`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "WHERE `T1`.`Status` <> 'trash' ";
    if ($_GET['action'] == 'search') {
        $search_fields = array(
            'Name',
            'Type',
            'Required',
            'Map',
            'Status'
        );
        foreach ($search_fields as $item) {
            if ($_GET[$item] != '') {
                $s_value = $rlValid->xSql($_GET[$item]);
                $sql .= $item == 'Name' ? "AND `T2`.`Value` LIKE '%{$s_value}%' " : "AND `T1`.`{$item}` = '{$s_value}' ";
            }
        }
    }
    if ($sort) {
        $sortField = $sort == 'name' ? "`T2`.`Value`" : "`T1`.`{$sort}`";
        $sql .= "ORDER BY {$sortField} {$sortDir} ";
    }
    $sql .= "LIMIT {$start}, {$limit}";
    $rlHook->load('apExtAccountFieldsSql');
    $data = $rlDb->getAll($sql);
    foreach ($data as $key => $value) {
        $data[$key]['Type']     = $l_types[$data[$key]['Type']];
        $data[$key]['Required'] = $data[$key]['Required'] ? $lang['yes'] : $lang['no'];
        $data[$key]['Map']      = $data[$key]['Map'] ? $lang['yes'] : $lang['no'];
        $data[$key]['Status']   = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $rlHook->load('apExtAccountFieldsData');
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Json');
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
} else {
    $rlHook->load('apPhpAccountFieldsTop');
    if ($_GET['action']) {
        $bcAStep = $_GET['action'] == 'add' ? $lang['add_field'] : $lang['edit_field'];
    }
    $allLangs = $rlLang->getLanguagesList('all');
    $rlSmarty->assign_by_ref('allLangs', $allLangs);
    $bind_data_formats = $rlDb->fetch(array(
        'Key',
        'ID'
    ), array(
        'Parent_ID' => '0',
        'Status' => 'active'
    ), "AND `Key` <> 'currency' ORDER BY `Position`", null, 'data_formats');
    $bind_data_formats = $rlLang->replaceLangKeys($bind_data_formats, 'data_formats', 'name', RL_LANG_CODE, 'admin');
    $rlSmarty->assign_by_ref('data_formats', $bind_data_formats);
    $reefless->loadClass('Fields', 'admin');
    $rlFields->table        = 'account_fields';
    $rlFields->source_table = 'accounts';
    $rlSmarty->assign('grid_key', 'accountFieldsGrid');
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        if ($_GET['action'] == 'edit') {
            $e_key      = $rlValid->xSql($_GET['field']);
            $field_info = $rlDb->fetch('*', array(
                'Key' => $e_key
            ), "AND `Status` <> 'trash'", null, $rlFields->table, 'row');
            $rlSmarty->assign_by_ref('field_info', $field_info);
            if (empty($field_info)) {
                $errors[] = $lang['notice_field_not_found'];
            } else {
                if (!$_POST['fromPost']) {
                    $rlFields->simulatePost($e_key, $field_info);
                    $rlHook->load('apPhpAccountFieldsPost');
                }
            }
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
            $f_type = $_POST['type'];
            $f_key  = $rlValid->xSql($_POST['key']);
            if (!utf8_is_ascii($f_key)) {
                $f_key = utf8_to_ascii($f_key);
            }
            if (strlen($f_key) < 2) {
                $errors[]       = $lang['incorrect_phrase_key'];
                $error_fields[] = 'key';
            }
            if ($_GET['action'] == 'add' && $f_key) {
                $exist_key = $rlDb->getRow("SHOW FIELDS FROM `" . RL_DBPREFIX . $rlFields->source_table . "` WHERE `Field` LIKE '{$f_key}'");
                if ($exist_key) {
                    $errors[]       = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_field_exist']);
                    $error_fields[] = 'key';
                }
            }
            $f_key = $_GET['action'] == 'add' ? $rlValid->str2key($f_key) : $rlValid->xSql($f_key);
            foreach ($allLangs as $lang_item) {
                if (empty($_POST['name'][$lang_item['Code']])) {
                    $errors[]       = str_replace('{field}', "<b>" . $lang['name'] . "({$lang_item['name']})</b>", $lang['notice_field_empty']);
                    $error_fields[] = 'name[' . $lang_item['Code'] . ']';
                }
            }
            if (empty($f_type)) {
                $errors[]       = $lang['notice_type_empty'];
                $error_fields[] = 'type';
            }
            if ($_POST['data_format'] || $_POST['mixed_data_format']) {
                $f_data['data_format']       = $_POST['data_format'];
                $f_data['mixed_data_format'] = $_POST['mixed_data_format'];
            }
            if ($f_type == 'date' && empty($_POST['date']['mode'])) {
                $errors[] = $lang['notice_mode_not_chose'];
            }
            if ($f_type == 'file' && empty($_POST['file']['type'])) {
                $errors[] = $lang['notice_type_empty'];
            }
            foreach ($allLangs as $lang_item) {
                if ($f_type == 'accept' && empty($_POST['accept'][$lang_item['Code']])) {
                    $errors[] = str_replace('{field}', "<b>" . $lang['agreement_text'] . "({$lang_item['name']})</b>", $lang['notice_field_empty']);
                }
            }
            $rlHook->load('apPhpAccountFieldsValidate');
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $rlHook->load('apPhpAccountFieldsBeforeAdd');
                    $action       = $rlFields->createField($f_type, $f_key, $allLangs);
                    $new_field_id = $rlFields->addID;
                    $rlHook->load('apPhpAccountFieldsAfterAdd');
                    $message = $lang['field_added'];
                    $aUrl    = array(
                        "controller" => $controller,
                        "action" => "add"
                    );
                } elseif ($_GET['action'] == 'edit') {
                    $rlHook->load('apPhpAccountFieldsBeforeEdit');
                    $action = $rlFields->editField($f_type, $e_key, $allLangs);
                    $rlHook->load('apPhpAccountFieldsAfterEdit');
                    $message = $lang['field_edited'];
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
    $rlHook->load('apPhpAccountFieldsBottom');
    $rlXajax->registerFunction(array(
        'deleteAField',
        $rlFields,
        'ajaxDeleteAField'
    ));
}