<?php

/**
 * Errormator Abstraction Class 
 * It uses CURL extension of PHP, make sure to enable it first OR catch the Exception
 *
 * @author strussi
 */
class ErrormatorClient
{

    public $curl;
    public $scheme;
    public $apiKey;
    public $debug = false;
    private $apiVersion = "0.3";
    private $client = "php-ss89";

    public function __construct($data)
    {
        $check = $this->checkPrerequisites();
        if (!$check)
        {
            throw new Exception("Curl not Found in your Configuration or not loaded. Please load the curl.(so/dll) and restart you Web server/PHP Fast CGI");
        }
        $this->setDefaultSettings($data);
    }

    private function setDefaultSettings($data)
    {
        $this->scheme = $data['scheme'];
        $this->apiKey = $data['api_key'];
        $this->curl = curl_init();
        if (isset($data['debug']) && $data['debug'] === true)
        {
            $this->debug = true;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-errormator-api-key: ' . $this->apiKey));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
    }

    public function genUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    public function checkApiKey()
    {
//      403 Forbidden = API Key not valid
        array(
            array(
                "client" => $this->client,
                "log_level" => "INFO",
                "message" => "Testing API Key",
                "server" => $_SERVER["HTTP_HOST"],
                "date" => date("d.m.Y H:i:s"),
                "namespace" => "ErrormatorClient",
                "request_id" => $this->genUuid(),
            )
        );
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        $response = $this->sendCurlRequest($this->scheme . "://api.errormator.com/api/logs?protocol_version=" . $this->apiVersion, $data);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        if ($this->debug === true)
        {
            echo "API KEY VALID CHECK RESPONDED: " . $response . "<br>";
        }
        if (strstr($response, "403 Forbidden"))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function checkPrerequisites()
    {
        return function_exists("curl_init");
    }

    public function slow($data)
    {
        $return = false;
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if (is_array($value['report_details']))
                {
                    foreach ($value['report_details'] as $ukey => $uvalue)
                    {
                        if (!isset($uvalue['request_id']) || empty($uvalue['request_id']))
                        {
                            $data[$key]['report_details'][$ukey]['request_id'] = $this->genUuid();
                        }
                    }
                }
                else
                {
                    return false;
                }
            }
            if(!isset($data[0]['client']))
            {
                $data[0]['client']=$this->client;
            }
            if ($this->scheme)
            {
                $return = $this->sendCurlRequest($this->scheme . "://api.errormator.com/api/slow_reports?protocol_version=" . $this->apiVersion, $data);
            }
        }
        return $return;
    }

    public function error($data)
    {
        $return = false;
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if (is_array($value['report_details']))
                {
                    foreach ($value['report_details'] as $ukey => $uvalue)
                    {
                        if (!isset($uvalue['request_id']) || empty($uvalue['request_id']))
                        {
                            $data[$key]['report_details'][$ukey]['request_id'] = $this->genUuid();
                        }
                    }
                }
                else
                {
                    return false;
                }
            }
            if(!isset($data[0]['client']))
            {
                $data[0]['client']=$this->client;
            }
            if ($this->scheme)
            {
                $return = $this->sendCurlRequest($this->scheme . "://api.errormator.com/api/reports?protocol_version=" . $this->apiVersion, $data);
            }
        }
        return $return;
    }

    public function log($data)
    {
        $return = false;
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if (!isset($value['request_id']) || empty($value['request_id']))
                {
                    $data[$key]['request_id'] = $this->genUuid();
                }
            }
            if ($this->scheme)
            {
                $return = $this->sendCurlRequest($this->scheme . "://api.errormator.com/api/logs?protocol_version=" . $this->apiVersion, $data);
            }
        }
        return $return;
    }

    public function sendCurlRequest($url, $data)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $json_data = json_encode($data);
        if ($this->debug === true)
        {
            echo "<br>" . $json_data . "<br>";
        }
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($this->curl);
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function setApiVersion($version)
    {
        return $this->apiVersion = $version;
    }

    public function getClient()
    {
        return $this->client;
    }
    
    public function setClient($name)
    {
        return $this->client = $name;
    }
}

?>
