<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google_gmaps.php
 *      \ingroup    google
 *      \brief      Setup page for google module (GMaps)
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
dol_include_once("/google/lib/google.lib.php");

if (!$user->admin)
    accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];



/*
 * Actions
 */

if ($actionsave)
{
    $db->begin();

	$res=0;
    $res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS',trim($_POST["GOOGLE_ENABLE_GMAPS"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_CONTACTS',trim($_POST["GOOGLE_ENABLE_GMAPS_CONTACTS"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_MEMBERS',trim($_POST["GOOGLE_ENABLE_GMAPS_MEMBERS"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_GMAPS_ZOOM_LEVEL',trim($_POST["GOOGLE_GMAPS_ZOOM_LEVEL"]),'chaine',0,'',$conf->entity);

    if ($res == 4)
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




/*
 * View
 */

$form=new Form($db);
$formadmin=new FormAdmin($db);
$formother=new FormOther($db);

$help_url='EN:Module_Google_EN|FR:Module_Google|ES:Modulo_Google';
llxHeader('',$langs->trans("GoogleSetup"),$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"),$linkback,'setup');
print '<br>';


$head=googleadmin_prepare_head();

dol_fiche_head($head, 'tabgmaps', $langs->trans("GoogleTools"));


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';

print $langs->trans("GoogleEnableThisToolThirdParties").': ';
if ($conf->societe->enabled)
{
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS",isset($_POST["GOOGLE_ENABLE_GMAPS"])?$_POST["GOOGLE_ENABLE_GMAPS"]:$conf->global->GOOGLE_ENABLE_GMAPS,1);
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

//print '<br>';
print $langs->trans("GoogleEnableThisToolContacts").': ';
if ($conf->societe->enabled)
{
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS_CONTACTS",isset($_POST["GOOGLE_ENABLE_GMAPS_CONTACTS"])?$_POST["GOOGLE_ENABLE_GMAPS_CONTACTS"]:$conf->global->GOOGLE_ENABLE_GMAPS_CONTACTS,1);
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

//print '<br>';
print $langs->trans("GoogleEnableThisToolMembers").': ';
if ($conf->adherent->enabled)
{
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS_MEMBERS",isset($_POST["GOOGLE_ENABLE_GMAPS_MEMBERS"])?$_POST["GOOGLE_ENABLE_GMAPS_MEMBERS"]:$conf->global->GOOGLE_ENABLE_GMAPS_MEMBERS,1);
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module310Name"));
print '<br>';


print '<br>';


$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";

//print '<br>';
print '<tr '.$bc[$var].'><td>'.$langs->trans("GoogleZoomLevel").'</td><td>';
print '<input class="flat" name="GOOGLE_GMAPS_ZOOM_LEVEL" id="GOOGLE_GMAPS_ZOOM_LEVEL" value="'.(isset($_POST["GOOGLE_GMAPS_ZOOM_LEVEL"])?$_POST["GOOGLE_GMAPS_ZOOM_LEVEL"]:$conf->global->GOOGLE_GMAPS_ZOOM_LEVEL).'" size="2">';
print '</td></tr>';

print '</table>';

print '<br>';
print '<center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();

dol_htmloutput_mesg($mesg);

// Show message
$message='';
//$urlgooglehelp='<a href="http://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
//$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
//print info_admin($message);

llxFooter();

$db->close();
?>
