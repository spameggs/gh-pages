<?php
class rlDataEntriesImport extends reefless
{
    var $tmpFile;
    var $delimiters = array('new_line' => "\n", 'tab' => "\t", 'comma' => ',');
    var $delimiter;
    var $parentID;
    var $parentKey;
    var $data = array();
    function rlDataEntriesImport()
    {
        $this->tmpFile = RL_UPLOAD . 'dataEntriesImport.tmp';
    }
    function import($ext = false, $delimiter = false)
    {
        if (method_exists('rlDataEntriesImport', "import{$ext}")) {
            if ($delimiter == 'another') {
                $this->delimiter = $delimiter;
            } else {
                $this->delimiter = $this->delimiters[$delimiter];
            }
            $method = "import{$ext}";
            return $this->$method();
        }
        return false;
    }
    function importTXT()
    {
        global $rlValid, $rlActions;
        $file = fopen($this->tmpFile, 'r');
        if ($file) {
            $position = $this->getRow("SELECT MAX(`position`) AS `max` FROM `" . RL_DBPREFIX . "data_formats` WHERE `Parent_ID` = '{$this->parentID}'");
            $position = (int) $position['max'];
            while ($line = fgets($file)) {
                if ($this->delimiter == $this->delimiters['new_line']) {
                    if (!empty($line)) {
                        $dfName = $rlValid->xSql($line);
                        $position++;
                        $this->addItem($this->uniqKeyByName($dfName), trim($dfName), $position);
                    }
                } else {
                    $parse = explode($this->delimiter, $line);
                    foreach ($parse as $entry) {
                        if (!empty($entry)) {
                            $dfName = $rlValid->xSql($entry);
                            $position++;
                            $this->addItem($this->uniqKeyByName($dfName), trim($dfName), $position);
                        }
                    }
                }
            }
            fclose($file);
            return $this->save();
        }
        return false;
    }
    function importCSV()
    {
        global $rlValid;
        require_once(RL_PLUGINS . 'dataEntriesImport' . RL_DS . 'lib' . RL_DS . 'parsecsv.lib.php');
        $csv            = new parseCSV();
        $csv->delimiter = $this->delimiter;
        $csv->parse($this->tmpFile);
        if (!empty($csv->data)) {
            $position = $this->getRow("SELECT MAX(`position`) AS `max` FROM `" . RL_DBPREFIX . "data_formats` WHERE `Parent_ID` = '{$this->parentID}'");
            $position = (int) $position['max'];
            foreach ($csv->data as $key => $entry) {
                foreach ($entry as $eKey => $item) {
                    if (!empty($item)) {
                        $dfName = $rlValid->xSql($item);
                        $position++;
                        $this->addItem($this->uniqKeyByName($dfName), trim($dfName), $position);
                    }
                }
            }
            return $this->save();
        }
        return false;
    }
    function importXLS()
    {
        global $rlValid;
        require_once(RL_PLUGINS . 'dataEntriesImport' . RL_DS . 'lib' . RL_DS . 'reader.php');
        $xlsData = new Spreadsheet_Excel_Reader();
        $xlsData->setOutputEncoding('UTF-8');
        $xlsData->read($this->tmpFile);
        $position = $this->getRow("SELECT MAX(`position`) AS `max` FROM `" . RL_DBPREFIX . "data_formats` WHERE `Parent_ID` = '{$this->parentID}'");
        $position = (int) $position['max'];
        for ($i = 1; $i <= $xlsData->sheets[0]['numRows']; $i++) {
            for ($j = 1; $j <= $xlsData->sheets[0]['numCols']; $j++) {
                if (!empty($xlsData->sheets[0]['cells'][$i][$j])) {
                    $dfName = $rlValid->xSql($xlsData->sheets[0]['cells'][$i][$j]);
                    $position++;
                    $this->addItem($this->uniqKeyByName($dfName), trim($dfName), $position);
                }
            }
        }
        return $this->save();
    }
    function uniqKeyByName($name = false)
    {
        global $rlValid;
        if (false === function_exists('utf8_is_ascii')) {
            loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
        }
        if (!utf8_is_ascii($name)) {
            $name = utf8_to_ascii($name);
        }
        $name   = $rlValid->str2key($name);
        $exists = $this->getRow("SELECT COUNT(`Key`) AS `count` FROM `" . RL_DBPREFIX . "data_formats` WHERE `Key` REGEXP '^{$name}(_[0-9]+)*$'");
        if ($exists['count'] > 0) {
            return "{$name}_" . (int) ($exists['count'] + 1);
        }
        return $name;
    }
    function addItem($dfKey = false, $dfName = false, $dfPosition = false)
    {
        $dfKey                   = $this->parentKey . '_' . $dfKey;
        $this->data['formats'][] = array(
            'Key' => $dfKey,
            'Parent_ID' => $this->parentID,
            'Position' => $dfPosition
        );
        foreach ($GLOBALS['languages'] as $key => $language) {
            $this->data['lang_keys'][] = array(
                'Key' => "data_formats+name+{$dfKey}",
                'Value' => $dfName,
                'Code' => $language['Code'],
                'Module' => 'common'
            );
        }
    }
    function save()
    {
        global $rlActions;
        if ($rlActions->insert($this->data['formats'], 'data_formats')) {
            $affectedRows = mysql_affected_rows();
            $res          = $rlActions->insert($this->data['lang_keys'], 'lang_keys');
            return $affectedRows;
        }
        return false;
    }
    function ajaxGetDFLevel($parent = false, $df_level = false)
    {
        global $_response, $rlLang;
        $parent = (int) $parent;
        $sql    = "SELECT `ID`, `Key` FROM `" . RL_DBPREFIX . "data_formats` WHERE `Parent_ID` = '{$parent}'";
        $df     = $this->getAll($sql);
        $df     = $rlLang->replaceLangKeys($df, 'data_formats', 'name');
        if (!empty($df)) {
            $this->loadClass('Json');
            $_response->script("tmp_df_list[{$parent}] = " . $GLOBALS['rlJson']->encode($df) . ";");
            $_response->script("dfLevelHandler({$df_level});");
            unset($df);
        } else {
            $_response->script("clearDfLevels({$df_level});");
        }
        return $_response;
    }
}