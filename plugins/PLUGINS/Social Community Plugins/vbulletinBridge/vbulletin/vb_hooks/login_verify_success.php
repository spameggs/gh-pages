<?php
if (VB_AREA != 'Flynax') {
    if (!in_array($vbulletin->GPC['logintype'], array(
        'cplogin',
        'modcplogin'
    ))) {
        $configLocation = str_replace('plugins/vbulletin/vb_hooks', 'includes/config.inc.php', __DIR__);
        if (file_exists($configLocation)) {
            require_once($configLocation);
            if ($vbulletin->config['Database']['dbname'] != RL_DBNAME) {
                $vbulletin->db->connect(RL_DBNAME, RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS);
            }
            $sql = "SELECT `T1`.*, `T2`.`Abilities`, `T2`.`ID` AS `Type_ID`, `T2`.`Own_location` ";
            $sql .= "FROM `" . RL_DBPREFIX . "accounts` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "account_types` AS `T2` ON `T1`.`Type` = `T2`.`Key` ";
            $sql .= "WHERE `T1`.`Username` = '{$username}' AND `T1`.`Status` <> 'trash'";
            $query   = $vbulletin->db->query_read_slave($sql);
            $account = $vbulletin->db->fetch_array($query);
            if (!empty($account)) {
                session_start();
                $abilities             = explode(',', $account['Abilities']);
                $abilities             = empty($abilities[0]) ? false : $abilities;
                $_SESSION['id']        = $account['ID'];
                $_SESSION['username']  = $account['Username'];
                $_SESSION['password']  = md5($account['Password']);
                $_SESSION['type']      = $account['Type'];
                $_SESSION['type_id']   = $account['Type_ID'];
                $_SESSION['abilities'] = $abilities;
                unset($account['Confirm_code']);
                $account['Password']  = md5($account['Password']);
                $account['Full_name'] = $account['First_name'] || $account['Last_name'] ? $account['First_name'] . ' ' . $account['Last_name'] : $account['Username'];
                $account['Abilities'] = $abilities;
                $_SESSION['account']  = $account;
            }
            if ($vbulletin->config['Database']['dbname'] != RL_DBNAME) {
                $dbConf   = $vbulletin->config['Database'];
                $dbMaster = $vbulletin->config['MasterServer'];
                $vbulletin->db->connect($dbConf['dbname'], $dbMaster['servername'], $dbMaster['port'], $dbMaster['username'], $dbMaster['password']);
            }
        }
    }
}