<?php
class rlHook extends reefless
{
    var $rlValid;
    var $rlCommon;
    var $rlHooks;
    var $index = 1;
    function rlHook()
    {
        global $rlValid, $rlCommon;
        $this->rlValid  = $rlValid;
        $this->rlCommon = $rlCommon;
        $this->getHooks();
    }
    function getHooks()
    {
        $this->setTable('hooks');
        $tmp_hooks = $this->fetch(array(
            'Name',
            'Code'
        ), array(
            'Status' => 'active'
        ));
        $this->resetTable();
        foreach ($tmp_hooks as $key => $value) {
            if (!$hooks[$tmp_hooks[$key]['Name']]) {
                $hooks[$tmp_hooks[$key]['Name']] = $tmp_hooks[$key]['Code'];
            } else {
                $tmp_hook = $hooks[$tmp_hooks[$key]['Name']];
                unset($hooks[$tmp_hooks[$key]['Name']]);
                if (is_array($tmp_hook)) {
                    $tmp_hook[]                      = $tmp_hooks[$key]['Code'];
                    $hooks[$tmp_hooks[$key]['Name']] = $tmp_hook;
                } else {
                    $hooks[$tmp_hooks[$key]['Name']][] = $tmp_hooks[$key]['Code'];
                    $hooks[$tmp_hooks[$key]['Name']][] = $tmp_hook;
                }
            }
            unset($tmp_hook);
        }
        unset($tmp_hooks);
        $this->rlHooks    = $hooks;
        $GLOBALS['hooks'] = $hooks;
    }
    function load($name = false, &$param1, &$param2, &$param3, &$param4, &$param5)
    {
        if (is_array($name)) {
            $name  = $name['name'];
            $hooks = $GLOBALS['hooks'];
        } else {
            $hooks = $this->rlHooks;
        }
        $code = isset($hooks[$name]) ? $hooks[$name] : '';
        if (!empty($code)) {
            if (is_array($code)) {
                foreach ($code as $item) {
                    $func    = "{$name}Hook" . $this->index;
                    $wrapper = "function {$func}(&\$param1, &\$param2, &\$param3, &\$param4, &\$param5) { " . PHP_EOL;
                    $wrapper .= "[code]" . PHP_EOL;
                    $wrapper .= "}";
                    eval(str_replace('[code]', $item, $wrapper));
                    $func(&$param1, &$param2, &$param3, &$param4, &$param5);
                    $this->index++;
                }
            } else {
                $func    = "{$name}Hook" . $this->index;
                $wrapper = "function {$func}(&\$param1, &\$param2, &\$param3, &\$param4, &\$param5) { " . PHP_EOL;
                $wrapper .= "[code]" . PHP_EOL;
                $wrapper .= "}";
                eval(str_replace('[code]', $code, $wrapper));
                $func(&$param1, &$param2, &$param3, &$param4, &$param5);
                $this->index++;
            }
        }
    }
}