<?php
class PHPExcel_Writer_Excel5_BIFFwriter
{
    private static $_byte_order;
    public $_data;
    public $_datasize;
    public $_limit = 8224;
    public function __construct()
    {
        $this->_data     = '';
        $this->_datasize = 0;
    }
    public static function getByteOrder()
    {
        if (!isset(self::$_byte_order)) {
            $teststr = pack("d", 1.2345);
            $number  = pack("C8", 0x8D, 0x97, 0x6E, 0x12, 0x83, 0xC0, 0xF3, 0x3F);
            if ($number == $teststr) {
                $byte_order = 0;
            } elseif ($number == strrev($teststr)) {
                $byte_order = 1;
            } else {
                throw new PHPExcel_Writer_Exception("Required floating point format not supported on this platform.");
            }
            self::$_byte_order = $byte_order;
        }
        return self::$_byte_order;
    }
    function _append($data)
    {
        if (strlen($data) - 4 > $this->_limit) {
            $data = $this->_addContinue($data);
        }
        $this->_data .= $data;
        $this->_datasize += strlen($data);
    }
    public function writeData($data)
    {
        if (strlen($data) - 4 > $this->_limit) {
            $data = $this->_addContinue($data);
        }
        $this->_datasize += strlen($data);
        return $data;
    }
    function _storeBof($type)
    {
        $record  = 0x0809;
        $length  = 0x0010;
        $unknown = pack("VV", 0x000100D1, 0x00000406);
        $build   = 0x0DBB;
        $year    = 0x07CC;
        $version = 0x0600;
        $header  = pack("vv", $record, $length);
        $data    = pack("vvvv", $version, $type, $build, $year);
        $this->_append($header . $data . $unknown);
    }
    function _storeEof()
    {
        $record = 0x000A;
        $length = 0x0000;
        $header = pack("vv", $record, $length);
        $this->_append($header);
    }
    public function writeEof()
    {
        $record = 0x000A;
        $length = 0x0000;
        $header = pack("vv", $record, $length);
        return $this->writeData($header);
    }
    function _addContinue($data)
    {
        $limit       = $this->_limit;
        $record      = 0x003C;
        $tmp         = substr($data, 0, 2) . pack("v", $limit) . substr($data, 4, $limit);
        $header      = pack("vv", $record, $limit);
        $data_length = strlen($data);
        for ($i = $limit + 4; $i < ($data_length - $limit); $i += $limit) {
            $tmp .= $header;
            $tmp .= substr($data, $i, $limit);
        }
        $header = pack("vv", $record, strlen($data) - $i);
        $tmp .= $header;
        $tmp .= substr($data, $i, strlen($data) - $i);
        return $tmp;
    }
}