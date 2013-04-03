<?php
/* Copyright (C) 2011 Regis Houssin	<regis@dolibarr.fr>
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


function getCommentIDTag()
{
	return	'--- (do not delete) --- dolibarr_id = ';
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

	$google_nltechno_tag=getCommentIDTag();

	$doc  = new DOMDocument();
	try {
		// perform login and set protocol version to 3.0
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		$groupName = ($object->element=='societe'?'Dolibarr thirdparties':'Dolibarr contacts');

		// create new entry
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' , 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' , 'xmlns:gd', 'http://schemas.google.com/g/2005');
		$doc->appendChild($entry);


		// Uncomment to test when all fields are empty
		//$object->email='';	$object->url=''; $object->address=''; $object->zip=''; $object->town=''; $object->note=''; unset($object->country_id);


		// add name element
		$name = $doc->createElement('gd:name');
		$entry->appendChild($name);
			$fullName = $doc->createElement('gd:fullName', $object->getFullName($langs));
			$name->appendChild($fullName);

		// add email element
		$email = $doc->createElement('gd:email');
		$email->setAttribute('address', ($object->email?$object->email:($object->getFullName($langs).'@noemail.com')));
		$email->setAttribute('rel', 'http://schemas.google.com/g/2005#home');
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
		$orgName = $doc->createElement('gd:orgName', 'xxx');
		$org->appendChild($orgName);
		$orgTitle = $doc->createElement('gd:orgTitle', 'xxx');
		$org->appendChild($orgTitle);
		*/

		$address = $doc->createElement('gd:structuredPostalAddress');
		$address->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
		$address->setAttribute('primary' ,'true');
		$entry->appendChild($address);

			$city = $doc->createElement('gd:city', $object->town);
			if (! empty($object->town))	$address->appendChild($city);
			$street = $doc->createElement('gd:street', $object->address);
			if (! empty($object->address)) $address->appendChild($street);
			$postcode = $doc->createElement('gd:postcode', $object->zip);
			if (! empty($object->zip))	$address->appendChild($postcode);
			/*$tmpstate=getState($object->state_id,0);
			$region = $doc->createElement('gd:region', $tmpstate);
			if ($tmpstate) $address->appendChild($region);*/
			$tmpcountry=getCountry($object->country_id,0);
			$country = $doc->createElement('gd:country', $tmpcountry);
			if ($tmpcountry) $address->appendChild($country);
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

		$tmpnote=$object->note;
		if (strpos($tmpnote,$google_nltechno_tag) === false) $tmpnote.="\n\n".$google_nltechno_tag.$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
		$note = $doc->createElement('atom:content',$tmpnote);
		$entry->appendChild($note);

		//To list all existing field we can edit: var_dump($doc->saveXML());exit;
		//var_dump($doc->saveXML());exit;

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
 * insertGContactGroup
 *
 * @param string $groupName
 * @return *googlegroupID
 */
function insertGContactGroup($gdata,$groupName)
{
	try {
		$doc = new DOMDocument("1.0", 'utf-8');
		$entry = $doc->createElement("atom:entry");
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');
		$el = $doc->createElement("atom:category");
		$el->setAttribute("term", "http://schemas.google.com/contact/2008#group");
		$el->setAttribute("scheme", "http://schemas.google.com/g/2005#kind");
		$entry->appendChild($el);
		$el = $doc->createElement("atom:title", $groupName);
		$el->setAttribute("type", "text");
		$entry->appendChild($el);
		$el = $doc->createElement("atom:content", $groupName);
		$el->setAttribute("type", "text");
		$entry->appendChild($el);
		$doc->appendChild($entry);
		$doc->formatOutput = true;
		$xmlStr = $doc->saveXML();
		// insert entry
		$entryResult = $gdata->insertEntry($xmlStr, 'http://www.google.com/m8/feeds/groups/default/full');
		dol_syslog(sprintf("Inserting gContact group %s in google contacts for google ID = %s", $groupName, $entryResult->id));
	} catch (Exception $e) {
		dol_syslog("Problem while inserting group", LOG_ERR);
		throw new Exception(sprintf("Problem while inserting group %s : %s", $groupName, $e->getMessage()));
	}
	return($entryResult->id);
}


