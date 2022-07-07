<?php
/* Copyright (C) 2012-2013 Philippe Berthet    <berthet@systune.be>
 * Copyright (C) 2013      Laurent Destailleur <eldy@users.sourceforge.net>
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
 */

/**
 *	\file       htdocs/google/class/gcontacts.class.php
 *  \ingroup    google
 *  \brief      class GContacts
 */

dol_include_once('/google/lib/google_contact.lib.php');


/**
 * GContact
 */
class GContact
{
	const ATOM_NAME_SPACE = "http://www.w3.org/2005/Atom";
	const REL_WORK='http://schemas.google.com/g/2005#work';
	const REL_MOBILE='http://schemas.google.com/g/2005#mobile';
	const REL_HOME='http://schemas.google.com/g/2005#home';
	const REL_WORK_FAX='http://schemas.google.com/g/2005#work_fax';
	const GOOGLE_SYSTEM_GROUP_MYCONTACTS='System Group: My Contacts';
	const GOOGLE_SYSTEM_GROUP='System Group: ';
	const MAX_RETREIVE=800;

	var $dolID;
	var $fullName;
	var $firstname;
	var $lastname;
	var $addr;
	var $phone_pro;
	var $phone_perso;
	var $phone_mobile;
	var $fax;
	var $email;
	var $company;
	var $orgName;
	var $poste;
	var $googleID;
	var $lastMod;
	public $doc;
	public $atomEntry;

	/**
	 * Constructor
	 *
	 * @param string 	$dolID		Dolibarr id
	 * @param string 	$type		Type of Google contact
	 * @param Gdata		$gdata		Gdata handler
	 */
	public function __construct($dolID, $type, $gdata)
	{
		global $conf;

		if ($dolID) {
			$this->from='dolibarr';
			$this->dolID = $dolID;

			$useremail = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;

			if ($type == 'thirdparty') $this->fetchThirdpartyFromDolibarr($gdata, $useremail);
			elseif ($type == 'contact') $this->fetchContactFromDolibarr($gdata, $useremail);
			elseif ($type == 'member') $this->fetchMemberFromDolibarr($gdata, $useremail);
			else dol_print_error('', 'Bad value for type');
		} else {
			$this->from='gmail';
		}
	}

	/**
	 * appendCustomField
	 *
	 * @param 	string $key			Key
	 * @param 	string $value			Value
	 * @return	void
	 */
	private function appendCustomField($key, $value)
	{
		$el = $this->doc->createElement('gcontact:userDefinedField');
		$el->setAttribute("key", $key);
		$el->setAttribute("value", htmlspecialchars($value));
		$this->atomEntry->appendChild($el);
	}

	/**
	 * appendEmail
	 *
	 * @param 	string $rel			Rel			Rel
	 * @param 	string $email			Email		EMail
	 * @param 	boolean $isPrimary	isPrimary	isPrimary
	 * @param 	string $label			Label		Label
	 * @return	void
	 */
	private function appendEmail($rel, $email, $isPrimary, $label = null)
	{
		if (empty($email)) return;
		$el = $this->doc->createElement('gdata:email');
		if ($label) {
			$el->setAttribute('label', $label);
		} else {
			$el->setAttribute('rel', $rel);
		}
		$el->setAttribute('address', $email);
		if ($isPrimary)
			$el->setAttribute('primary', 'true');
		$this->atomEntry->appendChild($el);
	}

	/**
	 * appendTextElement
	 *
	 * @param	DOMElement	$el			DOMElement
	 * @param 	string 		$elName		elName
	 * @param 	string 		$text		Text
	 * @return	void
	 */
	private function appendTextElement(DOMElement $el, $elName, $text)
	{
		if (empty($text)) return;
		//print_r(htmlspecialchars($text));
		$el->appendChild($this->doc->createElement($elName, htmlspecialchars($text)));
	}

	/**
	 * appendPostalAddress
	 *
	 * @param 	string $rel			Rel			Rel
	 * @param 	GCaddr $addr		Addr		Addr
	 * @param 	string $label		Label		Label
	 * @return	void
	 */
	private function appendPostalAddress($rel, GCaddr $addr = null, $label = null)
	{
		if (empty($addr)) return;
		$el = $this->doc->createElement("gdata:structuredPostalAddress");
		if ($label) {
			$el->setAttribute('label', $label);
		} else {
			$el->setAttribute('rel', $rel);
		}
		self::appendTextElement($el, "gdata:street", $addr->street);
		self::appendTextElement($el, "gdata:postcode", $addr->zip);
		self::appendTextElement($el, "gdata:city", $addr->town);
		self::appendTextElement($el, "gdata:region", dolEscapeXMLWithNoAnd($addr->state));		// La region pose des pb si il y a &amp; dedans alors que ok pour le street. Note dans ce mode on retrouve du &#38 alors que &amp; en mode update simple dans le source xml. On les remplace par -.
		self::appendTextElement($el, "gdata:country", $addr->country);
		$this->atomEntry->appendChild($el);
	}

	/**
	 * appendPhoneNumber
	 *
	 * @param 	string $rel				Rel
	 * @param 	string $phoneNumber		PhoneNumber
	 * @param 	boolean $isPrimary		IsPrimary
	 * @param 	string $label			Label
	 * @return	void
	 */
	private function appendPhoneNumber($rel, $phoneNumber, $isPrimary, $label = null)
	{
		if (empty($phoneNumber)) return;
		$el = $this->doc->createElement('gdata:phoneNumber');
		if ($label) {
			$el->setAttribute('label', $label);
		} else {
			$el->setAttribute('rel', $rel);
		}
		$el->appendChild($this->doc->createTextNode($phoneNumber));
		$this->atomEntry->appendChild($el);
	}

