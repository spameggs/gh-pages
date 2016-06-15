<?php
$sql = "SELECT `T1`.`ID`, `T2`.`Value` AS `name` FROM `" . RL_DBPREFIX . "data_formats` AS `T1` ";
$sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('data_formats+name+',`T1`.`Key`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
$sql .= "WHERE `T1`.`Status` <> 'trash' AND `T1`.`Key` <> 'years' AND `T1`.`Parent_ID` = '0'";
$dataFormats = $rlDb->getAll($sql);
$rlSmarty->assign_by_ref('data_formats', $dataFormats);
$allLangs = $GLOBALS['languages'];
$rlSmarty->assign_by_ref('allLangs', $allLangs);
$reefless->loadClass('Actions');
$reefless->loadClass('DataEntriesImport', false, 'dataEntriesImport');
$rlXajax->registerFunction(array(
    'getDFLevel',
    $rlDataEntriesImport,
    'ajaxGetDFLevel'
));
if ($_POST['upload'] && !$_REQUEST['xjxfun']) {
    @unlink($rlDataEntriesImport->tmpFile);
    $sourceFile    = $_FILES['source'];
    $errors        = $error_fields = array();
    $allowed_types = array(
        'text/csv',
        'text/plain',
        'application/vnd.ms-excel'
    );
    echo $sourceFile['type'];
    exit;
    if (!in_array($sourceFile['type'], $allowed_types)) {
        $ext = pathinfo($sourceFile['name'], PATHINFO_EXTENSION);
        array_push($errors, str_replace('{ext}', "<b>{$ext}</b>", $lang['notice_bad_file_ext']));
    } else {
        $importTo = $_POST['import_to'];
        if ($importTo == 'exists') {
            $dfID                           = (int) $_POST['import_to_parent'];
            $dfKey                          = $rlDb->getOne('Key', "`ID` = '{$dfID}'", 'data_formats');
            $rlDataEntriesImport->parentKey = $dfKey;
        } else {
            $f_name   = $_POST['name'];
            $defName  = !empty($f_name['en']) ? $f_name['en'] : current($f_name);
            $dfKey    = $rlDataEntriesImport->uniqKeyByName($defName);
            $langKeys = array();
            foreach ($allLangs as $lkey => $lval) {
                if (empty($f_name[$allLangs[$lkey]['Code']])) {
                    array_push($errors, str_replace('{field}', "<b>{$lang['name']}({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']));
                    array_push($error_fields, "name[{$lval['Code']}]");
                }
                array_push($langKeys, array(
                    'Key' => "data_formats+name+{$dfKey}",
                    'Value' => $rlValid->xSql($f_name[$allLangs[$lkey]['Code']]),
                    'Code' => $allLangs[$lkey]['Code'],
                    'Module' => 'common'
                ));
            }
            if (empty($errors)) {
                $data = array(
                    'Key' => $dfKey,
                    'Parent_ID' => (int) $_POST['import_to_parent_new'],
                    'Order_type' => in_array($_POST['order_type'], array(
                        'alphabetic',
                        'position'
                    )) ? $_POST['order_type'] : 'position'
                );
                if ($rlActions->insertOne($data, 'data_formats')) {
                    $dfID = mysql_insert_id();
                    $rlActions->insert($langKeys, 'lang_keys');
                }
            }
        }
    }
    if (!move_uploaded_file($sourceFile['tmp_name'], $rlDataEntriesImport->tmpFile)) {
        array_push($errors, $lang['dataEntriesImport_error_upload']);
    } else {
        chmod($rlDataEntriesImport->tmpFile, 0644);
    }
    if (!empty($errors)) {
        $rlSmarty->assign_by_ref('errors', $errors);
    } else {
        $sourceExt                      = end(explode('.', $sourceFile['name']));
        $rlDataEntriesImport->parentID  = $dfID;
        $rlDataEntriesImport->parentKey = $dfKey;
        if (false !== $res = $rlDataEntriesImport->import($sourceExt, $_POST['delimiter'])) {
            $rlCache->updateDataFormats();
            $rlCache->updateForms();
            $reefless->loadClass('Notice');
            $message = str_replace('[count]', $res, $lang['dataEntriesImport_notice']);
            $rlNotice->saveNotice($message);
            $reefless->redirect(array(
                'controller' => 'dataEntriesImport'
            ));
        }
    }
}