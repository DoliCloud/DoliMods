<?php
/* Copyright (C) 2008-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
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
if (! $res && @file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/(?:custom|nltechno)([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
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

$oauthurl='https://accounts.google.com/o/oauth2/auth';


/*
 * Actions
 */

if ($action == 'deletetoken')
{
	$res=dolibarr_del_const($db,'GOOGLE_WEB_TOKEN');
	unset($_SESSION['google_web_token']);
	if (! $res > 0) $error++;

	$action='';
}

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
	if (! GETPOST('GOOGLE_CONTACT_LOGIN'))
	{
		$langs->load("errors");
		dolibarr_del_const($db, 'GOOGLE_CONTACT_LOGIN');
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("GOOGLE_LOGIN")),'errors');
	}

	$res=dolibarr_set_const($db,'GOOGLE_API_CLIENT_ID',trim(GETPOST("GOOGLE_API_CLIENT_ID")),'chaine',0);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_API_CLIENT_SECRET',trim(GETPOST("GOOGLE_API_CLIENT_SECRET")),'chaine',0);
	if (! $res > 0) $error++;

	/*if (! GETPOST('GOOGLE_CONTACT_PASSWORD'))
	{
		$langs->load("errors");
		dolibarr_del_const($db, 'GOOGLE_CONTACT_PASSWORD');
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("GOOGLE_PASSWORD")),'errors');
	}*/

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
	$db->begin();

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

    if ($result >= 0)
    {
	    if ($action == 'testallthirdparties')
	    {
	    	$object->oldcopy = dol_clone($object);

	    	$object->name='Test Synchro new Thirdparty (can be deleted)';
		    $object->lastname='Thirdparty (can be deleted)';
		    $object->firstname='Test Synchro new';
		    $object->email='newemail@newemail.com';
		    $object->url='www.newspecimen.com';
		    $object->note_private='New private note with special char é and entity eacute xxx and html tag <strong>strong</strong>';
		    $object->street='New street';
		    $object->town='New town';
		    $result=$object->update($object->id, $user);

		    if ($result > 0) $result=$object->delete($object->id, $user);	// id of thirdparty to delete
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

	    	if ($result > 0) $result=$object->delete(0, $user);	// notrigger=0
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

	    	if ($result > 0) $result=$object->delete(0, $user);	// notrigger=0
	    }
    }

    if ($result >= 0)
    {
    	$db->rollback();	// It was a test, we rollback everything
        $mesg=$langs->trans("TestSuccessfull");
    }
    else
	{
    	$db->rollback();	// It was a test, we rollback everything

		if ($object->errors) setEventMessage($object->errors,'errors');
        else setEventMessage($object->error,'errors');
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


if (! function_exists("openssl_open")) print '<div class="warning">Warning: PHP Module \'openssl\' is not installed</div><br>';
if (! class_exists('DOMDocument')) print '<div class="warning">Warning: PHP Module \'xml\' is not installed</div><br>';


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="save">';

$head=googleadmin_prepare_head();

dol_fiche_head($head, 'tabcontactsync', $langs->trans("GoogleTools"));

if ($conf->use_javascript_ajax)
{
	print "\n".'<script type="text/javascript" language="javascript">';
	print 'jQuery(document).ready(function () {
		function initfields()
		{
			if (jQuery("#GOOGLE_DUPLICATE_INTO_THIRDPARTIES").val() > 0 || jQuery("#GOOGLE_DUPLICATE_INTO_CONTACTS").val() > 0 || jQuery("#GOOGLE_DUPLICATE_INTO_MEMBERS").val() > 0) jQuery(".syncx").show();
			else jQuery(".syncx").hide();

			if (jQuery("#GOOGLE_DUPLICATE_INTO_THIRDPARTIES").val() > 0) jQuery(".syncthirdparties,#trsyncthirdparties").show();
			else jQuery(".syncthirdparties,#trsyncthirdparties").hide();
			if (jQuery("#GOOGLE_DUPLICATE_INTO_CONTACTS").val() > 0) jQuery(".synccontacts,#trsynccontacts").show();
			else jQuery(".synccontacts,#trsynccontacts").hide();
			if (jQuery("#GOOGLE_DUPLICATE_INTO_MEMBERS").val() > 0) jQuery(".syncmembers,#trsyncmembers").show();
			else jQuery(".syncmembers,#trsyncmembers").hide();

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

if ($conf->societe->enabled) print $langs->trans("GoogleEnableSyncToThirdparties").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_THIRDPARTIES",isset($_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"])?$_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"]:$conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES,1).'<br>';
if ($conf->societe->enabled) print $langs->trans("GoogleEnableSyncToContacts").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_CONTACTS",isset($_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"])?$_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"]:$conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS,1).'<br>';
if ($conf->adherent->enabled) print $langs->trans("GoogleEnableSyncToMembers").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_MEMBERS",isset($_POST["GOOGLE_DUPLICATE_INTO_MEMBERS"])?$_POST["GOOGLE_DUPLICATE_INTO_MEMBERS"]:$conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS,1).'<br>';


