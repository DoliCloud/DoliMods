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
 *       \file       htdocs/sellyoursaas/backoffice/instance_backup.php
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
require_once(DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/contract.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
dol_include_once("/sellyoursaas/core/lib/dolicloud.lib.php");
dol_include_once('/sellyoursaas/class/dolicloud_customers.class.php');
dol_include_once('/sellyoursaas/class/cdolicloudplans.class.php');

$langs->load("admin");
$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("contracts");
$langs->load("commercial");
$langs->load("sellyoursaas@sellyoursaas");

$mesg='';

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$instanceoldid = GETPOST('instanceoldid','int');
$ref        = GETPOST('ref','alpha');
$refold     = GETPOST('refold','alpha');

$error=0; $errors=array();


if (empty($instanceoldid) && empty($refold) && $action != 'create')
{
	$object = new Contrat($db);
}
else
{
	$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
	if ($db2->error)
	{
		dol_print_error($db2,"host=".$conf->db->host.", port=".$conf->db->port.", user=".$conf->db->user.", databasename=".$conf->db->name.", ".$db2->error);
		exit;
	}

	$object = new Dolicloud_customers($db,$db2);
}


// Security check
$result = restrictedArea($user, 'sellyoursaas', 0, '','');

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('contractcard','globalcard'));


if ($id > 0 || $instanceoldid > 0 || $ref || $refold)
{
	$result=$object->fetch($id?$id:$instanceoldid, $ref?$ref:$refold);
	if ($result < 0) dol_print_error($db,$object->error);
	if ($object->element != 'contrat') $instanceoldid=$object->id;
	else $id=$object->id;
}

if (! empty($instanceoldid))
{
	$backupstring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/backup_instance.php '.$object->instance.' '.$conf->global->DOLICLOUD_BACKUP_PATH;
}
else
{
	$backupstring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/backup_instance.php '.$object->ref_customer.' '.$conf->global->DOLICLOUD_BACKUP_PATH;
}


/*
 *	Actions
 */

$parameters=array('id'=>$id, 'objcanvas'=>$objcanvas);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

