<?php
class rlSearchByDistance extends rlListings
{
	/**
	* requested latitude
	**/
	var $lat = false;
	
	/**
	* requested longitude
	**/
	var $lng = false;
	
	/**
	* requested distance
	**/
	var $distance = false;
	
	/**
	* Flynax geoCode serice servers
	**/
	var $servers = array(
		'http://api.geonames.org/postalCodeSearchJSON?postalcode={zip}&country={code}&maxRows=1&style=short&username={geouser}'
	);

	/**
	* box template
	**/
	var $box_tpl = '
		global $rlSmarty;
		$countries = array({countries});
		$rlSmarty -> assign_by_ref("sbd_countries", $countries);
		$rlSmarty -> display(RL_PLUGINS . "search_by_distance" . RL_DS . "block.tpl");
	';
	
	/**
	* class constructor
	**/
	function rlSearchByDistance()
	{
		//global $rlSmarty;
		
		//$rlSmarty -> assign('sbd_iso', $this -> county_iso);
		shuffle($this -> servers);
	}
	
	/**
	* @hook - listingsModifyFieldSearch
	**/
	function modifySqlSelect()
	{
		global $rlSearch, $data, $config, $lang, $reefless, $sql;
		
		if ( $data )
		{
			foreach ($data as $key => $item)
			{
				if ( strpos($key, 'country') !== false && $data[$key] )
				{
					$country_key = $item;
					$country_field_key = $key;
					continue;
				}
			}
		}
		
		$GLOBALS['aHooks']['search_by_distance'] = false;
		
		foreach ( $rlSearch -> fields as $field )
		{
			if (  false !== strpos($field['Key'], 'zip') )
			{
				$zip = preg_replace('/[\W]/', '', $data[$field['Key']]['zip']);
				$distance = $data[$field['Key']]['distance'];
				
				break;
			}
		}
		
		if ( $zip && $distance && $config['sbd_geonames_user'] && ($country_key || $config['sbd_default_country']) )
		{
			$geouser = $config['sbd_geonames_user'];
			$code = $country_key ? $this -> county_iso[$country_key] : $config['sbd_default_country'];
			$content = $reefless -> getPageContent(str_replace(array('{zip}', '{code}', '{geouser}'), array(urldecode($zip), strtolower($code), $geouser), $this -> servers[0]));
			$reefless -> loadClass('Json');
			$content = $GLOBALS['rlJson'] -> decode($content);
			
			if ( $content-> postalCodes[0] -> lat && $content-> postalCodes[0] -> lng )
			{
				$lat = $content -> postalCodes[0] -> lat;
				$lng = $content -> postalCodes[0] -> lng;
			}
			
			if ( $config['sbd_default_units'] == 'kilometres' )
			{
				$distance *= 1.609344;
			}
			
			$this -> distance = $distance;
			
			if ( $lat && $lng )
			{
				$this -> lat = $lat;
				$this -> lng = $lng;
				$sql .= "(3956 * 2 * ASIN(SQRT( POWER(SIN(({$lat} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) + COS({$lat} * 0.0174532925) * COS(`T1`.`Loc_latitude` * 0.0174532925) * POWER(SIN(({$lng} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2)))) AS `sbd_distance`, ";
				
				/* remove country from data */
				unset($data[$country_field_key]);
			}
		}
	}
	
	/**
	* @hook - listingsModifyWhereSearch
	**/
	function modifySqlWhere()
	{
		global $sql;
		
		if ( $this -> lat && $this -> lng && $this -> distance )
		{
			$sql .= "AND (3956 * 2 * ASIN(SQRT( POWER(SIN(({$this -> lat} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) + COS({$this -> lat} * 0.0174532925) * COS(`T1`.`Loc_latitude` * 0.0174532925) * POWER(SIN(({$this -> lng} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2))) <= {$this -> distance}) ";
		}
	}
	
