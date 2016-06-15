<?php
$tpl_settings = array(
    'inventory_menu' => false,
    'right_block' => true,
    'main_menu_width_calculation' => true,
    'main_menu_width' => 680
);
if (is_object($rlSmarty)) {
    $rlSmarty->assign_by_ref('tpl_settings', $tpl_settings);
}