<?php
class rlDb
{
    var $tName = null;
    var $mysqlVer = null;
    var $calcRows = false;
    var $start = 0;
    function connect($host, $port = 3306, $user, $pass, $base_name)
    {
        if (!mysql_connect($host . ":" . $port, $user, $pass)) {
            die(mysql_error());
        }
        $db = mysql_select_db($base_name);
        if (!$db) {
            die(mysql_error());
        }
        $this->mysqlVer = version_compare("4.1", mysql_get_server_info(), "<=") ? "5" : "4";
        if ($this->mysqlVer == 5) {
            $this->query("SET NAMES `utf8`");
        }
    }
    function setTable($name)
    {
        $this->tName = $name;
    }
    function resetTable()
    {
        $this->tName = null;
    }
    function getAll($sql)
    {
        $this->calcTime();
        $res = mysql_query($sql);
        if (!$res) {
            $this->error($sql);
        }
        $ret = array();
        while ($row = mysql_fetch_assoc($res)) {
            $index       = count($ret);
            $ret[$index] = $row;
        }
        $this->calcTime('end', $sql);
        return $ret;
    }
    function getOne($field = false, $where = null, $table = null)
    {
        $this->calcTime();
        if ($table == null) {
            if ($this->tName != null) {
                $table = $this->tName;
            } else {
                return $this->tableNoSel();
            }
        }
        if (!$field || !$where) {
            return false;
        }
        $sql = "SELECT `{$field}` FROM `" . RL_DBPREFIX . "{$table}` WHERE {$where} LIMIT 1";
        $res = mysql_query($sql);
        if (!$res) {
            $this->error($sql);
        }
        $ret = mysql_result($res, 0, $field);
        $this->calcTime('end', $sql);
        return $ret;
    }
    function query($sql = false)
    {
        $this->calcTime();
        $res = mysql_query($sql);
        if (!$res) {
            $this->error($sql);
        }
        $this->calcTime('end', $sql);
        return $res;
    }
    function getRow($sql = false)
    {
        $this->calcTime();
        $res = mysql_query($sql);
        if (!$res) {
            $this->error($sql);
        }
        $row = mysql_fetch_assoc($res);
        $this->calcTime('end', $sql);
        return $row;
    }
    function fetch($fields = '*', $where = null, $options = null, $limit = null, $table = null, $action = 'all')
    {
        if ($table == null) {
            if ($this->tName != null) {
                $table = $this->tName;
            } else {
                return $this->tableNoSel();
            }
        }
        $query = "SELECT ";
        if ($this->calcRows) {
            $query .= "SQL_CALC_FOUND_ROWS ";
        }
        if (is_array($fields)) {
            foreach ($fields as $sel_field) {
                $query .= "`{$sel_field}`,";
            }
            $query = substr($query, 0, -1);
        } else {
            $query .= " * ";
        }
        $query .= " FROM `" . RL_DBPREFIX . $table . "` ";
        if (is_array($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $value) {
                $query .= " (`{$key}` = '{$where[$key]}') AND";
            }
            $query = substr($query, 0, -3);
        }
        if ($options != null) {
            $query .= " " . $options . " ";
        }
        if (is_array($limit)) {
            $query .= " LIMIT {$limit[0]}, {$limit[1]} ";
        } else {
            if ($action == 'row' && empty($limit)) {
                $limit = 1;
            }
            if (!empty($limit)) {
                $query .= " LIMIT {$limit} ";
            }
        }
        if ($action == 'row') {
            $output = $this->getRow($query);
        } else {
            $output = $this->getAll($query);
        }
        if ($this->calcRows) {
            $calc            = $this->getRow("SELECT FOUND_ROWS() AS `calc`");
            $this->foundRows = $calc['calc'];
        }
        return $output;
    }
    function error($query = false)
    {
        $error  = debug_backtrace();
        $levels = count($error);
        if (isset($_POST['xjxfun']) || $_GET['q'] == 'ext') {
            $error = $error[2];
            echo 'MYSQL ERROR' . PHP_EOL;
            echo 'Error: ' . mysql_error() . PHP_EOL;
            echo 'Query: ' . $query . PHP_EOL;
            if ($error['function']) {
                echo 'Function: ' . $error['function'] . PHP_EOL;
            }
            if ($error['class']) {
                echo 'Class: ' . $error['class'] . PHP_EOL;
            }
            if ($error['file']) {
                echo 'File: ' . $error['file'] . ' (line# ' . $error['line'] . ')' . PHP_EOL;
            }
        } else {
            $error = $levels > 1 ? $error[count($error) - 3] : $error[0];
            echo '<table style="width: 100%;font-family: Arial;font-size: 14px;">';
            echo '<tr><td colspan="2" style="color: red;font-weight: bold;">MYSQL ERROR</td></tr>';
            echo '<tr><td style="width: 90px;">Error:</td><td>' . mysql_error() . '</td></tr>';
            echo '<tr><td>Query:</td><td>' . $query . '</td></tr>';
            if ($error['function']) {
                echo '<tr><td>Function:</td><td>' . $error['function'] . '</td></tr>';
            }
            if ($error['class']) {
                echo '<tr><td>Class:</td><td>' . $error['class'] . '</td></tr>';
            }
            if ($error['file']) {
                echo '<tr><td>File:</td><td>' . $error['file'] . ' (line# ' . $error['line'] . ')</td></tr>';
            }
            echo '</table>';
        }
        exit;
    }
    function calcTime($mode = 'start', $sql = false)
    {
        if (!RL_DB_DEBUG) {
            return false;
        }
        if ($mode == 'start') {
            $time        = microtime();
            $time        = explode(" ", $time);
            $time        = $time[1] + $time[0];
            $this->start = $time;
        } else {
            $time      = microtime();
            $time      = explode(" ", $time);
            $time      = $time[1] + $time[0];
            $finish    = $time;
            $totaltime = ($finish - $this->start);
            $_SESSION['sql_debug_time'] += $totaltime;
            printf("The query took %f seconds to load.<br />", $totaltime);
            echo $sql . '<br /><br />';
        }
    }
    function tableNoSel()
    {
        RlDebug::logger("SQL query can't be run, it isn't table name selected", null, null, 'Warning');
        return 'Table not selected, see error log';
    }
}