<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *  \file       sellyoursaas/class/sellyoursaasutils.class.php
 *  \ingroup    sellyoursaas
 *  \brief      Class with utilities
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
dol_include_once('sellyoursaas/lib/sellyoursaas.lib.php');


/**
 *	Class with cron tasks of SellYourSaas module
 */
class SellYourSaasUtils
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     * Action executed by scheduler for job SellYourSaasAlertSoftEndTrial
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doAlertSoftEndTrial()
    {
    	global $conf, $langs, $user;

    	$mode = 'test';

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doAlertSoftEndTrial.log';

    	$this->output = '';
    	$this->error='';

    	$error = 0;
    	$now = dol_now();

    	$delayindaysshort = $conf->global->SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT;
    	$delayindayshard = $conf->global->SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT;
    	if ($delayindaysshort <= 0 || $delayindayshard <= 0)
    	{
    		$this->error='BadValueForDelayBeforeTrialEndForAlert';
    		return -1;
    	}
    	dol_syslog(__METHOD__." we send email warning ".$delayindays." days before end of trial", LOG_DEBUG);

    	$sql = 'SELECT c.rowid, c.ref_customer, cd.rowid as lid';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'contrat as c, '.MAIN_DB_PREFIX.'contratdet as cd, '.MAIN_DB_PREFIX.'contrat_extrafields as ce';
    	$sql.= ' WHERE cd.fk_contrat = c.rowid AND ce.fk_object = c.rowid';
    	$sql.= " AND ce.deployment_status = 'done'";
    	//$sql.= " AND cd.date_fin_validite < '".$this->db->idate(dol_time_plus_duree($now, abs($delayindaysshort), 'd'))."'";
    	//$sql.= " AND cd.date_fin_validite > '".$this->db->idate(dol_time_plus_duree($now, abs($delayindayshard), 'd'))."'";
    	$sql.= " AND date_format(cd.date_fin_validite, '%Y-%m-%d') = date_format('".$this->db->idate(dol_time_plus_duree($now, abs($delayindaysshort), 'd'))."', '%Y-%m-%d')";
    	$sql.= " AND cd.statut = 4";
		//print $sql;

    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);

    		$contractprocessed = array();

    		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
    		include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
    		$formmail=new FormMail($this->db);

    		$i=0;
    		while ($i < $num)
    		{
    			$obj = $this->db->fetch_object($resql);
    			if ($obj)
    			{
    				if (! empty($contractprocessed[$object->id])) continue;

    				// Test if this is a paid or not instance
    				$object = new Contrat($this->db);
    				$object->fetch($obj->rowid);
    				$object->fetch_thirdparty();

    				$outputlangs = new Translate('', $conf);
    				$outputlangs->setDefaultLang($object->thirdparty->default_lang);

    				$arraydefaultmessage=$formmail->getEMailTemplate($this->db, 'contract', $user, $outputlangs, 0, 1, 'GentleTrialExpiringReminder');

    				$ispaid = sellyoursaasIsPaidInstance($object);
    				if ($mode == 'test' && $ispaid) continue;											// Discard if this is a paid instance when we are in test mode
    				//if ($mode == 'paid' && ! $ispaid) continue;											// Discard if this is a test instance when we are in paid mode

    				// Suspend instance
    				$expirationdate = sellyoursaasGetExpirationDate($object);

    				if ($expirationdate && $expirationdate < dol_time_plus_duree($now, abs($delayindaysshort), 'd'))
    				{
    					$substitutionarray=getCommonSubstitutionArray($outputlangs, 0, null, $object);
    					$substitutionarray['__SELLYOURSAAS_EXPIRY_DATE__']=dol_print_date($expirationdate, 'day', $outputlangs, 'tzserver');
    					complete_substitutions_array($substitutionarray, $outputlangs, $object);

    					//$object->array_options['options_deployment_status'] = 'suspended';
    					$subject = make_substitutions($arraydefaultmessage['topic'], $substitutionarray);
    					$msg     = make_substitutions($arraydefaultmessage['content'], $substitutionarray);
    					$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
    					$to = $object->thirdparty->email;

    					$cmail = new CMailFile($subject, $to, $from, $msg, array(), array(), array(), '', '', 0, 1);
    					$result = $cmail->sendfile();
    					if (! $result)
    					{
    						$error++;
    						$this->error = $cmail->error;
    						$this->errors = $cmail->errors;
    					}

    					$contractprocessed[$object->id]=$object->ref;
    				}
    			}
    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output = count($contractprocessed).' email(s) sent'.(count($contractprocessed)>0 ? ' '.join(',', $contractprocessed) : '');

    	return ($error ? 1: 0);
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doSuspendExpiredTestInstances()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doSuspendExpiredTestInstances.log';

    	dol_syslog(__METHOD__, LOG_DEBUG);
    	return $this->doSuspendInstances('test');
    }

    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doSuspendExpiredRealInstances()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doSuspendExpiredRealInstances.log';

    	dol_syslog(__METHOD__, LOG_DEBUG);
    	return $this->doSuspendInstances('paid');
    }


   	/**
   	 * Called by doSuspendExpiredTestInstances or doSuspendExpiredRealInstances
   	 *
   	 * @param	string	$mode		'test' or 'paid'
   	 * @return	int					0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
   	 */
   	private function doSuspendInstances($mode)
   	{
    	global $conf, $langs, $user;

    	if ($mode != 'test' && $mode != 'paid')
    	{
    		$this->error = 'Function doSuspendInstances called with bad value for parameter $mode';
    		return -1;
    	}

    	$this->output = '';
    	$this->error='';

    	$error = 0;
    	$now = dol_now();

    	$sql = 'SELECT c.rowid, c.ref_customer, cd.rowid as lid';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'contrat as c, '.MAIN_DB_PREFIX.'contratdet as cd, '.MAIN_DB_PREFIX.'contrat_extrafields as ce';
    	$sql.= ' WHERE cd.fk_contrat = c.rowid AND ce.fk_object = c.rowid';
    	$sql.= " AND ce.deployment_status = 'done'";
    	//$sql.= " AND cd.date_fin_validite < '".$this->db->idate(dol_time_plus_duree($now, 1, 'd'))."'";
    	$sql.= " AND cd.date_fin_validite < '".$this->db->idate($now)."'";
    	$sql.= " AND cd.statut = 4";

    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);

    		$contractprocessed = array();

    		$i=0;
    		while ($i < $num)
    		{
				$obj = $this->db->fetch_object($resql);
				if ($obj)
				{
					if (! empty($contractprocessed[$object->id])) continue;

					// Test if this is a paid or not instance
					$object = new Contrat($this->db);
					$object->fetch($obj->rowid);

					$ispaid = sellyoursaasIsPaidInstance($object);
					if ($mode == 'test' && $ispaid) continue;											// Discard if this is a paid instance when we are in test mode
					if ($mode == 'paid' && ! $ispaid) continue;											// Discard if this is a test instance when we are in paid mode

					// Suspend instance
					$expirationdate = sellyoursaasGetExpirationDate($object);

					if ($expirationdate && $expirationdate < $now)
					{
						//$object->array_options['options_deployment_status'] = 'suspended';
						$result = $object->closeAll($user);			// This may execute trigger that make remote actions to suspend instance
						if ($result < 0)
						{
							$error++;
							$this->error = $object->error;
							$this->errors = $object->errors;
						}

						$contractprocessed[$object->id]=$object->ref;
					}
				}
    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output = count($contractprocessed).' contract(s) suspended'.(count($contractprocessed)>0 ? ' '.join(',', $contractprocessed) : '');

    	return ($error ? 1: 0);
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doUndeployOldSuspendedTestInstances()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doUndeployOldSuspendedTestInstances.log';

    	dol_syslog(__METHOD__, LOG_DEBUG);
    	return $this->doUndeployOldSuspendedInstances('test');
    }

    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doUndeployOldSuspendedRealInstances()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doUndeployOldSuspendedRealInstances.log';

    	dol_syslog(__METHOD__, LOG_DEBUG);
    	return $this->doUndeployOldSuspendedInstances('paid');
    }

    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
   	 * @param	string	$mode		'test' or 'paid'
     * @return	int					0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doUndeployOldSuspendedInstances($mode)
    {
    	global $conf, $langs, $user;

    	if ($mode != 'test' && $mode != 'paid')
    	{
    		$this->error = 'Function doUndeployOldSuspendedInstances called with bad value for parameter $mode';
    		return -1;
    	}

    	$this->output = '';
    	$this->error='';

    	$error = 0;
		$now = dol_now();

    	$delayindays = 9999999;
    	if ($mode == 'test') $delayindays = $conf->global->SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_TRIAL_UNDEPLOYMENT;
    	if ($mode == 'paid') $delayindays = $conf->global->SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_PAID_UNDEPLOYMENT;
		if ($delayindays <= 1)
		{
			$this->error='BadValueForDelayBeforeUndeploymentCheckSetup';
			return -1;
		}
    	dol_syslog(__METHOD__." we undeploy instances mode=".$mode." that are expired since more than ".$delayindays." days", LOG_DEBUG);

    	global $conf, $langs, $user;

    	$this->output = '';
    	$this->error='';

    	$sql = 'SELECT c.rowid, c.ref_customer, cd.rowid as lid';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'contrat as c, '.MAIN_DB_PREFIX.'contratdet as cd, '.MAIN_DB_PREFIX.'contrat_extrafields as ce';
    	$sql.= ' WHERE cd.fk_contrat = c.rowid AND ce.fk_object = c.rowid';
    	$sql.= " AND ce.deployment_status = 'done'";
    	$sql.= " AND cd.date_fin_validite < '".$this->db->idate(dol_time_plus_duree($now, -1 * abs($delayindays), 'd'))."'";
    	$sql.= " AND cd.statut = 5";

    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);

    		$contractprocessed = array();

    		$i=0;
    		while ($i < $num)
    		{
    			$obj = $this->db->fetch_object($resql);
    			if ($obj)
    			{
    				if (! empty($contractprocessed[$object->id])) continue;

    				// Test if this is a paid or not instance
    				$object = new Contrat($this->db);
    				$object->fetch($obj->rowid);

    				$ispaid = sellyoursaasIsPaidInstance($object);
    				if ($mode == 'test' && $ispaid) continue;										// Discard if this is a paid instance when we are in test mode
    				if ($mode == 'paid' && ! $ispaid) continue;										// Discard if this is a test instance when we are in paid mode

    				// Undeploy instance
    				$expirationdate = sellyoursaasGetExpirationDate($object);

    				if ($expirationdate && $expirationdate < ($now - (abs($delayindays)*24*3600)))
    				{
    					$result = $this->sellyoursaasRemoteAction('undeploy', $object);
    					if ($result <= 0)
    					{
    						$error++;
    						$this->error=$sellyoursaasutils->error;
    						$this->errors=$sellyoursaasutils->errors;
    					}
    					//$object->array_options['options_deployment_status'] = 'suspended';

    					$contractprocessed[$object->id]=$object->id;	// To avoid to make action twice on same contract
    				}
    			}
    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output = count($contractprocessed).' contract(s) undeployed'.(count($contractprocessed)>0 ? ' '.join(',', $contractprocessed) : '');

    	return ($error ? 1: 0);
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doTakePaymentPaypal()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doTakePaymentPaypal.log';

    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	// ...

    	return 0;
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doTakePaymentStripe()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doTakePaymentStripe.log';

    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	// ...

    	return 0;
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doAlertCreditCardExpiration()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doAlertCreditCardExpiration.log';

    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	// ...

    	return 0;
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doAlertPaypalExpiration()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doAlertPaypalExpiration.log';

    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	// ...

    	return 0;
    }






    /**
     * Make a remote action on a contract (deploy/undeploy/suspend/unsuspend/...)
     *
     * @param	string					$remoteaction	Remote action
     * @param 	Contrat|ContratLigne	$object			Object contract or contract line
     * @param	string					$appusername	App login
     * @param	string					$email			Initial email
     * @param	string					$password		Initial password
     * @return	int										<0 if KO, >0 if OK
     */
    function sellyoursaasRemoteAction($remoteaction, $object, $appusername='admin', $email='', $password='')
    {
    	global $conf, $user;

    	$error = 0;

    	$now = dol_now();

    	if (get_class($object) == 'Contrat')
    	{
    		$listoflines = $object->lines;
    	}
    	else
    	{
    		$listoflines = array($object);
    	}

    	dol_syslog("Remote action on instance remoteaction=".$remoteaction." was called");

    	foreach($listoflines as $tmpobject)
    	{
    		$producttmp = new Product($this->db);
    		$producttmp->fetch($tmpobject->fk_product);

    		if (empty($tmpobject->context['fromdolicloudcustomerv1']) &&
    			$remoteaction != 'refresh' &&
    			($producttmp->array_options['options_app_or_option'] == 'app' || $producttmp->array_options['options_app_or_option'] == 'option'))
    		{
    			include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
    			dol_include_once('/sellyoursaas/class/packages.class.php');

    			$contract = new Contrat($this->db);
    			$contract->fetch($tmpobject->fk_contrat);

    			$targetdir = $conf->global->DOLICLOUD_INSTANCES_PATH;

    			$generatedunixlogin=$contract->array_options['options_username_os'];
    			$generatedunixpassword=$contract->array_options['options_password_os'];
    			$tmp=explode('.', $contract->ref_customer, 2);
    			$sldAndSubdomain=$tmp[0];
    			$domainname=$tmp[1];
    			$generateddbname=$contract->array_options['options_database_db'];
    			$generateddbport = ($contract->array_options['options_port_db']?$contract->array_options['options_port_db']:3306);
    			$generateddbusername=$contract->array_options['options_username_db'];
    			$generateddbpassword=$contract->array_options['options_password_db'];
    			$generateddbprefix=($contract->array_options['options_prefix_db']?$contract->array_options['options_prefix_db']:'llx_');

    			// Is it a product linked to a package ?
    			$tmppackage = new Packages($this->db);
    			if (! empty($producttmp->array_options['options_package']))
    			{
    				$tmppackage->fetch($producttmp->array_options['options_package']);
    			}

    			// Replace __INSTANCEDIR__, __INSTALLHOURS__, __INSTALLMINUTES__, __OSUSERNAME__, __APPUNIQUEKEY__, __APPDOMAIN__, ...
    			$substitarray=array(
    			'__INSTANCEDIR__'=>$targetdir.'/'.$generatedunixlogin.'/'.$generateddbname,
    			'__INSTANCEDBPREFIX__'=>$generateddbprefix,
    			'__DOL_DATA_ROOT__'=>DOL_DATA_ROOT,
    			'__INSTALLHOURS__'=>dol_print_date($now, '%H'),
    			'__INSTALLMINUTES__'=>dol_print_date($now, '%M'),
    			'__OSHOSTNAME__'=>$generatedunixhostname,
    			'__OSUSERNAME__'=>$generatedunixlogin,
    			'__OSPASSWORD__'=>$generatedunixpassword,
    			'__DBHOSTNAME__'=>$generateddbhostname,
    			'__DBNAME__'=>$generateddbname,
    			'__DBPORT__'=>$generateddbport,
    			'__DBUSER__'=>$generateddbusername,
    			'__DBPASSWORD__'=>$generateddbpassword,
    			'__PACKAGEREF__'=> $tmppackage->ref,
    			'__PACKAGENAME__'=> $tmppackage->label,
    			'__APPUSERNAME__'=>$appusername,
    			'__APPEMAIL__'=>$email,
    			'__APPPASSWORD__'=>$password,
    			'__APPUNIQUEKEY__'=>$generateduniquekey,
    			'__APPDOMAIN__'=>$sldAndSubdomain.'.'.$domainname
    			);

    			$tmppackage->srcconffile1 = '/tmp/conf.php.'.$sldAndSubdomain.'.'.$domainname.'.tmp';
    			$tmppackage->srccronfile = '/tmp/cron.'.$sldAndSubdomain.'.'.$domainname.'.tmp';

    			$conffile = make_substitutions($tmppackage->conffile1, $substitarray);
    			$cronfile = make_substitutions($tmppackage->crontoadd, $substitarray);

    			$tmppackage->targetconffile1 = make_substitutions($tmppackage->targetconffile1, $substitarray);
    			$tmppackage->datafile1 = make_substitutions($tmppackage->datafile1, $substitarray);
    			$tmppackage->srcfile1 = make_substitutions($tmppackage->srcfile1, $substitarray);
    			$tmppackage->srcfile2 = make_substitutions($tmppackage->srcfile2, $substitarray);
    			$tmppackage->srcfile3 = make_substitutions($tmppackage->srcfile3, $substitarray);
    			$tmppackage->targetsrcfile1 = make_substitutions($tmppackage->targetsrcfile1, $substitarray);
    			$tmppackage->targetsrcfile2 = make_substitutions($tmppackage->targetsrcfile2, $substitarray);
    			$tmppackage->targetsrcfile3 = make_substitutions($tmppackage->targetsrcfile3, $substitarray);

    			dol_syslog("Create conf file ".$tmppackage->srcconffile1);
    			file_put_contents($tmppackage->srcconffile1, $conffile);

    			dol_syslog("Create cron file ".$tmppackage->srccronfile1);
    			file_put_contents($tmppackage->srccronfile, $cronfile);

    			// Remote action : unsuspend
    			$commandurl = $generatedunixlogin.'&'.$generatedunixpassword.'&'.$sldAndSubdomain.'&'.$domainname;
    			$commandurl.= '&'.$generateddbname.'&'.$generateddbport.'&'.$generateddbusername.'&'.$generateddbpassword;
    			$commandurl.= '&'.$tmppackage->srcconffile1.'&'.$tmppackage->targetconffile1.'&'.$tmppackage->datafile1;
    			$commandurl.= '&'.$tmppackage->srcfile1.'&'.$tmppackage->targetsrcfile1.'&'.$tmppackage->srcfile2.'&'.$tmppackage->targetsrcfile2.'&'.$tmppackage->srcfile3.'&'.$tmppackage->targetsrcfile3;
    			$commandurl.= '&'.$tmppackage->srccronfile.'&'.$targetdir;

    			$outputfile = $conf->sellyoursaas->dir_temp.'/action-'.$remoteaction.'-'.dol_getmypid().'.out';

    			$serverdeployement = getRemoveServerDeploymentIp();

    			$urltoget='http://'.$serverdeployement.':8080/'.$remoteaction.'?'.urlencode($commandurl);
    			include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
    			$retarray = getURLContent($urltoget);

    			if ($retarray['curl_error_no'] != '' || $retarray['http_code'] != 200)
    			{
    				$error++;
    				if ($retarray['curl_error_no'] != '') $this->errors[] = $retarray['curl_error_msg'];
    				else $this->errors[] = $retarray['content'];
    			}

		    	// Execute personalized SQL requests
		    	if (! $error)
		    	{
		    		$sqltoexecute = make_substitutions($tmppackage->sqlafter, $substitarray);

		    		dol_syslog("Try to connect to instance database to execute personalized requests");

		    		//var_dump($generateddbhostname);	// fqn name dedicated to instance in dns
		    		//var_dump($serverdeployement);		// just ip of deployement server
		    		//$dbinstance = @getDoliDBInstance('mysqli', $generateddbhostname, $generateddbusername, $generateddbpassword, $generateddbname, $generateddbport);
		    		$dbinstance = @getDoliDBInstance('mysqli', $serverdeployement, $generateddbusername, $generateddbpassword, $generateddbname, $generateddbport);
		    		if (! $dbinstance || ! $dbinstance->connected)
		    		{
		    			$error++;
		    			$this->error = $dbinstance->error;
		    			$this->errors = $dbinstance->errors;

		    		}
		    		else
		    		{
		    			dol_syslog("Execute sql=".$sqltoexecute);
		    			$resql = $dbinstance->query($sqltoexecute);
		    		}
		    	}
    		}

    		if (empty($tmpobject->context['fromdolicloudcustomerv1']) &&
    			$remoteaction == 'refresh')
    		{
    			include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
    			dol_include_once('/sellyoursaas/class/packages.class.php');

    			if (! empty($producttmp->array_options['options_resource_formula']))
    			{
    				include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
    				dol_include_once('/sellyoursaas/class/packages.class.php');

    				$contract = new Contrat($this->db);
    				$contract->fetch($tmpobject->fk_contrat);

    				$targetdir = $conf->global->DOLICLOUD_INSTANCES_PATH;

    				$generatedunixlogin=$contract->array_options['options_username_os'];
    				$generatedunixpassword=$contract->array_options['options_password_os'];
    				$tmp=explode('.', $contract->ref_customer, 2);
    				$sldAndSubdomain=$tmp[0];
    				$domainname=$tmp[1];
    				$generateddbname=$contract->array_options['options_database_db'];
    				$generateddbport = ($contract->array_options['options_port_db']?$contract->array_options['options_port_db']:3306);
    				$generateddbusername=$contract->array_options['options_username_db'];
    				$generateddbpassword=$contract->array_options['options_password_db'];
    				$generateddbprefix=($contract->array_options['options_prefix_db']?$contract->array_options['options_prefix_db']:'llx_');

    				// Is it a product linked to a package ?
    				$tmppackage = new Packages($this->db);
    				if (! empty($producttmp->array_options['options_package']))
    				{
    					$tmppackage->fetch($producttmp->array_options['options_package']);
    				}

    				// Replace __INSTANCEDIR__, __INSTALLHOURS__, __INSTALLMINUTES__, __OSUSERNAME__, __APPUNIQUEKEY__, __APPDOMAIN__, ...
    				$substitarray=array(
    				'__INSTANCEDIR__'=>$targetdir.'/'.$generatedunixlogin.'/'.$generateddbname,
    				'__INSTANCEDBPREFIX__'=>$generateddbprefix,
    				'__DOL_DATA_ROOT__'=>DOL_DATA_ROOT,
    				'__INSTALLHOURS__'=>dol_print_date($now, '%H'),
    				'__INSTALLMINUTES__'=>dol_print_date($now, '%M'),
    				'__OSHOSTNAME__'=>$generatedunixhostname,
    				'__OSUSERNAME__'=>$generatedunixlogin,
    				'__OSPASSWORD__'=>$generatedunixpassword,
    				'__DBHOSTNAME__'=>$generateddbhostname,
    				'__DBNAME__'=>$generateddbname,
    				'__DBPORT__'=>$generateddbport,
    				'__DBUSER__'=>$generateddbusername,
    				'__DBPASSWORD__'=>$generateddbpassword,
    				'__PACKAGEREF__'=> $tmppackage->ref,
    				'__PACKAGENAME__'=> $tmppackage->label,
    				'__APPUSERNAME__'=>$appusername,
    				'__APPEMAIL__'=>$email,
    				'__APPPASSWORD__'=>$password,
    				'__APPUNIQUEKEY__'=>$generateduniquekey,
    				'__APPDOMAIN__'=>$sldAndSubdomain.'.'.$domainname
    				);


					// Now execute the formula
    				$currentqty = $tmpobject->qty;

    				$tmparray=explode(':', $producttmp->array_options['options_resource_formula'], 2);
    				if ($tmparray[0] == 'SQL')
    				{
    					$sqlformula = make_substitutions($tmparray[1], $substitarray);

    					$serverdeployement = getRemoveServerDeploymentIp();

    					dol_syslog("Try to connect to instance database to execute formula calculation");

    					//var_dump($generateddbhostname);	// fqn name dedicated to instance in dns
    					//var_dump($serverdeployement);		// just ip of deployement server
    					//$dbinstance = @getDoliDBInstance('mysqli', $generateddbhostname, $generateddbusername, $generateddbpassword, $generateddbname, $generateddbport);
    					$dbinstance = @getDoliDBInstance('mysqli', $serverdeployement, $generateddbusername, $generateddbpassword, $generateddbname, $generateddbport);
    					if (! $dbinstance || ! $dbinstance->connected)
    					{
    						$error++;
    						$this->error = $dbinstance->error;
    						$this->errors = $dbinstance->errors;
    					}
    					else
    					{
    						dol_syslog("Execute sql=".$sqlformula);
    						$resql = $dbinstance->query($sqlformula);
    						if ($resql)
    						{
    							$objsql = $dbinstance->fetch_object($resql);
    							if ($objsql)
    							{
    								$newqty = $objsql->nb;
    							}
    							else
    							{
    								$error++;
    								$this->error = 'SQL to get resource return nothing';
    								$this->errors[] = 'SQL to get resource return nothing';
    							}
    						}
    						else
    						{
    							$error++;
    							$this->error = $dbinstance->lasterror();
    							$this->errors[] = $dbinstance->lasterror();
    						}
    					}
    				}
    				else
    				{
    					$error++;
    					$this->error = 'Bad definition of formula to calculate resource for product '.$producttmp->ref;
    				}

    				if (! $error && $newqty != $currentqty)
    				{
    					$tmpobject->qty = $newqty;
    					$result = $tmpobject->update($user);
    					if ($result <= 0)
    					{
    						$error++;
    						$this->error = 'Failed to update the count for product '.$producttmp->ref;
    					}
    				}
    			}
    		}
    	}

    	if ($error) return -1;
    	else return 1;
    }


}
