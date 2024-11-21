<?php
/* Copyright (C) 2011 Regis Houssin	            <regis@dolibarr.fr>
 * Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *      \file       /google/core/triggers/interface_51_modGoogle_GoogleContactSynchro.class.php
 *      \ingroup    google
 *      \brief      File to manage triggers for Google contact sync
 */

include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
dol_include_once('/google/lib/google_contact.lib.php');


/**
 *	Class of triggers for module Google
 */
class InterfaceGoogleContactSynchro extends DolibarrTriggers
{
	var $db;
	var $error;

	var $date;
	var $duree;
	var $texte;
	var $desc;

	/**
	 *   Constructor.
	 *
	 *   @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "google";
		$this->description = "Triggers of this module allows to add a record inside Google contact for each Dolibarr business event.";
		$this->picto = 'google@google';
	}

	/**
	 *   Renvoi nom du lot de triggers
	 *
	 *   @return     string      Nom du lot de triggers
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 *   Renvoi descriptif du lot de triggers
	 *
	 *   @return     string      Descriptif du lot de triggers
	 */
	function getDesc()
	{
		return $this->description;
	}

	/**
	 *   Renvoi version du lot de triggers
	 *
	 *   @return     string      Version du lot de triggers
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'experimental') return $langs->trans("Experimental");
		elseif ($this->version == 'dolibarr') return DOL_VERSION;
		elseif ($this->version) return $this->version;
		else return $langs->trans("Unknown");
	}

	/**
	 *      Fonction appelee lors du declenchement d'un evenement Dolibarr.
	 *      D'autres fonctions runTrigger peuvent etre presentes dans includes/triggers
	 *
	 *      @param	string		$action     Code of event
	 *      @param 	Object		$object     Objet concerne
	 *      @param  User		$user       Objet user
	 *      @param  Translate	$langs      Objet lang
	 *      @param  Conf		$conf       Objet conf
	 *      @return int         			<0 if KO, 0 if nothing is done, >0 if OK
	 */
	function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		global $dolibarr_main_url_root;

		// Création / Mise à jour / Suppression d'un évènement dans Google contact

		if (empty($conf->google->enabled)) return 0; // Module non actif

		//var_dump($object); exit;

		$userlogin = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
		if (empty($userlogin)) {	// We use setup of user
			$fuser = new User($this->db);
			// TODO
			// $userlogin = ...
		}


		$pwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;
		//print $action.' - '.$user.' - '.$pwd.' - '.$conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES.' - '.$conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS; exit;


		// Actions
		if ($action == 'COMPANY_CREATE' || $action == 'COMPANY_MODIFY' || $action == 'COMPANY_DELETE'
			|| $action == 'CONTACT_CREATE' || $action == 'CONTACT_MODIFY' || $action == 'CONTACT_DELETE'
			|| $action == 'MEMBER_CREATE' || $action == 'MEMBER_MODIFY' || $action == 'MEMBER_DELETE'
			|| $action == 'CATEGORY_LINK' || $action == 'CATEGORY_UNLINK' || $action == 'CATEGORY_DELETE' || $action == 'CATEGORY_MODIFY') {
			// We check if we must proceed the trigger code
			if (preg_match('/^COMPANY_/', $action) && empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES)) return 0;
			if (preg_match('/^CONTACT_/', $action) && empty($conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS)) return 0;
			if (preg_match('/^MEMBER_/', $action) && empty($conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS)) return 0;

