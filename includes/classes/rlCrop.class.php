<?php
define("ccTOPLEFT", 0);
define("ccTOP", 1);
define("ccTOPRIGHT", 2);
define("ccLEFT", 3);
define("ccCENTRE", 4);
define("ccCENTER", 4);
define("ccRIGHT", 5);
define("ccBOTTOMLEFT", 6);
define("ccBOTTOM", 7);
define("ccBOTTOMRIGHT", 8);
class rlCrop
{
    var $_imgOrig = null;
    var $_imgFinal = null;
    var $_showDebug = false;
    var $gdInfo = array();
    function rlCrop($debug = false)
    {
        $this->setDebugging($debug);
        $this->gdInfo = $this->getGDInfo();
    }
    function setDebugging($do = false)
    {
        $this->_showDebug = ($do === true) ? true : false;
    }
    function loadImage($filename)
    {
        $ext  = strtolower($this->_getExtension($filename));
        $func = 'imagecreatefrom' . ($ext == 'jpg' || $ext == 'jpe' ? 'jpeg' : $ext);
        if (!$this->_isSupported($filename, $ext, $func, false)) {
            return false;
        }
        $this->_imgOrig = $func($filename);
        if ($this->_imgOrig == null) {
            $this->_debug("The image could not be created from the '$filename' file using the '$func' function.");
            return false;
        }
        return true;
    }
    function loadImageFromString($string)
    {
        $this->_imgOrig = imagecreatefromstring($string);
        if (!$this->_imgOrig) {
            $this->_debug('The image (supplied as a string) could not be created.');
            return false;
        }
        return true;
    }
    function saveImage($filename, $quality = 90, $forcetype = '')
    {
        if ($this->_imgFinal == null) {
            $this->_debug('There is no cropped image to save.');
            return false;
        }
        $ext  = ($forcetype == '') ? $this->_getExtension($filename) : strtolower($forcetype);
        $func = 'image' . ($ext == 'jpg' || $ext == 'jpe' ? 'jpeg' : $ext);
        if (!$this->_isSupported($filename, $ext, $func, true)) {
            return false;
        }
        $saved = false;
        switch ($ext) {
            case 'gif':
                if ($this->gdInfo['Truecolor Support'] && imageistruecolor($this->_imgFinal)) {
                    imagetruecolortopalette($this->_imgFinal, false, 255);
                }
            case 'png':
                $saved = $func($this->_imgFinal, $filename);
                break;
            case 'jpg':
            case 'jpeg':
                $saved = $func($this->_imgFinal, $filename, $quality);
                break;
        }
        if ($saved == false) {
            $this->_debug("The image could not be saved to the '$filename' file as the file type '$ext' using the '$func' function.");
            return false;
        }
        return true;
    }
    function showImage($type = 'png', $quality = 90)
    {
        if ($this->_imgFinal == null) {
            $this->_debug('There is no cropped image to show.');
            return false;
        }
        $type = strtolower($type);
        $func = 'image' . ($type == 'jpg' || $type == 'jpe' ? 'jpeg' : $type);
        $head = 'image/' . ($type == 'jpg' || $type == 'jpe' ? 'jpeg' : $type);
        if (!$this->_isSupported('[showing file]', $type, $func, false)) {
            return false;
        }
        header("Content-type: $head");
        switch ($type) {
            case 'gif':
                if ($this->gdInfo['Truecolor Support'] && imageistruecolor($this->_imgFinal)) {
                    imagetruecolortopalette($this->_imgFinal, false, 255);
                }
            case 'png':
                $func($this->_imgFinal);
                break;
            case 'jpg':
            case 'jpeg':
                $func($this->_imgFinal, '', $quality);
                break;
        }
        return true;
    }
    function cropBySize($x, $y, $position = ccCENTRE)
    {
        if ($x && $y) {
            if (imagesx($this->_imgOrig) <= imagesy($this->_imgOrig)) {
                $ny = (imagesx($this->_imgOrig) * $y) / $x;
                $nx = imagesx($this->_imgOrig);
                if ($ny > imagesy($this->_imgOrig)) {
                    $nx = (imagesy($this->_imgOrig) * $x) / $y;
                    $ny = imagesy($this->_imgOrig);
                }
            } else {
                $nx = (imagesy($this->_imgOrig) * $x) / $y;
                $ny = imagesy($this->_imgOrig);
                if ($nx > imagesx($this->_imgOrig)) {
                    $ny = (imagesx($this->_imgOrig) * $y) / $x;
                    $nx = imagesx($this->_imgOrig);
                }
            }
        } else {
            $nx = imagesx($this->_imgOrig);
            $ny = imagesy($this->_imgOrig);
        }
        return ($this->_cropSize(-1, -1, $nx, $ny, $position));
    }
    function cropToSize($x, $y, $position = ccCENTRE)
    {
        return ($this->_cropSize(-1, -1, ($x <= 0 ? 1 : $x), ($y <= 0 ? 1 : $y), $position));
    }
    function cropToDimensions($sx, $sy, $ex, $ey)
    {
        return ($this->_cropSize($sx, $sy, abs($ex - $sx), abs($ey - $sy), null));
    }
    function cropByPercent($px, $py, $position = ccCENTRE)
    {
        $nx = (!$px) ? imagesx($this->_imgOrig) : (imagesx($this->_imgOrig) - (($px / 100) * imagesx($this->_imgOrig)));
        $ny = (!$py) ? imagesy($this->_imgOrig) : (imagesy($this->_imgOrig) - (($py / 100) * imagesy($this->_imgOrig)));
        return ($this->_cropSize(-1, -1, $nx, $ny, $position));
    }
    function cropToPercent($px, $py, $position = ccCENTRE)
    {
        $nx = (!$px) ? imagesx($this->_imgOrig) : (($px / 100) * imagesx($this->_imgOrig));
        $ny = (!$py) ? imagesy($this->_imgOrig) : (($py / 100) * imagesy($this->_imgOrig));
        return ($this->_cropSize(-1, -1, $nx, $ny, $position));
    }
    function cropByAuto($threshold = 254)
    {
        if ($threshold < 0) {
            $threshold = 0;
        }
        if ($threshold > 255) {
            $threshold = 255;
        }
        $sizex = imagesx($this->_imgOrig);
        $sizey = imagesy($this->_imgOrig);
        $sx    = $sy = $ex = $ey = -1;
        for ($y = 0; $y < $sizey; $y++) {
            for ($x = 0; $x < $sizex; $x++) {
                if ($threshold >= $this->_getThresholdValue($this->_imgOrig, $x, $y)) {
                    if ($sy == -1) {
                        $sy = $y;
                    } else {
                        $ey = $y;
                    }
                    if ($sx == -1) {
                        $sx = $x;
                    } else {
                        if ($x < $sx) {
                            $sx = $x;
                        } else if ($x > $ex) {
                            $ex = $x;
                        }
                    }
                }
            }
        }
        return ($this->_cropSize($sx, $sy, abs($ex - $sx), abs($ey - $sy), ccTOPLEFT));
    }
    function flushImages($original = true)
    {
        imagedestroy($this->_imgFinal);
        $this->_imgFinal = null;
        if ($original) {
            imagedestroy($this->_imgOrig);
            $this->_imgOrig = null;
        }
    }
    function _cropSize($ox, $oy, $nx, $ny, $position)
    {
        if ($this->_imgOrig == null) {
            $this->_debug('The original image has not been loaded.');
            return false;
        }
        if (($nx <= 0) || ($ny <= 0)) {
            $this->_debug('The image could not be cropped because the size given is not valid.');
            return false;
        }
        if (($nx > imagesx($this->_imgOrig)) || ($ny > imagesy($this->_imgOrig))) {
            $this->_debug('The image could not be cropped because the size given is larger than the original image.');
            return false;
        }
        if ($ox == -1 || $oy == -1) {
            list($ox, $oy) = $this->_getCopyPosition($nx, $ny, $position);
        }
        if ($this->gdInfo['Truecolor Support']) {
            $this->_imgFinal = imagecreatetruecolor($nx, $ny);
            imagealphablending($this->_imgFinal, false);
            imagesavealpha($this->_imgFinal, true);
            imagecopyresampled($this->_imgFinal, $this->_imgOrig, 0, 0, $ox, $oy, $nx, $ny, $nx, $ny);
        } else {
            $this->_imgFinal = imagecreate($nx, $ny);
            imagecopyresized($this->_imgFinal, $this->_imgOrig, 0, 0, $ox, $oy, $nx, $ny, $nx, $ny);
        }
        return true;
    }
    function _getCopyPosition($nx, $ny, $position)
    {
        $ox = imagesx($this->_imgOrig);
        $oy = imagesy($this->_imgOrig);
        switch ($position) {
            case ccTOPLEFT:
                return array(
                    0,
                    0
                );
            case ccTOP:
                return array(
                    ceil(($ox - $nx) / 2),
                    0
                );
            case ccTOPRIGHT:
                return array(
                    ($ox - $nx),
                    0
                );
            case ccLEFT:
                return array(
                    0,
                    ceil(($oy - $ny) / 2)
                );
            case ccCENTRE:
                return array(
                    ceil(($ox - $nx) / 2),
                    ceil(($oy - $ny) / 2)
                );
            case ccRIGHT:
                return array(
                    ($ox - $nx),
                    ceil(($oy - $ny) / 2)
                );
            case ccBOTTOMLEFT:
                return array(
                    0,
                    ($oy - $ny)
                );
            case ccBOTTOM:
                return array(
                    ceil(($ox - $nx) / 2),
                    ($oy - $ny)
                );
            case ccBOTTOMRIGHT:
                return array(
                    ($ox - $nx),
                    ($oy - $ny)
                );
        }
        return array();
    }
    function _getThresholdValue($im, $x, $y)
    {
        $rgb = imagecolorat($im, $x, $y);
        $r   = ($rgb >> 16) & 0xFF;
        $g   = ($rgb >> 8) & 0xFF;
        $b   = $rgb & 0xFF;
        return (($r + $g + $b) / 3);
    }
    function _getExtension($file)
    {
        $ext = '';
        if (strrpos($file, '.')) {
            $ext = strtolower(substr($file, (strrpos($file, '.') ? strrpos($file, '.') + 1 : strlen($file)), strlen($file)));
        }
        $ext = $ext == 'jpe' ? 'jpg' : $ext;
        return $ext;
    }
    function _isSupported($filename, $extension, $function, $write = false)
    {
        $giftype   = ($write) ? ' Create Support' : ' Read Support';
        $extension = $extension == 'jpg' && !$this->gdInfo['JPG Support'] && $this->gdInfo['JPEG Support'] ? 'jpeg' : $extension;
        $support   = strtoupper($extension) . ($extension == 'gif' ? $giftype : ' Support');
        if (!isset($this->gdInfo[$support]) || $this->gdInfo[$support] == false) {
            $request = ($write) ? 'saving' : 'reading';
            $this->_debug("Support for $request the file type '$extension' cannot be found.");
            return false;
        }
        if (!function_exists($function)) {
            $request = ($write) ? 'save' : 'read';
            $this->_debug("The '$function' function required to $request the '$filename' file cannot be found.");
            return false;
        }
        return true;
    }
    function getGDInfo($justVersion = false)
    {
        $gdinfo = array();
        if (function_exists('gd_info')) {
            $gdinfo = gd_info();
            if ($gdinfo['JPEG Support']) {
                $gdinfo['JPG Support'] = 1;
                $gdinfo['JPE Support'] = 1;
            }
            if ($gdinfo['JPG Support']) {
                $gdinfo['JPEG Support'] = 1;
                $gdinfo['JPE Support']  = 1;
            }
        } else {
            $gd = array(
                'GD Version' => '',
                'FreeType Support' => false,
                'FreeType Linkage' => '',
                'T1Lib Support' => false,
                'GIF Read Support' => false,
                'GIF Create Support' => false,
                'JPG Support' => false,
                'PNG Support' => false,
                'WBMP Support' => false,
                'XBM Support' => false
            );
            ob_start();
            phpinfo();
            $buffer = ob_get_contents();
            ob_end_clean();
            foreach (explode("\n", $buffer) as $line) {
                $line = array_map('trim', (explode('|', strip_tags(str_replace('</td>', '|', $line)))));
                if (isset($gd[$line[0]])) {
                    if (strtolower($line[1]) == 'enabled') {
                        $gd[$line[0]] = true;
                    } else {
                        $gd[$line[0]] = $line[1];
                    }
                }
            }
            $gdinfo = $gd;
            if ($gdinfo['JPEG Support']) {
                $gdinfo['JPG Support'] = 1;
                $gdinfo['JPE Support'] = 1;
            }
            if ($gdinfo['JPG Support']) {
                $gdinfo['JPEG Support'] = 1;
                $gdinfo['JPE Support']  = 1;
            }
        }
        if (isset($gdinfo['JIS-mapped Japanese Font Support'])) {
            unset($gdinfo['JIS-mapped Japanese Font Support']);
        }
        if (function_exists('imagecreatefromgd')) {
            $gdinfo['GD Support'] = true;
        }
        if (function_exists('imagecreatefromgd2')) {
            $gdinfo['GD2 Support'] = true;
        }
        if (preg_match('/^(bundled|2)/', $gdinfo['GD Version'])) {
            $gdinfo['Truecolor Support'] = true;
        } else {
            $gdinfo['Truecolor Support'] = false;
        }
        if ($gdinfo['GD Version'] != '') {
            $match = array();
            if (preg_match('/([0-9\.]+)/', $gdinfo['GD Version'], $match)) {
                $foo               = explode('.', $match[0]);
                $gdinfo['Version'] = array(
                    'major' => $foo[0],
                    'minor' => $foo[1],
                    'patch' => $foo[2]
                );
            }
        }
        return ($justVersion) ? $gdinfo['Version'] : $gdinfo;
    }
    function _debug($string)
    {
        if ($this->_showDebug) {
            echo '<p class="debug">', $string, "</p>\n";
        }
    }
    function ajaxCrop($coords, $id)
    {
        global $_response, $rlDb, $account_info, $config, $lang, $rlResize, $rlListings;
        $id = (int) $id;
        $_response->setCharacterEncoding('UTF-8');
        $sql = "SELECT `T2`.`Account_ID`, `T2`.`ID` AS `Listing_ID`, `T1`.`Photo`, `T1`.`Thumbnail`, `T1`.`Original` ";
        $sql .= "FROM `" . RL_DBPREFIX . "listing_photos` AS `T1` ";
        $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listings` AS `T2` ON `T2`.`ID` = `T1`.`Listing_ID` ";
        $sql .= "WHERE `T1`.`ID` = '{$id}' LIMIT 1";
        $photo_info = $rlDb->getRow($sql);
        $owner_id   = $photo_info['Account_ID'];
        if ($owner_id == $account_info['ID'] || defined('REALM')) {
            $ext     = array_pop(explode('.', $photo_info['Original']));
            $tmp_dir = explode('/', $photo_info['Original']);
            array_pop($tmp_dir);
            $dir_name       = implode('/', $tmp_dir);
            $thumbnail_name = $dir_name . '/thumb_' . time() . mt_rand() . '.' . $ext;
            $thumbnail_file = RL_FILES . $thumbnail_name;
            $thumbnail_url  = RL_FILES_URL . $thumbnail_name;
            $photo_name     = $dir_name . '/large_' . time() . mt_rand() . '.' . $ext;
            $photo_file     = RL_FILES . $photo_name;
            $this->loadImage(RL_FILES . $photo_info['Original']);
            $this->cropToDimensions(ceil($coords[0]), ceil($coords[1]), ceil($coords[2]), ceil($coords[3]));
            $this->saveImage($photo_file, $config['img_quality']);
            $this->flushImages();
            if (is_readable($photo_file)) {
                $rlResize->resize($photo_file, $thumbnail_file, 'C', array(
                    $GLOBALS['config']['pg_upload_thumbnail_width'],
                    $GLOBALS['config']['pg_upload_thumbnail_height']
                ), null, false);
                $rlResize->resize($photo_file, $photo_file, 'C', array(
                    $GLOBALS['config']['pg_upload_large_width'],
                    $GLOBALS['config']['pg_upload_large_height']
                ), null, true);
            }
            if (is_readable($photo_file) && is_readable($thumbnail_file)) {
                unlink(RL_FILES . $photo_info['Photo']);
                unlink(RL_FILES . $photo_info['Thumbnail']);
                $update = array(
                    'fields' => array(
                        'Photo' => $photo_name,
                        'Thumbnail' => $thumbnail_name,
                        'Status' => 'active'
                    ),
                    'where' => array(
                        'ID' => $id
                    )
                );
                $GLOBALS['rlActions']->updateOne($update, 'listing_photos');
                $rlListings->updatePhotoData($photo_info['Listing_ID']);
                $_response->script("
					$('#crop_block').slideUp('slow');
					$('#navbar_{$id} img.crop').show();
					$('#navbar_{$id}').parent().find('img.delete').show();
					$('#navbar_{$id}').parent().find('img.thumbnail').attr('src', '" . $thumbnail_url . "');
					printMessage('notice', '{$lang['crop_completed']}');
				");
            }
        } else {
            $_response->script("printMessage('notice', '{$lang['error_crop_owner_fail']}')");
        }
        $_response->script("
			$('#crop_accept').val('{$lang['rl_accept']}');
			$('#crop_cancel').show();
		");
        return $_response;
    }
}