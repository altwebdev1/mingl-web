<?php

/**
 * Users endpoint
 * /v1/users
 *
 * @author eventurers
 */

/**
 * Load configuration
 */
require_once('../../config.php');

/**
 * Load models
 */
require_once '../../lib/ModelBaseInterface.php';            // base interface class for RedBean models
require_once '../../lib/Model_Users.php';
require_once '../../lib/Model_Events.php';
require_once "../../admin/includes/CommonFunctions.php";
require_once "../../admin/includes/phmagick.php";
/**
 * Library objects
 */
use RedBean_Facade as R;
use Helpers\RedBeanHelper as RedBeanHelper;
use Helpers\PasswordHelper as PasswordHelper;
use Enumerations\HttpStatusCode as HttpStatusCode;
use Exceptions\ApiException as ApiException;
use Enumerations\AccountType as AccountType;
use Enumerations\StatusType as StatusType;
use Enumerations\ErrorCodeType as ErrorCodeType;
/**
 * Initialize application
 */
interminglApi::init(true);
$app = new \Slim\Slim();

 /**
 * Check the callback function
 */
$checkLoginCallBackSilent = function($email) {
    return Model_Users::checkLoginSilent($email);
};

/**
 * New user creation - Registration STEP 1
 * POST /v1/users
 */
