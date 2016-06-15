<?php
class rlReportBrokenListing extends reefless
{
    function ajaxRreportBrokenListing($listing_id = false, $message = false)
    {
        global $_response, $page_info, $lang, $account_info, $rlValid;
        $listing_id = (int) $listing_id;
        if (!$listing_id)
            return $_response;
        if (empty($message)) {
            $_response->script("printMessage('error', '{$lang['reportbroken_you_should_add_comment']}')");
        } else {
            $this->loadClass('Actions');
            $insert = array(
                'ID' => '',
                'Listing_ID' => $listing_id,
                'Account_ID' => defined('IS_LOGIN') ? $account_info['ID'] : '',
                'Message' => $message,
                'Date' => 'NOW()'
            );
            $GLOBALS['rlActions']->insertOne($insert, 'report_broken_listing');
            $_response->script("
				printMessage('notice', '{$lang['reportbroken_listing_has_been_added']}');
				$('#modal_block>div.inner>div.close').trigger('click');
				reportBrokenLisitngIcon({$listing_id});
			");
        }
        return $_response;
    }
    function ajaxRemoveReportBrokenListing($listing_id = false)
    {
        global $_response, $page_info, $lang;
        $listing_id = (int) $listing_id;
        if (!$listing_id)
            return $_response;
        $this->query("DELETE FROM `" . RL_DBPREFIX . "report_broken_listing` WHERE `Listing_ID` = '{$listing_id}'");
        $_response->script("
			printMessage('notice', '{$lang['reportbroken_listing_has_been_removed']}');
			reportBrokenLisitngIcon({$listing_id});
		");
        return $_response;
    }
    function ajaxDeletereportBrokenListing($id = false)
    {
        global $_response, $lang;
        $id = (int) $id;
        if (!$id)
            return $_response;
        if ($this->checkSessionExpire() === false) {
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php?action=session_expired');
            return $_response;
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "report_broken_listing` WHERE `ID` = '{$id}' LIMIT 1");
        $_response->script("
			reportGrid.reload();
			printMessage('notice', '{$lang['item_deleted']}');
		");
        return $_response;
    }
    function ajaxDeleteListing($id = false)
    {
        global $_response, $config, $lang;
        $id = (int) $id;
        if (!$id)
            return $_response;
        $listing_id  = $this->getOne('Listing_ID', "`ID` = '{$id}'", 'report_broken_listing');
        $category_id = $this->getOne('Category_ID', "`ID` = '{$listing_id}'", 'listings');
        $this->loadClass('Categories');
        $GLOBALS['rlCategories']->listingsDecrease($category_id);
        $GLOBALS['rlActions']->delete(array(
            'ID' => $listing_id
        ), 'listings', $id, 1);
        $this->query("DELETE FROM `" . RL_DBPREFIX . "report_broken_listing` WHERE `ID` = '{$id}' LIMIT 1");
        if (!$config['trash']) {
            $this->loadClass('Listings', 'admin');
            $GLOBALS['rlListings']->deleteListingData($listing_id);
        }
        $del_action = $GLOBALS['rlActions']->action;
        $_response->script("
			reportGrid.reload();
			printMessage('notice', '{$GLOBALS['lang']['mass_listings_'.$del_action]}');
		");
        return $_response;
    }
    function deleteListingData($id)
    {
        $broken = $this->query("DELETE FROM `" . RL_DBPREFIX . "report_broken_listing` WHERE `Listing_ID` = '{$id}' LIMIT 1");
        $photos = $this->fetch(array(
            'Photo',
            'Thumbnail'
        ), array(
            'Listing_ID' => $id
        ), null, null, 'listing_photos');
        $video  = $this->fetch(array(
            'Video',
            'Preview'
        ), array(
            'Listing_ID' => $id
        ), null, 1, 'listing_video', 'row');
        foreach ($photos as $pKey => $pValue) {
            unlink(RL_FILES . $photos[$pKey]['Photo']);
            unlink(RL_FILES . $photos[$pKey]['Thumbnail']);
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "listing_photos` WHERE `Listing_ID` = '{$id}'");
        unlink(RL_FILES . $photos['Video']);
        unlink(RL_FILES . $photos['Preview']);
        $this->query("DELETE FROM `" . RL_DBPREFIX . "listing_video` WHERE `Listing_ID` = '{$id}'");
    }
}