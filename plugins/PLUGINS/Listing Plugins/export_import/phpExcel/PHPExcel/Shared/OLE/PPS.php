<?php
class PHPExcel_Shared_OLE_PPS
{
    public $No;
    public $Name;
    public $Type;
    public $PrevPps;
    public $NextPps;
    public $DirPps;
    public $Time1st;
    public $Time2nd;
    public $_StartBlock;
    public $Size;
    public $_data;
    public $children = array();
    public $ole;
    public function __construct($No, $name, $type, $prev, $next, $dir, $time_1st, $time_2nd, $data, $children)
    {
        $this->No       = $No;
        $this->Name     = $name;
        $this->Type     = $type;
        $this->PrevPps  = $prev;
        $this->NextPps  = $next;
        $this->DirPps   = $dir;
        $this->Time1st  = $time_1st;
        $this->Time2nd  = $time_2nd;
        $this->_data    = $data;
        $this->children = $children;
        if ($data != '') {
            $this->Size = strlen($data);
        } else {
            $this->Size = 0;
        }
    }
    public function _DataLen()
    {
        if (!isset($this->_data)) {
            return 0;
        }
        return strlen($this->_data);
    }
    public function _getPpsWk()
    {
        $ret = str_pad($this->Name, 64, "\x00");
        $ret .= pack("v", strlen($this->Name) + 2) . pack("c", $this->Type) . pack("c", 0x00) . pack("V", $this->PrevPps) . pack("V", $this->NextPps) . pack("V", $this->DirPps) . "\x00\x09\x02\x00" . "\x00\x00\x00\x00" . "\xc0\x00\x00\x00" . "\x00\x00\x00\x46" . "\x00\x00\x00\x00" . PHPExcel_Shared_OLE::LocalDate2OLE($this->Time1st) . PHPExcel_Shared_OLE::LocalDate2OLE($this->Time2nd) . pack("V", isset($this->_StartBlock) ? $this->_StartBlock : 0) . pack("V", $this->Size) . pack("V", 0);
        return $ret;
    }
    public static function _savePpsSetPnt(&$raList, $to_save, $depth = 0)
    {
        if (!is_array($to_save) || (empty($to_save))) {
            return 0xFFFFFFFF;
        } elseif (count($to_save) == 1) {
            $cnt                   = count($raList);
            $raList[$cnt]          = ($depth == 0) ? $to_save[0] : clone $to_save[0];
            $raList[$cnt]->No      = $cnt;
            $raList[$cnt]->PrevPps = 0xFFFFFFFF;
            $raList[$cnt]->NextPps = 0xFFFFFFFF;
            $raList[$cnt]->DirPps  = self::_savePpsSetPnt($raList, @$raList[$cnt]->children, $depth++);
        } else {
            $iPos                  = floor(count($to_save) / 2);
            $aPrev                 = array_slice($to_save, 0, $iPos);
            $aNext                 = array_slice($to_save, $iPos + 1);
            $cnt                   = count($raList);
            $raList[$cnt]          = ($depth == 0) ? $to_save[$iPos] : clone $to_save[$iPos];
            $raList[$cnt]->No      = $cnt;
            $raList[$cnt]->PrevPps = self::_savePpsSetPnt($raList, $aPrev, $depth++);
            $raList[$cnt]->NextPps = self::_savePpsSetPnt($raList, $aNext, $depth++);
            $raList[$cnt]->DirPps  = self::_savePpsSetPnt($raList, @$raList[$cnt]->children, $depth++);
        }
        return $cnt;
    }
}