	/**
	* @hook - accountsSearchDealerSqlSelect
	**/
	function accountModifySqlSelect( &$sql, &$data )
	{
		global $rlSearch, $config, $lang, $reefless;
		
		if ( $data )
		{
			foreach ($data as $key => $item)
			{
				if ( false !== strpos($key, 'country') && $item )
				{
					$country_key = $data[$country_key];
				}
				elseif (  false !== strpos($key, 'zip') && $item['zip'] )
				{
					$zip = preg_replace('/[\W]/', '', $item['zip']);
					$distance = $item['distance'];
					
					break;
				}
			}
		}
		
		if ( $zip && $distance && ($country_key || $config['sbd_default_country']) )
		{
			$geouser = $config['sbd_geonames_user'];
			$code = $country_key ? $this -> county_iso[$country_key] : $config['sbd_default_country'];
			$content = $reefless -> getPageContent(str_replace(array('{zip}', '{code}', '{geouser}'), array(urldecode($zip), strtolower($code), $geouser), $this -> servers[0]));
			$reefless -> loadClass('Json');
			$content = $GLOBALS['rlJson'] -> decode($content);
			
			if ( $content-> postalCodes[0] -> lat && $content-> postalCodes[0] -> lng )
			{
				$lat = $content -> postalCodes[0] -> lat;
				$lng = $content -> postalCodes[0] -> lng;
			}
			
			if ( $config['sbd_default_units'] == 'kilometres' )
			{
				$distance *= 1.609344;
			}
			
			$this -> distance = $distance;
			
			if ( $lat && $lng )
			{
				$this -> lat = $lat;
				$this -> lng = $lng;
				$sql .= "(3956 * 2 * ASIN(SQRT( POWER(SIN(({$lat} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) + COS({$lat} * 0.0174532925) * COS(`T1`.`Loc_latitude` * 0.0174532925) * POWER(SIN(({$lng} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2)))) AS `sbd_distance`, ";
			}
		}
	}
	
	/**
	* @hook - accountsSearchDealerSqlWhere
	**/
	function accountModifySqlWhere( &$sql )
	{
		if ( $this -> lat && $this -> lng && $this -> distance )
		{
			$sql .= "AND (3956 * 2 * ASIN(SQRT( POWER(SIN(({$this -> lat} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) + COS({$this -> lat} * 0.0174532925) * COS(`T1`.`Loc_latitude` * 0.0174532925) * POWER(SIN(({$this -> lng} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2))) <= {$this -> distance}) ";
		}
	}
	
