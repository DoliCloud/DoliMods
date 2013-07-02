<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Marc Barilley / Ocebo <marc@ocebo.com>
 * Copyright (C) 2005-2010 Regis Houssin         <regis@dolibarr.fr>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerke@telenet.be>
 * Copyright (C) 2012      Juanjo Menent		 <jmenent@2byte.es>
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
 *	\file       htdocs/pos/backend/paiement.php
 *	\ingroup    pos
 *	\brief      Page to create a payment
 */

$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory
dol_include_once('/pos/backend/class/payment.class.php');
dol_include_once('/pos/backend/class/ticket.class.php');
require_once(DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");

$langs->load('companies');
$langs->load('bills');
$langs->load('banks');

$action		= GETPOST('action');
$confirm	= GETPOST('confirm');

$ticketid	= GETPOST('ticketid');
$accountid	= GETPOST('accountid');
$paymentnum	= GETPOST('num_paiement');


$amounts=array();
$amountsresttopay=array();
$addwarning=0;

// Security check
$socid=0;
if ($user->societe_id > 0)
{
    $socid = $user->societe_id;
}



/*
 * Action add_paiement et confirm_paiement
 */
if ($action == 'add_paiement' || ($action == 'confirm_paiement' && $confirm=='yes'))
{
    $error = 0;

    $datepaye = dol_mktime(12, 0 , 0,
    $_POST['remonth'],
    $_POST['reday'],
    $_POST['reyear']);
    $paiement_id = 0;

    // Verifie si des paiements sont superieurs au montant facture
    foreach ($_POST as $key => $value)
    {
        if (substr($key,0,7) == 'amount_')
        {
            $cursorticketid = substr($key,7);
            $amounts[$cursorticketid] = price2num($_POST[$key]);
            $totalpaiement = $totalpaiement + $amounts[$cursorticketid];
            $tmpfacture=new Ticket($db);
            $tmpfacture->fetch($cursorticketid);
            $amountsresttopay[$cursorticketid]=price2num($tmpfacture->total_ttc-$tmpfacture->getSommePaiement());
            if ($amounts[$cursorticketid] && $amounts[$cursorticketid] > $amountsresttopay[$cursorticketid])
            {
                $addwarning=1;
                $formquestion['text'] = img_warning($langs->trans("PaymentHigherThanReminderToPay")).' '.$langs->trans("HelpPaymentHigherThanReminderToPay");
            }

            $formquestion[$i++]=array('type' => 'hidden','name' => $key,  'value' => $_POST[$key]);
        }
    }

    // Check parameters
    if (! GETPOST('paiementcode'))
    {
        $fiche_erreur_message = '<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentities('PaymentMode')).'</div>';
        $error++;
    }

    if ($conf->banque->enabled)
    {
        // Si module bank actif, un compte est obligatoire lors de la saisie
        // d'un paiement
        if (! $_POST['accountid'])
        {
            $fiche_erreur_message = '<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentities('AccountToCredit')).'</div>';
            $error++;
        }
    }

    if ($totalpaiement == 0)
    {
        $fiche_erreur_message = '<div class="error">'.$langs->transnoentities('ErrorFieldRequired',$langs->trans('PaymentAmount')).'</div>';
        $error++;
    }

    if (empty($datepaye))
    {
        $fiche_erreur_message = '<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentities('Date')).'</div>';
        $error++;
    }
}

/*
 * Action add_paiement
 */
if ($action == 'add_paiement')
{
    if ($error)
    {
        $action = 'create';
    }
    // Le reste propre a cette action s'affiche en bas de page.
}

/*
 * Action confirm_paiement
 */
if ($action == 'confirm_paiement' && $confirm == 'yes')
{
    $error=0;

    $datepaye = dol_mktime(12, 0, 0, $_POST['remonth'], $_POST['reday'], $_POST['reyear']);

    $db->begin();

    // Creation of payment line
    $paiement = new Payment($db);
    $paiement->datepaye     = $datepaye;
    $paiement->amounts      = $amounts;   // Array with all payments dispatching
    $paiement->paiementid   = dol_getIdFromCode($db,$_POST['paiementcode'],'c_paiement');
    $paiement->num_paiement = $_POST['num_paiement'];
    $paiement->note         = $_POST['comment'];

    if (! $error)
    {
        $paiement_id = $paiement->create($user);
        if ($paiement_id < 0)
        {
            $errmsg=$paiement->error;
            $error++;
        }
    }

    if (! $error)
    {
        $result=$paiement->addPaymentToBank($user,'payment','(CustomerTicketPayment)',$_POST['accountid'],$_POST['socid'],$_POST['chqemetteur'],$_POST['chqbank']);
        if ($result < 0)
        {
            $errmsg=$paiement->error;
            $error++;
        }
    }

    if (! $error)
    {
        $db->commit();
        
        // If payment dispatching on more than one ticket, we keep on summary page, otherwise go on invoice card
        $invoiceid=0;
        foreach ($paiement->amounts as $key => $amount)
        {
            $ticketid = $key;
            if (is_numeric($amount) && $amount <> 0)
            {
                if ($invoiceid != 0) $invoiceid=-1; // There is more than one invoice payed by this payment
                else $invoiceid=$ticketid;
            }
        }
        
        
        if ($invoiceid > 0) 
        {
			// Payments already done (from payment on this invoice)
			$sql = 'SELECT p.datep as dp, p.num_paiement, p.rowid,';
			$sql.= ' c.code as payment_code, c.libelle as payment_label,';
			$sql.= ' pf.amount';
			$sql.= ' FROM '.MAIN_DB_PREFIX.'paiement as p, '.MAIN_DB_PREFIX.'c_paiement as c, '.MAIN_DB_PREFIX.'pos_paiement_ticket as pf';
			$sql.= ' WHERE pf.fk_ticket = '.$invoiceid.' AND p.fk_paiement = c.id AND pf.fk_paiement = p.rowid';
			$sql.= ' ORDER BY dp, tms';

			$result = $db->query($sql);
			if ($result)
			{
				$num = $db->num_rows($result);
				$i = 0;
				$totalpaye=0;
				while ($i < $num)
				{
					$objp = $db->fetch_object($result);
					$totalpaye+= $objp->amount;
					$i++;
				}
			}
			$db->free($result);
			
			$sql ="UPDATE ".MAIN_DB_PREFIX."pos_ticket set customer_pay=".$totalpaye." WHERE rowid=".$invoiceid;
			$result = $db->query($sql);
        	
        	$loc =dol_buildpath('/pos/backend/ticket.php',1).'?id='.$invoiceid;
        }
        else $loc = DOL_URL_ROOT.'/compta/paiement/fiche.php?id='.$paiement_id;
        Header('Location: '.$loc);
        exit;
    }
    else
    {
        $db->rollback();
    }
}


/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$html=new Form($db);


if ($action == 'create' || $action == 'confirm_paiement' || $action == 'add_paiement')
{
    $ticket = new Ticket($db);
    $result=$ticket->fetch($ticketid);

    if ($result >= 0)
    {
        $ticket->fetch_thirdparty();

        $title='';
        $title.=$langs->trans("EnterPaymentReceivedFromCustomer");
        print_fiche_titre($title);

        dol_htmloutput_errors($errmsg);

        // Initialize data for confirmation (this is used because data can be change during confirmation)
        if ($action == 'add_paiement')
        {
            $i=0;

            $formquestion[$i++]=array('type' => 'hidden','name' => 'ticketid', 'value' => $ticket->id);
            $formquestion[$i++]=array('type' => 'hidden','name' => 'socid', 'value' => $ticket->socid);
            $formquestion[$i++]=array('type' => 'hidden','name' => 'type',  'value' => $ticket->type);
        }

        // Invoice with Paypal transaction
        if ($conf->paypalplus->enabled && $conf->global->PAYPAL_ENABLE_TRANSACTION_MANAGEMENT && ! empty($ticket->ref_int))
        {
        	if (! empty($conf->global->PAYPAL_BANK_ACCOUNT)) $accountid=$conf->global->PAYPAL_BANK_ACCOUNT;
        	$paymentnum=$ticket->ref_int;
        }

        print '<form id="payment_form" name="add_paiement" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="add_paiement">';
        print '<input type="hidden" name="ticketid" value="'.$ticket->id.'">';
        print '<input type="hidden" name="socid" value="'.$ticket->socid.'">';
        print '<input type="hidden" name="type" value="'.$ticket->type.'">';
        print '<input type="hidden" name="thirdpartylabel" id="thirdpartylabel" value="'.dol_escape_htmltag($ticket->client->name).'">';

        print '<table class="border" width="100%">';

        // Third party
        print '<tr><td><span class="fieldrequired">'.$langs->trans('Company').'</span></td><td colspan="2">'.$ticket->client->getNomUrl(4)."</td></tr>\n";

        // Date payment
        print '<tr><td><span class="fieldrequired">'.$langs->trans('Date').'</span></td><td>';
        $datepayment = dol_mktime(12, 0 , 0, $_POST['remonth'], $_POST['reday'], $_POST['reyear']);
        $datepayment= ($datepayment == '' ? (empty($conf->global->MAIN_AUTOFILL_DATE)?-1:0) : $datepayment);
        $html->select_date($datepayment,'','','',0,"add_paiement",1,1);
        print '</td>';
        print '<td>'.$langs->trans('Comments').'</td></tr>';

        $rowspan=5;
        if ($conf->use_javascript_ajax && !empty($conf->global->MAIN_JS_ON_PAYMENT)) $rowspan++;

        // Payment mode
        print '<tr><td><span class="fieldrequired">'.$langs->trans('PaymentMode').'</span></td><td>';
        $html->select_types_paiements((GETPOST('paiementcode')?GETPOST('paiementcode'):$ticket->mode_reglement_id),'paiementcode','',2);
        print "</td>\n";
        print '<td rowspan="'.$rowspan.'" valign="top">';
        print '<textarea name="comment" wrap="soft" cols="60" rows="'.ROWS_4.'">'.(empty($_POST['comment'])?'':$_POST['comment']).'</textarea></td>';
        print '</tr>';

        print '<tr>';
        if ($conf->banque->enabled)
        {
            print '<td><span class="fieldrequired">'.$langs->trans('AccountToCredit').'</span></td>';
            //if ($ticket->type == 2) print '<td><span class="fieldrequired">'.$langs->trans('AccountToDebit').'</span></td>';
            print '<td>';
            $html->select_comptes($accountid,'accountid',0,'',2);
            print '</td>';
        }
        else
        {
            print '<td colspan="2">&nbsp;</td>';
        }
        print "</tr>\n";

        // Cheque number
        print '<tr><td>'.$langs->trans('Numero');
        print ' <em>('.$langs->trans("ChequeOrTransferNumber").')</em>';
        print '</td>';
        print '<td><input name="num_paiement" type="text" value="'.$paymentnum.'"></td></tr>';

        // Check transmitter
        print '<tr><td class="'.(GETPOST('paiementcode')=='CHQ'?'fieldrequired ':'').'fieldrequireddyn">'.$langs->trans('CheckTransmitter');
        print ' <em>('.$langs->trans("ChequeMaker").')</em>';
        print '</td>';
        print '<td><input id="fieldchqemetteur" name="chqemetteur" size="30" type="text" value="'.GETPOST('chqemetteur').'"></td></tr>';

        // Bank name
        print '<tr><td>'.$langs->trans('Bank');
        print ' <em>('.$langs->trans("ChequeBank").')</em>';
        print '</td>';
        print '<td><input name="chqbank" size="30" type="text" value="'.GETPOST('chqbank').'"></td></tr>';

        print '</table>';

        /*
         * List of unpaid invoices
         */
        $sql = 'SELECT f.rowid as ticketid, f.ticketnumber, f.total_ttc, f.type, ';
        $sql.= ' f.date_closed as df';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
        $sql.= ' WHERE f.fk_soc = '.$ticket->socid;
        $sql.= ' AND f.total_ttc > f.customer_pay';
        $sql.= ' AND f.fk_statut in (1,2,3)'; 
        $sql.= ' AND type=0';	// Standard invoice, replacement, deposit
       
        $resql = $db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            if ($num > 0)
            {

                $i = 0;
                //print '<tr><td colspan="3">';
                print '<br>';
                print '<table class="noborder" width="100%">';
                print '<tr class="liste_titre">';
                print '<td>'.$langs->trans('Ticket').'</td>';
                print '<td align="center">'.$langs->trans('Date').'</td>';
                print '<td align="right">'.$langs->trans('AmountTTC').'</td>';
                print '<td align="right">'.$langs->trans('Received').'</td>';
                print '<td align="right">'.$langs->trans('RemainderToPay').'</td>';
                print '<td align="right">'.$langs->trans('PaymentAmount').'</td>';
                print '<td align="right">&nbsp;</td>';
                print "</tr>\n";

                $var=True;
                $total=0;
                $totalrecu=0;
                $totalrecucreditnote=0;
                $totalrecudeposits=0;

                while ($i < $num)
                {
                    $objp = $db->fetch_object($resql);
                    $var=!$var;

                    $invoice=new Ticket($db);
                    $invoice->fetch($objp->ticketid);
                    $paiement = $invoice->getSommePaiement();
                    $alreadypayed=price2num($paiement,'MT');
                    $remaintopay=price2num($invoice->total_ttc - $paiement,'MT');
					//if ($remaintopay>0)
					//{
	                    print '<tr '.$bc[$var].'>';
	
	                    print '<td>';
	                    print $invoice->getNomUrl(1,'');
	                    print "</td>\n";
	
	                    // Date
	                    print '<td align="center">'.dol_print_date($db->jdate($objp->df),'day')."</td>\n";
	
	                    // Prix
	                    print '<td align="right">'.price($objp->total_ttc).'</td>';
	
	                    // Recu
	                    print '<td align="right">'.price($paiement);
	                    if ($creditnotes) print '+'.price($creditnotes);
	                    if ($deposits) print '+'.price($deposits);
	                    print '</td>';
	
	                    // Remain to pay
	                    print '<td align="right">'.price($remaintopay).'</td>';
	                    $test= price(price2num($objp->total_ttc - $paiement - $creditnotes - $deposits));
	
	                    // Amount
	                    print '<td align="right">';
	
	                    // Add remind amount
	                    $namef = 'amount_'.$objp->ticketid;
	                    $nameRemain = 'remain_'.$objp->ticketid;
	
	                    if ($action != 'add_paiement')
	                    {
	                        if ($conf->use_javascript_ajax && !empty($conf->global->MAIN_JS_ON_PAYMENT))
	                        {
	                            print img_picto($langs->trans('AddRemind'),'rightarrow.png','id="'.$objp->ticketid.'" "');
	                        }
	                        print '<input type=hidden name="'.$nameRemain.'" value="'.$remaintopay.'">';
	                        print '<input type="text" size="8" name="'.$namef.'" value="'.$_POST[$namef].'">';
	                    }
	                    else
	                    {
	                        print '<input type="text" size="8" name="'.$namef.'_disabled" value="'.$_POST[$namef].'" disabled="true">';
	                        print '<input type="hidden" name="'.$namef.'" value="'.$_POST[$namef].'">';
	                    }
	                    print "</td>";
	
	                    // Warning
	                    print '<td align="center" width="16">';
	                    if ($amounts[$invoice->id] && $amounts[$invoice->id] > $amountsresttopay[$invoice->id])
	                    {
	                        print ' '.img_warning($langs->trans("PaymentHigherThanReminderToPay"));
	                    }
	                    print '</td>';
	
	
	                    print "</tr>\n";
	
	                    $total+=$objp->total;
	                    $total_ttc+=$objp->total_ttc;
	                    $totalrecu+=$paiement;
	                    $totalrecucreditnote+=$creditnotes;
	                    $totalrecudeposits+=$deposits;
	                    $i++;
					//}
                }
                if ($i > 1)
                {
                    // Print total
                    print '<tr class="liste_total">';
                    print '<td colspan="2" align="left">'.$langs->trans('TotalTTC').'</td>';
                    print '<td align="right"><b>'.price($total_ttc).'</b></td>';
                    print '<td align="right"><b>'.price($totalrecu);
                    print '</b></td>';
                    print '<td align="right"><b>'.price(price2num($total_ttc - $totalrecu,'MT')).'</b></td>';
                    print '<td align="right" id="result" style="font-weight:bold;"></td>';
                    print '<td align="center">&nbsp;</td>';
                    print "</tr>\n";
                }
                print "</table>";
                //print "</td></tr>\n";
            }
            $db->free($resql);
        }
        else
        {
            dol_print_error($db);
        }


        // Bouton Enregistrer
        if ($action != 'add_paiement')
        {
            print '<center><br><input type="submit" class="button" value="'.$langs->trans('Save').'"><br></center>';
        }

        // Message d'erreur
        if ($fiche_erreur_message)
        {
            print $fiche_erreur_message;
        }

        // Form to confirm payment
        if ($action == 'add_paiement')
        {
            $preselectedchoice=$addwarning?'no':'yes';

            print '<br>';
            $text=$langs->trans('ConfirmCustomerPayment',$totalpaiement,$langs->trans(currency_name($conf->currency)));
            if (GETPOST('closepaidinvoices'))
            {
                $text.='<br>'.$langs->trans("AllCompletelyPayedInvoiceWillBeClosed");
                print '<input type="hidden" name="closepaidinvoices" value="'.GETPOST('closepaidinvoices').'">';
            }
            $html->form_confirm($_SERVER['PHP_SELF'].'?ticketid='.$ticket->id.'&socid='.$ticket->socid.'&type='.$ticket->type,$langs->trans('ReceivedCustomersPayments'),$text,'confirm_paiement',$formquestion,$preselectedchoice);
        }

        print "</form>\n";
    }
}

$db->close();

llxFooter('$Date: 2011/08/08 01:01:46 $ - $Revision: 1.114 $');
?>