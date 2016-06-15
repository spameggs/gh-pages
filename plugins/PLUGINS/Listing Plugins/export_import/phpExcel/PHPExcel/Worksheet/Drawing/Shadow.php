<?php
class PHPExcel_Worksheet_Drawing_Shadow implements PHPExcel_IComparable
{
    const SHADOW_BOTTOM = 'b';
    const SHADOW_BOTTOM_LEFT = 'bl';
    const SHADOW_BOTTOM_RIGHT = 'br';
    const SHADOW_CENTER = 'ctr';
    const SHADOW_LEFT = 'l';
    const SHADOW_TOP = 't';
    const SHADOW_TOP_LEFT = 'tl';
    const SHADOW_TOP_RIGHT = 'tr';
    private $_visible;
    private $_blurRadius;
    private $_distance;
    private $_direction;
    private $_alignment;
    private $_color;
    private $_alpha;
    public function __construct()
    {
        $this->_visible    = false;
        $this->_blurRadius = 6;
        $this->_distance   = 2;
        $this->_direction  = 0;
        $this->_alignment  = PHPExcel_Worksheet_Drawing_Shadow::SHADOW_BOTTOM_RIGHT;
        $this->_color      = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
        $this->_alpha      = 50;
    }
    public function getVisible()
    {
        return $this->_visible;
    }
    public function setVisible($pValue = false)
    {
        $this->_visible = $pValue;
        return $this;
    }
    public function getBlurRadius()
    {
        return $this->_blurRadius;
    }
    public function setBlurRadius($pValue = 6)
    {
        $this->_blurRadius = $pValue;
        return $this;
    }
    public function getDistance()
    {
        return $this->_distance;
    }
    public function setDistance($pValue = 2)
    {
        $this->_distance = $pValue;
        return $this;
    }
    public function getDirection()
    {
        return $this->_direction;
    }
    public function setDirection($pValue = 0)
    {
        $this->_direction = $pValue;
        return $this;
    }
    public function getAlignment()
    {
        return $this->_alignment;
    }
    public function setAlignment($pValue = 0)
    {
        $this->_alignment = $pValue;
        return $this;
    }
    public function getColor()
    {
        return $this->_color;
    }
    public function setColor(PHPExcel_Style_Color $pValue = null)
    {
        $this->_color = $pValue;
        return $this;
    }
    public function getAlpha()
    {
        return $this->_alpha;
    }
    public function setAlpha($pValue = 0)
    {
        $this->_alpha = $pValue;
        return $this;
    }
    public function getHashCode()
    {
        return md5(($this->_visible ? 't' : 'f') . $this->_blurRadius . $this->_distance . $this->_direction . $this->_alignment . $this->_color->getHashCode() . $this->_alpha . __CLASS__);
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