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
 *       \file       htdocs/sellyoursaas/backoffice/instance_links.php
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

$upgradestring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/rsync_instance.php '.$conf->global->DOLICLOUD_LASTSTABLEVERSION_DIR.' '.$object->instance;



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

	// Add action to create file, etc...
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
	dol_fiche_head($head, 'upgrade', $title, 0, 'contract');
}
else
{
	// Show tabs
	$head = dolicloud_prepare_head($object);

	$title = $langs->trans("Contract");
	dol_fiche_head($head, 'upgrade', $title, 0, 'contract');
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
		$hostname_db  = $object->hostname_db;
		$username_db  = $object->username_db;
		$password_db  = $object->password_db;
		$database_db  = $object->database_db;
		$port_db      = $object->port_db;
		$username_os  = $object->username_web;		// $object->username_os not used on dolicloudcustomer
		$password_os  = $object->password_web;		// $object->password_os not used on dolicloudcustomer
		$hostname_os  = $object->hostname_web;		// $object->password_os not used on dolicloudcustomer
		$username_web = $object->email;
		$password_web = $object->xxx;
		$hostname_web = $object->hostname_web;

		$object->username_os  = $username_os;
		$object->password_os  = $password_os;
		$object->hostname_os  = $object->instance.'.on.dolicloud.com';

	}
	else	// $object is a contract (on old or new instance)
	{
		$object->fetch_thirdparty();

		$hostname_db  = $object->array_options['options_hostname_db'];
		$username_db  = $object->array_options['options_username_db'];
		$password_db  = $object->array_options['options_password_db'];
		$database_db  = $object->array_options['options_database_db'];
		$port_db      = $object->array_options['options_port_db'];
		$hostname_os  = $object->array_options['options_hostname_os'];
		$username_os  = $object->array_options['options_username_os'];
		$password_os  = $object->array_options['options_password_os'];
		$username_web = $object->thirdparty->email;
		$password_web = $object->thirdparty->array_options['options_password'];

		$tmp = explode('.', $object->ref_customer, 2);
		$object->instance = $tmp[0];

		$object->hostname_db  = $hostname_db;
		$object->username_db  = $username_db;
		$object->password_db  = $password_db;
		$object->database_db  = $database_db;
		$object->port_db      = $port_db;
		$object->username_os  = $username_os;
		$object->password_os  = $password_os;
		$object->hostname_os  = $hostname_os;
		$object->username_web = $username_web;
		$object->password_web = $password_web;
		$object->hostname_web = $hostname_os;
	}

	$newdb=getDoliDBInstance($type_db, $hostname_db, $username_db, $password_db, $database_db, $port_db?$port_db:3306);

	$confinstance = new Conf();

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
			setEventMessages('Success to connect to server, but failed to switch on database.'.$newdb->lasterror(), null, 'errors');
		}

	    $confinstance->setValues($newdb);
	}


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

	if (empty($instanceoldid)) $nodbprefix=0;
	else $nodbprefix=1;

	dol_banner_tab($object, ($instanceoldid?'refold':'ref'), $linkback, 1, ($instanceoldid?'name':'ref'), 'ref', $morehtmlref, '', $nodbprefix, '', '', 1);


	if (is_object($object->db2))
	{
		$object->db=$savdb;
	}

	print '<div class="fichecenter">';

	$backupdir=$conf->global->DOLICLOUD_BACKUP_PATH;

	$dirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$object->database_db);
	$login=$object->username_web;
	$password=$object->password_web;

	if (! empty($instanceoldid))
	{
		$server=$object->instance.'.on.dolicloud.com';
	}
	else
	{
		$server=$object->ref_customer;
	}

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

	print '</div>';
}


if ($id > 0 || $instanceoldid > 0)
{
	dol_fiche_end();
}

print '<br>';


if ($object->nbofusers == 0)
{
    // Try to get data
    if (is_object($newdb) && $newdb->connected)
    {
        $sql="SELECT COUNT(login) as nbofusers FROM llx_user WHERE statut <> 0 AND login <> '".$conf->global->SELLYOURSAAS_LOGIN_FOR_SUPPORT."'";
        $resql=$newdb->query($sql);
        if ($resql)
        {
            $obj = $newdb->fetch_object($resql);
            $object->nbofusers	= $obj->nbofusers;
        }
        else
        {
            setEventMessages('Failed to read remote customer instance: '.$newdb->lasterror(),'','warnings');
        }
    }
}


// Some data of instance

print '<div class="fichecenter">';

print '<table class="noborder" width="100%">';

// Nb of users
print '<tr><td width="20%">'.$langs->trans("NbOfUsers").'</td><td><font size="+2">'.round($object->nbofusers).'</font></td>';
print '<td></td><td>';
if (! $object->user_id && $user->rights->sellyoursaas->write)
{
    print ' <a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
}
print '</td>';
print '</tr>';

// Version
print '<tr>';
print '<td>'.$langs->trans("Version").'</td>';
print '<td colspan="3">MAIN_VERSION_LAST_INSTALL='.$confinstance->global->MAIN_VERSION_LAST_INSTALL.' / MAIN_VERSION_LAST_UPGRADE='.$confinstance->global->MAIN_VERSION_LAST_UPGRADE.'</td>';
print '</tr>';

// Modules
print '<tr>';
print '<td>'.$langs->trans("Modules").'</td>';
print '<td colspan="3">';
$i=0;
foreach($confinstance->global as $key => $val)
{
    if (preg_match('/^MAIN_MODULE_[^_]+$/',$key) && ! empty($val))
    {
        if ($i > 0) print ', ';
        print preg_replace('/^MAIN_MODULE_/','',$key);
        $i++;
    }
}
print '</td>';
print '</tr>';

// Authorized key file
print '<tr>';
print '<td>'.$langs->trans("Authorized_keyInstalled").'</td><td>'.($object->array_options['options_fileauthorizekey']?$langs->trans("Yes").' - '.dol_print_date($object->array_options['options_fileauthorizekey'],'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?'.(empty($instanceoldid)?'id=':'instanceoldid=').$object->id.'&action=addauthorizedkey">'.$langs->trans("Create").'</a>)';
print ($object->array_options['options_fileauthorizekey']?' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?'.(empty($instanceoldid)?'id=':'instanceoldid=').$object->id.'&action=delauthorizedkey">'.$langs->trans("Delete").'</a>)':'');
print '</td>';
print '<td></td><td></td>';
print '</tr>';

// Install.lock file
print '<tr>';
print '<td>'.$langs->trans("LockfileInstalled").'</td><td>'.($object->array_options['options_filelock']?$langs->trans("Yes").' - '.dol_print_date($object->array_options['options_filelock'],'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?'.(empty($instanceoldid)?'id=':'instanceoldid=').$object->id.'&action=addinstalllock">'.$langs->trans("Create").'</a>)';
print ($object->array_options['options_filelock']?' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?'.(empty($instanceoldid)?'id=':'instanceoldid=').$object->id.'&action=delinstalllock">'.$langs->trans("Delete").'</a>)':'');
print '</td>';
print '<td></td><td></td>';
print '</tr>';

print "</table><br>";

print "</div>";	//  End fiche=center


print getListOfLinks($object, $lastloginadmin, $lastpassadmin, $instanceoldid);


llxFooter();

$db->close();

