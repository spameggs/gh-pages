<?php
include_once(dirname(__FILE__) . "/../../includes/config.inc.php");
require_once(RL_INC . 'control.inc.php');
$url    = $_GET['url'];
$number = $_GET['number'];
if ($url && $number) {
    $content = $reefless->getPageContent($url);
    if ($content) {
        $reefless->loadClass('Rss');
        $reefless->loadClass('Json');
        $rlRss->items_number = $number;
        $rlRss->createParser($content);
        $rss_feeds = $rlRss->getRssContent();
        $rlRss->clear();
        $output['total'] = count($rss_feeds);
        $output['data']  = $rss_feeds;
        echo $rlJson->encode($output);
    } else {
        echo false;
    }
} else {
    echo false;
}