$app->post('/', function () use ($app,$checkLoginCallBackSilent) {
    try {
        // Create a http request
        $req = $app->request();
        /**
         * Get a new user account
         * @var Model_User $user
         */
		 R::freeze();
		 
        $user = R::dispense('users');
		if($req->params('FirstName'))
			$user->FirstName = $req->params('FirstName');
		if($req->params('LastName'))
			$user->LastName = $req->params('LastName');
		if($req->params('Email'))
			$user->Email = $req->params('Email');
		if($req->params('Password'))
			$user->Password = $req->params('Password');
		if($req->params('FacebookId'))
			$user->FacebookId = $req->params('FacebookId');
		if($req->params('LinkedInId'))
			$user->LinkedInId = $req->params('LinkedInId');
		/*if($req->params('City'))
			$user->City = $req->params('City');
		if($req->params('State'))
			$user->State = $req->params('State');
		if($req->params('Country'))
			$user->Country = $req->params('Country');*/
		if($req->params('Latitude'))
			$user->Latitude = $req->params('Latitude');
		if($req->params('Longitude'))
			$user->Longitude = $req->params('Longitude');
		
		if($req->params('Platform')){
			$platformText = $req->params('Platform');
			if($platformText == 'ios')
				$platform = 1;
			else if($platformText == 'android')
				$platform = 2;
			else
				$platform = 0;
		}
		else{
			$platform = 0;
		}
		$user->Platform = $platform;
		
		if($req->params('Location'))
			$userLocation 		= $req->params('Location');
		
		if(isset($userLocation)		&&	$userLocation!="")			
			$location				=	json_decode($userLocation);
		if(isset($location->FormattedAddressLines)	&&	$location->FormattedAddressLines!="")	
			$user->Location 		= 	implode(",",$location->FormattedAddressLines);
		if(isset($location->City)	&&	$location->City!="")	
			$user->City 			= 	$location->City;
		if(isset($location->State)	&&	$location->State!="")	
			$user->State 			= 	$location->State;
		if(isset($location->Country)	&&	$location->Country!="")
			$user->Country			=	$location->Country;

		$flag = 0;
		
		if (isset($_FILES['Photo']['tmp_name']) && $_FILES['Photo']['tmp_name'] != '') {
			$flag = checkImage($_FILES['Photo'],1);
		}
		
	    $user->PhotoFlag = $flag;
	    $user->EmailNotification = 1;
	   	//$user->HashtagPrivate = 0;
		
		
         /**
         * Create the account
         */
	    $userId = $user->create();
		
		/**
         * Saving user Photo
         */
		 if($userId) {
			$user->Photo = '';
			if (isset($_FILES['Photo']['tmp_name']) && $_FILES['Photo']['tmp_name'] != '') {
				$imageName 				= $userId . '_' . time() . '.png';
				$imagePath 				= UPLOAD_USER_PATH_REL.$imageName;
				$imageThumbPath 		= UPLOAD_USER_THUMB_PATH_REL.$imageName;
				$imageSmallThumbPath    = UPLOAD_USER_SMALL_THUMB_PATH_REL.$imageName;
				copy($_FILES['Photo']['tmp_name'],$imagePath);
				
				$phMagick = new phMagick($imagePath);
				$phMagick->setDestination($imageThumbPath)->resize(100,100);
				
				$phMagick = new phMagick($imagePath);
				$phMagick->setDestination($imageSmallThumbPath)->resize(70,70);
				
				/*if(SERVER) {
					uploadImageToS3($imagePath,3,$imageName);
					uploadImageToS3($imageThumbPath,1,$imageName);
					uploadImageToS3($imageSmallThumbPath,2,$imageName);
					unlink($imagePath);
					unlink($imageThumbPath);
					unlink($imageSmallThumbPath);
				}*/
				$user->Photo = $imageName;
				unlink($_FILES['Photo']['tmp_name']);
				$user->modify($userId);
			}
		 }
		 /**
         * After successful registration email was sent to registered user
         */
		 //if($req->params('Email')){
		  if($req->params('Email') && (!$req->params('FacebookId')) && (!$req->params('LinkedInId')) ){
			$adminDetails 						=   R::findOne('admins', 'id=?', ['1']);
			$adminMail							=	$adminDetails->EmailAddress;
			$mailContentArray['fileName']		=	'registration.html';
			$mailContentArray['from']			=	$adminMail;
			$mailContentArray['subject']		= 	"Registration";
			$mailContentArray['toemail']	    =	$req->params('Email');
			$mailContentArray['password']	    =	$req->params('Password');
			$mailContentArray['name']			=	ucfirst($req->params('FirstName'));
			sendMail($mailContentArray,2); 
		}
		
		
		// TO LOGIN THE ACTIVATED USER SILENTLY AND TO DISPLAY THE LOGIN DETAILS FOR STEP 2
		$userArray['Email']	= $req->params('Email');
		$userArray['UserId'] = $userId;
		
		if($userArray) {
			$silentLogin = $user->silentLogin($app, $checkLoginCallBackSilent, $userArray);
		}
        /**
         * New user creation was made success
         */
        $response = new interminglApiResponse();
        $response->setStatus(HttpStatusCode::Created);
        $response->meta->dataPropertyName = 'Registration';
		$response->returnedObject = '';
		if (isset($silentLogin) && !empty($silentLogin)) {
			$response->returnedObject = $silentLogin['Registration'];
		}
			
		/**
        * returning upon repsonse of new user creation
		*/
        $response->addNotification('User has been created successfully.');
        echo $response;
    } catch (ApiException $e) {
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        // If occurs any error message for slim framework then goes here
    } catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }
});

/**
 * Registration Step 2 and Edit User profile
 * PUT /v1/users
 */
