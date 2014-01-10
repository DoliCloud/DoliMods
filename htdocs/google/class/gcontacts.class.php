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
$path = dol_buildpath('/google/includes/zendgdata');
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Http_Client');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');


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
        if($dolID) {
            $this->from='dolibarr';
            $this->dolID = $dolID;
            if ($type == 'thirdparty') $this->fetchThirdpartyFromDolibarr($gdata);
            elseif ($type == 'contact') $this->fetchContactFromDolibarr($gdata);
            elseif ($type == 'member') $this->fetchMemberFromDolibarr($gdata);
            else dol_print_error('','Bad value for type');
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
    private function appendCustomField($key, $value) {
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
    private function appendEmail($rel, $email, $isPrimary, $label=null) {
        if(empty($email)) return;
        $el = $this->doc->createElement('gdata:email');
        if($label) {
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
    private function appendTextElement(DOMElement $el, $elName, $text) {
        if(empty($text)) return;
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
    private function appendPostalAddress($rel, GCaddr $addr=null,$label=null) {
        if(empty($addr)) return;
        $el = $this->doc->createElement("gdata:structuredPostalAddress");
        if($label) {
            $el->setAttribute('label', $label);
        } else {
            $el->setAttribute('rel', $rel);
        }
        self::appendTextElement($el, "gdata:street", $addr->street);
        self::appendTextElement($el, "gdata:postcode", $addr->zip);
        self::appendTextElement($el, "gdata:city", $addr->town);
        self::appendTextElement($el, "gdata:region", $addr->state);
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
    private function appendPhoneNumber($rel, $phoneNumber, $isPrimary, $label=null) {
        if(empty($phoneNumber)) return;
        $el = $this->doc->createElement('gdata:phoneNumber');
        if($label) {
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
    private function appendWebSite($href) {
        if(empty($href)) return;
        $el = $this->doc->createElement('gcontact:website');
        $el->setAttribute("label","URL");
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
    private function appendInstantMessaging($label, $im, $protocol) {
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
    private function appendRelation($label, $value) {
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
     * @return	void
     */
    private function appendGroup($gdata, $groupName)
    {
        $el = $this->doc->createElement("gcontact:groupMembershipInfo");
        $el->setAttribute("deleted", "false");
        $el->setAttribute("href", self::getGoogleGroupID($gdata,$groupName));
        $this->atomEntry->appendChild($el);
    }

    /**
     * Fill the GContact class from a dolibarID
     *
     * @param	Gdata		$gdata		Gdata handler
     * @return 	GContact
     */
    private function fetchThirdpartyFromDolibarr($gdata)
    {
    	global $conf,$langs;

        if($this->dolID==null) throw new Exception('Internal error: dolID is null');
        global $db, $langs, $conf;
        require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
        require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

        $dolContact = new Societe($db);
        $result=$dolContact->fetch($this->dolID);
        if($result==0)
            throw new Exception('Internal error: Thirdparty not found');
        if($result==0)
            throw new Exception($dolContact->$error);

        // Fill object with thirdparty infos
        $this->firstname = $dolContact->firstname;
        $this->lastname = $dolContact->lastname;
        $this->name = $dolContact->name;
        $this->fullName = $dolContact->getFullName($langs);
        $this->email = $dolContact->email?$dolContact->email:($this->fullName.'@noemail.com');
        if(!(empty($dolContact->address)&&empty($dolContact->zip)&&empty($dolContact->town)&&empty($dolContact->state)&&empty($dolContact->country))) {
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
        $this->socid= $dolContact->socid;

        $google_nltechno_tag=getCommentIDTag();
        $idindolibarr=$this->dolID."/thirdparty";

        $this->note_private = $dolContact->note_private;
        if (strpos($this->note_private,$google_nltechno_tag) === false) $this->note_private .= "\n\n".$google_nltechno_tag.$idindolibarr;

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

        $elfullName = $this->doc->createElement('gdata:fullName', $this->fullName);
        $el->appendChild($elfullName);

        // Note as comment and a custom field
        $this->atomEntry->appendChild($this->doc->createElement('atom:content', google_html_convert_entities($this->note_private)));
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
                $norm_phone_pro = preg_replace("/\s/","",$this->phone_pro);
                $norm_phone_pro = preg_replace("/\./","",$norm_phone_pro);
                $norm_phone_perso = preg_replace("/\s/","",$this->phone_perso);
                $norm_phone_perso = preg_replace("/\./","",$norm_phone_perso);
                if ($norm_phone_pro != $this->company->phone && $norm_phone_perso != $this->company->phone)
                    $this->appendPhoneNumber(null, $this->company->phone,false, $this->orgName);
                $norm_fax = preg_replace("/\s/","",$this->fax);
                $norm_fax = preg_replace("/\./","",$norm_fax);
                if ($norm_fax != $this->company->fax)
                    $this->appendPhoneNumber(null, $this->company->fax, false, 'Fax '.$this->orgName);
                if ($this->addr != $this->company->addr)
                    $this->appendPostalAddress(null /*rel*/, $this->company->addr, $this->orgName);
                if ($this->company->email != $this->email)
                    $this->appendEmail(self::REL_WORK, $this->company->email, false, $this->orgName);
        }

        $userdefined = $this->doc->createElement('gcontact:userDefinedField');
        $userdefined->setAttribute('key','dolibarr-id');
        $userdefined->setAttribute('value',$idindolibarr);
        $this->atomEntry->appendChild($userdefined);

		// Add tags
        $this->appendGroup($gdata, getTagLabel('thirdparties'));
        $this->doc->appendChild($this->atomEntry);
    }

    /**
     * Fill GContact instance for this->dolID.
     * Note: It creates groups if it not exists.
     *
     * @param	GData	$gdata		GData
     * @return 	GContact
     */
    private function fetchContactFromDolibarr($gdata)
    {
    	global $conf,$langs;

    	if($this->dolID==null) throw new Exception('Internal error: dolID is null');
    	global $db, $langs, $conf;
    	require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
    	require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");

    	$dolContact = new Contact($db);
    	$result=$dolContact->fetch($this->dolID);
    	if($result==0)
    		throw new Exception('Internal error: Contact not found');
    	if($result==0)
    		throw new Exception($dolContact->$error);

    	// Fill object with contact infos
    	$this->firstname = $dolContact->firstname;
    	$this->lastname = $dolContact->lastname;
        $this->fullName = $dolContact->getFullName($langs);
    	$this->email = ($dolContact->email?$dolContact->email:($this->fullName.'@noemail.com'));
    	if(!(empty($dolContact->address)&&empty($dolContact->zip)&&empty($dolContact->town)&&empty($dolContact->state)&&empty($dolContact->country)))
    	{
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
    	$this->socid= $dolContact->socid;
    	if ($dolContact->socid)
    	{
    		$company = new Societe($db);
    		$result=$company->fetch($dolContact->socid);
    		if ($result <=0) throw new Exception($company->$error);
    		$this->orgName=$company->name;
    	}
    	$this->poste= $dolContact->poste;

    	$google_nltechno_tag=getCommentIDTag();
    	$idindolibarr=$this->dolID."/contact";

        $this->note_private = $dolContact->note_private;
    	if (strpos($this->note_private,$google_nltechno_tag) === false) $this->note_private .= "\n\n".$google_nltechno_tag.$idindolibarr;

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
    	if (! empty($this->orgName) && ! empty($this->poste))
    	{
	    	$elorg = $this->doc->createElement('gdata:organization');
	    	$elorg->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
	    	if (! empty($this->orgName)) $this->appendTextElement($elorg, 'gdata:orgName', $this->orgName);
	    	if (! empty($this->poste))   $this->appendTextElement($elorg, 'gdata:orgTitle', $this->poste);
	    	$this->atomEntry->appendChild($elorg);
    	}

        // Note as comment and a custom field
    	$this->atomEntry->appendChild($this->doc->createElement('atom:content', $this->note_private));
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
    	$userdefined->setAttribute('key','dolibarr-id');
    	$userdefined->setAttribute('value',$idindolibarr);
    	$this->atomEntry->appendChild($userdefined);

    	// Add tags
        $this->appendGroup($gdata, getTagLabel('contacts'));
    	$this->doc->appendChild($this->atomEntry);
    }

    /**
     * Fill GContact instance for this->dolID.
     * Note: It creates groups if it not exists.
     *
     * @param	GData	$gdata		GData
     * @return 	GContact
     */
    private function fetchMemberFromDolibarr($gdata)
    {
    	global $conf,$langs;

    	if($this->dolID==null) throw new Exception('Internal error: dolID is null');
    	global $db, $langs, $conf;
    	require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");

    	$dolContact = new Adherent($db);
    	$result=$dolContact->fetch($this->dolID);
    	if($result==0)
    		throw new Exception('Internal error: Contact not found');
    	if($result==0)
    		throw new Exception($dolContact->$error);

    	// Fill object with contact infos
    	$this->firstname = $dolContact->firstname;
    	$this->lastname = $dolContact->lastname;
    	$this->fullName = $dolContact->getFullName($langs);
    	if (empty($this->fullName)) $this->fullName=$dolContact->company;
    	$this->email = ($dolContact->email?$dolContact->email:($this->fullName.'@noemail.com'));
    	if(!(empty($dolContact->address)&&empty($dolContact->zip)&&empty($dolContact->town)&&empty($dolContact->state)&&empty($dolContact->country)))
    	{
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

        $this->note_private = $dolContact->note_private;
    	if (strpos($this->note_private,$google_nltechno_tag) === false) $this->note_private .= "\n\n".$google_nltechno_tag.$idindolibarr;

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
    	if (! empty($this->orgName))
    	{
	    	$elorg = $this->doc->createElement('gdata:organization');
	    	$elorg->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
	    	if (! empty($this->orgName)) $this->appendTextElement($elorg, 'gdata:orgName', $this->orgName);
	    	$this->atomEntry->appendChild($elorg);
    	}

    	// Note as comment and a custom field
    	$this->atomEntry->appendChild($this->doc->createElement('atom:content', $this->note_private));
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
    	$userdefined->setAttribute('key','dolibarr-id');
    	$userdefined->setAttribute('value',$idindolibarr);
    	$this->atomEntry->appendChild($userdefined);

    	// Add tags
    	$this->appendGroup($gdata, getTagLabel('members'));
    	$this->doc->appendChild($this->atomEntry);
    }

    /**
     * Get list of googleContactsIDs matching the given pattern from Google contact
     *
     * @param	Gdata	$gdata		Gdata handler
     * @param 	string 	$pattern	Pattern to filter query
     * @param	string	$type		'thirdparty' or 'contact'
     * @return 	string 				array of google contactsID
     */
    public static function getDolibarrContactsGoogleIDS($gdata, $pattern, $type)
    {
        dol_syslog(get_class().'::getDolibarrContactsGoogleIDS');

        if (empty($type)) return array();

        $document = new DOMDocument("1.0", "utf-8");

		// Get full list of contacts
    	$queryString = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=1000';
        if (! empty($pattern)) $queryString .= '&q='.$pattern;
        $query = new Zend_Gdata_Query($queryString);
        $feed = $gdata->getFeed($query);
        $xmlStr = $feed->getXML();

        // Split answers into entries array
        $document->loadXML($xmlStr);
        $entries = $document->documentElement->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "entry");

        $n = $entries->length;
        dol_syslog(get_class().'::getDolibarrContactsGoogleIDS '.$n.' contacts retrieved from google contacts');

        $tagtofind=getCommentIDTag();
        $googleIDs = array();	// Nothing to delete by default
        foreach ($entries as $entry) {
        	// Try to qualify or not contact
        	// TODO Use the dolibarr-id instead of comment
        	$contentNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");	//<atom:content type="text"> = note
            if ($contentNodes->length == 1) {
                $content = $contentNodes->item(0)->textContent;
				//print $content."<br>";

                // Detect if contact is qualified to be deleted
                if (preg_match('/'.preg_quote($tagtofind).'([0-9]+)\/'.$type.'/m', $content, $reg))
                {
                    $googleIDNodes = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "id");
                    //$googleEMail = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");
					//var_dump($googleEMail->item(0)->nodeValue);
                    //$googleEMail = $entry->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");
                    //var_dump($googleEMail->item(0)->nodeValue);
                    if ($googleIDNodes->length == 1) {
                        $googleIDs[] = $googleIDNodes->item(0)->textContent;
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

        self::deleteEntries($gdata, $googleIDs, false);
        return(count($googleIDs));
    }



    /**
	 * insertGContactGroup
	 *
     * @param	Gdata	$gdata			Gdata handler
     * @param 	string 	$groupName		Name of group to create
     * @return 	string					googlegroupID
     */
    private static function insertGContactGroup($gdata, $groupName)
    {
    	dol_syslog("insertGContactGroup Create Google group ".$groupName);

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
            $entryResult = $gdata->insertEntry($xmlStr, 'https://www.google.com/m8/feeds/groups/default/full');
            dol_syslog(sprintf("Inserting gContact group %s in google contacts for user %s google ID = %s", $groupName, $googleUser, $entryResult->id));
        } catch (Exception $e) {
            dol_syslog("Problem while inserting group", LOG_ERR);
            throw new Exception(sprintf("Problem while inserting group %s : %s", $groupName, $e->getMessage()));
        }
        return($entryResult->id);
    }

    /**
     * Retreive a googleGroupID given a groupName.
     * If the groupName does not exist on Gmail account, it will be created as a side effect
     *
     * @param	Gdata	$gdata			Gdata handler
     * @param	string	$groupName		Name of group
     * @return 	array					Array of googleGroupID.
     */
    public static function getGoogleGroupID($gdata,$groupName)
    {
    	global $conf;
    	static $googleGroups;

    	// Search existing groups
    	if(!isset($googleGroups))
    	{
    		$document = new DOMDocument("1.0", "utf-8");
    		$xmlStr = self::getContactGroupsXml($gdata);
    		$document->loadXML($xmlStr);
    		$xmlStr = $document->saveXML();
    		$entries = $document->documentElement->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "entry");
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
    	}

    	// Create group if it not exists
    	if(!isset($googleGroups[$groupName])) {
    		$newGroupID = self::insertGContactGroup($gdata, $groupName);
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
               if($res->count != count($firstContacts) || $res->errors) print sprintf("Google error : %s", $res->lastError);

               dol_syslog(sprintf("Inserting %d google contacts", count($firstContacts)));
            } catch (Exception $e) {
                dol_syslog("Problem while inserting contact", LOG_ERR);
                throw new Exception($e->getMessage());
            }

        }

        return 1;
    }

    private static function parseResponse($xmlStr) {
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
     * Get an array of GContacts from a google account ;
     * query Arg could be a specific query i.e : q=OnelogMarker:123#' to get the contact 123
     *          or for an entire googleGroupID, the googlegroupID itself.
     *   Usefull to retreive all contacts in 'My contacts' system group one one google call.
     * @param string $queryArg
     * @return array GContacts
     */
    /*
     public static function getContactsGoogleDetails($queryArg='', $discardDolcontacts=false) {
        global $db;
        $queryString = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=1000';
        if(!empty($queryArg))
            $queryString .= '&'.$queryArg;   //Restrict the search to i.e OnelogMarker or a group
        $gdata=self::googleDataConnection();
        $query = new Zend_Gdata_Query($queryString);
        $feed = $gdata->getFeed($query);
        $entries = $gdata->retrieveAllEntriesForFeed($feed);
        $contactsLst = array();
        foreach ($entries as $entry) {
            $contact = new GContact();
            $doc = new DOMDocument("1.0", "utf-8");
            $doc->loadXML($entry->getXML());
            $contentNodes = $doc->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "content");
            if ($contentNodes->length == 1)
                $contact->note = $contentNodes->item(0)->textContent;
            if ($discardDolcontacts && strpos($contact->note, self::MARKER_FOR_DELETE) !== false) {
                continue; // Skip contacts marked as dolibar
            }
            $googleIDNodes = $doc->getElementsByTagNameNS(self::ATOM_NAME_SPACE, "id");
            if ($googleIDNodes->length == 1)
                $contact->googleID = $googleIDNodes->item(0)->textContent;
            $extensions = $entry->getExtensionElements();
            $unkElems = array(); // Usefull to debug non detected values
            foreach ($extensions as $extension) {
                switch ($extension->rootElement) {
                    case 'edited':
                        $contact->lastMod = $extension->text;
                        break;
                    case 'name':
                        foreach ($extension->getExtensionElements() as $elem) {
                            switch ($elem->rootElement) {
                                case 'givenName':
                                    $contact->firstname = $elem->text;
                                    break;
                                case 'familyName':
                                    $contact->lastname = $elem->text;
                                    break;
                                case 'fullName':
                                    $contact->fullname = $elem->text; // Unused for now
                                    break;
                                default:
                                    $unkElems['name:'.$elem->rootElement] = $elem->text;
                                    break;
                            }
                        }
                        break;
                    case 'organization':
                        foreach ($extension->getExtensionElements() as $elem) {
                            switch ($elem->rootElement) {
                                case 'orgName':
                                    $contact->orgName = $elem->text;
                                    break;
                                case 'orgTitle':
                                    $contact->poste = $elem->text;
                                    break;
                                default:
                                    $unkElems['organization:'.$elem->rootElement] = $elem->text;
                                    break;
                            }
                        }
                        break;
                    case 'phoneNumber':
                        $attributes = $extension->getExtensionAttributes();
                        if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_WORK)
                            $contact->phone_pro = $extension->text;
                        else if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_HOME)
                            $contact->phone_perso = $extension->text;
                        else if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_MOBILE)
                            $contact->phone_mobile = $extension->text;
                        else if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_WORK_FAX)
                            $contact->fax = $extension->text;
                        else if($attributes['label']['name']=='label' && $attributes['label']['value']==$contact->orgName) //On my experience, Org Extension is always before mail, phone addr.. So, normally $contact->orgName will be set when here
                            $contact->phone_office = $extension->text;
                        else if($attributes['label']['name']=='label' && $attributes['label']['value']=='Fax '.$contact->orgName)
                            $contact->fax_office = $extension->text;
                        else if(empty($contact->phone_other))
                            $contact->phone_other = $extension->text;
                        break;
                    case 'structuredPostalAddress':
                        $attributes = $extension->getExtensionAttributes();
                        $addr = new GCaddr();
                        foreach ($extension->getExtensionElements() as $elem) {
                            switch ($elem->rootElement) {
                                case 'formattedAddress':
                                    // Not sure if this is usefull on some case... Maybe we have to set $addr->street to this if all others are empty...
                                    break;
                                case 'street':
                                    $addr->street = $elem->text;
                                    break;
                                case 'postcode':
                                    $addr->zip = $elem->text;
                                    break;
                                case 'city':
                                    $addr->town = $elem->text;
                                    break;
                                case 'country':
                                    $addr->country = $elem->text;
                                    break;
                                case 'region':
                                    $addr->state = $elem->text;
                                    break;
                                default:
                                    $unkElems['addr:'.$elem->rootElement] = $elem->text;
                                    break;
                            }
                        }
                        if(!empty($addr->country))
                            $addr->fillIDs();
                        if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_WORK) {
                            $contact->addr = $addr;
                        } else if($attributes['label']['name']=='label' && $attributes['label']['value']==$contact->orgName) {
                            $contact->company->addr = $addr;
                        } else {
                            if(empty($contact->other_addr)) $contact->other_addr = $addr;
                        }
                        break;
                    case 'userDefinedField':
                        $attributes = $extension->getExtensionAttributes();
                        $key = $attributes['key']['value'];
                        $val = $attributes['value']['value'];
                        $unkElems['userDefinedField:'.$key] = $val;
                        break;
                    case 'groupMembershipInfo':
                        // Don't know what to do with this now... But could be interresting to match with dolibarr categories or contact type
                        break;
                    case 'email':
                        $attributes = $extension->getExtensionAttributes();
                        if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_WORK)
                            $contact->email = $attributes['address']['value'];
                        else if($attributes['label']['name']=='label' && $attributes['label']['value']==$contact->orgName)
                            $contact->email_office = $attributes['address']['value'];
                        else if($attributes['rel']['name']=='rel' && $attributes['rel']['value']==self::REL_HOME)
                            $contact->email_perso = $attributes['address']['value'];
                        else if(empty($contact->email_other))
                            $contact->email_other = $attributes['address']['value'];
                        break;
                    case 'im':
                        $attributes = $extension->getExtensionAttributes();
                        // Boarf... not sure it's interresting to struggle for that...
                        //$contact->jabber = $attributes['address']['value'];
                        break;
                    default:
                        $unkElems['email:'.$extension->rootElement]=0; //breakpoint here to see what we lose
                        break;
                        // Do not take care of anything else...;
                }
            }
            // On debug, dump/view $unkElems here
            $contactsLst[]=$contact;
            if(count($contactsLst)>self::MAX_RETREIVE) {
                throw new Exception('GContactsMaxRetreiveReach');
            }
        }
        return $contactsLst;
     }
     */


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
     	global $conf;

     	if ($groupFlag)
     	{
     		$headers = array();
     		//Due to a bug in zend not correctly taking into account headers (in particular If-Match), we do the request by hand.
     		//instead of using the $gdata->delete
     		$headers['If-Match'] = '*';
     		foreach ($googleIDs as $googleID) {
     			try {
     				$requestData = $gdata->prepareRequest('DELETE', $googleID, $headers);
     				$gdata->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null/* remainingRedirects */);
     				dol_syslog("Deleting contact or group $googleID for user $googleUser");
     				//$gdata->delete($googleID);
     			}  catch (Exception $e) {
     				dol_syslog("Problem while deleting one entry $googleID", LOG_ERR);
     				throw new Exception(sprintf("Problem while deleting one entry (%s) : %s", $googleID, $e->getMessage()));
     			}
     		}
     	}
     	else
     	{
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
     			$doc = new DOMDocument("1.0", "utf-8");
     			$doc->formatOutput = true;
     			$feed = $doc->createElement("atom:feed");
     			$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
     			$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gdata', 'http://schemas.google.com/g/2005');
     			$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gcontact', 'http://schemas.google.com/contact/2008');
     			$feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:batch', 'http://schemas.google.com/gdata/batch');
     			$feed->appendChild($doc->createElement("title", "The batch title: delete"));
     			$doc->appendChild($feed);
     			foreach ($firstIDs as $googleID) {
     				$entry = $doc->createElement("atom:entry");
     				$entry->setAttribute("gdata:etag", "*");
     				$entry->appendChild($doc->createElement("atom:id", $googleID));
     				$el = $doc->createElement("batch:operation");
     				$el->setAttribute("type", "delete");
     				$entry->appendChild($el);
     				$feed->appendChild($entry);
     			}
     			$xmlStr = $doc->saveXML();
     			dol_syslog(sprintf("Deleting %d google contacts for user %s", count($firstIDs), $googleUser));
     			try {
     				// uncomment for debugging :
     				file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massdelete.xml", $xmlStr);
     				@chmod(DOL_DATA_ROOT . "/dolibarr_google_massdelete.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
     				$response = $gdata->post($xmlStr, "https://www.google.com/m8/feeds/contacts/default/base/batch");
     				$responseXml = $response->getBody();
     				file_put_contents(DOL_DATA_ROOT . "/dolibarr_google_massdelete.response.xml", $xmlStr);
     				@chmod(DOL_DATA_ROOT . "/dolibarr_google_massdelete.response.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
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
     	$xmlStr = self::getContactGroupsXml($gdata);

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


    /**
     * Retreive a Xml feed of contactsGroups from Google
     *
     * @param	Gdata	$gdata		Gdata handler
     * @return	string				Xml string with list of groups
     */
    private static function getContactGroupsXml($gdata)
    {
        try {
        	// Get list of groups
            $query = new Zend_Gdata_Query('https://www.google.com/m8/feeds/groups/default/full?max-results=1000');
            $feed = $gdata->getFeed($query);
            $xmlStr = $feed->getXML();
            // uncomment for debugging :
            // file_put_contents(DOL_DATA_ROOT . "/gcontacts/temp/gmail.groups.xml", $xmlStr);
            // dump it with 'xmlstarlet fo gmail.groups.xml' command
        } catch (Exception $e) {
            dol_syslog("Error while feed xml groups", LOG_ERR);
            throw new Exception(sprintf("Error while feed xml groups : %s", $e->getMessage()));
        }
        return($xmlStr);
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

        $sql = "SELECT rowid, code as code_iso, libelle as label";
        $sql.= " FROM ".MAIN_DB_PREFIX."c_pays";
        $sql.= " WHERE active = 1";
        $resql=$db->query($sql);
        if (!$resql) throw new Exception($db->lasterror());
        while ($obj=$db->fetch_object($resql))
        {
            $dbLabel = $langs->transnoentitiesnoconv("Country".$obj->code_iso);
            if($dbLabel == $this->country)
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
        $sql .= " ".MAIN_DB_PREFIX ."c_departements as d, ".MAIN_DB_PREFIX."c_regions as r,".MAIN_DB_PREFIX."c_pays as p";
        $sql .= " WHERE d.fk_region=r.code_region and r.fk_pays=p.rowid";
        $sql .= " AND d.active = 1 AND r.active = 1 AND p.active = 1";
        $sql .= " AND p.rowid = '".$this->country_id."'";

        $resql=$db->query($sql);
        if (!$resql) throw new Exception($db->lasterror());
        while ($obj=$db->fetch_object($resql))
        {
            $dbLabel = $obj->stateLabel;
            if($langs->trans($obj->stateCode) != $obj->stateCode)
                $dbLabel = $langs->trans($obj->stateCode); // If a translation exists, get it.
            if($dbLabel == $this->state)
                $this->state_id=$obj->rowid;
        }
    }
}
