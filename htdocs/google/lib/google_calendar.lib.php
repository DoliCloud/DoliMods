<?php
/* Copyright (C) 2011 Regis Houssin	<regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 */

$path = dol_buildpath('/google/includes/zendgdata');
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

/**
 * @see Zend_Loader
 */
require_once('Zend/Loader.php');

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_AuthSub
 */
Zend_Loader::loadClass('Zend_Gdata_AuthSub');

/**
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_HttpClient
 */
Zend_Loader::loadClass('Zend_Gdata_HttpClient');

/**
 * @see Zend_Gdata_Calendar
 */
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
 * Returns the full URL of the current page, based upon env variables
 *
 * Env variables used:
 * $_SERVER['HTTPS'] = (on|off|)
 * $_SERVER['HTTP_HOST'] = value of the Host: header
 * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
 * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
 *
 * @return string Current URL
 */
function getCurrentUrl()
{
	global $_SERVER;

	/**
	 * Filter php_self to avoid a security vulnerability.
	 */
	$php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

	if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
		$protocol = 'https://';
	} else {
		$protocol = 'http://';
	}

	$host = $_SERVER['HTTP_HOST'];

	if ($_SERVER['SERVER_PORT'] != '' &&
	(($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
	($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
		$port = ':' . $_SERVER['SERVER_PORT'];
	} else {
		$port = '';
	}

	return $protocol . $host . $port . $php_request_uri;
}

/**
 * Returns the AuthSub URL which the user must visit to authenticate requests
 * from this application.
 *
 * Uses getCurrentUrl() to get the next URL which the user will be redirected
 * to after successfully authenticating with the Google service.
 *
 * @return string AuthSub URL
 */
function getAuthSubUrl()
{
	global $_authSubKeyFile;
	$next = getCurrentUrl();
	$scope = 'http://www.google.com/calendar/feeds/';
	$session = true;
	if ($_authSubKeyFile != null) {
		$secure = true;
	} else {
		$secure = false;
	}
	return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, $session);
}

/**
 * Outputs a request to the user to login to their Google account, including
 * a link to the AuthSub URL.
 *
 * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
 *
 * @return void
 */
function requestUserLogin($linkText)
{
	$authSubUrl = getAuthSubUrl();
	echo "<a href=\"{$authSubUrl}\">{$linkText}</a>";
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using AuthSub authentication.
 *
 * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
 * it is obtained.  The single use token supplied in the URL when redirected
 * after the user succesfully authenticated to Google is retrieved from the
 * $_GET['token'] variable.
 *
 * @return Zend_Http_Client
 */
function getAuthSubHttpClient()
{
	global $_SESSION, $_GET, $_authSubKeyFile, $_authSubKeyFilePassphrase;

	$client = new Zend_Gdata_HttpClient();
	if ($_authSubKeyFile != null) {
		// set the AuthSub key
		$client->setAuthSubPrivateKeyFile($_authSubKeyFile, $_authSubKeyFilePassphrase, true);
	}
	if (! isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
		$_SESSION['sessionToken'] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token'], $client);
	}
	$client->setAuthSubToken($_SESSION['sessionToken']);
	return $client;
}

/**
 * Processes loading of this sample code through a web browser.  Uses AuthSub
 * authentication and outputs a list of a user's calendars if succesfully
 * authenticated.
 *
 * @return void
 */
function processPageLoad()
{
	global $_SESSION, $_GET;

	if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
		requestUserLogin('Please login to your Google Account.');
	} else {
		$client = getAuthSubHttpClient();
		outputCalendarList($client);
	}
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using the ClientLogin credentials supplied.
 *
 * @param  string $user The username, in e-mail address format, to authenticate
 * @param  string $pass The password for the user specified
 * @return Zend_Http_Client
 */
function getClientLoginHttpClient($user, $pass)
{
	$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;

	$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
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
 * @param  string           $title     The event title
 * @param  string			$object	   Source object into Dolibarr
 * @return string The ID URL for the event.
 */
function createEvent($client, $object)
{
    // More examples on http://code.google.com/intl/fr/apis/calendar/data/1.0/developers_guide_php.html

	$gc = new Zend_Gdata_Calendar($client);

	$newEntry = $gc->newEventEntry();
	$newEntry->title = $gc->newTitle(trim($object->label));
	$newEntry->where  = array($gc->newWhere($object->location));

	$newEntry->content = $gc->newContent(dol_string_nohtmltag($object->note));
	$newEntry->content->type = 'text';

	$when = $gc->newWhen();
    if (empty($object->fulldayevent))
    {
        $when->startTime = dol_print_date($object->datep,"dayhourrfc",'gmt');
        $when->endTime = dol_print_date(empty($object->datef)?$object->datep:$object->datef,"dayhourrfc",'gmt');
    }
    else
    {
        $when->startTime = dol_print_date($object->datep,"dayrfc");
        $when->endTime = dol_print_date(empty($object->datef)?$object->datep:$object->datef,"dayrfc");
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
function createQuickAddEvent ($client, $quickAddText)
{
	$gdataCal = new Zend_Gdata_Calendar($client);
	$event = $gdataCal->newEventEntry();
	$event->content = $gdataCal->newContent($quickAddText);
	$event->quickAdd = $gdataCal->newQuickAdd(true);

	$newEvent = $gdataCal->insertEvent($event);
	return $newEvent->id->text;
}

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
	$gdataCal = new Zend_Gdata_Calendar($client);

	$eventOld = getEvent($client, $eventId);
	if ($eventOld)
	{
	    //echo "Old title: " . $eventOld->title->text . " -> ".$object->label."<br>\n"; exit;
	    $eventOld->title = $gdataCal->newTitle($object->label);
	    $eventOld->where = array($gdataCal->newWhere($object->location));

	    $eventOld->content = $gdataCal->newContent(dol_string_nohtmltag($object->note));
	    $eventOld->content->type = 'text';

	    $when = $gdataCal->newWhen();
	    if (empty($object->fulldayevent))
	    {
	        $when->startTime = dol_print_date($object->datep,"dayhourrfc",'gmt');
	        $when->endTime = dol_print_date(empty($object->datef)?$object->datep:$object->datef,"dayhourrfc",'gmt');
	    }
	    else
	    {
	        $when->startTime = dol_print_date($object->datep,"dayrfc");
	        $when->endTime = dol_print_date(empty($object->datef)?$object->datep:$object->datef,"dayrfc");
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