$app->put('/',interminglApi::checkToken(), function () use ($app) {

    try {
		R::freeze();
        // Create a http request
        $request = $app->request();
    	$body = $request->getBody();
		
    	$input = json_decode($body); 
		$requestedById = $userId = interminglApi::$resourceServer->getOwnerId();
        /**
         * Get a new user account
         * @var Model_User $user
         */
        $user = R::dispense('users');
        $user->id = $requestedById;
		
		$platform = 0;
		if(isset($input->Platform)){
			$platformText = $input->Platform;
			if($platformText == 'ios')
				$platform = 1;
			else
				$platform = 2;
		}
		else{
			$platformText = 'web';
		}
		
		if(isset($input->FirstName)) 	$user->FirstName 	= 	$input->FirstName;
		if(isset($input->LastName)) 	$user->LastName 	= 	$input->LastName;
		if(isset($input->Company)) 		$user->Company 		= 	$input->Company;
		if(isset($input->Title)) 		$user->Title 		= 	$input->Title;
		if(isset($input->Email)) 		$user->Email 		= 	$input->Email;
		if(isset($input->Location)) 	$user->Location 	= 	$input->Location;
		if(isset($input->Interests)) 	$user->Interests 	= 	$input->Interests;
		// TO ADD THE EDIT PART IN THE REGISTRATION STEP 2 WEBSERVICE
		if(isset($input->Summary)) 			$user->Summary 			= 	$input->Summary;
		if(isset($input->Phone))			$user->Phone			=	$input->Phone;
		if(isset($input->FbStatus))			$user->FbStatus			=	$input->FbStatus;
		if(isset($input->LinkedStatus))		$user->LinkedStatus		=	$input->LinkedStatus;
		if(isset($input->TwitterStatus))	$user->TwitterStatus	=	$input->TwitterStatus;
		if(isset($input->GoogleStatus))		$user->GoogleStatus		=	$input->GoogleStatus;
		if(isset($input->Education))		$user->Education		=	$input->Education;
		if(isset($input->Tags))				$user->Tags				=	$input->Tags;
		if(isset($input->CardId))			$user->CardId			=	$input->CardId;
		
		if(isset($input->Photo) && $input->Photo !=''){
			
			$userListResult = R::getAll("select Photo from users where id = ".$requestedById);
			$image_base64 	= $input->Photo;
			$decode_img 	= base64_decode($image_base64);
			$typecheck 		= getImageMimeType($decode_img);
			if($typecheck != ''){
				$img = imagecreatefromstring($decode_img);
				if($img != false)
				{
					$imageName = $userId . '_' . time() . '.png';
					
					$imagePath 				= UPLOAD_USER_PATH_REL.$imageName;
					$imageThumbPath 		= UPLOAD_USER_THUMB_PATH_REL.$imageName;
					$imageSmallThumbPath    = UPLOAD_USER_SMALL_THUMB_PATH_REL.$imageName;
				
					imagepng($img, $imagePath);
					
					$phMagick = new phMagick($imagePath);
					$phMagick->setDestination($imageThumbPath)->resize(100,100);
					
					$phMagick = new phMagick($imagePath);
					$phMagick->setDestination($imageSmallThumbPath)->resize(70,70);
					$userImage	= '';
					if(!empty($userListResult[0]['Photo']))
						$userImage = $userListResult[0]['Photo'];	
					if(!SERVER){
						if($userImage != ''){
							$imagePath 				= UPLOAD_USER_PATH_REL.$userImage;
							$imageThumbPath 		= UPLOAD_USER_THUMB_PATH_REL.$userImage;
							$imageSmallThumbPath    = UPLOAD_USER_SMALL_THUMB_PATH_REL.$userImage;
							
							if(file_exists($imagePath))
								unlink($imagePath);
							if(file_exists($imageThumbPath))
								unlink($imageThumbPath);
							if(file_exists($imageSmallThumbPath))
								unlink($imageSmallThumbPath);
						}
					}
					/*if(SERVER){
						if($userImage != ''){
							deleteImages(1,$userImage);
							deleteImages(2,$userImage);
							deleteImages(3,$userImage);
						}
						uploadImageToS3($imagePath,3,$imageName);
						uploadImageToS3($imageThumbPath,1,$imageName);
						uploadImageToS3($imageSmallThumbPath,2,$imageName);
						unlink($imagePath);
						unlink($imageThumbPath);
						unlink($imageSmallThumbPath);
					}*/
					$user->Photo = $imageName;
				}
		   }
		   else{
				// error for photo
				throw new ApiException("Please check the user's properties (Photo)" ,ErrorCodeType::ProblemInImage);
		   }
		}
		//$user->registrationStep2($userId,1);
		$user->registrationStep2();
		
		/**
         * TODO FOR FIRST RUN
         */
		//$event = R::dispense('events');
		//$event->firstRun($userId);
		
        /**
         * New user creation was made success
         */
        $response = new interminglApiResponse();
        $response->setStatus(HttpStatusCode::Created);
        $response->meta->dataPropertyName = 'UpdateProfile';
        $response->addNotification('User Details has been updated successfully');
        echo $response;

    }
    catch (ApiException $e){
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // If occurs any error message for slim framework then goes here
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }
});



