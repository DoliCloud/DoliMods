<?php
/* Copyright (C) 2005-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2009-2011 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2011	   Juanjo Menent        <jmenent@2byte.es>
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
 *	\file       htdocs/cabinetmed/core/triggers/interface_50_modCabinetmed_ActionsCabinetmed.class.php
 *  \ingroup    cabinetmed
 *  \brief      Trigger file for cabinetmed module
 */


/**
 *  Class of triggered functions for cabinetmed module
 */
class InterfaceActionsCabinetmed
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

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "cabinetmed";
        $this->description = "Triggers of this module add actions in cabinetmed according to setup made in cabinetmed setup.";
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
     *      All functions "run_trigger" are triggered if file is inside directory htdocs/core/triggers
     *      Following properties must be filled:
     *      $object->actiontypecode (translation action code: AC_OTH, ...)
     *      $object->actionmsg (note, long text)
     *      $object->actionmsg2 (label, short text)
     *      $object->sendtoid (id of contact)
     *      $object->socid
     *      Optionnal:
     *      $object->fk_element
     *      $object->elementtype
     *
     *      @param	string	$action     Event code (COMPANY_CREATE, PROPAL_VALIDATE, ...)
     *      @param  Object	$object     Object action is done on
     *      @param  User	$user       Object user
     *      @param  Langs	$langs      Object langs
     *      @param  Conf	$conf       Object conf
     *      @return int         		<0 if KO, 0 if no action are done, >0 if OK
     */
    function run_trigger($action,$object,$user,$langs,$conf)
    {
		$ok=0;

		// Actions
        if ($action == 'CABINETMED_OUTCOME_CREATE')
        {
            // object is consultation.class.php
            if (empty($conf->agenda->enabled)) return 0;     // Module not active, we do nothing

            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
            $langs->load("agenda");
            $langs->load("cabinetmed@cabinetmed");

            $thirdparty=new Societe($this->db);
            $thirdparty->fetch($object->fk_soc);

            $object->actiontypecode='AC_OTH_AUTO';
            if (empty($object->actionmsg2)) $object->actionmsg2=$langs->transnoentities("NewOutcomeToDolibarr",$object->id,$thirdparty->name);
            $object->actionmsg=$langs->transnoentities("NewOutcomeToDolibarr",$object->id,$thirdparty->name);
            //$this->desc.="\n".$langs->transnoentities("Customer").': '.yn($object->client);
            //$this->desc.="\n".$langs->transnoentities("Supplier").': '.yn($object->fournisseur);
            $object->actionmsg.="\n".$langs->transnoentities("Author").': '.$user->login;

            $object->sendtoid=0;
            $object->socid=$object->fk_soc;
            $ok=1;
        }
        if ($action == 'CABINETMED_SENTBYMAIL')
        {
            // object is societe.class.php
            if (empty($conf->agenda->enabled)) return 0;     // Module not active, we do nothing

            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
            $langs->load("agenda");
            $langs->load("cabinetmed@cabinetmed");

            $object->actiontypecode='AC_CABMED';
            if (empty($object->actionmsg2)) $object->actionmsg2=$langs->transnoentities("DocumentSentByEMail",$object->ref);
            if (empty($object->actionmsg))
            {
                $object->actionmsg=$langs->transnoentities("DocumentSentByEMail",$object->ref);
                $object->actionmsg.="\n".$langs->transnoentities("Author").': '.$user->login;
            }

            // Parameters $object->sendtoid and $object->socid defined by caller
            $ok=1;
		}

		// If not found
        /*
        else
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' was ran by ".__FILE__." but no handler found for this action.");
			return 0;
        }
        */

        // Add entry in event table
        if ($ok)
        {
			$now=dol_now();

            require_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
            require_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
            $contactforaction=new Contact($this->db);
            $societeforaction=new Societe($this->db);
			if ($object->sendtoid > 0) $contactforaction->fetch($object->sendtoid);
            if ($object->socid > 0)    $societeforaction->fetch($object->socid);

			// Insertion action
			require_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');
			$actioncomm = new ActionComm($this->db);
			$actioncomm->type_code   = $object->actiontypecode;
			$actioncomm->label       = $object->actionmsg2;
			$actioncomm->note        = $object->actionmsg;
			$actioncomm->datep       = $now;
			$actioncomm->datef       = $now;
			$actioncomm->durationp   = 0;
			$actioncomm->punctual    = 1;
			$actioncomm->percentage  = -1;   // Not applicable
			$actioncomm->contact     = $contactforaction;
			$actioncomm->societe     = $societeforaction;
			$actioncomm->author      = $user;   // User saving action
			//$actioncomm->usertodo  = $user;	// User affected to action
			$actioncomm->userdone    = $user;	// User doing action

			$actioncomm->fk_element  = $object->id;
			$actioncomm->elementtype = $object->element;

			$ret=$actioncomm->add($user);       // User qui saisit l'action
			if ($ret > 0)
			{
				return 1;
			}
			else
			{
                $error ="Failed to insert : ".$actioncomm->error;
                $this->error=$error;

                dol_syslog(get_class($this).": ".$this->error, LOG_ERR);
                return -1;
			}
		}

		return 0;
    }

}
?>
