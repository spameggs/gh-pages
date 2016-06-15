<?php
$reefless->loadClass('Listings');
$reefless->loadClass('Actions');
$reefless->loadClass('Crop');
$reefless->loadClass('Resize');
$id = $_GET['id'] ? (int) $_GET['id'] : $_SESSION['add_photo']['listing_id'];
$rlSmarty->assign_by_ref('listing_id', $id);
$_SESSION['add_photo']['listing_id'] = $id;
$listing                             = $rlListings->getShortDetails($id, $plan_info = true);
$rlSmarty->assign_by_ref('listing', $listing);
$photos_allow = $listing['Plan_image'];
$listing_type = $rlListingTypes->types[$listing['Listing_type']];
$rlSmarty->assign_by_ref('listing_type', $listing_type);
$rlHook->load('addPhotoTop');
if (!isset($account_info) || empty($id) || empty($listing) || ($listing['Plan_image'] == 0 && !$listing['Image_unlim']) || !$listing_type['Photo'] || $listing['Account_ID'] != $account_info['ID']) {
    $sError = true;
} else {
    $bc_last        = array_pop($bread_crumbs);
    $bread_crumbs[] = array(
        'name' => $lang['pages+name+' . $listing_type['My_key']],
        'title' => $lang['pages+title+' . $listing_type['My_key']],
        'path' => $pages[$listing_type['My_key']]
    );
    $bread_crumbs[] = $bc_last;
    $plan_info      = array(
        'Image_unlim' => $listing['Image_unlim'],
        'Image' => $listing['Plan_image']
    );
    $rlSmarty->assign_by_ref('plan_info', $plan_info);
    $rlXajax->registerFunction(array(
        'makeMain',
        $rlListings,
        'ajaxMakeMain'
    ));
    $rlXajax->registerFunction(array(
        'editDesc',
        $rlListings,
        'ajaxEditDesc'
    ));
    $rlXajax->registerFunction(array(
        'reorderPhoto',
        $rlListings,
        'ajaxReorderPhoto'
    ));
    $rlXajax->registerFunction(array(
        'crop',
        $rlCrop,
        'ajaxCrop'
    ));
    $rlSmarty->assign_by_ref('allowed_photos', $plan_info['Image']);
    $max_file_size = str_replace('M', '', ini_get('upload_max_filesize'));
    $rlSmarty->assign_by_ref('max_file_size', $max_file_size);
    $rlHook->load('addPhotoBottom');
}