	/**
	 * appendWebSite
	 *
	 * @param 	string $href		Href
	 * @return	void
	 */
	private function appendWebSite($href)
	{
		if (empty($href)) return;
		$el = $this->doc->createElement('gcontact:website');
		$el->setAttribute("label", "URL");
		$el->setAttribute("href", $href);
		$this->atomEntry->appendChild($el);
	}

	/**
	 * appendInstantMessaging
	 *
	 * @param string $label		Label
	 * @param string $im		IM address
	 * @param string $protocol	Protocol
	 * @return	void
	 */
	private function appendInstantMessaging($label, $im, $protocol)
	{
		$el = $this->doc->createElement('gdata:im');
		$el->setAttribute("protocol", $protocol);
		$el->setAttribute("label", $label); // Labels are not really visible in interface
		$el->setAttribute("address", $im);
		$this->atomEntry->appendChild($el);
	}

	/**
	 * appendRelation
	 *
	 * @param 	string $label		Label
	 * @param 	string $value		Href
	 * @return	void
	 */
	private function appendRelation($label, $value)
	{
		//Relationships
		$el = $this->doc->createElement('gcontact:relation');
		$el->setAttribute("label", $label);
		$el->appendChild($this->doc->createTextNode($value));
		$this->atomEntry->appendChild($el);
	}

	/**
	 * Create group
	 *
	 * @param	Gdata	$gdata		Gdata handler
	 * @param 	string 	$groupName	Name of group
	 * @param	string	$useremail	User email
	 * @return	void
	 */
	private function appendGroup($gdata, $groupName, $useremail)
	{
		$el = $this->doc->createElement("gcontact:groupMembershipInfo");
		$el->setAttribute("deleted", "false");
		$href = self::getGoogleGroupID($gdata, $groupName, $useremail);
		$el->setAttribute("href", $href);
		$this->atomEntry->appendChild($el);
	}

	/**
	 * Fill the GContact class from a dolibarID
	 *
	 * @param	Gdata		$gdata		Gdata handler
	 * @param	string		$useremail	User email
	 * @return 	GContact
	 */
	private function fetchThirdpartyFromDolibarr($gdata, $useremail)
	{
		global $conf,$langs;

		if ($this->dolID==null) throw new Exception('Internal error: dolID is null');
		global $db, $langs, $conf;
		require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";
		require_once DOL_DOCUMENT_ROOT."/societe/class/societe.class.php";

		$dolContact = new Societe($db);
		$result=$dolContact->fetch($this->dolID);
		if ($result==0)
			throw new Exception('Internal error: Thirdparty with ID '.$this->dolID.' not found');
		if ($result==0)
			throw new Exception($dolContact->error);

		// Fill object with thirdparty infos
		$this->firstname = $dolContact->firstname;
		$this->lastname = $dolContact->lastname;
		$this->name = $dolContact->name;
		$this->fullName = $dolContact->getFullName($langs);
		$this->email = $dolContact->email?$dolContact->email:($this->fullName.'@noemail.com');
		if (!(empty($dolContact->address)&&empty($dolContact->zip)&&empty($dolContact->town)&&empty($dolContact->state)&&empty($dolContact->country))) {
			$this->addr = new GCaddr();
			$this->addr->street = $dolContact->address;
			$this->addr->zip = $dolContact->zip;
			$this->addr->town = $dolContact->town;
			$this->addr->state = $dolContact->state;
			$this->addr->country = $dolContact->country;
		}
		$this->phone_pro= !empty($dolContact->phone)?$dolContact->phone:"";               // For thirdparty, phone is phone and not phone_pro
		$this->phone_perso= !empty($dolContact->phone_perso)?$dolContact->phone_perso:"";       // For thirdparty, should be useless
		$this->phone_mobile= !empty($dolContact->phone_mobile)?$dolContact->phone_mobile:"";     // For thirdparty, should be useless
		$this->fax= $dolContact->fax;
		$this->socid= !empty($dolContact->socid)?$dolContact->socid:"";

		$google_nltechno_tag=getCommentIDTag();
		$idindolibarr=$this->dolID."/thirdparty";

		$this->note_public = $dolContact->note_public;
		if (strpos($this->note_public, $google_nltechno_tag) === false) $this->note_public .= "\n\n".$google_nltechno_tag.$idindolibarr;

		// Prepare the DOM for google
		$this->doc = new DOMDocument("1.0", "utf-8");
		$this->doc->formatOutput = true;
		$this->atomEntry = $this->doc->createElement('atom:entry');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');

		// add name element
		$el = $this->doc->createElement('gdata:name');
		$this->appendTextElement($el, 'gdata:familyName', $this->lastname?$this->lastname:$this->name);
		$this->appendTextElement($el, 'gdata:givenName', $this->firstname);
		//$this->appendTextElement($doc, $el, 'gdata:additionalName', $middleName);
		//$this->appendTextElement($doc, $el, 'gdata:namePrefix', $peopleTitle);
		$this->atomEntry->appendChild($el);

		$elfullName = $this->doc->createElement('gdata:fullName', google_html_convert_entities(dol_htmlentities($this->fullName)));
		$el->appendChild($elfullName);

		// Note as comment and a custom field
		$this->atomEntry->appendChild($this->doc->createElement('atom:content', google_html_convert_entities(dol_htmlentities($this->note_public))));
		//$this->appendCustomField("Origin", 'Onelog');

		// Phones
		$this->appendPhoneNumber(self::REL_WORK, $this->phone_pro, true);
		$this->appendPhoneNumber(self::REL_HOME, $this->phone_perso, true);
		$this->appendPhoneNumber(self::REL_WORK_FAX, $this->fax, true);
		$this->appendPhoneNumber(self::REL_MOBILE, $this->phone_mobile, false);
		$this->appendPostalAddress(self::REL_WORK, $this->addr);
		$this->appendEmail(self::REL_WORK, $this->email, true);
		// Data from linked company
		if ($this->company) {
				$this->appendWebSite($doc, $this->atomEntry, $this->company->url);
				$norm_phone_pro = preg_replace("/\s/", "", $this->phone_pro);
				$norm_phone_pro = preg_replace("/\./", "", $norm_phone_pro);
				$norm_phone_perso = preg_replace("/\s/", "", $this->phone_perso);
				$norm_phone_perso = preg_replace("/\./", "", $norm_phone_perso);
				if ($norm_phone_pro != $this->company->phone && $norm_phone_perso != $this->company->phone)
					$this->appendPhoneNumber(null, $this->company->phone, false, $this->orgName);
				$norm_fax = preg_replace("/\s/", "", $this->fax);
				$norm_fax = preg_replace("/\./", "", $norm_fax);
				if ($norm_fax != $this->company->fax)
					$this->appendPhoneNumber(null, $this->company->fax, false, 'Fax '.$this->orgName);
				if ($this->addr != $this->company->addr)
					$this->appendPostalAddress(null /*rel*/, $this->company->addr, $this->orgName);
				if ($this->company->email != $this->email)
					$this->appendEmail(self::REL_WORK, $this->company->email, false, $this->orgName);
		}

		$userdefined = $this->doc->createElement('gcontact:userDefinedField');
		$userdefined->setAttribute('key', 'dolibarr-id');
		$userdefined->setAttribute('value', $idindolibarr);
		$this->atomEntry->appendChild($userdefined);

		// Add tags
		//$this->appendGroup($gdata, getTagLabel('thirdparties'), $useremail);
		//$this->doc->appendChild($this->atomEntry);
	}

