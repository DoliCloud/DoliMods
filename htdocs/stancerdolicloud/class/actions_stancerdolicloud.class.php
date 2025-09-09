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
 * \file    stancerdolicloud/class/actions_stancerdolicloud.class.php
 * \ingroup stancerdolicloud
 * \brief   File for Stancer hooks
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsStancerDolicloud
 */
class ActionsStancerDolicloud extends CommonHookActions
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
		$langs->load("stancerdolicloud@stancerdolicloud");
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
		$this->resprints = '';
		return 0;
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
		global $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("StancerMassAction").'</option>';
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
		$ret = 0;
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
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
			if ($user->hasRight('stancerdolicloud', 'myobject', 'read')) {
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
	 * @param   HookManager     $hookmanager    hookmanager
	 * @return  int                             Return integer <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// used to make some tabs removed
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('stancerdolicloud@stancerdolicloud');
			// used when we want to add some tabs
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/stancerdolicloud/stancerdolicloud_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('StancerTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'stancerdolicloudemails';
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
		global $langs;

		$error = 0; // Error counter
		$resprints = "";
		$error = "";

		if (array_key_exists("paymentmethod", $parameters) && (empty($parameters["paymentmethod"]) || $parameters["paymentmethod"] == 'stancerdolicloud') && isModEnabled('stancerdolicloud')) {
			if (!getDolGlobalString('STANCER_DOLICLOUD_LIVE')) {
				dol_htmloutput_mesg($langs->trans('YouAreCurrentlyInSandboxMode', 'Stancer'), [], 'warning');
			}

			$resprints .= '<div class="button buttonpayment" id="div_dopayment_stancer"><span class="fa fa-credit-card"></span> <input class="" type="submit" id="dopayment_stancerdolicloud" name="dopayment_stancerdolicloud" value="'.$langs->trans("StancerDoPayment").'">';
			$resprints .= '<input type="hidden" name="noidempotency" value="'.GETPOST('noidempotency', 'int').'">';
			$resprints .= '<input type="hidden" name="s" value="'.(GETPOST('s', 'alpha') ? GETPOST('s', 'alpha') : GETPOST('source', 'alpha')).'">';
			$resprints .= '<input type="hidden" name="ref" value="'.GETPOST('ref').'">';
			$resprints .= '<br>';
			$resprints .= '<span class="buttonpaymentsmall">'.$langs->trans("CreditOrDebitCard").'</span>';
			$resprints .= '</div>';
			$resprints .= '<script>
							$( document ).ready(function() {
								$("#div_dopayment_stancer").click(function(){
									$("#dopayment_stancerdolicloud").click();
								});
								$("#dopayment_stancerdolicloud").click(function(e){
									$("#div_dopayment_stancer").css( \'cursor\', \'wait\' );
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
		global $langs;

		$error = "";
		$validpaymentmethod = array();

		if (array_key_exists("paymentmethod", $parameters) && (empty($parameters["paymentmethod"]) || $parameters["paymentmethod"] == 'stancerdolicloud') && isModEnabled('stancerdolicloud')) {
			$langs->load("stancerdolicloud");
			if (!empty($parameters['mode'])) {
				$validpaymentmethod['stancerdolicloud'] = array('label' => 'Stancer', 'status' => 'valid');
			} else {
				$validpaymentmethod['stancerdolicloud'] = 'valid';
			}
		}

		if (!$error) {
			if (!empty($validpaymentmethod)) {
				$this->results["validpaymentmethod"] = $validpaymentmethod;
			}
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
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT."/core/lib/geturl.lib.php";
		include_once DOL_DOCUMENT_ROOT.'/core/lib/security.lib.php';
		dol_include_once('stancerdolicloud/lib/stancerdolicloud.lib.php');

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
		$suffix = GETPOST('suffix'); 	// ???
		$entity = GETPOST('entity');
		$getpostlang = GETPOST('lang');
		$amount = price2num(GETPOST("amount", 'alpha'));

		$object = null;

		if ($action == "returnDoPaymentStancer") {
			dol_syslog("Data after redirect from stancer payment page with session FinalPaymentAmt = ".$_SESSION["FinalPaymentAmt"]." currencycodeType = ".$_SESSION["currencyCodeType"], LOG_DEBUG);

			$stancerurlapi = "api.stancer.com";
			if (getDolGlobalInt("STANCER_DOLICLOUD_LIVE")) {
				$secretapikey = getDolGlobalString("STANCER_DOLICLOUD_PROD_SECRET_API_KEY");
			} else {
				$secretapikey = getDolGlobalString("STANCER_DOLICLOUD_TEST_SECRET_API_KEY");
			}
			$encodedkey = dol_encode($secretapikey, 0);

			$headers = array();
			$headers[] = "accept: application/json";
			$headers[] = "Authorization: Basic ".$encodedkey;
			$headers[] = "Content-Type: application/json";

			$jsontosenddata = '{}';
			// Find way verify if payment is done
			$urlforcheckout = "https://".urlencode($stancerurlapi)."/v2/payment_intents/".$_SESSION["STANCER_DOLICLOUD_PAYMENT_ID"];
			$ret1 = getURLContent($urlforcheckout, 'GET', $jsontosenddata, 1, $headers);

			$urlredirect = $urlwithroot.'/public/payment/';
			if ($ret1["http_code"] == 200) {
				$result1 = $ret1["content"];
				$json1 = json_decode($result1);
				$urlredirect .= "paymentok.php?fulltag=".urlencode($FULLTAG);
				header("Location: ".$urlredirect);
				exit;
			} else {
				$urlredirect .= "paymentko.php?fulltag=".urlencode($FULLTAG);
				header("Location: ".$urlredirect);
				exit;
			}
		}

		if (in_array($parameters['context'],array('newpayment')) && empty($parameters['paymentmethod'])) {
			$amount = price2num(stancerDolicloudGetDataFromObjects($source, $ref));
			if (!GETPOST("currency", 'alpha')) {
				$currency = $conf->currency;
			} else {
				$currency = GETPOST("currency", 'aZ09');
			}
			$_SESSION["FinalPaymentAmt"] = $amount;
			$_SESSION["currencyCodeType"] = $currency;

		} elseif (in_array($parameters['paymentmethod'], array('stancerdolicloud')) && $parameters['validpaymentmethod']["stancerdolicloud"] == "valid") {
			$urlback = $urlwithroot.'/public/payment/newpayment.php?';

			if (!preg_match('/^https:/i', $urlback)) {
				$langs->load("errors");
				$error++;
				$errors[] = $langs->trans("WarningAvailableOnlyForHTTPSServers");
			}

			//Verify if Stancer module is in test mode
			$stancerurlapi = "api.stancer.com";
			$stancerurlpayment = "payment.stancer.com";
			if (getDolGlobalInt("STANCER_DOLICLOUD_LIVE")) {
				$secretapikey = getDolGlobalString("STANCER_DOLICLOUD_PROD_SECRET_API_KEY");
			} else {
				$secretapikey = getDolGlobalString("STANCER_DOLICLOUD_TEST_SECRET_API_KEY");
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
			$urlback .= 'action=returnDoPaymentStancer';

			if (!$error) {
				$FinalPaymentAmt = $_SESSION["FinalPaymentAmt"];
				$currencyCodeType = $_SESSION["currencyCodeType"];
				$amounttotest = $amount;
				$encodedkey = dol_encode($secretapikey, 0);
				if (!$error) {
					//Permit to format the amount string to call Stancer API
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
						$headers[] = "Authorization: Basic ".$encodedkey;
						$headers[] = "Content-Type: application/json";

						$jsontosenddata = '{
							"amount": '.$amount.',
							"currency": "'.strtolower($currencyCodeType).'",
							"return_url": "'.$urlback.'"';
						$jsontosenddata .= '}';

						$urlforcheckout = "https://".urlencode($stancerurlapi)."/v2/payment_intents/";

						dol_syslog("Send Post to url=".$urlforcheckout." with session FinalPaymentAmt = ".$FinalPaymentAmt." currencyCodeType = ".$currencyCodeType, LOG_DEBUG);

						$ret1 = getURLContent($urlforcheckout, 'POSTALREADYFORMATED', $jsontosenddata, 1, $headers);
						if ($ret1["http_code"] == 200) {
							$result1 = $ret1["content"];
							$json1 = json_decode($result1);
							$_SESSION["STANCER_DOLICLOUD_PAYMENT_ID"] = urlencode($json1->id);
							$urlforredirect = "https://".urlencode($stancerurlpayment)."/".(!getDolGlobalInt("STANCER_DOLICLOUD_LIVE") ? "test_" : "").$_SESSION["STANCER_DOLICLOUD_PAYMENT_ID"];

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
							} else {
								if (!empty($arrayofmessage['errors']) && is_array($arrayofmessage['errors'])) {
									foreach($arrayofmessage['errors'] as $tmpkey => $tmpmessage) {
										if (!empty($tmpmessage['message'])) {
											$errors[] = $langs->trans("Error").' - '.$tmpmessage['message'];
										} else {
											$errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret1["http_code"];
										}
									}
								} else {
									$errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret1["http_code"];
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
		global $langs;

		require_once DOL_DOCUMENT_ROOT."/core/lib/geturl.lib.php";
		include_once DOL_DOCUMENT_ROOT.'/core/lib/security.lib.php';

		$error = 0; // Error counter
		$ispaymentok = true;

		if (in_array($parameters['paymentmethod'], array('stancerdolicloud'))){
			$code = GETPOST("code");

			if ($code == "refused") {
				$ispaymentok = false;
				$error ++;
			} else {
				$stancerurlapi = "api.stancer.com";
				$stancerurlpayment = "payment.stancer.com";
				if (getDolGlobalInt("STANCER_DOLICLOUD_LIVE")) {
					$secretapikey = getDolGlobalString("STANCER_DOLICLOUD_PROD_SECRET_API_KEY");
				} else {
					$secretapikey = getDolGlobalString("STANCER_DOLICLOUD_TEST_SECRET_API_KEY");
				}
				$encodedkey = dol_encode($secretapikey, 0);
				$headers = array();
				$headers[] = "accept: application/json";
				$headers[] = "Authorization: Basic ".$encodedkey;
				$headers[] = "Content-Type: application/json";

				$FinalPaymentID = $_SESSION["STANCER_DOLICLOUD_PAYMENT_ID"];
				$urlforcheckout = "https://".urlencode($stancerurlapi)."/v2/payment_intents/".$FinalPaymentID;
				dol_syslog("Send Get to url=".$urlforcheckout." with session STANCER_DOLICLOUD_PAYMENT_ID = ".$FinalPaymentID, LOG_DEBUG);
				$ret1 = getURLContent($urlforcheckout, 'GET', "", 1, $headers);
				if ($ret1["http_code"] == 200) {
					$result1 = $ret1["content"];
					$json = json_decode($result1);
					if (!in_array($json->status, array("captured", "authorized", "capture_sent", "to_capture"))) {
						$ispaymentok = false;
					}
				} else {
					$arrayofmessage = array();
					if (!empty($ret1['content'])) {
						$arrayofmessage = json_decode($ret1['content'], true);
					}
					if (!empty($arrayofmessage['message'])) {
						$this->errors[] = $arrayofmessage['message'];
					} else {
						if (!empty($arrayofmessage['errors']) && is_array($arrayofmessage['errors'])) {
							foreach($arrayofmessage['errors'] as $tmpkey => $tmpmessage) {
								if (!empty($tmpmessage['message'])) {
									$this->errors[] = $langs->trans("Error").' - '.$tmpmessage['message'];
								} else {
									$this->errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret1["http_code"];
								}
							}
						} else {
							$this->errors[] = $langs->trans("UnkownError").' - HTTP code = '.$ret1["http_code"];
						}
					}
					$error++;
					$ispaymentok = false;
				}
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
		$error = 0; // Error counter

		$bankaccountid = 0;

		if (in_array($parameters['paymentmethod'], array('stancerdolicloud'))){
			$bankaccountid = getDolGlobalInt('STANCER_DOLICLOUD_BANK_ACCOUNT_FOR_PAYMENTS');
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
		if (isModEnabled('stancerdolicloud')) {
			$this->results['showonlinepaymenturl'] = isModEnabled('stancerdolicloud');
		} else {
			return -1;
		}
		return 1;
	}
}

