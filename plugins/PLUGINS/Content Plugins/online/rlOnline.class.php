<?php
class rlOnline extends reefless
{
    function statistics()
    {
        global $config;
        $userIP         = $_SERVER['REMOTE_ADDR'];
        $userAgent      = $_SERVER['HTTP_USER_AGENT'];
        $sessionHash    = md5($userIP . $userAgent);
        $isUser         = defined('IS_LOGIN') ? 1 : 0;
        $nowDate        = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));
        $onlineDowntime = $nowDate - ($config['online_downtime'] * 60);
        if (false === $this->isBot($userAgent)) {
            $online = $this->getOne('sess_id', "`sess_id` = '{$sessionHash}'", 'online');
            if (!empty($online)) {
                $this->query("UPDATE `" . RL_DBPREFIX . "online` SET `last_online` = '{$nowDate}' , `visibility` = '1', `is_login` = '{$isUser}' WHERE `sess_id` = '{$sessionHash}' LIMIT 1");
            } else {
                $this->query("INSERT INTO `" . RL_DBPREFIX . "online` ( `sess_id`, `ip`, `last_online`, `visibility`, `is_login` ) VALUES ( '{$sessionHash}', '{$userIP}', '{$nowDate}', '1', '{$isUser}' )");
            }
        }
        $this->query("UPDATE `" . RL_DBPREFIX . "online` SET `visibility` = '0' WHERE `last_online` < '{$onlineDowntime}'");
    }
    function fetchStatisticsInfo()
    {
        global $config;
        $nowDate        = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));
        $onlineLastHour = $nowDate - ($config['online_last_hour'] * 3600);
        $onlineLastDay  = $nowDate - ($config['online_last_day'] * 3600);
        $sql            = "SELECT COUNT(`ID`) AS `total`, ";
        $sql .= "( SELECT COUNT(`ID`) FROM `" . RL_DBPREFIX . "online` WHERE `is_login` = '1' AND `visibility` = '1' ) AS `users`, ";
        $sql .= "( SELECT COUNT(`ID`) FROM `" . RL_DBPREFIX . "online` WHERE `is_login` = '0' AND `visibility` = '1' ) AS `guests`, ";
        $sql .= "( SELECT COUNT(`ID`) FROM `" . RL_DBPREFIX . "online` WHERE `last_online` > '{$onlineLastHour}' ) AS `lastHour`, ";
        $sql .= "( SELECT COUNT(`ID`) FROM `" . RL_DBPREFIX . "online` WHERE `last_online` > '{$onlineLastDay}' ) AS `lastDay` ";
        $sql .= "FROM `" . RL_DBPREFIX . "online` WHERE `visibility` = '1'";
        return $this->getRow($sql);
    }
    function ajaxAdminStatistics()
    {
        global $_response;
        $statistics = $this->fetchStatisticsInfo();
        $GLOBALS['rlSmarty']->assign('onlineStatistics', $statistics);
        unset($statistics);
        $tpl = RL_PLUGINS . 'online' . RL_DS . 'admin' . RL_DS . 'statistics_dom.tpl';
        $_response->assign("online_block_container", 'innerHTML', $GLOBALS['rlSmarty']->fetch($tpl, null, null, false));
        $_response->script("$('#online_block_container').fadeIn('normal', function() { $(this).parent().removeClass('block_loading'); });");
        return $_response;
    }
    function isBot($userAgent = '')
    {
        if (empty($userAgent))
            return true;
        $bots = array(
            "google",
            "bot",
            "radian",
            "yahoo",
            "spider",
            "crawl",
            "archiver",
            "curl",
            "yandex",
            "python",
            "nambu",
            "eventbox",
            "twitt",
            "perl",
            "monitor",
            "sphere",
            "PEAR",
            "mechanize",
            "java",
            "wordpress",
            "facebookexternal"
        );
        foreach ($bots as $bot) {
            if (false !== strpos($userAgent, $bot)) {
                return true;
            }
        }
        return false;
    }
}