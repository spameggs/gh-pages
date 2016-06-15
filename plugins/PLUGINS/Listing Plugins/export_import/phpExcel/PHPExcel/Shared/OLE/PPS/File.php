<?php
class PHPExcel_Shared_OLE_PPS_File extends PHPExcel_Shared_OLE_PPS
{
    public function __construct($name)
    {
        parent::__construct(null, $name, PHPExcel_Shared_OLE::OLE_PPS_TYPE_FILE, null, null, null, null, null, '', array());
    }
    public function init()
    {
        return true;
    }
    public function append($data)
    {
        $this->_data .= $data;
    }
    public function getStream()
    {
        $this->ole->getStream($this);
    }
}