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

dol_include_once("/google/lib/google.lib.php");
include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';


define('ATOM_NAME_SPACE', 'http://www.w3.org/2005/Atom');
define('GD_NAME_SPACE', 'http://schemas.google.com/g/2005');
define('GCONTACT_NAME_SPACE', 'http://schemas.google.com/contact/2008');

define('REL_WORK', 'http://schemas.google.com/g/2005#work');
define('REL_HOME', 'http://schemas.google.com/g/2005#home');
define('REL_MOBILE', 'http://schemas.google.com/g/2005#mobile');
define('REL_WORK_FAX', 'http://schemas.google.com/g/2005#work_fax');



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
 * @param  array	$client   		Service array with authenticated client object
 * @param  string	$object	   		Source object into Dolibarr
 * @param  string	$useremail		User email
 * @return string 					The ID URL for the contact or 'ERROR xxx' if error.
 */
function googleCreateContact($client, $object, $useremail = 'default')
{
	global $conf, $db, $langs;
	global $dolibarr_main_url_root;
	global $user;

	global $conf,$langs;
	global $tag_debug;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

	dol_syslog('googleCreateContact object->id='.$object->id.' type='.$object->element);

	$google_nltechno_tag=getCommentIDTag();

	$doc  = new DOMDocument("1.0", "utf-8");
	try {
		// perform login and set protocol version to 3.0
		$gdata=$client;

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
		//$object->email='';	$object->url=''; $object->address=''; $object->zip=''; $object->town=''; $object->note_public=''; unset($object->country_id);


		// Name
		$name = $doc->createElement('gd:name');
		$entry->appendChild($name);
		if ($object->element != 'societe' && $object->element != 'thirdparty') {
			$fullName = $doc->createElement('gd:fullName', $object->getFullName($langs));
			// TODO Add givenName and familyName
		} else {
			$fullName = $doc->createElement('gd:fullName', dolEscapeXML($object->name));
		}
		$name->appendChild($fullName);

		// Element
		$email = $doc->createElement('gd:email');
		$email->setAttribute('address', ($object->email?$object->email:(strtolower(preg_replace('/\s/', '', (empty($object->name)?$object->lastname.$object->firstname:$object->name)).'@noemail.com'))));
		$email->setAttribute('rel', constant('REL_WORK'));
		$entry->appendChild($email);

		// Address
		$address = $doc->createElement('gd:structuredPostalAddress');
		$address->setAttribute('rel', 'http://schemas.google.com/g/2005#work');
		$address->setAttribute('primary', 'true');
		$entry->appendChild($address);

			$city = $doc->createElement('gd:city', dolEscapeXML($object->town));
			if (! empty($object->town))	$address->appendChild($city);
			$street = $doc->createElement('gd:street', dolEscapeXML($object->address));
			if (! empty($object->address)) $address->appendChild($street);
			$postcode = $doc->createElement('gd:postcode', dolEscapeXML($object->zip));
			if (! empty($object->zip))	    $address->appendChild($postcode);

			$tmpstate=($object->state_id>0?getState($object->state_id):'');
			$tmpstate=dol_html_entity_decode($tmpstate, ENT_QUOTES);	// Should not be required. It is here because some bugged version of getState return a string with entities instead of utf8 with no entities
			$region = $doc->createElement('gd:region', dolEscapeXML($tmpstate));
			if ($tmpstate) $address->appendChild($region);

			$tmpcountry=getCountry($object->country_id, 0, '', $langs, 0);
			$country = $doc->createElement('gd:country', dolEscapeXML($tmpcountry));
			if ($tmpcountry) $address->appendChild($country);
			/*
			$formattedaddress = $doc->createElement('gd:formattedAddress', 'eeeee');
			$address->appendChild($formattedaddress);
			*/

		// Company - Function
		if ($object->element == 'contact') {
			// Company
			$company = $doc->createElement('gd:organization');
			$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
			$entry->appendChild($company);

			$object->fetch_thirdparty();
			if (! empty($object->thirdparty->name) || ! empty($object->poste)) {   // Job position and company name of contact
				$thirdpartyname=$object->thirdparty->name;

				$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
				if (! empty($thirdpartyname)) $company->appendChild($orgName);
				$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
				if (! empty($object->poste)) $company->appendChild($orgTitle);
			}
		}
		if ($object->element == 'member') {
			// Company
			$company = $doc->createElement('gd:organization');
			$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
			$entry->appendChild($company);

			//$object->fetch_thirdparty();
			if (! empty($object->company)) {
				$thirdpartyname=$object->company;

				$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
				if (! empty($thirdpartyname)) $company->appendChild($orgName);
				//$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
				//if (! empty($object->poste)) $company->appendChild($orgTitle);
			}
		}

		// Birthday
		if (! empty($object->birthday)) {
			$birthday = $doc->createElement('gcontact:birthday');
			$birthday->setAttribute('when', dol_print_date($object->birthday, 'dayrfc'));
			$entry->appendChild($birthday);
		}

		// URL
		if (! empty($object->url)) {
			$el = $doc->createElement('gcontact:website');
			$el->setAttribute("label", "URL");
			$el->setAttribute("href", $object->url);
			$entry->appendChild($el);
		}

		// Phones
		if (! empty($object->phone)) {
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_WORK'));
			$el->appendChild($doc->createTextNode($object->phone));
			$entry->appendChild($el);
		}
		if (! empty($object->phone_pro)) {
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_WORK'));
			$el->appendChild($doc->createTextNode($object->phone_pro));
			$entry->appendChild($el);
		}
		if (! empty($object->phone_perso)) {
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_HOME'));
			$el->appendChild($doc->createTextNode($object->phone_perso));
			$entry->appendChild($el);
		}
		if (! empty($object->phone_mobile)) {
			$el = $doc->createElement('gd:phoneNumber');
			$el->setAttribute('rel', constant('REL_MOBILE'));
			$el->appendChild($doc->createTextNode($object->phone_mobile));
			$entry->appendChild($el);
		}
		if (! empty($object->fax)) {
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
		$userdefined->setAttribute('key', 'dolibarr-id');
		$userdefined->setAttribute('value', $idindolibarr);
		$entry->appendChild($userdefined);

		$userdefined = $doc->createElement('gcontact:userDefinedField');
		$userdefined->setAttribute('key', 'dolibarr-date-create');
		$userdefined->setAttribute('value', dol_print_date(dol_now(), 'dayrfc'));
		$entry->appendChild($userdefined);

		// TODO Add other dolibarr fields
		//...

		// Comment
		$tmpnote=$object->note_public;
		if (strpos($tmpnote, $google_nltechno_tag) === false) $tmpnote.="\n\n".$google_nltechno_tag.$idindolibarr;
		$note = $doc->createElement('atom:content', google_html_convert_entities($tmpnote));
		$entry->appendChild($note);

		// Labels
		$googleGroups=array();
		$groupid = getGoogleGroupID($gdata, $groupName, $googleGroups, $useremail);
		if (empty($groupid) || $groupid == 'ErrorFailedToGetGroups') {
			return 0;
		}

		$el = $doc->createElement("gcontact:groupMembershipInfo");
		$el->setAttribute("deleted", "false");
		$el->setAttribute("href", $groupid);
		$entry->appendChild($el);

		$tag_debug='createcontact';

		//To list all existing field we can edit: var_dump($doc->saveXML());exit;
		$xmlStr = $doc->saveXML();
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_createcontact.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_createcontact.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_createcontact.xml' command

		$id = '';


		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'Content-Type'=>'application/atom+xml');
		$addheaderscurl=array('GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/atom+xml');

		// insert entry
		//$entryResult = $gdata->insertEntry($xmlStr,	'https://www.google.com/m8/feeds/contacts/'.$useremail.'/full');
		$response = getURLContent('https://www.google.com/m8/feeds/contacts/'.$useremail.'/full', 'POST', $xmlStr, 1, $addheaderscurl);
		if ($response['content'] && $response['content'] != 'Not found') {
			try {
				$document = new DOMDocument("1.0", "utf-8");
				$document->loadXml($response['content']);

				$errorselem = $document->getElementsByTagName("errors");
				//var_dump($errorselem);
				//var_dump($errorselem->length);
				//var_dump(count($errorselem));
				if ($errorselem->length) {
					dol_syslog($response['content'], LOG_ERR);
					return 'ERROR: Creation of record on Google returns an error';
				}

				$entries = $document->getElementsByTagName("id");
				foreach ($entries as $entry) {
					$id = basename($entry->nodeValue);
					break;
				}
			} catch (Exception $e) {
				die('ERROR:' . $e->getMessage());
			}
		}


		//var_dump($doc->saveXML());exit;
		//echo 'The id of the new entry is: ' . $entryResult->getId().'<br>';

		return $id;
	} catch (Exception $e) {
		die('ERROR:' . $e->getMessage());
	}
}




