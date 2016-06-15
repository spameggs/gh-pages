<?php
class rlVBulletin extends reefless
{
    function rlVBulletin()
    {
        error_reporting(E_ERROR);
        ini_set("display_errors", "0");
    }
    function getPath()
    {
        global $config, $lang;
        if (empty($config['vbulletin_destination']) || empty($config['vbulletin_path']))
            return false;
        $dir  = trim($config['vbulletin_path'], RL_DS);
        $path = false;
        switch ($config['vbulletin_destination']) {
            case 'root':
                $path = RL_DS . $dir . RL_DS;
                break;
            case 'inside':
                $path = RL_ROOT . $dir . RL_DS;
                break;
            case 'outsite':
                $path = str_replace($dir, '', RL_ROOT);
                $path = RL_DS . trim($path, RL_DS) . RL_DS;
                break;
            case 'parallel':
                $part = explode(',', $dir, 2);
                if (count($part) === 2) {
                    $path = str_replace($part[0], $part[1], RL_ROOT);
                }
                break;
        }
        return $path;
    }
    function vbInit($loginFunc = false)
    {
        global $vbulletin, $config;
        if (is_object($vbulletin)) {
            $this->reConnect('forum');
            return $vbulletin;
        }
        $forumDir = $this->getPath();
        if (!is_dir($forumDir)) {
            return false;
        }
        define('VB_AREA', 'Flynax');
        define('SKIP_SESSIONCREATE', 1);
        define('SKIP_USERINFO', 1);
        chdir($forumDir);
        require_once('global.php');
        chdir(RL_ROOT);
        if (is_object($vbulletin)) {
            $this->reConnect('forum');
            return $vbulletin;
        }
        return false;
    }
    function ajaxInstallProduct()
    {
        global $_response, $lang, $vbulletin;
        if (false === $vbulletin = $this->vbInit())
            return false;
        $product = "INSERT INTO `" . TABLE_PREFIX . "product` ( `productid`, `title`, `description`, `version`, `active`, `url` ) VALUES ";
        $product .= "( 'flynax', 'vBulletin Bridge', 'Plugin for vBulletin', '*', '1', '#' )";
        $vbulletin->db->query_write($product);
        $path    = RL_PLUGINS . 'vbulletin' . RL_DS . 'vb_hooks' . RL_DS;
        $plugins = "INSERT INTO `" . TABLE_PREFIX . "plugin` ( `title`, `hookname`, `phpcode`, `product`, `active`, `executionorder` ) VALUES ";
        $plugins .= "( 'Login', 'login_verify_success', 'require_once(\"{$path}login_verify_success.php\");', 'flynax', '1', '1' ),";
        $plugins .= "( 'Logout', 'logout_process', 'require_once(\"{$path}logout_process.php\");', 'flynax', '1', '1' ),";
        $plugins .= "( 'Registration', 'register_addmember_complete', 'require_once(\"{$path}register_addmember_complete.php\");', 'flynax', '1', '1' )";
        $vbulletin->db->query_write($plugins);
        vBulletinHook::build_datastore($vbulletin->db);
        $_response->script("printMessage('notice', '{$lang['vbulletin_installProductNotice']}');");
        $_response->script("$('#install_product_dom').html('<b>{$lang['active']}</b>');");
        return $_response;
    }
    function unIstallProduct()
    {
        global $vbulletin;
        if (false === $vbulletin = $this->vbInit())
            return false;
        $product = "DELETE FROM `" . TABLE_PREFIX . "product` WHERE `productid` = 'flynax'";
        $vbulletin->db->query_write($product);
        $plugins = "DELETE FROM `" . TABLE_PREFIX . "plugin` WHERE `product` = 'flynax'";
        $vbulletin->db->query_write($plugins);
        vBulletinHook::build_datastore($vbulletin->db);
        $this->reConnect();
    }
    function reConnect($module = false)
    {
        global $vbulletin, $config;
        $database = is_object($vbulletin) ? $vbulletin->db->database : '';
        if ($database != RL_DBNAME) {
            if ($module == 'forum') {
                $mServer = $vbulletin->config['MasterServer'];
                $this->connect($mServer['servername'], $mServer['port'], $mServer['username'], $mServer['password'], $vbulletin->config['Database']['dbname']);
            } else {
                $this->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
            }
        }
    }
    function newPosts()
    {
        global $config, $vbulletin;
        if (false === $vbulletin = $this->vbInit())
            return false;
        $forumids    = array_keys($vbulletin->forumcache);
        $forumchoice = $postarray = array();
        foreach ($forumids as $forumid) {
            $forumperms =& $vbulletin->userinfo['forumpermissions']["$forumid"];
            if ($forumperms & $vbulletin->bf_ugp_forumpermissions['canview'] && ($forumperms & $vbulletin->bf_ugp_forumpermissions['canviewothers']) && (($forumperms & $vbulletin->bf_ugp_forumpermissions['canviewthreads'])) && verify_forum_password($forumid, $vbulletin->forumcache["$forumid"]['password'], false)) {
                array_push($forumchoice, $forumid);
            }
        }
        if (!empty($forumchoice)) {
            $forumsql     = "AND `T2`.`forumid` IN (" . implode(',', $forumchoice) . ")";
            $globalignore = '';
            if (trim($vbulletin->options['globalignore']) != '') {
                require_once(DIR . '/includes/functions_bigthree.php');
                if ($Coventry = fetch_coventry('string')) {
                    $globalignore = "AND `T1`.`userid` NOT IN ($Coventry) ";
                }
            }
            $sql = "SELECT `T1`.`dateline`, `T1`.`pagetext` AS `message`, `T1`.`allowsmilie`, `T1`.`postid`,";
            $sql .= "`T2`.`threadid`, `T2`.`title`, `T2`.`prefixid`, `T1`.`attach`, `T3`.`forumid`, `T4`.* ";
            $sql .= "FROM `" . TABLE_PREFIX . "post` AS `T1` ";
            $sql .= "JOIN `" . TABLE_PREFIX . "thread` AS `T2` ON (`T2`.`threadid` = `T1`.`threadid`) ";
            $sql .= "JOIN `" . TABLE_PREFIX . "forum` AS `T3` ON (`T3`.`forumid` = `T2`.`forumid`) ";
            $sql .= "LEFT JOIN `" . TABLE_PREFIX . "user` AS `T4` ON (`T1`.`userid` = `T4`.`userid`) ";
            $sql .= "WHERE 1=1 {$forumsql} AND `T1`.`visible` = 1 AND `T2`.`visible` = 1 AND `T2`.`open` <> '10' ";
            $sql .= "{$globalignore} ORDER BY `T1`.`dateline` DESC LIMIT 0,{$config['vbulletin_limit_posts']}";
            $posts = $vbulletin->db->query_read_slave($sql);
            while ($post = $vbulletin->db->fetch_array($posts)) {
                $new_post['title']          = fetch_trimmed_title($post['title'], 70);
                $new_post['message']        = $post['message'];
                $new_post['url']            = rtrim($vbulletin->options['bburl'], '/') . '/' . fetch_seo_url('thread', $post, array(
                    'p' => $post['postid']
                )) . '#post' . $post['postid'];
                $new_post['usertitle']      = $post['username'];
                $new_post['userprofile']    = rtrim($vbulletin->options['bburl'], '/') . '/' . fetch_seo_url('member', $post);
                $postarray[$post['postid']] = $new_post;
            }
        }
        $this->reConnect();
        return $postarray ? $postarray : false;
    }
    function login($username = false, $password = false)
    {
        if (!$username || !$password || false === $vbulletin = $this->vbInit())
            return false;
        require_once(DIR . '/includes/functions_login.php');
        $strikes          = verify_strike_status($username);
        $originalUserInfo = $vbulletin->userinfo;
        if (!verify_authentication($username, $password, md5($password), md5($username), 1, true)) {
            exec_strike_user($vbulletin->userinfo['username']);
            $vbulletin->userinfo = $originalUserInfo;
            $this->errorHandler('Login', fetch_error('badlogin_passthru', $vbulletin->options['bburl'], $vbulletin->session->vars['sessionurl']));
        }
        exec_unstrike_user($username);
        process_new_login('', 1, '');
        $this->reConnect();
    }
    function logOut()
    {
        global $block_keys;
        if (false === $vbulletin = $this->vbInit())
            return false;
        require_once(DIR . '/includes/functions_login.php');
        process_logout();
        if (!array_key_exists('vbulletin_new_posts', $block_keys)) {
            $this->reConnect();
        }
    }
    function createAccount($username = false, $password = false, $email = false, $import = false)
    {
        global $config;
        if ($username === false || $password === false || $email === false || false === $vbulletin = $this->vbInit())
            return false;
        define('VB_API', true);
        $password = $import ? $password : md5($password);
        $manager =& datamanager_init('User', $vbulletin, ERRTYPE_ARRAY);
        $manager->set('username', $username);
        $manager->set('email', $email);
        $manager->set('password', $password);
        $manager->set('ipaddress', $_SERVER['REMOTE_ADDR']);
        $manager->user['password'] = md5($password . $manager->user['salt']);
        $group                     = (int) $config['vbulletin_user_group'];
        $manager->set('usergroupid', $group ? $group : 2);
        if (empty($manager->errors)) {
            $manager->save();
            if (!$import) {
                $this->reConnect();
            }
            return true;
        } else {
            $this->errorHandler($import ? 'Import VB' : 'Registration', 'Username/Mail already in use');
        }
        if (!$import) {
            $this->reConnect();
        }
        return false;
    }
    function createFlynaxAccount($data = false)
    {
        global $rlValid, $rlActions, $config;
        if ($data === false || empty($data))
            return false;
        $username = $rlValid->xSql($data['username']);
        $exists   = $this->getOne('Username', "`Username` = '{$username}'", 'accounts');
        if (!empty($exists)) {
            return false;
        }
        $insert = array(
            'Type' => $config['vbulletin_flynax_account_type'],
            'Username' => $username,
            'Own_address' => $rlValid->str2path($username),
            'Password' => $data['password'],
            'Password_salt' => $data['salt'],
            'Mail' => $data['email'],
            'Date' => date('Y-m-d H:i:s', $data['joindate']),
            'Display_email' => 0
        );
        $result = $rlActions->insertOne($insert, 'accounts');
        return $result;
    }
    function fetchUserGroups()
    {
        if (false === $vbulletin = $this->vbInit())
            return false;
        $groups = array();
        $sql    = "SELECT `usergroupid`, `title` FROM `" . TABLE_PREFIX . "usergroup` WHERE `usergroupid` > '8' OR `usergroupid` = '2'";
        $query  = $vbulletin->db->query_read_slave($sql);
        while ($row = $vbulletin->db->fetch_array($query)) {
            array_push($groups, array(
                'ID' => $row['usergroupid'],
                'name' => $row['title']
            ));
        }
        $this->reConnect();
        return $groups;
    }
    function fetchImportLogs($getStatus = false)
    {
        global $config, $lang, $rlSmarty;
        if (!$_POST['xjxfun'] && $getStatus === true) {
            if (false === $vbulletin = $this->vbInit())
                return false;
            $check         = "SELECT `Active` FROM `" . TABLE_PREFIX . "product` WHERE `productid` = 'flynax'";
            $query         = $vbulletin->db->query_read_slave($check);
            $flynaxProduct = $vbulletin->db->fetch_array($query);
            $productStatus = 'install';
            if (!empty($flynaxProduct)) {
                $productStatus = $flynaxProduct['Active'] ? 'active' : 'approval';
            }
            $rlSmarty->assign('productStatus', $productStatus);
            $this->reConnect();
        }
        if (!array_key_exists('vbulletin_import_logs', $config)) {
            $sql = "INSERT INTO `" . RL_DBPREFIX . "config` ( `Key`, `Group_ID`, `Default`, `Plugin` ) VALUES ";
            $sql .= "( 'vbulletin_import_logs', '0', '0,0,0|0,0,0', 'vbulletin' )";
            $this->query($sql);
            $config['vbulletin_import_logs'] = '0,0,0|0,0,0';
        }
        list($flynaxLog, $vbulletinLog) = explode('|', $config['vbulletin_import_logs'], 2);
        $flynaxLog    = explode(',', $flynaxLog, 3);
        $vbulletinLog = explode(',', $vbulletinLog, 3);
        $actions[0]   = array(
            'title' => $lang['vbulletin_importAccountsFromVBulletin'],
            'button' => $lang['vbulletin_importButton'],
            'func' => 'xajax_importFromVBulletin',
            'successful' => $vbulletinLog[0],
            'failed' => $vbulletinLog[1],
            'date' => $vbulletinLog[2]
        );
        $actions[1]   = array(
            'title' => $lang['vbulletin_importAccountsFromFlynax'],
            'button' => $lang['vbulletin_importButton'],
            'func' => 'xajax_importFromFlynax',
            'successful' => $flynaxLog[0],
            'failed' => $flynaxLog[1],
            'date' => $flynaxLog[2]
        );
        $rlSmarty->assign('actions', $actions);
        return true;
    }
    function updateTableView()
    {
        global $_response, $rlSmarty;
        $this->fetchImportLogs();
        $tpl = RL_PLUGINS . 'vbulletin' . RL_DS . 'admin' . RL_DS . 'modules.tpl';
        $_response->assign('import_modules_dom', 'innerHTML', $rlSmarty->fetch($tpl, null, null, false));
    }
    function updateImportLog($module = false, $successful = false, $failed = false)
    {
        global $_response, $config, $lang, $rlConfig;
        $successful = (int) $successful;
        $failed     = (int) $failed;
        list($flynaxLog, $vbulletinLog) = explode('|', $config['vbulletin_import_logs'], 2);
        $save = ($module == 'flynaxLog') ? "{$successful},{$failed}," . time() . "|{$vbulletinLog}" : "{$flynaxLog}|{$successful},{$failed}," . time();
        if ($rlConfig->setConfig('vbulletin_import_logs', $save)) {
            $config['vbulletin_import_logs'] = $save;
            $this->updateTableView();
        }
        $_response->script("printMessage('notice', '{$lang['vbulletin_importCompleteNotice']}');");
        return $_response;
    }
    function ajaxImportFromFlynax($start = false, $successful = false, $failed = false)
    {
        global $_response, $lang;
        $start      = (int) $start;
        $limit      = 500;
        $successful = (int) $successful;
        $failed     = (int) $failed;
        $sql        = "SELECT `Username`, `Password`, `Mail` FROM `" . RL_DBPREFIX . "accounts` LIMIT {$start},{$limit}";
        $accounts   = $this->getAll($sql);
        if (!empty($accounts)) {
            foreach ($accounts as $key => $entry) {
                $result = $this->createAccount($entry['Username'], $entry['Password'], $entry['Mail'], true);
                if ($result) {
                    $successful++;
                } else {
                    $failed++;
                }
            }
            unset($accounts);
            $start += $limit;
            $_response->script("xajax_importFromFlynax({$start}, {$successful}, {$failed});");
            return $_response;
        }
        $this->updateImportLog('flynaxLog', $successful, $failed);
        return $_response;
    }
    function ajaxImportFromVBulletin($start = false, $successful = false, $failed = false)
    {
        global $_response, $lang;
        if (false === $vbulletin = $this->vbInit())
            return false;
        $start      = (int) $start;
        $limit      = 500;
        $successful = (int) $successful;
        $failed     = (int) $failed;
        $sql        = "SELECT `userid`, `username`, `password`, `salt`, `email`, `joindate` FROM `" . TABLE_PREFIX . "user` LIMIT {$start},{$limit}";
        $accounts   = $this->getAll($sql);
        $this->reConnect();
        if (!empty($accounts)) {
            $this->loadClass('Actions');
            foreach ($accounts as $key => $entry) {
                $result = $this->createFlynaxAccount($entry);
                if ($result) {
                    $successful++;
                } else {
                    $failed++;
                }
            }
            unset($accounts);
            $start += $limit;
            $_response->script("xajax_importFromVBulletin({$start}, {$successful}, {$failed});");
            return $_response;
        }
        $this->updateImportLog('vbulletinLog', $successful, $failed);
        return $_response;
    }
    function errorHandler($action = false, $message = false)
    {
        if (!function_exists('file_put_contents')) {
            function file_put_contents($filename, $data)
            {
                $f = @fopen($filename, 'w');
                if (!$f) {
                    return false;
                } else {
                    $bytes = fwrite($f, $data);
                    fclose($f);
                    return $bytes;
                }
            }
        }
        file_put_contents(RL_TMP . 'errorLog' . RL_DS . 'vbErrors.log', "{$action} |" . date('Y-m-d H:i') . "| {$message}" . PHP_EOL, FILE_APPEND);
    }
    function unInstall()
    {
        $this->query("ALTER TABLE `" . RL_DBPREFIX . "accounts` DROP `Password_salt`");
        unlink(RL_TMP . 'errorLog' . RL_DS . 'vbErrors.log');
        $this->unIstallProduct();
    }
}