<?php

/**
 * Description of Model_Users
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
use Helpers\RedBeanHelper as RedBeanHelper;

class Model_Users extends RedBean_SimpleModel implements ModelBaseInterface {

    /**
     * Identifier
     * @var int
     */
    public $id;

    /**
     * When the record was created
     * @var int
     */
    public $DateCreated;

    /**
     * When the record was last updated
     * @var int
     */
    public $DateModified;
	

    /**
     * Constructor
     */
    public function __construct() {

    }
	
	 /**
     * Create an user account
	 * Validation for email , fbId,LinkedInId, Username
     */
    public function create($userId){ // creating
		 /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean 		= $this->bean;
		
		// validate the model
        $this->validate();

       	// validate the creation
        $this->validateUser($userId);
		$curr_date	= date('Y-m-d H:i:s');
		$bean->Status		= StatusType::InactiveStatus;
		$bean->DateCreated	= $curr_date;
		$bean->DateModified	= $curr_date;
		// save the bean to the database
        $tagId = R::store($bean);
		
		//Return the id of new tag
		return $tagId;
    }
	public function modify() {}
	
	 /**
     * Validate the model
     * @throws ApiException if the models fails to validate required fields
     */
    public function validate() {
		$rules = [
					'required' => [
						['Tag']
					]
				];
		$bean = $this->bean;
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the tag properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
		
    }
	/**
     * Validate the creation of an account
     * @throws ApiException if the user being creating the account with already exists of email , facebook and linked ids in the database.
     */
    public function validateUser($userId) {
		 /**
         * Get the identity of the person requesting the details
         */
       
		$user = R::findOne('users', 'id = ? and status = ?', [$userId,StatusType::ActiveStatus]);
	
		if (!$user) {
			// the user was not found
			throw new ApiException("Invalid user details", ErrorCodeType::UserNotInActiveStatus);
		}
	}
	/**
     * Validate the creation of an account
     * @throws ApiException if the user being creating the account with already exists of email , facebook and linked ids in the database.
     */
    public function validateCreate() {}
		
}