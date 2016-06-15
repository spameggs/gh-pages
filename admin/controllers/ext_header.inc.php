<?php
define('REALM', 'admin');
require_once(RL_CLASSES . 'rlDb.class.php');
require_once(RL_CLASSES . 'reefless.class.php');
$rlDb     = new rlDb();
$reefless = new reefless();
session_start();
if (!$reefless->checkSessionExpire()) {
    echo 'session_expired';
    exit;
}
$reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
$reefless->loadClass('Debug');
$reefless->loadClass('Valid');
$reefless->loadClass('Lang');
$reefless->loadClass('Config');
$reefless->loadClass('Hook');
$reefless->loadClass('Admin', 'admin');
if (!$rlAdmin->isLogin()) {
    exit;
}
$config = $rlConfig->allConfig();
$rlLang->extDefineLanguage();
$lang = $rlLang->getLangBySide('admin', RL_LANG_CODE, 'all');
require_once(RL_LIBS . 'system.lib.php');
$reefless->setTimeZone();
$reefless->loadClass('Cache');
$reefless->loadClass('ListingTypes');
function loadUTF8functions()
{
    $names = func_get_args();
    if (empty($names)) {
        return false;
    }
    foreach ($names as $name) {
        if (file_exists(RL_LIBS . 'utf8' . RL_DS . 'utils' . RL_DS . $name . '.php')) {
            require_once(RL_LIBS . 'utf8' . RL_DS . 'utils' . RL_DS . $name . '.php');
        }
    }
}
$rlHook->load('apExtHeader');