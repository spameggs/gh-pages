<?php
class PHPMailer
{
    var $Priority = 3;
    var $CharSet = 'utf-8';
    var $ContentType = 'text/plain';
    var $Encoding = '8bit';
    var $ErrorInfo = '';
    var $From = 'root@localhost';
    var $FromName = 'Root User';
    var $Sender = '';
    var $Subject = '';
    var $Body = '';
    var $AltBody = '';
    var $WordWrap = 0;
    var $Mailer = 'mail';
    var $Sendmail = '/usr/sbin/sendmail';
    var $PluginDir = '';
    var $Version = "2.1";
    var $ConfirmReadingTo = '';
    var $Hostname = '';
    var $MessageID = '';
    var $Host = 'localhost';
    var $Port = 25;
    var $Helo = '';
    var $SMTPSecure = "";
    var $SMTPAuth = false;
    var $Username = '';
    var $Password = '';
    var $Timeout = 10;
    var $SMTPDebug = false;
    var $SMTPKeepAlive = false;
    var $SingleTo = false;
    var $smtp = NULL;
    var $to = array();
    var $cc = array();
    var $bcc = array();
    var $ReplyTo = array();
    var $attachment = array();
    var $CustomHeader = array();
    var $message_type = '';
    var $boundary = array();
    var $language = array();
    var $error_count = 0;
    var $LE = "\n";
    var $sign_key_file = "";
    var $sign_key_pass = "";
    function IsHTML($bool)
    {
        if ($bool == true) {
            $this->ContentType = 'text/html';
        } else {
            $this->ContentType = 'text/plain';
        }
    }
    function IsSMTP()
    {
        $this->Mailer = 'smtp';
    }
    function IsMail()
    {
        $this->Mailer = 'mail';
    }
    function IsSendmail()
    {
        $this->Mailer = 'sendmail';
    }
    function IsQmail()
    {
        $this->Sendmail = '/var/qmail/bin/sendmail';
        $this->Mailer   = 'sendmail';
    }
    function AddAddress($address, $name = '')
    {
        $cur               = count($this->to);
        $this->to[$cur][0] = trim($address);
        $this->to[$cur][1] = $name;
    }
    function AddCC($address, $name = '')
    {
        $cur               = count($this->cc);
        $this->cc[$cur][0] = trim($address);
        $this->cc[$cur][1] = $name;
    }
    function AddBCC($address, $name = '')
    {
        $cur                = count($this->bcc);
        $this->bcc[$cur][0] = trim($address);
        $this->bcc[$cur][1] = $name;
    }
    function AddReplyTo($address, $name = '')
    {
        $cur                    = count($this->ReplyTo);
        $this->ReplyTo[$cur][0] = trim($address);
        $this->ReplyTo[$cur][1] = $name;
    }
    function Send()
    {
        $header = '';
        $body   = '';
        $result = true;
        if ((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
            $this->SetError($this->Lang('provide_address'));
            return false;
        }
        if (!empty($this->AltBody)) {
            $this->ContentType = 'multipart/alternative';
        }
        $this->error_count = 0;
        $this->SetMessageType();
        $header .= $this->CreateHeader();
        $body = $this->CreateBody();
        if ($body == '') {
            return false;
        }
        switch ($this->Mailer) {
            case 'sendmail':
                $result = $this->SendmailSend($header, $body);
                break;
            case 'smtp':
                $result = $this->SmtpSend($header, $body);
                break;
            case 'mail':
                $result = $this->MailSend($header, $body);
                break;
            default:
                $result = $this->MailSend($header, $body);
                break;
        }
        return $result;
    }
    function SendmailSend($header, $body)
    {
        if ($this->Sender != '') {
            $sendmail = sprintf("%s -oi -f %s -t", escapeshellcmd($this->Sendmail), escapeshellarg($this->Sender));
        } else {
            $sendmail = sprintf("%s -oi -t", escapeshellcmd($this->Sendmail));
        }
        if (!@$mail = popen($sendmail, 'w')) {
            $this->SetError($this->Lang('execute') . $this->Sendmail);
            return false;
        }
        fputs($mail, $header);
        fputs($mail, $body);
        $result = pclose($mail);
        if (version_compare(phpversion(), '4.2.3') == -1) {
            $result = $result >> 8 & 0xFF;
        }
        if ($result != 0) {
            $this->SetError($this->Lang('execute') . $this->Sendmail);
            return false;
        }
        return true;
    }
    function MailSend($header, $body)
    {
        $to = '';
        for ($i = 0; $i < count($this->to); $i++) {
            if ($i != 0) {
                $to .= ', ';
            }
            $to .= $this->AddrFormat($this->to[$i]);
        }
        $toArr  = split(',', $to);
        $params = sprintf("-oi -f %s", $this->Sender);
        if ($this->Sender != '' && strlen(ini_get('safe_mode')) < 1) {
            $old_from = ini_get('sendmail_from');
            ini_set('sendmail_from', $this->Sender);
            if ($this->SingleTo === true && count($toArr) > 1) {
                foreach ($toArr as $key => $val) {
                    $rt = @mail($val, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
                }
            } else {
                $rt = @mail($to, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
            }
        } else {
            if ($this->SingleTo === true && count($toArr) > 1) {
                foreach ($toArr as $key => $val) {
                    $rt = @mail($val, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
                }
            } else {
                $rt = @mail($to, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header);
            }
        }
        if (isset($old_from)) {
            ini_set('sendmail_from', $old_from);
        }
        if (!$rt) {
            $this->SetError($this->Lang('instantiate'));
            return false;
        }
        return true;
    }
    function SmtpSend($header, $body)
    {
        include_once($this->PluginDir . 'class.smtp.php');
        $error    = '';
        $bad_rcpt = array();
        if (!$this->SmtpConnect()) {
            return false;
        }
        $smtp_from = ($this->Sender == '') ? $this->From : $this->Sender;
        if (!$this->smtp->Mail($smtp_from)) {
            $error = $this->Lang('from_failed') . $smtp_from;
            $this->SetError($error);
            $this->smtp->Reset();
            return false;
        }
        for ($i = 0; $i < count($this->to); $i++) {
            if (!$this->smtp->Recipient($this->to[$i][0])) {
                $bad_rcpt[] = $this->to[$i][0];
            }
        }
        for ($i = 0; $i < count($this->cc); $i++) {
            if (!$this->smtp->Recipient($this->cc[$i][0])) {
                $bad_rcpt[] = $this->cc[$i][0];
            }
        }
        for ($i = 0; $i < count($this->bcc); $i++) {
            if (!$this->smtp->Recipient($this->bcc[$i][0])) {
                $bad_rcpt[] = $this->bcc[$i][0];
            }
        }
        if (count($bad_rcpt) > 0) {
            for ($i = 0; $i < count($bad_rcpt); $i++) {
                if ($i != 0) {
                    $error .= ', ';
                }
                $error .= $bad_rcpt[$i];
            }
            $error = $this->Lang('recipients_failed') . $error;
            $this->SetError($error);
            $this->smtp->Reset();
            return false;
        }
        if (!$this->smtp->Data($header . $body)) {
            $this->SetError($this->Lang('data_not_accepted'));
            $this->smtp->Reset();
            return false;
        }
        if ($this->SMTPKeepAlive == true) {
            $this->smtp->Reset();
        } else {
            $this->SmtpClose();
        }
        return true;
    }
    function SmtpConnect()
    {
        if ($this->smtp == NULL) {
            $this->smtp = new SMTP();
        }
        $this->smtp->do_debug = $this->SMTPDebug;
        $hosts                = explode(';', $this->Host);
        $index                = 0;
        $connection           = ($this->smtp->Connected());
        while ($index < count($hosts) && $connection == false) {
            $hostinfo = array();
            if (eregi('^(.+):([0-9]+)$', $hosts[$index], $hostinfo)) {
                $host = $hostinfo[1];
                $port = $hostinfo[2];
            } else {
                $host = $hosts[$index];
                $port = $this->Port;
            }
            $tls = ($this->SMTPSecure == 'tls');
            $ssl = ($this->SMTPSecure == 'ssl');
            if ($this->smtp->Connect(($ssl ? 'ssl://' : '') . $host, $port, $this->Timeout)) {
                $hello = ($this->Helo != '' ? $this->Hello : $this->ServerHostname());
                $this->smtp->Hello($hello);
                if ($tls) {
                    if (!$this->smtp->StartTLS()) {
                        $this->SetError($this->Lang("tls"));
                        $this->smtp->Reset();
                        $connection = false;
                    }
                    $this->smtp->Hello($hello);
                }
                $connection = true;
                if ($this->SMTPAuth) {
                    if (!$this->smtp->Authenticate($this->Username, $this->Password)) {
                        $this->SetError($this->Lang('authenticate'));
                        $this->smtp->Reset();
                        $connection = false;
                    }
                }
            }
            $index++;
        }
        if (!$connection) {
            $this->SetError($this->Lang('connect_host'));
        }
        return $connection;
    }
    function SmtpClose()
    {
        if ($this->smtp != NULL) {
            if ($this->smtp->Connected()) {
                $this->smtp->Quit();
                $this->smtp->Close();
            }
        }
    }
    function SetLanguage($lang_type = 'en', $lang_path = 'language/')
    {
        if (!(@include $lang_path . 'phpmailer.lang-' . $lang_type . '.php')) {
            $this->SetError('Could not load language file');
            return false;
        }
        $this->language = $PHPMAILER_LANG;
        return true;
    }
    function AddrAppend($type, $addr)
    {
        $addr_str = $type . ': ';
        $addr_str .= $this->AddrFormat($addr[0]);
        if (count($addr) > 1) {
            for ($i = 1; $i < count($addr); $i++) {
                $addr_str .= ', ' . $this->AddrFormat($addr[$i]);
            }
        }
        $addr_str .= $this->LE;
        return $addr_str;
    }
    function AddrFormat($addr)
    {
        if (empty($addr[1])) {
            $formatted = $this->SecureHeader($addr[0]);
        } else {
            $formatted = $this->EncodeHeader($this->SecureHeader($addr[1]), 'phrase') . " <" . $this->SecureHeader($addr[0]) . ">";
        }
        return $formatted;
    }
    function WrapText($message, $length, $qp_mode = false)
    {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;
        $is_utf8    = (strtolower($this->CharSet) == "utf-8");
        $message    = $this->FixEOL($message);
        if (substr($message, -1) == $this->LE) {
            $message = substr($message, 0, -1);
        }
        $line    = explode($this->LE, $message);
        $message = '';
        for ($i = 0; $i < count($line); $i++) {
            $line_part = explode(' ', $line[$i]);
            $buf       = '';
            for ($e = 0; $e < count($line_part); $e++) {
                $word = $line_part[$e];
                if ($qp_mode and (strlen($word) > $length)) {
                    $space_left = $length - strlen($buf) - 1;
                    if ($e != 0) {
                        if ($space_left > 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this->UTF8CharBoundary($word, $len);
                            } elseif (substr($word, $len - 1, 1) == "=") {
                                $len--;
                            } elseif (substr($word, $len - 2, 1) == "=") {
                                $len -= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $part;
                            $message .= $buf . sprintf("=%s", $this->LE);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) > 0) {
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this->UTF8CharBoundary($word, $len);
                        } elseif (substr($word, $len - 1, 1) == "=") {
                            $len--;
                        } elseif (substr($word, $len - 2, 1) == "=") {
                            $len -= 2;
                        }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        if (strlen($word) > 0) {
                            $message .= $part . sprintf("=%s", $this->LE);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    $buf .= ($e == 0) ? $word : (' ' . $word);
                    if (strlen($buf) > $length and $buf_o != '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
            }
            $message .= $buf . $this->LE;
        }
        return $message;
    }
    function UTF8CharBoundary($encodedText, $maxLength)
    {
        $foundSplitPos = false;
        $lookBack      = 3;
        while (!$foundSplitPos) {
            $lastChunk      = substr($encodedText, $maxLength - $lookBack, $lookBack);
            $encodedCharPos = strpos($lastChunk, "=");
            if ($encodedCharPos !== false) {
                $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
                $dec = hexdec($hex);
                if ($dec < 128) {
                    $maxLength     = ($encodedCharPos == 0) ? $maxLength : $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec >= 192) {
                    $maxLength     = $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec < 192) {
                    $lookBack += 3;
                }
            } else {
                $foundSplitPos = true;
            }
        }
        return $maxLength;
    }
    function SetWordWrap()
    {
        if ($this->WordWrap < 1) {
            return;
        }
        switch ($this->message_type) {
            case 'alt':
            case 'alt_attachments':
                $this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
                break;
            default:
                $this->Body = $this->WrapText($this->Body, $this->WordWrap);
                break;
        }
    }
    function CreateHeader()
    {
        $result            = '';
        $uniq_id           = md5(uniqid(time()));
        $this->boundary[1] = 'b1_' . $uniq_id;
        $this->boundary[2] = 'b2_' . $uniq_id;
        $result .= $this->HeaderLine('Date', $this->RFCDate());
        if ($this->Sender == '') {
            $result .= $this->HeaderLine('Return-Path', trim($this->From));
        } else {
            $result .= $this->HeaderLine('Return-Path', trim($this->Sender));
        }
        if ($this->Mailer != 'mail') {
            if (count($this->to) > 0) {
                $result .= $this->AddrAppend('To', $this->to);
            } elseif (count($this->cc) == 0) {
                $result .= $this->HeaderLine('To', 'undisclosed-recipients:;');
            }
            if (count($this->cc) > 0) {
                $result .= $this->AddrAppend('Cc', $this->cc);
            }
        }
        $from       = array();
        $from[0][0] = trim($this->From);
        $from[0][1] = $this->FromName;
        $result .= $this->AddrAppend('From', $from);
        if ((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->cc) > 0)) {
            $result .= $this->AddrAppend('Cc', $this->cc);
        }
        if ((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->bcc) > 0)) {
            $result .= $this->AddrAppend('Bcc', $this->bcc);
        }
        if (count($this->ReplyTo) > 0) {
            $result .= $this->AddrAppend('Reply-to', $this->ReplyTo);
        }
        if ($this->Mailer != 'mail') {
            $result .= $this->HeaderLine('Subject', $this->EncodeHeader($this->SecureHeader($this->Subject)));
        }
        if ($this->MessageID != '') {
            $result .= $this->HeaderLine('Message-ID', $this->MessageID);
        } else {
            $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
        }
        $result .= $this->HeaderLine('X-Priority', $this->Priority);
        $result .= $this->HeaderLine('X-Mailer', 'PHPMailer (phpmailer.codeworxtech.com) [version ' . $this->Version . ']');
        if ($this->ConfirmReadingTo != '') {
            $result .= $this->HeaderLine('Disposition-Notification-To', '<' . trim($this->ConfirmReadingTo) . '>');
        }
        for ($index = 0; $index < count($this->CustomHeader); $index++) {
            $result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
        }
        if (!$this->sign_key_file) {
            $result .= $this->HeaderLine('MIME-Version', '1.0');
            $result .= $this->GetMailMIME();
        }
        return $result;
    }
    function GetMailMIME()
    {
        $result = '';
        switch ($this->message_type) {
            case 'plain':
                $result .= $this->HeaderLine('Content-Transfer-Encoding', $this->Encoding);
                $result .= sprintf("Content-Type: %s; charset=\"%s\"", $this->ContentType, $this->CharSet);
                break;
            case 'attachments':
            case 'alt_attachments':
                if ($this->InlineImageExists()) {
                    $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 'multipart/related', $this->LE, $this->LE, $this->boundary[1], $this->LE);
                } else {
                    $result .= $this->HeaderLine('Content-Type', 'multipart/mixed;');
                    $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                }
                break;
            case 'alt':
                $result .= $this->HeaderLine('Content-Type', 'multipart/alternative;');
                $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
        }
        if ($this->Mailer != 'mail') {
            $result .= $this->LE . $this->LE;
        }
        return $result;
    }
    function CreateBody()
    {
        $result = '';
        if ($this->sign_key_file) {
            $result .= $this->GetMailMIME();
        }
        $this->SetWordWrap();
        switch ($this->message_type) {
            case 'alt':
                $result .= $this->GetBoundary($this->boundary[1], '', 'text/plain', '');
                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE . $this->LE;
                $result .= $this->GetBoundary($this->boundary[1], '', 'text/html', '');
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE . $this->LE;
                $result .= $this->EndBoundary($this->boundary[1]);
                break;
            case 'plain':
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                break;
            case 'attachments':
                $result .= $this->GetBoundary($this->boundary[1], '', '', '');
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE;
                $result .= $this->AttachAll();
                break;
            case 'alt_attachments':
                $result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
                $result .= sprintf("Content-Type: %s;%s" . "\tboundary=\"%s\"%s", 'multipart/alternative', $this->LE, $this->boundary[2], $this->LE . $this->LE);
                $result .= $this->GetBoundary($this->boundary[2], '', 'text/plain', '') . $this->LE;
                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE . $this->LE;
                $result .= $this->GetBoundary($this->boundary[2], '', 'text/html', '') . $this->LE;
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE . $this->LE;
                $result .= $this->EndBoundary($this->boundary[2]);
                $result .= $this->AttachAll();
                break;
        }
        if ($this->IsError()) {
            $result = '';
        } else if ($this->sign_key_file) {
            $file = tempnam("", "mail");
            $fp   = fopen($file, "w");
            fwrite($fp, $result);
            fclose($fp);
            $signed = tempnam("", "signed");
            if (@openssl_pkcs7_sign($file, $signed, "file://" . $this->sign_key_file, array(
                "file://" . $this->sign_key_file,
                $this->sign_key_pass
            ), null)) {
                $fp     = fopen($signed, "r");
                $result = fread($fp, filesize($this->sign_key_file));
                fclose($fp);
            } else {
                $this->SetError($this->Lang("signing") . openssl_error_string());
                $result = '';
            }
            unlink($file);
            unlink($signed);
        }
        return $result;
    }
    function GetBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = '';
        if ($charSet == '') {
            $charSet = $this->CharSet;
        }
        if ($contentType == '') {
            $contentType = $this->ContentType;
        }
        if ($encoding == '') {
            $encoding = $this->Encoding;
        }
        $result .= $this->TextLine('--' . $boundary);
        $result .= sprintf("Content-Type: %s; charset = \"%s\"", $contentType, $charSet);
        $result .= $this->LE;
        $result .= $this->HeaderLine('Content-Transfer-Encoding', $encoding);
        $result .= $this->LE;
        return $result;
    }
    function EndBoundary($boundary)
    {
        return $this->LE . '--' . $boundary . '--' . $this->LE;
    }
    function SetMessageType()
    {
        if (count($this->attachment) < 1 && strlen($this->AltBody) < 1) {
            $this->message_type = 'plain';
        } else {
            if (count($this->attachment) > 0) {
                $this->message_type = 'attachments';
            }
            if (strlen($this->AltBody) > 0 && count($this->attachment) < 1) {
                $this->message_type = 'alt';
            }
            if (strlen($this->AltBody) > 0 && count($this->attachment) > 0) {
                $this->message_type = 'alt_attachments';
            }
        }
    }
    function HeaderLine($name, $value)
    {
        return $name . ': ' . $value . $this->LE;
    }
    function TextLine($value)
    {
        return $value . $this->LE;
    }
    function AddAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
    {
        if (!@is_file($path)) {
            $this->SetError($this->Lang('file_access') . $path);
            return false;
        }
        $filename = basename($path);
        if ($name == '') {
            $name = $filename;
        }
        $cur                       = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false;
        $this->attachment[$cur][6] = 'attachment';
        $this->attachment[$cur][7] = 0;
        return true;
    }
    function AttachAll()
    {
        $mime = array();
        for ($i = 0; $i < count($this->attachment); $i++) {
            $bString = $this->attachment[$i][5];
            if ($bString) {
                $string = $this->attachment[$i][0];
            } else {
                $path = $this->attachment[$i][0];
            }
            $filename    = $this->attachment[$i][1];
            $name        = $this->attachment[$i][2];
            $encoding    = $this->attachment[$i][3];
            $type        = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid         = $this->attachment[$i][7];
            $mime[]      = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[]      = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[]      = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);
            if ($disposition == 'inline') {
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
            }
            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, $name, $this->LE . $this->LE);
            if ($bString) {
                $mime[] = $this->EncodeString($string, $encoding);
                if ($this->IsError()) {
                    return '';
                }
                $mime[] = $this->LE . $this->LE;
            } else {
                $mime[] = $this->EncodeFile($path, $encoding);
                if ($this->IsError()) {
                    return '';
                }
                $mime[] = $this->LE . $this->LE;
            }
        }
        $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);
        return join('', $mime);
    }
    function EncodeFile($path, $encoding = 'base64')
    {
        if (!@$fd = fopen($path, 'rb')) {
            $this->SetError($this->Lang('file_open') . $path);
            return '';
        }
        $magic_quotes = get_magic_quotes_runtime();
        set_magic_quotes_runtime(0);
        $file_buffer = file_get_contents($path);
        $file_buffer = $this->EncodeString($file_buffer, $encoding);
        fclose($fd);
        set_magic_quotes_runtime($magic_quotes);
        return $file_buffer;
    }
    function EncodeString($str, $encoding = 'base64')
    {
        $encoded = '';
        switch (strtolower($encoding)) {
            case 'base64':
                $encoded = chunk_split(base64_encode($str), 76, $this->LE);
                break;
            case '7bit':
            case '8bit':
                $encoded = $this->FixEOL($str);
                if (substr($encoded, -(strlen($this->LE))) != $this->LE)
                    $encoded .= $this->LE;
                break;
            case 'binary':
                $encoded = $str;
                break;
            case 'quoted-printable':
                $encoded = $this->EncodeQP($str);
                break;
            default:
                $this->SetError($this->Lang('encoding') . $encoding);
                break;
        }
        return $encoded;
    }
    function EncodeHeader($str, $position = 'text')
    {
        $x = 0;
        switch (strtolower($position)) {
            case 'phrase':
                if (!preg_match('/[\200-\377]/', $str)) {
                    $encoded = addcslashes($str, "\0..\37\177\\\"");
                    if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                        return ($encoded);
                    } else {
                        return ("\"$encoded\"");
                    }
                }
                $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                break;
            case 'comment':
                $x = preg_match_all('/[()"]/', $str, $matches);
            case 'text':
            default:
                $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                break;
        }
        if ($x == 0) {
            return ($str);
        }
        $maxlen = 75 - 7 - strlen($this->CharSet);
        if (strlen($str) / 3 < $x) {
            $encoding = 'B';
            if (function_exists('mb_strlen') && $this->HasMultiBytes($str)) {
                $encoded = $this->Base64EncodeWrapMB($str);
            } else {
                $encoded = base64_encode($str);
                $maxlen -= $maxlen % 4;
                $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
            }
        } else {
            $encoding = 'Q';
            $encoded  = $this->EncodeQ($str, $position);
            $encoded  = $this->WrapText($encoded, $maxlen, true);
            $encoded  = str_replace('=' . $this->LE, "\n", trim($encoded));
        }
        $encoded = preg_replace('/^(.*)$/m', " =?" . $this->CharSet . "?$encoding?\\1?=", $encoded);
        $encoded = trim(str_replace("\n", $this->LE, $encoded));
        return $encoded;
    }
    function HasMultiBytes($str)
    {
        if (function_exists('mb_strlen')) {
            return (strlen($str) > mb_strlen($str, $this->CharSet));
        } else {
            return False;
        }
    }
    function Base64EncodeWrapMB($str)
    {
        $start     = "=?" . $this->CharSet . "?B?";
        $end       = "?=";
        $encoded   = "";
        $mb_length = mb_strlen($str, $this->CharSet);
        $length    = 75 - strlen($start) - strlen($end);
        $ratio     = $mb_length / strlen($str);
        $offset    = $avgLength = floor($length * $ratio * .75);
        for ($i = 0; $i < $mb_length; $i += $offset) {
            $lookBack = 0;
            do {
                $offset = $avgLength - $lookBack;
                $chunk  = mb_substr($str, $i, $offset, $this->CharSet);
                $chunk  = base64_encode($chunk);
                $lookBack++;
            } while (strlen($chunk) > $length);
            $encoded .= $chunk . $this->LE;
        }
        $encoded = substr($encoded, 0, -strlen($this->LE));
        return $encoded;
    }
    function EncodeQP($input = '', $line_max = 76, $space_conv = false)
    {
        $hex    = array(
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F'
        );
        $lines  = preg_split('/(?:\r\n|\r|\n)/', $input);
        $eol    = "\r\n";
        $escape = '=';
        $output = '';
        while (list(, $line) = each($lines)) {
            $linlen  = strlen($line);
            $newline = '';
            for ($i = 0; $i < $linlen; $i++) {
                $c   = substr($line, $i, 1);
                $dec = ord($c);
                if (($i == 0) && ($dec == 46)) {
                    $c = '=2E';
                }
                if ($dec == 32) {
                    if ($i == ($linlen - 1)) {
                        $c = '=20';
                    } else if ($space_conv) {
                        $c = '=20';
                    }
                } elseif (($dec == 61) || ($dec < 32) || ($dec > 126)) {
                    $h2 = floor($dec / 16);
                    $h1 = floor($dec % 16);
                    $c  = $escape . $hex[$h2] . $hex[$h1];
                }
                if ((strlen($newline) + strlen($c)) >= $line_max) {
                    $output .= $newline . $escape . $eol;
                    $newline = '';
                    if ($dec == 46) {
                        $c = '=2E';
                    }
                }
                $newline .= $c;
            }
            $output .= $newline . $eol;
        }
        return trim($output);
    }
    function EncodeQ($str, $position = 'text')
    {
        $encoded = preg_replace("[\r\n]", '', $str);
        switch (strtolower($position)) {
            case 'phrase':
                $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
                break;
            case 'comment':
                $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
            case 'text':
            default:
                $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e', "'='.sprintf('%02X', ord('\\1'))", $encoded);
                break;
        }
        $encoded = str_replace(' ', '_', $encoded);
        return $encoded;
    }
    function AddStringAttachment($string, $filename, $encoding = 'base64', $type = 'application/octet-stream')
    {
        $cur                       = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true;
        $this->attachment[$cur][6] = 'attachment';
        $this->attachment[$cur][7] = 0;
    }
    function AddEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
    {
        if (!@is_file($path)) {
            $this->SetError($this->Lang('file_access') . $path);
            return false;
        }
        $filename = basename($path);
        if ($name == '') {
            $name = $filename;
        }
        $cur                       = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false;
        $this->attachment[$cur][6] = 'inline';
        $this->attachment[$cur][7] = $cid;
        return true;
    }
    function InlineImageExists()
    {
        $result = false;
        for ($i = 0; $i < count($this->attachment); $i++) {
            if ($this->attachment[$i][6] == 'inline') {
                $result = true;
                break;
            }
        }
        return $result;
    }
    function ClearAddresses()
    {
        $this->to = array();
    }
    function ClearCCs()
    {
        $this->cc = array();
    }
    function ClearBCCs()
    {
        $this->bcc = array();
    }
    function ClearReplyTos()
    {
        $this->ReplyTo = array();
    }
    function ClearAllRecipients()
    {
        $this->to  = array();
        $this->cc  = array();
        $this->bcc = array();
    }
    function ClearAttachments()
    {
        $this->attachment = array();
    }
    function ClearCustomHeaders()
    {
        $this->CustomHeader = array();
    }
    function SetError($msg)
    {
        $this->error_count++;
        $this->ErrorInfo = $msg;
    }
    function RFCDate()
    {
        $tz     = date('Z');
        $tzs    = ($tz < 0) ? '-' : '+';
        $tz     = abs($tz);
        $tz     = (int) ($tz / 3600) * 100 + ($tz % 3600) / 60;
        $result = sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);
        return $result;
    }
    function ServerHostname()
    {
        if (!empty($this->Hostname)) {
            $result = $this->Hostname;
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $result = $_SERVER['SERVER_NAME'];
        } else {
            $result = "localhost.localdomain";
        }
        return $result;
    }
    function Lang($key)
    {
        if (count($this->language) < 1) {
            $this->SetLanguage('en');
        }
        if (isset($this->language[$key])) {
            return $this->language[$key];
        } else {
            return 'Language string failed to load: ' . $key;
        }
    }
    function IsError()
    {
        return ($this->error_count > 0);
    }
    function FixEOL($str)
    {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_replace("\n", $this->LE, $str);
        return $str;
    }
    function AddCustomHeader($custom_header)
    {
        $this->CustomHeader[] = explode(':', $custom_header, 2);
    }
    function MsgHTML($message, $basedir = '')
    {
        preg_match_all("/(src|background)=\"(.*)\"/Ui", $message, $images);
        if (isset($images[2])) {
            foreach ($images[2] as $i => $url) {
                if (!preg_match('/^[A-z][A-z]*:\/\//', $url)) {
                    $filename  = basename($url);
                    $directory = dirname($url);
                    ($directory == '.') ? $directory = '' : '';
                    $cid       = 'cid:' . md5($filename);
                    $fileParts = split("\.", $filename);
                    $ext       = $fileParts[1];
                    $mimeType  = $this->_mime_types($ext);
                    if (strlen($basedir) > 1 && substr($basedir, -1) != '/') {
                        $basedir .= '/';
                    }
                    if (strlen($directory) > 1 && substr($basedir, -1) != '/') {
                        $directory .= '/';
                    }
                    $this->AddEmbeddedImage($basedir . $directory . $filename, md5($filename), $filename, 'base64', $mimeType);
                    if ($this->AddEmbeddedImage($basedir . $directory . $filename, md5($filename), $filename, 'base64', $mimeType)) {
                        $message = preg_replace("/" . $images[1][$i] . "=\"" . preg_quote($url, '/') . "\"/Ui", $images[1][$i] . "=\"" . $cid . "\"", $message);
                    }
                }
            }
        }
        $this->IsHTML(true);
        $this->Body = $message;
        $textMsg    = trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s', '', $message)));
        if (!empty($textMsg) && empty($this->AltBody)) {
            $this->AltBody = $textMsg;
        }
        if (empty($this->AltBody)) {
            $this->AltBody = 'To view this email message, open the email in with HTML compatibility!' . "\n\n";
        }
    }
    function _mime_types($ext = '')
    {
        $mimes = array(
            'hqx' => 'application/mac-binhex40',
            'cpt' => 'application/mac-compactpro',
            'doc' => 'application/msword',
            'bin' => 'application/macbinary',
            'dms' => 'application/octet-stream',
            'lha' => 'application/octet-stream',
            'lzh' => 'application/octet-stream',
            'exe' => 'application/octet-stream',
            'class' => 'application/octet-stream',
            'psd' => 'application/octet-stream',
            'so' => 'application/octet-stream',
            'sea' => 'application/octet-stream',
            'dll' => 'application/octet-stream',
            'oda' => 'application/oda',
            'pdf' => 'application/pdf',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'mif' => 'application/vnd.mif',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'gtar' => 'application/x-gtar',
            'php' => 'application/x-httpd-php',
            'php4' => 'application/x-httpd-php',
            'php3' => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source',
            'js' => 'application/x-javascript',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'tar' => 'application/x-tar',
            'tgz' => 'application/x-tar',
            'xhtml' => 'application/xhtml+xml',
            'xht' => 'application/xhtml+xml',
            'zip' => 'application/zip',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'rv' => 'video/vnd.rn-realvideo',
            'wav' => 'audio/x-wav',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'css' => 'text/css',
            'html' => 'text/html',
            'htm' => 'text/html',
            'shtml' => 'text/html',
            'txt' => 'text/plain',
            'text' => 'text/plain',
            'log' => 'text/plain',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'doc' => 'application/msword',
            'word' => 'application/msword',
            'xl' => 'application/excel',
            'eml' => 'message/rfc822'
        );
        return (!isset($mimes[strtolower($ext)])) ? 'application/octet-stream' : $mimes[strtolower($ext)];
    }
    function set($name, $value = '')
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            $this->SetError('Cannot set or reset variable ' . $name);
            return false;
        }
    }
    function getFile($filename)
    {
        $return = '';
        if ($fp = fopen($filename, 'rb')) {
            while (!feof($fp)) {
                $return .= fread($fp, 1024);
            }
            fclose($fp);
            return $return;
        } else {
            return false;
        }
    }
    function SecureHeader($str)
    {
        $str = trim($str);
        $str = str_replace("\r", "", $str);
        $str = str_replace("\n", "", $str);
        return $str;
    }
    function Sign($key_filename, $key_pass)
    {
        $this->sign_key_file = $key_filename;
        $this->sign_key_pass = $key_pass;
    }
}

?>
