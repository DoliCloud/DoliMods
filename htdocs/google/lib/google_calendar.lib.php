<?php
/* Copyright (C) 2011      Regis Houssin
 * Copyright (C) 2010-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 *
 * Documentation API v2 (Connect method used is "ClientLogin"):
 * https://developers.google.com/google-apps/calendar/v2/developers_guide_protocol
 * => V3:
 * https://developers.google.com/google-apps/calendar/migration
 * https://developers.google.com/google-apps/calendar/firstapp
 * https://developers.google.com/google-apps/calendar/v3/reference/
 *
 * Rem:
 * To get event:  https://www.google.com/calendar/feeds/default/private/full?start-min=2013-03-16T00:00:00&start-max=2014-03-24T23:59:59
 * To get list of calendar: https://www.google.com/calendar/feeds/default/allcalendars/full
 */

include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

dol_include_once("/google/lib/google.lib.php");
//$res=dol_include_once('/google/includes/google-api-php-client/autoload.php');
$res=dol_include_once('/google/includes/google-api-php-client/vendor/autoload.php');

//if (! class_exists('Google_Client')) dol_print_error('','Failed to load library file /nltechno/google/includes/google-api-php-client/autoload.php');

/**
 * @var string Location of AuthSub key file.  include_path is used to find this
 */
$_authSubKeyFile = null; // Example value for secure use: 'mykey.pem'

/**
 * @var string Passphrase for AuthSub key file.
 */
$_authSubKeyFilePassphrase = null;






/**
 * Get service
 *
 * @param	string		$clientid			Client ID
 * @param	string		$clientsecret		Client secret
 * @return	array                           Array
 * @deprecated Not used anymore ?
 */
function getTokenFromWebApp($clientid, $clientsecret)
{
	$client = new Google_Client();
	// OAuth2 client ID and secret can be found in the Google Developers Console.
	$client->setClientId($clientid);
	$client->setClientSecret($clientsecret);
	$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
	$client->addScope('https://www.googleapis.com/auth/calendar');
	$client->addScope('https://www.googleapis.com/auth/calendar.readonly');

	$service = new Google_Service_Calendar($client);

	$authUrl = $client->createAuthUrl();
	/*
	// Request authorization
	print "Please visit:\n$authUrl\n\n";
	print "Please enter the auth code:\n";
	//$authCode = trim(fgets(STDIN));

	// Exchange authorization code for access token
	//$accessToken = $client->authenticate($authCode);
	//$client->setAccessToken($accessToken);
	*/
	return array('client'=>$client, 'service'=>$service, 'authUrl'=>$authUrl);
}



/**
 * Get service token
 *
 * @param	string			$service_account_name		Service account name (Example: '258042696143-testbbpj13fb40ac8k5qjajn4e96test@developer.gserviceaccount.com'). Not used for authentication with mode=web.
 * @param	string			$key_file_location			Key file location (Example: 'API Project-69e4673ea29e.p12'). Not used for authentication with mode=web.
 * @param	int				$force_do_not_use_session	1=Do not get token from sessions $_SESSION['google_service_token_'.$conf->entity] or $_SESSION['google_web_token_'.$conf->entity]
 * @param	string			$mode						'service' or 'web' (Choose which token to use)
 * @param	string			$user_to_impersonate		The email of user we want the service account to act as.
 * @return	array|string								Error message or array with token
 */
