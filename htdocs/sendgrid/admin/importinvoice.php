<?php
/* Copyright (C) 2018 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       htdocs/sendgrid/admin/importinvoice.php
 *		\ingroup    sendgrid
 *		\brief      Setup of module Sendgrid - Tab to import invoices
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

dol_include_once('/sendgrid/class/sendgrid.class.php');
dol_include_once("/sendgrid/lib/sendgrid.lib.php");
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/user/class/user.class.php";
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP


$langs->load("sendgrid@sendgrid");
$langs->load("admin");
$langs->load("companies");
$langs->load("sms");

$error=0;

$action = GETPOST('action', 'aZ09');

// Protection if external user
if ($user->societe_id > 0) accessforbidden();

$endpoint = empty($conf->global->SENDGRID_ENDPOINT)?'sendgrid-eu':$conf->global->SENDGRID_ENDPOINT;



/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin) {
	$result1=dolibarr_set_const($db, "SENDGRID_THIRDPARTY_IMPORT", GETPOST("SENDGRID_THIRDPARTY_IMPORT"), 'chaine', 0, '', $conf->entity);
	$result2=dolibarr_set_const($db, "SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID", GETPOST("SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID"), 'chaine', 0, '', $conf->entity);
	$result3 = dolibarr_set_const($db, "SENDGRID_DEFAULT_BANK_ACCOUNT", (GETPOST("SENDGRID_DEFAULT_BANK_ACCOUNT") > 0 ? GETPOST("SENDGRID_DEFAULT_BANK_ACCOUNT") : 0), 'chaine', 0, '', $conf->entity);
	if ($result1 >= 0 && $result2 >= 0 && $result3 >= 0) {
		$mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
	} else {
		dol_print_error($db);
	}
}




/*
 * View
 */

$form=new Form($db);

$WS_DOL_URL = $conf->global->SENDGRIDSMS_SOAPURL;
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$login = $conf->global->SENDGRIDSMS_NICK;
$password = $conf->global->SENDGRID_SMS_PASS;

$logindol=$user->login;



$morejs = '';
llxHeader('', $langs->trans('SendgridSetup'), '', '', '', '', $morejs, '', 0, 0);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans('SendgridSetup'), $linkback, 'setup');

$head=sendgridadmin_prepare_head();

dol_htmloutput_mesg($mesg);



// Formulaire d'ajout de compte SMS qui sera valable pour tout Dolibarr
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';


dol_fiche_head($head, 'getinvoices', $langs->trans("Sendgrid"), -1);

if (empty($conf->global->SENDGRIDAPPKEY)) {
	echo '<div class="warning">'.$langs->trans("SendgridAuthenticationPartNotConfigured").'</div>';
}

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print '<td>&nbsp;</td>';
print "</tr>\n";

/*
 $var=!$var;
 print '<tr '.$bc[$var].'><td class="fieldrequired">';
 print $langs->trans("UserMakingImport").'</td><td>';
 print '<input size="64" type="text" name="SENDGRID_USER_LOGIN" value="'.$logindol.'">';
 print '<td>';
 print '</td></tr>';
 */

print '<tr class="oddeven"><td class="fieldrequired">';
print $langs->trans("SupplierToUseForImport").'</td><td>';
print $form->select_company($conf->global->SENDGRID_THIRDPARTY_IMPORT, 'SENDGRID_THIRDPARTY_IMPORT', 's.fournisseur = 1', 1, 'supplier');
print '<td>';
print '</td></tr>';

if ($conf->product->enable || $conf->service->enabled) {
	print '<tr class="oddeven"><td>';
	print $langs->trans("ProductGenericToUseForImport").'</td><td>';
	print $form->select_produits($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID, 'SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID');
	print '<td>';
	print $langs->trans("KeepEmptyToSaveLinesAsFreeLines");
	print '</td></tr>';
}

if ($conf->banque->enabled) {
	print '<tr class="oddeven"><td>';
	print $langs->trans("SendGridDefaultBankAccount") . '</td><td>';
	$form->select_comptes($conf->global->SENDGRID_DEFAULT_BANK_ACCOUNT, 'SENDGRID_DEFAULT_BANK_ACCOUNT', 0, '', 1);
	print '<td>';
	//print $langs->trans("KeepEmptyToSaveLinesAsFreeLines");
	print '</td></tr>';
}

print '</table>';

dol_fiche_end();

print '<div class="center"><input type="submit" class="button" value="' . $langs->trans("Modify") . '"></div>';


print '</form>';

llxFooter();

$db->close();