	/**
	* get listings by coordinates
	*
	* @package xajax
	*
	* @param double $lat - latitude
	* @param double $lng - longitude
	* @param double $distance - distance in miles
	* @param string $unit - requsted distance unit, mi or km
	* @param string $order - field name for order
	* @param string $order_type - order type
	* @param int $page - current page
	*
	* @return array - listings information
	**/
	function ajaxGetListings( $lat = false, $lng = false, $distance = false, $unit = false, $order = false, $order_type = 'ASC', $page = 0 )
	{
		global $_response, $rlSmarty, $lang, $config;
		
		if ( $lat == '' || $lng == '' || !$distance )
			return;

		$distance = $distance / 1000 / 1.609344;// convert to miles
		
		$coor['lat'] = $lat;
		$coor['lng'] = $lng;
		
		$listings = $this -> getListings( $coor, $distance, $order, $order_type, $page );
		if ( $listings )
		{
			//$listingsLoc = $this -> getListingsLoc( $coor, $distance );
			
			if ( $this -> calc > $config['sbd_listings_limit'] )
			{
				$_response -> script("
					printMessage('warning', '{$lang['sbd_search_limit_exceeded']}');
				");
				return $_response;
				exit;
			}
			
			$rlSmarty -> assign_by_ref('listings', $listings);
			$rlSmarty -> assign_by_ref('sbd_unit', $unit);
			
			$tpl = 'blocks' . RL_DS . 'grid.tpl';
			$_response -> assign( 'sbd_listings', 'innerHTML', $rlSmarty -> fetch( $tpl, null, null, false ) );
			$_response -> script("
				$('#sbd_dom').fadeIn();
			");
			
			$rlSmarty -> assign_by_ref('sbd_calc', $this -> calc);
			$rlSmarty -> assign_by_ref('sbd_page', $page);
			$tpl = RL_PLUGINS .'search_by_distance'. RL_DS . 'paging.tpl';
			$_response -> assign( 'sbd_paging', 'innerHTML', $rlSmarty -> fetch( $tpl, null, null, false ) );
			
			foreach ($listings as $listing)
			{
				$_response -> script("sbdSetMarker({$listing['Loc_latitude']}, {$listing['Loc_longitude']}, {$listing['ID']});");
				$ids[] = $listing['ID'];
			}
			
			//unset($listingsLoc);
			
			$found = str_replace(array('{number}', '{type}'), array("<b>{$this -> calc}</b>", $lang['listings']), $lang['listings_found']);
			
			$_response -> script("
				sbdClearMarker('". implode(',', $ids) ."');
				$('#sbd_count').html('{$found}');
				sbdPaging();
				
				if ( typeof(flFavoritesHandler) == 'function' )
				{
					flFavoritesHandler();
				}
				if ( typeof(reportBrokenLisitngHandler) == 'function' )
				{
					reportBrokenLisitngHandler();
				}
				if ( typeof(flCompare) == 'object' )
				{
					$('a.add_to_compare, a.remove_from_compare').unbind('click').click(function(){
						flCompare.action(this);
					});
				}
			");
		}
		else
		{
			$_response -> script("
				$('#sbd_listings').html('');
				$('#sbd_dom').fadeOut();
				$('#sbd_count').html('{$lang['no_listings_found_deny_posting']}');
				sbdClearMarker('all');
			");
		}
		
		/* clear progress flag */
		$_response -> script("sbdSearch_in_progress = false;");
		
		return $_response;
	}

	/**
	* get listings by coordinates
	*
	* @param array $coordinates - array(lat, lng)
	* @param double $distance - distance in miles
	*
	* @return array - listings information
	**/
	function getListingsLoc( $coordinates = false, $distance = false )
	{
		global $config;

		if ( $coordinates['lat'] == '' || $coordinates['lng'] == '' || !$distance )
			return;

		$GLOBALS['rlValid'] -> sql($coordinates);

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT `T1`.`Loc_latitude`, `T1`.`Loc_longitude`, `T1`.`ID` ";
		$sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";
		$sql .= "WHERE ( UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T2`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T2`.`Listing_period` = 0 ) ";

		$sql .= "AND (3956 * 2 * ASIN(SQRT(
				POWER(SIN(({$coordinates['lat']} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) +
				COS({$coordinates['lat']} * 0.0174532925) *
				COS(`T1`.`Loc_latitude` * 0.0174532925) *
				POWER(SIN(({$coordinates['lng']} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2))) <= {$distance}) ";

		$sql .= "AND `T1`.`Status` = 'active' AND `T3`.`Status` = 'active' AND `T7`.`Status` = 'active' ";
		$sql .= "GROUP BY `T1`.`ID` ";
		$sql .= "LIMIT 0, {$config['sbd_listings_limit']} ";

		$listings = $this -> getAll($sql);

		$calc = $this -> getRow( "SELECT FOUND_ROWS() AS `calc`" );
		$this -> calc = $calc['calc'];

		return $listings;
	}

	/**
	* get listings by coordinates
	*
	* @param array $coordinates - array(lat, lng)
	* @param double $distance - distance in miles
	* @param string $order - field name for order
	* @param string $order_type - order type
	* @param int $start - start DB position
	*
	* @return array - listings information
	**/
	function getListings( $coordinates = false, $distance = false, $order = false, $order_type = 'ASC', $start = 0, $limit = 10 )
	{
		global $sorting, $sql, $custom_order, $config, $rlLang;

		if ( $coordinates['lat'] == '' || $coordinates['lng'] == '' || !$distance )
			return;

		$limit = intval($config['sbd_listings_per_page'] ? $config['sbd_listings_per_page'] : $limit);
		$start = $start > 1 ? ($start - 1) * $limit : 0;
		$GLOBALS['rlValid'] -> sql($coordinates);

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";

		if ( version_compare($config['rl_version'], '4.1.0') < 0 )
		{
			$sql .= "SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT `T6`.`Thumbnail` ORDER BY `T6`.`Type` DESC, `T6`.`ID` ASC), ',', 1) AS `Main_photo`, ";
			$sql .= $config['grid_photos_count'] ? "COUNT(`T6`.`Thumbnail`) AS `Photos_count`, " : "";
		}

		$sql .= "`T1`.*, `T1`.`Shows`, `T3`.`Path` AS `Path`, `T3`.`Key` AS `Key`, `T3`.`Type` AS `Listing_type`, ";

		$sql .= "3956 * 2 * ASIN(SQRT(
			POWER(SIN(({$coordinates['lat']} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) +
			COS({$coordinates['lat']} * 0.0174532925) *
			COS(`T1`.`Loc_latitude` * 0.0174532925) *
			POWER(SIN(({$coordinates['lng']} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2)
		)) AS `sbd_distance`, ";

		$GLOBALS['rlHook'] -> load('listingsModifyField');

		$sql .= "IF(TIMESTAMPDIFF(HOUR, `T1`.`Featured_date`, NOW()) <= `T4`.`Listing_period` * 24 OR `T4`.`Listing_period` = 0, '1', '0') `Featured` ";
		$sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T2` ON `T1`.`Plan_ID` = `T2`.`ID` ";
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T4` ON `T1`.`Featured_ID` = `T4`.`ID` ";
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T1`.`Category_ID` = `T3`.`ID` ";

		if ( version_compare($config['rl_version'], '4.1.0') < 0 )
		{
			$sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_photos` AS `T6` ON `T1`.`ID` = `T6`.`Listing_ID` ";
		}
		$sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T7` ON `T1`.`Account_ID` = `T7`.`ID` ";

		$GLOBALS['rlHook'] -> load('listingsModifyJoin');

		$sql .= "WHERE (";
		$sql .= " TIMESTAMPDIFF(HOUR, `T1`.`Pay_date`, NOW()) <= `T2`.`Listing_period` * 24 "; //round to hour
		$sql .= " OR `T2`.`Listing_period` = 0 ";
		$sql .= ") ";

		$sql .= "AND (3956 * 2 * ASIN(SQRT(
				POWER(SIN(({$coordinates['lat']} - `T1`.`Loc_latitude`) * 0.0174532925 / 2), 2) +
				COS({$coordinates['lat']} * 0.0174532925) *
				COS(`T1`.`Loc_latitude` * 0.0174532925) *
				POWER(SIN(({$coordinates['lng']} - `T1`.`Loc_longitude`) * 0.0174532925 / 2), 2))) <= {$distance}) ";

		$sql .= "AND `T1`.`Status` = 'active' AND `T3`.`Status` = 'active' AND `T7`.`Status` = 'active' ";

		$GLOBALS['rlHook'] -> load('listingsModifyWhere');
		$GLOBALS['rlHook'] -> load('listingsModifyGroup');

		if ( false === strpos($sql, 'GROUP BY') )
		{
			$sql .= " GROUP BY `T1`.`ID` ";
		}
		$sql .= "ORDER BY `Featured` DESC ";

		$GLOBALS['rlValid'] -> sql($custom_order);
		$GLOBALS['rlValid'] -> sql($order_type);

		if ( $custom_order )
		{
			$sql .= ", `{$custom_order}` ". strtoupper($order_type) . " ";
		}
		elseif ( $order )
		{
			switch ($sorting[$order]['Type']){
				case 'price':
				case 'unit':
				case 'mixed':
					$sql .= ", ROUND(`T1`.`{$sorting[$order]['field']}`) " . strtoupper($order_type) . " ";
					break;

				case 'select':
					if ( $sorting[$order]['Key'] == 'Category_ID' )
					{
						$sql .= ", `T3`.`Key` " . strtoupper($order_type) . " ";
					}
					else
					{
						$sql .= ", `T1`.`{$sorting[$order]['field']}` " . strtoupper($order_type) . " ";
					}
					break;

				default:
					$sql .= ", `T1`.`{$sorting[$order]['field']}` " . strtoupper($order_type) . " ";
					break;
			}
		}

		$sql .= ", `ID` DESC ";
		$sql .= "LIMIT {$start}, {$limit} ";

		$listings = $this -> getAll($sql);

		$calc = $this -> getRow( "SELECT FOUND_ROWS() AS `calc`" );
		$this -> calc = $calc['calc'];

		$listings = $rlLang -> replaceLangKeys( $listings, 'categories', 'name' );

		if ( empty($listings) )
		{
			return false;
		}

		foreach ( $listings as $key => $value )
		{
			/* populate fields */
			$fields = $this -> getFormFields( $value['Category_ID'], 'short_forms', $value['Listing_type'] );

			foreach ( $fields as $fKey => $fValue )
			{
				if ( $first )
				{
					$fields[$fKey]['value'] = $GLOBALS['rlCommon'] -> adaptValue( $fValue, $value[$fKey], 'listing', $value['ID'] );
				}
				else
				{
					if ( $field['Condition'] == 'isUrl' || $field['Condition'] == 'isEmail' )
					{
						$fields[$fKey]['value'] = $listings[$key][$item];
					}
					else
					{
						$fields[$fKey]['value'] = $GLOBALS['rlCommon'] -> adaptValue( $fValue, $value[$fKey], 'listing', $value['ID'] );
					}
				}
				$first++;
			}

			$listings[$key]['fields'] = $fields;
			$listings[$key]['listing_title'] = $this -> getListingTitle( $value['Category_ID'], $value, $value['Listing_type'] );
		}

		return $listings;
	}
	
	/**
	* load listing details for map marker baloon
	*
	* @param int $id - lisitng ID
	*
	* @todo html - content for map marker baloon
	**/
	function loadListingData( $id = false )
	{
		global $rlListingTypes, $lang, $config, $rlListings, $pages, $rlSmarty;

		$id = (int)$_GET['id'];
		if ( !$id )
		{
			echo $lang['sbd_listing_unavailable'];
			return;
		}

		$listing = $rlListings -> getShortDetails($id);
		$listing_type = $rlListingTypes -> types[$listing['Listing_type']];

		if ( $listing )
		{
			$html = '<table class="sbd_baloon"><tr>';
			if ( $listing_type['Photo'] )
			{
				$link = SEO_BASE;
				$link .= $config['mod_rewrite'] ? $pages[$listing_type['Page_key']] .'/'. $listing['Category_path'] .'/'. $rlSmarty -> str2path($listing['listing_title']) .'-'. $id .'.html' : 'index.php?page='. $pages[$listing_type['Page_key']] .'&amp;id=' . $listing['ID'];

				$photo = $this -> getOne('Thumbnail', "`Listing_ID` = '{$id}' AND `Status` = 'active' ORDER BY `Type` DESC, `ID` ASC", 'listing_photos');
				if ( $photo )
				{
					$html .= '<td class="thumbnail"><div>';
					if ( $listing_type['Page'] )
					{
						$html .= '<a target="_blank" href="'. $link .'">';
					}
					$html .= '<img alt="'. $listing['listing_title'] .'" title="'. $listing['listing_title'] .'" src="'. RL_FILES_URL . $photo .'" />';
					if ( $listing_type['Page'] )
					{
						$html .= '</a>';
					}
					$html .= '</div></td>';
				}

				$html .= '<td valign="top"><div class="sbd_title"><a target="_blank" href="'. $link .'">'. $listing['listing_title'] .'</a></div>';

				if ( $listing['fields'] )
				{
					$html .= '<table class="table">';
					foreach ($listing['fields'] as $field)
					{
						$html .= '<tr><td class="name"><div title="'. $field['name'] .'">'. $field['name'] .'</div></td><td class="value">'. $field['value'] .'</td></tr>';
					}
					$html .= '</table>';
				}

				$html .= '</td>';
			}
			$html .= '</tr></table>';

			echo $html;
		}
		else
		{
			echo $lang['sbd_listing_unavailable'];
			return;
		}
	}

	/**
	* remove country
	*
	* @package xajax
	*
	* @param string $code - country code
	*
	**/
	function ajaxRemoveCountry( $code = false )
	{
		global $_response, $lang, $config;

		if ( !$code )
			return $_response;

		$GLOBALS['rlValid'] -> sql($code);
		$this -> query("DELETE FROM `". RL_DBPREFIX ."sbd_countries` WHERE `Code` = '{$code}' LIMIT 1");

		/* update box */
		$this -> updateBox();

		$_response -> script("
			countriesGrid.reload();
			printMessage('notice', '{$lang['notice_items_deleted']}');
		");

		return $_response;
	}
	
	/**
	* add country
	*
	* @package xajax
	*
	* @param string $code - country code abbr
	* @param string $names - country names (multilingual)
	*
	**/
	function ajaxAddCountry( $code = false, $names = false )
	{
		global $_response, $lang, $rlActions, $config, $allLangs;

		if ( !$code || !$names )
			return $_response;

		$GLOBALS['rlValid'] -> sql($code);
		$code = strtoupper($code);

		if ( $exist = $this -> getOne('Code', "`Code` = '{$code}'", 'sbd_countries') )
		{
			$errors[] = str_replace('{code}', $code, $lang['sbd_country_exists']);
		}

		if ( empty($names[$config['lang']]) )
		{
			$errors[] = str_replace('{field}', '<b>'. $lang['sbd_country_code'] .' ('. $allLangs[$config['lang']]['name'] .')</b>', $lang['notice_field_empty']);
		}

		preg_match('/([a-zA-Z]{2})/', $code, $matches);
		if ( !$matches[1] )
		{
			$errors[] = $lang['sbd_code_wrong'];
		}

		if ( !empty($errors) )
		{
			$out = '<ul>';

			/* print errors */
			foreach ($errors as $error)
			{
				$out .= '<li>'. $error .'</li>';
			}
			$out .= '</ul>';
			$_response -> script("printMessage('error', '{$out}');");
		}
		else
		{
			/* insert main entry */
			$insert = array(
				'Code' => strtoupper($code),
				'Status' => 'active'
			);
			$rlActions -> insertOne($insert, 'sbd_countries');

			/* insert phrases */
			foreach ($allLangs as $language)
			{
				$phrases[] = array(
					'Code' => $language['Code'],
					'Module' => 'common',
					'Status' => 'active',
					'Plugin' => 'search_by_distance',
					'Key' => 'sbd_countries+name+sbd_country_'. $code,
					'Value' => $names[$language['Code']] ? $names[$language['Code']] : $names[$config['lang']]
				);
			}

			$rlActions -> insert($phrases, 'lang_keys');

			/* update box */
			$this -> updateBox();

			$_response -> script("
				countriesGrid.reload();
				printMessage('notice', '{$lang['sbd_country_added_notice']}');
				$('#new_item').slideUp('normal',function(){
					$('#ni_code, input.nl_name').val('');
				});
			");
		}

		$_response -> script("$('input[name=add_new_country_submit]').val('{$lang['add']}');");

		return $_response;
	}

	/**
	* update country
	*
	* @package xajax
	*
	* @param string $code - country code abbr
	* @param string $names - country names (multilingual)
	*
	**/
	function ajaxUpdateCountry( $code = false, $names = false )
	{
		global $_response, $lang, $rlActions, $config, $allLangs;

		if ( !$code || !$names )
			return $_response;

		if ( empty($names[$config['lang']]) )
		{
			$errors[] = str_replace('{field}', '<b>'. $lang['sbd_country_code'] .' ('. $allLangs[$config['lang']]['name'] .')</b>', $lang['notice_field_empty']);
		}

		if ( !empty($errors) )
		{
			$out = '<ul>';

			/* print errors */
			foreach ($errors as $error)
			{
				$out .= '<li>'. $error .'</li>';
			}
			$out .= '</ul>';
			$_response -> script("printMessage('error', '{$out}');");
		}
		else
		{
			$GLOBALS['rlValid'] -> sql($code);

			/* insert/update phrases */
			foreach ($allLangs as $language)
			{
				if ( $this -> getOne('ID', "`Key` = 'sbd_countries+name+sbd_country_{$code}' AND `Code` = '{$language['Code']}'") )
				{
					$update[] = array(
						'fields' => array(
							'Value' => $names[$language['Code']] ? $names[$language['Code']] : $names[$config['lang']]
						),
						'where' => array(
							'Key' => 'sbd_countries+name+sbd_country_'. $code,
							'Code' => $language['Code']
						)
					);
				}
				else
				{
					$insert[] = array(
						'Code' => $language['Code'],
						'Module' => 'common',
						'Status' => 'active',
						'Plugin' => 'search_by_distance',
						'Key' => 'sbd_countries+name+sbd_country_'. $code,
						'Value' => $names[$language['Code']] ? $names[$language['Code']] : $names[$config['lang']]
					);
				}
			}

			if ( $insert )
			{
				$rlActions -> insert($insert, 'lang_keys');
			}
			if ( $update )
			{
				$rlActions -> update($update, 'lang_keys');
			}

			/* update box */
			$this -> updateBox();

			$_response -> script("
				countriesGrid.reload();
				printMessage('notice', '{$lang['sbd_country_edited_notice']}');
				$('#edit_item').slideUp('normal',function(){
					$('#ei_code, input.el_name').val('');
				});
			");
		}

		$_response -> script("$('input[name=add_new_country_submit]').val('{$lang['add']}');");

		return $_response;
	}

	/**
	* update box
	**/
	function updateBox()
	{
		global $rlActions, $config;
		
		$this -> setTable('sbd_countries');
		$sql = "SELECT `T1`.`Code` ";
		$sql .= "FROM `". RL_DBPREFIX ."sbd_countries` AS `T1` ";
		$sql .= "LEFT JOIN `". RL_DBPREFIX ."lang_keys` AS `T2` ON CONCAT('sbd_countries+name+sbd_country_', `T1`.`Code`) = `T2`.`Key` AND `T2`.`Code` = '{$config['lang']}' ";
		$sql .= "WHERE `T1`.`Status` = 'active' ORDER BY `T2`.`Value` ";
		
		$countries = $this -> getAll($sql);
		
		foreach ($countries as $country)
		{
			$code .= "'{$country['Code']}' => array('Code' => '{$country['Code']}', 'pName' => 'sbd_countries+name+sbd_country_{$country['Code']}'),";
		}
		$code = rtrim($code, ',');
		
		$rlActions -> rlAllowHTML = true;
		
		$update = array(
			'fields' => array(
				'Content' => str_replace('{countries}', $code, $this -> box_tpl)
			),
			'where' => array(
				'Key' => 'search_by_distance'
			)
		);
		$rlActions -> updateOne($update, 'blocks');
	}
	
	/**
	* fill in edit country form
	*
	* @package xajax
	*
	* @param string $code - requested country code
	*
	**/
	function ajaxEditFillIn( $code )
	{
		global $_response, $config;

		if ( !$code )
			return $_response;

		$GLOBALS['rlValid'] -> sql($code);

		$this -> setTable('lang_keys');
		$names_tmp = $this -> fetch(array('Value', 'Code'), array('Key' => 'sbd_countries+name+sbd_country_'. $code));
		foreach ($names_tmp as $name)
		{
			$names[$name['Code']] = $name['Value'];
		}
		unset($names_tmp);

		foreach ($GLOBALS['languages'] as $language)
		{
			$set_name = $names[$language['Code']] ? $names[$language['Code']] : $names[$config['lang']];
			$_response -> script("
				$('div#edit_item div.tab_area input[lang={$language['Code']}]').val('{$set_name}');
			");
		}

		$_response -> script("
			$('#ei_code').val('{$code}');

			$('#edit_item').slideDown();
			flynax.slideTo('#edit_item');
		");

		return $_response;
	}

	var $county_iso = array(
		'aaland_islands' => 'AX',
		'afghanistan' => 'AF',
		'albania' => 'AL',
		'algeria' => 'DZ',
		'american_samoa' => 'AS',
		'andorra' => 'AD',
		'angola' => 'AO',
		'anguilla' => 'AI',
		'antarctica' => 'AQ',
		'antigua_and_barbuda' => 'AG',
		'argentina' => 'AR',
		'armenia' => 'AM',
		'aruba' => 'AW',
		'australia' => 'AU',
		'austria' => 'AT',
		'azerbaijan' => 'AZ',
		'bahamas' => 'BS',
		'bahrain' => 'BH',
		'bangladesh' => 'BD',
		'barbados' => 'BB',
		'belarus' => 'BY',
		'belgium' => 'BE',
		'belize' => 'BZ',
		'benin' => 'BJ',
		'bermuda' => 'BM',
		'bhutan' => 'BT',
		'bolivia' => 'BO',
		'bosnia_and_herzegowina' => 'BA',
		'botswana' => 'BW',
		'bouvet_island' => 'BV',
		'brazil' => 'BR',
		'british_indian_ocean_territory' => 'IO',
		'brunei_darussalam' => 'BN',
		'bulgaria' => 'BG',
		'burkina_faso' => 'BF',
		'burundi' => 'BI',
		'cambodia' => 'KH',
		'cameroon' => 'CM',
		'canada' => 'CA',
		'cape_verde' => 'CV',
		'cayman_islands' => 'KY',
		'central_african_republic' => 'CF',
		'chad' => 'TD',
		'chile' => 'CL',
		'china' => 'CN',
		'christmas_island' => 'CX',
		'cocos_keeling_islands' => 'CC',
		'colombia' => 'CO',
		'comoros' => 'KM',
		'congo_democratic_republic_of_was_zaire' => 'CD',
		'congo_republic_of' => 'CG',
		'cook_islands' => 'CK',
		'costa_rica' => 'CR',
		'cote_d_ivoire' => 'CI',
		'croatia_local_name_hrvatska' => 'HR',
		'cuba' => 'CU',
		'cyprus' => 'CY',
		'czech_republic' => 'CZ',
		'denmark' => 'DK',
		'djibouti' => 'DJ',
		'dominica' => 'DM',
		'dominican_republic' => 'DO',
		'ecuador' => 'EC',
		'egypt' => 'EG',
		'el_salvador' => 'SV',
		'equatorial_guinea' => 'GQ',
		'eritrea' => 'ER',
		'estonia' => 'EE',
		'ethiopia' => 'ET',
		'falkland_islands_malvinas' => 'FK',
		'faroe_islands' => 'FO',
		'fiji' => 'FJ',
		'finland' => 'FI',
		'france' => 'FR',
		'french_guiana' => 'GF',
		'french_polynesia' => 'PF',
		'french_southern_territories' => 'TF',
		'gabon' => 'GA',
		'gambia' => 'GM',
		'georgia' => 'GE',
		'germany' => 'DE',
		'ghana' => 'GH',
		'gibraltar' => 'GI',
		'greece' => 'GR',
		'greenland' => 'GL',
		'grenada' => 'GD',
		'guadeloupe' => 'GP',
		'guam' => 'GU',
		'guatemala' => 'GT',
		'guinea' => 'GN',
		'guinea_bissau' => 'GW',
		'guyana' => 'GY',
		'haiti' => 'HT',
		'heard_and_mc_donald_islands' => 'HM',
		'honduras' => 'HN',
		'hong_kong' => 'HK',
		'hungary' => 'HU',
		'iceland' => 'IS',
		'india' => 'IN',
		'indonesia' => 'ID',
		'iran_islamic_republic_of' => 'IR',
		'iraq' => 'IQ',
		'ireland' => 'IE',
		'israel' => 'IL',
		'italy' => 'IT',
		'jamaica' => 'JM',
		'japan' => 'JP',
		'jordan' => 'JO',
		'kazakhstan' => 'KZ',
		'kenya' => 'KE',
		'kiribati' => 'KI',
		'korea_democratic_people_s_republic_of' => 'KP',
		'korea_republic_of' => 'KR',
		'kuwait' => 'KW',
		'kyrgyzstan' => 'KG',
		'lao_people_s_democratic_republic' => 'LA',
		'latvia' => 'LV',
		'lebanon' => 'LB',
		'lesotho' => 'LS',
		'liberia' => 'LR',
		'libyan_arab_jamahiriya' => 'LY',
		'liechtenstein' => 'LI',
		'lithuania' => 'LT',
		'luxembourg' => 'LU',
		'macau' => 'MO',
		'macedonia_the_former_yugoslav_republic_of' => 'MK',
		'madagascar' => 'MG',
		'malawi' => 'MW',
		'malaysia' => 'MY',
		'maldives' => 'MV',
		'mali' => 'ML',
		'malta' => 'MT',
		'marshall_islands' => 'MH',
		'martinique' => 'MQ',
		'mauritania' => 'MR',
		'mauritius' => 'MU',
		'mayotte' => 'YT',
		'mexico' => 'MX',
		'micronesia_federated_states_of' => 'FM',
		'moldova_republic_of' => 'MD',
		'monaco' => 'MC',
		'mongolia' => 'MN',
		'montserrat' => 'MS',
		'morocco' => 'MA',
		'mozambique' => 'MZ',
		'myanmar' => 'MM',
		'namibia' => 'NA',
		'nauru' => 'NR',
		'nepal' => 'NP',
		'netherlands' => 'NL',
		'netherlands_antilles' => 'AN',
		'new_caledonia' => 'NC',
		'new_zealand' => 'NZ',
		'nicaragua' => 'NI',
		'niger' => 'NE',
		'nigeria' => 'NG',
		'niue' => 'NU',
		'norfolk_island' => 'NF',
		'northern_mariana_islands' => 'MP',
		'norway' => 'NO',
		'oman' => 'OM',
		'pakistan' => 'PK',
		'palau' => 'PW',
		'palestinian_territory_occupied' => 'PS',
		'panama' => 'PA',
		'papua_new_guinea' => 'PG',
		'paraguay' => 'PY',
		'peru' => 'PE',
		'philippines' => 'PH',
		'pitcairn' => 'PN',
		'poland' => 'PL',
		'portugal' => 'PT',
		'puerto_rico' => 'PR',
		'qatar' => 'QA',
		'reunion' => 'RE',
		'romania' => 'RO',
		'russia' => 'RU',
		'rwanda' => 'RW',
		'saint_helena' => 'SH',
		'saint_kitts_and_nevis' => 'KN',
		'saint_lucia' => 'LC',
		'saint_pierre_and_miquelon' => 'PM',
		'saint_vincent_and_the_grenadines' => 'VC',
		'samoa' => 'WS',
		'san_marino' => 'SM',
		'sao_tome_and_principe' => 'ST',
		'saudi_arabia' => 'SA',
		'senegal' => 'SN',
		'serbia_and_montenegro' => 'CS',
		'seychelles' => 'SC',
		'sierra_leone' => 'SL',
		'singapore' => 'SG',
		'slovakia' => 'SK',
		'slovenia' => 'SI',
		'solomon_islands' => 'SB',
		'somalia' => 'SO',
		'south_africa' => 'ZA',
		'south_georgia_and_the_south_sandwich_islands' => 'GS',
		'spain' => 'ES',
		'sri_lanka' => 'LK',
		'sudan' => 'SD',
		'suriname' => 'SR',
		'svalbard_and_jan_mayen_islands' => 'SJ',
		'swaziland' => 'SZ',
		'sweden' => 'SE',
		'switzerland' => 'CH',
		'syrian_arab_republic' => 'SY',
		'taiwan' => 'TW',
		'tajikistan' => 'TJ',
		'tanzania_united_republic_of' => 'TZ',
		'thailand' => 'TH',
		'timor_leste' => 'TL',
		'togo' => 'TG',
		'tokelau' => 'TK',
		'tonga' => 'TO',
		'trinidad_and_tobago' => 'TT',
		'tunisia' => 'TN',
		'turkey' => 'TR',
		'turkmenistan' => 'TM',
		'turks_and_caicos_islands' => 'TC',
		'tuvalu' => 'TV',
		'uganda' => 'UG',
		'ukraine' => 'UA',
		'united_arab_emirates' => 'AE',
		'united_kingdom' => 'GB',
		'united_states' => 'US',
		'united_states_minor_outlying_islands' => 'UM',
		'uruguay' => 'UY',
		'uzbekistan' => 'UZ',
		'vanuatu' => 'VU',
		'vatican_city_state_holy_see' => 'VA',
		'venezuela' => 'VE',
		'viet_nam' => 'VN',
		'virgin_islands_british' => 'VG',
		'virgin_islands_u_s' => 'VI',
		'wallis_and_futuna_islands' => 'WF',
		'western_sahara' => 'EH',
		'yemen' => 'YE',
		'zambia' => 'ZM'
	);
}