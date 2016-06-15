<?php
class PHPExcel_Style_Conditional implements PHPExcel_IComparable
{
    const CONDITION_NONE = 'none';
    const CONDITION_CELLIS = 'cellIs';
    const CONDITION_CONTAINSTEXT = 'containsText';
    const CONDITION_EXPRESSION = 'expression';
    const OPERATOR_NONE = '';
    const OPERATOR_BEGINSWITH = 'beginsWith';
    const OPERATOR_ENDSWITH = 'endsWith';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATERTHAN = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    const OPERATOR_NOTEQUAL = 'notEqual';
    const OPERATOR_CONTAINSTEXT = 'containsText';
    const OPERATOR_NOTCONTAINS = 'notContains';
    const OPERATOR_BETWEEN = 'between';
    private $_conditionType;
    private $_operatorType;
    private $_text;
    private $_condition = array();
    private $_style;
    public function __construct()
    {
        $this->_conditionType = PHPExcel_Style_Conditional::CONDITION_NONE;
        $this->_operatorType  = PHPExcel_Style_Conditional::OPERATOR_NONE;
        $this->_text          = null;
        $this->_condition     = array();
        $this->_style         = new PHPExcel_Style(FALSE, TRUE);
    }
    public function getConditionType()
    {
        return $this->_conditionType;
    }
    public function setConditionType($pValue = PHPExcel_Style_Conditional::CONDITION_NONE)
    {
        $this->_conditionType = $pValue;
        return $this;
    }
    public function getOperatorType()
    {
        return $this->_operatorType;
    }
    public function setOperatorType($pValue = PHPExcel_Style_Conditional::OPERATOR_NONE)
    {
        $this->_operatorType = $pValue;
        return $this;
    }
    public function getText()
    {
        return $this->_text;
    }
    public function setText($value = null)
    {
        $this->_text = $value;
        return $this;
    }
    public function getCondition()
    {
        if (isset($this->_condition[0])) {
            return $this->_condition[0];
        }
        return '';
    }
    public function setCondition($pValue = '')
    {
        if (!is_array($pValue))
            $pValue = array(
                $pValue
            );
        return $this->setConditions($pValue);
    }
    public function getConditions()
    {
        return $this->_condition;
    }
    public function setConditions($pValue)
    {
        if (!is_array($pValue))
            $pValue = array(
                $pValue
            );
        $this->_condition = $pValue;
        return $this;
    }
    public function addCondition($pValue = '')
    {
        $this->_condition[] = $pValue;
        return $this;
    }
    public function getStyle()
    {
        return $this->_style;
    }
    public function setStyle(PHPExcel_Style $pValue = null)
    {
        $this->_style = $pValue;
        return $this;
    }
    public function getHashCode()
    {
        return md5($this->_conditionType . $this->_operatorType . implode(';', $this->_condition) . $this->_style->getHashCode() . __CLASS__);
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