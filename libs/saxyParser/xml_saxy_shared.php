<?php
define('SAXY_SEARCH_CDATA', '![CDATA[');
define('SAXY_CDATA_LEN', 8);
define('SAXY_SEARCH_NOTATION', '!NOTATION');
define('SAXY_SEARCH_DOCTYPE', '!DOCTYPE');
define('SAXY_STATE_ATTR_NONE', 0);
define('SAXY_STATE_ATTR_KEY', 1);
define('SAXY_STATE_ATTR_VALUE', 2);
class SAXY_Parser_Base
{
    var $state;
    var $charContainer;
    var $startElementHandler;
    var $endElementHandler;
    var $characterDataHandler;
    var $cDataSectionHandler = null;
    var $convertEntities = true;
    var $predefinedEntities = array('&amp;' => '&', '&lt;' => '<', '&gt;' => '>', '&quot;' => '"', '&apos;' => "'");
    var $definedEntities = array();
    var $preserveWhitespace = false;
    function SAXY_Parser_Base()
    {
        $this->charContainer = '';
    }
    function xml_set_element_handler($startHandler, $endHandler)
    {
        $this->startElementHandler = $startHandler;
        $this->endElementHandler   = $endHandler;
    }
    function xml_set_character_data_handler($handler)
    {
        $this->characterDataHandler =& $handler;
    }
    function xml_set_cdata_section_handler($handler)
    {
        $this->cDataSectionHandler =& $handler;
    }
    function convertEntities($truthVal)
    {
        $this->convertEntities = $truthVal;
    }
    function appendEntityTranslationTable($table)
    {
        $this->definedEntities = $table;
    }
    function getCharFromEnd($text, $index)
    {
        $len  = strlen($text);
        $char = $text{($len - 1 - $index)};
        return $char;
    }
    function parseAttributes($attrText)
    {
        $attrText     = trim($attrText);
        $attrArray    = array();
        $maybeEntity  = false;
        $total        = strlen($attrText);
        $keyDump      = '';
        $valueDump    = '';
        $currentState = SAXY_STATE_ATTR_NONE;
        $quoteType    = '';
        for ($i = 0; $i < $total; $i++) {
            $currentChar = $attrText{$i};
            if ($currentState == SAXY_STATE_ATTR_NONE) {
                if (trim($currentChar != '')) {
                    $currentState = SAXY_STATE_ATTR_KEY;
                }
            }
            switch ($currentChar) {
                case "\t":
                    if ($currentState == SAXY_STATE_ATTR_VALUE) {
                        $valueDump .= $currentChar;
                    } else {
                        $currentChar = '';
                    }
                    break;
                case "\x0B":
                case "\n":
                case "\r":
                    $currentChar = '';
                    break;
                case '=':
                    if ($currentState == SAXY_STATE_ATTR_VALUE) {
                        $valueDump .= $currentChar;
                    } else {
                        $currentState = SAXY_STATE_ATTR_VALUE;
                        $quoteType    = '';
                        $maybeEntity  = false;
                    }
                    break;
                case '"':
                    if ($currentState == SAXY_STATE_ATTR_VALUE) {
                        if ($quoteType == '') {
                            $quoteType = '"';
                        } else {
                            if ($quoteType == $currentChar) {
                                if ($this->convertEntities && $maybeEntity) {
                                    $valueDump = strtr($valueDump, $this->predefinedEntities);
                                    $valueDump = strtr($valueDump, $this->definedEntities);
                                }
                                $keyDump             = trim($keyDump);
                                $attrArray[$keyDump] = $valueDump;
                                $keyDump             = $valueDump = $quoteType = '';
                                $currentState        = SAXY_STATE_ATTR_NONE;
                            } else {
                                $valueDump .= $currentChar;
                            }
                        }
                    }
                    break;
                case "'":
                    if ($currentState == SAXY_STATE_ATTR_VALUE) {
                        if ($quoteType == '') {
                            $quoteType = "'";
                        } else {
                            if ($quoteType == $currentChar) {
                                if ($this->convertEntities && $maybeEntity) {
                                    $valueDump = strtr($valueDump, $this->predefinedEntities);
                                    $valueDump = strtr($valueDump, $this->definedEntities);
                                }
                                $keyDump             = trim($keyDump);
                                $attrArray[$keyDump] = $valueDump;
                                $keyDump             = $valueDump = $quoteType = '';
                                $currentState        = SAXY_STATE_ATTR_NONE;
                            } else {
                                $valueDump .= $currentChar;
                            }
                        }
                    }
                    break;
                case '&':
                    $maybeEntity = true;
                    $valueDump .= $currentChar;
                    break;
                default:
                    if ($currentState == SAXY_STATE_ATTR_KEY) {
                        $keyDump .= $currentChar;
                    } else {
                        $valueDump .= $currentChar;
                    }
            }
        }
        return $attrArray;
    }
    function parseBetweenTags($betweenTagText)
    {
        if (trim($betweenTagText) != '') {
            $this->fireCharacterDataEvent($betweenTagText);
        }
    }
    function fireStartElementEvent($tagName, $attributes)
    {
        call_user_func($this->startElementHandler, $this, $tagName, $attributes);
    }
    function fireEndElementEvent($tagName)
    {
        call_user_func($this->endElementHandler, $this, $tagName);
    }
    function fireCharacterDataEvent($data)
    {
        if ($this->convertEntities && ((strpos($data, "&") != -1))) {
            $data = strtr($data, $this->predefinedEntities);
            $data = strtr($data, $this->definedEntities);
        }
        call_user_func($this->characterDataHandler, $this, $data);
    }
    function fireCDataSectionEvent($data)
    {
        call_user_func($this->cDataSectionHandler, $this, $data);
    }
}
?>