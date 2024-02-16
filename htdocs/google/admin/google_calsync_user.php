<?php
/* Copyright (C) 2005-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012 Regis Houssin        <regis@dolibarr.fr>
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
 *       \file       htdocs/google/admin/google_calsync_user.php
 *       \brief      Page to show user setup for display
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

require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/usergroups.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php";
require_once DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
dol_include_once("/google/lib/google.lib.php");
dol_include_once('/google/lib/google_calendar.lib.php');


// Define $max, $maxgoogle and $notolderforsync
$max=(empty($conf->global->GOOGLE_MAX_FOR_MASS_AGENDA_SYNC)?50:$conf->global->GOOGLE_MAX_FOR_MASS_AGENDA_SYNC);
$maxgoogle=2500;
$notolderforsync=(empty($conf->global->GOOGLE_MAXOLDDAYS_FOR_MASS_AGENDA_SYNC)?10:$conf->global->GOOGLE_MAXOLDDAYS_FOR_MASS_AGENDA_SYNC);
$testoffset=3600;

$dateminsync=dol_mktime(GETPOST('synchour'), GETPOST('syncmin'), 0, GETPOST('syncmonth'), GETPOST('syncday'), GETPOST('syncyear'), 0);
//print dol_print_date($dateminsync, 'dayhour');

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

// Defini si peux lire/modifier permisssions
$canreaduser=($user->admin || $user->rights->user->user->lire);

$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');

if ($id) {
	// $user est le user qui edite, $id est l'id de l'utilisateur edite
	$caneditfield=((($user->id == $id) && $user->rights->user->self->creer)
	|| (($user->id != $id) && $user->rights->user->user->creer));
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
$feature2 = (($socid && $user->rights->user->self->creer)?'':'user');
if ($user->id == $id) {	// A user can always read its own card
	$feature2='';
	$canreaduser=1;
}
$result = restrictedArea($user, 'user', $id, 'user&user', $feature2);
if ($user->id <> $id && ! $canreaduser) accessforbidden();

$dirtop = "../core/menus/standard";
$dirleft = "../core/menus/standard";

// Charge utilisateur edite
$object = new User($db);
$result=$object->fetch($id, '', '', 1);
if ($result < 0) dol_print_error('', $object->error);
$object->getrights();

// Liste des zone de recherche permanentes supportees
$searchform=array("main_searchform_societe","main_searchform_contact","main_searchform_produitservice");
$searchformconst=array($object->conf->MAIN_SEARCHFORM_SOCIETE,$object->conf->MAIN_SEARCHFORM_CONTACT,$object->conf->MAIN_SEARCHFORM_PRODUITSERVICE);
$searchformtitle=array($langs->trans("Companies"),$langs->trans("Contacts"),$langs->trans("ProductsAndServices"));

$form = new Form($db);
$formadmin=new FormAdmin($db);


/*
 * Actions
 */

if ($action == 'save' && ($caneditfield  || $user->admin)) {
	if (! GETPOST('cancel', 'alpha')) {
		$tabparam=array();

		$tabparam["GOOGLE_DUPLICATE_INTO_GCAL"]=$_POST["GOOGLE_DUPLICATE_INTO_GCAL"];
		$tabparam["GOOGLE_LOGIN"]=$_POST["GOOGLE_LOGIN"];
		$tabparam["GOOGLE_PASSWORD"]=$_POST["GOOGLE_PASSWORD"];

		$result=dol_set_user_param($db, $conf, $object, $tabparam);

		$_SESSION["mainmenu"]="";   // Le gestionnaire de menu a pu changer

		setEventMessage($langs->trans("RecordModifiedSuccessfully"));
	}
}


// This is a test action to allow to test creation of event once synchro with Calendar has been enabled.
if (preg_match('/^test/', $action)) {
	include_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
	include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

	$objectfortest=new ActionComm($db);
	$result=$objectfortest->initAsSpecimen();

	$tmparray=dol_getdate(dol_now());
	$objectfortest->datep=dol_mktime(12, 0, 0, $tmparray['mon'], $tmparray['mday'], $tmparray['year']);
	$objectfortest->datef=$objectfortest->datep;

	$tmpcontact=new Contact($db);
	$tmpcontact->initAsSpecimen();
	$objectfortest->contact=$tmpcontact;

	if ($tmpcontact->socid > 0) {
		$tmpsoc=new Societe($db);
		$tmpsoc->fetch($tmpcontact->socid);	// Overwrite with value of an existing record
		$objectfortest->societe=$tmpsoc;
		$objectfortest->thirdparty=$tmpsoc;
	}

	$result=$objectfortest->create($user);
	if ($result < 0) $error++;

	if (! $error) {
		$objectfortest->label = 'New label';
		$objectfortest->location = 'New location';
		$objectfortest->note = "New 'public' note";
		$objectfortest->note_public = "New 'public' note";
		//$objectfortest->datep+=$testoffset;
		//$objectfortest->datef+=$testoffset;

		$result=$objectfortest->update($user);
		if ($result < 0) $error++;
	}

	if ($action == 'testall' && ! $error) {
		$result=$objectfortest->delete();
		if ($result < 0) $error++;
	}

	if (! $error) {
		setEventMessage($langs->trans("TestSuccessfull"));
	} else {
		if ($objectfortest->errors) setEventMessage($objectfortest->errors, 'errors');
		else setEventMessage($objectfortest->error, 'errors');
	}
}