function getTokenFromServiceAccount($service_account_name, $key_file_location, $force_do_not_use_session = false, $mode = 'service', $user_to_impersonate = false)
{
	global $conf;

	$applicationname = "Dolibarr";

	$client = new Google_Client();
	$client->setApplicationName($applicationname);	// Set prefix of User Agent. User agent is set by PHP API in method Client->execute() of PHP Google Lib.
	//$client->setClassConfig('Google_Cache_File', 'directory', $conf->google->dir_temp);		// Force dir if cache used is Google_Cache_File

	if ($mode == 'web') {    // use to synch contact
		if (empty($conf->global->GOOGLE_API_CLIENT_ID)) return 'ErrorModuleGoogleNoGoogleClientId';
		if (empty($conf->global->GOOGLE_API_CLIENT_SECRET)) return 'ErrorModuleGoogleNoGoogleClientSecret';

		$client->setClientId($conf->global->GOOGLE_API_CLIENT_ID);
		$client->setClientSecret($conf->global->GOOGLE_API_CLIENT_SECRET);
		$client->setAccessType('offline');

		if (empty($force_do_not_use_session) && isset($_SESSION['google_web_token_'.$conf->entity])) {
			dol_syslog("Get web token from session. google_web_token=".(is_array($_SESSION['google_web_token_'.$conf->entity])?implode(",",$_SESSION['google_web_token_'.$conf->entity]):$_SESSION['google_web_token_'.$conf->entity]));
			$client->setAccessToken($_SESSION['google_web_token_'.$conf->entity]);
		}
		if ((! isset($_SESSION['google_web_token_'.$conf->entity]) || ! empty($force_do_not_use_session)) && ! empty($conf->global->GOOGLE_WEB_TOKEN)) {
			// Look into database
			// $conf->global->GOOGLE_WEB_TOKEN = '{"access_token":"ya29.iQEPBPUAVLXeVq1-QnC6-SHydA9czPX3ySJ5SjkSo5ZIMfFEl5MTs62no8hZp5jUUsm3QVHTrBg7hw","expires_in":3600,"created":1433463453}';
			$_SESSION['google_web_token_'.$conf->entity] = $conf->global->GOOGLE_WEB_TOKEN;
			dol_syslog("Get service token from database and save into session. google_web_token=".$_SESSION['google_web_token_'.$conf->entity]);
			$client->setAccessToken($_SESSION['google_web_token_'.$conf->entity]);
		}

		if (empty($_SESSION['google_web_token_'.$conf->entity])) {
			return 'GoogleWebTokenNotDefinedDoALoginInitFirst';
		} else {
			dol_syslog("getTokenFromServiceAccount set current token to ".(is_array($_SESSION['google_web_token_'.$conf->entity])?implode(",",$_SESSION['google_web_token_'.$conf->entity]):$_SESSION['google_web_token_'.$conf->entity]), LOG_DEBUG);
			$client->setAccessToken($_SESSION['google_web_token_'.$conf->entity]);
		}

		try {
			dol_syslog("getTokenFromServiceAccount check isAccessTokenExpired", LOG_DEBUG);
			$checktoken=$client->isAccessTokenExpired();
			if ($checktoken) {
				$tmp=json_decode($conf->global->GOOGLE_WEB_TOKEN, true);
				$refreshtoken=$tmp['refresh_token'];
				if (empty($refreshtoken)) $refreshtoken=$tmp['access_token'];
				dol_syslog("getTokenFromServiceAccount token seems to be expired, we refresh it with the refresh token = ".$refreshtoken);
				$client->refreshToken($refreshtoken);
				$_SESSION['google_web_token_'.$conf->entity]= $client->getAccessToken();
				dol_syslog("getTokenFromServiceAccount new token in session is now ".(is_array($_SESSION['google_web_token_'.$conf->entity])?implode(",",$_SESSION['google_web_token_'.$conf->entity]):$_SESSION['google_web_token_'.$conf->entity]), LOG_DEBUG);
			} else dol_syslog("getTokenFromServiceAccount token not expired", LOG_DEBUG);
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	if ($mode == 'service') {    // used to sync events-calendar
		if (empty($service_account_name)) return 'ErrorModuleGoogleNoServiceAccountName';
		if (empty($key_file_location) || ! file_exists($key_file_location)) return 'ErrorModuleGoogleKeyFileNotFound';

		/************************************************
		  If we have an access token, we can carry on.
		  Otherwise, we'll get one with the help of an
		  assertion credential. In other examples the list
		  of scopes was managed by the Client, but here
		  we have to list them manually. We also supply
		  the service account
		 ************************************************/
		if (empty($force_do_not_use_session) && isset($_SESSION['google_service_token_'.$conf->entity])) {
			dol_syslog("Get service token from session. service_token=".$_SESSION['google_service_token_'.$conf->entity]);
			$client->setAccessToken($_SESSION['google_service_token_'.$conf->entity]);
		}

		dol_syslog("getTokenFromServiceAccount service_account_name=".$service_account_name." key_file_location=".$key_file_location." force_do_not_use_session=".$force_do_not_use_session, LOG_DEBUG);

		// API v1
		/*
		$key = file_get_contents($key_file_location);
		$cred = new Google_Auth_AssertionCredentials(
			$service_account_name,
			array('https://www.googleapis.com/auth/calendar','https://www.googleapis.com/auth/calendar.readonly'),
			$key,
			'notasecret',
			'http://oauth.net/grant_type/jwt/1.0/bearer',
			$user_to_impersonate
		);
		$client->setAssertionCredentials($cred);
		*/

		// API v2
		if ($user_to_impersonate) {
			$client->setSubject($user_to_impersonate);
		}

		try {
			$client->setAuthConfig($key_file_location);
			$client->setAccessType('offline');
			$scopes = array('https://www.googleapis.com/auth/calendar','https://www.googleapis.com/auth/calendar.events');
			$client->setScopes($scopes);
		} catch (Exception $e) {
			dol_syslog("getTokenFromServiceAccount Error ".$e->getMessage(), LOG_ERR);
			return $e->getMessage();
		}

		try {
			// API v1
			/*$checktoken=$client->getAuth()->isAccessTokenExpired();
			if ($checktoken)
			{
				dol_syslog("getTokenFromServiceAccount token seems to be expired, we refresh it", LOG_DEBUG);
				$client->getAuth()->refreshTokenWithAssertion($cred);
			}*/

			// API v2
			$checktoken=$client->isAccessTokenExpired();
			if ($checktoken) {
				dol_syslog("getTokenFromServiceAccount token seems to be expired, we refresh it", LOG_DEBUG);
				$result = $client->refreshTokenWithAssertion();
				//var_dump($result);
			}
			//var_dump($checktoken);
		} catch (Exception $e) {
			dol_syslog("getTokenFromServiceAccount Error ".$e->getMessage(), LOG_ERR);
			return $e->getMessage();
		}
	}


	if ($mode == 'web') {
		$_SESSION['google_web_token_'.$conf->entity] = $client->getAccessToken();	// Overwrite session with correct token

		dol_syslog("getTokenFromServiceAccount Return client name = ".$applicationname." google_web_token = ".is_array($_SESSION['google_web_token_'.$conf->entity])?implode(",",$_SESSION['google_web_token_'.$conf->entity]):$_SESSION['google_web_token_'.$conf->entity], LOG_INFO);
		//dol_syslog("getTokenFromServiceAccount getBasePath = ".$client->getBasePath(), LOG_DEBUG);
	}
	if ($mode == 'service') {
		$tmpres = $client->getAccessToken();

		$_SESSION['google_service_token_'.$conf->entity] = $tmpres;	// Overwrite session with correct token

		dol_syslog("getTokenFromServiceAccount Return client name = ".$applicationname." google_service_token = ".$_SESSION['google_service_token_'.$conf->entity], LOG_INFO);
		//dol_syslog("getTokenFromServiceAccount getBasePath = ".$client->getBasePath(), LOG_DEBUG);
	}

	return array('client'=>$client, 'google_service_token'=>!empty($_SESSION['google_service_token_'.$conf->entity])?$_SESSION['google_service_token_'.$conf->entity]:0, 'google_web_token'=>$_SESSION['google_web_token_'.$conf->entity]);
}



/**
 * Creates an event on the authenticated user's default calendar with the
 * specified event details.
 *
 * @param  array	$client   		Service array with authenticated client object
 * @param  string	$object	   		Source object into Dolibarr
 * @param  string	$login			CalendarId (login google or 'primary')
 * @return string 					The ID URL for the event or 'ERROR xxx' if error.
 */
function createEvent($client, $object, $login = 'primary')
{
	global $conf, $db, $langs;
	global $dolibarr_main_url_root;
	global $user;

	$event = new Google_Service_Calendar_Event();
	$start = new Google_Service_Calendar_EventDateTime();
	$end = new Google_Service_Calendar_EventDateTime();

	$tzfix=0;
	if (! empty($conf->global->GOOGLE_CAL_TZ_FIX) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX;
	if (empty($object->fulldayevent)) {
		$startTime = dol_print_date(($tzfix*3600) + $object->datep, "dayhourrfc", 'gmt');	// Example '2015-07-30T08:00:00Z' if we ask hour 10:00 on a dolibarr with a TZ = +2
		$endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef), "dayhourrfc", 'gmt');

		$start->setDateTime($startTime);	// '2011-06-03T10:00:00.000-07:00'
		$end->setDateTime($endTime);		// '2011-06-03T10:25:00.000-07:00'
	} else {
		$startTime = dol_print_date(($tzfix*3600) + $object->datep, "dayrfc");
		$endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef) + 3600*24, "dayrfc");	// For fulldayevent, into XML data, endTime must be day after

		$start->setDate($startTime);	// '2011-06-03'
		$end->setDate($endTime);		// '2011-06-03'
	}

	$event->setStart($start);
	$event->setEnd($end);

	$event->setSummary(trim($object->label));
	$event->setLocation($object->location);

	$note = html_entity_decode(($object->note_private ? $object->note_private : $object->note), ENT_QUOTES);    // Because dol_string_nohtmltag does not convert simple quotes
	$event->setDescription(dol_string_nohtmltag($note, 0));

	// Transparency 0=available, 1=busy
	$transparency=isset($object->userassigned[$user->id]['transparency'])?$object->userassigned[$user->id]['transparency']:0;
	if ($transparency > 0) $event->setTransparency("opaque");
	else $event->setTransparency("transparent");

	// Define $urlwithroot
	$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', trim($dolibarr_main_url_root));
	$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
	//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

	$urlevent=$urlwithroot.'/comm/action/card.php?id='.$object->id;
	$urlicon=$urlwithroot.'/favicon.ico';

	$source=new Google_Service_Calendar_EventSource();
	$source->setTitle($conf->global->MAIN_APPLICATION_TITLE);
	$source->setUrl($urlevent);

	$event->setSource($source);

	/*$gadget=new Google_Service_Calendar_EventGadget();
	$gadget->setLink($urlevent);
	$gadget->setIconLink($urlicon);
	$event->setGadget($gadget);*/

	$event->setStatus('confirmed');		// tentative, cancelled
	$event->setVisibility('default');	// default, public, private (view by attendees only), confidential (do not use)

	$event->setGuestsCanModify(false);
	$event->setGuestsCanInviteOthers(true);
	$event->setGuestsCanSeeOtherGuests(true);

	/*$organizer = new Google_Service_Calendar_EventOrganizer();
	$organizer->setEmail($user->email);
	$organizer->setDisplayName($user->getFullName($langs));
	$event->setOrganizer($organizer);*/
	$extendedProperties=new Google_Service_Calendar_EventExtendedProperties();
	$extendedProperties->setPrivate(array('dolibarr_id'=>$object->id.'/event', 'dolibarr_user_id'=>$object->userownerid));
	$event->setExtendedProperties($extendedProperties);

	$attendees = array();
	if (! empty($object->userassigned) && ! empty($conf->global->GOOGLE_INCLUDE_ATTENDEES)) {	// This can occurs with automatic events
		foreach ($object->userassigned as $key => $val) {
			if ($key == $user->id) continue;	// ourself, not an attendee
			$fuser=new User($db);
			$fuser->fetch($key);
			if ($fuser->id > 0 && $fuser->email) {
				$attendee = new Google_Service_Calendar_EventAttendee();
				$attendee->setEmail($fuser->email);
				$attendee->setDisplayName($fuser->getFullName($langs));
				$attendees[]=$attendee;
			}
		}
	}
	$event->attendees = $attendees;

	dol_syslog("createEvent for login=".$login.", label=".$object->label.", startTime=".$startTime.", endTime=".$endTime, LOG_DEBUG);

	try {
		$service = new Google_Service_Calendar($client['client']);

		$createdEvent = $service->events->insert($login, $event);

		$ret=$createdEvent->getId();
		dol_syslog("createEvent Id=".$ret, LOG_DEBUG);
	} catch (Exception $e) {
		dol_syslog("error ".$e->getMessage(), LOG_ERR);
		return 'ERROR '.$e->getMessage();
	}

	return $ret;
}