/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param  array		$client   		Array with tokens informations
 * @param  string       $contactId  	The ref into Google contact
 * @param  Object       $object			Object
 * @param  string		$useremail		User email
 * @return string						Google ref ID if OK, 0 if not found, <0 if KO
 */
function googleUpdateContact($client, $contactId, &$object, $useremail = 'default')
{
	global $conf, $db, $langs;
	global $tag_debug;

	$newcontactid=$contactId;
	$reg = array();
	if (preg_match('/google\.com\/.*\/([^\/]+)$/', $contactId, $reg)) {
		$newcontactid=$reg[1];
	}
	if (preg_match('/google:([^\/]+)$/', $contactId, $reg)) {
		$newcontactid=$reg[1];	// TODO This may not be enough because ID in dolibarr is 250 char max and in google may have 1024 chars
	}

	$tag_debug='updatecontact';

	dol_syslog('googleUpdateContact object->id='.$object->id.' type='.$object->element.' ref_ext='.$object->ref_ext.' contactid='.$newcontactid);

	include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

	$google_nltechno_tag=getCommentIDTag();

	// Fields: http://tools.ietf.org/html/rfc4287
	$gdata=$client;

	try {
		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token);
		$addheaderscurl=array('Content-Type: application/atom+xml','GData-Version: 3.0', 'Authorization: Bearer '.$access_token);
		//$useremail='default';

		//$request=new Google_Http_Request('https://www.google.com/m8/feeds/groups/'.urlencode($useremail).'/full?max-results=1000', 'GET', $addheaders, null);
		//$result=$gdata['client']->execute($request);	// Return json_decoded string. May return an exception.
		//$xmlStr=$result;

		$result = getURLContent('https://www.google.com/m8/feeds/contacts/'.urlencode($useremail).'/base/'.$newcontactid, 'GET', '', 0, $addheaderscurl);
		$xmlStr=$result['content'];

		/*if (empty($xmlStr))
		{
			print "Something is wrong. The getURLContent to Google return an empty string\n";
		}*/

		//$contactId='https://www.google.com/m8/feeds/contacts/eldy10%40gmail.com/base/4429b3590f5b343a';
		//$contactId='https://www.google.com/m8/feeds/contacts/contact%40nltechno.com/base/ee6fc620dbab6d7';
		try {
			$document = new DOMDocument("1.0", "utf-8");
			$document->loadXml($result['content']);

			$errorselem = $document->getElementsByTagName("errors");
			//var_dump($errorselem);
			//var_dump($errorselem->length);
			//var_dump(count($errorselem));
			if ($errorselem->length) {
				if (preg_match('/<code>notFound<\/code>/', $result['content'])) {
					dol_syslog('Google server return message '.$result['content'].' so we leave with code 0', LOG_DEBUG);
					return 0;
				}
				//dol_syslog('ERROR: '.$errorselem->item(0)->nodeValue, LOG_ERR);
				dol_syslog('ERROR:'.$result['content'], LOG_ERR);
				$object->error = $result['content'];
				return -1;
			}
		} catch (Exception $e) {
			dol_syslog('ERROR:'.$e->getMessage(), LOG_ERR);
			$object->error = $e->getMessage();
			return -1;
		}
	} catch (Exception $e) {
		dol_syslog('ERROR:'.$e->getMessage(), LOG_ERR);
		return -1;
	}

	// Warning, with google apps, if link start with http instead of http it will fails too, but with error 401 !
	if ($result['curl_error_no'] == '404') {
		// Not found error.
		dol_syslog('Failed to get Google record with ref='.$newcontactid.' '.$result['curl_error_msg'], LOG_WARNING);
		$object->error = 'Failed to get Google record with ref='.$newcontactid.', contactId='.$contactId;
		return 0;
	} elseif ($result['curl_error_no']) {
		dol_syslog('Failed to get Google record with ref='.$newcontactid.' '.$result['curl_error_msg'], LOG_WARNING);
		$object->error = 'Failed to get Google record with ref='.$newcontactid.', contactId='.$contactId;
		return -1;
	} elseif (is_string($xmlStr) && $xmlStr == 'Contact not found.') {
		// Not found error.
		//print 'Failed to get record with ref='.$newcontactid.' '.$result['curl_error_msg'];exit;
		dol_syslog('Failed to get Google record with ref='.$newcontactid, LOG_WARNING);
		$object->error = 'Failed to get Google record with ref='.$newcontactid.', contactId='.$contactId;
		return 0;
	}
	$id = '';

	try {
		$xmlgcontact = simplexml_load_string($xmlStr, null, 0, 'gContact', true);
		$xmlgd = simplexml_load_string($xmlStr, null, 0, 'gd', true);
		$xml = simplexml_load_string($xmlStr);
		//var_dump($xml);
		if ($object->element != 'societe' && $object->element != 'thirdparty') {
			$fullNameToUse = $object->getFullName($langs);
		} else {
			$fullNameToUse = $object->name;
		}
		$xml->name->fullName = $fullNameToUse;
		if (! empty($object->firstname))  $xml->name->givenName = $object->firstname;
		if (! empty($object->lastname))   $xml->name->familyName = $object->lastname;
		//$xml->name->additionnalName = 'xxx';
		//$xml->name->nameSuffix = 'xxx';
		//$xml->formattedAddress;
		$xml->email['address'] = ($object->email?$object->email:(strtolower(preg_replace('/\s/', '', (empty($object->name)?$object->lastname.$object->firstname:$object->name))).'@noemail.com'));

		// Address
		unset($xml->structuredPostalAddress->formattedAddress);
		$xml->structuredPostalAddress->street=$object->address;
		$xml->structuredPostalAddress->city=$object->town;
		$xml->structuredPostalAddress->postcode=$object->zip;
		$xml->structuredPostalAddress->country=($object->country_id>0?getCountry($object->country_id, 0, '', $langs, 0):'');
		$tmpstate=($object->state_id>0?getState($object->state_id):'');
		$tmpstate=dol_html_entity_decode($tmpstate, ENT_QUOTES);	// Should not be required. It is here because some bugged version of getState return a string with entities instead of utf8 with no entities
		$xml->structuredPostalAddress->region=$tmpstate;
		//var_dump($xml->organization->asXml());    // $xml->organization is SimpleXMLElement but isset($xml->organization) and $xml->organization->asXml() may be set or not
		// Company + Function
		if (isset($xml->organization)) {
			if ($object->element == 'contact') {
				unset($xml->organization->orgName);
				unset($xml->organization->orgTitle);
				$object->fetch_thirdparty();
				if (! empty($object->thirdparty->name) || ! empty($object->poste)) {
					$thirdpartyname=$object->thirdparty->name;
					$xml->organization['rel']="http://schemas.google.com/g/2005#other";
					if (! empty($object->thirdparty->name)) $xml->organization->orgName=$thirdpartyname;
					if (! empty($object->poste)) $xml->organization->orgTitle=$object->poste;
				}
			}
			if ($object->element == 'member') {
				unset($xml->organization->orgName);
				unset($xml->organization->orgTitle);
				if (! empty($object->company)) {
					$thirdpartyname=$object->company;
					$xml->organization['rel']="http://schemas.google.com/g/2005#other";
					if (! empty($object->company)) $xml->organization->orgName=$thirdpartyname;
				}
			}
		}

		$newphone=empty($object->phone)?$object->phone_pro:$object->phone;

		// Phone(s)
		//var_dump($xml->asXML());
		//print_r($xml);
		unset($xml->phoneNumber);
		if ($newphone) simplexml_merge($xml, new SimpleXMLElement('<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"><phoneNumber xmlns="http://schemas.google.com/g/2005" rel="'.constant('REL_WORK').'">'.$newphone.'</phoneNumber></atom:entry>'));
		if ($object->phone_perso)  simplexml_merge($xml, new SimpleXMLElement('<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"><phoneNumber xmlns="http://schemas.google.com/g/2005" rel="'.constant('REL_HOME').'">'.$object->phone_perso.'</phoneNumber></atom:entry>'));
		if ($object->phone_mobile) simplexml_merge($xml, new SimpleXMLElement('<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"><phoneNumber xmlns="http://schemas.google.com/g/2005" rel="'.constant('REL_MOBILE').'">'.$object->phone_mobile.'</phoneNumber></atom:entry>'));
		if ($object->fax) simplexml_merge($xml, new SimpleXMLElement('<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"><phoneNumber xmlns="http://schemas.google.com/g/2005" rel="'.constant('REL_WORK_FAX').'">'.$object->fax.'</phoneNumber></atom:entry>'));
		//var_dump($xml->asXML());
		//var_dump($xml);
		//exit;

		// userDefinedField
		// We don't change this

		// Birthday (in namespace gdContact)
		if (isset($xmlgcontact) && isset($xmlgcontact->birthday) && $xmlgcontact->birthday->asXml()) {  // If entry found into current remote record, we can update it
			if ($object->birthday) $xml->birthday['when'] = dol_print_date($object->birthday, 'dayrfc');
			else { unset($xml->birthday); }
		}

		// Comment
		$tmpnote=$object->note_public;
		if (strpos($tmpnote, $google_nltechno_tag) === false) $tmpnote.="\n\n".$google_nltechno_tag.$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
		$xml->content=google_html_convert_entities($tmpnote);


		$xmlStr=$xml->saveXML();


		// Remove <gContact:groupMembershipInfo
		//print dol_escape_htmltag($xmlStr);
		$xmlStr = preg_replace('/<gContact:groupMembershipInfo[^>]*/', '', $xmlStr);
		//print dol_escape_htmltag($xmlStr);exit;


		// Convert xml into DOM so we can use dom function to add website element
		$doc  = new DOMDocument("1.0", "utf-8");
		$doc->loadXML($xmlStr);
		$entries = $doc->getElementsByTagName('entry');

		// Birthday (in namespace gdContact)
		if (! $xmlgcontact->birthday->asXml() && $object->birthday) {    // Not into current remote record, we add it if defined
			foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
				$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', constant('GCONTACT_NAME_SPACE'));
				$birthday = $doc->createElement('gcontact:birthday');
				$birthday->setAttribute('when', dol_print_date($object->birthday, 'dayrfc'));
				$entry->appendChild($birthday);
			}
		}

		// URL
		$oldurl='';
		if (! empty($object->oldcopy->url)) $oldurl=$object->oldcopy->url;
		// Removed old url
		foreach ($doc->getElementsByTagName('website') as $nodewebsite) {
			$linkurl = $nodewebsite->getAttribute('href');
			$labelurl = $nodewebsite->getAttribute('label');
			if ($linkurl == $oldurl) {	// Delete only if value on google match old value into Dolibarr
				$nodewebsite->parentNode->removeChild($nodewebsite);
			}
		}
		// Add new url
		if (! empty($object->url)) {
			foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
				$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', constant('GCONTACT_NAME_SPACE'));
				$el = $doc->createElement('gcontact:website');
				$el->setAttribute("label", "URL");
				$el->setAttribute("href", $object->url);
				$entry->appendChild($el);
			}
		}

		// Add company if object organization did not exists (so it was not updated)
		if (! isset($xml->organization)) {
			// Company - Function
			if ($object->element == 'contact') {
				foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
					// Company
					$company = $doc->createElement('gd:organization');
					$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
					$entry->appendChild($company);

					$object->fetch_thirdparty();
					if (! empty($object->thirdparty->name) || ! empty($object->poste)) {   // Job position and company name of contact
						$thirdpartyname=$object->thirdparty->name;

						$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
						if (! empty($thirdpartyname)) $company->appendChild($orgName);
						$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
						if (! empty($object->poste)) $company->appendChild($orgTitle);
					}
				}
			}
			if ($object->element == 'member') {
				foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
					// Company
					$company = $doc->createElement('gd:organization');
					$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
					$entry->appendChild($company);

					//$object->fetch_thirdparty();
					if (! empty($object->company)) {
						$thirdpartyname=$object->company;

						$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
						if (! empty($thirdpartyname)) $company->appendChild($orgName);
						//$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
						//if (! empty($object->poste)) $company->appendChild($orgTitle);
					}
				}
			}
		}

		/* Old code used when using SimpleXML object (not working)
			foreach ($xml->website as $key => $val) {	// $key='@attributes' $val is an array('href'=>,'label'=>), however to set href it we must do $xml->website['href'] (it's a SimpleXML object)
				$oldvalue=(string) $val['href'];
				if (! empty($object->url)) $xml->website['href'] = $object->url;
				else unset($xml->website);
			}
		*/
		//var_dump($xmlStr);exit;

		$xmlStr=$doc->saveXML();


		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_updatecontact.xml' command

		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('If-Match'=>'*', 'GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'Content-Type'=>'application/atom+xml');
		$addheaderscurl=array('If-Match: *', 'GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/atom+xml');

		// update entry
		$response = getURLContent('https://www.google.com/m8/feeds/contacts/'.urlencode($useremail).'/base/'.$newcontactid, 'PUTALREADYFORMATED', $xmlStr, 1, $addheaderscurl);
		if ($response['content']) {
			try {
				$document = new DOMDocument("1.0", "utf-8");
				$document->loadXml($response['content']);

				$errorselem = $document->getElementsByTagName("errors");
				//var_dump($errorselem);
				//var_dump($errorselem->length);
				//var_dump(count($errorselem));
				if ($errorselem->length) {
					//dol_syslog('ERROR: '.$errorselem->item(0)->nodeValue, LOG_ERR);
					dol_syslog('ERROR:'.$response['content'], LOG_ERR);
					$object->error=$response['content'];
					return -1;
				}

				$entries = $document->getElementsByTagName("id");
				foreach ($entries as $entry) {
					$id = basename($entry->nodeValue);
					break;
				}
			} catch (Exception $e) {
				die('ERROR:' . $e->getMessage());
			}
		}

		//List of properties to set visible with var_dump($xml->saveXML());exit;
		//$extra_header = array('If-Match'=>'*');
		//$newentryResult = $gdata->updateEntry($xmlStr, $entryResult->getEditLink()->href, null, $extra_header);
	} catch (Exception $e) {
		// Exemple: "Expected response code 200, got 400 GData invalid website link must not be empty"
		dol_syslog('Failed to update google record: '.$e->getMessage(), LOG_ERR);
		return -1;
	}

	return $id;
}



