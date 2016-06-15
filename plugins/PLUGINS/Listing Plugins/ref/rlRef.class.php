<?php
class rlRef extends reefless
{
    var $rlLang;
    var $rlValid;
    function rlRef()
    {
        global $rlLang, $rlValid;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
    }
    function generate($listing_id = false, $ref_tpl = 'RF******')
    {
        $rlength = substr_count($ref_tpl, '*');
        $rand    = substr(mt_rand(), 0, $rlength);
        $ref     = str_replace(str_repeat('*', $rlength), $rand, $ref_tpl);
        $ref     = str_replace('#ID#', $listing_id, $ref);
        if ($this->getOne("ID", "`ref_number` = '{$ref}' AND `ID` != '" . $listing_id . "'", 'listings')) {
            return $this->generate($listing_id, $ref_tpl);
        } else {
            return $ref;
        }
    }
    function ajaxRefSearch($ref)
    {
        global $_response, $lang;
        $ref = $GLOBALS['rlValid']->xSql($ref);
        $sql = "SELECT `T1`.*, `T2`.`Path`, `T2`.`Key` AS `Cat_key`, `T3`.`Image`, `T2`.`Type` AS `Cat_type` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T3` ON `T1`.`Plan_ID` = `T3`.`ID` ";
        $sql .= "WHERE UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T3`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW() OR `T3`.`Listing_period` = 0) ";
        $sql .= "AND `T1`.`ref_number` = '{$ref}' AND `T1`.`Status` = 'active' LIMIT 1";
        $listing_data = $GLOBALS['rlDb']->getRow($sql);
        $GLOBALS['reefless']->loadClass('Listings');
        $listing_title = $GLOBALS['rlListings']->getListingTitle($listing_data['Category_ID'], $listing_data, $listing_data['Cat_type']);
        $listing       = $GLOBALS['rlDb']->fetch('*', array(
            'ref_number' => $ref
        ), null, null, 'listings', 'row');
        if ($listing_data) {
            $page_path = $GLOBALS['pages'][$GLOBALS['rlListingTypes']->types[$listing_data['Cat_type']]['Page_key']];
            $link      = $GLOBALS['config']['mod_rewrite'] ? SEO_BASE . $page_path . '/' . $listing_data['Path'] . '/' . $GLOBALS['rlSmarty']->str2path(array(
                'string' => $listing_title
            )) . '-' . $listing_data['ID'] . '.html' : RL_URL_HOME . 'index.php?page=' . $page_path . '&amp;id=' . $listing_data['ID'];
            $_response->redirect($link);
        } else {
            $_response->script("
				$('form[name=refnumber_lookup] input[type=submit]').val('{$lang['search']}');
				printMessage('error', '{$lang['ref_not_found']}');
			");
        }
        return $_response;
    }
    function ajaxRebuildRefs($self, $start)
    {
        global $_response, $lang;
        $GLOBALS['reefless']->loadClass('Ref', null, 'ref');
        $start    = $start ? $start : 0;
        $limit    = 1000;
        $listings = $GLOBALS['rlDb']->fetch(array(
            'ID'
        ), NULL, NULL, array(
            $start,
            $limit
        ), 'listings');
        foreach ($listings as $key => $listing) {
            $rn  = $GLOBALS['rlRef']->generate($listing['ID'], $GLOBALS['config']['ref_tpl']);
            $sql = "UPDATE `" . RL_DBPREFIX . "listings` SET `ref_number` = '" . $rn . "' WHERE `ID` = '" . $listing['ID'] . "'";
            $GLOBALS['rlDb']->query($sql);
        }
        if (count($listings) == $limit) {
            $next_limit = $start + $limit;
            $_response->script("xajax_rebuildRefs('{$self}','{$next_limit}');");
            return $_response;
        }
        $_response->script("printMessage('notice', '{$lang['ref_rebuilt']}')");
        $_response->script("$('{$self}').val('{$lang['rebuild']}');");
        return $_response;
    }
}