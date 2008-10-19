<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
	    \file       htdocs/admin/google.php
        \ingroup    google
        \brief      Setup page for google module
		\version    $Id: google.php,v 1.3 2008/10/19 13:44:50 eldy Exp $
*/

$res=@include("./pre.inc.php");
if (! $res) include("../../../dolibarr/htdocs/admin/pre.inc.php");	// Used on dev env only

require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/html.formadmin.class.php');


if (!$user->admin)
    accessforbidden();

$langs->load("google");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

$MAXAGENDA=5;


// Sauvegardes parametres
if ($actionsave)
{
    $db->begin();
    
    $i=1;
	$error=0;
	
	// Save agendas
	while ($i <= $MAXAGENDA)
	{
		$color=trim($_POST["google_agenda_color".$i]);
		if ($color=='-1') $color='';
		
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


/**
 * Affichage du formulaire de saisie
 */
$formadmin=new FormAdmin($db);

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"),$linkback,'setup');
print '<br>';


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Name")."</td>";
print "<td>".$langs->trans("GoogleIDAgenda")."</td>";
print "<td>".$langs->trans("Color")."</td>";
print "</tr>";

$i=1;
while ($i <= $MAXAGENDA)
{
	$key=$i;
	print "<tr class=\"impair\">";
	print '<td nowrap="nowrap">'.$langs->trans("GoogleAgendaNb",$key)."</td>";
	$name='GOOGLE_AGENDA_NAME'.$key;
	$src='GOOGLE_AGENDA_SRC'.$key;
	$color='GOOGLE_AGENDA_COLOR'.$key;
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_name".$key."\" value=\"". $conf->global->$name . "\" size=\"28\"></td>";
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_src".$key."\" value=\"". $conf->global->$src . "\" size=\"60\"></td>";
	print '<td nowrap="nowrap">';
	// Possible colors are limited by Google
	$colorlist=array('29527A','5229A3','A32929','7A367A','B1365F','0D7813');
	print $formadmin->select_colors($conf->global->$color, "google_agenda_color".$key, $colorlist);
	print '</td>';
	print "</tr>";
	$i++;
}

print '</table>';

print '<br>';

$var=false;
print "<table class=\"noborder\" width=\"100%\">";
print "<tr class=\"liste_titre\">";
print '<td width="100">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("TimeZone")."</td>";
print "<td>";
print $formadmin->select_timezone($conf->global->GOOGLE_AGENDA_TIMEZONE,'google_agenda_timezone');
print "</td>";
print "</tr>";

print "</table>";
print "<br>";

print '<br><center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


if ($mesg) print "<br>$mesg<br>";
print "<br>";

// Show message
$message='';
$urlgooglehelp='<a href="http://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
print info_admin($message);

$db->close();

llxFooter('$Date: 2008/10/19 13:44:50 $ - $Revision: 1.3 $');
?>
