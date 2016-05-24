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
 *	    \file       htdocs/externalbackup/admin/externalbackup.php
 *      \ingroup    externalbackup
 *      \brief      Page to setup module externalbackup
 */

define('NOCSRFCHECK',1);

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
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');

$error = 0;

if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("errors");
$langs->load("externalbackup@externalbackup");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");



/*
 * Actions
 */

if (preg_match('/^set/',$action))
{
    // This is to force to add a new param after css urls to force new file loading
    // This set must be done before calling llxHeader().
    $_SESSION['dol_resetcache']=dol_print_date(dol_now(),'dayhourlog');
}

if ($action == 'set')
{
    $name = "EXTERNAL_BACKUP_RCLONE_PATH";
    $value = GETPOST("EXTERNAL_BACKUP_RCLONE_PATH");
    $res1 = dolibarr_set_const($db, $name, $value,'chaine',0,'',$conf->entity);

    $name = "EXTERNAL_BACKUP_RCLONE_CONF_PATH";
    $value = GETPOST("EXTERNAL_BACKUP_RCLONE_CONF_PATH");
    $res2 = dolibarr_set_const($db, $name, $value,'chaine',0,'',$conf->entity);

    $name = "EXTERNAL_BACKUP_RCLONE_TARGET";
    $value = GETPOST("EXTERNAL_BACKUP_RCLONE_TARGET");
    $res3 = dolibarr_set_const($db, $name, $value,'chaine',0,'',$conf->entity);
    
	if (! $res1 > 0 || ! $res2 > 0 || ! $res3 > 0) $error++;
 	if (! $error)
    {
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}




/**
 * View
 */

$formother=new FormOther($db);

llxHeader('','externalbackup');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("externalbackupSetup"),$linkback,'setup');
print '<br>';

print $langs->trans("externalbackupDesc").'<br>';
print '<br>';


$head=array();
$h=0;

$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("Setup");
$head[$h][2] = 'tabsetup';
$h++;

$head[$h][0] = 'about.php';
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;

print '<form name="externalbackupform" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="set">';

dol_fiche_head($head,'tabsetup');


print '<br>';

print '<table class="noborder">';

$var = true;

$var = ! $var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("EXTERNAL_BACKUP_RCLONE_PATH").': <input type="text" size="60" name="EXTERNAL_BACKUP_RCLONE_PATH" value="'.$conf->global->EXTERNAL_BACKUP_RCLONE_PATH.'">';
if (! dol_is_file($conf->global->EXTERNAL_BACKUP_RCLONE_PATH)) print ' '.img_warning("ErrorFileNotFound");
print '</td><td>';
print $langs->trans("Example").': /usr/sbin/rclone';
print '</td></tr>';

$var = ! $var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("EXTERNAL_BACKUP_RCLONE_CONF_PATH").': <input type="text" size="60" name="EXTERNAL_BACKUP_RCLONE_CONF_PATH" value="'.$conf->global->EXTERNAL_BACKUP_RCLONE_CONF_PATH.'">';
if (! dol_is_file($conf->global->EXTERNAL_BACKUP_RCLONE_CONF_PATH)) print ' '.img_warning($langs->trans("ErrorFileNotFound", $conf->global->EXTERNAL_BACKUP_RCLONE_CONF_PATH));
print '</td><td>';
print $langs->trans("Example").': /home/backupuser/.rclone.conf';
print '</td></tr>';

$var = ! $var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("EXTERNAL_BACKUP_RCLONE_TARGET").': <input type="text" size="20" name="EXTERNAL_BACKUP_RCLONE_TARGET" value="'.$conf->global->EXTERNAL_BACKUP_RCLONE_TARGET.'">';
print '</td><td>';
print $langs->trans("EXTERNAL_BACKUP_RCLONE_TARGETDesc").'<br>';
print $langs->trans("Example").': hubic, googledrive, ...';
print '</td></tr>';

print '</table>';

print '<br>';

dol_fiche_end();

print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"></div>';

print '</form>';


llxFooter();

if (is_object($db)) $db->close();

