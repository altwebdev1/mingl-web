<?php

class interminglApiResponseMeta {

    public $code;
    public $errorType;
    public $errorMessage;
    public $errors;

    public $dataPropertyName;

    public $limit;
    public $offset;
    public $totalResults;
    public $query;
    public $searchTime;

    /**
     * Constructor
     */
    public function __construct() {


    }

    /**
     * @return array
     */
    public function toArray() {

        function filterArrayByValue($value)
        {
            if($value === null)
                return false;
            else
                return true;
        }

        $array = (array)($this);
        $array = (object) array_filter($array, 'filterArrayByValue');

        return $array;
    }

}