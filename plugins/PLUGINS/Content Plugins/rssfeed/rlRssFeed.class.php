<?php
class rlRssFeed extends reefless
{
    function get($rss_id)
    {
        global $rlRss;
        $rss = array();
        $rss = $this->fetch(array(
            'ID',
            'Link',
            'Article_num',
            'Side'
        ), array(
            'ID' => $rss_id,
            'Status' => 'active'
        ), null, 1, 'rss_feed', 'row');
        if (!empty($rss)) {
            $rlRss->items_number = $rss['Article_num'];
            $rlRss->createParser($GLOBALS['reefless']->getPageContent($rss['Link']));
            $rss_feeds = $rlRss->getRssContent();
            $rlRss->clear();
            $rss_side = $rss['Side'];
            $GLOBALS['rlSmarty']->assign_by_ref('rss_feed', $rss_feeds);
            $GLOBALS['rlSmarty']->assign_by_ref('rss_side', $rss_side);
        }
        $tpl = RL_PLUGINS . 'rssfeed' . RL_DS . 'rssfeed.block.tpl';
        $GLOBALS['rlSmarty']->display($tpl);
    }
    function ajaxDeleteRss($id = false)
    {
        global $_response, $lang;
        $id = (int) $id;
        if ($this->checkSessionExpire() === false) {
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php?action=session_expired');
            return $_response;
        }
        if (!$id) {
            return $_response;
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "rss_feed` WHERE `ID` = '{$id}' LIMIT 1");
        $this->query("DELETE FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = 'rssfeed_{$id}' LIMIT 1");
        $this->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'blocks+name+rssfeed_{$id}'");
        $_response->script("
			printMessage('notice', '{$lang['item_deleted']}');
			rssFeedGrid.reload();
		");
        return $_response;
    }
}