/**
* Recent Connection
* GET /v1/users/connections/
* eg: /v1/users/connections/
*/
$app->get('/connections/',interminglApi::checkToken(),function () use ($app)  {
	try {
			R::freeze();
			// Create a http request
			$limit			=	1;
			$req 			= 	$app->request();
			$response 		= 	new interminglApiResponse();
			$userId			=	interminglApi::$resourceServer->getOwnerId();
			if($req->params('Limit')!="")
				$limit			=	$req->params('Limit');
			$user				= 	R::dispense('users');
			$user->fkUsersId	=	$userId;
			$user->Limit		=	$limit;
			$user->Type			=	StatusType::ConnectionsType;
			
			$connectionDetails	=	$user->recentConnections();

			//echo $userId;
			$response->setStatus(HttpStatusCode::Ok);
			$response->meta->dataPropertyName	= 'RecentConnection';
			$response->returnedObject 			= $connectionDetails;
			$response->addNotification("Your recent Connections details retrived successfully");
			echo $response;
		} catch (ApiException $e) {
			// If occurs any error message then goes here
			interminglApi::showError(
				$e,
				$e->getHttpStatusCode(),
				$e->getErrors()
			);
		} catch (\Slim\Exception\Stop $e) {
			// If occurs any error message for slim framework then goes here
		} catch (Exception $e) {
			// If occurs any error message then goes here
			interminglApi::showError($e);
		}	
});

/**
 * Dashboard/Home
 * POST /v1/users/home
 */
$app->get('/home',interminglApi::checkToken(), function () use ($app) {
    try {
		R::freeze();
		$upcomingCount	=	$recentCount	=	0;
		$lat		=	$long	=	'';
		$finalArray	=	array();
		$limit		=	4;
		
		// Create a http request
        $req 		=	$app->request();
		$userId		=	interminglApi::$resourceServer->getOwnerId();

		// To get the nearby Event details
		if($req->params('Latitude')		!=	"")
			$lat	=	$req->params('Latitude');
		if($req->params('Longitude')	!=	"")
			$long	=	$req->params('Longitude');
		if($lat != '' && $long != '') {
			$event 							= 	R::dispense('events');
			$event->Latitude				=	$lat;
			$event->Longitude				=	$long;
			
			// To validate the latitude and longitude properties
			//$event->validateLatLong();
			
			$nearlyEvent					=	$event->getNearlyEvent();
			$finalArray['NearByEvents']		=	$nearlyEvent;
		}
		else
			$finalArray['NearByEvents']		= '';
		// To get the Event's Cover images
		$user							=	R::dispense('users');
		$user->fkUsersId				=	$userId;
		$coverImage						=	$user->getcoverImage();
		$finalArray['CoverImage']		=	$coverImage;
		
		// To get the upcoming Event Details
		$event								=	R::dispense('events');
		$event->Type						=	StatusType::UpcomingStatus;
		$event->Limit						=	$limit;
		$event->fkUsersId					=	$userId;
		$eventDetails						=	$event->getEventList(1);
		if(!empty($eventDetails))	{
			$upcomingCount					=	$eventDetails[0]['Total'];
			unset($eventDetails[0]);
		}
		$finalArray['UpcomingEvents']		=	array_values($eventDetails);

		// To get the Recent Events
		$event->Type						=	StatusType::RecentStatus;
		$event->Limit						=	2;
		$eventDetails						=	$event->getEventList(1);
		if(!empty($eventDetails))	{
			$recentCount					=	$eventDetails[0]['Total'];
			unset($eventDetails[0]);
		}
		$finalArray['RecentEvents']			=	array_values($eventDetails);
		
		// To get the Recent connection
		$user								= 	R::dispense('users');
		$user->fkUsersId					=	$userId;
		$user->Limit						=	3;
		$user->Type							=	StatusType::ConnectionsType;
		$connectionDetails					=	$user->recentConnections(1);
		$finalArray['RecentConnections']	=	$connectionDetails;
		
		// To get the Recent Actitvity
		$activityDetails					=	$user->recentActivity();
		$finalArray['RecentActivity']		=	$activityDetails;
	/*	if(!empty($eventDetails))
			$finalArray['RecentActivity']	=	array_values($activityDetails['RecentActivity']);
		else
			$finalArray['RecentActivity']	=	array();
	*/
			
		// Create a json response object
        $response 	=	new interminglApiResponse();
		
		/**
         * Get a event table instance
         */
		//$event  = R::dispense('events');
		//$userId = 2; 
		//$event->firstRun($userId);
		
		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 	= 	'Dashboard';
		$response->returnedObject			= 	$finalArray;
		$response->meta->TotalUpcomingEvent	=	$upcomingCount;
		$response->meta->TotalRecentEvent	=	$recentCount;
		$response->addNotification('Details retrived successfully.');
        echo $response;
    } catch (ApiException $e) {
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // Don't do anything when the slim framework is told to stop
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }

});


