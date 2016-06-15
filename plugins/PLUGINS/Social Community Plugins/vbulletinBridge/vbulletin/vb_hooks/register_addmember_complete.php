<?php
if (VB_AREA != 'Flynax') {
    $configLocation = str_replace('plugins/vbulletin/vb_hooks', 'includes/config.inc.php', __DIR__);
    if (file_exists($configLocation)) {
        require_once($configLocation);
        if ($vbulletin->config['Database']['dbname'] != RL_DBNAME) {
            $vbulletin->db->connect(RL_DBNAME, RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS);
        }
        $accountType = "SELECT `Default` FROM `" . RL_DBPREFIX . "config` WHERE `Key` = 'vbulletin_flynax_account_type'";
        $query       = $vbulletin->db->query_read_slave($accountType);
        $accountType = $vbulletin->db->fetch_array($query);
        $accountType = $accountType['Default'];
        $username    = stripslashes($vbulletin->GPC['username']);
        $password    = $vbulletin->GPC['password_md5'];
        $email       = $vbulletin->GPC['email'];
        $ownAddress  = preg_replace("/[^a-z0-9]+/i", '-', $username);
        $ownAddress  = preg_replace('/\-+/', '-', $ownAddress);
        $ownAddress  = strtolower($ownAddress);
        $ownAddress  = trim($ownAddress, '-');
        $ownAddress  = trim($ownAddress, '/');
        $ownAddress  = trim($ownAddress);
        $insert      = "INSERT INTO `" . RL_DBPREFIX . "accounts` ( `Type`, `Username`, `Own_address`, `Password`, `Mail`, `Date` ) ";
        $insert .= "VALUES ( '{$accountType}', '{$username}', '{$ownAddress}', '{$password}', '{$email}', NOW() )";
        $vbulletin->db->query_write($insert);
        if ($vbulletin->config['Database']['dbname'] != RL_DBNAME) {
            $dbConf   = $vbulletin->config['Database'];
            $dbMaster = $vbulletin->config['MasterServer'];
            $vbulletin->db->connect($dbConf['dbname'], $dbMaster['servername'], $dbMaster['port'], $dbMaster['username'], $dbMaster['password']);
        }
    }
}