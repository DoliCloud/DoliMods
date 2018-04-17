<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/sellyoursaas/backoffice/instance_info.php
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
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
dol_include_once("/sellyoursaas/core/lib/dolicloud.lib.php");
dol_include_once("/sellyoursaas/backoffice/lib/refresh.lib.php");
dol_include_once('/sellyoursaas/class/dolicloud_customers.class.php');
dol_include_once('/sellyoursaas/class/cdolicloudplans.class.php');

$langs->load("admin");
$langs->load("companies");
$langs->load("users");
$langs->load("contracts");
$langs->load("other");
$langs->load("commercial");
$langs->load("sellyoursaas@sellyoursaas");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$instanceoldid= GETPOST('instanceoldid','alpha');
$ref        = GETPOST('ref','alpha');
$refold     = GETPOST('refold','alpha');
$date_registration  = dol_mktime(0, 0, 0, GETPOST("date_registrationmonth",'int'), GETPOST("date_registrationday",'int'), GETPOST("date_registrationyear",'int'), 1);
$date_endfreeperiod = dol_mktime(0, 0, 0, GETPOST("endfreeperiodmonth",'int'), GETPOST("endfreeperiodday",'int'), GETPOST("endfreeperiodyear",'int'), 1);
if (empty($date_endfreeperiod) && ! empty($date_registration)) $date_endfreeperiod=$date_registration+15*24*3600;

$error = 0; $errors = array();


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
	$hostname_os = $object->instance.'on.dolicloud.com';
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
	$password_web = $object->array_options['options_username_os'];
	$hostname_os = $object->array_options['options_hostname_os'];
}


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
$user->rights->sellyoursaas->delete = $user->rights->sellyoursaas->write;
$result = restrictedArea($user, 'sellyoursaas', 0, '','');

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('contractcard','globalcard'));

if ($id > 0 || $instanceoldid > 0 || $ref || $refold)
{
	$result=$object->fetch($id?$id:$instanceoldid, $ref?$ref:$refold);
	if ($result < 0) dol_print_error($db,$object->error);
	if ($object->element != 'contrat') $instanceoldid=$object->id;
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

	// Add customer
	if ($action == 'add' && $user->rights->sellyoursaas->write)
	{
		$db->begin();

		if ($canvas) $object->canvas=$canvas;

		$object->instance		= $_POST["instance"];
		$object->organization	= $_POST["organization"];
		$object->plan			= $_POST["plan"];
		$object->lastname		= $_POST["lastname"];
		$object->firstname		= $_POST["firstname"];
		$object->address		= $_POST["address"];
		$object->zip			= $_POST["zipcode"];
		$object->town			= $_POST["town"];
		$object->country_id		= $_POST["country_id"];
		$object->state_id       = $_POST["state_id"];
		$object->vat_number     = $_POST["vat_number"];
		$object->email			= $_POST["email"];
		$object->phone        	= $_POST["phone"];
		$object->note			= $_POST["note"];
		$object->hostname_web	= $_POST["hostname_web"];
		$object->username_web	= $_POST["username_web"];
		$object->password_web	= $_POST["password_web"];
		$object->hostname_db	= $_POST["hostname_db"];
		$object->database_db	= $_POST["database_db"];
		$object->username_db    = $_POST["username_db"];
		$object->password_db    = $_POST["password_db"];

		$object->status         = $_POST["status"];
		$object->date_registration  = $date_registration;
		$object->date_endfreeperiod = $date_endfreeperiod;
		$object->partner		= $_POST["partner"];
		$object->source			= $_POST["source"];

		if (empty($_POST["instance"]) || empty($_POST["organization"]) || empty($_POST["plan"]) || empty($_POST["email"]))
		{
			$error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Instance").",".$langs->transnoentitiesnoconv("Organization").",".$langs->transnoentitiesnoconv("Plan").",".$langs->transnoentitiesnoconv("EMail"));
			$action = 'create';
		}

		if (! $error)
		{
			$id =  $object->create($user);
			if ($id <= 0)
			{
				$error++; $errors=array_merge($errors,($object->error?array($object->error):$object->errors));
				$action = 'create';
			}
		}

		if (! $error && $id > 0)
		{
			$db->commit();
			if (! empty($backtopage)) $url=$backtopage;
			else $url=$_SERVER["PHP_SELF"].'?instanceoldid='.$id;
			Header("Location: ".$url);
			exit;
		}
		else
		{
			$db->rollback();
		}
	}

	if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->sellyoursaas->write)
	{
		$result=$object->fetch($id);

		$result = $object->delete();
		if ($result > 0)
		{
			Header("Location: ".dol_buildpath('/sellyoursaas/backoffice/dolicloud_list.php',1));
			exit;
		}
		else
		{
			$error=$object->error; $errors=$object->errors;
		}
	}

	if ($action == 'update' && ! $_POST["cancel"] && $user->rights->sellyoursaas->write)
	{
		if (empty($_POST["organization"]) || empty($_POST["plan"]) || empty($_POST["email"]))
		{
			$error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Instance").",".$langs->transnoentitiesnoconv("Organization").",".$langs->transnoentitiesnoconv("Plan").",".$langs->transnoentitiesnoconv("EMail"));
			$action = 'edit';
		}

		if (! $error)
		{
			$object->oldcopy=dol_clone($object, 1);

			$object->instance    	= $_POST["instance"];
			$object->organization	= $_POST["organization"];
			$object->plan			= $_POST["plan"];
			$object->lastname		= $_POST["lastname"];
			$object->firstname		= $_POST["firstname"];

			$object->address		= $_POST["address"];
			$object->zip			= $_POST["zipcode"];
			$object->town			= $_POST["town"];
			$object->state_id   	= $_POST["state_id"];
			$object->country_id		= $_POST["country_id"];
			$object->vat_number     = $_POST["vat_number"];

			$object->email			= $_POST["email"];
			$object->phone    		= $_POST["phone"];
			$object->note			= $_POST["note"];

			$object->hostname_web	= $_POST["hostname_web"];
			$object->username_web	= $_POST["username_web"];
			$object->password_web	= $_POST["password_web"];
			$object->hostname_db	= $_POST["hostname_db"];
			$object->database_db	= $_POST["database_db"];
			$object->username_db    = $_POST["username_db"];
			$object->password_db    = $_POST["password_db"];

			$object->status         = $_POST["status"];
			$object->date_registration  = $date_registration;
			$object->date_endfreeperiod = $date_endfreeperiod;
			$object->partner		= $_POST["partner"];
			$object->source			= $_POST["source"];

			$result = $object->update($user);

			if ($result > 0)
			{
				if ($object->status == 'SUSPENDED' && $object->oldcopy->status != 'SUSPENDED')
				{
					$action = 'disable_instance';
				}
				if ($object->status != 'SUSPENDED' && $object->oldcopy->status == 'SUSPENDED')
				{
					$action = 'enable_instance';
				}
			}
			else
			{
				$error=$object->error; $errors=$object->errors;
				$action = 'edit';
			}
		}
	}


	// Add action to create file, etc...
	include 'refresh_action.inc.php';
}


