<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google.php
 *      \ingroup    google
 *      \brief      Setup page for google module (Calendar)
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
dol_include_once("/google/lib/google.lib.php");

if (!$user->admin) accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$action=GETPOST("action");

if (empty($conf->global->GOOGLE_AGENDA_NB)) $conf->global->GOOGLE_AGENDA_NB=5;
$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;

// List of Google colors (A lot of colors are ignored by Google)
$colorlist=array('7A367A','B1365F','5229A3','7A367A','29527A','2952A3','1B887A','28754E','0D7813','528800','88880E','AB8B00',
				 'BE6D00','865A5A','705770','4E5D6C','5A6986','6E6E41','8D6F47','691426','5C1158','125A12','875509','754916',
				 '5B123B','42104A','113F47','333333','711616','FFFFFF');


/*
 * Actions
 */
if ($action == 'save') {
	$db->begin();

	$res=dolibarr_set_const($db, 'GOOGLE_ENABLE_AGENDA', trim($_POST["GOOGLE_ENABLE_AGENDA"]), 'chaine', 0, '', $conf->entity);
	$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_CLIENT_ID', trim($_POST["GOOGLE_AGENDA_CLIENT_ID"]), 'chaine', 0, '', $conf->entity);
	$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_CLIENT_SECRET', trim($_POST["GOOGLE_AGENDA_CLIENT_SECRET"]), 'chaine', 0, '', $conf->entity);

	$i=1;
	$error=0;

	// Save agendas
	while ($i <= $MAXAGENDA) {
		$color=trim($_POST["google_agenda_color".$i]);
		if ($color=='-1') $color='';

		//print 'color='.$color;
		$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_NAME'.$i, trim($_POST["google_agenda_name".$i]), 'chaine', 0, '', $conf->entity);
		if (! $res > 0) $error++;
		$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_SRC'.$i, trim($_POST["google_agenda_src".$i]), 'chaine', 0, '', $conf->entity);
		if (! $res > 0) $error++;
		$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_COLOR'.$i, $color, 'chaine', 0, '', $conf->entity);
		if (! $res > 0) $error++;
		$i++;
	}

	// Save timezone
	$timezone=trim($_POST["google_agenda_timezone"]);
	if ($timezone=='-1') $timezone='';
	$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_TIMEZONE', $timezone, 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	// Save nb of agenda
	$res=dolibarr_set_const($db, 'GOOGLE_AGENDA_NB', trim($_POST["GOOGLE_AGENDA_NB"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	if (empty($conf->global->GOOGLE_AGENDA_NB)) $conf->global->GOOGLE_AGENDA_NB=5;
	$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;

	if (! $error) {
		$db->commit();
		setEventMessage($langs->trans("SetupSaved"));
	} else {
		$db->rollback();
		setEventMessage($langs->trans("Error"));
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
llxHeader('', $langs->trans("GoogleSetup"), $help_url, '', 0, 0, $arrayofjs, $arrayofcss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"), $linkback, 'setup');
print '<br>';



print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

$head=googleadmin_prepare_head();

dol_fiche_head($head, 'tabagenda', $langs->trans("GoogleTools"), -1);

print '<div class="fichecenter">';

if ($conf->use_javascript_ajax) {
	print "\n".'<script type="text/javascript" language="javascript">';
	print 'jQuery(document).ready(function () {
		function initfields()
		{
			if (jQuery("#GOOGLE_ENABLE_AGENDA").val() > 0) jQuery(".viewagenda").show();
			else jQuery(".viewagenda").hide();
		}
		initfields();
		jQuery("#GOOGLE_ENABLE_AGENDA").change(function() {
			initfields();
		});
	})';
	print '</script>'."\n";
}

print $langs->trans("GoogleEnableThisTool").' '.$form->selectyesno("GOOGLE_ENABLE_AGENDA", isset($_POST["GOOGLE_ENABLE_AGENDA"])?$_POST["GOOGLE_ENABLE_AGENDA"]:$conf->global->GOOGLE_ENABLE_AGENDA, 1).'<br>';


print '<div class="viewagenda">';
print '<br>';

$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td class="titlefield">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Timezone
print '<tr class="oddeven">';
print "<td>".$langs->trans("ClientTZ")."</td>";
print "<td>";
$tzarray=get_tz_array();
$selectedtz=(isset($conf->global->GOOGLE_AGENDA_TIMEZONE)?$conf->global->GOOGLE_AGENDA_TIMEZONE:$tzarray[$_SESSION['dol_tz']]);
print $formadmin->select_timezone($selectedtz, 'google_agenda_timezone');
print "</td>";
print "</tr>";
// Nb of agenda
print '<tr class="oddeven">';
print "<td>".$langs->trans("GoogleNbOfAgenda")."</td>";
print "<td>";
print '<input class="flat" type="text" size="2" name="GOOGLE_AGENDA_NB" value="'.$conf->global->GOOGLE_AGENDA_NB.'">';
print "</td>";
print "</tr>";

print "</table>";
print "<br>";


print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("GoogleAgendaToShow")."</td>";
print "<td>".$langs->trans("Name")."</td>";
print "<td>";
$text=$langs->trans("GoogleIDAgenda")." (".$langs->trans("Example").': assodolibarr@gmail.com)';
print $form->textwithpicto($text, $langs->trans("GoogleSetupHelp"));
print "</td>";
print '<td class="nowrap" align="center">'.$langs->trans("Color")."</td>";
print "</tr>";

$i=1;
$var=true;
while ($i <= $MAXAGENDA) {
	$key=$i;
	$var=!$var;
	print '<tr class="oddeven">';
	print '<td class="nowrap titlefield">'.$langs->trans("GoogleAgendaNb", $key)."</td>";
	$name='GOOGLE_AGENDA_NAME'.$key;
	$src='GOOGLE_AGENDA_SRC'.$key;
	$color='GOOGLE_AGENDA_COLOR'.$key;
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_name".$key."\" value=\"". $conf->global->$name . "\" size=\"28\"></td>";
	print "<td><input type=\"text\" class=\"flat\" name=\"google_agenda_src".$key."\" value=\"". $conf->global->$src . "\" size=\"60\"></td>";
	print '<td class="nowrap" align="center">';
	// Possible colors are limited by Google
	//print $formadmin->selectColor($conf->global->$color, "google_agenda_color".$key, $colorlist);
	print $formother->selectColor($conf->global->$color, "google_agenda_color".$key, 'googleconfig', 1, $colorlist);
	print '</td>';
	print "</tr>";
	$i++;
}

print '</table>';
print '<br>';



// Define $urlwithroot
//$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
//$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
$urlwithroot=DOL_MAIN_URL_ROOT;						// This is to use same domain name than current
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', $urlwithroot);

$redirect_uri=$urlwithouturlroot.dol_buildpath('/google/index.php', 1);		// Must be an url without parameters

$urltocreatekey='https://console.developers.google.com/apis/credentials';


print '<table class="noborder" width="100%">';

print '<tr class="liste_titre">';
print '<td class="titlefield">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleOAuth").')'."</td>";
print '<td colspan="2">'.$langs->trans("Name").'</td>';
print "</tr>";

// Setup for Oauth
print '<tr class="oddeven"><td colspan="3">';
print $langs->trans("DueToGoogleLimitYouNeedToLogin").'<br>';
print $langs->trans("AllowGoogleToLoginSetupKey").'<br>';
print '</td></tr>';

// Client ID
print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_CLIENT_ID")."</td>";
print "<td>";
print '<input class="flat minwidth300" type="text" name="GOOGLE_AGENDA_CLIENT_ID" value="'.$conf->global->GOOGLE_AGENDA_CLIENT_ID.'">';
print "</td>";

print '<td>';
//print $langs->trans("AllowGoogleToLoginWithClientID","https://console.developers.google.com/apis/credentials","https://console.developers.google.com/apis/credentials", $jsallowed, $redirect_uri).'<br>';
print $langs->trans("AllowGoogleToLoginWithClientID", $urltocreatekey, $urltocreatekey, $redirect_uri).'<br>';
//print $langs->trans("AllowGoogleToLoginProp",$urltocreatekey,$urltocreatekey,$redirect_uri);
print '</td>';

print "</tr>";
// Client Secret
print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_CLIENT_SECRET")."</td>";
print "<td>";
print '<input class="flat minwidth300" type="text" name="GOOGLE_AGENDA_CLIENT_SECRET" value="'.$conf->global->GOOGLE_AGENDA_CLIENT_SECRET.'">';
print "</td>";
print '<td>';
print $langs->trans("AllowGoogleToLoginWithClientSecret").'<br>';
print '</td>';
print "</tr>";

print '</table>';

print '</div>';

print '</div>';

dol_fiche_end();

print '<div align="center">';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</div>";

print "</form>\n";



// Show message
/*$message='';
$urlgooglehelp='<a href="https://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
print info_admin($message);
*/

llxFooter();

$db->close();
