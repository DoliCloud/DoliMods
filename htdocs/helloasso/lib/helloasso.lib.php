<?php
/* Copyright (C) 2023 Alice Adminson <myemail@mycompany.com>
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
 * \file    helloasso/lib/helloasso.lib.php
 * \ingroup helloasso
 * \brief   Library files with common functions for HelloAsso
 */

use OAuth\Common\Storage\DoliStorage;
use OAuth\OAuth2\Token\StdOAuth2Token;
/**
 * Prepare admin pages header
 *
 * @return array
 */
function helloassoAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("helloasso@helloasso");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/helloasso/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/helloasso/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/helloasso/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@helloasso:/helloasso/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@helloasso:/helloasso/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'helloasso@helloasso');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'helloasso@helloasso', 'remove');

	return $head;
}

/**
 * Refresh connection token
 * 
 * @throws Exception
 * @return TokenInterface|int	Token if OK
 */

 function refreshToken($storage, $service, $tokenobj, $client_id, $urltocall) {
	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	$refreshtoken = $tokenobj->getRefreshToken();
	$ret = getURLContent($urltocall, 'POST', 'grant_type=refresh_token&client_id='.$client_id.'&refresh_token='.$refreshtoken, 1, array('content-type: application/x-www-form-urlencoded'));

	if ($ret["http_code"] == 200) {
		$jsondata = $ret["content"];
		$json = json_decode($jsondata);
		$ttl = dol_time_plus_duree(dol_now(), $json->expires_in, 's');
		$newtokenobj = new StdOAuth2Token();
		$newtokenobj->setAccessToken($json->access_token);
		$newtokenobj->setRefreshToken($json->refresh_token);
		$newtokenobj->setEndOfLife($ttl);

		$params = array("scope" => $urltocall, "token_type" => $json->token_type);
		$newtokenobj->setExtraParams($params);
		$storage->storeAccessToken($service, $newtokenobj);
	} else {
		throw new Exception("Refresh token expires", 1);
	}
	return $storage->retrieveAccessToken($service);
 }

/**
 * Connect to helloasso database
 *
 * @return array|int 	An array with the token_type and the access_token defined if OK or -1 if KO
 */
function doConnectionHelloasso()
{
	require_once DOL_DOCUMENT_ROOT.'/includes/OAuth/bootstrap.php';
	include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
	include_once DOL_DOCUMENT_ROOT.'/core/lib/security.lib.php';
	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

	global $db, $conf;
	$result = array();

	$helloassourl = "api.helloasso-sandbox.com";
	$service = "Helloasso-Test";

	// Verify if Helloasso module is in test mode
	if (getDolGlobalInt("HELLOASSO_LIVE")) {
		$client_id = getDolGlobalString("HELLOASSO_CLIENT_ID");
		$client_id_secret = getDolGlobalString("HELLOASSO_CLIENT_SECRET");
		$helloassourl = "api.helloasso.com";
		$service = "Helloasso-Live";
	} else{
		$client_id = getDolGlobalString("HELLOASSO_TEST_CLIENT_ID");
		$client_id_secret = getDolGlobalString("HELLOASSO_TEST_CLIENT_SECRET");
	}

	$url = "https://".urlencode($helloassourl)."/oauth2/token";

	// Dolibarr storage
	$storage = new DoliStorage($db, $conf);
	try {
		$tokenobj = $storage->retrieveAccessToken($service);
		$ttl = $tokenobj->getEndOfLife();
		if ($ttl <= dol_now()) {
			$tokenobj = refreshToken($storage, $service, $tokenobj, $client_id, $url);
		}
		$result = array("token_type" => $tokenobj->getExtraParams()["token_type"], "access_token" => $tokenobj->getAccessToken());
	} catch (Exception $e) {
		$ret = getURLContent($url, 'POST', 'grant_type=client_credentials&client_id='.$client_id.'&client_secret='.$client_id_secret, 1, array('content-type: application/x-www-form-urlencoded'));

		if ($ret["http_code"] == 200) {
			$jsondata = $ret["content"];
			$json = json_decode($jsondata);
			$result = array("token_type" => $json->token_type, "access_token" => $json->access_token);

			$ttl = dol_time_plus_duree(dol_now(), $json->expires_in, 's');
			$tokenobj = new StdOAuth2Token();
			$tokenobj->setAccessToken($json->access_token);
			$tokenobj->setRefreshToken($json->refresh_token);
			$tokenobj->setEndOfLife($ttl);

			$params = array("scope" => $url, "token_type" => $json->token_type);
			$tokenobj->setExtraParams($params);

			$storage->storeAccessToken($service, $tokenobj);
		} else {
			$result = -1;
		}
	}
	return $result;
}

/**
 * Get data form an object
 * 
 * @param	$source 		The type of the object
 * @param	$ref			The ref of the object
 * @param	$mode			The mode to use for the function (amount or payer)
 * @param	$payerarray		An array to fill the payer informations (Must be set with payer mode)
 * 
 * @return	int				The amount to pay if mode amount or fill $payerarray for payer mode
 */

