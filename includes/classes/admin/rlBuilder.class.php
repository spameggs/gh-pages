<?php
class rlBuilder extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    var $rlAdmin;
    var $rlActions;
    var $rlNotice;
    var $rlBuildTable;
    var $rlBuildField;
    function rlBuilder()
    {
        global $rlLang, $rlValid, $rlConfig, $rlAdmin, $rlActions, $rlNotice;
        $this->rlBuildTable = 'listing_relations';
        $this->rlBuildField = 'Fields';
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
        $this->rlAdmin =& $rlAdmin;
        $this->rlActions =& $rlActions;
        $this->rlNotice =& $rlNotice;
    }
    function ajaxBuildForm($category_id = false, $data = false, $no_groups = false)
    {
        global $_response, $rlActions, $rlCache, $lang, $rlHook, $transfer;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        if (empty($category_id)) {
            $_response->script("
				build_in_progress = false;//FOR TEST
				$('#save_build_form').removeClass('bb_hover').find('span.center').html('{$lang['save']}');
			");
            return $_response;
        }
        $transfer = array(
            'category_id' => $category_id,
            'data' => $data
        );
        $rlHook->load('apAjaxBuildFormPreSaving');
        $sql = "DELETE FROM `" . RL_DBPREFIX . $this->rlBuildTable . "` WHERE `Category_ID` = '{$category_id}'";
        $this->query($sql);
        if (!trim($data['ordering'], ',')) {
            $_response->script("
				build_in_progress = false;//FOR TEST
				$('#save_build_form').removeClass('bb_hover').find('span.center').html('{$lang['save']}');
			");
            $rlCache->updateForms();
            return $_response;
        }
        unset($data['ordering']);
        $position = 1;
        foreach ($data as $item => $fields) {
            if (strpos($item, 'group') !== false) {
                $group      = explode('_', $item);
                $fields_out = '';
                if (!empty($fields[0])) {
                    foreach ($fields as $field) {
                        $tmp_field    = explode('_', $field);
                        $fields_out[] = $tmp_field[1];
                    }
                    $fields_out = implode(',', $fields_out);
                }
                $insert = array(
                    'Position' => $position,
                    'Category_ID' => $category_id,
                    'Group_ID' => $group[1],
                    $this->rlBuildField => $fields_out
                );
            } else {
                $fields_out = explode('_', $item);
                $insert     = array(
                    'Position' => $position,
                    'Category_ID' => $category_id,
                    'Group_ID' => 0,
                    $this->rlBuildField => $fields_out[1]
                );
            }
            if ($no_groups) {
                unset($insert['Group_ID']);
            }
            $rlActions->insertOne($insert, $this->rlBuildTable);
            $position++;
        }
        $rlHook->load('apAjaxBuildFormPostSaving');
        $rlCache->updateForms();
        $_response->script("
			build_in_progress = false;
			$('#save_build_form').removeClass('bb_hover').find('span.center').html('{$lang['save']}');
		");
        return $_response;
    }
    function getRelations($id)
    {
        $form = $this->fetch(array(
            'ID',
            'Category_ID',
            'Group_ID',
            $this->rlBuildField
        ), array(
            'Category_ID' => $id
        ), "ORDER BY `Position`", null, $this->rlBuildTable);
        foreach ($form as $key => $value) {
            $group_info = null;
            if ($form[$key]['Group_ID']) {
                $group_info = $this->fetch(array(
                    'Key',
                    'Status'
                ), array(
                    'ID' => $form[$key]['Group_ID']
                ), null, null, 'listing_groups', 'row');
                if (empty($group_info)) {
                    unset($form[$key]);
                    continue;
                } else {
                    $form[$key]['Key']    = $group_info['Key'];
                    $form[$key]['Status'] = $group_info['Status'];
                }
            }
            $fields = explode(',', $form[$key][$this->rlBuildField]);
            if (!empty($fields[0])) {
                $adapt = false;
                $res   = false;
                foreach ($fields as $field) {
                    $adapt_sql = "SELECT DISTINCT `ID`, `Key`, `Type`, `Status` FROM `" . RL_DBPREFIX . "listing_fields` WHERE `ID` = '{$field}' AND `Status` <> 'trash'";
                    if ($res = $this->getRow($adapt_sql)) {
                        if ($form[$key]['Group_ID']) {
                            $adapt[] = $res;
                        } else {
                            $adapt = $res;
                        }
                    }
                }
                $adapt                           = $this->rlLang->replaceLangKeys($adapt, 'listing_fields', array(
                    'name',
                    'default'
                ));
                $form[$key][$this->rlBuildField] = empty($adapt) ? false : $adapt;
            } else {
                unset($form[$key][$this->rlBuildField]);
            }
        }
        $form = $this->rlLang->replaceLangKeys($form, 'listing_groups', array(
            'name'
        ));
        return $form;
    }
    function getAvailableFields($id = false)
    {
        if (!$id)
            return false;
        $groups = $this->fetch(array(
            'Group_ID',
            'Fields'
        ), array(
            'Category_ID' => $id
        ), null, null, 'listing_relations');
        if (!$groups) {
            $general_cat_id = $GLOBALS['rlListingTypes']->types[$this->getOne('Type', "`ID` = '{$id}'", 'categories')]['Cat_general_cat'];
            if ($id == $general_cat_id) {
                return false;
            }
            if ($parent_id = $this->getOne('Parent_ID', "`ID` = '{$id}'", 'categories')) {
                return $this->getAvailableFields($parent_id);
            } else {
                if ($general_cat_id) {
                    return $this->getAvailableFields($general_cat_id);
                } else {
                    return false;
                }
            }
        } else {
            foreach ($groups as $group) {
                if ($group['Group_ID']) {
                    $tmp_fields = explode(',', $group['Fields']);
                    foreach ($tmp_fields as $field) {
                        if ($field) {
                            $out[] = $field;
                        }
                    }
                    unset($tmp_fields);
                } else {
                    $out[] = (int) $group['Fields'];
                }
            }
            unset($groups);
            return $out;
        }
    }
    function getFormRelations($id, $table = 'listing_fields')
    {
        $sql = "SELECT `T2`.`ID`, `T2`.`Key`, `T2`.`Type` FROM `" . RL_DBPREFIX . $this->rlBuildTable . "` AS `T1` ";
        $sql .= "RIGHT JOIN `" . RL_DBPREFIX . $table . "` AS `T2` ON `T1`.`Field_ID` = `T2`.`ID` ";
        $sql .= "WHERE `T1`.`Category_ID` = '{$id}' ";
        $sql .= "ORDER BY `T1`.`Position`";
        $fields = $this->getAll($sql);
        $fields = $this->rlLang->replaceLangKeys($fields, $table, array(
            'name'
        ));
        foreach ($fields as $key => $value) {
            $relations[$key] = array(
                'ID' => $fields[$key]['ID'],
                'Category_ID' => $id,
                'Fields' => $fields[$key]
            );
        }
        return $relations;
    }
}