php-errormator-client
=====================

PHP Client for errormator.com - helps you track errors in your web and cli apps

Please use commit 4854b1be1f for now - current commits don't work.
=====================

you can easily add the php-errormator-client to your php app by doing the following:

~~~
require 'ErrormatorClient.php';
$opts = array("apiKey" => $api_key);
$client = new ErrormatorClient($opts);
~~~

If you want to send a slow report, the data structure looks like:
~~~
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
~~~
keep in mind, that you can always post more than just one report with one request, just add another array into the first array.


If you want to send an error report, the data structure looks like:
~~~
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
~~~
and ofcourse the same rule applies here for sending more than just one report, you add another array in the first array

If you want to send a log, the data structure looks like:
~~~
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
            );
~~~
here you already see how you send more than 1 log

Using cakePHP?
Try my AppError and AppLog class for the php-errormator-client in the subfolder cakePHP.
Here is how to set it up [cakePHP set up errorhandlers](http://book.cakephp.org/2.0/en/development/errors.html)

Using no Framework?
You're welcome too to look at noFramework\example.php and noFramework\example2.php :) 
