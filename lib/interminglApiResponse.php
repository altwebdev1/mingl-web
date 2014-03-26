<?php


class interminglApiResponse {

    /**
     * @var interminglApiResponseMeta
     */
    public $meta;

    /**
     * @var object
     */
    public $returnedObject;

    /**
     * @var array
     */
    public $notificationMessages;

    /**
     * @var \Slim\Http\Response
     */
    private $_response;

    /**
     * Set the http status of the response
     * @var $status int HttpStatus
     */
    public function setStatus($status){
        $this->_response->status($status);
        $this->meta->code = $status;
    }

    /**
     * Constructor
     * @var $httpStatusCode int
     */
    public function __construct() {

        /**
         * Set the response
         * @var $app Slim\Slim
         */
        $app = Slim\Slim::getInstance();
        $this->_response = $app->response();
        $this->_response['Content-Type'] = 'application/json';

        $this->meta = new interminglApiResponseMeta();
        $this->notificationMessages = [];
    }

    /**
     * @return array
     * @throws Exception 'dataPropertyName' in the response meta data must be set
     */
    public function toArray() {

        $array = [];
        $array['meta'] = $this->meta->toArray();

        if($this->returnedObject)
        {
            if(!$this->meta->dataPropertyName){
                throw new Exception("You have to set the 'dataPropertyName' in the response meta data ");
            }
            else{
                $array[$this->meta->dataPropertyName] = (array)$this->returnedObject;
            }
        }

        if(count($this->notificationMessages) > 0)
            $array['notifications'] = $this->notificationMessages;

        return $array;
    }

    /**
     * Add a notification message for the user
     */
    public function addNotification($message)
    {
        array_push($this->notificationMessages, $message);
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString(){
        return json_encode($this->toArray());
    }
}