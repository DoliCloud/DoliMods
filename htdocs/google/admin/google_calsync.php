<?php
/* Copyright (C) 2008-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *
 * Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/
 * Tutorial: http://www.ibm.com/developerworks/library/x-phpgooglecontact/index.html
 * Tutorial: https://developers.google.com/google-apps/contacts/v3/
 */

/**
 *	    \file       htdocs/google/admin/google_calsync.php
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
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

dol_include_once("/google/lib/google.lib.php");
dol_include_once('/google/lib/google_calendar.lib.php');


// Define $max, $maxgoogle and $notolderforsync
$max=(empty($conf->global->GOOGLE_MAX_FOR_MASS_AGENDA_SYNC)?50:$conf->global->GOOGLE_MAX_FOR_MASS_AGENDA_SYNC);
$maxgoogle=2500;
$notolderforsync=(empty($conf->global->GOOGLE_MAXOLDDAYS_FOR_MASS_AGENDA_SYNC)?10:$conf->global->GOOGLE_MAXOLDDAYS_FOR_MASS_AGENDA_SYNC);
$testoffset=3600;

$dateminsync=dol_mktime(GETPOST('synchour'), GETPOST('syncmin'), 0, GETPOST('syncmonth'), GETPOST('syncday'), GETPOST('syncyear'));
//print dol_print_date($dateminsync, 'dayhour');

if (!$user->admin) accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$action=GETPOST("action");


if (!getDolGlobalString('GOOGLE_AGENDA_NB')) {
	$conf->global->GOOGLE_AGENDA_NB=5;
}
$MAXAGENDA = getDolGlobalInt('GOOGLE_AGENDA_NB', 5);

// List of Google colors (A lot of colors are ignored by Google)
$colorlist=array('7A367A','B1365F','5229A3','7A367A','29527A','2952A3','1B887A','28754E','0D7813','528800','88880E','AB8B00',
'BE6D00','865A5A','705770','4E5D6C','5A6986','6E6E41','8D6F47','691426','5C1158','125A12','875509','754916',
'5B123B','42104A','113F47','333333','711616','FFFFFF');

$error = 0;


/*
 * Actions
 */

