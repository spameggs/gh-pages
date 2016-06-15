 <?php
$subdomain = getHostSplitted();
$subcheck  = $subdomain[0];
$subcheck  = str_replace('www.', '', $subcheck);
function getHostSplitted()
{
    $matches = array();
    if (substr_count($_SERVER['HTTP_HOST'], '.') == 1) {
        preg_match('/^(?P<d>.+)\.(?P<tld>.+?)$/', $_SERVER['HTTP_HOST'], $matches);
    } else {
        preg_match('/^(?P<sd>.+)\.(?P<d>.+?)\.(?P<tld>.+?)$/', $_SERVER['HTTP_HOST'], $matches);
    }
    return array(
        0 => (isset($matches['sd']) ? $matches['sd'] : ''),
        1 => $matches['d'],
        2 => $matches['tld']
    );
}
if (empty($subcheck)) {
    if (version_compare(phpversion(), '5.4', '<')) {
        header('Location: install/index.php');
        exit;
    } else {
        $fa   = './includes/classes/rlDebug.class.php';
        $fa1  = '$this';
        $fa2  = '), E_ALL)';
        $fan1 = '"rlDebug"';
        $fan2 = '), E_ERROR)';
        $stra = file_get_contents($fa, TRUE) OR die;
        $stra = str_replace("$fa1", "$fan1", $stra);
        $stra = str_replace("$fa2", "$fan2", $stra);
        file_put_contents($fa, $stra);
		$fb   = './includes/classes/rlHook.class.php';
        $fb1  = '$func($param1, $param2, $param3, $param4, $param5)';
        $fbn1 = '$func(&$param1, &$param2, &$param3, &$param4, &$param5)';
        $strb = file_get_contents($fb, TRUE) OR die;
        $strb = str_replace("$fb1", "$fbn1", $strb);
        file_put_contents($fb, $strb);
        header('Location: install/index.php');
        exit;
    }
} else {
    if (version_compare(phpversion(), '5.4', '<')) {
        $htaccess = './.htaccess';
        $line1    = "RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.).*)\..+\.[^/]+$ [NC]";
        $line2    = "#RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.).*)\..+$ [NC] # FIRST LEVEL DOMAIN (localhost) USAGE";
        $nline1   = "#RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.|$subcheck\.).*)\..+\.[^/]+$ [NC]";
        $nline2   = "RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.|$subcheck\.).*)\..+$ [NC] # FIRST LEVEL DOMAIN (localhost) USAGE";
        $str = file_get_contents($htaccess, TRUE) OR die;
        $str = str_replace("$line1", "$nline1", $str);
        $str = str_replace("$line2", "$nline2", $str);
        file_put_contents($htaccess, $str);
        header('Location: install/index.php');
        exit;
    } else {
        $htaccess = './.htaccess';
        $line1    = "RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.).*)\..+\.[^/]+$ [NC]";
        $line2    = "#RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.).*)\..+$ [NC] # FIRST LEVEL DOMAIN (localhost) USAGE";
        $nline1   = "#RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.|$subcheck\.).*)\..+\.[^/]+$ [NC]";
        $nline2   = "RewriteCond %{HTTP_HOST} ^((?!www\.|m\.|mobile\.|$subcheck\.).*)\..+$ [NC] # FIRST LEVEL DOMAIN (localhost) USAGE";
        $str = file_get_contents($htaccess, TRUE) OR die;
        $str = str_replace("$line1", "$nline1", $str);
        $str = str_replace("$line2", "$nline2", $str);
        file_put_contents($htaccess, $str);
        $fa   = './includes/classes/rlDebug.class.php';
        $fa1  = '$this';
        $fa2  = '), E_ALL)';
        $fan1 = '"rlDebug"';
        $fan2 = '), E_ERROR)';
        $stra = file_get_contents($fa, TRUE) OR die;
        $stra = str_replace("$fa1", "$fan1", $stra);
        $stra = str_replace("$fa2", "$fan2", $stra);
        file_put_contents($fa, $stra);
		$fb   = './includes/classes/rlHook.class.php';
		$fb1  = '$func($param1, $param2, $param3, $param4, $param5)';
        $fbn1 = '$func(&$param1, &$param2, &$param3, &$param4, &$param5)';
        $strb = file_get_contents($fb, TRUE) OR die;
        $strb = str_replace("$fb1", "$fbn1", $strb);
        file_put_contents($fb, $strb);
        header('Location: install/index.php');
        exit;
    }
}
?>