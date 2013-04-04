<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
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
    $db->begin();

    //print 'color='.$color;
    $res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_THIRDPARTIES'.$i,trim($_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_CONTACTS'.$i,trim($_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_LOGIN',trim($_POST["GOOGLE_CONTACT_LOGIN"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_PASSWORD',trim($_POST["GOOGLE_CONTACT_PASSWORD"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_LABEL',trim($_POST["GOOGLE_CONTACT_LABEL"]),'chaine',0);
    if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_TAG_PREFIX',trim($_POST["GOOGLE_TAG_PREFIX"]),'chaine',0);
    if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_TAG_PREFIX_CONTACTS',trim($_POST["GOOGLE_TAG_PREFIX_CONTACTS"]),'chaine',0);
    if (! $res > 0) $error++;


    if (! $error)
    {
        $db->commit();
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $db->rollback();
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}

// This is a hidden action to allow to test creation of event once synchro with Calendar has been enabled.
if (preg_match('/^test/',$action))
{
    include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
    include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');

    //$object=new Contact($db);
    $object=new Societe($db);
    $result=$object->initAsSpecimen();

    if ($action == 'testcreate' || $action == 'testall')
    {
	    $object->name='Test Synchro Thirdparty (can be deleted)';
	    $object->lastname='Contact (can be deleted)';
	    $object->firstname='Test Synchro';
	    /*$object->code_client=-1;
	    $object->code_fournisseur=-1;*/
	    $result=$object->create($user);
    }

    if ($action == 'testall')
    {
	    $object->name='New test Synchro Thirdparty (can be deleted)';
	    $object->lastname='Synchro Contact (can be deleted)';
	    $object->firstname='New test';
	    $object->email='newemail@newemail.com';
	    $object->note='New private note';
	    $result=$object->update($object->id, $user);
    }

    if ($action == 'testdelete')
    {
    	$result=$object->delete($object->id);
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
	dol_include_once('/google/class/gcontacts.class.php');

	//	$res = GContact::deleteDolibarrContacts();
	$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'societe';
	$resql = $db->query($sql);
	if (! $resql)
	{
		dol_print_error($db);
		exit;
	}

	$synclimit = 3;	// 0 = all
	$i=0;
	while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
	{
		$gContacts[] = new GContact($obj->rowid,'thirdparty');
		$i++;
	}

	$result=0;
	if (count($gContacts)) $result=GContact::insertGContactsEntries($gContacts);

	if ($result >= 0) $mesg = $langs->trans("PushToGoogleSucess",count($gContacts));
	else $mesg = $langs->trans("Error");
}

if ($action == 'pushallcontacts')
{
	dol_include_once('/google/class/gcontacts.class.php');

	//	$res = GContact::deleteDolibarrContacts();
	$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'socpeople';
	$resql = $db->query($sql);
	if (! $resql)
	{
		dol_print_error($db);
		exit;
	}

	$synclimit = 1;	// 0 = all
	$i=0;
	while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
	{
		$gContacts[] = new GContact($obj->rowid,'contact');
		$i++;
	}

	$result=0;
	if (count($gContacts)) $result=GContact::insertGContactsEntries($gContacts);

	if ($result >= 0) $mesg = $langs->trans("PushToGoogleSucess",count($gContacts));
	else $mesg = $langs->trans("Error");
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


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="save">';

print $langs->trans("GoogleEnableSyncToThirdparties").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_THIRDPARTIES",isset($_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"])?$_POST["GOOGLE_DUPLICATE_INTO_THIRDPARTIES"]:$conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES,1).'<br>';
print $langs->trans("GoogleEnableSyncToContacts").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_CONTACTS",isset($_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"])?$_POST["GOOGLE_DUPLICATE_INTO_CONTACTS"]:$conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS,1).'<br>';
print '<br>';

$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter")."</td>";
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
// Label to use for thirdparties
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("GOOGLE_TAG_PREFIX")."<br /></td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_TAG_PREFIX" value="'.dol_escape_htmltag(getTagLabel('thirdparties')).'">';
print "</td>";
print "</tr>";
// Label to use for contacts
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("GOOGLE_TAG_PREFIX_CONTACTS")."<br /></td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_TAG_PREFIX_CONTACTS" value="'.dol_escape_htmltag(getTagLabel('contacts')).'">';
print "</td>";
print "</tr>";

print "</table>";
print "<br>";


print '<center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();

print '<br>';


print '<div class="tabsActions">';
if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES))
{
	print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreate")."</a>";
}
else
{
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testall">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreate">'.$langs->trans("TestCreate")."</a>";
}
print '</div>';


print '<br><br>';

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_THIRDPARTIES))
{
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallthirdparties">';
	print $langs->trans("ExportThirdpartiesToGoogle")." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Go").'">';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallthirdparties">';
	print $langs->trans("DeleteAllGoogleThirdparties")." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Go").'">';
	print "</form>\n";
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_CONTACTS))
{
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallcontacts">';
	print $langs->trans("ExportContactToGoogle")." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Go").'">';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallcontacts">';
	print $langs->trans("DeleteAllGoogleContacts")." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Go").'">';
	print "</form>\n";
}

dol_htmloutput_mesg($mesg);
dol_htmloutput_errors($error,$errors);



llxFooter();

if (is_object($db)) $db->close();
?>
