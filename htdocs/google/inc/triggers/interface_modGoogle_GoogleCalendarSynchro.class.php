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
 *      \file       /google/inc/triggers/interface_modGoogle_GoogleCalendarSynchro.class.php
 *      \ingroup    google
 *      \brief      Fichier de gestion des triggers google calendar
 *      \version	$Id: interface_modGoogle_GoogleCalendarSynchro.class.php,v 1.1 2011/07/18 09:00:30 hregis Exp $
 */

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
     *      @param      action      Code de l'evenement
     *      @param      object      Objet concerne
     *      @param      user        Objet user
     *      @param      lang        Objet lang
     *      @param      conf        Objet conf
     *      @return     int         <0 si ko, 0 si aucune action faite, >0 si ok
     */
    function run_trigger($action,$object,$user,$langs,$conf)
    {
        // Mettre ici le code a executer en reaction de l'action
        // Les donnees de l'action sont stockees dans $object

        if (! $conf->google->enabled) return 0;	// Module non actif
        //if (! $object->use_googlecal) return 0;	// Option syncro webcal non active

        // Actions
        if ($action == 'ACTION_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
            $langs->load("other");
            
            //var_dump($object); exit;
            
			$title			= $object->label;
            $desc			= dol_string_nohtmltag($object->note);
            $where			= $object->location;
            $startDate		= dol_print_date($object->datep,'%Y-%m-%d');
            $startTime		= dol_print_date($object->datep,'%H:%M');
            $endDate		= dol_print_date($object->datef,'%Y-%m-%d');
            $endTime		= dol_print_date($object->datef,'%H:%M');
            
            // For test only
	        $user = 'xxxxx@gmail.com';
	        $pwd = 'xxxxx';
	            
	        $client = getClientLoginHttpClient($user, $pwd);
	        //var_dump($client); exit;
	            
	        $ret = createEvent($client, $title, $desc, $where, $startDate, $startTime, $endDate, $endTime);
	        //var_dump($ret); exit;

        }

		return 0;
    }

}
?>
