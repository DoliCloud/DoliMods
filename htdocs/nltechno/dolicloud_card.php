<?php
/* Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *       \file       htdocs/nltechno/dolicloud_card.php
 *       \ingroup    societe
 *       \brief      Card of a contact
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
dol_include_once("/nltechno/core/lib/dolicloud.lib.php");
dol_include_once('/nltechno/class/dolicloudcustomer.class.php');

$langs->load("companies");
$langs->load("users");
$langs->load("other");
$langs->load("commercial");
$langs->load("nltechno@nltechno");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action','alpha') ? GETPOST('action','alpha') : 'view');
$confirm	= GETPOST('confirm','alpha');
$backtopage = GETPOST('backtopage','alpha');
$id			= GETPOST('id','int');
$socid		= GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;

$object = new DoliCloudCustomer($db);

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
//$object->getCanvas($id);
$canvas = $object->canvas?$object->canvas:GETPOST("canvas");
if (! empty($canvas))
{
    require_once(DOL_DOCUMENT_ROOT."/core/class/canvas.class.php");
    $objcanvas = new Canvas($db, $action);
    $objcanvas->getCanvas('contact', 'contactcard', $canvas);
}

// Security check
$result = restrictedArea($user, 'contact', $id, 'socpeople&societe', '', '', '', $objcanvas); // If we create a contact with no company (shared contacts), no check on write permission

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('contactcard'));


/*
 *	Actions
 */

$parameters=array('id'=>$id, 'objcanvas'=>$objcanvas);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

if (empty($reshook))
{
    // Cancel
    if (GETPOST("cancel") && ! empty($backtopage))
    {
        header("Location: ".$backtopage);
        exit;
    }

    // Add customer
    if ($action == 'add' && $user->rights->nltechno->dolicloud->create)
    {
        $db->begin();

        if ($canvas) $object->canvas=$canvas;

        $object->instance		= $_POST["instance"];
        $object->organization	= $_POST["organization"];
        $object->plan	= $_POST["plan"];
        $object->lastname		= $_POST["lastname"];
        $object->firstname		= $_POST["firstname"];
        $object->address		= $_POST["address"];
        $object->zip			= $_POST["zipcode"];
        $object->town			= $_POST["town"];
        $object->country_id		= $_POST["country_id"];
        $object->state_id       = $_POST["departement_id"];
        $object->email			= $_POST["email"];
        $object->phone_pro		= $_POST["phone_pro"];
        $object->note			= $_POST["note"];

        if (empty($_POST["instance"]) || empty($_POST["organization"]) || empty($_POST["plan"]) || empty($_POST["email"]))
        {
            $error++; $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Instance").",".$langs->transnoentitiesnoconv("Organization").",".$langs->transnoentitiesnoconv("Plan").",".$langs->transnoentitiesnoconv("EMail"));
            $action = 'create';
        }

        if (! $error)
        {
            $id =  $object->create($user);
            if ($id <= 0)
            {
                $error++; $errors=array_merge($errors,($object->error?array($object->error):$object->errors));
                $action = 'create';
            }
        }

        if (! $error && $id > 0)
        {
            $db->commit();
            if (! empty($backtopage)) $url=$backtopage;
            else $url=$_SERVER["PHP_SELF"].'?id='.$id;
            Header("Location: ".$url);
            exit;
        }
        else
        {
            $db->rollback();
        }
    }

    if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->nltechno->dolicloud->delete)
    {
        $result=$object->fetch($_GET["id"]);

        $result = $object->delete();
        if ($result > 0)
        {
            Header("Location: ".dol_buildpath('/nltechno/dolicloud_list.php'));
            exit;
        }
        else
        {
            $error=$object->error; $errors=$object->errors;
        }
    }

    if ($action == 'update' && ! $_POST["cancel"] && $user->rights->nltechno->dolicloud>create)
    {
        if (empty($_POST["lastname"]))
        {
            $error++; $errors=array($langs->trans("ErrorFieldRequired",$langs->transnoentities("Name").' / '.$langs->transnoentities("Label")));
            $action = 'edit';
        }

        if (! $error)
        {
            $object->fetch($_POST["contactid"]);

            $object->oldcopy=dol_clone($object);

            $object->instance		= $_POST["instance"];
            $object->organization	= $_POST["organization"];
            $object->lastname		= $_POST["lastname"];
            $object->firstname		= $_POST["firstname"];

            $object->address		= $_POST["address"];
            $object->zip			= $_POST["zipcode"];
            $object->town			= $_POST["town"];
            $object->state_id   	= $_POST["departement_id"];
            $object->country_id		= $_POST["country_id"];

            $object->email			= $_POST["email"];
            $object->phone_pro		= $_POST["phone_pro"];
            $object->note			= $_POST["note"];

            $result = $object->update(GETPOST('id'), $user);

            if ($result > 0)
            {
                $action = 'view';
            }
            else
            {
                $error=$object->error; $errors=$object->errors;
                $action = 'edit';
            }
        }
    }
}


