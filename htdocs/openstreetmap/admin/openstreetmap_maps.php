<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/openstreetmap/admin/openstreetmap_gmaps.php
 *      \ingroup    openstreetmap
 *      \brief      Setup page for openstreetmap module (Maps)
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
dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

if (!$user->admin)
    accessforbidden();

$langs->load("openstreetmap@openstreetmap");
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
    $res+=dolibarr_set_const($db,'OPENSTREETMAP_ENABLE_MAPS',trim($_POST["OPENSTREETMAP_ENABLE_MAPS"]),'chaine',0);
	$res+=dolibarr_set_const($db,'OPENSTREETMAP_ENABLE_MAPS_CONTACTS',trim($_POST["OPENSTREETMAP_ENABLE_MAPS_CONTACTS"]),'chaine',0);
	$res+=dolibarr_set_const($db,'OPENSTREETMAP_ENABLE_MAPS_MEMBERS',trim($_POST["OPENSTREETMAP_ENABLE_MAPS_MEMBERS"]),'chaine',0);
	$res+=dolibarr_set_const($db,'OPENSTREETMAP_MAPS_ZOOM_LEVEL',trim($_POST["OPENSTREETMAP_MAPS_ZOOM_LEVEL"]),'chaine',0);

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

$help_url='EN:Module_OpenStreetMap_EN|FR:Module_OpenStreetMap|ES:Modulo_OpenStreetMap';
llxHeader('',$langs->trans("OpenStreetMapSetup"),$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("OpenStreetMapSetup"),$linkback,'setup');
print '<br>';


$head=openstreetmapadmin_prepare_head();



print '<form name="openstreetmapconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';

dol_fiche_head($head, 'maps', $langs->trans("OpenStreetMapTools"));

print $langs->trans("OpenStreetMapEnableThisToolThirdParties").': ';
if ($conf->societe->enabled)
{
	print $form->selectyesno("OPENSTREETMAP_ENABLE_MAPS",isset($_POST["OPENSTREETMAP_ENABLE_MAPS"])?$_POST["OPENSTREETMAP_ENABLE_MAPS"]:$conf->global->OPENSTREETMAP_ENABLE_MAPS,1);
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

//print '<br>';
print $langs->trans("OpenStreetMapEnableThisToolContacts").': ';
if ($conf->societe->enabled)
{
	print $form->selectyesno("OPENSTREETMAP_ENABLE_MAPS_CONTACTS",isset($_POST["OPENSTREETMAP_ENABLE_MAPS_CONTACTS"])?$_POST["OPENSTREETMAP_ENABLE_MAPS_CONTACTS"]:$conf->global->OPENSTREETMAP_ENABLE_MAPS_CONTACTS,1);
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

//print '<br>';
print $langs->trans("OpenStreetMapEnableThisToolMembers").': ';
if ($conf->adherent->enabled)
{
	print $form->selectyesno("OPENSTREETMAP_ENABLE_MAPS_MEMBERS",isset($_POST["OPENSTREETMAP_ENABLE_MAPS_MEMBERS"])?$_POST["OPENSTREETMAP_ENABLE_MAPS_MEMBERS"]:$conf->global->OPENSTREETMAP_ENABLE_MAPS_MEMBERS,1);
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module310Name"));
print '<br>';

//print '<br>';
print $langs->trans("OpenStreetMapZoomLevel",2,18).': ';
print '<input class="flat" name="OPENSTREETMAP_MAPS_ZOOM_LEVEL" id="OPENSTREETMAP_MAPS_ZOOM_LEVEL" value="'.(isset($_POST["OPENSTREETMAP_MAPS_ZOOM_LEVEL"])?$_POST["OPENSTREETMAP_MAPS_ZOOM_LEVEL"]:($conf->global->OPENSTREETMAP_MAPS_ZOOM_LEVEL?$conf->global->OPENSTREETMAP_MAPS_ZOOM_LEVEL:15)).'" size="2">';

dol_fiche_end();

print '<center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_htmloutput_mesg($mesg);

// Show message
$message='';
//$urlopenstreetmaphelp='<a href="http://www.openstreetmap.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.openstreetmap.com/calendar/embed/EmbedHelper_en.html</a>';
//$message.=$langs->trans("OpenStreetMapSetupHelp",$urlopenstreetmaphelp);
//print info_admin($message);


llxFooter();

$db->close();
?>