/**
 * Get user Details
 * GET /v1/users profile page 
 */
$app->get('/:id',interminglApi::checkToken(), function ($userId) use ($app) {
    try {
		R::freeze();
        // Create a http request
        $req 			=	$app->request();
		$requestedById 	=	interminglApi::$resourceServer->getOwnerId();
		
		// If the userId is self then the requested User ID is same as the logged in user(i.e Viewing My Profile)
		if($userId	== 'self')
			$userId	=	$requestedById;
		
		// Instance for the Users Table
        $user 	 		=	R::dispense('users');
		
		// To Retrive the Requested UserDetails
        $userDetails	=	$user->getUserDetails($userId,$requestedById);

        if(!empty($userDetails)) {
			$response	=	new interminglApiResponse();
			$response->setStatus(HttpStatusCode::Created);
			$response->meta->dataPropertyName = 'UserDetails';

			// TO CHANGE THE USER DETAIL ARRAY TO JSON
			$response->returnedObject	= $userDetails;
			$response->addNotification('User details has been retrieved successfully');
			echo $response;
		}
		else {
			$response = new interminglApiResponse();
			$response->setStatus(HttpStatusCode::NotFound);
            $response->meta->errorMessage = 'Error in Processing';
			$response->meta->error = ErrorCodeType::ErrorInProcessing;	
		}
    }
    catch (ApiException $e){
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // If occurs any error message for slim framework then goes here
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }
});


/**
 * Forgot password
 * POST /v1/users/forgotpassword
 */
$app->post('/forgotpassword', function () use ($app) {
    try {
		R::freeze();
        $req = $app->request();

		// Create a json response object
        $response = new interminglApiResponse();
		/**
         * Get a user table instance
         */
        $user = R::dispense('users');
		//$user->UserName = $req->params('UserName');
		$user->Email = $req->params('Email');
		/**
         * Call update password function
         */
		
		$userDetails = $user->updatePassword();
		
		/**
         * Send mail to registered user
         */
		if(isset($userDetails['id']) && $userDetails['id'] !='') {
			$adminDetails 					=   R::findOne('admins', 'id=?', ['1']);
			$adminMail						=	$adminDetails->EmailAddress;
			$mailContentArray['fileName']	=	'userForgotPasswordMail.html';
			$mailContentArray['from']		=	$adminMail;
			$mailContentArray['toemail']	= 	trim($userDetails['Email']);
			$mailContentArray['subject']	= 	"Forgot Password";
			$mailContentArray['password']	=	trim($userDetails['Password']);
			$mailContentArray['email']		=	trim($req->params('Email'));
			$mailContentArray['name']		=	ucfirst($userDetails['Name']);
			$support = $adminDetails->SupportEmail; 
			sendMail($mailContentArray,1); //Send mail - Updated password Details
     		
			$content	=	array("status"	    =>	"Success",
						  	 	  "message"  	=>	"An e-mail has been sent to you with your new password. If this e-mail message is not received shortly, please contact $support");
			$response->returnedObject = $content;
		} else {
			$response->setStatus(HttpStatusCode::NotFound);
            $response->meta->errorMessage = 'Error in Processing';
			$response->meta->error = ErrorCodeType::ErrorInUpdateForgetPassword;	
		}
		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName = 'ForgotPassword';
        echo $response;
    } catch (ApiException $e) {
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // Don't do anything when the slim framework is told to stop
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }

});

