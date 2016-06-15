<?php
$rlDb->setTable('listing_groups');
$groups = $rlDb->fetch(array(
    'ID',
    'Key'
), array(
    'Status' => 'active'
));
$groups = $rlLang->replaceLangKeys($groups, 'listing_groups', array(
    'name'
), RL_LANG_CODE, 'admin');
$rlSmarty->assign_by_ref('groups', $groups);
$rlSmarty->assign_by_ref('Actions');
$rlSmarty->assign_by_ref('Notice');
$reefless->loadClass('LocationFinder', null, 'locationFinder');
$rlXajax->registerFunction(array(
    'save',
    $rlLocationFinder,
    'ajaxSave'
));