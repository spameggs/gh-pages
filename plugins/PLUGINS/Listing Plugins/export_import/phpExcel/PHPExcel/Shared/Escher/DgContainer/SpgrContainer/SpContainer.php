<?php
class PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer
{
    private $_parent;
    private $_spgr = false;
    private $_spType;
    private $_spFlag;
    private $_spId;
    private $_OPT;
    private $_startCoordinates;
    private $_startOffsetX;
    private $_startOffsetY;
    private $_endCoordinates;
    private $_endOffsetX;
    private $_endOffsetY;
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }
    public function getParent()
    {
        return $this->_parent;
    }
    public function setSpgr($value = false)
    {
        $this->_spgr = $value;
    }
    public function getSpgr()
    {
        return $this->_spgr;
    }
    public function setSpType($value)
    {
        $this->_spType = $value;
    }
    public function getSpType()
    {
        return $this->_spType;
    }
    public function setSpFlag($value)
    {
        $this->_spFlag = $value;
    }
    public function getSpFlag()
    {
        return $this->_spFlag;
    }
    public function setSpId($value)
    {
        $this->_spId = $value;
    }
    public function getSpId()
    {
        return $this->_spId;
    }
    public function setOPT($property, $value)
    {
        $this->_OPT[$property] = $value;
    }
    public function getOPT($property)
    {
        if (isset($this->_OPT[$property])) {
            return $this->_OPT[$property];
        }
        return null;
    }
    public function getOPTCollection()
    {
        return $this->_OPT;
    }
    public function setStartCoordinates($value = 'A1')
    {
        $this->_startCoordinates = $value;
    }
    public function getStartCoordinates()
    {
        return $this->_startCoordinates;
    }
    public function setStartOffsetX($startOffsetX = 0)
    {
        $this->_startOffsetX = $startOffsetX;
    }
    public function getStartOffsetX()
    {
        return $this->_startOffsetX;
    }
    public function setStartOffsetY($startOffsetY = 0)
    {
        $this->_startOffsetY = $startOffsetY;
    }
    public function getStartOffsetY()
    {
        return $this->_startOffsetY;
    }
    public function setEndCoordinates($value = 'A1')
    {
        $this->_endCoordinates = $value;
    }
    public function getEndCoordinates()
    {
        return $this->_endCoordinates;
    }
    public function setEndOffsetX($endOffsetX = 0)
    {
        $this->_endOffsetX = $endOffsetX;
    }
    public function getEndOffsetX()
    {
        return $this->_endOffsetX;
    }
    public function setEndOffsetY($endOffsetY = 0)
    {
        $this->_endOffsetY = $endOffsetY;
    }
    public function getEndOffsetY()
    {
        return $this->_endOffsetY;
    }
    public function getNestingLevel()
    {
        $nestingLevel = 0;
        $parent       = $this->getParent();
        while ($parent instanceof PHPExcel_Shared_Escher_DgContainer_SpgrContainer) {
            ++$nestingLevel;
            $parent = $parent->getParent();
        }
        return $nestingLevel;
    }
}