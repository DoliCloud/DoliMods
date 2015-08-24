<?php
/* Copyright (C) 2005      Patrick Rouillon     <patrick@rouillon.net>
 * Copyright (C) 2005-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *       \file       htdocs/cabinetmed/contact.php
 *       \ingroup    cabinetmed
 *       \brief      Tab for links between doctors and patient
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
include_once(DOL_DOCUMENT_ROOT."/core/lib/ajax.lib.php");
include_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");

$langs->load("cabinetmed@cabinetmed");
$langs->load("orders");
$langs->load("sendings");
$langs->load("companies");

// Security check
$socid = GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid);


/*
 * Add new contact
 */

if ($_POST["action"] == 'addcontact' && $user->rights->societe->creer)
{
	if ($_POST["contactid"] && $_POST["type"])
	{
		$result = 0;
		$societe = new Societe($db);
		$result = $societe->fetch($socid);

	    if ($result > 0 && $socid > 0)
	    {
	  		$result = $societe->add_contact($_POST["contactid"], $_POST["type"], $_POST["source"]);
	    }

		if ($result >= 0)
		{
			Header("Location: contact.php?socid=".$societe->id);
			exit;
		}
		else
		{
			if ($societe->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
			{
				$langs->load("errors");
				$mesg = '<div class="error">'.$langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType").'</div>';
			}
			else
			{
				$mesg = '<div class="error">'.$societe->error.'</div>';
			}
		}
	}
}

// bascule du statut d'un contact
if ($_GET["action"] == 'swapstatut' && $user->rights->societe->creer)
{
	$object = new Societe($db);
	if ($object->fetch(GETPOST('facid','int')))
	{
	    $result=$object->swapContactStatus(GETPOST('ligne'));
	}
	else
	{
		dol_print_error($db);
	}
}

// Efface un contact
if ($_GET["action"] == 'deleteline' && $user->rights->societe->creer)
{
	$societe = new Societe($db);
	$societe->fetch($socid);
	$result = $societe->delete_contact($_GET["lineid"]);

	if ($result >= 0)
	{
		Header("Location: contact.php?socid=".$societe->id);
		exit;
	}
	else {
		dol_print_error($db);
	}
}


/*
 * View
 */

llxHeader('',$langs->trans('Contacts'),'');

$html = new Form($db);
$form = new Form($db);
$formcompany = new FormCompany($db);
$contactstatic=new Contact($db);
$userstatic=new User($db);


/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */
if (isset($mesg)) print $mesg;

$id = $_GET['socid'];
$ref= $_GET['ref'];
if ($id > 0 || ! empty($ref))
{
	$societe = new Patient($db);
	$societe->fetch($id);

	$object = $societe;		// Use on test by module tabs declaration


	$head = societe_prepare_head($societe);
    dol_fiche_head($head, 'tabpatientcontacts', $langs->trans("Patient"),0,'company');

    $width=300;
    print '
            <style>
            .ui-autocomplete-input { width: '.$width.'px; }
            </style>
            ';

    print ajax_combobox('contactid');

    print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

    print '<table class="border" width="100%">';

    print '<tr><td width="25%">'.$langs->trans('PatientName').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($societe,'socid','',($user->societe_id?0:1),'rowid','nom');
    print '</td></tr>';

    if ($societe->client)
    {
        print '<tr><td>';
        print $langs->trans('PatientCode').'</td><td colspan="3">';
        print $societe->code_client;
        if ($societe->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongPatientCode").')</font>';
        print '</td></tr>';
    }

    if ($societe->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $societe->code_fournisseur;
        if ($societe->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }

    print "</table>";

    print '</form>';

    dol_fiche_end();

	/*
	* Lignes de contacts
	*/
    print '<form action="contact.php?socid='.$socid.'" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="addcontact">';
    print '<input type="hidden" name="source" value="external">';
    print '<input type="hidden" name="socid" value="'.$socid.'">';

    print '<br><table class="noborder" width="100%">';

	/*
	* Ajouter une ligne de contact
	* Non affiche en mode modification de ligne
	*/
	if ($_GET["action"] != 'editline')
	{
		print '<thead><tr class="liste_titre">';
		//print '<td>'.$langs->trans("Source").'</td>';
		print '<td>'.$langs->trans("Contacts").'</td>';
		print '<td>'.$langs->trans("ContactType").'</td>';
		print '<td colspan="3">&nbsp;</td>';
		print "</tr></thead>\n";

		$var = true;

		// Line to add contacts
		$var=!$var;
		print "<tr ".$bc[$var].">";

		print '<td colspan="1">';
		// $contactAlreadySelected = $commande->getListContactId('external');	// On ne doit pas desactiver un contact deja selectionner car on doit pouvoir le seclectionner une deuxieme fois pour un autre type
		$nbofcontacts=$html->select_contacts(0, '', 'contactid', 1, '', '', 1);
		//if ($nbofcontacts == 0) print $langs->trans("NoContactDefined");
		if (versioncompare(versiondolibarrarray(),array(3,7,-3)) >= 0)
		{
	        print ' <a href="'.DOL_URL_ROOT.'/contact/card.php?leftmenu=contacts&action=create&backtopage='.urlencode($_SERVER["PHP_SELF"]).'?socid='.$socid.'">'.$langs->trans("Add").'</a>';
		}
		else
		{
	        print ' <a href="'.DOL_URL_ROOT.'/contact/card.php?leftmenu=contacts&action=create&backtopage='.urlencode($_SERVER["PHP_SELF"]).'?socid='.$socid.'">'.$langs->trans("Add").'</a>';
		}
		print '</td>';
		print '<td>';
		$formcompany->selectTypeContact($societe, '', 'type','external','libelle',1);
        //if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
		print '</td>';
		print '<td align="center" colspan="3" ><input type="submit" class="button" value="'.$langs->trans("AddLink").'"';
		if (! $nbofcontacts) print ' disabled="disabled"';
		print '></td>';
		print '</tr>';

		print "</form>";
	}


	// List of linked contacts
	print '<tr class="liste_titre">';
	//print '<td>'.$langs->trans("Source").'</td>';
	print '<td>'.$langs->trans("Contacts").'</td>';
	print '<td>'.$langs->trans("ContactType").'</td>';
	print '<td align="center">'.$langs->trans("Status").'</td>';
	print '<td colspan="2">&nbsp;</td>';
	print "</tr>\n";

	$companystatic=new Societe($db);
	$var = true;

	foreach(array('external') as $source)
	{
		$tab = $societe->liste_contact(-1,$source);
		$num=count($tab);

		$i = 0;
		while ($i < $num)
		{
			$var = !$var;

			print '<tr '.$bc[$var].' valign="top">';

			// Source
			/*print '<td align="left">';
			if ($tab[$i]['source']=='internal') print $langs->trans("User");
			if ($tab[$i]['source']=='external') print $langs->trans("ThirdPartyContact");
			print '</td>';
			*/

			// Societe
			/*print '<td align="left">';
			if ($tab[$i]['socid'] > 0)
			{
				$companystatic->fetch($tab[$i]['socid']);
				print $companystatic->getNomUrl(1);
			}
			if ($tab[$i]['socid'] < 0)
			{
				print $conf->global->MAIN_INFO_SOCIETE_NOM;
			}
			if (! $tab[$i]['socid'])
			{
				print '&nbsp;';
			}
			print '</td>';
			*/

			// Contact
			print '<td>';
            if ($tab[$i]['source']=='internal')
            {
                $userstatic->id=$tab[$i]['id'];
                $userstatic->lastname=$tab[$i]['lastname'];
                $userstatic->firstname=$tab[$i]['firstname'];
                print $userstatic->getNomUrl(1);
            }
            if ($tab[$i]['source']=='external')
            {
                $contactstatic->id=$tab[$i]['id'];
                $contactstatic->lastname=$tab[$i]['lastname'];
                $contactstatic->firstname=$tab[$i]['firstname'];
                print $contactstatic->getNomUrl(1);
            }
			print '</td>';

			// Type de contact
			print '<td>'.$tab[$i]['libelle'].'</td>';

			// Statut
			print '<td align="center">';
			// Activation desativation du contact
			if ($societe->statut >= 0)	print '<a href="contact.php?socid='.$societe->id.'&amp;action=swapstatut&amp;ligne='.$tab[$i]['rowid'].'">';
			print $contactstatic->LibStatut($tab[$i]['status'],3);
			if ($societe->statut >= 0)	print '</a>';
			print '</td>';

			// Icon update et delete
			print '<td align="center" nowrap>';
			if ($societe->statut < 5 && $user->rights->societe->creer)
			{
				print '&nbsp;';
				print '<a href="contact.php?socid='.$societe->id.'&amp;action=deleteline&amp;lineid='.$tab[$i]['rowid'].'">';
				print img_delete();
				print '</a>';
			}
			print '</td>';

			print "</tr>\n";

			$i ++;
		}
	}
	print "</table>";
}

llxFooter();

$db->close();
