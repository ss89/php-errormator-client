<!doctype html>
<html>
    <head>
        <title>Example for PHP Errormator Client usage</title>
        <style>
            .error
            {
                color: red;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>PHP Errormator Client</h1>
        <form method="POST" action="">
            <p>You can test the PHP Errormator Client by pressing the button below.</p>
            <p>please keep in mind to create an errormator.com account first, then to add an application and to obtain its API Key.</p>
            <p>After you did that, change the value of the $api_key in this script.</p>
            <?php
            $api_key = "12345678901234567890123456789012";
            if ($api_key == "12345678901234567890123456789012")
            {
                ?>
                <p class="error">YOU HAVE NOT CHANGED YOUR API KEY YET</p>
                <?php
            }
            ?>
            <input name="do_api_request" type="submit" value="Post some test Messages">
        </form>
    </body>
</html>
<?php
require 'ErrormatorClient.php';
if (isset($_POST) && isset($_POST['do_api_request']))
{
    $opts = array("scheme" => "https", "api_key" => $api_key, "debug" => false);
    $client = new ErrormatorClient($opts);
    if($client->checkApiKey()===false)
    {
        die('<p class="error">API KEY NOT VALID</p>');
    }
    $data = array(
        array(
            "url" => "http://127.0.0.1/errormator-test",
            "server" => "127.0.0.1",
            "report_details" => array(
                array(
                    "start_time" => date("Y-m-d\TH:i:s.u"),
                    "end_time" => date("Y-m-d\TH:i:s.u"),
                    "username" => "myUserName",
                    "url" => "http://127.0.0.1/errormator-test-url",
                    "ip" => "127.0.0.1",
                    "user_agent" => "Firefox 1",
                    "message" => "my custom message",
                    "request" => array("field1" => "value1", "field2" => "value2"),
                    "slow_calls" => array(
                        array(
                            "duration" => "11.1234",
                            "timestamp" => date("Y-m-d\TH:i:s.u"),
                            "type" => "sql",
                            "subtype" => "mysql",
                            "parameters" => array("param1", "param2", "param3"),
                            "statement" => "select * from mytable"
                        )
                    )
                )
            )
        )
    );
    $ret = $client->slow($data);
    echo $ret;
    echo "<br>";
    unset($data);
    $data = array(
        array(
            "traceback" => "my traceback as string",
            "priority" => 1,
            "error_type" => "OMG ValueError happened",
            "occurences" => 2,
            "http_status" => 500,
            "errormator.client" => "php",
            "errormator.client" => "php",
            "server" => "127.0.0.1",
            "report_details" => array(
                array(
                    "start_time" => date("Y-m-d\TH:i:s.u"),
                    "username" => "myUserName",
                    "url" => "http://127.0.0.1/errormator-test-url",
                    "ip" => "127.0.0.1",
                    "user_agent" => "Firefox blah",
                    "message" => "my custom message",
                    "request" => array(
                        "REQUEST_METHOD" => "GET", 
                        "PATH_INFO" => "/FOO/BAR", 
                        "POST" => array(
                            "field1" => "value1"
                        )
                    ),
                )
            )
        )
    );
    $ret = $client->error($data);
    echo $ret;
    echo "<br>";
    unset($data);
    $data =
            array(
                array(
                    "log_level" => "INFO",
                    "message" => "OMG ValueINFO happened",
                    "name" => "php.namespace.indicator",
                    "server" => "127.0.0.1",
                ),
                array(
                    "log_level" => "WARN",
                    "message" => "OMG ValueWARN happened",
                    "name" => "php.namespace.indicator",
                    "server" => "127.0.0.1",
                ),
                array(
                    "log_level" => "ERROR",
                    "message" => "OMG ValueERROR happened",
                    "name" => "php.namespace.indicator",
                    "server" => "127.0.0.1",
                )
            )
    ;
    $ret = $client->log($data);
    echo $ret;
}
?>
