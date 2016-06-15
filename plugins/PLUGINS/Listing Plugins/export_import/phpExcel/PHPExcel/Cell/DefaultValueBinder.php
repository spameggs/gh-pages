<?php
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}
class PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
    public function bindValue(PHPExcel_Cell $cell, $value = null)
    {
        if (is_string($value)) {
            $value = PHPExcel_Shared_String::SanitizeUTF8($value);
        }
        $cell->setValueExplicit($value, self::dataTypeForValue($value));
        return TRUE;
    }
    public static function dataTypeForValue($pValue = null)
    {
        if (is_null($pValue)) {
            return PHPExcel_Cell_DataType::TYPE_NULL;
        } elseif ($pValue === '') {
            return PHPExcel_Cell_DataType::TYPE_STRING;
        } elseif ($pValue instanceof PHPExcel_RichText) {
            return PHPExcel_Cell_DataType::TYPE_INLINE;
        } elseif ($pValue{0} === '=' && strlen($pValue) > 1) {
            return PHPExcel_Cell_DataType::TYPE_FORMULA;
        } elseif (is_bool($pValue)) {
            return PHPExcel_Cell_DataType::TYPE_BOOL;
        } elseif (is_float($pValue) || is_int($pValue)) {
            return PHPExcel_Cell_DataType::TYPE_NUMERIC;
        } elseif (preg_match('/^\-?([0-9]+\\.?[0-9]*|[0-9]*\\.?[0-9]+)$/', $pValue)) {
            return PHPExcel_Cell_DataType::TYPE_NUMERIC;
        } elseif (is_string($pValue) && array_key_exists($pValue, PHPExcel_Cell_DataType::getErrorCodes())) {
            return PHPExcel_Cell_DataType::TYPE_ERROR;
        } else {
            return PHPExcel_Cell_DataType::TYPE_STRING;
        }
    }
}