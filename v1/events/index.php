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
require_once '../../lib/Model_Events.php';
require_once '../../lib/Model_Users.php';
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
 * New event creation 
 * POST /v1/events
 */
$app->post('/', function () use ($app) {
    try {
		R::freeze();
        // Create a http request
        $req = $app->request();
        /**
         * Get a new event 
         * @var Model_Events $event
         */
        $event = R::dispense('events');
		if($req->params('Title'))
			$event->Title = $req->params('Title');
		if($req->params('Description'))
			$event->Description = $req->params('Description');
		if($req->params('Location'))
			$event->Location = $req->params('Location');
		if($req->params('Latitude'))
			$event->Latitude = $req->params('Latitude');
		if($req->params('Longitude'))
			$event->Longitude = $req->params('Longitude');
		if($req->params('StartDate'))
			$event->StartDate = $req->params('StartDate');
		if($req->params('EndDate'))
			$event->EndDate = $req->params('EndDate');
			
		if($req->params('TwitterHashtag'))
			$event->TwitterHashtag = $req->params('TwitterHashtag');
		if($req->params('ShareFaceBookEvent'))
			$event->ShareFaceBookEvent = $req->params('ShareFaceBookEvent');
		if($req->params('ShareTwitterEvent'))
			$event->ShareTwitterEvent = $req->params('ShareTwitterEvent');
		
		
		
		
		$flag = 0;
		
		if (isset($_FILES['CoverPhoto']['tmp_name']) && $_FILES['CoverPhoto']['tmp_name'] != '') {
			$flag = checkImage($_FILES['CoverPhoto'],1);
		}
		
	   
	  
         /**
         * Create the event
         */
	    $eventId = $event->create();
		
		/**
         * Saving event Cover Photo
         */
		 if($eventId) {
				$event->CoverPhoto = '';
				if (isset($_FILES['CoverPhoto']['tmp_name']) && $_FILES['CoverPhoto']['tmp_name'] != '') {
					$imageName 				= $eventId . '_' . time() . '.png';
					$imagePath 				= UPLOAD_EVENT_COVER_PATH_REL.$imageName;
					$imageThumbPath 		= UPLOAD_EVENT_COVER_THUMB_PATH_REL.$imageName;
					copy($_FILES['CoverPhoto']['tmp_name'],$imagePath);
					
					$phMagick = new phMagick($imagePath);
					$phMagick->setDestination($imageThumbPath)->resize(100,100);
					
					if(SERVER){
						uploadImageToS3($imagePath,3,$imageName);
						uploadImageToS3($imageThumbPath,1,$imageName);
						unlink($imagePath);
						unlink($imageThumbPath);
					}
					$event->CoverPhoto = $imageName;
					unlink($_FILES['CoverPhoto']['tmp_name']);
					$event->modify($eventId);
				}
		 }
		
        /**
         * New event creation was made success
         */
        $response = new interminglApiResponse();
        $response->setStatus(HttpStatusCode::Created);
        $response->meta->dataPropertyName = 'event';
		
		/**
        * returning upon repsonse of new user creation
		*/
        $response->addNotification('Event has been created successfully.');
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
 * Event code verification
 * GET /v1/users/search/:Passphrase
 */
$app->get('/search/:Passphrase', function ($passphrase) use ($app) {
    try {
		R::freeze();
		// Create a http request
        $req = $app->request();
		
		// Create a json response object
        $response = new interminglApiResponse();
		/**
         * Get a event table instance
         */
		
        $event = R::dispense('events');
		$event->Passphrase = $passphrase;
		 
		$passphrase = $event->checkPassphrase();
		
		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName = 'Event';
    
		$response->addNotification('Event has been retrived successfully.');
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
* Get upcoming/Recent Event list
* POST /v1/events/lists/?Type=1&Limit=3
* eg: /v1/events/lists/?Type=1&Limit=3
*/
$app->get('/lists/',interminglApi::checkToken(),function () use ($app)  {
try {	
		R::freeze();
        // Create a http request
        $req 			= 	$app->request();
		$response 		= 	new interminglApiResponse();
		$eventName 		= 	$type	= '';
		// Set the default Limit to 4 if there is no limit passed in the params
		$limit 			=	4;		

		$userId			=	interminglApi::$resourceServer->getOwnerId();
		//echo $userId;
		if($req->params('Limit')	!=	"")	
			$limit		=	$req->params('Limit');
		if($req->params('Type')	!=	"")	
			$type		=	$req->params('Type');
		
		/* Create instance for the table Events */
		$event 				= 	R::dispense('events');
		$event->Type		=	$type;
		$event->Limit		=	$limit;
		$event->fkUsersId	=	$userId;
		$event->validateType();

		/* To get the list of Upcoming/Recent Events for the user */
		$eventDetails	=	$event->getEventList();
		if(!empty($eventDetails))	{
			$totalCount		=	$eventDetails[0]['Total'];
			unset($eventDetails[0]);
			$eventDetails	=	array_values($eventDetails);
		}
		

		if($type		==	StatusType::UpcomingStatus)
			$eventName	=	'upcoming';
		if($type		==	StatusType::RecentStatus)
			$eventName	=	'recent';

		if(empty($eventDetails))
			throw new ApiException(" You have no ".$eventName." event details ",ErrorCodeType::EventNotFound); 

		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'EventList';
		$response->meta->SearchListCount  		=	$totalCount;
		//$response->meta->SearchListTotalCount	=	(isset($eventDetails['TotalCount']) ? $eventDetails['TotalCount'] : 0);
		$response->returnedObject				=	$eventDetails;
		$response->addNotification("Your ".$eventName." event details retrived successfully");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});	

/**
* Get Event Goal list
* GET /v1/events/{EVENTCODE}/goals/
*/
$app->get('/:eventCode/goals/',function ($eventCode) use ($app)  {
try {	
		R::freeze();
        $req 		= 	$app->request();
		$response 	= 	new interminglApiResponse();
		
		$event 		=	R::dispense('events');
		/* To validate the eventcode exist */
		$event->validateEvent($eventCode);
		
		/* To get the goal list of the given Event code */
		$event->eventCode	=	$eventCode;
		$goalList			=	$event->eventGoalList();

		if(empty($goalList))
			throw new ApiException(" Requested event have no active goals ",ErrorCodeType::GoalNotFound);

			$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'GoalList';
		//$response->meta->SearchListCount  	=	count($goalList);
		//$response->meta->SearchListTotalCount	=	(isset($eventDetails['TotalCount']) ? $eventDetails['TotalCount'] : 0);
		$response->returnedObject				=	$goalList;
		$response->addNotification(" Goal List retrived successfully");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});	

/**
* Update Event Goal 
* PUT /v1/events/{EVENTCODE}/goals/
*/
$app->put('/:eventCode/goals/',interminglApi::checkToken(),function ($eventCode) use ($app)  {
try {	
		R::freeze();
        $req 		= 	$app->request();
		$body 		= 	$req->getBody();
		$input 		= 	json_decode($body); 
		$userId 	=   interminglApi::$resourceServer->getOwnerId();
		$response 	= 	new interminglApiResponse();
		
		$event 		=	R::dispense('events');
		if(isset($input->Goals))
			$event->Goals		= $input->Goals;
		else
			$event->Goals		= '';
		
		/* To update the event goal */
		$event->eventCode	= $eventCode;
		$event->userId		= $userId;
		$userEventId 		= $event->modifyEventGoal();
		/* To get the goal list of the given Event code */
		if($userEventId){
			$response->setStatus(HttpStatusCode::Ok);
			$response->meta->dataPropertyName = 'EventGoals';
			$response->addNotification('Event goals has been updated successfully');
			echo $response;
		}
		else{
				/** 
				* Error ocurred while updating event goals
				*/
				throw new ApiException("Some error ocurring while updating event goals", ErrorCodeType::ErrorInProcessing);
		}
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});	


/**
* Get People I like and People Like me list
* GET /v1/events/{EVENTCODE}/users/
*/
$app->get('/:eventCode/users/',interminglApi::checkToken(),function ($eventCode) use ($app)  {
try {	
		R::freeze();
		$type		=	$limit		=	'';
		$totalCount	=	0;
		
        $req 		= 	$app->request();
		$response 	= 	new interminglApiResponse();
		$userId 	=   interminglApi::$resourceServer->getOwnerId();
		$response 	= 	new interminglApiResponse();
		
		if($req->params('Type')	!=	"")	
			$type		=	$req->params('Type');
		if($req->params('Limit')	!=	"")	
			$limit		=	$req->params('Limit');

		$event 				=	R::dispense('events');
		$event->eventCode	=	$eventCode;
		$event->userId		=	$userId;
		$event->Type		=	$type;
		$event->Limit		=	$limit;

		$event->validateType();
		/* To validate the eventcode exist */
		$event->validateEvent($eventCode);
		
		/* To get the People list from the given Event */
		$peopleList			=	$event->eventPeopleList();
		if(isset($peopleList[0]['Total'])	&&	$peopleList[0]['Total']!= "")	{
			$totalCount		=	$peopleList[0]['Total'];
			unset($peopleList[0]);
		}
		
		if(empty($peopleList))	{
			if($type	==	1)
				throw new ApiException(" You haven't like any user from this Event so far ",ErrorCodeType::UserNotFound);
			if($type	==	2)
				throw new ApiException(" You haven't received any liked you so far ",ErrorCodeType::UserNotFound);
		}
			$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'PeopleList';
		$response->meta->PeopleCount  			=	$totalCount;
		$response->returnedObject				=	array_values($peopleList);
		$response->addNotification(" People List retrived successfully");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});	

/**
* Get Event Screeb Details
* GET /v1/events/{EVENTCODE}
*/
$app->get('/:eventCode',interminglApi::checkToken(),function ($eventCode) use ($app)  {
try {	
		R::freeze();
		$type			=	$limit	=	'';
		$likeMeCount	=	$iLikeCount	=	0;
        $req 			= 	$app->request();
		$response 		= 	new interminglApiResponse();
		$userId 		=   interminglApi::$resourceServer->getOwnerId();
		$response 		= 	new interminglApiResponse();
		
		$event 			=	R::dispense('events');
		/* To validate the eventcode exist */
		$event->validateEvent($eventCode);

		/* To get the People list from given Event */
		$event->eventCode			=	$eventCode;
		$event->userId				=	$userId;
		$event->Type				=	1;
		$event->Limit				=	$limit;
		$peopleILike				=	$event->eventPeopleList(1);
		if(isset($peopleILike[0]['Total'])	&&	$peopleILike[0]['Total']!= "")	{
			$iLikeCount		=	$peopleILike[0]['Total'];
			unset($peopleILike[0]);
		}
		$finalArray['PeopleILike']	=	array_values($peopleILike);
		/*if(!empty($peopleILike))
			$finalArray['PeopleILike']	=	array_values($peopleILike);
		else
			$finalArray['PeopleILike']	=	array();
		*/
		
		/* To get the People list from given Event */
		$event->eventCode			=	$eventCode;
		$event->userId				=	$userId;
		$event->Type				=	2;
		$event->Limit				=	$limit;
		$peopleLikeMe				=	$event->eventPeopleList(1);
		if(isset($peopleLikeMe[0]['Total'])	&&	$peopleLikeMe[0]['Total']!= "")	{
			$likeMeCount		=	$peopleLikeMe[0]['Total'];
			unset($peopleLikeMe[0]);
		}
		$finalArray['PeopleLikeMe']	=	array_values($peopleLikeMe);
		/*if(!empty($peopleLikeMe))
			$finalArray['PeopleLikeMe']	=	array_values($peopleLikeMe);
		else
			$finalArray['PeopleLikeMe']	=	array();
		*/
		
		if(empty($finalArray))	{
			if($type	==	1)
				throw new ApiException(" You haven't like any user from this Event so far ",ErrorCodeType::UserNotFound);
			if($type	==	2)
				throw new ApiException(" You haven't received any like so far ",ErrorCodeType::UserNotFound);
		}
			$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'EventDetails';
		$response->meta->PeopleILike  			=	$iLikeCount;
		$response->meta->PeopleLikeMe  			=	$likeMeCount;
		$response->returnedObject				=	$finalArray;
		$response->addNotification(" Event Details retrived successfully");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});


/**
* Get Specific Event Details to show in the join event screen
* GET /v1/events/{EVENTCODE}/join/
*/
$app->get('/:eventCode/join/',function ($eventCode) use ($app)  {
try {	
		R::freeze();
		$type		=	$limit		=	'';
        $req 		= 	$app->request();
		$response 	= 	new interminglApiResponse();
		$userId 	=   interminglApi::$resourceServer->getOwnerId();
		$response 	= 	new interminglApiResponse();
		
		$event 				=	R::dispense('events');
		$event->eventCode	=	$eventCode;

		/* To validate the eventcode exist */
	//	$event->validateEvent($eventCode);
		$eventInfo		=	$event->validateEventCode();
		$event->eventId	=	$eventInfo->id;
		
		
		/* To get the People list from the given Event */
		$eventInformation	=	$event->eventInformation();

		if(empty($eventInformation))	{
			throw new ApiException("No Details found for the requested Event" , ErrorCodeType::EventNotFound);
		}
		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'EventDetails';
		$response->returnedObject				=	$eventInformation;
		$response->addNotification(" Event Information Retrived Successfully ");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});


/**
* Get nearby event information
* GET /v1/events/{EVENTCODE}/maplists/
*/
$app->get('/:eventCode/maplists/',interminglApi::checkToken(),function ($eventCode) use ($app)  {
try {	
		R::freeze();
        $req 		= 	$app->request();
		$response 	= 	new interminglApiResponse();
		$userId 	=   interminglApi::$resourceServer->getOwnerId();
		$event 				=	R::dispense('events');
		$event->eventCode	=	$eventCode;
		
		if($req->params('Latitude')		!=	"")
			$event->Latitude	=	$req->params('Latitude');
		if($req->params('Longitude')	!=	"")
			$event->Longitude	=	$req->params('Longitude');
			
		// To validate the latitude and longitude properties
		$event->validateLatLong();

		/* To validate the eventcode exist */
		$eventInfo			=	$event->validateEventCode();
		$event->eventId		=	$eventInfo->id;

		$eventInfo			=	$event->getNearlyEvent(1);
		if(empty($eventInfo))	{
			throw new ApiException("No Details found for the requested Event" , ErrorCodeType::EventNotFound);
		}
		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'NearbyEvents';
		$response->returnedObject				=	$eventInfo;
		$response->addNotification("Event Information Retrived Successfully");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});	


/**
* Get Events based on the search key
* GET /v1/events/{EVENTCODE}/mapsearch/
*/
$app->get('/:eventCode/mapsearch/',interminglApi::checkToken(),function ($eventCode) use ($app)  {
try {	
		R::freeze();
        $req 		= 	$app->request();
		$response 	= 	new interminglApiResponse();
		$userId 	=   interminglApi::$resourceServer->getOwnerId();
		$event 				=	R::dispense('events');
		$event->eventCode	=	$eventCode;
		
		if($req->params('SearchKey')	!=	"")
			$event->SearchKey	=	$req->params('SearchKey');
		if($req->params('Latitude')		!=	"")
			$event->Latitude	=	$req->params('Latitude');
		if($req->params('Longitude')	!=	"")
			$event->Longitude	=	$req->params('Longitude');
			
		// To validate the latitude and longitude properties
		$event->validateLatLong();

		/* To validate the eventcode exist */
		$eventInfo			=	$event->validateEventCode();
		$event->eventId		=	$eventInfo->id;

		$eventInfo			=	$event->getNearlyEvent(2);
		if(empty($eventInfo))	{
			throw new ApiException("No Details found for the requested Event" , ErrorCodeType::EventNotFound);
		}
		$response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName 		=	'EventDetails';
		$response->returnedObject				=	$eventInfo;
		$response->addNotification(" Event Information Retrived Successfully");
		echo $response;
		
	} catch (ApiException $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError(
            $e,
            $e->getHttpStatusCode(),
            $e->getErrors()
        );
    } catch (\Slim\Exception\Stop $e) {
        /* If occurs any error message for slim framework then goes here */
    } catch (Exception $e) {
        /* If occurs any error message then goes here */
        interminglApi::showError($e);
    }	
});			
/**
 * Start the Slim Application
 */

$app->run();