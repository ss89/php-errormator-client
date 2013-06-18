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

class AppLog
{

    public static function createLogEntry()
    {
        $options = array("scheme" => "https", "api_key" => "554e4ecc3a5c42f0b5b2ef1b9c3047cc", "debug" => false);
        $errormatorClient = new ErrormatorClient($options);
        $username = AuthComponent::user("username");
        if (empty($username))
        {
            $username = AuthComponent::user("key");
        }
        if (empty($username))
        {
            $username = "unauthorized user";
        }
        $data = array(
            array(
                "client" => "php-ss89",
                "log_level" => "INFO",
                "message" => "[" . $_SERVER["REMOTE_ADDR"] . "] $username accesses " . "http://" . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'],
                "server" => $_SERVER["HTTP_HOST"],
                "date" => date("d.m.Y H:i:s"),
                "namespace" => date("d.m.Y H:i:s"),
            )
        );
        $ret = $errormatorClient->log($data);
        if (Configure::read("debug") > 0 && $ret != "OK: Reports accepted")
        {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            echo "<pre>";
            print_r($ret);
            echo "</pre>";
        }
        return array("data" => $data, "return" => $ret);
    }

}