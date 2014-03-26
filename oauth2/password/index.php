<?php

/**
 * @author eventurers
 */

/**
 * Load configuration
 */
require_once('../../config.php');

/**
 * Load libraries
 */
require('../../vendor/autoload.php');                   // composer library
require_once '../../lib/interminglApi.php';               	// application
require_once '../../lib/Helpers/PasswordHelper.php';    // password helper
require_once '../../lib/interminglApiResponse.php';       	// response
require_once '../../lib/interminglApiResponseMeta.php';   	// response meta
require_once '../../lib/ModelBaseInterface.php';        // base interface class for RedBean models
require_once '../../lib/Model_Users.php';            	// Model: Account

/**
 * Initialize application
 *
 * We set $startAuthServer to true to start the authorization server
 */
interminglApi::init(true);
$app = new \Slim\Slim();

/**
 * Check the Facebook and linkedIn Id callback function
 */
$checkLoginCallBack = function($userName,$password,$facebookId, $linkedInId,$deviceToken,$endpointARN,$platform) {
    return Model_Users::checkLogin($userName,$password,$facebookId, $linkedInId,$deviceToken,$endpointARN,$platform);
};

/**
 *
 * This is how you obtain an authentication token
 * We are doing a post to the following endpoint: /oauth2/password/token
 * When doing so we pass the following parameters:
 
 * - ClientId
 * - ClientSecret
 * - FBId
 * - LinkedInId
 * - UserName
 * - Password
 *
 * path: /oauth2/password/token
 */
$app->post('/token', function () use ($app, $checkLoginCallBack) {
	try {

        $req = $app->request();
		$res = $app->response();
		$res['Content-Type'] = 'application/json';

        // grab the authorization server from the api
        $authServer = interminglApi::$authServer;
		
        // We are going for this flow in oauth 2.0: Resource Owner Password Credentials Grant
        $grant = new League\OAuth2\Server\Grant\Password($authServer);

        // this is where we check the Facebook and linkedIn Id
		
		$user_id = $grant->setVerifyCredentialsCallback($checkLoginCallBack);
        $authServer->addGrantType($grant); 
        // get the response from the server
        $response = $authServer->getGrantType('password')->completeFlow(); 
		echo json_encode($response);
		//showError

    }
    catch (League\OAuth2\Server\Exception\ClientException $e) {

        // Get the http status code based on the oauth error
        $status = interminglApi::$errorCodeLookup[$e->getCode()];
		interminglApi::showError($e, $status);
    }
    catch (Exception $e) {

        // Something went wrong
        interminglApi::showError($e);

    }
});

/**
 * Start the Slim Application
 */
$app->run();