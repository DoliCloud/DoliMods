<?php
/* Copyright (C) 2008-2013	Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/partipirate/index.php
 *      \ingroup    partipirate
 *      \brief      Page index PartiPirate
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
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';


if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("partipirate@partipirate");

$def = array();
$action=GETPOST('action', 'alpha');
$confirm=GETPOST('confirm', 'alpha');
$actionsave=GETPOST('save', 'alpha');

$modules = array();


/*
 * Actions
 */

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

	$upload_dir = $conf->partipirate->dir_temp;

	if (dol_mkdir($upload_dir) >= 0) {
		$resupload=dol_move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_dir."/".$_FILES['userfile']['name'], 1, 0, $_FILES['userfile']['error']);
		if (is_numeric($resupload) && $resupload > 0) {
			setEventMessage($langs->trans("FileTransferComplete"), 'mesgs');
			$showmessage=1;
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
	}

	if ($error) {
		setEventMessage($langs->trans("ErrorFileNotUploaded"), 'errors');
	}
}

// Delete file
if ($action == 'remove_file') {
	$file = $conf->partipirate->dir_temp . "/" . GETPOST('file');	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).

	$ret=dol_delete_file($file);
	if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('file')));
	else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('file')), 'errors');
	header('Location: '.$_SERVER["PHP_SELF"]);
	exit;
}


/*
 * View
 */

$form=new Form($db);
$formfile=new FormFile($db);

llxHeader('', 'PartiPirate', $linktohelp);

print_fiche_titre($langs->trans("Page de fonctionnalités spécifiques au PartiPirate"))."\n";
print '<br>';

$formfile->form_attach_new_file($_SERVER['PHP_SELF'], $langs->trans("ImporterFichierAdherentsOuCotisations"), 0, 0, 1, 50, '', '', false);

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

if ($showmessage) {
	print 'Pour importer ce fichier, lancer la commande suivante depuis la ligne de commande<br>';
	print '<textarea class="flat" cols="120">';
	print 'php '.preg_replace('#('.preg_quote('htdocs/partipirate/').'|'.preg_quote('htdocs\partipirate/').')$#', '', $path).'scripts/partipirate/import-adherent-cotisation.php '.$user->login.' &lt;membertyperef&gt; '.$upload_dir . "/" . $_FILES['userfile']['name'];
	print '</textarea>';
}

print '<br>';
print $formfile->showdocuments('partipirate_temp', '', $conf->partipirate->dir_temp, $_SERVER["PHP_SELF"], 0, 1);

// Footer
llxFooter();
// Close database handler
$db->close();
