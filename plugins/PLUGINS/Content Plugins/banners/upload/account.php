<?php
include_once(dirname(__FILE__) . "/../../../includes/config.inc.php");
require_once(RL_INC . 'control.inc.php');
$config = $rlConfig->allConfig();
$rlSmarty->assign_by_ref('config', $config);
$GLOBALS['config'] = $config;
$reefless->loadClass('Account');
$edit_banner_path = $rlDb->getOne('Path', "`Key` = 'banners_edit_banner'", 'pages');
$banner_id        = false !== strpos($_SERVER['HTTP_REFERER'], $edit_banner_path) ? (int) $_SESSION['edit_banner']['banner_id'] : (int) $_SESSION['add_banner']['banner_id'];
$account_info     = $_SESSION['account'];
if (!$banner_id)
    exit;
if (!$rlAccount->isLogin())
    exit;
if ($account_info['ID'] != $rlDb->getOne('Account_ID', "`ID` = '{$banner_id}'", 'banners'))
    exit;
$reefless->loadClass('Json');
include_once(RL_PLUGINS . 'banners' . RL_DS . 'upload' . RL_DS . 'upload.php');