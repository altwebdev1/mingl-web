<?php

/**
 * Version endpoint
 * /v1
 *
 * @author eventurers
 */

/**
 * Load configuration
 */
require_once('../config.php');

/**
 * Load libraries
 */
require('../vendor/autoload.php');                      // composer library
require_once '../lib/broadtagsApi.php';                   // application
require_once '../lib/broadtagsApiResponse.php';           // api response object
require_once '../lib/broadtagsApiResponseMeta.php';       // api response meta object
require_once '../lib/broadtagsApiSetup.php';              // database setup script
require_once '../lib/Helpers/PasswordHelper.php';       // password helper
require_once '../lib/Enumerations/AccountType.php';     // account type enumeration
require_once '../lib/Enumerations/HttpStatusCode.php';  // status code enumeration

use RedBean_Facade as R;
use Enumerations\HttpStatusCode as HttpStatusCode;

/**
 * Initialize application
 */
broadtagsApi::init();
$app = new \Slim\Slim();

/**
 * Setup the database
 * GET /v1/setup
 */
$app->get('/setup', function () use ($app) {

    try {

        // Create the database schema
        broadtagsApiSetup::init();

        $response = new broadtagsApiResponse();
        $response->setStatus(HttpStatusCode::Found);
        $response->addNotification("Database connected successfully.");

        echo $response;
    }
    catch(Exception $e) {

        // If occurs any error message then goes here
        broadtagsApi::showError($e);
    }

});

/**
 * Start the Slim Application
 */
$app->run();