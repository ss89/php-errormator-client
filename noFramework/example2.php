<?php
require '../vendor/autoload.php';

/**
 * From http://php.net/manual/en/function.set-error-handler.php
 */

require '../ErrormatorClient/ErrormatorClient.php';
function myErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
    $client = new ErrormatorClient\ErrormatorClient(array("apiKey" => "12345678901234567890123456789012"));
    //in error case
    $data = array(
        array(
            "traceback" => print_r(debug_backtrace(),true),
            "priority" => 1,
            "error_type" => "OMG an PHP Error happened",
            "occurences" => 1,
            "http_status" => 500,
            "server" => "127.0.0.1",
            "report_details" => array(
                array(
                    "start_time" => date("Y-m-d\TH:i:s.u"),
                    "username" => "myUserName",
                    "url" => "http://127.0.0.1/errormator-test-url",
                    "ip" => "127.0.0.1",
                    "user_agent" => "Firefox blah",
                    "message" => $errstr . " (" . $errno . ")",
                    "request" => array(
                        "REQUEST_METHOD" => "GET",
                        "PATH_INFO" => $errfile . ":" . $errline,
                        "POST" => array(
                            "field1" => "value1"
                        )
                    ),
                )
            )
        )
    );
    switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:
            echo "<b>ERROR</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";
            $client->error($data);
            exit(1);
            break;
        case E_WARNING:
        case E_USER_WARNING:
            echo "<b>WARNING</b> [$errno] $errstr<br />\n";
            $client->error($data);
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            echo "<b>NOTICE</b> [$errno] $errstr<br />\n";
            $data = array(
                array(
                    "log_level" => "NOTICE",
                    "message" => "[$errno] $errstr in " . $errfile . ":" . $errline,
                    "name" => __NAMESPACE__,
                    "server" => $_SERVER['HTTP_HOST'],
                ),
            );
            $client->log($data);
            break;

        default:
            echo "Unknown error type: [$errno] $errstr<br />\n";
            $client->error($data);
            break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}
$old_error_handler = set_error_handler("myErrorHandler");
/*
 * 
 */

//WARNING:
echo 2/0;
//NOTICE:
var_dump($not_existent_variable);
//ERROR:
//yet to be found, i usually don't do these (and don't remember how to if i ever did) ;)

?>
