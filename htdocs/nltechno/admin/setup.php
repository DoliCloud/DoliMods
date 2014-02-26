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
 *     \file       htdocs/memcached/admin/memcached.php
 *     \brief      Page administration de memcached
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");

// Security check
if (!$user->admin)
accessforbidden();

$langs->load("admin");
$langs->load("errors");
$langs->load("install");
$langs->load("nltechno@nltechno");

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
		$dir=GETPOST("DOLICLOUD_INSTANCES_PATH");
		if (dol_is_dir($dir)) dolibarr_set_const($db,"DOLICLOUD_INSTANCES_PATH",GETPOST("DOLICLOUD_INSTANCES_PATH"),'chaine',0,'',$conf->entity);
		else setEventMessage($langs->trans("ErrorDirNotFound",$dir),'errors');

		$dir=GETPOST("DOLICLOUD_BACKUP_PATH");
		if (dol_is_dir($dir)) dolibarr_set_const($db,"DOLICLOUD_BACKUP_PATH",GETPOST("DOLICLOUD_BACKUP_PATH"),'chaine',0,'',$conf->entity);
		else setEventMessage($langs->trans("ErrorDirNotFound",$dir),'errors');

		$dir=GETPOST("DOLICLOUD_SCRIPTS_PATH");
		if (dol_is_dir($dir)) dolibarr_set_const($db,"DOLICLOUD_SCRIPTS_PATH",GETPOST("DOLICLOUD_SCRIPTS_PATH"),'chaine',0,'',$conf->entity);
		else setEventMessage($langs->trans("ErrorDirNotFound",$dir),'errors');

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
llxHeader("",$langs->trans("NLTechnoSetup"),$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans('NLTechnoSetup'),$linkback,'setup');

$head=array();
dol_fiche_head($head, 'serversetup', $langs->trans("NLTechno"));

print $langs->trans("NLTechnoDesc")."<br>\n";
print "<br>\n";

$error=0;


// Param
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setstratus5">';

print '<strong>STRATUS5</strong>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
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

print '</table>';

print '</form>';

print '<br>';

print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<strong>NLTECHNO</strong>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td>'.$langs->trans("Examples").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
print "</tr>\n";

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

print "</form>\n";

dol_fiche_end();


llxfooter();

$db->close();
?>