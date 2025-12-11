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
 * @param	array	$payerarray		An array to fill the payer informations (Must be set with payer mode)
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
				} elseif($mode == 'payer' && !is_null($payerarray)) {
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

					if (!empty($member->last_subscription_amount) && !GETPOSTISSET('newamount') && is_numeric($amount)){
						$amount = max($member->last_subscription_amount, $amount);
					}
					$amount = max(0, getDolGlobalString('MEMBER_MIN_AMOUNT'), $amount);
				} else if($mode == 'payer' && !is_null($payerarray)) {
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
				} else if ($mode == 'payer' && !is_null($payerarray)) {
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
				} else if ($mode == 'payer' && !is_null($payerarray)) {
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
				} else if ($mode == 'payer' && !is_null($payerarray)) {
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
				} else if ($mode == 'payer' && !is_null($payerarray)) {
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