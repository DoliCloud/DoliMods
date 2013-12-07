<?php
/* Copyright (C) 2001-2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013      Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012      Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2010           Juanjo Menent        <jmenent@2byte.es>
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
 *   \file       htdocs/cabinetmed/notes.php
 *   \brief      Tab for notes on third party
 *   \ingroup    societe
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
include_once("./class/patient.class.php");
include_once("./lib/cabinetmed.lib.php");

$langs->load("companies");
$langs->load("cabinetmed@cabinetmed");

$action = isset($_GET["action"])?$_GET["action"]:$_POST["action"];

$langs->load("companies");

// Security check
$socid = GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid, '&societe');

$object = new Patient($db);
if ($socid > 0) $object->fetch($socid);

/*
 * Actions
 */

if ($action == 'add' && ! GETPOST('cancel'))
{
    $error=0;

    $db->begin();

    $result=$object->update_note(dol_html_entity_decode(dol_htmlcleanlastbr(GETPOST('note_private')?GETPOST('note_private'):GETPOST('note')), ENT_QUOTES),'_private');
    if ($result < 0)
    {
        $error++;
        $errors[]=$object->errors;
    }

    $alert_note=($_POST["alert_note"]?'1':'0');
    $result=addAlert($db, 'alert_note', $socid, $alert_note);

    if ($result == '')
    {
         $object->alert_note=$alert_note;
         $mesgs[]=$langs->trans("RecordModifiedSuccessfully");
    }
    else
    {
        $error++;
        $errmesgs[]=$result;
    }

    if (! $error) $db->commit();
    else $db->rollback();
}


/*
 *	View
 */

if ($conf->global->MAIN_DIRECTEDITMODE && $user->rights->societe->creer) $action='edit';

$form = new Form($db);

llxHeader('',$langs->trans("Patient").' - '.$langs->trans("Notes"),$help_url);

if ($socid > 0)
{
    /*
     * Affichage onglets
     */
    if ($conf->notification->enabled) $langs->load("mails");

    $head = societe_prepare_head($object);


    dol_fiche_head($head, 'tabnotes', $langs->trans("Patient"),0,'company');



    print '<script type="text/javascript">
        var changed=false;
        jQuery(function() {
            jQuery(window).bind(\'beforeunload\', function(){
                /* alert(changed); */
                if (changed) return \''.dol_escape_js($langs->transnoentitiesnoconv("WarningExitPageWithoutSaving")).'\';
            });
            jQuery(".flat").keydown(function (e) {
    			changed=true;
            });
            jQuery("#alert_note").change(function () {
			    changed=true;
            });
            jQuery(".ignorechange").click(function () {
                changed=false;
            });
         });
        </script>';

    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

    print '<table class="border" width="100%">';

    print '<tr><td width="20%">'.$langs->trans('PatientName').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($object,'socid','',($user->societe_id?0:1),'rowid','nom');
    print '</td></tr>';

    if (! empty($conf->global->SOCIETE_USEPREFIX))  // Old not used prefix field
    {
        print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$object->prefix_comm.'</td></tr>';
    }

    if ($object->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $object->code_client;
        if ($object->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($object->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $object->code_fournisseur;
        if ($object->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }

    print '<tr><td valign="top">'.$langs->trans("Note");
    print '<br><input type="checkbox" id="alert_note" name="alert_note"'.((isset($_POST['alert_note'])?GETPOST('alert_note'):$object->alert_note)?' checked="checked"':'').'"> '.$langs->trans("Alert");
    print '</td>';
    print '<td valign="top">';
    $note=($object->note_private?$object->note_private:$object->note);
    if ($user->rights->societe->creer)
    {
        print '<input type="hidden" name="action" value="add" />';
        print '<input type="hidden" name="socid" value="'.$object->id.'" />';

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note',$note,'',360,'dolibarr_notes','In',true,false,$conf->global->FCKEDITOR_ENABLE_SOCIETE,20,70);
        $doleditor->Create(0,'.on( \'saveSnapshot\', function(e) { changed=true; });');
    }
    else
    {
        print dol_textishtml($note)?$note:dol_nl2br($note,1,true);
    }
    print "</td></tr>";

    print "</table>";

    if ($user->rights->societe->creer)
    {
        print '<center><br>';
        print '<input type="submit" class="button ignorechange" name="save" value="'.$langs->trans("Save").'">';
        print '</center>';
    }

    print '</form>';

    dol_fiche_end();
}

dol_htmloutput_mesg('',$mesgs);
dol_htmloutput_errors('',$errmesgs);


llxFooter();

$db->close();
?>
