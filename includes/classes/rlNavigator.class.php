<?php
class rlNavigator extends reefless
{	
    public $cPage = null;
    public $cLang = null;
    public $cMobile = false;
    public $rlConfig = null;
    public function rlNavigator()
    {
        global $rlConfig;
        $this->rlConfig =& $rlConfig;
        $_SESSION['GEOLocationData'] = $this->getGEOData();
        return;
    }
    public function rewriteGet($vareables = false, $page = false, $lang = false)
    {
        global $config;
        $page  = empty($page) ? '' : $page;
        $items = explode('/', trim($vareables, '/'));
        if (!(empty($lang))) {
            $langsList = $this->fetch('Code', array(
                'Code' => $lang
            ), null, null, 'languages', 'row');
            if (empty($langsList)) {
                $lang = $config['lang'];
            }
        }
        if ($config['mod_rewrite']) {
            if (isset($_GET['wildcard'])) {
                $request     = trim($vareables, '/');
                $request_exp = explode('/', $request);
                if ($request == $config['mobile_location_name']) {
                    $this->cMobile = true;
                }
                if (count($request_exp) > 1) {
                    $this->cLang = $_GET['lang'] = $request_exp[1];
                } else {
                    if (count($request_exp) == 1 && strlen($request) == 2) {
                        $this->cLang = $_GET['lang'] = trim($vareables, '/');
                    }
                }
            }
            if ($page == $config['mobile_location_name']) {
                $this->cPage   = $page = $items[0];
                $this->cMobile = true;
                $rlVars        = explode('/', trim($_GET['rlVareables'], '/'));
                unset($rlVars[0]);
                unset($items[0]);
                $items               = array_values($items);
                $_GET['rlVareables'] = implode('/', $rlVars);
            }
            if (strlen($page) < 3 && !$_GET['lang']) {
                $this->cLang  = $page;
                $this->cPage  = $items[0];
                $_GET['page'] = $items[0];
                $rlVars       = explode('/', trim($_GET['rlVareables'], '/'));
                unset($rlVars[0]);
                $_GET['rlVareables'] = implode('/', $rlVars);
                foreach ($items as $key => $value) {
                    $items[$key] = $items[$key + 1];
                    if (!(isset($items[$key]))) {
                        continue;
                    }
                    unset($items[$key]);
                    continue;
                }
            } else {
                if ($_GET['lang']) {
                    $this->cLang = $_GET['lang'];
                    $this->cPage = $page;
                } else {
                    $this->cLang = $config['lang'];
                    $this->cPage = $page;
                }
            }
        } else {
            $this->cLang = $lang;
            $this->cPage = $page;
        }
        if (!(empty($vareables))) {
            $count_vars = count($items);
            $i          = 0;
            while ($count_vars > $i) {
                $step                  = $i + 1;
                $_GET['nvar_' . $step] = $items[$i];
                $i++;
                continue;
            }
            unset($vareables);
        }
        return;
    }
    public function definePage()
    {
        global $account_info;
        global $lang;
        $page = $this->cPage;
        if ($page == 'index') {
            $page = '';
        }
        $sql = "SELECT * FROM `" . RL_DBPREFIX . ('' . "pages` WHERE `Path` = CONVERT('" . $page . "' USING utf8) AND `Status` = 'active' LIMIT 1");
        if ($pageInfo = $this->getRow($sql)) {
            $pageInfo = $GLOBALS['rlLang']->replaceLangKeys($pageInfo, 'pages', array(
                'name',
                'title',
                'meta_description',
                'meta_keywords'
            ));
            if ($pageInfo['Plugin'] && !(is_readable(RL_PLUGINS . $pageInfo['Plugin'] . RL_DS . $pageInfo['Controller'] . '.inc.php')) || empty($pageInfo['Controller']) || !$pageInfo['Plugin'] && !(is_readable(RL_CONTROL . $pageInfo['Controller'] . '.inc.php'))) {
                header("HTTP/1.0 404 Not Found");
                $pageInfo['Controller'] = '404';
                $pageInfo['Tpl']        = true;
                $pageInfo['title']      = $lang['undefined_page'];
                $pageInfo['name']       = $lang['undefined_page'];
                $pageInfo['Page_type']  = 'system';
            }
        } else {
            $address         = $this->cPage;
            $sql             = "SELECT `ID`, `Type` FROM `" . RL_DBPREFIX . ('' . "accounts` WHERE `Own_address` = CONVERT('" . $address . "' USING utf8) LIMIT 1");
            $account_details = $this->getRow($sql);
            $pageInfo        = $this->fetch('*', array(
                'Key' => 'at_' . $account_details['Type'],
                'Status' => 'active'
            ), null, 1, 'pages', 'row');
            $pageInfo        = $GLOBALS['rlLang']->replaceLangKeys($pageInfo, 'pages', array(
                'name',
                'title',
                'meta_description',
                'meta_keywords'
            ));
            $_GET['id']      = $account_details['ID'];
            if (empty($pageInfo['Controller']) || !(is_readable(RL_CONTROL . $pageInfo['Controller'] . '.inc.php')) || $pageInfo['Menus'] == '2' && !(isset($account_info['ID']))) {
                header("HTTP/1.0 404 Not Found");
                $pageInfo['Controller'] = '404';
                $pageInfo['Tpl']        = true;
                $pageInfo['title']      = $lang['undefined_page'];
                $pageInfo['name']       = $lang['undefined_page'];
                $pageInfo['Page_type']  = 'system';
            }
        }
        return $pageInfo;
    }
	
	function getAllPages() 
    { 
        $this -> setTable( 'pages' );
        $pages = $this -> fetch( array( 'Key', 'Path' ) );
        $this -> resetTable(); 

        foreach ( $pages as $key => $value ) 
        { 
            $out[$pages[$key]['Key']] = $pages[$key]['Path']; 
        } 
        unset($pages); 
         
        return $out; 
    }
	
    public function getGEOData($ips = FALSE, $format = 'json')
    {
        if (isset($_SESSION['GEOLocationData'], $_SESSION['GEOLocationData'])) {
            return $_SESSION['GEOLocationData'];
        }
        global $ips;
		/*$ips = $_SERVER['REMOTE_ADDR'];*/
        $ips = empty($ips) ? $_SERVER["REMOTE_ADDR"] : $ips;
        include("./includes/classes/fs.geoip.inc");
        include("./includes/classes/fs.geoipcity.inc");
        include("./includes/classes/fs.geoipregionvars.php");
        $getContent              = geoip_open("./includes/classes/fs.geolitecity.dat", GEOIP_STANDARD);
        $record                  = geoip_record_by_addr($getContent, $ips);
        $content                 = array();
        $content['Country_code'] = $record->country_code;
        $content['Country_name'] = $record->country_name;
        $content['Region']       = $GEOIP_REGION_NAME[$record->country_code][$record->region];
        $content['City']         = $record->city;
        $content['ISP_name']     = 'Unknown';
        geoip_close($getContent);
        $content = json_encode($content);
        $GLOBALS['rlHook']->load('phpGetGEOData');
        return $content;
    }
}
?>