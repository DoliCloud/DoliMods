<?php
/* Copyright (C) 2012      Mikael Carlavan        <mcarlavan@qis-network.com>
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
 *     	\file       htdocs/public/cmcic/confirm.php
 *		\ingroup    cmcic
 */

define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.

require("../../main.inc.php");
dol_include_once("/cmcic/lib/cmcic.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");
require_once(DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/lib/CMailFile.class.php');

// Security check
if (empty($conf->cmcic->enabled))
    accessforbidden('',1,1,1);

$langs->setDefaultLang();

$langs->load("main");
$langs->load("other");
$langs->load("dict");
$langs->load("bills");
$langs->load("companies");
$langs->load("cmcic@cmcic");

// Get parameters
$params = $conf->global;


// Check module configuration
if (empty($params->CMCIC_TPE_NUMBER))
{
    $err = true;
    dol_syslog('CMCIC: Configuration error : TPE number is not defined');
}

if (empty($params->CMCIC_SOCIETY_ID))
{
    $err = true;
    dol_syslog('CMCIC: Configuration error : society ID is not defined');
}

if (empty($params->CMCIC_KEY))
{
    $err = true;
    dol_syslog('CMCIC: Configuration error : key is not defined');
}

if (empty($params->CMCIC_BANK_SERVER))
{
    $err = true;
    dol_syslog('CMCIC: Configuration error : bank server is not defined');
}

if ($err){
    exit;
}

switch($params->CMCIC_BANK_SERVER){
    case 'cm' :
        $urlServer = ($params->CMCIC_API_TEST) ? $params->CM_URL_SERVER_TEST : $params->CM_URL_SERVER;
    break;

    case 'obc' :
        $urlServer = ($params->CMCIC_API_TEST) ? $params->OBC_URL_SERVER_TEST : $params->OBC_URL_SERVER;
    break;

    case 'cic' :
    default :
        $urlServer = ($params->CMCIC_API_TEST) ? $params->CIC_URL_SERVER_TEST : $params->CIC_URL_SERVER;
    break;
}

$language = strtoupper($langs->getDefaultLang(true));
$cmcicVersion = "3.0";

$vars = getMethode();

$oTpe = new CMCIC_Tpe($cmcicVersion,
                        $params->CMCIC_KEY,
                        $params->CMCIC_TPE_NUMBER,
                        $urlServer,
                        $params->CMCIC_SOCIETY_ID,
                        $params->CMCIC_URL_OK,
                        $params->CMCIC_URL_KO,
                        $language);

$oHmac = new CMCIC_Hmac($oTpe);

// Message Authentication
$fields = sprintf(CMCIC_CGI2_FIELDS, $oTpe->sNumero,
					  $vars["date"],
				          $vars['montant'],
				          $vars['reference'],
				          $vars['texte-libre'],
				          $oTpe->sVersion,
				          $vars['code-retour'],
    					  $vars['cvx'],
    					  $vars['vld'],
    					  $vars['brand'],
    					  $vars['status3ds'],
    					  $vars['numauto'],
    					  $vars['motifrefus'],
    					  $vars['originecb'],
    					  $vars['bincb'],
    					  $vars['hpancb'],
    					  $vars['ipclient'],
    					  $vars['originetr'],
    					  $vars['veres'],
    					  $vars['pares']);

$success = false;
if ($oHmac->computeHmac($fields) == strtolower($vars['MAC'])){
	switch($vars['code-retour']) {
        case "payetest":
		case "paiement":
            $success = true;
		break;

	}

	$receipt = CMCIC_CGI2_MACOK;

}
else
{
	// your code if the HMAC doesn't match
	$receipt = CMCIC_CGI2_MACNOTOK.$fields;
}

// Get invoice data
$referenceDolibarr = $vars['texte-libre'];
$dateTransaction = $vars['date'];
$referenceTransaction = $vars['reference'];
$referenceAutorisation = $vars['numauto'];

$amountTransaction = $vars['montant'];
$bankBin = $vars['bincb'];
$clientBankName = '';

$item = new Facture($db);
$result = $item->fetch('', $referenceDolibarr);

if ($result < 0)
{
    $err = true;
    dol_syslog('CMCIC: Invoice with specified reference does not exist, confirmation payment email has not been sent');
}
else
{
    $result = $item->fetch_thirdparty($item->socid);
    // Set transaction reference
    $item->updateObjectField($item->table_element, $item->id, 'ref_int', $referenceTransaction);
}

$substit = array(
    '__INVREF__' => $referenceDolibarr,
    '__SOCNAM__' => $params->MAIN_INFO_SOCIETE_NOM,
    '__SOCMAI__' => $params->MAIN_INFO_SOCIETE_MAIL,
    '__CLINAM__' => $item->client->name,
    '__AMOINV__' => $amountTransaction
);

// Update DB
if ($success){

    $db->begin();
    $amount = str_replace($params->MAIN_MONNAIE, '', $amountTransaction);//Remove currency
    // Creation of payment line
    $payment = new Paiement($db);
    $payment->datepaye     = dol_now();
    $payment->amounts      = array($item->id => price2num($amount));
    $payment->paiementid   = dol_getIdFromCode($db, 'CB', 'c_paiement');
    $payment->num_paiement = $referenceAutorisation;
    $payment->note         = '';

    $paymentId = $payment->create($user, $params->CMCIC_UPDATE_INVOICE_STATUT);

    if ($paymentId < 0)
    {
        dol_syslog('CMCIC: Payment has not been created in the database');
    }
    else
    {
        if (!empty($params->CMCIC_BANK_ACCOUNT_ID))
        {
            $payment->addPaymentToBank($user, 'payment', '(CustomerInvoicePayment)', $params->CMCIC_BANK_ACCOUNT_ID, $item->client->name, $clientBankName);
        }
    }

    $db->commit();

    $subject = $langs->transnoentities('CMCIC_SUCCESS_PAYMENT_EMAIL_SUBJECT_TEXT');
    $message = $langs->transnoentities('CMCIC_SUCCESS_PAYMENT_EMAIL_BODY_TEXT');

    $subject = make_substitutions($subject, $substit);
    $message = make_substitutions($message, $substit);

}else{

    $grounds = urldecode($vars['motifrefus']);

    switch(strtolower($grounds)){
        case 'Appel Phonie' :
            $message = $langs->transnoentities('CMCIC_ERROR_PAYMENT_CALL_EMAIL_TEXT');
        break;

        case 'Filtrage' :
            $filters = explode('-', $vars['filtragecause']);
            $message = $langs->transnoentities('CMCIC_ERROR_PAYMENT_FILTERS_EMAIL_TEXT');
            for ($i=0; $i<sizeof($filters); $i++){
                $filterMessage = '';
                switch($filters[$i]){
                    case '1' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_IPA_FILTER_EMAIL_TEXT'); break;
                    case '2' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_CAN_FILTER_EMAIL_TEXT'); break;
                    case '3' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_BIN_FILTER_EMAIL_TEXT'); break;
                    case '4' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_CCO_FILTER_EMAIL_TEXT'); break;
                    case '5' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_CIP_FILTER_EMAIL_TEXT'); break;
                    case '6' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_COH_FILTER_EMAIL_TEXT'); break;
                    case '7' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_EMA_FILTER_EMAIL_TEXT'); break;
                    case '8' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_AMO_FILTER_EMAIL_TEXT'); break;
                    case '9' : $filterMessage = $langs->transnoentities('CMCIC_ERROR_PAYMENT_TRL_FILTER_EMAIL_TEXT'); break;
                    default : $filterMessage = ''; break;

                }
                $message .= $filterMessage.'\n';
            }
        break;

        case 'Interdit' :
            $message = $langs->transnoentities('CMCIC_ERROR_PAYMENT_FORBIDDEN_EMAIL_TEXT');
        break;

        case 'Refus' :
        default :
            $message = $langs->transnoentities('CMCIC_ERROR_PAYMENT_UNAUTHORIZED_EMAIL_TEXT');
        break;
    }
    $message .= $langs->transnoentities('CMCIC_ERROR_EMAIL_BODY_TEXT');

    $subject = $langs->transnoentities('CMCIC_ERROR_PAYMENT_EMAIL_SUBJECT_TEXT');

    $subject = make_substitutions($subject, $substit);
    $message = make_substitutions($message, $substit);
}

if (!$err)
{
    //Get data for email
    $sendto = $item->client->email;
    $from = $params->MAIN_INFO_SOCIETE_MAIL;

    $message = str_replace('\n',"<br />", $message);

    // Send email
    $deliveryreceipt = 0; //Do not need receipt for confirmation email
    $addr_cc = ($params->CMCIC_CC_EMAIL ? $params->MAIN_INFO_SOCIETE_MAIL: "");

    if (!empty($params->CMCIC_CC_EMAILS)){
        $addr_cc.= (empty($addr_cc) ? $params->CMCIC_CC_EMAILS : ','.$params->CMCIC_CC_EMAILS);
    }

    $mail = new CMailFile($subject, $sendto, $from, $message, array(), array(), array(), $addr_cc, "", $deliveryreceipt, 1);
    $result = $mail->error;
    if (!$result)
    {
        $result = $mail->sendfile();
        if ($result){
            dol_syslog('CMCIC: Confirmation payment email has been correctly sent');
        }else{
            dol_syslog('CMCIC: Error sending confirmation payment email');
        }
    }
    else
    {
        dol_syslog('CMCIC: Error in creating confirmation payment email');
    }
}


printf (CMCIC_CGI2_RECEIPT, $receipt);

$db->close();
?>