<?php
$reefless->loadClass('Compare', false, 'compare');
$reefless->loadClass('Actions');
if (($_GET['nvar_1'] && $_GET['nvar_1'] != 'save') || $_GET['id']) {
    $where      = $_GET['nvar_1'] ? array(
        'Path' => $_GET['nvar_1']
    ) : array(
        'ID' => $_GET['id']
    );
    $saved_list = $_GET['nvar_1'] ? $_GET['nvar_1'] : $_GET['id'];
    $item       = $rlDb->fetch(array(
        'Name',
        'IDs',
        'Type'
    ), $where, null, 1, 'compare_table', 'row');
    if ($item) {
        if ($item['Type'] == 'private' && !defined('IS_LOGIN')) {
            $errors[] = $lang['compare_table_private_only'];
        } else {
            $rlSmarty->assign_by_ref('saved_list', $saved_list);
            $compare_ids       = $item['IDs'];
            $page_info['name'] = $item['Name'];
        }
    } else {
        $errors[] = $lang['compare_table_not_found'];
    }
} else if (($_GET['nvar_1'] && $_GET['nvar_1'] == 'save') || isset($_GET['save'])) {
    $save_mode = true;
    $rlSmarty->assign('save_mode', true);
    $page_info['name'] = $lang['compare_save_results'];
    if (!defined('IS_LOGIN')) {
        $rlSmarty->assign('pAlert', $lang['compare_save_table_login_notice']);
    } else {
        if ($_POST['action'] == 'save') {
            if (empty($_POST['name'])) {
                $errors[] = str_replace('{field}', '<b>' . $lang['name'] . '</b>', $lang['notice_field_empty']);
            }
            if (!$errors) {
                loadUTF8functions('ascii', 'utf8_to_ascii', 'unicode');
                $path      = !utf8_is_ascii($_POST['name']) ? utf8_to_ascii($_POST['name']) : $_POST['name'];
                $full_name = !utf8_is_ascii($account_info['Full_name']) ? utf8_to_ascii($account_info['Full_name']) : $account_info['Full_name'];
                $path      = $rlSmarty->str2path($full_name) . '-' . $rlSmarty->str2path($path);
                $insert    = array(
                    'Name' => $_POST['name'],
                    'Path' => $rlCompare->checkPath($path),
                    'Account_ID' => $account_info['ID'],
                    'IDs' => $_COOKIE['compare_listings'],
                    'Type' => $_POST['type'] == 'public' ? 'public' : 'private',
                    'Date' => 'NOW()'
                );
                if ($rlActions->insertOne($insert, 'compare_table')) {
                    $saved_table_id = mysql_insert_id();
                    setcookie('compare_listings', '', time() - 553600, '/');
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice(str_repeat('{name}', $_POST['name'], $lang['compare_save_completed_notice']));
                    $url = SEO_BASE;
                    $url .= $config['mod_rewrite'] ? $pages['compare_listings'] . '/' . $path . '.html' : '?page=' . $pages['compare_listings'] . '&id=' . $saved_table_id;
                    $reefless->redirect(null, $url);
                } else {
                    $sError = true;
                    $GLOBALS['rlDebug']->logger("Unable to insert table comparion results to DB");
                }
            }
        }
    }
} else {
    $compare_ids = $_COOKIE['compare_listings'];
}
$rlCompare->get($compare_ids);
if (defined('IS_LOGIN')) {
    $saved_tables = $rlDb->fetch(array(
        'Name',
        'IDs',
        'Type',
        'Path',
        'ID'
    ), array(
        'Account_ID' => $account_info['ID']
    ), "ORDER BY `Date` DESC", null, 'compare_table');
    $rlSmarty->assign_by_ref('saved_tables', $saved_tables);
    $rlXajax->registerFunction(array(
        'removeSavedItem',
        $rlCompare,
        'ajaxRemoveSavedItem'
    ));
    $rlXajax->registerFunction(array(
        'removeTable',
        $rlCompare,
        'ajaxRemoveTable'
    ));
}
if (!$save_mode && $rlSmarty->get_template_vars('compare_listings')) {
    $navIcons[] = '<a class="button compare_fullscreen" title="' . $lang['compare_fullscreen'] . '" href="javascript:void(0)"><span>' . $lang['compare_fullscreen'] . '</span></a>';
    $rlSmarty->assign_by_ref('navIcons', $navIcons);
}