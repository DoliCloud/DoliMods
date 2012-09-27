<?php
/* Copyright (C) 2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       /htdocs/dolicloud/core/triggers/interface_50_modDoliCloud_DoliCloudSynchro.class.php
 *      \ingroup    dolicloud
 *      \brief      File to manage triggers for DoliCloud calendar sync
 */

include_once(DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php');
dol_include_once('/google/lib/google_calendar.lib.php');


/**
 *     \class      InterfaceDoliCloudSynchro
 *     \brief      Classe des fonctions triggers des actions google calendar
 */
class InterfaceDoliCloudSynchro
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
    function InterfaceDoliCloudSynchro($db)
    {
        $this->db = $db;

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "dolicloud";
        $this->description = "Triggers of this module allows to add interfaces with DoliCloud.";
        $this->version = '3.2';                        // 'experimental' or 'dolibarr' or version
        $this->picto = 'generic';
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

        if (! $conf->dolicloud->enabled) return 0;	// Module non actif

        // Actions

		return 0;
    }

}
?>
