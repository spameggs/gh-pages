<?php
if ($config['messages_module']) {
    $reefless->loadClass('Message');
    $id = (int) $_GET['id'];
    if ($id) {
        if (isset($_GET['administrator'])) {
            $contact              = $rlDb->fetch(array(
                'ID',
                'Name',
                'Email'
            ), array(
                'ID' => $id
            ), null, 1, 'admins', 'row');
            $contact['Full_name'] = $contact['Name'] ? $contact['Name'] : $lang['administrator'];
            $contact['Admin']     = 1;
        } else {
            $contact = $rlAccount->getProfile((int) $id);
        }
        $rlSmarty->assign_by_ref('contact', $contact);
        $messages = $rlMessage->getMessages($id);
        if (empty($messages)) {
            $sError = true;
        } else {
            $rlSmarty->assign_by_ref('messages', $messages);
            $bread_crumbs[]    = array(
                'name' => $lang['chat_with'] . ' ' . $contact['Full_name']
            );
            $page_info['name'] = $lang['chat_with'] . ' ' . $contact['Full_name'];
        }
        $message_info = $rlCommon->checkMessages();
        if (!empty($message_info)) {
            $rlSmarty->assign_by_ref('new_messages', $message_info);
        }
        $rlHook->load('messagesBottom');
        $rlXajax->registerFunction(array(
            'sendMessage',
            $rlMessage,
            'ajaxSendMessage'
        ));
        $rlXajax->registerFunction(array(
            'refreshMessagesArea',
            $rlMessage,
            'ajaxRefreshMessagesArea'
        ));
        $rlXajax->registerFunction(array(
            'removeMsg',
            $rlMessage,
            'ajaxRemoveMsg'
        ));
    } else {
        $contacts = $rlMessage->getContacts();
        $rlSmarty->assign_by_ref('contacts', $contacts);
        $rlXajax->registerFunction(array(
            'removeContacts',
            $rlMessage,
            'ajaxRemoveContacts'
        ));
    }
} else {
    $sError = true;
}