function getDataFromObjects($source, $ref, $mode = 'amount', &$payerarray = null)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
	require_once DOL_DOCUMENT_ROOT.'/don/class/don.class.php';
	require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
	require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent_type.class.php';
	require_once DOL_DOCUMENT_ROOT.'/adherents/class/subscription.class.php';
	require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';

	$amount = price2num(GETPOST("amount", 'alpha'));

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
				if ($mode == 'amount') {
					if (GETPOST("amount", 'alpha')) {
						$amount = GETPOST("amount", 'alpha');
					} else {
						$amount = $don->getRemainToPay();
					}
				} else if($mode == 'payer' && !is_null($payerarray)) {
					$payerarray['firstName'] = $don->firstname;
					$payerarray['lastName'] = $don->lastname;
					$payerarray['email'] = $don->email;
					$payerarray['address'] = $don->address;
					$payerarray['city'] = $don->town;
					$payerarray['zipCode'] = $don->zip;
					$payerarray['companyName'] = $don->societe;
				}
			}
			break;

		case 'member':
			$result = $member->fetch('', $ref);
			if ($result <= 0) {
				$errors[] = $member->error;
				$error++;
			} else {
				if ($mode == 'amount') {
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
				} else if($mode == 'payer' && !is_null($payerarray)) {
					$payerarray['firstName'] = $member->firstname;
					$payerarray['lastName'] = $member->lastname;
					$payerarray['email'] = $member->email;
					$payerarray['dateOfBirth'] = dol_print_date($member->birth, 'dayrfc');
				}
			}
			break;

		case 'contractline':
			$result = $contractline->fetch('', $ref);
			if ($result <= 0) {
				$errors[] = $contractline->error;
				$error++;
			} else {
				if ($mode == 'amount') {
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
				} else if($mode == 'payer' && !is_null($payerarray)) {
					$invoice->fetch_thirdparty();
					$payerarray['companyName'] = $invoice->thirdparty->name;
					$payerarray['address'] = $invoice->thirdparty->address;
					$payerarray['zipCode'] = $invoice->thirdparty->zip;
					$payerarray['city'] = $invoice->thirdparty->town;
					$payerarray['email'] = $invoice->thirdparty->email;
				}
			}
			break;

		case 'invoice':
			$result = $invoice->fetch('', $ref);
			if ($result <= 0) {
				$errors[] = $invoice->error;
				$error++;
			} else {
				if ($mode == 'amount') {
					$amount = price2num($invoice->total_ttc - ($invoice->getSommePaiement() + $invoice->getSumCreditNotesUsed() + $invoice->getSumDepositsUsed()));
					if (GETPOST("amount", 'alpha')) {
						$amount = GETPOST("amount", 'alpha');
					}
				} else if($mode == 'payer' && !is_null($payerarray)) {
					$invoice->fetch_thirdparty();
					$payerarray['companyName'] = $invoice->thirdparty->name;
					$payerarray['address'] = $invoice->thirdparty->address;
					$payerarray['zipCode'] = $invoice->thirdparty->zip;
					$payerarray['city'] = $invoice->thirdparty->town;
					$payerarray['email'] = $invoice->thirdparty->email;
				}
			}
			break;

		case 'order':
			$result = $order->fetch('', $ref);
			if ($result <= 0) {
				$errors[] = $order->error;
				$error++;
			} else {
				if ($mode == 'amount') {
					$amount = $order->total_ttc;
					if (GETPOST("amount", 'alpha')) {
						$amount = GETPOST("amount", 'alpha');
					}
				} else if($mode == 'payer' && !is_null($payerarray)) {
					$order->fetch_thirdparty();
					$payerarray['companyName'] = $order->thirdparty->name;
					$payerarray['address'] = $order->thirdparty->address;
					$payerarray['zipCode'] = $order->thirdparty->zip;
					$payerarray['city'] = $order->thirdparty->town;
					$payerarray['email'] = $order->thirdparty->email;
				}
			}
			break;

		default:
			$resultinvoice = $invoice->fetch($ref);
			if ($resultinvoice <= 0) {
				$errors[] = $invoice->errors;
				$error++;
			} else {
				if ($mode == 'amount') {
					$amount = $invoice->total_ttc;
				} else if($mode == 'payer' && !is_null($payerarray)) {
					$invoice->fetch_thirdparty();
					$payerarray['companyName'] = $invoice->thirdparty->name;
					$payerarray['address'] = $invoice->thirdparty->address;
					$payerarray['zipCode'] = $invoice->thirdparty->zip;
					$payerarray['city'] = $invoice->thirdparty->town;
					$payerarray['email'] = $invoice->thirdparty->email;
				}
			}
			break;
	}
	return $amount;
}