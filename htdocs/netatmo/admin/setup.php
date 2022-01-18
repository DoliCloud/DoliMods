<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018 Alice Adminson <testldr9@dolicloud.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    netatmo/admin/setup.php
 * \ingroup netatmo
 * \brief   netatmo setup page.
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/netatmo.lib.php';
require_once "../includes/src/Netatmo/autoload.php";

// Translations
$langs->loadLangs(array("admin", "oauth", "netatmo@netatmo"));

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

$arrayofparameters=array(
	'NETATMO_SECURITY_KEY'=>array('css'=>'minwidth200', 'enabled'=>1),
	'NETATMO_CLIENT_ID'=>array('css'=>'minwidth200', 'enabled'=>1, 'help'=>'eee'),
	'NETATMO_CLIENT_SECRET'=>array('css'=>'minwidth200', 'enabled'=>1),
);

// Define $urlwithroot
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', trim($dolibarr_main_url_root));
$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
//$urlwithroot=DOL_MAIN_URL_ROOT;						// This is to use same domain name than current. For Paypal payment, we can use internal URL like localhost.


/*
 * Actions
 */

if ((float) DOL_VERSION >= 6) {
	include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
}

//test if "code" is provided in get parameters (which would mean that user has already accepted the app and has been redirected here)
if (isset($_GET['code'])) {
	$config = array();
	$config['client_id'] = $conf->global->NETATMO_CLIENT_ID;
	$config['client_secret'] = $conf->global->NETATMO_CLIENT_SECRET;
	$config['scope'] = 'read_presence';
	$client = new Netatmo\Clients\NAApiClient($config);

	try {
		$tokens = $client->getAccessToken();
		$refresh_token = $tokens['refresh_token'];
		$access_token = $tokens['access_token'];

		// Save token into database
		require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
		$res=dolibarr_set_const($db, 'NETATMO_WEB_TOKEN', trim($access_token), 'chaine', 0, '', $conf->entity);
		$_SESSION['netatmo_web_token_'.$conf->entity]=trim($access_token);
		if (! $res > 0) $error++;

		$res=dolibarr_set_const($db, 'NETATMO_REFRESH_TOKEN', trim($refresh_token), 'chaine', 0, '', $conf->entity);
		$_SESSION['netatmo_refresh_token_'.$conf->entity]=trim($refresh_token);
		if (! $res > 0) $error++;

		setEventMessages("HasAccessToken", null, 'mesgs');
	} catch (Netatmo\Exceptions\NAClientException $ex) {
		echo " An error occured while trying to retrieve your tokens \n";
	}
} elseif (isset($_GET['error'])) {
	if ($_GET['error'] === 'access_denied') {
		echo "You refused that this application access your Netatmo Data";
	} else {
		echo "An error occured :".dol_escape_htmltag($_GET['error'])."\n";
		/*var_dump($_POST);
		var_dump($_GET);*/
	}
} elseif (GETPOST('action', 'alpha') == 'createtoken') {
	$config = array();
	$config['client_id'] = $conf->global->NETATMO_CLIENT_ID;
	$config['client_secret'] = $conf->global->NETATMO_CLIENT_SECRET;
	$config['scope'] = 'read_presence';
	$client = new Netatmo\Clients\NAApiClient($config);

	//redirect to Netatmo Authorize URL
	$redirect_url = $client->getAuthorizeUrl();
	header("HTTP/1.1 ". OAUTH2_HTTP_FOUND);
	//var_dump($redirect_url);exit;
	header("Location: ". $redirect_url);
	die();
}


/*
 * View
 */

$page_name = "NetatmoSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage?$backtopage:DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_netatmo@netatmo');

// Configuration header
$head = netatmoAdminPrepareHead();
dol_fiche_head($head, 'settings', '', -1, "netatmo@netatmo");

// Setup page goes here
echo '<span class="opacitymedium">'.$langs->trans("NetatmoSetupPage").'</span><br><br>';

$urlasredirecturi='<a href="'.dol_buildpath('/netatmo/admin/setup.php', 2).'" target="_blank" rel="noopener">'.dol_buildpath('/netatmo/admin/setup.php', 2).'</a>';

print 'Go on page to create an App: <a href="https://dev.netatmo.com/myaccount/" target="_blank" rel="noopener">https://dev.netatmo.com/myaccount/</a><br>';
print 'Create your Client ID / Secret ID using the following URL as Redirect URI: '.$urlasredirecturi.'<br>';

print '<br>';

