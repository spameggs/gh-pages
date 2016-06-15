<?php
class PHPExcel_Calculation_Token_Stack
{
    private $_stack = array();
    private $_count = 0;
    public function count()
    {
        return $this->_count;
    }
    public function push($type, $value, $reference = null)
    {
        $this->_stack[$this->_count++] = array(
            'type' => $type,
            'value' => $value,
            'reference' => $reference
        );
        if ($type == 'Function') {
            $localeFunction = PHPExcel_Calculation::_localeFunc($value);
            if ($localeFunction != $value) {
                $this->_stack[($this->_count - 1)]['localeValue'] = $localeFunction;
            }
        }
    }
    public function pop()
    {
        if ($this->_count > 0) {
            return $this->_stack[--$this->_count];
        }
        return null;
    }
    public function last($n = 1)
    {
        if ($this->_count - $n < 0) {
            return null;
        }
        return $this->_stack[$this->_count - $n];
    }
    function __construct()
    {
    }
}
