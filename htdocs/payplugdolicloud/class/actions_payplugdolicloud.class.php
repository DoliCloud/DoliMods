<?php
/* Copyright (C) 2023		Laurent Destailleur			<eldy@users.sourceforge.net>
 * Copyright (C) 2024		SuperAdmin					<daoud.mouhamed@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    payplugdolicloud/class/actions_payplugdolicloud.class.php
 * \ingroup payplugdolicloud
 * \brief   Example hook overload.
 *
 * TODO: Write detailed description here.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsPayplugDolicloud
 */
class ActionsPayplugDolicloud extends CommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var string[] Errors
	 */
	public $errors = array();


	/**
	 * @var mixed[] Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var ?string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority;


	/**
	 * Constructor
	 *
	 *  @param	DoliDB	$db      Database handler
	 */
	public function __construct($db)
	{
		global $langs;
		$this->db = $db;
		$langs->load("payplugdolicloud@payplugdolicloud");
	}


	/**
	 * Execute action
	 *
	 * @param	array<string,mixed>	$parameters	Array of parameters
	 * @param	CommonObject		$object		The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string				$action		'add', 'update', 'view'
	 * @return	int								Return integer <0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *											>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overload the doActions function : replacing the parent's function with the one below
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	?string				$action			Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		// @phan-suppress-next-line PhanPluginEmptyStatementIf
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {	    // do something only for the context 'somecontext1' or 'somecontext2'
			// Do what you want here...
			// You can for example load and use call global vars like $fieldstosearchall to overwrite them, or update the database depending on $action and GETPOST values.

			if (!$error) {
				$this->results = array('myreturn' => 999);
				$this->resprints = 'A text to show';
				return 0; // or return 1 to replace standard code
			} else {
				$this->errors[] = 'Error message';
				return -1;
			}
		}

		return 0;
	}


	/**
	 * Overload the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	?string				$action			Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			// @phan-suppress-next-line PhanPluginEmptyStatementForeachLoop
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}

			if (!$error) {
				$this->results = array('myreturn' => 999);
				$this->resprints = 'A text to show';
				return 0; // or return 1 to replace standard code
			} else {
				$this->errors[] = 'Error message';
				return -1;
			}
		}

		return 0;
	}


	/**
	 * Overload the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param	array<string,mixed>	$parameters     Hook metadata (context, etc...)
	 * @param	CommonObject		$object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	?string	$action						Current action (if set). Generally create or edit or null
	 * @param	HookManager	$hookmanager			Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("PayplugDolicloudMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action before PDF (document) creation
	 *
	 * @param	array<string,mixed>	$parameters	Array of parameters
	 * @param	CommonObject		$object		Object output on PDF
	 * @param	string				$action		'add', 'update', 'view'
	 * @return	int								Return integer <0 if KO,
	 *											=0 if OK but we want to process standard actions too,
	 *											>0 if OK and we want to replace standard actions.
	 */
	public function beforePDFCreation($parameters, &$object, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0;
		$deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		// @phan-suppress-next-line PhanPluginEmptyStatementIf
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}

	/**
	 * Execute action after PDF (document) creation
	 *
	 * @param	array<string,mixed>	$parameters	Array of parameters
	 * @param	CommonDocGenerator	$pdfhandler	PDF builder handler
	 * @param	string				$action		'add', 'update', 'view'
	 * @return	int								Return integer <0 if KO,
	 * 											=0 if OK but we want to process standard actions too,
	 *											>0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0;
		$deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		// @phan-suppress-next-line PhanPluginEmptyStatementIf
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overload the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	?string				$action 		Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager    Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $langs;

		$langs->load("payplugdolicloud@payplugdolicloud");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'payplugdolicloud') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("PayplugDolicloud");
			$this->results['picto'] = 'payplugdolicloud@payplugdolicloud';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		$arrayoftypes = array();
		//$arrayoftypes['payplugdolicloud_myobject'] = array('label' => 'MyObject', 'picto'=>'myobject@payplugdolicloud', 'ObjectClassName' => 'MyObject', 'enabled' => isModEnabled('payplugdolicloud'), 'ClassPath' => "/payplugdolicloud/class/myobject.class.php", 'langs'=>'payplugdolicloud@payplugdolicloud')

		$this->results['arrayoftype'] = $arrayoftypes;

		return 0;
	}



	/**
	 * Overload the restrictedArea function : check permission on an object
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	string				$action			Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int									Return integer <0 if KO,
	 *												=0 if OK but we want to process standard actions too,
	 *												>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->hasRight('payplugdolicloud', 'myobject', 'read')) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Execute action completeTabsHead
	 *
	 * @param	array<string,mixed>	$parameters		Array of parameters
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string				$action			'add', 'update', 'view'
	 * @param	Hookmanager			$hookmanager	Hookmanager
	 * @return	int									Return integer <0 if KO,
	 *												=0 if OK but we want to process standard actions too,
	 *												>0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// used to make some tabs removed
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('payplugdolicloud@payplugdolicloud');
			// used when we want to add some tabs
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/payplugdolicloud/payplugdolicloud_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('PayplugDolicloudTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'payplugdolicloudemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {  // @phpstan-ignore-line
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// From V14 onwards, $parameters['head'] is modifiable by reference
				return 0;
			}
		} else {
			// Bad value for $parameters['mode']
			return -1;
		}
	}

	/* Add here any other hooked methods... */
	/**
	 * Overloading the doAddButton function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doAddButton($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$resprints = "";
		$error = "";

		if (array_key_exists("paymentmethod", $parameters) && (empty($parameters["paymentmethod"]) || $parameters["paymentmethod"] == 'payplug') && isModEnabled('payplugdolicloud')) {
			$resprints .= '<div class="button buttonpayment" id="div_dopayment_payplug"><span class="fa fa-credit-card"></span> <input class="" type="submit" id="dopayment_payplug" name="dopayment_payplug" value="'.$langs->trans("PayplugDoPayment").'">';
			$resprints .= '<input type="hidden" name="noidempotency" value="'.GETPOST('noidempotency', 'int').'">';
			$resprints .= '<input type="hidden" name="s" value="'.(GETPOST('s', 'alpha') ? GETPOST('s', 'alpha') : GETPOST('source', 'alpha')).'">';
			$resprints .= '<input type="hidden" name="ref" value="'.GETPOST('ref').'">';
			$resprints .= '<br>';			
			$resprints .= '<span class="buttonpaymentsmall">'.$langs->trans("CreditOrDebitCard").'</span>';
			$resprints .= '</div>';
			$resprints .= '<script>
							$( document ).ready(function() {
								$("#div_dopayment_payplug").click(function(){
									$("#dopayment_payplug").click();
								});
								$("#dopayment_payplug").click(function(e){
									$("#div_dopayment_payplug").css( \'cursor\', \'wait\' );
									e.stopPropagation();
									return true;
								});
							});
						</script>
			';
		}

		if (!$error) {
			$this->resprints = $resprints;
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = $error;
			return -1;
		}
	}

	/**
	 * Overloading the getValidPayment function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function getValidPayment($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;

		$error = 0; // Error counter
		$error = "";

		if (array_key_exists("paymentmethod", $parameters) && (empty($parameters["paymentmethod"]) || $parameters["paymentmethod"] == 'payplug') && isModEnabled('payplugdolicloud')) {
			$langs->load("payplugdolicloud");
			$validpaymentmethod['payplug'] = 'valid';
		}

		if (!$error) {
			$this->results["validpaymentmethod"] = $validpaymentmethod;
			return 0;
		} else {
			$this->errors[] = $error;
			return -1;
		}
	}

	/**
	 * Overloading the doPayment function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doPayment($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs,$db;

		require_once DOL_DOCUMENT_ROOT."/core/lib/geturl.lib.php";
		include_once DOL_DOCUMENT_ROOT.'/core/lib/security.lib.php';
		dol_include_once('payplugdolicloud/lib/payplugdolicloud.lib.php');

		$resprints = "";

		$error = 0; // Error counter
		$errors = array();

		$urlwithroot = DOL_MAIN_URL_ROOT; // This is to use same domain name than current. For Paypal payment, we can use internal URL like localhost.

		// Complete urls for post treatment
		$ref = $REF = GETPOST('ref', 'alpha');
		$TAG = GETPOST("tag", 'alpha');
		$FULLTAG = GETPOST("fulltag", 'alpha'); // fulltag is tag with more informations
		$SECUREKEY = GETPOST("securekey"); // Secure key
		$source = GETPOST('s', 'alpha') ? GETPOST('s', 'alpha') : GETPOST('source', 'alpha');
		$object = null;
		$amount = price2num(GETPOST("amount", 'alpha'));

		if ($action == "returnDoPaymentPayplugDolicloud") {
			dol_syslog("Data after redirect from payplug payment page with session FinalPaymentAmt = ".$_SESSION["FinalPaymentAmt"]." currencycodeType = ".$_SESSION["currencyCodeType"], LOG_DEBUG);

			$payplugrurlapi = "api.payplug.com";
			if (getDolGlobalInt("PAYPLUG_DOLICLOUD_LIVE")) {
				$secretapikey = getDolGlobalString("PAYPLUG_DOLICLOUD_PROD_SECRET_API_KEY");
			} else {
				$secretapikey = getDolGlobalString("PAYPLUG_DOLICLOUD_TEST_SECRET_API_KEY");
			}

			$headers = array();
			$headers[] = "accept: application/json";
			$headers[] = "Authorization: Bearer ".$secretapikey;
			$headers[] = "Content-Type: application/json";

			$jsontosenddata = '{}';
			// Verify if payment is done
			$urlforcheckout = "https://".urlencode($payplugrurlapi)."/v1/payments/".$_SESSION["PAYPLUG_DOLICLOUD_PAYMENT_ID"];
			$ret1 = getURLContent($urlforcheckout, 'GET', $jsontosenddata, 1, $headers);

			$result1 = $ret1["content"];
			$json1 = json_decode($result1);

			$urlredirect = $urlwithroot.'/public/payment/';
			if ($ret1["http_code"] == 200 && empty($json->failure)) {
				$urlredirect .= "paymentok.php?fulltag=".urlencode($FULLTAG);
				header("Location: ".$urlredirect);
				exit;
			} else {
				$_SESSION['errormessage'] = $json->failure->message;
				$urlredirect .= "paymentko.php?fulltag=".urlencode($FULLTAG);
				header("Location: ".$urlredirect);
				exit;
			}
		}

		if (in_array($parameters['context'],array('newpayment')) && empty($parameters['paymentmethod'])) {
			$amount = price2num(payplugGetDataFromObjects($source, $ref));
			if (!GETPOST("currency", 'alpha')) {
				$currency = $conf->currency;
			} else {
				$currency = GETPOST("currency", 'aZ09');
			}
			$_SESSION["FinalPaymentAmt"] = $amount;
			$_SESSION["currencyCodeType"] = $currency;

		} elseif (in_array($parameters['paymentmethod'], array('payplug')) && $parameters['validpaymentmethod']["payplug"] == "valid") {
			$urlback = $urlwithroot.'/public/payment/newpayment.php?';

			if (!preg_match('/^https:/i', $urlback)) {
				$langs->load("errors");
				$error++;
				$errors[] = $langs->trans("WarningAvailableOnlyForHTTPSServers");
			}

			$payplugrurlapi = "api.payplug.com";
			if (getDolGlobalInt("PAYPLUG_DOLICLOUD_LIVE")) {
				$secretapikey = getDolGlobalString("PAYPLUG_DOLICLOUD_PROD_SECRET_API_KEY");
			} else {
				$secretapikey = getDolGlobalString("PAYPLUG_DOLICLOUD_TEST_SECRET_API_KEY");
			}

			$paymentmethod = $parameters['paymentmethod'];

			if ($paymentmethod && !preg_match('/'.preg_quote('PM='.$paymentmethod, '/').'/', $FULLTAG)) {
				$FULLTAG .= ($FULLTAG ? '.' : '').'PM='.$paymentmethod;
			}
			if (!empty($suffix)) {
				$urlback .= 'suffix='.urlencode($suffix).'&';
			}
			if ($source) {
				$urlback .= 's='.urlencode($source).'&';
			}
			if (!empty($REF)) {
				$urlback .= 'ref='.urlencode($REF).'&';
			}
			if (!empty($TAG)) {
				$urlback .= 'tag='.urlencode($TAG).'&';
			}
			if (!empty($FULLTAG)) {
				$urlback .= 'fulltag='.urlencode($FULLTAG).'&';
			}
			if (!empty($SECUREKEY)) {
				$urlback .= 'securekey='.urlencode($SECUREKEY).'&';
			}
			if (!empty($entity)) {
				$urlback .= 'e='.urlencode($entity).'&';
			}
			if (!empty($getpostlang)) {
				$urlback .= 'lang='.urlencode($getpostlang).'&';
			}
			$cancel_url = $urlback;
			$urlback .= 'action=returnDoPaymentPayplugDolicloud';

			if (!$error) {
				$payerarray = array();
				PayplugGetDataFromObjects($source, $ref, 'payer', $payerarray);

				$fulltag = $FULLTAG;
				$FinalPaymentAmt = $_SESSION["FinalPaymentAmt"];
				$currencyCodeType = $_SESSION["currencyCodeType"];
				$amounttotest = $amount;
				if (!$error) {
					//Permit to format the amount string to call Payplug API
					$posdot = strpos($amount, '.');
					if ( $posdot === false) {
						$amount .= '00';
					} else {
						$amounttab = explode('.', $amount);
						if (strlen($amounttab[1]) == 1) {
							$amounttab[1] .= "0";
						} else if (strlen($amounttab[1]) > 2) {
							$amounttab[1] = substr($amounttab[1], 0, 2);
						}
						if (isset($amounttab[0])) {
							$val = intval($amounttab[0]);
							if ($val  == 0) {
								$amount = $amounttab[1];
							} else {
								$amount = strval($val) .$amounttab[1];
							}
						} else {
							$amount = $amounttab[1];
						}
					}

					if ($FinalPaymentAmt == $amounttotest) {
						$headers = array();
						$headers[] = "accept: application/json";
						$headers[] = "Authorization: Bearer ".$secretapikey;
						$headers[] = "Content-Type: application/json";

						$jsontosenddata = '{
							"amount": '.$amount.',
							"currency": "'.$currencyCodeType.'",
							"hosted_payment": {
								"return_url": "'.$urlback.'",
								"cancel_url": "'.$cancel_url.'"
							},
							"customer": {
								"first_name": "'.$payerarray["firstName"].'",
								"last_name": "'.$payerarray['lastName'].'",
								"email": "'.$payerarray['email'].'"
							}
						}';

						$urlforcheckout = "https://".urlencode($payplugrurlapi)."/v1/payments";

						dol_syslog("Send Post to url=".$urlforcheckout." with session FinalPaymentAmt = ".$FinalPaymentAmt." currencyCodeType = ".$currencyCodeType, LOG_DEBUG);

						$ret1 = getURLContent($urlforcheckout, 'POSTALREADYFORMATED', $jsontosenddata, 1, $headers);
						if ($ret1["http_code"] == 201) {
							$result1 = $ret1["content"];
							$json1 = json_decode($result1);
							$_SESSION["PAYPLUG_DOLICLOUD_PAYMENT_ID"] = urlencode($json1->id);
							$urlforredirect = $json1->hosted_payment->payment_url;

							// Gestion redirection
							dol_syslog("Send redirect to ".$urlforredirect);

							header("Location: ".$urlforredirect);
							exit;
						} else {
							$arrayofmessage = array();
							if (!empty($ret1['content'])) {
								$arrayofmessage = json_decode($ret1['content'], true);
							}
							if (!empty($arrayofmessage['message'])) {
								$errors[] = $arrayofmessage['message'];

								if (!empty($arrayofmessage['details']) && is_array($arrayofmessage['details'])) {
									foreach($arrayofmessage['details'] as $tmpkey => $tmpmessage) {
										if (!empty($tmpmessage)) {
											$errors[] = $langs->trans("Error"). ' : ' .$tmpmessage;
										} else {
											$errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret2["http_code"];
										}
									}
								}
							} else {
								$errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret2["http_code"];
							}
							$error++;
							$action = '';
						}
					} else {
						$error++;
						$errors[] = $langs->trans("ErrorValueFinalPaymentDiffers", $FinalPaymentAmt, $amounttotest);
					}
				}
			}
		}

		if (!$error) {
			$this->resprints = $resprints;
			return 1; // or return 1 to replace standard code
		} else {
			$this->errors = $errors;
			return -1;
		}
	}

	/**
	 * Overloading the isPaymentOK function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function isPaymentOK($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs,$db;

		$error = 0; // Error counter
		$ispaymentok = true;

		if (in_array($parameters['paymentmethod'], array('payplug'))){
			$code = GETPOST("code");
			if ($code == "refused") {
				$ispaymentok = false;
				$error ++;
			}
		}

		if (!$error) {
			$this->results["ispaymentok"] = $ispaymentok;
			return 1;
		} else {
			$this->errors[] = $langs->trans("PaymentRefused");
			return -1;
		}
	}

	/**
	 * Overloading the getBankAccountPaymentMethod function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function getBankAccountPaymentMethod($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;

		$error = 0; // Error counter

		$bankaccountid = 0;

		if (in_array($parameters['paymentmethod'], array('payplug'))){
			$bankaccountid = getDolGlobalInt('PAYPLUG_DOLICLOUD_BANK_ACCOUNT_FOR_PAYMENTS');
			if ($bankaccountid == 0) {
				$error++;
			}
		}

		if (!$error && $bankaccountid > 0) {
			$this->results["bankaccountid"] = $bankaccountid;
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Overloading the getBankAccountPaymentMethod function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doShowOnlinePaymentUrl($parameters, &$object, &$action, $hookmanager){
		if (isModEnabled('payplugdolicloud')) {
			$this->results['showonlinepaymenturl'] = isModEnabled('payplugdolicloud');
		}else {
			return -1;
		}
		return 1;
	}

}
