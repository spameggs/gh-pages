<?php
header('Content-Type: application/json;charset=UTF-8;');
require_once('../../includes/config.inc.php');
require_once(RL_CLASSES . 'rlDb.class.php');
require_once(RL_CLASSES . 'reefless.class.php');
$rlDb     = new rlDb();
$reefless = new reefless();
$reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
$listing_id = (int) $_GET['id'];
$data       = $rlDb->fetch(array(
    'ID',
    'From',
    'To',
    'Price'
), array(
    'Listing_ID' => $listing_id
), "ORDER BY `From`", null, 'booking_rate_range');
if (!empty($data)) {
    $reefless->loadClass('Valid');
    $GLOBALS['config']['price_delimiter'] = ',';
    foreach ($data as $key => $rate) {
        $data[$key]['From']  = date('M d, Y', $rate['From']);
        $data[$key]['To']    = date('M d, Y', $rate['To']);
        $data[$key]['Price'] = $rlValid->str2money($data[$key]['Price']);
    }
}
$reefless->loadClass('Json');
echo $rlJson->encode(array(
    'ranges' => $data
));