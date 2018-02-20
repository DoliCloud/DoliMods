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
        	case 'LINECONTRACT_ACTIVATE':
        		$remoteaction = 'unsuspend';
        		break;
        	case 'LINECONTRACT_CLOSE':
	    		$remoteaction = 'suspend';
	    		break;
        }

    	if ($remoteaction)
    	{
    		$producttmp = new Product($this->db);
    		$producttmp->fetch($object->fk_product);

    		if (empty($object->context['fromdolucloudcustomerv1']) &&
    			($producttmp->array_options['options_app_or_option'] == 'app' || $producttmp->array_options['options_app_or_option'] == 'option'))
    		{
	    		dol_syslog("Suspend/unsuspend instance remoteaction=".$remoteaction);

	    		include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
	    		dol_include_once('/sellyoursaas/lib/sellyoursaas.lib.php');
	    		$contract = new Contrat($this->db);
				$contract->fetch($object->fk_contrat);

				$generatedunixlogin=$contract->array_options['options_username_os'];
				$generatedunixpassword=$contract->array_options['options_password_os'];
				$tmp=preg_replace('/\./', $contract->ref_customer, 2);
				$sldAndSubdomain=$tmp[0];
				$domainname=$tmp[1];
				$generateddbname=$contract->array_options['options_username_db'];
				$generateddbpassword=$contract->array_options['options_password_db'];

				// Remote action : unsuspend
				$commandurl = $generatedunixlogin.'&'.$generatedunixpassword.'&'.$sldAndSubdomain.'&'.$domainname;
				$commandurl.= '&'.$generateddbname.'&'.$generateddbusername.'&'.$generateddbpassword;
				$commandurl.= '&'.$tmppackage->srcconffile1.'&'.$tmppackage->targetconffile1.'&'.$tmppackage->datafile1;
				$commandurl.= '&'.$tmppackage->srcfile1.'&'.$tmppackage->targetsrcfile1.'&'.$tmppackage->srcfile2.'&'.$tmppackage->targetsrcfile2.'&'.$tmppackage->srcfile3.'&'.$tmppackage->targetsrcfile3;
				$commandurl.= '&'.$tmppackage->srccronfile.'&'.$targetdir;

				$outputfile = $conf->sellyoursaas->dir_temp.'/action_deploy_undeploy-'.$remoteaction.'-'.dol_getmypid().'.out';

				$serverdeployement = getRemoveServerDeploymentIp();

				$urltoget='http://'.$serverdeployement.':8080/'.$remoteaction.'/'.urlencode($commandurl);
				include DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
				$retarray = getURLContent($urltoget);

				if ($retarray['curl_error_no'] != '')
				{
					$error++;
					$errormessages[] = $retarray['curl_error_msg'];
				}
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
