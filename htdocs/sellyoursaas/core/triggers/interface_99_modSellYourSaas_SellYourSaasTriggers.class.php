<?php
/* Copyright (C) 2018 Laurent Destailleur <eldy@users.sourceforge.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    core/triggers/interface_99_modSellYourSaas_SellYourSaasTriggers.class.php
 * \ingroup sellyoursaas
 * \brief   Trigger for sellyoursaas module.
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for SellYourSaas module
 */
class InterfaceSellYourSaasTriggers extends DolibarrTriggers
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "sellyoursaas";
		$this->description = "SellYourSaas triggers.";
		// 'development', 'experimental', 'dolibarr' or version
		$this->version = 1.0;
		$this->picto = 'sellyoursaas@sellyoursaas';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc()
	{
		return $this->description;
	}


	/**
	 * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param string 		$action 	Event action code
	 * @param CommonObject 	$object 	Object
	 * @param User 			$user 		Object user
	 * @param Translate 	$langs 		Object langs
	 * @param Conf 			$conf 		Object conf
	 * @return int              		<0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
        if (empty($conf->sellyoursaas->enabled)) return 0;     // Module not active, we do nothing

	    // Put here code you want to execute when a Dolibarr business events occurs.
		// Data and type of action are stored into $object and $action

        $error = 0;
        $remoteaction = '';

        switch ($action) {
        	case 'CATEGORY_LINK':
				// Test if this is a partner. If yes, send an email
        		include_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
        		if ($object->type === Categorie::TYPE_SUPPLIER || Categorie::$MAP_ID_TO_CODE[$object->type] == Categorie::TYPE_SUPPLIER)
        		{
        			// We link a supplier categorie to a thirdparty
        			if ($object->id == $conf->global->SELLYOURSAAS_DEFAULT_RESELLER_CATEG)
        			{
        				$reseller = $object->context['linkto'];

						// $object->context['linkto'] is Societe object
        				if (empty($reseller->name_alias))
        				{
        					$this->errors[] = $langs->trans("CompanyAliasIsRequiredWhenWeSetResellerTag");
        					return -1;
        				}

        				// If password not set yet, we set it
        				if (empty($reseller->array_options['options_password']))
        				{
        					$password = $reseller->name_alias;

        					$reseller->oldcopy = dol_clone($reseller);

        					$reseller->array_options['options_password']=$password;

        					$reseller->update($reseller->id, $user, 0);
        				}

        				// No email, can be done manually.
        				/*
        				// Send deployment email
        				include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
        				include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
        				$formmail=new FormMail($db);

        				$arraydefaultmessage=$formmail->getEMailTemplate($db, 'thirdparty', $user, $langs, 0, 1, 'ChannelPartnerCreated');

        				$substitutionarray=getCommonSubstitutionArray($langs, 0, null, $contract);
        				complete_substitutions_array($substitutionarray, $langs, $contract);

        				$subject = make_substitutions($arraydefaultmessage['topic'], $substitutionarray, $langs);
        				$msg     = make_substitutions($arraydefaultmessage['content'], $substitutionarray, $langs);
        				$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
        				$to = $contract->thirdparty->email;

        				$cmail = new CMailFile($subject, $to, $from, $msg, array(), array(), array(), '', '', 0, 1);
        				$result = $cmail->sendfile();
        				if (! $result)
        				{
        					$error++;
        					$this->errors += $cmail->errors;
        				}
						*/
        			}
        		}
				break;
        	case 'LINECONTRACT_ACTIVATE':
        		$remoteaction = 'unsuspend';
        		break;
        	case 'LINECONTRACT_CLOSE':
	    		$remoteaction = 'suspend';
	    		break;
        	case 'CONTRACT_MODIFY':
        		/*var_dump($object->oldcopy->array_options['options_date_endfreeperiod']);
        		var_dump($object->array_options['options_date_endfreeperiod']);
        		var_dump($object->lines);*/
        		if ($object->oldcopy->array_options['options_date_endfreeperiod'] != $object->array_options['options_date_endfreeperiod'])
        		{
        			// Check there is no recurring invoice. If yes, we refuse to change this.
        			$object->fetchObjectLinked();
        			//var_dump($object->linkedObjects);
        			if (is_array($object->linkedObjects['facturerec']))
        			{
        				if (count($object->linkedObjects['facturerec']) > 0)
        				{
        					$this->errors[]="ATemplateInvoiceExistsNoWayToChangeTrial";
        					return -1;
        				}
        			}

	        		foreach($object->lines as $line)
	        		{
	        			if ($line->date_end < $object->array_options['options_date_endfreeperiod'])
	        			{
	        				$line->date_end = $object->array_options['options_date_endfreeperiod'];
	        				$line->date_fin_validite = $object->array_options['options_date_endfreeperiod'];
	        				$line->update($user);
	        				//exit;
	        				break;	// No need to loop on all, there is also trigger that update all other when we update one
	        			}
	        		}
        		}
        		break;

        }

    	if ($remoteaction)
    	{
    		$producttmp = new Product($this->db);
    		$producttmp->fetch($object->fk_product);

    		if (empty($object->context['fromdolicloudcustomerv1']) &&
    			($producttmp->array_options['options_app_or_option'] == 'app' || $producttmp->array_options['options_app_or_option'] == 'option'))
    		{
	    		dol_syslog("Suspend/unsuspend instance remoteaction=".$remoteaction);

	    		include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
	    		dol_include_once('/sellyoursaas/lib/sellyoursaas.lib.php');
	    		dol_include_once('/sellyoursaas/class/packages.class.php');
	    		$contract = new Contrat($this->db);
				$contract->fetch($object->fk_contrat);

				$targetdir = $conf->global->DOLICLOUD_INSTANCES_PATH;

				$generatedunixlogin=$contract->array_options['options_username_os'];
				$generatedunixpassword=$contract->array_options['options_password_os'];
				$tmp=explode('.', $contract->ref_customer, 2);
				$sldAndSubdomain=$tmp[0];
				$domainname=$tmp[1];
				$generateddbname=$contract->array_options['options_database_db'];
				$generateddbport=$contract->array_options['options_port_db'];
				$generateddbusername=$contract->array_options['options_username_db'];
				$generateddbpassword=$contract->array_options['options_password_db'];

				$tmppackage = new Packages($this->db);

				// Remote action : unsuspend
				$commandurl = $generatedunixlogin.'&'.$generatedunixpassword.'&'.$sldAndSubdomain.'&'.$domainname;
				$commandurl.= '&'.$generateddbname.'&'.$generateddbport.'&'.$generateddbusername.'&'.$generateddbpassword;
				$commandurl.= '&'.$tmppackage->srcconffile1.'&'.$tmppackage->targetconffile1.'&'.$tmppackage->datafile1;
				$commandurl.= '&'.$tmppackage->srcfile1.'&'.$tmppackage->targetsrcfile1.'&'.$tmppackage->srcfile2.'&'.$tmppackage->targetsrcfile2.'&'.$tmppackage->srcfile3.'&'.$tmppackage->targetsrcfile3;
				$commandurl.= '&'.$tmppackage->srccronfile.'&'.$targetdir;

				$outputfile = $conf->sellyoursaas->dir_temp.'/action_deploy_undeploy-'.$remoteaction.'-'.dol_getmypid().'.out';

				$serverdeployement = getRemoveServerDeploymentIp();

				$urltoget='http://'.$serverdeployement.':8080/'.$remoteaction.'?'.urlencode($commandurl);
				include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
				$retarray = getURLContent($urltoget);

				if ($retarray['curl_error_no'] != '')
				{
					$error++;
					$errormessages[] = $retarray['curl_error_msg'];
				}

				// No email, can be done manually.
    		}
    	}

    	if ($error)
    	{
    		$this->errors=$errormessages;
    		return -1;
    	}

		return 0;
	}
}
