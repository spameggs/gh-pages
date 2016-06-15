<?php
class rlCategoriesIcons extends reefless
{
    var $rlActions;
    var $rlDb;
    function rlCategoriesIcons()
    {
        global $rlActions, $rlDb;
        $this->rlActions =& $rlActions;
        $this->rlDb =& $rlDb;
    }
    function ajaxDeleteIcon($category_id = null)
    {
        global $_response, $rlCache;
        $_response->setCharacterEncoding('UTF-8');
        $photo         = $this->rlDb->fetch('*', array(
            'Key' => $category_id
        ), null, null, 'categories', 'row');
        $update_info[] = array(
            'fields' => array(
                'Icon' => ''
            ),
            'where' => array(
                "Key" => $category_id
            )
        );
        $this->rlActions->update($update_info, 'categories');
        if (!empty($photo)) {
            unlink(RL_FILES . $photo['Icon']);
            unlink(RL_FILES . str_replace("icon", "icon_original", $photo['Icon']));
        }
        unset($photo);
        $GLOBALS['rlCache']->updateCategories();
        $_response->script("$('#gallery').slideUp('normal');");
        $_response->script("$('#fileupload').html(null);");
        $notice = $GLOBALS['rlNotice']->createNotice($GLOBALS['lang']['category_icon_icon_deleted']);
        $_response->assign('notice_block', 'innerHTML', $notice);
        $_response->script("$('#notice_obj').fadeIn('slow');");
        return $_response;
    }
    function updateIcons($width = 0, $height = 0)
    {
        if ($width > 0 && $height > 0) {
            $sql        = "SELECT `ID`,`Icon` FROM `" . RL_DBPREFIX . "categories` WHERE `Icon` <> '' AND `Status` <> 'trash'";
            $categories = $this->rlDb->getAll($sql);
            if (!empty($categories)) {
                for ($j = 0; $j < count($categories); $j++) {
                    if (!empty($categories[$j]['Icon'])) {
                        $this->loadClass('Resize');
                        $this->loadClass('Crop');
                        $original  = RL_FILES . str_replace("icon", "icon_original", $categories[$j]['Icon']);
                        $icon_name = $categories[$j]['Icon'];
                        $icon_file = RL_FILES . $icon_name;
                        if ($GLOBALS['config']['icon_crop_module']) {
                            $GLOBALS['rlCrop']->loadImage($original);
                            $GLOBALS['rlCrop']->cropBySize($width, $height, ccCENTER);
                            $GLOBALS['rlCrop']->saveImage($icon_file, $GLOBALS['config']['img_quality']);
                            $GLOBALS['rlCrop']->flushImages();
                            $GLOBALS['rlResize']->resize($icon_file, $icon_file, 'C', array(
                                $width,
                                $height
                            ));
                        } else {
                            $GLOBALS['rlResize']->resize($original, $icon_file, 'C', array(
                                $width,
                                $height
                            ), null, false);
                        }
                        if (is_readable($icon_file)) {
                            chmod($icon_file, 0644);
                        }
                    }
                }
            }
            unset($categories);
        }
    }
    function isImage($image = false)
    {
        if (!$image) {
            return false;
        }
        $allowed_types = array(
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png'
        );
        $img_details   = getimagesize($image);
        if (in_array($img_details['mime'], $allowed_types)) {
            return true;
        }
        return false;
    }
}