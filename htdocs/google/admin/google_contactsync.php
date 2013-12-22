<?php
/* Copyright (C) 2008-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *
 * Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/
 * Tutorial: http://www.ibm.com/developerworks/library/x-phpgooglecontact/index.html
 * Tutorial: https://developers.google.com/google-apps/contacts/v3/
 */

/**
 *	    \file       htdocs/google/admin/google_contactsync.php
 *      \ingroup    google
 *      \brief      Setup page for google module (Calendar)
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
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
require_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
require_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
require_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');
dol_include_once("/google/lib/google.lib.php");
dol_include_once('/google/lib/google_contact.lib.php');

if (!$user->admin) accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$action=GETPOST("action");


/*
 * Actions
 */

if ($action == 'save')
{
	$error=0;

	if (GETPOST("GOOGLE_TAG_PREFIX") == GETPOST("GOOGLE_TAG_PREFIX_CONTACTS")
		|| GETPOST("GOOGLE_TAG_PREFIX") == GETPOST("GOOGLE_TAG_PREFIX_MEMBERS")
		|| GETPOST("GOOGLE_TAG_PREFIX_CONTACTS") == GETPOST("GOOGLE_TAG_PREFIX_MEMBERS"))
	{
		setEventMessage($langs->trans("ErrorLabelsMustDiffers"),'errors');
		$error++;
	}
	if (! GETPOST('GOOGLE_CONTACT_LOGIN') || ! GETPOST('GOOGLE_CONTACT_PASSWORD'))
	{
		$langs->load("errors");
		setEventMessage($langs->trans("ErrorFieldRequired"),'errors');
		$error++;
	}

    if (! $error)
    {
    	$db->begin();

    	$res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_THIRDPARTIES',trim(GETPOST("GOOGLE_DUPLICATE_INTO_THIRDPARTIES")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_CONTACTS',trim(GETPOST("GOOGLE_DUPLICATE_INTO_CONTACTS")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_MEMBERS',trim(GETPOST("GOOGLE_DUPLICATE_INTO_MEMBERS")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_LOGIN',trim(GETPOST("GOOGLE_CONTACT_LOGIN")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_PASSWORD',trim(GETPOST("GOOGLE_CONTACT_PASSWORD")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_LABEL',trim(GETPOST("GOOGLE_CONTACT_LABEL")),'chaine',0);
	    if (! $res > 0) $error++;
		$res=dolibarr_set_const($db,'GOOGLE_TAG_PREFIX',trim(GETPOST("GOOGLE_TAG_PREFIX")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_TAG_PREFIX_CONTACTS',trim(GETPOST("GOOGLE_TAG_PREFIX_CONTACTS")),'chaine',0);
	    if (! $res > 0) $error++;
	    $res=dolibarr_set_const($db,'GOOGLE_TAG_PREFIX_MEMBERS',trim(GETPOST("GOOGLE_TAG_PREFIX_MEMBERS")),'chaine',0);
	    if (! $res > 0) $error++;

	    if (! $error)
	    {
	        $db->commit();
	        $mesg = '<font class="ok">'.$langs->trans("SetupSaved")."</font>";
	    }
	    else
	    {
	        $db->rollback();
	        $mesg = '<font class="error">'.$langs->trans("Error")."</font>";
	    }
    }
}

// This is a test action to allow to test creation of contact once synchro with Contact has been enabled.
if (preg_match('/^test/',$action))
{
    if ($action == 'testcreatethirdparties' || $action == 'testallthirdparties') $object=new Societe($db);
    if ($action == 'testcreatecontacts' || $action == 'testallcontacts') $object=new Contact($db);
    if ($action == 'testcreatemembers' || $action == 'testallmembers') $object=new Adherent($db);

    if ($action == 'testcreatethirdparties' || $action == 'testallthirdparties')
    {
    	$result=$object->initAsSpecimen();

    	$object->name='Test Synchro Thirdparty (can be deleted)';
	    $object->lastname='Thirdparty (can be deleted)';
	    $object->firstname='Test Synchro';
	    $object->note_private='New private note with special char é and entity eacute &eacute; and html tag <strong>strong</strong>';
	    /*$object->code_client=-1;
	    $object->code_fournisseur=-1;*/
	    $result=$object->create($user);
    }
    if ($action == 'testcreatecontacts' || $action == 'testallcontacts')
    {
    	$result=$object->initAsSpecimen();

    	$object->name='Test Synchro Contact (can be deleted)';
    	$object->lastname='Contact (can be deleted)';
    	$object->firstname='Test Synchro';
	    $object->note_private='New private note with special char é and entity eacute &eacute; and html tag <strong>strong</strong>';
    	/*$object->code_client=-1;
    	 $object->code_fournisseur=-1;*/
    	$result=$object->create($user);
    }
    if ($action == 'testcreatemembers' || $action == 'testallmembers')
    {
    	$result=$object->initAsSpecimen();

    	$object->name='Test Synchro Member (can be deleted)';
    	$object->lastname='Member (can be deleted)';
    	$object->firstname='Test Synchro';
	    $object->note_private='New private note with special char é and entity eacute &eacute; and html tag <strong>strong</strong>';
    	/*$object->code_client=-1;
    	 $object->code_fournisseur=-1;*/
    	$result=$object->create($user);
    }

    if ($action == 'testallthirdparties')
    {
    	$object->oldcopy = dol_clone($object);

    	$object->name='Test Synchro new Thirdparty (can be deleted)';
	    $object->lastname='Thirdparty (can be deleted)';
	    $object->firstname='Test Synchro new';
	    $object->email='newemail@newemail.com';
	    $object->url='www.newspecimen.com';
	    $object->note_private='New private note with special char é and entity eacute &eacute; and html tag <strong>strong</strong>';
	    $object->street='New street';
	    $object->town='New town';
	    $result=$object->update($object->id, $user);

	    $result=$object->delete($object->id);	// id of thirdparty to delete
    }
    if ($action == 'testallcontacts')
    {
    	$object->oldcopy = dol_clone($object);

    	$object->name='Test Synchro new Contact (can be deleted)';
    	$object->lastname='Contact (can be deleted)';
    	$object->firstname='Test Synchro new';
    	$object->email='newemail@newemail.com';
	    $object->url='www.newspecimen.com';
    	$object->note_private='New private note with special char é and entity eacute &eacute; and html tag <strong>strong</strong>';
	    $object->street='New street';
	    $object->town='New town';
    	$result=$object->update($object->id, $user);

	    $result=$object->delete(0);	// notrigger=0
    }
    if ($action == 'testallmembers')
    {
    	$object->oldcopy = dol_clone($object);

    	$object->name='Test Synchro new Member (can be deleted)';
    	$object->lastname='Member (can be deleted)';
    	$object->firstname='Test Synchro new';
    	$object->email='newemail@newemail.com';
    	$object->url='www.newspecimen.com';
    	$object->note_private='New private note with special char é and entity eacute &eacute; and html tag <strong>strong</strong>';
    	$object->street='New street';
    	$object->town='New town';
    	$result=$object->update($user);

    	$result=$object->delete(0);	// notrigger=0
    }

    if ($result > 0)
    {
        $mesg=$langs->trans("TestSuccessfull");
    }
    else
    {
        $error='<div class="error">'.$object->error.'</div>';
        $errors=$object->errors;
    }
}

if ($action == 'pushallthirdparties')
{
	$objectstatic=new Societe($db);

	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Create client object
	$service= 'cp';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
	$client = getClientLoginHttpClientContact($googleuser, $googlepwd, $service);
	//var_dump($client); exit;

	if ($client == null)
	{
		dol_syslog("Failed to login to Google for login ".$googleuser, LOG_ERR);
		$error='Failed to login to Google for login '.$googleuser;
	}
	else
	{
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		dol_include_once('/google/class/gcontacts.class.php');

		//	$res = GContact::deleteDolibarrContacts();
		$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'societe';
		$resql = $db->query($sql);
		if (! $resql)
		{
			dol_print_error($db);
			exit;
		}

		$synclimit = 0;	// 0 = all
		$i=0;
		while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
		{
			$gContacts[] = new GContact($obj->rowid,'thirdparty',$gdata);
			$i++;
		}

		$result=0;
		if (count($gContacts)) $result=insertGContactsEntries($gdata, $gContacts, $objectstatic);

		if (is_numeric($result) && $result >= 0)
		{
			$mesg = $langs->trans("PushToGoogleSucess",count($gContacts));
		}
		else
		{
			$error++;
			$errors[] = $langs->trans("Error").' '.$result;
		}
	}
}

if ($action == 'pushallcontacts')
{
	$objectstatic=new Contact($db);

	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Create client object
	$service= 'cp';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
	$client = getClientLoginHttpClientContact($googleuser, $googlepwd, $service);
	//var_dump($client); exit;

	if ($client == null)
	{
		dol_syslog("Failed to login to Google for login ".$googleuser, LOG_ERR);
		$error='Failed to login to Google for login '.$googleuser;
	}
	else
	{
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		dol_include_once('/google/class/gcontacts.class.php');

		//	$res = GContact::deleteDolibarrContacts();
		$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'socpeople';
		$resql = $db->query($sql);
		if (! $resql)
		{
			dol_print_error($db);
			exit;
		}

		$synclimit = 0;	// 0 = all
		$i=0;
		while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
		{
			$gContacts[] = new GContact($obj->rowid,'contact',$gdata);
			$i++;
		}

		$result=0;
		if (count($gContacts)) $result=insertGContactsEntries($gdata, $gContacts, $objectstatic);

		if (is_numeric($result) && $result >= 0)
		{
			$mesg = $langs->trans("PushToGoogleSucess",count($gContacts));
		}
		else
		{
			$error++;
			$errors[] = $langs->trans("Error").' '.$result;
		}
	}
}

if ($action == 'pushallmembers')
{
	$objectstatic=new Adherent($db);

	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Create client object
	$service= 'cp';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
	$client = getClientLoginHttpClientContact($googleuser, $googlepwd, $service);
	//var_dump($client); exit;

	if ($client == null)
	{
		dol_syslog("Failed to login to Google for login ".$googleuser, LOG_ERR);
		$error='Failed to login to Google for login '.$googleuser;
	}
	else
	{
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		dol_include_once('/google/class/gcontacts.class.php');

		//	$res = GContact::deleteDolibarrContacts();
		$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'adherent';
		$resql = $db->query($sql);
		if (! $resql)
		{
			dol_print_error($db);
			exit;
		}

		$synclimit = 0;	// 0 = all
		$i=0;
		while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
		{
			$gContacts[] = new GContact($obj->rowid,'member',$gdata);
			$i++;
		}

		$result=0;
		if (count($gContacts)) $result=insertGContactsEntries($gdata, $gContacts, $objectstatic);

		if (is_numeric($result) && $result >= 0)
		{
			$mesg = $langs->trans("PushToGoogleSucess",count($gContacts));
		}
		else
		{
			$error++;
			$errors[] = $langs->trans("Error").' '.$result;
		}
	}
}

if ($action == 'deleteallthirdparties')
{
	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Create client object
	$service= 'cp';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
	$client = getClientLoginHttpClientContact($googleuser, $googlepwd, $service);
	//var_dump($client); exit;

	if ($client == null)
	{
		dol_syslog("Failed to login to Google for login ".$googleuser, LOG_ERR);
		$error='Failed to login to Google for login '.$googleuser;
	}
	else
	{
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		dol_include_once('/google/class/gcontacts.class.php');

		$nbContacts = GContact::deleteDolibarrContacts($gdata,'','thirdparty');

		if ($nbContacts >= 0)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."societe SET ref_ext = NULL WHERE ref_ext LIKE '%google%'";
			dol_syslog("sql=".$sql);
			$db->query($sql);

			$mesg = $langs->trans("DeleteToGoogleSucess",$nbContacts);
		}
		else
		{
			$error++;
			$errors[] = $langs->trans("Error");
		}
	}
}

if ($action == 'deleteallcontacts')
{
	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Create client object
	$service= 'cp';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
	$client = getClientLoginHttpClientContact($googleuser, $googlepwd, $service);
	//var_dump($client); exit;

	if ($client == null)
	{
		dol_syslog("Failed to login to Google for login ".$googleuser, LOG_ERR);
		$error='Failed to login to Google for login '.$googleuser;
	}
	else
	{
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		dol_include_once('/google/class/gcontacts.class.php');

		$nbContacts = GContact::deleteDolibarrContacts($gdata,'','contact');

		if ($nbContacts >= 0)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."socpeople SET ref_ext = NULL WHERE ref_ext LIKE '%google%'";
			dol_syslog("sql=".$sql);
			$db->query($sql);

			$mesg = $langs->trans("DeleteToGoogleSucess",$nbContacts);
		}
		else
		{
			$error++;
			$errors[] = $langs->trans("Error");
		}
	}
}

if ($action == 'deleteallmembers')
{
	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Create client object
	$service= 'cp';		// cl = calendar, cp=contact, ... Search on AUTH_SERVICE_NAME into Zend API for full list
	$client = getClientLoginHttpClientContact($googleuser, $googlepwd, $service);
	//var_dump($client); exit;

	if ($client == null)
	{
		dol_syslog("Failed to login to Google for login ".$googleuser, LOG_ERR);
		$error='Failed to login to Google for login '.$googleuser;
	}
	else
	{
		$gdata = new Zend_Gdata($client);
		$gdata->setMajorProtocolVersion(3);

		dol_include_once('/google/class/gcontacts.class.php');

		$nbContacts = GContact::deleteDolibarrContacts($gdata,'','member');

		if ($nbContacts >= 0)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."adherent SET ref_ext = NULL WHERE ref_ext LIKE '%google%'";
			dol_syslog("sql=".$sql);
			$db->query($sql);

			$mesg = $langs->trans("DeleteToGoogleSucess",$nbContacts);
		}
		else
		{
			$error++;
			$errors[] = $langs->trans("Error");
		}
	}
}



