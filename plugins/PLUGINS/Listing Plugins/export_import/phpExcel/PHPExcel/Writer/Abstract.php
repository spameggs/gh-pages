<?php
abstract class PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter
{
    protected $_includeCharts = FALSE;
    protected $_preCalculateFormulas = TRUE;
    protected $_useDiskCaching = FALSE;
    protected $_diskCachingDirectory = './';
    public function getIncludeCharts()
    {
        return $this->_includeCharts;
    }
    public function setIncludeCharts($pValue = FALSE)
    {
        $this->_includeCharts = (boolean) $pValue;
        return $this;
    }
    public function getPreCalculateFormulas()
    {
        return $this->_preCalculateFormulas;
    }
    public function setPreCalculateFormulas($pValue = TRUE)
    {
        $this->_preCalculateFormulas = (boolean) $pValue;
        return $this;
    }
    public function getUseDiskCaching()
    {
        return $this->_useDiskCaching;
    }
    public function setUseDiskCaching($pValue = FALSE, $pDirectory = NULL)
    {
        $this->_useDiskCaching = $pValue;
        if ($pDirectory !== NULL) {
            if (is_dir($pDirectory)) {
                $this->_diskCachingDirectory = $pDirectory;
            } else {
                throw new PHPExcel_Writer_Exception("Directory does not exist: $pDirectory");
            }
        }
        return $this;
    }
    public function getDiskCachingDirectory()
    {
        return $this->_diskCachingDirectory;
    }
}