/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param  Zend_Http_Client $client   		The authenticated client object
 * @param  string           $contactId  	The event ID string
 * @param  Object           $object			Object
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function updateContact($client, $contactId, $object)
{
	global $langs;

	$google_nltechno_tag=getCommentIDTag();

	// Fields: http://tools.ietf.org/html/rfc4287

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
	$xml->email['address'] = ($object->email?$object->email:($object->getFullName($langs).'@noemail.com'));

	foreach ($xml->phoneNumber as $p) {
		$obj->phoneNumber[] = (string) $p;
	}
	foreach ($xml->website as $w) {
		$obj->website[] = (string) $w['href'];
	}

	$tmpnote=$object->note;
	if (strpos($tmpnote, $google_nltechno_tag) === false) $tmpnote.="\n\n".$google_nltechno_tag.$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
	$xml->content=$tmpnote;

	//List of properties to set visible with var_dump($xml->saveXML());exit;

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





/**
 * Insert contacts into a google account
 *
 * @param array $gContacts
 */
function insertGContactsEntries($gdata, $gContacts)
{
	$maxBatchLength = 98; //Google doc says max 100 entries.
	$remainingContacts = $gContacts;
	while (count($remainingContacts) > 0) {
		if (count($remainingContacts) > $maxBatchLength) {
			$firstContacts = array_slice($remainingContacts, 0, $maxBatchLength);
			$remainingContacts = array_slice($remainingContacts, $maxBatchLength);
		} else {
			$firstContacts = $remainingContacts;
			$remainingContacts = array();
		}
		$doc = new DOMDocument("1.0", "utf-8");
		$doc->formatOutput = true;
		$feed = $doc->createElement("atom:feed");
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:batch', 'http://schemas.google.com/gdata/batch');
		$feed->appendChild($doc->createElement("title", "The batch title: insert contacts"));
		$doc->appendChild($feed);
		foreach ($firstContacts as $gContact) {
			$entry = $gContact->atomEntry;
			$entry = $doc->importNode($entry, true);
			$entry->setAttribute("gdata:etag", "*");
			$entry = $feed->appendChild($entry);
			$el = $doc->createElement("batch:operation");
			$el->setAttribute("type", "insert");
			$entry->appendChild($el);
		}
		$xmlStr = $doc->saveXML();
		// uncomment for debugging :
		// file_put_contents(DOL_DATA_ROOT . "/gcontacts/temp/gmail.contacts.xml", $xmlStr);
		// dump it with 'xmlstarlet fo gmail.contacts.xml' command

		/* Be aware that Google API has some kind of side effect when you use either
		 * http://www.google.com/m8/feeds/contacts/default/base/...
		* or
		* http://www.google.com/m8/feeds/contacts/default/full/...
		* Some Ids retrieved when accessing base may not be used with full and vice versa
		* When using base, you may not change the group membership
		*/
		try {
			$response = $gdata->post($xmlStr, "http://www.google.com/m8/feeds/contacts/default/full/batch");
			$responseXml = $response->getBody();
			// uncomment for debugging :
			//file_put_contents(DOL_DATA_ROOT . "/gcontacts/temp/gmail.response.xml", $responseXml);
			// dump it with 'xmlstarlet fo gmail.response.xml' command
			$res=parseResponse($responseXml);
			if($res->count != count($firstContacts) || $res->errors)
				throw new Exception(sprintf("Google error : %s", $res->lastError));
			dol_syslog(sprintf("Inserting %d google contacts for user %s", count($firstContacts), $googleUser));
		} catch (Exception $e) {
			dol_syslog("Problem while inserting contact", LOG_ERR);
			throw new Exception($e->getMessage());
		}

	}
}

/**
 *
 * @param unknown_type $xmlStr
 */
function parseResponse($xmlStr)
{
	//$xmlStr = file_get_contents(DOL_DATA_ROOT . "/gcontacts/temp/gmail.response.xml");
	$doc = new DOMDocument("1.0", "utf-8");
	$doc->loadXML($xmlStr);
	$contentNodes = $doc->getElementsByTagName("entry");
	$res = new stdClass();
	$res->count = $contentNodes->length;
	$res->errors=0;
	foreach ($contentNodes as $node) {
		$title = $node->getElementsByTagName("title");
		if($title->length==1 && $title->item(0)->textContent=='Error') {
			$res->errors++;
			$content = $node->getElementsByTagName("content");
			if($content->length>0)
				$res->lastError=$content->item(0)->textContent;
		}
	}
	return $res;
}


/**
 * Return TAG to add into Google Gmail
 *
 * @param string $s		Type of tag
 */
function getTagLabel($s)
{
	global $conf,$langs;

	$tag=empty($conf->global->GOOGLE_TAG_PREFIX)?'Dolibarr':$conf->global->GOOGLE_TAG_PREFIX;
	if ($s=='thirdparties') $tag.=' ('.$langs->trans("Thirdparty").')';
	if ($s=='contact') $tag.=' ('.$langs->trans("Contact").')';
	return $tag;
}