if ($action == 'edit') {
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

	foreach ($arrayofparameters as $key => $val) {
		print '<tr class="oddeven"><td>';
		print $form->textwithpicto($langs->trans($key), $langs->trans($key.'Tooltip'));
		print '</td><td><input name="'.$key.'"  class="flat '.(empty($val['css'])?'minwidth200':$val['css']).'" value="' . $conf->global->$key . '"></td></tr>';
	}
	print '</table>';

	print '<br><div class="center">';
	print '<input class="button" type="submit" value="'.$langs->trans("Save").'">';
	print '</div>';

	print '</form>';
	print '<br>';
} else {
	if (! empty($arrayofparameters)) {
		print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

		foreach ($arrayofparameters as $key => $val) {
			print '<tr class="oddeven"><td>';
			print $form->textwithpicto($langs->trans($key), $langs->trans($key.'Tooltip'));
			print '</td><td>' . $conf->global->$key . '</td></tr>';
		}

		// Token
		print '<tr class="oddeven">';
		print '<td>'.$langs->trans("NETATMO_WEB_TOKEN")."</td>";
		print '<td colspan="2">';
		if (empty($conf->global->NETATMO_CLIENT_ID) || empty($conf->global->NETATMO_CLIENT_SECRET)) {
			print $langs->trans("FillAndSaveAccountIdAndSecret");
		} else {
			$oauthurl = $_SERVER["PHP_SELF"].'?action=createtoken';

			$completeoauthurl=$oauthurl;
			/*$completeoauthurl.='?response_type=code&client_id='.urlencode($conf->global->NETATMO_CLIENT_ID);
			$completeoauthurl.='&redirect_uri='.urlencode($redirect_uri);
			$completeoauthurl.='&scope='.urlencode('https://www.google.com/m8/feeds https://www.googleapis.com/auth/contacts.readonly');
			$completeoauthurl.='&include_granted_scopes=true';*/

			if (! empty($conf->global->NETATMO_WEB_TOKEN) || ! empty($_SESSION['netatmo_web_token_'.$conf->entity])) {
				print 'Database token';
				$sql="SELECT tms as token_date_last_update, entity from ".MAIN_DB_PREFIX."const where name = 'NETATMO_WEB_TOKEN' and value = '".$db->escape($conf->global->NETATMO_WEB_TOKEN)."'";
				$resql=$db->query($sql);
				//print $sql;
				if ($resql) {
					$obj=$db->fetch_object($resql);
					$token_date_last_update = $db->jdate($obj->token_date_last_update);
					$token_entity = $obj->entity;
					print ' - '.$langs->trans("DateCreation").'='.dol_print_date($token_date_last_update, 'dayhour').' - '.$langs->trans("Entity").'='.$token_entity;
				} else dol_print_error($db);
				print ':<br>';
				if (! empty($conf->global->NETATMO_WEB_TOKEN)) print '<div class="quatrevingtpercent" style="max-width: 800px; overflow: scroll; border: 1px solid #aaa;">'.$conf->global->NETATMO_WEB_TOKEN.'</div>';
				print '<br>';
				print 'Current session token:<br>';
				if (! empty($_SESSION['netatmo_web_token_'.$conf->entity])) print '<div class="quatrevingtpercent" style="max-width: 800px; overflow: scroll; border: 1px solid #aaa;">'.$_SESSION['netatmo_web_token_'.$conf->entity].'</div>';
				else print $langs->trans("None");
				print '<br>';
				print '<br>';
				print $langs->trans("RequestAccess").'<br>';
				print '<a href="'.$completeoauthurl.'">'.$langs->trans("OAuthSetupForLogin").'</a>';
				print '<br><br>';
				print $langs->trans("DeleteAccess").'<br>';
				print '<a href="https://dev.netatmo.com/myaccount/" target="_blank" rel="noopener">Go on your NetAtmo account</a>';
				/*print '<br><br>';
				print $langs->trans("NetatmoDeleteAuthorization").'<br>';
				print '<a href="https://security.google.com/settings/security/permissions" target="_blank">https://security.google.com/settings/security/permissions</a>';
				*/
			} else {
				print img_warning().' '.$langs->trans("NoTokenYet").'<br>';
				print '<a href="'.$completeoauthurl.'">'.$langs->trans("OAuthSetupForLogin").'</a>';
			}
		}
		print '</td>';
		print '</tr>';

		print '</table>';

		print '<div class="tabsAction">';
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit">'.$langs->trans("Modify").'</a>';
		print '</div>';
	} else {
		print '<br>'.$langs->trans("NothingToSetup");
	}
}



$message = '';
//$url='<a href="'.dol_buildpath('/netatmo/public/netatmo.php', 3).'?key='.($conf->global->NETATMO_SECURITY_KEY?urlencode($conf->global->NETATMO_SECURITY_KEY):'...').'" target="_blank">';
$url = dol_buildpath('/netatmo/public/netatmo.php', 3).'?key='.($conf->global->NETATMO_SECURITY_KEY?urlencode($conf->global->NETATMO_SECURITY_KEY):'KEYNOTDEFINED');
//$url .= '</a>';
$message .= img_picto('', 'object_globe.png').' '.$langs->trans("EndPointOfNetatmoServer");
$message .= '<div class="urllink"><input type="text" id="onlinepaymenturl" class="quatrevingtpercentminusx" value="'.$url.'"></div>';

print $message;


// Page end
dol_fiche_end();

llxFooter();
$db->close();
