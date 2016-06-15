<?php
class PHPExcel_RichText implements PHPExcel_IComparable
{
    private $_richTextElements;
    public function __construct(PHPExcel_Cell $pCell = null)
    {
        $this->_richTextElements = array();
        if ($pCell !== NULL) {
            if ($pCell->getValue() != "") {
                $objRun = new PHPExcel_RichText_Run($pCell->getValue());
                $objRun->setFont(clone $pCell->getParent()->getStyle($pCell->getCoordinate())->getFont());
                $this->addText($objRun);
            }
            $pCell->setValueExplicit($this, PHPExcel_Cell_DataType::TYPE_STRING);
        }
    }
    public function addText(PHPExcel_RichText_ITextElement $pText = null)
    {
        $this->_richTextElements[] = $pText;
        return $this;
    }
    public function createText($pText = '')
    {
        $objText = new PHPExcel_RichText_TextElement($pText);
        $this->addText($objText);
        return $objText;
    }
    public function createTextRun($pText = '')
    {
        $objText = new PHPExcel_RichText_Run($pText);
        $this->addText($objText);
        return $objText;
    }
    public function getPlainText()
    {
        $returnValue = '';
        foreach ($this->_richTextElements as $text) {
            $returnValue .= $text->getText();
        }
        return $returnValue;
    }
    public function __toString()
    {
        return $this->getPlainText();
    }
    public function getRichTextElements()
    {
        return $this->_richTextElements;
    }
    public function setRichTextElements($pElements = null)
    {
        if (is_array($pElements)) {
            $this->_richTextElements = $pElements;
        } else {
            throw new PHPExcel_Exception("Invalid PHPExcel_RichText_ITextElement[] array passed.");
        }
        return $this;
    }
    public function getHashCode()
    {
        $hashElements = '';
        foreach ($this->_richTextElements as $element) {
            $hashElements .= $element->getHashCode();
        }
        return md5($hashElements . __CLASS__);
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