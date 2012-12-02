<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google.php
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
    $res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_CONTACT'.$i,trim($_POST["GOOGLE_DUPLICATE_INTO_CONTACT"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_LOGIN',trim($_POST["GOOGLE_CONTACT_LOGIN"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_PASSWORD',trim($_POST["GOOGLE_CONTACT_PASSWORD"]),'chaine',0);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_CONTACT_LABEL',trim($_POST["GOOGLE_CONTACT_LABEL"]),'chaine',0);
    if (! $res > 0) $error++;
/*	$res=dolibarr_set_const($db,'GOOGLE_EVENT_LABEL_INC_SOCIETE',trim($_POST["GOOGLE_EVENT_LABEL_INC_SOCIETE"]),'chaine',0);
    if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_EVENT_LABEL_INC_CONTACT',trim($_POST["GOOGLE_EVENT_LABEL_INC_CONTACT"]),'chaine',0);
    if (! $res > 0) $error++;
*/

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
if ($action == 'testcreate')
{
    include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');

    $object=new Societe($db);
    $result=$object->initAsSpecimen();

    $object->name='Test Synchro Contact (can be deleted)';
    $object->email='email@email.com';
    /*$object->code_client=-1;
    $object->code_fournisseur=-1;*/
    $result=$object->create($user);

    $object->name='New test Synchro Contact (can be deleted)';
    $object->email='newemail@newemail.com';
    $result=$object->update($object->id, $user);

    $result=$object->delete($object->id);

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

print $langs->trans("GoogleEnableSyncToContact").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_CONTACT",isset($_POST["GOOGLE_DUPLICATE_INTO_CONTACT"])?$_POST["GOOGLE_DUPLICATE_INTO_CONTACT"]:$conf->global->GOOGLE_DUPLICATE_INTO_CONTACT,1).'<br><br>';


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
print '<input class="flat" type="text" size="24" name="GOOGLE_CONTACT_LOGIN" value="'.$conf->global->GOOGLE_CONTACT_LOGIN.'">';
//print ' &nbsp; '.$langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print "</td>";
print "</tr>";
// Google password
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_PASSWORD")."</td>";
print "<td>";
print '<input class="flat" type="text" size="10" name="GOOGLE_CONTACT_PASSWORD" value="'.$conf->global->GOOGLE_CONTACT_PASSWORD.'">';
//print ' &nbsp; '.$langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print "</td>";
print "</tr>";
// Label to use
/*
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("GOOGLE_CONTACT_LABEL")."<br /></td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_CONTACT_LABEL" value="'.$conf->global->GOOGLE_CONTACT_LABEL.'">';
print "</td>";
print "</tr>";
*/

print "</table>";
print "<br>";


print '<center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();

print '<br>';


print '<div class="tabsActions">';
if (empty($conf->global->GOOGLE_CONTACT_LOGIN) || empty($conf->global->GOOGLE_CONTACT_LOGIN))
{
	print '<a class="butActionRefused" href="#">'.$langs->trans("TestConnection")."</a>";
}
else
{
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreate">'.$langs->trans("TestCreateUpdateDelete")."</a>";
}
print '</div>';


dol_htmloutput_mesg($mesg);
dol_htmloutput_errors($error,$errors);



llxFooter();

if (is_object($db)) $db->close();
?>
