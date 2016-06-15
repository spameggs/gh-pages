<?php
class rlLocationFinder extends reefless
{
    function ajaxSave($position = false, $type = false)
    {
        global $_response, $lang, $rlActions, $rlNotice;
        $update = array(
            array(
                'fields' => array(
                    'Default' => $position
                ),
                'where' => array(
                    'Key' => 'locationFinder_position'
                )
            ),
            array(
                'fields' => array(
                    'Default' => $type
                ),
                'where' => array(
                    'Key' => 'locationFinder_type'
                )
            )
        );
        $rlActions->update($update, 'config');
        $_response->script("
			printMessage('notice', '{$lang['locationFinder_settings_saved']}');
			$('#lf_button').val('{$lang['save']}').attr('disabled', false);
		");
        return $_response;
    }
    function assignLocation()
    {
        global $lf_listing_id;
        if (!$lf_listing_id)
            return;
        $this->loadClass('Actions');
        $data = $_POST['f']['lf'];
        if ($data['use'] && $data['lat'] != '' && $data['lng'] != '') {
            $update = array(
                'fields' => array(
                    'Loc_latitude' => $data['lat'],
                    'Loc_longitude' => $data['lng'],
                    'lf_zoom' => $data['zoom'],
                    'lf_use' => $data['use']
                ),
                'where' => array(
                    'ID' => $lf_listing_id
                )
            );
            $GLOBALS['rlActions']->updateOne($update, 'listings');
        } else if (!$data['use']) {
            $update = array(
                'fields' => array(
                    'lf_zoom' => '',
                    'lf_use' => '0'
                ),
                'where' => array(
                    'ID' => $_SESSION['add_listing']['listing_id']
                )
            );
            $GLOBALS['rlActions']->updateOne($update, 'listings');
        }
    }
}