print '<div class="syncx">';

print '<br>';


$var=true;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Label to use for thirdparties
if ($conf->societe->enabled)
{
	$var=!$var;
	print '<tr '.$bc[$var].' id="trsyncthirdparties">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_TAG_PREFIX")."</td>";
	print "<td>";
	print '<input class="flat" type="text" size="28" name="GOOGLE_TAG_PREFIX" value="'.dol_escape_htmltag(getTagLabel('thirdparties')).'">';
	print "</td>";
	print "</tr>";
}
// Label to use for contacts
if ($conf->societe->enabled)
{
	$var=!$var;
	print '<tr '.$bc[$var].' id="trsynccontacts">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_TAG_PREFIX_CONTACTS")."</td>";
	print "<td>";
	print '<input class="flat" type="text" size="28" name="GOOGLE_TAG_PREFIX_CONTACTS" value="'.dol_escape_htmltag(getTagLabel('contacts')).'">';
	print "</td>";
	print "</tr>";
}
// Label to use for members
if ($conf->adherent->enabled)
{
	$var=!$var;
	print '<tr '.$bc[$var].' id="trsyncmembers">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_TAG_PREFIX_MEMBERS")."</td>";
	print "<td>";
	print '<input class="flat" type="text" size="28" name="GOOGLE_TAG_PREFIX_MEMBERS" value="'.dol_escape_htmltag(getTagLabel('members')).'">';
	print "</td>";
	print "</tr>";
}
print "</table>";
print $langs->trans("GoogleContactSyncInfo").'<br>';


print "<br>";
print "<br>";



$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleAPIv3Usage", "Contact").')'."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Note")."</td>";
print "</tr>";

// Google login
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GoogleIDContact")."</td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_CONTACT_LOGIN" autocomplete="off" value="'.$conf->global->GOOGLE_CONTACT_LOGIN.'">';
print "</td>";
print '<td>';
print $langs->trans("Example").": yourlogin@gmail.com, email@mydomain.com, 'primary'<br>";
//print $langs->trans("GoogleSetupHelp").'<br>';
//print $langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print '</td>';
print "</tr>";

/*

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

print info_admin($langs->trans("EnableAPI","https://code.google.com/apis/console/","https://code.google.com/apis/console/","Contacts API"));
*/

/*
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_EMAIL")."</td>";
print '<td>';
print '<input class="flat" type="text" size="90" name="GOOGLE_API_SERVICEACCOUNT_EMAIL" value="'.$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.'">';
print '</td>';
print '<td>';
print $langs->trans("AllowGoogleToLoginWithServiceAccount","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
print '</td>';
print '</tr>';

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_P12KEY")."</td>";
print '<td>';
if (! empty($conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY)) print $conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY.'<br>';
print '<input type="file" name="GOOGLE_API_SERVICEACCOUNT_P12KEY_file">';
print '</td>';
print '<td>';
print $langs->trans("AllowGoogleToLoginWithServiceAccountP12","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
print '</td>';
print '</tr>';
*/


