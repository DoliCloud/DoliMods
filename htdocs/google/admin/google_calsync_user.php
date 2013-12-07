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
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/usergroups.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");

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
$result = restrictedArea($user, 'user', $id, '&user', $feature2);
if ($user->id <> $id && ! $canreaduser) accessforbidden();

$dirtop = "../core/menus/standard";
$dirleft = "../core/menus/standard";

// Charge utilisateur edite
$fuser = new User($db);
$fuser->fetch($id);
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
    if (! $_POST["cancel"])
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

// This is a hidden action to allow to test creation of event once synchro with Calendar has been enabled.
if ($action == 'testcreate')
{
    include_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');

    $object=new ActionComm($db);
    $result=$object->initAsSpecimen();

    $result=$object->add($user);

    $object->label='New label';
    $object->location='New location';
    $object->note='New note';
    $object->datep+=3600;
    $object->datef+=3600;

    $result=$object->update($user);

    $result=$object->delete();

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



/*
 * View
 */

llxHeader();

$head = user_prepare_head($fuser);

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


$user = $conf->global->GOOGLE_LOGIN;
$pwd = $conf->global->GOOGLE_PASSWORD;

if (! empty($user) && ! empty($pwd))	// We use setup of user
{
	print $langs->trans("GoogleSetupIsGlobal",$user);
}
else
{
	print_fiche_titre($langs->trans("AgendaSync"), '', '');

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post" autocomplete="off">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="save">';
	print '<input type="hidden" name="id" value="'.$id.'">';

	$var=false;
	print "<table class=\"noborder\" width=\"100%\">";

	print "<tr class=\"liste_titre\">";
	print '<td width="25%">'.$langs->trans("Parameter")."</td>";
	print "<td>".$langs->trans("Value")."</td>";
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
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_LOGIN")."</td>";
	print "<td>";
	if (! empty($conf->global->GOOGLE_LOGIN)) print $conf->global->GOOGLE_LOGIN;
	else print '<input class="flat" type="text" size="30" name="GOOGLE_LOGIN" value="'.$fuser->conf->GOOGLE_LOGIN.'">';
	print "</td>";
	print "</tr>";
	// Google password
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td class="fieldrequired">'.$langs->trans("GOOGLE_PASSWORD")."</td>";
	print "<td>";
	if (! empty($conf->global->GOOGLE_PASSWORD)) print $conf->global->GOOGLE_PASSWORD;
	else print '<input class="flat" type="password" size="10" name="GOOGLE_PASSWORD" value="'.$fuser->conf->GOOGLE_PASSWORD.'">';
	print "</td>";
	print "</tr>";
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
	print "<br>";

	print '<center>';
	//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
	//print "&nbsp; &nbsp;";
	print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
	print "</center>";

	print "</form>\n";
}

dol_fiche_end();

llxFooter();
$db->close();
?>
