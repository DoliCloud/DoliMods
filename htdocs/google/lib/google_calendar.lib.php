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

dol_include_once("/google/lib/google.lib.php");
$res=dol_include_once('/google/includes/google-api-php-client/autoload.php');

if (! class_exists('Google_Client')) dol_print_error('','Failed to load library file /nltechno/google/includes/google-api-php-client/autoload.php');

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
 * @return				service
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
 * Get service
 *
 * @param	string			$client_id					Client ID (Example: '258042696143-s9klbbpj13fb40ac8k5qjajn4e9o1c49.apps.googleusercontent.com'). Not used.
 * @param	string			$service_account_name		Service account name (Example: '258042696143-s9klbbpj13fb40ac8k5qjajn4e9o1c49@developer.gserviceaccount.com')
 * @param	string			$key_file_location			Key file location (Example: 'API Project-69e4673ea29e.p12')
 * @param	int				$force_do_not_use_session	1=Do not get token from essions
 * @return	array|string								Error message or array with token
 */
function getTokenFromServiceAccount($client_id, $service_account_name, $key_file_location, $force_do_not_use_session=false)
{
	global $conf;

	if (empty($service_account_name)) return 'ErrorNoServiceAccountName';
	if (empty($key_file_location) || ! file_exists($key_file_location)) return 'ErrorKeyFileNotFound';

	$client = new Google_Client();
	$client->setApplicationName("Dolibarr");
	$client->setClassConfig('Google_Cache_File', 'directory', $conf->google->dir_temp);		// Force dir if cache used is Google_Cache_File

	/************************************************
	  If we have an access token, we can carry on.
	  Otherwise, we'll get one with the help of an
	  assertion credential. In other examples the list
	  of scopes was managed by the Client, but here
	  we have to list them manually. We also supply
	  the service account
	 ************************************************/
	if (empty($force_do_not_use_session) && isset($_SESSION['service_token']))
	{
		dol_syslog("Get service token from session. service_token=".$_SESSION['service_token']);
		$client->setAccessToken($_SESSION['service_token']);
	}

	dol_syslog("getTokenFromServiceAccount service_account_name=".$service_account_name." key_file_location=".$key_file_location." force_do_not_use_session=".$force_do_not_use_session, LOG_DEBUG);
	$key = file_get_contents($key_file_location);
	$cred = new Google_Auth_AssertionCredentials(
	    $service_account_name,
	    array('https://www.googleapis.com/auth/calendar','https://www.googleapis.com/auth/calendar.readonly'),
	    $key
	);

	$client->setAssertionCredentials($cred);

	try {
		$checktoken=$client->getAuth()->isAccessTokenExpired();
		if ($checktoken)
		{
			dol_syslog("getTokenFromServiceAccount token seems to be expired, we refresh it", LOG_DEBUG);
			$client->getAuth()->refreshTokenWithAssertion($cred);
		}
	}
	catch(Exception $e)
	{
		return $e->getMessage();
	}

	$_SESSION['service_token'] = $client->getAccessToken();

	dol_syslog("Return client name = ".$client->getApplicationName()." service_token = ".$_SESSION['service_token']);
	return array('client'=>$client, 'service_token'=>$_SESSION['service_token']);
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
function createEvent($client, $object, $login='primary')
{
	global $conf, $db, $trans;
	global $dolibarr_main_url_root;
	global $user;

	$event = new Google_Service_Calendar_Event();
	$start = new Google_Service_Calendar_EventDateTime();
	$end = new Google_Service_Calendar_EventDateTime();

	$tzfix=0;
	if (! empty($conf->global->GOOGLE_CAL_TZ_FIX) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX;
    if (empty($object->fulldayevent))
    {
        $startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayhourrfc",'gmt');
        $endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef),"dayhourrfc",'gmt');

        $start->setDateTime($startTime);	// '2011-06-03T10:00:00.000-07:00'
		$end->setDateTime($endTime);		// '2011-06-03T10:25:00.000-07:00'
    }
    else
    {
        $startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayrfc");
        $endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef) + 3600*24,"dayrfc");	// For fulldayevent, into XML data, endTime must be day after

        $start->setDate($startTime);	// '2011-06-03'
		$end->setDate($endTime);		// '2011-06-03'
    }

	$event->setStart($start);
	$event->setEnd($end);

	$event->setSummary(trim($object->label));
	$event->setLocation($object->location);
	$event->setDescription(dol_string_nohtmltag($object->note, 0));

	$extendedProperties=new Google_Service_Calendar_EventExtendedProperties();
	$extendedProperties->setPrivate(array('dolibarr_id'=>$object->id.'/event'));
	$event->setExtendedProperties($extendedProperties);

	// Transparency 0=available, 1=busy
	$transparency=isset($object->userassigned[$user->id]['transparency'])?$object->userassigned[$user->id]['transparency']:0;
	if ($transparency > 0) $event->setTransparency("opaque");
	else $event->setTransparency("transparent");

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

	/*$gadget=new Google_Service_Calendar_EventGadget();
	$gadget->setLink($urlevent);
	$gadget->setIconLink($urlicon);
	$event->setGadget($gadget);*/

	$event->setStatus('confirmed');		// tentative, cancelled
	$event->setVisibility('default');	// default, public, private (view by attendees only), confidential (do not use)

	$event->setGuestsCanModify(false);
	$event->setGuestsCanInviteOthers(true);
	$event->setGuestsCanSeeOtherGuests(true);

	$attendees = array();
	foreach($object->userassigned as $key => $val)
	{
		if ($key == $user->id) continue;	// ourself, not an attendee
		$fuser=new User($db);
		$fuser->fetch($key);
		if ($fuser->id > 0 && $fuser->email)
		{
			$attendee = new Google_Service_Calendar_EventAttendee();
			$attendee->setEmail($fuser->email);
			$attendees[]=$attendee;
		}
	}
	$event->attendees = $attendees;

	dol_syslog("createEvent for login=".$login.", label=".$object->label.", startTime=".$startTime.", endTime=".$endTime, LOG_DEBUG);

	try {
		$service = new Google_Service_Calendar($client['client']);

		$createdEvent = $service->events->insert($login, $event);

		$ret=$createdEvent->getId();
		dol_syslog("createEvent Id=".$ret, LOG_DEBUG);
	}
	catch(Exception $e)
	{
		dol_syslog("error ".$e->getMessage(), LOG_ERR);
		return 'ERROR '.$e->getMessage();
	}

	return $ret;
}