	/**
	 * Fill GContact instance for this->dolID.
	 * Note: It creates groups if it not exists.
	 *
	 * @param	GData	$gdata		GData
	 * @param	string	$useremail	User email
	 * @return 	GContact
	 */
	private function fetchContactFromDolibarr($gdata, $useremail)
	{
		global $conf,$langs;

		if ($this->dolID==null) throw new Exception('Internal error: dolID is null');
		global $db, $langs, $conf;
		require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";
		require_once DOL_DOCUMENT_ROOT."/societe/class/societe.class.php";

		$dolContact = new Contact($db);
		$result=$dolContact->fetch($this->dolID);
		if ($result==0)
			throw new Exception('Internal error: Contact with ID '.$this->dolID.' not found');
		if ($result==0)
			throw new Exception($dolContact->error);

		// Fill object with contact infos
		$this->firstname = $dolContact->firstname;
		$this->lastname = $dolContact->lastname;
		$this->fullName = $dolContact->getFullName($langs);
		$this->email = ($dolContact->email?$dolContact->email:($this->fullName.'@noemail.com'));

		if (!(empty($dolContact->address)&&empty($dolContact->zip)&&empty($dolContact->town)&&empty($dolContact->state)&&empty($dolContact->country))) {
			$this->addr = new GCaddr();
			/*          $this->addr->street = dolEscapeXMLWithNoAnd($dolContact->address);
			$this->addr->zip = dolEscapeXMLWithNoAnd($dolContact->zip);
			$this->addr->town = dolEscapeXMLWithNoAnd($dolContact->town);
			$this->addr->region = dolEscapeXMLWithNoAnd($dolContact->state);
			$this->addr->country = dolEscapeXMLWithNoAnd($dolContact->country);
			*/
			$this->addr->street = $dolContact->address;
			$this->addr->zip = $dolContact->zip;
			$this->addr->town = $dolContact->town;
			$this->addr->state = $dolContact->state;
			$this->addr->country = $dolContact->country;
		}
		$this->phone_pro= $dolContact->phone_pro;
		$this->phone_perso= $dolContact->phone_perso;
		$this->phone_mobile= $dolContact->phone_mobile;
		$this->fax= $dolContact->fax;
		$this->socid= $dolContact->socid;
		if ($dolContact->socid) {
			$company = new Societe($db);
			$result=$company->fetch($dolContact->socid);
			if ($result <=0) throw new Exception($company->$error);
			$this->orgName=$company->name;
		}
		$this->poste= $dolContact->poste;

		$google_nltechno_tag=getCommentIDTag();
		$idindolibarr=$this->dolID."/contact";

		$this->note_public = $dolContact->note_public;
		if (strpos($this->note_public, $google_nltechno_tag) === false) $this->note_public .= "\n\n".$google_nltechno_tag.$idindolibarr;

		// Prepare the DOM for google
		$this->doc = new DOMDocument("1.0", "utf-8");
		$this->doc->formatOutput = true;
		$this->atomEntry = $this->doc->createElement('atom:entry');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');

		// Add name element
		$el = $this->doc->createElement('gdata:name');
		$this->appendTextElement($el, 'gdata:givenName', $this->firstname);
		$this->appendTextElement($el, 'gdata:familyName', $this->lastname);
		//$this->appendTextElement($doc, $el, 'gdata:additionalName', $middleName);
		//$this->appendTextElement($doc, $el, 'gdata:namePrefix', $peopleTitle);
		$this->atomEntry->appendChild($el);

		$elfullName = $this->doc->createElement('gdata:fullName', $this->fullName);
		$el->appendChild($elfullName);

		// Add organization element (company + function)
		if (! empty($this->orgName) && ! empty($this->poste)) {
			$elorg = $this->doc->createElement('gdata:organization');
			$elorg->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
			if (! empty($this->orgName)) $this->appendTextElement($elorg, 'gdata:orgName', $this->orgName);
			if (! empty($this->poste))   $this->appendTextElement($elorg, 'gdata:orgTitle', $this->poste);
			$this->atomEntry->appendChild($elorg);
		}

		// Note as comment and a custom field
		$this->atomEntry->appendChild($this->doc->createElement('atom:content', $this->note_public));
		//$this->appendCustomField("Origin", 'Onelog');

		// Phones
		$this->appendPhoneNumber(self::REL_WORK, $this->phone_pro, true);
		$this->appendPhoneNumber(self::REL_HOME, $this->phone_perso, true);
		$this->appendPhoneNumber(self::REL_WORK_FAX, $this->fax, true);
		$this->appendPhoneNumber(self::REL_MOBILE, $this->phone_mobile, false);
		$this->appendPostalAddress(self::REL_WORK, $this->addr);
		$this->appendEmail(self::REL_WORK, $this->email, true);
		// Data from linked company
		/*if ($this->company) {
			$this->appendWebSite($doc, $this->atomEntry, $this->company->url);
		}*/
		//$this->appendWebSite($doc, $this->atomEntry, '???');


		$userdefined = $this->doc->createElement('gcontact:userDefinedField');
		$userdefined->setAttribute('key', 'dolibarr-id');
		$userdefined->setAttribute('value', $idindolibarr);
		$this->atomEntry->appendChild($userdefined);

		// Add tags
		$this->appendGroup($gdata, getTagLabel('contacts'), $useremail);
		$this->doc->appendChild($this->atomEntry);
	}

