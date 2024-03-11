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
 *	    \file       htdocs/skincoloreditor/usercolors.php
 *      \ingroup    skincoloreditor
 *      \brief      Page to setup SkincolorEditor for user
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
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/usergroups.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';


$langs->load("users");
$langs->load("admin");
$langs->load("other");
$langs->load("skincoloreditor@skincoloreditor");

$id = GETPOST('id', 'int');

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
$feature2 = (($socid && $user->rights->user->self->creer)?'':'user');
if ($user->id == $id) $feature2=''; // A user can always read its own card
$result = restrictedArea($user, 'user', $id, 'user&user', $feature2);

$object = new User($db);
$object->fetch($id, '', '', 1);
$object->getrights();

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
	$tab[$name]=$value;
	$res = dol_set_user_param($db, $conf, $object, $tab);

	if (! $res > 0) $error++;
	if (! $error) {
		$mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
	} else {
		$mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
	}
}

if ($action == 'setcolor') {
	/*$tab['THEME_ELDY_FONT_SIZE1']=GETPOST('THEME_ELDY_FONT_SIZE1','alpha');
	$tab['THEME_ELDY_USE_HOVER']=GETPOST('THEME_ELDY_USE_HOVER','alpha');
	$tab['THEME_ELDY_BACKBODY']=GETPOST('THEME_ELDY_BACKBODY','alpha');
	$tab['THEME_ELDY_BACKTABCARD1']=GETPOST('THEME_ELDY_BACKTABCARD1','alpha');
	$tab['THEME_ELDY_BACKTABCARD2']=GETPOST('THEME_ELDY_BACKTABCARD2','alpha');
	$tab['THEME_ELDY_TOPMENU_BACK1']=GETPOST('THEME_ELDY_USE_TOPMENU_BACK1','alpha');
	$tab['THEME_ELDY_VERMENU_BACK1']=GETPOST('THEME_ELDY_USE_VERMENU_BACK1','alpha');
	$tab['THEME_ELDY_BACKTITLE1']=GETPOST('THEME_ELDY_BACKTITLE1','alpha');
	$tab['THEME_ELDY_LINEIMPAIR1']=GETPOST('THEME_ELDY_LINEIMPAIR1','alpha');
	$tab['THEME_ELDY_LINEPAIR1']=GETPOST('THEME_ELDY_LINEPAIR1','alpha');*/

	$listofkey=array('THEME_ELDY_FONT_SIZE1','THEME_ELDY_USE_HOVER','THEME_ELDY_BACKBODY','THEME_ELDY_BACKTABCARD1','THEME_ELDY_BACKTABCARD2','THEME_ELDY_TOPMENU_BACK1','THEME_ELDY_VERMENU_BACK1','THEME_ELDY_BACKTITLE1','THEME_ELDY_LINEIMPAIR1','THEME_ELDY_LINEPAIR1');
	foreach ($listofkey as $key) {
		if (isset($_POST[$key])) $tab[$key]=GETPOST($key, 'alpha');
	}

	$res = dol_set_user_param($db, $conf, $object, $tab);

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

$form=new Form($db);
$formother=new FormOther($db);

llxHeader('', 'SkinColorEditor', $linktohelp);

$head = user_prepare_head($object);

$title = $langs->trans("User");
dol_fiche_head($head, 'tabskincoloreditors', $title, ((float) DOL_VERSION <= 6) ? 0 : -1, 'user');

dol_banner_tab($object, 'id', '', $user->hasRight('user', 'user', 'lire') || $user->admin);

print '<div class="underbanner clearboth"></div>';

dol_fiche_end();

print '<br>';

print $langs->trans("SkinColorEditorDescUser").'<br>';
print '<br>';

print $langs->trans("ActivateColorPersonalizingUser").': &nbsp; ';
$name='THEME_ELDY_ENABLE_PERSONALIZED';
if (empty($object->conf->$name)) {
	if (empty($dolibarr_main_demo)) {
		print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=1&amp;id='.$object->id.'">';
		print img_picto($langs->trans("Disabled"), 'switch_off');
		print '</a>';
	} else {
		print '<a href="#">';
		print img_picto($langs->trans("DisabledInDemoMode"), 'switch_off');
		print '</a>';
	}
} else {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=0&amp;id='.$object->id.'">';
	print img_picto($langs->trans("Enabled"), 'switch_on');
	print '</a>';
}
print '<br><br><br>';

if ($conf->theme != 'eldy') {
	print '<div class="warning">'.img_warning().' '.$langs->trans("WarningSkinMustBeEldy", $conf->theme).'</div>';
}

dol_htmloutput_mesg($mesg);


if (! empty($object->conf->THEME_ELDY_ENABLE_PERSONALIZED)) {
	print '<form name="formcolor" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="setcolor">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	dol_fiche_head();

	print $langs->trans("ForceSpecificValue").':<br><br>';


	// Font size
	//$defvalue=$conf->global->THEME_ELDY_FONT_SIZE1;
	//if (isset($object->conf->THEME_ELDY_FONT_SIZE1))
	$defvalue=$object->conf->THEME_ELDY_FONT_SIZE1;
	print $langs->trans("FontSize").': <input type="text" class="flat" name="THEME_ELDY_FONT_SIZE1" size="4" value="'.$defvalue.'"><br>';
	print '<br>';

	if (versioncompare(versiondolibarrarray(), array(3,8,-3)) >= 0) {
		print $langs->trans("SelectTabColor").' ';
		$defcolor=$conf->global->THEME_ELDY_BACKTABCARD1;
		if (isset($object->conf->THEME_ELDY_BACKTABCARD1)) $defcolor=$object->conf->THEME_ELDY_BACKTABCARD1;
		print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_BACKTABCARD1').'<br><br>';
	} else {
		// Force specific value
		print $langs->trans("SelectTabColor2").' ';
		$defcolor=$conf->global->THEME_ELDY_BACKTABCARD2;
		if (isset($object->conf->THEME_ELDY_BACKTABCARD2)) $defcolor=$object->conf->THEME_ELDY_BACKTABCARD2;
		print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_BACKTABCARD2').'<br><br>';
		print $langs->trans("SelectTabColor1").' ';
		$defcolor=$conf->global->THEME_ELDY_BACKTABCARD1;
		if (isset($object->conf->THEME_ELDY_BACKTABCARD1)) $defcolor=$object->conf->THEME_ELDY_BACKTABCARD1;
		print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_BACKTABCARD1').'<br><br>';
	}

	$defcolor=$conf->global->THEME_ELDY_BACKBODY;
	if (isset($object->conf->THEME_ELDY_BACKBODY)) $defcolor=$object->conf->THEME_ELDY_BACKBODY;
	print $langs->trans("BackgroundColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_BACKBODY', 'formcolor', 1).'<br><br>';

	$defcolor=$conf->global->THEME_ELDY_TOPMENU_BACK1;
	if (isset($object->conf->THEME_ELDY_TOPMENU_BACK1)) $defcolor=$object->conf->THEME_ELDY_TOPMENU_BACK1;
	print $langs->trans("TopMenuBackgroundColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_TOPMENU_BACK1', 'formcolor', 1).'<br><br>';
	//print $langs->trans("TopMenuFontColor").' ';
	//print $formother->selectColor($conf->global->THEME_ELDY_TOPMENU_BACK1,'THEME_ELDY_TOPMENU_BACK1','formcolor',1).'<br><br>';

	$defcolor=$conf->global->THEME_ELDY_VERMENU_BACK1;
	if (isset($object->conf->THEME_ELDY_VERMENU_BACK1)) $defcolor=$object->conf->THEME_ELDY_VERMENU_BACK1;
	print $langs->trans("LeftMenuBackgroundColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_VERMENU_BACK1', 'formcolor', 1).'<br><br>';
	//print $langs->trans("LeftMenuFontColor").' ';
	//print $formother->selectColor($conf->global->THEME_ELDY_TOPMENU_BACK1,'THEME_ELDY_TOPMENU_BACK1','formcolor',1).'<br><br>';

	$defcolor=$conf->global->THEME_ELDY_BACKTITLE1;
	if (isset($object->conf->THEME_ELDY_BACKTITLE1)) $defcolor=$object->conf->THEME_ELDY_BACKTITLE1;
	print $langs->trans("BackgroundTableTitleColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_BACKTITLE1', 'formcolor', 1).'<br><br>';

	$defcolor=$conf->global->THEME_ELDY_LINEIMPAIR1;
	if (isset($object->conf->THEME_ELDY_LINEIMPAIR1)) $defcolor=$object->conf->THEME_ELDY_LINEIMPAIR1;
	print $langs->trans("BackgroundTableLineOddColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_LINEIMPAIR1', 'formcolor', 1).'<br><br>';

	$defcolor=$conf->global->THEME_ELDY_LINEPAIR1;
	if (isset($object->conf->THEME_ELDY_LINEPAIR1)) $defcolor=$object->conf->THEME_ELDY_LINEPAIR1;
	print $langs->trans("BackgroundTableLineEvenColor").' ';
	print $formother->selectColor(colorArrayToHex(colorStringToArray($defcolor, array()), ''), 'THEME_ELDY_LINEPAIR1', 'formcolor', 1).'<br><br>';

	if (versioncompare(versiondolibarrarray(), array(3,9,-3)) >= 0) {
		// Use hover
		print $langs->trans("UseHoverOnLists").' ';
		print $formother->selectColor(colorArrayToHex(colorStringToArray($object->conf->THEME_ELDY_USE_HOVER, array()), ''), 'THEME_ELDY_USE_HOVER', 'formcolor', 1).'<br><br>';
	} else {
		// Use hover
		print $langs->trans("UseHoverOnLists").': <input type="checkbox" class="flat" name="THEME_ELDY_USE_HOVER" '.(empty($object->conf->THEME_ELDY_USE_HOVER)?'':' checked="checked"').'"><br>';
	}

	dol_fiche_end();


	print '<div align="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"></div>';

	print '</form>';
}


llxFooter();

if (is_object($db)) $db->close();