/**
 * Updates the event with the specified ID with the new properties. Also outputs the new and old title
 * with HTML br elements separating the lines.
 *
 * @param  	array					$client   		Service array with authenticated client object (Not used if $service is provided)
 * @param  	string   				$eventId        The event ID string
 * @param  	string					$object	   		Source object into Dolibarr
 * @param  	string					$login			CalendarId (login google or 'primary')
 * @param	Google_Service_Calendar	$service		Object service (will be created if not provided)
 * @return  int                                     1
 */
function updateEvent($client, $eventId, $object, $login = 'primary', $service = null)
{
	global $conf, $db, $langs;
	global $dolibarr_main_url_root;
	global $user;

	$neweventid=$eventId;
	$reg = array();
	if (preg_match('/google\.com\/.*\/([^\/]+)$/', $eventId, $reg)) {
		$neweventid=$reg[1];
	}
	if (preg_match('/google:([^\/]+)$/', $eventId, $reg)) {
		$neweventid=$reg[1];	// TODO This may not be enough because ID in dolibarr is 250 char max and in google may have 1024 chars
	}

	try {
		if (empty($service)) $service = new Google_Service_Calendar($client['client']);

		//$event = new Google_Service_Calendar_Event();
		$event = $service->events->get($login, $neweventid);

		if (is_object($event)) dol_syslog("updateEvent get old record id=".$event->getId()." found into google calendar", LOG_DEBUG);

		// Set new value of events
		$start = new Google_Service_Calendar_EventDateTime();
		$end = new Google_Service_Calendar_EventDateTime();

		$tzfix=0;
		if (! empty($conf->global->GOOGLE_CAL_TZ_FIX) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX;
		if (empty($object->fulldayevent)) {
			$startTime = dol_print_date(($tzfix*3600) + $object->datep, "dayhourrfc", 'gmt');	// we use gmt, tz is managed by the tzfix
			$endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef), "dayhourrfc", 'gmt');	// we use gmt, tz is managed by the tzfix

			$start->setDateTime($startTime);	// '2011-06-03T10:00:00.000-07:00'
			$end->setDateTime($endTime);		// '2011-06-03T10:25:00.000-07:00'
		} else {
			$startTime = dol_print_date(($tzfix*3600) + $object->datep, "dayrfc");
			$endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef) + 3600*24, "dayrfc");	// For fulldayevent, into XML data, endTime must be day after

			$start->setDate($startTime);	// '2011-06-03'
			$end->setDate($endTime);		// '2011-06-03'
		}
		$event->setStart($start);
		$event->setEnd($end);

		$event->setSummary(trim($object->label));
		$event->setLocation($object->location);

		$note = html_entity_decode(($object->note_private ? $object->note_private : $object->note), ENT_QUOTES);    // Because dol_string_nohtmltag does not convert simple quotes
		$event->setDescription(dol_string_nohtmltag($note, 2));

		// Transparency 0=available, 1=busy
		$transparency=isset($object->userassigned[$user->id]['transparency'])?$object->userassigned[$user->id]['transparency']:0;
		if ($transparency > 0) $event->setTransparency("opaque");
		else $event->setTransparency("transparent");

		// Define $urlwithroot
		$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', trim($dolibarr_main_url_root));
		$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
		//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

		$urlevent=$urlwithroot.'/comm/action/card.php?id='.$object->id;
		$urlicon=$urlwithroot.'/favicon.ico';

		// The source can be set only by the creator. And creator may be calendar owner and updater the service account
		//var_dump($login);
		//var_dump($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL);
		//var_dump($event->getCreator()->getEmail());
		/*if ($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL == $event->getCreator()->getEmail())
		{
			$source=new Google_Service_Calendar_EventSource();
			$source->setTitle($conf->global->MAIN_APPLICATION_TITLE);
			$source->setUrl($urlevent);
			$event->setSource($source);
		}*/

		/*$gadget=new Google_Service_Calendar_EventGadget();
		$gadget->setLink($urlevent);
		$gadget->setIconLink($urlicon);
		$event->setGadget($gadget);*/

		$event->setStatus('confirmed');		// tentative, cancelled
		$event->setVisibility('default');	// default, public, private (view by attendees only), confidential (do not use)

		$event->setGuestsCanModify(false);
		$event->setGuestsCanInviteOthers(true);
		$event->setGuestsCanSeeOtherGuests(true);

		/*$organizer = new Google_Service_Calendar_EventOrganizer();
		$organizer->setEmail($user->email);
		$organizer->setDisplayName($user->getFullName($langs));
		$event->setOrganizer($organizer);*/
		$extendedProperties=new Google_Service_Calendar_EventExtendedProperties();
		$extendedProperties->setPrivate(array('dolibarr_id'=>$object->id.'/event','dolibarr_user_id'=>$object->userownerid));
		$event->setExtendedProperties($extendedProperties);

		$attendees = array();
		if (! empty($object->userassigned) && ! empty($conf->global->GOOGLE_INCLUDE_ATTENDEES)) {	// This can occurs with automatic events
			foreach ($object->userassigned as $key => $val) {
				if ($key == $user->id) continue;	// ourself, not an attendee
				$fuser=new User($db);
				$fuser->fetch($key);
				if ($fuser->id > 0 && $fuser->email) {
					$attendee = new Google_Service_Calendar_EventAttendee();
					$attendee->setEmail($fuser->email);
					$attendee->setDisplayName($fuser->getFullName($langs));
					$attendees[]=$attendee;
				}
			}
		}
		$event->attendees = $attendees;

		dol_syslog("updateEvent for login=".$login.", id=".$neweventid.", label=".$object->label.", startTime=".$startTime.", endTime=".$endTime, LOG_DEBUG);

		$updatedEvent = $service->events->update($login, $neweventid, $event);

		// Print the updated date.
		//echo $updatedEvent->getUpdated();

		$ret = 1;
	} catch (Exception $e) {
		dol_syslog("updateEvent error in getting or updating record: ".$e->getMessage(), LOG_WARNING);

		return 'ERROR '.$e->getMessage();
	}

	return $ret;
}

