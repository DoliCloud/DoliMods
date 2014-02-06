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
 *
 * Rem:
 * To get event:  https://www.google.com/calendar/feeds/default/private/full?start-min=2013-03-16T00:00:00&start-max=2014-03-24T23:59:59
 * To get list of calendar: https://www.google.com/calendar/feeds/default/allcalendars/full
 */

$path = dol_buildpath('/google/includes/zendgdata');
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

/**
 * @var string Location of AuthSub key file.  include_path is used to find this
 */
$_authSubKeyFile = null; // Example value for secure use: 'mykey.pem'

/**
 * @var string Passphrase for AuthSub key file.
 */
$_authSubKeyFilePassphrase = null;



/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using the ClientLogin credentials supplied.
 *
 * @param  	string 	$user 		The username, in e-mail address format, to authenticate
 * @param  	string 	$pass 		The password for the user specified
 * @param	string	$service	The service to use (cp = calendar, cl=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list)
 * @return 	Zend_Http_Client
 */
function getClientLoginHttpClient($user, $pass, $service)
{
	$client=null;

	try {
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
	}
	catch(Exception $e)
	{
       	// DOL_LDR_CHANGE
       	global $conf;
       	if (! empty($conf->global->MODULE_GOOGLE_DEBUG))
        {
           	file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_client_login.log", $e->getMessage()." user=".$user." pass=".preg_replace('/./','*',$pass)." service=".$service);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_client_login.log", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
        }
	}
	return $client;
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an event
 * in the user's calendar.  The calendar is retrieved using the magic cookie
 * which allows read-only access to private calendar data using a special token
 * available from within the Calendar UI.
 *
 * @param  string $user        The username or address of the calendar to be retrieved.
 * @param  string $magicCookie The magic cookie token
 * @return void
 */
function outputCalendarMagicCookie($user, $magicCookie)
{
	$gdataCal = new Zend_Gdata_Calendar();
	$query = $gdataCal->newEventQuery();
	$query->setUser($user);
	$query->setVisibility('private-' . $magicCookie);
	$query->setProjection('full');
	$eventFeed = $gdataCal->getCalendarEventFeed($query);
	echo "<ul>\n";
	foreach ($eventFeed as $event) {
		echo "\t<li>" . $event->title->text . "</li>\n";
		$sl = $event->getLink('self')->href;
	}
	echo "</ul>\n";
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing a
 * calendar in the authenticated user's calendar list.
 *
 * @param  Zend_Http_Client $client The authenticated client object
 * @return void
 */
function outputCalendarList($client)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$calFeed = $gdataCal->getCalendarListFeed();
	echo "<h1>" . $calFeed->title->text . "</h1>\n";
	echo "<ul>\n";
	foreach ($calFeed as $calendar) {
		echo "\t<li>" . $calendar->title->text . "</li>\n";
	}
	echo "</ul>\n";
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an
 * event on the authenticated user's calendar.  Includes the start time and
 * event ID in the output.  Events are ordered by starttime and include only
 * events occurring in the future.
 *
 * @param  Zend_Http_Client $client The authenticated client object
 * @return void
 */
function outputCalendar($client, $user='default', $visibility='private', $projection='full')
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$query = $gdataCal->newEventQuery();

	$query->setUser($user);
	$query->setVisibility($visibility);
	$query->setProjection($projection);
	$query->setOrderby('starttime');
	//$query->setFutureevents(true);

	$eventFeed = $gdataCal->getCalendarEventFeed($query);
	// option 2
	// $eventFeed = $gdataCal->getCalendarEventFeed($query->getQueryUrl());
	echo "<ul>\n";
	foreach ($eventFeed as $event) {
		echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
		// Zend_Gdata_App_Extensions_Title->__toString() is defined, so the
		// following will also work on PHP >= 5.2.0
		//echo "\t<li>" . $event->title .  " (" . $event->id . ")\n";
		echo "\t\t<ul>\n";
		foreach ($event->when as $when) {
			echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
		}
		echo "\t\t</ul>\n";
		echo "\t</li>\n";
	}
	echo "</ul>\n";
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an
 * event on the authenticated user's calendar which occurs during the
 * specified date range.
 *
 * To query for all events occurring on 2006-12-24, you would query for
 * a startDate of '2006-12-24' and an endDate of '2006-12-25' as the upper
 * bound for date queries is exclusive.  See the 'query parameters reference':
 * http://code.google.com/apis/gdata/calendar.html#Parameters
 *
 * @param  Zend_Http_Client $client    The authenticated client object
 * @param  string           $startDate The start date in YYYY-MM-DD format
 * @param  string           $endDate   The end date in YYYY-MM-DD format
 * @return void
 */
function outputCalendarByDateRange($client, $startDate, $endDate)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$query = $gdataCal->newEventQuery();
	$query->setUser('default');
	$query->setVisibility('private');
	$query->setProjection('full');
	$query->setOrderby('starttime');
	$query->setStartMin($startDate);
	$query->setStartMax($endDate);
	$eventFeed = $gdataCal->getCalendarEventFeed($query);
	echo "<ul>\n";
	foreach ($eventFeed as $event) {
		echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
		echo "\t\t<ul>\n";
		foreach ($event->when as $when) {
			echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
		}
		echo "\t\t</ul>\n";
		echo "\t</li>\n";
	}
	echo "</ul>\n";
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an
 * event on the authenticated user's calendar which matches the search string
 * specified as the $fullTextQuery parameter
 *
 * @param  Zend_Http_Client $client        The authenticated client object
 * @param  string           $fullTextQuery The string for which you are searching
 * @return void
 */
function outputCalendarByFullTextQuery($client, $fullTextQuery)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$query = $gdataCal->newEventQuery();
	$query->setUser('default');
	$query->setVisibility('private');
	$query->setProjection('full');
	$query->setQuery($fullTextQuery);
	$eventFeed = $gdataCal->getCalendarEventFeed($query);
	echo "<ul>\n";
	foreach ($eventFeed as $event) {
		echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
		echo "\t\t<ul>\n";
		foreach ($event->when as $when) {
			echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
			echo "\t\t</ul>\n";
			echo "\t</li>\n";
		}
	}
	echo "</ul>\n";
}

/**
 * Creates an event on the authenticated user's default calendar with the
 * specified event details.
 *
 * @param  Zend_Http_Client $client    The authenticated client object
 * @param  string			$object	   Source object into Dolibarr
 * @return string The ID URL for the event.
 */
function createEvent($client, $object)
{
    // More examples on http://code.google.com/intl/fr/apis/calendar/data/1.0/developers_guide_php.html
	global $conf;

	$gc = new Zend_Gdata_Calendar($client, 'Dolibarr');

	$newEntry = $gc->newEventEntry();
	$newEntry->title = $gc->newTitle(trim($object->label));
	$newEntry->where  = array($gc->newWhere($object->location));

	$newEntry->content = $gc->newContent(dol_string_nohtmltag($object->note, 0));
	$newEntry->content->type = 'text';

	$tzfix=0;
	if (! empty($conf->global->GOOGLE_CAL_TZ_FIX) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX;

	$when = $gc->newWhen();
    if (empty($object->fulldayevent))
    {
        $when->startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayhourrfc",'gmt');
        $when->endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef),"dayhourrfc",'gmt');
    }
    else
    {
        $when->startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayrfc");
        $when->endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef) + 3600*24,"dayrfc");	// For fulldayevent, into XML data, endTime must be day after
    }
    $newEntry->when = array($when);

	dol_syslog("startTime=".$when->startTime." endTime=".$when->endTime);
	// Add Dolibarr action id into Google event properties
	$createdEntry = $gc->insertEvent($newEntry);
	$gid=basename($createdEntry->getId());
    //print $gid." $object->id";
    //$event = getEvent($client, $gid);
    //var_dump($event->title->text);
    if ($gid && $object->id) addExtendedProperty($client,$gid,'dol_id',$object->id);

	return $createdEntry->getId();    // Return full URL with id
}

