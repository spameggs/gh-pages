<?php
error_reporting(E_ERROR);
session_start();
$install_complete = false;
$c_f              = @fopen('../includes/config.inc.php', 'r');
if ($c_f) {
    while (!feof($c_f)) {
        $c_line = fgets($c_f, 1024);
        if (strpos($c_line, 'RL_DS') !== false) {
            $install_complete = true;
        }
    }
    fclose($c_f);
}
if ($install_complete && $_GET['step'] != 'finish') {
    header("Location: index.php?step=finish");
    exit;
}
function str2key($key)
{
    $key = preg_replace('/[^a-z0-9]+/i', '_', $key);
    $key = preg_replace('/\-+/', '_', $key);
    $key = strtolower($key);
    $key = trim($key, '_');
    return empty($key) ? false : $key;
}
function isEmail($mail)
{
    return (bool) preg_match('/^(?:(?:\"[^\"\f\n\r\t\v\b]+\")|(?:[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(?:\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@(?:(?:\[(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))\])|(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))|(?:(?:(?:[A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/', $mail);
}
$version              = '4.1.0';
$main_menu            = array(
    'introduction' => 'Introduction',
    'license_agreement' => 'Nulled Agreement',
    'requirements' => 'Requirements',
    'database' => 'Database settings',
    'advanced' => 'Advanced settings',
    'tables' => 'Tables creation',
    'config_file' => 'Config file creation',
    'finish' => 'Finish'
);
$php_version          = (version_compare(phpversion(), '4.1') == -1) ? false : true;
$php_register_globals = ini_get('register_globals') ? 'average' : 'good';
$php_magic_quotes     = get_magic_quotes_gpc() ? 'average' : 'good';
$mysql_version        = (version_compare(mysql_get_client_info(), '4.1') == 0) ? false : true;
if (function_exists('apache_get_modules')) {
    $php_mod_rewrite = array_search('mod_rewrite', apache_get_modules()) !== false ? 'good' : 'average';
} else {
    $php_mod_rewrite = 'average';
}
$curl = extension_loaded('curl') ? 'good' : 'average';
if (function_exists('gd_info')) {
    $gd = gd_info();
    if ($gd) {
        preg_match('/[0-9]\.+[0-9]\.[0-9]+/', $gd['GD Version'], $matches);
        $gd_version         = $matches[0];
        $gd_library_version = $gd_version;
        $gd_library         = (version_compare($gd_version, '2.0') == -1) ? 'bad' : 'good';
    } else {
        $gd_library         = 'average';
        $gd_library_version = 'No installed';
    }
} else {
    $gd_library         = 'average';
    $gd_library_version = 'Undefined';
}
$requires_access = true;
$databa_access   = true;
$requires_button = '';
if (!$php_version || !$mysql_version) {
    $requires_access = false;
    $requires_button = 'disabled';
}
$requires = array(
    'php_version' => array(
        'name' => 'PHP version',
        'description' => '4.x, 5.x or above',
        'value' => phpversion(),
        'result' => $php_version ? 'good' : 'bad'
    ),
    'register_globals' => array(
        'name' => 'PHP register_globals',
        'description' => 'We strongly recomend to disabled this option',
        'value' => ini_get('register_globals') ? 'Enabled' : 'Disabled',
        'result' => $php_register_globals
    ),
    'magic_quots' => array(
        'name' => 'PHP magic_quotes',
        'description' => 'We recomend to disabled this option',
        'value' => get_magic_quotes_gpc() ? 'Enabled' : 'Disabled',
        'result' => $php_magic_quotes
    ),
    'mod_rewrite' => array(
        'name' => 'Apach mod_rewrite module',
        'description' => 'We recomend to install this module to have <b>SEO</b> friendly front-end interface',
        'result' => $php_mod_rewrite
    ),
    'curl' => array(
        'name' => 'cURL module',
        'description' => 'We recomend to install cURL module for security reasons',
        'value' => extension_loaded('curl') ? 'Installed' : 'Not installed',
        'result' => $curl
    ),
    'gd_library' => array(
        'name' => 'GD lybrary',
        'description' => 'We strongly recomend to install this module, the system needs GD to handle uploaded images',
        'value' => $gd_library_version,
        'result' => $gd_library
    ),
    'mysql' => array(
        'name' => 'MySQL server',
        'description' => '4.1.x, 5.1.x or above',
        'value' => mysql_get_client_info(),
        'result' => $mysql_version ? 'good' : 'bad'
    )
);
if (function_exists('apache_get_modules')) {
    $requires['mod_rewrite']['value'] = array_search('mod_rewrite', apache_get_modules()) !== false ? 'Installed' : 'Not installed';
} else {
    $requires['mod_rewrite']['value'] = "Undefined";
}
$permissions = array(
    array(
        'path' => '/tmp/aCompile/',
        'description' => 'Admin Panle compile files directory'
    ),
    array(
        'path' => '/tmp/mCompile/',
        'description' => 'Mobile Version compile files directory'
    ),
    array(
        'path' => '/tmp/compile/',
        'description' => 'Front-End compile files directory'
    ),
    array(
        'path' => '/tmp/cache/',
        'description' => 'Cache files directory'
    ),
    array(
        'path' => '/tmp/errorLog/',
        'description' => 'Error logs files directory'
    ),
    array(
        'path' => '/tmp/upload/',
        'description' => 'Temporary uploaded files directory'
    ),
    array(
        'path' => '/files/',
        'description' => 'Listings, accounts, banners etc. files directory'
    ),
    array(
        'path' => '/plugins/',
        'description' => 'Plugins directory'
    ),
    array(
        'path' => '/backup/plugins/',
        'description' => 'Plugins backups directory'
    ),
    array(
        'path' => '/includes/classes/',
        'description' => 'System classes directory, <br /><span class="red"><b>Notice</b>:</span> the permissions must be restored manually to <b>0755</b> (not writable) after installation'
    ),
    array(
        'path' => '/includes/config.inc.php',
        'description' => 'Stable config file'
    ),
    array(
        'path' => '/admin/',
        'description' => 'Admin Panel directory (Optional), <br /><span class="red"><b>Notice</b>:</span> the permissions must be restored manually to <b>0755</b> (not writable) after installation',
        'no_necessary' => true
    )
);
$database    = array(
    array(
        'name' => 'Hostname',
        'var' => 'hostname',
        'type' => 'text',
        'description' => 'MySQL hostname or IP address',
        'value' => 'localhost'
    ),
    array(
        'name' => 'Port',
        'var' => 'port',
        'type' => 'text',
        'description' => 'MySQL server port',
        'value' => '3306'
    ),
    array(
        'name' => 'Username',
        'var' => 'username',
        'type' => 'text',
        'description' => 'MySQL username',
        'value' => ''
    ),
    array(
        'name' => 'Password',
        'var' => 'password',
        'type' => 'password',
        'description' => 'MySQL password',
        'value' => ''
    ),
    array(
        'name' => 'Database name',
        'var' => 'name',
        'type' => 'text',
        'description' => 'MySQL database name',
        'value' => ''
    ),
    array(
        'name' => 'Tables prefix',
        'var' => 'prefix',
        'type' => 'text',
        'description' => 'Leave default or use your',
        'value' => 'ra_'
    )
);
$advanced    = array(
    array(
        'name' => 'Site name',
        'var' => 'site_name',
        'type' => 'text',
        'description' => 'Site name, will apper as part of pages title',
        'value' => 'Escort Agency Service'
    ),
    array(
        'name' => 'Site owner name',
        'var' => 'site_owner',
        'type' => 'text',
        'description' => 'Your or your company name, will display in letters subjects',
        'value' => $_POST['site_owner']
    ),
    array(
        'name' => 'Site main e-mail',
        'var' => 'site_email',
        'type' => 'text',
        'description' => 'Your or your company contact email, will use for notification from the site',
        'value' => $_POST['site_email']
    ),
    array(
        'name' => 'Enable www. in urls',
        'var' => 'www_prefix',
        'type' => 'radio',
        'description' => '<b>Note</b>: subsequent changes to the option may affect search engine rankings of your site',
        'value' => $_POST['www_prefix']
    )
);
$admin       = array(
    array(
        'name' => 'Admin username',
        'var' => 'admin_username',
        'type' => 'text',
        'description' => 'Main Administrator username',
        'value' => 'admin'
    ),
    array(
        'name' => 'Admin password',
        'var' => 'admin_password',
        'type' => 'password',
        'description' => 'Administrator password'
    ),
    array(
        'name' => 'Confirm password',
        'var' => 'password_repeat',
        'type' => 'password',
        'description' => 'Confirm administrator password'
    ),
    array(
        'name' => 'Admin e-mail',
        'var' => 'admin_email',
        'type' => 'text',
        'description' => 'Administrator e-mail'
    ),
    array(
        'name' => 'Admin directory name',
        'var' => 'admin_dir',
        'type' => 'text',
        'description' => 'Admin Panel directory name, www.domain.com/<b>admin</b>/ for example',
        'value' => 'admin'
    )
);
$php         = explode('.', phpversion());
if ($php[0] == 4) {
    $php = 5;
} elseif ($php[0] == 5 && $php[1] >= 3) {
    $php = 5.3;
} elseif ($php[0] == 5 && $php[1] < 3) {
    $php = 5;
}
switch ($_GET['step']) {
    case 'license_agreement':
        $content = <<< VS
	<p>Please read the following.</p>
	<iframe style="width: 99%;margin: 10px 0;height: 400px; border: 1px silver solid;" src="license.html">
	</iframe>
	<p>If you accept that you TRY IT. - LIKE IT? - BUY IT!, please click on the <b>Accept</b> button to accept this and continue the installation.</p>
	<div style="text-align: right;padding: 10px 0 0 0;">
		<a href="index.php" class="cancel">Cancel</a>
		<input onclick="location.href='index.php?step=requirements'" type="button" value="Accept &raquo;" />
	</div>
VS;
        break;
    case 'requirements':
        $_SESSION['requires_access_info'] = $requires_access;
        $content                          = <<< VS
		<p>Please make sure your server meets system requirements shown below. If not then you should contact your hosting helpdesk to have the issue resolved.</p>
		
		<table class="list" style="margin: 10px 0;">
		<tr>
			<td colspan="2" class="table_caption"><div>Requirement</div></td>
		</tr>
VS;
        foreach ($requires as $key => $value) {
            $content .= '
			<tr>
				<td class="td_spliter"><div><b>' . $requires[$key]['name'] . '</b><br /><span>' . $requires[$key]['description'] . '</span></div></td>
				<td class="td_value ' . $requires[$key]['result'] . '"><div>' . $requires[$key]['value'] . '</div></td>
			</tr>';
        }
        $content .= "</table>";
        $content .= <<< VS
		<p>Then you should check if the following directories/files are writable.</p>
		
		<table class="list" style="margin: 10px 0;">
		<tr>
			<td colspan="2" class="table_caption"><div>Permissions</div></td>
		</tr>
VS;
        foreach ($permissions as $permission) {
            $permission['value']    = is_writable('..' . $permission['path']) ? 'Writable' : 'Unwritable';
            $permission['status']   = is_writable('..' . $permission['path']) ? 'good' : 'bad';
            $_SESSION['edit_admin'] = false;
            if (!is_writable('..' . $permission['path'])) {
                chmod('..' . $permission['path'], 0755);
                if (!is_writable('..' . $permission['path'])) {
                    chmod('..' . $permission['path'], 0777);
                }
            }
            if (!is_writable('..' . $permission['path'])) {
                if (!$permission['no_necessary']) {
                    $requires_access = false;
                    $requires_button = 'disabled';
                } else {
                    $permission['status'] = 'average';
                }
            } else {
                if ($permission['path'] == '/admin/') {
                    $_SESSION['edit_admin'] = true;
                }
            }
            $content .= '
			<tr>
				<td class="td_spliter"><div><b>' . $permission['path'] . '</b><br /><span>' . $permission['description'] . '</span></div></td>
				<td class="td_value ' . $permission['status'] . '"><div>' . $permission['value'] . '</div></td>
			</tr>';
        }
        $content .= '
		<tr><td colspan="2" class="table_footer"></td></tr>
		</table>';
        $content .= <<< VS
		<div style="text-align: right;padding: 10px 0 0 0;">
			<a href="index.php?step=license_agreement" class="cancel">&laquo; Back</a>
			<input style="margin-right: 5px;" onclick="location.href='index.php?step=requirements'" class="button" type="button" value="Refresh" />
			<input {$requires_button} onclick="location.href='index.php?step=database'" class="button {$requires_button}" type="button" value="Next &raquo;" />		
		</div>
VS;
        break;
    case 'database':
        $error = false;
        if ($_SESSION['requires_access_info']) {
            if ($_POST['action']) {
                foreach ($database as $key => $value) {
                    if (!empty($_POST[$database[$key]['var']])) {
                        $database[$key]['value'] = $_POST[$database[$key]['var']];
                    } else {
                        if ($database[$key]['var'] != 'password') {
                            $database[$key]['error'] = 'error';
                            $error                   = true;
                        }
                    }
                }
                if (!$error) {
                    $db_host     = $_POST['hostname'];
                    $db_port     = $_POST['port'];
                    $db_user     = $_POST['username'];
                    $db_password = $_POST['password'];
                    $db_name     = $_POST['name'];
                    $connect     = mysql_connect($db_host . ':' . $db_port, $db_user, $db_password);
                    if (!$connect) {
                        $errors[]      = mysql_error();
                        $databa_access = false;
                    } else {
                        $db = mysql_select_db($db_name, $connect);
                        if (!$db) {
                            $errors[]      = mysql_error();
                            $databa_access = false;
                        } else {
                            $_SESSION['database_info'] = array(
                                'hostname' => $db_host,
                                'port' => $db_port,
                                'username' => $db_user,
                                'password' => $db_password,
                                'name' => $db_name,
                                'prefix' => $_POST['prefix']
                            );
                            header("Location: index.php?step=advanced");
                            exit;
                        }
                    }
                }
            } else {
                foreach ($database as $key => $value) {
                    if (!empty($_SESSION['database_info'][$database[$key]['var']])) {
                        $database[$key]['value'] = $_SESSION['database_info'][$database[$key]['var']];
                    }
                }
            }
        } else {
            header("Location: index.php?step=requirements");
        }
        $content = <<< VS
	<p>Please fill in all fields bellow.</p>
	<form action="index.php?step=database" method="post">
	<input type="hidden" name="action" value="true" />
	<table class="list" style="margin: 10px 0;">
VS;
        foreach ($database as $key => $value) {
            $content .= '
			<tr>
				<td class="td_spliter"><div><b>' . $database[$key]['name'] . '</b><br /><span>' . $database[$key]['description'] . '</span></div></td>
				<td style="width: 180px" class="td_value"><input name="' . $database[$key]['var'] . '" class="' . $database[$key]['error'] . '" type="' . $database[$key]['type'] . '" value="' . $database[$key]['value'] . '" /></td>
			</tr>';
        }
        $content .= <<< VS
	</table>
	<div style="text-align: right;padding: 10px 0 0 0;">
		<a href="index.php?step=requirements" class="cancel">&laquo; Back</a>
		<input {$requires_button} onclick="location.href='index.php?step=database'" class="button" type="submit" value="Next &raquo;" />		
	</div>
	</form>
VS;
        break;
    case 'advanced':
        $error = false;
        if ($_SESSION['database_info']) {
            if ($_POST['action']) {
                foreach ($admin as $key => $value) {
                    if (empty($_POST[$admin[$key]['var']])) {
                        $error                = true;
                        $admin[$key]['error'] = 'error';
                        $errors[]             = '<b>' . $admin[$key]['name'] . "</b> field should be filled in";
                    } else {
                        if ($admin[$key]['var'] == 'admin_dir') {
                            $admin[$key]['value'] = trim(str2key($_POST[$admin[$key]['var']]));
                        } else {
                            $admin[$key]['value'] = $_POST[$admin[$key]['var']];
                        }
                    }
                }
                if (!$error) {
                    if ($_POST['admin_password'] != $_POST['password_repeat']) {
                        $error    = true;
                        $errors[] = 'Confirm password does not match Administrator password';
                    }
                    if (!isEmail($_POST['admin_email'])) {
                        $error    = true;
                        $errors[] = 'The email you\'ve entered is invalid';
                    }
                    if ((bool) preg_match('/[\W]/', $_POST['admin_dir'])) {
                        $error    = true;
                        $errors[] = 'Admin Panel directory name should contain alphabetic and numeric characters only';
                    }
                    if (!$error) {
                        foreach ($advanced as $key => $value) {
                            $_SESSION['advanced_info'][$advanced[$key]['var']] = $_POST[$advanced[$key]['var']];
                        }
                        foreach ($admin as $key => $value) {
                            $_SESSION['admin_info'][$admin[$key]['var']] = $_POST[$admin[$key]['var']];
                        }
                        header("Location: index.php?step=tables");
                        exit;
                    }
                }
            } else {
                foreach ($admin as $key => $value) {
                    if (!empty($_SESSION['database_info'][$database[$key]['var']])) {
                        $database[$key]['value'] = $_SESSION['database_info'][$database[$key]['var']];
                    }
                }
            }
        } else {
            header("Location: index.php?step=database");
        }
        $content = <<< VS
	Please fill in all fields bellow.
	<form action="index.php?step=advanced" method="post">
	<input type="hidden" name="action" value="true" />
	<table cellpadding="0" cellspacing="0" style="margin: 10px 0;">
	<tr>
		<td colspan="2" class="table_caption"><div>Main Settings</div></td>
	</tr>
VS;
        foreach ($advanced as $key => $value) {
            $content .= '
			<tr>
				<td class="td_spliter"><div><b>' . $advanced[$key]['name'] . '</b><br /><span>' . $advanced[$key]['description'] . '</span></div></td>
				<td class="td_value">';
            if ($value['var'] == 'www_prefix') {
                $www_checked     = $_POST['www_prefix'] != '0' ? 'checked="checked"' : '';
                $non_www_checked = $_POST['www_prefix'] == '0' ? 'checked="checked"' : '';
                $content .= '<label><input ' . $www_checked . ' name="www_prefix" type="radio" value="1" /> Yes</label>';
                $content .= '<label style="padding: 0 0 0 10px;"><input ' . $non_www_checked . ' name="www_prefix" type="radio" value="0" /> No</label>';
            } else {
                $content .= '<input style="width: 200px;" name="' . $advanced[$key]['var'] . '" class="' . $advanced[$key]['error'] . '" type="' . $advanced[$key]['type'] . '" value="' . $advanced[$key]['value'] . '" />';
            }
            $content .= '
				</td>
			</tr>';
        }
        $content .= <<< VS
	</table>
	<table cellpadding="0" cellspacing="0" style="margin: 10px 0;">
	<tr>
		<td colspan="2" class="table_caption"><div>Admin Settings</div></td>
	</tr>
VS;
        foreach ($admin as $key => $value) {
            $admin_disabled = ($admin[$key]['var'] == 'admin_dir' && !$_SESSION['edit_admin']) ? 'readonly' : '';
            $content .= '
			<tr>
				<td class="td_spliter"><div><b>' . $admin[$key]['name'] . '</b><br /><span>' . $admin[$key]['description'] . '</span></div></td>
				<td class="td_value"><input ' . $admin_disabled . ' style="width: 200px;" name="' . $admin[$key]['var'] . '" class="' . $admin[$key]['error'] . '" type="' . $admin[$key]['type'] . '" value="' . $admin[$key]['value'] . '" /></td>
			</tr>';
        }
        $content .= <<< VS
	</table>
	<div style="text-align: right;">
		<a href="index.php?step=database" class="cancel">&laquo; Back</a>
		<input {$requires_button} onclick="location.href='index.php?step=database'" class="button" type="submit" value="Next &raquo;" />		
	</div>
	</form>
VS;
        break;
    case 'tables':
        if ($_SESSION['requires_access_info'] && $_SESSION['database_info'] && $_SESSION['advanced_info'] && $_SESSION['admin_info']) {
            $db_host     = $_SESSION['database_info']['hostname'];
            $db_port     = $_SESSION['database_info']['port'];
            $db_user     = $_SESSION['database_info']['username'];
            $db_password = $_SESSION['database_info']['password'];
            $db_name     = $_SESSION['database_info']['name'];
            $connect     = mysql_connect($db_host . ':' . $db_port, $db_user, $db_password);
            if (!$connect) {
                $errors[]      = mysql_error();
                $databa_access = false;
            } else {
                $db = mysql_select_db($db_name, $connect);
                if (!$db) {
                    $errors[]      = mysql_error();
                    $databa_access = false;
                } else {
                    $queryql_dump = fopen("mysql/dump.sql", "r");
                    mysql_query("SET NAMES `utf8`");
                    if ($queryql_dump) {
                        while ($query = fgets($queryql_dump, 10240)) {
                            $query = trim($query);
                            if ($query[0] == '#')
                                continue;
                            if ($query[0] == '-')
                                continue;
                            if ($query[strlen($query) - 1] == ';') {
                                $query_sql .= $query;
                            } else {
                                $query_sql .= $query;
                                continue;
                            }
                            if (!empty($query_sql)) {
                                $find      = array(
                                    '{admin_user}',
                                    '{admin_password}',
                                    '{admin_email}',
                                    '{db_prefix}',
                                    '{advanced_site_name}',
                                    '{advanced_site_owner}',
                                    '{advanced_site_email}'
                                );
                                $replace   = array(
                                    $_SESSION['admin_info']['admin_username'],
                                    md5($_SESSION['admin_info']['admin_password']),
                                    $_SESSION['admin_info']['admin_email'],
                                    $_SESSION['database_info']['prefix'],
                                    $_SESSION['advanced_info']['site_name'],
                                    $_SESSION['advanced_info']['site_owner'],
                                    $_SESSION['advanced_info']['site_email']
                                );
                                $query_sql = str_replace($find, $replace, $query_sql);
                            }
                            $res = mysql_query($query_sql, $connect);
                            if (!$res) {
                                $mess = "Can not run sql query.<br />";
                                $mess .= "Error: " . mysql_error() . '<br />';
                                $mess .= "Query: " . $query_sql;
                                $errors[] = $mess;
                            }
                            unset($query_sql);
                        }
                        fclose($sql_dump);
                        if (empty($errors)) {
                            header("Location: index.php?step=config_file");
                            exit;
                        }
                    } else {
                        $errors[] = "Can not find SQL dump!";
                    }
                }
            }
        } else {
            header("Location: index.php?system_error");
        }
        $content .= <<< VS
		<p>MySQL error occurred!!</p>
VS;
        break;
    case 'config_file':
        if ($_SESSION['requires_access_info'] && $_SESSION['database_info'] && $_SESSION['advanced_info'] && $_SESSION['admin_info']) {
            $handle = fopen("config.inc.php.tmp", "r");
            if ($handle) {
                while (!feof($handle)) {
                    $config_content .= fgets($handle, 4096);
                }
                fclose($handle);
                if (!empty($config_content)) {
                    $root = str_replace('install', '', getcwd());
                    $host = trim($_SERVER['HTTP_HOST'], '/');
                    $host = (bool) preg_match('/^www\./', $host) && $_SESSION['advanced_info']['www_prefix'] ? $host : 'www.' . $host;
                    $host = !$_SESSION['advanced_info']['www_prefix'] ? preg_replace('/^(www\.)/', '', $host) : $host;
                    $self = $_SERVER['PHP_SELF'];
                    preg_match('/(.*)install/', $self, $matches);
                    $dir  = trim($matches[1], '/');
                    $root = empty($dir) ? $root : str_replace($dir, '', $root);
                    $dir  = empty($dir) ? "''" : "'" . $dir . "' . RL_DS";
                    $url  = 'http://' . $host . '/';
                    $url .= (empty($dir) || $dir == "''") ? '' : substr(trim($dir, "'"), 0, -9) . '/';
                    $root = rtrim($root, DIRECTORY_SEPARATOR);
                    if (rename('../tmp/cache/', '../tmp/cache_tmp/')) {
                        $cache_postfix = '_' . mt_rand();
                        rename('../tmp/cache_tmp/', '../tmp/cache/');
                    }
                    $find           = array(
                        '{db_port}',
                        '{db_host}',
                        '{db_user}',
                        '{db_pass}',
                        '{db_name}',
                        '{db_prefix}',
                        '{rl_admin}',
                        '{rl_root}',
                        '{rl_dir}',
                        '{rl_url}',
                        '{rl_cache_postfix}'
                    );
                    $replace        = array(
                        $_SESSION['database_info']['port'],
                        $_SESSION['database_info']['hostname'],
                        $_SESSION['database_info']['username'],
                        $_SESSION['database_info']['password'],
                        $_SESSION['database_info']['name'],
                        $_SESSION['database_info']['prefix'],
                        $_SESSION['admin_info']['admin_dir'],
                        $root,
                        $dir,
                        $url,
                        $cache_postfix
                    );
                    $config_content = str_replace($find, $replace, $config_content);
                    $orig_file      = fopen('../includes/config.inc.php', 'w+');
                    if (fwrite($orig_file, $config_content) === false) {
                        $errors[] = "Can not to write to /includes/config.inc.php file, please check file permissions.";
                    } else {
                        chmod('../includes/config.inc.php', 0644);
                        if ($_SESSION['edit_admin']) {
                            $admin_dir = '../' . trim($_SESSION['admin_info']['admin_dir'], '/');
                            rename('../admin/', $admin_dir);
                            chmod($admin_dir, 0755);
                            if (!is_writable($admin_dir)) {
                                chmod($admin_dir, 0777);
                            }
                        }
                        fclose($orig_file);
                        $new_cache_dir = '../tmp/cache' . $cache_postfix;
                        rename('../tmp/cache/', $new_cache_dir);
                        chmod($new_cache_dir, 0755);
                        if (!is_writable($new_cache_dir)) {
                            chmod($new_cache_dir, 0777);
                        }
                        require_once('../includes/config.inc.php');
                        require_once(RL_CLASSES . 'rlDb.class.php');
                        require_once(RL_CLASSES . 'reefless.class.php');
                        $rlDb     = new rlDb();
                        $reefless = new reefless();
                        $reefless->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);
                        $reefless->loadClass('Debug');
                        $reefless->loadClass('Valid');
                        $reefless->loadClass('Lang');
                        $reefless->loadClass('Actions');
                        $reefless->loadClass('Config');
                        $reefless->loadClass('Hook');
                        $config = $rlConfig->allConfig();
                        $rlLang->extDefineLanguage();
                        $lang = $rlLang->getLangBySide('admin', RL_LANG_CODE, 'all');
                        $reefless->loadClass('Cache');
                        $reefless->loadClass('ListingTypes');
                        function loadUTF8functions()
                        {
                            $names = func_get_args();
                            if (empty($names)) {
                                return false;
                            }
                            foreach ($names as $name) {
                                if (file_exists(RL_LIBS . 'utf8' . RL_DS . 'utils' . RL_DS . $name . '.php')) {
                                    require_once(RL_LIBS . 'utf8' . RL_DS . 'utils' . RL_DS . $name . '.php');
                                }
                            }
                        }
                        $rlCache->update();
                        $reefless->flTouch();
                        header("Location: index.php?step=finish");
                        exit;
                    }
                }
            }
        } else {
            header("Location: index.php?system_error");
        }
        $content .= <<< VS
		<div>Processing...</div>
