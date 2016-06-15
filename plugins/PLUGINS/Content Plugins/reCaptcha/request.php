<?php
session_start();
include_once(dirname(__FILE__) . "/../../includes/config.inc.php");
require_once(RL_CLASSES . 'rlDb.class.php');
require_once(RL_CLASSES . 'reefless.class.php');
$rlDb     = new rlDb();
$reefless = new reefless();
$reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
$config['reCaptcha_private_key'] = $rlDb->getOne('Default', "`Key` = 'reCaptcha_private_key'", 'config');
if (!extension_loaded('curl')) {
    echo false;
    exit;
}
$challenge = $_POST['challenge'];
$response  = $_POST['response'];
$id        = $_POST['id'];
$sess_key  = 'ses_security_code' . $id;
$url       = 'http://www.google.com/recaptcha/api/verify';
$url .= '?privatekey=' . trim($config['reCaptcha_private_key']);
$url .= '&remoteip=' . $_SERVER['REMOTE_ADDR'];
$url .= '&challenge=' . $challenge;
$url .= '&response=' . $response;
if ($_SESSION[$sess_key] == $response)
    return;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
$content = curl_exec($ch);
curl_close($ch);
preg_match('/^([true|false]+)/', $content, $matches);
if ($matches[0] == 'true') {
    $_SESSION[$sess_key] = $response;
} else {
    $_SESSION[$sess_key] = empty($_SESSION[$sess_key]) ? mt_rand() : $_SESSION[$sess_key];
}