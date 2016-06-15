<?php
require_once(dirname(__FILE__) . '/config/tcpdf_config.php');
class TCPDF
{
    private $tcpdf_version = '5.9.099';
    protected $page;
    protected $n;
    protected $offsets;
    protected $buffer;
    protected $pages = array();
    protected $state;
    protected $compress;
    protected $CurOrientation;
    protected $pagedim = array();
    protected $k;
    protected $fwPt;
    protected $fhPt;
    protected $wPt;
    protected $hPt;
    protected $w;
    protected $h;
    protected $lMargin;
    protected $tMargin;
    protected $rMargin;
    protected $bMargin;
    protected $cell_padding = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);
    protected $cell_margin = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);
    protected $x;
    protected $y;
    protected $lasth;
    protected $LineWidth;
    protected $CoreFonts;
    protected $fonts = array();
    protected $FontFiles = array();
    protected $diffs = array();
    protected $images = array();
    protected $PageAnnots = array();
    protected $links = array();
    protected $FontFamily;
    protected $FontStyle;
    protected $FontAscent;
    protected $FontDescent;
    protected $underline;
    protected $overline;
    protected $CurrentFont;
    protected $FontSizePt;
    protected $FontSize;
    protected $DrawColor;
    protected $FillColor;
    protected $TextColor;
    protected $ColorFlag;
    protected $AutoPageBreak;
    protected $PageBreakTrigger;
    protected $InHeader = false;
    protected $InFooter = false;
    protected $ZoomMode;
    protected $LayoutMode;
    protected $docinfounicode = true;
    protected $title = '';
    protected $subject = '';
    protected $author = '';
    protected $keywords = '';
    protected $creator = '';
    protected $starting_page_number = 1;
    protected $alias_tot_pages = '{:ptp:}';
    protected $alias_num_page = '{:pnp:}';
    protected $alias_group_tot_pages = '{:ptg:}';
    protected $alias_group_num_page = '{:png:}';
    protected $alias_right_shift = '{rsc:';
    protected $img_rb_x;
    protected $img_rb_y;
    protected $imgscale = 1;
    protected $isunicode = false;
    protected $unicode;
    protected $PDFVersion = '1.7';
    protected $header_xobjid = -1;
    protected $header_xobj_autoreset = false;
    protected $header_margin;
    protected $footer_margin;
    protected $original_lMargin;
    protected $original_rMargin;
    protected $header_font;
    protected $footer_font;
    protected $l;
    protected $barcode = false;
    protected $print_header = true;
    protected $print_footer = true;
    protected $header_logo = '';
    protected $header_logo_width = 30;
    protected $header_title = '';
    protected $header_string = '';
    protected $default_table_columns = 4;
    protected $HREF = array();
    protected $fontlist = array();
    protected $fgcolor;
    protected $listordered = array();
    protected $listcount = array();
    protected $listnum = 0;
    protected $listindent = 0;
    protected $listindentlevel = 0;
    protected $bgcolor;
    protected $tempfontsize = 10;
    protected $lispacer = '';
    protected $encoding = 'UTF-8';
    protected $internal_encoding;
    protected $rtl = false;
    protected $tmprtl = false;
    protected $encrypted;
    protected $encryptdata = array();
    protected $last_enc_key;
    protected $last_enc_key_c;
    protected $enc_padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
    protected $file_id;
    protected $outlines = array();
    protected $OutlineRoot;
    protected $javascript = '';
    protected $n_js;
    protected $linethrough;
    protected $ur = array();
    protected $dpi = 72;
    protected $newpagegroup = array();
    protected $pagegroups = array();
    protected $currpagegroup = 0;
    protected $visibility = 'all';
    protected $n_ocg_print;
    protected $n_ocg_view;
    protected $extgstates;
    protected $jpeg_quality;
    protected $cell_height_ratio = K_CELL_HEIGHT_RATIO;
    protected $viewer_preferences;
    protected $PageMode;
    protected $gradients = array();
    protected $intmrk = array();
    protected $bordermrk = array();
    protected $emptypagemrk = array();
    protected $cntmrk = array();
    protected $footerpos = array();
    protected $footerlen = array();
    protected $newline = true;
    protected $endlinex = 0;
    protected $linestyleWidth = '';
    protected $linestyleCap = '0 J';
    protected $linestyleJoin = '0 j';
    protected $linestyleDash = '[] 0 d';
    protected $openMarkedContent = false;
    protected $htmlvspace = 0;
    protected $spot_colors = array();
    protected $lisymbol = '';
    protected $epsmarker = 'x#!#EPS#!#x';
    protected $transfmatrix = array();
    protected $transfmatrix_key = 0;
    protected $booklet = false;
    protected $feps = 0.005;
    protected $tagvspaces = array();
    protected $customlistindent = -1;
    protected $opencell = true;
    protected $embeddedfiles = array();
    protected $premode = false;
    protected $transfmrk = array();
    protected $htmlLinkColorArray = array(0, 0, 255);
    protected $htmlLinkFontStyle = 'U';
    protected $numpages = 0;
    protected $pagelen = array();
    protected $numimages = 0;
    protected $imagekeys = array();
    protected $bufferlen = 0;
    protected $diskcache = false;
    protected $numfonts = 0;
    protected $fontkeys = array();
    protected $font_obj_ids = array();
    protected $pageopen = array();
    protected $default_monospaced_font = 'courier';
    protected $objcopy;
    protected $cache_file_length = array();
    protected $thead = '';
    protected $theadMargins = array();
    protected $cache_UTF8StringToArray = array();
    protected $cache_maxsize_UTF8StringToArray = 8;
    protected $cache_size_UTF8StringToArray = 0;
    protected $sign = false;
    protected $signature_data = array();
    protected $signature_max_length = 11742;
    protected $signature_appearance = array('page' => 1, 'rect' => '0 0 0 0');
    protected $re_spaces = '/[^\S\xa0]/';
    protected $re_space = array('p' => '[^\S\xa0]', 'm' => '');
    protected $sig_obj_id = 0;
    protected $byterange_string = '/ByteRange[0 ********** ********** **********]';
    protected $sig_annot_ref = '***SIGANNREF*** 0 R';
    protected $page_obj_id = array();
    protected $form_obj_id = array();
    protected $default_form_prop = array('lineWidth' => 1, 'borderStyle' => 'solid', 'fillColor' => array(255, 255, 255), 'strokeColor' => array(128, 128, 128));
    protected $js_objects = array();
    protected $form_action = '';
    protected $form_enctype = 'application/x-www-form-urlencoded';
    protected $form_mode = 'post';
    protected $annotation_fonts = array();
    protected $radiobutton_groups = array();
    protected $radio_groups = array();
    protected $textindent = 0;
    protected $start_transaction_page = 0;
    protected $start_transaction_y = 0;
    protected $inthead = false;
    protected $columns = array();
    protected $num_columns = 1;
    protected $current_column = 0;
    protected $column_start_page = 0;
    protected $maxselcol = array('page' => 0, 'column' => 0);
    protected $colxshift = array('x' => 0, 's' => array('H' => 0, 'V' => 0), 'p' => array('L' => 0, 'T' => 0, 'R' => 0, 'B' => 0));
    protected $textrendermode = 0;
    protected $textstrokewidth = 0;
    protected $strokecolor;
    protected $pdfunit = 'mm';
    protected $tocpage = false;
    protected $rasterize_vector_images = false;
    protected $font_subsetting = true;
    protected $default_graphic_vars = array();
    protected $xobjects = array();
    protected $inxobj = false;
    protected $xobjid = '';
    protected $font_stretching = 100;
    protected $font_spacing = 0;
    protected $page_regions = array();
    protected $webcolor = array();
    protected $spotcolor = array();
    protected $pdflayers = false;
    protected $dests = array();
    protected $n_dests;
    protected $svgdir = '';
    protected $svgunit = 'px';
    protected $svggradients = array();
    protected $svggradientid = 0;
    protected $svgdefsmode = false;
    protected $svgdefs = array();
    protected $svgclipmode = false;
    protected $svgclippaths = array();
    protected $svgcliptm = array();
    protected $svgclipid = 0;
    protected $svgtext = '';
    protected $svgtextmode = array();
    protected $svginheritprop = array('clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cursor', 'direction', 'fill', 'fill-opacity', 'fill-rule', 'font', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'glyph-orientation-horizontal', 'glyph-orientation-vertical', 'image-rendering', 'kerning', 'letter-spacing', 'marker', 'marker-end', 'marker-mid', 'marker-start', 'pointer-events', 'shape-rendering', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'text-anchor', 'text-rendering', 'visibility', 'word-spacing', 'writing-mode');
    protected $svgstyles = array(array('alignment-baseline' => 'auto', 'baseline-shift' => 'baseline', 'clip' => 'auto', 'clip-path' => 'none', 'clip-rule' => 'nonzero', 'color' => 'black', 'color-interpolation' => 'sRGB', 'color-interpolation-filters' => 'linearRGB', 'color-profile' => 'auto', 'color-rendering' => 'auto', 'cursor' => 'auto', 'direction' => 'ltr', 'display' => 'inline', 'dominant-baseline' => 'auto', 'enable-background' => 'accumulate', 'fill' => 'black', 'fill-opacity' => 1, 'fill-rule' => 'nonzero', 'filter' => 'none', 'flood-color' => 'black', 'flood-opacity' => 1, 'font' => '', 'font-family' => 'helvetica', 'font-size' => 'medium', 'font-size-adjust' => 'none', 'font-stretch' => 'normal', 'font-style' => 'normal', 'font-variant' => 'normal', 'font-weight' => 'normal', 'glyph-orientation-horizontal' => '0deg', 'glyph-orientation-vertical' => 'auto', 'image-rendering' => 'auto', 'kerning' => 'auto', 'letter-spacing' => 'normal', 'lighting-color' => 'white', 'marker' => '', 'marker-end' => 'none', 'marker-mid' => 'none', 'marker-start' => 'none', 'mask' => 'none', 'opacity' => 1, 'overflow' => 'auto', 'pointer-events' => 'visiblePainted', 'shape-rendering' => 'auto', 'stop-color' => 'black', 'stop-opacity' => 1, 'stroke' => 'none', 'stroke-dasharray' => 'none', 'stroke-dashoffset' => 0, 'stroke-linecap' => 'butt', 'stroke-linejoin' => 'miter', 'stroke-miterlimit' => 4, 'stroke-opacity' => 1, 'stroke-width' => 1, 'text-anchor' => 'start', 'text-decoration' => 'none', 'text-rendering' => 'auto', 'unicode-bidi' => 'normal', 'visibility' => 'visible', 'word-spacing' => 'normal', 'writing-mode' => 'lr-tb', 'text-color' => 'black', 'transfmatrix' => array(1, 0, 0, 1, 0, 0)));
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false)
    {
        if (function_exists('mb_internal_encoding') AND mb_internal_encoding()) {
            $this->internal_encoding = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        }
        require(dirname(__FILE__) . '/htmlcolors.php');
        $this->webcolor = $webcolor;
        if (file_exists(dirname(__FILE__) . '/spotcolors.php')) {
            require(dirname(__FILE__) . '/spotcolors.php');
            $this->spotcolor = $spotcolor;
        } else {
            $this->spotcolor = array();
        }
        require_once(dirname(__FILE__) . '/unicode_data.php');
        $this->unicode      = new TCPDF_UNICODE_DATA();
        $this->font_obj_ids = array();
        $this->page_obj_id  = array();
        $this->form_obj_id  = array();
        $this->diskcache    = $diskcache ? true : false;
        $this->rtl          = false;
        $this->tmprtl       = false;
        $this->_dochecks();
        $this->isunicode    = $unicode;
        $this->page         = 0;
        $this->transfmrk[0] = array();
        $this->pagedim      = array();
        $this->n            = 2;
        $this->buffer       = '';
        $this->pages        = array();
        $this->state        = 0;
        $this->fonts        = array();
        $this->FontFiles    = array();
        $this->diffs        = array();
        $this->images       = array();
        $this->links        = array();
        $this->gradients    = array();
        $this->InFooter     = false;
        $this->lasth        = 0;
        $this->FontFamily   = 'helvetica';
        $this->FontStyle    = '';
        $this->FontSizePt   = 12;
        $this->underline    = false;
        $this->overline     = false;
        $this->linethrough  = false;
        $this->DrawColor    = '0 G';
        $this->FillColor    = '0 g';
        $this->TextColor    = '0 g';
        $this->ColorFlag    = false;
        $this->pdflayers    = false;
        $this->encrypted    = false;
        $this->last_enc_key = '';
        $this->CoreFonts    = array(
            'courier' => 'Courier',
            'courierB' => 'Courier-Bold',
            'courierI' => 'Courier-Oblique',
            'courierBI' => 'Courier-BoldOblique',
            'helvetica' => 'Helvetica',
            'helveticaB' => 'Helvetica-Bold',
            'helveticaI' => 'Helvetica-Oblique',
            'helveticaBI' => 'Helvetica-BoldOblique',
            'times' => 'Times-Roman',
            'timesB' => 'Times-Bold',
            'timesI' => 'Times-Italic',
            'timesBI' => 'Times-BoldItalic',
            'symbol' => 'Symbol',
            'zapfdingbats' => 'ZapfDingbats'
        );
        $this->setPageUnit($unit);
        $this->setPageFormat($format, $orientation);
        $margin = 28.35 / $this->k;
        $this->SetMargins($margin, $margin);
        $cpadding = $margin / 10;
        $this->setCellPaddings($cpadding, 0, $cpadding, 0);
        $this->setCellMargins(0, 0, 0, 0);
        $this->LineWidth      = 0.57 / $this->k;
        $this->linestyleWidth = sprintf('%.2F w', ($this->LineWidth * $this->k));
        $this->linestyleCap   = '0 J';
        $this->linestyleJoin  = '0 j';
        $this->linestyleDash  = '[] 0 d';
        $this->SetAutoPageBreak(true, (2 * $margin));
        $this->SetDisplayMode('fullwidth');
        $this->SetCompression(true);
        $this->PDFVersion = '1.7';
        $this->encoding   = $encoding;
        $this->HREF       = array();
        $this->getFontsList();
        $this->fgcolor              = array(
            'R' => 0,
            'G' => 0,
            'B' => 0
        );
        $this->strokecolor          = array(
            'R' => 0,
            'G' => 0,
            'B' => 0
        );
        $this->bgcolor              = array(
            'R' => 255,
            'G' => 255,
            'B' => 255
        );
        $this->extgstates           = array();
        $this->sign                 = false;
        $this->ur['enabled']        = false;
        $this->ur['document']       = '/FullSave';
        $this->ur['annots']         = '/Create/Delete/Modify/Copy/Import/Export';
        $this->ur['form']           = '/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate';
        $this->ur['signature']      = '/Modify';
        $this->ur['ef']             = '/Create/Delete/Modify/Import';
        $this->ur['formex']         = '';
        $this->signature_appearance = array(
            'page' => 1,
            'rect' => '0 0 0 0'
        );
        $this->jpeg_quality         = 75;
        $this->utf8Bidi(array(
            ''
        ), '');
        $this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
        if ($this->isunicode AND (@preg_match('/\pL/u', 'a') == 1)) {
            $this->setSpacesRE('/[^\S\P{Z}\xa0]/u');
        } else {
            $this->setSpacesRE('/[^\S\xa0]/');
        }
        $this->default_form_prop     = array(
            'lineWidth' => 1,
            'borderStyle' => 'solid',
            'fillColor' => array(
                255,
                255,
                255
            ),
            'strokeColor' => array(
                128,
                128,
                128
            )
        );
        $this->file_id               = md5($this->getRandomSeed('TCPDF' . $orientation . $unit . $format . $encoding));
        $this->default_graphic_vars  = $this->getGraphicVars();
        $this->header_xobj_autoreset = false;
    }
    public function __destruct()
    {
        if (isset($this->internal_encoding) AND !empty($this->internal_encoding)) {
            mb_internal_encoding($this->internal_encoding);
        }
        $this->_destroy(true);
    }
    public function getTCPDFVersion()
    {
        return $this->tcpdf_version;
    }
    public function setPageUnit($unit)
    {
        $unit = strtolower($unit);
        switch ($unit) {
            case 'px':
            case 'pt': {
                $this->k = 1;
                break;
            }
            case 'mm': {
                $this->k = $this->dpi / 25.4;
                break;
            }
            case 'cm': {
                $this->k = $this->dpi / 2.54;
                break;
            }
            case 'in': {
                $this->k = $this->dpi;
                break;
            }
            default: {
                $this->Error('Incorrect unit: ' . $unit);
                break;
            }
        }
        $this->pdfunit = $unit;
        if (isset($this->CurOrientation)) {
            $this->setPageOrientation($this->CurOrientation);
        }
    }
    public function getPageSizeFromFormat($format)
    {
        switch (strtoupper($format)) {
            case 'A0': {
                $pf = array(
                    2383.937,
                    3370.394
                );
                break;
            }
            case 'A1': {
                $pf = array(
                    1683.780,
                    2383.937
                );
                break;
            }
            case 'A2': {
                $pf = array(
                    1190.551,
                    1683.780
                );
                break;
            }
            case 'A3': {
                $pf = array(
                    841.890,
                    1190.551
                );
                break;
            }
            case 'A4': {
                $pf = array(
                    595.276,
                    841.890
                );
                break;
            }
            case 'A5': {
                $pf = array(
                    419.528,
                    595.276
                );
                break;
            }
            case 'A6': {
                $pf = array(
                    297.638,
                    419.528
                );
                break;
            }
            case 'A7': {
                $pf = array(
                    209.764,
                    297.638
                );
                break;
            }
            case 'A8': {
                $pf = array(
                    147.402,
                    209.764
                );
                break;
            }
            case 'A9': {
                $pf = array(
                    104.882,
                    147.402
                );
                break;
            }
            case 'A10': {
                $pf = array(
                    73.701,
                    104.882
                );
                break;
            }
            case 'A11': {
                $pf = array(
                    51.024,
                    73.701
                );
                break;
            }
            case 'A12': {
                $pf = array(
                    36.850,
                    51.024
                );
                break;
            }
            case 'B0': {
                $pf = array(
                    2834.646,
                    4008.189
                );
                break;
            }
            case 'B1': {
                $pf = array(
                    2004.094,
                    2834.646
                );
                break;
            }
            case 'B2': {
                $pf = array(
                    1417.323,
                    2004.094
                );
                break;
            }
            case 'B3': {
                $pf = array(
                    1000.630,
                    1417.323
                );
                break;
            }
            case 'B4': {
                $pf = array(
                    708.661,
                    1000.630
                );
                break;
            }
            case 'B5': {
                $pf = array(
                    498.898,
                    708.661
                );
                break;
            }
            case 'B6': {
                $pf = array(
                    354.331,
                    498.898
                );
                break;
            }
            case 'B7': {
                $pf = array(
                    249.449,
                    354.331
                );
                break;
            }
            case 'B8': {
                $pf = array(
                    175.748,
                    249.449
                );
                break;
            }
            case 'B9': {
                $pf = array(
                    124.724,
                    175.748
                );
                break;
            }
            case 'B10': {
                $pf = array(
                    87.874,
                    124.724
                );
                break;
            }
            case 'B11': {
                $pf = array(
                    62.362,
                    87.874
                );
                break;
            }
            case 'B12': {
                $pf = array(
                    42.520,
                    62.362
                );
                break;
            }
            case 'C0': {
                $pf = array(
                    2599.370,
                    3676.535
                );
                break;
            }
            case 'C1': {
                $pf = array(
                    1836.850,
                    2599.370
                );
                break;
            }
            case 'C2': {
                $pf = array(
                    1298.268,
                    1836.850
                );
                break;
            }
            case 'C3': {
                $pf = array(
                    918.425,
                    1298.268
                );
                break;
            }
            case 'C4': {
                $pf = array(
                    649.134,
                    918.425
                );
                break;
            }
            case 'C5': {
                $pf = array(
                    459.213,
                    649.134
                );
                break;
            }
            case 'C6': {
                $pf = array(
                    323.150,
                    459.213
                );
                break;
            }
            case 'C7': {
                $pf = array(
                    229.606,
                    323.150
                );
                break;
            }
            case 'C8': {
                $pf = array(
                    161.575,
                    229.606
                );
                break;
            }
            case 'C9': {
                $pf = array(
                    113.386,
                    161.575
                );
                break;
            }
            case 'C10': {
                $pf = array(
                    79.370,
                    113.386
                );
                break;
            }
            case 'C11': {
                $pf = array(
                    56.693,
                    79.370
                );
                break;
            }
            case 'C12': {
                $pf = array(
                    39.685,
                    56.693
                );
                break;
            }
            case 'C76': {
                $pf = array(
                    229.606,
                    459.213
                );
                break;
            }
            case 'DL': {
                $pf = array(
                    311.811,
                    623.622
                );
                break;
            }
            case 'E0': {
                $pf = array(
                    2491.654,
                    3517.795
                );
                break;
            }
            case 'E1': {
                $pf = array(
                    1757.480,
                    2491.654
                );
                break;
            }
            case 'E2': {
                $pf = array(
                    1247.244,
                    1757.480
                );
                break;
            }
            case 'E3': {
                $pf = array(
                    878.740,
                    1247.244
                );
                break;
            }
            case 'E4': {
                $pf = array(
                    623.622,
                    878.740
                );
                break;
            }
            case 'E5': {
                $pf = array(
                    439.370,
                    623.622
                );
                break;
            }
            case 'E6': {
                $pf = array(
                    311.811,
                    439.370
                );
                break;
            }
            case 'E7': {
                $pf = array(
                    221.102,
                    311.811
                );
                break;
            }
            case 'E8': {
                $pf = array(
                    155.906,
                    221.102
                );
                break;
            }
            case 'E9': {
                $pf = array(
                    110.551,
                    155.906
                );
                break;
            }
            case 'E10': {
                $pf = array(
                    76.535,
                    110.551
                );
                break;
            }
            case 'E11': {
                $pf = array(
                    53.858,
                    76.535
                );
                break;
            }
            case 'E12': {
                $pf = array(
                    36.850,
                    53.858
                );
                break;
            }
            case 'G0': {
                $pf = array(
                    2715.591,
                    3838.110
                );
                break;
            }
            case 'G1': {
                $pf = array(
                    1919.055,
                    2715.591
                );
                break;
            }
            case 'G2': {
                $pf = array(
                    1357.795,
                    1919.055
                );
                break;
            }
            case 'G3': {
                $pf = array(
                    958.110,
                    1357.795
                );
                break;
            }
            case 'G4': {
                $pf = array(
                    677.480,
                    958.110
                );
                break;
            }
            case 'G5': {
                $pf = array(
                    479.055,
                    677.480
                );
                break;
            }
            case 'G6': {
                $pf = array(
                    337.323,
                    479.055
                );
                break;
            }
            case 'G7': {
                $pf = array(
                    238.110,
                    337.323
                );
                break;
            }
            case 'G8': {
                $pf = array(
                    167.244,
                    238.110
                );
                break;
            }
            case 'G9': {
                $pf = array(
                    119.055,
                    167.244
                );
                break;
            }
            case 'G10': {
                $pf = array(
                    82.205,
                    119.055
                );
                break;
            }
            case 'G11': {
                $pf = array(
                    59.528,
                    82.205
                );
                break;
            }
            case 'G12': {
                $pf = array(
                    39.685,
                    59.528
                );
                break;
            }
            case 'RA0': {
                $pf = array(
                    2437.795,
                    3458.268
                );
                break;
            }
            case 'RA1': {
                $pf = array(
                    1729.134,
                    2437.795
                );
                break;
            }
            case 'RA2': {
                $pf = array(
                    1218.898,
                    1729.134
                );
                break;
            }
            case 'RA3': {
                $pf = array(
                    864.567,
                    1218.898
                );
                break;
            }
            case 'RA4': {
                $pf = array(
                    609.449,
                    864.567
                );
                break;
            }
            case 'SRA0': {
                $pf = array(
                    2551.181,
                    3628.346
                );
                break;
            }
            case 'SRA1': {
                $pf = array(
                    1814.173,
                    2551.181
                );
                break;
            }
            case 'SRA2': {
                $pf = array(
                    1275.591,
                    1814.173
                );
                break;
            }
            case 'SRA3': {
                $pf = array(
                    907.087,
                    1275.591
                );
                break;
            }
            case 'SRA4': {
                $pf = array(
                    637.795,
                    907.087
                );
                break;
            }
            case '4A0': {
                $pf = array(
                    4767.874,
                    6740.787
                );
                break;
            }
            case '2A0': {
                $pf = array(
                    3370.394,
                    4767.874
                );
                break;
            }
            case 'A2_EXTRA': {
                $pf = array(
                    1261.417,
                    1754.646
                );
                break;
            }
            case 'A3+': {
                $pf = array(
                    932.598,
                    1369.134
                );
                break;
            }
            case 'A3_EXTRA': {
                $pf = array(
                    912.756,
                    1261.417
                );
                break;
            }
            case 'A3_SUPER': {
                $pf = array(
                    864.567,
                    1440.000
                );
                break;
            }
            case 'SUPER_A3': {
                $pf = array(
                    864.567,
                    1380.472
                );
                break;
            }
            case 'A4_EXTRA': {
                $pf = array(
                    666.142,
                    912.756
                );
                break;
            }
            case 'A4_SUPER': {
                $pf = array(
                    649.134,
                    912.756
                );
                break;
            }
            case 'SUPER_A4': {
                $pf = array(
                    643.465,
                    1009.134
                );
                break;
            }
            case 'A4_LONG': {
                $pf = array(
                    595.276,
                    986.457
                );
                break;
            }
            case 'F4': {
                $pf = array(
                    595.276,
                    935.433
                );
                break;
            }
            case 'SO_B5_EXTRA': {
                $pf = array(
                    572.598,
                    782.362
                );
                break;
            }
            case 'A5_EXTRA': {
                $pf = array(
                    490.394,
                    666.142
                );
                break;
            }
            case 'ANSI_E': {
                $pf = array(
                    2448.000,
                    3168.000
                );
                break;
            }
            case 'ANSI_D': {
                $pf = array(
                    1584.000,
                    2448.000
                );
                break;
            }
            case 'ANSI_C': {
                $pf = array(
                    1224.000,
                    1584.000
                );
                break;
            }
            case 'ANSI_B': {
                $pf = array(
                    792.000,
                    1224.000
                );
                break;
            }
            case 'ANSI_A': {
                $pf = array(
                    612.000,
                    792.000
                );
                break;
            }
            case 'USLEDGER':
            case 'LEDGER': {
                $pf = array(
                    1224.000,
                    792.000
                );
                break;
            }
            case 'ORGANIZERK':
            case 'BIBLE':
            case 'USTABLOID':
            case 'TABLOID': {
                $pf = array(
                    792.000,
                    1224.000
                );
                break;
            }
            case 'ORGANIZERM':
            case 'USLETTER':
            case 'LETTER': {
                $pf = array(
                    612.000,
                    792.000
                );
                break;
            }
            case 'USLEGAL':
            case 'LEGAL': {
                $pf = array(
                    612.000,
                    1008.000
                );
                break;
            }
            case 'GOVERNMENTLETTER':
            case 'GLETTER': {
                $pf = array(
                    576.000,
                    756.000
                );
                break;
            }
            case 'JUNIORLEGAL':
            case 'JLEGAL': {
                $pf = array(
                    576.000,
                    360.000
                );
                break;
            }
            case 'QUADDEMY': {
                $pf = array(
                    2520.000,
                    3240.000
                );
                break;
            }
            case 'SUPER_B': {
                $pf = array(
                    936.000,
                    1368.000
                );
                break;
            }
            case 'QUARTO': {
                $pf = array(
                    648.000,
                    792.000
                );
                break;
            }
            case 'GOVERNMENTLEGAL':
            case 'FOLIO': {
                $pf = array(
                    612.000,
                    936.000
                );
                break;
            }
            case 'MONARCH':
            case 'EXECUTIVE': {
                $pf = array(
                    522.000,
                    756.000
                );
                break;
            }
            case 'ORGANIZERL':
            case 'STATEMENT':
            case 'MEMO': {
                $pf = array(
                    396.000,
                    612.000
                );
                break;
            }
            case 'FOOLSCAP': {
                $pf = array(
                    595.440,
                    936.000
                );
                break;
            }
            case 'COMPACT': {
                $pf = array(
                    306.000,
                    486.000
                );
                break;
            }
            case 'ORGANIZERJ': {
                $pf = array(
                    198.000,
                    360.000
                );
                break;
            }
            case 'P1': {
                $pf = array(
                    1587.402,
                    2437.795
                );
                break;
            }
            case 'P2': {
                $pf = array(
                    1218.898,
                    1587.402
                );
                break;
            }
            case 'P3': {
                $pf = array(
                    793.701,
                    1218.898
                );
                break;
            }
            case 'P4': {
                $pf = array(
                    609.449,
                    793.701
                );
                break;
            }
            case 'P5': {
                $pf = array(
                    396.850,
                    609.449
                );
                break;
            }
            case 'P6': {
                $pf = array(
                    303.307,
                    396.850
                );
                break;
            }
            case 'ARCH_E': {
                $pf = array(
                    2592.000,
                    3456.000
                );
                break;
            }
            case 'ARCH_E1': {
                $pf = array(
                    2160.000,
                    3024.000
                );
                break;
            }
            case 'ARCH_D': {
                $pf = array(
                    1728.000,
                    2592.000
                );
                break;
            }
            case 'BROADSHEET':
            case 'ARCH_C': {
                $pf = array(
                    1296.000,
                    1728.000
                );
                break;
            }
            case 'ARCH_B': {
                $pf = array(
                    864.000,
                    1296.000
                );
                break;
            }
            case 'ARCH_A': {
                $pf = array(
                    648.000,
                    864.000
                );
                break;
            }
            case 'ANNENV_A2': {
                $pf = array(
                    314.640,
                    414.000
                );
                break;
            }
            case 'ANNENV_A6': {
                $pf = array(
                    342.000,
                    468.000
                );
                break;
            }
            case 'ANNENV_A7': {
                $pf = array(
                    378.000,
                    522.000
                );
                break;
            }
            case 'ANNENV_A8': {
                $pf = array(
                    396.000,
                    584.640
                );
                break;
            }
            case 'ANNENV_A10': {
                $pf = array(
                    450.000,
                    692.640
                );
                break;
            }
            case 'ANNENV_SLIM': {
                $pf = array(
                    278.640,
                    638.640
                );
                break;
            }
            case 'COMMENV_N6_1/4': {
                $pf = array(
                    252.000,
                    432.000
                );
                break;
            }
            case 'COMMENV_N6_3/4': {
                $pf = array(
                    260.640,
                    468.000
                );
                break;
            }
            case 'COMMENV_N8': {
                $pf = array(
                    278.640,
                    540.000
                );
                break;
            }
            case 'COMMENV_N9': {
                $pf = array(
                    278.640,
                    638.640
                );
                break;
            }
            case 'COMMENV_N10': {
                $pf = array(
                    296.640,
                    684.000
                );
                break;
            }
            case 'COMMENV_N11': {
                $pf = array(
                    324.000,
                    746.640
                );
                break;
            }
            case 'COMMENV_N12': {
                $pf = array(
                    342.000,
                    792.000
                );
                break;
            }
            case 'COMMENV_N14': {
                $pf = array(
                    360.000,
                    828.000
                );
                break;
            }
            case 'CATENV_N1': {
                $pf = array(
                    432.000,
                    648.000
                );
                break;
            }
            case 'CATENV_N1_3/4': {
                $pf = array(
                    468.000,
                    684.000
                );
                break;
            }
            case 'CATENV_N2': {
                $pf = array(
                    468.000,
                    720.000
                );
                break;
            }
            case 'CATENV_N3': {
                $pf = array(
                    504.000,
                    720.000
                );
                break;
            }
            case 'CATENV_N6': {
                $pf = array(
                    540.000,
                    756.000
                );
                break;
            }
            case 'CATENV_N7': {
                $pf = array(
                    576.000,
                    792.000
                );
                break;
            }
            case 'CATENV_N8': {
                $pf = array(
                    594.000,
                    810.000
                );
                break;
            }
            case 'CATENV_N9_1/2': {
                $pf = array(
                    612.000,
                    756.000
                );
                break;
            }
            case 'CATENV_N9_3/4': {
                $pf = array(
                    630.000,
                    810.000
                );
                break;
            }
            case 'CATENV_N10_1/2': {
                $pf = array(
                    648.000,
                    864.000
                );
                break;
            }
            case 'CATENV_N12_1/2': {
                $pf = array(
                    684.000,
                    900.000
                );
                break;
            }
            case 'CATENV_N13_1/2': {
                $pf = array(
                    720.000,
                    936.000
                );
                break;
            }
            case 'CATENV_N14_1/4': {
                $pf = array(
                    810.000,
                    882.000
                );
                break;
            }
            case 'CATENV_N14_1/2': {
                $pf = array(
                    828.000,
                    1044.000
                );
                break;
            }
            case 'JIS_B0': {
                $pf = array(
                    2919.685,
                    4127.244
                );
                break;
            }
            case 'JIS_B1': {
                $pf = array(
                    2063.622,
                    2919.685
                );
                break;
            }
            case 'JIS_B2': {
                $pf = array(
                    1459.843,
                    2063.622
                );
                break;
            }
            case 'JIS_B3': {
                $pf = array(
                    1031.811,
                    1459.843
                );
                break;
            }
            case 'JIS_B4': {
                $pf = array(
                    728.504,
                    1031.811
                );
                break;
            }
            case 'JIS_B5': {
                $pf = array(
                    515.906,
                    728.504
                );
                break;
            }
            case 'JIS_B6': {
                $pf = array(
                    362.835,
                    515.906
                );
                break;
            }
            case 'JIS_B7': {
                $pf = array(
                    257.953,
                    362.835
                );
                break;
            }
            case 'JIS_B8': {
                $pf = array(
                    181.417,
                    257.953
                );
                break;
            }
            case 'JIS_B9': {
                $pf = array(
                    127.559,
                    181.417
                );
                break;
            }
            case 'JIS_B10': {
                $pf = array(
                    90.709,
                    127.559
                );
                break;
            }
            case 'JIS_B11': {
                $pf = array(
                    62.362,
                    90.709
                );
                break;
            }
            case 'JIS_B12': {
                $pf = array(
                    45.354,
                    62.362
                );
                break;
            }
            case 'PA0': {
                $pf = array(
                    2381.102,
                    3174.803
                );
                break;
            }
            case 'PA1': {
                $pf = array(
                    1587.402,
                    2381.102
                );
                break;
            }
            case 'PA2': {
                $pf = array(
                    1190.551,
                    1587.402
                );
                break;
            }
            case 'PA3': {
                $pf = array(
                    793.701,
                    1190.551
                );
                break;
            }
            case 'PA4': {
                $pf = array(
                    595.276,
                    793.701
                );
                break;
            }
            case 'PA5': {
                $pf = array(
                    396.850,
                    595.276
                );
                break;
            }
            case 'PA6': {
                $pf = array(
                    297.638,
                    396.850
                );
                break;
            }
            case 'PA7': {
                $pf = array(
                    198.425,
                    297.638
                );
                break;
            }
            case 'PA8': {
                $pf = array(
                    147.402,
                    198.425
                );
                break;
            }
            case 'PA9': {
                $pf = array(
                    99.213,
                    147.402
                );
                break;
            }
            case 'PA10': {
                $pf = array(
                    73.701,
                    99.213
                );
                break;
            }
            case 'PASSPORT_PHOTO': {
                $pf = array(
                    99.213,
                    127.559
                );
                break;
            }
            case 'E': {
                $pf = array(
                    233.858,
                    340.157
                );
                break;
            }
            case 'L':
            case '3R': {
                $pf = array(
                    252.283,
                    360.000
                );
                break;
            }
            case 'KG':
            case '4R': {
                $pf = array(
                    289.134,
                    430.866
                );
                break;
            }
            case '4D': {
                $pf = array(
                    340.157,
                    430.866
                );
                break;
            }
            case '2L':
            case '5R': {
                $pf = array(
                    360.000,
                    504.567
                );
                break;
            }
            case '8P':
            case '6R': {
                $pf = array(
                    430.866,
                    575.433
                );
                break;
            }
            case '6P':
            case '8R': {
                $pf = array(
                    575.433,
                    720.000
                );
                break;
            }
            case '6PW':
            case 'S8R': {
                $pf = array(
                    575.433,
                    864.567
                );
                break;
            }
            case '4P':
            case '10R': {
                $pf = array(
                    720.000,
                    864.567
                );
                break;
            }
            case '4PW':
            case 'S10R': {
                $pf = array(
                    720.000,
                    1080.000
                );
                break;
            }
            case '11R': {
                $pf = array(
                    790.866,
                    1009.134
                );
                break;
            }
            case 'S11R': {
                $pf = array(
                    790.866,
                    1224.567
                );
                break;
            }
            case '12R': {
                $pf = array(
                    864.567,
                    1080.000
                );
                break;
            }
            case 'S12R': {
                $pf = array(
                    864.567,
                    1292.598
                );
                break;
            }
            case 'NEWSPAPER_BROADSHEET': {
                $pf = array(
                    2125.984,
                    1700.787
                );
                break;
            }
            case 'NEWSPAPER_BERLINER': {
                $pf = array(
                    1332.283,
                    892.913
                );
                break;
            }
            case 'NEWSPAPER_TABLOID':
            case 'NEWSPAPER_COMPACT': {
                $pf = array(
                    1218.898,
                    793.701
                );
                break;
            }
            case 'CREDIT_CARD':
            case 'BUSINESS_CARD':
            case 'BUSINESS_CARD_ISO7810': {
                $pf = array(
                    153.014,
                    242.646
                );
                break;
            }
            case 'BUSINESS_CARD_ISO216': {
                $pf = array(
                    147.402,
                    209.764
                );
                break;
            }
            case 'BUSINESS_CARD_IT':
            case 'BUSINESS_CARD_UK':
            case 'BUSINESS_CARD_FR':
            case 'BUSINESS_CARD_DE':
            case 'BUSINESS_CARD_ES': {
                $pf = array(
                    155.906,
                    240.945
                );
                break;
            }
            case 'BUSINESS_CARD_CA':
            case 'BUSINESS_CARD_US': {
                $pf = array(
                    144.567,
                    252.283
                );
                break;
            }
            case 'BUSINESS_CARD_JP': {
                $pf = array(
                    155.906,
                    257.953
                );
                break;
            }
            case 'BUSINESS_CARD_HK': {
                $pf = array(
                    153.071,
                    255.118
                );
                break;
            }
            case 'BUSINESS_CARD_AU':
            case 'BUSINESS_CARD_DK':
            case 'BUSINESS_CARD_SE': {
                $pf = array(
                    155.906,
                    255.118
                );
                break;
            }
            case 'BUSINESS_CARD_RU':
            case 'BUSINESS_CARD_CZ':
            case 'BUSINESS_CARD_FI':
            case 'BUSINESS_CARD_HU':
            case 'BUSINESS_CARD_IL': {
                $pf = array(
                    141.732,
                    255.118
                );
                break;
            }
            case '4SHEET': {
                $pf = array(
                    2880.000,
                    4320.000
                );
                break;
            }
            case '6SHEET': {
                $pf = array(
                    3401.575,
                    5102.362
                );
                break;
            }
            case '12SHEET': {
                $pf = array(
                    8640.000,
                    4320.000
                );
                break;
            }
            case '16SHEET': {
                $pf = array(
                    5760.000,
                    8640.000
                );
                break;
            }
            case '32SHEET': {
                $pf = array(
                    11520.000,
                    8640.000
                );
                break;
            }
            case '48SHEET': {
                $pf = array(
                    17280.000,
                    8640.000
                );
                break;
            }
            case '64SHEET': {
                $pf = array(
                    23040.000,
                    8640.000
                );
                break;
            }
            case '96SHEET': {
                $pf = array(
                    34560.000,
                    8640.000
                );
                break;
            }
            case 'EN_EMPEROR': {
                $pf = array(
                    3456.000,
                    5184.000
                );
                break;
            }
            case 'EN_ANTIQUARIAN': {
                $pf = array(
                    2232.000,
                    3816.000
                );
                break;
            }
            case 'EN_GRAND_EAGLE': {
                $pf = array(
                    2070.000,
                    3024.000
                );
                break;
            }
            case 'EN_DOUBLE_ELEPHANT': {
                $pf = array(
                    1926.000,
                    2880.000
                );
                break;
            }
            case 'EN_ATLAS': {
                $pf = array(
                    1872.000,
                    2448.000
                );
                break;
            }
            case 'EN_COLOMBIER': {
                $pf = array(
                    1692.000,
                    2484.000
                );
                break;
            }
            case 'EN_ELEPHANT': {
                $pf = array(
                    1656.000,
                    2016.000
                );
                break;
            }
            case 'EN_DOUBLE_DEMY': {
                $pf = array(
                    1620.000,
                    2556.000
                );
                break;
            }
            case 'EN_IMPERIAL': {
                $pf = array(
                    1584.000,
                    2160.000
                );
                break;
            }
            case 'EN_PRINCESS': {
                $pf = array(
                    1548.000,
                    2016.000
                );
                break;
            }
            case 'EN_CARTRIDGE': {
                $pf = array(
                    1512.000,
                    1872.000
                );
                break;
            }
            case 'EN_DOUBLE_LARGE_POST': {
                $pf = array(
                    1512.000,
                    2376.000
                );
                break;
            }
            case 'EN_ROYAL': {
                $pf = array(
                    1440.000,
                    1800.000
                );
                break;
            }
            case 'EN_SHEET':
            case 'EN_HALF_POST': {
                $pf = array(
                    1404.000,
                    1692.000
                );
                break;
            }
            case 'EN_SUPER_ROYAL': {
                $pf = array(
                    1368.000,
                    1944.000
                );
                break;
            }
            case 'EN_DOUBLE_POST': {
                $pf = array(
                    1368.000,
                    2196.000
                );
                break;
            }
            case 'EN_MEDIUM': {
                $pf = array(
                    1260.000,
                    1656.000
                );
                break;
            }
            case 'EN_DEMY': {
                $pf = array(
                    1260.000,
                    1620.000
                );
                break;
            }
            case 'EN_LARGE_POST': {
                $pf = array(
                    1188.000,
                    1512.000
                );
                break;
            }
            case 'EN_COPY_DRAUGHT': {
                $pf = array(
                    1152.000,
                    1440.000
                );
                break;
            }
            case 'EN_POST': {
                $pf = array(
                    1116.000,
                    1386.000
                );
                break;
            }
            case 'EN_CROWN': {
                $pf = array(
                    1080.000,
                    1440.000
                );
                break;
            }
            case 'EN_PINCHED_POST': {
                $pf = array(
                    1062.000,
                    1332.000
                );
                break;
            }
            case 'EN_BRIEF': {
                $pf = array(
                    972.000,
                    1152.000
                );
                break;
            }
            case 'EN_FOOLSCAP': {
                $pf = array(
                    972.000,
                    1224.000
                );
                break;
            }
            case 'EN_SMALL_FOOLSCAP': {
                $pf = array(
                    954.000,
                    1188.000
                );
                break;
            }
            case 'EN_POTT': {
                $pf = array(
                    900.000,
                    1080.000
                );
                break;
            }
            case 'BE_GRAND_AIGLE': {
                $pf = array(
                    1984.252,
                    2948.031
                );
                break;
            }
            case 'BE_COLOMBIER': {
                $pf = array(
                    1757.480,
                    2409.449
                );
                break;
            }
            case 'BE_DOUBLE_CARRE': {
                $pf = array(
                    1757.480,
                    2607.874
                );
                break;
            }
            case 'BE_ELEPHANT': {
                $pf = array(
                    1746.142,
                    2182.677
                );
                break;
            }
            case 'BE_PETIT_AIGLE': {
                $pf = array(
                    1700.787,
                    2381.102
                );
                break;
            }
            case 'BE_GRAND_JESUS': {
                $pf = array(
                    1559.055,
                    2069.291
                );
                break;
            }
            case 'BE_JESUS': {
                $pf = array(
                    1530.709,
                    2069.291
                );
                break;
            }
            case 'BE_RAISIN': {
                $pf = array(
                    1417.323,
                    1842.520
                );
                break;
            }
            case 'BE_GRAND_MEDIAN': {
                $pf = array(
                    1303.937,
                    1714.961
                );
                break;
            }
            case 'BE_DOUBLE_POSTE': {
                $pf = array(
                    1233.071,
                    1601.575
                );
                break;
            }
            case 'BE_COQUILLE': {
                $pf = array(
                    1218.898,
                    1587.402
                );
                break;
            }
            case 'BE_PETIT_MEDIAN': {
                $pf = array(
                    1176.378,
                    1502.362
                );
                break;
            }
            case 'BE_RUCHE': {
                $pf = array(
                    1020.472,
                    1303.937
                );
                break;
            }
            case 'BE_PROPATRIA': {
                $pf = array(
                    977.953,
                    1218.898
                );
                break;
            }
            case 'BE_LYS': {
                $pf = array(
                    898.583,
                    1125.354
                );
                break;
            }
            case 'BE_POT': {
                $pf = array(
                    870.236,
                    1088.504
                );
                break;
            }
            case 'BE_ROSETTE': {
                $pf = array(
                    765.354,
                    983.622
                );
                break;
            }
            case 'FR_UNIVERS': {
                $pf = array(
                    2834.646,
                    3685.039
                );
                break;
            }
            case 'FR_DOUBLE_COLOMBIER': {
                $pf = array(
                    2551.181,
                    3571.654
                );
                break;
            }
            case 'FR_GRANDE_MONDE': {
                $pf = array(
                    2551.181,
                    3571.654
                );
                break;
            }
            case 'FR_DOUBLE_SOLEIL': {
                $pf = array(
                    2267.717,
                    3401.575
                );
                break;
            }
            case 'FR_DOUBLE_JESUS': {
                $pf = array(
                    2154.331,
                    3174.803
                );
                break;
            }
            case 'FR_GRAND_AIGLE': {
                $pf = array(
                    2125.984,
                    3004.724
                );
                break;
            }
            case 'FR_PETIT_AIGLE': {
                $pf = array(
                    1984.252,
                    2664.567
                );
                break;
            }
            case 'FR_DOUBLE_RAISIN': {
                $pf = array(
                    1842.520,
                    2834.646
                );
                break;
            }
            case 'FR_JOURNAL': {
                $pf = array(
                    1842.520,
                    2664.567
                );
                break;
            }
            case 'FR_COLOMBIER_AFFICHE': {
                $pf = array(
                    1785.827,
                    2551.181
                );
                break;
            }
            case 'FR_DOUBLE_CAVALIER': {
                $pf = array(
                    1757.480,
                    2607.874
                );
                break;
            }
            case 'FR_CLOCHE': {
                $pf = array(
                    1700.787,
                    2267.717
                );
                break;
            }
            case 'FR_SOLEIL': {
                $pf = array(
                    1700.787,
                    2267.717
                );
                break;
            }
            case 'FR_DOUBLE_CARRE': {
                $pf = array(
                    1587.402,
                    2551.181
                );
                break;
            }
            case 'FR_DOUBLE_COQUILLE': {
                $pf = array(
                    1587.402,
                    2494.488
                );
                break;
            }
            case 'FR_JESUS': {
                $pf = array(
                    1587.402,
                    2154.331
                );
                break;
            }
            case 'FR_RAISIN': {
                $pf = array(
                    1417.323,
                    1842.520
                );
                break;
            }
            case 'FR_CAVALIER': {
                $pf = array(
                    1303.937,
                    1757.480
                );
                break;
            }
            case 'FR_DOUBLE_COURONNE': {
                $pf = array(
                    1303.937,
                    2040.945
                );
                break;
            }
            case 'FR_CARRE': {
                $pf = array(
                    1275.591,
                    1587.402
                );
                break;
            }
            case 'FR_COQUILLE': {
                $pf = array(
                    1247.244,
                    1587.402
                );
                break;
            }
            case 'FR_DOUBLE_TELLIERE': {
                $pf = array(
                    1247.244,
                    1927.559
                );
                break;
            }
            case 'FR_DOUBLE_CLOCHE': {
                $pf = array(
                    1133.858,
                    1700.787
                );
                break;
            }
            case 'FR_DOUBLE_POT': {
                $pf = array(
                    1133.858,
                    1757.480
                );
                break;
            }
            case 'FR_ECU': {
                $pf = array(
                    1133.858,
                    1474.016
                );
                break;
            }
            case 'FR_COURONNE': {
                $pf = array(
                    1020.472,
                    1303.937
                );
                break;
            }
            case 'FR_TELLIERE': {
                $pf = array(
                    963.780,
                    1247.244
                );
                break;
            }
            case 'FR_POT': {
                $pf = array(
                    878.740,
                    1133.858
                );
                break;
            }
            default: {
                $pf = array(
                    595.276,
                    841.890
                );
                break;
            }
        }
        return $pf;
    }
    protected function setPageFormat($format, $orientation = 'P')
    {
        if (!empty($format) AND isset($this->pagedim[$this->page])) {
            unset($this->pagedim[$this->page]);
        }
        if (is_string($format)) {
            $pf         = $this->getPageSizeFromFormat($format);
            $this->fwPt = $pf[0];
            $this->fhPt = $pf[1];
        } else {
            if (isset($format['MediaBox'])) {
                $this->setPageBoxes($this->page, 'MediaBox', $format['MediaBox']['llx'], $format['MediaBox']['lly'], $format['MediaBox']['urx'], $format['MediaBox']['ury'], false);
                $this->fwPt = (($format['MediaBox']['urx'] - $format['MediaBox']['llx']) * $this->k);
                $this->fhPt = (($format['MediaBox']['ury'] - $format['MediaBox']['lly']) * $this->k);
            } else {
                if (isset($format[0]) AND is_numeric($format[0]) AND isset($format[1]) AND is_numeric($format[1])) {
                    $pf = array(
                        ($format[0] * $this->k),
                        ($format[1] * $this->k)
                    );
                } else {
                    if (!isset($format['format'])) {
                        $format['format'] = 'A4';
                    }
                    $pf = $this->getPageSizeFromFormat($format['format']);
                }
                $this->fwPt = $pf[0];
                $this->fhPt = $pf[1];
                $this->setPageBoxes($this->page, 'MediaBox', 0, 0, $this->fwPt, $this->fhPt, true);
            }
            if (isset($format['CropBox'])) {
                $this->setPageBoxes($this->page, 'CropBox', $format['CropBox']['llx'], $format['CropBox']['lly'], $format['CropBox']['urx'], $format['CropBox']['ury'], false);
            }
            if (isset($format['BleedBox'])) {
                $this->setPageBoxes($this->page, 'BleedBox', $format['BleedBox']['llx'], $format['BleedBox']['lly'], $format['BleedBox']['urx'], $format['BleedBox']['ury'], false);
            }
            if (isset($format['TrimBox'])) {
                $this->setPageBoxes($this->page, 'TrimBox', $format['TrimBox']['llx'], $format['TrimBox']['lly'], $format['TrimBox']['urx'], $format['TrimBox']['ury'], false);
            }
            if (isset($format['ArtBox'])) {
                $this->setPageBoxes($this->page, 'ArtBox', $format['ArtBox']['llx'], $format['ArtBox']['lly'], $format['ArtBox']['urx'], $format['ArtBox']['ury'], false);
            }
            if (isset($format['BoxColorInfo'])) {
                $this->pagedim[$this->page]['BoxColorInfo'] = $format['BoxColorInfo'];
            }
            if (isset($format['Rotate']) AND (($format['Rotate'] % 90) == 0)) {
                $this->pagedim[$this->page]['Rotate'] = intval($format['Rotate']);
            }
            if (isset($format['PZ'])) {
                $this->pagedim[$this->page]['PZ'] = floatval($format['PZ']);
            }
            if (isset($format['trans'])) {
                if (isset($format['trans']['Dur'])) {
                    $this->pagedim[$this->page]['trans']['Dur'] = floatval($format['trans']['Dur']);
                }
                $stansition_styles = array(
                    'Split',
                    'Blinds',
                    'Box',
                    'Wipe',
                    'Dissolve',
                    'Glitter',
                    'R',
                    'Fly',
                    'Push',
                    'Cover',
                    'Uncover',
                    'Fade'
                );
                if (isset($format['trans']['S']) AND in_array($format['trans']['S'], $stansition_styles)) {
                    $this->pagedim[$this->page]['trans']['S'] = $format['trans']['S'];
                    $valid_effect                             = array(
                        'Split',
                        'Blinds'
                    );
                    $valid_vals                               = array(
                        'H',
                        'V'
                    );
                    if (isset($format['trans']['Dm']) AND in_array($format['trans']['S'], $valid_effect) AND in_array($format['trans']['Dm'], $valid_vals)) {
                        $this->pagedim[$this->page]['trans']['Dm'] = $format['trans']['Dm'];
                    }
                    $valid_effect = array(
                        'Split',
                        'Box',
                        'Fly'
                    );
                    $valid_vals   = array(
                        'I',
                        'O'
                    );
                    if (isset($format['trans']['M']) AND in_array($format['trans']['S'], $valid_effect) AND in_array($format['trans']['M'], $valid_vals)) {
                        $this->pagedim[$this->page]['trans']['M'] = $format['trans']['M'];
                    }
                    $valid_effect = array(
                        'Wipe',
                        'Glitter',
                        'Fly',
                        'Cover',
                        'Uncover',
                        'Push'
                    );
                    if (isset($format['trans']['Di']) AND in_array($format['trans']['S'], $valid_effect)) {
                        if (((($format['trans']['Di'] == 90) OR ($format['trans']['Di'] == 180)) AND ($format['trans']['S'] == 'Wipe')) OR (($format['trans']['Di'] == 315) AND ($format['trans']['S'] == 'Glitter')) OR (($format['trans']['Di'] == 0) OR ($format['trans']['Di'] == 270))) {
                            $this->pagedim[$this->page]['trans']['Di'] = intval($format['trans']['Di']);
                        }
                    }
                    if (isset($format['trans']['SS']) AND ($format['trans']['S'] == 'Fly')) {
                        $this->pagedim[$this->page]['trans']['SS'] = floatval($format['trans']['SS']);
                    }
                    if (isset($format['trans']['B']) AND ($format['trans']['B'] === true) AND ($format['trans']['S'] == 'Fly')) {
                        $this->pagedim[$this->page]['trans']['B'] = 'true';
                    }
                } else {
                    $this->pagedim[$this->page]['trans']['S'] = 'R';
                }
                if (isset($format['trans']['D'])) {
                    $this->pagedim[$this->page]['trans']['D'] = floatval($format['trans']['D']);
                } else {
                    $this->pagedim[$this->page]['trans']['D'] = 1;
                }
            }
        }
        $this->setPageOrientation($orientation);
    }
    public function setPageBoxes($page, $type, $llx, $lly, $urx, $ury, $points = false)
    {
        if (!isset($this->pagedim[$page])) {
            $this->pagedim[$page] = array();
        }
        $pageboxes = array(
            'MediaBox',
            'CropBox',
            'BleedBox',
            'TrimBox',
            'ArtBox'
        );
        if (!in_array($type, $pageboxes)) {
            return;
        }
        if ($points) {
            $k = 1;
        } else {
            $k = $this->k;
        }
        $this->pagedim[$page][$type]['llx'] = ($llx * $k);
        $this->pagedim[$page][$type]['lly'] = ($lly * $k);
        $this->pagedim[$page][$type]['urx'] = ($urx * $k);
        $this->pagedim[$page][$type]['ury'] = ($ury * $k);
    }
    protected function swapPageBoxCoordinates($page)
    {
        $pageboxes = array(
            'MediaBox',
            'CropBox',
            'BleedBox',
            'TrimBox',
            'ArtBox'
        );
        foreach ($pageboxes as $type) {
            if (isset($this->pagedim[$page][$type])) {
                $tmp                                = $this->pagedim[$page][$type]['llx'];
                $this->pagedim[$page][$type]['llx'] = $this->pagedim[$page][$type]['lly'];
                $this->pagedim[$page][$type]['lly'] = $tmp;
                $tmp                                = $this->pagedim[$page][$type]['urx'];
                $this->pagedim[$page][$type]['urx'] = $this->pagedim[$page][$type]['ury'];
                $this->pagedim[$page][$type]['ury'] = $tmp;
            }
        }
    }
    public function setPageOrientation($orientation, $autopagebreak = '', $bottommargin = '')
    {
        if (!isset($this->pagedim[$this->page]['MediaBox'])) {
            $this->setPageBoxes($this->page, 'MediaBox', 0, 0, $this->fwPt, $this->fhPt, true);
        }
        if (!isset($this->pagedim[$this->page]['CropBox'])) {
            $this->setPageBoxes($this->page, 'CropBox', $this->pagedim[$this->page]['MediaBox']['llx'], $this->pagedim[$this->page]['MediaBox']['lly'], $this->pagedim[$this->page]['MediaBox']['urx'], $this->pagedim[$this->page]['MediaBox']['ury'], true);
        }
        if (!isset($this->pagedim[$this->page]['BleedBox'])) {
            $this->setPageBoxes($this->page, 'BleedBox', $this->pagedim[$this->page]['CropBox']['llx'], $this->pagedim[$this->page]['CropBox']['lly'], $this->pagedim[$this->page]['CropBox']['urx'], $this->pagedim[$this->page]['CropBox']['ury'], true);
        }
        if (!isset($this->pagedim[$this->page]['TrimBox'])) {
            $this->setPageBoxes($this->page, 'TrimBox', $this->pagedim[$this->page]['CropBox']['llx'], $this->pagedim[$this->page]['CropBox']['lly'], $this->pagedim[$this->page]['CropBox']['urx'], $this->pagedim[$this->page]['CropBox']['ury'], true);
        }
        if (!isset($this->pagedim[$this->page]['ArtBox'])) {
            $this->setPageBoxes($this->page, 'ArtBox', $this->pagedim[$this->page]['CropBox']['llx'], $this->pagedim[$this->page]['CropBox']['lly'], $this->pagedim[$this->page]['CropBox']['urx'], $this->pagedim[$this->page]['CropBox']['ury'], true);
        }
        if (!isset($this->pagedim[$this->page]['Rotate'])) {
            $this->pagedim[$this->page]['Rotate'] = 0;
        }
        if (!isset($this->pagedim[$this->page]['PZ'])) {
            $this->pagedim[$this->page]['PZ'] = 1;
        }
        if ($this->fwPt > $this->fhPt) {
            $default_orientation = 'L';
        } else {
            $default_orientation = 'P';
        }
        $valid_orientations = array(
            'P',
            'L'
        );
        if (empty($orientation)) {
            $orientation = $default_orientation;
        } else {
            $orientation = strtoupper($orientation{0});
        }
        if (in_array($orientation, $valid_orientations) AND ($orientation != $default_orientation)) {
            $this->CurOrientation = $orientation;
            $this->wPt            = $this->fhPt;
            $this->hPt            = $this->fwPt;
        } else {
            $this->CurOrientation = $default_orientation;
            $this->wPt            = $this->fwPt;
            $this->hPt            = $this->fhPt;
        }
        if ((abs($this->pagedim[$this->page]['MediaBox']['urx'] - $this->hPt) < $this->feps) AND (abs($this->pagedim[$this->page]['MediaBox']['ury'] - $this->wPt) < $this->feps)) {
            $this->swapPageBoxCoordinates($this->page);
        }
        $this->w = $this->wPt / $this->k;
        $this->h = $this->hPt / $this->k;
        if ($this->empty_string($autopagebreak)) {
            if (isset($this->AutoPageBreak)) {
                $autopagebreak = $this->AutoPageBreak;
            } else {
                $autopagebreak = true;
            }
        }
        if ($this->empty_string($bottommargin)) {
            if (isset($this->bMargin)) {
                $bottommargin = $this->bMargin;
            } else {
                $bottommargin = 2 * 28.35 / $this->k;
            }
        }
        $this->SetAutoPageBreak($autopagebreak, $bottommargin);
        $this->pagedim[$this->page]['w']   = $this->wPt;
        $this->pagedim[$this->page]['h']   = $this->hPt;
        $this->pagedim[$this->page]['wk']  = $this->w;
        $this->pagedim[$this->page]['hk']  = $this->h;
        $this->pagedim[$this->page]['tm']  = $this->tMargin;
        $this->pagedim[$this->page]['bm']  = $bottommargin;
        $this->pagedim[$this->page]['lm']  = $this->lMargin;
        $this->pagedim[$this->page]['rm']  = $this->rMargin;
        $this->pagedim[$this->page]['pb']  = $autopagebreak;
        $this->pagedim[$this->page]['or']  = $this->CurOrientation;
        $this->pagedim[$this->page]['olm'] = $this->original_lMargin;
        $this->pagedim[$this->page]['orm'] = $this->original_rMargin;
    }
    public function setSpacesRE($re = '/[^\S\xa0]/')
    {
        $this->re_spaces = $re;
        $re_parts        = explode('/', $re);
        $this->re_space  = array();
        if (isset($re_parts[1]) AND !empty($re_parts[1])) {
            $this->re_space['p'] = $re_parts[1];
        } else {
            $this->re_space['p'] = '[\s]';
        }
        if (isset($re_parts[2]) AND !empty($re_parts[2])) {
            $this->re_space['m'] = $re_parts[2];
        } else {
            $this->re_space['m'] = '';
        }
    }
    public function setRTL($enable, $resetx = true)
    {
        $enable       = $enable ? true : false;
        $resetx       = ($resetx AND ($enable != $this->rtl));
        $this->rtl    = $enable;
        $this->tmprtl = false;
        if ($resetx) {
            $this->Ln(0);
        }
    }
    public function getRTL()
    {
        return $this->rtl;
    }
    public function setTempRTL($mode)
    {
        $newmode = false;
        switch (strtoupper($mode)) {
            case 'LTR':
            case 'L': {
                if ($this->rtl) {
                    $newmode = 'L';
                }
                break;
            }
            case 'RTL':
            case 'R': {
                if (!$this->rtl) {
                    $newmode = 'R';
                }
                break;
            }
            case false:
            default: {
                $newmode = false;
                break;
            }
        }
        $this->tmprtl = $newmode;
    }
    public function isRTLTextDir()
    {
        return ($this->rtl OR ($this->tmprtl == 'R'));
    }
    public function setLastH($h)
    {
        $this->lasth = $h;
    }
    public function resetLastH()
    {
        $this->lasth = ($this->FontSize * $this->cell_height_ratio) + $this->cell_padding['T'] + $this->cell_padding['B'];
    }
    public function getLastH()
    {
        return $this->lasth;
    }
    public function setImageScale($scale)
    {
        $this->imgscale = $scale;
    }
    public function getImageScale()
    {
        return $this->imgscale;
    }
    public function getPageDimensions($pagenum = '')
    {
        if (empty($pagenum)) {
            $pagenum = $this->page;
        }
        return $this->pagedim[$pagenum];
    }
    public function getPageWidth($pagenum = '')
    {
        if (empty($pagenum)) {
            return $this->w;
        }
        return $this->pagedim[$pagenum]['w'];
    }
    public function getPageHeight($pagenum = '')
    {
        if (empty($pagenum)) {
            return $this->h;
        }
        return $this->pagedim[$pagenum]['h'];
    }
    public function getBreakMargin($pagenum = '')
    {
        if (empty($pagenum)) {
            return $this->bMargin;
        }
        return $this->pagedim[$pagenum]['bm'];
    }
    public function getScaleFactor()
    {
        return $this->k;
    }
    public function SetMargins($left, $top, $right = -1, $keepmargins = false)
    {
        $this->lMargin = $left;
        $this->tMargin = $top;
        if ($right == -1) {
            $right = $left;
        }
        $this->rMargin = $right;
        if ($keepmargins) {
            $this->original_lMargin = $this->lMargin;
            $this->original_rMargin = $this->rMargin;
        }
    }
    public function SetLeftMargin($margin)
    {
        $this->lMargin = $margin;
        if (($this->page > 0) AND ($this->x < $margin)) {
            $this->x = $margin;
        }
    }
    public function SetTopMargin($margin)
    {
        $this->tMargin = $margin;
        if (($this->page > 0) AND ($this->y < $margin)) {
            $this->y = $margin;
        }
    }
    public function SetRightMargin($margin)
    {
        $this->rMargin = $margin;
        if (($this->page > 0) AND ($this->x > ($this->w - $margin))) {
            $this->x = $this->w - $margin;
        }
    }
    public function SetCellPadding($pad)
    {
        if ($pad >= 0) {
            $this->cell_padding['L'] = $pad;
            $this->cell_padding['T'] = $pad;
            $this->cell_padding['R'] = $pad;
            $this->cell_padding['B'] = $pad;
        }
    }
    public function setCellPaddings($left = '', $top = '', $right = '', $bottom = '')
    {
        if (($left !== '') AND ($left >= 0)) {
            $this->cell_padding['L'] = $left;
        }
        if (($top !== '') AND ($top >= 0)) {
            $this->cell_padding['T'] = $top;
        }
        if (($right !== '') AND ($right >= 0)) {
            $this->cell_padding['R'] = $right;
        }
        if (($bottom !== '') AND ($bottom >= 0)) {
            $this->cell_padding['B'] = $bottom;
        }
    }
    public function getCellPaddings()
    {
        return $this->cell_padding;
    }
    public function setCellMargins($left = '', $top = '', $right = '', $bottom = '')
    {
        if (($left !== '') AND ($left >= 0)) {
            $this->cell_margin['L'] = $left;
        }
        if (($top !== '') AND ($top >= 0)) {
            $this->cell_margin['T'] = $top;
        }
        if (($right !== '') AND ($right >= 0)) {
            $this->cell_margin['R'] = $right;
        }
        if (($bottom !== '') AND ($bottom >= 0)) {
            $this->cell_margin['B'] = $bottom;
        }
    }
    public function getCellMargins()
    {
        return $this->cell_margin;
    }
    protected function adjustCellPadding($brd = 0)
    {
        if (empty($brd)) {
            return;
        }
        if (is_string($brd)) {
            $slen   = strlen($brd);
            $newbrd = array();
            for ($i = 0; $i < $slen; ++$i) {
                $newbrd[$brd{$i}] = true;
            }
            $brd = $newbrd;
        } elseif (($brd === 1) OR ($brd === true) OR (is_numeric($brd) AND (intval($brd) > 0))) {
            $brd = array(
                'LRTB' => true
            );
        }
        if (!is_array($brd)) {
            return;
        }
        $cp = $this->cell_padding;
        if (isset($brd['mode'])) {
            $mode = $brd['mode'];
            unset($brd['mode']);
        } else {
            $mode = 'normal';
        }
        foreach ($brd as $border => $style) {
            $line_width = $this->LineWidth;
            if (is_array($style) AND isset($style['width'])) {
                $line_width = $style['width'];
            }
            $adj = 0;
            switch ($mode) {
                case 'ext': {
                    $adj = 0;
                    break;
                }
                case 'int': {
                    $adj = $line_width;
                    break;
                }
                case 'normal':
                default: {
                    $adj = ($line_width / 2);
                    break;
                }
            }
            if ((strpos($border, 'T') !== false) AND ($this->cell_padding['T'] < $adj)) {
                $this->cell_padding['T'] = $adj;
            }
            if ((strpos($border, 'R') !== false) AND ($this->cell_padding['R'] < $adj)) {
                $this->cell_padding['R'] = $adj;
            }
            if ((strpos($border, 'B') !== false) AND ($this->cell_padding['B'] < $adj)) {
                $this->cell_padding['B'] = $adj;
            }
            if ((strpos($border, 'L') !== false) AND ($this->cell_padding['L'] < $adj)) {
                $this->cell_padding['L'] = $adj;
            }
        }
        return array(
            'T' => ($this->cell_padding['T'] - $cp['T']),
            'R' => ($this->cell_padding['R'] - $cp['R']),
            'B' => ($this->cell_padding['B'] - $cp['B']),
            'L' => ($this->cell_padding['L'] - $cp['L'])
        );
    }
    public function SetAutoPageBreak($auto, $margin = 0)
    {
        $this->AutoPageBreak    = $auto;
        $this->bMargin          = $margin;
        $this->PageBreakTrigger = $this->h - $margin;
    }
    public function getAutoPageBreak()
    {
        return $this->AutoPageBreak;
    }
    public function SetDisplayMode($zoom, $layout = 'SinglePage', $mode = 'UseNone')
    {
        if (($zoom == 'fullpage') OR ($zoom == 'fullwidth') OR ($zoom == 'real') OR ($zoom == 'default') OR (!is_string($zoom))) {
            $this->ZoomMode = $zoom;
        } else {
            $this->Error('Incorrect zoom display mode: ' . $zoom);
        }
        switch ($layout) {
            case 'default':
            case 'single':
            case 'SinglePage': {
                $this->LayoutMode = 'SinglePage';
                break;
            }
            case 'continuous':
            case 'OneColumn': {
                $this->LayoutMode = 'OneColumn';
                break;
            }
            case 'two':
            case 'TwoColumnLeft': {
                $this->LayoutMode = 'TwoColumnLeft';
                break;
            }
            case 'TwoColumnRight': {
                $this->LayoutMode = 'TwoColumnRight';
                break;
            }
            case 'TwoPageLeft': {
                $this->LayoutMode = 'TwoPageLeft';
                break;
            }
            case 'TwoPageRight': {
                $this->LayoutMode = 'TwoPageRight';
                break;
            }
            default: {
                $this->LayoutMode = 'SinglePage';
            }
        }
        switch ($mode) {
            case 'UseNone': {
                $this->PageMode = 'UseNone';
                break;
            }
            case 'UseOutlines': {
                $this->PageMode = 'UseOutlines';
                break;
            }
            case 'UseThumbs': {
                $this->PageMode = 'UseThumbs';
                break;
            }
            case 'FullScreen': {
                $this->PageMode = 'FullScreen';
                break;
            }
            case 'UseOC': {
                $this->PageMode = 'UseOC';
                break;
            }
            case '': {
                $this->PageMode = 'UseAttachments';
                break;
            }
            default: {
                $this->PageMode = 'UseNone';
            }
        }
    }
    public function SetCompression($compress)
    {
        if (function_exists('gzcompress')) {
            $this->compress = $compress ? true : false;
        } else {
            $this->compress = false;
        }
    }
    public function SetDocInfoUnicode($unicode = true)
    {
        $this->docinfounicode = $unicode ? true : false;
    }
    public function SetTitle($title)
    {
        $this->title = $title;
    }
    public function SetSubject($subject)
    {
        $this->subject = $subject;
    }
    public function SetAuthor($author)
    {
        $this->author = $author;
    }
    public function SetKeywords($keywords)
    {
        $this->keywords = $keywords;
    }
    public function SetCreator($creator)
    {
        $this->creator = $creator;
    }
    public function Error($msg)
    {
        $this->_destroy(true);
        die('<strong>TCPDF ERROR: </strong>' . $msg);
    }
    public function Open()
    {
        $this->state = 1;
    }
    public function Close()
    {
        if ($this->state == 3) {
            return;
        }
        if ($this->page == 0) {
            $this->AddPage();
        }
        $gvars = $this->getGraphicVars();
        $this->setEqualColumns();
        $this->lastpage(true);
        $this->SetAutoPageBreak(false);
        $this->x       = 0;
        $this->y       = $this->h - (1 / $this->k);
        $this->lMargin = 0;
        $this->_out('q');
        $this->SetFont('helvetica', '', 1);
        $this->setTextRenderingMode(0, false, false);
        $msg = "\x50\x6f\x77\x65\x72\x65\x64\x20\x62\x79\x20\x54\x43\x50\x44\x46\x20\x28\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67\x29";
        $lnk = "\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67";
        $this->Cell(0, 0, $msg, 0, 0, 'L', 0, $lnk, 0, false, 'D', 'B');
        $this->_out('Q');
        $this->setGraphicVars($gvars);
        $this->endPage();
        $this->_enddoc();
        $this->_destroy(false);
    }
    public function setPage($pnum, $resetmargins = false)
    {
        if (($pnum == $this->page) AND ($this->state == 2)) {
            return;
        }
        if (($pnum > 0) AND ($pnum <= $this->numpages)) {
            $this->state            = 2;
            $oldpage                = $this->page;
            $this->page             = $pnum;
            $this->wPt              = $this->pagedim[$this->page]['w'];
            $this->hPt              = $this->pagedim[$this->page]['h'];
            $this->w                = $this->pagedim[$this->page]['wk'];
            $this->h                = $this->pagedim[$this->page]['hk'];
            $this->tMargin          = $this->pagedim[$this->page]['tm'];
            $this->bMargin          = $this->pagedim[$this->page]['bm'];
            $this->original_lMargin = $this->pagedim[$this->page]['olm'];
            $this->original_rMargin = $this->pagedim[$this->page]['orm'];
            $this->AutoPageBreak    = $this->pagedim[$this->page]['pb'];
            $this->CurOrientation   = $this->pagedim[$this->page]['or'];
            $this->SetAutoPageBreak($this->AutoPageBreak, $this->bMargin);
            if ($resetmargins) {
                $this->lMargin = $this->pagedim[$this->page]['olm'];
                $this->rMargin = $this->pagedim[$this->page]['orm'];
                $this->SetY($this->tMargin);
            } else {
                if ($this->pagedim[$this->page]['olm'] != $this->pagedim[$oldpage]['olm']) {
                    $deltam = $this->pagedim[$this->page]['olm'] - $this->pagedim[$this->page]['orm'];
                    $this->lMargin += $deltam;
                    $this->rMargin -= $deltam;
                }
            }
        } else {
            $this->Error('Wrong page number on setPage() function: ' . $pnum);
        }
    }
    public function lastPage($resetmargins = false)
    {
        $this->setPage($this->getNumPages(), $resetmargins);
    }
    public function getPage()
    {
        return $this->page;
    }
    public function getNumPages()
    {
        return $this->numpages;
    }
    public function addTOCPage($orientation = '', $format = '', $keepmargins = false)
    {
        $this->AddPage($orientation, $format, $keepmargins, true);
    }
    public function endTOCPage()
    {
        $this->endPage(true);
    }
    public function AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false)
    {
        if ($this->inxobj) {
            return;
        }
        if (!isset($this->original_lMargin) OR $keepmargins) {
            $this->original_lMargin = $this->lMargin;
        }
        if (!isset($this->original_rMargin) OR $keepmargins) {
            $this->original_rMargin = $this->rMargin;
        }
        $this->endPage();
        $this->startPage($orientation, $format, $tocpage);
    }
    public function endPage($tocpage = false)
    {
        if (($this->page == 0) OR ($this->numpages > $this->page) OR (!$this->pageopen[$this->page])) {
            return;
        }
        $this->setFooter();
        $this->_endpage();
        $this->pageopen[$this->page] = false;
        if ($tocpage) {
            $this->tocpage = false;
        }
    }
    public function startPage($orientation = '', $format = '', $tocpage = false)
    {
        if ($tocpage) {
            $this->tocpage = true;
        }
        if ($this->numpages > $this->page) {
            $this->setPage($this->page + 1);
            $this->SetY($this->tMargin);
            return;
        }
        if ($this->state == 0) {
            $this->Open();
        }
        ++$this->numpages;
        $this->swapMargins($this->booklet);
        $gvars = $this->getGraphicVars();
        $this->_beginpage($orientation, $format);
        $this->pageopen[$this->page] = true;
        $this->setGraphicVars($gvars);
        $this->setPageMark();
        $this->setHeader();
        $this->setGraphicVars($gvars);
        $this->setPageMark();
        $this->setTableHeader();
        $this->emptypagemrk[$this->page] = $this->pagelen[$this->page];
    }
    public function setPageMark()
    {
        $this->intmrk[$this->page]    = $this->pagelen[$this->page];
        $this->bordermrk[$this->page] = $this->intmrk[$this->page];
        $this->setContentMark();
    }
    protected function setContentMark($page = 0)
    {
        if ($page <= 0) {
            $page = $this->page;
        }
        if (isset($this->footerlen[$page])) {
            $this->cntmrk[$page] = $this->pagelen[$page] - $this->footerlen[$page];
        } else {
            $this->cntmrk[$page] = $this->pagelen[$page];
        }
    }
    public function setHeaderData($ln = '', $lw = 0, $ht = '', $hs = '')
    {
        $this->header_logo       = $ln;
        $this->header_logo_width = $lw;
        $this->header_title      = $ht;
        $this->header_string     = $hs;
    }
    public function getHeaderData()
    {
        $ret               = array();
        $ret['logo']       = $this->header_logo;
        $ret['logo_width'] = $this->header_logo_width;
        $ret['title']      = $this->header_title;
        $ret['string']     = $this->header_string;
        return $ret;
    }
    public function setHeaderMargin($hm = 10)
    {
        $this->header_margin = $hm;
    }
    public function getHeaderMargin()
    {
        return $this->header_margin;
    }
    public function setFooterMargin($fm = 10)
    {
        $this->footer_margin = $fm;
    }
    public function getFooterMargin()
    {
        return $this->footer_margin;
    }
    public function setPrintHeader($val = true)
    {
        $this->print_header = $val;
    }
    public function setPrintFooter($val = true)
    {
        $this->print_footer = $val;
    }
    public function getImageRBX()
    {
        return $this->img_rb_x;
    }
    public function getImageRBY()
    {
        return $this->img_rb_y;
    }
    public function resetHeaderTemplate()
    {
        $this->header_xobjid = -1;
    }
    public function setHeaderTemplateAutoreset($val = true)
    {
        $this->header_xobj_autoreset = $val;
    }
    public function Header()
    {
        if ($this->header_xobjid < 0) {
            $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
            $headerfont          = $this->getHeaderFont();
            $headerdata          = $this->getHeaderData();
            $this->y             = $this->header_margin;
            if ($this->rtl) {
                $this->x = $this->w - $this->original_rMargin;
            } else {
                $this->x = $this->original_lMargin;
            }
            if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
                $imgtype = $this->getImageFileType(K_PATH_IMAGES . $headerdata['logo']);
                if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
                    $this->ImageEps(K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width']);
                } elseif ($imgtype == 'svg') {
                    $this->ImageSVG(K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width']);
                } else {
                    $this->Image(K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width']);
                }
                $imgy = $this->getImageRBY();
            } else {
                $imgy = $this->y;
            }
            $cell_height = round(($this->cell_height_ratio * $headerfont[2]) / $this->k, 2);
            if ($this->getRTL()) {
                $header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
            } else {
                $header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
            }
            $cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
            $this->SetTextColor(72, 72, 72);
            $this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
            $this->SetX($header_x);
            $this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);
            $this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
            $this->SetX($header_x);
            $this->MultiCell($cw, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
            $this->SetLineStyle(array(
                'width' => 0.85 / $this->k,
                'cap' => 'butt',
                'join' => 'miter',
                'dash' => 0,
                'color' => array(
                    72,
                    72,
                    72
                )
            ));
            $this->SetY((2.835 / $this->k) + max($imgy, $this->y));
            if ($this->rtl) {
                $this->SetX($this->original_rMargin);
            } else {
                $this->SetX($this->original_lMargin);
            }
            $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
            $this->endTemplate();
        }
        $x  = 0;
        $dx = 0;
        if ($this->booklet AND (($this->page % 2) == 0)) {
            $dx = ($this->original_lMargin - $this->original_rMargin);
        }
        if ($this->rtl) {
            $x = $this->w + $dx;
        } else {
            $x = 0 + $dx;
        }
        $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
        if ($this->header_xobj_autoreset) {
            $this->header_xobjid = -1;
        }
    }
    public function Footer()
    {
        $cur_y = $this->y;
        $this->SetTextColor(0, 0, 0);
        $line_width = 0.85 / $this->k;
        $this->SetLineStyle(array(
            'width' => $line_width,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => array(
                72,
                72,
                72
            )
        ));
        $barcode = $this->getBarcode();
        if (!empty($barcode)) {
            $this->Ln($line_width);
            $barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
            $style         = array(
                'position' => $this->rtl ? 'R' : 'L',
                'align' => $this->rtl ? 'R' : 'L',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0,
                'fgcolor' => array(
                    0,
                    0,
                    0
                ),
                'bgcolor' => false,
                'text' => false
            );
            $this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
        }
        if (empty($this->pagegroups)) {
            $pagenumtxt = $this->l['w_page'] . ' ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
        } else {
            $pagenumtxt = $this->l['w_page'] . ' ' . $this->getPageNumGroupAlias() . ' / ' . $this->getPageGroupAlias();
        }
        $this->SetY($cur_y);
        if ($this->getRTL()) {
            $this->SetX($this->original_rMargin);
            $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
        } else {
            $this->SetX($this->original_lMargin);
            $this->Cell(0, 0, $this->getAliasRightShift() . $pagenumtxt, 'T', 0, 'R');
        }
    }
    protected function setHeader()
    {
        if (!$this->print_header) {
            return;
        }
        $this->InHeader = true;
        $this->setGraphicVars($this->default_graphic_vars);
        $temp_thead        = $this->thead;
        $temp_theadMargins = $this->theadMargins;
        $lasth             = $this->lasth;
        $this->_out('q');
        $this->rMargin = $this->original_rMargin;
        $this->lMargin = $this->original_lMargin;
        $this->SetCellPadding(0);
        if ($this->rtl) {
            $this->SetXY($this->original_rMargin, $this->header_margin);
        } else {
            $this->SetXY($this->original_lMargin, $this->header_margin);
        }
        $this->SetFont($this->header_font[0], $this->header_font[1], $this->header_font[2]);
        $this->Header();
        if ($this->rtl) {
            $this->SetXY($this->original_rMargin, $this->tMargin);
        } else {
            $this->SetXY($this->original_lMargin, $this->tMargin);
        }
        $this->_out('Q');
        $this->lasth        = $lasth;
        $this->thead        = $temp_thead;
        $this->theadMargins = $temp_theadMargins;
        $this->newline      = false;
        $this->InHeader     = false;
    }
    protected function setFooter()
    {
        $this->InFooter               = true;
        $gvars                        = $this->getGraphicVars();
        $this->footerpos[$this->page] = $this->pagelen[$this->page];
        $this->_out("\n");
        if ($this->print_footer) {
            $this->setGraphicVars($this->default_graphic_vars);
            $this->current_column = 0;
            $this->num_columns    = 1;
            $temp_thead           = $this->thead;
            $temp_theadMargins    = $this->theadMargins;
            $lasth                = $this->lasth;
            $this->_out('q');
            $this->rMargin = $this->original_rMargin;
            $this->lMargin = $this->original_lMargin;
            $this->SetCellPadding(0);
            $footer_y = $this->h - $this->footer_margin;
            if ($this->rtl) {
                $this->SetXY($this->original_rMargin, $footer_y);
            } else {
                $this->SetXY($this->original_lMargin, $footer_y);
            }
            $this->SetFont($this->footer_font[0], $this->footer_font[1], $this->footer_font[2]);
            $this->Footer();
            if ($this->rtl) {
                $this->SetXY($this->original_rMargin, $this->tMargin);
            } else {
                $this->SetXY($this->original_lMargin, $this->tMargin);
            }
            $this->_out('Q');
            $this->lasth        = $lasth;
            $this->thead        = $temp_thead;
            $this->theadMargins = $temp_theadMargins;
        }
        $this->setGraphicVars($gvars);
        $this->current_column         = $gvars['current_column'];
        $this->num_columns            = $gvars['num_columns'];
        $this->footerlen[$this->page] = $this->pagelen[$this->page] - $this->footerpos[$this->page] + 1;
        $this->InFooter               = false;
    }
    protected function inPageBody()
    {
        return (($this->InHeader === false) AND ($this->InFooter === false));
    }
    protected function setTableHeader()
    {
        if ($this->num_columns > 1) {
            return;
        }
        if (isset($this->theadMargins['top'])) {
            $this->tMargin                    = $this->theadMargins['top'];
            $this->pagedim[$this->page]['tm'] = $this->tMargin;
            $this->y                          = $this->tMargin;
        }
        if (!$this->empty_string($this->thead) AND (!$this->inthead)) {
            $prev_lMargin       = $this->lMargin;
            $prev_rMargin       = $this->rMargin;
            $prev_cell_padding  = $this->cell_padding;
            $this->lMargin      = $this->theadMargins['lmargin'] + ($this->pagedim[$this->page]['olm'] - $this->pagedim[$this->theadMargins['page']]['olm']);
            $this->rMargin      = $this->theadMargins['rmargin'] + ($this->pagedim[$this->page]['orm'] - $this->pagedim[$this->theadMargins['page']]['orm']);
            $this->cell_padding = $this->theadMargins['cell_padding'];
            if ($this->rtl) {
                $this->x = $this->w - $this->rMargin;
            } else {
                $this->x = $this->lMargin;
            }
            if ($this->theadMargins['cell']) {
                if ($this->rtl) {
                    $this->x -= $this->cell_padding['R'];
                } else {
                    $this->x += $this->cell_padding['L'];
                }
            }
            $this->writeHTML($this->thead, false, false, false, false, '');
            if (!isset($this->theadMargins['top'])) {
                $this->theadMargins['top'] = $this->tMargin;
            }
            if (!isset($this->columns[0]['th'])) {
                $this->columns[0]['th'] = array();
            }
            $this->columns[0]['th']['\'' . $this->page . '\''] = $this->y;
            $this->tMargin                                     = $this->y;
            $this->pagedim[$this->page]['tm']                  = $this->tMargin;
            $this->lasth                                       = 0;
            $this->lMargin                                     = $prev_lMargin;
            $this->rMargin                                     = $prev_rMargin;
            $this->cell_padding                                = $prev_cell_padding;
        }
    }
    public function PageNo()
    {
        return $this->page;
    }
    public function AddSpotColor($name, $c, $m, $y, $k)
    {
        if (!isset($this->spot_colors[$name])) {
            $i                        = 1 + count($this->spot_colors);
            $this->spot_colors[$name] = array(
                'i' => $i,
                'c' => $c,
                'm' => $m,
                'y' => $y,
                'k' => $k
            );
        }
        $color = preg_replace('/[\s]*/', '', $name);
        $color = strtolower($color);
        if (!isset($this->spotcolor[$color])) {
            $this->spotcolor[$color] = array(
                $c,
                $m,
                $y,
                $k,
                $name
            );
        }
    }
    public function SetDrawColorArray($color, $ret = false)
    {
        if (is_array($color)) {
            $color = array_values($color);
            $r     = isset($color[0]) ? $color[0] : -1;
            $g     = isset($color[1]) ? $color[1] : -1;
            $b     = isset($color[2]) ? $color[2] : -1;
            $k     = isset($color[3]) ? $color[3] : -1;
            $name  = isset($color[4]) ? $color[4] : '';
            if ($r >= 0) {
                return $this->SetDrawColor($r, $g, $b, $k, $ret, $name);
            }
        }
        return '';
    }
    public function SetDrawColor($col1 = 0, $col2 = -1, $col3 = -1, $col4 = -1, $ret = false, $name = '')
    {
        if (!is_numeric($col1)) {
            $col1 = 0;
        }
        if (!is_numeric($col2)) {
            $col2 = -1;
        }
        if (!is_numeric($col3)) {
            $col3 = -1;
        }
        if (!is_numeric($col4)) {
            $col4 = -1;
        }
        if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
            $this->DrawColor   = sprintf('%.3F G', ($col1 / 255));
            $this->strokecolor = array(
                'G' => $col1
            );
        } elseif ($col4 == -1) {
            $this->DrawColor   = sprintf('%.3F %.3F %.3F RG', ($col1 / 255), ($col2 / 255), ($col3 / 255));
            $this->strokecolor = array(
                'R' => $col1,
                'G' => $col2,
                'B' => $col3
            );
        } elseif (empty($name)) {
            $this->DrawColor   = sprintf('%.3F %.3F %.3F %.3F K', ($col1 / 100), ($col2 / 100), ($col3 / 100), ($col4 / 100));
            $this->strokecolor = array(
                'C' => $col1,
                'M' => $col2,
                'Y' => $col3,
                'K' => $col4
            );
        } else {
            $this->AddSpotColor($name, $col1, $col2, $col3, $col4);
            $this->DrawColor   = sprintf('/CS%d CS %.3F SCN', $this->spot_colors[$name]['i'], 1);
            $this->strokecolor = array(
                'C' => $col1,
                'M' => $col2,
                'Y' => $col3,
                'K' => $col4,
                'name' => $name
            );
        }
        if ($this->page > 0) {
            if (!$ret) {
                $this->_out($this->DrawColor);
            }
            return $this->DrawColor;
        }
        return '';
    }
    public function SetDrawSpotColor($name, $tint = 100)
    {
        if (!isset($this->spot_colors[$name])) {
            $this->Error('Undefined spot color: ' . $name);
        }
        $this->DrawColor   = sprintf('/CS%d CS %.3F SCN', $this->spot_colors[$name]['i'], ($tint / 100));
        $this->strokecolor = array(
            'C' => $this->spot_colors[$name]['c'],
            'M' => $this->spot_colors[$name]['m'],
            'Y' => $this->spot_colors[$name]['y'],
            'K' => $this->spot_colors[$name]['k'],
            'name' => $name
        );
        if ($this->page > 0) {
            $this->_out($this->DrawColor);
        }
    }
    public function SetFillColorArray($color, $ret = false)
    {
        if (is_array($color)) {
            $color = array_values($color);
            $r     = isset($color[0]) ? $color[0] : -1;
            $g     = isset($color[1]) ? $color[1] : -1;
            $b     = isset($color[2]) ? $color[2] : -1;
            $k     = isset($color[3]) ? $color[3] : -1;
            $name  = isset($color[4]) ? $color[4] : '';
            if ($r >= 0) {
                $this->SetFillColor($r, $g, $b, $k, $ret, $name);
            }
        }
    }
    public function SetFillColor($col1 = 0, $col2 = -1, $col3 = -1, $col4 = -1, $ret = false, $name = '')
    {
        if (!is_numeric($col1)) {
            $col1 = 0;
        }
        if (!is_numeric($col2)) {
            $col2 = -1;
        }
        if (!is_numeric($col3)) {
            $col3 = -1;
        }
        if (!is_numeric($col4)) {
            $col4 = -1;
        }
        if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
            $this->FillColor = sprintf('%.3F g', ($col1 / 255));
            $this->bgcolor   = array(
                'G' => $col1
            );
        } elseif ($col4 == -1) {
            $this->FillColor = sprintf('%.3F %.3F %.3F rg', ($col1 / 255), ($col2 / 255), ($col3 / 255));
            $this->bgcolor   = array(
                'R' => $col1,
                'G' => $col2,
                'B' => $col3
            );
        } elseif (empty($name)) {
            $this->FillColor = sprintf('%.3F %.3F %.3F %.3F k', ($col1 / 100), ($col2 / 100), ($col3 / 100), ($col4 / 100));
            $this->bgcolor   = array(
                'C' => $col1,
                'M' => $col2,
                'Y' => $col3,
                'K' => $col4
            );
        } else {
            $this->AddSpotColor($name, $col1, $col2, $col3, $col4);
            $this->FillColor = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$name]['i'], 1);
            $this->bgcolor   = array(
                'C' => $col1,
                'M' => $col2,
                'Y' => $col3,
                'K' => $col4,
                'name' => $name
            );
        }
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
        if ($this->page > 0) {
            if (!$ret) {
                $this->_out($this->FillColor);
            }
            return $this->FillColor;
        }
        return '';
    }
    public function SetFillSpotColor($name, $tint = 100)
    {
        if (!isset($this->spot_colors[$name])) {
            $this->Error('Undefined spot color: ' . $name);
        }
        $this->FillColor = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$name]['i'], ($tint / 100));
        $this->bgcolor   = array(
            'C' => $this->spot_colors[$name]['c'],
            'M' => $this->spot_colors[$name]['m'],
            'Y' => $this->spot_colors[$name]['y'],
            'K' => $this->spot_colors[$name]['k'],
            'name' => $name
        );
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
        if ($this->page > 0) {
            $this->_out($this->FillColor);
        }
    }
    public function SetTextColorArray($color, $ret = false)
    {
        if (is_array($color)) {
            $color = array_values($color);
            $r     = isset($color[0]) ? $color[0] : -1;
            $g     = isset($color[1]) ? $color[1] : -1;
            $b     = isset($color[2]) ? $color[2] : -1;
            $k     = isset($color[3]) ? $color[3] : -1;
            $name  = isset($color[4]) ? $color[4] : '';
            if ($r >= 0) {
                $this->SetTextColor($r, $g, $b, $k, $ret, $name);
            }
        }
    }
    public function SetTextColor($col1 = 0, $col2 = -1, $col3 = -1, $col4 = -1, $ret = false, $name = '')
    {
        if (!is_numeric($col1)) {
            $col1 = 0;
        }
        if (!is_numeric($col2)) {
            $col2 = -1;
        }
        if (!is_numeric($col3)) {
            $col3 = -1;
        }
        if (!is_numeric($col4)) {
            $col4 = -1;
        }
        if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
            $this->TextColor = sprintf('%.3F g', ($col1 / 255));
            $this->fgcolor   = array(
                'G' => $col1
            );
        } elseif ($col4 == -1) {
            $this->TextColor = sprintf('%.3F %.3F %.3F rg', ($col1 / 255), ($col2 / 255), ($col3 / 255));
            $this->fgcolor   = array(
                'R' => $col1,
                'G' => $col2,
                'B' => $col3
            );
        } elseif (empty($name)) {
            $this->TextColor = sprintf('%.3F %.3F %.3F %.3F k', ($col1 / 100), ($col2 / 100), ($col3 / 100), ($col4 / 100));
            $this->fgcolor   = array(
                'C' => $col1,
                'M' => $col2,
                'Y' => $col3,
                'K' => $col4
            );
        } else {
            $this->AddSpotColor($name, $col1, $col2, $col3, $col4);
            $this->TextColor = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$name]['i'], 1);
            $this->fgcolor   = array(
                'C' => $col1,
                'M' => $col2,
                'Y' => $col3,
                'K' => $col4,
                'name' => $name
            );
        }
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
    }
    public function SetTextSpotColor($name, $tint = 100)
    {
        if (!isset($this->spot_colors[$name])) {
            $this->Error('Undefined spot color: ' . $name);
        }
        $this->TextColor = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$name]['i'], ($tint / 100));
        $this->fgcolor   = array(
            'C' => $this->spot_colors[$name]['c'],
            'M' => $this->spot_colors[$name]['m'],
            'Y' => $this->spot_colors[$name]['y'],
            'K' => $this->spot_colors[$name]['k'],
            'name' => $name
        );
        $this->ColorFlag = ($this->FillColor != $this->TextColor);
        if ($this->page > 0) {
            $this->_out($this->TextColor);
        }
    }
    public function GetStringWidth($s, $fontname = '', $fontstyle = '', $fontsize = 0, $getarray = false)
    {
        return $this->GetArrStringWidth($this->utf8Bidi($this->UTF8StringToArray($s), $s, $this->tmprtl), $fontname, $fontstyle, $fontsize, $getarray);
    }
    public function GetArrStringWidth($sa, $fontname = '', $fontstyle = '', $fontsize = 0, $getarray = false)
    {
        if (!$this->empty_string($fontname)) {
            $prev_FontFamily = $this->FontFamily;
            $prev_FontStyle  = $this->FontStyle;
            $prev_FontSizePt = $this->FontSizePt;
            $this->SetFont($fontname, $fontstyle, $fontsize);
        }
        $sa = $this->UTF8ArrToLatin1($sa);
        $w  = 0;
        $wa = array();
        foreach ($sa as $ck => $char) {
            $cw   = $this->GetCharWidth($char, isset($sa[($ck + 1)]));
            $wa[] = $cw;
            $w += $cw;
        }
        if (!$this->empty_string($fontname)) {
            $this->SetFont($prev_FontFamily, $prev_FontStyle, $prev_FontSizePt);
        }
        if ($getarray) {
            return $wa;
        }
        return $w;
    }
    public function GetCharWidth($char, $notlast = true)
    {
        $chw = $this->getRawCharWidth($char);
        if (($this->font_spacing != 0) AND $notlast) {
            $chw += $this->font_spacing;
        }
        if ($this->font_stretching != 100) {
            $chw *= ($this->font_stretching / 100);
        }
        return $chw;
    }
    public function getRawCharWidth($char)
    {
        if ($char == 173) {
            return (0);
        }
        if (isset($this->CurrentFont['cw'][$char])) {
            $w = $this->CurrentFont['cw'][$char];
        } elseif (isset($this->CurrentFont['dw'])) {
            $w = $this->CurrentFont['dw'];
        } elseif (isset($this->CurrentFont['cw'][32])) {
            $w = $this->CurrentFont['cw'][32];
        } else {
            $w = 600;
        }
        return ($w * $this->FontSize / 1000);
    }
    public function GetNumChars($s)
    {
        if ($this->isUnicodeFont()) {
            return count($this->UTF8StringToArray($s));
        }
        return strlen($s);
    }
    protected function getFontsList()
    {
        $fontsdir = opendir($this->_getfontpath());
        while (($file = readdir($fontsdir)) !== false) {
            if (substr($file, -4) == '.php') {
                array_push($this->fontlist, strtolower(basename($file, '.php')));
            }
        }
        closedir($fontsdir);
    }
    public function AddFont($family, $style = '', $fontfile = '', $subset = 'default')
    {
        if ($subset === 'default') {
            $subset = $this->font_subsetting;
        }
        if ($this->empty_string($family)) {
            if (!$this->empty_string($this->FontFamily)) {
                $family = $this->FontFamily;
            } else {
                $this->Error('Empty font family');
            }
        }
        if (substr($family, -1) == 'I') {
            $style .= 'I';
            $family = substr($family, 0, -1);
        }
        if (substr($family, -1) == 'B') {
            $style .= 'B';
            $family = substr($family, 0, -1);
        }
        $family = strtolower($family);
        if ((!$this->isunicode) AND ($family == 'arial')) {
            $family = 'helvetica';
        }
        if (($family == 'symbol') OR ($family == 'zapfdingbats')) {
            $style = '';
        }
        $tempstyle = strtoupper($style);
        $style     = '';
        if (strpos($tempstyle, 'U') !== false) {
            $this->underline = true;
        } else {
            $this->underline = false;
        }
        if (strpos($tempstyle, 'D') !== false) {
            $this->linethrough = true;
        } else {
            $this->linethrough = false;
        }
        if (strpos($tempstyle, 'O') !== false) {
            $this->overline = true;
        } else {
            $this->overline = false;
        }
        if (strpos($tempstyle, 'B') !== false) {
            $style .= 'B';
        }
        if (strpos($tempstyle, 'I') !== false) {
            $style .= 'I';
        }
        $bistyle    = $style;
        $fontkey    = $family . $style;
        $font_style = $style . ($this->underline ? 'U' : '') . ($this->linethrough ? 'D' : '') . ($this->overline ? 'O' : '');
        $fontdata   = array(
            'fontkey' => $fontkey,
            'family' => $family,
            'style' => $font_style
        );
        $fb         = $this->getFontBuffer($fontkey);
        if ($fb !== false) {
            if ($this->inxobj) {
                $this->xobjects[$this->xobjid]['fonts'][$fontkey] = $fb['i'];
            }
            return $fontdata;
        }
        if (isset($type)) {
            unset($type);
        }
        if (isset($cw)) {
            unset($cw);
        }
        $fontdir = false;
        if (!$this->empty_string($fontfile)) {
            $fontdir = dirname($fontfile);
            if ($this->empty_string($fontdir) OR ($fontdir == '.')) {
                $fontdir = '';
            } else {
                $fontdir .= '/';
            }
        }
        $missing_style = false;
        if ($this->empty_string($fontfile) OR (!file_exists($fontfile))) {
            $tmp_fontfile = str_replace(' ', '', $family) . strtolower($style) . '.php';
            if (($fontdir !== false) AND file_exists($fontdir . $tmp_fontfile)) {
                $fontfile = $fontdir . $tmp_fontfile;
            } elseif (file_exists($this->_getfontpath() . $tmp_fontfile)) {
                $fontfile = $this->_getfontpath() . $tmp_fontfile;
            } elseif (file_exists($tmp_fontfile)) {
                $fontfile = $tmp_fontfile;
            } elseif (!$this->empty_string($style)) {
                $missing_style = true;
                $tmp_fontfile  = str_replace(' ', '', $family) . '.php';
                if (($fontdir !== false) AND file_exists($fontdir . $tmp_fontfile)) {
                    $fontfile = $fontdir . $tmp_fontfile;
                } elseif (file_exists($this->_getfontpath() . $tmp_fontfile)) {
                    $fontfile = $this->_getfontpath() . $tmp_fontfile;
                } else {
                    $fontfile = $tmp_fontfile;
                }
            }
        }
        if (file_exists($fontfile)) {
            include($fontfile);
        } else {
            $this->Error('Could not include font definition file: ' . $family . '');
        }
        if ((!isset($type)) OR (!isset($cw))) {
            $this->Error('The font definition file has a bad format: ' . $fontfile . '');
        }
        if (!isset($file) OR $this->empty_string($file)) {
            $file = '';
        }
        if (!isset($enc) OR $this->empty_string($enc)) {
            $enc = '';
        }
        if (!isset($cidinfo) OR $this->empty_string($cidinfo)) {
            $cidinfo            = array(
                'Registry' => 'Adobe',
                'Ordering' => 'Identity',
                'Supplement' => 0
            );
            $cidinfo['uni2cid'] = array();
        }
        if (!isset($ctg) OR $this->empty_string($ctg)) {
            $ctg = '';
        }
        if (!isset($desc) OR $this->empty_string($desc)) {
            $desc = array();
        }
        if (!isset($up) OR $this->empty_string($up)) {
            $up = -100;
        }
        if (!isset($ut) OR $this->empty_string($ut)) {
            $ut = 50;
        }
        if (!isset($cw) OR $this->empty_string($cw)) {
            $cw = array();
        }
        if (!isset($dw) OR $this->empty_string($dw)) {
            if (isset($desc['MissingWidth']) AND ($desc['MissingWidth'] > 0)) {
                $dw = $desc['MissingWidth'];
            } elseif (isset($cw[32])) {
                $dw = $cw[32];
            } else {
                $dw = 600;
            }
        }
        ++$this->numfonts;
        if ($type == 'core') {
            $name   = $this->CoreFonts[$fontkey];
            $subset = false;
        } elseif (($type == 'TrueType') OR ($type == 'Type1')) {
            $subset = false;
        } elseif ($type == 'TrueTypeUnicode') {
            $enc = 'Identity-H';
        } elseif ($type != 'cidfont0') {
            $this->Error('Unknow font type: ' . $type . '');
        }
        if (!isset($name) OR empty($name)) {
            $name = $fontkey;
        }
        if (($type != 'core') AND $missing_style) {
            $styles = array(
                '' => '',
                'B' => ',Bold',
                'I' => ',Italic',
                'BI' => ',BoldItalic'
            );
            $name .= $styles[$bistyle];
            if (strpos($bistyle, 'B') !== false) {
                if (isset($desc['StemV'])) {
                    $desc['StemV'] *= 2;
                } else {
                    $desc['StemV'] = 120;
                }
            }
            if (strpos($bistyle, 'I') !== false) {
                if (isset($desc['ItalicAngle'])) {
                    $desc['ItalicAngle'] -= 11;
                } else {
                    $desc['ItalicAngle'] = -11;
                }
                if (isset($desc['Flags'])) {
                    $desc['Flags'] |= 128;
                } else {
                    $desc['Flags'] = 128;
                }
            }
        }
        $subsetchars = array_fill(0, 256, true);
        $this->setFontBuffer($fontkey, array(
            'fontkey' => $fontkey,
            'i' => $this->numfonts,
            'type' => $type,
            'name' => $name,
            'desc' => $desc,
            'up' => $up,
            'ut' => $ut,
            'cw' => $cw,
            'dw' => $dw,
            'enc' => $enc,
            'cidinfo' => $cidinfo,
            'file' => $file,
            'ctg' => $ctg,
            'subset' => $subset,
            'subsetchars' => $subsetchars
        ));
        if ($this->inxobj) {
            $this->xobjects[$this->xobjid]['fonts'][$fontkey] = $this->numfonts;
        }
        if (isset($diff) AND (!empty($diff))) {
            $d  = 0;
            $nb = count($this->diffs);
            for ($i = 1; $i <= $nb; ++$i) {
                if ($this->diffs[$i] == $diff) {
                    $d = $i;
                    break;
                }
            }
            if ($d == 0) {
                $d               = $nb + 1;
                $this->diffs[$d] = $diff;
            }
            $this->setFontSubBuffer($fontkey, 'diff', $d);
        }
        if (!$this->empty_string($file)) {
            if (!isset($this->FontFiles[$file])) {
                if ((strcasecmp($type, 'TrueType') == 0) OR (strcasecmp($type, 'TrueTypeUnicode') == 0)) {
                    $this->FontFiles[$file] = array(
                        'length1' => $originalsize,
                        'fontdir' => $fontdir,
                        'subset' => $subset,
                        'fontkeys' => array(
                            $fontkey
                        )
                    );
                } elseif ($type != 'core') {
                    $this->FontFiles[$file] = array(
                        'length1' => $size1,
                        'length2' => $size2,
                        'fontdir' => $fontdir,
                        'subset' => $subset,
                        'fontkeys' => array(
                            $fontkey
                        )
                    );
                }
            } else {
                $this->FontFiles[$file]['subset'] = ($this->FontFiles[$file]['subset'] AND $subset);
                if (!in_array($fontkey, $this->FontFiles[$file]['fontkeys'])) {
                    $this->FontFiles[$file]['fontkeys'][] = $fontkey;
                }
            }
        }
        return $fontdata;
    }
    public function SetFont($family, $style = '', $size = 0, $fontfile = '', $subset = 'default')
    {
        if ($size == 0) {
            $size = $this->FontSizePt;
        }
        $fontdata          = $this->AddFont($family, $style, $fontfile, $subset);
        $this->FontFamily  = $fontdata['family'];
        $this->FontStyle   = $fontdata['style'];
        $this->CurrentFont = $this->getFontBuffer($fontdata['fontkey']);
        $this->SetFontSize($size);
    }
    public function SetFontSize($size, $out = true)
    {
        $this->FontSizePt = $size;
        $this->FontSize   = $size / $this->k;
        if (isset($this->CurrentFont['desc']['FontBBox'])) {
            $bbox        = explode(' ', substr($this->CurrentFont['desc']['FontBBox'], 1, -1));
            $font_height = ((intval($bbox[3]) - intval($bbox[1])) * $size / 1000);
        } else {
            $font_height = $size * 1.219;
        }
        if (isset($this->CurrentFont['desc']['Ascent']) AND ($this->CurrentFont['desc']['Ascent'] > 0)) {
            $font_ascent = ($this->CurrentFont['desc']['Ascent'] * $size / 1000);
        }
        if (isset($this->CurrentFont['desc']['Descent']) AND ($this->CurrentFont['desc']['Descent'] <= 0)) {
            $font_descent = (-$this->CurrentFont['desc']['Descent'] * $size / 1000);
        }
        if (!isset($font_ascent) AND !isset($font_descent)) {
            $font_ascent  = 0.76 * $font_height;
            $font_descent = $font_height - $font_ascent;
        } elseif (!isset($font_descent)) {
            $font_descent = $font_height - $font_ascent;
        } elseif (!isset($font_ascent)) {
            $font_ascent = $font_height - $font_descent;
        }
        $this->FontAscent  = $font_ascent / $this->k;
        $this->FontDescent = $font_descent / $this->k;
        if ($out AND ($this->page > 0) AND (isset($this->CurrentFont['i']))) {
            $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
        }
    }
    public function getFontDescent($font, $style = '', $size = 0)
    {
        $fontdata = $this->AddFont($font, $style);
        $fontinfo = $this->getFontBuffer($fontdata['fontkey']);
        if (isset($fontinfo['desc']['Descent']) AND ($fontinfo['desc']['Descent'] <= 0)) {
            $descent = (-$fontinfo['desc']['Descent'] * $size / 1000);
        } else {
            $descent = 1.219 * 0.24 * $size;
        }
        return ($descent / $this->k);
    }
    public function getFontAscent($font, $style = '', $size = 0)
    {
        $fontdata = $this->AddFont($font, $style);
        $fontinfo = $this->getFontBuffer($fontdata['fontkey']);
        if (isset($fontinfo['desc']['Ascent']) AND ($fontinfo['desc']['Ascent'] > 0)) {
            $ascent = ($fontinfo['desc']['Ascent'] * $size / 1000);
        } else {
            $ascent = 1.219 * 0.76 * $size;
        }
        return ($ascent / $this->k);
    }
    public function SetDefaultMonospacedFont($font)
    {
        $this->default_monospaced_font = $font;
    }
    public function AddLink()
    {
        $n               = count($this->links) + 1;
        $this->links[$n] = array(
            0,
            0
        );
        return $n;
    }
    public function SetLink($link, $y = 0, $page = -1)
    {
        if ($y == -1) {
            $y = $this->y;
        }
        if ($page == -1) {
            $page = $this->page;
        }
        $this->links[$link] = array(
            $page,
            $y
        );
    }
    public function Link($x, $y, $w, $h, $link, $spaces = 0)
    {
        $this->Annotation($x, $y, $w, $h, $link, array(
            'Subtype' => 'Link'
        ), $spaces);
    }
    public function Annotation($x, $y, $w, $h, $text, $opt = array('Subtype' => 'Text'), $spaces = 0)
    {
        if ($this->inxobj) {
            $this->xobjects[$this->xobjid]['annotations'][] = array(
                'x' => $x,
                'y' => $y,
                'w' => $w,
                'h' => $h,
                'text' => $text,
                'opt' => $opt,
                'spaces' => $spaces
            );
            return;
        }
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        if (isset($this->transfmatrix) AND !empty($this->transfmatrix)) {
            for ($i = $this->transfmatrix_key; $i > 0; --$i) {
                $maxid = count($this->transfmatrix[$i]) - 1;
                for ($j = $maxid; $j >= 0; --$j) {
                    $ctm = $this->transfmatrix[$i][$j];
                    if (isset($ctm['a'])) {
                        $x  = $x * $this->k;
                        $y  = ($this->h - $y) * $this->k;
                        $w  = $w * $this->k;
                        $h  = $h * $this->k;
                        $xt = $x;
                        $yt = $y;
                        $x1 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
                        $y1 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
                        $xt = $x + $w;
                        $yt = $y;
                        $x2 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
                        $y2 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
                        $xt = $x;
                        $yt = $y - $h;
                        $x3 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
                        $y3 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
                        $xt = $x + $w;
                        $yt = $y - $h;
                        $x4 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
                        $y4 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
                        $x  = min($x1, $x2, $x3, $x4);
                        $y  = max($y1, $y2, $y3, $y4);
                        $w  = (max($x1, $x2, $x3, $x4) - $x) / $this->k;
                        $h  = ($y - min($y1, $y2, $y3, $y4)) / $this->k;
                        $x  = $x / $this->k;
                        $y  = $this->h - ($y / $this->k);
                    }
                }
            }
        }
        if ($this->page <= 0) {
            $page = 1;
        } else {
            $page = $this->page;
        }
        if (!isset($this->PageAnnots[$page])) {
            $this->PageAnnots[$page] = array();
        }
        ++$this->n;
        $this->PageAnnots[$page][] = array(
            'n' => $this->n,
            'x' => $x,
            'y' => $y,
            'w' => $w,
            'h' => $h,
            'txt' => $text,
            'opt' => $opt,
            'numspaces' => $spaces
        );
        if ((($opt['Subtype'] == 'FileAttachment') OR ($opt['Subtype'] == 'Sound')) AND (!$this->empty_string($opt['FS'])) AND file_exists($opt['FS']) AND (!isset($this->embeddedfiles[basename($opt['FS'])]))) {
            ++$this->n;
            $this->embeddedfiles[basename($opt['FS'])] = array(
                'n' => $this->n,
                'file' => $opt['FS']
            );
        }
        if (isset($opt['mk']['i']) AND file_exists($opt['mk']['i'])) {
            $this->Image($opt['mk']['i'], '', '', 10, 10, '', '', '', false, 300, '', false, false, 0, false, true);
        }
        if (isset($opt['mk']['ri']) AND file_exists($opt['mk']['ri'])) {
            $this->Image($opt['mk']['ri'], '', '', 0, 0, '', '', '', false, 300, '', false, false, 0, false, true);
        }
        if (isset($opt['mk']['ix']) AND file_exists($opt['mk']['ix'])) {
            $this->Image($opt['mk']['ix'], '', '', 0, 0, '', '', '', false, 300, '', false, false, 0, false, true);
        }
    }
    protected function _putEmbeddedFiles()
    {
        reset($this->embeddedfiles);
        foreach ($this->embeddedfiles as $filename => $filedata) {
            $data   = file_get_contents($filedata['file']);
            $filter = '';
            if ($this->compress) {
                $data   = gzcompress($data);
                $filter = ' /Filter /FlateDecode';
            }
            $stream = $this->_getrawstream($data, $filedata['n']);
            $out    = $this->_getobj($filedata['n']) . "\n";
            $out .= '<< /Type /EmbeddedFile' . $filter . ' /Length ' . strlen($stream) . ' >>';
            $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
            $out .= "\n" . 'endobj';
            $this->_out($out);
        }
    }
    public function Text($x, $y, $txt, $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false)
    {
        $textrendermode  = $this->textrendermode;
        $textstrokewidth = $this->textstrokewidth;
        $this->setTextRenderingMode($fstroke, $ffill, $fclip);
        $this->SetXY($x, $y, $rtloff);
        $this->Cell(0, 0, $txt, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign);
        $this->textrendermode  = $textrendermode;
        $this->textstrokewidth = $textstrokewidth;
    }
    public function AcceptPageBreak()
    {
        if ($this->num_columns > 1) {
            if ($this->current_column < ($this->num_columns - 1)) {
                $this->selectColumn($this->current_column + 1);
            } else {
                $this->AddPage();
                $this->selectColumn(0);
            }
            return false;
        }
        return $this->AutoPageBreak;
    }
    protected function checkPageBreak($h = 0, $y = '', $addpage = true)
    {
        if ($this->empty_string($y)) {
            $y = $this->y;
        }
        $current_page = $this->page;
        if ((($y + $h) > $this->PageBreakTrigger) AND ($this->inPageBody()) AND ($this->AcceptPageBreak())) {
            if ($addpage) {
                $x = $this->x;
                $this->AddPage($this->CurOrientation);
                $this->y = $this->tMargin;
                $oldpage = $this->page - 1;
                if ($this->rtl) {
                    if ($this->pagedim[$this->page]['orm'] != $this->pagedim[$oldpage]['orm']) {
                        $this->x = $x - ($this->pagedim[$this->page]['orm'] - $this->pagedim[$oldpage]['orm']);
                    } else {
                        $this->x = $x;
                    }
                } else {
                    if ($this->pagedim[$this->page]['olm'] != $this->pagedim[$oldpage]['olm']) {
                        $this->x = $x + ($this->pagedim[$this->page]['olm'] - $this->pagedim[$oldpage]['olm']);
                    } else {
                        $this->x = $x;
                    }
                }
            }
            return true;
        }
        if ($current_page != $this->page) {
            return true;
        }
        return false;
    }
    public function removeSHY($txt = '')
    {
        $txt = preg_replace('/([\\xc2]{1}[\\xad]{1})/', '', $txt);
        if (!$this->isunicode) {
            $txt = preg_replace('/([\\xad]{1})/', '', $txt);
        }
        return $txt;
    }
    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M')
    {
        $prev_cell_margin  = $this->cell_margin;
        $prev_cell_padding = $this->cell_padding;
        $this->adjustCellPadding($border);
        if (!$ignore_min_height) {
            $min_cell_height = ($this->FontSize * $this->cell_height_ratio) + $this->cell_padding['T'] + $this->cell_padding['B'];
            if ($h < $min_cell_height) {
                $h = $min_cell_height;
            }
        }
        $this->checkPageBreak($h + $this->cell_margin['T'] + $this->cell_margin['B']);
        $this->_out($this->getCellCode($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch, true, $calign, $valign));
        $this->cell_padding = $prev_cell_padding;
        $this->cell_margin  = $prev_cell_margin;
    }
    protected function getCellCode($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M')
    {
        $txt               = str_replace($this->unichr(160), ' ', $txt);
        $prev_cell_margin  = $this->cell_margin;
        $prev_cell_padding = $this->cell_padding;
        $txt               = $this->removeSHY($txt);
        $rs                = '';
        $this->adjustCellPadding($border);
        if (!$ignore_min_height) {
            $min_cell_height = ($this->FontSize * $this->cell_height_ratio) + $this->cell_padding['T'] + $this->cell_padding['B'];
            if ($h < $min_cell_height) {
                $h = $min_cell_height;
            }
        }
        $k = $this->k;
        list($this->x, $this->y) = $this->checkPageRegions($h, $this->x, $this->y);
        if ($this->rtl) {
            $x = $this->x - $this->cell_margin['R'];
        } else {
            $x = $this->x + $this->cell_margin['L'];
        }
        $y                    = $this->y + $this->cell_margin['T'];
        $prev_font_stretching = $this->font_stretching;
        $prev_font_spacing    = $this->font_spacing;
        switch ($calign) {
            case 'A': {
                switch ($valign) {
                    case 'T': {
                        $y -= $this->cell_padding['T'];
                        break;
                    }
                    case 'B': {
                        $y -= ($h - $this->cell_padding['B'] - $this->FontAscent - $this->FontDescent);
                        break;
                    }
                    default:
                    case 'C':
                    case 'M': {
                        $y -= (($h - $this->FontAscent - $this->FontDescent) / 2);
                        break;
                    }
                }
                break;
            }
            case 'L': {
                switch ($valign) {
                    case 'T': {
                        $y -= ($this->cell_padding['T'] + $this->FontAscent);
                        break;
                    }
                    case 'B': {
                        $y -= ($h - $this->cell_padding['B'] - $this->FontDescent);
                        break;
                    }
                    default:
                    case 'C':
                    case 'M': {
                        $y -= (($h + $this->FontAscent - $this->FontDescent) / 2);
                        break;
                    }
                }
                break;
            }
            case 'D': {
                switch ($valign) {
                    case 'T': {
                        $y -= ($this->cell_padding['T'] + $this->FontAscent + $this->FontDescent);
                        break;
                    }
                    case 'B': {
                        $y -= ($h - $this->cell_padding['B']);
                        break;
                    }
                    default:
                    case 'C':
                    case 'M': {
                        $y -= (($h + $this->FontAscent + $this->FontDescent) / 2);
                        break;
                    }
                }
                break;
            }
            case 'B': {
                $y -= $h;
                break;
            }
            case 'C':
            case 'M': {
                $y -= ($h / 2);
                break;
            }
            default:
            case 'T': {
                break;
            }
        }
        switch ($valign) {
            case 'T': {
                $yt = $y + $this->cell_padding['T'];
                break;
            }
            case 'B': {
                $yt = $y + $h - $this->cell_padding['B'] - $this->FontAscent - $this->FontDescent;
                break;
            }
            default:
            case 'C':
            case 'M': {
                $yt = $y + (($h - $this->FontAscent - $this->FontDescent) / 2);
                break;
            }
        }
        $basefonty = $yt + $this->FontAscent;
        if ($this->empty_string($w) OR ($w <= 0)) {
            if ($this->rtl) {
                $w = $x - $this->lMargin;
            } else {
                $w = $this->w - $this->rMargin - $x;
            }
        }
        $s = '';
        if (is_string($border) AND (strlen($border) == 4)) {
            $border = 1;
        }
        if ($fill OR ($border == 1)) {
            if ($fill) {
                $op = ($border == 1) ? 'B' : 'f';
            } else {
                $op = 'S';
            }
            if ($this->rtl) {
                $xk = (($x - $w) * $k);
            } else {
                $xk = ($x * $k);
            }
            $s .= sprintf('%.2F %.2F %.2F %.2F re %s ', $xk, (($this->h - $y) * $k), ($w * $k), (-$h * $k), $op);
        }
        $s .= $this->getCellBorder($x, $y, $w, $h, $border);
        if ($txt != '') {
            $txt2 = $txt;
            if ($this->isunicode) {
                if (($this->CurrentFont['type'] == 'core') OR ($this->CurrentFont['type'] == 'TrueType') OR ($this->CurrentFont['type'] == 'Type1')) {
                    $txt2 = $this->UTF8ToLatin1($txt2);
                } else {
                    $unicode = $this->UTF8StringToArray($txt);
                    $unicode = $this->utf8Bidi($unicode, '', $this->tmprtl);
                    if (defined('K_THAI_TOPCHARS') AND (K_THAI_TOPCHARS == true)) {
                        $topchar           = array(
                            3611,
                            3613,
                            3615,
                            3650,
                            3651,
                            3652
                        );
                        $topsym            = array(
                            3633,
                            3636,
                            3637,
                            3638,
                            3639,
                            3655,
                            3656,
                            3657,
                            3658,
                            3659,
                            3660,
                            3661,
                            3662
                        );
                        $numchars          = count($unicode);
                        $unik              = 0;
                        $uniblock          = array();
                        $uniblock[$unik]   = array();
                        $uniblock[$unik][] = $unicode[0];
                        for ($i = 1; $i < $numchars; ++$i) {
                            if (in_array($unicode[$i], $topsym) AND (in_array($unicode[($i - 1)], $topsym) OR in_array($unicode[($i - 1)], $topchar))) {
                                ++$unik;
                                $uniblock[$unik]   = array();
                                $uniblock[$unik][] = $unicode[$i];
                                ++$unik;
                                $uniblock[$unik] = array();
                                $unicode[$i]     = 0x200b;
                            } else {
                                $uniblock[$unik][] = $unicode[$i];
                            }
                        }
                    }
                    $txt2 = $this->arrUTF8ToUTF16BE($unicode, false);
                }
            }
            $txt2    = $this->_escape($txt2);
            $txwidth = $this->GetStringWidth($txt);
            $width   = $txwidth;
            if ($stretch > 0) {
                if ($width <= 0) {
                    $ratio = 1;
                } else {
                    $ratio = (($w - $this->cell_padding['L'] - $this->cell_padding['R']) / $width);
                }
                if (($ratio < 1) OR (($ratio > 1) AND (($stretch % 2) == 0))) {
                    if ($stretch > 2) {
                        $this->font_spacing += ($w - $this->cell_padding['L'] - $this->cell_padding['R'] - $width) / (max(($this->GetNumChars($txt) - 1), 1) * ($this->font_stretching / 100));
                    } else {
                        $this->font_stretching *= $ratio;
                    }
                    $width = $w - $this->cell_padding['L'] - $this->cell_padding['R'];
                    $align = '';
                }
            }
            if ($this->font_stretching != 100) {
                $rs .= sprintf('BT %.2F Tz ET ', $this->font_stretching);
            }
            if ($this->font_spacing != 0) {
                $rs .= sprintf('BT %.2F Tc ET ', ($this->font_spacing * $this->k));
            }
            if ($this->ColorFlag AND ($this->textrendermode < 4)) {
                $s .= 'q ' . $this->TextColor . ' ';
            }
            $s .= sprintf('BT %d Tr %.2F w ET ', $this->textrendermode, $this->textstrokewidth);
            $ns         = substr_count($txt, chr(32));
            $spacewidth = 0;
            if (($align == 'J') AND ($ns > 0)) {
                if ($this->isUnicodeFont()) {
                    $width      = $this->GetStringWidth(str_replace(' ', '', $txt));
                    $spacewidth = -1000 * ($w - $width - $this->cell_padding['L'] - $this->cell_padding['R']) / ($ns ? $ns : 1) / $this->FontSize;
                    if ($this->font_stretching != 100) {
                        $spacewidth /= ($this->font_stretching / 100);
                    }
                    $txt2                  = str_replace(chr(0) . chr(32), ') ' . sprintf('%.3F', $spacewidth) . ' (', $txt2);
                    $unicode_justification = true;
                } else {
                    $width      = $txwidth;
                    $spacewidth = (($w - $width - $this->cell_padding['L'] - $this->cell_padding['R']) / ($ns ? $ns : 1)) * $this->k;
                    if ($this->font_stretching != 100) {
                        $spacewidth /= ($this->font_stretching / 100);
                    }
                    $rs .= sprintf('BT %.3F Tw ET ', $spacewidth);
                }
                $width = $w - $this->cell_padding['L'] - $this->cell_padding['R'];
            }
            $txt2 = str_replace("\r", ' ', $txt2);
            switch ($align) {
                case 'C': {
                    $dx = ($w - $width) / 2;
                    break;
                }
                case 'R': {
                    if ($this->rtl) {
                        $dx = $this->cell_padding['R'];
                    } else {
                        $dx = $w - $width - $this->cell_padding['R'];
                    }
                    break;
                }
                case 'L': {
                    if ($this->rtl) {
                        $dx = $w - $width - $this->cell_padding['L'];
                    } else {
                        $dx = $this->cell_padding['L'];
                    }
                    break;
                }
                case 'J':
                default: {
                    if ($this->rtl) {
                        $dx = $this->cell_padding['R'];
                    } else {
                        $dx = $this->cell_padding['L'];
                    }
                    break;
                }
            }
            if ($this->rtl) {
                $xdx = $x - $dx - $width;
            } else {
                $xdx = $x + $dx;
            }
            $xdk = $xdx * $k;
            $s .= sprintf('BT %.2F %.2F Td [(%s)] TJ ET', $xdk, (($this->h - $basefonty) * $k), $txt2);
            if (isset($uniblock)) {
                $xshift = 0;
                $ty     = (($this->h - $basefonty + (0.2 * $this->FontSize)) * $k);
                $spw    = (($w - $txwidth - $this->cell_padding['L'] - $this->cell_padding['R']) / ($ns ? $ns : 1));
                foreach ($uniblock as $uk => $uniarr) {
                    if (($uk % 2) == 0) {
                        if ($spacewidth != 0) {
                            $xshift += (count(array_keys($uniarr, 32)) * $spw);
                        }
                        $xshift += $this->GetArrStringWidth($uniarr);
                    } else {
                        $topchr = $this->arrUTF8ToUTF16BE($uniarr, false);
                        $topchr = $this->_escape($topchr);
                        $s .= sprintf(' BT %.2F %.2F Td [(%s)] TJ ET', ($xdk + ($xshift * $k)), $ty, $topchr);
                    }
                }
            }
            if ($this->underline) {
                $s .= ' ' . $this->_dounderlinew($xdx, $basefonty, $width);
            }
            if ($this->linethrough) {
                $s .= ' ' . $this->_dolinethroughw($xdx, $basefonty, $width);
            }
            if ($this->overline) {
                $s .= ' ' . $this->_dooverlinew($xdx, $basefonty, $width);
            }
            if ($this->ColorFlag AND ($this->textrendermode < 4)) {
                $s .= ' Q';
            }
            if ($link) {
                $this->Link($xdx, $yt, $width, ($this->FontAscent + $this->FontDescent), $link, $ns);
            }
        }
        if ($s) {
            $rs .= $s;
            if ($this->font_spacing != 0) {
                $rs .= ' BT 0 Tc ET';
            }
            if ($this->font_stretching != 100) {
                $rs .= ' BT 100 Tz ET';
            }
        }
        if (!$this->isUnicodeFont() AND ($align == 'J')) {
            $rs .= ' BT 0 Tw ET';
        }
        $this->font_stretching = $prev_font_stretching;
        $this->font_spacing    = $prev_font_spacing;
        $this->lasth           = $h;
        if ($ln > 0) {
            $this->y = $y + $h + $this->cell_margin['B'];
            if ($ln == 1) {
                if ($this->rtl) {
                    $this->x = $this->w - $this->rMargin;
                } else {
                    $this->x = $this->lMargin;
                }
            }
        } else {
            if ($this->rtl) {
                $this->x = $x - $w - $this->cell_margin['L'];
            } else {
                $this->x = $x + $w + $this->cell_margin['R'];
            }
        }
        $gstyles            = '' . $this->linestyleWidth . ' ' . $this->linestyleCap . ' ' . $this->linestyleJoin . ' ' . $this->linestyleDash . ' ' . $this->DrawColor . ' ' . $this->FillColor . "\n";
        $rs                 = $gstyles . $rs;
        $this->cell_padding = $prev_cell_padding;
        $this->cell_margin  = $prev_cell_margin;
        return $rs;
    }
    protected function getCellBorder($x, $y, $w, $h, $brd)
    {
        $s = '';
        if (empty($brd)) {
            return $s;
        }
        if ($brd == 1) {
            $brd = array(
                'LRTB' => true
            );
        }
        $k = $this->k;
        if ($this->rtl) {
            $xeL = ($x - $w) * $k;
            $xeR = $x * $k;
        } else {
            $xeL = $x * $k;
            $xeR = ($x + $w) * $k;
        }
        $yeL = (($this->h - ($y + $h)) * $k);
        $yeT = (($this->h - $y) * $k);
        $xeT = $xeL;
        $xeB = $xeR;
        $yeR = $yeT;
        $yeB = $yeL;
        if (is_string($brd)) {
            $slen   = strlen($brd);
            $newbrd = array();
            for ($i = 0; $i < $slen; ++$i) {
                $newbrd[$brd{$i}] = array(
                    'cap' => 'square',
                    'join' => 'miter'
                );
            }
            $brd = $newbrd;
        }
        if (isset($brd['mode'])) {
            $mode = $brd['mode'];
            unset($brd['mode']);
        } else {
            $mode = 'normal';
        }
        foreach ($brd as $border => $style) {
            if (is_array($style) AND !empty($style)) {
                $prev_style = $this->linestyleWidth . ' ' . $this->linestyleCap . ' ' . $this->linestyleJoin . ' ' . $this->linestyleDash . ' ' . $this->DrawColor . ' ';
                $s .= $this->SetLineStyle($style, true) . "\n";
            }
            switch ($mode) {
                case 'ext': {
                    $off = (($this->LineWidth / 2) * $k);
                    $xL  = $xeL - $off;
                    $xR  = $xeR + $off;
                    $yT  = $yeT + $off;
                    $yL  = $yeL - $off;
                    $xT  = $xL;
                    $xB  = $xR;
                    $yR  = $yT;
                    $yB  = $yL;
                    $w += $this->LineWidth;
                    $h += $this->LineWidth;
                    break;
                }
                case 'int': {
                    $off = ($this->LineWidth / 2) * $k;
                    $xL  = $xeL + $off;
                    $xR  = $xeR - $off;
                    $yT  = $yeT - $off;
                    $yL  = $yeL + $off;
                    $xT  = $xL;
                    $xB  = $xR;
                    $yR  = $yT;
                    $yB  = $yL;
                    $w -= $this->LineWidth;
                    $h -= $this->LineWidth;
                    break;
                }
                case 'normal':
                default: {
                    $xL = $xeL;
                    $xT = $xeT;
                    $xB = $xeB;
                    $xR = $xeR;
                    $yL = $yeL;
                    $yT = $yeT;
                    $yB = $yeB;
                    $yR = $yeR;
                    break;
                }
            }
            if (strlen($border) == 4) {
                $s .= sprintf('%.2F %.2F %.2F %.2F re S ', $xT, $yT, ($w * $k), (-$h * $k));
            } elseif (strlen($border) == 3) {
                if (strpos($border, 'B') === false) {
                    $s .= sprintf('%.2F %.2F m ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= 'S ';
                } elseif (strpos($border, 'L') === false) {
                    $s .= sprintf('%.2F %.2F m ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= 'S ';
                } elseif (strpos($border, 'T') === false) {
                    $s .= sprintf('%.2F %.2F m ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= 'S ';
                } elseif (strpos($border, 'R') === false) {
                    $s .= sprintf('%.2F %.2F m ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= 'S ';
                }
            } elseif (strlen($border) == 2) {
                if ((strpos($border, 'L') !== false) AND (strpos($border, 'T') !== false)) {
                    $s .= sprintf('%.2F %.2F m ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= 'S ';
                } elseif ((strpos($border, 'T') !== false) AND (strpos($border, 'R') !== false)) {
                    $s .= sprintf('%.2F %.2F m ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= 'S ';
                } elseif ((strpos($border, 'R') !== false) AND (strpos($border, 'B') !== false)) {
                    $s .= sprintf('%.2F %.2F m ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= 'S ';
                } elseif ((strpos($border, 'B') !== false) AND (strpos($border, 'L') !== false)) {
                    $s .= sprintf('%.2F %.2F m ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= 'S ';
                } elseif ((strpos($border, 'L') !== false) AND (strpos($border, 'R') !== false)) {
                    $s .= sprintf('%.2F %.2F m ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= 'S ';
                    $s .= sprintf('%.2F %.2F m ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= 'S ';
                } elseif ((strpos($border, 'T') !== false) AND (strpos($border, 'B') !== false)) {
                    $s .= sprintf('%.2F %.2F m ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= 'S ';
                    $s .= sprintf('%.2F %.2F m ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= 'S ';
                }
            } else {
                if (strpos($border, 'L') !== false) {
                    $s .= sprintf('%.2F %.2F m ', $xL, $yL);
                    $s .= sprintf('%.2F %.2F l ', $xT, $yT);
                    $s .= 'S ';
                } elseif (strpos($border, 'T') !== false) {
                    $s .= sprintf('%.2F %.2F m ', $xT, $yT);
                    $s .= sprintf('%.2F %.2F l ', $xR, $yR);
                    $s .= 'S ';
                } elseif (strpos($border, 'R') !== false) {
                    $s .= sprintf('%.2F %.2F m ', $xR, $yR);
                    $s .= sprintf('%.2F %.2F l ', $xB, $yB);
                    $s .= 'S ';
                } elseif (strpos($border, 'B') !== false) {
                    $s .= sprintf('%.2F %.2F m ', $xB, $yB);
                    $s .= sprintf('%.2F %.2F l ', $xL, $yL);
                    $s .= 'S ';
                }
            }
            if (is_array($style) AND !empty($style)) {
                $s .= "\n" . $this->linestyleWidth . ' ' . $this->linestyleCap . ' ' . $this->linestyleJoin . ' ' . $this->linestyleDash . ' ' . $this->DrawColor . "\n";
            }
        }
        return $s;
    }
    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false)
    {
        $prev_cell_margin  = $this->cell_margin;
        $prev_cell_padding = $this->cell_padding;
        $this->adjustCellPadding($border);
        $mc_padding              = $this->cell_padding;
        $mc_margin               = $this->cell_margin;
        $this->cell_padding['T'] = 0;
        $this->cell_padding['B'] = 0;
        $this->setCellMargins(0, 0, 0, 0);
        if ($this->empty_string($this->lasth) OR $reseth) {
            $this->resetLastH();
        }
        if (!$this->empty_string($y)) {
            $this->SetY($y);
        } else {
            $y = $this->GetY();
        }
        $resth = 0;
        if (($this->inPageBody()) AND (($y + $h + $mc_margin['T'] + $mc_margin['B']) > $this->PageBreakTrigger)) {
            $newh  = $this->PageBreakTrigger - $y;
            $resth = $h - $newh;
            $h     = $newh;
        }
        $startpage   = $this->page;
        $startcolumn = $this->current_column;
        if (!$this->empty_string($x)) {
            $this->SetX($x);
        } else {
            $x = $this->GetX();
        }
        list($x, $y) = $this->checkPageRegions(0, $x, $y);
        $oy = $y + $mc_margin['T'];
        if ($this->rtl) {
            $ox = $this->w - $x - $mc_margin['R'];
        } else {
            $ox = $x + $mc_margin['L'];
        }
        $this->x = $ox;
        $this->y = $oy;
        if ($this->empty_string($w) OR ($w <= 0)) {
            if ($this->rtl) {
                $w = $this->x - $this->lMargin - $mc_margin['L'];
            } else {
                $w = $this->w - $this->x - $this->rMargin - $mc_margin['R'];
            }
        }
        $lMargin = $this->lMargin;
        $rMargin = $this->rMargin;
        if ($this->rtl) {
            $this->rMargin = $this->w - $this->x;
            $this->lMargin = $this->x - $w;
        } else {
            $this->lMargin = $this->x;
            $this->rMargin = $this->w - $this->x - $w;
        }
        if ($autopadding) {
            $this->y += $mc_padding['T'];
        }
        if ($ishtml) {
            $this->writeHTML($txt, true, false, $reseth, true, $align);
            $nl = 1;
        } else {
            $prev_FontSizePt = $this->FontSizePt;
            if ($maxh > 0) {
                $text_height = $this->getStringHeight($w, $txt, $reseth, $autopadding, $mc_padding, $border);
                if ($fitcell) {
                    $fmin             = 1;
                    $fmax             = $this->FontSizePt;
                    $prev_text_height = $text_height;
                    $maxit            = 100;
                    while ($maxit > 0) {
                        $fmid = (($fmax + $fmin) / 2);
                        $this->SetFontSize($fmid, false);
                        $this->resetLastH();
                        $text_height = $this->getStringHeight($w, $txt, $reseth, $autopadding, $mc_padding, $border);
                        if (($text_height == $maxh) OR (($text_height < $maxh) AND ($fmin >= ($fmax - 0.01)))) {
                            break;
                        } elseif ($text_height < $maxh) {
                            $fmin = $fmid;
                        } else {
                            $fmax = $fmid;
                        }
                        --$maxit;
                    }
                    $this->SetFontSize($this->FontSizePt);
                }
                if ($text_height < $maxh) {
                    if ($valign == 'M') {
                        $this->y += (($maxh - $text_height) / 2);
                    } elseif ($valign == 'B') {
                        $this->y += ($maxh - $text_height);
                    }
                }
            }
            $nl = $this->Write($this->lasth, $txt, '', 0, $align, true, $stretch, false, true, $maxh, 0, $mc_margin);
            if ($fitcell) {
                $this->SetFontSize($prev_FontSizePt);
            }
        }
        if ($autopadding) {
            $this->y += $mc_padding['B'];
        }
        $currentY = $this->y;
        $endpage  = $this->page;
        if ($resth > 0) {
            $skip     = ($endpage - $startpage);
            $tmpresth = $resth;
            while ($tmpresth > 0) {
                if ($skip <= 0) {
                    $this->checkPageBreak($this->PageBreakTrigger + 1);
                }
                if ($this->num_columns > 1) {
                    $tmpresth -= ($this->h - $this->y - $this->bMargin);
                } else {
                    $tmpresth -= ($this->h - $this->tMargin - $this->bMargin);
                }
                --$skip;
            }
            $currentY = $this->y;
            $endpage  = $this->page;
        }
        $endcolumn = $this->current_column;
        if ($this->num_columns == 0) {
            $this->num_columns = 1;
        }
        $border_start  = $this->getBorderMode($border, $position = 'start');
        $border_end    = $this->getBorderMode($border, $position = 'end');
        $border_middle = $this->getBorderMode($border, $position = 'middle');
        for ($page = $startpage; $page <= $endpage; ++$page) {
            $ccode = '';
            $this->setPage($page);
            if ($this->num_columns < 2) {
                $this->SetX($x);
                $this->y = $this->tMargin;
            }
            if ($page > $startpage) {
                if (($this->rtl) AND ($this->pagedim[$page]['orm'] != $this->pagedim[$startpage]['orm'])) {
                    $this->x -= ($this->pagedim[$page]['orm'] - $this->pagedim[$startpage]['orm']);
                } elseif ((!$this->rtl) AND ($this->pagedim[$page]['olm'] != $this->pagedim[$startpage]['olm'])) {
                    $this->x += ($this->pagedim[$page]['olm'] - $this->pagedim[$startpage]['olm']);
                }
            }
            if ($startpage == $endpage) {
                for ($column = $startcolumn; $column <= $endcolumn; ++$column) {
                    $this->selectColumn($column);
                    if ($this->rtl) {
                        $this->x -= $mc_margin['R'];
                    } else {
                        $this->x += $mc_margin['L'];
                    }
                    if ($startcolumn == $endcolumn) {
                        $cborder = $border;
                        $h       = max($h, ($currentY - $oy));
                        $this->y = $oy;
                    } elseif ($column == $startcolumn) {
                        $cborder = $border_start;
                        $this->y = $oy;
                        $h       = $this->h - $this->y - $this->bMargin;
                    } elseif ($column == $endcolumn) {
                        $cborder = $border_end;
                        $h       = $currentY - $this->y;
                        if ($resth > $h) {
                            $h = $resth;
                        }
                    } else {
                        $cborder = $border_middle;
                        $h       = $this->h - $this->y - $this->bMargin;
                        $resth -= $h;
                    }
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            } elseif ($page == $startpage) {
                for ($column = $startcolumn; $column < $this->num_columns; ++$column) {
                    $this->selectColumn($column);
                    if ($this->rtl) {
                        $this->x -= $mc_margin['R'];
                    } else {
                        $this->x += $mc_margin['L'];
                    }
                    if ($column == $startcolumn) {
                        $cborder = $border_start;
                        $this->y = $oy;
                        $h       = $this->h - $this->y - $this->bMargin;
                    } else {
                        $cborder = $border_middle;
                        $h       = $this->h - $this->y - $this->bMargin;
                        $resth -= $h;
                    }
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            } elseif ($page == $endpage) {
                for ($column = 0; $column <= $endcolumn; ++$column) {
                    $this->selectColumn($column);
                    if ($this->rtl) {
                        $this->x -= $mc_margin['R'];
                    } else {
                        $this->x += $mc_margin['L'];
                    }
                    if ($column == $endcolumn) {
                        $cborder = $border_end;
                        $h       = $currentY - $this->y;
                        if ($resth > $h) {
                            $h = $resth;
                        }
                    } else {
                        $cborder = $border_middle;
                        $h       = $this->h - $this->y - $this->bMargin;
                        $resth -= $h;
                    }
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            } else {
                for ($column = 0; $column < $this->num_columns; ++$column) {
                    $this->selectColumn($column);
                    if ($this->rtl) {
                        $this->x -= $mc_margin['R'];
                    } else {
                        $this->x += $mc_margin['L'];
                    }
                    $cborder = $border_middle;
                    $h       = $this->h - $this->y - $this->bMargin;
                    $resth -= $h;
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            }
            if ($cborder OR $fill) {
                $offsetlen = strlen($ccode);
                if ($this->inxobj) {
                    if (end($this->xobjects[$this->xobjid]['transfmrk']) !== false) {
                        $pagemarkkey = key($this->xobjects[$this->xobjid]['transfmrk']);
                        $pagemark    = $this->xobjects[$this->xobjid]['transfmrk'][$pagemarkkey];
                        $this->xobjects[$this->xobjid]['transfmrk'][$pagemarkkey] += $offsetlen;
                    } else {
                        $pagemark = $this->xobjects[$this->xobjid]['intmrk'];
                        $this->xobjects[$this->xobjid]['intmrk'] += $offsetlen;
                    }
                    $pagebuff                                 = $this->xobjects[$this->xobjid]['outdata'];
                    $pstart                                   = substr($pagebuff, 0, $pagemark);
                    $pend                                     = substr($pagebuff, $pagemark);
                    $this->xobjects[$this->xobjid]['outdata'] = $pstart . $ccode . $pend;
                } else {
                    if (end($this->transfmrk[$this->page]) !== false) {
                        $pagemarkkey = key($this->transfmrk[$this->page]);
                        $pagemark    = $this->transfmrk[$this->page][$pagemarkkey];
                        $this->transfmrk[$this->page][$pagemarkkey] += $offsetlen;
                    } elseif ($this->InFooter) {
                        $pagemark = $this->footerpos[$this->page];
                        $this->footerpos[$this->page] += $offsetlen;
                    } else {
                        $pagemark = $this->intmrk[$this->page];
                        $this->intmrk[$this->page] += $offsetlen;
                    }
                    $pagebuff = $this->getPageBuffer($this->page);
                    $pstart   = substr($pagebuff, 0, $pagemark);
                    $pend     = substr($pagebuff, $pagemark);
                    $this->setPageBuffer($this->page, $pstart . $ccode . $pend);
                }
            }
        }
        $currentY = $this->GetY();
        $this->SetLeftMargin($lMargin);
        $this->SetRightMargin($rMargin);
        if ($ln > 0) {
            $this->SetY($currentY + $mc_margin['B']);
            if ($ln == 2) {
                $this->SetX($x + $w + $mc_margin['L'] + $mc_margin['R']);
            }
        } else {
            $this->setPage($startpage);
            $this->y = $y;
            $this->SetX($x + $w + $mc_margin['L'] + $mc_margin['R']);
        }
        $this->setContentMark();
        $this->cell_padding = $prev_cell_padding;
        $this->cell_margin  = $prev_cell_margin;
        return $nl;
    }
    protected function getBorderMode($brd, $position = 'start')
    {
        if ((!$this->opencell) OR empty($brd)) {
            return $brd;
        }
        if ($brd == 1) {
            $brd = 'LTRB';
        }
        if (is_string($brd)) {
            $slen   = strlen($brd);
            $newbrd = array();
            for ($i = 0; $i < $slen; ++$i) {
                $newbrd[$brd{$i}] = array(
                    'cap' => 'square',
                    'join' => 'miter'
                );
            }
            $brd = $newbrd;
        }
        foreach ($brd as $border => $style) {
            switch ($position) {
                case 'start': {
                    if (strpos($border, 'B') !== false) {
                        $newkey = str_replace('B', '', $border);
                        if (strlen($newkey) > 0) {
                            $brd[$newkey] = $style;
                        }
                        unset($brd[$border]);
                    }
                    break;
                }
                case 'middle': {
                    if (strpos($border, 'B') !== false) {
                        $newkey = str_replace('B', '', $border);
                        if (strlen($newkey) > 0) {
                            $brd[$newkey] = $style;
                        }
                        unset($brd[$border]);
                        $border = $newkey;
                    }
                    if (strpos($border, 'T') !== false) {
                        $newkey = str_replace('T', '', $border);
                        if (strlen($newkey) > 0) {
                            $brd[$newkey] = $style;
                        }
                        unset($brd[$border]);
                    }
                    break;
                }
                case 'end': {
                    if (strpos($border, 'T') !== false) {
                        $newkey = str_replace('T', '', $border);
                        if (strlen($newkey) > 0) {
                            $brd[$newkey] = $style;
                        }
                        unset($brd[$border]);
                    }
                    break;
                }
            }
        }
        return $brd;
    }
    public function getNumLines($txt, $w = 0, $reseth = false, $autopadding = true, $cellpadding = '', $border = 0)
    {
        if ($txt === '') {
            return 1;
        }
        $prev_cell_padding = $this->cell_padding;
        $prev_lasth        = $this->lasth;
        if (is_array($cellpadding)) {
            $this->cell_padding = $cellpadding;
        }
        $this->adjustCellPadding($border);
        if ($this->empty_string($w) OR ($w <= 0)) {
            if ($this->rtl) {
                $w = $this->x - $this->lMargin;
            } else {
                $w = $this->w - $this->rMargin - $this->x;
            }
        }
        $wmax = $w - $this->cell_padding['L'] - $this->cell_padding['R'];
        if ($reseth) {
            $this->resetLastH();
        }
        $lines         = 1;
        $sum           = 0;
        $chars         = $this->utf8Bidi($this->UTF8StringToArray($txt), $txt, $this->tmprtl);
        $charsWidth    = $this->GetArrStringWidth($chars, '', '', 0, true);
        $length        = count($chars);
        $lastSeparator = -1;
        for ($i = 0; $i < $length; ++$i) {
            $charWidth = $charsWidth[$i];
            if (preg_match($this->re_spaces, $this->unichr($chars[$i]))) {
                $lastSeparator = $i;
            }
            if ((($sum + $charWidth) > $wmax) OR ($chars[$i] == 10)) {
                ++$lines;
                if ($lastSeparator != -1) {
                    $i             = $lastSeparator;
                    $lastSeparator = -1;
                    $sum           = 0;
                } else {
                    $sum = $charWidth;
                }
            } else {
                $sum += $charWidth;
            }
        }
        if ($chars[($length - 1)] == 10) {
            --$lines;
        }
        $this->cell_padding = $prev_cell_padding;
        $this->lasth        = $prev_lasth;
        return $lines;
    }
    public function getStringHeight($w, $txt, $reseth = false, $autopadding = true, $cellpadding = '', $border = 0)
    {
        $prev_cell_padding = $this->cell_padding;
        $prev_lasth        = $this->lasth;
        if (is_array($cellpadding)) {
            $this->cell_padding = $cellpadding;
        }
        $this->adjustCellPadding($border);
        $lines  = $this->getNumLines($txt, $w, $reseth, $autopadding, $cellpadding, $border);
        $height = $lines * ($this->FontSize * $this->cell_height_ratio);
        if ($autopadding) {
            $height += ($this->cell_padding['T'] + $this->cell_padding['B']);
        }
        $this->cell_padding = $prev_cell_padding;
        $this->lasth        = $prev_lasth;
        return $height;
    }
    public function Write($h, $txt, $link = '', $fill = false, $align = '', $ln = false, $stretch = 0, $firstline = false, $firstblock = false, $maxh = 0, $wadj = 0, $margin = '')
    {
        list($this->x, $this->y) = $this->checkPageRegions($h, $this->x, $this->y);
        if (strlen($txt) == 0) {
            $txt = ' ';
        }
        if ($margin === '') {
            $margin = $this->cell_margin;
        }
        $s = str_replace("\r", '', $txt);
        if (preg_match($this->unicode->uni_RE_PATTERN_ARABIC, $s)) {
            $arabic = true;
        } else {
            $arabic = false;
        }
        if ($arabic OR ($this->tmprtl == 'R') OR preg_match($this->unicode->uni_RE_PATTERN_RTL, $s)) {
            $rtlmode = true;
        } else {
            $rtlmode = false;
        }
        $chrwidth              = $this->GetCharWidth(46);
        $chars                 = $this->UTF8StringToArray($s);
        $uchars                = $this->UTF8ArrayToUniArray($chars);
        $nb                    = count($chars);
        $shy_replacement       = 45;
        $shy_replacement_char  = $this->unichr($shy_replacement);
        $shy_replacement_width = $this->GetCharWidth($shy_replacement);
        $maxy                  = $this->y + $maxh - $h - $this->cell_padding['T'] - $this->cell_padding['B'];
        $pw                    = $w = $this->w - $this->lMargin - $this->rMargin;
        if ($this->rtl) {
            $w = $this->x - $this->lMargin;
        } else {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = $w - $wadj;
        if (!$firstline) {
            $wmax -= ($this->cell_padding['L'] + $this->cell_padding['R']);
        }
        if ((!$firstline) AND (($chrwidth > $wmax) OR ($this->GetCharWidth($chars[0]) > $wmax))) {
            return '';
        }
        $row_height = max($h, $this->FontSize * $this->cell_height_ratio);
        $start_page = $this->page;
        $i          = 0;
        $j          = 0;
        $sep        = -1;
        $shy        = false;
        $l          = 0;
        $nl         = 0;
        $linebreak  = false;
        $pc         = 0;
        while ($i < $nb) {
            if (($maxh > 0) AND ($this->y >= $maxy)) {
                break;
            }
            $c = $chars[$i];
            if ($c == 10) {
                if ($align == 'J') {
                    if ($this->rtl) {
                        $talign = 'R';
                    } else {
                        $talign = 'L';
                    }
                } else {
                    $talign = $align;
                }
                $tmpstr = $this->UniArrSubString($uchars, $j, $i);
                if ($firstline) {
                    $startx = $this->x;
                    $tmparr = array_slice($chars, $j, ($i - $j));
                    if ($rtlmode) {
                        $tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
                    }
                    $linew = $this->GetArrStringWidth($tmparr);
                    unset($tmparr);
                    if ($this->rtl) {
                        $this->endlinex = $startx - $linew;
                    } else {
                        $this->endlinex = $startx + $linew;
                    }
                    $w              = $linew;
                    $tmpcellpadding = $this->cell_padding;
                    if ($maxh == 0) {
                        $this->SetCellPadding(0);
                    }
                }
                if ($firstblock AND $this->isRTLTextDir()) {
                    $tmpstr = $this->stringRightTrim($tmpstr);
                }
                if (!empty($tmpstr) OR ($this->y < ($this->PageBreakTrigger - $row_height))) {
                    $this->Cell($w, $h, $tmpstr, 0, 1, $talign, $fill, $link, $stretch);
                }
                unset($tmpstr);
                if ($firstline) {
                    $this->cell_padding = $tmpcellpadding;
                    return ($this->UniArrSubString($uchars, $i));
                }
                ++$nl;
                $j   = $i + 1;
                $l   = 0;
                $sep = -1;
                $shy = false;
                if ((($this->y + $this->lasth) > $this->PageBreakTrigger) AND ($this->inPageBody())) {
                    $this->AcceptPageBreak();
                    if ($this->rtl) {
                        $this->x -= $margin['R'];
                    } else {
                        $this->x += $margin['L'];
                    }
                    $this->lMargin += $margin['L'];
                    $this->rMargin += $margin['R'];
                }
                $w    = $this->getRemainingWidth();
                $wmax = $w - $this->cell_padding['L'] - $this->cell_padding['R'];
            } else {
                if (($c != 160) AND (($c == 173) OR preg_match($this->re_spaces, $this->unichr($c)))) {
                    $sep = $i;
                    if ($c == 173) {
                        $shy = true;
                        if ($pc == 45) {
                            $tmp_shy_replacement_width = 0;
                            $tmp_shy_replacement_char  = '';
                        } else {
                            $tmp_shy_replacement_width = $shy_replacement_width;
                            $tmp_shy_replacement_char  = $shy_replacement_char;
                        }
                    } else {
                        $shy = false;
                    }
                }
                if ($this->isUnicodeFont() AND ($arabic)) {
                    $l = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, ($i - $j)), '', $this->tmprtl));
                } else {
                    $l += $this->GetCharWidth($c);
                }
                if (($l > $wmax) OR (($c == 173) AND (($l + $tmp_shy_replacement_width) > $wmax))) {
                    if ($sep == -1) {
                        if (($this->rtl AND ($this->x <= ($this->w - $this->rMargin - $chrwidth))) OR ((!$this->rtl) AND ($this->x >= ($this->lMargin + $chrwidth)))) {
                            $this->Cell($w, $h, '', 0, 1);
                            $linebreak = true;
                            if ($firstline) {
                                return ($this->UniArrSubString($uchars, $j));
                            }
                        } else {
                            $tmpstr = $this->UniArrSubString($uchars, $j, $i);
                            if ($firstline) {
                                $startx = $this->x;
                                $tmparr = array_slice($chars, $j, ($i - $j));
                                if ($rtlmode) {
                                    $tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
                                }
                                $linew = $this->GetArrStringWidth($tmparr);
                                unset($tmparr);
                                if ($this->rtl) {
                                    $this->endlinex = $startx - $linew;
                                } else {
                                    $this->endlinex = $startx + $linew;
                                }
                                $w              = $linew;
                                $tmpcellpadding = $this->cell_padding;
                                if ($maxh == 0) {
                                    $this->SetCellPadding(0);
                                }
                            }
                            if ($firstblock AND $this->isRTLTextDir()) {
                                $tmpstr = $this->stringRightTrim($tmpstr);
                            }
                            $this->Cell($w, $h, $tmpstr, 0, 1, $align, $fill, $link, $stretch);
                            unset($tmpstr);
                            if ($firstline) {
                                $this->cell_padding = $tmpcellpadding;
                                return ($this->UniArrSubString($uchars, $i));
                            }
                            $j = $i;
                            --$i;
                        }
                    } else {
                        if ($this->rtl AND (!$firstblock) AND ($sep < $i)) {
                            $endspace = 1;
                        } else {
                            $endspace = 0;
                        }
                        $strrest = $this->UniArrSubString($uchars, ($sep + $endspace));
                        $nextstr = preg_split('/' . $this->re_space['p'] . '/' . $this->re_space['m'], $this->stringTrim($strrest));
                        if (isset($nextstr[0]) AND ($this->GetStringWidth($nextstr[0]) > $pw)) {
                            $tmpstr = $this->UniArrSubString($uchars, $j, $i);
                            if ($firstline) {
                                $startx = $this->x;
                                $tmparr = array_slice($chars, $j, ($i - $j));
                                if ($rtlmode) {
                                    $tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
                                }
                                $linew = $this->GetArrStringWidth($tmparr);
                                unset($tmparr);
                                if ($this->rtl) {
                                    $this->endlinex = $startx - $linew;
                                } else {
                                    $this->endlinex = $startx + $linew;
                                }
                                $w              = $linew;
                                $tmpcellpadding = $this->cell_padding;
                                if ($maxh == 0) {
                                    $this->SetCellPadding(0);
                                }
                            }
                            if ($firstblock AND $this->isRTLTextDir()) {
                                $tmpstr = $this->stringRightTrim($tmpstr);
                            }
                            $this->Cell($w, $h, $tmpstr, 0, 1, $align, $fill, $link, $stretch);
                            unset($tmpstr);
                            if ($firstline) {
                                $this->cell_padding = $tmpcellpadding;
                                return ($this->UniArrSubString($uchars, $i));
                            }
                            $j = $i;
                            --$i;
                        } else {
                            if ($shy) {
                                $shy_width = $tmp_shy_replacement_width;
                                if ($this->rtl) {
                                    $shy_char_left  = $tmp_shy_replacement_char;
                                    $shy_char_right = '';
                                } else {
                                    $shy_char_left  = '';
                                    $shy_char_right = $tmp_shy_replacement_char;
                                }
                            } else {
                                $shy_width      = 0;
                                $shy_char_left  = '';
                                $shy_char_right = '';
                            }
                            $tmpstr = $this->UniArrSubString($uchars, $j, ($sep + $endspace));
                            if ($firstline) {
                                $startx = $this->x;
                                $tmparr = array_slice($chars, $j, (($sep + $endspace) - $j));
                                if ($rtlmode) {
                                    $tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
                                }
                                $linew = $this->GetArrStringWidth($tmparr);
                                unset($tmparr);
                                if ($this->rtl) {
                                    $this->endlinex = $startx - $linew - $shy_width;
                                } else {
                                    $this->endlinex = $startx + $linew + $shy_width;
                                }
                                $w              = $linew;
                                $tmpcellpadding = $this->cell_padding;
                                if ($maxh == 0) {
                                    $this->SetCellPadding(0);
                                }
                            }
                            if ($firstblock AND $this->isRTLTextDir()) {
                                $tmpstr = $this->stringRightTrim($tmpstr);
                            }
                            $this->Cell($w, $h, $shy_char_left . $tmpstr . $shy_char_right, 0, 1, $align, $fill, $link, $stretch);
                            unset($tmpstr);
                            if ($firstline) {
                                $this->cell_padding = $tmpcellpadding;
                                return ($this->UniArrSubString($uchars, ($sep + $endspace)));
                            }
                            $i   = $sep;
                            $sep = -1;
                            $shy = false;
                            $j   = ($i + 1);
                        }
                    }
                    if ((($this->y + $this->lasth) > $this->PageBreakTrigger) AND ($this->inPageBody())) {
                        $this->AcceptPageBreak();
                        if ($this->rtl) {
                            $this->x -= $margin['R'];
                        } else {
                            $this->x += $margin['L'];
                        }
                        $this->lMargin += $margin['L'];
                        $this->rMargin += $margin['R'];
                    }
                    $w    = $this->getRemainingWidth();
                    $wmax = $w - $this->cell_padding['L'] - $this->cell_padding['R'];
                    if ($linebreak) {
                        $linebreak = false;
                    } else {
                        ++$nl;
                        $l = 0;
                    }
                }
            }
            $pc = $c;
            ++$i;
        }
        if ($l > 0) {
            switch ($align) {
                case 'J':
                case 'C': {
                    $w = $w;
                    break;
                }
                case 'L': {
                    if ($this->rtl) {
                        $w = $w;
                    } else {
                        $w = $l;
                    }
                    break;
                }
                case 'R': {
                    if ($this->rtl) {
                        $w = $l;
                    } else {
                        $w = $w;
                    }
                    break;
                }
                default: {
                    $w = $l;
                    break;
                }
            }
            $tmpstr = $this->UniArrSubString($uchars, $j, $nb);
            if ($firstline) {
                $startx = $this->x;
                $tmparr = array_slice($chars, $j, ($nb - $j));
                if ($rtlmode) {
                    $tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
                }
                $linew = $this->GetArrStringWidth($tmparr);
                unset($tmparr);
                if ($this->rtl) {
                    $this->endlinex = $startx - $linew;
                } else {
                    $this->endlinex = $startx + $linew;
                }
                $w              = $linew;
                $tmpcellpadding = $this->cell_padding;
                if ($maxh == 0) {
                    $this->SetCellPadding(0);
                }
            }
            if ($firstblock AND $this->isRTLTextDir()) {
                $tmpstr = $this->stringRightTrim($tmpstr);
            }
            $this->Cell($w, $h, $tmpstr, 0, $ln, $align, $fill, $link, $stretch);
            unset($tmpstr);
            if ($firstline) {
                $this->cell_padding = $tmpcellpadding;
                return ($this->UniArrSubString($uchars, $nb));
            }
            ++$nl;
        }
        if ($firstline) {
            return '';
        }
        return $nl;
    }
    protected function getRemainingWidth()
    {
        list($this->x, $this->y) = $this->checkPageRegions(0, $this->x, $this->y);
        if ($this->rtl) {
            return ($this->x - $this->lMargin);
        } else {
            return ($this->w - $this->rMargin - $this->x);
        }
    }
    public function UTF8ArrSubString($strarr, $start = '', $end = '')
    {
        if (strlen($start) == 0) {
            $start = 0;
        }
        if (strlen($end) == 0) {
            $end = count($strarr);
        }
        $string = '';
        for ($i = $start; $i < $end; ++$i) {
            $string .= $this->unichr($strarr[$i]);
        }
        return $string;
    }
    public function UniArrSubString($uniarr, $start = '', $end = '')
    {
        if (strlen($start) == 0) {
            $start = 0;
        }
        if (strlen($end) == 0) {
            $end = count($uniarr);
        }
        $string = '';
        for ($i = $start; $i < $end; ++$i) {
            $string .= $uniarr[$i];
        }
        return $string;
    }
    public function UTF8ArrayToUniArray($ta)
    {
        return array_map(array(
            $this,
            'unichr'
        ), $ta);
    }
    public function unichr($c)
    {
        if (!$this->isunicode) {
            return chr($c);
        } elseif ($c <= 0x7F) {
            return chr($c);
        } elseif ($c <= 0x7FF) {
            return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
        } elseif ($c <= 0xFFFF) {
            return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F) . chr(0x80 | $c & 0x3F);
        } elseif ($c <= 0x10FFFF) {
            return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F) . chr(0x80 | $c >> 6 & 0x3F) . chr(0x80 | $c & 0x3F);
        } else {
            return '';
        }
    }
    public function getImageFileType($imgfile, $iminfo = array())
    {
        $type = '';
        if (isset($iminfo['mime']) AND !empty($iminfo['mime'])) {
            $mime = explode('/', $iminfo['mime']);
            if ((count($mime) > 1) AND ($mime[0] == 'image') AND (!empty($mime[1]))) {
                $type = strtolower(trim($mime[1]));
            }
        }
        if (empty($type)) {
            $fileinfo = pathinfo($imgfile);
            if (isset($fileinfo['extension']) AND (!$this->empty_string($fileinfo['extension']))) {
                $type = strtolower(trim($fileinfo['extension']));
            }
        }
        if ($type == 'jpg') {
            $type = 'jpeg';
        }
        return $type;
    }
    protected function fitBlock($w, $h, $x, $y, $fitonpage = false)
    {
        if ($w <= 0) {
            $w = ($this->w - $this->lMargin - $this->rMargin);
        }
        if ($h <= 0) {
            $h = ($this->PageBreakTrigger - $this->tMargin);
        }
        if ($fitonpage OR $this->AutoPageBreak) {
            $ratio_wh = ($w / $h);
            if ($h > ($this->PageBreakTrigger - $this->tMargin)) {
                $h = $this->PageBreakTrigger - $this->tMargin;
                $w = ($h * $ratio_wh);
            }
            if ($fitonpage) {
                $maxw = ($this->w - $this->lMargin - $this->rMargin);
                if ($w > $maxw) {
                    $w = $maxw;
                    $h = ($w / $ratio_wh);
                }
            }
        }
        $prev_x = $this->x;
        $prev_y = $this->y;
        if ($this->checkPageBreak($h, $y) OR ($this->y < $prev_y)) {
            $y = $this->y;
            if ($this->rtl) {
                $x += ($prev_x - $this->x);
            } else {
                $x += ($this->x - $prev_x);
            }
            $this->newline = true;
        }
        if ($fitonpage) {
            $ratio_wh = ($w / $h);
            if (($y + $h) > $this->PageBreakTrigger) {
                $h = $this->PageBreakTrigger - $y;
                $w = ($h * $ratio_wh);
            }
            if ((!$this->rtl) AND (($x + $w) > ($this->w - $this->rMargin))) {
                $w = $this->w - $this->rMargin - $x;
                $h = ($w / $ratio_wh);
            } elseif (($this->rtl) AND (($x - $w) < ($this->lMargin))) {
                $w = $x - $this->lMargin;
                $h = ($w / $ratio_wh);
            }
        }
        return array(
            $w,
            $h,
            $x,
            $y
        );
    }
    public function Image($file, $x = '', $y = '', $w = 0, $h = 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = array())
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        $cached_file = false;
        $exurl       = '';
        if ($file{0} === '@') {
            $imgdata = substr($file, 1);
            $file    = K_PATH_CACHE . 'img_' . md5($imgdata);
            $fp      = fopen($file, 'w');
            fwrite($fp, $imgdata);
            fclose($fp);
            unset($imgdata);
            $cached_file = true;
            $imsize      = @getimagesize($file);
            if ($imsize === FALSE) {
                unlink($file);
                $cached_file = false;
            }
        } else {
            if ($file{0} === '*') {
                $file  = substr($file, 1);
                $exurl = $file;
            }
            if (!@file_exists($file)) {
                $file = str_replace(' ', '%20', $file);
            }
            if (@file_exists($file)) {
                $imsize = @getimagesize($file);
            } else {
                $imsize = false;
            }
            if ($imsize === FALSE) {
                if (function_exists('curl_init')) {
                    $cs = curl_init();
                    curl_setopt($cs, CURLOPT_URL, $file);
                    curl_setopt($cs, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($cs, CURLOPT_FAILONERROR, true);
                    curl_setopt($cs, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cs, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($cs, CURLOPT_TIMEOUT, 30);
                    $imgdata = curl_exec($cs);
                    curl_close($cs);
                    if ($imgdata !== FALSE) {
                        $file = K_PATH_CACHE . 'img_' . md5($imgdata);
                        $fp   = fopen($file, 'w');
                        fwrite($fp, $imgdata);
                        fclose($fp);
                        unset($imgdata);
                        $cached_file = true;
                        $imsize      = @getimagesize($file);
                        if ($imsize === FALSE) {
                            unlink($file);
                            $cached_file = false;
                        }
                    }
                } elseif (($w > 0) AND ($h > 0)) {
                    $pw     = $this->getHTMLUnitToUnits($w, 0, $this->pdfunit, true) * $this->imgscale * $this->k;
                    $ph     = $this->getHTMLUnitToUnits($h, 0, $this->pdfunit, true) * $this->imgscale * $this->k;
                    $imsize = array(
                        $pw,
                        $ph
                    );
                }
            }
        }
        if ($imsize === FALSE) {
            if (substr($file, 0, -34) == K_PATH_CACHE . 'msk') {
                $pw     = $this->getHTMLUnitToUnits($w, 0, $this->pdfunit, true) * $this->imgscale * $this->k;
                $ph     = $this->getHTMLUnitToUnits($h, 0, $this->pdfunit, true) * $this->imgscale * $this->k;
                $imsize = array(
                    $pw,
                    $ph
                );
            } else {
                $this->Error('[Image] Unable to get image: ' . $file);
            }
        }
        list($pixw, $pixh) = $imsize;
        if (($w <= 0) AND ($h <= 0)) {
            $w = $this->pixelsToUnits($pixw);
            $h = $this->pixelsToUnits($pixh);
        } elseif ($w <= 0) {
            $w = $h * $pixw / $pixh;
        } elseif ($h <= 0) {
            $h = $w * $pixh / $pixw;
        } elseif (($fitbox !== false) AND ($w > 0) AND ($h > 0)) {
            if (strlen($fitbox) !== 2) {
                $fitbox = '--';
            }
            if ((($w * $pixh) / ($h * $pixw)) < 1) {
                $oldh  = $h;
                $h     = $w * $pixh / $pixw;
                $hdiff = ($oldh - $h);
                switch (strtoupper($fitbox{1})) {
                    case 'T': {
                        break;
                    }
                    case 'M': {
                        $y += ($hdiff / 2);
                        break;
                    }
                    case 'B': {
                        $y += $hdiff;
                        break;
                    }
                }
            } else {
                $oldw  = $w;
                $w     = $h * $pixw / $pixh;
                $wdiff = ($oldw - $w);
                switch (strtoupper($fitbox{0})) {
                    case 'L': {
                        if ($this->rtl) {
                            $x -= $wdiff;
                        }
                        break;
                    }
                    case 'C': {
                        if ($this->rtl) {
                            $x -= ($wdiff / 2);
                        } else {
                            $x += ($wdiff / 2);
                        }
                        break;
                    }
                    case 'R': {
                        if (!$this->rtl) {
                            $x += $wdiff;
                        }
                        break;
                    }
                }
            }
        }
        list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, $fitonpage);
        $neww    = round($w * $this->k * $dpi / $this->dpi);
        $newh    = round($h * $this->k * $dpi / $this->dpi);
        $newsize = ($neww * $newh);
        $pixsize = ($pixw * $pixh);
        if (intval($resize) == 2) {
            $resize = true;
        } elseif ($newsize >= $pixsize) {
            $resize = false;
        }
        $newimage = true;
        if (in_array($file, $this->imagekeys)) {
            $newimage = false;
            $info     = $this->getImageBuffer($file);
            if (substr($file, 0, -34) != K_PATH_CACHE . 'msk') {
                $oldsize = ($info['w'] * $info['h']);
                if ((($oldsize < $newsize) AND ($resize)) OR (($oldsize < $pixsize) AND (!$resize))) {
                    $newimage = true;
                }
            }
        } elseif (substr($file, 0, -34) != K_PATH_CACHE . 'msk') {
            $filehash       = md5($file);
            $tempfile_plain = K_PATH_CACHE . 'mskp_' . $filehash;
            $tempfile_alpha = K_PATH_CACHE . 'mska_' . $filehash;
            if (in_array($tempfile_plain, $this->imagekeys)) {
                $info    = $this->getImageBuffer($tempfile_plain);
                $oldsize = ($info['w'] * $info['h']);
                if ((($oldsize < $newsize) AND ($resize)) OR (($oldsize < $pixsize) AND (!$resize))) {
                    $newimage = true;
                } else {
                    $newimage = false;
                    $imgmask  = $this->Image($tempfile_alpha, $x, $y, $w, $h, 'PNG', '', '', $resize, $dpi, '', true, false);
                    return $this->Image($tempfile_plain, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, false, $imgmask);
                }
            }
        }
        if ($newimage) {
            $type = strtolower($type);
            if ($type == '') {
                $type = $this->getImageFileType($file, $imsize);
            } elseif ($type == 'jpg') {
                $type = 'jpeg';
            }
            $mqr = $this->get_mqr();
            $this->set_mqr(false);
            $mtd        = '_parse' . $type;
            $gdfunction = 'imagecreatefrom' . $type;
            $info       = false;
            if ((method_exists($this, $mtd)) AND (!($resize AND function_exists($gdfunction)))) {
                $info = $this->$mtd($file);
                if ($info == 'pngalpha') {
                    return $this->ImagePngAlpha($file, $x, $y, $pixw, $pixh, $w, $h, 'PNG', $link, $align, $resize, $dpi, $palign, $filehash);
                }
            }
            if (!$info) {
                if (function_exists($gdfunction)) {
                    $img = $gdfunction($file);
                    if ($resize) {
                        $imgr = imagecreatetruecolor($neww, $newh);
                        if (($type == 'gif') OR ($type == 'png')) {
                            $imgr = $this->_setGDImageTransparency($imgr, $img);
                        }
                        imagecopyresampled($imgr, $img, 0, 0, 0, 0, $neww, $newh, $pixw, $pixh);
                        if (($type == 'gif') OR ($type == 'png')) {
                            $info = $this->_toPNG($imgr);
                        } else {
                            $info = $this->_toJPEG($imgr);
                        }
                    } else {
                        if (($type == 'gif') OR ($type == 'png')) {
                            $info = $this->_toPNG($img);
                        } else {
                            $info = $this->_toJPEG($img);
                        }
                    }
                } elseif (extension_loaded('imagick')) {
                    $img = new Imagick();
                    if ($type == 'SVG') {
                        $svgimg = file_get_contents($file);
                        $regs   = array();
                        if (preg_match('/<svg([^\>]*)>/si', $svgimg, $regs)) {
                            $svgtag = $regs[1];
                            $tmp    = array();
                            if (preg_match('/[\s]+width[\s]*=[\s]*"([^"]*)"/si', $svgtag, $tmp)) {
                                $ow     = $this->getHTMLUnitToUnits($tmp[1], 1, $this->svgunit, false);
                                $owu    = sprintf('%.3F', ($ow * $dpi / 72)) . $this->pdfunit;
                                $svgtag = preg_replace('/[\s]+width[\s]*=[\s]*"[^"]*"/si', ' width="' . $owu . '"', $svgtag, 1);
                            } else {
                                $ow = $w;
                            }
                            $tmp = array();
                            if (preg_match('/[\s]+height[\s]*=[\s]*"([^"]*)"/si', $svgtag, $tmp)) {
                                $oh     = $this->getHTMLUnitToUnits($tmp[1], 1, $this->svgunit, false);
                                $ohu    = sprintf('%.3F', ($oh * $dpi / 72)) . $this->pdfunit;
                                $svgtag = preg_replace('/[\s]+height[\s]*=[\s]*"[^"]*"/si', ' height="' . $ohu . '"', $svgtag, 1);
                            } else {
                                $oh = $h;
                            }
                            $tmp = array();
                            if (!preg_match('/[\s]+viewBox[\s]*=[\s]*"[\s]*([0-9\.]+)[\s]+([0-9\.]+)[\s]+([0-9\.]+)[\s]+([0-9\.]+)[\s]*"/si', $svgtag, $tmp)) {
                                $vbw    = ($ow * $this->imgscale * $this->k);
                                $vbh    = ($oh * $this->imgscale * $this->k);
                                $vbox   = sprintf(' viewBox="0 0 %.3F %.3F" ', $vbw, $vbh);
                                $svgtag = $vbox . $svgtag;
                            }
                            $svgimg = preg_replace('/<svg([^\>]*)>/si', '<svg' . $svgtag . '>', $svgimg, 1);
                        }
                        $img->readImageBlob($svgimg);
                    } else {
                        $img->readImage($file);
                    }
                    if ($resize) {
                        $img->resizeImage($neww, $newh, 10, 1, false);
                    }
                    $img->setCompressionQuality($this->jpeg_quality);
                    $img->setImageFormat('jpeg');
                    $tempname = tempnam(K_PATH_CACHE, 'jpg_');
                    $img->writeImage($tempname);
                    $info = $this->_parsejpeg($tempname);
                    unlink($tempname);
                    $img->destroy();
                } else {
                    return;
                }
            }
            if ($info === false) {
                return;
            }
            $this->set_mqr($mqr);
            if ($ismask) {
                $info['cs'] = 'DeviceGray';
            }
            $info['i'] = $this->numimages;
            if (!in_array($file, $this->imagekeys)) {
                ++$info['i'];
            }
            if ($imgmask !== false) {
                $info['masked'] = $imgmask;
            }
            if (!empty($exurl)) {
                $info['exurl'] = $exurl;
            }
            $info['altimgs'] = $altimgs;
            $this->setImageBuffer($file, $info);
        }
        if ($cached_file) {
            unlink($file);
        }
        $this->img_rb_y = $y + $h;
        if ($this->rtl) {
            if ($palign == 'L') {
                $ximg = $this->lMargin;
            } elseif ($palign == 'C') {
                $ximg = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $ximg = $this->w - $this->rMargin - $w;
            } else {
                $ximg = $x - $w;
            }
            $this->img_rb_x = $ximg;
        } else {
            if ($palign == 'L') {
                $ximg = $this->lMargin;
            } elseif ($palign == 'C') {
                $ximg = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $ximg = $this->w - $this->rMargin - $w;
            } else {
                $ximg = $x;
            }
            $this->img_rb_x = $ximg + $w;
        }
        if ($ismask OR $hidden) {
            return $info['i'];
        }
        $xkimg = $ximg * $this->k;
        if (!$alt) {
            $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%u Do Q', ($w * $this->k), ($h * $this->k), $xkimg, (($this->h - ($y + $h)) * $this->k), $info['i']));
        }
        if (!empty($border)) {
            $bx      = $this->x;
            $by      = $this->y;
            $this->x = $ximg;
            if ($this->rtl) {
                $this->x += $w;
            }
            $this->y = $y;
            $this->Cell($w, $h, '', $border, 0, '', 0, '', 0, true);
            $this->x = $bx;
            $this->y = $by;
        }
        if ($link) {
            $this->Link($ximg, $y, $w, $h, $link, 0);
        }
        switch ($align) {
            case 'T': {
                $this->y = $y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'M': {
                $this->y = $y + round($h / 2);
                $this->x = $this->img_rb_x;
                break;
            }
            case 'B': {
                $this->y = $this->img_rb_y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'N': {
                $this->SetY($this->img_rb_y);
                break;
            }
            default: {
                break;
            }
        }
        $this->endlinex = $this->img_rb_x;
        if ($this->inxobj) {
            $this->xobjects[$this->xobjid]['images'][] = $info['i'];
        }
        return $info['i'];
    }
    public function set_mqr($mqr)
    {
        if (!defined('PHP_VERSION_ID')) {
            $version = PHP_VERSION;
            define('PHP_VERSION_ID', (($version{0} * 10000) + ($version{2} * 100) + $version{4}));
        }
        if (PHP_VERSION_ID < 50300) {
            @set_magic_quotes_runtime($mqr);
        }
    }
    public function get_mqr()
    {
        if (!defined('PHP_VERSION_ID')) {
            $version = PHP_VERSION;
            define('PHP_VERSION_ID', (($version{0} * 10000) + ($version{2} * 100) + $version{4}));
        }
        if (PHP_VERSION_ID < 50300) {
            return @get_magic_quotes_runtime();
        }
        return 0;
    }
    protected function _toJPEG($image)
    {
        $tempname = tempnam(K_PATH_CACHE, 'jpg_');
        imagejpeg($image, $tempname, $this->jpeg_quality);
        imagedestroy($image);
        $retvars = $this->_parsejpeg($tempname);
        unlink($tempname);
        return $retvars;
    }
    protected function _toPNG($image)
    {
        $tempname = tempnam(K_PATH_CACHE, 'jpg_');
        imageinterlace($image, 0);
        imagepng($image, $tempname);
        imagedestroy($image);
        $retvars = $this->_parsepng($tempname);
        unlink($tempname);
        return $retvars;
    }
    protected function _setGDImageTransparency($new_image, $image)
    {
        $tid  = imagecolortransparent($image);
        $tcol = array(
            'red' => 255,
            'green' => 255,
            'blue' => 255
        );
        if ($tid >= 0) {
            $tcol = imagecolorsforindex($image, $tid);
        }
        $tid = imagecolorallocate($new_image, $tcol['red'], $tcol['green'], $tcol['blue']);
        imagefill($new_image, 0, 0, $tid);
        imagecolortransparent($new_image, $tid);
        return $new_image;
    }
    protected function _parsejpeg($file)
    {
        $a = getimagesize($file);
        if (empty($a)) {
            $this->Error('Missing or incorrect image file: ' . $file);
        }
        if ($a[2] != 2) {
            $this->Error('Not a JPEG file: ' . $file);
        }
        if ((!isset($a['channels'])) OR ($a['channels'] == 3)) {
            $colspace = 'DeviceRGB';
        } elseif ($a['channels'] == 4) {
            $colspace = 'DeviceCMYK';
        } else {
            $colspace = 'DeviceGray';
        }
        $bpc  = isset($a['bits']) ? $a['bits'] : 8;
        $data = file_get_contents($file);
        return array(
            'w' => $a[0],
            'h' => $a[1],
            'cs' => $colspace,
            'bpc' => $bpc,
            'f' => 'DCTDecode',
            'data' => $data
        );
    }
    protected function _parsepng($file)
    {
        $f = fopen($file, 'rb');
        if ($f === false) {
            $this->Error('Can\'t open image file: ' . $file);
        }
        if (fread($f, 8) != chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10)) {
            $this->Error('Not a PNG file: ' . $file);
        }
        fread($f, 4);
        if (fread($f, 4) != 'IHDR') {
            $this->Error('Incorrect PNG file: ' . $file);
        }
        $w   = $this->_freadint($f);
        $h   = $this->_freadint($f);
        $bpc = ord(fread($f, 1));
        if ($bpc > 8) {
            fclose($f);
            return false;
        }
        $ct = ord(fread($f, 1));
        if ($ct == 0) {
            $colspace = 'DeviceGray';
        } elseif ($ct == 2) {
            $colspace = 'DeviceRGB';
        } elseif ($ct == 3) {
            $colspace = 'Indexed';
        } else {
            fclose($f);
            return 'pngalpha';
        }
        if (ord(fread($f, 1)) != 0) {
            fclose($f);
            return false;
        }
        if (ord(fread($f, 1)) != 0) {
            fclose($f);
            return false;
        }
        if (ord(fread($f, 1)) != 0) {
            fclose($f);
            return false;
        }
        fread($f, 4);
        $parms = '/DecodeParms << /Predictor 15 /Colors ' . ($ct == 2 ? 3 : 1) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w . ' >>';
        $pal   = '';
        $trns  = '';
        $data  = '';
        do {
            $n    = $this->_freadint($f);
            $type = fread($f, 4);
            if ($type == 'PLTE') {
                $pal = $this->rfread($f, $n);
                fread($f, 4);
            } elseif ($type == 'tRNS') {
                $t = $this->rfread($f, $n);
                if ($ct == 0) {
                    $trns = array(
                        ord(substr($t, 1, 1))
                    );
                } elseif ($ct == 2) {
                    $trns = array(
                        ord(substr($t, 1, 1)),
                        ord(substr($t, 3, 1)),
                        ord(substr($t, 5, 1))
                    );
                } else {
                    $pos = strpos($t, chr(0));
                    if ($pos !== false) {
                        $trns = array(
                            $pos
                        );
                    }
                }
                fread($f, 4);
            } elseif ($type == 'IDAT') {
                $data .= $this->rfread($f, $n);
                fread($f, 4);
            } elseif ($type == 'IEND') {
                break;
            } else {
                $this->rfread($f, $n + 4);
            }
        } while ($n);
        if (($colspace == 'Indexed') AND (empty($pal))) {
            fclose($f);
            return false;
        }
        fclose($f);
        return array(
            'w' => $w,
            'h' => $h,
            'cs' => $colspace,
            'bpc' => $bpc,
            'f' => 'FlateDecode',
            'parms' => $parms,
            'pal' => $pal,
            'trns' => $trns,
            'data' => $data
        );
    }
    protected function rfread($handle, $length)
    {
        $data = fread($handle, $length);
        if ($data === false) {
            return false;
        }
        $rest = $length - strlen($data);
        if ($rest > 0) {
            $data .= $this->rfread($handle, $rest);
        }
        return $data;
    }
    protected function ImagePngAlpha($file, $x, $y, $wpx, $hpx, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $filehash = '')
    {
        if (empty($filehash)) {
            $filehash = md5($file);
        }
        $tempfile_plain = K_PATH_CACHE . 'mskp_' . $filehash;
        $tempfile_alpha = K_PATH_CACHE . 'mska_' . $filehash;
        if (extension_loaded('imagick')) {
            $img = new Imagick();
            $img->readImage($file);
            $imga = $img->clone();
            $img->separateImageChannel(8);
            $img->negateImage(true);
            $img->setImageFormat('png');
            $img->writeImage($tempfile_alpha);
            $imga->separateImageChannel(39);
            $imga->setImageFormat('png');
            $imga->writeImage($tempfile_plain);
        } else {
            $img      = imagecreatefrompng($file);
            $imgalpha = imagecreate($wpx, $hpx);
            for ($c = 0; $c < 256; ++$c) {
                ImageColorAllocate($imgalpha, $c, $c, $c);
            }
            for ($xpx = 0; $xpx < $wpx; ++$xpx) {
                for ($ypx = 0; $ypx < $hpx; ++$ypx) {
                    $color = imagecolorat($img, $xpx, $ypx);
                    $alpha = ($color >> 24);
                    $alpha = (((127 - $alpha) / 127) * 255);
                    $alpha = $this->getGDgamma($alpha);
                    imagesetpixel($imgalpha, $xpx, $ypx, $alpha);
                }
            }
            imagepng($imgalpha, $tempfile_alpha);
            imagedestroy($imgalpha);
            $imgplain = imagecreatetruecolor($wpx, $hpx);
            imagecopy($imgplain, $img, 0, 0, 0, 0, $wpx, $hpx);
            imagepng($imgplain, $tempfile_plain);
            imagedestroy($imgplain);
        }
        $imgmask = $this->Image($tempfile_alpha, $x, $y, $w, $h, 'PNG', '', '', $resize, $dpi, '', true, false);
        $this->Image($tempfile_plain, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, false, $imgmask);
        unlink($tempfile_alpha);
        unlink($tempfile_plain);
    }
    protected function getGDgamma($v)
    {
        return (pow(($v / 255), 2.2) * 255);
    }
    public function Ln($h = '', $cell = false)
    {
        if (($this->num_columns > 1) AND ($this->y == $this->columns[$this->current_column]['y']) AND isset($this->columns[$this->current_column]['x']) AND ($this->x == $this->columns[$this->current_column]['x'])) {
            return;
        }
        if ($cell) {
            if ($this->rtl) {
                $cellpadding = $this->cell_padding['R'];
            } else {
                $cellpadding = $this->cell_padding['L'];
            }
        } else {
            $cellpadding = 0;
        }
        if ($this->rtl) {
            $this->x = $this->w - $this->rMargin - $cellpadding;
        } else {
            $this->x = $this->lMargin + $cellpadding;
        }
        if (is_string($h)) {
            $this->y += $this->lasth;
        } else {
            $this->y += $h;
        }
        $this->newline = true;
    }
    public function GetX()
    {
        if ($this->rtl) {
            return ($this->w - $this->x);
        } else {
            return $this->x;
        }
    }
    public function GetAbsX()
    {
        return $this->x;
    }
    public function GetY()
    {
        return $this->y;
    }
    public function SetX($x, $rtloff = false)
    {
        if (!$rtloff AND $this->rtl) {
            if ($x >= 0) {
                $this->x = $this->w - $x;
            } else {
                $this->x = abs($x);
            }
        } else {
            if ($x >= 0) {
                $this->x = $x;
            } else {
                $this->x = $this->w + $x;
            }
        }
        if ($this->x < 0) {
            $this->x = 0;
        }
        if ($this->x > $this->w) {
            $this->x = $this->w;
        }
    }
    public function SetY($y, $resetx = true, $rtloff = false)
    {
        if ($resetx) {
            if (!$rtloff AND $this->rtl) {
                $this->x = $this->w - $this->rMargin;
            } else {
                $this->x = $this->lMargin;
            }
        }
        if ($y >= 0) {
            $this->y = $y;
        } else {
            $this->y = $this->h + $y;
        }
        if ($this->y < 0) {
            $this->y = 0;
        }
        if ($this->y > $this->h) {
            $this->y = $this->h;
        }
    }
    public function SetXY($x, $y, $rtloff = false)
    {
        $this->SetY($y, false, $rtloff);
        $this->SetX($x, $rtloff);
    }
    protected function sendOutputData($data, $lenght)
    {
        if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            header('Content-Length: ' . $lenght);
        }
        echo $data;
    }
    public function Output($name = 'doc.pdf', $dest = 'I')
    {
        if ($this->state < 3) {
            $this->Close();
        }
        if (is_bool($dest)) {
            $dest = $dest ? 'D' : 'F';
        }
        $dest = strtoupper($dest);
        if ($dest{0} != 'F') {
            $name = preg_replace('/[\s]+/', '_', $name);
            $name = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $name);
        }
        if ($this->sign) {
            $pdfdoc = $this->getBuffer();
            $pdfdoc = substr($pdfdoc, 0, -1);
            if (isset($this->diskcache) AND $this->diskcache) {
                unlink($this->buffer);
            }
            unset($this->buffer);
            $byterange_string_len = strlen($this->byterange_string);
            $byte_range           = array();
            $byte_range[0]        = 0;
            $byte_range[1]        = strpos($pdfdoc, $this->byterange_string) + $byterange_string_len + 10;
            $byte_range[2]        = $byte_range[1] + $this->signature_max_length + 2;
            $byte_range[3]        = strlen($pdfdoc) - $byte_range[2];
            $pdfdoc               = substr($pdfdoc, 0, $byte_range[1]) . substr($pdfdoc, $byte_range[2]);
            $byterange            = sprintf('/ByteRange[0 %u %u %u]', $byte_range[1], $byte_range[2], $byte_range[3]);
            $byterange .= str_repeat(' ', ($byterange_string_len - strlen($byterange)));
            $pdfdoc  = str_replace($this->byterange_string, $byterange, $pdfdoc);
            $tempdoc = tempnam(K_PATH_CACHE, 'tmppdf_');
            $f       = fopen($tempdoc, 'wb');
            if (!$f) {
                $this->Error('Unable to create temporary file: ' . $tempdoc);
            }
            $pdfdoc_length = strlen($pdfdoc);
            fwrite($f, $pdfdoc, $pdfdoc_length);
            fclose($f);
            $tempsign = tempnam(K_PATH_CACHE, 'tmpsig_');
            if (empty($this->signature_data['extracerts'])) {
                openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array(
                    $this->signature_data['privkey'],
                    $this->signature_data['password']
                ), array(), PKCS7_BINARY | PKCS7_DETACHED);
            } else {
                openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array(
                    $this->signature_data['privkey'],
                    $this->signature_data['password']
                ), array(), PKCS7_BINARY | PKCS7_DETACHED, $this->signature_data['extracerts']);
            }
            unlink($tempdoc);
            $signature = file_get_contents($tempsign);
            unlink($tempsign);
            $signature = substr($signature, $pdfdoc_length);
            $signature = substr($signature, (strpos($signature, "%%EOF\n\n------") + 13));
            $tmparr    = explode("\n\n", $signature);
            $signature = $tmparr[1];
            unset($tmparr);
            $signature       = base64_decode(trim($signature));
            $signature       = current(unpack('H*', $signature));
            $signature       = str_pad($signature, $this->signature_max_length, '0');
            $this->diskcache = false;
            $this->buffer    = substr($pdfdoc, 0, $byte_range[1]) . '<' . $signature . '>' . substr($pdfdoc, $byte_range[1]);
            $this->bufferlen = strlen($this->buffer);
        }
        switch ($dest) {
            case 'I': {
                if (ob_get_contents()) {
                    $this->Error('Some data has already been output, can\'t send PDF file');
                }
                if (php_sapi_name() != 'cli') {
                    header('Content-Type: application/pdf');
                    if (headers_sent()) {
                        $this->Error('Some data has already been output to browser, can\'t send PDF file');
                    }
                    header('Cache-Control: public, must-revalidate, max-age=0');
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Content-Disposition: inline; filename="' . basename($name) . '";');
                    $this->sendOutputData($this->getBuffer(), $this->bufferlen);
                } else {
                    echo $this->getBuffer();
                }
                break;
            }
            case 'D': {
                if (ob_get_contents()) {
                    $this->Error('Some data has already been output, can\'t send PDF file');
                }
                header('Content-Description: File Transfer');
                if (headers_sent()) {
                    $this->Error('Some data has already been output to browser, can\'t send PDF file');
                }
                header('Cache-Control: public, must-revalidate, max-age=0');
                header('Pragma: public');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                if (strpos(php_sapi_name(), 'cgi') === false) {
                    header('Content-Type: application/force-download');
                    header('Content-Type: application/octet-stream', false);
                    header('Content-Type: application/download', false);
                    header('Content-Type: application/pdf', false);
                } else {
                    header('Content-Type: application/pdf');
                }
                header('Content-Disposition: attachment; filename="' . basename($name) . '";');
                header('Content-Transfer-Encoding: binary');
                $this->sendOutputData($this->getBuffer(), $this->bufferlen);
                break;
            }
            case 'F':
            case 'FI':
            case 'FD': {
                if ($this->diskcache) {
                    copy($this->buffer, $name);
                } else {
                    $f = fopen($name, 'wb');
                    if (!$f) {
                        $this->Error('Unable to create output file: ' . $name);
                    }
                    fwrite($f, $this->getBuffer(), $this->bufferlen);
                    fclose($f);
                }
                if ($dest == 'FI') {
                    header('Content-Type: application/pdf');
                    header('Cache-Control: public, must-revalidate, max-age=0');
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Content-Disposition: inline; filename="' . basename($name) . '";');
                    $this->sendOutputData(file_get_contents($name), filesize($name));
                } elseif ($dest == 'FD') {
                    if (ob_get_contents()) {
                        $this->Error('Some data has already been output, can\'t send PDF file');
                    }
                    header('Content-Description: File Transfer');
                    if (headers_sent()) {
                        $this->Error('Some data has already been output to browser, can\'t send PDF file');
                    }
                    header('Cache-Control: public, must-revalidate, max-age=0');
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    if (strpos(php_sapi_name(), 'cgi') === false) {
                        header('Content-Type: application/force-download');
                        header('Content-Type: application/octet-stream', false);
                        header('Content-Type: application/download', false);
                        header('Content-Type: application/pdf', false);
                    } else {
                        header('Content-Type: application/pdf');
                    }
                    header('Content-Disposition: attachment; filename="' . basename($name) . '";');
                    header('Content-Transfer-Encoding: binary');
                    $this->sendOutputData(file_get_contents($name), filesize($name));
                }
                break;
            }
            case 'E': {
                $retval = 'Content-Type: application/pdf;' . "\r\n";
                $retval .= ' name="' . $name . '"' . "\r\n";
                $retval .= 'Content-Transfer-Encoding: base64' . "\r\n";
                $retval .= 'Content-Disposition: attachment;' . "\r\n";
                $retval .= ' filename="' . $name . '"' . "\r\n\r\n";
                $retval .= chunk_split(base64_encode($this->getBuffer()), 76, "\r\n");
                return $retval;
            }
            case 'S': {
                return $this->getBuffer();
            }
            default: {
                $this->Error('Incorrect output destination: ' . $dest);
            }
        }
        return '';
    }
    public function _destroy($destroyall = false, $preserve_objcopy = false)
    {
        if ($destroyall AND isset($this->diskcache) AND $this->diskcache AND (!$preserve_objcopy) AND (!$this->empty_string($this->buffer))) {
            unlink($this->buffer);
        }
        foreach (array_keys(get_object_vars($this)) as $val) {
            if ($destroyall OR (($val != 'internal_encoding') AND ($val != 'state') AND ($val != 'bufferlen') AND ($val != 'buffer') AND ($val != 'diskcache') AND ($val != 'sign') AND ($val != 'signature_data') AND ($val != 'signature_max_length') AND ($val != 'byterange_string'))) {
                if ((!$preserve_objcopy OR ($val != 'objcopy')) AND isset($this->$val)) {
                    unset($this->$val);
                }
            }
        }
    }
    protected function _dochecks()
    {
        if (1.1 == 1) {
            $this->Error('Don\'t alter the locale before including class file');
        }
        if (sprintf('%.1F', 1.0) != '1.0') {
            setlocale(LC_NUMERIC, 'C');
        }
    }
    protected function _getfontpath()
    {
        if (!defined('K_PATH_FONTS') AND is_dir(dirname(__FILE__) . '/fonts')) {
            define('K_PATH_FONTS', dirname(__FILE__) . '/fonts/');
        }
        return defined('K_PATH_FONTS') ? K_PATH_FONTS : '';
    }
    protected function getInternalPageNumberAliases($a = '')
    {
        $alias        = array();
        $alias        = array(
            'u' => array(),
            'a' => array()
        );
        $u            = '{' . $a . '}';
        $alias['u'][] = $this->_escape($u);
        if ($this->isunicode) {
            $alias['u'][] = $this->_escape($this->UTF8ToLatin1($u));
            $alias['u'][] = $this->_escape($this->utf8StrRev($u, false, $this->tmprtl));
            $alias['a'][] = $this->_escape($this->UTF8ToLatin1($a));
            $alias['a'][] = $this->_escape($this->utf8StrRev($a, false, $this->tmprtl));
        }
        $alias['a'][] = $this->_escape($a);
        return $alias;
    }
    protected function getAllInternalPageNumberAliases()
    {
        $basic_alias = array(
            $this->alias_tot_pages,
            $this->alias_num_page,
            $this->alias_group_tot_pages,
            $this->alias_group_num_page,
            $this->alias_right_shift
        );
        $pnalias     = array();
        foreach ($basic_alias as $k => $a) {
            $pnalias[$k] = $this->getInternalPageNumberAliases($a);
        }
        return $pnalias;
    }
    protected function replacePageNumAliases($page, $replace, &$diff = 0)
    {
        foreach ($replace as $rep) {
            foreach ($rep[3] as $a) {
                $count = 0;
                $page  = str_replace($a, $rep[0], $page, $count);
                if ($count > 0) {
                    $diff += ($rep[2] - $rep[1]);
                }
            }
        }
        return $page;
    }
    protected function replaceRightShiftPageNumAliases($page, $aliases, $diff)
    {
        foreach ($aliases as $type => $alias) {
            foreach ($alias as $a) {
                $startnum = (strpos($a, ':') + 1);
                $a        = substr($a, 0, $startnum);
                if (($pos = strpos($page, $a)) !== false) {
                    $endnum = strpos($page, '}', $pos);
                    $aa     = substr($page, $pos, ($endnum - $pos + 1));
                    $ratio  = substr($page, ($pos + $startnum), ($endnum - $pos - $startnum));
                    $ratio  = preg_replace('/[^0-9\.]/', '', $ratio);
                    $ratio  = floatval($ratio);
                    if ($type == 'u') {
                        $chrdiff = floor(($diff + 12) * $ratio);
                        $shift   = str_repeat(' ', $chrdiff);
                        $shift   = $this->UTF8ToUTF16BE($shift, false);
                    } else {
                        $chrdiff = floor(($diff + 11) * $ratio);
                        $shift   = str_repeat(' ', $chrdiff);
                    }
                    $page = str_replace($aa, $shift, $page);
                }
            }
        }
        return $page;
    }
    protected function _putpages()
    {
        $filter        = ($this->compress) ? '/Filter /FlateDecode ' : '';
        $pnalias       = $this->getAllInternalPageNumberAliases();
        $num_pages     = $this->numpages;
        $ptpa          = $this->formatPageNumber(($this->starting_page_number + $num_pages - 1));
        $ptpu          = $this->UTF8ToUTF16BE($ptpa, false);
        $ptp_num_chars = $this->GetNumChars($ptpa);
        $pagegroupnum  = 0;
        $groupnum      = 0;
        $ptgu          = 1;
        $ptga          = 1;
        for ($n = 1; $n <= $num_pages; ++$n) {
            $temppage      = $this->getPageBuffer($n);
            $pagelen       = strlen($temppage);
            $pnpa          = $this->formatPageNumber(($this->starting_page_number + $n - 1));
            $pnpu          = $this->UTF8ToUTF16BE($pnpa, false);
            $pnp_num_chars = $this->GetNumChars($pnpa);
            $pdiff         = 0;
            $gdiff         = 0;
            if (!empty($this->pagegroups)) {
                if (isset($this->newpagegroup[$n])) {
                    $pagegroupnum = 0;
                    ++$groupnum;
                    $ptga          = $this->formatPageNumber($this->pagegroups[$groupnum]);
                    $ptgu          = $this->UTF8ToUTF16BE($ptga, false);
                    $ptg_num_chars = $this->GetNumChars($ptga);
                }
                ++$pagegroupnum;
                $pnga          = $this->formatPageNumber($pagegroupnum);
                $pngu          = $this->UTF8ToUTF16BE($pnga, false);
                $png_num_chars = $this->GetNumChars($pnga);
                $replace       = array();
                $replace[]     = array(
                    $ptgu,
                    $ptg_num_chars,
                    9,
                    $pnalias[2]['u']
                );
                $replace[]     = array(
                    $ptga,
                    $ptg_num_chars,
                    7,
                    $pnalias[2]['a']
                );
                $replace[]     = array(
                    $pngu,
                    $png_num_chars,
                    9,
                    $pnalias[3]['u']
                );
                $replace[]     = array(
                    $pnga,
                    $png_num_chars,
                    7,
                    $pnalias[3]['a']
                );
                $temppage      = $this->replacePageNumAliases($temppage, $replace, $gdiff);
            }
            $replace               = array();
            $replace[]             = array(
                $ptpu,
                $ptp_num_chars,
                9,
                $pnalias[0]['u']
            );
            $replace[]             = array(
                $ptpa,
                $ptp_num_chars,
                7,
                $pnalias[0]['a']
            );
            $replace[]             = array(
                $pnpu,
                $pnp_num_chars,
                9,
                $pnalias[1]['u']
            );
            $replace[]             = array(
                $pnpa,
                $pnp_num_chars,
                7,
                $pnalias[1]['a']
            );
            $temppage              = $this->replacePageNumAliases($temppage, $replace, $pdiff);
            $temppage              = $this->replaceRightShiftPageNumAliases($temppage, $pnalias[4], max($pdiff, $gdiff));
            $temppage              = str_replace($this->epsmarker, '', $temppage);
            $this->page_obj_id[$n] = $this->_newobj();
            $out                   = '<<';
            $out .= ' /Type /Page';
            $out .= ' /Parent 1 0 R';
            $out .= ' /LastModified ' . $this->_datestring();
            $out .= ' /Resources 2 0 R';
            $boxes = array(
                'MediaBox',
                'CropBox',
                'BleedBox',
                'TrimBox',
                'ArtBox'
            );
            foreach ($boxes as $box) {
                $out .= ' /' . $box;
                $out .= sprintf(' [%.2F %.2F %.2F %.2F]', $this->pagedim[$n][$box]['llx'], $this->pagedim[$n][$box]['lly'], $this->pagedim[$n][$box]['urx'], $this->pagedim[$n][$box]['ury']);
            }
            if (isset($this->pagedim[$n]['BoxColorInfo']) AND !empty($this->pagedim[$n]['BoxColorInfo'])) {
                $out .= ' /BoxColorInfo <<';
                foreach ($boxes as $box) {
                    if (isset($this->pagedim[$n]['BoxColorInfo'][$box])) {
                        $out .= ' /' . $box . ' <<';
                        if (isset($this->pagedim[$n]['BoxColorInfo'][$box]['C'])) {
                            $color = $this->pagedim[$n]['BoxColorInfo'][$box]['C'];
                            $out .= ' /C [';
                            $out .= sprintf(' %.3F %.3F %.3F', $color[0] / 255, $color[1] / 255, $color[2] / 255);
                            $out .= ' ]';
                        }
                        if (isset($this->pagedim[$n]['BoxColorInfo'][$box]['W'])) {
                            $out .= ' /W ' . ($this->pagedim[$n]['BoxColorInfo'][$box]['W'] * $this->k);
                        }
                        if (isset($this->pagedim[$n]['BoxColorInfo'][$box]['S'])) {
                            $out .= ' /S /' . $this->pagedim[$n]['BoxColorInfo'][$box]['S'];
                        }
                        if (isset($this->pagedim[$n]['BoxColorInfo'][$box]['D'])) {
                            $dashes = $this->pagedim[$n]['BoxColorInfo'][$box]['D'];
                            $out .= ' /D [';
                            foreach ($dashes as $dash) {
                                $out .= sprintf(' %.3F', ($dash * $this->k));
                            }
                            $out .= ' ]';
                        }
                        $out .= ' >>';
                    }
                }
                $out .= ' >>';
            }
            $out .= ' /Contents ' . ($this->n + 1) . ' 0 R';
            $out .= ' /Rotate ' . $this->pagedim[$n]['Rotate'];
            $out .= ' /Group << /Type /Group /S /Transparency /CS /DeviceRGB >>';
            if (isset($this->pagedim[$n]['trans']) AND !empty($this->pagedim[$n]['trans'])) {
                if (isset($this->pagedim[$n]['trans']['Dur'])) {
                    $out .= ' /Dur ' . $this->pagedim[$n]['trans']['Dur'];
                }
                $out .= ' /Trans <<';
                $out .= ' /Type /Trans';
                if (isset($this->pagedim[$n]['trans']['S'])) {
                    $out .= ' /S /' . $this->pagedim[$n]['trans']['S'];
                }
                if (isset($this->pagedim[$n]['trans']['D'])) {
                    $out .= ' /D ' . $this->pagedim[$n]['trans']['D'];
                }
                if (isset($this->pagedim[$n]['trans']['Dm'])) {
                    $out .= ' /Dm /' . $this->pagedim[$n]['trans']['Dm'];
                }
                if (isset($this->pagedim[$n]['trans']['M'])) {
                    $out .= ' /M /' . $this->pagedim[$n]['trans']['M'];
                }
                if (isset($this->pagedim[$n]['trans']['Di'])) {
                    $out .= ' /Di ' . $this->pagedim[$n]['trans']['Di'];
                }
                if (isset($this->pagedim[$n]['trans']['SS'])) {
                    $out .= ' /SS ' . $this->pagedim[$n]['trans']['SS'];
                }
                if (isset($this->pagedim[$n]['trans']['B'])) {
                    $out .= ' /B ' . $this->pagedim[$n]['trans']['B'];
                }
                $out .= ' >>';
            }
            $out .= $this->_getannotsrefs($n);
            $out .= ' /PZ ' . $this->pagedim[$n]['PZ'];
            $out .= ' >>';
            $out .= "\n" . 'endobj';
            $this->_out($out);
            $p = ($this->compress) ? gzcompress($temppage) : $temppage;
            $this->_newobj();
            $p = $this->_getrawstream($p);
            $this->_out('<<' . $filter . '/Length ' . strlen($p) . '>> stream' . "\n" . $p . "\n" . 'endstream' . "\n" . 'endobj');
            if ($this->diskcache) {
                unlink($this->pages[$n]);
            }
        }
        $out = $this->_getobj(1) . "\n";
        $out .= '<< /Type /Pages /Kids [';
        foreach ($this->page_obj_id as $page_obj) {
            $out .= ' ' . $page_obj . ' 0 R';
        }
        $out .= ' ] /Count ' . $num_pages . ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
    }
    protected function _putannotsrefs($n)
    {
        $this->_out($this->_getannotsrefs($n));
    }
    protected function _getannotsrefs($n)
    {
        if (!(isset($this->PageAnnots[$n]) OR ($this->sign AND isset($this->signature_data['cert_type'])))) {
            return '';
        }
        $out = ' /Annots [';
        if (isset($this->PageAnnots[$n])) {
            foreach ($this->PageAnnots[$n] as $key => $val) {
                if (!in_array($val['n'], $this->radio_groups)) {
                    $out .= ' ' . $val['n'] . ' 0 R';
                }
            }
            if (isset($this->radiobutton_groups[$n])) {
                foreach ($this->radiobutton_groups[$n] as $key => $data) {
                    if (isset($data['n'])) {
                        $out .= ' ' . $data['n'] . ' 0 R';
                    }
                }
            }
        }
        if ($this->sign AND ($n == $this->signature_appearance['page']) AND isset($this->signature_data['cert_type'])) {
            $out .= ' ' . $this->sig_obj_id . ' 0 R';
        }
        $out .= ' ]';
        return $out;
    }
    protected function _putannotsobjs()
    {
        for ($n = 1; $n <= $this->numpages; ++$n) {
            if (isset($this->PageAnnots[$n])) {
                foreach ($this->PageAnnots[$n] as $key => $pl) {
                    $annot_obj_id = $this->PageAnnots[$n][$key]['n'];
                    if (isset($this->radiobutton_groups[$n][$pl['txt']]) AND is_array($this->radiobutton_groups[$n][$pl['txt']])) {
                        $radio_button_obj_id = $this->radiobutton_groups[$n][$pl['txt']]['n'];
                        $annots              = '<<';
                        $annots .= ' /Type /Annot';
                        $annots .= ' /Subtype /Widget';
                        $annots .= ' /Rect [0 0 0 0]';
                        $annots .= ' /T ' . $this->_datastring($pl['txt'], $radio_button_obj_id);
                        $annots .= ' /FT /Btn';
                        $annots .= ' /Ff 49152';
                        $annots .= ' /Kids [';
                        foreach ($this->radiobutton_groups[$n][$pl['txt']] as $key => $data) {
                            if ($key !== 'n') {
                                $annots .= ' ' . $data['kid'] . ' 0 R';
                                if ($data['def'] !== 'Off') {
                                    $defval = $data['def'];
                                }
                            }
                        }
                        $annots .= ' ]';
                        if (isset($defval)) {
                            $annots .= ' /V /' . $defval;
                        }
                        $annots .= ' >>';
                        $this->_out($this->_getobj($radio_button_obj_id) . "\n" . $annots . "\n" . 'endobj');
                        $this->form_obj_id[]                      = $radio_button_obj_id;
                        $this->radiobutton_groups[$n][$pl['txt']] = $radio_button_obj_id;
                    }
                    $formfield = false;
                    $pl['opt'] = array_change_key_case($pl['opt'], CASE_LOWER);
                    $a         = $pl['x'] * $this->k;
                    $b         = $this->pagedim[$n]['h'] - (($pl['y'] + $pl['h']) * $this->k);
                    $c         = $pl['w'] * $this->k;
                    $d         = $pl['h'] * $this->k;
                    $rect      = sprintf('%.2F %.2F %.2F %.2F', $a, $b, $a + $c, $b + $d);
                    $annots    = '<</Type /Annot';
                    $annots .= ' /Subtype /' . $pl['opt']['subtype'];
                    $annots .= ' /Rect [' . $rect . ']';
                    $ft = array(
                        'Btn',
                        'Tx',
                        'Ch',
                        'Sig'
                    );
                    if (isset($pl['opt']['ft']) AND in_array($pl['opt']['ft'], $ft)) {
                        $annots .= ' /FT /' . $pl['opt']['ft'];
                        $formfield = true;
                    }
                    $annots .= ' /Contents ' . $this->_textstring($pl['txt'], $annot_obj_id);
                    $annots .= ' /P ' . $this->page_obj_id[$n] . ' 0 R';
                    $annots .= ' /NM ' . $this->_datastring(sprintf('%04u-%04u', $n, $key), $annot_obj_id);
                    $annots .= ' /M ' . $this->_datestring($annot_obj_id);
                    if (isset($pl['opt']['f'])) {
                        $val = 0;
                        if (is_array($pl['opt']['f'])) {
                            foreach ($pl['opt']['f'] as $f) {
                                switch (strtolower($f)) {
                                    case 'invisible': {
                                        $val += 1 << 0;
                                        break;
                                    }
                                    case 'hidden': {
                                        $val += 1 << 1;
                                        break;
                                    }
                                    case 'print': {
                                        $val += 1 << 2;
                                        break;
                                    }
                                    case 'nozoom': {
                                        $val += 1 << 3;
                                        break;
                                    }
                                    case 'norotate': {
                                        $val += 1 << 4;
                                        break;
                                    }
                                    case 'noview': {
                                        $val += 1 << 5;
                                        break;
                                    }
                                    case 'readonly': {
                                        $val += 1 << 6;
                                        break;
                                    }
                                    case 'locked': {
                                        $val += 1 << 8;
                                        break;
                                    }
                                    case 'togglenoview': {
                                        $val += 1 << 9;
                                        break;
                                    }
                                    case 'lockedcontents': {
                                        $val += 1 << 10;
                                        break;
                                    }
                                    default: {
                                        break;
                                    }
                                }
                            }
                        } else {
                            $val = intval($pl['opt']['f']);
                        }
                        $annots .= ' /F ' . intval($val);
                    }
                    if (isset($pl['opt']['as']) AND is_string($pl['opt']['as'])) {
                        $annots .= ' /AS /' . $pl['opt']['as'];
                    }
                    if (isset($pl['opt']['ap'])) {
                        $annots .= ' /AP <<';
                        if (is_array($pl['opt']['ap'])) {
                            foreach ($pl['opt']['ap'] as $apmode => $apdef) {
                                $annots .= ' /' . strtoupper($apmode);
                                if (is_array($apdef)) {
                                    $annots .= ' <<';
                                    foreach ($apdef as $apstate => $stream) {
                                        $apsobjid = $this->_putAPXObject($c, $d, $stream);
                                        $annots .= ' /' . $apstate . ' ' . $apsobjid . ' 0 R';
                                    }
                                    $annots .= ' >>';
                                } else {
                                    $apsobjid = $this->_putAPXObject($c, $d, $apdef);
                                    $annots .= ' ' . $apsobjid . ' 0 R';
                                }
                            }
                        } else {
                            $annots .= $pl['opt']['ap'];
                        }
                        $annots .= ' >>';
                    }
                    if (isset($pl['opt']['bs']) AND (is_array($pl['opt']['bs']))) {
                        $annots .= ' /BS <<';
                        $annots .= ' /Type /Border';
                        if (isset($pl['opt']['bs']['w'])) {
                            $annots .= ' /W ' . intval($pl['opt']['bs']['w']);
                        }
                        $bstyles = array(
                            'S',
                            'D',
                            'B',
                            'I',
                            'U'
                        );
                        if (isset($pl['opt']['bs']['s']) AND in_array($pl['opt']['bs']['s'], $bstyles)) {
                            $annots .= ' /S /' . $pl['opt']['bs']['s'];
                        }
                        if (isset($pl['opt']['bs']['d']) AND (is_array($pl['opt']['bs']['d']))) {
                            $annots .= ' /D [';
                            foreach ($pl['opt']['bs']['d'] as $cord) {
                                $annots .= ' ' . intval($cord);
                            }
                            $annots .= ']';
                        }
                        $annots .= ' >>';
                    } else {
                        $annots .= ' /Border [';
                        if (isset($pl['opt']['border']) AND (count($pl['opt']['border']) >= 3)) {
                            $annots .= intval($pl['opt']['border'][0]) . ' ';
                            $annots .= intval($pl['opt']['border'][1]) . ' ';
                            $annots .= intval($pl['opt']['border'][2]);
                            if (isset($pl['opt']['border'][3]) AND is_array($pl['opt']['border'][3])) {
                                $annots .= ' [';
                                foreach ($pl['opt']['border'][3] as $dash) {
                                    $annots .= intval($dash) . ' ';
                                }
                                $annots .= ']';
                            }
                        } else {
                            $annots .= '0 0 0';
                        }
                        $annots .= ']';
                    }
                    if (isset($pl['opt']['be']) AND (is_array($pl['opt']['be']))) {
                        $annots .= ' /BE <<';
                        $bstyles = array(
                            'S',
                            'C'
                        );
                        if (isset($pl['opt']['be']['s']) AND in_array($pl['opt']['be']['s'], $markups)) {
                            $annots .= ' /S /' . $pl['opt']['bs']['s'];
                        } else {
                            $annots .= ' /S /S';
                        }
                        if (isset($pl['opt']['be']['i']) AND ($pl['opt']['be']['i'] >= 0) AND ($pl['opt']['be']['i'] <= 2)) {
                            $annots .= ' /I ' . sprintf(' %.4F', $pl['opt']['be']['i']);
                        }
                        $annots .= '>>';
                    }
                    if (isset($pl['opt']['c']) AND (is_array($pl['opt']['c'])) AND !empty($pl['opt']['c'])) {
                        $annots .= ' /C [';
                        foreach ($pl['opt']['c'] as $col) {
                            $col   = intval($col);
                            $color = $col <= 0 ? 0 : ($col >= 255 ? 1 : $col / 255);
                            $annots .= sprintf(' %.4F', $color);
                        }
                        $annots .= ']';
                    }
                    $markups = array(
                        'text',
                        'freetext',
                        'line',
                        'square',
                        'circle',
                        'polygon',
                        'polyline',
                        'highlight',
                        'underline',
                        'squiggly',
                        'strikeout',
                        'stamp',
                        'caret',
                        'ink',
                        'fileattachment',
                        'sound'
                    );
                    if (in_array(strtolower($pl['opt']['subtype']), $markups)) {
                        if (isset($pl['opt']['t']) AND is_string($pl['opt']['t'])) {
                            $annots .= ' /T ' . $this->_textstring($pl['opt']['t'], $annot_obj_id);
                        }
                        if (isset($pl['opt']['ca'])) {
                            $annots .= ' /CA ' . sprintf('%.4F', floatval($pl['opt']['ca']));
                        }
                        if (isset($pl['opt']['rc'])) {
                            $annots .= ' /RC ' . $this->_textstring($pl['opt']['rc'], $annot_obj_id);
                        }
                        $annots .= ' /CreationDate ' . $this->_datestring($annot_obj_id);
                        if (isset($pl['opt']['subj'])) {
                            $annots .= ' /Subj ' . $this->_textstring($pl['opt']['subj'], $annot_obj_id);
                        }
                    }
                    $lineendings = array(
                        'Square',
                        'Circle',
                        'Diamond',
                        'OpenArrow',
                        'ClosedArrow',
                        'None',
                        'Butt',
                        'ROpenArrow',
                        'RClosedArrow',
                        'Slash'
                    );
                    switch (strtolower($pl['opt']['subtype'])) {
                        case 'text': {
                            if (isset($pl['opt']['open'])) {
                                $annots .= ' /Open ' . (strtolower($pl['opt']['open']) == 'true' ? 'true' : 'false');
                            }
                            $iconsapp = array(
                                'Comment',
                                'Help',
                                'Insert',
                                'Key',
                                'NewParagraph',
                                'Note',
                                'Paragraph'
                            );
                            if (isset($pl['opt']['name']) AND in_array($pl['opt']['name'], $iconsapp)) {
                                $annots .= ' /Name /' . $pl['opt']['name'];
                            } else {
                                $annots .= ' /Name /Note';
                            }
                            $statemodels = array(
                                'Marked',
                                'Review'
                            );
                            if (isset($pl['opt']['statemodel']) AND in_array($pl['opt']['statemodel'], $statemodels)) {
                                $annots .= ' /StateModel /' . $pl['opt']['statemodel'];
                            } else {
                                $pl['opt']['statemodel'] = 'Marked';
                                $annots .= ' /StateModel /' . $pl['opt']['statemodel'];
                            }
                            if ($pl['opt']['statemodel'] == 'Marked') {
                                $states = array(
                                    'Accepted',
                                    'Unmarked'
                                );
                            } else {
                                $states = array(
                                    'Accepted',
                                    'Rejected',
                                    'Cancelled',
                                    'Completed',
                                    'None'
                                );
                            }
                            if (isset($pl['opt']['state']) AND in_array($pl['opt']['state'], $states)) {
                                $annots .= ' /State /' . $pl['opt']['state'];
                            } else {
                                if ($pl['opt']['statemodel'] == 'Marked') {
                                    $annots .= ' /State /Unmarked';
                                } else {
                                    $annots .= ' /State /None';
                                }
                            }
                            break;
                        }
                        case 'link': {
                            if (is_string($pl['txt'])) {
                                $annots .= ' /A <</S /URI /URI ' . $this->_datastring($this->unhtmlentities($pl['txt']), $annot_obj_id) . '>>';
                            } else {
                                $l = $this->links[$pl['txt']];
                                $annots .= sprintf(' /Dest [%u 0 R /XYZ 0 %.2F null]', $this->page_obj_id[($l[0])], ($this->pagedim[$l[0]]['h'] - ($l[1] * $this->k)));
                            }
                            $hmodes = array(
                                'N',
                                'I',
                                'O',
                                'P'
                            );
                            if (isset($pl['opt']['h']) AND in_array($pl['opt']['h'], $hmodes)) {
                                $annots .= ' /H /' . $pl['opt']['h'];
                            } else {
                                $annots .= ' /H /I';
                            }
                            break;
                        }
                        case 'freetext': {
                            if (isset($pl['opt']['da']) AND !empty($pl['opt']['da'])) {
                                $annots .= ' /DA (' . $pl['opt']['da'] . ')';
                            }
                            if (isset($pl['opt']['q']) AND ($pl['opt']['q'] >= 0) AND ($pl['opt']['q'] <= 2)) {
                                $annots .= ' /Q ' . intval($pl['opt']['q']);
                            }
                            if (isset($pl['opt']['rc'])) {
                                $annots .= ' /RC ' . $this->_textstring($pl['opt']['rc'], $annot_obj_id);
                            }
                            if (isset($pl['opt']['ds'])) {
                                $annots .= ' /DS ' . $this->_textstring($pl['opt']['ds'], $annot_obj_id);
                            }
                            if (isset($pl['opt']['cl']) AND is_array($pl['opt']['cl'])) {
                                $annots .= ' /CL [';
                                foreach ($pl['opt']['cl'] as $cl) {
                                    $annots .= sprintf('%.4F ', $cl * $this->k);
                                }
                                $annots .= ']';
                            }
                            $tfit = array(
                                'FreeText',
                                'FreeTextCallout',
                                'FreeTextTypeWriter'
                            );
                            if (isset($pl['opt']['it']) AND in_array($pl['opt']['it'], $tfit)) {
                                $annots .= ' /IT /' . $pl['opt']['it'];
                            }
                            if (isset($pl['opt']['rd']) AND is_array($pl['opt']['rd'])) {
                                $l = $pl['opt']['rd'][0] * $this->k;
                                $r = $pl['opt']['rd'][1] * $this->k;
                                $t = $pl['opt']['rd'][2] * $this->k;
                                $b = $pl['opt']['rd'][3] * $this->k;
                                $annots .= ' /RD [' . sprintf('%.2F %.2F %.2F %.2F', $l, $r, $t, $b) . ']';
                            }
                            if (isset($pl['opt']['le']) AND in_array($pl['opt']['le'], $lineendings)) {
                                $annots .= ' /LE /' . $pl['opt']['le'];
                            }
                            break;
                        }
                        case 'line': {
                            break;
                        }
                        case 'square': {
                            break;
                        }
                        case 'circle': {
                            break;
                        }
                        case 'polygon': {
                            break;
                        }
                        case 'polyline': {
                            break;
                        }
                        case 'highlight': {
                            break;
                        }
                        case 'underline': {
                            break;
                        }
                        case 'squiggly': {
                            break;
                        }
                        case 'strikeout': {
                            break;
                        }
                        case 'stamp': {
                            break;
                        }
                        case 'caret': {
                            break;
                        }
                        case 'ink': {
                            break;
                        }
                        case 'popup': {
                            break;
                        }
                        case 'fileattachment': {
                            if (!isset($pl['opt']['fs'])) {
                                break;
                            }
                            $filename = basename($pl['opt']['fs']);
                            if (isset($this->embeddedfiles[$filename]['n'])) {
                                $annots .= ' /FS <</Type /Filespec /F ' . $this->_datastring($filename, $annot_obj_id) . ' /EF <</F ' . $this->embeddedfiles[$filename]['n'] . ' 0 R>> >>';
                                $iconsapp = array(
                                    'Graph',
                                    'Paperclip',
                                    'PushPin',
                                    'Tag'
                                );
                                if (isset($pl['opt']['name']) AND in_array($pl['opt']['name'], $iconsapp)) {
                                    $annots .= ' /Name /' . $pl['opt']['name'];
                                } else {
                                    $annots .= ' /Name /PushPin';
                                }
                            }
                            break;
                        }
                        case 'sound': {
                            if (!isset($pl['opt']['fs'])) {
                                break;
                            }
                            $filename = basename($pl['opt']['fs']);
                            if (isset($this->embeddedfiles[$filename]['n'])) {
                                $annots .= ' /Sound <</Type /Filespec /F ' . $this->_datastring($filename, $annot_obj_id) . ' /EF <</F ' . $this->embeddedfiles[$filename]['n'] . ' 0 R>> >>';
                                $iconsapp = array(
                                    'Speaker',
                                    'Mic'
                                );
                                if (isset($pl['opt']['name']) AND in_array($pl['opt']['name'], $iconsapp)) {
                                    $annots .= ' /Name /' . $pl['opt']['name'];
                                } else {
                                    $annots .= ' /Name /Speaker';
                                }
                            }
                            break;
                        }
                        case 'movie': {
                            break;
                        }
                        case 'widget': {
                            $hmode = array(
                                'N',
                                'I',
                                'O',
                                'P',
                                'T'
                            );
                            if (isset($pl['opt']['h']) AND in_array($pl['opt']['h'], $hmode)) {
                                $annots .= ' /H /' . $pl['opt']['h'];
                            }
                            if (isset($pl['opt']['mk']) AND (is_array($pl['opt']['mk'])) AND !empty($pl['opt']['mk'])) {
                                $annots .= ' /MK <<';
                                if (isset($pl['opt']['mk']['r'])) {
                                    $annots .= ' /R ' . $pl['opt']['mk']['r'];
                                }
                                if (isset($pl['opt']['mk']['bc']) AND (is_array($pl['opt']['mk']['bc']))) {
                                    $annots .= ' /BC [';
                                    foreach ($pl['opt']['mk']['bc'] AS $col) {
                                        $col   = intval($col);
                                        $color = $col <= 0 ? 0 : ($col >= 255 ? 1 : $col / 255);
                                        $annots .= sprintf(' %.2F', $color);
                                    }
                                    $annots .= ']';
                                }
                                if (isset($pl['opt']['mk']['bg']) AND (is_array($pl['opt']['mk']['bg']))) {
                                    $annots .= ' /BG [';
                                    foreach ($pl['opt']['mk']['bg'] AS $col) {
                                        $col   = intval($col);
                                        $color = $col <= 0 ? 0 : ($col >= 255 ? 1 : $col / 255);
                                        $annots .= sprintf(' %.2F', $color);
                                    }
                                    $annots .= ']';
                                }
                                if (isset($pl['opt']['mk']['ca'])) {
                                    $annots .= ' /CA ' . $pl['opt']['mk']['ca'];
                                }
                                if (isset($pl['opt']['mk']['rc'])) {
                                    $annots .= ' /RC ' . $pl['opt']['mk']['rc'];
                                }
                                if (isset($pl['opt']['mk']['ac'])) {
                                    $annots .= ' /AC ' . $pl['opt']['mk']['ac'];
                                }
                                if (isset($pl['opt']['mk']['i'])) {
                                    $info = $this->getImageBuffer($pl['opt']['mk']['i']);
                                    if ($info !== false) {
                                        $annots .= ' /I ' . $info['n'] . ' 0 R';
                                    }
                                }
                                if (isset($pl['opt']['mk']['ri'])) {
                                    $info = $this->getImageBuffer($pl['opt']['mk']['ri']);
                                    if ($info !== false) {
                                        $annots .= ' /RI ' . $info['n'] . ' 0 R';
                                    }
                                }
                                if (isset($pl['opt']['mk']['ix'])) {
                                    $info = $this->getImageBuffer($pl['opt']['mk']['ix']);
                                    if ($info !== false) {
                                        $annots .= ' /IX ' . $info['n'] . ' 0 R';
                                    }
                                }
                                if (isset($pl['opt']['mk']['if']) AND (is_array($pl['opt']['mk']['if'])) AND !empty($pl['opt']['mk']['if'])) {
                                    $annots .= ' /IF <<';
                                    $if_sw = array(
                                        'A',
                                        'B',
                                        'S',
                                        'N'
                                    );
                                    if (isset($pl['opt']['mk']['if']['sw']) AND in_array($pl['opt']['mk']['if']['sw'], $if_sw)) {
                                        $annots .= ' /SW /' . $pl['opt']['mk']['if']['sw'];
                                    }
                                    $if_s = array(
                                        'A',
                                        'P'
                                    );
                                    if (isset($pl['opt']['mk']['if']['s']) AND in_array($pl['opt']['mk']['if']['s'], $if_s)) {
                                        $annots .= ' /S /' . $pl['opt']['mk']['if']['s'];
                                    }
                                    if (isset($pl['opt']['mk']['if']['a']) AND (is_array($pl['opt']['mk']['if']['a'])) AND !empty($pl['opt']['mk']['if']['a'])) {
                                        $annots .= sprintf(' /A [%.2F %.2F]', $pl['opt']['mk']['if']['a'][0], $pl['opt']['mk']['if']['a'][1]);
                                    }
                                    if (isset($pl['opt']['mk']['if']['fb']) AND ($pl['opt']['mk']['if']['fb'])) {
                                        $annots .= ' /FB true';
                                    }
                                    $annots .= '>>';
                                }
                                if (isset($pl['opt']['mk']['tp']) AND ($pl['opt']['mk']['tp'] >= 0) AND ($pl['opt']['mk']['tp'] <= 6)) {
                                    $annots .= ' /TP ' . intval($pl['opt']['mk']['tp']);
                                } else {
                                    $annots .= ' /TP 0';
                                }
                                $annots .= '>>';
                            }
                            if (isset($this->radiobutton_groups[$n][$pl['txt']])) {
                                $annots .= ' /Parent ' . $this->radiobutton_groups[$n][$pl['txt']] . ' 0 R';
                            }
                            if (isset($pl['opt']['t']) AND is_string($pl['opt']['t'])) {
                                $annots .= ' /T ' . $this->_datastring($pl['opt']['t'], $annot_obj_id);
                            }
                            if (isset($pl['opt']['tu']) AND is_string($pl['opt']['tu'])) {
                                $annots .= ' /TU ' . $this->_datastring($pl['opt']['tu'], $annot_obj_id);
                            }
                            if (isset($pl['opt']['tm']) AND is_string($pl['opt']['tm'])) {
                                $annots .= ' /TM ' . $this->_datastring($pl['opt']['tm'], $annot_obj_id);
                            }
                            if (isset($pl['opt']['ff'])) {
                                if (is_array($pl['opt']['ff'])) {
                                    $flag = 0;
                                    foreach ($pl['opt']['ff'] as $val) {
                                        $flag += 1 << ($val - 1);
                                    }
                                } else {
                                    $flag = intval($pl['opt']['ff']);
                                }
                                $annots .= ' /Ff ' . $flag;
                            }
                            if (isset($pl['opt']['maxlen'])) {
                                $annots .= ' /MaxLen ' . intval($pl['opt']['maxlen']);
                            }
                            if (isset($pl['opt']['v'])) {
                                $annots .= ' /V';
                                if (is_array($pl['opt']['v'])) {
                                    foreach ($pl['opt']['v'] AS $optval) {
                                        if (is_float($optval)) {
                                            $optval = sprintf('%.2F', $optval);
                                        }
                                        $annots .= ' ' . $optval;
                                    }
                                } else {
                                    $annots .= ' ' . $this->_textstring($pl['opt']['v'], $annot_obj_id);
                                }
                            }
                            if (isset($pl['opt']['dv'])) {
                                $annots .= ' /DV';
                                if (is_array($pl['opt']['dv'])) {
                                    foreach ($pl['opt']['dv'] AS $optval) {
                                        if (is_float($optval)) {
                                            $optval = sprintf('%.2F', $optval);
                                        }
                                        $annots .= ' ' . $optval;
                                    }
                                } else {
                                    $annots .= ' ' . $this->_textstring($pl['opt']['dv'], $annot_obj_id);
                                }
                            }
                            if (isset($pl['opt']['rv'])) {
                                $annots .= ' /RV';
                                if (is_array($pl['opt']['rv'])) {
                                    foreach ($pl['opt']['rv'] AS $optval) {
                                        if (is_float($optval)) {
                                            $optval = sprintf('%.2F', $optval);
                                        }
                                        $annots .= ' ' . $optval;
                                    }
                                } else {
                                    $annots .= ' ' . $this->_textstring($pl['opt']['rv'], $annot_obj_id);
                                }
                            }
                            if (isset($pl['opt']['a']) AND !empty($pl['opt']['a'])) {
                                $annots .= ' /A << ' . $pl['opt']['a'] . ' >>';
                            }
                            if (isset($pl['opt']['aa']) AND !empty($pl['opt']['aa'])) {
                                $annots .= ' /AA << ' . $pl['opt']['aa'] . ' >>';
                            }
                            if (isset($pl['opt']['da']) AND !empty($pl['opt']['da'])) {
                                $annots .= ' /DA (' . $pl['opt']['da'] . ')';
                            }
                            if (isset($pl['opt']['q']) AND ($pl['opt']['q'] >= 0) AND ($pl['opt']['q'] <= 2)) {
                                $annots .= ' /Q ' . intval($pl['opt']['q']);
                            }
                            if (isset($pl['opt']['opt']) AND (is_array($pl['opt']['opt'])) AND !empty($pl['opt']['opt'])) {
                                $annots .= ' /Opt [';
                                foreach ($pl['opt']['opt'] AS $copt) {
                                    if (is_array($copt)) {
                                        $annots .= ' [' . $this->_textstring($copt[0], $annot_obj_id) . ' ' . $this->_textstring($copt[1], $annot_obj_id) . ']';
                                    } else {
                                        $annots .= ' ' . $this->_textstring($copt, $annot_obj_id);
                                    }
                                }
                                $annots .= ']';
                            }
                            if (isset($pl['opt']['ti'])) {
                                $annots .= ' /TI ' . intval($pl['opt']['ti']);
                            }
                            if (isset($pl['opt']['i']) AND (is_array($pl['opt']['i'])) AND !empty($pl['opt']['i'])) {
                                $annots .= ' /I [';
                                foreach ($pl['opt']['i'] AS $copt) {
                                    $annots .= intval($copt) . ' ';
                                }
                                $annots .= ']';
                            }
                            break;
                        }
                        case 'screen': {
                            break;
                        }
                        case 'printermark': {
                            break;
                        }
                        case 'trapnet': {
                            break;
                        }
                        case 'watermark': {
                            break;
                        }
                        case '3d': {
                            break;
                        }
                        default: {
                            break;
                        }
                    }
                    $annots .= '>>';
                    $this->_out($this->_getobj($annot_obj_id) . "\n" . $annots . "\n" . 'endobj');
                    if ($formfield AND !isset($this->radiobutton_groups[$n][$pl['txt']])) {
                        $this->form_obj_id[] = $annot_obj_id;
                    }
                }
            }
        }
    }
    protected function _putAPXObject($w = 0, $h = 0, $stream = '')
    {
        $stream                          = trim($stream);
        $out                             = $this->_getobj() . "\n";
        $this->xobjects['AX' . $this->n] = array(
            'n' => $this->n
        );
        $out .= '<<';
        $out .= ' /Type /XObject';
        $out .= ' /Subtype /Form';
        $out .= ' /FormType 1';
        if ($this->compress) {
            $stream = gzcompress($stream);
            $out .= ' /Filter /FlateDecode';
        }
        $rect = sprintf('%.2F %.2F', $w, $h);
        $out .= ' /BBox [0 0 ' . $rect . ']';
        $out .= ' /Matrix [1 0 0 1 0 0]';
        $out .= ' /Resources <<';
        $out .= ' /ProcSet [/PDF /Text]';
        $out .= ' /Font <<';
        foreach ($this->annotation_fonts as $fontkey => $fontid) {
            $out .= ' /F' . $fontid . ' ' . $this->font_obj_ids[$fontkey] . ' 0 R';
        }
        $out .= ' >>';
        $out .= ' >>';
        $stream = $this->_getrawstream($stream);
        $out .= ' /Length ' . strlen($stream);
        $out .= ' >>';
        $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        return $this->n;
    }
    protected function _getULONG($str, $offset)
    {
        $v = unpack('Ni', substr($str, $offset, 4));
        return $v['i'];
    }
    protected function _getUSHORT($str, $offset)
    {
        $v = unpack('ni', substr($str, $offset, 2));
        return $v['i'];
    }
    protected function _getSHORT($str, $offset)
    {
        $v = unpack('si', substr($str, $offset, 2));
        return $v['i'];
    }
    protected function _getBYTE($str, $offset)
    {
        $v = unpack('Ci', substr($str, $offset, 1));
        return $v['i'];
    }
    protected function _getTrueTypeFontSubset($font, $subsetchars)
    {
        ksort($subsetchars);
        $offset = 0;
        if ($this->_getULONG($font, $offset) != 0x10000) {
            return $font;
        }
        $offset += 4;
        $numTables = $this->_getUSHORT($font, $offset);
        $offset += 2;
        $offset += 6;
        $table = array();
        for ($i = 0; $i < $numTables; ++$i) {
            $tag = substr($font, $offset, 4);
            $offset += 4;
            $table[$tag]             = array();
            $table[$tag]['checkSum'] = $this->_getULONG($font, $offset);
            $offset += 4;
            $table[$tag]['offset'] = $this->_getULONG($font, $offset);
            $offset += 4;
            $table[$tag]['length'] = $this->_getULONG($font, $offset);
            $offset += 4;
        }
        $offset = $table['head']['offset'] + 12;
        if ($this->_getULONG($font, $offset) != 0x5F0F3CF5) {
            return $font;
        }
        $offset += 4;
        $offset       = $table['head']['offset'] + 50;
        $short_offset = ($this->_getSHORT($font, $offset) == 0);
        $offset += 2;
        $indexToLoc = array();
        $offset     = $table['loca']['offset'];
        if ($short_offset) {
            $tot_num_glyphs = ($table['loca']['length'] / 2);
            for ($i = 0; $i < $tot_num_glyphs; ++$i) {
                $indexToLoc[$i] = $this->_getUSHORT($font, $offset) * 2;
                $offset += 2;
            }
        } else {
            $tot_num_glyphs = ($table['loca']['length'] / 4);
            for ($i = 0; $i < $tot_num_glyphs; ++$i) {
                $indexToLoc[$i] = $this->_getULONG($font, $offset);
                $offset += 4;
            }
        }
        $subsetglyphs      = array();
        $subsetglyphs[0]   = true;
        $offset            = $table['cmap']['offset'] + 2;
        $numEncodingTables = $this->_getUSHORT($font, $offset);
        $offset += 2;
        $encodingTables = array();
        for ($i = 0; $i < $numEncodingTables; ++$i) {
            $encodingTables[$i]['platformID'] = $this->_getUSHORT($font, $offset);
            $offset += 2;
            $encodingTables[$i]['encodingID'] = $this->_getUSHORT($font, $offset);
            $offset += 2;
            $encodingTables[$i]['offset'] = $this->_getULONG($font, $offset);
            $offset += 4;
        }
        foreach ($encodingTables as $enctable) {
            if (($enctable['platformID'] == 3) AND ($enctable['encodingID'] == 0)) {
                $modesymbol = true;
            } else {
                $modesymbol = false;
            }
            $offset = $table['cmap']['offset'] + $enctable['offset'];
            $format = $this->_getUSHORT($font, $offset);
            $offset += 2;
            switch ($format) {
                case 0: {
                    $offset += 4;
                    for ($c = 0; $c < 256; ++$c) {
                        if (isset($subsetchars[$c])) {
                            $g                = $this->_getBYTE($font, $offset);
                            $subsetglyphs[$g] = true;
                        }
                        ++$offset;
                    }
                    break;
                }
                case 2: {
                    $offset += 4;
                    $numSubHeaders = 0;
                    for ($i = 0; $i < 256; ++$i) {
                        $subHeaderKeys[$i] = ($this->_getUSHORT($font, $offset) / 8);
                        $offset += 2;
                        if ($numSubHeaders < $subHeaderKeys[$i]) {
                            $numSubHeaders = $subHeaderKeys[$i];
                        }
                    }
                    ++$numSubHeaders;
                    $subHeaders = array();
                    for ($k = 0; $k < $numSubHeaders; ++$k) {
                        $subHeaders[$k]['firstCode'] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]['entryCount'] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]['idDelta'] = $this->_getSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]['idRangeOffset'] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]['idRangeOffset'] -= (2 + (($numSubHeaders - $k - 1) * 8));
                        $subHeaders[$k]['idRangeOffset'] /= 2;
                        $numGlyphIndexArray += $subHeaders[$k]['entryCount'];
                    }
                    for ($k = 0; $k < $numGlyphIndexArray; ++$k) {
                        $glyphIndexArray[$k] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    for ($i = 0; $i < 256; ++$i) {
                        $k = $subHeaderKeys[$i];
                        if ($k == 0) {
                            $c = $i;
                            if (isset($subsetchars[$c])) {
                                $g                = $glyphIndexArray[0];
                                $subsetglyphs[$g] = true;
                            }
                        } else {
                            $start_byte = $subHeaders[$k]['firstCode'];
                            $end_byte   = $start_byte + $subHeaders[$k]['entryCount'];
                            for ($j = $start_byte; $j < $end_byte; ++$j) {
                                $c = (($i << 8) + $j);
                                if (isset($subsetchars[$c])) {
                                    $idRangeOffset = ($subHeaders[$k]['idRangeOffset'] + $j - $subHeaders[$k]['firstCode']);
                                    $g             = $glyphIndexArray[$idRangeOffset];
                                    $g += ($idDelta[$k] - 65536);
                                    if ($g < 0) {
                                        $g = 0;
                                    }
                                    $subsetglyphs[$g] = true;
                                }
                            }
                        }
                    }
                    break;
                }
                case 4: {
                    $length = $this->_getUSHORT($font, $offset);
                    $offset += 2;
                    $offset += 2;
                    $segCount = ($this->_getUSHORT($font, $offset) / 2);
                    $offset += 2;
                    $offset += 6;
                    $endCount = array();
                    for ($k = 0; $k < $segCount; ++$k) {
                        $endCount[$k] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $offset += 2;
                    $startCount = array();
                    for ($k = 0; $k < $segCount; ++$k) {
                        $startCount[$k] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $idDelta = array();
                    for ($k = 0; $k < $segCount; ++$k) {
                        $idDelta[$k] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $idRangeOffset = array();
                    for ($k = 0; $k < $segCount; ++$k) {
                        $idRangeOffset[$k] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $gidlen       = ($length / 2) - 8 - (4 * $segCount);
                    $glyphIdArray = array();
                    for ($k = 0; $k < $gidlen; ++$k) {
                        $glyphIdArray[$k] = $this->_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    for ($k = 0; $k < $segCount; ++$k) {
                        for ($c = $startCount[$k]; $c <= $endCount[$k]; ++$c) {
                            if (isset($subsetchars[$c])) {
                                if ($idRangeOffset[$k] == 0) {
                                    $g = $c;
                                } else {
                                    $gid = (($idRangeOffset[$k] / 2) + ($c - $startCount[$k]) - ($segCount - $k));
                                    $g   = $glyphIdArray[$gid];
                                }
                                $g += ($idDelta[$k] - 65536);
                                if ($g < 0) {
                                    $g = 0;
                                }
                                $subsetglyphs[$g] = true;
                            }
                        }
                    }
                    break;
                }
                case 6: {
                    $offset += 4;
                    $firstCode = $this->_getUSHORT($font, $offset);
                    $offset += 2;
                    $entryCount = $this->_getUSHORT($font, $offset);
                    $offset += 2;
                    for ($k = 0; $k < $entryCount; ++$k) {
                        $c = ($k + $firstCode);
                        if (isset($subsetchars[$c])) {
                            $g                = $this->_getUSHORT($font, $offset);
                            $subsetglyphs[$g] = true;
                        }
                        $offset += 2;
                    }
                    break;
                }
                case 8: {
                    $offset += 10;
                    for ($k = 0; $k < 8192; ++$k) {
                        $is32[$k] = $this->_getBYTE($font, $offset);
                        ++$offset;
                    }
                    $nGroups = $this->_getULONG($font, $offset);
                    $offset += 4;
                    for ($i = 0; $i < $nGroups; ++$i) {
                        $startCharCode = $this->_getULONG($font, $offset);
                        $offset += 4;
                        $endCharCode = $this->_getULONG($font, $offset);
                        $offset += 4;
                        $startGlyphID = $this->_getULONG($font, $offset);
                        $offset += 4;
                        for ($k = $startCharCode; $k <= $endCharCode; ++$k) {
                            $is32idx = floor($c / 8);
                            if ((isset($is32[$is32idx])) AND (($is32[$is32idx] & (1 << (7 - ($c % 8)))) == 0)) {
                                $c = $k;
                            } else {
                                $c = ((55232 + ($k >> 10)) << 10) + (0xDC00 + ($k & 0x3FF)) - 56613888;
                            }
                            if (isset($subsetchars[$c])) {
                                $subsetglyphs[$startGlyphID] = true;
                            }
                            ++$startGlyphID;
                        }
                    }
                    break;
                }
                case 10: {
                    $offset += 10;
                    $startCharCode = $this->_getULONG($font, $offset);
                    $offset += 4;
                    $numChars = $this->_getULONG($font, $offset);
                    $offset += 4;
                    for ($k = 0; $k < $numChars; ++$k) {
                        $c = ($k + $startCharCode);
                        if (isset($subsetchars[$c])) {
                            $g                = $this->_getUSHORT($font, $offset);
                            $subsetglyphs[$g] = true;
                        }
                        $offset += 2;
                    }
                    break;
                }
                case 12: {
                    $offset += 10;
                    $nGroups = $this->_getULONG($font, $offset);
                    $offset += 4;
                    for ($k = 0; $k < $nGroups; ++$k) {
                        $startCharCode = $this->_getULONG($font, $offset);
                        $offset += 4;
                        $endCharCode = $this->_getULONG($font, $offset);
                        $offset += 4;
                        $startGlyphCode = $this->_getULONG($font, $offset);
                        $offset += 4;
                        for ($c = $startCharCode; $c <= $endCharCode; ++$c) {
                            if (isset($subsetchars[$c])) {
                                $subsetglyphs[$startGlyphCode] = true;
                            }
                            ++$startGlyphCode;
                        }
                    }
                    break;
                }
                case 13: {
                    break;
                }
                case 14: {
                    break;
                }
            }
        }
        $new_sga = $subsetglyphs;
        while (!empty($new_sga)) {
            $sga     = $new_sga;
            $new_sga = array();
            foreach ($sga as $key => $val) {
                if (isset($indexToLoc[$key])) {
                    $offset           = ($table['glyf']['offset'] + $indexToLoc[$key]);
                    $numberOfContours = $this->_getSHORT($font, $offset);
                    $offset += 2;
                    if ($numberOfContours < 0) {
                        $offset += 8;
                        do {
                            $flags = $this->_getUSHORT($font, $offset);
                            $offset += 2;
                            $glyphIndex = $this->_getUSHORT($font, $offset);
                            $offset += 2;
                            if (!isset($subsetglyphs[$glyphIndex])) {
                                $new_sga[$glyphIndex] = true;
                            }
                            if ($flags & 1) {
                                $offset += 4;
                            } else {
                                $offset += 2;
                            }
                            if ($flags & 8) {
                                $offset += 2;
                            } elseif ($flags & 64) {
                                $offset += 4;
                            } elseif ($flags & 128) {
                                $offset += 8;
                            }
                        } while ($flags & 32);
                    }
                }
            }
            $subsetglyphs += $new_sga;
        }
        ksort($subsetglyphs);
        $glyf        = '';
        $loca        = '';
        $offset      = 0;
        $glyf_offset = $table['glyf']['offset'];
        for ($i = 0; $i < $tot_num_glyphs; ++$i) {
            if (isset($subsetglyphs[$i])) {
                $length = ($indexToLoc[($i + 1)] - $indexToLoc[$i]);
                $glyf .= substr($font, ($glyf_offset + $indexToLoc[$i]), $length);
            } else {
                $length = 0;
            }
            if ($short_offset) {
                $loca .= pack('n', ($offset / 2));
            } else {
                $loca .= pack('N', $offset);
            }
            $offset += $length;
        }
        $table_names = array(
            'head',
            'hhea',
            'hmtx',
            'maxp',
            'cvt ',
            'fpgm',
            'prep'
        );
        $offset      = 12;
        foreach ($table as $tag => $val) {
            if (in_array($tag, $table_names)) {
                $table[$tag]['data'] = substr($font, $table[$tag]['offset'], $table[$tag]['length']);
                if ($tag == 'head') {
                    $table[$tag]['data'] = substr($table[$tag]['data'], 0, 8) . "\x0\x0\x0\x0" . substr($table[$tag]['data'], 12);
                }
                $pad = 4 - ($table[$tag]['length'] % 4);
                if ($pad != 4) {
                    $table[$tag]['length'] += $pad;
                    $table[$tag]['data'] .= str_repeat("\x0", $pad);
                }
                $table[$tag]['offset'] = $offset;
                $offset += $table[$tag]['length'];
            } else {
                unset($table[$tag]);
            }
        }
        $table['loca']['data']   = $loca;
        $table['loca']['length'] = strlen($loca);
        $pad                     = 4 - ($table['loca']['length'] % 4);
        if ($pad != 4) {
            $table['loca']['length'] += $pad;
            $table['loca']['data'] .= str_repeat("\x0", $pad);
        }
        $table['loca']['offset']   = $offset;
        $table['loca']['checkSum'] = $this->_getTTFtableChecksum($table['loca']['data'], $table['loca']['length']);
        $offset += $table['loca']['length'];
        $table['glyf']['data']   = $glyf;
        $table['glyf']['length'] = strlen($glyf);
        $pad                     = 4 - ($table['glyf']['length'] % 4);
        if ($pad != 4) {
            $table['glyf']['length'] += $pad;
            $table['glyf']['data'] .= str_repeat("\x0", $pad);
        }
        $table['glyf']['offset']   = $offset;
        $table['glyf']['checkSum'] = $this->_getTTFtableChecksum($table['glyf']['data'], $table['glyf']['length']);
        $font                      = '';
        $font .= pack('N', 0x10000);
        $numTables = count($table);
        $font .= pack('n', $numTables);
        $entrySelector = floor(log($numTables, 2));
        $searchRange   = pow(2, $entrySelector) * 16;
        $rangeShift    = ($numTables * 16) - $searchRange;
        $font .= pack('n', $searchRange);
        $font .= pack('n', $entrySelector);
        $font .= pack('n', $rangeShift);
        $offset = ($numTables * 16);
        foreach ($table as $tag => $data) {
            $font .= $tag;
            $font .= pack('N', $data['checkSum']);
            $font .= pack('N', ($data['offset'] + $offset));
            $font .= pack('N', $data['length']);
        }
        foreach ($table as $data) {
            $font .= $data['data'];
        }
        $checkSumAdjustment = 0xB1B0AFBA - $this->_getTTFtableChecksum($font, strlen($font));
        $font               = substr($font, 0, $table['head']['offset'] + 8) . pack('N', $checkSumAdjustment) . substr($font, $table['head']['offset'] + 12);
        return $font;
    }
    protected function _getTTFtableChecksum($table, $length)
    {
        $sum    = 0;
        $tlen   = ($length / 4);
        $offset = 0;
        for ($i = 0; $i < $tlen; ++$i) {
            $v = unpack('Ni', substr($table, $offset, 4));
            $sum += $v['i'];
            $offset += 4;
        }
        $sum = unpack('Ni', pack('N', $sum));
        return $sum['i'];
    }
    protected function _putfontwidths($font, $cidoffset = 0)
    {
        ksort($font['cw']);
        $rangeid   = 0;
        $range     = array();
        $prevcid   = -2;
        $prevwidth = -1;
        $interval  = false;
        foreach ($font['cw'] as $cid => $width) {
            $cid -= $cidoffset;
            if ($font['subset'] AND ($cid > 255) AND (!isset($font['subsetchars'][$cid]))) {
                continue;
            }
            if ($width != $font['dw']) {
                if ($cid == ($prevcid + 1)) {
                    if ($width == $prevwidth) {
                        if ($width == $range[$rangeid][0]) {
                            $range[$rangeid][] = $width;
                        } else {
                            array_pop($range[$rangeid]);
                            $rangeid           = $prevcid;
                            $range[$rangeid]   = array();
                            $range[$rangeid][] = $prevwidth;
                            $range[$rangeid][] = $width;
                        }
                        $interval                    = true;
                        $range[$rangeid]['interval'] = true;
                    } else {
                        if ($interval) {
                            $rangeid           = $cid;
                            $range[$rangeid]   = array();
                            $range[$rangeid][] = $width;
                        } else {
                            $range[$rangeid][] = $width;
                        }
                        $interval = false;
                    }
                } else {
                    $rangeid           = $cid;
                    $range[$rangeid]   = array();
                    $range[$rangeid][] = $width;
                    $interval          = false;
                }
                $prevcid   = $cid;
                $prevwidth = $width;
            }
        }
        $prevk   = -1;
        $nextk   = -1;
        $prevint = false;
        foreach ($range as $k => $ws) {
            $cws = count($ws);
            if (($k == $nextk) AND (!$prevint) AND ((!isset($ws['interval'])) OR ($cws < 4))) {
                if (isset($range[$k]['interval'])) {
                    unset($range[$k]['interval']);
                }
                $range[$prevk] = array_merge($range[$prevk], $range[$k]);
                unset($range[$k]);
            } else {
                $prevk = $k;
            }
            $nextk = $k + $cws;
            if (isset($ws['interval'])) {
                if ($cws > 3) {
                    $prevint = true;
                } else {
                    $prevint = false;
                }
                unset($range[$k]['interval']);
                --$nextk;
            } else {
                $prevint = false;
            }
        }
        $w = '';
        foreach ($range as $k => $ws) {
            if (count(array_count_values($ws)) == 1) {
                $w .= ' ' . $k . ' ' . ($k + count($ws) - 1) . ' ' . $ws[0];
            } else {
                $w .= ' ' . $k . ' [ ' . implode(' ', $ws) . ' ]';
            }
        }
        return '/W [' . $w . ' ]';
    }
    protected function _putfonts()
    {
        $nf = $this->n;
        foreach ($this->diffs as $diff) {
            $this->_newobj();
            $this->_out('<< /Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $diff . '] >>' . "\n" . 'endobj');
        }
        $mqr = $this->get_mqr();
        $this->set_mqr(false);
        foreach ($this->FontFiles as $file => $info) {
            $fontdir  = $info['fontdir'];
            $file     = strtolower($file);
            $fontfile = '';
            if (($fontdir !== false) AND file_exists($fontdir . $file)) {
                $fontfile = $fontdir . $file;
            } elseif (file_exists($this->_getfontpath() . $file)) {
                $fontfile = $this->_getfontpath() . $file;
            } elseif (file_exists($file)) {
                $fontfile = $file;
            }
            if (!$this->empty_string($fontfile)) {
                $font       = file_get_contents($fontfile);
                $compressed = (substr($file, -2) == '.z');
                if ((!$compressed) AND (isset($info['length2']))) {
                    $header = (ord($font{0}) == 128);
                    if ($header) {
                        $font = substr($font, 6);
                    }
                    if ($header AND (ord($font{$info['length1']}) == 128)) {
                        $font = substr($font, 0, $info['length1']) . substr($font, ($info['length1'] + 6));
                    }
                } elseif ($info['subset'] AND ((!$compressed) OR ($compressed AND function_exists('gzcompress')))) {
                    if ($compressed) {
                        $font = gzuncompress($font);
                    }
                    $subsetchars = array();
                    foreach ($info['fontkeys'] as $fontkey) {
                        $fontinfo = $this->getFontBuffer($fontkey);
                        $subsetchars += $fontinfo['subsetchars'];
                    }
                    $font            = $this->_getTrueTypeFontSubset($font, $subsetchars);
                    $info['length1'] = strlen($font);
                    if ($compressed) {
                        $font = gzcompress($font);
                    }
                }
                $this->_newobj();
                $this->FontFiles[$file]['n'] = $this->n;
                $stream                      = $this->_getrawstream($font);
                $out                         = '<< /Length ' . strlen($stream);
                if ($compressed) {
                    $out .= ' /Filter /FlateDecode';
                }
                $out .= ' /Length1 ' . $info['length1'];
                if (isset($info['length2'])) {
                    $out .= ' /Length2 ' . $info['length2'] . ' /Length3 0';
                }
                $out .= ' >>';
                $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            }
        }
        $this->set_mqr($mqr);
        foreach ($this->fontkeys as $k) {
            $font = $this->getFontBuffer($k);
            $type = $font['type'];
            $name = $font['name'];
            if ($type == 'core') {
                $out = $this->_getobj($this->font_obj_ids[$k]) . "\n";
                $out .= '<</Type /Font';
                $out .= ' /Subtype /Type1';
                $out .= ' /BaseFont /' . $name;
                $out .= ' /Name /F' . $font['i'];
                if ((strtolower($name) != 'symbol') AND (strtolower($name) != 'zapfdingbats')) {
                    $out .= ' /Encoding /WinAnsiEncoding';
                }
                if ($k == 'helvetica') {
                    $this->annotation_fonts[$k] = $font['i'];
                }
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            } elseif (($type == 'Type1') OR ($type == 'TrueType')) {
                $out = $this->_getobj($this->font_obj_ids[$k]) . "\n";
                $out .= '<</Type /Font';
                $out .= ' /Subtype /' . $type;
                $out .= ' /BaseFont /' . $name;
                $out .= ' /Name /F' . $font['i'];
                $out .= ' /FirstChar 32 /LastChar 255';
                $out .= ' /Widths ' . ($this->n + 1) . ' 0 R';
                $out .= ' /FontDescriptor ' . ($this->n + 2) . ' 0 R';
                if ($font['enc']) {
                    if (isset($font['diff'])) {
                        $out .= ' /Encoding ' . ($nf + $font['diff']) . ' 0 R';
                    } else {
                        $out .= ' /Encoding /WinAnsiEncoding';
                    }
                }
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
                $this->_newobj();
                $s = '[';
                for ($i = 32; $i < 256; ++$i) {
                    $s .= $font['cw'][$i] . ' ';
                }
                $s .= ']';
                $s .= "\n" . 'endobj';
                $this->_out($s);
                $this->_newobj();
                $s = '<</Type /FontDescriptor /FontName /' . $name;
                foreach ($font['desc'] as $fdk => $fdv) {
                    if (is_float($fdv)) {
                        $fdv = sprintf('%.3F', $fdv);
                    }
                    $s .= ' /' . $fdk . ' ' . $fdv . '';
                }
                if (!$this->empty_string($font['file'])) {
                    $s .= ' /FontFile' . ($type == 'Type1' ? '' : '2') . ' ' . $this->FontFiles[$font['file']]['n'] . ' 0 R';
                }
                $s .= '>>';
                $s .= "\n" . 'endobj';
                $this->_out($s);
            } else {
                $mtd = '_put' . strtolower($type);
                if (!method_exists($this, $mtd)) {
                    $this->Error('Unsupported font type: ' . $type);
                }
                $this->$mtd($font);
            }
        }
    }
    protected function _puttruetypeunicode($font)
    {
        $fontname = '';
        if ($font['subset']) {
            $subtag = sprintf('%06u', $font['i']);
            $subtag = strtr($subtag, '0123456789', 'ABCDEFGHIJ');
            $fontname .= $subtag . '+';
        }
        $fontname .= $font['name'];
        $out = $this->_getobj($this->font_obj_ids[$font['fontkey']]) . "\n";
        $out .= '<< /Type /Font';
        $out .= ' /Subtype /Type0';
        $out .= ' /BaseFont /' . $fontname;
        $out .= ' /Name /F' . $font['i'];
        $out .= ' /Encoding /' . $font['enc'];
        $out .= ' /ToUnicode ' . ($this->n + 1) . ' 0 R';
        $out .= ' /DescendantFonts [' . ($this->n + 2) . ' 0 R]';
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        $stream = "/CIDInit /ProcSet findresource begin\n";
        $stream .= "12 dict begin\n";
        $stream .= "begincmap\n";
        $stream .= "/CIDSystemInfo << /Registry (Adobe) /Ordering (UCS) /Supplement 0 >> def\n";
        $stream .= "/CMapName /Adobe-Identity-UCS def\n";
        $stream .= "/CMapType 2 def\n";
        $stream .= "/WMode 0 def\n";
        $stream .= "1 begincodespacerange\n";
        $stream .= "<0000> <FFFF>\n";
        $stream .= "endcodespacerange\n";
        $stream .= "100 beginbfrange\n";
        $stream .= "<0000> <00ff> <0000>\n";
        $stream .= "<0100> <01ff> <0100>\n";
        $stream .= "<0200> <02ff> <0200>\n";
        $stream .= "<0300> <03ff> <0300>\n";
        $stream .= "<0400> <04ff> <0400>\n";
        $stream .= "<0500> <05ff> <0500>\n";
        $stream .= "<0600> <06ff> <0600>\n";
        $stream .= "<0700> <07ff> <0700>\n";
        $stream .= "<0800> <08ff> <0800>\n";
        $stream .= "<0900> <09ff> <0900>\n";
        $stream .= "<0a00> <0aff> <0a00>\n";
        $stream .= "<0b00> <0bff> <0b00>\n";
        $stream .= "<0c00> <0cff> <0c00>\n";
        $stream .= "<0d00> <0dff> <0d00>\n";
        $stream .= "<0e00> <0eff> <0e00>\n";
        $stream .= "<0f00> <0fff> <0f00>\n";
        $stream .= "<1000> <10ff> <1000>\n";
        $stream .= "<1100> <11ff> <1100>\n";
        $stream .= "<1200> <12ff> <1200>\n";
        $stream .= "<1300> <13ff> <1300>\n";
        $stream .= "<1400> <14ff> <1400>\n";
        $stream .= "<1500> <15ff> <1500>\n";
        $stream .= "<1600> <16ff> <1600>\n";
        $stream .= "<1700> <17ff> <1700>\n";
        $stream .= "<1800> <18ff> <1800>\n";
        $stream .= "<1900> <19ff> <1900>\n";
        $stream .= "<1a00> <1aff> <1a00>\n";
        $stream .= "<1b00> <1bff> <1b00>\n";
        $stream .= "<1c00> <1cff> <1c00>\n";
        $stream .= "<1d00> <1dff> <1d00>\n";
        $stream .= "<1e00> <1eff> <1e00>\n";
        $stream .= "<1f00> <1fff> <1f00>\n";
        $stream .= "<2000> <20ff> <2000>\n";
        $stream .= "<2100> <21ff> <2100>\n";
        $stream .= "<2200> <22ff> <2200>\n";
        $stream .= "<2300> <23ff> <2300>\n";
        $stream .= "<2400> <24ff> <2400>\n";
        $stream .= "<2500> <25ff> <2500>\n";
        $stream .= "<2600> <26ff> <2600>\n";
        $stream .= "<2700> <27ff> <2700>\n";
        $stream .= "<2800> <28ff> <2800>\n";
        $stream .= "<2900> <29ff> <2900>\n";
        $stream .= "<2a00> <2aff> <2a00>\n";
        $stream .= "<2b00> <2bff> <2b00>\n";
        $stream .= "<2c00> <2cff> <2c00>\n";
        $stream .= "<2d00> <2dff> <2d00>\n";
        $stream .= "<2e00> <2eff> <2e00>\n";
        $stream .= "<2f00> <2fff> <2f00>\n";
        $stream .= "<3000> <30ff> <3000>\n";
        $stream .= "<3100> <31ff> <3100>\n";
        $stream .= "<3200> <32ff> <3200>\n";
        $stream .= "<3300> <33ff> <3300>\n";
        $stream .= "<3400> <34ff> <3400>\n";
        $stream .= "<3500> <35ff> <3500>\n";
        $stream .= "<3600> <36ff> <3600>\n";
        $stream .= "<3700> <37ff> <3700>\n";
        $stream .= "<3800> <38ff> <3800>\n";
        $stream .= "<3900> <39ff> <3900>\n";
        $stream .= "<3a00> <3aff> <3a00>\n";
        $stream .= "<3b00> <3bff> <3b00>\n";
        $stream .= "<3c00> <3cff> <3c00>\n";
        $stream .= "<3d00> <3dff> <3d00>\n";
        $stream .= "<3e00> <3eff> <3e00>\n";
        $stream .= "<3f00> <3fff> <3f00>\n";
        $stream .= "<4000> <40ff> <4000>\n";
        $stream .= "<4100> <41ff> <4100>\n";
        $stream .= "<4200> <42ff> <4200>\n";
        $stream .= "<4300> <43ff> <4300>\n";
        $stream .= "<4400> <44ff> <4400>\n";
        $stream .= "<4500> <45ff> <4500>\n";
        $stream .= "<4600> <46ff> <4600>\n";
        $stream .= "<4700> <47ff> <4700>\n";
        $stream .= "<4800> <48ff> <4800>\n";
        $stream .= "<4900> <49ff> <4900>\n";
        $stream .= "<4a00> <4aff> <4a00>\n";
        $stream .= "<4b00> <4bff> <4b00>\n";
        $stream .= "<4c00> <4cff> <4c00>\n";
        $stream .= "<4d00> <4dff> <4d00>\n";
        $stream .= "<4e00> <4eff> <4e00>\n";
        $stream .= "<4f00> <4fff> <4f00>\n";
        $stream .= "<5000> <50ff> <5000>\n";
        $stream .= "<5100> <51ff> <5100>\n";
        $stream .= "<5200> <52ff> <5200>\n";
        $stream .= "<5300> <53ff> <5300>\n";
        $stream .= "<5400> <54ff> <5400>\n";
        $stream .= "<5500> <55ff> <5500>\n";
        $stream .= "<5600> <56ff> <5600>\n";
        $stream .= "<5700> <57ff> <5700>\n";
        $stream .= "<5800> <58ff> <5800>\n";
        $stream .= "<5900> <59ff> <5900>\n";
        $stream .= "<5a00> <5aff> <5a00>\n";
        $stream .= "<5b00> <5bff> <5b00>\n";
        $stream .= "<5c00> <5cff> <5c00>\n";
        $stream .= "<5d00> <5dff> <5d00>\n";
        $stream .= "<5e00> <5eff> <5e00>\n";
        $stream .= "<5f00> <5fff> <5f00>\n";
        $stream .= "<6000> <60ff> <6000>\n";
        $stream .= "<6100> <61ff> <6100>\n";
        $stream .= "<6200> <62ff> <6200>\n";
        $stream .= "<6300> <63ff> <6300>\n";
        $stream .= "endbfrange\n";
        $stream .= "100 beginbfrange\n";
        $stream .= "<6400> <64ff> <6400>\n";
        $stream .= "<6500> <65ff> <6500>\n";
        $stream .= "<6600> <66ff> <6600>\n";
        $stream .= "<6700> <67ff> <6700>\n";
        $stream .= "<6800> <68ff> <6800>\n";
        $stream .= "<6900> <69ff> <6900>\n";
        $stream .= "<6a00> <6aff> <6a00>\n";
        $stream .= "<6b00> <6bff> <6b00>\n";
        $stream .= "<6c00> <6cff> <6c00>\n";
        $stream .= "<6d00> <6dff> <6d00>\n";
        $stream .= "<6e00> <6eff> <6e00>\n";
        $stream .= "<6f00> <6fff> <6f00>\n";
        $stream .= "<7000> <70ff> <7000>\n";
        $stream .= "<7100> <71ff> <7100>\n";
        $stream .= "<7200> <72ff> <7200>\n";
        $stream .= "<7300> <73ff> <7300>\n";
        $stream .= "<7400> <74ff> <7400>\n";
        $stream .= "<7500> <75ff> <7500>\n";
        $stream .= "<7600> <76ff> <7600>\n";
        $stream .= "<7700> <77ff> <7700>\n";
        $stream .= "<7800> <78ff> <7800>\n";
        $stream .= "<7900> <79ff> <7900>\n";
        $stream .= "<7a00> <7aff> <7a00>\n";
        $stream .= "<7b00> <7bff> <7b00>\n";
        $stream .= "<7c00> <7cff> <7c00>\n";
        $stream .= "<7d00> <7dff> <7d00>\n";
        $stream .= "<7e00> <7eff> <7e00>\n";
        $stream .= "<7f00> <7fff> <7f00>\n";
        $stream .= "<8000> <80ff> <8000>\n";
        $stream .= "<8100> <81ff> <8100>\n";
        $stream .= "<8200> <82ff> <8200>\n";
        $stream .= "<8300> <83ff> <8300>\n";
        $stream .= "<8400> <84ff> <8400>\n";
        $stream .= "<8500> <85ff> <8500>\n";
        $stream .= "<8600> <86ff> <8600>\n";
        $stream .= "<8700> <87ff> <8700>\n";
        $stream .= "<8800> <88ff> <8800>\n";
        $stream .= "<8900> <89ff> <8900>\n";
        $stream .= "<8a00> <8aff> <8a00>\n";
        $stream .= "<8b00> <8bff> <8b00>\n";
        $stream .= "<8c00> <8cff> <8c00>\n";
        $stream .= "<8d00> <8dff> <8d00>\n";
        $stream .= "<8e00> <8eff> <8e00>\n";
        $stream .= "<8f00> <8fff> <8f00>\n";
        $stream .= "<9000> <90ff> <9000>\n";
        $stream .= "<9100> <91ff> <9100>\n";
        $stream .= "<9200> <92ff> <9200>\n";
        $stream .= "<9300> <93ff> <9300>\n";
        $stream .= "<9400> <94ff> <9400>\n";
        $stream .= "<9500> <95ff> <9500>\n";
        $stream .= "<9600> <96ff> <9600>\n";
        $stream .= "<9700> <97ff> <9700>\n";
        $stream .= "<9800> <98ff> <9800>\n";
        $stream .= "<9900> <99ff> <9900>\n";
        $stream .= "<9a00> <9aff> <9a00>\n";
        $stream .= "<9b00> <9bff> <9b00>\n";
        $stream .= "<9c00> <9cff> <9c00>\n";
        $stream .= "<9d00> <9dff> <9d00>\n";
        $stream .= "<9e00> <9eff> <9e00>\n";
        $stream .= "<9f00> <9fff> <9f00>\n";
        $stream .= "<a000> <a0ff> <a000>\n";
        $stream .= "<a100> <a1ff> <a100>\n";
        $stream .= "<a200> <a2ff> <a200>\n";
        $stream .= "<a300> <a3ff> <a300>\n";
        $stream .= "<a400> <a4ff> <a400>\n";
        $stream .= "<a500> <a5ff> <a500>\n";
        $stream .= "<a600> <a6ff> <a600>\n";
        $stream .= "<a700> <a7ff> <a700>\n";
        $stream .= "<a800> <a8ff> <a800>\n";
        $stream .= "<a900> <a9ff> <a900>\n";
        $stream .= "<aa00> <aaff> <aa00>\n";
        $stream .= "<ab00> <abff> <ab00>\n";
        $stream .= "<ac00> <acff> <ac00>\n";
        $stream .= "<ad00> <adff> <ad00>\n";
        $stream .= "<ae00> <aeff> <ae00>\n";
        $stream .= "<af00> <afff> <af00>\n";
        $stream .= "<b000> <b0ff> <b000>\n";
        $stream .= "<b100> <b1ff> <b100>\n";
        $stream .= "<b200> <b2ff> <b200>\n";
        $stream .= "<b300> <b3ff> <b300>\n";
        $stream .= "<b400> <b4ff> <b400>\n";
        $stream .= "<b500> <b5ff> <b500>\n";
        $stream .= "<b600> <b6ff> <b600>\n";
        $stream .= "<b700> <b7ff> <b700>\n";
        $stream .= "<b800> <b8ff> <b800>\n";
        $stream .= "<b900> <b9ff> <b900>\n";
        $stream .= "<ba00> <baff> <ba00>\n";
        $stream .= "<bb00> <bbff> <bb00>\n";
        $stream .= "<bc00> <bcff> <bc00>\n";
        $stream .= "<bd00> <bdff> <bd00>\n";
        $stream .= "<be00> <beff> <be00>\n";
        $stream .= "<bf00> <bfff> <bf00>\n";
        $stream .= "<c000> <c0ff> <c000>\n";
        $stream .= "<c100> <c1ff> <c100>\n";
        $stream .= "<c200> <c2ff> <c200>\n";
        $stream .= "<c300> <c3ff> <c300>\n";
        $stream .= "<c400> <c4ff> <c400>\n";
        $stream .= "<c500> <c5ff> <c500>\n";
        $stream .= "<c600> <c6ff> <c600>\n";
        $stream .= "<c700> <c7ff> <c700>\n";
        $stream .= "endbfrange\n";
        $stream .= "56 beginbfrange\n";
        $stream .= "<c800> <c8ff> <c800>\n";
        $stream .= "<c900> <c9ff> <c900>\n";
        $stream .= "<ca00> <caff> <ca00>\n";
        $stream .= "<cb00> <cbff> <cb00>\n";
        $stream .= "<cc00> <ccff> <cc00>\n";
        $stream .= "<cd00> <cdff> <cd00>\n";
        $stream .= "<ce00> <ceff> <ce00>\n";
        $stream .= "<cf00> <cfff> <cf00>\n";
        $stream .= "<d000> <d0ff> <d000>\n";
        $stream .= "<d100> <d1ff> <d100>\n";
        $stream .= "<d200> <d2ff> <d200>\n";
        $stream .= "<d300> <d3ff> <d300>\n";
        $stream .= "<d400> <d4ff> <d400>\n";
        $stream .= "<d500> <d5ff> <d500>\n";
        $stream .= "<d600> <d6ff> <d600>\n";
        $stream .= "<d700> <d7ff> <d700>\n";
        $stream .= "<d800> <d8ff> <d800>\n";
        $stream .= "<d900> <d9ff> <d900>\n";
        $stream .= "<da00> <daff> <da00>\n";
        $stream .= "<db00> <dbff> <db00>\n";
        $stream .= "<dc00> <dcff> <dc00>\n";
        $stream .= "<dd00> <ddff> <dd00>\n";
        $stream .= "<de00> <deff> <de00>\n";
        $stream .= "<df00> <dfff> <df00>\n";
        $stream .= "<e000> <e0ff> <e000>\n";
        $stream .= "<e100> <e1ff> <e100>\n";
        $stream .= "<e200> <e2ff> <e200>\n";
        $stream .= "<e300> <e3ff> <e300>\n";
        $stream .= "<e400> <e4ff> <e400>\n";
        $stream .= "<e500> <e5ff> <e500>\n";
        $stream .= "<e600> <e6ff> <e600>\n";
        $stream .= "<e700> <e7ff> <e700>\n";
        $stream .= "<e800> <e8ff> <e800>\n";
        $stream .= "<e900> <e9ff> <e900>\n";
        $stream .= "<ea00> <eaff> <ea00>\n";
        $stream .= "<eb00> <ebff> <eb00>\n";
        $stream .= "<ec00> <ecff> <ec00>\n";
        $stream .= "<ed00> <edff> <ed00>\n";
        $stream .= "<ee00> <eeff> <ee00>\n";
        $stream .= "<ef00> <efff> <ef00>\n";
        $stream .= "<f000> <f0ff> <f000>\n";
        $stream .= "<f100> <f1ff> <f100>\n";
        $stream .= "<f200> <f2ff> <f200>\n";
        $stream .= "<f300> <f3ff> <f300>\n";
        $stream .= "<f400> <f4ff> <f400>\n";
        $stream .= "<f500> <f5ff> <f500>\n";
        $stream .= "<f600> <f6ff> <f600>\n";
        $stream .= "<f700> <f7ff> <f700>\n";
        $stream .= "<f800> <f8ff> <f800>\n";
        $stream .= "<f900> <f9ff> <f900>\n";
        $stream .= "<fa00> <faff> <fa00>\n";
        $stream .= "<fb00> <fbff> <fb00>\n";
        $stream .= "<fc00> <fcff> <fc00>\n";
        $stream .= "<fd00> <fdff> <fd00>\n";
        $stream .= "<fe00> <feff> <fe00>\n";
        $stream .= "<ff00> <ffff> <ff00>\n";
        $stream .= "endbfrange\n";
        $stream .= "endcmap\n";
        $stream .= "CMapName currentdict /CMap defineresource pop\n";
        $stream .= "end\n";
        $stream .= "end";
        $this->_newobj();
        $stream = ($this->compress) ? gzcompress($stream) : $stream;
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        $stream = $this->_getrawstream($stream);
        $this->_out('<<' . $filter . '/Length ' . strlen($stream) . '>> stream' . "\n" . $stream . "\n" . 'endstream' . "\n" . 'endobj');
        $oid = $this->_newobj();
        $out = '<< /Type /Font';
        $out .= ' /Subtype /CIDFontType2';
        $out .= ' /BaseFont /' . $fontname;
        $cidinfo = '/Registry ' . $this->_datastring($font['cidinfo']['Registry'], $oid);
        $cidinfo .= ' /Ordering ' . $this->_datastring($font['cidinfo']['Ordering'], $oid);
        $cidinfo .= ' /Supplement ' . $font['cidinfo']['Supplement'];
        $out .= ' /CIDSystemInfo << ' . $cidinfo . ' >>';
        $out .= ' /FontDescriptor ' . ($this->n + 1) . ' 0 R';
        $out .= ' /DW ' . $font['dw'];
        $out .= "\n" . $this->_putfontwidths($font, 0);
        if (isset($font['ctg']) AND (!$this->empty_string($font['ctg']))) {
            $out .= "\n" . '/CIDToGIDMap ' . ($this->n + 2) . ' 0 R';
        }
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        $this->_newobj();
        $out = '<< /Type /FontDescriptor';
        $out .= ' /FontName /' . $fontname;
        foreach ($font['desc'] as $key => $value) {
            if (is_float($value)) {
                $value = sprintf('%.3F', $value);
            }
            $out .= ' /' . $key . ' ' . $value;
        }
        $fontdir = false;
        if (!$this->empty_string($font['file'])) {
            $out .= ' /FontFile2 ' . $this->FontFiles[$font['file']]['n'] . ' 0 R';
            $fontdir = $this->FontFiles[$font['file']]['fontdir'];
        }
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        if (isset($font['ctg']) AND (!$this->empty_string($font['ctg']))) {
            $this->_newobj();
            $ctgfile  = strtolower($font['ctg']);
            $fontfile = '';
            if (($fontdir !== false) AND file_exists($fontdir . $ctgfile)) {
                $fontfile = $fontdir . $ctgfile;
            } elseif (file_exists($this->_getfontpath() . $ctgfile)) {
                $fontfile = $this->_getfontpath() . $ctgfile;
            } elseif (file_exists($ctgfile)) {
                $fontfile = $ctgfile;
            }
            if ($this->empty_string($fontfile)) {
                $this->Error('Font file not found: ' . $ctgfile);
            }
            $stream = $this->_getrawstream(file_get_contents($fontfile));
            $out    = '<< /Length ' . strlen($stream) . '';
            if (substr($fontfile, -2) == '.z') {
                $out .= ' /Filter /FlateDecode';
            }
            $out .= ' >>';
            $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
            $out .= "\n" . 'endobj';
            $this->_out($out);
        }
    }
    protected function _putcidfont0($font)
    {
        $cidoffset = 0;
        if (!isset($font['cw'][1])) {
            $cidoffset = 31;
        }
        if (isset($font['cidinfo']['uni2cid'])) {
            $uni2cid = $font['cidinfo']['uni2cid'];
            $cw      = array();
            foreach ($font['cw'] as $uni => $width) {
                if (isset($uni2cid[$uni])) {
                    $cw[($uni2cid[$uni] + $cidoffset)] = $width;
                } elseif ($uni < 256) {
                    $cw[$uni] = $width;
                }
            }
            $font = array_merge($font, array(
                'cw' => $cw
            ));
        }
        $name = $font['name'];
        $enc  = $font['enc'];
        if ($enc) {
            $longname = $name . '-' . $enc;
        } else {
            $longname = $name;
        }
        $out = $this->_getobj($this->font_obj_ids[$font['fontkey']]) . "\n";
        $out .= '<</Type /Font';
        $out .= ' /Subtype /Type0';
        $out .= ' /BaseFont /' . $longname;
        $out .= ' /Name /F' . $font['i'];
        if ($enc) {
            $out .= ' /Encoding /' . $enc;
        }
        $out .= ' /DescendantFonts [' . ($this->n + 1) . ' 0 R]';
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        $oid = $this->_newobj();
        $out = '<</Type /Font';
        $out .= ' /Subtype /CIDFontType0';
        $out .= ' /BaseFont /' . $name;
        $cidinfo = '/Registry ' . $this->_datastring($font['cidinfo']['Registry'], $oid);
        $cidinfo .= ' /Ordering ' . $this->_datastring($font['cidinfo']['Ordering'], $oid);
        $cidinfo .= ' /Supplement ' . $font['cidinfo']['Supplement'];
        $out .= ' /CIDSystemInfo <<' . $cidinfo . '>>';
        $out .= ' /FontDescriptor ' . ($this->n + 1) . ' 0 R';
        $out .= ' /DW ' . $font['dw'];
        $out .= "\n" . $this->_putfontwidths($font, $cidoffset);
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        $this->_newobj();
        $s = '<</Type /FontDescriptor /FontName /' . $name;
        foreach ($font['desc'] as $k => $v) {
            if ($k != 'Style') {
                if (is_float($v)) {
                    $v = sprintf('%.3F', $v);
                }
                $s .= ' /' . $k . ' ' . $v . '';
            }
        }
        $s .= '>>';
        $s .= "\n" . 'endobj';
        $this->_out($s);
    }
    protected function _putimages()
    {
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        foreach ($this->imagekeys as $file) {
            $info = $this->getImageBuffer($file);
            if (!empty($info['altimgs'])) {
                $altoid = $this->_newobj();
                $out    = '[';
                foreach ($info['altimgs'] as $altimage) {
                    if (isset($this->xobjects['I' . $altimage[0]]['n'])) {
                        $out .= ' << /Image ' . $this->xobjects['I' . $altimage[0]]['n'] . ' 0 R';
                        $out .= ' /DefaultForPrinting';
                        if ($altimage[1] === true) {
                            $out .= ' true';
                        } else {
                            $out .= ' false';
                        }
                        $out .= ' >>';
                    }
                }
                $out .= ' ]';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            }
            $oid                              = $this->_newobj();
            $this->xobjects['I' . $info['i']] = array(
                'n' => $oid
            );
            $this->setImageSubBuffer($file, 'n', $this->n);
            $out = '<</Type /XObject';
            $out .= ' /Subtype /Image';
            $out .= ' /Width ' . $info['w'];
            $out .= ' /Height ' . $info['h'];
            if (array_key_exists('masked', $info)) {
                $out .= ' /SMask ' . ($this->n - 1) . ' 0 R';
            }
            if ($info['cs'] == 'Indexed') {
                $out .= ' /ColorSpace [/Indexed /DeviceRGB ' . ((strlen($info['pal']) / 3) - 1) . ' ' . ($this->n + 1) . ' 0 R]';
            } else {
                $out .= ' /ColorSpace /' . $info['cs'];
                if ($info['cs'] == 'DeviceCMYK') {
                    $out .= ' /Decode [1 0 1 0 1 0 1 0]';
                }
            }
            $out .= ' /BitsPerComponent ' . $info['bpc'];
            if (isset($altoid) AND ($altoid > 0)) {
                $out .= ' /Alternates ' . $altoid . ' 0 R';
            }
            if (isset($info['exurl']) AND !empty($info['exurl'])) {
                $out .= ' /Length 0';
                $out .= ' /F << /FS /URL /F ' . $this->_datastring($info['exurl'], $oid) . ' >>';
                if (isset($info['f'])) {
                    $out .= ' /FFilter /' . $info['f'];
                }
                $out .= ' >>';
                $out .= ' stream' . "\n" . 'endstream';
            } else {
                if (isset($info['f'])) {
                    $out .= ' /Filter /' . $info['f'];
                }
                if (isset($info['parms'])) {
                    $out .= ' ' . $info['parms'];
                }
                if (isset($info['trns']) AND is_array($info['trns'])) {
                    $trns       = '';
                    $count_info = count($info['trns']);
                    for ($i = 0; $i < $count_info; ++$i) {
                        $trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
                    }
                    $out .= ' /Mask [' . $trns . ']';
                }
                $stream = $this->_getrawstream($info['data']);
                $out .= ' /Length ' . strlen($stream) . ' >>';
                $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
            }
            $out .= "\n" . 'endobj';
            $this->_out($out);
            if ($info['cs'] == 'Indexed') {
                $this->_newobj();
                $pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
                $pal = $this->_getrawstream($pal);
                $this->_out('<<' . $filter . '/Length ' . strlen($pal) . '>> stream' . "\n" . $pal . "\n" . 'endstream' . "\n" . 'endobj');
            }
        }
    }
    protected function _putxobjects()
    {
        foreach ($this->xobjects as $key => $data) {
            if (isset($data['outdata'])) {
                $stream = trim($data['outdata']);
                $out    = $this->_getobj($data['n']) . "\n";
                $out .= '<<';
                $out .= ' /Type /XObject';
                $out .= ' /Subtype /Form';
                $out .= ' /FormType 1';
                if ($this->compress) {
                    $stream = gzcompress($stream);
                    $out .= ' /Filter /FlateDecode';
                }
                $out .= sprintf(' /BBox [%.2F %.2F %.2F %.2F]', ($data['x'] * $this->k), (-$data['y'] * $this->k), (($data['w'] + $data['x']) * $this->k), (($data['h'] - $data['y']) * $this->k));
                $out .= ' /Matrix [1 0 0 1 0 0]';
                $out .= ' /Resources <<';
                $out .= ' /ProcSet [/PDF /Text /ImageB /ImageC /ImageI]';
                if (!empty($data['fonts'])) {
                    $out .= ' /Font <<';
                    foreach ($data['fonts'] as $fontkey => $fontid) {
                        $out .= ' /F' . $fontid . ' ' . $this->font_obj_ids[$fontkey] . ' 0 R';
                    }
                    $out .= ' >>';
                }
                if (!empty($data['images']) OR !empty($data['xobjects'])) {
                    $out .= ' /XObject <<';
                    foreach ($data['images'] as $imgid) {
                        $out .= ' /I' . $imgid . ' ' . $this->xobjects['I' . $imgid]['n'] . ' 0 R';
                    }
                    foreach ($data['xobjects'] as $sub_id => $sub_objid) {
                        $out .= ' /' . $sub_id . ' ' . $sub_objid['n'] . ' 0 R';
                    }
                    $out .= ' >>';
                }
                $out .= ' >>';
                $stream = $this->_getrawstream($stream, $data['n']);
                $out .= ' /Length ' . strlen($stream);
                $out .= ' >>';
                $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            }
        }
    }
    protected function _putspotcolors()
    {
        foreach ($this->spot_colors as $name => $color) {
            $this->_newobj();
            $this->spot_colors[$name]['n'] = $this->n;
            $out                           = '[/Separation /' . str_replace(' ', '#20', $name);
            $out .= ' /DeviceCMYK <<';
            $out .= ' /Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0]';
            $out .= ' ' . sprintf('/C1 [%.4F %.4F %.4F %.4F] ', ($color['c'] / 100), ($color['m'] / 100), ($color['y'] / 100), ($color['k'] / 100));
            $out .= ' /FunctionType 2 /Domain [0 1] /N 1>>]';
            $out .= "\n" . 'endobj';
            $this->_out($out);
        }
    }
    protected function _getxobjectdict()
    {
        $out = '';
        foreach ($this->xobjects as $id => $objid) {
            $out .= ' /' . $id . ' ' . $objid['n'] . ' 0 R';
        }
        return $out;
    }
    protected function _putresourcedict()
    {
        $out = $this->_getobj(2) . "\n";
        $out .= '<< /ProcSet [/PDF /Text /ImageB /ImageC /ImageI]';
        $out .= ' /Font <<';
        foreach ($this->fontkeys as $fontkey) {
            $font = $this->getFontBuffer($fontkey);
            $out .= ' /F' . $font['i'] . ' ' . $font['n'] . ' 0 R';
        }
        $out .= ' >>';
        $out .= ' /XObject <<';
        $out .= $this->_getxobjectdict();
        $out .= ' >>';
        if ($this->pdflayers) {
            $out .= ' /Properties <</OC1 ' . $this->n_ocg_print . ' 0 R /OC2 ' . $this->n_ocg_view . ' 0 R>>';
        }
        $out .= ' /ExtGState <<';
        foreach ($this->extgstates as $k => $extgstate) {
            if (isset($extgstate['name'])) {
                $out .= ' /' . $extgstate['name'];
            } else {
                $out .= ' /GS' . $k;
            }
            $out .= ' ' . $extgstate['n'] . ' 0 R';
        }
        $out .= ' >>';
        if (isset($this->gradients) AND (count($this->gradients) > 0)) {
            $out .= ' /Pattern <<';
            foreach ($this->gradients as $id => $grad) {
                $out .= ' /p' . $id . ' ' . $grad['pattern'] . ' 0 R';
            }
            $out .= ' >>';
        }
        if (isset($this->gradients) AND (count($this->gradients) > 0)) {
            $out .= ' /Shading <<';
            foreach ($this->gradients as $id => $grad) {
                $out .= ' /Sh' . $id . ' ' . $grad['id'] . ' 0 R';
            }
            $out .= ' >>';
        }
        if (isset($this->spot_colors) AND (count($this->spot_colors) > 0)) {
            $out .= ' /ColorSpace <<';
            foreach ($this->spot_colors as $color) {
                $out .= ' /CS' . $color['i'] . ' ' . $color['n'] . ' 0 R';
            }
            $out .= ' >>';
        }
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
    }
    protected function _putresources()
    {
        $this->_putextgstates();
        $this->_putocg();
        $this->_putfonts();
        $this->_putimages();
        $this->_putxobjects();
        $this->_putspotcolors();
        $this->_putshaders();
        $this->_putresourcedict();
        $this->_putdests();
        $this->_putbookmarks();
        $this->_putEmbeddedFiles();
        $this->_putannotsobjs();
        $this->_putjavascript();
        $this->_putencryption();
    }
    protected function _putinfo()
    {
        $oid            = $this->_newobj();
        $out            = '<<';
        $prev_isunicode = $this->isunicode;
        if ($this->docinfounicode) {
            $this->isunicode = true;
        }
        if (!$this->empty_string($this->title)) {
            $out .= ' /Title ' . $this->_textstring($this->title, $oid);
        }
        if (!$this->empty_string($this->author)) {
            $out .= ' /Author ' . $this->_textstring($this->author, $oid);
        }
        if (!$this->empty_string($this->subject)) {
            $out .= ' /Subject ' . $this->_textstring($this->subject, $oid);
        }
        if (!$this->empty_string($this->keywords)) {
            $out .= ' /Keywords ' . $this->_textstring($this->keywords . ' TCPDF', $oid);
        }
        if (!$this->empty_string($this->creator)) {
            $out .= ' /Creator ' . $this->_textstring($this->creator, $oid);
        }
        $this->isunicode = $prev_isunicode;
        $out .= ' /Producer ' . $this->_textstring("\x54\x43\x50\x44\x46\x20" . $this->tcpdf_version . "\x20\x28\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67\x29", $oid);
        $out .= ' /CreationDate ' . $this->_datestring();
        $out .= ' /ModDate ' . $this->_datestring();
        $out .= ' /Trapped /False';
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        return $oid;
    }
    protected function _putcatalog()
    {
        $oid = $this->_newobj();
        $out = '<< /Type /Catalog';
        $out .= ' /Version /' . $this->PDFVersion;
        $out .= ' /Pages 1 0 R';
        $out .= ' /Names <<';
        if ((!empty($this->javascript)) OR (!empty($this->js_objects))) {
            $out .= ' /JavaScript ' . ($this->n_js) . ' 0 R';
        }
        $out .= ' >>';
        if (!empty($this->dests)) {
            $out .= ' /Dests ' . $this->n_dests . ' 0 R';
        }
        $out .= $this->_putviewerpreferences();
        if (isset($this->LayoutMode) AND (!$this->empty_string($this->LayoutMode))) {
            $out .= ' /PageLayout /' . $this->LayoutMode;
        }
        if (isset($this->PageMode) AND (!$this->empty_string($this->PageMode))) {
            $out .= ' /PageMode /' . $this->PageMode;
        }
        if (count($this->outlines) > 0) {
            $out .= ' /Outlines ' . $this->OutlineRoot . ' 0 R';
            $out .= ' /PageMode /UseOutlines';
        }
        if ($this->ZoomMode == 'fullpage') {
            $out .= ' /OpenAction [' . $this->page_obj_id[1] . ' 0 R /Fit]';
        } elseif ($this->ZoomMode == 'fullwidth') {
            $out .= ' /OpenAction [' . $this->page_obj_id[1] . ' 0 R /FitH null]';
        } elseif ($this->ZoomMode == 'real') {
            $out .= ' /OpenAction [' . $this->page_obj_id[1] . ' 0 R /XYZ null null 1]';
        } elseif (!is_string($this->ZoomMode)) {
            $out .= sprintf(' /OpenAction [' . $this->page_obj_id[1] . ' 0 R /XYZ null null %.2F]', ($this->ZoomMode / 100));
        }
        if (isset($this->l['a_meta_language'])) {
            $out .= ' /Lang ' . $this->_textstring($this->l['a_meta_language'], $oid);
        }
        if ($this->pdflayers) {
            $p  = $this->n_ocg_print . ' 0 R';
            $v  = $this->n_ocg_view . ' 0 R';
            $as = '<< /Event /Print /OCGs [' . $p . ' ' . $v . '] /Category [/Print] >> << /Event /View /OCGs [' . $p . ' ' . $v . '] /Category [/View] >>';
            $out .= ' /OCProperties << /OCGs [' . $p . ' ' . $v . '] /D << /ON [' . $p . '] /OFF [' . $v . '] /AS [' . $as . '] >> >>';
        }
        if (!empty($this->form_obj_id) OR ($this->sign AND isset($this->signature_data['cert_type']))) {
            $out .= ' /AcroForm <<';
            $objrefs = '';
            if ($this->sign AND isset($this->signature_data['cert_type'])) {
                $objrefs .= $this->sig_obj_id . ' 0 R';
            }
            if (!empty($this->form_obj_id)) {
                foreach ($this->form_obj_id as $objid) {
                    $objrefs .= ' ' . $objid . ' 0 R';
                }
            }
            $out .= ' /Fields [' . $objrefs . ']';
            if (!empty($this->form_obj_id) AND !$this->sign) {
                $out .= ' /NeedAppearances true';
            }
            if ($this->sign AND isset($this->signature_data['cert_type'])) {
                if ($this->signature_data['cert_type'] > 0) {
                    $out .= ' /SigFlags 3';
                } else {
                    $out .= ' /SigFlags 1';
                }
            }
            if (isset($this->annotation_fonts) AND !empty($this->annotation_fonts)) {
                $out .= ' /DR <<';
                $out .= ' /Font <<';
                foreach ($this->annotation_fonts as $fontkey => $fontid) {
                    $out .= ' /F' . $fontid . ' ' . $this->font_obj_ids[$fontkey] . ' 0 R';
                }
                $out .= ' >> >>';
            }
            $font = $this->getFontBuffer('helvetica');
            $out .= ' /DA (/F' . $font['i'] . ' 0 Tf 0 g)';
            $out .= ' /Q ' . (($this->rtl) ? '2' : '0');
            $out .= ' >>';
            if ($this->sign AND isset($this->signature_data['cert_type'])) {
                if ($this->signature_data['cert_type'] > 0) {
                    $out .= ' /Perms << /DocMDP ' . ($this->sig_obj_id + 1) . ' 0 R >>';
                } else {
                    $out .= ' /Perms << /UR3 ' . ($this->sig_obj_id + 1) . ' 0 R >>';
                }
            }
        }
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        return $oid;
    }
    protected function _putviewerpreferences()
    {
        $out = ' /ViewerPreferences <<';
        if ($this->rtl) {
            $out .= ' /Direction /R2L';
        } else {
            $out .= ' /Direction /L2R';
        }
        if (isset($this->viewer_preferences['HideToolbar']) AND ($this->viewer_preferences['HideToolbar'])) {
            $out .= ' /HideToolbar true';
        }
        if (isset($this->viewer_preferences['HideMenubar']) AND ($this->viewer_preferences['HideMenubar'])) {
            $out .= ' /HideMenubar true';
        }
        if (isset($this->viewer_preferences['HideWindowUI']) AND ($this->viewer_preferences['HideWindowUI'])) {
            $out .= ' /HideWindowUI true';
        }
        if (isset($this->viewer_preferences['FitWindow']) AND ($this->viewer_preferences['FitWindow'])) {
            $out .= ' /FitWindow true';
        }
        if (isset($this->viewer_preferences['CenterWindow']) AND ($this->viewer_preferences['CenterWindow'])) {
            $out .= ' /CenterWindow true';
        }
        if (isset($this->viewer_preferences['DisplayDocTitle']) AND ($this->viewer_preferences['DisplayDocTitle'])) {
            $out .= ' /DisplayDocTitle true';
        }
        if (isset($this->viewer_preferences['NonFullScreenPageMode'])) {
            $out .= ' /NonFullScreenPageMode /' . $this->viewer_preferences['NonFullScreenPageMode'];
        }
        if (isset($this->viewer_preferences['ViewArea'])) {
            $out .= ' /ViewArea /' . $this->viewer_preferences['ViewArea'];
        }
        if (isset($this->viewer_preferences['ViewClip'])) {
            $out .= ' /ViewClip /' . $this->viewer_preferences['ViewClip'];
        }
        if (isset($this->viewer_preferences['PrintArea'])) {
            $out .= ' /PrintArea /' . $this->viewer_preferences['PrintArea'];
        }
        if (isset($this->viewer_preferences['PrintClip'])) {
            $out .= ' /PrintClip /' . $this->viewer_preferences['PrintClip'];
        }
        if (isset($this->viewer_preferences['PrintScaling'])) {
            $out .= ' /PrintScaling /' . $this->viewer_preferences['PrintScaling'];
        }
        if (isset($this->viewer_preferences['Duplex']) AND (!$this->empty_string($this->viewer_preferences['Duplex']))) {
            $out .= ' /Duplex /' . $this->viewer_preferences['Duplex'];
        }
        if (isset($this->viewer_preferences['PickTrayByPDFSize'])) {
            if ($this->viewer_preferences['PickTrayByPDFSize']) {
                $out .= ' /PickTrayByPDFSize true';
            } else {
                $out .= ' /PickTrayByPDFSize false';
            }
        }
        if (isset($this->viewer_preferences['PrintPageRange'])) {
            $PrintPageRangeNum = '';
            foreach ($this->viewer_preferences['PrintPageRange'] as $k => $v) {
                $PrintPageRangeNum .= ' ' . ($v - 1) . '';
            }
            $out .= ' /PrintPageRange [' . substr($PrintPageRangeNum, 1) . ']';
        }
        if (isset($this->viewer_preferences['NumCopies'])) {
            $out .= ' /NumCopies ' . intval($this->viewer_preferences['NumCopies']);
        }
        $out .= ' >>';
        return $out;
    }
    protected function _putheader()
    {
        $this->_out('%PDF-' . $this->PDFVersion);
    }
    protected function _enddoc()
    {
        $this->state = 1;
        $this->_putheader();
        $this->_putpages();
        $this->_putresources();
        if ($this->sign AND isset($this->signature_data['cert_type'])) {
            $out = $this->_getobj($this->sig_obj_id) . "\n";
            $out .= '<< /Type /Annot';
            $out .= ' /Subtype /Widget';
            $out .= ' /Rect [' . $this->signature_appearance['rect'] . ']';
            $out .= ' /P ' . $this->page_obj_id[($this->signature_appearance['page'])] . ' 0 R';
            $out .= ' /F 4';
            $out .= ' /FT /Sig';
            $out .= ' /T ' . $this->_textstring('Signature', $this->sig_obj_id);
            $out .= ' /Ff 0';
            $out .= ' /V ' . ($this->sig_obj_id + 1) . ' 0 R';
            $out .= ' >>';
            $out .= "\n" . 'endobj';
            $this->_out($out);
            $this->_putsignature();
        }
        $objid_info    = $this->_putinfo();
        $objid_catalog = $this->_putcatalog();
        $o             = $this->bufferlen;
        $this->_out('xref');
        $this->_out('0 ' . ($this->n + 1));
        $this->_out('0000000000 65535 f ');
        for ($i = 1; $i <= $this->n; ++$i) {
            if (!isset($this->offsets[$i]) AND ($i > 1)) {
                $this->offsets[$i] = $this->offsets[($i - 1)];
            }
            $this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
        }
        $out = 'trailer <<';
        $out .= ' /Size ' . ($this->n + 1);
        $out .= ' /Root ' . $objid_catalog . ' 0 R';
        $out .= ' /Info ' . $objid_info . ' 0 R';
        if ($this->encrypted) {
            $out .= ' /Encrypt ' . $this->encryptdata['objid'] . ' 0 R';
        }
        $out .= ' /ID [ <' . $this->file_id . '> <' . $this->file_id . '> ]';
        $out .= ' >>';
        $this->_out($out);
        $this->_out('startxref');
        $this->_out($o);
        $this->_out('%%EOF');
        $this->state = 3;
        if ($this->diskcache) {
            foreach ($this->imagekeys as $key) {
                unlink($this->images[$key]);
            }
            foreach ($this->fontkeys as $key) {
                unlink($this->fonts[$key]);
            }
        }
    }
    protected function _beginpage($orientation = '', $format = '')
    {
        ++$this->page;
        $this->setPageBuffer($this->page, '');
        $this->transfmrk[$this->page] = array();
        $this->state                  = 2;
        if ($this->empty_string($orientation)) {
            if (isset($this->CurOrientation)) {
                $orientation = $this->CurOrientation;
            } elseif ($this->fwPt > $this->fhPt) {
                $orientation = 'L';
            } else {
                $orientation = 'P';
            }
        }
        if ($this->empty_string($format)) {
            $this->pagedim[$this->page] = $this->pagedim[($this->page - 1)];
            $this->setPageOrientation($orientation);
        } else {
            $this->setPageFormat($format, $orientation);
        }
        if ($this->rtl) {
            $this->x = $this->w - $this->rMargin;
        } else {
            $this->x = $this->lMargin;
        }
        $this->y = $this->tMargin;
        if (isset($this->newpagegroup[$this->page])) {
            $this->currpagegroup                    = $this->newpagegroup[$this->page];
            $this->pagegroups[$this->currpagegroup] = 1;
        } elseif (isset($this->currpagegroup) AND ($this->currpagegroup > 0)) {
            ++$this->pagegroups[$this->currpagegroup];
        }
    }
    protected function _endpage()
    {
        $this->setVisibility('all');
        $this->state = 1;
    }
    protected function _newobj()
    {
        $this->_out($this->_getobj());
        return $this->n;
    }
    protected function _getobj($objid = '')
    {
        if ($objid === '') {
            ++$this->n;
            $objid = $this->n;
        }
        $this->offsets[$objid] = $this->bufferlen;
        return $objid . ' 0 obj';
    }
    protected function _dounderline($x, $y, $txt)
    {
        $w = $this->GetStringWidth($txt);
        return $this->_dounderlinew($x, $y, $w);
    }
    protected function _dounderlinew($x, $y, $w)
    {
        $linew = -$this->CurrentFont['ut'] / 1000 * $this->FontSizePt;
        return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ((($this->h - $y) * $this->k) + $linew), $w * $this->k, $linew);
    }
    protected function _dolinethrough($x, $y, $txt)
    {
        $w = $this->GetStringWidth($txt);
        return $this->_dolinethroughw($x, $y, $w);
    }
    protected function _dolinethroughw($x, $y, $w)
    {
        $linew = -$this->CurrentFont['ut'] / 1000 * $this->FontSizePt;
        return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ((($this->h - $y) * $this->k) + $linew + ($this->FontSizePt / 3)), $w * $this->k, $linew);
    }
    protected function _dooverline($x, $y, $txt)
    {
        $w = $this->GetStringWidth($txt);
        return $this->_dooverlinew($x, $y, $w);
    }
    protected function _dooverlinew($x, $y, $w)
    {
        $linew = -$this->CurrentFont['ut'] / 1000 * $this->FontSizePt;
        return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, (($this->h - $y + $this->FontAscent) * $this->k) - $linew, $w * $this->k, $linew);
    }
    protected function _freadint($f)
    {
        $a = unpack('Ni', fread($f, 4));
        return $a['i'];
    }
    protected function _escape($s)
    {
        return strtr($s, array(
            ')' => '\\)',
            '(' => '\\(',
            '\\' => '\\\\',
            chr(13) => '\r'
        ));
    }
    protected function _datastring($s, $n = 0)
    {
        if ($n == 0) {
            $n = $this->n;
        }
        $s = $this->_encrypt_data($n, $s);
        return '(' . $this->_escape($s) . ')';
    }
    protected function _datestring($n = 0)
    {
        $current_time = substr_replace(date('YmdHisO'), '\'', (0 - 2), 0) . '\'';
        return $this->_datastring('D:' . $current_time, $n);
    }
    protected function _textstring($s, $n = 0)
    {
        if ($this->isunicode) {
            $s = $this->UTF8ToUTF16BE($s, true);
        }
        return $this->_datastring($s, $n);
    }
    protected function _escapetext($s)
    {
        if ($this->isunicode) {
            if (($this->CurrentFont['type'] == 'core') OR ($this->CurrentFont['type'] == 'TrueType') OR ($this->CurrentFont['type'] == 'Type1')) {
                $s = $this->UTF8ToLatin1($s);
            } else {
                $s = $this->utf8StrRev($s, false, $this->tmprtl);
            }
        }
        return $this->_escape($s);
    }
    protected function _getrawstream($s, $n = 0)
    {
        if ($n <= 0) {
            $n = $this->n;
        }
        return $this->_encrypt_data($n, $s);
    }
    protected function _getstream($s, $n = 0)
    {
        return 'stream' . "\n" . $this->_getrawstream($s, $n) . "\n" . 'endstream';
    }
    protected function _putstream($s, $n = 0)
    {
        $this->_out($this->_getstream($s, $n));
    }
    protected function _out($s)
    {
        if ($this->state == 2) {
            if ($this->inxobj) {
                $this->xobjects[$this->xobjid]['outdata'] .= $s . "\n";
            } elseif ((!$this->InFooter) AND isset($this->footerlen[$this->page]) AND ($this->footerlen[$this->page] > 0)) {
                $pagebuff = $this->getPageBuffer($this->page);
                $page     = substr($pagebuff, 0, -$this->footerlen[$this->page]);
                $footer   = substr($pagebuff, -$this->footerlen[$this->page]);
                $this->setPageBuffer($this->page, $page . $s . "\n" . $footer);
                $this->footerpos[$this->page] += strlen($s . "\n");
            } else {
                $this->setPageBuffer($this->page, $s . "\n", true);
            }
        } else {
            $this->setBuffer($s . "\n");
        }
    }
    protected function UTF8StringToArray($str)
    {
        $strkey = md5($str);
        if (isset($this->cache_UTF8StringToArray[$strkey])) {
            $chrarray = $this->cache_UTF8StringToArray[$strkey]['s'];
            if (!isset($this->cache_UTF8StringToArray[$strkey]['f'][$this->CurrentFont['fontkey']])) {
                if ($this->isunicode) {
                    foreach ($chrarray as $chr) {
                        $this->CurrentFont['subsetchars'][$chr] = true;
                    }
                    $this->setFontSubBuffer($this->CurrentFont['fontkey'], 'subsetchars', $this->CurrentFont['subsetchars']);
                }
                $this->cache_UTF8StringToArray[$strkey]['f'][$this->CurrentFont['fontkey']] = true;
            }
            return $chrarray;
        }
        if ($this->cache_size_UTF8StringToArray >= $this->cache_maxsize_UTF8StringToArray) {
            array_shift($this->cache_UTF8StringToArray);
        }
        $this->cache_UTF8StringToArray[$strkey] = array(
            's' => array(),
            'f' => array()
        );
        ++$this->cache_size_UTF8StringToArray;
        if (!$this->isunicode) {
            $strarr = array();
            $strlen = strlen($str);
            for ($i = 0; $i < $strlen; ++$i) {
                $strarr[] = ord($str{$i});
            }
            $this->cache_UTF8StringToArray[$strkey]['s']                                = $strarr;
            $this->cache_UTF8StringToArray[$strkey]['f'][$this->CurrentFont['fontkey']] = true;
            return $strarr;
        }
        $unichar  = -1;
        $unicode  = array();
        $bytes    = array();
        $numbytes = 1;
        $str .= '';
        $length = strlen($str);
        for ($i = 0; $i < $length; ++$i) {
            $char = ord($str{$i});
            if (count($bytes) == 0) {
                if ($char <= 0x7F) {
                    $unichar  = $char;
                    $numbytes = 1;
                } elseif (($char >> 0x05) == 0x06) {
                    $bytes[]  = ($char - 0xC0) << 0x06;
                    $numbytes = 2;
                } elseif (($char >> 0x04) == 0x0E) {
                    $bytes[]  = ($char - 0xE0) << 0x0C;
                    $numbytes = 3;
                } elseif (($char >> 0x03) == 0x1E) {
                    $bytes[]  = ($char - 0xF0) << 0x12;
                    $numbytes = 4;
                } else {
                    $unichar  = 0xFFFD;
                    $bytes    = array();
                    $numbytes = 1;
                }
            } elseif (($char >> 0x06) == 0x02) {
                $bytes[] = $char - 0x80;
                if (count($bytes) == $numbytes) {
                    $char = $bytes[0];
                    for ($j = 1; $j < $numbytes; ++$j) {
                        $char += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
                    }
                    if ((($char >= 0xD800) AND ($char <= 0xDFFF)) OR ($char >= 0x10FFFF)) {
                        $unichar = 0xFFFD;
                    } else {
                        $unichar = $char;
                    }
                    $bytes    = array();
                    $numbytes = 1;
                }
            } else {
                $unichar  = 0xFFFD;
                $bytes    = array();
                $numbytes = 1;
            }
            if ($unichar >= 0) {
                $unicode[]                                  = $unichar;
                $this->CurrentFont['subsetchars'][$unichar] = true;
                $unichar                                    = -1;
            }
        }
        $this->setFontSubBuffer($this->CurrentFont['fontkey'], 'subsetchars', $this->CurrentFont['subsetchars']);
        $this->cache_UTF8StringToArray[$strkey]['s']                                = $unicode;
        $this->cache_UTF8StringToArray[$strkey]['f'][$this->CurrentFont['fontkey']] = true;
        return $unicode;
    }
    protected function UTF8ToUTF16BE($str, $setbom = false)
    {
        if (!$this->isunicode) {
            return $str;
        }
        $unicode = $this->UTF8StringToArray($str);
        return $this->arrUTF8ToUTF16BE($unicode, $setbom);
    }
    protected function UTF8ToLatin1($str)
    {
        if (!$this->isunicode) {
            return $str;
        }
        $outstr  = '';
        $unicode = $this->UTF8StringToArray($str);
        foreach ($unicode as $char) {
            if ($char < 256) {
                $outstr .= chr($char);
            } elseif (array_key_exists($char, $this->unicode->uni_utf8tolatin)) {
                $outstr .= chr($this->unicode->uni_utf8tolatin[$char]);
            } elseif ($char == 0xFFFD) {
            } else {
                $outstr .= '?';
            }
        }
        return $outstr;
    }
    protected function UTF8ArrToLatin1($unicode)
    {
        if ((!$this->isunicode) OR $this->isUnicodeFont()) {
            return $unicode;
        }
        $outarr = array();
        foreach ($unicode as $char) {
            if ($char < 256) {
                $outarr[] = $char;
            } elseif (array_key_exists($char, $this->unicode->uni_utf8tolatin)) {
                $outarr[] = $this->unicode->uni_utf8tolatin[$char];
            } elseif ($char == 0xFFFD) {
            } else {
                $outarr[] = 63;
            }
        }
        return $outarr;
    }
    protected function arrUTF8ToUTF16BE($unicode, $setbom = false)
    {
        $outstr = '';
        if ($setbom) {
            $outstr .= "\xFE\xFF";
        }
        foreach ($unicode as $char) {
            if ($char == 0x200b) {
            } elseif ($char == 0xFFFD) {
                $outstr .= "\xFF\xFD";
            } elseif ($char < 0x10000) {
                $outstr .= chr($char >> 0x08);
                $outstr .= chr($char & 0xFF);
            } else {
                $char -= 0x10000;
                $w1 = 0xD800 | ($char >> 0x0a);
                $w2 = 0xDC00 | ($char & 0x3FF);
                $outstr .= chr($w1 >> 0x08);
                $outstr .= chr($w1 & 0xFF);
                $outstr .= chr($w2 >> 0x08);
                $outstr .= chr($w2 & 0xFF);
            }
        }
        return $outstr;
    }
    public function setHeaderFont($font)
    {
        $this->header_font = $font;
    }
    public function getHeaderFont()
    {
        return $this->header_font;
    }
    public function setFooterFont($font)
    {
        $this->footer_font = $font;
    }
    public function getFooterFont()
    {
        return $this->footer_font;
    }
    public function setLanguageArray($language)
    {
        $this->l = $language;
        if (isset($this->l['a_meta_dir'])) {
            $this->rtl = $this->l['a_meta_dir'] == 'rtl' ? true : false;
        } else {
            $this->rtl = false;
        }
    }
    public function getPDFData()
    {
        if ($this->state < 3) {
            $this->Close();
        }
        return $this->buffer;
    }
    public function addHtmlLink($url, $name, $fill = false, $firstline = false, $color = '', $style = -1, $firstblock = false)
    {
        if (!$this->empty_string($url) AND ($url{0} == '#')) {
            $lnkdata = explode(',', $url);
            if (isset($lnkdata[0])) {
                $page = intval(substr($lnkdata[0], 1));
                if (empty($page) OR ($page <= 0)) {
                    $page = $this->page;
                }
                if (isset($lnkdata[1]) AND (strlen($lnkdata[1]) > 0)) {
                    $lnky = floatval($lnkdata[1]);
                } else {
                    $lnky = 0;
                }
                $url = $this->AddLink();
                $this->SetLink($url, $lnky, $page);
            }
        }
        $prevcolor = $this->fgcolor;
        $prevstyle = $this->FontStyle;
        if (empty($color)) {
            $this->SetTextColorArray($this->htmlLinkColorArray);
        } else {
            $this->SetTextColorArray($color);
        }
        if ($style == -1) {
            $this->SetFont('', $this->FontStyle . $this->htmlLinkFontStyle);
        } else {
            $this->SetFont('', $this->FontStyle . $style);
        }
        $ret = $this->Write($this->lasth, $name, $url, $fill, '', false, 0, $firstline, $firstblock, 0);
        $this->SetFont('', $prevstyle);
        $this->SetTextColorArray($prevcolor);
        return $ret;
    }
    public function convertHTMLColorToDec($hcolor = '#FFFFFF')
    {
        $returncolor = false;
        $color       = preg_replace('/[\s]*/', '', $hcolor);
        $color       = strtolower($color);
        if (($dotpos = strpos($color, '.')) !== false) {
            $color = substr($color, ($dotpos + 1));
        }
        if (strlen($color) == 0) {
            return false;
        }
        if (substr($color, 0, 3) == 'rgb') {
            $codes       = substr($color, 4);
            $codes       = str_replace(')', '', $codes);
            $returncolor = explode(',', $codes);
            foreach ($returncolor as $key => $val) {
                if (strpos($val, '%') > 0) {
                    $returncolor[$key] = (255 * intval($val) / 100);
                } else {
                    $returncolor[$key] = intval($val);
                }
                $returncolor[$key] = max(0, min(255, $returncolor[$key]));
            }
            return $returncolor;
        }
        if (substr($color, 0, 4) == 'cmyk') {
            $codes       = substr($color, 5);
            $codes       = str_replace(')', '', $codes);
            $returncolor = explode(',', $codes);
            foreach ($returncolor as $key => $val) {
                if (strpos($val, '%') !== false) {
                    $returncolor[$key] = (100 * intval($val) / 100);
                } else {
                    $returncolor[$key] = intval($val);
                }
                $returncolor[$key] = max(0, min(100, $returncolor[$key]));
            }
            return $returncolor;
        }
        if (substr($color, 0, 1) != '#') {
            if (isset($this->webcolor[$color])) {
                $color_code = $this->webcolor[$color];
            } elseif (isset($this->spot_colors[$hcolor])) {
                return array(
                    $this->spot_colors[$hcolor]['c'],
                    $this->spot_colors[$hcolor]['m'],
                    $this->spot_colors[$hcolor]['y'],
                    $this->spot_colors[$hcolor]['k'],
                    $hcolor
                );
            } elseif (isset($this->spotcolor[$color])) {
                return $this->spotcolor[$color];
            } else {
                return false;
            }
        } else {
            $color_code = substr($color, 1);
        }
        switch (strlen($color_code)) {
            case 3: {
                $r                = substr($color_code, 0, 1);
                $g                = substr($color_code, 1, 1);
                $b                = substr($color_code, 2, 1);
                $returncolor      = array();
                $returncolor['R'] = max(0, min(255, hexdec($r . $r)));
                $returncolor['G'] = max(0, min(255, hexdec($g . $g)));
                $returncolor['B'] = max(0, min(255, hexdec($b . $b)));
                break;
            }
            case 6: {
                $returncolor      = array();
                $returncolor['R'] = max(0, min(255, hexdec(substr($color_code, 0, 2))));
                $returncolor['G'] = max(0, min(255, hexdec(substr($color_code, 2, 2))));
                $returncolor['B'] = max(0, min(255, hexdec(substr($color_code, 4, 2))));
                break;
            }
        }
        return $returncolor;
    }
    public function pixelsToUnits($px)
    {
        return ($px / ($this->imgscale * $this->k));
    }
    public function unhtmlentities($text_to_convert)
    {
        return @html_entity_decode($text_to_convert, ENT_QUOTES, $this->encoding);
    }
    protected function getRandomSeed($seed = '')
    {
        $seed .= microtime();
        if (function_exists('openssl_random_pseudo_bytes') AND (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
            $seed .= openssl_random_pseudo_bytes(512);
        } else {
            for ($i = 0; $i < 23; ++$i) {
                $seed .= uniqid('', true);
            }
        }
        $seed .= uniqid('', true);
        $seed .= rand();
        $seed .= getmypid();
        $seed .= __FILE__;
        $seed .= $this->bufferlen;
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $seed .= $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $seed .= $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $seed .= $_SERVER['HTTP_ACCEPT'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $seed .= $_SERVER['HTTP_ACCEPT_ENCODING'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $seed .= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
            $seed .= $_SERVER['HTTP_ACCEPT_CHARSET'];
        }
        $seed .= rand();
        $seed .= uniqid('', true);
        $seed .= microtime();
        return $seed;
    }
    protected function _objectkey($n)
    {
        $objkey = $this->encryptdata['key'] . pack('VXxx', $n);
        if ($this->encryptdata['mode'] == 2) {
            $objkey .= "\x73\x41\x6C\x54";
        }
        $objkey = substr($this->_md5_16($objkey), 0, (($this->encryptdata['Length'] / 8) + 5));
        $objkey = substr($objkey, 0, 16);
        return $objkey;
    }
    protected function _encrypt_data($n, $s)
    {
        if (!$this->encrypted) {
            return $s;
        }
        switch ($this->encryptdata['mode']) {
            case 0:
            case 1: {
                $s = $this->_RC4($this->_objectkey($n), $s);
                break;
            }
            case 2: {
                $s = $this->_AES($this->_objectkey($n), $s);
                break;
            }
            case 3: {
                $s = $this->_AES($this->encryptdata['key'], $s);
                break;
            }
        }
        return $s;
    }
    protected function _putencryption()
    {
        if (!$this->encrypted) {
            return;
        }
        $this->encryptdata['objid'] = $this->_newobj();
        $out                        = '<<';
        if (!isset($this->encryptdata['Filter']) OR empty($this->encryptdata['Filter'])) {
            $this->encryptdata['Filter'] = 'Standard';
        }
        $out .= ' /Filter /' . $this->encryptdata['Filter'];
        if (isset($this->encryptdata['SubFilter']) AND !empty($this->encryptdata['SubFilter'])) {
            $out .= ' /SubFilter /' . $this->encryptdata['SubFilter'];
        }
        if (!isset($this->encryptdata['V']) OR empty($this->encryptdata['V'])) {
            $this->encryptdata['V'] = 1;
        }
        $out .= ' /V ' . $this->encryptdata['V'];
        if (isset($this->encryptdata['Length']) AND !empty($this->encryptdata['Length'])) {
            $out .= ' /Length ' . $this->encryptdata['Length'];
        } else {
            $out .= ' /Length 40';
        }
        if ($this->encryptdata['V'] >= 4) {
            if (!isset($this->encryptdata['StmF']) OR empty($this->encryptdata['StmF'])) {
                $this->encryptdata['StmF'] = 'Identity';
            }
            if (!isset($this->encryptdata['StrF']) OR empty($this->encryptdata['StrF'])) {
                $this->encryptdata['StrF'] = 'Identity';
            }
            if (isset($this->encryptdata['CF']) AND !empty($this->encryptdata['CF'])) {
                $out .= ' /CF <<';
                $out .= ' /' . $this->encryptdata['StmF'] . ' <<';
                $out .= ' /Type /CryptFilter';
                if (isset($this->encryptdata['CF']['CFM']) AND !empty($this->encryptdata['CF']['CFM'])) {
                    $out .= ' /CFM /' . $this->encryptdata['CF']['CFM'];
                    if ($this->encryptdata['pubkey']) {
                        $out .= ' /Recipients [';
                        foreach ($this->encryptdata['Recipients'] as $rec) {
                            $out .= ' <' . $rec . '>';
                        }
                        $out .= ' ]';
                        if (isset($this->encryptdata['CF']['EncryptMetadata']) AND (!$this->encryptdata['CF']['EncryptMetadata'])) {
                            $out .= ' /EncryptMetadata false';
                        } else {
                            $out .= ' /EncryptMetadata true';
                        }
                    }
                } else {
                    $out .= ' /CFM /None';
                }
                if (isset($this->encryptdata['CF']['AuthEvent']) AND !empty($this->encryptdata['CF']['AuthEvent'])) {
                    $out .= ' /AuthEvent /' . $this->encryptdata['CF']['AuthEvent'];
                } else {
                    $out .= ' /AuthEvent /DocOpen';
                }
                if (isset($this->encryptdata['CF']['Length']) AND !empty($this->encryptdata['CF']['Length'])) {
                    $out .= ' /Length ' . $this->encryptdata['CF']['Length'];
                }
                $out .= ' >> >>';
            }
            $out .= ' /StmF /' . $this->encryptdata['StmF'];
            $out .= ' /StrF /' . $this->encryptdata['StrF'];
            if (isset($this->encryptdata['EFF']) AND !empty($this->encryptdata['EFF'])) {
                $out .= ' /EFF /' . $this->encryptdata[''];
            }
        }
        if ($this->encryptdata['pubkey']) {
            if (($this->encryptdata['V'] < 4) AND isset($this->encryptdata['Recipients']) AND !empty($this->encryptdata['Recipients'])) {
                $out .= ' /Recipients [';
                foreach ($this->encryptdata['Recipients'] as $rec) {
                    $out .= ' <' . $rec . '>';
                }
                $out .= ' ]';
            }
        } else {
            $out .= ' /R';
            if ($this->encryptdata['V'] == 5) {
                $out .= ' 5';
                $out .= ' /OE (' . $this->_escape($this->encryptdata['OE']) . ')';
                $out .= ' /UE (' . $this->_escape($this->encryptdata['UE']) . ')';
                $out .= ' /Perms (' . $this->_escape($this->encryptdata['perms']) . ')';
            } elseif ($this->encryptdata['V'] == 4) {
                $out .= ' 4';
            } elseif ($this->encryptdata['V'] < 2) {
                $out .= ' 2';
            } else {
                $out .= ' 3';
            }
            $out .= ' /O (' . $this->_escape($this->encryptdata['O']) . ')';
            $out .= ' /U (' . $this->_escape($this->encryptdata['U']) . ')';
            $out .= ' /P ' . $this->encryptdata['P'];
            if (isset($this->encryptdata['EncryptMetadata']) AND (!$this->encryptdata['EncryptMetadata'])) {
                $out .= ' /EncryptMetadata false';
            } else {
                $out .= ' /EncryptMetadata true';
            }
        }
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
    }
    protected function _RC4($key, $text)
    {
        if (function_exists('mcrypt_decrypt') AND ($out = @mcrypt_decrypt(MCRYPT_ARCFOUR, $key, $text, MCRYPT_MODE_STREAM, ''))) {
            return $out;
        }
        if ($this->last_enc_key != $key) {
            $k   = str_repeat($key, ((256 / strlen($key)) + 1));
            $rc4 = range(0, 255);
            $j   = 0;
            for ($i = 0; $i < 256; ++$i) {
                $t       = $rc4[$i];
                $j       = ($j + $t + ord($k{$i})) % 256;
                $rc4[$i] = $rc4[$j];
                $rc4[$j] = $t;
            }
            $this->last_enc_key   = $key;
            $this->last_enc_key_c = $rc4;
        } else {
            $rc4 = $this->last_enc_key_c;
        }
        $len = strlen($text);
        $a   = 0;
        $b   = 0;
        $out = '';
        for ($i = 0; $i < $len; ++$i) {
            $a       = ($a + 1) % 256;
            $t       = $rc4[$a];
            $b       = ($b + $t) % 256;
            $rc4[$a] = $rc4[$b];
            $rc4[$b] = $t;
            $k       = $rc4[($rc4[$a] + $rc4[$b]) % 256];
            $out .= chr(ord($text{$i}) ^ $k);
        }
        return $out;
    }
    protected function _AES($key, $text)
    {
        $padding = 16 - (strlen($text) % 16);
        $text .= str_repeat(chr($padding), $padding);
        $iv   = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        $text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
        $text = $iv . $text;
        return $text;
    }
    protected function _md5_16($str)
    {
        return pack('H*', md5($str));
    }
    protected function _Uvalue()
    {
        if ($this->encryptdata['mode'] == 0) {
            return $this->_RC4($this->encryptdata['key'], $this->enc_padding);
        } elseif ($this->encryptdata['mode'] < 3) {
            $tmp = $this->_md5_16($this->enc_padding . $this->encryptdata['fileid']);
            $enc = $this->_RC4($this->encryptdata['key'], $tmp);
            $len = strlen($tmp);
            for ($i = 1; $i <= 19; ++$i) {
                $ek = '';
                for ($j = 0; $j < $len; ++$j) {
                    $ek .= chr(ord($this->encryptdata['key']{$j}) ^ $i);
                }
                $enc = $this->_RC4($ek, $enc);
            }
            $enc .= str_repeat("\x00", 16);
            return substr($enc, 0, 32);
        } elseif ($this->encryptdata['mode'] == 3) {
            $seed                     = $this->_md5_16($this->getRandomSeed());
            $this->encryptdata['UVS'] = substr($seed, 0, 8);
            $this->encryptdata['UKS'] = substr($seed, 8, 16);
            return hash('sha256', $this->encryptdata['user_password'] . $this->encryptdata['UVS'], true) . $this->encryptdata['UVS'] . $this->encryptdata['UKS'];
        }
    }
    protected function _UEvalue()
    {
        $hashkey = hash('sha256', $this->encryptdata['user_password'] . $this->encryptdata['UKS'], true);
        $iv      = str_repeat("\x00", mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $hashkey, $this->encryptdata['key'], MCRYPT_MODE_CBC, $iv);
    }
    protected function _Ovalue()
    {
        if ($this->encryptdata['mode'] < 3) {
            $tmp = $this->_md5_16($this->encryptdata['owner_password']);
            if ($this->encryptdata['mode'] > 0) {
                for ($i = 0; $i < 50; ++$i) {
                    $tmp = $this->_md5_16($tmp);
                }
            }
            $owner_key = substr($tmp, 0, ($this->encryptdata['Length'] / 8));
            $enc       = $this->_RC4($owner_key, $this->encryptdata['user_password']);
            if ($this->encryptdata['mode'] > 0) {
                $len = strlen($owner_key);
                for ($i = 1; $i <= 19; ++$i) {
                    $ek = '';
                    for ($j = 0; $j < $len; ++$j) {
                        $ek .= chr(ord($owner_key{$j}) ^ $i);
                    }
                    $enc = $this->_RC4($ek, $enc);
                }
            }
            return $enc;
        } elseif ($this->encryptdata['mode'] == 3) {
            $seed                     = $this->_md5_16($this->getRandomSeed());
            $this->encryptdata['OVS'] = substr($seed, 0, 8);
            $this->encryptdata['OKS'] = substr($seed, 8, 16);
            return hash('sha256', $this->encryptdata['owner_password'] . $this->encryptdata['OVS'] . $this->encryptdata['U'], true) . $this->encryptdata['OVS'] . $this->encryptdata['OKS'];
        }
    }
    protected function _OEvalue()
    {
        $hashkey = hash('sha256', $this->encryptdata['owner_password'] . $this->encryptdata['OKS'] . $this->encryptdata['U'], true);
        $iv      = str_repeat("\x00", mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $hashkey, $this->encryptdata['key'], MCRYPT_MODE_CBC, $iv);
    }
    protected function _fixAES256Password($password)
    {
        $psw       = '';
        $psw_array = $this->utf8Bidi($this->UTF8StringToArray($password), $password, $this->rtl);
        foreach ($psw_array as $c) {
            $psw .= $this->unichr($c);
        }
        return substr($psw, 0, 127);
    }
    protected function _generateencryptionkey()
    {
        $keybytelen = ($this->encryptdata['Length'] / 8);
        if (!$this->encryptdata['pubkey']) {
            if ($this->encryptdata['mode'] == 3) {
                $this->encryptdata['key']            = substr(hash('sha256', $this->getRandomSeed(), true), 0, $keybytelen);
                $this->encryptdata['user_password']  = $this->_fixAES256Password($this->encryptdata['user_password']);
                $this->encryptdata['owner_password'] = $this->_fixAES256Password($this->encryptdata['owner_password']);
                $this->encryptdata['U']              = $this->_Uvalue();
                $this->encryptdata['UE']             = $this->_UEvalue();
                $this->encryptdata['O']              = $this->_Ovalue();
                $this->encryptdata['OE']             = $this->_OEvalue();
                $this->encryptdata['P']              = $this->encryptdata['protection'];
                $perms                               = $this->getEncPermissionsString($this->encryptdata['protection']);
                $perms .= chr(255) . chr(255) . chr(255) . chr(255);
                if (isset($this->encryptdata['CF']['EncryptMetadata']) AND (!$this->encryptdata['CF']['EncryptMetadata'])) {
                    $perms .= 'F';
                } else {
                    $perms .= 'T';
                }
                $perms .= 'adb';
                $perms .= 'nick';
                $iv                         = str_repeat("\x00", mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB));
                $this->encryptdata['perms'] = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->encryptdata['key'], $perms, MCRYPT_MODE_ECB, $iv);
            } else {
                $this->encryptdata['user_password']  = substr($this->encryptdata['user_password'] . $this->enc_padding, 0, 32);
                $this->encryptdata['owner_password'] = substr($this->encryptdata['owner_password'] . $this->enc_padding, 0, 32);
                $this->encryptdata['O']              = $this->_Ovalue();
                $permissions                         = $this->getEncPermissionsString($this->encryptdata['protection']);
                $tmp                                 = $this->_md5_16($this->encryptdata['user_password'] . $this->encryptdata['O'] . $permissions . $this->encryptdata['fileid']);
                if ($this->encryptdata['mode'] > 0) {
                    for ($i = 0; $i < 50; ++$i) {
                        $tmp = $this->_md5_16(substr($tmp, 0, $keybytelen));
                    }
                }
                $this->encryptdata['key'] = substr($tmp, 0, $keybytelen);
                $this->encryptdata['U']   = $this->_Uvalue();
                $this->encryptdata['P']   = $this->encryptdata['protection'];
            }
        } else {
            $seed            = sha1($this->getRandomSeed(), true);
            $recipient_bytes = '';
            foreach ($this->encryptdata['pubkeys'] as $pubkey) {
                if (isset($pubkey['p'])) {
                    $pkprotection = $this->getUserPermissionCode($pubkey['p'], $this->encryptdata['mode']);
                } else {
                    $pkprotection = $this->encryptdata['protection'];
                }
                $pkpermissions = $this->getEncPermissionsString($pkprotection);
                $envelope      = $seed . $pkpermissions;
                $tempkeyfile   = tempnam(K_PATH_CACHE, 'tmpkey_');
                $f             = fopen($tempkeyfile, 'wb');
                if (!$f) {
                    $this->Error('Unable to create temporary key file: ' . $tempkeyfile);
                }
                $envelope_lenght = strlen($envelope);
                fwrite($f, $envelope, $envelope_lenght);
                fclose($f);
                $tempencfile = tempnam(K_PATH_CACHE, 'tmpenc_');
                if (!openssl_pkcs7_encrypt($tempkeyfile, $tempencfile, $pubkey['c'], array(), PKCS7_DETACHED | PKCS7_BINARY)) {
                    $this->Error('Unable to encrypt the file: ' . $tempkeyfile);
                }
                unlink($tempkeyfile);
                $signature = file_get_contents($tempencfile, false, null, $envelope_lenght);
                unlink($tempencfile);
                $signature = substr($signature, strpos($signature, 'Content-Disposition'));
                $tmparr    = explode("\n\n", $signature);
                $signature = trim($tmparr[1]);
                unset($tmparr);
                $signature                         = base64_decode($signature);
                $hexsignature                      = current(unpack('H*', $signature));
                $this->encryptdata['Recipients'][] = $hexsignature;
                $recipient_bytes .= $signature;
            }
            if ($this->encryptdata['mode'] == 3) {
                $this->encryptdata['key'] = substr(hash('sha256', $seed . $recipient_bytes, true), 0, $keybytelen);
            } else {
                $this->encryptdata['key'] = substr(sha1($seed . $recipient_bytes, true), 0, $keybytelen);
            }
        }
    }
    protected function getUserPermissionCode($permissions, $mode = 0)
    {
        $options    = array(
            'owner' => 2,
            'print' => 4,
            'modify' => 8,
            'copy' => 16,
            'annot-forms' => 32,
            'fill-forms' => 256,
            'extract' => 512,
            'assemble' => 1024,
            'print-high' => 2048
        );
        $protection = 2147422012;
        foreach ($permissions as $permission) {
            if (!isset($options[$permission])) {
                $this->Error('Incorrect permission: ' . $permission);
            }
            if (($mode > 0) OR ($options[$permission] <= 32)) {
                if ($options[$permission] == 2) {
                    $protection += $options[$permission];
                } else {
                    $protection -= $options[$permission];
                }
            }
        }
        return $protection;
    }
    public function SetProtection($permissions = array('print', 'modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'), $user_pass = '', $owner_pass = null, $mode = 0, $pubkeys = null)
    {
        $this->encryptdata['protection'] = $this->getUserPermissionCode($permissions, $mode);
        if (($pubkeys !== null) AND (is_array($pubkeys))) {
            $this->encryptdata['pubkeys'] = $pubkeys;
            if ($mode == 0) {
                $mode = 1;
            }
            if (!function_exists('openssl_pkcs7_encrypt')) {
                $this->Error('Public-Key Security requires openssl library.');
            }
            $this->encryptdata['pubkey'] = true;
            $this->encryptdata['Filter'] = 'Adobe.PubSec';
            $this->encryptdata['StmF']   = 'DefaultCryptFilter';
            $this->encryptdata['StrF']   = 'DefaultCryptFilter';
        } else {
            $this->encryptdata['pubkey'] = false;
            $this->encryptdata['Filter'] = 'Standard';
            $this->encryptdata['StmF']   = 'StdCF';
            $this->encryptdata['StrF']   = 'StdCF';
        }
        if ($mode > 1) {
            if (!extension_loaded('mcrypt')) {
                $this->Error('AES encryption requires mcrypt library (http://www.php.net/manual/en/mcrypt.requirements.php).');
            }
            if (mcrypt_get_cipher_name(MCRYPT_RIJNDAEL_128) === false) {
                $this->Error('AES encryption requires MCRYPT_RIJNDAEL_128 cypher.');
            }
            if (($mode == 3) AND !function_exists('hash')) {
                $this->Error('AES 256 encryption requires HASH Message Digest Framework (http://www.php.net/manual/en/book.hash.php).');
            }
        }
        if ($owner_pass === null) {
            $owner_pass = md5($this->getRandomSeed());
        }
        $this->encryptdata['user_password']  = $user_pass;
        $this->encryptdata['owner_password'] = $owner_pass;
        $this->encryptdata['mode']           = $mode;
        switch ($mode) {
            case 0: {
                $this->encryptdata['V']         = 1;
                $this->encryptdata['Length']    = 40;
                $this->encryptdata['CF']['CFM'] = 'V2';
                break;
            }
            case 1: {
                $this->encryptdata['V']         = 2;
                $this->encryptdata['Length']    = 128;
                $this->encryptdata['CF']['CFM'] = 'V2';
                if ($this->encryptdata['pubkey']) {
                    $this->encryptdata['SubFilter']  = 'adbe.pkcs7.s4';
                    $this->encryptdata['Recipients'] = array();
                }
                break;
            }
            case 2: {
                $this->encryptdata['V']            = 4;
                $this->encryptdata['Length']       = 128;
                $this->encryptdata['CF']['CFM']    = 'AESV2';
                $this->encryptdata['CF']['Length'] = 128;
                if ($this->encryptdata['pubkey']) {
                    $this->encryptdata['SubFilter']  = 'adbe.pkcs7.s5';
                    $this->encryptdata['Recipients'] = array();
                }
                break;
            }
            case 3: {
                $this->encryptdata['V']            = 5;
                $this->encryptdata['Length']       = 256;
                $this->encryptdata['CF']['CFM']    = 'AESV3';
                $this->encryptdata['CF']['Length'] = 256;
                if ($this->encryptdata['pubkey']) {
                    $this->encryptdata['SubFilter']  = 'adbe.pkcs7.s5';
                    $this->encryptdata['Recipients'] = array();
                }
                break;
            }
        }
        $this->encrypted             = true;
        $this->encryptdata['fileid'] = $this->convertHexStringToString($this->file_id);
        $this->_generateencryptionkey();
    }
    protected function convertHexStringToString($bs)
    {
        $string   = '';
        $bslenght = strlen($bs);
        if (($bslenght % 2) != 0) {
            $bs .= '0';
            ++$bslenght;
        }
        for ($i = 0; $i < $bslenght; $i += 2) {
            $string .= chr(hexdec($bs{$i} . $bs{($i + 1)}));
        }
        return $string;
    }
    protected function convertStringToHexString($s)
    {
        $bs    = '';
        $chars = preg_split('//', $s, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $c) {
            $bs .= sprintf('%02s', dechex(ord($c)));
        }
        return $bs;
    }
    protected function getEncPermissionsString($protection)
    {
        $binprot = sprintf('%032b', $protection);
        $str     = chr(bindec(substr($binprot, 24, 8)));
        $str .= chr(bindec(substr($binprot, 16, 8)));
        $str .= chr(bindec(substr($binprot, 8, 8)));
        $str .= chr(bindec(substr($binprot, 0, 8)));
        return $str;
    }
    public function StartTransform()
    {
        $this->_out('q');
        if ($this->inxobj) {
            $this->xobjects[$this->xobjid]['transfmrk'][] = strlen($this->xobjects[$this->xobjid]['outdata']);
        } else {
            $this->transfmrk[$this->page][] = $this->pagelen[$this->page];
        }
        ++$this->transfmatrix_key;
        $this->transfmatrix[$this->transfmatrix_key] = array();
    }
    public function StopTransform()
    {
        $this->_out('Q');
        if (isset($this->transfmatrix[$this->transfmatrix_key])) {
            array_pop($this->transfmatrix[$this->transfmatrix_key]);
            --$this->transfmatrix_key;
        }
        if ($this->inxobj) {
            array_pop($this->xobjects[$this->xobjid]['transfmrk']);
        } else {
            array_pop($this->transfmrk[$this->page]);
        }
    }
    public function ScaleX($s_x, $x = '', $y = '')
    {
        $this->Scale($s_x, 100, $x, $y);
    }
    public function ScaleY($s_y, $x = '', $y = '')
    {
        $this->Scale(100, $s_y, $x, $y);
    }
    public function ScaleXY($s, $x = '', $y = '')
    {
        $this->Scale($s, $s, $x, $y);
    }
    public function Scale($s_x, $s_y, $x = '', $y = '')
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        if (($s_x == 0) OR ($s_y == 0)) {
            $this->Error('Please do not use values equal to zero for scaling');
        }
        $y = ($this->h - $y) * $this->k;
        $x *= $this->k;
        $s_x /= 100;
        $s_y /= 100;
        $tm    = array();
        $tm[0] = $s_x;
        $tm[1] = 0;
        $tm[2] = 0;
        $tm[3] = $s_y;
        $tm[4] = $x * (1 - $s_x);
        $tm[5] = $y * (1 - $s_y);
        $this->Transform($tm);
    }
    public function MirrorH($x = '')
    {
        $this->Scale(-100, 100, $x);
    }
    public function MirrorV($y = '')
    {
        $this->Scale(100, -100, '', $y);
    }
    public function MirrorP($x = '', $y = '')
    {
        $this->Scale(-100, -100, $x, $y);
    }
    public function MirrorL($angle = 0, $x = '', $y = '')
    {
        $this->Scale(-100, 100, $x, $y);
        $this->Rotate(-2 * ($angle - 90), $x, $y);
    }
    public function TranslateX($t_x)
    {
        $this->Translate($t_x, 0);
    }
    public function TranslateY($t_y)
    {
        $this->Translate(0, $t_y);
    }
    public function Translate($t_x, $t_y)
    {
        $tm    = array();
        $tm[0] = 1;
        $tm[1] = 0;
        $tm[2] = 0;
        $tm[3] = 1;
        $tm[4] = $t_x * $this->k;
        $tm[5] = -$t_y * $this->k;
        $this->Transform($tm);
    }
    public function Rotate($angle, $x = '', $y = '')
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        $y = ($this->h - $y) * $this->k;
        $x *= $this->k;
        $tm    = array();
        $tm[0] = cos(deg2rad($angle));
        $tm[1] = sin(deg2rad($angle));
        $tm[2] = -$tm[1];
        $tm[3] = $tm[0];
        $tm[4] = $x + ($tm[1] * $y) - ($tm[0] * $x);
        $tm[5] = $y - ($tm[0] * $y) - ($tm[1] * $x);
        $this->Transform($tm);
    }
    public function SkewX($angle_x, $x = '', $y = '')
    {
        $this->Skew($angle_x, 0, $x, $y);
    }
    public function SkewY($angle_y, $x = '', $y = '')
    {
        $this->Skew(0, $angle_y, $x, $y);
    }
    public function Skew($angle_x, $angle_y, $x = '', $y = '')
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        if (($angle_x <= -90) OR ($angle_x >= 90) OR ($angle_y <= -90) OR ($angle_y >= 90)) {
            $this->Error('Please use values between -90 and +90 degrees for Skewing.');
        }
        $x *= $this->k;
        $y     = ($this->h - $y) * $this->k;
        $tm    = array();
        $tm[0] = 1;
        $tm[1] = tan(deg2rad($angle_y));
        $tm[2] = tan(deg2rad($angle_x));
        $tm[3] = 1;
        $tm[4] = -$tm[2] * $y;
        $tm[5] = -$tm[1] * $x;
        $this->Transform($tm);
    }
    protected function Transform($tm)
    {
        $this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $tm[0], $tm[1], $tm[2], $tm[3], $tm[4], $tm[5]));
        $this->transfmatrix[$this->transfmatrix_key][] = array(
            'a' => $tm[0],
            'b' => $tm[1],
            'c' => $tm[2],
            'd' => $tm[3],
            'e' => $tm[4],
            'f' => $tm[5]
        );
        if ($this->inxobj) {
            if (end($this->xobjects[$this->xobjid]['transfmrk']) !== false) {
                $key                                              = key($this->xobjects[$this->xobjid]['transfmrk']);
                $this->xobjects[$this->xobjid]['transfmrk'][$key] = strlen($this->xobjects[$this->xobjid]['outdata']);
            }
        } elseif (end($this->transfmrk[$this->page]) !== false) {
            $key                                = key($this->transfmrk[$this->page]);
            $this->transfmrk[$this->page][$key] = $this->pagelen[$this->page];
        }
    }
    public function SetLineWidth($width)
    {
        $this->LineWidth      = $width;
        $this->linestyleWidth = sprintf('%.2F w', ($width * $this->k));
        if ($this->page > 0) {
            $this->_out($this->linestyleWidth);
        }
    }
    public function GetLineWidth()
    {
        return $this->LineWidth;
    }
    public function SetLineStyle($style, $ret = false)
    {
        $s = '';
        if (!is_array($style)) {
            return;
        }
        if (isset($style['width'])) {
            $this->LineWidth      = $style['width'];
            $this->linestyleWidth = sprintf('%.2F w', ($style['width'] * $this->k));
            $s .= $this->linestyleWidth . ' ';
        }
        if (isset($style['cap'])) {
            $ca = array(
                'butt' => 0,
                'round' => 1,
                'square' => 2
            );
            if (isset($ca[$style['cap']])) {
                $this->linestyleCap = $ca[$style['cap']] . ' J';
                $s .= $this->linestyleCap . ' ';
            }
        }
        if (isset($style['join'])) {
            $ja = array(
                'miter' => 0,
                'round' => 1,
                'bevel' => 2
            );
            if (isset($ja[$style['join']])) {
                $this->linestyleJoin = $ja[$style['join']] . ' j';
                $s .= $this->linestyleJoin . ' ';
            }
        }
        if (isset($style['dash'])) {
            $dash_string = '';
            if ($style['dash']) {
                if (preg_match('/^.+,/', $style['dash']) > 0) {
                    $tab = explode(',', $style['dash']);
                } else {
                    $tab = array(
                        $style['dash']
                    );
                }
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i) {
                        $dash_string .= ' ';
                    }
                    $dash_string .= sprintf('%.2F', $v);
                }
            }
            if (!isset($style['phase']) OR !$style['dash']) {
                $style['phase'] = 0;
            }
            $this->linestyleDash = sprintf('[%s] %.2F d', $dash_string, $style['phase']);
            $s .= $this->linestyleDash . ' ';
        }
        if (isset($style['color'])) {
            $s .= $this->SetDrawColorArray($style['color'], true) . ' ';
        }
        if (!$ret) {
            $this->_out($s);
        }
        return $s;
    }
    protected function _outPoint($x, $y)
    {
        $this->_out(sprintf('%.2F %.2F m', $x * $this->k, ($this->h - $y) * $this->k));
    }
    protected function _outLine($x, $y)
    {
        $this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
    }
    protected function _outRect($x, $y, $w, $h, $op)
    {
        $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x * $this->k, ($this->h - $y) * $this->k, $w * $this->k, -$h * $this->k, $op));
    }
    protected function _outCurve($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }
    protected function _outCurveV($x2, $y2, $x3, $y3)
    {
        $this->_out(sprintf('%.2F %.2F %.2F %.2F v', $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }
    protected function _outCurveY($x1, $y1, $x3, $y3)
    {
        $this->_out(sprintf('%.2F %.2F %.2F %.2F y', $x1 * $this->k, ($this->h - $y1) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }
    public function Line($x1, $y1, $x2, $y2, $style = array())
    {
        if (is_array($style)) {
            $this->SetLineStyle($style);
        }
        $this->_outPoint($x1, $y1);
        $this->_outLine($x2, $y2);
        $this->_out('S');
    }
    public function Rect($x, $y, $w, $h, $style = '', $border_style = array(), $fill_color = array())
    {
        if (!(false === strpos($style, 'F')) AND !empty($fill_color)) {
            $this->SetFillColorArray($fill_color);
        }
        $op = $this->getPathPaintOperator($style);
        if ((!$border_style) OR (isset($border_style['all']))) {
            if (isset($border_style['all']) AND $border_style['all']) {
                $this->SetLineStyle($border_style['all']);
                $border_style = array();
            }
        }
        $this->_outRect($x, $y, $w, $h, $op);
        if ($border_style) {
            $border_style2 = array();
            foreach ($border_style as $line => $value) {
                $length = strlen($line);
                for ($i = 0; $i < $length; ++$i) {
                    $border_style2[$line[$i]] = $value;
                }
            }
            $border_style = $border_style2;
            if (isset($border_style['L']) AND $border_style['L']) {
                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
            }
            if (isset($border_style['T']) AND $border_style['T']) {
                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
            }
            if (isset($border_style['R']) AND $border_style['R']) {
                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
            }
            if (isset($border_style['B']) AND $border_style['B']) {
                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
            }
        }
    }
    public function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = array(), $fill_color = array())
    {
        if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
            $this->SetFillColorArray($fill_color);
        }
        $op = $this->getPathPaintOperator($style);
        if ($line_style) {
            $this->SetLineStyle($line_style);
        }
        $this->_outPoint($x0, $y0);
        $this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
        $this->_out($op);
    }
    public function Polycurve($x0, $y0, $segments, $style = '', $line_style = array(), $fill_color = array())
    {
        if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
            $this->SetFillColorArray($fill_color);
        }
        $op = $this->getPathPaintOperator($style);
        if ($op == 'f') {
            $line_style = array();
        }
        if ($line_style) {
            $this->SetLineStyle($line_style);
        }
        $this->_outPoint($x0, $y0);
        foreach ($segments as $segment) {
            list($x1, $y1, $x2, $y2, $x3, $y3) = $segment;
            $this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
        }
        $this->_out($op);
    }
    public function Ellipse($x0, $y0, $rx, $ry = '', $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = array(), $fill_color = array(), $nc = 2)
    {
        if ($this->empty_string($ry) OR ($ry == 0)) {
            $ry = $rx;
        }
        if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
            $this->SetFillColorArray($fill_color);
        }
        $op = $this->getPathPaintOperator($style);
        if ($op == 'f') {
            $line_style = array();
        }
        if ($line_style) {
            $this->SetLineStyle($line_style);
        }
        $this->_outellipticalarc($x0, $y0, $rx, $ry, $angle, $astart, $afinish, false, $nc, true, true, false);
        $this->_out($op);
    }
    protected function _outellipticalarc($xc, $yc, $rx, $ry, $xang = 0, $angs = 0, $angf = 360, $pie = false, $nc = 2, $startpoint = true, $ccw = true, $svg = false)
    {
        $k = $this->k;
        if ($nc < 2) {
            $nc = 2;
        }
        $xmin = 2147483647;
        $ymin = 2147483647;
        $xmax = 0;
        $ymax = 0;
        if ($pie) {
            $this->_outPoint($xc, $yc);
        }
        $xang = deg2rad((float) $xang);
        $angs = deg2rad((float) $angs);
        $angf = deg2rad((float) $angf);
        if ($svg) {
            $as = $angs;
            $af = $angf;
        } else {
            $as = atan2((sin($angs) / $ry), (cos($angs) / $rx));
            $af = atan2((sin($angf) / $ry), (cos($angf) / $rx));
        }
        if ($as < 0) {
            $as += (2 * M_PI);
        }
        if ($af < 0) {
            $af += (2 * M_PI);
        }
        if ($ccw AND ($as > $af)) {
            $as -= (2 * M_PI);
        } elseif (!$ccw AND ($as < $af)) {
            $af -= (2 * M_PI);
        }
        $total_angle = ($af - $as);
        if ($nc < 2) {
            $nc = 2;
        }
        $nc *= (2 * abs($total_angle) / M_PI);
        $nc       = round($nc) + 1;
        $arcang   = ($total_angle / $nc);
        $x0       = $xc;
        $y0       = ($this->h - $yc);
        $ang      = $as;
        $alpha    = sin($arcang) * ((sqrt(4 + (3 * pow(tan(($arcang) / 2), 2))) - 1) / 3);
        $cos_xang = cos($xang);
        $sin_xang = sin($xang);
        $cos_ang  = cos($ang);
        $sin_ang  = sin($ang);
        $px1      = $x0 + ($rx * $cos_xang * $cos_ang) - ($ry * $sin_xang * $sin_ang);
        $py1      = $y0 + ($rx * $sin_xang * $cos_ang) + ($ry * $cos_xang * $sin_ang);
        $qx1      = ($alpha * ((-$rx * $cos_xang * $sin_ang) - ($ry * $sin_xang * $cos_ang)));
        $qy1      = ($alpha * ((-$rx * $sin_xang * $sin_ang) + ($ry * $cos_xang * $cos_ang)));
        if ($pie) {
            $this->_outLine($px1, $this->h - $py1);
        } elseif ($startpoint) {
            $this->_outPoint($px1, $this->h - $py1);
        }
        for ($i = 1; $i <= $nc; ++$i) {
            $ang = $as + ($i * $arcang);
            if ($i == $nc) {
                $ang = $af;
            }
            $cos_ang = cos($ang);
            $sin_ang = sin($ang);
            $px2     = $x0 + ($rx * $cos_xang * $cos_ang) - ($ry * $sin_xang * $sin_ang);
            $py2     = $y0 + ($rx * $sin_xang * $cos_ang) + ($ry * $cos_xang * $sin_ang);
            $qx2     = ($alpha * ((-$rx * $cos_xang * $sin_ang) - ($ry * $sin_xang * $cos_ang)));
            $qy2     = ($alpha * ((-$rx * $sin_xang * $sin_ang) + ($ry * $cos_xang * $cos_ang)));
            $cx1     = ($px1 + $qx1);
            $cy1     = ($this->h - ($py1 + $qy1));
            $cx2     = ($px2 - $qx2);
            $cy2     = ($this->h - ($py2 - $qy2));
            $cx3     = $px2;
            $cy3     = ($this->h - $py2);
            $this->_outCurve($cx1, $cy1, $cx2, $cy2, $cx3, $cy3);
            $xmin = min($xmin, $cx1, $cx2, $cx3);
            $ymin = min($ymin, $cy1, $cy2, $cy3);
            $xmax = max($xmax, $cx1, $cx2, $cx3);
            $ymax = max($ymax, $cy1, $cy2, $cy3);
            $px1  = $px2;
            $py1  = $py2;
            $qx1  = $qx2;
            $qy1  = $qy2;
        }
        if ($pie) {
            $this->_outLine($xc, $yc);
            $xmin = min($xmin, $xc);
            $ymin = min($ymin, $yc);
            $xmax = max($xmax, $xc);
            $ymax = max($ymax, $yc);
        }
        return array(
            $xmin,
            $ymin,
            $xmax,
            $ymax
        );
    }
    public function Circle($x0, $y0, $r, $angstr = 0, $angend = 360, $style = '', $line_style = array(), $fill_color = array(), $nc = 2)
    {
        $this->Ellipse($x0, $y0, $r, $r, 0, $angstr, $angend, $style, $line_style, $fill_color, $nc);
    }
    public function PolyLine($p, $style = '', $line_style = array(), $fill_color = array())
    {
        $this->Polygon($p, $style, $line_style, $fill_color, false);
    }
    public function Polygon($p, $style = '', $line_style = array(), $fill_color = array(), $closed = true)
    {
        $nc = count($p);
        $np = $nc / 2;
        if ($closed) {
            for ($i = 0; $i < 4; ++$i) {
                $p[$nc + $i] = $p[$i];
            }
            if (isset($line_style[0])) {
                $line_style[$np] = $line_style[0];
            }
            $nc += 4;
        }
        if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
            $this->SetFillColorArray($fill_color);
        }
        $op = $this->getPathPaintOperator($style);
        if ($op == 'f') {
            $line_style = array();
        }
        $draw = true;
        if ($line_style) {
            if (isset($line_style['all'])) {
                $this->SetLineStyle($line_style['all']);
            } else {
                $draw = false;
                if ($op == 'B') {
                    $op = 'f';
                    $this->_outPoint($p[0], $p[1]);
                    for ($i = 2; $i < $nc; $i = $i + 2) {
                        $this->_outLine($p[$i], $p[$i + 1]);
                    }
                    $this->_out($op);
                }
                $this->_outPoint($p[0], $p[1]);
                for ($i = 2; $i < $nc; $i = $i + 2) {
                    $line_num = ($i / 2) - 1;
                    if (isset($line_style[$line_num])) {
                        if ($line_style[$line_num] != 0) {
                            if (is_array($line_style[$line_num])) {
                                $this->_out('S');
                                $this->SetLineStyle($line_style[$line_num]);
                                $this->_outPoint($p[$i - 2], $p[$i - 1]);
                                $this->_outLine($p[$i], $p[$i + 1]);
                                $this->_out('S');
                                $this->_outPoint($p[$i], $p[$i + 1]);
                            } else {
                                $this->_outLine($p[$i], $p[$i + 1]);
                            }
                        }
                    } else {
                        $this->_outLine($p[$i], $p[$i + 1]);
                    }
                }
                $this->_out($op);
            }
        }
        if ($draw) {
            $this->_outPoint($p[0], $p[1]);
            for ($i = 2; $i < $nc; $i = $i + 2) {
                $this->_outLine($p[$i], $p[$i + 1]);
            }
            $this->_out($op);
        }
    }
    public function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $draw_circle = false, $style = '', $line_style = array(), $fill_color = array(), $circle_style = '', $circle_outLine_style = array(), $circle_fill_color = array())
    {
        if (3 > $ns) {
            $ns = 3;
        }
        if ($draw_circle) {
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
        }
        $p = array();
        for ($i = 0; $i < $ns; ++$i) {
            $a     = $angle + ($i * 360 / $ns);
            $a_rad = deg2rad((float) $a);
            $p[]   = $x0 + ($r * sin($a_rad));
            $p[]   = $y0 + ($r * cos($a_rad));
        }
        $this->Polygon($p, $style, $line_style, $fill_color);
    }
    public function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $draw_circle = false, $style = '', $line_style = array(), $fill_color = array(), $circle_style = '', $circle_outLine_style = array(), $circle_fill_color = array())
    {
        if ($nv < 2) {
            $nv = 2;
        }
        if ($draw_circle) {
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
        }
        $p2      = array();
        $visited = array();
        for ($i = 0; $i < $nv; ++$i) {
            $a         = $angle + ($i * 360 / $nv);
            $a_rad     = deg2rad((float) $a);
            $p2[]      = $x0 + ($r * sin($a_rad));
            $p2[]      = $y0 + ($r * cos($a_rad));
            $visited[] = false;
        }
        $p = array();
        $i = 0;
        do {
            $p[]         = $p2[$i * 2];
            $p[]         = $p2[($i * 2) + 1];
            $visited[$i] = true;
            $i += $ng;
            $i %= $nv;
        } while (!$visited[$i]);
        $this->Polygon($p, $style, $line_style, $fill_color);
    }
    public function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = array(), $fill_color = array())
    {
        $this->RoundedRectXY($x, $y, $w, $h, $r, $r, $round_corner, $style, $border_style, $fill_color);
    }
    public function RoundedRectXY($x, $y, $w, $h, $rx, $ry, $round_corner = '1111', $style = '', $border_style = array(), $fill_color = array())
    {
        if (($round_corner == '0000') OR (($rx == $ry) AND ($rx == 0))) {
            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
            return;
        }
        if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
            $this->SetFillColorArray($fill_color);
        }
        $op = $this->getPathPaintOperator($style);
        if ($op == 'f') {
            $border_style = array();
        }
        if ($border_style) {
            $this->SetLineStyle($border_style);
        }
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_outPoint($x + $rx, $y);
        $xc = $x + $w - $rx;
        $yc = $y + $ry;
        $this->_outLine($xc, $y);
        if ($round_corner[0]) {
            $this->_outCurve($xc + ($rx * $MyArc), $yc - $ry, $xc + $rx, $yc - ($ry * $MyArc), $xc + $rx, $yc);
        } else {
            $this->_outLine($x + $w, $y);
        }
        $xc = $x + $w - $rx;
        $yc = $y + $h - $ry;
        $this->_outLine($x + $w, $yc);
        if ($round_corner[1]) {
            $this->_outCurve($xc + $rx, $yc + ($ry * $MyArc), $xc + ($rx * $MyArc), $yc + $ry, $xc, $yc + $ry);
        } else {
            $this->_outLine($x + $w, $y + $h);
        }
        $xc = $x + $rx;
        $yc = $y + $h - $ry;
        $this->_outLine($xc, $y + $h);
        if ($round_corner[2]) {
            $this->_outCurve($xc - ($rx * $MyArc), $yc + $ry, $xc - $rx, $yc + ($ry * $MyArc), $xc - $rx, $yc);
        } else {
            $this->_outLine($x, $y + $h);
        }
        $xc = $x + $rx;
        $yc = $y + $ry;
        $this->_outLine($x, $yc);
        if ($round_corner[3]) {
            $this->_outCurve($xc - $rx, $yc - ($ry * $MyArc), $xc - ($rx * $MyArc), $yc - $ry, $xc, $yc - $ry);
        } else {
            $this->_outLine($x, $y);
            $this->_outLine($x + $rx, $y);
        }
        $this->_out($op);
    }
    public function Arrow($x0, $y0, $x1, $y1, $head_style = 0, $arm_size = 5, $arm_angle = 15)
    {
        $dir_angle = atan2(($y0 - $y1), ($x0 - $x1));
        if ($dir_angle < 0) {
            $dir_angle += (2 * M_PI);
        }
        $arm_angle = deg2rad($arm_angle);
        $sx1       = $x1;
        $sy1       = $y1;
        if ($head_style > 0) {
            $sx1 = $x1 + (($arm_size - $this->LineWidth) * cos($dir_angle));
            $sy1 = $y1 + (($arm_size - $this->LineWidth) * sin($dir_angle));
        }
        $this->Line($x0, $y0, $sx1, $sy1);
        $x2L   = $x1 + ($arm_size * cos($dir_angle + $arm_angle));
        $y2L   = $y1 + ($arm_size * sin($dir_angle + $arm_angle));
        $x2R   = $x1 + ($arm_size * cos($dir_angle - $arm_angle));
        $y2R   = $y1 + ($arm_size * sin($dir_angle - $arm_angle));
        $mode  = 'D';
        $style = array();
        switch ($head_style) {
            case 0: {
                $mode  = 'D';
                $style = array(
                    1,
                    1,
                    0
                );
                break;
            }
            case 1: {
                $mode = 'D';
                break;
            }
            case 2: {
                $mode = 'DF';
                break;
            }
            case 3: {
                $mode = 'F';
                break;
            }
        }
        $this->Polygon(array(
            $x2L,
            $y2L,
            $x1,
            $y1,
            $x2R,
            $y2R
        ), $mode, $style, array());
    }
    protected function utf8StrRev($str, $setbom = false, $forcertl = false)
    {
        return $this->utf8StrArrRev($this->UTF8StringToArray($str), $str, $setbom, $forcertl);
    }
    protected function utf8StrArrRev($arr, $str = '', $setbom = false, $forcertl = false)
    {
        return $this->arrUTF8ToUTF16BE($this->utf8Bidi($arr, $str, $forcertl), $setbom);
    }
    protected function utf8Bidi($ta, $str = '', $forcertl = false)
    {
        $pel      = 0;
        $maxlevel = 0;
        if ($this->empty_string($str)) {
            $str = $this->UTF8ArrSubString($ta);
        }
        if (preg_match($this->unicode->uni_RE_PATTERN_ARABIC, $str)) {
            $arabic = true;
        } else {
            $arabic = false;
        }
        if (!($forcertl OR $arabic OR preg_match($this->unicode->uni_RE_PATTERN_RTL, $str))) {
            return $ta;
        }
        $numchars = count($ta);
        if ($forcertl == 'R') {
            $pel = 1;
        } elseif ($forcertl == 'L') {
            $pel = 0;
        } else {
            for ($i = 0; $i < $numchars; ++$i) {
                $type = $this->unicode->uni_type[$ta[$i]];
                if ($type == 'L') {
                    $pel = 0;
                    break;
                } elseif (($type == 'AL') OR ($type == 'R')) {
                    $pel = 1;
                    break;
                }
            }
        }
        $cel      = $pel;
        $dos      = 'N';
        $remember = array();
        $sor      = $pel % 2 ? 'R' : 'L';
        $eor      = $sor;
        $chardata = Array();
        for ($i = 0; $i < $numchars; ++$i) {
            if ($ta[$i] == $this->unicode->uni_RLE) {
                $next_level = $cel + ($cel % 2) + 1;
                if ($next_level < 62) {
                    $remember[] = array(
                        'num' => $this->unicode->uni_RLE,
                        'cel' => $cel,
                        'dos' => $dos
                    );
                    $cel        = $next_level;
                    $dos        = 'N';
                    $sor        = $eor;
                    $eor        = $cel % 2 ? 'R' : 'L';
                }
            } elseif ($ta[$i] == $this->unicode->uni_LRE) {
                $next_level = $cel + 2 - ($cel % 2);
                if ($next_level < 62) {
                    $remember[] = array(
                        'num' => $this->unicode->uni_LRE,
                        'cel' => $cel,
                        'dos' => $dos
                    );
                    $cel        = $next_level;
                    $dos        = 'N';
                    $sor        = $eor;
                    $eor        = $cel % 2 ? 'R' : 'L';
                }
            } elseif ($ta[$i] == $this->unicode->uni_RLO) {
                $next_level = $cel + ($cel % 2) + 1;
                if ($next_level < 62) {
                    $remember[] = array(
                        'num' => $this->unicode->uni_RLO,
                        'cel' => $cel,
                        'dos' => $dos
                    );
                    $cel        = $next_level;
                    $dos        = 'R';
                    $sor        = $eor;
                    $eor        = $cel % 2 ? 'R' : 'L';
                }
            } elseif ($ta[$i] == $this->unicode->uni_LRO) {
                $next_level = $cel + 2 - ($cel % 2);
                if ($next_level < 62) {
                    $remember[] = array(
                        'num' => $this->unicode->uni_LRO,
                        'cel' => $cel,
                        'dos' => $dos
                    );
                    $cel        = $next_level;
                    $dos        = 'L';
                    $sor        = $eor;
                    $eor        = $cel % 2 ? 'R' : 'L';
                }
            } elseif ($ta[$i] == $this->unicode->uni_PDF) {
                if (count($remember)) {
                    $last = count($remember) - 1;
                    if (($remember[$last]['num'] == $this->unicode->uni_RLE) OR ($remember[$last]['num'] == $this->unicode->uni_LRE) OR ($remember[$last]['num'] == $this->unicode->uni_RLO) OR ($remember[$last]['num'] == $this->unicode->uni_LRO)) {
                        $match = array_pop($remember);
                        $cel   = $match['cel'];
                        $dos   = $match['dos'];
                        $sor   = $eor;
                        $eor   = ($cel > $match['cel'] ? $cel : $match['cel']) % 2 ? 'R' : 'L';
                    }
                }
            } elseif (($ta[$i] != $this->unicode->uni_RLE) AND ($ta[$i] != $this->unicode->uni_LRE) AND ($ta[$i] != $this->unicode->uni_RLO) AND ($ta[$i] != $this->unicode->uni_LRO) AND ($ta[$i] != $this->unicode->uni_PDF)) {
                if ($dos != 'N') {
                    $chardir = $dos;
                } else {
                    if (isset($this->unicode->uni_type[$ta[$i]])) {
                        $chardir = $this->unicode->uni_type[$ta[$i]];
                    } else {
                        $chardir = 'L';
                    }
                }
                $chardata[] = array(
                    'char' => $ta[$i],
                    'level' => $cel,
                    'type' => $chardir,
                    'sor' => $sor,
                    'eor' => $eor
                );
            }
        }
        $numchars  = count($chardata);
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if ($chardata[$i]['type'] == 'NSM') {
                if ($levcount) {
                    $chardata[$i]['type'] = $chardata[$i]['sor'];
                } elseif ($i > 0) {
                    $chardata[$i]['type'] = $chardata[($i - 1)]['type'];
                }
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if ($chardata[$i]['char'] == 'EN') {
                for ($j = $levcount; $j >= 0; $j--) {
                    if ($chardata[$j]['type'] == 'AL') {
                        $chardata[$i]['type'] = 'AN';
                    } elseif (($chardata[$j]['type'] == 'L') OR ($chardata[$j]['type'] == 'R')) {
                        break;
                    }
                }
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        for ($i = 0; $i < $numchars; ++$i) {
            if ($chardata[$i]['type'] == 'AL') {
                $chardata[$i]['type'] = 'R';
            }
        }
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if (($levcount > 0) AND (($i + 1) < $numchars) AND ($chardata[($i + 1)]['level'] == $prevlevel)) {
                if (($chardata[$i]['type'] == 'ES') AND ($chardata[($i - 1)]['type'] == 'EN') AND ($chardata[($i + 1)]['type'] == 'EN')) {
                    $chardata[$i]['type'] = 'EN';
                } elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i - 1)]['type'] == 'EN') AND ($chardata[($i + 1)]['type'] == 'EN')) {
                    $chardata[$i]['type'] = 'EN';
                } elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i - 1)]['type'] == 'AN') AND ($chardata[($i + 1)]['type'] == 'AN')) {
                    $chardata[$i]['type'] = 'AN';
                }
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if ($chardata[$i]['type'] == 'ET') {
                if (($levcount > 0) AND ($chardata[($i - 1)]['type'] == 'EN')) {
                    $chardata[$i]['type'] = 'EN';
                } else {
                    $j = $i + 1;
                    while (($j < $numchars) AND ($chardata[$j]['level'] == $prevlevel)) {
                        if ($chardata[$j]['type'] == 'EN') {
                            $chardata[$i]['type'] = 'EN';
                            break;
                        } elseif ($chardata[$j]['type'] != 'ET') {
                            break;
                        }
                        ++$j;
                    }
                }
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if (($chardata[$i]['type'] == 'ET') OR ($chardata[$i]['type'] == 'ES') OR ($chardata[$i]['type'] == 'CS')) {
                $chardata[$i]['type'] = 'ON';
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if ($chardata[$i]['char'] == 'EN') {
                for ($j = $levcount; $j >= 0; $j--) {
                    if ($chardata[$j]['type'] == 'L') {
                        $chardata[$i]['type'] = 'L';
                    } elseif ($chardata[$j]['type'] == 'R') {
                        break;
                    }
                }
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        $prevlevel = -1;
        $levcount  = 0;
        for ($i = 0; $i < $numchars; ++$i) {
            if (($levcount > 0) AND (($i + 1) < $numchars) AND ($chardata[($i + 1)]['level'] == $prevlevel)) {
                if (($chardata[$i]['type'] == 'N') AND ($chardata[($i - 1)]['type'] == 'L') AND ($chardata[($i + 1)]['type'] == 'L')) {
                    $chardata[$i]['type'] = 'L';
                } elseif (($chardata[$i]['type'] == 'N') AND (($chardata[($i - 1)]['type'] == 'R') OR ($chardata[($i - 1)]['type'] == 'EN') OR ($chardata[($i - 1)]['type'] == 'AN')) AND (($chardata[($i + 1)]['type'] == 'R') OR ($chardata[($i + 1)]['type'] == 'EN') OR ($chardata[($i + 1)]['type'] == 'AN'))) {
                    $chardata[$i]['type'] = 'R';
                } elseif ($chardata[$i]['type'] == 'N') {
                    $chardata[$i]['type'] = $chardata[$i]['sor'];
                }
            } elseif (($levcount == 0) AND (($i + 1) < $numchars) AND ($chardata[($i + 1)]['level'] == $prevlevel)) {
                if (($chardata[$i]['type'] == 'N') AND ($chardata[$i]['sor'] == 'L') AND ($chardata[($i + 1)]['type'] == 'L')) {
                    $chardata[$i]['type'] = 'L';
                } elseif (($chardata[$i]['type'] == 'N') AND (($chardata[$i]['sor'] == 'R') OR ($chardata[$i]['sor'] == 'EN') OR ($chardata[$i]['sor'] == 'AN')) AND (($chardata[($i + 1)]['type'] == 'R') OR ($chardata[($i + 1)]['type'] == 'EN') OR ($chardata[($i + 1)]['type'] == 'AN'))) {
                    $chardata[$i]['type'] = 'R';
                } elseif ($chardata[$i]['type'] == 'N') {
                    $chardata[$i]['type'] = $chardata[$i]['sor'];
                }
            } elseif (($levcount > 0) AND ((($i + 1) == $numchars) OR (($i + 1) < $numchars) AND ($chardata[($i + 1)]['level'] != $prevlevel))) {
                if (($chardata[$i]['type'] == 'N') AND ($chardata[($i - 1)]['type'] == 'L') AND ($chardata[$i]['eor'] == 'L')) {
                    $chardata[$i]['type'] = 'L';
                } elseif (($chardata[$i]['type'] == 'N') AND (($chardata[($i - 1)]['type'] == 'R') OR ($chardata[($i - 1)]['type'] == 'EN') OR ($chardata[($i - 1)]['type'] == 'AN')) AND (($chardata[$i]['eor'] == 'R') OR ($chardata[$i]['eor'] == 'EN') OR ($chardata[$i]['eor'] == 'AN'))) {
                    $chardata[$i]['type'] = 'R';
                } elseif ($chardata[$i]['type'] == 'N') {
                    $chardata[$i]['type'] = $chardata[$i]['sor'];
                }
            } elseif ($chardata[$i]['type'] == 'N') {
                $chardata[$i]['type'] = $chardata[$i]['sor'];
            }
            if ($chardata[$i]['level'] != $prevlevel) {
                $levcount = 0;
            } else {
                ++$levcount;
            }
            $prevlevel = $chardata[$i]['level'];
        }
        for ($i = 0; $i < $numchars; ++$i) {
            $odd = $chardata[$i]['level'] % 2;
            if ($odd) {
                if (($chardata[$i]['type'] == 'L') OR ($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')) {
                    $chardata[$i]['level'] += 1;
                }
            } else {
                if ($chardata[$i]['type'] == 'R') {
                    $chardata[$i]['level'] += 1;
                } elseif (($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')) {
                    $chardata[$i]['level'] += 2;
                }
            }
            $maxlevel = max($chardata[$i]['level'], $maxlevel);
        }
        for ($i = 0; $i < $numchars; ++$i) {
            if (($chardata[$i]['type'] == 'B') OR ($chardata[$i]['type'] == 'S')) {
                $chardata[$i]['level'] = $pel;
            } elseif ($chardata[$i]['type'] == 'WS') {
                $j = $i + 1;
                while ($j < $numchars) {
                    if ((($chardata[$j]['type'] == 'B') OR ($chardata[$j]['type'] == 'S')) OR (($j == ($numchars - 1)) AND ($chardata[$j]['type'] == 'WS'))) {
                        $chardata[$i]['level'] = $pel;
                        break;
                    } elseif ($chardata[$j]['type'] != 'WS') {
                        break;
                    }
                    ++$j;
                }
            }
        }
        if ($arabic) {
            $endedletter = array(
                1569,
                1570,
                1571,
                1572,
                1573,
                1575,
                1577,
                1583,
                1584,
                1585,
                1586,
                1608,
                1688
            );
            $alfletter   = array(
                1570,
                1571,
                1573,
                1575
            );
            $chardata2   = $chardata;
            $laaletter   = false;
            $charAL      = array();
            $x           = 0;
            for ($i = 0; $i < $numchars; ++$i) {
                if (($this->unicode->uni_type[$chardata[$i]['char']] == 'AL') OR ($chardata[$i]['char'] == 32) OR ($chardata[$i]['char'] == 8204)) {
                    $charAL[$x]        = $chardata[$i];
                    $charAL[$x]['i']   = $i;
                    $chardata[$i]['x'] = $x;
                    ++$x;
                }
            }
            $numAL = $x;
            for ($i = 0; $i < $numchars; ++$i) {
                $thischar = $chardata[$i];
                if ($i > 0) {
                    $prevchar = $chardata[($i - 1)];
                } else {
                    $prevchar = false;
                }
                if (($i + 1) < $numchars) {
                    $nextchar = $chardata[($i + 1)];
                } else {
                    $nextchar = false;
                }
                if ($this->unicode->uni_type[$thischar['char']] == 'AL') {
                    $x = $thischar['x'];
                    if ($x > 0) {
                        $prevchar = $charAL[($x - 1)];
                    } else {
                        $prevchar = false;
                    }
                    if (($x + 1) < $numAL) {
                        $nextchar = $charAL[($x + 1)];
                    } else {
                        $nextchar = false;
                    }
                    if (($prevchar !== false) AND ($prevchar['char'] == 1604) AND (in_array($thischar['char'], $alfletter))) {
                        $arabicarr = $this->unicode->uni_laa_array;
                        $laaletter = true;
                        if ($x > 1) {
                            $prevchar = $charAL[($x - 2)];
                        } else {
                            $prevchar = false;
                        }
                    } else {
                        $arabicarr = $this->unicode->uni_arabicsubst;
                        $laaletter = false;
                    }
                    if (($prevchar !== false) AND ($nextchar !== false) AND (($this->unicode->uni_type[$prevchar['char']] == 'AL') OR ($this->unicode->uni_type[$prevchar['char']] == 'NSM')) AND (($this->unicode->uni_type[$nextchar['char']] == 'AL') OR ($this->unicode->uni_type[$nextchar['char']] == 'NSM')) AND ($prevchar['type'] == $thischar['type']) AND ($nextchar['type'] == $thischar['type']) AND ($nextchar['char'] != 1567)) {
                        if (in_array($prevchar['char'], $endedletter)) {
                            if (isset($arabicarr[$thischar['char']][2])) {
                                $chardata2[$i]['char'] = $arabicarr[$thischar['char']][2];
                            }
                        } else {
                            if (isset($arabicarr[$thischar['char']][3])) {
                                $chardata2[$i]['char'] = $arabicarr[$thischar['char']][3];
                            }
                        }
                    } elseif (($nextchar !== false) AND (($this->unicode->uni_type[$nextchar['char']] == 'AL') OR ($this->unicode->uni_type[$nextchar['char']] == 'NSM')) AND ($nextchar['type'] == $thischar['type']) AND ($nextchar['char'] != 1567)) {
                        if (isset($arabicarr[$chardata[$i]['char']][2])) {
                            $chardata2[$i]['char'] = $arabicarr[$thischar['char']][2];
                        }
                    } elseif ((($prevchar !== false) AND (($this->unicode->uni_type[$prevchar['char']] == 'AL') OR ($this->unicode->uni_type[$prevchar['char']] == 'NSM')) AND ($prevchar['type'] == $thischar['type'])) OR (($nextchar !== false) AND ($nextchar['char'] == 1567))) {
                        if (($i > 1) AND ($thischar['char'] == 1607) AND ($chardata[$i - 1]['char'] == 1604) AND ($chardata[$i - 2]['char'] == 1604)) {
                            $chardata2[$i - 2]['char'] = false;
                            $chardata2[$i - 1]['char'] = false;
                            $chardata2[$i]['char']     = 65010;
                        } else {
                            if (($prevchar !== false) AND in_array($prevchar['char'], $endedletter)) {
                                if (isset($arabicarr[$thischar['char']][0])) {
                                    $chardata2[$i]['char'] = $arabicarr[$thischar['char']][0];
                                }
                            } else {
                                if (isset($arabicarr[$thischar['char']][1])) {
                                    $chardata2[$i]['char'] = $arabicarr[$thischar['char']][1];
                                }
                            }
                        }
                    } elseif (isset($arabicarr[$thischar['char']][0])) {
                        $chardata2[$i]['char'] = $arabicarr[$thischar['char']][0];
                    }
                    if ($laaletter) {
                        $chardata2[($charAL[($x - 1)]['i'])]['char'] = false;
                    }
                }
            }
            for ($i = 0; $i < ($numchars - 1); ++$i) {
                if (($chardata2[$i]['char'] == 1617) AND (isset($this->unicode->uni_diacritics[($chardata2[$i + 1]['char'])]))) {
                    if (isset($this->CurrentFont['cw'][($this->unicode->uni_diacritics[($chardata2[$i + 1]['char'])])])) {
                        $chardata2[$i]['char']     = false;
                        $chardata2[$i + 1]['char'] = $this->unicode->uni_diacritics[($chardata2[$i + 1]['char'])];
                    }
                }
            }
            foreach ($chardata2 as $key => $value) {
                if ($value['char'] === false) {
                    unset($chardata2[$key]);
                }
            }
            $chardata = array_values($chardata2);
            $numchars = count($chardata);
            unset($chardata2);
            unset($arabicarr);
            unset($laaletter);
            unset($charAL);
        }
        for ($j = $maxlevel; $j > 0; $j--) {
            $ordarray = Array();
            $revarr   = Array();
            $onlevel  = false;
            for ($i = 0; $i < $numchars; ++$i) {
                if ($chardata[$i]['level'] >= $j) {
                    $onlevel = true;
                    if (isset($this->unicode->uni_mirror[$chardata[$i]['char']])) {
                        $chardata[$i]['char'] = $this->unicode->uni_mirror[$chardata[$i]['char']];
                    }
                    $revarr[] = $chardata[$i];
                } else {
                    if ($onlevel) {
                        $revarr   = array_reverse($revarr);
                        $ordarray = array_merge($ordarray, $revarr);
                        $revarr   = Array();
                        $onlevel  = false;
                    }
                    $ordarray[] = $chardata[$i];
                }
            }
            if ($onlevel) {
                $revarr   = array_reverse($revarr);
                $ordarray = array_merge($ordarray, $revarr);
            }
            $chardata = $ordarray;
        }
        $ordarray = array();
        for ($i = 0; $i < $numchars; ++$i) {
            $ordarray[]                                              = $chardata[$i]['char'];
            $this->CurrentFont['subsetchars'][$chardata[$i]['char']] = true;
        }
        $this->setFontSubBuffer($this->CurrentFont['fontkey'], 'subsetchars', $this->CurrentFont['subsetchars']);
        return $ordarray;
    }
    protected function encodeNameObject($name)
    {
        $escname = '';
        $length  = strlen($name);
        for ($i = 0; $i < $length; ++$i) {
            $chr = $name{$i};
            if (preg_match('/[0-9a-zA-Z]/', $chr) == 1) {
                $escname .= $chr;
            } else {
                $escname .= sprintf('#%02X', ord($chr));
            }
        }
        return $escname;
    }
    public function setDestination($name, $y = -1, $page = '')
    {
        $name = $this->encodeNameObject($name);
        if ($this->empty_string($name)) {
            return false;
        }
        if ($y == -1) {
            $y = $this->GetY();
        }
        if (empty($page)) {
            $page = $this->PageNo();
            if (empty($page)) {
                return;
            }
        }
        $this->dests[$name] = array(
            'y' => $y,
            'p' => $page
        );
        return $name;
    }
    public function getDestination()
    {
        return $this->dests;
    }
    protected function _putdests()
    {
        if (empty($this->dests)) {
            return;
        }
        $this->n_dests = $this->_newobj();
        $out           = ' <<';
        foreach ($this->dests as $name => $o) {
            $out .= ' /' . $name . ' ' . sprintf('[%u 0 R /XYZ 0 %.2F null]', $this->page_obj_id[($o['p'])], ($this->pagedim[$o['p']]['h'] - ($o['y'] * $this->k)));
        }
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
    }
    public function setBookmark($txt, $level = 0, $y = -1, $page = '', $style = '', $color = array(0, 0, 0))
    {
        $this->Bookmark($txt, $level, $y, $page, $style, $color);
    }
    public function Bookmark($txt, $level = 0, $y = -1, $page = '', $style = '', $color = array(0, 0, 0))
    {
        if ($level < 0) {
            $level = 0;
        }
        if (isset($this->outlines[0])) {
            $lastoutline = end($this->outlines);
            $maxlevel    = $lastoutline['l'] + 1;
        } else {
            $maxlevel = 0;
        }
        if ($level > $maxlevel) {
            $level = $maxlevel;
        }
        if ($y == -1) {
            $y = $this->GetY();
        }
        if (empty($page)) {
            $page = $this->PageNo();
            if (empty($page)) {
                return;
            }
        }
        $this->outlines[] = array(
            't' => $txt,
            'l' => $level,
            'y' => $y,
            'p' => $page,
            's' => strtoupper($style),
            'c' => $color
        );
    }
    protected function _putbookmarks()
    {
        $nb = count($this->outlines);
        if ($nb == 0) {
            return;
        }
        $outline_p = array();
        $outline_y = array();
        foreach ($this->outlines as $key => $row) {
            $outline_p[$key] = $row['p'];
            $outline_k[$key] = $key;
        }
        array_multisort($outline_p, SORT_NUMERIC, SORT_ASC, $outline_k, SORT_NUMERIC, SORT_ASC, $this->outlines);
        $lru   = array();
        $level = 0;
        foreach ($this->outlines as $i => $o) {
            if ($o['l'] > 0) {
                $parent                          = $lru[($o['l'] - 1)];
                $this->outlines[$i]['parent']    = $parent;
                $this->outlines[$parent]['last'] = $i;
                if ($o['l'] > $level) {
                    $this->outlines[$parent]['first'] = $i;
                }
            } else {
                $this->outlines[$i]['parent'] = $nb;
            }
            if (($o['l'] <= $level) AND ($i > 0)) {
                $prev                          = $lru[$o['l']];
                $this->outlines[$prev]['next'] = $i;
                $this->outlines[$i]['prev']    = $prev;
            }
            $lru[$o['l']] = $i;
            $level        = $o['l'];
        }
        $n      = $this->n + 1;
        $nltags = '/<br[\s]?\/>|<\/(blockquote|dd|dl|div|dt|h1|h2|h3|h4|h5|h6|hr|li|ol|p|pre|ul|tcpdf|table|tr|td)>/si';
        foreach ($this->outlines as $i => $o) {
            if (isset($this->page_obj_id[($o['p'])])) {
                $oid   = $this->_newobj();
                $title = preg_replace($nltags, "\n", $o['t']);
                $title = preg_replace("/[\r]+/si", '', $title);
                $title = preg_replace("/[\n]+/si", "\n", $title);
                $title = strip_tags($title);
                $title = $this->stringTrim($title);
                $out   = '<</Title ' . $this->_textstring($title, $oid);
                $out .= ' /Parent ' . ($n + $o['parent']) . ' 0 R';
                if (isset($o['prev'])) {
                    $out .= ' /Prev ' . ($n + $o['prev']) . ' 0 R';
                }
                if (isset($o['next'])) {
                    $out .= ' /Next ' . ($n + $o['next']) . ' 0 R';
                }
                if (isset($o['first'])) {
                    $out .= ' /First ' . ($n + $o['first']) . ' 0 R';
                }
                if (isset($o['last'])) {
                    $out .= ' /Last ' . ($n + $o['last']) . ' 0 R';
                }
                $out .= ' ' . sprintf('/Dest [%u 0 R /XYZ 0 %.2F null]', $this->page_obj_id[($o['p'])], ($this->pagedim[$o['p']]['h'] - ($o['y'] * $this->k)));
                $style = 0;
                if (!empty($o['s'])) {
                    if (strpos($o['s'], 'B') !== false) {
                        $style |= 2;
                    }
                    if (strpos($o['s'], 'I') !== false) {
                        $style |= 1;
                    }
                }
                $out .= sprintf(' /F %d', $style);
                if (isset($o['c']) AND is_array($o['c']) AND (count($o['c']) == 3)) {
                    $color = array_values($o['c']);
                    $out .= sprintf(' /C [%.3F %.3F %.3F]', ($color[0] / 255), ($color[1] / 255), ($color[2] / 255));
                } else {
                    $out .= ' /C [0.0 0.0 0.0]';
                }
                $out .= ' /Count 0';
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            }
        }
        $this->OutlineRoot = $this->_newobj();
        $this->_out('<< /Type /Outlines /First ' . $n . ' 0 R /Last ' . ($n + $lru[0]) . ' 0 R >>' . "\n" . 'endobj');
    }
    public function IncludeJS($script)
    {
        $this->javascript .= $script;
    }
    public function addJavascriptObject($script, $onload = false)
    {
        ++$this->n;
        $this->js_objects[$this->n] = array(
            'n' => $this->n,
            'js' => $script,
            'onload' => $onload
        );
        return $this->n;
    }
    protected function _putjavascript()
    {
        if (empty($this->javascript) AND empty($this->js_objects)) {
            return;
        }
        if (strpos($this->javascript, 'this.addField') > 0) {
            if (!$this->ur['enabled']) {
            }
            $jsa              = sprintf("ftcpdfdocsaved=this.addField('%s','%s',%d,[%.2F,%.2F,%.2F,%.2F]);", 'tcpdfdocsaved', 'text', 0, 0, 1, 0, 1);
            $jsb              = "getField('tcpdfdocsaved').value='saved';";
            $this->javascript = $jsa . "\n" . $this->javascript . "\n" . $jsb;
        }
        $this->n_js = $this->_newobj();
        $out        = ' << /Names [';
        if (!empty($this->javascript)) {
            $out .= ' (EmbeddedJS) ' . ($this->n + 1) . ' 0 R';
        }
        if (!empty($this->js_objects)) {
            foreach ($this->js_objects as $key => $val) {
                if ($val['onload']) {
                    $out .= ' (JS' . $key . ') ' . $key . ' 0 R';
                }
            }
        }
        $out .= ' ] >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
        if (!empty($this->javascript)) {
            $obj_id = $this->_newobj();
            $out    = '<< /S /JavaScript';
            $out .= ' /JS ' . $this->_textstring($this->javascript, $obj_id);
            $out .= ' >>';
            $out .= "\n" . 'endobj';
            $this->_out($out);
        }
        if (!empty($this->js_objects)) {
            foreach ($this->js_objects as $key => $val) {
                $out = $this->_getobj($key) . "\n" . ' << /S /JavaScript /JS ' . $this->_textstring($val['js'], $key) . ' >>' . "\n" . 'endobj';
                $this->_out($out);
            }
        }
    }
    protected function _JScolor($color)
    {
        static $aColors = array('transparent', 'black', 'white', 'red', 'green', 'blue', 'cyan', 'magenta', 'yellow', 'dkGray', 'gray', 'ltGray');
        if (substr($color, 0, 1) == '#') {
            return sprintf("['RGB',%.3F,%.3F,%.3F]", hexdec(substr($color, 1, 2)) / 255, hexdec(substr($color, 3, 2)) / 255, hexdec(substr($color, 5, 2)) / 255);
        }
        if (!in_array($color, $aColors)) {
            $this->Error('Invalid color: ' . $color);
        }
        return 'color.' . $color;
    }
    protected function _addfield($type, $name, $x, $y, $w, $h, $prop)
    {
        if ($this->rtl) {
            $x = $x - $w;
        }
        $this->javascript .= "if (getField('tcpdfdocsaved').value != 'saved') {";
        $k = $this->k;
        $this->javascript .= sprintf("f" . $name . "=this.addField('%s','%s',%u,[%.2F,%.2F,%.2F,%.2F]);", $name, $type, $this->PageNo() - 1, $x * $k, ($this->h - $y) * $k + 1, ($x + $w) * $k, ($this->h - $y - $h) * $k + 1) . "\n";
        $this->javascript .= 'f' . $name . '.textSize=' . $this->FontSizePt . ";\n";
        while (list($key, $val) = each($prop)) {
            if (strcmp(substr($key, -5), 'Color') == 0) {
                $val = $this->_JScolor($val);
            } else {
                $val = "'" . $val . "'";
            }
            $this->javascript .= 'f' . $name . '.' . $key . '=' . $val . ";\n";
        }
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
        $this->javascript .= '}';
    }
    protected function getAnnotOptFromJSProp($prop)
    {
        if (isset($prop['aopt']) AND is_array($prop['aopt'])) {
            return $prop['aopt'];
        }
        $opt = array();
        if (isset($prop['alignment'])) {
            switch ($prop['alignment']) {
                case 'left': {
                    $opt['q'] = 0;
                    break;
                }
                case 'center': {
                    $opt['q'] = 1;
                    break;
                }
                case 'right': {
                    $opt['q'] = 2;
                    break;
                }
                default: {
                    $opt['q'] = ($this->rtl) ? 2 : 0;
                    break;
                }
            }
        }
        if (isset($prop['lineWidth'])) {
            $linewidth = intval($prop['lineWidth']);
        } else {
            $linewidth = 1;
        }
        if (isset($prop['borderStyle'])) {
            switch ($prop['borderStyle']) {
                case 'border.d':
                case 'dashed': {
                    $opt['border'] = array(
                        0,
                        0,
                        $linewidth,
                        array(
                            3,
                            2
                        )
                    );
                    $opt['bs']     = array(
                        'w' => $linewidth,
                        's' => 'D',
                        'd' => array(
                            3,
                            2
                        )
                    );
                    break;
                }
                case 'border.b':
                case 'beveled': {
                    $opt['border'] = array(
                        0,
                        0,
                        $linewidth
                    );
                    $opt['bs']     = array(
                        'w' => $linewidth,
                        's' => 'B'
                    );
                    break;
                }
                case 'border.i':
                case 'inset': {
                    $opt['border'] = array(
                        0,
                        0,
                        $linewidth
                    );
                    $opt['bs']     = array(
                        'w' => $linewidth,
                        's' => 'I'
                    );
                    break;
                }
                case 'border.u':
                case 'underline': {
                    $opt['border'] = array(
                        0,
                        0,
                        $linewidth
                    );
                    $opt['bs']     = array(
                        'w' => $linewidth,
                        's' => 'U'
                    );
                    break;
                }
                case 'border.s':
                case 'solid': {
                    $opt['border'] = array(
                        0,
                        0,
                        $linewidth
                    );
                    $opt['bs']     = array(
                        'w' => $linewidth,
                        's' => 'S'
                    );
                    break;
                }
                default: {
                    break;
                }
            }
        }
        if (isset($prop['border']) AND is_array($prop['border'])) {
            $opt['border'] = $prop['border'];
        }
        if (!isset($opt['mk'])) {
            $opt['mk'] = array();
        }
        if (!isset($opt['mk']['if'])) {
            $opt['mk']['if'] = array();
        }
        $opt['mk']['if']['a'] = array(
            0.5,
            0.5
        );
        if (isset($prop['buttonAlignX'])) {
            $opt['mk']['if']['a'][0] = $prop['buttonAlignX'];
        }
        if (isset($prop['buttonAlignY'])) {
            $opt['mk']['if']['a'][1] = $prop['buttonAlignY'];
        }
        if (isset($prop['buttonFitBounds']) AND ($prop['buttonFitBounds'] == 'true')) {
            $opt['mk']['if']['fb'] = true;
        }
        if (isset($prop['buttonScaleHow'])) {
            switch ($prop['buttonScaleHow']) {
                case 'scaleHow.proportional': {
                    $opt['mk']['if']['s'] = 'P';
                    break;
                }
                case 'scaleHow.anamorphic': {
                    $opt['mk']['if']['s'] = 'A';
                    break;
                }
            }
        }
        if (isset($prop['buttonScaleWhen'])) {
            switch ($prop['buttonScaleWhen']) {
                case 'scaleWhen.always': {
                    $opt['mk']['if']['sw'] = 'A';
                    break;
                }
                case 'scaleWhen.never': {
                    $opt['mk']['if']['sw'] = 'N';
                    break;
                }
                case 'scaleWhen.tooBig': {
                    $opt['mk']['if']['sw'] = 'B';
                    break;
                }
                case 'scaleWhen.tooSmall': {
                    $opt['mk']['if']['sw'] = 'S';
                    break;
                }
            }
        }
        if (isset($prop['buttonPosition'])) {
            switch ($prop['buttonPosition']) {
                case 0:
                case 'position.textOnly': {
                    $opt['mk']['tp'] = 0;
                    break;
                }
                case 1:
                case 'position.iconOnly': {
                    $opt['mk']['tp'] = 1;
                    break;
                }
                case 2:
                case 'position.iconTextV': {
                    $opt['mk']['tp'] = 2;
                    break;
                }
                case 3:
                case 'position.textIconV': {
                    $opt['mk']['tp'] = 3;
                    break;
                }
                case 4:
                case 'position.iconTextH': {
                    $opt['mk']['tp'] = 4;
                    break;
                }
                case 5:
                case 'position.textIconH': {
                    $opt['mk']['tp'] = 5;
                    break;
                }
                case 6:
                case 'position.overlay': {
                    $opt['mk']['tp'] = 6;
                    break;
                }
            }
        }
        if (isset($prop['fillColor'])) {
            if (is_array($prop['fillColor'])) {
                $opt['mk']['bg'] = $prop['fillColor'];
            } else {
                $opt['mk']['bg'] = $this->convertHTMLColorToDec($prop['fillColor']);
            }
        }
        if (isset($prop['strokeColor'])) {
            if (is_array($prop['strokeColor'])) {
                $opt['mk']['bc'] = $prop['strokeColor'];
            } else {
                $opt['mk']['bc'] = $this->convertHTMLColorToDec($prop['strokeColor']);
            }
        }
        if (isset($prop['rotation'])) {
            $opt['mk']['r'] = $prop['rotation'];
        }
        if (isset($prop['charLimit'])) {
            $opt['maxlen'] = intval($prop['charLimit']);
        }
        if (!isset($ff)) {
            $ff = 0;
        }
        if (isset($prop['readonly']) AND ($prop['readonly'] == 'true')) {
            $ff += 1 << 0;
        }
        if (isset($prop['required']) AND ($prop['required'] == 'true')) {
            $ff += 1 << 1;
        }
        if (isset($prop['multiline']) AND ($prop['multiline'] == 'true')) {
            $ff += 1 << 12;
        }
        if (isset($prop['password']) AND ($prop['password'] == 'true')) {
            $ff += 1 << 13;
        }
        if (isset($prop['NoToggleToOff']) AND ($prop['NoToggleToOff'] == 'true')) {
            $ff += 1 << 14;
        }
        if (isset($prop['Radio']) AND ($prop['Radio'] == 'true')) {
            $ff += 1 << 15;
        }
        if (isset($prop['Pushbutton']) AND ($prop['Pushbutton'] == 'true')) {
            $ff += 1 << 16;
        }
        if (isset($prop['Combo']) AND ($prop['Combo'] == 'true')) {
            $ff += 1 << 17;
        }
        if (isset($prop['editable']) AND ($prop['editable'] == 'true')) {
            $ff += 1 << 18;
        }
        if (isset($prop['Sort']) AND ($prop['Sort'] == 'true')) {
            $ff += 1 << 19;
        }
        if (isset($prop['fileSelect']) AND ($prop['fileSelect'] == 'true')) {
            $ff += 1 << 20;
        }
        if (isset($prop['multipleSelection']) AND ($prop['multipleSelection'] == 'true')) {
            $ff += 1 << 21;
        }
        if (isset($prop['doNotSpellCheck']) AND ($prop['doNotSpellCheck'] == 'true')) {
            $ff += 1 << 22;
        }
        if (isset($prop['doNotScroll']) AND ($prop['doNotScroll'] == 'true')) {
            $ff += 1 << 23;
        }
        if (isset($prop['comb']) AND ($prop['comb'] == 'true')) {
            $ff += 1 << 24;
        }
        if (isset($prop['radiosInUnison']) AND ($prop['radiosInUnison'] == 'true')) {
            $ff += 1 << 25;
        }
        if (isset($prop['richText']) AND ($prop['richText'] == 'true')) {
            $ff += 1 << 25;
        }
        if (isset($prop['commitOnSelChange']) AND ($prop['commitOnSelChange'] == 'true')) {
            $ff += 1 << 26;
        }
        $opt['ff'] = $ff;
        if (isset($prop['defaultValue'])) {
            $opt['dv'] = $prop['defaultValue'];
        }
        $f = 4;
        if (isset($prop['readonly']) AND ($prop['readonly'] == 'true')) {
            $f += 1 << 6;
        }
        if (isset($prop['display'])) {
            if ($prop['display'] == 'display.visible') {
            } elseif ($prop['display'] == 'display.hidden') {
                $f += 1 << 1;
            } elseif ($prop['display'] == 'display.noPrint') {
                $f -= 1 << 2;
            } elseif ($prop['display'] == 'display.noView') {
                $f += 1 << 5;
            }
        }
        $opt['f'] = $f;
        if (isset($prop['currentValueIndices']) AND is_array($prop['currentValueIndices'])) {
            $opt['i'] = $prop['currentValueIndices'];
        }
        if (isset($prop['value'])) {
            if (is_array($prop['value'])) {
                $opt['opt'] = array();
                foreach ($prop['value'] AS $key => $optval) {
                    if (isset($prop['exportValues'][$key])) {
                        $opt['opt'][$key] = array(
                            $prop['exportValues'][$key],
                            $prop['value'][$key]
                        );
                    } else {
                        $opt['opt'][$key] = $prop['value'][$key];
                    }
                }
            } else {
                $opt['v'] = $prop['value'];
            }
        }
        if (isset($prop['richValue'])) {
            $opt['rv'] = $prop['richValue'];
        }
        if (isset($prop['submitName'])) {
            $opt['tm'] = $prop['submitName'];
        }
        if (isset($prop['name'])) {
            $opt['t'] = $prop['name'];
        }
        if (isset($prop['userName'])) {
            $opt['tu'] = $prop['userName'];
        }
        if (isset($prop['highlight'])) {
            switch ($prop['highlight']) {
                case 'none':
                case 'highlight.n': {
                    $opt['h'] = 'N';
                    break;
                }
                case 'invert':
                case 'highlight.i': {
                    $opt['h'] = 'i';
                    break;
                }
                case 'push':
                case 'highlight.p': {
                    $opt['h'] = 'P';
                    break;
                }
                case 'outline':
                case 'highlight.o': {
                    $opt['h'] = 'O';
                    break;
                }
            }
        }
        return $opt;
    }
    public function setFormDefaultProp($prop = array())
    {
        $this->default_form_prop = $prop;
    }
    public function getFormDefaultProp()
    {
        return $this->default_form_prop;
    }
    public function TextField($name, $w, $h, $prop = array(), $opt = array(), $x = '', $y = '', $js = false)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        if ($js) {
            $this->_addfield('text', $name, $x, $y, $w, $h, $prop);
            return;
        }
        $prop                                                  = array_merge($this->getFormDefaultProp(), $prop);
        $popt                                                  = $this->getAnnotOptFromJSProp($prop);
        $this->annotation_fonts[$this->CurrentFont['fontkey']] = $this->CurrentFont['i'];
        $fontstyle                                             = sprintf('/F%d %.2F Tf %s', $this->CurrentFont['i'], $this->FontSizePt, $this->TextColor);
        $popt['da']                                            = $fontstyle;
        $popt['ap']                                            = array();
        $popt['ap']['n']                                       = 'q BT ' . $fontstyle . ' ET Q';
        $opt                                                   = array_merge($popt, $opt);
        unset($opt['bs']);
        $opt['Subtype'] = 'Widget';
        $opt['ft']      = 'Tx';
        $opt['t']       = $name;
        $this->Annotation($x, $y, $w, $h, $name, $opt, 0);
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
    }
    public function RadioButton($name, $w, $prop = array(), $opt = array(), $onvalue = 'On', $checked = false, $x = '', $y = '', $js = false)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($w, $x, $y);
        if ($js) {
            $this->_addfield('radiobutton', $name, $x, $y, $w, $w, $prop);
            return;
        }
        if ($this->empty_string($onvalue)) {
            $onvalue = 'On';
        }
        if ($checked) {
            $defval = $onvalue;
        } else {
            $defval = 'Off';
        }
        if (!isset($this->radiobutton_groups[$this->page])) {
            $this->radiobutton_groups[$this->page] = array();
        }
        if (!isset($this->radiobutton_groups[$this->page][$name])) {
            $this->radiobutton_groups[$this->page][$name] = array();
            ++$this->n;
            $this->radiobutton_groups[$this->page][$name]['n'] = $this->n;
            $this->radio_groups[]                              = $this->n;
            $kid                                               = ($this->n + 2);
        } else {
            $kid = ($this->n + 1);
        }
        $this->radiobutton_groups[$this->page][$name][] = array(
            'kid' => $kid,
            'def' => $defval
        );
        $prop                                           = array_merge($this->getFormDefaultProp(), $prop);
        $prop['NoToggleToOff']                          = 'true';
        $prop['Radio']                                  = 'true';
        $prop['borderStyle']                            = 'inset';
        $popt                                           = $this->getAnnotOptFromJSProp($prop);
        $font                                           = 'zapfdingbats';
        $this->AddFont($font);
        $tmpfont                                     = $this->getFontBuffer($font);
        $this->annotation_fonts[$tmpfont['fontkey']] = $tmpfont['i'];
        $fontstyle                                   = sprintf('/F%d %.2F Tf %s', $tmpfont['i'], $this->FontSizePt, $this->TextColor);
        $popt['da']                                  = $fontstyle;
        $popt['ap']                                  = array();
        $popt['ap']['n']                             = array();
        $popt['ap']['n'][$onvalue]                   = 'q BT ' . $fontstyle . ' 0 0 Td (8) Tj ET Q';
        $popt['ap']['n']['Off']                      = 'q BT ' . $fontstyle . ' 0 0 Td (8) Tj ET Q';
        if (!isset($popt['mk'])) {
            $popt['mk'] = array();
        }
        $popt['mk']['ca'] = '(l)';
        $opt              = array_merge($popt, $opt);
        $opt['Subtype']   = 'Widget';
        $opt['ft']        = 'Btn';
        if ($checked) {
            $opt['v']  = array(
                '/' . $onvalue
            );
            $opt['as'] = $onvalue;
        } else {
            $opt['as'] = 'Off';
        }
        $this->Annotation($x, $y, $w, $w, $name, $opt, 0);
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
    }
    public function ListBox($name, $w, $h, $values, $prop = array(), $opt = array(), $x = '', $y = '', $js = false)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        if ($js) {
            $this->_addfield('listbox', $name, $x, $y, $w, $h, $prop);
            $s = '';
            foreach ($values as $value) {
                $s .= "'" . addslashes($value) . "',";
            }
            $this->javascript .= 'f' . $name . '.setItems([' . substr($s, 0, -1) . "]);\n";
            return;
        }
        $prop                                                  = array_merge($this->getFormDefaultProp(), $prop);
        $popt                                                  = $this->getAnnotOptFromJSProp($prop);
        $this->annotation_fonts[$this->CurrentFont['fontkey']] = $this->CurrentFont['i'];
        $fontstyle                                             = sprintf('/F%d %.2F Tf %s', $this->CurrentFont['i'], $this->FontSizePt, $this->TextColor);
        $popt['da']                                            = $fontstyle;
        $popt['ap']                                            = array();
        $popt['ap']['n']                                       = 'q BT ' . $fontstyle . ' ET Q';
        $opt                                                   = array_merge($popt, $opt);
        $opt['Subtype']                                        = 'Widget';
        $opt['ft']                                             = 'Ch';
        $opt['t']                                              = $name;
        $opt['opt']                                            = $values;
        $this->Annotation($x, $y, $w, $h, $name, $opt, 0);
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
    }
    public function ComboBox($name, $w, $h, $values, $prop = array(), $opt = array(), $x = '', $y = '', $js = false)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        if ($js) {
            $this->_addfield('combobox', $name, $x, $y, $w, $h, $prop);
            $s = '';
            foreach ($values as $value) {
                $s .= "'" . addslashes($value) . "',";
            }
            $this->javascript .= 'f' . $name . '.setItems([' . substr($s, 0, -1) . "]);\n";
            return;
        }
        $prop                                                  = array_merge($this->getFormDefaultProp(), $prop);
        $prop['Combo']                                         = true;
        $popt                                                  = $this->getAnnotOptFromJSProp($prop);
        $this->annotation_fonts[$this->CurrentFont['fontkey']] = $this->CurrentFont['i'];
        $fontstyle                                             = sprintf('/F%d %.2F Tf %s', $this->CurrentFont['i'], $this->FontSizePt, $this->TextColor);
        $popt['da']                                            = $fontstyle;
        $popt['ap']                                            = array();
        $popt['ap']['n']                                       = 'q BT ' . $fontstyle . ' ET Q';
        $opt                                                   = array_merge($popt, $opt);
        $opt['Subtype']                                        = 'Widget';
        $opt['ft']                                             = 'Ch';
        $opt['t']                                              = $name;
        $opt['opt']                                            = $values;
        $this->Annotation($x, $y, $w, $h, $name, $opt, 0);
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
    }
    public function CheckBox($name, $w, $checked = false, $prop = array(), $opt = array(), $onvalue = 'Yes', $x = '', $y = '', $js = false)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($w, $x, $y);
        if ($js) {
            $this->_addfield('checkbox', $name, $x, $y, $w, $w, $prop);
            return;
        }
        if (!isset($prop['value'])) {
            $prop['value'] = array(
                'Yes'
            );
        }
        $prop                = array_merge($this->getFormDefaultProp(), $prop);
        $prop['borderStyle'] = 'inset';
        $popt                = $this->getAnnotOptFromJSProp($prop);
        $font                = 'zapfdingbats';
        $this->AddFont($font);
        $tmpfont                                     = $this->getFontBuffer($font);
        $this->annotation_fonts[$tmpfont['fontkey']] = $tmpfont['i'];
        $fontstyle                                   = sprintf('/F%d %.2F Tf %s', $tmpfont['i'], $this->FontSizePt, $this->TextColor);
        $popt['da']                                  = $fontstyle;
        $popt['ap']                                  = array();
        $popt['ap']['n']                             = array();
        $popt['ap']['n']['Yes']                      = 'q BT ' . $fontstyle . ' 0 0 Td (8) Tj ET Q';
        $popt['ap']['n']['Off']                      = 'q BT ' . $fontstyle . ' 0 0 Td (8) Tj ET Q';
        $opt                                         = array_merge($popt, $opt);
        $opt['Subtype']                              = 'Widget';
        $opt['ft']                                   = 'Btn';
        $opt['t']                                    = $name;
        $opt['opt']                                  = array(
            $onvalue
        );
        if ($checked) {
            $opt['v']  = array(
                '/0'
            );
            $opt['as'] = 'Yes';
        } else {
            $opt['v']  = array(
                '/Off'
            );
            $opt['as'] = 'Off';
        }
        $this->Annotation($x, $y, $w, $w, $name, $opt, 0);
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
    }
    public function Button($name, $w, $h, $caption, $action, $prop = array(), $opt = array(), $x = '', $y = '', $js = false)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        if ($js) {
            $this->_addfield('button', $name, $this->x, $this->y, $w, $h, $prop);
            $this->javascript .= 'f' . $name . ".buttonSetCaption('" . addslashes($caption) . "');\n";
            $this->javascript .= 'f' . $name . ".setAction('MouseUp','" . addslashes($action) . "');\n";
            $this->javascript .= 'f' . $name . ".highlight='push';\n";
            $this->javascript .= 'f' . $name . ".print=false;\n";
            return;
        }
        $prop                                                  = array_merge($this->getFormDefaultProp(), $prop);
        $prop['Pushbutton']                                    = 'true';
        $prop['highlight']                                     = 'push';
        $prop['display']                                       = 'display.noPrint';
        $popt                                                  = $this->getAnnotOptFromJSProp($prop);
        $this->annotation_fonts[$this->CurrentFont['fontkey']] = $this->CurrentFont['i'];
        $fontstyle                                             = sprintf('/F%d %.2F Tf %s', $this->CurrentFont['i'], $this->FontSizePt, $this->TextColor);
        $popt['da']                                            = $fontstyle;
        $popt['ap']                                            = array();
        $popt['ap']['n']                                       = 'q BT ' . $fontstyle . ' ET Q';
        if (!isset($popt['mk'])) {
            $popt['mk'] = array();
        }
        $ann_obj_id = ($this->n + 1);
        if (!empty($action) AND !is_array($action)) {
            $ann_obj_id = ($this->n + 2);
        }
        $popt['mk']['ca'] = $this->_textstring($caption, $ann_obj_id);
        $popt['mk']['rc'] = $this->_textstring($caption, $ann_obj_id);
        $popt['mk']['ac'] = $this->_textstring($caption, $ann_obj_id);
        $opt              = array_merge($popt, $opt);
        $opt['Subtype']   = 'Widget';
        $opt['ft']        = 'Btn';
        $opt['t']         = $caption;
        $opt['v']         = $name;
        if (!empty($action)) {
            if (is_array($action)) {
                $opt['aa'] = '/D <<';
                $bmode     = array(
                    'SubmitForm',
                    'ResetForm',
                    'ImportData'
                );
                foreach ($action AS $key => $val) {
                    if (($key == 'S') AND in_array($val, $bmode)) {
                        $opt['aa'] .= ' /S /' . $val;
                    } elseif (($key == 'F') AND (!empty($val))) {
                        $opt['aa'] .= ' /F ' . $this->_datastring($val, $ann_obj_id);
                    } elseif (($key == 'Fields') AND is_array($val) AND !empty($val)) {
                        $opt['aa'] .= ' /Fields [';
                        foreach ($val AS $field) {
                            $opt['aa'] .= ' ' . $this->_textstring($field, $ann_obj_id);
                        }
                        $opt['aa'] .= ']';
                    } elseif (($key == 'Flags')) {
                        $ff = 0;
                        if (is_array($val)) {
                            foreach ($val AS $flag) {
                                switch ($flag) {
                                    case 'Include/Exclude': {
                                        $ff += 1 << 0;
                                        break;
                                    }
                                    case 'IncludeNoValueFields': {
                                        $ff += 1 << 1;
                                        break;
                                    }
                                    case 'ExportFormat': {
                                        $ff += 1 << 2;
                                        break;
                                    }
                                    case 'GetMethod': {
                                        $ff += 1 << 3;
                                        break;
                                    }
                                    case 'SubmitCoordinates': {
                                        $ff += 1 << 4;
                                        break;
                                    }
                                    case 'XFDF': {
                                        $ff += 1 << 5;
                                        break;
                                    }
                                    case 'IncludeAppendSaves': {
                                        $ff += 1 << 6;
                                        break;
                                    }
                                    case 'IncludeAnnotations': {
                                        $ff += 1 << 7;
                                        break;
                                    }
                                    case 'SubmitPDF': {
                                        $ff += 1 << 8;
                                        break;
                                    }
                                    case 'CanonicalFormat': {
                                        $ff += 1 << 9;
                                        break;
                                    }
                                    case 'ExclNonUserAnnots': {
                                        $ff += 1 << 10;
                                        break;
                                    }
                                    case 'ExclFKey': {
                                        $ff += 1 << 11;
                                        break;
                                    }
                                    case 'EmbedForm': {
                                        $ff += 1 << 13;
                                        break;
                                    }
                                }
                            }
                        } else {
                            $ff = intval($val);
                        }
                        $opt['aa'] .= ' /Flags ' . $ff;
                    }
                }
                $opt['aa'] .= ' >>';
            } else {
                $js_obj_id = $this->addJavascriptObject($action);
                $opt['aa'] = '/D ' . $js_obj_id . ' 0 R';
            }
        }
        $this->Annotation($x, $y, $w, $h, $name, $opt, 0);
        if ($this->rtl) {
            $this->x -= $w;
        } else {
            $this->x += $w;
        }
    }
    protected function _putsignature()
    {
        if ((!$this->sign) OR (!isset($this->signature_data['cert_type']))) {
            return;
        }
        $sigobjid = ($this->sig_obj_id + 1);
        $out      = $this->_getobj($sigobjid) . "\n";
        $out .= '<< /Type /Sig';
        $out .= ' /Filter /Adobe.PPKLite';
        $out .= ' /SubFilter /adbe.pkcs7.detached';
        $out .= ' ' . $this->byterange_string;
        $out .= ' /Contents<' . str_repeat('0', $this->signature_max_length) . '>';
        $out .= ' /Reference [';
        $out .= ' << /Type /SigRef';
        if ($this->signature_data['cert_type'] > 0) {
            $out .= ' /TransformMethod /DocMDP';
            $out .= ' /TransformParams <<';
            $out .= ' /Type /TransformParams';
            $out .= ' /P ' . $this->signature_data['cert_type'];
            $out .= ' /V /1.2';
        } else {
            $out .= ' /TransformMethod /UR3';
            $out .= ' /TransformParams <<';
            $out .= ' /Type /TransformParams';
            $out .= ' /V /2.2';
            if (!$this->empty_string($this->ur['document'])) {
                $out .= ' /Document[' . $this->ur['document'] . ']';
            }
            if (!$this->empty_string($this->ur['form'])) {
                $out .= ' /Form[' . $this->ur['form'] . ']';
            }
            if (!$this->empty_string($this->ur['signature'])) {
                $out .= ' /Signature[' . $this->ur['signature'] . ']';
            }
            if (!$this->empty_string($this->ur['annots'])) {
                $out .= ' /Annots[' . $this->ur['annots'] . ']';
            }
            if (!$this->empty_string($this->ur['ef'])) {
                $out .= ' /EF[' . $this->ur['ef'] . ']';
            }
            if (!$this->empty_string($this->ur['formex'])) {
                $out .= ' /FormEX[' . $this->ur['formex'] . ']';
            }
        }
        $out .= ' >>';
        $out .= ' >>';
        $out .= ' ]';
        if (isset($this->signature_data['info']['Name']) AND !$this->empty_string($this->signature_data['info']['Name'])) {
            $out .= ' /Name ' . $this->_textstring($this->signature_data['info']['Name'], $sigobjid);
        }
        if (isset($this->signature_data['info']['Location']) AND !$this->empty_string($this->signature_data['info']['Location'])) {
            $out .= ' /Location ' . $this->_textstring($this->signature_data['info']['Location'], $sigobjid);
        }
        if (isset($this->signature_data['info']['Reason']) AND !$this->empty_string($this->signature_data['info']['Reason'])) {
            $out .= ' /Reason ' . $this->_textstring($this->signature_data['info']['Reason'], $sigobjid);
        }
        if (isset($this->signature_data['info']['ContactInfo']) AND !$this->empty_string($this->signature_data['info']['ContactInfo'])) {
            $out .= ' /ContactInfo ' . $this->_textstring($this->signature_data['info']['ContactInfo'], $sigobjid);
        }
        $out .= ' /M ' . $this->_datestring($sigobjid);
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);
    }
    public function setUserRights($enable = true, $document = '/FullSave', $annots = '/Create/Delete/Modify/Copy/Import/Export', $form = '/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate', $signature = '/Modify', $ef = '/Create/Delete/Modify/Import', $formex = '')
    {
        $this->ur['enabled']   = $enable;
        $this->ur['document']  = $document;
        $this->ur['annots']    = $annots;
        $this->ur['form']      = $form;
        $this->ur['signature'] = $signature;
        $this->ur['ef']        = $ef;
        $this->ur['formex']    = $formex;
        if (!$this->sign) {
            $this->setSignature('', '', '', '', 0, array());
        }
    }
    public function setSignature($signing_cert = '', $private_key = '', $private_key_password = '', $extracerts = '', $cert_type = 2, $info = array())
    {
        $this->sign = true;
        ++$this->n;
        $this->sig_obj_id = $this->n;
        ++$this->n;
        $this->signature_data = array();
        if (strlen($signing_cert) == 0) {
            $signing_cert         = 'file://' . dirname(__FILE__) . '/tcpdf.crt';
            $private_key_password = 'tcpdfdemo';
        }
        if (strlen($private_key) == 0) {
            $private_key = $signing_cert;
        }
        $this->signature_data['signcert']   = $signing_cert;
        $this->signature_data['privkey']    = $private_key;
        $this->signature_data['password']   = $private_key_password;
        $this->signature_data['extracerts'] = $extracerts;
        $this->signature_data['cert_type']  = $cert_type;
        $this->signature_data['info']       = $info;
    }
    public function setSignatureAppearance($x = 0, $y = 0, $w = 0, $h = 0, $page = -1)
    {
        if (($page < 1) OR ($page > $this->numpages)) {
            $this->signature_appearance['page'] = $this->page;
        } else {
            $this->signature_appearance['page'] = intval($page);
        }
        $a                                  = $x * $this->k;
        $b                                  = $this->pagedim[($this->signature_appearance['page'])]['h'] - (($y + $h) * $this->k);
        $c                                  = $w * $this->k;
        $d                                  = $h * $this->k;
        $this->signature_appearance['rect'] = sprintf('%.2F %.2F %.2F %.2F', $a, $b, $a + $c, $b + $d);
    }
    public function startPageGroup($page = '')
    {
        if (empty($page)) {
            $page = $this->page + 1;
        }
        $this->newpagegroup[$page] = sizeof($this->newpagegroup) + 1;
    }
    public function AliasNbPages($s = '')
    {
    }
    public function AliasNumPage($s = '')
    {
    }
    public function setStartingPageNumber($num = 1)
    {
        $this->starting_page_number = max(0, intval($num));
    }
    public function getAliasRightShift()
    {
        $ref   = '{' . $this->alias_right_shift . '}{' . $this->alias_tot_pages . '}{' . $this->alias_num_page . '}';
        $rep   = str_repeat(' ', $this->GetNumChars($ref));
        $wdiff = max(1, ($this->GetStringWidth($ref) / $this->GetStringWidth($rep)));
        $sdiff = sprintf('%.3F', $wdiff);
        $alias = $this->alias_right_shift . $sdiff . '}';
        if ($this->isUnicodeFont()) {
            $alias = '{' . $alias;
        }
        return $alias;
    }
    public function getAliasNbPages()
    {
        if ($this->isUnicodeFont()) {
            return '{' . $this->alias_tot_pages . '}';
        }
        return $this->alias_tot_pages;
    }
    public function getAliasNumPage()
    {
        if ($this->isUnicodeFont()) {
            return '{' . $this->alias_num_page . '}';
        }
        return $this->alias_num_page;
    }
    public function getPageGroupAlias()
    {
        if ($this->isUnicodeFont()) {
            return '{' . $this->alias_group_tot_pages . '}';
        }
        return $this->alias_group_tot_pages;
    }
    public function getPageNumGroupAlias()
    {
        if ($this->isUnicodeFont()) {
            return '{' . $this->alias_group_num_page . '}';
        }
        return $this->alias_group_num_page;
    }
    public function getGroupPageNo()
    {
        return $this->pagegroups[$this->currpagegroup];
    }
    public function getGroupPageNoFormatted()
    {
        return $this->formatPageNumber($this->getGroupPageNo());
    }
    protected function formatPageNumber($num)
    {
        return number_format((float) $num, 0, '', '.');
    }
    protected function formatTOCPageNumber($num)
    {
        return number_format((float) $num, 0, '', '.');
    }
    public function PageNoFormatted()
    {
        return $this->formatPageNumber($this->PageNo());
    }
    protected function _putocg()
    {
        if ($this->pdflayers) {
            $this->n_ocg_print = $this->_newobj();
            $this->_out('<< /Type /OCG /Name ' . $this->_textstring('print', $this->n_ocg_print) . ' /Usage << /Print <</PrintState /ON>> /View <</ViewState /OFF>> >> >>' . "\n" . 'endobj');
            $this->n_ocg_view = $this->_newobj();
            $this->_out('<< /Type /OCG /Name ' . $this->_textstring('view', $this->n_ocg_view) . ' /Usage << /Print <</PrintState /OFF>> /View <</ViewState /ON>> >> >>' . "\n" . 'endobj');
        }
    }
    public function setVisibility($v)
    {
        if ($this->openMarkedContent) {
            $this->_out('EMC');
            $this->openMarkedContent = false;
        }
        switch ($v) {
            case 'print': {
                $this->_out('/OC /OC1 BDC');
                $this->openMarkedContent = true;
                $this->pdflayers         = true;
                break;
            }
            case 'screen': {
                $this->_out('/OC /OC2 BDC');
                $this->openMarkedContent = true;
                $this->pdflayers         = true;
                break;
            }
            case 'all': {
                $this->_out('');
                break;
            }
            default: {
                $this->Error('Incorrect visibility: ' . $v);
                break;
            }
        }
        $this->visibility = $v;
    }
    protected function addExtGState($parms)
    {
        $n = count($this->extgstates) + 1;
        for ($i = 1; $i < $n; ++$i) {
            if ($this->extgstates[$i]['parms'] == $parms) {
                return $i;
            }
        }
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }
    protected function setExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }
    protected function _putextgstates()
    {
        $ne = count($this->extgstates);
        for ($i = 1; $i <= $ne; ++$i) {
            $this->extgstates[$i]['n'] = $this->_newobj();
            $out                       = '<< /Type /ExtGState';
            foreach ($this->extgstates[$i]['parms'] as $k => $v) {
                if (is_float($v)) {
                    $v = sprintf('%.2F', $v);
                }
                $out .= ' /' . $k . ' ' . $v;
            }
            $out .= ' >>';
            $out .= "\n" . 'endobj';
            $this->_out($out);
        }
    }
    public function setAlpha($alpha, $bm = 'Normal')
    {
        $gs = $this->addExtGState(array(
            'ca' => $alpha,
            'CA' => $alpha,
            'BM' => '/' . $bm,
            'AIS' => 'false'
        ));
        $this->setExtGState($gs);
    }
    public function setJPEGQuality($quality)
    {
        if (($quality < 1) OR ($quality > 100)) {
            $quality = 75;
        }
        $this->jpeg_quality = intval($quality);
    }
    public function setDefaultTableColumns($cols = 4)
    {
        $this->default_table_columns = intval($cols);
    }
    public function setCellHeightRatio($h)
    {
        $this->cell_height_ratio = $h;
    }
    public function getCellHeightRatio()
    {
        return $this->cell_height_ratio;
    }
    public function setPDFVersion($version = '1.7')
    {
        $this->PDFVersion = $version;
    }
    public function setViewerPreferences($preferences)
    {
        $this->viewer_preferences = $preferences;
    }
    public function colorRegistrationBar($x, $y, $w, $h, $transition = true, $vertical = false, $colors = 'A,R,G,B,C,M,Y,K')
    {
        $bars    = explode(',', $colors);
        $numbars = count($bars);
        if ($vertical) {
            $coords = array(
                0,
                0,
                0,
                1
            );
            $wb     = $w / $numbars;
            $hb     = $h;
            $xd     = $wb;
            $yd     = 0;
        } else {
            $coords = array(
                1,
                0,
                0,
                0
            );
            $wb     = $w;
            $hb     = $h / $numbars;
            $xd     = 0;
            $yd     = $hb;
        }
        $xb = $x;
        $yb = $y;
        foreach ($bars as $col) {
            switch ($col) {
                case 'A': {
                    $col_a = array(
                        255
                    );
                    $col_b = array(
                        0
                    );
                    break;
                }
                case 'W': {
                    $col_a = array(
                        0
                    );
                    $col_b = array(
                        255
                    );
                    break;
                }
                case 'R': {
                    $col_a = array(
                        255,
                        255,
                        255
                    );
                    $col_b = array(
                        255,
                        0,
                        0
                    );
                    break;
                }
                case 'G': {
                    $col_a = array(
                        255,
                        255,
                        255
                    );
                    $col_b = array(
                        0,
                        255,
                        0
                    );
                    break;
                }
                case 'B': {
                    $col_a = array(
                        255,
                        255,
                        255
                    );
                    $col_b = array(
                        0,
                        0,
                        255
                    );
                    break;
                }
                case 'C': {
                    $col_a = array(
                        0,
                        0,
                        0,
                        0
                    );
                    $col_b = array(
                        100,
                        0,
                        0,
                        0
                    );
                    break;
                }
                case 'M': {
                    $col_a = array(
                        0,
                        0,
                        0,
                        0
                    );
                    $col_b = array(
                        0,
                        100,
                        0,
                        0
                    );
                    break;
                }
                case 'Y': {
                    $col_a = array(
                        0,
                        0,
                        0,
                        0
                    );
                    $col_b = array(
                        0,
                        0,
                        100,
                        0
                    );
                    break;
                }
                case 'K': {
                    $col_a = array(
                        0,
                        0,
                        0,
                        0
                    );
                    $col_b = array(
                        0,
                        0,
                        0,
                        100
                    );
                    break;
                }
                default: {
                    $col_a = array(
                        255
                    );
                    $col_b = array(
                        0
                    );
                    break;
                }
            }
            if ($transition) {
                $this->LinearGradient($xb, $yb, $wb, $hb, $col_a, $col_b, $coords);
            } else {
                $this->SetFillColorArray($col_b);
                $this->Rect($xb, $yb, $wb, $hb, 'F', array());
            }
            $xb += $xd;
            $yb += $yd;
        }
    }
    public function cropMark($x, $y, $w, $h, $type = 'A,B,C,D', $color = array(0, 0, 0))
    {
        $this->SetLineStyle(array(
            'width' => (0.5 / $this->k),
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => $color
        ));
        $crops    = explode(',', $type);
        $numcrops = count($crops);
        $dw       = $w / 4;
        $dh       = $h / 4;
        foreach ($crops as $crop) {
            switch ($crop) {
                case 'A': {
                    $x1 = $x;
                    $y1 = $y - $h;
                    $x2 = $x;
                    $y2 = $y - $dh;
                    $x3 = $x - $w;
                    $y3 = $y;
                    $x4 = $x - $dw;
                    $y4 = $y;
                    break;
                }
                case 'B': {
                    $x1 = $x;
                    $y1 = $y - $h;
                    $x2 = $x;
                    $y2 = $y - $dh;
                    $x3 = $x + $dw;
                    $y3 = $y;
                    $x4 = $x + $w;
                    $y4 = $y;
                    break;
                }
                case 'C': {
                    $x1 = $x - $w;
                    $y1 = $y;
                    $x2 = $x - $dw;
                    $y2 = $y;
                    $x3 = $x;
                    $y3 = $y + $dh;
                    $x4 = $x;
                    $y4 = $y + $h;
                    break;
                }
                case 'D': {
                    $x1 = $x + $dw;
                    $y1 = $y;
                    $x2 = $x + $w;
                    $y2 = $y;
                    $x3 = $x;
                    $y3 = $y + $dh;
                    $x4 = $x;
                    $y4 = $y + $h;
                    break;
                }
            }
            $this->Line($x1, $y1, $x2, $y2);
            $this->Line($x3, $y3, $x4, $y4);
        }
    }
    public function registrationMark($x, $y, $r, $double = false, $cola = array(0, 0, 0), $colb = array(255, 255, 255))
    {
        $line_style = array(
            'width' => (0.5 / $this->k),
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => $cola
        );
        $this->SetFillColorArray($cola);
        $this->PieSector($x, $y, $r, 90, 180, 'F');
        $this->PieSector($x, $y, $r, 270, 360, 'F');
        $this->Circle($x, $y, $r, 0, 360, 'C', $line_style, array(), 8);
        if ($double) {
            $r2 = $r * 0.5;
            $this->SetFillColorArray($colb);
            $this->PieSector($x, $y, $r2, 90, 180, 'F');
            $this->PieSector($x, $y, $r2, 270, 360, 'F');
            $this->SetFillColorArray($cola);
            $this->PieSector($x, $y, $r2, 0, 90, 'F');
            $this->PieSector($x, $y, $r2, 180, 270, 'F');
            $this->Circle($x, $y, $r2, 0, 360, 'C', $line_style, array(), 8);
        }
    }
    public function LinearGradient($x, $y, $w, $h, $col1 = array(), $col2 = array(), $coords = array(0, 0, 1, 0))
    {
        $this->Clip($x, $y, $w, $h);
        $this->Gradient(2, $coords, array(
            array(
                'color' => $col1,
                'offset' => 0,
                'exponent' => 1
            ),
            array(
                'color' => $col2,
                'offset' => 1,
                'exponent' => 1
            )
        ), array(), false);
    }
    public function RadialGradient($x, $y, $w, $h, $col1 = array(), $col2 = array(), $coords = array(0.5, 0.5, 0.5, 0.5, 1))
    {
        $this->Clip($x, $y, $w, $h);
        $this->Gradient(3, $coords, array(
            array(
                'color' => $col1,
                'offset' => 0,
                'exponent' => 1
            ),
            array(
                'color' => $col2,
                'offset' => 1,
                'exponent' => 1
            )
        ), array(), false);
    }
    public function CoonsPatchMesh($x, $y, $w, $h, $col1 = array(), $col2 = array(), $col3 = array(), $col4 = array(), $coords = array(0.00, 0.0, 0.33, 0.00, 0.67, 0.00, 1.00, 0.00, 1.00, 0.33, 1.00, 0.67, 1.00, 1.00, 0.67, 1.00, 0.33, 1.00, 0.00, 1.00, 0.00, 0.67, 0.00, 0.33), $coords_min = 0, $coords_max = 1, $antialias = false)
    {
        $this->Clip($x, $y, $w, $h);
        $n                                   = count($this->gradients) + 1;
        $this->gradients[$n]                 = array();
        $this->gradients[$n]['type']         = 6;
        $this->gradients[$n]['coords']       = array();
        $this->gradients[$n]['antialias']    = $antialias;
        $this->gradients[$n]['colors']       = array();
        $this->gradients[$n]['transparency'] = false;
        if (!isset($coords[0]['f'])) {
            if (!isset($col1[1])) {
                $col1[1] = $col1[2] = $col1[0];
            }
            if (!isset($col2[1])) {
                $col2[1] = $col2[2] = $col2[0];
            }
            if (!isset($col3[1])) {
                $col3[1] = $col3[2] = $col3[0];
            }
            if (!isset($col4[1])) {
                $col4[1] = $col4[2] = $col4[0];
            }
            $patch_array[0]['f']              = 0;
            $patch_array[0]['points']         = $coords;
            $patch_array[0]['colors'][0]['r'] = $col1[0];
            $patch_array[0]['colors'][0]['g'] = $col1[1];
            $patch_array[0]['colors'][0]['b'] = $col1[2];
            $patch_array[0]['colors'][1]['r'] = $col2[0];
            $patch_array[0]['colors'][1]['g'] = $col2[1];
            $patch_array[0]['colors'][1]['b'] = $col2[2];
            $patch_array[0]['colors'][2]['r'] = $col3[0];
            $patch_array[0]['colors'][2]['g'] = $col3[1];
            $patch_array[0]['colors'][2]['b'] = $col3[2];
            $patch_array[0]['colors'][3]['r'] = $col4[0];
            $patch_array[0]['colors'][3]['g'] = $col4[1];
            $patch_array[0]['colors'][3]['b'] = $col4[2];
        } else {
            $patch_array = $coords;
        }
        $bpcd                          = 65535;
        $this->gradients[$n]['stream'] = '';
        $count_patch                   = count($patch_array);
        for ($i = 0; $i < $count_patch; ++$i) {
            $this->gradients[$n]['stream'] .= chr($patch_array[$i]['f']);
            $count_points = count($patch_array[$i]['points']);
            for ($j = 0; $j < $count_points; ++$j) {
                $patch_array[$i]['points'][$j] = (($patch_array[$i]['points'][$j] - $coords_min) / ($coords_max - $coords_min)) * $bpcd;
                if ($patch_array[$i]['points'][$j] < 0) {
                    $patch_array[$i]['points'][$j] = 0;
                }
                if ($patch_array[$i]['points'][$j] > $bpcd) {
                    $patch_array[$i]['points'][$j] = $bpcd;
                }
                $this->gradients[$n]['stream'] .= chr(floor($patch_array[$i]['points'][$j] / 256));
                $this->gradients[$n]['stream'] .= chr(floor($patch_array[$i]['points'][$j] % 256));
            }
            $count_cols = count($patch_array[$i]['colors']);
            for ($j = 0; $j < $count_cols; ++$j) {
                $this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['r']);
                $this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['g']);
                $this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['b']);
            }
        }
        $this->_out('/Sh' . $n . ' sh');
        $this->_out('Q');
    }
    protected function Clip($x, $y, $w, $h)
    {
        if ($this->rtl) {
            $x = $this->w - $x - $w;
        }
        $s = 'q';
        $s .= sprintf(' %.2F %.2F %.2F %.2F re W n', $x * $this->k, ($this->h - $y) * $this->k, $w * $this->k, -$h * $this->k);
        $s .= sprintf(' %.3F 0 0 %.3F %.3F %.3F cm', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k);
        $this->_out($s);
    }
    public function Gradient($type, $coords, $stops, $background = array(), $antialias = false)
    {
        $n                                   = count($this->gradients) + 1;
        $this->gradients[$n]                 = array();
        $this->gradients[$n]['type']         = $type;
        $this->gradients[$n]['coords']       = $coords;
        $this->gradients[$n]['antialias']    = $antialias;
        $this->gradients[$n]['colors']       = array();
        $this->gradients[$n]['transparency'] = false;
        $numcolspace                         = count($stops[0]['color']);
        $bcolor                              = array_values($background);
        switch ($numcolspace) {
            case 4: {
                $this->gradients[$n]['colspace'] = 'DeviceCMYK';
                if (!empty($background)) {
                    $this->gradients[$n]['background'] = sprintf('%.3F %.3F %.3F %.3F', $bcolor[0] / 100, $bcolor[1] / 100, $bcolor[2] / 100, $bcolor[3] / 100);
                }
                break;
            }
            case 3: {
                $this->gradients[$n]['colspace'] = 'DeviceRGB';
                if (!empty($background)) {
                    $this->gradients[$n]['background'] = sprintf('%.3F %.3F %.3F', $bcolor[0] / 255, $bcolor[1] / 255, $bcolor[2] / 255);
                }
                break;
            }
            case 1: {
                $this->gradients[$n]['colspace'] = 'DeviceGray';
                if (!empty($background)) {
                    $this->gradients[$n]['background'] = sprintf('%.3F', $bcolor[0] / 255);
                }
                break;
            }
        }
        $num_stops    = count($stops);
        $last_stop_id = $num_stops - 1;
        foreach ($stops as $key => $stop) {
            $this->gradients[$n]['colors'][$key] = array();
            if (isset($stop['offset'])) {
                $this->gradients[$n]['colors'][$key]['offset'] = $stop['offset'];
            } else {
                if ($key == 0) {
                    $this->gradients[$n]['colors'][$key]['offset'] = 0;
                } elseif ($key == $last_stop_id) {
                    $this->gradients[$n]['colors'][$key]['offset'] = 1;
                } else {
                    $offsetstep                                    = (1 - $this->gradients[$n]['colors'][($key - 1)]['offset']) / ($num_stops - $key);
                    $this->gradients[$n]['colors'][$key]['offset'] = $this->gradients[$n]['colors'][($key - 1)]['offset'] + $offsetstep;
                }
            }
            if (isset($stop['opacity'])) {
                $this->gradients[$n]['colors'][$key]['opacity'] = $stop['opacity'];
                if ($stop['opacity'] < 1) {
                    $this->gradients[$n]['transparency'] = true;
                }
            } else {
                $this->gradients[$n]['colors'][$key]['opacity'] = 1;
            }
            if (isset($stop['exponent'])) {
                $this->gradients[$n]['colors'][$key]['exponent'] = $stop['exponent'];
            } else {
                $this->gradients[$n]['colors'][$key]['exponent'] = 1;
            }
            $color = array_values($stop['color']);
            switch ($numcolspace) {
                case 4: {
                    $this->gradients[$n]['colors'][$key]['color'] = sprintf('%.3F %.3F %.3F %.3F', $color[0] / 100, $color[1] / 100, $color[2] / 100, $color[3] / 100);
                    break;
                }
                case 3: {
                    $this->gradients[$n]['colors'][$key]['color'] = sprintf('%.3F %.3F %.3F', $color[0] / 255, $color[1] / 255, $color[2] / 255);
                    break;
                }
                case 1: {
                    $this->gradients[$n]['colors'][$key]['color'] = sprintf('%.3F', $color[0] / 255);
                    break;
                }
            }
        }
        if ($this->gradients[$n]['transparency']) {
            $this->_out('/TGS' . $n . ' gs');
        }
        $this->_out('/Sh' . $n . ' sh');
        $this->_out('Q');
    }
    function _putshaders()
    {
        $idt = count($this->gradients);
        foreach ($this->gradients as $id => $grad) {
            if (($grad['type'] == 2) OR ($grad['type'] == 3)) {
                $fc  = $this->_newobj();
                $out = '<<';
                $out .= ' /FunctionType 3';
                $out .= ' /Domain [0 1]';
                $functions = '';
                $bounds    = '';
                $encode    = '';
                $i         = 1;
                $num_cols  = count($grad['colors']);
                $lastcols  = $num_cols - 1;
                for ($i = 1; $i < $num_cols; ++$i) {
                    $functions .= ($fc + $i) . ' 0 R ';
                    if ($i < $lastcols) {
                        $bounds .= sprintf('%.3F ', $grad['colors'][$i]['offset']);
                    }
                    $encode .= '0 1 ';
                }
                $out .= ' /Functions [' . trim($functions) . ']';
                $out .= ' /Bounds [' . trim($bounds) . ']';
                $out .= ' /Encode [' . trim($encode) . ']';
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
                for ($i = 1; $i < $num_cols; ++$i) {
                    $this->_newobj();
                    $out = '<<';
                    $out .= ' /FunctionType 2';
                    $out .= ' /Domain [0 1]';
                    $out .= ' /C0 [' . $grad['colors'][($i - 1)]['color'] . ']';
                    $out .= ' /C1 [' . $grad['colors'][$i]['color'] . ']';
                    $out .= ' /N ' . $grad['colors'][$i]['exponent'];
                    $out .= ' >>';
                    $out .= "\n" . 'endobj';
                    $this->_out($out);
                }
                if ($grad['transparency']) {
                    $ft  = $this->_newobj();
                    $out = '<<';
                    $out .= ' /FunctionType 3';
                    $out .= ' /Domain [0 1]';
                    $functions = '';
                    $i         = 1;
                    $num_cols  = count($grad['colors']);
                    for ($i = 1; $i < $num_cols; ++$i) {
                        $functions .= ($ft + $i) . ' 0 R ';
                    }
                    $out .= ' /Functions [' . trim($functions) . ']';
                    $out .= ' /Bounds [' . trim($bounds) . ']';
                    $out .= ' /Encode [' . trim($encode) . ']';
                    $out .= ' >>';
                    $out .= "\n" . 'endobj';
                    $this->_out($out);
                    for ($i = 1; $i < $num_cols; ++$i) {
                        $this->_newobj();
                        $out = '<<';
                        $out .= ' /FunctionType 2';
                        $out .= ' /Domain [0 1]';
                        $out .= ' /C0 [' . $grad['colors'][($i - 1)]['opacity'] . ']';
                        $out .= ' /C1 [' . $grad['colors'][$i]['opacity'] . ']';
                        $out .= ' /N ' . $grad['colors'][$i]['exponent'];
                        $out .= ' >>';
                        $out .= "\n" . 'endobj';
                        $this->_out($out);
                    }
                }
            }
            $this->_newobj();
            $out = '<< /ShadingType ' . $grad['type'];
            if (isset($grad['colspace'])) {
                $out .= ' /ColorSpace /' . $grad['colspace'];
            } else {
                $out .= ' /ColorSpace /DeviceRGB';
            }
            if (isset($grad['background']) AND !empty($grad['background'])) {
                $out .= ' /Background [' . $grad['background'] . ']';
            }
            if (isset($grad['antialias']) AND ($grad['antialias'] === true)) {
                $out .= ' /AntiAlias true';
            }
            if ($grad['type'] == 2) {
                $out .= ' ' . sprintf('/Coords [%.3F %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3]);
                $out .= ' /Domain [0 1]';
                $out .= ' /Function ' . $fc . ' 0 R';
                $out .= ' /Extend [true true]';
                $out .= ' >>';
            } elseif ($grad['type'] == 3) {
                $out .= ' ' . sprintf('/Coords [%.3F %.3F 0 %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3], $grad['coords'][4]);
                $out .= ' /Domain [0 1]';
                $out .= ' /Function ' . $fc . ' 0 R';
                $out .= ' /Extend [true true]';
                $out .= ' >>';
            } elseif ($grad['type'] == 6) {
                $out .= ' /BitsPerCoordinate 16';
                $out .= ' /BitsPerComponent 8';
                $out .= ' /Decode[0 1 0 1 0 1 0 1 0 1]';
                $out .= ' /BitsPerFlag 8';
                $stream = $this->_getrawstream($grad['stream']);
                $out .= ' /Length ' . strlen($stream);
                $out .= ' >>';
                $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
            }
            $out .= "\n" . 'endobj';
            $this->_out($out);
            if ($grad['transparency']) {
                $shading_transparency = preg_replace('/\/ColorSpace \/[^\s]+/si', '/ColorSpace /DeviceGray', $out);
                $shading_transparency = preg_replace('/\/Function [0-9]+ /si', '/Function ' . $ft . ' ', $shading_transparency);
            }
            $this->gradients[$id]['id'] = $this->n;
            $this->_newobj();
            $out = '<< /Type /Pattern /PatternType 2';
            $out .= ' /Shading ' . $this->gradients[$id]['id'] . ' 0 R';
            $out .= ' >>';
            $out .= "\n" . 'endobj';
            $this->_out($out);
            $this->gradients[$id]['pattern'] = $this->n;
            if ($grad['transparency']) {
                $idgs = $id + $idt;
                $this->_newobj();
                $this->_out($shading_transparency);
                $this->gradients[$idgs]['id'] = $this->n;
                $this->_newobj();
                $out = '<< /Type /Pattern /PatternType 2';
                $out .= ' /Shading ' . $this->gradients[$idgs]['id'] . ' 0 R';
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
                $this->gradients[$idgs]['pattern'] = $this->n;
                $oid                               = $this->_newobj();
                $this->xobjects['LX' . $oid]       = array(
                    'n' => $oid
                );
                $filter                            = '';
                $stream                            = 'q /a0 gs /Pattern cs /p' . $idgs . ' scn 0 0 ' . $this->wPt . ' ' . $this->hPt . ' re f Q';
                if ($this->compress) {
                    $filter = ' /Filter /FlateDecode';
                    $stream = gzcompress($stream);
                }
                $stream = $this->_getrawstream($stream);
                $out    = '<< /Type /XObject /Subtype /Form /FormType 1' . $filter;
                $out .= ' /Length ' . strlen($stream);
                $rect = sprintf('%.2F %.2F', $this->wPt, $this->hPt);
                $out .= ' /BBox [0 0 ' . $rect . ']';
                $out .= ' /Group << /Type /Group /S /Transparency /CS /DeviceGray >>';
                $out .= ' /Resources <<';
                $out .= ' /ExtGState << /a0 << /ca 1 /CA 1 >> >>';
                $out .= ' /Pattern << /p' . $idgs . ' ' . $this->gradients[$idgs]['pattern'] . ' 0 R >>';
                $out .= ' >>';
                $out .= ' >> ';
                $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
                $out .= "\n" . 'endobj';
                $this->_out($out);
                $this->_newobj();
                $out = '<< /Type /Mask /S /Luminosity /G ' . ($this->n - 1) . ' 0 R >>' . "\n" . 'endobj';
                $this->_out($out);
                $this->_newobj();
                $out = '<< /Type /ExtGState /SMask ' . ($this->n - 1) . ' 0 R /AIS false >>' . "\n" . 'endobj';
                $this->_out($out);
                $this->extgstates[] = array(
                    'n' => $this->n,
                    'name' => 'TGS' . $id
                );
            }
        }
    }
    public function PieSector($xc, $yc, $r, $a, $b, $style = 'FD', $cw = true, $o = 90)
    {
        $this->PieSectorXY($xc, $yc, $r, $r, $a, $b, $style, $cw, $o);
    }
    public function PieSectorXY($xc, $yc, $rx, $ry, $a, $b, $style = 'FD', $cw = false, $o = 0, $nc = 2)
    {
        if ($this->rtl) {
            $xc = $this->w - $xc;
        }
        $op = $this->getPathPaintOperator($style);
        if ($op == 'f') {
            $line_style = array();
        }
        if ($cw) {
            $d = $b;
            $b = 360 - $a + $o;
            $a = 360 - $d + $o;
        } else {
            $b += $o;
            $a += $o;
        }
        $this->_outellipticalarc($xc, $yc, $rx, $ry, 0, $a, $b, true, $nc);
        $this->_out($op);
    }
    public function ImageEps($file, $x = '', $y = '', $w = 0, $h = 0, $link = '', $useBoundingBox = true, $align = '', $palign = '', $border = 0, $fitonpage = false, $fixoutvals = false)
    {
        if ($this->rasterize_vector_images AND ($w > 0) AND ($h > 0)) {
            return $this->Image($file, $x, $y, $w, $h, 'EPS', $link, $align, true, 300, $palign, false, false, $border, false, false, $fitonpage);
        }
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        $k = $this->k;
        if ($file{0} === '@') {
            $data = substr($file, 1);
        } else {
            $data = file_get_contents($file);
        }
        if ($data === false) {
            $this->Error('EPS file not found: ' . $file);
        }
        $regs = array();
        preg_match("/%%Creator:([^\r\n]+)/", $data, $regs);
        if (count($regs) > 1) {
            $version_str = trim($regs[1]);
            if (strpos($version_str, 'Adobe Illustrator') !== false) {
                $versexp = explode(' ', $version_str);
                $version = (float) array_pop($versexp);
                if ($version >= 9) {
                    $this->Error('This version of Adobe Illustrator file is not supported: ' . $file);
                }
            }
        }
        $start = strpos($data, '%!PS-Adobe');
        if ($start > 0) {
            $data = substr($data, $start);
        }
        preg_match("/%%BoundingBox:([^\r\n]+)/", $data, $regs);
        if (count($regs) > 1) {
            list($x1, $y1, $x2, $y2) = explode(' ', trim($regs[1]));
        } else {
            $this->Error('No BoundingBox found in EPS/AI file: ' . $file);
        }
        $start = strpos($data, '%%EndSetup');
        if ($start === false) {
            $start = strpos($data, '%%EndProlog');
        }
        if ($start === false) {
            $start = strpos($data, '%%BoundingBox');
        }
        $data = substr($data, $start);
        $end  = strpos($data, '%%PageTrailer');
        if ($end === false) {
            $end = strpos($data, 'showpage');
        }
        if ($end) {
            $data = substr($data, 0, $end);
        }
        if (($w <= 0) AND ($h <= 0)) {
            $w = ($x2 - $x1) / $k;
            $h = ($y2 - $y1) / $k;
        } elseif ($w <= 0) {
            $w = ($x2 - $x1) / $k * ($h / (($y2 - $y1) / $k));
        } elseif ($h <= 0) {
            $h = ($y2 - $y1) / $k * ($w / (($x2 - $x1) / $k));
        }
        list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, $fitonpage);
        if ($this->rasterize_vector_images) {
            return $this->Image($file, $x, $y, $w, $h, 'EPS', $link, $align, true, 300, $palign, false, false, $border, false, false, $fitonpage);
        }
        $scale_x        = $w / (($x2 - $x1) / $k);
        $scale_y        = $h / (($y2 - $y1) / $k);
        $this->img_rb_y = $y + $h;
        if ($this->rtl) {
            if ($palign == 'L') {
                $ximg = $this->lMargin;
            } elseif ($palign == 'C') {
                $ximg = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $ximg = $this->w - $this->rMargin - $w;
            } else {
                $ximg = $x - $w;
            }
            $this->img_rb_x = $ximg;
        } else {
            if ($palign == 'L') {
                $ximg = $this->lMargin;
            } elseif ($palign == 'C') {
                $ximg = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $ximg = $this->w - $this->rMargin - $w;
            } else {
                $ximg = $x;
            }
            $this->img_rb_x = $ximg + $w;
        }
        if ($useBoundingBox) {
            $dx = $ximg * $k - $x1;
            $dy = $y * $k - $y1;
        } else {
            $dx = $ximg * $k;
            $dy = $y * $k;
        }
        $this->_out('q' . $this->epsmarker);
        $this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', 1, 0, 0, 1, $dx, $dy + ($this->hPt - (2 * $y * $k) - ($y2 - $y1))));
        if (isset($scale_x)) {
            $this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $scale_x, 0, 0, $scale_y, $x1 * (1 - $scale_x), $y2 * (1 - $scale_y)));
        }
        $lines = preg_split('/[\r\n]+/si', $data, -1, PREG_SPLIT_NO_EMPTY);
        $u     = 0;
        $cnt   = count($lines);
        for ($i = 0; $i < $cnt; ++$i) {
            $line = $lines[$i];
            if (($line == '') OR ($line{0} == '%')) {
                continue;
            }
            $len        = strlen($line);
            $color_name = '';
            if (strcasecmp('x', substr(trim($line), -1)) == 0) {
                if (preg_match('/\([^\)]*\)/', $line, $matches) > 0) {
                    $color_name = $matches[0];
                    $line       = str_replace(' ' . $color_name, '', $line);
                    $color_name = substr($color_name, 1, -1);
                }
            }
            $chunks = explode(' ', $line);
            $cmd    = trim(array_pop($chunks));
            if (($cmd == 'Xa') OR ($cmd == 'XA')) {
                $b = array_pop($chunks);
                $g = array_pop($chunks);
                $r = array_pop($chunks);
                $this->_out('' . $r . ' ' . $g . ' ' . $b . ' ' . ($cmd == 'Xa' ? 'rg' : 'RG'));
                continue;
            }
            $skip = false;
            if ($fixoutvals) {
                switch ($cmd) {
                    case 'm':
                    case 'l':
                    case 'L': {
                        foreach ($chunks as $key => $val) {
                            if ((($key % 2) == 0) AND (($val < $x1) OR ($val > $x2))) {
                                $skip = true;
                            } elseif ((($key % 2) != 0) AND (($val < $y1) OR ($val > $y2))) {
                                $skip = true;
                            }
                        }
                    }
                }
            }
            switch ($cmd) {
                case 'm':
                case 'l':
                case 'v':
                case 'y':
                case 'c':
                case 'k':
                case 'K':
                case 'g':
                case 'G':
                case 's':
                case 'S':
                case 'J':
                case 'j':
                case 'w':
                case 'M':
                case 'd':
                case 'n': {
                    if ($skip) {
                        break;
                    }
                    $this->_out($line);
                    break;
                }
                case 'x': {
                    if (empty($color_name)) {
                        list($col_c, $col_m, $col_y, $col_k) = $chunks;
                        $this->_out('' . $col_c . ' ' . $col_m . ' ' . $col_y . ' ' . $col_k . ' k');
                    } else {
                        list($col_c, $col_m, $col_y, $col_k, $col_t) = $chunks;
                        $this->AddSpotColor($color_name, ($col_c * 100), ($col_m * 100), ($col_y * 100), ($col_k * 100));
                        $color_cmd = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$color_name]['i'], (1 - $col_t));
                        $this->_out($color_cmd);
                    }
                    break;
                }
                case 'X': {
                    if (empty($color_name)) {
                        list($col_c, $col_m, $col_y, $col_k) = $chunks;
                        $this->_out('' . $col_c . ' ' . $col_m . ' ' . $col_y . ' ' . $col_k . ' K');
                    } else {
                        list($col_c, $col_m, $col_y, $col_k, $col_t) = $chunks;
                        $this->AddSpotColor($color_name, ($col_c * 100), ($col_m * 100), ($col_y * 100), ($col_k * 100));
                        $color_cmd = sprintf('/CS%d CS %.3F SCN', $this->spot_colors[$color_name]['i'], (1 - $col_t));
                        $this->_out($color_cmd);
                    }
                    break;
                }
                case 'Y':
                case 'N':
                case 'V':
                case 'L':
                case 'C': {
                    if ($skip) {
                        break;
                    }
                    $line{$len - 1} = strtolower($cmd);
                    $this->_out($line);
                    break;
                }
                case 'b':
                case 'B': {
                    $this->_out($cmd . '*');
                    break;
                }
                case 'f':
                case 'F': {
                    if ($u > 0) {
                        $isU = false;
                        $max = min(($i + 5), $cnt);
                        for ($j = ($i + 1); $j < $max; ++$j) {
                            $isU = ($isU OR (($lines[$j] == 'U') OR ($lines[$j] == '*U')));
                        }
                        if ($isU) {
                            $this->_out('f*');
                        }
                    } else {
                        $this->_out('f*');
                    }
                    break;
                }
                case '*u': {
                    ++$u;
                    break;
                }
                case '*U': {
                    --$u;
                    break;
                }
            }
        }
        $this->_out($this->epsmarker . 'Q');
        if (!empty($border)) {
            $bx      = $this->x;
            $by      = $this->y;
            $this->x = $ximg;
            if ($this->rtl) {
                $this->x += $w;
            }
            $this->y = $y;
            $this->Cell($w, $h, '', $border, 0, '', 0, '', 0, true);
            $this->x = $bx;
            $this->y = $by;
        }
        if ($link) {
            $this->Link($ximg, $y, $w, $h, $link, 0);
        }
        switch ($align) {
            case 'T': {
                $this->y = $y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'M': {
                $this->y = $y + round($h / 2);
                $this->x = $this->img_rb_x;
                break;
            }
            case 'B': {
                $this->y = $this->img_rb_y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'N': {
                $this->SetY($this->img_rb_y);
                break;
            }
            default: {
                break;
            }
        }
        $this->endlinex = $this->img_rb_x;
    }
    public function setBarcode($bc = '')
    {
        $this->barcode = $bc;
    }
    public function getBarcode()
    {
        return $this->barcode;
    }
    public function write1DBarcode($code, $type, $x = '', $y = '', $w = '', $h = '', $xres = '', $style = '', $align = '')
    {
        if ($this->empty_string(trim($code))) {
            return;
        }
        require_once(dirname(__FILE__) . '/barcodes.php');
        $gvars      = $this->getGraphicVars();
        $barcodeobj = new TCPDFBarcode($code, $type);
        $arrcode    = $barcodeobj->getBarcodeArray();
        if ($arrcode === false) {
            $this->Error('Error in 1D barcode string');
        }
        if (!isset($style['position'])) {
            $style['position'] = '';
        } elseif ($style['position'] == 'S') {
            $style['position'] = '';
            $style['stretch']  = true;
        }
        if (!isset($style['fitwidth'])) {
            if (!isset($style['stretch'])) {
                $style['fitwidth'] = true;
            } else {
                $style['fitwidth'] = false;
            }
        }
        if ($style['fitwidth']) {
            $style['stretch'] = false;
        }
        if (!isset($style['stretch'])) {
            if (($w === '') OR ($w <= 0)) {
                $style['stretch'] = false;
            } else {
                $style['stretch'] = true;
            }
        }
        if (!isset($style['fgcolor'])) {
            $style['fgcolor'] = array(
                0,
                0,
                0
            );
        }
        if (!isset($style['bgcolor'])) {
            $style['bgcolor'] = false;
        }
        if (!isset($style['border'])) {
            $style['border'] = false;
        }
        $fontsize = 0;
        if (!isset($style['text'])) {
            $style['text'] = false;
        }
        if ($style['text'] AND isset($style['font'])) {
            if (isset($style['fontsize'])) {
                $fontsize = $style['fontsize'];
            }
            $this->SetFont($style['font'], '', $fontsize);
        }
        if (!isset($style['stretchtext'])) {
            $style['stretchtext'] = 4;
        }
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        if (($w === '') OR ($w <= 0)) {
            if ($this->rtl) {
                $w = $x - $this->lMargin;
            } else {
                $w = $this->w - $this->rMargin - $x;
            }
        }
        if (!isset($style['padding'])) {
            $padding = 0;
        } elseif ($style['padding'] === 'auto') {
            $padding = 10 * ($w / ($arrcode['maxw'] + 20));
        } else {
            $padding = floatval($style['padding']);
        }
        if (!isset($style['hpadding'])) {
            $hpadding = $padding;
        } elseif ($style['hpadding'] === 'auto') {
            $hpadding = 10 * ($w / ($arrcode['maxw'] + 20));
        } else {
            $hpadding = floatval($style['hpadding']);
        }
        if (!isset($style['vpadding'])) {
            $vpadding = $padding;
        } elseif ($style['vpadding'] === 'auto') {
            $vpadding = ($hpadding / 2);
        } else {
            $vpadding = floatval($style['vpadding']);
        }
        $max_xres = ($w - (2 * $hpadding)) / $arrcode['maxw'];
        if ($style['stretch']) {
            $xres = $max_xres;
        } else {
            if ($this->empty_string($xres)) {
                $xres = (0.141 * $this->k);
            }
            if ($xres > $max_xres) {
                $xres = $max_xres;
            }
            if ((isset($style['padding']) AND ($style['padding'] === 'auto')) OR (isset($style['hpadding']) AND ($style['hpadding'] === 'auto'))) {
                $hpadding = 10 * $xres;
                if (isset($style['vpadding']) AND ($style['vpadding'] === 'auto')) {
                    $vpadding = ($hpadding / 2);
                }
            }
        }
        if ($style['fitwidth']) {
            $wold = $w;
            $w    = (($arrcode['maxw'] * $xres) + (2 * $hpadding));
            if (isset($style['cellfitalign'])) {
                switch ($style['cellfitalign']) {
                    case 'L': {
                        if ($this->rtl) {
                            $x -= ($wold - $w);
                        }
                        break;
                    }
                    case 'R': {
                        if (!$this->rtl) {
                            $x += ($wold - $w);
                        }
                        break;
                    }
                    case 'C': {
                        if ($this->rtl) {
                            $x -= (($wold - $w) / 2);
                        } else {
                            $x += (($wold - $w) / 2);
                        }
                        break;
                    }
                    default: {
                        break;
                    }
                }
            }
        }
        $text_height = ($this->cell_height_ratio * $fontsize / $this->k);
        if (($h === '') OR ($h <= 0)) {
            $h = (($arrcode['maxw'] * $xres) / 3) + (2 * $vpadding) + $text_height;
        }
        $barh = $h - $text_height - (2 * $vpadding);
        if ($barh <= 0) {
            if ($text_height > $h) {
                $fontsize    = (($h * $this->k) / (4 * $this->cell_height_ratio));
                $text_height = ($this->cell_height_ratio * $fontsize / $this->k);
                $this->SetFont($style['font'], '', $fontsize);
            }
            if ($vpadding > 0) {
                $vpadding = (($h - $text_height) / 4);
            }
            $barh = $h - $text_height - (2 * $vpadding);
        }
        list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, false);
        $this->img_rb_y = $y + $h;
        if ($this->rtl) {
            if ($style['position'] == 'L') {
                $xpos = $this->lMargin;
            } elseif ($style['position'] == 'C') {
                $xpos = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($style['position'] == 'R') {
                $xpos = $this->w - $this->rMargin - $w;
            } else {
                $xpos = $x - $w;
            }
            $this->img_rb_x = $xpos;
        } else {
            if ($style['position'] == 'L') {
                $xpos = $this->lMargin;
            } elseif ($style['position'] == 'C') {
                $xpos = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($style['position'] == 'R') {
                $xpos = $this->w - $this->rMargin - $w;
            } else {
                $xpos = $x;
            }
            $this->img_rb_x = $xpos + $w;
        }
        $xpos_rect = $xpos;
        if (!isset($style['align'])) {
            $style['align'] = 'C';
        }
        switch ($style['align']) {
            case 'L': {
                $xpos = $xpos_rect + $hpadding;
                break;
            }
            case 'R': {
                $xpos = $xpos_rect + ($w - ($arrcode['maxw'] * $xres)) - $hpadding;
                break;
            }
            case 'C':
            default: {
                $xpos = $xpos_rect + (($w - ($arrcode['maxw'] * $xres)) / 2);
                break;
            }
        }
        $xpos_text = $xpos;
        $tempRTL   = $this->rtl;
        $this->rtl = false;
        if ($style['bgcolor']) {
            $this->Rect($xpos_rect, $y, $w, $h, $style['border'] ? 'DF' : 'F', '', $style['bgcolor']);
        } elseif ($style['border']) {
            $this->Rect($xpos_rect, $y, $w, $h, 'D');
        }
        $this->SetDrawColorArray($style['fgcolor']);
        $this->SetTextColorArray($style['fgcolor']);
        foreach ($arrcode['bcode'] as $k => $v) {
            $bw = ($v['w'] * $xres);
            if ($v['t']) {
                $ypos = $y + $vpadding + ($v['p'] * $barh / $arrcode['maxh']);
                $this->Rect($xpos, $ypos, $bw, ($v['h'] * $barh / $arrcode['maxh']), 'F', array(), $style['fgcolor']);
            }
            $xpos += $bw;
        }
        if ($style['text']) {
            if (isset($style['label']) AND !$this->empty_string($style['label'])) {
                $label = $style['label'];
            } else {
                $label = $code;
            }
            $txtwidth = ($arrcode['maxw'] * $xres);
            if ($this->GetStringWidth($label) > $txtwidth) {
                $style['stretchtext'] = 2;
            }
            $this->x     = $xpos_text;
            $this->y     = $y + $vpadding + $barh;
            $cellpadding = $this->cell_padding;
            $this->SetCellPadding(0);
            $this->Cell($txtwidth, '', $label, 0, 0, 'C', 0, '', $style['stretchtext'], false, 'T', 'T');
            $this->cell_padding = $cellpadding;
        }
        $this->rtl = $tempRTL;
        $this->setGraphicVars($gvars);
        switch ($align) {
            case 'T': {
                $this->y = $y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'M': {
                $this->y = $y + round($h / 2);
                $this->x = $this->img_rb_x;
                break;
            }
            case 'B': {
                $this->y = $this->img_rb_y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'N': {
                $this->SetY($this->img_rb_y);
                break;
            }
            default: {
                break;
            }
        }
        $this->endlinex = $this->img_rb_x;
    }
    public function writeBarcode($x, $y, $w, $h, $type, $style, $font, $xres, $code)
    {
        $xres     = 1 / $xres;
        $newstyle = array(
            'position' => '',
            'align' => '',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(
                0,
                0,
                0
            ),
            'bgcolor' => false,
            'text' => true,
            'font' => $font,
            'fontsize' => 8,
            'stretchtext' => 4
        );
        if ($style & 1) {
            $newstyle['border'] = true;
        }
        if ($style & 2) {
            $newstyle['bgcolor'] = false;
        }
        if ($style & 4) {
            $newstyle['position'] = 'C';
        } elseif ($style & 8) {
            $newstyle['position'] = 'L';
        } elseif ($style & 16) {
            $newstyle['position'] = 'R';
        }
        if ($style & 128) {
            $newstyle['text'] = true;
        }
        if ($style & 256) {
            $newstyle['stretchtext'] = 4;
        }
        $this->write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
    }
    public function write2DBarcode($code, $type, $x = '', $y = '', $w = '', $h = '', $style = '', $align = '', $distort = false)
    {
        if ($this->empty_string(trim($code))) {
            return;
        }
        require_once(dirname(__FILE__) . '/2dbarcodes.php');
        $gvars      = $this->getGraphicVars();
        $barcodeobj = new TCPDF2DBarcode($code, $type);
        $arrcode    = $barcodeobj->getBarcodeArray();
        if (($arrcode === false) OR empty($arrcode)) {
            $this->Error('Error in 2D barcode string');
        }
        if (!isset($style['position'])) {
            $style['position'] = '';
        }
        if (!isset($style['fgcolor'])) {
            $style['fgcolor'] = array(
                0,
                0,
                0
            );
        }
        if (!isset($style['bgcolor'])) {
            $style['bgcolor'] = false;
        }
        if (!isset($style['border'])) {
            $style['border'] = false;
        }
        if (!isset($style['padding'])) {
            $style['padding'] = 0;
        } elseif ($style['padding'] === 'auto') {
            $style['padding'] = 4;
        }
        if (!isset($style['hpadding'])) {
            $style['hpadding'] = $style['padding'];
        } elseif ($style['hpadding'] === 'auto') {
            $style['hpadding'] = 4;
        }
        if (!isset($style['vpadding'])) {
            $style['vpadding'] = $style['padding'];
        } elseif ($style['vpadding'] === 'auto') {
            $style['vpadding'] = 4;
        }
        if (!isset($style['module_width'])) {
            $style['module_width'] = 1;
        }
        if (!isset($style['module_height'])) {
            $style['module_height'] = 1;
        }
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        $rows = $arrcode['num_rows'];
        $cols = $arrcode['num_cols'];
        $mw   = $style['module_width'];
        $mh   = $style['module_height'];
        if ($this->rtl) {
            $maxw = $x - $this->lMargin;
        } else {
            $maxw = $this->w - $this->rMargin - $x;
        }
        $maxh    = ($this->h - $this->tMargin - $this->bMargin);
        $ratioHW = ($rows * $mh) / ($cols * $mw);
        $ratioWH = ($cols * $mw) / ($rows * $mh);
        if (!$distort) {
            if (($maxw * $ratioHW) > $maxh) {
                $maxw = $maxh * $ratioWH;
            }
            if (($maxh * $ratioWH) > $maxw) {
                $maxh = $maxw * $ratioHW;
            }
        }
        if ($w > $maxw) {
            $w = $maxw;
        }
        if ($h > $maxh) {
            $h = $maxh;
        }
        $hpad = (2 * $style['hpadding']);
        $vpad = (2 * $style['vpadding']);
        if ((($w === '') OR ($w <= 0)) AND (($h === '') OR ($h <= 0))) {
            $w = ($cols + $hpad) * ($mw / $this->k);
            $h = ($rows + $vpad) * ($mh / $this->k);
        } elseif (($w === '') OR ($w <= 0)) {
            $w = $h * $ratioWH;
        } elseif (($h === '') OR ($h <= 0)) {
            $h = $w * $ratioHW;
        }
        $bw = ($w * $cols) / ($cols + $hpad);
        $bh = ($h * $rows) / ($rows + $vpad);
        $cw = $bw / $cols;
        $ch = $bh / $rows;
        if (!$distort) {
            if (($cw / $ch) > ($mw / $mh)) {
                $cw                = $ch * $mw / $mh;
                $bw                = $cw * $cols;
                $style['hpadding'] = ($w - $bw) / (2 * $cw);
            } else {
                $ch                = $cw * $mh / $mw;
                $bh                = $ch * $rows;
                $style['vpadding'] = ($h - $bh) / (2 * $ch);
            }
        }
        list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, false);
        $this->img_rb_y = $y + $h;
        if ($this->rtl) {
            if ($style['position'] == 'L') {
                $xpos = $this->lMargin;
            } elseif ($style['position'] == 'C') {
                $xpos = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($style['position'] == 'R') {
                $xpos = $this->w - $this->rMargin - $w;
            } else {
                $xpos = $x - $w;
            }
            $this->img_rb_x = $xpos;
        } else {
            if ($style['position'] == 'L') {
                $xpos = $this->lMargin;
            } elseif ($style['position'] == 'C') {
                $xpos = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($style['position'] == 'R') {
                $xpos = $this->w - $this->rMargin - $w;
            } else {
                $xpos = $x;
            }
            $this->img_rb_x = $xpos + $w;
        }
        $xstart    = $xpos + ($style['hpadding'] * $cw);
        $ystart    = $y + ($style['vpadding'] * $ch);
        $tempRTL   = $this->rtl;
        $this->rtl = false;
        if ($style['bgcolor']) {
            $this->Rect($xpos, $y, $w, $h, $style['border'] ? 'DF' : 'F', '', $style['bgcolor']);
        } elseif ($style['border']) {
            $this->Rect($xpos, $y, $w, $h, 'D');
        }
        $this->SetDrawColorArray($style['fgcolor']);
        for ($r = 0; $r < $rows; ++$r) {
            $xr = $xstart;
            for ($c = 0; $c < $cols; ++$c) {
                if ($arrcode['bcode'][$r][$c] == 1) {
                    $this->Rect($xr, $ystart, $cw, $ch, 'F', array(), $style['fgcolor']);
                }
                $xr += $cw;
            }
            $ystart += $ch;
        }
        $this->rtl = $tempRTL;
        $this->setGraphicVars($gvars);
        switch ($align) {
            case 'T': {
                $this->y = $y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'M': {
                $this->y = $y + round($h / 2);
                $this->x = $this->img_rb_x;
                break;
            }
            case 'B': {
                $this->y = $this->img_rb_y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'N': {
                $this->SetY($this->img_rb_y);
                break;
            }
            default: {
                break;
            }
        }
        $this->endlinex = $this->img_rb_x;
    }
    public function getMargins()
    {
        $ret = array(
            'left' => $this->lMargin,
            'right' => $this->rMargin,
            'top' => $this->tMargin,
            'bottom' => $this->bMargin,
            'header' => $this->header_margin,
            'footer' => $this->footer_margin,
            'cell' => $this->cell_padding,
            'padding_left' => $this->cell_padding['L'],
            'padding_top' => $this->cell_padding['T'],
            'padding_right' => $this->cell_padding['R'],
            'padding_bottom' => $this->cell_padding['B']
        );
        return $ret;
    }
    public function getOriginalMargins()
    {
        $ret = array(
            'left' => $this->original_lMargin,
            'right' => $this->original_rMargin
        );
        return $ret;
    }
    public function getFontSize()
    {
        return $this->FontSize;
    }
    public function getFontSizePt()
    {
        return $this->FontSizePt;
    }
    public function getFontFamily()
    {
        return $this->FontFamily;
    }
    public function getFontStyle()
    {
        return $this->FontStyle;
    }
    public function fixHTMLCode($html, $default_css = '', $tagvs = '', $tidy_options = '')
    {
        if ($tidy_options === '') {
            $tidy_options = array(
                'clean' => 1,
                'drop-empty-paras' => 0,
                'drop-proprietary-attributes' => 1,
                'fix-backslash' => 1,
                'hide-comments' => 1,
                'join-styles' => 1,
                'lower-literals' => 1,
                'merge-divs' => 1,
                'merge-spans' => 1,
                'output-xhtml' => 1,
                'word-2000' => 1,
                'wrap' => 0,
                'output-bom' => 0
            );
        }
        $tidy = tidy_parse_string($html, $tidy_options);
        $tidy->cleanRepair();
        $tidy_head = tidy_get_head($tidy);
        $css       = $tidy_head->value;
        $css       = preg_replace('/<style([^>]+)>/ims', '<style>', $css);
        $css       = preg_replace('/<\/style>(.*)<style>/ims', "\n", $css);
        $css       = str_replace('/*<![CDATA[*/', '', $css);
        $css       = str_replace('/*]]>*/', '', $css);
        preg_match('/<style>(.*)<\/style>/ims', $css, $matches);
        if (isset($matches[1])) {
            $css = strtolower($matches[1]);
        } else {
            $css = '';
        }
        $css       = '<style>' . $default_css . $css . '</style>';
        $tidy_body = tidy_get_body($tidy);
        $html      = $tidy_body->value;
        $html      = str_replace('<br>', '<br />', $html);
        $html      = preg_replace('/<div([^\>]*)><\/div>/', '', $html);
        $html      = preg_replace('/<p([^\>]*)><\/p>/', '', $html);
        if ($tagvs !== '') {
            $this->setHtmlVSpace($tagvs);
        }
        return $css . $html;
    }
    protected function extractCSSproperties($cssdata)
    {
        if (empty($cssdata)) {
            return array();
        }
        $cssdata   = preg_replace('/\/\*[^\*]*\*\//', '', $cssdata);
        $cssdata   = preg_replace('/[\s]+/', ' ', $cssdata);
        $cssdata   = preg_replace('/[\s]*([;:\{\}]{1})[\s]*/', '\\1', $cssdata);
        $cssdata   = preg_replace('/([^\}\{]+)\{\}/', '', $cssdata);
        $cssdata   = preg_replace('/@media[\s]+([^\{]*)\{/i', '@media \\1§', $cssdata);
        $cssdata   = preg_replace('/\}\}/si', '}§', $cssdata);
        $cssdata   = trim($cssdata);
        $cssblocks = array();
        $matches   = array();
        if (preg_match_all('/@media[\s]+([^\§]*)§([^§]*)§/i', $cssdata, $matches) > 0) {
            foreach ($matches[1] as $key => $type) {
                $cssblocks[$type] = $matches[2][$key];
            }
            $cssdata = preg_replace('/@media[\s]+([^\§]*)§([^§]*)§/i', '', $cssdata);
        }
        if (isset($cssblocks['all']) AND !empty($cssblocks['all'])) {
            $cssdata .= $cssblocks['all'];
        }
        if (isset($cssblocks['print']) AND !empty($cssblocks['print'])) {
            $cssdata .= $cssblocks['print'];
        }
        $cssblocks = array();
        $matches   = array();
        if (substr($cssdata, -1) == '}') {
            $cssdata = substr($cssdata, 0, -1);
        }
        $matches = explode('}', $cssdata);
        foreach ($matches as $key => $block) {
            $cssblocks[$key] = explode('{', $block);
            if (!isset($cssblocks[$key][1])) {
                unset($cssblocks[$key]);
            }
        }
        foreach ($cssblocks as $key => $block) {
            if (strpos($block[0], ',') > 0) {
                $selectors = explode(',', $block[0]);
                foreach ($selectors as $sel) {
                    $cssblocks[] = array(
                        0 => trim($sel),
                        1 => $block[1]
                    );
                }
                unset($cssblocks[$key]);
            }
        }
        $cssdata = array();
        foreach ($cssblocks as $block) {
            $selector = $block[0];
            $matches  = array();
            $a        = 0;
            $b        = intval(preg_match_all('/[\#]/', $selector, $matches));
            $c        = intval(preg_match_all('/[\[\.]/', $selector, $matches));
            $c += intval(preg_match_all('/[\:]link|visited|hover|active|focus|target|lang|enabled|disabled|checked|indeterminate|root|nth|first|last|only|empty|contains|not/i', $selector, $matches));
            $d = intval(preg_match_all('/[\>\+\~\s]{1}[a-zA-Z0-9]+/', ' ' . $selector, $matches));
            $d += intval(preg_match_all('/[\:][\:]/', $selector, $matches));
            $specificity                             = $a . $b . $c . $d;
            $cssdata[$specificity . ' ' . $selector] = $block[1];
        }
        ksort($cssdata, SORT_STRING);
        return $cssdata;
    }
    protected function isValidCSSSelectorForTag($dom, $key, $selector)
    {
        $valid = false;
        $tag   = $dom[$key]['value'];
        $class = array();
        if (isset($dom[$key]['attribute']['class']) AND !empty($dom[$key]['attribute']['class'])) {
            $class = explode(' ', strtolower($dom[$key]['attribute']['class']));
        }
        $id = '';
        if (isset($dom[$key]['attribute']['id']) AND !empty($dom[$key]['attribute']['id'])) {
            $id = strtolower($dom[$key]['attribute']['id']);
        }
        $selector = preg_replace('/([\>\+\~\s]{1})([\.]{1})([^\>\+\~\s]*)/si', '\\1*.\\3', $selector);
        $matches  = array();
        if (preg_match_all('/([\>\+\~\s]{1})([a-zA-Z0-9\*]+)([^\>\+\~\s]*)/si', $selector, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) > 0) {
            $parentop = array_pop($matches[1]);
            $operator = $parentop[0];
            $offset   = $parentop[1];
            $lasttag  = array_pop($matches[2]);
            $lasttag  = strtolower(trim($lasttag[0]));
            if (($lasttag == '*') OR ($lasttag == $tag)) {
                $attrib = array_pop($matches[3]);
                $attrib = strtolower(trim($attrib[0]));
                if (!empty($attrib)) {
                    switch ($attrib{0}) {
                        case '.': {
                            if (in_array(substr($attrib, 1), $class)) {
                                $valid = true;
                            }
                            break;
                        }
                        case '#': {
                            if (substr($attrib, 1) == $id) {
                                $valid = true;
                            }
                            break;
                        }
                        case '[': {
                            $attrmatch = array();
                            if (preg_match('/\[([a-zA-Z0-9]*)[\s]*([\~\^\$\*\|\=]*)[\s]*["]?([^"\]]*)["]?\]/i', $attrib, $attrmatch) > 0) {
                                $att = strtolower($attrmatch[1]);
                                $val = $attrmatch[3];
                                if (isset($dom[$key]['attribute'][$att])) {
                                    switch ($attrmatch[2]) {
                                        case '=': {
                                            if ($dom[$key]['attribute'][$att] == $val) {
                                                $valid = true;
                                            }
                                            break;
                                        }
                                        case '~=': {
                                            if (in_array($val, explode(' ', $dom[$key]['attribute'][$att]))) {
                                                $valid = true;
                                            }
                                            break;
                                        }
                                        case '^=': {
                                            if ($val == substr($dom[$key]['attribute'][$att], 0, strlen($val))) {
                                                $valid = true;
                                            }
                                            break;
                                        }
                                        case '$=': {
                                            if ($val == substr($dom[$key]['attribute'][$att], -strlen($val))) {
                                                $valid = true;
                                            }
                                            break;
                                        }
                                        case '*=': {
                                            if (strpos($dom[$key]['attribute'][$att], $val) !== false) {
                                                $valid = true;
                                            }
                                            break;
                                        }
                                        case '|=': {
                                            if ($dom[$key]['attribute'][$att] == $val) {
                                                $valid = true;
                                            } elseif (preg_match('/' . $val . '[\-]{1}/i', $dom[$key]['attribute'][$att]) > 0) {
                                                $valid = true;
                                            }
                                            break;
                                        }
                                        default: {
                                            $valid = true;
                                        }
                                    }
                                }
                            }
                            break;
                        }
                        case ':': {
                            if ($attrib{1} == ':') {
                            } else {
                            }
                            break;
                        }
                    }
                } else {
                    $valid = true;
                }
                if ($valid AND ($offset > 0)) {
                    $valid    = false;
                    $selector = substr($selector, 0, $offset);
                    switch ($operator) {
                        case ' ': {
                            while ($dom[$key]['parent'] > 0) {
                                if ($this->isValidCSSSelectorForTag($dom, $dom[$key]['parent'], $selector)) {
                                    $valid = true;
                                    break;
                                } else {
                                    $key = $dom[$key]['parent'];
                                }
                            }
                            break;
                        }
                        case '>': {
                            $valid = $this->isValidCSSSelectorForTag($dom, $dom[$key]['parent'], $selector);
                            break;
                        }
                        case '+': {
                            for ($i = ($key - 1); $i > $dom[$key]['parent']; --$i) {
                                if ($dom[$i]['tag'] AND $dom[$i]['opening']) {
                                    $valid = $this->isValidCSSSelectorForTag($dom, $i, $selector);
                                    break;
                                }
                            }
                            break;
                        }
                        case '~': {
                            for ($i = ($key - 1); $i > $dom[$key]['parent']; --$i) {
                                if ($dom[$i]['tag'] AND $dom[$i]['opening']) {
                                    if ($this->isValidCSSSelectorForTag($dom, $i, $selector)) {
                                        break;
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        return $valid;
    }
    protected function getCSSdataArray($dom, $key, $css)
    {
        $cssarray  = array();
        $selectors = array();
        if (isset($dom[($dom[$key]['parent'])]['csssel'])) {
            $selectors = $dom[($dom[$key]['parent'])]['csssel'];
        }
        foreach ($css as $selector => $style) {
            $pos         = strpos($selector, ' ');
            $specificity = substr($selector, 0, $pos);
            $selector    = substr($selector, $pos);
            if ($this->isValidCSSSelectorForTag($dom, $key, $selector)) {
                if (!in_array($selector, $selectors)) {
                    $cssarray[]  = array(
                        'k' => $selector,
                        's' => $specificity,
                        'c' => $style
                    );
                    $selectors[] = $selector;
                }
            }
        }
        if (isset($dom[$key]['attribute']['style'])) {
            $cssarray[] = array(
                'k' => '',
                's' => '1000',
                'c' => $dom[$key]['attribute']['style']
            );
        }
        $cssordered = array();
        foreach ($cssarray as $key => $val) {
            $skey                                = sprintf('%04d', $key);
            $cssordered[$val['s'] . '_' . $skey] = $val;
        }
        ksort($cssordered, SORT_STRING);
        return array(
            $selectors,
            $cssordered
        );
    }
    protected function getTagStyleFromCSSarray($css)
    {
        $tagstyle = '';
        foreach ($css as $style) {
            $csscmds = explode(';', $style['c']);
            foreach ($csscmds as $cmd) {
                if (!empty($cmd)) {
                    $pos = strpos($cmd, ':');
                    if ($pos !== false) {
                        $cmd = substr($cmd, 0, ($pos + 1));
                        if (strpos($tagstyle, $cmd) !== false) {
                            $tagstyle = preg_replace('/' . $cmd . '[^;]+/i', '', $tagstyle);
                        }
                    }
                }
            }
            $tagstyle .= ';' . $style['c'];
        }
        $tagstyle = preg_replace('/[;]+/', ';', $tagstyle);
        return $tagstyle;
    }
    protected function getCSSBorderWidth($width)
    {
        if ($width == 'thin') {
            $width = (2 / $this->k);
        } elseif ($width == 'medium') {
            $width = (4 / $this->k);
        } elseif ($width == 'thick') {
            $width = (6 / $this->k);
        } else {
            $width = $this->getHTMLUnitToUnits($width, 1, 'px', false);
        }
        return $width;
    }
    protected function getCSSBorderDashStyle($style)
    {
        switch (strtolower($style)) {
            case 'none':
            case 'hidden': {
                $dash = -1;
                break;
            }
            case 'dotted': {
                $dash = 1;
                break;
            }
            case 'dashed': {
                $dash = 3;
                break;
            }
            case 'double':
            case 'groove':
            case 'ridge':
            case 'inset':
            case 'outset':
            case 'solid':
            default: {
                $dash = 0;
                break;
            }
        }
        return $dash;
    }
    protected function getCSSBorderStyle($cssborder)
    {
        $bprop  = preg_split('/[\s]+/', trim($cssborder));
        $border = array();
        switch (count($bprop)) {
            case 3: {
                $width = $bprop[0];
                $style = $bprop[1];
                $color = $bprop[2];
                break;
            }
            case 2: {
                $width = 'medium';
                $style = $bprop[0];
                $color = $bprop[1];
                break;
            }
            case 1: {
                $width = 'medium';
                $style = $bprop[0];
                $color = 'black';
                break;
            }
            default: {
                $width = 'medium';
                $style = 'solid';
                $color = 'black';
                break;
            }
        }
        if ($style == 'none') {
            return array();
        }
        $border['cap']  = 'square';
        $border['join'] = 'miter';
        $border['dash'] = $this->getCSSBorderDashStyle($style);
        if ($border['dash'] < 0) {
            return array();
        }
        $border['width'] = $this->getCSSBorderWidth($width);
        $border['color'] = $this->convertHTMLColorToDec($color);
        return $border;
    }
    public function getCSSPadding($csspadding, $width = 0)
    {
        $padding      = preg_split('/[\s]+/', trim($csspadding));
        $cell_padding = array();
        switch (count($padding)) {
            case 4: {
                $cell_padding['T'] = $padding[0];
                $cell_padding['R'] = $padding[1];
                $cell_padding['B'] = $padding[2];
                $cell_padding['L'] = $padding[3];
                break;
            }
            case 3: {
                $cell_padding['T'] = $padding[0];
                $cell_padding['R'] = $padding[1];
                $cell_padding['B'] = $padding[2];
                $cell_padding['L'] = $padding[1];
                break;
            }
            case 2: {
                $cell_padding['T'] = $padding[0];
                $cell_padding['R'] = $padding[1];
                $cell_padding['B'] = $padding[0];
                $cell_padding['L'] = $padding[1];
                break;
            }
            case 1: {
                $cell_padding['T'] = $padding[0];
                $cell_padding['R'] = $padding[0];
                $cell_padding['B'] = $padding[0];
                $cell_padding['L'] = $padding[0];
                break;
            }
            default: {
                return $this->cell_padding;
            }
        }
        if ($width == 0) {
            $width = $this->w - $this->lMargin - $this->rMargin;
        }
        $cell_padding['T'] = $this->getHTMLUnitToUnits($cell_padding['T'], $width, 'px', false);
        $cell_padding['R'] = $this->getHTMLUnitToUnits($cell_padding['R'], $width, 'px', false);
        $cell_padding['B'] = $this->getHTMLUnitToUnits($cell_padding['B'], $width, 'px', false);
        $cell_padding['L'] = $this->getHTMLUnitToUnits($cell_padding['L'], $width, 'px', false);
        return $cell_padding;
    }
    public function getCSSMargin($cssmargin, $width = 0)
    {
        $margin      = preg_split('/[\s]+/', trim($cssmargin));
        $cell_margin = array();
        switch (count($margin)) {
            case 4: {
                $cell_margin['T'] = $margin[0];
                $cell_margin['R'] = $margin[1];
                $cell_margin['B'] = $margin[2];
                $cell_margin['L'] = $margin[3];
                break;
            }
            case 3: {
                $cell_margin['T'] = $margin[0];
                $cell_margin['R'] = $margin[1];
                $cell_margin['B'] = $margin[2];
                $cell_margin['L'] = $margin[1];
                break;
            }
            case 2: {
                $cell_margin['T'] = $margin[0];
                $cell_margin['R'] = $margin[1];
                $cell_margin['B'] = $margin[0];
                $cell_margin['L'] = $margin[1];
                break;
            }
            case 1: {
                $cell_margin['T'] = $margin[0];
                $cell_margin['R'] = $margin[0];
                $cell_margin['B'] = $margin[0];
                $cell_margin['L'] = $margin[0];
                break;
            }
            default: {
                return $this->cell_margin;
            }
        }
        if ($width == 0) {
            $width = $this->w - $this->lMargin - $this->rMargin;
        }
        $cell_margin['T'] = $this->getHTMLUnitToUnits(str_replace('auto', '0', $cell_margin['T']), $width, 'px', false);
        $cell_margin['R'] = $this->getHTMLUnitToUnits(str_replace('auto', '0', $cell_margin['R']), $width, 'px', false);
        $cell_margin['B'] = $this->getHTMLUnitToUnits(str_replace('auto', '0', $cell_margin['B']), $width, 'px', false);
        $cell_margin['L'] = $this->getHTMLUnitToUnits(str_replace('auto', '0', $cell_margin['L']), $width, 'px', false);
        return $cell_margin;
    }
    public function getCSSBorderMargin($cssbspace, $width = 0)
    {
        $space          = preg_split('/[\s]+/', trim($cssbspace));
        $border_spacing = array();
        switch (count($space)) {
            case 2: {
                $border_spacing['H'] = $space[0];
                $border_spacing['V'] = $space[1];
                break;
            }
            case 1: {
                $border_spacing['H'] = $space[0];
                $border_spacing['V'] = $space[0];
                break;
            }
            default: {
                return array(
                    'H' => 0,
                    'V' => 0
                );
            }
        }
        if ($width == 0) {
            $width = $this->w - $this->lMargin - $this->rMargin;
        }
        $border_spacing['H'] = $this->getHTMLUnitToUnits($border_spacing['H'], $width, 'px', false);
        $border_spacing['V'] = $this->getHTMLUnitToUnits($border_spacing['V'], $width, 'px', false);
        return $border_spacing;
    }
    protected function getCSSFontSpacing($spacing, $parent = 0)
    {
        $val     = 0;
        $spacing = trim($spacing);
        switch ($spacing) {
            case 'normal': {
                $val = 0;
                break;
            }
            case 'inherit': {
                if ($parent == 'normal') {
                    $val = 0;
                } else {
                    $val = $parent;
                }
                break;
            }
            default: {
                $val = $this->getHTMLUnitToUnits($spacing, 0, 'px', false);
            }
        }
        return $val;
    }
    protected function getCSSFontStretching($stretch, $parent = 100)
    {
        $val     = 100;
        $stretch = trim($stretch);
        switch ($stretch) {
            case 'ultra-condensed': {
                $val = 40;
                break;
            }
            case 'extra-condensed': {
                $val = 55;
                break;
            }
            case 'condensed': {
                $val = 70;
                break;
            }
            case 'semi-condensed': {
                $val = 85;
                break;
            }
            case 'normal': {
                $val = 100;
                break;
            }
            case 'semi-expanded': {
                $val = 115;
                break;
            }
            case 'expanded': {
                $val = 130;
                break;
            }
            case 'extra-expanded': {
                $val = 145;
                break;
            }
            case 'ultra-expanded': {
                $val = 160;
                break;
            }
            case 'wider': {
                $val = $parent + 10;
                break;
            }
            case 'narrower': {
                $val = $parent - 10;
                break;
            }
            case 'inherit': {
                if ($parent == 'normal') {
                    $val = 100;
                } else {
                    $val = $parent;
                }
                break;
            }
            default: {
                $val = $this->getHTMLUnitToUnits($stretch, 100, '%', false);
            }
        }
        return $val;
    }
    protected function getHtmlDomArray($html)
    {
        $css     = array();
        $matches = array();
        if (preg_match_all('/<cssarray>([^\<]*)<\/cssarray>/isU', $html, $matches) > 0) {
            if (isset($matches[1][0])) {
                $css = array_merge($css, unserialize($this->unhtmlentities($matches[1][0])));
            }
            $html = preg_replace('/<cssarray>(.*?)<\/cssarray>/isU', '', $html);
        }
        $matches = array();
        if (preg_match_all('/<link([^\>]*)>/isU', $html, $matches) > 0) {
            foreach ($matches[1] as $key => $link) {
                $type = array();
                if (preg_match('/type[\s]*=[\s]*"text\/css"/', $link, $type)) {
                    $type = array();
                    preg_match('/media[\s]*=[\s]*"([^"]*)"/', $link, $type);
                    if (empty($type) OR (isset($type[1]) AND (($type[1] == 'all') OR ($type[1] == 'print')))) {
                        $type = array();
                        if (preg_match('/href[\s]*=[\s]*"([^"]*)"/', $link, $type) > 0) {
                            $cssdata = file_get_contents(trim($type[1]));
                            $css     = array_merge($css, $this->extractCSSproperties($cssdata));
                        }
                    }
                }
            }
        }
        $matches = array();
        if (preg_match_all('/<style([^\>]*)>([^\<]*)<\/style>/isU', $html, $matches) > 0) {
            foreach ($matches[1] as $key => $media) {
                $type = array();
                preg_match('/media[\s]*=[\s]*"([^"]*)"/', $media, $type);
                if (empty($type) OR (isset($type[1]) AND (($type[1] == 'all') OR ($type[1] == 'print')))) {
                    $cssdata = $matches[2][$key];
                    $css     = array_merge($css, $this->extractCSSproperties($cssdata));
                }
            }
        }
        $csstagarray     = '<cssarray>' . htmlentities(serialize($css)) . '</cssarray>';
        $html            = preg_replace('/<head([^\>]*)>(.*?)<\/head>/siU', '', $html);
        $html            = preg_replace('/<style([^\>]*)>([^\<]*)<\/style>/isU', '', $html);
        $blocktags       = array(
            'blockquote',
            'br',
            'dd',
            'dl',
            'div',
            'dt',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hr',
            'li',
            'ol',
            'p',
            'pre',
            'ul',
            'tcpdf',
            'table',
            'tr',
            'td'
        );
        $selfclosingtags = array(
            'area',
            'base',
            'basefont',
            'br',
            'hr',
            'input',
            'img',
            'link',
            'meta'
        );
        $html            = strip_tags($html, '<marker/><a><b><blockquote><body><br><br/><dd><del><div><dl><dt><em><font><form><h1><h2><h3><h4><h5><h6><hr><hr/><i><img><input><label><li><ol><option><p><pre><s><select><small><span><strike><strong><sub><sup><table><tablehead><tcpdf><td><textarea><th><thead><tr><tt><u><ul>');
        $html            = preg_replace('/<pre/', '<xre', $html);
        $html            = preg_replace('/<(table|tr|td|th|tcpdf|blockquote|dd|div|dl|dt|form|h1|h2|h3|h4|h5|h6|br|hr|li|ol|ul|p)([^\>]*)>[\n\r\t]+/', '<\\1\\2>', $html);
        $html            = preg_replace('@(\r\n|\r)@', "\n", $html);
        $repTable        = array(
            "\t" => ' ',
            "\0" => ' ',
            "\x0B" => ' ',
            "\\" => "\\\\"
        );
        $html            = strtr($html, $repTable);
        $offset          = 0;
        while (($offset < strlen($html)) AND ($pos = strpos($html, '</pre>', $offset)) !== false) {
            $html_a = substr($html, 0, $offset);
            $html_b = substr($html, $offset, ($pos - $offset + 6));
            while (preg_match("'<xre([^\>]*)>(.*?)\n(.*?)</pre>'si", $html_b)) {
                $html_b = preg_replace("'<xre([^\>]*)>(.*?)\n(.*?)</pre>'si", "<xre\\1>\\2<br />\\3</pre>", $html_b);
            }
            while (preg_match("'<xre([^\>]*)>(.*?)" . $this->re_space['p'] . "(.*?)</pre>'" . $this->re_space['m'], $html_b)) {
                $html_b = preg_replace("'<xre([^\>]*)>(.*?)" . $this->re_space['p'] . "(.*?)</pre>'" . $this->re_space['m'], "<xre\\1>\\2&nbsp;\\3</pre>", $html_b);
            }
            $html   = $html_a . $html_b . substr($html, $pos + 6);
            $offset = strlen($html_a . $html_b);
        }
        $offset = 0;
        while (($offset < strlen($html)) AND ($pos = strpos($html, '</textarea>', $offset)) !== false) {
            $html_a = substr($html, 0, $offset);
            $html_b = substr($html, $offset, ($pos - $offset + 11));
            while (preg_match("'<textarea([^\>]*)>(.*?)\n(.*?)</textarea>'si", $html_b)) {
                $html_b = preg_replace("'<textarea([^\>]*)>(.*?)\n(.*?)</textarea>'si", "<textarea\\1>\\2<TBR>\\3</textarea>", $html_b);
                $html_b = preg_replace("'<textarea([^\>]*)>(.*?)[\"](.*?)</textarea>'si", "<textarea\\1>\\2''\\3</textarea>", $html_b);
            }
            $html   = $html_a . $html_b . substr($html, $pos + 11);
            $offset = strlen($html_a . $html_b);
        }
        $html   = preg_replace('/([\s]*)<option/si', '<option', $html);
        $html   = preg_replace('/<\/option>([\s]*)/si', '</option>', $html);
        $offset = 0;
        while (($offset < strlen($html)) AND ($pos = strpos($html, '</option>', $offset)) !== false) {
            $html_a = substr($html, 0, $offset);
            $html_b = substr($html, $offset, ($pos - $offset + 9));
            while (preg_match("'<option([^\>]*)>(.*?)</option>'si", $html_b)) {
                $html_b = preg_replace("'<option([\s]+)value=\"([^\"]*)\"([^\>]*)>(.*?)</option>'si", "\\2#!TaB!#\\4#!NwL!#", $html_b);
                $html_b = preg_replace("'<option([^\>]*)>(.*?)</option>'si", "\\2#!NwL!#", $html_b);
            }
            $html   = $html_a . $html_b . substr($html, $pos + 9);
            $offset = strlen($html_a . $html_b);
        }
        if (preg_match("'</select'si", $html)) {
            $html = preg_replace("'<select([^\>]*)>'si", "<select\\1 opt=\"", $html);
            $html = preg_replace("'#!NwL!#</select>'si", "\" />", $html);
        }
        $html                        = str_replace("\n", ' ', $html);
        $html                        = str_replace('<TBR>', "\n", $html);
        $html                        = preg_replace('/[\s]+<\/(table|tr|ul|ol|dl)>/', '</\\1>', $html);
        $html                        = preg_replace('/' . $this->re_space['p'] . '+<\/(td|th|li|dt|dd)>/' . $this->re_space['m'], '</\\1>', $html);
        $html                        = preg_replace('/[\s]+<(tr|td|th|li|dt|dd)/', '<\\1', $html);
        $html                        = preg_replace('/' . $this->re_space['p'] . '+<(ul|ol|dl|br)/' . $this->re_space['m'], '<\\1', $html);
        $html                        = preg_replace('/<\/(table|tr|td|th|blockquote|dd|dt|dl|div|dt|h1|h2|h3|h4|h5|h6|hr|li|ol|ul|p)>[\s]+</', '</\\1><', $html);
        $html                        = preg_replace('/<\/(td|th)>/', '<marker style="font-size:0"/></\\1>', $html);
        $html                        = preg_replace('/<\/table>([\s]*)<marker style="font-size:0"\/>/', '</table>', $html);
        $html                        = preg_replace('/' . $this->re_space['p'] . '+<img/' . $this->re_space['m'], chr(32) . '<img', $html);
        $html                        = preg_replace('/<img([^\>]*)>[\s]+([^\<])/xi', '<img\\1>&nbsp;\\2', $html);
        $html                        = preg_replace('/<img([^\>]*)>/xi', '<img\\1><span><marker style="font-size:0"/></span>', $html);
        $html                        = preg_replace('/<xre/', '<pre', $html);
        $html                        = preg_replace('/<textarea([^\>]*)>([^\<]*)<\/textarea>/xi', '<textarea\\1 value="\\2" />', $html);
        $html                        = preg_replace('/<li([^\>]*)><\/li>/', '<li\\1>&nbsp;</li>', $html);
        $html                        = preg_replace('/<li([^\>]*)>' . $this->re_space['p'] . '*<img/' . $this->re_space['m'], '<li\\1><font size="1">&nbsp;</font><img', $html);
        $html                        = preg_replace('/<([^\>\/]*)>[\s]/', '<\\1>&nbsp;', $html);
        $html                        = preg_replace('/[\s]<\/([^\>]*)>/', '&nbsp;</\\1>', $html);
        $html                        = preg_replace('/' . $this->re_space['p'] . '+/' . $this->re_space['m'], chr(32), $html);
        $html                        = $this->stringTrim($html);
        $html                        = preg_replace('/^<img/', '<span style="font-size:0"><br /></span> <img', $html, 1);
        $tagpattern                  = '/(<[^>]+>)/';
        $a                           = preg_split($tagpattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $maxel                       = count($a);
        $elkey                       = 0;
        $key                         = 0;
        $dom                         = array();
        $dom[$key]                   = array();
        $dom[$key]['tag']            = false;
        $dom[$key]['block']          = false;
        $dom[$key]['value']          = '';
        $dom[$key]['parent']         = 0;
        $dom[$key]['hide']           = false;
        $dom[$key]['fontname']       = $this->FontFamily;
        $dom[$key]['fontstyle']      = $this->FontStyle;
        $dom[$key]['fontsize']       = $this->FontSizePt;
        $dom[$key]['font-stretch']   = $this->font_stretching;
        $dom[$key]['letter-spacing'] = $this->font_spacing;
        $dom[$key]['stroke']         = $this->textstrokewidth;
        $dom[$key]['fill']           = (($this->textrendermode % 2) == 0);
        $dom[$key]['clip']           = ($this->textrendermode > 3);
        $dom[$key]['line-height']    = $this->cell_height_ratio;
        $dom[$key]['bgcolor']        = false;
        $dom[$key]['fgcolor']        = $this->fgcolor;
        $dom[$key]['strokecolor']    = $this->strokecolor;
        $dom[$key]['align']          = '';
        $dom[$key]['listtype']       = '';
        $dom[$key]['text-indent']    = 0;
        $dom[$key]['border']         = array();
        $dom[$key]['dir']            = $this->rtl ? 'rtl' : 'ltr';
        $thead                       = false;
        ++$key;
        $level = array();
        array_push($level, 0);
        while ($elkey < $maxel) {
            $dom[$key]          = array();
            $element            = $a[$elkey];
            $dom[$key]['elkey'] = $elkey;
            if (preg_match($tagpattern, $element)) {
                $element = substr($element, 1, -1);
                preg_match('/[\/]?([a-zA-Z0-9]*)/', $element, $tag);
                $tagname = strtolower($tag[1]);
                if ($tagname == 'thead') {
                    if ($element{0} == '/') {
                        $thead = false;
                    } else {
                        $thead = true;
                    }
                    ++$elkey;
                    continue;
                }
                $dom[$key]['tag']   = true;
                $dom[$key]['value'] = $tagname;
                if (in_array($dom[$key]['value'], $blocktags)) {
                    $dom[$key]['block'] = true;
                } else {
                    $dom[$key]['block'] = false;
                }
                if ($element{0} == '/') {
                    $dom[$key]['opening'] = false;
                    $dom[$key]['parent']  = end($level);
                    array_pop($level);
                    $dom[$key]['hide']           = $dom[($dom[($dom[$key]['parent'])]['parent'])]['hide'];
                    $dom[$key]['fontname']       = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontname'];
                    $dom[$key]['fontstyle']      = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontstyle'];
                    $dom[$key]['fontsize']       = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontsize'];
                    $dom[$key]['font-stretch']   = $dom[($dom[($dom[$key]['parent'])]['parent'])]['font-stretch'];
                    $dom[$key]['letter-spacing'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['letter-spacing'];
                    $dom[$key]['stroke']         = $dom[($dom[($dom[$key]['parent'])]['parent'])]['stroke'];
                    $dom[$key]['fill']           = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fill'];
                    $dom[$key]['clip']           = $dom[($dom[($dom[$key]['parent'])]['parent'])]['clip'];
                    $dom[$key]['line-height']    = $dom[($dom[($dom[$key]['parent'])]['parent'])]['line-height'];
                    $dom[$key]['bgcolor']        = $dom[($dom[($dom[$key]['parent'])]['parent'])]['bgcolor'];
                    $dom[$key]['fgcolor']        = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fgcolor'];
                    $dom[$key]['strokecolor']    = $dom[($dom[($dom[$key]['parent'])]['parent'])]['strokecolor'];
                    $dom[$key]['align']          = $dom[($dom[($dom[$key]['parent'])]['parent'])]['align'];
                    $dom[$key]['dir']            = $dom[($dom[($dom[$key]['parent'])]['parent'])]['dir'];
                    if (isset($dom[($dom[($dom[$key]['parent'])]['parent'])]['listtype'])) {
                        $dom[$key]['listtype'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['listtype'];
                    }
                    if (($dom[$key]['value'] == 'tr') AND (!isset($dom[($dom[($dom[$key]['parent'])]['parent'])]['cols']))) {
                        $dom[($dom[($dom[$key]['parent'])]['parent'])]['cols'] = $dom[($dom[$key]['parent'])]['cols'];
                    }
                    if (($dom[$key]['value'] == 'td') OR ($dom[$key]['value'] == 'th')) {
                        $dom[($dom[$key]['parent'])]['content'] = $csstagarray;
                        for ($i = ($dom[$key]['parent'] + 1); $i < $key; ++$i) {
                            $dom[($dom[$key]['parent'])]['content'] .= $a[$dom[$i]['elkey']];
                        }
                        $key                                    = $i;
                        $dom[($dom[$key]['parent'])]['content'] = str_replace('<table', '<table nested="true"', $dom[($dom[$key]['parent'])]['content']);
                        $dom[($dom[$key]['parent'])]['content'] = str_replace('<thead>', '', $dom[($dom[$key]['parent'])]['content']);
                        $dom[($dom[$key]['parent'])]['content'] = str_replace('</thead>', '', $dom[($dom[$key]['parent'])]['content']);
                    }
                    if (($dom[$key]['value'] == 'tr') AND ($dom[($dom[$key]['parent'])]['thead'] === true)) {
                        if ($this->empty_string($dom[($dom[($dom[$key]['parent'])]['parent'])]['thead'])) {
                            $dom[($dom[($dom[$key]['parent'])]['parent'])]['thead'] = $csstagarray . $a[$dom[($dom[($dom[$key]['parent'])]['parent'])]['elkey']];
                        }
                        for ($i = $dom[$key]['parent']; $i <= $key; ++$i) {
                            $dom[($dom[($dom[$key]['parent'])]['parent'])]['thead'] .= $a[$dom[$i]['elkey']];
                        }
                        if (!isset($dom[($dom[$key]['parent'])]['attribute'])) {
                            $dom[($dom[$key]['parent'])]['attribute'] = array();
                        }
                        $dom[($dom[$key]['parent'])]['attribute']['nobr'] = 'true';
                    }
                    if (($dom[$key]['value'] == 'table') AND (!$this->empty_string($dom[($dom[$key]['parent'])]['thead']))) {
                        $dom[($dom[$key]['parent'])]['thead'] = str_replace(' nobr="true"', '', $dom[($dom[$key]['parent'])]['thead']);
                        $dom[($dom[$key]['parent'])]['thead'] .= '</tablehead>';
                    }
                } else {
                    $dom[$key]['opening'] = true;
                    $dom[$key]['parent']  = end($level);
                    if ((substr($element, -1, 1) == '/') OR (in_array($dom[$key]['value'], $selfclosingtags))) {
                        $dom[$key]['self'] = true;
                    } else {
                        array_push($level, $key);
                        $dom[$key]['self'] = false;
                    }
                    $parentkey = 0;
                    if ($key > 0) {
                        $parentkey                   = $dom[$key]['parent'];
                        $dom[$key]['hide']           = $dom[$parentkey]['hide'];
                        $dom[$key]['fontname']       = $dom[$parentkey]['fontname'];
                        $dom[$key]['fontstyle']      = $dom[$parentkey]['fontstyle'];
                        $dom[$key]['fontsize']       = $dom[$parentkey]['fontsize'];
                        $dom[$key]['font-stretch']   = $dom[$parentkey]['font-stretch'];
                        $dom[$key]['letter-spacing'] = $dom[$parentkey]['letter-spacing'];
                        $dom[$key]['stroke']         = $dom[$parentkey]['stroke'];
                        $dom[$key]['fill']           = $dom[$parentkey]['fill'];
                        $dom[$key]['clip']           = $dom[$parentkey]['clip'];
                        $dom[$key]['line-height']    = $dom[$parentkey]['line-height'];
                        $dom[$key]['bgcolor']        = $dom[$parentkey]['bgcolor'];
                        $dom[$key]['fgcolor']        = $dom[$parentkey]['fgcolor'];
                        $dom[$key]['strokecolor']    = $dom[$parentkey]['strokecolor'];
                        $dom[$key]['align']          = $dom[$parentkey]['align'];
                        $dom[$key]['listtype']       = $dom[$parentkey]['listtype'];
                        $dom[$key]['text-indent']    = $dom[$parentkey]['text-indent'];
                        $dom[$key]['border']         = array();
                        $dom[$key]['dir']            = $dom[$parentkey]['dir'];
                    }
                    preg_match_all('/([^=\s]*)[\s]*=[\s]*"([^"]*)"/', $element, $attr_array, PREG_PATTERN_ORDER);
                    $dom[$key]['attribute'] = array();
                    while (list($id, $name) = each($attr_array[1])) {
                        $dom[$key]['attribute'][strtolower($name)] = $attr_array[2][$id];
                    }
                    if (!empty($css)) {
                        list($dom[$key]['csssel'], $dom[$key]['cssdata']) = $this->getCSSdataArray($dom, $key, $css);
                        $dom[$key]['attribute']['style'] = $this->getTagStyleFromCSSarray($dom[$key]['cssdata']);
                    }
                    if (isset($dom[$key]['attribute']['style']) AND !empty($dom[$key]['attribute']['style'])) {
                        preg_match_all('/([^;:\s]*):([^;]*)/', $dom[$key]['attribute']['style'], $style_array, PREG_PATTERN_ORDER);
                        $dom[$key]['style'] = array();
                        while (list($id, $name) = each($style_array[1])) {
                            $dom[$key]['style'][strtolower($name)] = trim($style_array[2][$id]);
                        }
                        if (isset($dom[$key]['style']['direction'])) {
                            $dom[$key]['dir'] = $dom[$key]['style']['direction'];
                        }
                        if (isset($dom[$key]['style']['display'])) {
                            $dom[$key]['hide'] = (trim(strtolower($dom[$key]['style']['display'])) == 'none');
                        }
                        if (isset($dom[$key]['style']['font-family'])) {
                            $dom[$key]['fontname'] = $this->getFontFamilyName($dom[$key]['style']['font-family']);
                        }
                        if (isset($dom[$key]['style']['list-style-type'])) {
                            $dom[$key]['listtype'] = trim(strtolower($dom[$key]['style']['list-style-type']));
                            if ($dom[$key]['listtype'] == 'inherit') {
                                $dom[$key]['listtype'] = $dom[$parentkey]['listtype'];
                            }
                        }
                        if (isset($dom[$key]['style']['text-indent'])) {
                            $dom[$key]['text-indent'] = $this->getHTMLUnitToUnits($dom[$key]['style']['text-indent']);
                            if ($dom[$key]['text-indent'] == 'inherit') {
                                $dom[$key]['text-indent'] = $dom[$parentkey]['text-indent'];
                            }
                        }
                        if (isset($dom[$key]['style']['font-size'])) {
                            $fsize = trim($dom[$key]['style']['font-size']);
                            switch ($fsize) {
                                case 'xx-small': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'] - 4;
                                    break;
                                }
                                case 'x-small': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'] - 3;
                                    break;
                                }
                                case 'small': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'] - 2;
                                    break;
                                }
                                case 'medium': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'];
                                    break;
                                }
                                case 'large': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'] + 2;
                                    break;
                                }
                                case 'x-large': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'] + 4;
                                    break;
                                }
                                case 'xx-large': {
                                    $dom[$key]['fontsize'] = $dom[0]['fontsize'] + 6;
                                    break;
                                }
                                case 'smaller': {
                                    $dom[$key]['fontsize'] = $dom[$parentkey]['fontsize'] - 3;
                                    break;
                                }
                                case 'larger': {
                                    $dom[$key]['fontsize'] = $dom[$parentkey]['fontsize'] + 3;
                                    break;
                                }
                                default: {
                                    $dom[$key]['fontsize'] = $this->getHTMLUnitToUnits($fsize, $dom[$parentkey]['fontsize'], 'pt', true);
                                }
                            }
                        }
                        if (isset($dom[$key]['style']['font-stretch'])) {
                            $dom[$key]['font-stretch'] = $this->getCSSFontStretching($dom[$key]['style']['font-stretch'], $dom[$parentkey]['font-stretch']);
                        }
                        if (isset($dom[$key]['style']['letter-spacing'])) {
                            $dom[$key]['letter-spacing'] = $this->getCSSFontSpacing($dom[$key]['style']['letter-spacing'], $dom[$parentkey]['letter-spacing']);
                        }
                        if (isset($dom[$key]['style']['line-height'])) {
                            $lineheight = trim($dom[$key]['style']['line-height']);
                            switch ($lineheight) {
                                case 'normal': {
                                    $dom[$key]['line-height'] = $dom[0]['line-height'];
                                    break;
                                }
                                default: {
                                    if (is_numeric($lineheight)) {
                                        $lineheight = $lineheight * 100;
                                    }
                                    $dom[$key]['line-height'] = $this->getHTMLUnitToUnits($lineheight, 1, '%', true);
                                }
                            }
                        }
                        if (isset($dom[$key]['style']['font-weight'])) {
                            if (strtolower($dom[$key]['style']['font-weight']{0}) == 'n') {
                                if (strpos($dom[$key]['fontstyle'], 'B') !== false) {
                                    $dom[$key]['fontstyle'] = str_replace('B', '', $dom[$key]['fontstyle']);
                                }
                            } elseif (strtolower($dom[$key]['style']['font-weight']{0}) == 'b') {
                                $dom[$key]['fontstyle'] .= 'B';
                            }
                        }
                        if (isset($dom[$key]['style']['font-style']) AND (strtolower($dom[$key]['style']['font-style']{0}) == 'i')) {
                            $dom[$key]['fontstyle'] .= 'I';
                        }
                        if (isset($dom[$key]['style']['color']) AND (!$this->empty_string($dom[$key]['style']['color']))) {
                            $dom[$key]['fgcolor'] = $this->convertHTMLColorToDec($dom[$key]['style']['color']);
                        } elseif ($dom[$key]['value'] == 'a') {
                            $dom[$key]['fgcolor'] = $this->htmlLinkColorArray;
                        }
                        if (isset($dom[$key]['style']['background-color']) AND (!$this->empty_string($dom[$key]['style']['background-color']))) {
                            $dom[$key]['bgcolor'] = $this->convertHTMLColorToDec($dom[$key]['style']['background-color']);
                        }
                        if (isset($dom[$key]['style']['text-decoration'])) {
                            $decors = explode(' ', strtolower($dom[$key]['style']['text-decoration']));
                            foreach ($decors as $dec) {
                                $dec = trim($dec);
                                if (!$this->empty_string($dec)) {
                                    if ($dec{0} == 'u') {
                                        $dom[$key]['fontstyle'] .= 'U';
                                    } elseif ($dec{0} == 'l') {
                                        $dom[$key]['fontstyle'] .= 'D';
                                    } elseif ($dec{0} == 'o') {
                                        $dom[$key]['fontstyle'] .= 'O';
                                    }
                                }
                            }
                        } elseif ($dom[$key]['value'] == 'a') {
                            $dom[$key]['fontstyle'] = $this->htmlLinkFontStyle;
                        }
                        if (isset($dom[$key]['style']['width'])) {
                            $dom[$key]['width'] = $dom[$key]['style']['width'];
                        }
                        if (isset($dom[$key]['style']['height'])) {
                            $dom[$key]['height'] = $dom[$key]['style']['height'];
                        }
                        if (isset($dom[$key]['style']['text-align'])) {
                            $dom[$key]['align'] = strtoupper($dom[$key]['style']['text-align']{0});
                        }
                        if (isset($dom[$key]['style']['border'])) {
                            $borderstyle = $this->getCSSBorderStyle($dom[$key]['style']['border']);
                            if (!empty($borderstyle)) {
                                $dom[$key]['border']['LTRB'] = $borderstyle;
                            }
                        }
                        if (isset($dom[$key]['style']['border-color'])) {
                            $brd_colors = preg_split('/[\s]+/', trim($dom[$key]['style']['border-color']));
                            if (isset($brd_colors[3])) {
                                $dom[$key]['border']['L']['color'] = $this->convertHTMLColorToDec($brd_colors[3]);
                            }
                            if (isset($brd_colors[1])) {
                                $dom[$key]['border']['R']['color'] = $this->convertHTMLColorToDec($brd_colors[1]);
                            }
                            if (isset($brd_colors[0])) {
                                $dom[$key]['border']['T']['color'] = $this->convertHTMLColorToDec($brd_colors[0]);
                            }
                            if (isset($brd_colors[2])) {
                                $dom[$key]['border']['B']['color'] = $this->convertHTMLColorToDec($brd_colors[2]);
                            }
                        }
                        if (isset($dom[$key]['style']['border-width'])) {
                            $brd_widths = preg_split('/[\s]+/', trim($dom[$key]['style']['border-width']));
                            if (isset($brd_widths[3])) {
                                $dom[$key]['border']['L']['width'] = $this->getCSSBorderWidth($brd_widths[3]);
                            }
                            if (isset($brd_widths[1])) {
                                $dom[$key]['border']['R']['width'] = $this->getCSSBorderWidth($brd_widths[1]);
                            }
                            if (isset($brd_widths[0])) {
                                $dom[$key]['border']['T']['width'] = $this->getCSSBorderWidth($brd_widths[0]);
                            }
                            if (isset($brd_widths[2])) {
                                $dom[$key]['border']['B']['width'] = $this->getCSSBorderWidth($brd_widths[2]);
                            }
                        }
                        if (isset($dom[$key]['style']['border-style'])) {
                            $brd_styles = preg_split('/[\s]+/', trim($dom[$key]['style']['border-style']));
                            if (isset($brd_styles[3])) {
                                $dom[$key]['border']['L']['cap']  = 'square';
                                $dom[$key]['border']['L']['join'] = 'miter';
                                $dom[$key]['border']['L']['dash'] = $this->getCSSBorderDashStyle($brd_styles[3]);
                                if ($dom[$key]['border']['L']['dash'] < 0) {
                                    $dom[$key]['border']['L'] = array();
                                }
                            }
                            if (isset($brd_styles[1])) {
                                $dom[$key]['border']['R']['cap']  = 'square';
                                $dom[$key]['border']['R']['join'] = 'miter';
                                $dom[$key]['border']['R']['dash'] = $this->getCSSBorderDashStyle($brd_styles[1]);
                                if ($dom[$key]['border']['R']['dash'] < 0) {
                                    $dom[$key]['border']['R'] = array();
                                }
                            }
                            if (isset($brd_styles[0])) {
                                $dom[$key]['border']['T']['cap']  = 'square';
                                $dom[$key]['border']['T']['join'] = 'miter';
                                $dom[$key]['border']['T']['dash'] = $this->getCSSBorderDashStyle($brd_styles[0]);
                                if ($dom[$key]['border']['T']['dash'] < 0) {
                                    $dom[$key]['border']['T'] = array();
                                }
                            }
                            if (isset($brd_styles[2])) {
                                $dom[$key]['border']['B']['cap']  = 'square';
                                $dom[$key]['border']['B']['join'] = 'miter';
                                $dom[$key]['border']['B']['dash'] = $this->getCSSBorderDashStyle($brd_styles[2]);
                                if ($dom[$key]['border']['B']['dash'] < 0) {
                                    $dom[$key]['border']['B'] = array();
                                }
                            }
                        }
                        $cellside = array(
                            'L' => 'left',
                            'R' => 'right',
                            'T' => 'top',
                            'B' => 'bottom'
                        );
                        foreach ($cellside as $bsk => $bsv) {
                            if (isset($dom[$key]['style']['border-' . $bsv])) {
                                $borderstyle = $this->getCSSBorderStyle($dom[$key]['style']['border-' . $bsv]);
                                if (!empty($borderstyle)) {
                                    $dom[$key]['border'][$bsk] = $borderstyle;
                                }
                            }
                            if (isset($dom[$key]['style']['border-' . $bsv . '-color'])) {
                                $dom[$key]['border'][$bsk]['color'] = $this->convertHTMLColorToDec($dom[$key]['style']['border-' . $bsv . '-color']);
                            }
                            if (isset($dom[$key]['style']['border-' . $bsv . '-width'])) {
                                $dom[$key]['border'][$bsk]['width'] = $this->getCSSBorderWidth($dom[$key]['style']['border-' . $bsv . '-width']);
                            }
                            if (isset($dom[$key]['style']['border-' . $bsv . '-style'])) {
                                $dom[$key]['border'][$bsk]['dash'] = $this->getCSSBorderDashStyle($dom[$key]['style']['border-' . $bsv . '-style']);
                                if ($dom[$key]['border'][$bsk]['dash'] < 0) {
                                    $dom[$key]['border'][$bsk] = array();
                                }
                            }
                        }
                        if (isset($dom[$key]['style']['padding'])) {
                            $dom[$key]['padding'] = $this->getCSSPadding($dom[$key]['style']['padding']);
                        } else {
                            $dom[$key]['padding'] = $this->cell_padding;
                        }
                        foreach ($cellside as $psk => $psv) {
                            if (isset($dom[$key]['style']['padding-' . $psv])) {
                                $dom[$key]['padding'][$psk] = $this->getHTMLUnitToUnits($dom[$key]['style']['padding-' . $psv], 0, 'px', false);
                            }
                        }
                        if (isset($dom[$key]['style']['margin'])) {
                            $dom[$key]['margin'] = $this->getCSSMargin($dom[$key]['style']['margin']);
                        } else {
                            $dom[$key]['margin'] = $this->cell_margin;
                        }
                        foreach ($cellside as $psk => $psv) {
                            if (isset($dom[$key]['style']['margin-' . $psv])) {
                                $dom[$key]['margin'][$psk] = $this->getHTMLUnitToUnits(str_replace('auto', '0', $dom[$key]['style']['margin-' . $psv]), 0, 'px', false);
                            }
                        }
                        if (isset($dom[$key]['style']['border-spacing'])) {
                            $dom[$key]['border-spacing'] = $this->getCSSBorderMargin($dom[$key]['style']['border-spacing']);
                        }
                        if (isset($dom[$key]['style']['page-break-inside']) AND ($dom[$key]['style']['page-break-inside'] == 'avoid')) {
                            $dom[$key]['attribute']['nobr'] = 'true';
                        }
                        if (isset($dom[$key]['style']['page-break-before'])) {
                            if ($dom[$key]['style']['page-break-before'] == 'always') {
                                $dom[$key]['attribute']['pagebreak'] = 'true';
                            } elseif ($dom[$key]['style']['page-break-before'] == 'left') {
                                $dom[$key]['attribute']['pagebreak'] = 'left';
                            } elseif ($dom[$key]['style']['page-break-before'] == 'right') {
                                $dom[$key]['attribute']['pagebreak'] = 'right';
                            }
                        }
                        if (isset($dom[$key]['style']['page-break-after'])) {
                            if ($dom[$key]['style']['page-break-after'] == 'always') {
                                $dom[$key]['attribute']['pagebreakafter'] = 'true';
                            } elseif ($dom[$key]['style']['page-break-after'] == 'left') {
                                $dom[$key]['attribute']['pagebreakafter'] = 'left';
                            } elseif ($dom[$key]['style']['page-break-after'] == 'right') {
                                $dom[$key]['attribute']['pagebreakafter'] = 'right';
                            }
                        }
                    }
                    if (isset($dom[$key]['attribute']['display'])) {
                        $dom[$key]['hide'] = (trim(strtolower($dom[$key]['attribute']['display'])) == 'none');
                    }
                    if (isset($dom[$key]['attribute']['border']) AND ($dom[$key]['attribute']['border'] != 0)) {
                        $borderstyle = $this->getCSSBorderStyle($dom[$key]['attribute']['border'] . ' solid black');
                        if (!empty($borderstyle)) {
                            $dom[$key]['border']['LTRB'] = $borderstyle;
                        }
                    }
                    if ($dom[$key]['value'] == 'font') {
                        if (isset($dom[$key]['attribute']['face'])) {
                            $dom[$key]['fontname'] = $this->getFontFamilyName($dom[$key]['attribute']['face']);
                        }
                        if (isset($dom[$key]['attribute']['size'])) {
                            if ($key > 0) {
                                if ($dom[$key]['attribute']['size']{0} == '+') {
                                    $dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'] + intval(substr($dom[$key]['attribute']['size'], 1));
                                } elseif ($dom[$key]['attribute']['size']{0} == '-') {
                                    $dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'] - intval(substr($dom[$key]['attribute']['size'], 1));
                                } else {
                                    $dom[$key]['fontsize'] = intval($dom[$key]['attribute']['size']);
                                }
                            } else {
                                $dom[$key]['fontsize'] = intval($dom[$key]['attribute']['size']);
                            }
                        }
                    }
                    if ((($dom[$key]['value'] == 'ul') OR ($dom[$key]['value'] == 'ol') OR ($dom[$key]['value'] == 'dl')) AND (!isset($dom[$key]['align']) OR $this->empty_string($dom[$key]['align']) OR ($dom[$key]['align'] != 'J'))) {
                        if ($this->rtl) {
                            $dom[$key]['align'] = 'R';
                        } else {
                            $dom[$key]['align'] = 'L';
                        }
                    }
                    if (($dom[$key]['value'] == 'small') OR ($dom[$key]['value'] == 'sup') OR ($dom[$key]['value'] == 'sub')) {
                        if (!isset($dom[$key]['attribute']['size']) AND !isset($dom[$key]['style']['font-size'])) {
                            $dom[$key]['fontsize'] = $dom[$key]['fontsize'] * K_SMALL_RATIO;
                        }
                    }
                    if (($dom[$key]['value'] == 'strong') OR ($dom[$key]['value'] == 'b')) {
                        $dom[$key]['fontstyle'] .= 'B';
                    }
                    if (($dom[$key]['value'] == 'em') OR ($dom[$key]['value'] == 'i')) {
                        $dom[$key]['fontstyle'] .= 'I';
                    }
                    if ($dom[$key]['value'] == 'u') {
                        $dom[$key]['fontstyle'] .= 'U';
                    }
                    if (($dom[$key]['value'] == 'del') OR ($dom[$key]['value'] == 's') OR ($dom[$key]['value'] == 'strike')) {
                        $dom[$key]['fontstyle'] .= 'D';
                    }
                    if (!isset($dom[$key]['style']['text-decoration']) AND ($dom[$key]['value'] == 'a')) {
                        $dom[$key]['fontstyle'] = $this->htmlLinkFontStyle;
                    }
                    if (($dom[$key]['value'] == 'pre') OR ($dom[$key]['value'] == 'tt')) {
                        $dom[$key]['fontname'] = $this->default_monospaced_font;
                    }
                    if (($dom[$key]['value']{0} == 'h') AND (intval($dom[$key]['value']{1}) > 0) AND (intval($dom[$key]['value']{1}) < 7)) {
                        if (!isset($dom[$key]['attribute']['size']) AND !isset($dom[$key]['style']['font-size'])) {
                            $headsize              = (4 - intval($dom[$key]['value']{1})) * 2;
                            $dom[$key]['fontsize'] = $dom[0]['fontsize'] + $headsize;
                        }
                        if (!isset($dom[$key]['style']['font-weight'])) {
                            $dom[$key]['fontstyle'] .= 'B';
                        }
                    }
                    if (($dom[$key]['value'] == 'table')) {
                        $dom[$key]['rows']  = 0;
                        $dom[$key]['trids'] = array();
                        $dom[$key]['thead'] = '';
                    }
                    if (($dom[$key]['value'] == 'tr')) {
                        $dom[$key]['cols'] = 0;
                        if ($thead) {
                            $dom[$key]['thead'] = true;
                        } else {
                            $dom[$key]['thead'] = false;
                            ++$dom[($dom[$key]['parent'])]['rows'];
                            array_push($dom[($dom[$key]['parent'])]['trids'], $key);
                        }
                    }
                    if (($dom[$key]['value'] == 'th') OR ($dom[$key]['value'] == 'td')) {
                        if (isset($dom[$key]['attribute']['colspan'])) {
                            $colspan = intval($dom[$key]['attribute']['colspan']);
                        } else {
                            $colspan = 1;
                        }
                        $dom[$key]['attribute']['colspan'] = $colspan;
                        $dom[($dom[$key]['parent'])]['cols'] += $colspan;
                    }
                    if (isset($dom[$key]['attribute']['dir'])) {
                        $dom[$key]['dir'] = $dom[$key]['attribute']['dir'];
                    }
                    if (isset($dom[$key]['attribute']['color']) AND (!$this->empty_string($dom[$key]['attribute']['color']))) {
                        $dom[$key]['fgcolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['color']);
                    } elseif (!isset($dom[$key]['style']['color']) AND ($dom[$key]['value'] == 'a')) {
                        $dom[$key]['fgcolor'] = $this->htmlLinkColorArray;
                    }
                    if (isset($dom[$key]['attribute']['bgcolor']) AND (!$this->empty_string($dom[$key]['attribute']['bgcolor']))) {
                        $dom[$key]['bgcolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['bgcolor']);
                    }
                    if (isset($dom[$key]['attribute']['strokecolor']) AND (!$this->empty_string($dom[$key]['attribute']['strokecolor']))) {
                        $dom[$key]['strokecolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['strokecolor']);
                    }
                    if (isset($dom[$key]['attribute']['width'])) {
                        $dom[$key]['width'] = $dom[$key]['attribute']['width'];
                    }
                    if (isset($dom[$key]['attribute']['height'])) {
                        $dom[$key]['height'] = $dom[$key]['attribute']['height'];
                    }
                    if (isset($dom[$key]['attribute']['align']) AND (!$this->empty_string($dom[$key]['attribute']['align'])) AND ($dom[$key]['value'] !== 'img')) {
                        $dom[$key]['align'] = strtoupper($dom[$key]['attribute']['align']{0});
                    }
                    if (isset($dom[$key]['attribute']['stroke'])) {
                        $dom[$key]['stroke'] = $this->getHTMLUnitToUnits($dom[$key]['attribute']['stroke'], $dom[$key]['fontsize'], 'pt', true);
                    }
                    if (isset($dom[$key]['attribute']['fill'])) {
                        if ($dom[$key]['attribute']['fill'] == 'true') {
                            $dom[$key]['fill'] = true;
                        } else {
                            $dom[$key]['fill'] = false;
                        }
                    }
                    if (isset($dom[$key]['attribute']['clip'])) {
                        if ($dom[$key]['attribute']['clip'] == 'true') {
                            $dom[$key]['clip'] = true;
                        } else {
                            $dom[$key]['clip'] = false;
                        }
                    }
                }
            } else {
                $dom[$key]['tag']    = false;
                $dom[$key]['block']  = false;
                $dom[$key]['value']  = stripslashes($this->unhtmlentities($element));
                $dom[$key]['parent'] = end($level);
                $dom[$key]['dir']    = $dom[$dom[$key]['parent']]['dir'];
            }
            ++$elkey;
            ++$key;
        }
        return $dom;
    }
    protected function getSpaceString()
    {
        $spacestr = chr(32);
        if ($this->isUnicodeFont()) {
            $spacestr = chr(0) . chr(32);
        }
        return $spacestr;
    }
    public function writeHTMLCell($w, $h, $x, $y, $html = '', $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true)
    {
        return $this->MultiCell($w, $h, $html, $border, $align, $fill, $ln, $x, $y, $reseth, 0, true, $autopadding, 0, 'T', false);
    }
    public function writeHTML($html, $ln = true, $fill = false, $reseth = false, $cell = false, $align = '')
    {
        $gvars             = $this->getGraphicVars();
        $prev_cell_margin  = $this->cell_margin;
        $prev_cell_padding = $this->cell_padding;
        $prevPage          = $this->page;
        $prevlMargin       = $this->lMargin;
        $prevrMargin       = $this->rMargin;
        $curfontname       = $this->FontFamily;
        $curfontstyle      = $this->FontStyle;
        $curfontsize       = $this->FontSizePt;
        $curfontascent     = $this->getFontAscent($curfontname, $curfontstyle, $curfontsize);
        $curfontdescent    = $this->getFontDescent($curfontname, $curfontstyle, $curfontsize);
        $curfontstretcing  = $this->font_stretching;
        $curfontkerning    = $this->font_spacing;
        $this->newline     = true;
        $newline           = true;
        $startlinepage     = $this->page;
        $minstartliney     = $this->y;
        $maxbottomliney    = 0;
        $startlinex        = $this->x;
        $startliney        = $this->y;
        $yshift            = 0;
        $loop              = 0;
        $curpos            = 0;
        $this_method_vars  = array();
        $undo              = false;
        $fontaligned       = false;
        $reverse_dir       = false;
        $this->premode     = false;
        if ($this->inxobj) {
            $pask = count($this->xobjects[$this->xobjid]['annotations']);
        } elseif (isset($this->PageAnnots[$this->page])) {
            $pask = count($this->PageAnnots[$this->page]);
        } else {
            $pask = 0;
        }
        if ($this->inxobj) {
            $startlinepos = strlen($this->xobjects[$this->xobjid]['outdata']);
        } elseif (!$this->InFooter) {
            if (isset($this->footerlen[$this->page])) {
                $this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
            } else {
                $this->footerpos[$this->page] = $this->pagelen[$this->page];
            }
            $startlinepos = $this->footerpos[$this->page];
        } else {
            $startlinepos = $this->pagelen[$this->page];
        }
        $lalign  = $align;
        $plalign = $align;
        if ($this->rtl) {
            $w = $this->x - $this->lMargin;
        } else {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $w -= ($this->cell_padding['L'] + $this->cell_padding['R']);
        if ($cell) {
            if ($this->rtl) {
                $this->x -= $this->cell_padding['R'];
                $this->lMargin += $this->cell_padding['R'];
            } else {
                $this->x += $this->cell_padding['L'];
                $this->rMargin += $this->cell_padding['L'];
            }
        }
        if ($this->customlistindent >= 0) {
            $this->listindent = $this->customlistindent;
        } else {
            $this->listindent = $this->GetStringWidth('000000');
        }
        $this->listindentlevel  = 0;
        $prev_cell_height_ratio = $this->cell_height_ratio;
        $prev_listnum           = $this->listnum;
        $prev_listordered       = $this->listordered;
        $prev_listcount         = $this->listcount;
        $prev_lispacer          = $this->lispacer;
        $this->listnum          = 0;
        $this->listordered      = array();
        $this->listcount        = array();
        $this->lispacer         = '';
        if (($this->empty_string($this->lasth)) OR ($reseth)) {
            $this->resetLastH();
        }
        $dom             = $this->getHtmlDomArray($html);
        $maxel           = count($dom);
        $key             = 0;
        $hidden_node_key = -1;
        while ($key < $maxel) {
            if ($dom[$key]['tag']) {
                if ($dom[$key]['opening']) {
                    if (($hidden_node_key <= 0) AND $dom[$key]['hide']) {
                        $hidden_node_key = $key;
                    }
                } elseif (($hidden_node_key > 0) AND ($dom[$key]['parent'] == $hidden_node_key)) {
                    $hidden_node_key = 0;
                }
            }
            if ($hidden_node_key >= 0) {
                ++$key;
                if ($hidden_node_key == 0) {
                    $hidden_node_key = -1;
                }
                continue;
            }
            if ($dom[$key]['tag'] AND isset($dom[$key]['attribute']['pagebreak'])) {
                if (($dom[$key]['attribute']['pagebreak'] == 'true') OR ($dom[$key]['attribute']['pagebreak'] == 'left') OR ($dom[$key]['attribute']['pagebreak'] == 'right')) {
                    $this->checkPageBreak($this->PageBreakTrigger + 1);
                }
                if ((($dom[$key]['attribute']['pagebreak'] == 'left') AND (((!$this->rtl) AND (($this->page % 2) == 0)) OR (($this->rtl) AND (($this->page % 2) != 0)))) OR (($dom[$key]['attribute']['pagebreak'] == 'right') AND (((!$this->rtl) AND (($this->page % 2) != 0)) OR (($this->rtl) AND (($this->page % 2) == 0))))) {
                    $this->checkPageBreak($this->PageBreakTrigger + 1);
                }
            }
            if ($dom[$key]['tag'] AND $dom[$key]['opening'] AND isset($dom[$key]['attribute']['nobr']) AND ($dom[$key]['attribute']['nobr'] == 'true')) {
                if (isset($dom[($dom[$key]['parent'])]['attribute']['nobr']) AND ($dom[($dom[$key]['parent'])]['attribute']['nobr'] == 'true')) {
                    $dom[$key]['attribute']['nobr'] = false;
                } else {
                    $this->startTransaction();
                    $this_method_vars['html']                   = $html;
                    $this_method_vars['ln']                     = $ln;
                    $this_method_vars['fill']                   = $fill;
                    $this_method_vars['reseth']                 = $reseth;
                    $this_method_vars['cell']                   = $cell;
                    $this_method_vars['align']                  = $align;
                    $this_method_vars['gvars']                  = $gvars;
                    $this_method_vars['prevPage']               = $prevPage;
                    $this_method_vars['prev_cell_margin']       = $prev_cell_margin;
                    $this_method_vars['prev_cell_padding']      = $prev_cell_padding;
                    $this_method_vars['prevlMargin']            = $prevlMargin;
                    $this_method_vars['prevrMargin']            = $prevrMargin;
                    $this_method_vars['curfontname']            = $curfontname;
                    $this_method_vars['curfontstyle']           = $curfontstyle;
                    $this_method_vars['curfontsize']            = $curfontsize;
                    $this_method_vars['curfontascent']          = $curfontascent;
                    $this_method_vars['curfontdescent']         = $curfontdescent;
                    $this_method_vars['curfontstretcing']       = $curfontstretcing;
                    $this_method_vars['curfontkerning']         = $curfontkerning;
                    $this_method_vars['minstartliney']          = $minstartliney;
                    $this_method_vars['maxbottomliney']         = $maxbottomliney;
                    $this_method_vars['yshift']                 = $yshift;
                    $this_method_vars['startlinepage']          = $startlinepage;
                    $this_method_vars['startlinepos']           = $startlinepos;
                    $this_method_vars['startlinex']             = $startlinex;
                    $this_method_vars['startliney']             = $startliney;
                    $this_method_vars['newline']                = $newline;
                    $this_method_vars['loop']                   = $loop;
                    $this_method_vars['curpos']                 = $curpos;
                    $this_method_vars['pask']                   = $pask;
                    $this_method_vars['lalign']                 = $lalign;
                    $this_method_vars['plalign']                = $plalign;
                    $this_method_vars['w']                      = $w;
                    $this_method_vars['prev_cell_height_ratio'] = $prev_cell_height_ratio;
                    $this_method_vars['prev_listnum']           = $prev_listnum;
                    $this_method_vars['prev_listordered']       = $prev_listordered;
                    $this_method_vars['prev_listcount']         = $prev_listcount;
                    $this_method_vars['prev_lispacer']          = $prev_lispacer;
                    $this_method_vars['fontaligned']            = $fontaligned;
                    $this_method_vars['key']                    = $key;
                    $this_method_vars['dom']                    = $dom;
                }
            }
            if (($dom[$key]['value'] == 'tr') AND isset($dom[$key]['thead']) AND $dom[$key]['thead']) {
                if (isset($dom[$key]['parent']) AND isset($dom[$dom[$key]['parent']]['thead']) AND !$this->empty_string($dom[$dom[$key]['parent']]['thead'])) {
                    $this->inthead = true;
                    $this->writeHTML($this->thead, false, false, false, false, '');
                    if (($this->y < $this->start_transaction_y) OR ($this->checkPageBreak($this->lasth, '', false))) {
                        $this->rollbackTransaction(true);
                        foreach ($this_method_vars as $vkey => $vval) {
                            $$vkey = $vval;
                        }
                        $tmp_thead   = $this->thead;
                        $this->thead = '';
                        $pre_y       = $this->y;
                        if ((!$this->checkPageBreak($this->PageBreakTrigger + 1)) AND ($this->y < $pre_y)) {
                            $startliney = $this->y;
                        }
                        $this->start_transaction_page = $this->page;
                        $this->start_transaction_y    = $this->y;
                        $this->thead                  = $tmp_thead;
                        if (isset($dom[$dom[$key]['parent']]['attribute']['cellspacing'])) {
                            $tmp_cellspacing = $this->getHTMLUnitToUnits($dom[$dom[$key]['parent']]['attribute']['cellspacing'], 1, 'px');
                        } elseif (isset($dom[$dom[$key]['parent']]['border-spacing'])) {
                            $tmp_cellspacing = $dom[$dom[$key]['parent']]['border-spacing']['V'];
                        } else {
                            $tmp_cellspacing = 0;
                        }
                        $dom[$dom[$key]['parent']]['borderposition']['page']   = $this->page;
                        $dom[$dom[$key]['parent']]['borderposition']['column'] = $this->current_column;
                        $dom[$dom[$key]['parent']]['borderposition']['y']      = $this->y + $tmp_cellspacing;
                        $xoffset                                               = ($this->x - $dom[$dom[$key]['parent']]['borderposition']['x']);
                        $dom[$dom[$key]['parent']]['borderposition']['x'] += $xoffset;
                        $dom[$dom[$key]['parent']]['borderposition']['xmax'] += $xoffset;
                        $this->writeHTML($this->thead, false, false, false, false, '');
                    }
                }
                while (($key < $maxel) AND (!(($dom[$key]['tag'] AND $dom[$key]['opening'] AND ($dom[$key]['value'] == 'tr') AND (!isset($dom[$key]['thead']) OR !$dom[$key]['thead'])) OR ($dom[$key]['tag'] AND (!$dom[$key]['opening']) AND ($dom[$key]['value'] == 'table'))))) {
                    ++$key;
                }
            }
            if ($dom[$key]['tag'] OR ($key == 0)) {
                if ((($dom[$key]['value'] == 'table') OR ($dom[$key]['value'] == 'tr')) AND (isset($dom[$key]['align']))) {
                    $dom[$key]['align'] = ($this->rtl) ? 'R' : 'L';
                }
                if ((!$this->newline) AND ($dom[$key]['value'] == 'img') AND (isset($dom[$key]['height'])) AND ($dom[$key]['height'] > 0)) {
                    $imgh          = $this->getHTMLUnitToUnits($dom[$key]['height'], $this->lasth, 'px');
                    $autolinebreak = false;
                    if (isset($dom[$key]['width']) AND ($dom[$key]['width'] > 0)) {
                        $imgw = $this->getHTMLUnitToUnits($dom[$key]['width'], 1, 'px', false);
                        if (($imgw <= ($this->w - $this->lMargin - $this->rMargin - $this->cell_padding['L'] - $this->cell_padding['R'])) AND ((($this->rtl) AND (($this->x - $imgw) < ($this->lMargin + $this->cell_padding['L']))) OR ((!$this->rtl) AND (($this->x + $imgw) > ($this->w - $this->rMargin - $this->cell_padding['R']))))) {
                            $autolinebreak = true;
                            $this->Ln('', $cell);
                            if ((!$dom[($key - 1)]['tag']) AND ($dom[($key - 1)]['value'] == ' ')) {
                                --$key;
                            }
                        }
                    }
                    if (!$autolinebreak) {
                        if ($this->inPageBody()) {
                            $pre_y = $this->y;
                            if ((!$this->checkPageBreak($imgh)) AND ($this->y < $pre_y)) {
                                $startliney = $this->y;
                            }
                        }
                        if ($this->page > $startlinepage) {
                            if (isset($this->footerlen[$startlinepage])) {
                                $curpos = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
                            }
                            $pagebuff = $this->getPageBuffer($startlinepage);
                            $linebeg  = substr($pagebuff, $startlinepos, ($curpos - $startlinepos));
                            $tstart   = substr($pagebuff, 0, $startlinepos);
                            $tend     = substr($this->getPageBuffer($startlinepage), $curpos);
                            $this->setPageBuffer($startlinepage, $tstart . '' . $tend);
                            $pagebuff = $this->getPageBuffer($this->page);
                            $tstart   = substr($pagebuff, 0, $this->cntmrk[$this->page]);
                            $tend     = substr($pagebuff, $this->cntmrk[$this->page]);
                            $yshift   = $minstartliney - $this->y;
                            if ($fontaligned) {
                                $yshift += ($curfontsize / $this->k);
                            }
                            $try = sprintf('1 0 0 1 0 %.3F cm', ($yshift * $this->k));
                            $this->setPageBuffer($this->page, $tstart . "\nq\n" . $try . "\n" . $linebeg . "\nQ\n" . $tend);
                            if (isset($this->PageAnnots[$this->page])) {
                                $next_pask = count($this->PageAnnots[$this->page]);
                            } else {
                                $next_pask = 0;
                            }
                            if (isset($this->PageAnnots[$startlinepage])) {
                                foreach ($this->PageAnnots[$startlinepage] as $pak => $pac) {
                                    if ($pak >= $pask) {
                                        $this->PageAnnots[$this->page][] = $pac;
                                        unset($this->PageAnnots[$startlinepage][$pak]);
                                        $npak = count($this->PageAnnots[$this->page]) - 1;
                                        $this->PageAnnots[$this->page][$npak]['y'] -= $yshift;
                                    }
                                }
                            }
                            $pask          = $next_pask;
                            $startlinepos  = $this->cntmrk[$this->page];
                            $startlinepage = $this->page;
                            $startliney    = $this->y;
                            $this->newline = false;
                        }
                        $this->y += ((($curfontsize * $this->cell_height_ratio / $this->k) + $curfontascent - $curfontdescent) / 2) - $imgh;
                        $minstartliney  = min($this->y, $minstartliney);
                        $maxbottomliney = ($startliney + ($this->FontSize * $this->cell_height_ratio));
                    }
                } elseif (isset($dom[$key]['fontname']) OR isset($dom[$key]['fontstyle']) OR isset($dom[$key]['fontsize']) OR isset($dom[$key]['line-height'])) {
                    $pfontname   = $curfontname;
                    $pfontstyle  = $curfontstyle;
                    $pfontsize   = $curfontsize;
                    $fontname    = isset($dom[$key]['fontname']) ? $dom[$key]['fontname'] : $curfontname;
                    $fontstyle   = isset($dom[$key]['fontstyle']) ? $dom[$key]['fontstyle'] : $curfontstyle;
                    $fontsize    = isset($dom[$key]['fontsize']) ? $dom[$key]['fontsize'] : $curfontsize;
                    $fontascent  = $this->getFontAscent($fontname, $fontstyle, $fontsize);
                    $fontdescent = $this->getFontDescent($fontname, $fontstyle, $fontsize);
                    if (($fontname != $curfontname) OR ($fontstyle != $curfontstyle) OR ($fontsize != $curfontsize) OR ($this->cell_height_ratio != $dom[$key]['line-height']) OR ($dom[$key]['tag'] AND $dom[$key]['opening'] AND ($dom[$key]['value'] == 'li'))) {
                        if (($key < ($maxel - 1)) AND (($dom[$key]['tag'] AND $dom[$key]['opening'] AND ($dom[$key]['value'] == 'li')) OR ($this->cell_height_ratio != $dom[$key]['line-height']) OR (!$this->newline AND is_numeric($fontsize) AND is_numeric($curfontsize) AND ($fontsize >= 0) AND ($curfontsize >= 0) AND ($fontsize != $curfontsize)))) {
                            if ($this->page > $startlinepage) {
                                if (isset($this->footerlen[$startlinepage])) {
                                    $curpos = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
                                }
                                $pagebuff = $this->getPageBuffer($startlinepage);
                                $linebeg  = substr($pagebuff, $startlinepos, ($curpos - $startlinepos));
                                $tstart   = substr($pagebuff, 0, $startlinepos);
                                $tend     = substr($this->getPageBuffer($startlinepage), $curpos);
                                $this->setPageBuffer($startlinepage, $tstart . '' . $tend);
                                $pagebuff = $this->getPageBuffer($this->page);
                                $tstart   = substr($pagebuff, 0, $this->cntmrk[$this->page]);
                                $tend     = substr($pagebuff, $this->cntmrk[$this->page]);
                                $yshift   = $minstartliney - $this->y;
                                $try      = sprintf('1 0 0 1 0 %.3F cm', ($yshift * $this->k));
                                $this->setPageBuffer($this->page, $tstart . "\nq\n" . $try . "\n" . $linebeg . "\nQ\n" . $tend);
                                if (isset($this->PageAnnots[$this->page])) {
                                    $next_pask = count($this->PageAnnots[$this->page]);
                                } else {
                                    $next_pask = 0;
                                }
                                if (isset($this->PageAnnots[$startlinepage])) {
                                    foreach ($this->PageAnnots[$startlinepage] as $pak => $pac) {
                                        if ($pak >= $pask) {
                                            $this->PageAnnots[$this->page][] = $pac;
                                            unset($this->PageAnnots[$startlinepage][$pak]);
                                            $npak = count($this->PageAnnots[$this->page]) - 1;
                                            $this->PageAnnots[$this->page][$npak]['y'] -= $yshift;
                                        }
                                    }
                                }
                                $pask          = $next_pask;
                                $startlinepos  = $this->cntmrk[$this->page];
                                $startlinepage = $this->page;
                                $startliney    = $this->y;
                            }
                            if (!isset($dom[$key]['line-height'])) {
                                $dom[$key]['line-height'] = $this->cell_height_ratio;
                            }
                            if (!$dom[$key]['block']) {
                                $this->y += (((($curfontsize * $this->cell_height_ratio) - ($fontsize * $dom[$key]['line-height'])) / $this->k) + $curfontascent - $fontascent - $curfontdescent + $fontdescent) / 2;
                                if (($dom[$key]['value'] != 'sup') AND ($dom[$key]['value'] != 'sub')) {
                                    $minstartliney  = min($this->y, $minstartliney);
                                    $maxbottomliney = max(($this->y + (($fontsize * $this->cell_height_ratio) / $this->k)), $maxbottomliney);
                                }
                            }
                            $this->cell_height_ratio = $dom[$key]['line-height'];
                            $fontaligned             = true;
                        }
                        $this->SetFont($fontname, $fontstyle, $fontsize);
                        $this->resetLastH();
                        $curfontname    = $fontname;
                        $curfontstyle   = $fontstyle;
                        $curfontsize    = $fontsize;
                        $curfontascent  = $fontascent;
                        $curfontdescent = $fontdescent;
                    }
                }
                $textstroke = isset($dom[$key]['stroke']) ? $dom[$key]['stroke'] : $this->textstrokewidth;
                $textfill   = isset($dom[$key]['fill']) ? $dom[$key]['fill'] : (($this->textrendermode % 2) == 0);
                $textclip   = isset($dom[$key]['clip']) ? $dom[$key]['clip'] : ($this->textrendermode > 3);
                $this->setTextRenderingMode($textstroke, $textfill, $textclip);
                if (isset($dom[$key]['font-stretch']) AND ($dom[$key]['font-stretch'] !== false)) {
                    $this->setFontStretching($dom[$key]['font-stretch']);
                }
                if (isset($dom[$key]['letter-spacing']) AND ($dom[$key]['letter-spacing'] !== false)) {
                    $this->setFontSpacing($dom[$key]['letter-spacing']);
                }
                if (($plalign == 'J') AND $dom[$key]['block']) {
                    $plalign = '';
                }
                $curpos = $this->pagelen[$startlinepage];
                if (isset($dom[$key]['bgcolor']) AND ($dom[$key]['bgcolor'] !== false)) {
                    $this->SetFillColorArray($dom[$key]['bgcolor']);
                    $wfill = true;
                } else {
                    $wfill = $fill | false;
                }
                if (isset($dom[$key]['fgcolor']) AND ($dom[$key]['fgcolor'] !== false)) {
                    $this->SetTextColorArray($dom[$key]['fgcolor']);
                }
                if (isset($dom[$key]['strokecolor']) AND ($dom[$key]['strokecolor'] !== false)) {
                    $this->SetDrawColorArray($dom[$key]['strokecolor']);
                }
                if (isset($dom[$key]['align'])) {
                    $lalign = $dom[$key]['align'];
                }
                if ($this->empty_string($lalign)) {
                    $lalign = $align;
                }
            }
            if ($this->newline AND (strlen($dom[$key]['value']) > 0) AND ($dom[$key]['value'] != 'td') AND ($dom[$key]['value'] != 'th')) {
                $newline     = true;
                $fontaligned = false;
                if (isset($startlinex)) {
                    $yshift = $minstartliney - $startliney;
                    if (($yshift > 0) OR ($this->page > $startlinepage)) {
                        $yshift = 0;
                    }
                    $t_x   = 0;
                    $linew = abs($this->endlinex - $startlinex);
                    if ($this->inxobj) {
                        $pstart = substr($this->xobjects[$this->xobjid]['outdata'], 0, $startlinepos);
                        if (isset($opentagpos)) {
                            $midpos = $opentagpos;
                        } else {
                            $midpos = 0;
                        }
                        if ($midpos > 0) {
                            $pmid = substr($this->xobjects[$this->xobjid]['outdata'], $startlinepos, ($midpos - $startlinepos));
                            $pend = substr($this->xobjects[$this->xobjid]['outdata'], $midpos);
                        } else {
                            $pmid = substr($this->xobjects[$this->xobjid]['outdata'], $startlinepos);
                            $pend = '';
                        }
                    } else {
                        $pstart = substr($this->getPageBuffer($startlinepage), 0, $startlinepos);
                        if (isset($opentagpos) AND isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
                            $this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
                            $midpos                          = min($opentagpos, $this->footerpos[$startlinepage]);
                        } elseif (isset($opentagpos)) {
                            $midpos = $opentagpos;
                        } elseif (isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
                            $this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
                            $midpos                          = $this->footerpos[$startlinepage];
                        } else {
                            $midpos = 0;
                        }
                        if ($midpos > 0) {
                            $pmid = substr($this->getPageBuffer($startlinepage), $startlinepos, ($midpos - $startlinepos));
                            $pend = substr($this->getPageBuffer($startlinepage), $midpos);
                        } else {
                            $pmid = substr($this->getPageBuffer($startlinepage), $startlinepos);
                            $pend = '';
                        }
                    }
                    if ((isset($plalign) AND ((($plalign == 'C') OR ($plalign == 'J') OR (($plalign == 'R') AND (!$this->rtl)) OR (($plalign == 'L') AND ($this->rtl)))))) {
                        $tw = $w;
                        if (($plalign == 'J') AND $this->isRTLTextDir() AND ($this->num_columns > 1)) {
                            $tw += $this->cell_padding['R'];
                        }
                        if ($this->lMargin != $prevlMargin) {
                            $tw += ($prevlMargin - $this->lMargin);
                        }
                        if ($this->rMargin != $prevrMargin) {
                            $tw += ($prevrMargin - $this->rMargin);
                        }
                        $one_space_width = $this->GetStringWidth(chr(32));
                        $no              = 0;
                        if ($this->isRTLTextDir()) {
                            $pos1 = $this->revstrpos($pmid, '[(');
                            if ($pos1 > 0) {
                                $pos1 = intval($pos1);
                                if ($this->isUnicodeFont()) {
                                    $pos2     = intval($this->revstrpos($pmid, '[(' . chr(0) . chr(32)));
                                    $spacelen = 2;
                                } else {
                                    $pos2     = intval($this->revstrpos($pmid, '[(' . chr(32)));
                                    $spacelen = 1;
                                }
                                if ($pos1 == $pos2) {
                                    $pmid = substr($pmid, 0, ($pos1 + 2)) . substr($pmid, ($pos1 + 2 + $spacelen));
                                    if (substr($pmid, $pos1, 4) == '[()]') {
                                        $linew -= $one_space_width;
                                    } elseif ($pos1 == strpos($pmid, '[(')) {
                                        $no = 1;
                                    }
                                }
                            }
                        } else {
                            $pos1 = $this->revstrpos($pmid, ')]');
                            if ($pos1 > 0) {
                                $pos1 = intval($pos1);
                                if ($this->isUnicodeFont()) {
                                    $pos2     = intval($this->revstrpos($pmid, chr(0) . chr(32) . ')]')) + 2;
                                    $spacelen = 2;
                                } else {
                                    $pos2     = intval($this->revstrpos($pmid, chr(32) . ')]')) + 1;
                                    $spacelen = 1;
                                }
                                if ($pos1 == $pos2) {
                                    $pmid = substr($pmid, 0, ($pos1 - $spacelen)) . substr($pmid, $pos1);
                                    $linew -= $one_space_width;
                                }
                            }
                        }
                        $mdiff = ($tw - $linew);
                        if ($plalign == 'C') {
                            if ($this->rtl) {
                                $t_x = -($mdiff / 2);
                            } else {
                                $t_x = ($mdiff / 2);
                            }
                        } elseif ($plalign == 'R') {
                            $t_x = $mdiff;
                        } elseif ($plalign == 'L') {
                            $t_x = -$mdiff;
                        } elseif (($plalign == 'J') AND ($plalign == $lalign)) {
                            if ($this->isRTLTextDir()) {
                                $t_x = -$mdiff;
                            }
                            $ns       = 0;
                            $pmidtemp = $pmid;
                            $pmidtemp = preg_replace('/[\\\][\(]/x', '\\#!#OP#!#', $pmidtemp);
                            $pmidtemp = preg_replace('/[\\\][\)]/x', '\\#!#CP#!#', $pmidtemp);
                            if (preg_match_all('/\[\(([^\)]*)\)\]/x', $pmidtemp, $lnstring, PREG_PATTERN_ORDER)) {
                                $spacestr = $this->getSpaceString();
                                $maxkk    = count($lnstring[1]) - 1;
                                for ($kk = 0; $kk <= $maxkk; ++$kk) {
                                    $lnstring[1][$kk] = str_replace('#!#OP#!#', '(', $lnstring[1][$kk]);
                                    $lnstring[1][$kk] = str_replace('#!#CP#!#', ')', $lnstring[1][$kk]);
                                    $lnstring[2][$kk] = substr_count($lnstring[1][$kk], $spacestr);
                                    $ns += $lnstring[2][$kk];
                                    $lnstring[3][$kk] = $ns;
                                }
                                if ($ns == 0) {
                                    $ns = 1;
                                }
                                $spacewidth  = ($mdiff / ($ns - $no)) * $this->k;
                                $spacewidthu = -1000 * ($mdiff + (($ns + $no) * $one_space_width)) / $ns / $this->FontSize;
                                if ($this->font_spacing != 0) {
                                    $osw = -1000 * $this->font_spacing / $this->FontSize;
                                    $spacewidthu += $osw;
                                }
                                $nsmax = $ns;
                                $ns    = 0;
                                reset($lnstring);
                                $offset         = 0;
                                $strcount       = 0;
                                $prev_epsposbeg = 0;
                                $textpos        = 0;
                                if ($this->isRTLTextDir()) {
                                    $textpos = $this->wPt;
                                }
                                global $spacew;
                                while (preg_match('/([0-9\.\+\-]*)[\s](Td|cm|m|l|c|re)[\s]/x', $pmid, $strpiece, PREG_OFFSET_CAPTURE, $offset) == 1) {
                                    $stroffset = strpos($pmid, '[(', $offset);
                                    if (($stroffset !== false) AND ($stroffset <= $strpiece[2][1])) {
                                        $offset = strpos($pmid, ')]', $stroffset);
                                        while (($offset !== false) AND ($pmid{($offset - 1)} == '\\')) {
                                            $offset = strpos($pmid, ')]', ($offset + 1));
                                        }
                                        if ($offset === false) {
                                            $this->Error('HTML Justification: malformed PDF code.');
                                        }
                                        continue;
                                    }
                                    if ($this->isRTLTextDir()) {
                                        $spacew = ($spacewidth * ($nsmax - $ns));
                                    } else {
                                        $spacew = ($spacewidth * $ns);
                                    }
                                    $offset    = $strpiece[2][1] + strlen($strpiece[2][0]);
                                    $epsposbeg = strpos($pmid, 'q' . $this->epsmarker, $offset);
                                    $epsposend = strpos($pmid, $this->epsmarker . 'Q', $offset) + strlen($this->epsmarker . 'Q');
                                    if ((($epsposbeg > 0) AND ($epsposend > 0) AND ($offset > $epsposbeg) AND ($offset < $epsposend)) OR (($epsposbeg === false) AND ($epsposend > 0) AND ($offset < $epsposend))) {
                                        $trx       = sprintf('1 0 0 1 %.3F 0 cm', $spacew);
                                        $epsposbeg = strpos($pmid, 'q' . $this->epsmarker, ($prev_epsposbeg - 6));
                                        $pmid_b    = substr($pmid, 0, $epsposbeg);
                                        $pmid_m    = substr($pmid, $epsposbeg, ($epsposend - $epsposbeg));
                                        $pmid_e    = substr($pmid, $epsposend);
                                        $pmid      = $pmid_b . "\nq\n" . $trx . "\n" . $pmid_m . "\nQ\n" . $pmid_e;
                                        $offset    = $epsposend;
                                        continue;
                                    }
                                    $prev_epsposbeg = $epsposbeg;
                                    $currentxpos    = 0;
                                    switch ($strpiece[2][0]) {
                                        case 'Td':
                                        case 'cm':
                                        case 'm':
                                        case 'l': {
                                            preg_match('/([0-9\.\+\-]*)[\s](' . $strpiece[1][0] . ')[\s](' . $strpiece[2][0] . ')([\s]*)/x', $pmid, $xmatches);
                                            $currentxpos = $xmatches[1];
                                            $textpos     = $currentxpos;
                                            if (($strcount <= $maxkk) AND ($strpiece[2][0] == 'Td')) {
                                                $ns = $lnstring[3][$strcount];
                                                if ($this->isRTLTextDir()) {
                                                    $spacew = ($spacewidth * ($nsmax - $ns));
                                                }
                                                ++$strcount;
                                            }
                                            $pmid = preg_replace_callback('/([0-9\.\+\-]*)[\s](' . $strpiece[1][0] . ')[\s](' . $strpiece[2][0] . ')([\s]*)/x', create_function('$matches', 'global $spacew;
												$newx = sprintf("%.2F",(floatval($matches[1]) + $spacew));
												return "".$newx." ".$matches[2]." x*#!#*x".$matches[3].$matches[4];'), $pmid, 1);
                                            break;
                                        }
                                        case 're': {
                                            if (!$this->empty_string($this->lispacer)) {
                                                $this->lispacer = '';
                                                continue;
                                            }
                                            preg_match('/([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s](' . $strpiece[1][0] . ')[\s](re)([\s]*)/x', $pmid, $xmatches);
                                            $currentxpos = $xmatches[1];
                                            global $x_diff, $w_diff;
                                            $x_diff = 0;
                                            $w_diff = 0;
                                            if ($this->isRTLTextDir()) {
                                                if ($currentxpos < $textpos) {
                                                    $x_diff = ($spacewidth * ($nsmax - $lnstring[3][$strcount]));
                                                    $w_diff = ($spacewidth * $lnstring[2][$strcount]);
                                                } else {
                                                    if ($strcount > 0) {
                                                        $x_diff = ($spacewidth * ($nsmax - $lnstring[3][($strcount - 1)]));
                                                        $w_diff = ($spacewidth * $lnstring[2][($strcount - 1)]);
                                                    }
                                                }
                                            } else {
                                                if ($currentxpos > $textpos) {
                                                    if ($strcount > 0) {
                                                        $x_diff = ($spacewidth * $lnstring[3][($strcount - 1)]);
                                                    }
                                                    $w_diff = ($spacewidth * $lnstring[2][$strcount]);
                                                } else {
                                                    if ($strcount > 1) {
                                                        $x_diff = ($spacewidth * $lnstring[3][($strcount - 2)]);
                                                    }
                                                    if ($strcount > 0) {
                                                        $w_diff = ($spacewidth * $lnstring[2][($strcount - 1)]);
                                                    }
                                                }
                                            }
                                            $pmid = preg_replace_callback('/(' . $xmatches[1] . ')[\s](' . $xmatches[2] . ')[\s](' . $xmatches[3] . ')[\s](' . $strpiece[1][0] . ')[\s](re)([\s]*)/x', create_function('$matches', 'global $x_diff, $w_diff;
												$newx = sprintf("%.2F",(floatval($matches[1]) + $x_diff));
												$neww = sprintf("%.2F",(floatval($matches[3]) + $w_diff));
												return "".$newx." ".$matches[2]." ".$neww." ".$matches[4]." x*#!#*x".$matches[5].$matches[6];'), $pmid, 1);
                                            break;
                                        }
                                        case 'c': {
                                            preg_match('/([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s](' . $strpiece[1][0] . ')[\s](c)([\s]*)/x', $pmid, $xmatches);
                                            $currentxpos = $xmatches[1];
                                            $pmid        = preg_replace_callback('/(' . $xmatches[1] . ')[\s](' . $xmatches[2] . ')[\s](' . $xmatches[3] . ')[\s](' . $xmatches[4] . ')[\s](' . $xmatches[5] . ')[\s](' . $strpiece[1][0] . ')[\s](c)([\s]*)/x', create_function('$matches', 'global $spacew;
												$newx1 = sprintf("%.3F",(floatval($matches[1]) + $spacew));
												$newx2 = sprintf("%.3F",(floatval($matches[3]) + $spacew));
												$newx3 = sprintf("%.3F",(floatval($matches[5]) + $spacew));
												return "".$newx1." ".$matches[2]." ".$newx2." ".$matches[4]." ".$newx3." ".$matches[6]." x*#!#*x".$matches[7].$matches[8];'), $pmid, 1);
                                            break;
                                        }
                                    }
                                    $cxpos = ($currentxpos / $this->k);
                                    $lmpos = ($this->lMargin + $this->cell_padding['L'] + $this->feps);
                                    if ($this->inxobj) {
                                        foreach ($this->xobjects[$this->xobjid]['annotations'] as $pak => $pac) {
                                            if (($pac['y'] >= $minstartliney) AND (($pac['x'] * $this->k) >= ($currentxpos - $this->feps)) AND (($pac['x'] * $this->k) <= ($currentxpos + $this->feps))) {
                                                if ($cxpos > $lmpos) {
                                                    $this->xobjects[$this->xobjid]['annotations'][$pak]['x'] += ($spacew / $this->k);
                                                    $this->xobjects[$this->xobjid]['annotations'][$pak]['w'] += (($spacewidth * $pac['numspaces']) / $this->k);
                                                } else {
                                                    $this->xobjects[$this->xobjid]['annotations'][$pak]['w'] += (($spacewidth * $pac['numspaces']) / $this->k);
                                                }
                                                break;
                                            }
                                        }
                                    } elseif (isset($this->PageAnnots[$this->page])) {
                                        foreach ($this->PageAnnots[$this->page] as $pak => $pac) {
                                            if (($pac['y'] >= $minstartliney) AND (($pac['x'] * $this->k) >= ($currentxpos - $this->feps)) AND (($pac['x'] * $this->k) <= ($currentxpos + $this->feps))) {
                                                if ($cxpos > $lmpos) {
                                                    $this->PageAnnots[$this->page][$pak]['x'] += ($spacew / $this->k);
                                                    $this->PageAnnots[$this->page][$pak]['w'] += (($spacewidth * $pac['numspaces']) / $this->k);
                                                } else {
                                                    $this->PageAnnots[$this->page][$pak]['w'] += (($spacewidth * $pac['numspaces']) / $this->k);
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                                $pmid = str_replace('x*#!#*x', '', $pmid);
                                if ($this->isUnicodeFont()) {
                                    $spacew = $spacewidthu;
                                    if ($this->font_stretching != 100) {
                                        $spacew /= ($this->font_stretching / 100);
                                    }
                                    $pmidtemp = $pmid;
                                    $pmidtemp = preg_replace('/[\\\][\(]/x', '\\#!#OP#!#', $pmidtemp);
                                    $pmidtemp = preg_replace('/[\\\][\)]/x', '\\#!#CP#!#', $pmidtemp);
                                    $pmid     = preg_replace_callback("/\[\(([^\)]*)\)\]/x", create_function('$matches', 'global $spacew;
												$matches[1] = str_replace("#!#OP#!#", "(", $matches[1]);
												$matches[1] = str_replace("#!#CP#!#", ")", $matches[1]);
												return "[(".str_replace(chr(0).chr(32), ") ".sprintf("%.3F", $spacew)." (", $matches[1]).")]";'), $pmidtemp);
                                    if ($this->inxobj) {
                                        $this->xobjects[$this->xobjid]['outdata'] = $pstart . "\n" . $pmid . "\n" . $pend;
                                    } else {
                                        $this->setPageBuffer($startlinepage, $pstart . "\n" . $pmid . "\n" . $pend);
                                    }
                                    $endlinepos = strlen($pstart . "\n" . $pmid . "\n");
                                } else {
                                    if ($this->font_stretching != 100) {
                                        $spacewidth /= ($this->font_stretching / 100);
                                    }
                                    $rs   = sprintf('%.3F Tw', $spacewidth);
                                    $pmid = preg_replace("/\[\(/x", $rs . ' [(', $pmid);
                                    if ($this->inxobj) {
                                        $this->xobjects[$this->xobjid]['outdata'] = $pstart . "\n" . $pmid . "\nBT 0 Tw ET\n" . $pend;
                                    } else {
                                        $this->setPageBuffer($startlinepage, $pstart . "\n" . $pmid . "\nBT 0 Tw ET\n" . $pend);
                                    }
                                    $endlinepos = strlen($pstart . "\n" . $pmid . "\nBT 0 Tw ET\n");
                                }
                            }
                        }
                    }
                    if (($t_x != 0) OR ($yshift < 0)) {
                        $trx = sprintf('1 0 0 1 %.3F %.3F cm', ($t_x * $this->k), ($yshift * $this->k));
                        $pstart .= "\nq\n" . $trx . "\n" . $pmid . "\nQ\n";
                        $endlinepos = strlen($pstart);
                        if ($this->inxobj) {
                            $this->xobjects[$this->xobjid]['outdata'] = $pstart . $pend;
                            foreach ($this->xobjects[$this->xobjid]['annotations'] as $pak => $pac) {
                                if ($pak >= $pask) {
                                    $this->xobjects[$this->xobjid]['annotations'][$pak]['x'] += $t_x;
                                    $this->xobjects[$this->xobjid]['annotations'][$pak]['y'] -= $yshift;
                                }
                            }
                        } else {
                            $this->setPageBuffer($startlinepage, $pstart . $pend);
                            if (isset($this->PageAnnots[$this->page])) {
                                foreach ($this->PageAnnots[$this->page] as $pak => $pac) {
                                    if ($pak >= $pask) {
                                        $this->PageAnnots[$this->page][$pak]['x'] += $t_x;
                                        $this->PageAnnots[$this->page][$pak]['y'] -= $yshift;
                                    }
                                }
                            }
                        }
                        $this->y -= $yshift;
                    }
                }
                $pbrk          = $this->checkPageBreak($this->lasth);
                $this->newline = false;
                $startlinex    = $this->x;
                $startliney    = $this->y;
                if ($dom[$dom[$key]['parent']]['value'] == 'sup') {
                    $startliney -= ((0.3 * $this->FontSizePt) / $this->k);
                } elseif ($dom[$dom[$key]['parent']]['value'] == 'sub') {
                    $startliney -= (($this->FontSizePt / 0.7) / $this->k);
                } else {
                    $minstartliney  = $startliney;
                    $maxbottomliney = ($this->y + (($fontsize * $this->cell_height_ratio) / $this->k));
                }
                $startlinepage = $this->page;
                if (isset($endlinepos) AND (!$pbrk)) {
                    $startlinepos = $endlinepos;
                } else {
                    if ($this->inxobj) {
                        $startlinepos = strlen($this->xobjects[$this->xobjid]['outdata']);
                    } elseif (!$this->InFooter) {
                        if (isset($this->footerlen[$this->page])) {
                            $this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
                        } else {
                            $this->footerpos[$this->page] = $this->pagelen[$this->page];
                        }
                        $startlinepos = $this->footerpos[$this->page];
                    } else {
                        $startlinepos = $this->pagelen[$this->page];
                    }
                }
                unset($endlinepos);
                $plalign = $lalign;
                if (isset($this->PageAnnots[$this->page])) {
                    $pask = count($this->PageAnnots[$this->page]);
                } else {
                    $pask = 0;
                }
                if (!($dom[$key]['tag'] AND !$dom[$key]['opening'] AND ($dom[$key]['value'] == 'table') AND (isset($this->emptypagemrk[$this->page])) AND ($this->emptypagemrk[$this->page] == $this->pagelen[$this->page]))) {
                    $this->SetFont($fontname, $fontstyle, $fontsize);
                    if ($wfill) {
                        $this->SetFillColorArray($this->bgcolor);
                    }
                }
            }
            if (isset($opentagpos)) {
                unset($opentagpos);
            }
            if ($dom[$key]['tag']) {
                if ($dom[$key]['opening']) {
                    if (isset($dom[$key]['text-indent']) AND $dom[$key]['block']) {
                        $this->textindent = $dom[$key]['text-indent'];
                        $this->newline    = true;
                    }
                    if ($dom[$key]['value'] == 'table') {
                        if ($this->rtl) {
                            $wtmp = $this->x - $this->lMargin;
                        } else {
                            $wtmp = $this->w - $this->rMargin - $this->x;
                        }
                        if (isset($dom[$key]['attribute']['cellspacing'])) {
                            $clsp        = $this->getHTMLUnitToUnits($dom[$key]['attribute']['cellspacing'], 1, 'px');
                            $cellspacing = array(
                                'H' => $clsp,
                                'V' => $clsp
                            );
                        } elseif (isset($dom[$key]['border-spacing'])) {
                            $cellspacing = $dom[$key]['border-spacing'];
                        } else {
                            $cellspacing = array(
                                'H' => 0,
                                'V' => 0
                            );
                        }
                        if (isset($dom[$key]['width'])) {
                            $table_width = $this->getHTMLUnitToUnits($dom[$key]['width'], $wtmp, 'px');
                        } else {
                            $table_width = $wtmp;
                        }
                        $table_width -= (2 * $cellspacing['H']);
                        if (!$this->inthead) {
                            $this->y += $cellspacing['V'];
                        }
                        if ($this->rtl) {
                            $cellspacingx = -$cellspacing['H'];
                        } else {
                            $cellspacingx = $cellspacing['H'];
                        }
                        $table_columns_width    = ($table_width - ($cellspacing['H'] * ($dom[$key]['cols'] - 1)));
                        $table_min_column_width = ($table_columns_width / $dom[$key]['cols']);
                        $table_colwidths        = array_fill(0, $dom[$key]['cols'], $table_min_column_width);
                    }
                    if ($dom[$key]['value'] == 'tr') {
                        $colid = 0;
                    }
                    if (($dom[$key]['value'] == 'td') OR ($dom[$key]['value'] == 'th')) {
                        $trid     = $dom[$key]['parent'];
                        $table_el = $dom[$trid]['parent'];
                        if (!isset($dom[$table_el]['cols'])) {
                            $dom[$table_el]['cols'] = $dom[$trid]['cols'];
                        }
                        $tdborder = 0;
                        if (isset($dom[$key]['border']) AND !empty($dom[$key]['border'])) {
                            $tdborder = $dom[$key]['border'];
                        }
                        $colspan          = $dom[$key]['attribute']['colspan'];
                        $old_cell_padding = $this->cell_padding;
                        if (isset($dom[($dom[$trid]['parent'])]['attribute']['cellpadding'])) {
                            $crclpd               = $this->getHTMLUnitToUnits($dom[($dom[$trid]['parent'])]['attribute']['cellpadding'], 1, 'px');
                            $current_cell_padding = array(
                                'L' => $crclpd,
                                'T' => $crclpd,
                                'R' => $crclpd,
                                'B' => $crclpd
                            );
                        } elseif (isset($dom[($dom[$trid]['parent'])]['padding'])) {
                            $current_cell_padding = $dom[($dom[$trid]['parent'])]['padding'];
                        } else {
                            $current_cell_padding = array(
                                'L' => 0,
                                'T' => 0,
                                'R' => 0,
                                'B' => 0
                            );
                        }
                        $this->cell_padding = $current_cell_padding;
                        if (isset($dom[$key]['height'])) {
                            $cellh = $this->getHTMLUnitToUnits($dom[$key]['height'], 0, 'px');
                        } else {
                            $cellh = 0;
                        }
                        if (isset($dom[$key]['content'])) {
                            $cell_content = stripslashes($dom[$key]['content']);
                        } else {
                            $cell_content = '&nbsp;';
                        }
                        $tagtype  = $dom[$key]['value'];
                        $parentid = $key;
                        while (($key < $maxel) AND (!(($dom[$key]['tag']) AND (!$dom[$key]['opening']) AND ($dom[$key]['value'] == $tagtype) AND ($dom[$key]['parent'] == $parentid)))) {
                            ++$key;
                        }
                        if (!isset($dom[$trid]['startpage'])) {
                            $dom[$trid]['startpage'] = $this->page;
                        } else {
                            $this->setPage($dom[$trid]['startpage']);
                        }
                        if (!isset($dom[$trid]['startcolumn'])) {
                            $dom[$trid]['startcolumn'] = $this->current_column;
                        } elseif ($this->current_column != $dom[$trid]['startcolumn']) {
                            $tmpx = $this->x;
                            $this->selectColumn($dom[$trid]['startcolumn']);
                            $this->x = $tmpx;
                        }
                        if (!isset($dom[$trid]['starty'])) {
                            $dom[$trid]['starty'] = $this->y;
                        } else {
                            $this->y = $dom[$trid]['starty'];
                        }
                        if (!isset($dom[$trid]['startx'])) {
                            $dom[$trid]['startx'] = $this->x;
                            $this->x += $cellspacingx;
                        } else {
                            $this->x += ($cellspacingx / 2);
                        }
                        if (isset($dom[$parentid]['attribute']['rowspan'])) {
                            $rowspan = intval($dom[$parentid]['attribute']['rowspan']);
                        } else {
                            $rowspan = 1;
                        }
                        if (isset($dom[$table_el]['rowspans'])) {
                            $rsk    = 0;
                            $rskmax = count($dom[$table_el]['rowspans']);
                            while ($rsk < $rskmax) {
                                $trwsp    = $dom[$table_el]['rowspans'][$rsk];
                                $rsstartx = $trwsp['startx'];
                                $rsendx   = $trwsp['endx'];
                                if ($trwsp['startpage'] < $this->page) {
                                    if (($this->rtl) AND ($this->pagedim[$this->page]['orm'] != $this->pagedim[$trwsp['startpage']]['orm'])) {
                                        $dl = ($this->pagedim[$this->page]['orm'] - $this->pagedim[$trwsp['startpage']]['orm']);
                                        $rsstartx -= $dl;
                                        $rsendx -= $dl;
                                    } elseif ((!$this->rtl) AND ($this->pagedim[$this->page]['olm'] != $this->pagedim[$trwsp['startpage']]['olm'])) {
                                        $dl = ($this->pagedim[$this->page]['olm'] - $this->pagedim[$trwsp['startpage']]['olm']);
                                        $rsstartx += $dl;
                                        $rsendx += $dl;
                                    }
                                }
                                if (($trwsp['rowspan'] > 0) AND ($rsstartx > ($this->x - $cellspacing['H'] - $current_cell_padding['L'] - $this->feps)) AND ($rsstartx < ($this->x + $cellspacing['H'] + $current_cell_padding['R'] + $this->feps)) AND (($trwsp['starty'] < ($this->y - $this->feps)) OR ($trwsp['startpage'] < $this->page) OR ($trwsp['startcolumn'] < $this->current_column))) {
                                    $this->x = $rsendx + $cellspacingx;
                                    $colid += $trwsp['colspan'];
                                    if (($trwsp['rowspan'] == 1) AND (isset($dom[$trid]['endy'])) AND (isset($dom[$trid]['endpage'])) AND (isset($dom[$trid]['endcolumn'])) AND ($trwsp['endpage'] == $dom[$trid]['endpage']) AND ($trwsp['endcolumn'] == $dom[$trid]['endcolumn'])) {
                                        $dom[$table_el]['rowspans'][$rsk]['endy'] = max($dom[$trid]['endy'], $trwsp['endy']);
                                        $dom[$trid]['endy']                       = $dom[$table_el]['rowspans'][$rsk]['endy'];
                                    }
                                    $rsk = 0;
                                } else {
                                    ++$rsk;
                                }
                            }
                        }
                        if (isset($dom[$parentid]['width'])) {
                            $cellw = $this->getHTMLUnitToUnits($dom[$parentid]['width'], $table_columns_width, 'px');
                            $tmpcw = ($cellw / $colspan);
                            for ($i = 0; $i < $colspan; ++$i) {
                                $table_colwidths[($colid + $i)] = $tmpcw;
                            }
                        } else {
                            $cellw = 0;
                            for ($i = 0; $i < $colspan; ++$i) {
                                $cellw += $table_colwidths[($colid + $i)];
                            }
                        }
                        $cellw += (($colspan - 1) * $cellspacing['H']);
                        $colid += $colspan;
                        if ($rowspan > 1) {
                            $trsid = array_push($dom[$table_el]['rowspans'], array(
                                'trid' => $trid,
                                'rowspan' => $rowspan,
                                'mrowspan' => $rowspan,
                                'colspan' => $colspan,
                                'startpage' => $this->page,
                                'startcolumn' => $this->current_column,
                                'startx' => $this->x,
                                'starty' => $this->y
                            ));
                        }
                        $cellid = array_push($dom[$trid]['cellpos'], array(
                            'startx' => $this->x
                        ));
                        if ($rowspan > 1) {
                            $dom[$trid]['cellpos'][($cellid - 1)]['rowspanid'] = ($trsid - 1);
                        }
                        if (isset($dom[$parentid]['bgcolor']) AND ($dom[$parentid]['bgcolor'] !== false)) {
                            $dom[$trid]['cellpos'][($cellid - 1)]['bgcolor'] = $dom[$parentid]['bgcolor'];
                        }
                        if (isset($tdborder) AND !empty($tdborder)) {
                            $dom[$trid]['cellpos'][($cellid - 1)]['border'] = $tdborder;
                        }
                        $prevLastH = $this->lasth;
                        if ($this->rtl) {
                            $this->colxshift['x'] = $this->w - $this->x - $this->rMargin;
                        } else {
                            $this->colxshift['x'] = $this->x - $this->lMargin;
                        }
                        $this->colxshift['s'] = $cellspacing;
                        $this->colxshift['p'] = $current_cell_padding;
                        $this->MultiCell($cellw, $cellh, $cell_content, false, $lalign, false, 2, '', '', true, 0, true, true, 0, 'T', false);
                        $this->colxshift                              = array(
                            'x' => 0,
                            's' => array(
                                'H' => 0,
                                'V' => 0
                            ),
                            'p' => array(
                                'L' => 0,
                                'T' => 0,
                                'R' => 0,
                                'B' => 0
                            )
                        );
                        $this->lasth                                  = $prevLastH;
                        $this->cell_padding                           = $old_cell_padding;
                        $dom[$trid]['cellpos'][($cellid - 1)]['endx'] = $this->x;
                        if ($rowspan <= 1) {
                            if (isset($dom[$trid]['endy'])) {
                                if (($this->page == $dom[$trid]['endpage']) AND ($this->current_column == $dom[$trid]['endcolumn'])) {
                                    $dom[$trid]['endy'] = max($this->y, $dom[$trid]['endy']);
                                } elseif (($this->page > $dom[$trid]['endpage']) OR ($this->current_column > $dom[$trid]['endcolumn'])) {
                                    $dom[$trid]['endy'] = $this->y;
                                }
                            } else {
                                $dom[$trid]['endy'] = $this->y;
                            }
                            if (isset($dom[$trid]['endpage'])) {
                                $dom[$trid]['endpage'] = max($this->page, $dom[$trid]['endpage']);
                            } else {
                                $dom[$trid]['endpage'] = $this->page;
                            }
                            if (isset($dom[$trid]['endcolumn'])) {
                                $dom[$trid]['endcolumn'] = max($this->current_column, $dom[$trid]['endcolumn']);
                            } else {
                                $dom[$trid]['endcolumn'] = $this->current_column;
                            }
                        } else {
                            $dom[$table_el]['rowspans'][($trsid - 1)]['endx']      = $this->x;
                            $dom[$table_el]['rowspans'][($trsid - 1)]['endy']      = $this->y;
                            $dom[$table_el]['rowspans'][($trsid - 1)]['endpage']   = $this->page;
                            $dom[$table_el]['rowspans'][($trsid - 1)]['endcolumn'] = $this->current_column;
                        }
                        if (isset($dom[$table_el]['rowspans'])) {
                            foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
                                if ($trwsp['rowspan'] > 0) {
                                    if (isset($dom[$trid]['endpage'])) {
                                        if (($trwsp['endpage'] == $dom[$trid]['endpage']) AND ($trwsp['endcolumn'] == $dom[$trid]['endcolumn'])) {
                                            $dom[$table_el]['rowspans'][$k]['endy'] = max($dom[$trid]['endy'], $trwsp['endy']);
                                        } elseif (($trwsp['endpage'] < $dom[$trid]['endpage']) OR ($trwsp['endcolumn'] < $dom[$trid]['endcolumn'])) {
                                            $dom[$table_el]['rowspans'][$k]['endy']      = $dom[$trid]['endy'];
                                            $dom[$table_el]['rowspans'][$k]['endpage']   = $dom[$trid]['endpage'];
                                            $dom[$table_el]['rowspans'][$k]['endcolumn'] = $dom[$trid]['endcolumn'];
                                        } else {
                                            $dom[$trid]['endy'] = $this->pagedim[$dom[$trid]['endpage']]['hk'] - $this->pagedim[$dom[$trid]['endpage']]['bm'];
                                        }
                                    }
                                }
                            }
                        }
                        $this->x += ($cellspacingx / 2);
                    } else {
                        if (!isset($opentagpos)) {
                            if ($this->inxobj) {
                                $opentagpos = strlen($this->xobjects[$this->xobjid]['outdata']);
                            } elseif (!$this->InFooter) {
                                if (isset($this->footerlen[$this->page])) {
                                    $this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
                                } else {
                                    $this->footerpos[$this->page] = $this->pagelen[$this->page];
                                }
                                $opentagpos = $this->footerpos[$this->page];
                            }
                        }
                        $dom = $this->openHTMLTagHandler($dom, $key, $cell);
                    }
                } else {
                    $prev_numpages = $this->numpages;
                    $old_bordermrk = $this->bordermrk[$this->page];
                    $dom           = $this->closeHTMLTagHandler($dom, $key, $cell, $maxbottomliney);
                    if ($this->bordermrk[$this->page] > $old_bordermrk) {
                        $startlinepos += ($this->bordermrk[$this->page] - $old_bordermrk);
                    }
                    if ($prev_numpages > $this->numpages) {
                        $startlinepage = $this->page;
                    }
                }
            } elseif (strlen($dom[$key]['value']) > 0) {
                if (!$this->empty_string($this->lispacer) AND ($this->lispacer != '^')) {
                    $this->SetFont($pfontname, $pfontstyle, $pfontsize);
                    $this->resetLastH();
                    $minstartliney  = $this->y;
                    $maxbottomliney = ($startliney + ($this->FontSize * $this->cell_height_ratio));
                    $this->putHtmlListBullet($this->listnum, $this->lispacer, $pfontsize);
                    $this->SetFont($curfontname, $curfontstyle, $curfontsize);
                    $this->resetLastH();
                    if (is_numeric($pfontsize) AND ($pfontsize > 0) AND is_numeric($curfontsize) AND ($curfontsize > 0) AND ($pfontsize != $curfontsize)) {
                        $pfontascent  = $this->getFontAscent($pfontname, $pfontstyle, $pfontsize);
                        $pfontdescent = $this->getFontDescent($pfontname, $pfontstyle, $pfontsize);
                        $this->y += ((($pfontsize - $curfontsize) * $this->cell_height_ratio / $this->k) + $pfontascent - $curfontascent - $pfontdescent + $curfontdescent) / 2;
                        $minstartliney  = min($this->y, $minstartliney);
                        $maxbottomliney = max(($this->y + (($pfontsize * $this->cell_height_ratio) / $this->k)), $maxbottomliney);
                    }
                }
                $this->htmlvspace = 0;
                if ((!$this->premode) AND $this->isRTLTextDir()) {
                    $lsp = '';
                    $rsp = '';
                    if (preg_match('/^(' . $this->re_space['p'] . '+)/' . $this->re_space['m'], $dom[$key]['value'], $matches)) {
                        $lsp = $matches[1];
                    }
                    if (preg_match('/(' . $this->re_space['p'] . '+)$/' . $this->re_space['m'], $dom[$key]['value'], $matches)) {
                        $rsp = $matches[1];
                    }
                    $dom[$key]['value'] = $rsp . $this->stringTrim($dom[$key]['value']) . $lsp;
                }
                if ($newline) {
                    if (!$this->premode) {
                        $prelen = strlen($dom[$key]['value']);
                        if ($this->isRTLTextDir()) {
                            $dom[$key]['value'] = $this->stringRightTrim($dom[$key]['value']);
                        } else {
                            $dom[$key]['value'] = $this->stringLeftTrim($dom[$key]['value']);
                        }
                        $postlen = strlen($dom[$key]['value']);
                        if (($postlen == 0) AND ($prelen > 0)) {
                            $dom[$key]['trimmed_space'] = true;
                        }
                    }
                    $newline    = false;
                    $firstblock = true;
                } else {
                    $firstblock         = false;
                    $dom[$key]['value'] = preg_replace('/^' . $this->re_space['p'] . '+$/' . $this->re_space['m'], chr(32), $dom[$key]['value']);
                }
                $strrest = '';
                if ($this->rtl) {
                    $this->x -= $this->textindent;
                } else {
                    $this->x += $this->textindent;
                }
                if (!isset($dom[$key]['trimmed_space']) OR !$dom[$key]['trimmed_space']) {
                    $strlinelen = $this->GetStringWidth($dom[$key]['value']);
                    if (!empty($this->HREF) AND (isset($this->HREF['url']))) {
                        $hrefcolor = '';
                        if (isset($dom[($dom[$key]['parent'])]['fgcolor']) AND ($dom[($dom[$key]['parent'])]['fgcolor'] !== false)) {
                            $hrefcolor = $dom[($dom[$key]['parent'])]['fgcolor'];
                        }
                        $hrefstyle = -1;
                        if (isset($dom[($dom[$key]['parent'])]['fontstyle']) AND ($dom[($dom[$key]['parent'])]['fontstyle'] !== false)) {
                            $hrefstyle = $dom[($dom[$key]['parent'])]['fontstyle'];
                        }
                        $strrest = $this->addHtmlLink($this->HREF['url'], $dom[$key]['value'], $wfill, true, $hrefcolor, $hrefstyle, true);
                    } else {
                        $wadj = 0;
                        if ($this->rtl) {
                            $cwa = $this->x - $this->lMargin;
                        } else {
                            $cwa = $this->w - $this->rMargin - $this->x;
                        }
                        if (($strlinelen < $cwa) AND (isset($dom[($key + 1)])) AND ($dom[($key + 1)]['tag']) AND (!$dom[($key + 1)]['block'])) {
                            $nkey          = ($key + 1);
                            $write_block   = true;
                            $same_textdir  = true;
                            $tmp_fontname  = $this->FontFamily;
                            $tmp_fontstyle = $this->FontStyle;
                            $tmp_fontsize  = $this->FontSizePt;
                            while ($write_block AND isset($dom[$nkey])) {
                                if ($dom[$nkey]['tag']) {
                                    if ($dom[$nkey]['block']) {
                                        $write_block = false;
                                    }
                                    $tmp_fontname  = isset($dom[$nkey]['fontname']) ? $dom[$nkey]['fontname'] : $this->FontFamily;
                                    $tmp_fontstyle = isset($dom[$nkey]['fontstyle']) ? $dom[$nkey]['fontstyle'] : $this->FontStyle;
                                    $tmp_fontsize  = isset($dom[$nkey]['fontsize']) ? $dom[$nkey]['fontsize'] : $this->FontSizePt;
                                    $same_textdir  = ($dom[$nkey]['dir'] == $dom[$key]['dir']);
                                } else {
                                    $nextstr = preg_split('/' . $this->re_space['p'] . '+/' . $this->re_space['m'], $dom[$nkey]['value']);
                                    if (isset($nextstr[0]) AND $same_textdir) {
                                        $wadj += $this->GetStringWidth($nextstr[0], $tmp_fontname, $tmp_fontstyle, $tmp_fontsize);
                                    }
                                    if (isset($nextstr[1])) {
                                        $write_block = false;
                                    }
                                }
                                ++$nkey;
                            }
                        }
                        if (($wadj > 0) AND (($strlinelen + $wadj) >= $cwa)) {
                            $wadj    = 0;
                            $nextstr = preg_split('/' . $this->re_space['p'] . '/' . $this->re_space['m'], $dom[$key]['value']);
                            $numblks = count($nextstr);
                            if ($numblks > 1) {
                                $wadj = ($cwa - $strlinelen + $this->GetStringWidth($nextstr[($numblks - 1)]));
                            }
                        }
                        if (($wadj > 0) AND (($this->rtl AND ($this->tmprtl === 'L')) OR (!$this->rtl AND ($this->tmprtl === 'R')))) {
                            $reverse_dir = true;
                            $this->rtl   = !$this->rtl;
                            $revshift    = ($strlinelen + $wadj + 0.000001);
                            if ($this->rtl) {
                                $this->x += $revshift;
                            } else {
                                $this->x -= $revshift;
                            }
                            $xws = $this->x;
                        }
                        $strrest = $this->Write($this->lasth, $dom[$key]['value'], '', $wfill, '', false, 0, true, $firstblock, 0, $wadj);
                        if ($reverse_dir AND ($wadj == 0)) {
                            $this->x     = $xws;
                            $this->rtl   = !$this->rtl;
                            $reverse_dir = false;
                        }
                    }
                }
                $this->textindent = 0;
                if (strlen($strrest) > 0) {
                    $this->newline = true;
                    if ($strrest == $dom[$key]['value']) {
                        ++$loop;
                    } else {
                        $loop = 0;
                    }
                    $dom[$key]['value'] = $strrest;
                    if ($cell) {
                        if ($this->rtl) {
                            $this->x -= $this->cell_padding['R'];
                        } else {
                            $this->x += $this->cell_padding['L'];
                        }
                    }
                    if ($loop < 3) {
                        --$key;
                    }
                } else {
                    $loop = 0;
                }
            }
            ++$key;
            if (isset($dom[$key]['tag']) AND $dom[$key]['tag'] AND (!isset($dom[$key]['opening']) OR !$dom[$key]['opening']) AND isset($dom[($dom[$key]['parent'])]['attribute']['nobr']) AND ($dom[($dom[$key]['parent'])]['attribute']['nobr'] == 'true')) {
                if ((!$undo) AND (($this->y < $this->start_transaction_y) OR (($dom[$key]['value'] == 'tr') AND ($dom[($dom[$key]['parent'])]['endy'] < $this->start_transaction_y)))) {
                    $this->rollbackTransaction(true);
                    foreach ($this_method_vars as $vkey => $vval) {
                        $$vkey = $vval;
                    }
                    $pre_y = $this->y;
                    if ((!$this->checkPageBreak($this->PageBreakTrigger + 1)) AND ($this->y < $pre_y)) {
                        $startliney = $this->y;
                    }
                    $undo = true;
                } else {
                    $undo = false;
                }
            }
        }
        if (isset($startlinex)) {
            $yshift = $minstartliney - $startliney;
            if (($yshift > 0) OR ($this->page > $startlinepage)) {
                $yshift = 0;
            }
            $t_x   = 0;
            $linew = abs($this->endlinex - $startlinex);
            if ($this->inxobj) {
                $pstart = substr($this->xobjects[$this->xobjid]['outdata'], 0, $startlinepos);
                if (isset($opentagpos)) {
                    $midpos = $opentagpos;
                } else {
                    $midpos = 0;
                }
                if ($midpos > 0) {
                    $pmid = substr($this->xobjects[$this->xobjid]['outdata'], $startlinepos, ($midpos - $startlinepos));
                    $pend = substr($this->xobjects[$this->xobjid]['outdata'], $midpos);
                } else {
                    $pmid = substr($this->xobjects[$this->xobjid]['outdata'], $startlinepos);
                    $pend = '';
                }
            } else {
                $pstart = substr($this->getPageBuffer($startlinepage), 0, $startlinepos);
                if (isset($opentagpos) AND isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
                    $this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
                    $midpos                          = min($opentagpos, $this->footerpos[$startlinepage]);
                } elseif (isset($opentagpos)) {
                    $midpos = $opentagpos;
                } elseif (isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
                    $this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
                    $midpos                          = $this->footerpos[$startlinepage];
                } else {
                    $midpos = 0;
                }
                if ($midpos > 0) {
                    $pmid = substr($this->getPageBuffer($startlinepage), $startlinepos, ($midpos - $startlinepos));
                    $pend = substr($this->getPageBuffer($startlinepage), $midpos);
                } else {
                    $pmid = substr($this->getPageBuffer($startlinepage), $startlinepos);
                    $pend = '';
                }
            }
            if ((isset($plalign) AND ((($plalign == 'C') OR (($plalign == 'R') AND (!$this->rtl)) OR (($plalign == 'L') AND ($this->rtl)))))) {
                $tw = $w;
                if ($this->lMargin != $prevlMargin) {
                    $tw += ($prevlMargin - $this->lMargin);
                }
                if ($this->rMargin != $prevrMargin) {
                    $tw += ($prevrMargin - $this->rMargin);
                }
                $one_space_width = $this->GetStringWidth(chr(32));
                $no              = 0;
                if ($this->isRTLTextDir()) {
                    $pos1 = $this->revstrpos($pmid, '[(');
                    if ($pos1 > 0) {
                        $pos1 = intval($pos1);
                        if ($this->isUnicodeFont()) {
                            $pos2     = intval($this->revstrpos($pmid, '[(' . chr(0) . chr(32)));
                            $spacelen = 2;
                        } else {
                            $pos2     = intval($this->revstrpos($pmid, '[(' . chr(32)));
                            $spacelen = 1;
                        }
                        if ($pos1 == $pos2) {
                            $pmid = substr($pmid, 0, ($pos1 + 2)) . substr($pmid, ($pos1 + 2 + $spacelen));
                            if (substr($pmid, $pos1, 4) == '[()]') {
                                $linew -= $one_space_width;
                            } elseif ($pos1 == strpos($pmid, '[(')) {
                                $no = 1;
                            }
                        }
                    }
                } else {
                    $pos1 = $this->revstrpos($pmid, ')]');
                    if ($pos1 > 0) {
                        $pos1 = intval($pos1);
                        if ($this->isUnicodeFont()) {
                            $pos2     = intval($this->revstrpos($pmid, chr(0) . chr(32) . ')]')) + 2;
                            $spacelen = 2;
                        } else {
                            $pos2     = intval($this->revstrpos($pmid, chr(32) . ')]')) + 1;
                            $spacelen = 1;
                        }
                        if ($pos1 == $pos2) {
                            $pmid = substr($pmid, 0, ($pos1 - $spacelen)) . substr($pmid, $pos1);
                            $linew -= $one_space_width;
                        }
                    }
                }
                $mdiff = ($tw - $linew);
                if ($plalign == 'C') {
                    if ($this->rtl) {
                        $t_x = -($mdiff / 2);
                    } else {
                        $t_x = ($mdiff / 2);
                    }
                } elseif ($plalign == 'R') {
                    $t_x = $mdiff;
                } elseif ($plalign == 'L') {
                    $t_x = -$mdiff;
                }
            }
            if (($t_x != 0) OR ($yshift < 0)) {
                $trx = sprintf('1 0 0 1 %.3F %.3F cm', ($t_x * $this->k), ($yshift * $this->k));
                $pstart .= "\nq\n" . $trx . "\n" . $pmid . "\nQ\n";
                $endlinepos = strlen($pstart);
                if ($this->inxobj) {
                    $this->xobjects[$this->xobjid]['outdata'] = $pstart . $pend;
                    foreach ($this->xobjects[$this->xobjid]['annotations'] as $pak => $pac) {
                        if ($pak >= $pask) {
                            $this->xobjects[$this->xobjid]['annotations'][$pak]['x'] += $t_x;
                            $this->xobjects[$this->xobjid]['annotations'][$pak]['y'] -= $yshift;
                        }
                    }
                } else {
                    $this->setPageBuffer($startlinepage, $pstart . $pend);
                    if (isset($this->PageAnnots[$this->page])) {
                        foreach ($this->PageAnnots[$this->page] as $pak => $pac) {
                            if ($pak >= $pask) {
                                $this->PageAnnots[$this->page][$pak]['x'] += $t_x;
                                $this->PageAnnots[$this->page][$pak]['y'] -= $yshift;
                            }
                        }
                    }
                }
                $this->y -= $yshift;
            }
        }
        $this->setGraphicVars($gvars);
        if ($this->num_columns > 1) {
            $this->selectColumn();
        } elseif ($this->page > $prevPage) {
            $this->lMargin = $this->pagedim[$this->page]['olm'];
            $this->rMargin = $this->pagedim[$this->page]['orm'];
        }
        $this->cell_height_ratio = $prev_cell_height_ratio;
        $this->listnum           = $prev_listnum;
        $this->listordered       = $prev_listordered;
        $this->listcount         = $prev_listcount;
        $this->lispacer          = $prev_lispacer;
        if ($ln AND (!($cell AND ($dom[$key - 1]['value'] == 'table')))) {
            $this->Ln($this->lasth);
            if ($this->y < $maxbottomliney) {
                $this->y = $maxbottomliney;
            }
        }
        unset($dom);
    }
    protected function openHTMLTagHandler($dom, $key, $cell)
    {
        $tag      = $dom[$key];
        $parent   = $dom[($dom[$key]['parent'])];
        $firsttag = ($key == 1);
        if (isset($tag['dir'])) {
            $this->setTempRTL($tag['dir']);
        } else {
            $this->tmprtl = false;
        }
        if ($tag['block']) {
            $hbz = 0;
            $hb  = 0;
            if (isset($this->tagvspaces[$tag['value']][0]['h']) AND ($this->tagvspaces[$tag['value']][0]['h'] >= 0)) {
                $cur_h = $this->tagvspaces[$tag['value']][0]['h'];
            } elseif (isset($tag['fontsize'])) {
                $cur_h = ($tag['fontsize'] / $this->k) * $this->cell_height_ratio;
            } else {
                $cur_h = $this->FontSize * $this->cell_height_ratio;
            }
            if (isset($this->tagvspaces[$tag['value']][0]['n'])) {
                $n = $this->tagvspaces[$tag['value']][0]['n'];
            } elseif (preg_match('/[h][0-9]/', $tag['value']) > 0) {
                $n = 0.6;
            } else {
                $n = 1;
            }
            if ((!isset($this->tagvspaces[$tag['value']])) AND (in_array($tag['value'], array(
                'div',
                'dt',
                'dd',
                'li',
                'br'
            )))) {
                $hb = 0;
            } else {
                $hb = ($n * $cur_h);
            }
            if (($this->htmlvspace <= 0) AND ($n > 0)) {
                if (isset($parent['fontsize'])) {
                    $hbz = (($parent['fontsize'] / $this->k) * $this->cell_height_ratio);
                } else {
                    $hbz = $this->FontSize * $this->cell_height_ratio;
                }
            }
        }
        switch ($tag['value']) {
            case 'table': {
                $cp                    = 0;
                $cs                    = 0;
                $dom[$key]['rowspans'] = array();
                if (!isset($dom[$key]['attribute']['nested']) OR ($dom[$key]['attribute']['nested'] != 'true')) {
                    if (!$this->empty_string($dom[$key]['thead'])) {
                        $this->thead = $dom[$key]['thead'];
                        if (!isset($this->theadMargins) OR (empty($this->theadMargins))) {
                            $this->theadMargins                 = array();
                            $this->theadMargins['cell_padding'] = $this->cell_padding;
                            $this->theadMargins['lmargin']      = $this->lMargin;
                            $this->theadMargins['rmargin']      = $this->rMargin;
                            $this->theadMargins['page']         = $this->page;
                            $this->theadMargins['cell']         = $cell;
                        }
                    }
                }
                $dom[$key]['old_cell_padding'] = $this->cell_padding;
                if (isset($tag['attribute']['cellpadding'])) {
                    $pad = $this->getHTMLUnitToUnits($tag['attribute']['cellpadding'], 1, 'px');
                    $this->SetCellPadding($pad);
                } elseif (isset($tag['padding'])) {
                    $this->cell_padding = $tag['padding'];
                }
                if (isset($tag['attribute']['cellspacing'])) {
                    $cs = $this->getHTMLUnitToUnits($tag['attribute']['cellspacing'], 1, 'px');
                } elseif (isset($tag['border-spacing'])) {
                    $cs = $tag['border-spacing']['V'];
                }
                $prev_y = $this->y;
                if ($this->checkPageBreak(((2 * $cp) + (2 * $cs) + $this->lasth), '', false) OR ($this->y < $prev_y)) {
                    $this->inthead = true;
                    $this->checkPageBreak($this->PageBreakTrigger + 1);
                }
                break;
            }
            case 'tr': {
                $dom[$key]['cellpos'] = array();
                break;
            }
            case 'hr': {
                if ((isset($tag['height'])) AND ($tag['height'] != '')) {
                    $hrHeight = $this->getHTMLUnitToUnits($tag['height'], 1, 'px');
                } else {
                    $hrHeight = $this->GetLineWidth();
                }
                $this->addHTMLVertSpace($hbz, ($hrHeight / 2), $cell, $firsttag);
                $x    = $this->GetX();
                $y    = $this->GetY();
                $wtmp = $this->w - $this->lMargin - $this->rMargin;
                if ($cell) {
                    $wtmp -= ($this->cell_padding['L'] + $this->cell_padding['R']);
                }
                if ((isset($tag['width'])) AND ($tag['width'] != '')) {
                    $hrWidth = $this->getHTMLUnitToUnits($tag['width'], $wtmp, 'px');
                } else {
                    $hrWidth = $wtmp;
                }
                $prevlinewidth = $this->GetLineWidth();
                $this->SetLineWidth($hrHeight);
                $this->Line($x, $y, $x + $hrWidth, $y);
                $this->SetLineWidth($prevlinewidth);
                $this->addHTMLVertSpace(($hrHeight / 2), 0, $cell, !isset($dom[($key + 1)]));
                break;
            }
            case 'a': {
                if (array_key_exists('href', $tag['attribute'])) {
                    $this->HREF['url'] = $tag['attribute']['href'];
                }
                break;
            }
            case 'img': {
                if (isset($tag['attribute']['src'])) {
                    if (($tag['attribute']['src'][0] == '/') AND !empty($_SERVER['DOCUMENT_ROOT']) AND ($_SERVER['DOCUMENT_ROOT'] != '/')) {
                        $findroot = strpos($tag['attribute']['src'], $_SERVER['DOCUMENT_ROOT']);
                        if (($findroot === false) OR ($findroot > 1)) {
                            if (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/') {
                                $tag['attribute']['src'] = substr($_SERVER['DOCUMENT_ROOT'], 0, -1) . $tag['attribute']['src'];
                            } else {
                                $tag['attribute']['src'] = $_SERVER['DOCUMENT_ROOT'] . $tag['attribute']['src'];
                            }
                        }
                    }
                    $tag['attribute']['src'] = urldecode($tag['attribute']['src']);
                    $type                    = $this->getImageFileType($tag['attribute']['src']);
                    $testscrtype             = @parse_url($tag['attribute']['src']);
                    if (!isset($testscrtype['query']) OR empty($testscrtype['query'])) {
                        $tag['attribute']['src'] = str_replace(K_PATH_URL, K_PATH_MAIN, $tag['attribute']['src']);
                    }
                    if (!isset($tag['width'])) {
                        $tag['width'] = 0;
                    }
                    if (!isset($tag['height'])) {
                        $tag['height'] = 0;
                    }
                    $tag['attribute']['align'] = 'bottom';
                    switch ($tag['attribute']['align']) {
                        case 'top': {
                            $align = 'T';
                            break;
                        }
                        case 'middle': {
                            $align = 'M';
                            break;
                        }
                        case 'bottom': {
                            $align = 'B';
                            break;
                        }
                        default: {
                            $align = 'B';
                            break;
                        }
                    }
                    $prevy   = $this->y;
                    $xpos    = $this->x;
                    $imglink = '';
                    if (isset($this->HREF['url']) AND !$this->empty_string($this->HREF['url'])) {
                        $imglink = $this->HREF['url'];
                        if ($imglink{0} == '#') {
                            $lnkdata = explode(',', $imglink);
                            if (isset($lnkdata[0])) {
                                $page = intval(substr($lnkdata[0], 1));
                                if (empty($page) OR ($page <= 0)) {
                                    $page = $this->page;
                                }
                                if (isset($lnkdata[1]) AND (strlen($lnkdata[1]) > 0)) {
                                    $lnky = floatval($lnkdata[1]);
                                } else {
                                    $lnky = 0;
                                }
                                $imglink = $this->AddLink();
                                $this->SetLink($imglink, $lnky, $page);
                            }
                        }
                    }
                    $border = 0;
                    if (isset($tag['border']) AND !empty($tag['border'])) {
                        $border = $tag['border'];
                    }
                    $iw = '';
                    if (isset($tag['width'])) {
                        $iw = $this->getHTMLUnitToUnits($tag['width'], 1, 'px', false);
                    }
                    $ih = '';
                    if (isset($tag['height'])) {
                        $ih = $this->getHTMLUnitToUnits($tag['height'], 1, 'px', false);
                    }
                    if (($type == 'eps') OR ($type == 'ai')) {
                        $this->ImageEps($tag['attribute']['src'], $xpos, $this->y, $iw, $ih, $imglink, true, $align, '', $border, true);
                    } elseif ($type == 'svg') {
                        $this->ImageSVG($tag['attribute']['src'], $xpos, $this->y, $iw, $ih, $imglink, $align, '', $border, true);
                    } else {
                        $this->Image($tag['attribute']['src'], $xpos, $this->y, $iw, $ih, '', $imglink, $align, false, 300, '', false, false, $border, false, false, true);
                    }
                    switch ($align) {
                        case 'T': {
                            $this->y = $prevy;
                            break;
                        }
                        case 'M': {
                            $this->y = (($this->img_rb_y + $prevy - ($tag['fontsize'] / $this->k)) / 2);
                            break;
                        }
                        case 'B': {
                            $this->y = $this->img_rb_y - ($tag['fontsize'] / $this->k);
                            break;
                        }
                    }
                }
                break;
            }
            case 'dl': {
                ++$this->listnum;
                if ($this->listnum == 1) {
                    $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                } else {
                    $this->addHTMLVertSpace(0, 0, $cell, $firsttag);
                }
                break;
            }
            case 'dt': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'dd': {
                if ($this->rtl) {
                    $this->rMargin += $this->listindent;
                } else {
                    $this->lMargin += $this->listindent;
                }
                ++$this->listindentlevel;
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'ul':
            case 'ol': {
                ++$this->listnum;
                if ($tag['value'] == 'ol') {
                    $this->listordered[$this->listnum] = true;
                } else {
                    $this->listordered[$this->listnum] = false;
                }
                if (isset($tag['attribute']['start'])) {
                    $this->listcount[$this->listnum] = intval($tag['attribute']['start']) - 1;
                } else {
                    $this->listcount[$this->listnum] = 0;
                }
                if ($this->rtl) {
                    $this->rMargin += $this->listindent;
                    $this->x -= $this->listindent;
                } else {
                    $this->lMargin += $this->listindent;
                    $this->x += $this->listindent;
                }
                ++$this->listindentlevel;
                if ($this->listnum == 1) {
                    if ($key > 1) {
                        $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                    }
                } else {
                    $this->addHTMLVertSpace(0, 0, $cell, $firsttag);
                }
                break;
            }
            case 'li': {
                if ($key > 2) {
                    $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                }
                if ($this->listordered[$this->listnum]) {
                    if (isset($parent['attribute']['type']) AND !$this->empty_string($parent['attribute']['type'])) {
                        $this->lispacer = $parent['attribute']['type'];
                    } elseif (isset($parent['listtype']) AND !$this->empty_string($parent['listtype'])) {
                        $this->lispacer = $parent['listtype'];
                    } elseif (isset($this->lisymbol) AND !$this->empty_string($this->lisymbol)) {
                        $this->lispacer = $this->lisymbol;
                    } else {
                        $this->lispacer = '#';
                    }
                    ++$this->listcount[$this->listnum];
                    if (isset($tag['attribute']['value'])) {
                        $this->listcount[$this->listnum] = intval($tag['attribute']['value']);
                    }
                } else {
                    if (isset($parent['attribute']['type']) AND !$this->empty_string($parent['attribute']['type'])) {
                        $this->lispacer = $parent['attribute']['type'];
                    } elseif (isset($parent['listtype']) AND !$this->empty_string($parent['listtype'])) {
                        $this->lispacer = $parent['listtype'];
                    } elseif (isset($this->lisymbol) AND !$this->empty_string($this->lisymbol)) {
                        $this->lispacer = $this->lisymbol;
                    } else {
                        $this->lispacer = '!';
                    }
                }
                break;
            }
            case 'blockquote': {
                if ($this->rtl) {
                    $this->rMargin += $this->listindent;
                } else {
                    $this->lMargin += $this->listindent;
                }
                ++$this->listindentlevel;
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'br': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'div': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'p': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'pre': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                $this->premode = true;
                break;
            }
            case 'sup': {
                $this->SetXY($this->GetX(), $this->GetY() - ((0.7 * $this->FontSizePt) / $this->k));
                break;
            }
            case 'sub': {
                $this->SetXY($this->GetX(), $this->GetY() + ((0.3 * $this->FontSizePt) / $this->k));
                break;
            }
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, $firsttag);
                break;
            }
            case 'form': {
                if (isset($tag['attribute']['action'])) {
                    $this->form_action = $tag['attribute']['action'];
                } else {
                    $this->form_action = K_PATH_URL . $_SERVER['SCRIPT_NAME'];
                }
                if (isset($tag['attribute']['enctype'])) {
                    $this->form_enctype = $tag['attribute']['enctype'];
                } else {
                    $this->form_enctype = 'application/x-www-form-urlencoded';
                }
                if (isset($tag['attribute']['method'])) {
                    $this->form_mode = $tag['attribute']['method'];
                } else {
                    $this->form_mode = 'post';
                }
                break;
            }
            case 'input': {
                if (isset($tag['attribute']['name']) AND !$this->empty_string($tag['attribute']['name'])) {
                    $name = $tag['attribute']['name'];
                } else {
                    break;
                }
                $prop = array();
                $opt  = array();
                if (isset($tag['attribute']['readonly']) AND !$this->empty_string($tag['attribute']['readonly'])) {
                    $prop['readonly'] = true;
                }
                if (isset($tag['attribute']['value']) AND !$this->empty_string($tag['attribute']['value'])) {
                    $value = $tag['attribute']['value'];
                }
                if (isset($tag['attribute']['maxlength']) AND !$this->empty_string($tag['attribute']['maxlength'])) {
                    $opt['maxlen'] = intval($tag['attribute']['value']);
                }
                $h = $this->FontSize * $this->cell_height_ratio;
                if (isset($tag['attribute']['size']) AND !$this->empty_string($tag['attribute']['size'])) {
                    $w = intval($tag['attribute']['size']) * $this->GetStringWidth(chr(32)) * 2;
                } else {
                    $w = $h;
                }
                if (isset($tag['attribute']['checked']) AND (($tag['attribute']['checked'] == 'checked') OR ($tag['attribute']['checked'] == 'true'))) {
                    $checked = true;
                } else {
                    $checked = false;
                }
                switch ($tag['attribute']['type']) {
                    case 'text': {
                        if (isset($value)) {
                            $opt['v'] = $value;
                        }
                        $this->TextField($name, $w, $h, $prop, $opt, '', '', false);
                        break;
                    }
                    case 'password': {
                        if (isset($value)) {
                            $opt['v'] = $value;
                        }
                        $prop['password'] = 'true';
                        $this->TextField($name, $w, $h, $prop, $opt, '', '', false);
                        break;
                    }
                    case 'checkbox': {
                        $this->CheckBox($name, $w, $checked, $prop, $opt, $value, '', '', false);
                        break;
                    }
                    case 'radio': {
                        $this->RadioButton($name, $w, $prop, $opt, $value, $checked, '', '', false);
                        break;
                    }
                    case 'submit': {
                        $w = $this->GetStringWidth($value) * 1.5;
                        $h *= 1.6;
                        $prop        = array(
                            'lineWidth' => 1,
                            'borderStyle' => 'beveled',
                            'fillColor' => array(
                                196,
                                196,
                                196
                            ),
                            'strokeColor' => array(
                                255,
                                255,
                                255
                            )
                        );
                        $action      = array();
                        $action['S'] = 'SubmitForm';
                        $action['F'] = $this->form_action;
                        if ($this->form_enctype != 'FDF') {
                            $action['Flags'] = array(
                                'ExportFormat'
                            );
                        }
                        if ($this->form_mode == 'get') {
                            $action['Flags'] = array(
                                'GetMethod'
                            );
                        }
                        $this->Button($name, $w, $h, $value, $action, $prop, $opt, '', '', false);
                        break;
                    }
                    case 'reset': {
                        $w = $this->GetStringWidth($value) * 1.5;
                        $h *= 1.6;
                        $prop = array(
                            'lineWidth' => 1,
                            'borderStyle' => 'beveled',
                            'fillColor' => array(
                                196,
                                196,
                                196
                            ),
                            'strokeColor' => array(
                                255,
                                255,
                                255
                            )
                        );
                        $this->Button($name, $w, $h, $value, array(
                            'S' => 'ResetForm'
                        ), $prop, $opt, '', '', false);
                        break;
                    }
                    case 'file': {
                        $prop['fileSelect'] = 'true';
                        $this->TextField($name, $w, $h, $prop, $opt, '', '', false);
                        if (!isset($value)) {
                            $value = '*';
                        }
                        $w = $this->GetStringWidth($value) * 2;
                        $h *= 1.2;
                        $prop     = array(
                            'lineWidth' => 1,
                            'borderStyle' => 'beveled',
                            'fillColor' => array(
                                196,
                                196,
                                196
                            ),
                            'strokeColor' => array(
                                255,
                                255,
                                255
                            )
                        );
                        $jsaction = 'var f=this.getField(\'' . $name . '\'); f.browseForFileToSubmit();';
                        $this->Button('FB_' . $name, $w, $h, $value, $jsaction, $prop, $opt, '', '', false);
                        break;
                    }
                    case 'hidden': {
                        if (isset($value)) {
                            $opt['v'] = $value;
                        }
                        $opt['f'] = array(
                            'invisible',
                            'hidden'
                        );
                        $this->TextField($name, 0, 0, $prop, $opt, '', '', false);
                        break;
                    }
                    case 'image': {
                        if (isset($tag['attribute']['src']) AND !$this->empty_string($tag['attribute']['src'])) {
                            $img = $tag['attribute']['src'];
                        } else {
                            break;
                        }
                        $value = 'img';
                        if (isset($tag['attribute']['onclick']) AND !empty($tag['attribute']['onclick'])) {
                            $jsaction = $tag['attribute']['onclick'];
                        } else {
                            $jsaction = '';
                        }
                        $this->Button($name, $w, $h, $value, $jsaction, $prop, $opt, '', '', false);
                        break;
                    }
                    case 'button': {
                        $w = $this->GetStringWidth($value) * 1.5;
                        $h *= 1.6;
                        $prop = array(
                            'lineWidth' => 1,
                            'borderStyle' => 'beveled',
                            'fillColor' => array(
                                196,
                                196,
                                196
                            ),
                            'strokeColor' => array(
                                255,
                                255,
                                255
                            )
                        );
                        if (isset($tag['attribute']['onclick']) AND !empty($tag['attribute']['onclick'])) {
                            $jsaction = $tag['attribute']['onclick'];
                        } else {
                            $jsaction = '';
                        }
                        $this->Button($name, $w, $h, $value, $jsaction, $prop, $opt, '', '', false);
                        break;
                    }
                }
                break;
            }
            case 'textarea': {
                $prop = array();
                $opt  = array();
                if (isset($tag['attribute']['readonly']) AND !$this->empty_string($tag['attribute']['readonly'])) {
                    $prop['readonly'] = true;
                }
                if (isset($tag['attribute']['name']) AND !$this->empty_string($tag['attribute']['name'])) {
                    $name = $tag['attribute']['name'];
                } else {
                    break;
                }
                if (isset($tag['attribute']['value']) AND !$this->empty_string($tag['attribute']['value'])) {
                    $opt['v'] = $tag['attribute']['value'];
                }
                if (isset($tag['attribute']['cols']) AND !$this->empty_string($tag['attribute']['cols'])) {
                    $w = intval($tag['attribute']['cols']) * $this->GetStringWidth(chr(32)) * 2;
                } else {
                    $w = 40;
                }
                if (isset($tag['attribute']['rows']) AND !$this->empty_string($tag['attribute']['rows'])) {
                    $h = intval($tag['attribute']['rows']) * $this->FontSize * $this->cell_height_ratio;
                } else {
                    $h = 10;
                }
                $prop['multiline'] = 'true';
                $this->TextField($name, $w, $h, $prop, $opt, '', '', false);
                break;
            }
            case 'select': {
                $h = $this->FontSize * $this->cell_height_ratio;
                if (isset($tag['attribute']['size']) AND !$this->empty_string($tag['attribute']['size'])) {
                    $h *= ($tag['attribute']['size'] + 1);
                }
                $prop = array();
                $opt  = array();
                if (isset($tag['attribute']['name']) AND !$this->empty_string($tag['attribute']['name'])) {
                    $name = $tag['attribute']['name'];
                } else {
                    break;
                }
                $w = 0;
                if (isset($tag['attribute']['opt']) AND !$this->empty_string($tag['attribute']['opt'])) {
                    $options = explode('#!NwL!#', $tag['attribute']['opt']);
                    $values  = array();
                    foreach ($options as $val) {
                        if (strpos($val, '#!TaB!#') !== false) {
                            $opts     = explode('#!TaB!#', $val);
                            $values[] = $opts;
                            $w        = max($w, $this->GetStringWidth($opts[1]));
                        } else {
                            $values[] = $val;
                            $w        = max($w, $this->GetStringWidth($val));
                        }
                    }
                } else {
                    break;
                }
                $w *= 2;
                if (isset($tag['attribute']['multiple']) AND ($tag['attribute']['multiple'] = 'multiple')) {
                    $prop['multipleSelection'] = 'true';
                    $this->ListBox($name, $w, $h, $values, $prop, $opt, '', '', false);
                } else {
                    $this->ComboBox($name, $w, $h, $values, $prop, $opt, '', '', false);
                }
                break;
            }
            case 'tcpdf': {
                if (defined('K_TCPDF_CALLS_IN_HTML') AND (K_TCPDF_CALLS_IN_HTML === true)) {
                    if (isset($tag['attribute']['method'])) {
                        $tcpdf_method = $tag['attribute']['method'];
                        if (method_exists($this, $tcpdf_method)) {
                            if (isset($tag['attribute']['params']) AND (!empty($tag['attribute']['params']))) {
                                $params = unserialize(urldecode($tag['attribute']['params']));
                                call_user_func_array(array(
                                    $this,
                                    $tcpdf_method
                                ), $params);
                            } else {
                                $this->$tcpdf_method();
                            }
                            $this->newline = true;
                        }
                    }
                }
                break;
            }
            default: {
                break;
            }
        }
        $bordertags = array(
            'blockquote',
            'br',
            'dd',
            'dl',
            'div',
            'dt',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hr',
            'li',
            'ol',
            'p',
            'pre',
            'ul',
            'tcpdf',
            'table'
        );
        if (in_array($tag['value'], $bordertags)) {
            $dom[$key]['borderposition'] = $this->getBorderStartPosition();
        }
        if ($dom[$key]['self'] AND isset($dom[$key]['attribute']['pagebreakafter'])) {
            $pba = $dom[$key]['attribute']['pagebreakafter'];
            if (($pba == 'true') OR ($pba == 'left') OR ($pba == 'right')) {
                $this->checkPageBreak($this->PageBreakTrigger + 1);
            }
            if ((($pba == 'left') AND (((!$this->rtl) AND (($this->page % 2) == 0)) OR (($this->rtl) AND (($this->page % 2) != 0)))) OR (($pba == 'right') AND (((!$this->rtl) AND (($this->page % 2) != 0)) OR (($this->rtl) AND (($this->page % 2) == 0))))) {
                $this->checkPageBreak($this->PageBreakTrigger + 1);
            }
        }
        return $dom;
    }
    protected function closeHTMLTagHandler($dom, $key, $cell, $maxbottomliney = 0)
    {
        $tag           = $dom[$key];
        $parent        = $dom[($dom[$key]['parent'])];
        $lasttag       = ((!isset($dom[($key + 1)])) OR ((!isset($dom[($key + 2)])) AND ($dom[($key + 1)]['value'] == 'marker')));
        $in_table_head = false;
        if ($this->rtl) {
            $xmax = $this->w;
        } else {
            $xmax = 0;
        }
        if ($tag['block']) {
            $hbz = 0;
            $hb  = 0;
            if (isset($this->tagvspaces[$tag['value']][1]['h']) AND ($this->tagvspaces[$tag['value']][1]['h'] >= 0)) {
                $pre_h = $this->tagvspaces[$tag['value']][1]['h'];
            } elseif (isset($parent['fontsize'])) {
                $pre_h = (($parent['fontsize'] / $this->k) * $this->cell_height_ratio);
            } else {
                $pre_h = $this->FontSize * $this->cell_height_ratio;
            }
            if (isset($this->tagvspaces[$tag['value']][1]['n'])) {
                $n = $this->tagvspaces[$tag['value']][1]['n'];
            } elseif (preg_match('/[h][0-9]/', $tag['value']) > 0) {
                $n = 0.6;
            } else {
                $n = 1;
            }
            if ((!isset($this->tagvspaces[$tag['value']])) AND ($tag['value'] == 'div')) {
                $hb = 0;
            } else {
                $hb = ($n * $pre_h);
            }
            if ($maxbottomliney > $this->PageBreakTrigger) {
                $hbz = ($this->FontSize * $this->cell_height_ratio);
            } elseif ($this->y < $maxbottomliney) {
                $hbz = ($maxbottomliney - $this->y);
            }
        }
        switch ($tag['value']) {
            case 'tr': {
                $table_el = $dom[($dom[$key]['parent'])]['parent'];
                if (!isset($parent['endy'])) {
                    $dom[($dom[$key]['parent'])]['endy'] = $this->y;
                    $parent['endy']                      = $this->y;
                }
                if (!isset($parent['endpage'])) {
                    $dom[($dom[$key]['parent'])]['endpage'] = $this->page;
                    $parent['endpage']                      = $this->page;
                }
                if (!isset($parent['endcolumn'])) {
                    $dom[($dom[$key]['parent'])]['endcolumn'] = $this->current_column;
                    $parent['endcolumn']                      = $this->current_column;
                }
                if (isset($dom[$table_el]['rowspans'])) {
                    foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
                        $dom[$table_el]['rowspans'][$k]['rowspan'] -= 1;
                        if ($dom[$table_el]['rowspans'][$k]['rowspan'] == 0) {
                            if (($dom[$table_el]['rowspans'][$k]['endpage'] == $parent['endpage']) AND ($dom[$table_el]['rowspans'][$k]['endcolumn'] == $parent['endcolumn'])) {
                                $dom[($dom[$key]['parent'])]['endy'] = max($dom[$table_el]['rowspans'][$k]['endy'], $parent['endy']);
                            } elseif (($dom[$table_el]['rowspans'][$k]['endpage'] > $parent['endpage']) OR ($dom[$table_el]['rowspans'][$k]['endcolumn'] > $parent['endcolumn'])) {
                                $dom[($dom[$key]['parent'])]['endy']      = $dom[$table_el]['rowspans'][$k]['endy'];
                                $dom[($dom[$key]['parent'])]['endpage']   = $dom[$table_el]['rowspans'][$k]['endpage'];
                                $dom[($dom[$key]['parent'])]['endcolumn'] = $dom[$table_el]['rowspans'][$k]['endcolumn'];
                            }
                        }
                    }
                    foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
                        if ($dom[$table_el]['rowspans'][$k]['rowspan'] == 0) {
                            $dom[$table_el]['rowspans'][$k]['endpage']   = max($dom[$table_el]['rowspans'][$k]['endpage'], $dom[($dom[$key]['parent'])]['endpage']);
                            $dom[($dom[$key]['parent'])]['endpage']      = $dom[$table_el]['rowspans'][$k]['endpage'];
                            $dom[$table_el]['rowspans'][$k]['endcolumn'] = max($dom[$table_el]['rowspans'][$k]['endcolumn'], $dom[($dom[$key]['parent'])]['endcolumn']);
                            $dom[($dom[$key]['parent'])]['endcolumn']    = $dom[$table_el]['rowspans'][$k]['endcolumn'];
                            $dom[$table_el]['rowspans'][$k]['endy']      = max($dom[$table_el]['rowspans'][$k]['endy'], $dom[($dom[$key]['parent'])]['endy']);
                            $dom[($dom[$key]['parent'])]['endy']         = $dom[$table_el]['rowspans'][$k]['endy'];
                        }
                    }
                    foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
                        if ($dom[$table_el]['rowspans'][$k]['rowspan'] == 0) {
                            $dom[$table_el]['rowspans'][$k]['endpage']   = $dom[($dom[$key]['parent'])]['endpage'];
                            $dom[$table_el]['rowspans'][$k]['endcolumn'] = $dom[($dom[$key]['parent'])]['endcolumn'];
                            $dom[$table_el]['rowspans'][$k]['endy']      = $dom[($dom[$key]['parent'])]['endy'];
                        }
                    }
                }
                $this->setPage($dom[($dom[$key]['parent'])]['endpage']);
                if ($this->num_columns > 1) {
                    $this->selectColumn($dom[($dom[$key]['parent'])]['endcolumn']);
                }
                $this->y = $dom[($dom[$key]['parent'])]['endy'];
                if (isset($dom[$table_el]['attribute']['cellspacing'])) {
                    $this->y += $this->getHTMLUnitToUnits($dom[$table_el]['attribute']['cellspacing'], 1, 'px');
                } elseif (isset($dom[$table_el]['border-spacing'])) {
                    $this->y += $dom[$table_el]['border-spacing']['V'];
                }
                $this->Ln(0, $cell);
                if ($this->current_column == $parent['startcolumn']) {
                    $this->x = $parent['startx'];
                }
                if ($this->page > $parent['startpage']) {
                    if (($this->rtl) AND ($this->pagedim[$this->page]['orm'] != $this->pagedim[$parent['startpage']]['orm'])) {
                        $this->x -= ($this->pagedim[$this->page]['orm'] - $this->pagedim[$parent['startpage']]['orm']);
                    } elseif ((!$this->rtl) AND ($this->pagedim[$this->page]['olm'] != $this->pagedim[$parent['startpage']]['olm'])) {
                        $this->x += ($this->pagedim[$this->page]['olm'] - $this->pagedim[$parent['startpage']]['olm']);
                    }
                }
                break;
            }
            case 'tablehead':
                $in_table_head = true;
                $this->inthead = false;
            case 'table': {
                $table_el = $parent;
                if (isset($table_el['attribute']['border']) AND ($table_el['attribute']['border'] > 0)) {
                    $border = array(
                        'LTRB' => array(
                            'width' => $this->getCSSBorderWidth($table_el['attribute']['border']),
                            'cap' => 'square',
                            'join' => 'miter',
                            'dash' => 0,
                            'color' => array(
                                0,
                                0,
                                0
                            )
                        )
                    );
                } else {
                    $border = 0;
                }
                $default_border = $border;
                foreach ($dom[($dom[$key]['parent'])]['trids'] as $j => $trkey) {
                    if (isset($dom[($dom[$key]['parent'])]['rowspans'])) {
                        foreach ($dom[($dom[$key]['parent'])]['rowspans'] as $k => $trwsp) {
                            if ($trwsp['trid'] == $trkey) {
                                $dom[($dom[$key]['parent'])]['rowspans'][$k]['mrowspan'] -= 1;
                            }
                            if (isset($prevtrkey) AND ($trwsp['trid'] == $prevtrkey) AND ($trwsp['mrowspan'] >= 0)) {
                                $dom[($dom[$key]['parent'])]['rowspans'][$k]['trid'] = $trkey;
                            }
                        }
                    }
                    if (isset($prevtrkey) AND ($dom[$trkey]['startpage'] > $dom[$prevtrkey]['endpage'])) {
                        $pgendy                  = $this->pagedim[$dom[$prevtrkey]['endpage']]['hk'] - $this->pagedim[$dom[$prevtrkey]['endpage']]['bm'];
                        $dom[$prevtrkey]['endy'] = $pgendy;
                        if (isset($dom[($dom[$key]['parent'])]['rowspans'])) {
                            foreach ($dom[($dom[$key]['parent'])]['rowspans'] as $k => $trwsp) {
                                if (($trwsp['trid'] == $trkey) AND ($trwsp['mrowspan'] > 1) AND ($trwsp['endpage'] == $dom[$prevtrkey]['endpage'])) {
                                    $dom[($dom[$key]['parent'])]['rowspans'][$k]['endy']     = $pgendy;
                                    $dom[($dom[$key]['parent'])]['rowspans'][$k]['mrowspan'] = -1;
                                }
                            }
                        }
                    }
                    $prevtrkey = $trkey;
                    $table_el  = $dom[($dom[$key]['parent'])];
                }
                if (count($table_el['trids']) > 0) {
                    unset($xmax);
                }
                foreach ($table_el['trids'] as $j => $trkey) {
                    $parent = $dom[$trkey];
                    if (!isset($xmax)) {
                        $xmax = $parent['cellpos'][(count($parent['cellpos']) - 1)]['endx'];
                    }
                    foreach ($parent['cellpos'] as $k => $cellpos) {
                        if (isset($cellpos['rowspanid']) AND ($cellpos['rowspanid'] >= 0)) {
                            $cellpos['startx'] = $table_el['rowspans'][($cellpos['rowspanid'])]['startx'];
                            $cellpos['endx']   = $table_el['rowspans'][($cellpos['rowspanid'])]['endx'];
                            $endy              = $table_el['rowspans'][($cellpos['rowspanid'])]['endy'];
                            $startpage         = $table_el['rowspans'][($cellpos['rowspanid'])]['startpage'];
                            $endpage           = $table_el['rowspans'][($cellpos['rowspanid'])]['endpage'];
                            $startcolumn       = $table_el['rowspans'][($cellpos['rowspanid'])]['startcolumn'];
                            $endcolumn         = $table_el['rowspans'][($cellpos['rowspanid'])]['endcolumn'];
                        } else {
                            $endy        = $parent['endy'];
                            $startpage   = $parent['startpage'];
                            $endpage     = $parent['endpage'];
                            $startcolumn = $parent['startcolumn'];
                            $endcolumn   = $parent['endcolumn'];
                        }
                        if ($this->num_columns == 0) {
                            $this->num_columns = 1;
                        }
                        if (isset($cellpos['border'])) {
                            $border = $cellpos['border'];
                        }
                        if (isset($cellpos['bgcolor']) AND ($cellpos['bgcolor']) !== false) {
                            $this->SetFillColorArray($cellpos['bgcolor']);
                            $fill = true;
                        } else {
                            $fill = false;
                        }
                        $x             = $cellpos['startx'];
                        $y             = $parent['starty'];
                        $starty        = $y;
                        $w             = abs($cellpos['endx'] - $cellpos['startx']);
                        $border_start  = $this->getBorderMode($border, $position = 'start');
                        $border_end    = $this->getBorderMode($border, $position = 'end');
                        $border_middle = $this->getBorderMode($border, $position = 'middle');
                        for ($page = $startpage; $page <= $endpage; ++$page) {
                            $ccode = '';
                            $this->setPage($page);
                            if ($this->num_columns < 2) {
                                $this->x = $x;
                                $this->y = $this->tMargin;
                            }
                            if ($page > $startpage) {
                                if (($this->rtl) AND ($this->pagedim[$page]['orm'] != $this->pagedim[$startpage]['orm'])) {
                                    $this->x -= ($this->pagedim[$page]['orm'] - $this->pagedim[$startpage]['orm']);
                                } elseif ((!$this->rtl) AND ($this->pagedim[$page]['olm'] != $this->pagedim[$startpage]['olm'])) {
                                    $this->x += ($this->pagedim[$page]['olm'] - $this->pagedim[$startpage]['olm']);
                                }
                            }
                            if ($startpage == $endpage) {
                                $deltacol = 0;
                                $deltath  = 0;
                                for ($column = $startcolumn; $column <= $endcolumn; ++$column) {
                                    $this->selectColumn($column);
                                    if ($startcolumn == $endcolumn) {
                                        $cborder = $border;
                                        $h       = $endy - $parent['starty'];
                                        $this->y = $y;
                                        $this->x = $x;
                                    } elseif ($column == $startcolumn) {
                                        $cborder = $border_start;
                                        $this->y = $starty;
                                        $this->x = $x;
                                        $h       = $this->h - $this->y - $this->bMargin;
                                        if ($this->rtl) {
                                            $deltacol = $this->x + $this->rMargin - $this->w;
                                        } else {
                                            $deltacol = $this->x - $this->lMargin;
                                        }
                                    } elseif ($column == $endcolumn) {
                                        $cborder = $border_end;
                                        if (isset($this->columns[$column]['th']['\'' . $page . '\''])) {
                                            $this->y = $this->columns[$column]['th']['\'' . $page . '\''];
                                        }
                                        $this->x += $deltacol;
                                        $h = $endy - $this->y;
                                    } else {
                                        $cborder = $border_middle;
                                        if (isset($this->columns[$column]['th']['\'' . $page . '\''])) {
                                            $this->y = $this->columns[$column]['th']['\'' . $page . '\''];
                                        }
                                        $this->x += $deltacol;
                                        $h = $this->h - $this->y - $this->bMargin;
                                    }
                                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                                }
                            } elseif ($page == $startpage) {
                                $deltacol = 0;
                                $deltath  = 0;
                                for ($column = $startcolumn; $column < $this->num_columns; ++$column) {
                                    $this->selectColumn($column);
                                    if ($column == $startcolumn) {
                                        $cborder = $border_start;
                                        $this->y = $starty;
                                        $this->x = $x;
                                        $h       = $this->h - $this->y - $this->bMargin;
                                        if ($this->rtl) {
                                            $deltacol = $this->x + $this->rMargin - $this->w;
                                        } else {
                                            $deltacol = $this->x - $this->lMargin;
                                        }
                                    } else {
                                        $cborder = $border_middle;
                                        if (isset($this->columns[$column]['th']['\'' . $page . '\''])) {
                                            $this->y = $this->columns[$column]['th']['\'' . $page . '\''];
                                        }
                                        $this->x += $deltacol;
                                        $h = $this->h - $this->y - $this->bMargin;
                                    }
                                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                                }
                            } elseif ($page == $endpage) {
                                $deltacol = 0;
                                $deltath  = 0;
                                for ($column = 0; $column <= $endcolumn; ++$column) {
                                    $this->selectColumn($column);
                                    if ($column == $endcolumn) {
                                        $cborder = $border_end;
                                        if (isset($this->columns[$column]['th']['\'' . $page . '\''])) {
                                            $this->y = $this->columns[$column]['th']['\'' . $page . '\''];
                                        }
                                        $this->x += $deltacol;
                                        $h = $endy - $this->y;
                                    } else {
                                        $cborder = $border_middle;
                                        if (isset($this->columns[$column]['th']['\'' . $page . '\''])) {
                                            $this->y = $this->columns[$column]['th']['\'' . $page . '\''];
                                        }
                                        $this->x += $deltacol;
                                        $h = $this->h - $this->y - $this->bMargin;
                                    }
                                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                                }
                            } else {
                                $deltacol = 0;
                                $deltath  = 0;
                                for ($column = 0; $column < $this->num_columns; ++$column) {
                                    $this->selectColumn($column);
                                    $cborder = $border_middle;
                                    if (isset($this->columns[$column]['th']['\'' . $page . '\''])) {
                                        $this->y = $this->columns[$column]['th']['\'' . $page . '\''];
                                    }
                                    $this->x += $deltacol;
                                    $h = $this->h - $this->y - $this->bMargin;
                                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                                }
                            }
                            if ($cborder OR $fill) {
                                $offsetlen = strlen($ccode);
                                if ($this->inxobj) {
                                    if (end($this->xobjects[$this->xobjid]['transfmrk']) !== false) {
                                        $pagemarkkey = key($this->xobjects[$this->xobjid]['transfmrk']);
                                        $pagemark    = $this->xobjects[$this->xobjid]['transfmrk'][$pagemarkkey];
                                        $this->xobjects[$this->xobjid]['transfmrk'][$pagemarkkey] += $offsetlen;
                                    } else {
                                        $pagemark = $this->xobjects[$this->xobjid]['intmrk'];
                                        $this->xobjects[$this->xobjid]['intmrk'] += $offsetlen;
                                    }
                                    $pagebuff                                 = $this->xobjects[$this->xobjid]['outdata'];
                                    $pstart                                   = substr($pagebuff, 0, $pagemark);
                                    $pend                                     = substr($pagebuff, $pagemark);
                                    $this->xobjects[$this->xobjid]['outdata'] = $pstart . $ccode . $pend;
                                } else {
                                    if (end($this->transfmrk[$this->page]) !== false) {
                                        $pagemarkkey = key($this->transfmrk[$this->page]);
                                        $pagemark    = $this->transfmrk[$this->page][$pagemarkkey];
                                        $this->transfmrk[$this->page][$pagemarkkey] += $offsetlen;
                                    } elseif ($this->InFooter) {
                                        $pagemark = $this->footerpos[$this->page];
                                        $this->footerpos[$this->page] += $offsetlen;
                                    } else {
                                        $pagemark = $this->intmrk[$this->page];
                                        $this->intmrk[$this->page] += $offsetlen;
                                    }
                                    $pagebuff = $this->getPageBuffer($this->page);
                                    $pstart   = substr($pagebuff, 0, $pagemark);
                                    $pend     = substr($pagebuff, $pagemark);
                                    $this->setPageBuffer($this->page, $pstart . $ccode . $pend);
                                }
                            }
                        }
                        $border = $default_border;
                    }
                    if (isset($table_el['attribute']['cellspacing'])) {
                        $this->y += $this->getHTMLUnitToUnits($table_el['attribute']['cellspacing'], 1, 'px');
                    } elseif (isset($table_el['border-spacing'])) {
                        $this->y += $table_el['border-spacing']['V'];
                    }
                    $this->Ln(0, $cell);
                    $this->x = $parent['startx'];
                    if ($endpage > $startpage) {
                        if (($this->rtl) AND ($this->pagedim[$endpage]['orm'] != $this->pagedim[$startpage]['orm'])) {
                            $this->x += ($this->pagedim[$endpage]['orm'] - $this->pagedim[$startpage]['orm']);
                        } elseif ((!$this->rtl) AND ($this->pagedim[$endpage]['olm'] != $this->pagedim[$startpage]['olm'])) {
                            $this->x += ($this->pagedim[$endpage]['olm'] - $this->pagedim[$startpage]['olm']);
                        }
                    }
                }
                if (!$in_table_head) {
                    $this->cell_padding = $table_el['old_cell_padding'];
                    $this->resetLastH();
                    if (($this->page == ($this->numpages - 1)) AND ($this->pageopen[$this->numpages])) {
                        $plendiff = ($this->pagelen[$this->numpages] - $this->emptypagemrk[$this->numpages]);
                        if (($plendiff > 0) AND ($plendiff < 60)) {
                            $pagediff = substr($this->getPageBuffer($this->numpages), $this->emptypagemrk[$this->numpages], $plendiff);
                            if (substr($pagediff, 0, 5) == 'BT /F') {
                                $plendiff = 0;
                            }
                        }
                        if ($plendiff == 0) {
                            $this->deletePage($this->numpages);
                        }
                    }
                    if (isset($this->theadMargins['top'])) {
                        $this->tMargin = $this->theadMargins['top'];
                    }
                    if (!isset($table_el['attribute']['nested']) OR ($table_el['attribute']['nested'] != 'true')) {
                        $this->thead                      = '';
                        $this->theadMargins               = array();
                        $this->pagedim[$this->page]['tm'] = $this->tMargin;
                    }
                }
                $parent = $table_el;
                break;
            }
            case 'a': {
                $this->HREF = '';
                break;
            }
            case 'sup': {
                $this->SetXY($this->GetX(), $this->GetY() + ((0.7 * $parent['fontsize']) / $this->k));
                break;
            }
            case 'sub': {
                $this->SetXY($this->GetX(), $this->GetY() - ((0.3 * $parent['fontsize']) / $this->k));
                break;
            }
            case 'div': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                break;
            }
            case 'blockquote': {
                if ($this->rtl) {
                    $this->rMargin -= $this->listindent;
                } else {
                    $this->lMargin -= $this->listindent;
                }
                --$this->listindentlevel;
                $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                break;
            }
            case 'p': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                break;
            }
            case 'pre': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                $this->premode = false;
                break;
            }
            case 'dl': {
                --$this->listnum;
                if ($this->listnum <= 0) {
                    $this->listnum = 0;
                    $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                } else {
                    $this->addHTMLVertSpace(0, 0, $cell, false, $lasttag);
                }
                $this->resetLastH();
                break;
            }
            case 'dt': {
                $this->lispacer = '';
                $this->addHTMLVertSpace(0, 0, $cell, false, $lasttag);
                break;
            }
            case 'dd': {
                $this->lispacer = '';
                if ($this->rtl) {
                    $this->rMargin -= $this->listindent;
                } else {
                    $this->lMargin -= $this->listindent;
                }
                --$this->listindentlevel;
                $this->addHTMLVertSpace(0, 0, $cell, false, $lasttag);
                break;
            }
            case 'ul':
            case 'ol': {
                --$this->listnum;
                $this->lispacer = '';
                if ($this->rtl) {
                    $this->rMargin -= $this->listindent;
                } else {
                    $this->lMargin -= $this->listindent;
                }
                --$this->listindentlevel;
                if ($this->listnum <= 0) {
                    $this->listnum = 0;
                    $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                } else {
                    $this->addHTMLVertSpace(0, 0, $cell, false, $lasttag);
                }
                $this->resetLastH();
                break;
            }
            case 'li': {
                $this->lispacer = '';
                $this->addHTMLVertSpace(0, 0, $cell, false, $lasttag);
                break;
            }
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6': {
                $this->addHTMLVertSpace($hbz, $hb, $cell, false, $lasttag);
                break;
            }
            case 'form': {
                $this->form_action  = '';
                $this->form_enctype = 'application/x-www-form-urlencoded';
                break;
            }
            default: {
                break;
            }
        }
        $this->drawHTMLTagBorder($parent, $xmax);
        if (isset($dom[($dom[$key]['parent'])]['attribute']['pagebreakafter'])) {
            $pba = $dom[($dom[$key]['parent'])]['attribute']['pagebreakafter'];
            if (($pba == 'true') OR ($pba == 'left') OR ($pba == 'right')) {
                $this->checkPageBreak($this->PageBreakTrigger + 1);
            }
            if ((($pba == 'left') AND (((!$this->rtl) AND (($this->page % 2) == 0)) OR (($this->rtl) AND (($this->page % 2) != 0)))) OR (($pba == 'right') AND (((!$this->rtl) AND (($this->page % 2) != 0)) OR (($this->rtl) AND (($this->page % 2) == 0))))) {
                $this->checkPageBreak($this->PageBreakTrigger + 1);
            }
        }
        $this->tmprtl = false;
        return $dom;
    }
    protected function addHTMLVertSpace($hbz = 0, $hb = 0, $cell = false, $firsttag = false, $lasttag = false)
    {
        if ($firsttag) {
            $this->Ln(0, $cell);
            $this->htmlvspace = 0;
            return;
        }
        if ($lasttag) {
            $this->Ln($hbz, $cell);
            $this->htmlvspace = 0;
            return;
        }
        if ($hb < $this->htmlvspace) {
            $hd = 0;
        } else {
            $hd               = $hb - $this->htmlvspace;
            $this->htmlvspace = $hb;
        }
        $this->Ln(($hbz + $hd), $cell);
    }
    protected function getBorderStartPosition()
    {
        if ($this->rtl) {
            $xmax = $this->lMargin;
        } else {
            $xmax = $this->w - $this->rMargin;
        }
        return array(
            'page' => $this->page,
            'column' => $this->current_column,
            'x' => $this->x,
            'y' => $this->y,
            'xmax' => $xmax
        );
    }
    protected function drawHTMLTagBorder($tag, $xmax)
    {
        if (!isset($tag['borderposition'])) {
            return;
        }
        $prev_x      = $this->x;
        $prev_y      = $this->y;
        $prev_lasth  = $this->lasth;
        $border      = 0;
        $fill        = false;
        $this->lasth = 0;
        if (isset($tag['border']) AND !empty($tag['border'])) {
            $border = $tag['border'];
            if (!$this->empty_string($this->thead) AND (!$this->inthead)) {
                $border = $this->getBorderMode($border, $position = 'middle');
            }
        }
        if (isset($tag['bgcolor']) AND ($tag['bgcolor'] !== false)) {
            $old_bgcolor = $this->bgcolor;
            $this->SetFillColorArray($tag['bgcolor']);
            $fill = true;
        }
        if (!$border AND !$fill) {
            return;
        }
        if (isset($tag['attribute']['cellspacing'])) {
            $clsp        = $this->getHTMLUnitToUnits($tag['attribute']['cellspacing'], 1, 'px');
            $cellspacing = array(
                'H' => $clsp,
                'V' => $clsp
            );
        } elseif (isset($tag['border-spacing'])) {
            $cellspacing = $tag['border-spacing'];
        } else {
            $cellspacing = array(
                'H' => 0,
                'V' => 0
            );
        }
        if (($tag['value'] != 'table') AND (is_array($border)) AND (!empty($border))) {
            $border['mode'] = 'ext';
        }
        if ($this->rtl) {
            if ($xmax >= $tag['borderposition']['x']) {
                $xmax = $tag['borderposition']['xmax'];
            }
            $w = ($tag['borderposition']['x'] - $xmax);
        } else {
            if ($xmax <= $tag['borderposition']['x']) {
                $xmax = $tag['borderposition']['xmax'];
            }
            $w = ($xmax - $tag['borderposition']['x']);
        }
        if ($w <= 0) {
            return;
        }
        $w += $cellspacing['H'];
        $startpage   = $tag['borderposition']['page'];
        $startcolumn = $tag['borderposition']['column'];
        $x           = $tag['borderposition']['x'];
        $y           = $tag['borderposition']['y'];
        $endpage     = $this->page;
        $starty      = $tag['borderposition']['y'] - $cellspacing['V'];
        $currentY    = $this->y;
        $this->x     = $x;
        $endcolumn   = $this->current_column;
        if ($this->num_columns == 0) {
            $this->num_columns = 1;
        }
        $border_start       = $this->getBorderMode($border, $position = 'start');
        $border_end         = $this->getBorderMode($border, $position = 'end');
        $border_middle      = $this->getBorderMode($border, $position = 'middle');
        $temp_page_regions  = $this->page_regions;
        $this->page_regions = array();
        for ($page = $startpage; $page <= $endpage; ++$page) {
            $ccode = '';
            $this->setPage($page);
            if ($this->num_columns < 2) {
                $this->x = $x;
                $this->y = $this->tMargin;
            }
            if ($page > $startpage) {
                if (($this->rtl) AND ($this->pagedim[$page]['orm'] != $this->pagedim[$startpage]['orm'])) {
                    $this->x -= ($this->pagedim[$page]['orm'] - $this->pagedim[$startpage]['orm']);
                } elseif ((!$this->rtl) AND ($this->pagedim[$page]['olm'] != $this->pagedim[$startpage]['olm'])) {
                    $this->x += ($this->pagedim[$page]['olm'] - $this->pagedim[$startpage]['olm']);
                }
            }
            if ($startpage == $endpage) {
                for ($column = $startcolumn; $column <= $endcolumn; ++$column) {
                    $this->selectColumn($column);
                    if ($startcolumn == $endcolumn) {
                        $cborder = $border;
                        $h       = ($currentY - $y) + $cellspacing['V'];
                        $this->y = $starty;
                    } elseif ($column == $startcolumn) {
                        $cborder = $border_start;
                        $this->y = $starty;
                        $h       = $this->h - $this->y - $this->bMargin;
                    } elseif ($column == $endcolumn) {
                        $cborder = $border_end;
                        $h       = $currentY - $this->y;
                    } else {
                        $cborder = $border_middle;
                        $h       = $this->h - $this->y - $this->bMargin;
                    }
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            } elseif ($page == $startpage) {
                for ($column = $startcolumn; $column < $this->num_columns; ++$column) {
                    $this->selectColumn($column);
                    if ($column == $startcolumn) {
                        $cborder = $border_start;
                        $this->y = $starty;
                        $h       = $this->h - $this->y - $this->bMargin;
                    } else {
                        $cborder = $border_middle;
                        $h       = $this->h - $this->y - $this->bMargin;
                    }
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            } elseif ($page == $endpage) {
                for ($column = 0; $column <= $endcolumn; ++$column) {
                    $this->selectColumn($column);
                    if ($column == $endcolumn) {
                        $cborder = $border_end;
                        $h       = $currentY - $this->y;
                    } else {
                        $cborder = $border_middle;
                        $h       = $this->h - $this->y - $this->bMargin;
                    }
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            } else {
                for ($column = 0; $column < $this->num_columns; ++$column) {
                    $this->selectColumn($column);
                    $cborder = $border_middle;
                    $h       = $this->h - $this->y - $this->bMargin;
                    $ccode .= $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, true) . "\n";
                }
            }
            if ($cborder OR $fill) {
                $offsetlen = strlen($ccode);
                if ($this->inxobj) {
                    if (end($this->xobjects[$this->xobjid]['transfmrk']) !== false) {
                        $pagemarkkey = key($this->xobjects[$this->xobjid]['transfmrk']);
                        $pagemark    = $this->xobjects[$this->xobjid]['transfmrk'][$pagemarkkey];
                        $this->xobjects[$this->xobjid]['transfmrk'][$pagemarkkey] += $offsetlen;
                    } else {
                        $pagemark = $this->xobjects[$this->xobjid]['intmrk'];
                        $this->xobjects[$this->xobjid]['intmrk'] += $offsetlen;
                    }
                    $pagebuff                                 = $this->xobjects[$this->xobjid]['outdata'];
                    $pstart                                   = substr($pagebuff, 0, $pagemark);
                    $pend                                     = substr($pagebuff, $pagemark);
                    $this->xobjects[$this->xobjid]['outdata'] = $pstart . $ccode . $pend;
                } else {
                    if (end($this->transfmrk[$this->page]) !== false) {
                        $pagemarkkey = key($this->transfmrk[$this->page]);
                        $pagemark    = $this->transfmrk[$this->page][$pagemarkkey];
                        $this->transfmrk[$this->page][$pagemarkkey] += $offsetlen;
                    } elseif ($this->InFooter) {
                        $pagemark = $this->footerpos[$this->page];
                        $this->footerpos[$this->page] += $offsetlen;
                    } else {
                        $pagemark = $this->intmrk[$this->page];
                        $this->intmrk[$this->page] += $offsetlen;
                    }
                    $pagebuff = $this->getPageBuffer($this->page);
                    $pstart   = substr($pagebuff, 0, $this->bordermrk[$this->page]);
                    $pend     = substr($pagebuff, $this->bordermrk[$this->page]);
                    $this->setPageBuffer($this->page, $pstart . $ccode . $pend);
                    $this->bordermrk[$this->page] += $offsetlen;
                    $this->cntmrk[$this->page] += $offsetlen;
                }
            }
        }
        $this->page_regions = $temp_page_regions;
        if (isset($old_bgcolor)) {
            $this->SetFillColorArray($old_bgcolor);
        }
        $this->x     = $prev_x;
        $this->y     = $prev_y;
        $this->lasth = $prev_lasth;
    }
    public function setLIsymbol($symbol = '!')
    {
        if (substr($symbol, 0, 4) == 'img|') {
            $this->lisymbol = $symbol;
            return;
        }
        $symbol = strtolower($symbol);
        switch ($symbol) {
            case '!':
            case '#':
            case 'disc':
            case 'circle':
            case 'square':
            case '1':
            case 'decimal':
            case 'decimal-leading-zero':
            case 'i':
            case 'lower-roman':
            case 'I':
            case 'upper-roman':
            case 'a':
            case 'lower-alpha':
            case 'lower-latin':
            case 'A':
            case 'upper-alpha':
            case 'upper-latin':
            case 'lower-greek': {
                $this->lisymbol = $symbol;
                break;
            }
            default: {
                $this->lisymbol = '';
            }
        }
    }
    public function SetBooklet($booklet = true, $inner = -1, $outer = -1)
    {
        $this->booklet = $booklet;
        if ($inner >= 0) {
            $this->lMargin = $inner;
        }
        if ($outer >= 0) {
            $this->rMargin = $outer;
        }
    }
    protected function swapMargins($reverse = true)
    {
        if ($reverse) {
            $mtemp                  = $this->original_lMargin;
            $this->original_lMargin = $this->original_rMargin;
            $this->original_rMargin = $mtemp;
            $deltam                 = $this->original_lMargin - $this->original_rMargin;
            $this->lMargin += $deltam;
            $this->rMargin -= $deltam;
        }
    }
    public function setHtmlVSpace($tagvs)
    {
        $this->tagvspaces = $tagvs;
    }
    public function setListIndentWidth($width)
    {
        return $this->customlistindent = floatval($width);
    }
    public function setOpenCell($isopen)
    {
        $this->opencell = $isopen;
    }
    public function setHtmlLinksStyle($color = array(0, 0, 255), $fontstyle = 'U')
    {
        $this->htmlLinkColorArray = $color;
        $this->htmlLinkFontStyle  = $fontstyle;
    }
    public function getHTMLUnitToUnits($htmlval, $refsize = 1, $defaultunit = 'px', $points = false)
    {
        $supportedunits = array(
            '%',
            'em',
            'ex',
            'px',
            'in',
            'cm',
            'mm',
            'pc',
            'pt'
        );
        $retval         = 0;
        $value          = 0;
        $unit           = 'px';
        $k              = $this->k;
        if ($points) {
            $k = 1;
        }
        if (in_array($defaultunit, $supportedunits)) {
            $unit = $defaultunit;
        }
        if (is_numeric($htmlval)) {
            $value = floatval($htmlval);
        } elseif (preg_match('/([0-9\.\-\+]+)/', $htmlval, $mnum)) {
            $value = floatval($mnum[1]);
            if (preg_match('/([a-z%]+)/', $htmlval, $munit)) {
                if (in_array($munit[1], $supportedunits)) {
                    $unit = $munit[1];
                }
            }
        }
        switch ($unit) {
            case '%': {
                $retval = (($value * $refsize) / 100);
                break;
            }
            case 'em': {
                $retval = ($value * $refsize);
                break;
            }
            case 'ex': {
                $retval = $value * ($refsize / 2);
                break;
            }
            case 'in': {
                $retval = ($value * $this->dpi) / $k;
                break;
            }
            case 'cm': {
                $retval = ($value / 2.54 * $this->dpi) / $k;
                break;
            }
            case 'mm': {
                $retval = ($value / 25.4 * $this->dpi) / $k;
                break;
            }
            case 'pc': {
                $retval = ($value * 12) / $k;
                break;
            }
            case 'pt': {
                $retval = $value / $k;
                break;
            }
            case 'px': {
                $retval = $this->pixelsToUnits($value);
                break;
            }
        }
        return $retval;
    }
    public function intToRoman($number)
    {
        $roman = '';
        while ($number >= 1000) {
            $roman .= 'M';
            $number -= 1000;
        }
        while ($number >= 900) {
            $roman .= 'CM';
            $number -= 900;
        }
        while ($number >= 500) {
            $roman .= 'D';
            $number -= 500;
        }
        while ($number >= 400) {
            $roman .= 'CD';
            $number -= 400;
        }
        while ($number >= 100) {
            $roman .= 'C';
            $number -= 100;
        }
        while ($number >= 90) {
            $roman .= 'XC';
            $number -= 90;
        }
        while ($number >= 50) {
            $roman .= 'L';
            $number -= 50;
        }
        while ($number >= 40) {
            $roman .= 'XL';
            $number -= 40;
        }
        while ($number >= 10) {
            $roman .= 'X';
            $number -= 10;
        }
        while ($number >= 9) {
            $roman .= 'IX';
            $number -= 9;
        }
        while ($number >= 5) {
            $roman .= 'V';
            $number -= 5;
        }
        while ($number >= 4) {
            $roman .= 'IV';
            $number -= 4;
        }
        while ($number >= 1) {
            $roman .= 'I';
            --$number;
        }
        return $roman;
    }
    protected function putHtmlListBullet($listdepth, $listtype = '', $size = 10)
    {
        $size /= $this->k;
        $fill        = '';
        $bgcolor     = $this->bgcolor;
        $color       = $this->fgcolor;
        $strokecolor = $this->strokecolor;
        $width       = 0;
        $textitem    = '';
        $tmpx        = $this->x;
        $lspace      = $this->GetStringWidth('  ');
        if ($listtype == '^') {
            $this->lispacer = '';
            return;
        } elseif ($listtype == '!') {
            $deftypes = array(
                'disc',
                'circle',
                'square'
            );
            $listtype = $deftypes[($listdepth - 1) % 3];
        } elseif ($listtype == '#') {
            $listtype = 'decimal';
        } elseif (substr($listtype, 0, 4) == 'img|') {
            $img      = explode('|', $listtype);
            $listtype = 'img';
        }
        switch ($listtype) {
            case 'none': {
                break;
            }
            case 'disc': {
                $r = $size / 6;
                $lspace += (2 * $r);
                if ($this->rtl) {
                    $this->x += $lspace;
                } else {
                    $this->x -= $lspace;
                }
                $this->Circle(($this->x + $r), ($this->y + ($this->lasth / 2)), $r, 0, 360, 'F', array(), $color, 8);
                break;
            }
            case 'circle': {
                $r = $size / 6;
                $lspace += (2 * $r);
                if ($this->rtl) {
                    $this->x += $lspace;
                } else {
                    $this->x -= $lspace;
                }
                $prev_line_style = $this->linestyleWidth . ' ' . $this->linestyleCap . ' ' . $this->linestyleJoin . ' ' . $this->linestyleDash . ' ' . $this->DrawColor;
                $new_line_style  = array(
                    'width' => ($r / 3),
                    'cap' => 'butt',
                    'join' => 'miter',
                    'dash' => 0,
                    'phase' => 0,
                    'color' => $color
                );
                $this->Circle(($this->x + $r), ($this->y + ($this->lasth / 2)), ($r * (1 - (1 / 6))), 0, 360, 'D', $new_line_style, array(), 8);
                $this->_out($prev_line_style);
                break;
            }
            case 'square': {
                $l = $size / 3;
                $lspace += $l;
                if ($this->rtl) {
                    ;
                    $this->x += $lspace;
                } else {
                    $this->x -= $lspace;
                }
                $this->Rect($this->x, ($this->y + (($this->lasth - $l) / 2)), $l, $l, 'F', array(), $color);
                break;
            }
            case 'img': {
                $lspace += $img[2];
                if ($this->rtl) {
                    ;
                    $this->x += $lspace;
                } else {
                    $this->x -= $lspace;
                }
                $imgtype = strtolower($img[1]);
                $prev_y  = $this->y;
                switch ($imgtype) {
                    case 'svg': {
                        $this->ImageSVG($img[4], $this->x, ($this->y + (($this->lasth - $img[3]) / 2)), $img[2], $img[3], '', 'T', '', 0, false);
                        break;
                    }
                    case 'ai':
                    case 'eps': {
                        $this->ImageEps($img[4], $this->x, ($this->y + (($this->lasth - $img[3]) / 2)), $img[2], $img[3], '', true, 'T', '', 0, false);
                        break;
                    }
                    default: {
                        $this->Image($img[4], $this->x, ($this->y + (($this->lasth - $img[3]) / 2)), $img[2], $img[3], $img[1], '', 'T', false, 300, '', false, false, 0, false, false, false);
                        break;
                    }
                }
                $this->y = $prev_y;
                break;
            }
            case '1':
            case 'decimal': {
                $textitem = $this->listcount[$this->listnum];
                break;
            }
            case 'decimal-leading-zero': {
                $textitem = sprintf('%02d', $this->listcount[$this->listnum]);
                break;
            }
            case 'i':
            case 'lower-roman': {
                $textitem = strtolower($this->intToRoman($this->listcount[$this->listnum]));
                break;
            }
            case 'I':
            case 'upper-roman': {
                $textitem = $this->intToRoman($this->listcount[$this->listnum]);
                break;
            }
            case 'a':
            case 'lower-alpha':
            case 'lower-latin': {
                $textitem = chr(97 + $this->listcount[$this->listnum] - 1);
                break;
            }
            case 'A':
            case 'upper-alpha':
            case 'upper-latin': {
                $textitem = chr(65 + $this->listcount[$this->listnum] - 1);
                break;
            }
            case 'lower-greek': {
                $textitem = $this->unichr(945 + $this->listcount[$this->listnum] - 1);
                break;
            }
            default: {
                $textitem = $this->listcount[$this->listnum];
            }
        }
        if (!$this->empty_string($textitem)) {
            $prev_y = $this->y;
            $h      = ($this->FontSize * $this->cell_height_ratio) + $this->cell_padding['T'] + $this->cell_padding['B'];
            if ($this->checkPageBreak($h) OR ($this->y < $prev_y)) {
                $tmpx = $this->x;
            }
            if ($this->rtl) {
                $textitem = '.' . $textitem;
            } else {
                $textitem = $textitem . '.';
            }
            $lspace += $this->GetStringWidth($textitem);
            if ($this->rtl) {
                $this->x += $lspace;
            } else {
                $this->x -= $lspace;
            }
            $this->Write($this->lasth, $textitem, '', false, '', false, 0, false);
        }
        $this->x        = $tmpx;
        $this->lispacer = '^';
        $this->SetFillColorArray($bgcolor);
        $this->SetDrawColorArray($strokecolor);
        $this->SettextColorArray($color);
    }
    protected function getGraphicVars()
    {
        $grapvars = array(
            'FontFamily' => $this->FontFamily,
            'FontStyle' => $this->FontStyle,
            'FontSizePt' => $this->FontSizePt,
            'rMargin' => $this->rMargin,
            'lMargin' => $this->lMargin,
            'cell_padding' => $this->cell_padding,
            'cell_margin' => $this->cell_margin,
            'LineWidth' => $this->LineWidth,
            'linestyleWidth' => $this->linestyleWidth,
            'linestyleCap' => $this->linestyleCap,
            'linestyleJoin' => $this->linestyleJoin,
            'linestyleDash' => $this->linestyleDash,
            'textrendermode' => $this->textrendermode,
            'textstrokewidth' => $this->textstrokewidth,
            'DrawColor' => $this->DrawColor,
            'FillColor' => $this->FillColor,
            'TextColor' => $this->TextColor,
            'ColorFlag' => $this->ColorFlag,
            'bgcolor' => $this->bgcolor,
            'fgcolor' => $this->fgcolor,
            'htmlvspace' => $this->htmlvspace,
            'listindent' => $this->listindent,
            'listindentlevel' => $this->listindentlevel,
            'listnum' => $this->listnum,
            'listordered' => $this->listordered,
            'listcount' => $this->listcount,
            'lispacer' => $this->lispacer,
            'cell_height_ratio' => $this->cell_height_ratio,
            'font_stretching' => $this->font_stretching,
            'font_spacing' => $this->font_spacing,
            'lasth' => $this->lasth,
            'tMargin' => $this->tMargin,
            'bMargin' => $this->bMargin,
            'AutoPageBreak' => $this->AutoPageBreak,
            'PageBreakTrigger' => $this->PageBreakTrigger,
            'x' => $this->x,
            'y' => $this->y,
            'w' => $this->w,
            'h' => $this->h,
            'wPt' => $this->wPt,
            'hPt' => $this->hPt,
            'fwPt' => $this->fwPt,
            'fhPt' => $this->fhPt,
            'page' => $this->page,
            'current_column' => $this->current_column,
            'num_columns' => $this->num_columns
        );
        return $grapvars;
    }
    protected function setGraphicVars($gvars, $extended = false)
    {
        $this->FontFamily        = $gvars['FontFamily'];
        $this->FontStyle         = $gvars['FontStyle'];
        $this->FontSizePt        = $gvars['FontSizePt'];
        $this->rMargin           = $gvars['rMargin'];
        $this->lMargin           = $gvars['lMargin'];
        $this->cell_padding      = $gvars['cell_padding'];
        $this->cell_margin       = $gvars['cell_margin'];
        $this->LineWidth         = $gvars['LineWidth'];
        $this->linestyleWidth    = $gvars['linestyleWidth'];
        $this->linestyleCap      = $gvars['linestyleCap'];
        $this->linestyleJoin     = $gvars['linestyleJoin'];
        $this->linestyleDash     = $gvars['linestyleDash'];
        $this->textrendermode    = $gvars['textrendermode'];
        $this->textstrokewidth   = $gvars['textstrokewidth'];
        $this->DrawColor         = $gvars['DrawColor'];
        $this->FillColor         = $gvars['FillColor'];
        $this->TextColor         = $gvars['TextColor'];
        $this->ColorFlag         = $gvars['ColorFlag'];
        $this->bgcolor           = $gvars['bgcolor'];
        $this->fgcolor           = $gvars['fgcolor'];
        $this->htmlvspace        = $gvars['htmlvspace'];
        $this->listindent        = $gvars['listindent'];
        $this->listindentlevel   = $gvars['listindentlevel'];
        $this->listnum           = $gvars['listnum'];
        $this->listordered       = $gvars['listordered'];
        $this->listcount         = $gvars['listcount'];
        $this->lispacer          = $gvars['lispacer'];
        $this->cell_height_ratio = $gvars['cell_height_ratio'];
        $this->font_stretching   = $gvars['font_stretching'];
        $this->font_spacing      = $gvars['font_spacing'];
        if ($extended) {
            $this->lasth            = $gvars['lasth'];
            $this->tMargin          = $gvars['tMargin'];
            $this->bMargin          = $gvars['bMargin'];
            $this->AutoPageBreak    = $gvars['AutoPageBreak'];
            $this->PageBreakTrigger = $gvars['PageBreakTrigger'];
            $this->x                = $gvars['x'];
            $this->y                = $gvars['y'];
            $this->w                = $gvars['w'];
            $this->h                = $gvars['h'];
            $this->wPt              = $gvars['wPt'];
            $this->hPt              = $gvars['hPt'];
            $this->fwPt             = $gvars['fwPt'];
            $this->fhPt             = $gvars['fhPt'];
            $this->page             = $gvars['page'];
            $this->current_column   = $gvars['current_column'];
            $this->num_columns      = $gvars['num_columns'];
        }
        $this->_out('' . $this->linestyleWidth . ' ' . $this->linestyleCap . ' ' . $this->linestyleJoin . ' ' . $this->linestyleDash . ' ' . $this->DrawColor . ' ' . $this->FillColor . '');
        if (!$this->empty_string($this->FontFamily)) {
            $this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
        }
    }
    protected function getObjFilename($name)
    {
        return tempnam(K_PATH_CACHE, $name . '_');
    }
    protected function writeDiskCache($filename, $data, $append = false)
    {
        if ($append) {
            $fmode = 'ab+';
        } else {
            $fmode = 'wb+';
        }
        $f = @fopen($filename, $fmode);
        if (!$f) {
            $this->Error('Unable to write cache file: ' . $filename);
        } else {
            fwrite($f, $data);
            fclose($f);
        }
        if (!isset($this->cache_file_length['_' . $filename])) {
            $this->cache_file_length['_' . $filename] = strlen($data);
        } else {
            $this->cache_file_length['_' . $filename] += strlen($data);
        }
    }
    protected function readDiskCache($filename)
    {
        return file_get_contents($filename);
    }
    protected function setBuffer($data)
    {
        $this->bufferlen += strlen($data);
        if ($this->diskcache) {
            if (!isset($this->buffer) OR $this->empty_string($this->buffer)) {
                $this->buffer = $this->getObjFilename('buffer');
            }
            $this->writeDiskCache($this->buffer, $data, true);
        } else {
            $this->buffer .= $data;
        }
    }
    protected function replaceBuffer($data)
    {
        $this->bufferlen = strlen($data);
        if ($this->diskcache) {
            if (!isset($this->buffer) OR $this->empty_string($this->buffer)) {
                $this->buffer = $this->getObjFilename('buffer');
            }
            $this->writeDiskCache($this->buffer, $data, false);
        } else {
            $this->buffer = $data;
        }
    }
    protected function getBuffer()
    {
        if ($this->diskcache) {
            return $this->readDiskCache($this->buffer);
        } else {
            return $this->buffer;
        }
    }
    protected function setPageBuffer($page, $data, $append = false)
    {
        if ($this->diskcache) {
            if (!isset($this->pages[$page])) {
                $this->pages[$page] = $this->getObjFilename('page' . $page);
            }
            $this->writeDiskCache($this->pages[$page], $data, $append);
        } else {
            if ($append) {
                $this->pages[$page] .= $data;
            } else {
                $this->pages[$page] = $data;
            }
        }
        if ($append AND isset($this->pagelen[$page])) {
            $this->pagelen[$page] += strlen($data);
        } else {
            $this->pagelen[$page] = strlen($data);
        }
    }
    protected function getPageBuffer($page)
    {
        if ($this->diskcache) {
            return $this->readDiskCache($this->pages[$page]);
        } elseif (isset($this->pages[$page])) {
            return $this->pages[$page];
        }
        return false;
    }
    protected function setImageBuffer($image, $data)
    {
        if ($this->diskcache) {
            if (!isset($this->images[$image])) {
                $this->images[$image] = $this->getObjFilename('image' . $image);
            }
            $this->writeDiskCache($this->images[$image], serialize($data));
        } else {
            $this->images[$image] = $data;
        }
        if (!in_array($image, $this->imagekeys)) {
            $this->imagekeys[] = $image;
            ++$this->numimages;
        }
    }
    protected function setImageSubBuffer($image, $key, $data)
    {
        if (!isset($this->images[$image])) {
            $this->setImageBuffer($image, array());
        }
        if ($this->diskcache) {
            $tmpimg       = $this->getImageBuffer($image);
            $tmpimg[$key] = $data;
            $this->writeDiskCache($this->images[$image], serialize($tmpimg));
        } else {
            $this->images[$image][$key] = $data;
        }
    }
    protected function getImageBuffer($image)
    {
        if ($this->diskcache AND isset($this->images[$image])) {
            return unserialize($this->readDiskCache($this->images[$image]));
        } elseif (isset($this->images[$image])) {
            return $this->images[$image];
        }
        return false;
    }
    protected function setFontBuffer($font, $data)
    {
        if ($this->diskcache) {
            if (!isset($this->fonts[$font])) {
                $this->fonts[$font] = $this->getObjFilename('font');
            }
            $this->writeDiskCache($this->fonts[$font], serialize($data));
        } else {
            $this->fonts[$font] = $data;
        }
        if (!in_array($font, $this->fontkeys)) {
            $this->fontkeys[] = $font;
            ++$this->n;
            $this->font_obj_ids[$font] = $this->n;
            $this->setFontSubBuffer($font, 'n', $this->n);
        }
    }
    protected function setFontSubBuffer($font, $key, $data)
    {
        if (!isset($this->fonts[$font])) {
            $this->setFontBuffer($font, array());
        }
        if ($this->diskcache) {
            $tmpfont       = $this->getFontBuffer($font);
            $tmpfont[$key] = $data;
            $this->writeDiskCache($this->fonts[$font], serialize($tmpfont));
        } else {
            $this->fonts[$font][$key] = $data;
        }
    }
    protected function getFontBuffer($font)
    {
        if ($this->diskcache AND isset($this->fonts[$font])) {
            return unserialize($this->readDiskCache($this->fonts[$font]));
        } elseif (isset($this->fonts[$font])) {
            return $this->fonts[$font];
        }
        return false;
    }
    public function movePage($frompage, $topage)
    {
        if (($frompage > $this->numpages) OR ($frompage <= $topage)) {
            return false;
        }
        if ($frompage == $this->page) {
            $this->endPage();
        }
        $tmppage      = $this->pages[$frompage];
        $tmppagedim   = $this->pagedim[$frompage];
        $tmppagelen   = $this->pagelen[$frompage];
        $tmpintmrk    = $this->intmrk[$frompage];
        $tmpbordermrk = $this->bordermrk[$frompage];
        $tmpcntmrk    = $this->cntmrk[$frompage];
        if (isset($this->footerpos[$frompage])) {
            $tmpfooterpos = $this->footerpos[$frompage];
        }
        if (isset($this->footerlen[$frompage])) {
            $tmpfooterlen = $this->footerlen[$frompage];
        }
        if (isset($this->transfmrk[$frompage])) {
            $tmptransfmrk = $this->transfmrk[$frompage];
        }
        if (isset($this->PageAnnots[$frompage])) {
            $tmpannots = $this->PageAnnots[$frompage];
        }
        if (isset($this->newpagegroup) AND !empty($this->newpagegroup)) {
            for ($i = $frompage; $i > $topage; --$i) {
                if (isset($this->newpagegroup[$i]) AND (($i + $this->pagegroups[$this->newpagegroup[$i]]) > $frompage)) {
                    --$this->pagegroups[$this->newpagegroup[$i]];
                    break;
                }
            }
            for ($i = $topage; $i > 0; --$i) {
                if (isset($this->newpagegroup[$i]) AND (($i + $this->pagegroups[$this->newpagegroup[$i]]) > $topage)) {
                    ++$this->pagegroups[$this->newpagegroup[$i]];
                    break;
                }
            }
        }
        for ($i = $frompage; $i > $topage; --$i) {
            $j                   = $i - 1;
            $this->pages[$i]     = $this->pages[$j];
            $this->pagedim[$i]   = $this->pagedim[$j];
            $this->pagelen[$i]   = $this->pagelen[$j];
            $this->intmrk[$i]    = $this->intmrk[$j];
            $this->bordermrk[$i] = $this->bordermrk[$j];
            $this->cntmrk[$i]    = $this->cntmrk[$j];
            if (isset($this->footerpos[$j])) {
                $this->footerpos[$i] = $this->footerpos[$j];
            } elseif (isset($this->footerpos[$i])) {
                unset($this->footerpos[$i]);
            }
            if (isset($this->footerlen[$j])) {
                $this->footerlen[$i] = $this->footerlen[$j];
            } elseif (isset($this->footerlen[$i])) {
                unset($this->footerlen[$i]);
            }
            if (isset($this->transfmrk[$j])) {
                $this->transfmrk[$i] = $this->transfmrk[$j];
            } elseif (isset($this->transfmrk[$i])) {
                unset($this->transfmrk[$i]);
            }
            if (isset($this->PageAnnots[$j])) {
                $this->PageAnnots[$i] = $this->PageAnnots[$j];
            } elseif (isset($this->PageAnnots[$i])) {
                unset($this->PageAnnots[$i]);
            }
            if (isset($this->newpagegroup[$j])) {
                $this->newpagegroup[$i] = $this->newpagegroup[$j];
                unset($this->newpagegroup[$j]);
            }
            if ($this->currpagegroup == $j) {
                $this->currpagegroup = $i;
            }
        }
        $this->pages[$topage]     = $tmppage;
        $this->pagedim[$topage]   = $tmppagedim;
        $this->pagelen[$topage]   = $tmppagelen;
        $this->intmrk[$topage]    = $tmpintmrk;
        $this->bordermrk[$topage] = $tmpbordermrk;
        $this->cntmrk[$topage]    = $tmpcntmrk;
        if (isset($tmpfooterpos)) {
            $this->footerpos[$topage] = $tmpfooterpos;
        } elseif (isset($this->footerpos[$topage])) {
            unset($this->footerpos[$topage]);
        }
        if (isset($tmpfooterlen)) {
            $this->footerlen[$topage] = $tmpfooterlen;
        } elseif (isset($this->footerlen[$topage])) {
            unset($this->footerlen[$topage]);
        }
        if (isset($tmptransfmrk)) {
            $this->transfmrk[$topage] = $tmptransfmrk;
        } elseif (isset($this->transfmrk[$topage])) {
            unset($this->transfmrk[$topage]);
        }
        if (isset($tmpannots)) {
            $this->PageAnnots[$topage] = $tmpannots;
        } elseif (isset($this->PageAnnots[$topage])) {
            unset($this->PageAnnots[$topage]);
        }
        $tmpoutlines = $this->outlines;
        foreach ($tmpoutlines as $key => $outline) {
            if (($outline['p'] >= $topage) AND ($outline['p'] < $frompage)) {
                $this->outlines[$key]['p'] = $outline['p'] + 1;
            } elseif ($outline['p'] == $frompage) {
                $this->outlines[$key]['p'] = $topage;
            }
        }
        $tmpdests = $this->dests;
        foreach ($tmpdests as $key => $dest) {
            if (($dest['p'] >= $topage) AND ($dest['p'] < $frompage)) {
                $this->dests[$key]['p'] = $dest['p'] + 1;
            } elseif ($dest['p'] == $frompage) {
                $this->dests[$key]['p'] = $topage;
            }
        }
        $tmplinks = $this->links;
        foreach ($tmplinks as $key => $link) {
            if (($link[0] >= $topage) AND ($link[0] < $frompage)) {
                $this->links[$key][0] = $link[0] + 1;
            } elseif ($link[0] == $frompage) {
                $this->links[$key][0] = $topage;
            }
        }
        $tmpjavascript = $this->javascript;
        global $jfrompage, $jtopage;
        $jfrompage        = $frompage;
        $jtopage          = $topage;
        $this->javascript = preg_replace_callback('/this\.addField\(\'([^\']*)\',\'([^\']*)\',([0-9]+)/', create_function('$matches', 'global $jfrompage, $jtopage;
			$pagenum = intval($matches[3]) + 1;
			if (($pagenum >= $jtopage) AND ($pagenum < $jfrompage)) {
				$newpage = ($pagenum + 1);
			} elseif ($pagenum == $jfrompage) {
				$newpage = $jtopage;
			} else {
				$newpage = $pagenum;
			}
			--$newpage;
			return "this.addField(\'".$matches[1]."\',\'".$matches[2]."\',".$newpage."";'), $tmpjavascript);
        $this->lastPage(true);
        return true;
    }
    public function deletePage($page)
    {
        if (($page < 1) OR ($page > $this->numpages)) {
            return false;
        }
        unset($this->pages[$page]);
        unset($this->pagedim[$page]);
        unset($this->pagelen[$page]);
        unset($this->intmrk[$page]);
        unset($this->bordermrk[$page]);
        unset($this->cntmrk[$page]);
        if (isset($this->footerpos[$page])) {
            unset($this->footerpos[$page]);
        }
        if (isset($this->footerlen[$page])) {
            unset($this->footerlen[$page]);
        }
        if (isset($this->transfmrk[$page])) {
            unset($this->transfmrk[$page]);
        }
        if (isset($this->PageAnnots[$page])) {
            unset($this->PageAnnots[$page]);
        }
        if (isset($this->newpagegroup) AND !empty($this->newpagegroup)) {
            for ($i = $page; $i > 0; --$i) {
                if (isset($this->newpagegroup[$i]) AND (($i + $this->pagegroups[$this->newpagegroup[$i]]) > $page)) {
                    --$this->pagegroups[$this->newpagegroup[$i]];
                    break;
                }
            }
        }
        if (isset($this->pageopen[$page])) {
            unset($this->pageopen[$page]);
        }
        if ($page < $this->numpages) {
            for ($i = $page; $i < $this->numpages; ++$i) {
                $j                   = $i + 1;
                $this->pages[$i]     = $this->pages[$j];
                $this->pagedim[$i]   = $this->pagedim[$j];
                $this->pagelen[$i]   = $this->pagelen[$j];
                $this->intmrk[$i]    = $this->intmrk[$j];
                $this->bordermrk[$i] = $this->bordermrk[$j];
                $this->cntmrk[$i]    = $this->cntmrk[$j];
                if (isset($this->footerpos[$j])) {
                    $this->footerpos[$i] = $this->footerpos[$j];
                } elseif (isset($this->footerpos[$i])) {
                    unset($this->footerpos[$i]);
                }
                if (isset($this->footerlen[$j])) {
                    $this->footerlen[$i] = $this->footerlen[$j];
                } elseif (isset($this->footerlen[$i])) {
                    unset($this->footerlen[$i]);
                }
                if (isset($this->transfmrk[$j])) {
                    $this->transfmrk[$i] = $this->transfmrk[$j];
                } elseif (isset($this->transfmrk[$i])) {
                    unset($this->transfmrk[$i]);
                }
                if (isset($this->PageAnnots[$j])) {
                    $this->PageAnnots[$i] = $this->PageAnnots[$j];
                } elseif (isset($this->PageAnnots[$i])) {
                    unset($this->PageAnnots[$i]);
                }
                if (isset($this->newpagegroup[$j])) {
                    $this->newpagegroup[$i] = $this->newpagegroup[$j];
                    unset($this->newpagegroup[$j]);
                }
                if ($this->currpagegroup == $j) {
                    $this->currpagegroup = $i;
                }
                if (isset($this->pageopen[$j])) {
                    $this->pageopen[$i] = $this->pageopen[$j];
                } elseif (isset($this->pageopen[$i])) {
                    unset($this->pageopen[$i]);
                }
            }
            unset($this->pages[$this->numpages]);
            unset($this->pagedim[$this->numpages]);
            unset($this->pagelen[$this->numpages]);
            unset($this->intmrk[$this->numpages]);
            unset($this->bordermrk[$this->numpages]);
            unset($this->cntmrk[$this->numpages]);
            if (isset($this->footerpos[$this->numpages])) {
                unset($this->footerpos[$this->numpages]);
            }
            if (isset($this->footerlen[$this->numpages])) {
                unset($this->footerlen[$this->numpages]);
            }
            if (isset($this->transfmrk[$this->numpages])) {
                unset($this->transfmrk[$this->numpages]);
            }
            if (isset($this->PageAnnots[$this->numpages])) {
                unset($this->PageAnnots[$this->numpages]);
            }
            if (isset($this->newpagegroup[$this->numpages])) {
                unset($this->newpagegroup[$this->numpages]);
            }
            if ($this->currpagegroup == $this->numpages) {
                $this->currpagegroup = ($this->numpages - 1);
            }
            if (isset($this->pagegroups[$this->numpages])) {
                unset($this->pagegroups[$this->numpages]);
            }
            if (isset($this->pageopen[$this->numpages])) {
                unset($this->pageopen[$this->numpages]);
            }
        }
        --$this->numpages;
        $this->page  = $this->numpages;
        $tmpoutlines = $this->outlines;
        foreach ($tmpoutlines as $key => $outline) {
            if ($outline['p'] > $page) {
                $this->outlines[$key]['p'] = $outline['p'] - 1;
            } elseif ($outline['p'] == $page) {
                unset($this->outlines[$key]);
            }
        }
        $tmpdests = $this->dests;
        foreach ($tmpdests as $key => $dest) {
            if ($dest['p'] > $page) {
                $this->dests[$key]['p'] = $dest['p'] - 1;
            } elseif ($dest['p'] == $page) {
                unset($this->dests[$key]);
            }
        }
        $tmplinks = $this->links;
        foreach ($tmplinks as $key => $link) {
            if ($link[0] > $page) {
                $this->links[$key][0] = $link[0] - 1;
            } elseif ($link[0] == $page) {
                unset($this->links[$key]);
            }
        }
        $tmpjavascript = $this->javascript;
        global $jpage;
        $jpage            = $page;
        $this->javascript = preg_replace_callback('/this\.addField\(\'([^\']*)\',\'([^\']*)\',([0-9]+)/', create_function('$matches', 'global $jpage;
			$pagenum = intval($matches[3]) + 1;
			if ($pagenum >= $jpage) {
				$newpage = ($pagenum - 1);
			} elseif ($pagenum == $jpage) {
				$newpage = 1;
			} else {
				$newpage = $pagenum;
			}
			--$newpage;
			return "this.addField(\'".$matches[1]."\',\'".$matches[2]."\',".$newpage."";'), $tmpjavascript);
        $this->lastPage(true);
        return true;
    }
    public function copyPage($page = 0)
    {
        if ($page == 0) {
            $page = $this->page;
        }
        if (($page < 1) OR ($page > $this->numpages)) {
            return false;
        }
        $this->endPage();
        ++$this->numpages;
        $this->page                   = $this->numpages;
        $this->pages[$this->page]     = $this->pages[$page];
        $this->pagedim[$this->page]   = $this->pagedim[$page];
        $this->pagelen[$this->page]   = $this->pagelen[$page];
        $this->intmrk[$this->page]    = $this->intmrk[$page];
        $this->bordermrk[$this->page] = $this->bordermrk[$page];
        $this->cntmrk[$this->page]    = $this->cntmrk[$page];
        $this->pageopen[$this->page]  = false;
        if (isset($this->footerpos[$page])) {
            $this->footerpos[$this->page] = $this->footerpos[$page];
        }
        if (isset($this->footerlen[$page])) {
            $this->footerlen[$this->page] = $this->footerlen[$page];
        }
        if (isset($this->transfmrk[$page])) {
            $this->transfmrk[$this->page] = $this->transfmrk[$page];
        }
        if (isset($this->PageAnnots[$page])) {
            $this->PageAnnots[$this->page] = $this->PageAnnots[$page];
        }
        if (isset($this->newpagegroup[$page])) {
            $this->newpagegroup[$this->page]        = sizeof($this->newpagegroup) + 1;
            $this->currpagegroup                    = $this->newpagegroup[$this->page];
            $this->pagegroups[$this->currpagegroup] = 1;
        } elseif (isset($this->currpagegroup) AND ($this->currpagegroup > 0)) {
            ++$this->pagegroups[$this->currpagegroup];
        }
        $tmpoutlines = $this->outlines;
        foreach ($tmpoutlines as $key => $outline) {
            if ($outline['p'] == $page) {
                $this->outlines[] = array(
                    't' => $outline['t'],
                    'l' => $outline['l'],
                    'y' => $outline['y'],
                    'p' => $this->page,
                    's' => $outline['s'],
                    'c' => $outline['c']
                );
            }
        }
        $tmplinks = $this->links;
        foreach ($tmplinks as $key => $link) {
            if ($link[0] == $page) {
                $this->links[] = array(
                    $this->page,
                    $link[1]
                );
            }
        }
        $this->lastPage(true);
        return true;
    }
    public function addTOC($page = '', $numbersfont = '', $filler = '.', $toc_name = 'TOC', $style = '', $color = array(0, 0, 0))
    {
        $fontsize       = $this->FontSizePt;
        $fontfamily     = $this->FontFamily;
        $fontstyle      = $this->FontStyle;
        $w              = $this->w - $this->lMargin - $this->rMargin;
        $spacer         = $this->GetStringWidth(chr(32)) * 4;
        $page_first     = $this->getPage();
        $lmargin        = $this->lMargin;
        $rmargin        = $this->rMargin;
        $x_start        = $this->GetX();
        $current_page   = $this->page;
        $current_column = $this->current_column;
        if ($this->empty_string($numbersfont)) {
            $numbersfont = $this->default_monospaced_font;
        }
        if ($this->empty_string($filler)) {
            $filler = ' ';
        }
        if ($this->empty_string($page)) {
            $gap = ' ';
        } else {
            $gap = '';
            if ($page < 1) {
                $page = 1;
            }
        }
        $this->SetFont($numbersfont, $fontstyle, $fontsize);
        $numwidth = $this->GetStringWidth('00000');
        foreach ($this->outlines as $key => $outline) {
            if ($this->rtl) {
                $aligntext = 'R';
                $alignnum  = 'L';
            } else {
                $aligntext = 'L';
                $alignnum  = 'R';
            }
            if ($outline['l'] == 0) {
                $this->SetFont($fontfamily, $fontstyle . 'B', $fontsize);
            } else {
                $this->SetFont($fontfamily, $fontstyle, $fontsize - $outline['l']);
            }
            $this->checkPageBreak((2 * $this->FontSize * $this->cell_height_ratio));
            if (($this->page == $current_page) AND ($this->current_column == $current_column)) {
                $this->lMargin = $lmargin;
                $this->rMargin = $rmargin;
            } else {
                if ($this->current_column != $current_column) {
                    if ($this->rtl) {
                        $x_start = $this->w - $this->columns[$this->current_column]['x'];
                    } else {
                        $x_start = $this->columns[$this->current_column]['x'];
                    }
                }
                $lmargin        = $this->lMargin;
                $rmargin        = $this->rMargin;
                $current_page   = $this->page;
                $current_column = $this->current_column;
            }
            $this->SetX($x_start);
            $indent = ($spacer * $outline['l']);
            if ($this->rtl) {
                $this->x -= $indent;
                $this->rMargin = $this->w - $this->x;
            } else {
                $this->x += $indent;
                $this->lMargin = $this->x;
            }
            $link = $this->AddLink();
            $this->SetLink($link, $outline['y'], $outline['p']);
            if ($this->rtl) {
                $txt = ' ' . $outline['t'];
            } else {
                $txt = $outline['t'] . ' ';
            }
            $this->Write(0, $txt, $link, false, $aligntext, false, 0, false, false, 0, $numwidth, '');
            if ($this->rtl) {
                $tw = $this->x - $this->lMargin;
            } else {
                $tw = $this->w - $this->rMargin - $this->x;
            }
            $this->SetFont($numbersfont, $fontstyle, $fontsize);
            if ($this->empty_string($page)) {
                $pagenum = $outline['p'];
            } else {
                $pagenum = '{#' . ($outline['p']) . '}';
                if ($this->isUnicodeFont()) {
                    $pagenum = '{' . $pagenum . '}';
                }
            }
            $fw       = ($tw - $this->GetStringWidth($pagenum . $filler));
            $numfills = floor($fw / $this->GetStringWidth($filler));
            if ($numfills > 0) {
                $rowfill = str_repeat($filler, $numfills);
            } else {
                $rowfill = '';
            }
            if ($this->rtl) {
                $pagenum = $pagenum . $gap . $rowfill;
            } else {
                $pagenum = $rowfill . $gap . $pagenum;
            }
            $this->Cell($tw, 0, $pagenum, 0, 1, $alignnum, 0, $link, 0);
        }
        $page_last = $this->getPage();
        $numpages  = $page_last - $page_first + 1;
        if (!$this->empty_string($page)) {
            for ($p = $page_first; $p <= $page_last; ++$p) {
                $temppage = $this->getPageBuffer($p);
                for ($n = 1; $n <= $this->numpages; ++$n) {
                    $a       = '{#' . $n . '}';
                    $pnalias = $this->getInternalPageNumberAliases($a);
                    if ($n >= $page) {
                        $np = $n + $numpages;
                    } else {
                        $np = $n;
                    }
                    $na = $this->formatTOCPageNumber(($this->starting_page_number + $np - 1));
                    $nu = $this->UTF8ToUTF16BE($na, false);
                    foreach ($pnalias['u'] as $u) {
                        $sfill = str_repeat($filler, max(0, (strlen($u) - strlen($nu . ' '))));
                        if ($this->rtl) {
                            $nr = $nu . $this->UTF8ToUTF16BE(' ' . $sfill);
                        } else {
                            $nr = $this->UTF8ToUTF16BE($sfill . ' ') . $nu;
                        }
                        $temppage = str_replace($u, $nr, $temppage);
                    }
                    foreach ($pnalias['a'] as $a) {
                        $sfill = str_repeat($filler, max(0, (strlen($a) - strlen($na . ' '))));
                        if ($this->rtl) {
                            $nr = $na . ' ' . $sfill;
                        } else {
                            $nr = $sfill . ' ' . $na;
                        }
                        $temppage = str_replace($a, $nr, $temppage);
                    }
                }
                $this->setPageBuffer($p, $temppage);
            }
            $this->Bookmark($toc_name, 0, 0, $page_first, $style, $color);
            for ($i = 0; $i < $numpages; ++$i) {
                $this->movePage($page_last, $page);
            }
        }
    }
    public function addHTMLTOC($page = '', $toc_name = 'TOC', $templates = array(), $correct_align = true, $style = '', $color = array(0, 0, 0))
    {
        $filler                   = ' ';
        $prev_htmlLinkColorArray  = $this->htmlLinkColorArray;
        $prev_htmlLinkFontStyle   = $this->htmlLinkFontStyle;
        $this->htmlLinkColorArray = array();
        $this->htmlLinkFontStyle  = '';
        $page_first               = $this->getPage();
        $current_font             = $this->FontFamily;
        foreach ($templates as $level => $html) {
            $dom = $this->getHtmlDomArray($html);
            foreach ($dom as $key => $value) {
                if ($value['value'] == '#TOC_PAGE_NUMBER#') {
                    $this->SetFont($dom[($key - 1)]['fontname']);
                    $templates['F' . $level] = $this->isUnicodeFont();
                }
            }
        }
        $this->SetFont($current_font);
        foreach ($this->outlines as $key => $outline) {
            $row = $templates[$outline['l']];
            if ($this->empty_string($page)) {
                $pagenum = $outline['p'];
            } else {
                $pagenum = '{#' . ($outline['p']) . '}';
                if ($templates['F' . $outline['l']]) {
                    $pagenum = '{' . $pagenum . '}';
                }
            }
            $row = str_replace('#TOC_DESCRIPTION#', $outline['t'], $row);
            $row = str_replace('#TOC_PAGE_NUMBER#', $pagenum, $row);
            $row = '<a href="#' . $outline['p'] . ',' . $outline['y'] . '">' . $row . '</a>';
            $this->writeHTML($row, false, false, true, false, '');
        }
        $this->htmlLinkColorArray = $prev_htmlLinkColorArray;
        $this->htmlLinkFontStyle  = $prev_htmlLinkFontStyle;
        $page_last                = $this->getPage();
        $numpages                 = $page_last - $page_first + 1;
        if (!$this->empty_string($page)) {
            for ($p = $page_first; $p <= $page_last; ++$p) {
                $temppage = $this->getPageBuffer($p);
                for ($n = 1; $n <= $this->numpages; ++$n) {
                    $a       = '{#' . $n . '}';
                    $pnalias = $this->getInternalPageNumberAliases($a);
                    if ($n >= $page) {
                        $np = $n + $numpages;
                    } else {
                        $np = $n;
                    }
                    $na = $this->formatTOCPageNumber(($this->starting_page_number + $np - 1));
                    $nu = $this->UTF8ToUTF16BE($na, false);
                    foreach ($pnalias['u'] as $u) {
                        if ($correct_align) {
                            $sfill = str_repeat($filler, (strlen($u) - strlen($nu . ' ')));
                            if ($this->rtl) {
                                $nr = $nu . $this->UTF8ToUTF16BE(' ' . $sfill);
                            } else {
                                $nr = $this->UTF8ToUTF16BE($sfill . ' ') . $nu;
                            }
                        } else {
                            $nr = $nu;
                        }
                        $temppage = str_replace($u, $nr, $temppage);
                    }
                    foreach ($pnalias['a'] as $a) {
                        if ($correct_align) {
                            $sfill = str_repeat($filler, (strlen($a) - strlen($na . ' ')));
                            if ($this->rtl) {
                                $nr = $na . ' ' . $sfill;
                            } else {
                                $nr = $sfill . ' ' . $na;
                            }
                        } else {
                            $nr = $na;
                        }
                        $temppage = str_replace($a, $nr, $temppage);
                    }
                }
                $this->setPageBuffer($p, $temppage);
            }
            $this->Bookmark($toc_name, 0, 0, $page_first, $style, $color);
            for ($i = 0; $i < $numpages; ++$i) {
                $this->movePage($page_last, $page);
            }
        }
    }
    public function startTransaction()
    {
        if (isset($this->objcopy)) {
            $this->commitTransaction();
        }
        $this->start_transaction_page = $this->page;
        $this->start_transaction_y    = $this->y;
        $this->objcopy                = $this->objclone($this);
    }
    public function commitTransaction()
    {
        if (isset($this->objcopy)) {
            $this->objcopy->_destroy(true, true);
            unset($this->objcopy);
        }
    }
    public function rollbackTransaction($self = false)
    {
        if (isset($this->objcopy)) {
            if (isset($this->objcopy->diskcache) AND $this->objcopy->diskcache) {
                foreach ($this->objcopy->cache_file_length as $file => $length) {
                    $file   = substr($file, 1);
                    $handle = fopen($file, 'r+');
                    ftruncate($handle, $length);
                }
            }
            $this->_destroy(true, true);
            if ($self) {
                $objvars = get_object_vars($this->objcopy);
                foreach ($objvars as $key => $value) {
                    $this->$key = $value;
                }
            }
            return $this->objcopy;
        }
        return $this;
    }
    public function objclone($object)
    {
        return @clone ($object);
    }
    public function empty_string($str)
    {
        return (is_null($str) OR (is_string($str) AND (strlen($str) == 0)));
    }
    public function revstrpos($haystack, $needle, $offset = 0)
    {
        $length = strlen($haystack);
        $offset = ($offset > 0) ? ($length - $offset) : abs($offset);
        $pos    = strpos(strrev($haystack), strrev($needle), $offset);
        return ($pos === false) ? false : ($length - $pos - strlen($needle));
    }
    public function setEqualColumns($numcols = 0, $width = 0, $y = '')
    {
        $this->columns = array();
        if ($numcols < 2) {
            $numcols       = 0;
            $this->columns = array();
        } else {
            $maxwidth = ($this->w - $this->original_lMargin - $this->original_rMargin) / $numcols;
            if (($width == 0) OR ($width > $maxwidth)) {
                $width = $maxwidth;
            }
            if ($this->empty_string($y)) {
                $y = $this->y;
            }
            $space = (($this->w - $this->original_lMargin - $this->original_rMargin - ($numcols * $width)) / ($numcols - 1));
            for ($i = 0; $i < $numcols; ++$i) {
                $this->columns[$i] = array(
                    'w' => $width,
                    's' => $space,
                    'y' => $y
                );
            }
        }
        $this->num_columns       = $numcols;
        $this->current_column    = 0;
        $this->column_start_page = $this->page;
        $this->selectColumn(0);
    }
    public function resetColumns()
    {
        $this->lMargin = $this->original_lMargin;
        $this->rMargin = $this->original_rMargin;
        $this->setEqualColumns();
    }
    public function setColumnsArray($columns)
    {
        $this->columns           = $columns;
        $this->num_columns       = count($columns);
        $this->current_column    = 0;
        $this->column_start_page = $this->page;
        $this->selectColumn(0);
    }
    public function selectColumn($col = '')
    {
        if (is_string($col)) {
            $col = $this->current_column;
        } elseif ($col >= $this->num_columns) {
            $col = 0;
        }
        $xshift       = array(
            'x' => 0,
            's' => array(
                'H' => 0,
                'V' => 0
            ),
            'p' => array(
                'L' => 0,
                'T' => 0,
                'R' => 0,
                'B' => 0
            )
        );
        $enable_thead = false;
        if ($this->num_columns > 1) {
            if ($col != $this->current_column) {
                if ($this->column_start_page == $this->page) {
                    $this->y = $this->columns[$col]['y'];
                } else {
                    $this->y = $this->tMargin;
                }
                if (($this->page > $this->maxselcol['page']) OR (($this->page == $this->maxselcol['page']) AND ($col > $this->maxselcol['column']))) {
                    $enable_thead              = true;
                    $this->maxselcol['page']   = $this->page;
                    $this->maxselcol['column'] = $col;
                }
            }
            $xshift     = $this->colxshift;
            $listindent = ($this->listindentlevel * $this->listindent);
            $colpos     = ($col * ($this->columns[$col]['w'] + $this->columns[$col]['s']));
            if ($this->rtl) {
                $x             = $this->w - $this->original_rMargin - $colpos;
                $this->rMargin = ($this->w - $x + $listindent);
                $this->lMargin = ($x - $this->columns[$col]['w']);
                $this->x       = $x - $listindent;
            } else {
                $x             = $this->original_lMargin + $colpos;
                $this->lMargin = ($x + $listindent);
                $this->rMargin = ($this->w - $x - $this->columns[$col]['w']);
                $this->x       = $x + $listindent;
            }
            $this->columns[$col]['x'] = $x;
        }
        $this->current_column = $col;
        $this->newline        = true;
        if ((!$this->empty_string($this->thead)) AND (!$this->inthead)) {
            if ($enable_thead) {
                $this->writeHTML($this->thead, false, false, false, false, '');
                $this->y += $xshift['s']['V'];
                if (!isset($this->columns[$col]['th'])) {
                    $this->columns[$col]['th'] = array();
                }
                $this->columns[$col]['th']['\'' . $this->page . '\''] = $this->y;
                $this->lasth                                          = 0;
            } elseif (isset($this->columns[$col]['th']['\'' . $this->page . '\''])) {
                $this->y = $this->columns[$col]['th']['\'' . $this->page . '\''];
            }
        }
        if ($this->rtl) {
            $this->rMargin += $xshift['x'];
            $this->x -= ($xshift['x'] + $xshift['p']['R']);
        } else {
            $this->lMargin += $xshift['x'];
            $this->x += $xshift['x'] + $xshift['p']['L'];
        }
    }
    public function getColumn()
    {
        return $this->current_column;
    }
    public function getNumberOfColumns()
    {
        return $this->num_columns;
    }
    public function serializeTCPDFtagParameters($pararray)
    {
        return urlencode(serialize($pararray));
    }
    public function setTextRenderingMode($stroke = 0, $fill = true, $clip = false)
    {
        if ($stroke < 0) {
            $stroke = 0;
        }
        if ($fill === true) {
            if ($stroke > 0) {
                if ($clip === true) {
                    $textrendermode = 6;
                } else {
                    $textrendermode = 2;
                }
                $textstrokewidth = $stroke;
            } else {
                if ($clip === true) {
                    $textrendermode = 4;
                } else {
                    $textrendermode = 0;
                }
            }
        } else {
            if ($stroke > 0) {
                if ($clip === true) {
                    $textrendermode = 5;
                } else {
                    $textrendermode = 1;
                }
                $textstrokewidth = $stroke;
            } else {
                if ($clip === true) {
                    $textrendermode = 7;
                } else {
                    $textrendermode = 3;
                }
            }
        }
        $this->textrendermode  = $textrendermode;
        $this->textstrokewidth = $stroke * $this->k;
    }
    protected function hyphenateWord($word, $patterns, $dictionary = array(), $leftmin = 1, $rightmin = 2, $charmin = 1, $charmax = 8)
    {
        $hyphenword = array();
        $numchars   = count($word);
        if ($numchars <= $charmin) {
            return $word;
        }
        $word_string = $this->UTF8ArrSubString($word);
        $pattern     = '/^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
        if (preg_match($pattern, $word_string) > 0) {
            return $word;
        }
        $pattern = '/(([a-zA-Z0-9\-]+\.)?)((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
        if (preg_match($pattern, $word_string) > 0) {
            return $word;
        }
        if (isset($dictionary[$word_string])) {
            return $this->UTF8StringToArray($dictionary[$word_string]);
        }
        $tmpword     = array_merge(array(
            95
        ), $word, array(
            95
        ));
        $tmpnumchars = $numchars + 2;
        $maxpos      = $tmpnumchars - $charmin;
        for ($pos = 0; $pos < $maxpos; ++$pos) {
            $imax = min(($tmpnumchars - $pos), $charmax);
            for ($i = $charmin; $i <= $imax; ++$i) {
                $subword = strtolower($this->UTF8ArrSubString($tmpword, $pos, $pos + $i));
                if (isset($patterns[$subword])) {
                    $pattern        = $this->UTF8StringToArray($patterns[$subword]);
                    $pattern_length = count($pattern);
                    $digits         = 1;
                    for ($j = 0; $j < $pattern_length; ++$j) {
                        if (($pattern[$j] >= 48) AND ($pattern[$j] <= 57)) {
                            if ($j == 0) {
                                $zero = $pos - 1;
                            } else {
                                $zero = $pos + $j - $digits;
                            }
                            if (!isset($hyphenword[$zero]) OR ($hyphenword[$zero] != $pattern[$j])) {
                                $hyphenword[$zero] = $this->unichr($pattern[$j]);
                            }
                            ++$digits;
                        }
                    }
                }
            }
        }
        $inserted = 0;
        $maxpos   = $numchars - $rightmin;
        for ($i = $leftmin; $i <= $maxpos; ++$i) {
            if (isset($hyphenword[$i]) AND (($hyphenword[$i] % 2) != 0)) {
                array_splice($word, $i + $inserted, 0, 173);
                ++$inserted;
            }
        }
        return $word;
    }
    public function getHyphenPatternsFromTEX($file)
    {
        $data     = file_get_contents($file);
        $patterns = array();
        $data     = preg_replace('/\%[^\n]*/', '', $data);
        preg_match('/\\\\patterns\{([^\}]*)\}/i', $data, $matches);
        $data           = trim(substr($matches[0], 10, -1));
        $patterns_array = preg_split('/[\s]+/', $data);
        $patterns       = array();
        foreach ($patterns_array as $val) {
            if (!$this->empty_string($val)) {
                $val            = trim($val);
                $val            = str_replace('\'', '\\\'', $val);
                $key            = preg_replace('/[0-9]+/', '', $val);
                $patterns[$key] = $val;
            }
        }
        return $patterns;
    }
    public function hyphenateText($text, $patterns, $dictionary = array(), $leftmin = 1, $rightmin = 2, $charmin = 1, $charmax = 8)
    {
        $text   = $this->unhtmlentities($text);
        $word   = array();
        $txtarr = array();
        $intag  = false;
        if (!is_array($patterns)) {
            $patterns = $this->getHyphenPatternsFromTEX($patterns);
        }
        $unichars = $this->UTF8StringToArray($text);
        foreach ($unichars as $char) {
            if ((!$intag) AND $this->unicode->uni_type[$char] == 'L') {
                $word[] = $char;
            } else {
                if (!$this->empty_string($word)) {
                    $txtarr = array_merge($txtarr, $this->hyphenateWord($word, $patterns, $dictionary, $leftmin, $rightmin, $charmin, $charmax));
                    $word   = array();
                }
                $txtarr[] = $char;
                if (chr($char) == '<') {
                    $intag = true;
                } elseif ($intag AND (chr($char) == '>')) {
                    $intag = false;
                }
            }
        }
        if (!$this->empty_string($word)) {
            $txtarr = array_merge($txtarr, $this->hyphenateWord($word, $patterns, $dictionary, $leftmin, $rightmin, $charmin, $charmax));
        }
        return $this->UTF8ArrSubString($txtarr);
    }
    public function setRasterizeVectorImages($mode)
    {
        $this->rasterize_vector_images = $mode;
    }
    protected function getPathPaintOperator($style, $default = 'S')
    {
        $op = '';
        switch ($style) {
            case 'S':
            case 'D': {
                $op = 'S';
                break;
            }
            case 's':
            case 'd': {
                $op = 's';
                break;
            }
            case 'f':
            case 'F': {
                $op = 'f';
                break;
            }
            case 'f*':
            case 'F*': {
                $op = 'f*';
                break;
            }
            case 'B':
            case 'FD':
            case 'DF': {
                $op = 'B';
                break;
            }
            case 'B*':
            case 'F*D':
            case 'DF*': {
                $op = 'B*';
                break;
            }
            case 'b':
            case 'fd':
            case 'df': {
                $op = 'b';
                break;
            }
            case 'b*':
            case 'f*d':
            case 'df*': {
                $op = 'b*';
                break;
            }
            case 'CNZ': {
                $op = 'W n';
                break;
            }
            case 'CEO': {
                $op = 'W* n';
                break;
            }
            case 'n': {
                $op = 'n';
                break;
            }
            default: {
                if (!empty($default)) {
                    $op = $this->getPathPaintOperator($default, '');
                } else {
                    $op = '';
                }
            }
        }
        return $op;
    }
    public function setFontSubsetting($enable = true)
    {
        $this->font_subsetting = $enable ? true : false;
    }
    public function getFontSubsetting()
    {
        return $this->font_subsetting;
    }
    public function stringLeftTrim($str, $replace = '')
    {
        return preg_replace('/^' . $this->re_space['p'] . '+/' . $this->re_space['m'], $replace, $str);
    }
    public function stringRightTrim($str, $replace = '')
    {
        return preg_replace('/' . $this->re_space['p'] . '+$/' . $this->re_space['m'], $replace, $str);
    }
    public function stringTrim($str, $replace = '')
    {
        $str = $this->stringLeftTrim($str, $replace);
        $str = $this->stringRightTrim($str, $replace);
        return $str;
    }
    public function isUnicodeFont()
    {
        return (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0'));
    }
    public function getFontFamilyName($fontfamily)
    {
        $fontfamily = preg_replace('/[^a-z0-9\,]/', '', strtolower($fontfamily));
        $fontslist  = preg_split('/[,]/', $fontfamily);
        foreach ($fontslist as $font) {
            $font          = preg_replace('/italic$/', 'I', $font);
            $font          = preg_replace('/oblique$/', 'I', $font);
            $font          = preg_replace('/bold([I]?)$/', 'B\\1', $font);
            $pattern       = array();
            $replacement   = array();
            $pattern[]     = '/^serif|^cursive|^fantasy|^timesnewroman/';
            $replacement[] = 'times';
            $pattern[]     = '/^sansserif/';
            $replacement[] = 'helvetica';
            $pattern[]     = '/^monospace/';
            $replacement[] = 'courier';
            $font          = preg_replace($pattern, $replacement, $font);
            if (in_array(strtolower($font), $this->fontlist) OR in_array($font, $this->fontkeys)) {
                return $font;
            }
        }
        return $this->CurrentFont['fontkey'];
    }
    public function startTemplate($w = 0, $h = 0)
    {
        if ($this->inxobj) {
            return false;
        }
        $this->inxobj = true;
        ++$this->n;
        $this->xobjid                                 = 'XT' . $this->n;
        $this->xobjects[$this->xobjid]                = array(
            'n' => $this->n
        );
        $this->xobjects[$this->xobjid]['gvars']       = $this->getGraphicVars();
        $this->xobjects[$this->xobjid]['intmrk']      = 0;
        $this->xobjects[$this->xobjid]['transfmrk']   = array();
        $this->xobjects[$this->xobjid]['outdata']     = '';
        $this->xobjects[$this->xobjid]['xobjects']    = array();
        $this->xobjects[$this->xobjid]['images']      = array();
        $this->xobjects[$this->xobjid]['fonts']       = array();
        $this->xobjects[$this->xobjid]['annotations'] = array();
        $this->num_columns                            = 1;
        $this->current_column                         = 0;
        $this->SetAutoPageBreak(false);
        if (($w === '') OR ($w <= 0)) {
            $w = $this->w - $this->lMargin - $this->rMargin;
        }
        if (($h === '') OR ($h <= 0)) {
            $h = $this->h - $this->tMargin - $this->bMargin;
        }
        $this->xobjects[$this->xobjid]['x'] = 0;
        $this->xobjects[$this->xobjid]['y'] = 0;
        $this->xobjects[$this->xobjid]['w'] = $w;
        $this->xobjects[$this->xobjid]['h'] = $h;
        $this->w                            = $w;
        $this->h                            = $h;
        $this->wPt                          = $this->w * $this->k;
        $this->hPt                          = $this->h * $this->k;
        $this->fwPt                         = $this->wPt;
        $this->fhPt                         = $this->hPt;
        $this->x                            = 0;
        $this->y                            = 0;
        $this->lMargin                      = 0;
        $this->rMargin                      = 0;
        $this->tMargin                      = 0;
        $this->bMargin                      = 0;
        return $this->xobjid;
    }
    public function endTemplate()
    {
        if (!$this->inxobj) {
            return false;
        }
        $this->inxobj = false;
        $this->setGraphicVars($this->xobjects[$this->xobjid]['gvars'], true);
        return $this->xobjid;
    }
    public function printTemplate($id, $x = '', $y = '', $w = 0, $h = 0, $align = '', $palign = '', $fitonpage = false)
    {
        if (!isset($this->xobjects[$id])) {
            $this->Error('The XObject Template \'' . $id . '\' doesn\'t exist!');
        }
        if ($this->inxobj) {
            if ($id == $this->xobjid) {
                $this->endTemplate();
            } else {
                $this->xobjects[$this->xobjid]['xobjects'][$id] = $this->xobjects[$id];
            }
        }
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        $ow = $this->xobjects[$id]['w'];
        $oh = $this->xobjects[$id]['h'];
        if (($w <= 0) AND ($h <= 0)) {
            $w = $ow;
            $h = $oh;
        } elseif ($w <= 0) {
            $w = $h * $ow / $oh;
        } elseif ($h <= 0) {
            $h = $w * $oh / $ow;
        }
        list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, $fitonpage);
        $rb_y = $y + $h;
        if ($this->rtl) {
            if ($palign == 'L') {
                $xt = $this->lMargin;
            } elseif ($palign == 'C') {
                $xt = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $xt = $this->w - $this->rMargin - $w;
            } else {
                $xt = $x - $w;
            }
            $rb_x = $xt;
        } else {
            if ($palign == 'L') {
                $xt = $this->lMargin;
            } elseif ($palign == 'C') {
                $xt = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $xt = $this->w - $this->rMargin - $w;
            } else {
                $xt = $x;
            }
            $rb_x = $xt + $w;
        }
        $this->StartTransform();
        $sx    = ($w / $this->xobjects[$id]['w']);
        $sy    = ($h / $this->xobjects[$id]['h']);
        $tm    = array();
        $tm[0] = $sx;
        $tm[1] = 0;
        $tm[2] = 0;
        $tm[3] = $sy;
        $tm[4] = $xt * $this->k;
        $tm[5] = ($this->h - $h - $y) * $this->k;
        $this->Transform($tm);
        $this->_out('/' . $id . ' Do');
        $this->StopTransform();
        if (!empty($this->xobjects[$id]['annotations'])) {
            foreach ($this->xobjects[$id]['annotations'] as $annot) {
                $coordlt = $this->getTransformationMatrixProduct($tm, array(
                    1,
                    0,
                    0,
                    1,
                    ($annot['x'] * $this->k),
                    (-$annot['y'] * $this->k)
                ));
                $ax      = ($coordlt[4] / $this->k);
                $ay      = ($this->h - $h - ($coordlt[5] / $this->k));
                $coordrb = $this->getTransformationMatrixProduct($tm, array(
                    1,
                    0,
                    0,
                    1,
                    (($annot['x'] + $annot['w']) * $this->k),
                    ((-$annot['y'] - $annot['h']) * $this->k)
                ));
                $aw      = ($coordrb[4] / $this->k) - $ax;
                $ah      = ($this->h - $h - ($coordrb[5] / $this->k)) - $ay;
                $this->Annotation($ax, $ay, $aw, $ah, $annot['text'], $annot['opt'], $annot['spaces']);
            }
        }
        switch ($align) {
            case 'T': {
                $this->y = $y;
                $this->x = $rb_x;
                break;
            }
            case 'M': {
                $this->y = $y + round($h / 2);
                $this->x = $rb_x;
                break;
            }
            case 'B': {
                $this->y = $rb_y;
                $this->x = $rb_x;
                break;
            }
            case 'N': {
                $this->SetY($rb_y);
                break;
            }
            default: {
                break;
            }
        }
    }
    public function setFontStretching($perc = 100)
    {
        $this->font_stretching = $perc;
    }
    public function getFontStretching()
    {
        return $this->font_stretching;
    }
    public function setFontSpacing($spacing = 0)
    {
        $this->font_spacing = $spacing;
    }
    public function getFontSpacing()
    {
        return $this->font_spacing;
    }
    public function getPageRegions()
    {
        return $this->page_regions;
    }
    public function setPageRegions($regions = array())
    {
        $this->page_regions = array();
        foreach ($regions as $data) {
            $this->addPageRegion($data);
        }
    }
    public function addPageRegion($region)
    {
        if (!isset($region['page']) OR empty($region['page'])) {
            $region['page'] = $this->page;
        }
        if (isset($region['xt']) AND isset($region['xb']) AND ($region['xt'] > 0) AND ($region['xb'] > 0) AND isset($region['yt']) AND isset($region['yb']) AND ($region['yt'] >= 0) AND ($region['yt'] < $region['yb']) AND isset($region['side']) AND (($region['side'] == 'L') OR ($region['side'] == 'R'))) {
            $this->page_regions[] = $region;
        }
    }
    public function removePageRegion($key)
    {
        if (isset($this->page_regions[$key])) {
            unset($this->page_regions[$key]);
        }
    }
    protected function checkPageRegions($h, $x, $y)
    {
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        if (empty($this->page_regions)) {
            return array(
                $x,
                $y
            );
        }
        if (empty($h)) {
            $h = ($this->FontSize * $this->cell_height_ratio) + $this->cell_padding['T'] + $this->cell_padding['B'];
        }
        if ($this->checkPageBreak($h, $y)) {
            $x = $this->x;
            $y = $this->y;
        }
        if ($this->num_columns > 1) {
            if ($this->rtl) {
                $this->lMargin = $this->columns[$this->current_column]['x'] - $this->columns[$this->current_column]['w'];
            } else {
                $this->rMargin = $this->w - $this->columns[$this->current_column]['x'] - $this->columns[$this->current_column]['w'];
            }
        } else {
            if ($this->rtl) {
                $this->lMargin = $this->original_lMargin;
            } else {
                $this->rMargin = $this->original_rMargin;
            }
        }
        foreach ($this->page_regions as $regid => $regdata) {
            if ($regdata['page'] == $this->page) {
                if (($y > ($regdata['yt'] - $h)) AND ($y <= $regdata['yb'])) {
                    $minv = ($regdata['xb'] - $regdata['xt']) / ($regdata['yb'] - $regdata['yt']);
                    $yt   = max($y, $regdata['yt']);
                    $yb   = min(($yt + $h), $regdata['yb']);
                    $xt   = (($yt - $regdata['yt']) * $minv) + $regdata['xt'];
                    $xb   = (($yb - $regdata['yt']) * $minv) + $regdata['xt'];
                    if ($regdata['side'] == 'L') {
                        $new_margin = max($xt, $xb);
                        if ($this->lMargin < $new_margin) {
                            if ($this->rtl) {
                                $this->lMargin = $new_margin;
                            }
                            if ($x < $new_margin) {
                                $x = $new_margin;
                                if ($new_margin > ($this->w - $this->rMargin)) {
                                    $y = $regdata['yb'] - $h;
                                }
                            }
                        }
                    } elseif ($regdata['side'] == 'R') {
                        $new_margin = min($xt, $xb);
                        if (($this->w - $this->rMargin) > $new_margin) {
                            if (!$this->rtl) {
                                $this->rMargin = ($this->w - $new_margin);
                            }
                            if ($x > $new_margin) {
                                $x = $new_margin;
                                if ($new_margin > $this->lMargin) {
                                    $y = $regdata['yb'] - $h;
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            $x,
            $y
        );
    }
    public function ImageSVG($file, $x = '', $y = '', $w = 0, $h = 0, $link = '', $align = '', $palign = '', $border = 0, $fitonpage = false)
    {
        if ($this->rasterize_vector_images AND ($w > 0) AND ($h > 0)) {
            return $this->Image($file, $x, $y, $w, $h, 'SVG', $link, $align, true, 300, $palign, false, false, $border, false, false, false);
        }
        if ($file{0} === '@') {
            $this->svgdir = '';
            $svgdata      = substr($file, 1);
        } else {
            $this->svgdir = dirname($file);
            $svgdata      = file_get_contents($file);
        }
        if ($svgdata === false) {
            $this->Error('SVG file not found: ' . $file);
        }
        if ($x === '') {
            $x = $this->x;
        }
        if ($y === '') {
            $y = $this->y;
        }
        list($x, $y) = $this->checkPageRegions($h, $x, $y);
        $k                  = $this->k;
        $ox                 = 0;
        $oy                 = 0;
        $ow                 = $w;
        $oh                 = $h;
        $aspect_ratio_align = 'xMidYMid';
        $aspect_ratio_ms    = 'meet';
        $regs               = array();
        preg_match('/<svg([^\>]*)>/si', $svgdata, $regs);
        if (isset($regs[1]) AND !empty($regs[1])) {
            $tmp = array();
            if (preg_match('/[\s]+x[\s]*=[\s]*"([^"]*)"/si', $regs[1], $tmp)) {
                $ox = $this->getHTMLUnitToUnits($tmp[1], 0, $this->svgunit, false);
            }
            $tmp = array();
            if (preg_match('/[\s]+y[\s]*=[\s]*"([^"]*)"/si', $regs[1], $tmp)) {
                $oy = $this->getHTMLUnitToUnits($tmp[1], 0, $this->svgunit, false);
            }
            $tmp = array();
            if (preg_match('/[\s]+width[\s]*=[\s]*"([^"]*)"/si', $regs[1], $tmp)) {
                $ow = $this->getHTMLUnitToUnits($tmp[1], 1, $this->svgunit, false);
            }
            $tmp = array();
            if (preg_match('/[\s]+height[\s]*=[\s]*"([^"]*)"/si', $regs[1], $tmp)) {
                $oh = $this->getHTMLUnitToUnits($tmp[1], 1, $this->svgunit, false);
            }
            $tmp      = array();
            $view_box = array();
            if (preg_match('/[\s]+viewBox[\s]*=[\s]*"[\s]*([0-9\.\-]+)[\s]+([0-9\.\-]+)[\s]+([0-9\.]+)[\s]+([0-9\.]+)[\s]*"/si', $regs[1], $tmp)) {
                if (count($tmp) == 5) {
                    array_shift($tmp);
                    foreach ($tmp as $key => $val) {
                        $view_box[$key] = $this->getHTMLUnitToUnits($val, 0, $this->svgunit, false);
                    }
                    $ox = $view_box[0];
                    $oy = $view_box[1];
                }
                $tmp = array();
                if (preg_match('/[\s]+preserveAspectRatio[\s]*=[\s]*"([^"]*)"/si', $regs[1], $tmp)) {
                    $aspect_ratio = preg_split('/[\s]+/si', $tmp[1]);
                    switch (count($aspect_ratio)) {
                        case 3: {
                            $aspect_ratio_align = $aspect_ratio[1];
                            $aspect_ratio_ms    = $aspect_ratio[2];
                            break;
                        }
                        case 2: {
                            $aspect_ratio_align = $aspect_ratio[0];
                            $aspect_ratio_ms    = $aspect_ratio[1];
                            break;
                        }
                        case 1: {
                            $aspect_ratio_align = $aspect_ratio[0];
                            $aspect_ratio_ms    = 'meet';
                            break;
                        }
                    }
                }
            }
        }
        if (($w <= 0) AND ($h <= 0)) {
            $w = $ow;
            $h = $oh;
        } elseif ($w <= 0) {
            $w = $h * $ow / $oh;
        } elseif ($h <= 0) {
            $h = $w * $oh / $ow;
        }
        list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, $fitonpage);
        if ($this->rasterize_vector_images) {
            return $this->Image($file, $x, $y, $w, $h, 'SVG', $link, $align, true, 300, $palign, false, false, $border, false, false, false);
        }
        $this->img_rb_y = $y + $h;
        if ($this->rtl) {
            if ($palign == 'L') {
                $ximg = $this->lMargin;
            } elseif ($palign == 'C') {
                $ximg = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $ximg = $this->w - $this->rMargin - $w;
            } else {
                $ximg = $x - $w;
            }
            $this->img_rb_x = $ximg;
        } else {
            if ($palign == 'L') {
                $ximg = $this->lMargin;
            } elseif ($palign == 'C') {
                $ximg = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
            } elseif ($palign == 'R') {
                $ximg = $this->w - $this->rMargin - $w;
            } else {
                $ximg = $x;
            }
            $this->img_rb_x = $ximg + $w;
        }
        $gvars       = $this->getGraphicVars();
        $svgoffset_x = ($ximg - $ox) * $this->k;
        $svgoffset_y = -($y - $oy) * $this->k;
        if (isset($view_box[2]) AND ($view_box[2] > 0) AND ($view_box[3] > 0)) {
            $ow = $view_box[2];
            $oh = $view_box[3];
        } else {
            if ($ow <= 0) {
                $ow = $w;
            }
            if ($oh <= 0) {
                $oh = $h;
            }
        }
        $svgscale_x = $w / $ow;
        $svgscale_y = $h / $oh;
        if ($aspect_ratio_align != 'none') {
            $svgscale_old_x = $svgscale_x;
            $svgscale_old_y = $svgscale_y;
            if ($aspect_ratio_ms == 'slice') {
                if ($svgscale_x > $svgscale_y) {
                    $svgscale_y = $svgscale_x;
                } elseif ($svgscale_x < $svgscale_y) {
                    $svgscale_x = $svgscale_y;
                }
            } else {
                if ($svgscale_x < $svgscale_y) {
                    $svgscale_y = $svgscale_x;
                } elseif ($svgscale_x > $svgscale_y) {
                    $svgscale_x = $svgscale_y;
                }
            }
            switch (substr($aspect_ratio_align, 1, 3)) {
                case 'Min': {
                    break;
                }
                case 'Max': {
                    $svgoffset_x += (($w * $this->k) - ($ow * $this->k * $svgscale_x));
                    break;
                }
                default:
                case 'Mid': {
                    $svgoffset_x += ((($w * $this->k) - ($ow * $this->k * $svgscale_x)) / 2);
                    break;
                }
            }
            switch (substr($aspect_ratio_align, 5)) {
                case 'Min': {
                    break;
                }
                case 'Max': {
                    $svgoffset_y -= (($h * $this->k) - ($oh * $this->k * $svgscale_y));
                    break;
                }
                default:
                case 'Mid': {
                    $svgoffset_y -= ((($h * $this->k) - ($oh * $this->k * $svgscale_y)) / 2);
                    break;
                }
            }
        }
        $page_break_mode   = $this->AutoPageBreak;
        $page_break_margin = $this->getBreakMargin();
        $cell_padding      = $this->cell_padding;
        $this->SetCellPadding(0);
        $this->SetAutoPageBreak(false);
        $this->_out('q' . $this->epsmarker);
        $this->Rect($x, $y, $w, $h, 'CNZ', array(), array());
        $e = $ox * $this->k * (1 - $svgscale_x);
        $f = ($this->h - $oy) * $this->k * (1 - $svgscale_y);
        $this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $svgscale_x, 0, 0, $svgscale_y, $e + $svgoffset_x, $f + $svgoffset_y));
        $this->parser = xml_parser_create('UTF-8');
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->parser, 'startSVGElementHandler', 'endSVGElementHandler');
        xml_set_character_data_handler($this->parser, 'segSVGContentHandler');
        if (!xml_parse($this->parser, $svgdata)) {
            $error_message = sprintf('SVG Error: %s at line %d', xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser));
            $this->Error($error_message);
        }
        xml_parser_free($this->parser);
        $this->_out($this->epsmarker . 'Q');
        $this->setGraphicVars($gvars);
        $this->lasth = $gvars['lasth'];
        if (!empty($border)) {
            $bx      = $this->x;
            $by      = $this->y;
            $this->x = $ximg;
            if ($this->rtl) {
                $this->x += $w;
            }
            $this->y = $y;
            $this->Cell($w, $h, '', $border, 0, '', 0, '', 0, true);
            $this->x = $bx;
            $this->y = $by;
        }
        if ($link) {
            $this->Link($ximg, $y, $w, $h, $link, 0);
        }
        switch ($align) {
            case 'T': {
                $this->y = $y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'M': {
                $this->y = $y + round($h / 2);
                $this->x = $this->img_rb_x;
                break;
            }
            case 'B': {
                $this->y = $this->img_rb_y;
                $this->x = $this->img_rb_x;
                break;
            }
            case 'N': {
                $this->SetY($this->img_rb_y);
                break;
            }
            default: {
                $this->x              = $gvars['x'];
                $this->y              = $gvars['y'];
                $this->page           = $gvars['page'];
                $this->current_column = $gvars['current_column'];
                $this->tMargin        = $gvars['tMargin'];
                $this->bMargin        = $gvars['bMargin'];
                $this->w              = $gvars['w'];
                $this->h              = $gvars['h'];
                $this->wPt            = $gvars['wPt'];
                $this->hPt            = $gvars['hPt'];
                $this->fwPt           = $gvars['fwPt'];
                $this->fhPt           = $gvars['fhPt'];
                break;
            }
        }
        $this->endlinex = $this->img_rb_x;
        $this->SetAutoPageBreak($page_break_mode, $page_break_margin);
        $this->cell_padding = $cell_padding;
    }
    protected function getSVGTransformMatrix($attribute)
    {
        $tm        = array(
            1,
            0,
            0,
            1,
            0,
            0
        );
        $transform = array();
        if (preg_match_all('/(matrix|translate|scale|rotate|skewX|skewY)[\s]*\(([^\)]+)\)/si', $attribute, $transform, PREG_SET_ORDER) > 0) {
            foreach ($transform as $key => $data) {
                if (!empty($data[2])) {
                    $a    = 1;
                    $b    = 0;
                    $c    = 0;
                    $d    = 1;
                    $e    = 0;
                    $f    = 0;
                    $regs = array();
                    switch ($data[1]) {
                        case 'matrix': {
                            if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                                $a = $regs[1];
                                $b = $regs[2];
                                $c = $regs[3];
                                $d = $regs[4];
                                $e = $regs[5];
                                $f = $regs[6];
                            }
                            break;
                        }
                        case 'translate': {
                            if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                                $e = $regs[1];
                                $f = $regs[2];
                            } elseif (preg_match('/([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                                $e = $regs[1];
                            }
                            break;
                        }
                        case 'scale': {
                            if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                                $a = $regs[1];
                                $d = $regs[2];
                            } elseif (preg_match('/([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                                $a = $regs[1];
                                $d = $a;
                            }
                            break;
                        }
                        case 'rotate': {
                            if (preg_match('/([0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                                $ang = deg2rad($regs[1]);
                                $x   = $regs[2];
                                $y   = $regs[3];
                                $a   = cos($ang);
                                $b   = sin($ang);
                                $c   = -$b;
                                $d   = $a;
                                $e   = ($x * (1 - $a)) - ($y * $c);
                                $f   = ($y * (1 - $d)) - ($x * $b);
                            } elseif (preg_match('/([0-9\-\.]+)/si', $data[2], $regs)) {
                                $ang = deg2rad($regs[1]);
                                $a   = cos($ang);
                                $b   = sin($ang);
                                $c   = -$b;
                                $d   = $a;
                                $e   = 0;
                                $f   = 0;
                            }
                            break;
                        }
                        case 'skewX': {
                            if (preg_match('/([0-9\-\.]+)/si', $data[2], $regs)) {
                                $c = tan(deg2rad($regs[1]));
                            }
                            break;
                        }
                        case 'skewY': {
                            if (preg_match('/([0-9\-\.]+)/si', $data[2], $regs)) {
                                $b = tan(deg2rad($regs[1]));
                            }
                            break;
                        }
                    }
                    $tm = $this->getTransformationMatrixProduct($tm, array(
                        $a,
                        $b,
                        $c,
                        $d,
                        $e,
                        $f
                    ));
                }
            }
        }
        return $tm;
    }
    protected function getTransformationMatrixProduct($ta, $tb)
    {
        $tm    = array();
        $tm[0] = ($ta[0] * $tb[0]) + ($ta[2] * $tb[1]);
        $tm[1] = ($ta[1] * $tb[0]) + ($ta[3] * $tb[1]);
        $tm[2] = ($ta[0] * $tb[2]) + ($ta[2] * $tb[3]);
        $tm[3] = ($ta[1] * $tb[2]) + ($ta[3] * $tb[3]);
        $tm[4] = ($ta[0] * $tb[4]) + ($ta[2] * $tb[5]) + $ta[4];
        $tm[5] = ($ta[1] * $tb[4]) + ($ta[3] * $tb[5]) + $ta[5];
        return $tm;
    }
    protected function convertSVGtMatrix($tm)
    {
        $a = $tm[0];
        $b = -$tm[1];
        $c = -$tm[2];
        $d = $tm[3];
        $e = $this->getHTMLUnitToUnits($tm[4], 1, $this->svgunit, false) * $this->k;
        $f = -$this->getHTMLUnitToUnits($tm[5], 1, $this->svgunit, false) * $this->k;
        $x = 0;
        $y = $this->h * $this->k;
        $e = ($x * (1 - $a)) - ($y * $c) + $e;
        $f = ($y * (1 - $d)) - ($x * $b) + $f;
        return array(
            $a,
            $b,
            $c,
            $d,
            $e,
            $f
        );
    }
    protected function SVGTransform($tm)
    {
        $this->Transform($this->convertSVGtMatrix($tm));
    }
    protected function setSVGStyles($svgstyle, $prevsvgstyle, $x = 0, $y = 0, $w = 1, $h = 1, $clip_function = '', $clip_params = array())
    {
        $objstyle = '';
        $minlen   = (0.01 / $this->k);
        if (!isset($svgstyle['opacity'])) {
            return $objstyle;
        }
        $regs = array();
        if (preg_match('/url\([\s]*\#([^\)]*)\)/si', $svgstyle['clip-path'], $regs)) {
            $clip_path = $this->svgclippaths[$regs[1]];
            foreach ($clip_path as $cp) {
                $this->startSVGElementHandler('clip-path', $cp['name'], $cp['attribs'], $cp['tm']);
            }
        }
        if ($svgstyle['opacity'] != 1) {
            $this->SetAlpha($svgstyle['opacity']);
        }
        $fill_color = $this->convertHTMLColorToDec($svgstyle['color']);
        $this->SetFillColorArray($fill_color);
        $text_color = $this->convertHTMLColorToDec($svgstyle['text-color']);
        $this->SetTextColorArray($text_color);
        if (preg_match('/rect\(([a-z0-9\-\.]*)[\s]*([a-z0-9\-\.]*)[\s]*([a-z0-9\-\.]*)[\s]*([a-z0-9\-\.]*)\)/si', $svgstyle['clip'], $regs)) {
            $top    = (isset($regs[1]) ? $this->getHTMLUnitToUnits($regs[1], 0, $this->svgunit, false) : 0);
            $right  = (isset($regs[2]) ? $this->getHTMLUnitToUnits($regs[2], 0, $this->svgunit, false) : 0);
            $bottom = (isset($regs[3]) ? $this->getHTMLUnitToUnits($regs[3], 0, $this->svgunit, false) : 0);
            $left   = (isset($regs[4]) ? $this->getHTMLUnitToUnits($regs[4], 0, $this->svgunit, false) : 0);
            $cx     = $x + $left;
            $cy     = $y + $top;
            $cw     = $w - $left - $right;
            $ch     = $h - $top - $bottom;
            if ($svgstyle['clip-rule'] == 'evenodd') {
                $clip_rule = 'CNZ';
            } else {
                $clip_rule = 'CEO';
            }
            $this->Rect($cx, $cy, $cw, $ch, $clip_rule, array(), array());
        }
        $regs = array();
        if (preg_match('/url\([\s]*\#([^\)]*)\)/si', $svgstyle['fill'], $regs)) {
            $gradient = $this->svggradients[$regs[1]];
            if (isset($gradient['xref'])) {
                $newgradient                  = $this->svggradients[$gradient['xref']];
                $newgradient['coords']        = $gradient['coords'];
                $newgradient['mode']          = $gradient['mode'];
                $newgradient['gradientUnits'] = $gradient['gradientUnits'];
                if (isset($gradient['gradientTransform'])) {
                    $newgradient['gradientTransform'] = $gradient['gradientTransform'];
                }
                $gradient = $newgradient;
            }
            $this->_out('q');
            if (!empty($clip_function) AND method_exists($this, $clip_function)) {
                $bbox = call_user_func_array(array(
                    $this,
                    $clip_function
                ), $clip_params);
                if (is_array($bbox) AND (count($bbox) == 4)) {
                    list($x, $y, $w, $h) = $bbox;
                }
            }
            if ($gradient['mode'] == 'measure') {
                if (isset($gradient['gradientTransform']) AND !empty($gradient['gradientTransform'])) {
                    $gtm = $gradient['gradientTransform'];
                    $xa  = ($gtm[0] * $gradient['coords'][0]) + ($gtm[2] * $gradient['coords'][1]) + $gtm[4];
                    $ya  = ($gtm[1] * $gradient['coords'][0]) + ($gtm[3] * $gradient['coords'][1]) + $gtm[5];
                    $xb  = ($gtm[0] * $gradient['coords'][2]) + ($gtm[2] * $gradient['coords'][3]) + $gtm[4];
                    $yb  = ($gtm[1] * $gradient['coords'][2]) + ($gtm[3] * $gradient['coords'][3]) + $gtm[5];
                    if (isset($gradient['coords'][4])) {
                        $gradient['coords'][4] = sqrt(pow(($gtm[0] * $gradient['coords'][4]), 2) + pow(($gtm[1] * $gradient['coords'][4]), 2));
                    }
                    $gradient['coords'][0] = $xa;
                    $gradient['coords'][1] = $ya;
                    $gradient['coords'][2] = $xb;
                    $gradient['coords'][3] = $yb;
                }
                $gradient['coords'][0] = $this->getHTMLUnitToUnits($gradient['coords'][0], 0, $this->svgunit, false);
                $gradient['coords'][1] = $this->getHTMLUnitToUnits($gradient['coords'][1], 0, $this->svgunit, false);
                $gradient['coords'][2] = $this->getHTMLUnitToUnits($gradient['coords'][2], 0, $this->svgunit, false);
                $gradient['coords'][3] = $this->getHTMLUnitToUnits($gradient['coords'][3], 0, $this->svgunit, false);
                if (isset($gradient['coords'][4])) {
                    $gradient['coords'][4] = $this->getHTMLUnitToUnits($gradient['coords'][4], 0, $this->svgunit, false);
                }
                if ($gradient['gradientUnits'] == 'objectBoundingBox') {
                    $gradient['coords'][0] += $x;
                    $gradient['coords'][1] += $y;
                    $gradient['coords'][2] += $x;
                    $gradient['coords'][3] += $y;
                }
                if ($w <= $minlen) {
                    $w = $minlen;
                }
                if ($h <= $minlen) {
                    $h = $minlen;
                }
                $gradient['coords'][0] = ($gradient['coords'][0] - $x) / $w;
                $gradient['coords'][1] = ($gradient['coords'][1] - $y) / $h;
                $gradient['coords'][2] = ($gradient['coords'][2] - $x) / $w;
                $gradient['coords'][3] = ($gradient['coords'][3] - $y) / $h;
                if (isset($gradient['coords'][4])) {
                    $gradient['coords'][4] /= $w;
                }
            } elseif ($gradient['mode'] == 'percentage') {
                foreach ($gradient['coords'] as $key => $val) {
                    $gradient['coords'][$key] = (intval($val) / 100);
                }
            }
            foreach ($gradient['coords'] as $key => $val) {
                if ($val < 0) {
                    $gradient['coords'][$key] = 0;
                } elseif ($val > 1) {
                    $gradient['coords'][$key] = 1;
                }
            }
            if (($gradient['type'] == 2) AND ($gradient['coords'][0] == $gradient['coords'][2]) AND ($gradient['coords'][1] == $gradient['coords'][3])) {
                $gradient['coords'][0] = 1;
                $gradient['coords'][1] = 0;
                $gradient['coords'][2] = 0.999;
                $gradient['coords'][3] = 0;
            }
            $tmp                   = $gradient['coords'][1];
            $gradient['coords'][1] = $gradient['coords'][3];
            $gradient['coords'][3] = $tmp;
            if ($gradient['type'] == 3) {
                $cy = $this->h - $y - ($gradient['coords'][1] * ($w + $h));
                $this->_out(sprintf('%.3F 0 0 %.3F %.3F %.3F cm', $w * $this->k, $w * $this->k, $x * $this->k, $cy * $this->k));
            } else {
                $this->_out(sprintf('%.3F 0 0 %.3F %.3F %.3F cm', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k));
            }
            if (count($gradient['stops']) > 1) {
                $this->Gradient($gradient['type'], $gradient['coords'], $gradient['stops'], array(), false);
            }
        } elseif ($svgstyle['fill'] != 'none') {
            $fill_color = $this->convertHTMLColorToDec($svgstyle['fill']);
            if ($svgstyle['fill-opacity'] != 1) {
                $this->SetAlpha($svgstyle['fill-opacity']);
            }
            $this->SetFillColorArray($fill_color);
            if ($svgstyle['fill-rule'] == 'evenodd') {
                $objstyle .= 'F*';
            } else {
                $objstyle .= 'F';
            }
        }
        if ($svgstyle['stroke'] != 'none') {
            $stroke_style = array(
                'color' => $this->convertHTMLColorToDec($svgstyle['stroke']),
                'width' => $this->getHTMLUnitToUnits($svgstyle['stroke-width'], 0, $this->svgunit, false),
                'cap' => $svgstyle['stroke-linecap'],
                'join' => $svgstyle['stroke-linejoin']
            );
            if (isset($svgstyle['stroke-dasharray']) AND !empty($svgstyle['stroke-dasharray']) AND ($svgstyle['stroke-dasharray'] != 'none')) {
                $stroke_style['dash'] = $svgstyle['stroke-dasharray'];
            }
            $this->SetLineStyle($stroke_style);
            $objstyle .= 'D';
        }
        $regs = array();
        if (!empty($svgstyle['font'])) {
            if (preg_match('/font-family[\s]*:[\s]*([^\;\"]*)/si', $svgstyle['font'], $regs)) {
                $font_family = $this->getFontFamilyName($regs[1]);
            } else {
                $font_family = $svgstyle['font-family'];
            }
            if (preg_match('/font-size[\s]*:[\s]*([^\s\;\"]*)/si', $svgstyle['font'], $regs)) {
                $font_size = trim($regs[1]);
            } else {
                $font_size = $svgstyle['font-size'];
            }
            if (preg_match('/font-style[\s]*:[\s]*([^\s\;\"]*)/si', $svgstyle['font'], $regs)) {
                $font_style = trim($regs[1]);
            } else {
                $font_style = $svgstyle['font-style'];
            }
            if (preg_match('/font-weight[\s]*:[\s]*([^\s\;\"]*)/si', $svgstyle['font'], $regs)) {
                $font_weight = trim($regs[1]);
            } else {
                $font_weight = $svgstyle['font-weight'];
            }
            if (preg_match('/font-stretch[\s]*:[\s]*([^\s\;\"]*)/si', $svgstyle['font'], $regs)) {
                $font_stretch = trim($regs[1]);
            } else {
                $font_stretch = $svgstyle['font-stretch'];
            }
            if (preg_match('/letter-spacing[\s]*:[\s]*([^\s\;\"]*)/si', $svgstyle['font'], $regs)) {
                $font_spacing = trim($regs[1]);
            } else {
                $font_spacing = $svgstyle['letter-spacing'];
            }
        } else {
            $font_family  = $this->getFontFamilyName($svgstyle['font-family']);
            $font_size    = $svgstyle['font-size'];
            $font_style   = $svgstyle['font-style'];
            $font_weight  = $svgstyle['font-weight'];
            $font_stretch = $svgstyle['font-stretch'];
            $font_spacing = $svgstyle['letter-spacing'];
        }
        $font_size    = $this->getHTMLUnitToUnits($font_size, $prevsvgstyle['font-size'], $this->svgunit, false) * $this->k;
        $font_stretch = $this->getCSSFontStretching($font_stretch, $svgstyle['font-stretch']);
        $font_spacing = $this->getCSSFontSpacing($font_spacing, $svgstyle['letter-spacing']);
        switch ($font_style) {
            case 'italic': {
                $font_style = 'I';
                break;
            }
            case 'oblique': {
                $font_style = 'I';
                break;
            }
            default:
            case 'normal': {
                $font_style = '';
                break;
            }
        }
        switch ($font_weight) {
            case 'bold':
            case 'bolder': {
                $font_style .= 'B';
                break;
            }
        }
        switch ($svgstyle['text-decoration']) {
            case 'underline': {
                $font_style .= 'U';
                break;
            }
            case 'overline': {
                $font_style .= 'O';
                break;
            }
            case 'line-through': {
                $font_style .= 'D';
                break;
            }
            default:
            case 'none': {
                break;
            }
        }
        $this->SetFont($font_family, $font_style, $font_size);
        $this->setFontStretching($font_stretch);
        $this->setFontSpacing($font_spacing);
        return $objstyle;
    }
    protected function SVGPath($d, $style = '')
    {
        $op = $this->getPathPaintOperator($style, '');
        if (empty($op)) {
            return;
        }
        $paths = array();
        $d     = preg_replace('/([0-9ACHLMQSTVZ])([\-\+])/si', '\\1 \\2', $d);
        preg_match_all('/([ACHLMQSTVZ])[\s]*([^ACHLMQSTVZ\"]*)/si', $d, $paths, PREG_SET_ORDER);
        $x        = 0;
        $y        = 0;
        $x1       = 0;
        $y1       = 0;
        $x2       = 0;
        $y2       = 0;
        $xmin     = 2147483647;
        $xmax     = 0;
        $ymin     = 2147483647;
        $ymax     = 0;
        $relcoord = false;
        $minlen   = (0.01 / $this->k);
        foreach ($paths as $key => $val) {
            $cmd = trim($val[1]);
            if (strtolower($cmd) == $cmd) {
                $relcoord = true;
                $xoffset  = $x;
                $yoffset  = $y;
            } else {
                $relcoord = false;
                $xoffset  = 0;
                $yoffset  = 0;
            }
            $params = array();
            if (isset($val[2])) {
                $rawparams = preg_split('/([\,\s]+)/si', trim($val[2]));
                $params    = array();
                foreach ($rawparams as $ck => $cp) {
                    $params[$ck] = $this->getHTMLUnitToUnits($cp, 0, $this->svgunit, false);
                    if (abs($params[$ck]) < $minlen) {
                        $params[$ck] = 0;
                    }
                }
            }
            $x0 = $x;
            $y0 = $y;
            switch (strtoupper($cmd)) {
                case 'M': {
                    foreach ($params as $ck => $cp) {
                        if (($ck % 2) == 0) {
                            $x = $cp + $xoffset;
                        } else {
                            $y = $cp + $yoffset;
                            if ((abs($x0 - $x) >= $minlen) OR (abs($y0 - $y) >= $minlen)) {
                                if ($ck == 1) {
                                    $this->_outPoint($x, $y);
                                } else {
                                    $this->_outLine($x, $y);
                                }
                            }
                            $xmin = min($xmin, $x);
                            $ymin = min($ymin, $y);
                            $xmax = max($xmax, $x);
                            $ymax = max($ymax, $y);
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'L': {
                    foreach ($params as $ck => $cp) {
                        if (($ck % 2) == 0) {
                            $x = $cp + $xoffset;
                        } else {
                            $y = $cp + $yoffset;
                            if ((abs($x0 - $x) >= $minlen) OR (abs($y0 - $y) >= $minlen)) {
                                $this->_outLine($x, $y);
                            }
                            $xmin = min($xmin, $x);
                            $ymin = min($ymin, $y);
                            $xmax = max($xmax, $x);
                            $ymax = max($ymax, $y);
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'H': {
                    foreach ($params as $ck => $cp) {
                        $x = $cp + $xoffset;
                        if ((abs($x0 - $x) >= $minlen) OR (abs($y0 - $y) >= $minlen)) {
                            $this->_outLine($x, $y);
                        }
                        $xmin = min($xmin, $x);
                        $xmax = max($xmax, $x);
                        if ($relcoord) {
                            $xoffset = $x;
                        }
                    }
                    break;
                }
                case 'V': {
                    foreach ($params as $ck => $cp) {
                        $y = $cp + $yoffset;
                        if ((abs($x0 - $x) >= $minlen) OR (abs($y0 - $y) >= $minlen)) {
                            $this->_outLine($x, $y);
                        }
                        $ymin = min($ymin, $y);
                        $ymax = max($ymax, $y);
                        if ($relcoord) {
                            $yoffset = $y;
                        }
                    }
                    break;
                }
                case 'C': {
                    foreach ($params as $ck => $cp) {
                        $params[$ck] = $cp;
                        if ((($ck + 1) % 6) == 0) {
                            $x1 = $params[($ck - 5)] + $xoffset;
                            $y1 = $params[($ck - 4)] + $yoffset;
                            $x2 = $params[($ck - 3)] + $xoffset;
                            $y2 = $params[($ck - 2)] + $yoffset;
                            $x  = $params[($ck - 1)] + $xoffset;
                            $y  = $params[($ck)] + $yoffset;
                            $this->_outCurve($x1, $y1, $x2, $y2, $x, $y);
                            $xmin = min($xmin, $x, $x1, $x2);
                            $ymin = min($ymin, $y, $y1, $y2);
                            $xmax = max($xmax, $x, $x1, $x2);
                            $ymax = max($ymax, $y, $y1, $y2);
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'S': {
                    foreach ($params as $ck => $cp) {
                        $params[$ck] = $cp;
                        if ((($ck + 1) % 4) == 0) {
                            if (($key > 0) AND ((strtoupper($paths[($key - 1)][1]) == 'C') OR (strtoupper($paths[($key - 1)][1]) == 'S'))) {
                                $x1 = (2 * $x) - $x2;
                                $y1 = (2 * $y) - $y2;
                            } else {
                                $x1 = $x;
                                $y1 = $y;
                            }
                            $x2 = $params[($ck - 3)] + $xoffset;
                            $y2 = $params[($ck - 2)] + $yoffset;
                            $x  = $params[($ck - 1)] + $xoffset;
                            $y  = $params[($ck)] + $yoffset;
                            $this->_outCurve($x1, $y1, $x2, $y2, $x, $y);
                            $xmin = min($xmin, $x, $x1, $x2);
                            $ymin = min($ymin, $y, $y1, $y2);
                            $xmax = max($xmax, $x, $x1, $x2);
                            $ymax = max($ymax, $y, $y1, $y2);
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'Q': {
                    foreach ($params as $ck => $cp) {
                        $params[$ck] = $cp;
                        if ((($ck + 1) % 4) == 0) {
                            $x1 = $params[($ck - 3)] + $xoffset;
                            $y1 = $params[($ck - 2)] + $yoffset;
                            $xa = ($x + (2 * $x1)) / 3;
                            $ya = ($y + (2 * $y1)) / 3;
                            $x  = $params[($ck - 1)] + $xoffset;
                            $y  = $params[($ck)] + $yoffset;
                            $xb = ($x + (2 * $x1)) / 3;
                            $yb = ($y + (2 * $y1)) / 3;
                            $this->_outCurve($xa, $ya, $xb, $yb, $x, $y);
                            $xmin = min($xmin, $x, $xa, $xb);
                            $ymin = min($ymin, $y, $ya, $yb);
                            $xmax = max($xmax, $x, $xa, $xb);
                            $ymax = max($ymax, $y, $ya, $yb);
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'T': {
                    foreach ($params as $ck => $cp) {
                        $params[$ck] = $cp;
                        if (($ck % 2) != 0) {
                            if (($key > 0) AND ((strtoupper($paths[($key - 1)][1]) == 'Q') OR (strtoupper($paths[($key - 1)][1]) == 'T'))) {
                                $x1 = (2 * $x) - $x1;
                                $y1 = (2 * $y) - $y1;
                            } else {
                                $x1 = $x;
                                $y1 = $y;
                            }
                            $xa = ($x + (2 * $x1)) / 3;
                            $ya = ($y + (2 * $y1)) / 3;
                            $x  = $params[($ck - 1)] + $xoffset;
                            $y  = $params[($ck)] + $yoffset;
                            $xb = ($x + (2 * $x1)) / 3;
                            $yb = ($y + (2 * $y1)) / 3;
                            $this->_outCurve($xa, $ya, $xb, $yb, $x, $y);
                            $xmin = min($xmin, $x, $xa, $xb);
                            $ymin = min($ymin, $y, $ya, $yb);
                            $xmax = max($xmax, $x, $xa, $xb);
                            $ymax = max($ymax, $y, $ya, $yb);
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'A': {
                    foreach ($params as $ck => $cp) {
                        $params[$ck] = $cp;
                        if ((($ck + 1) % 7) == 0) {
                            $x0    = $x;
                            $y0    = $y;
                            $rx    = abs($params[($ck - 6)]);
                            $ry    = abs($params[($ck - 5)]);
                            $ang   = -$rawparams[($ck - 4)];
                            $angle = deg2rad($ang);
                            $fa    = $rawparams[($ck - 3)];
                            $fs    = $rawparams[($ck - 2)];
                            $x     = $params[($ck - 1)] + $xoffset;
                            $y     = $params[$ck] + $yoffset;
                            if ((abs($x0 - $x) < $minlen) AND (abs($y0 - $y) < $minlen)) {
                                $xmin = min($xmin, $x);
                                $ymin = min($ymin, $y);
                                $xmax = max($xmax, $x);
                                $ymax = max($ymax, $y);
                            } else {
                                $cos_ang = cos($angle);
                                $sin_ang = sin($angle);
                                $a       = (($x0 - $x) / 2);
                                $b       = (($y0 - $y) / 2);
                                $xa      = ($a * $cos_ang) - ($b * $sin_ang);
                                $ya      = ($a * $sin_ang) + ($b * $cos_ang);
                                $rx2     = $rx * $rx;
                                $ry2     = $ry * $ry;
                                $xa2     = $xa * $xa;
                                $ya2     = $ya * $ya;
                                $delta   = ($xa2 / $rx2) + ($ya2 / $ry2);
                                if ($delta > 1) {
                                    $rx *= sqrt($delta);
                                    $ry *= sqrt($delta);
                                    $rx2 = $rx * $rx;
                                    $ry2 = $ry * $ry;
                                }
                                $numerator = (($rx2 * $ry2) - ($rx2 * $ya2) - ($ry2 * $xa2));
                                if ($numerator < 0) {
                                    $root = 0;
                                } else {
                                    $root = sqrt($numerator / (($rx2 * $ya2) + ($ry2 * $xa2)));
                                }
                                if ($fa == $fs) {
                                    $root *= -1;
                                }
                                $cax  = $root * (($rx * $ya) / $ry);
                                $cay  = -$root * (($ry * $xa) / $rx);
                                $cx   = ($cax * $cos_ang) - ($cay * $sin_ang) + (($x0 + $x) / 2);
                                $cy   = ($cax * $sin_ang) + ($cay * $cos_ang) + (($y0 + $y) / 2);
                                $angs = $this->getVectorsAngle(1, 0, (($xa - $cax) / $rx), (($cay - $ya) / $ry));
                                $dang = $this->getVectorsAngle((($xa - $cax) / $rx), (($ya - $cay) / $ry), ((-$xa - $cax) / $rx), ((-$ya - $cay) / $ry));
                                if (($fs == 0) AND ($dang > 0)) {
                                    $dang -= (2 * M_PI);
                                } elseif (($fs == 1) AND ($dang < 0)) {
                                    $dang += (2 * M_PI);
                                }
                                $angf = $angs - $dang;
                                if ((($fs == 0) AND ($angs > $angf)) OR (($fs == 1) AND ($angs < $angf))) {
                                    $tmp  = $angs;
                                    $angs = $angf;
                                    $angf = $tmp;
                                }
                                $angs = round(rad2deg($angs), 6);
                                $angf = round(rad2deg($angf), 6);
                                if (($angs < 0) AND ($angf < 0)) {
                                    $angs += 360;
                                    $angf += 360;
                                }
                                $pie = false;
                                if (($key == 0) AND (isset($paths[($key + 1)][1])) AND (trim($paths[($key + 1)][1]) == 'z')) {
                                    $pie = true;
                                }
                                list($axmin, $aymin, $axmax, $aymax) = $this->_outellipticalarc($cx, $cy, $rx, $ry, $ang, $angs, $angf, $pie, 2, false, ($fs == 0), true);
                                $xmin = min($xmin, $x, $axmin);
                                $ymin = min($ymin, $y, $aymin);
                                $xmax = max($xmax, $x, $axmax);
                                $ymax = max($ymax, $y, $aymax);
                            }
                            if ($relcoord) {
                                $xoffset = $x;
                                $yoffset = $y;
                            }
                        }
                    }
                    break;
                }
                case 'Z': {
                    $this->_out('h');
                    break;
                }
            }
        }
        if (!empty($op)) {
            $this->_out($op);
        }
        return array(
            $xmin,
            $ymin,
            ($xmax - $xmin),
            ($ymax - $ymin)
        );
    }
    protected function getVectorsAngle($x1, $y1, $x2, $y2)
    {
        $dprod = ($x1 * $x2) + ($y1 * $y2);
        $dist1 = sqrt(($x1 * $x1) + ($y1 * $y1));
        $dist2 = sqrt(($x2 * $x2) + ($y2 * $y2));
        $angle = acos($dprod / ($dist1 * $dist2));
        if (is_nan($angle)) {
            $angle = M_PI;
        }
        if ((($x1 * $y2) - ($x2 * $y1)) < 0) {
            $angle *= -1;
        }
        return $angle;
    }
    protected function startSVGElementHandler($parser, $name, $attribs, $ctm = array())
    {
        if ($this->svgclipmode) {
            $this->svgclippaths[$this->svgclipid][] = array(
                'name' => $name,
                'attribs' => $attribs,
                'tm' => $this->svgcliptm[$this->svgclipid]
            );
            return;
        }
        if ($this->svgdefsmode AND !in_array($name, array(
            'clipPath',
            'linearGradient',
            'radialGradient',
            'stop'
        ))) {
            if (!isset($attribs['id'])) {
                $attribs['id'] = 'DF_' . (count($this->svgdefs) + 1);
            }
            $this->svgdefs[$attribs['id']] = array(
                'name' => $name,
                'attribs' => $attribs
            );
            return;
        }
        $clipping = false;
        if ($parser == 'clip-path') {
            $clipping = true;
        }
        $prev_svgstyle = $this->svgstyles[(count($this->svgstyles) - 1)];
        $svgstyle      = $this->svgstyles[0];
        if (isset($attribs['style']) AND !$this->empty_string($attribs['style'])) {
            $attribs['style'] = ';' . $attribs['style'];
        }
        foreach ($prev_svgstyle as $key => $val) {
            if (in_array($key, $this->svginheritprop)) {
                $svgstyle[$key] = $val;
            }
            if (isset($attribs[$key]) AND !$this->empty_string($attribs[$key])) {
                if ($attribs[$key] == 'inherit') {
                    $svgstyle[$key] = $val;
                } else {
                    $svgstyle[$key] = $attribs[$key];
                }
            } elseif (isset($attribs['style']) AND !$this->empty_string($attribs['style'])) {
                $attrval = array();
                if (preg_match('/[;\"\s]{1}' . $key . '[\s]*:[\s]*([^;\"]*)/si', $attribs['style'], $attrval) AND isset($attrval[1])) {
                    if ($attrval[1] == 'inherit') {
                        $svgstyle[$key] = $val;
                    } else {
                        $svgstyle[$key] = $attrval[1];
                    }
                }
            }
        }
        if (!empty($ctm)) {
            $tm = $ctm;
        } else {
            $tm = $this->svgstyles[(count($this->svgstyles) - 1)]['transfmatrix'];
        }
        if (isset($attribs['transform']) AND !empty($attribs['transform'])) {
            $tm = $this->getTransformationMatrixProduct($tm, $this->getSVGTransformMatrix($attribs['transform']));
        }
        $svgstyle['transfmatrix'] = $tm;
        $invisible                = false;
        if (($svgstyle['visibility'] == 'hidden') OR ($svgstyle['visibility'] == 'collapse') OR ($svgstyle['display'] == 'none')) {
            $invisible = true;
        }
        switch ($name) {
            case 'defs': {
                $this->svgdefsmode = true;
                break;
            }
            case 'clipPath': {
                if ($invisible) {
                    break;
                }
                $this->svgclipmode = true;
                if (!isset($attribs['id'])) {
                    $attribs['id'] = 'CP_' . (count($this->svgcliptm) + 1);
                }
                $this->svgclipid                      = $attribs['id'];
                $this->svgclippaths[$this->svgclipid] = array();
                $this->svgcliptm[$this->svgclipid]    = $tm;
                break;
            }
            case 'svg': {
                break;
            }
            case 'g': {
                array_push($this->svgstyles, $svgstyle);
                $this->StartTransform();
                $this->setSVGStyles($svgstyle, $prev_svgstyle);
                break;
            }
            case 'linearGradient': {
                if (!isset($attribs['id'])) {
                    $attribs['id'] = 'GR_' . (count($this->svggradients) + 1);
                }
                $this->svggradientid                               = $attribs['id'];
                $this->svggradients[$this->svggradientid]          = array();
                $this->svggradients[$this->svggradientid]['type']  = 2;
                $this->svggradients[$this->svggradientid]['stops'] = array();
                if (isset($attribs['gradientUnits'])) {
                    $this->svggradients[$this->svggradientid]['gradientUnits'] = $attribs['gradientUnits'];
                } else {
                    $this->svggradients[$this->svggradientid]['gradientUnits'] = 'objectBoundingBox';
                }
                $x1 = (isset($attribs['x1']) ? $attribs['x1'] : '0%');
                $y1 = (isset($attribs['y1']) ? $attribs['y1'] : '0%');
                $x2 = (isset($attribs['x2']) ? $attribs['x2'] : '100%');
                $y2 = (isset($attribs['y2']) ? $attribs['y2'] : '0%');
                if (substr($x1, -1) != '%') {
                    $this->svggradients[$this->svggradientid]['mode'] = 'measure';
                } else {
                    $this->svggradients[$this->svggradientid]['mode'] = 'percentage';
                }
                if (isset($attribs['gradientTransform'])) {
                    $this->svggradients[$this->svggradientid]['gradientTransform'] = $this->getSVGTransformMatrix($attribs['gradientTransform']);
                }
                $this->svggradients[$this->svggradientid]['coords'] = array(
                    $x1,
                    $y1,
                    $x2,
                    $y2
                );
                if (isset($attribs['xlink:href']) AND !empty($attribs['xlink:href'])) {
                    $this->svggradients[$this->svggradientid]['xref'] = substr($attribs['xlink:href'], 1);
                }
                break;
            }
            case 'radialGradient': {
                if (!isset($attribs['id'])) {
                    $attribs['id'] = 'GR_' . (count($this->svggradients) + 1);
                }
                $this->svggradientid                               = $attribs['id'];
                $this->svggradients[$this->svggradientid]          = array();
                $this->svggradients[$this->svggradientid]['type']  = 3;
                $this->svggradients[$this->svggradientid]['stops'] = array();
                if (isset($attribs['gradientUnits'])) {
                    $this->svggradients[$this->svggradientid]['gradientUnits'] = $attribs['gradientUnits'];
                } else {
                    $this->svggradients[$this->svggradientid]['gradientUnits'] = 'objectBoundingBox';
                }
                $cx = (isset($attribs['cx']) ? $attribs['cx'] : 0.5);
                $cy = (isset($attribs['cy']) ? $attribs['cy'] : 0.5);
                $fx = (isset($attribs['fx']) ? $attribs['fx'] : $cx);
                $fy = (isset($attribs['fy']) ? $attribs['fy'] : $cy);
                $r  = (isset($attribs['r']) ? $attribs['r'] : 0.5);
                if (isset($attribs['cx']) AND (substr($attribs['cx'], -1) != '%')) {
                    $this->svggradients[$this->svggradientid]['mode'] = 'measure';
                } else {
                    $this->svggradients[$this->svggradientid]['mode'] = 'percentage';
                }
                if (isset($attribs['gradientTransform'])) {
                    $this->svggradients[$this->svggradientid]['gradientTransform'] = $this->getSVGTransformMatrix($attribs['gradientTransform']);
                }
                $this->svggradients[$this->svggradientid]['coords'] = array(
                    $cx,
                    $cy,
                    $fx,
                    $fy,
                    $r
                );
                if (isset($attribs['xlink:href']) AND !empty($attribs['xlink:href'])) {
                    $this->svggradients[$this->svggradientid]['xref'] = substr($attribs['xlink:href'], 1);
                }
                break;
            }
            case 'stop': {
                if (substr($attribs['offset'], -1) == '%') {
                    $offset = floatval(substr($attribs['offset'], -1)) / 100;
                } else {
                    $offset = floatval($attribs['offset']);
                    if ($offset > 1) {
                        $offset /= 100;
                    }
                }
                $stop_color                                          = isset($svgstyle['stop-color']) ? $this->convertHTMLColorToDec($svgstyle['stop-color']) : 'black';
                $opacity                                             = isset($svgstyle['stop-opacity']) ? $svgstyle['stop-opacity'] : 1;
                $this->svggradients[$this->svggradientid]['stops'][] = array(
                    'offset' => $offset,
                    'color' => $stop_color,
                    'opacity' => $opacity
                );
                break;
            }
            case 'path': {
                if ($invisible) {
                    break;
                }
                if (isset($attribs['d'])) {
                    $d = trim($attribs['d']);
                    if (!empty($d)) {
                        if ($clipping) {
                            $this->SVGTransform($tm);
                            $this->SVGPath($d, 'CNZ');
                        } else {
                            $this->StartTransform();
                            $this->SVGTransform($tm);
                            $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, 0, 0, 1, 1, 'SVGPath', array(
                                $d,
                                'CNZ'
                            ));
                            if (!empty($obstyle)) {
                                $this->SVGPath($d, $obstyle);
                            }
                            $this->StopTransform();
                        }
                    }
                }
                break;
            }
            case 'rect': {
                if ($invisible) {
                    break;
                }
                $x  = (isset($attribs['x']) ? $this->getHTMLUnitToUnits($attribs['x'], 0, $this->svgunit, false) : 0);
                $y  = (isset($attribs['y']) ? $this->getHTMLUnitToUnits($attribs['y'], 0, $this->svgunit, false) : 0);
                $w  = (isset($attribs['width']) ? $this->getHTMLUnitToUnits($attribs['width'], 0, $this->svgunit, false) : 0);
                $h  = (isset($attribs['height']) ? $this->getHTMLUnitToUnits($attribs['height'], 0, $this->svgunit, false) : 0);
                $rx = (isset($attribs['rx']) ? $this->getHTMLUnitToUnits($attribs['rx'], 0, $this->svgunit, false) : 0);
                $ry = (isset($attribs['ry']) ? $this->getHTMLUnitToUnits($attribs['ry'], 0, $this->svgunit, false) : $rx);
                if ($clipping) {
                    $this->SVGTransform($tm);
                    $this->RoundedRectXY($x, $y, $w, $h, $rx, $ry, '1111', 'CNZ', array(), array());
                } else {
                    $this->StartTransform();
                    $this->SVGTransform($tm);
                    $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h, 'RoundedRectXY', array(
                        $x,
                        $y,
                        $w,
                        $h,
                        $rx,
                        $ry,
                        '1111',
                        'CNZ'
                    ));
                    if (!empty($obstyle)) {
                        $this->RoundedRectXY($x, $y, $w, $h, $rx, $ry, '1111', $obstyle, array(), array());
                    }
                    $this->StopTransform();
                }
                break;
            }
            case 'circle': {
                if ($invisible) {
                    break;
                }
                $cx = (isset($attribs['cx']) ? $this->getHTMLUnitToUnits($attribs['cx'], 0, $this->svgunit, false) : 0);
                $cy = (isset($attribs['cy']) ? $this->getHTMLUnitToUnits($attribs['cy'], 0, $this->svgunit, false) : 0);
                $r  = (isset($attribs['r']) ? $this->getHTMLUnitToUnits($attribs['r'], 0, $this->svgunit, false) : 0);
                $x  = $cx - $r;
                $y  = $cy - $r;
                $w  = 2 * $r;
                $h  = $w;
                if ($clipping) {
                    $this->SVGTransform($tm);
                    $this->Circle($cx, $cy, $r, 0, 360, 'CNZ', array(), array(), 8);
                } else {
                    $this->StartTransform();
                    $this->SVGTransform($tm);
                    $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h, 'Circle', array(
                        $cx,
                        $cy,
                        $r,
                        0,
                        360,
                        'CNZ'
                    ));
                    if (!empty($obstyle)) {
                        $this->Circle($cx, $cy, $r, 0, 360, $obstyle, array(), array(), 8);
                    }
                    $this->StopTransform();
                }
                break;
            }
            case 'ellipse': {
                if ($invisible) {
                    break;
                }
                $cx = (isset($attribs['cx']) ? $this->getHTMLUnitToUnits($attribs['cx'], 0, $this->svgunit, false) : 0);
                $cy = (isset($attribs['cy']) ? $this->getHTMLUnitToUnits($attribs['cy'], 0, $this->svgunit, false) : 0);
                $rx = (isset($attribs['rx']) ? $this->getHTMLUnitToUnits($attribs['rx'], 0, $this->svgunit, false) : 0);
                $ry = (isset($attribs['ry']) ? $this->getHTMLUnitToUnits($attribs['ry'], 0, $this->svgunit, false) : 0);
                $x  = $cx - $rx;
                $y  = $cy - $ry;
                $w  = 2 * $rx;
                $h  = 2 * $ry;
                if ($clipping) {
                    $this->SVGTransform($tm);
                    $this->Ellipse($cx, $cy, $rx, $ry, 0, 0, 360, 'CNZ', array(), array(), 8);
                } else {
                    $this->StartTransform();
                    $this->SVGTransform($tm);
                    $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h, 'Ellipse', array(
                        $cx,
                        $cy,
                        $rx,
                        $ry,
                        0,
                        0,
                        360,
                        'CNZ'
                    ));
                    if (!empty($obstyle)) {
                        $this->Ellipse($cx, $cy, $rx, $ry, 0, 0, 360, $obstyle, array(), array(), 8);
                    }
                    $this->StopTransform();
                }
                break;
            }
            case 'line': {
                if ($invisible) {
                    break;
                }
                $x1 = (isset($attribs['x1']) ? $this->getHTMLUnitToUnits($attribs['x1'], 0, $this->svgunit, false) : 0);
                $y1 = (isset($attribs['y1']) ? $this->getHTMLUnitToUnits($attribs['y1'], 0, $this->svgunit, false) : 0);
                $x2 = (isset($attribs['x2']) ? $this->getHTMLUnitToUnits($attribs['x2'], 0, $this->svgunit, false) : 0);
                $y2 = (isset($attribs['y2']) ? $this->getHTMLUnitToUnits($attribs['y2'], 0, $this->svgunit, false) : 0);
                $x  = $x1;
                $y  = $y1;
                $w  = abs($x2 - $x1);
                $h  = abs($y2 - $y1);
                if (!$clipping) {
                    $this->StartTransform();
                    $this->SVGTransform($tm);
                    $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h, 'Line', array(
                        $x1,
                        $y1,
                        $x2,
                        $y2
                    ));
                    $this->Line($x1, $y1, $x2, $y2);
                    $this->StopTransform();
                }
                break;
            }
            case 'polyline':
            case 'polygon': {
                if ($invisible) {
                    break;
                }
                $points = (isset($attribs['points']) ? $attribs['points'] : '0 0');
                $points = trim($points);
                $points = preg_split('/[\,\s]+/si', $points);
                if (count($points) < 4) {
                    break;
                }
                $p    = array();
                $xmin = 2147483647;
                $xmax = 0;
                $ymin = 2147483647;
                $ymax = 0;
                foreach ($points as $key => $val) {
                    $p[$key] = $this->getHTMLUnitToUnits($val, 0, $this->svgunit, false);
                    if (($key % 2) == 0) {
                        $xmin = min($xmin, $p[$key]);
                        $xmax = max($xmax, $p[$key]);
                    } else {
                        $ymin = min($ymin, $p[$key]);
                        $ymax = max($ymax, $p[$key]);
                    }
                }
                $x = $xmin;
                $y = $ymin;
                $w = ($xmax - $xmin);
                $h = ($ymax - $ymin);
                if ($name == 'polyline') {
                    $this->StartTransform();
                    $this->SVGTransform($tm);
                    $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h, 'PolyLine', array(
                        $p,
                        'CNZ'
                    ));
                    $this->PolyLine($p, 'D', array(), array());
                    $this->StopTransform();
                } else {
                    if ($clipping) {
                        $this->SVGTransform($tm);
                        $this->Polygon($p, 'CNZ', array(), array(), true);
                    } else {
                        $this->StartTransform();
                        $this->SVGTransform($tm);
                        $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h, 'Polygon', array(
                            $p,
                            'CNZ'
                        ));
                        if (!empty($obstyle)) {
                            $this->Polygon($p, $obstyle, array(), array(), true);
                        }
                        $this->StopTransform();
                    }
                }
                break;
            }
            case 'image': {
                if ($invisible) {
                    break;
                }
                if (!isset($attribs['xlink:href']) OR empty($attribs['xlink:href'])) {
                    break;
                }
                $x   = (isset($attribs['x']) ? $this->getHTMLUnitToUnits($attribs['x'], 0, $this->svgunit, false) : 0);
                $y   = (isset($attribs['y']) ? $this->getHTMLUnitToUnits($attribs['y'], 0, $this->svgunit, false) : 0);
                $w   = (isset($attribs['width']) ? $this->getHTMLUnitToUnits($attribs['width'], 0, $this->svgunit, false) : 0);
                $h   = (isset($attribs['height']) ? $this->getHTMLUnitToUnits($attribs['height'], 0, $this->svgunit, false) : 0);
                $img = $attribs['xlink:href'];
                if (!$clipping) {
                    $this->StartTransform();
                    $this->SVGTransform($tm);
                    $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, $w, $h);
                    if (!$this->empty_string($this->svgdir) AND (($img{0} == '.') OR (basename($img) == $img))) {
                        $img = $this->svgdir . '/' . $img;
                    }
                    if (($img[0] == '/') AND !empty($_SERVER['DOCUMENT_ROOT']) AND ($_SERVER['DOCUMENT_ROOT'] != '/')) {
                        $findroot = strpos($img, $_SERVER['DOCUMENT_ROOT']);
                        if (($findroot === false) OR ($findroot > 1)) {
                            if (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/') {
                                $img = substr($_SERVER['DOCUMENT_ROOT'], 0, -1) . $img;
                            } else {
                                $img = $_SERVER['DOCUMENT_ROOT'] . $img;
                            }
                        }
                    }
                    $img         = urldecode($img);
                    $testscrtype = @parse_url($img);
                    if (!isset($testscrtype['query']) OR empty($testscrtype['query'])) {
                        $img = str_replace(K_PATH_URL, K_PATH_MAIN, $img);
                    }
                    $this->Image($img, $x, $y, $w, $h);
                    $this->StopTransform();
                }
                break;
            }
            case 'text':
            case 'tspan': {
                $this->svgtextmode['invisible'] = $invisible;
                if ($invisible) {
                    break;
                }
                array_push($this->svgstyles, $svgstyle);
                $x                      = (isset($attribs['x']) ? $this->getHTMLUnitToUnits($attribs['x'], 0, $this->svgunit, false) : $this->x);
                $y                      = (isset($attribs['y']) ? $this->getHTMLUnitToUnits($attribs['y'], 0, $this->svgunit, false) : $this->y);
                $svgstyle['text-color'] = $svgstyle['fill'];
                $this->svgtext          = '';
                if (isset($svgstyle['text-anchor'])) {
                    $this->svgtextmode['text-anchor'] = $svgstyle['text-anchor'];
                } else {
                    $this->svgtextmode['text-anchor'] = 'start';
                }
                if (isset($svgstyle['direction'])) {
                    if ($svgstyle['direction'] == 'rtl') {
                        $this->svgtextmode['rtl'] = true;
                    } else {
                        $this->svgtextmode['rtl'] = false;
                    }
                } else {
                    $this->svgtextmode['rtl'] = false;
                }
                if (isset($svgstyle['stroke']) AND ($svgstyle['stroke'] != 'none') AND isset($svgstyle['stroke-width']) AND ($svgstyle['stroke-width'] > 0)) {
                    $this->svgtextmode['stroke'] = $this->getHTMLUnitToUnits($svgstyle['stroke-width'], 0, $this->svgunit, false);
                } else {
                    $this->svgtextmode['stroke'] = false;
                }
                $this->StartTransform();
                $this->SVGTransform($tm);
                $obstyle = $this->setSVGStyles($svgstyle, $prev_svgstyle, $x, $y, 1, 1);
                $this->x = $x;
                $this->y = $y;
                break;
            }
            case 'use': {
                if (isset($attribs['xlink:href'])) {
                    $use = $this->svgdefs[substr($attribs['xlink:href'], 1)];
                    if (isset($attribs['xlink:href'])) {
                        unset($attribs['xlink:href']);
                    }
                    if (isset($attribs['id'])) {
                        unset($attribs['id']);
                    }
                    $attribs = array_merge($use['attribs'], $attribs);
                    $this->startSVGElementHandler($parser, $use['name'], $use['attribs']);
                }
                break;
            }
            default: {
                break;
            }
        }
    }
    protected function endSVGElementHandler($parser, $name)
    {
        switch ($name) {
            case 'defs': {
                $this->svgdefsmode = false;
                break;
            }
            case 'clipPath': {
                $this->svgclipmode = false;
                break;
            }
            case 'g': {
                array_pop($this->svgstyles);
                $this->StopTransform();
                break;
            }
            case 'text':
            case 'tspan': {
                if ($this->svgtextmode['invisible']) {
                    break;
                }
                $text = $this->stringTrim($this->svgtext);
                if ($this->svgtextmode['text-anchor'] != 'start') {
                    $textlen = $this->GetStringWidth($text);
                    if ($this->svgtextmode['text-anchor'] == 'end') {
                        if ($this->svgtextmode['rtl']) {
                            $this->x += $textlen;
                        } else {
                            $this->x -= $textlen;
                        }
                    } elseif ($this->svgtextmode['text-anchor'] == 'middle') {
                        if ($this->svgtextmode['rtl']) {
                            $this->x += ($textlen / 2);
                        } else {
                            $this->x -= ($textlen / 2);
                        }
                    }
                }
                $textrendermode  = $this->textrendermode;
                $textstrokewidth = $this->textstrokewidth;
                $this->setTextRenderingMode($this->svgtextmode['stroke'], true, false);
                $this->Cell(0, 0, $text, 0, 0, '', false, '', 0, false, 'L', 'T');
                $this->textrendermode  = $textrendermode;
                $this->textstrokewidth = $textstrokewidth;
                $this->svgtext         = '';
                $this->StopTransform();
                array_pop($this->svgstyles);
                break;
            }
            default: {
                break;
            }
        }
    }
    protected function segSVGContentHandler($parser, $data)
    {
        $this->svgtext .= $data;
    }
}