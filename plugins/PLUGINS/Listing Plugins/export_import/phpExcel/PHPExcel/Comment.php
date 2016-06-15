<?php
class PHPExcel_Comment implements PHPExcel_IComparable
{
    private $_author;
    private $_text;
    private $_width = '96pt';
    private $_marginLeft = '59.25pt';
    private $_marginTop = '1.5pt';
    private $_visible = false;
    private $_height = '55.5pt';
    private $_fillColor;
    private $_alignment;
    public function __construct()
    {
        $this->_author    = 'Author';
        $this->_text      = new PHPExcel_RichText();
        $this->_fillColor = new PHPExcel_Style_Color('FFFFFFE1');
        $this->_alignment = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
    }
    public function getAuthor()
    {
        return $this->_author;
    }
    public function setAuthor($pValue = '')
    {
        $this->_author = $pValue;
        return $this;
    }
    public function getText()
    {
        return $this->_text;
    }
    public function setText(PHPExcel_RichText $pValue)
    {
        $this->_text = $pValue;
        return $this;
    }
    public function getWidth()
    {
        return $this->_width;
    }
    public function setWidth($value = '96pt')
    {
        $this->_width = $value;
        return $this;
    }
    public function getHeight()
    {
        return $this->_height;
    }
    public function setHeight($value = '55.5pt')
    {
        $this->_height = $value;
        return $this;
    }
    public function getMarginLeft()
    {
        return $this->_marginLeft;
    }
    public function setMarginLeft($value = '59.25pt')
    {
        $this->_marginLeft = $value;
        return $this;
    }
    public function getMarginTop()
    {
        return $this->_marginTop;
    }
    public function setMarginTop($value = '1.5pt')
    {
        $this->_marginTop = $value;
        return $this;
    }
    public function getVisible()
    {
        return $this->_visible;
    }
    public function setVisible($value = false)
    {
        $this->_visible = $value;
        return $this;
    }
    public function getFillColor()
    {
        return $this->_fillColor;
    }
    public function setAlignment($pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL)
    {
        $this->_alignment = $pValue;
        return $this;
    }
    public function getAlignment()
    {
        return $this->_alignment;
    }
    public function getHashCode()
    {
        return md5($this->_author . $this->_text->getHashCode() . $this->_width . $this->_height . $this->_marginLeft . $this->_marginTop . ($this->_visible ? 1 : 0) . $this->_fillColor->getHashCode() . $this->_alignment . __CLASS__);
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