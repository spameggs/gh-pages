<?php
class rlComment extends reefless
{
    function show_tab()
    {
        $GLOBALS['rlSmarty']->display(RL_PLUGINS . 'comment/comment.block.tpl');
    }
    function getComments($listing_id)
    {
        $listing_id = (int) $listing_id;
        $comments   = $this->fetch(array(
            'ID',
            'Listing_ID',
            'User_ID',
            'Title',
            'Description',
            'Author',
            'Date',
            'Status',
            'Rating'
        ), array(
            'Listing_ID' => $listing_id,
            'Status' => 'active'
        ), null, null, 'comments');
        foreach ($comments as $key => $comment) {
            $comments[$key]['Description'] = preg_replace('/(https?\:\/\/[^\s]+)/', '<a href="$1">$1</a>', $comment['Description']);
        }
        $GLOBALS['rlSmarty']->assign_by_ref('comments', $comments);
        $tpl = RL_PLUGINS . 'comment' . RL_DS . 'comment.block.tpl';
        $GLOBALS['rlSmarty']->display($tpl);
    }
    function ajaxCommentAdd($author, $title, $message, $security_code = false, $rating = 0)
    {
        global $_response, $page_info, $config, $listing_id, $lang, $account_info, $pages, $rlListingTypes, $rlSmarty;
        if (empty($author)) {
            $errors[] = str_replace('{field}', '<b>' . $lang['comment_author'] . '</b>', $lang['notice_field_empty']);
        }
        if (empty($title)) {
            $errors[] = str_replace('{field}', '<b>' . $lang['comment_title'] . '</b>', $lang['notice_field_empty']);
        }
        if (empty($message)) {
            $errors[] = str_replace('{field}', '<b>' . $lang['message'] . '</b>', $lang['notice_field_empty']);
        }
        if ($config['security_img_comment_captcha'] && $security_code != $_SESSION['ses_security_code_comment']) {
            $errors[] = $lang['security_code_incorrect'];
        }
        if (!empty($errors)) {
            $error_content = '<ul>';
            foreach ($errors as $error) {
                $error_content .= "<li>" . $error . "</li>";
            }
            $error_content .= '</ul>';
            $_response->script("
				printMessage('error', '{$error_content}');
				$('form[name=add_comment] input[type=submit]').val('{$lang['comment_add_comment']}');
			");
        } else {
            $this->loadClass('Actions');
            $this->setTable('comments');
            $listing_id = (int) $listing_id;
            $account_id = $account_info['ID'] ? $account_info['ID'] : 0;
            $status     = $config['comment_auto_approval'] ? 'active' : 'pending';
            function getClientIp()
            {
                $result       = null;
                $ipSourceList = array(
                    'HTTP_CLIENT_IP',
                    'HTTP_X_FORWARDED_FOR',
                    'HTTP_X_FORWARDED',
                    'HTTP_FORWARDED_FOR',
                    'HTTP_FORWARDED',
                    'REMOTE_ADDR'
                );
                foreach ($ipSourceList as $ipSource) {
                    if (isset($_SERVER[$ipSource])) {
                        $result = $_SERVER[$ipSource];
                        break;
                    }
                }
                return $result;
            }
            $user_ip = getClientIp();
            $comment = array(
                'Listing_ID' => $listing_id,
                'Author' => $author,
                'User_ID' => $account_id,
                'Title' => $title,
                'Description' => $message,
                'Rating' => (int) $rating,
                'Status' => $status,
                'Date' => 'NOW()'
            );
            $GLOBALS['rlActions']->insertOne($comment, 'comments');
            if ($config['comment_auto_approval']) {
                $this->query("UPDATE `" . RL_DBPREFIX . "listings` SET `comments_count` = `comments_count` + 1 WHERE `ID` = '{$listing_id}' LIMIT 1");
            }
            if ($config['comments_send_email_after_added_comment']) {
                $this->loadClass('Mail');
                $this->loadClass('Listings');
                $this->loadClass('Account');
                $mail_tpl      = $GLOBALS['rlMail']->getEmailTemplate('comment_email');
                $listing_info  = $GLOBALS['rlListings']->getListing($listing_id);
                $listing_type  = $rlListingTypes->types[$listing_info['Listing_type']];
                $account_info  = $GLOBALS['rlAccount']->getProfile((int) $listing_info['Account_ID']);
                $listing_title = $GLOBALS['rlListings']->getListingTitle($listing_info['Category_ID'], $listing_info, $listing_info['Listing_type']);
                $message       = nl2br($message);
                $link          = SEO_BASE;
                $link .= $config['mod_rewrite'] ? $pages[$listing_type['Page_key']] . '/' . $listing_info['Category_path'] . '/' . $rlSmarty->str2path($listing_title) . '-' . $listing_id . '.html#comments' : '?page=' . $pages[$listing_type['Page_key']] . '&amp;id=' . $listing_id . '#comments';
                $link             = '<a href="' . $link . '">' . $listing_title . '</a>';
                $mail_tpl['body'] = str_replace(array(
                    '{username}',
                    '{author}',
                    '{title}',
                    '{message}',
                    '{listing_title}'
                ), array(
                    $account_info['Full_name'],
                    $author,
                    $title,
                    $message,
                    $link
                ), $mail_tpl['body']);
                $GLOBALS['rlMail']->send($mail_tpl, $account_info['Mail']);
            }
            $comments = $this->fetch(array(
                'Author',
                'Date',
                'Description',
                'Title',
                'Rating'
            ), array(
                'Listing_ID' => $listing_id,
                'Status' => 'active'
            ));
            foreach ($comments as $key => $comment) {
                $comments[$key]['Description'] = preg_replace('/(https?\:\/\/[^\s]+)/', '<a href="$1">$1</a>', $comment['Description']);
            }
            $rlSmarty->assign_by_ref('comments', $comments);
            $tpl = RL_PLUGINS . "comment" . RL_DS . 'comment_dom.tpl';
            $_response->assign('comments_dom', 'innerHTML', $rlSmarty->fetch($tpl, null, null, false));
            if (!$config['comment_auto_approval']) {
                $mess = $lang['notice_comment_added_approval'];
            } else {
                $mess = $lang['notice_comment_added'];
            }
            $_response->script("
				printMessage('notice', '{$mess}');
				$('#comment_title').val(''), $('#comment_message').val(''), $('#comment_security_code').val('')
				$('.comment_star').removeClass('comment_star_active');
				comment_star = false;
			");
            $this->resetTable();
        }
        $_response->script("
			$('img#comment_security_img').attr('src', '" . RL_LIBS_URL . "kcaptcha/getImage.php?'+Math.random()+'&id=comment')
			$('form[name=add_comment] input[type=submit]').val('{$lang['comment_add_comment']}');
		");
        return $_response;
    }
    function ajaxDeleteComment($id = false)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false) {
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php?action=session_expired');
            return $_response;
        }
        $id = (int) $id;
        if (!$id) {
            return $_response;
        }
        $listing_id = $this->getOne('Listing_ID', "`ID` = '{$id}'", 'comments');
        $this->query("UPDATE `" . RL_DBPREFIX . "listings` SET `comments_count` = `comments_count` - 1 WHERE `ID` = '{$listing_id}' LIMIT 1");
        $this->query("DELETE FROM `" . RL_DBPREFIX . "comments` WHERE `ID` = '{$id}' LIMIT 1");
        $_response->script("
			commentsGrid.reload();
			printMessage('notice', '{$lang['item_deleted']}');
		");
        return $_response;
    }
    function selectCommentsInBlock()
    {
        global $rlListings, $rlSmarty, $rlCommon, $config, $rlListingTypes, $pages;
        $limit = $config['comments_number_comments'] ? $config['comments_number_comments'] : 5;
        $sql   = "SELECT `T2`.*, `T1`.`Author`, `T1`.`Title`, `T1`.`Description`, `T1`.`Date`, `T3`.`Type` AS `Listing_type`, `T3`.`Path` AS `Category_path` ";
        $sql .= "FROM `" . RL_DBPREFIX . "comments` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T2` ON `T1`.`Listing_ID` = `T2`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T3` ON `T2`.`Category_ID` = `T3`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T4` ON `T2`.`Plan_ID` = `T4`.`ID` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T5` ON `T2`.`Account_ID` = `T5`.`ID` ";
        $sql .= "WHERE `T1`.`Status` = 'active' AND `T2`.`Status` = 'active' AND `T3`.`Status` = 'active' AND `T5`.`Status` = 'active' ";
        $sql .= "AND ( UNIX_TIMESTAMP(DATE_ADD(`T2`.`Pay_date`, INTERVAL `T4`.`Listing_period` DAY)) > UNIX_TIMESTAMP(NOW()) OR `T4`.`Listing_period` = 0 )";
        if ($config['comments_select_comments_random'] == 'Last') {
            $sql .= "ORDER BY `T1`.`Date` DESC ";
        } else {
            $sql .= "ORDER BY RAND() ";
        }
        $sql .= "LIMIT {$limit}";
        $comments = $this->getAll($sql);
        if (!$comments)
            return false;
        foreach ($comments as $key => $comment) {
            $comments[$key]['Listing_title'] = $rlListings->getListingTitle($comment['Category_ID'], $comment, $comment['Listing_type']);
            $listing_type                    = $rlListingTypes->types[$comment['Listing_type']];
            $link                            = SEO_BASE;
            $link .= $config['mod_rewrite'] ? $pages[$listing_type['Page_key']] . '/' . $comment['Category_path'] . '/' . $rlSmarty->str2path($comments[$key]['Listing_title']) . '-' . $comment['ID'] . '.html#comments' : '?page=' . $pages[$listing_type['Page_key']] . '&amp;id=' . $comment['ID'] . '#comments';
            $comments[$key]['Listing_link'] = $link;
        }
        return $comments;
    }
    function apStatistics()
    {
        global $plugin_statistics, $lang;
        $total               = $this->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "comments`");
        $total               = $total['Count'];
        $pending             = $this->getRow("SELECT COUNT(`ID`) AS `Count` FROM `" . RL_DBPREFIX . "comments` WHERE `Status` = 'pending'");
        $pending             = $pending['Count'];
        $link                = RL_URL_HOME . ADMIN . '/index.php?controller=comment';
        $plugin_statistics[] = array(
            'name' => $lang['comment_tab'],
            'items' => array(
                array(
                    'name' => $lang['total'],
                    'link' => $link,
                    'count' => $total
                ),
                array(
                    'name' => $lang['pending'] . ' / ' . $lang['new'],
                    'link' => $link . '&amp;status=pending',
                    'count' => $pending
                )
            )
        );
    }
}