	/**
	 * Fill GContact instance for this->dolID.
	 * Note: It creates groups if it not exists.
	 *
	 * @param	GData	$gdata		GData
	 * @param	string	$useremail	User email
	 * @return 	GContact
	 */
	private function fetchMemberFromDolibarr($gdata, $useremail)
	{
		global $conf,$langs;

		if ($this->dolID==null) throw new Exception('Internal error: dolID is null');
		global $db, $langs, $conf;
		require_once DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php";

		$dolContact = new Adherent($db);
		$result=$dolContact->fetch($this->dolID);
		if ($result==0)
			throw new Exception('Internal error: Member with ID '.$this->dolID.' not found');
		if ($result==0)
			throw new Exception($dolContact->error);

		// Fill object with contact infos
		$this->firstname = $dolContact->firstname;
		$this->lastname = $dolContact->lastname;
		$this->fullName = $dolContact->getFullName($langs);
		if (empty($this->fullName)) $this->fullName=$dolContact->company;
		$this->email = ($dolContact->email?$dolContact->email:($this->fullName.'@noemail.com'));
		if (!(empty($dolContact->address)&&empty($dolContact->zip)&&empty($dolContact->town)&&empty($dolContact->state)&&empty($dolContact->country))) {
			$this->addr = new GCaddr();
			$this->addr->street = $dolContact->address;
			$this->addr->zip = $dolContact->zip;
			$this->addr->town = $dolContact->town;
			$this->addr->state = $dolContact->state;
			$this->addr->country = $dolContact->country;
		}
		$this->phone_pro= $dolContact->phone_pro;
		$this->phone_perso= $dolContact->phone_perso;
		$this->phone_mobile= $dolContact->phone_mobile;
		$this->fax= $dolContact->fax;
		$this->orgName=$dolContact->company;

		$google_nltechno_tag=getCommentIDTag();
		$idindolibarr=$this->dolID."/member";

		$this->note_public = $dolContact->note_public;
		if (strpos($this->note_public, $google_nltechno_tag) === false) $this->note_public .= "\n\n".$google_nltechno_tag.$idindolibarr;

		// Prepare the DOM for google
		$this->doc = new DOMDocument("1.0", "utf-8");
		$this->doc->formatOutput = true;
		$this->atomEntry = $this->doc->createElement('atom:entry');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
		$this->atomEntry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');

		// Add name element
		$el = $this->doc->createElement('gdata:name');
		$this->appendTextElement($el, 'gdata:givenName', $this->firstname);
		$this->appendTextElement($el, 'gdata:familyName', $this->lastname);
		//$this->appendTextElement($doc, $el, 'gdata:additionalName', $middleName);
		//$this->appendTextElement($doc, $el, 'gdata:namePrefix', $peopleTitle);
		$this->atomEntry->appendChild($el);

		$elfullName = $this->doc->createElement('gdata:fullName', $this->fullName);
		$el->appendChild($elfullName);

		// Add organization element (company + function)
		if (! empty($this->orgName)) {
			$elorg = $this->doc->createElement('gdata:organization');
			$elorg->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
			if (! empty($this->orgName)) $this->appendTextElement($elorg, 'gdata:orgName', $this->orgName);
			$this->atomEntry->appendChild($elorg);
		}

		// Note as comment and a custom field
		$this->atomEntry->appendChild($this->doc->createElement('atom:content', $this->note_public));
		//$this->appendCustomField("Origin", 'Onelog');

		// Phones
		$this->appendPhoneNumber(self::REL_WORK, $this->phone_pro, true);
		$this->appendPhoneNumber(self::REL_HOME, $this->phone_perso, true);
		$this->appendPhoneNumber(self::REL_WORK_FAX, $this->fax, true);
		$this->appendPhoneNumber(self::REL_MOBILE, $this->phone_mobile, false);
		$this->appendPostalAddress(self::REL_WORK, $this->addr);
		$this->appendEmail(self::REL_WORK, $this->email, true);
		// Data from linked company
		/*if ($this->company) {
		 $this->appendWebSite($doc, $this->atomEntry, $this->company->url);
		}*/
		//$this->appendWebSite($doc, $this->atomEntry, '???');

		$userdefined = $this->doc->createElement('gcontact:userDefinedField');
		$userdefined->setAttribute('key', 'dolibarr-id');
		$userdefined->setAttribute('value', $idindolibarr);
		$this->atomEntry->appendChild($userdefined);

		// Add tags
		$this->appendGroup($gdata, getTagLabel('members'), $useremail);
		$this->doc->appendChild($this->atomEntry);
	}

