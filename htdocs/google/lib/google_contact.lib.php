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
 *
 * Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/
 * Tutorial: http://www.ibm.com/developerworks/library/x-phpgooglecontact/index.html
 * Tutorial: https://developers.google.com/google-apps/contacts/v3/
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

	include_once(DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php');

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

		// im
		/*$im = $doc->createElement('gd:im');
		$im->setAttribute('protocol', 'xxx');
		$im->setAttribute('protocol', 'address');
		$entry->appendChild($im);
		*/

		// add org name element
		/*
		$org = $doc->createElement('gd:organization');
		$org->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
		$entry->appendChild($org);
		$orgName = $doc->createElement('gd:orgName', $object->name);
		$org->appendChild($orgName);
		*/

		$address = $doc->createElement('gd:structuredPostalAddress');
		$address->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
		$address->setAttribute('primary' ,'true');
		$entry->appendChild($address);
		$city = $doc->createElement('gd:city', $object->town);
		$address->appendChild($city);
		$street = $doc->createElement('gd:street', $object->address);
		$address->appendChild($street);
		$postcode = $doc->createElement('gd:postcode', $object->zip);
		$address->appendChild($postcode);
		/*
		$region = $doc->createElement('gd:region', getState($object->state_id,0));
		$address->appendChild($region);
		*/
		$country = $doc->createElement('gd:country', getCountry($object->country_id,0));
		$address->appendChild($country);
		/*
		$formattedaddress = $doc->createElement('gd:formattedAddress', 'eeeee');
		$address->appendChild($formattedaddress);
		*/

		/*
		$birthday = $doc->createElement('gd:birthday');
		$birthday->setAttribute('when' , dol_print_date($object->birthday,'dayrfc'));
		$entry->appendChild($birthday);
		*/

		$website = $doc->createElement('gd:website');
		$website->setAttribute('href',$object->url);
		$entry->appendChild($website);

		$more = $doc->createElement('gd:extendedProperty');
		$more->setAttribute('name','dolibarr-contact-id');
		$more->setAttribute('value',$object->id);
		$entry->appendChild($more);
		$extid = $doc->createElement('gd:externaleId');
		$extid->setAttribute('name','dolibarr-contact-id');
		$extid->setAttribute('value',$object->id);
		$entry->appendChild($extid);
		$userdefined = $doc->createElement('gd:userDefinedField');
		$userdefined->setAttribute('key','dolibarr-contact-id');
		$userdefined->setAttribute('value',$object->id);
		$entry->appendChild($userdefined);


		$note = $doc->createElement('atom:content',$object->note);
		$entry->appendChild($note);

//		var_dump($doc->saveXML());exit;

		// insert entry
		$entryResult = $gdata->insertEntry($doc->saveXML(),	'http://www.google.com/m8/feeds/contacts/default/full');

		//var_dump($doc->saveXML());exit;

		//echo 'The id of the new entry is: ' . $entryResult->getId().'<br>';
		//var_dump($entryResult);exit;

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

	//$contactId='http://www.google.com/m8/feeds/contacts/eldy10%40gmail.com/base/75ba08690d17cdf2';
	$query = new Zend_Gdata_Query($contactId);
	//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
	$entryResult = $gdata->getEntry($query);

	$xml = simplexml_load_string($entryResult->getXML());

	//$xml->name->fullName = $object->getFullName($langs);
	$xml->name->fullName = $object->getFullName($langs);
	//$xml->name->givenName = 'xxx';
	//$xml->name->additionnalName = 'xxx';
	//$xml->name->familyName = 'xxx';
	//$xml->name->nameSuffix = 'xxx';
	//$xml->formattedAddress;
	$xml->email['address'] = $object->email;

	foreach ($xml->phoneNumber as $p) {
		$obj->phoneNumber[] = (string) $p;
	}
	foreach ($xml->website as $w) {
		$obj->website[] = (string) $w['href'];
	}

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
 * @return string 						'' if OK, error message if KO
 */
function deleteContactByRef($client, $ref)
{
	dol_syslog("deleteEventByRef ".$ref);

	try
	{
		//$gdata = new Zend_Gdata_Contacts($client);
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		$query = new Zend_Gdata_Query($ref);
		//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
		$entryResult = $gdata->getEntry($query);

		$extra_header = array('If-Match'=>$entryResult->getEtag());
		$entryResult->delete($extra_header);

		return '';
	}
	catch(Exception $e)
	{
		return $e->getMessage();
	}
}


