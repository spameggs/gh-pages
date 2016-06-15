<?php
abstract class PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    protected $_readDataOnly = FALSE;
    protected $_includeCharts = FALSE;
    protected $_loadSheetsOnly = NULL;
    protected $_readFilter = NULL;
    public function getReadDataOnly()
    {
        return $this->_readDataOnly;
    }
    public function setReadDataOnly($pValue = FALSE)
    {
        $this->_readDataOnly = $pValue;
        return $this;
    }
    public function getIncludeCharts()
    {
        return $this->_includeCharts;
    }
    public function setIncludeCharts($pValue = FALSE)
    {
        $this->_includeCharts = (boolean) $pValue;
        return $this;
    }
    public function getLoadSheetsOnly()
    {
        return $this->_loadSheetsOnly;
    }
    public function setLoadSheetsOnly($value = NULL)
    {
        $this->_loadSheetsOnly = is_array($value) ? $value : array(
            $value
        );
        return $this;
    }
    public function setLoadAllSheets()
    {
        $this->_loadSheetsOnly = NULL;
        return $this;
    }
    public function getReadFilter()
    {
        return $this->_readFilter;
    }
    public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue)
    {
        $this->_readFilter = $pValue;
        return $this;
    }
}