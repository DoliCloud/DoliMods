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

include_once(DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php');
dol_include_once('/google/lib/google_calendar.lib.php');


/**
 *	Class of triggers for module Google
 */
class InterfaceGoogleCalendarSynchro
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

		$this->name = preg_replace('/^Interface/i','',get_class($this));
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
	 *      D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
	 *
	 *      @param	string		$action     Code of event
	 *      @param 	Action		$object     Objet concerne
	 *      @param  User		$user       Objet user
	 *      @param  Translate	$lang       Objet lang
	 *      @param  Conf		$conf       Objet conf
	 *      @return int         			<0 if KO, 0 if nothing is done, >0 if OK
	 */
	function run_trigger($action, $object, $user, $langs, $conf)
	{
		global $dolibarr_main_url_root;

		// Création / Mise à jour / Suppression d'un évènement dans Google Calendar

		if (!$conf->google->enabled) return 0; // Module non actif

		$fuser = new User($this->db);

		//var_dump($object); exit;
		$user = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;
		$pwd  = empty($conf->global->GOOGLE_PASSWORD)?'':$conf->global->GOOGLE_PASSWORD;

		if (empty($user) || empty($pwd))	// We use setup of user
		{
			// L'utilisateur concerné est l'utilisateur affecté à l'évènement dans Dolibarr
			// TODO : à rendre configurable ? (choix entre créateur / affecté / réalisateur)
			if(empty($object->usertodo->id)) return 0;

			$fuser->fetch($object->usertodo->id);

			$user = $fuser->conf->GOOGLE_LOGIN;
			$pwd = $fuser->conf->GOOGLE_PASSWORD;

			//if (empty($fuser->conf->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;
			if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;
		}
		else								// We use global setup
		{
			if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;
		}
		//print $action.' - '.$user.' - '.$pwd.' - '.$conf->global->GOOGLE_DUPLICATE_INTO_GCAL; exit;



		// Actions
		if ($action == 'ACTION_CREATE' || $action == 'ACTION_MODIFY' || $action == 'ACTION_DELETE')
		{
			dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);

			$langs->load("other");

			if (empty($user) || empty($pwd))
			{
				dol_syslog("Setup to synchronize events into a Google calendar of ".$fuser->login." is on but can't find complete setup for login/password (nor global nor for user).", LOG_WARNING);
				return 0;
			}

			// Create client object
			$service= 'cl';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
			$client = getClientLoginHttpClient($user, $pwd, $service);
			//var_dump($client); exit;

			if ($client == null)
			{
				dol_syslog("Failed to login to Google for login ".$user, LOG_ERR);
				$this->error='Failed to login to Google for login '.$user;
				$this->errors[]=$this->error;
				return -1;
			}
			else
			{
				// Event label can now include company and / or contact info, see configuration
				$eventlabel = trim($object->label);

				// Define $urlwithroot
				$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
				$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
				//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current


				if (! empty($object->societe->id) && $object->societe->id > 0 && empty($conf->global->GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE)) {
					$societe = new Societe($this->db);
					$societe->fetch($object->societe->id);
					$eventlabel .= ' - '.$societe->name;
					$tmpadd=$societe->getFullAddress(0);
					if ($tmpadd && empty($conf->global->GOOGLE_DISABLE_ADD_ADDRESS_INTO_DESC)) $object->note.="\n\n".$societe->getFullAddress(1);
					if (! empty($societe->phone)) $object->note.="\n".$langs->trans("Phone").': '.$societe->phone;

					$urltoelem=$urlwithroot.'/societe/soc.ph?socid='.$societe->id;
					$object->note.="\n".$langs->trans("LinkToThirdParty").': '.$urltoelem;
				}
				if (! empty($object->contact->id) && $object->contact->id > 0 && empty($conf->global->GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT)) {
					$contact = new Contact($this->db);
					$contact->fetch($object->contact->id);
					$eventlabel .= ' - '.$contact->getFullName($langs, 1);
					$tmpadd=$contact->getFullAddress(0);
					if ($tmpadd && empty($conf->global->GOOGLE_DISABLE_ADD_ADDRESS_INTO_DESC)) $object->note.="\n\n".$contact->getFullAddress(1);
					if (! empty($contact->phone)) $object->note.="\n".$langs->trans("Phone").': '.$contact->phone;
					if (! empty($contact->phone_perso)) $object->note.="\n".$langs->trans("PhonePerso").': '.$contact->phone_perso;
					if (! empty($contact->phone_mobile)) $object->note.="\n".$langs->trans("PhoneMobile").': '.$contact->phone_mobile;

					$urltoelem=$urlwithroot.'/contact/fiche.ph?id='.$contact->id;
					$object->note.="\n".$langs->trans("LinkToContact").': '.$urltoelem;
				}

				$object->label = $eventlabel;

				if ($action == 'ACTION_CREATE') {
					$ret = createEvent($client, $object);
					//var_dump($ret); exit;
					$object->update_ref_ext($ret);
					// This is to store ref_ext to allow updates

					return 1;
				}
				if ($action == 'ACTION_MODIFY') {
					$gid = basename($object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) // This record is linked with Google Calendar
					{
						$ret = updateEvent($client, $gid, $object);
						//var_dump($ret); exit;

						if ($ret < 0)// Fails to update, we try to create
						{
							$ret = createEvent($client, $object);
							//var_dump($ret); exit;

							$object->update_ref_ext($ret);
							// This is to store ref_ext to allow updates
						}
						return 1;
					} else if ($gid == '') { // No google id, may be a reaffected event
						$ret = createEvent($client, $object);
						//var_dump($ret); exit;

						$object->update_ref_ext($ret);
						// This is to store ref_ext to allow updates
					}
				}
				if ($action == 'ACTION_DELETE') {
					$gid = basename($object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) // This record is linked with Google Calendar
					{
						$ret = deleteEventById($client, $gid);
						//var_dump($ret); exit;

						return 1;
					}
				}
			}
		}

		return 0;
	}

}
?>
