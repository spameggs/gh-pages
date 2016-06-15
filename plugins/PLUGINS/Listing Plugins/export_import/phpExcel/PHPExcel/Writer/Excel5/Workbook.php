<?php
class PHPExcel_Writer_Excel5_Workbook extends PHPExcel_Writer_Excel5_BIFFwriter
{
    private $_parser;
    public $_biffsize;
    private $_xfWriters = array();
    public $_palette;
    public $_codepage;
    public $_country_code;
    private $_phpExcel;
    private $_fontWriters = array();
    private $_addedFonts = array();
    private $_numberFormats = array();
    private $_addedNumberFormats = array();
    private $_worksheetSizes = array();
    private $_worksheetOffsets = array();
    private $_str_total;
    private $_str_unique;
    private $_str_table;
    private $_colors;
    private $_escher;
    public function __construct(PHPExcel $phpExcel = null, &$str_total, &$str_unique, &$str_table, &$colors, $parser)
    {
        parent::__construct();
        $this->_parser       = $parser;
        $this->_biffsize     = 0;
        $this->_palette      = array();
        $this->_country_code = -1;
        $this->_str_total =& $str_total;
        $this->_str_unique =& $str_unique;
        $this->_str_table =& $str_table;
        $this->_colors =& $colors;
        $this->_setPaletteXl97();
        $this->_phpExcel = $phpExcel;
        $this->_codepage = 0x04B0;
        $countSheets     = $phpExcel->getSheetCount();
        for ($i = 0; $i < $countSheets; ++$i) {
            $phpSheet = $phpExcel->getSheet($i);
            $this->_parser->setExtSheet($phpSheet->getTitle(), $i);
            $supbook_index                = 0x00;
            $ref                          = pack('vvv', $supbook_index, $i, $i);
            $this->_parser->_references[] = $ref;
            if ($phpSheet->isTabColorSet()) {
                $this->_addColor($phpSheet->getTabColor()->getRGB());
            }
        }
    }
    public function addXfWriter($style, $isStyleXf = false)
    {
        $xfWriter = new PHPExcel_Writer_Excel5_Xf($style);
        $xfWriter->setIsStyleXf($isStyleXf);
        $fontIndex = $this->_addFont($style->getFont());
        $xfWriter->setFontIndex($fontIndex);
        $xfWriter->setFgColor($this->_addColor($style->getFill()->getStartColor()->getRGB()));
        $xfWriter->setBgColor($this->_addColor($style->getFill()->getEndColor()->getRGB()));
        $xfWriter->setBottomColor($this->_addColor($style->getBorders()->getBottom()->getColor()->getRGB()));
        $xfWriter->setTopColor($this->_addColor($style->getBorders()->getTop()->getColor()->getRGB()));
        $xfWriter->setRightColor($this->_addColor($style->getBorders()->getRight()->getColor()->getRGB()));
        $xfWriter->setLeftColor($this->_addColor($style->getBorders()->getLeft()->getColor()->getRGB()));
        $xfWriter->setDiagColor($this->_addColor($style->getBorders()->getDiagonal()->getColor()->getRGB()));
        if ($style->getNumberFormat()->getBuiltInFormatCode() === false) {
            $numberFormatHashCode = $style->getNumberFormat()->getHashCode();
            if (isset($this->_addedNumberFormats[$numberFormatHashCode])) {
                $numberFormatIndex = $this->_addedNumberFormats[$numberFormatHashCode];
            } else {
                $numberFormatIndex                                = 164 + count($this->_numberFormats);
                $this->_numberFormats[$numberFormatIndex]         = $style->getNumberFormat();
                $this->_addedNumberFormats[$numberFormatHashCode] = $numberFormatIndex;
            }
        } else {
            $numberFormatIndex = (int) $style->getNumberFormat()->getBuiltInFormatCode();
        }
        $xfWriter->setNumberFormatIndex($numberFormatIndex);
        $this->_xfWriters[] = $xfWriter;
        $xfIndex            = count($this->_xfWriters) - 1;
        return $xfIndex;
    }
    public function _addFont(PHPExcel_Style_Font $font)
    {
        $fontHashCode = $font->getHashCode();
        if (isset($this->_addedFonts[$fontHashCode])) {
            $fontIndex = $this->_addedFonts[$fontHashCode];
        } else {
            $countFonts = count($this->_fontWriters);
            $fontIndex  = ($countFonts < 4) ? $countFonts : $countFonts + 1;
            $fontWriter = new PHPExcel_Writer_Excel5_Font($font);
            $fontWriter->setColorIndex($this->_addColor($font->getColor()->getRGB()));
            $this->_fontWriters[]             = $fontWriter;
            $this->_addedFonts[$fontHashCode] = $fontIndex;
        }
        return $fontIndex;
    }
    private function _addColor($rgb)
    {
        if (!isset($this->_colors[$rgb])) {
            if (count($this->_colors) < 57) {
                $colorIndex                  = 8 + count($this->_colors);
                $this->_palette[$colorIndex] = array(
                    hexdec(substr($rgb, 0, 2)),
                    hexdec(substr($rgb, 2, 2)),
                    hexdec(substr($rgb, 4)),
                    0
                );
                $this->_colors[$rgb]         = $colorIndex;
            } else {
                $colorIndex = 0;
            }
        } else {
            $colorIndex = $this->_colors[$rgb];
        }
        return $colorIndex;
    }
    function _setPaletteXl97()
    {
        $this->_palette = array(
            0x08 => array(
                0x00,
                0x00,
                0x00,
                0x00
            ),
            0x09 => array(
                0xff,
                0xff,
                0xff,
                0x00
            ),
            0x0A => array(
                0xff,
                0x00,
                0x00,
                0x00
            ),
            0x0B => array(
                0x00,
                0xff,
                0x00,
                0x00
            ),
            0x0C => array(
                0x00,
                0x00,
                0xff,
                0x00
            ),
            0x0D => array(
                0xff,
                0xff,
                0x00,
                0x00
            ),
            0x0E => array(
                0xff,
                0x00,
                0xff,
                0x00
            ),
            0x0F => array(
                0x00,
                0xff,
                0xff,
                0x00
            ),
            0x10 => array(
                0x80,
                0x00,
                0x00,
                0x00
            ),
            0x11 => array(
                0x00,
                0x80,
                0x00,
                0x00
            ),
            0x12 => array(
                0x00,
                0x00,
                0x80,
                0x00
            ),
            0x13 => array(
                0x80,
                0x80,
                0x00,
                0x00
            ),
            0x14 => array(
                0x80,
                0x00,
                0x80,
                0x00
            ),
            0x15 => array(
                0x00,
                0x80,
                0x80,
                0x00
            ),
            0x16 => array(
                0xc0,
                0xc0,
                0xc0,
                0x00
            ),
            0x17 => array(
                0x80,
                0x80,
                0x80,
                0x00
            ),
            0x18 => array(
                0x99,
                0x99,
                0xff,
                0x00
            ),
            0x19 => array(
                0x99,
                0x33,
                0x66,
                0x00
            ),
            0x1A => array(
                0xff,
                0xff,
                0xcc,
                0x00
            ),
            0x1B => array(
                0xcc,
                0xff,
                0xff,
                0x00
            ),
            0x1C => array(
                0x66,
                0x00,
                0x66,
                0x00
            ),
            0x1D => array(
                0xff,
                0x80,
                0x80,
                0x00
            ),
            0x1E => array(
                0x00,
                0x66,
                0xcc,
                0x00
            ),
            0x1F => array(
                0xcc,
                0xcc,
                0xff,
                0x00
            ),
            0x20 => array(
                0x00,
                0x00,
                0x80,
                0x00
            ),
            0x21 => array(
                0xff,
                0x00,
                0xff,
                0x00
            ),
            0x22 => array(
                0xff,
                0xff,
                0x00,
                0x00
            ),
            0x23 => array(
                0x00,
                0xff,
                0xff,
                0x00
            ),
            0x24 => array(
                0x80,
                0x00,
                0x80,
                0x00
            ),
            0x25 => array(
                0x80,
                0x00,
                0x00,
                0x00
            ),
            0x26 => array(
                0x00,
                0x80,
                0x80,
                0x00
            ),
            0x27 => array(
                0x00,
                0x00,
                0xff,
                0x00
            ),
            0x28 => array(
                0x00,
                0xcc,
                0xff,
                0x00
            ),
            0x29 => array(
                0xcc,
                0xff,
                0xff,
                0x00
            ),
            0x2A => array(
                0xcc,
                0xff,
                0xcc,
                0x00
            ),
            0x2B => array(
                0xff,
                0xff,
                0x99,
                0x00
            ),
            0x2C => array(
                0x99,
                0xcc,
                0xff,
                0x00
            ),
            0x2D => array(
                0xff,
                0x99,
                0xcc,
                0x00
            ),
            0x2E => array(
                0xcc,
                0x99,
                0xff,
                0x00
            ),
            0x2F => array(
                0xff,
                0xcc,
                0x99,
                0x00
            ),
            0x30 => array(
                0x33,
                0x66,
                0xff,
                0x00
            ),
            0x31 => array(
                0x33,
                0xcc,
                0xcc,
                0x00
            ),
            0x32 => array(
                0x99,
                0xcc,
                0x00,
                0x00
            ),
            0x33 => array(
                0xff,
                0xcc,
                0x00,
                0x00
            ),
            0x34 => array(
                0xff,
                0x99,
                0x00,
                0x00
            ),
            0x35 => array(
                0xff,
                0x66,
                0x00,
                0x00
            ),
            0x36 => array(
                0x66,
                0x66,
                0x99,
                0x00
            ),
            0x37 => array(
                0x96,
                0x96,
                0x96,
                0x00
            ),
            0x38 => array(
                0x00,
                0x33,
                0x66,
                0x00
            ),
            0x39 => array(
                0x33,
                0x99,
                0x66,
                0x00
            ),
            0x3A => array(
                0x00,
                0x33,
                0x00,
                0x00
            ),
            0x3B => array(
                0x33,
                0x33,
                0x00,
                0x00
            ),
            0x3C => array(
                0x99,
                0x33,
                0x00,
                0x00
            ),
            0x3D => array(
                0x99,
                0x33,
                0x66,
                0x00
            ),
            0x3E => array(
                0x33,
                0x33,
                0x99,
                0x00
            ),
            0x3F => array(
                0x33,
                0x33,
                0x33,
                0x00
            )
        );
    }
    public function writeWorkbook($pWorksheetSizes = null)
    {
        $this->_worksheetSizes = $pWorksheetSizes;
        $total_worksheets      = $this->_phpExcel->getSheetCount();
        $this->_storeBof(0x0005);
        $this->_writeCodepage();
        $this->_writeWindow1();
        $this->_writeDatemode();
        $this->_writeAllFonts();
        $this->_writeAllNumFormats();
        $this->_writeAllXfs();
        $this->_writeAllStyles();
        $this->_writePalette();
        $part3 = '';
        if ($this->_country_code != -1) {
            $part3 .= $this->_writeCountry();
        }
        $part3 .= $this->_writeRecalcId();
        $part3 .= $this->_writeSupbookInternal();
        $part3 .= $this->_writeExternsheetBiff8();
        $part3 .= $this->_writeAllDefinedNamesBiff8();
        $part3 .= $this->_writeMsoDrawingGroup();
        $part3 .= $this->_writeSharedStringsTable();
        $part3 .= $this->writeEof();
        $this->_calcSheetOffsets();
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->_writeBoundsheet($this->_phpExcel->getSheet($i), $this->_worksheetOffsets[$i]);
        }
        $this->_data .= $part3;
        return $this->_data;
    }
    function _calcSheetOffsets()
    {
        $boundsheet_length = 10;
        $offset            = $this->_datasize;
        $total_worksheets  = count($this->_phpExcel->getAllSheets());
        foreach ($this->_phpExcel->getWorksheetIterator() as $sheet) {
            $offset += $boundsheet_length + strlen(PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($sheet->getTitle()));
        }
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->_worksheetOffsets[$i] = $offset;
            $offset += $this->_worksheetSizes[$i];
        }
        $this->_biffsize = $offset;
    }
    private function _writeAllFonts()
    {
        foreach ($this->_fontWriters as $fontWriter) {
            $this->_append($fontWriter->writeFont());
        }
    }
    private function _writeAllNumFormats()
    {
        foreach ($this->_numberFormats as $numberFormatIndex => $numberFormat) {
            $this->_writeNumFormat($numberFormat->getFormatCode(), $numberFormatIndex);
        }
    }
    private function _writeAllXfs()
    {
        foreach ($this->_xfWriters as $xfWriter) {
            $this->_append($xfWriter->writeXf());
        }
    }
    private function _writeAllStyles()
    {
        $this->_writeStyle();
    }
    private function _writeExterns()
    {
        $countSheets = $this->_phpExcel->getSheetCount();
        $this->_writeExterncount($countSheets);
        for ($i = 0; $i < $countSheets; ++$i) {
            $this->_writeExternsheet($this->_phpExcel->getSheet($i)->getTitle());
        }
    }
    private function _writeNames()
    {
        $total_worksheets = $this->_phpExcel->getSheetCount();
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();
            if ($sheetSetup->isPrintAreaSet()) {
                $printArea    = PHPExcel_Cell::splitRange($sheetSetup->getPrintArea());
                $printArea    = $printArea[0];
                $printArea[0] = PHPExcel_Cell::coordinateFromString($printArea[0]);
                $printArea[1] = PHPExcel_Cell::coordinateFromString($printArea[1]);
                $print_rowmin = $printArea[0][1] - 1;
                $print_rowmax = $printArea[1][1] - 1;
                $print_colmin = PHPExcel_Cell::columnIndexFromString($printArea[0][0]) - 1;
                $print_colmax = PHPExcel_Cell::columnIndexFromString($printArea[1][0]) - 1;
                $this->_writeNameShort($i, 0x06, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);
            }
        }
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();
            if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
                $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
                $repeat = $sheetSetup->getRowsToRepeatAtTop();
                $rowmin = $repeat[0] - 1;
                $rowmax = $repeat[1] - 1;
                $this->_writeNameLong($i, 0x07, $rowmin, $rowmax, $colmin, $colmax);
            } else if ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
                if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
                    $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                    $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                    $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
                } else {
                    $colmin = 0;
                    $colmax = 255;
                }
                if ($sheetSetup->isRowsToRepeatAtTopSet()) {
                    $repeat = $sheetSetup->getRowsToRepeatAtTop();
                    $rowmin = $repeat[0] - 1;
                    $rowmax = $repeat[1] - 1;
                } else {
                    $rowmin = 0;
                    $rowmax = 65535;
                }
                $this->_writeNameShort($i, 0x07, $rowmin, $rowmax, $colmin, $colmax);
            }
        }
    }
    private function _writeAllDefinedNamesBiff8()
    {
        $chunk = '';
        if (count($this->_phpExcel->getNamedRanges()) > 0) {
            $namedRanges = $this->_phpExcel->getNamedRanges();
            foreach ($namedRanges as $namedRange) {
                $range = PHPExcel_Cell::splitRange($namedRange->getRange());
                for ($i = 0; $i < count($range); $i++) {
                    $range[$i][0] = '\'' . str_replace("'", "''", $namedRange->getWorksheet()->getTitle()) . '\'!' . PHPExcel_Cell::absoluteCoordinate($range[$i][0]);
                    if (isset($range[$i][1])) {
                        $range[$i][1] = PHPExcel_Cell::absoluteCoordinate($range[$i][1]);
                    }
                }
                $range = PHPExcel_Cell::buildRange($range);
                try {
                    $error       = $this->_parser->parse($range);
                    $formulaData = $this->_parser->toReversePolish();
                    if (isset($formulaData{0}) and ($formulaData{0} == "\x7A" or $formulaData{0} == "\x5A")) {
                        $formulaData = "\x3A" . substr($formulaData, 1);
                    }
                    if ($namedRange->getLocalOnly()) {
                        $scope = $this->_phpExcel->getIndex($namedRange->getScope()) + 1;
                    } else {
                        $scope = 0;
                    }
                    $chunk .= $this->writeData($this->_writeDefinedNameBiff8($namedRange->getName(), $formulaData, $scope, false));
                }
                catch (PHPExcel_Exception $e) {
                }
            }
        }
        $total_worksheets = $this->_phpExcel->getSheetCount();
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();
            if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
                $repeat      = $sheetSetup->getColumnsToRepeatAtLeft();
                $colmin      = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                $colmax      = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
                $repeat      = $sheetSetup->getRowsToRepeatAtTop();
                $rowmin      = $repeat[0] - 1;
                $rowmax      = $repeat[1] - 1;
                $formulaData = pack('Cv', 0x29, 0x17);
                $formulaData .= pack('Cvvvvv', 0x3B, $i, 0, 65535, $colmin, $colmax);
                $formulaData .= pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, 0, 255);
                $formulaData .= pack('C', 0x10);
                $chunk .= $this->writeData($this->_writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));
            } else if ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
                if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
                    $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                    $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                    $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
                } else {
                    $colmin = 0;
                    $colmax = 255;
                }
                if ($sheetSetup->isRowsToRepeatAtTopSet()) {
                    $repeat = $sheetSetup->getRowsToRepeatAtTop();
                    $rowmin = $repeat[0] - 1;
                    $rowmax = $repeat[1] - 1;
                } else {
                    $rowmin = 0;
                    $rowmax = 65535;
                }
                $formulaData = pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, $colmin, $colmax);
                $chunk .= $this->writeData($this->_writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));
            }
        }
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();
            if ($sheetSetup->isPrintAreaSet()) {
                $printArea      = PHPExcel_Cell::splitRange($sheetSetup->getPrintArea());
                $countPrintArea = count($printArea);
                $formulaData    = '';
                for ($j = 0; $j < $countPrintArea; ++$j) {
                    $printAreaRect    = $printArea[$j];
                    $printAreaRect[0] = PHPExcel_Cell::coordinateFromString($printAreaRect[0]);
                    $printAreaRect[1] = PHPExcel_Cell::coordinateFromString($printAreaRect[1]);
                    $print_rowmin     = $printAreaRect[0][1] - 1;
                    $print_rowmax     = $printAreaRect[1][1] - 1;
                    $print_colmin     = PHPExcel_Cell::columnIndexFromString($printAreaRect[0][0]) - 1;
                    $print_colmax     = PHPExcel_Cell::columnIndexFromString($printAreaRect[1][0]) - 1;
                    $formulaData .= pack('Cvvvvv', 0x3B, $i, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);
                    if ($j > 0) {
                        $formulaData .= pack('C', 0x10);
                    }
                }
                $chunk .= $this->writeData($this->_writeDefinedNameBiff8(pack('C', 0x06), $formulaData, $i + 1, true));
            }
        }
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetAutoFilter = $this->_phpExcel->getSheet($i)->getAutoFilter();
            $autoFilterRange = $sheetAutoFilter->getRange();
            if (!empty($autoFilterRange)) {
                $rangeBounds = PHPExcel_Cell::rangeBoundaries($autoFilterRange);
                $name        = pack('C', 0x0D);
                $chunk .= $this->writeData($this->_writeShortNameBiff8($name, $i + 1, $rangeBounds, true));
            }
        }
        return $chunk;
    }
    private function _writeDefinedNameBiff8($name, $formulaData, $sheetIndex = 0, $isBuiltIn = false)
    {
        $record  = 0x0018;
        $options = $isBuiltIn ? 0x20 : 0x00;
        $nlen    = PHPExcel_Shared_String::CountCharacters($name);
        $name    = substr(PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($name), 2);
        $sz      = strlen($formulaData);
        $data    = pack('vCCvvvCCCC', $options, 0, $nlen, $sz, 0, $sheetIndex, 0, 0, 0, 0) . $name . $formulaData;
        $length  = strlen($data);
        $header  = pack('vv', $record, $length);
        return $header . $data;
    }
    private function _writeShortNameBiff8($name, $sheetIndex = 0, $rangeBounds, $isHidden = false)
    {
        $record  = 0x0018;
        $options = ($isHidden ? 0x21 : 0x00);
        $extra   = pack('Cvvvvv', 0x3B, $sheetIndex - 1, $rangeBounds[0][1] - 1, $rangeBounds[1][1] - 1, $rangeBounds[0][0] - 1, $rangeBounds[1][0] - 1);
        $sz      = strlen($extra);
        $data    = pack('vCCvvvCCCCC', $options, 0, 1, $sz, 0, $sheetIndex, 0, 0, 0, 0, 0) . $name . $extra;
        $length  = strlen($data);
        $header  = pack('vv', $record, $length);
        return $header . $data;
    }
    private function _writeCodepage()
    {
        $record = 0x0042;
        $length = 0x0002;
        $cv     = $this->_codepage;
        $header = pack('vv', $record, $length);
        $data   = pack('v', $cv);
        $this->_append($header . $data);
    }
    private function _writeWindow1()
    {
        $record    = 0x003D;
        $length    = 0x0012;
        $xWn       = 0x0000;
        $yWn       = 0x0000;
        $dxWn      = 0x25BC;
        $dyWn      = 0x1572;
        $grbit     = 0x0038;
        $ctabsel   = 1;
        $wTabRatio = 0x0258;
        $itabFirst = 0;
        $itabCur   = $this->_phpExcel->getActiveSheetIndex();
        $header    = pack("vv", $record, $length);
        $data      = pack("vvvvvvvvv", $xWn, $yWn, $dxWn, $dyWn, $grbit, $itabCur, $itabFirst, $ctabsel, $wTabRatio);
        $this->_append($header . $data);
    }
    private function _writeBoundsheet($sheet, $offset)
    {
        $sheetname = $sheet->getTitle();
        $record    = 0x0085;
        switch ($sheet->getSheetState()) {
            case PHPExcel_Worksheet::SHEETSTATE_VISIBLE:
                $ss = 0x00;
                break;
            case PHPExcel_Worksheet::SHEETSTATE_HIDDEN:
                $ss = 0x01;
                break;
            case PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN:
                $ss = 0x02;
                break;
            default:
                $ss = 0x00;
                break;
        }
        $st    = 0x00;
        $grbit = 0x0000;
        $data  = pack("VCC", $offset, $ss, $st);
        $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($sheetname);
        $length = strlen($data);
        $header = pack("vv", $record, $length);
        $this->_append($header . $data);
    }
    private function _writeSupbookInternal()
    {
        $record = 0x01AE;
        $length = 0x0004;
        $header = pack("vv", $record, $length);
        $data   = pack("vv", $this->_phpExcel->getSheetCount(), 0x0401);
        return $this->writeData($header . $data);
    }
    private function _writeExternsheetBiff8()
    {
        $total_references = count($this->_parser->_references);
        $record           = 0x0017;
        $length           = 2 + 6 * $total_references;
        $supbook_index    = 0;
        $header           = pack("vv", $record, $length);
        $data             = pack('v', $total_references);
        for ($i = 0; $i < $total_references; ++$i) {
            $data .= $this->_parser->_references[$i];
        }
        return $this->writeData($header . $data);
    }
    private function _writeStyle()
    {
        $record  = 0x0293;
        $length  = 0x0004;
        $ixfe    = 0x8000;
        $BuiltIn = 0x00;
        $iLevel  = 0xff;
        $header  = pack("vv", $record, $length);
        $data    = pack("vCC", $ixfe, $BuiltIn, $iLevel);
        $this->_append($header . $data);
    }
    private function _writeNumFormat($format, $ifmt)
    {
        $record             = 0x041E;
        $numberFormatString = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($format);
        $length             = 2 + strlen($numberFormatString);
        $header             = pack("vv", $record, $length);
        $data               = pack("v", $ifmt) . $numberFormatString;
        $this->_append($header . $data);
    }
    private function _writeDatemode()
    {
        $record = 0x0022;
        $length = 0x0002;
        $f1904  = (PHPExcel_Shared_Date::getExcelCalendar() == PHPExcel_Shared_Date::CALENDAR_MAC_1904) ? 1 : 0;
        $header = pack("vv", $record, $length);
        $data   = pack("v", $f1904);
        $this->_append($header . $data);
    }
    private function _writeExterncount($cxals)
    {
        $record = 0x0016;
        $length = 0x0002;
        $header = pack("vv", $record, $length);
        $data   = pack("v", $cxals);
        $this->_append($header . $data);
    }
    private function _writeExternsheet($sheetname)
    {
        $record = 0x0017;
        $length = 0x02 + strlen($sheetname);
        $cch    = strlen($sheetname);
        $rgch   = 0x03;
        $header = pack("vv", $record, $length);
        $data   = pack("CC", $cch, $rgch);
        $this->_append($header . $data . $sheetname);
    }
    private function _writeNameShort($index, $type, $rowmin, $rowmax, $colmin, $colmax)
    {
        $record         = 0x0018;
        $length         = 0x0024;
        $grbit          = 0x0020;
        $chKey          = 0x00;
        $cch            = 0x01;
        $cce            = 0x0015;
        $ixals          = $index + 1;
        $itab           = $ixals;
        $cchCustMenu    = 0x00;
        $cchDescription = 0x00;
        $cchHelptopic   = 0x00;
        $cchStatustext  = 0x00;
        $rgch           = $type;
        $unknown03      = 0x3b;
        $unknown04      = 0xffff - $index;
        $unknown05      = 0x0000;
        $unknown06      = 0x0000;
        $unknown07      = 0x1087;
        $unknown08      = 0x8005;
        $header         = pack("vv", $record, $length);
        $data           = pack("v", $grbit);
        $data .= pack("C", $chKey);
        $data .= pack("C", $cch);
        $data .= pack("v", $cce);
        $data .= pack("v", $ixals);
        $data .= pack("v", $itab);
        $data .= pack("C", $cchCustMenu);
        $data .= pack("C", $cchDescription);
        $data .= pack("C", $cchHelptopic);
        $data .= pack("C", $cchStatustext);
        $data .= pack("C", $rgch);
        $data .= pack("C", $unknown03);
        $data .= pack("v", $unknown04);
        $data .= pack("v", $unknown05);
        $data .= pack("v", $unknown06);
        $data .= pack("v", $unknown07);
        $data .= pack("v", $unknown08);
        $data .= pack("v", $index);
        $data .= pack("v", $index);
        $data .= pack("v", $rowmin);
        $data .= pack("v", $rowmax);
        $data .= pack("C", $colmin);
        $data .= pack("C", $colmax);
        $this->_append($header . $data);
    }
    private function _writeNameLong($index, $type, $rowmin, $rowmax, $colmin, $colmax)
    {
        $record         = 0x0018;
        $length         = 0x003d;
        $grbit          = 0x0020;
        $chKey          = 0x00;
        $cch            = 0x01;
        $cce            = 0x002e;
        $ixals          = $index + 1;
        $itab           = $ixals;
        $cchCustMenu    = 0x00;
        $cchDescription = 0x00;
        $cchHelptopic   = 0x00;
        $cchStatustext  = 0x00;
        $rgch           = $type;
        $unknown01      = 0x29;
        $unknown02      = 0x002b;
        $unknown03      = 0x3b;
        $unknown04      = 0xffff - $index;
        $unknown05      = 0x0000;
        $unknown06      = 0x0000;
        $unknown07      = 0x1087;
        $unknown08      = 0x8008;
        $header         = pack("vv", $record, $length);
        $data           = pack("v", $grbit);
        $data .= pack("C", $chKey);
        $data .= pack("C", $cch);
        $data .= pack("v", $cce);
        $data .= pack("v", $ixals);
        $data .= pack("v", $itab);
        $data .= pack("C", $cchCustMenu);
        $data .= pack("C", $cchDescription);
        $data .= pack("C", $cchHelptopic);
        $data .= pack("C", $cchStatustext);
        $data .= pack("C", $rgch);
        $data .= pack("C", $unknown01);
        $data .= pack("v", $unknown02);
        $data .= pack("C", $unknown03);
        $data .= pack("v", $unknown04);
        $data .= pack("v", $unknown05);
        $data .= pack("v", $unknown06);
        $data .= pack("v", $unknown07);
        $data .= pack("v", $unknown08);
        $data .= pack("v", $index);
        $data .= pack("v", $index);
        $data .= pack("v", 0x0000);
        $data .= pack("v", 0x3fff);
        $data .= pack("C", $colmin);
        $data .= pack("C", $colmax);
        $data .= pack("C", $unknown03);
        $data .= pack("v", $unknown04);
        $data .= pack("v", $unknown05);
        $data .= pack("v", $unknown06);
        $data .= pack("v", $unknown07);
        $data .= pack("v", $unknown08);
        $data .= pack("v", $index);
        $data .= pack("v", $index);
        $data .= pack("v", $rowmin);
        $data .= pack("v", $rowmax);
        $data .= pack("C", 0x00);
        $data .= pack("C", 0xff);
        $data .= pack("C", 0x10);
        $this->_append($header . $data);
    }
    private function _writeCountry()
    {
        $record = 0x008C;
        $length = 4;
        $header = pack('vv', $record, $length);
        $data   = pack('vv', $this->_country_code, $this->_country_code);
        return $this->writeData($header . $data);
    }
    private function _writeRecalcId()
    {
        $record = 0x01C1;
        $length = 8;
        $header = pack('vv', $record, $length);
        $data   = pack('VV', 0x000001C1, 0x00001E667);
        return $this->writeData($header . $data);
    }
    private function _writePalette()
    {
        $aref   = $this->_palette;
        $record = 0x0092;
        $length = 2 + 4 * count($aref);
        $ccv    = count($aref);
        $data   = '';
        foreach ($aref as $color) {
            foreach ($color as $byte) {
                $data .= pack("C", $byte);
            }
        }
        $header = pack("vvv", $record, $length, $ccv);
        $this->_append($header . $data);
    }
    private function _writeSharedStringsTable()
    {
        $continue_limit = 8224;
        $recordDatas    = array();
        $recordData     = pack("VV", $this->_str_total, $this->_str_unique);
        foreach (array_keys($this->_str_table) as $string) {
            $headerinfo = unpack("vlength/Cencoding", $string);
            $encoding   = $headerinfo["encoding"];
            $finished   = false;
            while ($finished === false) {
                if (strlen($recordData) + strlen($string) <= $continue_limit) {
                    $recordData .= $string;
                    if (strlen($recordData) + strlen($string) == $continue_limit) {
                        $recordDatas[] = $recordData;
                        $recordData    = '';
                    }
                    $finished = true;
                } else {
                    $space_remaining  = $continue_limit - strlen($recordData);
                    $min_space_needed = ($encoding == 1) ? 5 : 4;
                    if ($space_remaining < $min_space_needed) {
                        $recordDatas[] = $recordData;
                        $recordData    = '';
                    } else {
                        $effective_space_remaining = $space_remaining;
                        if ($encoding == 1 && (strlen($string) - $space_remaining) % 2 == 1) {
                            --$effective_space_remaining;
                        }
                        $recordData .= substr($string, 0, $effective_space_remaining);
                        $string        = substr($string, $effective_space_remaining);
                        $recordDatas[] = $recordData;
                        $recordData    = pack('C', $encoding);
                    }
                }
            }
        }
        if (strlen($recordData) > 0) {
            $recordDatas[] = $recordData;
        }
        $chunk = '';
        foreach ($recordDatas as $i => $recordData) {
            $record = ($i == 0) ? 0x00FC : 0x003C;
            $header = pack("vv", $record, strlen($recordData));
            $data   = $header . $recordData;
            $chunk .= $this->writeData($data);
        }
        return $chunk;
    }
    private function _writeMsoDrawingGroup()
    {
        if (isset($this->_escher)) {
            $writer = new PHPExcel_Writer_Excel5_Escher($this->_escher);
            $data   = $writer->close();
            $record = 0x00EB;
            $length = strlen($data);
            $header = pack("vv", $record, $length);
            return $this->writeData($header . $data);
        } else {
            return '';
        }
    }
    public function getEscher()
    {
        return $this->_escher;
    }
    public function setEscher(PHPExcel_Shared_Escher $pValue = null)
    {
        $this->_escher = $pValue;
    }
}