<?php
class PHPExcel_Shared_File
{
    protected static $_useUploadTempDirectory = FALSE;
    public static function setUseUploadTempDirectory($useUploadTempDir = FALSE)
    {
        self::$_useUploadTempDirectory = (boolean) $useUploadTempDir;
    }
    public static function getUseUploadTempDirectory()
    {
        return self::$_useUploadTempDirectory;
    }
    public static function file_exists($pFilename)
    {
        if (strtolower(substr($pFilename, 0, 3)) == 'zip') {
            $zipFile     = substr($pFilename, 6, strpos($pFilename, '#') - 6);
            $archiveFile = substr($pFilename, strpos($pFilename, '#') + 1);
            $zip         = new ZipArchive();
            if ($zip->open($zipFile) === true) {
                $returnValue = ($zip->getFromName($archiveFile) !== false);
                $zip->close();
                return $returnValue;
            } else {
                return false;
            }
        } else {
            return file_exists($pFilename);
        }
    }
    public static function realpath($pFilename)
    {
        $returnValue = '';
        if (file_exists($pFilename)) {
            $returnValue = realpath($pFilename);
        }
        if ($returnValue == '' || ($returnValue === NULL)) {
            $pathArray = explode('/', $pFilename);
            while (in_array('..', $pathArray) && $pathArray[0] != '..') {
                for ($i = 0; $i < count($pathArray); ++$i) {
                    if ($pathArray[$i] == '..' && $i > 0) {
                        unset($pathArray[$i]);
                        unset($pathArray[$i - 1]);
                        break;
                    }
                }
            }
            $returnValue = implode('/', $pathArray);
        }
        return $returnValue;
    }
    public static function sys_get_temp_dir()
    {
        if (self::$_useUploadTempDirectory) {
            if (ini_get('upload_tmp_dir') !== FALSE) {
                if ($temp = ini_get('upload_tmp_dir')) {
                    if (file_exists($temp))
                        return realpath($temp);
                }
            }
        }
        if (!function_exists('sys_get_temp_dir')) {
            if ($temp = getenv('TMP')) {
                if ((!empty($temp)) && (file_exists($temp))) {
                    return realpath($temp);
                }
            }
            if ($temp = getenv('TEMP')) {
                if ((!empty($temp)) && (file_exists($temp))) {
                    return realpath($temp);
                }
            }
            if ($temp = getenv('TMPDIR')) {
                if ((!empty($temp)) && (file_exists($temp))) {
                    return realpath($temp);
                }
            }
            $temp = tempnam(__FILE__, '');
            if (file_exists($temp)) {
                unlink($temp);
                return realpath(dirname($temp));
            }
            return null;
        }
        return realpath(sys_get_temp_dir());
    }
}