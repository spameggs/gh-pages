<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $id         = $rlValid->xSql($_GET['id']);
        $key        = $rlValid->xSql($_GET['key']);
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'banner_plans');
        exit;
    }
    $limit   = (int) $_GET['limit'];
    $start   = (int) $_GET['start'];
    $sort    = $rlValid->xSql($_GET['sort']);
    $sortDir = $rlValid->xSql($_GET['dir']);
    $sql     = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.*, `T2`.`Value` AS `name` ";
    $sql .= "FROM `" . RL_DBPREFIX . "banner_plans` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('banner_plans+name+',`T1`.`Key`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
    $sql .= "WHERE `T1`.`Status` <> 'trash' ";
    if ($sort) {
        switch ($sort) {
            case 'name':
                $sortField = "`T2`.`Value`";
                break;
            case 'Type_name':
                $sortField = "`T1`.`Type`";
                break;
            default:
                $sortField = "`T1`.`{$sort}`";
                break;
        }
        $sql .= "ORDER BY {$sortField} {$sortDir} ";
    }
    $sql .= "LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    foreach ($data as $key => $value) {
        $data[$key]['Admin']  = $data[$key]['Admin'] ? $GLOBALS['lang']['yes'] : $GLOBALS['lang']['no'];
        $data[$key]['Status'] = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $out['data']  = $data;
    $out['count'] = (int) $count['count'];
    $reefless->loadClass('Json');
    echo $rlJson->encode($out);
    exit;
}
if (isset($_GET['action'])) {
    $bcAStep[0] = array(
        'name' => $lang['banners_listOfPlans'],
        'Controller' => 'banners',
        'Vars' => 'module=banner_plans'
    );
    $bcAStep[1] = array(
        'name' => $_GET['action'] == 'add' ? $lang['banners_addPlan'] : $lang['banners_editPlan']
    );
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $reefless->loadClass('Account');
        $account_types = $rlAccount->getAccountTypes('visitor');
        $rlSmarty->assign_by_ref('account_types', $account_types);
        $sql = "SELECT `T1`.`Key`, `T2`.`Value` AS `name` FROM `" . RL_DBPREFIX . "blocks` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "lang_keys` AS `T2` ON CONCAT('blocks+name+',`T1`.`Key`) = `T2`.`Key` AND `T2`.`Code` = '" . RL_LANG_CODE . "' ";
        $sql .= "WHERE `T1`.`Status` = 'active' AND `T1`.`Plugin` = 'banners' GROUP BY `T1`.`ID`";
        $boxes = $rlDb->getAll($sql);
        $rlSmarty->assign_by_ref('boxes', $boxes);
        $countries = $rlBanners->getCountriesList();
        $rlSmarty->assign_by_ref('countries', $countries);
        if (isset($_GET['plan'])) {
            $planId   = (int) $_GET['plan'];
            $planInfo = $rlDb->fetch('*', array(
                'ID' => $planId
            ), "AND `Status` <> 'trash'", null, 'banner_plans', 'row');
            $rlSmarty->assign_by_ref('plan', $planInfo);
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['color']                 = $planInfo['Color'];
            $_POST['price']                 = $planInfo['Price'];
            $_POST['status']                = $planInfo['Status'];
            $_POST['banners_admin']         = $planInfo['Admin'];
            $_POST['account_type']          = explode(',', $planInfo['Allow_for']);
            $_POST['countries']             = explode(',', $planInfo['Country']);
            $_POST['banners_geo']           = (int) $planInfo['Geo'];
            $_POST['boxes']                 = explode(',', $planInfo['Boxes']);
            $_POST['banner_type']           = explode(',', $planInfo['Types']);
            $_POST['banners_live_for_type'] = $planInfo['Plan_Type'];
            $_POST[$planInfo['Plan_Type']]  = $planInfo['Period'];
            if (count($allLangs) > 1) {
                $names = $rlDb->fetch(array(
                    'Code',
                    'Value'
                ), array(
                    'Key' => 'banner_plans+name+' . $planInfo['Key']
                ), "AND `Status` <> 'trash'", null, 'lang_keys');
                foreach ($names as $pKey => $pVal) {
                    $_POST['name'][$names[$pKey]['Code']] = $names[$pKey]['Value'];
                }
                $descriptions = $rlDb->fetch(array(
                    'Code',
                    'Value'
                ), array(
                    'Key' => 'banner_plans+des+' . $planInfo['Key']
                ), "AND `Status` <> 'trash'", null, 'lang_keys');
                foreach ($descriptions as $pKey => $pVal) {
                    $_POST['description'][$descriptions[$pKey]['Code']] = $descriptions[$pKey]['Value'];
                }
                unset($names, $descriptions);
            } else {
                $_POST['name'][$config['lang']]        = $lang['banner_plans+name+' . $planInfo['Key']];
                $_POST['description'][$config['lang']] = $lang['banner_plans+des+' . $planInfo['Key']];
            }
        }
        if (isset($_POST['submit'])) {
            $reefless->loadClass('Actions');
            $errors        = $error_fields = array();
            $f_name        = $_POST['name'];
            $f_description = $_POST['description'];
            if (empty($f_name[$config['lang']])) {
                $langName = count($allLangs) > 1 ? "{$lang['name']}({$allLangs[$config['lang']]['name']})" : $lang['name'];
                array_push($errors, str_replace('{field}', "<b>{$langName}</b>", $lang['notice_field_empty']));
                array_push($error_fields, "name[{$config['lang']}]");
            }
            $f_banners_live_for_type = $_POST['banners_live_for_type'];
            $f_period                = (int) $_POST['period'];
            if ($f_banners_live_for_type == 'period' && $f_period < 0) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerLiveFor']}\"</b>", $lang['notice_field_empty']));
                array_push($error_fields, 'period');
            }
            $f_views = (int) $_POST['views'];
            if ($f_banners_live_for_type == 'views' && $f_views < 0) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerLiveFor']}\"</b>", $lang['notice_field_empty']));
                array_push($error_fields, 'views');
            }
            $f_countries = $_POST['countries'];
            if (in_array($_POST['banners_geo'], array(
                0,
                2
            )) && empty($f_countries)) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_showCountries']}\"</b>", $lang['notice_field_empty']));
                array_push($error_fields, 'countries[]');
            }
            $f_boxes = $_POST['boxes'];
            if (empty($f_boxes)) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_boxes']}\"</b>", $lang['notice_field_empty']));
                array_push($error_fields, 'boxes');
            }
            $f_banner_type = $_POST['banner_type'];
            if (empty($f_banner_type)) {
                array_push($errors, str_replace('{field}', "<b>\"{$lang['banners_bannerType']}\"</b>", $lang['notice_field_empty']));
                array_push($error_fields, 'banner_type');
            }
            $f_account_type = $_POST['account_type'];
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $defName  = !empty($f_name['en']) ? $f_name['en'] : $f_name[$config['lang']];
                    $f_key    = $rlBanners->uniqKeyByName($defName, 'banner_plans', 'bp_');
                    $position = $rlDb->getRow("SELECT MAX(`Position`) AS `max` FROM `" . RL_DBPREFIX . "banner_plans`");
                    $data     = array(
                        'Key' => $f_key,
                        'Admin' => (int) $_POST['banners_admin'],
                        'Allow_for' => implode(',', $f_account_type),
                        'Country' => implode(',', $f_countries),
                        'Geo' => (int) $_POST['banners_geo'],
                        'Boxes' => implode(',', $f_boxes),
                        'Types' => implode(',', $f_banner_type),
                        'Color' => $_POST['color'],
                        'Price' => (double) $_POST['price'],
                        'Plan_Type' => $f_banners_live_for_type,
                        'Period' => $f_banners_live_for_type == 'period' ? (int) $_POST['period'] : (int) $_POST['views'],
                        'Status' => $_POST['status'],
                        'Position' => $position['max'] + 1
                    );
                    if ($action = $rlActions->insertOne($data, 'banner_plans')) {
                        foreach ($allLangs as $key => $value) {
                            $lang_keys[] = array(
                                'Code' => $allLangs[$key]['Code'],
                                'Module' => 'common',
                                'Status' => 'active',
                                'Key' => 'banner_plans+name+' . $f_key,
                                'Value' => !empty($f_name[$allLangs[$key]['Code']]) ? $f_name[$allLangs[$key]['Code']] : $f_name[$config['lang']],
                                'Plugin' => 'banners'
                            );
                            if (!empty($f_description[$allLangs[$key]['Code']])) {
                                $lang_keys[] = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Status' => 'active',
                                    'Key' => 'banner_plans+des+' . $f_key,
                                    'Value' => $f_description[$allLangs[$key]['Code']],
                                    'Plugin' => 'banners'
                                );
                            }
                        }
                        $rlActions->insert($lang_keys, 'lang_keys');
                        $message = $lang['plan_added'];
                        $aUrl    = array(
                            "controller" => $controller,
                            'module' => 'banner_plans'
                        );
                    } else {
                        trigger_error("Can't add new banner plan (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new banner plan (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $f_key  = $planInfo['Key'];
                    $update = array(
                        'fields' => array(
                            'Admin' => (int) $_POST['banners_admin'],
                            'Allow_for' => implode(',', $f_account_type),
                            'Country' => implode(',', $f_countries),
                            'Geo' => (int) $_POST['banners_geo'],
                            'Boxes' => implode(',', $f_boxes),
                            'Types' => implode(',', $f_banner_type),
                            'Color' => $_POST['color'],
                            'Price' => (double) $_POST['price'],
                            'Plan_Type' => $f_banners_live_for_type,
                            'Period' => $f_banners_live_for_type == 'period' ? (int) $_POST['period'] : (int) $_POST['views'],
                            'Status' => $_POST['status']
                        ),
                        'where' => array(
                            'Key' => $f_key
                        )
                    );
                    if ($action = $rlActions->updateOne($update, 'banner_plans')) {
                        foreach ($allLangs as $key => $value) {
                            if ($rlDb->getOne('ID', "`Key` = 'banner_plans+name+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'", 'lang_keys')) {
                                $update_phrases = array(
                                    'fields' => array(
                                        'Value' => !empty($f_name[$allLangs[$key]['Code']]) ? $f_name[$allLangs[$key]['Code']] : $f_name[$config['lang']]
                                    ),
                                    'where' => array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Key' => 'banner_plans+name+' . $f_key
                                    )
                                );
                                $rlActions->updateOne($update_phrases, 'lang_keys');
                            } else {
                                $insert_phrases = array(
                                    'Code' => $allLangs[$key]['Code'],
                                    'Module' => 'common',
                                    'Key' => 'banner_plans+name+' . $f_key,
                                    'Value' => !empty($f_name[$allLangs[$key]['Code']]) ? $f_name[$allLangs[$key]['Code']] : $f_name[$config['lang']],
                                    'Plugin' => 'banners'
                                );
                                $rlActions->insertOne($insert_phrases, 'lang_keys');
                            }
                            $c_query = $rlDb->fetch(array(
                                'ID'
                            ), array(
                                'Key' => 'banner_plans+des+' . $f_key,
                                'Code' => $allLangs[$key]['Code']
                            ), null, null, 'lang_keys', 'row');
                            if (!empty($c_query)) {
                                if (!empty($f_description[$allLangs[$key]['Code']])) {
                                    $lang_keys_des[] = array(
                                        'where' => array(
                                            'Code' => $allLangs[$key]['Code'],
                                            'Key' => 'banner_plans+des+' . $f_key
                                        ),
                                        'fields' => array(
                                            'Value' => !empty($f_description[$allLangs[$key]['Code']]) ? $f_description[$allLangs[$key]['Code']] : $f_description[$config['lang']]
                                        )
                                    );
                                } else {
                                    $rlDb->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'banner_plans+des+{$f_key}' AND `Code` = '{$allLangs[$key]['Code']}'");
                                }
                            } else {
                                if (!empty($f_description[$allLangs[$key]['Code']])) {
                                    $lang_keys_des = array(
                                        'Code' => $allLangs[$key]['Code'],
                                        'Module' => 'common',
                                        'Status' => 'active',
                                        'Key' => 'banner_plans+des+' . $f_key,
                                        'Value' => $f_description[$allLangs[$key]['Code']],
                                        'Plugin' => 'banners'
                                    );
                                    $rlActions->insertOne($lang_keys_des, 'lang_keys');
                                }
                            }
                        }
                        $rlActions->update($lang_keys_des, 'lang_keys');
                    }
                    $message = $lang['plan_edited'];
                    $aUrl    = array(
                        "controller" => $controller,
                        'module' => 'banner_plans'
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
} else {
    $bcAStep = $lang['banners_listOfPlans'];
    $rlXajax->registerFunction(array(
        'prepareDeleting',
        $rlBanners,
        'ajaxPrepareDeleting'
    ));
    $rlXajax->registerFunction(array(
        'deletePlan',
        $rlBanners,
        'ajaxDeletePlan'
    ));
}