/*
 * View
 */

$form=new Form($db);
$formadmin=new FormAdmin($db);
$formother=new FormOther($db);

$help_url='EN:Module_Google_EN|FR:Module_Google|ES:Modulo_Google';
//$arrayofjs=array('/includes/jquery/plugins/colorpicker/jquery.colorpicker.js');
//$arrayofcss=array('/includes/jquery/plugins/colorpicker/jquery.colorpicker.css');
$arrayofjs=array();
$arrayofcss=array();
llxHeader('',$langs->trans("GoogleSetup"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"),$linkback,'setup');
print '<br>';


$head=googleadmin_prepare_head();


dol_fiche_head($head, 'tabcontactsync', $langs->trans("GoogleTools"));

if (! function_exists("openssl_open")) print '<div class="warning">Warning: PHP Module \'openssl\' is not installed</div><br>';
if (! class_exists('DOMDocument')) print '<div class="warning">Warning: PHP Module \'xml\' is not installed</div><br>';

if ($conf->use_javascript_ajax)
{
	print "\n".'<script type="text/javascript" language="javascript">';
	print 'jQuery(document).ready(function () {
		function initfields()
		{
			if (jQuery("#GOOGLE_DUPLICATE_INTO_THIRDPARTIES").val() > 0) jQuery("#syncthirdparties").show();
			else jQuery("#syncthirdparties").hide();
			if (jQuery("#GOOGLE_DUPLICATE_INTO_CONTACTS").val() > 0) jQuery("#synccontacts").show();
			else jQuery("#synccontacts").hide();
			if (jQuery("#GOOGLE_DUPLICATE_INTO_MEMBERS").val() > 0) jQuery("#syncmembers").show();
			else jQuery("#syncmembers").hide();
		}
		initfields();
		jQuery("#GOOGLE_DUPLICATE_INTO_THIRDPARTIES").change(function() {
			initfields();
		});
		jQuery("#GOOGLE_DUPLICATE_INTO_CONTACTS").change(function() {
			initfields();
		});
		jQuery("#GOOGLE_DUPLICATE_INTO_MEMBERS").change(function() {
			initfields();
		});
	})';
	print '</script>'."\n";
}

