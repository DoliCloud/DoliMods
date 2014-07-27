<?php
/* Copyright (C) 2004-2014      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   \file       htdocs/cabinetmed/examautre.php
 *   \brief      Tab for examens other
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
include_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");
include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
include_once("./lib/cabinetmed.lib.php");
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");
include_once("./class/cabinetmedexamother.class.php");

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
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield='t.dateexam';
if (! $sortorder) $sortorder='DESC';
$limit = $conf->liste_limit;

$examother = new CabinetmedExamOther($db);


/*
 * Actions
 */

// Delete exam
if (GETPOST("action") == 'confirm_delete' && GETPOST("confirm") == 'yes' && $user->rights->societe->supprimer)
{
    $examother->fetch($id);
    $result = $examother->delete($user);
    if ($result >= 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"].'?socid='.$socid);
        exit;
    }
    else
    {
        $langs->load("errors");
        $mesg=$langs->trans($examother->error);
        $action='';
    }
}


if ($action == 'add' || $action == 'update')
{
    if (! GETPOST('cancel'))
    {
        $error=0;

        $dateexam=dol_mktime(0,0,0,$_POST["exammonth"],$_POST["examday"],$_POST["examyear"]);

        if ($action == 'update')
        {
            $result=$examother->fetch($id);
            if ($result <= 0)
            {
                dol_print_error($db,$examother);
                exit;
            }
        }
        $examother->fk_soc=$_POST["socid"];
        $examother->dateexam=$dateexam;
        $examother->examprinc=trim($_POST["examprinc"]);
        $examother->examsec=trim($_POST["examsec"]);
        $examother->concprinc=$_POST["examconcprinc"];
        $examother->concsec=$_POST["examconcsec"];

        if (empty($examother->examprinc))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("ExamenPrescrit"));
        }
        if (empty($examother->concprinc))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("ExamenResultat"));
        }
        if (empty($dateexam))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Date"));
        }

        $db->begin();

        if (! $error)
        {
            if ($action == 'add')
            {
                $result=$examother->create($user);
            }
            if ($action == 'update')
            {
                $result=$examother->update($user);
            }
            if ($result < 0)
            {
                $mesgarray[]=$examother->error;
                $error++;
            }
        }

        if (! $error)
        {
            $db->commit();
            header("Location: ".$_SERVER["PHP_SELF"].'?socid='.$examother->fk_soc);
            exit(0);
        }
        else
        {
            $db->rollback();
            $mesgarray[]=$examother->error;
            if ($action == 'add')    $action='create';
            if ($action == 'update') $action='edit';
        }
    }
    else
    {
        $action='';
    }
}



/*
 *  View
 */

$form = new Form($db);
$width="328";

llxHeader('',$langs->trans("ResultExamAutre"));

