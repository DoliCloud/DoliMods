<?php
/* Copyright (C) 2013 Laurent Destailleur	<eldy@users.sourceforge.net>
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


define('ATOM_NAME_SPACE','http://www.w3.org/2005/Atom');
define('GD_NAME_SPACE','http://schemas.google.com/g/2005');
define('GCONTACT_NAME_SPACE','http://schemas.google.com/contact/2008');

define('REL_WORK','http://schemas.google.com/g/2005#work');
define('REL_HOME','http://schemas.google.com/g/2005#home');
define('REL_MOBILE','http://schemas.google.com/g/2005#mobile');
define('REL_WORK_FAX','http://schemas.google.com/g/2005#work_fax');



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
 * Return value of tag to add at end of comment into Google comments
 *
 * @return	string		Value of tag
 */
function getCommentIDTag()
{
	return	'--- (do not delete) --- dolibarr_id = ';
}


/**
 * Creates an event on the authenticated user's default calendar with the
 * specified event details.
 *
 * @param  Zend_Http_Client $client		The authenticated client object
 * @param  string			$object		Source object into Dolibarr
 * @return string 						The ID URL for the event.
 */
function googleCreateContact($client, $object)
{
	global $langs;

	include_once(DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php');

	dol_syslog('googleCreateContact object->id='.$object->id.' type='.$object->element);

	$google_nltechno_tag=getCommentIDTag();

	$doc  = new DOMDocument("1.0", "utf-8");
	try {
		// perform login and set protocol version to 3.0
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		$idindolibarr=$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
		$paramtogettag=array('societe'=>'thirdparties','contact'=>'contacts','member'=>'members');
		$groupName = getTagLabel($paramtogettag[$object->element]);
		if ($groupName == 'UnknownType') return 'ErrorTypeOfObjectNotSupported';

		// create new entry
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom',     constant('ATOM_NAME_SPACE'));
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gd',       constant('GD_NAME_SPACE'));
        $entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', constant('GCONTACT_NAME_SPACE'));
		$doc->appendChild($entry);


		// Uncomment to test when all fields are empty
		//$object->email='';	$object->url=''; $object->address=''; $object->zip=''; $object->town=''; $object->note=''; unset($object->country_id);


		// Name
		$name = $doc->createElement('gd:name');
		$entry->appendChild($name);
		if ($object->element != 'societe' && $object->element != 'thirdparty')
		{
			$fullName = $doc->createElement('gd:fullName', $object->getFullName($langs));
			$name->appendChild($fullName);
		}

		// Element
		$email = $doc->createElement('gd:email');
		$email->setAttribute('address', ($object->email?$object->email:($object->getFullName($langs).'@noemail.com')));
		$email->setAttribute('rel', 'http://schemas.google.com/g/2005#home');
		$entry->appendChild($email);

		// Address
		$address = $doc->createElement('gd:structuredPostalAddress');
		$address->setAttribute('rel', 'http://schemas.google.com/g/2005#work');
		$address->setAttribute('primary', 'true');
		$entry->appendChild($address);

			$city = $doc->createElement('gd:city', $object->town);
			if (! empty($object->town))	$address->appendChild($city);
			$street = $doc->createElement('gd:street', $object->address);
			if (! empty($object->address)) $address->appendChild($street);
			$postcode = $doc->createElement('gd:postcode', $object->zip);
			if (! empty($object->zip))	    $address->appendChild($postcode);
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

		// Birthday
		if (! empty($object->birthday))
		{
			/*
			$birthday = $doc->createElement('gd:birthday');
			$birthday->setAttribute('when' , dol_print_date($object->birthday,'dayrfc'));
			$entry->appendChild($birthday);*/
		}

		// URL
		if (! empty($object->url))
		{
			$el = $doc->createElement('gcontact:website');
			$el->setAttribute("label","URL");
			$el->setAttribute("href", $object->url);
			$entry->appendChild($el);
		}

		// Phones
		if (! empty($object->phone))
		{
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_WORK'));
			$el->appendChild($doc->createTextNode($object->phone));
			$entry->appendChild($el);
		}
		if (! empty($object->phone_pro))
		{
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_WORK'));
			$el->appendChild($doc->createTextNode($object->phone_pro));
			$entry->appendChild($el);
		}
		if (! empty($object->phone_perso))
		{
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_HOME'));
			$el->appendChild($doc->createTextNode($object->phone_perso));
			$entry->appendChild($el);
		}
		if (! empty($object->phone_mobile))
		{
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_MOBILE'));
			$el->appendChild($doc->createTextNode($object->phone_mobile));
			$entry->appendChild($el);
		}
		if (! empty($object->fax))
		{
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_WORK_FAX'));
			$el->appendChild($doc->createTextNode($object->fax));
			$entry->appendChild($el);
		}

		// Id source
		/*$extid = $doc->createElement('gcontact:externaleId');
		$extid->setAttribute('name','dolibarr-id');
		$extid->setAttribute('value',$idindolibarr);
		$entry->appendChild($extid);*/
		$userdefined = $doc->createElement('gcontact:userDefinedField');
		$userdefined->setAttribute('key','dolibarr-id');
		$userdefined->setAttribute('value',$idindolibarr);
		$entry->appendChild($userdefined);

		// Comment
		$tmpnote=$object->note_private;
		if (strpos($tmpnote,$google_nltechno_tag) === false) $tmpnote.="\n\n".$google_nltechno_tag.$idindolibarr;
		$note = $doc->createElement('atom:content',$tmpnote);
		$entry->appendChild($note);

		// Labels
		$googleGroups=array();
		$el = $doc->createElement("gcontact:groupMembershipInfo");
		$el->setAttribute("deleted", "false");
		$el->setAttribute("href", getGoogleGroupID($gdata, $groupName, $googleGroups));
		$entry->appendChild($el);

		//To list all existing field we can edit: var_dump($doc->saveXML());exit;
		$xmlStr = $doc->saveXML();
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_createcontact.xml", $xmlStr);

		// insert entry
		$entryResult = $gdata->insertEntry($xmlStr,	'http://www.google.com/m8/feeds/contacts/default/full');

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
 * @param  string           $contactId  	The ref into Google contact
 * @param  Object           $object			Object
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function googleUpdateContact($client, $contactId, $object)
{
	global $langs;

	dol_syslog('googleUpdateContact object->id='.$object->id.' type='.$object->element.' ref_ext='.$object->ref_ext);

	$google_nltechno_tag=getCommentIDTag();

	// Fields: http://tools.ietf.org/html/rfc4287

	//$gdata = new Zend_Gdata_Contacts($client);
	try {
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		//$contactId='http://www.google.com/m8/feeds/contacts/eldy10%40gmail.com/base/4429b3590f5b343a';
		$query = new Zend_Gdata_Query($contactId);
		//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
		$entryResult = $gdata->getEntry($query);
	}
	catch(Exception $e)
	{
		dol_print_error('','Failed to get record with ref='.$contactId,$e->getMessage());
	}

	try {
		$xml = simplexml_load_string($entryResult->getXML());

		//$xml->name->fullName = $object->getFullName($langs);
		$xml->name->fullName = $object->getFullName($langs);
		$xml->name->givenName = $object->firstname;
		$xml->name->familyName = $object->lastname;
		//$xml->name->additionnalName = 'xxx';
		//$xml->name->nameSuffix = 'xxx';
		//$xml->formattedAddress;
		$xml->email['address'] = ($object->email?$object->email:($object->getFullName($langs).'@noemail.com'));

		// Address
		unset($xml->structuredPostalAddress->formattedAddress);
		$xml->structuredPostalAddress->street=$object->address;
		$xml->structuredPostalAddress->city=$object->town;
		$xml->structuredPostalAddress->postcode=$object->zip;
		$xml->structuredPostalAddress->country=($object->country_id>0?'err'.getCountry($object->country_id):0);
		$xml->structuredPostalAddress->state=($object->state_id>0?getState($object->state_id):'');

		// Phone
		/*
		unset($xml->phoneNumber);
		foreach ($xml->phoneNumber as $key => $val) {
			$oldvalue=(string) $val;
			//var_dump($oldvalue);
		}*/

		foreach ($xml->website as $key => $val) {
			$oldvalue=(string) $val['href'];
			$xml->website['href'] = $object->url;
		}
		//var_dump($xml);exit;

		// userDefinedField
		// We don't change this

		// Comment
		$tmpnote=$object->note;
		if (strpos($tmpnote, $google_nltechno_tag) === false) $tmpnote.="\n\n".$google_nltechno_tag.$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
		$xml->content=$tmpnote;

		//List of properties to set visible with var_dump($xml->saveXML());exit;
		$extra_header = array('If-Match'=>'*');

		$newentryResult = $gdata->updateEntry($xml->saveXML(), $entryResult->getEditLink()->href, null, $extra_header);

	}
	catch(Exception $e)
	{
		dol_print_error('',$e->getMessage());
	}

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
function googleDeleteContactByRef($client, $ref)
{
	dol_syslog('googleDeleteContactByRef Gcontact ref to delete='.$ref);

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
 * Mass insert of several contacts into a google account
 *
 * @param 	Mixed	$gdata			Handler of Gdata connexion
 * @param 	array 	$gContacts		Array of object GContact
 * @return	int						>0 if OK, 'error string' if error
 */
function insertGContactsEntries($gdata, $gContacts)
{
	$maxBatchLength = 98; //Google doc says max 100 entries.
	$remainingContacts = $gContacts;
	while (count($remainingContacts) > 0)
	{
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
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', constant('ATOM_NAME_SPACE'));
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
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert.xml", $xmlStr);
		// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert.xml' command

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
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.xml", $responseXml);
			// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert_response.xml' command
			$res=parseResponse($responseXml);
			if($res->count != count($firstContacts) || $res->nbOfErrors)
			{
				dol_syslog("Failed to batch insert nb of errors=".$res->nbOfErrors." lasterror=".$res->lastError, LOG_ERR);
				return sprintf("Google error : %s", $res->lastError);
			}
			else
			{
				dol_syslog(sprintf("Inserting %d google contacts for user %s", count($firstContacts), $googleUser));
			}
		}
		catch (Exception $e) {
			dol_syslog("Problem while inserting contact ".$e->getMessage(), LOG_ERR);
		}
	}

	return 1;
}

/**
 * parseResponse
 *
 * @param 	string		$xmlStr		String
 * @return	stdClass				Class with response
 */
function parseResponse($xmlStr)
{
	$doc = new DOMDocument("1.0", "utf-8");
	$doc->loadXML($xmlStr);
	$contentNodes = $doc->getElementsByTagName("entry");
	$res = new stdClass();
	$res->count = $contentNodes->length;
	$res->nbOfErrors=0;
	foreach ($contentNodes as $node) {
		$title = $node->getElementsByTagName("title");
		if ($title->length==1 && ($title->item(0)->textContent=='Error' || $title->item(0)->textContent=='Fatal Error'))
		{
			$res->nbOfErrors++;
			$content = $node->getElementsByTagName("content");
			//$batchinter = $node->getElementsByTagName("batch");
			//$reason = $batchinter->item(0)->getAttribute("reason");
			//var_dump($reason);
			if($content->length>0)
			{
				$res->lastError=$content->item(0)->textContent;
				//$res->lastError=$batchinter;
			}
		}
	}
	return $res;
}


/**
 * Return TAG to add into Google Gmail
 *
 * @param 	string $s		Type of tag
 * @return	string			Label
 */
function getTagLabel($s)
{
	global $conf,$langs;

	$tag='UnknownType';
	$tagthirdparties=empty($conf->global->GOOGLE_TAG_PREFIX)?'':$conf->global->GOOGLE_TAG_PREFIX;
	$tagcontacts=empty($conf->global->GOOGLE_TAG_PREFIX_CONTACTS)?'':$conf->global->GOOGLE_TAG_PREFIX_CONTACTS;
	$tagmembers=empty($conf->global->GOOGLE_TAG_PREFIX_MEMBERS)?'':$conf->global->GOOGLE_TAG_PREFIX_MEMBERS;
	if ($s=='thirdparties') $tag=$tagthirdparties?$tagthirdparties:'Dolibarr ('.$langs->trans("Thirdparties").')';
	if ($s=='contacts')     $tag=$tagcontacts?$tagcontacts:'Dolibarr ('.$langs->trans("Contacts").')';
	if ($s=='members')      $tag=$tagmembers?$tagmembers:'Dolibarr ('.$langs->trans("Members").')';
	return $tag;
}


/**
 * Retreive a googleGroupID for a groupName.
 * If the groupName does not exist on Gmail account, it will be created as a side effect
 *
 * @param	Mixed	$gdata			GData handler
 * @param	string	$groupName		Group name
 * @param	array	&$googleGroups	Array of Google Group we know they already exists
 * @return 	string					Google Group ID for groupName.
 */
function getGoogleGroupID($gdata, $groupName, &$googleGroups=array())
{
	global $conf;

	// Search existing groups
	if (! is_array($googleGroups) || count($googleGroups) == 0)
	{
		$document = new DOMDocument("1.0", "utf-8");
		$xmlStr = getContactGroupsXml($gdata);
		$document->loadXML($xmlStr);
		$xmlStr = $document->saveXML();
		$entries = $document->documentElement->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "entry");
		$n = $entries->length;
		$googleGroups = array();
		foreach ($entries as $entry) {
			$titleNodes = $entry->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "title");
			if ($titleNodes->length == 1) {
				$title = $titleNodes->item(0)->textContent;
				$googleIDNodes = $entry->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "id");
				if ($googleIDNodes->length == 1) {
					$googleGroups[$title] = $googleIDNodes->item(0)->textContent;
				}
			}
		}
	}

	// Create group if it not exists
	if (!isset($googleGroups[$groupName]))
	{
		$newGroupID = insertGContactGroup($gdata, $groupName);
		$googleGroups[$groupName] = $newGroupID;
	}
	return $googleGroups[$groupName];
}


/**
 * Retreive a Xml feed of contacts groups from Google
 *
 * @param	Mixed	$gdata			GData handler
 * @return	string					XML string with all groups
 */
function getContactGroupsXml($gdata)
{
	try {
		$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/groups/default/full?max-results=1000');
		$feed = $gdata->getFeed($query);
		$xmlStr = $feed->getXML();
		// uncomment for debugging :
		//file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups.xml", $xmlStr);
		// you can view this file with 'xmlstarlet fo dolibarr_google_groups.xml' command
	} catch (Exception $e) {
		dol_syslog(sprintf("Error while feed xml groups : %s", $e->getMessage()), LOG_ERR);
	}
	return($xmlStr);
}



/**
 * Create a group/label into Google contact
 *
 * @param	Mixed	$gdata			GData handler
 * @param 	string 	$groupName		Group name to create into Google Contact
 * @return 	string					googlegroupID
 */
function insertGContactGroup($gdata,$groupName)
{
	dol_syslog('insertGContactGroup create group '.$groupName.' into Google contact');

	try {
		$doc = new DOMDocument("1.0", 'utf-8');
		$entry = $doc->createElement("atom:entry");
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', constant('ATOM_NAME_SPACE'));
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
		dol_syslog(sprintf("Problem while inserting group %s : %s", $groupName, $e->getMessage()), LOG_ERR);
	}

	return($entryResult->id);
}
