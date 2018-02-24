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

        if (in_array($parameters['currentcontext'], array('contractlist')))
        {
        	global $fieldstosearchall;

        	$fieldstosearchall['s.email']="ThirdPartyEmail";
        }

        return 0;
    }

    /**
     * Complete search forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	int						1=Replace standard code, 0=Continue standard code
     */
    function addSearchEntry($parameters)
    {
        global $langs;

        $langs->load("sellyoursaas@sellyoursaas");
        $search_boxvalue = $parameters['search_boxvalue'];

        $this->results['searchintocontract']=$parameters['arrayresult']['searchintocontract'];
        $this->results['searchintocontract']['position']=22;

        $this->results['searchintodolicloud']=array('position'=>23, 'img'=>'object_generic', 'label'=>$langs->trans("SearchIntoOldDoliCloudInstances", $search_boxvalue), 'text'=>img_picto('','object_generic').' '.$langs->trans("OldDoliCloudInstances", $search_boxvalue), 'url'=>dol_buildpath('/sellyoursaas/backoffice/dolicloud_list.php',1).'?search_multi='.urlencode($search_boxvalue));

        return 0;
    }


    /**
     * Complete search forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	int						1=Replace standard code, 0=Continue standard code
     */
    function getDefaultFromEmail($parameters)
    {
    	global $conf, $langs;
    	global $object;

    	$langs->load("sellyoursaas@sellyoursaas");

    	$result='';

    	if (is_object($object))
    	{
    		$thirdparty = null;
    		if (is_object($object->thirdparty)) $thirdparty = $object->thirdparty;
    		elseif ($object->element == 'societe') $thirdparty = $object;

    		if (is_object($thirdparty))
    		{
	    		$categ_customer_sellyoursaas = $conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG;

	    		include_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
	    		$categobj = new Categorie($this->db);
	    		$categobj->fetch($categ_customer_sellyoursaas);

	    		// Search if customer is a dolicloud customer
	    		$hascateg = $categobj->containsObject('customer', $thirdparty->id);
				if ($hascateg) $result='senderprofile_1_1';
	    		//var_dump($hascateg);

	    		// Search if customer has a premium subscription
	    		//var_dump($object->thirdparty);

    		}
    	}
    	$this->results['defaultfrom']=$result;

    	return 0;
    }


    /**
     * Run substitutions during ODT generation
     *
     * @param	array	$parameters		Array of parameters
     * @return	int						1=Replace standard code, 0=Continue standard code
     */
    function ODTSubstitution($parameters)
    {
    	global $conf, $langs;
    	global $object;

    	$langs->load("sellyoursaas@sellyoursaas");

    	$contract = $parameters['object'];

    	$parameters['substitutionarray']['sellyoursaas_version']=7;
    	$parameters['substitutionarray']['sellyoursaas_signature_logo']=DOL_DATA_ROOT.'/mycompany/signature_owner.jpg';

    	return 0;
    }

}


