<?php

function initTheDevStackErrorHandler($logfile) {
  // Save the log file name to constant for the error handler
  define("LOGFILE", $logfile);
  // Custom error handler that writes to a file
  set_error_handler("theDevStackErrorHandler");
}

/**
 * Error handler to write all messages to a dedicated log file.
 */
function theDevStackErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
    $date = date(DATE_W3C);
    $str = "$date ";

    switch ($errno) {
        case E_USER_ERROR:
            $str .= "ERROR [$errno]: $errstr, Fatal error on line $errline in file $errfile\n";
            break;

        case E_USER_WARNING:
            $str .= "WARNING [$errno]: $errstr\n";
            break;

        case E_USER_NOTICE:
            $str .= "NOTICE [$errno]: $errstr\n";
            break;

        default:
            $str .= "Unknown error type: [$errno] $errstr\n";
            break;
    }

    file_put_contents(LOGFILE, $str, FILE_APPEND);

    /* Don't execute PHP internal error handler */
    return true;
}
?>