VS;
        break;
    case 'finish':
        require_once("../includes/config.inc.php");
        $admin_interface    = RL_URL_HOME . ADMIN . '/index.php';
        $frontend_interface = RL_URL_HOME;
        $content .= <<< VS
		<p>Our congratulations, the nulled <b>Escort Agency Service {$version}</b> has been successfully installed!<br /><br />
		Now you can log in your Admin Panel just click on this link: <a href="{$admin_interface}"><b>Admin Panel</b></a><br />
		Also you may visit your Front-End interface, click on this link please: <a href="{$frontend_interface}"><b>Front End Interface</b></a><br /><br /></p>
		<p class="red"><b>Notice</b>: Do not forget to remove the /install/ directory from your server.</p>
		
VS;
        break;
    case 'introduction':
    default:
        $content = <<< VS
	<p>
	Welcome to <b>Escort Agency Service {$version}</b><br /><br />
	
	Escort Agency Service is flexible and easy-to-use software that can change your perception of web solutions. Escort Agency Service Software was developed based on modern web technologies; it features a powerful <b>content management system</b> (CMS). Never before has the administrator of classifieds software had so many things to control and manage from the admin panel, for instance manage all fields, categories, listings, forms etc and a number of small features. Escort Agency Service Software is the best solution for your e-business.<br /><br />
	
	The Installation Wizard will help you install nulled Escort Agency Service {$version} on your server.
	Please follow the Installation Guide and feel free to use it for zer0.<br />

	<br />To initiate the installation please click on the INSTALL button.</p>
	<div style="padding: 10px 0 0 0;">
		<input onclick="location.href='index.php?step=license_agreement'" type="button" value="Install" />
	</div>
