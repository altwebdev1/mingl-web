<?php
class Model extends Database
{
    /*Table Name*/
    var $adminTable              			=   "admins";
	var $userTable               			=   "users";
	var $TagTable              	 			=   "tags";
	var $hashTagPostTable           		=   "hashtagpost";
	var $hashCountersTable         	 		=   "hashcounters";
	var $hashTagProcessTable				=	"hashtagprocess";
	var $wordsTable                 		=   "words";
	var $staticpagesTable           		=   "staticpages";
	var $oauthClientEndpointsTable  		=   "oauth_client_endpoints";
	var $oauthClientEndpointsParamsTable	=	"oauth_client_endpoints_params";
	var $sharetrackingTable					=   "sharetracking";
	var $contactTable               		=   "contact";
	var $commentsTable              		=   "comments";
	var $postLikeTable              		=   "postlike";
	var $activityTable              		=   "activity";
	var $messageTable               		=   "message";
	var $eventsTable               			=   "events";
	var $joinEventsTable               		=   "joinevents";
	var $goalsTable               			=   "goals";
	var $interestsTable               		=   "interests";
	var $distanceTable               		=   "setting";
	var $userInterestTable             		=   "userinterests";
	var $userTagTable             			=   "usertags";
	var $cardsTable             			=   "cards";
	var $connectionsTable					=   "connections";
	var $logTable 				 			=	"logs";
	var $oauthSessionAccessTokensTable 		=	"oauth_session_access_tokens";
	var $oauthSessionTable 					=	"oauth_sessions";
	var $oauthClientsTable 					=	"oauth_clients";
	/*Table Name*/
	function Model()
	{
		global $globalDbManager;
		$this->dbConnect = $globalDbManager->dbConnect;
	}
}?>