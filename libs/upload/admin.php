<?php
include_once(dirname(__FILE__) . "/../../includes/config.inc.php");
require_once(RL_ADMIN_CONTROL . 'admin.control.inc.php');
$config = $rlConfig->allConfig();
$rlSmarty->assign_by_ref('config', $config);
$GLOBALS['config'] = $config;
$reefless->loadClass('Account');
$add_photo_path    = $rlDb->getOne('Path', "`Key` = 'add_photo'", 'pages');
$listing_id        = $_SESSION['admin_transfer']['listing_id'];
$upload_controller = 'admin.php';
if (!$listing_id)
    exit;
if (!$rlAdmin->isLogin())
    exit;
$reefless->loadClass('Json');
include_once(RL_LIBS . 'upload' . RL_DS . 'upload.php');