/**
 * Deletes the event specified by retrieving the atom entry object
 * and calling delete() method.  This is for
 * example purposes only, as it is inefficient to retrieve the entire
 * atom entry only for the purposes of deleting it.
 *
 * @param  	array					$client   		Service array with authenticated client object (Not used if $service is provided)
 * @param  	string  				$eventId        The event ID string
 * @param  	string					$login			CalendarId (login google or 'primary')
 * @param	Google_Service_Calendar	$service		Object service (will be created if not provided)
 * @return 	void
 */
function deleteEventById($client, $eventId, $login = 'primary', $service = null)
{
	global $conf, $db, $langs;
	global $dolibarr_main_url_root;
	global $user;

	$neweventid=$eventId;
	if (preg_match('/google\.com\/.*\/([^\/]+)$/', $eventId, $reg)) {
		$neweventid=$reg[1];
	}
	if (preg_match('/google:([^\/]+)$/', $eventId, $reg)) {
		$neweventid=$reg[1];	// TODO This may not be enough because ID in dolibarr is 250 char max and in google may have 1024 chars
	}

	dol_syslog("deleteEventById Delete old record on Google calendar with login=".$login.", id=".$neweventid, LOG_DEBUG);

	try {
		if (empty($service)) $service = new Google_Service_Calendar($client['client']);

		$service->events->delete($login, $neweventid);

		$ret = 1;
	} catch (Exception $e) {
		dol_syslog("deleteEventById error in getting or deleting old record: ".$e->getMessage(), LOG_WARNING);

		return 'ERROR '.$e->getMessage();
	}

	return $ret;
}


