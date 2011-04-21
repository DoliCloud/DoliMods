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
 *   \file       htdocs/cabinetmed/exambio.php
 *   \brief      Tab for consultations
 *   \ingroup    cabinetmed
 *   \version    $Id: exambio.php,v 1.10 2011/04/21 22:30:15 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
include_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");
include_once("./lib/cabinetmed.lib.php");
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");
include_once("./class/cabinetmedexambio.class.php");

$action = GETPOST("action");
$id=GETPOST("id");  // Id consultation

$langs->load("companies");
$langs->load("bills");
$langs->load("banks");
$langs->load("cabinetmed@cabinetmed");

// Security check
$socid = GETPOST("socid");
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid);

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

$exambio = new CabinetmedExamBio($db);


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

// Add exam
if ($action == 'add' || $action == 'update')
{
    if (! GETPOST('cancel'))
    {
        $error=0;

        $dateexam=dol_mktime(0,0,0,$_POST["exammonth"],$_POST["examday"],$_POST["examyear"]);

        if ($action == 'update')
        {
            $result=$exambio->fetch($id);
            if ($result <= 0)
            {
                dol_print_error($db,$exambio);
                exit;
            }
        }

        $exambio->fk_soc=$_POST["socid"];
        $exambio->dateexam=$dateexam;
        $exambio->banque=trim($_POST["banque"]);
        $exambio->num_cheque=trim($_POST["num_cheque"]);
        $exambio->typepriseencharge=$_POST["typepriseencharge"];
        $exambio->motifconsprinc=$_POST["motifconsprinc"];
        $exambio->diaglesprinc=$_POST["diaglesprinc"];
        $exambio->motifconssec=$_POST["motifconssec"];
        $exambio->diaglessec=$_POST["diaglessec"];
        $exambio->examenclinique=trim($_POST["examenclinique"]);
        $exambio->examenprescrit=trim($_POST["examenprescrit"]);
        $exambio->traitementprescrit=trim($_POST["traitementprescrit"]);
        $exambio->comment=trim($_POST["comment"]);
        $exambio->typevisit=$_POST["typevisit"];
        $exambio->infiltration=trim($_POST["infiltration"]);
        $exambio->codageccam=trim($_POST["ccam"]);

        $nbnotempty=0;
        if (! empty($exambio->montant_cheque)) $nbnotempty++;
        if (! empty($exambio->montant_espece)) $nbnotempty++;
        if (! empty($exambio->montant_carte))  $nbnotempty++;
        if (! empty($exambio->montant_tiers))  $nbnotempty++;
        if ($nbnotempty==0)
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"));
        }
        if ($nbnotempty > 1)
        {
            $error++;
            $mesgarray[]=$langs->trans("Un seul champ montant possible à la fois");
        }
        if ($exambio->montant_cheque && empty($exambio->banque))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("ChequeBank"));
        }
        if (empty($exambio->typevisit))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("TypeVisite"));
        }
        if (empty($dateexam))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Date"));
        }
        if (empty($exambio->motifconsprinc))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("MotifConsultation"));
        }

        $db->begin();

        if (! $error)
        {
            if ($action == 'add')
            {
                $amount='';
                if (! empty($_POST["montant_cheque"])) $amount=$exambio->montant_cheque;
                if (! empty($_POST["montant_carte"])) $amount=$exambio->montant_carte;
                if (! empty($_POST["montant_espece"])) $amount=$exambio->montant_espece;
                if (! empty($_POST["montant_tiers"])) $amount=$exambio->montant_tiers;
                $banque='';
                if (! empty($_POST["bankchequeto"])) $banque=$_POST["bankchequeto"];
                if (! empty($_POST["bankcarteto"])) $banque=$_POST["bankcarteto"];
                if (! empty($_POST["bankespeceto"])) $banque=$_POST["bankespeceto"];
                if (! empty($_POST["banktiersto"])) $banque=$_POST["banktiersto"];
                $type='';
                if (! empty($_POST["montant_cheque"])) $type='CHQ';
                if (! empty($_POST["montant_carte"])) $type='CB';
                if (! empty($_POST["montant_espece"])) $type='LIQ';
                if (! empty($_POST["montant_tiers"])) $type='VIR';

                $result=$exambio->create($user);

                $societe = new Societe($db);
                $societe->fetch($exambio->fk_soc);

                $bankaccount=new Account($db);
                $result=$bankaccount->fetch($banque);
                $lineid=$bankaccount->addline(dol_now(), $type, $langs->trans("CustomerInvoicePayment"), $amount, $exambio->num_cheque, '', $user, $societe->nom, $exambio->banque);
                if ($lineid <= 0)
                {
                    $error++;
                    $exambio->error=$bankaccount->error;
                }
                if (! $error)
                {
                    $result1=$bankaccount->add_url_line($lineid,$exambio->id,dol_buildpath('/cabinetmed/consultations.php',1).'?action=edit&socid='.$exambio->fk_soc.'&id=','Consultation','consultation');
                    $result2=$bankaccount->add_url_line($lineid,$exambio->fk_soc,'',$societe->nom,'company');
                    if ($result1 <= 0 || $result2 <= 0)
                    {
                        $error++;
                    }
                }
            }
            if ($action == 'update')
            {
                $result=$exambio->update($user);

                // Search if there is a bank line
                $bid=0;
                $sql.= "SELECT b.rowid FROM ".MAIN_DB_PREFIX."bank_url as bu, ".MAIN_DB_PREFIX."bank as b";
                $sql.= " WHERE bu.url_id = ".$exambio->id." AND type = 'consultation'";
                $sql.= " AND bu.fk_bank = b.rowid";
                dol_syslog($sql);
                $resql=$db->query($sql);
                if ($resql)
                {
                    $obj=$db->fetch_object($resql);
                    if ($obj)
                    {
                        $bid=$obj->rowid;
                    }
                }
                else
                {
                    $error++;
                    $exambio->error=$db->lasterror();
                }

                if (! $error)
                {
                    // If bid
                    if ($bid)
                    {
                        $bankaccountline=new AccountLine($db);
                        $result=$bankaccountline->fetch($bid);
                        $bankaccountline->delete($user);
                    }

                    $amount='';
                    if (! empty($_POST["montant_cheque"])) $amount=$exambio->montant_cheque;
                    if (! empty($_POST["montant_carte"])) $amount=$exambio->montant_carte;
                    if (! empty($_POST["montant_espece"])) $amount=$exambio->montant_espece;
                    if (! empty($_POST["montant_tiers"])) $amount=$exambio->montant_tiers;
                    $banque='';
                    if (! empty($_POST["bankchequeto"])) $banque=$_POST["bankchequeto"];
                    if (! empty($_POST["bankcarteto"])) $banque=$_POST["bankcarteto"];
                    if (! empty($_POST["bankespeceto"])) $banque=$_POST["bankespeceto"];
                    if (! empty($_POST["banktiersto"])) $banque=$_POST["banktiersto"];
                    $type='';
                    if (! empty($_POST["montant_cheque"])) $type='CHQ';
                    if (! empty($_POST["montant_carte"])) $type='CB';
                    if (! empty($_POST["montant_espece"])) $type='LIQ';
                    if (! empty($_POST["montant_tiers"])) $type='VIR';

                    $bankaccount=new Account($db);
                    $result=$bankaccount->fetch($banque);
                    $lineid=$bankaccount->addline(dol_now(), $type, $langs->trans("CustomerInvoicePayment"), $amount, $exambio->num_cheque, '', $user, $societe->nom, $exambio->banque);
                    $result1=$bankaccount->add_url_line($lineid,$exambio->id,dol_buildpath('/cabinetmed/consultations.php',1).'?action=edit&socid='.$exambio->fk_soc.'&id=','Consultation','consultation');
                    $result2=$bankaccount->add_url_line($lineid,$exambio->fk_soc,'',$societe->nom,'company');
                    if ($lineid <= 0 || $result1 <= 0 || $result2 <= 0)
                    {
                        $error++;
                    }
                }
            }
        }

        if (! $error)
        {
            $db->commit();
            header("Location: ".$_SERVER["PHP_SELF"].'?socid='.$exambio->fk_soc);
            exit(0);
        }
        else
        {
            $db->rollback();
            $mesgarray[]=$exambio->error;
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
 *	View
 */

$form = new Form($db);
$width="242";

llxHeader();

if ($socid > 0)
{
    $societe = new Societe($db);
    $societe->fetch($socid);

    if ($id && ! $exambio->id)
    {
        $result=$exambio->fetch($id);
        if ($result < 0) dol_print_error($db,$exambio->error);
    }

	/*
	 * Affichage onglets
	 */
    if ($conf->notification->enabled) $langs->load("mails");

	$head = societe_prepare_head($societe);
	dol_fiche_head($head, 'tabexambio', $langs->trans("ThirdParty"),0,'company');

	print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

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
        jQuery(function() {
            jQuery("#addmotifprinc").click(function () {
                /*alert(jQuery("#listmotifcons option:selected" ).val());
                alert(jQuery("#listmotifcons option:selected" ).text());*/
                var t=jQuery("#listmotifcons").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#motifconsprinc").val(t);
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listmotifcons").get(0).selectedIndex = 0;
                }
            });
            jQuery("#addmotifsec").click(function () {
                var t=jQuery("#listmotifcons").children( ":selected" ).text();
                if (t != "")
                {
                    if (jQuery("#motifconsprinc").val() == t)
                    {
                        alert(\'Le motif "\'+t+\'" est deja en motif principal\');
                    }
                    else
                    {
                        jQuery("#motifconssec").append(t+"\n");
                        jQuery(".ui-autocomplete-input").val("");
                        jQuery(".ui-autocomplete-input").text("");
                        jQuery("#listmotifcons").get(0).selectedIndex = 0;
                    }
                }
            });
            jQuery("#adddiaglesprinc").click(function () {
                var t=jQuery("#listdiagles").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#diaglesprinc").val(t);
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listdiagles").get(0).selectedIndex = 0;
                }
            });
            jQuery("#adddiaglessec").click(function () {
                var t=jQuery("#listdiagles").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#diaglessec").append(t+"\n");
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listmotifcons").get(0).selectedIndex = 0;
                }
            });
        });
        </script>';

        print '
            <style>
            .ui-autocomplete-input { width: '.$width.'px; }
            </style>
            ';

        print '
            <script>
            jQuery(function() {
            });
            </script>
                ';

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
            print ' - '.$langs->trans("ExamBioNumero").': '.sprintf("%08d",$exambio->id).'<br><br>';
        }
        print '</legend>'."\n";

        print '<table class="notopnoleftnoright" width="100%">';
        print '<tr><td width="60%">';
        print $langs->trans("Date").': ';
        $form->select_date($dateexam,'exam');
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
        print $langs->trans("Result").':<br>';
        print '<textarea name="result" id="result" cols="60" rows="'.ROWS_9.'">';
        print $exambio->result;
        print '</textarea>';
        print '</td>';
        print '</tr>';
        print '</table>';

        print '</td><td valign="top">';

        print $langs->trans("Conclusion").':<br>';
        print '<textarea name="conclusion" id="conclusion" cols="60" rows="'.ROWS_4.'">';
        print $exambio->conclusion;
        print '</textarea>';

        print '<br>';

        print $langs->trans("Comment").':<br>';
        print '<textarea name="comment" id="comment" cols="60" rows="'.ROWS_4.'">';
        print $exambio->comment;
        print '</textarea>';

        print '</td></tr>';

        print '</table>';

        print '<hr style="height:1px; color: #dddddd;">';

        print 'eeeee';

        print '</fieldset>';

        print '<br>';

        dol_htmloutput_errors($mesg,$mesgarray);


        print '<center>';
        if ($action == 'edit')
        {
            print '<input type="submit" class="button" name="update" value="'.$langs->trans("Save").'">';
        }
        if ($action == 'create')
        {
            print '<input type="submit" class="button" name="add" value="'.$langs->trans("Add").'">';
        }
        print ' &nbsp; &nbsp; ';
        print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
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
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?socid='.$societe->id.'&amp;action=create">'.$langs->trans("NewExamBio").'</a>';
    }

    print '</div>';
}


