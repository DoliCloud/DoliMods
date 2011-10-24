<?php
/* Copyright (C) 2001-2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011      Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2006      Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2010           Juanjo Menent        <jmenent@2byte.es>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *   \file       htdocs/cabinetmed/antecedant.php
 *   \brief      Tab for antecedants
 *   \ingroup    societe
 *   \version    $Id: antecedant.php,v 1.12 2011/08/24 00:17:29 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
include_once("./class/patient.class.php");

$langs->load("companies");
$langs->load("cabinetmed@cabinetmed");

$action = GETPOST('action');
if (empty($action)) $action='edit';

// Security check
$socid = GETPOST('socid');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid);

if (!$user->rights->cabinetmed->read) accessforbidden();


/*
 * Actions
 */
if ($action == 'addupdate')
{
    $sql = "INSERT INTO ".MAIN_DB_PREFIX."cabinetmed_patient(rowid, note_antemed, note_antechirgen, note_antechirortho, note_anterhum, note_other, note_traitallergie)";
    $sql.= " VALUES('".$_POST["socid"]."','".addslashes($_POST["note_antemed"])."','".addslashes($_POST["note_antechirgen"])."',";
    $sql.= " '".addslashes($_POST["note_antechirortho"])."','".addslashes($_POST["note_anterhum"])."','".addslashes($_POST["note_other"])."','".addslashes($_POST["note_traitallergie"])."')";
    $result = $db->query($sql);
    //if (! $result) dol_print_error($db);

    $sql = "UPDATE ".MAIN_DB_PREFIX."cabinetmed_patient SET";
    $sql.= " note_antemed='".addslashes($_POST["note_antemed"])."',";
    $sql.= " note_antechirgen='".addslashes($_POST["note_antechirgen"])."',";
    $sql.= " note_antechirortho='".addslashes($_POST["note_antechirortho"])."',";
    $sql.= " note_anterhum='".addslashes($_POST["note_anterhum"])."',";
    //$sql.= " note_other='".addslashes($_POST["note_other"])."',";
    $sql.= " note_traitallergie='".addslashes($_POST["note_traitallergie"])."'";
    $sql.= " WHERE rowid=".$_POST["socid"];
    $result = $db->query($sql);
    if (! $result) dol_print_error($db);
    else $mesg=$langs->trans("RecordModifiedSuccessfully");

    $action='edit';
}


/*
 *	View
 */

$form = new Form($db);

llxHeader('',$langs->trans("ATCD"));