print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="save">';

if ($conf->societe->enabled) print $langs->trans("GoogleEnableSyncToThirdparties").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_THIRDPARTIES",isset($_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"])?$_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"]:$conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES,1).'<br>';
if ($conf->societe->enabled) print $langs->trans("GoogleEnableSyncToContacts").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_CONTACTS",isset($_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"])?$_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"]:$conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS,1).'<br>';
if ($conf->adherent->enabled) print $langs->trans("GoogleEnableSyncToMembers").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_MEMBERS",isset($_POST["GOOGLE_DUPLICATE_INTO_MEMBERS"])?$_POST["GOOGLE_DUPLICATE_INTO_MEMBERS"]:$conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS,1).'<br>';
print '<br>';


$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleAPIv3Usage", "Contact").')'."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";

// Google login
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_LOGIN")."</td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_CONTACT_LOGIN" autocomplete="off" value="'.$conf->global->GOOGLE_CONTACT_LOGIN.'">';
//print ' &nbsp; '.$langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print "</td>";
print "</tr>";
// Google password
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_PASSWORD")."</td>";
print "<td>";
print '<input class="flat" type="password" size="10" name="GOOGLE_CONTACT_PASSWORD" autocomplete="off" value="'.$conf->global->GOOGLE_CONTACT_PASSWORD.'">';
//print ' &nbsp; '.$langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print "</td>";
print "</tr>";

