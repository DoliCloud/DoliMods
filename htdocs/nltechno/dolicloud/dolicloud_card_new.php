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
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
dol_include_once("/nltechno/core/lib/dolicloud.lib.php");
dol_include_once("/nltechno/core/dolicloud/lib/refresh.lib.php");
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
$date_registration  = dol_mktime(0, 0, 0, GETPOST("date_registrationmonth"), GETPOST("date_registrationday"), GETPOST("date_registrationyear"), 1);
$date_endfreeperiod = dol_mktime(0, 0, 0, GETPOST("endfreeperiodmonth"), GETPOST("endfreeperiodday"), GETPOST("endfreeperiodyear"), 1);
if (empty($date_endfreeperiod) && ! empty($date_registration)) $date_endfreeperiod=$date_registration+15*24*3600;

$error = 0; $errors = array();



$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->db->host.", port=".$conf->db->port.", user=".$conf->db->user.", databasename=".$conf->db->name.", ".$db2->error);
	exit;
}



$object = new DoliCloudCustomerNew($db,$db2);

// Security check
$user->rights->nltechno->dolicloud->delete = $user->rights->nltechno->dolicloud->write;
$result = restrictedArea($user, 'nltechno', 0, '','dolicloud');

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
$hookmanager=new HookManager($db);

if ($id > 0 || $instance)
{
	$result=$object->fetch($id,($id?'':$instance));
	if ($result < 0) dol_print_error($db,$object->error);
}


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

	// Add customer
	if ($action == 'add' && $user->rights->nltechno->dolicloud->write)
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
			else $url=$_SERVER["PHP_SELF"].'?id='.$id;
			Header("Location: ".$url);
			exit;
		}
		else
		{
			$db->rollback();
		}
	}

	if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->nltechno->dolicloud->write)
	{
		$result=$object->fetch($id);

		$result = $object->delete();
		if ($result > 0)
		{
			Header("Location: ".dol_buildpath('/nltechno/dolicloud/dolicloud_list.php',1));
			exit;
		}
		else
		{
			$error=$object->error; $errors=$object->errors;
		}
	}

	if ($action == 'update' && ! $_POST["cancel"] && $user->rights->nltechno->dolicloud->write)
	{
		if (empty($_POST["organization"]) || empty($_POST["plan"]) || empty($_POST["email"]))
		{
			$error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Instance").",".$langs->transnoentitiesnoconv("Organization").",".$langs->transnoentitiesnoconv("Plan").",".$langs->transnoentitiesnoconv("EMail"));
			$action = 'edit';
		}

		if (! $error)
		{
			$object->oldcopy=dol_clone($object);

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
	include 'refresh_action_new.inc.php';
}


/*
 *	View
 */

$help_url='';
llxHeader('',$langs->trans("DoliCloudInstances"),$help_url);

$form = new Form($db);
$form2 = new Form($db2);
$formother = new FormOther($db);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';
$arraystatus=Dolicloudcustomernew::$listOfStatus;


// Confirm deleting object
if ($user->rights->nltechno->dolicloud->write)
{
	if ($action == 'delete')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$object->id,$langs->trans("DeleteContact"),$langs->trans("ConfirmDeleteContact"),"confirm_delete",'',0,1);
		if ($ret == 'html') print '<br>';
	}
}


// Tabs
if ($id > 0 || $instance || $action == 'create')
{
	// Show tabs
	$head = dolicloud_prepare_head($object,'_new');

	$title = $langs->trans("DoliCloudInstances");
	dol_fiche_head($head, 'card', $title, 0, 'contact');
}

