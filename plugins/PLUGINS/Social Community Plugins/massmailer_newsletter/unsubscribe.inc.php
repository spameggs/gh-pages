<?php
$hash  = $rlValid->xSql($_GET['hash']);
$type  = $hash[0];
$email = substr($hash, 1, 32);
$date  = substr($hash, 33);
switch ($type) {
    case 1:
        $table = 'accounts';
        $field = 'Subscribe';
        $where = 'Mail';
        $value = 0;
        break;
    case 2:
        $table = 'subscribers';
        $field = 'Status';
        $where = 'Mail';
        $value = 'approval';
        break;
    case 3:
        $table = 'contacts';
        $where = 'Email';
        $field = 'Subscribe';
        $value = 0;
        break;
}
if ($table && $field && $where) {
    $id = $rlDb->getOne('ID', "MD5(`{$where}`) = '{$email}' AND MD5(`Date`) = '{$date}' AND `{$field}` <> '{$value}'", $table);
}
if ($id) {
    $reefless->loadClass('Actions');
    $update = array(
        'fields' => array(
            $field => $value
        ),
        'where' => array(
            'ID' => $id
        )
    );
    $rlActions->updateOne($update, $table);
    $reefless->loadClass('Notice');
    $rlNotice->saveNotice(str_replace('{sitename}', $GLOBALS['lang']['pages+title+home'], $lang['massmailer_newsletter_person_unsubscibed']));
} else {
    $errors[] = str_replace('{sitename}', $GLOBALS['lang']['pages+title+home'], $lang['massmailer_newsletter_incorrect_request']);
    $rlSmarty->assign_by_ref('errors', $errors);
}