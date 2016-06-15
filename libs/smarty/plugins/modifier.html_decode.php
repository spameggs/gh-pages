<?php
function smarty_modifier_html_decode($string = false)
{
    return html_entity_decode($string, null, 'utf-8');
}

?>
