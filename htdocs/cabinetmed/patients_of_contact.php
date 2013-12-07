<?php
/* Copyright (C) 2004-2012      Laurent Destailleur  <eldy@users.sourceforge.net>
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
* or see http://www.gnu.org/
*/

/**
 *   \file       htdocs/cabinetmed/patients_of_contact.php
 *   \brief      Tab for patients for contact
 *   \ingroup    cabinetmed
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php");
include_once("./lib/cabinetmed.lib.php");
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");

$action = GETPOST("action");
$id=GETPOST('id','int');  // Id consultation

$langs->load("companies");
$langs->load("bills");
$langs->load("banks");
$langs->load("cabinetmed@cabinetmed");

// Security check
$socid = GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid);

if (!$user->rights->cabinetmed->read) accessforbidden();

$mesgarray=array();

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) {
    $page = 0;
}
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield='s.nom';
if (! $sortorder) $sortorder='ASC';
$limit = $conf->liste_limit;

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'contact', $id, 'socpeople&societe');
$object = new Contact($db);

$now=dol_now();


/*
 * Actions
*/

// Delete consultation
if (GETPOST("action") == 'confirm_delete' && GETPOST("confirm") == 'yes' && $user->rights->societe->supprimer)
{
    $consult->fetch($id);
    $result = $consult->delete($user);
    if ($result >= 0)
    {
        header("Location: ".$_SERVER["PHP_SELF"].'?socid='.$socid);
        exit;
    }
    else
    {
        $langs->load("errors");
        $mesg=$langs->trans($consult->error);
        $action='';
    }
}


/*
 *	View
*/


/*
 *	View
*/

$now=dol_now();

llxHeader('',$langs->trans("PatientsOfContact"),'');

$form = new Form($db);

$object->fetch($id, $user);

$head = contact_prepare_head($object);

dol_fiche_head($head, 'tabpatient', $langs->trans("ContactsAddresses"), 0, 'contact');


if ($id > 0)
{
    /*
     * Fiche en mode visu
    */
    print '<table class="border" width="100%">';

    // Ref
    print '<tr><td width="20%">'.$langs->trans("Ref").'</td><td colspan="3">';
    print $form->showrefnav($object,'id');
    print '</td></tr>';

    // Name
    print '<tr><td width="20%">'.$langs->trans("Lastname").' / '.$langs->trans("Label").'</td><td width="30%">'.$object->lastname.'</td>';
    print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%">'.$object->firstname.'</td></tr>';

    // Company
    if (empty($conf->global->SOCIETE_DISABLE_CONTACTS))
    {
        if ($object->socid > 0)
        {
            $objsoc = new Societe($db);
            $objsoc->fetch($object->socid);

            print '<tr><td>'.$langs->trans("Company").'</td><td colspan="3">'.$objsoc->getNomUrl(1).'</td></tr>';
        }

        else
        {
            print '<tr><td>'.$langs->trans("Company").'</td><td colspan="3">';
            print $langs->trans("ContactNotLinkedToCompany");
            print '</td></tr>';
        }
    }

    // Civility
    print '<tr><td>'.$langs->trans("UserTitle").'</td><td colspan="3">';
    print $object->getCivilityLabel();
    print '</td></tr>';

    // Role
    print '<tr><td>'.$langs->trans("PostOrFunction").'</td><td colspan="3">'.$object->poste.'</td></tr>';

    print "</table>";
}

dol_fiche_end();


print_fiche_titre($langs->trans("ListOfPatients"),'','');

$param='&id='.$id;

print "\n";
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans('Name'),$_SERVER['PHP_SELF'],'s.nom','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('CustomerCode'),$_SERVER['PHP_SELF'],'s.code_client','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Zip'),$_SERVER['PHP_SELF'],'s.zip','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Town'),$_SERVER['PHP_SELF'],'s.town','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('ProfId3'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
print '</tr>';


// List of patients
$sql = "SELECT";
$sql.= " s.rowid,";
$sql.= " s.nom as name,";
$sql.= " s.code_client as customer_code,";
$sql.= " s.zip as zip,";
$sql.= " s.town as town,";
$sql.= " s.ape";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s,";
$sql.= " ".MAIN_DB_PREFIX."element_contact as ec,";
$sql.= " ".MAIN_DB_PREFIX."c_type_contact as tc";
$sql.= " WHERE ec.fk_socpeople = ".$id;
$sql.= " AND ec.element_id = s.rowid";
$sql.= " AND ec.fk_c_type_contact = tc.rowid";
$sql.= " AND tc.element = 'societe'";
$sql.= " ORDER BY ".$sortfield." ".$sortorder.", s.rowid DESC";

$resql=$db->query($sql);
if ($resql)
{
    $i = 0 ;
    $num = $db->num_rows($resql);
    $var=true;

    $societestatic=new Societe($db);

    while ($i < $num)
    {
        $obj = $db->fetch_object($resql);

        $societestatic->id=$obj->rowid;
        $societestatic->name=$obj->name;

        $var=!$var;
        print '<tr '.$bc[$var].'>';

        print '<td>';
        print $societestatic->getNomUrl(1);
        print '</td>';
        print '<td>';
        print $obj->customer_code;
        print '</td>';
        print '<td>';
        print $obj->zip;
        print '</td>';
        print '<td>';
        print $obj->town;
        print '</td>';
        print '<td>';
        print $obj->ape;
        print '</td>';

        print '</tr>';
        $i++;
    }
}
else
{
    dol_print_error($db);
}

print '</table>';
print '<br>';

llxFooter();

$db->close();
?>
