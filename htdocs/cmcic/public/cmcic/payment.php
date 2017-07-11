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
 *     	\file       htdocs/public/cmcic/payment.php
 *		\ingroup    cmcic
 *		\brief      File to offer a payment form for an invoice
 */

define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/security.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");
dol_include_once("/cmcic/lib/cmcic.inc.php");

// Security check
if (empty($conf->cmcic->enabled))
    accessforbidden('',1,1,1);

$langs->load("main");
$langs->load("other");
$langs->load("dict");
$langs->load("bills");
$langs->load("companies");
$langs->load("errors");

$langs->load("cmcic@cmcic");



$cmcicVersion = "3.0";

$refDolibarr = GETPOST("ref", 'alpha');
$secToken    = GETPOST("token", 'alpha');

// Get parameters
$params = $conf->global;
$err = false;
$errMsg = '';

// Get societe info
$societyName = $mysoc->name;
$creditorName = $societyName;

// Define logo and logosmall
$urlLogo = '';
if (!empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small))
{
	$urlLogo = DOL_URL_ROOT.'/viewimage.php?modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_small);
}
elseif (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo))
{
	$urlLogo = DOL_URL_ROOT.'/viewimage.php?modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
}


// Check module configuration
if (empty($params->CMCIC_TPE_NUMBER))
{
    $err = true;
    $errMsg = $langs->trans('CMCIC_TPE_NUMBER_UNDEFINED');
    dol_syslog('CMCIC: Configuration error : TPE number is not defined');
}

if (empty($params->CMCIC_SOCIETY_ID))
{
    $err = true;
    $errMsg = $langs->trans('CMCIC_SOCIETY_ID_UNDEFINED');
    dol_syslog('CMCIC: Configuration error : society ID is not defined');
}

if (empty($params->CMCIC_KEY))
{
    $err = true;
    $errMsg = $langs->trans('CMCIC_KEY_UNDEFINED');
    dol_syslog('CMCIC: Configuration error : key is not defined');
}

if (empty($params->CMCIC_BANK_SERVER))
{
    $err = true;
    $errMsg = $langs->trans('CMCIC_BANK_UNDEFINED');
    dol_syslog('CMCIC: Configuration error : bank server is not defined');
}


if (empty($refDolibarr))
{
    $err = true;
    $errMsg = $langs->trans('CMCIC_REF_PARAM_UNDEFINED');
    dol_syslog('CMCIC: Invoice reference has not been defined');
}


// Prepare form
$language = strtoupper($langs->getDefaultLang(true));
//$dateTransaction = date("d/m/Y:H:i:s");//dol_print_date(dol_now(), "%d%m%Y-%H%M%S")
$dateTransaction = dol_print_date(dol_now(), "%d/%m/%Y:%H:%M:%S");
$baseURL = preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','', $dolibarr_main_url_root);

switch($params->CMCIC_BANK_SERVER){
    case 'cm' :
        $bankName = 'Crédit Mutuel';
        $urlServer = ($params->CMCIC_API_TEST) ? $params->CM_URL_SERVER_TEST : $params->CM_URL_SERVER;
    break;

    case 'obc' :
        $bankName = 'Neuflize OBC';
        $urlServer = ($params->CMCIC_API_TEST) ? $params->OBC_URL_SERVER_TEST : $params->OBC_URL_SERVER;
    break;

    case 'cic' :
    default :
        $bankName = 'CIC';
        $urlServer = ($params->CMCIC_API_TEST) ? $params->CIC_URL_SERVER_TEST : $params->CIC_URL_SERVER;
    break;
}

// Check token
$trueToken = dol_hash($params->CMCIC_SECURITY_TOKEN.$refDolibarr, 2);

if ($trueToken != $secToken)
{
	$err = true;
    $errMsg = $langs->trans('CMCIC_WRONG_TOKEN');
    dol_syslog('CMCIC: Wrong token');
}

$refTransaction = dol_print_date(dol_now(), "%d%m%y%H%M%S");

$item = new Facture($db);
$result = $item->fetch('', $refDolibarr);

$alreadyPaid = 0;
$amountTransaction = 0;
$freeTag = '';
$needPayment = false;

