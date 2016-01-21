<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-François FERRY  <jfefe@aternatik.fr>
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
 *   	\file       htdocs/ovh/admin/ovh_click2dial.php
 *		\ingroup    ovh
 *		\brief      Configuration du module ovh
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
dol_include_once("/ovh/class/ovhsms.class.php");
dol_include_once("/ovh/lib/ovh.lib.php");
require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;


$action=GETPOST('action');

// Load traductions files requiredby by page
$langs->load("admin");
$langs->load("companies");
$langs->load("ovh@ovh");
$langs->load("sms");

if (!$user->admin) accessforbidden();
// Get parameters


// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;


/*
 * Actions
 */

if ($action == 'setvalue_account' && $user->admin)
{
    $result=dolibarr_set_const($db, "OVHC2C_ACCOUNT",$_POST["OVHC2C_ACCOUNT"],'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSN_ACCOUNT",$_POST["OVHSN_ACCOUNT"],'chaine',0,'',$conf->entity);
    
    if ($result >= 0)
    {
        $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
    }
    else
    {
        dol_print_error($db);
    }
}




/*
 * View
 */

$WS_DOL_URL = $conf->global->OVHSMS_SOAPURL;
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$login = $conf->global->OVHC2C_ACCOUNT;
$password = $conf->global->OVH_SMS_PASS;

llxHeader('',$langs->trans('OvhSmsSetup'),'','');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans("OvhSmsSetup"),$linkback,'setup');

$head=ovhadmin_prepare_head();

if ($mesg)
{
    if (preg_match('/class="error"/',$mesg)) dol_htmloutput_mesg($mesg,'','error');
    else
    {
        setEventMessages($mesg,null,'mesgs');
    }
}


if (empty($conf->global->OVH_NEWAPI) && (empty($conf->global->OVHC2C_ACCOUNT) || empty($WS_DOL_URL)))
{
    echo '<div class="warning">'.$langs->trans("OvhSmsNotConfigured").'</div>';
}
else
{
   // Formulaire d'ajout de compte SMS qui sera valable pour tout Dolibarr
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="setvalue_account">';
    
    
    dol_fiche_head($head, 'click2dial', $langs->trans("Ovh"));
   
    
    print '<table class="noborder" width="100%">';
    
    print '<tr class="liste_titre">';
    print '<td width="200px">'.$langs->trans("Parameter").'</td>';
    print '<td>'.$langs->trans("Value").'</td>';
    print '<td>&nbsp;</td>';
    print "</tr>\n";
    
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("OvhBillingAccount").'</td><td>';
    print '<input size="64" type="text" name="OVHC2C_ACCOUNT" value="'.$conf->global->OVHC2C_ACCOUNT.'">';
    print '<br>'.$langs->trans("Example").': nh123-ovh-1';
    //print '<td>'.'<a href="ovh_smsrecap.php" target="_blank">'.$langs->trans("ListOfSmsAccountsForNH").'</a>';
    print '</td></tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("OvhServiceName").'</td><td>';
    print '<input size="64" type="text" name="OVHSN_ACCOUNT" value="'.$conf->global->OVHSN_ACCOUNT.'">';
    //print '<br>'.$langs->trans("Example").': nh123-ovh-1';
    //print '<td>'.'<a href="ovh_smsrecap.php" target="_blank">'.$langs->trans("ListOfSmsAccountsForNH").'</a>';
    print '</td></tr>';
    
    print '</table>';    
    
    
    print '<br>';

    // Show message
    $message='';
    
    $tmpurl='/ovh/wrapper.php?login=__LOGIN__&password=__PASS__&caller=__PHONEFROM__&called=__PHONETO__';
    if (! empty($conf->global->OVH_NEWAPI)) 
    {
        $tmpurl.='&billingaccount='.$conf->global->OVHC2C_ACCOUNT.'&servicename='.$conf->global->OVHSN_ACCOUNT;
    }
        
    $url='<a href="'.dol_buildpath($tmpurl,2).'" target="_blank">'.dol_buildpath($tmpurl,2).'</a>';
    $message.=img_picto('','object_globe.png').' '.$langs->trans("ClickToDialLink",'OVH',$url);
    $message.='<br>';
    $message.='<br>';
    print $message;

    
    dol_fiche_end();
    
    print '<div class="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';
    
    print '</form>';
    
}



llxFooter();

$db->close();

