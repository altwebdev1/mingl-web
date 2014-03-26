<?php

/**
 * Description of Model_General
 *
 * @author eventurers
 */
use RedBean_Facade as R;
use Helpers\PasswordHelper as PasswordHelper;
use Enumerations\HttpStatusCode as HttpStatusCode;
use Enumerations\AccountType as AccountType;
use Enumerations\StatusType as StatusType;
use Enumerations\ErrorCodeType as ErrorCodeType;
use Exceptions\ApiException as ApiException;
use Valitron\Validator as Validator;


class Model_General extends RedBean_SimpleModel {

    /**
     * Identifier
     * @var int
     */
    public $id;

	 /**
     * Identifier
     * @var int
     */
	 public $JobTypeName;
	 
	 /**
     * Identifier
     * @var int
     */
	 public $Popular;
	 
	 /**
     * Identifier
     * @var int
     */
	 public $Status;

    /**
     * Constructor
     */
    public function __construct() {

    }

	/**
     * get job type
     */
    public function getStaticPages() {
		/**
         * Query to get static contents
         */
		$sql = "SELECT PageName,Content FROM staticpages";
   		$result = R::getAll($sql);
		if(is_array($result) && count($result) >0) {
			/**
             * The users were found
             */
			foreach($result as $key=>$value) {
					$content    = array("PageName"			=>		$value['PageName'],
										"Content"			=>		ucfirst($value['Content']),
										);
					$StaticArray[]	=	$content;
			}
			return $StaticArray;
		} else {
			 /**
	         * throwing error when static data
	         */
			  throw new ApiException("No results Found", HttpStatusCode::Forbidden,ErrorCodeType::NoResultFound);
		}
	}
}