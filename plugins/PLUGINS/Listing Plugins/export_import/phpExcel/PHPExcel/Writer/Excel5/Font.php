<?php
class PHPExcel_Writer_Excel5_Font
{
    private $_colorIndex;
    private $_font;
    public function __construct(PHPExcel_Style_Font $font = null)
    {
        $this->_colorIndex = 0x7FFF;
        $this->_font       = $font;
    }
    public function setColorIndex($colorIndex)
    {
        $this->_colorIndex = $colorIndex;
    }
    public function writeFont()
    {
        $font_outline = 0;
        $font_shadow  = 0;
        $icv          = $this->_colorIndex;
        if ($this->_font->getSuperScript()) {
            $sss = 1;
        } else if ($this->_font->getSubScript()) {
            $sss = 2;
        } else {
            $sss = 0;
        }
        $bFamily  = 0;
        $bCharSet = PHPExcel_Shared_Font::getCharsetFromFontName($this->_font->getName());
        $record   = 0x31;
        $reserved = 0x00;
        $grbit    = 0x00;
        if ($this->_font->getItalic()) {
            $grbit |= 0x02;
        }
        if ($this->_font->getStrikethrough()) {
            $grbit |= 0x08;
        }
        if ($font_outline) {
            $grbit |= 0x10;
        }
        if ($font_shadow) {
            $grbit |= 0x20;
        }
        $data = pack("vvvvvCCCC", $this->_font->getSize() * 20, $grbit, $icv, self::_mapBold($this->_font->getBold()), $sss, self::_mapUnderline($this->_font->getUnderline()), $bFamily, $bCharSet, $reserved);
        $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($this->_font->getName());
        $length = strlen($data);
        $header = pack("vv", $record, $length);
        return ($header . $data);
    }
    private static function _mapBold($bold)
    {
        if ($bold) {
            return 0x2BC;
        }
        return 0x190;
    }
    private static $_mapUnderline = array(PHPExcel_Style_Font::UNDERLINE_NONE => 0x00, PHPExcel_Style_Font::UNDERLINE_SINGLE => 0x01, PHPExcel_Style_Font::UNDERLINE_DOUBLE => 0x02, PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING => 0x21, PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING => 0x22);
    private static function _mapUnderline($underline)
    {
        if (isset(self::$_mapUnderline[$underline]))
            return self::$_mapUnderline[$underline];
        return 0x00;
    }
}