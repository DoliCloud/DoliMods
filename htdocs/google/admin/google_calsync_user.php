<?php
/* Copyright (C) 2005-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && @file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/(?:custom|nltechno)([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/usergroups.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
dol_include_once("/google/lib/google.lib.php");
dol_include_once('/google/lib/google_calendar.lib.php');

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

// Defini si peux lire/modifier permisssions
$canreaduser=($user->admin || $user->rights->user->user->lire);

$id = GETPOST('id','int');
$action = GETPOST('action','alpha');

if ($id)
{
    // $user est le user qui edite, $id est l'id de l'utilisateur edite
    $caneditfield=((($user->id == $id) && $user->rights->user->self->creer)
    || (($user->id != $id) && $user->rights->user->user->creer));
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
$feature2 = (($socid && $user->rights->user->self->creer)?'':'user');
if ($user->id == $id)	// A user can always read its own card
{
    $feature2='';
    $canreaduser=1;
}
$result = restrictedArea($user, 'user', $id, 'user&user', $feature2);
if ($user->id <> $id && ! $canreaduser) accessforbidden();

$dirtop = "../core/menus/standard";
$dirleft = "../core/menus/standard";

// Charge utilisateur edite
$fuser = new User($db);
$result=$fuser->fetch($id);
if ($result < 0) dol_print_error('',$fuser->error);
$fuser->getrights();

// Liste des zone de recherche permanentes supportees
$searchform=array("main_searchform_societe","main_searchform_contact","main_searchform_produitservice");
$searchformconst=array($fuser->conf->MAIN_SEARCHFORM_SOCIETE,$fuser->conf->MAIN_SEARCHFORM_CONTACT,$fuser->conf->MAIN_SEARCHFORM_PRODUITSERVICE);
$searchformtitle=array($langs->trans("Companies"),$langs->trans("Contacts"),$langs->trans("ProductsAndServices"));

$form = new Form($db);
$formadmin=new FormAdmin($db);


/*
 * Actions
*/
if ($action == 'save' && ($caneditfield  || $user->admin))
{
    if (! GETPOST("cancel"))
    {
        $tabparam=array();

        $tabparam["GOOGLE_DUPLICATE_INTO_GCAL"]=$_POST["GOOGLE_DUPLICATE_INTO_GCAL"];
        $tabparam["GOOGLE_LOGIN"]=$_POST["GOOGLE_LOGIN"];
        $tabparam["GOOGLE_PASSWORD"]=$_POST["GOOGLE_PASSWORD"];

        $result=dol_set_user_param($db, $conf, $fuser, $tabparam);

        $_SESSION["mainmenu"]="";   // Le gestionnaire de menu a pu changer

        setEventMessage($langs->trans("RecordModifiedSuccessfully"));
    }
}


// This is a test action to allow to test creation of event once synchro with Calendar has been enabled.
if (preg_match('/^test/',$action))
{
	include_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');
	include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
	include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');

	$object=new ActionComm($db);
	$result=$object->initAsSpecimen();

	$tmpcontact=new Contact($db);
	$tmpcontact->initAsSpecimen();
	$object->contact=$tmpcontact;

	if ($tmpcontact->socid > 0)
	{
		$tmpsoc=new Societe($db);
		$tmpsoc->fetch($tmpcontact->socid);	// Overwrite with value of an existing record
		$object->societe=$tmpsoc;
		$object->thirdparty=$tmpsoc;
	}

	$result=$object->add($user);
	if ($result < 0) $error++;

	if (! $error)
	{
		$object->label='New label';
		$object->location='New location';
		$object->note='New note';
		$object->datep+=3600;
		$object->datef+=3600;

		$result=$object->update($user);
		if ($result < 0) $error++;
	}

	if ($action == 'testall' && ! $error)
	{
		$result=$object->delete();
		if ($result < 0) $error++;
	}

	if (! $error)
	{
		setEventMessage($langs->trans("TestSuccessfull"));
	}
	else
	{
		if ($object->errors) setEventMessage($object->errors,'errors');
		else setEventMessage($object->error,'errors');
	}
}


