<?php
class rlPlan extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    function rlPlan()
    {
        global $rlLang, $rlValid, $rlConfig;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
    }
    function getPlan($id = false, $account_id = false)
    {
        global $sql, $lang, $rlHook;
        if (!$id)
            return false;
        $sql = "SELECT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Price`, `T1`.`Listing_period`, `T1`.`Listing_number`, `T1`.`Cross`, `T1`.`Limit` ";
        if ($account_id) {
            $sql .= ", `T2`.`Listings_remains` AS `Using`, `T2`.`ID` AS `Plan_using_ID`, `T3`.`Listings_remains` ";
        }
        $sql .= "FROM `" . RL_DBPREFIX . "listing_plans` AS `T1` ";
        if ($account_id) {
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_packages` AS `T2` ON `T1`.`ID` = `T2`.`Plan_ID` AND `T2`.`Account_ID` = '{$account_id}' AND `T2`.`Type` = 'limited' ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_packages` AS `T3` ON `T1`.`ID` = `T3`.`Plan_ID` AND `T3`.`Account_ID` = '{$account_id}' AND `T3`.`Type` = 'package' ";
        }
        $sql .= "WHERE `T1`.`ID` = '{$id}' AND `T1`.`Status` = 'active'";
        $rlHook->load('phpGetPlanSql');
        $plan         = $this->getRow($sql);
        $plan['name'] = $lang['listing_plans+name+' . $plan['Key']];
        return $plan;
    }
    function getPlans($types = false, $account_type_filter = false)
    {
        global $account_info;
        if ($types) {
            $where = "AND (";
            if (is_array($types)) {
                foreach ($types as $type) {
                    $where .= "`Type` =  '{$type}' OR ";
                }
                $where = substr($where, 0, -3);
            } else {
                $where .= " `Type` =  '{$types}' ";
            }
            $where .= ") ";
        }
        if ($account_type_filter) {
            $where .= "AND (FIND_IN_SET('{$account_info['Type']}', `Allow_for`) > 0 OR `Allow_for` = '') ";
        }
        $plans = $this->fetch(array(
            'ID',
            'Key',
            'Type',
            'Featured',
            'Advanced_mode',
            'Standard_listings',
            'Featured_listings',
            'Price',
            'Listing_period',
            'Plan_period',
            'Image',
            'Video',
            'Listing_number',
            'Color'
        ), null, "WHERE `Status` = 'active' {$where}", null, 'listing_plans');
        $plans = $this->rlLang->replaceLangKeys($plans, 'listing_plans', array(
            'name',
            'des'
        ));
        return $plans;
    }
    function detectParentIncludes($id, $data = false)
    {
        if (!$id)
            return false;
        $parent = $this->getOne('Parent_ID', "`ID` = '{$id}'", 'categories');
        if (!empty($parent)) {
            $target = $this->getOne('ID', "FIND_IN_SET('{$parent}', `Category_ID`) > 0  AND `Status` = 'active' AND `Subcategories` = '1'", 'listing_plans');
            if (empty($target)) {
                return $this->detectParentIncludes($parent);
            }
            return $parent;
        } else {
            return false;
        }
    }
    function getPlanByCategory($id = false, $account_type = false, $featured = false)
    {
        global $account_info;
        if ($id && $additional = $this->detectParentIncludes($id)) {
            $add_where = "OR (FIND_IN_SET('{$additional}', `Category_ID`) > 0 AND `Subcategories` = '1')";
        }
        $where_type = defined('REALM') ? '' : "(FIND_IN_SET('{$account_type}', `T1`.`Allow_for`) > 0 OR `T1`.`Allow_for` = '' ) ";
        $sql        = "SELECT DISTINCT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Featured`, `T1`.`Advanced_mode`, `T1`.`Standard_listings`, ";
        $sql .= "`T1`.`Featured_listings`, `T1`.`Listing_number`, `T1`.`Price`, `T1`.`Cross`, `T1`.`Limit`, `T1`.`Image`, `T1`.`Image_unlim`, ";
        $sql .= "`T1`.`Video`, `T1`.`Video_unlim`, `T1`.`Listing_period`, `T1`.`Plan_period`, `T1`.`Color`, ";
        $sql .= "`T2`.`Listings_remains` AS `Using`, `T2`.`ID` AS `Plan_using_ID`, ";
        $sql .= "`T3`.`ID` AS `Package_ID`, `T3`.`Listings_remains`, `T3`.`Standard_remains`, `T3`.`Featured_remains` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listing_plans` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_packages` AS `T2` ON `T1`.`ID` = `T2`.`Plan_ID` AND `T2`.`Account_ID` = '{$account_info['ID']}' AND `T2`.`Type` = 'limited' ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_packages` AS `T3` ON `T1`.`ID` = `T3`.`Plan_ID` AND `T3`.`Account_ID` = '{$account_info['ID']}' AND `T3`.`Type` = 'package' ";
        $sql .= "AND (((`T3`.`Standard_remains` > 0 OR `T1`.`Standard_listings` = 0) AND `T1`.`Advanced_mode` = '1' ) OR ((`T3`.`Featured_remains` > 0 OR `T1`.`Featured_listings` = 0) AND `T1`.`Advanced_mode` = '1') OR ((`T3`.`Listings_remains` > 0 OR `T1`.`Listing_number` = 0) AND `T1`.`Advanced_mode` <> '1')) ";
        $sql .= "AND (UNIX_TIMESTAMP(DATE_ADD(`T3`.`Date`, INTERVAL `T1`.`Plan_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T1`.`Plan_period` = 0) ";
        $sql .= "WHERE `T1`.`Status` = 'active' ";
        if ($id) {
            $sql .= "AND ((FIND_IN_SET('{$id}', `T1`.`Category_ID`) > 0 OR `T1`.`Sticky` = '1') {$add_where}) ";
        }
        if ($account_type) {
            $sql .= "AND {$where_type} ";
        }
        if ($featured) {
            $sql .= "AND `T1`.`Type` = 'featured' ";
        } else {
            $sql .= "AND `T1`.`Type` <> 'featured' ";
        }
        $sql .= "ORDER BY `Position`";
        $plans = $this->getAll($sql);
        $plans = $this->rlLang->replaceLangKeys($plans, 'listing_plans', array(
            'name',
            'des'
        ));
        foreach ($plans as $plan) {
            $tmp_plans[$plan['ID']] = $plan;
        }
        $plans = $tmp_plans;
        unset($tmp_plans);
        return $plans;
    }
    function stepByPath($path = false, $steps = false)
    {
        if (!$path || !$steps)
            return;
        foreach ($steps as $key => $step) {
            if ($step['path'] == $path) {
                return $key;
            }
        }
    }
    function grantPlan($account_id = false, $plan_id = false)
    {
        global $rlActions;
        if (!$account_id || !$plan_id)
            return;
        $package_info = $this->fetch(array(
            'Listing_number',
            'Standard_listings',
            'Featured_listings'
        ), array(
            'ID' => $plan_id
        ), null, 1, 'listing_plans', 'row');
        $insert       = array(
            'Account_ID' => $account_id,
            'Plan_ID' => $plan_id,
            'Listings_remains' => $package_info['Listing_number'],
            'Standard_remains' => $package_info['Standard_listings'],
            'Featured_remains' => $package_info['Featured_listings'],
            'Type' => 'package',
            'Date' => 'NOW()',
            'IP' => $_SERVER['REMOTE_ADDR']
        );
        $rlActions->insertOne($insert, 'listing_packages');
        return true;
    }
    function ajaxPrepareDeleting($id = false)
    {
        global $_response, $rlSmarty, $rlHook, $delete_details, $lang, $config;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $id = (int) $id;
        if (!$id)
            return $_response;
        $plan_details         = $this->fetch(array(
            'Key',
            'ID',
            'Type'
        ), array(
            'ID' => $id
        ), null, 1, 'listing_plans', 'row');
        $plan_details['name'] = $lang['listing_plans+name+' . $plan_details['Key']];
        $rlSmarty->assign_by_ref('plan_details', $plan_details);
        $listings         = $this->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "listings` WHERE `Plan_ID` = '{$id}' AND `Status` <> 'trash'");
        $delete_details[] = array(
            'name' => $lang['listings'],
            'items' => $listings['Count'],
            'link' => RL_URL_HOME . ADMIN . '/index.php?controller=listings&amp;plan_id=' . $id
        );
        $delete_total_items += $listings['Count'];
        $packages         = $this->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "listing_packages` WHERE `Plan_ID` = '{$id}' AND `Type` = 'package' AND `Listings_remains` > 0");
        $delete_details[] = array(
            'name' => $lang['purchased_packages'],
            'items' => $packages['Count'],
            'link' => RL_URL_HOME . ADMIN . '/index.php?controller=plans_using&amp;plan_id=' . $id
        );
        $delete_total_items += $packages['Count'];
        $rlSmarty->assign_by_ref('delete_details', $delete_details);
        if ($delete_total_items) {
            $plans = $this->getPlans($plan_details['Type'] == 'featured' ? 'featured' : array(
                'listing',
                'package'
            ));
            $rlSmarty->assign_by_ref('plans', $plans);
            $tpl = 'blocks' . RL_DS . 'delete_preparing_plan.tpl';
            $_response->assign("delete_container", 'innerHTML', $GLOBALS['rlSmarty']->fetch($tpl, null, null, false));
            $_response->script("
				$('input[name=new_account]').rlAutoComplete({add_id: true});
				$('#delete_block').slideDown();
			");
        } else {
            $phrase = $config['trash'] ? str_replace('{username}', $plan_details['name'], $lang['notice_drop_plan']) : str_replace('{username}', $account_details['Username'], $lang['notice_delete_plan']);
            $_response->script("
				$('#delete_block').slideUp();
				flynax.confirm('{$phrase}', xajax_deletePlan, '{$plan_details['Key']}');
			");
        }
        return $_response;
    }
    function ajaxDeletePlan($key = false, $reason = false)
    {
        global $_response, $lang, $rlCategories, $rlListings, $rlActions, $rlListingTypes, $config, $pages;
        if (is_array($key)) {
            $replace = $key[1];
            $key     = $key[0];
        }
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $plan_info = $this->fetch(array(
            'ID',
            'Type'
        ), array(
            'Key' => $key
        ), null, 1, 'listing_plans', 'row');
        $id        = $plan_info['ID'];
        if ($replace) {
            if ($plan_info['Type'] == 'featured') {
                $this->query("UPDATE `" . RL_DBPREFIX . "listings` SET `Featured_ID` = '{$replace}' WHERE `Featured_ID` = '{$id}'");
            } else {
                $this->query("UPDATE `" . RL_DBPREFIX . "listings` SET `Plan_ID` = '{$replace}' WHERE `Plan_ID` = '{$id}'");
            }
        } else {
            if ($plan_info['Type'] == 'featured') {
                $this->query("UPDATE `" . RL_DBPREFIX . "listings` SET `Featured_ID` = '' WHERE `Featured_ID` = '{$id}'");
            } else {
                $this->loadClass('Mail');
                $this->loadClass('Account');
                $sql = "SELECT `T1`.*, `T2`.`Type` AS `Listing_type` ";
                $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
                $sql .= "WHERE `T1`.`Plan_ID` = '{$id}'";
                $listings        = $this->getAll($sql);
                $mail_tpl_source = $GLOBALS['rlMail']->getEmailTemplate('listing_plan_removed');
                foreach ($listings as $listing) {
                    if ($rlListings->isActive($listing['ID'])) {
                        $rlCategories->listingsDecrease($listing['Category_ID']);
                        if ($listing['Crossed']) {
                            $crossed_categories = explode(',', $listing['Crossed']);
                            foreach ($crossed_categories as $crossed_category) {
                                $rlCategories->listingsDecrease($crossed_category);
                            }
                        }
                        $account_info  = $GLOBALS['rlAccount']->getProfile((int) $listing['Account_ID']);
                        $mail_tpl      = $mail_tpl_source;
                        $listing_tiele = $rlListings->getListingTitle($listing['Category_ID'], $listing, $listing['Listing_type']);
                        $link          = $contact_link = RL_URL_HOME;
                        if ($config['mod_rewrite']) {
                            $link .= $pages[$rlListingTypes->types[$listing['Listing_type']]['My_key']] . '.html';
                            $contact_link .= $pages['contact_us'] . '.html';
                        } else {
                            $link .= '?page=' . $pages[$rlListingTypes->types[$listing['Listing_type']]['My_key']];
                            $contact_link .= '?page=' . $pages['contact_us'];
                        }
                        $link             = '<a href="' . $link . '">' . $listing_tiele . '</a>';
                        $find             = array(
                            '{username}',
                            '{link}',
                            '{reason}'
                        );
                        $replace          = array(
                            $account_info['Full_name'],
                            $link,
                            $reason
                        );
                        $mail_tpl['body'] = str_replace($find, $replace, $mail_tpl['body']);
                        $mail_tpl['body'] = preg_replace('/(\[(.+)\])/', '<a href="' . $contact_link . '">$2</a>', $mail_tpl['body']);
                        $GLOBALS['rlMail']->send($mail_tpl, $account_info['Mail']);
                    }
                }
                $this->query("UPDATE `" . RL_DBPREFIX . "listings` SET `Plan_ID` = '' WHERE `Plan_ID` = '{$id}'");
            }
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "listing_packages` WHERE `Plan_ID` = '{$id}'");
        $lang_keys = array(
            array(
                'Key' => 'listing_plans+name+' . $key
            ),
            array(
                'Key' => 'listing_plans+des+' . $key
            )
        );
        $rlActions->delete(array(
            'Key' => $key
        ), array(
            'listing_plans',
            'lang_keys'
        ), null, 1, $key, $lang_keys);
        $del_mode = $GLOBALS['rlActions']->action;
        $_response->script("
			listingPlansGrid.reload();
			printMessage('notice', '{$lang['plan_' . $del_mode]}');
			$('#delete_block').slideUp();
		");
        return $_response;
    }
}