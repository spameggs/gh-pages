<?php
class PHPExcel_Shared_String
{
    const STRING_REGEXP_FRACTION = '(-?)(\d+)\s+(\d+\/\d+)';
    private static $_controlCharacters = array();
    private static $_SYLKCharacters = array();
    private static $_decimalSeparator;
    private static $_thousandsSeparator;
    private static $_currencyCode;
    private static $_isMbstringEnabled;
    private static $_isIconvEnabled;
    private static function _buildControlCharacters()
    {
        for ($i = 0; $i <= 31; ++$i) {
            if ($i != 9 && $i != 10 && $i != 13) {
                $find                            = '_x' . sprintf('%04s', strtoupper(dechex($i))) . '_';
                $replace                         = chr($i);
                self::$_controlCharacters[$find] = $replace;
            }
        }
    }
    private static function _buildSYLKCharacters()
    {
        self::$_SYLKCharacters = array(
            "\x1B 0" => chr(0),
            "\x1B 1" => chr(1),
            "\x1B 2" => chr(2),
            "\x1B 3" => chr(3),
            "\x1B 4" => chr(4),
            "\x1B 5" => chr(5),
            "\x1B 6" => chr(6),
            "\x1B 7" => chr(7),
            "\x1B 8" => chr(8),
            "\x1B 9" => chr(9),
            "\x1B :" => chr(10),
            "\x1B ;" => chr(11),
            "\x1B <" => chr(12),
            "\x1B :" => chr(13),
            "\x1B >" => chr(14),
            "\x1B ?" => chr(15),
            "\x1B!0" => chr(16),
            "\x1B!1" => chr(17),
            "\x1B!2" => chr(18),
            "\x1B!3" => chr(19),
            "\x1B!4" => chr(20),
            "\x1B!5" => chr(21),
            "\x1B!6" => chr(22),
            "\x1B!7" => chr(23),
            "\x1B!8" => chr(24),
            "\x1B!9" => chr(25),
            "\x1B!:" => chr(26),
            "\x1B!;" => chr(27),
            "\x1B!<" => chr(28),
            "\x1B!=" => chr(29),
            "\x1B!>" => chr(30),
            "\x1B!?" => chr(31),
            "\x1B'?" => chr(127),
            "\x1B(0" => '€',
            "\x1B(2" => '‚',
            "\x1B(3" => 'ƒ',
            "\x1B(4" => '"',
            "\x1B(5" => '…',
            "\x1B(6" => '†',
            "\x1B(7" => '‡',
            "\x1B(8" => 'ˆ',
            "\x1B(9" => '‰',
            "\x1B(:" => 'Š',
            "\x1B(;" => '‹',
            "\x1BNj" => 'Œ',
            "\x1B(>" => 'Ž',
            "\x1B)1" => ''',
            "\x1B)2" => ''',
            "\x1B)3" => '"',
            "\x1B)4" => '"',
            "\x1B)5" => '•',
            "\x1B)6" => '–',
            "\x1B)7" => '—',
            "\x1B)8" => '˜',
            "\x1B)9" => '™',
            "\x1B):" => 'š',
            "\x1B);" => '›',
            "\x1BNz" => 'œ',
            "\x1B)>" => 'ž',
            "\x1B)?" => 'Ÿ',
            "\x1B*0" => ' ',
            "\x1BN!" => '¡',
            "\x1BN\"" => '¢',
            "\x1BN#" => '£',
            "\x1BN(" => '¤',
            "\x1BN%" => '¥',
            "\x1B*6" => '¦',
            "\x1BN'" => '§',
            "\x1BNH " => '¨',
            "\x1BNS" => '©',
            "\x1BNc" => 'ª',
            "\x1BN+" => '«',
            "\x1B*<" => '¬',
            "\x1B*=" => '­',
            "\x1BNR" => '®',
            "\x1B*?" => '¯',
            "\x1BN0" => '°',
            "\x1BN1" => '±',
            "\x1BN2" => '²',
            "\x1BN3" => '³',
            "\x1BNB " => '´',
            "\x1BN5" => 'µ',
            "\x1BN6" => '¶',
            "\x1BN7" => '·',
            "\x1B+8" => '¸',
            "\x1BNQ" => '¹',
            "\x1BNk" => 'º',
            "\x1BN;" => '»',
            "\x1BN<" => '¼',
            "\x1BN=" => '½',
            "\x1BN>" => '¾',
            "\x1BN?" => '¿',
            "\x1BNAA" => 'À',
            "\x1BNBA" => 'Á',
            "\x1BNCA" => 'Â',
            "\x1BNDA" => 'Ã',
            "\x1BNHA" => 'Ä',
            "\x1BNJA" => 'Å',
            "\x1BNa" => 'Æ',
            "\x1BNKC" => 'Ç',
            "\x1BNAE" => 'È',
            "\x1BNBE" => 'É',
            "\x1BNCE" => 'Ê',
            "\x1BNHE" => 'Ë',
            "\x1BNAI" => 'Ì',
            "\x1BNBI" => 'Í',
            "\x1BNCI" => 'Î',
            "\x1BNHI" => 'Ï',
            "\x1BNb" => 'Ð',
            "\x1BNDN" => 'Ñ',
            "\x1BNAO" => 'Ò',
            "\x1BNBO" => 'Ó',
            "\x1BNCO" => 'Ô',
            "\x1BNDO" => 'Õ',
            "\x1BNHO" => 'Ö',
            "\x1B-7" => '×',
            "\x1BNi" => 'Ø',
            "\x1BNAU" => 'Ù',
            "\x1BNBU" => 'Ú',
            "\x1BNCU" => 'Û',
            "\x1BNHU" => 'Ü',
            "\x1B-=" => 'Ý',
            "\x1BNl" => 'Þ',
            "\x1BN{" => 'ß',
            "\x1BNAa" => 'à',
            "\x1BNBa" => 'á',
            "\x1BNCa" => 'â',
            "\x1BNDa" => 'ã',
            "\x1BNHa" => 'ä',
            "\x1BNJa" => 'å',
            "\x1BNq" => 'æ',
            "\x1BNKc" => 'ç',
            "\x1BNAe" => 'è',
            "\x1BNBe" => 'é',
            "\x1BNCe" => 'ê',
            "\x1BNHe" => 'ë',
            "\x1BNAi" => 'ì',
            "\x1BNBi" => 'í',
            "\x1BNCi" => 'î',
            "\x1BNHi" => 'ï',
            "\x1BNs" => 'ð',
            "\x1BNDn" => 'ñ',
            "\x1BNAo" => 'ò',
            "\x1BNBo" => 'ó',
            "\x1BNCo" => 'ô',
            "\x1BNDo" => 'õ',
            "\x1BNHo" => 'ö',
            "\x1B/7" => '÷',
            "\x1BNy" => 'ø',
            "\x1BNAu" => 'ù',
            "\x1BNBu" => 'ú',
            "\x1BNCu" => 'û',
            "\x1BNHu" => 'ü',
            "\x1B/=" => 'ý',
            "\x1BN|" => 'þ',
            "\x1BNHy" => 'ÿ'
        );
    }
    public static function getIsMbstringEnabled()
    {
        if (isset(self::$_isMbstringEnabled)) {
            return self::$_isMbstringEnabled;
        }
        self::$_isMbstringEnabled = function_exists('mb_convert_encoding') ? true : false;
        return self::$_isMbstringEnabled;
    }
    public static function getIsIconvEnabled()
    {
        if (isset(self::$_isIconvEnabled)) {
            return self::$_isIconvEnabled;
        }
        if (!function_exists('iconv')) {
            self::$_isIconvEnabled = false;
            return false;
        }
        if (!@iconv('UTF-8', 'UTF-16LE', 'x')) {
            self::$_isIconvEnabled = false;
            return false;
        }
        if (!@iconv_substr('A', 0, 1, 'UTF-8')) {
            self::$_isIconvEnabled = false;
            return false;
        }
        if (defined('PHP_OS') && @stristr(PHP_OS, 'AIX') && defined('ICONV_IMPL') && (@strcasecmp(ICONV_IMPL, 'unknown') == 0) && defined('ICONV_VERSION') && (@strcasecmp(ICONV_VERSION, 'unknown') == 0)) {
            self::$_isIconvEnabled = false;
            return false;
        }
        self::$_isIconvEnabled = true;
        return true;
    }
    public static function buildCharacterSets()
    {
        if (empty(self::$_controlCharacters)) {
            self::_buildControlCharacters();
        }
        if (empty(self::$_SYLKCharacters)) {
            self::_buildSYLKCharacters();
        }
    }
    public static function ControlCharacterOOXML2PHP($value = '')
    {
        return str_replace(array_keys(self::$_controlCharacters), array_values(self::$_controlCharacters), $value);
    }
    public static function ControlCharacterPHP2OOXML($value = '')
    {
        return str_replace(array_values(self::$_controlCharacters), array_keys(self::$_controlCharacters), $value);
    }
    public static function SanitizeUTF8($value)
    {
        if (self::getIsIconvEnabled()) {
            $value = @iconv('UTF-8', 'UTF-8', $value);
            return $value;
        }
        if (self::getIsMbstringEnabled()) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            return $value;
        }
        return $value;
    }
    public static function IsUTF8($value = '')
    {
        return utf8_encode(utf8_decode($value)) === $value;
    }
    public static function FormatNumber($value)
    {
        if (is_float($value)) {
            return str_replace(',', '.', $value);
        }
        return (string) $value;
    }
    public static function UTF8toBIFF8UnicodeShort($value, $arrcRuns = array())
    {
        $ln = self::CountCharacters($value, 'UTF-8');
        if (empty($arrcRuns)) {
            $opt  = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ? 0x0001 : 0x0000;
            $data = pack('CC', $ln, $opt);
            $data .= self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');
        } else {
            $data = pack('vC', $ln, 0x09);
            $data .= pack('v', count($arrcRuns));
            $data .= self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');
            foreach ($arrcRuns as $cRun) {
                $data .= pack('v', $cRun['strlen']);
                $data .= pack('v', $cRun['fontidx']);
            }
        }
        return $data;
    }
    public static function UTF8toBIFF8UnicodeLong($value)
    {
        $ln    = self::CountCharacters($value, 'UTF-8');
        $opt   = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ? 0x0001 : 0x0000;
        $chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');
        $data  = pack('vC', $ln, $opt) . $chars;
        return $data;
    }
    public static function ConvertEncoding($value, $to, $from)
    {
        if (self::getIsIconvEnabled()) {
            return iconv($from, $to, $value);
        }
        if (self::getIsMbstringEnabled()) {
            return mb_convert_encoding($value, $to, $from);
        }
        if ($from == 'UTF-16LE') {
            return self::utf16_decode($value, false);
        } else if ($from == 'UTF-16BE') {
            return self::utf16_decode($value);
        }
        return $value;
    }
    public static function utf16_decode($str, $bom_be = true)
    {
        if (strlen($str) < 2)
            return $str;
        $c0 = ord($str{0});
        $c1 = ord($str{1});
        if ($c0 == 0xfe && $c1 == 0xff) {
            $str = substr($str, 2);
        } elseif ($c0 == 0xff && $c1 == 0xfe) {
            $str    = substr($str, 2);
            $bom_be = false;
        }
        $len    = strlen($str);
        $newstr = '';
        for ($i = 0; $i < $len; $i += 2) {
            if ($bom_be) {
                $val = ord($str{$i}) << 4;
                $val += ord($str{$i + 1});
            } else {
                $val = ord($str{$i + 1}) << 4;
                $val += ord($str{$i});
            }
            $newstr .= ($val == 0x228) ? "\n" : chr($val);
        }
        return $newstr;
    }
    public static function CountCharacters($value, $enc = 'UTF-8')
    {
        if (self::getIsIconvEnabled()) {
            return iconv_strlen($value, $enc);
        }
        if (self::getIsMbstringEnabled()) {
            return mb_strlen($value, $enc);
        }
        return strlen($value);
    }
    public static function Substring($pValue = '', $pStart = 0, $pLength = 0)
    {
        if (self::getIsIconvEnabled()) {
            return iconv_substr($pValue, $pStart, $pLength, 'UTF-8');
        }
        if (self::getIsMbstringEnabled()) {
            return mb_substr($pValue, $pStart, $pLength, 'UTF-8');
        }
        return substr($pValue, $pStart, $pLength);
    }
    public static function StrToUpper($pValue = '')
    {
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($pValue, MB_CASE_UPPER, "UTF-8");
        }
        return strtoupper($pValue);
    }
    public static function StrToLower($pValue = '')
    {
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($pValue, MB_CASE_LOWER, "UTF-8");
        }
        return strtolower($pValue);
    }
    public static function StrToTitle($pValue = '')
    {
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($pValue, MB_CASE_TITLE, "UTF-8");
        }
        return ucwords($pValue);
    }
    public static function convertToNumberIfFraction(&$operand)
    {
        if (preg_match('/^' . self::STRING_REGEXP_FRACTION . '$/i', $operand, $match)) {
            $sign            = ($match[1] == '-') ? '-' : '+';
            $fractionFormula = '=' . $sign . $match[2] . $sign . $match[3];
            $operand         = PHPExcel_Calculation::getInstance()->_calculateFormulaValue($fractionFormula);
            return true;
        }
        return false;
    }
    public static function getDecimalSeparator()
    {
        if (!isset(self::$_decimalSeparator)) {
            $localeconv              = localeconv();
            self::$_decimalSeparator = ($localeconv['decimal_point'] != '') ? $localeconv['decimal_point'] : $localeconv['mon_decimal_point'];
            if (self::$_decimalSeparator == '') {
                self::$_decimalSeparator = '.';
            }
        }
        return self::$_decimalSeparator;
    }
    public static function setDecimalSeparator($pValue = '.')
    {
        self::$_decimalSeparator = $pValue;
    }
    public static function getThousandsSeparator()
    {
        if (!isset(self::$_thousandsSeparator)) {
            $localeconv                = localeconv();
            self::$_thousandsSeparator = ($localeconv['thousands_sep'] != '') ? $localeconv['thousands_sep'] : $localeconv['mon_thousands_sep'];
        }
        return self::$_thousandsSeparator;
    }
    public static function setThousandsSeparator($pValue = ',')
    {
        self::$_thousandsSeparator = $pValue;
    }
    public static function getCurrencyCode()
    {
        if (!isset(self::$_currencyCode)) {
            $localeconv          = localeconv();
            self::$_currencyCode = ($localeconv['currency_symbol'] != '') ? $localeconv['currency_symbol'] : $localeconv['int_curr_symbol'];
            if (self::$_currencyCode == '') {
                self::$_currencyCode = '$';
            }
        }
        return self::$_currencyCode;
    }
    public static function setCurrencyCode($pValue = '$')
    {
        self::$_currencyCode = $pValue;
    }
    public static function SYLKtoUTF8($pValue = '')
    {
        if (strpos($pValue, '') === false) {
            return $pValue;
        }
        foreach (self::$_SYLKCharacters as $k => $v) {
            $pValue = str_replace($k, $v, $pValue);
        }
        return $pValue;
    }
    public static function testStringAsNumeric($value)
    {
        if (is_numeric($value))
            return $value;
        $v = floatval($value);
        return (is_numeric(substr($value, 0, strlen($v)))) ? $v : $value;
    }
}