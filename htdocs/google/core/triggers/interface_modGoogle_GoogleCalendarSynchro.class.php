<?php
/* Copyright (C) 2011 Regis Houssin	<regis@dolibarr.fr>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *      \file       /google/inclucdes/triggers/interface_modGoogle_GoogleCalendarSynchro.class.php
 *      \ingroup    google
 *      \brief      File to manage triggers for Google calendar sync
 *      \version	$Id: interface_modGoogle_GoogleCalendarSynchro.class.php,v 1.1 2011/08/01 19:28:55 eldy Exp $
 */

include_once(DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php');
dol_include_once('/google/lib/google_calendar.lib.php');


/**
 *     \class      InterfaceGoogleCalendarSynchro
 *     \brief      Classe des fonctions triggers des actions google calendar
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
     *   @param      DB      Database handler
     */
    function InterfaceGoogleCalendarSynchro($DB)
    {
        $this->db = $DB ;

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "google";
        $this->description = "Triggers of this module allows to add an event inside Google calendar for each Dolibarr business event.";
        $this->version = 'dolibarr';                        // 'experimental' or 'dolibarr' or version
        $this->picto = 'google@google';
    }

    /**
     *   Renvoi nom du lot de triggers
     *   @return     string      Nom du lot de triggers
     */
    function getName()
    {
        return $this->name;
    }

    /**
     *   Renvoi descriptif du lot de triggers
     *   @return     string      Descriptif du lot de triggers
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *   Renvoi version du lot de triggers
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
    function run_trigger($action,$object,$user,$langs,$conf)
    {
        // Mettre ici le code a executer en reaction de l'action
        // Les donnees de l'action sont stockees dans $object

        if (! $conf->google->enabled) return 0;	// Module non actif
        if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) return 0;

        // Actions
        if ($action == 'ACTION_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

            if (empty($conf->global->GOOGLE_LOGIN) || empty($conf->global->GOOGLE_PASSWORD))
            {
                dol_syslog("Setup to duplicate events into a Google calendar is on but setup (login/password) is not complete", LOG_WARNING);
                return 0;
            }

            $langs->load("other");

            //var_dump($object); exit;

			$title			= $object->label;
            $desc			= dol_string_nohtmltag($object->note);
            $where			= $object->location;
            $startDate		= dol_print_date($object->datep,'%Y-%m-%d');
            $startTime		= dol_print_date($object->datep,'%H:%M');
            $endDate		= dol_print_date($object->datef,'%Y-%m-%d');
            $endTime		= dol_print_date($object->datef,'%H:%M');
            if (empty($endDate)) $endDate=$startDate;
            if (empty($endTime)) $endTime=$startTime;

	        $user = $conf->global->GOOGLE_LOGIN;
	        $pwd = $conf->global->GOOGLE_PASSWORD;

	        $client = getClientLoginHttpClient($user, $pwd);
	        //var_dump($client); exit;

	        $ret = createEvent($client, $title, $desc, $where, $startDate, $startTime, $endDate, $endTime, getCurrentTimeZone(), $object->id);
	        //var_dump($ret); exit;

	        $object->update_ref_ext($ret);    // This is to store ref_ext to allow updates
        }

		return 0;
    }

}
?>
