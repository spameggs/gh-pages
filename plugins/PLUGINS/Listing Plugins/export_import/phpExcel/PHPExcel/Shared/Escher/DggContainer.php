<?php
class PHPExcel_Shared_Escher_DggContainer
{
    private $_spIdMax;
    private $_cDgSaved;
    private $_cSpSaved;
    private $_bstoreContainer;
    private $_OPT = array();
    private $_IDCLs = array();
    public function getSpIdMax()
    {
        return $this->_spIdMax;
    }
    public function setSpIdMax($value)
    {
        $this->_spIdMax = $value;
    }
    public function getCDgSaved()
    {
        return $this->_cDgSaved;
    }
    public function setCDgSaved($value)
    {
        $this->_cDgSaved = $value;
    }
    public function getCSpSaved()
    {
        return $this->_cSpSaved;
    }
    public function setCSpSaved($value)
    {
        $this->_cSpSaved = $value;
    }
    public function getBstoreContainer()
    {
        return $this->_bstoreContainer;
    }
    public function setBstoreContainer($bstoreContainer)
    {
        $this->_bstoreContainer = $bstoreContainer;
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
    public function getIDCLs()
    {
        return $this->_IDCLs;
    }
    public function setIDCLs($pValue)
    {
        $this->_IDCLs = $pValue;
    }
}