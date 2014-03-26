<?php

/**
 * Job Details endpoint
 * /v1/JobCards
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
require_once '../../lib/Model_General.php';              // Job details model

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
 * Get static pages
 * GET /v1/
 */
$app->get('/', function () use ($app) {

    try {

        /**
         * Retreiving Supported country array
         */
        $response = new interminglApiResponse();
        $response->setStatus(HttpStatusCode::Ok);
        $response->meta->dataPropertyName = 'StaticContent';
		$cms = new Model_General();
		$pages = $cms->getStaticPages();
	    $response->returnedObject = $pages;
        $response->addNotification('Static content has been retrieved successfully');
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
 * Start the Slim Application
 */

$app->run();