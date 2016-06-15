<?php
class rlControls extends reefless
{
    var $rlLang;
    var $rlValid;
    function rlControls()
    {
        global $rlLang, $rlValid;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
    }
    function ajaxRecountListings($self = false, $direct = false, $start = false)
    {
        global $_response, $lang, $rlCache, $rlHook;
        if ($this->checkSessionExpire() === false && !$direct) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $start = (int) $start;
        $limit = 500;
        $this->setTable('categories');
        $categories = $this->fetch(array(
            'ID',
            'Parent_ID'
        ), array(
            'Status' => 'active'
        ), "ORDER BY `Parent_ID`", array(
            $start,
            $limit
        ));
        $this->resetTable();
        foreach ($categories as $key => $value) {
            $sql = "SELECT COUNT(`T1`.`ID`) AS `Count` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
            $sql .= "WHERE (`T1`.`Category_ID` = '{$categories[$key]['ID']}' OR FIND_IN_SET('{$categories[$key]['ID']}', `Crossed`) > 0) AND `T1`.`Status` = 'active' ";
            $sql .= "AND (UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0) ";
            $rlHook->load('apAjaxRecountListings', $sql);
            $cat_listings = $this->getRow($sql);
            $update       = array(
                'fields' => array(
                    'Count' => $cat_listings['Count']
                ),
                'where' => array(
                    'ID' => $categories[$key]['ID']
                )
            );
            $GLOBALS['rlActions']->updateOne($update, 'categories');
            if ($categories[$key]['Parent_ID'] > 0) {
                $this->recountListings($categories[$key]['Parent_ID'], $cat_listings['Count']);
            }
        }
        if (count($categories) == $limit) {
            $start += $limit;
            $_response->script("xajax_recountListings('{$self}', '{$direct}', {$start});");
            unset($categories);
            return $_response;
        }
        $rlCache->updateCategories();
        $rlCache->updateListingStatistics();
        if (!$direct) {
            $_response->script("printMessage('notice', '{$lang['listings_recounted']}')");
            $_response->script("$('{$self}').val('{$lang['recount']}');");
        }
        return $_response;
    }
    function recountListings($parent_id, $current_number)
    {
        $update = "UPDATE `" . RL_DBPREFIX . "categories` SET `Count` = `Count` + '{$current_number}' WHERE `ID` = '{$parent_id}'";
        $this->query($update);
        $category = $this->fetch(array(
            'ID',
            'Parent_ID'
        ), array(
            'ID' => $parent_id,
            'Status' => 'active'
        ), null, 1, 'categories', 'row');
        if ($category['Parent_ID'] > 0) {
            $this->recountListings($category['Parent_ID'], $current_number);
        }
    }
    function ajaxRebuildCatLevels($mode = true, $self = false, $start = false)
    {
        global $_response, $lang, $rlListingTypes;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $start = (int) $start;
        $limit = 500;
        $this->setTable('categories');
        $categories = $this->fetch(array(
            'ID',
            'Parent_ID',
            'Position',
            'Type'
        ), null, "ORDER BY `Parent_ID`", array(
            $start,
            $limit
        ));
        $this->loadClass('Categories');
        foreach ($categories as $key => $category) {
            $tree         = '';
            $level        = 0;
            $related_cats = $GLOBALS['rlCategories']->getBreadCrumbs($category['Parent_ID'], false, $rlListingTypes->types[$category['Type']]);
            $related_cats = array_reverse($related_cats);
            foreach ($related_cats as $r_category) {
                $tree .= $r_category['Position'] . '.';
            }
            $tree .= $category['Position'];
            $level      = empty($category['Parent_ID']) ? 0 : count($related_cats);
            $parent_ids = '';
            if ($category['Parent_ID']) {
                $parent_ids[] = $category['Parent_ID'];
                if ($parents = $GLOBALS['rlCategories']->getParentIDs($category['Parent_ID'])) {
                    $parent_ids = array_merge($parent_ids, $parents);
                }
                $parent_ids = implode(',', $parent_ids);
            }
            $update[] = array(
                'fields' => array(
                    'Level' => $level,
                    'Tree' => $tree,
                    'Parent_IDs' => $parent_ids
                ),
                'where' => array(
                    'ID' => $category['ID']
                )
            );
        }
        if ($update) {
            $GLOBALS['rlActions']->update($update, 'categories');
            if (count($categories) == $limit) {
                $start += $limit;
                $_response->script("xajax_rebuildCatLevels('{$mode}', '{$self}', {$start});");
                unset($categories);
                return $_response;
            }
        }
        if ((bool) $mode === true) {
            $_response->script("printMessage('notice', '{$lang['levels_rebuilt']}')");
            $_response->script("$('{$self}').val('{$lang['rebuild']}');");
        }
        unset($update, $categories, $related_cats);
        return $_response;
    }
    function ajaxReorderFields($mode = true, $self = false, $start = false)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $start = (int) $start;
        $limit = 500;
        $this->setTable('categories');
        $categories = $this->fetch(array(
            'ID'
        ), null, "ORDER BY `Parent_ID`", array(
            $start,
            $limit
        ));
        $this->resetTable();
        foreach ($categories as $key => $value) {
            $main_form = $this->fetch(array(
                'ID'
            ), array(
                'Category_ID' => $categories[$key]['ID']
            ), "ORDER BY `Position`", null, 'listing_relations');
            foreach ($main_form as $sKey => $sVal) {
                $pos                     = $sKey + 1;
                $update[$sKey]['where']  = array(
                    'ID' => $main_form[$sKey]['ID']
                );
                $update[$sKey]['fields'] = array(
                    'Position' => $pos
                );
            }
            if (!empty($update)) {
                $GLOBALS['rlActions']->update($update, 'listing_relations');
            }
            $short_form = $this->fetch(array(
                'ID'
            ), array(
                'Category_ID' => $categories[$key]['ID']
            ), "ORDER BY `Position`", null, 'short_forms');
            unset($update);
            foreach ($short_form as $sKey => $sVal) {
                $pos                     = $sKey + 1;
                $update[$sKey]['where']  = array(
                    'ID' => $short_form[$sKey]['ID']
                );
                $update[$sKey]['fields'] = array(
                    'Position' => $pos
                );
            }
            if (!empty($update)) {
                $GLOBALS['rlActions']->update($update, 'short_forms');
            }
            $listing_titles = $this->fetch(array(
                'ID'
            ), array(
                'Category_ID' => $categories[$key]['ID']
            ), "ORDER BY `Position`", null, 'listing_titles');
            unset($update);
            foreach ($listing_titles as $sKey => $sVal) {
                $pos                     = $sKey + 1;
                $update[$sKey]['where']  = array(
                    'ID' => $listing_titles[$sKey]['ID']
                );
                $update[$sKey]['fields'] = array(
                    'Position' => $pos
                );
            }
            if (!empty($update)) {
                $GLOBALS['rlActions']->update($update, 'listing_titles');
            }
            $featured_form = $this->fetch(array(
                'ID'
            ), array(
                'Category_ID' => $categories[$key]['ID']
            ), "ORDER BY `Position`", null, 'featured_form');
            unset($update);
            foreach ($featured_form as $sKey => $sVal) {
                $pos                     = $sKey + 1;
                $update[$sKey]['where']  = array(
                    'ID' => $featured_form[$sKey]['ID']
                );
                $update[$sKey]['fields'] = array(
                    'Position' => $pos
                );
            }
            if (!empty($update)) {
                $GLOBALS['rlActions']->update($update, 'featured_form');
            }
        }
        $this->setTable('search_forms');
        $forms = $this->fetch(array(
            'ID'
        ), null, "ORDER BY `ID`");
        $this->resetTable();
        foreach ($forms as $key => $value) {
            $search_form = $this->fetch(array(
                'ID'
            ), array(
                'Category_ID' => $forms[$key]['ID']
            ), "ORDER BY `Position`", null, 'search_forms_relations');
            unset($update);
            foreach ($search_form as $sKey => $sVal) {
                $pos                     = $sKey + 1;
                $update[$sKey]['where']  = array(
                    'ID' => $search_form[$sKey]['ID']
                );
                $update[$sKey]['fields'] = array(
                    'Position' => $pos
                );
            }
            if (!empty($update)) {
                $GLOBALS['rlActions']->update($update, 'search_forms_relations');
            }
        }
        if (count($categories) == $limit) {
            $start += $limit;
            $_response->script("xajax_reorderFields('{$mode}', '{$self}', {$start});");
            unset($categories);
            return $_response;
        }
        if ((bool) $mode === true) {
            $_response->script("printMessage('notice', '{$lang['positions_reordered']}')");
            $_response->script("$('{$self}').val('{$lang['reorder']}');");
        }
        return $_response;
    }
    function ajaxUpdateCache($mode = true, $self = false)
    {
        global $_response, $lang, $rlCache;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $rlCache->update();
        if ((bool) $mode === true) {
            $_response->script("printMessage('notice', '{$lang['cache_updated']}')");
            $_response->script("$('{$self}').val('{$lang['update']}');");
        }
        return $_response;
    }
}