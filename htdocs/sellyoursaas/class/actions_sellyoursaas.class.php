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
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture-rec.class.php';


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
     *    @param	array			$parameters		Array of parameters
     *    @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     *    @param    string			$action      	'add', 'update', 'view'
     *    @return   int         					<0 if KO,
     *                              				=0 if OK but we want to process standard actions too,
     *                              				>0 if OK and we want to replace standard actions.
     */
    function getNomUrl($parameters,&$object,&$action)
    {
    	global $db,$langs,$conf,$user;

    	if ($object->element == 'societe')
    	{
	    	// Dashboard
	    	if ($user->admin && ! empty($object->array_options['options_dolicloud']))
	    	{
	    		$url = '';
	    		if ($object->array_options['options_dolicloud'] == 'yesv1')
		    	{
		    		$url='https://www.on.dolicloud.com/signIn/index?email='.$object->email;	// Note that password may have change and not being the one of dolibarr admin user
		    	}
		    	if ($object->array_options['options_dolicloud'] == 'yesv2')
		    	{
		    		$dol_login_hash=dol_hash($conf->global->SELLYOURSAAS_KEYFORHASH.$object->email.dol_print_date(dol_now(),'dayrfc'));	// hash is valid one hour
		    		$url=$conf->global->SELLYOURSAAS_ACCOUNT_URL.'?mode=logout_dashboard&username='.$object->email.'&password=&login_hash='.$dol_login_hash;
		    	}

		    	if ($url)
		    	{
			    	$this->resprints = ' - <!-- Added by getNomUrl hook of SellYourSaas -->';
			    	if ($object->array_options['options_dolicloud'] == 'yesv1') $this->resprints .= 'V1 - ';
			    	if ($object->array_options['options_dolicloud'] == 'yesv2') $this->resprints .= 'V2 - ';
		    		$this->resprints .= '<a href="'.$url.'" target="_myaccount" alt="'.$langs->trans("Dashboard").'"><span class="fa fa-desktop"></span> '.$conf->global->SELLYOURSAAS_NAME.' '.$langs->trans("Dashboard").'</a>';
		    	}
	    	}
    	}

    	return 0;
    }


    /**
     *    Execute action
     *
     *    @param	array	$parameters				Array of parameters
     *    @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     *    @param    string	$action      			'add', 'update', 'view'
     *    @return   int         					<0 if KO,
     *                              				=0 if OK but we want to process standard actions too,
     *                              				>0 if OK and we want to replace standard actions.
     */
    function addMoreActionsButtons($parameters,&$object,&$action)
    {
    	global $db,$langs,$conf,$user;

    	dol_syslog(get_class($this).'::addMoreActionsButtons action='.$action);
    	$langs->load("sellyoursaas@sellyoursaas");

    	if (in_array($parameters['currentcontext'], array('contractcard'))
    		&& ! empty($object->array_options['options_deployment_status']))		// do something only for the context 'somecontext1' or 'somecontext2'
    	{
	    	if ($user->rights->sellyoursaas->write)
	    	{
	    		if (in_array($object->array_options['options_deployment_status'], array('processing', 'undeployed')))
	    		{
	    			$alt = $langs->trans("SellYourSaasSubDomains").' '.$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES;
	    			$alt.= '<br>'.$langs->trans("SellYourSaasSubDomainsIP").' '.$conf->global->SELLYOURSAAS_SUB_DOMAIN_IP;

	    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=deploy" title="'.dol_escape_htmltag($alt).'">' . $langs->trans('Redeploy') . '</a>';
	    		}
	    		else
	    		{
	    			print '<a class="butActionRefused" href="#" title="'.$langs->trans("ContractMustHaveStatusProcessingOrUndeployed").'">' . $langs->trans('Redeploy') . '</a>';
	    		}

	    		if (in_array($object->array_options['options_deployment_status'], array('done')))
	    		{
	    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=refresh">' . $langs->trans('RefreshRemoteData') . '</a>';

	    			if (empty($object->array_options['options_fileauthorizekey']))
	    			{
	    				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=recreateauthorizedkeys">' . $langs->trans('RecreateAuthorizedKey') . '</a>';
	    			}

	    			if (empty($object->array_options['options_filelock']))
	    			{
		    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=recreatelock">' . $langs->trans('RecreateLock') . '</a>';
	    			}
	    			else
	    			{
		    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=deletelock">' . $langs->trans('SellYourSaasRemoveLock') . '</a>';
		    		}
	    		}
	    		else
	    		{
	    			print '<a class="butActionRefused" href="#" title="'.$langs->trans("ContractMustHaveStatusDone").'">' . $langs->trans('RefreshRemoteData') . '</a>';
	    		}

	    		if (in_array($object->array_options['options_deployment_status'], array('done')))
	    		{
	    			print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=undeploy">' . $langs->trans('Undeploy') . '</a>';
	    		}
	    		else
	    		{
	    			print '<a class="butActionRefused" href="#" title="'.$langs->trans("ContractMustHaveStatusDone").'">' . $langs->trans('Undeploy') . '</a>';
	    		}
	    	}
    	}

    	return 0;
    }



    /**
     *    Execute action
     *
     *    @param	array			$parameters		Array of parameters
     *    @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     *    @param    string			$action      	'add', 'update', 'view'
     *    @return   int         					<0 if KO,
     *                              				=0 if OK but we want to process standard actions too,
     *                              				>0 if OK and we want to replace standard actions.
     */
    function doActions($parameters,&$object,&$action)
    {
        global $db,$langs,$conf,$user;

        dol_syslog(get_class($this).'::doActions action='.$action);
        $langs->load("sellyoursaas@sellyoursaas");

		/*
        if (is_object($object) && (get_class($object) == 'Contrat') && is_object($object->thirdparty))
        {
        	$object->email = $object->thirdparty->email;
        }*/


        if (in_array($parameters['currentcontext'], array('contractlist')))
        {
        	global $fieldstosearchall;

        	$fieldstosearchall['s.email']="ThirdPartyEmail";
        }

        if (in_array($parameters['currentcontext'], array('contractcard')))
        {
			if ($action == 'deploy')
			{
				$db->begin();

				// SAME CODE THAN INTO MYACCOUNT INDEX.PHP

				// Disable template invoice
				$object->fetchObjectLinked();

				$foundtemplate=0;
				$freqlabel = array('d'=>$langs->trans('Day'), 'm'=>$langs->trans('Month'), 'y'=>$langs->trans('Year'));
				if (is_array($object->linkedObjects['facturerec']) && count($object->linkedObjects['facturerec']) > 0)
				{
					function cmp($a, $b)
					{
						return strcmp($a->date, $b->date);
					}
					usort($object->linkedObjects['facturerec'], "cmp");

					//var_dump($object->linkedObjects['facture']);
					//dol_sort_array($object->linkedObjects['facture'], 'date');
					foreach($object->linkedObjects['facturerec'] as $idinvoice => $invoice)
					{
						if ($invoice->suspended == FactureRec::STATUS_SUSPENDED)
						{
							$result = $invoice->setStatut(FactureRec::STATUS_NOTSUSPENDED);
							if ($result <= 0)
							{
								$error++;
								$this->error=$invoice->error;
								$this->errors=$invoice->errors;
							}
						}
					}
				}

				if (! $error)
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
				}

				// Finish deployall

				$comment = 'Activation after click on redeploy from contract card';

				// Activate all lines
				if (! $error)
				{
					dol_syslog("Activate all lines - doActions deploy");

					$object->context['deployallwasjustdone']=1;		// Add a key so trigger into activateAll will know we have just made a "deployall"

					$result = $object->activateAll($user, dol_now(), 1, $comment);
					if ($result <= 0)
					{
						$error++;
						$this->error=$object->error;
						$this->errors=$object->errors;
					}
				}

				// End of deployment is now OK / Complete
				if (! $error)
				{
					$object->array_options['options_deployment_status'] = 'done';
					$object->array_options['options_deployment_date_end'] = dol_now();
					$object->array_options['options_undeployment_date'] = '';
					$object->array_options['options_undeployment_ip'] = '';

					$result = $object->update($user);
					if ($result < 0)
					{
						// We ignore errors. This should not happen in real life.
						//setEventMessages($contract->error, $contract->errors, 'errors');
					}
					else
					{
						setEventMessages($langs->trans("InstanceWasDeployed"), null, 'mesgs');
					}
				}

				if (! $error)
				{
					$db->commit();
				}
				else
				{
					$db->rollback();
				}


				$urlto=preg_replace('/action=[a-z]+/', '', $_SERVER['REQUEST_URI']);
				if ($urlto)
				{
					dol_syslog("Redirect to page urlto=".$urlto." to avoid to do action twice if we do back");
					header("Location: ".$urlto);
					exit;
				}
			}

			if ($action == 'undeploy')
			{
				$db->begin();

				// SAME CODE THAN INTO MYACCOUNT INDEX.PHP

				// Disable template invoice
				$object->fetchObjectLinked();

				$foundtemplate=0;
				$freqlabel = array('d'=>$langs->trans('Day'), 'm'=>$langs->trans('Month'), 'y'=>$langs->trans('Year'));
				if (is_array($object->linkedObjects['facturerec']) && count($object->linkedObjects['facturerec']) > 0)
				{
					function cmp($a, $b)
					{
						return strcmp($a->date, $b->date);
					}
					usort($object->linkedObjects['facturerec'], "cmp");

					//var_dump($object->linkedObjects['facture']);
					//dol_sort_array($object->linkedObjects['facture'], 'date');
					foreach($object->linkedObjects['facturerec'] as $idinvoice => $invoice)
					{
						if ($invoice->suspended == FactureRec::STATUS_NOTSUSPENDED)
						{
							$result = $invoice->setStatut(FactureRec::STATUS_SUSPENDED);
							if ($result <= 0)
							{
								$error++;
								$this->error=$invoice->error;
								$this->errors=$invoice->errors;
							}
						}
					}
				}

				if (! $error)
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
				}

				// Finish deployall

				$comment = 'Close after click on undeploy from contract card';

				// Unactivate all lines
				if (! $error)
				{
					dol_syslog("Unactivate all lines - doActions undeploy");

					$result = $object->closeAll($user, 1, $comment);
					if ($result <= 0)
					{
						$error++;
						$this->error=$object->error;
						$this->errors=$object->errors;
					}
				}

				// End of undeployment is now OK / Complete
				if (! $error)
				{
					$object->array_options['options_deployment_status'] = 'undeployed';
					$object->array_options['options_undeployment_date'] = dol_now();
					$object->array_options['options_undeployment_ip'] = $_SERVER['REMOTE_ADDR'];

					$result = $object->update($user);
					if ($result < 0)
					{
						// We ignore errors. This should not happen in real life.
						//setEventMessages($contract->error, $contract->errors, 'errors');
					}
					else
					{
						setEventMessages($langs->trans("InstanceWasUndeployed"), null, 'mesgs');
						//setEventMessages($langs->trans("InstanceWasUndeployedToConfirm"), null, 'mesgs');
					}
				}

				if (! $error)
				{
					$db->commit();
				}
				else
				{
					$db->rollback();
				}

				$urlto=preg_replace('/action=[a-z]+/', '', $_SERVER['REQUEST_URI']);
				if ($urlto)
				{
					dol_syslog("Redirect to page urlto=".$urlto." to avoid to do action twice if we do back");
					header("Location: ".$urlto);
					exit;
				}
			}

			if (empty(GETPOST('instanceoldid','int')) && in_array($action, array('refresh','recreateauthorizedkeys','deletelock','recreatelock')))
			{
				dol_include_once('sellyoursaas/class/sellyoursaasutils.class.php');
				$sellyoursaasutils = new SellYourSaasUtils($db);
				$result = $sellyoursaasutils->sellyoursaasRemoteAction($action, $object);
				if ($result <= 0)
				{
					$error++;
					$this->error=$sellyoursaasutils->error;
					$this->errors=$sellyoursaasutils->errors;
				}
				else
				{
					if ($action == 'refresh') setEventMessages($langs->trans("ResourceComputed"), null, 'mesgs');
					if ($action == 'recreateauthorizedkeys') setEventMessages($langs->trans("FileCreated"), null, 'mesgs');
					if ($action == 'recreatelock') setEventMessages($langs->trans("FileCreated"), null, 'mesgs');
					if ($action == 'deletelock') setEventMessages($langs->trans("FilesDeleted"), null, 'mesgs');
				}
			}

        }

        dol_syslog(get_class($this).'::doActions end');
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
     * @param	array			$parameters		Array of parameters
     * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @return	int								1=Replace standard code, 0=Continue standard code
     */
    function moreHtmlStatus($parameters)
    {
    	global $conf, $langs, $user;
    	global $object;

    	if ($parameters['currentcontext'] == 'contractcard')
    	{
    		if (! empty($object->array_options['options_deployment_status']))
    		{
    			dol_include_once('sellyoursaas/lib/sellyoursaas.lib.php');
				$ret = '<br><br><div class="right bold">';
				$ispaid = sellyoursaasIsPaidInstance($object);
				if ($ispaid) $ret .= '<span class="badge" style="font-size: 1em; background-color: green">'.$langs->trans("PayedMode").'</span>';
				else $ret .= '<span class="badge" style="font-size: 1em">'.$langs->trans("TrialMode").'</span>';
				$ret .= '</div>';

				$this->resprints = $ret;
    		}
    	}

    	return 0;
    }


    /**
     * Complete search forms
     *
     * @param	array			$parameters		Array of parameters
     * @return	int								1=Replace standard code, 0=Continue standard code
     */
    function printEmail($parameters)
    {
    	global $conf, $langs, $user;
		global $object;
		//var_dump($parameters['currentcontext']);

		if (in_array($parameters['currentcontext'], array('thirdpartycard','thirdpartycontact','thirdpartycomm','thirdpartyticket','thirdpartynote','thirdpartydocument','contactthirdparty','projectthirdparty','consumptionthirdparty','thirdpartybancard','thirdpartymargins','ticketsuplist','thirdpartynotification','agendathirdparty')))
    	{
    		if ($object->element == 'societe')
    		{
    			// Dashboard
    			if ($user->admin && ! empty($object->array_options['options_dolicloud']))
    			{
    				$url='';
    				if ($object->array_options['options_dolicloud'] == 'yesv1')
    				{
    					$url='https://www.on.dolicloud.com/signIn/index?email='.$object->email;	// Note that password may have change and not being the one of dolibarr admin user
    				}
    				if ($object->array_options['options_dolicloud'] == 'yesv2')
    				{
    					$dol_login_hash=dol_hash($conf->global->SELLYOURSAAS_KEYFORHASH.$object->email.dol_print_date(dol_now(),'dayrfc'));	// hash is valid one hour
    					$url=$conf->global->SELLYOURSAAS_ACCOUNT_URL.'?mode=logout_dashboard&username='.$object->email.'&password=&login_hash='.$dol_login_hash;
    				}

					if ($url)
					{
						$this->resprints = '<!-- Added by getNomUrl hook of SellYourSaas --><br><div class="clearboth">';
						if ($object->array_options['options_dolicloud'] == 'yesv1') $this->resprints .= 'V1 - ';
						if ($object->array_options['options_dolicloud'] == 'yesv2') $this->resprints .= 'V2 - ';
    					$this->resprints .= '<a href="'.$url.'" target="_myaccount" alt="'.$langs->trans("Dashboard").'"><span class="fa fa-desktop"></span> '.$conf->global->SELLYOURSAAS_NAME.' '.$langs->trans("Dashboard").'</a></div>';
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


