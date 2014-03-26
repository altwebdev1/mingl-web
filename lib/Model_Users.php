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
     * User email
     * @var string
     */
    public $Email;
	
    /**
     * Person first name
     * @var string
     */
    public $FirstName;

    /**
     * Person last name
     * @var string
     */
    public $LastName;

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
     * Check the Login details
    */
	public static function checkLogin($email,$password,$facebookId,$linkedInId,$deviceToken,$endpointARN,$platform) {
	
		// validate the login
		if($facebookId == '' && $linkedInId == '' && $email =='' && $password =='') {
			// the User credentials was not found
           	throw new ApiException("The user credentials were incorrect", ErrorCodeType::SomeFieldsMissing);
		}
       	
	   /**
         * @var Model_User
         */		
		if($facebookId != ''){
			$result = R::findOne('users', 'FacebookId = ? and ( Status = ? || Status = ? ) ', array($facebookId,StatusType::ActiveStatus,StatusType::Incomplete));
		}	
		else if($linkedInId != ''){
			$result = R::findOne('users', 'LinkedInId = ? and ( Status = ? || Status = ? ) ', array($linkedInId,StatusType::ActiveStatus,StatusType::Incomplete));
		}
		else{
		//	R::debug(true);
			$result = R::findOne('users', 'Email = ? and Password = ? and ( Status = ? || Status = ? )', array($email, PasswordHelper::encrypt($password),StatusType::ActiveStatus,StatusType::Incomplete));
		}
        if (!$result) {
            return false;
        }
        else {
			if($result->Status == StatusType::DeleteStatus) {
				// the User was not found
				throw new ApiException("User has suspended form Intermingl.", ErrorCodeType::UserNotInActiveStatus);
			} else if($result->Status == StatusType::InactiveStatus) {
				// the User was not found
				throw new ApiException("User not in active state.", ErrorCodeType::UserNotInActiveStatus);
			} else {
				$tokenArray['UserId'] = $result->id;
				$tokenArray['DeviceToken'] = $deviceToken;
				$tokenArray['EndPointARN'] = $endpointARN;
				$tokenArray['Platform'] = $platform;
				$users = new Model_Users();
				$users->addTokens($tokenArray);
				return array('UserId' => $result->id, 'UserStatus' => $result->Status);
			}
        }
    }
	
	/**
     * Update Password
     * @param updating password by using word table from database
     */
	public function updatePassword() {	 
			
			$bean = $this->bean;
			
			//validate param
			$this->validateEmail();
			
			// validate the modification
       	    $this->validateUpdatePassword();
			
			$wordResult 			= R::findOne('words', '1 order by rand()',[]);
			$word           		= $wordResult->words;
			$numeric        		= '1234567890';
			$numbers        		= substr(str_shuffle($numeric), 0, 3);
			$newPassword    		= trim($word.$numbers).$bean->id;
			$bean->ActualPassword 	= $newPassword;
			
			// encrypt the password
        	$bean->Password = PasswordHelper::encrypt($newPassword);
			
      		 // save the bean to the user table
       		$userId 	= R::store($this);
			
			$userDetails['id'] = $userId;
			$userDetails['Password'] = $newPassword;
			$userDetails['Name'] = $bean->FirstName;
			$userDetails['Email'] = $bean->Email;
			return $userDetails;
	}
	/**
     * the Email requested for forgetpassword
     * @throws ApiException if the Email not exists and the person in active status can able to request for new password
     */
    public function validateUpdatePassword() {
        /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean = $this->bean;

        /**
         * check email is exists
         * @var Model_User
         */
        $user = R::findOne('users', 'Email=? and Password != "" order by id desc',  [$bean->Email]);
        if(!$user) {
            /**
	         * No Emial registered with email address
	         */
            throw new ApiException("You are not registered with Email Signup process", ErrorCodeType::NoEmailAddressExists);
        }
       /* else if($user->Status != StatusType::ActiveStatus)	// REMOVED FOR FIRST DEMO TO WORK WITH INACTIVE USER // 12/03/14
        {
             
	         // Only active user can request for request forgetpassord for this account  
	         
			  throw new ApiException("You are not authorized to request forgot password for this account", ErrorCodeType::NoAuthoriseToRequestForPassword);
		}*/
		else{
			$bean->id = $user->id;
			$bean->Email = $user->Email;
			$bean->FirstName = $user->FirstName;
		}
	}
	 /**
     * Validate the fields (Email) for forgotPassword
     * @throws ApiException if the models fails to validate
     */
    public function validateEmail()
    {
		$rules = [
            'required' => [
               ['Email']
            ],
			'email' => 'Email',
        ];

        $v = new Validator($this->bean);
        $v->rules($rules);

        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the Email" ,  ErrorCodeType::SomeFieldsMissing, $errors);
        }
    }
	 /**
     * Create an user account
	 * Validation for email , fbId,LinkedInId, Username
     */
    public function create() { // broadtags creating

		 
		 /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean 		= $this->bean;
		$flag 		= $bean->PhotoFlag;
		
		
		// validate the model
        $this->validate();

        // validate the creation
        $this->validateCreate();
		
		if($flag != 0) {
			//error throw for image error
			if($flag == 1)
				throw new ApiException("Problem in Image - Type", ErrorCodeType::ProblemInImage);
			else if($flag == 2)
				throw new ApiException("Problem in Image",ErrorCodeType::ProblemInImage);
			else if($flag == 3)
				throw new ApiException("Problem in Image - Size",ErrorCodeType::ProblemInImage);
			else if($flag == 4)
				throw new ApiException("Problem in Image - Dimension.Minimum should be (100X100)",ErrorCodeType::ProblemInImage);
		}
		
		unset($bean->PhotoFlag);
		
        $bean->DateCreated 			= date('Y-m-d H:i:s');
        $bean->DateModified 		= $bean->DateCreated;
		
		// encrypt the password
		if($bean->Password){
			$bean->ActualPassword  	= $bean->Password ;
        	$bean->Password 		= PasswordHelper::encrypt($bean->Password);
		}
		//save ip address
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$bean->IpAddress = $ip;
		$bean->Status    = StatusType::Incomplete;
		 
		 $email	=	$bean->Email;
		// save the bean to the database
        $userId = R::store($this);
		
		// To insert a Primary card for the user 
		$cardExist	=	R::findone('cards','fkUsersId = ?',[$userId]);
		if(empty($cardExist))	{
			$card				=	R::dispense('cards');
			$card->fkUsersId	=	$userId;
			$card->Card			=	'1';
			$card->Email		=	$email;
			$card->Status		=	StatusType::ActiveStatus;
			$card->DateCreated 	=	date('Y-m-d H:i:s');
			$card->DateModified =	$bean->DateCreated;
			$cardId				=	R::store($card);
		}
		//Return the id of new user
		return $userId;
    }
	 /**
     * Registration Step 2 an user account
	 * Validation for email , fbId,LinkedInId, Username
     */
    public function registrationStep2(){ 

		
		 /**
         * Get the bean
         * @var $bean Model_User
         */
		$phone 	= $fbStatus 	= $linkedStatus = $twitterStatus = $googleStatus = $summary = "";
		$tagArray 	= $eduArray	= $interest_array = array();
        $bean 	= $this->bean;
		$flag 	= $bean->PhotoFlag;
		$cardId	= '';
		
		// validate the model
        $this->validateReg2();

        // validate the creation
        $this->validateUserReg2();

		if(isset($bean->id))	{
			$userId	=	$bean->id;
		}
		if(isset($bean->CardId))	{
			$cardId			=	$bean->CardId;
			unset($bean->CardId);
		}

		// To validate the card information
		if(isset($cardId)	&&	$cardId	!=	"")	{
			$this->validateCardInformation($cardId,$userId,1);
		}
		else	{
			$cardInfo	=	R::findone('cards','fkUsersId	=	?',[$userId]);
			if($cardInfo)
				$cardId		=	$cardInfo->id;
		}
	
		if(isset($bean->Interests))		{
		$interest_array	=	$bean->Interests;
		unset($bean->Interests);
		}
		
		$userInfo 	= 	R::findOne('users','id = ?',[$bean->id]);
		$userStatus	=	$userInfo->Status;
		
        $bean->DateModified	=	date('Y-m-d H:i:s');
		$bean->Status    	=	StatusType::ActiveStatus;

		// save the bean to the database
		if(isset($bean->Phone))	{			
			$phone			=	$bean->Phone;
			unset($bean->Phone);
		}
		if(isset($bean->FbStatus))	{			
			$fbStatus		=	$bean->FbStatus;
			unset($bean->FbStatus);
		}
		if(isset($bean->LinkedStatus))	{
			$linkedStatus	=	$bean->LinkedStatus;
			unset($bean->LinkedStatus);
		}
		if(isset($bean->TwitterStatus))	{	
			$twitterStatus	=	$bean->TwitterStatus;
			unset($bean->TwitterStatus);
		}
		if(isset($bean->GoogleStatus))	{		
			$googleStatus	=	$bean->GoogleStatus;
			unset($bean->GoogleStatus);
		}
		if(isset($bean->Education))	{		
			$eduArray		=	$bean->Education;
			unset($bean->Education);
		}
		if(isset($bean->Tags))	{		
			$tagArray			=	$bean->Tags;
			unset($bean->Tags);
		}

		if(isset($bean->Summary))	{
			$summary		=	$bean->Summary;
			unset($bean->Summary);
		}
		//if($_SERVER['REMOTE_ADDR']=='172.21.4.140')
		//	echo "<pre>";print_R($this);echo "</pre>";
        $userId = R::store($this);
		
		$cardUpdate					=	R::dispense('cards');
		if($cardId			!=	"")
			$cardUpdate->id			=	$cardId;
		if($phone			!=	"")		
			$cardUpdate->Phone		=	$phone;			
		if($fbStatus		!=	"")	
			$cardUpdate->Facebook	=	$fbStatus;
		if($linkedStatus	!=	"")	
			$cardUpdate->LinkedIn	=	$linkedStatus;	
		if($twitterStatus	!=	"")
			$cardUpdate->Twitter	=	$twitterStatus;
		if($googleStatus	!=	"")
			$cardUpdate->GooglePlus	=	$googleStatus;
		if($summary			!=	"")
			$cardUpdate->Summary	=	$summary;
		$cardUpdate->DateModified	=	date('Y-m-d H:i:s');
		$cardId						=	R::store($cardUpdate);
		// End of Updating the Card Details

		
		$user						=	R::dispense('users');
		$user->Interests			=	$interest_array;
		$user->userId				=	$userId;
		$user->cardId				=	$cardId;
		
		// BEGIN: To update User Interest
		$user->addInterestForUser();
		unset($user->Interests);
		// END: To update User Interest
		
		// BEGIN :	Insert Education field for the User
		$user->eduArray		=	$eduArray;
		$user->addEducation();
		// END :	Insert Education field for the User
		
		// BEGIN: To Insert Tag Details for the User
		if(isset($tagArray)	&&	is_array($tagArray)	&&	count($tagArray)>0	)	{
			$tagDelete			=	R::find('usertags','fkUsersId = ? and tofkUsersId = ? and Status = ? ',[$userId,$userId,StatusType::ActiveStatus]);
			if($tagDelete)	{
				foreach($tagDelete as $key=>$value)	{
					$updateTag					=	R::dispense('usertags');
					$updateTag->Status			=	StatusType::DeleteStatus;
					$updateTag->DateModified	=	date('Y-m-d H:i:s');
					$updateTag->id				=	$value['id'];
					R::store($updateTag);
				}
			}
			foreach($tagArray	as $key=>$value)	{
				$tagId			=	$this->findAndGetTag($value);
				$user			=	R::dispense('users');
				$user->tagId	=	$tagId;
				$user->toUserId	=	$userId;
				$user->userId	=	$userId;
				$user->cardId	=	$cardId;
				$user->checkTagAlreadyExists(1);
				$user->addUserTag();
			}
		}
		// END: To Insert Tag Details for the User
		
		/**
		 * TODO FOR FIRST RUN
		 */
		if ($userStatus == StatusType::Incomplete) {
			$event = R::dispense('events');
			$event->firstRun($bean->id);
		}
		//Return the id of new user
		return $userId;
    }
	
	public function	addUserTag()	{
		$bean	=	$this->bean;
	//	echo "<pre>";print_r($bean);echo "</pre>";
		$usertags	=	R::dispense('usertags');
		$usertags->fkUsersId	=	$bean->userId;
		$usertags->tofkUsersId	=	$bean->toUserId;
		$usertags->fkTagsId		=	$bean->tagId;
		$usertags->fkCardsId	=	$bean->cardId;
		$usertags->Status		=	StatusType::ActiveStatus;
		$usertags->DateCreated	=	date('Y-m-d H:i:s');
		$usertags->DateModified	=	$usertags->DateCreated;
		R::store($usertags);
	}
	
	/**
	* To Add Education information for the user
	*/
	public function addEducation($subvalue = 0)	{
		$bean		=	$this->bean;
		$cardId		=	$bean->cardId;
		$userId		=	$bean->userId;
		$eduArray	=	$bean->eduArray;
		if(isset($eduArray)	&&	is_array($eduArray)	&&	count($eduArray)>0	)	{
		//	R::debug(true);
			$educationDelete				=	R::find('education','fkUsersId = ? and fkCardsId = ? and Status = ? ',[$userId,$cardId,StatusType::ActiveStatus]);
			if(!empty($educationDelete))		{
				foreach($educationDelete as $key=>$value)	{
					$updateEdu				=	R::dispense('education');
					$updateEdu->Status		=	StatusType::DeleteStatus;
					$updateEdu->DateModified=	date('Y-m-d H:i:s');
					$updateEdu->id			=	$value['id'];
					R::store($updateEdu);
				}
			}
			//echo "=========>";

			foreach($eduArray	as $key=>$value)
			{
				$run						=	0;
				$education					=	R::dispense('education');
				$education->fkCardsId		=	$cardId;
				$education->fkUsersId		=	$userId;
				$education->Status			=	StatusType::ActiveStatus;
				$education->DateCreated		=	date('Y-m-d H:i:s');
				$education->DateModified	=	$education->DateCreated;
				$educationExist	=	R::findone('education','fkCardsId = ? and fkUsersId	= ?	and	Course = ? and StartYear = ? and CompleteYear = ? and Status = ?',[$cardId,$userId,$value->Course,$value->StartYear,$value->CompleteYear,StatusType::ActiveStatus]);
				if(empty($educationExist))	{
					if(isset($value->Course)	&&	$value->Course	!="")	{
						$education->Course		=	$value->Course;
						$run	=	1;
					}
					if(isset($value->StartYear)	&&	$value->StartYear	!="")	{
						$education->StartYear	=	$value->StartYear;
						$run	=	1;
					}
					if(isset($value->CompleteYear)	&&	$value->CompleteYear	!="")	{
						$education->CompleteYear	=	$value->CompleteYear;
						$run	=	1;
					}
					if($run	==	1)	
						R::store($education);
				}
			}
			//exit;
		}
	}
	
	/**
	* To validate the Card Information
	* @throw ApiException if the user has already connected or liked
	*/
	public function validateCardInformation($cardId	=	0,$userId	=	0,	$subvalue	=	0)	{
		$cardInfo	=	"";
		$cardInfo	=	R::findone('cards','id	=	?	and	fkUsersId	=	?	and Status = ? ',[$cardId,$userId,StatusType::ActiveStatus]);
		if(empty($cardInfo)	&&	$subvalue	==	0)	{
			throw new ApiException("Requested Card doesn't exist",ErrorCodeType::CardNotExist);
		}
		return $cardInfo;
	}
	
	
	
	/** ADD INTERESTS TO THE USER **/
	public function addInterestForUser() {
		
		/**
         * Get the bean
         * @var $bean Model_User
         */
		$bean 			= $this->bean;
		$DateCreated 	= date('Y-m-d H:i:s');
		$DateModified	= $DateCreated;
//		echo "<pre>";print_R($bean);
		if(!empty($bean->Interests)) {
			$i = 0;
			$interestArray	= array();
			R::exec('UPDATE userinterests SET Status = '.StatusType::DeleteStatus.' WHERE fkUsersId = '.$bean->userId);	//and fkCardsId = '.$bean->cardId'    on 25/03/2014
			foreach($bean->Interests as $value) {
				$interest 		= R::findOne('interests','Interest = ? AND Status = ?',[$value,StatusType::ActiveStatus]);
				if($interest)	{
					$interestId	= $interest->id;
				}
				else {
					$interestBean						= R::dispense('interests');
					$interestBean->Interest				= $value;
					$interestBean->Status 				= StatusType::ActiveStatus;
					$interestBean->DateCreated 			= $DateCreated;
					$interestBean->DateModified 		= $DateModified;
					$interestId 						= R::store($interestBean);
				}
				if($interestId != '') {
					$interestArray[$i]					= R::dispense('userinterests');
					$interestArray[$i]->fkUsersId 		= $bean->userId;
					$interestArray[$i]->fkInterestsId 	= $interestId;
					$interestArray[$i]->fkCardsId		= $bean->cardId;
					$interestArray[$i]->Status 			= StatusType::ActiveStatus;
					$interestArray[$i]->DateCreated 	= $DateCreated;
					$interestArray[$i]->DateModified 	= $DateModified;
					$i++;
				}
			}
			if(!empty($interestArray))	{
				$insertId	=	R::storeAll($interestArray);
				return $insertId;
			}
		}
		
		//$interest = R::findOne('interests','');	//userinterests
	}
	/**
     * @param Modify the user entity
	 * Throws exception for email , facebook , linkedInId already condition
     */
    public function modify(){

		/**
         * Get the bean
         * @var $bean Model_User
         */
		$bean = $this->bean;
		unset($bean->PhotoFlag);
		$bean->DateModified 	= date('Y-m-d H:i:s');
		
        // modify the bean to the database
        R::store($this);
    }
	
	/**
     * @param Get user details
     */
    public function getUserDetails($userId,$requestedBy) {
		
		//	validate the UserAccount
		$this->validateUser($userId);
		//R::debug(true);
		$tag	=	$interest	=	$education	=	$note	=	array();		
		
		// To Retrive the Tags given for the Requested User
   		$query			=	" SELECT Tags from tags as t left join usertags as ut on (t.id = ut.fkTagsId) where ut.tofkUsersId = ".$userId." and ut.Status = ".StatusType::ActiveStatus;
		$tagArray		=	R::getAll($query);
		if(!empty($tagArray))	{
			foreach($tagArray	as $key=>$value)
				$tag[]	=	$value['Tags'];
		}
		
		// To Retrive the Interests for the Requested User
		$query				=	" SELECT Interest from interests as i left join userinterests as ui on (i.id = ui.fkInterestsId) where ui.fkUsersId = ".$userId." and ui.Status = ".StatusType::ActiveStatus;
		$interestArray		=	R::getAll($query);
		if(!empty($interestArray))	{
			foreach($interestArray	as $key=>$value)
				$interest[]	=	$value['Interest'];
		}
		
		// To Retrive the Education Details of the Requested User
		$educationArray		=	R::find('education','fkUsersId=? and Status = ?',[$userId,StatusType::ActiveStatus]);
		if(!empty($educationArray))	{
			foreach($educationArray	as $key=>$value)	{
				$education[$key]['Course']			=	$value['Course'];
				$education[$key]['StartYear']		=	$value['StartYear'];
				$education[$key]['CompleteYear']	=	$value['CompleteYear'];
			}
		}
		
		// To Retrive the Notes Details of the Requested User
		$noteArray		=	R::find('notes','tofkUsersId=?',[$userId]);
		if(!empty($noteArray))	{
			foreach($noteArray	as $key=>$value)	{
				$note[$key]['Note']			=	$value['Note'];
			//	if($value['Photo']!="")
			//		$value['Photo']			=	USER_IMAGE_PATH.$value['Photo'];
				$note[$key]['Photo']		=	$value['Photo'];
			}
		}
		
		// To Retrive the User Informations
		$userArray		=	R::findone('users',' id = ? and Status = ? ', [$userId,StatusType::ActiveStatus]);
		
		if($userArray)	{
			$returnArray['UserId']			=	$userArray->id;
			$returnArray['FirstName']		=	$userArray->FirstName;
			$returnArray['LastName']		=	$userArray->LastName;
			$returnArray['Email']			=	$userArray->Email;
			if($userArray->Photo!="")
				$userArray->Photo			=	USER_IMAGE_PATH.$userArray->Photo;
			$returnArray['Photo']			=	$userArray->Photo;
			$returnArray['FacebookId']		=	$userArray->FacebookId;
			$returnArray['LinkedInId']		=	$userArray->LinkedInId;
			$returnArray['TwitterId']		=	$userArray->TwitterId;
			$returnArray['GooglePlusId']	=	$userArray->GooglePlusId;
		//	$returnArray['Interest']		=	$userArray->Interest;
		//	$returnArray['Summary']			=	$userArray->Summary;
			$returnArray['Company']			=	$userArray->Company;
			$returnArray['Title']			=	$userArray->Title;
			$returnArray['Location']		=	$userArray->Location;
			$returnArray['City']			=	$userArray->City;
			$returnArray['State']			=	$userArray->State;
			$returnArray['Country']			=	$userArray->Country;
		//	$returnArray['Phone']			=	$userArray->Phone;
			$returnArray['AppNetId']		=	$userArray->AppNetId;
			$returnArray['Status']			=	$userArray->Status;
			$returnArray['UserTag']			=	array_Values($tag);
			$returnArray['UserInterest']	=	array_values($interest);
			$returnArray['UserEducation']	=	array_values($education);
			$returnArray['UserNote']		=	array_values($note);
			return $returnArray;
		}
		else{
			// no User found with this id
            throw new ApiException("User Details you are requesting is no more", HttpStatusCode::BadRequest);
		}
    }
	
	 /**
     * Validate the model
     * @throws ApiException if the models fails to validate required fields
     */
	public function validate()
    {
		$bean = $this->bean;
		
		if($bean->FacebookId == '' && $bean->LinkedInId == '') {
			$rules = [
				'required' => [
					['FirstName'],['LastName'],['Email'],['Password']
				],
				'email' => 'Email',
			];
		} else {
			$rules = [
	            'required' => [
	                ['FirstName'],['LastName'],['Email']
	            ],
				'email' => 'Email',
	        ];
		}
		
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the user's properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
	}
	/**
     * Validate the creation of an account
     * @throws ApiException if the user being creating the account with already exists of email , facebook and linked ids in the database.
     */
    public function validateCreate() {

        /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean = $this->bean;
		/**
         * Email Id must be unique
         */
		 /**
         * FacebookId must be unique
         */
		if($bean->FacebookId != ''){
	        $existingAccount = R::findOne('users', 'FacebookId = ? and Status <> ? order by DateModified desc', array($bean->FacebookId,StatusType::DeleteStatus));
	        if ($existingAccount) {
	            // an account with that FacebookId already exists in the system - don't create account
	            throw new ApiException("This Facebook ID is already associated with another account.", ErrorCodeType::FbIdAlreadyExists);
			}
		}
		/**
         * LinkedInId must be unique
         */
		if($bean->LinkedInId != ''){
	        $existingAccount = R::findOne('users', 'LinkedInId = ? and Status <> ? order by DateModified desc', array($bean->LinkedInId,StatusType::DeleteStatus));
	        if ($existingAccount) {
	            // an account with that LinkedInId already exists in the system - don't create account
	            throw new ApiException("This LinkedIn ID is already associated with another account.",ErrorCodeType::LinkedInIdAlreadyExists);
			}
		}
		if($bean->Email != '') {
			$existingAccount = R::findOne('users', 'Email = ? and Status <> ? order by DateModified desc', array($bean->Email,StatusType::DeleteStatus));
			if ($existingAccount) {
				if($bean->FacebookId != ''){
					 throw new ApiException("This email address is already associated with another account. Please try logging in with Use emailaddress or LinkedIn or contact us at support@intermingl.com", ErrorCodeType::EmailAlreadyExists);
				}
				else if($bean->LinkedInId != '') {
					 throw new ApiException("This e-mail address is already associated with another account. Please try logging in with Use emailaddress or Facebook or contact us at support@intermingl.com", ErrorCodeType::EmailAlreadyExists);
				}
				else {
					throw new ApiException("This e-mail address is already associated with another account. Please try logging in with Facebook or LinkedIn or contact us at support@intermingl.com", ErrorCodeType::EmailAlreadyExists);
				}
				/*				
				// an account with that email already exists in the system - don't create account
				throw new ApiException("This Email Address is already associated with another account", ErrorCodeType::EmailAlreadyExists);
				*/
			}
		}
		/**
         * UserName must be unique
         *//*
	        $existingAccount = R::findOne('users', 'UserName = ? and (Status <> ? and Status <> ?) order by DateModified desc', array($bean->UserName,StatusType::DeleteStatus,StatusType::InactiveStatus));
			if ($existingAccount) {
	            // an account with that UserName exists in the system - don't create account
	            throw new ApiException("This UserName is already associated with another account", ErrorCodeType::UserNameAlreadyExists);
			}*/
       
    }
	 public function validateReg2() {
		$rules = [
					'required' => [
						['FirstName'],['LastName'],['Email']
					],
					'email' => 'Email',
				];
		$bean = $this->bean;
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the user's properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
		
    }
	/**
     * Validate the creation of an account
     * @throws ApiException if the user being creating the account with already exists of email , facebook and linked ids in the database.
     */
    public function validateUserReg2(){

        /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean = $this->bean;
		/**
         * Email Id must be unique
         */
	        $existingAccount = R::findOne('users', 'Email = ? and (Status <> ? and Status <> ?) and id <> ? order by DateModified desc', array($bean->Email,StatusType::DeleteStatus,StatusType::InactiveStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that email already exists in the system - don't create account
	            throw new ApiException("This Email Address is already associated with another account", ErrorCodeType::EmailAlreadyExists);
			}
		/**
         * UserName must be unique
         */
	       /* $existingAccount = R::findOne('users', 'UserName = ? and (Status <> ? and Status <> ?) order by DateModified desc', array($bean->UserName,StatusType::DeleteStatus,StatusType::InactiveStatus));
			if ($existingAccount) {
	            // an account with that UserName exists in the system - don't create account
	            throw new ApiException("This UserName is already associated with another account", ErrorCodeType::UserNameAlreadyExists);
			}*/
        /**
         * FacebookId must be unique
         */
		if($bean->FacebookId != ''){
	        $existingAccount = R::findOne('users', 'FacebookId = ? and (Status <> ? and Status <> ?) and id <> ? order by DateModified desc', array($bean->FacebookId,StatusType::DeleteStatus,StatusType::InactiveStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that FacebookId already exists in the system - don't create account
	            throw new ApiException("This facebookId is already associated with another account", ErrorCodeType::FbIdAlreadyExists);
			}
		}
		/**
         * LinkedInId must be unique
         */
		if($bean->LinkedInId != ''){
	        $existingAccount = R::findOne('users', 'LinkedInId = ? and (Status <> ? and Status <> ?) and id <> ? order by DateModified desc', array($bean->LinkedInId,StatusType::DeleteStatus,StatusType::InactiveStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that LinkedInId already exists in the system - don't create account
	            throw new ApiException("This LinkedInId is already associated with another account",ErrorCodeType::LinkedInIdAlreadyExists);
			}
		}
    }
	/**
     * @param Post user device token saving process
     */
    public function addTokens($post){
		if(isset($post['DeviceToken']) && trim($post['DeviceToken']) !='' && isset($post['EndPointARN']) && trim($post['EndPointARN']) =='') {
		 	 $valueToken1 = ltrim($post['DeviceToken'],'<');
			 $valueToken = Rtrim( $valueToken1,'>');
			 $tokenExists = R::findOne('devicetoken','Token = ?',[$valueToken]);
			 if($tokenExists){
			 	$post['EndPointARN'] =  $tokenExists['EndPointARN'];
			 }
		} 
		if(isset($post['DeviceToken']) && trim($post['DeviceToken']) !='' && isset($post['EndPointARN']) && trim($post['EndPointARN']) !='') {
			 if($post['Platform'] == 'android')
			 	$platform = 2;
			 else
			 	$platform = 1;
				
			 $valueToken1 = ltrim($post['DeviceToken'],'<');
			 $valueToken = Rtrim( $valueToken1,'>');
			 $token 				= R::dispense('devicetoken');
			 $token->LoginedDate 	= date('Y-m-d H:i:s');
			 $token->fkUsersId 		= $post['UserId'];
			 $token->Token 			= $valueToken;
			 $token->EndPointARN 	= $post['EndPointARN'];
			 $token->Platform 		= $platform;
			 $token->Status 		= 1;
			 $sql = "update devicetoken set Status = 0 where Token = '".$valueToken."'";
			 R::exec($sql);
			 $tokenExists = R::findOne('devicetoken','Token = ? and fkUserId = ?',[$valueToken, $post['UserId']]);
			 if($tokenExists)
			 {
			 	$token->id =  $tokenExists['id'];
			 }
			 R::store($token);
		}
	}
	/** Update Social Media Details **/
	public function UpdateSocialMedia() {
		/**
         * Get the bean
         * @var $bean Model_User
         */
		
        $bean 		= $this->bean;
		$this->validateSocialMedia();
		$this->validateSocialMediaExists();
		$bean->DateModified = date('Y-m-d H:i:s');
		R::store($this);
	}
	/**
     * Validate the model
     * @throws ApiException if the models fails to validate required fields if any field has values
     */
    public function validateSocialMedia() {
		$bean = $this->bean;
		
		if(is_null($bean->FacebookId) && is_null($bean->LinkedInId) && is_null($bean->TwitterId) && is_null($bean->GooglePlusId)) {
            throw new ApiException("Please check the social media properties" ,  ErrorCodeType::SomeFieldsRequired);
        } 
		else if((is_string($bean->FacebookId) and trim($bean->FacebookId) === '') && 
				(is_string($bean->LinkedInId) and trim($bean->LinkedInId) === '') && 
				(is_string($bean->TwitterId) and trim($bean->TwitterId) === '') && 
				(is_string($bean->GooglePlusId) and trim($bean->GooglePlusId) === '')) {
            throw new ApiException("Please check the social media properties" ,  ErrorCodeType::SomeFieldsRequired);
        }
    }
		
	/** Validate the social media already exists **/
	public function validateSocialMediaExists() {
		/**
         * Get the bean
         * @var $bean Model_User
         */
        $bean 		= $this->bean;
		
		/**
         * FacebookId must be unique
         */
		//echo "<pre>";print_r($bean);echo "</pre>";
		// R::debug(true);
		if($bean->FacebookId != ''){
	        $existingAccount = R::findOne('users', 'FacebookId = ? and Status <> ? and id <> ? order by DateModified desc', array($bean->FacebookId,StatusType::DeleteStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that FacebookId already exists in the system - don't create account
				//echo "----->".ErrorCodeType::FbIdAlreadyExists;
	            throw new ApiException("This Facebook ID is already associated with another account", ErrorCodeType::FbIdAlreadyExists);
			}
		}
		else
			unset($bean->FacebookId);
		/**
         * LinkedInId must be unique
         */
		if($bean->LinkedInId != ''){
			$existingAccount = R::findOne('users', 'LinkedInId = ? and Status <> ? and id <> ? order by DateModified desc', array($bean->LinkedInId,StatusType::DeleteStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that LinkedInId already exists in the system - don't create account
	            throw new ApiException("This LinkedIn ID is already associated with another account",ErrorCodeType::LinkedInIdAlreadyExists);
			}
		}
		else
			unset($bean->LinkedInId);
		/**
         * TwitterId must be unique
         */
		if($bean->TwitterId != ''){
			$existingAccount = R::findOne('users', 'TwitterId = ? and Status <> ? and id <> ? order by DateModified desc', array($bean->TwitterId,StatusType::DeleteStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that TwitterId already exists in the system - don't create account
	            throw new ApiException("This Twitter ID is already associated with another account",ErrorCodeType::TwitterIdAlreadyExists);
			}
		}
		else
			unset($bean->TwitterId);
		/**
         * GooglePlusId must be unique
         */
		if($bean->GooglePlusId != ''){
			$existingAccount = R::findOne('users', 'GooglePlusId = ? and Status <> ? and id <> ? order by DateModified desc', array($bean->GooglePlusId,StatusType::DeleteStatus, $bean->id));
	        if ($existingAccount) {
	            // an account with that GooglePlusId already exists in the system - don't create account
	            throw new ApiException("This GooglePlus ID is already associated with another account",ErrorCodeType::GooglePlusAlreadyExists);
			}
		}
		else
			unset($bean->GooglePlusId);
	}
	
	/**
     * Create New Tags for the user
     */
    public function createTags(){ // creating
		 /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean 		= $this->bean;

		$userId		=	$bean->userId;
		$toUserId	=	$bean->toUserId;
		$cardId		=	$bean->CardId;

		// validate the given Tags
        $this->validateCreation('CardId','Tag');

       	// validate the userId
        $this->validateUser($userId);
		$this->validateUser($toUserId);
		
		// Validate card Details
		$this->validateCardInformation($cardId,$toUserId);
		$tag		= 	$bean->Tag;
		
		if(isset($tag)	&&	is_array($tag)	&&	count($tag)>0)	{
			$tagDelete			=	R::find('usertags','fkUsersId = ? and tofkUsersId = ? and Status = ? ',[$userId,$toUserId,StatusType::ActiveStatus]);
			if(!empty($tagDelete))	{
				foreach($tagDelete	as $key=>$value)	{
					$updateTag					=	R::dispense('usertags');
					$updateTag->Status			=	StatusType::DeleteStatus;
					$updateTag->DateModified	=	date('Y-m-d H:i:s');
					$updateTag->id				=	$value['id'];
					R::store($updateTag);
				}
			}
			foreach($tag as $key=>$value)	{
				$tagId			= 	$this->findAndGetTag($value);
				$user			= 	R::dispense('users');
				$user->userId	=	$userId;
				$user->toUserId	=	$toUserId;
				$user->tagId	=	$tagId;
				$user->cardId	=	$cardId;
				$user->checkTagAlreadyExists(1);
				$user->addUserTag();
			}

		}
		//Return the id of new tag
		return $tagId;
    }
	/** Find tag with id and to insert if not exists **/
	public function findAndGetTag($tag) {
		$tag_details = R::findOne('tags', 'Tags = ? and Status = ? ', [trim($tag),StatusType::ActiveStatus]); //and status = ? ,StatusType::ActiveStatus
		if($tag_details)
			return $tag_details->id;
		else {
			$tags	= R::dispense('tags');
			$curr_date		= date('Y-m-d H:i:s');
			$tags->Tags		= $tag;
			$tags->Status	= StatusType::ActiveStatus;
			$tags->DateCreated	= $curr_date;
			$tags->DateModified	= $curr_date;
			$tagId = R::store($tags);
			return $tagId;
		}
	}
	/**
     * Validate the tag already assigned to this user
     * @throws ApiException.
     */
    public function checkTagAlreadyExists($subvalue	=	0) {
		 /**
         * Get the identity of the person requesting the details
         */
		$bean		=	$this->bean;
		$user 		= 	R::findOne('usertags', 'fkTagsId = ? and fkUsersId = ? and tofkUsersId = ? ', [$bean->tagId,$bean->userId,$bean->toUserId]);
		if ($user	&&	$subvalue==0) {
			// the user was not found
				throw new ApiException("Tag already added.", ErrorCodeType::TagAlreadyExists);
		}
		return true;
	}
	
/**
     * Create an user account
	 * Validation for email , fbId,LinkedInId, Username
     */
    public function createInterest(){ // creating
		 /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean 		= 	$this->bean;
		
		$userId		=	$bean->userId;
		$toUserId	=	$bean->toUserId;
		$cardId		=	$bean->CardId;
		
		//echo "<pre>";print_r($bean);echo "</pre>";
		// validate the model
        $this->validateCreation('Interest','CardId');

       	// validate the creation
        $this->validateUser($userId);
		$this->validateUser($toUserId);
		$interest	= 	$bean->Interest;

		//To validate the give card details
		$cardInfo	=	$this->validateCardInformation($cardId,$toUserId);

		// To check weather the given interest already exists if not to do new insert
		// findAndGetId(Value,Field,TableName);
		if(!empty($interest))	{
			$interestId	=	$this->addInterestForUser();
		}
		//Return the id of new tag
		return $interestId;
    }
	
	/** Find tag with id and to insert if not exists **/
	public function findAndGetId($value,$field,$table) {
		//R::debug();
		$interest_details = R::findOne($table, "$field = ? and Status = ? ", [trim($value),StatusType::ActiveStatus]);
		if($interest_details)
			return $interest_details->id;
		else {
			$instance				=	R::dispense($table);
			$curr_date				=	date('Y-m-d H:i:s');
			$instance->Interest		=	$value;
			$instance->Status		=	StatusType::ActiveStatus;
			$instance->DateCreated	=	$curr_date;
			$instance->DateModified	=	$curr_date;
			$interestId 			=	R::store($instance);
			return $interestId;
		}
	}
	
	/**
     * Validate the Interest already assigned to this user
     * @throws ApiException.
     */
    public function checkInterestAlreadyExists($subvalue	=	0) {
		 /**
         * Get the identity of the person requesting the details
         */
		$bean		=	$this->bean;
		//R::debug();
		$user 		= 	R::findOne('userinterests', 'fkUsersId = ? and fkCardsId = ?  and fkInterestsId = ? ', [$bean->userId,$bean->cardId,$bean->interestId]);
		if ($user	&&	$subvalue==0) {
			// the user was not found
				throw new ApiException("Interest already added.", ErrorCodeType::InterestAlreadyExists);
		}
		return true;
	}
	
	/**
     * Validate the model
     * @throws ApiException if the models fails to validate required fields
     */
    public function validateTags() {
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
     * Validate the model
     * @throws ApiException if the models fails to validate required fields
     */
    public function validateCreation($field1,$field2) {
		$rules = [
					'required' => [
						[$field1],[$field2]
					]
				];
		$bean = $this->bean;
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the Card and Tag properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
		
    }
	
	/**
     * Validate the user account
     * @throws ApiException.
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
     * Validate the model
     * @throws ApiException if the models fails to validate required fields
     */
	public function validateConnection($type)
    {
		$bean = $this->bean;
		if($type == StatusType::LikeType) {
			$rules = [
				'required' => [
					['EventId'],['UserId']
				]
			];
		} 
		if($type == StatusType::ConnectionsType)  {
			$rules = [
	            'required' => [
	                ['UserId']
	            ]
	        ];
		}
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the user's properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
	}
	/**
     * Check the Login details
    */
    public static function checkLoginSilent($email){
        /**
         * @var Model_User
         */		
		$result = R::findOne('users', 'Email = ? and Status <> ? and Status <> ?', array($email, StatusType::DeleteStatus,StatusType::InactiveStatus));
        if (!$result) {
            return false;
        }
        else {
			return array('UserId' => $result->id, 'UserStatus' => $result->Status);
		}
    }
	public function  silentLogin($app, $checkLoginCallBackSilent, $userArray) {
		$res = $app->response();
		$res['Content-Type'] = 'application/json';
		// grab the authorization server from the api
		$authServer = interminglApi::$authServer;
		// We are going for this flow in oauth 2.0: Resource Owner Password Credentials Grant
		$grant = new League\OAuth2\Server\Grant\Password($authServer);
		// this is where we check the login details
		$grant->setVerifyCredentialsCallback($checkLoginCallBackSilent); //exit;
		$authServer->addGrantType($grant);
		// TO GET THE CLIENT ID AND CLIENT SECRET FOR LOGIN CREDENTIALS
		$clientDetails		=	R::findOne('oauth_clients');
		if($clientDetails) {
			$inputParams	=	array('ClientId'=>$clientDetails->id,'ClientSecret'=>$clientDetails->secret,'Email'=>$userArray['Email'],'Password'=>'', 'SilentLogin'=>1); //,'FacebookId'=>$userArray['FacebookId'], 'LinkedInId'=>$userArray['LinkedInId']
			
			// get the response from the server
			$response = $authServer->getGrantType('password')->completeFlow($inputParams);
			
			// TO ADD THE EMAIL IN THE RESPONSE FOR STEP 2 EMAIL PRE-POPULATE FEATURES
			if($response) {
				/** BEGIN: TO UPDATE THE LAST LOGIN DETAILS IN THE USER TABLE **/
				/*	$users					=	R::dispense('users');
					$users->id				=	$response['login']['UserId'];
					$users->LastLoginDate	=	date('Y-m-d H:i:s');
					R::store($users); */
				/** END: TO UPDATE THE LAST LOGIN DETAILS IN THE USER TABLE **/			
				//echo json_encode($response);
				return $response;
			}
		}
	}
	
	/**
     * To get the specific user details
     * @throws ApiException.
     */
    public function getUserDetail($userId) {
		 /**
         * Get the identity of the person requesting the details
         */
		$user = R::findOne('users', 'id = ? and status = ?', [$userId,StatusType::ActiveStatus]);
	
		if (!empty($user)) {
			// the user details found
			return $user;
		}
	}
	
	/**
	* To get the event Details for the specific user
	* @throw ApiException if there is no recent Connection
	*/
	public function recentConnections($subvalue = 0)	{	
		$bean				=	$this->bean;
		$today 				=	date('Y-m-d H:i:s');
		$returnArray		=	array();
		//R::debug(true);
		if($subvalue	==	0)	{
			$query			=	"SELECT tofkUsersId from connections as c 
								where c.fkUsersId =".$bean->fkUsersId." and Type = ".$bean->Type." ORDER BY c.DateCreated desc Limit ".$bean->Limit;
		}
		//R::debug(true);
		if($subvalue	==	1)	{
			$query			=	"SELECT tofkUsersId FROM connections as c 
								 WHERE c.fkUsersId =".$bean->fkUsersId." ORDER BY c.DateCreated desc Limit ".$bean->Limit;		
		}
		$recentConnection	=	R::getAll($query);
		if(empty($recentConnection)	&&	$subvalue ==0 )
			throw new ApiException(" You don't have connections yet ",ErrorCodeType::userNotAllowedToAddOrBlockOwn);
		
		/* To Remove the Duplicate Values from the Array if there is a duplicate */
		if(!empty($recentConnection))	{
			foreach($recentConnection as $key=>$value)
				$idArray[$value['tofkUsersId']]	=	$value['tofkUsersId'];
			
			$connectionId	=	implode(',',$idArray);
			//R::debug(true);
			//	$userDetails	=	R::find('users',' id IN ('.R::genSlots($idArray).') ',$idArray);
			//	$userDetails	=	R::find('users',' Status=? and id IN ('.R::genSlots($idArray).') ',[StatusType::ActiveStatus,$connectionId]);
			$userDetails		=	R::find('users',' Status = ? and id IN ('.$connectionId.') ',[StatusType::ActiveStatus]);
			if(empty($userDetails)	&&	$subvalue ==0 )
				throw new ApiException(" You don't have active user connection ",ErrorCodeType::userNotAllowedToAddOrBlockOwn);
			
			if(!empty($userDetails))	{
				foreach($userDetails as $key=>$value) {
					$returnArray[$value->id]['UserId']	=	$value->id;
					$returnArray[$value->id]['FirstName']	=	$value->FirstName;
					$returnArray[$value->id]['LastName']	=	$value->LastName;
					$returnArray[$value->id]['Email']		=	$value->Email;
					$returnArray[$value->id]['Photo']		=	(($value->Photo != '') ? USER_IMAGE_PATH.$value->Photo : '');
				}
			}
		}
		return array_values($returnArray);
	}

	/**
	* To get recent activity details to the database
	* @throw ApiException when there is no recent activity
	*/	
	public function recentActivity($subvalue = 0)	{
		$bean			=	$this->bean;
		$returnArray	=	array();
		$query			=	"SELECT * FROM (
										(

										SELECT jevent.fkUsersId AS UserId, '1' AS EventJoin, '' AS Connection, jevent.fkEventsId AS EventId, jevent.DateModified,e.PassPhrase,u.FirstName,u.LastName,u.Email,u.Photo
										FROM joinevents AS jevent
										left join events as e on (e.id = jevent.fkEventsId)
										left join users as u on (u.id = jevent.fkUsersId)
										WHERE jevent.fkEventsId
										IN (

										SELECT je.fkEventsId
										FROM joinevents AS je
										WHERE je.fkUsersId = '".$bean->fkUsersId."'
										ORDER BY je.id DESC
										)
										ORDER BY jevent.id DESC
										LIMIT 3
										)
										UNION (

										SELECT con.fkUsersId AS UserId, '' AS EventJoin, con.Type AS Connection, con.fkEventsId AS EventId, con.DateModified,e.PassPhrase,u.FirstName,u.LastName,u.Email,u.Photo
										FROM connections AS con
										left join events as e on (e.id = con.fkEventsId)
										left join users as u on (u.id = con.fkUsersId)
										WHERE con.tofkUsersId ='".$bean->fkUsersId."'
										ORDER BY con.id DESC
										LIMIT 3
										)
									) 
									AS recentActivity
									ORDER BY DateModified DESC
									LIMIT 3 ";
		$recentActivity	=	R::getAll($query);
		if(empty($recentActivity)	&&	$subvalue ==0 )
			throw new ApiException(" You don't have any recent Activity ",ErrorCodeType::userNotAllowedToAddOrBlockOwn);

		if(!empty($recentActivity))	{
			$iteration	=	0;
			foreach($recentActivity as $key=>$value)	{
				$returnArray[$iteration]['UserId']		=	$value['UserId'];
				$returnArray[$iteration]['EventId']		=	$value['EventId'];
				if($value['EventJoin'] == 1)
					$returnArray[$iteration]['Type']	=	StatusType::JoinedStatus;
				else if($value['Connection'] == 1)
					$returnArray[$iteration]['Type']	=	StatusType::ConnectionsType;
				else if($value['Connection'] == 2)
					$returnArray[$iteration]['Type']	=	StatusType::LikeType;
				else
					$returnArray[$iteration]['Type']	=	'';
				$returnArray[$iteration]['FirstName']	=	$value['FirstName'];
				$returnArray[$iteration]['LastName']	=	$value['LastName'];
				$returnArray[$iteration]['Email']		=	$value['Email'];
				$returnArray[$iteration]['PassPhrase']	=	$value['PassPhrase'];
				$returnArray[$iteration]['Photo']		=	(($value['Photo'] != '') ? USER_IMAGE_PATH.$value['Photo'] : '');
				$iteration++;
			}
		}
		return array_values($returnArray); 
	}
	
	/**
	* To get CoverImages of the Events
	*/		
	public function getcoverImage()	{
		$bean			=	$this->bean;
		//echo $bean->fkUsersId;
		$returnArray	=	array();
		$query			=	"SELECT e.id AS EventId, e.CoverPhoto AS CoverPhoto, j.DateModified AS DateModified, e.StartDate, e.EndDate
								FROM events AS e
								LEFT JOIN joinevents AS j ON ( j.fkEventsId = e.id )
								WHERE j.fkUsersId ='".$bean->fkUsersId."' 
								GROUP BY e.id
								ORDER BY j.DateModified DESC
								LIMIT 10 ";
		//echo $query;
		$coverImages	=	R::getAll($query);
		if(!empty($coverImages))	{
			$iteration	=	0;
			foreach($coverImages as $key=>$value)	{
				if($value['CoverPhoto']	!=	""	&&	$iteration<5)	{
					$returnArray[$value['EventId']]['EventId']		=	$value['EventId'];
					$returnArray[$value['EventId']]['CoverPhoto']	=	EVENT_COVER_IMAGE_PATH.$value['CoverPhoto'];
					$iteration++;
				}
			}
		}
		return array_Values($returnArray); 
	}
	/**
	* To Add connection details to the database
	* @throw ApiException when the user add connection to themself
	*/
	public function addConnection ()	{
		$dateCreated 	=	date('Y-m-d H:i:s');
		$bean			=	$this->bean;
		/* To validate the connection to check weather the connections are already made */
		$this->validateConnectionType();
		
		$tofkUsersId	=	$bean->tofkUsersId;
		$fkUsersId		=	$bean->fkUsersId;
		$eventId		=	$bean->fkEventsId;
		$eventType		=	$bean->eventType;
		
		/* To check wheather the user try to connect or like themself */
		if($fkUsersId	==	$tofkUsersId)	{
			if($bean->eventType	==	StatusType::LikeType)
				throw new ApiException("You are not allowed to like your own events",ErrorCodeType::userNotAllowedToAddOrBlockOwn);
			if($bean->eventType == StatusType::ConnectionsType)
				throw new ApiException("You cannot connect to yourself",ErrorCodeType::userNotAllowedToAddOrBlockOwn);
		}
		$connections				=	R::dispense('connections');
		$connections->fkUsersId		=	$fkUsersId;
		$connections->tofkUsersId	=	$tofkUsersId;
		$connections->fkEventsId	=	$eventId;
		$connections->Type			=	$eventType;
		$connections->DateCreated	=	$dateCreated;
		$connections->DateModified	=	$dateCreated;
		
		/* To store the connection information to the table */ 
		$insertConnection			=	R::store($connections);
		return $insertConnection;
	}

	/**
	* To validate the connection_aborted
	* @throw ApiException if the user has already connected or liked
	*/
	public function validateConnectionType()	{
		$bean	=	$this->bean;
		if($bean->eventType	==	StatusType::LikeType)
			$connection	=	R::findone('connections','fkUsersId = ? and tofkUsersId = ? and fkEventsId = ? and Type = ?', [$bean->fkUsersId,$bean->tofkUsersId,$bean->fkEventsId,$bean->eventType]);
		if($bean->eventType == StatusType::ConnectionsType)
			$connection	=	R::findone('connections','fkUsersId = ? and tofkUsersId = ? and Type = ?', [$bean->fkUsersId,$bean->tofkUsersId,$bean->eventType]);
		if(!empty($connection))	{
			if($bean->eventType	==	StatusType::LikeType)
				throw new ApiException(" You already liked this User",ErrorCodeType::AlreadyLiked);
			if($bean->eventType == StatusType::ConnectionsType)
				throw new ApiException(" You already Connected to this User",ErrorCodeType::AlreadyAddedOrBlockedContact);
		}
	}
	

}