/**
 * Creates an event on the authenticated user's default calendar using
 * the specified QuickAdd string.
 *
 * @param  Zend_Http_Client $client       The authenticated client object
 * @param  string           $quickAddText The QuickAdd text for the event
 * @return string The ID URL for the event
 */
/*
function createQuickAddEvent ($client, $quickAddText)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$event = $gdataCal->newEventEntry();
	$event->content = $gdataCal->newContent($quickAddText);
	$event->quickAdd = $gdataCal->newQuickAdd(true);

	$newEvent = $gdataCal->insertEvent($event);
	return $newEvent->id->text;
}
*/
/**
 * Creates a new web content event on the authenticated user's default
 * calendar with the specified event details. For simplicity, the event
 * is created as an all day event and does not include a description.
 *
 * @param  Zend_Http_Client $client    The authenticated client object
 * @param  string           $title     The event title
 * @param  string           $startDate The start date of the event in YYYY-MM-DD format
 * @param  string           $endDate   The end time of the event in HH:MM 24hr format
 * @param  string           $icon      URL pointing to a 16x16 px icon representing the event.
 * @param  string           $url       The URL containing the web content for the event.
 * @param  string           $height    The desired height of the web content pane.
 * @param  string           $width     The desired width of the web content pane.
 * @param  string           $type      The MIME type of the web content.
 * @return string The ID URL for the event.
 */
