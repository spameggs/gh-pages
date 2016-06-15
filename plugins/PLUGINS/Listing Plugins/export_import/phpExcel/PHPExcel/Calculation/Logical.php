<?php
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}
class PHPExcel_Calculation_Logical
{
    public static function TRUE()
    {
        return TRUE;
    }
    public static function FALSE()
    {
        return FALSE;
    }
    public static function LOGICAL_AND()
    {
        $returnValue = TRUE;
        $aArgs       = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $argCount    = -1;
        foreach ($aArgs as $argCount => $arg) {
            if (is_bool($arg)) {
                $returnValue = $returnValue && $arg;
            } elseif ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue = $returnValue && ($arg != 0);
            } elseif (is_string($arg)) {
                $arg = strtoupper($arg);
                if (($arg == 'TRUE') || ($arg == PHPExcel_Calculation::getTRUE())) {
                    $arg = TRUE;
                } elseif (($arg == 'FALSE') || ($arg == PHPExcel_Calculation::getFALSE())) {
                    $arg = FALSE;
                } else {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $returnValue = $returnValue && ($arg != 0);
            }
        }
        if ($argCount < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        return $returnValue;
    }
    public static function LOGICAL_OR()
    {
        $returnValue = FALSE;
        $aArgs       = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $argCount    = -1;
        foreach ($aArgs as $argCount => $arg) {
            if (is_bool($arg)) {
                $returnValue = $returnValue || $arg;
            } elseif ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue = $returnValue || ($arg != 0);
            } elseif (is_string($arg)) {
                $arg = strtoupper($arg);
                if (($arg == 'TRUE') || ($arg == PHPExcel_Calculation::getTRUE())) {
                    $arg = TRUE;
                } elseif (($arg == 'FALSE') || ($arg == PHPExcel_Calculation::getFALSE())) {
                    $arg = FALSE;
                } else {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $returnValue = $returnValue || ($arg != 0);
            }
        }
        if ($argCount < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        return $returnValue;
    }
    public static function NOT($logical = FALSE)
    {
        $logical = PHPExcel_Calculation_Functions::flattenSingleValue($logical);
        if (is_string($logical)) {
            $logical = strtoupper($logical);
            if (($logical == 'TRUE') || ($logical == PHPExcel_Calculation::getTRUE())) {
                return FALSE;
            } elseif (($logical == 'FALSE') || ($logical == PHPExcel_Calculation::getFALSE())) {
                return TRUE;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        return !$logical;
    }
    public static function STATEMENT_IF($condition = TRUE, $returnIfTrue = 0, $returnIfFalse = FALSE)
    {
        $condition     = (is_null($condition)) ? TRUE : (boolean) PHPExcel_Calculation_Functions::flattenSingleValue($condition);
        $returnIfTrue  = (is_null($returnIfTrue)) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($returnIfTrue);
        $returnIfFalse = (is_null($returnIfFalse)) ? FALSE : PHPExcel_Calculation_Functions::flattenSingleValue($returnIfFalse);
        return ($condition) ? $returnIfTrue : $returnIfFalse;
    }
    public static function IFERROR($testValue = '', $errorpart = '')
    {
        $testValue = (is_null($testValue)) ? '' : PHPExcel_Calculation_Functions::flattenSingleValue($testValue);
        $errorpart = (is_null($errorpart)) ? '' : PHPExcel_Calculation_Functions::flattenSingleValue($errorpart);
        return self::STATEMENT_IF(PHPExcel_Calculation_Functions::IS_ERROR($testValue), $errorpart, $testValue);
    }
}