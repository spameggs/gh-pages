<?php
global $rlDb, $rlSmarty;
if ($GLOBALS['config']['ccbill_module']) {
    $plans_ccbill     = array();
    $sql              = "SELECT `ID`, `ccbill_allowedTypes` FROM `" . RL_DBPREFIX . "listing_plans` WHERE `Status` = 'active' ";
    $plans_ccbill_tmp = $GLOBALS['rlDb']->getAll($sql);
    foreach ($plans_ccbill_tmp as $pKey => $pVal) {
        $plans_ccbill[$pVal['ID']] = $pVal;
    }
    $GLOBALS['rlSmarty']->assign_by_ref('plans_ccbill', $plans_ccbill);
}