	/**
	 * Get list of googleContactsIDs matching the given pattern from Google contact
	 *
	 * @param	Gdata	$gdata		Gdata handler
	 * @param 	string 	$pattern	Pattern to filter query
	 * @param	string	$type		'thirdparty' or 'contact'
	 * @return 	string 				array of google contactsID, <0 if KO
	 */
	public static function getDolibarrContactsGoogleIDS($gdata, $pattern, $type)
	{
		global $conf, $tag_debug;

		dol_syslog(get_class().'::getDolibarrContactsGoogleIDS');

		if (empty($type)) return array();

		$document = new DOMDocument("1.0", "utf-8");

		// Get full list of contacts
		$tag_debug='getallcontacts';

		if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
			$access_token=$gdata['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($gdata['google_web_token']);
			$access_token=!empty($tmp->access_token)?$tmp->access_token:"";
		}
		$addheaders=array('authorization'=>'Bearer '.$access_token);
		$addheaderscurl=array('GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'Content-Type: application/json');
		//$useremail='default';

		$queryString = 'https://people.googleapis.com/v1/people/me/connections?personFields=userDefined';
		if (! empty($pattern)) $queryString .= '&q='.$pattern;
		$response = getURLContent($queryString, 'GET', '', 0, $addheaderscurl);

		if (is_array($response) && $response['http_code'] >= 400) {
			dol_syslog('Error http '.$response['http_code'], LOG_WARNING);
			return -1;
		}

		// return $response['content'].'NEXTPAGE?????2'.json_decode($response['content'])->nextPageToken;
		$jsonStr = $response['content'];
		$xmlStr=$response['content'];
		if ($response['content']) {
			$json = json_decode($jsonStr);
			/*$document = new DOMDocument("1.0", "utf-8");
			$document->loadXml($response['content']);

			$errorselem = $document->getElementsByTagName("errors");*/
			//var_dump($errorselem);
			//var_dump($errorselem->length);
			//var_dump(count($errorselem));
			if (!empty($json->error)) {
				dol_syslog($response['content'], LOG_ERR);
				return -1;
			}
		}

		// Split answers into entries array
		// $json = json_decode($jsonStr);
		$entries = $json->connections;


		// A page of fetched contacts has a maximum size of 100, so we may need to iterate over the pages
		$nextPageToken = $json->nextPageToken;
		while (!empty($nextPageToken)) {
			$queryString = 'https://people.googleapis.com/v1/people/me/connections?personFields=userDefined&pageToken='.$nextPageToken;
			if (! empty($pattern)) $queryString .= '&q='.$pattern;
			$response = getURLContent($queryString, 'GET', '', 0, $addheaderscurl);

			if (is_array($response) && $response['http_code'] >= 400) {
				dol_syslog('Error http '.$response['http_code'], LOG_WARNING);
				return -1;
			}

			$jsonStr = $response['content'];
			$xmlStr=$response['content'];
			if ($response['content']) {
				$json = json_decode($jsonStr);
				/*$document = new DOMDocument("1.0", "utf-8");
				$document->loadXml($response['content']);

				$errorselem = $document->getElementsByTagName("errors");*/
				//var_dump($errorselem);
				//var_dump($errorselem->length);
				//var_dump(count($errorselem));
				if (!empty($json->error)) {
					dol_syslog($response['content'], LOG_ERR);
					return -1;
				}
			}

			// Split answers into entries array
			// $json = json_decode($jsonStr);
			$entries = array_merge($entries, $json->connections);
			$nextPageToken = $json->nextPageToken;
		}
		//$document->loadXML($xmlStr);
		//$entries = $document->documentElement->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "entry");

		$n = count($entries);
		dol_syslog(get_class().'::getDolibarrContactsGoogleIDS '.$n.' contacts retrieved from google contacts');

		$tagtofind=getCommentIDTag();
		dol_syslog(get_class().'::getDolibarrContactsGoogleIDS Now search if contacts contains /'.preg_quote($tagtofind).'([0-9]+)\/'.$type.'/ regex');
		$googleIDs = array();	// Nothing to delete by default
		foreach ($entries as $entry) {
			// Try to qualify or not contact
			// TODO Use the dolibarr-id instead of comment
			//$contentNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");	//<atom:content type="text"> = note
			$contentNodes = !empty($entry->userDefined)?$entry->userDefined:null;
			if (!empty($contentNodes) && count($contentNodes) == 1) {
				$content = $contentNodes[0];
				//print $content."<br>";

				// Detect if contact is qualified to be deleted
				$reg = array();
				if ($content->key == "dolibarr-id" && preg_match('/([0-9]+)\/'.$type.'/m', $content->value, $reg)) {
					//$googleIDNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "id");
					//$googleEMail = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");
					//var_dump($googleEMail->item(0)->nodeValue);
					//$googleEMail = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");
					//var_dump($googleEMail->item(0)->nodeValue);
					$reg = array();
					$googleIDNodes = $entry->resourceName;
					if (!empty($googleIDNodes)) {
						$googleIDs[] = $googleIDNodes;
					}
				}
			}
		}

		dol_syslog(get_class().'::getDolibarrContactsGoogleIDS '.count($googleIDs).' contacts qualified as Dolibarr records');
		return($googleIDs);
	}


	/**
	 * Delete contacts marqued as comming from dolibarr on Gmail account
	 *
	 * @param	Gdata	$gdata		Gdata handler
	 * @param 	string 	$pattern	pattern : default is 'OnelogMarker' wich will supress all contacts comming from Dolibarr
	 *                         		To delete a specific contact, use 'OnelogMarker:XX#' where XX is the dolibarr ID of the contact
	 * @param	string	$type		'thirdparty' or 'contact'
	 * @return 	int 				<0 if KO, >=0 nb of deleted contacts
	 */
	public static function deleteDolibarrContacts($gdata, $pattern, $type)
	{
		// Search for id
		$googleIDs = self::getDolibarrContactsGoogleIDS($gdata, $pattern, $type);
		if ($googleIDs != '-1') {
			self::deleteEntries($gdata, $googleIDs, false);
			return(count($googleIDs));
		} else {
			return -1;
		}
	}



	/**
	 * insertGContactGroup
	 *
	 * @param	Gdata	$gdata			Gdata handler
	 * @param 	string 	$groupName		Name of group to create
	 * @param	string	$useremail		User email
	 * @return 	string					googlegroupID
	 */
	private static function insertGContactGroup($gdata, $groupName, $useremail)
	{
		dol_syslog("insertGContactGroup Create Google group ".$groupName);

		$groupid = '';

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
			//$entryResult = $gdata->insertEntry($xmlStr, 'https://www.google.com/m8/feeds/groups/default/full');
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
			//$groupid = $entryResult->id;
			$response = getURLContent('https://www.google.com/m8/feeds/groups/'.$useremail.'/full', 'POST', $xmlStr, 1, $addheaderscurl);

			$xmlStr = $response['content'];

			if (strpos($xmlStr, 'Contacts API is being deprecated') === 0) {
				// $xmlStr may be the error message "Contacts API is being deprecated. Migrate to People API to retain programmatic access to Google Contacts. See https://developers.google.com/people/contacts-api-migration."
				dol_syslog("getContactGroupsXml Failed because Google Contact API are now closed", LOG_WARNING);
				return '';
			}

			try {
				$document = new DOMDocument("1.0", 'utf-8');

				$document->loadXML($xmlStr);
				$xmlStr = $document->saveXML();

				foreach ($document->documentElement->childNodes as $item) {
					if ($item->nodeName == 'id') {
						$groupidstring = $item->nodeValue;		// For example http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/1559baea0c3177e7
						//$groupidarray = explode('/', $groupidstring);
						//$groupid = $groupidarray[count($groupidarray) - 1];
						$groupidstring = $groupidstring;
						break;
					}
				}

				//$groupid = $googleIDNodes->item(0)->textContent;
				dol_syslog("getContactGroupsXml groupid=".$groupid, LOG_WARNING);
			} catch(Exception $e) {
				print 'Error when parsing result of group creation in insertGContactGroup. Can\'t find groupid created.';
				return '';
			}

			//dol_syslog(sprintf("Inserting gContact group %s in google contacts for user %s google ID = %s", $groupName, $googleUser, $entryResult->id));
			dol_syslog(sprintf("Inserting gContact group %s in google contacts", $groupName));
		} catch (Exception $e) {
			dol_syslog("Problem while inserting group", LOG_ERR);
			throw new Exception(sprintf("Problem while inserting group %s : %s", $groupName, $e->getMessage()));
		}
		return($groupid);
	}

