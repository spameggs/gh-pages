<?php
class rlResize
{
    var $strOriginalImagePath;
    var $strResizedImagePath;
    var $arrOriginalDetails;
    var $arrResizedDetails;
    var $resOriginalImage;
    var $resResizedImage;
    var $boolProtect = true;
    var $gdVersion;
    var $returnRes = false;
    var $rlValid;
    var $driftX = 5;
    var $driftY = 5;
    var $rlWatermark;
    function rlResize()
    {
        global $rlValid;
        $this->rlValid = $rlValid;
        $_gd_info      = gd_info();
        if (!$_gd_info)
            return false;
        preg_match('/(\d)\.(\d)/', $_gd_info['GD Version'], $_match);
        $this->gdVersion = $_match[1];
    }
    function resize($strPath, $strSavePath, $strType = 'W', $value = '150', $boolProtect = true, $watermark = true)
    {
        $this->strOriginalImagePath = $strPath;
        $this->strResizedImagePath  = $strSavePath;
        $this->boolProtect          = $boolProtect;
        $this->rlWatermark          = $watermark;
        $this->arrOriginalDetails   = getimagesize($this->strOriginalImagePath);
        $this->arrResizedDetails    = $this->arrOriginalDetails;
        $this->resOriginalImage     = $this->createImage($this->strOriginalImagePath);
        switch (strtoupper($strType)) {
            case 'P':
                $this->resizeToPercent($value);
                break;
            case 'H':
                $this->resizeToHeight($value);
                break;
            case 'C':
                $this->resizeToCustom($value);
                break;
            case 'W':
            default:
                $this->resizeToWidth($value);
                break;
        }
    }
    function findResourceDetails($resImage)
    {
        if ($resImage == $this->resResizedImage) {
            return $this->arrResizedDetails;
        } else {
            return $this->arrOriginalDetails;
        }
    }
    function updateNewDetails()
    {
        $this->arrResizedDetails[0] = imagesx($this->resResizedImage);
        $this->arrResizedDetails[1] = imagesy($this->resResizedImage);
    }
    function createImage($strImagePath)
    {
        $arrDetails = $this->findResourceDetails($strImagePath);
        switch ($arrDetails['mime']) {
            case 'image/jpeg':
                return imagecreatefromjpeg($strImagePath);
                break;
            case 'image/png':
                return imagecreatefrompng($strImagePath);
                break;
            case 'image/gif':
                return imagecreatefromgif($strImagePath);
                break;
        }
    }
    function saveImage()
    {
        $numQuality = $GLOBALS['config']['img_quality'];
        if ($GLOBALS['config']['watermark_using'] && $this->rlWatermark) {
            if ($GLOBALS['config']['watermark_type'] == 'image') {
                $w_source  = $GLOBALS['config']['watermark_image_url'];
                $watermark = imagecreatefrompng($w_source);
                if ($watermark) {
                    list($watermark_width, $watermark_height) = getimagesize($w_source);
                    $image  = $this->resResizedImage;
                    $dest_x = $this->arrResizedDetails[0] - $watermark_width - $this->driftX;
                    $dest_y = $this->arrResizedDetails[1] - $watermark_height - $this->driftY;
                    imagesavealpha($this->resResizedImage, true);
                    imagecopyresampled($this->resResizedImage, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);
                    imagedestroy($watermark);
                }
            } else {
                $w_text = $GLOBALS['config']['watermark_text'];
                if (empty($w_text)) {
                    $w_text = $this->rlValid->getDomain(RL_URL_HOME);
                }
                $w_blank   = round(strlen($w_text) * 6.5);
                $watermark = imagecreatetruecolor($w_blank, 18);
                $bgc       = imagecolortransparent($watermark, 0);
                $tc        = imagecolorallocate($watermark, 255, 255, 255);
                imagefilledrectangle($watermark, 0, 0, $w_blank, 18, $bgc);
                imagestring($watermark, 2, 5, 4, $w_text, $tc);
                $watermark_width  = imagesx($watermark);
                $watermark_height = imagesy($watermark);
                $dest_x           = $this->arrResizedDetails[0] - $watermark_width - $this->driftX;
                $dest_y           = $this->arrResizedDetails[1] - $watermark_height - $this->driftY;
                imagecopymerge($this->resResizedImage, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);
                imagedestroy($watermark);
                imagedestroy($bgc);
                imagedestroy($tc);
            }
        }
        switch ($this->arrResizedDetails['mime']) {
            case 'image/jpeg':
                $this->returnRes = imagejpeg($this->resResizedImage, $this->strResizedImagePath, $numQuality);
                break;
            case 'image/png':
                $this->returnRes = imagepng($this->resResizedImage, $this->strResizedImagePath);
                break;
            case 'image/gif':
                $this->returnRes = imagegif($this->resResizedImage, $this->strResizedImagePath);
                break;
        }
    }
    function showImage($resImage)
    {
        $arrDetails = $this->findResourceDetails($resImage);
        header("Content-type: " . $arrDetails['mime']);
        switch ($arrDetails['mime']) {
            case 'image/jpeg':
                return imagejpeg($resImage);
                break;
            case 'image/png':
                return imagepng($resImage);
                break;
            case 'image/gif':
                return imagegif($resImage);
                break;
        }
    }
    function destroyImage()
    {
        imagedestroy($this->resResizedImage);
        imagedestroy($this->resOriginalImage);
        unset($this->resResizedImage);
        unset($this->strResizedImagePath);
        unset($this->resOriginalImage);
        unset($this->strOriginalImagePath);
    }
    function _resize($numWidth, $numHeight)
    {
        global $config;
        switch ($this->arrOriginalDetails['mime']) {
            case 'image/gif':
                $this->resResizedImage = imagecreate($numWidth, $numHeight);
                break;
            case 'image/png':
                $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);
                imagealphablending($this->resResizedImage, false);
                imagesavealpha($this->resResizedImage, true);
                break;
            default:
                $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);
                break;
        }
        $white = imagecolorallocate($this->resResizedImage, 13, 13, 13);
        imagefill($this->resResizedImage, 0, 0, $white);
        $this->updateNewDetails();
        $resize_method = function_exists('imagecopyresampled') ? 'imagecopyresampled' : 'imagecopyresized';
        if (!$config['img_crop_module'] && !$this->boolProtect) {
            if (($this->arrOriginalDetails[0] / $this->arrOriginalDetails[1]) >= ($numWidth / $numHeight)) {
                $nw = $numWidth;
                $nh = $this->arrOriginalDetails[1] * ($numWidth / $this->arrOriginalDetails[0]);
                $nx = 0;
                $ny = round(abs($numHeight - $nh) / 2);
                $this->driftY += $ny;
            } else {
                $nw = $this->arrOriginalDetails[0] * ($numHeight / $this->arrOriginalDetails[1]);
                $nh = $numHeight;
                $nx = round(abs($numWidth - $nw) / 2);
                $ny = 0;
                $this->driftX += $nx;
            }
            $resize_method($this->resResizedImage, $this->resOriginalImage, $nx, $ny, 0, 0, $nw, $nh, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
        } else {
            $resize_method($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $numWidth, $numHeight, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]);
        }
        $this->saveImage();
        $this->destroyImage();
    }
    function _imageProtect($numWidth, $numHeight)
    {
        if ($this->boolProtect AND ($numWidth > $this->arrOriginalDetails[0] OR $numHeight > $this->arrOriginalDetails[1])) {
            return 0;
        }
        return 1;
    }
    function resizeToWidth($numWidth)
    {
        $numHeight = (int) (($numWidth * $this->arrOriginalDetails[1]) / $this->arrOriginalDetails[0]);
        $this->_resize($numWidth, $numHeight);
    }
    function resizeToHeight($numHeight)
    {
        $numWidth = (int) (($numHeight * $this->arrOriginalDetails[0]) / $this->arrOriginalDetails[1]);
        $this->_resize($numWidth, $numHeight);
    }
    function resizeToPercent($numPercent)
    {
        $numWidth  = (int) (($this->arrOriginalDetails[0] / 100) * $numPercent);
        $numHeight = (int) (($this->arrOriginalDetails[1] / 100) * $numPercent);
        $this->_resize($numWidth, $numHeight);
    }
    function resizeToCustom($size)
    {
        if (is_array($size)) {
            $_photo_width  = $this->arrOriginalDetails[0];
            $_photo_height = $this->arrOriginalDetails[1];
            $img_width     = (int) $size[0];
            $img_height    = (int) $size[1];
            $this->_resize($img_width, $img_height);
        } else {
            $this->resizeToWidth($size);
        }
    }
}
?>