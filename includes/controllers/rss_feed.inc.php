<?php
$rlSmarty->register_function('str2path', array(
    'rlSmarty',
    'str2path'
));
$rlSmarty->register_function('rlHook', array(
    'rlHook',
    'load'
));
$languages = $rlLang->getLanguagesList();
$rlLang->modifyLanguagesList($languages);
$reefless->loadClass('Valid');
$reefless->loadClass('Categories');
$reefless->loadClass('Listings');
$reefless->loadClass('Common');
$site_name = str_replace('&', '&amp;', $lang['pages+title+home']);
$rlSmarty->assign('site_name', $site_name);
$item = $rlValid->xSql($_GET['nvar_1'] ? $_GET['nvar_1'] : $_GET['item']);
$id   = $rlValid->xSql($_GET['nvar_2'] ? $_GET['nvar_2'] : $_GET['id']);
$rlSmarty->assign('rss_item', $item);
switch ($item) {
    case 'account-listings':
        $reefless->loadClass('Account');
        $account = $rlAccount->getProfile($id);
        if ($account) {
            $rss = array(
                'title' => str_replace('{account_name}', $account['Full_name'], $lang['account_rss_feed_caption']),
                'path' => $account['Personal_address']
            );
            $rlSmarty->assign_by_ref('rss', $rss);
            $sorting['ID']['field'] = 'ID';
            $data                   = $rlListings->getListingsByAccount($account['ID'], 'ID', 'DESC', 0, $config['listings_per_rss']);
            $rlSmarty->assign_by_ref('listings', $data);
        } else {
            $sError = true;
        }
        break;
    case 'news':
        $reefless->loadClass('News');
        $news = $rlNews->get(false, true, $pInfo['current']);
        $rlSmarty->assign_by_ref('news', $news);
        $rss = array(
            'title' => $lang['pages+name+' . $pages['news']],
            'description' => $lang['pages+meta_description+' . $pages['news']] ? $lang['pages+meta_description+' . $pages['news']] : $lang['pages+title+' . $pages['news']],
            'path' => $pages['news']
        );
        $rlSmarty->assign_by_ref('rss', $rss);
        break;
    case 'category':
        $category = $rlCategories->getCategory($id);
        if (!$category) {
            $sError = true;
        }
        $rlSmarty->assign_by_ref('category', $category);
        $rss = array(
            'title' => $category['name'],
            'path' => $category['Path'],
            'id' => $category['ID']
        );
        $rlSmarty->assign_by_ref('rss', $rss);
        $listings = $rlListings->getListings($category['ID'], false, 'ASC', $pInfo['current'], $config['listings_per_rss']);
        $rlSmarty->assign_by_ref('listings', $listings);
        break;
    default:
        $rss = array(
            'title' => $lang['pages+title+listings'] . ' | ' . $site_name,
            'description' => $lang['pages+meta_description+listings']
        );
        $rlSmarty->assign_by_ref('rss', $rss);
        $listings = $rlListings->getRecentlyAdded(0, $config['listings_per_rss']);
        $rlSmarty->assign_by_ref('listings', $listings);
        break;
}
$rlHook->load('rssFeedBottom');
header("Content-Type: text/xml; charset=utf-8");