VS;
        break;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-gb" xml:lang="en-gb">
<head>
<title>Welcome to nulled Escort Agency Service<?php
echo $version;
?> Installation Wizard</title>
<link href="style.css" type="text/css" rel="stylesheet" />
<link type="image/x-icon" rel="shortcut icon" href="img/favicon.ico" />
</head>
<body>
<div id="main_container">
	<div id="header">
		<div id="logo"></div>
		<div id="title">Installation Wizard <span class="ver">v<?php
echo $version;
?></span></div>
		<div id="vav_bar">
			<a target="_blank" href="http://www.internetfirstpage.com">This is the first...</a>
			<a target="_blank" href="http://www.google.de">Questions??</a>
		</div>
		<div class="clear"></div>
	</div>
	<div id="body">
		<div class="inner">
			<table>
			<tr>
				<td id="sidebar">
					<div class="inner">
						<ul>
						<?php
$_GET['step'] = empty($_GET['step']) ? 'introduction' : $_GET['step'];
$s            = 1;
foreach ($main_menu as $key => $value) {
    if ($_GET['step'] == $key) {
        $no_proceed = true;
        $margin     = $s == 1 ? 'style="margin-top: 0;"' : '';
        echo '<li ' . $margin . ' class="active"><a href="javascript:void(0)">' . $value . '</a></li>';
    } else {
        if (!$no_proceed) {
            echo '<li class="done"><a href="index.php?step=' . $key . '">' . $value . '</a></li>';
        } else {
            echo '<li><div>' . $value . '</div></li>';
        }
    }
    $s++;
}
?>
						</ul>
						<div class="clear"></div>
					</div>
				</td>
			
				<td id="content">
					<div class="inner">
						<?php
if (!empty($errors)) {
    echo '<div id="error"><div class="inner"><ul>';
    foreach ($errors as $er) {
        echo '<li>' . $er . '</li>';
    }
    echo '</ul></div></div>';
}
?>
						<div id="center">
							<?php
echo $content;
?>
						</div>
					</div>
				</td>
			</tr>
			</table>
		</div>
	</div>
	
	<div id="footer">
		<span>&copy; <?php
echo date('Y');
?>, <a href="http://www.endoftheinternet.com/">This is the end...</a></span>
	</div>
</div>
</body>
</html>