if ($socid > 0)
{
    $societe = new Societe($db);
    $societe->fetch($socid);

    if ($id && ! $examother->id)
    {
        $result=$examother->fetch($id);
        if ($result < 0) dol_print_error($db,$examother->error);
    }

    /*
     * Affichage onglets
     */
    if ($conf->notification->enabled) $langs->load("mails");

    $head = societe_prepare_head($societe);
    dol_fiche_head($head, 'tabexamautre', $langs->trans("Patient"),0,'company');

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
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $societe->code_client;
        if ($societe->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
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


    // Form to create
    if ($action == 'create' || $action == 'edit')
    {
        dol_fiche_end();
        dol_fiche_head();

        $x=1;
        $nboflines=4;

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
    		jQuery("#addexamprinc").click(function () {
                var t=jQuery("#listexam").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#examprinc").val(t);
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listexam").get(0).selectedIndex = 0;
                }
            });
            jQuery("#addexamsec").click(function () {
                var t=jQuery("#listexam").children( ":selected" ).text();
                if (t != "")
                {
                    if (jQuery("#examprinc").val() == t)
                    {
                        alert(\'Le motif "\'+t+\'" est deja en motif principal\');
                    }
                    else
                    {
                        var box = jQuery("#examsec");
                        u=box.val() + (box.val() != \'\' ? "\n" : \'\') + t;
                        box.val(u); box.html(u);
                        jQuery(".ui-autocomplete-input").val("");
                        jQuery(".ui-autocomplete-input").text("");
                        jQuery("#listexam").get(0).selectedIndex = 0;
                    }
                }
            });
            jQuery("#addexamconcprinc").click(function () {
                var t=jQuery("#listexamconc").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#examconcprinc").val(t);
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listexamconc").get(0).selectedIndex = 0;
                }
            });
            jQuery("#addexamconcsec").click(function () {
                var t=jQuery("#listexamconc").children( ":selected" ).text();
                if (t != "")
                {
                    var box = jQuery("#examconcsec");
                    u=box.val() + (box.val() != \'\' ? "\n" : \'\') + t;
                    box.val(u); box.html(u);
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listexamconc").get(0).selectedIndex = 0;
                }
            });
        });
        </script>';

        print '
            <style>
            .ui-autocomplete-input { width: '.$width.'px; }
            </style>
            ';

        print ajax_combobox('listexam');
        print ajax_combobox('listexamconc');

        //print_fiche_titre($langs->trans("NewConsult"),'','');

        // General
        print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
        if ($action=='create') print '<input type="hidden" name="action" value="add">';
        if ($action=='edit')   print '<input type="hidden" name="action" value="update">';
        print '<input type="hidden" name="socid" value="'.$socid.'">';
        print '<input type="hidden" name="id" value="'.$id.'">';

        print '<fieldset id="fieldsetanalyse">';
        print '<legend>'.$langs->trans("Examen");
        if ($action=='edit' || $action=='update')
        {
            print ' - '.$langs->trans("Numero").': <strong>'.sprintf("%08d",$examother->id).'</strong>';
        }
        if ($examother->fk_user > 0)
        {
        	$fuser=new User($db);
        	$fuser->fetch($examother->fk_user);
        	print ' - '.$langs->trans("CreatedBy").': <strong>'.$fuser->getFullName($langs).'</strong>';
        }
        print '</legend>'."\n";

        print '<table class="notopnoleftnoright" width="100%">';
        print '<tr><td width="60%" class="fieldrequired">';
        print $langs->trans("Date").': ';
        $form->select_date($examother->dateexam,'exam');
        print '</td><td>';
        print '</td></tr>';

        print '</table>';
        //print '</fieldset>';

        //print '<br>';

        // Analyse
//        print '<fieldset id="fieldsetanalyse">';
//        print '<legend>'.$langs->trans("Diagnostiques et prescriptions").'</legend>'."\n";
        print '<hr style="height:1px; color: #dddddd;">';

        print '<table class="notopnoleftnoright" width="100%">';
        print '<tr><td width="60%">';

        print '<table class="notopnoleftnoright" width="100%">';

        print '<tr><td valign="top" width="160">';
        print $langs->trans("ExamenPrescrit").':';
        print '</td><td nowrap="nowrap">';
        //print '<input type="text" size="3" class="flat" name="searchmotifcons" value="'.GETPOST("searchmotifcons").'" id="searchmotifcons">';
        listexamen(1,$width,"RADIO','OTHER','AUTRE",0,'exam');
        /*print ' '.img_picto('Ajouter motif principal','edit_add_p.png@cabinetmed');
        print ' '.img_picto('Ajouter motif secondaire','edit_add_s.png@cabinetmed');*/
        print ' <input type="button" class="button" id="addexamprinc" name="addexamprinc" value="+P">';
        print ' <input type="button" class="button" id="addexamsec" name="addexamsec" value="+S">';
        if ($user->admin) print ' '.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        print '</td></tr>';
        print '<tr><td class="fieldrequired">'.$langs->trans("Principal").':';
        print '</td><td>';
        print '<input type="text" size="48" class="flat" name="examprinc" value="'.$examother->examprinc.'" id="examprinc"><br>';
        print '</td></tr>';
        print '<tr><td valign="top">'.$langs->trans("Secondaires").':';
        print '</td><td>';
        print '<textarea class="flat" name="examsec" id="examsec" cols="46" rows="'.ROWS_5.'">';
        print $examother->examsec;
        print '</textarea>';
        print '</td>';
        print '</tr>';

        print '<tr><td><br></td></tr>';

        print '<tr><td valign="top" width="160">';
        print $langs->trans("ExamenResultat").':';
        print '</td><td nowrap="nowrap">';
        //print '<input type="text" size="3" class="flat" name="searchdiagles" value="'.GETPOST("searchdiagles").'" id="searchdiagles">';
        listexamconclusion(1,$width,'examconc');
        print ' <input type="button" class="button" id="addexamconcprinc" name="addexamconcprinc" value="+P">';
        print ' <input type="button" class="button" id="addexamconcsec" name="addexamconcsec" value="+S">';
        if ($user->admin) print ' '.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        print '</td></tr>';
        print '<tr><td class="fieldrequired">'.$langs->trans("Principal").':';
        print '</td><td>';
        print '<input type="text" size="48" class="flat" name="examconcprinc" value="'.$examother->concprinc.'" id="examconcprinc"><br>';
        print '</td></tr>';
        print '<tr><td valign="top">'.$langs->trans("Secondaires").':';
        print '</td><td>';
        print '<textarea class="flat" name="examconcsec" id="examconcsec" cols="46" rows="'.ROWS_5.'">';
        print $examother->concsec;
        print '</textarea>';
        print '</td>';
        print '</tr>';

        print '</table>';

        print '</td><td valign="top">';


        print '</td></tr>';

        print '</table>';
        print '</fieldset>';

        print '<br>';

        dol_htmloutput_errors($mesg,$mesgarray);


        print '<center>';
        if ($action == 'edit')
        {
            print '<input type="submit" class="button ignorechange" name="update" value="'.$langs->trans("Save").'">';
        }
        if ($action == 'create')
        {
            print '<input type="submit" class="button ignorechange" name="add" value="'.$langs->trans("Add").'">';
        }
        print ' &nbsp; &nbsp; ';
        print '<input type="submit" class="button ignorechange" name="cancel" value="'.$langs->trans("Cancel").'">';
        print '</center>';
        print '</form>';
    }


    dol_fiche_end();
}


