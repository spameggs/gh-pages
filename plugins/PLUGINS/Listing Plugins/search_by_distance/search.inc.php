<?php
$reefless -> loadClass('Listings');
$reefless -> loadClass('SearchByDistance', null, 'search_by_distance');

if ( $_GET['request'] == 'sbd' )
{
	$rlSearchByDistance -> loadListingData($_GET['id']);
	exit;
}

$geoData = $_SESSION['GEOLocationData']; 
$rlSmarty -> assign_by_ref('geoData', $geoData);

$sql = "SELECT `T1`.`Code` ";
$sql .= "FROM `". RL_DBPREFIX ."sbd_countries` AS `T1` ";
$sql .= "LEFT JOIN `". RL_DBPREFIX ."lang_keys` AS `T2` ON CONCAT('sbd_countries+name+sbd_country_', `T1`.`Code`) = `T2`.`Key` AND `T2`.`Code` = '". RL_LANG_CODE ."' ";
$sql .= "WHERE `T1`.`Status` = 'active' ORDER BY `T2`.`Value` ";

$countries_tmp = $rlDb -> getAll($sql);
foreach ($countries_tmp as $country)
{
	$countries[$country['Code']] = array('Code' => $country['Code'], 'pName' => 'sbd_countries+name+sbd_country_'. $country['Code']);
}
unset($countries_tmp);
$rlSmarty -> assign_by_ref('sbd_countries', $countries);

/* redefine "open in new window" config */
$config['view_details_new_window'] = $config['sbd_listings_blank'];

/* get data from block */
if ( isset($_POST['sbd_block']) )
{
	$country_key = array_search($_POST['block_country'], $rlSearchByDistance -> county_iso);
	$rlSmarty -> assign('sbdStartCountry', $lang['data_formats+name+'.$country_key]);
	$config['sbd_default_distance'] = $_POST['block_distance'];
	$config['sbd_default_units'] = $_POST['block_distance_unit'];
}

/* build sorting bar */
$sorting = array(
	'type' => array(
		'name' => $lang['listing_type'],
		'field' => 'Listing_type',
		'Key' => 'Listing_type',
		'Type' => 'select'
	),
	'category' => array(
		'name' => $lang['category'],
		'field' => 'Category_ID',
		'Key' => 'Category_ID',
		'Type' => 'select'
	),
	'post_date' => array(
		'name' => $lang['join_date'],
		'field' => 'Date',
		'Key' => 'Date'
	)
);
$rlSmarty -> assign_by_ref( 'sorting', $sorting );

/* register xajax functions */
$rlXajax -> registerFunction( array( 'getListings', $rlSearchByDistance, 'ajaxGetListings' ) );