<?php
error_reporting(0);
ini_set("display_errors", 0);
set_error_handler(array(
    "rlDebug",
    "errorHandler"
), E_ERROR);
$reefless->loadClass('Categories');
$reefless->loadClass('Actions');
$reefless->loadClass('ImportExportCategories', false, 'importExportCategories');
$tmpFile = RL_TMP . 'upload' . RL_DS . 'impotr_export_categories.tmp';
if (!isset($_GET['action'])) {
    unlink($tmpFile);
    unset($_SESSION['file_info']);
    if (isset($_SESSION['import_error'])) {
        $errors[] = $_SESSION['import_error'];
        $rlSmarty->assign_by_ref('errors', $errors);
        unset($_SESSION['import_error']);
    }
}
if (isset($_GET['action'])) {
    $bcAStep = $_GET['action'] == 'import' ? $lang['importExportCategories_import'] : $lang['importExportCategories_export'];
}
if ($_GET['action'] == 'import') {
    if (isset($_POST['submit'])) {
        $errors   = array();
        $fileInfo = $_FILES['file_import'];
        $pathInfo = pathinfo($fileInfo['name']);
        if (empty($pathInfo['filename'])) {
            array_push($errors, str_replace('[field]', "<b>{$lang['file']}</b>", $lang['notice_field_empty']));
        } elseif ($pathInfo['extension'] != 'xls') {
            array_push($errors, $lang['importExportCategories_incorrect_file_ext']);
        }
        if (!empty($fileInfo['tmp_name'])) {
            if (move_uploaded_file($fileInfo['tmp_name'], $tmpFile)) {
                chmod($tmpFile, 0644);
                $_SESSION['file_info'] = $_FILES['file_import'];
            } else {
                array_push($errors, $lang['importExportCategories_not_move_file']);
            }
        }
        if (!empty($errors)) {
            $rlSmarty->assign_by_ref('errors', $errors);
        }
    }
    if (is_readable($tmpFile)) {
        require_once(RL_PLUGINS . 'importExportCategories' . RL_DS . 'admin' . RL_DS . 'lib' . RL_DS . 'reader.php');
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('UTF-8');
        if (false === $data->read($tmpFile)) {
            $_SESSION['import_error'] = $lang['importExportCategories_file_not_readable'];
            $reefless->redirect(array(
                "controller" => "importExportCategories"
            ));
        } else {
            $row = 0;
            for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
                $row++;
                for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
                    $array_data[$row - 1][$j] = $data->sheets[0]['cells'][$i][$j];
                }
            }
            loadUTF8functions('ascii', 'utf8_to_ascii', 'utf8_is_ascii');
            $existKeys = array();
            foreach ($array_data as $cKey => $value) {
                $name = $key = $value[1];
                if (!utf8_is_ascii($key)) {
                    if ($tmp_key = utf8_to_ascii($key)) {
                        $key = $tmp_key;
                    } else {
                        if (!utf8_is_ascii_ctrl($key)) {
                            $key = utf8_strip_non_ascii_ctrl($key);
                        }
                    }
                }
                $parent = $value[2];
                $key    = strtolower($rlValid->str2key($parent . '_' . $key));
                $path   = !empty($value[3]) ? $rlImportExportCategories->str2path($value[3]) : $rlImportExportCategories->str2path($key);
                $path   = !empty($parent) ? $rlImportExportCategories->str2path($parent) . '/' . $path : $path;
                $lock   = !empty($value[4]) ? $value[4] : 0;
                $lock   = !in_array($lock, array(
                    0,
                    1
                )) ? 0 : $lock;
                if (!empty($name)) {
                    $categories[$cKey]['key']    = $key;
                    $categories[$cKey]['name']   = $value[1];
                    $categories[$cKey]['parent'] = $parent;
                    $categories[$cKey]['path']   = $path;
                    $categories[$cKey]['type']   = '';
                    $categories[$cKey]['lock']   = $lock;
                    if ($rlDb->getOne('Key', "`Key`='{$key}'", 'categories')) {
                        array_push($existKeys, $cKey);
                    }
                }
            }
            unset($array_data);
            $sections = $rlCategories->getCatTree(0, false, true);
            $rlSmarty->assign_by_ref('sections', $sections_list);
            $rlSmarty->assign_by_ref('listing_types', $rlListingTypes->types);
            $rlSmarty->assign_by_ref('cats_array', $categories);
            $rlSmarty->assign_by_ref('exist_keys', $existKeys);
            $rlSmarty->assign('selector_tr', array(
                'key',
                'name',
                'parent',
                'path',
                'type',
                'lock'
            ));
        }
    }
    if (isset($_POST['categories_add'])) {
        $rlImportExportCategories->import();
    }
} else {
    if (!$_REQUEST['xjxfun']) {
        $sections = $rlCategories->getCatTree(0, false, true);
        $rlSmarty->assign_by_ref('sections', $sections);
    }
    if (isset($_POST['submit'])) {
        $rlImportExportCategories->export();
    }
    $rlXajax->registerFunction(array(
        'getCatLevel',
        $rlCategories,
        'ajaxGetCatLevel'
    ));
}