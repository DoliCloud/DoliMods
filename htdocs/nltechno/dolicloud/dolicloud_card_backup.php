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
 *       \file       htdocs/nltechno/dolicloud/dolicloud_card.php
 *       \ingroup    societe
 *       \brief      Card of a contact
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
dol_include_once("/nltechno/core/lib/dolicloud.lib.php");
dol_include_once('/nltechno/class/dolicloudcustomer.class.php');
dol_include_once('/nltechno/class/cdolicloudplans.class.php');

$langs->load("admin");
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");
$langs->load("nltechno@nltechno");

$mesg='';

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$instance   = GETPOST('instance');

$error=0; $errors=array();

$object = new DoliCloudCustomer($db);

// Security check
$result = restrictedArea($user, 'nltechno', 0, '','dolicloud');

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
$hookmanager=new HookManager($db);


if ($id > 0 || $instance)
{
	$result=$object->fetch($id,$instance);
	if ($result < 0) dol_print_error($db,$object->error);
}

$backupstring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/nltechno/backup_instance.php '.$object->instance.' '.$conf->global->DOLICLOUD_INSTANCES_PATH;



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

	include 'refresh_action.inc.php';

	$action = 'view';
}


/*
 *	View
 */

$help_url='';
llxHeader('',$langs->trans("DoliCloudCustomers"),$help_url);

$form = new Form($db);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';

