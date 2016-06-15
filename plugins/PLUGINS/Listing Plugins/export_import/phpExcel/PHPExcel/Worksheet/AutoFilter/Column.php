<?php
class PHPExcel_Worksheet_AutoFilter_Column
{
    const AUTOFILTER_FILTERTYPE_FILTER = 'filters';
    const AUTOFILTER_FILTERTYPE_CUSTOMFILTER = 'customFilters';
    const AUTOFILTER_FILTERTYPE_DYNAMICFILTER = 'dynamicFilter';
    const AUTOFILTER_FILTERTYPE_TOPTENFILTER = 'top10';
    private static $_filterTypes = array(self::AUTOFILTER_FILTERTYPE_FILTER, self::AUTOFILTER_FILTERTYPE_CUSTOMFILTER, self::AUTOFILTER_FILTERTYPE_DYNAMICFILTER, self::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
    const AUTOFILTER_COLUMN_JOIN_AND = 'and';
    const AUTOFILTER_COLUMN_JOIN_OR = 'or';
    private static $_ruleJoins = array(self::AUTOFILTER_COLUMN_JOIN_AND, self::AUTOFILTER_COLUMN_JOIN_OR);
    private $_parent = NULL;
    private $_columnIndex = '';
    private $_filterType = self::AUTOFILTER_FILTERTYPE_FILTER;
    private $_join = self::AUTOFILTER_COLUMN_JOIN_OR;
    private $_ruleset = array();
    private $_attributes = array();
    public function __construct($pColumn, PHPExcel_Worksheet_AutoFilter $pParent = NULL)
    {
        $this->_columnIndex = $pColumn;
        $this->_parent      = $pParent;
    }
    public function getColumnIndex()
    {
        return $this->_columnIndex;
    }
    public function setColumnIndex($pColumn)
    {
        $pColumn = strtoupper($pColumn);
        if ($this->_parent !== NULL) {
            $this->_parent->testColumnInRange($pColumn);
        }
        $this->_columnIndex = $pColumn;
        return $this;
    }
    public function getParent()
    {
        return $this->_parent;
    }
    public function setParent(PHPExcel_Worksheet_AutoFilter $pParent = NULL)
    {
        $this->_parent = $pParent;
        return $this;
    }
    public function getFilterType()
    {
        return $this->_filterType;
    }
    public function setFilterType($pFilterType = self::AUTOFILTER_FILTERTYPE_FILTER)
    {
        if (!in_array($pFilterType, self::$_filterTypes)) {
            throw new PHPExcel_Exception('Invalid filter type for column AutoFilter.');
        }
        $this->_filterType = $pFilterType;
        return $this;
    }
    public function getJoin()
    {
        return $this->_join;
    }
    public function setJoin($pJoin = self::AUTOFILTER_COLUMN_JOIN_OR)
    {
        $pJoin = strtolower($pJoin);
        if (!in_array($pJoin, self::$_ruleJoins)) {
            throw new PHPExcel_Exception('Invalid rule connection for column AutoFilter.');
        }
        $this->_join = $pJoin;
        return $this;
    }
    public function setAttributes($pAttributes = array())
    {
        $this->_attributes = $pAttributes;
        return $this;
    }
    public function setAttribute($pName, $pValue)
    {
        $this->_attributes[$pName] = $pValue;
        return $this;
    }
    public function getAttributes()
    {
        return $this->_attributes;
    }
    public function getAttribute($pName)
    {
        if (isset($this->_attributes[$pName]))
            return $this->_attributes[$pName];
        return NULL;
    }
    public function getRules()
    {
        return $this->_ruleset;
    }
    public function getRule($pIndex)
    {
        if (!isset($this->_ruleset[$pIndex])) {
            $this->_ruleset[$pIndex] = new PHPExcel_Worksheet_AutoFilter_Column_Rule($this);
        }
        return $this->_ruleset[$pIndex];
    }
    public function createRule()
    {
        $this->_ruleset[] = new PHPExcel_Worksheet_AutoFilter_Column_Rule($this);
        return end($this->_ruleset);
    }
    public function addRule(PHPExcel_Worksheet_AutoFilter_Column_Rule $pRule, $returnRule = TRUE)
    {
        $pRule->setParent($this);
        $this->_ruleset[] = $pRule;
        return ($returnRule) ? $pRule : $this;
    }
    public function deleteRule($pIndex)
    {
        if (isset($this->_ruleset[$pIndex])) {
            unset($this->_ruleset[$pIndex]);
            if (count($this->_ruleset) <= 1) {
                $this->setJoin(self::AUTOFILTER_COLUMN_JOIN_OR);
            }
        }
        return $this;
    }
    public function clearRules()
    {
        $this->_ruleset = array();
        $this->setJoin(self::AUTOFILTER_COLUMN_JOIN_OR);
        return $this;
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                if ($key == '_parent') {
                    $this->$key = NULL;
                } else {
                    $this->$key = clone $value;
                }
            } elseif ((is_array($value)) && ($key == '_ruleset')) {
                $this->$key = array();
                foreach ($value as $k => $v) {
                    $this->$key[$k] = clone $v;
                    $this->$key[$k]->setParent($this);
                }
            } else {
                $this->$key = $value;
            }
        }
    }
}