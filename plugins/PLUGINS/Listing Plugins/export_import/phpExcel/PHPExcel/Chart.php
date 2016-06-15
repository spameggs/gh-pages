<?php
class PHPExcel_Chart
{
    private $_name = '';
    private $_worksheet = null;
    private $_title = null;
    private $_legend = null;
    private $_xAxisLabel = null;
    private $_yAxisLabel = null;
    private $_plotArea = null;
    private $_plotVisibleOnly = true;
    private $_displayBlanksAs = '0';
    private $_topLeftCellRef = 'A1';
    private $_topLeftXOffset = 0;
    private $_topLeftYOffset = 0;
    private $_bottomRightCellRef = 'A1';
    private $_bottomRightXOffset = 10;
    private $_bottomRightYOffset = 10;
    public function __construct($name, PHPExcel_Chart_Title $title = null, PHPExcel_Chart_Legend $legend = null, PHPExcel_Chart_PlotArea $plotArea = null, $plotVisibleOnly = true, $displayBlanksAs = '0', PHPExcel_Chart_Title $xAxisLabel = null, PHPExcel_Chart_Title $yAxisLabel = null)
    {
        $this->_name            = $name;
        $this->_title           = $title;
        $this->_legend          = $legend;
        $this->_xAxisLabel      = $xAxisLabel;
        $this->_yAxisLabel      = $yAxisLabel;
        $this->_plotArea        = $plotArea;
        $this->_plotVisibleOnly = $plotVisibleOnly;
        $this->_displayBlanksAs = $displayBlanksAs;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function getWorksheet()
    {
        return $this->_worksheet;
    }
    public function setWorksheet(PHPExcel_Worksheet $pValue = null)
    {
        $this->_worksheet = $pValue;
        return $this;
    }
    public function getTitle()
    {
        return $this->_title;
    }
    public function setTitle(PHPExcel_Chart_Title $title)
    {
        $this->_title = $title;
        return $this;
    }
    public function getLegend()
    {
        return $this->_legend;
    }
    public function setLegend(PHPExcel_Chart_Legend $legend)
    {
        $this->_legend = $legend;
        return $this;
    }
    public function getXAxisLabel()
    {
        return $this->_xAxisLabel;
    }
    public function setXAxisLabel(PHPExcel_Chart_Title $label)
    {
        $this->_xAxisLabel = $label;
        return $this;
    }
    public function getYAxisLabel()
    {
        return $this->_yAxisLabel;
    }
    public function setYAxisLabel(PHPExcel_Chart_Title $label)
    {
        $this->_yAxisLabel = $label;
        return $this;
    }
    public function getPlotArea()
    {
        return $this->_plotArea;
    }
    public function getPlotVisibleOnly()
    {
        return $this->_plotVisibleOnly;
    }
    public function setPlotVisibleOnly($plotVisibleOnly = true)
    {
        $this->_plotVisibleOnly = $plotVisibleOnly;
        return $this;
    }
    public function getDisplayBlanksAs()
    {
        return $this->_displayBlanksAs;
    }
    public function setDisplayBlanksAs($displayBlanksAs = '0')
    {
        $this->_displayBlanksAs = $displayBlanksAs;
    }
    public function setTopLeftPosition($cell, $xOffset = null, $yOffset = null)
    {
        $this->_topLeftCellRef = $cell;
        if (!is_null($xOffset))
            $this->setTopLeftXOffset($xOffset);
        if (!is_null($yOffset))
            $this->setTopLeftYOffset($yOffset);
        return $this;
    }
    public function getTopLeftPosition()
    {
        return array(
            'cell' => $this->_topLeftCellRef,
            'xOffset' => $this->_topLeftXOffset,
            'yOffset' => $this->_topLeftYOffset
        );
    }
    public function getTopLeftCell()
    {
        return $this->_topLeftCellRef;
    }
    public function setTopLeftCell($cell)
    {
        $this->_topLeftCellRef = $cell;
        return $this;
    }
    public function setTopLeftOffset($xOffset = null, $yOffset = null)
    {
        if (!is_null($xOffset))
            $this->setTopLeftXOffset($xOffset);
        if (!is_null($yOffset))
            $this->setTopLeftYOffset($yOffset);
        return $this;
    }
    public function getTopLeftOffset()
    {
        return array(
            'X' => $this->_topLeftXOffset,
            'Y' => $this->_topLeftYOffset
        );
    }
    public function setTopLeftXOffset($xOffset)
    {
        $this->_topLeftXOffset = $xOffset;
        return $this;
    }
    public function getTopLeftXOffset()
    {
        return $this->_topLeftXOffset;
    }
    public function setTopLeftYOffset($yOffset)
    {
        $this->_topLeftYOffset = $yOffset;
        return $this;
    }
    public function getTopLeftYOffset()
    {
        return $this->_topLeftYOffset;
    }
    public function setBottomRightPosition($cell, $xOffset = null, $yOffset = null)
    {
        $this->_bottomRightCellRef = $cell;
        if (!is_null($xOffset))
            $this->setBottomRightXOffset($xOffset);
        if (!is_null($yOffset))
            $this->setBottomRightYOffset($yOffset);
        return $this;
    }
    public function getBottomRightPosition()
    {
        return array(
            'cell' => $this->_bottomRightCellRef,
            'xOffset' => $this->_bottomRightXOffset,
            'yOffset' => $this->_bottomRightYOffset
        );
    }
    public function setBottomRightCell($cell)
    {
        $this->_bottomRightCellRef = $cell;
        return $this;
    }
    public function getBottomRightCell()
    {
        return $this->_bottomRightCellRef;
    }
    public function setBottomRightOffset($xOffset = null, $yOffset = null)
    {
        if (!is_null($xOffset))
            $this->setBottomRightXOffset($xOffset);
        if (!is_null($yOffset))
            $this->setBottomRightYOffset($yOffset);
        return $this;
    }
    public function getBottomRightOffset()
    {
        return array(
            'X' => $this->_bottomRightXOffset,
            'Y' => $this->_bottomRightYOffset
        );
    }
    public function setBottomRightXOffset($xOffset)
    {
        $this->_bottomRightXOffset = $xOffset;
        return $this;
    }
    public function getBottomRightXOffset()
    {
        return $this->_bottomRightXOffset;
    }
    public function setBottomRightYOffset($yOffset)
    {
        $this->_bottomRightYOffset = $yOffset;
        return $this;
    }
    public function getBottomRightYOffset()
    {
        return $this->_bottomRightYOffset;
    }
    public function refresh()
    {
        if ($this->_worksheet !== NULL) {
            $this->_plotArea->refresh($this->_worksheet);
        }
    }
    public function render($outputDestination = null)
    {
        $libraryName = PHPExcel_Settings::getChartRendererName();
        if (is_null($libraryName)) {
            return false;
        }
        $this->refresh();
        $libraryPath  = PHPExcel_Settings::getChartRendererPath();
        $includePath  = str_replace('\\', '/', get_include_path());
        $rendererPath = str_replace('\\', '/', $libraryPath);
        if (strpos($rendererPath, $includePath) === false) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $libraryPath);
        }
        $rendererName = 'PHPExcel_Chart_Renderer_' . $libraryName;
        $renderer     = new $rendererName($this);
        if ($outputDestination == 'php://output') {
            $outputDestination = null;
        }
        return $renderer->render($outputDestination);
    }
}