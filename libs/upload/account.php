<?php
include_once(dirname(__FILE__) . "/../../includes/config.inc.php");
require_once(RL_INC . 'control.inc.php');
$config = $rlConfig->allConfig();
$rlSmarty->assign_by_ref('config', $config);
$GLOBALS['config'] = $config;
$reefless->loadClass('Account');
$add_photo_path    = $rlDb->getOne('Path', "`Key` = 'add_photo'", 'pages');
$listing_id        = false !== strpos($_SERVER['HTTP_REFERER'], $add_photo_path) ? (int) $_SESSION['add_photo']['listing_id'] : (int) $_SESSION['add_listing']['listing_id'];
$account_info      = $_SESSION['account'];
$upload_controller = 'account.php';
if (!$listing_id)
    exit;
if (!$rlAccount->isLogin())
    exit;
if ($account_info['ID'] != $rlDb->getOne('Account_ID', "`ID` = '{$listing_id}'", 'listings'))
    exit;
$reefless->loadClass('Json');
include_once(RL_LIBS . 'upload' . RL_DS . 'upload.php');