/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param  	array					$client   		Service array with authenticated client object (Not used if $service is provided)
 * @param  	string   				$eventId        The event ID string
 * @param  	string					$object	   		Source object into Dolibarr
 * @param  	string					$login			CalendarId (login google or 'primary')
 * @param	Google_Service_Calendar	$service		Object service (will be created if not provided)
 * @return
 */
function updateEvent($client, $eventId, $object, $login='primary', $service=null)
{
	global $conf;
	global $dolibarr_main_url_root;
	global $user;

	//$gdataCal = new Zend_Gdata_Calendar($client);

	$oldeventId=$eventId;
	if (preg_match('/google\.com\/.*\/([^\/]+)$/',$eventId,$reg))
	{
		$oldeventId=$reg[1];
	}
	if (preg_match('/google:([^\/]+)$/',$eventId,$reg))
	{
		$oldeventId=$reg[1];
	}

	try {
		if (empty($service)) $service = new Google_Service_Calendar($client['client']);

		//$event = new Google_Service_Calendar_Event();
		$event = $service->events->get($login, $oldeventId);
		if (is_object($event)) dol_syslog("updateEvent get old record id=".$event->getId()." found into google calendar", LOG_DEBUG);

		// Set new value of events
		$start = new Google_Service_Calendar_EventDateTime();
		$end = new Google_Service_Calendar_EventDateTime();

		$tzfix=0;
		if (! empty($conf->global->GOOGLE_CAL_TZ_FIX) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX;
	    if (empty($object->fulldayevent))
	    {
	        $startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayhourrfc",'gmt');
	        $endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef),"dayhourrfc",'gmt');

	        $start->setDateTime($startTime);	// '2011-06-03T10:00:00.000-07:00'
			$end->setDateTime($endTime);		// '2011-06-03T10:25:00.000-07:00'
	    }
	    else
	    {
	        $startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayrfc");
	        $endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef) + 3600*24,"dayrfc");	// For fulldayevent, into XML data, endTime must be day after

	        $start->setDate($startTime);	// '2011-06-03'
			$end->setDate($endTime);		// '2011-06-03'
	    }
		$event->setStart($start);
		$event->setEnd($end);

		$event->setSummary(trim($object->label));
		$event->setLocation($object->location);
		$event->setDescription(dol_string_nohtmltag($object->note, 0));

		/* Disabled for update
		$extendedProperties=new Google_Service_Calendar_EventExtendedProperties();
		$extendedProperties->setPrivate(array('dolibarr_id'=>$object->id.'/event'));
		$event->setExtendedProperties($extendedProperties);
		*/

		// Transparency 0=available, 1=busy
		$transparency=isset($object->userassigned[$user->id]['transparency'])?$object->userassigned[$user->id]['transparency']:0;
		if ($transparency > 0) $event->setTransparency("opaque");
		else $event->setTransparency("transparent");

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

		/*$gadget=new Google_Service_Calendar_EventGadget();
		$gadget->setLink($urlevent);
		$gadget->setIconLink($urlicon);
		$event->setGadget($gadget);*/

		$event->setStatus('confirmed');		// tentative, cancelled
		$event->setVisibility('default');	// default, public, private (view by attendees only), confidential (do not use)

		$event->setGuestsCanModify(false);
		$event->setGuestsCanInviteOthers(true);
		$event->setGuestsCanSeeOtherGuests(true);

		$attendees = array();
		foreach($object->userassigned as $key => $val)
		{
			if ($key == $user->id) continue;	// ourself, not an attendee
			$fuser=new User($db);
			$fuser->fetch($key);
			if ($fuser->id > 0 && $fuser->email)
			{
				$attendee = new Google_Service_Calendar_EventAttendee();
				$attendee->setEmail($fuser->email);
				$attendees[]=$attendee;
			}
		}
		$event->attendees = $attendees;

		dol_syslog("updateEvent Update record on Google calendar with login=".$login.", id=".$oldeventId, LOG_DEBUG);

		$updatedEvent = $service->events->update($login, $oldeventId, $event);

		// Print the updated date.
		//echo $updatedEvent->getUpdated();

		$ret = 1;
	}
	catch(Exception $e)
	{
		dol_syslog("updateEvent error in getting or updating record: ".$e->getMessage(), LOG_WARNING);

		return 'ERROR '.$e->getMessage();
	}

	return $ret;
}

