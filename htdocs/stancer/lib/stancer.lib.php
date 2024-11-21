<?php
/* Copyright (C) 2024      Lucas Marcouiller    <lmarcouiller@dolicloud.com>
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
 * \file    stancer/lib/stancer.lib.php
 * \ingroup stancer
 * \brief   Library files with common functions for Stancer
 */

use OAuth\Common\Storage\DoliStorage;
use OAuth\OAuth2\Token\StdOAuth2Token;
/**
 * Prepare admin pages header
 *
 * @return array
 */
function stancerAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("stancer@stancer");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/stancer/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/stancer/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/stancer/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@stancer:/stancer/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@stancer:/stancer/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'stancer@stancer');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'stancer@stancer', 'remove');

	return $head;
}


/**
 * Get data form an object
 *
 * @param	$source 		The type of the object
 * @param	$ref			The ref of the object
 * @param	$mode			The mode to use for the function amount
 *
 * @return	int				The amount to pay if mode amount
 */

function stancerGetDataFromObjects($source, $ref, $mode = 'amount')
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

	dol_syslog('Stancer::stancerGetDataFromObjects');

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
				}
			}
			break;

	}
	return $amount;
}