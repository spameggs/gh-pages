<?php
PHPExcel_Autoloader::Register();
if (ini_get('mbstring.func_overload') & 2) {
    throw new PHPExcel_Exception('Multibyte function overloading in PHP must be disabled for string functions (2).');
}
PHPExcel_Shared_String::buildCharacterSets();
class PHPExcel_Autoloader
{
    public static function Register()
    {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
        return spl_autoload_register(array(
            'PHPExcel_Autoloader',
            'Load'
        ));
    }
    public static function Load($pClassName)
    {
        if ((class_exists($pClassName, FALSE)) || (strpos($pClassName, 'PHPExcel') !== 0)) {
            return FALSE;
        }
        $pClassFilePath = PHPEXCEL_ROOT . str_replace('_', DIRECTORY_SEPARATOR, $pClassName) . '.php';
        if ((file_exists($pClassFilePath) === FALSE) || (is_readable($pClassFilePath) === FALSE)) {
            return FALSE;
        }
        require($pClassFilePath);
    }
}