/**
 * Deletes the event specified by retrieving the atom entry object
 * and calling Zend_Feed_EntryAtom::delete() method.  This is for
 * example purposes only, as it is inefficient to retrieve the entire
 * atom entry only for the purposes of deleting it.
 *
 * @param  array		$client   		Array with tokens informations
 * @param  string       $ref			The ref string
 * @param  string		$useremail		User email
 * @return string 						'' if OK, error message if KO
 */
function googleDeleteContactByRef($client, $ref, $useremail = 'default')
{
	global $conf, $db, $langs;
	global $tag_debug;

	$newcontactid=$ref;
	if (preg_match('/google\.com\/.*\/([^\/]+)$/', $ref, $reg)) {
		$newcontactid=$reg[1];
	}
	if (preg_match('/google:([^\/]+)$/', $ref, $reg)) {
		$newcontactid=$reg[1];	// TODO This may not be enough because ID in dolibarr is 250 char max and in google may have 1024 chars
	}

	dol_syslog('googleDeleteContactByRef Gcontact ref to delete='.$newcontactid);

	$gdata=$client;

	try {
		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'If-Match: *', 'Authorization'=>'Bearer '.$access_token);
		$addheaderscurl=array('GData-Version: 3.0', 'If-Match: *', 'Authorization: Bearer '.$access_token);
		//$useremail='default';

		$result = getURLContent('https://www.google.com/m8/feeds/contacts/'.urlencode($useremail).'/full/'.$newcontactid, 'DELETE', '', 0, $addheaderscurl);
		$xmlStr=$result['content'];

		return '';
	} catch (Exception $e) {
		return $e->getMessage();
	}
}





