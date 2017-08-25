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
 * or see http://www.gnu.org/
 */

/**
 *     \file       htdocs/sellyoursaas/admin/sellyoursaas.php
 *     \brief      Page administration module SellYourSaas
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");

// Security check
if (!$user->admin)
accessforbidden();

$langs->load("admin");
$langs->load("errors");
$langs->load("install");
$langs->load("nltechno@sellyoursaas");

$action=GETPOST('action');

//exit;


/*
 * Actions
 */

if ($action == 'setstratus5')
{
	$error=0;

	if (! $error)
	{
		$dir=GETPOST("DOLICLOUD_EXT_HOME");
		dolibarr_set_const($db,"DOLICLOUD_EXT_HOME",GETPOST("DOLICLOUD_EXT_HOME"),'chaine',0,'',$conf->entity);
		setEventMessage($langs->trans("Saved"),'mesgs');
	}
}

if ($action == 'set')
{
	$error=0;

	if (! $error)
	{
		dolibarr_set_const($db,"SELLYOURSAAS_NAME",GETPOST("SELLYOURSAAS_NAME"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_SCRIPTS_PATH");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_SCRIPTS_PATH",GETPOST("DOLICLOUD_SCRIPTS_PATH"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_LASTSTABLEVERSION_DIR");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_LASTSTABLEVERSION_DIR",GETPOST("DOLICLOUD_LASTSTABLEVERSION_DIR"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_INSTANCES_PATH");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_INSTANCES_PATH",GETPOST("DOLICLOUD_INSTANCES_PATH"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_BACKUP_PATH");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_BACKUP_PATH",GETPOST("DOLICLOUD_BACKUP_PATH"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"DOLICLOUD_DATABASE_HOST",GETPOST("DOLICLOUD_DATABASE_HOST"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_PORT",GETPOST("DOLICLOUD_DATABASE_PORT"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_NAME",GETPOST("DOLICLOUD_DATABASE_NAME"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_USER",GETPOST("DOLICLOUD_DATABASE_USER"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_PASS",GETPOST("DOLICLOUD_DATABASE_PASS"),'chaine',0,'',$conf->entity);
	}
}



/*
 * View
 */

$html=new Form($db);

$help_url="";
llxHeader("",$langs->trans("SellYouSaasSetup"),$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans('SellYouSaasSetup'),$linkback,'setup');

$head=array();
dol_fiche_head($head, 'serversetup', $langs->trans("SellYourSaas"), -1);

print $langs->trans("SellYouSaasDesc")."<br>\n";
print "<br>\n";

$error=0;


print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td>'.$langs->trans("Examples").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
print "</tr>\n";

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("SellYourSaasName").'</td>';
print '<td>';
print '<input size="40" type="text" name="SELLYOURSAAS_NAME" value="'.$conf->global->SELLYOURSAAS_NAME.'">';
print '</td>';
print '<td>My SaaS service</td>';
print '<td>&nbsp;</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DirForScriptPath").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_SCRIPTS_PATH" value="'.$conf->global->DOLICLOUD_SCRIPTS_PATH.'">';
print '</td>';
print '<td>/home/admin/wwwroot/dolibarr_nltechno/scripts</td>';
print '<td>&nbsp;</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DirForLastStableVersionOfDolibarr").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_LASTSTABLEVERSION_DIR" value="'.$conf->global->DOLICLOUD_LASTSTABLEVERSION_DIR.'">';
print '</td>';
print '<td>/home/admin/wwwroot/dolibarr_old</td>';
print '<td>&nbsp;</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DirForDoliCloudInstances").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_INSTANCES_PATH" value="'.$conf->global->DOLICLOUD_INSTANCES_PATH.'">';
print '</td>';
print '<td>/home/dolicloud/home</td>';
print '<td>&nbsp;</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DirForDoliCloudBackupInstances").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_BACKUP_PATH" value="'.$conf->global->DOLICLOUD_BACKUP_PATH.'">';
print '</td>';
print '<td>/home/dolicloud/backup</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '</table>';

print "</form>\n";


print "<br>";


// Param
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setstratus5">';

print '<strong>DoliCloud V1</strong>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td>'.$langs->trans("Examples").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
print "</tr>\n";

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DirForDoliCloudInstances").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_EXT_HOME" value="'.$conf->global->DOLICLOUD_EXT_HOME.'">';
print '</td>';
print '<td>/home/jail/home</td>';
print '<td>&nbsp;</td>';
print '</tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DatabaseServer").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_HOST" value="'.$conf->global->DOLICLOUD_DATABASE_HOST.'">';
print '</td>';
print '<td>www.dolicloud.com</td>';
print '<td>&nbsp;</td>';
print '</tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DatabasePort").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_PORT" value="'.$conf->global->DOLICLOUD_DATABASE_PORT.'">';
print '</td>';
print '<td>3306</td>';
print '<td>&nbsp;</td>';
print '</tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DatabaseName").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_NAME" value="'.$conf->global->DOLICLOUD_DATABASE_NAME.'">';
print '</td>';
print '<td>dolicloud_saasplex</td>';
print '<td>&nbsp;</td>';
print '</tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DatabaseUser").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_USER" value="'.$conf->global->DOLICLOUD_DATABASE_USER.'">';
print '</td>';
print '<td>dolicloud</td>';
print '<td>&nbsp;</td>';
print '</tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>'.$langs->trans("DatabasePassword").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_PASS" value="'.$conf->global->DOLICLOUD_DATABASE_PASS.'">';
print '</td>';
print '<td></td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '</table>';

print '</form>';

print '<br>';


dol_fiche_end();


llxfooter();

$db->close();
