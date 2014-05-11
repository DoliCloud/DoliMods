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
 *   \file       htdocs/cabinetmed/consultations.php
 *   \brief      Tab for consultations
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
include_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
include_once("./lib/cabinetmed.lib.php");
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");

$action=GETPOST("action");
$id=GETPOST('id','int');  // Id consultation
$fk_agenda=GETPOST('fk_agenda','int');	// Id event if consultation is created from an event

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
if (! $sortfield) $sortfield='t.datecons';
if (! $sortorder) $sortorder='DESC';
$limit = $conf->liste_limit;

$consult = new CabinetmedCons($db);

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

// Add consultation
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

            $result=$consult->fetch_bankid();

            $oldconsult=dol_clone($consult);
        }
        else
        {
            $consult->datecons=$datecons;
            $consult->fk_soc=$_POST["socid"];
        }

        $amount=array();
        if (! empty($_POST["montant_cheque"])) $amount['CHQ']=price2num($_POST["montant_cheque"]);
        if (! empty($_POST["montant_carte"]))  $amount['CB']=price2num($_POST["montant_carte"]);
        if (! empty($_POST["montant_espece"])) $amount['LIQ']=price2num($_POST["montant_espece"]);
        if (! empty($_POST["montant_tiers"]))  $amount['VIR']=price2num($_POST["montant_tiers"]);
        $banque=array();
        if (! empty($_POST["bankchequeto"]))   $banque['CHQ']=$_POST["bankchequeto"];
        if (! empty($_POST["bankcarteto"]))    $banque['CB']=$_POST["bankcarteto"];
        if (! empty($_POST["bankespeceto"]))   $banque['LIQ']=$_POST["bankespeceto"];
        if (! empty($_POST["banktiersto"]))    $banque['VIR']=$_POST["banktiersto"];  // Should be always empty

        unset($consult->montant_carte);
        unset($consult->montant_cheque);
        unset($consult->montant_espece);
        unset($consult->montant_tiers);
        if (GETPOST("montant_cheque") != '') $consult->montant_cheque=price2num($_POST["montant_cheque"]);
        if (GETPOST("montant_espece") != '') $consult->montant_espece=price2num($_POST["montant_espece"]);
        if (GETPOST("montant_carte") != '')  $consult->montant_carte=price2num($_POST["montant_carte"]);
        if (GETPOST("montant_tiers") != '')  $consult->montant_tiers=price2num($_POST["montant_tiers"]);

        $consult->banque=trim(GETPOST("banque"));
        $consult->num_cheque=trim(GETPOST("num_cheque"));
        $consult->typepriseencharge=GETPOST("typepriseencharge");
        $consult->motifconsprinc=GETPOST("motifconsprinc");
        $consult->diaglesprinc=GETPOST("diaglesprinc");
        $consult->motifconssec=GETPOST("motifconssec");
        $consult->diaglessec=GETPOST("diaglessec");
        $consult->hdm=trim(GETPOST("hdm"));
        $consult->examenclinique=trim(GETPOST("examenclinique"));
        $consult->examenprescrit=trim(GETPOST("examenprescrit"));
        $consult->traitementprescrit=trim(GETPOST("traitementprescrit"));
        $consult->comment=trim(GETPOST("comment"));
        $consult->typevisit=GETPOST("typevisit");
        $consult->infiltration=trim(GETPOST("infiltration"));
        $consult->codageccam=trim(GETPOST("codageccam"));
		$consult->fk_agenda=GETPOST("fk_agenda");

        //print "X".$_POST["montant_cheque"].'-'.$_POST["montant_espece"].'-'.$_POST["montant_carte"].'-'.$_POST["montant_tiers"]."Z";
        $nbnotempty=0;
        if (trim($_POST["montant_cheque"])!='') $nbnotempty++;
        if (trim($_POST["montant_espece"])!='') $nbnotempty++;
        if (trim($_POST["montant_carte"])!='')  $nbnotempty++;
        if (trim($_POST["montant_tiers"])!='')  $nbnotempty++;
        if ($nbnotempty==0)
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"));
        }
        if ((trim($_POST["montant_cheque"])!='' && price2num($_POST["montant_cheque"]) == 0)
        || (trim($_POST["montant_espece"])!='' && price2num($_POST["montant_espece"]) == 0)
        || (trim($_POST["montant_carte"])!='' && price2num($_POST["montant_carte"]) == 0))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Amount"));
        }
		// If bank module enabled, bank account is required.
		if ($conf->banque->enabled)
		{
			if (! empty($_POST["montant_cheque"]) && (! GETPOST('bankchequeto') || GETPOST('bankchequeto') < 0)) { $error++; $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("RecBank")); }
			if (! empty($_POST["montant_carte"])  && (! GETPOST('bankcarteto')  || GETPOST('bankcarteto') < 0))  { $error++; $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("RecBank")); }
			if (! empty($_POST["montant_espece"]) && (! GETPOST('bankespeceto') || GETPOST('bankespeceto') < 0)) { $error++; $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("RecBank")); }
		}
        // Other
        if (trim($_POST["montant_cheque"])!='' && ! empty($conf->global->CABINETMED_BANK_PATIENT_REQUIRED) && ! trim($_POST["banque"]))
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
        if (empty($consult->diaglesprinc))
        {
            $error++;
            $mesgarray[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("DiagnostiqueLesionnel"));
        }

        $db->begin();

        if (! $error)
        {
            if ($action == 'add')
            {
                $result=$consult->create($user);
                if ($result < 0)
                {
                    $mesg=$consult->error;
                    $error++;
                }

                if (! $error)
                {
                    $object = new Patient($db);
                    $object->fetch($consult->fk_soc);

                    foreach(array('CHQ','CB','LIQ','VIR') as $key)
                    {
                        if ($conf->banque->enabled && isset($banque[$key]) && $banque[$key] > 0)
                        {
                            $bankaccount=new Account($db);
                            $result=$bankaccount->fetch($banque[$key]);
                            if ($result < 0) dol_print_error($db,$bankaccount->error);
                            if ($key == 'CHQ') $lineid=$bankaccount->addline(dol_now(), $key, $langs->trans("CustomerInvoicePayment"), $amount[$key], $consult->num_cheque, '', $user, $object->name, $consult->banque);
                            else $lineid=$bankaccount->addline(dol_now(), $key, $langs->trans("CustomerInvoicePayment"), $amount[$key], '', '', $user, $object->name, '');
                            if ($lineid <= 0)
                            {
                                $error++;
                                $consult->error=$bankaccount->error;
                            }
                            if (! $error)
                            {
                                $result1=$bankaccount->add_url_line($lineid,$consult->id,dol_buildpath('/cabinetmed/consultations.php',1).'?action=edit&socid='.$consult->fk_soc.'&id=','Consultation','consultation');
                                $result2=$bankaccount->add_url_line($lineid,$consult->fk_soc,'',$object->name,'company');
                                if ($result1 <= 0 || $result2 <= 0)
                                {
                                    $error++;
                                }
                            }
                        }
                    }
                }
            }
            if ($action == 'update')
            {
                $object = new Patient($db);
                $result=$object->fetch($consult->fk_soc);

                $result=$consult->update($user);
                if ($result < 0)
                {
                    $mesg=$consult->error;
                    $error++;
                }

                if (! $error)
                {
                    foreach(array('CHQ','CB','LIQ','THIRD') as $key)
                    {
                        $bankmodified=0;

                        if ($key == 'CHQ' &&
                        (price2num($oldconsult->montant_cheque,'MT') != price2num($_POST["montant_cheque"],'MT') ||
                        $oldconsult->banque != trim($_POST["banque"]) ||
                        $oldconsult->num_cheque != trim($_POST["num_cheque"]) ||
                        $oldconsult->bank['CHQ']['account_id'] != $_POST["bankchequeto"])) $bankmodified=1;
                        if ($key == 'CB' &&
                        (price2num($oldconsult->montant_carte,'MT') != price2num($_POST["montant_carte"],'MT') ||
                        $oldconsult->bank['CB']['account_id'] != $_POST["bankcarteto"])) $bankmodified=1;
                        if ($key == 'LIQ' &&
                        (price2num($oldconsult->montant_espece,'MT') != price2num($_POST["montant_espece"],'MT') ||
                        $oldconsult->bank['LIQ']['account_id'] != $_POST["bankespeceto"])) $bankmodified=1;
                        if ($key == 'VIR' &&
                        (price2num($oldconsult->montant_tiers,'MT') != price2num($_POST["montant_tiers"],'MT'))) $bankmodified=1;

                        if ($conf->banque->enabled && $bankmodified)
                        {
                            // TODO Check if cheque is already into a receipt
                            if ($key == 'CHQ' && 1 == 1)
                            {

                            }
                            // TODO Check if bank record is already conciliated

                        }

                        //print 'xx '.$key.' => '.$bankmodified;exit;
                        //if ($key == 'CB') { var_dump($oldconsult->bank);exit; }

                        // If we changed bank informations for this key
                        if ($bankmodified)
                        {
                            // If consult has a bank id for this key, we remove it
                            if ($consult->bank[$key]['bank_id'] && ! $consult->bank[$key]['rappro'])
                            {
                                $bankaccountline=new AccountLine($db);
                                $result=$bankaccountline->fetch($consult->bank[$key]['bank_id']);
                                $bank_chq=$bankaccountline->bank_chq;
                                $fk_bordereau=$bankaccountline->fk_bordereau;
                                $bankaccountline->delete($user);
                            }

                            if ($conf->banque->enabled && isset($banque[$key]) && $banque[$key] > 0)
                            {
                                $bankaccount=new Account($db);
                                $result=$bankaccount->fetch($banque[$key]);
                            	if ($result < 0) dol_print_error($db,$bankaccount->error);
                                if ($key == 'CHQ') $lineid=$bankaccount->addline($consult->datecons, $key, $langs->trans("CustomerInvoicePayment"), $amount[$key], $consult->num_cheque, '', $user, $object->name, $consult->banque);
                                else $lineid=$bankaccount->addline($consult->datecons, $key, $langs->trans("CustomerInvoicePayment"), $amount[$key], '', '', $user, $object->name, '');
                                $result1=$bankaccount->add_url_line($lineid,$consult->id,dol_buildpath('/cabinetmed/consultations.php',1).'?action=edit&socid='.$consult->fk_soc.'&id=','Consultation','consultation');
                                $result2=$bankaccount->add_url_line($lineid,$consult->fk_soc,'',$object->name,'company');
                                if ($lineid <= 0 || $result1 <= 0 || $result2 <= 0)
                                {
                                    $error++;
                                }
                            }
                        }
                    }
                }
                else
				{
                    $error++;
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
        if (GETPOST("backtopage"))
        {
            header("Location: ".GETPOST("backtopage"));
            exit(0);
        }
        $action='';
    }
}



/*
 *	View
 */

$form = new Form($db);
$fuser = new User($db);
$width="242";

llxHeader('',$langs->trans("Consultation"));

if ($socid > 0)
{
    $object = new Patient($db);
    $result=$object->fetch($socid);
	if ($result < 0) { dol_print_error('',$object->error); }

    if ($id && ! $consult->id)
    {
        $result=$consult->fetch($id);
        if ($result < 0) dol_print_error($db,$consult->error);

        $result=$consult->fetch_bankid();
        if ($result < 0) dol_print_error($db,$consult->error);
    }

	/*
	 * Affichage onglets
	 */
    if ($conf->notification->enabled) $langs->load("mails");

	$head = societe_prepare_head($object);
	dol_fiche_head($head, 'tabconsultations', $langs->trans("Patient"),0,'company');

	print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<table class="border" width="100%">';

	print '<tr><td width="25%">'.$langs->trans('PatientName').'</td>';
	print '<td colspan="3">';
	print $form->showrefnav($object,'socid','',($user->societe_id?0:1),'rowid','nom');
	print '</td></tr>';

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

	print "</table>";

	print '</form>';

    // Form to create
    if ($action == 'create' || $action == 'edit')
    {
        dol_fiche_end();
        dol_fiche_head();

        $x=1;
        $nboflines=4;

        print '<script type="text/javascript" language="javascript">
        var changed=false;
        function init_montant_cheque()
        {
	        if (jQuery("#idmontant_cheque").val() != "")
	        {
		        jQuery("#banque").removeAttr(\'disabled\');
		        jQuery("#selectbankchequeto").removeAttr(\'disabled\');
	    	    jQuery("#idnum_cheque").removeAttr(\'disabled\');
    	    }
	    	else
	    	{
	    		jQuery("#banque").attr(\'disabled\', \'disabled\');
	    		jQuery("#selectbankchequeto").attr(\'disabled\', \'disabled\');
	    		jQuery("#idnum_cheque").attr(\'disabled\', \'disabled\');
    		}
			/* jQuery("#selectbankchequeto").selectmenu("refresh"); */
    	}
        function init_montant_carte()
        {
            if (jQuery("#idmontant_carte").val() != "")
            {
                jQuery("#selectbankcarteto").removeAttr(\'disabled\');
            }
            else
            {
                jQuery("#selectbankcarteto").attr(\'disabled\', \'disabled\');
            }
			/* jQuery("#selectbankcarteto").selectmenu("refresh"); */
    	}
        function init_montant_espece()
        {
            if (jQuery("#idmontant_espece").val() != "")
            {
                jQuery("#selectbankespeceto").removeAttr(\'disabled\');
            }
            else
            {
                jQuery("#selectbankespeceto").attr(\'disabled\', \'disabled\');
            }
			/* jQuery("#selectbankespeceto").selectmenu("refresh"); */
    	}
        jQuery(document).ready(function()
        {
           	init_montant_cheque();
           	init_montant_carte();
           	init_montant_espece();

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
    		jQuery("#cs").click(function () {
                jQuery("#idcodageccam").attr(\'disabled\', \'disabled\');
            });
            jQuery("#c2").click(function () {
                jQuery("#idcodageccam").attr(\'disabled\', \'disabled\');
            });
            jQuery("#ccam").click(function () {
                jQuery("#idcodageccam").removeAttr(\'disabled\');
            });
            jQuery("#idmontant_cheque").keyup(function () {
            	init_montant_cheque();
            });
            jQuery("#idmontant_carte").keyup(function () {
				init_montant_carte();
    		});
            jQuery("#idmontant_espece").keyup(function () {
           		init_montant_espece();
            });

            jQuery("#addmotifprinc").click(function () {
                /*alert(jQuery("#listmotifcons option:selected" ).val());
                alert(jQuery("#listmotifcons option:selected" ).text());*/
                var t=jQuery("#listmotifcons").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#motifconsprinc").val(t);
                    jQuery("#addmotifbox .ui-autocomplete-input").val("");
                    jQuery("#addmotifbox .ui-autocomplete-input").text("");
                    jQuery("#listmotifcons").get(0).selectedIndex = 0;
 					changed=true;
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
                        var box = jQuery("#motifconssec");
                        u=box.val() + (box.val() != \'\' ? "\n" : \'\') + t;
                        box.val(u); box.html(u);
                        jQuery("#addmotifbox .ui-autocomplete-input").val("");
                        jQuery("#addmotifbox .ui-autocomplete-input").text("");
                        jQuery("#listmotifcons").get(0).selectedIndex = 0;
 						changed=true;
    				}
                }
            });
            jQuery("#adddiaglesprinc").click(function () {
                var t=jQuery("#listdiagles").children( ":selected" ).text();
                if (t != "")
                {
                    jQuery("#diaglesprinc").val(t);
                    jQuery("#adddiagbox .ui-autocomplete-input").val("");
                    jQuery("#adddiagbox .ui-autocomplete-input").text("");
                    jQuery("#listdiagles").get(0).selectedIndex = 0;
 					changed=true;
    			}
            });
            jQuery("#adddiaglessec").click(function () {
                var t=jQuery("#listdiagles").children( ":selected" ).text();
                if (t != "")
                {
                    var box = jQuery("#diaglessec");
                    u=box.val() + (box.val() != \'\' ? "\n" : \'\') + t;
                    box.val(u); box.html(u);
                    jQuery("#adddiagbox .ui-autocomplete-input").val("");
                    jQuery("#adddiagbox .ui-autocomplete-input").text("");
                    jQuery("#listmotifcons").get(0).selectedIndex = 0;
 					changed=true;
    			}
            });
            jQuery("#addexamenprescrit").click(function () {
                var t=jQuery("#listexamenprescrit").children( ":selected" ).text();
                if (t != "")
                {
                    var box = jQuery("#examenprescrit");
                    u=box.val() + (box.val() != \'\' ? "\n" : \'\') + t;
                    box.val(u); box.html(u);
                    jQuery("#addexambox .ui-autocomplete-input").val("");
                    jQuery("#addexambox .ui-autocomplete-input").text("");
                    jQuery("#listexamenprescrit").get(0).selectedIndex = 0;
 					changed=true;
    			}
            });
    		';
        	if ($consult->typevisit != 'CCAM')
        	{
        		print ' jQuery("#idcodageccam").attr(\'disabled\',\'disabled\'); '."\n";
        	}
		print '
        });
        </script>


        <style>
            #addmotifbox .ui-autocomplete-input { width: '.$width.'px; }
            #adddiagbox .ui-autocomplete-input { width: '.$width.'px; }
            #addexambox .ui-autocomplete-input { width: '.$width.'px; }
            #paymentsbox .ui-autocomplete-input { width: 140px !important; }
        </style>

		';

		print ajax_combobox('listmotifcons');
		print ajax_combobox('listdiagles');
		print ajax_combobox('listexamenprescrit');
		print ajax_combobox('banque');

        // General
        print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
        if ($action=='create') print '<input type="hidden" name="action" value="add">';
        if ($action=='edit')   print '<input type="hidden" name="action" value="update">';
        print '<input type="hidden" name="socid" value="'.$socid.'">';
        print '<input type="hidden" name="id" value="'.$id.'">';
        print '<input type="hidden" name="backtourl" value="'.GETPOST('backtourl').'">';


        /*if ($action=='edit' || $action=='update')
        {
	        print '<table class="border" width="100%">';
			print '<tr><td width="25%">'.$langs->trans('ConsultationNumero').'</td>';
			print '<td>'.sprintf("%08d",$consult->id);
            if ($consult->fk_user > 0)
	        {
	        	$fuser->fetch($consult->fk_user);
	        	print ' - '.$langs->trans("CreatedBy").': <strong>'.$fuser->getFullName($langs).'</strong>';
	        }
	        if ($consult->date_c > 0)
	        {
	        	print ' - '.$langs->trans("DateCreation").': <strong>'.dol_print_date($consult->date_c, 'dayhour').'</strong>';
	        }
	        if ($consult->date_m > 0)
	        {
	        	print ' - '.$langs->trans("DateModificationShort").': <strong>'.dol_print_date($consult->date_m, 'dayhour').'</strong>';
	        }
			print '</td>';
			print '</tr></table><br>';
        }*/

        print '<fieldset id="fieldsetanalyse">';
        print '<legend>'.$langs->trans("InfoGenerales");
        print '</legend>'."\n";

        $fk_agenda=empty($fk_agenda)?$consult->fk_agenda:$fk_agenda;

		if ($action=='edit' || $action=='update' || $fk_agenda) print '<table class="notopnoleftnoright" width="100%">';
        if ($action=='edit' || $action=='update')
        {
			print '<tr><td width="160">'.$langs->trans('ConsultationNumero').'</td>';
			print '<td>'.sprintf("%08d",$consult->id);
            if ($consult->fk_user > 0)
	        {
	        	$fuser->fetch($consult->fk_user);
	        	print ' - '.$langs->trans("CreatedBy").': <strong>'.$fuser->getFullName($langs).'</strong>';
	        }
	        if ($consult->date_c > 0)
	        {
	        	print ' - '.$langs->trans("DateCreation").': <strong>'.dol_print_date($consult->date_c, 'dayhour').'</strong>';
	        }
	        if ($consult->date_m > 0)
	        {
	        	print ' - '.$langs->trans("DateModificationShort").': <strong>'.dol_print_date($consult->date_m, 'dayhour').'</strong>';
	        }
			print '</td>';
			print '</tr>';
        }
	    if ($fk_agenda)
        {
        	$actioncomm=new ActionComm($db);
        	$result=$actioncomm->fetch($fk_agenda);
        	if ($result > 0)
        	{
	        	print '<tr style="height: 24px;"><td colspan="2">';
        		print $langs->trans("RecordCreatedFromRDV", $actioncomm->getNomUrl(1), dol_print_date($actioncomm->datep,'dayhour')).'<br>';
        		print '<input type="hidden" name="fk_agenda" value="'.$actioncomm->id.'">';
        		print '</td></tr>';
        	}
        }
		if ($action=='edit' || $action=='update' || $fk_agenda) print '</table>';

		if ($action=='edit' || $action=='update' || $fk_agenda) print '<hr style="height:1px; color: #dddddd;">';

        print '<div class="fichecenter"><div class="fichehalfleft">';
        print '<table class="notopnoleftnoright" width="100%">';

        print '<tr><td style="width: 160px" class="fieldrequired">';
        print $langs->trans("Date").': ';
        print '</td><td align="left">';
        $form->select_date($consult->datecons,'cons');
        print '</td></tr>';
        print '</table>';

        print '</div><div class="fichehalfright"><div class="ficheaddleft">';

        if (! empty($conf->global->CABINETMED_FRENCH_PRISEENCHARGE))
        {
            print $langs->trans("Priseencharge").': &nbsp;';
            print '<input type="radio" class="flat" name="typepriseencharge" value="ALD"'.($consult->typepriseencharge=='ALD'?' checked="checked"':'').'> ALD';
            print ' &nbsp; ';
            print '<input type="radio" class="flat" name="typepriseencharge" value="INV"'.($consult->typepriseencharge=='INV'?' checked="checked"':'').'> INV';
            print ' &nbsp; ';
            print '<input type="radio" class="flat" name="typepriseencharge" value="AT"'.($consult->typepriseencharge=='AT'?' checked="checked"':'').'> AT';
            print ' &nbsp; ';
            print '<input type="radio" class="flat" name="typepriseencharge" value="CMU"'.($consult->typepriseencharge=='CMU'?' checked="checked"':'').'> CMU';
            print ' &nbsp; ';
            print '<input type="radio" class="flat" name="typepriseencharge" value="AME"'.($consult->typepriseencharge=='AME'?' checked="checked"':'').'> AME';
        }

        //print '</td></tr>';
        //print '</table>';
        print '</div></div></div>';

        //print '</fieldset>';

        //print '<br>';

        // Analyse
//        print '<fieldset id="fieldsetanalyse">';
//        print '<legend>'.$langs->trans("Diagnostiques et prescriptions").'</legend>'."\n";
        print '<div class="fichecenter"></div>';

        print '<hr style="height:1px; color: #dddddd;">';

        //print '<table class="notopnoleftnoright" width="100%">';
        //print '<tr><td width="60%">';
        print '<div class="fichecenter"><div class="fichehalfleft">';

        print '<table class="notopnoleftnoright" id="addmotifbox" width="100%">';
        print '<tr><td valign="top" width="160">';
        print $langs->trans("MotifConsultation").':';
        print '</td><td>';
        //print '<input type="text" size="3" class="flat" name="searchmotifcons" value="'.GETPOST("searchmotifcons").'" id="searchmotifcons">';
        listmotifcons(1,400);
        /*print ' '.img_picto('Ajouter motif principal','edit_add_p.png@cabinetmed');
        print ' '.img_picto('Ajouter motif secondaire','edit_add_s.png@cabinetmed');*/
        print ' <input type="button" class="button" id="addmotifprinc" name="addmotifprinc" value="+P" title="'.dol_escape_htmltag($langs->trans("ClickHereToSetPrimaryReason")).'">';
        print ' <input type="button" class="button" id="addmotifsec" name="addmotifsec" value="+S" title="'.dol_escape_htmltag($langs->trans("ClickHereToSetSecondaryReason")).'">';
        if ($user->admin) print ' '.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        print '</td></tr>';
        print '<tr><td class="fieldrequired">'.$langs->trans("Primary").':';
        print '</td><td>';
        print '<input type="text" size="32" class="flat" name="motifconsprinc" value="'.$consult->motifconsprinc.'" id="motifconsprinc"><br>';
        print '</td></tr>';
        print '<tr><td valign="top">'.$langs->trans("Secondaries").':';
        print '</td><td>';
        print '<textarea class="flat" name="motifconssec" id="motifconssec" cols="40" rows="'.ROWS_3.'">';
        print $consult->motifconssec;
        print '</textarea>';
        print '</td>';
        print '</tr>';
        print '</table>';

        //print '</td><td>';
        print '</div><div class="fichehalfright"><div class="ficheaddleft">';

        print ''.$langs->trans("HistoireDeLaMaladie").'<br>';
        print '<textarea name="hdm" id="hdm" class="flat" cols="50" rows="'.ROWS_5.'">'.$consult->hdm.'</textarea>';

        //print '</td><td valign="top">';
        //print '</td></tr><tr><td>';
        print '</div></div></div>';

        print '<div class="fichecenter"><div class="fichehalfleft">';

        print '<table class="notopnoleftnoright" id="adddiagbox" width="100%">';
        //print '<tr><td><br></td></tr>';
        print '<tr><td valign="top" width="160">';
        print $langs->trans("DiagnostiqueLesionnel").':';
        print '</td><td>';
        //print '<input type="text" size="3" class="flat" name="searchdiagles" value="'.GETPOST("searchdiagles").'" id="searchdiagles">';
        print listdiagles(1,$width);
        print ' <input type="button" class="button" id="adddiaglesprinc" name="adddiaglesprinc" value="+P" title="'.dol_escape_htmltag($langs->trans("ClickHereToSetPrimaryDiagnostic")).'">';
        print ' <input type="button" class="button" id="adddiaglessec" name="adddiaglessec" value="+S" title="'.dol_escape_htmltag($langs->trans("ClickHereToSetSecondaryDiagnostic")).'">';
        if ($user->admin) print ' '.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        print '</td></tr>';
        print '<tr><td class="fieldrequired">'.$langs->trans("Primary").':';
        print '</td><td>';
        print '<input type="text" size="32" class="flat" name="diaglesprinc" value="'.$consult->diaglesprinc.'" id="diaglesprinc"><br>';
        print '</td></tr>';
        print '<tr><td valign="top">'.$langs->trans("Secondaries").':';
        print '</td><td>';
        print '<textarea class="flat" name="diaglessec" id="diaglessec" cols="40" rows="'.ROWS_3.'">';
        print $consult->diaglessec;
        print '</textarea>';
        print '</td>';
        print '</tr>';
        print '</table>';

        //print '</td><td>';
        print '</div><div class="fichehalfright"><div class="ficheaddleft">';

        print ''.$langs->trans("ExamensCliniques").'<br>';
        print '<textarea name="examenclinique" id="examenclinique" class="flat" cols="50" rows="'.ROWS_6.'">'.$consult->examenclinique.'</textarea>';

        print '</div></div></div>';
        //print '</td></tr>';
        //print '</table>';
        //print '</fieldset>';

        print '<div class="fichecenter"></div>';

        // Prescriptions
        //print '<fieldset id="fieldsetprescription">';
        //print '<legend>'.$langs->trans("Prescription").'</legend>'."\n";
        print '<hr style="height:1px; color: #dddddd;">';

        print '<div class="fichecenter"><div class="fichehalfleft">';
        //print '<table class="notopnoleftnoright" width="100%">';
        //print '<tr><td width="60%" valign="top">';

        print '<table class="notopnoleftnoright" id="addexambox" width="100%">';

        print '<tr><td valign="top" width="160">';
        print $langs->trans("ExamensPrescrits").':';
        print '</td><td>';
        //print '<input type="text" size="3" class="flat" name="searchexamenprescrit" value="'.GETPOST("searchexamenprescrit").'" id="searchexamenprescrit">';
        listexamen(1,$width,'',0,'examenprescrit');
        print ' <input type="button" class="button" id="addexamenprescrit" name="addexamenprescrit" value="+">';
        if ($user->admin) print ' '.info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        print '</td></tr>';
        print '<tr><td valign="top">';
        print '</td><td>';
        print '<textarea class="flat" name="examenprescrit" id="examenprescrit" cols="40" rows="'.ROWS_4.'">';
        print $consult->examenprescrit;
        print '</textarea>';
        print '</td>';
        print '</tr>';

        print '<tr><td valign="top"><br>'.$langs->trans("Commentaires").':';
        print '</td><td><br>';
        print '<textarea name="comment" id="comment" class="flat" cols="40" rows="'.($nboflines-1).'">'.$consult->comment.'</textarea>';
        print '</td></tr>';

        print '</table>';

        //print '</td><td valign="top">';
        print '</div><div class="fichehalfright"><div class="ficheaddleft">';

        print $langs->trans("TraitementsPrescrits").'<br>';
        print '<textarea name="traitementprescrit" class="flat" cols="50" rows="'.($nboflines+1).'">'.$consult->traitementprescrit.'</textarea><br>';
        print $langs->trans("Infiltrations").'<br>';
        print '<textarea name="infiltration" id="infiltration" class="flat" cols="50" rows="'.ROWS_2.'">'.$consult->infiltration.'</textarea><br>';
        //print '<input type="text" class="flat" name="infiltration" id="infiltration" value="'.$consult->infiltration.'" size="50">';

        print '<br><b>'.$langs->trans("TypeVisite").'</b>: &nbsp; &nbsp; &nbsp; ';
        print '<input type="radio" class="flat" name="typevisit" value="CS" id="cs"'.($consult->typevisit=='CS'?' checked="checked"':'').'> '.$langs->trans("CS");
        print ' &nbsp; &nbsp; ';
        print '<input type="radio" class="flat" name="typevisit" value="CS2" id="c2"'.($consult->typevisit=='CS2'?' checked="checked"':'').'> '.$langs->trans("CS2");
        print ' &nbsp; &nbsp; ';
        print '<input type="radio" class="flat" name="typevisit" value="CCAM" id="ccam"'.($consult->typevisit=='CCAM'?' checked="checked"':'').'> '.$langs->trans("CCAM");
        print '<br>';
        print '<br>'.$langs->trans("CodageCCAM").': &nbsp; ';
        print '<input type="text" class="flat" name="codageccam" id="idcodageccam" value="'.$consult->codageccam.'" size="30">';	// name must differ from id
        print '</td></tr>';

        print '</table>';

        print '</div></div></div>';

        print '</fieldset>'; // End of general information

        print '<br>';

        print '<fieldset id="fieldsetanalyse">';
        print '<legend>'.$langs->trans("Paiement").'</legend>'."\n";

        // Try to autodetect the default bank account to use. For this we search opened account with user name into label or owner
        $defaultbankaccountchq=0;
        $defaultbankaccountliq=0;
        $sql="SELECT rowid, label, bank, courant";
        $sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
        $sql.= " WHERE clos = 0";
        $sql.= " AND entity = ".$conf->entity;
        $sql.= " AND (proprio LIKE '%".$user->lastname."%' OR label LIKE '%".$user->lastname."%')";
        $sql.= " ORDER BY label";
        //print $sql;
        $resql=$db->query($sql);
        if ($resql)
        {
            $num=$db->num_rows($resql);
            $i=0;
            while($i < $num)
            {
                $obj=$db->fetch_object($resql);
                if ($obj)
                {
                    if ($obj->courant == 1) $defaultbankaccountchq=$obj->rowid;
                    if ($obj->courant == 2) $defaultbankaccountliq=$obj->rowid;
                }
                $i++;
            }
        }


        print '<table class="notopnoleftnoright" id="paymentsbox" width="100%">';

        // Cheque
        print '<tr class="cabpaymentcheque"><td width="160">';
        print ''.$langs->trans("PaymentTypeCheque").'</td><td>';
        //print '<table class="nobordernopadding"><tr><td>';
        print '<input type="text" class="flat" name="montant_cheque" id="idmontant_cheque" value="'.($consult->montant_cheque!=''?price($consult->montant_cheque):'').'" size="5">';
        if ($conf->banque->enabled)
        {
        	print ' &nbsp; '.$langs->trans("RecBank").' ';
            $form->select_comptes(GETPOST('bankchequeto')?GETPOST('bankchequeto'):($consult->bank['CHQ']['account_id']?$consult->bank['CHQ']['account_id']:$defaultbankaccountchq),'bankchequeto',2,'courant = 1',1);
        }
        //print '</td><td>';
        print ' &nbsp; ';
        print $langs->trans("ChequeBank").' ';
        //print '<input type="text" class="flat" name="banque" id="banque" value="'.$consult->banque.'" size="18"'.($consult->montant_cheque?'':' disabled="disabled"').'>';
        listebanques(1,0,$consult->banque);
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
        //print '</td></tr><tr><td></td><td>';
        if ($conf->banque->enabled)
        {
        	print ' &nbsp; '.$langs->trans("ChequeOrTransferNumber").' ';
        	print '<input type="text" class="flat" name="num_cheque" id="idnum_cheque" value="'.$consult->num_cheque.'" size="6">';
        }
        //print '</td></tr></table>';
        print '</td></tr>';
        // Card
        print '<tr class="cabpaymentcarte"><td>';
        print $langs->trans("PaymentTypeCarte").'</td><td>';
        print '<input type="text" class="flat" name="montant_carte" id="idmontant_carte" value="'.($consult->montant_carte!=''?price($consult->montant_carte):'').'" size="5">';
        if ($conf->banque->enabled)
        {
            print ' &nbsp; '.$langs->trans("RecBank").' ';
            $form->select_comptes(GETPOST('bankcarteto')?GETPOST('bankcarteto'):($consult->bank['CB']['account_id']?$consult->bank['CB']['account_id']:$defaultbankaccountchq),'bankcarteto',2,'courant = 1',1);
        }
        print '</td></tr>';
        // Cash
        print '<tr class="cabpaymentcash"><td>';
        print $langs->trans("PaymentTypeEspece").'</td><td>';
        print '<input type="text" class="flat" name="montant_espece" id="idmontant_espece" value="'.($consult->montant_espece!=''?price($consult->montant_espece):'').'" size="5">';
        if ($conf->banque->enabled)
        {
            print ' &nbsp; '.$langs->trans("RecBank").' ';
            $form->select_comptes(GETPOST('bankespeceto')?GETPOST('bankespeceto'):($consult->bank['LIQ']['account_id']?$consult->bank['LIQ']['account_id']:$defaultbankaccountliq),'bankespeceto',2,'courant = 2',1);
        }
        print '</td></tr>';
        // Third party
        print '<tr class="cabpaymentthirdparty"><td>';
        print $langs->trans("PaymentTypeThirdParty").'</td><td>';
        print '<input type="text" class="flat" name="montant_tiers" id="idmontant_tiers" value="'.($consult->montant_tiers!=''?price($consult->montant_tiers):'').'" size="5">';
        print ' &nbsp; ('.$langs->trans("ZeroHereIfNoPayment").')';
        print '</td></tr>';

        print '</table>';
        print '</fieldset>';

        print '<br>';

        dol_htmloutput_errors($mesg,$mesgarray);


        print '<center>';
        if ($action == 'edit')
        {
        	// Set option if not defined
        	if (! isset($conf->global->CABINETMED_DELAY_TO_LOCK_RECORD)) $conf->global->CABINETMED_DELAY_TO_LOCK_RECORD=30;

        	// If consult was create before current date - CABINETMED_DELAY_TO_LOCK_RECORD days.
        	if (! empty($conf->global->CABINETMED_DELAY_TO_LOCK_RECORD) && $consult->date_c < ($now - ($conf->global->CABINETMED_DELAY_TO_LOCK_RECORD * 24 * 3600)))
        	{
            	print '<input type="submit" class="button ignorechange" id="updatebutton" name="update" value="'.$langs->trans("Save").'" disabled="disabled" title="'.dol_escape_htmltag($langs->trans("ConsultTooOld",$conf->global->CABINETMED_DELAY_TO_LOCK_RECORD)).'">';
        	}
        	else
        	{
            	print '<input type="submit" class="button ignorechange" id="updatebutton" name="update" value="'.$langs->trans("Save").'">';
        	}
        }
        if ($action == 'create')
        {
            print '<input type="submit" class="button ignorechange" id="addbutton" name="add" value="'.$langs->trans("Add").'">';
        }
        print ' &nbsp; &nbsp; ';
        print '<input type="submit" class="button ignorechange" id="cancelbutton" name="cancel" value="'.$langs->trans("Cancel").'">';
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
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?socid='.$object->id.'&amp;action=create">'.$langs->trans("NewConsult").'</a>';
    }

    print '</div>';
}


if ($action == '' || $action == 'delete')
{
    if ($object->alert_antemed)       $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("AntecedentsMed"));
    if ($object->alert_antechirgen)   $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("AntecedentsChirGene"));
    if ($object->alert_antechirortho) $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("AntecedentsChirOrtho"));
    if ($object->alert_anterhum)      $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("AntecedentsRhumato"));
    if ($object->alert_other)         $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("AntecedentsMed"));
    if ($object->alert_traitclass)    $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("xxx"));
    if ($object->alert_traitallergie) $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("Allergies"));
    if ($object->alert_traitintol)    $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("Intolerances"));
    if ($object->alert_traitspec)     $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("SpecPharma"));
    if ($object->alert_note)          $mesgs[]=$langs->transnoentitiesnoconv("Warning").': '.$langs->transnoentitiesnoconv("AlertTriggered",$langs->transnoentitiesnoconv("Note"));

    // Confirm delete consultation
    if (GETPOST("action") == 'delete')
    {
        $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?socid=".$socid.'&id='.GETPOST('id','int'),$langs->trans("DeleteAConsultation"),$langs->trans("ConfirmDeleteConsultation"),"confirm_delete",'',0,1);
        if ($ret == 'html') print '<br>';
    }

    print_fiche_titre($langs->trans("ListOfConsultations"));


    dol_htmloutput_mesg('',$mesgs,'warning');


    $param='&socid='.$socid;

    print "\n";
    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('Num'),$_SERVER['PHP_SELF'],'t.rowid','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'t.datecons','',$param,'',$sortfield,$sortorder);
    if (! empty($conf->global->CABINETMED_FRENCH_PRISEENCHARGE))
    {
        print_liste_field_titre($langs->trans('Priseencharge'),$_SERVER['PHP_SELF'],'t.typepriseencharge','',$param,'',$sortfield,$sortorder);
    }
    print_liste_field_titre($langs->trans('DiagLesPrincipal'),$_SERVER['PHP_SELF'],'t.diaglesprinc','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('ConsultActe'),$_SERVER['PHP_SELF'],'t.typevisit','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('MontantPaiement'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('TypePaiement'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
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
    $sql.= " t.hdm,";
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
    $sql.= " t.banque";
//    $sql.= " bu.fk_bank, b.fk_account, b.fk_type";
    $sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_cons as t";
//    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_url as bu on bu.url_id = t.rowid AND type = 'consultation'";
//    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank as b on bu.fk_bank = b.rowid";
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

            $consult->id=$obj->rowid;
            $consult->fetch_bankid();

            $var=!$var;
            print '<tr '.$bc[$var].'><td>';
            print '<a href="'.$_SERVER["PHP_SELF"].'?socid='.$obj->fk_soc.'&id='.$obj->rowid.'&action=edit">'.sprintf("%08d",$obj->rowid).'</a>';
            print '</td><td>';
            print dol_print_date($db->jdate($obj->datecons),'day');
            print '</td>';
            if (! empty($conf->global->CABINETMED_FRENCH_PRISEENCHARGE))
            {
                print '<td>';
                print $obj->typepriseencharge;
                print '</td>';
            }
            print '<td>';
            print dol_trunc($obj->diaglesprinc,32);
            print '</td>';
            print '<td>';
            //print dol_print_date($obj->diaglesprinc,'day');
            //print '</td><td>';
            print $langs->trans($obj->typevisit);
            print '</td>';
            print '<td>';
            $foundamount=0;
            if (price2num($obj->montant_cheque) > 0) {
                if ($foundamount) print '+';
                print price($obj->montant_cheque);
                $foundamount++;
            }
            if (price2num($obj->montant_espece) > 0)  {
                if ($foundamount) print '+';
                print price($obj->montant_espece);
                $foundamount++;
            }
            if (price2num($obj->montant_carte) > 0)  {
                if ($foundamount) print '+';
                print price($obj->montant_carte);
                $foundamount++;
            }
            if (price2num($obj->montant_tiers) > 0)  {
                if ($foundamount) print '+';
                print price($obj->montant_tiers);
                $foundamount++;
            }
            print '</td>';

            print '<td>';
            $foundamount=0;
            if (price2num($obj->montant_cheque) > 0) {
                if ($foundamount) print ' + ';
                print $langs->trans("Cheque");
                if ($conf->banque->enabled && $consult->bank['CHQ']['account_id'])
                {
                    $bank=new Account($db);
                    $bank->fetch($consult->bank['CHQ']['account_id']);
                    print '&nbsp;('.$bank->getNomUrl(0,'transactions').')';
                }
                $foundamount++;
            }
            if (price2num($obj->montant_espece) > 0)  {
                if ($foundamount) print ' + ';
                print $langs->trans("Cash");
                if ($conf->banque->enabled && $consult->bank['LIQ']['account_id'])
                {
                    $bank=new Account($db);
                    $bank->fetch($consult->bank['LIQ']['account_id']);
                    print '&nbsp;('.$bank->getNomUrl(0,'transactions').')';
                }
                $foundamount++;
            }
            if (price2num($obj->montant_carte) > 0)  {
                if ($foundamount) print ' + ';
                print $langs->trans("CreditCard");
                if ($conf->banque->enabled && $consult->bank['CB']['account_id'])
                {
                    $bank=new Account($db);
                    $bank->fetch($consult->bank['CB']['account_id']);
                    print '&nbsp;('.$bank->getNomUrl(0,'transactions').')';
                }
                $foundamount++;
            }
            if (price2num($obj->montant_tiers) > 0)  {
                if ($foundamount) print ' + ';
                print $langs->trans("PaymentTypeThirdParty");
                if ($conf->banque->enabled && $consult->bank['OTH']['account_id'])
                {
                    $bank=new Account($db);
                    $bank->fetch($consult->bank['OTH']['account_id']);
                    print '&nbsp;('.$bank->getNomUrl(0,'transactions').')';
                }
                $foundamount++;
            }
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
    print '</table>';
}


llxFooter();

$db->close();
?>