/**
 * Mass insert of several contacts into a google account
 * Use API v2 batch
 *
 * @param 	mixed	$gdata			Handler of Gdata connexion
 * @param 	array 	$gContacts		Array of object GContact
 * @param	mixed	$objectstatic	Object static to update ref_ext of records if success
 * @param	string	$useremail		User email
 * @return	int						>0 if OK, 'error string' if error
 * @see		insertGCalsEntries 		(same function for contacts)
 */
function insertGContactsEntries($gdata, $gContacts, $objectstatic, $useremail = 'default')
{
	global $conf;

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
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', constant('ATOM_NAME_SPACE'));
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');
		$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:batch', 'http://schemas.google.com/gdata/batch');
		$feed->appendChild($doc->createElement("title", "The batch title: insert contacts"));
		$doc->appendChild($feed);
		foreach ($firstContacts as $gContact) {
			//print_r($gContact);
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
			/* HERE AN EXAMPLE STRING */

			// Exemple of a group id: https://www.google.com/m8/feeds/groups/eldy10%40gmail.com/base/766e9f670b5f327a
			/*
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
			<gcontact:groupMembershipInfo deleted="false" href="https://www.google.com/m8/feeds/groups/eldy10%40gmail.com/base/766e9f670b5f327a"/>
			<batch:operation type="insert"/>
			</atom:entry>
			</atom:feed>
			END;
			*/

			// Convert text entities into numeric entities
			$xmlStr = google_html_convert_entities($xmlStr);

			if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
				$access_token=$gdata['google_web_token']['access_token'];
			} else {
				$tmp=json_decode($gdata['google_web_token']);
				$access_token=$tmp->access_token;
			}
			$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
			$addheaderscurl=array('Content-Type: application/atom+xml','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');

			//$request=new Google_Http_Request('https://www.google.com/m8/feeds/contacts/default/base/batch', 'POST', $addheaders, $xmlStr);
			//$requestData = $gdata['client']->execute($request);
			$result = getURLContent('https://www.google.com/m8/feeds/contacts/'.$useremail.'/full/batch', 'POST', $xmlStr, 0, $addheaderscurl);
			$xmlStr=$result['content'];
			try {
				$document = new DOMDocument("1.0", "utf-8");
				$document->loadXml($result['content']);

				$errorselem = $document->getElementsByTagName("errors");
				//var_dump($errorselem);
				//var_dump($errorselem->length);
				//var_dump(count($errorselem));
				if ($errorselem->length) {
					dol_syslog('ERROR:'.$result['content'], LOG_ERR);
					return -1;
				}
			} catch (Exception $e) {
				dol_syslog('ERROR:'.$e->getMessage(), LOG_ERR);
				return -1;
			}

			$responseXml = $xmlStr;

			// uncomment for debugging :
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.xml", $responseXml);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
			// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert_response.xml' command
			$res=parseResponse($responseXml);
			if ($res->count != count($firstContacts) || $res->nbOfErrors) {
				dol_syslog("Failed to batch insert count=".$res->count.", count(firstContacts)=".count($firstContacts).", nb of errors=".$res->nbOfErrors.", lasterror=".$res->lastError, LOG_ERR);
				return sprintf("Google error : Nb of records to insert = %s, nb inserted = %s, error label = %s", count($firstContacts), $res->count, $res->lastError);
			} else {
				dol_syslog(sprintf("Inserting %d google contacts", count($firstContacts)));

				// Now update each record into database with external ref
				if (is_object($objectstatic)) {
					$doctoparse = new DOMDocument("1.0", "utf-8");
					$doctoparse->loadXML($responseXml);
					$contentNodes = $doctoparse->getElementsByTagName("entry");
					foreach ($contentNodes as $node) {
						$titlenode = $node->getElementsByTagName("title"); $title=$titlenode->item(0)->textContent;
						$idnode = $node->getElementsByTagName("id"); $id=$idnode->item(0)->textContent;
						$userdefinednode = $node->getElementsByTagName("userDefinedField");
						$userdefined=$userdefinednode->item(0)->getAttribute('value');
						if (! empty($idnode) && preg_match('/^(\d+)\/(.*)/', $userdefined, $reg)) {
							if (! empty($reg[2])) {
								$objectstatic->id=$reg[1];
								$objectstatic->update_ref_ext($id);
							}
						}
					}
				}
			}
		} catch (Exception $e) {
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
		if ($title->length==1 && ($title->item(0)->textContent=='Error' || $title->item(0)->textContent=='Fatal Error')) {
			$res->nbOfErrors++;
			$content = $node->getElementsByTagName("content");
			//$batchinter = $node->getElementsByTagName("batch");
			//$reason = $batchinter->item(0)->getAttribute("reason");
			//var_dump($reason);
			if ($content->length>0) {
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
 * @param	array	$gdata			Array with tokens info
 * @param	string	$groupName		Group name
 * @param	array	$googleGroups	Array of Google Group we know they already exists
 * @param	string	$useremail		User email
 * @return 	string					Google Group Full URL ID for groupName (also key in $googleGroups) or 'ErrorFailedToGetGroups'.
 */
function getGoogleGroupID($gdata, $groupName, &$googleGroups = array(), $useremail = 'default')
{
	global $conf;

	$error=0;

	// Search existing groups
	if (! is_array($googleGroups) || count($googleGroups) == 0) {
		$document = new DOMDocument("1.0", "utf-8");
		$xmlStr = getContactGroupsXml($gdata, $useremail);
		if (! empty($xmlStr)) {
			$resultloadxml = $document->loadXML($xmlStr);
			if ($resultloadxml === false) {
				dol_syslog("getGoogleGroupID Failed to parse xml string ".$xmlStr, LOG_WARNING);
			} else {
				$xmlStr = $document->saveXML();
				$entries = $document->documentElement->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "entry");
				$n = $entries->length;
				$googleGroups = array();
				foreach ($entries as $entry) {
					$titleNodes = $entry->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "title");
					if ($titleNodes->length == 1) {
						$title = $titleNodes->item(0)->textContent;	// We got the title of a group (For example: 'System Group: My Contacts', 'System Group: Friend', 'Dolibarr (Thirdparties)', ...)
						$googleIDNodes = $entry->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "id");
						if ($googleIDNodes->length == 1) {
							$googleGroups[$title] = $googleIDNodes->item(0)->textContent;	// We get <id> of group
						}
					}
				}
				dol_syslog("getGoogleGroupID We found ".count($googleGroups)." groups", LOG_DEBUG);
			}
		} else {
			$error++;
			dol_syslog("getGoogleGroupID ErrorFailedToGetGroups", LOG_ERR);
			return 'ErrorFailedToGetGroups';
		}
	}

	// Create group if it not exists
	if (! $error && !isset($googleGroups[$groupName])) {
		$newGroupID = insertGContactGroup($gdata, $groupName, $useremail);
		$googleGroups[$groupName] = $newGroupID;
	}

	dol_syslog("Full URL ID found for group ".$groupName." = ".$googleGroups[$groupName], LOG_DEBUG);
	return $googleGroups[$groupName];
}


