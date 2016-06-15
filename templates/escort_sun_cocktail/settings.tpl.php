<?php
$tpl_settings = array(
    'inventory_menu' => false,
    'right_block' => true,
    'main_menu_width' => false,
    'random_featured' => false,
    'girls_category_id' => 90,
    'guys_category_id' => 91
);
if (is_object($rlSmarty)) {
    $rlSmarty->assign_by_ref('tpl_settings', $tpl_settings);
}