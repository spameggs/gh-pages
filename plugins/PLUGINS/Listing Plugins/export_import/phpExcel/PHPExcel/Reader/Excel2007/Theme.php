<?php
class PHPExcel_Reader_Excel2007_Theme
{
    private $_themeName;
    private $_colourSchemeName;
    private $_colourMapValues;
    private $_colourMap;
    public function __construct($themeName, $colourSchemeName, $colourMap)
    {
        $this->_themeName        = $themeName;
        $this->_colourSchemeName = $colourSchemeName;
        $this->_colourMap        = $colourMap;
    }
    public function getThemeName()
    {
        return $this->_themeName;
    }
    public function getColourSchemeName()
    {
        return $this->_colourSchemeName;
    }
    public function getColourByIndex($index = 0)
    {
        if (isset($this->_colourMap[$index])) {
            return $this->_colourMap[$index];
        }
        return null;
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != '_parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}