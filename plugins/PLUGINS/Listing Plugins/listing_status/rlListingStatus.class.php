<?php
class rlListingStatus extends reefless
{
    var $rlLang;
    var $rlValid;
    function rlListingStatus()
    {
        global $rlLang, $rlValid;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
    }
    function installWatermark()
    {
        $allLangs    = $GLOBALS['languages'];
        $data        = array(
            'Key' => 'sold',
            'Type' => 'listings',
            'Days' => '3',
            'Count' => '6',
            'Delete' => 'disabled',
            'Used_block' => '1',
            'Status' => 'active'
        );
        $folder_name = RL_FILES . "watermark";
        $this->rlMkdir($folder_name);
        if ($GLOBALS['rlActions']->insertOne($data, 'listing_status')) {
            $file      = RL_PLUGINS . 'listing_status' . RL_DS . 'sold.png';
            $fileLarge = RL_PLUGINS . 'listing_status' . RL_DS . 'sold_large.png';
            $field     = '';
            $fieldL    = '';
            foreach ($allLangs as $lkey => $lval) {
                $newname      = 'sold_' . $lkey . '.png';
                $newnameLarge = 'sold_large_' . $lkey . '.png';
                $newfile      = $folder_name . RL_DS . $newname;
                $newfileLarge = $folder_name . RL_DS . $newnameLarge;
                if (copy($file, $newfile) && copy($fileLarge, $newfileLarge)) {
                    $field  = 'watermark_' . $lkey;
                    $fieldL = 'watermarkLarge_' . $lkey;
                    $this->query("ALTER TABLE `" . RL_DBPREFIX . "listing_status` ADD `{$field}` VARCHAR( 50 ) NOT NULL AFTER `Key`, ADD `{$fieldL}` VARCHAR( 50 ) NOT NULL AFTER `Key`");
                    $updat_watermark['fields'][$field]  = $newname;
                    $updat_watermark['fields'][$fieldL] = $newnameLarge;
                }
            }
            $updat_watermark['where']['ID'] = '1';
            $GLOBALS['rlActions']->updateOne($updat_watermark, 'listing_status');
        }
        return $listings;
    }
    function ajaxDeleteWatermark($key = false, $code = false, $name = false, $large = false)
    {
        global $_response;
        $field = $large == 1 ? 'watermarkLarge_' . $code : 'watermark_' . $code;
        $this->query("UPDATE `" . RL_DBPREFIX . "listing_status` SET `{$field}` = '' WHERE `Key` = '{$key}'");
        $file = RL_FILES . "watermark" . RL_DS . $name;
        unlink($file);
        $GLOBALS['rlSmarty']->assign_by_ref('code', $code);
        if ($large) {
            $GLOBALS['rlSmarty']->assign_by_ref('large', $large);
        }
        $tpl = RL_PLUGINS . 'listing_status' . RL_DS . 'admin' . RL_DS . 'watermark.tpl';
        $_response->assign($field, 'innerHTML', $GLOBALS['rlSmarty']->fetch($tpl, null, null, false));
        return $_response;
    }
    function checkContentBlock($type = false, $days = false, $limit = false, $label = false, $order = false, $field = false)
    {
        $days  = (int) $days;
        $limit = (int) $limit;
        if (is_array($field)) {
            $data  = $this->fetch(array(
                'Type',
                'Days',
                'Count',
                'Order',
                'Key'
            ), array(
                'ID' => $field[2]
            ), null, null, 'listing_status', 'row');
            $type  = $data['Type'];
            $label = $data['Key'];
            $order = $data['Order'];
            if ($field[0] == 'Days') {
                $days  = $field[1];
                $limit = $data['Count'];
            } elseif ($field[0] == 'Count') {
                $days  = $data['Days'];
                $limit = $field[1];
            }
        }
        $content = '
				global $reefless, $rlSmarty;
				$reefless -> loadClass("ListingStatus", null, "listing_status");
				global $rlListingStatus;
				$ls_listings = $rlListingStatus -> getRecentlySoldListings( "' . $type . '", "' . $days . '", "' . $limit . '", "' . $label . '", "' . $order . '" );
				$key_s = ' . $label . ';
				$rlSmarty -> assign_by_ref( "ls_key",  $key_s);
				$rlSmarty -> assign_by_ref( "ls_listings", $ls_listings );
				$rlSmarty -> display( RL_PLUGINS . "listing_status" . RL_DS . "recently_sold.block.tpl" );
			';
        return $content;
    }
    function ajaxChangeStatus($listing_id, $status = 'visible', $admin = false)
    {
        global $_response, $config;
        if (empty($listing_id)) {
            return $_response;
        }
        $allLangs = $GLOBALS['languages'];
        $GLOBALS['reefless']->loadClass('Categories');
        $GLOBALS['reefless']->loadClass('Resize');
        $GLOBALS['reefless']->loadClass('Crop');
        $listing_info = $this->fetch(array(
            'Sub_status',
            'Category_ID'
        ), array(
            'ID' => $listing_id
        ), NULL, NULL, 'listings', 'row');
        if ($status != 'visible' && $status != 'invisible') {
            $sql = "UPDATE `" . RL_DBPREFIX . "listings` SET `Sold_date` = NOW(),`Sub_status` = '{$status}' WHERE `ID` =" . $listing_id;
            $this->query($sql);
            if ($config['rl_version'] == '4.0.1') {
                $sql = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT `T6`.`Thumbnail` ORDER BY `T6`.`Type` DESC, `T6`.`ID` ASC), ',', 1) AS `Main_photo` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_photos` AS `T6` ON `T1`.`ID` = `T6`.`Listing_ID` ";
                $sql .= "WHERE `T1`.`ID` =" . $listing_id . "  LIMIT 1";
                $main = $this->getRow($sql);
            } else {
                $main['Main_photo'] = $this->getOne('Main_photo', "`ID` = {$listing_id}", 'listings');
            }
            if ($main['Main_photo']) {
                $sql                                  = "SELECT * FROM `" . RL_DBPREFIX . "listing_photos` WHERE `Listing_ID` =" . $listing_id . " ORDER BY `Position`";
                $photos                               = $this->getAll($sql);
                $GLOBALS['config']['watermark_using'] = 1;
                $GLOBALS['config']['watermark_type']  = 'image';
                $status_info                          = $this->fetch('*', array(
                    'Status' => 'active',
                    'Key' => $status
                ), null, null, 'listing_status', 'row');
                foreach ($allLangs as $kLang => $code) {
                    foreach ($photos as $key => $photo) {
                        $stat_field                               = 'watermark_' . $code['Code'];
                        $GLOBALS['config']['watermark_image_url'] = RL_FILES . 'watermark' . RL_DS . $status_info[$stat_field];
                        $original_file                            = RL_FILES . $photo['Original'];
                        $thumbnail_file                           = RL_FILES . $photo['Thumbnail'];
                        $photo_file                               = RL_FILES . $photo['Photo'];
                        $thum_name                                = explode('.', $photo['Thumbnail']);
                        $thum_ext                                 = $thum_name[1];
                        $thum_pname                               = $thum_name[0];
                        $old_thumb                                = RL_FILES . str_replace('/', RL_DS, $thum_pname) . '_' . $listing_info['Sub_status'] . '_' . $code['Code'] . '.' . $thum_ext;
                        if (file_exists($old_thumb)) {
                            unlink($old_thumb);
                        }
                        $new_thumb      = $thum_pname . '_' . $status . '_' . $code['Code'] . '.' . $thum_ext;
                        $new_thumb_file = RL_FILES . $new_thumb;
                        chmod($new_thumb_file, 0777);
                        if ($photo['Original']) {
                            $GLOBALS['rlCrop']->loadImage($original_file);
                        } else {
                            $GLOBALS['rlCrop']->loadImage($photo_file);
                        }
                        $GLOBALS['rlCrop']->cropBySize($GLOBALS['config']['pg_upload_thumbnail_width'], $GLOBALS['config']['pg_upload_thumbnail_height'], ccCENTER);
                        $GLOBALS['rlCrop']->saveImage($new_thumb_file, $GLOBALS['config']['img_quality']);
                        $GLOBALS['rlCrop']->flushImages();
                        $GLOBALS['rlResize']->resize($new_thumb_file, $new_thumb_file, 'C', array(
                            $GLOBALS['config']['pg_upload_thumbnail_width'],
                            $GLOBALS['config']['pg_upload_thumbnail_height']
                        ), null, true);
                        if ($key == 0) {
                            $stat_field                               = 'watermarkLarge_' . $code['Code'];
                            $GLOBALS['config']['watermark_image_url'] = RL_FILES . 'watermark' . RL_DS . $status_info[$stat_field];
                            $photo_name                               = explode('.', $photo['Photo']);
                            $old_photo                                = RL_FILES . str_replace('/', RL_DS, $photo_name[0]) . '_' . $listing_info['Sub_status'] . '_' . $code['Code'] . '.' . $photo_name[1];
                            if (file_exists($old_photo)) {
                                unlink($old_photo);
                            }
                            $new_photo      = $photo_name[0] . '_' . $status . '_' . $code['Code'] . '.' . $photo_name[1];
                            $new_photo_file = RL_FILES . $new_photo;
                            chmod($new_photo_file, 0777);
                            if ($photo['Original']) {
                                $GLOBALS['rlCrop']->loadImage($original_file);
                            } else {
                                $GLOBALS['rlCrop']->loadImage($photo_file);
                            }
                            $GLOBALS['rlCrop']->cropBySize($GLOBALS['config']['pg_upload_large_width'], $GLOBALS['config']['pg_upload_large_height'], ccCENTER);
                            $GLOBALS['rlCrop']->saveImage($new_photo_file, $GLOBALS['config']['img_quality']);
                            $GLOBALS['rlCrop']->flushImages();
                            $GLOBALS['rlResize']->resize($new_photo_file, $new_photo_file, 'C', array(
                                $GLOBALS['config']['pg_upload_large_width'],
                                $GLOBALS['config']['pg_upload_large_height']
                            ), true, true);
                        }
                    }
                }
                $main_name  = explode('.', $main['Main_photo']);
                $main_ext   = $main_name[1];
                $main_pname = $main_name[0];
                $main       = $main_pname . '_' . $status . '_' . RL_LANG_CODE . '.' . $main_ext;
                if (!$admin) {
                    $img = '<img src="' . RL_URL_HOME . 'files/' . $main . '"/>';
                    $_response->script("$('#listing_" . $listing_id . "').find('tr:eq(0) td.photo div a:eq(0)').html('" . $img . "');");
                }
            }
            if ($listing_info['Sub_status'] == 'invisible') {
                $GLOBALS['rlCategories']->listingsIncrease($listing_info['Category_ID']);
            }
        } else {
            if ($status == 'invisible') {
                $GLOBALS['rlCategories']->listingsDecrease($listing_info['Category_ID']);
            } elseif ($status == 'visible' && $listing_info['Sub_status'] == 'invisible') {
                $GLOBALS['rlCategories']->listingsIncrease($listing_info['Category_ID']);
            }
            $sql = "UPDATE `" . RL_DBPREFIX . "listings` SET `Sold_date` = '0000-00-00 00:00:00',`Sub_status` = '{$status}' WHERE `ID` =" . $listing_id;
            $this->query($sql);
            if ($listing_info['Sub_status'] != 'visible' && $listing_info['Sub_status'] != 'invisible') {
                $sql = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT `T6`.`Thumbnail` ORDER BY `T6`.`Type` DESC, `T6`.`ID` ASC), ',', 1) AS `Main_photo` FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
                $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_photos` AS `T6` ON `T1`.`ID` = `T6`.`Listing_ID` ";
                $sql .= "WHERE `T1`.`ID` =" . $listing_id . " LIMIT 1";
                $main = $this->getRow($sql);
                if ($main['Main_photo']) {
                    $sql    = "SELECT `Thumbnail`, `Photo` FROM `" . RL_DBPREFIX . "listing_photos` WHERE `Listing_ID` =" . $listing_id . " ORDER BY `Position";
                    $photos = $this->getAll($sql);
                    foreach ($photos as $key => $photo) {
                        foreach ($allLangs as $kLang => $code) {
                            $thum_name  = explode('.', $photo['Thumbnail']);
                            $thum_ext   = $thum_name[1];
                            $thum_pname = $thum_name[0];
                            $old_thumb  = RL_FILES . str_replace('/', RL_DS, $thum_pname) . '_' . $listing_info['Sub_status'] . '_' . $code['Code'] . '.' . $thum_ext;
                            if (file_exists($old_thumb)) {
                                unlink($old_thumb);
                            }
                            if ($key == 0) {
                                $thum_name  = explode('.', $photo['Photo']);
                                $thum_ext   = $thum_name[1];
                                $thum_pname = $thum_name[0];
                                $old_photo  = RL_FILES . str_replace('/', RL_DS, $thum_pname) . '_' . $listing_info['Sub_status'] . '_' . $code['Code'] . '.' . $thum_ext;
                                if (file_exists($old_photo)) {
                                    unlink($old_photo);
                                }
                            }
                        }
                    }
                    if (!$admin) {
                        $img = '<img src="' . RL_URL_HOME . 'files/' . $main['Main_photo'] . '"/>';
                        $_response->script("$('#listing_" . $listing_id . "').find('tr:eq(0) td.photo div a:eq(0)').html('" . $img . "');");
                    }
                }
            }
        }
        $mess = $GLOBALS['lang']['ls_notice_' . $status];
        $_response->script("printMessage('notice', '" . $mess . "');");
        return $_response;
    }
    function ajaxDeleteStatusBlock($id = false)
    {
        global $_response;
        $id = (int) $id;
        if ($this->checkSessionExpire() === false) {
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php?action=session_expired');
            return $_response;
        }
        if (!$id) {
            return $_response;
        }
        $key = $this->getOne('Key', "`ID` = '{$id}'", 'listing_status');
        $this->query("DELETE FROM `" . RL_DBPREFIX . "listing_status` WHERE `ID` = '{$id}' LIMIT 1");
        $this->query("DELETE FROM `" . RL_DBPREFIX . "blocks` WHERE `Key` = 'lb_{$key}' LIMIT 1");
        $this->query("DELETE FROM `" . RL_DBPREFIX . "lang_keys` WHERE `Key` = 'lsl_{$key}' || `Key` = 'blocks+name+lb_{$key}'");
        $GLOBALS['rlActions']->enumRemove('listings', 'Sub_status', $key);
        $_response->script("
				ListingStatus.reload();
				printMessage('notice', '{$GLOBALS['lang']['block_deleted']}')
			");
        return $_response;
    }
    function getStatus()
    {
        global $sql, $config, $lang;
        $status[] = Array(
            'Key' => 'visible',
            'Type' => 'all',
            'name' => $lang['ls_visible']
        );
        $status[] = Array(
            'Key' => 'invisible',
            'Type' => 'all',
            'name' => $lang['ls_invisible']
        );
        $data     = $this->fetch(array(
            'Key',
            'Type'
        ), array(
            'Status' => 'active'
        ), null, null, 'listing_status');
        foreach ($data as $key => $val) {
            $status[] = Array(
                'Key' => $data[$key]['Key'],
                'Type' => explode(',', $data[$key]['Type']),
                'name' => $lang['lsl_' . $data[$key]['Key']]
            );
        }
        return $status;
    }
    function getRecentlySoldListings($listings_type, $days, $limit, $s_status, $order)
    {
        global $sql, $config;
        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT {hook} SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT `T6`.`Thumbnail` ORDER BY `T6`.`Type` DESC, `T6`.`ID` ASC), ',', 1) AS `Main_photo`, ";
        $sql .= "`T1`.*, `T1`.`Shows`, `T2`.`Image`, `T2`.`Image_unlim`, `T3`.`Path` AS `Path`, `T3`.`Key` AS `Key`, `T3`.`Type` AS `Listing_type`, ";
        $sql .= $config['grid_photos_count'] ? "COUNT(`T6`.`Thumbnail`) AS `Photos_count`, " : "";
        $GLOBALS['rlHook']->load('listingsModifyField');
        $sql .= "IF(UNIX_TIMESTAMP(DATE_ADD(`T1`.`Featured_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0, '1', '0') `Featured` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_status` AS `T4` ON `T1`.`Sub_status` = `T4`.`Key` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_photos` AS `T6` ON `T1`.`ID` = `T6`.`Listing_ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
        $GLOBALS['rlHook']->load('listingsModifyJoin');
        $sql .= "WHERE if( `T4`.`Delete` != 'simple' , UNIX_TIMESTAMP(DATE_ADD(`T1`.`Sold_date`, INTERVAL {$days} DAY)) > UNIX_TIMESTAMP(NOW()), 1) AND ";
        $sql .= "(`T3`.`Type` = '{$listings_type}' OR FIND_IN_SET( `T3`.`Type` , '{$listings_type}') > 0 ) AND ";
        $sql .= "`T1`.`Status` = 'active' AND `T3`.`Status` = 'active' AND `T7`.`Status` = 'active' AND ";
        $sql .= "`T1`.`Sub_status` = '{$s_status}' ";
        $sql .= "GROUP BY `ID` ";
        switch ($order) {
            case 'latest':
                $sql .= "ORDER BY `T1`.`Sold_date` DESC ";
                break;
            case 'random':
                $sql .= "ORDER BY RAND() ";
                break;
            default:
                $sql .= "ORDER BY `ID` DESC ";
                break;
        }
        $sql .= "LIMIT {$limit} ";
        $sql      = str_replace('{hook}', $hook, $sql);
        $listings = $this->getAll($sql);
        $listings = $this->rlLang->replaceLangKeys($listings, 'categories', 'name');
        if (empty($listings)) {
            return false;
        }
        $calc       = $this->getRow("SELECT FOUND_ROWS() AS `calc`");
        $this->calc = $calc['calc'];
        if (!$config['cache']) {
            $fields = $GLOBALS['rlListings']->getFormFields($category_id, 'featured_form', $listings[0]['Listing_type']);
        }
        foreach ($listings as $key => $value) {
            if ($config['cache']) {
                $fields = $GLOBALS['rlListings']->getFormFields($value['Category_ID'], 'featured_form', $value['Listing_type']);
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
    function deleteListingPhotos($info = false)
    {
        global $rlDb;
        $allLangs   = $GLOBALS['languages'];
        $sub_status = $this->getOne('Sub_status', "`ID` = '{$info['ID']}'", 'listings');
        if ($sub_status != 'visible' && $sub_status != 'invisible') {
            $data_photos = $this->fetch(array(
                'Thumbnail'
            ), array(
                'Listing_ID' => $info['ID']
            ), null, null, 'listing_photos');
            foreach ($data_photos as $key => $val) {
                $thum = explode('.', str_replace('/', RL_DS, $val['Thumbnail']));
                foreach ($allLangs as $lkey => $lval) {
                    $thum_label = RL_FILES . $thum[0] . '_' . $sub_status . '_' . $lval['Code'] . '.' . $thum[1];
                    unlink($thum_label);
                }
            }
        }
    }
    function deleteAllListingPhotosWithLable()
    {
        $data = $this->getAll("SELECT `Key` FROM `" . RL_DBPREFIX . "listing_status`  ");
        foreach ($data as $key => $val) {
            $this->foundPhotoLable(RL_FILES, $val['Key']);
        }
    }
    function foundPhotoLable($dir = RL_ROOT, $label = false)
    {
        $files = $this->scanDir($dir, false, true);
        if ($files) {
            foreach ($files as $file) {
                if ($file['type'] == 'dir') {
                    $this->foundPhotoLable($dir . $file['name'] . RL_DS, $label);
                } elseif ($file['type'] == 'file') {
                    $test = strpos($file['name'], $label);
                    if (preg_match('/_' . $label . '_/', $file['name'])) {
                        $file_path = rtrim($dir) . $file['name'];
                        unlink($file_path);
                    }
                }
            }
        }
    }
    function replacePhotos($photos, $listing_info)
    {
        foreach ($photos as $key => $photo) {
            if ($key == 0) {
                $Photo_name            = explode('.', $photo['Photo']);
                $photos[$key]['Photo'] = $Photo_name[0] . '_' . $listing_info['Sub_status'] . '_' . RL_LANG_CODE . '.' . $Photo_name[1];
            }
            $thum_name                 = explode('.', $photo['Thumbnail']);
            $photos[$key]['Thumbnail'] = $thum_name[0] . '_' . $listing_info['Sub_status'] . '_' . RL_LANG_CODE . '.' . $thum_name[1];
        }
        return $photos;
    }
    function updateStatus()
    {
        global $rlDb;
        if (!$rlDb->getRow("SHOW FIELDS FROM `" . RL_DBPREFIX . "listing_status` WHERE `Field` LIKE 'watermarkLarge_%'")) {
            $data = $this->fetch('*', array(
                'Status' => 'active'
            ), null, null, 'listing_status');
            foreach ($GLOBALS['languages'] as $key => $val) {
                $lable       = "watermark_" . $val['Code'];
                $large_lable = "watermarkLarge_" . $val['Code'];
                $rlDb->query("ALTER TABLE `" . RL_DBPREFIX . "listing_status` ADD `" . $large_lable . "` VARCHAR( 50 ) NOT NULL AFTER `" . $lable . "`");
                foreach ($data as $sKey => $sVal) {
                    $file_ext   = array_reverse(explode('.', $sVal[$lable]));
                    $photo_name = $sVal['Key'] . '_large_' . $val['Code'] . '.' . $file_ext[0];
                    if (copy(RL_FILES . 'watermark' . RL_DS . $sVal[$lable], RL_FILES . 'watermark' . RL_DS . $photo_name)) {
                        $rlDb->query("UPDATE `" . RL_DBPREFIX . "listing_status` SET `{$large_lable}` = '{$photo_name}' WHERE `Key` = '{$sVal['Key']}'");
                    }
                }
            }
        }
        return true;
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