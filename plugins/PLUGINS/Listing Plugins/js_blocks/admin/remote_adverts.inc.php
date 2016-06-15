<?php
$listing_type = current($rlListingTypes->types);
$listing_type = $listing_type['Key'];
$rlSmarty->assign('listing_types', $rlListingTypes->types);
$rlSmarty->assign('listing_type', $listing_type);
$categories = $rlCategories->getCategories(0, $listing_type);
$rlSmarty->assign('categories', $categories);
$reefless->loadClass('RemoteAdverts', null, 'js_blocks');
$rlXajax->registerFunction(array(
    'loadCategories',
    $rlRemoteAdverts,
    'ajaxLoadCategories'
));
$box_id = "ra" . mt_rand();
$out    = '<div id="' . $box_id . '"> </div>';
$out .= '<script async="true" type="text/javascript" src="' . RL_PLUGINS_URL . 'js_blocks/blocks.inc.php[aurl]"></script>';
$rlSmarty->assign('out', $out);
$rlSmarty->assign('box_id', $box_id);