<?php
/* Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
dol_include_once("/nltechno/core/lib/dolicloud.lib.php");
dol_include_once('/nltechno/class/dolicloudcustomernew.class.php');
dol_include_once('/nltechno/class/cdolicloudplans.class.php');

$langs->load("admin");
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");
$langs->load("nltechno@nltechno");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$instance   = GETPOST('instance');

$error = 0; $errors = array();



$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->db->host.", port=".$conf->db->port.", user=".$conf->db->user.", databasename=".$conf->db->name.", ".$db2->error);
	exit;
}



$object = new DoliCloudCustomerNew($db,$db2);

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
	dol_fiche_head($head, 'users', $title, 0, 'contact');
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
//	else print 'Error, failed to connect';

	dol_htmloutput_errors($error,$errors);


	print '<table class="border" width="100%">';

	// Instance / Organization
	print '<tr><td width="20%">'.$langs->trans("Instance").'</td><td colspan="3">';
	$savdb=$object->db;
	$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function
	print $form2->showrefnav($object,'instance','',1,'name','instance','','',1);
	$object->db=$savdb;
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
	print $object->getLibStatut(4,$form);
	print '</td>';
	print '</tr>';

	print "</table>";
	//print '<br>';

	/*
	// Last refresh
	print $langs->trans("DateLastCheck").': '.($object->date_lastcheck?dol_print_date($object->date_lastcheck,'dayhour','tzuser'):$langs->trans("Never"));

	if (! $object->user_id && $user->rights->nltechno->dolicloud->write)
	{
		print ' <a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
	}*/
	print '<br><br>';


	// ----- DoliCloud instance -----
	print '<strong>INSTANCE SERVEUR STRATUS5 ('.$newdb->database_host.')</strong><br>';

	print_user_table($newdb);

	print '<br>';

	// ----- Backup instance -----
	$backupdir=$conf->global->DOLICLOUD_BACKUP_PATH;

	$dirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$object->database_db);
	$login=$object->username_web;
	$password=$object->password_web;
	$server=$object->instance.'.on.dolicloud.com';

	$newdb2=getDoliDBInstance($db2->type, $conf->global->DOLICLOUD_DATABASE_HOST, $object->username_db, $object->password_db, $object->database_db, 3306);
	if (is_object($newdb2))
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

	print '<strong>INSTANCE SERVEUR NLTECHNO ('.$newdb2->database_host.')</strong><br>';
	print '<table class="border" width="100%">';

	print_user_table($newdb2);

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

	// Dolibarr instance login
	$url='https://'.$object->instance.'.on.dolicloud.com?username='.$lastloginadmin.'&amp;password='.$lastpassadmin;
	$link='<a href="'.$url.'" target="_blank">'.$url.'</a>';
	print 'Dolibarr link<br>';
	//print '<input type="text" name="dashboardconnectstring" value="'.dashboardconnectstring.'" size="100"><br>';
	print $link.'<br>';
	print '<br>';

}


llxFooter();

$db->close();


/**
 * Print list of users
 *
 * @param   string    $newdb        New db
 * @return  void
 */
function print_user_table($newdb)
{
	global $langs;

	print '<table class="border" width="100%">';

	// Nb of users
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Login").'</td>';
	print '<td>'.$langs->trans("Lastname").'</td>';
	print '<td>'.$langs->trans("Firstname").'</td>';
	print '<td>'.$langs->trans("Admin").'</td>';
	print '<td>'.$langs->trans("Email").'</td>';
	print '<td>'.$langs->trans("Pass").'</td>';
	print '<td>'.$langs->trans("DateCreation").'</td>';
	print '<td>'.$langs->trans("DateChange").'</td>';
	print '<td>'.$langs->trans("DateLastLogin").'</td>';
	print '<td>'.$langs->trans("Entity").'</td>';
	print '<td>'.$langs->trans("ParentsId").'</td>';
	print '<td>'.$langs->trans("Status").'</td>';
	print '</tr>';

	if (is_object($newdb))
	{
		// Get user/pass of last admin user
		$sql ="SELECT login, lastname, firstname, admin, email, pass, pass_crypted, datec, tms as datem, datelastlogin, fk_soc, fk_socpeople, fk_member, entity, statut";
		$sql.=" FROM llx_user ORDER BY statut DESC";
		$resql=$newdb->query($sql);
		if (empty($resql))	// Alternative for 3.7-
		{
			$sql ="SELECT login, lastname as lastname, firstname, admin, email, pass, pass_crypted, datec, tms as datem, datelastlogin, fk_societe, fk_socpeople, fk_member, entity, statut";
			$sql.=" FROM llx_user ORDER BY statut DESC";
			$resql=$newdb->query($sql);
			if (empty($resql))	// Alternative for 3.3-
    		{
    			$sql ="SELECT login, nom as lastname, prenom as firstname, admin, email, pass, pass_crypted, datec, tms as datem, datelastlogin, fk_societe, fk_socpeople, fk_member, entity, statut";
    			$sql.=" FROM llx_user ORDER BY statut DESC";
    			$resql=$newdb->query($sql);
    		}
		}
		if ($resql)
		{
			$var=false;
			$num=$newdb->num_rows($resql);
			$i=0;
			while ($i < $num)
			{
				$var=!$var;
				$obj = $newdb->fetch_object($resql);
				print '<tr '.$bc[$var].'>';
				print '<td>'.$obj->login.'</td>';
				print '<td>'.$obj->lastname.'</td>';
				print '<td>'.$obj->firstname.'</td>';
				print '<td>'.$obj->admin.'</td>';
				print '<td>'.$obj->email.'</td>';
				print '<td>'.$obj->pass.' ('.$obj->pass_crypted.')</td>';
				print '<td>'.dol_print_date($newdb->jdate($obj->datec),'dayhour').'</td>';
				print '<td>'.dol_print_date($newdb->jdate($obj->datem),'dayhour').'</td>';
				print '<td>'.dol_print_date($newdb->jdate($obj->datelastlogin),'dayhour').'</td>';
				print '<td>'.$obj->entity.'</td>';
				print '<td>'.$obj->fk_soc.'/'.$obj->fk_socpeople.'/'.$obj->fk_member.'</td>';
				print '<td align="right">'.$obj->statut.'</td>';
				print '</tr>';
				$i++;
			}
		}
		else
		{
			dol_print_error($newdb);
		}
	}

	print "</table>";
}
