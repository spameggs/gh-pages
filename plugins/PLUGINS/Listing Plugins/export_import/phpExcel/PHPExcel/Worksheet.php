<?php
class PHPExcel_Worksheet implements PHPExcel_IComparable
{
    const BREAK_NONE = 0;
    const BREAK_ROW = 1;
    const BREAK_COLUMN = 2;
    const SHEETSTATE_VISIBLE = 'visible';
    const SHEETSTATE_HIDDEN = 'hidden';
    const SHEETSTATE_VERYHIDDEN = 'veryHidden';
    private static $_invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');
    private $_parent;
    private $_cellCollection = null;
    private $_rowDimensions = array();
    private $_defaultRowDimension = null;
    private $_columnDimensions = array();
    private $_defaultColumnDimension = null;
    private $_drawingCollection = null;
    private $_chartCollection = array();
    private $_title;
    private $_sheetState;
    private $_pageSetup;
    private $_pageMargins;
    private $_headerFooter;
    private $_sheetView;
    private $_protection;
    private $_styles = array();
    private $_conditionalStylesCollection = array();
    private $_cellCollectionIsSorted = false;
    private $_breaks = array();
    private $_mergeCells = array();
    private $_protectedCells = array();
    private $_autoFilter = null;
    private $_freezePane = '';
    private $_showGridlines = true;
    private $_printGridlines = false;
    private $_showRowColHeaders = true;
    private $_showSummaryBelow = true;
    private $_showSummaryRight = true;
    private $_comments = array();
    private $_activeCell = 'A1';
    private $_selectedCells = 'A1';
    private $_cachedHighestColumn = 'A';
    private $_cachedHighestRow = 1;
    private $_rightToLeft = false;
    private $_hyperlinkCollection = array();
    private $_dataValidationCollection = array();
    private $_tabColor;
    private $_dirty = true;
    private $_hash = null;
    public function __construct(PHPExcel $pParent = null, $pTitle = 'Worksheet')
    {
        $this->_parent = $pParent;
        $this->setTitle($pTitle, FALSE);
        $this->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VISIBLE);
        $this->_cellCollection         = PHPExcel_CachedObjectStorageFactory::getInstance($this);
        $this->_pageSetup              = new PHPExcel_Worksheet_PageSetup();
        $this->_pageMargins            = new PHPExcel_Worksheet_PageMargins();
        $this->_headerFooter           = new PHPExcel_Worksheet_HeaderFooter();
        $this->_sheetView              = new PHPExcel_Worksheet_SheetView();
        $this->_drawingCollection      = new ArrayObject();
        $this->_chartCollection        = new ArrayObject();
        $this->_protection             = new PHPExcel_Worksheet_Protection();
        $this->_defaultRowDimension    = new PHPExcel_Worksheet_RowDimension(null);
        $this->_defaultColumnDimension = new PHPExcel_Worksheet_ColumnDimension(null);
        $this->_autoFilter             = new PHPExcel_Worksheet_AutoFilter(null, $this);
    }
    public function disconnectCells()
    {
        if ($this->_cellCollection != null) {
            $this->_cellCollection->unsetWorksheetCells();
            $this->_cellCollection = null;
        }
        $this->_parent = null;
    }
    function __destruct()
    {
        if ($this->_cellCollection !== null) {
            $this->disconnectCells();
        }
    }
    public function getCellCacheController()
    {
        return $this->_cellCollection;
    }
    public static function getInvalidCharacters()
    {
        return self::$_invalidCharacters;
    }
    private static function _checkSheetTitle($pValue)
    {
        if (str_replace(self::$_invalidCharacters, '', $pValue) !== $pValue) {
            throw new PHPExcel_Exception('Invalid character found in sheet title');
        }
        if (PHPExcel_Shared_String::CountCharacters($pValue) > 31) {
            throw new PHPExcel_Exception('Maximum 31 characters allowed in sheet title.');
        }
        return $pValue;
    }
    public function getCellCollection($pSorted = true)
    {
        if ($pSorted) {
            return $this->sortCellCollection();
        }
        if ($this->_cellCollection !== null) {
            return $this->_cellCollection->getCellList();
        }
        return array();
    }
    public function sortCellCollection()
    {
        if ($this->_cellCollection !== null) {
            return $this->_cellCollection->getSortedCellList();
        }
        return array();
    }
    public function getRowDimensions()
    {
        return $this->_rowDimensions;
    }
    public function getDefaultRowDimension()
    {
        return $this->_defaultRowDimension;
    }
    public function getColumnDimensions()
    {
        return $this->_columnDimensions;
    }
    public function getDefaultColumnDimension()
    {
        return $this->_defaultColumnDimension;
    }
    public function getDrawingCollection()
    {
        return $this->_drawingCollection;
    }
    public function getChartCollection()
    {
        return $this->_chartCollection;
    }
    public function addChart(PHPExcel_Chart $pChart = null, $iChartIndex = null)
    {
        $pChart->setWorksheet($this);
        if (is_null($iChartIndex)) {
            $this->_chartCollection[] = $pChart;
        } else {
            array_splice($this->_chartCollection, $iChartIndex, 0, array(
                $pChart
            ));
        }
        return $pChart;
    }
    public function getChartCount()
    {
        return count($this->_chartCollection);
    }
    public function getChartByIndex($index = null)
    {
        $chartCount = count($this->_chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        if (is_null($index)) {
            $index = --$chartCount;
        }
        if (!isset($this->_chartCollection[$index])) {
            return false;
        }
        return $this->_chartCollection[$index];
    }
    public function getChartNames()
    {
        $chartNames = array();
        foreach ($this->_chartCollection as $chart) {
            $chartNames[] = $chart->getName();
        }
        return $chartNames;
    }
    public function getChartByName($chartName = '')
    {
        $chartCount = count($this->_chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        foreach ($this->_chartCollection as $index => $chart) {
            if ($chart->getName() == $chartName) {
                return $this->_chartCollection[$index];
            }
        }
        return false;
    }
    public function refreshColumnDimensions()
    {
        $currentColumnDimensions = $this->getColumnDimensions();
        $newColumnDimensions     = array();
        foreach ($currentColumnDimensions as $objColumnDimension) {
            $newColumnDimensions[$objColumnDimension->getColumnIndex()] = $objColumnDimension;
        }
        $this->_columnDimensions = $newColumnDimensions;
        return $this;
    }
    public function refreshRowDimensions()
    {
        $currentRowDimensions = $this->getRowDimensions();
        $newRowDimensions     = array();
        foreach ($currentRowDimensions as $objRowDimension) {
            $newRowDimensions[$objRowDimension->getRowIndex()] = $objRowDimension;
        }
        $this->_rowDimensions = $newRowDimensions;
        return $this;
    }
    public function calculateWorksheetDimension()
    {
        return 'A1' . ':' . $this->getHighestColumn() . $this->getHighestRow();
    }
    public function calculateWorksheetDataDimension()
    {
        return 'A1' . ':' . $this->getHighestDataColumn() . $this->getHighestDataRow();
    }
    public function calculateColumnWidths($calculateMergeCells = false)
    {
        $autoSizes = array();
        foreach ($this->getColumnDimensions() as $colDimension) {
            if ($colDimension->getAutoSize()) {
                $autoSizes[$colDimension->getColumnIndex()] = -1;
            }
        }
        if (!empty($autoSizes)) {
            $isMergeCell = array();
            foreach ($this->getMergeCells() as $cells) {
                foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }
            foreach ($this->getCellCollection(false) as $cellID) {
                $cell = $this->getCell($cellID);
                if (isset($autoSizes[$cell->getColumn()])) {
                    if (!isset($isMergeCell[$cell->getCoordinate()])) {
                        $cellValue                     = $cell->getCalculatedValue();
                        $cellValue                     = PHPExcel_Style_NumberFormat::toFormattedString($cellValue, $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode());
                        $autoSizes[$cell->getColumn()] = max((float) $autoSizes[$cell->getColumn()], (float) PHPExcel_Shared_Font::calculateColumnWidth($this->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont(), $cellValue, $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getAlignment()->getTextRotation(), $this->getDefaultStyle()->getFont()));
                    }
                }
            }
            foreach ($autoSizes as $columnIndex => $width) {
                if ($width == -1)
                    $width = $this->getDefaultColumnDimension()->getWidth();
                $this->getColumnDimension($columnIndex)->setWidth($width);
            }
        }
        return $this;
    }
    public function getParent()
    {
        return $this->_parent;
    }
    public function rebindParent(PHPExcel $parent)
    {
        $namedRanges = $this->_parent->getNamedRanges();
        foreach ($namedRanges as $namedRange) {
            $parent->addNamedRange($namedRange);
        }
        $this->_parent->removeSheetByIndex($this->_parent->getIndex($this));
        $this->_parent = $parent;
        return $this;
    }
    public function getTitle()
    {
        return $this->_title;
    }
    public function setTitle($pValue = 'Worksheet', $updateFormulaCellReferences = true)
    {
        if ($this->getTitle() == $pValue) {
            return $this;
        }
        self::_checkSheetTitle($pValue);
        $oldTitle = $this->getTitle();
        if ($this->getParent()) {
            if ($this->getParent()->sheetNameExists($pValue)) {
                if (PHPExcel_Shared_String::CountCharacters($pValue) > 29) {
                    $pValue = PHPExcel_Shared_String::Substring($pValue, 0, 29);
                }
                $i = 1;
                while ($this->getParent()->sheetNameExists($pValue . ' ' . $i)) {
                    ++$i;
                    if ($i == 10) {
                        if (PHPExcel_Shared_String::CountCharacters($pValue) > 28) {
                            $pValue = PHPExcel_Shared_String::Substring($pValue, 0, 28);
                        }
                    } elseif ($i == 100) {
                        if (PHPExcel_Shared_String::CountCharacters($pValue) > 27) {
                            $pValue = PHPExcel_Shared_String::Substring($pValue, 0, 27);
                        }
                    }
                }
                $altTitle = $pValue . ' ' . $i;
                return $this->setTitle($altTitle, $updateFormulaCellReferences);
            }
        }
        $this->_title = $pValue;
        $this->_dirty = true;
        if ($this->getParent()) {
            $newTitle = $this->getTitle();
            if ($updateFormulaCellReferences)
                PHPExcel_ReferenceHelper::getInstance()->updateNamedFormulas($this->getParent(), $oldTitle, $newTitle);
        }
        return $this;
    }
    public function getSheetState()
    {
        return $this->_sheetState;
    }
    public function setSheetState($value = PHPExcel_Worksheet::SHEETSTATE_VISIBLE)
    {
        $this->_sheetState = $value;
        return $this;
    }
    public function getPageSetup()
    {
        return $this->_pageSetup;
    }
    public function setPageSetup(PHPExcel_Worksheet_PageSetup $pValue)
    {
        $this->_pageSetup = $pValue;
        return $this;
    }
    public function getPageMargins()
    {
        return $this->_pageMargins;
    }
    public function setPageMargins(PHPExcel_Worksheet_PageMargins $pValue)
    {
        $this->_pageMargins = $pValue;
        return $this;
    }
    public function getHeaderFooter()
    {
        return $this->_headerFooter;
    }
    public function setHeaderFooter(PHPExcel_Worksheet_HeaderFooter $pValue)
    {
        $this->_headerFooter = $pValue;
        return $this;
    }
    public function getSheetView()
    {
        return $this->_sheetView;
    }
    public function setSheetView(PHPExcel_Worksheet_SheetView $pValue)
    {
        $this->_sheetView = $pValue;
        return $this;
    }
    public function getProtection()
    {
        return $this->_protection;
    }
    public function setProtection(PHPExcel_Worksheet_Protection $pValue)
    {
        $this->_protection = $pValue;
        $this->_dirty      = true;
        return $this;
    }
    public function getHighestColumn()
    {
        return $this->_cachedHighestColumn;
    }
    public function getHighestDataColumn()
    {
        return $this->_cellCollection->getHighestColumn();
    }
    public function getHighestRow()
    {
        return $this->_cachedHighestRow;
    }
    public function getHighestDataRow()
    {
        return $this->_cellCollection->getHighestRow();
    }
    public function getHighestRowAndColumn()
    {
        return $this->_cellCollection->getHighestRowAndColumn();
    }
    public function setCellValue($pCoordinate = 'A1', $pValue = null, $returnCell = false)
    {
        $cell = $this->getCell($pCoordinate)->setValue($pValue);
        return ($returnCell) ? $cell : $this;
    }
    public function setCellValueByColumnAndRow($pColumn = 0, $pRow = 1, $pValue = null, $returnCell = false)
    {
        $cell = $this->getCell(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow)->setValue($pValue);
        return ($returnCell) ? $cell : $this;
    }
    public function setCellValueExplicit($pCoordinate = 'A1', $pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING, $returnCell = false)
    {
        $cell = $this->getCell($pCoordinate)->setValueExplicit($pValue, $pDataType);
        return ($returnCell) ? $cell : $this;
    }
    public function setCellValueExplicitByColumnAndRow($pColumn = 0, $pRow = 1, $pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING, $returnCell = false)
    {
        $cell = $this->getCell(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow)->setValueExplicit($pValue, $pDataType);
        return ($returnCell) ? $cell : $this;
    }
    public function getCell($pCoordinate = 'A1')
    {
        if ($this->_cellCollection->isDataSet($pCoordinate)) {
            return $this->_cellCollection->getCacheData($pCoordinate);
        }
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = PHPExcel_Worksheet::extractSheetTitle($pCoordinate, true);
            return $this->getParent()->getSheetByName($worksheetReference[0])->getCell($worksheetReference[1]);
        }
        if ((!preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $pCoordinate, $matches)) && (preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE . '$/i', $pCoordinate, $matches))) {
            $namedRange = PHPExcel_NamedRange::resolveRange($pCoordinate, $this);
            if ($namedRange !== null) {
                $pCoordinate = $namedRange->getRange();
                return $namedRange->getWorksheet()->getCell($pCoordinate);
            }
        }
        $pCoordinate = strtoupper($pCoordinate);
        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            throw new PHPExcel_Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate must not be absolute.');
        } else {
            $aCoordinates                  = PHPExcel_Cell::coordinateFromString($pCoordinate);
            $cell                          = $this->_cellCollection->addCacheData($pCoordinate, new PHPExcel_Cell($pCoordinate, null, PHPExcel_Cell_DataType::TYPE_NULL, $this));
            $this->_cellCollectionIsSorted = false;
            if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < PHPExcel_Cell::columnIndexFromString($aCoordinates[0]))
                $this->_cachedHighestColumn = $aCoordinates[0];
            $this->_cachedHighestRow = max($this->_cachedHighestRow, $aCoordinates[1]);
            $rowDimensions           = $this->getRowDimensions();
            $columnDimensions        = $this->getColumnDimensions();
            if (isset($rowDimensions[$aCoordinates[1]]) && $rowDimensions[$aCoordinates[1]]->getXfIndex() !== null) {
                $cell->setXfIndex($rowDimensions[$aCoordinates[1]]->getXfIndex());
            } else if (isset($columnDimensions[$aCoordinates[0]])) {
                $cell->setXfIndex($columnDimensions[$aCoordinates[0]]->getXfIndex());
            } else {
                $cell->setXfIndex(0);
            }
            return $cell;
        }
    }
    public function getCellByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($pColumn);
        $coordinate   = $columnLetter . $pRow;
        if (!$this->_cellCollection->isDataSet($coordinate)) {
            $cell                          = $this->_cellCollection->addCacheData($coordinate, new PHPExcel_Cell($coordinate, null, PHPExcel_Cell_DataType::TYPE_NULL, $this));
            $this->_cellCollectionIsSorted = false;
            if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < $pColumn)
                $this->_cachedHighestColumn = $columnLetter;
            $this->_cachedHighestRow = max($this->_cachedHighestRow, $pRow);
            return $cell;
        }
        return $this->_cellCollection->getCacheData($coordinate);
    }
    public function cellExists($pCoordinate = 'A1')
    {
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = PHPExcel_Worksheet::extractSheetTitle($pCoordinate, true);
            return $this->getParent()->getSheetByName($worksheetReference[0])->cellExists($worksheetReference[1]);
        }
        if ((!preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $pCoordinate, $matches)) && (preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE . '$/i', $pCoordinate, $matches))) {
            $namedRange = PHPExcel_NamedRange::resolveRange($pCoordinate, $this);
            if ($namedRange !== null) {
                $pCoordinate = $namedRange->getRange();
                if ($this->getHashCode() != $namedRange->getWorksheet()->getHashCode()) {
                    if (!$namedRange->getLocalOnly()) {
                        return $namedRange->getWorksheet()->cellExists($pCoordinate);
                    } else {
                        throw new PHPExcel_Exception('Named range ' . $namedRange->getName() . ' is not accessible from within sheet ' . $this->getTitle());
                    }
                }
            } else {
                return false;
            }
        }
        $pCoordinate = strtoupper($pCoordinate);
        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            throw new PHPExcel_Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate must not be absolute.');
        } else {
            $aCoordinates = PHPExcel_Cell::coordinateFromString($pCoordinate);
            return $this->_cellCollection->isDataSet($pCoordinate);
        }
    }
    public function cellExistsByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->cellExists(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    public function getRowDimension($pRow = 1)
    {
        $found = null;
        if (!isset($this->_rowDimensions[$pRow])) {
            $this->_rowDimensions[$pRow] = new PHPExcel_Worksheet_RowDimension($pRow);
            $this->_cachedHighestRow     = max($this->_cachedHighestRow, $pRow);
        }
        return $this->_rowDimensions[$pRow];
    }
    public function getColumnDimension($pColumn = 'A')
    {
        $pColumn = strtoupper($pColumn);
        if (!isset($this->_columnDimensions[$pColumn])) {
            $this->_columnDimensions[$pColumn] = new PHPExcel_Worksheet_ColumnDimension($pColumn);
            if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < PHPExcel_Cell::columnIndexFromString($pColumn))
                $this->_cachedHighestColumn = $pColumn;
        }
        return $this->_columnDimensions[$pColumn];
    }
    public function getColumnDimensionByColumn($pColumn = 0)
    {
        return $this->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($pColumn));
    }
    public function getStyles()
    {
        return $this->_styles;
    }
    public function getDefaultStyle()
    {
        return $this->_parent->getDefaultStyle();
    }
    public function setDefaultStyle(PHPExcel_Style $pValue)
    {
        $this->_parent->getDefaultStyle()->applyFromArray(array(
            'font' => array(
                'name' => $pValue->getFont()->getName(),
                'size' => $pValue->getFont()->getSize()
            )
        ));
        return $this;
    }
    public function getStyle($pCellCoordinate = 'A1')
    {
        $this->_parent->setActiveSheetIndex($this->_parent->getIndex($this));
        $this->setSelectedCells($pCellCoordinate);
        return $this->_parent->getCellXfSupervisor();
    }
    public function getConditionalStyles($pCoordinate = 'A1')
    {
        if (!isset($this->_conditionalStylesCollection[$pCoordinate])) {
            $this->_conditionalStylesCollection[$pCoordinate] = array();
        }
        return $this->_conditionalStylesCollection[$pCoordinate];
    }
    public function conditionalStylesExists($pCoordinate = 'A1')
    {
        if (isset($this->_conditionalStylesCollection[$pCoordinate])) {
            return true;
        }
        return false;
    }
    public function removeConditionalStyles($pCoordinate = 'A1')
    {
        unset($this->_conditionalStylesCollection[$pCoordinate]);
        return $this;
    }
    public function getConditionalStylesCollection()
    {
        return $this->_conditionalStylesCollection;
    }
    public function setConditionalStyles($pCoordinate = 'A1', $pValue)
    {
        $this->_conditionalStylesCollection[$pCoordinate] = $pValue;
        return $this;
    }
    public function getStyleByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->getStyle(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    public function setSharedStyle(PHPExcel_Style $pSharedCellStyle = null, $pRange = '')
    {
        $this->duplicateStyle($pSharedCellStyle, $pRange);
        return $this;
    }
    public function duplicateStyle(PHPExcel_Style $pCellStyle = null, $pRange = '')
    {
        $style    = $pCellStyle->getIsSupervisor() ? $pCellStyle->getSharedComponent() : $pCellStyle;
        $workbook = $this->_parent;
        if ($this->_parent->cellXfExists($pCellStyle)) {
            $xfIndex = $pCellStyle->getIndex();
        } else {
            $workbook->addCellXf($pCellStyle);
            $xfIndex = $pCellStyle->getIndex();
        }
        $pRange = strtoupper($pRange);
        $rangeA = '';
        $rangeB = '';
        if (strpos($pRange, ':') === false) {
            $rangeA = $pRange;
            $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }
        $rangeStart    = PHPExcel_Cell::coordinateFromString($rangeA);
        $rangeEnd      = PHPExcel_Cell::coordinateFromString($rangeB);
        $rangeStart[0] = PHPExcel_Cell::columnIndexFromString($rangeStart[0]) - 1;
        $rangeEnd[0]   = PHPExcel_Cell::columnIndexFromString($rangeEnd[0]) - 1;
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp        = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd   = $tmp;
        }
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->setXfIndex($xfIndex);
            }
        }
        return $this;
    }
    public function duplicateConditionalStyle(array $pCellStyle = null, $pRange = '')
    {
        foreach ($pCellStyle as $cellStyle) {
            if (!($cellStyle instanceof PHPExcel_Style_Conditional)) {
                throw new PHPExcel_Exception('Style is not a conditional style');
            }
        }
        $pRange = strtoupper($pRange);
        $rangeA = '';
        $rangeB = '';
        if (strpos($pRange, ':') === false) {
            $rangeA = $pRange;
            $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }
        $rangeStart    = PHPExcel_Cell::coordinateFromString($rangeA);
        $rangeEnd      = PHPExcel_Cell::coordinateFromString($rangeB);
        $rangeStart[0] = PHPExcel_Cell::columnIndexFromString($rangeStart[0]) - 1;
        $rangeEnd[0]   = PHPExcel_Cell::columnIndexFromString($rangeEnd[0]) - 1;
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp        = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd   = $tmp;
        }
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->setConditionalStyles(PHPExcel_Cell::stringFromColumnIndex($col) . $row, $pCellStyle);
            }
        }
        return $this;
    }
    public function duplicateStyleArray($pStyles = null, $pRange = '', $pAdvanced = true)
    {
        $this->getStyle($pRange)->applyFromArray($pStyles, $pAdvanced);
        return $this;
    }
    public function setBreak($pCell = 'A1', $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
        $pCell = strtoupper($pCell);
        if ($pCell != '') {
            $this->_breaks[$pCell] = $pBreak;
        } else {
            throw new PHPExcel_Exception('No cell coordinate specified.');
        }
        return $this;
    }
    public function setBreakByColumnAndRow($pColumn = 0, $pRow = 1, $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
        return $this->setBreak(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pBreak);
    }
    public function getBreaks()
    {
        return $this->_breaks;
    }
    public function mergeCells($pRange = 'A1:A1')
    {
        $pRange = strtoupper($pRange);
        if (strpos($pRange, ':') !== false) {
            $this->_mergeCells[$pRange] = $pRange;
            $aReferences                = PHPExcel_Cell::extractAllCellReferencesInRange($pRange);
            $upperLeft                  = $aReferences[0];
            if (!$this->cellExists($upperLeft)) {
                $this->getCell($upperLeft)->setValueExplicit(null, PHPExcel_Cell_DataType::TYPE_NULL);
            }
            $count = count($aReferences);
            for ($i = 1; $i < $count; $i++) {
                $this->getCell($aReferences[$i])->setValueExplicit(null, PHPExcel_Cell_DataType::TYPE_NULL);
            }
        } else {
            throw new PHPExcel_Exception('Merge must be set on a range of cells.');
        }
        return $this;
    }
    public function mergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->mergeCells($cellRange);
    }
    public function unmergeCells($pRange = 'A1:A1')
    {
        $pRange = strtoupper($pRange);
        if (strpos($pRange, ':') !== false) {
            if (isset($this->_mergeCells[$pRange])) {
                unset($this->_mergeCells[$pRange]);
            } else {
                throw new PHPExcel_Exception('Cell range ' . $pRange . ' not known as merged.');
            }
        } else {
            throw new PHPExcel_Exception('Merge can only be removed from a range of cells.');
        }
        return $this;
    }
    public function unmergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->unmergeCells($cellRange);
    }
    public function getMergeCells()
    {
        return $this->_mergeCells;
    }
    public function setMergeCells($pValue = array())
    {
        $this->_mergeCells = $pValue;
        return $this;
    }
    public function protectCells($pRange = 'A1', $pPassword = '', $pAlreadyHashed = false)
    {
        $pRange = strtoupper($pRange);
        if (!$pAlreadyHashed) {
            $pPassword = PHPExcel_Shared_PasswordHasher::hashPassword($pPassword);
        }
        $this->_protectedCells[$pRange] = $pPassword;
        return $this;
    }
    public function protectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1, $pPassword = '', $pAlreadyHashed = false)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->protectCells($cellRange, $pPassword, $pAlreadyHashed);
    }
    public function unprotectCells($pRange = 'A1')
    {
        $pRange = strtoupper($pRange);
        if (isset($this->_protectedCells[$pRange])) {
            unset($this->_protectedCells[$pRange]);
        } else {
            throw new PHPExcel_Exception('Cell range ' . $pRange . ' not known as protected.');
        }
        return $this;
    }
    public function unprotectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1, $pPassword = '', $pAlreadyHashed = false)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->unprotectCells($cellRange, $pPassword, $pAlreadyHashed);
    }
    public function getProtectedCells()
    {
        return $this->_protectedCells;
    }
    public function getAutoFilter()
    {
        return $this->_autoFilter;
    }
    public function setAutoFilter($pValue)
    {
        if (is_string($pValue)) {
            $this->_autoFilter->setRange($pValue);
        } elseif (is_object($pValue) && ($pValue instanceof PHPExcel_Worksheet_AutoFilter)) {
            $this->_autoFilter = $pValue;
        }
        return $this;
    }
    public function setAutoFilterByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        return $this->setAutoFilter(PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2);
    }
    public function removeAutoFilter()
    {
        $this->_autoFilter->setRange(null);
        return $this;
    }
    public function getFreezePane()
    {
        return $this->_freezePane;
    }
    public function freezePane($pCell = '')
    {
        $pCell = strtoupper($pCell);
        if (strpos($pCell, ':') === false && strpos($pCell, ',') === false) {
            $this->_freezePane = $pCell;
        } else {
            throw new PHPExcel_Exception('Freeze pane can not be set on a range of cells.');
        }
        return $this;
    }
    public function freezePaneByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->freezePane(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    public function unfreezePane()
    {
        return $this->freezePane('');
    }
    public function insertNewRowBefore($pBefore = 1, $pNumRows = 1)
    {
        if ($pBefore >= 1) {
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . $pBefore, 0, $pNumRows, $this);
        } else {
            throw new PHPExcel_Exception("Rows can only be inserted before at least row 1.");
        }
        return $this;
    }
    public function insertNewColumnBefore($pBefore = 'A', $pNumCols = 1)
    {
        if (!is_numeric($pBefore)) {
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pBefore . '1', $pNumCols, 0, $this);
        } else {
            throw new PHPExcel_Exception("Column references should not be numeric.");
        }
        return $this;
    }
    public function insertNewColumnBeforeByIndex($pBefore = 0, $pNumCols = 1)
    {
        if ($pBefore >= 0) {
            return $this->insertNewColumnBefore(PHPExcel_Cell::stringFromColumnIndex($pBefore), $pNumCols);
        } else {
            throw new PHPExcel_Exception("Columns can only be inserted before at least column A (0).");
        }
    }
    public function removeRow($pRow = 1, $pNumRows = 1)
    {
        if ($pRow >= 1) {
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . ($pRow + $pNumRows), 0, -$pNumRows, $this);
        } else {
            throw new PHPExcel_Exception("Rows to be deleted should at least start from row 1.");
        }
        return $this;
    }
    public function removeColumn($pColumn = 'A', $pNumCols = 1)
    {
        if (!is_numeric($pColumn)) {
            $pColumn            = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($pColumn) - 1 + $pNumCols);
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pColumn . '1', -$pNumCols, 0, $this);
        } else {
            throw new PHPExcel_Exception("Column references should not be numeric.");
        }
        return $this;
    }
    public function removeColumnByIndex($pColumn = 0, $pNumCols = 1)
    {
        if ($pColumn >= 0) {
            return $this->removeColumn(PHPExcel_Cell::stringFromColumnIndex($pColumn), $pNumCols);
        } else {
            throw new PHPExcel_Exception("Columns to be deleted should at least start from column 0");
        }
    }
    public function getShowGridlines()
    {
        return $this->_showGridlines;
    }
    public function setShowGridlines($pValue = false)
    {
        $this->_showGridlines = $pValue;
        return $this;
    }
    public function getPrintGridlines()
    {
        return $this->_printGridlines;
    }
    public function setPrintGridlines($pValue = false)
    {
        $this->_printGridlines = $pValue;
        return $this;
    }
    public function getShowRowColHeaders()
    {
        return $this->_showRowColHeaders;
    }
    public function setShowRowColHeaders($pValue = false)
    {
        $this->_showRowColHeaders = $pValue;
        return $this;
    }
    public function getShowSummaryBelow()
    {
        return $this->_showSummaryBelow;
    }
    public function setShowSummaryBelow($pValue = true)
    {
        $this->_showSummaryBelow = $pValue;
        return $this;
    }
    public function getShowSummaryRight()
    {
        return $this->_showSummaryRight;
    }
    public function setShowSummaryRight($pValue = true)
    {
        $this->_showSummaryRight = $pValue;
        return $this;
    }
    public function getComments()
    {
        return $this->_comments;
    }
    public function setComments($pValue = array())
    {
        $this->_comments = $pValue;
        return $this;
    }
    public function getComment($pCellCoordinate = 'A1')
    {
        $pCellCoordinate = strtoupper($pCellCoordinate);
        if (strpos($pCellCoordinate, ':') !== false || strpos($pCellCoordinate, ',') !== false) {
            throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells.');
        } else if (strpos($pCellCoordinate, '$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate string must not be absolute.');
        } else if ($pCellCoordinate == '') {
            throw new PHPExcel_Exception('Cell coordinate can not be zero-length string.');
        } else {
            if (isset($this->_comments[$pCellCoordinate])) {
                return $this->_comments[$pCellCoordinate];
            } else {
                $newComment                        = new PHPExcel_Comment();
                $this->_comments[$pCellCoordinate] = $newComment;
                return $newComment;
            }
        }
    }
    public function getCommentByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->getComment(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    public function getSelectedCell()
    {
        return $this->getSelectedCells();
    }
    public function getActiveCell()
    {
        return $this->_activeCell;
    }
    public function getSelectedCells()
    {
        return $this->_selectedCells;
    }
    public function setSelectedCell($pCoordinate = 'A1')
    {
        return $this->setSelectedCells($pCoordinate);
    }
    public function setSelectedCells($pCoordinate = 'A1')
    {
        $pCoordinate = strtoupper($pCoordinate);
        $pCoordinate = preg_replace('/^([A-Z]+)$/', '${1}:${1}', $pCoordinate);
        $pCoordinate = preg_replace('/^([0-9]+)$/', '${1}:${1}', $pCoordinate);
        $pCoordinate = preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $pCoordinate);
        $pCoordinate = preg_replace('/^([0-9]+):([0-9]+)$/', 'A${1}:XFD${2}', $pCoordinate);
        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            list($first, ) = PHPExcel_Cell::splitRange($pCoordinate);
            $this->_activeCell = $first[0];
        } else {
            $this->_activeCell = $pCoordinate;
        }
        $this->_selectedCells = $pCoordinate;
        return $this;
    }
    public function setSelectedCellByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->setSelectedCells(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    public function getRightToLeft()
    {
        return $this->_rightToLeft;
    }
    public function setRightToLeft($value = false)
    {
        $this->_rightToLeft = $value;
        return $this;
    }
    public function fromArray($source = null, $nullValue = null, $startCell = 'A1', $strictNullComparison = false)
    {
        if (is_array($source)) {
            if (!is_array(end($source))) {
                $source = array(
                    $source
                );
            }
            list($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($startCell);
            foreach ($source as $rowData) {
                $currentColumn = $startColumn;
                foreach ($rowData as $cellValue) {
                    if ($strictNullComparison) {
                        if ($cellValue !== $nullValue) {
                            $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                        }
                    } else {
                        if ($cellValue != $nullValue) {
                            $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                        }
                    }
                    ++$currentColumn;
                }
                ++$startRow;
            }
        } else {
            throw new PHPExcel_Exception("Parameter \$source should be an array.");
        }
        return $this;
    }
    public function rangeToArray($pRange = 'A1', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        $returnValue = array();
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange);
        $minCol = PHPExcel_Cell::stringFromColumnIndex($rangeStart[0] - 1);
        $minRow = $rangeStart[1];
        $maxCol = PHPExcel_Cell::stringFromColumnIndex($rangeEnd[0] - 1);
        $maxRow = $rangeEnd[1];
        $maxCol++;
        $r = -1;
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            $rRef = ($returnCellRef) ? $row : ++$r;
            $c    = -1;
            for ($col = $minCol; $col != $maxCol; ++$col) {
                $cRef = ($returnCellRef) ? $col : ++$c;
                if ($this->_cellCollection->isDataSet($col . $row)) {
                    $cell = $this->_cellCollection->getCacheData($col . $row);
                    if ($cell->getValue() !== null) {
                        if ($cell->getValue() instanceof PHPExcel_RichText) {
                            $returnValue[$rRef][$cRef] = $cell->getValue()->getPlainText();
                        } else {
                            if ($calculateFormulas) {
                                $returnValue[$rRef][$cRef] = $cell->getCalculatedValue();
                            } else {
                                $returnValue[$rRef][$cRef] = $cell->getValue();
                            }
                        }
                        if ($formatData) {
                            $style                     = $this->_parent->getCellXfByIndex($cell->getXfIndex());
                            $returnValue[$rRef][$cRef] = PHPExcel_Style_NumberFormat::toFormattedString($returnValue[$rRef][$cRef], $style->getNumberFormat()->getFormatCode());
                        }
                    } else {
                        $returnValue[$rRef][$cRef] = $nullValue;
                    }
                } else {
                    $returnValue[$rRef][$cRef] = $nullValue;
                }
            }
        }
        return $returnValue;
    }
    public function namedRangeToArray($pNamedRange = '', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        $namedRange = PHPExcel_NamedRange::resolveRange($pNamedRange, $this);
        if ($namedRange !== null) {
            $pWorkSheet = $namedRange->getWorksheet();
            $pCellRange = $namedRange->getRange();
            return $pWorkSheet->rangeToArray($pCellRange, $nullValue, $calculateFormulas, $formatData, $returnCellRef);
        }
        throw new PHPExcel_Exception('Named Range ' . $pNamedRange . ' does not exist.');
    }
    public function toArray($nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        $this->garbageCollect();
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();
        return $this->rangeToArray('A1:' . $maxCol . $maxRow, $nullValue, $calculateFormulas, $formatData, $returnCellRef);
    }
    public function getRowIterator($startRow = 1)
    {
        return new PHPExcel_Worksheet_RowIterator($this, $startRow);
    }
    public function garbageCollect()
    {
        $this->_cellCollection->getCacheData('A1');
        $colRow        = $this->_cellCollection->getHighestRowAndColumn();
        $highestRow    = $colRow['row'];
        $highestColumn = PHPExcel_Cell::columnIndexFromString($colRow['column']);
        foreach ($this->_columnDimensions as $dimension) {
            $highestColumn = max($highestColumn, PHPExcel_Cell::columnIndexFromString($dimension->getColumnIndex()));
        }
        foreach ($this->_rowDimensions as $dimension) {
            $highestRow = max($highestRow, $dimension->getRowIndex());
        }
        if ($highestColumn < 0) {
            $this->_cachedHighestColumn = 'A';
        } else {
            $this->_cachedHighestColumn = PHPExcel_Cell::stringFromColumnIndex(--$highestColumn);
        }
        $this->_cachedHighestRow = $highestRow;
        return $this;
    }
    public function getHashCode()
    {
        if ($this->_dirty) {
            $this->_hash  = md5($this->_title . $this->_autoFilter . ($this->_protection->isProtectionEnabled() ? 't' : 'f') . __CLASS__);
            $this->_dirty = false;
        }
        return $this->_hash;
    }
    public static function extractSheetTitle($pRange, $returnRange = false)
    {
        if (($sep = strpos($pRange, '!')) === false) {
            return '';
        }
        if ($returnRange) {
            return array(
                trim(substr($pRange, 0, $sep), "'"),
                substr($pRange, $sep + 1)
            );
        }
        return substr($pRange, $sep + 1);
    }
    public function getHyperlink($pCellCoordinate = 'A1')
    {
        if (isset($this->_hyperlinkCollection[$pCellCoordinate])) {
            return $this->_hyperlinkCollection[$pCellCoordinate];
        }
        $this->_hyperlinkCollection[$pCellCoordinate] = new PHPExcel_Cell_Hyperlink();
        return $this->_hyperlinkCollection[$pCellCoordinate];
    }
    public function setHyperlink($pCellCoordinate = 'A1', PHPExcel_Cell_Hyperlink $pHyperlink = null)
    {
        if ($pHyperlink === null) {
            unset($this->_hyperlinkCollection[$pCellCoordinate]);
        } else {
            $this->_hyperlinkCollection[$pCellCoordinate] = $pHyperlink;
        }
        return $this;
    }
    public function hyperlinkExists($pCoordinate = 'A1')
    {
        return isset($this->_hyperlinkCollection[$pCoordinate]);
    }
    public function getHyperlinkCollection()
    {
        return $this->_hyperlinkCollection;
    }
    public function getDataValidation($pCellCoordinate = 'A1')
    {
        if (isset($this->_dataValidationCollection[$pCellCoordinate])) {
            return $this->_dataValidationCollection[$pCellCoordinate];
        }
        $this->_dataValidationCollection[$pCellCoordinate] = new PHPExcel_Cell_DataValidation();
        return $this->_dataValidationCollection[$pCellCoordinate];
    }
    public function setDataValidation($pCellCoordinate = 'A1', PHPExcel_Cell_DataValidation $pDataValidation = null)
    {
        if ($pDataValidation === null) {
            unset($this->_dataValidationCollection[$pCellCoordinate]);
        } else {
            $this->_dataValidationCollection[$pCellCoordinate] = $pDataValidation;
        }
        return $this;
    }
    public function dataValidationExists($pCoordinate = 'A1')
    {
        return isset($this->_dataValidationCollection[$pCoordinate]);
    }
    public function getDataValidationCollection()
    {
        return $this->_dataValidationCollection;
    }
    public function shrinkRangeToFit($range)
    {
        $maxCol      = $this->getHighestColumn();
        $maxRow      = $this->getHighestRow();
        $maxCol      = PHPExcel_Cell::columnIndexFromString($maxCol);
        $rangeBlocks = explode(' ', $range);
        foreach ($rangeBlocks as &$rangeSet) {
            $rangeBoundaries = PHPExcel_Cell::getRangeBoundaries($rangeSet);
            if (PHPExcel_Cell::columnIndexFromString($rangeBoundaries[0][0]) > $maxCol) {
                $rangeBoundaries[0][0] = PHPExcel_Cell::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[0][1] > $maxRow) {
                $rangeBoundaries[0][1] = $maxRow;
            }
            if (PHPExcel_Cell::columnIndexFromString($rangeBoundaries[1][0]) > $maxCol) {
                $rangeBoundaries[1][0] = PHPExcel_Cell::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[1][1] > $maxRow) {
                $rangeBoundaries[1][1] = $maxRow;
            }
            $rangeSet = $rangeBoundaries[0][0] . $rangeBoundaries[0][1] . ':' . $rangeBoundaries[1][0] . $rangeBoundaries[1][1];
        }
        unset($rangeSet);
        $stRange = implode(' ', $rangeBlocks);
        return $stRange;
    }
    public function getTabColor()
    {
        if ($this->_tabColor === null)
            $this->_tabColor = new PHPExcel_Style_Color();
        return $this->_tabColor;
    }
    public function resetTabColor()
    {
        $this->_tabColor = null;
        unset($this->_tabColor);
        return $this;
    }
    public function isTabColorSet()
    {
        return ($this->_tabColor !== null);
    }
    public function copy()
    {
        $copied = clone $this;
        return $copied;
    }
    public function __clone()
    {
        foreach ($this as $key => $val) {
            if ($key == '_parent') {
                continue;
            }
            if (is_object($val) || (is_array($val))) {
                if ($key == '_cellCollection') {
                    $newCollection = clone $this->_cellCollection;
                    $newCollection->copyCellCollection($this);
                    $this->_cellCollection = $newCollection;
                } elseif ($key == '_drawingCollection') {
                    $newCollection            = clone $this->_drawingCollection;
                    $this->_drawingCollection = $newCollection;
                } elseif (($key == '_autoFilter') && ($this->_autoFilter instanceof PHPExcel_Worksheet_AutoFilter)) {
                    $newAutoFilter     = clone $this->_autoFilter;
                    $this->_autoFilter = $newAutoFilter;
                    $this->_autoFilter->setParent($this);
                } else {
                    $this->{$key} = unserialize(serialize($val));
                }
            }
        }
    }
}