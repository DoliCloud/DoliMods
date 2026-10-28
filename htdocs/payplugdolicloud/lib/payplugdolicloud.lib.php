<?php
/* Copyright (C) 2024		SuperAdmin					<daoud.mouhamed@gmail.com>
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
 * \file    payplugdolicloud/lib/payplugdolicloud.lib.php
 * \ingroup payplugdolicloud
 * \brief   Library files with common functions for PayplugDolicloud
 */

/**
 * Prepare admin pages header
 *
 * @return array<array{string,string,string}>
 */
function payplugdolicloudAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("payplugdolicloud@payplugdolicloud");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/payplugdolicloud/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/payplugdolicloud/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/payplugdolicloud/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@payplugdolicloud:/payplugdolicloud/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@payplugdolicloud:/payplugdolicloud/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'payplugdolicloud@payplugdolicloud');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'payplugdolicloud@payplugdolicloud', 'remove');

	return $head;
}


/**
 * Get data form an object
 *
 * @param	string	$source 		The type of the object
 * @param	string	$ref			The ref of the object
 * @param	string	$mode			The mode to use for the function amount
 * @param	array	$payerarray		An array to fill the payer information (Must be set with payer mode)
 * @return	int						The amount to pay if mode amount
 */
function payplugGetDataFromObjects($source, $ref, $mode = 'amount', &$payerarray = null)
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

	$errors = array();
	$error = 0;

	dol_syslog('Payplug::payplugGetDataFromObjects');

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
				} elseif ($mode == 'payer' && !is_null($payerarray)) {
					$payerarray['firstName'] = $don->firstname;
					$payerarray['lastName'] = $don->lastname;
					$payerarray['email'] = $don->email;
					$payerarray['address'] = $don->address;
					$payerarray['city'] = $don->town;
					$payerarray['zipCode'] = $don->zip;
					$payerarray['countryCode'] = $don->country_code;
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

					if (!empty($member->last_subscription_amount) && !GETPOSTISSET('newamount') && is_numeric($amount)) {
						$amount = max($member->last_subscription_amount, $amount);
					}
					$amount = max(0, getDolGlobalString('MEMBER_MIN_AMOUNT'), $amount);
				} elseif ($mode == 'payer' && !is_null($payerarray)) {
					$payerarray['firstName'] = $member->firstname;
					$payerarray['lastName'] = $member->lastname;
					$payerarray['companyName'] = $member->company;
					$payerarray['zipCode'] = $member->zip;
					$payerarray['address'] = $member->address;
					$payerarray['email'] = $member->email;
					$payerarray['countryCode'] = $member->country_code;
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
				} elseif ($mode == 'payer' && !is_null($payerarray)) {
					$contract = new Contrat($db);
					$contract->fetch($contractline->fk_contrat);
					$contract->fetch_thirdparty();

					if ($contract->thirdparty->isACompany()) {
						$payerarray['companyName'] = $contract->thirdparty->name;
					}
					$payerarray['address'] = $contract->thirdparty->address;
					$payerarray['zipCode'] = $contract->thirdparty->zip;
					$payerarray['city'] = $contract->thirdparty->town;
					$payerarray['country'] = $contract->thirdparty->country_code;
					$payerarray['email'] = $contract->thirdparty->email;

					$result = $member->fetch(0, '', $contract->thirdparty->id);
					if ($result > 0) {
						$payerarray['firstName'] = $member->firstname;
						$payerarray['lastName'] = $member->lastname;
						$payerarray['dateOfBirth'] = dol_print_date($member->birth, 'dayrfc');
					}
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
				} elseif ($mode == 'payer' && !is_null($payerarray)) {
					$invoice->fetch_thirdparty();

					if ($invoice->thirdparty->isACompany()) {
						$payerarray['companyName'] = $invoice->thirdparty->name;
					}
					$payerarray['address'] = $invoice->thirdparty->address;
					$payerarray['zipCode'] = $invoice->thirdparty->zip;
					$payerarray['city'] = $invoice->thirdparty->town;
					$payerarray['country'] = $invoice->thirdparty->country_code;
					$payerarray['email'] = $invoice->thirdparty->email;

					$result = $member->fetch(0, '', $invoice->thirdparty->id);
					if ($result > 0) {
						$payerarray['firstName'] = $member->firstname;
						$payerarray['lastName'] = $member->lastname;
						$payerarray['dateOfBirth'] = dol_print_date($member->birth, 'dayrfc');
					}
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
				} elseif ($mode == 'payer' && !is_null($payerarray)) {
					$order->fetch_thirdparty();

					if ($order->thirdparty->isACompany()) {
						$payerarray['companyName'] = $order->thirdparty->name;
					}
					$payerarray['address'] = $order->thirdparty->address;
					$payerarray['zipCode'] = $order->thirdparty->zip;
					$payerarray['city'] = $order->thirdparty->town;
					$payerarray['email'] = $order->thirdparty->email;

					$result = $member->fetch(0, '', $order->thirdparty->id);
					if ($result > 0) {
						$payerarray['firstName'] = $member->firstname;
						$payerarray['lastName'] = $member->lastname;
						$payerarray['dateOfBirth'] = dol_print_date($member->birth, 'dayrfc');
					}
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
				} elseif ($mode == 'payer' && !is_null($payerarray)) {
					$invoice->fetch_thirdparty();

					if ($invoice->thirdparty->isACompany()) {
						$payerarray['companyName'] = $invoice->thirdparty->name;
					}
					$payerarray['address'] = $invoice->thirdparty->address;
					$payerarray['zipCode'] = $invoice->thirdparty->zip;
					$payerarray['city'] = $invoice->thirdparty->town;
					$payerarray['email'] = $invoice->thirdparty->email;

					$result = $member->fetch(0, '', $invoice->thirdparty->id);
					if ($result > 0) {
						$payerarray['firstName'] = $member->firstname;
						$payerarray['lastName'] = $member->lastname;
						$payerarray['dateOfBirth'] = dol_print_date($member->birth, 'dayrfc');
					}
				}
			}
			break;
	}
	return $amount;
}