/*
 *	View
 */

$help_url='';
llxHeader('',$langs->trans("DoliCloudCustomers"),$help_url);

$form = new Form($db);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';

if ($socid > 0)
{
    $objsoc = new Societe($db);
    $objsoc->fetch($socid);
}

if (is_object($objcanvas) && $objcanvas->displayCanvasExists($action))
{
    // -----------------------------------------
    // When used with CANVAS
    // -----------------------------------------
    if (empty($object->error) && $id)
 	{
	     $object = new Contact($db);
	     $object->fetch($id);
 	}
	$objcanvas->assign_values($action, $id);	// Set value for templates
	$objcanvas->display_canvas($action);		// Show template
}
else
{
    // -----------------------------------------
    // When used in standard mode
    // -----------------------------------------

    // Confirm deleting contact
    if ($user->rights->nltechno->dolicloud->delete)
    {
        if ($action == 'delete')
        {
            $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$_GET["id"],$langs->trans("DeleteContact"),$langs->trans("ConfirmDeleteContact"),"confirm_delete",'',0,1);
            if ($ret == 'html') print '<br>';
        }
    }

    /*
     * Onglets
     */
    if ($id > 0)
    {
        // Si edition contact deja existant
        $object = new DoliCloudCustomer($db);
        $return=$object->fetch($id, $user);
        if ($return <= 0)
        {
            dol_print_error('',$object->error);
            $id=0;
        }

        // Show tabs
        $head = dolicloud_prepare_head($object);

        $title = $langs->trans("DoliCloudCustomers");
        dol_fiche_head($head, 'card', $title, 0, 'contact');
    }

    if ($user->rights->nltechno->dolicloud->create)
    {
        if ($action == 'create')
        {
            /*
             * Fiche en mode creation
             */
            $object->canvas=$canvas;

            // We set country_id, country_code and label for the selected country
            $object->country_id=$_POST["country_id"]?$_POST["country_id"]:$mysoc->country_id;
            if ($object->country_id)
            {
            	$tmparray=getCountry($object->country_id,'all');
                $object->pays_code    = $tmparray['code'];
                $object->pays         = $tmparray['label'];
                $object->country_code = $tmparray['code'];
                $object->country      = $tmparray['label'];
            }

            $title = $addcontact = $langs->trans("DoliCloudCustomers");
            print_fiche_titre($title);

            // Affiche les erreurs
            dol_htmloutput_errors(is_numeric($error)?'':$error,$errors);

            if ($conf->use_javascript_ajax)
            {
                print "\n".'<script type="text/javascript" language="javascript">';
                print 'jQuery(document).ready(function () {
							jQuery("#selectcountry_id").change(function() {
								document.formsoc.action.value="create";
								document.formsoc.submit();
                        	});
						})';
                print '</script>'."\n";
            }

            print '<br>';
            print '<form method="post" name="formsoc" action="'.$_SERVER["PHP_SELF"].'">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="add">';
            print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
            print '<table class="border" width="100%">';

            // Instance
            print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Instance").'</td><td width="30%"><input name="instance" type="text" size="30" maxlength="80" value="'.(isset($_POST["instance"])?$_POST["instance"]:$object->instance).'"></td>';
            print '<td width="20%" class="fieldrequired">'.$langs->trans("Organization").'/'.$langs->trans("Company").'</td><td width="30%"><input name="organization" type="text" size="30" maxlength="80" value="'.(isset($_POST["organization"])?$_POST["organization"]:$object->organization).'"></td></tr>';

            // EMail
            print '<tr><td class="fieldrequired">'.$langs->trans("Email").'</td><td colspan="3"><input name="email" type="text" size="50" maxlength="80" value="'.(isset($_POST["email"])?$_POST["email"]:$object->email).'"></td></tr>';

            // Plan
            print '<tr><td class="fieldrequired">'.$langs->trans("Plan").'</td><td colspan="3"><input name="plan" type="text" size="50" maxlength="80" value="'.(isset($_POST["plan"])?$_POST["plan"]:($object->plan?$object->plan:'Basic')).'"></td></tr>';

            // Name
            print '<tr><td width="20%">'.$langs->trans("Lastname").'</td><td width="30%"><input name="lastname" type="text" size="30" maxlength="80" value="'.(isset($_POST["lastname"])?$_POST["lastname"]:$object->lastname).'"></td>';
            print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%"><input name="firstname" type="text" size="30" maxlength="80" value="'.(isset($_POST["firstname"])?$_POST["firstname"]:$object->firstname).'"></td></tr>';

            // Address
            if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->address)) == 0) $object->address = $objsoc->address;	// Predefined with third party
            print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3"><textarea class="flat" name="address" cols="70">'.(isset($_POST["address"])?$_POST["address"]:$object->address).'</textarea></td>';

            // Zip / Town
            if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->zip)) == 0) $object->zip = $objsoc->zip;			// Predefined with third party
            if (($objsoc->typent_code == 'TE_PRIVATE' || ! empty($conf->global->CONTACT_USE_COMPANY_ADDRESS)) && dol_strlen(trim($object->town)) == 0) $object->town = $objsoc->town;	// Predefined with third party
            print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
            print $formcompany->select_ziptown((isset($_POST["zipcode"])?$_POST["zipcode"]:$object->zip),'zipcode',array('town','selectcountry_id','departement_id'),6).'&nbsp;';
            print $formcompany->select_ziptown((isset($_POST["town"])?$_POST["town"]:$object->town),'town',array('zipcode','selectcountry_id','departement_id'));
            print '</td></tr>';

            // Country
            if (dol_strlen(trim($object->fk_pays)) == 0) $object->fk_pays = $objsoc->country_id;	// Predefined with third party
            print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';
            print $form->select_country((isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id),'country_id');
            if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
            print '</td></tr>';

            // State
            /*
            if (empty($conf->global->SOCIETE_DISABLE_STATE))
            {
                print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">';
                if ($object->country_id)
                {
                    print $formcompany->select_state(isset($_POST["departement_id"])?$_POST["departement_id"]:$object->fk_departement,$object->country_code);
                }
                else
                {
                    print $countrynotdefined;
                }
                print '</td></tr>';
            }
			*/

            // Phone / Fax
            print '<tr><td>'.$langs->trans("PhonePro").'</td><td><input name="phone_pro" type="text" size="18" maxlength="80" value="'.(isset($_POST["phone_pro"])?$_POST["phone_pro"]:$object->phone_pro).'"></td>';
            print '<td colspan="2"></td></tr>';

            // Note
            print '<tr><td valign="top">'.$langs->trans("Note").'</td><td colspan="3" valign="top"><textarea name="note" cols="70" rows="'.ROWS_3.'">'.(isset($_POST["note"])?$_POST["note"]:$object->note).'</textarea></td></tr>';

            print "</table><br><br>";


            print '<center>';
            print '<input type="submit" class="button" name="add" value="'.$langs->trans("Add").'">';
            if (! empty($backtopage))
            {
                print ' &nbsp; &nbsp; ';
                print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
            }
            print '</center>';

            print "</form>";
        }
        elseif ($action == 'edit' && ! empty($id))
        {
            /*
             * Fiche en mode edition
             */

            // We set country_id, and country_code label of the chosen country
            if (isset($_POST["country_id"]) || $object->country_id)
            {
	            $tmparray=getCountry($object->country_id,'all');
	            $object->pays_code    =	$tmparray['code'];
	            $object->pays         =	$tmparray['label'];
	            $object->country_code =	$tmparray['code'];
	            $object->country      =	$tmparray['label'];
            }

            // Affiche les erreurs
            dol_htmloutput_errors($error,$errors);

            if ($conf->use_javascript_ajax)
            {
                print '<script type="text/javascript" language="javascript">';
                print 'jQuery(document).ready(function () {
							jQuery("#selectcountry_id").change(function() {
								document.formsoc.action.value="edit";
								document.formsoc.submit();
							});
						})';
                print '</script>';
            }

            print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$id.'" name="formsoc">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="id" value="'.$id.'">';
            print '<input type="hidden" name="action" value="update">';
            print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
            print '<input type="hidden" name="contactid" value="'.$object->id.'">';
            print '<input type="hidden" name="old_name" value="'.$object->name.'">';
            print '<input type="hidden" name="old_firstname" value="'.$object->firstname.'">';
            print '<table class="border" width="100%">';

            // Instance
            print '<tr><td class="fieldrequired">'.$langs->trans("Instance").'</td><td>';
            print $object->ref;
            print '</td><td class="fieldrequired">'.$langs->trans("Organization").'</td><td>';
            print '<input name="organization" type="text" size="20" maxlength="80" value="'.(isset($_POST["organization"])?$_POST["organization"]:$object->organization).'">';
            print '</td></tr>';

            // EMail
            print '<tr><td class="fieldrequired">'.$langs->trans("EMail").'</td><td colspan="3"><input name="email" type="text" size="40" maxlength="80" value="'.(isset($_POST["email"])?$_POST["email"]:$object->email).'"></td>';
            print '</tr>';

            // Plan
            print '<tr><td width="20%" class="fieldrequired">'.$langs->trans("Plan").'</td><td width="30%" colspan="3"><input name="plan" type="text" size="20" maxlength="80" value="'.(isset($_POST["plan"])?$_POST["plan"]:$object->plan).'"></td>';
            print '</tr>';

            // Name
            print '<tr><td width="20%">'.$langs->trans("Lastname").'</td><td width="30%"><input name="lastname" type="text" size="20" maxlength="80" value="'.(isset($_POST["lastname"])?$_POST["lastname"]:$object->lastname).'"></td>';
            print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%"><input name="firstname" type="text" size="20" maxlength="80" value="'.(isset($_POST["firstname"])?$_POST["firstname"]:$object->firstname).'"></td></tr>';

            // Address
            print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3"><textarea class="flat" name="address" cols="70">'.(isset($_POST["address"])?$_POST["address"]:$object->address).'</textarea></td>';

            // Zip / Town
            print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
           print $formcompany->select_ziptown((isset($_POST["zipcode"])?$_POST["zipcode"]:$object->zip),'zipcode',array('town','selectcountry_id','departement_id'),6).'&nbsp;';
            print $formcompany->select_ziptown((isset($_POST["town"])?$_POST["town"]:$object->town),'town',array('zipcode','selectcountry_id','departement_id'));
            print '</td></tr>';

            // Country
            print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';
            print $form->select_country(isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id,'country_id');
            if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
            print '</td></tr>';

            // State
            /*
            if (empty($conf->global->SOCIETE_DISABLE_STATE))
            {
                print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">';
                print $formcompany->select_state($object->fk_departement,isset($_POST["country_id"])?$_POST["country_id"]:$object->country_id);
                print '</td></tr>';
            }
            */

            // Phone
            print '<tr><td>'.$langs->trans("PhonePro").'</td><td><input name="phone_pro" type="text" size="18" maxlength="80" value="'.(isset($_POST["phone_pro"])?$_POST["phone_pro"]:$object->phone_pro).'"></td>';
            print '<td colspan="2"></td></tr>';

            print '<tr><td valign="top">'.$langs->trans("Note").'</td><td colspan="3">';
            print '<textarea name="note" cols="70" rows="'.ROWS_3.'">';
            print isset($_POST["note"])?$_POST["note"]:$object->note;
            print '</textarea></td></tr>';

            print '</table><br>';

            print '<center>';
            print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
            print ' &nbsp; ';
            print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
            print '</center>';

            print "</form>";
        }
    }

    if (! empty($id) && $action != 'edit' && $action != 'create')
    {
        /*
         * Fiche en mode visualisation
         */

        dol_htmloutput_errors($error,$errors);

        print '<table class="border" width="100%">';

        // Instance / Organization
        print '<tr><td width="20%">'.$langs->trans("Instance").'</td><td>';
        print $form->showrefnav($object,'id');
        print '</td><td>'.$langs->trans("Organization").'</td><td>';
        print $object->organization;
        print '</td></tr>';

        // Email
        print '<tr><td>'.$langs->trans("EMail").'</td><td colspan="3">'.dol_print_email($object->email,$object->id,$object->socid,'AC_EMAIL').'</td>';
        print '</tr>';

        // Plan
        print '<tr><td width="20%">'.$langs->trans("Plan").'</td><td colspan="3">'.$object->plan.'</td>';
        print '</tr>';

        // Lastname / Name
        print '<tr><td width="20%">'.$langs->trans("Lastname").' / '.$langs->trans("Label").'</td><td width="30%">'.$object->lastname.'</td>';
        print '<td width="20%">'.$langs->trans("Firstname").'</td><td width="30%">'.$object->firstname.'</td></tr>';

        // Address
        print '<tr><td>'.$langs->trans("Address").'</td><td colspan="3">';
        dol_print_address($object->address,'gmap','contact',$object->id);
        print '</td></tr>';

        // Zip Town
        print '<tr><td>'.$langs->trans("Zip").' / '.$langs->trans("Town").'</td><td colspan="3">';
        print $object->cp;
        if ($object->cp) print '&nbsp;';
        print $object->ville.'</td></tr>';

        // Country
        print '<tr><td>'.$langs->trans("Country").'</td><td colspan="3">';
        $img=picto_from_langcode($object->country_code);
        if ($img) print $img.' ';
        print $object->pays;
        print '</td></tr>';

        // State
        if (empty($conf->global->SOCIETE_DISABLE_STATE))
        {
            print '<tr><td>'.$langs->trans('State').'</td><td colspan="3">'.$object->departement.'</td>';
        }

        // Phone
        print '<tr><td>'.$langs->trans("PhonePro").'</td><td>'.dol_print_phone($object->phone_pro,$object->country_code,$object->id,$object->socid,'AC_TEL').'</td>';
        print '<td colspan="2"></td></tr>';

        print '<tr><td valign="top">'.$langs->trans("Note").'</td><td colspan="3">';
        print nl2br($object->note);
        print '</td></tr>';

        print "</table>";

        print "</div>";

        // Barre d'actions
        if (! $user->societe_id)
        {
            print '<div class="tabsAction">';

            if ($user->rights->nltechno->dolicloud->create)
            {
                print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Modify').'</a>';
            }

            if (! $object->user_id && $user->rights->nltechno->dolicloud->create)
            {
                print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=refresh">'.$langs->trans("Refresh").'</a>';
            }

            if ($user->rights->nltechno->dolicloud->create)
            {
                print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a>';
            }

            print "</div><br>";
        }

        /*
        print load_fiche_titre($langs->trans("TasksHistoryForThisCustomer"),'','');

        print show_actions_todo($conf,$langs,$db,'',$object);

        print show_actions_done($conf,$langs,$db,'',$object);
        */
    }
}


llxFooter();

$db->close();
?>
