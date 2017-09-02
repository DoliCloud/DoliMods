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
 *	\file       htdocs/sellyoursaas/class/actions_sellyoursaas.class.php
 *	\ingroup    cabinetmed
 *	\brief      File to control actions
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	Class to manage hooks for module SellYourSaas
 */
class ActionsSellyoursaas
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
     *    Execute action
     *
     *    @param	array	$parameters		Array of parameters
     *    @param    mixed	$object      	Deprecated. This field is not used
     *    @param    string	$action      	'add', 'update', 'view'
     *    @return   int         			<0 if KO,
     *                              		=0 if OK but we want to process standard actions too,
     *                              		>0 if OK and we want to replace standard actions.
     */
    function doActions($parameters,&$object,&$action)
    {
        global $db,$langs,$conf;

        dol_syslog(get_class($this).'::executeHooks action='.$action);
        $langs->load("sellyoursaas@sellyoursaas");

        return 0;
    }

    /**
     * Complete search forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function addSearchEntry($parameters)
    {
        global $langs;

        $langs->load("sellyoursaas@sellyoursaas");
        $search_boxvalue = $parameters['search_boxvalue'];

        $this->results['searchintodolicloud']=array('img'=>'object_generic', 'label'=>$langs->trans("SearchIntoDoliCloud", $search_boxvalue), 'text'=>img_picto('','object_generic').' '.$langs->trans("InstanceDolicloud", $search_boxvalue), 'url'=>dol_buildpath('/sellyoursaas/backoffice/dolicloud_list.php',1).'?search_multi='.urlencode($search_boxvalue));

        return 0;
    }
}