/*
 *	View
 */


$help_url='';
llxHeader('',$langs->trans("Users"),$help_url);

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
	dol_fiche_head($head, 'infoinstance', $title, 0, 'contract');
}
else
{
	// Show tabs
	$head = dolicloud_prepare_head($object);

	$title = $langs->trans("Contract");
	dol_fiche_head($head, 'infoinstance', $title, 0, 'contract');
}

if (($id > 0 || $instanceoldid > 0) && $action != 'edit' && $action != 'create')
{
	/*
	 * Fiche en mode visualisation
	 */

	$newdb=getDoliDBInstance($type_db, $hostname_db, $username_db, $password_db, $database_db, $port_db);

	if (is_object($newdb) && $newdb->connected)
	{
		// Get user/pass of last admin user
		$sql="SELECT login, pass FROM llx_user WHERE admin = 1 ORDER BY statut DESC, datelastlogin DESC LIMIT 1";
		$resql=$newdb->query($sql);
		if ($resql)
		{
			$obj = $newdb->fetch_object($resql);
			$object->lastlogin_admin=$obj->login;
			$object->lastpass_admin=$obj->pass;
			$lastloginadmin=$object->lastlogin_admin;
			$lastpassadmin=$object->lastpass_admin;
		}
		else
		{
			setEventMessages('Failed to read remote customer instance: '.$newdb->lasterror(),'','warnings');
		}
	}
	//	else print 'Error, failed to connect';



	if (is_object($object->db2))
	{
		$savdb=$object->db;
		$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function.  $db = stratus5 database
	}

	$object->fetch_thirdparty();

	//$object->email = $object->thirdparty->email;

	// Contract card

	$linkback = '<a href="'.DOL_URL_ROOT.'/contrat/list.php?restore_lastsearch_values=1'.(! empty($socid)?'&socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

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
		$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1);
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

	dol_banner_tab($object, ($instanceoldid?'refold':'ref'), $linkback, 1, ($instanceoldid?'name':'ref'), 'ref', $morehtmlref, '', 1, '', '', 1);

	if (is_object($object->db2))
	{
		$object->db=$savdb;
	}

	print '<div class="fichecenter">';
	print '</div>';
}

