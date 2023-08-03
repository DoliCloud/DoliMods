<?php
/* Copyright (C) 2008-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       /google/core/triggers/interface_50_modGoogle_GoogleCalendarSynchro.class.php
 *      \ingroup    google
 *      \brief      File to manage triggers for Google calendar sync
 */

include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
dol_include_once('/google/lib/google_calendar.lib.php');


/**
 *	Class of triggers for module Google
 */
class InterfaceGoogleCalendarSynchro extends DolibarrTriggers
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
		$this->description = "Triggers of this module allows to add an event inside Google calendar for each Dolibarr business event.";
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
	 *      @param  Translate	$langs       Objet lang
	 *      @param  Conf		$conf       Objet conf
	 *      @return int         			<0 if KO, 0 if nothing is done, >0 if OK
	 */
	function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		global $dolibarr_main_url_root;

		// Création / Mise à jour / Suppression d'un évènement dans Google Calendar

		if (!$conf->google->enabled) return 0; // Module non actif

		if (empty($conf->global->GOOGLE_INCLUDE_AUTO_EVENT) && isset($object->type_code) && $object->type_code == 'AC_OTH_AUTO') {
			return 0;
		}

		// Actions
		if ($action == 'ACTION_CREATE' || $action == 'ACTION_MODIFY' || $action == 'ACTION_DELETE') {
			dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);

			$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;
			if (empty($userlogin)) {	// We use setup of user
				if (empty($conf->global->GOOGLE_SYNC_EVENT_TO_SALE_REPRESENTATIVE)) {
					// L'utilisateur concerné est l'utilisateur propriétaire de l'évènement (proprio dans Dolibarr)
					if (! empty($object->userownerid)) {
						$fuser = new User($this->db);
						$fuser->fetch($object->userownerid, '', '', 1);		// 1 to be sure to load personal conf
						$userlogin = $fuser->conf->GOOGLE_LOGIN;
					} elseif (! empty($object->usertodo) && is_object($object->usertodo)) {	// For backward compatibility (3.6)
						$fuser = new User($this->db);
						$fuser->fetch($object->usertodo->id);
						$userlogin = $fuser->conf->GOOGLE_LOGIN;
					} else {
						return 0;    // Should not occurs. This means there is no owner of event.
					}
				} else {
					// We want user that is first sale representative of company linked to event
					if (is_object($object->societe) && isset($object->societe->id) && $object->societe->id > 0) {
						$salerep=$object->societe->getSalesRepresentatives($user);
						if (is_array($salerep) && count($salerep) > 0) {
							$idusersalerep=$salerep[0]['id'];
							$fuser = new User($this->db);
							$fuser->fetch($idusersalerep, '', '', 1);		// 1 to be sure to load personal conf
							$userlogin = $fuser->conf->GOOGLE_LOGIN;
						} else {
							dol_syslog("Setup to synchronize events into a Google calendar is on but there is no sale representative linked to this event.", LOG_DEBUG);
							return 0;     // There is no sale representative
						}
					} else {
						dol_syslog("Setup to synchronize events into a Google calendar is on but this event is not linked to a company so not linked to any sale representative", LOG_DEBUG);
						return 0;     // There is no sale representative
					}
				}

				if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;  // In a future this option may be overwrite per user
			} else // We use global setup
			{
				if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;
			}

			$langs->load("other");
			$langs->load("google@google");

			if (empty($userlogin)) {
				dol_syslog("Setup to synchronize events into a Google calendar for user id ".$fuser->id.", login=".$fuser->login." is on but we can't find a complete setup for agenda id target (nor in global setup nor in user setup).", LOG_WARNING);
				return 0;
			}

			// Create client/token object
			$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
			$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default

			$user_to_impersonate = false;
			if (! empty($conf->global->GOOGLE_INCLUDE_ATTENDEES)) {
				$user_to_impersonate = $userlogin;
			}

			$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session, 'service', $user_to_impersonate);

			if (! is_array($servicearray) || $servicearray == null) {
				$this->error = "Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
				$this->errors[] = "Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
				$this->errors[] = $this->error;
				if ($servicearray) {
					$this->errors[] = $servicearray;
				}
				dol_syslog($this->error, LOG_ERR);
				return -1;
			} else {
				// Event label can now include company and / or contact info, and url link to thirdparty or contact, see configuration
				google_complete_label_and_note($object, $langs);
				//var_dump($action.' '.$object->note_private.' '.$object->ref_ext);exit;

				if ($action == 'ACTION_CREATE') {
					$ret = createEvent($servicearray, $object, $userlogin);
					if (! preg_match('/ERROR/', $ret)) {
						if (! preg_match('/google\.com/', $ret)) $ret='google:'.$ret;
						$object->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
						return 1;
					} else {
						$this->errors[]=$ret;
						return -1;
					}
				}
				if ($action == 'ACTION_MODIFY') {
					$gid = basename($object->ref_ext);

					if ($gid && preg_match('/google/i', $object->ref_ext)) { // This record is linked with Google Calendar
						$ret = updateEvent($servicearray, $gid, $object, $userlogin);

						if (! is_numeric($ret) || $ret < 0) {// Fails to update
							dol_syslog("ret=".$ret);
							if (preg_match('/\(403\)/', $ret)) {
								$this->errors[]=$ret;
								return -1;
							}

							// We suppose update failed because record was not found, we try to create it
							$ret = createEvent($servicearray, $object, $userlogin);
							//var_dump($ret); exit;

							if (! preg_match('/ERROR/', $ret)) {
								if (! preg_match('/google\.com/', $ret)) $ret='google:'.$ret;
								$object->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
								return 1;
							} else {
								$this->errors[]=$ret;
								return -1;
							}
						}
						return 1;
					} elseif ($gid == '') { // No google id, may be a reaffected event
						$ret = createEvent($servicearray, $object, $userlogin);
						//var_dump($ret); exit;

						if (! preg_match('/ERROR/', $ret)) {
							if (! preg_match('/google\.com/', $ret)) $ret='google:'.$ret;
							$object->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
							return 1;
						} else {
							$this->errors[]=$ret;
							return -1;
						}
					}
				}
				if ($action == 'ACTION_DELETE') {
					$gid = basename($object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) { // This record is linked with Google Calendar
						$ret = deleteEventById($servicearray, $gid, $userlogin);
						//var_dump($ret); exit;

						return 1;
					}
				}
			}
		}

		return 0;
	}
}
