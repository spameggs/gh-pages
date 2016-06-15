<?php
$content = $rlDb->fetch(array(
    'Value'
), array(
    'Key' => 'pages+content+' . $page_info['Key'],
    'Code' => RL_LANG_CODE
), "AND `Status` = 'active'", null, 'lang_keys', 'row');
$rlSmarty->assign('staticContent', $content['Value']);
$rlHook->load('static');