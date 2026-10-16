<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/openstreetmap/admin/openstreetmap_gmaps.php
 *      \ingroup    openstreetmap
 *      \brief      Setup page for openstreetmap module (Maps)
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 */

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

if (!$user->admin)
	accessforbidden();

$langs->load("openstreetmap@openstreetmap");
$langs->load("admin");
$langs->load("other");

$def = array();
$actionsave = GETPOST('save', 'alpha');



/*
 * Actions
 */

if ($actionsave) {
	$db->begin();

	$res=0;
	$res+=dolibarr_set_const($db, 'OPENSTREETMAP_ENABLE_MAPS', (GETPOSTINT('OPENSTREETMAP_ENABLE_MAPS') ? '1' : '0'), 'chaine', 0, '', $conf->entity);
	$res+=dolibarr_set_const($db, 'OPENSTREETMAP_ENABLE_MAPS_CONTACTS', (GETPOSTINT('OPENSTREETMAP_ENABLE_MAPS_CONTACTS') ? '1' : '0'), 'chaine', 0, '', $conf->entity);
	$res+=dolibarr_set_const($db, 'OPENSTREETMAP_MENU_MAP_CONTACTS', (GETPOSTINT('OPENSTREETMAP_MENU_MAP_CONTACTS') ? '1' : '0'), 'chaine', 0, '', $conf->entity);
	$res+=dolibarr_set_const($db, 'OPENSTREETMAP_ENABLE_MAPS_MEMBERS', (GETPOSTINT('OPENSTREETMAP_ENABLE_MAPS_MEMBERS') ? '1' : '0'), 'chaine', 0, '', $conf->entity);
	$res+=dolibarr_set_const($db, 'OPENSTREETMAP_MAPS_ZOOM_LEVEL', (string) max(2, min(18, GETPOSTINT('OPENSTREETMAP_MAPS_ZOOM_LEVEL') ? GETPOSTINT('OPENSTREETMAP_MAPS_ZOOM_LEVEL') : 15)), 'chaine', 0, '', $conf->entity);

	if ($res == 5) {
		$db->commit();
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		$db->rollback();
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}




/*
 * View
 */

$form=new Form($db);
$formadmin=new FormAdmin($db);
$formother=new FormOther($db);

$help_url='EN:Module_OpenStreetMap_EN|FR:Module_OpenStreetMap|ES:Modulo_OpenStreetMap';
llxHeader('', $langs->trans("OpenStreetMapSetup"), $help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("OpenStreetMapSetup"), $linkback, 'setup');
print '<br>';


$head=openstreetmapadmin_prepare_head();



print '<form name="openstreetmapconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';

dol_fiche_head($head, 'maps', $langs->trans("OpenStreetMapTools"));

print $langs->trans("OpenStreetMapEnableThisToolThirdParties").': ';
if (isset($conf->societe->enabled) && $conf->societe->enabled) {
	print $form->selectyesno("OPENSTREETMAP_ENABLE_MAPS", GETPOSTISSET('OPENSTREETMAP_ENABLE_MAPS') ? GETPOSTINT('OPENSTREETMAP_ENABLE_MAPS') : getDolGlobalInt('OPENSTREETMAP_ENABLE_MAPS'), 1);
} else print $langs->trans("ModuleMustBeEnabledFirst", $langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

//print '<br>';
print $langs->trans("OpenStreetMapEnableThisToolContacts").': ';
if (isset($conf->societe->enabled) && $conf->societe->enabled) {
	print $form->selectyesno("OPENSTREETMAP_ENABLE_MAPS_CONTACTS", GETPOSTISSET('OPENSTREETMAP_ENABLE_MAPS_CONTACTS') ? GETPOSTINT('OPENSTREETMAP_ENABLE_MAPS_CONTACTS') : getDolGlobalInt('OPENSTREETMAP_ENABLE_MAPS_CONTACTS'), 1);
} else print $langs->trans("ModuleMustBeEnabledFirst", $langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

print $langs->trans("OpenStreetMapMenuMapContacts").': ';
if (isset($conf->societe->enabled) && $conf->societe->enabled) {
	print $form->selectyesno("OPENSTREETMAP_MENU_MAP_CONTACTS", GETPOSTISSET('OPENSTREETMAP_MENU_MAP_CONTACTS') ? GETPOSTINT('OPENSTREETMAP_MENU_MAP_CONTACTS') : getDolGlobalInt('OPENSTREETMAP_MENU_MAP_CONTACTS'), 1);
	print ' <span class="opacitymedium small">'.$langs->trans("OpenStreetMapMenuMapContactsDesc").'</span>';
} else print $langs->trans("ModuleMustBeEnabledFirst", $langs->transnoentitiesnoconv("Module1Name"));
print '<br>';

//print '<br>';
print $langs->trans("OpenStreetMapEnableThisToolMembers").': ';
if (isset($conf->adherent->enabled) && $conf->adherent->enabled) {
	print $form->selectyesno("OPENSTREETMAP_ENABLE_MAPS_MEMBERS", GETPOSTISSET('OPENSTREETMAP_ENABLE_MAPS_MEMBERS') ? GETPOSTINT('OPENSTREETMAP_ENABLE_MAPS_MEMBERS') : getDolGlobalInt('OPENSTREETMAP_ENABLE_MAPS_MEMBERS'), 1);
} else print $langs->trans("ModuleMustBeEnabledFirst", $langs->transnoentitiesnoconv("Module310Name"));
print '<br>';

//print '<br>';
print $langs->trans("OpenStreetMapZoomLevel", 2, 18).': ';
print '<input class="flat" name="OPENSTREETMAP_MAPS_ZOOM_LEVEL" id="OPENSTREETMAP_MAPS_ZOOM_LEVEL" value="'.(GETPOSTISSET("OPENSTREETMAP_MAPS_ZOOM_LEVEL") ? GETPOST("OPENSTREETMAP_MAPS_ZOOM_LEVEL", 'int') : getDolGlobalInt('OPENSTREETMAP_MAPS_ZOOM_LEVEL', 15)).'" size="2">';

dol_fiche_end();

print '<center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


// Show message
$message='';
//$urlopenstreetmaphelp='<a href="http://www.openstreetmap.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.openstreetmap.com/calendar/embed/EmbedHelper_en.html</a>';
//$message.=$langs->trans("OpenStreetMapSetupHelp",$urlopenstreetmaphelp);
//print info_admin($message);


llxFooter();

$db->close();
