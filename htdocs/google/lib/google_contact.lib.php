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
	global $tag_debug;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
	include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

	dol_syslog('googleCreateContact object->id='.$object->id.' type='.$object->element);

	$google_nltechno_tag=getCommentIDTag();
	$person_array = [];

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
		$newphone = (empty($object->phone) ? (empty($object->phone_pro) ? '' : $object->phone_pro) : $object->phone);
		if (!empty($newphone)){
			$person_array["phoneNumbers"][] = [
				"value" => $newphone,
				"type" => "work",
			];
		}
		/*
		if (!empty($object->office_phone)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->office_phone,
				"type" => "work",
			];
		}
		if (!empty($object->user_mobile)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->user_mobile,
				"type" => "work",
			];
		}
		if (!empty($object->personal_mobile)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->personal_mobile,
				"type" => "home",
			];
		}
		*/
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
		if (!empty($object->office_fax)) {
			$person_array["phoneNumbers"][] = [
				"value" => $object->office_fax,
				"type" => "workFax",
			];
		}

		// Set occupations
		if (!empty($object->poste)) {
			$person_array["occupations"][] = [
				"value" => $object->poste
			];
		}
		// Set company and job
		if (!empty($object->socid) && $object->socid > 0) {
			$result = $object->fetch_thirdparty();
			if ($result > 0) {
				$person_array["organizations"][0] = [
					"name" => $object->thirdparty->name,
					"location" => $object->thirdparty->getFullAddress()
				];
				if (!empty($object->poste)) {
					$person_array["organizations"][0]['title'] = $object->poste;
				}
			}
		}

		//<gdata:structuredPostalAddress>
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
		if (getDolGlobalInt('GOOGLE_DEBUG')) {
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_createcontact.json", $jsonData);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_createcontact.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		}

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

	$google_nltechno_tag = getCommentIDTag();

	// Fields: http://tools.ietf.org/html/rfc4287
	$gdata = $client;

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

		// Names
		if ($object->element != 'societe' && $object->element != 'thirdparty') {
			$fullNameToUse = $object->getFullName($langs);
		} else {
			$fullNameToUse = $object->name;
		}
		$person_array["etag"] = $json->etag;
		$person_array["names"][0]["familyName"] = !empty($object->lastname) ? $object->lastname : $fullNameToUse;
		if (!empty($object->firstname)) $person_array["names"][0]["givenName"] = $object->firstname;
		$updatePersonFields .= "names";

		// Email Address
		$person_array["emailAddresses"][0]["value"] = $object->email?$object->email:(strtolower(preg_replace('/\s/', '', (empty($object->name)?$object->lastname.$object->firstname:$object->name))).'@noemail.com');
		$updatePersonFields .= ",emailAddresses";

		// Set occupations
		if (!empty($object->poste)) {
			$person_array["occupations"][] = [
				"value" => $object->poste
			];
		}
		// Set company and job
		if (!empty($object->socid) && $object->socid > 0) {
			$result = $object->fetch_thirdparty();
			if ($result > 0) {
				$person_array["organizations"][0] = [
					"name" => $object->thirdparty->name,
					"location" => $object->thirdparty->getFullAddress()
				];
				if (!empty($object->poste)) {
					$person_array["organizations"][0]['title'] = $object->poste;
				}
			}
		}

		// Address
		$person_array["addresses"][0]["country"] = $object->country_id>0?getCountry($object->country_id, 0, '', $langs, 0):'';
		$person_array["addresses"][0]["postalCode"] = $object->zip;
		$person_array["addresses"][0]["streetAddress"] = $object->address;
		$person_array["addresses"][0]["city"] = $object->town;
		$tmpstate=($object->state_id>0?getState($object->state_id):'');
		$tmpstate=dol_html_entity_decode($tmpstate, ENT_QUOTES);	// Should not be required. It is here because some bugged version of getState return a string with entities instead of utf8 with no entities
		$person_array["addresses"][0]["region"] = $tmpstate;
		$updatePersonFields .= ",addresses";

		// Phone
		$newphone = (empty($object->phone) ? (empty($object->phone_pro) ? '' : $object->phone_pro) : $object->phone);
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
		if (getDolGlobalInt('GOOGLE_DEBUG')) {
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.json", $jsonData);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_updatecontact.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		}

		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('If-Match'=>'*', 'GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'Content-Type'=>'application/json');
		$addheaderscurl=array('If-Match: *', 'GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/json');

		//var_dump($person_array);exit;

		// update entry.'&updatePersonFields='.$updatePersonFields
		//$client_google = new Google_Client($client);
		$google = new Google_Service_PeopleService($gdata["client"]);
		$person = new Google_Service_PeopleService_Person($person_array);
		$personParam['updatePersonFields'] = $updatePersonFields;
		$response = $google->people->updateContact($newcontactid,$person,$personParam);
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
 * Link a contact $contactID to group $groupID in google contact
 *
 * @param string  	$client 	Google client
 * @param string 	$groupID 	ID of group to update
 * @param string	$contactID 	ID of contact to update
 * @return int					<0 if KO, >0 if OK
 */
function googleLinkGroup($client, $groupID, $contactID) {
	// prepare json data
	$jsonData = '{';
	$jsonData .= '"resourceNamesToAdd":'. json_encode(array($contactID));
	$jsonData .= '}';

	// prepare request
	$gdata = $client;
	if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
		$access_token=$gdata['google_web_token']['access_token'];
	} else {
		$tmp=json_decode($gdata['google_web_token']);
		$access_token=$tmp->access_token;
	}
	$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
	$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');

	// uncomment for debugging :
	if (getDolGlobalInt('GOOGLE_DEBUG')) {
		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_linkGroup.json", $jsonData);
		@chmod(DOL_DATA_ROOT . "/dolibarr_google_linkGroup.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
	}

	dol_syslog('googleLinkGroup start', LOG_DEBUG);

	$result = getURLContent('https://people.googleapis.com/v1/'.$groupID.'/members:modify', 'POST', $jsonData, 0, $addheaderscurl);

	dol_syslog('googleLinkGroup end', LOG_DEBUG);

	if ($result['http_code'] == 404) {
		dol_syslog('Failed to link contact to group: '.$result['http_code'], LOG_ERR);
		return -1;
	}
	$jsonStr = $result['content'];
	$json = json_decode($jsonStr);
	if (!empty($json->error)) {
		dol_syslog('Link group Error:'.$json->error->message, LOG_ERR);
		return $json->error->message;
	}
	return 1;
}

/**
 * Link a member to group in google contact
 *
 * @param string  	$client 	Google client
 * @param string 	$groupID 	ID of group to update
 * @param string	$contactID 	ID of contact to update
 * @return int					<0 if KO, >0 if OK
 */
function googleUnlinkGroup($client, $groupID, $contactID) {

	// prepare json data
	$jsonData = '{';
	$jsonData .= '"resourceNamesToRemove":'.json_encode(array($contactID));
	$jsonData .= '}';

	// prepare request
	$gdata = $client;
	if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
		$access_token=$gdata['google_web_token']['access_token'];
	} else {
		$tmp=json_decode($gdata['google_web_token']);
		$access_token=$tmp->access_token;
	}
	$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
	$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
	$result = getURLContent('https://people.googleapis.com/v1/'.$groupID.'/members:modify', 'POST', $jsonData, 0, $addheaderscurl);
	$jsonStr = $result['content'];
	$json = json_decode($jsonStr);
	if (!empty($json->error)) {
		dol_syslog('Unlink group Error:'.$json->error->message, LOG_ERR);
		return -1;
	}
	return 1;
}


/**
 * Delete a group in google contact
 *
 * @param string  	$client 	Google client
 * @param string 	$groupID 	ID of group to update
 *
 * @return int					<0 if KO, >0 if OK
 */
function googleDeleteGroup($client, $groupID) {

	// prepare request
	$gdata = $client;
	if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
		$access_token=$gdata['google_web_token']['access_token'];
	} else {
		$tmp=json_decode($gdata['google_web_token']);
		$access_token=$tmp->access_token;
	}
	$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
	$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
	$result = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'DELETE', '', 0, $addheaderscurl);
	$jsonStr = $result['content'];
	$json = json_decode($jsonStr);
	if (!empty($json->error)) {
		dol_syslog('Delete group Error:'.$json->error->message, LOG_ERR);
		return -1;
	}
	return 1;
}

