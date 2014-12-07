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

		//var_dump($object); exit;

		$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;
		if (empty($userlogin))	// We use setup of user
		{
			// L'utilisateur concerné est l'utilisateur affecté à l'évènement dans Dolibarr
			// TODO : à rendre configurable ? (choix entre propriétaire / assigné)
			if (! empty($object->userownerid))
			{
				$fuser = new User($this->db);
				$fuser->fetch($object->userownerid);
				$userlogin = $fuser->conf->GOOGLE_LOGIN;
			}
			else return 0;

			if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;
		}
		else								// We use global setup
		{
			if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;
		}


		// Actions
		if ($action == 'ACTION_CREATE' || $action == 'ACTION_MODIFY' || $action == 'ACTION_DELETE')
		{
			dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);

			$langs->load("other");

			if (empty($userlogin))
			{
				dol_syslog("Setup to synchronize events into a Google calendar of ".$userlogin." is on but can't find complete setup for login/password (nor global nor for user).", LOG_WARNING);
				return 0;
			}

			// Create client/token object
			$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
			$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
			$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID, $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session);

			if (! is_array($servicearray))
			{
				$this->errors[]=$servicearray;
				return -1;
			}

			if ($servicearray == null)
			{
				$this->error="Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID.", ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
				dol_syslog($this->error, LOG_ERR);
				$this->errors[]=$this->error;
				return -1;
			}
			else
			{
				// Event label can now include company and / or contact info, see configuration
				google_complete_label_and_note($object, $langs);

				if ($action == 'ACTION_CREATE')
				{
					$ret = createEvent($servicearray, $object, $userlogin);
					if (! preg_match('/ERROR/',$ret))
					{
						if (! preg_match('/google\.com/',$ret)) $ret='google:'.$ret;
						$object->update_ref_ext($ret);	// This is to store ref_ext to allow updates
						return 1;
					}
					else
					{
						$this->errors[]=$ret;
						return -1;
					}
				}
				if ($action == 'ACTION_MODIFY')
				{
					$gid = basename($object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) // This record is linked with Google Calendar
					{
						$ret = updateEvent($servicearray, $gid, $object, $userlogin);
						//var_dump($ret); exit;

						if (! is_numeric($ret) || $ret < 0)// Fails to update, we try to create
						{
							$ret = createEvent($servicearray, $object, $userlogin);
							//var_dump($ret); exit;

							if (! preg_match('/ERROR/',$ret))
							{
								if (! preg_match('/google\.com/',$ret)) $ret='google:'.$ret;
								$object->update_ref_ext($ret);	// This is to store ref_ext to allow updates
								return 1;
							}
							else
							{
								$this->errors[]=$ret;
								return -1;
							}
						}
						return 1;
					} else if ($gid == '') { // No google id, may be a reaffected event
						$ret = createEvent($servicearray, $object, $userlogin);
						//var_dump($ret); exit;

						if (! preg_match('/ERROR/',$ret))
						{
							if (! preg_match('/google\.com/',$ret)) $ret='google:'.$ret;
							$object->update_ref_ext($ret);	// This is to store ref_ext to allow updates
							return 1;
						}
						else
						{
							$this->errors[]=$ret;
							return -1;
						}
					}
				}
				if ($action == 'ACTION_DELETE')
				{
					$gid = basename($object->ref_ext);
					if ($gid && preg_match('/google/i', $object->ref_ext)) // This record is linked with Google Calendar
					{
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
?>
