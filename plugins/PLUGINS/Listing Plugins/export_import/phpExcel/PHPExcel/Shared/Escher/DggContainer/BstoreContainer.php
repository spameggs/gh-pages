<?php
class PHPExcel_Shared_Escher_DggContainer_BstoreContainer
{
    private $_BSECollection = array();
    public function addBSE($BSE)
    {
        $this->_BSECollection[] = $BSE;
        $BSE->setParent($this);
    }
    public function getBSECollection()
    {
        return $this->_BSECollection;
    }
}