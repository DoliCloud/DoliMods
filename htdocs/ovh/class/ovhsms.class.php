<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-François FERRY <jfefe@aternatik.fr>
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
     *      \brief      Constructor
     *      \param      DB      Database handler
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
        dol_syslog("Create nusoap_client for URL=".$conf->global->OVHSMS_SOAPURL);
        $this->soap = new SoapClient($conf->global->OVHSMS_SOAPURL);
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
           dol_syslog("Error nusoap_client for URL=".$conf->global->OVHSMS_SOAPURL. ' : '.$fault);

           return 0;
        }
        return 1;
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
         return -1;
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



/**
 * 		\brief		Show html area for list of contacts
 *		\param		conf		Object conf
 * 		\param		lang		Object lang
 * 		\param		db			Database handler
 * 		\param		objsoc		Third party object
 */
function show_sms_contacts($conf,$langs,$db,$objsoc)
{
	global $user;
	global $bc;

	$contactstatic = new Contact($db);

	if ($conf->clicktodial->enabled)
	{
		$user->fetch_clicktodial(); // lecture des infos de clicktodial
	}

	print_titre($langs->trans("ContactsForCompany"));
	print '<table class="noborder" width="100%">';

	print '<tr class="liste_titre"><td>'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Tel").'</td>';
	print '<td>'.$langs->trans("Portable").'</td>';
	print "<td>&nbsp;</td>";

	print "</tr>";

	$sql = "SELECT p.rowid, p.name, p.firstname, p.poste, p.phone, p.phone_mobile, p.fax, p.email, p.note ";
	$sql .= " FROM ".MAIN_DB_PREFIX."socpeople as p";
	$sql .= " WHERE p.fk_soc = ".$objsoc->id;
	$sql .= " ORDER by p.datec";

	$result = $db->query($sql);
	$i = 0;
	$num = $db->num_rows($result);
	$var=true;

	if ($num)
	{
		while ($i < $num)
		{
			$obj = $db->fetch_object($result);
			$var = !$var;

			print "<tr ".$bc[$var].">";

			print '<td>';
			$contactstatic->id = $obj->rowid;
			$contactstatic->name = $obj->name;
			$contactstatic->firstname = $obj->firstname;
			print $contactstatic->getNomUrl(1);
			print '</td>';

			// Lien click to dial
			print '<td id="contact_tel">';
			print dol_print_phone($obj->phone,$obj->pays_code,$obj->rowid,$objsoc->id,'AC_TEL');
			print '</td>';
			print '<td id="contact_mobile">';
			print dol_print_phone($obj->phone_mobile,$obj->pays_code,$obj->rowid,$objsoc->id,'AC_MOBILE');
			print '</td>';


			print '<td align="center">';
			print '<a href="'.DOL_URL_ROOT.'/contact/fiche.php?action=edit&amp;id='.$obj->rowid.'">';
			print img_edit();
			print '</a></td>';



			print "</tr>\n";
			$i++;
		}
	}
	else
	{
		print "<tr ".$bc[$var].">";
		print '<td>'.$langs->trans("NoContactsYetDefined").'</td>';
		print "</tr>\n";
	}
	print "</table>\n";

	print "<br>\n";
}


}
?>