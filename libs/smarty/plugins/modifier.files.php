<?php
function smarty_modifier_files($field = false, $parent = false)
{
    if (!$field)
        return false;
    if ($parent) {
        return (bool) $_FILES[$parent]['name'][$field];
    } else {
        return (bool) $_FILES[$field]['name'];
    }
}

?>
