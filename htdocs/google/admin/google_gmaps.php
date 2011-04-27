<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google_gmaps.php
 *      \ingroup    google
 *      \brief      Setup page for google module
 *		\version    $Id: google_gmaps.php,v 1.2 2011/04/27 18:13:14 eldy Exp $
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
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
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
    $res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS'.$i,trim($_POST["GOOGLE_ENABLE_GMAPS".$i]),'chaine',0);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_CONTACTS'.$i,trim($_POST["GOOGLE_ENABLE_GMAPS_CONTACTS".$i]),'chaine',0);
	$res+=dolibarr_set_const($db,'GOOGLE_ENABLE_GMAPS_MEMBERS'.$i,trim($_POST["GOOGLE_ENABLE_GMAPS_MEMBERS".$i]),'chaine',0);
	
    if ($res == 3)
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

dol_fiche_head($head, 'gmaps', $langs->trans("GoogleTools"));


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';

print $langs->trans("GoogleEnableThisToolThirdParties").': ';
if ($conf->societe->enabled) 
{
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS",isset($_POST["GOOGLE_ENABLE_GMAPS"])?$_POST["GOOGLE_ENABLE_GMAPS"]:$conf->global->GOOGLE_ENABLE_GMAPS,1).'<br>';
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module1Name"));

print $langs->trans("GoogleEnableThisToolContacts").': ';
if ($conf->societe->enabled) 
{
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS_CONTACTS",isset($_POST["GOOGLE_ENABLE_GMAPS_CONTACTS"])?$_POST["GOOGLE_ENABLE_GMAPS_CONTACTS"]:$conf->global->GOOGLE_ENABLE_GMAPS_CONTACTS,1).'<br>';
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module1Name"));

print $langs->trans("GoogleEnableThisToolMembers").': ';
if ($conf->adherent->enabled) 
{
	print $form->selectyesno("GOOGLE_ENABLE_GMAPS_MEMBERS",isset($_POST["GOOGLE_ENABLE_GMAPS_MEMBERS"])?$_POST["GOOGLE_ENABLE_GMAPS_MEMBERS"]:$conf->global->GOOGLE_ENABLE_GMAPS_MEMBERS,1).'<br>';
}
else print $langs->trans("ModuleMustBeEnabledFirst",$langs->transnoentitiesnoconv("Module310Name"));

/*
$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="180">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Timezone
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ClientTZ")."</td>";
print "<td>";
print $formadmin->select_timezone($conf->global->GOOGLE_AGENDA_TIMEZONE,'google_agenda_timezone');
print "</td>";
print "</tr>";
// Nb of agenda
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("GoogleNbOfAgenda")."</td>";
print "<td>";
print '<input class="flat" type="text" size="2" name="GOOGLE_AGENDA_NB" value="'.$conf->global->GOOGLE_AGENDA_NB.'">';
print "</td>";
print "</tr>";

print "</table>";
print "<br>";


print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Name")."</td>";
print "<td>".$langs->trans("GoogleIDAgenda")." (".$langs->trans("Example").': assodolibarr@gmail.com)</td>';
print "<td>".$langs->trans("Color")."</td>";
print "</tr>";

$i=1;
$var=true;
while ($i <= $MAXAGENDA)
{
	$key=$i;
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td width="180" nowrap="nowrap">'.$langs->trans("GoogleAgendaNb",$key)."</td>";
	$name='GOOGLE_AGENDA_NAME'.$key;
	$src='GOOGLE_AGENDA_SRC'.$key;
	$color='GOOGLE_AGENDA_COLOR'.$key;
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_name".$key."\" value=\"". $conf->global->$name . "\" size=\"28\"></td>";
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_src".$key."\" value=\"". $conf->global->$src . "\" size=\"60\"></td>";
	print '<td nowrap="nowrap">';
	// Possible colors are limited by Google
	//print $formadmin->select_colors($conf->global->$color, "google_agenda_color".$key, $colorlist);
	print $formother->select_color($conf->global->$color, "google_agenda_color".$key, 'googleconfig', 1, $colorlist);
	print '</td>';
	print "</tr>";
	$i++;
}

print '</table>';
print '<br>';
*/

print '<br>';
print '<center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();


if ($mesg) print "<br>$mesg<br>";
print "<br>";

// Show message
$message='';
//$urlgooglehelp='<a href="http://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
//$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
//print info_admin($message);

$db->close();

llxFooter('$Date: 2011/04/27 18:13:14 $ - $Revision: 1.2 $');
?>
