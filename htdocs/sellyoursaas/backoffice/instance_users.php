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
 *       \file       htdocs/sellyoursaas/backoffice/instance_users.php
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
$langs->load("contracts");
$langs->load("other");
$langs->load("commercial");
$langs->load("sellyoursaas@sellyoursaas");

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$instanceoldid = GETPOST('instanceoldid','int');
$ref        = GETPOST('ref','alpha');
$refold     = GETPOST('refold','alpha');

$error = 0; $errors = array();


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

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array array
include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
$hookmanager=new HookManager($db);


if ($id > 0 || $instanceoldid > 0 || $ref || $refold)
{
	$result=$object->fetch($id?$id:$instanceoldid, $ref?$ref:$refold);
	if ($result < 0) dol_print_error($db,$object->error);
	if ($object->element != 'contrat') $instanceoldid=$object->id;
}

$backupstring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/backup_instance.php '.$object->instance.' '.$conf->global->DOLICLOUD_INSTANCES_PATH;



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

	if ($action == "createsupportdolicloud")
	{
	    $newdb=getDoliDBInstance($conf->db->type, $object->instance.'.on.dolicloud.com', $object->username_db, $object->password_db, $object->database_db, 3306);
	    if (is_object($newdb))
	    {
	        // Get user/pass of last admin user
	        $password_crypted = dol_hash($password);
	        $sql="INSERT INTO llx_user(login, admin, pass, pass_crypted) VALUES('supportdolicloud', 1, 'supportdolicloud', '".$newdb->escape($password_crypted)."')";
	        $resql=$newdb->query($sql);
	        if (! $resql) dol_print_error($newdb);

	        // TODO Add permissions admin
	    }
	}
	if ($action == "deletesupportdolicloud")
	{
	    $newdb=getDoliDBInstance($conf->db->type, $object->instance.'.on.dolicloud.com', $object->username_db, $object->password_db, $object->database_db, 3306);
	    if (is_object($newdb))
	    {
	        $sql="DELETE FROM llx_user_rights where fk_user IN (SELECT rowid FROM llx_user WHERE login = 'supportdolicloud')";
	        $resql=$newdb->query($sql);
	        if (! $resql) dol_print_error($newdb);

	    	// Get user/pass of last admin user
	        $sql="DELETE FROM llx_user WHERE login = 'supportdolicloud'";
	        $resql=$newdb->query($sql);
	        if (! $resql) dol_print_error($newdb);
	    }
	}

	if (! in_array($action, array('resetpassword', 'confirm_resetpassword', 'createsupportdolicloud', 'deletesupportdolicloud')))
	{
		include 'refresh_action.inc.php';

		$action = 'view';
	}
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
	dol_fiche_head($head, 'users', $title, 0, 'contract');
}
else
{
	// Show tabs
	$head = dolicloud_prepare_head($object);

	$title = $langs->trans("Contract");
	dol_fiche_head($head, 'users', $title, 0, 'contract');
}

if (($id > 0 || $instanceoldid > 0) && $action != 'edit' && $action != 'create')
{
	/*
	 * Fiche en mode visualisation
	 */

	$prefix = 'with';
	$instance = 'xxxx';
	$type_db = $conf->db->type;

	if ($instanceoldid)
	{
		$prefix='on';
		$instance = $object->instance;
		$hostname_db = $object->hostname_db;
		$username_db = $object->username_db;
		$password_db = $object->password_db;
		$database_db = $object->database_db;

		$username_web = $object->username_web;
		$password_web = $object->password_web;
	}
	else	// $object is a contract (on old or new instance)
	{
		if (preg_match('/\.on\./', $object->ref_customer)) $prefix='on';
		else $prefix='with';

		$hostname_db = $object->array_options['options_hostname_db'];
		$username_db = $object->array_options['options_username_db'];
		$password_db = $object->array_options['options_password_db'];
		$database_db = $object->array_options['options_database_db'];
		$username_web = $object->array_options['options_username_os'];
		$password_web = $object->array_options['options_username_os'];
	}

	$newdb=getDoliDBInstance($type_db, $hostname_db, $username_db, $password_db, $database_db, 3306);

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
			$morehtmlref.='<br>'.$langs->trans('Project') . ' ';
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


if (empty($instanceoldid))
{
	$prefix = 'with';
	$instance = 'xxxx';
	$type_db = $conf->db->type;

	if ($instanceoldid)	// $object is old dolicloud_customers
	{
		$prefix='on';
		$instance = $object->instance;
		$hostname_db = $object->hostname_db;
		$username_db = $object->username_db;
		$password_db = $object->password_db;
		$database_db = $object->database_db;

		$username_web = $object->username_web;
		$password_web = $object->password_web;
	}
	else	// $object is a contract (on old or new instance)
	{
		if (preg_match('/\.on\./', $object->ref_customer)) $prefix='on';
		else $prefix='with';

		$hostname_db = $object->array_options['options_hostname_db'];
		$username_db = $object->array_options['options_username_db'];
		$password_db = $object->array_options['options_password_db'];
		$database_db = $object->array_options['options_database_db'];
		$username_web = $object->array_options['options_username_os'];
		$password_web = $object->array_options['options_username_os'];
	}

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
			dol_print_error($dbcustomerinstance);
		}
	}


	if ($action == 'resetpassword') {
		include_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';
		$formquestion[] = array('type' => 'text','name' => 'newpassword','label' => $langs->trans("NewPassword"),'value' => getRandomPassword(false));

		print $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id . '&remoteid=' . GETPOST('remoteid','int'), $langs->trans('ResetPassword'), $langs->trans('ConfirmResetPassword'), 'confirm_resetpassword', $formquestion, 0, 1);
	}

	print '<strong>INSTANCE '.$conf->global->SELLYOURSAAS_NAME.' (Customer instance '.$dbcustomerinstance->database_host.')</strong><br>';
	print '<table class="border" width="100%">';

	print_user_table($dbcustomerinstance);

	print "</table><br>";
}


