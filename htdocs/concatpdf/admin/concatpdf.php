<?php
/* Copyright (C) 2008-2019	Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2012		Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	    \file       htdocs/concatpdf/admin/concatpdf.php
 *      \ingroup    concatpdf
 *      \brief      Page to setup module ConcatPdf
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
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';


if (!$user->admin) accessforbidden();

$langs->loadLangs(array("admin", "other", "concatpdf@concatpdf", "supplier_proposal", "propal", "orders", "bills"));

$def = array();
$action=GETPOST('action', 'alpha');
$confirm=GETPOST('confirm', 'alpha');
$actionsave=GETPOST('save', 'alpha');

// Define list of object supported
$modules = array();
if (!empty($conf->propal->enabled)) $modules['proposals']=array('label'=>'Proposals', 'picto'=>img_picto('', 'propal', 'class="pictofixedwidth"'));
if (!empty($conf->commande->enabled)) $modules['orders']=array('label'=>'Orders', 'picto'=>img_picto('', 'order', 'class="pictofixedwidth"'));
if (!empty($conf->facture->enabled)) $modules['invoices']=array('label'=>'Invoices', 'picto'=>img_picto('', 'bill', 'class="pictofixedwidth"'));
if (!empty($conf->supplier_proposal->enabled)) $modules['supplier_proposals']=array('label'=>'SupplierProposals', 'picto'=>img_picto('', 'supplier_proposal', 'class="pictofixedwidth"'));
if (!empty($conf->fournisseur->enabled)) $modules['supplier_orders']=array('label'=>'SuppliersOrders', 'picto'=>img_picto('', 'supplier_order', 'class="pictofixedwidth"'));
if (!empty($conf->fournisseur->enabled)) $modules['supplier_invoices']=array('label'=>'SuppliersInvoices', 'picto'=>img_picto('', 'supplier_invoice', 'class="pictofixedwidth"'));
if (!empty($conf->contract->enabled)) $modules['contracts']=array('label'=>'Contracts', 'picto'=>img_picto('', 'contract', 'class="pictofixedwidth"'));
// Add key data-html
foreach($modules as $key => $value) {
	$modules[$key]['data-html'] = dol_escape_htmltag($value['picto'].$langs->transnoentitiesnoconv($value['label']));
}

if (empty($conf->concatpdf->enabled)) {
	accessforbidden();
}


/*
 * Actions
 */

$reg = array();
if (preg_match('/set_(.*)/', $action, $reg)) {
	$code=$reg[1];
	if (dolibarr_set_const($db, $code, 1, 'chaine', 0, '', 0) > 0) {
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	} else {
		dol_print_error($db);
	}
}

if (preg_match('/del_(.*)/', $action, $reg)) {
	$code=$reg[1];
	if (dolibarr_del_const($db, $code, 0) > 0) {
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	} else {
		dol_print_error($db);
	}
}

// Send file
if (GETPOST('sendit') && ! empty($conf->global->MAIN_UPLOAD_DOC)) {
	$error=0;
	if (! GETPOST('module', 'alpha') || is_numeric(GETPOST('module', 'alpha'))) {
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Type")), 'warnings');
	}

	if (! $error) {
		if (is_array($_FILES['userfile']['name'])) {
			$listoffiles=$_FILES['userfile']['name'];
		} else {
			$listoffiles=array($_FILES['userfile']['name']);
		}

		foreach ($listoffiles as $key => $filename) {
			if (preg_match('/\.pdf$/i', $filename)) {
				$upload_dir = $conf->concatpdf->dir_output.'/'.GETPOST('module', 'alpha');
				if (dol_mkdir($upload_dir) >= 0) {
					if (is_array($_FILES['userfile']['name'])) {
						$tmp_name = $_FILES['userfile']['tmp_name'][$key];
						$fileerror = $_FILES['userfile']['error'][$key];
					} else {
						$tmp_name = $_FILES['userfile']['tmp_name'];
						$fileerror = $_FILES['userfile']['error'];
					}

					$resupload=dol_move_uploaded_file($tmp_name, $upload_dir . "/" . $filename, 0, 0, $fileerror);
					if (is_numeric($resupload) && $resupload > 0) {
						setEventMessage($langs->trans("FileTransferComplete"), 'mesgs');
					} else {
						$langs->load("errors");
						if ($resupload < 0) {	// Unknown error
							setEventMessage($langs->trans("ErrorFileNotUploaded"), 'mesgs');
						} elseif (preg_match('/ErrorFileIsInfectedWithAVirus/', $resupload)) {	// Files infected by a virus
							setEventMessage($langs->trans("ErrorFileIsInfectedWithAVirus"), 'mesgs');
						} else // Known error
						{
							setEventMessage($langs->trans($resupload), 'errors');
						}
					}
				} else {
					$langs->load('errors');
					setEventMessage($langs->trans("ErrorFailToCreateDir", $upload_dir), 'errors');
				}
			} else {
				setEventMessage($langs->trans("ErrorFileMustBeAPdf"), 'errors');
			}
		}
	}
}

// Delete file
if ($action == 'confirm_deletefile' && $confirm == 'yes') {
	$file = $conf->concatpdf->dir_output . "/" . GETPOST('urlfile');	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).

	$ret=dol_delete_file($file);
	if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
	else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
	header('Location: '.$_SERVER["PHP_SELF"]);
	exit;
}

