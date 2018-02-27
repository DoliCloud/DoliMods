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
    function addMoreActionsButtons($parameters,&$object,&$action)
    {
    	global $db,$langs,$conf,$user;

    	dol_syslog(get_class($this).'::executeHooks action='.$action);
    	$langs->load("sellyoursaas@sellyoursaas");

    	if (in_array($parameters['currentcontext'], array('contractcard'))
    		&& ! empty($object->array_options['options_deployment_status']))		// do something only for the context 'somecontext1' or 'somecontext2'
    	{
	    	if ($user->rights->sellyoursaas->write)
	    	{
	    		if (in_array($object->array_options['options_deployment_status'], array('processing', 'undeployed')))
	    		{
	    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=deploy">' . $langs->trans('Redeploy') . '</a>';
	    		}
	    		else
	    		{
	    			print '<a class="butActionRefused" href="#" title="'.$langs->trans("ContractMustHaveStatusProcessingOrUndeployed").'">' . $langs->trans('Redeploy') . '</a>';
	    		}

	    		if (in_array($object->array_options['options_deployment_status'], array('done')))
	    		{
	    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=undeploy">' . $langs->trans('Undeploy') . '</a>';
	    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=refresh">' . $langs->trans('RefreshRemoteData') . '</a>';
	    		}
	    		else
	    		{
	    			print '<a class="butActionRefused" href="#" title="'.$langs->trans("ContractMustHaveStatusDone").'">' . $langs->trans('Undeploy') . '</a>';
	    			print '<a class="butActionrefused" href="#" title="'.$langs->trans("ContractMustHaveStatusDone").'">' . $langs->trans('RefreshRemoteData') . '</a>';
	    		}
	    	}
    	}

    	return 0;
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
        global $db,$langs,$conf,$user;

        dol_syslog(get_class($this).'::executeHooks action='.$action);
        $langs->load("sellyoursaas@sellyoursaas");

        if (in_array($parameters['currentcontext'], array('contractlist')))
        {
        	global $fieldstosearchall;

        	$fieldstosearchall['s.email']="ThirdPartyEmail";
        }

        if (in_array($parameters['currentcontext'], array('contractcard')))
        {
			if ($action == 'deploy')
			{
				dol_include_once('sellyoursaas/class/sellyoursaasutils.class.php');
				$sellyoursaasutils = new SellYourSaasUtils($db);
				$result = $sellyoursaasutils->sellyoursaasRemoteAction('deployall', $object);
				if ($result <= 0)
				{
					$error++;
					$this->error=$sellyoursaasutils->error;
					$this->errors=$sellyoursaasutils->errors;
				}

				// Finish deployall

				$comment = 'Activation after click on redeploy from contract card';

				// Activate all lines
				if (! $error)
				{
					dol_syslog("Activate all lines");

					$result = $object->activateAll($user, dol_now(), 1, $comment);
					if ($result <= 0)
					{
						$error++;
						setEventMessages($object->error, $object->errors, 'errors');
					}
				}

				// End of deployment is now OK / Complete
				if (! $error)
				{
					$object->array_options['options_deployment_status'] = 'done';
					$object->array_options['options_deployment_date_end'] = dol_now('tzserver');
					$object->array_options['options_undeployment_date'] = '';
					$object->array_options['options_undeployment_ip'] = '';

					$result = $object->update($user);
					if ($result < 0)
					{
						// We ignore errors. This should not happen in real life.
						//setEventMessages($contract->error, $contract->errors, 'errors');
					}
				}
			}

			if ($action == 'undeploy')
			{
				dol_include_once('sellyoursaas/class/sellyoursaasutils.class.php');
				$sellyoursaasutils = new SellYourSaasUtils($db);
				$result = $sellyoursaasutils->sellyoursaasRemoteAction('undeploy', $object);
				if ($result <= 0)
				{
					$error++;
					$this->error=$sellyoursaasutils->error;
					$this->errors=$sellyoursaasutils->errors;
				}

				// Finish deployall

				$comment = 'Close after click on undeploy from contract card';

				// Activate all lines
				if (! $error)
				{
					dol_syslog("Activate all lines");

					$result = $object->closeAll($user, 1, $comment);
					if ($result <= 0)
					{
						$error++;
						setEventMessages($object->error, $object->errors, 'errors');
					}
				}

				// End of deployment is now OK / Complete
				if (! $error)
				{
					$object->array_options['options_deployment_status'] = 'undeployed';
					$object->array_options['options_undeployment_date'] = dol_now('tzserver');
					$object->array_options['options_undeployment_ip'] = $_SERVER['REMOTE_ADDR'];

					$result = $object->update($user);
					if ($result < 0)
					{
						// We ignore errors. This should not happen in real life.
						//setEventMessages($contract->error, $contract->errors, 'errors');
					}
				}
			}
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
        global $langs, $user;

        if ($user->rights->sellyoursaas->read)
        {
        	$langs->load("sellyoursaas@sellyoursaas");
	        $search_boxvalue = $parameters['search_boxvalue'];

	        $this->results['searchintocontract']=$parameters['arrayresult']['searchintocontract'];
	        $this->results['searchintocontract']['position']=22;

	        $this->results['searchintodolicloud']=array('position'=>23, 'img'=>'object_generic', 'label'=>$langs->trans("SearchIntoOldDoliCloudInstances", $search_boxvalue), 'text'=>img_picto('','object_generic').' '.$langs->trans("OldDoliCloudInstances", $search_boxvalue), 'url'=>dol_buildpath('/sellyoursaas/backoffice/dolicloud_list.php',1).'?search_multi='.urlencode($search_boxvalue));
        }

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
    	global $conf, $langs, $user;
    	global $object;

    	$langs->load("sellyoursaas@sellyoursaas");

    	$result='';

    	if ($user->rights->sellyoursaas->read)
    	{
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
    	}

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


