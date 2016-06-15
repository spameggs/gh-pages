<?php
$reefless->loadClass('Plan');
unset($_SESSION['complete_payment']);
if (isset($_GET['canceled'])) {
    $errors[] = $lang['notice_payment_canceled'];
}
$listing_id = (int) $_REQUEST['id'] ? (int) $_REQUEST['id'] : (int) $_REQUEST['item'];
if ($listing_id) {
    $sql = "SELECT `T1`.*, `T1`.`Category_ID`, `T1`.`Status`, UNIX_TIMESTAMP(`T1`.`Pay_date`) AS `Pay_date`, `T1`.`Crossed`, ";
    $sql .= "`T2`.`Type` AS `Listing_type`, `T2`.`Path` AS `Category_path`, `T1`.`Last_type` AS `Listing_mode` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
    $sql .= "WHERE `T1`.`ID` = {$listing_id} AND `T1`.`Account_ID` = '{$account_info['ID']}' ";
    $rlHook->load('upgradeListingSql', $sql);
    $sql .= "LIMIT 1";
    $listing = $rlDb->getRow($sql);
}
if (!$listing || !$listing_id) {
    $sError = true;
} else {
    $listing_type = $rlListingTypes->types[$listing['Listing_type']];
    $rlSmarty->assign_by_ref('listing_type', $listing_type);
    if (!$_POST['from_post']) {
        $_POST['plan'] = $listing['Plan_ID'];
    }
    if (!$_POST['listing_type']) {
        $_POST['listing_type'] = $listing['Listing_mode'];
    }
    $featured = isset($_GET['features']) || $_GET['nvar_1'] == 'featured' ? true : false;
    $rlSmarty->assign('featured', $featured);
    $plans = $rlPlan->getPlanByCategory($listing['Category_ID'], $account_info['Type'], $featured);
    foreach ($plans as $key => $value) {
        $tmp_plans[$value['ID']] = $value;
    }
    $plans = $tmp_plans;
    unset($tmp_plans);
    $rlSmarty->assign_by_ref('plans', $plans);
    $l_type         = $rlListingTypes->types[$listing['Listing_type']];
    $bc_last        = array_pop($bread_crumbs);
    $bread_crumbs[] = array(
        'name' => $lang['pages+name+my_' . $l_type['Key']],
        'title' => $lang['pages+title+my_' . $l_type['Key']],
        'path' => $pages['my_' . $l_type['Key']]
    );
    $bread_crumbs[] = $bc_last;
    if (isset($_GET['item'])) {
        $listing_title = $rlListings->getListingTitle($listing['Category_ID'], $listing, $listing['Listing_type']);
        $link          = SEO_BASE;
        $link .= $config['mod_rewrite'] ? $pages['lt_' . $l_type['Key']] . '/' . $listing['Category_path'] . '/' . $rlSmarty->str2path($listing_title) . '-' . $listing_id . '.html' : '?page=' . $pages['lt_' . $l_type['Key']] . '&amp;id=' . $listing_id;
        $rlSmarty->assign_by_ref('link', $link);
    } else {
        if ($featured) {
            $page_info['name']  = $lang['upgrade_to_featured'];
            $page_info['title'] = $lang['upgrade_to_featured'];
        }
        if ($_POST['upgrade']) {
            $plan_id           = (int) $_POST['plan'];
            $gateway           = $_POST['gateway'];
            $listing_mode      = $_POST['listing_type'];
            $plan_info         = $plans[$plan_id];
            $current_plan_info = $plans[$listing['Plan_ID']];
            $rlHook->load('phpListingsUpgradePlanInfo');
            if (empty($plan_id)) {
                $errors[] = $lang['notice_listing_plan_does_not_chose'];
            }
            if (!$gateway && $plan_info['Price'] > 0) {
                $errors[] = $lang['notice_payment_gateway_does_not_chose'];
            }
            if ($plan_info['Using'] != '' && $plan_info['Limit'] > 0) {
                $errors[] = $lang['plan_limit_using_hack'];
            }
            if ($plan_info['Package_ID'] && $listing_mode && ($plan_info[ucfirst($listing_mode) . '_remains'] <= 0 && $plan_info[ucfirst($listing_mode) . '_listings'] > 0)) {
                $errors[] = $lang['plan_option_using_hack'];
            }
            if (empty($errors)) {
                $reefless->loadClass('Mail');
                $reefless->loadClass('Notice');
                $reefless->loadClass('Actions');
                $listing_title = $rlListings->getListingTitle($listing['Category_ID'], $listing, $listing['Listing_type']);
                if ($plan_info['Type'] == 'featured') {
                    if ($plan_info['Price'] > 0) {
                        $item_name  = $lang[$plan_info['Type'] . '_plan'];
                        $cancel_url = SEO_BASE;
                        $cancel_url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html?canceled' : '?page=' . $page_info['Path'] . '&amp;canceled';
                        $cancel_url .= '&amp;item=' . $listing_id;
                        $success_url = SEO_BASE;
                        $success_url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html?completed' : '?page=' . $page_info['Path'] . '&amp;completed';
                        $success_url .= '&amp;item=' . $listing_id;
                        $complete_payment_info        = array(
                            'item_name' => $item_name . ' - ' . $lang['listing'] . ' #' . $listing_id . ' (' . $listing_title . ')',
                            'category_id' => $listing['Category_ID'],
                            'plan_info' => $plan_info,
                            'gateway' => $gateway,
                            'item_id' => $listing_id,
                            'account_id' => $account_info['ID'],
                            'callback' => array(
                                'class' => 'rlListings',
                                'method' => 'upgradeListing',
                                'cancel_url' => $cancel_url,
                                'success_url' => $success_url
                            )
                        );
                        $_SESSION['complete_payment'] = $complete_payment_info;
                        $redirect                     = SEO_BASE;
                        $redirect .= $config['mod_rewrite'] ? $pages['payment'] . '.html' : '?page=' . $pages['payment'];
                        $reefless->redirect(null, $redirect);
                        exit;
                    } else {
                        $upgrade_date = $plan_info['Price'] > 0 ? '' : 'NOW()';
                        $update       = array(
                            'fields' => array(
                                'Featured_ID' => $plan_info['ID'],
                                'Featured_date' => $upgrade_date
                            ),
                            'where' => array(
                                'ID' => $listing_id
                            )
                        );
                        if ($rlActions->updateOne($update, 'listings')) {
                            if ($plan_info['Limit'] > 0) {
                                if ($plan_info['Using'] == '') {
                                    $plan_using_insert = array(
                                        'Account_ID' => $account_info['ID'],
                                        'Plan_ID' => $plan_info['ID'],
                                        'Listings_remains' => $plan_info['Limit'] - 1,
                                        'Type' => 'limited',
                                        'Date' => 'NOW()',
                                        'IP' => $_SERVER['REMOTE_ADDR']
                                    );
                                    $rlActions->insertOne($plan_using_insert, 'listing_packages');
                                } else {
                                    $plan_using_update = array(
                                        'fields' => array(
                                            'Account_ID' => $account_info['ID'],
                                            'Plan_ID' => $plan_info['ID'],
                                            'Listings_remains' => $plan_info['Using'] - 1,
                                            'Type' => 'limited',
                                            'Date' => 'NOW()',
                                            'IP' => $_SERVER['REMOTE_ADDR']
                                        ),
                                        'where' => array(
                                            'ID' => $plan_info['Plan_using_ID']
                                        )
                                    );
                                    $rlActions->updateOne($plan_using_update, 'listing_packages');
                                }
                            }
                            $mail_tpl = $rlMail->getEmailTemplate('listing_upgraded_to_featured');
                            $link     = SEO_BASE;
                            $link .= $config['mod_rewrite'] ? $pages['lt_' . $l_type['Key']] . '/' . $listing['Category_path'] . '/' . $rlSmarty->str2path($listing_title) . '-' . $listing_id . '.html' : '?page=' . $pages['lt_' . $l_type['Key']] . '&amp;id=' . $listing_id;
                            $_SESSION['notice_link'] = $link;
                            $find                    = array(
                                '{listing}',
                                '{plan_name}',
                                '{plan_price}',
                                '{start_date}',
                                '{expiration_date}'
                            );
                            $replace                 = array(
                                '<a href="' . $link . '">' . $listing_title . '</a>',
                                $plan_info['name'],
                                $lang['free'],
                                date(str_replace(array(
                                    'b',
                                    '%'
                                ), array(
                                    'M',
                                    ''
                                ), RL_DATE_FORMAT)),
                                date(str_replace(array(
                                    'b',
                                    '%'
                                ), array(
                                    'M',
                                    ''
                                ), RL_DATE_FORMAT), strtotime('+' . $plan_info['Listing_period'] . ' days'))
                            );
                            $mail_tpl['body']        = str_replace($find, $replace, $mail_tpl['body']);
                            $mail_tpl['body']        = preg_replace('/\{if.*\{\/if\}(<br\s+\/>)?/', '', $mail_tpl['body']);
                            $rlMail->send($mail_tpl, $account_info['Mail']);
                            $mail_tpl         = $rlMail->getEmailTemplate('listing_upgraded_to_featured_for_admin');
                            $link             = RL_URL_HOME . ADMIN . '/index.php?controller=listings&amp;action=view&amp;id=' . $listing_id;
                            $find             = array(
                                '{listing}',
                                '{plan_name}',
                                '{listing_id}',
                                '{owner}',
                                '{start_date}',
                                '{expiration_date}'
                            );
                            $replace          = array(
                                '<a href="' . $link . '">' . $listing_title . '</a>',
                                $plan_info['name'],
                                $listing_id,
                                $account_info['Full_name'],
                                date(str_replace(array(
                                    'b',
                                    '%'
                                ), array(
                                    'M',
                                    ''
                                ), RL_DATE_FORMAT)),
                                date(str_replace(array(
                                    'b',
                                    '%'
                                ), array(
                                    'M',
                                    ''
                                ), RL_DATE_FORMAT), strtotime('+' . $plan_info['Listing_period'] . ' days'))
                            );
                            $mail_tpl['body'] = str_replace($find, $replace, $mail_tpl['body']);
                            $rlMail->send($mail_tpl, $config['notifications_email']);
                        }
                    }
                } elseif (($plan_info['Type'] == 'package' && !$plan_info['Listings_remains'] && $plan_info['Price'] > 0) || ($plan_info['Type'] == 'listing' && $plan_info['Price'] > 0)) {
                    $update_plan_id     = $plan_info['ID'];
                    $update_featured_id = ($plan_info['Featured'] && !$plan_info['Advanced_mode']) || ($plan_info['Advanced_mode'] && $listing_mode == 'featured') ? $plan_info['ID'] : '';
                    $item_name          = $lang[$plan_info['Type'] . '_plan'];
                    $cancel_url         = SEO_BASE;
                    $cancel_url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html?canceled' : '?page=' . $page_info['Path'] . '&amp;canceled';
                    $cancel_url .= '&amp;item=' . $listing_id;
                    $success_url = SEO_BASE;
                    $success_url .= $config['mod_rewrite'] ? $page_info['Path'] . '.html?completed' : '?page=' . $page_info['Path'] . '&amp;completed';
                    $success_url .= '&amp;item=' . $listing_id;
                    $complete_payment_info        = array(
                        'item_name' => $item_name . ' - ' . $lang['listing'] . ' #' . $listing_id . ' (' . $listing_title . ')',
                        'category_id' => $category['ID'],
                        'plan_info' => $plan_info,
                        'gateway' => $gateway,
                        'item_id' => $listing_id,
                        'account_id' => $account_info['ID'],
                        'callback' => array(
                            'class' => 'rlListings',
                            'method' => 'upgradeListing',
                            'cancel_url' => $cancel_url,
                            'success_url' => $success_url
                        )
                    );
                    $_SESSION['complete_payment'] = $complete_payment_info;
                    $redirect                     = SEO_BASE;
                    $redirect .= $config['mod_rewrite'] ? $pages['payment'] . '.html' : '?page=' . $pages['payment'];
                    $reefless->redirect(null, $redirect);
                    exit;
                } elseif (($plan_info['Type'] == 'package' && ($plan_info['Package_ID'] || $plan_info['Price'] <= 0)) || ($plan_info['Type'] == 'listing' && $plan_info['Price'] <= 0)) {
                    $update_featured_id    = ($plan_info['Featured'] && !$plan_info['Advanced_mode']) || $listing_mode == 'featured' ? $plan_info['ID'] : '';
                    $upgrade_featured_date = ($plan_info['Featured'] && !$plan_info['Advanced_mode']) || $listing_mode == 'featured' ? 'IF(UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_ADD(`Featured_date`, INTERVAL ' . $plan_info['Listing_period'] . ' DAY)) OR UNIX_TIMESTAMP(`Featured_date`) = 0, NOW(), DATE_ADD(`Featured_date`, INTERVAL ' . $plan_info['Listing_period'] . ' DAY))' : '';
                    $upgrade_date          = 'IF(UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_ADD(`Pay_date`, INTERVAL ' . $plan_info['Listing_period'] . ' DAY)) OR UNIX_TIMESTAMP(`Pay_date`) = 0, NOW(), DATE_ADD(`Pay_date`, INTERVAL ' . $plan_info['Listing_period'] . ' DAY))';
                    $update                = array(
                        'fields' => array(
                            'Plan_ID' => $plan_info['ID'],
                            'Pay_date' => $upgrade_date,
                            'Featured_ID' => $update_featured_id,
                            'Featured_date' => $upgrade_featured_date,
                            'Last_type' => $listing_mode
                        ),
                        'where' => array(
                            'ID' => $listing_id
                        )
                    );
                    if ($listing['Status'] == 'incomplete' && $listing['Last_step'] == 'checkout') {
                        $update['fields']['Status']    = $config['listing_auto_approval'] ? 'active' : 'pending';
                        $update['fields']['Last_step'] = '';
                    }
                    if ($listing['Status'] == 'expired') {
                        $update['fields']['Status'] = 'active';
                    }
                    if ($rlActions->updateOne($update, 'listings')) {
                        if ($plan_info['Type'] == 'package' && $plan_info['Package_ID']) {
                            if ($plan_info['Listings_remains'] != 0) {
                                $update_entry = array(
                                    'fields' => array(
                                        'Listings_remains' => $plan_info['Listings_remains'] - 1
                                    ),
                                    'where' => array(
                                        'ID' => $plan_info['Package_ID']
                                    )
                                );
                                if ($plan_info[ucfirst($listing_mode) . '_listings'] != 0) {
                                    $update_entry['fields'][ucfirst($listing_mode) . '_remains'] = $plan_info[ucfirst($listing_mode) . '_remains'] - 1;
                                }
                                $rlActions->updateOne($update_entry, 'listing_packages');
                            } else {
                                echo "Logic error occurred...";
                                exit;
                            }
                        } elseif ($plan_info['Type'] == 'package' && !$plan_info['Package_ID'] && $plan_info['Price'] <= 0) {
                            $insert_entry = array(
                                'Account_ID' => $account_info['ID'],
                                'Plan_ID' => $plan_info['ID'],
                                'Listings_remains' => $plan_info['Listing_number'] - 1,
                                'Type' => 'package',
                                'Date' => 'NOW()',
                                'IP' => $_SERVER['REMOTE_ADDR']
                            );
                            if ($plan_info['Featured'] && $plan_info['Advanced_mode'] && $plan_info['Standard_listings']) {
                                $insert_entry['Standard_remains'] = $plan_info['Standard_listings'] - 1;
                            }
                            if ($plan_info['Featured'] && $plan_info['Advanced_mode'] && $plan_info['Featured_listings']) {
                                $insert_entry['Featured_remains'] = $plan_info['Featured_listings'] - 1;
                            }
                            $rlActions->insertOne($insert_entry, 'listing_packages');
                        } elseif ($plan_info['Type'] == 'listing' && $plan_info['Limit'] > 0) {
                            if (empty($plan_info['Using'])) {
                                $plan_using_insert = array(
                                    'Account_ID' => $account_info['ID'],
                                    'Plan_ID' => $plan_info['ID'],
                                    'Listings_remains' => $plan_info['Limit'] - 1,
                                    'Type' => 'limited',
                                    'Date' => 'NOW()',
                                    'IP' => $_SERVER['REMOTE_ADDR']
                                );
                                $GLOBALS['rlActions']->insertOne($plan_using_insert, 'listing_packages');
                            } else {
                                $plan_using_update = array(
                                    'fields' => array(
                                        'Account_ID' => $account_info['ID'],
                                        'Plan_ID' => $plan_info['ID'],
                                        'Listings_remains' => $plan_info['Using'] - 1,
                                        'Type' => 'limited',
                                        'Date' => 'NOW()',
                                        'IP' => $_SERVER['REMOTE_ADDR']
                                    ),
                                    'where' => array(
                                        'ID' => $plan_info['Plan_using_ID']
                                    )
                                );
                                $GLOBALS['rlActions']->updateOne($plan_using_update, 'listing_packages');
                            }
                        }
                        if (!$plan_info['Image_unlim'] && $plan_info['Image'] < $listing['Photos_count'] && $plan_info['Type'] != 'featured') {
                            $photos_count_update = array(
                                'fields' => array(
                                    'Photos_count' => $plan_info['Image']
                                ),
                                'where' => array(
                                    'ID' => $listing['ID']
                                )
                            );
                            $GLOBALS['rlActions']->updateOne($photos_count_update, 'listings');
                        }
                        if ($config['listing_auto_approval']) {
                            $rlCategories->listingsIncrease($category['ID']);
                        }
                        $mail_tpl = $rlMail->getEmailTemplate(($config['listing_auto_approval'] || $listing['Status'] == 'active') ? 'listing_upgraded_active' : 'listing_upgraded_approval');
                        $link     = SEO_BASE;
                        if ($config['listing_auto_approval']) {
                            $link .= $config['mod_rewrite'] ? $pages['lt_' . $listing_type['Key']] . '/' . $listing['Category_path'] . '/' . $rlSmarty->str2path($listing_title) . '-' . $listing_id . '.html' : '?page=' . $pages['lt_' . $listing_type['Key']] . '&amp;id=' . $listing_id;
                        } else {
                            $link .= $config['mod_rewrite'] ? $pages['my_' . $listing_type['Key']] . '.html' : '?page=' . $pages['my_' . $listing_type['Key']];
                        }
                        $mail_tpl['body'] = str_replace(array(
                            '{username}',
                            '{link}',
                            '{plan}'
                        ), array(
                            $account_info['Username'],
                            '<a href="' . $link . '">' . $link . '</a>',
                            $plan_info['name']
                        ), $mail_tpl['body']);
                        $rlMail->send($mail_tpl, $account_info['Mail']);
                    }
                }
                if (!$plan_info['Cross'] && $current_plan_info['Cross'] && $listing['Crossed']) {
                    foreach (explode(',', $listing['Crossed']) as $crossed_category_id) {
                        $rlCategories->listingsDecrease($crossed_category_id);
                    }
                    $sql = "UPDATE `" . RL_DBPREFIX . "listings` SET `Crossed` = '' WHERE `ID` = '{$listing['ID']}'";
                    $rlDb->query($sql);
                }
                $mail_tpl = $rlMail->getEmailTemplate('admin_listing_added');
                $link     = SEO_BASE;
                $link .= $config['mod_rewrite'] ? $pages['lt_' . $category['Type']] . '/' . $category['Path'] . '/' . $listing_title . '-' . $listing_id . '.html' : '?page=' . $pages['lt_' . $category['Type']] . '&amp;id=' . $listing_id;
                $m_find           = array(
                    '{username}',
                    '{link}',
                    '{date}',
                    '{status}',
                    '{paid}'
                );
                $m_replace        = array(
                    $_SESSION['account']['Username'],
                    '<a href="' . RL_URL_HOME . ADMIN . '/index.php?controller=listings&amp;action=view&amp;id=' . $listing_id . '">' . $listing_title . '</a>',
                    date(str_replace(array(
                        'b',
                        '%'
                    ), array(
                        'M',
                        ''
                    ), RL_DATE_FORMAT)),
                    $lang['suspended'],
                    $lang['not_paid']
                );
                $mail_tpl['body'] = str_replace($m_find, $m_replace, $mail_tpl['body']);
                if ($config['listing_auto_approval']) {
                    $mail_tpl['body'] = preg_replace('/\{if activation is enabled\}(.*)\{\/if\}/', '', $mail_tpl['body']);
                } else {
                    $activation_link  = RL_URL_HOME . ADMIN . '/index.php?controller=listings&amp;action=remote_activation&amp;id=' . $listing_id . '&amp;hash=' . md5($rlDb->getOne('Date', "`ID` = '{$listing_id}'", 'listings'));
                    $activation_link  = '<a href="' . $activation_link . '">' . $activation_link . '</a>';
                    $mail_tpl['body'] = preg_replace('/(\{if activation is enabled\})(.*)(\{activation_link\})(.*)(\{\/if\})/', '$2 ' . $activation_link . ' $4', $mail_tpl['body']);
                }
                $rlNotice->saveNotice($lang['notice_listing_upgraded']);
                $url = SEO_BASE;
                $url .= $config['mod_rewrite'] ? $pages[$listing_type['My_key']] . '.html' : '?page=' . $pages[$listing_type['My_key']];
                $reefless->redirect(null, $url);
            }
        }
    }
}