if ($result < 0)
{
	$err = true;
    $errMsg = $langs->trans('CMCIC_NO_PAYMENT_INVOICE');
    dol_syslog('CMCIC: Invoice reference does not exist');
}
else
{
	$result = $item->fetch_thirdparty($item->socid);

    $alreadyPaid = $item->getSommePaiement();
    $creditnotes = $item->getSumCreditNotesUsed();
    $deposits = $item->getSumDepositsUsed();
    $totalInvoice = $item->total_ttc;

    $alreadyPaid = empty($alreadyPaid) ? 0 : $alreadyPaid;
    $creditnotes = empty($creditnotes) ? 0 : $creditnotes;
    $deposits = empty($deposits) ? 0 : $deposits;

    $totalInvoice = empty($totalInvoice) ? 0 : $totalInvoice;

    $amountTransaction =  $totalInvoice - ($alreadyPaid + $creditnotes + $deposits);
    $freeTag = $item->ref;
    $needPayment = ($item->statut == 1) ? true : false;

    // Do nothing if payment is already completed
    if ($amountTransaction == 0 || !$needPayment){
        $err = true;
        $errMsg = $langs->trans('CMCIC_PAYMENT_ALREADY_DONE');
        dol_syslog('CMCIC: Payment already completed, form will not be displayed');
    }
}



if (!$err)
{
    $customerEmail = $item->client->email;
    $customerName = empty($item->client->nom)?$item->client->name:$item->client->nom;

    //Clean data
    $refTransaction = dol_string_unaccent($refTransaction);
    $freeTag =  dol_string_unaccent($freeTag);
    $amountTransactionNum = price2num($amountTransaction);
    $currency = $params->MAIN_MONNAIE;
    $amountCurrency = $amountTransactionNum .$currency;

    $oTpe = new CMCIC_Tpe($cmcicVersion,
                            $params->CMCIC_KEY,
                            $params->CMCIC_TPE_NUMBER,
                            $urlServer,
                            $params->CMCIC_SOCIETY_ID,
                            $params->CMCIC_URL_OK,
                            $params->CMCIC_URL_KO,
                            $language);

    // Data to certify
    $fields = sprintf(CMCIC_CGI1_FIELDS,     $oTpe->sNumero,
                                                 $dateTransaction,
                                                  $amountTransactionNum,
                                                  $params->MAIN_MONNAIE,
                                                  $refTransaction,
                                                  $freeTag,
                                                  $oTpe->sVersion,
                                                  $oTpe->sLangue,
                                                  $oTpe->sCodeSociete,
                                                  $customerEmail,
                                                  "", "", "", "", "", "", "", "", "", "");

    // MAC computation
    $oHmac = new CMCIC_Hmac($oTpe);
    $macToken = $oHmac->computeHmac($fields);
    /*
     * View
     */
    $substit = array(
        '__INVREF__' => $refDolibarr,
        '__SOCNAM__' => $params->MAIN_INFO_SOCIETE_NOM,
        '__SOCMAI__' => $params->MAIN_INFO_SOCIETE_MAIL,
        '__CLINAM__' => $item->client->name,
        '__AMOINV__' => price2num($amountTransaction)
    );

     $welcomeTitle = make_substitutions($langs->transnoentities('CMCIC_PAYMENT_FORM_WELCOME_TITLE'), $substit);
     $welcomeText = make_substitutions($langs->transnoentities('CMCIC_PAYMENT_FORM_WELCOME_TEXT'), $substit);

     $descText = make_substitutions($langs->transnoentities('CMCIC_PAYMENT_FORM_DESC_TEXT'), $substit);

    require_once('tpl/payment_form.php');
}else{

    /*
     * View
     */

    $substit = array(
        '__INVREF__' => $refDolibarr,
        '__SOCNAM__' => $params->MAIN_INFO_SOCIETE_NOM,
        '__SOCMAI__' => $params->MAIN_INFO_SOCIETE_MAIL,
        '__CLINAM__' => $item->client->name,
        '__AMOINV__' => price2num($amountTransaction)
    );
    $msg = make_substitutions($errMsg, $substit);
    require_once('tpl/message.php');
}

$db->close();