	/**
	 * Retreive a googleGroupID for a given groupName.
	 * If the groupName does not exist on Gmail account, it will be created as a side effect
	 *
	 * @param	Gdata	$gdata			Gdata handler
	 * @param	string	$groupName		Name of group
	 * @param	string	$useremail		User email
	 * @return 	array					Array of googleGroupID. Example: array(
	 *									'System Group: My Contacts' => string 'http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/6' (length=67)
	 *								  	'System Group: Friends' => string 'http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/d' (length=67)
	 *								  	'System Group: Family' => string 'http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/e' (length=67)
	 *								  	'System Group: Coworkers' => string 'http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/f' (length=67)
	 *								  	'Starred in Android' => string 'http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/d2ad5ab8a27af3f' (length=81)
	 *								  	'Dolibarr (Thirdparties)' => string 'http://www.google.com/m8/feeds/groups/testldrdev%40gmail.com/base/3e8259858aa42968' (length=82)
	 *									);
	 */
	public static function getGoogleGroupID($gdata, $groupName, $useremail)
	{
		global $conf;
		static $googleGroups;

		$error = 0;

		// Search existing groups
		if (!isset($googleGroups)) {
			$document = new DOMDocument("1.0", "utf-8");
			$xmlStr = getContactGroupsXml($gdata);

			if ($xmlStr) {
				try {
					$document->loadXML($xmlStr);
					$xmlStr = $document->saveXML();

					$entries = $document->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "entry");
					$n = $entries->length;
					$googleGroups = array();
					foreach ($entries as $entry) {
						$titleNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "title");
						if ($titleNodes->length == 1) {
							$title = $titleNodes->item(0)->textContent;
							$googleIDNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "id");
							if ($googleIDNodes->length == 1) {
								$googleGroups[$title] = $googleIDNodes->item(0)->textContent;
							}
						}
					}
				} catch(Exception $e) {
					print 'Error when getting groups';
					exit;
				}
			} else {
				$error++;
			}
		}

		// Create group if it does not exists
		if (!$error && !isset($googleGroups[$groupName])) {
			try {
				$newGroupID = self::insertGContactGroup($gdata, $groupName, $useremail);
			} catch(Exception $e) {
				dol_syslog("Error in getGoogleGroupID", LOG_WARNING);
			}
			$googleGroups[$groupName] = $newGroupID;
		}

		return $googleGroups[$groupName];
	}


	/**
	 * Insert contacts into a google account
	 *
	 * @param	Mixed	$gdata			GData handler
	 * @param 	array 	$gContacts		Array of GContact objects
	 * @return	int						>0 if OK
	 */
	public static function insertGContactsEntries($gdata, array $gContacts)
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
			$feed->appendChild($doc->createElement("title", "Dolibarr mass insert into Google contacts"));
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
			 * https://www.google.com/m8/feeds/contacts/default/base/...
			 * or
			 * https://www.google.com/m8/feeds/contacts/default/full/...
			 * Some Ids retrieved when accessing base may not be used with full and vice versa
			 * When using base, you may not change the group membership
			 */
			try {
				$response = $gdata->post($xmlStr, "https://www.google.com/m8/feeds/contacts/default/full/batch");
				$responseXml = $response->getBody();
				// uncomment for debugging :
				file_put_contents(DOL_DATA_ROOT . "/gcontacts/temp/gmail.response.xml", $responseXml);
				// you can view this with 'xmlstarlet fo gmail.response.xml' command
				$res=self::parseResponse($responseXml);
				if ($res->count != count($firstContacts) || $res->errors) print sprintf("Google error : %s", $res->lastError);

				dol_syslog(sprintf("Inserting %d google contacts", count($firstContacts)));
			} catch (Exception $e) {
				dol_syslog("Problem while inserting contact", LOG_ERR);
				throw new Exception($e->getMessage());
			}
		}

		return 1;
	}

	private static function parseResponse($xmlStr)
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
			if ($title->length==1 && $title->item(0)->textContent=='Error') {
				$res->errors++;
				$content = $node->getElementsByTagName("content");
				if ($content->length>0)
					$res->lastError=$content->item(0)->textContent;
			}
		}
		return $res;
	}


	 /**
	  * Delete Google Contacts or Groups on Gmail account
	  *
	  * @param	Gdata	$gdata			Gdata handler
	  * @param 	array	$googleIDs		Array of Google id to delete
	  * @param 	boolean $groupFlag		Method of deletion (false=batch mode)
	  * @return 	void
	  */
	public static function deleteEntries($gdata, array $googleIDs, $groupFlag)
	{
		global $conf, $tag_debug;

		if ($groupFlag) {
			// Due to a bug in zend not correctly taking into account headers (in particular If-Match), we do the request by hand (performHttpRequest instead of using the $gdata->delete)
			$addheaders = array();
			$addheaders['If-Match'] = '*';
			foreach ($googleIDs as $googleID) {
				try {
					dol_syslog("Deleting contact or group ".$googleID." with mode no batch");
					$requestData = $gdata->prepareRequest('DELETE', $googleID, $addheaders);
					$response = $gdata->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null/* remainingRedirects */);
					//$gdata->delete($googleID);
				} catch (Exception $e) {
					dol_syslog("Problem while deleting one entry $googleID", LOG_ERR);
					throw new Exception(sprintf("Problem while deleting one entry (%s) : %s", $googleID, $e->getMessage()));
				}
			}
		} else {
			$maxBatchLength = 98; //Google doc says max 100 entries.
			$remainingIDs = $googleIDs;
			while (count($remainingIDs) > 0) {
				if (count($remainingIDs) > $maxBatchLength) {
					$firstIDs = array_slice($remainingIDs, 0, $maxBatchLength);
					$remainingIDs = array_slice($remainingIDs, $maxBatchLength);
				} else {
					$firstIDs = $remainingIDs;
					$remainingIDs = array();
				}
				/*$doc = new DOMDocument("1.0", "utf-8");
				$doc->formatOutput = true;
				$feed = $doc->createElement("atom:feed");
				$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
				$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
				$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');
				$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:batch', 'http://schemas.google.com/gdata/batch');
				$feed->appendChild($doc->createElement("title", "The batch title: delete"));
				$doc->appendChild($feed);*/
				$jsonData = '{"resourceNames" : [';
				$nbentries = 1;
				foreach ($firstIDs as $googleID) {
					//$googleID = preg_replace('/http:\/\//','https://',$googleID);	// Force https

					/*$entry = $doc->createElement("atom:entry");
					$entry->setAttribute("gdata:etag", "*");
					$entry->appendChild($doc->createElement("atom:id", $googleID));
					$el = $doc->createElement("batch:operation");
					$el->setAttribute("type", "delete");
					$entry->appendChild($el);
					$feed->appendChild($entry);*/
					if ($nbentries > 1) {
						$jsonData .= ",";
					}
					$jsonData .= json_encode($googleID);
					$nbentries ++;
				}
				$jsonData .= "]}";
				//$xmlStr = $doc->saveXML();

				dol_syslog(sprintf("Deleting %d google contacts for user %s", count($firstIDs), !empty($googleUser)?$googleUser:""));
				try {
					$tag_debug='massdelete';
					//file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massdelete.xml", $xmlStr);
					//@chmod(DOL_DATA_ROOT . "/dolibarr_google_massdelete.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));

					if (is_array($gdata['google_web_token']) && key_exists('access_token', $gdata['google_web_token'])) {
						$access_token=$gdata['google_web_token']['access_token'];
					} else {
						$tmp=json_decode($gdata['google_web_token']);
						$access_token=$tmp->access_token;
					}
					$addheaders=array('authorization'=>'Bearer '.$access_token, 'If-Match'=>'*');
					$addheaderscurl=array('Content-Type: application/json','authorization: Bearer '.$access_token, 'If-Match: *');

					//$request=new Google_Http_Request('https://www.google.com/m8/feeds/contacts/default/base/batch', 'POST', $addheaders, $xmlStr);
					//$requestData = $gdata['client']->execute($request);
					$result = getURLContent('https://people.googleapis.com/v1/people:batchDeleteContacts', 'POST', $jsonData, 0, $addheaderscurl);
					$jsonStr=$result['content'];
					try {
						$json = json_decode($jsonStr);
						/*$document = new DOMDocument("1.0", "utf-8");
						$document->loadXml($result['content']);

						$errorselem = $document->getElementsByTagName("errors");*/
						//var_dump($errorselem);
						//var_dump($errorselem->length);
						//var_dump(count($errorselem));
						if (!empty($json->error)) {
							dol_syslog('ERROR:'.$result['content'], LOG_ERR);
							return -1;
						}
					} catch (Exception $e) {
						dol_syslog('ERROR:'.$e->getMessage(), LOG_ERR);
						return -1;
					}

					//file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massdelete.response.xml", $responseXml);
					//@chmod(DOL_DATA_ROOT . "/dolibarr_google_massdelete.response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
				} catch (Exception $e) {
					dol_syslog("Problem while deleting contacts", LOG_ERR);
					throw new Exception(sprintf("Problem while deleting contacts : %s", $e->getMessage()));
				}
			}
		}
	}

	 /**
	  * Delete dollibar groups on Gmail account : All groups beginning with 'Dolibarr'
	  *
	  * @param	Gdata	$gdata		Gdata handler
	  * @return 	int 				count of contacts deleted
	  */
	public static function deleteDolibarrContactGroups($gdata)
	{
		global $conf;

		// Get list of groups
		$document = new DOMDocument("1.0", "utf-8");
		$xmlStr = getContactGroupsXml($gdata);

		$document->loadXML($xmlStr);
		$xmlStr = $document->saveXML();

		// Search into groups
		// TODO
		/*
		 $entries = $document->documentElement->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "entry");
		$n = $entries->length;
		$googleIDs = array();
		$groupPrefix=$conf->global->GCONTACTS_GROUP_PREFIX;
		foreach ($entries as $entry) {
		$titleNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "title");
		if ($titleNodes->length == 1) {
		$title = $titleNodes->item(0)->textContent;
		$a = $groupPrefix.'/';
		$b = strlen($groupPrefix.'/');
		if ($title==$groupPrefix || (strncasecmp($title, $groupPrefix.'/', strlen($groupPrefix.'/'))==0)) {
		$googleIDNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "id");
		if ($googleIDNodes->length == 1) {
		$googleIDs[] = $googleIDNodes->item(0)->textContent;
		}
		}
		}
		}
		self::deleteEntries($googleIDs, true);
		*/
		return(count($googleIDs));
	}
}