/**
 * Update a name of a group in google contact
 *
 * @param string  	$client 	Google client
 * @param string 	$groupID 	ID of group to update
 * @param string 	$name	 	New name of group
 * @return int					<0 if KO, >0 if OK
 */
function googleUpdateGroup($client, $groupID, $name) {
	//TODO
	// global $conf;
	// $gdata = $client;
	// // Send request to Google
	// if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
	// 	$access_token=$gdata['google_web_token']['access_token'];
	// } else {
	// 	$tmp=json_decode($gdata['google_web_token']);
	// 	$access_token=$tmp->access_token;
	// }
	// $addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
	// $addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
	// $result = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
	// $jsonStr = $result['content'];
	// $json = json_decode($jsonStr);
	// $json->name = $name;
	// $json->formattedName = $name;
	// $jsonData = '{';
	// $jsonData .='"contactGroup":';
	// $jsonData .= json_encode($json);
	// $jsonData .= '}';
	// $result = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'PUT', $jsonData, 0, $addheaderscurl);
	// // return json_encode($result);
	// $jsonStr = $result['content'];
	// $json = json_decode($jsonStr);
	// if (!empty($json->error)) {
	// 	dol_syslog('googleUpdateGroup Error:'.$json->error->message, LOG_ERR);
	// 	return "ERROR:".$json->error->message;
	// }
	return 1;

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

	$newcontactid = $ref;
	$reg = array();
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
 */
function insertGContactsEntries($gdata, $gContacts, $objectstatic, $useremail = 'default')
{
	global $conf, $db;

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
		if (getDolGlobalInt('GOOGLE_DEBUG')) {
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert.xml", $xmlStr);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		}
*/
		// People call
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
			//<gdata:membership> (tags)
			// $jsonData .='"contactGroups": [';
			// $jsonData .='{ "contactGroupMembership": ';
			// $jsonData .='{ "contactGroupResourceName": "aloooooo/2444444"}';
			// $jsonData .='],';
			//<gdata:event>
			// $jsonData .='"events": [';
			// $jsonData .='{ "date": ';
			// $jsonData .='{ "year": "2021",';
			// $jsonData .='"month": "10",';
			// $jsonData .='"day": "21"},';
			// $jsonData .='{ "type": "fête du village"';
			// 	$jsonData .='}],';

			//<gdata:occupations>
			// Set occupations
			if (!empty($gContact->poste)) {
				$jsonData .='"occupations": [';
				$jsonData .='{ "value": '.json_encode($gContact->poste).'}';
				$jsonData .='],';
			}
			//<gdata:organizations>
			// Set company and job
			if (!empty($gContact->socid) && $gContact->socid > 0) {
				$object = new Contact($db);
				$object->socid = $gContact->socid;
				$result = $object->fetch_thirdparty();
				if ($result > 0) {
					$jsonData .='"organizations": [';
					$jsonData .='{"name": '.json_encode($object->thirdparty->name).'}';
					$jsonData .=',{"location": '.json_encode($object->thirdparty->getFullAddress()).'}';
					if (!empty($gContact->poste)) {
						$jsonData .=',{"title": '.json_encode($gContact->poste).'}';
					}
					$jsonData .='],';
				}
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

		// uncomment for debugging :
		if (getDolGlobalInt('GOOGLE_DEBUG')) {
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert.json", $jsonData);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		}

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
			if (getDolGlobalInt('GOOGLE_DEBUG')) {
				file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.json", $responseJson);
				@chmod(DOL_DATA_ROOT . "/dolibarr_google_massinsert_response.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
			}

			$res = parseResponse($responseJson);
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
 * Retreive tags of an entity for google contacts
 *
 * @param 	int 	$id 	Entity id
 * @param 	string	$type	Type of entity (thirdparty, contact or member)
 * @return	array			Array of tags
 */
function getTags($id, $type) {

	global $db;
	if ($type == 'thirdparty') {
		$sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'categorie lc';
		$sql .= ' JOIN '.MAIN_DB_PREFIX.'categorie_societe lcm ON lc.rowid = lcm.fk_categorie';
		$sql .= ' WHERE lcm.fk_soc = '.$id;
	} elseif ($type == 'contact') {
		$sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'categorie lc';
		$sql .= ' JOIN '.MAIN_DB_PREFIX.'categorie_contact lcm ON lc.rowid = lcm.fk_categorie';
		$sql .= ' WHERE lcm.fk_socpeople = '.$id;
	} elseif ($type == 'member') {
		$sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'categorie lc';
		$sql .= ' JOIN '.MAIN_DB_PREFIX.'categorie_member lcm ON lc.rowid = lcm.fk_categorie';
		$sql .= ' WHERE lcm.fk_member = '.$id;
	}

	$ressql = $db->query($sql);
	$tags = array();
	if ($ressql) {
		while ($obj = $db->fetch_object($ressql)) {
			$tags[] = array('id' => $obj->rowid, 'label' => $obj->label, 'type' => $type);
		}
	}
	return $tags;
}




// /**
//  * Retreive a googleGroupID for a groupName.
//  * If the groupName does not exist on Gmail account, it is also created. So this method should always return a group ID (except on technical error)
//  *
//  * @param	array	$gdata			Array with tokens info
//  * @param	string	$groupName		Group name
//  * @param	array	$googleGroups	Array of Google Group we know they already exists
//  * @param	string	$useremail		User email
//  * @return 	string					Google Group Full URL ID for groupName (also key in $googleGroups) or 'ErrorFailedToGetGroups'.
//  */
// function getGoogleGroupID($gdata, $groupName, &$googleGroups = array(), $useremail = 'default')
// {
// 	global $conf;

// 	$error=0;

// 	// Search existing groups
// 	if (! is_array($googleGroups) || count($googleGroups) == 0) {
// 		$document = new DOMDocument("1.0", "utf-8");
// 		$xmlStr = getContactGroupsXml($gdata, $useremail);
// 		if (! empty($xmlStr)) {
// 			$resultloadxml = $document->loadXML($xmlStr);
// 			if ($resultloadxml === false) {
// 				dol_syslog("getGoogleGroupID Failed to parse xml string ".$xmlStr, LOG_WARNING);
// 			} else {
// 				$xmlStr = $document->saveXML();
// 				$entries = $document->documentElement->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "entry");
// 				$n = $entries->length;
// 				$googleGroups = array();
// 				foreach ($entries as $entry) {
// 					$titleNodes = $entry->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "title");
// 					if ($titleNodes->length == 1) {
// 						$title = $titleNodes->item(0)->textContent;	// We got the title of a group (For example: 'System Group: My Contacts', 'System Group: Friend', 'Dolibarr (Thirdparties)', ...)
// 						$googleIDNodes = $entry->getElementsByTagNameNS(constant('ATOM_NAME_SPACE'), "id");
// 						if ($googleIDNodes->length == 1) {
// 							$googleGroups[$title] = $googleIDNodes->item(0)->textContent;	// We get <id> of group
// 						}
// 					}
// 				}
// 				dol_syslog("getGoogleGroupID We found ".count($googleGroups)." groups", LOG_DEBUG);
// 			}
// 		} else {
// 			$error++;
// 			dol_syslog("getGoogleGroupID ErrorFailedToGetGroups", LOG_ERR);
// 			return 'ErrorFailedToGetGroups';
// 		}
// 	}

// 	// Create group if it does not exists
// 	if (! $error && !isset($googleGroups[$groupName])) {
// 		$newGroupID = insertGContactGroup($gdata, $groupName, $useremail);
// 		$googleGroups[$groupName] = $newGroupID;
// 	}

// 	dol_syslog("Full URL ID found for group ".$groupName." = ".$googleGroups[$groupName], LOG_DEBUG);
// 	return $googleGroups[$groupName];
// }


// /**
//  * Retreive a Xml string with list of all groups of contacts from Google
//  *
//  * @param	array	$gdata			Array with tokens info
//  * @param	string	$useremail		User email
//  * @return	string					XML string with all groups, '' if error
//  */
// function getContactGroupsXml($gdata, $useremail = 'default')
// {
// 	global $conf;
// 	global $tag_debug;

// 	$tag_debug='groupgroups';

// 	$xmlStr='';
// 	try {
// 		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
// 			$access_token=$gdata['google_web_token']['access_token'];
// 		} else {
// 			$tmp=json_decode($gdata['google_web_token']);
// 			$access_token=$tmp->access_token;
// 		}
// 		$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token);
// 		$addheaderscurl=array('GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/atom+xml');
// 		//$useremail='default';

// 		//$request=new Google_Http_Request('https://www.google.com/m8/feeds/groups/'.urlencode($useremail).'/full?max-results=1000', 'GET', $addheaders, null);
// 		//$result=$gdata['client']->execute($request);	// Return json_decoded string. May return an exception.
// 		//$xmlStr=$result;

// 		$result = getURLContent('https://www.google.com/m8/feeds/groups/'.urlencode($useremail).'/full?max-results=1000', 'GET', '', 0, $addheaderscurl);
// 		$xmlStr = $result['content'];

// 		// uncomment for debugging :
// 		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $xmlStr);
// 		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
// 		// you can view this file with 'xmlstarlet fo dolibarr_google_groups.xml' command

// 		if (strpos($xmlStr, 'Contacts API is being deprecated') === 0) {
// 			// $xmlStr may be the error message "Contacts API is being deprecated. Migrate to People API to retain programmatic access to Google Contacts. See https://developers.google.com/people/contacts-api-migration."
// 			dol_syslog("getContactGroupsXml Failed because Google Contact API are now closed", LOG_WARNING);
// 			return '';
// 		}

// 		try {
// 			$document = new DOMDocument("1.0", "utf-8");
// 			$resultloadxml = $document->loadXml($xmlStr);
// 			if ($resultloadxml === false) {
// 				dol_syslog("getContactGroupsXml Failed to parse xml string ".$xmlStr, LOG_WARNING);
// 			} else {
// 				$errorselem = $document->getElementsByTagName("errors");
// 				//var_dump($errorselem);
// 				//var_dump($errorselem->length);
// 				//var_dump(count($errorselem));
// 				if ($errorselem->length) {
// 					dol_syslog('getContactGroupsXml ERROR:'.$result['content'], LOG_ERR);
// 					return '';
// 				}
// 			}
// 		} catch (Exception $e) {
// 			dol_syslog('getContactGroupsXml ERROR:'.$e->getMessage(), LOG_ERR);
// 			return '';
// 		}
// 	} catch (Exception $e) {
// 		dol_syslog(sprintf("getContactGroupsXml Error while getting feeds xml groups : %s", $e->getMessage()), LOG_ERR);
// 		file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", $e->getMessage());
// 		@chmod(DOL_DATA_ROOT . "/dolibarr_google_groups_response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
// 	}

// 	return($xmlStr);
// }



/**
 * Link groups of the contacts in google contact
 * @param	array	$gdata			Array with tokens info
 * @param 	array 	$gContacts		Array of object GContact
 * @param	string	$type			Type of contact ('thirdparty', 'contact' or 'member')
 * @param	string	$useremail		User email
 *
 * @return	int						>0 if OK, <0 if KO
 */
function updateGContactGroups($gdata, $gContacts, $type, $useremail = 'default') {

	global $db, $conf;

	// For each contact :
	foreach ($gContacts as $gContact) {

		// Retreive google id from gContact
		$dolID = $gContact->dolID;

		if ($type == 'thirdparty') {
			$sql = 'SELECT ref_ext FROM '.MAIN_DB_PREFIX.'societe WHERE rowid = '.((int) $dolID);
		} elseif ($type == 'contact') {
			$sql = 'SELECT ref_ext FROM '.MAIN_DB_PREFIX.'socpeople WHERE rowid = '.((int) $dolID);
		} elseif ($type == 'member') {
			$sql = 'SELECT ref_ext FROM '.MAIN_DB_PREFIX.'adherent WHERE rowid = '.((int) $dolID);
		} else {
			return -1;
		}

		$ressql = $db->query($sql);
		if ($ressql) {
			$obj = $db->fetch_object($ressql);
			$googleID = $obj->ref_ext;
			$reg = array();
			if (!empty($googleID) && preg_match('/google:(people\/.*)/', $googleID, $reg)) {
				$googleID = $reg[1];
			}
			else {
				dol_syslog('updateGContactGroups Error: invalid googleID for dolID='.$dolID, LOG_ERR);
			}
		} else {
			dol_syslog('updateGContactGroups Error:'.$db->lasterror(), LOG_ERR);
			return -1;
		}

		//var_dump($type);

		// Set group for element type
		$typeGroupID = getGContactTypeGroupID($gdata, $type);
		if (is_numeric($typeGroupID) && $typeGroupID < 0) {
			dol_syslog('updateGContactGroups Error: typeGroupID not found for type='.$type, LOG_ERR);
			return -1;
		}

		$ret = googleLinkGroup($gdata, $typeGroupID, $googleID);
		if (!is_numeric($ret) || $ret < 0) {
			dol_syslog('updateGContactGroups Error: googleLinkGroup failed for googleID='.$googleID.' groupID='.$typeGroupID, LOG_ERR);
			return $ret;
		}

		// Retreive tags/categories of object from gContact
		if (getDolGlobalInt('GOOGLE_CONTACT_SYNC_ALL_TAGS')) {
			$tags = $gContact->tags;
			// For each tag/category :
			foreach ($tags as $tag) {
				// Retreive groupe id from google contact (if not exist, create it)
				$groupID = getGContactGroupID($gdata, $tag, $useremail);
				if ($groupID < 0) {
					dol_syslog('updateGContactGroups Error: groupID not found for tag='.$tag, LOG_ERR);
					return $groupID;
				}

				// Link contact to group

				// prepare json data
				$ret = googleLinkGroup($gdata, $groupID, $googleID);
				if (!is_numeric($ret) || $ret < 0) {
					dol_syslog('updateGContactGroups Error: googleLinkGroup failed for googleID='.$googleID.' groupID='.$groupID, LOG_ERR);
					return $ret;
				}
			}
		}
	}

	return 1;
}

/**
 * Get the group id of Google that is stored into Dolibarr setup.
 *
 * @param 	array 	$gdata 	Google data array for API use
 * @param 	string 	$type 	type of element (thirdparty, contact or member) we search Google ID for
 * @return 	string 			Group id or < 0 if error, 0 if already exists
 */
function getGContactTypeGroupID($gdata, $type)
{
	require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
	global $db, $conf;


	if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
		$access_token=$gdata['google_web_token']['access_token'];
	} else {
		$tmp=json_decode($gdata['google_web_token']);
		$access_token=$tmp->access_token;
	}

	$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');

	$groupID = ''; $tagprefix = '';
	$reg = array();
	if ($type === 'thirdparty') {
		$tagprefix = 'GOOGLE_TAG_PREFIX';
		$label = empty($conf->global->GOOGLE_TAG_PREFIX) ? 'Dolibarr Thirdparties': $conf->global->GOOGLE_TAG_PREFIX;
		// See if ref_ext exists and if it is a google group
		if (!empty($conf->global->GOOGLE_TAG_REF_EXT) && preg_match('/google:(contactGroups\/.*)/', $conf->global->GOOGLE_TAG_REF_EXT, $reg)) {
			$groupID = $reg[1];
		}
	} else if ($type === 'contact') {
		$tagprefix = 'GOOGLE_TAG_PREFIX_CONTACTS';
		$label = empty($conf->global->GOOGLE_TAG_PREFIX_CONTACTS) ? 'Dolibarr contacts': $conf->global->GOOGLE_TAG_PREFIX_CONTACTS;
		// See if ref_ext exists and if it is a google group
		if (!empty($conf->global->GOOGLE_TAG_REF_EXT_CONTACTS) && preg_match('/google:(contactGroups\/.*)/', $conf->global->GOOGLE_TAG_REF_EXT_CONTACTS, $reg)) {
			$groupID = $reg[1];
		}
	} else if ($type === 'member') {
		$tagprefix = 'GOOGLE_TAG_PREFIX_MEMBERS';
		$label = empty($conf->global->GOOGLE_TAG_PREFIX_MEMBERS) ? 'Dolibarr members': $conf->global->GOOGLE_TAG_PREFIX_MEMBERS;
		// See if ref_ext exists and if it is a google group
		if (!empty($conf->global->GOOGLE_TAG_REF_EXT_MEMBERS) && preg_match('/google:(contactGroups\/.*)/', $conf->global->GOOGLE_TAG_REF_EXT_MEMBERS, $reg)) {
			$groupID = $reg[1];
		}
	} else {
		return -1;
	}

	// A groupID was set into setup.
	// To be sur that group exists in google contact, we search it.
	// Note: a groupID must be a hex number or a value among [contactGroups/all, contactGroups/blocked, contactGroups/chatBuddies, contactGroups/coworkers, contactGroups/family, contactGroups/friends, contactGroups/myContacts, contactGroups/starred]
	if ($groupID) {
		// We found the value of groupID into the cached constant GOOGLE_TAG_REF_EXT.... so we have it and we don't have to create it.
		if (getDolGlobalInt("GOOGLE_TAG_REF_EXT_NOCACHE") == 2) {
			// Check that the group exists
			$result = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
			$jsonStr = $result['content'];
			$json = json_decode($jsonStr);
			if (empty($json->error)) {
				dol_syslog("We found the value of groupID = ".$groupID." for type = ".$type." and check on Google says it exists");
				return $groupID;
			} else {
				dol_syslog("We found the value of groupID = ".$groupID." for type = ".$type." but check on Google says it does not exists so we continue as if it was not defined");
			}
		} elseif (getDolGlobalInt("GOOGLE_TAG_REF_EXT_NOCACHE") == 1) {
			dol_syslog("We found the value of groupID = ".$groupID." for type = ".$type." but option GOOGLE_TAG_REF_EXT_NOCACHE ask to ignore it so we continue as if it was not defined");
		} else {
			dol_syslog("We found the value of groupID for type = ".$type." into the cached constant GOOGLE_TAG_REF_EXT... = ".$groupID);
			return $groupID;
		}
	}

	// Group not found, we create it
	// We create it
	if (! in_array($label, array('contactGroups/all', 'contactGroups/blocked', 'contactGroups/chatBuddies', 'contactGroups/coworkers', 'contactGroups/family', 'contactGroups/friends', 'contactGroups/myContacts', 'contactGroups/starred'))) {

		$jsonData = '{';
		$jsonData .= '"contactGroup":{';
		$jsonData .= '"name": "'.$label.'"';
		$jsonData .= '}';
		$jsonData .= '}';

		// uncomment for debugging :
		if (getDolGlobalInt('GOOGLE_DEBUG')) {
			file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_contactGroups.json", $jsonData);
			@chmod(DOL_DATA_ROOT . "/dolibarr_google_contactGroups.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
		}

		$result = getURLContent('https://people.googleapis.com/v1/contactGroups', 'POST', $jsonData, 0, $addheaderscurl);
		$jsonStr = $result['content'];
		try {
			// uncomment for debugging :
			if (getDolGlobalInt('GOOGLE_DEBUG')) {
				file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_contactGroups_response.json", $jsonStr);
				@chmod(DOL_DATA_ROOT . "/dolibarr_google_contactGroups_response.json", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
			}

			$json = json_decode($jsonStr);
			if (!empty($json->error)) {
				if ($json->error->status == 'ALREADY_EXISTS') {
					// If we got an error saying it already exists, we get list of all existing groups
					$result = getURLContent('https://people.googleapis.com/v1/contactGroups', 'GET', '', 0, $addheaderscurl);
					$jsonStrListOfGrp = $result['content'];
					$jsonListOfGrp = json_decode($jsonStrListOfGrp, true);
					if (!empty($jsonListOfGrp['contactGroups'])) {
						foreach($jsonListOfGrp['contactGroups'] as $key => $val) {
							if ($val['name'] == $label) {
								$groupID = $val['resourceName'];
								break;
							}
						}
					}
					/*
					var_dump($groupID);
					var_dump($jsonData);
					var_dump($json);exit;
					*/
					dol_syslog('insertGContactGroup The group '.getDolGlobalString($tagprefix).' seems to already exists', LOG_DEBUG);
					//return 'The group '.getDolGlobalString($tagprefix).' seems to already exists';
					if (empty($groupID)) {
						dol_syslog('insertGContactGroup ...but we failed to find its ID.', LOG_DEBUG);
						return 0;
					}
				} else {
					dol_syslog('insertGContactGroup Error:'.$json->error->message.' '.getDolGlobalString($tagprefix), LOG_ERR);
					//return $json->error->message.' '.getDolGlobalString($tagprefix);
					return -1;
				}
			}
		} catch (Exception $e) {
			dol_syslog('insertGContactGroup Error:'.$e->getMessage(), LOG_ERR);
			return -1;
		}

		// Now we set external ref in conf
		if (empty($groupID)) {
			$json = json_decode($jsonStr);
			if (!empty($json)) {
				$groupID = $json->resourceName;
			} else {
				dol_syslog('insertGContactGroup Error:'.$jsonStr, LOG_ERR);
				return -1;
			}
		}
	} else {
		$groupID = $label;
	}

	// Now we save the group ID for $type into database
	$res = 0;
	if ($type === 'thirdparty') {
		$res = dolibarr_set_const($db, 'GOOGLE_TAG_REF_EXT', "google:".$groupID, 'chaine', 0, '', $conf->entity);
	} else if ($type === 'contact') {
		$res = dolibarr_set_const($db, 'GOOGLE_TAG_REF_EXT_CONTACTS', "google:".$groupID, 'chaine', 0, '', $conf->entity);
	} else if ($type === 'member') {
		$res = dolibarr_set_const($db, 'GOOGLE_TAG_REF_EXT_MEMBERS', "google:".$groupID, 'chaine', 0, '', $conf->entity);
	}

	if ($res > 0) {
		return $groupID;
	} else {
		dol_syslog('insertGContactGroup Error:'.$db->lasterror(), LOG_ERR);
		return -1;
	}
}


/**
 * Get group id from google contact. Create it if not found.
 *
 * @param	array		$gdata			Array with tokens info
 * @param 	array 		$tag			Array with tag info to retreive or create into Google Contact
 * @param	string		$useremail		User email
 * @return 	string|int					Google Group ID string or < 0 if KO
 */
function getGContactGroupID($gdata, $tag, $useremail = 'default') {

	global $db;

	$tagID = $tag['id'];
	$sql = 'SELECT ref_ext FROM '.MAIN_DB_PREFIX.'categorie as la WHERE la.rowid = '.((int) $tagID);

	$ressql = $db->query($sql);
	if (!$ressql) {
		dol_syslog('getGContactGroupID Error:'.$db->lasterror(), LOG_ERR);
		return -9;
	}

	$obj = $db->fetch_object($ressql);
	$groupID = $obj->ref_ext;

	$reg = array();
	if (!empty($groupID) && preg_match('/google:(contactGroups\/.*)/', $groupID, $reg)) {
		$groupID = $reg[1];
		// To be sur that group is in google contact
		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
		$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
		$result = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$jsonStr = $result['content'];
		$json = json_decode($jsonStr);
		if (!empty($json->error)) {
			if ($json->error->status == 'NOT_FOUND') {
				// Group not found, we create it
				$objectstatic = new Categorie($db);
				$groupID = insertGContactGroup($gdata, $tag, $objectstatic, $useremail);
			} else {
				dol_syslog('getGContactGroupID Error:'.$json->error->message, LOG_ERR);
				return -1;
			}
		}
	} else {
		// Create group
		$objectstatic = new Categorie($db);
		$groupID = insertGContactGroup($gdata, $tag, $objectstatic, $useremail);
		if ($groupID < 0) {
			dol_syslog('getGContactGroupID Error: insertGContactGroup failed for tag='.$tag, LOG_ERR);
			return $groupID;
		}
	}

	return $groupID;
}



/**
 * Create a group/label into Google contact
 *
 * @param	array	$gdata			Array with tokens info
 * @param 	string 	$groupName		Group name to create into Google Contact
 * @param	string	$useremail		User email
 * @return string					created group ID
 */
function insertGContactGroup($gdata, $tag, $objectstatic, $useremail = 'default')
{
	global $conf;
	// Prepare json data for POST request
	$jsonData = '{';
	$jsonData .= '"contactGroup":{';
	$jsonData .= '"name": "'.$tag['label'].'"';
	$jsonData .= '}';
	$jsonData .= '}';


	// Send request to Google
	if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
		$access_token=$gdata['google_web_token']['access_token'];
	} else {
		$tmp=json_decode($gdata['google_web_token']);
		$access_token=$tmp->access_token;
	}

	$addheaders=array('GData-Version'=>'3.0', 'Authorization'=>'Bearer '.$access_token);
	$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
	$result = getURLContent('https://people.googleapis.com/v1/contactGroups', 'POST', $jsonData, 0, $addheaderscurl);
	$jsonStr = $result['content'];
	try {
		$json = json_decode($jsonStr);
		if (!empty($json->error)) {
			dol_syslog('insertGContactGroup Error:'.$json->error->message, LOG_ERR);
			return -1;
		}
	} catch (Exception $e) {
		dol_syslog('insertGContactGroup Error:'.$e->getMessage(), LOG_ERR);
		return -1;
	}
	// Now update record into database with external ref
	if (is_object($objectstatic)) {
		$json = json_decode($jsonStr);
		if (!empty($json)) {
			$groupID = $json->resourceName;
			$objectstatic->id = $tag['id'];
			$objectstatic->update_ref_ext("google:".$groupID);
		}
	}

	return $groupID;
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
