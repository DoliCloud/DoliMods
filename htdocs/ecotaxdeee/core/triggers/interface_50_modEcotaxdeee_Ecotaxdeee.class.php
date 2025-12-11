<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       ecotaxdeee/core/triggers/interface_modEcotaxdeee_Ecotaxdeee.class.php
 *  \ingroup    ecotaxdeee
 *  \brief      Add Ecotax for products into specific category
 */

include_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for module Ecotaxdeee
 */
class InterfaceEcotaxdeee extends DolibarrTriggers
{
	var $db;
	var $error;

	/**
	 *   Constructeur.
	 *
	 *   @param	DoliDB	$db      Handler d'acces base
	 */
	function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "product";
		$this->description = "Triggers of this module calculate ecotax of a product if product is inside category 'Ecotax').";
		$this->picto = 'generic';
	}

	/**
	 *   Renvoi nom du lot de triggers
	 *
	 *   @return     string      Nom du lot de triggers
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 *   Renvoi descriptif du lot de triggers
	 *
	 *   @return     string      Descriptif du lot de triggers
	 */
	function getDesc()
	{
		return $this->description;
	}

	/**
	 *   Renvoi version du lot de triggers
	 *
	 *   @return     string      Version du lot de triggers
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'experimental') return $langs->trans("Experimental");
		elseif ($this->version == 'dolibarr') return DOL_VERSION;
		elseif ($this->version) return $this->version;
		else return $langs->trans("Unknown");
	}

	/**
	 *      Fonction appelee lors du declenchement d'un evenement Dolibarr.
	 *      D'autres fonctions runTrigger peuvent etre presentes dans includes/triggers
	 *
	 *      @param	string		$action     Code of event
	 *      @param 	Object		$object     Objet concerne
	 *      @param  User		$user       Objet user
	 *      @param  Translate	$langs      Objet lang
	 *      @param  Conf		$conf       Objet conf
	 *      @return int         			<0 if KO, 0 if nothing is done, >0 if OK
	 */
	function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		// Création / Mise à jour / Suppression d'un évènement dans Google contact
		if (!isModEnabled("ecotaxdeee")) {
			return 0;
		}
		if (isset($object->special_code) && $object->special_code == 2) {
			return 0;			// To avoid infinite loop. Line with special_code = 2, like ecotax lines, are not triggered
		}
		if (! empty($object->context['createfromclone'])) {
			return 0;           // To avoid to add ecotax line during cloning
		}
		if (! empty($object->context['createcreditnotefrominvoice'])) {
			return 0;           // To avoid to add ecotax line during cloning
		}

		if (getDolGlobalString('ECOTAXDEEE_USE_ON_PROPOSAL')) {
			if ($action == 'LINEPROPAL_INSERT' || $action == 'LINEPROPAL_CREATE') {
				//var_dump($object);
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
			if ($action == 'LINEPROPAL_UPDATE' || $action == 'LINEPROPAL_MODIFY') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
			if ($action == 'LINEPROPAL_DELETE') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
		}
		if (getDolGlobalString('ECOTAXDEEE_USE_ON_CUSTOMER_ORDER')) {
			if ($action == 'LINEORDER_INSERT' || $action == 'LINEORDER_CREATE') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
			if ($action == 'LINEORDER_UPDATE' || $action == 'LINEORDER_MODIFY') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
			if ($action == 'LINEORDER_DELETE') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
		}
		if (getDolGlobalString('ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE')) {
			if ($action == 'LINEBILL_INSERT' || $action == 'LINEBILL_CREATE') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
			if ($action == 'LINEBILL_UPDATE' || $action == 'LINEBILL_MODIFY') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
			if ($action == 'LINEBILL_DELETE') {
				if ($object->product_type == 9) {
					return 0;
				}
				return $this->_add_replace_ecotax($action, $object, $user, $langs, $conf);
			}
		}
		/* TODO
		if (getDolGlobalString('ECOTAXDEEE_USE_ON_SUPPLIER_ORDER'))
		{
			if ($action == 'LINEORDER_SUPPLIER_INSERT' || $action == 'LINEORDER_SUPPLIER_CREATE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEORDER_SUPPLIER_UPDATE' || $action == 'LINEORDER_SUPPLIER_MODIFY')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEORDER_SUPPLIER_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}
		if (getDolGlobalString('ECOTAXDEEE_USE_ON_SUPPLIER_INVOICE'))
		{
			if ($action == 'LINEBILL_SUPPLIER_INSERT' || $action == 'LINEBILL_SUPPLIER_CREATE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEBILL_SUPPLIER_UPDATE' || $action == 'LINEBILL_SUPPLIER_MODIFY')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEBILL_SUPPLIER_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}
		*/

		// Renvoi 0 car aucune action de faite
		return 0;
	}


	/**
	 * Calculate ecotax.
	 * Called after an insert, update or delete of a line.
	 *
	 * @param  string      $action     Action
	 * @param  Object      $object     Is a line of object (->element, ->table_element must be defined)
	 * @param  User        $user       User
	 * @param  Translate   $langs      Langs
	 * @param  Conf        $conf       Conf
	 */
	function _add_replace_ecotax($action, $object, $user, $langs, $conf)
	{
		global $mysoc;

		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// The next 3 parameters can be replaced at will:
		$desc = getDolGlobalString('ECOTAXDEEE_LABEL_LINE', $langs->trans("EcoTaxDeee")); // label for ecotax
		//$ecocat = empty($conf->global->ECOTAXDEEE_CATEGORY_REF)?"Ecotax":$conf->global->ECOTAXDEEE_CATEGORY_REF; // the category products must be in, for ecotax to apply

		// Add a line EcoTax automatically
		dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id." rowid=".$object->rowid);

		$idlineecotax = array();
		$amountlineecotax_ht = array();
		$amountlineecotax_ttc = array();
		$amountlineecotax_vat = array();
		$keylineecotax = array();
		$tmpecotaxline = array();

		if ($object->special_code == 2 && in_array($action, array('LINEORDER_DELETE','LINEPROPAL_DELETE','LINEBILL_DELETE','LINEORDER_SUPPLIER_DELETE','LINEBILL_SUPPLIER_DELETE'))) {
			return 0;
		}

		// If we are creating an object from an other one, we forget adding eco tax (we keep all lines as into the source).
		if ((! empty($_POST['origin']) && (! empty($_POST['originid']) || ! empty($_POST['origin_id'])))
			|| (! empty($object->context['origin']) && ! empty($object->context['origin_id']))
			|| (! empty($object->context['createcreditnotefrominvoice']))) {
			return 0;
		}


		/*
		 * Calculate the EcoTax DEEE and try to find idlineecotax
		 */
		$ecoamount = array();
		$fieldparentid='';
		$parentobject=null;
		if ($object->element == 'facturedet' || get_class($object) == 'FactureLigne') {
			$fieldparentid='fk_facture';
			$parentobject=new Facture($this->db);
		}
		if ($object->element == 'propaldet' || get_class($object) == 'PropaleLigne') {
			$fieldparentid='fk_propal';
			$parentobject=new Propal($this->db);
		}
		if ($object->element == 'commandedet' || get_class($object) == 'OrderLine') {
			$fieldparentid='fk_commande';
			$parentobject=new Commande($this->db);
		}
		if (empty($fieldparentid)) {
			dol_syslog('Object '.$object->element.' not supported', LOG_WARNING);
			return;
		}
		$parentid = $object->$fieldparentid;
		if (empty($parentid)) {
			$parentid=$object->oldline->$fieldparentid;	// When trigger is LINEXXX_UPDATE, only new value are set into $object, rest of old line is into $object->oldline
		}
		$parentobject->fetch($parentid);	// Note: The fetch_lines() is included into the fetch

		// $parentobject is the parent object of the line we have inserted (so an new invoice if event is a creation of an invoice for example).
		$lines = $parentobject->lines;

		// Get eco tax deee amount from extra field
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$optionsArray = $extrafields->fetch_name_optionals_label('product');

		// Loop on each lines of the object (the proposal, the sale order or the invoice)
		$nboflineswithpossibleecotax=0;
		foreach ($lines as $key => $line) {
			$ecocateg='NOCATEG';	// TODO For a future feature

			if ($line->special_code == 2) {				// This line is an already existing ecotax service line
				$idlineecotax[$ecocateg] = ($line->id ? $line->id : $line->rowid);
				$amountlineecotax_ht[$ecocateg] = $line->total_ht;
				$amountlineecotax_ttc[$ecocateg] = $line->total_ttc;
				$amountlineecotax_vat[$ecocateg] = (empty($line->total_vat) ? $line->total_tva : $line->total_vat);
				$keylineecotax[$ecocateg]=$key;

				$tmpecotaxline[$ecocateg] = $line;
				continue;
			}

			if (empty($line->fk_product))  {
				continue;	// This line is not a predefined product, so we suppose there is no eco tax deee
			}

			$ecotaxCalculationMode = getDolGlobalInt('ECOTAX_CALCULATION_MODE');	// By default, mode 0 = using the price on product.

			if ($line->special_code != 1 && $line->special_code != 2) {				// Discard shipping lines and ecotax lines
				if (preg_match('/_DELETE$/', $action) && $line->id == $object->id) {
                    continue;	// We discard the line that we delete
                }

				// If line is a common line, we add it to the $ecoamount array.
				$nboflineswithpossibleecotax++;

				if ($ecotaxCalculationMode == 0) {
					// Default calculation mode, we take the ecotax amount defined on the product
					$tmpproduct = new Product($this->db);
					$tmpproduct->fetch($line->fk_product);
					include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
					if (! empty($tmpproduct->array_options['options_ecotaxdeee']) && $line->qty) {
						if (empty($ecoamount[$ecocateg])) {	// To force init of var
							$ecoamount[$ecocateg] = 0;
						}

						$sign = 1;
						// If unit prices were negative (exemple for a credit note), we must use a negative ecotax too.
						if ($line->total_ht < 0 || $line->total_ttc < 0) {
							$sign = -1;
						}
						$ecoamount[$ecocateg] += ($sign * $tmpproduct->array_options['options_ecotaxdeee'] * $line->qty);
					}
				}

				if ($ecotaxCalculationMode == 1) {
					// Implement another mode
					$tmpproduct = new Product($this->db);
					$tmpproduct->fetch($line->fk_product);
					include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

					if (!empty($tmpproduct->array_options['options_ecotaxdeeecode']) && $line->qty) {
						$ecotaxCode = $tmpproduct->array_options['options_ecotaxdeeecode'];

						// search and get amount from code
						$sql = "SELECT amount FROM ".MAIN_DB_PREFIX."ecotaxdeee";
						$sql .= " WHERE code = '".$this->db->escape($ecotaxCode)."'";
						$resql = $this->db->query($sql);
						if ($resql) {
							if ($this->db->num_rows($resql) > 0) {
								$obj = $this->db->fetch_object($resql);
								$ecotaxAmount = $obj->amount;
								if (empty($ecoamount[$ecocateg])) {	// To force init of var
									$ecoamount[$ecocateg] = 0;
								}

								$sign = 1;
								// If unit prices were negative (exemple for a credit note), we must use a negative ecotax too.
								if ($line->total_ht < 0 || $line->total_ttc < 0) {
									$sign = -1;
								}

								$ecoamount[$ecocateg] += ($sign * $ecotaxAmount * $line->qty);
							}
						}
					} else {
						dol_syslog(get_class($this).'::checkCode: code not founded');
					}

				}
			}
		}

		// Delete existing ecotax lines, so we can reinsert it later after the new calculation
		foreach ($tmpecotaxline as $ecocateg => $value) {
			if (empty($ecoamount[$ecocateg])) {
				if (is_object($tmpecotaxline[$ecocateg])) {
					$result = $tmpecotaxline[$ecocateg]->delete($user);
				}
			}
		}


		$seller = $mysoc;

		$buyer=new Societe($this->db);
		$buyer->fetch($parentobject->socid);

		// Update/insert ecotax defined into the $ecoamount array
		$result=0;
		$error='';
		foreach ($ecoamount as $ecocateg => $value) {
			// If the ecotax line was already existing for $ecocateg
			if (!empty($tmpecotaxline[$ecocateg]) && is_object($tmpecotaxline[$ecocateg]) && $idlineecotax[$ecocateg] > 0) {
				if ($ecoamount[$ecocateg]) {
					// Update line
					$tmpecotaxline[$ecocateg]->oldline = dol_clone($tmpecotaxline[$ecocateg], 0);
					$tmpecotaxline[$ecocateg]->qty = 1;
					$tmpecotaxline[$ecocateg]->subprice = $ecoamount[$ecocateg];

					$localtaxarray = getLocalTaxesFromRate($tmpecotaxline[$ecocateg]->tva_tx, 0, $buyer, $seller);
					$tmparray = calcul_price_total($tmpecotaxline[$ecocateg]->qty, $tmpecotaxline[$ecocateg]->subprice, $tmpecotaxline[$ecocateg]->remise_percent, $tmpecotaxline[$ecocateg]->tva_tx, 0, 0, 0, 'HT', $tmpecotaxline[$ecocateg]->info_bits, isset($tmpecotaxline[$ecocateg]->type) ? $tmpecotaxline[$ecocateg]->type : $tmpecotaxline[$ecocateg]->product_type, $seller, $localtaxarray);

					$tmpecotaxline[$ecocateg]->subprice_ttc = $tmparray[5];

					$tmpecotaxline[$ecocateg]->total_ht = $tmparray[0];
					$tmpecotaxline[$ecocateg]->total_tva = $tmparray[1];
					$tmpecotaxline[$ecocateg]->total_ttc = $tmparray[2];
					$tmpecotaxline[$ecocateg]->total_localtax1 = $tmparray[9];
					$tmpecotaxline[$ecocateg]->total_localtax2 = $tmparray[10];

					// For multicurrency fields
					$tmpecotaxline[$ecocateg]->multicurrency_subprice = $tmparray[19];
					$tmpecotaxline[$ecocateg]->multicurrency_subprice_ttc = $tmparray[21];
					$tmpecotaxline[$ecocateg]->multicurrency_total_ht = $tmparray[16];
					$tmpecotaxline[$ecocateg]->multicurrency_total_tva = $tmparray[17];
					$tmpecotaxline[$ecocateg]->multicurrency_total_ttc = $tmparray[18];
					$tmpecotaxline[$ecocateg]->multicurrency_total_localtax1 = $tmparray[25];
					$tmpecotaxline[$ecocateg]->multicurrency_total_localtax2 = $tmparray[26];

					if ($parentobject->table_element == 'facture')  $result = $tmpecotaxline[$ecocateg]->update($user, 0);
					if ($parentobject->table_element == 'commande') $result = $tmpecotaxline[$ecocateg]->update($user, 0);
					if ($parentobject->table_element == 'propal')   $result = $tmpecotaxline[$ecocateg]->update($user, 0);
					//if ($parentobject->table_element == 'order_supplier')   $result = $tmpecotaxline[$ecocateg]->update($user, 0);
					if ($parentobject->table_element == 'invoice_supplier') $result = $tmpecotaxline[$ecocateg]->update($user, 0);

					// Now update the buy_price_ht (because this is not included into the update() method we have just run)
					if ($result > 0) {
						$tmpecotaxline[$ecocateg]->buy_price_ht = $ecoamount[$ecocateg];
						$sql = '';
						if ($parentobject->table_element == 'facture')  $sql = 'UPDATE '.MAIN_DB_PREFIX.$parentobject->table_element_line.' SET buy_price_ht = '.((float) $tmpecotaxline[$ecocateg]->buy_price_ht).' WHERE rowid = '.((int) $tmpecotaxline[$ecocateg]->id);
						if ($parentobject->table_element == 'commande') $sql = 'UPDATE '.MAIN_DB_PREFIX.$parentobject->table_element_line.' SET buy_price_ht = '.((float) $tmpecotaxline[$ecocateg]->buy_price_ht).' WHERE rowid = '.((int) $tmpecotaxline[$ecocateg]->id);
						if ($parentobject->table_element == 'propal')   $sql = 'UPDATE '.MAIN_DB_PREFIX.$parentobject->table_element_line.' SET buy_price_ht = '.((float) $tmpecotaxline[$ecocateg]->buy_price_ht).' WHERE rowid = '.((int) $tmpecotaxline[$ecocateg]->id);
						//if ($parentobject->table_element == 'order_supplier')   $sql=
						if ($parentobject->table_element == 'invoice_supplier') $sql = 'UPDATE '.MAIN_DB_PREFIX.$parentobject->table_element_line.' SET buy_price_ht = '.((float) $tmpecotaxline[$ecocateg]->buy_price_ht).' WHERE rowid = '.((int) $tmpecotaxline[$ecocateg]->id);
						if ($sql) {
							$resql = $this->db->query($sql);
							if (!$resql) {
								$error = $this->db->lasterror();
							}
						}
					}
				} else {
					$result = $tmpecotaxline[$ecocateg]->delete($user);
				}

				if ($result <= 0) {
					$error = $tmpecotaxline[$ecocateg]->error;
				}
			} else { // If then ecotax line did not yet exists for $ecocateg and we need it
				$product_id = getDolGlobalInt('WEEE_PRODUCT_ID');

				// Insert line
				$rang = count($lines) + 1;
				$special_code = 2;
				$txtva = 0;
				$localtax1 = 0;
				$localtax2 = 0;
				if (!getDolGlobalString('WEEE_DISABLE_VAT_ON_ECOTAX')) {	// This option should not be set.
					$txtva = get_default_tva($seller, $buyer, $product_id, 0);	// Get default VAT Eco Tax product (if defined) or for generic product id=0 (highest vat rate) if no predefined product set for Eco Tax line
					//$localtax1 = get_default_localtax($seller, $buyer, 1, $product_id);
					//$localtax2 = get_default_localtax($seller, $buyer, 2, $product_id);
					dol_syslog("The vat rate we get for the product for ecotax is ".$txtva, LOG_DEBUG);
				}

				include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

				// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $price_base_type='HT', $pu_ttc=0, $info_bits=0, $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=0, $pa_ht=0, $label='',$date_start='', $date_end='',$array_options=0)
				if ($parentobject->table_element == 'propal')   {
					$result=$parentobject->addline($desc, $ecoamount[$ecocateg], 1, $txtva, $localtax1, $localtax2, $product_id, 0, 'HT', 0, 0, 1, $rang, $special_code, '', 0, $ecoamount[$ecocateg], null, '', '', 0);
				}
				// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $info_bits=0, $fk_remise_except=0, $price_base_type='HT', $pu_ttc=0, $date_start='', $date_end='', $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_options=0)
				if ($parentobject->table_element == 'commande') {
					$result=$parentobject->addline($desc, $ecoamount[$ecocateg], 1, $txtva, $localtax1, $localtax2, $product_id, 0, 0, 0, 'HT', '', '', '', 1, $rang, $special_code, '', 0, $ecoamount[$ecocateg], null, 0, 0);
				}
				// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits=0, $fk_remise_except='', $price_base_type='HT', $pu_ttc=0, $type=0, $rang=-1, $special_code=0, $origin='', $origin_id=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_options=0)
				if ($parentobject->table_element == 'facture')  {
					$result=$parentobject->addline($desc, $ecoamount[$ecocateg], 1, $txtva, $localtax1, $localtax2, $product_id, 0, '', '', 0, 0, '', 'HT', 0, 1, $rang, $special_code, '', 0, 0, null, $ecoamount[$ecocateg], '', 0);
				}
				// TODO order_supplier and invoice_supplier

				//var_dump($result);exit;
				if ($result <= 0) {
					$error = $parentobject->error;
				}
			}
		}

		if (! $error) {
			return 1;
		} else {
			$this->error = $error;
			$this->errors[] = $error;
			dol_syslog("Trigger '".$this->name."' in action '".$action."' ERROR ".$error, LOG_ERR);
			return -1;
		}
	}


	/**
	 * See if the product is in the ecotax category.
	 * Stop and return 0 if the product is not in it.
	 *
	 * @param	string	$ecocat			Category
	 * @param	int		$product_id		Product id
	 */
	function _is_in_cat($ecocat, $product_id)
	{
		require_once DOL_DOCUMENT_ROOT."/categories/categorie.class.php";

		if (!isset($product_id) || empty($product_id)) {
			return 0;
		}

		$c = new Categorie($this->db);
		$cats = array();
		$cats = $c->containing($product_id, 0);
		$found=0;
		if (count($cats)==0) return 0;
		foreach ($cats as $cat) {
			if ($cat->label===$ecocat) {
				$found=1;
				break;
			}
		}
		if ($found==0) return 0;

		return 1;
	}
}
