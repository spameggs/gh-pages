<?php
class rlFAQs extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    var $calc_faqs;
    function rlFAQs()
    {
        global $rlLang, $rlValid, $rlConfig;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
    }
    function get($id = false, $page = false, $pg = 1, $calc_fr = false)
    {
        $id  = (int) $id;
        $sql = "SELECT ";
        if ($calc_fr === true) {
            $sql .= "SQL_CALC_FOUND_ROWS ";
        }
        $sql .= "`ID`, `ID` AS `Key`, `Date`, `Path` FROM `" . RL_DBPREFIX . "faqs` ";
        $sql .= "WHERE `Status` = 'active' ";
        if ($id) {
            $sql .= "AND `ID` = '{$id}'";
        }
        $GLOBALS['rlHook']->load('rlFAQsGetSql', $sql);
        $sql .= "ORDER BY `Date` DESC ";
        if ($page === 'block') {
            $sql .= "LIMIT " . $GLOBALS['config']['faqs_block_in_block'];
        } else {
            $start = 0;
            if ($pg > 1) {
                $start = ($pg - 1) * $GLOBALS['config']['faqs_at_page'];
            }
            $sql .= "LIMIT {$start}," . $GLOBALS['config']['faqs_at_page'];
        }
        if ($id) {
            $faqs = $this->getRow($sql);
        } else {
            $faqs = $this->getAll($sql);
        }
        if ($calc_fr === true) {
            $faqs_number     = $this->getRow("SELECT FOUND_ROWS() AS `calc`");
            $this->calc_faqs = $faqs_number['calc'];
        }
        $faqs = $this->rlLang->replaceLangKeys($faqs, 'faqs', array(
            'title',
            'content'
        ));
        return $faqs;
    }
    function ajaxDeleteFAQs($id)
    {
        global $_response, $lang;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        if (!$id)
            return $_response;
        $id          = (int) $id;
        $lang_keys[] = array(
            'Key' => 'faqs+title+' . $id
        );
        $lang_keys[] = array(
            'Key' => 'faqs+content+' . $id
        );
        $GLOBALS['rlActions']->delete(array(
            'ID' => $id
        ), array(
            'faqs'
        ), null, null, $id, $lang_keys);
        $del_mode = $GLOBALS['rlActions']->action;
        $_response->script("
			faqsGrid.reload();
			printMessage('notice', '{$lang['faq_' . $del_mode]}');
		");
        return $_response;
    }
}