/**
 * Complete $object to change ->label and ->note_private before pushing event to Google Calendar.
 *
 * @param 	Object		$object		Object event to complete
 * @param	Translate	$langs		Language object
 * @return	void
 */
function google_complete_label_and_note(&$object, $langs)
{
	global $conf, $db, $langs;
	global $dolibarr_main_url_root;

	$eventlabel = trim($object->label);
	// Define $urlwithroot
	$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', trim($dolibarr_main_url_root));
	$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
	//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current
	if (($object->socid > 0 || (! empty($object->thirdparty->id) && $object->thirdparty->id > 0)) && empty($conf->global->GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE)) {
		$thirdparty = new Societe($db);
		$result=$thirdparty->fetch($object->socid?$object->socid:$object->thirdparty->id);
		if ($result > 0) {
			$eventlabel .= ' - '.$thirdparty->name;
			$tmpadd=$thirdparty->getFullAddress(0);
			$more='';
			if ($tmpadd && empty($conf->global->GOOGLE_DISABLE_ADD_ADDRESS_INTO_DESC)) $more.=$thirdparty->name."\n".$thirdparty->getFullAddress(1)."\n";
			if (! empty($thirdparty->phone)) $more.="\n".$langs->trans("Phone").': '.$thirdparty->phone;
			if (! empty($thirdparty->phone_pro)) $more.="\n".$langs->trans("Phone").': '.$thirdparty->phone_pro;
			if (! empty($thirdparty->fax)) $more.="\n".$langs->trans("Fax").': '.$thirdparty->fax;

			$pagename=(((float) DOL_VERSION >= 6.0)?'/societe/card.php':'/societe/soc.php');

			$urltoelem=$urlwithroot.$pagename.'?socid='.$thirdparty->id;
			$object->note = ($object->note_private ? $object->note_private : ($object->note ? $object->note : $object->note_public));    		// For backward compatibility
			$object->note_public = ($object->note_private ? $object->note_private : ($object->note ? $object->note : $object->note_public));   	// For backward compatibility
			$object->note_private = ($object->note_private ? $object->note_private : ($object->note ? $object->note : $object->note_public));

			if (strpos($object->note, '-----+++++-----') === false) {
				$object->note.="\n\n-----+++++-----\n".$more."\n".$langs->trans("LinkToThirdParty").': '.$urltoelem;
			}
			if (strpos($object->note_public, '-----+++++-----') === false) {
				$object->note_public.="\n\n-----+++++-----\n".$more."\n".$langs->trans("LinkToThirdParty").': '.$urltoelem;
			}
			if (strpos($object->note_private, '-----+++++-----') === false) {
				$object->note_private.="\n\n-----+++++-----\n".$more."\n".$langs->trans("LinkToThirdParty").': '.$urltoelem;
			}
		}
	}
	if (($object->contactid > 0 || $object->contact_id > 0 || (! empty($object->contact->id) && $object->contact->id > 0)) && empty($conf->global->GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT)) {
		$contact = new Contact($db);
		$result=$contact->fetch($object->contact_id ? $object->contact_id : ($object->contactid ? $object->contactid : $object->contact->id));
		if ($result > 0) {
			$eventlabel .= ' - '.$contact->getFullName($langs, 1);
			$tmpadd=$contact->getFullAddress(0);
			$more='';
			if ($tmpadd && empty($conf->global->GOOGLE_DISABLE_ADD_ADDRESS_INTO_DESC)) $more.=$contact->name."\n".$contact->getFullAddress(1)."\n";
			if (! empty($contact->phone)) $more.="\n".$langs->trans("Phone").': '.$contact->phone;
			if (! empty($contact->phone_pro)) $more.="\n".$langs->trans("Phone").': '.$contact->phone_pro;
			if (! empty($contact->phone_perso)) $more.="\n".$langs->trans("PhonePerso").': '.$contact->phone_perso;
			if (! empty($contact->phone_mobile)) $more.="\n".$langs->trans("PhoneMobile").': '.$contact->phone_mobile;
			if (! empty($contact->fax)) $more.="\n".$langs->trans("Fax").': '.$contact->fax;

			$urltoelem=$urlwithroot.'/contact/card.ph?id='.$contact->id;
			$object->note = ($object->note_private ? $object->note_private : ($object->note ? $object->note : $object->note_public));    		// For backward compatibility
			$object->note_public = ($object->note_private ? $object->note_private : ($object->note ? $object->note : $object->note_public));   	// For backward compatibility
			$object->note_private = ($object->note_private ? $object->note_private : ($object->note ? $object->note : $object->note_public));

			if (strpos($object->note, '-----+++++-----') === false) {
				$object->note.="\n\n-----+++++-----\n".$more."\n".$langs->trans("LinkToContact").': '.$urltoelem;
			}
			if (strpos($object->note_public, '-----+++++-----') === false) {
				$object->note_public.="\n\n-----+++++-----\n".$more."\n".$langs->trans("LinkToContact").': '.$urltoelem;
			}
			if (strpos($object->note_private, '-----+++++-----') === false) {
				$object->note_private.="\n\n-----+++++-----\n".$more."\n".$langs->trans("LinkToContact").': '.$urltoelem;
			}
		}
	}
	$object->label = $eventlabel;
}