print "</table>";
print "<br>";


$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Label to use for thirdparties
if ($conf->societe->enabled)
{
	$var=!$var;
	print '<tr '.$bc[$var].' id="syncthirdparties">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_TAG_PREFIX")."<br /></td>";
	print "<td>";
	print '<input class="flat" type="text" size="28" name="GOOGLE_TAG_PREFIX" value="'.dol_escape_htmltag(getTagLabel('thirdparties')).'">';
	print "</td>";
	print "</tr>";
}
// Label to use for contacts
$var=!$var;
if ($conf->societe->enabled)
{
	print '<tr '.$bc[$var].' id="synccontacts">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_TAG_PREFIX_CONTACTS")."<br /></td>";
	print "<td>";
	print '<input class="flat" type="text" size="28" name="GOOGLE_TAG_PREFIX_CONTACTS" value="'.dol_escape_htmltag(getTagLabel('contacts')).'">';
	print "</td>";
	print "</tr>";
}
// Label to use for members
if ($conf->adherent->enabled)
{
	print '<tr '.$bc[$var].' id="syncmembers">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_TAG_PREFIX_MEMBERS")."<br /></td>";
	print "<td>";
	print '<input class="flat" type="text" size="28" name="GOOGLE_TAG_PREFIX_MEMBERS" value="'.dol_escape_htmltag(getTagLabel('members')).'">';
	print "</td>";
	print "</tr>";
}
print "</table>";
print "<br>";