if ($action == 'save') {
	$db->begin();

	//print 'color='.$color;
	$res=dolibarr_set_const($db, 'GOOGLE_LOGIN', trim($_POST["GOOGLE_LOGIN"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_DUPLICATE_INTO_GCAL', trim($_POST["GOOGLE_DUPLICATE_INTO_GCAL"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_API_SERVICEACCOUNT_CLIENT_ID', trim($_POST["GOOGLE_API_SERVICEACCOUNT_CLIENT_ID"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_API_SERVICEACCOUNT_EMAIL', trim($_POST["GOOGLE_API_SERVICEACCOUNT_EMAIL"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE', trim($_POST["GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT', trim($_POST["GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_CAL_TZ_FIX', trim($_POST["GOOGLE_CAL_TZ_FIX"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_CAL_TZ_FIX_G2D', trim($_POST["GOOGLE_CAL_TZ_FIX_G2D"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_INCLUDE_AUTO_EVENT', trim($_POST["GOOGLE_INCLUDE_AUTO_EVENT"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db, 'GOOGLE_INCLUDE_ATTENDEES', trim($_POST["GOOGLE_INCLUDE_ATTENDEES"]), 'chaine', 0, '', $conf->entity);
	if (! $res > 0) $error++;

	if (! empty($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['tmp_name'])) {
		$dir     = $conf->google->multidir_output[$conf->entity]."/";
		$file_OK = is_uploaded_file($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['tmp_name']);
		if ($file_OK) {
			dol_mkdir($dir);

			if (@is_dir($dir)) {
				$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['name']);
				$result = dol_move_uploaded_file($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['tmp_name'], $newfile, 1);
				if (! ($result > 0)) {
					$errors[] = "ErrorFailedToSaveFile";
					$error++;
				} else {
					$res=dolibarr_set_const($db, 'GOOGLE_API_SERVICEACCOUNT_P12KEY', trim($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['name']), 'chaine', 0, '', $conf->entity);
				}
			} else {
				$errors[] = "ErrorFailedToCreateDir";
				$error++;
			}
		} else {
			$error++;
			switch ($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['error']) {
				case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
				case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$errors[] = "ErrorFileSizeTooLarge";
					break;
				case 3: //uploaded file was only partially uploaded
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
			}
		}
	}

	if (! $error) {
		$db->commit();
		setEventMessage($langs->trans("SetupSaved"));
	} else {
		$db->rollback();
		setEventMessage($errors, 'errors');
	}
}

// This is a test action to allow to test creation of event once synchro with Calendar has been enabled.
if (preg_match('/^test/', $action)) {
	include_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

	$object=new ActionComm($db);
	$result=$object->initAsSpecimen();
	$object->label='Test Dolibar-Google - Label';

	$tmparray=dol_getdate(dol_now());
	$object->datep=dol_mktime(12, 0, 0, $tmparray['mon'], $tmparray['mday'], $tmparray['year']);
	$object->datef=$object->datep;

	$tmpcontact=new Contact($db);
	$tmpcontact->initAsSpecimen();
	$object->contact=$tmpcontact;
	$object->contact_id=$tmpcontact->id;

	if ($tmpcontact->socid > 0) {
		$tmpsoc=new Societe($db);
		$tmpsoc->fetch($tmpcontact->socid);	// Overwrite with value of an existing record
		$object->societe=$tmpsoc;
		$object->thirdparty=$tmpsoc;
	}

	if (getDolGlobalString('GOOGLE_INCLUDE_ATTENDEES')) {
		$idofotheruser = 0;
		//$idofotheruser = 18;		// Add id of another user here to allow test with attendees
		if ($idofotheruser > 0 && $idofotheruser != $user->id) {
			$object->userassigned[$idofotheruser] = array('id'=>$idofotheruser, 'transparency'=> 1);
		}
	}

	$result=$object->create($user);
	if ($result < 0) $error++;

	if (! $error) {
		$object->label = 'Test Dolibar-Google - New label';
		$object->location = 'New location';
		$object->note = "New 'public' note";
		$object->note_public = "New 'public' note";
		//$object->datep+=$testoffset;
		//$object->datef+=$testoffset;

		$result=$object->update($user);
		if ($result < 0) $error++;
	}

	if ($action == 'testall' && ! $error) {
		if ((float) DOL_VERSION < 20) {
			$result = $object->delete();
		} else {
			$result = $object->delete($user);
		}
		if ($result < 0) $error++;
	}

	if (! $error) {
		setEventMessage($langs->trans("TestSuccessfull"));
	} else {
		if ($object->errors) setEventMessage($object->errors, 'errors');
		else setEventMessage($object->error, 'errors');
	}
}


if (GETPOST('cleanup')) {
	$error=0;
	$nbdeleted=0;

	$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/" . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY');
	$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session, 'service');

	if (! is_array($servicearray)) {
		$errors[]=$servicearray;
		$error++;
	}

	if ($servicearray == null) {
		$txterror="Failed to login to Google with credentials provided into setup page " . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_EMAIL').", ".$key_file_location;
		dol_syslog($txterror, LOG_ERR);
		$errors[]=$txterror;
		$error++;
	}

	if (! $error) {
		try {
			$service = new Google_Service_Calendar($servicearray['client']);
			$events = $service->events->listEvents($userlogin);
			while (true) {
				foreach ($events->getItems() as $event) {
					$dolibarr_id='';
					$extendedProperties=$event->getExtendedProperties();
					if (is_object($extendedProperties)) {
						$shared=$extendedProperties->getShared();	// Was set by old version of module Google
						$priv=$extendedProperties->getPrivate();	// Private property dolibarr_id is set during google create. Not modified by update.
						$dolibarr_id=($priv['dolibarr_id']?$priv['dolibarr_id']:$shared['dol_id']);
					}
					if ($dolibarr_id) {
						//echo 'This is a dolibarr event '.$dolibarr_id.' - '.$event->getSummary().'<br>'."\n";
						deleteEventById($servicearray['client'], $event->getId(), $userlogin, $service);
						$nbdeleted++;
					}
				}
				$pageToken = $events->getNextPageToken();
				if ($pageToken) {
					$optParams = array('pageToken' => $pageToken);
					$events = $service->events->listEvents($userlogin, $optParams);
				} else {
					break;
				}
			}
		} catch (Exception $e) {
			$errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	setEventMessage($langs->trans("XRecordDeleted", $nbdeleted), 'mesgs');
	if ($error) {
		setEventMessage($errors, 'errors');
	}
}

if ($action == 'pushallevents') {
	$error=0;
	$nbinserted=0;

	$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/" . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY');
	$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session, 'service');

	if (! is_array($servicearray)) {
		$errors[]=$servicearray;
		$error++;
	}

	if ($servicearray == null) {
		$txterror="Failed to login to Google with credentials provided into setup page " . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_EMAIL').", ".$key_file_location;
		dol_syslog($txterror, LOG_ERR);
		$errors[]=$txterror;
		$error++;
	}

	if (! $error) {
		try {
			$service = new Google_Service_Calendar($servicearray['client']);

			// Search all events
			$sql = 'SELECT id, datep, datep2 as datef, code, label, transparency, priority, fulldayevent, percent, location, fk_soc, fk_contact, note as note_private';
			$sql.= ' FROM '.MAIN_DB_PREFIX.'actioncomm';
			$sql.=$db->order('datep', 'DESC');
			$sql.=$db->plimit($max);

			$resql = $db->query($sql);
			if (! $resql) {
				dol_print_error($db);
				exit;
			}
			$synclimit = 0;	// 0 = all
			$i=0;
			while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit))) {
				$objecttmp = new ActionComm($db);
				$objecttmp->id=$obj->id;
				$objecttmp->datep=$db->jdate($obj->datep);
				$objecttmp->datef=$db->jdate($obj->datef);
				$objecttmp->code=$obj->code;
				$objecttmp->label=$obj->label;
				$objecttmp->transparency=$obj->transparency;
				$objecttmp->priority=$obj->priority;
				$objecttmp->fulldayevent=$obj->fulldayevent;
				$objecttmp->percent=$obj->percent;
				$objecttmp->location=$obj->location;
				$objecttmp->socid=$obj->fk_soc;
				$objecttmp->contactid=$obj->fk_contact;
				$objecttmp->contact_id=$obj->fk_contact;
				$objecttmp->note=$obj->note_private;
				$objecttmp->note_private=$obj->note_private;
				//$objecttmp->note_public=$obj->note_public;

				// Event label can now include company and / or contact info, see configuration
				google_complete_label_and_note($objecttmp, $langs);

				$ret = createEvent($servicearray, $objecttmp, $userlogin);
				if (! preg_match('/ERROR/', $ret)) {
					if (! preg_match('/google\.com/', $ret)) $ret='google:'.$ret;
					$objecttmp->update_ref_ext(substr($ret, 0, 255));	// This is to store ref_ext to allow updates
					$nbinserted++;
				} else {
					$errors[]=$ret;
					$error++;
				}

				$i++;
			}
		} catch (Exception $e) {
			$errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	setEventMessage($langs->trans("PushToGoogleSucess", $nbinserted), 'mesgs');
	if ($error) {
		setEventMessage($errors, 'errors');
	}
}

if ($action == 'syncfromgoogle') {
	$fuser = $user;		// $fuser = user for synch
	$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;

	if (empty($dateminsync)) {
		setEventMessage($langs->trans("ErrorBadValueForDate"), 'errors');
		$error++;
	}

	if (! $error) {
		$resarray = syncEventsFromGoogleCalendar($userlogin, $fuser, $dateminsync, $max);

		$errors=$resarray['errors'];
		$nbinserted=$resarray['nbinserted'];
		$nbupdated=$resarray['nbupdated'];
		$nbdeleted=$resarray['nbdeleted'];
		$nbalreadydeleted=$resarray['nbalreadydeleted'];

		if (! empty($errors)) {
			setEventMessage($errors, 'errors');
		} else {
			$langs->load("google@google");
			setEventMessage($langs->trans("GetFromGoogleSucess", ($nbinserted ? $nbinserted : '0'), ($nbupdated ? $nbupdated : '0'), ($nbdeleted ? $nbdeleted : '0')), 'mesgs');
			if ($nbalreadydeleted) setEventMessage($langs->trans("GetFromGoogleAlreadyDeleted", $nbalreadydeleted), 'mesgs');
		}
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


if (! function_exists("openssl_open")) print '<div class="warning">Warning: PHP Module \'openssl\' is not installed</div><br>';
if (! class_exists('DOMDocument')) print '<div class="warning">Warning: PHP Module \'xml\' is not installed</div><br>';


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="POST" autocomplete="off" enctype="multipart/form-data">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

$head=googleadmin_prepare_head();


dol_fiche_head($head, 'tabagendasync', '', -1);

print '<div class="fichecenter">';

if ($conf->use_javascript_ajax) {
	print "\n".'<script type="text/javascript" language="javascript">';
	print 'jQuery(document).ready(function () {
		function initfields()
		{
			if (jQuery("#GOOGLE_DUPLICATE_INTO_GCAL").val() > 0) jQuery(".synccal").show();
			else jQuery(".synccal").hide();
            if (jQuery("#GOOGLE_LOGIN").val() != "")
            {
                jQuery(".showifidagendaset").show();
            }
            else
            {
                jQuery(".showifidagendaset").hide();
            }
		}
		initfields();
		jQuery("#GOOGLE_DUPLICATE_INTO_GCAL").change(function() {
			initfields();
		});
		jQuery("#GOOGLE_LOGIN").keyup(function() {
			initfields();
		});
	})';
	print '</script>'."\n";
}

print $langs->trans("GoogleEnableSyncToCalendar").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_GCAL", GETPOSTISSET("GOOGLE_DUPLICATE_INTO_GCAL") ? GETPOST("GOOGLE_DUPLICATE_INTO_GCAL") : getDolGlobalString('GOOGLE_DUPLICATE_INTO_GCAL'), 1).'<br>';

$var=false;

print '<div class="synccal">';
print '<br>';

print '<table class="noborder centpercent">';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Google TZ fix Dolibarr -> Google
print '<tr class="oddeven">';
print '<td>'.$langs->trans("GOOGLE_FIX_TZ")."</td>";
print '<td class="nowraponall">';
print '<input class="flat" type="text" size="4" name="GOOGLE_CAL_TZ_FIX" value="'.getDolGlobalString('GOOGLE_CAL_TZ_FIX').'">';
print ' '.$form->textwithpicto('', $langs->trans("FillThisOnlyIfRequired"));
print "</td>";
print "</tr>";
// Google TZ fix Google -> Dolibarr
print '<tr class="oddeven">';
print '<td>'.$langs->trans("GOOGLE_FIX_TZ_G2D")."</td>";
print '<td class="nowraponall">';
print '<input class="flat" type="text" size="4" name="GOOGLE_CAL_TZ_FIX_G2D" value="'.getDolGlobalString('GOOGLE_CAL_TZ_FIX_G2D').'">';
print ' '.$form->textwithpicto('', $langs->trans("FillThisOnlyIfRequired"));
print "</td>";
print "</tr>";
// Include auto event
print '<tr class="oddeven">';
print '<td>'.$langs->trans("GOOGLE_INCLUDE_AUTO_EVENT")."</td>";
print "<td>";
print $form->selectyesno("GOOGLE_INCLUDE_AUTO_EVENT", getDolGlobalString('GOOGLE_INCLUDE_AUTO_EVENT'), 1);
print "</td>";
print "</tr>";
// Include attendees
print '<tr class="oddeven">';
print '<td>'.$langs->trans("GOOGLE_INCLUDE_ATTENDEES")."</td>";
print "<td>";
print $form->selectyesno("GOOGLE_INCLUDE_ATTENDEES", getDolGlobalString('GOOGLE_INCLUDE_ATTENDEES'), 1);
print "</td>";
print "</tr>";

print '</table>';


print '<br>';


print '<table class="noborder centpercent">'."\n";

print '<tr class="liste_titre">';
print '<td class="nowrap">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleAPIv3Usage", "Calendar").')'."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Note")."</td>";
print "</tr>";

print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_EMAIL")."</td>";
print '<td>';
print '<input class="flat minwidth400" type="text" name="GOOGLE_API_SERVICEACCOUNT_EMAIL" value="'.getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_EMAIL').'">';
print '</td>';
print '<td class="aaa">';
print $langs->trans("AllowGoogleToLoginWithServiceAccount", "https://console.developers.google.com/apis/credentials", "https://console.developers.google.com/apis/credentials").'<br>';
print '</td>';
print '</tr>';

// Google file (JSON or P12). JSON is now recommended.
print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_P12KEY")."</td>";
print '<td>';
if (getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY')) {
	print getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY');
	$pathtojsonfile = $conf->google->multidir_output[$conf->entity]."/" . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY');
	if (!dol_is_file($pathtojsonfile)) {
		$langs->load("errors");
		print ' '.img_warning($langs->trans("ErrorFileNotFound", $conf->google->multidir_output[$conf->entity]."/" . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY')));
	} else {
		print '<span class="opacitymedium small"> - '.dol_print_date(dol_filemtime($pathtojsonfile), 'dayhour', 'tzserver').' (server TZ)</span>';
	}
	print '<br>';
}
print '<input class="minwidth400" type="file" name="GOOGLE_API_SERVICEACCOUNT_P12KEY_file">';
print '</td>';
print '<td class="aaa">';
print $langs->trans("AllowGoogleToLoginWithServiceAccountP12", "https://console.developers.google.com/apis/credentials", "https://console.developers.google.com/apis/credentials").'<br>';
print '</td>';
print '</tr>';

// Google login
print '<tr class="oddeven">';
print '<td>'.$langs->trans("GoogleIDAgenda")."</td>";
print "<td>";
print '<input id="GOOGLE_LOGIN" class="flat minwidth300" type="text" size="24" name="GOOGLE_LOGIN" autocomplete="off" value="'.getDolGlobalString('GOOGLE_LOGIN').'">';
print "</td>";
print '<td class="aaa">';
print $langs->trans("Example").": yourlogin@gmail.com, email@mydomain.com<br>";
print '<br>';
print $langs->trans("GoogleSetupHelp").'<br>';
print '<br>';
print $langs->trans("KeepEmptyYoUseLoginPassOfEventUser").'<br>';
if (empty($conf->global->GOOGLE_LOGIN)) {
	print '<br>';
	//print '<u>'.$langs->trans("TargetUser").'</u>: ';
	if (empty($conf->global->GOOGLE_SYNC_EVENT_TO_SALE_REPRESENTATIVE)) {
		print $langs->trans("KeepEmptyYoUseLoginPassOfEventUserAssigned");
	} else {
		print $langs->trans("KeepEmptyYoUseLoginPassOfEventUserSaleRep");
	}
}
print '</td>';
print "</tr>";

print "</table>";

print info_admin($langs->trans("EnableAPI", "https://console.developers.google.com/apis/library/", "https://console.developers.google.com/apis/library/", "Calendar API"), 0, 0, '1', 'showifidagendaset');

$htmltext = $langs->trans("ShareCalendarWithServiceAccount", $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $langs->transnoentitiesnoconv("GoogleIDAgenda"));
$htmltext .= '<br>';
$htmltext .= $langs->trans("ShareCalendarWithServiceAccount2");
print info_admin($htmltext, 0, 0, '1', 'showifidagendaset');


print '</div>';

print '</div>';

dol_fiche_end();

print '<div align="center">';
print '<input type="submit" name="save" class="button" value="'.$langs->trans("Save").'">';
print '</div>';

print "</form>\n";

print '<br><br><br>';


// Test area

print '<div class="tabsActions">';

print '<div class="synccal showifidagendaset">';
if (empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL) || empty($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL) || empty($conf->global->GOOGLE_LOGIN)) {
	// We do not show test buttons
	if (!empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) {
		print '<a class="butActionRefused small" href="#">'.$langs->trans("TestCreateUpdateDelete")."</a>";
		print '<a class="butActionRefused small" href="#">'.$langs->trans("TestCreateUpdate")."</a>";
	}
} else {
	print '<a class="butAction small" href="'.$_SERVER['PHP_SELF'].'?action=testall">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	//print '<a class="butAction" title="Make a record at current date + '.$testoffset.'s" href="'.$_SERVER['PHP_SELF'].'?action=testcreate">'.$langs->trans("TestCreateUpdate")."</a>";
	print '<a class="butAction small" title="Make a record at 12:00" href="'.$_SERVER['PHP_SELF'].'?action=testcreate">'.$langs->trans("TestCreateUpdate")."</a>";
}
print '</div>';

print '</div>';

print '<br>';


print '<div class="synccal showifidagendaset">';

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) {
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="pushallevents">';
	print $langs->trans("ExportEventsToGoogle", $max, getDolGlobalString('GOOGLE_LOGIN'))." ";
	print '<input type="submit" name="pushall" class="button small" value="'.$langs->trans("Run").'"';
	if (empty($conf->global->GOOGLE_LOGIN)) print ' disabled="disabled"';
	print '>';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="deleteallevents">';
	print $langs->trans("DeleteAllGoogleEvents", getDolGlobalString('GOOGLE_LOGIN'))." ";
	print '('.$langs->trans("OperationMayBeLong").') ';
	print '<input type="submit" name="cleanup" class="button small" value="'.$langs->trans("Run").'"';
	if (empty($conf->global->GOOGLE_LOGIN)) print ' disabled="disabled"';
	print '>';
	print "</form>\n";
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) {
	if (versioncompare(versiondolibarrarray(), array(3,7,2)) >= 0) {
		print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="syncfromgoogle">';
		print $langs->trans("ImportEventsFromGoogle", $max, getDolGlobalString('GOOGLE_LOGIN'))." ";
		$now = dol_now() - ($notolderforsync * 24 * 3600);
		print $form->selectDate($dateminsync ? $dateminsync : $now, 'sync', 1, 1, 0, '', 1, 0, empty($conf->global->GOOGLE_LOGIN)?1:0);
		print '<input type="submit" name="getall" class="button small" value="'.$langs->trans("Run").'"';
		if (empty($conf->global->GOOGLE_LOGIN)) print ' disabled="disabled"';
		print '>';
		print "</form>\n";
	}
}

print '</div>';

llxFooter();

if (is_object($db)) $db->close();
