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

class Model_Events extends RedBean_SimpleModel implements ModelBaseInterface {

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
    public function create(){ // creating
		 /**
         * Get the bean
         * @var $bean Model_User
         */
        $bean = $this->bean;
		$bean->Passphrase = $this->validatePassphrase(8);
		
		// validate the model
        $this->validate();

        // validate the creation
        $this->validateCreate();
		 
		// save the bean to the database
        $eventId = R::store($this);
		
		//Return the id of new user
		return $eventId;
    }
	public function modify() {
		/**
         * Get the bean
         * @var $bean Model_User
         */
		$bean = $this->bean;
		unset($bean->PhotoFlag);
		$bean->DateModified 		= date('Y-m-d H:i:s');
		
        // modify the bean to the database
        R::store($this);
    }
	public function checkPassphrase() {
		//R::debug(true);
		$rules = [
					'required' => [
						['Passphrase']
					]
				];
		$bean = $this->bean;
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the event's properties" ,  ErrorCodeType::EventNotFound, $errors);
        }
		
		$eventDetails = R::findOne('events', "Passphrase = '".$bean->Passphrase."'");
		if (!$eventDetails)
			throw new ApiException("This is not a valid code.",  ErrorCodeType::EventNotFound);
		else if (isset($eventDetails->Status) && $eventDetails->Status == StatusType::InactiveStatus)
			throw new ApiException("This event has been inactive.",  ErrorCodeType::EventNotFound);	
		else if (isset($eventDetails->Status) && $eventDetails->Status == StatusType::DeleteStatus)
			throw new ApiException("This event has been removed.",  ErrorCodeType::EventNotFound);	
		else
			return $eventDetails;
	}
	
	public function firstRun($userId) {
		$currentDate = date('Y-m-d H:i:s');

		/** Get gobal user events list. **/
		$events	=	R::find('events','fkUsersId = ? and EndDate >= ? and Status = ?',[GLOBALUSERID,$currentDate,StatusType::ActiveStatus]);
		//$events = R::find('events', 'fkUserId = ?', array(GLOBALUSERID));
		if (!empty($events)) {
			foreach ($events as $event) {
				/** Join gobal events **/
				$oEvent = R::dispense('joinevents');
				$oEvent->fkUsersId 		=	$userId;
				$oEvent->fkEventsId 	=	$event->id;
				$oEvent->DateModified	=	$oEvent->DateCreated = $currentDate;
				R::store($oEvent);
				/** add Goal to registered users events **/
				/*	Commented on 12/03/2014 
					$eventgoals = R::find('eventgoals', 'fkEventId = ?', array($event->id));
					foreach ($eventgoals as $goal) {
						$usereventgoals					=	R::dispense('usereventgoals');
						$usereventgoals->fkUserId		= 	$userId;
						$usereventgoals->fkEventId		= 	$goal->fkEventId;
						//$usereventgoals->fkGoalId		= 	$goal->id;   //Commented on 12-03-2014
						$usereventgoals->fkGoalId		= 	$goal->fkGoalId;   
						$usereventgoals->DateModified	=	$usereventgoals->DateCreated = $currentDate;
						R::store($usereventgoals);
					}
				*/
				/** like global user via global events **/
				$likes = R::dispense('connections');
				$likes->fkUsersId = $userId;
				$likes->tofkUsersId = GLOBALUSERID;
				$likes->fkEventsId = $event->id;
				$likes->Type = StatusType::LikeType;
				$likes->DateModified = $likes->DateCreated = $currentDate;
				R::store($likes);
			}
		}
		/** connect to gobal user via gobal events **/
		$connections 				=	R::dispense('connections');
		$connections->fkUsersId		=	$userId;
		$connections->tofkUsersId	=	GLOBALUSERID;
		$connections->fkEventsId 	=	$event->id;
		$connections->Type 			=	StatusType::ConnectionsType;
		$connections->DateModified 	=	$connections->DateCreated	=	$currentDate;
		R::store($connections);
		
	}
	 /**
     * Validate the model
     * @throws ApiException if the models fails to validate res is not a quired fields
     */
    public function validate() {
		$rules = [
					'required' => [
						['Title']
					]
				];
		$bean = $this->bean;
        $v = new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the event's properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
		
    }
	 /**
     * ValidateType value the model
     * @throws ApiException if the models fails to validate res is not a quired fields
     */
	public function validateType()	{
		$rules = [
					'required' => [
						['Type']
					],
					'in' => [
						['Type',['1','2']]
					]
				];
		$bean 	=	$this->bean;
        $v 		=	new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please check the Type properties" ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
    }	
	/**
     * Validate the creation of an account
     * @throws ApiException if the user being creating the account with already exists of email , facebook and linked ids in the database.
     */
    public function validateCreate() {
	
	}
	public function validatePassphrase($length) {
		$passphrase = getPassphrase($length);
		$existingPassphrase = R::findOne('events', 'Passphrase = ?', array($passphrase));
		if ($existingPassphrase)
			$this->validatePassphrase($length);
		return $passphrase;		
	}


	/**
	* Validate the event exist condition
	* @throw ApiException if the event doesn't exist or not in the active status
	*/
	public function validateEvent($eventId)	{
		/**
		* To Get the event details 
		*/
		$event	=	R::findOne('events','id	= ?	', [$eventId]);
		if(!$event)	
			throw new ApiException("Invalid Event Details", ErrorCodeType::EventNotFound);
		else if(isset($event->Status)	&&	$event->Status	==	StatusType::InactiveStatus)	
			throw new ApiException("This event has been inactive.",  ErrorCodeType::EventNotFound);
		else if (isset($event->Status) && $event->Status == StatusType::DeleteStatus)	
			throw new ApiException("This event has been removed.",  ErrorCodeType::EventNotFound);
		else
			return $event;
	}
	
	/**
	* Validate the event exist condition with the help of its passphrase
	* @throw ApiException if the event doesn't exist or not in the active status
	*/
	public function validateEventCode()	{
		/**
		* To Get the event details 
		*/
		$bean	=	$this->bean;
		$event	=	R::findOne('events',' Passphrase = ?	', [$bean->eventCode]);
		if(!$event)	
			throw new ApiException("Invalid Event Details", ErrorCodeType::EventNotFound);
		else if(isset($event->Status)	&&	$event->Status	==	StatusType::InactiveStatus)	
			throw new ApiException("This event has been inactive.",  ErrorCodeType::EventNotFound);
		else if (isset($event->Status) && $event->Status == StatusType::DeleteStatus)	
			throw new ApiException("This event has been removed.",  ErrorCodeType::EventNotFound);
		else
			return $event;
	}

	/**
	* To get the event Details for the specific user
	* @throw ApiException if there is no upcoming or recent events for the user
	*/
	public function getEventList($subvalue = 0)	{
		$bean		=	$this->bean;
		$eventName	=	$totalRec	= '';
		$returnArray=	array();
		$today 		=	date('Y-m-d H:i:s');
		
		//R::debug(true);
		/* To get the upcoming Event Details */
		if($bean->Type		==	StatusType::UpcomingStatus)	{
			$eventName		=	'upcoming';
			$query			=	"SELECT SQL_CALC_FOUND_ROWS e.*,ie.Type as InviteType from events  as e left join inviteevents as ie on (ie.tofkUsersId = ".$bean->fkUsersId.") where (e.fkUsersId=".$bean->fkUsersId." OR e.fkUsersId=".GLOBALUSERID.") and Status = ".StatusType::ActiveStatus." and StartDate <= '".$today."' and EndDate >='".$today."' Limit ".$bean->Limit;
		}
		
		/* To get the Recent Event Details */
		if($bean->Type		==	StatusType::RecentStatus)	{
			$eventName		=	'recent';
			$query			=	"SELECT SQL_CALC_FOUND_ROWS e.* from events as e where  (e.fkUsersId=".$bean->fkUsersId." OR e.fkUsersId=".GLOBALUSERID.") and Status = ".StatusType::ActiveStatus." and EndDate <='".$today."' Limit ".$bean->Limit;
		}

		//if($_SERVER['REMOTE_ADDR']=='172.21.4.140')
		//	echo $query;
		$eventDetails	=	R::getAll($query);
		if($eventDetails)
			$totalRec	=  R::getAll('SELECT FOUND_ROWS() as count');
		if($totalRec)
			$returnArray['TotalCount']['Total']			=	$totalRec[0]['count'];

		if(empty($eventDetails)	&&	$subvalue==0)	{
			throw new ApiException(" You have no ".$eventName." event details ",ErrorCodeType::EventNotFound);
		}
		if(!empty($eventDetails))	{
			foreach($eventDetails as $key=>$value)	{
				$returnArray[$key]['UserId']				=	$value['fkUsersId'];
				$returnArray[$key]['EventId']				=	$value['id'];
				$returnArray[$key]['Passphrase']			=	$value['Passphrase'];
				if($value['CoverPhoto']	!=	"")
					$value['CoverPhoto']	=	EVENT_COVER_IMAGE_PATH.$value['CoverPhoto'];
				$returnArray[$key]['CoverPhoto']			=	$value['CoverPhoto'];
				$returnArray[$key]['Title']					=	$value['Title'];
				$returnArray[$key]['Description']			=	$value['Description'];
				$returnArray[$key]['Location']				=	$value['Location'];
				$returnArray[$key]['Latitude']				=	$value['Latitude'];
				$returnArray[$key]['Longitude']				=	$value['Longitude'];
				if($subvalue	==	0)	{
					$returnArray[$key]['TwitterHashtag']	=	$value['TwitterHashtag'];
					$returnArray[$key]['InstagramId']		=	$value['InstagramId'];
					$returnArray[$key]['ShareFaceBook']		=	$value['ShareFaceBook'];			
					$returnArray[$key]['ShareTwitter']		=	$value['ShareTwitter'];	
					$returnArray[$key]['SMS']				=	$value['SMS'];
					$returnArray[$key]['FirstComment']		=	$value['FirstComment'];
					$returnArray[$key]['CommentPhotoType']	=	$value['CommentPhotoType'];
				}
				if($value['fkUsersId']	==	GLOBALUSERID)		
					$returnArray[$key]['GlobalEvent']		=	1;
				else
					$returnArray[$key]['GlobalEvent']		=	0;
				
				if($bean->Type		==	StatusType::UpcomingStatus)		{
					if(isset($value['InviteType']) 	&&	$value['InviteType']!="")	
						$returnArray[$key]['Invite']		=	1;
					else
						$returnArray[$key]['Invite']		=	0;
				}
			}
		}
		//echo "<pre>";print_r($returnArray);echo "</pre>";
		//return array('EventDetails'=>array_values($returnArray));
		return array_values($returnArray);
	}
	
	/**
	* To get the list of goals for the given event
	* throw ApiException if there is no goals for the given event
	*/
	public function eventGoalList()	{
		$bean		=	$this->bean;
		$query		=	" SELECT Goal from goals as g left join eventgoals as eg on (g.id = eg.fkGoalsId) where eg.fkEventsId = ".$bean->eventCode." and eg.Status = ".StatusType::ActiveStatus;
		$goalList	=	R::getAll($query);
		
		if(!empty($goalList))	{
			foreach($goalList	as $key=>$value)
				$goal[]	=	$value['Goal'];
		}
		else
			throw new ApiException(" Requested event have no active goals ",ErrorCodeType::GoalNotFound); 

		return array_values($goal);
	}
	
	/**
	* Validate the event user condition
	* @throw ApiException if the event doesn't exist or not in the active status
	*/
	public function validateEventGoal($eventId,$userId)	{
		/**
		* To Get the event details 
		*/
		$event	=	R::findOne('events','id	= ?	', [$eventId]);
		if(!$event)	
			throw new ApiException("Invalid Event Details", ErrorCodeType::EventNotFound);
		else if(isset($event->Status)	&&	$event->Status	==	StatusType::InactiveStatus)	
			throw new ApiException("This event has been inactive.",  ErrorCodeType::EventNotFound);
		else if (isset($event->Status) && $event->Status == StatusType::DeleteStatus)	
			throw new ApiException("This event has been removed.",  ErrorCodeType::EventNotFound);
		else if ($event->fkUsersId != $userId)
			throw new ApiException("You have no access to edit this event goal", ErrorCodeType::NotAccessToDoProcess);
		else
			return $event;
	}
	
	/**
	* to Modify the event goals for the given event
	* Throw ApiException if there is no goals for the event
	*/
	public function modifyEventGoal() {
		/**
         * Get the bean
         * @var $bean Model_User
         */
		$bean = $this->bean;
		$this->validateEvent($bean->eventCode);
		$i = 0;
		if($bean->Goals)
		{
			$goalsArray = explode(',',$bean->Goals);
			
			if($goalsArray){
				foreach($goalsArray as $value){
					//$goalExists = R::findOne('goals','Goal = ? and Status = ? ',[$value,StatusType::ActiveStatus]);
					$goalExists = R::findOne('goals','id = ? and Status = ? ',[$value,StatusType::ActiveStatus]);
					if($goalExists){
						$goalEventExists = R::findOne('usereventgoals','fkEventsId = ? and fkGoalsId = ? and fkUsersId = ? ',[$bean->eventCode,$goalExists['id'],$bean->userId]);
						$userGoalsEvents[] = R::dispense('usereventgoals');
						$userGoalsEvents[$i]['fkUsersId']	= $bean->userId;
						$userGoalsEvents[$i]['fkEventsId']	= $bean->eventCode;
						$userGoalsEvents[$i]['fkGoalsId']	= $goalExists['id'];
						$userGoalsEvents[$i]['Status']		= StatusType::ActiveStatus;
						$userGoalsEvents[$i]['DateCreated']	= date('Y-m-d H:i:s');
						$userGoalsEvents[$i]['DateModified']= date('Y-m-d H:i:s');
						if($goalEventExists){
							$userGoalsEvents[$i]['id']			= $goalEventExists['id'];
						}
						$i++;
					}
					else{
						throw new ApiException("Please check the Goals ids" , ErrorCodeType::GoalNotFound);
					}
				}
			}
		}
		else{
			throw new ApiException("Please check the Goals properties" , ErrorCodeType::SomeFieldsRequired);
		}
		// save the bean to the database
		if($i > 0 ){
			$userGoalEventId = R::storeAll($userGoalsEvents);
			return $userGoalEventId;
		}
		else{
			throw new ApiException("Please check the Goals id" , ErrorCodeType::GoalNotFound);
		}
        // modify the bean to the database
        R::store($this);
    }
	
	/*
	* To get the list of peoples whom i like and whom like me
	* @ throw ApiException when there is no user found
	*/
	function eventPeopleList($subvalue	=	0)
	{
		$peopleArray	=	array();
		$bean			=	$this->bean;
		if(isset($bean->Limit)	&&	$bean->Limit	==	'')
			$bean->Limit	=	6;

		//R::debug(true);
		if($bean->Type		==	1)	{
			$peopleList		=	" select SQL_CALC_FOUND_ROWS u.id,u.FirstName,u.LastName,u.Email from users as u left join connections as c on (u.id = c.tofkUsersId) where fkEventsId = '".
								$bean->eventCode."' and Type = '".StatusType::LikeType."' and fkUsersId ='".$bean->userId."' limit ".$bean->Limit;
		}
		else if($bean->Type	==	2)	{
			$peopleList		=	" select SQL_CALC_FOUND_ROWS u.id,u.FirstName,u.LastName,u.Email from users as u left join connections as c on (u.id = c.fkUsersId) where fkEventsId = '".
								$bean->eventCode."' and Type = '".StatusType::LikeType."' and tofkUsersId ='".$bean->userId."' limit ".$bean->Limit;
		}
		else
			$peopleList		=	'';
	
		//if($_SERVER['REMOTE_ADDR']=='172.21.4.140')
		//	echo $peopleList;
		if($peopleList!='')
			$peopleDetails	=	R::getAll($peopleList);
		if(isset($peopleDetails)	&&	!empty($peopleDetails))
			$totalRec		=  R::getAll('SELECT FOUND_ROWS() as count');
		if(isset($totalRec)	&&	!empty($totalRec))
			$peopleArray['TotalCount']['Total']		=	$totalRec[0]['count'];
		
		if(isset($peopleDetails)	&&	!empty($peopleDetails))	{
			foreach($peopleDetails	as $key=>$value)	{
				$peopleArray[$key]['id']			=	$value['id'];
				$peopleArray[$key]['FirstName']		=	$value['FirstName'];
				$peopleArray[$key]['LastName']		=	$value['LastName'];
				$peopleArray[$key]['Email']			=	$value['Email'];
			}
			return array_values($peopleArray);
		}
		else	{
			if($subvalue!=0)
				return $peopleArray;
			if($subvalue	==	0	&&	$bean->Type	==	1)
				throw new ApiException("You haven't like any user from this Event so far " , ErrorCodeType::UserNotFound);
			if($subvalue	==	0	&&	$bean->Type	==	2)
				throw new ApiException(" You haven't received any like so far " , ErrorCodeType::UserNotFound);
		}
	}
	
	/*
	* To get the specific event information
	* @ throw ApiException when there is no user found
	*/	
	public function eventInformation()	{
		$bean			=	$this->bean;
		$returnArray	=	array();
		// To fetch the count of the joined users and the comments  counts
		$query			=	"SELECT DISTINCT e.fkUsersId, e.Passphrase, e.CoverPhoto, e.Title, e.Description, e.Location, e.City, e.State, 
								e.Country, e.Latitude, e.Longitude, count( j.fkUsersId ) AS joinedUser, j.fkEventsId,
									(
										SELECT count( c.id )
										FROM comments AS c
										WHERE c.fkEventsId = '".$bean->eventId."' and c.Status = '".StatusType::ActiveStatus."'
									) AS totalComment
								FROM EVENTS AS e
								LEFT JOIN joinevents AS j ON ( e.id = j.fkEventsId )
								WHERE e.id = ".$bean->eventId;
		$eventDetail	=	R::getAll($query);
		
		// To fetch the featured People Details
		$query			=	"SELECT u.id,u.FirstName,u.LastName,u.Company,u.Title,u.Photo
								FROM `users` AS u
								LEFT JOIN joinevents AS j ON ( j.fkUsersId = u.id )
								WHERE j.fkEventsId = ".$bean->eventId."
								ORDER BY j.DateModified DESC Limit  2";
		$featurePeople	=	R::getAll($query);
		if(!empty($featurePeople))
		{
			foreach($featurePeople	as $key=>$value)	{
				$peopleDetail[$value['id']]['UserId']	=	$value['id'];
				$peopleDetail[$value['id']]['FirstName']=	$value['FirstName'];
				$peopleDetail[$value['id']]['LastName']	=	$value['LastName'];
				$peopleDetail[$value['id']]['Company']	=	$value['Company'];
				$peopleDetail[$value['id']]['Title']	=	$value['Title'];
				if($value['Photo']!="")
					$value['Photo']	=	USER_IMAGE_PATH.$value['Photo'];
				$peopleDetail[$value['id']]['Photo']	=	$value['Photo'];
			}
		}
		if(!empty($eventDetail))	{
			foreach($eventDetail as $key=>$value)	{
				$returnArray['EventId']					=	$value['fkEventsId'];
				$returnArray['PassPhrase']				=	$value['Passphrase'];
				$returnArray['Title']					=	$value['Title'];
				if($value['CoverPhoto']					!=	"")
					$value['CoverPhoto']				=	EVENT_COVER_IMAGE_PATH.$value['CoverPhoto'];
				$returnArray['CoverPhoto']				=	$value['CoverPhoto'];
				$returnArray['Description']				=	$value['Description'];
				$returnArray['Location']				=	$value['Location'];
				$returnArray['City']					=	$value['City'];
				$returnArray['State']					=	$value['State'];
				$returnArray['Country']					=	$value['Country'];
				$returnArray['Latitude']				=	$value['Latitude'];
				$returnArray['Longitude']				=	$value['Longitude'];
				$returnArray['PeopleCount']				=	$value['joinedUser'];
				$returnArray['Commentscount']			=	$value['totalComment'];
				$returnArray['FeaturedPeople']			=	array_values($peopleDetail);
			}
		}
		else	{
			throw new ApiException("No Details found for the requested Event" , ErrorCodeType::EventNotFound);
		}
		return array('EventDetails'=>$returnArray);
	
	}

	 /**
     * ValidateType value the model
     * @throws ApiException if the models fails to validate res is not a quired fields
     */
	public function validateLatLong()	{
		$rules = [
					'required' => [
						['Latitude'],['Longitude']
					]
				];
		$bean 	=	$this->bean;
        $v 		=	new Validator($this->bean);
        $v->rules($rules);
        if (!$v->validate()) {
            $errors = $v->errors();
            throw new ApiException("Please Check the Latitude and Longitude " ,  ErrorCodeType::SomeFieldsRequired, $errors);
        }
    }	
	
	/**
	* To fetch the nearby event information based on the given latitude and longitude
	* Throw ApiException if there is no event present in the given latitude and longitude
	*/
	public function getNearlyEvent($subvalue = 0)	{
		$bean			=	$this->bean;
		$today 			=	date('Y-m-d H:i:s');
		$returnArray	=	array();
		$returnString	=	'';
		//To fetch the distance value from the setting table
		$distanceInfo	=	R::findone('setting','id=?',[1]);
		$distance		=	$distanceInfo->Distance;
		
		//To fetch nearby upcoming events 
		$where			=	" and acos(sin('".$bean->Latitude."') * sin(Latitude) + cos('".$bean->Latitude."') * cos(Latitude) * cos(Longitude - ('".$bean->Longitude."'))) * 6371 < ".$distance." and Status = ".StatusType::ActiveStatus." and StartDate <= '".$today."' and EndDate >= '".$today."' ";
		
		if($subvalue == 2)	{
			$where	.=	" and (Passphrase like '%".$bean->SearchKey."%' OR Title like '%".$bean->SearchKey."%' OR Location like '%".$bean->SearchKey."%'  OR City like '%".$bean->SearchKey."%'  OR State like '%".$bean->SearchKey."%'   OR Country like '%".$bean->SearchKey."%' OR Description like '%".$bean->SearchKey."%') ";
		}
		$query			=	"SELECT * FROM events where 1 ".$where;
		
		$nearlyByEvents	=	R::getAll($query);
		if(!empty($nearlyByEvents))	{
			$row					=	count($nearlyByEvents);
			if($subvalue == 0)	{
				if($row==1)
					$returnString	=	$nearlyByEvents[0]['Title'];
				else
					$returnString	=	$row;
				return $returnString;
			}
			if($subvalue == 1	||	$subvalue == 2)	{
				foreach($nearlyByEvents as $key=>$value)	{
					$returnArray[$value['id']]['EventId']			=	$value['id'];
					$returnArray[$value['id']]['Passphrase']		=	$value['Passphrase'];
					$returnArray[$value['id']]['Latitude']			=	$value['Latitude'];
					$returnArray[$value['id']]['Longitude']			=	$value['Longitude'];
					$returnArray[$value['id']]['Title']				=	$value['Title'];
					if($subvalue	==	2)	{
						$returnArray[$value['id']]['Description']	=	$value['Description'];
						$returnArray[$value['id']]['Location']		=	$value['Location'];
						$returnArray[$value['id']]['City']			=	$value['City'];
						$returnArray[$value['id']]['State']			=	$value['State'];
						$returnArray[$value['id']]['Country']		=	$value['Country'];
					}
				}
				return array_Values($returnArray);
			}
		}
		else	{
			if($subvalue	==	0)
				return $returnString;
			if($subvalue	==	1	||	$subvalue	==2)
				throw new ApiException("No Details found for the requested Search" , ErrorCodeType::EventNotFound);
		}
	}
}