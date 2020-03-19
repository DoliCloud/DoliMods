<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-Francois FERRY  <jf.ferry@aternatik.fr>
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
 *
 * https://www.ovh.com/fr/soapi-to-apiv6-migration/
 */

/**
 *   	\file       ovh/admin/ovhsms_recap.php
 *		\ingroup    ovhsms
 *		\brief      Configuration du module ovhsms
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

include_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
dol_include_once("/ovh/class/ovhsms.class.php");
require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;
use GuzzleHttp\Client as GClient;


// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("ovh@ovh");
$langs->load("sms");

if (!$user->admin) accessforbidden();

// Get parameters
$account = GETPOST("account");

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;    // Can be "soyoustart-eu" or "kimsufi-eu"



/*
 * Actions
 */

// None



/*
 * View
 */

llxHeader('',$langs->trans('OvhSmsRecap'),'','');

$linkback='<a href="'.dol_buildpath('/ovh/admin/ovh_sms_setup.php',1).'">'.$langs->trans("OvhSmsBackToAdmin").'</a>';
print_fiche_titre($langs->trans("OvhSmsRecap"),$linkback,'setup');
print '<br>';

$var=true;


$sms = new OvhSms($db);
if (! empty($sms))  // Do not use here sms > 0 as a constructor return an object
{
    //telephonySmsAccountList
    $telephonySmsAccountList = $sms->getSmsListAccount($sms->session);

    print '<table class="liste     centpercent" width="100%">';
    print '<tr class="liste_titre"><td>'.$langs->trans("Account").'</td>';
    print '<td>'.$langs->trans("SendersAllowed").'</td>';
    print '<td align="right">'.$langs->trans("NbSmsLeft").'</td>';
    print "</tr>\n";

    foreach ($telephonySmsAccountList as $accountlisted)
    {
        print '<tr class="oddeven">';
        print '<td>';
        print '<a href="'.$_SERVER["PHP_SELF"].'?account='.$accountlisted.'">'.$accountlisted.'</a>';
        print '</td>';
        print '<td>';
        $sms->account=$accountlisted;
        $result=$sms->SmsSenderList();
        $i=0;
        foreach($result as $val)
        {
            if (! empty($conf->global->OVH_OLDAPI)) print ($val->status=='enable'?'':'<strike>');
            print $val->number.(empty($val->description)?'':' ('.$val->description.')');
            if (! empty($conf->global->OVH_OLDAPI)) print ($val->status=='enable'?'':'</strike>');
            $i++;
            if ($i < count($result)) print ', ';
        }
        print '</td>';
        print '<td align="right">';
        // Ask credit left for account
        $sms->account=$accountlisted;
        print $sms->CreditLeft();
        print '</td>';
        print '</tr>';
    }
    print '</table>';



    if (!empty($account))
    {
        // $stopafternbenvoi = 0;

        //telephonySmsHistory
        print '<br>';
        print_fiche_titre($langs->trans('OvhSmsHistory').' ('.$account.')','','');

        $resulthistory = $sms->SmsHistory($account);
        rsort($resulthistory);

        print '<table class="liste centpercent">';
        print '<tr class="liste_titre">';
        if (empty($conf->global->OVH_OLDAPI)) echo '<th class="liste_titre">ID</th>';
        echo '<th class="liste_titre">'.$langs->trans("Date").'</th>';
        echo '<th class="liste_titre">'.$langs->trans("Sender").'</th>';
        echo '<th class="liste_titre">'.$langs->trans("Recipient").'</th>';
        echo '<th class="liste_titre">'.$langs->trans("Text").'</th>';
        if (! empty($conf->global->OVH_OLDAPI)) echo '<th class="liste_titre">'.$langs->trans("Status").'</th>';
        //echo '<td>Message</td>';
        //echo '<td>Etat</td>';
        echo '</tr>';


        $i=0;
        while (isset($resulthistory[$i]) && $i < 50)
        {
            print '<tr class="oddeven">';

            if (! empty($conf->global->OVH_OLDAPI))
            {
                //date
                $date = $resulthistory[$i]->date;
                $an = substr($date,0,4);
                $mois = substr($date,4,2);
                $jour = substr($date,6,2);
                $heure = substr($date,8,2);
                $min = substr($date,10,2);
                $sec = substr($date,12,2);

                if (!empty($jour))
                {
                    echo '<td>'.$date.'</td>';
                }
                else
                {
                    echo '<td>NC</td>';
                }
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
            }
            else
            {
                print '<td>'.$resulthistory[$i].'</td>';

                $resultinfo = $sms->conn->get('/sms/'.$sms->account.'/outgoing/'.$resulthistory[$i]);
                $resultinfo = json_decode(json_encode($resultinfo), true);

                echo '<td>'.$resultinfo['creationDatetime'].'</td>';
                echo '<td>'.$resultinfo['sender'].'</td>';
                echo '<td>'.$resultinfo['receiver'].'</td>';
                echo '<td>'.$resultinfo['message'].'</td>';
                /*echo '<td>';
                if ($resulthistory[$i]->status == "sent") { echo $langs->trans("OvhSmsStatutSent");}
                if ($resulthistory[$i]->status == "submitted") { echo $langs->trans('OvhSmsStatutSubmitted');}
                if ($resulthistory[$i]->status == "waiting") { echo $langs->trans('OvhSmsStatutWaiting');}
                if ($resulthistory[$i]->status == "delivery failed") { echo $langs->trans('OvhSmsStatutFailed');}
                if ($resulthistory[$i]->status <> "sent" AND $resulthistory[$i]->status <> "submitted" AND $resulthistory[$i]->status <> "waiting" AND $resulthistory[$i]->status <> "delivery failed") {echo $resulthistory[$i]->status;}
                echo '</td>';
                */
                echo '</tr>';

            }

            $i++;
            if ($i == $stopafternbenvoi) {break;}
        }
        print '</table>';


        //logout
        $sms->logout();
    }

}


// End of page
llxFooter();

$db->close();