/**
 * GCaddr
 */
class GCaddr
{
	var $street;
	var $zip;
	var $town;
	var $state;
	var $country;
	var $country_id;
	var $state_id;

	/**
	 *	Fill country and state id from labels
	 *
	 * 	@return	void
	 */
	function fillIDs()
	{
		$this->guessCountryID();
		$this->guessStateID();
	}

	 /**
	  * Do our best to retreive dolibarr country_id from the country label.
	  * knowing that labels from google are free and traduction problem could arise...
	  *
	  * @return	string	Country id
	  */
	private function guessCountryID()
	{
		if (empty($this->country)) return;
		global $db,$langs;
		$langs->load("dict");

		$countrytable="c_pays";
		include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
		if (versioncompare(versiondolibarrarray(), array(3,7,-3)) >= 0) {
			$countrytable="c_country";
		}

		$sql = "SELECT rowid, code as code_iso, label";
		$sql.= " FROM ".MAIN_DB_PREFIX.$countrytable;
		$sql.= " WHERE active = 1";
		$resql=$db->query($sql);
		if (!$resql) throw new Exception($db->lasterror());
		while ($obj=$db->fetch_object($resql)) {
			$dbLabel = $langs->transnoentitiesnoconv("Country".$obj->code_iso);
			if ($dbLabel == $this->country)
				$this->country_id = $obj->rowid;
		}
	}

