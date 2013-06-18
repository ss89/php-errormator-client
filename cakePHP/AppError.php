<?php

/**
 * Created by JetBrains PhpStorm.
 * User: webdev
 * Date: 24.05.13
 * Time: 09:38
 * To change this template use File | Settings | File Templates.
 */
App::uses("ErrormatorClient", "Vendor/php-errormator-client");
App::uses("AuthComponent", "Controller/Component");

class AppError
{
    public static function handleError($code, $description, $file = null, $line = null, $context = null)
    {
        $options = array("scheme" => "https", "api_key" => "554e4ecc3a5c42f0b5b2ef1b9c3047cc", "debug" => false);
        $errormatorClient = new ErrormatorClient($options);
        $username = AuthComponent::user("username");
        if (empty($username))
        {
            $username = AuthComponent::user("key");
        }
        $data = array(
            array(
                "traceback" => Debugger::trace(array("format"=>"txt")),
                "priority" => 1,
                "error_type" => $description,
                "occurences" => 1,
                "http_status" => $code,
                "client" => "php-ss89",
                "server" => $_SERVER["HTTP_HOST"],
                "report_details" => array(
                    array(
                        "start_time" => date("Y-m-d H:i:s.u",  gmmktime()),
                        "username" => $username,
                        "url" => "http://" . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'],
                        "ip" => $_SERVER["REMOTE_ADDR"],
                        "user_agent" => $_SERVER["HTTP_USER_AGENT"],
                        "message" => $description,
                        "request" => array(
                            "REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"],
                            "PATH_INFO" => $file . ":" . $line,
                            "POST" => print_r($_POST, true)
                        ),
                    )
                )
            )
        );
        $ret = $errormatorClient->error($data);
        if (Configure::read("debug") > 1 && $ret != "OK: Reports accepted")
        {
            echo "<pre>handleError:\n";
            print_r($data);
            echo "</pre>";
            echo "<pre>";
            print_r($ret);
            echo "</pre>";
        }
        return ErrorHandler::handleError($code, $description, $file, $line, $context);
    }

    public static function handleException(Exception $exception)
    {
        $options = array("scheme" => "https", "api_key" => "554e4ecc3a5c42f0b5b2ef1b9c3047cc", "debug" => false);
        $errormatorClient = new ErrormatorClient($options);
        $username = AuthComponent::user("username");
        if (empty($username))
        {
            $username = AuthComponent::user("key");
        }
        $data = array(
            array(
                "traceback" => $exception->getTraceAsString(),
                "priority" => 1,
                "error_type" => $exception->getMessage(),
                "occurences" => 1,
                "http_status" => $exception->getCode(),
                "client" => "php-ss89",
                "server" => $_SERVER["HTTP_HOST"],
                "report_details" => array(
                    array(
                        "start_time" => date("Y-m-d H:i:s.u",  gmmktime()),
                        "username" => $username,
                        "url" => "http://" . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'],
                        "ip" => $_SERVER["REMOTE_ADDR"],
                        "user_agent" => $_SERVER["HTTP_USER_AGENT"],
                        "message" => $exception->getMessage(),
                        "request" => array(
                            "REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"],
                            "PATH_INFO" => $exception->getFile() . ":" . $exception->getLine(),
                            "POST" => $_POST
                        ),
                    )
                )
            )
        );
        $ret = $errormatorClient->error($data);
        if (Configure::read("debug") > 1 && $ret != "OK: Reports accepted")
        {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            echo "<pre>";
            print_r($ret);
            echo "</pre>";
        }
        return ErrorHandler::handleException($exception);
    }

}