/*
function createWebContentEvent ($client, $title, $startDate, $endDate, $icon, $url, $height = '120', $width = '276', $type = 'image/gif')
{
	$gc = new Zend_Gdata_Calendar($client);
	$newEntry = $gc->newEventEntry();
	$newEntry->title = $gc->newTitle(trim($title));

	$when = $gc->newWhen();
	$when->startTime = $startDate;
	$when->endTime = $endDate;
	$newEntry->when = array($when);

	$wc = $gc->newWebContent();
	$wc->url = $url;
	$wc->height = $height;
	$wc->width = $width;

	$wcLink = $gc->newLink();
	$wcLink->rel = "http://schemas.google.com/gCal/2005/webContent";
	$wcLink->title = $title;
	$wcLink->type = $type;
	$wcLink->href = $icon;

	$wcLink->webContent = $wc;
	$newEntry->link = array($wcLink);

	$createdEntry = $gc->insertEvent($newEntry);
	return $createdEntry->id->text;
}
*/

/**
 * Creates a recurring event on the authenticated user's default calendar with
 * the specified event details.
 *
 * @param  Zend_Http_Client $client    The authenticated client object
 * @param  string           $title     The event title
 * @param  string           $desc      The detailed description of the event
 * @param  string           $where
 * @param  string           $recurData The iCalendar recurring event syntax (RFC2445)
 * @return void
 */
function createRecurringEvent ($client, $title, $desc, $where, $recurData = null)
{
	$gc = new Zend_Gdata_Calendar($client);
	$newEntry = $gc->newEventEntry();

	$newEntry->title			= $gc->newTitle(trim($title));
	$newEntry->where			= array($gc->newWhere($where));
	$newEntry->content			= $gc->newContent($desc);
	$newEntry->content->type	= 'text';

	/**
	 * Due to the length of this recurrence syntax, we did not specify
	 * it as a default parameter value directly
	 */
	if ($recurData == null) {
		$recurData =
        "DTSTART;VALUE=DATE:20070501\r\n" .
        "DTEND;VALUE=DATE:20070502\r\n" .
        "RRULE:FREQ=WEEKLY;BYDAY=Tu;UNTIL=20070904\r\n";
  }

  $newEntry->recurrence = $gc->newRecurrence($recurData);

  $gc->post($newEntry->saveXML());
}

/**
 * Returns an entry object representing the event with the specified ID.
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $eventId The event ID string
 * @return Zend_Gdata_Calendar_EventEntry|null if the event is found, null if it's not
 */
function getEvent($client, $eventId)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$query = $gdataCal->newEventQuery();
	$query->setUser('default');
	$query->setVisibility('private');
	$query->setProjection('full');
	$query->setEvent($eventId);

	try {
		$eventEntry = $gdataCal->getCalendarEventEntry($query);
		return $eventEntry;
	} catch (Zend_Gdata_App_Exception $e) {
		dol_syslog("Error during getCalendarEventEntry", LOG_ERR);
		return null;
	}
}