if ($socid > 0)
{
    $societe = new Patient($db);
    $res=$societe->fetch($socid);
    if ($res < 0)
    {
        dol_print_error($db,$societe->error);
    }
    $societe->id=$socid;

    /*
     * Affichage onglets
     */
    if ($conf->notification->enabled) $langs->load("mails");

    $head = societe_prepare_head($societe);

    dol_fiche_head($head, 'tabantecedents', $langs->trans("ThirdParty"),0,'company');


    print '<script type="text/javascript">
    var changed=false;
    jQuery(function() {
        jQuery(window).bind(\'beforeunload\', function(){
            /* alert(changed); */
            if (changed) return \''.dol_escape_js($langs->transnoentitiesnoconv("WarningExitPageWithoutSaving")).'\';
        });
        jQuery(".flat").change(function () {
            changed=true;
        });
        jQuery(".ignorechange").click(function () {
            changed=false;
        });
     });
    </script>';

    print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="socid" value="'.$societe->id.'">';
    print '<input type="hidden" name="action" value="addupdate">';

    print '<table class="border" width="100%">';

    print '<tr><td width="25%">'.$langs->trans('ThirdPartyName').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($societe,'socid','',($user->societe_id?0:1),'rowid','nom');
    print '</td></tr>';

    if ($societe->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $societe->code_client;
        if ($societe->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($conf->fournisseur->enabled && $societe->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $societe->code_fournisseur;
        if ($societe->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }


    $conf->fckeditor->enabled=false;
    $height=120;

    print '<tr height="80"><td valign="top">'.$langs->trans("AntecedentsMed").'</td>';
    print '<td valign="top">';
    if ($action == 'edit' && $user->rights->societe->creer)
    {
        print "<input type=\"hidden\" name=\"socid\" value=\"".$societe->id."\">";

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note_antemed',$societe->note_antemed,0,$height,'dolibarr_notes','In',false,false,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE,6,70);
        $doleditor->Create();
    }
    else
    {
        print nl2br($societe->note_antemed);
    }
    print "</td></tr>";

    print '<tr height="80"><td valign="top">'.$langs->trans("AntecedentsChirGene").'</td>';
    print '<td valign="top">';
    if ($action == 'edit' && $user->rights->societe->creer)
    {
        print "<input type=\"hidden\" name=\"socid\" value=\"".$societe->id."\">";

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note_antechirgen',$societe->note_antechirgen,0,$height,'dolibarr_notes','In',false,false,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE,6,70);
        $doleditor->Create();
    }
    else
    {
        print nl2br($societe->note_antechirgen);
    }
    print "</td></tr>";

    print '<tr height="80"><td valign="top">'.$langs->trans("AntecedentsChirOrtho").'</td>';
    print '<td valign="top">';
    if ($action == 'edit' && $user->rights->societe->creer)
    {
        print "<input type=\"hidden\" name=\"socid\" value=\"".$societe->id."\">";

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note_antechirortho',$societe->note_antechirortho,0,$height,'dolibarr_notes','In',false,false,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE,6,70);
        $doleditor->Create();
    }
    else
    {
        print nl2br($societe->note_antechirortho);
    }
    print "</td></tr>";

    print '<tr height="80"><td valign="top">'.$langs->trans("AntecedentsRhumato").'</td>';
    print '<td valign="top">';
    if ($action == 'edit' && $user->rights->societe->creer)
    {
        print "<input type=\"hidden\" name=\"socid\" value=\"".$societe->id."\">";

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note_anterhum',$societe->note_anterhum,0,$height,'dolibarr_notes','In',false,false,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE,6,70);
        $doleditor->Create();
    }
    else
    {
        print nl2br($societe->note_anterhum);
    }
    print "</td></tr>";

    /*
    print '<tr height="80"><td valign="top">'.$langs->trans("Other").'</td>';
    print '<td valign="top">';
    if ($action == 'edit' && $user->rights->societe->creer)
    {
        print "<input type=\"hidden\" name=\"socid\" value=\"".$societe->id."\">";

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note_other',$societe->note_other,0,$height,'dolibarr_notes','In',false,false,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE,6,70);
        $doleditor->Create();
    }
    else
    {
        print nl2br($societe->note_other);
    }
    print "</td></tr>";
    */


    print '<tr height="80"><td valign="top">'.$langs->trans("Allergies").'</td>';
    print '<td valign="top">';
    if ($action == 'edit' && $user->rights->societe->creer)
    {
        print "<input type=\"hidden\" name=\"socid\" value=\"".$societe->id."\">";

        // Editeur wysiwyg
        require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
        $doleditor=new DolEditor('note_traitallergie',$societe->note_traitallergie,0,$height,'dolibarr_notes','In',false,false,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE,6,70);
        $doleditor->Create();
    }
    else
    {
        print nl2br($societe->note_traitallergie);
    }
    print "</td></tr>";

    print "</table>";


    if ($action == 'edit')
    {
        print '<center><br><input type="submit" class="button ignorechange" value="'.$langs->trans("Save").'"></center>';
    }

    print '</form>';
}

print '</div>';


/*
 * Boutons actions
 */
if ($action == '')
{
    print '<div class="tabsAction">';

    if ($user->rights->societe->creer)
    {
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?socid='.$societe->id.'&amp;action=edit">'.$langs->trans("Modify").'</a>';
    }

    print '</div>';
}


dol_htmloutput_mesg($mesg);


$db->close();

llxFooter('$Date: 2011/08/24 00:17:29 $ - $Revision: 1.12 $');
?>
