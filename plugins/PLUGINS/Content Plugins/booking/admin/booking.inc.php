<?php
if (isset($_GET['q'])) {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    $start = (int) $_GET['start'];
    $limit = (int) $_GET['limit'];
    $reefless->loadClass('Json');
}
if ($_GET['q'] == 'ext') {
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $id         = (int) $_GET['id'];
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'booking_fields');
        exit;
    }
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.`ID`, `T1`.`Key`, `T1`.`Type`, `T1`.`Required`, `T1`.`Position`,`T1`.`Status` ";
    $sql .= "FROM `" . RL_DBPREFIX . "booking_fields` AS `T1` ";
    $sql .= "WHERE `T1`.`Status` <> 'trash' ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    foreach ($data as $key => $value) {
        $data[$key]['name']     = $rlDb->getOne('Value', "`Key`='booking_fields+name+{$data[$key]['Key']}' AND `Code`='" . RL_LANG_CODE . "'", 'lang_keys');
        $data[$key]['Type']     = $l_types[$data[$key]['Type']];
        $data[$key]['Required'] = $data[$key]['Required'] ? $lang['yes'] : $lang['no'];
        $data[$key]['Status']   = $lang[$data[$key]['Status']];
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
    exit;
} elseif ($_GET['q'] == 'ext_stat') {
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $type  = $rlValid->xSql($_GET['type']);
        $field = $rlValid->xSql($_GET['field']);
        $value = $rlValid->xSql(nl2br($_GET['value']));
        $key   = $rlValid->xSql($_GET['key']);
        $id    = (int) $_GET['id'];
        $table = 'booking_requests';
        if ($field == 'Booking_status') {
            $field = 'Status';
            $table = 'listings_book';
        }
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, $table);
        exit;
    }
    $sql = "SELECT SQL_CALC_FOUND_ROWS `T3`.`ID` AS `Listing_ID`,`T1`.`ID` AS `Request_ID`, `T4`.`Username`, IF(`T1`.`Renter_ID` > 0, CONCAT(`T4`.`First_name`, ' ', `T4`.`Last_name`), ";
    $sql .= "CONCAT(`T1`.`first_name`, ' ', `T1`.`last_name`)) AS `Booking_client`, `T2`.`From` AS `Booking_from`, ";
    $sql .= "`T2`.`To` AS `Booking_to`, `T2`.`Status` AS `Booking_status`, `T5`.`Type`, ";
    $sql .= "`T2`.`ID` AS `Booking_ID`, `T1`.`Status` AS `Book_status`, `T3`.* ";
    $sql .= "FROM `" . RL_DBPREFIX . "booking_requests` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings_book` AS `T2` ON `T1`.`Book_ID` = `T2`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T3` ON `T2`.`Listing_ID` = `T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T4` ON `T4`.`ID` = `T1`.`Renter_ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T5` ON `T3`.`Category_ID` = `T5`.`ID` ";
    $sql .= "GROUP BY `T1`.`ID` ORDER BY `T1`.`Date` DESC LIMIT {$start},{$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $reefless->loadClass('Listings');
    foreach ($data as $key => $value) {
        switch ($value['Booking_status']) {
            case 'process':
                $data[$key]['Booking_status'] = $lang['new'];
                break;
            case 'booked':
                $data[$key]['Booking_status'] = $lang['booking_accepted'];
                break;
            case 'refused':
                $data[$key]['Booking_status'] = $lang['booking_refused'];
                break;
        }
        $data[$key]['Booking_client'] = trim($data[$key]['Booking_client']);
        $data[$key]['Booking_client'] = !empty($data[$key]['Booking_client']) ? $data[$key]['Booking_client'] : $data[$key]['Username'];
        $data[$key]['Listing_title']  = $rlListings->getListingTitle($data[$key]['Category_ID'], $data[$key], $data[$key]['Type']);
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
    exit;
} elseif ($_GET['q'] == 'ext_ranges_list') {
    $id  = (int) $_GET['id'];
    $sql = "SELECT SQL_CALC_FOUND_ROWS `T1`.`ID`, `T1`.`From`, `T1`.`To`, `T1`.`Price`, `T2`.`Value` AS `desc` FROM `" . RL_DBPREFIX . "booking_rate_range` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON `T2`.`Key` = CONCAT('booking_range+desc+', `T1`.`From`, '_', `T1`.`To`) ";
    $sql .= "WHERE `T1`.`Listing_ID` = '{$id}' ORDER BY `T1`.`From` LIMIT {$start},{$limit}";
    $data            = $rlDb->getAll($sql);
    $count           = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
    exit;
} elseif ($_GET['q'] == 'ext_ranges') {
    $reefless->loadClass('Listings');
    $reefless->loadClass('ListingTypes');
    $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T1`.`ID` AS `Listing_ID`, `T3`.`Type` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
    $sql .= "WHERE (TIMESTAMPDIFF(HOUR, `T1`.`Pay_date`, NOW()) <= `T2`.`Listing_period` * 24 OR `T2`.`Listing_period` = 0) ";
    $sql .= "AND `T1`.`booking_module` = '1' AND `T1`.`Status`='active' LIMIT {$start},{$limit}";
    $listings = $rlDb->getAll($sql);
    $count    = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $data     = array();
    foreach ($listings as $key => $value) {
        $data[$key]['ID']    = $listings[$key]['Listing_ID'];
        $data[$key]['ref']   = $listings[$key]['ref_number'] ? $listings[$key]['ref_number'] : 'N/A';
        $data[$key]['title'] = $rlListings->getListingTitle($listings[$key]['Category_ID'], $listings[$key], $listings[$key]['Type']);
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    echo $rlJson->encode($output);
    exit;
} else {
    $refExists = $rlDb->getOne('Key', "`Key` = 'ref'", 'plugins');
    $rlSmarty->assign('refExists', empty($refExists) ? false : true);
    if ($_GET['mode'] == 'additionals') {
        $bcAStep = $lang['booking_admin_additional_options'];
        if (!isset($_POST['submit'])) {
            $time_frame = $rlDb->getOne('Values', "`Key`='time_frame'", 'listing_fields');
            $exp_frame  = explode(',', $time_frame);
            foreach ($exp_frame as $key => $frame) {
                $rent_time_frame[$key]['value'] = $frame;
                $rent_time_frame[$key]['name']  = $lang['listing_fields+name+time_frame_' . $frame];
            }
            $rlSmarty->assign_by_ref('rent_time_frame', $rent_time_frame);
            $rlSmarty->assign_by_ref('cur_rent_time_frame', $config['booking_time_frame']);
            if ($config['booking_binding_plans']) {
                $plans = $rlDb->fetch('*', array(
                    'Status' => 'active'
                ), null, null, 'listing_plans');
                $plans = $rlLang->replaceLangKeys($plans, 'listing_plans', array(
                    'name'
                ));
                $rlSmarty->assign_by_ref('plans', $plans);
            }
            if ($config['booking_binding_plans']) {
                $_POST['plan'] = explode(',', $config['booking_plans']);
            }
            $price_fields = $rlDb->fetch(array(
                'Key'
            ), array(
                'Status' => 'active',
                'Type' => 'price'
            ), null, null, 'listing_fields');
            $price_fields = $rlLang->replaceLangKeys($price_fields, 'listing_fields', array(
                'name'
            ));
            $rlSmarty->assign_by_ref('price_fields', $price_fields);
            $rlSmarty->assign_by_ref('current_price_field', $config['booking_price_field']);
        } else {
            if ($config['booking_binding_plans']) {
                $split_plans = implode(",", $_POST['plan']);
                $rlConfig->setConfig('booking_plans', $split_plans);
            }
            if ($_POST['divide_check']) {
                if (!empty($_POST['divide_price'])) {
                    $rlConfig->setConfig('booking_divide_price', (int) $_POST['divide_price']);
                } else {
                    $rlConfig->setConfig('booking_divide_price', 'null');
                }
            } else {
                $time_frame = serialize($_POST['time_frame']);
                $rlConfig->setConfig('booking_time_frame', $time_frame);
            }
            $price_field = $_POST['price_field'];
            $rlConfig->setConfig('booking_price_field', $price_field);
            $reefless->loadClass('Notice');
            $rlNotice->saveNotice($lang['booking_additionals_saved']);
            $reefless->redirect(array(
                "controller" => $controller,
                "mode" => "additionals"
            ));
        }
    } elseif ($_GET['mode'] == 'ranges') {
        $bcAStep[0] = array(
            'name' => $lang['ext_listing_rate_range_manager'],
            'Controller' => 'booking',
            'Vars' => 'mode=ranges'
        );
        if (isset($_GET['listing_id'])) {
            $reefless->loadClass('Booking', false, 'booking');
            $listing_id = $_GET['listing_id'];
            if ($_GET['view'] == 'rate_range') {
                $bcAStep[1]['name'] = $lang['booking_rate_range'];
                $rlSmarty->register_function('str2money', array(
                    'rlSmarty',
                    'str2money'
                ));
                $rate_range         = $rlDb->fetch('*', array(
                    'Listing_ID' => $listing_id
                ), "ORDER BY `From`", null, 'booking_rate_range');
                $range_regular_desc = $rlDb->getOne('Value', "`Key`='booking_range+regular+desc+{$listing_id}'", 'lang_keys');
                $rlSmarty->assign_by_ref('range_regular_desc', $range_regular_desc);
                foreach ($rate_range as $rKey => $range) {
                    $rate_desc = $rlDb->getOne('Value', "`Key`='booking_range+desc+{$range['From']}_{$range['To']}'", 'lang_keys');
                    if (!empty($rate_desc)) {
                        $rate_range[$rKey]['desc'] = $rate_desc;
                    }
                }
                $no_errors  = $rlDb->getRow("SHOW COLUMNS FROM `" . RL_DBPREFIX . "listings` LIKE 'time_frame'");
                $select_rtf = '';
                if ($no_errors) {
                    $select_rtf = ",`time_frame`";
                }
                $def_price              = $rlDb->getRow("SELECT `{$config['booking_price_field']}`{$select_rtf} FROM `" . RL_DBPREFIX . "listings` WHERE `ID`='{$listing_id}'");
                $expPrice               = explode('|', $def_price[$config['booking_price_field']]);
                $price_cel              = $expPrice[0];
                $adaptPrice['name']     = $lang['data_formats+name+' . $expPrice[1]] . ' ' . $rlSmarty->str2money($price_cel);
                $adaptPrice['currency'] = $lang['data_formats+name+' . $expPrice[1]];
                $adaptPrice['value']    = $price_cel;
                $rlSmarty->assign_by_ref('defPrice', $adaptPrice);
                $rlSmarty->assign_by_ref('rate_range', $rate_range);
                $rlXajax->registerFunction(array(
                    'saveDesc',
                    $rlBooking,
                    'ajaxSaveDesc'
                ));
                $rlXajax->registerFunction(array(
                    'saveRateRange',
                    $rlBooking,
                    'ajaxSaveRateRange'
                ));
                $rlXajax->registerFunction(array(
                    'deleteRateRange',
                    $rlBooking,
                    'ajaxDeleteRateRange'
                ));
            } elseif ($_GET['view'] == 'binding_days') {
                $bcAStep[1]['name'] = $lang['booking_binding_days'];
                $massDays           = array(
                    'mon' => $lang['booking_monday'],
                    'tue' => $lang['booking_tuesday'],
                    'wed' => $lang['booking_wednesday'],
                    'thu' => $lang['booking_thursday'],
                    'fri' => $lang['booking_friday'],
                    'sat' => $lang['booking_saturday'],
                    'sun' => $lang['booking_sunday']
                );
                $binding_days       = $rlDb->fetch('*', array(
                    'Listing_ID' => $listing_id,
                    'Status' => 'active'
                ), null, null, 'booking_bindings', 'row');
                $rlSmarty->assign_by_ref('mass_days', $massDays);
                $rlSmarty->assign_by_ref('binding_days', $binding_days);
                $rlXajax->registerFunction(array(
                    'saveBindingDays',
                    $rlBooking,
                    'ajaxSaveBindingDays'
                ));
            }
        }
    } elseif ($_GET['mode'] == 'booking_colors') {
        $bcAStep[0]['name'] = $lang['booking_colors'];
        if (!isset($_POST['submit'])) {
            $colors_info                 = explode('|', $config['booking_colors']);
            $_POST['b']['xselect']       = $colors_info[0];
            $_POST['b']['available']     = $colors_info[1];
            $_POST['b']['booked']        = $colors_info[2];
            $_POST['b']['requested']     = $colors_info[3];
            $_POST['b']['closed']        = $colors_info[4];
            $_POST['b']['available_rgb'] = $colors_info[5];
            $_POST['b']['booked_rgb']    = $colors_info[6];
            $_POST['b']['requested_rgb'] = $colors_info[7];
        } else {
            $colors_form    = $_POST['b'];
            $available_rgb  = explode(',', $colors_form['available_rgb']);
            $booked_rgb     = explode(',', $colors_form['booked_rgb']);
            $requested_rgb  = explode(',', $colors_form['requested_rgb']);
            $closed_rgb     = explode(',', $colors_form['closed_rgb']);
            $booking_colors = $_POST['b']['xselect'] . '|' . $_POST['b']['available'] . '|' . $_POST['b']['booked'] . '|' . $_POST['b']['requested'] . '|' . $_POST['b']['closed'] . '|';
            $booking_colors .= $colors_form['available_rgb'] . '|' . $colors_form['booked_rgb'] . '|' . $colors_form['requested_rgb'] . '|' . $colors_form['closed_rgb'];
            $rlConfig->setConfig('booking_colors', $booking_colors);
            $reefless->loadClass('Booking', null, 'booking');
            $rlBooking->createGradient($available_rgb[0], $available_rgb[1], $available_rgb[2], $closed_rgb[0], $closed_rgb[1], $closed_rgb[2], 'available_closed');
            $rlBooking->createGradient($booked_rgb[0], $booked_rgb[1], $booked_rgb[2], $closed_rgb[0], $closed_rgb[1], $closed_rgb[2], 'booked_closed');
            $rlBooking->createGradient($requested_rgb[0], $requested_rgb[1], $requested_rgb[2], $closed_rgb[0], $closed_rgb[1], $closed_rgb[2], 'processed_closed');
            $rlBooking->createGradient($booked_rgb[0], $booked_rgb[1], $booked_rgb[2], $requested_rgb[0], $requested_rgb[1], $requested_rgb[2], 'booked_processed');
            $rlBooking->createGradient($available_rgb[0], $available_rgb[1], $available_rgb[2], $booked_rgb[0], $booked_rgb[1], $booked_rgb[2], 'booked');
            $rlBooking->createGradient($available_rgb[0], $available_rgb[1], $available_rgb[2], $requested_rgb[0], $requested_rgb[1], $requested_rgb[2], 'processed');
            $reefless->loadClass('Notice');
            $rlNotice->saveNotice("Colors saved!");
            $reefless->redirect(array(
                "controller" => $controller,
                "mode" => "booking_colors"
            ));
        }
    } elseif ($_GET['id']) {
        $reefless->loadClass('Common');
        $reefless->loadClass('Listings');
        $reefless->loadClass('Lang');
        $request_id         = (int) $_GET['id'];
        $bcAStep[0]['name'] = $lang['booking_page_details'];
        $fields             = $rlDb->fetch('*', array(
            'Status' => 'active'
        ), null, null, 'booking_fields');
        $fields             = $rlLang->replaceLangKeys($fields, 'booking_fields', array(
            'name'
        ));
        $sql                = "SELECT `T1`.`ID` AS `Req_ID`, `T3`.`ID` AS `Listing_ID`, `T1`.`Amount`, `T1`.`Status` AS `Stat`, `T2`.`Status` AS `Req_status`,`T3`.*,`T1`.`From`,`T1`.`To`,`T2`.* FROM `" . RL_DBPREFIX . "listings_book` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "booking_requests` AS `T2` ON `T1`.`ID`=`T2`.`Book_ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T3` ON `T1`.`Listing_ID`=`T3`.`ID` ";
        $sql .= "WHERE `T1`.`ID`='{$request_id}'";
        $requests = $rlDb->getRow($sql);
        if (!$requests) {
            $sError = true;
        }
        $requests['Stat']  = str_replace(array(
            'process',
            'booked',
            'refused'
        ), array(
            $lang['booking_processed'],
            $lang['booking_accepted'],
            $lang['booking_refused']
        ), $requests['Stat']);
        $requests['title'] = $rlListings->getListingTitle($requests['Category_ID'], $requests);
        foreach ($fields as $key => $field) {
            if (!empty($requests[$field['Key']])) {
                $requests['fields'][$key]          = $field;
                $requests['fields'][$key]['value'] = $requests[$field['Key']];
            }
        }
        $bread_crumbs[] = array(
            'title' => $requests['title'],
            'name' => $requests['title']
        );
        $rlSmarty->assign_by_ref('requests', $requests);
        $reefless->loadClass('Booking', null, 'booking');
        $rlBooking->getRateRange($requests['Listing_ID'], true);
    } elseif ($_GET['mode'] == 'booking_fields') {
        $bcAStep[0] = array(
            'name' => $lang['booking_fields_list'],
            'Controller' => 'booking',
            'Vars' => 'mode=booking_fields'
        );
        if ($_GET['action']) {
            $bcAStep[1] = array(
                'name' => $_GET['action'] == 'add' ? $lang['add_field'] : $lang['edit_field']
            );
        }
        $b_types = array(
            'text' => $lang['type_text'],
            'textarea' => $lang['type_textarea'],
            'number' => $lang['type_number'],
            'bool' => $lang['type_bool']
        );
        $rlSmarty->assign_by_ref('b_types', $b_types);
        $reefless->loadClass('BoookingFields', null, 'booking');
        if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
            if ($_GET['action'] == 'edit') {
                $e_key      = $rlValid->xSql($_GET['field']);
                $field_info = $rlDb->fetch('*', array(
                    'Key' => $e_key
                ), "AND `Status` <> 'trash'", null, 'booking_fields', 'row');
                if (empty($field_info)) {
                    $errors[] = $lang['notice_field_not_found'];
                } else {
                    if (!$_POST['fromPost']) {
                        $_POST['key']      = $e_key;
                        $_POST['required'] = $field_info['Required'];
                        $_POST['status']   = $field_info['Status'];
                        $_POST['type']     = $field_info['Type'];
                        $e_names           = $rlDb->fetch(array(
                            'Code',
                            'Value'
                        ), array(
                            'Key' => 'booking_fields+name+' . $e_key
                        ), "AND `Status` <> 'trash'", null, 'lang_keys');
                        foreach ($e_names as $nKey => $nVal) {
                            $_POST['name'][$e_names[$nKey]['Code']] = $e_names[$nKey]['Value'];
                        }
                        $descriptions = $rlDb->fetch(array(
                            'Code',
                            'Value'
                        ), array(
                            'Key' => 'booking_fields+description+' . $e_key
                        ), "AND `Status` <> 'trash'", null, 'lang_keys');
                        foreach ($descriptions as $pKey => $pVal) {
                            $_POST['description'][$descriptions[$pKey]['Code']] = $descriptions[$pKey]['Value'];
                        }
                        switch ($field_info['Type']) {
                            case 'text':
                                $e_default = $rlDb->fetch(array(
                                    'Code',
                                    'Value'
                                ), array(
                                    'Key' => 'booking_fields+default+' . $e_key
                                ), "AND `Status` <> 'trash'", null, 'lang_keys');
                                foreach ($e_default as $nKey => $nVal) {
                                    $_POST['text']['default'][$e_default[$nKey]['Code']] = $e_default[$nKey]['Value'];
                                }
                                $_POST['text']['condition'] = $field_info['Condition'];
                                $_POST['text']['maxlength'] = $field_info['Values'];
                                break;
                            case 'textarea':
                                $_POST['textarea']['maxlength'] = $field_info['Values'];
                                break;
                            case 'number':
                                $_POST['number']['maxlength'] = $field_info['Default'];
                                break;
                            case 'bool':
                                $_POST['bool']['default'] = $field_info['Default'];
                                break;
                        }
                        ;
                    }
                }
            }
            $allLangs = $rlLang->getLanguagesList('all');
            $rlSmarty->assign_by_ref('allLangs', $allLangs);
            if (isset($_POST['submit'])) {
                $errors = array();
                loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
                $f_type = $_POST['type'];
                $f_key  = $rlValid->xSql($_POST['key']);
                if (!utf8_is_ascii($f_key)) {
                    $f_key = utf8_to_ascii($f_key);
                }
                if (strlen($f_key) < 2) {
                    $errors[] = $lang['incorrect_phrase_key'];
                }
                if ($_GET['action'] == 'add') {
                    $exist_keys = $rlDb->getAll("SHOW FIELDS FROM `" . RL_DBPREFIX . "booking_requests`");
                    foreach ($exist_keys as $eKey => $eValue) {
                        if (strtolower($exist_keys[$eKey]['Field']) == strtolower($f_key)) {
                            $errors[] = str_replace('{key}', "<b>\"" . $f_key . "\"</b>", $lang['notice_field_exist']);
                        }
                    }
                }
                $f_key  = $_GET['action'] == 'add' ? $rlValid->str2key($f_key) : $rlValid->xSql($f_key);
                $f_name = $_POST['name'];
                foreach ($allLangs as $lkey => $lval) {
                    if (empty($f_name[$allLangs[$lkey]['Code']])) {
                        $errors[] = str_replace('{field}', "<b>" . $lang['name'] . "({$allLangs[$lkey]['name']})</b>", $lang['notice_field_empty']);
                    }
                    $f_names[$allLangs[$lkey]['Code']] = $f_name[$allLangs[$lkey]['Code']];
                }
                if (empty($f_type))
                    $errors[] = $lang['notice_type_empty'];
                $f_text_maxlength = $_POST['text']['maxlength'];
                if (empty($f_text_maxlength) || ($f_text_maxlength < 1 && $f_text_maxlength > 255))
                    $f_text_maxlength = 50;
                if (!empty($errors)) {
                    $rlSmarty->assign_by_ref('errors', $errors);
                } else {
                    $f_data['key']         = $f_key;
                    $f_data['names']       = $f_names;
                    $f_data['description'] = $_POST['description'];
                    $f_data['required']    = (int) $_POST['required'];
                    $f_data['status']      = $_POST['status'];
                    switch ($f_type) {
                        case 'text':
                            foreach ($allLangs as $lkey => $lval) {
                                if (!empty($_POST['text']['default'][$allLangs[$lkey]['Code']])) {
                                    $f_data['default'][$allLangs[$lkey]['Code']] = $rlValid->xSql($_POST['text']['default'][$allLangs[$lkey]['Code']]);
                                }
                            }
                            $f_data['maxlength'] = (int) $f_text_maxlength;
                            $f_data['condition'] = $_POST['text']['condition'];
                            break;
                        case 'textarea':
                            $f_data['maxlength'] = (int) $_POST['textarea']['maxlength'];
                            break;
                        case 'number':
                            $f_data['max'] = (int) $_POST['number']['maxlength'];
                            break;
                        case 'bool':
                            $f_data['default'] = (int) $_POST['bool']['default'];
                            break;
                    }
                    ;
                    if ($_GET['action'] == 'add') {
                        $action  = $rlBoookingFields->createBookingField($f_type, $f_data, $allLangs);
                        $message = $lang['field_added'];
                        $aUrl    = array(
                            "controller" => $controller,
                            "mode" => "booking_fields"
                        );
                    } elseif ($_GET['action'] == 'edit') {
                        $action  = $rlBoookingFields->editBookingField($f_type, $f_data, $allLangs);
                        $message = $lang['field_edited'];
                        $aUrl    = array(
                            "controller" => $controller,
                            "mode" => "booking_fields"
                        );
                    }
                    if ($action) {
                        $reefless->loadClass('Notice');
                        $rlNotice->saveNotice($message);
                        $reefless->redirect($aUrl);
                    }
                }
            }
        }
        $rlXajax->registerFunction(array(
            'deleteLField',
            $rlBoookingFields,
            'ajaxDeleteLField'
        ));
    } else {
        $bcAStep[0]['name'] = $lang['booking_requests'];
        $reefless->loadClass('Booking', null, 'booking');
        $rlXajax->registerFunction(array(
            'deleteRequest',
            $rlBooking,
            'ajaxDeleteRequestAP'
        ));
    }
}