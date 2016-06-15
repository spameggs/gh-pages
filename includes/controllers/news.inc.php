<?php
if ($config['mod_rewrite']) {
    $path    = $rlValid->xSql($_GET['nvar_1']);
    $news_id = $rlDb->getOne('ID', "`Path` = '{$path}'", 'news');
} else {
    $news_id = (int) $_GET['id'];
}
$pInfo['current'] = (int) $_GET['pg'];
$reefless->loadClass('News');
if (empty($news_id)) {
    if ($pInfo['current'] > 1) {
        $bc_page = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
        $bread_crumbs[1]['title'] .= $bc_page;
    }
    $all_news = $rlNews->get(false, true, $pInfo['current']);
    $rlSmarty->assign_by_ref('all_news', $all_news);
    $pInfo['calc'] = $rlNews->calc_news;
    $rlSmarty->assign_by_ref('pInfo', $pInfo);
    $rlHook->load('newsList');
    $rss = array(
        'item' => 'news',
        'title' => $lang['pages+name+' . $pages['news']]
    );
    $rlSmarty->assign_by_ref('rss', $rss);
} else {
    $news = $rlNews->get($news_id, true);
    $rlSmarty->assign('news', $news);
    $page_info['meta_description'] = $news['meta_description'];
    $page_info['meta_keywords']    = $news['meta_keywords'];
    $bread_crumbs[]                = array(
        'title' => $news['title']
    );
    $page_info['name']             = $news['title'];
    $rlHook->load('newsItem');
}