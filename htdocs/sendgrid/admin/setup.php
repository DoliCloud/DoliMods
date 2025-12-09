<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-François FERRY  <jfefe@aternatik.fr>
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
 */

/**
 *   	\file       htdocs/sendgrid/admin/setup.php
 *		\ingroup    sendgrid
 *		\brief      Setup of module SendGrid
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
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
dol_include_once("/sendgrid/lib/sendgrid.lib.php");

// Load traductions files requiredby by page
$langs->load("admin");
$langs->load("companies");
$langs->load("sendgrid@sendgrid");
$langs->load("sms");

if (!$user->admin)
accessforbidden();
// Get parameters

$action=GETPOST('action', 'aZ09');

// Protection if external user
if ($user->societe_id > 0) {
	//accessforbidden();
}

$substitutionarrayfortest=array(
'__ID__' => 'TESTIdRecord',
'__LASTNAME__' => 'TESTLastname',
'__FIRSTNAME__' => 'TESTFirstname'
);


// Activate error interceptions
if (! empty($conf->global->MAIN_ENABLE_EXCEPTION)) {
	function traitementErreur($code, $message, $fichier, $ligne, $contexte)
	{
		if (error_reporting() & $code) {
			throw new Exception($message, $code);
		}
	}
	set_error_handler('traitementErreur');
}




/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin) {
	$result=dolibarr_set_const($db, "SENDGRIDAPPKEY", trim(GETPOST("SENDGRIDAPPKEY")), 'chaine', 0, '', $conf->entity);

	if ($result >= 0) {
		$mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
	} else {
		dol_print_error($db);
	}
}

if ($action == 'setvalue_account' && $user->admin) {
	$result=dolibarr_set_const($db, "SENDGRIDSMS_ACCOUNT", $_POST["SENDGRIDSMS_ACCOUNT"], 'chaine', 0, '', $conf->entity);

	if ($result >= 0) {
		$mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
	} else {
		dol_print_error($db);
	}
}

if ($action == 'requestcredential') {
	// Informations about your application
	$applicationKey = $conf->global->SENDGRIDAPPKEY;
	$applicationSecret = $conf->global->SENDGRIDAPPSECRET;
	$redirect_uri=dol_buildpath('/sendgrid/admin/sendgrid_setup.php?action=backfromauth', 2);

	// Information about API and rights asked
	$rights = array(
		(object) ['method'    => 'GET', 'path'      => '/me*' ],        // This include /me/bill
		(object) ['method'    => 'GET', 'path'      => '/sms*' ],
		(object) ['method'    => 'GET', 'path'      => '/telephony*' ],
		(object) ['method'    => 'GET', 'path'      => '/dedicated/server*' ],
		(object) ['method'    => 'GET', 'path'      => '/cloud*' ],
		(object) ['method'    => 'POST', 'path'      => '/cloud/project/*/instance/*/snapshot' ],
		(object) ['method'    => 'GET', 'path'      => '/ip*' ],
		(object) ['method'    => 'POST', 'path'      => '/sms*' ],
		(object) ['method'    => 'POST', 'path'      => '/telephony*' ],
	);
	/*
	$rights = array( (object) [
		'method'    => 'GET',
		'path'      => '/me*'
	]);*/

	// Get credentials
	try {
		dol_syslog("Request credential to endpoint ".$endpoint);
		dol_syslog("applicationKey=".$applicationKey." applicationSecret=".$applicationKey);

		$http_client = new GClient();
		$http_client->setDefaultOption('connect_timeout', empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT);  // Timeout by default of SENDGRID is 5 and it is not enough
		$http_client->setDefaultOption('timeout', empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);

		$conn = new Api($applicationKey, $applicationSecret, $endpoint, null, $http_client);    // consumer_key is not set to force to get a new one
		$credentials = $conn->requestCredentials($rights, $redirect_uri);

		$_SESSION['sendgrid_consumer_key']=$credentials["consumerKey"];
		header('Location: '. $credentials["validationUrl"]);
		exit;
	} catch (Exception $e) {
		setEventMessages($e->getMessage(), null, 'errors');
		$action='';
	}
}

