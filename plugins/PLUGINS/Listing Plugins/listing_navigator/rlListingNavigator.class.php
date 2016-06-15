<?php
class rlListingNavigator extends reefless
{
    var $lts = 'lnp_listingTypeSearch';
    var $kws = 'lnp_keyword_search';
    var $bc = 'lnp_browse_category';
    var $ra = 'lnp_recently_added';
    var $al = 'lnp_account_listings';
    function rlListingNavigator()
    {
    }
    function get($id = false, &$listing_data, $pass_current_stack = false, $pass_current_index = false)
    {
        global $rlSmarty, $page_info, $config;
        $get_item = $this->getItemKey($page_info, $id, $listing_data);
        $item     = $_GET['lnp_item'] ? $_GET['lnp_item'] : $get_item;
        if (!$item || !$id || !$_SESSION[$item])
            return false;
        $current_stack = $pass_current_stack;
        $current_index = $pass_current_index;
        if ($current_stack === false && $current_index === false) {
            foreach ($_SESSION[$item]['stacks'] as $stack_id => &$stacks) {
                foreach ($stacks as $index => $listing) {
                    if ($id == $listing['ID']) {
                        $current_stack = $stack_id;
                        $current_index = $index;
                    }
                }
            }
        }
        if ($_SESSION[$item]['stacks'][$current_stack][$current_index - 1]) {
            $data_prev = $_SESSION[$item]['stacks'][$current_stack][$current_index - 1];
        } elseif ($_SESSION[$item]['stacks'][$current_stack - 1][count($_SESSION[$item]['stacks'][$current_stack - 1]) - 1]) {
            $data_prev = $_SESSION[$item]['stacks'][$current_stack - 1][count($_SESSION[$item]['stacks'][$current_stack - 1]) - 1];
        } else {
            if ($pass_current_stack === false && $current_stack > 1) {
                $this->getNextStack($item, $current_stack, 'prev');
                $this->get($id, $listing_data, $current_stack - 1, count($_SESSION[$item]['stacks'][$current_stack - 1]));
            }
        }
        if ($data_prev) {
            $rlSmarty->assign_by_ref('lnp_data_prev', $data_prev);
        }
        if ($_SESSION[$item]['stacks'][$current_stack][$current_index + 1]) {
            $data_next = $_SESSION[$item]['stacks'][$current_stack][$current_index + 1];
        } elseif ($_SESSION[$item]['stacks'][$current_stack + 1][0]) {
            $data_next = $_SESSION[$item]['stacks'][$current_stack + 1][0];
        } else {
            if ($pass_current_stack === false) {
                if ($this->getNextStack($item, $current_stack, 'next')) {
                    $this->get($id, $listing_data, $current_stack + 1, -1);
                }
            }
        }
        if ($data_next) {
            $rlSmarty->assign_by_ref('lnp_data_next', $data_next);
        }
    }
    function getNextStack($item = false, $current_stack = false, $direction = 'next')
    {
        global $config, $rlListingTypes, $sorting;
        $stack = $direction == 'next' ? $current_stack + 1 : $current_stack - 1;
        switch ($item) {
            case $this->lts:
                $this->loadClass('Search');
                $GLOBALS['rlSearch']->fields = $_SESSION[$this->lts]['data']['fields'];
                $listings                    = $GLOBALS['rlSearch']->search($_SESSION[$this->lts]['data']['data'], $_SESSION[$this->lts]['data']['listing_type_key'], $stack, $config['listings_per_page']);
                if (empty($listings))
                    return false;
                $this->listingTypeSearch($listings, $stack);
                break;
            case $this->kws;
                $this->loadClass('Search');
                $GLOBALS['rlSearch']->fields['keyword_search'] = array(
                    'Key' => 'keyword_search',
                    'Type' => 'text'
                );
                $sorting                                       = $_SESSION[$this->kws]['data']['sorting'];
                $listings                                      = $GLOBALS['rlSearch']->search($_SESSION[$this->kws]['data']['data'], false, $stack, $config['listings_per_page']);
                if (empty($listings))
                    return false;
                $this->keywordSearch($listings, $stack);
                break;
            case $this->bc;
                $this->loadClass('Listings');
                $sorting  = $_SESSION[$this->bc]['data']['sorting'];
                $listings = $GLOBALS['rlListings']->getListings($_SESSION[$this->bc]['data']['category_id'], $_SESSION[$this->bc]['data']['order_field'], $_SESSION[$this->bc]['data']['sort_type'], $stack, $config['listings_per_page']);
                if (empty($listings))
                    return false;
                $this->browseCategory($listings, $stack);
                break;
            case $this->ra;
                $this->loadClass('Listings');
                $requested_type = $_SESSION['recently_added_type'];
                $listings       = $GLOBALS['rlListings']->getRecentlyAdded($stack, $config['listings_per_page'], $requested_type);
                if (empty($listings))
                    return false;
                $this->recentlyAdded($listings, $stack);
                break;
            case $this->al;
                $this->loadClass('Listings');
                $sorting  = $_SESSION[$this->at]['data']['sorting'];
                $listings = $GLOBALS['rlListings']->getListingsByAccount($_SESSION[$this->at]['data']['account_id'], $_SESSION[$this->at]['data']['sort_by'], $_SESSION[$this->at]['data']['sort_type'], $stack, $config['listings_per_page']);
                if (empty($listings))
                    return false;
                $this->accountListings($listings, $stack);
                break;
        }
        return true;
    }
    function getItemKey($page_info = false, $id = false, &$listing_data)
    {
        if (ereg('^lt_.*_search', $page_info['prev'])) {
            $item = $this->lts;
        } elseif ($page_info['prev'] == 'search') {
            $item = $this->kws;
        } elseif (ereg('^lt_', $page_info['prev'])) {
            $item = $this->bc;
        } elseif ($page_info['prev'] == 'listings') {
            $item = $this->ra;
        } elseif (ereg('^at_', $page_info['prev'])) {
            $item = $this->al;
        } else {
            $item = $this->bc;
            $this->directListing($id, $listing_data);
        }
        return $item;
    }
    function directListing($id = false, &$listing_data)
    {
        if (!$id || !$listing_data)
            return;
        $this->loadClass('Listings');
        $listings       = $GLOBALS['rlListings']->getListings($listing_data['Category_ID'], false, null, 1, 30);
        $sorting_fields = $GLOBALS['rlListings']->getFormFields($listing_data['Category_ID'], 'short_forms', $listing_data['Cat_type']);
        foreach ($sorting_fields as &$field) {
            if ($field['Details_page']) {
                $sorting[$field['Key']] = $field;
            }
        }
        unset($sorting_fields);
        $_SESSION[$this->bc]['data'] = array(
            'category_id' => $listing_data['Category_ID'],
            'order_field' => false,
            'sort_type' => 'ASC',
            'sorting' => $sorting
        );
        $this->browseCategory($listings, 1);
    }
    function populate(&$listings, $pass_stack = false, $item = false)
    {
        global $config, $rlListingTypes, $pages, $rlValid;
        if (empty($listings))
            return;
        $stack                                  = (int) $_GET['pg'] ? (int) $_GET['pg'] : 1;
        $work_stack                             = $pass_stack ? $pass_stack : $stack;
        $_SESSION[$item]['stacks'][$work_stack] = array();
        foreach ($listings as &$listing) {
            if (!$rlListingTypes->types[$listing['Listing_type']]['Page'])
                continue;
            $href = SEO_BASE;
            if ($config['mod_rewrite']) {
                $href .= $pages[$rlListingTypes->types[$listing['Listing_type']]['Page_key']] . '/' . $listing['Path'] . '/' . $rlValid->str2path($listing['listing_title']) . '-' . $listing['ID'] . '.html?lnp_item=' . $item;
            } else {
                $href .= '?page=' . $pages[$rlListingTypes->types[$listing['Listing_type']]['Page_key']] . '&id=' . $listing['ID'] . '&lnp_item=' . $item;
            }
            $_SESSION[$item]['stacks'][$work_stack][] = array(
                'ID' => $listing['ID'],
                'listing_title' => $listing['listing_title'],
                'href' => $href
            );
        }
    }
    function listingTypeSearch($pass_listings = false, $pass_stack = false)
    {
        global $listings, $rlListingTypes, $listing_type_key;
        if ($_REQUEST['action'] == 'search' || $_SESSION[$this->lts]['data']['listing_type_key'] != $listing_type_key) {
            unset($_SESSION[$this->lts]['stacks']);
        }
        $work_listings = $pass_listings ? $pass_listings : $listings;
        $this->populate($work_listings, $pass_stack, $this->lts);
    }
    function keywordSearch($pass_listings = false, $pass_stack = false)
    {
        global $listings;
        if ($_POST['form'] == 'keyword_search') {
            unset($_SESSION[$this->kws]['stacks']);
        }
        $work_listings = $pass_listings ? $pass_listings : $listings;
        $this->populate($work_listings, $pass_stack, $this->kws);
    }
    function browseCategory($pass_listings = false, $pass_stack = false)
    {
        global $listings, $page_info;
        if ($page_info['prev'] != $page_info['Key']) {
            unset($_SESSION[$this->bc]['stacks']);
        }
        $work_listings = $pass_listings ? $pass_listings : $listings;
        $this->populate($work_listings, $pass_stack, $this->bc);
    }
    function recentlyAdded($pass_listings = false, $pass_stack = false)
    {
        global $listings, $page_info;
        if ($page_info['prev'] != $page_info['Key']) {
            unset($_SESSION[$this->ra]['stacks']);
        }
        $work_listings = $pass_listings ? $pass_listings : $listings;
        $this->populate($work_listings, $pass_stack, $this->ra);
    }
    function accountListings($pass_listings = false, $pass_stack = false)
    {
        global $listings, $page_info;
        if ($page_info['prev'] != $page_info['Key']) {
            unset($_SESSION[$this->al]['stacks']);
        }
        $work_listings = $pass_listings ? $pass_listings : $listings;
        $this->populate($work_listings, $pass_stack, $this->al);
    }
}