<?php
class rlAutoRegPrevent extends reefless
{
    var $parseFields;
    function rlAutoRegPrevent()
    {
        $this->parseFields = array(
            'type',
            'appears'
        );
    }
    function xml2array($xml = '')
    {
        if (empty($xml))
            return false;
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, $xml, $vals, $ind);
        xml_parser_free($parser);
        $iteration = $index = 0;
        foreach ($vals as $val) {
            $tag = strtolower($val['tag']);
            if (in_array($tag, $this->parseFields)) {
                $array[$index][$tag] = $val['value'];
                $iteration++;
                if ($iteration % count($this->parseFields) == 0) {
                    $index++;
                }
            }
        }
        return count($array) ? $array : false;
    }
    function saveToBase($result = false, &$formData = array())
    {
        global $config, $rlValid;
        $reason = '';
        foreach ($result as $key => $value) {
            if ($value['appears'] == 'yes') {
                $reason .= "{$value['type']},";
            }
        }
        $reason = trim($reason, ',');
        if (!empty($reason)) {
            $sql = "INSERT INTO `" . RL_DBPREFIX . "reg_prevent` ( `Username`, `Mail`, `IP`, `Reason`, `Date`, `Status` ) VALUES ";
            $sql .= "( '" . $rlValid->xSql($formData['username']) . "', '" . $rlValid->xSql($formData['mail']) . "', '{$_SERVER['REMOTE_ADDR']}', '{$reason}', NOW(), 'block' )";
            $this->query($sql);
            return true;
        }
        return false;
    }
    function checkBase($where = '')
    {
        $result = $this->getOne('Status', "{$where}", 'reg_prevent');
        return !empty($result) ? $result : false;
    }
    function check(&$formData)
    {
        global $config, $rlValid;
        $where        = '';
        $checkSpamURL = "http://www.stopforumspam.com/api?";
        if ($config['autoRegPrevent_check_username']) {
            $checkSpamURL .= "username={$formData['username']}&";
            $where .= "`Username` = '" . $rlValid->xSql($formData['username']) . "' AND";
        }
        if ($config['autoRegPrevent_check_email']) {
            $checkSpamURL .= "email={$formData['mail']}&";
            $where .= "`Mail` = '" . $rlValid->xSql($formData['mail']) . "' AND";
        }
        if ($config['autoRegPrevent_check_ip']) {
            $checkSpamURL .= "ip={$_SERVER['REMOTE_ADDR']}&";
            $where .= "`IP` = '{$_SERVER['REMOTE_ADDR']}' AND";
        }
        $checkSpamURL = trim($checkSpamURL, '&');
        $where        = trim($where, 'AND');
        if (false === $dbStatus = $this->checkBase($where)) {
            $xml = $this->getPageContent($checkSpamURL);
            if (false !== $result = $this->xml2array($xml)) {
                if (true === $this->saveToBase($result, $formData)) {
                    return false;
                }
            }
        } else {
            if ($dbStatus == 'unblock') {
                return true;
            }
            return false;
        }
        return true;
    }
    function ajaxAddSpamers($username = false, $email = false, $ip = false)
    {
        global $_response, $rlValid, $lang;
        $username = $username ? $rlValid->xSql($username) : 'N/A';
        $email    = $email ? $rlValid->xSql($email) : 'N/A';
        $ip       = $ip ? $rlValid->xSql($ip) : 'N/A';
        $sql      = "INSERT INTO `" . RL_DBPREFIX . "reg_prevent` ( `Username`, `Mail`, `IP`, `Reason`, `Date`, `Status` ) VALUES ";
        $sql .= "( '{$username}', '{$email}', '{$ip}', '{$lang['autoRegPrevent_adminAdded']}', NOW(), 'block' )";
        $this->query($sql);
        $_response->script("$('input#arp_username').val('');$('input#arp_email').val('');$('input#arp_ip').val('');");
        $_response->script("$('input[name=item_submit]').val('{$lang['add']}');autoRegPrevent.reload();");
        return $_response;
    }
}