// ----- Instance DoliCloud v1 -----
if (! empty($instanceoldid))
{
	print '<strong>INSTANCE DOLICLOUD v1 ('.$newdb->database_host.')</strong><br>';

	print_user_table($newdb);
}


// Barre d'actions
if (! $user->societe_id)
{
    print '<div class="tabsAction">';

    if ($user->rights->sellyoursaas->create)
    {
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?'.($instanceoldid?'instanceoldid':'id').'='.$object->id.'&amp;action=createsupportdolicloud">'.$langs->trans('CreateSupportUser').'</a>';
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?'.($instanceoldid?'instanceoldid':'id').'='.$object->id.'&amp;action=deletesupportdolicloud">'.$langs->trans('DeleteSupportUser').'</a>';
    }

    print "</div><br>";
}


// Dolibarr instance login
$url='https://'.$object->instance.'.on.dolicloud.com?username='.$lastloginadmin.'&amp;password='.$lastpassadmin;
$link='<a href="'.$url.'" target="_blank">'.$url.'</a>';
print 'Dolibarr link<br>';
//print '<input type="text" name="dashboardconnectstring" value="'.dashboardconnectstring.'" size="100"><br>';
print $link.'<br>';
print '<br>';


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
	global $instanceoldid;
	global $id;

	print '<table class="noborder" width="100%">';

	// Nb of users
	print '<tr class="liste_titre">';
	print '<td>'.$langs->trans("Login").'</td>';
	print '<td>'.$langs->trans("Lastname").'</td>';
	print '<td>'.$langs->trans("Firstname").'</td>';
	print '<td>'.$langs->trans("Admin").'</td>';
	print '<td>'.$langs->trans("Email").'</td>';
	print '<td>'.$langs->trans("Pass").'</td>';
	print '<td>'.$langs->trans("DateCreation").'</td>';
	print '<td>'.$langs->trans("DateModification").'</td>';
	print '<td>'.$langs->trans("DateLastLogin").'</td>';
	print '<td>'.$langs->trans("Entity").'</td>';
	print '<td>'.$langs->trans("ParentsId").'</td>';
	print '<td>'.$langs->trans("Status").'</td>';
	print '<td></td>';
	print '</tr>';

	if (is_object($newdb) && $newdb->connected)
	{
		// Get user/pass of last admin user
		$sql ="SELECT rowid, login, lastname, firstname, admin, email, pass, pass_crypted, datec, tms as datem, datelastlogin, fk_soc, fk_socpeople, fk_member, entity, statut";
		$sql.=" FROM llx_user ORDER BY statut DESC";
		$resql=$newdb->query($sql);
		if (empty($resql))	// Alternative for 3.7-
		{
			$sql ="SELECT rowid, login, lastname as lastname, firstname, admin, email, pass, pass_crypted, datec, tms as datem, datelastlogin, fk_societe, fk_socpeople, fk_member, entity, statut";
			$sql.=" FROM llx_user ORDER BY statut DESC";
			$resql=$newdb->query($sql);
			if (empty($resql))	// Alternative for 3.3-
    		{
    			$sql ="SELECT rowid, login, nom as lastname, prenom as firstname, admin, email, pass, pass_crypted, datec, tms as datem, datelastlogin, fk_societe, fk_socpeople, fk_member, entity, statut";
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
				$obj = $newdb->fetch_object($resql);
				print '<tr class="oddeven">';
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
				print '<td>';
				if ($obj->fk_user > 0) print 'Parent user: '.$obj->fk_user;
				if ($obj->fk_soc > 0) print 'Parent thirdparty: '.$obj->fk_soc;
				if ($obj->fk_socpeople > 0) print 'Parent contact: '.$obj->fk_socpeople;
				if ($obj->fk_member > 0) print 'Parent member: '.$obj->fk_member;
				print '</td>';
				print '<td align="right">'.$obj->statut.'</td>';
				print '<td align="right">';

				print '<a href="'.$_SERVER["PHP_SELF"].'?action=resetpassword&remoteid='.$obj->rowid.($instanceoldid?'&instanceoldid='.$instanceoldid:('&id='.$id)).'">'.img_picto('ResetPassword', 'object_technic').'</a>';

				print '</td>';
				print '</tr>';
				$i++;
			}
		}
		else
		{
			dol_print_error($newdb);
		}
	}
	else
	{
		print '<tr><td class="opacitymedium">'.$langs->trans("FailedToConnectMayBeOldInstance").'</td></tr>';
	}

	print "</table>";
}