/**
 * Execute sync
 *
 * @param	string		$userlogin	Name of calendar to sync
 * @param 	User		$fuser		User making sync
 * @param	int			$mindate	Minimum date
 * @param	int			$max		Max nb of records to sync
 * @return	array					array('nbinserted'=>, 'nbupdated'=>, 'errors'=>)
 */
function syncEventsFromGoogleCalendar($userlogin, User $fuser, $mindate, $max = 0)
{
	global $db, $langs, $conf;
	global $dolibarr_main_url_root;

	$tzfix=0;
	if (! empty($conf->global->GOOGLE_CAL_TZ_FIX_G2D) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX_G2D)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX_G2D;

	$nbinserted=0;
	$nbupdated=0;
	$nbdeleted=0;
	$nbalreadydeleted=0;
	$nbnotdeleted=0;		// Not eleed because option off

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
	$force_do_not_use_session=true;
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session);

	if (! is_array($servicearray)) {
		$errors[]=$langs->trans($servicearray);
		$error++;
	}

	if ($error || $servicearray == null) {
		$txterror="Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
		dol_syslog($txterror, LOG_ERR);
		$errors[]=$txterror;
		$error++;
	} else {
		try {
			$service = new Google_Service_Calendar($servicearray['client']);

			// Get last 50 modified record (after mindate)
			$optParams=array('showDeleted'=>true, 'orderBy'=>'updated', 'maxResults'=>$max, 'updatedMin'=>dol_print_date($mindate, 'dayhourrfc', 'gmt'));
			//var_dump($optParams);exit;
			//$optParams=array('maxResults'=>$max, 'orderBy'=>'updated', 'showDeleted'=>True);
			$events = $service->events->listEvents($userlogin, $optParams);

			$i=0;

			while (true) {
				foreach ($events->getItems() as $event) {
					//$event = new Google_Service_Calendar_Event();
					//var_dump($event);

					$i++;

					$status = $event->getStatus();		// 'cancelled', 'confirmed', ...

					$dolibarr_user_id='';
					$extendedProperties=$event->getExtendedProperties();
					if (is_object($extendedProperties)) {
						$priv=$extendedProperties->getPrivate();	// Private property dolibarr_id is set during google create. Not modified by update.
						$dolibarr_user_id=$priv['dolibarr_user_id'];
					}

					$object = new ActionComm($db);
					$ref_ext = substr('google:'.$event->getId(), 0, 255);
					$result = $object->fetch(0, '', $ref_ext);

					if ($result > 0) {	// Found into dolibarr
						$object->fetch_thirdparty();

						//$event = new Google_Service_Calendar_Event();

						// Create into dolibarr
						$ds=$event->getStart();
						$de=$event->getEnd();
						if ($ds) $dates=$ds->getDate();
						if ($de) $datee=$de->getDate();
						if ($ds) $datest=$ds->getDateTime();
						if ($de) $dateet=$de->getDateTime();

						$object->punctual=0;

						if ($datest) {
							// $datest = '2015-07-29T10:00:00+02:00' means 2015-07-29T12:00:00 in TZ +2
							// We remove the TZ from string. tz will be managed by the ($tzfix*3600)
							if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(\-|\+)([0-9]{2})/i', $datest, $reg)) {
								$datest = $reg[1].'-'.$reg[2].'-'.$reg[3].'T'.$reg[4].':'.$reg[5].':'.$reg[6];
								$tzs=(int) ($reg[7].$reg[8]);
							}
							if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(\-|\+)([0-9]{2})/i', $dateet, $reg)) {
								$dateet = $reg[1].'-'.$reg[2].'-'.$reg[3].'T'.$reg[4].':'.$reg[5].':'.$reg[6];
								$tze=(int) ($reg[7].$reg[8]);
							}
							$object->datep=(dol_stringtotime($datest, 1) - ($tzs*3600) - ($tzfix*3600));
							$object->datef=(dol_stringtotime($dateet, 1) - ($tze*3600) - ($tzfix*3600));
							$object->fulldayevent=0;
							if ($object->datep == $object->datef) $object->punctual=1;
							//print dol_print_date($object->datep, 'dayhour', 'tzserver');
						} elseif ($dates) {
							$object->datep=(dol_stringtotime($dates, 0));
							$object->datef=(dol_stringtotime($datee, 0) - 1);
							$object->fulldayevent=1;
						}
						//$object->type_code='AC_OTH';
						//$object->code='AC_OTH';

						$newlabel=$event->getSummary();
						// Remove the ' - thirdpartyname' added when synchronizing from Dolibarr to Google
						$newlabel = preg_replace('/'.preg_quote(' - '.$object->thirdparty->name, '/').'$/', '', $newlabel);
						//var_dump($object->thirdparty->name);var_dump($object->label); var_dump($newlabel);
						$object->label = $newlabel;
						//exit;

						$object->transparency=((empty($transtmp) || $transtmp == 'opaque')?1:0);		// null or 'opaque' = busy = transparency to 1, 'transparent' = available
						//$object->priority=0;
						//$object->percentage=-1;
						$object->location=$event->getLocation();
						//$object->socid=$obj->fk_soc;
						//$object->contact_id=$obj->fk_contact;
						$object->note=trim(preg_replace('/'.preg_quote('-----+++++-----', '/').'.*$/s', '', $event->getDescription()));
						$object->note_public=trim(preg_replace('/'.preg_quote('-----+++++-----', '/').'.*$/s', '', $event->getDescription()));
						$object->note_private=trim(preg_replace('/'.preg_quote('-----+++++-----', '/').'.*$/s', '', $event->getDescription()));

						// Organizer
						/*$organizer=$event->getOrganizer();
						if ($organizer)
						{
							$emailtmp = $organizer->getEmail();
							print ' - organizer = '.$emailtmp;
							if ($emailtmp)
							{
								// Get user
								$sql = "SELECT u.rowid FROM ".MAIN_DB_PREFIX."user as u WHERE email = '".$db->escape($emailtmp)."'";
								$result = $db->query($sql);
								if ($result)
								{
									$obj = $db->fetch_object($result);
									if ($obj)
									{
										$tmpid = $obj->rowid;
										//$userstatic->fetch($tmpid)
										$object->userassigned[$tmpid]=array('id'=>$tmpid);
										print $tmpid;
									}
								}
								else
								{
									dol_print_error($db);
									exit;
								}
							}
						}
						else	// If organizer not set, we take current user (this should no happened)
						{
							print 'errror: organizer not set';
							$object->userassigned[$fuser->id]=array('id'=>$fuser->id);
						}*/

						// Owner
						if ($dolibarr_user_id) {		// If owner were saved and found into google event
							$object->userassigned=array();
							$object->userassigned[$dolibarr_user_id]=array('id'=>$dolibarr_user_id, 'transparency'=>$object->transparency);
							$object->userownerid=$dolibarr_user_id;
						} else // If owner were not saved, we keep old one
						{
							$object->userassigned=array();
							//$object->userownerid=$fuser->id;
							$object->userassigned[$object->userownerid]=array('id'=>$object->userownerid, 'transparency'=>$object->transparency);
						}

						// Attendees
						$attendees = $event->getAttendees();
						if (! empty($attendees)) {
							foreach ($attendees as $attendee) {
								//var_dump($attendee);
								$emailtmp=$attendee->getEmail();
								if ($emailtmp) {
									// Get user
									$sql = "SELECT u.rowid FROM ".MAIN_DB_PREFIX."user as u WHERE email = '".$db->escape($emailtmp)."'";
									$result = $db->query($sql);
									if ($result) {
										$obj = $db->fetch_object($result);
										if ($obj) {
											$tmpid = $obj->rowid;
											//$userstatic->fetch($tmpid)
											$object->userassigned[$tmpid]=array('id'=>$tmpid, 'transparency'=>$object->transparency);
										}
									} else {
										dol_print_error($db);
										exit;
									}
								}
							}
						}

						if ($status == 'cancelled') {
							$conf->global->GOOGLE_DELETEONDOL_WHEN_DELETEDONGOOGLE=1;
							if (! empty($conf->global->GOOGLE_DELETEONDOL_WHEN_DELETEDONGOOGLE)) {
								$result=$object->delete(1);
								if ($result > 0) {
									$nbdeleted++;
								} else {
									$nberror++;
								}
							} else {
								$nbnotdeleted++;
							}
						} else {
							$result=$object->update($fuser, 1);
							if ($result > 0) {
								$nbupdated++;
							} else {
								$nberror++;
							}
						}
					} else // Not found into dolibarr
					{
						//$event = new Google_Service_Calendar_Event();

						// Create into dolibarr
						$ds=$event->getStart();
						$de=$event->getEnd();
						if ($ds) $dates=$ds->getDate();
						if ($de) $datee=$de->getDate();
						if ($ds) $datest=$ds->getDateTime();
						if ($de) $dateet=$de->getDateTime();

						$object->punctual=0;

						if ($datest) {
							// $datest = '2015-07-29T10:00:00+02:00' means 2015-07-29T08:00:00
							// We remove the TZ from string. tz will be managed by the ($tzfix*3600)
							if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(\-|\+)([0-9]{2})/i', $datest, $reg)) {
								$datest = $reg[1].'-'.$reg[2].'-'.$reg[3].'T'.$reg[4].':'.$reg[5].':'.$reg[6];
								$tzs=(int) ($reg[7].$reg[8]);
							}
							if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(\-|\+)([0-9]{2})/i', $dateet, $reg)) {
								$dateet = $reg[1].'-'.$reg[2].'-'.$reg[3].'T'.$reg[4].':'.$reg[5].':'.$reg[6];
								$tze=(int) ($reg[7].$reg[8]);
							}
							$object->datep=(dol_stringtotime($datest, 1) - ($tzs*3600) - ($tzfix*3600));
							$object->datef=(dol_stringtotime($dateet, 1) - ($tze*3600) - ($tzfix*3600));
							$object->fulldayevent=0;
							if ($object->datep == $object->datef) $object->punctual=1;
							//print dol_print_date($object->datep, 'dayhour', 'tzserver');
						} elseif ($dates) {
							$object->datep=(dol_stringtotime($dates, 0));
							$object->datef=(dol_stringtotime($datee, 0) - 1);
							$object->fulldayevent=1;
						}
						$object->type_code='AC_OTH';
						$object->code='AC_OTH';
						$object->label=$event->getSummary();

						$transtmp=$event->getTransparency();
						$object->transparency=((empty($transtmp) || $transtmp == 'opaque')?1:0);		// null or 'opaque' = busy = transparency to 1, 'transparent' = available
						$object->priority=0;
						$object->percentage=(empty($conf->global->GOOGLE_NEW_EVENT_FROM_GOOGLE_STATUS)?-1:$conf->global->GOOGLE_NEW_EVENT_FROM_GOOGLE_STATUS);
						$object->location=$event->getLocation();
						//$object->socid=$obj->fk_soc;
						//$object->contactid=$obj->fk_contact;
						$object->note=trim(preg_replace('/'.preg_quote('-----+++++-----', '/').'.*$/s', '', $event->getDescription()));
						$object->note_public=trim(preg_replace('/'.preg_quote('-----+++++-----', '/').'.*$/s', '', $event->getDescription()));
						$object->note_private=trim(preg_replace('/'.preg_quote('-----+++++-----', '/').'.*$/s', '', $event->getDescription()));

						// Organizer
						/*$organizer=$event->getOrganizer();
						if ($organizer)
						{
							$emailtmp = $organizer->getEmail();
							print ' - organizer = '.$emailtmp;
							if ($emailtmp)
							{
								// Get user
								$sql = "SELECT u.rowid FROM ".MAIN_DB_PREFIX."user as u WHERE email = '".$db->escape($emailtmp)."'";
								$result = $db->query($sql);
								if ($result)
								{
									$obj = $db->fetch_object($result);
									if ($obj)
									{
										$tmpid = $obj->rowid;
										//$userstatic->fetch($tmpid)
										$object->userassigned[$tmpid]=array('id'=>$tmpid);
										print $tmpid;
									}
								}
								else
								{
									dol_print_error($db);
									exit;
								}
							}
						}
						else	// If organizer not set, we take current user (this should no happened)
						{
							print 'errror: organizer not set';
							$object->userassigned[$user->id]=array('id'=>$user->id);
						}*/

						// Owner
						if ($dolibarr_user_id) {		// If owner were saved
							$object->userassigned=array();
							$object->userassigned[$dolibarr_user_id]=array('id'=>$dolibarr_user_id, 'transparency'=>$object->transparency);
							$object->userownerid=$dolibarr_user_id;
						} else // If owner were not saved, we keep old one
						{
							$object->userassigned=array();
							$object->userownerid=$fuser->id;
							$object->userassigned[$object->userownerid]=array('id'=>$object->userownerid, 'transparency'=>$object->transparency);
						}

						// Attendees
						$attendees = $event->getAttendees();
						if (! empty($attendees)) {
							foreach ($attendees as $attendee) {
								$emailtmp=$attendee->getEmail();
								if ($emailtmp) {
									// Get user
									$sql = "SELECT u.rowid FROM ".MAIN_DB_PREFIX."user as u WHERE email = '".$db->escape($emailtmp)."'";
									$result = $db->query($sql);
									if ($result) {
										$obj = $db->fetch_object($result);
										if ($obj) {
											$tmpid = $obj->rowid;
											//$userstatic->fetch($tmpid)
											$object->userassigned[$tmpid]=array('id'=>$tmpid, 'transparency'=>$object->transparency);
										}
									} else {
										dol_print_error($db);
										exit;
									}
								}
							}
						}

						//var_dump($object->userassigned);
						if ($status == 'cancelled') {
							// It's ok. We wont' add it
							$nbalreadydeleted++;
						} else {
							$result=$object->create($fuser, 1);
							if ($result > 0) {
								$ret='google:'.$event->getId();
								$object->update_ref_ext($ret);	// This is to store ref_ext into Dolibarr to allow updates

								// Update dolibarr_id and dolibarr_user_id into Google record
								if (empty($extendedProperties)) {
									$extendedProperties=new Google_Service_Calendar_EventExtendedProperties();
									$extendedProperties->setPrivate(array('dolibarr_id'=>$object->id.'/event','dolibarr_user_id'=>$object->userownerid));
									$event->setExtendedProperties($extendedProperties);
								} else {
									$arraytmp=$extendedProperties->getPrivate();
									$arraytmp['dolibarr_id']=$object->id.'/event';
									$extendedProperties->setPrivate($arraytmp);
									$event->setExtendedProperties($extendedProperties);
								}

								/*
								The source can only be modified by creator of event. It may differs from account used to login if event is a shared event

								// Define $urlwithroot
								$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
								$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
								//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

								$urlevent=$urlwithroot.'/comm/action/card.php?id='.$object->id;
								$urlicon=$urlwithroot.'/favicon.ico';

								$source=new Google_Service_Calendar_EventSource();
								$source->setTitle($conf->global->MAIN_APPLICATION_TITLE);
								$source->setUrl($urlevent);

								$event->setSource($source);
								*/

								dol_syslog("Update google record to set the extended property");
								$updatedEvent = $service->events->update($userlogin, $event->getId(), $event);

								$nbinserted++;
							} else {
								dol_print_error('', $object->error);
								$nberror++;
							}
						}
					}

					unset($object);
				}

				$pageToken = $events->getNextPageToken();   // If $pageToken is set, it means we have more result than $maxResults = $max but we get only $maxResults
				if ($pageToken && ($i < $max)) {
					$optParams['pageToken'] = $pageToken;
					$events = $service->events->listEvents($userlogin, $optParams);    // Load next page of results
				} else {
					break; // exit loop
				}
			}
		} catch (Exception $e) {
			$errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	return array('nbinserted'=>$nbinserted, 'nbupdated'=>$nbupdated, 'nbdeleted'=>$nbdeleted, 'nbalreadydeleted'=>$nbalreadydeleted, 'nbnotdeleted'=>$nbnotdeleted, 'errors'=>$errors);
}
