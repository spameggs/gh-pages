<?php
class PHPExcel_Worksheet_RowDimension
{
    private $_rowIndex;
    private $_rowHeight = -1;
    private $_zeroHeight = false;
    private $_visible = true;
    private $_outlineLevel = 0;
    private $_collapsed = false;
    private $_xfIndex;
    public function __construct($pIndex = 0)
    {
        $this->_rowIndex = $pIndex;
        $this->_xfIndex  = null;
    }
    public function getRowIndex()
    {
        return $this->_rowIndex;
    }
    public function setRowIndex($pValue)
    {
        $this->_rowIndex = $pValue;
        return $this;
    }
    public function getRowHeight()
    {
        return $this->_rowHeight;
    }
    public function setRowHeight($pValue = -1)
    {
        $this->_rowHeight = $pValue;
        return $this;
    }
    public function getzeroHeight()
    {
        return $this->_zeroHeight;
    }
    public function setzeroHeight($pValue = false)
    {
        $this->_zeroHeight = $pValue;
        return $this;
    }
    public function getVisible()
    {
        return $this->_visible;
    }
    public function setVisible($pValue = true)
    {
        $this->_visible = $pValue;
        return $this;
    }
    public function getOutlineLevel()
    {
        return $this->_outlineLevel;
    }
    public function setOutlineLevel($pValue)
    {
        if ($pValue < 0 || $pValue > 7) {
            throw new PHPExcel_Exception("Outline level must range between 0 and 7.");
        }
        $this->_outlineLevel = $pValue;
        return $this;
    }
    public function getCollapsed()
    {
        return $this->_collapsed;
    }
    public function setCollapsed($pValue = true)
    {
        $this->_collapsed = $pValue;
        return $this;
    }
    public function getXfIndex()
    {
        return $this->_xfIndex;
    }
    public function setXfIndex($pValue = 0)
    {
        $this->_xfIndex = $pValue;
        return $this;
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}