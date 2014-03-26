#!/usr/bin/env php
<?php

// Load the AWS SDK for PHP
require __DIR__ . '/aws.phar';

/*
 * Your access key for AWS
 */
define('AWS_ACCESS_KEY', 'AKIAIPHGO5I52FSST5SQ');//

/*
 * Your private AWS key
 */
define('AWS_PRIVATE_KEY', 'mo6ukbUdmTwjZOhOFvJ6XdwG+v21pdbxuWtE2g54');//

// Create a new Amazon SNS client
$sns = Aws\Sns\SnsClient::factory(array(
    'key'    => AWS_ACCESS_KEY,
    'secret' => AWS_PRIVATE_KEY,
    'region' => 'us-west-2'
));

$EndpointArn = 'arn:aws:sns:us-west-2:782176346226:endpoint/GCM/BroadtagsDev-Android/2ca47394-a941-36e1-a5b2-f5dbbdeb9cb9';
try
  {
			$message = 'hi . this is endpoint check for you1111111';
			$apns = 'GCM';
			$dat = json_encode(array(
			            'data' => array('message' => $message,'badge'=>1 ,'sound' => 'default','fromUserId' => 12,'type' => 2 ),
			        ));
			$data = array(
			    'TargetArn' => $EndpointArn,
			    'MessageStructure' => 'json',
			    'Message' => json_encode(array(
			        $apns => $dat
			    ))
			 );
		echo'<pre>';print_r($data);echo'</pre>';
		$sns->publish($data);
}
catch (Exception $e)
  {
    print($EndpointArn . " - Failed: " . $e->getMessage() . "!\n");
  }
die();

// Get and display the platform applications
print("List All Platform Applications:\n");
$Model1 = $sns->listPlatformApplications();
foreach ($Model1['PlatformApplications'] as $App)
{
  print($App['PlatformApplicationArn'] . "\n");
}
print("\n");

// Get the Arn of the first application
$AppArn = $Model1['PlatformApplications'][1]['PlatformApplicationArn'];

// Get the application's endpoints
$Model2 = $sns->listEndpointsByPlatformApplication(array('PlatformApplicationArn' => $AppArn));

// Display all of the endpoints for the first application
print("List All Endpoints for First App:\n");
foreach ($Model2['Endpoints'] as $Endpoint)
{
  $EndpointArn = $Endpoint['EndpointArn'];
  print($EndpointArn . "\n");
}
print("\n");

// Send a message to each endpoint
print("Send Message to all Endpoints:\n");
$type = 1;$userId = 2;$message = "testing for local";$badge = 34;$processId = 5;
foreach ($Model2['Endpoints'] as $Endpoint)
{
  $EndpointArn = $Endpoint['EndpointArn'];

  try
  {
    	  if($_SERVER['SERVER_ADDR']=='172.21.4.104')
				$apns = 'APNS_SANDBOX';
		    else
		  		$apns = 'APNS';
			$data = array(
				    'TargetArn' => $EndpointArn,
				    'MessageStructure' => 'json',
				    'Message' => json_encode(array(
				        'processId' => $processId,'type' => $type,'userId' => $userId ,
						$apns => json_encode(array(
				            'aps' => array('alert' => $message,'badge'=>$badge ,'sound' => 'default'),
				        ))
				    ))
				 );
			$sns->publish($data);
	/*$sns->publish(array('Message' => 'Hello from PHP',
			'TargetArn' => $EndpointArn));*/

    print($EndpointArn . " - Succeeded!\n");
  }
  catch (Exception $e)
  {
    print($EndpointArn . " - Failed: " . $e->getMessage() . "!\n");
  }
}

?>