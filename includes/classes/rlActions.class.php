<?php
class rlActions extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    var $rlAllowHTML = false;
    var $mysqlFunctions = array('NOW()', 'DATE_ADD()', 'IF()');
    function rlActions()
    {
        global $rlLang, $rlValid, $rlConfig;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
    }
    function delete($fields = false, $table = false, $options = false, $limit = 1, $key = false, $lang_keys = false, $className = false, $deleteMethod = false, $restoreMethod = false, $plugin = false)
    {
        if (!is_array($table)) {
            $table = array(
                $table
            );
        }
        if ($GLOBALS['config']['trash']) {
            $this->trash($fields, $table, $key, $lang_keys, $className, $deleteMethod, $restoreMethod, $plugin);
            $this->action = "dropped";
        } else {
            $this->remove($fields, $table, $key, $options, $lang_keys, $className, $deleteMethod, $restoreMethod, $plugin);
            $this->action = "deleted";
        }
    }
    function remove($fields = false, $table = false, $key = false, $options = false, $lang_keys = false, $className = false, $deleteMethod = false, $restoreMethod = false)
    {
        if (!is_array($fields)) {
            $msg = "remove method can not be run, incorrect structure of \$data parameter";
            trigger_error($msg, E_WARNING);
            $GLOBALS['rlDebug']->logger($msg);
            return false;
        }
        if (empty($table)) {
            $msg = "remove method can not be run, database table does not chose";
            trigger_error($msg, E_WARNING);
            $GLOBALS['rlDebug']->logger($msg);
            return false;
        }
        foreach ($table as $tbl) {
            $sql = "DELETE FROM `" . RL_DBPREFIX . $tbl . "` WHERE ";
            foreach ($fields as $field => $value) {
                if ($table[0] == 'accounts' && $tbl != 'accounts' && $field == 'ID') {
                    $field = "Account_ID";
                }
                $criterion .= "`{$field}` = '{$value}' AND ";
                $sql .= "`{$field}` = '{$value}' AND ";
            }
            if ($options != null && !empty($options) && $tbl != 'lang_keys') {
                $sql .= $options;
            } else {
                $sql = substr($sql, 0, -4);
            }
            if ($limit != null) {
                $sql .= " LIMIT 1";
            }
            $this->query($sql);
        }
        if (!empty($lang_keys)) {
            foreach ($lang_keys as $lKey => $lVal) {
                $d_sql = "DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = '{$lang_keys[$lKey]['Key']}'";
                $this->query($d_sql);
            }
        }
        if ($className && $deleteMethod) {
            $this->loadClass($className, null, $plugin);
            $className = 'rl' . $className;
            if (!method_exists($className, $deleteMethod)) {
                $GLOBALS['rlDebug']->logger("There are not such method ({$deleteMethod}) in loaded class ({$className})");
                return false;
            }
            global $$className;
            $$className->$deleteMethod($key);
        }
        return true;
    }
    function trash($fields = false, $tables = false, $key = false, $lang_keys = false, $className = false, $deleteMethod = false, $restoreMethod = false, $plugin = false)
    {
        if (!is_array($fields)) {
            $msg = "trash method can not be run, incorrect structure of \$fields parameter";
            trigger_error($msg, E_WARNING);
            $GLOBALS['rlDebug']->logger($msg);
            return false;
        }
        if (empty($tables)) {
            $msg = "trash method can not be run, System zones does not chose";
            trigger_error($msg, E_WARNING);
            $GLOBALS['rlDebug']->logger($msg);
            return false;
        }
        foreach ($tables as $table) {
            $sql = "UPDATE `" . RL_DBPREFIX . $table . "` SET ";
            $sql .= "`Status` = 'trash' WHERE ";
            $criterion = '';
            foreach ($fields as $field => $value) {
                $criterion .= "`{$field}` = '{$value}' AND ";
                if ($tables[0] == 'accounts' && $table != 'accounts' && $field == 'ID') {
                    $field = "Account_ID";
                }
                $sql .= "`{$field}` = '{$value}' AND";
            }
            $sql = substr($sql, 0, -3);
            $this->query($sql);
            $tables_set .= $table . ",";
        }
        $tables_set = substr($tables_set, 0, -1);
        if (!empty($lang_keys)) {
            foreach ($lang_keys as $lKey => $lVal) {
                $l_update[$lKey]['where']  = $lang_keys[$lKey];
                $l_update[$lKey]['fields'] = array(
                    'Status' => 'trash'
                );
            }
            $this->update($l_update, 'lang_keys');
        }
        $criterion = substr($criterion, 0, -4);
        $admin     = (defined('REALM') && REALM == 'admin') ? $_SESSION['sessAdmin']['user_id'] : 0;
        $qTrash    = array(
            'Zones' => $tables_set,
            'Key' => $key,
            'Criterion' => $criterion,
            'Class_name' => $className,
            'Restore_method' => $restoreMethod,
            'Remove_method' => $deleteMethod,
            'Admin_ID' => $admin,
            'Date' => 'NOW()',
            'Lang_keys' => serialize($lang_keys),
            'Plugin' => $plugin
        );
        $this->insertOne($qTrash, 'trash_box');
        return true;
    }
    function insertOne($data, $table = null, $html_fields = false)
    {
        if ($table == null) {
            if ($this->tName != null) {
                $table = $this->tName;
            } else {
                return $this->tableNoSel();
            }
        }
        if (empty($data)) {
            trigger_error("Requested parameters is empty | insertOne()", E_WARNING);
            $GLOBALS['rlDebug']->logger("Requested parameters is empty | insertOne()");
            return true;
        }
        $sql = "INSERT INTO `" . RL_DBPREFIX . $table . "` ( ";
        foreach ($data as $field => $value) {
            $sql .= "`{$field}`, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " ) VALUES ( ";
        foreach ($data as $field => $value) {
            $value = $this->rlValid->xSql($value);
            if (in_array($field, $html_fields)) {
                $value = $this->rlValid->stripJS($value);
            } elseif (!defined('REALM') && REALM != 'admin' && !$this->rlAllowHTML) {
                $value = $this->rlValid->xHtml($value);
            }
            if (in_array($value, $this->mysqlFunctions)) {
                $sql .= "{$value}, ";
            } else {
                $sql .= "'{$value}', ";
            }
        }
        $sql = substr($sql, 0, -2);
        $sql .= " )";
        return $this->query($sql);
    }
    function insert($data, $table = null)
    {
        if ($table == null) {
            if ($this->tName != null) {
                $table = $this->tName;
            } else {
                return $this->tableNoSel();
            }
        }
        if (empty($data)) {
            trigger_error("Requested parameters is empty | insert()", E_WARNING);
            $GLOBALS['rlDebug']->logger("Requested parameters is empty | insert()");
            return true;
        }
        $sql     = "INSERT INTO `" . RL_DBPREFIX . $table . "` ( ";
        $min_key = array_shift(array_keys($data));
        foreach ($data[$min_key] as $field => $value) {
            $sql .= "`{$field}`, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " ) VALUES ";
        foreach ($data as $item => $row) {
            $sql .= " ( ";
            foreach ($data[$item] as $field => $value) {
                $value = $this->rlValid->xSql($value);
                if (!defined('REALM') && REALM != 'admin' && !$this->rlAllowHTML) {
                    $value = $this->rlValid->xHtml($value);
                }
                if (in_array($value, $this->mysqlFunctions)) {
                    $sql .= "{$value}, ";
                } else {
                    $sql .= "'{$value}', ";
                }
            }
            $sql = substr($sql, 0, -2);
            $sql .= " ), ";
        }
        $sql = substr($sql, 0, -2);
        return $this->query($sql);
    }
    function update($data, $table = null)
    {
        if ($table == null) {
            if ($this->tName != null) {
                $table = $this->tName;
            } else {
                return $this->tableNoSel();
            }
        }
        if (is_array($data) && !empty($data)) {
            foreach ($data as $index => $rec) {
                $sql = "UPDATE `" . RL_DBPREFIX . $table . "` SET ";
                if (!is_array($data[$index]['fields'])) {
                    return false;
                }
                if (!is_array($data[$index]['where'])) {
                    return false;
                }
                foreach ($data[$index]['fields'] as $field => $value) {
                    $value = $this->rlValid->xSql($value);
                    if (!defined('REALM') && REALM != 'admin' && !$this->rlAllowHTML) {
                        $value = $this->rlValid->xHtml($value);
                    }
                    $sql .= "`{$field}` = ";
                    if (in_array($value, $this->mysqlFunctions)) {
                        $sql .= "{$value}";
                    } else {
                        $sql .= "'{$value}'";
                    }
                    $sql .= ", ";
                }
                $sql = substr($sql, 0, -2);
                $sql .= "WHERE ";
                foreach ($data[$index]['where'] as $field => $value) {
                    $sql .= "`{$field}` = '{$value}' AND ";
                }
                $sql = substr($sql, 0, -4);
                $this->query($sql);
            }
            return true;
        } else {
            trigger_error("Requested parameters is empty | update()", E_WARNING);
            $GLOBALS['rlDebug']->logger("Requested parameters is empty | update()");
            return true;
        }
    }
    function updateOne($data, $table = null, $html_fields = false)
    {
        if ($table == null) {
            if ($this->tName != null) {
                $table = $this->tName;
            } else {
                return $this->tableNoSel();
            }
        }
        if (empty($data)) {
            trigger_error("Requested parameters is empty | updateOne()", E_WARNING);
            $GLOBALS['rlDebug']->logger("Requested parameters is empty | updateOne()");
            return true;
        }
        $sql = "UPDATE `" . RL_DBPREFIX . $table . "` SET ";
        foreach ($data['fields'] as $field => $value) {
            preg_match('/^([^\(][A-Z]+)+/', $value, $matches);
            $value = $this->rlValid->xSql($value);
            if (in_array($field, $html_fields)) {
                $value = $this->rlValid->stripJS($value);
            } elseif (!defined('REALM') && REALM != 'admin' && !$this->rlAllowHTML && !in_array($matches[0] . '()', $this->mysqlFunctions)) {
                $value = $this->rlValid->xHtml($value);
            }
            $sql .= "`{$field}` = ";
            if (in_array($value, $this->mysqlFunctions) || in_array($matches[0] . '()', $this->mysqlFunctions)) {
                $sql .= "{$value}";
            } else {
                $sql .= "'{$value}'";
            }
            $sql .= ", ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE ";
        foreach ($data['where'] as $field => $value) {
            $sql .= "`{$field}` = '{$value}' AND ";
        }
        $sql = substr($sql, 0, -4);
        return $this->query($sql);
    }
    function upload($field = false, $file = false, $resize_type = false, $resolution = false, $parent = false, $watermark = true)
    {
        global $config, $l_deny_files_regexp, $rlHook;
        $tmp = $_SESSION['tmp_files'];
        $rlHook->load('phpUpload');
        if ((is_readable(RL_UPLOAD . $tmp[$parent][$field]) && $tmp[$parent][$field]) || (is_readable(RL_UPLOAD . $tmp[$field]) && $tmp[$field])) {
            $file_tmp_name = $_SESSION['tmp_files'][$parent][$field] ? $_SESSION['tmp_files'][$parent][$field] : $_SESSION['tmp_files'][$field];
            if (preg_match($l_deny_files_regexp, $file_tmp_name)) {
                return false;
            }
            $file_ext  = pathinfo($file_tmp_name, PATHINFO_EXTENSION);
            $file_name = $file . '.' . $file_ext;
            $file_dir  = RL_UPLOAD . $file_tmp_name;
        } else {
            $file_tmp_name = $parent && $_FILES[$parent] ? $_FILES[$parent]['name'][$field] : $_FILES[$field]['name'];
            if (preg_match($l_deny_files_regexp, $file_tmp_name)) {
                return false;
            }
            $file_type    = $parent && $_FILES[$parent] ? $_FILES[$parent]['type'][$field] : $_FILES[$field]['type'];
            $file_tmp_dir = $parent && $_FILES[$parent] ? $_FILES[$parent]['tmp_name'][$field] : $_FILES[$field]['tmp_name'];
            $file_ext     = pathinfo($file_tmp_name, PATHINFO_EXTENSION);
            $file_name    = $file . '.' . $file_ext;
            $file_dir     = RL_UPLOAD . $file_name;
            if (move_uploaded_file($file_tmp_dir, $file_dir)) {
                chmod($file_dir, 0777);
            } else {
                trigger_error("Unable to move_uploaded_file", E_WARNING);
                $GLOBALS['rlDebug']->logger("Unable to move_uploaded_file");
            }
        }
        if (is_readable($file_dir)) {
            $final_distanation = RL_FILES . $file_name;
            if (!empty($resize_type) && !empty($resolution)) {
                $this->loadClass('Resize');
                $this->loadClass('Crop');
                if (is_array($resolution)) {
                    $GLOBALS['rlCrop']->loadImage($file_dir);
                    $GLOBALS['rlCrop']->cropBySize($resolution[0], $resolution[1], ccTOP);
                    $GLOBALS['rlCrop']->saveImage($file_dir, $config['img_quality']);
                    $GLOBALS['rlCrop']->flushImages();
                }
                $resize_type = strtoupper($resize_type);
                $GLOBALS['rlResize']->resize($file_dir, $final_distanation, $resize_type, $resolution, true, $watermark);
            } else {
                copy($file_dir, $final_distanation);
            }
            if (is_readable($final_distanation)) {
                chmod($final_distanation, 0644);
                unlink($file_dir);
                unset($_SESSION['tmp_files'][$parent]);
                unset($_SESSION['tmp_files'][$field]);
                return $file_name;
            }
        }
        return false;
    }
    function enumAdd($table = false, $field = false, $value = false)
    {
        $this->enum($table, $field, $value, 'add');
    }
    function enumRemove($table = false, $field = false, $value = false)
    {
        $this->enum($table, $field, $value, 'remove');
    }
    function enum($table = false, $field = false, $value = false, $mode = 'add')
    {
        if (!$table || !$field || !$value) {
            return false;
        }
        $sql      = "SHOW COLUMNS FROM `" . RL_DBPREFIX . $table . "` LIKE '{$field}'";
        $enum_row = $this->getRow($sql);
        preg_match('/([a-z]*)\((.*)\)/', $enum_row['Type'], $matches);
        if (!in_array(strtolower($matches[1]), array(
            'enum',
            'set'
        ))) {
            die('ENUM add/edit method (table: ' . $table . '): <b>' . $field . '</b> field is not ENUM or SET type field');
            return false;
        }
        $enum_values = explode(',', $matches[2]);
        if ($mode == 'add') {
            if (false !== array_search("'{$value}'", $enum_values)) {
                die('ENUM add/edit method (table: ' . $table . '): <b>' . $field . '</b> field already has <b>' . $value . '</b> value');
                return false;
            }
            array_push($enum_values, "'{$value}'");
        } elseif ($mode == 'remove') {
            $pos = array_search("'{$value}'", $enum_values);
            if ($pos === false) {
                return false;
            }
            unset($enum_values[$pos]);
            if (empty($enum_values)) {
                die('ENUM add/edit method (table: ' . $table . '): <b>' . $field . '</b> field will not has any values after your remove');
                return false;
            }
            $enum_values = array_values($enum_values);
        }
        $sql = "ALTER TABLE `" . RL_DBPREFIX . $table . "` CHANGE `{$field}` `{$field}` " . strtoupper($matches[1]) . "( " . implode(',', $enum_values) . " ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
        if (strtolower($matches[1]) == 'enum') {
            $sql .= " DEFAULT {$enum_values[0]}";
        }
        $this->query($sql);
        return true;
    }
    function tableNoSel()
    {
        RlDebug::logger("SQL query can't be run, it isn't table name selected", null, null, 'Warning');
        return 'Table not selected, see error log';
    }
}