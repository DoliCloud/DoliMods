<?php
/* Copyright (C) 2024 Alice Adminson <myemail@mycompany.com>
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
 * \file    helloasso/class/actions_helloasso.class.php
 * \ingroup helloasso
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsHelloAsso
 */
class ActionsHelloAsso extends CommonHookActions
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
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $langs;
		$this->db = $db;
		$langs->load("helloasso@helloasso");
	}


	/**
	 * Execute action
	 *
	 * @param	array			$parameters		Array of parameters
	 * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string			$action      	'add', 'update', 'view'
	 * @return	int         					Return integer <0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *                            				>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}
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


	/**
	 * Overloading the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}
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

	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("HelloAssoMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$object		   	Object output on PDF
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	Return integer <0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
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
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}

	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$pdfhandler     PDF builder handler
	 * @param   string	$action         'add', 'update', 'view'
	 * @return  int 		            Return integer <0 if KO,
	 *                                  =0 if OK but we want to process standard actions too,
	 *                                  >0 if OK and we want to replace standard actions.
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
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overloading the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$langs->load("helloasso@helloasso");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'helloasso') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("HelloAsso");
			$this->results['picto'] = 'helloasso@helloasso';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		return 1;
	}



	/**
	 * Overloading the restrictedArea function : check permission on an object
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int 		      			  	Return integer <0 if KO,
	 *                          				=0 if OK but we want to process standard actions too,
	 *  	                            		>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->hasRight('helloasso', 'myobject', 'read')) {
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
	 * @param   array           $parameters     Array of parameters
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         'add', 'update', 'view'
	 * @param   Hookmanager     $hookmanager    hookmanager
	 * @return  int                             Return integer <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
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
			$langs->load('helloasso@helloasso');
			// used when we want to add some tabs
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/helloasso/helloasso_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('HelloAssoTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'helloassoemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// en V14 et + $parameters['head'] est modifiable par référence
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

		if (array_key_exists("paymentmethod", $parameters) && (empty($parameters["paymentmethod"]) || $parameters["paymentmethod"] == 'helloasso') && isModEnabled('helloasso')) {
			$resprints .= '<div class="button buttonpayment" id="div_dopayment_helloasso"><span class="fa fa-credit-card"></span> <input class="" type="submit" id="dopayment_helloasso" name="dopayment_helloasso" value="'.$langs->trans("HelloAssoDoPayment").'">';
			$resprints .= '<input type="hidden" name="noidempotency" value="'.GETPOST('noidempotency', 'int').'">';
			$resprints .= '<input type="hidden" name="s" value="'.(GETPOST('s', 'alpha') ? GETPOST('s', 'alpha') : GETPOST('source', 'alpha')).'">';
			$resprints .= '<input type="hidden" name="ref" value="'.GETPOST('ref').'">';
			$resprints .= '<br>';
			$resprints .= '<span class="buttonpaymentsmall">'.$langs->trans("CreditOrDebitCard").'</span>';
			$resprints .= '</div>';
			$resprints .= '<script>
							$( document ).ready(function() {
								$("#div_dopayment_helloasso").click(function(){
									$("#dopayment_helloasso").click();
								});
								$("#dopayment_helloasso").click(function(e){
									$("#div_dopayment_helloasso").css( \'cursor\', \'wait\' );
									e.stopPropagation();
									return true;
								});
							});
						</script>
			';
		}

		if (!$error) {
			$this->resprints = $resprints;
			return 1; // or return 1 to replace standard code
		} else {
			$this->errors[] = $error;
			return -1;
		}
	}

	/* Add here any other hooked methods... */
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
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$resprints = "";
		$error = "";

		if (array_key_exists("paymentmethod", $parameters) && (empty($parameters["paymentmethod"]) || $parameters["paymentmethod"] == 'helloasso') && isModEnabled('helloasso')) {
			$langs->load("helloasso");
			$validpaymentmethod['helloasso'] = 'valid';
		}

		if (!$error) {
			$this->results["validpaymentmethod"] = $validpaymentmethod;
			return 1; // or return 1 to replace standard code
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

		if ($action == "returnDoPaymentHelloAsso") {
			$urlredirect = $urlwithroot.'/public/payment/';
			$typereturn = GETPOST("typereturn");
			if ($typereturn == "error") {
				$urlredirect .= "paymentko.php?fulltag=".urlencode($FULLTAG);
				header("Location: ".$urlredirect);
				exit;
			} elseif ($typereturn == "return") {
				$code = GETPOST("code");
				$urlredirect .= "paymentok.php?fulltag=".urlencode($FULLTAG).'&code='.urlencode($code);
				header("Location: ".$urlredirect);
				exit;
			}
		}

		if (in_array($parameters['context'],array('newpayment')) && empty($parameters['paymentmethod'])) {
			require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
			require_once DOL_DOCUMENT_ROOT.'/don/class/don.class.php';
			require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
			require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent_type.class.php';
			require_once DOL_DOCUMENT_ROOT.'/adherents/class/subscription.class.php';
			require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
			require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';

			$invoice = new Facture($db);
			$don = new Don($db);
			$member = new Adherent($db);
			$adht = new AdherentType($db);
			$contract = new Contrat($db);
			$contractline = new ContratLigne($db);
			$order = new Commande($db);
			if ($source == "membersubscription") {
				$source = 'member';
			}
			switch ($source) {
				case 'donation':
					$result = $don->fetch($ref);
					if ($result <= 0) {
						$errors[] = $don->error;
						$error++;
					} else {
						if (GETPOST("amount", 'alpha')) {
							$amount = GETPOST("amount", 'alpha');
						} else {
							$amount = $don->getRemainToPay();
						}
					}
					break;

				case 'member':
					$result = $member->fetch('', $ref);
					if ($result <= 0) {
						$errors[] = $member->error;
						$error++;
					} else {
						$member->fetch_thirdparty();
						$subscription = new Subscription($db);
						$adht->fetch($member->typeid);
						$amount = $subscription->total_ttc;
						if (GETPOST("amount", 'alpha')) {
							$amount = GETPOST("amount", 'alpha');
						}
						if (empty($amount)) {
							$amount = $adht->amount;
						}

						if (!empty($member->last_subscription_amount) && !GETPOSTISSET('newamount') && is_numeric($amount)){
							$amount = max($member->last_subscription_amount, $amount);
						}
						$amount = max(0, getDolGlobalString('MEMBER_MIN_AMOUNT'), $amount);
					}
					break;

				case 'contractline':
					$result = $contractline->fetch('', $ref);
					if ($result <= 0) {
						$errors[] = $contractline->error;
						$error++;
					} else {
						$amount = $contractline->total_ttc;
						if ($contractline->fk_product && getDolGlobalString('PAYMENT_USE_NEW_PRICE_FOR_CONTRACTLINES')) {
							$product = new Product($db);
							$result = $product->fetch($contractline->fk_product);

							// We define price for product (TODO Put this in a method in product class)
							if (getDolGlobalString('PRODUIT_MULTIPRICES')) {
								$pu_ttc = $product->multiprices_ttc[$contract->thirdparty->price_level];
							} else {
								$pu_ttc = $product->price_ttc;
							}

							$amount = $pu_ttc;
						}
					}
					break;

				case 'invoice':
					$result = $invoice->fetch('', $ref);
					if ($result <= 0) {
						$errors[] = $invoice->error;
						$error++;
					} else {
						$amount = price2num($invoice->total_ttc - ($invoice->getSommePaiement() + $invoice->getSumCreditNotesUsed() + $invoice->getSumDepositsUsed()));
						if (GETPOST("amount", 'alpha')) {
							$amount = GETPOST("amount", 'alpha');
						}
					}
					break;

				case 'order':
					$result = $order->fetch('', $ref);
					if ($result <= 0) {
						$errors[] = $order->error;
						$error++;
					} else {
						$amount = $order->total_ttc;
						if (GETPOST("amount", 'alpha')) {
							$amount = GETPOST("amount", 'alpha');
						}
					}
					break;

				default:
					$resultinvoice = $invoice->fetch($ref);
					if ($resultinvoice <= 0) {
						$errors[] = $invoice->errors;
						$error++;
					} else {
						$amount = $invoice->total_ttc;
					}
					break;
			}

			$amount = price2num($amount);
			$_SESSION["FinalPaymentAmt"] = $amount;

		} elseif (in_array($parameters['paymentmethod'], array('helloasso')) && $parameters['validpaymentmethod']["helloasso"] == "valid") {
			require_once DOL_DOCUMENT_ROOT."/core/lib/geturl.lib.php";
			dol_include_once('helloasso/lib/helloasso.lib.php');
			$urlback = $urlwithroot.'/public/payment/newpayment.php?';

			if (!preg_match('/^https:/i', $urlback)) {
				$langs->load("errors");
				$error++;
				$errors[] = $langs->trans("WarningAvailableOnlyForHTTPSServers");
			}

			//Verify if Helloasso module is in test mode
			if (getDolGlobalInt("HELLOASSO_LIVE")) {
				$client_organisation = getDolGlobalString("HELLOASSO_CLIENT_ORGANISATION");
				$helloassourl = "api.helloasso.com";
			} else {
				$client_organisation = getDolGlobalString("HELLOASSO_TEST_CLIENT_ORGANISATION");
				$helloassourl = "api.helloasso-sandbox.com";
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
			$urlback .= 'action=returnDoPaymentHelloAsso';

			$result = doConnectionHelloasso();	// @TODO LMR Get the token from database and OAuth module

			if ($result <= 0) {
				$errors[] = $langs->trans("ErrorFailedToGetTokenFromClientIdAndSecret");
				$error++;
				$action = '';
			}


			if (!$error) {
				$fulltag = $FULLTAG;
				$FinalPaymentAmt = $_SESSION["FinalPaymentAmt"];
				$amounttotest = $amount;
				if (!$error) {
					//Permit to format the amount string to call HelloAsso API
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
						$headers[] = "Authorization: ".ucfirst($result["token_type"])." ".$result["access_token"];
						$headers[] = "Accept: application/json";
						$headers[] = "Content-Type: application/json";

						$jsontosenddata = '{
							"totalAmount": '.$amount.',
							"initialAmount": '.$amount.',
							"itemName": "'.dol_escape_js($ref).'",
							"backUrl": "'.$urlback.'&typereturn=back",
							"returnUrl": "'.$urlback.'&typereturn=return",
							"errorUrl": "'.$urlback.'&typereturn=error",
							"containsDonation": false,';
						// @TODO LMR Add information on payer
						/*
						$jsontosenddata .= '
							"payer": {
								"firstName": "John",
								"lastName": "Doe",
								"email": "john.doe@test.com",
								"address": "23 rue du palmier",
								"city": "Paris",
								"zipCode": "75000",
								"country": "FRA",
								"companyName": "JJJ"
							},';
						*/
						$jsontosenddata .= '
							"metadata": {
								"source": "'.dol_escape_js($source).'",
								"ref": "'.dol_escape_js($ref).'"
							}';
						$jsontosenddata .= '}';

						$urlforcheckout = "https://".urlencode($helloassourl)."/v5/organizations/".urlencode($client_organisation)."/checkout-intents";

						$ret2 = getURLContent($urlforcheckout, 'POSTALREADYFORMATED', $jsontosenddata, 1, $headers);
						if ($ret2["http_code"] == 200) {
							$result2 = $ret2["content"];
							$json2 = json_decode($result2);

							dol_syslog("Send redirect to ".$json2->redirectUrl);

							header("Location: ".$json2->redirectUrl);
							exit;
						} else {
							$arrayofmessage = array();
							if (!empty($ret2['content'])) {
								$arrayofmessage = json_decode($ret2['content'], true);
							}
							if (!empty($arrayofmessage['message'])) {
								$errors[] = $arrayofmessage['message'];
							} else {
								if (!empty($arrayofmessage['errors']) && is_array($arrayofmessage['errors'])) {
									foreach($arrayofmessage['errors'] as $tmpkey => $tmpmessage) {
										if (!empty($tmpmessage['message'])) {
											$errors[] = $langs->trans("Error").' - '.$tmpmessage['message'];
										} else {
											$errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret2["http_code"];
										}
									}
								} else {
									$errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret2["http_code"];
								}
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

		if (in_array($parameters['paymentmethod'], array('helloasso'))){
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

		if (in_array($parameters['paymentmethod'], array('helloasso'))){
			$bankaccountid = getDolGlobalInt('HELLOASSO_BANK_ACCOUNT_FOR_PAYMENTS');
			if ($bankaccountid == 0) {
				$error++;
			}
		}

		if (!$error) {
			$this->results["bankaccountid"] = $bankaccountid;
			return 1;
		} else {
			$this->errors[] = $langs->trans("BankAccountNotFound");
			return -1;
		}
	}
}

