<?php
class rlReCaptcha extends reefless
{
    function rlReCaptcha($mode = false)
    {
        $this->clearCompile($mode);
    }
    function clearCompile($mode = false)
    {
        global $config;
        if ($mode) {
            $compile = $this->scanDir(RL_TMP . 'compile');
            foreach ($compile as $file) {
                unlink(RL_TMP . 'compile' . RL_DS . $file);
            }
        } else {
            $group_id = $this->getOne('ID', "`Key` = 'reCaptcha'", 'config_groups');
            if ($_POST['group_id'] == $group_id && !$config['reCaptcha_module'] && $_POST['config']['reCaptcha_module']['value']) {
                $compile = $this->scanDir(RL_TMP . 'compile');
                foreach ($compile as $file) {
                    unlink(RL_TMP . 'compile' . RL_DS . $file);
                }
            }
        }
    }
}