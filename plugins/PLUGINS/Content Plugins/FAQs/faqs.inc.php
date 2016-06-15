<?php
if ($config['mod_rewrite']) {
    $path    = $rlValid->xSql($_GET['nvar_1']);
    $faqs_id = $rlDb->getOne('ID', "`Path` = '{$path}'", 'faqs');
} else {
    $faqs_id = (int) $_GET['id'];
}
$pInfo['current'] = (int) $_GET['pg'];
$reefless->loadClass('FAQs', null, 'FAQs');
if (empty($faqs_id)) {
    if ($pInfo['current'] > 1) {
        $bc_page = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
        $bread_crumbs[1]['title'] .= $bc_page;
    }
    $all_faqs = $rlFAQs->get(false, true, $pInfo['current'], true);
    $rlSmarty->assign_by_ref('all_faqs', $all_faqs);
    $pInfo['calc'] = $rlFAQs->calc_faqs;
    $rlSmarty->assign_by_ref('pInfo', $pInfo);
    $rlHook->load('faqsList');
} else {
    $faqs = $rlFAQs->get($faqs_id, true);
    $rlSmarty->assign('faqs', $faqs);
    $bread_crumbs[]    = array(
        'title' => $faqs['title']
    );
    $page_info['name'] = $faqs['title'];
    $rlHook->load('faqsItem');
}