/**
 * Deletes the event specified by retrieving the atom entry object
 * and calling Zend_Feed_EntryAtom::delete() method.  This is for
 * example purposes only, as it is inefficient to retrieve the entire
 * atom entry only for the purposes of deleting it.
 *
 * @param  	array					$client   		Service array with authenticated client object (Not used if $service is provided)
 * @param  	string  				$eventId        The event ID string
 * @param  	string					$login			CalendarId (login google or 'primary')
 * @param	Google_Service_Calendar	$service		Object service (will be created if not provided)
 * @return 	void
 */
function deleteEventById ($client, $eventId, $login='primary', $service=null)
{
	$oldeventId=$eventId;
	if (preg_match('/google\.com\/.*\/([^\/]+)$/',$eventId,$reg))
	{
		$oldeventId=$reg[1];
	}
	if (preg_match('/google:([^\/]+)$/',$eventId,$reg))
	{
		$oldeventId=$reg[1];
	}

	dol_syslog("deleteEventById Delete old record on Google calendar with login=".$login.", id=".$oldeventId, LOG_DEBUG);

	try {
		if (empty($service)) $service = new Google_Service_Calendar($client['client']);

		$service->events->delete($login, $oldeventId);

		$ret = 1;
	}
	catch(Exception $e)
	{
		dol_syslog("deleteEventById error in getting or deleting old record: ".$e->getMessage(), LOG_WARNING);

		return 'ERROR '.$e->getMessage();
	}

	return $ret;
}


