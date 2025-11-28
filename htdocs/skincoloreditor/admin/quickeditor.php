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
 *	    \file       htdocs/skincoloreditor/admin/quickeditor.php
 *      \ingroup    skincoloreditor
 *      \brief      Page to setup module SkincolorEditor
 */

define('NOCSRFCHECK', 1);

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';


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
		setEventMessage($langs->trans("SetupSaved"));
	} else {
		setEventMessage($langs->trans("Error"), 'errors');
	}
}

if ($action == 'setcolor') {
	//$res = dolibarr_set_const($db, 'THEME_ELDY_RGB', GETPOST('THEME_ELDY_RGB'),'chaine',0,'',$conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_FONT_SIZE1', GETPOST('THEME_ELDY_FONT_SIZE1'), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_USE_HOVER', GETPOST('THEME_ELDY_USE_HOVER'), 'chaine', 0, '', $conf->entity);

	$res = dolibarr_set_const($db, 'THEME_ELDY_TOPMENU_BACK1', join(',', colorStringToArray(GETPOST('THEME_ELDY_TOPMENU_BACK1'), array())), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_VERMENU_BACK1', join(',', colorStringToArray(GETPOST('THEME_ELDY_VERMENU_BACK1'), array())), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_BACKBODY', join(',', colorStringToArray(GETPOST('THEME_ELDY_BACKBODY'), array())), 'chaine', 0, '', $conf->entity);

	$res = dolibarr_set_const($db, 'THEME_ELDY_BACKTABCARD1', join(',', colorStringToArray(GETPOST('THEME_ELDY_BACKTABCARD1'), array())), 'chaine', 0, '', $conf->entity);

	// Tables
	$res = dolibarr_set_const($db, 'THEME_ELDY_BACKTITLE1', join(',', colorStringToArray(GETPOST('THEME_ELDY_BACKTITLE1'), array())), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_LINEIMPAIR1', join(',', colorStringToArray(GETPOST('THEME_ELDY_LINEIMPAIR1'), array())), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_LINEPAIR1', join(',', colorStringToArray(GETPOST('THEME_ELDY_LINEPAIR1'), array())), 'chaine', 0, '', $conf->entity);

	if (! $res > 0) $error++;
	if (! $error) {
		setEventMessage($langs->trans("SetupSaved"));
	} else {
		setEventMessage($langs->trans("Error"), 'errors');
	}
}



/**
 * View
 */

$formother=new FormOther($db);

llxHeader('', 'SkinColorEditor', $linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SkinColorEditorSetup"), $linkback, 'setup');
print '<br>';

print $langs->trans("SkinColorEditorDesc").'<br>';
print '<br>';


print '<form name="formcolor" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="action" value="setcolor">';


$head=array();
$h=0;

$head[$h][0] = dol_buildpath("/skincoloreditor/admin/quickeditor.php", 1);
$head[$h][1] = $langs->trans("ColorEditor");
$head[$h][2] = 'fasteditor';
$h++;

$head[$h][0] = 'about.php';
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;

dol_fiche_head($head, 'fasteditor', '', -1);


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


print '<br>';

if ($conf->theme != 'eldy') {
	print '<br>';
	print '<div class="warning">'.img_warning().' '.$langs->trans("WarningSkinMustBeEldy", $conf->theme).'</div>';
	print '<br>';
} elseif (! empty($user->conf->THEME_ELDY_ENABLE_PERSONALIZED)) {
	print '<br>';
	print '<div class="warning">'.img_warning().' '.$langs->trans("YourUseHasPersonalized", dol_buildpath('/skincoloreditor/usercolors.php', 1).'?id='.$user->id, $langs->transnoentitiesnoconv("ColorEditor")).'</div>';
	print '<br>';
}
print '<br>';



if (! empty($conf->global->THEME_ELDY_ENABLE_PERSONALIZED)) {
	/*
	$head[$h][0] = dol_buildpath("/skincoloreditor/admin/advancededitor.php",1);
	$head[$h][1] = $langs->trans("SkinColorEditorAdvancedEditor");
	$head[$h][2] = 'advancededitor';
	$h++;
	*/


	//print $langs->trans("SelectMainColor").' ';
	//$defcolor=dechex(235).dechex(235).dechex(235);
	//if (isset($conf->global->THEME_ELDY_RGB)) $defcolor=$conf->global->THEME_ELDY_RGB;
	// Color
	//print $formother->selectColor($defcolor,'THEME_ELDY_RGB','formcolor',1).'<br><br>';

	// Font size
	print $langs->trans("FontSize").': <input type="text" class="flat" name="THEME_ELDY_FONT_SIZE1" size="4" value="' . getDolGlobalString('THEME_ELDY_FONT_SIZE1').'"><br>';
	print '<br>';

	if (versioncompare(versiondolibarrarray(), array(3,8,-3)) >= 0) {
		print $langs->trans("SelectTabColor").' ';
		print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_BACKTABCARD1, array()), ''), 'THEME_ELDY_BACKTABCARD1', 'formcolor', 1).'<br><br>';
	}

	print $langs->trans("BackgroundColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_BACKBODY, array()), ''), 'THEME_ELDY_BACKBODY', 'formcolor', 1).'<br><br>';

	print $langs->trans("TopMenuBackgroundColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_TOPMENU_BACK1, array()), ''), 'THEME_ELDY_TOPMENU_BACK1', 'formcolor', 1).'<br><br>';
	//print $langs->trans("TopMenuFontColor").' ';
	//print $formother->selectColor($conf->global->THEME_ELDY_TOPMENU_BACK1,'THEME_ELDY_TOPMENU_BACK1','formcolor',1).'<br><br>';

	print $langs->trans("LeftMenuBackgroundColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_VERMENU_BACK1, array()), ''), 'THEME_ELDY_VERMENU_BACK1', 'formcolor', 1).'<br><br>';
	//print $langs->trans("LeftMenuFontColor").' ';
	//print $formother->selectColor($conf->global->THEME_ELDY_TOPMENU_BACK1,'THEME_ELDY_TOPMENU_BACK1','formcolor',1).'<br><br>';

	print $langs->trans("BackgroundTableTitleColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_BACKTITLE1, array()), ''), 'THEME_ELDY_BACKTITLE1', 'formcolor', 1).'<br><br>';

	print $langs->trans("BackgroundTableLineOddColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_LINEIMPAIR1, array()), ''), 'THEME_ELDY_LINEIMPAIR1', 'formcolor', 1).'<br><br>';

	print $langs->trans("BackgroundTableLineEvenColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_LINEPAIR1, array()), ''), 'THEME_ELDY_LINEPAIR1', 'formcolor', 1).'<br><br>';

	if (versioncompare(versiondolibarrarray(), array(3,9,-3)) >= 0) {
		// Use hover
		print $langs->trans("UseHoverOnLists").' ';
		print $formother->selectColor(colorArrayToHex(colorStringToArray($conf->global->THEME_ELDY_USE_HOVER, array()), ''), 'THEME_ELDY_USE_HOVER', 'formcolor', 1).'<br><br>';
	} else {
		// Use hover
		print $langs->trans("UseHoverOnLists").': <input type="checkbox" class="flat" name="THEME_ELDY_USE_HOVER" '.(empty($conf->global->THEME_ELDY_USE_HOVER)?'':' checked="checked"').'"><br>';
	}
}


dol_fiche_end();


print '<div align="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"></div>';
print '</form>';


llxFooter();

if (is_object($db)) $db->close();
