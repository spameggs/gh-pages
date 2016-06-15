<?php
set_time_limit(0);
require_once('../../../includes/config.inc.php');
require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
require_once(RL_LIBS . 'system.lib.php');
$reefless->loadClass('Json');
$reefless->loadClass('Actions');
$reefless->loadClass('Cache');
$reefless->loadClass('Categories');
$limit = $_SESSION['iel_data']['post']['import_per_run'];
$start = (int) $_GET['index'];
foreach ($_SESSION['iel_data']['post']['rows_tmp'] as $index => $val) {
    $available_rows[] = $index;
}
$reefless->loadClass('ExportImport', null, 'export_import');
$rlExportImport->import($_SESSION['iel_data']['post']['data'], $available_rows, $_SESSION['iel_data']['post']['cols'], $_SESSION['iel_data']['post']['field'], $start, $limit, $_SESSION['iel_data']['post']['import_listing_type'], $_SESSION['iel_data']['post']['import_category_id'], $_SESSION['iel_data']['post']['import_account_id'], $_SESSION['iel_data']['post']['import_plan_id'], $_SESSION['iel_data']['post']['import_paid'], $_SESSION['iel_data']['post']['import_status']);
$items['from']  = $start + $limit;
$items['to']    = $start + ($limit * 2) - 1;
$items['count'] = count($available_rows);
echo $rlJson->encode($items);