/**
 * Complete $object to change ->label and ->note before pushing event to Google Calendar.
 *
 * @param 	Object		$object		Object event to complete
 * @param	Translate	$langs		Language object
 * @return	void
 */
function google_complete_label_and_note(&$object, $langs)
{
	global $conf, $db;
	global $dolibarr_main_url_root;

	$eventlabel = trim($object->label);
	// Define $urlwithroot
	$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
	$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
	//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current
	if (($object->socid > 0 || (! empty($object->thirdparty->id) && $object->thirdparty->id > 0)) && empty($conf->global->GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE)) {
		$thirdparty = new Societe($db);
		$result=$thirdparty->fetch($object->socid?$object->socid:$object->thirdparty->id);
		if ($result > 0)
		{
			$eventlabel .= ' - '.$thirdparty->name;
			$tmpadd=$thirdparty->getFullAddress(0);
			if ($tmpadd && empty($conf->global->GOOGLE_DISABLE_ADD_ADDRESS_INTO_DESC)) $object->note.="\n\n".$thirdparty->name."\n".$thirdparty->getFullAddress(1)."\n";
			if (! empty($thirdparty->phone)) $object->note.="\n".$langs->trans("Phone").': '.$thirdparty->phone;
			if (! empty($thirdparty->phone_pro)) $object->note.="\n".$langs->trans("Phone").': '.$thirdparty->phone_pro;
			if (! empty($thirdparty->fax)) $object->note.="\n".$langs->trans("Fax").': '.$thirdparty->fax;

			$urltoelem=$urlwithroot.'/societe/soc.ph?socid='.$thirdparty->id;
			$object->note.="\n".$langs->trans("LinkToThirdParty").': '.$urltoelem;
		}
	}
	if (($object->contactid > 0 || (! empty($object->contact->id) && $object->contact->id > 0)) && empty($conf->global->GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT)) {
		$contact = new Contact($db);
		$result=$contact->fetch($object->contactid?$object->contactid:$object->contact->id);
		if ($result > 0)
		{
			$eventlabel .= ' - '.$contact->getFullName($langs, 1);
			$tmpadd=$contact->getFullAddress(0);
			if ($tmpadd && empty($conf->global->GOOGLE_DISABLE_ADD_ADDRESS_INTO_DESC)) $object->note.="\n\n".$contact->name."\n".$contact->getFullAddress(1)."\n";
			if (! empty($contact->phone)) $object->note.="\n".$langs->trans("Phone").': '.$contact->phone;
			if (! empty($contact->phone_pro)) $object->note.="\n".$langs->trans("Phone").': '.$contact->phone_pro;
			if (! empty($contact->phone_perso)) $object->note.="\n".$langs->trans("PhonePerso").': '.$contact->phone_perso;
			if (! empty($contact->phone_mobile)) $object->note.="\n".$langs->trans("PhoneMobile").': '.$contact->phone_mobile;
			if (! empty($contact->fax)) $object->note.="\n".$langs->trans("Fax").': '.$contact->fax;

			$urltoelem=$urlwithroot.'/contact/fiche.ph?id='.$contact->id;
			$object->note.="\n".$langs->trans("LinkToContact").': '.$urltoelem;
		}
	}
	$object->label = $eventlabel;
}
