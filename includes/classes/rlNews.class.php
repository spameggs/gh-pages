<?php
class rlNews extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    var $calc_news;
    function rlNews()
    {
        global $rlLang, $rlValid, $rlConfig;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
    }
    function get($id = false, $page = false, $pg = 1)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS `ID`, `ID` AS `Key`, `Date`, `Path` FROM `" . RL_DBPREFIX . "news` ";
        $sql .= "WHERE `Status` = 'active' ";
        if ($id) {
            $sql .= "AND `ID` = '{$id}'";
        }
        $GLOBALS['rlHook']->load('rlNewsGetSql', $sql);
        $sql .= "ORDER BY `Date` DESC ";
        if (!$page) {
            $sql .= "LIMIT " . $GLOBALS['config']['news_block_news_in_block'];
        } else {
            $start = 0;
            if ($pg > 1) {
                $start = ($pg - 1) * $GLOBALS['config']['news_at_page'];
            }
            $sql .= "LIMIT {$start}," . $GLOBALS['config']['news_at_page'];
        }
        if ($id) {
            $news = $this->getRow($sql);
        } else {
            $news = $this->getAll($sql);
        }
        $news_number     = $this->getRow("SELECT FOUND_ROWS() AS `calc`");
        $this->calc_news = $news_number['calc'];
        $news            = $this->rlLang->replaceLangKeys($news, 'news', array(
            'title',
            'content',
            'meta_description',
            'meta_keywords'
        ));
        return $news;
    }
}