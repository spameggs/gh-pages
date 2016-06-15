<?php
class PHPExcel_Cell_Hyperlink
{
    private $_url;
    private $_tooltip;
    public function __construct($pUrl = '', $pTooltip = '')
    {
        $this->_url     = $pUrl;
        $this->_tooltip = $pTooltip;
    }
    public function getUrl()
    {
        return $this->_url;
    }
    public function setUrl($value = '')
    {
        $this->_url = $value;
        return $this;
    }
    public function getTooltip()
    {
        return $this->_tooltip;
    }
    public function setTooltip($value = '')
    {
        $this->_tooltip = $value;
        return $this;
    }
    public function isInternal()
    {
        return strpos($this->_url, 'sheet://') !== false;
    }
    public function getHashCode()
    {
        return md5($this->_url . $this->_tooltip . __CLASS__);
    }
}