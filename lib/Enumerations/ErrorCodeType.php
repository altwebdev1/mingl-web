<?php

/**
 * Description of Account Types
 *
 * @author eventurers
 */

namespace Enumerations;


class ErrorCodeType {
	const SomeFieldsMissing = 1000;
	const EmailAlreadyExists = 1001;
    const FbIdAlreadyExists = 1002;
	const TwitterIdAlreadyExists = 1003;
	const UserNameAlreadyExists = 1004;
	const NoAuthoriseToRequestForPassword = 1005;
	const ErrorInUpdateForgetPassword = 1006;
	const NoUserNameExists = 1007;
	const NotAllowedToDisconnect = 1008;
	const NotAccessToDoProcess = 1009;
	
	const UserNotFound = 1010;
	const UserNotInActiveStatus = 1011;
	const userNotAllowedToDoProcess = 1012;
	const userNotAllowedToAddOrBlockOwn = 1013;
	const userNotAllowedToMessageOwn = 1014;
	
	const ErrorInProcessing = 1015;
	const ErrorInSaving = 1016;
	
	const ProblemInImage = 1017;
	const ProblemInVIdeo = 1018;
	const ProblemInAudio = 1019;
	
	const ErrorInNewPost = 1020;
	const ErrorInHashtagName = 1021;
	const ErrorInPostType = 1022;
	const ErrorInProcessType = 1023;
	const ErrorInSettingTypeOrSettingAction = 1024;
	
	const AlreadyLiked = 1025;
	const Alreadyfollowed = 1026;
	const AlreadyAddedOrBlockedContact = 1027;
	const NotAllowToUnlike = 1028;
	const NotAllowToUnfollow = 1029;
	const NotAllowToDeleteComments = 1030;
	const NotAllowToRemoveFromContact = 1031;
	
	const HashtagNotExists = 1032;
	const PostNotInActiveState = 1033;
	const CheckAction = 1034;
	const CheckShareType = 1035;
	const CheckType = 1036;
	const ContactType = 1037;
	const MessageType = 1038;
	const NotAllowToDeleteMessages = 1039;
	const userNotAllowedToDeleteMessageOwn = 1040;
	const NoEmailAddressExists = 1041;
	const HashtagNotInActiveState = 1042;
	const TagAlreadyExists = 1043;
	const LinkedInIdAlreadyExists = 1044;
	const GooglePlusAlreadyExists = 1045;
	
	const EventNotFound	=	1046;
	const EventNotInActiveStatus	=	1047; // Haven't used so far /* 05-03-2014 */
	const GoalNotFound	=	1048;
	const CardNotExist	=	1049;
	const InterestAlreadyExists = 1050;

	
	const NoResultFound = 2000;
	const SomeFieldsRequired = 3000;
	

	
	
}