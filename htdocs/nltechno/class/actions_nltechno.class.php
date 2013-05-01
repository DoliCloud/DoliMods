<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/nltechno/class/actions_nltechno.class.php
 *	\ingroup    cabinetmed
 *	\brief      File to control actions
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	Class to manage hooks for module NLTechno
 */
class ActionsNltechno
{
    var $db;
    var $error;
    var $errors=array();

    /**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    }


    /**
     * Complete search forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function printSearchForm($parameters)
    {
        global $langs, $user, $conf;

        $searchform='';
        if (! empty($conf->nltechno->enabled))
        {
            $langs->load("companies");
            $langs->load("nltechno@nltechno");
            $searchform=printSearchForm(dol_buildpath('/nltechno/dolicloud/dolicloud_list.php',1), dol_buildpath('/nltechno/dolicloud/dolicloud_list.php',1), img_picto('','object_generic').' '.$langs->trans("InstanceDolicloud"), '', 'search_instance');
        }
		$this->resprints = $searchform;

        return 0;
    }

}

?>
