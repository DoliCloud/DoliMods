<?php
/* Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/sellyoursaas/backoffice/dolicloud_card.php
 *       \ingroup    societe
 *       \brief      Card of a contact
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

require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
dol_include_once("/sellyoursaas/core/lib/dolicloud.lib.php");
dol_include_once('/sellyoursaas/class/dolicloudcustomernew.class.php');
dol_include_once('/sellyoursaas/class/cdolicloudplans.class.php');

$langs->load("admin");
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");
$langs->load("sellyoursaas@sellyoursaas");

$mesg='';

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$instance   = GETPOST('instance');

$error=0; $errors=array();



$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->db->host.", port=".$conf->db->port.", user=".$conf->db->user.", databasename=".$conf->db->name.", ".$db2->error);
	exit;
}



$object = new DoliCloudCustomerNew($db,$db2);

// Security check
$result = restrictedArea($user, 'sellyoursaas', 0, '','sellyoursaas');

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array array
include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
$hookmanager=new HookManager($db);


if ($id > 0 || $instance)
{
	$result=$object->fetch($id,$instance);
	if ($result < 0) dol_print_error($db,$object->error);
}

$upgradestring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/rsync_instance.php '.$conf->global->DOLICLOUD_LASTSTABLEVERSION_DIR.' '.$object->instance;



/*
 *	Actions
*/

$parameters=array('id'=>$id, 'objcanvas'=>$objcanvas);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

if (empty($reshook))
{
	// Cancel
	if (GETPOST("cancel") && ! empty($backtopage))
	{
		header("Location: ".$backtopage);
		exit;
	}

	include 'refresh_action_new.inc.php';

	$action = 'view';
}


/*
 *	View
 */

$help_url='';
llxHeader('',$langs->trans("DoliCloudInstances"),$help_url);

$form = new Form($db);
$form2 = new Form($db2);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';
$arraystatus=Dolicloudcustomernew::$listOfStatus;

if ($id > 0 || $instance)
{
	// Show tabs
	$head = dolicloud_prepare_head($object,'_new');

	$title = $langs->trans("DoliCloudInstances");
	dol_fiche_head($head, 'upgrade', $title, 0, 'contact');
}

if (($id > 0 || $instance) && $action != 'edit' && $action != 'create')
{
	/*
	 * Fiche en mode visualisation
	*/
	$newdb=getDoliDBInstance($conf->db->type, $object->instance.'.on.dolicloud.com', $object->username_db, $object->password_db, $object->database_db, 3306);
	if (is_object($newdb))
	{
		// Get user/pass of last admin user
		$sql="SELECT login, pass FROM llx_user WHERE admin = 1 ORDER BY statut DESC, datelastlogin DESC LIMIT 1";
		$resql=$newdb->query($sql);
		$obj = $newdb->fetch_object($resql);
		$object->lastlogin_admin=$obj->login;
		$object->lastpass_admin=$obj->pass;
		$lastloginadmin=$object->lastlogin_admin;
		$lastpassadmin=$object->lastpass_admin;
	}

	dol_htmloutput_errors($error,$errors);


	$savdb=$object->db;
	$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function
	dol_banner_tab($object,'instance','',1,'name','instance','','',1);
	$object->db=$savdb;


	print '<div class="fichecenter">';

	$backupdir=$conf->global->DOLICLOUD_BACKUP_PATH;

	$dirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$object->database_db);
	$login=$object->username_web;
	$password=$object->password_web;
	$server=$object->instance.'.on.dolicloud.com';

	// Barre d'actions
/*	if (! $user->societe_id)
	{
		print '<div class="tabsAction">';

		if ($user->rights->sellyoursaas->sellyoursaas->write)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=upgrade">'.$langs->trans('Upgrade').'</a>';
		}

		print "</div><br>";
	}
*/

	print '</div>';
}


if ($id > 0 || $instance)
{
	dol_fiche_end();
}

print '<br>';


// Upgrade link
$upgradestringtoshow=$upgradestring.' test';
print 'Upgrade version line string (remplacer "test" par "confirmunlock" pour exécuter réellement)<br>';
print '<input type="text" id="upgradestring" name="upgradestring" value="'.$upgradestringtoshow.'" size="160"><br>';
print ajax_autoselect("upgradestring", 0);
print '<br>';

// Document restore
$sftprestorestring='rsync -n -v -a dolibarr_documents/* '.$object->username_web.'@'.$object->hostname_web.':'.$object->fs_path.'/documents';
print 'Rsync to copy/overwrite document dir (remove -n to execute really):<br>';
print '<input type="text" id="sftprestorestring" name="sftprestorestring" value="'.$sftprestorestring.'" size="160"><br>';
print ajax_autoselect("sftprestorestring", 0);
print '<br>';

// Deploy module
$sftpdeploystring='rsync -n -v -a pathtohtdocsmodule/* '.$object->username_web.'@'.$object->hostname_web.':'.$object->fs_path.'/htdocs/namemodule';
print 'Rsync to install or overwrite module (remove -n to execute really):<br>';
print '<input type="text" id="sftpdeploystring" name="sftpdeploystring" value="'.$sftpdeploystring.'" size="160"><br>';
print ajax_autoselect("sftpdeploystring", 0);
print '<br>';

// Mysql Restore
$mysqlresotrecommand='mysql -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db.' < filetorestore';
print 'Mysql overwrite database:<br>';
print '<input type="text" id="mysqlrestorecommand" name="mysqlrestorecommand" value="'.$mysqlresotrecommand.'" size="160"><br>';
print ajax_autoselect("mysqlrestorecommand", 0);


llxFooter();

$db->close();

