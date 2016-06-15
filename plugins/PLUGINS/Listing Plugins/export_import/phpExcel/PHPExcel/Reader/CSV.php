<?php
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}
class PHPExcel_Reader_CSV extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    private $_inputEncoding = 'UTF-8';
    private $_delimiter = ',';
    private $_enclosure = '"';
    private $_lineEnding = PHP_EOL;
    private $_sheetIndex = 0;
    private $_contiguous = false;
    private $_contiguousRow = -1;
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    public function canRead($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }
        return true;
    }
    public function setInputEncoding($pValue = 'UTF-8')
    {
        $this->_inputEncoding = $pValue;
        return $this;
    }
    public function getInputEncoding()
    {
        return $this->_inputEncoding;
    }
    public function listWorksheetInfo($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }
        $fileHandle = fopen($pFilename, 'r');
        if ($fileHandle === false) {
            throw new PHPExcel_Reader_Exception("Could not open file " . $pFilename . " for reading.");
        }
        switch ($this->_inputEncoding) {
            case 'UTF-8':
                fgets($fileHandle, 4) == "\xEF\xBB\xBF" ? fseek($fileHandle, 3) : fseek($fileHandle, 0);
                break;
            case 'UTF-16LE':
                fgets($fileHandle, 3) == "\xFF\xFE" ? fseek($fileHandle, 2) : fseek($fileHandle, 0);
                break;
            case 'UTF-16BE':
                fgets($fileHandle, 3) == "\xFE\xFF" ? fseek($fileHandle, 2) : fseek($fileHandle, 0);
                break;
            case 'UTF-32LE':
                fgets($fileHandle, 5) == "\xFF\xFE\x00\x00" ? fseek($fileHandle, 4) : fseek($fileHandle, 0);
                break;
            case 'UTF-32BE':
                fgets($fileHandle, 5) == "\x00\x00\xFE\xFF" ? fseek($fileHandle, 4) : fseek($fileHandle, 0);
                break;
            default:
                break;
        }
        $escapeEnclosures                     = array(
            "\\" . $this->_enclosure,
            $this->_enclosure . $this->_enclosure
        );
        $worksheetInfo                        = array();
        $worksheetInfo[0]['worksheetName']    = 'Worksheet';
        $worksheetInfo[0]['lastColumnLetter'] = 'A';
        $worksheetInfo[0]['lastColumnIndex']  = 0;
        $worksheetInfo[0]['totalRows']        = 0;
        $worksheetInfo[0]['totalColumns']     = 0;
        while (($rowData = fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== FALSE) {
            $worksheetInfo[0]['totalRows']++;
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
        }
        $worksheetInfo[0]['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex']);
        $worksheetInfo[0]['totalColumns']     = $worksheetInfo[0]['lastColumnIndex'] + 1;
        fclose($fileHandle);
        return $worksheetInfo;
    }
    public function load($pFilename)
    {
        $objPHPExcel = new PHPExcel();
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
    }
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }
        while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
            $objPHPExcel->createSheet();
        }
        $sheet      = $objPHPExcel->setActiveSheetIndex($this->_sheetIndex);
        $lineEnding = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', true);
        $fileHandle = fopen($pFilename, 'r');
        if ($fileHandle === false) {
            throw new PHPExcel_Reader_Exception("Could not open file $pFilename for reading.");
        }
        switch ($this->_inputEncoding) {
            case 'UTF-8':
                fgets($fileHandle, 4) == "\xEF\xBB\xBF" ? fseek($fileHandle, 3) : fseek($fileHandle, 0);
                break;
            case 'UTF-16LE':
                fgets($fileHandle, 3) == "\xFF\xFE" ? fseek($fileHandle, 2) : fseek($fileHandle, 0);
                break;
            case 'UTF-16BE':
                fgets($fileHandle, 3) == "\xFE\xFF" ? fseek($fileHandle, 2) : fseek($fileHandle, 0);
                break;
            case 'UTF-32LE':
                fgets($fileHandle, 5) == "\xFF\xFE\x00\x00" ? fseek($fileHandle, 4) : fseek($fileHandle, 0);
                break;
            case 'UTF-32BE':
                fgets($fileHandle, 5) == "\x00\x00\xFE\xFF" ? fseek($fileHandle, 4) : fseek($fileHandle, 0);
                break;
            default:
                break;
        }
        $escapeEnclosures = array(
            "\\" . $this->_enclosure,
            $this->_enclosure . $this->_enclosure
        );
        $currentRow       = 1;
        if ($this->_contiguous) {
            $currentRow = ($this->_contiguousRow == -1) ? $sheet->getHighestRow() : $this->_contiguousRow;
        }
        while (($rowData = fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== FALSE) {
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                if ($rowDatum != '' && $this->_readFilter->readCell($columnLetter, $currentRow)) {
                    $rowDatum = str_replace($escapeEnclosures, $this->_enclosure, $rowDatum);
                    if ($this->_inputEncoding !== 'UTF-8') {
                        $rowDatum = PHPExcel_Shared_String::ConvertEncoding($rowDatum, 'UTF-8', $this->_inputEncoding);
                    }
                    $sheet->getCell($columnLetter . $currentRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            ++$currentRow;
        }
        fclose($fileHandle);
        if ($this->_contiguous) {
            $this->_contiguousRow = $currentRow;
        }
        ini_set('auto_detect_line_endings', $lineEnding);
        return $objPHPExcel;
    }
    public function getDelimiter()
    {
        return $this->_delimiter;
    }
    public function setDelimiter($pValue = ',')
    {
        $this->_delimiter = $pValue;
        return $this;
    }
    public function getEnclosure()
    {
        return $this->_enclosure;
    }
    public function setEnclosure($pValue = '"')
    {
        if ($pValue == '') {
            $pValue = '"';
        }
        $this->_enclosure = $pValue;
        return $this;
    }
    public function getLineEnding()
    {
        return $this->_lineEnding;
    }
    public function setLineEnding($pValue = PHP_EOL)
    {
        $this->_lineEnding = $pValue;
        return $this;
    }
    public function getSheetIndex()
    {
        return $this->_sheetIndex;
    }
    public function setSheetIndex($pValue = 0)
    {
        $this->_sheetIndex = $pValue;
        return $this;
    }
    public function setContiguous($contiguous = false)
    {
        $this->_contiguous = (bool) $contiguous;
        if (!$contiguous) {
            $this->_contiguousRow = -1;
        }
        return $this;
    }
    public function getContiguous()
    {
        return $this->_contiguous;
    }
}