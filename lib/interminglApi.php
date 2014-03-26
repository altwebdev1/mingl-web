<?php

/**
 * Description of intermingl
 * This is the Static class that functions as a singleton of the application
 *
 * @author eventurers
 */
use RedBean_Facade as R;
use Enumerations\HttpStatusCode as HttpStatusCode;
use Exceptions\ApiException as ApiException;

class interminglApi {

    /**
     * Oauth error code lookup
     * see: League\OAuth2\Server\Authorization::$exceptionHttpStatusCodes
     *
     * Exception error HTTP status codes
     * @var array
     *
     * RFC 6749, section 4.1.2.1.:
     * No 503 status code for 'temporarily_unavailable', because
     * "a 503 Service Unavailable HTTP status code cannot be
     * returned to the client via an HTTP redirect"
     */
    public static $errorCodeLookup = array(
        0   =>  400,    // invalid_request
        1   =>  400,    // unauthorized_client
        2   =>  401,    // access_denied
        3   =>  400,    // unsupported_response_type
        4   =>  400,    // invalid_scope
        5   =>  500,    // server_error
        6   =>  400,    // temporarily_unavailable
        7   =>  501,    // unsupported_grant_type
        8   =>  401,    // invalid_client
        9   =>  400     // invalid_grant
    );

    /**
     * OAuth resource server
     * @var League\OAuth2\Server\Resource
     */
    public static $resourceServer;

    /**
     * OAuth authorization server
     * @var League\OAuth2\Server\Authorization
     */
    public static $authServer;

    /**
     * Current Request
     * @var \League\OAuth2\Server\Util\Request();
     */
    public static $request;

    /**
     * Initializes application
     *
     * @param boolean $startAuthServer set to true if you want to initialise the authorization server
     */
    public static function init($startAuthServer = false) {

        // start store
        session_start();

        // start database
        R::setup(DBDSN, DBUSER, DBPASS);

        // disable underscores in column names
        RedBean_OODBBean::setFlagBeautifulColumnNames(false);

        // don't allow database schema updates on production servers
        // http://redbeanphp.com/freeze
        if(DBHOST != 'localhost'){
            R::freeze(true);
        }

        // Initiate a new special database connection for oauth
        $db = new League\OAuth2\Server\Storage\PDO\Db(DBDSNEZ);

        // Init a new request
        self::$request = new \League\OAuth2\Server\Util\Request();

        // Initiate the resource server
        self::$resourceServer = new League\OAuth2\Server\Resource(
            new League\OAuth2\Server\Storage\PDO\Session($db)
        );

        // Only start the authentication server when getting new access tokens
        if($startAuthServer)
        {
            self::$authServer = new League\OAuth2\Server\Authorization(
                new League\OAuth2\Server\Storage\PDO\Client($db),
                new League\OAuth2\Server\Storage\PDO\Session($db),
                new League\OAuth2\Server\Storage\PDO\Scope($db)
            );
        }
    }

    /**
     * Check the validity of the OAuth token
     */
    public static function checkToken()  {
        return function()
        {
            try {
				//echo"<br>see here==========>";die();
                self::$resourceServer->isValid();
            }
            catch (League\OAuth2\Server\Exception\InvalidAccessTokenException $e)
            {
                $response = new interminglApiResponse();
                $response->setStatus(HttpStatusCode::BadRequest);
                $response->meta->errorMessage = $e->getMessage();

                echo $response;

                /**
                 * Stop the execution
                 * @var $app Slim\Slim
                 */
                $app = Slim\Slim::getInstance();
                $app->stop();
            }
        };
    }

    /**
     * @param $e Exception      Exception which was raised
     * @param int $status int       HTTP status code for the error
     * @param $errors array     More error information
     * @internal param \Slim\Slim $app Slim application
     */
    public static function showError($e,$status = 1, $errors = null){ // $status = HttpStatusCode::InternalServerError

        $response = new interminglApiResponse();
        $response->setStatus($status);
        $response->meta->errorMessage = $e->getMessage();

        if($errors)
            $response->meta->errors = $errors;

        echo $response;
    }
}