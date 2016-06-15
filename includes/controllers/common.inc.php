<?php
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: store, no-cache, max-age=3600, must-revalidate");
$page_info['canonical'] = RL_URL_HOME;
$page_info['canonical'] .= $config['mod_rewrite'] ? parse_url(substr($_SERVER['REQUEST_URI'],1),PHP_URL_PATH) : "index.php?".$_SERVER['QUERY_STRING'];
$reefless->loadClass('Common');
$blocks = $rlCommon->getBlocks();
$rlSmarty->assign_by_ref('blocks', $blocks);
$block_keys = $rlCommon->block_keys;
if (false !== strpos($page_info['Key'], 'lt_')) {
    $listing_type_key = str_replace('lt_', '', $page_info['Key']);
}
$rlCommon->simulateCatBlocks();
if (!$_REQUEST['xjxfun']) {
    $rlCommon->buildMenus();
    $rlListings->buildFeaturedBoxes($listing_type_key);
    if ($block_keys['statistics']) {
        $rlListingTypes->statisticsBlock();
    }
    $bread_crumbs = $rlCommon->getBreadCrumbs($page_info);
    $message_info = $rlCommon->checkMessages();
    if (!empty($message_info)) {
        $rlSmarty->assign_by_ref('new_messages', $message_info);
    }
}
$rlHook->load('specialBlock');
if (in_array($page_info['Controller'], array(
    'home',
    'listing_type',
    'search'
))) {
    $rlXajax->registerFunction(array(
        'multiCatNext',
        $rlCategories,
        'ajaxMultiCatNext'
    ));
    $rlXajax->registerFunction(array(
        'multiCatBuild',
        $rlCategories,
        'ajaxMultiCatBuild'
    ));
}
function smartyEval($param, $content, &$smarty)
{
    return $content;
}
function insert_eval($params, &$smarty)
{
    require_once(RL_LIBS . 'smarty' . RL_DS . 'plugins' . RL_DS . 'function.eval.php');
    return smarty_function_eval(array(
        "var" => $params['content']
    ), $smarty);
}
$rlSmarty->register_block('eval', 'smartyEval', false);
$rlSmarty->register_function('str2path', array(
    'rlSmarty',
    'str2path'
));
$rlSmarty->register_function('str2money', array(
    'rlSmarty',
    'str2money'
));
$rlSmarty->register_function('paging', array(
    'rlSmarty',
    'paging'
));
$rlSmarty->register_function('search', array(
    'rlSmarty',
    'search'
));
$rlSmarty->register_function('rlHook', array(
    'rlHook',
    'load'
));
$rlSmarty->register_function('getTmpFile', array(
    'reefless',
    'getTmpFile'
));
$rlSmarty->register_function('encodeEmail', array(
    'rlSmarty',
    'encodeEmail'
));