if (GETPOST('cleanup')) {
	$error=0;
	$nbdeleted=0;

	$userlogin = empty($object->conf->GOOGLE_LOGIN)?'':$object->conf->GOOGLE_LOGIN;

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
						$shared=$extendedProperties->getShared();
						$priv=$extendedProperties->getPrivate();
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

	$userlogin = empty($object->conf->GOOGLE_LOGIN)?'':$object->conf->GOOGLE_LOGIN;

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
		$error++;
	}

	if (! $error) {
		try {
			$service = new Google_Service_Calendar($servicearray['client']);

			// Search all events
			$sql = 'SELECT id, datep, datep2 as datef, code, label, transparency, priority, fulldayevent, percent, location, fk_soc, fk_contact, note';
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
				$objecttmp->note=$obj->note;
				$objecttmp->note_public=$obj->note_public;

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

// Import last 50 modified events
if ($action == 'syncfromgoogle') {
	$error=0;

	//$object = $user;		// $object = user for synch
	$userlogin = empty($object->conf->GOOGLE_LOGIN)?'':$object->conf->GOOGLE_LOGIN;

	if (empty($dateminsync)) {
		setEventMessage($langs->trans("ErrorBadValueForDate"), 'errors');
		$error++;
	}

	if (! $error) {
		$resarray = syncEventsFromGoogleCalendar($userlogin, $object, $dateminsync, $max);

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

llxHeader();

$head = user_prepare_head($object);


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post" autocomplete="off">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';
print '<input type="hidden" name="id" value="'.$id.'">';


$title = $langs->trans("User");
dol_fiche_head($head, 'gsetup', '', -1, 'user');

dol_banner_tab($object, 'id', '', $user->hasRight('user', 'user', 'lire') || $user->admin);

print '<div class="underbanner clearboth"></div>';

print '<br>';

$userlogin = $conf->global->GOOGLE_LOGIN;

if (! empty($userlogin)) {	// We use setup of user
	print $langs->trans("GoogleSetupIsGlobal", $userlogin);
} else {
	print_fiche_titre($langs->trans("AgendaSync"), '', '');

	$var=true;
	print "<table class=\"noborder\" width=\"100%\">";

	print "<tr class=\"liste_titre\">";
	print '<td width="25%">'.$langs->trans("Parameter").'</td>';
	print '<td colspan="2">'.$langs->trans("Value").'</td>';
	print "</tr>";

	// Activation synchronisation
	/*
	print "<tr ".$bc[$var].">";
	print "<td>".$langs->trans("GoogleEnableSyncToCalendar")."</td>";
	print "<td>";
	print $form->selectyesno("GOOGLE_DUPLICATE_INTO_GCAL",isset($_POST["GOOGLE_DUPLICATE_INTO_GCAL"])?$_POST["GOOGLE_DUPLICATE_INTO_GCAL"]:$object->conf->GOOGLE_DUPLICATE_INTO_GCAL,1);
	print "</td>";
	print "</tr>";
	*/
	// Google login
	print '<tr class="oddeven">';
	print '<td class="fieldrequired">'.$langs->trans("GoogleIDAgenda")."</td>";
	print "<td>";
	if (! empty($conf->global->GOOGLE_LOGIN)) print $conf->global->GOOGLE_LOGIN;
	else print '<input class="flat" type="text" size="30" name="GOOGLE_LOGIN" value="'.$object->conf->GOOGLE_LOGIN.'">';
	print "</td>";
	print '<td>';
	print $langs->trans("Example").": yourlogin@gmail.com, email@mydomain.com<br>";
	print $langs->trans("GoogleSetupHelp");
	print '</td>';
	print "</tr>";

	print '<tr class="oddeven">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_EMAIL")."</td>";
	print '<td>';
	print '<input class="flat" type="text" size="90" name="GOOGLE_API_SERVICEACCOUNT_EMAIL" value="' . getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_EMAIL').'" disabled="disabled">';
	print '</td>';
	print '<td>';
	print $langs->trans("ThisFieldIsAGlobalSetup").'<br>';
	//print $langs->trans("AllowGoogleToLoginWithServiceAccount","https://console.developers.google.com/apis/credentials","https://console.developers.google.com/apis/credentials").'<br>';
	print '</td>';
	print '</tr>';

	print '<tr lass="oddeven">';
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_P12KEY")."</td>";
	print '<td>';
	if (! empty($conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY)) print getDolGlobalString('GOOGLE_API_SERVICEACCOUNT_P12KEY') . '<br>';
	//print '<input type="file" name="GOOGLE_API_SERVICEACCOUNT_P12KEY_file">';
	print '</td>';
	print '<td>';
	print $langs->trans("ThisFieldIsAGlobalSetup").'<br>';
	//print $langs->trans("AllowGoogleToLoginWithServiceAccountP12","https://console.developers.google.com/apis/credentials","https://console.developers.google.com/apis/credentials").'<br>';
	print '</td>';
	print '</tr>';

	/* Done by default
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print "<td>".$langs->trans("GOOGLE_EVENT_LABEL_INC_SOCIETE")."<br /></td>";
	print "<td>";
	print $form->selectyesno("GOOGLE_EVENT_LABEL_INC_SOCIETE",isset($_POST["GOOGLE_EVENT_LABEL_INC_SOCIETE"])?$_POST["GOOGLE_EVENT_LABEL_INC_SOCIETE"]:$object->conf->GOOGLE_EVENT_LABEL_INC_SOCIETE,1);
	print "</td>";
	print "</tr>";
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print "<td>".$langs->trans("GOOGLE_EVENT_LABEL_INC_CONTACT")."<br /></td>";
	print "<td>";
	print $form->selectyesno("GOOGLE_EVENT_LABEL_INC_CONTACT",isset($_POST["GOOGLE_EVENT_LABEL_INC_CONTACT"])?$_POST["GOOGLE_EVENT_LABEL_INC_CONTACT"]:$object->conf->GOOGLE_EVENT_LABEL_INC_CONTACT,1);
	print "</td>";
	print "</tr>";
	*/

	print "</table>";

	print '<br>';

	print info_admin($langs->trans("EnableAPI", "https://console.developers.google.com/apis/library/", "https://console.developers.google.com/apis/library/", "Calendar API"));

	$htmltext = $langs->trans("ShareCalendarWithServiceAccount", $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $langs->transnoentitiesnoconv("GoogleIDAgenda"));
	$htmltext .= '<br>';
	$htmltext .= $langs->trans("ShareCalendarWithServiceAccount2");
	print info_admin($htmltext, 0, 0, '1', 'showifidagendaset');
}

dol_fiche_end();


if (empty($userlogin)) {	// We use setup of user
	print '<div class="center">';
	//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
	//print "&nbsp; &nbsp;";
	print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
	print "</div>";
}


print "</form>\n";

print '<br><br>';


// Test area

if (empty($userlogin)) {	// We use setup of user
	print '<div class="tabsActions">';

	print '<div class="synccal">';
	if (empty($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL) || empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL) || empty($object->conf->GOOGLE_LOGIN)) {
		print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")."</a>";

		print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreate")."</a>";
	} else {
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testall&id='.$id.'">'.$langs->trans("TestCreateUpdateDelete")."</a>";

		//print '<a class="butAction" title="Make a record at current date + '.$testoffset.'s" href="'.$_SERVER['PHP_SELF'].'?action=testcreate&id='.$id.'">'.$langs->trans("TestCreateUpdate")."</a>";
		print '<a class="butAction" title="Make a record at 12:00" href="'.$_SERVER['PHP_SELF'].'?action=testcreate&id='.$id.'">'.$langs->trans("TestCreateUpdate")."</a>";
	}
	print '</div>';

	print '</div>';

	print '<br>';


	print '<div class="synccal">';

	if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) {
		print '<br>';
		print '<br>';

		print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		print '<input type="hidden" name="token" value="'.newToken().'">';

		print '<input type="hidden" name="action" value="pushallevents">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print $langs->trans("ExportEventsToGoogle", $max, $object->conf->GOOGLE_LOGIN)." ";
		print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'"';
		if (empty($object->conf->GOOGLE_LOGIN)) print ' disabled="disabled"';
		print '>';
		print "</form>\n";
	}

	if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) {
		print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		print '<input type="hidden" name="action" value="deleteallevents">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print $langs->trans("DeleteAllGoogleEvents", $object->conf->GOOGLE_LOGIN)." ";
		print '('.$langs->trans("OperationMayBeLong").') ';
		print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'"';
		if (empty($object->conf->GOOGLE_LOGIN)) print ' disabled="disabled"';
		print '>';
		print "</form>\n";
	}

	if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL)) {
		if (versioncompare(versiondolibarrarray(), array(3,7,2)) >= 0) {
			print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
			print '<input type="hidden" name="token" value="'.newToken().'">';
			print '<input type="hidden" name="action" value="syncfromgoogle">';
			print '<input type="hidden" name="id" value="'.$id.'">';
			print $langs->trans("ImportEventsFromGoogle", $max, $object->conf->GOOGLE_LOGIN)." ";
			$now = dol_now() - ($notolderforsync * 24 * 3600);
			print $form->selectDate($dateminsync ? $dateminsync : $now, 'sync', 1, 1, 0, '', 1, 0, empty($object->conf->GOOGLE_LOGIN)?1:0);
			print '<input type="submit" name="getall" class="button" value="'.$langs->trans("Run").'"';
			if (empty($object->conf->GOOGLE_LOGIN)) print ' disabled="disabled"';
			print '>';
			print "</form>\n";
		}
	}

	print '</div>';
}


llxFooter();
$db->close();