/**
 * Add social media updates
 * PUT /v1/users/social
 */
$app->put('/social', interminglApi::checkToken(), function () use ($app) {
	try {
		// Create a http request
		R::freeze();
		
		// Create a http request
        $request 		=	$app->request();
    	$body 			=	$request->getBody();
		
		// Create a json response object
    	$input 			=	json_decode($body); 
		$requestedById 	=	$userId = interminglApi::$resourceServer->getOwnerId();
		
		$users			=	R::dispense('users');
		$users->id		=	$requestedById;
		if(isset($input->FacebookId))	$users->FacebookId 		= $input->FacebookId;
		if(isset($input->LinkedInId))	$users->LinkedInId 		= $input->LinkedInId;
		if(isset($input->TwitterId))	$users->TwitterId 		= $input->TwitterId;
		if(isset($input->GooglePlusId))	$users->GooglePlusId 	= $input->GooglePlusId;
		
		$users->UpdateSocialMedia();

		/** BEGIN: TO SEND THE RESPONSE **/
		$response 	=	new interminglApiResponse();
		$response->setStatus(HttpStatusCode::Ok);
		$response->meta->dataPropertyName = 'SocialMediaUpdate';
		$response->addNotification('Social media details updated successfully');
		echo $response;
		/** END: TO SEND THE RESPONSE **/
	}
    catch (ApiException $e){
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // If occurs any error message for slim framework then goes here
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }
	
});

/**
 * Add tags for self and others
 * POST /v1/users/{:USER ID}/tags
 */
$app->post('/:USERID/tags', interminglApi::checkToken(), function ($toUserId) use ($app) {
	try {
		R::freeze();
		$tag_array		=	array();
		$cardId			=	'';
		$req 			=	$app->request();
		$response 		=	new interminglApiResponse();
		$requestedById  =	interminglApi::$resourceServer->getOwnerId();
		
		if($toUserId == 'self')
			$toUserId	= $requestedById;
		$userId		=	$requestedById;
		$users		=	R::dispense('users');
		if($req->params('Tag'))	
		$tag_array 	= json_decode($req->params('Tag'));
		if($req->params('CardId'))	
		$cardId 			=	$req->params('CardId');
		if(!empty($tag_array))
			$users->Tag			=	$tag_array;
		$users->userId		=	$userId;
		$users->toUserId	=	$toUserId;
		$users->CardId		=	$cardId;
		
	/*	if($_SERVER['REMOTE_ADDR'] == '172.21.4.140')	{
			echo "<pre>";print_r($tag_array);echo "</pre>";
			exit;
		}*/
		$tagid	= $users->createTags();
		
		/** BEGIN: TO SEND THE RESPONSE **/
		$response->setStatus(HttpStatusCode::Ok);
		$response->meta->dataPropertyName = 'TagsInsert';
		$response->addNotification('Tags inserted successfully');
		echo $response;
		/** END: TO SEND THE RESPONSE **/
	}
    catch (ApiException $e){
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // If occurs any error message for slim framework then goes here
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }
	
});

