<?php
if ($_GET['q'] == 'ext') {
    require_once('../../../includes/config.inc.php');
    require_once(RL_ADMIN_CONTROL . 'ext_header.inc.php');
    require_once(RL_LIBS . 'system.lib.php');
    if ($_GET['action'] == 'update') {
        $reefless->loadClass('Actions');
        $reefless->loadClass('ListingsCarousel', null, 'listings_carousel');
        $field      = $rlValid->xSql($_GET['field']);
        $value      = $rlValid->xSql(nl2br($_GET['value']));
        $key        = $rlValid->xSql($_GET['key']);
        $id         = (int) $_GET['id'];
        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );
        $rlActions->updateOne($updateData, 'listings_carousel');
        $rlListingsCarousel->updateCarouselBlock();
        exit;
    }
    $limit = (int) $_GET['limit'];
    $start = (int) $_GET['start'];
    $sql   = "SELECT SQL_CALC_FOUND_ROWS DISTINCT * ";
    $sql .= "FROM `" . RL_DBPREFIX . "listings_carousel` ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data  = $rlDb->getAll($sql);
    $count = $rlDb->getRow("SELECT FOUND_ROWS() AS `count`");
    $sql   = "SELECT `ID`,`Key` FROM `" . RL_DBPREFIX . "blocks` WHERE `Status` != 'trash' AND ( `Key` RLIKE 'listing_box_(.*)$'  OR `Key` RLIKE 'ltfb_(.*)$' ) ";
    $box   = $rlDb->getAll($sql);
    foreach ($box as $key => $val) {
        $block[$val['ID']] = 'blocks+name+' . $val['Key'];
    }
    foreach ($data as $key => $value) {
        $box_ids = explode(',', $data[$key]['Block_IDs']);
        foreach ($box_ids as $idKey => $idVal) {
            $data[$key]['Assigned_boxes'] .= $GLOBALS['lang'][$block[$idVal]] . ', ';
        }
        $data[$key]['Assigned_boxes'] = substr($data[$key]['Assigned_boxes'], 0, -2);
        $data[$key]['Direction']      = $GLOBALS['lang']['listings_carousel_' . $data[$key]['Direction']];
        $data[$key]['Status']         = $GLOBALS['lang'][$data[$key]['Status']];
    }
    $output['total'] = $count['count'];
    $output['data']  = $data;
    $reefless->loadClass('Json');
    echo $rlJson->encode($output);
    unset($output);
} else {
    $reefless->loadClass('ListingsCarousel', null, 'listings_carousel');
    $sql = "SELECT `T1`.`ID`,`T1`.`Side`,`T1`.`Key`, if(`T2`.`ID`, 1,0) AS `disabled`, `T2`.`ID` AS `Carousel_ID` ";
    $sql .= "FROM `" . RL_DBPREFIX . "blocks` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings_carousel` AS `T2` ON FIND_IN_SET(`T1`.`ID`, `T2`.`Block_IDs`) ";
    $sql .= "WHERE `T1`.`Status` != 'trash' AND ( `T1`.`Key` RLIKE 'listing_box_(.*)$'  OR `T1`.`Key` RLIKE 'ltfb_(.*)$' ) ";
    $box = $rlDb->getAll($sql);
    $rlSmarty->assign_by_ref('box', $box);
    if ($_GET['action'] == 'add' || $_GET['action'] == 'edit') {
        $id            = $rlValid->xSql($_GET['id']);
        $carousel_info = $rlDb->fetch('*', array(
            'Status' => 'active',
            'ID' => $id
        ), null, 1, 'listings_carousel', 'row');
        if ($_GET['action'] == 'add') {
            $bcAStep[] = array(
                'name' => $lang['add']
            );
        } else if ($_GET['action'] == 'edit') {
            $bcAStep[] = array(
                'name' => $lang['edit']
            );
        }
        if ($_GET['action'] == 'edit' && !$_POST['fromPost']) {
            $_POST['id']        = $carousel_info['ID'];
            $_POST['status']    = $carousel_info['Status'];
            $_POST['direction'] = $carousel_info['Direction'];
            $_POST['number']    = $carousel_info['Number'];
            $_POST['delay']     = $carousel_info['Delay'];
            $_POST['per_slide'] = $carousel_info['Per_slide'];
            $_POST['visible']   = $carousel_info['Visible'];
            $_POST['round']     = $carousel_info['Round'];
            $_POST['box']       = explode(',', $carousel_info['Block_IDs']);
        }
        if (isset($_POST['submit'])) {
            $errors = array();
            $f_box  = $_POST['box'];
            if (empty($f_box)) {
                $errors[]       = str_replace('{field}', "<b>" . $lang['listings_carousel_box'] . "</b>", $lang['notice_field_empty']);
                $error_fields[] = "box";
            }
            $f_number = $_POST['number'];
            if (empty($f_number)) {
                $errors[]       = str_replace('{field}', "<b>" . $lang['listings_carousel_number'] . "</b>", $lang['notice_field_empty']);
                $error_fields[] = "number";
            }
            $f_delay = $_POST['delay'];
            if (empty($f_delay)) {
                $errors[]       = str_replace('{field}', "<b>" . $lang['listings_carousel_delay'] . "</b>", $lang['notice_field_empty']);
                $error_fields[] = "delay";
            }
            $f_per_slide = $_POST['per_slide'];
            if (empty($f_per_slide)) {
                $errors[]       = str_replace('{field}', "<b>" . $lang['listings_carousel_per_slide'] . "</b>", $lang['notice_field_empty']);
                $error_fields[] = "per_slide";
            }
            $f_visible = $_POST['visible'];
            if (empty($f_visible)) {
                $errors[]       = str_replace('{field}', "<b>" . $lang['listings_carousel_visible'] . "</b>", $lang['notice_field_empty']);
                $error_fields[] = "visible";
            }
            if (!empty($errors)) {
                $rlSmarty->assign_by_ref('errors', $errors);
            } else {
                if ($_GET['action'] == 'add') {
                    $data = array(
                        'Direction' => $_POST['direction'],
                        'Block_IDs' => implode(',', $f_box),
                        'Number' => $f_number,
                        'Delay' => $f_delay,
                        'Per_slide' => $f_per_slide,
                        'Visible' => $f_visible,
                        'Round' => empty($_POST['round']) ? 0 : 1,
                        'Status' => $_POST['status']
                    );
                    if ($action = $rlActions->insertOne($data, 'listings_carousel')) {
                        $message = $lang['carousel_block_added'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    } else {
                        trigger_error("Can't add new block (MYSQL problems)", E_WARNING);
                        $rlDebug->logger("Can't add new block (MYSQL problems)");
                    }
                } elseif ($_GET['action'] == 'edit') {
                    $data_block = array(
                        'fields' => array(
                            'Direction' => $_POST['direction'],
                            'Block_IDs' => implode(',', $f_box),
                            'Number' => $f_number,
                            'Delay' => $f_delay,
                            'Per_slide' => $f_per_slide,
                            'Visible' => $f_visible,
                            'Round' => empty($_POST['round']) ? 0 : 1,
                            'Status' => $_POST['status']
                        ),
                        'where' => array(
                            'ID' => $_POST['id']
                        )
                    );
                    if ($action = $GLOBALS['rlActions']->updateOne($data_block, 'listings_carousel')) {
                        $message = $lang['carousel_block_edited'];
                        $aUrl    = array(
                            "controller" => $controller
                        );
                    }
                }
                if ($action) {
                    $rlListingsCarousel->updateCarouselBlock();
                    $reefless->loadClass('Notice');
                    $rlNotice->saveNotice($message);
                    $reefless->redirect($aUrl);
                }
            }
        }
    }
    $rlXajax->registerFunction(array(
        'deleteCarouselBox',
        $rlListingsCarousel,
        'ajaxDeleteCarouselBox'
    ));
}