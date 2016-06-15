<?php
class rlImportExportCategories extends reefless
{
    function str2path($str, $keep_slashes = false)
    {
        if ($keep_slashes) {
            $rx = '\/';
        }
        $str = preg_replace("/[^a-z0-9{$rx}\.]+/i", '-', $str);
        $str = preg_replace('/\-+/', '-', $str);
        $str = strtolower($str);
        $str = trim($str, '-');
        $str = trim($str, '/');
        $str = trim($str);
        return empty($str) ? false : $str;
    }
    function import()
    {
        global $lang, $rlActions, $rlValid, $rlCache;
        $import_data = $_POST['categories'];
        if (!empty($import_data)) {
            $addedCategoryCount = 0;
            $langs_add          = false;
            $maxPos             = $this->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "categories`");
            $maxPos             = (int) $maxPos['max'];
            foreach ($import_data as $iKey => $iCat) {
                if ($import_data[$iKey]['checkbox'] && !$import_data[$iKey]['exist']) {
                    $maxPos++;
                    $adaptParent = $rlValid->str2key($iCat['parent']);
                    $parentInfo  = $this->getRow("SELECT `ID`, `Level`, `Position` FROM `" . RL_DBPREFIX . "categories` WHERE `Key` = '{$adaptParent}' LIMIT 1");
                    $parent_id   = (int) $parentInfo['ID'];
                    $c_key       = $rlValid->xSql($iCat['key']);
                    $c_name      = $rlValid->xSql($iCat['name']);
                    $cLevel      = $parent_id ? $parentInfo['Level'] + 1 : 0;
                    $cTree       = $parent_id ? "{$parentInfo['Position']}.{$maxPos}" : $maxPos;
                    $sql         = "INSERT INTO `" . RL_DBPREFIX . "categories` ( `Key`, `Parent_ID`, `Position`, `Path`, `Type`, `Lock`, `Modified`, `Status`, `Level`, `Tree` ) VALUES ";
                    $sql .= "( '{$c_key}', '{$parent_id}', '{$maxPos}', '{$iCat['path']}', '{$iCat['type']}', '{$iCat['lock']}', NOW(), 'active', '{$cLevel}', '{$cTree}' )";
                    if ($this->query($sql)) {
                        $addedCategoryCount++;
                        foreach ($GLOBALS['languages'] as $lkey => $lvalue) {
                            $lang_keys[] = array(
                                'Code' => $GLOBALS['languages'][$lkey]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'categories+name+' . $c_key,
                                'Value' => $c_name
                            );
                        }
                    }
                }
            }
            if (!empty($lang_keys)) {
                $rlActions->insert($lang_keys, 'lang_keys');
            }
            $rlCache->updateCategories();
            $this->loadClass('Notice');
            $GLOBALS['rlNotice']->saveNotice(str_replace("[count]", $addedCategoryCount, $lang['importExportCategories_count']));
            $this->redirect(array(
                "controller" => "importExportCategories"
            ));
        }
    }
    function export()
    {
        global $lang;
        if (isset($_POST['cat_sticky'])) {
            $categoriesExport = array();
            $tmpCategories    = $this->getAll("SELECT `ID` FROM `" . RL_DBPREFIX . "categories`");
            foreach ($tmpCategories as $key => $entry) {
                array_push($categoriesExport, $entry['ID']);
            }
            unset($tmpCategories);
        } else {
            $categoriesExport = $_POST['categories'];
        }
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=categoies.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        if (!empty($categoriesExport)) {
            $categories = '';
            foreach ($categoriesExport as $key => $id) {
                $sql     = "SELECT `Key`, `Parent_ID`, `Path`, `Lock` FROM `" . RL_DBPREFIX . "categories` WHERE `ID` = '{$id}'";
                $catInfo = $this->getRow($sql);
                if ($catInfo['Parent_ID'] != 0) {
                    $parent_key        = $this->getOne('Key', "`ID`='{$catInfo['Parent_ID']}'", 'categories');
                    $catInfo['parent'] = $lang['categories+name+' . $parent_key];
                } else {
                    $catInfo['parent'] = "";
                }
                unset($catInfo['Parent_ID']);
                $catInfo['name'] = $lang['categories+name+' . $catInfo['Key']];
                $categories .= "{$catInfo['name']}\t{$catInfo['parent']}\t{$catInfo['Path']}\t{$catInfo['Lock']}\n";
            }
            echo $categories;
            unset($categories);
            exit;
        } else {
            echo $lang['importExportCategories_empty'];
            exit;
        }
    }
}