if (empty($reshook))
{
	// Cancel
	if (GETPOST('cancel','alpha') && ! empty($backtopage))
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
llxHeader('',$langs->trans("DoliCloudInstances"),$help_url);

$form = new Form($db);
$form2 = new Form($db2);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';
$arraystatus=Dolicloud_customers::$listOfStatus;

if (empty($instanceoldid) && $action != 'create')
{
	// Show tabs
	$head = contract_prepare_head($object);

	$title = $langs->trans("Contract");
	dol_fiche_head($head, 'backup', $title, 0, 'contract');
}
else
{
	// Show tabs
	$head = dolicloud_prepare_head($object);

	$title = $langs->trans("Contract");
	dol_fiche_head($head, 'backup', $title, 0, 'contract');
}


if (($id > 0 || $instanceoldid > 0) && $action != 'edit' && $action != 'create')
{
	/*
	 * Fiche en mode visualisation
	*/

	$instance = 'xxxx';
	$type_db = $conf->db->type;
	if ($instanceoldid)
	{
		$instance = $object->instance;
		$hostname_db = $object->hostname_db;
		$username_db = $object->username_db;
		$password_db = $object->password_db;
		$database_db = $object->database_db;
		$port_db = $object->port_db?$object->port_db:3306;
		$username_web = $object->username_web;
		$password_web = $object->password_web;
		$hostname_os = $object->instance.'.on.dolicloud.com';
	}
	else	// $object is a contract (on old or new instance)
	{
		$instance = $object->ref_customer;
		$hostname_db = $object->array_options['options_hostname_db'];
		$username_db = $object->array_options['options_username_db'];
		$password_db = $object->array_options['options_password_db'];
		$database_db = $object->array_options['options_database_db'];
		$port_db     = $object->array_options['options_port_db'];
		$username_web = $object->array_options['options_username_os'];
		$password_web = $object->array_options['options_password_os'];
		$hostname_os = $object->array_options['options_hostname_os'];
	}

	$newdb=getDoliDBInstance($type_db, $hostname_db, $username_db, $password_db, $database_db, 3306);

	if (is_object($object->db2))
	{
		$savdb=$object->db;
		$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function.  $db = stratus5 database
	}



	$object->fetch_thirdparty();

	//$object->email = $object->thirdparty->email;

	// Contract card

	if (empty($instanceoldid))
	{
		$linkback = '<a href="'.DOL_URL_ROOT.'/contrat/list.php?restore_lastsearch_values=1'.(! empty($socid)?'&socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';
	}
	else
	{
		$linkback = '<a href="'.dol_buildpath('/sellyoursaas/backoffice/dolicloud_list.php',1).'?instanceoldid='.$instanceoldid.'&restore_lastsearch_values=1'.(! empty($socid)?'&socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';
	}

	$morehtmlref='';

	if (empty($instanceoldid))
	{
		$morehtmlref.='<div class="refidno">';
		// Ref customer
		$morehtmlref.=$form->editfieldkey("RefCustomer", 'ref_customer', $object->ref_customer, $object, 0, 'string', '', 0, 1);
		$morehtmlref.=$form->editfieldval("RefCustomer", 'ref_customer', $object->ref_customer, $object, 0, 'string', '', null, null, '', 1);
		// Ref supplier
		$morehtmlref.='<br>';
		$morehtmlref.=$form->editfieldkey("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', 0, 1);
		$morehtmlref.=$form->editfieldval("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', null, null, '', 1);
		// Thirdparty
		$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . ($object->thirdparty ? $object->thirdparty->getNomUrl(1) : '');
		// Project
		if (! empty($conf->projet->enabled))
		{
			$langs->load("projects");
			$morehtmlref.='<br>'.$langs->trans('Project') . ' : ';
			if (0)
			{
				if ($action != 'classify')
					$morehtmlref.='<a href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> : ';
					if ($action == 'classify') {
						//$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
						$morehtmlref.='<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
						$morehtmlref.='<input type="hidden" name="action" value="classin">';
						$morehtmlref.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
						$morehtmlref.=$formproject->select_projects($object->thirdparty->id, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
						$morehtmlref.='<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
						$morehtmlref.='</form>';
					} else {
						$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->thirdparty->id, $object->fk_project, 'none', 0, 0, 0, 1);
					}
			} else {
				if (! empty($object->fk_project)) {
					$proj = new Project($db);
					$proj->fetch($object->fk_project);
					$morehtmlref.='<a href="'.DOL_URL_ROOT.'/projet/card.php?id=' . $object->fk_project . '" title="' . $langs->trans('ShowProject') . '">';
					$morehtmlref.=$proj->ref;
					$morehtmlref.='</a>';
				} else {
					$morehtmlref.='';
				}
			}
		}
		$morehtmlref.='</div>';
	}

	//dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'none', $morehtmlref);

	if (empty($instanceoldid)) $nodbprefix=0;
	else $nodbprefix=1;

	dol_banner_tab($object, ($instanceoldid?'refold':'ref'), $linkback, 1, ($instanceoldid?'name':'ref'), 'ref', $morehtmlref, '', $nodbprefix, '', '', 1);
}

if ($id > 0 || $instanceoldid > 0)
{
    dol_fiche_end();
}

if (($id > 0 || $instanceoldid > 0) && $action != 'edit' && $action != 'create')
{
    print '<br>';

	if (is_object($object->db2))
	{
		$object->db=$savdb;
	}


	print '<div class="fichecenter">';

	$backupdir=$conf->global->DOLICLOUD_BACKUP_PATH;

	$login=$username_web;
	$password=$password_web;

	if (! empty($instanceoldid))
	{
		$server=$object->instance.'.on.dolicloud.com';
	}
	else
	{
		$server=$object->ref_customer;
	}

	// ----- Backup instance -----
	print '<strong>INSTANCE BACKUP</strong><br>';

	print '<div class="underbanner clearboth"></div>';
	print '<table class="noborder" width="100%">';

	// Backup dir
	print '<tr class="oddeven">';
	print '<td width="20%">'.$langs->trans("BackupDir").'</td>';
	print '<td>'.$backupdir.'/'.$login.'</td>';
	print '</tr>';

	// Last backup date
	print '<tr class="oddeven">';
	print '<td class="titlefield">'.$langs->trans("DateLastBackup").'</td>';
	print '<td>';
	if (! empty($instanceoldid)) print ($object->date_lastrsync?dol_print_date($object->date_lastrsync,'dayhour','tzuser'):'');
	else
	{
		if ($object->array_options['options_latestbackup_date']) print dol_print_date($object->array_options['options_latestbackup_date'], 'dayhour');
	}
	print '</td>';
	print '</tr>';

	// Current backup status
	print '<tr class="oddeven">';
	print '<td>'.$langs->trans("CurrentBackupStatus").'</td>';
	print '<td>';
	if (! empty($instanceoldid)) print $object->backup_status;
	else {
		print $object->array_options['options_latestbackup_status'];
	}
	print '</td>';
	print '</tr>';

	print "</table><br>";


	print "</div>";

	// Barre d'actions
/*	if (! $user->societe_id)
	{
		print '<div class="tabsAction">';

		if ($user->rights->sellyoursaas->write)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=upgrade">'.$langs->trans('Upgrade').'</a>';
		}

		print "</div><br>";
	}
*/
}




// Upgrade link
$backupstringtoshow=$backupstring.' testrsync|testdatabase|confirmrsync|confirmdatabase|confirm';
print 'Backup command line string<br>';
print '<input type="text" name="backupstring" id="backupstring" value="'.$backupstringtoshow.'" size="160"><br>';
print ajax_autoselect('backupstring');


llxFooter();

$db->close();