if ($id > 0 || $instance)
{
	// Show tabs
	$head = dolicloud_prepare_head($object);

	$title = $langs->trans("DoliCloudCustomers");
	dol_fiche_head($head, 'backup', $title, 0, 'contact');
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


	print '<table class="border" width="100%">';

	// Instance / Organization
	print '<tr><td width="20%">'.$langs->trans("Instance").'</td><td colspan="3">';
	print $form->showrefnav($object,'instance','',1,'instance','instance','');
	print '</td></tr>';
	print '<tr><td>'.$langs->trans("Organization").'</td><td colspan="3">';
	print $object->organization;
	print '</td></tr>';

	// Email
	print '<tr><td>'.$langs->trans("EMail").'</td><td colspan="3">'.dol_print_email($object->email,$object->id,0,'AC_EMAIL').'</td>';
	print '</tr>';

	// Plan
	print '<tr><td width="20%">'.$langs->trans("Plan").'</td><td colspan="3">'.$object->plan.' - ';
	$plan=new Cdolicloudplans($db);
	$result=$plan->fetch('',$object->plan);
	if ($plan->price_instance) print ' '.$plan->price_instance.' '.currency_name('EUR').'/instance';
	if ($plan->price_user) print ' '.$plan->price_user.' '.currency_name('EUR').'/user';
	if ($plan->price_gb) print ' '.$plan->price_gb.' '.currency_name('EUR').'/GB';
	print ' <a href="http://www.dolicloud.com/fr/component/content/article/134-pricing" target="_blank">('.$langs->trans("Prices").')';
	print '</td>';
	print '</tr>';

	// Partner
	print '<tr><td width="20%">'.$langs->trans("Partner").'</td><td width="30%">'.$object->partner.'</td><td width="20%">'.$langs->trans("Source").'</td><td>'.($object->source?$object->source:$langs->trans("Unknown")).'</td></tr>';

	print "</table>";

	print '<br>';

	print '<table class="border" width="100%">';

	// SFTP
	print '<tr><td width="20%">'.$langs->trans("SFTP Server").'</td><td colspan="3">'.$object->hostname_web.'</td>';
	print '</tr>';
	// Login/Pass
	print '<tr>';
	print '<td width="20%">'.$langs->trans("SFTPLogin").'</td><td width="30%">'.$object->username_web.'</td>';
	print '<td width="20%">'.$langs->trans("Password").'</td><td width="30%">'.$object->password_web.'</td>';
	print '</tr>';

	// Database
	print '<tr><td>'.$langs->trans("DatabaseServer").'</td><td>'.$object->hostname_db.'</td>';
	print '<td>'.$langs->trans("DatabaseName").'</td><td>'.$object->database_db.'</td>';
	print '</tr>';
	// Login/Pass
	print '<tr>';
	print '<td>'.$langs->trans("DatabaseLogin").'</td><td>'.$object->username_db.'</td>';
	print '<td>'.$langs->trans("Password").'</td><td>'.$object->password_db.'</td>';
	print '</tr>';

	// Status
	print '<tr><td>'.$langs->trans("Status").'</td><td colspan="3">';
	print $object->getLibStatut(2);
	print '</td>';
	print '</tr>';

	print "</table>";
	print '<br>';


	// ----- DoliCloud instance -----
	print '<strong>INSTANCE SERVEUR STRATUS5</strong>';
	// Last refresh
	print ' - '.$langs->trans("DateLastCheck").': '.($object->lastcheck?dol_print_date($object->lastcheck,'dayhour','tzuser'):$langs->trans("Never"));

	if (! $object->user_id && $user->rights->nltechno->dolicloud->write)
	{
		print ' <a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
	}
	print '<br>';

	print '<table class="border" width="100%">';

	// Nb of users
	print '<tr><td width="20%">'.$langs->trans("NbOfUsers").'</td><td colspan="3"><font size="+2">'.$object->nbofusers.'</font></td>';
	print '</tr>';

	// Dates
	print '<tr><td width="20%">'.$langs->trans("DateRegistration").'</td><td width="30%">'.dol_print_date($object->date_registration,'dayhour');
	//print ' (<a href="'.dol_buildpath('/nltechno/dolicloud/dolicloud_card.php',1).'?id='.$object->id.'&amp;action=setdate&amp;date=">'.$langs->trans("SetDate").'</a>)';
	print '</td>';
	print '<td width="20%">'.$langs->trans("DateEndFreePeriod").'</td><td width="30%">'.dol_print_date($object->date_endfreeperiod,'dayhour').'</td>';
	print '</tr>';

	/*
	// Lastlogin
	print '<tr>';
	print '<td>'.$langs->trans("LastLogin").' / '.$langs->trans("Password").'</td><td>'.$object->lastlogin.' / '.$object->lastpass.'</td>';
	print '<td>'.$langs->trans("DateLastLogin").'</td><td>'.($object->date_lastlogin?dol_print_date($object->date_lastlogin,'dayhour','tzuser'):'').'</td>';
	print '</tr>';
	*/
	// Version
	print '<tr>';
	print '<td>'.$langs->trans("Version").'</td><td colspan="3">'.$object->version.'</td>';
	print '</tr>';

	// Modules
	print '<tr>';
	print '<td>'.$langs->trans("Modules").'</td><td colspan="3">'.join(', ',explode(',',$object->modulesenabled)).'</td>';
	print '</tr>';

	// Authorized key file
	print '<tr>';
	print '<td>'.$langs->trans("Authorized_keyInstalled").'</td><td colspan="3">'.($object->fileauthorizedkey?$langs->trans("Yes").' - '.dol_print_date($object->fileauthorizedkey,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
	print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addauthorizedkey">'.$langs->trans("Create").'</a>)';
	print '</td>';
	print '</tr>';

	// Install.lock file
	print '<tr>';
	print '<td>'.$langs->trans("LockfileInstalled").'</td><td colspan="3">'.($object->filelock?$langs->trans("Yes").' - '.dol_print_date($object->filelock,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
	print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addinstalllock">'.$langs->trans("Create").'</a>)';
	print ($object->filelock?' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=delinstalllock">'.$langs->trans("Delete").'</a>)':'');
	print '</td>';
	print '</tr>';

	print "</table>";
	print '<br>';


	$backupdir=$conf->global->DOLICLOUD_BACKUP_PATH;

	$dirdb=preg_replace('/_dolibarr/','',$object->database_db);
	$login=$object->username_web;
	$password=$object->password_web;
	$server=$object->instance.'.on.dolicloud.com';

	// ----- Backup instance -----
	print '<strong>INSTANCE BACKUP</strong><br>';
	print '<table class="border" width="100%">';

	// Last backup date
	print '<tr>';
	print '<td width="20%">'.$langs->trans("DateLastBackup").'</td>';
	print '<td width="30%">'.($object->date_lastrsync?dol_print_date($object->date_lastrsync,'dayhour','tzuser'):'').'</td>';
	print '<td>'.$langs->trans("BackupDir").'</td>';
	print '<td>'.$backupdir.'/'.$login.'/'.$dirdb.'</td>';
	print '</tr>';

	print "</table><br>";


	print "</div>";

	// Barre d'actions
/*	if (! $user->societe_id)
	{
		print '<div class="tabsAction">';

		if ($user->rights->nltechno->dolicloud->write)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=upgrade">'.$langs->trans('Upgrade').'</a>';
		}

		print "</div><br>";
	}
*/

	// Upgrade link
	$backupstringtoshow=$backupstring.' test';
	print 'Backup command line string<br>';
	print '<input type="text" name="backupstring" value="'.$backupstringtoshow.'" size="120"><br>';

}


llxFooter();

$db->close();
?>