if ($user->rights->nltechno->dolicloud->write)
{
	if ($action == 'create')
	{
		/*
		 * Fiche en mode creation
		 */
		$object->canvas=$canvas;

		// We set country_id, country_code and label for the selected country
		$object->country_id=$_POST["country_id"]?$_POST["country_id"]:$mysoc->country_id;
		if ($object->country_id)
		{
			$tmparray=getCountry($object->country_id,'all');
			$object->country_code = $tmparray['code'];
			$object->country      = $tmparray['label'];
		}

		$title = $addcontact = $langs->trans("DoliCloudCustomers");

		// Show errors
		dol_htmloutput_errors(is_numeric($error)?'':$error,$errors);

		if ($conf->use_javascript_ajax)
		{
			print "\n".'<script type="text/javascript" language="javascript">';
			print '
			function initstatus()
			{
				//if (jQuery("#status").val()==\'TRIAL\' || jQuery("#status").val()==\'TRIAL_EXPIRED\') { jQuery("#hideendfreetrial").show() }
				//else { jQuery("#hideendfreetrial").hide() };
				jQuery("#hideendfreetrial").show();
			}

			jQuery(document).ready(function () {
				jQuery("#selectcountry_id").change(function() {
					document.formsoc.action.value="create";
					document.formsoc.submit();
				});
				jQuery("#instance").keyup(function() {
					var dolicloud=".on.dolicloud.com";
					jQuery("#hostname_web").val(jQuery("#instance").val()+dolicloud);
					jQuery("#hostname_db").val(jQuery("#instance").val()+dolicloud);
				});
				jQuery("#status").change(function() {
					initstatus();
				});

				initstatus();
			});
			';
			print '</script>'."\n";
		}

		print '<form method="post" name="formsoc" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';


		print "Si l'organisation est connue, essayer avec https://apollon1.nltechno.com/dolibarr/custom/nltechno/dolicloud/dolicloud_card.php?action=create&organization=NOM+ORGANISATION<br>";
		print "Sinon, saisir manuellement tous les champs par recopie du dashboard DoliCloud.<br>\n";


		print '<table class="border" width="100%">';

		$instancetoshow=(GETPOST("instance")?GETPOST("instance"):$object->instance);
		$organizationtoshow=(GETPOST("organization")?GETPOST("organization"):$object->organization);
		if (empty($instancetoshow)) $instancetoshow=preg_replace('/\.(.*)/','',getvalfromkey($db,'dolicloud_saasplex.app_instance.name',$organizationtoshow));

		// Instance
		print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Instance").'</td><td colspan="3"><input name="instance" id="instance" type="text" size="30" maxlength="80" value="'.$instancetoshow.'"></td></tr>';

		// Organization / Company
		print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Organization").' / '.$langs->trans("Company").'</td><td colspan="3"><input name="organization" type="text" size="30" maxlength="80" value="'.$organizationtoshow.'"></td></tr>';

		// EMail
		print '<tr><td class="fieldrequired">'.$langs->trans("Email").'</td><td colspan="3"><input name="email" type="email" size="50" maxlength="80" value="'.(GETPOST("email")?GETPOST("email"):$object->email).'"></td></tr>';

		// Plan
		print '<tr><td class="fieldrequired">'.$langs->trans("Plan").'</td><td colspan="3">';
		print $formother->select_dictionnary('plan', 'c_dolicloud_plans', 'code', 'code', (GETPOST('plan')?GETPOST('plan'):($object->plan?$object->plan:'Dolibarr ERP & CRM Basic')));
		print '</td></tr>';

		// Partner
		print '<tr><td>'.$langs->trans("Partner").'</td><td><input name="partner" type="text" size="20" maxlength="80" value="'.(isset($_POST["partner"])?$_POST["partner"]:($object->partner?$object->partner:'')).'"></td>';
		print '<td>'.$langs->trans("Source").'</td><td><input name="source" type="text" size="20" maxlength="80" value="'.(isset($_POST["source"])?$_POST["source"]:($object->source?$object->source:'')).'"></td></tr>';

		// Name
		$lastnametoshow=(GETPOST("lastname")?GETPOST("lastname"):$object->lastname);
		$firstnametoshow=(GETPOST("firstname")?GETPOST("firstname"):$object->firstname);
		print '<tr><td width="20%">'.$langs->trans("Lastname").'</td><td width="30%"><input name="lastname" type="text" size="30" maxlength="80" value="'.$lastnametoshow.'"></td>';
		print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%"><input name="firstname" type="text" size="30" maxlength="80" value="'.$firstnametoshow.'"></td></tr>';

		// Address
		$addresstoshow=(isset($_POST["address"])?$_POST["address"]:$object->address);
		if (empty($addresstoshow)) $addresstoshow=getvalfromkey($db,'dolicloud_saasplex.address.address_line1',$organizationtoshow);
		if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->address)) == 0) $object->address = $objsoc->address;	// Predefined with third party
		print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3"><textarea class="flat" name="address" cols="70">'.$addresstoshow.'</textarea></td>';

		// Zip / Town
		if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->zip)) == 0) $object->zip = $objsoc->zip;			// Predefined with third party
		if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->town)) == 0) $object->town = $objsoc->town;	// Predefined with third party
		$ziptoshow=(isset($_POST["zipcode"])?$_POST["zipcode"]:$object->zip);
		$citytoshow=(isset($_POST["town"])?$_POST["town"]:$object->town);
		if (empty($citytoshow)) $citytoshow=getvalfromkey($db,'dolicloud_saasplex.address.city',$organizationtoshow);
		if (empty($ziptoshow)) $ziptoshow=getvalfromkey($db,'dolicloud_saasplex.address.zip',$organizationtoshow);
		print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
		print $formcompany->select_ziptown($ziptoshow,'zipcode',array('town','selectcountry_id','state_id'),6).'&nbsp;';
		print $formcompany->select_ziptown($citytoshow,'town',array('zipcode','selectcountry_id','state_id'));
		print '</td></tr>';

		// Country
		$countrytoshow=(isset($_POST["country_id"])?$_POST["country_id"]:0);
		if (empty($countrytoshow)) $countrytoshow=getCountry(getvalfromkey($db,'dolicloud_saasplex.country_region.alpha2',$organizationtoshow), 3);
		if (dol_strlen(trim($object->fk_pays)) == 0) $object->fk_pays = $objsoc->country_id;	// Predefined with third party
		print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';

		print $form->select_country($countrytoshow,'country_id');
		if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
		print '</td></tr>';

		// State
		if (empty($conf->global->SOCIETE_DISABLE_STATE))
		{
			print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">';
			if ($object->country_id)
			{
				print $formcompany->select_state(isset($_POST["state_id"])?$_POST["state_id"]:$object->state_id,$object->country_code,'state_id');
			}
			else
			{
				print $countrynotdefined;
			}
			print '</td></tr>';
		}

		// VAT
		print '<tr><td>'.$langs->trans("VATIntra").'</td><td colspan="3"><input name="vat_number" type="text" size="18" maxlength="32" value="'.(isset($_POST["vat_number"])?$_POST["vat_number"]:$object->vat_number).'"></td>';
		print '</tr>';

		// Phone
		$phonetoshow=(isset($_POST["phone"])?$_POST["phone"]:$object->phone);
		if (empty($phonetoshow)) $phonetoshow=getvalfromkey($db,'dolicloud_saasplex.customer_account.tel',$organizationtoshow);
		print '<tr><td>'.$langs->trans("PhonePro").'</td><td colspan="3"><input name="phone" type="text" size="18" maxlength="80" value="'.$phonetoshow.'"></td>';
		print '</tr>';

		// Note
		print '<tr><td valign="top">'.$langs->trans("Note").'</td><td colspan="3" valign="top"><textarea class="flat" name="note" cols="70" rows="'.ROWS_3.'">'.(isset($_POST["note"])?$_POST["note"]:$object->note).'</textarea></td></tr>';

		print "</table><br>";

		print '<br>';

		print '<table class="border" width="100%">';

		// Status
		print '<tr><td class="fieldrequired">'.$langs->trans("Status").'</td><td colspan="3">';
		print $form->selectarray('status', $arraystatus, GETPOST('status')?GETPOST('status'):'ACTIVE');
		print '</td>';
		print '</tr>';

		// Date end of trial
		if (empty($date_registration)) $date_registration=getvalfromkey($db,'dolicloud_saasplex.customer_account.acquired_date',$organizationtoshow);
		if (empty($date_endfreeperiod)) $date_endfreeperiod=getvalfromkey($db,'dolicloud_saasplex.customer_account.tel',$organizationtoshow);
		print '<tr id="hideendfreetrial">';
		print '<td>'.$langs->trans("DateRegistration").'</td><td colspan="3">';
		print $form->select_date($date_registration?$date_registration:-1, 'date_registration', 0, 0, 1, '', 1, 1);
		print '</td>';
		print '<tr>';

		$hostname_web_toshow=(isset($_POST["hostname_web"])?$_POST["hostname_web"]:$object->hostname_web);
		$username_web_toshow=(isset($_POST["username_web"])?$_POST["username_web"]:$object->username_web);
		$password_web_toshow=(isset($_POST["password_web"])?$_POST["password_web"]:$object->password_web);
		$hostname_db_toshow=(isset($_POST["hostname_db"])?$_POST["hostname_db"]:$object->hostname_db);
		$database_db_toshow=(isset($_POST["database_db"])?$_POST["database_db"]:$object->database_db);
		$username_db_toshow=(isset($_POST["username_db"])?$_POST["username_db"]:$object->username_db);
		$password_db_toshow=(isset($_POST["password_db"])?$_POST["password_db"]:$object->password_db);
		if (empty($hostname_web_toshow)) $hostname_web_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.name',$organizationtoshow);
		if (empty($username_web_toshow)) $username_web_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.os_username',$organizationtoshow);
		if (empty($password_web_toshow)) $password_web_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.os_password',$organizationtoshow);
		if (empty($hostname_db_toshow))  $hostname_db_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.name',$organizationtoshow);
		if (empty($database_db_toshow))  $database_db_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.db_name',$organizationtoshow);
		if (empty($username_db_toshow))  $username_db_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.db_username',$organizationtoshow);
		if (empty($password_db_toshow))  $password_db_toshow=getvalfromkey($db,'dolicloud_saasplex.app_instance.db_password',$organizationtoshow);

		// SFTP
		print '<tr><td width="20%">'.$langs->trans("SFTP Server").'</td><td colspan="3"><input name="hostname_web" id="hostname_web" type="text" size="18" maxlength="80" value="'.$hostname_web_toshow.'"></td>';
		print '</tr>';
		// Login/Pass
		print '<tr>';
		print '<td>'.$langs->trans("SFTPLogin").'</td><td><input name="username_web" type="text" size="18" maxlength="80" value="'.$username_web_toshow.'"></td>';
		print '<td>'.$langs->trans("Password").'</td><td><input name="password_web" type="text" size="18" maxlength="80" value="'.$password_web_toshow.'"></td>';
		print '</tr>';

		// Database
		print '<tr><td>'.$langs->trans("DatabaseServer").'</td><td><input name="hostname_db" id="hostname_db" type="text" size="18" maxlength="80" value="'.$hostname_db_toshow.'"></td>';
		print '<td>'.$langs->trans("DatabaseName").'</td><td><input name="database_db" type="text" size="18" maxlength="80" value="'.$database_db_toshow.'"></td>';
		print '</tr>';
		// Login/Pass
		print '<tr>';
		print '<td>'.$langs->trans("DatabaseLogin").'</td><td><input name="username_db" type="text" size="18" maxlength="80" value="'.$username_db_toshow.'"></td>';
		print '<td>'.$langs->trans("Password").'</td><td><input name="password_db" type="text" size="18" maxlength="80" value="'.$password_db_toshow.'"></td>';
		print '</tr>';

		print "</table>";

		print "<br><br>";

		print '<center>';
		print '<input type="submit" class="button" name="add" value="'.$langs->trans("Add").'">';
		if (! empty($backtopage))
		{
			print ' &nbsp; &nbsp; ';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		}
		print '</center>';

		print "</form>";
	}
	elseif ($action == 'edit' && ! empty($id))
	{
		/*
		 * Fiche en mode edition
		 */

		// We set country_id, and country_code label of the chosen country
		if (isset($_POST["country_id"]) || $object->country_id)
		{
			$tmparray=getCountry($object->country_id,'all');
			$object->country_code =	$tmparray['code'];
			$object->country      =	$tmparray['label'];
		}

		// Affiche les erreurs
		dol_htmloutput_errors($error,$errors);

		if ($conf->use_javascript_ajax)
		{
			print "\n".'<script type="text/javascript" language="javascript">';
			print 'jQuery(document).ready(function () {
			jQuery("#instance").keyup(function() {
			var dolicloud=".on.dolicloud.com";
			jQuery("#hostname_web").val(jQuery("#instance").val()+dolicloud);
			jQuery("#hostname_db").val(jQuery("#instance").val()+dolicloud);
		});
		})';
			print '</script>'."\n";
		}

		if ($conf->use_javascript_ajax)
		{
			print '<script type="text/javascript" language="javascript">';
			print 'jQuery(document).ready(function () {
			jQuery("#selectcountry_id").change(function() {
			document.formsoc.action.value="edit";
			document.formsoc.submit();
		});
		})';
			print '</script>';
		}

		print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" name="formsoc">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print '<input type="hidden" name="action" value="update">';
		print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		print '<input type="hidden" name="contactid" value="'.$object->id.'">';
		print '<input type="hidden" name="old_name" value="'.$object->name.'">';
		print '<input type="hidden" name="old_firstname" value="'.$object->firstname.'">';
		print '<table class="border" width="100%">';

		// Instance
		print '<tr><td class="fieldrequired">'.$langs->trans("Instance").'</td><td colspan="3">';
		print '<input name="instance" type="text" size="20" maxlength="80" value="'.(isset($_POST["instance"])?$_POST["instance"]:$object->instance).'">';
		print '</td></tr>';

		print '<tr><td class="fieldrequired">'.$langs->trans("Organization").'</td><td colspan="3">';
		print '<input name="organization" type="text" size="40" maxlength="80" value="'.(isset($_POST["organization"])?$_POST["organization"]:$object->organization).'">';
		print '</td></tr>';

		// EMail
		print '<tr><td class="fieldrequired">'.$langs->trans("EMail").'</td><td colspan="3"><input name="email" type="text" size="40" maxlength="80" value="'.(isset($_POST["email"])?$_POST["email"]:$object->email).'"></td>';
		print '</tr>';

		// Plan
		print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Plan").'</td><td width="30%" colspan="3"><input name="plan" type="text" size="20" maxlength="80" value="'.(isset($_POST["plan"])?$_POST["plan"]:$object->plan).'"></td>';
		print '</tr>';

		// Partner
		print '<tr><td>'.$langs->trans("Partner").'</td><td><input name="partner" type="text" size="20" maxlength="80" value="'.(isset($_POST["partner"])?$_POST["partner"]:($object->partner?$object->partner:'')).'"></td>';
		print '<td>'.$langs->trans("Source").'</td><td><input name="source" type="text" size="20" maxlength="80" value="'.(isset($_POST["source"])?$_POST["source"]:($object->source?$object->source:'')).'"></td>';

		// Name
		print '<tr><td width="20%">'.$langs->trans("Lastname").'</td><td width="30%"><input name="lastname" type="text" size="20" maxlength="80" value="'.(isset($_POST["lastname"])?$_POST["lastname"]:$object->lastname).'"></td>';
		print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%"><input name="firstname" type="text" size="20" maxlength="80" value="'.(isset($_POST["firstname"])?$_POST["firstname"]:$object->firstname).'"></td></tr>';

		// Address
		print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3"><textarea class="flat" name="address" cols="70">'.(isset($_POST["address"])?$_POST["address"]:$object->address).'</textarea></td>';

		// Zip / Town
		print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
		print $formcompany->select_ziptown((isset($_POST["zipcode"])?$_POST["zipcode"]:$object->zip),'zipcode',array('town','selectcountry_id','state_id'),6).'&nbsp;';
		print $formcompany->select_ziptown((isset($_POST["town"])?$_POST["town"]:$object->town),'town',array('zipcode','selectcountry_id','state_id'));
		print '</td></tr>';

		// Country
		print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';
		print $form->select_country(isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id,'country_id');
		if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
		print '</td></tr>';

		// State
		if (empty($conf->global->SOCIETE_DISABLE_STATE))
		{
			print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">';
			print $formcompany->select_state($object->state_id,(isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id),'state_id');
			print '</td></tr>';
		}

		// VAT Number
		print '<tr><td>'.$langs->trans("VATIntra").'</td><td colspan="3"><input name="vat_number" type="text" size="18" maxlength="32" value="'.(isset($_POST["vat_number"])?$_POST["vat_number"]:$object->vat_number).'"></td>';
		print '</tr>';

		// Phone
		print '<tr><td>'.$langs->trans("PhonePro").'</td><td colspan="3"><input name="phone" type="text" size="18" maxlength="80" value="'.(isset($_POST["phone"])?$_POST["phone"]:$object->phone).'"></td>';
		print '</tr>';

		print '<tr><td valign="top">'.$langs->trans("Note").'</td><td colspan="3">';
		print '<textarea name="note" cols="70" rows="'.ROWS_3.'">';
		print isset($_POST["note"])?$_POST["note"]:$object->note;
		print '</textarea></td></tr>';

		print '</table>';

		print '<br>';

		print '<table class="border" width="100%">';

		// Status
		print '<tr><td class="fieldrequired">'.$langs->trans("Status").'</td><td colspan="3">';
		print $form->selectarray('status', $arraystatus, GETPOST('status')?GETPOST('status'):($object->status?$object->status:'ACTIVE'));
		print '</td>';
		print '</tr>';

		// Date end of trial
		print '<tr id="hideendfreetrial">';
		print '<td>'.$langs->trans("DateDeployment").'</td><td colspan="3">';
		print $form->select_date($object->date_registration, 'date_registration', 0, 0, 1, '', 1, 0);
		print '</td>';
		print '<tr>';

		// SFTP
		print '<tr><td width="20%">'.$langs->trans("SFTP Server").'</td><td colspan="3"><input name="hostname_web" type="text" size="28" maxlength="80" value="'.(isset($_POST["hostname_web"])?$_POST["hostname_web"]:$object->hostname_web).'"></td>';
		print '</tr>';
		// Login/Pass
		print '<tr>';
		print '<td>'.$langs->trans("SFTPLogin").'</td><td><input name="username_web" type="text" size="18" maxlength="80" value="'.(isset($_POST["username_web"])?$_POST["username_web"]:$object->username_web).'"></td>';
		print '<td>'.$langs->trans("Password").'</td><td><input name="password_web" type="text" size="18" maxlength="80" value="'.(isset($_POST["password_web"])?$_POST["password_web"]:$object->password_web).'"></td>';
		print '</tr>';

		// Database
		print '<tr><td>'.$langs->trans("DatabaseServer").'</td><td><input name="hostname_db" type="text" size="28" maxlength="80" value="'.(isset($_POST["hostname_db"])?$_POST["hostname_db"]:$object->hostname_db).'"></td>';
		print '<td>'.$langs->trans("DatabaseName").'</td><td><input name="database_db" type="text" size="28" maxlength="80" value="'.(isset($_POST["database_db"])?$_POST["database_db"]:$object->database_db).'"></td>';
		print '</tr>';
		// Login/Pass
		print '<tr>';
		print '<td>'.$langs->trans("DatabaseLogin").'</td><td><input name="username_db" type="text" size="18" maxlength="80" value="'.(isset($_POST["username_db"])?$_POST["username_db"]:$object->username_db).'"></td>';
		print '<td>'.$langs->trans("Password").'</td><td><input name="password_db" type="text" size="18" maxlength="80" value="'.(isset($_POST["password_db"])?$_POST["password_db"]:$object->password_db).'"></td>';
		print '</tr>';

		print "</table>";

		print '<br>';

		print '<center>';
		print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
		print ' &nbsp; ';
		print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		print '</center>';

		print "</form>";
	}
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
	print '<tr><td valign="top">'.$langs->trans("Note").'</td><td colspan="3">';
	print nl2br($object->note);
	print '</td></tr>';

	print "</table>";

	print '<br>';

	print '<table class="border" width="100%">';

	// SFTP
	print '<tr><td width="20%">'.$langs->trans("SFTP Server").'</td><td>'.$object->hostname_web.'</td>';
	print '<td>'.$langs->trans("FsPath").'</td><td>/home/jail/home/'.$object->username_web.'/'.(preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db)).'</td>';
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
	print '<br>';


	// Define links
    $links='';

	// Dolibarr instance login
	$url='https://'.$object->instance.'.on.dolicloud.com?username='.$lastloginadmin.'&amp;password='.$lastpassadmin;
	$link='<a href="'.$url.'" target="_blank">'.$url.'</a>';
	$links.='Dolibarr link: ';
	//print '<input type="text" name="dashboardconnectstring" value="'.dashboardconnectstring.'" size="100"><br>';
	$links.=$link.'<br>';
	//$links.='<br>';

	// Dashboard
	$url='https://www.on.dolicloud.com/signIn/index?email='.$object->email.'&amp;password='.$object->password_web;	// Note that password may have change and not being the one of dolibarr admin user
	$link='<a href="'.$url.'" target="_blank">'.$url.'</a>';
	$links.='Dashboard: ';
	$links.=$link.'<br>';
	$links.='<br>';

	// SFTP
	$sftpconnectstring=$object->username_web.':'.$object->password_web.'@'.$object->hostname_web.':'.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
	$links.='SFTP connect string: ';
	$links.='<input type="text" name="sftpconnectstring" value="'.$sftpconnectstring.'" size="110"><br>';
	//$links.='<br>';

	// MySQL
	$mysqlconnectstring='mysql -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db;
	$links.='Mysql connect string: ';
	$links.='<input type="text" name="mysqlconnectstring" value="'.$mysqlconnectstring.'" size="110"><br>';

	// JDBC
	$jdbcconnectstring='jdbc:mysql://176.34.178.16/';
	//$jdbcconnectstring.=$object->database_db;
	$links.='JDBC connect string: ';
	$links.='<input type="text" name="jdbcconnectstring" value="'.$jdbcconnectstring.'" size="110"><br>';


	// ----- DoliCloud instance -----
	print '<strong>INSTANCE SERVEUR STRATUS5</strong>';
	// Last refresh
	print ' - '.$langs->trans("DateLastCheck").': '.($object->date_lastcheck?dol_print_date($object->date_lastcheck,'dayhour','tzuser'):$langs->trans("Never"));

	if (! $object->user_id && $user->rights->nltechno->dolicloud->write)
	{
		print ' <a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
	}
	print '<br>';

	print '<table class="border" width="100%">';

	// Nb of users
	print '<tr><td width="20%">'.$langs->trans("NbOfUsers").'</td><td><font size="+2">'.round($object->nbofusers).'</font></td>';
	print '<td rowspan="6" valign="middle">';
	print $links;
	print '</td>';
	print '</tr>';

	// Dates
	print '<tr><td width="20%">'.$langs->trans("DateDeployment").'</td><td width="30%">'.dol_print_date($object->date_registration,'dayhour');
	//print ' (<a href="'.dol_buildpath('/nltechno/dolicloud/dolicloud_card.php',1).'?id='.$object->id.'&amp;action=setdate&amp;date=">'.$langs->trans("SetDate").'</a>)';
	print '</td></tr>';

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
	print '</tr>';

	// Modules
	print '<tr>';
	print '<td>'.$langs->trans("Modules").'</td><td>'.join(', ',explode(',',$object->modulesenabled)).'</td>';
	print '</tr>';

	// Authorized key file
	print '<tr>';
	print '<td>'.$langs->trans("Authorized_keyInstalled").'</td><td>'.($object->fileauthorizedkey?$langs->trans("Yes").' - '.dol_print_date($object->fileauthorizedkey,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
	print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addauthorizedkey">'.$langs->trans("Create").'</a>)';
	print '</td>';
	print '</tr>';

	// Install.lock file
	print '<tr>';
	print '<td>'.$langs->trans("LockfileInstalled").'</td><td>'.($object->filelock?$langs->trans("Yes").' - '.dol_print_date($object->filelock,'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
	print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addinstalllock">'.$langs->trans("Create").'</a>)';
	print ($object->filelock?' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=delinstalllock">'.$langs->trans("Delete").'</a>)':'');
	print '</td>';
	print '</tr>';

	print "</table><br>";


	// ----- NLTechno instance -----
	$DNS_ROOT=(empty($conf->global->NLTECHNO_DNS_ROOT)?'/etc/bind':$conf->global->NLTECHNO_DNS_ROOT);
	$APACHE_ROOT=(empty($conf->global->NLTECHNO_APACHE_ROOT)?'/etc/apache2':$conf->global->NLTECHNO_APACHE_ROOT);

	print '<strong>INSTANCE SERVEUR NLTECHNO</strong>';
	/*
	print ' - '.$langs->trans("DateLastCheck").': '.($object->lastcheck?dol_print_date($object->lastcheck,'dayhour','tzuser'):$langs->trans("Never"));

	if (! $object->user_id && $user->rights->nltechno->dolicloud->write)
	{
		print ' <a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refresh">'.img_picto($langs->trans("Refresh"),'refresh').'</a>';
	}
	*/
	print '<br>';

	print '<table class="border" width="100%">';

	/*
	// Nb of users
	print '<tr><td width="20%">'.$langs->trans("NbOfUsers").'</td><td colspan="3"><font size="+2">'.$object->nbofusers.'</font></td>';
	print '</tr>';

	// Dates
	print '<tr><td width="20%">'.$langs->trans("DateDeployment").'</td><td colspan="3">'.dol_print_date($object->date_registration,'dayhour');
	//print ' (<a href="'.dol_buildpath('/nltechno/dolicloud/dolicloud_card.php',1).'?id='.$object->id.'&amp;action=setdate&amp;date=">'.$langs->trans("SetDate").'</a>)';
	print '</td>';
	print '</tr>';

	// Lastlogin
	print '<tr>';
	print '<td>'.$langs->trans("LastLogin").' / '.$langs->trans("Password").'</td><td>'.$object->lastlogin.' / '.$object->lastpass.'</td>';
	print '<td>'.$langs->trans("DateLastLogin").'</td><td>'.($object->date_lastlogin?dol_print_date($object->date_lastlogin,'dayhour','tzuser'):'').'</td>';
	print '</tr>';

	// Version
	print '<tr>';
	print '<td>'.$langs->trans("Version").'</td><td colspan="3">'.$object->version.'</td>';
	print '</tr>';

	// Modules
	print '<tr>';
	print '<td>'.$langs->trans("Modules").'</td><td colspan="3">'.join(', ',explode(',',$object->modulesenabled)).'</td>';
	print '</tr>';
	*/

	/*
	$TTL 3d
	$ORIGIN on.dolicloud.com.
	@               IN     SOA   ns1.on.dolicloud.com. root.on.dolicloud.com. (
		130412009         ; serial number
		600              ; refresh =  2 hours
		300              ; update retry = 15 minutes
		604800           ; expiry = 3 weeks + 12 hours
		600              ; minimum = 2 hours + 20 minutes
		)
		NS              ns1.on.dolicloud.com.
		NS              ns1.eazybusiness.com.
		IN      TXT     "v=spf1 mx ~all".

		@               IN      A       176.34.178.16
		ns1             IN      A       176.34.178.16

		www             IN      CNAME   @
		rm              IN      CNAME   @

		$ORIGIN staging.on.dolicloud.com.

		@               IN      NS      ns1.staging.on.dolicloud.com.
		ns1   5         IN      A       85.25.151.49 ;'glue' record

		$ORIGIN on.dolicloud.com.

		; other sub-domain records

		mahema   A   176.34.178.16
		testldr9   A   176.34.178.16
		testldr1   A   176.34.178.16
		testldr2   A   176.34.178.16
	*/
	// DNS Entry
	if (! file_exists($DNS_ROOT.'/mysimplerp.com/mysimpleerp.com.hosts')) print 'Error link to sites-available not found<br>';
	else $dnsfileavailable=stat($DNS_ROOT.'/mysimplerp.com/mysimpleerp.com.hosts');

	print '<tr>';
	print '<td width="20%">'.$langs->trans("DNSFileFile").' ('.$DNS_ROOT.')</td><td colspan="3">'.($dnsfileavailable['size']?$langs->trans("Yes").' - '.dol_print_date($dnsfileavailable['mtime'],'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
	print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=adddnsfile">'.$langs->trans("Create").'</a>)';
	print '</td>';
	print '</tr>';

	// Instance Apache (fichier vhost)
	if (! file_exists($APACHE_ROOT.'/sites-available')) print 'Error link to sites-available not found<br>';
	else $vhostfileavailable=stat($APACHE_ROOT.'/sites-available/vhost_instance');
	if (! file_exists($APACHE_ROOT.'/sites-enabled')) print 'Error link to sites-enabled not found<br>';
	else $vhostfileenabled=stat($APACHE_ROOT.'/sites-enabled/vhost_instance');

	print '<tr>';
	print '<td width="20%">'.$langs->trans("VHostFile").' ('.$APACHE_ROOT.')</td><td colspan="3">'.($vhostfileavailable['size']?$langs->trans("Yes").' - '.dol_print_date($vhostfileavailable['mtime'],'%Y-%m-%d %H:%M:%S','tzuser'):$langs->trans("No"));
	print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addvhostfile">'.$langs->trans("Create").'</a>)';
	if ($object->status == 'ACTIVE' && ! $vhostfileenabled['ctime']) print ' &nbsp; (<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=enablevhostfile">'.$langs->trans("Enable").'</a>)';
	print '</td>';
	print '</tr>';

	print "</table>";
	print '<br>';


	$backupdir=$conf->global->DOLICLOUD_BACKUP_PATH;

	$dirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$object->database_db);
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
	if (! $user->societe_id)
	{
		print '<div class="tabsAction">';

		if ($user->rights->nltechno->dolicloud->write)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Modify').'</a>';
		}

		if ($user->rights->nltechno->dolicloud->write)
		{
			print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a>';
		}

		print "</div><br>";
	}


    print '<table width="100%"><tr><td width="50%" valign="top">';


    print '</td><td valign="top" width="50%">';

	// List of actions on element
	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
	$formactions=new FormActions($db);
	$somethingshown=$formactions->showactions($object,'dolicloudcustomers',0,1);

	print '</td></tr></table>';
}

if ($id > 0 || $instance || $action == 'create')
{
	dol_fiche_end();
}


llxFooter();

$db->close();
?>
