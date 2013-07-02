<?php
/* Copyright (C) 2011-2012      Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2012      		Ferran Marcet         <fmarcet@2byte.es>
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
 *	\file       htdocs/pos/backed/ticket.php
 *	\ingroup    pos
 *	\brief      Page to see an ticket
 */

$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory
dol_include_once('/pos/backend/class/ticket.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
dol_include_once('/pos/backend/class/pos.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
dol_include_once('/pos/backend/lib/ticket.lib.php');
dol_include_once('/pos/backend/class/place.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");

$langs->load('pos@pos');
$langs->load('companies');
$langs->load('products');
$langs->load('main');
$langs->load('bills');

// Security check
$ticketid = isset($_GET["ticketid"])?$_GET["ticketid"]:'';
if ($user->societe_id) $socid=$user->societe_id;
if (!$user->rights->pos->backend)
accessforbidden();

$id=GETPOST('id');
$ref=GETPOST('ref');

$socid=GETPOST('socid');
$action=GETPOST('action');

$mesg = $_SESSION['message'];
$_SESSION['message'] = '';

$object=new Ticket($db);
$result=$object->fetch($id,$ref);

if (($action == 'send') && ! GETPOST('addfile') && ! GETPOST('removedfile') && ! GETPOST('cancel'))
{
	$langs->load('mails');
	$actiontypecode='';$subject='';$actionmsg='';$actionmsg2='';

	if (GETPOST('sendto'))
	{
		// Le destinataire a ete fourni via le champ libre
		$sendto = GETPOST('sendto');
		$sendtoid = 0;
	}
	if (dol_strlen($sendto))
	{
		$langs->load("commercial");

		$from =  $conf->global->MAIN_INFO_SOCIETE_NOM."<".$conf->global->MAIN_INFO_SOCIETE_MAIL.">";
		$message = GETPOST('message','alpha');
		
		if (GETPOST('action','alpha') == 'send')
		{
			if (dol_strlen(GETPOST('subject','alpha'))) $subject = GETPOST('subject','alpha');
			else $subject = $langs->transnoentities('Bill').' '.$object->ref;
			$actiontypecode='AC_FAC';
			$actionmsg=$langs->transnoentities('MailSentBy').' '.$from.' '.$langs->transnoentities('To').' '.$sendto.".\n";
			if ($message)
			{
				$actionmsg.=$langs->transnoentities('MailTopic').": ".$subject."\n";
				$actionmsg.=$langs->transnoentities('TextUsedInTheMessageBody').":\n";
				$actionmsg.=$message;
			}
		}

				
		// Send mail
		require_once(DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php');
		$mailfile = new CMailFile($subject,$sendto,$from,$message);
		if(!preg_match("/^(?:[\w\d]+\.?)+@(?:(?:[\w\d]\-?)+\.)+\w{2,4}$/", $sendto)) {
			$mailfile->error = $langs->trans('ErrorFailedToSendMail',$from,$sendto); 
		}
		
    	if ($mailfile->error)
		{
			$mesg='<div class="error">'.$mailfile->error.'</div>';
		}
		else
		{
			$result=$mailfile->sendfile();
			if ($result)
			{
				$mesg=$langs->trans('MailSuccessfulySent',$from,$sendto);		// Must not contain "

				$_SESSION['message'] = $mesg;
				
				Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id.'&mesg=1');
				//exit;

			}
			else
			{
				$langs->load("other");
				$mesg='<div class="error">';
				if ($mailfile->error)
				{
					$mesg.=$langs->trans('ErrorFailedToSendMail',$from,$sendto);
					$mesg.='<br>'.$mailfile->error;
				}
				else
				{
					$mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
				}
				$mesg.='</div>';
			}
		}
	}
	else
	{
		$langs->load("other");
		$mesg='<div class="error">'.$langs->trans('ErrorMailRecipientIsEmpty').'</div>';
		dol_syslog('Recipient email is empty');
	}

	$_GET['action'] = 'presend';
}
if ($action=='send' && $cancel)
{
	include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php');
	$formmail = new FormMail($db);
	$formmail->clear_attached_files();

	Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
	exit;
}
if (GETPOST('action','alpha') == 'confirm_deleteticket' )
{
	$result=$object->delete_ticket();
	
	
	$mesg = '<div class="ok">'.$langs->trans("TicketWasRemoved").'</div>';

	header('Location: '.dol_buildpath('/pos/backend/liste.php',1).'?id='.$id);
}

if (GETPOST('action','string') == 'confirm_abandonticket' )
{
	$result=$object->set_canceled();

	$mesg = '<div class="ok">'.$langs->trans("TicketWasAbandoned").'</div>';

	Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
}

if (GETPOST('action','string') == 'confirm_factureticket' )
{
	$result=$object->create_facture();

	$mesg = '<div class="ok">'.$langs->trans("BillWasCreated").'</div>';

	//Header('Location: '.DOL_URL_ROOT.'/compta/facture.php?facid='.$result);
}

if (GETPOST('action','string') == 'reopen' )
{
	$result=$object->set_unpaid();

	Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
}

/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('',$langs->trans('Ticket'),$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$result=$object->fetch($id,$ref);

$head = ticket_prepare_head($object);
dol_fiche_head($head, 'ticket', $langs->trans("Ticket"), 0, 'ticket');

$html = new Form($db);
$htmlother = new FormOther($db);
$formfile = new FormFile($db);
$now=dol_now();

if ($result > 0)
{
	if ($user->societe_id>0 && $user->societe_id!=$object->socid)  accessforbidden('',0);

	$result=$object->fetch_thirdparty();

	$soc = new Societe($db, $object->socid);
	$soc->fetch($object->socid);

	print '<table class="border" width="100%">';

	// Ref
	print '<tr>';
	print '<td width="20%">'.$langs->trans('Ref').'</td>';
	print '<td colspan="5">';
	$morehtmlref = '';
	//print $html->showrefnav($object,'id','',1,'ticketnumber','ticketnumber',$morehtmlref);
	print $html->showrefnav($object,'ref','',1,'ticketnumber','ref',$morehtmlref);
	print '</td>';
	print '</tr>';

	// Third party
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr><td>'.$langs->trans('Company').'</td>';
	print '</td><td colspan="5">';
	print '</tr></table>';
	print '</td><td colspan="5">';	
	print ' &nbsp;'.$soc->getNomUrl(1,'compta');	
	print '</td>';
	print '</tr>';

	// Type
	print '<tr>';
	print '<td>'.$langs->trans('Type').'</td><td colspan="5">';
	print $object->getLibType();
	print '</td>';
	print '</tr>';
	
	//Terminal
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr><td>'.$langs->trans('Terminal').'</td>';
	print '</td><td colspan="5">';
	print '</tr></table>';
	print '</td><td colspan="5">';	
	$cash=new Cash($db);
	$cash->fetch($object->fk_cash);
	print $cash->getNomUrl(1);	
	print '</td>';
	print '</tr>';
	
	//Place
	if($conf->global->POS_PLACES){
		print '<tr><td>';
		print '<table class="nobordernopadding" width="100%">';
		print '<tr><td>'.$langs->trans('Place').'</td>';
		print '</td><td colspan="5">';
		print '</tr></table>';
		print '</td><td colspan="5">';
		$place=new Place($db);
		$place->fetch($object->fk_place);
		print $place->getNomUrl(1);
		print '</td>';
		print '</tr>';	
	}
	// Date ticket
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('Date');
	print '</td>';            
	print '</tr></table>';
	print '</td><td colspan="3">';
	if (!empty($object->date_closed))
	{
		print dol_print_date($object->date_closed,'dayhourtext');
	}
	else 
	{
		print dol_print_date($object->date_creation,'dayhourtext');
	}            
 	print '</td>';

 	dol_htmloutput_mesg($mesg);
 	
 	
 	/*
	* List of payments
	*/

	$nbrows=7;

	//Local taxes
	if ($mysoc->pays_code=='ES')
	{
		if($mysoc->localtax1_assuj=="1") $nbrows++;
		if($mysoc->localtax2_assuj=="1") $nbrows++;
	}

	print '<td rowspan="'.$nbrows.'" colspan="2" valign="top">';

	print '<table class="nobordernopadding" width="100%">';

	// List of payments already done
	print '<tr class="liste_titre">';
	print '<td>'.($object->type == 2 ? $langs->trans("PaymentsBack") : $langs->trans('Payments')).'</td>';
	print '<td>'.$langs->trans('Type').'</td>';
	print '<td align="right">'.$langs->trans('Amount').'</td>';
	print '<td width="18">&nbsp;</td>';
	print '</tr>';

	$var=true;

	// Payments already done (from payment on this invoice)
	$sql = 'SELECT p.datep as dp, p.num_paiement, p.rowid,';
	$sql.= ' c.code as payment_code, c.libelle as payment_label,';
	$sql.= ' pf.amount';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'paiement as p, '.MAIN_DB_PREFIX.'c_paiement as c, '.MAIN_DB_PREFIX.'pos_paiement_ticket as pf';
	$sql.= ' WHERE pf.fk_ticket = '.$object->id.' AND p.fk_paiement = c.id AND pf.fk_paiement = p.rowid';
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
			$var=!$var;
			print '<tr '.$bc[$var].'><td>';
			print '<a href="'.DOL_URL_ROOT.'/compta/paiement/fiche.php?id='.$objp->rowid.'">'.img_object($langs->trans('ShowPayment'),'payment').' ';
			print dol_print_date($db->jdate($objp->dp),'day').'</a></td>';
			$label=($langs->trans("PaymentType".$objp->payment_code)!=("PaymentType".$objp->payment_code))?$langs->trans("PaymentType".$objp->payment_code):$objp->payment_label;
			print '<td>'.$label.' '.$objp->num_paiement.'</td>';
			print '<td align="right">'.price($objp->amount).'</td>';
			print '<td>&nbsp;</td>';
			print '</tr>';
			$totalpaye+= $objp->amount;
			$i++;
		}
	}
	$db->free($result);
              
	// Total already paid
	print '<tr><td colspan="2" align="right">';
	print $langs->trans('AlreadyPaid');
	print ' :</td><td align="right">'.price($totalpaye).'</td><td>&nbsp;</td></tr>';
	
	if($object->type==0)
		$total_ttc= $object->total_ttc;
	else 
		$total_ttc= $object->total_ttc*-1;
		
	$resteapayeraffiche=$total_ttc-$totalpaye;

	// Billed
	print '<tr><td colspan="2" align="right">'.$langs->trans("Billed").' :</td><td align="right" style="border: 1px solid;">'.price($total_ttc).'</td><td>&nbsp;</td></tr>';

	// Remainder to pay
	print '<tr><td colspan="2" align="right">';
	if ($resteapayeraffiche >= 0) print $langs->trans('RemainderToPay');
	else print $langs->trans('ExcessReceived');
	print ' :</td>';
	print '<td align="right" style="border: 1px solid;" bgcolor="#f0f0f0"><b>'.price($resteapayeraffiche).'</b></td>';
	print '<td nowrap="nowrap">&nbsp;</td></tr>';
             
	print '</table>';
	print '</td></tr>';

	// Mode de reglement
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('PaymentMode');
	print '</td>';
	
	print '</tr></table>';
	print '</td><td colspan="3">';
	$html->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$object->id,$object->mode_reglement_id,'none');

	print '</td></tr>';

	// Amount
	print '<tr><td>'.$langs->trans('AmountHT').'</td>';
	print '<td align="right" colspan="2" nowrap>'.price($object->total_ht).'</td>';
	print '<td>'.$langs->trans(currency_name($conf->currency)).'</td></tr>';
	print '<tr><td>'.$langs->trans('AmountVAT').'</td><td align="right" colspan="2" nowrap>'.price($object->total_tva).'</td>';
	print '<td>'.$langs->trans(currency_name($conf->currency)).'</td></tr>';

	// Amount Local Taxes
	if ($mysoc->pays_code=='ES')
	{
		if ($mysoc->localtax1_assuj=="1") //Localtax1 RE
		{
			print '<tr><td>'.$langs->transcountry("AmountLT1",$mysoc->pays_code).'</td>';
			print '<td align="right" colspan="2" nowrap>'.price($object->total_localtax1).'</td>';
			print '<td>'.$langs->trans(currency_name($conf->currency)).'</td></tr>';
		}
		if ($mysoc->localtax2_assuj=="1") //Localtax2 IRPF
		{
			print '<tr><td>'.$langs->transcountry("AmountLT2",$mysoc->pays_code).'</td>';
			print '<td align="right" colspan="2" nowrap>'.price($object->total_localtax2).'</td>';
			print '<td>'.$langs->trans(currency_name($conf->currency)).'</td></tr>';
		}
	}

	print '<tr><td>'.$langs->trans('AmountTTC').'</td><td align="right" colspan="2" nowrap>'.price($object->total_ttc).'</td>';
	print '<td>'.$langs->trans(currency_name($conf->currency)).'</td></tr>';

	// Statut
	print '<tr><td>'.$langs->trans('Status').'</td>';
	print '<td align="left" colspan="3">'.($object->getLibStatut(1)).'</td></tr>';
	
	// Note
	print '<tr><td>'.$langs->trans('Note').'</td>';
	print '<td align="left" colspan="3">'.($object->note).'</td></tr>';
	print '</table><br>';
	
	/*
	* Lines
	*/
	$result = $object->getLinesArray();

	if ($conf->use_javascript_ajax && $object->statut == 0)
	{
		include(DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php');
	}

	print '<table id="tablelines" class="noborder" width="100%">';

	// Show object lines
	$form=new Form($db);
	if (! empty($object->lines)) $object->printObjectLines($action,$mysoc,$soc,$lineid,1);

	print "</table>\n";
	print "</div>\n";

	/*
	 * Boutons actions
	 */
	
	print '<div class="tabsAction">';
	if ($object->statut>0 && $object->statut<3)
	{
		if($object->type != 1 && $resteapayeraffiche <= 0 && $user->rights->facture->creer && !$object->fk_facture)
		{
			print '<a class="butAction" href="'.dol_buildpath('/pos/backend/ticket.php?action=facture&id='.$object->id,1).'">'.$langs->trans('CreateBill').'</a>';
		}
		else{
			print '<span class="butActionRefused" title="'.$langs->trans("DisabledBecauseTicketHasFacture").'">'.$langs->trans('CreateBill').'</span>';
		}
				
		if ($resteapayeraffiche <= 0)
		{
			print '<span class="butActionRefused" title="'.$langs->trans("DisabledBecauseRemainderToPayIsZero").'">'.$langs->trans('DoPayment').'</span>';
			print '<span class="butActionRefused" title="'.$langs->trans("DisabledBecauseRemainderToPayIsZero").'">'.$langs->trans('ClassifyCanceled').'</span>';
		}
		else 
		{
			print '<a class="butAction" href="paiement.php?ticketid='.$object->id.'&amp;action=create">'.$langs->trans('DoPayment').'</a>';
			print '<a class="butAction" href="'.dol_buildpath('/pos/backend/ticket.php?action=abandon&id='.$object->id,1).'">'.$langs->trans('ClassifyCanceled').'</a>';
		}
		$url = '../frontend/tpl/ticket.tpl.php?id='.$object->id;
		print '<a class="butAction" href='.$url.' target="_blank">'.$langs->trans('PrintCopy').'</a>';
		
		print '<a class="butAction" href="'.dol_buildpath('/pos/backend/ticket.php',1).'?id='.$object->id.'&action=mail">'.$langs->trans('MailCopy').'</a>';
	
	}
	if ($object->statut == 3)
	{
		print '<a class="butAction" href="'.dol_buildpath('/pos/backend/ticket.php?action=reopen&id='.$object->id,1).'">'.$langs->trans('ReOpen').'</a>';
	}
	if ($object->is_erasable())
	{
		print '<a class="butAction" href="'.dol_buildpath('/pos/backend/ticket.php?action=delete&id='.$object->id,1).'">'.$langs->trans('Delete').'</a>';
	}
	else
	{
		print '<a class="butActionRefused" href="#" title="'.$langs->trans("DisabledBecauseNotErasable").'">'.$langs->trans('Delete').'</a>';
	}
	print '<a class="butAction" href="'.dol_buildpath('/pos/backend/liste.php',1).'">'.$langs->trans('Back').'</a>';
	print '</div>';
	
	//show associated facture
	print '<table width="100%"><tr><td width="50%" valign="top">';
	print '<a name="builddoc"></a>'; // ancre
	$somethingshown=$object->showLinkedObjectBlock();
	print '</td><td valign="top" width="50%"></td>';
	
	if (GETPOST('action','alpha') == 'delete')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$id, $langs->trans('DeleteTicket'), $langs->trans('ConfirmDeleteTicket',$object->ticketnumber), 'confirm_deleteticket','','',1);
		if ($ret == 'html') print '<br>';
				
	}
	if (GETPOST('action','string') == 'abandon')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$id, $langs->trans('ClassifyCanceled'), $langs->trans('ConfirmAbandonTicket',$object->ticketnumber), 'confirm_abandonticket','','',1);
		if ($ret == 'html') print '<br>';
	}
	if (GETPOST('action','string') == 'facture')
	{
		$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$id, $langs->trans('CreateBill'), $langs->trans('ConfirmCreateBill',$object->ticketnumber), 'confirm_factureticket','','',1);
		if ($ret == 'html') print '<br>';
	
	}
	if( GETPOST('action','string') == 'mail')
	{
		include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php');
		$formmail = new FormMail($db);
	
		$action='send';
		$modelmail='body';
		
		print '<br>';
	
		print_titre($langs->trans($titre));
	
		$formmail->fromtype = 'user';
		$formmail->fromid   = $user->id;
		$formmail->fromname = $conf->global->MAIN_INFO_SOCIETE_NOM;
		$formmail->frommail = $conf->global->MAIN_INFO_SOCIETE_MAIL;
		$formmail->withfrom=0;
		$formmail->withto=empty($_POST["sendto"])?1:GETPOST('sendto');
		$formmail->withtocc=0;
		$formmail->withtoccsocid=0;
		$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
		$formmail->withtocccsocid=0;
		$formmail->withtopic=$conf->global->MAIN_INFO_SOCIETE_NOM.': '.$langs->trans("CopyOfTicket").' '.$object->ticketnumber;
		$formmail->withfile=0;
		$formmail->withbody= POS::fillMailTicketBody($id);
		$formmail->withdeliveryreceipt=0;
		$formmail->withcancel=1;
	
		$formmail->param['action']=$action;
		$formmail->param['models']=$modelmail;
		$formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$id;
		$formmail->show_form();
	
		print '<br>';
	}
	
}

llxFooter();

$db->close();
?>