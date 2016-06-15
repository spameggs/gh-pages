<?php
$reefless->loadClass('Search');
$rlSearch->getHomePageSearchForm();
if ($tpl_settings['random_featured_home']) {
    foreach ($rlListingTypes->types as $home_type) {
        if ($home_type['Random_featured']) {
            $random_featured = $rlListings->getRandom($home_type['Key'], $home_type['Random_featured_type'], $home_type['Random_featured_number']);
            $rlSmarty->assign_by_ref('random_featured', $random_featured);
            $rlSmarty->assign('listing_type', $home_type);
            break;
        }
    }
}
$rss = array(
    'title' => $page_info['title']
);
$rlSmarty->assign_by_ref('rss', $rss);
$rlHook->load('homeBottom');