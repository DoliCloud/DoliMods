<?php
/* Copyright (C) 2007-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-François FERRY  <jfefe@aternatik.fr>
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
// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
dol_include_once("/ovh/class/ovhsms.class.php");
dol_include_once("/ovh/lib/ovh.lib.php");
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;
use GuzzleHttp\Client as GClient;


$action=GETPOST('action', 'aZ09');

// Load traductions files requiredby by page
$langs->load("admin");
$langs->load("companies");
$langs->load("ovh@ovh");
$langs->load("sms");

if (!$user->admin) accessforbidden();
// Get parameters


// Protection if external user
if ($user->socid > 0) {
	//accessforbidden();
}

$urlexample='http://www.ovh.com/soapi/soapi-re-latest.wsdl';

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;    // Can be "soyoustart-eu" or "kimsufi-eu"

//$conf->global->OVH_OLDAPI =1;


/*
 * Actions
 */

if ($action == 'setvalue_account' && $user->admin) {
	if (! empty($conf->global->OVH_OLDAPI)) {
		$result=dolibarr_set_const($db, "OVHSMS_NICK", trim(GETPOST("OVHSMS_NICK")), 'chaine', 0, '', $conf->entity);
		$result=dolibarr_set_const($db, "OVHSMS_PASS", trim(GETPOST("OVHSMS_PASS")), 'chaine', 0, '', $conf->entity);
		$result=dolibarr_set_const($db, "OVHSMS_SOAPURL", trim(GETPOST("OVHSMS_SOAPURL")), 'chaine', 0, '', $conf->entity);
	} else {
		$result=dolibarr_set_const($db, "OVHC2C_ACCOUNT", trim(GETPOST("OVHC2C_ACCOUNT")), 'chaine', 0, '', $conf->entity);
		$result=dolibarr_set_const($db, "OVHSN_ACCOUNT", trim(GETPOST("OVHSN_ACCOUNT")), 'chaine', 0, '', $conf->entity);
	}
	if ($result >= 0) {
		$mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
	} else {
		dol_print_error($db);
	}
}




/*
 * View
 */

$WS_DOL_URL = empty($conf->global->OVHSMS_SOAPURL) ? '' : strval($conf->global->OVHSMS_SOAPURL);
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$c2cAccount = empty($conf->global->OVHC2C_ACCOUNT) ? '' : strval($conf->global->OVHC2C_ACCOUNT);
$snAccount = empty($conf->global->OVHSN_ACCOUNT) ? '' : strval($conf->global->OVHSN_ACCOUNT);

$oldLogin = empty($conf->global->OVHSMS_NICK) ? '' : strval($conf->global->OVHSMS_NICK);
$oldPassword = empty($conf->global->OVHSMS_PASS) ? '' : strval($conf->global->OVHSMS_PASS);

llxHeader('', $langs->trans('OvhSmsSetup'), '', '');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans("OvhSmsSetup"), $linkback, 'setup');

$head=ovhadmin_prepare_head();

if ($mesg) {
	if (preg_match('/class="error"/', $mesg)) dol_htmloutput_mesg($mesg, '', 'error');
	else {
		setEventMessages($mesg, null, 'mesgs');
	}
}


// Formulaire d'ajout de compte qui sera valable pour le click2dial
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="setvalue_account">';


dol_fiche_head($head, 'click2dial', $langs->trans("Ovh"), -1);


print '<div class="div-table-responsive">';
print '<table class="noborder centpercent">';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print '<td>&nbsp;</td>';
print "</tr>\n";


if (! empty($conf->global->OVH_OLDAPI) || ! empty($conf->global->OVH_OLDAPI_FORCLICK2DIAL)) {
	print '<tr class="oddeven"><td class="fieldrequired">';
	print $langs->trans("OvhSmsNick").'</td><td>';
	print '<input size="64" type="text" name="OVHSMS_NICK" value="'.dol_escape_htmltag($oldLogin).'">';
	print '</td><td>'.$langs->trans("Example").': AA123-OVH';
	print '</td></tr>';

	print '<tr class="oddeven"><td class="fieldrequired">';
	print $langs->trans("OvhSmsPass").'</td><td>';
	print '<input size="64" type="password" name="OVHSMS_PASS" value="'.dol_escape_htmltag($oldPassword).'">';
	print '</td><td></td></tr>';

	print '<tr class="oddeven"><td class="fieldrequired">';
	print $langs->trans("OvhSmsSoapUrl").'</td><td>';
	print '<input size="64" type="text" name="OVHSMS_SOAPURL" value="'.dol_escape_htmltag($WS_DOL_URL).'">';
	print '</td><td>'.$langs->trans("Example").': '.$urlexample;
	print '</td></tr>';
} else {
	print '<tr class="oddeven"><td class="fieldrequired">';
	$htmltext=$langs->trans("OvhTelAccountHelp");
	print $form->textwithpicto($langs->trans("OvhBillingAccount"), $htmltext).'</td><td>';
	print '<input size="64" type="text" name="OVHC2C_ACCOUNT" value="'.dol_escape_htmltag($c2cAccount).'">';
	print '</td><td><span class="opacitymedium">'.$langs->trans("Example").': nh123-ovh-1</span>';
	print '</td></tr>';

	print '<tr class="oddeven"><td>';
	$htmltext=$langs->trans("OvhServiceNameHelp");
	print $form->textwithpicto($langs->trans("OvhServiceName"), $htmltext).'</td><td>';
	print '<input size="64" type="text" name="OVHSN_ACCOUNT" value="'.dol_escape_htmltag($snAccount).'">';
	print '</td><td><span class="opacitymedium">'.$langs->trans("Example").': 0033123456789</span>';
	print '</td></tr>';
}

print '</table>';
print '</div>';

print '<br>';

// Show message
$message='';

$tmpurl='/ovh/wrapper.php?caller=__PHONEFROM__&called=__PHONETO__';
if (!getDolGlobalString('OVH_OLDAPI')) {
	$tmpurl.='&billingaccount='.(empty($c2cAccount)?'???':$c2cAccount).'&servicename='.(empty($snAccount)?'SIPLineNumber':$snAccount);
} else {
	$tmpurl.='&login=__LOGIN__&password=__PASS__';
}

print info_admin($langs->trans("IfYouChangeHereChangeAlsoClickToDial")).'<br>';

$url='<a href="'.dol_buildpath($tmpurl, 2).'" target="_blank">'.dol_buildpath($tmpurl, 2).'</a>';

print '<span class="opacitymedium">'.$langs->trans("ClickToDialLink", 'OVH', '').'</span>';

print '<div class="urllink">';
$message.=img_picto('', 'object_globe.png').' <input type="text" class="quatrevingtpercent" id="url" name="url" value="'.dol_escape_htmltag(dol_buildpath($tmpurl, 2)).'">';
if (function_exists('ajax_autoselect')) {
	$message.=ajax_autoselect('url');
}
print $message;
print '</div>';
print '<br>';

print $langs->trans("ToGoOnClickToDialSetup").': <a href="'.DOL_URL_ROOT.'/admin/clicktodial.php" target="setup">'.$langs->trans("ClickHere").'</a><br>';

dol_fiche_end();

print '<div class="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';

print '</form>';


llxFooter();

$db->close();
