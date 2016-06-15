<?php
class rlExportImport extends reefless
{
    var $loop_category_id = false;
    var $loop_account_id = false;
    function ajaxFetchOptions($type = false, $el = 'import_category_id', $user_mode = false)
    {
        global $_response, $rlListingTypes, $lang, $rlSmarty;
        $this->loadClass('Categories');
        $categories = $GLOBALS['rlCategories']->getCatTitles($type);
        if ($categories) {
            $options = '<option value="">' . $lang['select'] . '</option>';
            foreach ($categories as $category) {
                $margin    = $category['margin'] ? 'margin-left: ' . $category['margin'] . 'px;' : '';
                $highlight = $category['Level'] == 0 ? 'class="highlight_opt highlight_option"' : '';
                if (defined('REALM') && REALM == 'admin')
                    $count = $el == 'export_category_id' ? " ({$category['Count']})" : '';
                $options .= '<option ' . $highlight . ' style="' . $margin . '" value="' . $category['ID'] . '">' . $lang[$category['pName']] . $count . '</option>';
            }
        } else {
            $options = '<option value="">' . $lang['eil_no_categories_available'] . '</option>';
        }
        $_response->script("$('select[name={$el}]').html('{$options}');");
        if ($el == 'export_category_id') {
            $this->loadClass('Search');
            $fields = $GLOBALS['rlSearch']->buildSearch($type . '_quick', $type);
            $rlSmarty->assign_by_ref('fields', $fields);
            $tpl = $user_mode ? RL_PLUGINS . 'export_import' . RL_DS . 'search.tpl' : RL_PLUGINS . 'export_import' . RL_DS . 'admin' . RL_DS . 'search.tpl';
            $_response->assign('export_table', 'innerHTML', $rlSmarty->fetch($tpl, null, null, false));
        }
        if ($GLOBALS['multi_formats']) {
            foreach ($GLOBALS['multi_formats'] as $multi_format) {
                $sql            = "SELECT * FROM `" . RL_DBPREFIX . "listing_fields` WHERE `Condition` = '{$multi_format['Key']}' AND `Key` NOT REGEXP 'level[0-9]'";
                $related_fields = $this->getAll($sql);
                foreach ($related_fields as $k => $field) {
                    $m_fields[] = $field;
                }
            }
            $rlSmarty->assign('fields', $m_fields);
            $tpl2 = RL_PLUGINS . 'multiField' . RL_DS . 'mf_reg_js.tpl';
            $js   = $GLOBALS['rlSmarty']->fetch($tpl2, null, null, false);
            $_response->script($js);
        }
        return $_response;
    }
    function import(&$data, &$available_rows, &$columns, $fields, &$start, $limit = 5, &$listing_type, &$category_id, &$account_id, &$plan_id, $paid, &$status, $user_mode)
    {
        global $rlListingTypes, $lang, $rlActions, $config, $rlCategories, $plan_info;
        if (!$data || !$available_rows || !$limit || !$listing_type || !$category_id || !$account_id || !$plan_id)
            return false;
        if (is_numeric(array_search('Category_ID', $fields)) && is_numeric($subcat_index = array_search('Subcategory_ID', $fields))) {
            unset($fields[$subcat_index]);
        }
        if ($reference_plugin = $this->getOne('ID', "`Key` = 'ref' AND `Status` = 'active'", 'plugins')) {
            $this->loadClass('Ref', null, 'ref');
        }
        $add_sql_fields = 'AND (';
        foreach ($fields as $field_index => $field) {
            if ($field && $columns[$field_index]) {
                $add_sql_fields .= "`Key` = '{$field}' OR ";
                $available_fields[$field] = $field_index;
            }
        }
        $add_sql_fields = rtrim($add_sql_fields, ' OR ');
        $add_sql_fields .= ')';
        $fields_info_tmp = $this->fetch(array(
            'Key',
            'Type',
            'Default',
            'Values',
            'Condition',
            'Map'
        ), null, "WHERE `Status` <> 'trash' " . $add_sql_fields, null, 'listing_fields');
        foreach ($fields_info_tmp as $field) {
            $fields_info[$field['Key']] = $field;
        }
        unset($fields_info_tmp);
        $plan_info = $this->fetch(array(
            'Featured',
            'Image',
            'Image_unlim',
            'Price',
            'Limit'
        ), array(
            'ID' => $plan_id
        ), null, 1, 'listing_plans', 'row');
        for ($index = $start; $index < $start + $limit; $index++) {
            $row_data = $data[$available_rows[$index]];
            if (!$row_data)
                continue;
            foreach ($fields as $field_index => $field) {
                if ($field && $columns[$field_index]) {
                    if ($field == 'Category_ID') {
                        $this->handleCategory($row_data, $field_index, $subcat_index);
                    } else {
                        if ($value = $this->adaptValue($row_data[$field_index], $field, $fields_info)) {
                            if ($fields_info[$field]['Map']) {
                                $loc_request = $value . ', ';
                            }
                            $insert_fields[$field] = preg_replace('/[\r]+/', '<br />', $value);
                        }
                    }
                }
            }
            $set_category_id        = $this->loop_category_id ? $this->loop_category_id : $category_id;
            $this->loop_category_id = false;
            $set_account_id         = $this->loop_account_id ? $this->loop_account_id : $account_id;
            $this->loop_account_id  = false;
            $user_status            = $config['listing_auto_approval'] ? $status : 'pending';
            $import                 = array(
                'Category_ID' => $set_category_id,
                'Account_ID' => $set_account_id,
                'Plan_ID' => $plan_id,
                'Pay_date' => $paid ? 'NOW()' : '',
                'Date' => 'NOW()',
                'Status' => $user_mode ? $user_status : $status,
                'Import_file' => $_SESSION['iel_data']['file_name']
            );
            if ($plan_info['Featured']) {
                $import['Featured_ID']   = $plan_id;
                $import['Featured_date'] = $paid ? 'NOW()' : '';
            }
            if ($user_mode) {
                $this->planData($available_rows[$index], $import, $plan_id, $plan_info);
            }
            $import = array_merge($import, $insert_fields);
            if ($loc_request) {
                $loc_request = urlencode(rtrim($loc_request, ', '));
                $content     = $this->getPageContent("http://maps.googleapis.com/maps/api/geocode/json?address={$loc_request}&sensor=false");
                $this->loadClass('Json');
                $content = $GLOBALS['rlJson']->decode($content);
                if (strtolower($content->status) == 'ok') {
                    $import['Loc_address']   = $content->results[0]->formatted_address;
                    $import['Loc_latitude']  = $content->results[0]->geometry->location->lat;
                    $import['Loc_longitude'] = $content->results[0]->geometry->location->lng;
                }
            }
            $rlActions->insertOne($import, 'listings');
            $imported_id = mysql_insert_id();
            if ($reference_plugin) {
                $ref            = $GLOBALS['rlRef']->generate($imported_id, $config['ref_tpl']);
                $ref_update_sql = "UPDATE `" . RL_DBPREFIX . "listings` SET `ref_number` = '{$ref}' WHERE `ID` = '{$imported_id}'";
                $GLOBALS['rlDb']->query($ref_update_sql);
            }
            if ($status == 'active' && $paid) {
                $rlCategories->listingsIncrease($set_category_id, $listing_type);
            }
            if (array_key_exists('Main_photo_url', $available_fields)) {
                $this->uploadPictures('url', $imported_id, $row_data[$available_fields['Main_photo_url']]);
            }
            if (array_key_exists('Main_photo_zip', $available_fields)) {
                $this->uploadPictures('zip', $imported_id, $row_data[$available_fields['Main_photo_zip']]);
            }
            if (array_key_exists('Youtube_video', $available_fields)) {
                $this->parseYouTube($imported_id, $row_data[$available_fields['Youtube_video']]);
            }
            unset($insert_fields, $row_data, $imported_id, $import, $update);
            unset($_SESSION['iel_data']['post']['rows'][$available_rows[$index]]);
        }
        if ($user_mode) {
            $this->updatePlans($account_id);
        }
        return true;
    }
    function planData($index = false, &$import, $plan_id = false, $plan_info = false)
    {
        global $config;
        $status        = $import['Status'];
        $plan_id       = $import['Plan_ID'];
        $pay_date      = $import['Pay_date'];
        $featured_id   = $import['Featured_ID'];
        $featured_date = $import['Featured_date'];
        if ($_SESSION['iel_data']['post']['plan'][$index] || $_SESSION['iel_data']['user_plans'][$plan_id]) {
            $user_plan_id = $_SESSION['iel_data']['post']['plan'][$index] ? $_SESSION['iel_data']['post']['plan'][$index] : $plan_id;
            $user_plan    = $_SESSION['iel_data']['user_plans'][$user_plan_id];
            if ($_SESSION['iel_data']['user_plans'][$user_plan_id]['Listings_remains'] > 0) {
                $status   = 'active';
                $plan_id  = $user_plan['ID'];
                $pay_date = 'NOW()';
                $type     = ucfirst($_SESSION['iel_data']['post']['type'][$index]);
                if (($user_plan['Featured'] && !$user_plan['Advanced_mode']) || ($user_plan['Advanced_mode'] && $type == 'Featured' && $user_plan[$type . '_remains'] > 0)) {
                    $featured_id   = $user_plan['ID'];
                    $featured_date = 'NOW()';
                }
                $_SESSION['iel_data']['user_plans'][$user_plan_id]['Listings_remains'] -= 1;
                if ($user_plan['Advanced_mode'] && $_SESSION['iel_data']['user_plans'][$user_plan_id][$type . '_remains'] > 0) {
                    $_SESSION['iel_data']['user_plans'][$user_plan_id][$type . '_remains'] -= 1;
                }
            }
        } else {
            if (!$plan_info['Price'] && !$plan_info['Limit']) {
                $status   = 'active';
                $pay_date = 'NOW()';
                if ($plan_info['Featured']) {
                    $featured_id   = $plan_id;
                    $featured_date = 'NOW()';
                }
            }
        }
        $import['Status']        = $config['listing_auto_approval'] ? $status : 'pending';
        $import['Plan_ID']       = $plan_id;
        $import['Pay_date']      = $pay_date;
        $import['Featured_ID']   = $featured_id;
        $import['Featured_date'] = $featured_date;
    }
    function updatePlans($account_id = false)
    {
        global $rlActions;
        if (!$account_id || empty($_SESSION['iel_data']['user_plans']))
            return;
        foreach ($_SESSION['iel_data']['user_plans'] as $plan) {
            if ($plan['Package_ID'] || $plan['Limit'] > 0) {
                if ($plan['Package_ID']) {
                    $update = array(
                        'fields' => array(
                            'Listings_remains' => $plan['Listings_remains'],
                            'Standard_remains' => $plan['Standard_remains'],
                            'Featured_remains' => $plan['Featured_remains']
                        ),
                        'where' => array(
                            'ID' => $plan['Package_ID']
                        )
                    );
                    $rlActions->updateOne($update, 'listing_packages');
                } else {
                    $insert = array(
                        'Account_ID' => $account_id,
                        'Plan_ID' => $plan['ID'],
                        'Listings_remains' => $plan['Listings_remains'],
                        'Standard_remains' => $plan['Standard_remains'],
                        'Featured_remains' => $plan['Featured_remains'],
                        'Type' => $plan['Package'] ? $plan['Package'] : 'limited',
                        'Date' => 'NOW()',
                        'IP' => $_SERVER['REMOTE_ADDR']
                    );
                    $rlActions->insertOne($insert, 'listing_packages');
                    $_SESSION['iel_data']['user_plans'][$plan['ID']]['Package_ID'] = mysql_insert_id();
                }
            }
        }
    }
    function handleCategory(&$row, $category_index = false, $subcategory_index = false)
    {
        $value = $row[$category_index];
        if (is_numeric($value)) {
            if ($category_id = $this->getOne('ID', "`ID` = '{$value}' AND `Status` = 'active'", 'categories')) {
                $this->loop_category_id = $category_id;
            }
        } elseif ($value) {
            if ($row[$subcategory_index] && $subcategory_index) {
                $subcategory_value = $row[$subcategory_index];
                $compare_sign      = strlen($value) > 4 ? '?' : '';
                $sql               = "SELECT `T1`.`ID` FROM `" . RL_DBPREFIX . "categories` AS `T1` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('categories+name+', `T1`.`Key`) = `T2`.`Key` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T3`.`ID` = `T1`.`Parent_ID` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T4` ON CONCAT('categories+name+', `T3`.`Key`) = `T4`.`Key` ";
                $sql .= "WHERE ";
                $sql .= "`T4`.`Value` RLIKE '^{$value}{$compare_sign}' AND `T4`.`Key` LIKE 'categories+name+%' AND ";
                $sql .= "`T2`.`Value` RLIKE '^{$subcategory_value}{$compare_sign}' AND `T2`.`Key` LIKE 'categories+name+%' ";
                $sql .= "AND `T1`.`Status` <> 'trash' ";
                $sql .= "LIMIT 1";
                if ($category = $this->getRow($sql)) {
                    $this->loop_category_id = $category['ID'];
                }
            } else {
                $compare_sign = strlen($value) > 5 ? '?' : '';
                $sql          = "SELECT `Key` FROM `" . RL_DBPREFIX . "lang_keys` ";
                $sql .= "WHERE `Value` RLIKE '^{$value}{$compare_sign}' AND `Key` LIKE 'categories+name+%' AND `Status` <> 'trash' LIMIT 1";
                if ($phrase_key = $this->getRow($sql)) {
                    $phrase_key_exp = array_reverse(explode('+', $phrase_key['Key']));
                    if ($category_id = $this->getOne('ID', "`Key` = '{$phrase_key_exp[0]}' AND `Status` = 'active'", 'categories')) {
                        $this->loop_category_id = $category_id;
                    }
                }
            }
        }
    }
    function adaptValue($value, $key = false, &$info)
    {
        global $rlValid;
        if (!$key)
            return $value;
        $value = $rlValid->xSql(trim($value));
        switch ($key) {
            case 'Account_ID':
                if (is_numeric($value)) {
                    if ($account_id = $this->getOne('ID', "`ID` = '{$value}' AND `Status` = 'active'", 'accounts')) {
                        $this->loop_account_id = $account_id;
                    }
                } elseif ($value) {
                    if ($account_id = $this->getOne('ID', "`Username` = '{$value}' AND `Status` = 'active'", 'accounts')) {
                        $this->loop_account_id = $account_id;
                    }
                }
                break;
            default:
                switch ($info[$key]['Type']) {
                    case 'text':
                    case 'textarea':
                    case 'number':
                    case 'date':
                        if ($value != '') {
                            $out = $value;
                        } elseif ($info[$key]['Default']) {
                            $out = $info[$key]['Default'];
                        }
                        break;
                    case 'bool':
                        if (is_numeric($value)) {
                            $out = $value ? 1 : 0;
                        } elseif ($value == '' && $info[$key]['Default']) {
                            $out = $info[$key]['Default'];
                        } else {
                            $out = in_array(strtolower($value), array(
                                'no',
                                'n'
                            )) ? 0 : 1;
                        }
                        break;
                    case 'phone':
                        preg_match('/(\+\s?[0-9]{0,4})?\s*(\(?[0-9]{0,5}\)?)?\s*([-0-9]{0,9})?\s*(\/.+)?/', $value, $matches);
                        if ($matches) {
                            if ($matches[1]) {
                                $out = 'c:' . (int) $matches[1] . '|';
                            }
                            $out .= 'a:' . (int) $matches[2] . '|';
                            $out .= 'n:' . (int) $matches[3];
                            if ($matches[4]) {
                                $out .= '|e:' . (int) $matches[4];
                            }
                        }
                        break;
                    case 'mixed':
                        preg_match('/([^\s]+)?\s*([\/,%,\w\d\,\.\s]+)?\s*([^\s]+)?/', $value, $matches);
                        if ($matches[1]) {
                            $out = (float) str_replace(',', '', $matches[1]);
                            if ($unit = $matches[2]) {
                                $unit         = trim($unit);
                                $compare_sign = strlen($unit) > 5 ? '?' : '';
                                if ($info[$key]['Condition']) {
                                    $sql = "SELECT `Key` FROM `" . RL_DBPREFIX . "lang_keys` ";
                                    $sql .= "WHERE `Value` = '{$unit}' AND `Key` REGEXP 'data_formats\\\+name\\\+({$info[$key]['Condition']}_)?' AND `Status` <> 'trash' LIMIT 1";
                                    if ($phrase_key = $this->getRow($sql)) {
                                        $phrase_key_exp = str_replace('data_formats+name+', '', $phrase_key['Key']);
                                        $out .= '|' . $phrase_key_exp;
                                    }
                                } else {
                                    $sql = "SELECT `Key` FROM `" . RL_DBPREFIX . "lang_keys` ";
                                    $sql .= "WHERE `Value` = '{$unit}' AND `Key` REGEXP 'listing_fields\\\+name\\\+{$key}_[0-9]+' AND `Status` <> 'trash' LIMIT 1";
                                    if ($phrase_key = $this->getRow($sql)) {
                                        $phrase_key_exp = str_replace('listing_fields+name+', '', $phrase_key['Key']);
                                        $out .= '|' . $phrase_key_exp;
                                    }
                                }
                            }
                        }
                        break;
                    case 'price':
                        preg_match('/([\D]+)?\s*([\d\,]+)?\s*([\D]+)?/', $value, $matches);
                        if ($matches[2]) {
                            $out = (float) str_replace(',', '', $matches[2]);
                            if ($unit = $matches[1] ? $matches[1] : $matches[3]) {
                                $unit = trim($unit);
                                $sql  = "SELECT `Key` FROM `" . RL_DBPREFIX . "lang_keys` ";
                                $sql .= "WHERE `Value` = '{$unit}' AND `Key` LIKE 'data_formats+name+%' AND `Status` <> 'trash' LIMIT 1";
                                if ($phrase_key = $this->getRow($sql)) {
                                    $phrase_key_exp = array_reverse(explode('+', $phrase_key['Key']));
                                    $out .= '|' . $phrase_key_exp[0];
                                }
                            }
                        }
                        break;
                    case 'select':
                    case 'radio':
                    case 'checkbox':
                        if ($value != '') {
                            if ($info[$key]['Condition'] == 'years') {
                                $out = $value;
                            } elseif ($info[$key]['Condition']) {
                                if ($value_exp = explode(',', $value)) {
                                    foreach ($value_exp as $sub_value) {
                                        $sub_value    = trim($sub_value);
                                        $compare_sign = strlen($sub_value) > 5 ? '?' : '';
                                        $sql          = "SELECT `Key` FROM `" . RL_DBPREFIX . "lang_keys` ";
                                        $sql .= "WHERE `Value` = '{$sub_value}' AND `Key` RLIKE 'data_formats\\\+name\\\+({$info[$key]['Condition']}_)?' AND `Status` <> 'trash' LIMIT 1";
                                        if ($phrase_key = $this->getRow($sql)) {
                                            $phrase_key_exp = str_replace('data_formats+name+', '', $phrase_key['Key']);
                                            $out .= $phrase_key_exp . ',';
                                        }
                                    }
                                }
                            } else {
                                if ($value_exp = explode(',', $value)) {
                                    foreach ($value_exp as $sub_value) {
                                        $sub_value    = trim($sub_value);
                                        $compare_sign = strlen($sub_value) > 5 ? '?' : '';
                                        $sql          = "SELECT `Key` FROM `" . RL_DBPREFIX . "lang_keys` ";
                                        $sql .= "WHERE `Value` RLIKE '^{$sub_value}{$compare_sign}' AND `Key` REGEXP 'listing_fields\\\+name\\\+{$key}_[0-9]+' AND `Status` <> 'trash' LIMIT 1";
                                        if ($phrase_key = $this->getRow($sql)) {
                                            $phrase_key_exp = array_reverse(explode('_', $phrase_key['Key']));
                                            $out .= $phrase_key_exp[0] . ',';
                                        }
                                    }
                                }
                            }
                        } elseif ($info[$key]['Default']) {
                            $out = $info[$key]['Default'];
                        }
                        $out = rtrim($out, ',');
                        break;
                }
                break;
        }
        return $out ? $out : false;
    }
    function uploadPictures($mode = 'url', $id = false, &$value)
    {
        global $config, $rlActions, $plan_info;
        if (!$id || !$value)
            return false;
        $this->loadClass('Crop');
        $this->loadClass('Resize');
        $ext_regexp = "/(jpg|jpeg|jpe|gif|png|bmp)$/";
        $dir        = RL_FILES . date('m-Y') . RL_DS . 'ad' . $id . RL_DS;
        $dir_name   = date('m-Y') . '/ad' . $id . '/';
        $url        = RL_FILES_URL . $dir_name;
        $this->rlMkdir($dir);
        switch ($mode) {
            case 'url':
                $picture_urls = explode(',', $value);
                $iteration    = 1;
                foreach ($picture_urls as $url) {
                    $url = trim($url);
                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                    if (!(bool) preg_match($ext_regexp, $ext))
                        continue;
                    $orig_name      = 'orig_' . time() . mt_rand() . '.' . $ext;
                    $orig_path      = $dir . $orig_name;
                    $thumbnail_name = 'thumb_' . time() . mt_rand() . '.' . $ext;
                    $thumbnail_path = $dir . $thumbnail_name;
                    $large_name     = 'large_' . time() . mt_rand() . '.' . $ext;
                    $large_path     = $dir . $large_name;
                    if (!copy($url, $orig_path)) {
                        $source = file_get_contents($url);
                        $handle = fopen($orig_path, "w");
                        fwrite($handle, $source);
                        fclose($handle);
                    }
                    chmod($orig_path, 0644);
                    if (!is_readable($orig_path))
                        continue;
                    $GLOBALS['rlCrop']->loadImage($orig_path);
                    $GLOBALS['rlCrop']->cropBySize($config['pg_upload_thumbnail_width'], $config['pg_upload_thumbnail_height'], ccCENTER);
                    $GLOBALS['rlCrop']->saveImage($thumbnail_path, $config['img_quality']);
                    $GLOBALS['rlCrop']->flushImages();
                    $GLOBALS['rlResize']->resize($thumbnail_path, $thumbnail_path, 'C', array(
                        $config['pg_upload_thumbnail_width'],
                        $config['pg_upload_thumbnail_height']
                    ), true, false);
                    if ($config['img_crop_module']) {
                        $GLOBALS['rlCrop']->loadImage($orig_path);
                        $GLOBALS['rlCrop']->cropBySize($config['pg_upload_large_width'], $config['pg_upload_large_height'], ccCENTER);
                        $GLOBALS['rlCrop']->saveImage($large_path, $config['img_quality']);
                        $GLOBALS['rlCrop']->flushImages();
                    }
                    $GLOBALS['rlResize']->resize($config['img_crop_module'] ? $large_path : $orig_path, $large_path, 'C', array(
                        $config['pg_upload_large_width'],
                        $config['pg_upload_large_height']
                    ), false, $config['watermark_using']);
                    if (!$config['img_crop_interface']) {
                        unlink($orig_path);
                        $orig_name = false;
                    }
                    $pictures[] = array(
                        'Listing_ID' => $id,
                        'Position' => $iteration,
                        'Photo' => $dir_name . $large_name,
                        'Thumbnail' => $dir_name . $thumbnail_name,
                        'Original' => $orig_name ? $dir_name . $orig_name : ''
                    );
                    if ($iteration == 1) {
                        $main_photo = $dir_name . $thumbnail_name;
                    }
                    $iteration++;
                }
                break;
            case 'zip':
                $picture_names = explode(',', $value);
                $iteration     = 1;
                foreach ($picture_names as $name) {
                    $name = trim($name);
                    if (!(bool) preg_match($ext_regexp, $name))
                        continue;
                    $picture_dir    = $_SESSION['iel_data']['archive_dir'] . RL_DS . $name;
                    $ext            = pathinfo($picture_dir, PATHINFO_EXTENSION);
                    $orig_name      = 'orig_' . time() . mt_rand() . '.' . $ext;
                    $orig_path      = $dir . $orig_name;
                    $thumbnail_name = 'thumb_' . time() . mt_rand() . '.' . $ext;
                    $thumbnail_path = $dir . $thumbnail_name;
                    $large_name     = 'large_' . time() . mt_rand() . '.' . $ext;
                    $large_path     = $dir . $large_name;
                    if (!copy($picture_dir, $orig_path)) {
                        $source = file_get_contents($picture_dir);
                        $handle = fopen($orig_path, "w");
                        fwrite($handle, $source);
                        fclose($handle);
                    }
                    chmod($orig_path, 0644);
                    if (!is_readable($orig_path))
                        continue;
                    $GLOBALS['rlCrop']->loadImage($orig_path);
                    $GLOBALS['rlCrop']->cropBySize($config['pg_upload_thumbnail_width'], $config['pg_upload_thumbnail_height'], ccCENTER);
                    $GLOBALS['rlCrop']->saveImage($thumbnail_path, $config['img_quality']);
                    $GLOBALS['rlCrop']->flushImages();
                    $GLOBALS['rlResize']->resize($thumbnail_path, $thumbnail_path, 'C', array(
                        $config['pg_upload_thumbnail_width'],
                        $config['pg_upload_thumbnail_height']
                    ), true, false);
                    if ($config['img_crop_module']) {
                        $GLOBALS['rlCrop']->loadImage($orig_path);
                        $GLOBALS['rlCrop']->cropBySize($config['pg_upload_large_width'], $config['pg_upload_large_height'], ccCENTER);
                        $GLOBALS['rlCrop']->saveImage($large_path, $config['img_quality']);
                        $GLOBALS['rlCrop']->flushImages();
                    }
                    $GLOBALS['rlResize']->resize($config['img_crop_module'] ? $large_path : $orig_path, $large_path, 'C', array(
                        $config['pg_upload_large_width'],
                        $config['pg_upload_large_height']
                    ), false, $config['watermark_using']);
                    if (!$config['img_crop_interface']) {
                        unlink($orig_path);
                        $orig_name = false;
                    }
                    $pictures[] = array(
                        'Listing_ID' => $id,
                        'Position' => $iteration,
                        'Photo' => $dir_name . $large_name,
                        'Thumbnail' => $dir_name . $thumbnail_name,
                        'Original' => $orig_name ? $dir_name . $orig_name : ''
                    );
                    if ($iteration == 1) {
                        $main_photo = $dir_name . $thumbnail_name;
                    }
                    $iteration++;
                }
                break;
        }
        $photos_count = !$plan_info['Image_unlim'] && count($pictures) > $plan_info['Image'] ? $plan_info['Image'] : count($pictures);
        $rlActions->insert($pictures, 'listing_photos');
        if ($photos_count && version_compare($config['rl_version'], '4.1.0') >= 0) {
            $update = array(
                'fields' => array(
                    'Main_photo' => $main_photo,
                    'Photos_count' => $photos_count
                ),
                'where' => array(
                    'ID' => $id
                )
            );
            $rlActions->updateOne($update, 'listings');
        }
    }
    function parseYouTube($id = false, &$value)
    {
        global $rlActions;
        $video_items = explode(',', $value);
        $iteration   = 1;
        foreach ($video_items as $item) {
            if (0 === strpos($item, 'http')) {
                if (false !== strpos($item, 'youtu.be')) {
                    $matches[1] = array_pop(explode('/', $item));
                } else {
                    preg_match('/v=([\w-_]*)/', $item, $matches);
                }
            } else {
                preg_match('/(.{5,15})/', $item, $matches);
            }
            if ($matches[1]) {
                $insert = array(
                    'Listing_ID' => $id,
                    'Preview' => $matches[1],
                    'Position' => $possition,
                    'Type' => 'youtube'
                );
                $rlActions->insertOne($insert, 'listing_video');
                $iteration++;
            }
        }
    }
    function getUserPlans($account_info = false)
    {
        if (!$account_info)
            return false;
        global $rlLang;
        $sql = "SELECT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Featured`, `T1`.`Advanced_mode`, `T1`.`Limit`, `T1`.`Standard_listings`, ";
        $sql .= "`T1`.`Featured_listings`, `T1`.`Listing_number`, ";
        $sql .= "`T2`.`Type` AS `Package`, `T2`.`Listings_remains`, `T2`.`Standard_remains`, `T2`.`Featured_remains`, `T2`.`ID` AS `Package_ID` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listing_plans` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_packages` AS `T2` ON `T1`.`ID` = `T2`.`Plan_ID` AND `T2`.`Account_ID` = {$account_info['ID']} ";
        $sql .= "WHERE `T1`.`Status` = 'active' AND ";
        $sql .= "(FIND_IN_SET('{$account_info['Type']}', `T1`.`Allow_for`) > 0 OR `T1`.`Allow_for` = '') AND";
        $sql .= "(";
        $sql .= " (`T2`.`Type` = 'package' AND ";
        $sql .= "  (`T1`.`Listing_number` = 0 OR ";
        $sql .= "   ( ";
        $sql .= "      `T1`.`Listing_number` > 0 AND `T2`.`Listings_remains` > 0 AND `T1`.`Plan_period` > 0 AND ";
        $sql .= "     DATE_ADD(`T2`.`Date`, INTERVAL `T1`.`Plan_period` DAY) <= NOW() ";
        $sql .= "   )";
        $sql .= "  ) ";
        $sql .= " ) OR ";
        $sql .= " ( ";
        $sql .= "  (`T1`.`Type` = 'listing' AND `T1`.`Price` = 0) OR ";
        $sql .= "  (`T2`.`Type` = 'limited' AND `T2`.`Listings_remains` > 0) ";
        $sql .= " ) ";
        $sql .= ") ";
        $sql .= "GROUP BY `T1`.`ID` ";
        $sql .= "ORDER BY `T1`.`Position` ";
        $plans_tmp = $rlLang->replaceLangKeys($this->getAll($sql), 'listing_plans', array(
            'name'
        ));
        foreach ($plans_tmp as $plan) {
            if ($plan['Limit'] > 0 && !$plan['Package']) {
                $plan['Listings_remains'] = $plan['Limit'];
            }
            $plans[$plan['ID']] = $plan;
        }
        unset($plans_tmp);
        return $plans;
    }
}