if ($id > 0 || $instanceoldid > 0)
{
	dol_fiche_end();
}

print '<br>';



if (($id > 0 || $instanceoldid > 0) && $action != 'edit' && $action != 'create')
{
	$dbcustomerinstance=getDoliDBInstance($type_db, $hostname_db, $username_db, $password_db, $database_db, 3306);

	if (is_object($dbcustomerinstance) && $dbcustomerinstance->connected)
	{
		// Get user/pass of last admin user
		$sql="SELECT login, pass FROM llx_user WHERE admin = 1 ORDER BY statut DESC, datelastlogin DESC LIMIT 1";
		$resql=$dbcustomerinstance->query($sql);
		if ($resql)
		{
			$obj = $dbcustomerinstance->fetch_object($resql);
			$object->lastlogin_admin=$obj->login;
			$object->lastpass_admin=$obj->pass;
			$lastloginadmin=$object->lastlogin_admin;
			$lastpassadmin=$object->lastpass_admin;
		}
		else
		{
			setEventMessages('Failed to read remote customer instance: '.$dbcustomerinstance->lasterror(),'','warnings');
		}
	}

	if (! preg_match('/\.on\./', $object->ref_customer))
	{
		print '<div class="fichecenter">';

		// ----- SellYourSaas instance -----
		print '<strong>INSTANCE '.$conf->global->SELLYOURSAAS_NAME.' (Customer instance '.$dbcustomerinstance->database_host.')</strong><br>';

		// Last refresh
		print ' - '.$langs->trans("DateLastCheck").': '.($object->date_lastcheck?dol_print_date($object->date_lastcheck,'dayhour','tzuser'):$langs->trans("Never"));

		if (! $object->user_id && $user->rights->sellyoursaas->write)
		{
			print ' <a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
		}
		print '<br>';

		print '<div class="underbanner clearboth"></div>';
		print '<table class="border" width="100%">';

		// Instance / Organization
		/*
		 print '<tr><td width="20%">'.$langs->trans("Instance").'</td><td colspan="3">';
		 $savdb=$object->db;
		 $object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function
		 print $form2->showrefnav($object,'instance','',1,'name','instance','','',1);
		 $object->db=$savdb;
		 print '</td></tr>';
		 print '<tr><td>'.$langs->trans("Organization").'</td><td colspan="3">';
		 print $object->organization;
		 print '</td></tr>';
		 */

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
		print ' <a href="https://www.dolicloud.com/fr/component/content/article/134-pricing" target="_blank">('.$langs->trans("Prices").')';
		print '</td>';
		print '</tr>';

		// Partner
		print '<tr><td width="20%">'.$langs->trans("Partner").'</td><td width="30%">'.$object->partner.'</td><td width="20%">'.$langs->trans("Source").'</td><td>'.($object->source?$object->source:'').'</td></tr>';

		// Lastname / Firstname
		print '<tr><td width="20%">'.$langs->trans("Lastname").'</td><td width="30%">'.$object->lastname.'</td>';
		print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%">'.$object->firstname.'</td></tr>';

		// Address
		print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3">';
		dol_print_address($object->address,'gmap','dolicloud',$object->id);
		print '</td></tr>';

		// Zip Town
		print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
		print $object->zip;
		if ($object->zip) print '&nbsp;';
		print $object->town.'</td></tr>';

		// Country
		print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';
		$img=picto_from_langcode($object->country_code);
		if ($object->country_code) print $img.' ';
		print getCountry($object->country_code,0);
		print '</td></tr>';

		// State
		if (empty($conf->global->SOCIETE_DISABLE_STATE))
		{
			print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">'.$object->state.'</td>';
		}

		// VAT number
		print '<tr><td>'.$langs->trans("VATIntra").'</td><td colspan="3">'.$object->vat_number.'</td>';
		print '</tr>';

		// Phone
		print '<tr><td>'.$langs->trans("PhonePro").'</td><td colspan="3">'.dol_print_phone($object->phone,$object->country_code,$object->id,0,'AC_TEL').'</td>';
		print '</tr>';

		// Note
		print '<tr><td class="tdtop">'.$langs->trans("Note").'</td><td colspan="3">';
		print nl2br($object->note);
		print '</td></tr>';

		// SFTP
		print '<tr><td width="20%">'.$langs->trans("SFTP Server").'</td><td>'.$object->hostname_web.'</td>';
		print '<td>'.$langs->trans("FsPath").'</td><td>'.$object->fs_path.'</td>';
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

		// Nb of users
		print '<tr><td width="20%">'.$langs->trans("NbOfUsers").'</td><td><font size="+2">'.round($object->nbofusers).'</font></td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Dates
		print '<tr><td width="20%">'.$langs->trans("DateDeployment").'</td><td width="30%">'.dol_print_date($object->date_registration,'dayhour');
		//print ' (<a href="'.dol_buildpath('/sellyoursaas/backoffice/dolicloud_card.php',1).'?instanceoldid='.$object->id.'&amp;action=setdate&amp;date=">'.$langs->trans("SetDate").'</a>)';
		print '</td>';
		print '<td></td><td></td>';
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
		print '<td>'.$langs->trans("Version").'</td><td>'.$object->version.'</td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Modules
		print '<tr>';
		print '<td>'.$langs->trans("Modules").'</td><td>'.join(', ',explode(',',$object->modulesenabled)).'</td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Authorized key file
		print '<tr>';
		print '<td>'.$langs->trans("Authorized_keyInstalled").'</td><td>'.($object->fileauthorizedkey?$langs->trans("Yes").' - '.dol_print_date($object->fileauthorizedkey,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
		print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&action=addauthorizedkey">'.$langs->trans("Create").'</a>)';
		print '</td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Install.lock file
		print '<tr>';
		print '<td>'.$langs->trans("LockfileInstalled").'</td><td>'.($object->filelock?$langs->trans("Yes").' - '.dol_print_date($object->filelock,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
		print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&action=addinstalllock">'.$langs->trans("Create").'</a>)';
		print ($object->filelock?' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&action=delinstalllock">'.$langs->trans("Delete").'</a>)':'');
		print '</td>';
		print '<td></td><td></td>';
		print '</tr>';

		print '</table>';

		print '</div>';
		print '</div>';
	}

	if (preg_match('/\.on\./', $object->ref_customer) || get_class($object) == 'Dolicloud_customers')
	{
		if (get_class($object) != 'Dolicloud_customers')
		{
			$ref_instance = $object->ref_customer;

			if (! is_object($db2))
			{
				$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
				if ($db2->error)
				{
					dol_print_error($db2,"host=".$conf->db->host.", port=".$conf->db->port.", user=".$conf->db->user.", databasename=".$conf->db->name.", ".$db2->error);
					exit;
				}
			}

			$object = new Dolicloud_customers($db, $db2);
			$object->fetch(0, $ref_instance);
		}
		/*var_dump($dolicloudcustomer); */
		//var_dump($object);

		print '<div class="fichecenter">';

		if (is_object($object->db2))
		{
			$savdb=$object->db;
			$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function.  $db = stratus5 database
		}

		dol_banner_tab($object,($instanceoldid?'refold':'ref'),'',1,($instanceoldid?'name':'ref'),'ref','','',1);

		if (is_object($object->db2))
		{
			$savdb=$object->db;
			$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function.  $db = stratus5 database
		}

		// ----- DoliCloud instance -----
		print '<strong>INSTANCE DOLICLOUD v1</strong>';
		// Last refresh
		print ' - '.$langs->trans("DateLastCheck").': '.($object->date_lastcheck?dol_print_date($object->date_lastcheck,'dayhour','tzuser'):$langs->trans("Never"));

		if (! $object->user_id && $user->rights->sellyoursaas->write)
		{
			print ' <a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
		}
		print '<br>';

		print '<div class="underbanner clearboth"></div>';
		print '<table class="border" width="100%">';

		// Instance / Organization
		/*
		print '<tr><td width="20%">'.$langs->trans("Instance").'</td><td colspan="3">';
		$savdb=$object->db;
		$object->db=$object->db2;	// To have ->db to point to db2 for showrefnav function
		print $form2->showrefnav($object,'instance','',1,'name','instance','','',1);
		$object->db=$savdb;
		print '</td></tr>';
		print '<tr><td>'.$langs->trans("Organization").'</td><td colspan="3">';
		print $object->organization;
		print '</td></tr>';
		*/

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
		print ' <a href="https://www.dolicloud.com/fr/component/content/article/134-pricing" target="_blank">('.$langs->trans("Prices").')';
		print '</td>';
		print '</tr>';

		// Partner
		print '<tr><td width="20%">'.$langs->trans("Partner").'</td><td width="30%">'.$object->partner.'</td><td width="20%">'.$langs->trans("Source").'</td><td>'.($object->source?$object->source:'').'</td></tr>';

		// Lastname / Firstname
		print '<tr><td width="20%">'.$langs->trans("Lastname").'</td><td width="30%">'.$object->lastname.'</td>';
		print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%">'.$object->firstname.'</td></tr>';

		// Address
		print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3">';
		dol_print_address($object->address,'gmap','dolicloud',$object->id);
		print '</td></tr>';

		// Zip Town
		print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
		print $object->zip;
		if ($object->zip) print '&nbsp;';
		print $object->town.'</td></tr>';

		// Country
		print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';
		$img=picto_from_langcode($object->country_code);
		if ($object->country_code) print $img.' ';
		print getCountry($object->country_code,0);
		print '</td></tr>';

		// State
		if (empty($conf->global->SOCIETE_DISABLE_STATE))
		{
			print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">'.$object->state.'</td>';
		}

		// VAT number
		print '<tr><td>'.$langs->trans("VATIntra").'</td><td colspan="3">'.$object->vat_number.'</td>';
		print '</tr>';

		// Phone
		print '<tr><td>'.$langs->trans("PhonePro").'</td><td colspan="3">'.dol_print_phone($object->phone,$object->country_code,$object->id,0,'AC_TEL').'</td>';
		print '</tr>';

		// Note
		print '<tr><td class="tdtop">'.$langs->trans("Note").'</td><td colspan="3">';
		print nl2br($object->note);
		print '</td></tr>';

		// SFTP
		print '<tr><td width="20%">'.$langs->trans("SFTP Server").'</td><td>'.$object->hostname_web.'</td>';
		print '<td>'.$langs->trans("FsPath").'</td><td>'.$object->fs_path.'</td>';
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

		// Nb of users
		print '<tr><td width="20%">'.$langs->trans("NbOfUsers").'</td><td><font size="+2">'.round($object->nbofusers).'</font></td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Dates
		print '<tr><td width="20%">'.$langs->trans("DateDeployment").'</td><td width="30%">'.dol_print_date($object->date_registration,'dayhour');
		//print ' (<a href="'.dol_buildpath('/sellyoursaas/backoffice/dolicloud_card.php',1).'?instanceoldid='.$object->id.'&amp;action=setdate&amp;date=">'.$langs->trans("SetDate").'</a>)';
		print '</td>';
		print '<td></td><td></td>';
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
		print '<td>'.$langs->trans("Version").'</td><td>'.$object->version.'</td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Modules
		print '<tr>';
		print '<td>'.$langs->trans("Modules").'</td><td>'.join(', ',explode(',',$object->modulesenabled)).'</td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Authorized key file
		print '<tr>';
		print '<td>'.$langs->trans("Authorized_keyInstalled").'</td><td>'.($object->fileauthorizedkey?$langs->trans("Yes").' - '.dol_print_date($object->fileauthorizedkey,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
		print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&action=addauthorizedkey">'.$langs->trans("Create").'</a>)';
		print '</td>';
		print '<td></td><td></td>';
		print '</tr>';

		// Install.lock file
		print '<tr>';
		print '<td>'.$langs->trans("LockfileInstalled").'</td><td>'.($object->filelock?$langs->trans("Yes").' - '.dol_print_date($object->filelock,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
		print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&action=addinstalllock">'.$langs->trans("Create").'</a>)';
		print ($object->filelock?' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?instanceoldid='.$object->id.'&action=delinstalllock">'.$langs->trans("Delete").'</a>)':'');
		print '</td>';
		print '<td></td><td></td>';
		print '</tr>';

		print "</table><br>";

		print "</div>";	//  End fiche=center
	}




	print '<table width="100%"><tr><td width="50%" valign="top">';

    print '</td><td valign="top" width="50%">';

    if (empty($instanceoldid))
    {
    	// List of actions on element
    	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
    	$formactions=new FormActions($db);
    	$somethingshown = $formactions->showactions($object,'contract',0,1);
    }
    else
    {
		// List of actions on element
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
		$formactions=new FormActions($db);
		$somethingshown = $formactions->showactions($object,'dolicloudcustomers',0,1);
    }

	print '</td></tr></table>';
}

if ($id > 0 || $instanceid > 0 || $action == 'create')
{
	dol_fiche_end();
}


llxFooter();

$db->close();