/**
 * Retreive a Xml feed of contacts groups from Google
 *
 * @param	array	$gdata			Array with tokens info
 * @param	string	$useremail		User email
 * @return	string					XML string with all groups, '' if error
 */
function getContactGroupsXml($gdata, $useremail = 'default')
{
	global $conf;
	global $tag_debug;

	$tag_debug='groupgroups';

	$xmlStr='';
	try {
		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token);
		$addheaderscurl=array('GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/atom+xml');
		//$useremail='default';

		//$request=new Google_Http_Request('https://www.google.com/m8/feeds/groups/'.urlencode($useremail).'/full?max-results=1000', 'GET', $addheaders, null);
		//$result=$gdata['client']->execute($request);	// Return json_decoded string. May return an exception.
		//$xmlStr=$result;

		$result = getURLContent('https://www.google.com/m8/feeds/groups/'.urlencode($useremail).'/full?max-results=1000', 'GET', '', 0, $addheaderscurl);
		$xmlStr = $result['content'];
		try {
			$document = new DOMDocument("1.0", "utf-8");
			$resultloadxml = $document->loadXml($xmlStr);
			if ($resultloadxml === false) {
				dol_syslog("getContactGroupsXml Failed to parse xml string ".$xmlStr, LOG_WARNING);
			} else {
				$errorselem = $document->getElementsByTagName("errors");
				//var_dump($errorselem);
				//var_dump($errorselem->length);
				//var_dump(count($errorselem));
				if ($errorselem->length) {
					dol_syslog('getContactGroupsXml ERROR:'.$result['content'], LOG_ERR);
					return '';
				}
			}
		} catch (Exception $e) {
			dol_syslog('getContactGroupsXml ERROR:'.$e->getMessage(), LOG_ERR);
			return '';
		}

		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_groups.xml' command
	} catch (Exception $e) {
		dol_syslog(sprintf("getContactGroupsXml Error while getting feeds xml groups : %s", $e->getMessage()), LOG_ERR);
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $e->getMessage());
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
	}

	return($xmlStr);
}



