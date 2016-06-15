<?php
define('REALM', 'admin');
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: store, no-cache, max-age=3600, must-revalidate");
session_start();
require_once(RL_CLASSES . 'rlDb.class.php');
require_once(RL_CLASSES . 'reefless.class.php');
$rlDb     = new rlDb();
$reefless = new reefless();
$reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
$reefless->loadClass('Debug');
$reefless->loadClass('Config');
$reefless->loadClass('Lang');
$reefless->loadClass('Valid');
$reefless->loadClass('Notice');
$reefless->loadClass('Admin', 'admin');
$reefless->loadClass('Actions');
$reefless->loadClass('Hook');
$reefless->loadClass('Common');
require_once(RL_AJAX . 'xajax_core' . RL_DS . 'xajax.inc.php');
$rlXajax              = new xajax();
$_response            = new xajaxResponse();
$GLOBALS['_response'] = $_response;
$rlXajax->configure('javascript URI', RL_URL_HOME . 'libs/ajax/');
$rlXajax->configure('debug', RL_AJAX_DEBUG);
$rlXajax->setCharEncoding('UTF-8');
$reefless->loadClass('AjaxAdmin', 'admin');
$rlXajax->registerFunction(array(
    'logIn',
    $rlAjaxAdmin,
    'ajaxLogIn'
));
$rlXajax->registerFunction(array(
    'logOut',
    $rlAjaxAdmin,
    'ajaxLogOut'
));
$rlXajax->registerFunction(array(
    'removeTmpFile',
    $reefless,
    'ajaxRemoveTmpFile'
));
require_once(RL_LIBS . 'smarty' . RL_DS . 'Smarty.class.php');
$reefless->loadClass('Smarty');
$rlSmarty->register_function('fckEditor', array(
    'rlSmarty',
    'fckEditor'
));
$rlSmarty->register_function('getTmpFile', array(
    'reefless',
    'getTmpFile'
));
$rlSmarty->register_function('str2path', array(
    'rlSmarty',
    'str2path'
));
$rlSmarty->register_function('str2money', array(
    'rlSmarty',
    'str2money'
));
$rlSmarty->register_function('rlHook', array(
    'rlHook',
    'load'
));
define('RL_SETUP', '');
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
if (get_magic_quotes_gpc()) {
    if (isset($_SERVER['CONTENT_TYPE'])) {
        $in = array(
            &$_GET,
            &$_POST
        );
        while (list($k, $v) = each($in)) {
            foreach ($v as $key => $val) {
                if (!is_array($val)) {
                    $in[$k][$key] = str_replace(array(
                        "\'",
                        '\"'
                    ), array(
                        "'",
                        '"'
                    ), $val);
                    continue;
                }
                $in[] =& $in[$k][$key];
            }
        }
        unset($in);
    }
}