<?php
$reefless->loadClass('Listings');
$sql = "SELECT `T1`.*, `T2`.`Path`, `T2`.`Type` AS `Listing_type`, `T2`.`Key` AS `Cat_key`, `T2`.`Type` AS `Cat_type`, `T2`.`Parent_ID` AS `Cat_parent_ID`, ";
$sql .= "`T3`.`Image`, `T3`.`Image_unlim`, `T3`.`Video`, `T3`.`Video_unlim`, CONCAT('categories+name+', `T2`.`Key`) AS `Category_pName`, ";
$sql .= "IF ( UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T3`.`Listing_period` DAY)) <= UNIX_TIMESTAMP(NOW()) AND `T3`.`Listing_period` > 0, 1, 0) AS `Listing_expired` ";
$sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
$sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
$sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T3` ON `T1`.`Plan_ID` = `T3`.`ID` ";
$sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T5` ON `T1`.`Account_ID` = `T5`.`ID` ";
$sql .= "WHERE `T1`.`ID` = '{$listing_id}' AND `T5`.`Status` = 'active' ";
$rlHook->load('listingDetailsSql', $sql);
$sql .= "LIMIT 1";
$listing_data = $rlDb->getRow($sql);
$rlSmarty->assign_by_ref('listing_data', $listing_data);
$listing_type = $rlListingTypes->types[$listing_data['Listing_type']];
$rlSmarty->assign_by_ref('listing_type', $listing_type);
if (empty($listing_id) || empty($listing_data) || ($listing_data['Status'] != 'active' && $listing_data['Account_ID'] != $account_info['ID'])) {
    $sError = true;
} elseif ($listing_data['Listing_expired']) {
    $errors[] = $lang['error_listing_expired'];
} else {
    $rlHook->load('listingDetailsTop');
    if ($config['count_listing_visits']) {
        $rlListings->countVisit($listing_data['ID']);
    }
    $print = array(
        'item' => 'listing',
        'id' => $listing_data['ID']
    );
    $rlSmarty->assign_by_ref('print', $print);
    $navIcons[] = '<a title="' . $lang['add_to_favorites'] . '" id="fav_' . $listing_data['ID'] . '" class="icon add_favorite" href="javascript:void(0)"> <span></span> </a>';
    if ($_SESSION['keyword_search_data']) {
        $navIcons    = array_reverse($navIcons);
        $return_link = SEO_BASE;
        if ($_SESSION['keyword_search_pageNum'] > 1) {
            $paging = $config['mod_rewrite'] ? '/index' . $_SESSION['keyword_search_pageNum'] : '&amp;pg=' . $_SESSION['keyword_search_pageNum'];
        }
        $return_link .= $config['mod_rewrite'] ? $pages['search'] . $paging . '.html' : '?page=' . $pages['search'] . '&amp;' . $paging;
        $navIcons[] = '<a title="' . $lang['back_to_search_results'] . '" href="' . $return_link . '">&larr; ' . $lang['back_to_search_results'] . '</a>';
        $navIcons   = array_reverse($navIcons);
    } elseif ($_SESSION[$listing_type['Key'] . '_post']) {
        $navIcons    = array_reverse($navIcons);
        $return_link = SEO_BASE;
        if ($_SESSION[$listing_type['Key'] . '_advanced']) {
            $search_results_url = $config['mod_rewrite'] ? $advanced_search_url . '/' . $search_results_url : $advanced_search_url . '&amp;' . $search_results_url;
        }
        if ($_SESSION[$listing_type['Key'] . '_pageNum'] > 1) {
            $paging = $config['mod_rewrite'] ? '/index' . $_SESSION[$listing_type['Key'] . '_pageNum'] : '&amp;pg=' . $_SESSION[$listing_type['Key'] . '_pageNum'];
        }
        $return_link .= $config['mod_rewrite'] ? $page_info['Path'] . '/' . $search_results_url . $paging . '.html' : '?page=' . $page_info['Path'] . '&amp;' . $search_results_url . $paging;
        $navIcons[] = '<a title="' . $lang['back_to_search_results'] . '" href="' . $return_link . '">&larr; ' . $lang['back_to_search_results'] . '</a>';
        $navIcons   = array_reverse($navIcons);
    }
    $rlSmarty->assign_by_ref('navIcons', $navIcons);
    $category_id = $listing_data['Category_ID'];
    $listing     = $rlListings->getListingDetails($category_id, $listing_data, $listing_type);
    $rlSmarty->assign('listing', $listing);
    $seller_info = $rlAccount->getProfile((int) $listing_data['Account_ID']);
    $rlSmarty->assign_by_ref('seller_info', $seller_info);
    if ($config['address_on_map'] && $listing_data['account_address_on_map']) {
        $location = $rlAccount->mapLocation;
        if ($seller_info['Loc_latitude'] && $seller_info['Loc_longitude']) {
            $location['direct'] = $seller_info['Loc_latitude'] . ',' . $seller_info['Loc_longitude'];
        }
    } else {
        $fields_list = $rlListings->fieldsList;
        $location    = false;
        foreach ($fields_list as $key => $value) {
            if ($fields_list[$key]['Map'] && !empty($listing_data[$fields_list[$key]['Key']])) {
                $mValue = str_replace("'", "\'", $value['value']);
                $location['search'] .= $mValue . ', ';
                $location['show'] .= $lang[$value['pName']] . ': <b>' . $mValue . '<\/b><br />';
                unset($mValue);
            }
        }
        if (!empty($location)) {
            $location['search'] = substr($location['search'], 0, -2);
        }
        if ($listing_data['Loc_latitude'] && $listing_data['Loc_longitude']) {
            $location['direct'] = $listing_data['Loc_latitude'] . ',' . $listing_data['Loc_longitude'];
        }
    }
    $rlSmarty->assign_by_ref('location', $location);
    $listing_title = $rlListings->getListingTitle($category_id, $listing_data, $listing_type['Key']);
    $reefless->loadClass('Categories');
    if ($listing_type['Cat_position'] == 'hide') {
        $categories = $rlCategories->getCategories($listing_data['Cat_parent_ID'], $listing_type['Key']);
        if (count($categories) == 1) {
            $single_category_mode = true;
            $rlSmarty->assign_by_ref('single_category_mode', $single_category_mode);
        }
    }
    if (!$single_category_mode) {
        $cat_bread_crumbs = $rlCategories->getBreadCrumbs($category_id, null, $listing_type);
        $cat_bread_crumbs = array_reverse($cat_bread_crumbs);
        if (!empty($cat_bread_crumbs)) {
            foreach ($cat_bread_crumbs as $bKey => $bVal) {
                $cat_bread_crumbs[$bKey]['path']     = $config['mod_rewrite'] ? $page_info['Path'] . '/' . $cat_bread_crumbs[$bKey]['Path'] : $page_info['Path'] . '&amp;category=' . $cat_bread_crumbs[$bKey]['ID'];
                $cat_bread_crumbs[$bKey]['title']    = $cat_bread_crumbs[$bKey]['name'];
                $cat_bread_crumbs[$bKey]['category'] = true;
                $bread_crumbs[]                      = $cat_bread_crumbs[$bKey];
            }
        }
    }
    $bread_crumbs[]                = array(
        'title' => $listing_title,
        'name' => $lang['pages+name+view_details']
    );
    $page_info['name']             = $listing_title;
    $page_info['meta_description'] = $rlListings->replaceMetaFields($listing_data['Category_ID'], $listing_data, 'description');
    $page_info['meta_keywords']    = $rlListings->replaceMetaFields($listing_data['Category_ID'], $listing_data, 'keywords');
    $page_info['meta_title']       = $rlListings->replaceMetaFields($listing_data['Category_ID'], $listing_data, 'title');
    $photos_limit                  = $listing_data['Image_unlim'] ? null : $listing_data['Image'];
    $photos                        = $rlDb->fetch('*', array(
        'Listing_ID' => $listing_id,
        'Status' => 'active'
    ), "AND `Thumbnail` <> '' AND `Photo` <> '' ORDER BY `Position`", $photos_limit, 'listing_photos');
    $rlSmarty->assign_by_ref('photos', $photos);
    if ($config['map_amenities']) {
        $rlDb->setTable('map_amenities');
        $amenities = $rlDb->fetch(array(
            'Key',
            'Default'
        ), array(
            'Status' => 'active'
        ), "ORDER BY `Position`");
        $amenities = $rlLang->replaceLangKeys($amenities, 'map_amenities', array(
            'name'
        ));
        $rlSmarty->assign_by_ref('amenities', $amenities);
    }
    $rlDb->setTable('listing_video');
    $videos = $rlDb->fetch(array(
        'ID',
        'Type',
        'Video',
        'Preview'
    ), array(
        'Listing_ID' => $listing_id
    ), "ORDER BY `Position`");
    $rlSmarty->assign_by_ref('videos', $videos);
    $tabs = array(
        'listing' => array(
            'key' => 'listing',
            'name' => $lang['listing']
        ),
        'seller' => array(
            'key' => 'seller',
            'name' => $lang['seller_info']
        ),
        'video' => array(
            'key' => 'video',
            'name' => $lang['video']
        ),
        'map' => array(
            'key' => 'map',
            'name' => $lang['map']
        ),
        'tell_friend' => array(
            'key' => 'tell_friend',
            'name' => $lang['tell_friend']
        )
    );
    if (empty($videos) || !$listing_type['Video'] || ($listing_data['Video'] == 0 && !$listing_data['Video_unlim'])) {
        unset($tabs['video']);
    }
    if (!$config['map_module'] || !$location) {
        unset($tabs['map']);
    }
    $rlSmarty->assign_by_ref('tabs', $tabs);
    $reefless->loadClass('Message');
    $rlXajax->registerFunction(array(
        'tellFriend',
        $rlListings,
        'ajaxTellFriend'
    ));
    $rlXajax->registerFunction(array(
        'contactOwner',
        $rlMessage,
        'ajaxContactOwner'
    ));
    $rlHook->load('listingDetailsBottom');
}