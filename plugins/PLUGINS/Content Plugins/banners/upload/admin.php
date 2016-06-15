<?php
include_once(dirname(__FILE__) . "/../../../includes/config.inc.php");
require_once(RL_ADMIN_CONTROL . 'admin.control.inc.php');
$config = $rlConfig->allConfig();
$rlSmarty->assign_by_ref('config', $config);
$GLOBALS['config'] = $config;
$banner_id         = (int) $_SESSION['banner_id'];
if (!$rlAdmin->isLogin())
    exit;
$reefless->loadClass('Json');
include_once(RL_PLUGINS . 'banners' . RL_DS . 'upload' . RL_DS . 'upload.php');