<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2019 Alice Adminson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    revertinvoice/admin/setup.php
 * \ingroup revertinvoice
 * \brief   RevertInvoice setup page.
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/revertinvoice.lib.php';
//require_once "../class/myclass.class.php";

// Translations
$langs->loadLangs(array("admin", "revertinvoice@revertinvoice"));

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

$revertinvoicethirdpartyid = GETPOST('revertinvoicethirdpartyid', 'int');
$revertinvoiceentityid = GETPOST('revertinvoiceentityid', 'int');

$arrayofparameters=array(
	'REVERTINVOICE_MYPARAM1'=>array('css'=>'minwidth200','enabled'=>1),
	'REVERTINVOICE_MYPARAM2'=>array('css'=>'minwidth500','enabled'=>1)
);



/*
 * Actions
 */

if ((float) DOL_VERSION >= 6) {
	include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
}

if ($action == 'addrevertinvoice') {
	if ($revertinvoicethirdpartyid > 0) {
		$result = dolibarr_set_const($db, 'REVERTINVOICE_THIRDPARTYID_'.$revertinvoicethirdpartyid, $revertinvoiceentityid, 'chaine', 0, '', 0);
	} else {
		setEventMessages('Select a thirdparty', null, 'errors');
	}
}

if ($action == 'delete') {
	if ($revertinvoicethirdpartyid > 0) {
		$const = 'REVERTINVOICE_THIRDPARTYID_'.$revertinvoicethirdpartyid;
		var_dump($const);
		$result = dolibarr_del_const($db, $const, 0);

		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
}


/*
 * View
 */

$form = new Form($db);

$page_name = "RevertInvoiceSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage?$backtopage:DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_revertinvoice@revertinvoice');

// Configuration header
$head = revertinvoiceAdminPrepareHead();
dol_fiche_head($head, 'settings', '', -1, "revertinvoice@revertinvoice");

// Setup page goes here
echo '<span class="opacitymedium">'.$langs->trans("RevertInvoiceSetupPage").'</span><br><br>';


print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="addrevertinvoice">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("ThirdParty").'</td><td>'.$langs->trans("Entity").'</td><td></td></tr>';

print '<tr class="oddeven"><td>';
print $form->select_company('', 'revertinvoicethirdpartyid');
print '</td><td>';
print '<input type="text" name="revertinvoiceentityid" value="1" class="maxwidth50">';
print '</td><td>';
print '<input class="button" type="submit" value="'.$langs->trans("Add").'">';
print '</td></tr>';

$i=0;

foreach ($conf->global as $key => $value) {
	if (preg_match('/REVERTINVOICE_THIRDPARTYID_(.*)/', $key, $reg)) {
		$thirdpartyid = $reg[1];
		$const = 'REVERTINVOICE_THIRPDARTYID_'.$thirdpartyid;
		$entityid = $value;

		$tmpcompany = new Societe($db);
		$tmpcompany->fetch($thirdpartyid);

		print '<tr class="oddeven"><td>';
		print $tmpcompany->getNomUrl(1);
		print '</td><td>';
		print $value;
		// Label of entity
		if (is_object($mc)) {
			$mc->getInfo($value);
			print ' - '.$mc->label;
		}
		print '</td><td>';
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=delete&token='.newToken().'&revertinvoicethirdpartyid='.$thirdpartyid.'">';
		print img_delete();
		print '</a>';
		print '</td></tr>';
	}
}
print '</table>';

print '</form>';
print '<br>';


// Page end
dol_fiche_end();

llxFooter();
$db->close();
