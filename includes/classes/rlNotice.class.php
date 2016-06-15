<?php
class rlNotice extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    function rlNotice()
    {
        global $rlLang, $rlValid, $rlConfig;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
    }
    function saveNotice($message = false, $type = 'notice')
    {
        $sesVar     = 'notice';
        $sesVarType = 'notice_type';
        if (defined('REALM')) {
            $sesVar     = REALM . "_" . $sesVar;
            $sesVarType = REALM . "_" . $sesVarType;
        }
        if (!empty($message)) {
            $_SESSION[$sesVar]     = $message;
            $_SESSION[$sesVarType] = $type;
        } else {
            return false;
        }
    }
    function resetNotice()
    {
        $sesVar     = 'notice';
        $sesVarType = 'type';
        if (defined('REALM')) {
            $sesVar     = REALM . "_" . $sesVar;
            $sesVarType = REALM . "_" . $sesVarType;
        }
        unset($_SESSION[$sesVar], $_SESSION[$sesVarType]);
        return true;
    }
    function createNotice($message)
    {
        echo 'rlNotice -> createNotice()';
        $tpl   = 'blocks' . RL_DS . 'notice_block_start.tpl';
        $block = $GLOBALS['rlSmarty']->fetch($tpl, null, null, false);
        $block .= $message;
        $tpl = 'blocks' . RL_DS . 'notice_block_end.tpl';
        $block .= $GLOBALS['rlSmarty']->fetch($tpl, null, null, false);
        return $block;
    }
    function createError($message)
    {
        echo 'rlNotice -> createError()';
        $tpl          = 'blocks' . RL_DS . 'error_block_start.tpl';
        $block        = $GLOBALS['rlSmarty']->fetch($tpl, null, null, false);
        $mess_content = null;
        if (is_array($message)) {
            foreach ($message as $error) {
                $mess_content .= '- ' . $error . '<br />';
            }
            $block .= $mess_content;
        } else {
            $block .= $message;
        }
        $mess_content = substr($mess_content, 0, -6);
        $tpl          = 'blocks' . RL_DS . 'error_block_end.tpl';
        $block .= $GLOBALS['rlSmarty']->fetch($tpl, null, null, false);
        return $block;
    }
    function createAlert($message)
    {
        echo 'rlNotice -> createAlert()';
        $tpl          = 'blocks' . RL_DS . 'alert_block_start.tpl';
        $block        = $GLOBALS['rlSmarty']->fetch($tpl, null, null, false);
        $mess_content = null;
        if (is_array($message)) {
            foreach ($message as $alert) {
                $mess_content .= '- ' . $alert . '<br />';
            }
            $block .= $mess_content;
        } else {
            $block .= $message;
        }
        $mess_content = substr($mess_content, 0, -6);
        $tpl          = 'blocks' . RL_DS . 'alert_block_end.tpl';
        $block .= $GLOBALS['rlSmarty']->fetch($tpl, null, null, false);
        return $block;
    }
}