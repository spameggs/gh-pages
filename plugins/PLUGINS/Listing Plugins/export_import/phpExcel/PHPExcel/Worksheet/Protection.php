<?php
class PHPExcel_Worksheet_Protection
{
    private $_sheet = false;
    private $_objects = false;
    private $_scenarios = false;
    private $_formatCells = false;
    private $_formatColumns = false;
    private $_formatRows = false;
    private $_insertColumns = false;
    private $_insertRows = false;
    private $_insertHyperlinks = false;
    private $_deleteColumns = false;
    private $_deleteRows = false;
    private $_selectLockedCells = false;
    private $_sort = false;
    private $_autoFilter = false;
    private $_pivotTables = false;
    private $_selectUnlockedCells = false;
    private $_password = '';
    public function __construct()
    {
    }
    function isProtectionEnabled()
    {
        return $this->_sheet || $this->_objects || $this->_scenarios || $this->_formatCells || $this->_formatColumns || $this->_formatRows || $this->_insertColumns || $this->_insertRows || $this->_insertHyperlinks || $this->_deleteColumns || $this->_deleteRows || $this->_selectLockedCells || $this->_sort || $this->_autoFilter || $this->_pivotTables || $this->_selectUnlockedCells;
    }
    function getSheet()
    {
        return $this->_sheet;
    }
    function setSheet($pValue = false)
    {
        $this->_sheet = $pValue;
        return $this;
    }
    function getObjects()
    {
        return $this->_objects;
    }
    function setObjects($pValue = false)
    {
        $this->_objects = $pValue;
        return $this;
    }
    function getScenarios()
    {
        return $this->_scenarios;
    }
    function setScenarios($pValue = false)
    {
        $this->_scenarios = $pValue;
        return $this;
    }
    function getFormatCells()
    {
        return $this->_formatCells;
    }
    function setFormatCells($pValue = false)
    {
        $this->_formatCells = $pValue;
        return $this;
    }
    function getFormatColumns()
    {
        return $this->_formatColumns;
    }
    function setFormatColumns($pValue = false)
    {
        $this->_formatColumns = $pValue;
        return $this;
    }
    function getFormatRows()
    {
        return $this->_formatRows;
    }
    function setFormatRows($pValue = false)
    {
        $this->_formatRows = $pValue;
        return $this;
    }
    function getInsertColumns()
    {
        return $this->_insertColumns;
    }
    function setInsertColumns($pValue = false)
    {
        $this->_insertColumns = $pValue;
        return $this;
    }
    function getInsertRows()
    {
        return $this->_insertRows;
    }
    function setInsertRows($pValue = false)
    {
        $this->_insertRows = $pValue;
        return $this;
    }
    function getInsertHyperlinks()
    {
        return $this->_insertHyperlinks;
    }
    function setInsertHyperlinks($pValue = false)
    {
        $this->_insertHyperlinks = $pValue;
        return $this;
    }
    function getDeleteColumns()
    {
        return $this->_deleteColumns;
    }
    function setDeleteColumns($pValue = false)
    {
        $this->_deleteColumns = $pValue;
        return $this;
    }
    function getDeleteRows()
    {
        return $this->_deleteRows;
    }
    function setDeleteRows($pValue = false)
    {
        $this->_deleteRows = $pValue;
        return $this;
    }
    function getSelectLockedCells()
    {
        return $this->_selectLockedCells;
    }
    function setSelectLockedCells($pValue = false)
    {
        $this->_selectLockedCells = $pValue;
        return $this;
    }
    function getSort()
    {
        return $this->_sort;
    }
    function setSort($pValue = false)
    {
        $this->_sort = $pValue;
        return $this;
    }
    function getAutoFilter()
    {
        return $this->_autoFilter;
    }
    function setAutoFilter($pValue = false)
    {
        $this->_autoFilter = $pValue;
        return $this;
    }
    function getPivotTables()
    {
        return $this->_pivotTables;
    }
    function setPivotTables($pValue = false)
    {
        $this->_pivotTables = $pValue;
        return $this;
    }
    function getSelectUnlockedCells()
    {
        return $this->_selectUnlockedCells;
    }
    function setSelectUnlockedCells($pValue = false)
    {
        $this->_selectUnlockedCells = $pValue;
        return $this;
    }
    function getPassword()
    {
        return $this->_password;
    }
    function setPassword($pValue = '', $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
        }
        $this->_password = $pValue;
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