/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param  Zend_Http_Client $client   The authenticated client object
 * @param  string           $eventId  The event ID string
 * @param  string           $newTitle The new title to set on this event
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function updateEvent($client, $eventId, $object)
{
	global $conf;

	$gdataCal = new Zend_Gdata_Calendar($client);

	$eventOld = getEvent($client, $eventId);
	if ($eventOld)
	{
	    //echo "Old title: " . $eventOld->title->text . " -> ".$object->label."<br>\n"; exit;
	    $eventOld->title = $gdataCal->newTitle($object->label);
	    $eventOld->where = array($gdataCal->newWhere($object->location));

	    $eventOld->content = $gdataCal->newContent(dol_string_nohtmltag($object->note, 0));
	    $eventOld->content->type = 'text';

	    $tzfix=0;
	    if (! empty($conf->global->GOOGLE_CAL_TZ_FIX) && is_numeric($conf->global->GOOGLE_CAL_TZ_FIX)) $tzfix=$conf->global->GOOGLE_CAL_TZ_FIX;

	    $when = $gdataCal->newWhen();
	    if (empty($object->fulldayevent))
	    {
	        $when->startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayhourrfc",'gmt');
	        $when->endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef),"dayhourrfc",'gmt');
	    }
	    else
	    {
	        $when->startTime = dol_print_date(($tzfix*3600) + $object->datep,"dayrfc");
	        $when->endTime = dol_print_date(($tzfix*3600) + (empty($object->datef)?$object->datep:$object->datef) + 3600*24,"dayrfc");	// For fulldayevent, into XML data, endTime must be day after
	    }
	    $eventOld->when = array($when);

	    dol_syslog("startTime=".$when->startTime." endTime=".$when->endTime);
	    try {
			$eventOld->save();
		} catch (Zend_Gdata_App_Exception $e) {
			var_dump($e);
			return null;
		}
		//$eventNew = getEvent($client, $eventId);
		//echo "New title: " . $eventNew->title->text . "<br>\n";
		return $eventOld->getId();
	}
	else
	{
	    dol_syslog("Event with id ".$eventId." not found into Calendar. We must create it.");
	    return -1;
	}
}

/**
 * Adds an extended property to the event specified as a parameter.
 * An extended property is an arbitrary name/value pair that can be added
 * to an event and retrieved via the API.  It is not accessible from the
 * calendar web interface.
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $eventId The event ID string
 * @param  string           $name    The name of the extended property
 * @param  string           $value   The value of the extended property
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function addExtendedProperty ($client, $eventId, $name, $value)
{
	$gc = new Zend_Gdata_Calendar($client);
	if ($event = getEvent($client, $eventId)) {
		$extProp = $gc->newExtendedProperty($name, $value);
		$extProps = array_merge($event->extendedProperty, array($extProp));
		$event->extendedProperty = $extProps;
		$eventNew = $event->save();
		return $eventNew;
	} else {
		return null;
	}
}


/**
 * Adds a reminder to the event specified as a parameter.
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $eventId The event ID string
 * @param  integer          $minutes Minutes before event to set reminder
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function setReminder($client, $eventId, $minutes=15)
{
	$gc = new Zend_Gdata_Calendar($client);
	$method = "alert";
	if ($event = getEvent($client, $eventId)) {
		$times = $event->when;
		foreach ($times as $when) {
			$reminder = $gc->newReminder();
			$reminder->setMinutes($minutes);
			$reminder->setMethod($method);
			$when->reminders = array($reminder);
		}
		$eventNew = $event->save();
		return $eventNew;
	} else {
		return null;
	}
}

/**
 * Deletes the event specified by retrieving the atom entry object
 * and calling Zend_Feed_EntryAtom::delete() method.  This is for
 * example purposes only, as it is inefficient to retrieve the entire
 * atom entry only for the purposes of deleting it.
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $eventId The event ID string
 * @return void
 */
function deleteEventById ($client, $eventId)
{
	dol_syslog("deleteEventById ".$eventId);
    if ($eventOld = getEvent($client, $eventId))
    {
	    $eventOld->delete();
    }
}

