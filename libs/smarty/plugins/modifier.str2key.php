<?php
function smarty_modifier_str2key($string = false)
{
    global $rlValid;
    return $rlValid->str2key($string);
}

?>