print '<center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();

print '<br>';


if ($conf->societe->enabled)
{
	print '<div class="tabsActions">';
	// Thirdparties
	if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES))
	{
		print '<div class="inline-block divButAction"><font class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")." (".$langs->trans("ThirdParty").")</font></a></div>";

		print '<div class="inline-block divButAction"><font class="butActionRefused" href="#">'.$langs->trans("TestCreate")." (".$langs->trans("ThirdParty").")</font></a></div>";
	}
	else
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testallthirdparties">'.$langs->trans("TestCreateUpdateDelete")." (".$langs->trans("ThirdParty").")</a></div>";

		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreatethirdparties">'.$langs->trans("TestCreate")." (".$langs->trans("ThirdParty").")</a></div>";
	}
	print '</div>';
}

if ($conf->societe->enabled)
{
	print '<div class="tabsActions">';
	// Contacts
	if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS))
	{
		print '<div class="inline-block divButAction"><font class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")." (".$langs->trans("Contact").")</font></a></div>";

		print '<div class="inline-block divButAction"><font class="butActionRefused" href="#">'.$langs->trans("TestCreate")." (".$langs->trans("Contact").")</font></a></div>";
	}
	else
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testallcontacts">'.$langs->trans("TestCreateUpdateDelete")." (".$langs->trans("Contact").")</a></div>";

		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreatecontacts">'.$langs->trans("TestCreate")." (".$langs->trans("Contact").")</a></div>";
	}
	print '</div>';
}

// Members
if ($conf->adherent->enabled)
{
	print '<div class="tabsActions">';
	if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS))
	{
		print '<div class="inline-block divButAction"><font class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")." (".$langs->trans("Member").")</font></a></div>";

		print '<div class="inline-block divButAction"><font class="butActionRefused" href="#">'.$langs->trans("TestCreate")." (".$langs->trans("Member").")</font></a></div>";
	}
	else
	{
		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testallmembers">'.$langs->trans("TestCreateUpdateDelete")." (".$langs->trans("Member").")</a></div>";

		print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreatemembers">'.$langs->trans("TestCreate")." (".$langs->trans("Member").")</a></div>";
	}
	print '</div>';
}


print '<br><br>';

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES))
{
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallthirdparties">';
	print $langs->trans("ExportThirdpartiesToGoogle")." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallthirdparties">';
	print $langs->trans("DeleteAllGoogleThirdparties")." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS))
{
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallcontacts">';
	print $langs->trans("ExportContactToGoogle")." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallcontacts">';
	print $langs->trans("DeleteAllGoogleContacts")." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS))
{
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallmembers">';
	print $langs->trans("ExportMembersToGoogle")." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallmembers">';
	print $langs->trans("DeleteAllGoogleMembers")." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";
}

dol_htmloutput_mesg($mesg);
dol_htmloutput_errors((is_numeric($error)?'':$error),$errors);



llxFooter();

if (is_object($db)) $db->close();
?>
