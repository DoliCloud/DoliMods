<?php
/* Copyright (C) 2007-2011 Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-François FERRY <jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *      \file       ovh/class/ovhsms.class.php
 *      \ingroup    ovh
 *      \brief      This file allow to send sms with an OVH account
 *		\author		Jean-François FERRY
 */


require_once(NUSOAP_PATH.'/nusoap.php');

/**
 *      \class      OvhSms
 *      \brief      Use an OVH account to send SMS with Dolibarr
 */
class OvhSms  extends CommonObject
{
    var $db;							//!< To store db handler
    var $error;							//!< To return error code (or message)
    var $errors=array();				//!< To return several error codes (or messages)
    var $element='ovhsms';			//!< Id that identify managed object

    var $id;
    var $account;
    var $fk_soc;
    var $expe;
    var $dest;
    var $message;
    var $validity;
    var $class;
    var $deferred;
    var $priority;


    /**
     *      Constructor
     *      @param      DB      Database handler
     */
    function OvhSms($DB)
    {
        global $conf, $langs;
        $this->db = $DB;

        // Réglages par défaut
        $this->validity = '10';
        $this->class = '2';
        $this->priority = '3';
        $this->deferred = '60';
        // Set the WebService URL
        dol_syslog(get_class($this)."::OvhSms URL=".$conf->global->OVHSMS_SOAPURL);

        if ($conf->global->OVHSMS_SOAPURL)
        {
            require_once(DOL_DOCUMENT_ROOT.'/lib/functions2.lib.php');
            $params=getSoapParams();
            ini_set('default_socket_timeout', $params['response_timeout']);
            $this->soap = new SoapClient($conf->global->OVHSMS_SOAPURL,$params);
            // https://www.ovh.com/soapi/soapi-re-1.8.wsdl
            try {

                $this->login = $nic;
                $this->password = $passe;

                $language = null;
                $multisession = false;

                $this->session = $this->soap->login($conf->global->OVHSMS_NICK, $conf->global->OVHSMS_PASS,$language,$multisession);
                // On mémorise le compe sms associé
                $this->account = $conf->global->OVHSMS_ACCOUNT;

                return 1;

            } catch(SoapFault $fault) {
                dol_syslog(get_class($this).'::SoapFault: '.$fault);

                return 0;
            }
            return 1;
        }
        else return 0;
    }

    function logout() {
        $this->soap->logout($this->session);
        return 1;
    }


    /*
     * Envoi d'un sms
     */
    function SmsSend() {
        //telephonySmsSend
        try
        {
            $resultsend = $this->soap->telephonySmsSend($this->session, $this->account, $this->expe, $this->dest, $this->message, $this->validity, $this->class, $this->deferred, $this->priority);
            return $resultsend;
        }
        catch(SoapFault $fault)
        {
            $errmsg="Error ".$fault->faultstring;
            dol_syslog(get_class($this)."::SmsSend ".$errmsg, LOG_ERR);

            $this->error.=($this->error?', '.$errmsg:$errmsg);
        }
        return -1;
    }


    function printListAccount() {
        $resultaccount = $this->getSmsListAccount();
        print '<select name="ovh_account" id="ovh_account">';
        foreach ($resultaccount as $accountlisted) {
            print '<option value="'.$accountlisted.'">'.$accountlisted.'</option>';
        }
        print '</select>';
    }


    function getSmsListAccount()
    {
        //telephonySmsAccountList
        return $this->soap->telephonySmsAccountList($this->session);
    }

    function CreditLeft()
    {
        return $this->soap->telephonySmsCreditLeft($this->session, $this->account);
    }

    function SmsHistory($account)
    {
        return $this->soap->telephonySmsHistory($this->session, $this->account, "");
    }

    function SmsSenderList($account)
    {
        try {
            $telephonySmsSenderList = $this->soap->telephonySmsSenderList($this->session, $this->account);
            return $telephonySmsSenderList;
        }
        catch(SoapFault $fault) {
            $errmsg="Error ".$fault->faultstring;
            dol_syslog(get_class($this)."::SmsSenderList ".$errmsg, LOG_ERR);
            $this->error.=($this->error?', '.$errmsg:$errmsg);
            return -1;
        }
        return -1;
    }

}
?>