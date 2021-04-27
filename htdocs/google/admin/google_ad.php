<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google_ad.php
 *      \ingroup    google
 *      \brief      Setup page for google module (AdSense)
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
dol_include_once("/google/lib/google.lib.php");

if (!$user->admin)
	accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];



/*
 * Actions
 */
if ($actionsave) {
	$db->begin();

	$res=dolibarr_set_const($db, 'MAIN_GOOGLE_AD_CLIENT', trim($_POST["MAIN_GOOGLE_AD_CLIENT"]), 'chaine', 0, '', 0);	// Param for all env
	$res=dolibarr_set_const($db, 'MAIN_GOOGLE_AD_SLOT', trim($_POST["MAIN_GOOGLE_AD_SLOT"]), 'chaine', 0, '', 0);	// Param for all env
	$res=dolibarr_set_const($db, 'MAIN_GOOGLE_AD_WIDTH', trim($_POST["MAIN_GOOGLE_AD_WIDTH"]), 'chaine', 0, '', 0);	// Param for all env
	$res=dolibarr_set_const($db, 'MAIN_GOOGLE_AD_HEIGHT', trim($_POST["MAIN_GOOGLE_AD_HEIGHT"]), 'chaine', 0, '', 0);	// Param for all env

	if (! $error) {
		$db->commit();
		$mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
	} else {
		$db->rollback();
		$mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
	}
}




/*
 * View
 */


$form=new Form($db);
$formadmin=new FormAdmin($db);
$formother=new FormOther($db);

$help_url='EN:Module_Google_EN|FR:Module_Google|ES:Modulo_Google';
llxHeader('', $langs->trans("GoogleSetup"), $help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"), $linkback, 'setup');
print '<br>';


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';

$head=googleadmin_prepare_head();

dol_fiche_head($head, 'tabadsense', $langs->trans("GoogleTools"), -1);

print '<div class="fichecenter">';

print '<span class="opacitymedium">'.$langs->trans("GoogleAddPubOnLogonPage").'</span><br>';
print '<br>';


$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="140">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Example")."</td>";
print "</tr>";
// Client id
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("MAIN_GOOGLE_AD_CLIENT")."</td>";
print "<td>";
print '<input class="flat" type="text" size="20" name="MAIN_GOOGLE_AD_CLIENT" value="'.$conf->global->MAIN_GOOGLE_AD_CLIENT.'">';
print "</td>";
print '<td>ca-pub-1071905880519467</td>';
print "</tr>";
// Slot id
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("MAIN_GOOGLE_AD_SLOT")."</td>";
print "<td>";
print '<input class="flat" type="text" size="20" name="MAIN_GOOGLE_AD_SLOT" value="'.$conf->global->MAIN_GOOGLE_AD_SLOT.'">';
print "</td>";
print '<td>1421205532</td>';
print "</tr>";
// Slot id
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("MAIN_GOOGLE_AD_WIDTH")."</td>";
print "<td>";
print '<input class="flat" type="text" size="20" name="MAIN_GOOGLE_AD_WIDTH" value="'.$conf->global->MAIN_GOOGLE_AD_WIDTH.'">';
print "</td>";
print '<td>468</td>';
print "</tr>";
// Slot id
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("MAIN_GOOGLE_AD_HEIGHT")."</td>";
print "<td>";
print '<input class="flat" type="text" size="20" name="MAIN_GOOGLE_AD_HEIGHT" value="'.$conf->global->MAIN_GOOGLE_AD_HEIGHT.'">';
print "</td>";
print '<td>60</td>';
print "</tr>";

print "</table>";

print '</div>';

dol_fiche_end();

print '<div align="center">';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</div>";

print "</form>\n";

dol_htmloutput_mesg($mesg);

llxFooter();

$db->close();
