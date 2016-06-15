<?php
class rlBookmarks extends reefless
{
    var $services = array('google_plusone' => 'Google Plus', 'facebook_like' => 'Facebook Like', 'tweet' => 'Tweet', 'counter' => 'AddThis Counter', 'compact' => 'AddThis Button', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'email' => 'Email', 'print' => 'Print', 'google' => 'Google', 'myspace' => 'MySpace', 'stumbleupon' => 'StumbleUpon', 'favorites' => 'Favorites', 'live' => 'Messenger', 'delicious' => 'Delicious', 'digg' => 'Digg', 'orkut' => 'orkut', 'blogger' => 'Blogger', 'gmail' => 'Gmail', 'yahoomail' => 'Y! Mail', 'reddit' => 'Reddit', 'vk' => 'vk.com', 'aim' => 'Aol Lifestream', 'meneame' => 'Meneame', 'mailto' => 'Email App', 'googlebuzz' => 'Google Buzz', 'hotmail' => 'Hotmail', 'linkedin' => 'LinkedIn', 'yahoobkm' => 'Y! Buzz', 'viadeo' => 'Viadeo', 'aolmail' => 'AOL Mail', 'friendfeed' => 'FriendFeed', 'tumblr' => 'tumblr', 'friendster' => 'Friendster', 'baidu' => 'Baidu', 'wordpress' => 'Wordpress', 'yahoobkm' => 'Y! Bookmarks', '100zakladok' => '100zakladok', 'misterwong_de' => 'Mister Wong DE', 'hyves' => 'Hyves', 'sonico' => 'Sonico', 'amazonwishlist' => 'Amazon', 'bebo' => 'Bebo', 'bitly' => 'Bit.ly', 'addio' => 'Add.io', 'bobrdobr' => 'Bobrdobr', 'adifni' => 'Adifni', 'dotnetshoutout' => 'DotNetShoutout', '2tag' => '2 Tag', 'googlereader' => 'Google Reader', 'studivz' => 'studiVZ', 'fark' => 'Fark', 'livejournal' => 'LiveJournal', 'allmyfaves' => 'All My Faves', 'oyyla' => 'Oyyla');
    var $bookmarks = array('googleplus_like_tweet' => array('Key' => 'googleplus_like_tweet', 'Name' => 'bsh_googleplus_like_tweet', 'Align' => true, 'Services' => array('counter', 'google_plusone', 'facebook_like', 'tweet')), 'floating_bar' => array('Key' => 'floating_bar', 'Name' => 'bsh_floating_bar', 'Align' => true, 'Services' => true, 'Color' => true), 'vertical_share_counter' => array('Key' => 'vertical_share_counter', 'Name' => 'bsh_vertical_share_counter', 'Align' => true), 'horizontal_share_counter' => array('Key' => 'horizontal_share_counter', 'Name' => 'bsh_horizontal_share_counter', 'Align' => true), 'tweet_like_share' => array('Key' => 'tweet_like_share', 'Name' => 'bsh_tweet_like_share', 'Align' => true), 'toolbox_facebook_like' => array('Key' => 'toolbox_facebook_like', 'Name' => 'bsh_toolbox_facebook_like', 'Align' => true, 'Services' => true), '32x32_icons_addthis' => array('Key' => '32x32_icons_addthis', 'Name' => 'bsh_32x32_icons_addthis', 'Align' => true, 'Services' => true), '64x64_icons_aquaticus' => array('Key' => '64x64_icons_aquaticus', 'Name' => 'bsh_64x64_icons_aquaticus', 'Align' => true, 'Services' => array('compact', 'facebook', 'twitter', 'myspace', 'stumbleupon', 'delicious', 'reddit')), 'css3_share_buttons' => array('Key' => 'css3_share_buttons', 'Name' => 'bsh_css3_share_buttons', 'Align' => true, 'Color' => true), '32x32_vertical_icons' => array('Key' => '32x32_vertical_icons', 'Name' => 'bsh_32x32_vertical_icons', 'Align' => true, 'Services' => true), 'share_button' => array('Key' => 'share_button', 'Name' => 'bsh_share_button', 'Align' => true), 'vertical_layout_menu' => array('Key' => 'vertical_layout_menu', 'Name' => 'bsh_vertical_layout_menu', 'Align' => true, 'Color' => true, 'Services' => true));
    function ajaxDeleteBookmark($id)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $id  = (int) $id;
        $key = $this->getOne('Key', "`ID` = {$id}", 'bookmarks');
        $sql = "DELETE FROM `" . RL_DBPREFIX . "bookmarks` WHERE `Key` = '{$key}' LIMIT 1";
        $this->query($sql);
        $sql = "DELETE FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = 'bookmark_{$key}' LIMIT 1";
        $this->query($sql);
        $sql = "DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'blocks+name+bookmark_{$key}'";
        $this->query($sql);
        $_response->script("
			bookmarkGrid.reload();
			printMessage('notice', '{$lang['block_deleted']}');
		");
        return $_response;
    }
}