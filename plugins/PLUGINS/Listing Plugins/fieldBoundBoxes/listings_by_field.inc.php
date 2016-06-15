<?php
$reefless->loadClass('Search');
$reefless->loadClass('FieldBoundBoxes', null, 'fieldBoundBoxes');
if ($config['mod_rewrite']) {
    $path  = $_GET['nvar_1'];
    $value = $_GET['nvar_2'];
} else {
    foreach ($_GET as $key => $val) {
        if ($key != 'page' && $key != 'sort_by' && $key != 'sort_type' && $key != 'pg') {
            $path  = $key;
            $value = $val;
        }
    }
}
if ($value) {
    $field_info       = $rlDb->fetch(array(
        'Listing_type',
        'Field_key'
    ), array(
        'Path' => $path
    ), null, null, 'field_bound_boxes', 'row');
    $field            = $field_info['Field_key'];
    $listing_type     = $rlListingTypes->types[$field_info['Listing_type']];
    $pInfo['current'] = (int) $_GET['pg'];
    $sorting          = array(
        'category' => array(
            'name' => $lang['category'],
            'field' => 'Category_ID'
        ),
        'status' => array(
            'name' => $lang['status'],
            'field' => 'Status'
        ),
        'expire_date' => array(
            'name' => $lang['expire_date'],
            'field' => 'Plan_expire'
        )
    );
    $rlSmarty->assign_by_ref('sorting', $sorting);
    $sort_by = empty($_GET['sort_by']) ? $_SESSION['ml_sort_by'] : $_GET['sort_by'];
    if (!empty($sorting[$sort_by])) {
        $order_field = $sorting[$sort_by]['field'];
    }
    $_SESSION['ml_sort_by'] = $sort_by;
    $rlSmarty->assign_by_ref('sort_by', $sort_by);
    $sort_type                = empty($_GET['sort_type']) ? $_SESSION['ml_sort_type'] : $_GET['sort_type'];
    $sort_type                = in_array($sort_type, array(
        'asc',
        'desc'
    )) ? $sort_type : false;
    $_SESSION['ml_sort_type'] = $sort_type;
    $rlSmarty->assign_by_ref('sort_type', $sort_type);
    if ($pInfo['current'] > 1) {
        $bc_page = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
        $bread_crumbs[1]['title'] .= $bc_page;
    }
    $listings = $rlFieldBoundBoxes->getListings(array(
        "field" => $field,
        "value" => $value
    ), $listing_type['Key'], $order_field, $sort_type, $pInfo['current'], $config['listings_per_page']);
    $rlSmarty->assign_by_ref('listings', $listings);
    $pInfo['calc'] = $rlFieldBoundBoxes->calc;
    $rlSmarty->assign_by_ref('pInfo', $pInfo);
    $rlHook->load('searchMiddle');
    $bread_crumbs[1] = array(
        'name' => $lang['listing_fields+name+' . $field_info['Field_key']],
        'path' => $path
    );
    if ($field_condition = $rlDb->getOne("Condition", "`Key` ='" . $field_info['Field_key'] . "'", 'listing_fields')) {
        $option_name = $lang['data_formats+name+' . $field_condition . "_" . $value] ? $lang['data_formats+name+' . $field_condition . "_" . $value] : $lang['data_formats+name+' . $value];
        $add_key     = $field_condition;
    } else {
        $option_name = $lang['listing_fields+name+' . $field_info['Field_key'] . "_" . $value];
        $add_key     = $field_info['Field_key'];
    }
    $description = $lang['field_bound_items+des+' . $add_key . "_" . $value] ? $lang['field_bound_items+des+' . $add_key . "_" . $value] : $lang['field_bound_items+des+' . $value];
    $rlSmarty->assign('description', $description);
    $page_info['meta_description'] = $lang['field_bound_items+meta_description+' . $add_key . "_" . $value] ? $lang['field_bound_items+meta_description+' . $add_key . "_" . $value] : $lang['field_bound_items+meta_description+' . $value];
    $page_info['meta_keywords']    = $lang['field_bound_items+meta_keywords+' . $add_key . "_" . $value] ? $lang['field_bound_items+meta_keywords+' . $add_key . "_" . $value] : $lang['field_bound_items+meta_keywords+' . $value];
    $bread_crumbs[]                = array(
        'name' => $option_name
    );
    if ($listings) {
        $page_info['name'] = str_replace(array(
            '{number}',
            '{type}'
        ), array(
            $pInfo['calc'],
            $option_name . " " . $listing_type['name']
        ), $lang['listings_found']);
    }
} elseif ($path) {
    $box_info        = $rlDb->fetch(array(
        'ID',
        'Field_key',
        'Page_columns',
        'Icons_position',
        'Path',
        'Icons',
        'Show_count',
        'Postfix'
    ), array(
        "Path" => $path
    ), null, null, "field_bound_boxes", 'row');
    $field_condition = $rlDb->getOne("Condition", "`Key` = '" . $box_info['Field_key'] . "'", "listing_fields");
    $replacement     = $field_condition ? $field_condition . "_" : $box_info['Field_key'] . "_";
    $sql             = "SELECT *, REPLACE(`Key`, '" . $replacement . "', '') as `Key` FROM `" . RL_DBPREFIX . "field_bound_items` ";
    $sql .= "WHERE `Status` = 'active' AND `Box_ID` = {$box_info['ID']} ORDER BY `Position`";
    $items = $rlDb->getAll($sql);
    $rlSmarty->assign('options', $items);
    $rlSmarty->assign("field_key", $box_info['Field_key']);
    $rlSmarty->assign("path", $box_info['Path']);
    $rlSmarty->assign("columns_number", $box_info['Page_columns']);
    $rlSmarty->assign("icons_position", $box_info['Icons_position']);
    $rlSmarty->assign("show_count", $box_info['Show_count']);
    $rlSmarty->assign("html_postfix", $box_info['Postfix']);
    $bread_crumbs[1]   = array(
        'name' => $lang['listing_fields+name+' . $box_info['Field_key']],
        'path' => $path
    );
    $page_info['name'] = $lang['listing_fields+name+' . $box_info['Field_key']];
}