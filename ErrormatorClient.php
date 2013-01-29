<?php

/**
 * Errormator Abstraction Class 
 * It uses CURL extension of PHP, make sure to enable it first OR catch the CurlNotFound Exception
 *
 * @author strussi
 */
class ErrormatorClient
{

    public $curl;
    public $scheme;
    public $api_key;
    public $debug=false;

    public function __construct($data)
    {
        $check = $this->__checkPrerequisites();
        if (!$check)
        {
            throw new CurlNotFound;
        }
        $this->scheme = $data['scheme'];
        $this->api_key = $data['api_key'];
        $this->curl = curl_init();
        if(isset($data['debug']) && $data['debug']===true)
        {
            $this->debug=true;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-errormator-api-key: ' . $this->api_key));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        $api_key_valid=false;
        $api_key_valid=  $this->__checkApiKey();
        if(!$api_key_valid)
        {
            if($this->debug===true)
            {
                echo "<br>API KEY NOT VALID<br>";
            }
            return false;
        }
    }
    
    public function __checkApiKey()
    {
//        403 Forbidden
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        $response=  $this->send_curl_request($this->scheme . "://api.errormator.com/api/slow_reports?protocol_version=0.3", array());
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        if($this->debug===true)
        {
            echo "API KEY VALID CHECK RESPONDED: ".$response."<br>";
        }
        if(strstr($response,"403 Forbidden"))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function __checkPrerequisites()
    {
        return function_exists("curl_init");
    }

    public function slow($data)
    {
        $return = false;
        if ($this->scheme)
        {
            $return = $this->send_curl_request($this->scheme . "://api.errormator.com/api/slow_reports?protocol_version=0.3", $data);
        }
        return $return;
    }

    public function error($data)
    {
        $return = false;
        if ($this->scheme)
        {
            $return = $this->send_curl_request($this->scheme . "://api.errormator.com/api/reports?protocol_version=0.3", $data);
        }
        return $return;
    }

    public function log($data)
    {
        $return = false;
        if ($this->scheme)
        {
            $return = $this->send_curl_request($this->scheme . "://api.errormator.com/api/logs?protocol_version=0.3", $data);
        }
        return $return;
    }

    public function send_curl_request($url, $data)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $json_data = json_encode($data);
        if($this->debug===true)
        {
            echo "<br>" . $json_data . "<br>";
        }
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json_data);
        return curl_exec($this->curl);
    }

}

?>