			if (preg_match('/^CATEGORY_/', $action) && in_array($object->type, array(Categorie::TYPE_CUSTOMER, Categorie::TYPE_SUPPLIER)) && empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES)) return 0;
			if (preg_match('/^CATEGORY_/', $action) && $object->type == Categorie::TYPE_CONTACT && empty($conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS)) return 0;
			if (preg_match('/^CATEGORY_/', $action) && $object->type == Categorie::TYPE_MEMBER && empty($conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS)) return 0;
			if (preg_match('/^CATEGORY_/', $action) && !in_array($object->type, array(Categorie::TYPE_CUSTOMER, Categorie::TYPE_SUPPLIER, Categorie::TYPE_CONTACT, Categorie::TYPE_MEMBER))) return 0;

			if (getDolGlobalString('GOOGLE_DUPLICATE_INTO_THIRDPARTIES') == 'customersonly' && $object->client != 1 && $object->client != 3) return 0;
			if (getDolGlobalString('GOOGLE_DUPLICATE_INTO_THIRDPARTIES') == 'prospectsonly' && $object->client != 2 && $object->client != 3) return 0;

			dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id." element=".$object->element);

			$langs->load("other");

			if (empty($userlogin)) {
				dol_syslog("Setup to synchronize contacts into a Google contact is on but can't find complete setup for calendar target.", LOG_WARNING);
				return 0;
			}

			// Create client/token object
			$key_file_location = $conf->google->multidir_output[$conf->entity]."/".getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY');
			$force_do_not_use_session=false; // by default
			if (preg_match('/^testall/', GETPOST('action'))) $force_do_not_use_session=true;
			if (preg_match('/^testcreate/', GETPOST('action'))) $force_do_not_use_session=true;

			// Get the array with connection/token for contact sync (we use the OAuth token generated in mode Web)
			$servicearray = getTokenFromServiceAccount(getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_EMAIL'), $key_file_location, $force_do_not_use_session, 'web');

			if (! is_array($servicearray) || $servicearray == null) {
				$this->error="Failed to login to Google with current token";
				if ($servicearray) {
					$this->error .= " - ".$langs->trans($servicearray);
				}
				dol_syslog($this->error, LOG_ERR);
				$this->errors[] = $this->error;
				return -1;
			} else {
				if ($action == 'COMPANY_CREATE' || $action == 'CONTACT_CREATE' || $action == 'MEMBER_CREATE') {
					// Create the object on google side
					$ret = googleCreateContact($servicearray, $object, $userlogin);

					if (! preg_match('/ERROR/', $ret)) {
						if ($ret && ! preg_match('/google\.com/', $ret)) {
							$ret='google:'.$ret;
						}
						$object->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
						// Link to type group
						$contactID = $object->ref_ext;
						$reg = array();
						if ($contactID && preg_match('/google:(people\/.*)/', $contactID, $reg)) {
							$contactID = $reg[1];
							$type = ($object->element ? $object->element : 'unknown');
							$type = ($type === 'societe' ? 'thirdparty' : $type);

							// Check and update groups
							$typeGroupID = getGContactTypeGroupID($servicearray, $type);	// $typeGroupID will be defined if group already exists

							if ($typeGroupID && preg_match('/contactGroups\/.*/', $typeGroupID)) {
								$ret = googleLinkGroup($servicearray, $typeGroupID, $contactID);
								if ($ret > 0) {
									return 1;
								} else {
									$this->error = "Failed to link contact to group for ".$type." - ".$typeGroupID;
									dol_syslog($this->error, LOG_ERR);
									$this->errors[]=$this->error;
									return -1;
								}
							} else {
								$this->error = "Failed to get type group ID for ".$type." - ".$typeGroupID;
								dol_syslog($this->error, LOG_ERR);
								$this->errors[]=$this->error;
								return -1;
							}
						} else {
							$this->error = "Failed to get contact ID";
							dol_syslog($this->error, LOG_ERR);
							$this->errors[]=$this->error;
							return -1;
						}
					} else {
						$this->errors[]=$ret;
						return -1;
					}
				}
				if ($action == 'COMPANY_MODIFY' || $action == 'CONTACT_MODIFY' || $action == 'MEMBER_MODIFY') {
					$gid = preg_replace('/http:\/\//', 'https://', $object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) { // This record is linked with Google Contact
						$ret = googleUpdateContact($servicearray, $gid, $object, $userlogin);
						if ($ret === 0) { // Fails to update because not found, we try to create
							dol_syslog("Failed to update thirdparty, contact or member. It may have been deleted on Google side. we force the creation");
							$ret = googleCreateContact($servicearray, $object, $userlogin);
							//var_dump($ret); exit;

							if (! preg_match('/ERROR/', $ret)) {
								if ($ret && ! preg_match('/google\.com/', $ret)) {
									$ret='google:'.$ret;
								}
								$object->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
								return 1;
							} else {
								$this->errors[]=$ret;
								return -1;
							}
						}
						if ($ret == -1) {
							$this->errors[]=$object->error;
							return -1;
						}
						return 1;
					} elseif ($gid == '') {
						$ret = googleCreateContact($servicearray, $object, $userlogin);
						//var_dump($ret); exit;

						if (! preg_match('/ERROR/', $ret)) {
							if ($ret && ! preg_match('/google\.com/', $ret)) {
								$ret='google:'.$ret;
							}
							$object->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
							return 1;
						} else {
							$this->errors[]=$ret;
							return -1;
						}
					}

					return 1;
				}
				if ($action == 'COMPANY_DELETE' || $action == 'CONTACT_DELETE' || $action == 'MEMBER_DELETE') {
					$gid = basename($object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) { // This record is linked with Google Contact
						$ret = googleDeleteContactByRef($servicearray, $gid, $userlogin);
						if ($ret) {
							$this->error=$ret;
							$this->errors[]=$this->error;
							return 0;	// We do not stop delete if error
						}
					}
					return 1;
				}
				if ($action == 'CATEGORY_LINK') {
					$type = $object->context['linkto']->element ? $object->context['linkto']->element : 'unknown';
					$tag = array('id' => $object->id, 'label' => $object->label, 'type' => $type);
					$groupID = getGContactGroupID($servicearray, $tag);
					if ($groupID) {
						if (preg_match('/contactGroups\/.*/', $groupID)) { // This record is linked with Google Contact
							$object->update_ref_ext(substr('google:'.$groupID, 0, 255));
							$contactID = $object->context['linkto']->ref_ext;
							$reg = array();
							if ($contactID && preg_match('/google:(people\/.*)/', $contactID, $reg)) {
								$contactID = $reg[1];
								$ret = googleLinkGroup($servicearray, $groupID, $contactID);
								if ($ret > 0) {
									return 1;
								}
							}
						} elseif (((int) $groupID) < 0) {
							$object->error = "Failed to get/create category on Google";
						}
					}
					$this->error=$object->error;
					$this->errors[]=$this->error;
					return -1;
				}

				if ($action == 'CATEGORY_UNLINK') {
					$type = $object->context['unlinkoff']->element ? $object->context['unlinkoff']->element : 'unknown';
					$tag = array('id' => $object->id, 'label' => $object->label, 'type' => $type);
					$groupID = getGContactGroupID($servicearray, $tag);
					if ($groupID && preg_match('/contactGroups\/.*/', $groupID)) { // This record is linked with Google Contact
						$contactID = $object->context['unlinkoff']->ref_ext;
						$reg = array();
						if ($contactID && preg_match('/google:(people\/.*)/', $contactID, $reg)) {
							$contactID = $reg[1];
							$ret = googleUnlinkGroup($servicearray, $groupID, $contactID);
							if ($ret > 0) {
								return 1;
							}
						}
					}
					$this->error=$object->error;
					$this->errors[]=$this->error;
					return -1;
				}

				if ($action == 'CATEGORY_DELETE') {
					$tag = array('id' => $object->id, 'label' => $object->label);
					$groupID = getGContactGroupID($servicearray, $tag);
					if ($groupID && preg_match('/contactGroups\/.*/', $groupID)) { // This record is linked with Google Contact
						$ret = googleDeleteGroup($servicearray, $groupID);
						if ($ret > 0) {
							return 1;

						}
					}
					$this->error=$object->error;
					$this->errors[]=$this->error;
					return -1;
				}

				if ($action == 'CATEGORY_MODIFY') {
					// TODO Call the update in Google only if label has changed: $object->label != $object->oldcopy->label ?
					$newlabel = $object->label;
					$tag = array('id' => $object->id, 'label' => $newlabel);
					$groupID = getGContactGroupID($servicearray, $tag);
					if ($groupID && preg_match('/contactGroups\/.*/', $groupID)) { // This record is linked with Google Contact
						$ret = googleUpdateGroup($servicearray, $groupID, $newlabel);
						if (is_numeric($ret) && $ret > 0) {
							return 1;

						}
					}
					$this->error=$object->error;
					$this->errors[]=$ret;
					return -1;
				}
			}
		}

		return 0;
	}
}
