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
     * Action executed by scheduler for job SellYourSaasValidateDraftInvoices
     * Check account is not closed. Validate draft invoice if not, delete if closed.
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doValidateDraftInvoices()
    {
    	global $conf, $langs, $user;
		include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
		$invoice = new Facture($this->db);

    	dol_syslog(__METHOD__." search and validate draft invoices", LOG_DEBUG);

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	$draftinvoiceprocessed = array();

    	$this->db->begin();

		$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'facture WHERE fk_statut = '.Facture::STATUS_DRAFT;
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num_rows = $this->db->num_rows($resql);
			$i= 0;
			while($i < $num_rows)
			{
				$obj = $this->db->fetch_object($resql);
				if ($invoice->fetch($obj->rowid) > 0)
				{
					// Search contract linked to invoice
					$invoice->fetchObjectLinked();

					if (is_array($invoice->linkedObjects['contrat']) && count($invoice->linkedObjects['contrat']) > 0)
					{
						//dol_sort_array($object->linkedObjects['facture'], 'date');
						foreach($invoice->linkedObjects['contrat'] as $idcontract => $contract)
						{
							// We ignore $contract->nbofserviceswait +  and $contract->nbofservicesclosed
							$nbservice = $contract->nbofservicesopened + $contract->nbofservicesexpired;
							if ($nbservice && empty($draftinvoiceprocessed[$invoice->id]))	// If ok and not yet processed
							{
								$result = $invoice->validate($user);
								if ($result > 0)
								{
									$draftinvoiceprocessed[$invoice->id]=$invoice->ref;
								}
								else
								{
									$error++;
									$this->error = $invoice->error;
									$this->errors = $invoice->errors;
									break;
								}
							}
						}
					}
				}
				else
				{
					$error++;
					$this->errors[] = 'Failed to get invoice '.$obj->rowid;
				}

				$i++;
			}
		}
		else
		{
			$error++;
			$this->error = $this->db->lasterror();
		}

		$this->output = count($draftinvoiceprocessed).' invoice(s) validated on '.$num_rows.' draft invoice found'.(count($draftinvoiceprocessed)>0 ? ' : '.join(',', $draftinvoiceprocessed) : '');

		$this->db->commit();

		return ($error ? 1: 0);
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

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	$now = dol_now();

    	$delayindaysshort = $conf->global->SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT;
    	$delayindayshard = $conf->global->SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT;
    	if ($delayindaysshort <= 0 || $delayindayshard <= 0)
    	{
    		$this->error='BadValueForDelayBeforeTrialEndForAlert';
    		return -1;
    	}
    	dol_syslog(__METHOD__." we send email warning ".$delayindays." days before end of trial", LOG_DEBUG);

    	$this->db->begin();

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

    				$isAPayingContract = sellyoursaasIsPaidInstance($object);
    				if ($mode == 'test' && $isAPayingContract) continue;											// Discard if this is a paid instance when we are in test mode
    				//if ($mode == 'paid' && ! $isAPayingContract) continue;											// Discard if this is a test instance when we are in paid mode

    				// Suspend instance
    				$tmparray = sellyoursaasGetExpirationDate($object);
    				$expirationdate = $tmparray['expirationdate'];

    				if ($expirationdate && $expirationdate < dol_time_plus_duree($now, abs($delayindaysshort), 'd'))
    				{
    					$substitutionarray=getCommonSubstitutionArray($outputlangs, 0, null, $object);
    					$substitutionarray['__SELLYOURSAAS_EXPIRY_DATE__']=dol_print_date($expirationdate, 'day', $outputlangs, 'tzserver');
    					complete_substitutions_array($substitutionarray, $outputlangs, $object);

    					//$object->array_options['options_deployment_status'] = 'suspended';
    					$subject = make_substitutions($arraydefaultmessage->topic, $substitutionarray);
    					$msg     = make_substitutions($arraydefaultmessage->content, $substitutionarray);
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

    	$this->output = count($contractprocessed).' email(s) sent'.(count($contractprocessed)>0 ? ' : '.join(',', $contractprocessed) : '');

    	$this->db->commit();

    	return ($error ? 1: 0);
    }


    /**
     * Action executed by scheduler. To run every day
     * CAN BE A CRON TASK
     *
     * @param	int			$day1	Day1 in month to launch warnings (1st)
     * @param	int			$day2	Day2 in month to launch warnings (20th)
     * @return	int					0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doAlertCreditCardExpiration($day1='',$day2='')
    {
    	global $conf, $langs, $user;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doAlertCreditCardExpiration.log';

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__.' - Search card that expire at end of month and send remind. Test is done the day '.$day1.' and '.$day2.' of month', LOG_DEBUG);

    	if (empty($day1) ||empty($day2))
    	{
    		$this->error = 'Bad value for parameter day1 and day2. Set param to "1, 20" for example';
    		$error++;
    		return 1;
    	}

    	$servicestatus = 1;
    	if (! empty($conf->stripe->enabled))
    	{
    		$service = 'StripeTest';
    		$servicestatus = 0;
    		if (! empty($conf->global->STRIPE_LIVE) && ! GETPOST('forcesandbox','alpha'))
    		{
    			$service = 'StripeLive';
    			$servicestatus = 1;
    		}
    	}

    	$now = dol_now();
    	$currentdate = dol_getdate($now);
    	$currentday = $currentdate['mday'];
    	$currentmonth = $currentdate['mon'];
    	$currentyear = $currentdate['year'];

    	if ($currentday != $day1 && $currentday != $day2) {
    		$this->output = 'Nothing to do. We are not the day '.$day1.', neither the day '.$day2.' of the month';
    		return 0;
    	}

    	$this->db->begin();

    	// Get warning email template
    	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
    	include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
    	$formmail=new FormMail($db);

    	$nextyear = $currentyear;
    	$nextmonth = $currentmonth + 1;
    	if ($nextmonth > 12) { $nextmonth = 1; $nextyear++; }

    	$sql = 'SELECT sr.rowid, sr.fk_soc, sr.exp_date_month, sr.exp_date_year, sr.last_four, sr.status FROM '.MAIN_DB_PREFIX.'societe_rib as sr, '.MAIN_DB_PREFIX.'societe as s';
    	$sql.= " WHERE sr.fk_soc = s.rowid AND sr.default_rib = 1 AND sr.type = 'card' AND sr.status = ".$servicestatus;
    	$sql.= " AND sr.exp_date_month = ".$currentmonth." AND sr.exp_date_year = ".$currentyear;

    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$num_rows = $this->db->num_rows($resql);
    		$i = 0;
    		while ($i < $num_rows)
    		{
    			$obj = $this->db->fetch_object($resql);

    			$thirdparty = new Societe($this->db);
    			$thirdparty->fetch($obj->fk_soc);
    			if ($thirdparty->id)
    			{
    				$langstouse = new Translate('', $conf);
    				$langstouse->setDefaultLang($thirdparty->default_lang ? $thirdparty->default_lang : $langs->defaultlang);

    				$arraydefaultmessage=$formmail->getEMailTemplate($this->db, 'thirdparty', $user, $langstouse, -2, 1, '(AlertCreditCardExpiration)');		// Templates are init into data.sql

    				if (is_object($arraydefaultmessage) && ! empty($arraydefaultmessage->topic))
    				{
    					$substitutionarray=getCommonSubstitutionArray($langstouse, 0, null, $thirdparty);
    					$substitutionarray['__CARD_EXP_DATE_MONTH__']=$obj->exp_date_month;
    					$substitutionarray['__CARD_EXP_DATE_YEAR__']=$obj->exp_date_year;
    					$substitutionarray['__CARD_LAST4__']=$obj->last_four;

    					complete_substitutions_array($substitutionarray, $langstouse, $contract);

    					$subject = make_substitutions($arraydefaultmessage->topic, $substitutionarray, $langstouse);
    					$msg     = make_substitutions($arraydefaultmessage->content, $substitutionarray, $langstouse);
    					$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
    					$to = $thirdparty->email;

    					$cmail = new CMailFile($subject, $to, $from, $msg, array(), array(), array(), '', '', 0, 1);
    					$result = $cmail->sendfile();
    					if (! $result)
    					{
    						$error++;
    						$this->error = 'Failed to send email to thirdparty id = '.$thirdparty->id.' : '.$cmail->error;
    						$this->errors[] = 'Failed to send email to thirdparty id = '.$thirdparty->id.' : '.$cmail->error;
    					}
    				}
    				else
    				{
    					$error++;
    					$this->error = 'Failed to get email a valid template (AlertCreditCardExpiration)';
    					$this->errors[] = 'Failed to get email a valid template (AlertCreditCardExpiration)';
    				}
    			}

    			$i++;
    		}
    	}
    	else
    	{
    		$error++;
    		$this->error = $this->db->lasterror();
    	}

    	if (! $error)
    	{
    		$this->output = 'Found '.$num_rows.' record with credit card that will expire soon';
    	}

    	$this->db->commit();

    	return $error;
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @param	int			$day1	Day1 in month to launch warnings (1st)
     * @param	int			$day2	Day2 in month to launch warnings (20th)
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doAlertPaypalExpiration($day1='', $day2='')
    {
    	global $conf, $langs, $user;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doAlertPaypalExpiration.log';

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__.' - Search paypal approval that expire at end of month and send remind. Test is done the day '.$day1.' and '.$day2.' of month', LOG_DEBUG);

    	if (empty($day1) ||empty($day2))
    	{
    		$this->error = 'Bad value for parameter day1 and day2. Set param to "1, 20" for example';
    		$error++;
    		return 1;
    	}

    	$servicestatus = 1;
    	if (! empty($conf->paypal->enabled))
    	{
    		//$service = 'StripeTest';
    		$servicestatus = 0;
    		if (! empty($conf->global->PAYPAL_LIVE) && ! GETPOST('forcesandbox','alpha'))
    		{
    			//$service = 'StripeLive';
    			$servicestatus = 1;
    		}
    	}

    	$now = dol_now();
    	$currentdate = dol_getdate($now);
    	$currentday = $currentdate['mday'];
    	$currentmonth = $currentdate['mon'];
    	$currentyear = $currentdate['year'];

    	if ($currentday != $day1 && $currentday != $day2) {
    		$this->output = 'Nothing to do. We are not the day '.$day1.', neither the day '.$day2.' of the month';
    		return 0;
    	}

    	$this->db->begin();

    	// Get warning email template
    	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
    	include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
    	$formmail=new FormMail($db);

    	$nextyear = $currentyear;
    	$nextmonth = $currentmonth + 1;
    	if ($nextmonth > 12) { $nextmonth = 1; $nextyear++; }
    	$timelessonemonth = dol_time_plus_duree($now, -1, 'm');

    	if ($timelessonemonth)
    	{
	    	$sql = 'SELECT sr.rowid, sr.fk_soc, sr.ending_date, sr.status FROM '.MAIN_DB_PREFIX.'societe_rib as sr, '.MAIN_DB_PREFIX.'societe as s';
	    	$sql.= " WHERE sr.fk_soc = s.rowid AND sr.default_rib = 1 AND sr.type = 'card' AND sr.status = ".$servicestatus;
	    	$sql.= " AND sr.ending_date > '".$this->db->idate($timelessonemonth)."'";

	    	$resql = $this->db->query($sql);
	    	if ($resql)
	    	{
	    		$num_rows = $this->db->num_rows($resql);
	    		$i = 0;
	    		while ($i < $num_rows)
	    		{
	    			$obj = $this->db->fetch_object($resql);

	    			$thirdparty = new Societe($this->db);
	    			$thirdparty->fetch($obj->fk_soc);
	    			if ($thirdparty->id)
	    			{
	    				$langstouse = new Translate('', $conf);
	    				$langstouse->setDefaultLang($thirdparty->default_lang ? $thirdparty->default_lang : $langs->defaultlang);

	    				$arraydefaultmessage=$formmail->getEMailTemplate($this->db, 'thirdparty', $user, $langstouse, -2, 1, 'AlertPaypalApprovalExpiration');		// Templates are init into data.sql

	    				if (is_object($arraydefaultmessage) && ! empty($arraydefaultmessage->topic))
	    				{
	    					$substitutionarray=getCommonSubstitutionArray($langstouse, 0, null, $thirdparty);
	    					$substitutionarray['__PAYPAL_EXP_DATE__']=dol_print_date($obj->ending_date, 'day', $langstouse);

	    					complete_substitutions_array($substitutionarray, $langstouse, $contract);

	    					$subject = make_substitutions($arraydefaultmessage->topic, $substitutionarray, $langstouse);
	    					$msg     = make_substitutions($arraydefaultmessage->content, $substitutionarray, $langstouse);
	    					$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
	    					$to = $thirdparty->email;

	    					$cmail = new CMailFile($subject, $to, $from, $msg, array(), array(), array(), '', '', 0, 1);
	    					$result = $cmail->sendfile();
	    					if (! $result)
	    					{
	    						$error++;
	    						$this->error = 'Failed to send email to thirdparty id = '.$thirdparty->id.' : '.$cmail->error;
	    						$this->errors[] = 'Failed to send email to thirdparty id = '.$thirdparty->id.' : '.$cmail->error;
	    					}
	    				}
	    				else
	    				{
	    					$error++;
	    					$this->error = 'Failed to get email a valid template AlertPaypalApprovalExpiration';
	    					$this->errors[] = 'Failed to get email a valid template AlertPaypalApprovalExpiration';
	    				}
	    			}

	    			$i++;
	    		}
	    	}
	    	else
	    	{
	    		$error++;
	    		$this->error = $this->db->lasterror();
	    	}
    	}

    	if (! $error)
    	{
    		$this->output = 'Found '.$num_rows.' record with paypal approval that will expire soon';
    	}

    	$this->db->commit();

    	return $error;
    }


    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     * Loop on each contract. If it is a paid contract, and there is no unpayed invoice for contract, and end date < in 2 days (so expired or soon expired), we update to contract service end date to end of next period.
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doRenewalContracts()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doRenewalContracts.log';
    	$now = dol_now();

    	$mode = 'paid';
    	$delayindaysshort= 2;
    	$enddatetoscan = dol_time_plus_duree($now, abs($delayindaysshort), 'd');		// $enddatetoscan = yesterday

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	$contractprocessed = array();
    	$contractignored = array();

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	$this->db->begin();

    	$sql = 'SELECT c.rowid, c.ref_customer, cd.rowid as lid, cd.date_fin_validite';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'contrat as c, '.MAIN_DB_PREFIX.'contratdet as cd, '.MAIN_DB_PREFIX.'contrat_extrafields as ce';
    	$sql.= ' WHERE cd.fk_contrat = c.rowid AND ce.fk_object = c.rowid';
    	$sql.= " AND ce.deployment_status = 'done'";
    	//$sql.= " AND cd.date_fin_validite < '".$this->db->idate(dol_time_plus_duree($now, abs($delayindaysshort), 'd'))."'";
    	//$sql.= " AND cd.date_fin_validite > '".$this->db->idate(dol_time_plus_duree($now, abs($delayindayshard), 'd'))."'";
    	$sql.= " AND date_format(cd.date_fin_validite, '%Y-%m-%d') < date_format('".$this->db->idate($enddatetoscan)."', '%Y-%m-%d')";
    	$sql.= " AND cd.statut = 4";
    	//print $sql;

    	function cmp($a, $b)
    	{
    		return strcmp($a->date, $b->date);
    	}

    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);

    		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';

    		$i=0;
    		while ($i < $num)
    		{
    			$obj = $this->db->fetch_object($resql);
    			if ($obj)
    			{
    				if (! empty($contractprocessed[$object->id]) || ! empty($contractignored[$object->id])) continue;

    				// Test if this is a paid or not instance
    				$object = new Contrat($this->db);
    				$object->fetch($obj->rowid);		// fetch also lines
    				$object->fetch_thirdparty();

    				$isAPayingContract = sellyoursaasIsPaidInstance($object);
    				//var_dump($mode.' '.$isAPayingContract);
    				if ($mode == 'test' && $isAPayingContract)
    				{
    					$contractignored[$object->id]=$object->ref;
    					continue;											// Discard if this is a paid instance when we are in test mode
    				}
    				if ($mode == 'paid' && ! $isAPayingContract)
    				{
    					$contractignored[$object->id]=$object->ref;
    					continue;											// Discard if this is a test instance when we are in paid mode
    				}

    				// Update expiration date of instance
    				$tmparray = sellyoursaasGetExpirationDate($object);
    				$expirationdate = $tmparray['expirationdate'];
    				$duration_value = $tmparray['duration_value'];
    				$duration_unit = $tmparray['duration_unit'];
    				//var_dump($expirationdate.' '.$enddatetoscan);

    				// Test if there is pending invoice
    				$object->fetchObjectLinked();

    				if (is_array($object->linkedObjects['facture']) && count($object->linkedObjects['facture']) > 0)
    				{
    					usort($object->linkedObjects['facture'], "cmp");

    					//dol_sort_array($contract->linkedObjects['facture'], 'date');
    					$someinvoicenotpaid=0;
    					foreach($object->linkedObjects['facture'] as $idinvoice => $invoice)
    					{
    						if (empty($invoice->paye))
    						{
    							$someinvoicenotpaid++;
    						}
    					}
    					if ($someinvoicenotpaid)
    					{
    						$this->output .= 'Contract '.$object->ref.' is qualified for renewal but there is '.$someinvoicenotpaid.' invoice(s) unpayed so we cancel renewal'."\n";
    						$contractignored[$object->id]=$object->ref;
							continue;
    					}
    				}

    				if ($expirationdate && $expirationdate < $enddatetoscan)
    				{
    					$newdate = $expirationdate;
    					$protecti=0;	//$protecti is to avoid infinite loop
    					while ($newdate < $enddatetoscan && $protecti < 1000)
    					{
    						$newdate = dol_time_plus_duree($newdate, $duration_value, $duration_unit);
    						$protecti++;
    					}

    					if ($protecti < 1000)
    					{
    						$sqlupdate = 'UPDATE '.MAIN_DB_PREFIX."contratdet SET date_fin_validite = '".$this->db->idate($newdate)."'";
    						$sqlupdate.= ' WHERE fk_contrat = '.$object->id;
    						$resqlupdate = $this->db->query($sqlupdate);
    						if ($resqlupdate)
    						{
    							$contractprocessed[$object->id]=$object->ref;
    						}
    						else
    						{
    							$error++;
    							$this->error = $this->db->lasterror();
    						}
    					}
    					else
    					{
    						$error++;
    						$this->error = "Bad value for newdate";
    						dol_syslog("Bad value for newdate", LOG_ERR);
    					}
    				}
    			}
    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output .= count($contractprocessed).' paying contract(s) with end date before '.dol_print_date($enddatetoscan, 'day').' were renewed'.(count($contractprocessed)>0 ? ' : '.join(',', $contractprocessed) : '');

    	$this->db->commit();

    	return ($error ? 1: 0);
    }



    /**
     * Action executed by scheduler
     * Loop on invoice for customer with default payment mode Stripe and take payment/send email. Unsuspend if it was suspended (done by trigger BILL_CANCEL or BILL_PAID).
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doTakePaymentStripe()
    {
    	global $conf, $langs, $mysoc;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doTakePaymentStripe.log';

    	include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
    	include_once DOL_DOCUMENT_ROOT.'/societe/class/companypaymentmode.class.php';

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	$invoiceprocessed = array();
    	$invoiceprocessedok = array();
    	$invoiceprocessedko = array();

    	if (empty($conf->stripe->enabled))
    	{
    		$this->error='Error, stripe module not enabled';
    		return 1;
    	}

   		$service = 'StripeTest';
   		$servicestatus = 0;
   		if (! empty($conf->global->STRIPE_LIVE) && ! GETPOST('forcesandbox','alpha'))
   		{
   			$service = 'StripeLive';
   			$servicestatus = 1;
    	}

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	$this->db->begin();

    	$sql = 'SELECT f.rowid, s.rowid as socid, sr.rowid as companypaymentmodeid';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'societe as s, '.MAIN_DB_PREFIX.'societe_rib as sr';
    	$sql.= ' WHERE f.fk_soc = s.rowid AND sr.fk_soc = s.rowid';
    	$sql.= " AND f.paye = 0 AND f.type = 0 AND f.fk_statut = ".Facture::STATUS_VALIDATED;
    	$sql.= " AND sr.status = ".$servicestatus;
    	$sql.= " ORDER BY f.date ASC, sr.default_rib DESC, sr.tms DESC";		// Lines may be duplicated. Never mind, we wil exclude duplicated invoice later.
    	//print $sql;

    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$num = $this->db->num_rows($resql);

    		$i=0;
    		while ($i < $num)
    		{
    			$obj = $this->db->fetch_object($resql);
    			if ($obj)
    			{
    				if (! empty($invoiceprocessed[$obj->rowid])) continue;		// Invoice already processed

    				$invoice = new Facture($this->db);
    				$result1 = $invoice->fetch($obj->rowid);

    				$companypaymentmode = new CompanyPaymentMode($this->db);
    				$result2 = $companypaymentmode->fetch($obj->companypaymentmodeid);

	    			if ($result1 <= 0 || $result2 <= 0)
	    			{
	    				$error++;
	    				dol_syslog('Failed to get invoice id = '.$invoice_id.' or companypaymentmode id ='.$companypaymentmodeid, LOG_ERR);
	    				$this->errors[] = 'Failed to get invoice id = '.$invoice_id.' or companypaymentmode id ='.$companypaymentmodeid;
	    			}
    				else
    				{
    					$result = $this->doTakeStripePaymentForThirdparty($service, $servicestatus, $obj->socid, $companypaymentmode, $invoice);
						if ($result == 0)	// No error
						{
							$invoiceprocessedok[$obj->rowid]=$invoice->ref;
						}
						else
						{
							$invoiceprocessedko[$obj->rowid]=$invoice->ref;
						}
    				}

    				$invoiceprocessed[$obj->rowid]=$invoice->ref;
    			}

    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output = count($invoiceprocessed).' validated invoice with a valid default payment mode processed'.(count($invoiceprocessed)>0 ? ' : '.join(',', $invoiceprocessed) : '');

    	$this->db->commit();

    	return 0;
    }


    /**
     * doTakeStripePaymentForInvoice
     * Take payment/send email. Unsuspend if it was suspended (done by trigger BILL_CANCEL or BILL_PAID).
     *
     * @param	int		$service				'StripeTest' or 'StripeLive'
     * @param	int		$servicestatus			Service 0 or 1
     * @param	int		$thirdparty_id			Thirdparty id
     * @param	int		$companypaymentmodeid	Company payment mode id
     * @param	int		$invoice				null=All invoices of thirdparty, Invoice=Only this invoice
     * @return	int								0 if no error, >0 if error
     */
    function doTakeStripePaymentForThirdparty($service, $servicestatus, $thirdparty_id, $companypaymentmode, $invoice=null)
    {
    	global $conf, $mysoc, $user, $langs;

    	$error = 0;

    	dol_syslog("doTakeStripePaymentForThirdparty thirdparty_id=".$thirdparty_id);

    	// Check parameters
    	if (empty($thirdparty_id))
    	{
    		$this->errors[]='Empty parameter thirdparty_id when calling doTakeStripePaymentForThirdparty';
    		return 1;
    	}

    	$currency = $mysoc->currency_code;
    	$cardstripe = $companypaymentmode->stripe_ref_card;

    	$invoices=array();
    	if (empty($invoice))
    	{
    		$sql = 'SELECT f.rowid';
    		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'societe as s';
    		$sql.= ' WHERE f.fk_soc = s.rowid';
    		$sql.= " AND f.paye = 0 AND f.type = 0 AND f.fk_statut = ".Facture::STATUS_VALIDATED;
    		$sql.= " AND s.rowid = ".$thirdparty_id;
    		$sql.= " ORDER BY f.datef ASC";
    		//print $sql;

    		$resql = $this->db->query($sql);
    		if ($resql)
    		{
    			$num = $this->db->num_rows($resql);

    			$i=0;
    			while ($i < $num)
    			{
    				$obj = $this->db->fetch_object($resql);
    				if ($obj)
    				{
    					$invoice = new Facture($this->db);
    					$result = $invoice->fetch($obj->rowid);
    					if ($result > 0) $invoices[] = $invoice;
    				}
    				$i++;
    			}
    		}
    	}
    	else
    	{
    		$invoices[] = $invoice;
    	}
		if (count($invoices) == 0)
		{
			dol_syslog("No validated invoices found for thirdparty_id = ".$thirdparty_id);
		}

		// Loop on each invoice
		foreach($invoices as $invoice)
		{
			dol_syslog("Process invoice thirdparty_id = ".thirdparty_id." id=".$invoice->id." ref=".$invoice->ref, LOG_DEBUG);

			$alreadypayed = $invoice->getSommePaiement();
    		$amount_credit_notes_included = $invoice->getSumCreditNotesUsed();
    		$amountstripe = $invoice->total_ttc - $alreadypayed - $amount_credit_notes_included;

    		// Correct the amount according to unit of currency
    		// See https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
    		$arrayzerounitcurrency=array('BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF');
    		if (! in_array($currency, $arrayzerounitcurrency)) $amountstripe=$amountstripe * 100;

    		if ($amountstripe > 0)
    		{
    			try {
    				dol_syslog("Search existing Stripe card for companypaymentmodeid=".$companypaymentmode->id, LOG_DEBUG);

    				$thirdparty = new Societe($this->db);
    				$resultthirdparty = $thirdparty->fetch($thirdparty_id);

    				include_once DOL_DOCUMENT_ROOT.'/stripe/class/stripe.class.php';
    				$stripe = new Stripe($this->db);
    				$stripeacc = $stripe->getStripeAccount($service);								// Get Stripe OAuth connect account if it exists (no network access here)
    				$customer = $stripe->customerStripe($thirdparty, $stripeacc, $servicestatus, 0);
    				if ($resultthirdparty > 0 && ! empty($customer))
    				{
    					$stripecard = $stripe->cardStripe($customer, $companypaymentmode, $stripeacc, $servicestatus, 0);
    					if ($stripecard)
    					{
    						$FULLTAG='INV='.$invoice->id.'-CUS='.$thirdparty->ref_customer;
    						$description='Stripe payment from doTakePaymentStripe: '.$FULLTAG;

    						dol_syslog("Create charge on card ".$card->id, LOG_DEBUG, 0, '_stripe');
    						$charge = \Stripe\Charge::create(array(
	    						'amount'   => price2num($amountstripe, 'MU'),
	    						'currency' => $currency,
	    						'capture'  => true,							// Charge immediatly
	    						'description' => $description,
	    						'metadata' => array("FULLTAG" => $FULLTAG, 'Recipient' => $mysoc->name, 'dol_version'=>DOL_VERSION, 'dol_entity'=>$conf->entity, 'ipaddress'=>(empty($_SERVER['REMOTE_ADDR'])?'':$_SERVER['REMOTE_ADDR'])),
	    						'customer' => $customer->id,
	    						'source' => $stripecard,
	    						'statement_descriptor' => dol_trunc(dol_trunc(dol_string_unaccent($mysoc->name), 6, 'right', 'UTF-8', 1).' '.$FULLTAG, 22, 'right', 'UTF-8', 1)     // 22 chars that appears on bank receipt
    						));
    						// Return $charge = array('id'=>'ch_XXXX', 'status'=>'succeeded|pending|failed', 'failure_code'=>, 'failure_message'=>...)
    						if (empty($charge))
    						{
    							$error++;
    							dol_syslog('Failed to charge card '.$stripecard->id, LOG_WARNING);
    							$this->errors[]='Failed to charge card '.$stripecard->id;

    							$description='Stripe payment ERROR from doTakePaymentStripe: '.$FULLTAG;
    							$postactionmessages[]='Failed to charge card '.$stripecard->id;
    						}
    						else
    						{
    							$description='Stripe payment OK from doTakePaymentStripe: '.$FULLTAG;
    							$postactionmessages=array();

    							$db=$this->db;
    							$ipaddress = (empty($_SERVER['REMOTE_ADDR'])?'':$_SERVER['REMOTE_ADDR']);
    							$TRANSACTIONID = $charge->id;
    							$FinalPaymentAmt = $amountstripe;
    							$currency=$conf->currency;
    							$paymentmethod='stripe';

    							// Same code than into paymentok.php...

    							$paymentTypeId = 0;
    							if ($paymentmethod == 'paybox') $paymentTypeId = $conf->global->PAYBOX_PAYMENT_MODE_FOR_PAYMENTS;
    							if ($paymentmethod == 'paypal') $paymentTypeId = $conf->global->PAYPAL_PAYMENT_MODE_FOR_PAYMENTS;
    							if ($paymentmethod == 'stripe') $paymentTypeId = $conf->global->STRIPE_PAYMENT_MODE_FOR_PAYMENTS;
    							if (empty($paymentTypeId))
    							{
    								$paymentType = $_SESSION["paymentType"];
    								if (empty($paymentType)) $paymentType = 'CB';
    								$paymentTypeId = dol_getIdFromCode($db, $paymentType, 'c_paiement', 'code', 'id', 1);
    							}

    							$currencyCodeType = $currency;

    							// Creation of payment line
    							include_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';
    							$paiement = new Paiement($db);
    							$paiement->datepaye     = $now;
    							if ($currencyCodeType == $conf->currency)
    							{
    								$paiement->amounts      = array($invoice->id => $FinalPaymentAmt);   // Array with all payments dispatching with invoice id
    							}
    							else
    							{
    								$paiement->multicurrency_amounts = array($invoice->id => $FinalPaymentAmt);   // Array with all payments dispatching

    								$postactionmessages[] = 'Payment was done in a different currency that currency expected of company';
    								$ispostactionok = -1;
    								$error++;	// Not yet supported
    							}
    							$paiement->paiementid   = $paymentTypeId;
    							$paiement->num_paiement = '';
    							$paiement->note_public  = 'Online payment '.dol_print_date($now, 'standard').' using '.$paymentmethod.' from '.$ipaddress.' - Transaction ID = '.$TRANSACTIONID;

    							if (! $error)
    							{
    								$paiement_id = $paiement->create($user, 1);    // This include closing invoices and regenerating documents
    								if ($paiement_id < 0)
    								{
    									$postactionmessages[] = $paiement->error.' '.join("<br>\n", $paiement->errors);
    									$ispostactionok = -1;
    									$error++;
    								}
    								else
    								{
    									$postactionmessages[] = 'Payment created';
    									$ispostactionok=1;
    								}
    							}

    							if (! $error && ! empty($conf->banque->enabled))
    							{
    								$bankaccountid = 0;
    								if ($paymentmethod == 'paybox') $bankaccountid = $conf->global->PAYBOX_BANK_ACCOUNT_FOR_PAYMENTS;
    								if ($paymentmethod == 'paypal') $bankaccountid = $conf->global->PAYPAL_BANK_ACCOUNT_FOR_PAYMENTS;
    								if ($paymentmethod == 'stripe') $bankaccountid = $conf->global->STRIPE_BANK_ACCOUNT_FOR_PAYMENTS;

    								if ($bankaccountid > 0)
    								{
    									$label='(CustomerInvoicePayment)';
    									if ($invoice->type == Facture::TYPE_CREDIT_NOTE) $label='(CustomerInvoicePaymentBack)';  // Refund of a credit note
    									$result=$paiement->addPaymentToBank($user,'payment',$label, $bankaccountid, '', '');
    									if ($result < 0)
    									{
    										$postactionmessages[] = $paiement->error.' '.joint("<br>\n", $paiement->errors);
    										$ispostactionok = -1;
    										$error++;
    									}
    									else
    									{
    										$postactionmessages[] = 'Bank entry of payment created';
    										$ispostactionok=1;
    									}
    								}
    								else
    								{
    									$postactionmessages[] = 'Setup of bank account to use in module '.$paymentmethod.' was not set. Not way to record the payment.';
    									$ispostactionok = -1;
    									$error++;
    								}
    							}

    							if (count($postactionmessages) > 0)
    							{
    								$description='Stripe payment OK but post action KO from doTakePaymentStripe: '.$FULLTAG;
    							}
    						}

    						$object = $invoice;

    						// Send email
    						include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
    						$formmail=new FormMail($db);
    						// Set output language
    						$outputlangs = new Translate('', $conf);
    						$outputlangs->setDefaultLang(empty($object->thirdparty->default_lang) ? $mysoc->default_lang : $object->thirdparty->default_lang);
    						$outputlangs->loadLangs(array("main", "members", "bills"));
    						// Get email content from templae
    						$arraydefaultmessage=null;


    						if (empty($charge))
    						{
    							$labeltouse = 'InvoicePaymentSuccess';
    						}
    						else
    						{
    							$labeltouse = 'InvoicePaymentFailure';
    						}

    						if (! empty($labeltouse)) $arraydefaultmessage=$formmail->getEMailTemplate($db, 'facture_send', $user, $outputlangs, 0, 1, $labeltouse);

    						if (! empty($labeltouse) && is_object($arraydefaultmessage) && $arraydefaultmessage->id > 0)
    						{
    							$subject = $arraydefaultmessage->topic;
    							$msg     = $arraydefaultmessage->content;
    						}

    						$substitutionarray=getCommonSubstitutionArray($outputlangs, 0, null, $object);
    						complete_substitutions_array($substitutionarray, $outputlangs, $object);
    						$subjecttosend = make_substitutions($subject, $substitutionarray, $outputlangs);
    						$texttosend = make_substitutions($msg, $substitutionarray, $outputlangs);

    						// Attach a file ?
    						$file='';
    						$listofpaths=array();
    						$listofnames=array();
    						$listofmimes=array();
    						if (is_object($invoice))
    						{
    							$invoicediroutput = $conf->facture->dir_output;
    							$fileparams = dol_most_recent_file($invoicediroutput . '/' . $invoice->ref, preg_quote($invoice->ref, '/').'[^\-]+');
    							$file = $fileparams['fullname'];

    							$listofpaths=array($file);
    							$listofnames=array(basename($file));
    							$listofmimes=array(dol_mimetype($file));
    						}

    						$result=$object->send_an_email($texttosend, $subjecttosend, $listofpaths, $listofnames, $listofmimes, "", "", 0, -1);

    						if ($result < 0)
    						{
    							$errmsg=$object->error;
    							$postactionmessages[] = $errmsg;
    							$ispostactionok = -1;
    						}
    						else
    						{
    							if ($file) $postactionmessages[] = 'Email sent to member (with invoice document attached)';
    							else $postactionmessages[] = 'Email sent to member (without any attached document)';
    						}

    						// Track an event
    						$action='PAYMENT_STRIPE';

    						// Insert record of payment error
    						$actioncomm = new ActionComm($this->db);

    						$actioncomm->type_code   = 'AC_OTH_AUTO';		// Type of event ('AC_OTH', 'AC_OTH_AUTO', 'AC_XXX'...)
    						$actioncomm->code        = 'AC_'.$action;
    						$actioncomm->label       = $description;
    						$actioncomm->note        = join(',', $postactionmessages);
    						$actioncomm->fk_project  = $invoice->fk_project;
    						$actioncomm->datep       = $now;
    						$actioncomm->datef       = $now;
    						$actioncomm->percentage  = -1;   // Not applicable
    						$actioncomm->socid       = $thirdparty->id;
    						$actioncomm->contactid   = 0;
    						$actioncomm->authorid    = $user->id;   // User saving action
    						$actioncomm->userownerid = $user->id;	// Owner of action
    						// Fields when action is en email (content should be added into note)
    						/*$actioncomm->email_msgid = $object->email_msgid;
    						 $actioncomm->email_from  = $object->email_from;
    						 $actioncomm->email_sender= $object->email_sender;
    						 $actioncomm->email_to    = $object->email_to;
    						 $actioncomm->email_tocc  = $object->email_tocc;
    						 $actioncomm->email_tobcc = $object->email_tobcc;
    						 $actioncomm->email_subject = $object->email_subject;
    						 $actioncomm->errors_to   = $object->errors_to;*/
    						$actioncomm->fk_element  = $invoice->id;
    						$actioncomm->elementtype = $invoice->element;

    						$actioncomm->create($user);
    					}
    					else
    					{
    						//$error++;
    						dol_syslog("No card found for this stripe customer ".$customer->id, LOG_WARNING);
    						$this->errors[]='Failed to get card for stripe customer = '.$customer->id;
    					}
    				} else {
    					//$error++;
    					dol_syslog('Failed to get customer for thirdparty_id = '.$thirdparty->id, LOG_WARNING);
    					$this->errors[]='Failed to get customer for thirdparty_id = '.$thirdparty->id;
    				}
    			}
    			catch(Exception $e)
    			{
    				$error++;
    				dol_syslog('Error '.$e->getMessage(), LOG_ERR);
    				$this->errors[]='Error '.$e->getMessage();
    			}
    		}
    		else
    		{
    			dol_syslog("Remain to pay is null for this invoice", LOG_WARNING);
    			$this->errors[]='Remain to pay is null for this invoice = '.$customer->id;
    		}
		}

    	return $error;
    }


    /**
     * Action executed by scheduler
     * Loop on invoice for customer with default payment mode Paypal and take payment. Unsuspend if it was suspended.
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doTakePaymentPaypal()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doTakePaymentPaypal.log';

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	$this->db->begin();

    	// ...

    	$this->db->commit();

    	return 0;
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

    	$langs->load("sellyoursaas");

    	$error = 0;
    	$this->output = '';
    	$this->error='';

    	$now = dol_now();

    	$this->db->begin();

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

					$isAPayingContract = sellyoursaasIsPaidInstance($object);
					if ($mode == 'test' && $isAPayingContract) continue;											// Discard if this is a paid instance when we are in test mode
					if ($mode == 'paid' && ! $isAPayingContract) continue;											// Discard if this is a test instance when we are in paid mode

					// Suspend instance
					$tmparray = sellyoursaasGetExpirationDate($object);
					$expirationdate = $tmparray['expirationdate'];

					if ($expirationdate && $expirationdate < $now)
					{
						//$object->array_options['options_deployment_status'] = 'suspended';
						$result = $object->closeAll($user, 0, 'Closed by batch doSuspendInstances the '.dol_print_date($now, 'dayrfc'));			// This may execute trigger that make remote actions to suspend instance
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

   		if (! $error)
   		{
   			$this->output = count($contractprocessed).' contract(s) suspended'.(count($contractprocessed)>0 ? ' : '.join(',', $contractprocessed) : '');
   			$this->db->commit();
   		}
   		else
   		{
   			$this->output = count($contractprocessed).' contract(s) to suspend'.(count($contractprocessed)>0 ? ' : '.join(',', $contractprocessed) : '');
   			$this->db->rollback();
   		}

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
    		$this->error = 'Function doUndeployOldSuspendedInstances called with bad value for parameter '.$mode;
    		return -1;
    	}

    	$error = 0;
    	$this->output = '';
    	$this->error='';

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

    	$this->db->begin();

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

    				$isAPayingContract = sellyoursaasIsPaidInstance($object);
    				if ($mode == 'test' && $isAPayingContract) continue;										// Discard if this is a paid instance when we are in test mode
    				if ($mode == 'paid' && ! $isAPayingContract) continue;										// Discard if this is a test instance when we are in paid mode

    				// Undeploy instance
    				$tmparray = sellyoursaasGetExpirationDate($object);
    				$expirationdate = $tmparray['expirationdate'];

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

    					$contractprocessed[$object->id]=$object->ref;	// To avoid to make action twice on same contract
    				}
    			}
    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output = count($contractprocessed).' contract(s) undeployed'.(count($contractprocessed)>0 ? ' : '.join(',', $contractprocessed) : '');

    	$this->db->commit();

    	return ($error ? 1: 0);
    }




    /**
     * Make a remote action on a contract (deploy/undeploy/suspend/unsuspend/...)
     *
     * @param	string					$remoteaction	Remote action ('suspend/unsuspend'=change apache virtual file, 'deploy/undeploy'=create/delete database, 'refresh'=read remote data)
     * @param 	Contrat|ContratLigne	$object			Object contract or contract line
     * @param	string					$appusername	App login
     * @param	string					$email			Initial email
     * @param	string					$password		Initial password
     * @return	int										<0 if KO, >0 if OK
     */
    function sellyoursaasRemoteAction($remoteaction, $object, $appusername='admin', $email='', $password='')
    {
    	global $conf, $langs, $user;

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

    	dol_syslog("Remote action on instance was called (remoteaction=".$remoteaction." email=".$email." password=".$password);

    	include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

		// Action 'refresh', 'deletelock', 'recreatelock' for contract, check install.lock file
    	if (empty($object->context['fromdolicloudcustomerv1']) && in_array($remoteaction, array('refresh','recreateauthorizedkeys','deletelock','recreatelock')) && get_class($object) == 'Contrat')
    	{
    		// SFTP refresh
    		if (function_exists("ssh2_connect"))
    		{
    			$server=$object->array_options['options_hostname_os'];

    			$connection = @ssh2_connect($server, 22);
    			if ($connection)
    			{
    				//print ">>".$object->array_options['options_username_os']." - ".$object->array_options['options_password_os']."<br>\n";exit;
    				if (! @ssh2_auth_password($connection, $object->array_options['options_username_os'], $object->array_options['options_password_os']))
    				{
    					dol_syslog("Could not authenticate with username ".$object->array_options['options_username_os'], LOG_WARNING);
    					$this->errors[] = "Could not authenticate with username ".$object->array_options['options_username_os']." and password ".preg_replace('/./', '*', $object->array_options['options_password_os']);
    					$error++;
    				}
    				else
    				{
    					if ($remoteaction == 'refresh')
    					{
	    					$sftp = ssh2_sftp($connection);
	    					if (! $sftp)
	    					{
	    						dol_syslog("Could not execute ssh2_sftp",LOG_ERR);
	    						$this->errors[]='Failed to connect to ssh2_sftp to '.$server;
	    						$error++;
	    					}

		    				if (! $error)
		    				{
		    					// Check if install.lock exists
		    					$dir = $object->array_options['options_database_db'];
		    					//$fileinstalllock="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';
		    					$fileinstalllock="ssh2.sftp://".intval($sftp).$object->array_options['options_hostname_os'].'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock';
		    					$fileinstalllock2=$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock';
		    					$fstatlock=@ssh2_sftp_stat($sftp, $fileinstalllock2);
		    					$datelockfile=(empty($fstatlock['atime'])?'':$fstatlock['atime']);

		    					// Check if authorized_keys exists (created during os account creation, into skel dir)
		    					$fileauthorizedkeys="ssh2.sftp://".intval($sftp).$object->array_options['options_hostname_os'].'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock';
		    					$fileauthorizedkeys2=$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/.ssh/authorized_keys';
		    					$fstatlock=@ssh2_sftp_stat($sftp, $fileauthorizedkeys2);
		    					$dateauthorizedkeysfile=(empty($fstatlock['atime'])?'':$fstatlock['atime']);
		    					//var_dump($datelockfile);
		    					//var_dump($fileauthorizedkeys2);

		    					$object->array_options['options_filelock'] = $datelockfile;
		    					$object->array_options['options_fileauthorizekey'] = $dateauthorizedkeysfile;
		    					$object->update($user);
		    				}
    					}

    					if ($remoteaction == 'recreateauthorizedkeys')
    					{
    						$sftp = ssh2_sftp($connection);
    						if (! $sftp)
    						{
    							dol_syslog("Could not execute ssh2_sftp",LOG_ERR);
    							$this->errors[]='Failed to connect to ssh2_sftp to '.$server;
    							$error++;
    						}

    						// Update ssl certificate
    						// Dir .ssh must have rwx------ permissions
    						// File authorized_keys must have rw------- permissions
    						$dircreated=0;
    						$result=ssh2_sftp_mkdir($sftp, $conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/.ssh');
    						if ($result) {
    							$dircreated=1;
    						}	// Created
    						else {
    							$dircreated=0;
    						}	// Creation fails or already exists

    						// Check if authorized_key exists
    						//$filecert="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys';
    						$filecert="ssh2.sftp://".intval($sftp).$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/.ssh/authorized_keys';  // With PHP 5.6.27+
    						$fstat=@ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/.ssh/authorized_keys');

    						// Create authorized_keys file
    						if (empty($fstat['atime']))		// Failed to connect or file does not exists
    						{
    							$stream = fopen($filecert, 'w');
    							if ($stream === false)
    							{
    								$error++;
    								$this->errors[] =$langs->transnoentitiesnoconv("ErrorConnectOkButFailedToCreateFile");
    							}
    							else
    							{
    								// Add public keys
    								fwrite($stream,"ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCltq3M8hs4Zl9WVxSBS2Pn/d6oc9kaLl4NncZCMMvvgEwz48Llo9bKqpr4698Alj2vYCfynjDo4XkU3H7kd/Rq/VRUEQCptzUOAX+/SjwpQUMOy0UDzovw/tYSyY/2tt17lzylR1CJPIoZJINXz5Gy2Et172MWY383EEvHdpAKgrcCZQp3KP3wv892GC79+/MfjV/uyRg0ZN1+hTiGBWmkNtHVBoABA+MgJTFOjRw7aoOLvI4g/zFvAy+6AgtDR1b9QJZvgHKoM/Pfi82RGxEqMCz6jXEMc1UqsadUU5k57Ck1R/Cc3sG/0ufXPdJxHSqbLh9e2uI8JcI0Zmvl4Cun ldestailleur@PCHOME-LD\n");
    								fwrite($stream,"ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAIEAp6Nj1j5jVgziTIRPiWIdqm95P+yT5wAFYzzyzy5g1/ip+YRz6DT+TJUnpI3+coKPtTGahFkHRUIxCMBBObbgkpw0wJr9aBJrZ4YNSIe+DdmIe0JU4L40eHtOcxDNRFCeS8n9LaQ3/K+UV6JEhplibLYEhPKPn4fTfm7Krj0KDVc= admin@apollon1.nltechno.com\n");
    								fwrite($stream,"ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC/A0b/8wwC8wNmb1h3GmwU93oh8M+WDybZbxdRO5IMXw6RKCaLKrnQjs15t4++Qp5ono0oF5HFBWMCrbj8pf15sP02op59rOzALGxFKO8eGtRzcOenCnKCW2ndjGbQFg76evpg3LiE29tpEMQDUM+WMwrATozCIeJE1Q8SJh6/QKJsQTACETJu1+hHKoRTozsqRM/5NLfZ9kiNYbqN80dfm6wDHT8ApiFZ9xnTSxay3NtZjBojeD57TLMmEo9E/2inX5Vupb/JtVik09e80qXSd48s6vk0ecNU9x2LUmNLvbhsPrWeiY2rwCi0h9qW9Y6kwELqqfMe3/cP999UzWnn admin@apollon\n");

    								fclose($stream);
    								$fstat=ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/.ssh/authorized_keys');
    							}
    						}
    						else
    						{
    							$error++;
    							$this->errors[] = $langs->transnoentitiesnoconv("ErrorFileAlreadyExists");
    						}

    						$object->array_options['options_fileauthorizekey']=(empty($fstat['atime'])?'':$fstat['atime']);

    						if (! empty($fstat['atime'])) $result = $object->update($user);
    					}

    					if ($remoteaction == 'deletelock')
    					{
    						$sftp = ssh2_sftp($connection);
    						if (! $sftp)
    						{
    							dol_syslog("Could not execute ssh2_sftp",LOG_ERR);
    							$this->errors[]='Failed to connect to ssh2_sftp to '.$server;
    							$error++;
    						}

    						// Check if install.lock exists
    						$dir = $object->array_options['options_database_db'];
    						$filetodelete=$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock';
    						$result=ssh2_sftp_unlink($sftp, $filetodelete);

    						if (! $result)
    						{
    							$error++;
    							$this->errors[] = $langs->transnoentitiesnoconv("DeleteFails");
    						}
    						else
    						{
    							$object->array_options['options_filelock'] = '';
    						}
    						if ($result)
    						{
    							$result = $object->update($user, 1);
    						}
    					}

    					if ($remoteaction == 'recreatelock')
    					{
    						$sftp = ssh2_sftp($connection);
    						if (! $sftp)
    						{
    							dol_syslog("Could not execute ssh2_sftp",LOG_ERR);
    							$this->errors[]='Failed to connect to ssh2_sftp to '.$server;
    							$error++;
    						}

    						// Check if install.lock exists
    						$dir = $object->array_options['options_database_db'];
    						//$fileinstalllock="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock';
    						$fileinstalllock="ssh2.sftp://".intval($sftp).$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock';
    						$fstat=@ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock');
    						if (empty($fstat['atime']))
    						{
    							$stream = fopen($fileinstalllock, 'w');
    							//var_dump($stream);exit;
    							fwrite($stream,"// File to protect from install/upgrade.\n");
    							fclose($stream);
    							$fstat=ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$object->array_options['options_username_os'].'/'.$dir.'/documents/install.lock');
    						}
    						else
    						{
    							$error++;
    							$this->errors[]=$langs->transnoentitiesnoconv("ErrorFileAlreadyExists");
    						}

    						$object->array_options['options_filelock']=(empty($fstat['atime'])?'':$fstat['atime']);

    						if (! empty($fstat['atime']))
    						{
    							$result = $object->update($user, 1);
    						}
    					}
    				}
    			}
    			else {
    				$this->errors[]='Failed to connect to ssh2 to '.$server;
    				$error++;
    			}
    		}
    		else {
    			$this->errors[]='ssh2_connect not supported by this PHP';
    			$error++;
    		}
    	}

    	// Loop on each line of contract
    	foreach($listoflines as $tmpobject)
    	{
    		$producttmp = new Product($this->db);
    		$producttmp->fetch($tmpobject->fk_product);

    		if (empty($tmpobject->context['fromdolicloudcustomerv1']) &&
    			in_array($remoteaction, array('deploy','deployall','suspend','unsuspend','undeploy')) &&
    			($producttmp->array_options['options_app_or_option'] == 'app' || $producttmp->array_options['options_app_or_option'] == 'option'))
    		{
    			include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
    			dol_include_once('/sellyoursaas/class/packages.class.php');

    			$contract = new Contrat($this->db);
    			$contract->fetch($tmpobject->fk_contrat);

    			$tmp=explode('.', $contract->ref_customer, 2);
    			$sldAndSubdomain=$tmp[0];
    			$domainname=$tmp[1];
    			$serverdeployement = $this->getRemoveServerDeploymentIp($domainname);
    			if (empty($serverdeployement))	// Failed to get remote ip
    			{
    				$error++;
    				break;
    			}

    			$targetdir = $conf->global->DOLICLOUD_INSTANCES_PATH;

    			$generatedunixlogin=$contract->array_options['options_username_os'];
    			$generatedunixpassword=$contract->array_options['options_password_os'];
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
    			$tmppackage->srccliafter = '/tmp/cliafter.'.$sldAndSubdomain.'.'.$domainname.'.tmp';

    			$conffile = make_substitutions($tmppackage->conffile1, $substitarray);
    			$cronfile = make_substitutions($tmppackage->crontoadd, $substitarray);
    			$cliafter = make_substitutions($tmppackage->cliafter, $substitarray);

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

    			dol_syslog("Create cli file ".$tmppackage->srccliafter);
    			file_put_contents($tmppackage->srccliafter, $cliafter);

    			// Remote action : unsuspend
    			$commandurl = $generatedunixlogin.'&'.$generatedunixpassword.'&'.$sldAndSubdomain.'&'.$domainname;
    			$commandurl.= '&'.$generateddbname.'&'.$generateddbport.'&'.$generateddbusername.'&'.$generateddbpassword;
    			$commandurl.= '&'.$tmppackage->srcconffile1.'&'.$tmppackage->targetconffile1.'&'.$tmppackage->datafile1;
    			$commandurl.= '&'.$tmppackage->srcfile1.'&'.$tmppackage->targetsrcfile1.'&'.$tmppackage->srcfile2.'&'.$tmppackage->targetsrcfile2.'&'.$tmppackage->srcfile3.'&'.$tmppackage->targetsrcfile3;
    			$commandurl.= '&'.$tmppackage->srccronfile.'&'.$tmppackage->srccliafter.'&'.$targetdir;
    			$commandurl.= '&'.$conf->global->SELLYOURSAAS_SUPERVISION_EMAIL;
    			$commandurl.= '&'.$serverdeployement;
    			$commandurl.= '&'.$conf->global->SELLYOURSAAS_ACCOUNT_URL;

    			$outputfile = $conf->sellyoursaas->dir_temp.'/action-'.$remoteaction.'-'.dol_getmypid().'.out';


    			$conf->global->MAIN_USE_RESPONSE_TIMEOUT = 60;

    			if (! $error)
    			{
	    			$urltoget='http://'.$serverdeployement.':8080/'.$remoteaction.'?'.urlencode($commandurl);
	    			include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	    			$retarray = getURLContent($urltoget);

	    			if ($retarray['curl_error_no'] != '' || $retarray['http_code'] != 200)
	    			{
	    				$error++;
	    				if ($retarray['curl_error_no'] != '') $this->errors[] = $retarray['curl_error_msg'];
	    				else $this->errors[] = $retarray['content'];
	    			}
    			}

    			if (! $error && in_array($remoteaction, array('deploy','deployall')))
    			{
			    	// Execute personalized SQL requests
			    	if (! $error)
			    	{
			    		$sqltoexecute = make_substitutions($tmppackage->sqlafter, $substitarray);

			    		dol_syslog("Try to connect to instance database to execute personalized requests substitarray=".join(',', $substitarray));

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
    		}

    		if (empty($tmpobject->context['fromdolicloudcustomerv1']) &&
    			$remoteaction == 'refresh')
    		{
    			include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
    			dol_include_once('/sellyoursaas/class/packages.class.php');

    			$contract = new Contrat($this->db);
    			$contract->fetch($tmpobject->fk_contrat);

    			// Update resource count
    			if (! empty($producttmp->array_options['options_resource_formula']))
    			{
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

    					$serverdeployement = $this->getRemoveServerDeploymentIp($domainname);

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




    /**
     * Return IP of server to deploy to
     *
     * @param	string		$domainname		Domain name to select remote ip to deploy to
     * @return	string						'' if KO, ip if OK
     */
    function getRemoveServerDeploymentIp($domainname)
    {
    	global $conf;

    	$error = 0;

    	$REMOTEIPTODEPLOYTO='';
    	$tmparray=explode(',', $conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES);
    	$found=0;
    	foreach($tmparray as $key => $val)
    	{
    		if ($val == $domainname)
    		{
    			$found = $key+1;
    			break;
    		}
    	}
    	//print 'Found domain at position '.$found;
    	if (! $found)
    	{
    		$this->error="Failed to found position of server domain '".$domainname."' to deploy to into SELLYOURSAAS_SUB_DOMAIN_NAMES=".$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES;
    		$this->errors[]="Failed to found position of server domain '".$domainname."' to deploy to into SELLYOURSAAS_SUB_DOMAIN_NAMES=".$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES;
    		$error++;
    	}
    	else
    	{
    		$tmparray=explode(',', $conf->global->SELLYOURSAAS_SUB_DOMAIN_IP);
    		$REMOTEIPTODEPLOYTO=$tmparray[($found-1)];
	    	if (! $REMOTEIPTODEPLOYTO)
	    	{
	    		$this->error="Failed to found ip of server domain '".$domainname."' to deploy to at position '".$found."' into SELLYOURSAAS_SUB_DOMAIN_IPS=".$conf->global->SELLYOURSAAS_SUB_DOMAIN_IP;
	    		$this->errors[]="Failed to found ip of server domain '".$domainname."' to deploy to at position '".$found."' into SELLYOURSAAS_SUB_DOMAIN_IPS=".$conf->global->SELLYOURSAAS_SUB_DOMAIN_IP;
	    		$error++;
	    	}
    	}

    	if ($error) return '';
    	return $REMOTEIPTODEPLOYTO;
    }

}
