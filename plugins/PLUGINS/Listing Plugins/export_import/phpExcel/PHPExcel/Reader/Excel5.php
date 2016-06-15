<?php
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}
class PHPExcel_Reader_Excel5 extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    const XLS_BIFF8 = 0x0600;
    const XLS_BIFF7 = 0x0500;
    const XLS_WorkbookGlobals = 0x0005;
    const XLS_Worksheet = 0x0010;
    const XLS_Type_FORMULA = 0x0006;
    const XLS_Type_EOF = 0x000a;
    const XLS_Type_PROTECT = 0x0012;
    const XLS_Type_OBJECTPROTECT = 0x0063;
    const XLS_Type_SCENPROTECT = 0x00dd;
    const XLS_Type_PASSWORD = 0x0013;
    const XLS_Type_HEADER = 0x0014;
    const XLS_Type_FOOTER = 0x0015;
    const XLS_Type_EXTERNSHEET = 0x0017;
    const XLS_Type_DEFINEDNAME = 0x0018;
    const XLS_Type_VERTICALPAGEBREAKS = 0x001a;
    const XLS_Type_HORIZONTALPAGEBREAKS = 0x001b;
    const XLS_Type_NOTE = 0x001c;
    const XLS_Type_SELECTION = 0x001d;
    const XLS_Type_DATEMODE = 0x0022;
    const XLS_Type_EXTERNNAME = 0x0023;
    const XLS_Type_LEFTMARGIN = 0x0026;
    const XLS_Type_RIGHTMARGIN = 0x0027;
    const XLS_Type_TOPMARGIN = 0x0028;
    const XLS_Type_BOTTOMMARGIN = 0x0029;
    const XLS_Type_PRINTGRIDLINES = 0x002b;
    const XLS_Type_FILEPASS = 0x002f;
    const XLS_Type_FONT = 0x0031;
    const XLS_Type_CONTINUE = 0x003c;
    const XLS_Type_PANE = 0x0041;
    const XLS_Type_CODEPAGE = 0x0042;
    const XLS_Type_DEFCOLWIDTH = 0x0055;
    const XLS_Type_OBJ = 0x005d;
    const XLS_Type_COLINFO = 0x007d;
    const XLS_Type_IMDATA = 0x007f;
    const XLS_Type_SHEETPR = 0x0081;
    const XLS_Type_HCENTER = 0x0083;
    const XLS_Type_VCENTER = 0x0084;
    const XLS_Type_SHEET = 0x0085;
    const XLS_Type_PALETTE = 0x0092;
    const XLS_Type_SCL = 0x00a0;
    const XLS_Type_PAGESETUP = 0x00a1;
    const XLS_Type_MULRK = 0x00bd;
    const XLS_Type_MULBLANK = 0x00be;
    const XLS_Type_DBCELL = 0x00d7;
    const XLS_Type_XF = 0x00e0;
    const XLS_Type_MERGEDCELLS = 0x00e5;
    const XLS_Type_MSODRAWINGGROUP = 0x00eb;
    const XLS_Type_MSODRAWING = 0x00ec;
    const XLS_Type_SST = 0x00fc;
    const XLS_Type_LABELSST = 0x00fd;
    const XLS_Type_EXTSST = 0x00ff;
    const XLS_Type_EXTERNALBOOK = 0x01ae;
    const XLS_Type_DATAVALIDATIONS = 0x01b2;
    const XLS_Type_TXO = 0x01b6;
    const XLS_Type_HYPERLINK = 0x01b8;
    const XLS_Type_DATAVALIDATION = 0x01be;
    const XLS_Type_DIMENSION = 0x0200;
    const XLS_Type_BLANK = 0x0201;
    const XLS_Type_NUMBER = 0x0203;
    const XLS_Type_LABEL = 0x0204;
    const XLS_Type_BOOLERR = 0x0205;
    const XLS_Type_STRING = 0x0207;
    const XLS_Type_ROW = 0x0208;
    const XLS_Type_INDEX = 0x020b;
    const XLS_Type_ARRAY = 0x0221;
    const XLS_Type_DEFAULTROWHEIGHT = 0x0225;
    const XLS_Type_WINDOW2 = 0x023e;
    const XLS_Type_RK = 0x027e;
    const XLS_Type_STYLE = 0x0293;
    const XLS_Type_FORMAT = 0x041e;
    const XLS_Type_SHAREDFMLA = 0x04bc;
    const XLS_Type_BOF = 0x0809;
    const XLS_Type_SHEETPROTECTION = 0x0867;
    const XLS_Type_RANGEPROTECTION = 0x0868;
    const XLS_Type_SHEETLAYOUT = 0x0862;
    const XLS_Type_XFEXT = 0x087d;
    const XLS_Type_PAGELAYOUTVIEW = 0x088b;
    const XLS_Type_UNKNOWN = 0xffff;
    private $_summaryInformation;
    private $_documentSummaryInformation;
    private $_userDefinedProperties;
    private $_data;
    private $_dataSize;
    private $_pos;
    private $_phpExcel;
    private $_phpSheet;
    private $_version;
    private $_codepage;
    private $_formats;
    private $_objFonts;
    private $_palette;
    private $_sheets;
    private $_externalBooks;
    private $_ref;
    private $_externalNames;
    private $_definedname;
    private $_sst;
    private $_frozen;
    private $_isFitToPages;
    private $_objs;
    private $_textObjects;
    private $_cellNotes;
    private $_drawingGroupData;
    private $_drawingData;
    private $_xfIndex;
    private $_mapCellXfIndex;
    private $_mapCellStyleXfIndex;
    private $_sharedFormulas;
    private $_sharedFormulaParts;
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    public function canRead($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }
        try {
            $ole = new PHPExcel_Shared_OLERead();
            $res = $ole->read($pFilename);
            return true;
        }
        catch (PHPExcel_Exception $e) {
            return false;
        }
    }
    public function listWorksheetNames($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }
        $worksheetNames = array();
        $this->_loadOLE($pFilename);
        $this->_dataSize = strlen($this->_data);
        $this->_pos      = 0;
        $this->_sheets   = array();
        while ($this->_pos < $this->_dataSize) {
            $code = self::_GetInt2d($this->_data, $this->_pos);
            switch ($code) {
                case self::XLS_Type_BOF:
                    $this->_readBof();
                    break;
                case self::XLS_Type_SHEET:
                    $this->_readSheet();
                    break;
                case self::XLS_Type_EOF:
                    $this->_readDefault();
                    break 2;
                default:
                    $this->_readDefault();
                    break;
            }
        }
        foreach ($this->_sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                continue;
            }
            $worksheetNames[] = $sheet['name'];
        }
        return $worksheetNames;
    }
    public function listWorksheetInfo($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }
        $worksheetInfo = array();
        $this->_loadOLE($pFilename);
        $this->_dataSize = strlen($this->_data);
        $this->_pos      = 0;
        $this->_sheets   = array();
        while ($this->_pos < $this->_dataSize) {
            $code = self::_GetInt2d($this->_data, $this->_pos);
            switch ($code) {
                case self::XLS_Type_BOF:
                    $this->_readBof();
                    break;
                case self::XLS_Type_SHEET:
                    $this->_readSheet();
                    break;
                case self::XLS_Type_EOF:
                    $this->_readDefault();
                    break 2;
                default:
                    $this->_readDefault();
                    break;
            }
        }
        foreach ($this->_sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                continue;
            }
            $tmpInfo                     = array();
            $tmpInfo['worksheetName']    = $sheet['name'];
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex']  = 0;
            $tmpInfo['totalRows']        = 0;
            $tmpInfo['totalColumns']     = 0;
            $this->_pos                  = $sheet['offset'];
            while ($this->_pos <= $this->_dataSize - 4) {
                $code = self::_GetInt2d($this->_data, $this->_pos);
                switch ($code) {
                    case self::XLS_Type_RK:
                    case self::XLS_Type_LABELSST:
                    case self::XLS_Type_NUMBER:
                    case self::XLS_Type_FORMULA:
                    case self::XLS_Type_BOOLERR:
                    case self::XLS_Type_LABEL:
                        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
                        $recordData = substr($this->_data, $this->_pos + 4, $length);
                        $this->_pos += 4 + $length;
                        $rowIndex                   = self::_GetInt2d($recordData, 0) + 1;
                        $columnIndex                = self::_GetInt2d($recordData, 2);
                        $tmpInfo['totalRows']       = max($tmpInfo['totalRows'], $rowIndex);
                        $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
                        break;
                    case self::XLS_Type_BOF:
                        $this->_readBof();
                        break;
                    case self::XLS_Type_EOF:
                        $this->_readDefault();
                        break 2;
                    default:
                        $this->_readDefault();
                        break;
                }
            }
            $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
            $tmpInfo['totalColumns']     = $tmpInfo['lastColumnIndex'] + 1;
            $worksheetInfo[]             = $tmpInfo;
        }
        return $worksheetInfo;
    }
    public function load($pFilename)
    {
        $this->_loadOLE($pFilename);
        $this->_phpExcel = new PHPExcel;
        $this->_phpExcel->removeSheetByIndex(0);
        if (!$this->_readDataOnly) {
            $this->_phpExcel->removeCellStyleXfByIndex(0);
            $this->_phpExcel->removeCellXfByIndex(0);
        }
        $this->_readSummaryInformation();
        $this->_readDocumentSummaryInformation();
        $this->_dataSize            = strlen($this->_data);
        $this->_pos                 = 0;
        $this->_codepage            = 'CP1252';
        $this->_formats             = array();
        $this->_objFonts            = array();
        $this->_palette             = array();
        $this->_sheets              = array();
        $this->_externalBooks       = array();
        $this->_ref                 = array();
        $this->_definedname         = array();
        $this->_sst                 = array();
        $this->_drawingGroupData    = '';
        $this->_xfIndex             = '';
        $this->_mapCellXfIndex      = array();
        $this->_mapCellStyleXfIndex = array();
        while ($this->_pos < $this->_dataSize) {
            $code = self::_GetInt2d($this->_data, $this->_pos);
            switch ($code) {
                case self::XLS_Type_BOF:
                    $this->_readBof();
                    break;
                case self::XLS_Type_FILEPASS:
                    $this->_readFilepass();
                    break;
                case self::XLS_Type_CODEPAGE:
                    $this->_readCodepage();
                    break;
                case self::XLS_Type_DATEMODE:
                    $this->_readDateMode();
                    break;
                case self::XLS_Type_FONT:
                    $this->_readFont();
                    break;
                case self::XLS_Type_FORMAT:
                    $this->_readFormat();
                    break;
                case self::XLS_Type_XF:
                    $this->_readXf();
                    break;
                case self::XLS_Type_XFEXT:
                    $this->_readXfExt();
                    break;
                case self::XLS_Type_STYLE:
                    $this->_readStyle();
                    break;
                case self::XLS_Type_PALETTE:
                    $this->_readPalette();
                    break;
                case self::XLS_Type_SHEET:
                    $this->_readSheet();
                    break;
                case self::XLS_Type_EXTERNALBOOK:
                    $this->_readExternalBook();
                    break;
                case self::XLS_Type_EXTERNNAME:
                    $this->_readExternName();
                    break;
                case self::XLS_Type_EXTERNSHEET:
                    $this->_readExternSheet();
                    break;
                case self::XLS_Type_DEFINEDNAME:
                    $this->_readDefinedName();
                    break;
                case self::XLS_Type_MSODRAWINGGROUP:
                    $this->_readMsoDrawingGroup();
                    break;
                case self::XLS_Type_SST:
                    $this->_readSst();
                    break;
                case self::XLS_Type_EOF:
                    $this->_readDefault();
                    break 2;
                default:
                    $this->_readDefault();
                    break;
            }
        }
        if (!$this->_readDataOnly) {
            foreach ($this->_objFonts as $objFont) {
                if (isset($objFont->colorIndex)) {
                    $color = self::_readColor($objFont->colorIndex, $this->_palette, $this->_version);
                    $objFont->getColor()->setRGB($color['rgb']);
                }
            }
            foreach ($this->_phpExcel->getCellXfCollection() as $objStyle) {
                $fill = $objStyle->getFill();
                if (isset($fill->startcolorIndex)) {
                    $startColor = self::_readColor($fill->startcolorIndex, $this->_palette, $this->_version);
                    $fill->getStartColor()->setRGB($startColor['rgb']);
                }
                if (isset($fill->endcolorIndex)) {
                    $endColor = self::_readColor($fill->endcolorIndex, $this->_palette, $this->_version);
                    $fill->getEndColor()->setRGB($endColor['rgb']);
                }
                $top      = $objStyle->getBorders()->getTop();
                $right    = $objStyle->getBorders()->getRight();
                $bottom   = $objStyle->getBorders()->getBottom();
                $left     = $objStyle->getBorders()->getLeft();
                $diagonal = $objStyle->getBorders()->getDiagonal();
                if (isset($top->colorIndex)) {
                    $borderTopColor = self::_readColor($top->colorIndex, $this->_palette, $this->_version);
                    $top->getColor()->setRGB($borderTopColor['rgb']);
                }
                if (isset($right->colorIndex)) {
                    $borderRightColor = self::_readColor($right->colorIndex, $this->_palette, $this->_version);
                    $right->getColor()->setRGB($borderRightColor['rgb']);
                }
                if (isset($bottom->colorIndex)) {
                    $borderBottomColor = self::_readColor($bottom->colorIndex, $this->_palette, $this->_version);
                    $bottom->getColor()->setRGB($borderBottomColor['rgb']);
                }
                if (isset($left->colorIndex)) {
                    $borderLeftColor = self::_readColor($left->colorIndex, $this->_palette, $this->_version);
                    $left->getColor()->setRGB($borderLeftColor['rgb']);
                }
                if (isset($diagonal->colorIndex)) {
                    $borderDiagonalColor = self::_readColor($diagonal->colorIndex, $this->_palette, $this->_version);
                    $diagonal->getColor()->setRGB($borderDiagonalColor['rgb']);
                }
            }
        }
        if (!$this->_readDataOnly && $this->_drawingGroupData) {
            $escherWorkbook = new PHPExcel_Shared_Escher();
            $reader         = new PHPExcel_Reader_Excel5_Escher($escherWorkbook);
            $escherWorkbook = $reader->load($this->_drawingGroupData);
        }
        foreach ($this->_sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                continue;
            }
            if (isset($this->_loadSheetsOnly) && !in_array($sheet['name'], $this->_loadSheetsOnly)) {
                continue;
            }
            $this->_phpSheet = $this->_phpExcel->createSheet();
            $this->_phpSheet->setTitle($sheet['name'], false);
            $this->_phpSheet->setSheetState($sheet['sheetState']);
            $this->_pos                = $sheet['offset'];
            $this->_isFitToPages       = false;
            $this->_drawingData        = '';
            $this->_objs               = array();
            $this->_sharedFormulaParts = array();
            $this->_sharedFormulas     = array();
            $this->_textObjects        = array();
            $this->_cellNotes          = array();
            $this->textObjRef          = -1;
            while ($this->_pos <= $this->_dataSize - 4) {
                $code = self::_GetInt2d($this->_data, $this->_pos);
                switch ($code) {
                    case self::XLS_Type_BOF:
                        $this->_readBof();
                        break;
                    case self::XLS_Type_PRINTGRIDLINES:
                        $this->_readPrintGridlines();
                        break;
                    case self::XLS_Type_DEFAULTROWHEIGHT:
                        $this->_readDefaultRowHeight();
                        break;
                    case self::XLS_Type_SHEETPR:
                        $this->_readSheetPr();
                        break;
                    case self::XLS_Type_HORIZONTALPAGEBREAKS:
                        $this->_readHorizontalPageBreaks();
                        break;
                    case self::XLS_Type_VERTICALPAGEBREAKS:
                        $this->_readVerticalPageBreaks();
                        break;
                    case self::XLS_Type_HEADER:
                        $this->_readHeader();
                        break;
                    case self::XLS_Type_FOOTER:
                        $this->_readFooter();
                        break;
                    case self::XLS_Type_HCENTER:
                        $this->_readHcenter();
                        break;
                    case self::XLS_Type_VCENTER:
                        $this->_readVcenter();
                        break;
                    case self::XLS_Type_LEFTMARGIN:
                        $this->_readLeftMargin();
                        break;
                    case self::XLS_Type_RIGHTMARGIN:
                        $this->_readRightMargin();
                        break;
                    case self::XLS_Type_TOPMARGIN:
                        $this->_readTopMargin();
                        break;
                    case self::XLS_Type_BOTTOMMARGIN:
                        $this->_readBottomMargin();
                        break;
                    case self::XLS_Type_PAGESETUP:
                        $this->_readPageSetup();
                        break;
                    case self::XLS_Type_PROTECT:
                        $this->_readProtect();
                        break;
                    case self::XLS_Type_SCENPROTECT:
                        $this->_readScenProtect();
                        break;
                    case self::XLS_Type_OBJECTPROTECT:
                        $this->_readObjectProtect();
                        break;
                    case self::XLS_Type_PASSWORD:
                        $this->_readPassword();
                        break;
                    case self::XLS_Type_DEFCOLWIDTH:
                        $this->_readDefColWidth();
                        break;
                    case self::XLS_Type_COLINFO:
                        $this->_readColInfo();
                        break;
                    case self::XLS_Type_DIMENSION:
                        $this->_readDefault();
                        break;
                    case self::XLS_Type_ROW:
                        $this->_readRow();
                        break;
                    case self::XLS_Type_DBCELL:
                        $this->_readDefault();
                        break;
                    case self::XLS_Type_RK:
                        $this->_readRk();
                        break;
                    case self::XLS_Type_LABELSST:
                        $this->_readLabelSst();
                        break;
                    case self::XLS_Type_MULRK:
                        $this->_readMulRk();
                        break;
                    case self::XLS_Type_NUMBER:
                        $this->_readNumber();
                        break;
                    case self::XLS_Type_FORMULA:
                        $this->_readFormula();
                        break;
                    case self::XLS_Type_SHAREDFMLA:
                        $this->_readSharedFmla();
                        break;
                    case self::XLS_Type_BOOLERR:
                        $this->_readBoolErr();
                        break;
                    case self::XLS_Type_MULBLANK:
                        $this->_readMulBlank();
                        break;
                    case self::XLS_Type_LABEL:
                        $this->_readLabel();
                        break;
                    case self::XLS_Type_BLANK:
                        $this->_readBlank();
                        break;
                    case self::XLS_Type_MSODRAWING:
                        $this->_readMsoDrawing();
                        break;
                    case self::XLS_Type_OBJ:
                        $this->_readObj();
                        break;
                    case self::XLS_Type_WINDOW2:
                        $this->_readWindow2();
                        break;
                    case self::XLS_Type_PAGELAYOUTVIEW:
                        $this->_readPageLayoutView();
                        break;
                    case self::XLS_Type_SCL:
                        $this->_readScl();
                        break;
                    case self::XLS_Type_PANE:
                        $this->_readPane();
                        break;
                    case self::XLS_Type_SELECTION:
                        $this->_readSelection();
                        break;
                    case self::XLS_Type_MERGEDCELLS:
                        $this->_readMergedCells();
                        break;
                    case self::XLS_Type_HYPERLINK:
                        $this->_readHyperLink();
                        break;
                    case self::XLS_Type_DATAVALIDATIONS:
                        $this->_readDataValidations();
                        break;
                    case self::XLS_Type_DATAVALIDATION:
                        $this->_readDataValidation();
                        break;
                    case self::XLS_Type_SHEETLAYOUT:
                        $this->_readSheetLayout();
                        break;
                    case self::XLS_Type_SHEETPROTECTION:
                        $this->_readSheetProtection();
                        break;
                    case self::XLS_Type_RANGEPROTECTION:
                        $this->_readRangeProtection();
                        break;
                    case self::XLS_Type_NOTE:
                        $this->_readNote();
                        break;
                    case self::XLS_Type_TXO:
                        $this->_readTextObject();
                        break;
                    case self::XLS_Type_CONTINUE:
                        $this->_readContinue();
                        break;
                    case self::XLS_Type_EOF:
                        $this->_readDefault();
                        break 2;
                    default:
                        $this->_readDefault();
                        break;
                }
            }
            if (!$this->_readDataOnly && $this->_drawingData) {
                $escherWorksheet = new PHPExcel_Shared_Escher();
                $reader          = new PHPExcel_Reader_Excel5_Escher($escherWorksheet);
                $escherWorksheet = $reader->load($this->_drawingData);
                $allSpContainers = $escherWorksheet->getDgContainer()->getSpgrContainer()->getAllSpContainers();
            }
            foreach ($this->_objs as $n => $obj) {
                if (isset($allSpContainers[$n + 1]) && is_object($allSpContainers[$n + 1])) {
                    $spContainer = $allSpContainers[$n + 1];
                    if ($spContainer->getNestingLevel() > 1) {
                        continue;
                    }
                    list($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($spContainer->getStartCoordinates());
                    list($endColumn, $endRow) = PHPExcel_Cell::coordinateFromString($spContainer->getEndCoordinates());
                    $startOffsetX = $spContainer->getStartOffsetX();
                    $startOffsetY = $spContainer->getStartOffsetY();
                    $endOffsetX   = $spContainer->getEndOffsetX();
                    $endOffsetY   = $spContainer->getEndOffsetY();
                    $width        = PHPExcel_Shared_Excel5::getDistanceX($this->_phpSheet, $startColumn, $startOffsetX, $endColumn, $endOffsetX);
                    $height       = PHPExcel_Shared_Excel5::getDistanceY($this->_phpSheet, $startRow, $startOffsetY, $endRow, $endOffsetY);
                    $offsetX      = $startOffsetX * PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, $startColumn) / 1024;
                    $offsetY      = $startOffsetY * PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $startRow) / 256;
                    switch ($obj['otObjType']) {
                        case 0x19:
                            if (isset($this->_cellNotes[$obj['idObjID']])) {
                                $cellNote = $this->_cellNotes[$obj['idObjID']];
                                if (isset($this->_textObjects[$obj['idObjID']])) {
                                    $textObject                                       = $this->_textObjects[$obj['idObjID']];
                                    $this->_cellNotes[$obj['idObjID']]['objTextData'] = $textObject;
                                }
                            }
                            break;
                        case 0x08:
                            $BSEindex      = $spContainer->getOPT(0x0104);
                            $BSECollection = $escherWorkbook->getDggContainer()->getBstoreContainer()->getBSECollection();
                            $BSE           = $BSECollection[$BSEindex - 1];
                            $blipType      = $BSE->getBlipType();
                            if ($blip = $BSE->getBlip()) {
                                $ih      = imagecreatefromstring($blip->getData());
                                $drawing = new PHPExcel_Worksheet_MemoryDrawing();
                                $drawing->setImageResource($ih);
                                $drawing->setResizeProportional(false);
                                $drawing->setWidth($width);
                                $drawing->setHeight($height);
                                $drawing->setOffsetX($offsetX);
                                $drawing->setOffsetY($offsetY);
                                switch ($blipType) {
                                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG:
                                        $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                                        $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG);
                                        break;
                                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG:
                                        $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                                        $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
                                        break;
                                }
                                $drawing->setWorksheet($this->_phpSheet);
                                $drawing->setCoordinates($spContainer->getStartCoordinates());
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
            if ($this->_version == self::XLS_BIFF8) {
                foreach ($this->_sharedFormulaParts as $cell => $baseCell) {
                    list($column, $row) = PHPExcel_Cell::coordinateFromString($cell);
                    if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($column, $row, $this->_phpSheet->getTitle())) {
                        $formula = $this->_getFormulaFromStructure($this->_sharedFormulas[$baseCell], $cell);
                        $this->_phpSheet->getCell($cell)->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
                    }
                }
            }
            if (!empty($this->_cellNotes)) {
                foreach ($this->_cellNotes as $note => $noteDetails) {
                    if (!isset($noteDetails['objTextData'])) {
                        if (isset($this->_textObjects[$note])) {
                            $textObject                 = $this->_textObjects[$note];
                            $noteDetails['objTextData'] = $textObject;
                        } else {
                            $noteDetails['objTextData']['text'] = '';
                        }
                    }
                    $cellAddress = str_replace('$', '', $noteDetails['cellRef']);
                    $this->_phpSheet->getComment($cellAddress)->setAuthor($noteDetails['author'])->setText($this->_parseRichText($noteDetails['objTextData']['text']));
                }
            }
        }
        foreach ($this->_definedname as $definedName) {
            if ($definedName['isBuiltInName']) {
                switch ($definedName['name']) {
                    case pack('C', 0x06):
                        $ranges          = explode(',', $definedName['formula']);
                        $extractedRanges = array();
                        foreach ($ranges as $range) {
                            $explodes  = explode('!', $range);
                            $sheetName = trim($explodes[0], "'");
                            if (count($explodes) == 2) {
                                if (strpos($explodes[1], ':') === FALSE) {
                                    $explodes[1] = $explodes[1] . ':' . $explodes[1];
                                }
                                $extractedRanges[] = str_replace('$', '', $explodes[1]);
                            }
                        }
                        if ($docSheet = $this->_phpExcel->getSheetByName($sheetName)) {
                            $docSheet->getPageSetup()->setPrintArea(implode(',', $extractedRanges));
                        }
                        break;
                    case pack('C', 0x07):
                        $ranges = explode(',', $definedName['formula']);
                        foreach ($ranges as $range) {
                            $explodes = explode('!', $range);
                            if (count($explodes) == 2) {
                                if ($docSheet = $this->_phpExcel->getSheetByName($explodes[0])) {
                                    $extractedRange    = $explodes[1];
                                    $extractedRange    = str_replace('$', '', $extractedRange);
                                    $coordinateStrings = explode(':', $extractedRange);
                                    if (count($coordinateStrings) == 2) {
                                        list($firstColumn, $firstRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[0]);
                                        list($lastColumn, $lastRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[1]);
                                        if ($firstColumn == 'A' and $lastColumn == 'IV') {
                                            $docSheet->getPageSetup()->setRowsToRepeatAtTop(array(
                                                $firstRow,
                                                $lastRow
                                            ));
                                        } elseif ($firstRow == 1 and $lastRow == 65536) {
                                            $docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array(
                                                $firstColumn,
                                                $lastColumn
                                            ));
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
            } else {
                $explodes = explode('!', $definedName['formula']);
                if (count($explodes) == 2) {
                    if (($docSheet = $this->_phpExcel->getSheetByName($explodes[0])) || ($docSheet = $this->_phpExcel->getSheetByName(trim($explodes[0], "'")))) {
                        $extractedRange = $explodes[1];
                        $extractedRange = str_replace('$', '', $extractedRange);
                        $localOnly      = ($definedName['scope'] == 0) ? false : true;
                        $scope          = ($definedName['scope'] == 0) ? null : $this->_phpExcel->getSheetByName($this->_sheets[$definedName['scope'] - 1]['name']);
                        $this->_phpExcel->addNamedRange(new PHPExcel_NamedRange((string) $definedName['name'], $docSheet, $extractedRange, $localOnly, $scope));
                    }
                } else {
                }
            }
        }
        return $this->_phpExcel;
    }
    private function _loadOLE($pFilename)
    {
        $ole                               = new PHPExcel_Shared_OLERead();
        $res                               = $ole->read($pFilename);
        $this->_data                       = $ole->getStream($ole->wrkbook);
        $this->_summaryInformation         = $ole->getStream($ole->summaryInformation);
        $this->_documentSummaryInformation = $ole->getStream($ole->documentSummaryInformation);
    }
    private function _readSummaryInformation()
    {
        if (!isset($this->_summaryInformation)) {
            return;
        }
        $secCount        = self::_GetInt4d($this->_summaryInformation, 24);
        $secOffset       = self::_GetInt4d($this->_summaryInformation, 44);
        $secLength       = self::_GetInt4d($this->_summaryInformation, $secOffset);
        $countProperties = self::_GetInt4d($this->_summaryInformation, $secOffset + 4);
        $codePage        = 'CP1252';
        for ($i = 0; $i < $countProperties; ++$i) {
            $id     = self::_GetInt4d($this->_summaryInformation, ($secOffset + 8) + (8 * $i));
            $offset = self::_GetInt4d($this->_summaryInformation, ($secOffset + 12) + (8 * $i));
            $type   = self::_GetInt4d($this->_summaryInformation, $secOffset + $offset);
            $value  = null;
            switch ($type) {
                case 0x02:
                    $value = self::_GetInt2d($this->_summaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x03:
                    $value = self::_GetInt4d($this->_summaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x13:
                    break;
                case 0x1E:
                    $byteLength = self::_GetInt4d($this->_summaryInformation, $secOffset + 4 + $offset);
                    $value      = substr($this->_summaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value      = PHPExcel_Shared_String::ConvertEncoding($value, 'UTF-8', $codePage);
                    $value      = rtrim($value);
                    break;
                case 0x40:
                    $value = PHPExcel_Shared_OLE::OLE2LocalDate(substr($this->_summaryInformation, $secOffset + 4 + $offset, 8));
                    break;
                case 0x47:
                    break;
            }
            switch ($id) {
                case 0x01:
                    $codePage = PHPExcel_Shared_CodePage::NumberToName($value);
                    break;
                case 0x02:
                    $this->_phpExcel->getProperties()->setTitle($value);
                    break;
                case 0x03:
                    $this->_phpExcel->getProperties()->setSubject($value);
                    break;
                case 0x04:
                    $this->_phpExcel->getProperties()->setCreator($value);
                    break;
                case 0x05:
                    $this->_phpExcel->getProperties()->setKeywords($value);
                    break;
                case 0x06:
                    $this->_phpExcel->getProperties()->setDescription($value);
                    break;
                case 0x07:
                    break;
                case 0x08:
                    $this->_phpExcel->getProperties()->setLastModifiedBy($value);
                    break;
                case 0x09:
                    break;
                case 0x0A:
                    break;
                case 0x0B:
                    break;
                case 0x0C:
                    $this->_phpExcel->getProperties()->setCreated($value);
                    break;
                case 0x0D:
                    $this->_phpExcel->getProperties()->setModified($value);
                    break;
                case 0x0E:
                    break;
                case 0x0F:
                    break;
                case 0x10:
                    break;
                case 0x11:
                    break;
                case 0x12:
                    break;
                case 0x13:
                    break;
            }
        }
    }
    private function _readDocumentSummaryInformation()
    {
        if (!isset($this->_documentSummaryInformation)) {
            return;
        }
        $secCount        = self::_GetInt4d($this->_documentSummaryInformation, 24);
        $secOffset       = self::_GetInt4d($this->_documentSummaryInformation, 44);
        $secLength       = self::_GetInt4d($this->_documentSummaryInformation, $secOffset);
        $countProperties = self::_GetInt4d($this->_documentSummaryInformation, $secOffset + 4);
        $codePage        = 'CP1252';
        for ($i = 0; $i < $countProperties; ++$i) {
            $id     = self::_GetInt4d($this->_documentSummaryInformation, ($secOffset + 8) + (8 * $i));
            $offset = self::_GetInt4d($this->_documentSummaryInformation, ($secOffset + 12) + (8 * $i));
            $type   = self::_GetInt4d($this->_documentSummaryInformation, $secOffset + $offset);
            $value  = null;
            switch ($type) {
                case 0x02:
                    $value = self::_GetInt2d($this->_documentSummaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x03:
                    $value = self::_GetInt4d($this->_documentSummaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x0B:
                    $value = self::_GetInt2d($this->_documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = ($value == 0 ? false : true);
                    break;
                case 0x13:
                    break;
                case 0x1E:
                    $byteLength = self::_GetInt4d($this->_documentSummaryInformation, $secOffset + 4 + $offset);
                    $value      = substr($this->_documentSummaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value      = PHPExcel_Shared_String::ConvertEncoding($value, 'UTF-8', $codePage);
                    $value      = rtrim($value);
                    break;
                case 0x40:
                    $value = PHPExcel_Shared_OLE::OLE2LocalDate(substr($this->_documentSummaryInformation, $secOffset + 4 + $offset, 8));
                    break;
                case 0x47:
                    break;
            }
            switch ($id) {
                case 0x01:
                    $codePage = PHPExcel_Shared_CodePage::NumberToName($value);
                    break;
                case 0x02:
                    $this->_phpExcel->getProperties()->setCategory($value);
                    break;
                case 0x03:
                    break;
                case 0x04:
                    break;
                case 0x05:
                    break;
                case 0x06:
                    break;
                case 0x07:
                    break;
                case 0x08:
                    break;
                case 0x09:
                    break;
                case 0x0A:
                    break;
                case 0x0B:
                    break;
                case 0x0C:
                    break;
                case 0x0D:
                    break;
                case 0x0E:
                    $this->_phpExcel->getProperties()->setManager($value);
                    break;
                case 0x0F:
                    $this->_phpExcel->getProperties()->setCompany($value);
                    break;
                case 0x10:
                    break;
            }
        }
    }
    private function _readDefault()
    {
        $length = self::_GetInt2d($this->_data, $this->_pos + 2);
        $this->_pos += 4 + $length;
    }
    private function _readNote()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $cellAddress = $this->_readBIFF8CellAddress(substr($recordData, 0, 4));
        if ($this->_version == self::XLS_BIFF8) {
            $noteObjID                    = self::_GetInt2d($recordData, 6);
            $noteAuthor                   = self::_readUnicodeStringLong(substr($recordData, 8));
            $noteAuthor                   = $noteAuthor['value'];
            $this->_cellNotes[$noteObjID] = array(
                'cellRef' => $cellAddress,
                'objectID' => $noteObjID,
                'author' => $noteAuthor
            );
        } else {
            $extension = false;
            if ($cellAddress == '$B$65536') {
                $row         = self::_GetInt2d($recordData, 0);
                $extension   = true;
                $cellAddress = array_pop(array_keys($this->_phpSheet->getComments()));
            }
            $cellAddress = str_replace('$', '', $cellAddress);
            $noteLength  = self::_GetInt2d($recordData, 4);
            $noteText    = trim(substr($recordData, 6));
            if ($extension) {
                $comment     = $this->_phpSheet->getComment($cellAddress);
                $commentText = $comment->getText()->getPlainText();
                $comment->setText($this->_parseRichText($commentText . $noteText));
            } else {
                $this->_phpSheet->getComment($cellAddress)->setText($this->_parseRichText($noteText));
            }
        }
    }
    private function _readTextObject()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $grbitOpts                             = self::_GetInt2d($recordData, 0);
        $rot                                   = self::_GetInt2d($recordData, 2);
        $cchText                               = self::_GetInt2d($recordData, 10);
        $cbRuns                                = self::_GetInt2d($recordData, 12);
        $text                                  = $this->_getSplicedRecordData();
        $this->_textObjects[$this->textObjRef] = array(
            'text' => substr($text["recordData"], $text["spliceOffsets"][0] + 1, $cchText),
            'format' => substr($text["recordData"], $text["spliceOffsets"][1], $cbRuns),
            'alignment' => $grbitOpts,
            'rotation' => $rot
        );
    }
    private function _readBof()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $substreamType = self::_GetInt2d($recordData, 2);
        switch ($substreamType) {
            case self::XLS_WorkbookGlobals:
                $version = self::_GetInt2d($recordData, 0);
                if (($version != self::XLS_BIFF8) && ($version != self::XLS_BIFF7)) {
                    throw new PHPExcel_Reader_Exception('Cannot read this Excel file. Version is too old.');
                }
                $this->_version = $version;
                break;
            case self::XLS_Worksheet:
                break;
            default:
                do {
                    $code = self::_GetInt2d($this->_data, $this->_pos);
                    $this->_readDefault();
                } while ($code != self::XLS_Type_EOF && $this->_pos < $this->_dataSize);
                break;
        }
    }
    private function _readFilepass()
    {
        $length = self::_GetInt2d($this->_data, $this->_pos + 2);
        $this->_pos += 4 + $length;
        throw new PHPExcel_Reader_Exception('Cannot read encrypted file');
    }
    private function _readCodepage()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $codepage        = self::_GetInt2d($recordData, 0);
        $this->_codepage = PHPExcel_Shared_CodePage::NumberToName($codepage);
    }
    private function _readDateMode()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
        if (ord($recordData{0}) == 1) {
            PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
        }
    }
    private function _readFont()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $objFont = new PHPExcel_Style_Font();
            $size    = self::_GetInt2d($recordData, 0);
            $objFont->setSize($size / 20);
            $isItalic = (0x0002 & self::_GetInt2d($recordData, 2)) >> 1;
            if ($isItalic)
                $objFont->setItalic(true);
            $isStrike = (0x0008 & self::_GetInt2d($recordData, 2)) >> 3;
            if ($isStrike)
                $objFont->setStrikethrough(true);
            $colorIndex          = self::_GetInt2d($recordData, 4);
            $objFont->colorIndex = $colorIndex;
            $weight              = self::_GetInt2d($recordData, 6);
            switch ($weight) {
                case 0x02BC:
                    $objFont->setBold(true);
                    break;
            }
            $escapement = self::_GetInt2d($recordData, 8);
            switch ($escapement) {
                case 0x0001:
                    $objFont->setSuperScript(true);
                    break;
                case 0x0002:
                    $objFont->setSubScript(true);
                    break;
            }
            $underlineType = ord($recordData{10});
            switch ($underlineType) {
                case 0x00:
                    break;
                case 0x01:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                    break;
                case 0x02:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLE);
                    break;
                case 0x21:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING);
                    break;
                case 0x22:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING);
                    break;
            }
            if ($this->_version == self::XLS_BIFF8) {
                $string = self::_readUnicodeStringShort(substr($recordData, 14));
            } else {
                $string = $this->_readByteStringShort(substr($recordData, 14));
            }
            $objFont->setName($string['value']);
            $this->_objFonts[] = $objFont;
        }
    }
    private function _readFormat()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $indexCode = self::_GetInt2d($recordData, 0);
            if ($this->_version == self::XLS_BIFF8) {
                $string = self::_readUnicodeStringLong(substr($recordData, 2));
            } else {
                $string = $this->_readByteStringShort(substr($recordData, 2));
            }
            $formatString               = $string['value'];
            $this->_formats[$indexCode] = $formatString;
        }
    }
    private function _readXf()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $objStyle = new PHPExcel_Style();
        if (!$this->_readDataOnly) {
            if (self::_GetInt2d($recordData, 0) < 4) {
                $fontIndex = self::_GetInt2d($recordData, 0);
            } else {
                $fontIndex = self::_GetInt2d($recordData, 0) - 1;
            }
            $objStyle->setFont($this->_objFonts[$fontIndex]);
            $numberFormatIndex = self::_GetInt2d($recordData, 2);
            if (isset($this->_formats[$numberFormatIndex])) {
                $numberformat = array(
                    'code' => $this->_formats[$numberFormatIndex]
                );
            } elseif (($code = PHPExcel_Style_NumberFormat::builtInFormatCode($numberFormatIndex)) !== '') {
                $numberformat = array(
                    'code' => $code
                );
            } else {
                $numberformat = array(
                    'code' => 'General'
                );
            }
            $objStyle->getNumberFormat()->setFormatCode($numberformat['code']);
            $xfTypeProt = self::_GetInt2d($recordData, 4);
            $isLocked   = (0x01 & $xfTypeProt) >> 0;
            $objStyle->getProtection()->setLocked($isLocked ? PHPExcel_Style_Protection::PROTECTION_INHERIT : PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
            $isHidden = (0x02 & $xfTypeProt) >> 1;
            $objStyle->getProtection()->setHidden($isHidden ? PHPExcel_Style_Protection::PROTECTION_PROTECTED : PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
            $isCellStyleXf = (0x04 & $xfTypeProt) >> 2;
            $horAlign      = (0x07 & ord($recordData{6})) >> 0;
            switch ($horAlign) {
                case 0:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
                    break;
                case 1:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    break;
                case 2:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    break;
                case 3:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    break;
                case 5:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                    break;
                case 6:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
                    break;
            }
            $wrapText = (0x08 & ord($recordData{6})) >> 3;
            switch ($wrapText) {
                case 0:
                    $objStyle->getAlignment()->setWrapText(false);
                    break;
                case 1:
                    $objStyle->getAlignment()->setWrapText(true);
                    break;
            }
            $vertAlign = (0x70 & ord($recordData{6})) >> 4;
            switch ($vertAlign) {
                case 0:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    break;
                case 1:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    break;
                case 2:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                    break;
                case 3:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
                    break;
            }
            if ($this->_version == self::XLS_BIFF8) {
                $angle    = ord($recordData{7});
                $rotation = 0;
                if ($angle <= 90) {
                    $rotation = $angle;
                } else if ($angle <= 180) {
                    $rotation = 90 - $angle;
                } else if ($angle == 255) {
                    $rotation = -165;
                }
                $objStyle->getAlignment()->setTextRotation($rotation);
                $indent = (0x0F & ord($recordData{8})) >> 0;
                $objStyle->getAlignment()->setIndent($indent);
                $shrinkToFit = (0x10 & ord($recordData{8})) >> 4;
                switch ($shrinkToFit) {
                    case 0:
                        $objStyle->getAlignment()->setShrinkToFit(false);
                        break;
                    case 1:
                        $objStyle->getAlignment()->setShrinkToFit(true);
                        break;
                }
                if ($bordersLeftStyle = self::_mapBorderStyle((0x0000000F & self::_GetInt4d($recordData, 10)) >> 0)) {
                    $objStyle->getBorders()->getLeft()->setBorderStyle($bordersLeftStyle);
                }
                if ($bordersRightStyle = self::_mapBorderStyle((0x000000F0 & self::_GetInt4d($recordData, 10)) >> 4)) {
                    $objStyle->getBorders()->getRight()->setBorderStyle($bordersRightStyle);
                }
                if ($bordersTopStyle = self::_mapBorderStyle((0x00000F00 & self::_GetInt4d($recordData, 10)) >> 8)) {
                    $objStyle->getBorders()->getTop()->setBorderStyle($bordersTopStyle);
                }
                if ($bordersBottomStyle = self::_mapBorderStyle((0x0000F000 & self::_GetInt4d($recordData, 10)) >> 12)) {
                    $objStyle->getBorders()->getBottom()->setBorderStyle($bordersBottomStyle);
                }
                $objStyle->getBorders()->getLeft()->colorIndex  = (0x007F0000 & self::_GetInt4d($recordData, 10)) >> 16;
                $objStyle->getBorders()->getRight()->colorIndex = (0x3F800000 & self::_GetInt4d($recordData, 10)) >> 23;
                $diagonalDown                                   = (0x40000000 & self::_GetInt4d($recordData, 10)) >> 30 ? true : false;
                $diagonalUp                                     = (0x80000000 & self::_GetInt4d($recordData, 10)) >> 31 ? true : false;
                if ($diagonalUp == false && $diagonalDown == false) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_NONE);
                } elseif ($diagonalUp == true && $diagonalDown == false) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_UP);
                } elseif ($diagonalUp == false && $diagonalDown == true) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_DOWN);
                } elseif ($diagonalUp == true && $diagonalDown == true) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_BOTH);
                }
                $objStyle->getBorders()->getTop()->colorIndex      = (0x0000007F & self::_GetInt4d($recordData, 14)) >> 0;
                $objStyle->getBorders()->getBottom()->colorIndex   = (0x00003F80 & self::_GetInt4d($recordData, 14)) >> 7;
                $objStyle->getBorders()->getDiagonal()->colorIndex = (0x001FC000 & self::_GetInt4d($recordData, 14)) >> 14;
                if ($bordersDiagonalStyle = self::_mapBorderStyle((0x01E00000 & self::_GetInt4d($recordData, 14)) >> 21)) {
                    $objStyle->getBorders()->getDiagonal()->setBorderStyle($bordersDiagonalStyle);
                }
                if ($fillType = self::_mapFillPattern((0xFC000000 & self::_GetInt4d($recordData, 14)) >> 26)) {
                    $objStyle->getFill()->setFillType($fillType);
                }
                $objStyle->getFill()->startcolorIndex = (0x007F & self::_GetInt2d($recordData, 18)) >> 0;
                $objStyle->getFill()->endcolorIndex   = (0x3F80 & self::_GetInt2d($recordData, 18)) >> 7;
            } else {
                $orientationAndFlags = ord($recordData{7});
                $xfOrientation       = (0x03 & $orientationAndFlags) >> 0;
                switch ($xfOrientation) {
                    case 0:
                        $objStyle->getAlignment()->setTextRotation(0);
                        break;
                    case 1:
                        $objStyle->getAlignment()->setTextRotation(-165);
                        break;
                    case 2:
                        $objStyle->getAlignment()->setTextRotation(90);
                        break;
                    case 3:
                        $objStyle->getAlignment()->setTextRotation(-90);
                        break;
                }
                $borderAndBackground                  = self::_GetInt4d($recordData, 8);
                $objStyle->getFill()->startcolorIndex = (0x0000007F & $borderAndBackground) >> 0;
                $objStyle->getFill()->endcolorIndex   = (0x00003F80 & $borderAndBackground) >> 7;
                $objStyle->getFill()->setFillType(self::_mapFillPattern((0x003F0000 & $borderAndBackground) >> 16));
                $objStyle->getBorders()->getBottom()->setBorderStyle(self::_mapBorderStyle((0x01C00000 & $borderAndBackground) >> 22));
                $objStyle->getBorders()->getBottom()->colorIndex = (0xFE000000 & $borderAndBackground) >> 25;
                $borderLines                                     = self::_GetInt4d($recordData, 12);
                $objStyle->getBorders()->getTop()->setBorderStyle(self::_mapBorderStyle((0x00000007 & $borderLines) >> 0));
                $objStyle->getBorders()->getLeft()->setBorderStyle(self::_mapBorderStyle((0x00000038 & $borderLines) >> 3));
                $objStyle->getBorders()->getRight()->setBorderStyle(self::_mapBorderStyle((0x000001C0 & $borderLines) >> 6));
                $objStyle->getBorders()->getTop()->colorIndex   = (0x0000FE00 & $borderLines) >> 9;
                $objStyle->getBorders()->getLeft()->colorIndex  = (0x007F0000 & $borderLines) >> 16;
                $objStyle->getBorders()->getRight()->colorIndex = (0x3F800000 & $borderLines) >> 23;
            }
            if ($isCellStyleXf) {
                if ($this->_xfIndex == 0) {
                    $this->_phpExcel->addCellStyleXf($objStyle);
                    $this->_mapCellStyleXfIndex[$this->_xfIndex] = 0;
                }
            } else {
                $this->_phpExcel->addCellXf($objStyle);
                $this->_mapCellXfIndex[$this->_xfIndex] = count($this->_phpExcel->getCellXfCollection()) - 1;
            }
            ++$this->_xfIndex;
        }
    }
    private function _readXfExt()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $ixfe   = self::_GetInt2d($recordData, 14);
            $cexts  = self::_GetInt2d($recordData, 18);
            $offset = 20;
            while ($offset < $length) {
                $extType = self::_GetInt2d($recordData, $offset);
                $cb      = self::_GetInt2d($recordData, $offset + 2);
                $extData = substr($recordData, $offset + 4, $cb);
                switch ($extType) {
                    case 4:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $fill = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getFill();
                                $fill->getStartColor()->setRGB($rgb);
                                unset($fill->startcolorIndex);
                            }
                        }
                        break;
                    case 5:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $fill = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getFill();
                                $fill->getEndColor()->setRGB($rgb);
                                unset($fill->endcolorIndex);
                            }
                        }
                        break;
                    case 7:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $top = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getBorders()->getTop();
                                $top->getColor()->setRGB($rgb);
                                unset($top->colorIndex);
                            }
                        }
                        break;
                    case 8:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $bottom = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getBorders()->getBottom();
                                $bottom->getColor()->setRGB($rgb);
                                unset($bottom->colorIndex);
                            }
                        }
                        break;
                    case 9:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $left = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getBorders()->getLeft();
                                $left->getColor()->setRGB($rgb);
                                unset($left->colorIndex);
                            }
                        }
                        break;
                    case 10:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $right = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getBorders()->getRight();
                                $right->getColor()->setRGB($rgb);
                                unset($right->colorIndex);
                            }
                        }
                        break;
                    case 11:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $diagonal = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getBorders()->getDiagonal();
                                $diagonal->getColor()->setRGB($rgb);
                                unset($diagonal->colorIndex);
                            }
                        }
                        break;
                    case 13:
                        $xclfType  = self::_GetInt2d($extData, 0);
                        $xclrValue = substr($extData, 4, 4);
                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));
                            if (isset($this->_mapCellXfIndex[$ixfe])) {
                                $font = $this->_phpExcel->getCellXfByIndex($this->_mapCellXfIndex[$ixfe])->getFont();
                                $font->getColor()->setRGB($rgb);
                                unset($font->colorIndex);
                            }
                        }
                        break;
                }
                $offset += $cb;
            }
        }
    }
    private function _readStyle()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $ixfe      = self::_GetInt2d($recordData, 0);
            $xfIndex   = (0x0FFF & $ixfe) >> 0;
            $isBuiltIn = (bool) ((0x8000 & $ixfe) >> 15);
            if ($isBuiltIn) {
                $builtInId = ord($recordData{2});
                switch ($builtInId) {
                    case 0x00:
                        break;
                    default:
                        break;
                }
            } else {
            }
        }
    }
    private function _readPalette()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $nm = self::_GetInt2d($recordData, 0);
            for ($i = 0; $i < $nm; ++$i) {
                $rgb              = substr($recordData, 2 + 4 * $i, 4);
                $this->_palette[] = self::_readRGB($rgb);
            }
        }
    }
    private function _readSheet()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $rec_offset = self::_GetInt4d($recordData, 0);
        switch (ord($recordData{4})) {
            case 0x00:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_VISIBLE;
                break;
            case 0x01:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_HIDDEN;
                break;
            case 0x02:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN;
                break;
            default:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_VISIBLE;
                break;
        }
        $sheetType = ord($recordData{5});
        if ($this->_version == self::XLS_BIFF8) {
            $string   = self::_readUnicodeStringShort(substr($recordData, 6));
            $rec_name = $string['value'];
        } elseif ($this->_version == self::XLS_BIFF7) {
            $string   = $this->_readByteStringShort(substr($recordData, 6));
            $rec_name = $string['value'];
        }
        $this->_sheets[] = array(
            'name' => $rec_name,
            'offset' => $rec_offset,
            'sheetState' => $sheetState,
            'sheetType' => $sheetType
        );
    }
    private function _readExternalBook()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $offset = 0;
        if (strlen($recordData) > 4) {
            $nm = self::_GetInt2d($recordData, 0);
            $offset += 2;
            $encodedUrlString = self::_readUnicodeStringLong(substr($recordData, 2));
            $offset += $encodedUrlString['size'];
            $externalSheetNames = array();
            for ($i = 0; $i < $nm; ++$i) {
                $externalSheetNameString = self::_readUnicodeStringLong(substr($recordData, $offset));
                $externalSheetNames[]    = $externalSheetNameString['value'];
                $offset += $externalSheetNameString['size'];
            }
            $this->_externalBooks[] = array(
                'type' => 'external',
                'encodedUrl' => $encodedUrlString['value'],
                'externalSheetNames' => $externalSheetNames
            );
        } elseif (substr($recordData, 2, 2) == pack('CC', 0x01, 0x04)) {
            $this->_externalBooks[] = array(
                'type' => 'internal'
            );
        } elseif (substr($recordData, 0, 4) == pack('vCC', 0x0001, 0x01, 0x3A)) {
            $this->_externalBooks[] = array(
                'type' => 'addInFunction'
            );
        } elseif (substr($recordData, 0, 2) == pack('v', 0x0000)) {
            $this->_externalBooks[] = array(
                'type' => 'DDEorOLE'
            );
        }
    }
    private function _readExternName()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8) {
            $options                = self::_GetInt2d($recordData, 0);
            $nameString             = self::_readUnicodeStringShort(substr($recordData, 6));
            $offset                 = 6 + $nameString['size'];
            $formula                = $this->_getFormulaFromStructure(substr($recordData, $offset));
            $this->_externalNames[] = array(
                'name' => $nameString['value'],
                'formula' => $formula
            );
        }
    }
    private function _readExternSheet()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8) {
            $nm = self::_GetInt2d($recordData, 0);
            for ($i = 0; $i < $nm; ++$i) {
                $this->_ref[] = array(
                    'externalBookIndex' => self::_GetInt2d($recordData, 2 + 6 * $i),
                    'firstSheetIndex' => self::_GetInt2d($recordData, 4 + 6 * $i),
                    'lastSheetIndex' => self::_GetInt2d($recordData, 6 + 6 * $i)
                );
            }
        }
    }
    private function _readDefinedName()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8) {
            $opts             = self::_GetInt2d($recordData, 0);
            $isBuiltInName    = (0x0020 & $opts) >> 5;
            $nlen             = ord($recordData{3});
            $flen             = self::_GetInt2d($recordData, 4);
            $scope            = self::_GetInt2d($recordData, 8);
            $string           = self::_readUnicodeString(substr($recordData, 14), $nlen);
            $offset           = 14 + $string['size'];
            $formulaStructure = pack('v', $flen) . substr($recordData, $offset);
            try {
                $formula = $this->_getFormulaFromStructure($formulaStructure);
            }
            catch (PHPExcel_Exception $e) {
                $formula = '';
            }
            $this->_definedname[] = array(
                'isBuiltInName' => $isBuiltInName,
                'name' => $string['value'],
                'formula' => $formula,
                'scope' => $scope
            );
        }
    }
    private function _readMsoDrawingGroup()
    {
        $length            = self::_GetInt2d($this->_data, $this->_pos + 2);
        $splicedRecordData = $this->_getSplicedRecordData();
        $recordData        = $splicedRecordData['recordData'];
        $this->_drawingGroupData .= $recordData;
    }
    private function _readSst()
    {
        $pos               = 0;
        $splicedRecordData = $this->_getSplicedRecordData();
        $recordData        = $splicedRecordData['recordData'];
        $spliceOffsets     = $splicedRecordData['spliceOffsets'];
        $pos += 4;
        $nm = self::_GetInt4d($recordData, 4);
        $pos += 4;
        for ($i = 0; $i < $nm; ++$i) {
            $numChars = self::_GetInt2d($recordData, $pos);
            $pos += 2;
            $optionFlags = ord($recordData{$pos});
            ++$pos;
            $isCompressed = (($optionFlags & 0x01) == 0);
            $hasAsian     = (($optionFlags & 0x04) != 0);
            $hasRichText  = (($optionFlags & 0x08) != 0);
            if ($hasRichText) {
                $formattingRuns = self::_GetInt2d($recordData, $pos);
                $pos += 2;
            }
            if ($hasAsian) {
                $extendedRunLength = self::_GetInt4d($recordData, $pos);
                $pos += 4;
            }
            $len = ($isCompressed) ? $numChars : $numChars * 2;
            foreach ($spliceOffsets as $spliceOffset) {
                if ($pos <= $spliceOffset) {
                    $limitpos = $spliceOffset;
                    break;
                }
            }
            if ($pos + $len <= $limitpos) {
                $retstr = substr($recordData, $pos, $len);
                $pos += $len;
            } else {
                $retstr    = substr($recordData, $pos, $limitpos - $pos);
                $bytesRead = $limitpos - $pos;
                $charsLeft = $numChars - (($isCompressed) ? $bytesRead : ($bytesRead / 2));
                $pos       = $limitpos;
                while ($charsLeft > 0) {
                    foreach ($spliceOffsets as $spliceOffset) {
                        if ($pos < $spliceOffset) {
                            $limitpos = $spliceOffset;
                            break;
                        }
                    }
                    $option = ord($recordData{$pos});
                    ++$pos;
                    if ($isCompressed && ($option == 0)) {
                        $len = min($charsLeft, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len;
                        $isCompressed = true;
                    } elseif (!$isCompressed && ($option != 0)) {
                        $len = min($charsLeft * 2, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len / 2;
                        $isCompressed = false;
                    } elseif (!$isCompressed && ($option == 0)) {
                        $len = min($charsLeft, $limitpos - $pos);
                        for ($j = 0; $j < $len; ++$j) {
                            $retstr .= $recordData{$pos + $j} . chr(0);
                        }
                        $charsLeft -= $len;
                        $isCompressed = false;
                    } else {
                        $newstr = '';
                        for ($j = 0; $j < strlen($retstr); ++$j) {
                            $newstr .= $retstr[$j] . chr(0);
                        }
                        $retstr = $newstr;
                        $len    = min($charsLeft * 2, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len / 2;
                        $isCompressed = false;
                    }
                    $pos += $len;
                }
            }
            $retstr  = self::_encodeUTF16($retstr, $isCompressed);
            $fmtRuns = array();
            if ($hasRichText) {
                for ($j = 0; $j < $formattingRuns; ++$j) {
                    $charPos   = self::_GetInt2d($recordData, $pos + $j * 4);
                    $fontIndex = self::_GetInt2d($recordData, $pos + 2 + $j * 4);
                    $fmtRuns[] = array(
                        'charPos' => $charPos,
                        'fontIndex' => $fontIndex
                    );
                }
                $pos += 4 * $formattingRuns;
            }
            if ($hasAsian) {
                $pos += $extendedRunLength;
            }
            $this->_sst[] = array(
                'value' => $retstr,
                'fmtRuns' => $fmtRuns
            );
        }
    }
    private function _readPrintGridlines()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8 && !$this->_readDataOnly) {
            $printGridlines = (bool) self::_GetInt2d($recordData, 0);
            $this->_phpSheet->setPrintGridlines($printGridlines);
        }
    }
    private function _readDefaultRowHeight()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $height = self::_GetInt2d($recordData, 2);
        $this->_phpSheet->getDefaultRowDimension()->setRowHeight($height / 20);
    }
    private function _readSheetPr()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $isSummaryBelow = (0x0040 & self::_GetInt2d($recordData, 0)) >> 6;
        $this->_phpSheet->setShowSummaryBelow($isSummaryBelow);
        $isSummaryRight = (0x0080 & self::_GetInt2d($recordData, 0)) >> 7;
        $this->_phpSheet->setShowSummaryRight($isSummaryRight);
        $this->_isFitToPages = (bool) ((0x0100 & self::_GetInt2d($recordData, 0)) >> 8);
    }
    private function _readHorizontalPageBreaks()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8 && !$this->_readDataOnly) {
            $nm = self::_GetInt2d($recordData, 0);
            for ($i = 0; $i < $nm; ++$i) {
                $r  = self::_GetInt2d($recordData, 2 + 6 * $i);
                $cf = self::_GetInt2d($recordData, 2 + 6 * $i + 2);
                $cl = self::_GetInt2d($recordData, 2 + 6 * $i + 4);
                $this->_phpSheet->setBreakByColumnAndRow($cf, $r, PHPExcel_Worksheet::BREAK_ROW);
            }
        }
    }
    private function _readVerticalPageBreaks()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8 && !$this->_readDataOnly) {
            $nm = self::_GetInt2d($recordData, 0);
            for ($i = 0; $i < $nm; ++$i) {
                $c  = self::_GetInt2d($recordData, 2 + 6 * $i);
                $rf = self::_GetInt2d($recordData, 2 + 6 * $i + 2);
                $rl = self::_GetInt2d($recordData, 2 + 6 * $i + 4);
                $this->_phpSheet->setBreakByColumnAndRow($c, $rf, PHPExcel_Worksheet::BREAK_COLUMN);
            }
        }
    }
    private function _readHeader()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            if ($recordData) {
                if ($this->_version == self::XLS_BIFF8) {
                    $string = self::_readUnicodeStringLong($recordData);
                } else {
                    $string = $this->_readByteStringShort($recordData);
                }
                $this->_phpSheet->getHeaderFooter()->setOddHeader($string['value']);
                $this->_phpSheet->getHeaderFooter()->setEvenHeader($string['value']);
            }
        }
    }
    private function _readFooter()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            if ($recordData) {
                if ($this->_version == self::XLS_BIFF8) {
                    $string = self::_readUnicodeStringLong($recordData);
                } else {
                    $string = $this->_readByteStringShort($recordData);
                }
                $this->_phpSheet->getHeaderFooter()->setOddFooter($string['value']);
                $this->_phpSheet->getHeaderFooter()->setEvenFooter($string['value']);
            }
        }
    }
    private function _readHcenter()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $isHorizontalCentered = (bool) self::_GetInt2d($recordData, 0);
            $this->_phpSheet->getPageSetup()->setHorizontalCentered($isHorizontalCentered);
        }
    }
    private function _readVcenter()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $isVerticalCentered = (bool) self::_GetInt2d($recordData, 0);
            $this->_phpSheet->getPageSetup()->setVerticalCentered($isVerticalCentered);
        }
    }
    private function _readLeftMargin()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $this->_phpSheet->getPageMargins()->setLeft(self::_extractNumber($recordData));
        }
    }
    private function _readRightMargin()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $this->_phpSheet->getPageMargins()->setRight(self::_extractNumber($recordData));
        }
    }
    private function _readTopMargin()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $this->_phpSheet->getPageMargins()->setTop(self::_extractNumber($recordData));
        }
    }
    private function _readBottomMargin()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $this->_phpSheet->getPageMargins()->setBottom(self::_extractNumber($recordData));
        }
    }
    private function _readPageSetup()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $paperSize   = self::_GetInt2d($recordData, 0);
            $scale       = self::_GetInt2d($recordData, 2);
            $fitToWidth  = self::_GetInt2d($recordData, 6);
            $fitToHeight = self::_GetInt2d($recordData, 8);
            $isPortrait  = (0x0002 & self::_GetInt2d($recordData, 10)) >> 1;
            $isNotInit   = (0x0004 & self::_GetInt2d($recordData, 10)) >> 2;
            if (!$isNotInit) {
                $this->_phpSheet->getPageSetup()->setPaperSize($paperSize);
                switch ($isPortrait) {
                    case 0:
                        $this->_phpSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        break;
                    case 1:
                        $this->_phpSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                        break;
                }
                $this->_phpSheet->getPageSetup()->setScale($scale, false);
                $this->_phpSheet->getPageSetup()->setFitToPage((bool) $this->_isFitToPages);
                $this->_phpSheet->getPageSetup()->setFitToWidth($fitToWidth, false);
                $this->_phpSheet->getPageSetup()->setFitToHeight($fitToHeight, false);
            }
            $marginHeader = self::_extractNumber(substr($recordData, 16, 8));
            $this->_phpSheet->getPageMargins()->setHeader($marginHeader);
            $marginFooter = self::_extractNumber(substr($recordData, 24, 8));
            $this->_phpSheet->getPageMargins()->setFooter($marginFooter);
        }
    }
    private function _readProtect()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $bool = (0x01 & self::_GetInt2d($recordData, 0)) >> 0;
        $this->_phpSheet->getProtection()->setSheet((bool) $bool);
    }
    private function _readScenProtect()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $bool = (0x01 & self::_GetInt2d($recordData, 0)) >> 0;
        $this->_phpSheet->getProtection()->setScenarios((bool) $bool);
    }
    private function _readObjectProtect()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $bool = (0x01 & self::_GetInt2d($recordData, 0)) >> 0;
        $this->_phpSheet->getProtection()->setObjects((bool) $bool);
    }
    private function _readPassword()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $password = strtoupper(dechex(self::_GetInt2d($recordData, 0)));
            $this->_phpSheet->getProtection()->setPassword($password, true);
        }
    }
    private function _readDefColWidth()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $width = self::_GetInt2d($recordData, 0);
        if ($width != 8) {
            $this->_phpSheet->getDefaultColumnDimension()->setWidth($width);
        }
    }
    private function _readColInfo()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $fc          = self::_GetInt2d($recordData, 0);
            $lc          = self::_GetInt2d($recordData, 2);
            $width       = self::_GetInt2d($recordData, 4);
            $xfIndex     = self::_GetInt2d($recordData, 6);
            $isHidden    = (0x0001 & self::_GetInt2d($recordData, 8)) >> 0;
            $level       = (0x0700 & self::_GetInt2d($recordData, 8)) >> 8;
            $isCollapsed = (0x1000 & self::_GetInt2d($recordData, 8)) >> 12;
            for ($i = $fc; $i <= $lc; ++$i) {
                if ($lc == 255 || $lc == 256) {
                    $this->_phpSheet->getDefaultColumnDimension()->setWidth($width / 256);
                    break;
                }
                $this->_phpSheet->getColumnDimensionByColumn($i)->setWidth($width / 256);
                $this->_phpSheet->getColumnDimensionByColumn($i)->setVisible(!$isHidden);
                $this->_phpSheet->getColumnDimensionByColumn($i)->setOutlineLevel($level);
                $this->_phpSheet->getColumnDimensionByColumn($i)->setCollapsed($isCollapsed);
                $this->_phpSheet->getColumnDimensionByColumn($i)->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
        }
    }
    private function _readRow()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $r                = self::_GetInt2d($recordData, 0);
            $height           = (0x7FFF & self::_GetInt2d($recordData, 6)) >> 0;
            $useDefaultHeight = (0x8000 & self::_GetInt2d($recordData, 6)) >> 15;
            if (!$useDefaultHeight) {
                $this->_phpSheet->getRowDimension($r + 1)->setRowHeight($height / 20);
            }
            $level = (0x00000007 & self::_GetInt4d($recordData, 12)) >> 0;
            $this->_phpSheet->getRowDimension($r + 1)->setOutlineLevel($level);
            $isCollapsed = (0x00000010 & self::_GetInt4d($recordData, 12)) >> 4;
            $this->_phpSheet->getRowDimension($r + 1)->setCollapsed($isCollapsed);
            $isHidden = (0x00000020 & self::_GetInt4d($recordData, 12)) >> 5;
            $this->_phpSheet->getRowDimension($r + 1)->setVisible(!$isHidden);
            $hasExplicitFormat = (0x00000080 & self::_GetInt4d($recordData, 12)) >> 7;
            $xfIndex           = (0x0FFF0000 & self::_GetInt4d($recordData, 12)) >> 16;
            if ($hasExplicitFormat) {
                $this->_phpSheet->getRowDimension($r + 1)->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
        }
    }
    private function _readRk()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row          = self::_GetInt2d($recordData, 0);
        $column       = self::_GetInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            $xfIndex  = self::_GetInt2d($recordData, 4);
            $rknum    = self::_GetInt4d($recordData, 6);
            $numValue = self::_GetIEEE754($rknum);
            $cell     = $this->_phpSheet->getCell($columnString . ($row + 1));
            if (!$this->_readDataOnly) {
                $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
            $cell->setValueExplicit($numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }
    }
    private function _readLabelSst()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row          = self::_GetInt2d($recordData, 0);
        $column       = self::_GetInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            $xfIndex = self::_GetInt2d($recordData, 4);
            $index   = self::_GetInt4d($recordData, 6);
            if (($fmtRuns = $this->_sst[$index]['fmtRuns']) && !$this->_readDataOnly) {
                $richText = new PHPExcel_RichText();
                $charPos  = 0;
                $sstCount = count($this->_sst[$index]['fmtRuns']);
                for ($i = 0; $i <= $sstCount; ++$i) {
                    if (isset($fmtRuns[$i])) {
                        $text    = PHPExcel_Shared_String::Substring($this->_sst[$index]['value'], $charPos, $fmtRuns[$i]['charPos'] - $charPos);
                        $charPos = $fmtRuns[$i]['charPos'];
                    } else {
                        $text = PHPExcel_Shared_String::Substring($this->_sst[$index]['value'], $charPos, PHPExcel_Shared_String::CountCharacters($this->_sst[$index]['value']));
                    }
                    if (PHPExcel_Shared_String::CountCharacters($text) > 0) {
                        if ($i == 0) {
                            $richText->createText($text);
                        } else {
                            $textRun = $richText->createTextRun($text);
                            if (isset($fmtRuns[$i - 1])) {
                                if ($fmtRuns[$i - 1]['fontIndex'] < 4) {
                                    $fontIndex = $fmtRuns[$i - 1]['fontIndex'];
                                } else {
                                    $fontIndex = $fmtRuns[$i - 1]['fontIndex'] - 1;
                                }
                                $textRun->setFont(clone $this->_objFonts[$fontIndex]);
                            }
                        }
                    }
                }
                $cell = $this->_phpSheet->getCell($columnString . ($row + 1));
                $cell->setValueExplicit($richText, PHPExcel_Cell_DataType::TYPE_STRING);
            } else {
                $cell = $this->_phpSheet->getCell($columnString . ($row + 1));
                $cell->setValueExplicit($this->_sst[$index]['value'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
            if (!$this->_readDataOnly) {
                $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
        }
    }
    private function _readMulRk()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row      = self::_GetInt2d($recordData, 0);
        $colFirst = self::_GetInt2d($recordData, 2);
        $colLast  = self::_GetInt2d($recordData, $length - 2);
        $columns  = $colLast - $colFirst + 1;
        $offset   = 4;
        for ($i = 0; $i < $columns; ++$i) {
            $columnString = PHPExcel_Cell::stringFromColumnIndex($colFirst + $i);
            if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
                $xfIndex  = self::_GetInt2d($recordData, $offset);
                $numValue = self::_GetIEEE754(self::_GetInt4d($recordData, $offset + 2));
                $cell     = $this->_phpSheet->getCell($columnString . ($row + 1));
                if (!$this->_readDataOnly) {
                    $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
                }
                $cell->setValueExplicit($numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            }
            $offset += 6;
        }
    }
    private function _readNumber()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row          = self::_GetInt2d($recordData, 0);
        $column       = self::_GetInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            $xfIndex  = self::_GetInt2d($recordData, 4);
            $numValue = self::_extractNumber(substr($recordData, 6, 8));
            $cell     = $this->_phpSheet->getCell($columnString . ($row + 1));
            if (!$this->_readDataOnly) {
                $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
            $cell->setValueExplicit($numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }
    }
    private function _readFormula()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row                   = self::_GetInt2d($recordData, 0);
        $column                = self::_GetInt2d($recordData, 2);
        $columnString          = PHPExcel_Cell::stringFromColumnIndex($column);
        $formulaStructure      = substr($recordData, 20);
        $options               = self::_GetInt2d($recordData, 14);
        $isPartOfSharedFormula = (bool) (0x0008 & $options);
        $isPartOfSharedFormula = $isPartOfSharedFormula && ord($formulaStructure{2}) == 0x01;
        if ($isPartOfSharedFormula) {
            $baseRow         = self::_GetInt2d($formulaStructure, 3);
            $baseCol         = self::_GetInt2d($formulaStructure, 5);
            $this->_baseCell = PHPExcel_Cell::stringFromColumnIndex($baseCol) . ($baseRow + 1);
        }
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            if ($isPartOfSharedFormula) {
                $this->_sharedFormulaParts[$columnString . ($row + 1)] = $this->_baseCell;
            }
            $xfIndex = self::_GetInt2d($recordData, 4);
            if ((ord($recordData{6}) == 0) && (ord($recordData{12}) == 255) && (ord($recordData{13}) == 255)) {
                $dataType = PHPExcel_Cell_DataType::TYPE_STRING;
                $code     = self::_GetInt2d($this->_data, $this->_pos);
                if ($code == self::XLS_Type_SHAREDFMLA) {
                    $this->_readSharedFmla();
                }
                $value = $this->_readString();
            } elseif ((ord($recordData{6}) == 1) && (ord($recordData{12}) == 255) && (ord($recordData{13}) == 255)) {
                $dataType = PHPExcel_Cell_DataType::TYPE_BOOL;
                $value    = (bool) ord($recordData{8});
            } elseif ((ord($recordData{6}) == 2) && (ord($recordData{12}) == 255) && (ord($recordData{13}) == 255)) {
                $dataType = PHPExcel_Cell_DataType::TYPE_ERROR;
                $value    = self::_mapErrorCode(ord($recordData{8}));
            } elseif ((ord($recordData{6}) == 3) && (ord($recordData{12}) == 255) && (ord($recordData{13}) == 255)) {
                $dataType = PHPExcel_Cell_DataType::TYPE_NULL;
                $value    = '';
            } else {
                $dataType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                $value    = self::_extractNumber(substr($recordData, 6, 8));
            }
            $cell = $this->_phpSheet->getCell($columnString . ($row + 1));
            if (!$this->_readDataOnly) {
                $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
            if (!$isPartOfSharedFormula) {
                try {
                    if ($this->_version != self::XLS_BIFF8) {
                        throw new PHPExcel_Reader_Exception('Not BIFF8. Can only read BIFF8 formulas');
                    }
                    $formula = $this->_getFormulaFromStructure($formulaStructure);
                    $cell->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
                }
                catch (PHPExcel_Exception $e) {
                    $cell->setValueExplicit($value, $dataType);
                }
            } else {
                if ($this->_version == self::XLS_BIFF8) {
                } else {
                    $cell->setValueExplicit($value, $dataType);
                }
            }
            $cell->setCalculatedValue($value);
        }
    }
    private function _readSharedFmla()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $cellRange                               = substr($recordData, 0, 6);
        $cellRange                               = $this->_readBIFF5CellRangeAddressFixed($cellRange);
        $no                                      = ord($recordData{7});
        $formula                                 = substr($recordData, 8);
        $this->_sharedFormulas[$this->_baseCell] = $formula;
    }
    private function _readString()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8) {
            $string = self::_readUnicodeStringLong($recordData);
            $value  = $string['value'];
        } else {
            $string = $this->_readByteStringLong($recordData);
            $value  = $string['value'];
        }
        return $value;
    }
    private function _readBoolErr()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row          = self::_GetInt2d($recordData, 0);
        $column       = self::_GetInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            $xfIndex = self::_GetInt2d($recordData, 4);
            $boolErr = ord($recordData{6});
            $isError = ord($recordData{7});
            $cell    = $this->_phpSheet->getCell($columnString . ($row + 1));
            switch ($isError) {
                case 0:
                    $value = (bool) $boolErr;
                    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_BOOL);
                    break;
                case 1:
                    $value = self::_mapErrorCode($boolErr);
                    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_ERROR);
                    break;
            }
            if (!$this->_readDataOnly) {
                $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
        }
    }
    private function _readMulBlank()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row = self::_GetInt2d($recordData, 0);
        $fc  = self::_GetInt2d($recordData, 2);
        if (!$this->_readDataOnly) {
            for ($i = 0; $i < $length / 2 - 3; ++$i) {
                $columnString = PHPExcel_Cell::stringFromColumnIndex($fc + $i);
                if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
                    $xfIndex = self::_GetInt2d($recordData, 4 + 2 * $i);
                    $this->_phpSheet->getCell($columnString . ($row + 1))->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
                }
            }
        }
    }
    private function _readLabel()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row          = self::_GetInt2d($recordData, 0);
        $column       = self::_GetInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            $xfIndex = self::_GetInt2d($recordData, 4);
            if ($this->_version == self::XLS_BIFF8) {
                $string = self::_readUnicodeStringLong(substr($recordData, 6));
                $value  = $string['value'];
            } else {
                $string = $this->_readByteStringLong(substr($recordData, 6));
                $value  = $string['value'];
            }
            $cell = $this->_phpSheet->getCell($columnString . ($row + 1));
            $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
            if (!$this->_readDataOnly) {
                $cell->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
        }
    }
    private function _readBlank()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $row          = self::_GetInt2d($recordData, 0);
        $col          = self::_GetInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($col);
        if (($this->getReadFilter() !== NULL) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_phpSheet->getTitle())) {
            $xfIndex = self::_GetInt2d($recordData, 4);
            if (!$this->_readDataOnly) {
                $this->_phpSheet->getCell($columnString . ($row + 1))->setXfIndex($this->_mapCellXfIndex[$xfIndex]);
            }
        }
    }
    private function _readMsoDrawing()
    {
        $length            = self::_GetInt2d($this->_data, $this->_pos + 2);
        $splicedRecordData = $this->_getSplicedRecordData();
        $recordData        = $splicedRecordData['recordData'];
        $this->_drawingData .= $recordData;
    }
    private function _readObj()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly || $this->_version != self::XLS_BIFF8) {
            return;
        }
        $ftCmoType        = self::_GetInt2d($recordData, 0);
        $cbCmoSize        = self::_GetInt2d($recordData, 2);
        $otObjType        = self::_GetInt2d($recordData, 4);
        $idObjID          = self::_GetInt2d($recordData, 6);
        $grbitOpts        = self::_GetInt2d($recordData, 6);
        $this->_objs[]    = array(
            'ftCmoType' => $ftCmoType,
            'cbCmoSize' => $cbCmoSize,
            'otObjType' => $otObjType,
            'idObjID' => $idObjID,
            'grbitOpts' => $grbitOpts
        );
        $this->textObjRef = $idObjID;
    }
    private function _readWindow2()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $options            = self::_GetInt2d($recordData, 0);
        $firstVisibleRow    = self::_GetInt2d($recordData, 2);
        $firstVisibleColumn = self::_GetInt2d($recordData, 4);
        if ($this->_version === self::XLS_BIFF8) {
            $zoomscaleInPageBreakPreview = self::_GetInt2d($recordData, 10);
            if ($zoomscaleInPageBreakPreview === 0)
                $zoomscaleInPageBreakPreview = 60;
            $zoomscaleInNormalView = self::_GetInt2d($recordData, 12);
            if ($zoomscaleInNormalView === 0)
                $zoomscaleInNormalView = 100;
        }
        $showGridlines = (bool) ((0x0002 & $options) >> 1);
        $this->_phpSheet->setShowGridlines($showGridlines);
        $showRowColHeaders = (bool) ((0x0004 & $options) >> 2);
        $this->_phpSheet->setShowRowColHeaders($showRowColHeaders);
        $this->_frozen = (bool) ((0x0008 & $options) >> 3);
        $this->_phpSheet->setRightToLeft((bool) ((0x0040 & $options) >> 6));
        $isActive = (bool) ((0x0400 & $options) >> 10);
        if ($isActive) {
            $this->_phpExcel->setActiveSheetIndex($this->_phpExcel->getIndex($this->_phpSheet));
        }
        $isPageBreakPreview = (bool) ((0x0800 & $options) >> 11);
        if ($this->_phpSheet->getSheetView()->getView() !== PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_LAYOUT) {
            $view = $isPageBreakPreview ? PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW : PHPExcel_Worksheet_SheetView::SHEETVIEW_NORMAL;
            $this->_phpSheet->getSheetView()->setView($view);
            if ($this->_version === self::XLS_BIFF8) {
                $zoomScale = $isPageBreakPreview ? $zoomscaleInPageBreakPreview : $zoomscaleInNormalView;
                $this->_phpSheet->getSheetView()->setZoomScale($zoomScale);
                $this->_phpSheet->getSheetView()->setZoomScaleNormal($zoomscaleInNormalView);
            }
        }
    }
    private function _readPageLayoutView()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $rt                = self::_GetInt2d($recordData, 0);
        $grbitFrt          = self::_GetInt2d($recordData, 2);
        $wScalePLV         = self::_GetInt2d($recordData, 12);
        $grbit             = self::_GetInt2d($recordData, 14);
        $fPageLayoutView   = $grbit & 0x01;
        $fRulerVisible     = ($grbit >> 1) & 0x01;
        $fWhitespaceHidden = ($grbit >> 3) & 0x01;
        if ($fPageLayoutView === 1) {
            $this->_phpSheet->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_LAYOUT);
            $this->_phpSheet->getSheetView()->setZoomScale($wScalePLV);
        }
    }
    private function _readScl()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $numerator   = self::_GetInt2d($recordData, 0);
        $denumerator = self::_GetInt2d($recordData, 2);
        $this->_phpSheet->getSheetView()->setZoomScale($numerator * 100 / $denumerator);
    }
    private function _readPane()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $px = self::_GetInt2d($recordData, 0);
            $py = self::_GetInt2d($recordData, 2);
            if ($this->_frozen) {
                $this->_phpSheet->freezePane(PHPExcel_Cell::stringFromColumnIndex($px) . ($py + 1));
            } else {
            }
        }
    }
    private function _readSelection()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            $paneId               = ord($recordData{0});
            $r                    = self::_GetInt2d($recordData, 1);
            $c                    = self::_GetInt2d($recordData, 3);
            $index                = self::_GetInt2d($recordData, 5);
            $data                 = substr($recordData, 7);
            $cellRangeAddressList = $this->_readBIFF5CellRangeAddressList($data);
            $selectedCells        = $cellRangeAddressList['cellRangeAddresses'][0];
            if (preg_match('/^([A-Z]+1\:[A-Z]+)16384$/', $selectedCells)) {
                $selectedCells = preg_replace('/^([A-Z]+1\:[A-Z]+)16384$/', '${1}1048576', $selectedCells);
            }
            if (preg_match('/^([A-Z]+1\:[A-Z]+)65536$/', $selectedCells)) {
                $selectedCells = preg_replace('/^([A-Z]+1\:[A-Z]+)65536$/', '${1}1048576', $selectedCells);
            }
            if (preg_match('/^(A[0-9]+\:)IV([0-9]+)$/', $selectedCells)) {
                $selectedCells = preg_replace('/^(A[0-9]+\:)IV([0-9]+)$/', '${1}XFD${2}', $selectedCells);
            }
            $this->_phpSheet->setSelectedCells($selectedCells);
        }
    }
    private function _includeCellRangeFiltered($cellRangeAddress)
    {
        $includeCellRange = true;
        if ($this->getReadFilter() !== NULL) {
            $includeCellRange = false;
            $rangeBoundaries  = PHPExcel_Cell::getRangeBoundaries($cellRangeAddress);
            $rangeBoundaries[1][0]++;
            for ($row = $rangeBoundaries[0][1]; $row <= $rangeBoundaries[1][1]; $row++) {
                for ($column = $rangeBoundaries[0][0]; $column != $rangeBoundaries[1][0]; $column++) {
                    if ($this->getReadFilter()->readCell($column, $row, $this->_phpSheet->getTitle())) {
                        $includeCellRange = true;
                        break 2;
                    }
                }
            }
        }
        return $includeCellRange;
    }
    private function _readMergedCells()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_version == self::XLS_BIFF8 && !$this->_readDataOnly) {
            $cellRangeAddressList = $this->_readBIFF8CellRangeAddressList($recordData);
            foreach ($cellRangeAddressList['cellRangeAddresses'] as $cellRangeAddress) {
                if ((strpos($cellRangeAddress, ':') !== FALSE) && ($this->_includeCellRangeFiltered($cellRangeAddress))) {
                    $this->_phpSheet->mergeCells($cellRangeAddress);
                }
            }
        }
    }
    private function _readHyperLink()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if (!$this->_readDataOnly) {
            try {
                $cellRange = $this->_readBIFF8CellRangeAddressFixed($recordData, 0, 8);
            }
            catch (PHPExcel_Exception $e) {
                return;
            }
            $isFileLinkOrUrl = (0x00000001 & self::_GetInt2d($recordData, 28)) >> 0;
            $isAbsPathOrUrl  = (0x00000001 & self::_GetInt2d($recordData, 28)) >> 1;
            $hasDesc         = (0x00000014 & self::_GetInt2d($recordData, 28)) >> 2;
            $hasText         = (0x00000008 & self::_GetInt2d($recordData, 28)) >> 3;
            $hasFrame        = (0x00000080 & self::_GetInt2d($recordData, 28)) >> 7;
            $isUNC           = (0x00000100 & self::_GetInt2d($recordData, 28)) >> 8;
            $offset          = 32;
            if ($hasDesc) {
                $dl   = self::_GetInt4d($recordData, 32);
                $desc = self::_encodeUTF16(substr($recordData, 36, 2 * ($dl - 1)), false);
                $offset += 4 + 2 * $dl;
            }
            if ($hasFrame) {
                $fl = self::_GetInt4d($recordData, $offset);
                $offset += 4 + 2 * $fl;
            }
            $hyperlinkType = null;
            if ($isUNC) {
                $hyperlinkType = 'UNC';
            } else if (!$isFileLinkOrUrl) {
                $hyperlinkType = 'workbook';
            } else if (ord($recordData{$offset}) == 0x03) {
                $hyperlinkType = 'local';
            } else if (ord($recordData{$offset}) == 0xE0) {
                $hyperlinkType = 'URL';
            }
            switch ($hyperlinkType) {
                case 'URL':
                    $offset += 16;
                    $us = self::_GetInt4d($recordData, $offset);
                    $offset += 4;
                    $url = self::_encodeUTF16(substr($recordData, $offset, $us - 2), false);
                    $url .= $hasText ? '#' : '';
                    $offset += $us;
                    break;
                case 'local':
                    $offset += 16;
                    $upLevelCount = self::_GetInt2d($recordData, $offset);
                    $offset += 2;
                    $sl = self::_GetInt4d($recordData, $offset);
                    $offset += 4;
                    $shortenedFilePath = substr($recordData, $offset, $sl);
                    $shortenedFilePath = self::_encodeUTF16($shortenedFilePath, true);
                    $shortenedFilePath = substr($shortenedFilePath, 0, -1);
                    $offset += $sl;
                    $offset += 24;
                    $sz = self::_GetInt4d($recordData, $offset);
                    $offset += 4;
                    if ($sz > 0) {
                        $xl = self::_GetInt4d($recordData, $offset);
                        $offset += 4;
                        $offset += 2;
                        $extendedFilePath = substr($recordData, $offset, $xl);
                        $extendedFilePath = self::_encodeUTF16($extendedFilePath, false);
                        $offset += $xl;
                    }
                    $url = str_repeat('..\\', $upLevelCount);
                    $url .= ($sz > 0) ? $extendedFilePath : $shortenedFilePath;
                    $url .= $hasText ? '#' : '';
                    break;
                case 'UNC':
                    return;
                case 'workbook':
                    $url = 'sheet://';
                    break;
                default:
                    return;
            }
            if ($hasText) {
                $tl = self::_GetInt4d($recordData, $offset);
                $offset += 4;
                $text = self::_encodeUTF16(substr($recordData, $offset, 2 * ($tl - 1)), false);
                $url .= $text;
            }
            foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cellRange) as $coordinate) {
                $this->_phpSheet->getCell($coordinate)->getHyperLink()->setUrl($url);
            }
        }
    }
    private function _readDataValidations()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
    }
    private function _readDataValidation()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $options = self::_GetInt4d($recordData, 0);
        $type    = (0x0000000F & $options) >> 0;
        switch ($type) {
            case 0x00:
                $type = PHPExcel_Cell_DataValidation::TYPE_NONE;
                break;
            case 0x01:
                $type = PHPExcel_Cell_DataValidation::TYPE_WHOLE;
                break;
            case 0x02:
                $type = PHPExcel_Cell_DataValidation::TYPE_DECIMAL;
                break;
            case 0x03:
                $type = PHPExcel_Cell_DataValidation::TYPE_LIST;
                break;
            case 0x04:
                $type = PHPExcel_Cell_DataValidation::TYPE_DATE;
                break;
            case 0x05:
                $type = PHPExcel_Cell_DataValidation::TYPE_TIME;
                break;
            case 0x06:
                $type = PHPExcel_Cell_DataValidation::TYPE_TEXTLENGTH;
                break;
            case 0x07:
                $type = PHPExcel_Cell_DataValidation::TYPE_CUSTOM;
                break;
        }
        $errorStyle = (0x00000070 & $options) >> 4;
        switch ($errorStyle) {
            case 0x00:
                $errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP;
                break;
            case 0x01:
                $errorStyle = PHPExcel_Cell_DataValidation::STYLE_WARNING;
                break;
            case 0x02:
                $errorStyle = PHPExcel_Cell_DataValidation::STYLE_INFORMATION;
                break;
        }
        $explicitFormula  = (0x00000080 & $options) >> 7;
        $allowBlank       = (0x00000100 & $options) >> 8;
        $suppressDropDown = (0x00000200 & $options) >> 9;
        $showInputMessage = (0x00040000 & $options) >> 18;
        $showErrorMessage = (0x00080000 & $options) >> 19;
        $operator         = (0x00F00000 & $options) >> 20;
        switch ($operator) {
            case 0x00:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_BETWEEN;
                break;
            case 0x01:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_NOTBETWEEN;
                break;
            case 0x02:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_EQUAL;
                break;
            case 0x03:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_NOTEQUAL;
                break;
            case 0x04:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHAN;
                break;
            case 0x05:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_LESSTHAN;
                break;
            case 0x06:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHANOREQUAL;
                break;
            case 0x07:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_LESSTHANOREQUAL;
                break;
        }
        $offset      = 4;
        $string      = self::_readUnicodeStringLong(substr($recordData, $offset));
        $promptTitle = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];
        $string     = self::_readUnicodeStringLong(substr($recordData, $offset));
        $errorTitle = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];
        $string = self::_readUnicodeStringLong(substr($recordData, $offset));
        $prompt = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];
        $string = self::_readUnicodeStringLong(substr($recordData, $offset));
        $error  = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];
        $sz1 = self::_GetInt2d($recordData, $offset);
        $offset += 2;
        $offset += 2;
        $formula1 = substr($recordData, $offset, $sz1);
        $formula1 = pack('v', $sz1) . $formula1;
        try {
            $formula1 = $this->_getFormulaFromStructure($formula1);
            if ($type == PHPExcel_Cell_DataValidation::TYPE_LIST) {
                $formula1 = str_replace(chr(0), ',', $formula1);
            }
        }
        catch (PHPExcel_Exception $e) {
            return;
        }
        $offset += $sz1;
        $sz2 = self::_GetInt2d($recordData, $offset);
        $offset += 2;
        $offset += 2;
        $formula2 = substr($recordData, $offset, $sz2);
        $formula2 = pack('v', $sz2) . $formula2;
        try {
            $formula2 = $this->_getFormulaFromStructure($formula2);
        }
        catch (PHPExcel_Exception $e) {
            return;
        }
        $offset += $sz2;
        $cellRangeAddressList = $this->_readBIFF8CellRangeAddressList(substr($recordData, $offset));
        $cellRangeAddresses   = $cellRangeAddressList['cellRangeAddresses'];
        foreach ($cellRangeAddresses as $cellRange) {
            $stRange = $this->_phpSheet->shrinkRangeToFit($cellRange);
            $stRange = PHPExcel_Cell::extractAllCellReferencesInRange($stRange);
            foreach ($stRange as $coordinate) {
                $objValidation = $this->_phpSheet->getCell($coordinate)->getDataValidation();
                $objValidation->setType($type);
                $objValidation->setErrorStyle($errorStyle);
                $objValidation->setAllowBlank((bool) $allowBlank);
                $objValidation->setShowInputMessage((bool) $showInputMessage);
                $objValidation->setShowErrorMessage((bool) $showErrorMessage);
                $objValidation->setShowDropDown(!$suppressDropDown);
                $objValidation->setOperator($operator);
                $objValidation->setErrorTitle($errorTitle);
                $objValidation->setError($error);
                $objValidation->setPromptTitle($promptTitle);
                $objValidation->setPrompt($prompt);
                $objValidation->setFormula1($formula1);
                $objValidation->setFormula2($formula2);
            }
        }
    }
    private function _readSheetLayout()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $offset = 0;
        if (!$this->_readDataOnly) {
            $sz = self::_GetInt4d($recordData, 12);
            switch ($sz) {
                case 0x14:
                    $colorIndex = self::_GetInt2d($recordData, 16);
                    $color      = self::_readColor($colorIndex, $this->_palette, $this->_version);
                    $this->_phpSheet->getTabColor()->setRGB($color['rgb']);
                    break;
                case 0x28:
                    return;
                    break;
            }
        }
    }
    private function _readSheetProtection()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        if ($this->_readDataOnly) {
            return;
        }
        $isf = self::_GetInt2d($recordData, 12);
        if ($isf != 2) {
            return;
        }
        $options = self::_GetInt2d($recordData, 19);
        $bool    = (0x0001 & $options) >> 0;
        $this->_phpSheet->getProtection()->setObjects(!$bool);
        $bool = (0x0002 & $options) >> 1;
        $this->_phpSheet->getProtection()->setScenarios(!$bool);
        $bool = (0x0004 & $options) >> 2;
        $this->_phpSheet->getProtection()->setFormatCells(!$bool);
        $bool = (0x0008 & $options) >> 3;
        $this->_phpSheet->getProtection()->setFormatColumns(!$bool);
        $bool = (0x0010 & $options) >> 4;
        $this->_phpSheet->getProtection()->setFormatRows(!$bool);
        $bool = (0x0020 & $options) >> 5;
        $this->_phpSheet->getProtection()->setInsertColumns(!$bool);
        $bool = (0x0040 & $options) >> 6;
        $this->_phpSheet->getProtection()->setInsertRows(!$bool);
        $bool = (0x0080 & $options) >> 7;
        $this->_phpSheet->getProtection()->setInsertHyperlinks(!$bool);
        $bool = (0x0100 & $options) >> 8;
        $this->_phpSheet->getProtection()->setDeleteColumns(!$bool);
        $bool = (0x0200 & $options) >> 9;
        $this->_phpSheet->getProtection()->setDeleteRows(!$bool);
        $bool = (0x0400 & $options) >> 10;
        $this->_phpSheet->getProtection()->setSelectLockedCells(!$bool);
        $bool = (0x0800 & $options) >> 11;
        $this->_phpSheet->getProtection()->setSort(!$bool);
        $bool = (0x1000 & $options) >> 12;
        $this->_phpSheet->getProtection()->setAutoFilter(!$bool);
        $bool = (0x2000 & $options) >> 13;
        $this->_phpSheet->getProtection()->setPivotTables(!$bool);
        $bool = (0x4000 & $options) >> 14;
        $this->_phpSheet->getProtection()->setSelectUnlockedCells(!$bool);
    }
    private function _readRangeProtection()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        $this->_pos += 4 + $length;
        $offset = 0;
        if (!$this->_readDataOnly) {
            $offset += 12;
            $isf = self::_GetInt2d($recordData, 12);
            if ($isf != 2) {
                return;
            }
            $offset += 2;
            $offset += 5;
            $cref = self::_GetInt2d($recordData, 19);
            $offset += 2;
            $offset += 6;
            $cellRanges = array();
            for ($i = 0; $i < $cref; ++$i) {
                try {
                    $cellRange = $this->_readBIFF8CellRangeAddressFixed(substr($recordData, 27 + 8 * $i, 8));
                }
                catch (PHPExcel_Exception $e) {
                    return;
                }
                $cellRanges[] = $cellRange;
                $offset += 8;
            }
            $rgbFeat = substr($recordData, $offset);
            $offset += 4;
            $wPassword = self::_GetInt4d($recordData, $offset);
            $offset += 4;
            if ($cellRanges) {
                $this->_phpSheet->protectCells(implode(' ', $cellRanges), strtoupper(dechex($wPassword)), true);
            }
        }
    }
    private function _readImData()
    {
        $length            = self::_GetInt2d($this->_data, $this->_pos + 2);
        $splicedRecordData = $this->_getSplicedRecordData();
        $recordData        = $splicedRecordData['recordData'];
        $cf                = self::_GetInt2d($recordData, 0);
        $env               = self::_GetInt2d($recordData, 2);
        $lcb               = self::_GetInt4d($recordData, 4);
        $iData             = substr($recordData, 8);
        switch ($cf) {
            case 0x09:
                $bcSize     = self::_GetInt4d($iData, 0);
                $bcWidth    = self::_GetInt2d($iData, 4);
                $bcHeight   = self::_GetInt2d($iData, 6);
                $ih         = imagecreatetruecolor($bcWidth, $bcHeight);
                $bcBitCount = self::_GetInt2d($iData, 10);
                $rgbString  = substr($iData, 12);
                $rgbTriples = array();
                while (strlen($rgbString) > 0) {
                    $rgbTriples[] = unpack('Cb/Cg/Cr', $rgbString);
                    $rgbString    = substr($rgbString, 3);
                }
                $x = 0;
                $y = 0;
                foreach ($rgbTriples as $i => $rgbTriple) {
                    $color = imagecolorallocate($ih, $rgbTriple['r'], $rgbTriple['g'], $rgbTriple['b']);
                    imagesetpixel($ih, $x, $bcHeight - 1 - $y, $color);
                    $x = ($x + 1) % $bcWidth;
                    $y = $y + floor(($x + 1) / $bcWidth);
                }
                $drawing = new PHPExcel_Worksheet_Drawing();
                $drawing->setPath($filename);
                $drawing->setWorksheet($this->_phpSheet);
                break;
            case 0x02:
            case 0x0e:
            default;
                break;
        }
    }
    private function _readContinue()
    {
        $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
        $recordData = substr($this->_data, $this->_pos + 4, $length);
        if ($this->_drawingData == '') {
            $this->_pos += 4 + $length;
            return;
        }
        if ($length < 4) {
            $this->_pos += 4 + $length;
            return;
        }
        $validSplitPoints = array(
            0xF003,
            0xF004,
            0xF00D
        );
        $splitPoint       = self::_GetInt2d($recordData, 2);
        if (in_array($splitPoint, $validSplitPoints)) {
            $splicedRecordData = $this->_getSplicedRecordData();
            $this->_drawingData .= $splicedRecordData['recordData'];
            return;
        }
        $this->_pos += 4 + $length;
    }
    private function _getSplicedRecordData()
    {
        $data             = '';
        $spliceOffsets    = array();
        $i                = 0;
        $spliceOffsets[0] = 0;
        do {
            ++$i;
            $identifier = self::_GetInt2d($this->_data, $this->_pos);
            $length     = self::_GetInt2d($this->_data, $this->_pos + 2);
            $data .= substr($this->_data, $this->_pos + 4, $length);
            $spliceOffsets[$i] = $spliceOffsets[$i - 1] + $length;
            $this->_pos += 4 + $length;
            $nextIdentifier = self::_GetInt2d($this->_data, $this->_pos);
        } while ($nextIdentifier == self::XLS_Type_CONTINUE);
        $splicedData = array(
            'recordData' => $data,
            'spliceOffsets' => $spliceOffsets
        );
        return $splicedData;
    }
    private function _getFormulaFromStructure($formulaStructure, $baseCell = 'A1')
    {
        $sz          = self::_GetInt2d($formulaStructure, 0);
        $formulaData = substr($formulaStructure, 2, $sz);
        if (strlen($formulaStructure) > 2 + $sz) {
            $additionalData = substr($formulaStructure, 2 + $sz);
        } else {
            $additionalData = '';
        }
        return $this->_getFormulaFromData($formulaData, $additionalData, $baseCell);
    }
    private function _getFormulaFromData($formulaData, $additionalData = '', $baseCell = 'A1')
    {
        $tokens = array();
        while (strlen($formulaData) > 0 and $token = $this->_getNextToken($formulaData, $baseCell)) {
            $tokens[]    = $token;
            $formulaData = substr($formulaData, $token['size']);
        }
        $formulaString = $this->_createFormulaFromTokens($tokens, $additionalData);
        return $formulaString;
    }
    private function _createFormulaFromTokens($tokens, $additionalData)
    {
        if (empty($tokens)) {
            return '';
        }
        $formulaStrings = array();
        foreach ($tokens as $token) {
            $space0 = isset($space0) ? $space0 : '';
            $space1 = isset($space1) ? $space1 : '';
            $space2 = isset($space2) ? $space2 : '';
            $space3 = isset($space3) ? $space3 : '';
            $space4 = isset($space4) ? $space4 : '';
            $space5 = isset($space5) ? $space5 : '';
            switch ($token['name']) {
                case 'tAdd':
                case 'tConcat':
                case 'tDiv':
                case 'tEQ':
                case 'tGE':
                case 'tGT':
                case 'tIsect':
                case 'tLE':
                case 'tList':
                case 'tLT':
                case 'tMul':
                case 'tNE':
                case 'tPower':
                case 'tRange':
                case 'tSub':
                    $op2              = array_pop($formulaStrings);
                    $op1              = array_pop($formulaStrings);
                    $formulaStrings[] = "$op1$space1$space0{$token['data']}$op2";
                    unset($space0, $space1);
                    break;
                case 'tUplus':
                case 'tUminus':
                    $op               = array_pop($formulaStrings);
                    $formulaStrings[] = "$space1$space0{$token['data']}$op";
                    unset($space0, $space1);
                    break;
                case 'tPercent':
                    $op               = array_pop($formulaStrings);
                    $formulaStrings[] = "$op$space1$space0{$token['data']}";
                    unset($space0, $space1);
                    break;
                case 'tAttrVolatile':
                case 'tAttrIf':
                case 'tAttrSkip':
                case 'tAttrChoose':
                    break;
                case 'tAttrSpace':
                    switch ($token['data']['spacetype']) {
                        case 'type0':
                            $space0 = str_repeat(' ', $token['data']['spacecount']);
                            break;
                        case 'type1':
                            $space1 = str_repeat("\n", $token['data']['spacecount']);
                            break;
                        case 'type2':
                            $space2 = str_repeat(' ', $token['data']['spacecount']);
                            break;
                        case 'type3':
                            $space3 = str_repeat("\n", $token['data']['spacecount']);
                            break;
                        case 'type4':
                            $space4 = str_repeat(' ', $token['data']['spacecount']);
                            break;
                        case 'type5':
                            $space5 = str_repeat("\n", $token['data']['spacecount']);
                            break;
                    }
                    break;
                case 'tAttrSum':
                    $op               = array_pop($formulaStrings);
                    $formulaStrings[] = "{$space1}{$space0}SUM($op)";
                    unset($space0, $space1);
                    break;
                case 'tFunc':
                case 'tFuncV':
                    if ($token['data']['function'] != '') {
                        $ops = array();
                        for ($i = 0; $i < $token['data']['args']; ++$i) {
                            $ops[] = array_pop($formulaStrings);
                        }
                        $ops              = array_reverse($ops);
                        $formulaStrings[] = "$space1$space0{$token['data']['function']}(" . implode(',', $ops) . ")";
                        unset($space0, $space1);
                    } else {
                        $ops = array();
                        for ($i = 0; $i < $token['data']['args'] - 1; ++$i) {
                            $ops[] = array_pop($formulaStrings);
                        }
                        $ops              = array_reverse($ops);
                        $function         = array_pop($formulaStrings);
                        $formulaStrings[] = "$space1$space0$function(" . implode(',', $ops) . ")";
                        unset($space0, $space1);
                    }
                    break;
                case 'tParen':
                    $expression       = array_pop($formulaStrings);
                    $formulaStrings[] = "$space3$space2($expression$space5$space4)";
                    unset($space2, $space3, $space4, $space5);
                    break;
                case 'tArray':
                    $constantArray    = self::_readBIFF8ConstantArray($additionalData);
                    $formulaStrings[] = $space1 . $space0 . $constantArray['value'];
                    $additionalData   = substr($additionalData, $constantArray['size']);
                    unset($space0, $space1);
                    break;
                case 'tMemArea':
                    $cellRangeAddressList = $this->_readBIFF8CellRangeAddressList($additionalData);
                    $additionalData       = substr($additionalData, $cellRangeAddressList['size']);
                    $formulaStrings[]     = "$space1$space0{$token['data']}";
                    unset($space0, $space1);
                    break;
                case 'tArea':
                case 'tBool':
                case 'tErr':
                case 'tInt':
                case 'tMemErr':
                case 'tMemFunc':
                case 'tMissArg':
                case 'tName':
                case 'tNameX':
                case 'tNum':
                case 'tRef':
                case 'tRef3d':
                case 'tArea3d':
                case 'tRefN':
                case 'tAreaN':
                case 'tStr':
                    $formulaStrings[] = "$space1$space0{$token['data']}";
                    unset($space0, $space1);
                    break;
            }
        }
        $formulaString = $formulaStrings[0];
        return $formulaString;
    }
    private function _getNextToken($formulaData, $baseCell = 'A1')
    {
        $id   = ord($formulaData[0]);
        $name = false;
        switch ($id) {
            case 0x03:
                $name = 'tAdd';
                $size = 1;
                $data = '+';
                break;
            case 0x04:
                $name = 'tSub';
                $size = 1;
                $data = '-';
                break;
            case 0x05:
                $name = 'tMul';
                $size = 1;
                $data = '*';
                break;
            case 0x06:
                $name = 'tDiv';
                $size = 1;
                $data = '/';
                break;
            case 0x07:
                $name = 'tPower';
                $size = 1;
                $data = '^';
                break;
            case 0x08:
                $name = 'tConcat';
                $size = 1;
                $data = '&';
                break;
            case 0x09:
                $name = 'tLT';
                $size = 1;
                $data = '<';
                break;
            case 0x0A:
                $name = 'tLE';
                $size = 1;
                $data = '<=';
                break;
            case 0x0B:
                $name = 'tEQ';
                $size = 1;
                $data = '=';
                break;
            case 0x0C:
                $name = 'tGE';
                $size = 1;
                $data = '>=';
                break;
            case 0x0D:
                $name = 'tGT';
                $size = 1;
                $data = '>';
                break;
            case 0x0E:
                $name = 'tNE';
                $size = 1;
                $data = '<>';
                break;
            case 0x0F:
                $name = 'tIsect';
                $size = 1;
                $data = ' ';
                break;
            case 0x10:
                $name = 'tList';
                $size = 1;
                $data = ',';
                break;
            case 0x11:
                $name = 'tRange';
                $size = 1;
                $data = ':';
                break;
            case 0x12:
                $name = 'tUplus';
                $size = 1;
                $data = '+';
                break;
            case 0x13:
                $name = 'tUminus';
                $size = 1;
                $data = '-';
                break;
            case 0x14:
                $name = 'tPercent';
                $size = 1;
                $data = '%';
                break;
            case 0x15:
                $name = 'tParen';
                $size = 1;
                $data = null;
                break;
            case 0x16:
                $name = 'tMissArg';
                $size = 1;
                $data = '';
                break;
            case 0x17:
                $name   = 'tStr';
                $string = self::_readUnicodeStringShort(substr($formulaData, 1));
                $size   = 1 + $string['size'];
                $data   = self::_UTF8toExcelDoubleQuoted($string['value']);
                break;
            case 0x19:
                switch (ord($formulaData[1])) {
                    case 0x01:
                        $name = 'tAttrVolatile';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x02:
                        $name = 'tAttrIf';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x04:
                        $name = 'tAttrChoose';
                        $nc   = self::_GetInt2d($formulaData, 2);
                        $size = 2 * $nc + 6;
                        $data = null;
                        break;
                    case 0x08:
                        $name = 'tAttrSkip';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x10:
                        $name = 'tAttrSum';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x40:
                    case 0x41:
                        $name = 'tAttrSpace';
                        $size = 4;
                        switch (ord($formulaData[2])) {
                            case 0x00:
                                $spacetype = 'type0';
                                break;
                            case 0x01:
                                $spacetype = 'type1';
                                break;
                            case 0x02:
                                $spacetype = 'type2';
                                break;
                            case 0x03:
                                $spacetype = 'type3';
                                break;
                            case 0x04:
                                $spacetype = 'type4';
                                break;
                            case 0x05:
                                $spacetype = 'type5';
                                break;
                            default:
                                throw new PHPExcel_Reader_Exception('Unrecognized space type in tAttrSpace token');
                                break;
                        }
                        $spacecount = ord($formulaData[3]);
                        $data       = array(
                            'spacetype' => $spacetype,
                            'spacecount' => $spacecount
                        );
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception('Unrecognized attribute flag in tAttr token');
                        break;
                }
                break;
            case 0x1C:
                $name = 'tErr';
                $size = 2;
                $data = self::_mapErrorCode(ord($formulaData[1]));
                break;
            case 0x1D:
                $name = 'tBool';
                $size = 2;
                $data = ord($formulaData[1]) ? 'TRUE' : 'FALSE';
                break;
            case 0x1E:
                $name = 'tInt';
                $size = 3;
                $data = self::_GetInt2d($formulaData, 1);
                break;
            case 0x1F:
                $name = 'tNum';
                $size = 9;
                $data = self::_extractNumber(substr($formulaData, 1));
                $data = str_replace(',', '.', (string) $data);
                break;
            case 0x20:
            case 0x40:
            case 0x60:
                $name = 'tArray';
                $size = 8;
                $data = null;
                break;
            case 0x21:
            case 0x41:
            case 0x61:
                $name = 'tFunc';
                $size = 3;
                switch (self::_GetInt2d($formulaData, 1)) {
                    case 2:
                        $function = 'ISNA';
                        $args     = 1;
                        break;
                    case 3:
                        $function = 'ISERROR';
                        $args     = 1;
                        break;
                    case 10:
                        $function = 'NA';
                        $args     = 0;
                        break;
                    case 15:
                        $function = 'SIN';
                        $args     = 1;
                        break;
                    case 16:
                        $function = 'COS';
                        $args     = 1;
                        break;
                    case 17:
                        $function = 'TAN';
                        $args     = 1;
                        break;
                    case 18:
                        $function = 'ATAN';
                        $args     = 1;
                        break;
                    case 19:
                        $function = 'PI';
                        $args     = 0;
                        break;
                    case 20:
                        $function = 'SQRT';
                        $args     = 1;
                        break;
                    case 21:
                        $function = 'EXP';
                        $args     = 1;
                        break;
                    case 22:
                        $function = 'LN';
                        $args     = 1;
                        break;
                    case 23:
                        $function = 'LOG10';
                        $args     = 1;
                        break;
                    case 24:
                        $function = 'ABS';
                        $args     = 1;
                        break;
                    case 25:
                        $function = 'INT';
                        $args     = 1;
                        break;
                    case 26:
                        $function = 'SIGN';
                        $args     = 1;
                        break;
                    case 27:
                        $function = 'ROUND';
                        $args     = 2;
                        break;
                    case 30:
                        $function = 'REPT';
                        $args     = 2;
                        break;
                    case 31:
                        $function = 'MID';
                        $args     = 3;
                        break;
                    case 32:
                        $function = 'LEN';
                        $args     = 1;
                        break;
                    case 33:
                        $function = 'VALUE';
                        $args     = 1;
                        break;
                    case 34:
                        $function = 'TRUE';
                        $args     = 0;
                        break;
                    case 35:
                        $function = 'FALSE';
                        $args     = 0;
                        break;
                    case 38:
                        $function = 'NOT';
                        $args     = 1;
                        break;
                    case 39:
                        $function = 'MOD';
                        $args     = 2;
                        break;
                    case 40:
                        $function = 'DCOUNT';
                        $args     = 3;
                        break;
                    case 41:
                        $function = 'DSUM';
                        $args     = 3;
                        break;
                    case 42:
                        $function = 'DAVERAGE';
                        $args     = 3;
                        break;
                    case 43:
                        $function = 'DMIN';
                        $args     = 3;
                        break;
                    case 44:
                        $function = 'DMAX';
                        $args     = 3;
                        break;
                    case 45:
                        $function = 'DSTDEV';
                        $args     = 3;
                        break;
                    case 48:
                        $function = 'TEXT';
                        $args     = 2;
                        break;
                    case 61:
                        $function = 'MIRR';
                        $args     = 3;
                        break;
                    case 63:
                        $function = 'RAND';
                        $args     = 0;
                        break;
                    case 65:
                        $function = 'DATE';
                        $args     = 3;
                        break;
                    case 66:
                        $function = 'TIME';
                        $args     = 3;
                        break;
                    case 67:
                        $function = 'DAY';
                        $args     = 1;
                        break;
                    case 68:
                        $function = 'MONTH';
                        $args     = 1;
                        break;
                    case 69:
                        $function = 'YEAR';
                        $args     = 1;
                        break;
                    case 71:
                        $function = 'HOUR';
                        $args     = 1;
                        break;
                    case 72:
                        $function = 'MINUTE';
                        $args     = 1;
                        break;
                    case 73:
                        $function = 'SECOND';
                        $args     = 1;
                        break;
                    case 74:
                        $function = 'NOW';
                        $args     = 0;
                        break;
                    case 75:
                        $function = 'AREAS';
                        $args     = 1;
                        break;
                    case 76:
                        $function = 'ROWS';
                        $args     = 1;
                        break;
                    case 77:
                        $function = 'COLUMNS';
                        $args     = 1;
                        break;
                    case 83:
                        $function = 'TRANSPOSE';
                        $args     = 1;
                        break;
                    case 86:
                        $function = 'TYPE';
                        $args     = 1;
                        break;
                    case 97:
                        $function = 'ATAN2';
                        $args     = 2;
                        break;
                    case 98:
                        $function = 'ASIN';
                        $args     = 1;
                        break;
                    case 99:
                        $function = 'ACOS';
                        $args     = 1;
                        break;
                    case 105:
                        $function = 'ISREF';
                        $args     = 1;
                        break;
                    case 111:
                        $function = 'CHAR';
                        $args     = 1;
                        break;
                    case 112:
                        $function = 'LOWER';
                        $args     = 1;
                        break;
                    case 113:
                        $function = 'UPPER';
                        $args     = 1;
                        break;
                    case 114:
                        $function = 'PROPER';
                        $args     = 1;
                        break;
                    case 117:
                        $function = 'EXACT';
                        $args     = 2;
                        break;
                    case 118:
                        $function = 'TRIM';
                        $args     = 1;
                        break;
                    case 119:
                        $function = 'REPLACE';
                        $args     = 4;
                        break;
                    case 121:
                        $function = 'CODE';
                        $args     = 1;
                        break;
                    case 126:
                        $function = 'ISERR';
                        $args     = 1;
                        break;
                    case 127:
                        $function = 'ISTEXT';
                        $args     = 1;
                        break;
                    case 128:
                        $function = 'ISNUMBER';
                        $args     = 1;
                        break;
                    case 129:
                        $function = 'ISBLANK';
                        $args     = 1;
                        break;
                    case 130:
                        $function = 'T';
                        $args     = 1;
                        break;
                    case 131:
                        $function = 'N';
                        $args     = 1;
                        break;
                    case 140:
                        $function = 'DATEVALUE';
                        $args     = 1;
                        break;
                    case 141:
                        $function = 'TIMEVALUE';
                        $args     = 1;
                        break;
                    case 142:
                        $function = 'SLN';
                        $args     = 3;
                        break;
                    case 143:
                        $function = 'SYD';
                        $args     = 4;
                        break;
                    case 162:
                        $function = 'CLEAN';
                        $args     = 1;
                        break;
                    case 163:
                        $function = 'MDETERM';
                        $args     = 1;
                        break;
                    case 164:
                        $function = 'MINVERSE';
                        $args     = 1;
                        break;
                    case 165:
                        $function = 'MMULT';
                        $args     = 2;
                        break;
                    case 184:
                        $function = 'FACT';
                        $args     = 1;
                        break;
                    case 189:
                        $function = 'DPRODUCT';
                        $args     = 3;
                        break;
                    case 190:
                        $function = 'ISNONTEXT';
                        $args     = 1;
                        break;
                    case 195:
                        $function = 'DSTDEVP';
                        $args     = 3;
                        break;
                    case 196:
                        $function = 'DVARP';
                        $args     = 3;
                        break;
                    case 198:
                        $function = 'ISLOGICAL';
                        $args     = 1;
                        break;
                    case 199:
                        $function = 'DCOUNTA';
                        $args     = 3;
                        break;
                    case 207:
                        $function = 'REPLACEB';
                        $args     = 4;
                        break;
                    case 210:
                        $function = 'MIDB';
                        $args     = 3;
                        break;
                    case 211:
                        $function = 'LENB';
                        $args     = 1;
                        break;
                    case 212:
                        $function = 'ROUNDUP';
                        $args     = 2;
                        break;
                    case 213:
                        $function = 'ROUNDDOWN';
                        $args     = 2;
                        break;
                    case 214:
                        $function = 'ASC';
                        $args     = 1;
                        break;
                    case 215:
                        $function = 'DBCS';
                        $args     = 1;
                        break;
                    case 221:
                        $function = 'TODAY';
                        $args     = 0;
                        break;
                    case 229:
                        $function = 'SINH';
                        $args     = 1;
                        break;
                    case 230:
                        $function = 'COSH';
                        $args     = 1;
                        break;
                    case 231:
                        $function = 'TANH';
                        $args     = 1;
                        break;
                    case 232:
                        $function = 'ASINH';
                        $args     = 1;
                        break;
                    case 233:
                        $function = 'ACOSH';
                        $args     = 1;
                        break;
                    case 234:
                        $function = 'ATANH';
                        $args     = 1;
                        break;
                    case 235:
                        $function = 'DGET';
                        $args     = 3;
                        break;
                    case 244:
                        $function = 'INFO';
                        $args     = 1;
                        break;
                    case 252:
                        $function = 'FREQUENCY';
                        $args     = 2;
                        break;
                    case 261:
                        $function = 'ERROR.TYPE';
                        $args     = 1;
                        break;
                    case 271:
                        $function = 'GAMMALN';
                        $args     = 1;
                        break;
                    case 273:
                        $function = 'BINOMDIST';
                        $args     = 4;
                        break;
                    case 274:
                        $function = 'CHIDIST';
                        $args     = 2;
                        break;
                    case 275:
                        $function = 'CHIINV';
                        $args     = 2;
                        break;
                    case 276:
                        $function = 'COMBIN';
                        $args     = 2;
                        break;
                    case 277:
                        $function = 'CONFIDENCE';
                        $args     = 3;
                        break;
                    case 278:
                        $function = 'CRITBINOM';
                        $args     = 3;
                        break;
                    case 279:
                        $function = 'EVEN';
                        $args     = 1;
                        break;
                    case 280:
                        $function = 'EXPONDIST';
                        $args     = 3;
                        break;
                    case 281:
                        $function = 'FDIST';
                        $args     = 3;
                        break;
                    case 282:
                        $function = 'FINV';
                        $args     = 3;
                        break;
                    case 283:
                        $function = 'FISHER';
                        $args     = 1;
                        break;
                    case 284:
                        $function = 'FISHERINV';
                        $args     = 1;
                        break;
                    case 285:
                        $function = 'FLOOR';
                        $args     = 2;
                        break;
                    case 286:
                        $function = 'GAMMADIST';
                        $args     = 4;
                        break;
                    case 287:
                        $function = 'GAMMAINV';
                        $args     = 3;
                        break;
                    case 288:
                        $function = 'CEILING';
                        $args     = 2;
                        break;
                    case 289:
                        $function = 'HYPGEOMDIST';
                        $args     = 4;
                        break;
                    case 290:
                        $function = 'LOGNORMDIST';
                        $args     = 3;
                        break;
                    case 291:
                        $function = 'LOGINV';
                        $args     = 3;
                        break;
                    case 292:
                        $function = 'NEGBINOMDIST';
                        $args     = 3;
                        break;
                    case 293:
                        $function = 'NORMDIST';
                        $args     = 4;
                        break;
                    case 294:
                        $function = 'NORMSDIST';
                        $args     = 1;
                        break;
                    case 295:
                        $function = 'NORMINV';
                        $args     = 3;
                        break;
                    case 296:
                        $function = 'NORMSINV';
                        $args     = 1;
                        break;
                    case 297:
                        $function = 'STANDARDIZE';
                        $args     = 3;
                        break;
                    case 298:
                        $function = 'ODD';
                        $args     = 1;
                        break;
                    case 299:
                        $function = 'PERMUT';
                        $args     = 2;
                        break;
                    case 300:
                        $function = 'POISSON';
                        $args     = 3;
                        break;
                    case 301:
                        $function = 'TDIST';
                        $args     = 3;
                        break;
                    case 302:
                        $function = 'WEIBULL';
                        $args     = 4;
                        break;
                    case 303:
                        $function = 'SUMXMY2';
                        $args     = 2;
                        break;
                    case 304:
                        $function = 'SUMX2MY2';
                        $args     = 2;
                        break;
                    case 305:
                        $function = 'SUMX2PY2';
                        $args     = 2;
                        break;
                    case 306:
                        $function = 'CHITEST';
                        $args     = 2;
                        break;
                    case 307:
                        $function = 'CORREL';
                        $args     = 2;
                        break;
                    case 308:
                        $function = 'COVAR';
                        $args     = 2;
                        break;
                    case 309:
                        $function = 'FORECAST';
                        $args     = 3;
                        break;
                    case 310:
                        $function = 'FTEST';
                        $args     = 2;
                        break;
                    case 311:
                        $function = 'INTERCEPT';
                        $args     = 2;
                        break;
                    case 312:
                        $function = 'PEARSON';
                        $args     = 2;
                        break;
                    case 313:
                        $function = 'RSQ';
                        $args     = 2;
                        break;
                    case 314:
                        $function = 'STEYX';
                        $args     = 2;
                        break;
                    case 315:
                        $function = 'SLOPE';
                        $args     = 2;
                        break;
                    case 316:
                        $function = 'TTEST';
                        $args     = 4;
                        break;
                    case 325:
                        $function = 'LARGE';
                        $args     = 2;
                        break;
                    case 326:
                        $function = 'SMALL';
                        $args     = 2;
                        break;
                    case 327:
                        $function = 'QUARTILE';
                        $args     = 2;
                        break;
                    case 328:
                        $function = 'PERCENTILE';
                        $args     = 2;
                        break;
                    case 331:
                        $function = 'TRIMMEAN';
                        $args     = 2;
                        break;
                    case 332:
                        $function = 'TINV';
                        $args     = 2;
                        break;
                    case 337:
                        $function = 'POWER';
                        $args     = 2;
                        break;
                    case 342:
                        $function = 'RADIANS';
                        $args     = 1;
                        break;
                    case 343:
                        $function = 'DEGREES';
                        $args     = 1;
                        break;
                    case 346:
                        $function = 'COUNTIF';
                        $args     = 2;
                        break;
                    case 347:
                        $function = 'COUNTBLANK';
                        $args     = 1;
                        break;
                    case 350:
                        $function = 'ISPMT';
                        $args     = 4;
                        break;
                    case 351:
                        $function = 'DATEDIF';
                        $args     = 3;
                        break;
                    case 352:
                        $function = 'DATESTRING';
                        $args     = 1;
                        break;
                    case 353:
                        $function = 'NUMBERSTRING';
                        $args     = 2;
                        break;
                    case 360:
                        $function = 'PHONETIC';
                        $args     = 1;
                        break;
                    case 368:
                        $function = 'BAHTTEXT';
                        $args     = 1;
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception('Unrecognized function in formula');
                        break;
                }
                $data = array(
                    'function' => $function,
                    'args' => $args
                );
                break;
            case 0x22:
            case 0x42:
            case 0x62:
                $name  = 'tFuncV';
                $size  = 4;
                $args  = ord($formulaData[1]);
                $index = self::_GetInt2d($formulaData, 2);
                switch ($index) {
                    case 0:
                        $function = 'COUNT';
                        break;
                    case 1:
                        $function = 'IF';
                        break;
                    case 4:
                        $function = 'SUM';
                        break;
                    case 5:
                        $function = 'AVERAGE';
                        break;
                    case 6:
                        $function = 'MIN';
                        break;
                    case 7:
                        $function = 'MAX';
                        break;
                    case 8:
                        $function = 'ROW';
                        break;
                    case 9:
                        $function = 'COLUMN';
                        break;
                    case 11:
                        $function = 'NPV';
                        break;
                    case 12:
                        $function = 'STDEV';
                        break;
                    case 13:
                        $function = 'DOLLAR';
                        break;
                    case 14:
                        $function = 'FIXED';
                        break;
                    case 28:
                        $function = 'LOOKUP';
                        break;
                    case 29:
                        $function = 'INDEX';
                        break;
                    case 36:
                        $function = 'AND';
                        break;
                    case 37:
                        $function = 'OR';
                        break;
                    case 46:
                        $function = 'VAR';
                        break;
                    case 49:
                        $function = 'LINEST';
                        break;
                    case 50:
                        $function = 'TREND';
                        break;
                    case 51:
                        $function = 'LOGEST';
                        break;
                    case 52:
                        $function = 'GROWTH';
                        break;
                    case 56:
                        $function = 'PV';
                        break;
                    case 57:
                        $function = 'FV';
                        break;
                    case 58:
                        $function = 'NPER';
                        break;
                    case 59:
                        $function = 'PMT';
                        break;
                    case 60:
                        $function = 'RATE';
                        break;
                    case 62:
                        $function = 'IRR';
                        break;
                    case 64:
                        $function = 'MATCH';
                        break;
                    case 70:
                        $function = 'WEEKDAY';
                        break;
                    case 78:
                        $function = 'OFFSET';
                        break;
                    case 82:
                        $function = 'SEARCH';
                        break;
                    case 100:
                        $function = 'CHOOSE';
                        break;
                    case 101:
                        $function = 'HLOOKUP';
                        break;
                    case 102:
                        $function = 'VLOOKUP';
                        break;
                    case 109:
                        $function = 'LOG';
                        break;
                    case 115:
                        $function = 'LEFT';
                        break;
                    case 116:
                        $function = 'RIGHT';
                        break;
                    case 120:
                        $function = 'SUBSTITUTE';
                        break;
                    case 124:
                        $function = 'FIND';
                        break;
                    case 125:
                        $function = 'CELL';
                        break;
                    case 144:
                        $function = 'DDB';
                        break;
                    case 148:
                        $function = 'INDIRECT';
                        break;
                    case 167:
                        $function = 'IPMT';
                        break;
                    case 168:
                        $function = 'PPMT';
                        break;
                    case 169:
                        $function = 'COUNTA';
                        break;
                    case 183:
                        $function = 'PRODUCT';
                        break;
                    case 193:
                        $function = 'STDEVP';
                        break;
                    case 194:
                        $function = 'VARP';
                        break;
                    case 197:
                        $function = 'TRUNC';
                        break;
                    case 204:
                        $function = 'USDOLLAR';
                        break;
                    case 205:
                        $function = 'FINDB';
                        break;
                    case 206:
                        $function = 'SEARCHB';
                        break;
                    case 208:
                        $function = 'LEFTB';
                        break;
                    case 209:
                        $function = 'RIGHTB';
                        break;
                    case 216:
                        $function = 'RANK';
                        break;
                    case 219:
                        $function = 'ADDRESS';
                        break;
                    case 220:
                        $function = 'DAYS360';
                        break;
                    case 222:
                        $function = 'VDB';
                        break;
                    case 227:
                        $function = 'MEDIAN';
                        break;
                    case 228:
                        $function = 'SUMPRODUCT';
                        break;
                    case 247:
                        $function = 'DB';
                        break;
                    case 255:
                        $function = '';
                        break;
                    case 269:
                        $function = 'AVEDEV';
                        break;
                    case 270:
                        $function = 'BETADIST';
                        break;
                    case 272:
                        $function = 'BETAINV';
                        break;
                    case 317:
                        $function = 'PROB';
                        break;
                    case 318:
                        $function = 'DEVSQ';
                        break;
                    case 319:
                        $function = 'GEOMEAN';
                        break;
                    case 320:
                        $function = 'HARMEAN';
                        break;
                    case 321:
                        $function = 'SUMSQ';
                        break;
                    case 322:
                        $function = 'KURT';
                        break;
                    case 323:
                        $function = 'SKEW';
                        break;
                    case 324:
                        $function = 'ZTEST';
                        break;
                    case 329:
                        $function = 'PERCENTRANK';
                        break;
                    case 330:
                        $function = 'MODE';
                        break;
                    case 336:
                        $function = 'CONCATENATE';
                        break;
                    case 344:
                        $function = 'SUBTOTAL';
                        break;
                    case 345:
                        $function = 'SUMIF';
                        break;
                    case 354:
                        $function = 'ROMAN';
                        break;
                    case 358:
                        $function = 'GETPIVOTDATA';
                        break;
                    case 359:
                        $function = 'HYPERLINK';
                        break;
                    case 361:
                        $function = 'AVERAGEA';
                        break;
                    case 362:
                        $function = 'MAXA';
                        break;
                    case 363:
                        $function = 'MINA';
                        break;
                    case 364:
                        $function = 'STDEVPA';
                        break;
                    case 365:
                        $function = 'VARPA';
                        break;
                    case 366:
                        $function = 'STDEVA';
                        break;
                    case 367:
                        $function = 'VARA';
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception('Unrecognized function in formula');
                        break;
                }
                $data = array(
                    'function' => $function,
                    'args' => $args
                );
                break;
            case 0x23:
            case 0x43:
            case 0x63:
                $name             = 'tName';
                $size             = 5;
                $definedNameIndex = self::_GetInt2d($formulaData, 1) - 1;
                $data             = $this->_definedname[$definedNameIndex]['name'];
                break;
            case 0x24:
            case 0x44:
            case 0x64:
                $name = 'tRef';
                $size = 5;
                $data = $this->_readBIFF8CellAddress(substr($formulaData, 1, 4));
                break;
            case 0x25:
            case 0x45:
            case 0x65:
                $name = 'tArea';
                $size = 9;
                $data = $this->_readBIFF8CellRangeAddress(substr($formulaData, 1, 8));
                break;
            case 0x26:
            case 0x46:
            case 0x66:
                $name    = 'tMemArea';
                $subSize = self::_GetInt2d($formulaData, 5);
                $size    = 7 + $subSize;
                $data    = $this->_getFormulaFromData(substr($formulaData, 7, $subSize));
                break;
            case 0x27:
            case 0x47:
            case 0x67:
                $name    = 'tMemErr';
                $subSize = self::_GetInt2d($formulaData, 5);
                $size    = 7 + $subSize;
                $data    = $this->_getFormulaFromData(substr($formulaData, 7, $subSize));
                break;
            case 0x29:
            case 0x49:
            case 0x69:
                $name    = 'tMemFunc';
                $subSize = self::_GetInt2d($formulaData, 1);
                $size    = 3 + $subSize;
                $data    = $this->_getFormulaFromData(substr($formulaData, 3, $subSize));
                break;
            case 0x2C:
            case 0x4C:
            case 0x6C:
                $name = 'tRefN';
                $size = 5;
                $data = $this->_readBIFF8CellAddressB(substr($formulaData, 1, 4), $baseCell);
                break;
            case 0x2D:
            case 0x4D:
            case 0x6D:
                $name = 'tAreaN';
                $size = 9;
                $data = $this->_readBIFF8CellRangeAddressB(substr($formulaData, 1, 8), $baseCell);
                break;
            case 0x39:
            case 0x59:
            case 0x79:
                $name  = 'tNameX';
                $size  = 7;
                $index = self::_GetInt2d($formulaData, 3);
                $data  = $this->_externalNames[$index - 1]['name'];
                break;
            case 0x3A:
            case 0x5A:
            case 0x7A:
                $name = 'tRef3d';
                $size = 7;
                try {
                    $sheetRange  = $this->_readSheetRangeByRefIndex(self::_GetInt2d($formulaData, 1));
                    $cellAddress = $this->_readBIFF8CellAddress(substr($formulaData, 3, 4));
                    $data        = "$sheetRange!$cellAddress";
                }
                catch (PHPExcel_Exception $e) {
                    $data = '#REF!';
                }
                break;
            case 0x3B:
            case 0x5B:
            case 0x7B:
                $name = 'tArea3d';
                $size = 11;
                try {
                    $sheetRange       = $this->_readSheetRangeByRefIndex(self::_GetInt2d($formulaData, 1));
                    $cellRangeAddress = $this->_readBIFF8CellRangeAddress(substr($formulaData, 3, 8));
                    $data             = "$sheetRange!$cellRangeAddress";
                }
                catch (PHPExcel_Exception $e) {
                    $data = '#REF!';
                }
                break;
            default:
                throw new PHPExcel_Reader_Exception('Unrecognized token ' . sprintf('%02X', $id) . ' in formula');
                break;
        }
        return array(
            'id' => $id,
            'name' => $name,
            'size' => $size,
            'data' => $data
        );
    }
    private function _readBIFF8CellAddress($cellAddressStructure)
    {
        $row    = self::_GetInt2d($cellAddressStructure, 0) + 1;
        $column = PHPExcel_Cell::stringFromColumnIndex(0x00FF & self::_GetInt2d($cellAddressStructure, 2));
        if (!(0x4000 & self::_GetInt2d($cellAddressStructure, 2))) {
            $column = '$' . $column;
        }
        if (!(0x8000 & self::_GetInt2d($cellAddressStructure, 2))) {
            $row = '$' . $row;
        }
        return $column . $row;
    }
    private function _readBIFF8CellAddressB($cellAddressStructure, $baseCell = 'A1')
    {
        list($baseCol, $baseRow) = PHPExcel_Cell::coordinateFromString($baseCell);
        $baseCol  = PHPExcel_Cell::columnIndexFromString($baseCol) - 1;
        $rowIndex = self::_GetInt2d($cellAddressStructure, 0);
        $row      = self::_GetInt2d($cellAddressStructure, 0) + 1;
        $colIndex = 0x00FF & self::_GetInt2d($cellAddressStructure, 2);
        if (!(0x4000 & self::_GetInt2d($cellAddressStructure, 2))) {
            $column = PHPExcel_Cell::stringFromColumnIndex($colIndex);
            $column = '$' . $column;
        } else {
            $colIndex = ($colIndex <= 127) ? $colIndex : $colIndex - 256;
            $column   = PHPExcel_Cell::stringFromColumnIndex($baseCol + $colIndex);
        }
        if (!(0x8000 & self::_GetInt2d($cellAddressStructure, 2))) {
            $row = '$' . $row;
        } else {
            $rowIndex = ($rowIndex <= 32767) ? $rowIndex : $rowIndex - 65536;
            $row      = $baseRow + $rowIndex;
        }
        return $column . $row;
    }
    private function _readBIFF5CellRangeAddressFixed($subData)
    {
        $fr = self::_GetInt2d($subData, 0) + 1;
        $lr = self::_GetInt2d($subData, 2) + 1;
        $fc = ord($subData{4});
        $lc = ord($subData{5});
        if ($fr > $lr || $fc > $lc) {
            throw new PHPExcel_Reader_Exception('Not a cell range address');
        }
        $fc = PHPExcel_Cell::stringFromColumnIndex($fc);
        $lc = PHPExcel_Cell::stringFromColumnIndex($lc);
        if ($fr == $lr and $fc == $lc) {
            return "$fc$fr";
        }
        return "$fc$fr:$lc$lr";
    }
    private function _readBIFF8CellRangeAddressFixed($subData)
    {
        $fr = self::_GetInt2d($subData, 0) + 1;
        $lr = self::_GetInt2d($subData, 2) + 1;
        $fc = self::_GetInt2d($subData, 4);
        $lc = self::_GetInt2d($subData, 6);
        if ($fr > $lr || $fc > $lc) {
            throw new PHPExcel_Reader_Exception('Not a cell range address');
        }
        $fc = PHPExcel_Cell::stringFromColumnIndex($fc);
        $lc = PHPExcel_Cell::stringFromColumnIndex($lc);
        if ($fr == $lr and $fc == $lc) {
            return "$fc$fr";
        }
        return "$fc$fr:$lc$lr";
    }
    private function _readBIFF8CellRangeAddress($subData)
    {
        $fr = self::_GetInt2d($subData, 0) + 1;
        $lr = self::_GetInt2d($subData, 2) + 1;
        $fc = PHPExcel_Cell::stringFromColumnIndex(0x00FF & self::_GetInt2d($subData, 4));
        if (!(0x4000 & self::_GetInt2d($subData, 4))) {
            $fc = '$' . $fc;
        }
        if (!(0x8000 & self::_GetInt2d($subData, 4))) {
            $fr = '$' . $fr;
        }
        $lc = PHPExcel_Cell::stringFromColumnIndex(0x00FF & self::_GetInt2d($subData, 6));
        if (!(0x4000 & self::_GetInt2d($subData, 6))) {
            $lc = '$' . $lc;
        }
        if (!(0x8000 & self::_GetInt2d($subData, 6))) {
            $lr = '$' . $lr;
        }
        return "$fc$fr:$lc$lr";
    }
    private function _readBIFF8CellRangeAddressB($subData, $baseCell = 'A1')
    {
        list($baseCol, $baseRow) = PHPExcel_Cell::coordinateFromString($baseCell);
        $baseCol = PHPExcel_Cell::columnIndexFromString($baseCol) - 1;
        $frIndex = self::_GetInt2d($subData, 0);
        $lrIndex = self::_GetInt2d($subData, 2);
        $fcIndex = 0x00FF & self::_GetInt2d($subData, 4);
        if (!(0x4000 & self::_GetInt2d($subData, 4))) {
            $fc = PHPExcel_Cell::stringFromColumnIndex($fcIndex);
            $fc = '$' . $fc;
        } else {
            $fcIndex = ($fcIndex <= 127) ? $fcIndex : $fcIndex - 256;
            $fc      = PHPExcel_Cell::stringFromColumnIndex($baseCol + $fcIndex);
        }
        if (!(0x8000 & self::_GetInt2d($subData, 4))) {
            $fr = $frIndex + 1;
            $fr = '$' . $fr;
        } else {
            $frIndex = ($frIndex <= 32767) ? $frIndex : $frIndex - 65536;
            $fr      = $baseRow + $frIndex;
        }
        $lcIndex = 0x00FF & self::_GetInt2d($subData, 6);
        $lcIndex = ($lcIndex <= 127) ? $lcIndex : $lcIndex - 256;
        $lc      = PHPExcel_Cell::stringFromColumnIndex($baseCol + $lcIndex);
        if (!(0x4000 & self::_GetInt2d($subData, 6))) {
            $lc = PHPExcel_Cell::stringFromColumnIndex($lcIndex);
            $lc = '$' . $lc;
        } else {
            $lcIndex = ($lcIndex <= 127) ? $lcIndex : $lcIndex - 256;
            $lc      = PHPExcel_Cell::stringFromColumnIndex($baseCol + $lcIndex);
        }
        if (!(0x8000 & self::_GetInt2d($subData, 6))) {
            $lr = $lrIndex + 1;
            $lr = '$' . $lr;
        } else {
            $lrIndex = ($lrIndex <= 32767) ? $lrIndex : $lrIndex - 65536;
            $lr      = $baseRow + $lrIndex;
        }
        return "$fc$fr:$lc$lr";
    }
    private function _readBIFF8CellRangeAddressList($subData)
    {
        $cellRangeAddresses = array();
        $nm                 = self::_GetInt2d($subData, 0);
        $offset             = 2;
        for ($i = 0; $i < $nm; ++$i) {
            $cellRangeAddresses[] = $this->_readBIFF8CellRangeAddressFixed(substr($subData, $offset, 8));
            $offset += 8;
        }
        return array(
            'size' => 2 + 8 * $nm,
            'cellRangeAddresses' => $cellRangeAddresses
        );
    }
    private function _readBIFF5CellRangeAddressList($subData)
    {
        $cellRangeAddresses = array();
        $nm                 = self::_GetInt2d($subData, 0);
        $offset             = 2;
        for ($i = 0; $i < $nm; ++$i) {
            $cellRangeAddresses[] = $this->_readBIFF5CellRangeAddressFixed(substr($subData, $offset, 6));
            $offset += 6;
        }
        return array(
            'size' => 2 + 6 * $nm,
            'cellRangeAddresses' => $cellRangeAddresses
        );
    }
    private function _readSheetRangeByRefIndex($index)
    {
        if (isset($this->_ref[$index])) {
            $type = $this->_externalBooks[$this->_ref[$index]['externalBookIndex']]['type'];
            switch ($type) {
                case 'internal':
                    if ($this->_ref[$index]['firstSheetIndex'] == 0xFFFF or $this->_ref[$index]['lastSheetIndex'] == 0xFFFF) {
                        throw new PHPExcel_Reader_Exception('Deleted sheet reference');
                    }
                    $firstSheetName = $this->_sheets[$this->_ref[$index]['firstSheetIndex']]['name'];
                    $lastSheetName  = $this->_sheets[$this->_ref[$index]['lastSheetIndex']]['name'];
                    if ($firstSheetName == $lastSheetName) {
                        $sheetRange = $firstSheetName;
                    } else {
                        $sheetRange = "$firstSheetName:$lastSheetName";
                    }
                    $sheetRange = str_replace("'", "''", $sheetRange);
                    if (preg_match("/[ !\"@#£$%&{()}<>=+'|^,;-]/", $sheetRange)) {
                        $sheetRange = "'$sheetRange'";
                    }
                    return $sheetRange;
                    break;
                default:
                    throw new PHPExcel_Reader_Exception('Excel5 reader only supports internal sheets in fomulas');
                    break;
            }
        }
        return false;
    }
    private static function _readBIFF8ConstantArray($arrayData)
    {
        $nc           = ord($arrayData[0]);
        $nr           = self::_GetInt2d($arrayData, 1);
        $size         = 3;
        $arrayData    = substr($arrayData, 3);
        $matrixChunks = array();
        for ($r = 1; $r <= $nr + 1; ++$r) {
            $items = array();
            for ($c = 1; $c <= $nc + 1; ++$c) {
                $constant  = self::_readBIFF8Constant($arrayData);
                $items[]   = $constant['value'];
                $arrayData = substr($arrayData, $constant['size']);
                $size += $constant['size'];
            }
            $matrixChunks[] = implode(',', $items);
        }
        $matrix = '{' . implode(';', $matrixChunks) . '}';
        return array(
            'value' => $matrix,
            'size' => $size
        );
    }
    private static function _readBIFF8Constant($valueData)
    {
        $identifier = ord($valueData[0]);
        switch ($identifier) {
            case 0x00:
                $value = '';
                $size  = 9;
                break;
            case 0x01:
                $value = self::_extractNumber(substr($valueData, 1, 8));
                $size  = 9;
                break;
            case 0x02:
                $string = self::_readUnicodeStringLong(substr($valueData, 1));
                $value  = '"' . $string['value'] . '"';
                $size   = 1 + $string['size'];
                break;
            case 0x04:
                if (ord($valueData[1])) {
                    $value = 'TRUE';
                } else {
                    $value = 'FALSE';
                }
                $size = 9;
                break;
            case 0x10:
                $value = self::_mapErrorCode(ord($valueData[1]));
                $size  = 9;
                break;
        }
        return array(
            'value' => $value,
            'size' => $size
        );
    }
    private static function _readRGB($rgb)
    {
        $r   = ord($rgb{0});
        $g   = ord($rgb{1});
        $b   = ord($rgb{2});
        $rgb = sprintf('%02X%02X%02X', $r, $g, $b);
        return array(
            'rgb' => $rgb
        );
    }
    private function _readByteStringShort($subData)
    {
        $ln    = ord($subData[0]);
        $value = $this->_decodeCodepage(substr($subData, 1, $ln));
        return array(
            'value' => $value,
            'size' => 1 + $ln
        );
    }
    private function _readByteStringLong($subData)
    {
        $ln    = self::_GetInt2d($subData, 0);
        $value = $this->_decodeCodepage(substr($subData, 2));
        return array(
            'value' => $value,
            'size' => 2 + $ln
        );
    }
    private static function _readUnicodeStringShort($subData)
    {
        $value          = '';
        $characterCount = ord($subData[0]);
        $string         = self::_readUnicodeString(substr($subData, 1), $characterCount);
        $string['size'] += 1;
        return $string;
    }
    private static function _readUnicodeStringLong($subData)
    {
        $value          = '';
        $characterCount = self::_GetInt2d($subData, 0);
        $string         = self::_readUnicodeString(substr($subData, 2), $characterCount);
        $string['size'] += 2;
        return $string;
    }
    private static function _readUnicodeString($subData, $characterCount)
    {
        $value        = '';
        $isCompressed = !((0x01 & ord($subData[0])) >> 0);
        $hasAsian     = (0x04) & ord($subData[0]) >> 2;
        $hasRichText  = (0x08) & ord($subData[0]) >> 3;
        $value        = self::_encodeUTF16(substr($subData, 1, $isCompressed ? $characterCount : 2 * $characterCount), $isCompressed);
        return array(
            'value' => $value,
            'size' => $isCompressed ? 1 + $characterCount : 1 + 2 * $characterCount
        );
    }
    private static function _UTF8toExcelDoubleQuoted($value)
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
    private static function _extractNumber($data)
    {
        $rknumhigh    = self::_GetInt4d($data, 4);
        $rknumlow     = self::_GetInt4d($data, 0);
        $sign         = ($rknumhigh & 0x80000000) >> 31;
        $exp          = (($rknumhigh & 0x7ff00000) >> 20) - 1023;
        $mantissa     = (0x100000 | ($rknumhigh & 0x000fffff));
        $mantissalow1 = ($rknumlow & 0x80000000) >> 31;
        $mantissalow2 = ($rknumlow & 0x7fffffff);
        $value        = $mantissa / pow(2, (20 - $exp));
        if ($mantissalow1 != 0) {
            $value += 1 / pow(2, (21 - $exp));
        }
        $value += $mantissalow2 / pow(2, (52 - $exp));
        if ($sign) {
            $value *= -1;
        }
        return $value;
    }
    private static function _GetIEEE754($rknum)
    {
        if (($rknum & 0x02) != 0) {
            $value = $rknum >> 2;
        } else {
            $sign     = ($rknum & 0x80000000) >> 31;
            $exp      = ($rknum & 0x7ff00000) >> 20;
            $mantissa = (0x100000 | ($rknum & 0x000ffffc));
            $value    = $mantissa / pow(2, (20 - ($exp - 1023)));
            if ($sign) {
                $value = -1 * $value;
            }
        }
        if (($rknum & 0x01) != 0) {
            $value /= 100;
        }
        return $value;
    }
    private static function _encodeUTF16($string, $compressed = '')
    {
        if ($compressed) {
            $string = self::_uncompressByteString($string);
        }
        return PHPExcel_Shared_String::ConvertEncoding($string, 'UTF-8', 'UTF-16LE');
    }
    private static function _uncompressByteString($string)
    {
        $uncompressedString = '';
        $strLen             = strlen($string);
        for ($i = 0; $i < $strLen; ++$i) {
            $uncompressedString .= $string[$i] . "\0";
        }
        return $uncompressedString;
    }
    private function _decodeCodepage($string)
    {
        return PHPExcel_Shared_String::ConvertEncoding($string, 'UTF-8', $this->_codepage);
    }
    public static function _GetInt2d($data, $pos)
    {
        return ord($data[$pos]) | (ord($data[$pos + 1]) << 8);
    }
    public static function _GetInt4d($data, $pos)
    {
        $_or_24 = ord($data[$pos + 3]);
        if ($_or_24 >= 128) {
            $_ord_24 = -abs((256 - $_or_24) << 24);
        } else {
            $_ord_24 = ($_or_24 & 127) << 24;
        }
        return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16) | $_ord_24;
    }
    private static function _readColor($color, $palette, $version)
    {
        if ($color <= 0x07 || $color >= 0x40) {
            return self::_mapBuiltInColor($color);
        } elseif (isset($palette) && isset($palette[$color - 8])) {
            return $palette[$color - 8];
        } else {
            if ($version == self::XLS_BIFF8) {
                return self::_mapColor($color);
            } else {
                return self::_mapColorBIFF5($color);
            }
        }
        return $color;
    }
    private static function _mapBorderStyle($index)
    {
        switch ($index) {
            case 0x00:
                return PHPExcel_Style_Border::BORDER_NONE;
            case 0x01:
                return PHPExcel_Style_Border::BORDER_THIN;
            case 0x02:
                return PHPExcel_Style_Border::BORDER_MEDIUM;
            case 0x03:
                return PHPExcel_Style_Border::BORDER_DASHED;
            case 0x04:
                return PHPExcel_Style_Border::BORDER_DOTTED;
            case 0x05:
                return PHPExcel_Style_Border::BORDER_THICK;
            case 0x06:
                return PHPExcel_Style_Border::BORDER_DOUBLE;
            case 0x07:
                return PHPExcel_Style_Border::BORDER_HAIR;
            case 0x08:
                return PHPExcel_Style_Border::BORDER_MEDIUMDASHED;
            case 0x09:
                return PHPExcel_Style_Border::BORDER_DASHDOT;
            case 0x0A:
                return PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT;
            case 0x0B:
                return PHPExcel_Style_Border::BORDER_DASHDOTDOT;
            case 0x0C:
                return PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT;
            case 0x0D:
                return PHPExcel_Style_Border::BORDER_SLANTDASHDOT;
            default:
                return PHPExcel_Style_Border::BORDER_NONE;
        }
    }
    private static function _mapFillPattern($index)
    {
        switch ($index) {
            case 0x00:
                return PHPExcel_Style_Fill::FILL_NONE;
            case 0x01:
                return PHPExcel_Style_Fill::FILL_SOLID;
            case 0x02:
                return PHPExcel_Style_Fill::FILL_PATTERN_MEDIUMGRAY;
            case 0x03:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY;
            case 0x04:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY;
            case 0x05:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKHORIZONTAL;
            case 0x06:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKVERTICAL;
            case 0x07:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKDOWN;
            case 0x08:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKUP;
            case 0x09:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKGRID;
            case 0x0A:
                return PHPExcel_Style_Fill::FILL_PATTERN_DARKTRELLIS;
            case 0x0B:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTHORIZONTAL;
            case 0x0C:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTVERTICAL;
            case 0x0D:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTDOWN;
            case 0x0E:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTUP;
            case 0x0F:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRID;
            case 0x10:
                return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTTRELLIS;
            case 0x11:
                return PHPExcel_Style_Fill::FILL_PATTERN_GRAY125;
            case 0x12:
                return PHPExcel_Style_Fill::FILL_PATTERN_GRAY0625;
            default:
                return PHPExcel_Style_Fill::FILL_NONE;
        }
    }
    private static function _mapErrorCode($subData)
    {
        switch ($subData) {
            case 0x00:
                return '#NULL!';
                break;
            case 0x07:
                return '#DIV/0!';
                break;
            case 0x0F:
                return '#VALUE!';
                break;
            case 0x17:
                return '#REF!';
                break;
            case 0x1D:
                return '#NAME?';
                break;
            case 0x24:
                return '#NUM!';
                break;
            case 0x2A:
                return '#N/A';
                break;
            default:
                return false;
        }
    }
    private static function _mapBuiltInColor($color)
    {
        switch ($color) {
            case 0x00:
                return array(
                    'rgb' => '000000'
                );
            case 0x01:
                return array(
                    'rgb' => 'FFFFFF'
                );
            case 0x02:
                return array(
                    'rgb' => 'FF0000'
                );
            case 0x03:
                return array(
                    'rgb' => '00FF00'
                );
            case 0x04:
                return array(
                    'rgb' => '0000FF'
                );
            case 0x05:
                return array(
                    'rgb' => 'FFFF00'
                );
            case 0x06:
                return array(
                    'rgb' => 'FF00FF'
                );
            case 0x07:
                return array(
                    'rgb' => '00FFFF'
                );
            case 0x40:
                return array(
                    'rgb' => '000000'
                );
            case 0x41:
                return array(
                    'rgb' => 'FFFFFF'
                );
            default:
                return array(
                    'rgb' => '000000'
                );
        }
    }
    private static function _mapColorBIFF5($subData)
    {
        switch ($subData) {
            case 0x08:
                return array(
                    'rgb' => '000000'
                );
            case 0x09:
                return array(
                    'rgb' => 'FFFFFF'
                );
            case 0x0A:
                return array(
                    'rgb' => 'FF0000'
                );
            case 0x0B:
                return array(
                    'rgb' => '00FF00'
                );
            case 0x0C:
                return array(
                    'rgb' => '0000FF'
                );
            case 0x0D:
                return array(
                    'rgb' => 'FFFF00'
                );
            case 0x0E:
                return array(
                    'rgb' => 'FF00FF'
                );
            case 0x0F:
                return array(
                    'rgb' => '00FFFF'
                );
            case 0x10:
                return array(
                    'rgb' => '800000'
                );
            case 0x11:
                return array(
                    'rgb' => '008000'
                );
            case 0x12:
                return array(
                    'rgb' => '000080'
                );
            case 0x13:
                return array(
                    'rgb' => '808000'
                );
            case 0x14:
                return array(
                    'rgb' => '800080'
                );
            case 0x15:
                return array(
                    'rgb' => '008080'
                );
            case 0x16:
                return array(
                    'rgb' => 'C0C0C0'
                );
            case 0x17:
                return array(
                    'rgb' => '808080'
                );
            case 0x18:
                return array(
                    'rgb' => '8080FF'
                );
            case 0x19:
                return array(
                    'rgb' => '802060'
                );
            case 0x1A:
                return array(
                    'rgb' => 'FFFFC0'
                );
            case 0x1B:
                return array(
                    'rgb' => 'A0E0F0'
                );
            case 0x1C:
                return array(
                    'rgb' => '600080'
                );
            case 0x1D:
                return array(
                    'rgb' => 'FF8080'
                );
            case 0x1E:
                return array(
                    'rgb' => '0080C0'
                );
            case 0x1F:
                return array(
                    'rgb' => 'C0C0FF'
                );
            case 0x20:
                return array(
                    'rgb' => '000080'
                );
            case 0x21:
                return array(
                    'rgb' => 'FF00FF'
                );
            case 0x22:
                return array(
                    'rgb' => 'FFFF00'
                );
            case 0x23:
                return array(
                    'rgb' => '00FFFF'
                );
            case 0x24:
                return array(
                    'rgb' => '800080'
                );
            case 0x25:
                return array(
                    'rgb' => '800000'
                );
            case 0x26:
                return array(
                    'rgb' => '008080'
                );
            case 0x27:
                return array(
                    'rgb' => '0000FF'
                );
            case 0x28:
                return array(
                    'rgb' => '00CFFF'
                );
            case 0x29:
                return array(
                    'rgb' => '69FFFF'
                );
            case 0x2A:
                return array(
                    'rgb' => 'E0FFE0'
                );
            case 0x2B:
                return array(
                    'rgb' => 'FFFF80'
                );
            case 0x2C:
                return array(
                    'rgb' => 'A6CAF0'
                );
            case 0x2D:
                return array(
                    'rgb' => 'DD9CB3'
                );
            case 0x2E:
                return array(
                    'rgb' => 'B38FEE'
                );
            case 0x2F:
                return array(
                    'rgb' => 'E3E3E3'
                );
            case 0x30:
                return array(
                    'rgb' => '2A6FF9'
                );
            case 0x31:
                return array(
                    'rgb' => '3FB8CD'
                );
            case 0x32:
                return array(
                    'rgb' => '488436'
                );
            case 0x33:
                return array(
                    'rgb' => '958C41'
                );
            case 0x34:
                return array(
                    'rgb' => '8E5E42'
                );
            case 0x35:
                return array(
                    'rgb' => 'A0627A'
                );
            case 0x36:
                return array(
                    'rgb' => '624FAC'
                );
            case 0x37:
                return array(
                    'rgb' => '969696'
                );
            case 0x38:
                return array(
                    'rgb' => '1D2FBE'
                );
            case 0x39:
                return array(
                    'rgb' => '286676'
                );
            case 0x3A:
                return array(
                    'rgb' => '004500'
                );
            case 0x3B:
                return array(
                    'rgb' => '453E01'
                );
            case 0x3C:
                return array(
                    'rgb' => '6A2813'
                );
            case 0x3D:
                return array(
                    'rgb' => '85396A'
                );
            case 0x3E:
                return array(
                    'rgb' => '4A3285'
                );
            case 0x3F:
                return array(
                    'rgb' => '424242'
                );
            default:
                return array(
                    'rgb' => '000000'
                );
        }
    }
    private static function _mapColor($subData)
    {
        switch ($subData) {
            case 0x08:
                return array(
                    'rgb' => '000000'
                );
            case 0x09:
                return array(
                    'rgb' => 'FFFFFF'
                );
            case 0x0A:
                return array(
                    'rgb' => 'FF0000'
                );
            case 0x0B:
                return array(
                    'rgb' => '00FF00'
                );
            case 0x0C:
                return array(
                    'rgb' => '0000FF'
                );
            case 0x0D:
                return array(
                    'rgb' => 'FFFF00'
                );
            case 0x0E:
                return array(
                    'rgb' => 'FF00FF'
                );
            case 0x0F:
                return array(
                    'rgb' => '00FFFF'
                );
            case 0x10:
                return array(
                    'rgb' => '800000'
                );
            case 0x11:
                return array(
                    'rgb' => '008000'
                );
            case 0x12:
                return array(
                    'rgb' => '000080'
                );
            case 0x13:
                return array(
                    'rgb' => '808000'
                );
            case 0x14:
                return array(
                    'rgb' => '800080'
                );
            case 0x15:
                return array(
                    'rgb' => '008080'
                );
            case 0x16:
                return array(
                    'rgb' => 'C0C0C0'
                );
            case 0x17:
                return array(
                    'rgb' => '808080'
                );
            case 0x18:
                return array(
                    'rgb' => '9999FF'
                );
            case 0x19:
                return array(
                    'rgb' => '993366'
                );
            case 0x1A:
                return array(
                    'rgb' => 'FFFFCC'
                );
            case 0x1B:
                return array(
                    'rgb' => 'CCFFFF'
                );
            case 0x1C:
                return array(
                    'rgb' => '660066'
                );
            case 0x1D:
                return array(
                    'rgb' => 'FF8080'
                );
            case 0x1E:
                return array(
                    'rgb' => '0066CC'
                );
            case 0x1F:
                return array(
                    'rgb' => 'CCCCFF'
                );
            case 0x20:
                return array(
                    'rgb' => '000080'
                );
            case 0x21:
                return array(
                    'rgb' => 'FF00FF'
                );
            case 0x22:
                return array(
                    'rgb' => 'FFFF00'
                );
            case 0x23:
                return array(
                    'rgb' => '00FFFF'
                );
            case 0x24:
                return array(
                    'rgb' => '800080'
                );
            case 0x25:
                return array(
                    'rgb' => '800000'
                );
            case 0x26:
                return array(
                    'rgb' => '008080'
                );
            case 0x27:
                return array(
                    'rgb' => '0000FF'
                );
            case 0x28:
                return array(
                    'rgb' => '00CCFF'
                );
            case 0x29:
                return array(
                    'rgb' => 'CCFFFF'
                );
            case 0x2A:
                return array(
                    'rgb' => 'CCFFCC'
                );
            case 0x2B:
                return array(
                    'rgb' => 'FFFF99'
                );
            case 0x2C:
                return array(
                    'rgb' => '99CCFF'
                );
            case 0x2D:
                return array(
                    'rgb' => 'FF99CC'
                );
            case 0x2E:
                return array(
                    'rgb' => 'CC99FF'
                );
            case 0x2F:
                return array(
                    'rgb' => 'FFCC99'
                );
            case 0x30:
                return array(
                    'rgb' => '3366FF'
                );
            case 0x31:
                return array(
                    'rgb' => '33CCCC'
                );
            case 0x32:
                return array(
                    'rgb' => '99CC00'
                );
            case 0x33:
                return array(
                    'rgb' => 'FFCC00'
                );
            case 0x34:
                return array(
                    'rgb' => 'FF9900'
                );
            case 0x35:
                return array(
                    'rgb' => 'FF6600'
                );
            case 0x36:
                return array(
                    'rgb' => '666699'
                );
            case 0x37:
                return array(
                    'rgb' => '969696'
                );
            case 0x38:
                return array(
                    'rgb' => '003366'
                );
            case 0x39:
                return array(
                    'rgb' => '339966'
                );
            case 0x3A:
                return array(
                    'rgb' => '003300'
                );
            case 0x3B:
                return array(
                    'rgb' => '333300'
                );
            case 0x3C:
                return array(
                    'rgb' => '993300'
                );
            case 0x3D:
                return array(
                    'rgb' => '993366'
                );
            case 0x3E:
                return array(
                    'rgb' => '333399'
                );
            case 0x3F:
                return array(
                    'rgb' => '333333'
                );
            default:
                return array(
                    'rgb' => '000000'
                );
        }
    }
    private function _parseRichText($is = '')
    {
        $value = new PHPExcel_RichText();
        $value->createText($is);
        return $value;
    }
}