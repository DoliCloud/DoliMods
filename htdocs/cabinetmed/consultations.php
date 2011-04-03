<?php
/* Copyright (C) 2004-2011      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   \file       htdocs/cabinetmed/consultations.php
 *   \brief      Tab for consultations
 *   \ingroup    cabinetmed
 *   \version    $Id: consultations.php,v 1.8 2011/04/03 21:06:03 eldy Exp $
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
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");

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
if (! $sortfield) $sortfield='t.datecons';
if (! $sortorder) $sortorder='DESC';
$limit = $conf->liste_limit;

$consult = new CabinetmedCons($db);


/*
 * Actions
 */

if ($action == 'add' || $action == 'update')
{
    if (! GETPOST('cancel'))
    {
        $error=0;

        $datecons=dol_mktime(0,0,0,$_POST["consmonth"],$_POST["consday"],$_POST["consyear"]);

        if ($action == 'update')
        {
            $result=$consult->fetch($id);
            if ($result <= 0)
            {
                dol_print_error($db,$consult);
                exit;
            }
        }

        $consult->fk_soc=$_POST["socid"];
        $consult->datecons=$datecons;
        unset($consult->montant_carte);
        unset($consult->montant_cheque);
        unset($consult->montant_tiers);
        unset($consult->montant_espece);
        if (GETPOST("montant_cheque") != '') $consult->montant_cheque=price2num($_POST["montant_cheque"]);
        if (GETPOST("montant_espece") != '') $consult->montant_espece=price2num($_POST["montant_espece"]);
        if (GETPOST("montant_carte") != '')  $consult->montant_carte=price2num($_POST["montant_carte"]);
        if (GETPOST("montant_tiers") != '')  $consult->montant_tiers=price2num($_POST["montant_tiers"]);
        $consult->banque=trim($_POST["banque"]);
        $consult->num_cheque=trim($_POST["num_cheque"]);
        $consult->typepriseencharge=$_POST["typepriseencharge"];
        $consult->motifconsprinc=$_POST["motifconsprinc"];
        $consult->diaglesprinc=$_POST["diaglesprinc"];
        $consult->motifconssec=$_POST["motifconssec"];
        $consult->diaglessec=$_POST["diaglessec"];
        $consult->examenclinique=trim($_POST["examenclinique"]);
        $consult->examenprescrit=trim($_POST["examenprescrit"]);
        $consult->traitementprescrit=trim($_POST["traitementprescrit"]);
        $consult->comment=trim($_POST["comment"]);
        $consult->typevisit=$_POST["typevisit"];
        $consult->infiltration=trim($_POST["infiltration"]);
        $consult->codageccam=trim($_POST["ccam"]);

        $nbnotempty=0;
        if (! empty($consult->montant_cheque)) $nbnotempty++;
        if (! empty($consult->montant_espece)) $nbnotempty++;
        if (! empty($consult->montant_carte))  $nbnotempty++;
        if (! empty($consult->montant_tiers))  $nbnotempty++;
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
        if ($consult->montant_cheque && empty($consult->banque))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("ChequeBank"));
        }
        if (empty($consult->typevisit))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("TypeVisite"));
        }
        if (empty($datecons))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Date"));
        }
        if (empty($consult->motifconsprinc))
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
                if (! empty($_POST["montant_cheque"])) $amount=$consult->montant_cheque;
                if (! empty($_POST["montant_carte"])) $amount=$consult->montant_carte;
                if (! empty($_POST["montant_espece"])) $amount=$consult->montant_espece;
                if (! empty($_POST["montant_tiers"])) $amount=$consult->montant_tiers;
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

                $result=$consult->create($user);

                $societe = new Societe($db);
                $societe->fetch($consult->fk_soc);

                $bankaccount=new Account($db);
                $result=$bankaccount->fetch($banque);
                $lineid=$bankaccount->addline(dol_now(), $type, $langs->trans("CustomerInvoicePayment"), $amount, $consult->num_cheque, '', $user, $societe->nom, $consult->banque);
                if ($lineid <= 0)
                {
                    $error++;
                    $consult->error=$bankaccount->error;
                }
                if (! $error)
                {
                    $result1=$bankaccount->add_url_line($lineid,$consult->id,dol_buildpath('/cabinetmed/consultations.php',1).'?action=edit&socid='.$consult->fk_soc.'&id=','Consultation','consultation');
                    $result2=$bankaccount->add_url_line($lineid,$consult->fk_soc,'',$societe->nom,'company');
                    if ($result1 <= 0 || $result2 <= 0)
                    {
                        $error++;
                    }
                }
            }
            if ($action == 'update')
            {
                $result=$consult->update($user);

                // Search if there is a bank line
                $bid=0;
                $sql.= "SELECT b.rowid FROM ".MAIN_DB_PREFIX."bank_url as bu, ".MAIN_DB_PREFIX."bank as b";
                $sql.= " WHERE bu.url_id = ".$consult->id." AND type = 'consultation'";
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
                    $consult->error=$db->lasterror();
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
                    if (! empty($_POST["montant_cheque"])) $amount=$consult->montant_cheque;
                    if (! empty($_POST["montant_carte"])) $amount=$consult->montant_carte;
                    if (! empty($_POST["montant_espece"])) $amount=$consult->montant_espece;
                    if (! empty($_POST["montant_tiers"])) $amount=$consult->montant_tiers;
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
                    $lineid=$bankaccount->addline(dol_now(), $type, $langs->trans("CustomerInvoicePayment"), $amount, $consult->num_cheque, '', $user, $societe->nom, $consult->banque);
                    $result1=$bankaccount->add_url_line($lineid,$consult->id,dol_buildpath('/cabinetmed/consultations.php',1).'?action=edit&socid='.$consult->fk_soc.'&id=','Consultation','consultation');
                    $result2=$bankaccount->add_url_line($lineid,$consult->fk_soc,'',$societe->nom,'company');
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
            header("Location: ".$_SERVER["PHP_SELF"].'?socid='.$consult->fk_soc);
            exit(0);
        }
        else
        {
            $db->rollback();
            $mesgarray[]=$consult->error;
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

    if ($id && ! $consult->id)
    {
        $result=$consult->fetch($id);
        if ($result < 0) dol_print_error($db,$consult->error);
    }

	/*
	 * Affichage onglets
	 */
    if ($conf->notification->enabled) $langs->load("mails");

	$head = societe_prepare_head($societe);
	dol_fiche_head($head, 'tabconsultations', $langs->trans("ThirdParty"),0,'company');

	print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<table class="border" width="100%">';

	print '<tr><td width="25%">'.$langs->trans('Name').'</td>';
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
        $x=1;
        $nboflines=4;

        print '<br>';

        print '<script type="text/javascript">
        jQuery(function() {
            jQuery("#cs").click(function () {
                jQuery("#infiltration").attr(\'disabled\', \'disabled\');
                jQuery("#codageccam").attr(\'disabled\', \'disabled\');
            });
            jQuery("#c2").click(function () {
                jQuery("#infiltration").attr(\'disabled\', \'disabled\');
                jQuery("#codageccam").attr(\'disabled\', \'disabled\');
            });
            jQuery("#ccam").click(function () {
                jQuery("#infiltration").removeAttr(\'disabled\');
                jQuery("#codageccam").removeAttr(\'disabled\');
            });
            jQuery("#montant_cheque").keyup(function () {
                if (jQuery("#montant_cheque").val() != "")
                {
                    jQuery("#banque").removeAttr(\'disabled\');
                    jQuery("#selectbankchequeto").removeAttr(\'disabled\');
                    jQuery("#num_cheque").removeAttr(\'disabled\');
                }
                else {
                    jQuery("#banque").attr(\'disabled\', \'disabled\');
                    jQuery("#selectbankchequeto").attr(\'disabled\', \'disabled\');
                    jQuery("#num_cheque").attr(\'disabled\', \'disabled\');
                }
            });
            jQuery("#montant_espece").keyup(function () {
                if (jQuery("#montant_espece").val() != "")
                {
                    jQuery("#selectbankespeceto").removeAttr(\'disabled\');
                }
                else {
                    jQuery("#selectbankespeceto").attr(\'disabled\', \'disabled\');
                }
            });
            jQuery("#montant_carte").keyup(function () {
                if (jQuery("#montant_carte").val() != "")
                {
                    jQuery("#selectbankcarteto").removeAttr(\'disabled\');
                }
                else {
                    jQuery("#selectbankcarteto").attr(\'disabled\', \'disabled\');
                }
            });

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
            jQuery("#addexamenprescrit").click(function () {
                var t=jQuery("#listexamenprescrit").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#examenprescrit").append(t+"\n");
                    jQuery(".ui-autocomplete-input").val("");
                    jQuery(".ui-autocomplete-input").text("");
                    jQuery("#listexamenprescrit").get(0).selectedIndex = 0;
                }
            });
        });
        </script>


        <style>
            .ui-autocomplete-input { width: '.$width.'px; }
        </style>


        <script>
            jQuery(function() {
                jQuery( "#listmotifcons" ).combobox();
                jQuery( "#listdiagles" ).combobox();
                jQuery( "#listexamenprescrit" ).combobox();
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
        print '<legend>'.$langs->trans("InfoGenerales").'</legend>'."\n";

        print '<table class="notopnoleftnoright" width="100%">';
        print '<tr><td width="60%">';
        if ($action=='edit' || $action=='update')
        {
            print $langs->trans("ConsultationNumero").': '.sprintf("%08d",$consult->id).'<br><br>';
        }
        print $langs->trans("Date").': ';
        $form->select_date($datecons,'cons');
        print '</td><td>';
        print '<input type="radio" name="typepriseencharge" value="ALD"'.($consult->typepriseencharge=='ALD'?' checked="checked"':'').'> ALD';
        print ' &nbsp; ';
        print '<input type="radio" name="typepriseencharge" value="INV"'.($consult->typepriseencharge=='INV'?' checked="checked"':'').'> INV';
        print ' &nbsp; ';
        print '<input type="radio" name="typepriseencharge" value="AT"'.($consult->typepriseencharge=='AT'?' checked="checked"':'').'> AT';
        print ' &nbsp; ';
        print '<input type="radio" name="typepriseencharge" value="CMU"'.($consult->typepriseencharge=='CMU'?' checked="checked"':'').'> CMU';
        print ' &nbsp; ';
        print '<input type="radio" name="typepriseencharge" value="AME"'.($consult->typepriseencharge=='AME'?' checked="checked"':'').'> AME';
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
        print $langs->trans("MotifConsultation").':';
        print '</td><td>';
        //print '<input type="text" size="3" class="flat" name="searchmotifcons" value="'.GETPOST("searchmotifcons").'" id="searchmotifcons">';
        listmotifcons(1,400);
        /*print ' '.img_picto('Ajouter motif principal','edit_add_p.png@cabinetmed');
        print ' '.img_picto('Ajouter motif secondaire','edit_add_s.png@cabinetmed');*/
        print ' <input type="button" class="button" id="addmotifprinc" name="addmotifprinc" value="+P">';
        print ' <input type="button" class="button" id="addmotifsec" name="addmotifsec" value="+S">';
        print '</td></tr>';
        print '<tr><td>Principal:';
        print '</td><td>';
        print '<input type="text" size="32" class="flat" name="motifconsprinc" value="'.$consult->motifconsprinc.'" id="motifconsprinc"><br>';
        print '</td></tr>';
        print '<tr><td valign="top">Secondaires:';
        print '</td><td>';
        print '<textarea name="motifconssec" id="motifconssec" cols="40">';
        print $consult->motifconssec;
        print '</textarea>';
        print '</td>';
        print '</tr>';

        print '<tr><td><br></td></tr>';

        print '<tr><td valign="top" width="160">';
        print $langs->trans("DiagnostiqueLesionnel").':';
        print '</td><td>';
        //print '<input type="text" size="3" class="flat" name="searchdiagles" value="'.GETPOST("searchdiagles").'" id="searchdiagles">';
        listdiagles(1,$width);
        print ' <input type="button" class="button" id="adddiaglesprinc" name="adddiaglesprinc" value="+P">';
        print ' <input type="button" class="button" id="adddiaglessec" name="adddiaglessec" value="+S">';
        print '</td></tr>';
        print '<tr><td>Principal:';
        print '</td><td>';
        print '<input type="text" size="32" class="flat" name="diaglesprinc" value="'.$consult->diaglesprinc.'" id="diaglesprinc"><br>';
        print '</td></tr>';
        print '<tr><td valign="top">Secondaires:';
        print '</td><td>';
        print '<textarea name="diaglessec" id="diaglessec" cols="40">';
        print $consult->diaglessec;
        print '</textarea>';
        print '</td>';
        print '</tr>';

        print '</table>';

        print '</td><td valign="top">';

        print ''.$langs->trans("ExamensCliniques").'<br>';
        print '<textarea name="examenclinique" id="examenclinique" class="flat" cols="50" rows="6">'.$consult->examenclinique.'</textarea>';

        print '</td></tr>';

        print '</table>';
        //print '</fieldset>';


        // Prescriptions
        //print '<fieldset id="fieldsetprescription">';
        //print '<legend>'.$langs->trans("Prescription").'</legend>'."\n";
        print '<hr style="height:1px; color: #dddddd;">';

        print '<table class="notopnoleftnoright" width="100%">';
        print '<tr><td width="60%" valign="top">';

        print '<table class="notopnoleftnoright" width="100%">';

        print '<tr><td valign="top" width="160">';
        print $langs->trans("ExamensPrescrits").':';
        print '</td><td>';
        //print '<input type="text" size="3" class="flat" name="searchexamenprescrit" value="'.GETPOST("searchexamenprescrit").'" id="searchexamenprescrit">';
        listexamenprescrit(1,$width);
        print ' <input type="button" class="button" id="addexamenprescrit" name="addexamenprescrit" value="+">';
        print '</td></tr>';
        print '<tr><td valign="top">';
        print '</td><td>';
        print '<textarea name="examenprescrit" id="examenprescrit" cols="40">';
        print $consult->examenprescrit;
        print '</textarea>';
        print '</td>';
        print '</tr>';

        print '<tr><td valign="top"><br>'.$langs->trans("Commentaires");
        print '</td><td><br>';
        print '<textarea name="comment" id="comment" class="flat" cols="40" rows="'.($nboflines-1).'">'.$consult->comment.'</textarea>';
        print '</td></tr>';

        print '</table>';

        print '</td><td valign="top">';

        print $langs->trans("TraitementsPrescrits").'<br>';
        print '<textarea name="traitementprescrit" class="flat" cols="50" rows="'.($nboflines-1).'">'.$consult->traitementprescrit.'</textarea>';

        print '<br><br><b>'.$langs->trans("TypeVisite").'</b> &nbsp; &nbsp; &nbsp; ';
        print '<input type="radio" class="flat" name="typevisit" value="CS" id="cs"'.($consult->typevisit=='CS'?' checked="true"':'').'> CS';
        print ' &nbsp; &nbsp; ';
        print '<input type="radio" class="flat" name="typevisit" value="C2" id="c2"'.($consult->typevisit=='C2'?' checked="true"':'').'> C2';
        print ' &nbsp; &nbsp; ';
        print '<input type="radio" class="flat" name="typevisit" value="CCAM" id="ccam"'.($consult->typevisit=='CCAM'?' checked="true"':'').'> CCAM';
        print '<br><br>'.$langs->trans("Infiltrations").' ';
        print '<input type="text" class="flat" name="infiltration" id="infiltration" value="'.$consult->infiltration.'" size="30"'.($consult->infiltration?'':' disabled="disabled"').'>';
        print '<br>'.$langs->trans("Codage CCAM").' ';
        print '<input type="text" class="flat" name="codageccam" id="codageccam" value="'.$consult->codageccam.'" size="30"'.($consult->codageccam?'':' disabled="disabled"').'>';
        print '</td></tr>';

        print '</table>';
        print '</fieldset>';

        print '<br>';

        print '<fieldset id="fieldsetanalyse">';
        print '<legend>'.$langs->trans("Paiement").'</legend>'."\n";

        print '<table class="notopnoleftnoright" width="100%">';
        print '<tr><td width="160">';
        print ''.$langs->trans("Cheque").'</td><td>';
        print '<input type="text" class="flat" name="montant_cheque" id="montant_cheque" value="'.($consult->montant_cheque!=''?price($consult->montant_cheque):'').'" size="5">';
        if ($conf->banque->enabled)
        {
            print ' &nbsp; '.$langs->trans("A encaiser sur").' ';
            $form->select_comptes('','bankchequeto',0,"(proprio LIKE '%".$user->nom."%' OR label LIKE '%".$user->nom."%') AND courant = 1",0,($consult->montant_cheque?'':' disabled="disabled"'));
        }
        print ' &nbsp; '.$langs->trans("ChequeBank").' ';
        print '<input type="text" class="flat" name="banque" id="banque" value="'.$consult->banque.'" size="18"'.($consult->montant_cheque?'':' disabled="disabled"').'>';
        if ($conf->banque->enabled)
        {
            print ' &nbsp; '.$langs->trans("ChequeOrTransferNumber").' ';
            print '<input type="text" class="flat" name="num_cheque" id="num_cheque" value="'.$consult->num_cheque.'" size="6"'.($consult->montant_cheque?'':' disabled="disabled"').'>';
        }
        print '</td></tr><tr><td>';
        print $langs->trans("Espece").'</td><td>';
        print '<input type="text" class="flat" name="montant_espece" id="montant_espece" value="'.($consult->montant_espece!=''?price($consult->montant_espece):'').'" size="5">';
        if ($conf->banque->enabled)
        {
            print ' &nbsp; '.$langs->trans("A encaiser sur").' ';
            $form->select_comptes('','bankespeceto',0,"(proprio LIKE '%".$user->nom."%' OR label LIKE '%".$user->nom."%') AND courant = 2",0,($consult->montant_espece?'':' disabled="disabled"'));
        }
        print '</td></tr><tr><td>';
        print $langs->trans("Carte").'</td><td>';
        print '<input type="text" class="flat" name="montant_carte" id="montant_carte" value="'.($consult->montant_carte!=''?price($consult->montant_carte):'').'" size="5">';
        if ($conf->banque->enabled)
        {
            print ' &nbsp; '.$langs->trans("A encaiser sur").' ';
            $form->select_comptes('','bankcarteto',0,"(proprio LIKE '%".$user->nom."%' OR label LIKE '%".$user->nom."%') AND courant = 1",0,($consult->montant_carte?'':' disabled="disabled"'));
        }
        print '</td></tr><tr><td>';
        print $langs->trans("Tiers").'</td><td>';
        print '<input type="text" class="flat" name="montant_tiers" id="montant_tiers" value="'.($consult->montant_tiers!=''?price($consult->montant_tiers):'').'" size="5">';
        print '</td><td>';


        print '</td></tr>';

        print '</table>';
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
if ($action == '')
{
    print '<div class="tabsAction">';

    if ($user->rights->societe->creer)
    {
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?socid='.$societe->id.'&amp;action=create">'.$langs->trans("NewConsult").'</a>';
    }

    print '</div>';
}


if ($action == '')
{
    print_fiche_titre($langs->trans("ListOfConsultations"));

    $param='&socid='.$socid;

    print "\n";
    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('Num'),$_SERVER['PHP_SELF'],'t.rowid','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.datecons','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Prise en charge'),$_SERVER['PHP_SELF'],'t.typepriseencharge','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('MotifPrincipal'),$_SERVER['PHP_SELF'],'t.motifconsprinc','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('ConsultActe'),$_SERVER['PHP_SELF'],'t.typevisit','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('MontantPaiement'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('TypePaiement'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
    if ($conf->banque->enabled) print_liste_field_titre($langs->trans('Bank'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
    print '<td>&nbsp;</td>';
    print '</tr>';


    // List des consult
    $sql = "SELECT";
    $sql.= " t.rowid,";
    $sql.= " t.fk_soc,";
    $sql.= " t.datecons,";
    $sql.= " t.typepriseencharge,";
    $sql.= " t.motifconsprinc,";
    $sql.= " t.diaglesprinc,";
    $sql.= " t.motifconssec,";
    $sql.= " t.diaglessec,";
    $sql.= " t.examenclinique,";
    $sql.= " t.examenprescrit,";
    $sql.= " t.traitementprescrit,";
    $sql.= " t.comment,";
    $sql.= " t.typevisit,";
    $sql.= " t.infiltration,";
    $sql.= " t.codageccam,";
    $sql.= " t.montant_cheque,";
    $sql.= " t.montant_espece,";
    $sql.= " t.montant_carte,";
    $sql.= " t.montant_tiers,";
    $sql.= " t.banque,";
    $sql.= " bu.fk_bank, b.fk_account";
    $sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_cons as t";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_url as bu on bu.url_id = t.rowid AND type = 'consultation'";
    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank as b on bu.fk_bank = b.rowid";
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
            print '<tr '.$bc[$var].'><td>';
            print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=edit">'.sprintf("%08d",$obj->rowid).'</a>';
            print '</td><td>';
            print dol_print_date($db->jdate($obj->datecons),'day');
            print '</td><td>';
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
            print '<td align="right">';
            print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=edit">'.img_edit().'</a>';
/*            if ($user->rights->societe->supprimer)
            {
                print ' &nbsp; ';
                print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=delete">'.img_delete().'</a>';
            }
*/            print '</td>';
            print '</tr>';
            $i++;
        }
    }
    else
    {
        dol_print_error($db);
    }
}




function listmotifcons($nboflines,$newwidth=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listmotifcons" name="motifcons" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_motifcons as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.$obj->code.'">'.$obj->label.'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}

function listdiagles($nboflines,$newwidth=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listdiagles" name="diagles" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_diaglec as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.$obj->code.'">'.$obj->label.'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}

/**
 *  Show combo box with all exams
 *  @param          nboflines       Max nb of lines
 *  @param          newwidth        Force width
 *  @param          type            To filter on a type
 *  @param          showtype        Show type
 */
function listexamenprescrit($nboflines,$newwidth=0,$type='',$showtype=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listexamenprescrit" name="examenprescrit" '.($newwidth?'style="width: '.$newwidth.'px"':'').' size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label, s.biorad as type';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_examenprescrit as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.$obj->code.'">'.$obj->label.($showtype?' ('.strtolower($obj->type).')':'').'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}


$db->close();

llxFooter('$Date: 2011/04/03 21:06:03 $ - $Revision: 1.8 $');
?>