if ($action == 'backfromauth' && ! empty($_SESSION["sendgrid_consumer_key"])) {
	// Save
	$result=dolibarr_set_const($db, "SENDGRIDCONSUMERKEY", $_SESSION["sendgrid_consumer_key"], 'chaine', 0, '', $conf->entity);

	if ($result >= 0) {
		$mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
	} else {
		dol_print_error($db);
	}
}


/*
 * View
 */

llxHeader('', $langs->trans('SendgridSetup'), '', '');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans("SendgridSetup"), $linkback, 'setup');

$head=sendgridadmin_prepare_head();


print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="setvalue">';

if (! empty($conf->global->SENDGRID_OLDAPI)) {
	if (!extension_loaded('soap')) {
		print '<div class="error">'.$langs->trans("PHPExtensionSoapRequired").'</div>';
	}
}

$var=true;

dol_fiche_head($head, 'common', $langs->trans("Sendgrid"), -1);

if (empty($conf->global->SENDGRID_OLDAPI)) {
	print $langs->trans("SendgridGoOnPageToCreateYourAPIKey", 'https://eu.api.sendgrid.com/createApp/', 'https://eu.api.sendgrid.com/createApp/').'<br>';
	print $langs->trans("ListOfExistingAPIApp", 'https://eu.api.sendgrid.com/console/#/me/api/application#GET', 'https://eu.api.sendgrid.com/console/#/me/api/application#GET').' (first log in on top right corner)<br><br>';
}

print '<table class="noborder" width="100%">';


	$var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">';
	print $langs->trans("SendgridApplicationKey").'</td><td>';
	print '<input size="64" type="text" name="SENDGRIDAPPKEY" value="' . getDolGlobalString('SENDGRIDAPPKEY').'">';
	print '</td><td>'.$langs->trans("Example").': Ld9GQ3AfaXDyZdsM';
	print '</td></tr>';

/*    $var=!$var;
	print '<tr '.$bc[$var].'><td class="fieldrequired">';
	print $langs->trans("SendgridConsumerkey").'</td><td>';

	if (! empty($conf->global->SENDGRIDAPPNAME) && ! empty($conf->global->SENDGRIDAPPKEY) && ! empty($conf->global->SENDGRIDAPPSECRET))
	{
		print '<input size="64" type="text" name="SENDGRIDCONSUMERKEY" value="'.$conf->global->SENDGRIDCONSUMERKEY.'">';
	}
	else
	{
		print $langs->trans("PleaseFillOtherFieldFirst");
	}
	print '</td><td>';
	if (! empty($conf->global->SENDGRIDAPPNAME) && ! empty($conf->global->SENDGRIDAPPKEY) && ! empty($conf->global->SENDGRIDAPPSECRET))
	{
		if (empty($conf->global->SENDGRIDCONSUMERKEY)) print img_warning().' ';
		print $langs->trans("ClickHereToLoginAndGetYourConsumerKey", $_SERVER["PHP_SELF"].'?action=requestcredential');
		//print '<br>'.info_admin($langs->trans('SENDGRIDURLMustNotBeLocal'));   Can work with a local URL.
	}
	print '</td></tr>';
*/

print '</table>';

dol_fiche_end();

print '<div align="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';

print '</form>';



dol_htmloutput_mesg($mesg);



// End of page

llxFooter();

$db->close();

/**
 * Function to trap FATAL errors
 *
 * @param string	$no        No
 * @param string	$str       Str
 * @param string	$file      File
 * @param string	$line      Line
 */
function my_error_handler($no, $str, $file, $line)
{
	$e = new ErrorException($str, $no, 0, $file, $line);
	print $e;
}
