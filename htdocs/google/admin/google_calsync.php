<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/google/admin/google.php
 *      \ingroup    google
 *      \brief      Setup page for google module (Calendar)
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
require_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');
dol_include_once("/google/lib/google.lib.php");
dol_include_once('/google/lib/google_calendar.lib.php');

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

if ($action == 'save')
{
    $db->begin();

    //print 'color='.$color;
    $res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_GCAL',trim($_POST["GOOGLE_DUPLICATE_INTO_GCAL"]),'chaine',0,'',$conf->entity);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_LOGIN',trim($_POST["GOOGLE_LOGIN"]),'chaine',0,'',$conf->entity);
    if (! $res > 0) $error++;
    $res=dolibarr_set_const($db,'GOOGLE_PASSWORD',trim($_POST["GOOGLE_PASSWORD"]),'chaine',0,'',$conf->entity);
    if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE',trim($_POST["GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE"]),'chaine',0,'',$conf->entity);
    if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT',trim($_POST["GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT"]),'chaine',0,'',$conf->entity);
    if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_CAL_TZ_FIX',trim($_POST["GOOGLE_CAL_TZ_FIX"]),'chaine',0,'',$conf->entity);
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

    $object->label='New label';
    $object->location='New location';
    $object->note='New note';
    $object->datep+=3600;
    $object->datef+=3600;

    $result=$object->update($user);

    if ($action == 'testall')
    {
	    $result=$object->delete();
    }

    if ($result > 0)
    {
        $mesg=$langs->trans("TestSuccessfull");
    }
    else
    {
        $error='<div class="error">'.$object->error.'</div>';
        $errors=$object->errors;
    }
}


if ($action == 'pushallevents')
{
	$googleuser = empty($conf->global->GOOGLE_CONTACT_LOGIN)?'':$conf->global->GOOGLE_CONTACT_LOGIN;
	$googlepwd  = empty($conf->global->GOOGLE_CONTACT_PASSWORD)?'':$conf->global->GOOGLE_CONTACT_PASSWORD;

	// Try to use V3 API
	$sql = 'SELECT id, datep, datep2 as datef, code, label, transparency, priority, fulldayevent, punctual, percent';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'actioncomm';
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
		$event = new ActionComm($db);
		$event->id=$obj->rowid;
		$event->datep=$obj->datep;
		$event->datef=$obj->datef;
		$event->code=$obj->code;
		$event->label=$obj->label;
		$event->transparency=$obj->transparency;
		$event->priority=$obj->priority;
		$event->fulldayevent=$obj->fulldayevent;
		$event->punctual=$obj->punctual;
		$event->percent=$obj->percent;
		$gCals[]=$event;

		$i++;
	}
	$result=0;
	if (count($gCals)) $result=insertGCalsEntries($gCals);

	if (is_numeric($result) && $result >= 0)
	{
		$mesg = $langs->trans("PushToGoogleSucess",count($gCals));
	}
	else
	{
		$error++;
		$errors[] = $langs->trans("Error").' '.$result;
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
llxHeader('',$langs->trans("GoogleSetup"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"),$linkback,'setup');
print '<br>';


$head=googleadmin_prepare_head();


dol_fiche_head($head, 'tabagendasync', $langs->trans("GoogleTools"));

if (! function_exists("openssl_open")) print '<div class="warning">Warning: PHP Module \'openssl\' is not installed</div><br>';
if (! class_exists('DOMDocument')) print '<div class="warning">Warning: PHP Module \'xml\' is not installed</div><br>';

print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post" autocomplete="off">';
print '<input type="hidden" name="action" value="save">';

print $langs->trans("GoogleEnableSyncToCalendar").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_GCAL",isset($_POST["GOOGLE_DUPLICATE_INTO_GCAL"])?$_POST["GOOGLE_DUPLICATE_INTO_GCAL"]:$conf->global->GOOGLE_DUPLICATE_INTO_GCAL,1).'<br><br>';


$var=false;

print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleAPIv2Usage","Calendar").')'."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Google login
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_LOGIN")."</td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_LOGIN" value="'.$conf->global->GOOGLE_LOGIN.'">';
print ' &nbsp; '.$langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print "</td>";
print "</tr>";
// Google password
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_PASSWORD")."</td>";
print "<td>";
print '<input class="flat" type="password" size="10" name="GOOGLE_PASSWORD" value="'.$conf->global->GOOGLE_PASSWORD.'">';
print ' &nbsp; '.$langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print "</td>";
print "</tr>";

print "</table>";
print "<br>";

print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="25%">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Google login
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_FIX_TZ")."</td>";
print "<td>";
print '<input class="flat" type="text" size="4" name="GOOGLE_CAL_TZ_FIX" value="'.$conf->global->GOOGLE_CAL_TZ_FIX.'">';
print ' &nbsp; '.$langs->trans("FillThisOnlyIfRequired");
print "</td>";
print "</tr>";

print '</table>';

print '<br>';

print '<center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();

print '<br>';


print '<div class="tabsActions">';
if (empty($conf->global->GOOGLE_LOGIN) || empty($conf->global->GOOGLE_LOGIN) || empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL))
{
	print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreate")."</a>";
}
else
{
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testall">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreate">'.$langs->trans("TestCreate")."</a>";
}
print '</div>';

print '<br>';


if ($conf->global->MAIN_FEATURES_LEVEL > 0 && ! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL))
{
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallevents">';
	print $langs->trans("ExportEventsToGoogle")." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallevents">';
	print $langs->trans("DeleteAllGoogleEvents")." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'">';
	print "</form>\n";
}



dol_htmloutput_mesg($mesg);
dol_htmloutput_errors($error,$errors);



llxFooter();

if (is_object($db)) $db->close();
?>
