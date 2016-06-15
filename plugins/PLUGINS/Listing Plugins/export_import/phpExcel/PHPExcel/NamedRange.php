<?php
class PHPExcel_NamedRange
{
    private $_name;
    private $_worksheet;
    private $_range;
    private $_localOnly;
    private $_scope;
    public function __construct($pName = null, PHPExcel_Worksheet $pWorksheet, $pRange = 'A1', $pLocalOnly = false, $pScope = null)
    {
        if (($pName === NULL) || ($pWorksheet === NULL) || ($pRange === NULL)) {
            throw new PHPExcel_Exception('Parameters can not be null.');
        }
        $this->_name      = $pName;
        $this->_worksheet = $pWorksheet;
        $this->_range     = $pRange;
        $this->_localOnly = $pLocalOnly;
        $this->_scope     = ($pLocalOnly == true) ? (($pScope == null) ? $pWorksheet : $pScope) : null;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function setName($value = null)
    {
        if ($value !== NULL) {
            $oldTitle = $this->_name;
            if ($this->_worksheet !== NULL) {
                $this->_worksheet->getParent()->removeNamedRange($this->_name, $this->_worksheet);
            }
            $this->_name = $value;
            if ($this->_worksheet !== NULL) {
                $this->_worksheet->getParent()->addNamedRange($this);
            }
            $newTitle = $this->_name;
            PHPExcel_ReferenceHelper::getInstance()->updateNamedFormulas($this->_worksheet->getParent(), $oldTitle, $newTitle);
        }
        return $this;
    }
    public function getWorksheet()
    {
        return $this->_worksheet;
    }
    public function setWorksheet(PHPExcel_Worksheet $value = null)
    {
        if ($value !== NULL) {
            $this->_worksheet = $value;
        }
        return $this;
    }
    public function getRange()
    {
        return $this->_range;
    }
    public function setRange($value = null)
    {
        if ($value !== NULL) {
            $this->_range = $value;
        }
        return $this;
    }
    public function getLocalOnly()
    {
        return $this->_localOnly;
    }
    public function setLocalOnly($value = false)
    {
        $this->_localOnly = $value;
        $this->_scope     = $value ? $this->_worksheet : null;
        return $this;
    }
    public function getScope()
    {
        return $this->_scope;
    }
    public function setScope(PHPExcel_Worksheet $value = null)
    {
        $this->_scope     = $value;
        $this->_localOnly = ($value == null) ? false : true;
        return $this;
    }
    public static function resolveRange($pNamedRange = '', PHPExcel_Worksheet $pSheet)
    {
        return $pSheet->getParent()->getNamedRange($pNamedRange, $pSheet);
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