/**
* Add new Connection/ Like
* POST /v1/users/connections/
* eg: /v1/users/connections?UserId=6&EventId=5?Type=1
*/
$app->post('/connections',interminglApi::checkToken(),function () use ($app)  {
	try {
			R::freeze();
			// Create a http request
			$req 			= 	$app->request();
			$response 		= 	new interminglApiResponse();
			$tofkUsersId	=	$req->params('UserId');
			$fkEventsId		=	$req->params('EventId');
			$type			=	$req->params('Type');
			if($fkEventsId	==	""	&&	$type	==	StatusType::ConnectionsType)
				$fkEventsId	=	0;

			$userId			=	interminglApi::$resourceServer->getOwnerId();
			
			$event			=	R::dispense('events');
			$event->Type	=	$type;
			$event->validateType();
			
			$user 			=	R::dispense('users');
			$user->EventId	=	$fkEventsId;
			$user->UserId	=	$tofkUsersId;

			/* To validate the logged in user Details and tofkUserId */
			$user->validateUser($userId);
			$user->validateUser($tofkUsersId);
			
			// To validate the already exist condition for the given connection type
			$user->validateConnection($type);
			
			// Validate the event details only if the connection type is 'Like'
			if($type		==	StatusType::LikeType	||	$fkEventsId!=0)	{
				$event		=	R::dispense('events');
				$event->validateEvent($fkEventsId);
			}

			$user 				=	R::dispense('users');
			$user->tofkUsersId	=	$tofkUsersId;
			$user->fkUsersId	=	$userId;
			$user->fkEventsId	=	$fkEventsId;
			$user->eventType	=	$type;

			/* To add connection to the database when the logged user and to fkUser*/
			$result		=	$user->addConnection();
			
			// To display the response message based on the eventtype
			if($type	==	StatusType::LikeType)
				$eventName	=	'like';
			if($type	==	StatusType::ConnectionsType)
				$eventName	=	'connection';
			/* To display the respose of the webservice call when it executed successfully */
			$response->setStatus(HttpStatusCode::Ok);
			$response->meta->dataPropertyName = 'Add'.ucfirst($eventName);
			$response->addNotification('Your '.$eventName.' is added successfully.');
			echo $response;
			
		} catch (ApiException $e) {
			// If occurs any error message then goes here
			interminglApi::showError(
				$e,
				$e->getHttpStatusCode(),
				$e->getErrors()
			);
		} catch (\Slim\Exception\Stop $e) {
			// If occurs any error message for slim framework then goes here
		} catch (Exception $e) {
			// If occurs any error message then goes here
			interminglApi::showError($e);
		}	
});


/**
 * Add Interest for self and others
 * POST /v1/users/{:USER ID}/interests
 */
$app->post('/:USERID/interests', interminglApi::checkToken(), function ($toUserId) use ($app) {
	try {
		R::freeze();
		$req 			= $app->request();
		$interest_array	=	array();
		$requestedById  = interminglApi::$resourceServer->getOwnerId();
		
		if($toUserId 	==	'self')
			$toUserId	=	$requestedById;
		$userId			=	$requestedById;
		$users			=	R::dispense('users');
		if($req->params('Interest'))	
			$interest_array = json_decode($req->params('Interest'));
		if($req->params('CardId'))	
			$users->CardId 	= $req->params('CardId');
		if(!empty($interest_array))
			$users->Interest	=	$interest_array;
		$users->userId		=	$userId;
		$users->toUserId	=	$toUserId;


		$interestId	=	$users->createInterest();
		
		/** BEGIN: TO SEND THE RESPONSE **/
		$response 	= new interminglApiResponse();
		$response->setStatus(HttpStatusCode::Ok);
		$response->meta->dataPropertyName = 'Add Interest';
		$response->addNotification('Interest inserted successfully');
		echo $response;
		/** END: TO SEND THE RESPONSE **/
	}
   catch (ApiException $e){
        // If occurs any error message then goes here
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    }
    catch (\Slim\Exception\Stop $e){
        // If occurs any error message for slim framework then goes here
    }
    catch (Exception $e) {
        // If occurs any error message then goes here
        interminglApi::showError($e);
    }
	
});
/**
 * Start the Slim Application
 */
$app->run();