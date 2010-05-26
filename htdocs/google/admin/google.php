<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/admin/google.php
 *      \ingroup    google
 *      \brief      Setup page for google module
 *		\version    $Id: google.php,v 1.1 2010/05/26 13:06:08 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=@include("../../main.inc.php");
if (! $res) include("../../../../dolibarr/htdocs/main.inc.php");	// Used on dev env only

require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');

$res=@include_once("../google.lib.php");
if (! $res) include_once(DOL_DOCUMENT_ROOT."/google/google.lib.php");

if (!$user->admin)
    accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

if (empty($conf->global->GOOGLE_AGENDA_NB)) $conf->global->GOOGLE_AGENDA_NB=5;
$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;

// List of Google colors
$colorlist=array('29527A','5229A3','A32929','7A367A','B1365F','0D7813');


/*
 * Actions
 */
if ($actionsave)
{
    $db->begin();

	$res=dolibarr_set_const($db,'GOOGLE_ENABLE_AGENDA'.$i,trim($_POST["GOOGLE_ENABLE_AGENDA".$i]),'chaine',0);

	$i=1;
	$error=0;

	// Save agendas
	while ($i <= $MAXAGENDA)
	{
		$color=trim($_POST["google_agenda_color".$i]);
		if ($color=='-1') $color='';

		//print 'color='.$color;
		$res=dolibarr_set_const($db,'GOOGLE_AGENDA_NAME'.$i,trim($_POST["google_agenda_name".$i]),'chaine',0);
		if (! $res > 0) $error++;
		$res=dolibarr_set_const($db,'GOOGLE_AGENDA_SRC'.$i,trim($_POST["google_agenda_src".$i]),'chaine',0);
		if (! $res > 0) $error++;
		$res=dolibarr_set_const($db,'GOOGLE_AGENDA_COLOR'.$i,$color,'chaine',0);
		if (! $res > 0) $error++;
		$i++;
	}

	// Save timezone
	$timezone=trim($_POST["google_agenda_timezone"]);
	if ($timezone=='-1') $timezone='';
    $res=dolibarr_set_const($db,'GOOGLE_AGENDA_TIMEZONE',$timezone,'chaine',0);
	if (! $res > 0) $error++;
	// Save nb of agenda
	$res=dolibarr_set_const($db,'GOOGLE_AGENDA_NB',trim($_POST["GOOGLE_AGENDA_NB"]),'chaine',0);
	if (! $res > 0) $error++;
	if (empty($conf->global->GOOGLE_AGENDA_NB)) $conf->global->GOOGLE_AGENDA_NB=5;
	$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;

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




/*
 * View
 */


$form=new Form($db);
$formadmin=new FormAdmin($db);

$help_url='EN:Module_Google_EN|FR:Module_Google|ES:Modulo_Google';
llxHeader('',$langs->trans("GoogleSetup"),$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"),$linkback,'setup');
print '<br>';


$head=googleadmin_prepare_head();

dol_fiche_head($head, 'agenda', $langs->trans("GoogleTools"));


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';

print $langs->trans("GoogleEnableThisTool").' '.$form->selectyesno("GOOGLE_ENABLE_AGENDA",isset($_POST["GOOGLE_ENABLE_AGENDA"])?$_POST["GOOGLE_ENABLE_AGENDA"]:0,1).'<br><br>';


$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="140">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Timezone
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("TimeZone")."</td>";
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
print "<td>".$langs->trans("GoogleIDAgenda")."</td>";
print "<td>".$langs->trans("Color")."</td>";
print "</tr>";

$i=1;
$var=true;
while ($i <= $MAXAGENDA)
{
	$key=$i;
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td width="140" nowrap="nowrap">'.$langs->trans("GoogleAgendaNb",$key)."</td>";
	$name='GOOGLE_AGENDA_NAME'.$key;
	$src='GOOGLE_AGENDA_SRC'.$key;
	$color='GOOGLE_AGENDA_COLOR'.$key;
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_name".$key."\" value=\"". $conf->global->$name . "\" size=\"28\"></td>";
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_src".$key."\" value=\"". $conf->global->$src . "\" size=\"60\"></td>";
	print '<td nowrap="nowrap">';
	// Possible colors are limited by Google
	print $formadmin->select_colors($conf->global->$color, "google_agenda_color".$key, $colorlist);
	print '</td>';
	print "</tr>";
	$i++;
}

print '</table>';
print '<br>';


print '<center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

print '</div>';


if ($mesg) print "<br>$mesg<br>";
print "<br>";

// Show message
$message='';
$urlgooglehelp='<a href="http://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
print info_admin($message);

$db->close();

llxFooter('$Date: 2010/05/26 13:06:08 $ - $Revision: 1.1 $');
?>
