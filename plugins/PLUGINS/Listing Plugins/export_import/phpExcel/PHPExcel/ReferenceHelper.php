<?php
class PHPExcel_ReferenceHelper
{
    const REFHELPER_REGEXP_CELLREF = '((\w*|\'[^!]*\')!)?(?<![:a-z\$])(\$?[a-z]{1,3}\$?\d+)(?=[^:!\d\'])';
    const REFHELPER_REGEXP_CELLRANGE = '((\w*|\'[^!]*\')!)?(\$?[a-z]{1,3}\$?\d+):(\$?[a-z]{1,3}\$?\d+)';
    const REFHELPER_REGEXP_ROWRANGE = '((\w*|\'[^!]*\')!)?(\$?\d+):(\$?\d+)';
    const REFHELPER_REGEXP_COLRANGE = '((\w*|\'[^!]*\')!)?(\$?[a-z]{1,3}):(\$?[a-z]{1,3})';
    private static $_instance;
    public static function getInstance()
    {
        if (!isset(self::$_instance) || (self::$_instance === NULL)) {
            self::$_instance = new PHPExcel_ReferenceHelper();
        }
        return self::$_instance;
    }
    protected function __construct()
    {
    }
    public function insertNewBefore($pBefore = 'A1', $pNumCols = 0, $pNumRows = 0, PHPExcel_Worksheet $pSheet = null)
    {
        $remove          = ($pNumCols < 0 || $pNumRows < 0);
        $aCellCollection = $pSheet->getCellCollection();
        $beforeColumn    = 'A';
        $beforeRow       = 1;
        list($beforeColumn, $beforeRow) = PHPExcel_Cell::coordinateFromString($pBefore);
        $beforeColumnIndex = PHPExcel_Cell::columnIndexFromString($beforeColumn);
        $highestColumn     = $pSheet->getHighestColumn();
        $highestRow        = $pSheet->getHighestRow();
        if ($pNumCols < 0 && $beforeColumnIndex - 2 + $pNumCols > 0) {
            for ($i = 1; $i <= $highestRow - 1; ++$i) {
                for ($j = $beforeColumnIndex - 1 + $pNumCols; $j <= $beforeColumnIndex - 2; ++$j) {
                    $coordinate = PHPExcel_Cell::stringFromColumnIndex($j) . $i;
                    $pSheet->removeConditionalStyles($coordinate);
                    if ($pSheet->cellExists($coordinate)) {
                        $pSheet->getCell($coordinate)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_NULL);
                        $pSheet->getCell($coordinate)->setXfIndex(0);
                    }
                }
            }
        }
        if ($pNumRows < 0 && $beforeRow - 1 + $pNumRows > 0) {
            for ($i = $beforeColumnIndex - 1; $i <= PHPExcel_Cell::columnIndexFromString($highestColumn) - 1; ++$i) {
                for ($j = $beforeRow + $pNumRows; $j <= $beforeRow - 1; ++$j) {
                    $coordinate = PHPExcel_Cell::stringFromColumnIndex($i) . $j;
                    $pSheet->removeConditionalStyles($coordinate);
                    if ($pSheet->cellExists($coordinate)) {
                        $pSheet->getCell($coordinate)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_NULL);
                        $pSheet->getCell($coordinate)->setXfIndex(0);
                    }
                }
            }
        }
        while (($cellID = $remove ? array_shift($aCellCollection) : array_pop($aCellCollection))) {
            $cell      = $pSheet->getCell($cellID);
            $cellIndex = PHPExcel_Cell::columnIndexFromString($cell->getColumn());
            if ($cellIndex - 1 + $pNumCols < 0) {
                continue;
            }
            $newCoordinates = PHPExcel_Cell::stringFromColumnIndex($cellIndex - 1 + $pNumCols) . ($cell->getRow() + $pNumRows);
            if (($cellIndex >= $beforeColumnIndex) && ($cell->getRow() >= $beforeRow)) {
                $pSheet->getCell($newCoordinates)->setXfIndex($cell->getXfIndex());
                if ($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_FORMULA) {
                    $pSheet->getCell($newCoordinates)->setValue($this->updateFormulaReferences($cell->getValue(), $pBefore, $pNumCols, $pNumRows, $pSheet->getTitle()));
                } else {
                    $pSheet->getCell($newCoordinates)->setValue($cell->getValue());
                }
                $pSheet->getCellCacheController()->deleteCacheData($cellID);
            } else {
                if ($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_FORMULA) {
                    $cell->setValue($this->updateFormulaReferences($cell->getValue(), $pBefore, $pNumCols, $pNumRows, $pSheet->getTitle()));
                }
            }
        }
        $highestColumn = $pSheet->getHighestColumn();
        $highestRow    = $pSheet->getHighestRow();
        if ($pNumCols > 0 && $beforeColumnIndex - 2 > 0) {
            for ($i = $beforeRow; $i <= $highestRow - 1; ++$i) {
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($beforeColumnIndex - 2) . $i;
                if ($pSheet->cellExists($coordinate)) {
                    $xfIndex           = $pSheet->getCell($coordinate)->getXfIndex();
                    $conditionalStyles = $pSheet->conditionalStylesExists($coordinate) ? $pSheet->getConditionalStyles($coordinate) : false;
                    for ($j = $beforeColumnIndex - 1; $j <= $beforeColumnIndex - 2 + $pNumCols; ++$j) {
                        $pSheet->getCellByColumnAndRow($j, $i)->setXfIndex($xfIndex);
                        if ($conditionalStyles) {
                            $cloned = array();
                            foreach ($conditionalStyles as $conditionalStyle) {
                                $cloned[] = clone $conditionalStyle;
                            }
                            $pSheet->setConditionalStyles(PHPExcel_Cell::stringFromColumnIndex($j) . $i, $cloned);
                        }
                    }
                }
            }
        }
        if ($pNumRows > 0 && $beforeRow - 1 > 0) {
            for ($i = $beforeColumnIndex - 1; $i <= PHPExcel_Cell::columnIndexFromString($highestColumn) - 1; ++$i) {
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($i) . ($beforeRow - 1);
                if ($pSheet->cellExists($coordinate)) {
                    $xfIndex           = $pSheet->getCell($coordinate)->getXfIndex();
                    $conditionalStyles = $pSheet->conditionalStylesExists($coordinate) ? $pSheet->getConditionalStyles($coordinate) : false;
                    for ($j = $beforeRow; $j <= $beforeRow - 1 + $pNumRows; ++$j) {
                        $pSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($i) . $j)->setXfIndex($xfIndex);
                        if ($conditionalStyles) {
                            $cloned = array();
                            foreach ($conditionalStyles as $conditionalStyle) {
                                $cloned[] = clone $conditionalStyle;
                            }
                            $pSheet->setConditionalStyles(PHPExcel_Cell::stringFromColumnIndex($i) . $j, $cloned);
                        }
                    }
                }
            }
        }
        $aColumnDimensions = array_reverse($pSheet->getColumnDimensions(), true);
        if (!empty($aColumnDimensions)) {
            foreach ($aColumnDimensions as $objColumnDimension) {
                $newReference = $this->updateCellReference($objColumnDimension->getColumnIndex() . '1', $pBefore, $pNumCols, $pNumRows);
                list($newReference) = PHPExcel_Cell::coordinateFromString($newReference);
                if ($objColumnDimension->getColumnIndex() != $newReference) {
                    $objColumnDimension->setColumnIndex($newReference);
                }
            }
            $pSheet->refreshColumnDimensions();
        }
        $aRowDimensions = array_reverse($pSheet->getRowDimensions(), true);
        if (!empty($aRowDimensions)) {
            foreach ($aRowDimensions as $objRowDimension) {
                $newReference = $this->updateCellReference('A' . $objRowDimension->getRowIndex(), $pBefore, $pNumCols, $pNumRows);
                list(, $newReference) = PHPExcel_Cell::coordinateFromString($newReference);
                if ($objRowDimension->getRowIndex() != $newReference) {
                    $objRowDimension->setRowIndex($newReference);
                }
            }
            $pSheet->refreshRowDimensions();
            $copyDimension = $pSheet->getRowDimension($beforeRow - 1);
            for ($i = $beforeRow; $i <= $beforeRow - 1 + $pNumRows; ++$i) {
                $newDimension = $pSheet->getRowDimension($i);
                $newDimension->setRowHeight($copyDimension->getRowHeight());
                $newDimension->setVisible($copyDimension->getVisible());
                $newDimension->setOutlineLevel($copyDimension->getOutlineLevel());
                $newDimension->setCollapsed($copyDimension->getCollapsed());
            }
        }
        $aBreaks = array_reverse($pSheet->getBreaks(), true);
        foreach ($aBreaks as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->setBreak($newReference, $value);
                $pSheet->setBreak($key, PHPExcel_Worksheet::BREAK_NONE);
            }
        }
        $aComments    = $pSheet->getComments();
        $aNewComments = array();
        foreach ($aComments as $key => &$value) {
            $newReference                = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            $aNewComments[$newReference] = $value;
        }
        $pSheet->setComments($aNewComments);
        $aHyperlinkCollection = array_reverse($pSheet->getHyperlinkCollection(), true);
        foreach ($aHyperlinkCollection as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->setHyperlink($newReference, $value);
                $pSheet->setHyperlink($key, null);
            }
        }
        $aDataValidationCollection = array_reverse($pSheet->getDataValidationCollection(), true);
        foreach ($aDataValidationCollection as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->setDataValidation($newReference, $value);
                $pSheet->setDataValidation($key, null);
            }
        }
        $aMergeCells    = $pSheet->getMergeCells();
        $aNewMergeCells = array();
        foreach ($aMergeCells as $key => &$value) {
            $newReference                  = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            $aNewMergeCells[$newReference] = $newReference;
        }
        $pSheet->setMergeCells($aNewMergeCells);
        $aProtectedCells = array_reverse($pSheet->getProtectedCells(), true);
        foreach ($aProtectedCells as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->protectCells($newReference, $value, true);
                $pSheet->unprotectCells($key);
            }
        }
        $autoFilter      = $pSheet->getAutoFilter();
        $autoFilterRange = $autoFilter->getRange();
        if (!empty($autoFilterRange)) {
            if ($pNumCols != 0) {
                $autoFilterColumns = array_keys($autoFilter->getColumns());
                if (count($autoFilterColumns) > 0) {
                    list($column, $row) = sscanf($pBefore, '%[A-Z]%d');
                    $columnIndex = PHPExcel_Cell::columnIndexFromString($column);
                    list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($autoFilterRange);
                    if ($columnIndex <= $rangeEnd[0]) {
                        if ($pNumCols < 0) {
                            $deleteColumn = $columnIndex + $pNumCols - 1;
                            $deleteCount  = abs($pNumCols);
                            for ($i = 1; $i <= $deleteCount; ++$i) {
                                if (in_array(PHPExcel_Cell::stringFromColumnIndex($deleteColumn), $autoFilterColumns)) {
                                    $autoFilter->clearColumn(PHPExcel_Cell::stringFromColumnIndex($deleteColumn));
                                }
                                ++$deleteColumn;
                            }
                        }
                        $startCol = ($columnIndex > $rangeStart[0]) ? $columnIndex : $rangeStart[0];
                        if ($pNumCols > 0) {
                            $startColID  = PHPExcel_Cell::stringFromColumnIndex($startCol - 1);
                            $toColID     = PHPExcel_Cell::stringFromColumnIndex($startCol + $pNumCols - 1);
                            $endColID    = PHPExcel_Cell::stringFromColumnIndex($rangeEnd[0]);
                            $startColRef = $startCol;
                            $endColRef   = $rangeEnd[0];
                            $toColRef    = $rangeEnd[0] + $pNumCols;
                            do {
                                $autoFilter->shiftColumn(PHPExcel_Cell::stringFromColumnIndex($endColRef - 1), PHPExcel_Cell::stringFromColumnIndex($toColRef - 1));
                                --$endColRef;
                                --$toColRef;
                            } while ($startColRef <= $endColRef);
                        } else {
                            $startColID = PHPExcel_Cell::stringFromColumnIndex($startCol - 1);
                            $toColID    = PHPExcel_Cell::stringFromColumnIndex($startCol + $pNumCols - 1);
                            $endColID   = PHPExcel_Cell::stringFromColumnIndex($rangeEnd[0]);
                            do {
                                $autoFilter->shiftColumn($startColID, $toColID);
                                ++$startColID;
                                ++$toColID;
                            } while ($startColID != $endColID);
                        }
                    }
                }
            }
            $pSheet->setAutoFilter($this->updateCellReference($autoFilterRange, $pBefore, $pNumCols, $pNumRows));
        }
        if ($pSheet->getFreezePane() != '') {
            $pSheet->freezePane($this->updateCellReference($pSheet->getFreezePane(), $pBefore, $pNumCols, $pNumRows));
        }
        if ($pSheet->getPageSetup()->isPrintAreaSet()) {
            $pSheet->getPageSetup()->setPrintArea($this->updateCellReference($pSheet->getPageSetup()->getPrintArea(), $pBefore, $pNumCols, $pNumRows));
        }
        $aDrawings = $pSheet->getDrawingCollection();
        foreach ($aDrawings as $objDrawing) {
            $newReference = $this->updateCellReference($objDrawing->getCoordinates(), $pBefore, $pNumCols, $pNumRows);
            if ($objDrawing->getCoordinates() != $newReference) {
                $objDrawing->setCoordinates($newReference);
            }
        }
        if (count($pSheet->getParent()->getNamedRanges()) > 0) {
            foreach ($pSheet->getParent()->getNamedRanges() as $namedRange) {
                if ($namedRange->getWorksheet()->getHashCode() == $pSheet->getHashCode()) {
                    $namedRange->setRange($this->updateCellReference($namedRange->getRange(), $pBefore, $pNumCols, $pNumRows));
                }
            }
        }
        $pSheet->garbageCollect();
    }
    public function updateFormulaReferences($pFormula = '', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0, $sheetName = '')
    {
        $formulaBlocks = explode('"', $pFormula);
        $i             = false;
        foreach ($formulaBlocks as &$formulaBlock) {
            if ($i = !$i) {
                $adjustCount   = 0;
                $newCellTokens = $cellTokens = array();
                $matchCount    = preg_match_all('/' . self::REFHELPER_REGEXP_ROWRANGE . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = substr($this->updateCellReference('$A' . $match[3], $pBefore, $pNumCols, $pNumRows), 2);
                        $modified4 = substr($this->updateCellReference('$A' . $match[4], $pBefore, $pNumCols, $pNumRows), 2);
                        if ($match[3] . ':' . $match[4] !== $modified3 . ':' . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                $column                    = 100000;
                                $row                       = 10000000 + trim($match[3], '$');
                                $cellIndex                 = $column . $row;
                                $newCellTokens[$cellIndex] = preg_quote($toString);
                                $cellTokens[$cellIndex]    = '/(?<!\d)' . preg_quote($fromString) . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_COLRANGE . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = substr($this->updateCellReference($match[3] . '$1', $pBefore, $pNumCols, $pNumRows), 0, -2);
                        $modified4 = substr($this->updateCellReference($match[4] . '$1', $pBefore, $pNumCols, $pNumRows), 0, -2);
                        if ($match[3] . ':' . $match[4] !== $modified3 . ':' . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                $column                    = PHPExcel_Cell::columnIndexFromString(trim($match[3], '$')) + 100000;
                                $row                       = 10000000;
                                $cellIndex                 = $column . $row;
                                $newCellTokens[$cellIndex] = preg_quote($toString);
                                $cellTokens[$cellIndex]    = '/(?<![A-Z])' . preg_quote($fromString) . '(?![A-Z])/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_CELLRANGE . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = $this->updateCellReference($match[3], $pBefore, $pNumCols, $pNumRows);
                        $modified4 = $this->updateCellReference($match[4], $pBefore, $pNumCols, $pNumRows);
                        if ($match[3] . $match[4] !== $modified3 . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                list($column, $row) = PHPExcel_Cell::coordinateFromString($match[3]);
                                $column                    = PHPExcel_Cell::columnIndexFromString(trim($column, '$')) + 100000;
                                $row                       = trim($row, '$') + 10000000;
                                $cellIndex                 = $column . $row;
                                $newCellTokens[$cellIndex] = preg_quote($toString);
                                $cellTokens[$cellIndex]    = '/(?<![A-Z])' . preg_quote($fromString) . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_CELLREF . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3];
                        $modified3 = $this->updateCellReference($match[3], $pBefore, $pNumCols, $pNumRows);
                        if ($match[3] !== $modified3) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3;
                                list($column, $row) = PHPExcel_Cell::coordinateFromString($match[3]);
                                $column                    = PHPExcel_Cell::columnIndexFromString(trim($column, '$')) + 100000;
                                $row                       = trim($row, '$') + 10000000;
                                $cellIndex                 = $column . $row;
                                $newCellTokens[$cellIndex] = preg_quote($toString);
                                $cellTokens[$cellIndex]    = '/(?<![A-Z])' . preg_quote($fromString) . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                if ($adjustCount > 0) {
                    if ($pNumCols > 0) {
                        krsort($cellTokens);
                        krsort($newCellTokens);
                    } else {
                        ksort($cellTokens);
                        ksort($newCellTokens);
                    }
                    $formulaBlock = str_replace('\\', '', preg_replace($cellTokens, $newCellTokens, $formulaBlock));
                }
            }
        }
        unset($formulaBlock);
        return implode('"', $formulaBlocks);
    }
    public function updateCellReference($pCellRange = 'A1', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0)
    {
        if (strpos($pCellRange, "!") !== false) {
            return $pCellRange;
        } elseif (strpos($pCellRange, ':') === false && strpos($pCellRange, ',') === false) {
            return $this->_updateSingleCellReference($pCellRange, $pBefore, $pNumCols, $pNumRows);
        } elseif (strpos($pCellRange, ':') !== false || strpos($pCellRange, ',') !== false) {
            return $this->_updateCellRange($pCellRange, $pBefore, $pNumCols, $pNumRows);
        } else {
            return $pCellRange;
        }
    }
    public function updateNamedFormulas(PHPExcel $pPhpExcel, $oldName = '', $newName = '')
    {
        if ($oldName == '') {
            return;
        }
        foreach ($pPhpExcel->getWorksheetIterator() as $sheet) {
            foreach ($sheet->getCellCollection(false) as $cellID) {
                $cell = $sheet->getCell($cellID);
                if (($cell !== NULL) && ($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_FORMULA)) {
                    $formula = $cell->getValue();
                    if (strpos($formula, $oldName) !== false) {
                        $formula = str_replace("'" . $oldName . "'!", "'" . $newName . "'!", $formula);
                        $formula = str_replace($oldName . "!", $newName . "!", $formula);
                        $cell->setValueExplicit($formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
                    }
                }
            }
        }
    }
    private function _updateCellRange($pCellRange = 'A1:A1', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0)
    {
        if (strpos($pCellRange, ':') !== false || strpos($pCellRange, ',') !== false) {
            $range = PHPExcel_Cell::splitRange($pCellRange);
            $ic    = count($range);
            for ($i = 0; $i < $ic; ++$i) {
                $jc = count($range[$i]);
                for ($j = 0; $j < $jc; ++$j) {
                    if (ctype_alpha($range[$i][$j])) {
                        $r             = PHPExcel_Cell::coordinateFromString($this->_updateSingleCellReference($range[$i][$j] . '1', $pBefore, $pNumCols, $pNumRows));
                        $range[$i][$j] = $r[0];
                    } elseif (ctype_digit($range[$i][$j])) {
                        $r             = PHPExcel_Cell::coordinateFromString($this->_updateSingleCellReference('A' . $range[$i][$j], $pBefore, $pNumCols, $pNumRows));
                        $range[$i][$j] = $r[1];
                    } else {
                        $range[$i][$j] = $this->_updateSingleCellReference($range[$i][$j], $pBefore, $pNumCols, $pNumRows);
                    }
                }
            }
            return PHPExcel_Cell::buildRange($range);
        } else {
            throw new PHPExcel_Exception("Only cell ranges may be passed to this method.");
        }
    }
    private function _updateSingleCellReference($pCellReference = 'A1', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0)
    {
        if (strpos($pCellReference, ':') === false && strpos($pCellReference, ',') === false) {
            list($beforeColumn, $beforeRow) = PHPExcel_Cell::coordinateFromString($pBefore);
            list($newColumn, $newRow) = PHPExcel_Cell::coordinateFromString($pCellReference);
            $updateColumn = (($newColumn{0} != '$') && ($beforeColumn{0} != '$') && PHPExcel_Cell::columnIndexFromString($newColumn) >= PHPExcel_Cell::columnIndexFromString($beforeColumn));
            $updateRow    = (($newRow{0} != '$') && ($beforeRow{0} != '$') && $newRow >= $beforeRow);
            if ($updateColumn) {
                $newColumn = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($newColumn) - 1 + $pNumCols);
            }
            if ($updateRow) {
                $newRow = $newRow + $pNumRows;
            }
            return $newColumn . $newRow;
        } else {
            throw new PHPExcel_Exception("Only single cell references may be passed to this method.");
        }
    }
    public final function __clone()
    {
        throw new PHPExcel_Exception("Cloning a Singleton is not allowed!");
    }
}