if ($action == '' || $action == 'delete')
{
    // Confirm delete exam
    if (GETPOST("action") == 'delete')
    {
        $html = new Form($db);
        $ret=$html->form_confirm($_SERVER["PHP_SELF"]."?socid=".$socid.'&id='.GETPOST('id'),$langs->trans("DeleteAnExam"),$langs->trans("ConfirmDeleteExam"),"confirm_delete",'',0,1);
        if ($ret == 'html') print '<br>';
    }


    print_fiche_titre($langs->trans("ListOfExamBio"));

    $param='&socid='.$socid;

    print "\n";
    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    //print_liste_field_titre($langs->trans('Num'),$_SERVER['PHP_SELF'],'t.rowid','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.dateexam','',$param,'',$sortfield,$sortorder);
    print '<td>&nbsp;</td>';
    print '</tr>';


    // List des consult
    $sql = "SELECT";
    $sql.= " t.rowid,";
    $sql.= " t.fk_soc,";
    $sql.= " t.dateexam,";
    $sql.= " t.tms";
    $sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_exambio as t";
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
            print dol_print_date($db->jdate($obj->dateexambio),'day');
            print '</a>';
            print '</td>';
            /*print '<td>';
            print $obj->typepriseencharge;
            print '</td><td>';
            print dol_trunc($obj->motifconsprinc,32);
            print '</td>';
            print '<td>';
            //print dol_print_date($obj->diaglesprinc,'day');
            //print '</td><td>';
            print $obj->typevisit;
            print '</td>';
            if (price2num($obj->montant_cheque) > 0)
            {
                print '<td>';
                print price($obj->montant_cheque);
                print '</td><td>';
                print 'Cheque';
                print '</td>';
            }
            if (price2num($obj->montant_carte) > 0)
            {
                print '<td>';
                print price($obj->montant_carte);
                print '</td><td>';
                print 'Carte';
                print '</td>';
            }
            if (price2num($obj->montant_espece) > 0)
            {
                print '<td>';
                print price($obj->montant_espece);
                print '</td><td>';
                print 'Espece';
                print '</td>';
            }
            if (price2num($obj->montant_tiers) > 0)
            {
                print '<td>';
                print price($obj->montant_tiers);
                print '</td><td>';
                print 'Tiers';
                print '</td>';
            }
            if ($conf->banque->enabled)
            {
                print '<td>';
                if ($obj->fk_account)
                {
                    $bank=new Account($db);
                    $bank->fetch($obj->fk_account);
                    print $bank->getNomUrl(1,'transactions');
                }
                print '</td>';
            }
            */
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


$db->close();

llxFooter('$Date: 2011/04/21 22:30:15 $ - $Revision: 1.10 $');
?>
