<?php
if ($_POST['import']) {
    $dump_sours = $_FILES['dump']['tmp_name'];
    $dump_file  = $_FILES['dump']['name'];
    preg_match("/(\.sql)/", $dump_file, $matches);
    if (strtolower($matches[1]) == '.sql') {
        if (is_readable($dump_sours)) {
            $dump_content = fopen($dump_sours, "r");
            $rlDb->query("SET NAMES `utf8`");
            if ($dump_content) {
                while ($query = fgets($dump_content, 10240)) {
                    $query = trim($query);
                    if ($query[0] == '#')
                        continue;
                    if ($query[0] == '-')
                        continue;
                    if ($query[strlen($query) - 1] == ';') {
                        $query_sql .= $query;
                    } else {
                        $query_sql .= $query;
                        continue;
                    }
                    if (!empty($query_sql) && empty($errors)) {
                        $query_sql = str_replace('{sql_prefix}', RL_DBPREFIX, $query_sql);
                    }
                    $res = $rlDb->query($query_sql);
                    if (!$res && count($errors) < 5) {
                        $errors[] = $lang['can_not_run_sql_query'] . mysql_error();
                    }
                    unset($query_sql);
                }
                fclose($sql_dump);
                if (empty($errors)) {
                    $rlNotice->saveNotice($lang['dump_imported']);
                    $aUrl = array(
                        "controller" => $controller
                    );
                    $reefless->redirect($aUrl);
                } else {
                    $errors[] = $lang['dump_query_corrupt'];
                }
            } else {
                $errors[] = $lang['dump_has_not_content'];
            }
        } else {
            $errors[] = $lang['can_not_read_file'];
            trigger_error("Can not to read uploaded file | Database Import", E_WARNING);
            $rlDebug->logger("Can not to read uploaded file | Database Import");
        }
    } else {
        $errors[] = $lang['incorrect_dump_file'];
    }
    if (!empty($errors)) {
        $rlSmarty->assign_by_ref('errors', $errors);
    }
}
$rlHook->load('apPhpDatabaseBottom');
$db_tables = $rlDb->getAll("SHOW TABLES");
$rlSmarty->assign_by_ref('db_tables', $db_tables);
$rlXajax->registerFunction(array(
    'runSqlQuery',
    $rlAdmin,
    'ajaxRunSqlQuery'
));