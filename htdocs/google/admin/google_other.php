<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google_an.php
 *      \ingroup    google
 *      \brief      Setup page for google module (Analytics)
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

if (!$user->admin) accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest = GETPOST("test");
$actionsave = GETPOST("save");



/*
 * Actions
 */

if ($actionsave) {
	$db->begin();

	$res=dolibarr_set_const($db, 'GOOGLE_DEBUG', trim(GETPOST("GOOGLE_DEBUG")), 'chaine', 0, '', $conf->entity);

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

dol_fiche_head($head, 'tabother', '', -1);

print '<div class="fichecenter">';

print '<span class="opacitymedium">'.$langs->trans("GoogleAddAnalyticsOnLogonPage").'</span><br>';
print '<br>';

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Example")."</td>";
print "</tr>";

// Google debug
print '<tr class="oddeven"><td>'.$langs->trans("GOOGLE_DEBUG").'</td>';
print '<td>';
print ajax_constantonoff('GOOGLE_DEBUG', array(), $conf->entity, 0, 0, 1);
print '</td>';
print '<td><span class="opacitymedium small">1</span></td>';
print '</tr>';

print "</table>";
print '</div>';

print '</div>';

dol_fiche_end();

print '<div align="center">';
//print '<input type="submit" name="save" class="button" value="'.$langs->trans("Save").'">';
print "</div>";

print "</form>\n";


dol_htmloutput_mesg($mesg);

llxFooter();

$db->close();
