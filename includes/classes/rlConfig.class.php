<?php
class rlConfig extends reefless
{
    function getConfig($name)
    {
        if (empty($GLOBALS['config'])) {
            $output = $this->fetch(array(
                'Default'
            ), array(
                'Key' => $name
            ), null, null, 'config', 'row');
            return $output['Default'];
        } else {
            return $GLOBALS['config'][$name];
        }
    }
    function setConfig($key, $value)
    {
        $data = array(
            'fields' => array(
                'Default' => $value
            ),
            'where' => array(
                'Key' => $key
            )
        );
        if ($GLOBALS['rlActions']->updateOne($data, 'config')) {
            return true;
        }
        return false;
    }
    function allConfig($group = null)
    {
        $where  = !empty($group) ? array(
            'Group_ID' => $group
        ) : "*";
        $output = $this->fetch(array(
            'Key',
            'Default'
        ), $where, null, null, 'config');
        if (empty($GLOBALS['config'])) {
            foreach ($output as $key => $value) {
                $configs[$output[$key]['Key']] = $output[$key]['Default'];
            }
            $GLOBALS['config'] = $configs;
            return $configs;
        } else {
            return $GLOBALS['config'];
        }
    }
}