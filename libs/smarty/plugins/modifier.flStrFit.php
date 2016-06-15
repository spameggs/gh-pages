<?php
function smarty_modifier_flStrFit($string = false, $pos = false, $char = '-')
{
    global $reefless;
    return $reefless->flStrSplit($string, $pos, $char);
}

?>
