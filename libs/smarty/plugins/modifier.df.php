<?php
function smarty_modifier_df($field = false)
{
    global $rlCategories;
    if (!$field)
        return false;
    return $rlCategories->getDF($field);
}

?>
