<?php
class rlValid extends reefless
{
    function sql(&$data)
    {
        if (is_array($data)) {
            foreach ($data as $string => $value) {
                $this->sql($data[$string]);
            }
        } else {
            if (is_array($data)) {
                $data = array_map('trim', $data);
                if (get_magic_quotes_gpc()) {
                    $data = array_map('stripslashes', $data);
                }
                $data = str_replace("\'", "'", $data);
                $data = array_map('addslashes', $data);
            } else {
                $data = trim($data);
                if (get_magic_quotes_gpc()) {
                    $data = stripslashes($data);
                }
                $data = str_replace("\'", "'", $data);
                $data = addslashes($data);
            }
        }
    }
    function xSql($data)
    {
        if (is_array($data)) {
            foreach ($data as $string => $value) {
                $data[$string] = $this->xSql($data[$string]);
            }
        } else {
            if (is_array($data)) {
                $data = array_map('trim', $data);
                if (get_magic_quotes_gpc()) {
                    $data = array_map('stripslashes', $data);
                }
                $data = str_replace("\'", "'", $data);
                $data = array_map('addslashes', $data);
            } else {
                $data = trim($data);
                if (get_magic_quotes_gpc()) {
                    $data = stripslashes($data);
                }
                $data = str_replace("\'", "'", $data);
                $data = addslashes($data);
            }
        }
        return $data;
    }
    function html(&$data)
    {
        if (is_array($data)) {
            foreach ($data as $string => $value) {
                $data[$string] = htmlspecialchars($data[$string]);
            }
        } else {
            $data = htmlspecialchars($data);
        }
    }
    function xHtml($data)
    {
        if (is_array($data)) {
            foreach ($data as $string => $value) {
                if (!is_array($data[$string])) {
                    $data[$string] = htmlspecialchars($data[$string]);
                }
            }
        } else {
            $data = htmlspecialchars($data);
        }
        return $data;
    }
    function stripJS($data)
    {
        if (is_array($data)) {
            foreach ($data as $string => $value) {
                if (!is_array($data[$string])) {
                    $data[$string] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data[$string]);
                    $data[$string] = preg_replace('/[\r\n\t]/is', '', $data[$string]);
                }
            }
        } else {
            $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data);
            $data = preg_replace('/[\r\n\t]/is', '', $data);
        }
        return $data;
    }
    function isEmail($mail)
    {
        return (bool) preg_match('/^(?:(?:\"[^\"\f\n\r\t\v\b]+\")|(?:[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(?:\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@(?:(?:\[(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))\])|(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))|(?:(?:(?:[A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/', $mail);
    }
    function isUrl($url)
    {
        /*return (bool) preg_match('/^https?:\/\/[a-z0-9-]{2,63}(?:\.[a-z0-9-]{2,})+(?::[0-9]{0,5})?(?:\/|$|\?)\S*$/', $url);*/
		return (bool) preg_match('/(^https?:\/\/)?([a-z0-9]+\.)*[a-z0-9]+\.([a-z]{2,3})\/?$/', $url);
    }
    function isDomain($domain)
    {
        return (bool) preg_match('/^[^\.]([w]{3}[0-9]?\.?)?[a-zA-Z0-9\-\_\.]{2,68}\.[a-zA-Z0-9]{2,10}$/', $domain);
    }
    function isImage($extension)
    {
        $available_ext = array(
            1 => 'jpg',
            2 => 'jpeg',
            3 => 'gif',
            4 => 'png'
        );
        if (!array_search(strtolower($extension), $available_ext)) {
            return false;
        }
        return true;
    }
    function isFile($type, $extension)
    {
        include_once(RL_LIBS . 'system.lib.php');
        global $l_file_types;
        $available_ext = $l_file_types[$type]['ext'];
        if (false === strpos($available_ext, strtolower($extension))) {
            return false;
        }
        return true;
    }
    function getDomain($url = null, $mode = false)
    {
        return parse_url($url, PHP_URL_HOST);
    }
    function str2key($key, $replace = '_')
    {
        $key = preg_replace('/[^a-zA-Z0-9\+]+/i', $replace, $key);
        $key = strtolower($key);
        $key = trim($key, $replace);
        return empty($key) ? false : $key;
    }
    function str2path($str, $keep_slashes = false)
    {
        if ($keep_slashes) {
            $rx = '\/';
        }
        loadUTF8functions('ascii', 'utf8_to_ascii', 'utf8_is_ascii');
        if (!utf8_is_ascii($str)) {
            $str = utf8_to_ascii($str);
        }
        $str = preg_replace("/[^a-z0-9{$rx}\.]+/i", '-', $str);
        $str = preg_replace('/\-+/', '-', $str);
        $str = strtolower($str);
        $str = trim($str, '-');
        $str = trim($str, '/');
        $str = trim($str);
        return empty($str) ? false : $str;
    }
    function str2money($aParams)
    {
		$pdecimal = $GLOBALS['config']['price_decimal'];
		$frest = '00' . $pdecimal;
        $string = is_array($aParams) ? $aParams['string'] : $aParams;
        $len    = strlen($string);
        $string = strrev($string);
        if (strpos($string, $pdecimal)) {
            $rest   = substr($string, 0, strpos($string, $pdecimal));
            $string = substr($string, strpos($string, $pdecimal) + 1, $len);
            $len -= strlen($rest) + 1;
            $rest = strrev(substr(strrev($rest), 0, 2)) . $pdecimal;
        } elseif ($GLOBALS['config']['show_cents']) {
            $rest = $frest;
        }
        for ($i = 0; $i <= $len; $i++) {
            $val .= $string[$i];
            if ((($i + 1) % 3 == 0) && ($i + 1 < $len)) {
                $val .= $GLOBALS['config']['price_delimiter'];
            }
        }
        $val = strrev($rest . $val);
        return $val;
    }
    function uniqueKey($key = false, $table = false, $keyField = 'Key')
    {
        if (!$key || !$table)
            return 'key_' . mt_rand();
        if ($this->getOne($keyField, "`{$keyField}` = '{$key}'", $table)) {
            $key .= rand(1, 9);
            return $this->uniqueKey($key, $table, $keyField);
        } else {
            return $key;
        }
    }
}