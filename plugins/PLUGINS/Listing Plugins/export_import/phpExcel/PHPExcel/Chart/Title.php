<?php
class PHPExcel_Chart_Title
{
    private $_caption = null;
    private $_layout = null;
    public function __construct($caption = null, PHPExcel_Chart_Layout $layout = null)
    {
        $this->_caption = $caption;
        $this->_layout  = $layout;
    }
    public function getCaption()
    {
        return $this->_caption;
    }
    public function setCaption($caption = null)
    {
        $this->_caption = $caption;
    }
    public function getLayout()
    {
        return $this->_layout;
    }
}