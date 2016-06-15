<?php
global $rlDb, $profile_data;
if (isset($_POST['Total_credits'])) {
    $balance    = (float) $_POST['Total_credits'];
    $sql_update = "UPDATE `" . RL_DBPREFIX . "accounts` SET `Total_credits` = '{$balance}', `paygc_pay_date` = NOW() WHERE `Username` = '{$profile_data['username']}'";
    $rlDb->query($sql_update);
}