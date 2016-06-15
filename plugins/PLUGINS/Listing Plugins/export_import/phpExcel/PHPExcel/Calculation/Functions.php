<?php
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}
define('MAX_VALUE', 1.2e308);
define('M_2DIVPI', 0.63661977236758134307553505349006);
define('MAX_ITERATIONS', 256);
define('PRECISION', 8.88E-016);
class PHPExcel_Calculation_Functions
{
    const COMPATIBILITY_EXCEL = 'Excel';
    const COMPATIBILITY_GNUMERIC = 'Gnumeric';
    const COMPATIBILITY_OPENOFFICE = 'OpenOfficeCalc';
    const RETURNDATE_PHP_NUMERIC = 'P';
    const RETURNDATE_PHP_OBJECT = 'O';
    const RETURNDATE_EXCEL = 'E';
    protected static $compatibilityMode = self::COMPATIBILITY_EXCEL;
    protected static $ReturnDateType = self::RETURNDATE_EXCEL;
    protected static $_errorCodes = array('null' => '#NULL!', 'divisionbyzero' => '#DIV/0!', 'value' => '#VALUE!', 'reference' => '#REF!', 'name' => '#NAME?', 'num' => '#NUM!', 'na' => '#N/A', 'gettingdata' => '#GETTING_DATA');
    public static function setCompatibilityMode($compatibilityMode)
    {
        if (($compatibilityMode == self::COMPATIBILITY_EXCEL) || ($compatibilityMode == self::COMPATIBILITY_GNUMERIC) || ($compatibilityMode == self::COMPATIBILITY_OPENOFFICE)) {
            self::$compatibilityMode = $compatibilityMode;
            return True;
        }
        return False;
    }
    public static function getCompatibilityMode()
    {
        return self::$compatibilityMode;
    }
    public static function setReturnDateType($returnDateType)
    {
        if (($returnDateType == self::RETURNDATE_PHP_NUMERIC) || ($returnDateType == self::RETURNDATE_PHP_OBJECT) || ($returnDateType == self::RETURNDATE_EXCEL)) {
            self::$ReturnDateType = $returnDateType;
            return True;
        }
        return False;
    }
    public static function getReturnDateType()
    {
        return self::$ReturnDateType;
    }
    public static function DUMMY()
    {
        return '#Not Yet Implemented';
    }
    public static function DIV0()
    {
        return self::$_errorCodes['divisionbyzero'];
    }
    public static function NA()
    {
        return self::$_errorCodes['na'];
    }
    public static function NaN()
    {
        return self::$_errorCodes['num'];
    }
    public static function NAME()
    {
        return self::$_errorCodes['name'];
    }
    public static function REF()
    {
        return self::$_errorCodes['reference'];
    }
    public static function NULL()
    {
        return self::$_errorCodes['null'];
    }
    public static function VALUE()
    {
        return self::$_errorCodes['value'];
    }
    public static function isMatrixValue($idx)
    {
        return ((substr_count($idx, '.') <= 1) || (preg_match('/\.[A-Z]/', $idx) > 0));
    }
    public static function isValue($idx)
    {
        return (substr_count($idx, '.') == 0);
    }
    public static function isCellValue($idx)
    {
        return (substr_count($idx, '.') > 1);
    }
    public static function _ifCondition($condition)
    {
        $condition = PHPExcel_Calculation_Functions::flattenSingleValue($condition);
        if (!in_array($condition{0}, array(
            '>',
            '<',
            '='
        ))) {
            if (!is_numeric($condition)) {
                $condition = PHPExcel_Calculation::_wrapResult(strtoupper($condition));
            }
            return '=' . $condition;
        } else {
            preg_match('/([<>=]+)(.*)/', $condition, $matches);
            list(, $operator, $operand) = $matches;
            if (!is_numeric($operand)) {
                $operand = PHPExcel_Calculation::_wrapResult(strtoupper($operand));
            }
            return $operator . $operand;
        }
    }
    public static function ERROR_TYPE($value = '')
    {
        $value = self::flattenSingleValue($value);
        $i     = 1;
        foreach (self::$_errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }
        return self::NA();
    }
    public static function IS_BLANK($value = NULL)
    {
        if (!is_null($value)) {
            $value = self::flattenSingleValue($value);
        }
        return is_null($value);
    }
    public static function IS_ERR($value = '')
    {
        $value = self::flattenSingleValue($value);
        return self::IS_ERROR($value) && (!self::IS_NA($value));
    }
    public static function IS_ERROR($value = '')
    {
        $value = self::flattenSingleValue($value);
        if (!is_string($value))
            return false;
        return in_array($value, array_values(self::$_errorCodes));
    }
    public static function IS_NA($value = '')
    {
        $value = self::flattenSingleValue($value);
        return ($value === self::NA());
    }
    public static function IS_EVEN($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        if ($value === NULL)
            return self::NAME();
        if ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value))))
            return self::VALUE();
        return ($value % 2 == 0);
    }
    public static function IS_ODD($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        if ($value === NULL)
            return self::NAME();
        if ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value))))
            return self::VALUE();
        return (abs($value) % 2 == 1);
    }
    public static function IS_NUMBER($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        if (is_string($value)) {
            return False;
        }
        return is_numeric($value);
    }
    public static function IS_LOGICAL($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        return is_bool($value);
    }
    public static function IS_TEXT($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        return (is_string($value) && !self::IS_ERROR($value));
    }
    public static function IS_NONTEXT($value = NULL)
    {
        return !self::IS_TEXT($value);
    }
    public static function VERSION()
    {
        return 'PHPExcel ##VERSION##, ##DATE##';
    }
    public static function N($value = NULL)
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }
        switch (gettype($value)) {
            case 'double':
            case 'float':
            case 'integer':
                return $value;
                break;
            case 'boolean':
                return (integer) $value;
                break;
            case 'string':
                if ((strlen($value) > 0) && ($value{0} == '#')) {
                    return $value;
                }
                break;
        }
        return 0;
    }
    public static function TYPE($value = NULL)
    {
        $value = self::flattenArrayIndexed($value);
        if (is_array($value) && (count($value) > 1)) {
            $a = array_keys($value);
            $a = array_pop($a);
            if (self::isCellValue($a)) {
                return 16;
            } elseif (self::isMatrixValue($a)) {
                return 64;
            }
        } elseif (empty($value)) {
            return 1;
        }
        $value = self::flattenSingleValue($value);
        if (($value === NULL) || (is_float($value)) || (is_int($value))) {
            return 1;
        } elseif (is_bool($value)) {
            return 4;
        } elseif (is_array($value)) {
            return 64;
            break;
        } elseif (is_string($value)) {
            if ((strlen($value) > 0) && ($value{0} == '#')) {
                return 16;
            }
            return 2;
        }
        return 0;
    }
    public static function flattenArray($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }
        $arrayValues = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $arrayValues[] = $v;
                        }
                    } else {
                        $arrayValues[] = $val;
                    }
                }
            } else {
                $arrayValues[] = $value;
            }
        }
        return $arrayValues;
    }
    public static function flattenArrayIndexed($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }
        $arrayValues = array();
        foreach ($array as $k1 => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k3 => $v) {
                            $arrayValues[$k1 . '.' . $k2 . '.' . $k3] = $v;
                        }
                    } else {
                        $arrayValues[$k1 . '.' . $k2] = $val;
                    }
                }
            } else {
                $arrayValues[$k1] = $value;
            }
        }
        return $arrayValues;
    }
    public static function flattenSingleValue($value = '')
    {
        while (is_array($value)) {
            $value = array_pop($value);
        }
        return $value;
    }
}
if (!function_exists('acosh')) {
    function acosh($x)
    {
        return 2 * log(sqrt(($x + 1) / 2) + sqrt(($x - 1) / 2));
    }
}
if (!function_exists('asinh')) {
    function asinh($x)
    {
        return log($x + sqrt(1 + $x * $x));
    }
}
if (!function_exists('atanh')) {
    function atanh($x)
    {
        return (log(1 + $x) - log(1 - $x)) / 2;
    }
}
if (!function_exists('money_format')) {
    function money_format($format, $number)
    {
        $regex = array(
            '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?(?:#([0-9]+))?',
            '(?:\.([0-9]+))?([in%])/'
        );
        $regex = implode('', $regex);
        if (setlocale(LC_MONETARY, null) == '') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        $number = floatval($number);
        if (!preg_match($regex, $format, $fmatch)) {
            trigger_error("No format specified or invalid format", E_USER_WARNING);
            return $number;
        }
        $flags      = array(
            'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ? $match[1] : ' ',
            'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? $match[0] : '+',
            'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
            'isleft' => preg_match('/\-/', $fmatch[1]) > 0
        );
        $width      = trim($fmatch[2]) ? (int) $fmatch[2] : 0;
        $left       = trim($fmatch[3]) ? (int) $fmatch[3] : 0;
        $right      = trim($fmatch[4]) ? (int) $fmatch[4] : $locale['int_frac_digits'];
        $conversion = $fmatch[5];
        $positive   = true;
        if ($number < 0) {
            $positive = false;
            $number *= -1;
        }
        $letter = $positive ? 'p' : 'n';
        $prefix = $suffix = $cprefix = $csuffix = $signal = '';
        if (!$positive) {
            $signal = $locale['negative_sign'];
            switch (true) {
                case $locale['n_sign_posn'] == 0 || $flags['usesignal'] == '(':
                    $prefix = '(';
                    $suffix = ')';
                    break;
                case $locale['n_sign_posn'] == 1:
                    $prefix = $signal;
                    break;
                case $locale['n_sign_posn'] == 2:
                    $suffix = $signal;
                    break;
                case $locale['n_sign_posn'] == 3:
                    $cprefix = $signal;
                    break;
                case $locale['n_sign_posn'] == 4:
                    $csuffix = $signal;
                    break;
            }
        }
        if (!$flags['nosimbol']) {
            $currency = $cprefix;
            $currency .= ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']);
            $currency .= $csuffix;
            $currency = iconv('ISO-8859-1', 'UTF-8', $currency);
        } else {
            $currency = '';
        }
        $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';
        if (!isset($locale['mon_decimal_point']) || empty($locale['mon_decimal_point'])) {
            $locale['mon_decimal_point'] = (!isset($locale['decimal_point']) || empty($locale['decimal_point'])) ? $locale['decimal_point'] : '.';
        }
        $number = number_format($number, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $number = explode($locale['mon_decimal_point'], $number);
        $n      = strlen($prefix) + strlen($currency);
        if ($left > 0 && $left > $n) {
            if ($flags['isleft']) {
                $number[0] .= str_repeat($flags['fillchar'], $left - $n);
            } else {
                $number[0] = str_repeat($flags['fillchar'], $left - $n) . $number[0];
            }
        }
        $number = implode($locale['mon_decimal_point'], $number);
        if ($locale["{$letter}_cs_precedes"]) {
            $number = $prefix . $currency . $space . $number . $suffix;
        } else {
            $number = $prefix . $number . $space . $currency . $suffix;
        }
        if ($width > 0) {
            $number = str_pad($number, $width, $flags['fillchar'], $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT);
        }
        $format = str_replace($fmatch[0], $number, $format);
        return $format;
    }
}
if ((!function_exists('mb_str_replace')) && (function_exists('mb_substr')) && (function_exists('mb_strlen')) && (function_exists('mb_strpos'))) {
    function mb_str_replace($search, $replace, $subject)
    {
        if (is_array($subject)) {
            $ret = array();
            foreach ($subject as $key => $val) {
                $ret[$key] = mb_str_replace($search, $replace, $val);
            }
            return $ret;
        }
        foreach ((array) $search as $key => $s) {
            if ($s == '') {
                continue;
            }
            $r   = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
            $pos = mb_strpos($subject, $s, 0, 'UTF-8');
            while ($pos !== false) {
                $subject = mb_substr($subject, 0, $pos, 'UTF-8') . $r . mb_substr($subject, $pos + mb_strlen($s, 'UTF-8'), 65535, 'UTF-8');
                $pos     = mb_strpos($subject, $s, $pos + mb_strlen($r, 'UTF-8'), 'UTF-8');
            }
        }
        return $subject;
    }
}