/**
 * Create a group/label into Google contact
 *
 * @param	array	$gdata			Array with tokens info
 * @param 	string 	$groupName		Group name to create into Google Contact
 * @param	string	$useremail		User email
 * @return 	string					Ful URL group ID (http://...xxx)
 */
function insertGContactGroup($gdata, $groupName, $useremail = 'default')
{
	global $tag_debug;

	$tag_debug='createcontact';

	dol_syslog('insertGContactGroup create group '.$groupName.' into Google contact');

	$id='';

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

		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token);
		$addheaderscurl=array('GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/atom+xml');

		// insert entry
		//$entryResult = $gdata->insertEntry($xmlStr, 'https://www.google.com/m8/feeds/groups/'.$useremail.'/full');
		$response = getURLContent('https://www.google.com/m8/feeds/groups/'.$useremail.'/full', 'POST', $xmlStr, 1, $addheaderscurl);
		if ($response['content']) {
			$document = new DOMDocument("1.0", "utf-8");
			$document->loadXml($response['content']);

			$errorselem = $document->getElementsByTagName("errors");
			//var_dump($errorselem);
			//var_dump($errorselem->length);
			//var_dump(count($errorselem));
			if ($errorselem->length) {
				dol_syslog($response['content'], LOG_ERR);
				return -1;
			}

			$entries = $document->getElementsByTagName("id");
			foreach ($entries as $entry) {
				$id = $entry->nodeValue;		// No basename here, we want full URL ID
				break;
			}
		}

		dol_syslog(sprintf("We have just created the google contact group '%s'. Its Full URL group ID is %s", $groupName, $id));
	} catch (Exception $e) {
		dol_syslog(sprintf("Problem while inserting group %s : %s", $groupName, $e->getMessage()), LOG_ERR);
	}

	return($id);
}



/**
 * Encode string for xml usage
 *
 * @param 	string	$string		String to encode
 * @return	string				String encoded
 */
function dolEscapeXMLWithNoAnd($string)
{
	return strtr($string, array('\''=>'&apos;','"'=>'&quot;','&amp;'=>'-','&'=>'-','<'=>'&lt;','>'=>'&gt;'));
}


if (! function_exists('dolEscapeXML')) {
	/**
	 * Encode string for xml usage
	 *
	 * @param 	string	$string		String to encode
	 * @return	string				String encoded
	 */
	function dolEscapeXML($string)
	{
		return strtr($string, array('\''=>'&apos;','"'=>'&quot;','&'=>'&amp;','<'=>'&lt;','>'=>'&gt;'));
	}
}
