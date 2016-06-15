<?php
$account_type_key = str_replace('at_', '', $page_info['Key']);
$account_type     = $rlAccount->getTypeDetails($account_type_key);
$rlHook->load('accountTypeTop');
if ($account_type && $account_type['Page']) {
    $rlSmarty->assign_by_ref('account_type', $account_type);
    $reefless->loadClass('Message');
    $rlXajax->registerFunction(array(
        'contactOwner',
        $rlMessage,
        'ajaxContactOwner'
    ));
    $account_id = (int) $_GET['id'] ? (int) $_GET['id'] : $_GET['nvar_1'];
    $account    = $rlAccount->getProfile($account_id);
    if ($account) {
        if (!empty($account)) {
            if ($config['account_wildcard'] && isset($_GET['wildcard']) && $config['mod_rewrite']) {
                if (defined('RL_MOBILE') && RL_MOBILE) {
                    $lang_url_home = str_replace($rlValid->getDomain(RL_URL_HOME), $_SERVER['HTTP_HOST'], $rlSmarty->get_template_vars('rlBaseLang'));
                    $rlSmarty->assign('rlBaseLang', $lang_url_home);
                } else {
                    $lang_url_home = str_replace($rlValid->getDomain(RL_URL_HOME), $_SERVER['HTTP_HOST'], RL_URL_HOME);
                    $rlSmarty->assign('lang_url_home', $lang_url_home);
                }
            }
            $rlSmarty->assign_by_ref('account', $account);
            $tabs = array(
                'details' => array(
                    'key' => 'details',
                    'name' => $lang['account_info']
                ),
                'listings' => array(
                    'key' => 'listings',
                    'name' => $lang['account_listings']
                ),
                'map' => array(
                    'key' => 'map',
                    'name' => $lang['map']
                )
            );
            $rlSmarty->assign_by_ref('tabs', $tabs);
            $bread_crumbs[]    = array(
                'name' => $lang['personal_page']
            );
            $page_info['name'] = $account['Full_name'];
            $sorting           = array(
                'join_date' => array(
                    'name' => $lang['date'],
                    'field' => 'Date'
                ),
                'category' => array(
                    'name' => $lang['category'],
                    'field' => 'Category_ID'
                )
            );
            $rlSmarty->assign_by_ref('sorting', $sorting);
            $sort_by = $_SESSION['account_sort_by'] = $_REQUEST['sort_by'] ? $_REQUEST['sort_by'] : $_SESSION['account_sort_by'];
            if ($_REQUEST['sort_by']) {
                $rlSmarty->assign('sorting_mode', true);
            }
            $sort_by = $sort_by ? $sort_by : 'join_date';
            if (!empty($sorting[$sort_by])) {
                $order_field     = $sorting[$sort_by]['Key'];
                $data['sort_by'] = $sort_by;
                $rlSmarty->assign_by_ref('sort_by', $sort_by);
            }
            $sort_type = $_SESSION['account_sort_type'] = $_REQUEST['sort_type'] ? $_REQUEST['sort_type'] : $_SESSION['account_sort_type'];
            $sort_type = $sort_type ? $sort_type : 'desc';
            if ($sort_type) {
                $data['sort_type'] = $sort_type = in_array($sort_type, array(
                    'asc',
                    'desc'
                )) ? $sort_type : false;
                $rlSmarty->assign_by_ref('sort_type', $sort_type);
            }
            $pInfo['current'] = (int) $_GET['pg'];
            if (!is_int($account_id)) {
                $account_id = $account['ID'];
            }
            $reefless->loadClass('Listings');
            $listings = $rlListings->getListingsByAccount($account['ID'], $sort_by, $sort_type, $pInfo['current'], $config['listings_per_page']);
            $rlSmarty->assign_by_ref('listings', $listings);
            $pInfo['calc'] = $rlListings->calc;
            $rlSmarty->assign_by_ref('pInfo', $pInfo);
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
            if ($listings) {
                $rss = array(
                    'item' => 'account-listings',
                    'id' => $account['Own_address'],
                    'title' => str_replace('{account_name}', $account['Full_name'], $lang['account_rss_feed_caption'])
                );
                $rlSmarty->assign_by_ref('rss', $rss);
            }
            $location = $rlAccount->mapLocation;
            if ($account['Loc_latitude'] && $account['Loc_longitude']) {
                $location['direct'] = $account['Loc_latitude'] . ',' . $account['Loc_longitude'];
            }
            if (!empty($location) && $config['map_module']) {
                $rlSmarty->assign_by_ref('location', $location);
            } else {
                unset($tabs['map']);
            }
            $rlHook->load('accountTypeAccount');
        } else {
            $errors[] = $lang['account_request_fail'];
        }
    } else {
        if (!$_GET['nvar_1'] && !isset($_GET[$search_results_url])) {
            if ($_SESSION['at_data_' . $account_type_key]) {
                $_POST = $_SESSION['at_data_' . $account_type_key];
                unset($_SESSION['at_data_' . $account_type_key]);
            }
        }
        $tabs = array(
            'characters' => array(
                'key' => 'characters',
                'name' => $lang['alphabetic_search']
            ),
            'search' => array(
                'key' => 'search',
                'name' => $lang['advanced_search']
            )
        );
        $rlSmarty->assign_by_ref('tabs', $tabs);
        $fields = $rlAccount->buildSearch($account_type['ID']);
        $rlSmarty->assign_by_ref('fields', $fields);
        $alphabet = explode(',', $lang['alphabet_characters']);
        $rlSmarty->assign_by_ref('alphabet', $alphabet);
        if ($_GET['nvar_1'] == $search_results_url || isset($_GET[$search_results_url])) {
            $return_link = SEO_BASE;
            $return_link .= $config['mod_rewrite'] ? $page_info['Path'] . '.html#modify' : '?page=' . $page_info['Path'] . '#modify';
            $navIcons[] = '<a title="' . $lang['modify_search_criterion'] . '" href="' . $return_link . '">&larr; ' . $lang['modify_search_criterion'] . '</a>';
            $rlSmarty->assign_by_ref('navIcons', $navIcons);
            $bread_crumbs[] = array(
                'name' => $lang['search_results']
            );
            $rlSmarty->assign('search_results', 'search');
            $sorting              = array_reverse($fields);
            $sorting['join_date'] = array(
                'Key' => 'Date',
                'name' => $lang['join_date']
            );
            $sorting              = array_reverse($sorting);
            $rlSmarty->assign_by_ref('sorting', $sorting);
            $data             = $_SESSION['at_data_' . $account_type_key] = $_POST['f'] ? $_POST['f'] : $_SESSION['at_data_' . $account_type_key];
            $pInfo['current'] = (int) $_GET['pg'];
            $sort_by          = $_SESSION[$account_type_key . '_sort_by'] = $_REQUEST['sort_by'] ? $_REQUEST['sort_by'] : $_SESSION[$account_type_key . '_sort_by'];
            $sort_by          = $sort_by ? $sort_by : 'join_date';
            if (!empty($sorting[$sort_by])) {
                $order_field     = $sorting[$sort_by]['Key'];
                $data['sort_by'] = $sort_by;
                $rlSmarty->assign_by_ref('sort_by', $sort_by);
            }
            $sort_type = $_SESSION[$account_type_key . '_sort_type'] = $_REQUEST['sort_type'] ? $_REQUEST['sort_type'] : $_SESSION[$account_type_key . '_sort_type'];
            $sort_type = $sort_type ? $sort_type : 'desc';
            if ($sort_type) {
                $data['sort_type'] = $sort_type = in_array($sort_type, array(
                    'asc',
                    'desc'
                )) ? $sort_type : false;
                $rlSmarty->assign_by_ref('sort_type', $sort_type);
            }
            $dealers = $rlAccount->searchDealers($data, $fields, $config['dealers_per_page'], $pInfo['current'], $account_type);
            $rlSmarty->assign_by_ref('dealers', $dealers);
            $pInfo['calc'] = $rlAccount->calc;
            $rlSmarty->assign_by_ref('pInfo', $pInfo);
            $page_info['name'] = str_replace(array(
                '{number}'
            ), array(
                $pInfo['calc']
            ), $lang['accounts_found']);
        } else {
            $_GET['nvar_1'] = $_GET['nvar_1'] === '0' ? '0-9' : $_GET['nvar_1'];
            $char           = in_array($_GET['nvar_1'], $alphabet) ? $_GET['nvar_1'] : $_REQUEST['character'];
            $request_char   = $char ? true : false;
            $rlSmarty->assign('alphabet_mode', $request_char);
            $char = $char ? $char : $alphabet[0];
            $rlSmarty->assign_by_ref('char', $char);
            if ($request_char) {
                $pInfo['current'] = (int) $_GET['pg'];
            }
            $alphabet_dealers = $rlAccount->getDealersByChar($char, $config['dealers_per_page'], $pInfo['current'], $account_type);
            $rlSmarty->assign_by_ref('alphabet_dealers', $alphabet_dealers);
            $pInfo['calc_alphabet'] = $rlAccount->calc_alphabet;
            $rlSmarty->assign_by_ref('pInfo', $pInfo);
            if ($request_char) {
                if ($pInfo['current'] > 1) {
                    $bc_page = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
                }
                $alp_title         = str_replace('{char}', $char, $lang['search_by']);
                $bread_crumbs[]    = array(
                    'title' => $alp_title . $bc_page,
                    'name' => $lang['alphabetic_search']
                );
                $page_info['name'] = $alp_title;
            }
        }
        $rlHook->load('accountTypeAccountsList');
    }
} else {
    $errors[] = $lang['account_type_page_access_restricted'];
}