/*
 * Boutons actions
 */
if ($action == '' || $action == 'delete')
{
    print '<div class="tabsAction">';

    if ($user->rights->societe->creer)
    {
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?socid='.$societe->id.'&amp;action=create">'.$langs->trans("NewExamAutre").'</a>';
    }

    print '</div>';
}


if ($action == '' || $action == 'delete')
{
    // Confirm delete exam
    if (GETPOST("action") == 'delete')
    {
        $html = new Form($db);
        $ret=$html->form_confirm($_SERVER["PHP_SELF"]."?socid=".$socid.'&id='.GETPOST('id','int'),$langs->trans("DeleteAnExam"),$langs->trans("ConfirmDeleteExam"),"confirm_delete",'',0,1);
        if ($ret == 'html') print '<br>';
    }


    print_fiche_titre($langs->trans("ListOfExamAutre"));

    $param='&socid='.$socid;

    print "\n";
    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    //print_liste_field_titre($langs->trans('Num'),$_SERVER['PHP_SELF'],'t.rowid','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.dateexam','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Examen'),$_SERVER['PHP_SELF'],'t.examprinc','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Conclusion'),$_SERVER['PHP_SELF'],'t.examsec','',$param,'',$sortfield,$sortorder);
    print '<td>&nbsp;</td>';
    print '</tr>';


    // List des consult
    $sql = "SELECT";
    $sql.= " t.rowid,";
    $sql.= " t.fk_soc,";
    $sql.= " t.dateexam,";
    $sql.= " t.examprinc,";
    $sql.= " t.examsec,";
    $sql.= " t.concprinc,";
    $sql.= " t.concsec,";
    $sql.= " t.tms";
    $sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_examaut as t";
    $sql.= " WHERE t.fk_soc = ".$socid;
    $sql.= " ORDER BY ".$sortfield." ".$sortorder.", t.rowid DESC";

    $resql=$db->query($sql);
    if ($resql)
    {
        $i = 0 ;
        $num = $db->num_rows($resql);
        $var=true;
        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);

            $var=!$var;
            print '<tr '.$bc[$var].'>';
            //print '<td>';
            //print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=edit">'.sprintf("%08d",$obj->rowid).'</a>';
            //print '</td>';
            print '<td>';
            print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=edit">';
            print dol_print_date($db->jdate($obj->dateexam),'day');
            print '</a>';
            print '</td><td>';
            print $obj->examprinc;
            print '</td><td>';
            print $obj->concprinc;
            print '</td>';
            print '<td align="right">';
            print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=edit">'.img_edit().'</a>';
            if ($user->rights->societe->supprimer)
            {
                print ' &nbsp; ';
                print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=delete">'.img_delete().'</a>';
            }
            print '</td>';
            print '</tr>';
            $i++;
        }
    }
    else
    {
        dol_print_error($db);
    }
}


llxFooter();

$db->close();
?>
