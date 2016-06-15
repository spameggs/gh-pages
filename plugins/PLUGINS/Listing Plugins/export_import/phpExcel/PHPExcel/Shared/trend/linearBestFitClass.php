<?php
require_once(PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/bestFitClass.php');
class PHPExcel_Linear_Best_Fit extends PHPExcel_Best_Fit
{
    protected $_bestFitType = 'linear';
    public function getValueOfYForX($xValue)
    {
        return $this->getIntersect() + $this->getSlope() * $xValue;
    }
    public function getValueOfXForY($yValue)
    {
        return ($yValue - $this->getIntersect()) / $this->getSlope();
    }
    public function getEquation($dp = 0)
    {
        $slope     = $this->getSlope($dp);
        $intersect = $this->getIntersect($dp);
        return 'Y = ' . $intersect . ' + ' . $slope . ' * X';
    }
    private function _linear_regression($yValues, $xValues, $const)
    {
        $this->_leastSquareFit($yValues, $xValues, $const);
    }
    function __construct($yValues, $xValues = array(), $const = True)
    {
        if (parent::__construct($yValues, $xValues) !== False) {
            $this->_linear_regression($yValues, $xValues, $const);
        }
    }
}