/**
 * Save the data of a pending payment, to find them back on the return pages.
 * When the core provides onlinePaymentSession*() (v25+), they are used (table llx_onlinepayment_session), else the table of the module is used.
 *
 * @param 	string		$ref_payment	Unique key of the pending payment (generated when the payment is initiated, sent in the return url)
 * @param 	array<string,mixed>	$data		Data to save (stored in json)
 * @return 	int			1 if the data was saved successfully, -1 if error
 */
function payplugDolicloudPendingPaymentSave($ref_payment, $data)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/payments.lib.php';
	if (function_exists('onlinePaymentSessionSave')) {
		return onlinePaymentSessionSave('PayPlug', $ref_payment, $data);
	}

	$sql = "INSERT INTO ".MAIN_DB_PREFIX."payplugdolicloud_pending_payments (ref_payment, data, date_creation)";
	$sql .= " VALUES ('".$db->escape($ref_payment)."', '".$db->escape(json_encode($data))."', '".$db->idate(dol_now())."')";
	return $db->query($sql) ? 1 : -1;
}

/**
 * Get the data of a pending payment.
 *
 * @param 	string		$ref_payment	Unique key of the pending payment (sent in the return url)
 * @return 	stdClass|null				Data of the pending payment, or null if not found or error
 */
function payplugDolicloudPendingPaymentFetch($ref_payment)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/payments.lib.php';
	if (function_exists('onlinePaymentSessionFetch')) {
		return onlinePaymentSessionFetch('PayPlug', $ref_payment);
	}

	$sql = "SELECT data FROM ".MAIN_DB_PREFIX."payplugdolicloud_pending_payments WHERE ref_payment = '".$db->escape($ref_payment)."'";
	$resql = $db->query($sql);
	if (!$resql) {
		dol_syslog(__FUNCTION__." SQL Error on SELECT: ".$db->lasterror(), LOG_ERR);
		return null;
	}
	$obj = $db->fetch_object($resql);
	if (!$obj) {
		return null;
	}
	$json = json_decode($obj->data);
	return is_object($json) ? $json : null;
}

/**
 * Replace the data of a pending payment.
 *
 * @param 	string		$ref_payment	Unique key of the pending payment
 * @param 	stdClass|array<string,mixed>	$data	New data (stored in json)
 * @return 	int			1 if the data was updated successfully, -1 if error
 */
function payplugDolicloudPendingPaymentUpdate($ref_payment, $data)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/payments.lib.php';
	if (function_exists('onlinePaymentSessionSave')) {
		// onlinePaymentSessionSave() updates the row if it already exists
		return onlinePaymentSessionSave('PayPlug', $ref_payment, (array) $data);
	}

	$sql = "UPDATE ".MAIN_DB_PREFIX."payplugdolicloud_pending_payments SET data = '".$db->escape(json_encode($data))."' WHERE ref_payment = '".$db->escape($ref_payment)."'";
	return $db->query($sql) ? 1 : -1;
}

/**
 * Delete a pending payment so it can't be replayed. Only the call that really deletes it returns 1.
 *
 * @param 	string		$ref_payment	Unique key of the pending payment
 * @return 	int			1 if exactly one row was deleted, -1 if error
 */
function payplugDolicloudPendingPaymentConsume($ref_payment)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/payments.lib.php';
	if (function_exists('onlinePaymentSessionConsume')) {
		return onlinePaymentSessionConsume('PayPlug', $ref_payment);
	}

	$sql = "DELETE FROM ".MAIN_DB_PREFIX."payplugdolicloud_pending_payments WHERE ref_payment = '".$db->escape($ref_payment)."'";
	$resql = $db->query($sql);
	return ($resql && $db->affected_rows($resql) == 1) ? 1 : -1;
}

/**
 * Delete the old pending payments.
 *
 * @param 	integer		$datelimit		Pending payments created before this date (timestamp) are deleted
 * @return 	int			1 if the old pending payments were deleted successfully, -1 if error
 */
function payplugDolicloudPendingPaymentPurge($datelimit)
{
	global $db;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/payments.lib.php';
	if (function_exists('onlinePaymentSessionSave')) {
		// Nothing to do: onlinePaymentSessionSave() of the core already purges the old rows, and the purge of the core is not limited to this module
		return 1;
	}

	$sql = "DELETE FROM ".MAIN_DB_PREFIX."payplugdolicloud_pending_payments WHERE date_creation <= '".$db->idate($datelimit)."'";
	return $db->query($sql) ? 1 : -1;
}
