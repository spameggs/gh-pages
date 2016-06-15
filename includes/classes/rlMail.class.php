<?php
class rlMail extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    var $rlActions;
    var $phpMailer;
    function rlMail()
    {
        global $rlLang, $rlValid, $rlConfig, $rlActions;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
        $this->rlActions =& $rlActions;
        $php_version = version_compare(phpversion(), "5", ">") ? 5 : 4;
        include_once(RL_LIBS . 'phpmailer' . RL_DS . 'php' . $php_version . RL_DS . 'class.phpmailer.php');
        $this->phpMailer = new PHPMailer();
    }
    function send($mail_tpl, $to, $attach_file = false, $from_mail = false, $from_name = false)
    {
        global $config, $lang;
        if (!$mail_tpl['body']) {
            return false;
        }
        if ($GLOBALS['config']['mail_method'] == 'smtp') {
            $this->phpMailer->IsSMTP();
            $this->phpMailer->Host     = $config['smtp_server'];
            $this->phpMailer->Username = $config['smtp_username'];
            $this->phpMailer->Password = $config['smtp_password'];
            if (empty($config['smtp_username']) && empty($config['smtp_password'])) {
                $this->phpMailer->SMTPAuth = false;
            }
        }
        $subject = $mail_tpl['subject'];
        $body    = $mail_tpl['body'];
        if ($mail_tpl['Type'] == 'html') {
            $tpl_base    = RL_ROOT . 'templates' . RL_DS . $config['template'] . RL_DS;
            $tpl_url     = RL_URL_HOME . 'templates/' . $config['template'] . '/';
            $html_source = $this->getPageContent($tpl_base . 'tpl' . RL_DS . 'html_email_source.html');
            if ($html_source) {
                $find    = array(
                    '{content}',
                    '{tpl_base}',
                    '{site_name}',
                    '{footer}',
                    '{site_url}'
                );
                $replace = array(
                    $body,
                    $tpl_url,
                    $lang['pages+title+home'],
                    $lang['email_footer'],
                    RL_URL_HOME
                );
                $body    = str_replace($find, $replace, $html_source);
            }
        }
        $this->phpMailer->From     = $from_mail ? $from_mail : $config['site_main_email'];
        $this->phpMailer->FromName = $from_name ? $from_name : $config['owner_name'];
        $this->phpMailer->Subject  = $subject;
        $this->phpMailer->AltBody  = "To view the message, please use an HTML compatible email viewer!";
        $this->phpMailer->MsgHTML($body);
        $this->phpMailer->AddAddress($to);
        if ($attach_file) {
            $this->phpMailer->AddAttachment($attach_file);
        }
        if (!$this->phpMailer->Send()) {
            trigger_error($mail->ErrorInfo, E_USER_WARNING);
            $this->phpMailer->ClearAddresses();
            $GLOBALS['rlDebug']->logger($mail->ErrorInfo);
        } else {
            $this->phpMailer->ClearAddresses();
            return true;
        }
    }
    function getEmailTemplate($key)
    {
        $output = $this->fetch(array(
            'Key',
            'Type'
        ), array(
            'Key' => $key,
            'Status' => 'active'
        ), null, null, 'email_templates', 'row');
        $output = $this->rlLang->replaceLangKeys($output, 'email_templates', array(
            'subject',
            'body'
        ), RL_LANG_CODE, 'email_tpl');
        $output = $this->replaceVariables($output);
        return empty($output) ? false : $output;
    }
    function replaceVariables($mail_tpl)
    {
        global $account_info, $lang, $config;
        $tpl_vars = array(
            '{site_name}' => $lang['pages+title+home'],
            '{site_url}' => '<a href="' . RL_URL_HOME . '">' . RL_URL_HOME . '</a>',
            '{site_email}' => '<a href="mailto:' . $config['site_main_email'] . '">' . $config['site_main_email'] . '</a>'
        );
        if (!empty($account_info['Full_name']) && !defined('REALM')) {
            $tpl_vars['{username}'] = $account_info['Full_name'];
        }
        $mail_tpl['body'] = str_replace(PHP_EOL, '<br />', $mail_tpl['body']);
        foreach ($tpl_vars as $key => $value) {
            $mail_tpl['subject'] = str_replace($key, $value, $mail_tpl['subject']);
            $mail_tpl['body']    = str_replace($key, $value, $mail_tpl['body']);
        }
        return $mail_tpl;
    }
}