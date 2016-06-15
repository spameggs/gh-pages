<?php
class rlDebug extends reefless
{
    function rlDebug()
    {
        if (RL_DEBUG === true) {
            error_reporting(E_ALL);
            ini_set("display_errors", "1");
            set_error_handler(array(
                "rlDebug",
                "errorHandler"
            ), E_ALL);
        } else {
            error_reporting(E_ERROR);
            ini_set("display_errors", "0");
            set_error_handler(array(
                "rlDebug",
                "errorHandler"
            ), E_ERROR);
        }
        if (RL_DB_DEBUG) {
            unset($_SESSION['sql_debug_time']);
        }
    }
    function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (E_NOTICE == $errno || E_USER_NOTICE != $errno || E_STRICT == $errno) {
            return true;
        }
        $die = false;
        switch ($errno) {
            case E_NOTICE:
                $msg = "Notice";
                break;
            case E_USER_NOTICE:
                $msg = "User notice";
                break;
            case E_WARNING:
                $msg = "Warning";
                break;
            case E_USER_ERROR:
                $msg = "Fatal ERROR";
                $die = true;
                break;
            case E_USER_ERROR:
                $msg = "Fatal ERROR";
                $die = true;
                break;
            case E_USER_WARNING:
                $msg = "User warning";
                break;
        }
        if (!function_exists('file_put_contents')) {
            function file_put_contents($filename, $data)
            {
                $f = @fopen($filename, 'w');
                if (!$f) {
                    return false;
                } else {
                    $bytes = fwrite($f, $data);
                    fclose($f);
                    return $bytes;
                }
            }
        }
        file_put_contents(RL_TMP . 'errorLog/errors.log', $msg . ": " . $errstr . " on line# " . $errline . " (file: " . $errfile . ")" . PHP_EOL, FILE_APPEND);
        echo "<span style='font-family: tahoma; font-size: 12px;'>";
        echo "<h2>System {$msg} occurred:</h2> <b>$errstr</b><br />";
        echo "line# <font color='green'><b>$errline</b></font><br />";
        echo "file: <font color='green'><b>$errfile</b></font><br />";
        echo "PHP version: " . PHP_VERSION . " <br /></span>";
        if ($die) {
            die("Error occured. Please see the logs or report an error to the administrator");
        }
        return true;
    }
    function logger($errstr, $errfile = __FILE__, $errline = __LINE__, $errorType = "Warning")
    {
        file_put_contents(RL_TMP . 'errorLog/errors.log', $errorType . ": " . $errstr . " on line# " . $errline . " (file: " . $errfile . ")" . PHP_EOL, FILE_APPEND);
    }
}