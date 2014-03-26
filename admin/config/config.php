<?php
/**
 * configuration variables
 *
 * This file has constants and global variable used throughout the application.
 *
 */
//define("TITLE","Intermingl");
define("TITLE","Mingl");
if (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == 'on' ) )
	$site = 'https://';
else
	$site = 'http://';

if($_SERVER['SERVER_ADDR']=='172.21.4.104')
{
	define('BASE_URL',$site.$_SERVER['HTTP_HOST']);
	define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/intermingl/admin');
	define('ADMIN_ABS_PATH',  'C:/wamp/www/intermingl/admin');
	define('SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/intermingl');
	define('ABS_PATH',  'C:/wamp/www/intermingl');
	define('SERVER',  0);
	define('GOBALUSERID', 1);
	define('GLOBALUSERID', 1);
}
else
{
	define('BASE_URL',$site.$_SERVER['HTTP_HOST']);
	define('ADMIN_SITE_PATH',  $site.$_SERVER['HTTP_HOST'].'/admin');
	define('ADMIN_ABS_PATH',  '/var/www/html/admin');
	define('SITE_PATH',  $site.$_SERVER['HTTP_HOST']);
	define('ABS_PATH',  '/var/www/html');
	define('SERVER',  1);
	define('GOBALUSERID', 1);
	define('GLOBALUSERID', 1);
}
//script ans style path
//define('SITE_TITLE', 'Intermingl');
define('SITE_TITLE', 'Mingl');
define('ADMIN_SCRIPT_PATH', ADMIN_SITE_PATH.'/webresources/js/');
define('ADMIN_STYLE_PATH', ADMIN_SITE_PATH.'/webresources/css/');
define('ADMIN_IMAGE_PATH', ADMIN_SITE_PATH.'/webresources/images/');

//Images related constants
define('UPLOAD_USER_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/');
define('UPLOAD_USER_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/thumbnail/');
define('UPLOAD_USER_SMALL_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/smallThumbnail/');

define('UPLOAD_EVENT_COVER_PATH_REL', ABS_PATH.'/admin/webresources/uploads/events/cover/original/');
define('UPLOAD_EVENT_COVER_THUMB_PATH_REL', ABS_PATH.'/admin/webresources/uploads/events/cover/');


define('TEMP_USER_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/temp/');	
define('TEMP_USER_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/temp/');	



if($_SERVER['SERVER_ADDR']=='172.21.4.104')
{
	define('USER_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/users/');	
	define('USER_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/');
	define('USER_THUMB_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/users/thumbnail/');	
	define('USER_THUMB_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/thumbnail/');
	define('USER_SMALL_THUMB_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/users/smallThumbnail/');	
	define('USER_SMALL_THUMB_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/smallThumbnail/');
	
	define('EVENT_COVER_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/events/cover/original/');	
	define('EVENT_COVER_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/events/cover/original/');
	define('EVENT_COVER_THUMB_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/events/cover/');	
	define('EVENT_COVER_THUMB_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/events/cover/');
	
	define('CARD_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/cards/original/');	
	define('CARD_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/cards/original/');
} else {

	define('USER_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/users/');	
	define('USER_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/');
	define('USER_THUMB_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/users/thumbnail/');	
	define('USER_THUMB_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/thumbnail/');
	define('USER_SMALL_THUMB_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/users/smallThumbnail/');	
	define('USER_SMALL_THUMB_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/users/smallThumbnail/');
	
	define('EVENT_COVER_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/events/cover/original/');	
	define('EVENT_COVER_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/events/cover/original/');
	define('EVENT_COVER_THUMB_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/events/cover/');	
	define('EVENT_COVER_THUMB_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/events/cover/');
	
	define('CARD_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/cards/original/');	
	define('CARD_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/cards/original/');
	/*define('USER_IMAGE_PATH', 'http://dvaaevwam16m7.cloudfront.net/users/');
	define('USER_IMAGE_PATH_REL', 'http://dvaaevwam16m7.cloudfront.net/users/');	
	define('USER_THUMB_IMAGE_PATH', 'http://dvaaevwam16m7.cloudfront.net/users/thumbnail/');	
	define('USER_THUMB_IMAGE_PATH_REL', 'http://dvaaevwam16m7.cloudfront.net/users/thumbnail/');
	define('USER_SMALL_THUMB_IMAGE_PATH', 'http://dvaaevwam16m7.cloudfront.net/users/smallThumbnail/');	
	define('USER_SMALL_THUMB_IMAGE_PATH_REL', 'http://dvaaevwam16m7.cloudfront.net/users/smallThumbnail/');	
	
	define('EVENT_COVER_IMAGE_PATH', 'http://dvaaevwam16m7.cloudfront.net/events/cover/original/');	
	define('EVENT_COVER_IMAGE_PATH_REL', 'http://dvaaevwam16m7.cloudfront.net/events/cover/original/');	
	define('EVENT_COVER_THUMB_IMAGE_PATH', 'http://dvaaevwam16m7.cloudfront.net/events/cover/');	
	define('EVENT_COVER_THUMB_IMAGE_PATH_REL', 'http://dvaaevwam16m7.cloudfront.net/events/cover/');
	
	define('CARD_IMAGE_PATH', SITE_PATH.'/admin/webresources/uploads/cards/original/');	
	define('CARD_IMAGE_PATH_REL', ABS_PATH.'/admin/webresources/uploads/cards/original/');*/
}
define('LIMIT',100);
define('PERPAGE',25);
define('PASSPHRASE_LENGTH',8);
define('ADMIN_PER_PAGE_LIMIT', 10);

