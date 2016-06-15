<?php
set_time_limit(0);
require_once('../../includes/config.inc.php');
session_start();
require_once(RL_CLASSES . 'rlDb.class.php');
require_once(RL_CLASSES . 'reefless.class.php');
$rlDb     = new rlDb();
$reefless = new reefless();
$reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
$reefless->loadClass('Debug');
$reefless->loadClass('Config');
$config = $rlConfig->allConfig();
$reefless->loadClass('Cache');
$reefless->loadClass('Valid');
$reefless->loadClass('Hook');
$reefless->loadClass('Lang');
$reefless->loadClass('Actions');
$reefless->loadClass('ListingTypes', null, false, true);
$limit        = $_SESSION['iel_data']['post']['import_per_run'];
$start        = (int) $_GET['index'];
$account_info = $_SESSION['account'];
$reefless->loadClass('Account');
if (!$rlAccount->isLogin())
    exit;
foreach ($_SESSION['iel_data']['post']['rows_tmp'] as $index => $val) {
    $available_rows[] = $index;
}
$reefless->loadClass('ExportImport', null, 'export_import');
$rlExportImport->import($_SESSION['iel_data']['post']['data'], $available_rows, $_SESSION['iel_data']['post']['cols'], $_SESSION['iel_data']['post']['field'], $start, $limit, $_SESSION['iel_data']['post']['import_listing_type'], $_SESSION['iel_data']['post']['import_category_id'], $account_info['ID'], $_SESSION['iel_data']['post']['import_plan_id'], false, $_SESSION['iel_data']['post']['import_status'], true);
$items['from']  = $start + $limit;
$items['to']    = $start + ($limit * 2) - 1;
$items['count'] = count($available_rows);
echo $rlJson->encode($items);