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

require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Contacts');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');

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
function getClientLoginHttpClientContact($user, $pass, $service)
{
	$client=null;

	try {
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
	}
	catch(Exception $e)
	{

	}
	return $client;
}



/**
 * Creates an event on the authenticated user's default calendar with the
 * specified event details.
 *
 * @param  Zend_Http_Client $client    The authenticated client object
 * @param  string			$object	   Source object into Dolibarr
 * @return string The ID URL for the event.
 */
function createContact($client, $object)
{
	global $langs;

	$doc  = new DOMDocument();
	try {
		// perform login and set protocol version to 3.0
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		// create new entry
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' ,
			'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' ,
			'xmlns:gd', 'http://schemas.google.com/g/2005');
		$doc->appendChild($entry);

		// add name element
		$name = $doc->createElement('gd:name');
		$entry->appendChild($name);

		$fullName = $doc->createElement('gd:fullName', $object->getFullName($langs));
		$name->appendChild($fullName);

		// add email element
		$email = $doc->createElement('gd:email');
		$email->setAttribute('address' ,$object->email);
		$email->setAttribute('rel' ,'http://schemas.google.com/g/2005#home');
		$entry->appendChild($email);

		// add org name element
		/*
		$org = $doc->createElement('gd:organization');
		$org->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
		$entry->appendChild($org);
		$orgName = $doc->createElement('gd:orgName', $object->name);
		$org->appendChild($orgName);
		*/

		// insert entry
		$entryResult = $gdata->insertEntry($doc->saveXML(),	'http://www.google.com/m8/feeds/contacts/default/full');

		//echo 'The id of the new entry is: ' . $entryResult->getId().'<br>';

		return $entryResult->getId();
	} catch (Exception $e) {
		die('ERROR:' . $e->getMessage());
	}
}


/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param  Zend_Http_Client $client   		The authenticated client object
 * @param  string           $contactId  	The event ID string
 * @param  string           $newTitle 		The new title to set on this event
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function updateContact($client, $contactId, $object)
{
	global $langs;

	//$gdata = new Zend_Gdata_Contacts($client);
	$gdata = new Zend_Gdata($client);
	$gdata->setMajorProtocolVersion(3);

	$query = new Zend_Gdata_Query($contactId);
	//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
	$entryResult = $gdata->getEntry($query);

	$xml = simplexml_load_string($entryResult->getXML());

	//$xml->name->fullName = $object->getFullName($langs);
	$xml->name->fullName = $object->getFullName($langs);
	$xml->email['address'] = $object->email;

	$extra_header = array('If-Match'=>'*');
	$newentryResult = $gdata->updateEntry($xml->saveXML(), $entryResult->getEditLink()->href, null, $extra_header);

	return $entryResult->getId();
}



/**
 * Deletes the event specified by retrieving the atom entry object
 * and calling Zend_Feed_EntryAtom::delete() method.  This is for
 * example purposes only, as it is inefficient to retrieve the entire
 * atom entry only for the purposes of deleting it.
 *
 * @param  Zend_Http_Client $client  	The authenticated client object
 * @param  string           $ref		The ref string
 * @return void
 */
function deleteContactByRef($client, $ref)
{
	dol_syslog("deleteEventByRef ".$ref);

	//$gdata = new Zend_Gdata_Contacts($client);
	$gdata = new Zend_Gdata($client);
	$gdata->setMajorProtocolVersion(3);

	$query = new Zend_Gdata_Query($ref);
	//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
	$entryResult = $gdata->getEntry($query);

	$extra_header = array('If-Match'=>$entryResult->getEtag());
	$entryResult->delete($extra_header);
}


