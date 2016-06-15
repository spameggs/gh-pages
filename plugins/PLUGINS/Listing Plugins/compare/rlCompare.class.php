<?php
class rlCompare extends reefless
{
    function load($ids = false)
    {
        global $rlListingTypes, $lang, $config, $rlListings, $pages, $rlSmarty, $rlValid;
        $rlValid->sql($ids);
        if (!$ids) {
            echo false;
            return;
        }
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT `T6`.`Thumbnail` ORDER BY `T6`.`Type` DESC, `T6`.`ID` ASC), ',', 1) AS `Main_photo`, ";
        $sql .= "`T1`.*, `T4`.`Path` AS `Category_path`, `T4`.`Key` AS `Category_key`, `T4`.`Parent_ID`, `T4`.`Type` AS `Listing_type` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T3` ON `T1`.`Plan_ID` = `T3`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T4` ON `T1`.`Category_ID` = `T4`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_photos` AS `T6` ON `T1`.`ID` = `T6`.`Listing_ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
        $sql .= "WHERE ( ";
        $sql .= " UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T3`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) ";
        $sql .= " OR `T3`.`Listing_period` = 0 ";
        $sql .= ") ";
        $sql .= "AND FIND_IN_SET(`T1`.`ID`, '{$ids}') > 0 ";
        $sql .= "AND `T1`.`Status` = 'active' AND `T4`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
        if ($this->selectedIDs) {
            $sql .= "AND FIND_IN_SET(`T1`.`ID`, '" . implode(',', $this->selectedIDs) . "') = 0 ";
        }
        $sql .= "GROUP BY `T1`.`ID` ORDER BY FIELD(`T1`.`ID`, {$ids}) ASC ";
        $listings = $this->getAll($sql);
        if (empty($listings)) {
            echo false;
            return;
        }
        foreach ($listings as $key => $value) {
            $listing_type                        = $rlListingTypes->types[$value['Listing_type']];
            $listings_out[$key]['Listing_title'] = $rlListings->getListingTitle($value['Category_ID'], $value, $value['Listing_type']);
            $link                                = SEO_BASE;
            $link .= $config['mod_rewrite'] ? $pages[$listing_type['Page_key']] . '/' . $value['Category_path'] . '/' . $rlSmarty->str2path($listings_out[$key]['Listing_title']) . '-' . $value['ID'] . '.html' : '?page=' . $pages[$listing_type['Page_key']] . '&id=' . $value['ID'];
            $postfix       = $listing_type['Cat_postfix'] ? '.html' : '/';
            $category_name = $lang['categories+name+' . $value['Category_key']];
            $category_link = SEO_BASE;
            $category_link .= $config['mod_rewrite'] ? $pages[$listing_type['Page_key']] . '/' . $value['Category_path'] . $postfix : '?page=' . $pages[$listing_type['Page_key']] . '&category=' . $value['Category_ID'];
            $category                            = '<a class="cat_caption" href="' . $category_link . '" title="' . $lang['category'] . ': ' . $category_name . '">' . $category_name . '</a>';
            $listings_out[$key]['ID']            = $value['ID'];
            $listings_out[$key]['Category_path'] = $value['Category_path'];
            $listings_out[$key]['Main_photo']    = $value['Main_photo'];
            $listings_out[$key]['href']          = $link;
            $listings_out[$key]['category']      = $category;
        }
        unset($listings);
        $this->loadClass('Json');
        echo $GLOBALS['rlJson']->encode($listings_out);
    }
    function get($ids = false)
    {
        global $rlListingTypes, $lang, $config, $rlListings, $pages, $rlSmarty, $rlValid;
        $rlValid->sql($ids);
        if (!$ids) {
            echo false;
            return;
        }
        $deny_fields = array(
            'account_address_on_map'
        );
        $sql         = "SELECT DISTINCT ";
        $sql .= "`T1`.*, `T4`.`Path` AS `Category_path`, `T4`.`Key` AS `Category_key`, `T4`.`Parent_ID`, `T4`.`Type` AS `Listing_type` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T3` ON `T1`.`Plan_ID` = `T3`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T4` ON `T1`.`Category_ID` = `T4`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
        $sql .= "WHERE ( ";
        $sql .= " UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T3`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) ";
        $sql .= " OR `T3`.`Listing_period` = 0 ";
        $sql .= ") ";
        $sql .= "AND FIND_IN_SET(`T1`.`ID`, '{$ids}') > 0 ";
        $sql .= "AND `T1`.`Status` = 'active' AND `T4`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
        if ($this->selectedIDs) {
            $sql .= "AND FIND_IN_SET(`T1`.`ID`, '" . implode(',', $this->selectedIDs) . "') = 0 ";
        }
        $sql .= "GROUP BY `T1`.`ID` ORDER BY FIELD(`T1`.`ID`, {$ids}) ASC ";
        $listings = $this->getAll($sql);
        if (empty($listings)) {
            return false;
        }
        foreach ($listings as $key => $value) {
            $listing_type                    = $rlListingTypes->types[$value['Listing_type']];
            $listings[$key]['listing_title'] = $value['listing_title'] = $rlListings->getListingTitle($value['Category_ID'], $value, $value['Listing_type']);
            $link                            = SEO_BASE;
            $link .= $config['mod_rewrite'] ? $pages[$listing_type['Page_key']] . '/' . $value['Category_path'] . '/' . $rlSmarty->str2path($listings[$key]['listing_title']) . '-' . $value['ID'] . '.html' : '?page=' . $pages[$listing_type['Page_key']] . '&id=' . $value['ID'];
            $listings[$key]['listing_link'] = $link;
            $thumbnail_field                = array(
                'sys' => 1,
                'Key' => 'Main_photo',
                'Type' => 'text',
                'Details_page' => 1,
                'name' => $lang['compare_picture']
            );
            $img                            = $value['Main_photo'] ? RL_FILES_URL . $value['Main_photo'] : RL_TPL_BASE . 'img/no-picture.jpg';
            $listings[$key]['Main_photo']   = $value['Main_photo'] = $img;
            $fields['Main_photo']           = $thumbnail_field;
            $title_field                    = array(
                'sys' => 1,
                'Key' => 'listing_title',
                'Type' => 'text',
                'Details_page' => 1,
                'name' => $lang['compare_title'],
                'value' => $listings[$key]['listing_title']
            );
            $fields['listing_title']        = $title_field;
            $sys_fields                     = $this->getParentCatFields($value['Category_ID']);
            if (empty($sys_fields) && $listing_type['Cat_general_cat']) {
                $sys_fields = $this->getParentCatFields($listing_type['Cat_general_cat']);
            }
            $sys_fields = $GLOBALS['rlLang']->replaceLangKeys($sys_fields, 'listing_fields', array(
                'name'
            ));
            if ($sys_fields) {
                $fields = $fields + $sys_fields;
                unset($sys_fields);
            }
            foreach ($fields as $fKey => $fValue) {
                if (!$fValue['sys']) {
                    if ($first) {
                        $fields[$fKey]['value'] = $GLOBALS['rlCommon']->adaptValue($fValue, $value[$fKey], 'listing', $value['ID']);
                    } else {
                        if ($field['Condition'] == 'isUrl' || $field['Condition'] == 'isEmail') {
                            $fields[$fKey]['value'] = $value[$item];
                        } else {
                            $fields[$fKey]['value'] = $GLOBALS['rlCommon']->adaptValue($fValue, $value[$fKey], 'listing', $value['ID']);
                        }
                    }
                }
                $first++;
            }
            $listings[$key]['fields'] = $fields;
            foreach ($fields as $field) {
                if (!$fields_out[$field['Key']] && $value[$field['Key']] != '' && !in_array($field['Key'], $deny_fields)) {
                    $fields_out[$field['Key']] = $field;
                }
            }
        }
        $rlSmarty->assign_by_ref('fields_out', $fields_out);
        $rlSmarty->assign_by_ref('compare_listings', $listings);
    }
    function getParentCatFields($id = false)
    {
        $sql = "SELECT `Fields` FROM `" . RL_DBPREFIX . "listing_relations` ";
        $sql .= "WHERE `Category_ID` = '{$id}' ORDER BY `Position`";
        $fields_ids_tmp = $this->getAll($sql);
        if ($fields_ids_tmp) {
            foreach ($fields_ids_tmp as $ids) {
                $fields_ids .= ',' . $ids['Fields'];
            }
            $fields_ids = trim($fields_ids, ',');
            $sql        = "SELECT `Key`, `Type`, `Default`, `Condition`, `Details_page`, `Multilingual` ";
            $sql .= "FROM `" . RL_DBPREFIX . "listing_fields` ";
            $sql .= "WHERE FIND_IN_SET(`ID`, '{$fields_ids}') > 0 AND `Status` = 'active' AND `Details_page` = '1' ";
            $sql .= "ORDER BY FIND_IN_SET(`ID`, '{$fields_ids}')";
            $fields = $this->getAll($sql);
        }
        if (empty($fields)) {
            $parent = $this->getOne('Parent_ID', "`ID` = '{$id}' AND `Parent_ID` != '{$id}'", 'categories');
            if (!empty($parent)) {
                return $this->getParentCatFields($parent, $table);
            }
        } else {
            foreach ($fields as $value) {
                $tmp_fields[$value['Key']] = $value;
            }
            $fields = $tmp_fields;
            unset($tmp_fields);
            return $fields;
        }
    }
    function checkPath($path = false)
    {
        if ($this->getOne('ID', "`Path` = '{$path}'", 'compare_table')) {
            $path .= rand(1, 9);
            return $this->checkPath($path);
        } else {
            return $path;
        }
    }
    function ajaxRemoveSavedItem($id = false)
    {
        global $_response, $account_info, $rlActions;
        $where               = $_GET['nvar_1'] ? array(
            'Path' => $_GET['nvar_1']
        ) : array(
            'ID' => $_GET['id']
        );
        $where['Account_ID'] = $account_info['ID'];
        $item                = $this->fetch(array(
            'ID',
            'Name',
            'IDs',
            'Type'
        ), $where, null, 1, 'compare_table', 'row');
        if (!$item)
            return $_response;
        $ids = explode(',', $item['IDs']);
        unset($ids[array_search($id, $ids)]);
        if ($ids) {
            $update = array(
                'fields' => array(
                    'IDs' => implode(',', $ids)
                ),
                'where' => array(
                    'ID' => $item['ID']
                )
            );
            $rlActions->updateOne($update, 'compare_table');
            $_response->script("flCompare.remove(false, '{$id}', true, true)");
        } else {
            $this->query("DELETE FROM `" . RL_DBPREFIX . "compare_table` WHERE `ID` = '{$item['ID']}' LIMIT 1");
            $_response->script("
				$('table.compare').parent().fadeOut(function(){
					$('span#compare_no_data').fadeIn();
					$('#compare_saved_list_{$item['ID']}').fadeOut(function(){
						if ( $(this).parent().find('li').length <= 1 )
						{
							$(this).closest('div.fieldset').remove();
							$('#compare_no_data_block').fadeIn();
							$('a.button compare_fullscreen').fadeOut();
						}
					});
				});
			");
        }
        return $_response;
    }
    function ajaxRemoveTable($id = false)
    {
        global $_response, $account_info, $config, $page_info;
        if (!$id)
            return $_response;
        $where = array(
            'ID' => (int) $id,
            'Account_ID' => $account_info['ID']
        );
        $item  = $this->fetch(array(
            'ID',
            'Path'
        ), $where, null, 1, 'compare_table', 'row');
        if (!$item)
            return $_response;
        $this->query("DELETE FROM `" . RL_DBPREFIX . "compare_table` WHERE `ID` = {$id}");
        $_response->script("
			printMessage('notice', '{$lang['compare_table_removed']}');
			$('ul.comapre_saved_list li#compare_saved_list_{$id}').remove();
			
			if ( $('ul.comapre_saved_list li').length <= 0 )
			{
				$('ul.comapre_saved_list').closest('.fieldset').remove();
				$('#compare_no_data_block').show();
			}
		");
        $path = $_GET['id'] ? $_GET['id'] : $_GET['nvar_1'];
        if ($path == $item['Path']) {
            $url = SEO_BASE;
            $url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html' : '?page=' . $page_info['Path'];
            $_response->script("
				setTimeout(function(){
					location.href='" . $url . "';
				}, 1500);
			");
        }
        return $_response;
    }
}