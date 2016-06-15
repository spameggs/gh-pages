<?php
$reefless->loadClass('ExportImport', null, 'export_import');
$rlXajax->registerFunction(array(
    'fetchOptions',
    $rlExportImport,
    'ajaxFetchOptions'
));
$reefless->loadClass('Account');
$reefless->loadClass('Listings', 'admin');
$rlXajax->registerFunction(array(
    'massActions',
    $rlListings,
    'ajaxMassActions'
));
$rlXajax->registerFunction(array(
    'deleteListing',
    $rlListings,
    'ajaxDeleteListing'
));
if ($rlDb->getRow("SELECT `Key` FROM `" . RL_DBPREFIX . "plugins` WHERE `Status` = 'active' AND `Key` = 'multiField'")) {
    $GLOBALS['reefless']->loadClass('MultiField', null, 'multiField');
    $GLOBALS['rlXajax']->registerFunction(array(
        'mfGetNext',
        $GLOBALS['rlMultiField'],
        'ajaxGetNext'
    ));
    $GLOBALS['rlXajax']->registerFunction(array(
        'mfBuild',
        $GLOBALS['rlMultiField'],
        'ajaxBuild'
    ));
    $sql = "SELECT * FROM `" . RL_DBPREFIX . "multi_formats` WHERE 1 ";
    global $multi_formats;
    $mf_tmp = $rlDb->getAll($sql);
    foreach ($mf_tmp as $key => $item) {
        $multi_formats[$item['Key']] = $item;
    }
    $rlSmarty->assign('multi_formats', $multi_formats);
}
$max_file_size = str_replace('M', '', ini_get('upload_max_filesize'));
$rlSmarty->assign_by_ref('max_file_size', $max_file_size);
if (!$_REQUEST['xjxfun']) {
    $allowed_types = array(
        'application/vnd.ms-excel'
    );
    if (isset($_GET['reset'])) {
        unlink($_SESSION['iel_data']['file']);
        $reefless->deleteDirectory($_SESSION['iel_data']['archive_dir']);
        unset($_SESSION['iel_data'], $_SESSION['eil_data']);
    }
    if ($_POST['action'] == 'import_file' && $_FILES['file']['name']) {
        if (!in_array($_FILES['file']['type'], $allowed_types)) {
            $errors[] = $lang['eil_import_wrong_file_format'];
        }
        if ($_FILES['archive']['tmp_name'] && !(bool) preg_match('/\.zip$/', $_FILES['archive']['name'])) {
            $errors[] = $lang['eil_import_wrong_archive_format'];
        }
        if (!$errors) {
            $ext       = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $dynamic   = time() . mt_rand();
            $file_name = $_SESSION['sessAdmin']['user'] . '_import_' . $dynamic . '.' . $ext;
            $file      = RL_UPLOAD . $file_name;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $_SESSION['iel_data'] = array(
                    'file' => $file,
                    'file_name' => $file_name
                );
                chmod($file, 0644);
                if ($_FILES['archive']['tmp_name']) {
                    $ext          = pathinfo($_FILES['archive']['name'], PATHINFO_EXTENSION);
                    $archive_name = $_SESSION['sessAdmin']['user'] . '_import_zip_' . $dynamic . '.' . $ext;
                    $archive      = RL_UPLOAD . $archive_name;
                    $archive_dir  = RL_UPLOAD . $_SESSION['sessAdmin']['user'] . '_import_zip_' . $dynamic;
                    $reefless->rlMkdir($archive_dir);
                    if (move_uploaded_file($_FILES['archive']['tmp_name'], $archive)) {
                        $_SESSION['iel_data']['archive_dir'] = $archive_dir;
                        chmod($archive, 0644);
                        require_once(RL_CLASSES . 'dUnzip2.class.php');
                        $unzip = new dUnzip2($archive);
                        $unzip->unzipAll($archive_dir);
                        $unzip->__destroy();
                        unset($unzip);
                        unlink($archive);
                    }
                }
                $reefless->redirect(array(
                    'controller' => $controller,
                    'action' => 'import'
                ));
            } else {
                $errors[] = $lang['eil_import_unable_to_upload'];
            }
        }
    }
    if (in_array($_GET['action'], array(
        'import',
        'importing'
    ))) {
        if ($_POST['import_listing_type'] || $_SESSION['iel_data']['post']['import_listing_type']) {
            $lt = $_POST['import_listing_type'] ? $_POST['import_listing_type'] : $_SESSION['iel_data']['post']['import_listing_type'];
            $reefless->loadClass('Categories');
            $categories_tmp = $rlCategories->getCatTitles($lt);
            foreach ($categories_tmp as $category) {
                $categories[$category['ID']] = $category;
            }
            unset($categories_tmp, $category);
            $rlSmarty->assign_by_ref('categories', $categories);
        }
        $reefless->loadClass('Plan');
        $plans_tmp = $rlPlan->getPlans(array(
            'listing',
            'package'
        ));
        foreach ($plans_tmp as $plan) {
            $plans[$plan['ID']] = $plan;
        }
        unset($plans_tmp);
        $rlSmarty->assign_by_ref('plans', $plans);
        $per_run = array(
            2,
            5,
            10,
            20,
            40,
            50,
            100
        );
        $rlSmarty->assign_by_ref('per_run', $per_run);
        unset($_SESSION['eil_data']);
    }
    if ($_SESSION['iel_data']['file'] && is_readable($_SESSION['iel_data']['file']) && $_GET['action'] == 'import') {
        $ei_mode = 'import_form';
        $bcAStep = $lang['eil_import_form'];
        $rlDb->setTable('listing_fields');
        $listing_fields_tmp = $rlDb->fetch(array(
            'Key',
            'Type',
            'Default'
        ), null, "WHERE `Status` <> 'trash' AND (`Readonly` = '0' OR `Key` LIKE '%level%') AND `Type` <> 'image' AND `Type` <> 'accept' AND `Type` <> 'file' AND `Status` = 'active'");
        $listing_fields_tmp = $rlLang->replaceLangKeys($listing_fields_tmp, 'listing_fields', array(
            'name'
        ), RL_LANG_CODE, 'admin');
        foreach ($listing_fields_tmp as $tmp_listign_field) {
            $listing_fields[$tmp_listign_field['Key']] = $tmp_listign_field;
        }
        unset($listing_fields_tmp);
        $rlSmarty->assign_by_ref('listing_fields', $listing_fields);
        $system_fields = array(
            array(
                'Key' => 'Category_ID',
                'name' => $lang['eil_category_name']
            ),
            array(
                'Key' => 'Subcategory_ID',
                'name' => $lang['eil_subcategory_name']
            ),
            array(
                'Key' => 'Account_ID',
                'name' => $lang['eil_owner_account']
            ),
            array(
                'Key' => 'Main_photo_url',
                'name' => $lang['eil_pictures_by_url']
            ),
            array(
                'Key' => 'Main_photo_zip',
                'name' => $lang['eil_pictures_from_zip']
            ),
            array(
                'Key' => 'Youtube_video',
                'name' => $lang['eil_youtube_video_field']
            )
        );
        $rlSmarty->assign_by_ref('system_fields', $system_fields);
        if ($_POST['from_post'] && $_POST['action'] == 'import_form') {
            if (!$_POST['import_listing_type']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['listing_type'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'import_listing_type';
            }
            if (!$_POST['import_category_id']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['eil_default_category'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'import_category_id';
            }
            if (!$_POST['import_status']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['eil_default_status'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'import_status';
            }
            if (!$_POST['import_account_id']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['eil_default_owner'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'import_account_id';
            }
            if (!$_POST['import_plan_id']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['eil_default_plan'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'import_plan_id';
            }
            if (!$_POST['rows']) {
                $errors[] = $lang['eil_no_listings'];
            }
            if (!$_POST['cols']) {
                $errors[] = $lang['eil_no_fields_checked'];
            }
            if (!is_numeric(array_search('Category_ID', $_POST['field'])) && is_numeric(array_search('Subcategory_ID', $_POST['field']))) {
                $error_fields[] = 'Subcategory_ID';
                $errors[]       = $lang['eil_subcategory_using_fail'];
            }
            $selected_fields = 0;
            foreach ($_POST['field'] as $post_field) {
                if (!empty($post_field)) {
                    $selected_fields++;
                }
            }
            if ($selected_fields < 2) {
                $errors[] = $lang['eil_no_fields_selected'];
            }
            if (count(array_filter(array_unique($_POST['field']))) != count(array_filter($_POST['field']))) {
                foreach (array_diff_assoc($_POST['field'], array_unique($_POST['field'])) as $duplicate) {
                    $duplicate_fields .= $listing_fields[$duplicate]['name'] . ', ';
                }
                $duplicate_fields = rtrim($duplicate_fields, ', ');
                $errors[]         = $lang['eil_duplicate_fields_selected'] . ' (<b>' . $duplicate_fields . '</b>)';
            }
            if (!$errors) {
                unset($_POST['action']);
                $_SESSION['iel_data']['post']             = $_POST;
                $_SESSION['iel_data']['post']['rows_tmp'] = $_POST['rows'];
                $reefless->redirect(array(
                    'controller' => $controller,
                    'action' => 'importing'
                ));
            }
        } else {
            if ($_SESSION['iel_data']['post']) {
                $_POST = $_SESSION['iel_data']['post'];
                unset($_SESSION['iel_data']['post']);
            } else {
                switch (pathinfo($_SESSION['iel_data']['file'], PATHINFO_EXTENSION)) {
                    case 'csv':
                        $handle = fopen($_SESSION['iel_data']['file'], 'r');
                        while (($data = fgetcsv($handle)) !== false) {
                            $source[] = $data;
                        }
                        break;
                    case 'xls':
                        require_once(RL_PLUGINS . 'export_import' . RL_DS . 'phpExcel' . RL_DS . 'PHPExcel' . RL_DS . 'IOFactory.php');
                        $objPHPExcel = PHPExcel_IOFactory::load($_SESSION['iel_data']['file']);
                        $source      = $objPHPExcel->getActiveSheet()->toArray('', true, true, false);
                        break;
                }
                if ($source) {
                    foreach ($source[0] as $key => $col) {
                        $_POST['cols'][$key] = true;
                    }
                    foreach ($source as $key => $row) {
                        foreach ($row as $index => $cel) {
                            $_POST['data'][$key][$index] = $cel;
                        }
                    }
                } else {
                    unset($ei_mode);
                    $errors[] = $lang['eil_import_no_content'];
                }
            }
        }
    } elseif ($_SESSION['iel_data']['post'] && $_GET['action'] == 'importing') {
        $ei_mode        = 'import_importing';
        $bcAStep        = $lang['eil_preview'];
        $import_details = array(
            array(
                'name' => $lang['eil_total_listings'],
                'value' => count($_SESSION['iel_data']['post']['rows_tmp'])
            ),
            array(
                'name' => $lang['eil_per_run'],
                'value' => $_SESSION['iel_data']['post']['import_per_run']
            ),
            array(
                'name' => $lang['listing_type'],
                'value' => $rlListingTypes->types[$_SESSION['iel_data']['post']['import_listing_type']]['name']
            ),
            array(
                'name' => $lang['eil_default_category'],
                'value' => $lang[$categories[$_SESSION['iel_data']['post']['import_category_id']]['pName']]
            ),
            array(
                'name' => $lang['eil_default_status'],
                'value' => $lang[$_SESSION['iel_data']['post']['import_status']]
            ),
            array(
                'name' => $lang['eil_default_owner'],
                'value' => '<a title="' . $lang['view_account'] . '" target="_blank" href="' . RL_URL_HOME . ADMIN . '/index.php?controller=accounts&amp;action=view&amp;userid=' . $_SESSION['iel_data']['post']['import_account_id'] . '">' . $_SESSION['iel_data']['post']['import_account_id_tmp'] . '</a>'
            ),
            array(
                'name' => $lang['eil_default_plan'],
                'value' => $plans[$_SESSION['iel_data']['post']['import_plan_id']]['name']
            ),
            array(
                'name' => $lang['eil_paid'],
                'value' => $_SESSION['iel_data']['post']['import_paid'] ? $lang['yes'] : $lang['no']
            )
        );
        $rlSmarty->assign_by_ref('import_details', $import_details);
    }
    if ($_GET['action'] == 'export') {
        $reefless->loadClass('Search');
        if ($_SESSION['eil_data']['post'] && !$_POST['from_post']) {
            $_POST = $_SESSION['eil_data']['post'];
        }
        $lt = $_POST['export_listing_type'];
        if ($lt) {
            $reefless->loadClass('Categories');
            $categories_tmp = $rlCategories->getCatTitles($lt);
            foreach ($categories_tmp as $category) {
                $categories[$category['ID']] = $category;
            }
            unset($categories_tmp, $category);
            $rlSmarty->assign_by_ref('categories', $categories);
            $fields = $rlSearch->buildSearch($lt . '_quick', $lt);
            $rlSearch->getFields($lt . '_quick', $lt);
            $rlSmarty->assign_by_ref('fields', $fields);
        }
        if ($_POST['from_post'] && $_POST['action'] == 'export_condition') {
            if (!$_POST['export_format']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['eil_file_format'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'export_format';
            }
            if (!$lt) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['listing_type'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'export_listing_type';
            }
            if (!$_POST['export_category_id'] && !$rlListingTypes->types[$lt]['Cat_general_cat']) {
                $errors[]       = str_replace('{field}', "<b>\"" . $lang['category'] . "\"</b>", $lang['notice_select_empty']);
                $error_fields[] = 'export_category_id';
            }
            if ($_POST['export_category_id'] && $lt) {
                $form_id = $_POST['export_category_id'] ? $_POST['export_category_id'] : $rlListingTypes->types[$lt]['Cat_general_cat'];
                if (!$rlCategories->buildListingForm($form_id, $rlListingTypes->types[$lt])) {
                    $errors[] = $lang['eil_no_form'];
                }
                if (!$rlSearch->search($_POST['f'], $lt, 0, 1)) {
                    $errors[] = $lang['eil_no_listings_found'];
                }
            }
            if (!$errors) {
                unset($_POST['action'], $_POST['from_post']);
                $_SESSION['eil_data']['post'] = $_POST;
                $reefless->redirect(array(
                    'controller' => $controller,
                    'action' => 'export_table'
                ));
            }
        } else {
            unset($_SESSION['eil_data']);
        }
        $ei_mode = 'export';
        $bcAStep = $lang['eil_export'];
        $rlSmarty->assign('cpTitle', $lang['eil_export_criteria']);
        unlink($_SESSION['iel_data']['file']);
        $reefless->deleteDirectory($_SESSION['iel_data']['archive_dir']);
        unset($_SESSION['iel_data']);
    } elseif ($_GET['action'] == 'export_table') {
        $reefless->loadClass('Search');
        $ei_mode     = 'export_table';
        $bcAStep     = $lang['eil_export'];
        $lt          = $_SESSION['eil_data']['post']['export_listing_type'];
        $category_id = $_SESSION['eil_data']['post']['export_category_id'];
        $rlSearch->getFields($lt . '_quick', $lt);
        $form_id    = $category_id ? $category_id : $rlListingTypes->types[$lt]['Cat_general_cat'];
        $form       = $rlCategories->buildListingForm($form_id, $rlListingTypes->types[$lt]);
        $fields     = array();
        $sys_fields = array(
            'Cat_key' => array(
                'pName' => 'category',
                'Key' => 'Cat_key'
            ),
            'Account_username' => array(
                'pName' => 'username',
                'Key' => 'Account_username'
            ),
            'Picture_URLs' => array(
                'pName' => 'eil_pictures_urls',
                'Key' => 'Picture_URLs'
            ),
            'Account_email' => array(
                'pName' => 'mail',
                'Key' => 'Account_email'
            )
        );
        $fields     = array_merge($sys_fields, $fields);
        foreach ($form as $group) {
            $fields = array_merge($fields, $group['Fields']);
        }
        $rlSmarty->assign('fields', $fields);
        $listings       = $rlSearch->search($_SESSION['eil_data']['post']['f'], $lt, 0, 5000);
        $listings_count = count($listings);
        $rlSmarty->assign('cpTitle', $lang['eil_export_listings'] . " ({$listings_count})");
        foreach ($listings as &$listing) {
            foreach ($listing as $field_key => &$field) {
                if (array_key_exists($field_key, $sys_fields)) {
                    switch ($field_key) {
                        case 'Cat_key':
                            $field = $lang['categories+name+' . $field];
                            break;
                        case 'Picture_URLs':
                            if ($listing['Photos_count']) {
                                $field = '';
                                if ($pictures = $rlDb->fetch(array(
                                    'Photo'
                                ), array(
                                    'Listing_ID' => $listing['ID'],
                                    'Status' => 'active'
                                ), "ORDER BY `Type` DESC, `Position` ASC", null, 'listing_photos')) {
                                    foreach ($pictures as $picture) {
                                        $field .= RL_FILES_URL . $picture['Photo'] . ', ';
                                    }
                                    $field = trim($field, ', ');
                                }
                            }
                            break;
                    }
                } else {
                    if ($fields[$field_key]) {
                        $field = $rlCommon->adaptValue($fields[$field_key], $field);
                    }
                }
            }
        }
        $rlSmarty->assign_by_ref('listings', $listings);
        if ($_POST['action'] == 'export_table') {
            if (!$_POST['rows']) {
                $errors[] = $lang['eil_no_listings_to_export'];
            }
            if (!$_POST['cols']) {
                $errors[] = $lang['eil_no_fields_checked'];
            }
            if (!$errors) {
                $cat_key   = $category_id ? str_replace('_', '-', $rlDb->getOne('Key', "`ID` = '{$category_id}'", 'categories')) : $rlListingTypes->types[$lt]['name'];
                $file_name = 'export-' . $cat_key . '-' . date('M\.j\-Y');
                switch ($_SESSION['eil_data']['post']['export_format']) {
                    case 'xls':
                        require_once(RL_PLUGINS . 'export_import' . RL_DS . 'phpExcel' . RL_DS . 'PHPExcel.php');
                        loadUTF8functions('ascii', 'utf8_to_ascii');
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->getProperties()->setCreator($config['owner_name'])->setLastModifiedBy($config['owner_name'])->setTitle($cat_key)->setSubject($cat_key)->setDescription()->setKeywords()->setCategory();
                        $objPHPExcel->setActiveSheetIndex(0);
                        $row = 1;
                        $col = $index = 0;
                        foreach ($fields as $field) {
                            if ($_POST['cols'][$index]) {
                                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $lang[$field['pName']]);
                                $col++;
                            }
                            $index++;
                        }
                        $row++;
                        $styleArray = array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'startcolor' => array(
                                    'argb' => 'FFd6d6d6'
                                )
                            )
                        );
                        $last_col   = $objPHPExcel->getActiveSheet()->getHighestColumn();
                        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $last_col . '1')->applyFromArray($styleArray);
                        foreach ($listings as $l_index => &$listing) {
                            $col = $index = 0;
                            if ($_POST['rows'][$l_index]) {
                                foreach ($fields as $field) {
                                    if ($_POST['cols'][$index]) {
                                        $set_value = strip_tags($listing[$field['Key']]);
                                        if ((bool) preg_match("/" . PHP_EOL . "/", $set_value)) {
                                            $set_value = preg_replace("/" . PHP_EOL . "/", "\n", $set_value);
                                            $set_value = str_replace(array(
                                                "<br>",
                                                "<br />",
                                                "<br/>"
                                            ), "\n", $set_value);
                                            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getAlignment()->setWrapText(true);
                                        }
                                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $set_value);
                                        $col++;
                                    }
                                    $index++;
                                }
                                $row++;
                            }
                        }
                        header("Content-Disposition: attachment; filename={$file_name}.xls");
                        header("Content-Type: application/vnd.ms-excel;");
                        header("Cache-Control: max-age=0");
                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save('php://output');
                        exit;
                        break;
                    case 'csv':
                        header("Content-Disposition: attachment; filename={$file_name}.csv; charset=utf-8");
                        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
                        $index = 0;
                        foreach ($fields as $field) {
                            if ($_POST['cols'][$index]) {
                                $csv .= $lang[$field['pName']] . ',';
                            }
                            $index++;
                        }
                        $csv = rtrim($csv, ',') . PHP_EOL;
                        foreach ($listings as $l_index => &$listing) {
                            $index = 0;
                            if ($_POST['rows'][$l_index]) {
                                foreach ($fields as $field) {
                                    if ($_POST['cols'][$index]) {
                                        $set_value = strip_tags($listing[$field['Key']]);
                                        $set_value = '"' . str_replace('"', '""', $set_value) . '"';
                                        $csv .= $set_value . ',';
                                    }
                                    $index++;
                                }
                                $csv = rtrim($csv, ',') . PHP_EOL;
                            }
                        }
                        echo $csv;
                        exit;
                        break;
                }
            }
        }
    }
    if (!$ei_mode) {
        $rlSmarty->assign('cpTitle', $lang['eil_import']);
        $ei_mode = 'import_upload';
    }
    $rlSmarty->assign_by_ref('ei_mode', $ei_mode);
}