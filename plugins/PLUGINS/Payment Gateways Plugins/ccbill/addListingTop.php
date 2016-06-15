<?php
global $rlDb, $rlSmarty;
if ($GLOBALS['config']['ccbill_module'] && !empty($_SESSION['add_listing']['plan_id'])) {
    $ccbill_allowedTypes = $GLOBALS['rlDb']->fetch(array(
        'ID',
        'ccbill_allowedTypes'
    ), array(
        'ID' => $_SESSION['add_listing']['plan_id']
    ), null, 1, 'listing_plans', 'row');
    $GLOBALS['rlSmarty']->assign('ccbill_allowedTypes', $ccbill_allowedTypes);
}