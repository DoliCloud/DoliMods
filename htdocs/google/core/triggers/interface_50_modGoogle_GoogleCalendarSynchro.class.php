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
     *
     *   @param		DoliDB		$db      Database handler
     */
    function InterfaceGoogleCalendarSynchro($db)
    {
        $this->db = $db;

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "google";
        $this->description = "Triggers of this module allows to add an event inside Google calendar for each Dolibarr business event.";
        $this->version = '3.2';                        // 'experimental' or 'dolibarr' or version
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
                dol_syslog("Setup to synchronize events into a Google calendar is on but setup (login/password) is not complete", LOG_WARNING);
                return 0;
            }

            $langs->load("other");

            //var_dump($object); exit;

	        $user = $conf->global->GOOGLE_LOGIN;
	        $pwd = $conf->global->GOOGLE_PASSWORD;

	        $client = getClientLoginHttpClient($user, $pwd);
	        //var_dump($client); exit;

	        $ret = createEvent($client, $object);
	        //var_dump($ret); exit;

	        $object->update_ref_ext($ret);    // This is to store ref_ext to allow updates

	        return 1;
        }

        if ($action == 'ACTION_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

            $gid=basename($object->ref_ext);
            if ($gid && preg_match('/google/i',$object->ref_ext))    // This record is linked with Google Calendar
            {
                if (empty($conf->global->GOOGLE_LOGIN) || empty($conf->global->GOOGLE_PASSWORD))
                {
                    dol_syslog("Setup to synchronize events into a Google calendar is on but setup (login/password) is not complete", LOG_WARNING);
                    return 0;
                }

                $langs->load("other");

                $user = $conf->global->GOOGLE_LOGIN;
                $pwd = $conf->global->GOOGLE_PASSWORD;

                $client = getClientLoginHttpClient($user, $pwd);
                //var_dump($client); exit;

                $ret = updateEvent($client, $gid, $object);
                //var_dump($ret); exit;

                if ($ret < 0)     // Fails to update, we try to create
                {
        	        $ret = createEvent($client, $object);
        	        //var_dump($ret); exit;

        	        $object->update_ref_ext($ret);    // This is to store ref_ext to allow updates
                }
                return 1;
            }
        }

        if ($action == 'ACTION_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

            $gid=basename($object->ref_ext);
            if ($gid && preg_match('/google/i',$object->ref_ext))    // This record is linked with Google Calendar
            {
                if (empty($conf->global->GOOGLE_LOGIN) || empty($conf->global->GOOGLE_PASSWORD))
                {
                    dol_syslog("Setup to synchronize events into a Google calendar is on but setup (login/password) is not complete", LOG_WARNING);
                    return 0;
                }

                $langs->load("other");

                $user = $conf->global->GOOGLE_LOGIN;
                $pwd = $conf->global->GOOGLE_PASSWORD;

                $client = getClientLoginHttpClient($user, $pwd);
                //var_dump($client); exit;

                $ret = deleteEventById($client, $gid);
                //var_dump($ret); exit;

                return 1;
            }
        }

		return 0;
    }

}
?>
