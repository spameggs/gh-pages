<?php
class PHPExcel_Shared_Escher
{
    private $_dggContainer;
    private $_dgContainer;
    public function getDggContainer()
    {
        return $this->_dggContainer;
    }
    public function setDggContainer($dggContainer)
    {
        return $this->_dggContainer = $dggContainer;
    }
    public function getDgContainer()
    {
        return $this->_dgContainer;
    }
    public function setDgContainer($dgContainer)
    {
        return $this->_dgContainer = $dgContainer;
    }
}