<?php
class rlCategoryFilter extends reefless
{
    var $box_content;
    var $filters;
    var $fields_info;
    var $box_fields;
    var $no_sort_limit = 10;
    var $split_by = 6;
    var $count_max = false;
    var $items_max = false;
    function rlCategoryFilter()
    {
        global $rlSmarty;
        if (!$rlSmarty->_plugins['compiler']['continue']) {
            function flContinue($contents, &$smarty)
            {
                return 'continue';
            }
            $rlSmarty->register_compiler_function('continue', 'flContinue');
        }
        $this->box_content = <<< VS
		global \$rlSmarty, \$rlCategoryFilter;
		
		\$category = \$rlSmarty -> get_template_vars('category');
		if ( \$category['Path_back'] )
		{
			\$category['Path'] = \$category['Path_back'];
			\$rlSmarty -> assign('category', \$category);
		}
		
		\$box_id = {box_id};
		\$box_mode = '{box_mode}';
		\$box_type = '{box_type}';
		\$box_category = '{box_category}';
		
		\$item_names = '{item_names}';
		\$rlSmarty -> assign_by_ref('item_names', unserialize(\$item_names));
		\$rlSmarty -> assign_by_ref('cf_box_id', \$box_id);
		
		\$fields = <<< FL
		{fields}
FL;
		
		if ( \$box_mode == 'type' )
		{
			\$items = array();
			\$rlCategoryFilter -> request(unserialize(trim(\$fields)), \$box_id, \$items, true);
		}
		else
		{
			\$items = <<< IT
			{items}
IT;
			\$items = array_map('get_object_vars', get_object_vars(\$GLOBALS['rlJson'] -> decode(trim(\$items))));
			\$rlCategoryFilter -> request(unserialize(trim(\$fields)), \$box_id, \$items);
		}
		
		\$rlSmarty -> assign_by_ref('cf_items', \$items);
		
		\$rlSmarty -> display(RL_PLUGINS .'categoryFilter'. RL_DS . 'box.tpl');
VS;
        $this->loadClass('Json');
    }
    function saveForm($data = array())
    {
        if (!$data['category_id'])
            return false;
        $box_id = (int) $data['category_id'];
        unset($data['data']['ordering']);
        foreach ($data['data'] as $field_id_ind => $field) {
            if (!$field_id_ind)
                continue;
            $field_id    = explode('_', $field_id_ind);
            $field_ids[] = $field_id[1];
        }
        $sql = "SELECT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Condition`, `T1`.`Values`, CONCAT('listing_fields+name+', `T1`.`Key`) AS `pName`, ";
        $sql .= "`T2`.`Items_display_limit`, `T2`.`Mode`, `T2`.`Item_names` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listing_fields` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "category_filter_field` AS `T2` ON `T2`.`Field_ID` = `T1`.`ID` AND `T2`.`Box_ID` = '{$box_id}' ";
        $sql .= "WHERE `T1`.`Status` = 'active' AND (`T1`.`ID` = '" . implode("' OR `T1`.`ID` = '", $field_ids) . "') ORDER BY FIND_IN_SET(`T1`.`ID`, '" . implode(",", $field_ids) . "')";
        $fields_tmp = $this->getAll($sql);
        foreach ($fields_tmp as $field_tmp) {
            if ($field_tmp['Condition']) {
                $values = $this->fetch(array(
                    'Key'
                ), array(
                    'Status' => 'active',
                    'Plugin' => ''
                ), "AND `Key` LIKE '{$field_tmp['Condition']}_%'", null, 'data_formats');
                if ($values) {
                    foreach ($values as $value) {
                        $de_fields[] = $value['Key'];
                    }
                    $field_tmp['Values'] = implode(',', $de_fields);
                    unset($values, $de_fields);
                }
            }
            $fields[$field_tmp['Key']] = $field_tmp;
        }
        unset($fields_tmp);
        $this->updateFilter($fields, $box_id);
        $this->box_fields = $fields;
    }
    function clearForm($data = array())
    {
        unset($data['data']['ordering'], $data['data']['']);
        if (empty($data['data'])) {
            $box_id = (int) $data['category_id'];
            $sql    = "DELETE FROM `" . RL_DBPREFIX . "category_filter_field` WHERE `Box_ID` = '{$box_id}'";
            $this->query($sql);
            $this->compile($box_id);
        }
    }
    function updateFilter(&$fields, $box_id, $where = false, $box_mode = 'category')
    {
        global $rlActions, $config, $aHooks, $category;
        if ($fields && $box_mode == 'category') {
            $categories = $this->getOne('Category_IDs', "`ID` = '{$box_id}'", 'category_filter');
            $categories = explode(',', $categories);
        } elseif ($box_mode == 'type') {
            $categories[] = $category['ID'];
        }
        $check = array();
        foreach ($fields as &$field) {
            $check[]     = $field['ID'];
            $count_field = $field['Key'] == 'posted_by' ? "`T7`.`Type`" : "`T1`.`{$field['Key']}`";
            $sql         = "SELECT COUNT({$count_field}) AS `Number`, ";
            foreach ($categories as $tmp_category) {
                $sql .= "SUM(IF(";
                $sql .= "`T1`.`Category_ID` = '{$tmp_category}' OR (FIND_IN_SET('{$tmp_category}', `T1`.`Crossed`) > 0 AND `T2`.`Cross` > 0) ";
                if ($config['lisitng_get_children']) {
                    $sql .= "OR (FIND_IN_SET('{$tmp_category}', `T3`.`Parent_IDs`) > 0) ";
                }
                $sql .= ", 1, 0)) AS `Category_count_{$tmp_category}`, ";
            }
            if ($field['Key'] == 'posted_by') {
                $sql .= "`T7`.`Type` AS `{$field['Key']}` ";
            } else {
                $sql .= "`T1`.`{$field['Key']}` ";
            }
            $ccExists = $aHooks['currencyConverter'] || (defined('REALM') && REALM == 'admin' && $this->getOne('ID', "`Key` = 'currencyConverter' AND `Status` = 'active'", 'plugins'));
            if ($ccExists && $config['currencyConverter_price_field'] == $field['Key']) {
                $sql .= ", SUBSTRING_INDEX(`T1`.`{$field['Key']}`, '|', 1)/IF(`CURCONV`.`Rate` IS NULL, 1, `CURCONV`.`Rate`) AS `{$field['Key']}` ";
            } elseif (in_array($field['Type'], array(
                'price',
                'mixed'
            ))) {
                $sql .= ", SUBSTRING_INDEX(`T1`.`{$field['Key']}`, '|', 1) AS `{$field['Key']}` ";
            }
            $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
            if ($ccExists) {
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "currency_rate` AS `CURCONV` ON SUBSTRING_INDEX(`T1`.`{$field['Key']}`, '|', -1) = `CURCONV`.`Key` AND `CURCONV`.`Status` = 'active' ";
            }
            $GLOBALS['rlHook']->load('listingsModifyJoin', $sql);
            $sql .= "WHERE (UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0) ";
            if ($field['Key'] != 'posted_by') {
                $sql .= "AND `T1`.`{$field['Key']}` <> '' AND `T1`.`{$field['Key']}` NOT LIKE '%+%' ";
            }
            $sql .= "AND `T1`.`Status` = 'active' AND `T3`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
            if ($field['Type'] != 'bool' && $field['Key'] != 'posted_by') {
                $sql .= "AND `T1`.`{$field['Key']}` <> '0' ";
            }
            if ($categories) {
                $sql .= "AND (";
                foreach ($categories as $tmp_category) {
                    $sql .= $config['lisitng_get_children'] ? "(" : '';
                    $sql .= "(`T1`.`Category_ID` = '{$tmp_category}' OR (FIND_IN_SET('{$tmp_category}', `T1`.`Crossed`) > 0 AND `T2`.`Cross` > 0)) ";
                    if ($config['lisitng_get_children']) {
                        $sql .= "OR FIND_IN_SET('{$tmp_category}', `T3`.`Parent_IDs`) > 0)";
                    }
                    $sql .= "OR ";
                }
                $sql = rtrim($sql, 'OR ');
                $sql .= ") ";
            }
            if ($where || $box_mode == 'type') {
                $sql .= $where;
            }
            $hack = 'categoryFilter';
            $GLOBALS['rlHook']->load('listingsModifyWhere', $sql, $hack);
            $sql .= !is_numeric(strpos($sql, 'GROUP BY ')) ? "GROUP BY " : ', ';
            $sql .= $count_field;
            $sql .= " HAVING COUNT({$count_field}) > 0 ";
            $sql .= $this->count_max ? "AND `Number` <= {$this -> count_max} " : '';
            $sql .= "ORDER BY `Number` DESC ";
            $sql .= $this->items_max ? 'LIMIT ' . $this->items_max : '';
            $items = $this->getAll($sql);
            if (strpos($field['Key'], 'level')) {
                global $lang;
                $field['Items'] = "";
                $phrks          = array();
                foreach ($items as $mfk => $mfv) {
                    if ($field['Condition']) {
                        $phrks[] = "data_formats+name+" . $mfv[$field['Key']];
                    }
                }
                if ($phrks) {
                    $msql = "SELECT `Key`, `Value` FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Code` = '" . RL_LANG_CODE . "' AND (";
                    foreach ($phrks as $phk => $phkey) {
                        $msql .= "`Key` = '{$phkey}' OR ";
                    }
                    $msql = substr($msql, 0, -4);
                    $msql .= ")";
                    $mf_phrases = $this->getAll($msql);
                }
                foreach ($mf_phrases as $mfpk => $mfp) {
                    $GLOBALS['lang'][$mfp['Key']] = $mfp['Value'];
                }
            }
            if (count($items) > 0) {
                foreach ($items as $item) {
                    foreach ($categories as $tmp_category) {
                        if ($item['Number']) {
                            $value                            = $this->prepareValue($item[$field['Key']], $field, 'get');
                            $items_out[$value][$tmp_category] = $item['Category_count_' . $tmp_category];
                        }
                    }
                }
            }
            unset($items);
            $mode = 'auto';
            if (($field['Condition'] == 'years' && $field['Mode'] == 'group') || ($field['Type'] == 'price' && $field['Mode'] == 'group') || ($field['Type'] == 'mixed' && $field['Mode'] == 'group')) {
                $field['Type'] = 'number';
            }
            switch ($field['Type']) {
                case 'text':
                    break;
                case 'number':
                case 'mixed':
                    if ($items_out && (count($items_out) > $this->no_sort_limit || $field['Mode'] == 'group')) {
                        if (!$field['Item_names']) {
                            $sorted = $items_out;
                            ksort($sorted);
                            $first = key($sorted);
                            end($sorted);
                            $last = key($sorted);
                            unset($sorted);
                            $diff    = ceil(($last - $first) / ($this->split_by));
                            $current = $first;
                            while ($current < $last) {
                                $from = $current;
                                $to   = $from + $diff;
                                $from = $current > $first ? $from + 1 : $from;
                                $current += $diff;
                                foreach ($items_out as $item_key => $item) {
                                    if ($item_key >= $from && $item_key <= $to) {
                                        $items_new[$from . '-' . $to] = $this->flArraySum($item, $items_new[$from . '-' . $to]);
                                    }
                                }
                            }
                        } else {
                            $values = unserialize($field['Item_names']);
                            foreach ($values as $key => $value) {
                                preg_match('/([0-9]+)?([\<\-\>]+)?([0-9]+)?/', $key, $matches);
                                $from = $matches[1];
                                $to   = $matches[3];
                                $sign = $matches[2];
                                foreach ($items_out as $item_key => $item) {
                                    if (($sign == '-' && $item_key >= $from && $item_key <= $to) || ($sign == '>' && $item_key > $from) || ($sign == '<' && $item_key < $to)) {
                                        $items_new[$key] = $this->flArraySum($item, $items_new[$key]);
                                    }
                                }
                            }
                        }
                        if ($items_new) {
                            $items_out = $items_new;
                        }
                        unset($items_new);
                        $mode = 'group';
                    }
                    break;
                case 'price':
                    if (!$field['Mode']) {
                        $mode = 'slider';
                    }
                    break;
                case 'bool':
                    break;
                case 'select':
                    if ($field['Condition'] == 'years' && !$field['Mode']) {
                        $mode = 'slider';
                    }
                    break;
                case 'radio':
                    break;
                case 'checkbox':
                    if ($items_out) {
                        $values    = explode(',', $field['Values']);
                        $avb_items = array();
                        foreach ($items_out as $item_out_key => $item_out_value) {
                            $avb_items = array_merge($avb_items, explode(',', $item_out_key));
                        }
                        $avb_items = array_unique($avb_items);
                        $items_out = false;
                        if ($values[0]) {
                            foreach ($values as $item) {
                                if (is_numeric(array_search($item, $avb_items))) {
                                    foreach ($categories as $tmp_category) {
                                        $items_out[$item][$tmp_category] = 3;
                                    }
                                }
                            }
                        } else {
                            $items_out = false;
                        }
                        unset($avb_items);
                    }
                    break;
            }
            if ($where || $box_mode == 'type') {
                $field['Items']     = $items_out ? $GLOBALS['rlJson']->encode($items_out) : false;
                $out[$field['Key']] = $field;
            } else {
                if (!$this->getOne('ID', "`Box_ID` = '{$box_id}' AND `Field_ID` = '{$field['ID']}'", 'category_filter_field')) {
                    $field['Mode']                = $field['Mode'] ? $field['Mode'] : $mode;
                    $field['Items_display_limit'] = $field['Items_display_limit'] ? $field['Items_display_limit'] : 8;
                    $insert[]                     = array(
                        'Box_ID' => $box_id,
                        'Field_ID' => $field['ID'],
                        'Items' => $items_out && $box_mode == 'category' ? $GLOBALS['rlJson']->encode($items_out) : false,
                        'Items_display_limit' => 8,
                        'Mode' => $mode
                    );
                } else {
                    $update[] = array(
                        'fields' => array(
                            'Items' => $items_out ? $GLOBALS['rlJson']->encode($items_out) : false
                        ),
                        'where' => array(
                            'Box_ID' => $box_id,
                            'Field_ID' => $field['ID']
                        )
                    );
                }
            }
            unset($items_out);
        }
        $this->loadClass('Actions');
        if ($where || $box_mode == 'type') {
            return $out;
        }
        $rlActions->rlAllowHTML = true;
        if ($insert) {
            $rlActions->insert($insert, 'category_filter_field');
        }
        if ($update) {
            $rlActions->update($update, 'category_filter_field');
        }
        $sql = "DELETE FROM `" . RL_DBPREFIX . "category_filter_field` WHERE `Box_ID` = '{$box_id}' ";
        if ($check) {
            $sql .= "AND (`Field_ID` <> '" . implode("' AND `Field_ID` <> '", $check) . "')";
        }
        $this->query($sql);
    }
    function update($box_id = false, $mode = false)
    {
        if (!$box_id)
            return false;
        $mode = $mode ? $mode : $this->getOne('Mode', "`ID` = '{$box_id}'", 'category_filter');
        if ($mode == 'category') {
            $sql = "SELECT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Condition`, `T1`.`Values`, CONCAT('listing_fields+name+', `T1`.`Key`) AS `pName`, ";
            $sql .= "`T2`.`Items_display_limit`, `T2`.`Item_names`, `T2`.`Mode` ";
            $sql .= "FROM `" . RL_DBPREFIX . "listing_fields` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "category_filter_field` AS `T2` ON `T2`.`Field_ID` = `T1`.`ID` AND `T2`.`Box_ID` = '{$box_id}' ";
            $sql .= "WHERE `T1`.`Status` = 'active' AND `T2`.`Box_ID` = '{$box_id}'";
            $fields_tmp = $this->getAll($sql);
            foreach ($fields_tmp as $field_tmp) {
                if ($field_tmp['Condition']) {
                    $values = $this->fetch(array(
                        'Key'
                    ), array(
                        'Status' => 'active',
                        'Plugin' => ''
                    ), "AND `Key` LIKE '{$field_tmp['Condition']}_%'", null, 'data_formats');
                    if ($values) {
                        foreach ($values as $value) {
                            $de_fields[] = $value['Key'];
                        }
                        $field_tmp['Values'] = implode(',', $de_fields);
                        unset($values, $de_fields);
                    }
                }
                $fields[$field_tmp['Key']] = $field_tmp;
            }
            unset($fields_tmp);
            $this->box_fields = $fields;
            $this->updateFilter($fields, $box_id);
        }
    }
    function recount()
    {
        $this->setTable('category_filter');
        $boxes = $this->fetch(array(
            'ID'
        ), array(
            'Mode' => 'category'
        ));
        if ($boxes) {
            foreach ($boxes as $box) {
                $this->update($box['ID'], 'category');
                $this->compile($box['ID']);
            }
        }
    }
    function ajaxCfRecount()
    {
        global $_response, $lang;
        $this->recount();
        $_response->script("
			printMessage('notice', '{$lang['categoryFilter_box_recounted']}');
			$('#reorder_categoryFiler').val('{$lang['recount']}');
		");
        return $_response;
    }
    function request($fields, &$box_id, &$original, $direct = false)
    {
        global $aHooks, $config;
        if ($this->filters || $direct) {
            foreach ($this->filters as $filter_key => $filter_val) {
                preg_match('/^([0-9]+)?([\-\<\>]+)([0-9]+)?$/', $filter_val, $ranges);
                $ranges = $this->prepareValue($ranges, $this->fields_info[$filter_key], 'where');
                preg_match('/^([0-9]+\,.*)$/', $filter_val, $checkbox);
                if (($ranges[1] != '' || $ranges[3] != '') && $ranges[2]) {
                    if ($aHooks['currencyConverter'] && ($_SESSION['curConv_code'] || $_COOKIE['curConv_code']) && $filter_key == $config['currencyConverter_price_field']) {
                        $code           = $_SESSION['curConv_code'] ? $_SESSION['curConv_code'] : $_COOKIE['curConv_code'];
                        $requested_rate = $GLOBALS['rlCurrencyConverter']->rates[$code]['Rate'];
                        $orig_from      = $from = $ranges[1];
                        $from /= $requested_rate;
                        $where .= "AND SUBSTRING_INDEX(`T1`.`{$filter_key}`, '|', 1)/IF(`CURCONV`.`Rate` IS NULL, 1, `CURCONV`.`Rate`) >= {$from} ";
                        $orig_to = $to = $ranges[3];
                        $to /= $requested_rate;
                        $where .= "AND SUBSTRING_INDEX(`T1`.`{$filter_key}`, '|', 1)/IF(`CURCONV`.`Rate` IS NULL, 1, `CURCONV`.`Rate`) <= {$to} ";
                        if ($orig_from || $orig_to) {
                        }
                    } else {
                        switch ($ranges[2]) {
                            case '-':
                                $where .= "AND (ROUND(`T1`.`{$filter_key}`, 2) >= {$ranges[1]} AND ROUND(`T1`.`{$filter_key}`, 2) <= {$ranges[3]}) ";
                                break;
                            case '<':
                                $where .= "AND (ROUND(`T1`.`{$filter_key}`, 2) < {$ranges[3]} AND ROUND(`T1`.`{$filter_key}`, 2) > 0) ";
                                break;
                            case '>':
                                $where .= "AND (ROUND(`T1`.`{$filter_key}`, 2) > {$ranges[1]}) ";
                                break;
                        }
                    }
                } elseif ($checkbox[1]) {
                    $ids = explode(',', $checkbox[1]);
                    $where .= "AND (FIND_IN_SET('" . implode("', `T1`.`{$filter_key}`) > 0 AND FIND_IN_SET('", $ids) . "', `T1`.`{$filter_key}`) > 0 ) ";
                } else {
                    if (in_array($this->fields_info[$filter_key]['Type'], array(
                        'price',
                        'mixed'
                    ))) {
                        $where .= "AND `T1`.`{$filter_key}` LIKE '{$filter_val}%' ";
                    } else {
                        if ($filter_key == 'posted_by') {
                            $where .= "AND `T7`.`Type` = '{$filter_val}' ";
                        } else {
                            $where .= "AND `T1`.`{$filter_key}` = '{$filter_val}' ";
                        }
                    }
                }
                if (!$direct) {
                    unset($fields[$filter_key]);
                }
            }
            if ($direct) {
                $original = $this->updateFilter($fields, $box_id, $where, 'type');
                if ($direct && !$this->filters && $original) {
                    foreach ($original as $item) {
                        if ($item['Mode'] == 'slider') {
                            $min   = $max = false;
                            $items = $GLOBALS['rlJson']->decode($item['Items']);
                            foreach ($items as $item_val => $object) {
                                if (is_numeric(strpos($item_val, '-'))) {
                                    $new_item_val = explode('-', $item_val);
                                    $min          = !is_numeric($min) ? $new_item_val[0] : $min;
                                    $max          = !is_numeric($max) ? $new_item_val[1] : $max;
                                } else {
                                    $min = !is_numeric($min) ? $item_val : $min;
                                    $max = !is_numeric($max) ? $item_val : $max;
                                }
                                $min = $item_val < $min ? $item_val : $min;
                                $max = $item_val > $max ? $item_val : $max;
                            }
                            $_SESSION['cf_slider_data'][$box_id][$item['Key']] = array(
                                'min' => $min,
                                'max' => $max
                            );
                        }
                    }
                }
            } else {
                $data = $this->updateFilter($fields, $box_id, $where);
                foreach ($data as $field) {
                    $original[$field['Key']]['Items'] = $field['Items'];
                }
            }
        }
    }
    function compile($box_id = false)
    {
        global $rlActions;
        if (!$box_id)
            return;
        $box_info = $this->fetch(array(
            'Mode',
            'Type',
            'Category_IDs'
        ), array(
            'ID' => $box_id
        ), null, 1, 'category_filter', 'row');
        if ($box_info['Mode'] == 'category' && $box_info['Category_IDs']) {
            $sql = "SELECT `T1`.`Items`, `T1`.`Item_names`, `T1`.`Items_display_limit`, `T1`.`Mode`, `T2`.`Type`, `T2`.`Key`, `T2`.`Condition`, ";
            $sql .= "`T2`.`Values`, CONCAT('listing_fields+name+', `T2`.`Key`) AS `pName` ";
            $sql .= "FROM `" . RL_DBPREFIX . "category_filter_field` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_fields` AS `T2` ON `T1`.`Field_ID` = `T2`.`ID` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "category_filter_relation` AS `T3` ON `T1`.`Box_ID` = `T3`.`Category_ID` AND `T1`.`Field_ID` = `T3`.`Fields` ";
            $sql .= "WHERE `Box_ID` = '{$box_id}' AND `T1`.`Status` = 'active' ";
            $sql .= "ORDER BY `T3`.`Position`";
            $filters_tmp = $this->getAll($sql);
            foreach ($filters_tmp as $filter) {
                $filter['Items']         = str_replace("'", "&#39;", $filter['Items']);
                $filters[$filter['Key']] = $filter;
            }
            unset($filters_tmp);
        }
        $sql = "SELECT `T1`.`Item_names`, `T2`.`Key` ";
        $sql .= "FROM `" . RL_DBPREFIX . "category_filter_field` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_fields` AS `T2` ON `T1`.`Field_ID` = `T2`.`ID` ";
        $sql .= "WHERE `Box_ID` = '{$box_id}' AND `T1`.`Status` = 'active' ";
        $item_names_tmp = $this->getAll($sql);
        foreach ($item_names_tmp as $item_name) {
            $item_names[$item_name['Key']] = unserialize($item_name['Item_names']);
        }
        unset($item_names_tmp);
        if (!$this->box_fields) {
            $sql = "SELECT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Condition`, `T1`.`Values`, CONCAT('listing_fields+name+', `T1`.`Key`) AS `pName`, ";
            $sql .= "`T2`.`Items_display_limit`, `T2`.`Mode`, `T2`.`Item_names` ";
            $sql .= "FROM `" . RL_DBPREFIX . "listing_fields` AS `T1` ";
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "category_filter_field` AS `T2` ON `T2`.`Field_ID` = `T1`.`ID` AND `T2`.`Box_ID` = '{$box_id}' ";
            $sql .= "WHERE `T1`.`Status` = 'active' AND `T2`.`Box_ID` = '{$box_id}'";
            $fields_tmp = $this->getAll($sql);
            foreach ($fields_tmp as $field_tmp) {
                $fields[$field_tmp['Key']] = $field_tmp;
            }
            unset($fields_tmp);
            $this->box_fields = $fields;
        }
        $new_content = str_replace(array(
            '{items}',
            '{item_names}',
            '{fields}',
            '{box_id}',
            '{box_mode}',
            '{box_type}',
            '{box_category}'
        ), array(
            $filters ? mysql_real_escape_string($GLOBALS['rlJson']->encode($filters)) : false,
            $item_names ? serialize($item_names) : false,
            serialize($this->box_fields),
            $box_id,
            $box_info['Mode'],
            $box_info['Type'],
            $box_info['Category_IDs']
        ), $this->box_content);
        $new_content = str_replace("'", "''", $new_content);
        $this->query("UPDATE `" . RL_DBPREFIX . "blocks` SET `Content` = '{$new_content}' WHERE `Key` = 'categoryFilter_{$box_id}' LIMIT 1");
    }
    function ajaxDeleteBox($id = false)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        if (!$id)
            return $_response;
        $id  = (int) $id;
        $sql = "DELETE FROM `" . RL_DBPREFIX . "category_filter` WHERE `ID` = '{$id}' LIMIT 1";
        $this->query($sql);
        $sql = "DELETE FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = 'categoryFilter_{$id}' LIMIT 1";
        $this->query($sql);
        $sql = "DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'blocks+name+categoryFilter_{$id}'";
        $this->query($sql);
        $_response->script("
			categoryFilterGrid.reload();
			printMessage('notice', '{$lang['categoryFilter_filter_box_deleted']}');
		");
        return $_response;
    }
    function ctJsonDecodeModifier($string)
    {
        return $GLOBALS['rlJson']->decode($string);
    }
    function parseUrl()
    {
        global $rlSmarty, $config;
        if ($config['mod_rewrite']) {
            if ($_GET['nvar_1']) {
                if (false != strpos($_GET['rlVareables'], '/')) {
                    $vars = explode('/', $_GET['rlVareables']);
                    foreach ($vars as $var) {
                        if (!is_numeric(strpos($var, ':'))) {
                            $allow[] = $var;
                        }
                    }
                    if ($allow) {
                        $_GET['rlVareables'] = implode('/', $allow);
                    }
                }
                foreach ($_GET as $key => $nvar) {
                    if (is_numeric(strpos($nvar, ':'))) {
                        $item                                    = explode(':', $nvar);
                        $filter[str_replace('-', '_', $item[0])] = urldecode($item[1]);
                        unset($_GET[$key]);
                    }
                }
            }
        } else {
            foreach ($_GET as $key => $nvar) {
                if (0 === strpos($key, 'cf-')) {
                    $item                                    = explode('cf-', $key);
                    $filter[str_replace('-', '_', $item[1])] = urldecode($nvar);
                    unset($_GET[$key]);
                }
            }
        }
        $this->filters = $filter;
    }
    function ajaxRemoveRow($item = false)
    {
        global $_response, $box_id, $field_id, $rlActions;
        if (!$box_id || !$field_id || !$item)
            return $_response;
        $box_id   = (int) $box_id;
        $field_id = (int) $field_id;
        $field    = $this->fetch(array(
            'ID',
            'Item_names'
        ), array(
            'Box_ID' => $box_id,
            'Field_ID' => $field_id
        ), null, 1, 'category_filter_field', 'row');
        $field    = unserialize($field['Item_names']);
        unset($field[$item]);
        $update = array(
            'fields' => array(
                'Item_names' => serialize($field)
            ),
            'where' => array(
                'Box_ID' => $box_id,
                'Field_ID' => $field_id
            )
        );
        $rlActions->updateOne($update, 'category_filter_field');
        $this->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'category_filter+name+{$box_id}_{$field_id}_{$item}' AND `plugin` = 'categoryFilter'");
        $this->update($box_id);
        $_response->script("$('input[name=\"sign[{$item}]\"]').parent().parent().remove();");
        return $_response;
    }
    function where($sql, $param2)
    {
        global $sql, $aHooks;
        if ($param2 == 'categoryFilter')
            return;
        if (!$this->filters)
            return;
        foreach ($this->filters as $filter_key => $filter_val) {
            $filter_keys[] = $filter_key;
        }
        $box_id = $GLOBALS['categoryFilter_activeBoxID'];
        foreach ($this->filters as $filter_key => $filter_val) {
            if (!isset($this->fields_info[$filter_key])) {
                unset($this->filters[$filter_key]);
                continue;
            }
            preg_match('/^([0-9]+)?([\-\<\>]+)([0-9]+)?$/', $filter_val, $ranges);
            $ranges = $this->prepareValue($ranges, $this->fields_info[$filter_key], 'where');
            if (($ranges[1] != '' || $ranges[3] != '') && $ranges[2]) {
                if ($aHooks['currencyConverter'] && $this->fields_info[$filter_key]['Type'] == 'price' && ($_SESSION['curConv_code'] || $_COOKIE['curConv_code'])) {
                    $code           = $_SESSION['curConv_code'] ? $_SESSION['curConv_code'] : $_COOKIE['curConv_code'];
                    $requested_rate = $GLOBALS['rlCurrencyConverter']->rates[$code]['Rate'];
                    $orig_from      = $from = $ranges[1];
                    $from /= $requested_rate;
                    $sql .= "AND SUBSTRING_INDEX(`T1`.`{$filter_key}`, '|', 1)/IF(`CURCONV`.`Rate` IS NULL, 1, `CURCONV`.`Rate`) >= {$from} ";
                    $orig_to = $to = $ranges[3];
                    $to /= $requested_rate;
                    $sql .= "AND SUBSTRING_INDEX(`T1`.`{$filter_key}`, '|', 1)/IF(`CURCONV`.`Rate` IS NULL, 1, `CURCONV`.`Rate`) <= {$to} ";
                    if ($orig_from || $orig_to) {
                    }
                } else {
                    switch ($ranges[2]) {
                        case '-':
                            $sql .= "AND (ROUND(`T1`.`{$filter_key}`, 2) >= {$ranges[1]} AND ROUND(`T1`.`{$filter_key}`, 2) <= {$ranges[3]}) ";
                            break;
                        case '<':
                            $sql .= "AND (ROUND(`T1`.`{$filter_key}`, 2) < {$ranges[3]} AND ROUND(`T1`.`{$filter_key}`, 2) > 0) ";
                            break;
                        case '>':
                            $sql .= "AND (ROUND(`T1`.`{$filter_key}`, 2) > {$ranges[1]}) ";
                            break;
                    }
                }
            } elseif ($this->fields_info[$filter_key]['Type'] == 'checkbox') {
                $ids = explode(',', $filter_val);
                $sql .= "AND (FIND_IN_SET('" . implode("', `T1`.`{$filter_key}`) > 0 AND FIND_IN_SET('", $ids) . "', `T1`.`{$filter_key}`) > 0 ) ";
            } else {
                if (in_array($this->fields_info[$filter_key]['Type'], array(
                    'price',
                    'mixed'
                ))) {
                    $sql .= "AND `T1`.`{$filter_key}` LIKE '{$filter_val}%' ";
                } else {
                    if ($filter_key == 'posted_by') {
                        $sql .= "AND `T7`.`Type` = '{$filter_val}' ";
                    } else {
                        $sql .= "AND `T1`.`{$filter_key}` = '{$filter_val}' ";
                    }
                }
            }
        }
    }
    function join()
    {
        global $sql, $aHooks, $config;
        if (!$this->filters)
            return;
        $field = $config['currencyConverter_price_field'];
        if ($aHooks['currencyConverter'] && ($_SESSION['curConv_code'] || $_COOKIE['curConv_code'])) {
            $sql .= "LEFT JOIN `" . RL_DBPREFIX . "currency_rate` AS `CURCONV` ON SUBSTRING_INDEX(`T1`.`{$field}`, '|', -1) = `CURCONV`.`Key` AND `CURCONV`.`Status` = 'active' ";
        }
    }
    function title()
    {
        global $bread_crumbs, $page_info, $rlSmarty, $lang;
        if (!$this->filters)
            return;
        $add = ' / ';
        foreach ($this->filters as $filter_key => $filter_val) {
            $item_names = $this->fields_info[$filter_key]['Item_names'];
            switch ($this->fields_info[$filter_key]['Type']) {
                case 'bool':
                    if ($item_names) {
                        $bool = $lang[$item_names[$filter_val]];
                    } else {
                        $bool = $filter_val ? $lang['yes'] : $lang['no'];
                    }
                    $add .= $lang[$this->fields_info[$filter_key]['pName']] . ': ' . $bool . ', ';
                    break;
                case 'radio':
                case 'select':
                    if ($this->fields_info[$filter_key]['Condition'] == 'years') {
                        $add .= $lang[$this->fields_info[$filter_key]['pName']] . ': ' . $filter_val . ', ';
                    } else {
                        if ($item_names) {
                            $phrase = $item_names[$filter_val];
                        } else {
                            if ($this->fields_info[$filter_key]['Condition']) {
                                $phrase = 'data_formats+name+' . $this->fields_info[$filter_key]['Condition'] . '_' . $filter_val;
                                if (!$lang[$phrase]) {
                                    $phrase = 'data_formats+name+' . $filter_val;
                                }
                            } else {
                                if ($filter_key == 'posted_by') {
                                    $phrase = 'account_types+name+' . $filter_val;
                                } else {
                                    $phrase = 'listing_fields+name+' . $filter_key . '_' . $filter_val;
                                }
                            }
                        }
                        $add .= $lang[$this->fields_info[$filter_key]['pName']] . ': ' . $lang[$phrase] . ', ';
                    }
                    break;
                case 'checkbox':
                    $exp_values = explode(',', $filter_val);
                    foreach ($exp_values as $exp_value) {
                        if ($item_names) {
                            $phrase = $item_names[$exp_value];
                        } else {
                            if ($this->fields_info[$filter_key]['Condition']) {
                                $phrase = 'data_formats+name+' . $this->fields_info[$filter_key]['Condition'] . '_' . $exp_value;
                                if (!$lang[$phrase]) {
                                    $phrase = 'data_formats+name+' . $exp_value;
                                }
                            } else {
                                $phrase = 'listing_fields+name+' . $filter_key . '_' . $exp_value;
                            }
                        }
                        $out .= $lang[$phrase] . ', ';
                    }
                    $add .= $lang[$this->fields_info[$filter_key]['pName']] . ': ' . rtrim($out, ', ') . ', ';
                    break;
                default:
                    if ($item_names) {
                        $phrase = $lang[$item_names[$filter_val]];
                    } else {
                        $phrase = $filter_val;
                    }
                    $add .= $lang[$this->fields_info[$filter_key]['pName']] . ': ' . $phrase . ', ';
                    break;
            }
            unset($phrase);
        }
        $add                      = rtrim($add, ', ') . ' - ' . $lang['categoryFilter_filtered'];
        $bread_crumbs             = array_reverse($bread_crumbs);
        $bread_crumbs[0]['title'] = $bread_crumbs[0]['title'] . $add;
        $bread_crumbs             = array_reverse($bread_crumbs);
    }
    function paging()
    {
        global $rlSmarty;
        if (!$filters = $rlSmarty->get_template_vars('cf_filter'))
            return;
        $category = $rlSmarty->get_template_vars('category');
        foreach ($filters as $filter_key => $filter_val) {
            $add .= "{$filter_key}:{$filter_val}/";
        }
        $category['Path_back'] = $category['Path'];
        $category['Path'] .= '/' . rtrim($add, '/');
        $rlSmarty->assign('category', $category);
    }
    function blocks()
    {
        global $rlSmarty, $blocks, $rlCommon, $category;
        foreach ($blocks as $block_key => $block) {
            if (0 === strpos($block_key, 'categoryFilter_')) {
                $id = explode('_', $block_key);
                $id = $id[1];
                if (!$category_mode) {
                    preg_match('/\$box\_mode = \'([^\']+)\';/', $block['Content'], $matches);
                    if ($matches[1] == 'type') {
                        $GLOBALS['categoryFilter_activeBoxID'] = $id;
                        $active_key                            = $block_key;
                    }
                }
                preg_match('/\$box\_category = \'([^\']+)\';/', $block['Content'], $matches);
                if ($matches[1]) {
                    $GLOBALS['categoryFilter_activeBoxID'] = $id;
                    $category_ids                          = explode(',', $matches[1]);
                    if (is_numeric(array_search($category['ID'], $category_ids))) {
                        $active_key    = $block_key;
                        $category_mode = true;
                    }
                }
            }
        }
        if (!$active_key)
            return;
        foreach ($blocks as $block_key => $block) {
            if (0 === strpos($block_key, 'categoryFilter_') && $block_key != $active_key) {
                unset($blocks[$block_key]);
            } else {
                preg_match('/\$fields \= \<\<\< FL\n(.*)\nFL\;/', $block['Content'], $matches);
                if ($matches[1]) {
                    $fields = unserialize(trim($matches[1]));
                    foreach ($fields as $field_key => $filed) {
                        $fields[$field_key]['Item_names'] = unserialize($fields[$field_key]['Item_names']);
                    }
                    $this->fields_info = $fields;
                    unset($fields);
                }
            }
        }
        $rlCommon->defineBlocksExist($blocks);
    }
    function removeBlocks($is_listings = false)
    {
        global $rlSmarty, $blocks, $rlCommon, $category, $page_info, $listings;
        if ($page_info['Controller'] != 'listing_type')
            return;
        $do = 0;
        foreach ($blocks as $block_key => $block) {
            if ((!$is_listings && 0 === strpos($block_key, 'categoryFilter_')) || (!$listings && $is_listings && 0 === strpos($block_key, 'categoryFilter_'))) {
                unset($blocks[$block_key]);
                $do++;
            }
        }
        if ($do) {
            $rlCommon->defineBlocksExist($blocks);
        }
    }
    function flArraySum(&$array1, &$array2)
    {
        if (!$array1 || !$array2)
            return $array1;
        foreach ($array1 as $key => $value) {
            $out[$key] = $value + $array2[$key];
        }
        return $out;
    }
    function prepareValue($value, &$field, $mode = 'get')
    {
        switch ($mode) {
            case 'get':
                if ($field['Type'] == 'select' && $field['Key'] == 'age') {
                    $value = date('Y') - $value;
                }
                break;
            case 'where':
                if ($field['Type'] == 'select' && $field['Key'] == 'age') {
                    if ($value[1] != '') {
                        $tmp      = $value[1];
                        $value[1] = date('Y') - $value[3];
                    }
                    if ($value[3] != '') {
                        $value[3] = date('Y') - $tmp;
                    }
                }
                break;
        }
        return $value;
    }
}