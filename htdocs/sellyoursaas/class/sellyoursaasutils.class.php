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
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class SellYourSaasUtils extends CommonObject
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
    public function doUndeployOldSuspendedTestInstances()
    {
    	global $conf, $langs;

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
    public function doSuspendNotPaidRealInstances()
    {
    	global $conf, $langs;

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

    	$this->output = '';
    	$this->error='';

    	dol_syslog(__METHOD__, LOG_DEBUG);

    	// ...

    	return 0;
    }

}
