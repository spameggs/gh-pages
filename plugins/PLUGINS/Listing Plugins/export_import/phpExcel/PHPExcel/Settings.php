<?php
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}
class PHPExcel_Settings
{
    const PCLZIP = 'PHPExcel_Shared_ZipArchive';
    const ZIPARCHIVE = 'ZipArchive';
    const CHART_RENDERER_JPGRAPH = 'jpgraph';
    const PDF_RENDERER_TCPDF = 'tcPDF';
    const PDF_RENDERER_DOMPDF = 'DomPDF';
    const PDF_RENDERER_MPDF = 'mPDF';
    private static $_chartRenderers = array(self::CHART_RENDERER_JPGRAPH);
    private static $_pdfRenderers = array(self::PDF_RENDERER_TCPDF, self::PDF_RENDERER_DOMPDF, self::PDF_RENDERER_MPDF);
    private static $_zipClass = self::ZIPARCHIVE;
    private static $_chartRendererName = NULL;
    private static $_chartRendererPath = NULL;
    private static $_pdfRendererName = NULL;
    private static $_pdfRendererPath = NULL;
    public static function setZipClass($zipClass)
    {
        if (($zipClass === self::PCLZIP) || ($zipClass === self::ZIPARCHIVE)) {
            self::$_zipClass = $zipClass;
            return TRUE;
        }
        return FALSE;
    }
    public static function getZipClass()
    {
        return self::$_zipClass;
    }
    public static function getCacheStorageMethod()
    {
        return PHPExcel_CachedObjectStorageFactory::getCacheStorageMethod();
    }
    public static function getCacheStorageClass()
    {
        return PHPExcel_CachedObjectStorageFactory::getCacheStorageClass();
    }
    public static function setCacheStorageMethod($method = PHPExcel_CachedObjectStorageFactory::cache_in_memory, $arguments = array())
    {
        return PHPExcel_CachedObjectStorageFactory::initialize($method, $arguments);
    }
    public static function setLocale($locale = 'en_us')
    {
        return PHPExcel_Calculation::getInstance()->setLocale($locale);
    }
    public static function setChartRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setChartRendererName($libraryName))
            return FALSE;
        return self::setChartRendererPath($libraryBaseDir);
    }
    public static function setChartRendererName($libraryName)
    {
        if (!in_array($libraryName, self::$_chartRenderers)) {
            return FALSE;
        }
        self::$_chartRendererName = $libraryName;
        return TRUE;
    }
    public static function setChartRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return FALSE;
        }
        self::$_chartRendererPath = $libraryBaseDir;
        return TRUE;
    }
    public static function getChartRendererName()
    {
        return self::$_chartRendererName;
    }
    public static function getChartRendererPath()
    {
        return self::$_chartRendererPath;
    }
    public static function setPdfRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setPdfRendererName($libraryName))
            return FALSE;
        return self::setPdfRendererPath($libraryBaseDir);
    }
    public static function setPdfRendererName($libraryName)
    {
        if (!in_array($libraryName, self::$_pdfRenderers)) {
            return FALSE;
        }
        self::$_pdfRendererName = $libraryName;
        return TRUE;
    }
    public static function setPdfRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return FALSE;
        }
        self::$_pdfRendererPath = $libraryBaseDir;
        return TRUE;
    }
    public static function getPdfRendererName()
    {
        return self::$_pdfRendererName;
    }
    public static function getPdfRendererPath()
    {
        return self::$_pdfRendererPath;
    }
}