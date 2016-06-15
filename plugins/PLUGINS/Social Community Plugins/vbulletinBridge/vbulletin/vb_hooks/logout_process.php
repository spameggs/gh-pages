<?php
if (VB_AREA != 'Flynax') {
    session_start();
    unset($_SESSION['id'], $_SESSION['username'], $_SESSION['password'], $_SESSION['type'], $_SESSION['type_id'], $_SESSION['abilities'], $_SESSION['account']);
}