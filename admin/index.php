<?php
if (isset($_GET['system_info'])) {
    phpinfo();
    exit;
}
require_once('..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.inc.php');
require_once(RL_ADMIN_CONTROL . 'admin.control.inc.php');
$reefless->wwwRedirect(true);
$rlHook->load('apBoot');
$config = $rlConfig->allConfig();
$rlSmarty->assign_by_ref('config', $config);
$reefless->loadClass('Cache');
$st_path = RL_ROOT . 'templates' . RL_DS . $config['template'] . RL_DS . 'settings.tpl.php';
if (is_readable($st_path)) {
    require_once($st_path);
}
$rlDb->setTable('languages');
$languages = $rlLang->getLanguagesList();
$rlLang->defineLanguage($_GET['language']);
$rlLang->modifyLanguagesList($languages);
$lang            = $rlLang->getLangBySide('admin', RL_LANG_CODE);
$GLOBALS['lang'] = $lang;
$rlSmarty->assign_by_ref('lang', $lang);
$reefless->loginAttempt(true);
require_once(RL_LIBS . 'system.lib.php');
$reefless->setTimeZone();
$pages = $GLOBALS['pages'] = $rlAdmin->getAllPages();
$rlSmarty->assign_by_ref('pages', $pages);
$ext_phrases = $rlLang->getLangBySide('ext', RL_LANG_CODE);
$rlSmarty->assign_by_ref('ext_phrases', $ext_phrases);
$rlSmarty->assign('rlBase', RL_URL_HOME . ADMIN . '/');
$rlSmarty->assign('rlBaseC', RL_URL_HOME . ADMIN . '/index.php?controller=' . $_GET['controller'] . '&amp;');
$rlSmarty->assign('rlTplBase', RL_URL_HOME . ADMIN . '/');
define('RL_TPL_BASE', RL_URL_HOME . ADMIN . '/');
if (!$rlAdmin->isLogin()) {
    $rlSmarty->assign_by_ref('languages', $languages);
    $rlSmarty->assign('langCount', count($languages));
    $reefless->loadClass('Admin', 'admin');
    $rlXajax->processRequest();
    ob_start();
    $rlXajax->printJavascript();
    $ajax_javascripts = ob_get_contents();
    ob_end_clean();
    $rlSmarty->assign_by_ref('ajaxJavascripts', $ajax_javascripts);
    $rlSmarty->display('login.tpl');
    $_SESSION['query_string'] = $_SERVER['QUERY_STRING'];
    $rlHook->load('apNotLogin');
    exit;
}
$reefless->loadClass('ListingTypes');
if (!$_REQUEST['xjxfun']) {
    $mMenuItems = $rlAdmin->getMainMenuItems();
    $rlSmarty->assign_by_ref('mMenuItems', $mMenuItems);
    $mMenu_controllers = $rlAdmin->mMenu_controllers;
    $rlSmarty->assign_by_ref('mMenu_controllers', $mMenu_controllers);
    $menu_icons = array(
        'common' => -97,
        'listings' => -116,
        'categories' => -135,
        'plugins' => -154,
        'forms' => -173,
        'account' => -192,
        'content' => -211
    );
    $rlSmarty->assign_by_ref('menu_icons', $menu_icons);
    if (!isset($_POST['xjxfun'])) {
        $ses_exp = session_cache_expire() - 5;
        if (isset($_SESSION['admin_expire_time']) && $_SERVER['REQUEST_TIME'] - $_SESSION['admin_expire_time'] > $ses_exp * 60) {
            session_destroy();
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $reefless->redirect(null, $redirect_url);
        } else {
            $_SESSION['admin_expire_time'] = $_SERVER['REQUEST_TIME'];
        }
    }
}
$rlAdmin->checkNewMessages();
$controller = $_GET['controller'];
if (empty($controller)) {
    $controller = 'home';
}
$cInfo = $rlAdmin->getController($controller);
if ($_SESSION['sessAdmin']['type'] == 'super') {
    $_SESSION['sessAdmin']['rights'][$cInfo['Key']] = array(
        'add' => 'add',
        'edit' => 'edit',
        'delete' => 'delete'
    );
    $_SESSION['sessAdmin']['rights']['listings']    = array(
        'add' => 'add',
        'edit' => 'edit',
        'delete' => 'delete'
    );
    $_SESSION['sessAdmin']['rights']['categories']  = array(
        'add' => 'add',
        'edit' => 'edit',
        'delete' => 'delete'
    );
}
if (($_SESSION['sessAdmin']['rights'][$cInfo['Key']] || $_SESSION['sessAdmin']['type'] == 'super') || $controller == 'home') {
    $controlFile = $cInfo['Plugin'] ? RL_PLUGINS . $cInfo['Plugin'] . RL_DS . 'admin' . RL_DS . $controller . ".inc.php" : RL_ADMIN_CONTROL . $controller . ".inc.php";
    if (file_exists($controlFile)) {
        require_once($controlFile);
        if ($sError === true) {
            $cInfo['Controller'] = '404';
            $rlSmarty->assign('errors', array(
                $GLOBALS['lang']['error_404']
            ));
        }
    } else {
        $cInfo['Controller'] = '404';
        $rlSmarty->assign('errors', array(
            $GLOBALS['lang']['error_404']
        ));
    }
    $rlSmarty->assign_by_ref('errors', $errors);
} else {
    $cInfo['Controller'] = '404';
    $rlSmarty->assign('errors', array(
        str_replace('{manager}', '<b>' . $cInfo['name'] . '</b>', $lang['admin_access_denied'])
    ));
}
if (!$_REQUEST['xjxfun']) {
    $extended_sections = array(
        'admins',
        'languages',
        'data_formats',
        'listings',
        'listing_fields',
        'listing_types',
        'listing_sections',
        'listing_groups',
        'listing_plans',
        'plans_using',
        'categories',
        'all_accounts',
        'account_types',
        'map_amenities',
        'account_fields',
        'pages',
        'news',
        'blocks',
        'saved_searches'
    );
    $rlSmarty->assign_by_ref('extended_sections', $extended_sections);
    $extended_modes = array(
        'add',
        'edit',
        'delete'
    );
    $rlSmarty->assign_by_ref('extended_modes', $extended_modes);
    $rlSmarty->assign_by_ref('cInfo', $cInfo);
    $rlSmarty->assign_by_ref('aRights', $_SESSION['sessAdmin']['rights']);
    $rlSmarty->assign_by_ref('cKey', $cInfo['Key']);
    $breadCrumbs = $rlAdmin->getBreadCrumbs($cInfo['ID'], $bcAStep, array(), $cInfo['Plugin']);
    $rlSmarty->assign_by_ref('breadCrumbs', $breadCrumbs);
    $rlSmarty->assign_by_ref('error_fields', $error_fields);
    if (isset($_SESSION['admin_notice'])) {
        $pNotice = $_SESSION['admin_notice'];
        $rlSmarty->assign_by_ref('pNotice', $pNotice);
        $rlNotice->resetNotice();
    }
}
if (RL_DB_DEBUG) {
    echo '<br /><br />Total sql queries time: <b>' . $_SESSION['sql_debug_time'] . '</b>.<br />';
}
$rlHook->load('apPhpIndexBottom');
$rlXajax->processRequest();
ob_start();
$rlXajax->printJavascript();
$ajax_javascripts = ob_get_contents();
ob_end_clean();
$rlSmarty->assign_by_ref('ajaxJavascripts', $ajax_javascripts);
if (!$_REQUEST['xjxfun']) {
    $rlSmarty->display('index.tpl');
}