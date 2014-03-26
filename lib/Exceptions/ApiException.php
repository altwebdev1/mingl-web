<?php

namespace Exceptions;

class ApiException extends \Exception {

    private $httpStatusCode;
    private $errors;

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function getErrors()
    {
        return $this->errors;
    }
	

    /**
     * An ApiException is used to better inform the user and provide more information to the api user
     * @param string $message
     * @param int $httpStatusCode
     * @param null $errors
     * @param int $code
     */
    public function __construct($message, $httpStatusCode, $errors = null, $code = 0)
    {
       	$this->httpStatusCode = $httpStatusCode;
        $this->errors = $errors;		
        parent::__construct($message,$code);
    }
}