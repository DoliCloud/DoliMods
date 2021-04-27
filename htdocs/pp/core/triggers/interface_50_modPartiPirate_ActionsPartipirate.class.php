<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/partipirate/core/triggers/interface_50_modPartiPirate_ActionsPartiPirate.class.php
 *  \ingroup    partipirate
 *  \brief      Trigger file for partipirate module
 */


/**
 *  Class of triggered functions for partipirate module
 */
class InterfaceActionsPartiPirate
{
	var $db;
	var $error;

	var $date;
	var $duree;
	var $texte;
	var $desc;

	/**
	 *	Constructor
	 *
	 *  @param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "partipirate";
		$this->description = "Triggers of this module add actions in partipirate according to setup made in partipirate setup.";
		$this->picto = 'user';
	}

	/**
	 *   Return name of trigger file
	 *
	 *   @return     string      Name of trigger file
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 *   Return description of trigger file
	 *
	 *   @return     string      Description of trigger file
	 */
	function getDesc()
	{
		return $this->description;
	}

	/**
	 *   Return version of trigger file
	 *
	 *   @return     string      Version of trigger file
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
	 *      Function called when a Dolibarrr business event is done.
	 *      All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers
	 *
	 *      @param	string	$action     Event code (COMPANY_CREATE, PROPAL_VALIDATE, ...)
	 *      @param  Object	$object     Object action is done on
	 *      @param  User	$user       Object user
	 *      @param  Langs	$langs      Object langs
	 *      @param  Conf	$conf       Object conf
	 *      @return int         		<0 if KO, 0 if no action are done, >0 if OK
	 */
	function runTrigger($action, $object, $user, $langs, $conf)
	{
		$ok=0;

		// Actions
		if ($action == 'XXX') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			$langs->load("partipirate@partipirate");


			$ok=1;
		}

		// Add entry in event table
		if ($ok) {
		}

		return 0;
	}
}
