<?php
/**
 * Description of UnresolvedDependenciesException
 *
 * @author ss89
 */
namespace ErrormatorClient;
class UnresolvedDependenciesException extends \Exception implements ClientException
{
    protected $message="";
    protected $code=0;
    private $previous=0;
    function __construct($message, $code=0, $previous=null)
    {
        $this->message=$message;
        $this->code=$code;
        $this->previous=$previous;
        parent::__construct($message, $code, $previous);
        return $this;
    }
    public function getErrorMessage()
    {
        return "Error Code: ".$this->code.", Message: ".$this->message;
    }
}

?>