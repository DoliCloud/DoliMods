<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-Francois FERRY  <jf.ferry@aternatik.fr>
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
 *   	\file       admin/ovhsms_setup.php
 *		\ingroup    ovhsms
 *		\brief      Configuration du module ovhsms
 *		\version    $Id: ovh_recap.php,v 1.1 2010/12/04 01:32:57 eldy Exp $
 *		\author		Put author name here
 *		\remarks	Put here some comments
 */

define('NOCSRFCHECK',1);

$res=@include("../../main.inc.php");
if (! $res) include("../../../../dolibarr/htdocs/main.inc.php");    // Used on dev env only

require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/ovh/class/ovhsms.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("ovh");

if (!$user->admin)
	accessforbidden();

// Get parameters
$account = isset($_GET["account"])?$_GET["account"]:'';



/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/


llxHeader($langs->trans('OvhSmsRecap'),'','');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/ovhsms_setup.php">'.$langs->trans("OvhSmsBackToAdmin").'</a>';
print_fiche_titre($langs->trans("OvhSmsRecap"),$linkback,'setup');


$var=true;


require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP




$sms = new OvhSms($db);
if($sms > 0) {

      //telephonySmsAccountList
      $telephonySmsAccountList = $sms->getSmsListAccount($sms->session);

      print '<table class="nobordernopadding" width="100%">';
      print '<tr class="liste_titre"><td>'.$langs->trans("Account").'</td>';
      print '<td>'.$langs->trans("NbSmsLeft").'</td>';
      print "</tr>\n";

      foreach ($telephonySmsAccountList as $accountlisted) {
         $var=!$var;
         print '<tr '.$bc[$var].'>';
         print '<td><a href="ovhsms_recap.php?account='.$accountlisted.'">';
         print $accountlisted;
         print '</a></td>';
         print '<td>'.$sms->CreditLeft($accountlisted).'</td>';
         print '</tr>';
      }
      print '</table>';



      if(!empty($account)) {

		  $nbenvoi = '29';
		  $nbenvoi2 = $nbenvoi+1;
		  //telephonySmsHistory
		  echo '<h2>'.$langs->trans('OvhSmsHistory',$nbenvoi2).'</h2>';

		  $resulthistory = $sms->SmsHistory($account);
		  rsort($resulthistory);
		  //print_r($resulthistory); // your code here ...

		  print '<table class="nopadding">';
		  print '<tr >';
		  //echo '<td>ID</td>';
		  echo '<th class="liste_titre" width="10%">Date</th>';
		  echo '<th class="liste_titre">Expediteur</th>';
		  echo '<th class="liste_titre">Destinataire</th>';
		  echo '<th class="liste_titre">Texte</th>';
		  echo '<th class="liste_titre">Status</th>';
		  //echo '<td>Message</td>';
		  //echo '<td>Etat</td>';
		  echo '</tr>';


		  $i=0;
		  while($resulthistory[$i]){
		  $var=!$var;
		  print '<tr '.$bc[$var].'>';

		     //echo '<td>'.$resulthistory[$i]->smsId.'</td>';
		     //date
		     $date = $resulthistory[$i]->date;
		     $an = substr($date,0,4);
		     $mois = substr($date,4,2);
		     $jour = substr($date,6,2);
		     $heure = substr($date,8,2);
		     $min = substr($date,10,2);
		     $sec = substr($date,12,2);

		     if (!empty($jour))
		     {echo '<td>'.$jour.'/'.$mois.'/'.$an.' '.$heure.':'.$min.':'.$sec.'</td>';}
		     else
		     {echo '<td>NC</td>';}
		     echo '<td>'.$resulthistory[$i]->numberFrom.'</td>';
		     echo '<td>'.$resulthistory[$i]->numberTo.'</td>';
		     echo '<td>'.$resulthistory[$i]->text.'</td>';
		     echo '<td>';
		      if ($resulthistory[$i]->status == "sent") { echo $langs->trans("OvhSmsStatutSent");}
            if ($resulthistory[$i]->status == "submitted") { echo $langs->trans('OvhSmsStatutSubmitted');}
            if ($resulthistory[$i]->status == "waiting") { echo $langs->trans('OvhSmsStatutWaiting');}
            if ($resulthistory[$i]->status == "delivery failed") { echo $langs->trans('OvhSmsStatutFailed');}

		     if ($resulthistory[$i]->status <> "sent" AND $resulthistory[$i]->status <> "submitted" AND $resulthistory[$i]->status <> "waiting" AND $resulthistory[$i]->status <> "delivery failed") {echo $resulthistory[$i]->status;}

		     echo '</td>';
		     echo '</tr>';

		     if ($i==$nbenvoi) {break;}
		     $i++;
		  }
		  print '</table>';


      //logout
      $sms->logout();
      }

  }








// End of page
$db->close();
llxFooter('$Date: 2010/12/04 01:32:57 $ - $Revision: 1.1 $');
?>
