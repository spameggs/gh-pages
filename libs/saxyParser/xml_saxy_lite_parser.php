<?php
if (!defined('SAXY_INCLUDE_PATH')) {
    define('SAXY_INCLUDE_PATH', (dirname(__FILE__) . "/"));
}
define('SAXY_LITE_VERSION', '1.0');
define('SAXY_STATE_NONE', 0);
define('SAXY_STATE_PARSING', 1);

require_once(SAXY_INCLUDE_PATH . 'xml_saxy_shared.php');
class SAXY_Lite_Parser extends SAXY_Parser_Base
{
    function SAXY_Lite_Parser()
    {
        $this->SAXY_Parser_Base();
        $this->state = SAXY_STATE_NONE;
    }
    function getVersion()
    {
        return SAXY_LITE_VERSION;
    }
    function preprocessXML($xmlText)
    {
        $xmlText = trim($xmlText);
        $total   = strlen($xmlText);
        for ($i = 0; $i < $total; $i++) {
            if ($xmlText{$i} == '<') {
                switch ($xmlText{($i + 1)}) {
                    case '?':
                    case '!':
                        break;
                    default:
                        $this->state = SAXY_STATE_PARSING;
                        return (substr($xmlText, $i));
                }
            }
        }
    }
    function parse($xmlText)
    {
        $xmlText = $this->preprocessXML($xmlText);
        $total   = strlen($xmlText);
        for ($i = 0; $i < $total; $i++) {
            $currentChar = $xmlText{$i};
            switch ($this->state) {
                case SAXY_STATE_PARSING:
                    switch ($currentChar) {
                        case '<':
                            if (substr($this->charContainer, 0, SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA) {
                                $this->charContainer .= $currentChar;
                            } else {
                                $this->parseBetweenTags($this->charContainer);
                                $this->charContainer = '';
                            }
                            break;
                        case '>':
                            if ((substr($this->charContainer, 0, SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA) && !(($this->getCharFromEnd($this->charContainer, 0) == ']') && ($this->getCharFromEnd($this->charContainer, 1) == ']'))) {
                                $this->charContainer .= $currentChar;
                            } else {
                                $this->parseTag($this->charContainer);
                                $this->charContainer = '';
                            }
                            break;
                        default:
                            $this->charContainer .= $currentChar;
                    }
                    break;
            }
        }
        return true;
    }
    function parseTag($tagText)
    {
        $tagText      = trim($tagText);
        $firstChar    = $tagText{0};
        $myAttributes = array();
        switch ($firstChar) {
            case '/':
                $tagName = substr($tagText, 1);
                $this->fireEndElementEvent($tagName);
                break;
            case '!':
                $upperCaseTagText = strtoupper($tagText);
                if (strpos($upperCaseTagText, SAXY_SEARCH_CDATA) !== false) {
                    $total          = strlen($tagText);
                    $openBraceCount = 0;
                    $textNodeText   = '';
                    for ($i = 0; $i < $total; $i++) {
                        $currentChar = $tagText{$i};
                        if (($currentChar == ']') && ($tagText{($i + 1)} == ']')) {
                            break;
                        } else if ($openBraceCount > 1) {
                            $textNodeText .= $currentChar;
                        } else if ($currentChar == '[') {
                            $openBraceCount++;
                        }
                    }
                    if ($this->cDataSectionHandler == null) {
                        $this->fireCharacterDataEvent($textNodeText);
                    } else {
                        $this->fireCDataSectionEvent($textNodeText);
                    }
                } else if (strpos($upperCaseTagText, SAXY_SEARCH_NOTATION) !== false) {
                    return;
                } else if (substr($tagText, 0, 2) == '!-') {
                    return;
                }
                break;
            case '?':
                return;
            default:
                if ((strpos($tagText, '"') !== false) || (strpos($tagText, "'") !== false)) {
                    $total   = strlen($tagText);
                    $tagName = '';
                    for ($i = 0; $i < $total; $i++) {
                        $currentChar = $tagText{$i};
                        if (($currentChar == ' ') || ($currentChar == "\t") || ($currentChar == "\n") || ($currentChar == "\r") || ($currentChar == "\x0B")) {
                            $myAttributes = $this->parseAttributes(substr($tagText, $i));
                            break;
                        } else {
                            $tagName .= $currentChar;
                        }
                    }
                    if (strrpos($tagText, '/') == (strlen($tagText) - 1)) {
                        $this->fireStartElementEvent($tagName, $myAttributes);
                        $this->fireEndElementEvent($tagName);
                    } else {
                        $this->fireStartElementEvent($tagName, $myAttributes);
                    }
                } else {
                    if (strpos($tagText, '/') !== false) {
                        $tagText = trim(substr($tagText, 0, (strrchr($tagText, '/') - 1)));
                        $this->fireStartElementEvent($tagText, $myAttributes);
                        $this->fireEndElementEvent($tagText);
                    } else {
                        $this->fireStartElementEvent($tagText, $myAttributes);
                    }
                }
        }
    }
    function xml_get_error_code()
    {
        return -1;
    }
    function xml_error_string($code)
    {
        return "";
    }
}
?>
