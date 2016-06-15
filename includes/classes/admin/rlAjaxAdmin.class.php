<?php
class rlAjaxAdmin extends reefless
{
    var $rlLang;
    var $rlValid;
    var $rlConfig;
    var $rlAdmin;
    function rlAjaxAdmin()
    {
        global $rlLang, $rlValid, $rlConfig, $rlAdmin;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
        $this->rlConfig =& $rlConfig;
        $this->rlAdmin =& $rlAdmin;
    }
    function ajaxLogIn($user = null, $pass = null, $language = null)
    {
        global $_response, $config, $lang, $rlActions, $reefless;
        if ($reefless->attemptsLeft <= 0 && $config['security_login_attempt_admin_module']) {
            $msg = str_replace('{period}', '<b>' . $config['security_login_attempt_admin_period'] . '</b>', $lang['login_attempt_error']);
            $_response->script("
				$('#logo').next().fadeOut('normal', function(){
					$(this).remove();
					var msg = '<div class=\"error hide\"><div class=\"inner\"><div class=\"icon\"></div>{$msg}</div></div>';
					$('#logo').after(msg).next().fadeIn();
				});
			");
            return $_response;
            exit;
        }
        $_response->setCharacterEncoding('UTF-8');
        $sec_key       = md5($config['security_key']);
        $user          = $this->rlValid->xSql($user);
        $user_info     = $this->fetch('*', array(
            'User' => $user,
            'Status' => 'active'
        ), null, null, 'admins', 'row');
        $real_password = $sec_key . $user_info['Pass'];
        if ($config['security_login_attempt_admin_module']) {
            $insert = array(
                'IP' => $_SERVER['REMOTE_ADDR'],
                'Date' => 'NOW()',
                'Status' => $pass == $real_password ? 'success' : 'fail',
                'Interface' => 'admin',
                'Username' => $user
            );
            $rlActions->insertOne($insert, 'login_attempts');
        }
        if ($pass == $real_password) {
            $this->rlAdmin->LogIn($user_info);
            $query_string = $_SESSION['query_string'] ? '?' . $_SESSION['query_string'] : '';
            $pos          = strpos($_SESSION['query_string'], 'session_expired');
            if ($pos !== false) {
                $query_string = '?' . substr($_SESSION['query_string'], 0, $pos);
            }
            $query_string = $query_string ? $query_string . '&language=' . $language : '?language=' . $language;
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php' . $query_string);
        } else {
            $message = $lang['rl_logging_error'];
            if ($config['security_login_attempt_admin_module']) {
                if ($reefless->attempts > 0) {
                    $message .= '<br />' . $reefless->attemptsMessage;
                }
            }
            $_response->script("fail_alert('#login_notify', '{$message}')");
            $_response->script("$('#login_button').val('{$lang['login']}')");
        }
        return $_response;
    }
    function ajaxLogOut($user = null, $pass = null, $lang = null)
    {
        global $_response;
        if ($this->checkSessionExpire() === false) {
            $redirect_url = RL_URL_HOME . ADMIN . "/index.php";
            $redirect_url .= empty($_SERVER['QUERY_STRING']) ? '?session_expired' : '?' . $_SERVER['QUERY_STRING'] . '&session_expired';
            $_response->redirect($redirect_url);
        }
        $this->rlAdmin->LogOut($user_info);
        $_response->redirect(RL_URL_HOME . ADMIN . '/');
        return $_response;
    }
}