<?php
if (!defined('IS_LOGIN')) {
    $sError = true;
} else {
    $reefless->loadClass('Banners', null, 'banners');
    if (isset($_GET['incomplete'])) {
        $id         = (int) $_GET['incomplete'];
        $step       = $_GET['step'];
        $bSteps     = $rlBanners->getSteps();
        $bannerInfo = $rlDb->getRow("SELECT `Plan_ID`, `Status` FROM `" . RL_DBPREFIX . "banners` WHERE `ID` = {$id}");
        if ($bannerInfo['Status'] == 'incomplete') {
            $_SESSION['add_banner']['plan_id']   = (int) $bannerInfo['Plan_ID'];
            $_SESSION['add_banner']['banner_id'] = $id;
            $url                                 = SEO_BASE;
            $url .= $config['mod_rewrite'] ? $pages['add_banner'] . '/' . $bSteps[$step]['path'] . '.html' : '?page=' . $pages['add_banner'] . '&step=' . $bSteps[$step]['path'];
            $reefless->redirect(null, $url);
            exit;
        } else {
            $sError = true;
        }
    }
    $rlXajax->registerFunction(array(
        'deleteBanner',
        $rlBanners,
        'ajaxDeleteBanner'
    ));
    if (!$_POST['xjxfun']) {
        unset($_SESSION['mb_deleted']);
    }
    $add_banner_href = $config['mod_rewrite'] ? SEO_BASE . $pages['add_banner'] . '.html' : RL_URL_HOME . 'index.php?page=' . $pages['add_banner'];
    $rlSmarty->assign('add_banner_href', $add_banner_href);
    $pInfo['current'] = (int) $_GET['pg'];
    $sorting          = array(
        'shows' => array(
            'name' => $lang['banners_bannerShows'],
            'field' => 'Shows'
        ),
        'clicks' => array(
            'name' => $lang['banners_bannerClicks'],
            'field' => 'Clicks'
        ),
        'status' => array(
            'name' => $lang['status'],
            'field' => 'Status'
        ),
        'expire_date' => array(
            'name' => $lang['expire_date'],
            'field' => 'Date_to'
        )
    );
    $rlSmarty->assign_by_ref('sorting', $sorting);
    $sort_by = empty($_GET['sort_by']) ? $_SESSION['mb_sort_by'] : $_GET['sort_by'];
    if (!empty($sorting[$sort_by])) {
        $order_field = $sorting[$sort_by]['field'];
    }
    $_SESSION['mb_sort_by'] = $sort_by;
    $rlSmarty->assign_by_ref('sort_by', $sort_by);
    $sort_type                = empty($_GET['sort_type']) ? $_SESSION['mb_sort_type'] : $_GET['sort_type'];
    $sort_type                = in_array($sort_type, array(
        'asc',
        'desc'
    )) ? $sort_type : false;
    $_SESSION['mb_sort_type'] = $sort_type;
    $rlSmarty->assign_by_ref('sort_type', $sort_type);
    if ($pInfo['current'] > 1) {
        $bread_crumbs[1]['title'] .= str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
    }
    $myBanners = $rlBanners->getMyBanners((int) $_SESSION['id'], $order_field, $sort_type, $pInfo['current'], $config['listings_per_page']);
    $rlSmarty->assign_by_ref('myBanners', $myBanners);
    if (!empty($myBanners)) {
        $rlSmarty->assign('navIcons', array(
            '<a class="button" title="" href="' . $add_banner_href . '">' . $lang['banners_addBanner'] . '</a>'
        ));
    }
    $pInfo['calc'] = $rlBanners->calc;
    $rlSmarty->assign_by_ref('pInfo', $pInfo);
}