	/**
	 * Try to return the dolibarr StateID given a dolibarr countryID and a stateLabel
	 *
	 * @return	int		State id
	 */
	private function guessStateID()
	{
		if (empty($this->state) || empty($this->country_id)) return;
		global $db,$langs;
		$langs->load("dict");

		$sql = "SELECT d.rowid, d.code_departement as stateCode , d.nom as stateLabel, p.rowid as countryID FROM";
		$sql .= " ".MAIN_DB_PREFIX ."c_departements as d, ".MAIN_DB_PREFIX."c_regions as r,".MAIN_DB_PREFIX.$countrytable." as p";
		$sql .= " WHERE d.fk_region=r.code_region and r.fk_pays=p.rowid";
		$sql .= " AND d.active = 1 AND r.active = 1 AND p.active = 1";
		$sql .= " AND p.rowid = '".$this->country_id."'";

		$resql=$db->query($sql);
		if (!$resql) throw new Exception($db->lasterror());
		while ($obj=$db->fetch_object($resql)) {
			$dbLabel = $obj->stateLabel;
			if ($langs->transnoentitiesnoconv($obj->stateCode) != $obj->stateCode)
				$dbLabel = $langs->transnoentitiesnoconv($obj->stateCode); // If a translation exists, get it.
			if ($dbLabel == $this->state)
				$this->state_id=$obj->rowid;
		}
	}
}
