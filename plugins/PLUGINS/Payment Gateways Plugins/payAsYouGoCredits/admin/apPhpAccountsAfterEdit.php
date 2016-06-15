<?php
global $rlDb, $profile_data;
$id = (int) $_GET['account'];
if (isset($_POST['Total_credits'])) {
    $balance    = (float) $_POST['Total_credits'];
    $sql_update = "UPDATE `" . RL_DBPREFIX . "accounts` SET `Total_credits` = '{$balance}', `paygc_pay_date` = NOW() WHERE `ID` = '{$id}'";
    $rlDb->query($sql_update);
}