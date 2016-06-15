<?php
class PHPExcel_Chart_DataSeriesValues
{
    const DATASERIES_TYPE_STRING = 'String';
    const DATASERIES_TYPE_NUMBER = 'Number';
    private static $_dataTypeValues = array(self::DATASERIES_TYPE_STRING, self::DATASERIES_TYPE_NUMBER);
    private $_dataType = null;
    private $_dataSource = null;
    private $_formatCode = null;
    private $_marker = null;
    private $_pointCount = 0;
    private $_dataValues = array();
    public function __construct($dataType = self::DATASERIES_TYPE_NUMBER, $dataSource = null, $formatCode = null, $pointCount = 0, $dataValues = array(), $marker = null)
    {
        $this->setDataType($dataType);
        $this->_dataSource = $dataSource;
        $this->_formatCode = $formatCode;
        $this->_pointCount = $pointCount;
        $this->_dataValues = $dataValues;
        $this->_marker     = $marker;
    }
    public function getDataType()
    {
        return $this->_dataType;
    }
    public function setDataType($dataType = self::DATASERIES_TYPE_NUMBER)
    {
        if (!in_array($dataType, self::$_dataTypeValues)) {
            throw new PHPExcel_Chart_Exception('Invalid datatype for chart data series values');
        }
        $this->_dataType = $dataType;
        return $this;
    }
    public function getDataSource()
    {
        return $this->_dataSource;
    }
    public function setDataSource($dataSource = null, $refreshDataValues = true)
    {
        $this->_dataSource = $dataSource;
        if ($refreshDataValues) {
        }
        return $this;
    }
    public function getPointMarker()
    {
        return $this->_marker;
    }
    public function setPointMarker($marker = null)
    {
        $this->_marker = $marker;
        return $this;
    }
    public function getFormatCode()
    {
        return $this->_formatCode;
    }
    public function setFormatCode($formatCode = null)
    {
        $this->_formatCode = $formatCode;
        return $this;
    }
    public function getPointCount()
    {
        return $this->_pointCount;
    }
    public function isMultiLevelSeries()
    {
        if (count($this->_dataValues) > 0) {
            return is_array($this->_dataValues[0]);
        }
        return null;
    }
    public function multiLevelCount()
    {
        $levelCount = 0;
        foreach ($this->_dataValues as $dataValueSet) {
            $levelCount = max($levelCount, count($dataValueSet));
        }
        return $levelCount;
    }
    public function getDataValues()
    {
        return $this->_dataValues;
    }
    public function getDataValue()
    {
        $count = count($this->_dataValues);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
            return $this->_dataValues[0];
        }
        return $this->_dataValues;
    }
    public function setDataValues($dataValues = array(), $refreshDataSource = TRUE)
    {
        $this->_dataValues = PHPExcel_Calculation_Functions::flattenArray($dataValues);
        $this->_pointCount = count($dataValues);
        if ($refreshDataSource) {
        }
        return $this;
    }
    private function _stripNulls($var)
    {
        return $var !== NULL;
    }
    public function refresh(PHPExcel_Worksheet $worksheet, $flatten = TRUE)
    {
        if ($this->_dataSource !== NULL) {
            $calcEngine    = PHPExcel_Calculation::getInstance();
            $newDataValues = PHPExcel_Calculation::_unwrapResult($calcEngine->_calculateFormulaValue('=' . $this->_dataSource, NULL, $worksheet->getCell('A1')));
            if ($flatten) {
                $this->_dataValues = PHPExcel_Calculation_Functions::flattenArray($newDataValues);
                foreach ($this->_dataValues as &$dataValue) {
                    if ((!empty($dataValue)) && ($dataValue[0] == '#')) {
                        $dataValue = 0.0;
                    }
                }
                unset($dataValue);
            } else {
                $cellRange = explode('!', $this->_dataSource);
                if (count($cellRange) > 1) {
                    list(, $cellRange) = $cellRange;
                }
                $dimensions = PHPExcel_Cell::rangeDimension(str_replace('$', '', $cellRange));
                if (($dimensions[0] == 1) || ($dimensions[1] == 1)) {
                    $this->_dataValues = PHPExcel_Calculation_Functions::flattenArray($newDataValues);
                } else {
                    $newArray = array_values(array_shift($newDataValues));
                    foreach ($newArray as $i => $newDataSet) {
                        $newArray[$i] = array(
                            $newDataSet
                        );
                    }
                    foreach ($newDataValues as $newDataSet) {
                        $i = 0;
                        foreach ($newDataSet as $newDataVal) {
                            array_unshift($newArray[$i++], $newDataVal);
                        }
                    }
                    $this->_dataValues = $newArray;
                }
            }
            $this->_pointCount = count($this->_dataValues);
        }
    }
}