/**
 * Deletes the event specified by calling the Zend_Gdata::delete()
 * method.  The URL is typically in the format of:
 * http://www.google.com/calendar/feeds/default/private/full/<eventId>
 *
 * @param  Zend_Http_Client $client The authenticated client object
 * @param  string           $url    The url for the event to be deleted
 * @return void
 */
function deleteEventByUrl ($client, $url)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$gdataCal->delete($url);
}


/**
 * Mass insert of several contacts into a google account
 *
 * @param 	array 	$gCals			Array of object ActionComm
 * @return	int						>0 if OK, 'error string' if error
 */
function insertGCalsEntries($gCals)
{
	global $conf;

	$maxBatchLength = 98; //Google doc says max 100 entries.
	$remainingCals = $gCals;
	while (count($remainingCals) > 0)
	{
		if (count($remainingCals) > $maxBatchLength) {
			$firstContacts = array_slice($remainingCals, 0, $maxBatchLength);
			$remainingCals = array_slice($remainingCals, $maxBatchLength);
		} else {
			$firstContacts = $remainingCals;
			$remainingCals = array();
		}

		foreach ($firstContacts as $gContact) {

		}

		/*
		$client_id='258042696143.apps.googleusercontent.com';
		$client_secret='HdmLOMStzB9MBbAjCr87gz27';
		$redirect_uri='http://localhost/dolibarrnew/custom/google/googlecallback.php';
		$url='https://accounts.google.com/o/oauth2/auth?client_id='.$client_id.'&redirect_uri='.urlencode($redirect_uri).'&scope=https://www.google.com/m8/feeds/&response_type=code';

		dol_include_once('/google/includes/google-api-php-client/src/Google_Client.php');
		dol_include_once('/google/includes/google-api-php-client/src/contrib/Google_CalendarService.php');

		$client = new Google_Client();
		$client->setApplicationName("Google Calendar PHP Starter Application");

		// Visit https://code.google.com/apis/console?api=calendar to generate your
		// client id, client secret, and to register your redirect uri.
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->setDeveloperKey('insert_your_developer_key');

		$cal = new Google_CalendarService($client);
		if (isset($_GET['logout'])) {
			unset($_SESSION['google_oauth_token']);
		}

		if (isset($_GET['code'])) {
			$client->authenticate($_GET['code']);
			$_SESSION['google_oauth_token'] = $client->getAccessToken();
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		}

		if (isset($_SESSION['google_oauth_token'])) {
			$client->setAccessToken($_SESSION['google_oauth_token']);
		}

		if ($client->getAccessToken()) {
			$calList = $cal->calendarList->listCalendarList();
			print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";

			$_SESSION['google_oauth_token'] = $client->getAccessToken();
		} else {
			$authUrl = $client->createAuthUrl();
			print "<a class='login' href='$authUrl'>Connect Me!</a>";
		}
		*/

		$xmlStr = '';
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_cal_massinsert.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_cal_massinsert.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert.xml' command

		/* Be aware that Google API has some kind of side effect when you use either
		 * http://www.google.com/m8/feeds/contacts/default/base/...
		* or
		* http://www.google.com/m8/feeds/contacts/default/full/...
		* Some Ids retrieved when accessing base may not be used with full and vice versa
		* When using base, you may not change the group membership
		*/
		try {
			$responseXml = '';
			// uncomment for debugging :
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_cal_massinsert_response.xml", $responseXml);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_cal_massinsert_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
			// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert_response.xml' command
			//$res=parseResponse($responseXml);
			if($res->count != count($firstContacts) || $res->nbOfErrors)
			{
				dol_syslog("Failed to batch insert nb of errors=".$res->nbOfErrors." lasterror=".$res->lastError, LOG_ERR);
				return sprintf("Google error : %s", $res->lastError);
			}
			else
			{
				dol_syslog(sprintf("Inserting %d google events", count($firstContacts)));
			}
		}
		catch (Exception $e) {
			dol_syslog("Problem while inserting events ".$e->getMessage(), LOG_ERR);
		}
	}

	return 1;
}
