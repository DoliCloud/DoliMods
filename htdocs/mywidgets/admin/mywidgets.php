<?php
/* Copyright (C) 2008-2015	Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/billedonorders/admin/billedonorders.php
 *      \ingroup    billedonorders
 *      \brief      Page to setup module billedonorders
 */

define('NOCSRFCHECK', 1);

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

$langs->load("admin");
$langs->load("other");
$langs->load("billedonorders@billedonorders");

$def = array();
$action=GETPOST('action', 'alpha');
$confirm=GETPOST('confirm', 'alpha');
$actionsave=GETPOST('save', 'alpha');


/*
 * Actions
 */

if ($action == 'update') {
	$res=dolibarr_set_const($db, 'BILLEDONORDERS_DISABLE_BILLEDWOTAX', GETPOST("BILLEDONORDERS_DISABLE_BILLEDWOTAX"), 'texte', 0, '', $conf->entity);

	$res=dolibarr_set_const($db, 'BILLEDONORDERS_DISABLE_BILLED', GETPOST("BILLEDONORDERS_DISABLE_BILLED"), 'texte', 0, '', $conf->entity);

	$res=dolibarr_set_const($db, 'BILLEDONORDERS_DISABLE_PAYED', GETPOST("BILLEDONORDERS_DISABLE_PAYED"), 'texte', 0, '', $conf->entity);

	$res=dolibarr_set_const($db, 'BILLEDONORDERS_DISABLE_REMAINTOPAY', GETPOST("BILLEDONORDERS_DISABLE_REMAINTOPAY"), 'texte', 1, '', $conf->entity);

	if ($res == 1) $mesg=$langs->trans("RecordModifiedSuccessfully");
	else {
		dol_print_error($db);
	}
}


/*
 * View
 */

$form=new Form($db);
$formfile=new FormFile($db);

$linktohelp = '';

llxHeader('', 'mywidgets', $linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("Setup"), $linkback, 'setup');
print '<br>';

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



print '<form name="cabinetmed" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="update">';

dol_fiche_head($head, 'tabsetup', '', -1);

//print $langs->trans("BilledOnOrdersNothingToSetup");
$var=true;

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("BILLEDONORDERS_DISABLE_BILLEDWOTAX").'</td>';
print '<td>'.$form->selectyesno('BILLEDONORDERS_DISABLE_BILLEDWOTAX', $conf->global->BILLEDONORDERS_DISABLE_BILLEDWOTAX, 1).'</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("BILLEDONORDERS_DISABLE_BILLED").'</td>';
print '<td>'.$form->selectyesno('BILLEDONORDERS_DISABLE_BILLED', $conf->global->BILLEDONORDERS_DISABLE_BILLED, 1).'</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("BILLEDONORDERS_DISABLE_PAYED").'</td>';
print '<td>'.$form->selectyesno('BILLEDONORDERS_DISABLE_PAYED', $conf->global->BILLEDONORDERS_DISABLE_PAYED, 1).'</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("BILLEDONORDERS_DISABLE_REMAINTOPAY").'</td>';
print '<td>'.$form->selectyesno('BILLEDONORDERS_DISABLE_REMAINTOPAY', $conf->global->BILLEDONORDERS_DISABLE_REMAINTOPAY, 1).'</td>';
print '</tr>';

print '</table>';

dol_fiche_end();

print '<div class="center"><input type="submit" name="save" value="'.$langs->trans("Save").'" class="button"></div>';
print '</form>';


// Footer
llxFooter();
// Close database handler
$db->close();
