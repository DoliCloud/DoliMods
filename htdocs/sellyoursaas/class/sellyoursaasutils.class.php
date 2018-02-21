<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doSendWelcomeMessage()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doSendWelcomeMessage.log';

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
    public function doAlertSoftEndTrial()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doAlertSoftEndTrial.log';

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
    public function doSuspendNotPaidTestInstances()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doSuspendNotPaidTestInstances.log';

    	dol_syslog(__METHOD__, LOG_DEBUG);
    	return $this->doSuspendInstances('test');
    }

    /**
     * Action executed by scheduler
     * CAN BE A CRON TASK
     *
     * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
     */
    public function doSuspendNotPaidRealInstances()
    {
    	global $conf, $langs;

    	$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_doSuspendNotPaidRealInstances.log';

    	dol_syslog(__METHOD__, LOG_DEBUG);
    	return $this->doSuspendInstances('paid');
    }


   	/**
   	 * Called by doSuspendNotPaidTestInstances or doSuspendNotPaidRealInstances
   	 *
   	 * @param	string	$mode		'test' or 'paid'
   	 * @return	int					0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
   	 */
   	private function doSuspendInstances($mode)
   	{
    	global $conf, $langs, $user;

    	if ($mode != 'test' && $mode != 'paid') return -1;

    	$this->output = '';
    	$this->error='';

    	$now = dol_now();

    	$sql = 'SELECT c.rowid, c.ref_customer, cd.rowid as lid';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'contrat as c, '.MAIN_DB_PREFIX.'contratdet as cd, '.MAIN_DB_PREFIX.'contrat_extrafields as ce';
    	$sql.= ' WHERE cd.fk_contrat = c.rowid AND ce.fk_object = c.rowid';
    	$sql.= " AND ce.deployment_status = 'done'";
    	//$sql.= " AND cd.date_fin_validite < '".$this->db->idate(dol_time_plus_duree(dol_now(), 1, 'd'))."'";
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
						var_dump($object->ref_customer);
						//$object->array_options['options_deployment_status'] = 'suspended';
						$object->closeAll($user);			// This may execute trigger that make system actions to suspend instance

						$contractprocessed[$object->id]=$object->id;
					}
				}
    			$i++;
    		}
    	}
    	else $this->error = $this->db->lasterror();

    	$this->output = count($contractprocessed).' contract(s) suspended';

    	return 0;
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

}