if (GETPOST('cleanup'))
{
	$nbdeleted=0;

	$userlogin = empty($fuser->conf->GOOGLE_LOGIN)?'':$fuser->conf->GOOGLE_LOGIN;

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
	$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID, $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session);

	if (! is_array($servicearray))
	{
		$this->errors[]=$servicearray;
		$error++;
	}

	if ($servicearray == null)
	{
		$this->error="Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID.", ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
		dol_syslog($this->error, LOG_ERR);
		$this->errors[]=$this->error;
		$error++;
	}
	else
	{
		try {
			$service = new Google_Service_Calendar($servicearray['client']);
			$events = $service->events->listEvents($userlogin);
			while(true)
			{
				foreach ($events->getItems() as $event)
				{
					$dolibarr_id='';
					$extendedProperties=$event->getExtendedProperties();
					if (is_object($extendedProperties))
					{
						$shared=$extendedProperties->getShared();
						$priv=$extendedProperties->getPrivate();
						$dolibarr_id=($priv['dolibarr_id']?$priv['dolibarr_id']:$shared['dol_id']);
					}
					if ($dolibarr_id)
					{
						//echo 'This is a dolibarr event '.$dolibarr_id.' - '.$event->getSummary().'<br>'."\n";
						deleteEventById($servicearray['client'], $event->getId(), $userlogin, $service);
						$nbdeleted++;
					}
				}
				$pageToken = $events->getNextPageToken();
				if ($pageToken)
				{
					$optParams = array('pageToken' => $pageToken);
					$events = $service->events->listEvents($userlogin, $optParams);
				}
				else
				{
					break;
				}
			}
		}
		catch(Exception $e)
		{
			$this->errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	if ($error)
	{
		setEventMessage($this->errors, 'errors');
	}
	else
	{
		setEventMessage($langs->trans("XRecordDeleted",$nbdeleted), 'mesgs');
	}
}

if ($action == 'pushallevents')
{
	$nbinserted=0;

	$userlogin = empty($fuser->conf->GOOGLE_LOGIN)?'':$fuser->conf->GOOGLE_LOGIN;

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
	$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID, $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session);

	if (! is_array($servicearray))
	{
		$this->errors[]=$servicearray;
		$error++;
	}

	if ($servicearray == null)
	{
		$this->error="Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID.", ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
		dol_syslog($this->error, LOG_ERR);
		$this->errors[]=$this->error;
		$error++;
	}
	else
	{
		try {
			$service = new Google_Service_Calendar($servicearray['client']);

			// Search all events
			$sql = 'SELECT id, datep, datep2 as datef, code, label, transparency, priority, fulldayevent, punctual, percent, location, fk_soc, fk_contact, note';
			$sql.= ' FROM '.MAIN_DB_PREFIX.'actioncomm';
			$sql.=$db->order('datep','DESC');
			$sql.=$db->plimit($max);

			$resql = $db->query($sql);
			if (! $resql)
			{
				dol_print_error($db);
				exit;
			}
			$synclimit = 0;	// 0 = all
			$i=0;
			while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
			{
				$object = new ActionComm($db);
				$object->id=$obj->id;
				$object->datep=$db->jdate($obj->datep);
				$object->datef=$db->jdate($obj->datef);
				$object->code=$obj->code;
				$object->label=$obj->label;
				$object->transparency=$obj->transparency;
				$object->priority=$obj->priority;
				$object->fulldayevent=$obj->fulldayevent;
				$object->punctual=$obj->punctual;
				$object->percent=$obj->percent;
				$object->location=$obj->location;
				$object->socid=$obj->fk_soc;
				$object->contactid=$obj->fk_contact;
				$object->note=$obj->note;

				// Event label can now include company and / or contact info, see configuration
				google_complete_label_and_note($object, $langs);

				$ret = createEvent($servicearray, $object, $userlogin);
				if (! preg_match('/ERROR/',$ret))
				{
					if (! preg_match('/google\.com/',$ret)) $ret='google:'.$ret;
					$object->update_ref_ext($ret);	// This is to store ref_ext to allow updates
					$nbinserted++;
				}
				else
				{
					$this->errors[]=$ret;
					$error++;
				}

				$i++;
			}
		}
		catch(Exception $e)
		{
			$this->errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	setEventMessage($langs->trans("PushToGoogleSucess",$nbinserted), 'mesgs');
	if ($error)
	{
		setEventMessage($this->errors, 'errors');
	}

}



/*
 * View
 */

llxHeader();

$head = user_prepare_head($fuser);


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post" autocomplete="off">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="save">';
print '<input type="hidden" name="id" value="'.$id.'">';


$title = $langs->trans("User");
dol_fiche_head($head, 'gsetup', $title, 0, 'user');

print '<table class="border" width="100%">';

// Ref
print '<tr><td width="25%" valign="top">'.$langs->trans("Ref").'</td>';
print '<td colspan="2">';
print $form->showrefnav($fuser,'id','',$user->rights->user->user->lire || $user->admin);
print '</td>';
print '</tr>';

// Lastname
print '<tr><td width="25%" valign="top">'.$langs->trans("LastName").'</td>';
print '<td colspan="2">'.$fuser->lastname.'</td>';
print "</tr>\n";

// Firstname
print '<tr><td width="25%" valign="top">'.$langs->trans("FirstName").'</td>';
print '<td colspan="2">'.$fuser->firstname.'</td>';
print "</tr>\n";

print '</table><br>';


$userlogin = $conf->global->GOOGLE_LOGIN;

if (! empty($userlogin))	// We use setup of user
{
	print $langs->trans("GoogleSetupIsGlobal",$userlogin);
}
else
{
	print_fiche_titre($langs->trans("AgendaSync"), '', '');

	$var=false;
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
	print $form->selectyesno("GOOGLE_DUPLICATE_INTO_GCAL",isset($_POST["GOOGLE_DUPLICATE_INTO_GCAL"])?$_POST["GOOGLE_DUPLICATE_INTO_GCAL"]:$fuser->conf->GOOGLE_DUPLICATE_INTO_GCAL,1);
	print "</td>";
	print "</tr>";
	*/
	// Google login
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td class="fieldrequired">'.$langs->trans("GoogleIDAgenda")."</td>";
	print "<td>";
	if (! empty($conf->global->GOOGLE_LOGIN)) print $conf->global->GOOGLE_LOGIN;
	else print '<input class="flat" type="text" size="30" name="GOOGLE_LOGIN" value="'.$fuser->conf->GOOGLE_LOGIN.'">';
	print "</td>";
	print '<td>';
	print $langs->trans("Example").": yourlogin@gmail.com, email@mydomain.com, 'primary'<br>";
	print $langs->trans("GoogleSetupHelp");
	print '</td>';
	print "</tr>";

	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_EMAIL")."</td>";
	print '<td>';
	print '<input class="flat" type="text" size="90" name="GOOGLE_API_SERVICEACCOUNT_EMAIL" value="'.$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.'" disabled="disabled">';
	print '</td>';
	print '<td>';
	print $langs->trans("ThisFieldIsAGlobalSetup").'<br>';
	//print $langs->trans("AllowGoogleToLoginWithServiceAccount","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
	print '</td>';
	print '</tr>';

	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_P12KEY")."</td>";
	print '<td>';
	if (! empty($conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY)) print $conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY.'<br>';
	//print '<input type="file" name="GOOGLE_API_SERVICEACCOUNT_P12KEY_file">';
	print '</td>';
	print '<td>';
	print $langs->trans("ThisFieldIsAGlobalSetup").'<br>';
	//print $langs->trans("AllowGoogleToLoginWithServiceAccountP12","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
	print '</td>';
	print '</tr>';

	/* Done by default
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print "<td>".$langs->trans("GOOGLE_EVENT_LABEL_INC_SOCIETE")."<br /></td>";
	print "<td>";
	print $form->selectyesno("GOOGLE_EVENT_LABEL_INC_SOCIETE",isset($_POST["GOOGLE_EVENT_LABEL_INC_SOCIETE"])?$_POST["GOOGLE_EVENT_LABEL_INC_SOCIETE"]:$fuser->conf->GOOGLE_EVENT_LABEL_INC_SOCIETE,1);
	print "</td>";
	print "</tr>";
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print "<td>".$langs->trans("GOOGLE_EVENT_LABEL_INC_CONTACT")."<br /></td>";
	print "<td>";
	print $form->selectyesno("GOOGLE_EVENT_LABEL_INC_CONTACT",isset($_POST["GOOGLE_EVENT_LABEL_INC_CONTACT"])?$_POST["GOOGLE_EVENT_LABEL_INC_CONTACT"]:$fuser->conf->GOOGLE_EVENT_LABEL_INC_CONTACT,1);
	print "</td>";
	print "</tr>";
	*/

	print "</table>";

	print info_admin($langs->trans("EnableAPI","https://code.google.com/apis/console/","https://code.google.com/apis/console/","Calendar API"));

	print info_admin($langs->trans("ShareCalendarWithServiceAccount",$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL,$langs->transnoentitiesnoconv("GoogleIDAgenda")));
}

dol_fiche_end();


if (empty($userlogin))	// We use setup of user
{
	print '<div class="center">';
	//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
	//print "&nbsp; &nbsp;";
	print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
	print "</div>";
}


print "</form>\n";

print '<br>';



// Test area

if (empty($userlogin))	// We use setup of user
{

	print '<div class="tabsActions">';

	print '<div class="synccal">';
	if (empty($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL) || empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL) || empty($fuser->conf->GOOGLE_LOGIN))
	{
		print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")."</a>";

		print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreate")."</a>";
	}
	else
	{
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testall&id='.$id.'">'.$langs->trans("TestCreateUpdateDelete")."</a>";

		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreate&id='.$id.'">'.$langs->trans("TestCreateUpdate")."</a>";
	}
	print '</div>';

	print '</div>';

	print '<br>';


	print '<div class="synccal">';

	if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL))
	{
		print '<br>';
		print '<br>';

		print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		print '<input type="hidden" name="action" value="pushallevents">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print $langs->trans("ExportEventsToGoogle",$max,$fuser->conf->GOOGLE_LOGIN)." ";
		print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'"';
		if (empty($fuser->conf->GOOGLE_LOGIN)) print ' disabled="disabled"';
		print '>';
		print "</form>\n";
	}

	if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL))
	{
		print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		print '<input type="hidden" name="action" value="deleteallevents">';
		print '<input type="hidden" name="id" value="'.$id.'">';
		print $langs->trans("DeleteAllGoogleEvents",$fuser->conf->GOOGLE_LOGIN)." ";
		print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'"';
		if (empty($fuser->conf->GOOGLE_LOGIN)) print ' disabled="disabled"';
		print '>';
		print "</form>\n";
	}

	print '</div>';
}


llxFooter();
$db->close();

