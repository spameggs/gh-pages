<?php
$reefless->loadClass('VBulletin', null, 'vbulletin');
if (false === $rlVBulletin->fetchImportLogs(true)) {
    $errors = $lang['vbulletin_settingsEmpty'];
}
$rlXajax->registerFunction(array(
    'installProduct',
    $rlVBulletin,
    'ajaxInstallProduct'
));
$rlXajax->registerFunction(array(
    'importFromFlynax',
    $rlVBulletin,
    'ajaxImportFromFlynax'
));
$rlXajax->registerFunction(array(
    'importFromVBulletin',
    $rlVBulletin,
    'ajaxImportFromVBulletin'
));