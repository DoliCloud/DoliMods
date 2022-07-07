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
	$person_array = [];

	$jsonData = "{";
	try {
		$gdata = $client;
		//<gdata:name>
		if ($object->element != 'societe' && $object->element != 'thirdparty') {
			$fullNameToUse = $object->getFullName($langs);
		} else {
			$fullNameToUse = $object->name;
		}
		$person_array["names"][0]["familyName"] = !empty($object->lastname) ? $object->lastname : $fullNameToUse;
		if (!empty($object->firstname)) $person_array["names"][0]["givenName"] = $object->firstname;

		//<atom:content>
		$tmpnote=$object->note_public;
		if (strpos($tmpnote, $google_nltechno_tag) === false){
			$tmpnote.="\n\n".$google_nltechno_tag.$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
		}
		$person_array["biographies"][] = [
			"value" => google_html_convert_entities($tmpnote),
		];

		//<gdata:phoneNumber>
		$newphone=!empty($object->phone)?$object->phone_pro:$object->phone;
		if (!empty($newphone)){
			$person_array["phoneNumbers"][] = [
				"value" => $newphone,
				"type" => "work",
			];
		}
		if (!empty($object->phone_perso)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->phone_perso,
				"type" => "home",
			];
		}
		if (!empty($object->phone_mobile)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->phone_mobile,
				"type" => "mobile",
			];
		}
		if (!empty($object->fax)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->fax,
				"type" => "workFax",
			];
		}

		//<gdata:structuredPostalAddress>
		$jsonData .='"addresses": [';
		$jsonData .='{"country": '.json_encode(getCountry($object->country_id,0,"",$langs,0)).'}';
		$person_array["addresses"][0]["country"] = $object->country_id>0 ? getCountry($object->country_id, 0, '', $langs, 0) : '';
		$person_array["addresses"][0]["postalCode"] = !empty($object->zip) ? $object->zip : "";
		$person_array["addresses"][0]["streetAddress"] = !empty($object->address) ? $object->address : "";
		$person_array["addresses"][0]["city"] = !empty($object->town) ? $object->town : "";
		$tmpstate=($object->state_id>0?getState($object->state_id):'');
		$tmpstate=dol_html_entity_decode($tmpstate, ENT_QUOTES);	// Should not be required. It is here because some bugged version of getState return a string with entities instead of utf8 with no entities
		$person_array["addresses"][0]["region"] = $tmpstate;

		//<gdata:email>
		$person_array["emailAddresses"][0]["value"] = $object->email?$object->email:(strtolower(preg_replace('/\s/', '', (empty($object->name)?$object->lastname.$object->firstname:$object->name))).'@noemail.com');

		//<gcontact:userDefinedField>
		if ($object->element == "societe") {
			$element = "thirdparty";
		} elseif ($object->element == "contact") {
			$element = "contact";
		} elseif ($object->element == "member") {
			$element = "member";
		} else {
			$element = "unknown";
		}
		$person_array["userDefined"][0]["key"] = "dolibarr-id";
		$person_array["userDefined"][0]["value"] = $object->id.'/'.$element;

		//<gcontact:birthday>
		if (!empty($object->birthday)){
			$person_array["birthdays"][] = [
				"text" => dol_print_date($object->birthday, 'dayrfc'),
			];
		}
		$jsonData = json_encode($person_array);
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_createcontact.json", $jsonData);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_createcontact.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_createcontact.xml' command

		$id = '';
		// insert entry
		$google = new Google_Service_PeopleService($gdata["client"]);
		$person = new Google_Service_PeopleService_Person($person_array);
		$response = $google->people->createContact($person);
		if (!empty($response)) {
			try {

				$id = $response->getResourceName();
			} catch (Exception $e) {
				die('ERROR:' . $e->getMessage());
			}
		}

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

	$newcontactid = $contactId;

	$reg = array();
	if (preg_match('/google\.com\/.*\/([^\/]+)$/', $contactId, $reg)) {
		$newcontactid=$reg[1];
	}
	if (preg_match('/google:(\D+\d+)$/', $contactId, $reg)) {
		$newcontactid=$reg[1];	// TODO This may not be enough because ID in dolibarr is 250 char max and in google may have 1024 chars
	}

	$tag_debug='updatecontact';

	dol_syslog('googleUpdateContact object->id='.$object->id.' type='.$object->element.' ref_ext='.$object->ref_ext.' contactid='.$newcontactid);

	if (empty($newcontactid)) {
		dol_syslog('googleUpdateContact object->id='.$object->id.' type='.$object->element.' ref_ext='.$object->ref_ext.' bad value for $contactId='.$contactId, LOG_WARNING);
		return 0;
	}

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
		$personFields = "addresses,biographies,birthdays,calendarUrls,clientData,emailAddresses,events,externalIds,genders,imClients,interests,locales,locations,memberships,miscKeywords,names,nicknames,occupations,organizations,phoneNumbers,relations,sipAddresses,urls,userDefined";
		$result = getURLContent('https://people.googleapis.com/v1/'.$newcontactid.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$xmlStr=$result['content'];

		/*if (empty($xmlStr))
		{
			print "Something is wrong. The getURLContent to Google return an empty string\n";
		}*/

		//$contactId='https://www.google.com/m8/feeds/contacts/eldy10%40gmail.com/base/4429b3590f5b343a';
		//$contactId='https://www.google.com/m8/feeds/contacts/contact%40nltechno.com/base/ee6fc620dbab6d7';
		try {
			$json = json_decode($result["content"]);
			//var_dump($errorselem);
			//var_dump($errorselem->length);
			//var_dump(count($errorselem));
			if (preg_match('/<title>.*Not Found.*<\/title>/', $result['content'])) {
				dol_syslog('Google server return message '.$result['content'].' so we leave with code 0', LOG_DEBUG);
				return 0;
			}
			if (!empty($json->error)) {
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
		$json = json_decode($result["content"]);
		$person_array = [];
		$jsonData = "{";
		$updatePersonFields = "";

		//Names
		if ($object->element != 'societe' && $object->element != 'thirdparty') {
			$fullNameToUse = $object->getFullName($langs);
		} else {
			$fullNameToUse = $object->name;
		}
		$person_array["etag"] = $json->etag;
		$person_array["names"][0]["familyName"] = !empty($object->lastname) ? $object->lastname : $fullNameToUse;
		if (!empty($object->firstname)) $person_array["names"][0]["givenName"] = $object->firstname;
		$updatePersonFields .= "names";

		//Email Address
		$person_array["emailAddresses"][0]["value"] = $object->email?$object->email:(strtolower(preg_replace('/\s/', '', (empty($object->name)?$object->lastname.$object->firstname:$object->name))).'@noemail.com');
		$updatePersonFields .= ",emailAddresses";

		//Address
		$person_array["addresses"][0]["country"] = $object->country_id>0?getCountry($object->country_id, 0, '', $langs, 0):'';
		$person_array["addresses"][0]["postalCode"] = $object->zip;
		$person_array["addresses"][0]["streetAddress"] = $object->address;
		$person_array["addresses"][0]["city"] = $object->town;
		$tmpstate=($object->state_id>0?getState($object->state_id):'');
		$tmpstate=dol_html_entity_decode($tmpstate, ENT_QUOTES);	// Should not be required. It is here because some bugged version of getState return a string with entities instead of utf8 with no entities
		$person_array["addresses"][0]["region"] = $tmpstate;
		$updatePersonFields .= ",addresses";

		//Phone
		$newphone=empty($object->phone)?$object->phone_pro:$object->phone;
		if (!empty($newphone)){
			$person_array["phoneNumbers"][] = [
				"value" => $newphone,
				"type" => "work",
			];
			$updatePersonFields .= ",phoneNumbers";
		}
		if (!empty($object->phone_perso)){
			$person_array["phoneNumbers"][] = [
				"value" => $object->phone_perso,
				"type" => "home",
			];
			if(strpos($updatePersonFields,"phoneNumbers") === false) $updatePersonFields .= ",phoneNumbers";
		}
		if (!empty($object->phone_mobile)){
			$person_array["phoneNumbers"][] = [
				"value" => $object->phone_mobile,
				"type" => "mobile",
			];
			if(strpos($updatePersonFields,"phoneNumbers") === false) $updatePersonFields .= ",phoneNumbers";
		}
		if (!empty($object->fax)){
			$person_array["phoneNumbers"][] = [
				"value" => $object->fax,
				"type" => "workFax",
			];
			if(strpos($updatePersonFields,"phoneNumbers") === false) $updatePersonFields .= ",phoneNumbers";
		}

		// userDefinedField
		// We don't change this

		// Birthday
		if (!empty($json->birthdays)) {
			$person_array["birthdays"][] = [
				"text" => dol_print_date($object->birthday, 'dayrfc'),
			];
		}

		// Comment
		$tmpnote=$object->note_public;
		if (strpos($tmpnote, $google_nltechno_tag) === false){
			$tmpnote.="\n\n".$google_nltechno_tag.$object->id.'/'.($object->element=='societe'?'thirdparty':$object->element);
		}
		$person_array["biographies"][] = [
			"value" => google_html_convert_entities($tmpnote),
		];

		//var_dump($xml->organization->asXml());    // $xml->organization is SimpleXMLElement but isset($xml->organization) and $xml->organization->asXml() may be set or not
		// Company + Function
		// if (isset($xml->organization)) {
		// 	if ($object->element == 'contact') {
		// 		unset($xml->organization->orgName);
		// 		unset($xml->organization->orgTitle);
		// 		$object->fetch_thirdparty();
		// 		if (! empty($object->thirdparty->name) || ! empty($object->poste)) {
		// 			$thirdpartyname=$object->thirdparty->name;
		// 			$xml->organization['rel']="http://schemas.google.com/g/2005#other";
		// 			if (! empty($object->thirdparty->name)) $xml->organization->orgName=$thirdpartyname;
		// 			if (! empty($object->poste)) $xml->organization->orgTitle=$object->poste;
		// 		}
		// 	}
		// 	if ($object->element == 'member') {
		// 		unset($xml->organization->orgName);
		// 		unset($xml->organization->orgTitle);
		// 		if (! empty($object->company)) {
		// 			$thirdpartyname=$object->company;
		// 			$xml->organization['rel']="http://schemas.google.com/g/2005#other";
		// 			if (! empty($object->company)) $xml->organization->orgName=$thirdpartyname;
		// 		}
		// 	}
		// }


		// $xmlStr=$xml->saveXML();


		// // Remove <gContact:groupMembershipInfo
		// //print dol_escape_htmltag($xmlStr);
		// $xmlStr = preg_replace('/<gContact:groupMembershipInfo[^>]*/', '', $xmlStr);
		// //print dol_escape_htmltag($xmlStr);exit;


		// // Convert xml into DOM so we can use dom function to add website element
		// $doc  = new DOMDocument("1.0", "utf-8");
		// $doc->loadXML($xmlStr);
		// $entries = $doc->getElementsByTagName('entry');

		// // Birthday (in namespace gdContact)
		// if (! $xmlgcontact->birthday->asXml() && $object->birthday) {    // Not into current remote record, we add it if defined
		// 	foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
		// 		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', constant('GCONTACT_NAME_SPACE'));
		// 		$birthday = $doc->createElement('gcontact:birthday');
		// 		$birthday->setAttribute('when', dol_print_date($object->birthday, 'dayrfc'));
		// 		$entry->appendChild($birthday);
		// 	}
		// }

		// // URL
		// $oldurl='';
		// if (! empty($object->oldcopy->url)) $oldurl=$object->oldcopy->url;
		// // Removed old url
		// foreach ($doc->getElementsByTagName('website') as $nodewebsite) {
		// 	$linkurl = $nodewebsite->getAttribute('href');
		// 	$labelurl = $nodewebsite->getAttribute('label');
		// 	if ($linkurl == $oldurl) {	// Delete only if value on google match old value into Dolibarr
		// 		$nodewebsite->parentNode->removeChild($nodewebsite);
		// 	}
		// }
		// // Add new url
		// if (! empty($object->url)) {
		// 	foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
		// 		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', constant('GCONTACT_NAME_SPACE'));
		// 		$el = $doc->createElement('gcontact:website');
		// 		$el->setAttribute("label", "URL");
		// 		$el->setAttribute("href", $object->url);
		// 		$entry->appendChild($el);
		// 	}
		// }

		// // Add company if object organization did not exists (so it was not updated)
		// if (! isset($xml->organization)) {
		// 	// Company - Function
		// 	if ($object->element == 'contact') {
		// 		foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
		// 			// Company
		// 			$company = $doc->createElement('gd:organization');
		// 			$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
		// 			$entry->appendChild($company);

		// 			$object->fetch_thirdparty();
		// 			if (! empty($object->thirdparty->name) || ! empty($object->poste)) {   // Job position and company name of contact
		// 				$thirdpartyname=$object->thirdparty->name;

		// 				$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
		// 				if (! empty($thirdpartyname)) $company->appendChild($orgName);
		// 				$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
		// 				if (! empty($object->poste)) $company->appendChild($orgTitle);
		// 			}
		// 		}
		// 	}
		// 	if ($object->element == 'member') {
		// 		foreach ($entries as $entry) {	// We should have only one <entry>, loop is required to access first record of $entries.
		// 			// Company
		// 			$company = $doc->createElement('gd:organization');
		// 			$company->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
		// 			$entry->appendChild($company);

		// 			//$object->fetch_thirdparty();
		// 			if (! empty($object->company)) {
		// 				$thirdpartyname=$object->company;

		// 				$orgName = $doc->createElement('gd:orgName', $thirdpartyname);
		// 				if (! empty($thirdpartyname)) $company->appendChild($orgName);
		// 				//$orgTitle = $doc->createElement('gd:orgTitle', $object->poste);
		// 				//if (! empty($object->poste)) $company->appendChild($orgTitle);
		// 			}
		// 		}
		// 	}
		// }

		// /* Old code used when using SimpleXML object (not working)
		// 	foreach ($xml->website as $key => $val) {	// $key='@attributes' $val is an array('href'=>,'label'=>), however to set href it we must do $xml->website['href'] (it's a SimpleXML object)
		// 		$oldvalue=(string) $val['href'];
		// 		if (! empty($object->url)) $xml->website['href'] = $object->url;
		// 		else unset($xml->website);
		// 	}
		// */
		// //var_dump($xmlStr);exit;

		// $xmlStr=$doc->saveXML();
		$jsonData .='}';
		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.json", $jsonData);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_updatecontact.xml' command

		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('If-Match'=>'*', 'GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'Content-Type'=>'application/json');
		$addheaderscurl=array('If-Match: *', 'GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/json');

		// update entry.'&updatePersonFields='.$updatePersonFields
		//$client_google = new Google_Client($client);
		$google = new Google_Service_PeopleService($gdata["client"]);
		$person = new Google_Service_PeopleService_Person($person_array);
		$personParam['updatePersonFields'] = $updatePersonFields;
		$response = $google->people->UpdateContact($newcontactid,$person,$personParam);
		try {
			//$url ='https://people.googleapis.com/v1/'.$newcontactid.':updateContact';
			//$response = getURLContent($url, 'POSTALREADYFORMATED', $jsonData, 1, $addheaderscurl);
			if(empty($response)) throw new Exception("Error on google record update", 1);
			$id = $response->getResourceName();
		} catch (Exception $e) {
			die('ERROR:' . $e->getMessage());
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
		/*if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'If-Match: *', 'Authorization'=>'Bearer '.$access_token);
		$addheaderscurl=array('GData-Version: 3.0', 'If-Match: *', 'Authorization: Bearer '.$access_token);*/
		//$useremail='default';
		$google = new Google_Service_PeopleService($gdata["client"]);
		$result = $google->people->deleteContact("people/".$ref);
		//$result = getURLContent('https://www.google.com/m8/feeds/contacts/'.urlencode($useremail).'/full/'.$newcontactid, 'DELETE', '', 0, $addheaderscurl);
		//$xmlStr=$result['content'];

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

		/*$doc = new DOMDocument("1.0", "utf-8");
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
*/
		//People call
		$jsonData = '{"contacts": [';
		$nbcontacts = 1;
		foreach ($firstContacts as $gContact) {
			if ($nbcontacts > 1) {
				$jsonData .=',';
			}
			$jsonData .='{"contactPerson": {';
			//<gdata:name>
			$jsonData .='"names":[';
			$jsonData .='{"familyName": '.json_encode(!empty($gContact->lastname)?$gContact->lastname:$gContact->fullName);
			if (!empty($gContact->firstname)) {
				$jsonData .=',"givenName": '.json_encode($gContact->firstname);
			}
			$jsonData .='}],';
			//<atom:content>
			$jsonData .='"biographies": [';
			$jsonData .='{ "value": '.json_encode($gContact->note_public).'}';
			$jsonData .='],';
			//<gdata:phoneNumber>
			if (!empty($gContact->phone_pro)) {
				$jsonData .='"phoneNumbers": [';
				$jsonData .='{ "type": "work"},';
				$jsonData .='{ "value": '.json_encode($gContact->phone_pro).'}';
				$jsonData .='],';
			}
			if (!empty($gContact->phone_perso)) {
				$jsonData .='"phoneNumbers": [';
				$jsonData .='{ "type": "home"},';
				$jsonData .='{ "value": '.json_encode($gContact->phone_perso).'}';
				$jsonData .='],';
			}
			if (!empty($gContact->phone_mobile)) {
				$jsonData .='"phoneNumbers": [';
				$jsonData .='{ "type": "mobile"},';
				$jsonData .='{ "value": '.json_encode($gContact->phone_mobile).'}';
				$jsonData .='],';
			}
			if (!empty($gContact->fax)) {
				$jsonData .='"phoneNumbers": [';
				$jsonData .='{ "type": "workFax"},';
				$jsonData .='{ "value": '.json_encode($gContact->fax).'}';
				$jsonData .='],';
			}
			//<gdata:structuredPostalAddress>
			if (!empty($gContact->addr)) {
				$addresses = $gContact->addr;
				$jsonData .='"addresses": [';
				$jsonData .='{"country": '.json_encode($addresses->country).'}';
				if (!empty($addresses->zip)) {
					$jsonData .=',{"postalCode": '.json_encode($addresses->zip).'}';
				}
				if (!empty($addresses->zip)) {
					$jsonData .=',{"region": '.json_encode($addresses->state).'}';
				}
				if (!empty($addresses->street)) {
					$jsonData .=',{"streetAddress": '.json_encode($addresses->street).'}';
				}
				if (!empty($addresses->town)) {
					$jsonData .=',{"city": '.json_encode($addresses->town).'}';
				}
				$jsonData .='],';
			}
			//<gdata:email>
			if (!empty($gContact->email)) {
				$jsonData .='"emailAddresses": [';
				$jsonData .='{ "value": '.json_encode($gContact->email).'}';
				$jsonData .='],';
			}
			//<gcontact:userDefinedField>
			$jsonData .='"userDefined": [';
			$jsonData .='{ "key": "dolibarr-id",';
			if ($objectstatic->element == "societe") {
				$element = "thirdparty";
			} elseif ($objectstatic->element == "contact") {
				$element = "contact";
			} elseif ($objectstatic->element == "member") {
				$element = "member";
			} else {
				$element = "unknown";
			}
			$jsonData .='"value": '.json_encode($gContact->dolID.'/'.$element).'}';
			$jsonData .='],';
			$jsonData .='}}';
			$nbcontacts ++;
		}
		$jsonData .= '],';
		$jsonData .= '"readMask" : "userDefined"';
		$jsonData .= '}';
		//$jsonData = json_encode($jsonData);
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
			//$xmlStr = google_html_convert_entities($xmlStr);

			if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
				$access_token=$gdata['google_web_token']['access_token'];
			} else {
				$tmp=json_decode($gdata['google_web_token']);
				$access_token=$tmp->access_token;
			}
			$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
			$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
			$result = getURLContent('https://people.googleapis.com/v1/people:batchCreateContacts', 'POST', $jsonData, 0, $addheaderscurl);
			$jsonStr=$result['content'];
			try {
				$json = json_decode($jsonStr);
				if(!empty($json->error)){
					dol_syslog('ERROR:'.$result['content'], LOG_ERR);
					return -1;
				}

			} catch (Exception $e) {
				dol_syslog('ERROR:'.$e->getMessage(), LOG_ERR);
				return -1;
			}

			$responseJson = $jsonStr;
			// uncomment for debugging :
			//file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.json", $responseJson);
			//@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
			// you can view this file with 'xmlstarlet fo dolibarr_google_massinsert_response.xml' command
			$res=parseResponse($responseJson);
			if ($res->count != count($firstContacts) || $res->nbOfErrors) {
				dol_syslog("Failed to batch insert count=".$res->count.", count(firstContacts)=".count($firstContacts).", nb of errors=".$res->nbOfErrors.", lasterror=".$res->lastError, LOG_ERR);
				return sprintf("Google error : Nb of records to insert = %s, nb inserted = %s, error label = %s", count($firstContacts), $res->count, $res->lastError);
			} else {
				dol_syslog(sprintf("Inserting %d google contacts", count($firstContacts)));

				// Now update each record into database with external ref
				if (is_object($objectstatic)) {
					$json = json_decode($responseJson);
					$contentNodes = $json->createdPeople;
					foreach ($contentNodes as $node) {
						if (!empty($node->person)) {
							$idnode = $node->person->resourceName;
							$userdefined = $node->person->userDefined[0]->value;
							if (! empty($idnode) && preg_match('/^(\d+)\/(.*)/', $userdefined, $reg)) {
								if (! empty($reg[2])) {
									$objectstatic->id=$reg[1];
									$objectstatic->update_ref_ext("google:".$idnode);
								}
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
 * @param 	string		$jsonStr	String
 * @return	stdClass				Class with response
 */
function parseResponse($jsonStr)
{

	$json = json_decode($jsonStr);
	$res = new stdClass();
	if (!empty($json->contactErrors)) {
		$res->count = count($json->contactErrors);
		$res->nbOfErrors = count($json->contactErrors);
		$res->lastError = $json->contactErrors[$res->nbOfErrors-1];

	}else{
		$res->count = count($json->createdPeople);
		$res->nbOfErrors=0;
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
 * If the groupName does not exist on Gmail account, it is also created. So this method should always return a group ID (except on technical error)
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

	// Create group if it does not exists
	if (! $error && !isset($googleGroups[$groupName])) {
		$newGroupID = insertGContactGroup($gdata, $groupName, $useremail);
		$googleGroups[$groupName] = $newGroupID;
	}

	dol_syslog("Full URL ID found for group ".$groupName." = ".$googleGroups[$groupName], LOG_DEBUG);
	return $googleGroups[$groupName];
}


/**
 * Retreive a Xml string with list of all groups of contacts from Google
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

		// uncomment for debugging :
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $xmlStr);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		// you can view this file with 'xmlstarlet fo dolibarr_google_groups.xml' command

		if (strpos($xmlStr, 'Contacts API is being deprecated') === 0) {
			// $xmlStr may be the error message "Contacts API is being deprecated. Migrate to People API to retain programmatic access to Google Contacts. See https://developers.google.com/people/contacts-api-migration."
			dol_syslog("getContactGroupsXml Failed because Google Contact API are now closed", LOG_WARNING);
			return '';
		}

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
 * @return 	string					Ful URL of the group ID (http://...xxx)
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