/*
		// Define $urlwithroot
		$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
		$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
		//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current
*/
$redirect_uri=dol_buildpath('/google/oauth2callback.php', 2);
$jsallowed=preg_replace('/(https*:\/\/[^\/]+\/).*$/','\1',$redirect_uri);

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_CLIENT_ID")."</td>";
print '<td>';
print '<input class="flat" type="text" size="90" name="GOOGLE_API_CLIENT_ID" value="'.$conf->global->GOOGLE_API_CLIENT_ID.'">';
print '</td>';
print '<td>';
//print $langs->trans("AllowGoogleToLoginWithClientID","https://code.google.com/apis/console/","https://code.google.com/apis/console/", $jsallowed, $redirect_uri).'<br>';
print $langs->trans("AllowGoogleToLoginWithClientID","https://code.google.com/apis/console/","https://code.google.com/apis/console/", $redirect_uri).'<br>';
print '</td>';
print '</tr>';

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_CLIENT_SECRET")."</td>";
print '<td>';
print '<input class="flat" type="text" size="90" name="GOOGLE_API_CLIENT_SECRET" value="'.$conf->global->GOOGLE_API_CLIENT_SECRET.'">';
print '</td>';
print '<td>';
print $langs->trans("AllowGoogleToLoginWithClientSecret").'<br>';
print '</td>';
print '</tr>';

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("GOOGLE_WEB_TOKEN")."</td>";
print '<td colspan="2">';
if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_API_CLIENT_ID))
{
	print $langs->trans("FillAndSaveGoogleAccount");
}
else
{
	$completeoauthurl=$oauthurl;
	$completeoauthurl.='?response_type=code&client_id='.urlencode($conf->global->GOOGLE_API_CLIENT_ID);
	$completeoauthurl.='&redirect_uri='.urlencode($redirect_uri);
	$completeoauthurl.='&scope='.urlencode('https://www.google.com/m8/feeds https://www.googleapis.com/auth/contacts.readonly');
	$completeoauthurl.='&state=dolibarrtokenrequest-googleadmincontactsync';		// To know we are coming from this page
	//$completeoauthurl.='&access_type=online';
	$completeoauthurl.='&approval_prompt=force';
	$completeoauthurl.='&login_hint='.urlencode($conf->global->GOOGLE_CONTACT_LOGIN);
	$completeoauthurl.='&include_granted_scopes=true';

	if (! empty($conf->global->GOOGLE_WEB_TOKEN) || ! empty($_SESSION['google_web_token']))
	{
		print 'Database token: '.$conf->global->GOOGLE_WEB_TOKEN.'<br>';
		print 'Current session token: '.$_SESSION['google_web_token'].'<br>';
		print '<br>';
		print $langs->trans("GoogleRecreateToken").'<br>';
		//print '<a href="'.$completeoauthurl.'" target="_blank">'.$langs->trans("LinkToOAuthPage").'</a>';
		print '<a href="'.$completeoauthurl.'">'.$langs->trans("LinkToOAuthPage").'</a>';
		print '<br><br>';
		print $langs->trans("GoogleDeleteToken").'<br>';
		print '<a href="'.$_SERVER["PHP_SELF"].'?action=deletetoken" target="_blank">'.$langs->trans("ClickHere").'</a>';
		print '<br><br>';
		print $langs->trans("GoogleDeleteAuthorization").'<br>';
		print '<a href="https://security.google.com/settings/security/permissions" target="_blank">https://security.google.com/settings/security/permissions</a>';
	}
	else {
		print img_warning().' '.$langs->trans("GoogleNoTokenYet").'<br>';
		//print '<a href="'.$completeoauthurl.'" target="_blank">'.$langs->trans("LinkToOAuthPage").'</a>';
		print '<a href="'.$completeoauthurl.'">'.$langs->trans("LinkToOAuthPage").'</a>';
	}
}
print '</td>';
print '</tr>';

print "</table>";

print info_admin($langs->trans("EnableAPI","https://code.google.com/apis/console/","https://code.google.com/apis/console/","Contact API"));

//print info_admin($langs->trans("ShareContactWithServiceAccount",$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL,$langs->transnoentitiesnoconv("GoogleIDContact")));

print '</div>';

dol_fiche_end();

print '<div align="center">';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</div>";

print "</form>\n";

print '<br>';


if ($conf->societe->enabled)
{
	print '<div class="tabsActions syncthirdparties">';
	// Thirdparties
	if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_WEB_TOKEN))
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
	print '<div class="tabsActions synccontacts">';
	// Contacts
	if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_WEB_TOKEN))
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
	print '<div class="tabsActions syncmembers">';
	if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_WEB_TOKEN))
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

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES) && ! empty($conf->global->GOOGLE_WEB_TOKEN))
{
	print '<div class="tabsActions syncthirdparties">';
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
	print '</div>';
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS) && ! empty($conf->global->GOOGLE_WEB_TOKEN))
{
	print '<div class="tabsActions synccontacts">';
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
	print '</div>';
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_MEMBERS) && ! empty($conf->global->GOOGLE_WEB_TOKEN))
{
	print '<div class="tabsActions syncmembers">';
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

	print '</div>';
}

dol_htmloutput_mesg($mesg);
dol_htmloutput_errors((is_numeric($error)?'':$error),$errors);


llxFooter();

if (is_object($db)) $db->close();
