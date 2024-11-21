<?php
/* Copyright (C) 2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/skincoloreditor/admin/advancededitor.php
 *      \ingroup    skincoloreditor
 *      \brief      Page to setup module SkicolorEditor
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';


if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("skincoloreditor@skincoloreditor");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");



/*
 * Actions
 */

if (preg_match('/^set/', $action)) {
	// This is to force to add a new param after css urls to force new file loading
	// This set must be done before calling llxHeader().
	$_SESSION['dol_resetcache']=dol_print_date(dol_now(), 'dayhourlog');
}

if ($action == 'set') {
	$name = GETPOST("name");
	$value = GETPOST("value");
	$res = dolibarr_set_const($db, $name, $value, 'chaine', 0, '', $conf->entity);

	if (! $res > 0) $error++;
	if (! $error) {
		$mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
	} else {
		$mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
	}
}




/**
 * View
 */

$formfile=new FormFile($db);

llxHeader('', 'SkinColorEditor', $linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SkinColorEditorSetup"), $linkback, 'setup');
print '<br>';

print $langs->trans("SkinColorEditorDesc").'<br>';
print '<br>';

print $langs->trans("ActivateColorPersonalizing").': &nbsp; ';
$name='THEME_ELDY_ENABLE_PERSONALIZED';
if (empty($conf->global->$name)) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off');
	print '</a>';
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on');
	print '</a>';
}
print '<br><br><br>';

if ($conf->theme != 'eldy') {
	print '<div class="warning">'.img_warning().' '.$langs->trans("WarningSkinMustBeEldy", $conf->theme).'</div>';
}

dol_htmloutput_mesg($mesg);


if (! empty($conf->global->THEME_ELDY_ENABLE_PERSONALIZED)) {
	$head=array();
	$h=0;

	$head[$h][0] = dol_buildpath("/skincoloreditor/admin/quickeditor.php", 1);
	$head[$h][1] = $langs->trans("SkinColorEditorFastEditor");
	$head[$h][2] = 'fasteditor';
	$h++;

	$head[$h][0] = dol_buildpath("/skincoloreditor/admin/advancededitor.php", 1);
	$head[$h][1] = $langs->trans("SkinColorEditorAdvancedEditor");
	$head[$h][2] = 'advancededitor';
	$h++;

	dol_fiche_head($head, 'advancededitor', '', -1);

	print $langs->trans("FeatureNotYetAvailable");

	dol_fiche_end();
}


llxFooter();

if (is_object($db)) $db->close();
