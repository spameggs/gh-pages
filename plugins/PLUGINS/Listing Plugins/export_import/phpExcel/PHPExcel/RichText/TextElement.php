<?php
class PHPExcel_RichText_TextElement implements PHPExcel_RichText_ITextElement
{
    private $_text;
    public function __construct($pText = '')
    {
        $this->_text = $pText;
    }
    public function getText()
    {
        return $this->_text;
    }
    public function setText($pText = '')
    {
        $this->_text = $pText;
        return $this;
    }
    public function getFont()
    {
        return null;
    }
    public function getHashCode()
    {
        return md5($this->_text . __CLASS__);
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