if ($action == 'save') {
	dolibarr_set_const($db, 'CONCATPDF_PRESELECTED_MODELS', GETPOST('CONCATPDF_PRESELECTED_MODELS'), 'chaine', 0, '', 0);
}

/*
 * View
 */

$form=new Form($db);
$formfile=new FormFile($db);

llxHeader('', 'ConcatPdf', $linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ConcatPdfSetup"), $linkback, 'setup');

clearstatcache();


$h=0;
$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("Setup");
$head[$h][2] = 'tabsetup';
$h++;

$head[$h][0] = 'about.php';
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;

if ((float) DOL_VERSION < 8.0) {
	dol_fiche_head($head, 'tabsetup', '');
} else {
	dol_fiche_head($head, 'tabsetup', '', -1);
}

if (! empty($conf->global->PDF_SECURITY_ENCRYPTION)) {
	print info_admin($langs->trans("WarningConcatPDFIsNotCompatibleWithOptionReadOnlyPDF", $langs->transnoentities("ProtectAndEncryptPdfFiles")), 0, 0, '1', 'error');
}

/*
 * Confirmation suppression fichier
 */
if ($action == 'remove_file') {
	print $form->formconfirm($_SERVER["PHP_SELF"].'?&urlfile='.urlencode(GETPOST("file")), $langs->trans('DeleteFile'), $langs->trans('ConfirmDeleteFile'), 'confirm_deletefile', '', 0, 1);
}

// Show dir for each module
print '<div class="opacitymedium">';
print $langs->trans("ConcatPDfTakeFileFrom").'<br><br>';
foreach ($modules as $module => $moduletrans) {
	$outputdir=$conf->concatpdf->dir_output.'/'.$module;
	print '* '.str_replace('{s1}', $moduletrans['picto'], $langs->trans("ConcatPDfTakeFileFrom2", '{s1}'.$langs->transnoentitiesnoconv($moduletrans['label']), $outputdir));
	print '<br>';
}
print '</div>';
print '<br>';

// Show for to add a file
$select_module = $form->selectarray('module', $modules, GETPOST('module'), 1, 0, 0, '', 1);

$formfile->form_attach_new_file($_SERVER['PHP_SELF'], $langs->trans("AddFilesToConcat"), 0, 0, 1, 50, '', $select_module, false, '', 0);


// Show option for CONCATPDF_MULTIPLE_CONCATENATION_ENABLED
if (! empty($conf->global->MAIN_USE_JQUERY_MULTISELECT)) {
	print '<br>';

	$form=new Form($db);
	$var=true;
	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Parameters").'</td>'."\n";
	print '<td align="center" width="20">&nbsp;</td>';
	print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";
	print '</tr>';

	/*
	 * Parameters form
	 */

	// Use multiple concatenation
	print '<tr class="oddeven">';
	print '<td>'.$langs->trans("EnableMultipleConcatenation").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	if (! empty($conf->use_javascript_ajax)) {
		print ajax_constantonoff('CONCATPDF_MULTIPLE_CONCATENATION_ENABLED', '', 0);
	} else {
		if (empty($conf->global->CONCATPDF_MULTIPLE_CONCATENATION_ENABLED)) {
			print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CONCATPDF_MULTIPLE_CONCATENATION_ENABLED">'.img_picto($langs->trans("Disabled"), 'off').'</a>';
		} else {
			print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_CONCATPDF_MULTIPLE_CONCATENATION_ENABLED">'.img_picto($langs->trans("Enabled"), 'on').'</a>';
		}
	}
	print '</td></tr>';

	print '</table>';
}


dol_fiche_end();


print '<br><br>';


foreach ($modules as $module => $moduletrans) {
	$outputdir=$conf->concatpdf->dir_output.'/'.$module;
	$listoffiles=dol_dir_list($outputdir, 'files', 0, '', array('^SPECIMEN\.pdf$'));
	if (count($listoffiles)) {
		print $formfile->showdocuments('concatpdf', $module, $outputdir, $_SERVER["PHP_SELF"].'?module='.$module, 0, $user->admin, '', 0, 0, 0, 0, 0, '', $moduletrans['picto'].$langs->trans("PathDirectory").' '.$outputdir);
	} else {
		print '<div class="titre">'.$moduletrans['picto'].$langs->trans("PathDirectory").' '.$outputdir.' :</div>';
		print '<span class="opacitymedium">'.$langs->trans("NoPDFFileFound").'</span><br>';
	}

	print '<br><br>';
}

// TODO
// Replace this with a checkbox on each file line
print '<form action="'.$_SERVER["PHP_SELF"].'" mode="POST">';
print '<input type="hidden" name="page_y">';
print '<input type="hidden" name="action" value="save">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<div class="">';
print 'CONCATPDF_PRESELECTED_MODELS = <input type="text" class="minwidth300" name="CONCATPDF_PRESELECTED_MODELS" value="'.getDolGlobalString('CONCATPDF_PRESELECTED_MODELS').'">';
print '<input type="submit" class="button smallpaddingimp reposition" value="'.$langs->trans("Save").'">';
print '</div>';
print '</form>';

print '<br><br>';

// Footer
llxFooter();
// Close database handler
$db->close();
