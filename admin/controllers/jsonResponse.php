<?php
require_once('../../includes/config.inc.php');
require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
require_once(RL_LIBS . 'system.lib.php');
switch ($_GET['q']) {
    case 'phrase':
        $key    = $_GET['key'];
        $lang   = $_GET['lang'];
        $output = $rlDb->getOne('Value', "`Key` = '{$key}' AND `Code` = '{$lang}'", 'lang_keys');
        break;
    case 'accounts':
        $str    = $_GET['str'];
        $fields = $_GET['add_id'] ? ', `ID`' : '';
        $output = array();
        if (!empty($str)) {
            $sql    = "SELECT `Username` {$fields} FROM `" . RL_DBPREFIX . "accounts` WHERE `Username` REGEXP '^{$str}' AND `Status` ='active'";
            $output = $rlDb->getAll($sql);
        }
        break;
    default:
        exit;
        break;
}
$rlHook->load('apPhpJsonResponse');
$reefless->loadClass('Json');
echo $rlJson->encode($output);