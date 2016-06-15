<?php
global $rlDb;
if ($GLOBALS['config']['paygc_period'] > 0) {
    $days     = (int) $GLOBALS['config']['paygc_period'] * 30 * 24;
    $sql      = "SELECT `ID`, `Total_credits`, IF(TIMESTAMPDIFF(HOUR, `paygc_pay_date`, NOW()) > " . $days . ", '1', '0') `expired` FROM `" . RL_DBPREFIX . "accounts` WHERE `Status` = 'active' ";
    $accounts = $GLOBALS['rlDb']->getAll($sql);
    foreach ($accounts as $key => $account) {
        if ($account['expired']) {
            $sql_update = "UPDATE `" . RL_DBPREFIX . "accounts` SET `Total_credits` = '0', `paygc_pay_date` = '0000-00-00 00:00:00' WHERE `{$account['ID']}`";
            $GLOBALS['rlDb']->query($sql_update);
        }
    }
}