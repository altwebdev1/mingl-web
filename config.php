<?php
error_reporting(E_ALL);
/**
 * Database configuration
 */
if($_SERVER['SERVER_ADDR']=='172.21.4.104'){
		define('DBTYPE',    'mysql');
		define('DBUSER',    'root');
		define('DBPASS',    '');
		define('DBHOST',    'localhost');
		define('DBNAME',    'intermingl');
		define('DBDSN',     DBTYPE.':'.'host='.DBHOST.';dbname='.DBNAME);
		define('DBDSNEZ',   DBTYPE.'://'.DBUSER.':'.DBPASS.'@'.DBHOST.'/'.DBNAME);
}
else{
		define('DBTYPE',    'mysql');
		define('DBUSER',    'mingldbuser');
		define('DBPASS',    'mi2ng0ld1bu4ser');
		define('DBHOST',    'aa1lp0mi27p9f2t.c5wtujrxixvn.us-west-2.rds.amazonaws.com');
		define('DBNAME',    'ebdb');
		define('DBDSN',     DBTYPE.':'.'host='.DBHOST.';dbname='.DBNAME);
		define('DBDSNEZ',   DBTYPE.'://'.DBUSER.':'.DBPASS.'@'.DBHOST.'/'.DBNAME);
}
 
define('ENCRYPT_SALT',      'saltisgood');
//include files
/**
 * Load constants and functions
 */

require_once('admin/config/config.php');


/**
 * Load libraries
 */
require('vendor/autoload.php');                       // composer library
require_once 'lib/interminglApi.php';                   	// application
require_once 'lib/interminglApiResponse.php';           	// api response object
require_once 'lib/interminglApiResponseMeta.php';       	// api response meta object
require_once 'lib/Helpers/RedBeanHelper.php';         // RedBean helper
//require_once 'lib/Helpers/ArrayHelper.php';           // array helper
require_once 'lib/Helpers/PasswordHelper.php';        // password helper
require_once 'lib/Enumerations/AccountType.php';      // user type enumeration
require_once 'lib/Enumerations/StatusType.php';      	// status type enumeration
require_once 'lib/Enumerations/ErrorCodeType.php';    // error code type enumeration
require_once 'lib/Enumerations/HttpStatusCode.php';   // http status code
require_once 'lib/Exceptions/ApiException.php';       // http exceptions

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