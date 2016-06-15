<?php
class rlFieldBoundBoxes extends reefless
{
    var $calc;
    function getListings($data = false, $listing_type = false, $order = false, $order_type = 'ASC', $start = 0, $limit = 10)
    {
        global $sorting, $sql, $custom_order, $config;
        $field_info = $this->fetch(array(
            "Type",
            "Condition"
        ), array(
            "Key" => $data['field']
        ), null, null, 'listing_fields', 'row');
        if (!$data)
            return false;
        $start = $start > 1 ? ($start - 1) * $limit : 0;
        $hook  = '';
        $sql   = "SELECT SQL_CALC_FOUND_ROWS DISTINCT {hook} SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT `T6`.`Thumbnail` ORDER BY `T6`.`Type` DESC, `T6`.`ID` ASC), ',', 1) AS `Main_photo`, ";
        $sql .= "DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY) AS `Plan_expire`, ";
        $sql .= "`T1`.*, `T1`.`Shows`, `T3`.`Path` AS `Path`, `T3`.`Key` AS `Key`, `T3`.`Type` AS `Listing_type`, ";
        $sql .= $config['grid_photos_count'] ? "COUNT(`T6`.`Thumbnail`) AS `Photos_count`, " : "";
        $GLOBALS['rlHook']->load('listingsModifyField');
        $sql .= "IF(UNIX_TIMESTAMP(DATE_ADD(`T1`.`Featured_date`, INTERVAL `T4`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T4`.`Listing_period` = 0, '1', '0') `Featured` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T4` ON `T1`.`Featured_ID` = `T4`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_photos` AS `T6` ON `T1`.`ID` = `T6`.`Listing_ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
        $GLOBALS['rlHook']->load('listingsModifyJoin');
        $sql .= "WHERE ( UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0 ) ";
        if ($data['field'] == 'posted_by') {
            $sql .= "AND (`T7`.`Type` = '" . $data['value'] . "' ";
        } else {
            $sql .= "AND (`T1`.`" . $data['field'] . "` = '" . $data['value'] . "' ";
        }
        if ($field_info['Condition']) {
            $sql .= "OR `T1`.`" . $data['field'] . "` = '" . $field_info['Condition'] . "_" . $data['value'] . "'";
        }
        $sql .= ") ";
        if ($listing_type) {
            $sql .= "AND `T3`.`Type` = '{$listing_type}' ";
        }
        $sql .= "AND `T1`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
        $GLOBALS['rlHook']->load('listingsModifyWhere');
        $GLOBALS['rlHook']->load('listingsModifyGroup');
        if (false === strpos($sql, 'GROUP BY')) {
            $sql .= " GROUP BY `T1`.`ID` ";
        }
        $sql .= "ORDER BY `Featured` DESC ";
        if ($order) {
            if ($order == 'Plan_expire') {
                $sql .= ", `{$order}` " . strtoupper($order_type) . " ";
            } elseif ($order == 'category') {
                $sql .= ", `T4`.`Path` " . strtoupper($order_type) . " ";
            } else {
                switch ($sorting[$order]['Type']) {
                    case 'price':
                    case 'unit':
                    case 'mixed':
                        $sql .= ", ROUND(`T1`.`{$order}`) " . strtoupper($order_type) . " ";
                        break;
                    case 'select':
                        if ($sorting[$order]['Key'] == 'Category_ID') {
                            $sql .= ", `T3`.`Key` " . strtoupper($order_type) . " ";
                        } else {
                            $sql .= ", `T1`.`{$order}` " . strtoupper($order_type) . " ";
                        }
                        break;
                    default:
                        $sql .= ", `T1`.`{$order}` " . strtoupper($order_type) . " ";
                        break;
                }
            }
        }
        $sql .= ", `ID` DESC ";
        $sql .= "LIMIT {$start}, {$limit} ";
        $sql      = str_replace('{hook}', $hook, $sql);
        $listings = $this->getAll($sql);
        $listings = $GLOBALS['rlLang']->replaceLangKeys($listings, 'categories', 'name');
        if (empty($listings)) {
            return false;
        }
        $calc       = $this->getRow("SELECT FOUND_ROWS() AS `calc`");
        $this->calc = $calc['calc'];
        $this->loadClass('Listings');
        foreach ($listings as $key => $value) {
            $fields = $GLOBALS['rlListings']->getFormFields($value['Category_ID'], 'short_forms', $value['Listing_type']);
            if ($prev_cat_id != $value['Category_ID']) {
                $prev_cat_id = $value['Category_ID'];
                $fields      = $GLOBALS['rlListings']->getFormFields($value['Category_ID'], 'short_forms', $value['Listing_type']);
            }
            foreach ($fields as $fKey => $fValue) {
                if ($first) {
                    $fields[$fKey]['value'] = $GLOBALS['rlCommon']->adaptValue($fValue, $value[$fKey], 'listing', $value['ID']);
                } else {
                    if ($field['Condition'] == 'isUrl' || $field['Condition'] == 'isEmail') {
                        $fields[$fKey]['value'] = $listings[$key][$item];
                    } else {
                        $fields[$fKey]['value'] = $GLOBALS['rlCommon']->adaptValue($fValue, $value[$fKey], 'listing', $value['ID']);
                    }
                }
                $first++;
            }
            $listings[$key]['fields']        = $fields;
            $listings[$key]['listing_title'] = $GLOBALS['rlListings']->getListingTitle($value['Category_ID'], $value, $value['Listing_type']);
        }
        return $listings;
    }
    function ajaxDeleteBox($key = false)
    {
        global $_response, $lang, $config;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        if (!$key) {
            return false;
        }
        $box_id      = $this->getOne("ID", "`Key` = '" . $key . "'", "field_bound_boxes");
        $lang_keys[] = array(
            'Key' => 'blocks+name+' . $key
        );
        $GLOBALS['rlActions']->delete(array(
            'Key' => $key
        ), array(
            'field_bound_boxes'
        ), NULL, 1, $key);
        $GLOBALS['rlActions']->delete(array(
            'Key' => $key
        ), array(
            'blocks',
            'lang_keys'
        ), NULL, 1, $key, $lang_keys);
        $this->query("DELETE FROM `" . RL_DBPREFIX . "field_bound_items` WHERE `Box_ID` = '" . $box_id . "'");
        $GLOBALS['reefless']->deleteDirectory(RL_FILES . "fieldBoundBoxes" . RL_DS . $key);
        $del_mode = 'deleted';
        $_response->script("
			fieldBoundBoxesGrid.reload();
			printMessage('notice', '{$lang['block_' . $del_mode]}')
		");
        return $_response;
    }
    function ajaxDeleteFieldBoundItem($data)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $data   = explode(',', $data);
        $key    = $data[0];
        $box    = $data[1];
        $box_id = $this->getOne("ID", "`Key` = '{$box}'", "field_bound_boxes");
        $icon   = $this->getOne("Icon", "`Key` = '{$key}'", "field_bound_items");
        if ($icon) {
            unlink($icon);
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "field_bound_items` WHERE `Key` = '{$key}' AND `Box_ID` = '" . $box_id . "' LIMIT 1");
        $_response->script("printMessage('notice', '{$lang['item_deleted']}')");
        $_response->script("$('#loading').fadeOut('normal');");
        $_response->script("itemsGrid.reload()");
        $_response->script("$('#edit_item').slideUp('normal');");
        $_response->script("$('#new_item').slideUp('normal');");
        $this->updateBoxContent($box);
        return $_response;
    }
    function ajaxDeleteIcon($item_id)
    {
        global $_response;
        $item_info = $this->fetch(array(
            'Box_ID',
            'Icon'
        ), array(
            'ID' => $item_id
        ), null, null, 'field_bound_items', 'row');
        if (!$item_info) {
            return $_response;
        }
        if ($item_info['Icon']) {
            unlink(RL_FILES . $item_info['Icon']);
        }
        $sql = "UPDATE `" . RL_DBPREFIX . "field_bound_items` SET `Icon`= '' WHERE `ID` = " . $item_id;
        $GLOBALS['rlDb']->query($sql);
        $this->updateBoxContent(null, $item_info['Box_ID']);
        $_response->script("$('#gallery').slideUp('normal');");
        $_response->script("$('#fileupload').html(null);");
        $_response->script("$('printMessage('notice', '{$lang['fb_icon_deleted']}');");
        return $_response;
    }
    function ajaxRecount($self)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false && !$direct) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $this->recount();
        $this->updateBoxContent();
        $_response->script("printMessage('notice', '{$lang['fb_listings_recounted']}')");
        $_response->script("$('{$self}').val('{$lang['recount']}');");
        return $_response;
    }
    function generateBoxContent($box_id)
    {
        global $rlDb;
        $box_info        = $rlDb->fetch(array(
            'Field_key',
            'Columns',
            'Icons_position',
            'Path',
            'Icons',
            'Show_count',
            'Postfix',
            'Show_empty'
        ), array(
            "ID" => $box_id
        ), null, null, "field_bound_boxes", 'row');
        $items           = $rlDb->fetch('*', array(
            "Box_ID" => $box_id,
            'Status' => 'active'
        ), "ORDER BY `Position`", null, "field_bound_items");
        $field_condition = $rlDb->getOne("Condition", "`Key` = '" . $box_info['Field_key'] . "'", "listing_fields");
        $replacement     = $field_condition ? $field_condition . "_" : $box_info['Field_key'] . "_";
        $content         = 'global $rlSmarty;';
        if ($items) {
            $content .= '$options = array(';
            foreach ($items as $key => $item) {
                if ($item['Count'] || $box_info['Show_empty']) {
                    $item_key = str_replace($replacement, '', $item['Key']);
                    $content .= (int) $key . '=> array( ';
                    $content .= '"pName" => "' . $item['pName'] . '",';
                    if ($box_info['Show_count']) {
                        $content .= '"Count" => "' . $item['Count'] . '",';
                    }
                    if ($box_info['Icons']) {
                        $content .= '"Icon" => "' . $item['Icon'] . '",';
                    }
                    $content .= '"Key" => "' . $item_key . '"';
                    $content .= '),';
                }
            }
            $content = substr($content, 0, -1);
            $content .= ');';
        }
        $content .= '$rlSmarty -> assign("options", $options);';
        $content .= '$rlSmarty -> assign("field_key","' . $box_info['Field_key'] . '");';
        $content .= '$rlSmarty -> assign("path","' . $box_info['Path'] . '");';
        $content .= '$rlSmarty -> assign("columns_number",' . $box_info['Columns'] . ');';
        $content .= '$rlSmarty -> assign("icons_position","' . $box_info['Icons_position'] . '");';
        $content .= '$rlSmarty -> assign("html_postfix","' . $box_info['Postfix'] . '");';
        $content .= '$rlSmarty -> assign("show_count","' . $box_info['Show_count'] . '");';
        $content .= '$rlSmarty -> display(RL_PLUGINS."fieldBoundBoxes".RL_DS."field-bound_box.tpl");';
        return $content;
    }
    function updateBoxContent($box_key = false, $box_id = false, $boxes = false)
    {
        if (!$box_key && $box_id) {
            $box_key = $this->getOne("Key", "`ID` = '{$box_id}'", "field_bound_boxes");
        } elseif ($box_key && !$box_id) {
            $box_id = $this->getOne("ID", "`Key` = '{$box_key}'", "field_bound_boxes");
        } elseif (!$boxes) {
            $boxes = $this->fetch(array(
                'ID',
                'Key'
            ), array(
                'Status' => 'active'
            ), null, null, 'field_bound_boxes');
        }
        $GLOBALS['reefless']->loadClass('Actions');
        $GLOBALS['rlActions']->rlAllowHTML = true;
        if (is_array($boxes)) {
            foreach ($boxes as $key => $box) {
                $update[$key]['fields']['Content'] = $this->generateBoxContent($box['ID']);
                $update[$key]['where']['Key']      = $box['Key'];
            }
            return $GLOBALS['rlActions']->update($update, 'blocks');
        } else {
            $block_content = $this->generateBoxContent($box_id);
            $update_data   = array(
                'fields' => array(
                    'Content' => $block_content
                ),
                'where' => array(
                    'Key' => $box_key
                )
            );
            return $GLOBALS['rlActions']->updateOne($update_data, 'blocks');
        }
    }
    function recount()
    {
        global $rlDb;
        $sql = "SELECT `T1`.`ID`, `T1`.`Show_count`, `T1`.`Listing_type`, `T2`.`Condition`, `T1`.`Field_key` FROM `" . RL_DBPREFIX . "field_bound_boxes` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_fields` AS `T2` ON `T2`.`Key` = `T1`.`Field_key` ";
        $sql .= "WHERE `T1`.`Status` = 'active' ";
        $boxes = $GLOBALS['rlDb']->getAll($sql);
        foreach ($boxes as $key => $box) {
            if (!$box['Show_count']) {
                continue;
            }
            if (!$box['Condition']) {
                $sql = "SELECT REPLACE( `Key`, '{$box['Field_key']}_', '') AS `Values` ";
            } else {
                $sql = "SELECT `Key` AS `Values` ";
            }
            $sql .= "FROM `" . RL_DBPREFIX . "field_bound_items` WHERE `Status` = 'active' AND `Box_ID` = " . $box['ID'];
            $items_tmp = $rlDb->getAll($sql);
            foreach ($items_tmp as $kk => $tmp_item) {
                $items['Values'] .= $tmp_item['Values'] . ",";
            }
            if ($box['Field_key'] == 'posted_by') {
                $sql = "SELECT COUNT(*) as `Count`, `T7`.`Type` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
            } else {
                $sql = "SELECT COUNT(*) as `Count`, `T1`.`" . $box['Field_key'] . "` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
            }
            $sql .= "JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
            $sql .= "JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
            $sql .= "JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
            $GLOBALS['rlHook']->load('listingsModifyJoin');
            $sql .= "WHERE ( UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0 ) ";
            if ($box['Field_key'] == 'posted_by') {
                $sql .= "AND FIND_IN_SET(`T7`.`Type`, '" . $items['Values'] . "') ";
            } else {
                $sql .= "AND FIND_IN_SET(`T1`.`" . $box['Field_key'] . "`, '" . $items['Values'] . "') ";
            }
            $sql .= "AND `T1`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
            if ($box['Listing_type']) {
                $sql .= "AND `T3`.`Type` = '{$box['Listing_type']}' ";
            }
            $GLOBALS['rlHook']->load('listingsModifyWhere');
            if ($box['Field_key'] == 'posted_by') {
                $sql .= "GROUP BY `T7`.`Type`";
            } else {
                $sql .= "GROUP BY `T1`.`" . $box['Field_key'] . "`";
            }
            $counts = $rlDb->getAll($sql);
            $rlDb->query("UPDATE `" . RL_DBPREFIX . "field_bound_items` SET `Count` = 0 WHERE `Box_ID` =" . $box['ID']);
            foreach ($counts as $row) {
                $sql = "UPDATE `" . RL_DBPREFIX . "field_bound_items` SET `Count` = " . $row['Count'] . " ";
                if (!$box['Condition']) {
                    if ($box['Field_key'] == 'posted_by') {
                        $sql .= "WHERE `Key` = '" . $row['Type'] . "' ";
                    } else {
                        $sql .= "WHERE `Key` = '" . $box['Field_key'] . "_" . $row[$box['Field_key']] . "' ";
                    }
                } else {
                    $sql .= "WHERE `Key` = '" . $row[$box['Field_key']] . "' ";
                }
                $sql .= "AND `Box_ID` = '" . $box['ID'] . "'";
                $rlDb->query($sql);
            }
        }
    }
    function decreaseRelatedItems($listing_id = false)
    {
        return $this->affectRelatedItems($listing_id, 'decrease');
    }
    function increaseRelatedItems($listing_id = false)
    {
        return $this->affectRelatedItems($listing_id, 'increase');
    }
    function affectRelatedItems($listing_id = false, $mode = false)
    {
        if (!$listing_id || !$mode)
            return false;
        $cat_id       = $this->getOne("Category_ID", "`ID` = '" . $listing_id . "'", 'listings');
        $listing_type = $this->getOne("Type", "`ID` = '" . $cat_id . "'", 'categories');
        $fields       = $this->getBoxesListingFields($cat_id);
        if (!$fields)
            return false;
        $sql    = "SELECT `Key`, `Condition` FROM `" . RL_DBPREFIX . "listing_fields` WHERE FIND_IN_SET(`ID`, '" . implode(',', $fields) . "') ";
        $fields = $this->getAll($sql);
        foreach ($fields as $field) {
            $fields_tmp[] = $field['Key'];
        }
        $sql          = "SELECT `" . implode("`, `", $fields_tmp) . "` FROM `" . RL_DBPREFIX . "listings` WHERE `ID` = " . $listing_id;
        $listing_info = $this->getRow($sql);
        foreach ($fields as $fk => $field) {
            if (!$listing_info[$field['Key']]) {
                continue;
            }
            $sql = "UPDATE `" . RL_DBPREFIX . "field_bound_items` AS `T1` ";
            $sql .= "JOIN `" . RL_DBPREFIX . "field_bound_boxes` AS `T2` ON `T2`.`ID` = `T1`.`Box_ID` ";
            if ($mode == 'increase') {
                $sql .= "SET `T1`.`Count` = `T1`.`Count` + 1 ";
            } else {
                $sql .= "SET `T1`.`Count` = `T1`.`Count` - 1 ";
            }
            if (!$field['Condition']) {
                $sql .= "WHERE `T1`.`Key` ='" . $field['Key'] . "_" . $listing_info[$field['Key']] . "' ";
            } else {
                $sql .= "WHERE `T1`.`Key` ='" . $listing_info[$field['Key']] . "' ";
            }
            $sql .= "AND (`T2`.`Listing_type` = '" . $listing_type . "' OR `T2`.`Listing_type` ='')";
            $this->query($sql);
        }
        $sql   = "SELECT `ID`, `Key` FROM `" . RL_DBPREFIX . "field_bound_boxes` WHERE FIND_IN_SET(`Field_key`, '" . implode(",", $fields_tmp) . "')";
        $boxes = $this->getAll($sql);
        return $this->updateBoxContent(null, null, $boxes);
    }
    function editListing($listing_id, $listing_type, $diff)
    {
        $fields = '';
        foreach ($diff as $field => $vals) {
            if ($vals['new']) {
                $sql = "UPDATE `" . RL_DBPREFIX . "field_bound_items` AS `T1` ";
                $sql .= "JOIN `" . RL_DBPREFIX . "field_bound_boxes` AS `T2` ON `T2`.`ID` = `T1`.`Box_ID` ";
                $sql .= "SET `T1`.`Count` = `T1`.`Count` + 1 ";
                $sql .= "WHERE `T1`.`Key` ='" . $vals['new'] . "' AND `T2`.`Listing_type` = '" . $listing_type . "' AND `T2`.`Field_key` = '" . $field . "' ";
                $this->query($sql);
            }
            if ($vals['old']) {
                $sql = "UPDATE `" . RL_DBPREFIX . "field_bound_items` AS `T1` ";
                $sql .= "JOIN `" . RL_DBPREFIX . "field_bound_boxes` AS `T2` ON `T2`.`ID` = `T1`.`Box_ID` ";
                $sql .= "SET `T1`.`Count` = `T1`.`Count` - 1 ";
                $sql .= "WHERE `T1`.`Key` ='" . $vals['old'] . "' AND `T2`.`Listing_type` = '" . $listing_type . "' AND `T2`.`Field_key` = '" . $field . "' ";
                $this->query($sql);
            }
            $fields .= $field . ",";
        }
        $fields = substr($fields, 0, -1);
        $sql    = "SELECT `ID`, `Key` FROM `" . RL_DBPREFIX . "field_bound_boxes` WHERE FIND_IN_SET(`Field_key`, '{$fields}')";
        $boxes  = $this->getAll($sql);
        return $this->updateBoxContent(null, null, $boxes);
    }
    function getBoxesListingFields($cat_id = false)
    {
        if (!$cat_id)
            return false;
        $groups = $this->fetch(array(
            'Group_ID',
            'Fields'
        ), array(
            'Category_ID' => $cat_id
        ), null, null, 'listing_relations');
        if (!$groups) {
            if ($parent_id = $this->getOne('Parent_ID', "`ID` = '{$cat_id}'", 'categories')) {
                return $this->getBoxesListingFields($parent_id);
            } else {
                if ($general_cat_id = $GLOBALS['rlListingTypes']->types[$this->getOne('Type', "`ID` = '{$cat_id}'", 'categories')]['Cat_general_cat']) {
                    if ($general_cat_id == $cat_id) {
                        return false;
                    } else {
                        return $this->getBoxesListingFields($general_cat_id);
                    }
                } else {
                    return false;
                }
            }
        } else {
            foreach ($groups as $group) {
                if ($group['Group_ID']) {
                    $tmp_fields = explode(',', $group['Fields']);
                    foreach ($tmp_fields as $field) {
                        if ($field && $this->getOne("Field_key", "`Field_key` = '" . $this->getOne('Key', "`ID` = " . $field, 'listing_fields') . "'", 'field_bound_boxes')) {
                            $out[] = $field;
                        }
                    }
                    unset($tmp_fields);
                } else {
                    if ($this->getOne("Field_key", "`Field_key` = '" . $this->getOne('Key', "`ID` = " . (int) $group['Fields'], 'listing_fields') . "'", 'field_bound_boxes')) {
                        $out[] = (int) $group['Fields'];
                    }
                }
            }
            unset($groups);
            return $out;
        }
    }
    function ajaxRebuildItems($box_key)
    {
        global $_response, $rlDb;
        $box_info = $rlDb->fetch('*', array(
            'Key' => $box_key
        ), null, null, 'field_bound_boxes', 'row');
        if (!$box_info)
            return $_response;
        $tmp_fields[0] = $rlDb->fetch('*', array(
            'Key' => $box_info['Field_key']
        ), null, null, 'listing_fields', 'row');
        $tmp_values    = $GLOBALS['rlCommon']->fieldValuesAdaptation($tmp_fields, 'listing_fields');
        $old_values    = $rlDb->fetch('*', array(
            'Box_ID' => $box_info['ID']
        ), null, null, 'field_bound_items');
        foreach ($old_values as $key => $value) {
            if ($value['Icon']) {
                unlink(RL_FILES . $value['Icon']);
            }
        }
        $sql = "DELETE FROM `" . RL_DBPREFIX . "field_bound_items` WHERE `Box_ID` = " . $box_info['ID'];
        $rlDb->query($sql);
        $pos = 1;
        foreach ($tmp_values[0]['Values'] as $key => $value) {
            $insert_values[$key]['Key']      = $value['Key'];
            $insert_values[$key]['pName']    = $value['pName'];
            $insert_values[$key]['Box_ID']   = $box_info['ID'];
            $insert_values[$key]['Position'] = $pos;
            $pos++;
        }
        unset($tmp_values, $tmp_fields);
        if ($GLOBALS['rlActions']->insert($insert_values, 'field_bound_items')) {
            $this->updateBoxContent($box_key);
            $_response->script("
				itemsGrid.reload();
				printMessage('notice', '{$GLOBALS['lang']['fb_items_recopied']}')
			");
        }
        return $_response;
    }
    function isImage($image = false)
    {
        if (!$image) {
            return false;
        }
        $allowed_types = array(
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png'
        );
        $img_details   = getimagesize($image);
        if (in_array($img_details['mime'], $allowed_types)) {
            return true;
        }
        return false;
    }
}