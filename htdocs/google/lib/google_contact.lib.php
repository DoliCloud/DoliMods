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
 * Documentation API v3 (Connect method used is "ClientLogin"):
 * https://developers.google.com/google-apps/contacts/v3/
 *
 * Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/
 * Tutorial: http://www.ibm.com/developerworks/library/x-phpgooglecontact/index.html
 *
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
	global $tag_debug;

	$tag_debug='clientlogin';

	$client=null;

	try {
		dol_syslog("getClientLoginHttpClientContact user=".$user." pass=".preg_replace('/./','*',$pass)." service=".$service, LOG_DEBUG);
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
 * @param  string			$useremail	User email
 * @return string 						The ID URL for the event.
 */
function googleCreateContact($client, $object, $useremail='default')
{
	global $conf,$langs;
	global $tag_debug;

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
		}
		else
		{
			$fullName = $doc->createElement('gd:fullName', $object->name);
		}
		$name->appendChild($fullName);

		// Element
		$email = $doc->createElement('gd:email');
		$email->setAttribute('address', ($object->email?$object->email:((empty($object->name)?$object->lastname.$object->firstname:$object->name).'@noemail.com')));
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
			$tmpcountry=getCountry($object->country_id,0,'',$langs,0);
			$country = $doc->createElement('gd:country', $tmpcountry);
			if ($tmpcountry) $address->appendChild($country);
			/*
			$formattedaddress = $doc->createElement('gd:formattedAddress', 'eeeee');
			$address->appendChild($formattedaddress);
			*/

		// Company - Function
		if ($object->element == 'contact')
		{
			// Company
			$company = $doc->createElement('gd:organization');
			$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
			$entry->appendChild($company);

			$object->fetch_thirdparty();
			if (! empty($object->thirdparty->name) || ! empty($object->poste))
			{
				$thirdpartyname=$object->thirdparty->name;

				$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
				if (! empty($thirdpartyname)) $company->appendChild($orgName);
				$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
				if (! empty($object->poste)) $company->appendChild($orgTitle);
			}
		}
		if ($object->element == 'member')
		{
			// Company
			$company = $doc->createElement('gd:organization');
			$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
			$entry->appendChild($company);

			//$object->fetch_thirdparty();
			if (! empty($object->company))
			{
				$thirdpartyname=$object->company;

				$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
				if (! empty($thirdpartyname)) $company->appendChild($orgName);
				//$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
				//if (! empty($object->poste)) $company->appendChild($orgTitle);
			}
		}

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
		$note = $doc->createElement('atom:content',google_html_convert_entities($tmpnote));
		$entry->appendChild($note);

		// Labels
		$googleGroups=array();
		$el = $doc->createElement("gcontact:groupMembershipInfo");
		$el->setAttribute("deleted", "false");
		$el->setAttribute("href", getGoogleGroupID($gdata, $groupName, $googleGroups, $useremail));
		$entry->appendChild($el);

		$tag_debug='createcontact';

		//To list all existing field we can edit: var_dump($doc->saveXML());exit;
		$xmlStr = $doc->saveXML();
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_createcontact.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_createcontact.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_createcontact.xml' command

		// insert entry
		$entryResult = $gdata->insertEntry($xmlStr,	'https://www.google.com/m8/feeds/contacts/'.$useremail.'/full');

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
 * @param  string			$useremail		User email
 * @return string							Zend_Gdata id, 0 if not found, <0 if error
 */
function googleUpdateContact($client, $contactId, $object, $useremail='default')
{
	global $conf,$langs;
	global $tag_debug;

	$tag_debug='updatecontact';

	dol_syslog('googleUpdateContact object->id='.$object->id.' type='.$object->element.' ref_ext='.$object->ref_ext);

	$google_nltechno_tag=getCommentIDTag();

	// Fields: http://tools.ietf.org/html/rfc4287

	//$gdata = new Zend_Gdata_Contacts($client);
	try {
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		//$contactId='https://www.google.com/m8/feeds/contacts/eldy10%40gmail.com/base/4429b3590f5b343a';
		$query = new Zend_Gdata_Query($contactId);
		//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
		$entryResult = $gdata->getEntry($query);
	}
	catch(Exception $e)
	{
		// Not found error
		dol_syslog('Failed to get record with ref='.$contactId,$e->getMessage(), LOG_WARNING);
		return 0;
	}

	try {
		$xml = simplexml_load_string($entryResult->getXML());
		//var_dump($xml);

		if ($object->element != 'societe' && $object->element != 'thirdparty')
		{
			$fullNameToUse = $object->getFullName($langs);
		}
		else
		{
			$fullNameToUse = $object->name;
		}
		$xml->name->fullName = $fullNameToUse;
		if (! empty($object->firstname))  $xml->name->givenName = $object->firstname;
		if (! empty($object->lastname))   $xml->name->familyName = $object->lastname;
		//$xml->name->additionnalName = 'xxx';
		//$xml->name->nameSuffix = 'xxx';
		//$xml->formattedAddress;
		$xml->email['address'] = ($object->email?$object->email:((empty($object->name)?$object->lastname.$object->firstname:$object->name).'@noemail.com'));

		// Address
		unset($xml->structuredPostalAddress->formattedAddress);
		if (! empty($object->address)) $xml->structuredPostalAddress->street=$object->address;
		if (! empty($object->town)) $xml->structuredPostalAddress->city=$object->town;
		if (! empty($object->zip)) $xml->structuredPostalAddress->postcode=$object->zip;
		if ($object->country_id > 0) $xml->structuredPostalAddress->country=($object->country_id>0?getCountry($object->country_id,0,'',$langs,0):'');
		if ($object->state_id > 0) $xml->structuredPostalAddress->state=($object->state_id>0?getState($object->state_id):'');

		// Company + Function
		if ($object->element == 'contact')
		{
			unset($xml->organization->orgName);
			unset($xml->organization->orgTitle);
			$object->fetch_thirdparty();
			if (! empty($object->thirdparty->name) || ! empty($object->poste))
			{
				$thirdpartyname=$object->thirdparty->name;
				$xml->organization['rel']="http://schemas.google.com/g/2005#other";
				if (! empty($object->thirdparty->name)) $xml->organization->orgName=$thirdpartyname;
				if (! empty($object->poste)) $xml->organization->orgTitle=$object->poste;
			}
		}
		if ($object->element == 'member')
		{
			unset($xml->organization->orgName);
			unset($xml->organization->orgTitle);
			if (! empty($object->company))
			{
				$thirdpartyname=$object->company;
				$xml->organization['rel']="http://schemas.google.com/g/2005#other";
				if (! empty($object->company)) $xml->organization->orgName=$thirdpartyname;
			}
		}

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
		$xml->content=google_html_convert_entities($tmpnote);

		//List of properties to set visible with var_dump($xml->saveXML());exit;
		$extra_header = array('If-Match'=>'*');

		$xmlStr=$xml->saveXML();
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_updatecontact.xml' command

		$newentryResult = $gdata->updateEntry($xmlStr, $entryResult->getEditLink()->href, null, $extra_header);
	}
	catch(Exception $e)
	{
		dol_print_error('','Failed to update google '.$e->getMessage());
		return -1;
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
 * @param  string			$useremail	User email
 * @return string 						'' if OK, error message if KO
 */
function googleDeleteContactByRef($client, $ref, $useremail='default')
{
	global $tag_debug;

	$tag_debug='deletecontactbyref';

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
 * Use API v2 batch
 *
 * @param 	Mixed	$gdata			Handler of Gdata connexion
 * @param 	array 	$gContacts		Array of object GContact
 * @param	Mixed	$objectstatic	Object static to update ref_ext of records if success
 * @param	string	$useremail		User email
 * @return	int						>0 if OK, 'error string' if error
 */
function insertGContactsEntries($gdata, $gContacts, $objectstatic, $useremail='default')
{
	global $conf;

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
		foreach ($firstContacts as $gContact)
		{
			$entry = $gContact->atomEntry;
			$entry = $doc->importNode($entry, true);
			$entry->setAttribute("gdata:etag", "*");
			$entry = $feed->appendChild($entry);
			$el = $doc->createElement("batch:operation");
			$el->setAttribute("type", "insert");
			$entry->appendChild($el);
		}

		$xmlStr = $doc->saveXML();
		//var_dump($xmlStr);exit;

		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert.xml' command

		/* Be aware that Google API has some kind of side effect when you use either
		 * https://www.google.com/m8/feeds/contacts/default/base/...
		 * or
		 * https://www.google.com/m8/feeds/contacts/default/full/...
		 * Some Ids retrieved when accessing base may not be used with full and vice versa
		 * When using base, you may not change the group membership
		 */
		try {

/* HERE AN EXAMPLE STRING
$xmlStr = <<<END
<?xml version="1.0" encoding="utf-8"?>
<atom:feed xmlns:atom="http://www.w3.org/2005/Atom" xmlns:gdata="http://schemas.google.com/g/2005" xmlns:gcontact="http://schemas.google.com/contact/2008" xmlns:batch="http://schemas.google.com/gdata/batch">
  <title>The batch title: insert contacts</title>
  <atom:entry gdata:etag="*">
    <gdata:name>
      <gdata:familyName>SARL XXX</gdata:familyName>
      <gdata:fullName>SARL XXX</gdata:fullName>
    </gdata:name>
    <atom:content>
Text &eacute;


--- (do not delete) --- dolibarr_id = 11/thirdparty</atom:content>
    <gdata:phoneNumber rel="http://schemas.google.com/g/2005#work_fax">0325700775</gdata:phoneNumber>
    <gdata:structuredPostalAddress rel="http://schemas.google.com/g/2005#work">
      <gdata:street>Address</gdata:street>
      <gdata:postcode>75000</gdata:postcode>
      <gdata:city>Paris</gdata:city>
      <gdata:country>France</gdata:country>
    </gdata:structuredPostalAddress>
    <gdata:email rel="http://schemas.google.com/g/2005#work" address="contact@email.com" primary="true"/>
    <gcontact:userDefinedField key="dolibarr-id" value="11/thirdparty"/>
    <gcontact:groupMembershipInfo deleted="false" href="https://www.google.com/m8/feeds/groups/ecidfrance%40gmail.com/base/3709b8488903c095"/>
    <batch:operation type="insert"/>
  </atom:entry>
</atom:feed>
END;
$xmlStr = google_html_convert_entities($xmlStr);
*/
			$response = $gdata->post($xmlStr, "https://www.google.com/m8/feeds/contacts/".$useremail."/full/batch");
			$responseXml = $response->getBody();
			// uncomment for debugging :
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.xml", $responseXml);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
			// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert_response.xml' command
			$res=parseResponse($responseXml);
			if ($res->count != count($firstContacts) || $res->nbOfErrors)
			{
				dol_syslog("Failed to batch insert nb of errors=".$res->nbOfErrors." lasterror=".$res->lastError, LOG_ERR);
				return sprintf("Google error : %s", $res->lastError);
			}
			else
			{
				dol_syslog(sprintf("Inserting %d google contacts", count($firstContacts)));

				// Now update each record into database with external ref
				if (is_object($objectstatic))
				{
					$doctoparse = new DOMDocument("1.0", "utf-8");
					$doctoparse->loadXML($responseXml);
					$contentNodes = $doctoparse->getElementsByTagName("entry");
					foreach ($contentNodes as $node)
					{
						$titlenode = $node->getElementsByTagName("title"); $title=$titlenode->item(0)->textContent;
						$idnode = $node->getElementsByTagName("id"); $id=$idnode->item(0)->textContent;
						$userdefinednode = $node->getElementsByTagName("userDefinedField");
						$userdefined=$userdefinednode->item(0)->getAttribute('value');
						if (! empty($idnode) && preg_match('/^(\d+)\/(.*)/',$userdefined,$reg))
						{
							if (! empty($reg[2]))
							{
								$objectstatic->id=$reg[1];
								$objectstatic->update_ref_ext($id);
							}
						}
					}
				}
			}
		}
		catch (Exception $e)
		{
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

	foreach ($contentNodes as $node)
	{
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
 * @param	string	$useremail		User email
 * @return 	string					Google Group ID for groupName.
 */
function getGoogleGroupID($gdata, $groupName, &$googleGroups=array(), $useremail='default')
{
	global $conf;

	// Search existing groups
	if (! is_array($googleGroups) || count($googleGroups) == 0)
	{
		$document = new DOMDocument("1.0", "utf-8");
		$xmlStr = getContactGroupsXml($gdata, $useremail);
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
		$newGroupID = insertGContactGroup($gdata, $groupName, $useremail);
		$googleGroups[$groupName] = $newGroupID;
	}
	return $googleGroups[$groupName];
}


/**
 * Retreive a Xml feed of contacts groups from Google
 *
 * @param	Mixed	$gdata			GData handler
 * @param	string	$useremail		User email
 * @return	string					XML string with all groups
 */
function getContactGroupsXml($gdata, $useremail='default')
{
	global $conf;
	global $tag_debug;

	$tag_debug='groupgroups';

	try {
		$query = new Zend_Gdata_Query('https://www.google.com/m8/feeds/groups/'.$useremail.'/full?max-results=1000');
		$feed = $gdata->getFeed($query);
		$xmlStr = $feed->getXML();
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_groups.xml' command
	} catch (Exception $e) {
		dol_syslog(sprintf("Error while feed xml groups : %s", $e->getMessage()), LOG_ERR);
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $e->getMessage());
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
	}
	return($xmlStr);
}



/**
 * Create a group/label into Google contact
 *
 * @param	Mixed	$gdata			GData handler
 * @param 	string 	$groupName		Group name to create into Google Contact
 * @param	string	$useremail		User email
 * @return 	string					googlegroupID
 */
function insertGContactGroup($gdata,$groupName,$useremail='default')
{
	global $tag_debug;

	$tag_debug='createcontact';

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
		$entryResult = $gdata->insertEntry($xmlStr, 'https://www.google.com/m8/feeds/groups/'.$useremail.'/full');
		dol_syslog(sprintf("Inserting gContact group %s in google contacts for google ID = %s", $groupName, $entryResult->id));
	} catch (Exception $e) {
		dol_syslog(sprintf("Problem while inserting group %s : %s", $groupName, $e->getMessage()), LOG_ERR);
	}

	return($entryResult->id);
}






/**
 * Convert all text entities in a string to numeric entities
 * With XML, only &lt; &gt; and &amp; are allowed.
 *
 * @param	string	$string		String to encode
 * @return	string				Modified string
 */
function google_html_convert_entities($string)
{
  	return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/S', '_google_convert_entity', $string);
}

/**
 * Swap HTML named entity with its numeric equivalent. If the entity
 * isn't in the lookup table, this function returns a blank, which
 * destroys the character in the output - this is probably the desired behaviour when producing XML.
 * List available on page http://www.w3.org/TR/REC-html40/sgml/entities.html
 *
 * @param	string	$matches	String to check and modify
 * @return	string				Modified string
 */
function _google_convert_entity($matches)
{
	static $table = array('quot'    => '&#34;',
                        'amp'      => '&#38;',
                        'lt'       => '&#60;',
                        'gt'       => '&#62;',
                        'OElig'    => '&#338;',
                        'oelig'    => '&#339;',
                        'Scaron'   => '&#352;',
                        'scaron'   => '&#353;',
                        'Yuml'     => '&#376;',
                        'circ'     => '&#710;',
                        'tilde'    => '&#732;',
                        'ensp'     => '&#8194;',
                        'emsp'     => '&#8195;',
                        'thinsp'   => '&#8201;',
                        'zwnj'     => '&#8204;',
                        'zwj'      => '&#8205;',
                        'lrm'      => '&#8206;',
                        'rlm'      => '&#8207;',
                        'ndash'    => '&#8211;',
                        'mdash'    => '&#8212;',
                        'lsquo'    => '&#8216;',
                        'rsquo'    => '&#8217;',
                        'sbquo'    => '&#8218;',
                        'ldquo'    => '&#8220;',
                        'rdquo'    => '&#8221;',
                        'bdquo'    => '&#8222;',
                        'dagger'   => '&#8224;',
                        'Dagger'   => '&#8225;',
                        'permil'   => '&#8240;',
                        'lsaquo'   => '&#8249;',
                        'rsaquo'   => '&#8250;',
                        'euro'     => '&#8364;',
                        'fnof'     => '&#402;',
                        'Alpha'    => '&#913;',
                        'Beta'     => '&#914;',
                        'Gamma'    => '&#915;',
                        'Delta'    => '&#916;',
                        'Epsilon'  => '&#917;',
                        'Zeta'     => '&#918;',
                        'Eta'      => '&#919;',
                        'Theta'    => '&#920;',
                        'Iota'     => '&#921;',
                        'Kappa'    => '&#922;',
                        'Lambda'   => '&#923;',
                        'Mu'       => '&#924;',
                        'Nu'       => '&#925;',
                        'Xi'       => '&#926;',
                        'Omicron'  => '&#927;',
                        'Pi'       => '&#928;',
                        'Rho'      => '&#929;',
                        'Sigma'    => '&#931;',
                        'Tau'      => '&#932;',
                        'Upsilon'  => '&#933;',
                        'Phi'      => '&#934;',
                        'Chi'      => '&#935;',
                        'Psi'      => '&#936;',
                        'Omega'    => '&#937;',
                        'alpha'    => '&#945;',
                        'beta'     => '&#946;',
                        'gamma'    => '&#947;',
                        'delta'    => '&#948;',
                        'epsilon'  => '&#949;',
                        'zeta'     => '&#950;',
                        'eta'      => '&#951;',
                        'theta'    => '&#952;',
                        'iota'     => '&#953;',
                        'kappa'    => '&#954;',
                        'lambda'   => '&#955;',
                        'mu'       => '&#956;',
                        'nu'       => '&#957;',
                        'xi'       => '&#958;',
                        'omicron'  => '&#959;',
                        'pi'       => '&#960;',
                        'rho'      => '&#961;',
                        'sigmaf'   => '&#962;',
                        'sigma'    => '&#963;',
                        'tau'      => '&#964;',
                        'upsilon'  => '&#965;',
                        'phi'      => '&#966;',
                        'chi'      => '&#967;',
                        'psi'      => '&#968;',
                        'omega'    => '&#969;',
                        'thetasym' => '&#977;',
                        'upsih'    => '&#978;',
                        'piv'      => '&#982;',
                        'bull'     => '&#8226;',
                        'hellip'   => '&#8230;',
                        'prime'    => '&#8242;',
                        'Prime'    => '&#8243;',
                        'oline'    => '&#8254;',
                        'frasl'    => '&#8260;',
                        'weierp'   => '&#8472;',
                        'image'    => '&#8465;',
                        'real'     => '&#8476;',
                        'trade'    => '&#8482;',
                        'alefsym'  => '&#8501;',
                        'larr'     => '&#8592;',
                        'uarr'     => '&#8593;',
                        'rarr'     => '&#8594;',
                        'darr'     => '&#8595;',
                        'harr'     => '&#8596;',
                        'crarr'    => '&#8629;',
                        'lArr'     => '&#8656;',
                        'uArr'     => '&#8657;',
                        'rArr'     => '&#8658;',
                        'dArr'     => '&#8659;',
                        'hArr'     => '&#8660;',
                        'forall'   => '&#8704;',
                        'part'     => '&#8706;',
                        'exist'    => '&#8707;',
                        'empty'    => '&#8709;',
                        'nabla'    => '&#8711;',
                        'isin'     => '&#8712;',
                        'notin'    => '&#8713;',
                        'ni'       => '&#8715;',
                        'prod'     => '&#8719;',
                        'sum'      => '&#8721;',
                        'minus'    => '&#8722;',
                        'lowast'   => '&#8727;',
                        'radic'    => '&#8730;',
                        'prop'     => '&#8733;',
                        'infin'    => '&#8734;',
                        'ang'      => '&#8736;',
                        'and'      => '&#8743;',
                        'or'       => '&#8744;',
                        'cap'      => '&#8745;',
                        'cup'      => '&#8746;',
                        'int'      => '&#8747;',
                        'there4'   => '&#8756;',
                        'sim'      => '&#8764;',
                        'cong'     => '&#8773;',
                        'asymp'    => '&#8776;',
                        'ne'       => '&#8800;',
                        'equiv'    => '&#8801;',
                        'le'       => '&#8804;',
                        'ge'       => '&#8805;',
                        'sub'      => '&#8834;',
                        'sup'      => '&#8835;',
                        'nsub'     => '&#8836;',
                        'sube'     => '&#8838;',
                        'supe'     => '&#8839;',
                        'oplus'    => '&#8853;',
                        'otimes'   => '&#8855;',
                        'perp'     => '&#8869;',
                        'sdot'     => '&#8901;',
                        'lceil'    => '&#8968;',
                        'rceil'    => '&#8969;',
                        'lfloor'   => '&#8970;',
                        'rfloor'   => '&#8971;',
                        'lang'     => '&#9001;',
                        'rang'     => '&#9002;',
                        'loz'      => '&#9674;',
                        'spades'   => '&#9824;',
                        'clubs'    => '&#9827;',
                        'hearts'   => '&#9829;',
                        'diams'    => '&#9830;',
                        'nbsp'     => '&#160;',
                        'iexcl'    => '&#161;',
                        'cent'     => '&#162;',
                        'pound'    => '&#163;',
                        'curren'   => '&#164;',
                        'yen'      => '&#165;',
                        'brvbar'   => '&#166;',
                        'sect'     => '&#167;',
                        'uml'      => '&#168;',
                        'copy'     => '&#169;',
                        'ordf'     => '&#170;',
                        'laquo'    => '&#171;',
                        'not'      => '&#172;',
                        'shy'      => '&#173;',
                        'reg'      => '&#174;',
                        'macr'     => '&#175;',
                        'deg'      => '&#176;',
                        'plusmn'   => '&#177;',
                        'sup2'     => '&#178;',
                        'sup3'     => '&#179;',
                        'acute'    => '&#180;',
                        'micro'    => '&#181;',
                        'para'     => '&#182;',
                        'middot'   => '&#183;',
                        'cedil'    => '&#184;',
                        'sup1'     => '&#185;',
                        'ordm'     => '&#186;',
                        'raquo'    => '&#187;',
                        'frac14'   => '&#188;',
                        'frac12'   => '&#189;',
                        'frac34'   => '&#190;',
                        'iquest'   => '&#191;',
                        'Agrave'   => '&#192;',
                        'Aacute'   => '&#193;',
                        'Acirc'    => '&#194;',
                        'Atilde'   => '&#195;',
                        'Auml'     => '&#196;',
                        'Aring'    => '&#197;',
                        'AElig'    => '&#198;',
                        'Ccedil'   => '&#199;',
                        'Egrave'   => '&#200;',
                        'Eacute'   => '&#201;',
                        'Ecirc'    => '&#202;',
                        'Euml'     => '&#203;',
                        'Igrave'   => '&#204;',
                        'Iacute'   => '&#205;',
                        'Icirc'    => '&#206;',
                        'Iuml'     => '&#207;',
                        'ETH'      => '&#208;',
                        'Ntilde'   => '&#209;',
                        'Ograve'   => '&#210;',
                        'Oacute'   => '&#211;',
                        'Ocirc'    => '&#212;',
                        'Otilde'   => '&#213;',
                        'Ouml'     => '&#214;',
                        'times'    => '&#215;',
                        'Oslash'   => '&#216;',
                        'Ugrave'   => '&#217;',
                        'Uacute'   => '&#218;',
                        'Ucirc'    => '&#219;',
                        'Uuml'     => '&#220;',
                        'Yacute'   => '&#221;',
                        'THORN'    => '&#222;',
                        'szlig'    => '&#223;',
                        'agrave'   => '&#224;',
                        'aacute'   => '&#225;',
                        'acirc'    => '&#226;',
                        'atilde'   => '&#227;',
                        'auml'     => '&#228;',
                        'aring'    => '&#229;',
                        'aelig'    => '&#230;',
                        'ccedil'   => '&#231;',
                        'egrave'   => '&#232;',
                        'eacute'   => '&#233;',
                        'ecirc'    => '&#234;',
                        'euml'     => '&#235;',
                        'igrave'   => '&#236;',
                        'iacute'   => '&#237;',
                        'icirc'    => '&#238;',
                        'iuml'     => '&#239;',
                        'eth'      => '&#240;',
                        'ntilde'   => '&#241;',
                        'ograve'   => '&#242;',
                        'oacute'   => '&#243;',
                        'ocirc'    => '&#244;',
                        'otilde'   => '&#245;',
                        'ouml'     => '&#246;',
                        'divide'   => '&#247;',
                        'oslash'   => '&#248;',
                        'ugrave'   => '&#249;',
                        'uacute'   => '&#250;',
                        'ucirc'    => '&#251;',
                        'uuml'     => '&#252;',
                        'yacute'   => '&#253;',
                        'thorn'    => '&#254;',
                        'yuml'     => '&#255;'

                        );
  // Entity not found? Destroy it.
  return isset($table[$matches[1]]) ? $table[$matches[1]] : '';
}
