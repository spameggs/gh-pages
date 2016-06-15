<?php
require_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.inc.php');
require_once(RL_CLASSES . 'rlDb.class.php');
require_once(RL_CLASSES . 'reefless.class.php');
@ini_set('display_errors', false);
$rlDb     = new rlDb();
$reefless = new reefless();
$reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
$reefless->loadClass('Debug');
$reefless->loadClass('Config');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
require_once(RL_LIBS . 'kcaptcha' . RL_DS . 'rlKCaptcha.php');
session_start();
$captcha_id = $_GET['id'];
$captcha    = new rlKCaptcha($captcha_id);
if ($captcha_id) {
    $_SESSION['ses_security_code_' . $captcha_id] = $captcha->getKeyString();
} else {
    $_SESSION['ses_security_code'] = $captcha->getKeyString();
}