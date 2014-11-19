<?php
/*
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
session_start();
include_once "templates/base.php";

/************************************************
  Make an API request authenticated with a service
  account.
 ************************************************/
require_once realpath(dirname(__FILE__) . '/../autoload.php');

/************************************************
  ATTENTION: Fill in these values! You can get
  them by creating a new Service Account in the
  API console. Be sure to store the key file
  somewhere you can get to it - though in real
  operations you'd want to make sure it wasn't
  accessible from the webserver!
  The name is the email address value provided
  as part of the service account (not your
  address!)
  Make sure the Books API is enabled on this
  account as well, or the call will fail.
 ************************************************/
$client_id = '<YOUR_CLIENT_ID>'; //Client ID
$client_id            = '258042696143-s9klbbpj13fb40ac8k5qjajn4e9o1c49.apps.googleusercontent.com';
$service_account_name = '258042696143-s9klbbpj13fb40ac8k5qjajn4e9o1c49@developer.gserviceaccount.com'; //Email Address
$key_file_location = 'API Project-69e4673ea29e.p12'; //key.p12

echo pageHeader("Service Account Access");
if ($client_id == '<YOUR_CLIENT_ID>'
    || !strlen($service_account_name)
    || !strlen($key_file_location)) {
  echo missingServiceAccountDetailsWarning();
}

$client = new Google_Client();
$client->setApplicationName("Dolibarr");




/************************************************
  If we have an access token, we can carry on.
  Otherwise, we'll get one with the help of an
  assertion credential. In other examples the list
  of scopes was managed by the Client, but here
  we have to list them manually. We also supply
  the service account
 ************************************************/
if (isset($_SESSION['service_token'])) {
//  $client->setAccessToken($_SESSION['service_token']);
}
$key = file_get_contents($key_file_location);

$cred = new Google_Auth_AssertionCredentials(
    $service_account_name,
    array('https://www.googleapis.com/auth/calendar','https://www.googleapis.com/auth/calendar.readonly','https://www.googleapis.com/auth/books'),
	$key
);


$client->setAssertionCredentials($cred);


//$client->setAccessToken(null);


try {
	if ($client->getAuth()->isAccessTokenExpired()) {
		$client->getAuth()->refreshTokenWithAssertion($cred);
	}
}
catch(Exception $e)
{
	print 'Echec recup jeton';
	var_dump($e);
}

var_dump($client->getAccessToken());

$_SESSION['service_token'] = $client->getAccessToken();


/************************************************
  We're just going to make the same call as in the
  simple query as an example.
 ************************************************/
$service = new Google_Service_Books($client);
$optParams = array('filter' => 'free-ebooks');
$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);
echo "<h3>Results Of Call:</h3>";
foreach ($results as $item) {
  echo $item['volumeInfo']['title'], "<br /> \n";
}



/************************************************
  Example for calendars
 ************************************************/
try {
	$service2 = new Google_Service_Calendar($client);
	$calendar = $service2->calendars->get('eldy10@gmail.com');
	echo "<br>\n".'Resume agenda<br>';
	echo $calendar->getSummary();
//	var_dump($calendar);
}
catch(Google_Service_Exception $gse)
{
	print 'errror 1';
	var_dump($gse);
}
catch(Exception $e)
{
	print 'errror 2';
	var_dump($e);
}

echo pageFooter();
