<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google_gmaps.php
 *      \ingroup    google
 *      \brief      Setup page for google module (GMaps)
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
$action=GETPOST('action');


/*
 * Actions
 */

if ($action == 'gmap_deleteerrors')
{
    $sql="DELETE FROM ".MAIN_DB_PREFIX."google_maps WHERE result_code <> 'OK'";
    $result=$db->query($sql);

    if ($result)
    {
        setEventMessages($langs->trans("RecordInGeoEncodingErrorDeleted"), null);
    }
    else
    {
        setEventMessages("ErrorDeleting table goolg_maps with result_code <> 'OK'", null, 'errors');
    }
}

if ($actionsave)
{
    $db->begin();

	$res=0;
    $res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS',trim($_POST["GOOGLE_ENABLE_GMAPS"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_CONTACTS',trim($_POST["GOOGLE_ENABLE_GMAPS_CONTACTS"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_MEMBERS',trim($_POST["GOOGLE_ENABLE_GMAPS_MEMBERS"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_TICON',trim($_POST["GOOGLE_ENABLE_GMAPS_TICON"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_GMAPS_ZOOM_LEVEL',trim($_POST["GOOGLE_GMAPS_ZOOM_LEVEL"]),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'GOOGLE_API_SERVERKEY',trim($_POST["GOOGLE_API_SERVERKEY"]),'chaine',0,'',$conf->entity);

    if ($res == 6)
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

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"), $linkback, 'setup');
print '<br>';


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';

$head=googleadmin_prepare_head();

dol_fiche_head($head, 'tabgmaps', $langs->trans("GoogleTools"), -1);

print '<div class="fichecenter">';

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


print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td class="titlefield">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";

//print '<br>';
print '<tr class="oddeven"><td>'.$langs->trans("GoogleZoomLevel").'</td><td>';
print '<input class="flat" name="GOOGLE_GMAPS_ZOOM_LEVEL" id="GOOGLE_GMAPS_ZOOM_LEVEL" value="'.(isset($_POST["GOOGLE_GMAPS_ZOOM_LEVEL"])?$_POST["GOOGLE_GMAPS_ZOOM_LEVEL"]:$conf->global->GOOGLE_GMAPS_ZOOM_LEVEL).'" size="2">';
print '</td></tr>';

//ajout de la gestion des icones de status des Tiers : prospects/clients
if (! empty($conf->global->GOOGLE_CAN_USE_PROSPECT_ICONS) && ! empty($conf->societe->enabled))
{
	print '<tr class="oddeven"><td>'.$langs->trans("IconTiers").'</td><td>';
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS_TICON",isset($_POST["GOOGLE_ENABLE_GMAPS_TICON"])?$_POST["GOOGLE_ENABLE_GMAPS_TICON"]:$conf->global->GOOGLE_ENABLE_GMAPS_TICON,1);
	print '</td></tr>';
}



print '</table>';

print '<br>';

print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td class="titlefield">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleAPIv3Usage","Geocoding").')'."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Note")."</td>";
print "</tr>";
// Google login
print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVERKEY")."</td>";
print "<td>";
print '<input class="flat" type="text" size="64" name="GOOGLE_API_SERVERKEY" value="'.$conf->global->GOOGLE_API_SERVERKEY.'">';
print '</td>';
print '<td>';
//print $langs->trans("KeepEmptyYoUsePublicQuotaOfAPI","Geocoding API").'<br>';
print $langs->trans("AllowGoogleToLoginWithKey","https://console.developers.google.com/apis/credentials","https://console.developers.google.com/apis/credentials").'<br>';
print "</td>";
print "</tr>";

print '</table>';

print info_admin($langs->trans("EnableAPI","https://console.developers.google.com/apis/library/","https://console.developers.google.com/apis/library/","Google Maps Geocoding API, Google Maps Javascript API"));

print '</div>';

dol_fiche_end();

print '<div align="center">';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</div>";

print "</form>\n";


print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=gmap_deleteerrors">'.$langs->trans("ResetGeoEncodingErrors").'</a><br>';


dol_htmloutput_mesg($mesg);

// Show message
$message='';
//$urlgooglehelp='<a href="https://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
//$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
//print info_admin($message);

llxFooter();

$db->close();