define('AWSACCESSKEY', 'AKIAIPHGO5I52FSST5SQ');
define('AWSSECRETKEY', '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');

if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
	define('BUCKET_NAME','intermingldemo');
}
else{
	define('BUCKET_NAME','intermingl');
}
//Encrypt word
define('ENCRYPTSALT',      'saltisgood');
global $admin_per_page_array;
$admin_per_page_array = array(10,50,100,200,250);
define('ADMIN_PER_PAGE_ARRAY', 'return ' . var_export($admin_per_page_array, 1) . ';');//define constant array
global $userStatus;
$userStatus	=	array('1'=>'Active','2'=>'Inactive','3'=>'Deleted','4'=>'Incomplete');
global $methodArray;
$methodArray = array('POST','DELETE','GET','PUT');
global $shareTypeArray;
$shareTypeArray = array('1'=>'Facebook','2'=>'Twitter','3'=>'Email');
global $postTypeArray;
$postTypeArray = array('1'=>'Text','2'=>'Image','3'=>'Video');
global $platformArray;
$platformArray = array('0'=>'Web','1'=>'ios','2'=>'Android');
global $month_name;
$month_name 		= array("1"	=>	"January",
						"2"	=>	"February",
						"3"	=>	"March",
						"4"	=>	"April",
						"5"	=>	"May",
						"6"	=>	"June",
						"7"	=>	"July",
						"8"	=>	"August",
						"9"	=>	"September",
						"10"	=>	"October",
						"11"	=>	"November",
						"12"	=>	"December",
						);
global $genderArray;
$genderArray	=	array('1'=>'Male','2'=>'Female','3'=>'Not Tell');
global $socialNetworkArray;
$socialNetworkArray	=	array('1'=>'Facebook','2'=>'LinkedId');
global $statusArray;
$statusArray	=	array('1'=>'Active','2'=>'Inactive','3'=>'Delete');
global $notification_msg;
$notification_msg	=	array('1'=>'added successfully','2'=>'updated successfully','3'=>'deleted successfully','4'=>'Status changed successfully','5'=>'Message sent successfully');
global $notification_msg_class;
$notification_msg_class	=	array('1'=>'success_msg','2'=>'success_msg','3'=>'error_msg','4'=>'success_msg','5'=>'success_msg'); 
global $cardTypeArray;
$cardTypeArray	=	array('1'=>'First Card','2'=>'Second Card','3'=>'Third Card');
global $device_type_array;
$device_type_array	=	array(1=> 'iOS', 2=> 'Android', 3=> 'Web');
/* global $goalStatus;
$goalStatus	=	array(1=>'Active',2=>'Inactive');
global $tagStatus;
$tagStatus	=	array(1=>'Active',2=>'Inactive' );
global $interestStatus;
$interestStatus	=	array(1=>'Active',2=>'Inactive');
global $eventStatus;
$eventStatus	=	array(1=>'Active',2=>'Inactive');
global $bulkActions;
$bulkActions	=	array(1=>'Active',2=>'Inactive',3=>'Delete'); */
?>
