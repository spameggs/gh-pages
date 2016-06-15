<?php
global $reefless, $dConfig;
if (!empty($dConfig['categories_icons_width']['value']) && !empty($dConfig['categories_icons_height']['value'])) {
    $reefless->loadClass('CategoriesIcons', null, 'categories_icons');
    $GLOBALS['rlCategoriesIcons']->updateIcons((int) $dConfig['categories_icons_width']['value'], (int) $dConfig['categories_icons_height']['value']);
}