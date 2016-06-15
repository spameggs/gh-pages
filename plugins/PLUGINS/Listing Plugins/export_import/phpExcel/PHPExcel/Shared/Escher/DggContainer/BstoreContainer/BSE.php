<?php
class PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE
{
    const BLIPTYPE_ERROR = 0x00;
    const BLIPTYPE_UNKNOWN = 0x01;
    const BLIPTYPE_EMF = 0x02;
    const BLIPTYPE_WMF = 0x03;
    const BLIPTYPE_PICT = 0x04;
    const BLIPTYPE_JPEG = 0x05;
    const BLIPTYPE_PNG = 0x06;
    const BLIPTYPE_DIB = 0x07;
    const BLIPTYPE_TIFF = 0x11;
    const BLIPTYPE_CMYKJPEG = 0x12;
    private $_parent;
    private $_blip;
    private $_blipType;
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }
    public function getBlip()
    {
        return $this->_blip;
    }
    public function setBlip($blip)
    {
        $this->_blip = $blip;
        $blip->setParent($this);
    }
    public function getBlipType()
    {
        return $this->_blipType;
    }
    public function setBlipType($blipType)
    {
        $this->_blipType = $blipType;
    }
}