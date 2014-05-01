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
 *  \file       ecotaxdeee/core/triggers/interface_modCommande_Ecotax.class.php
 *  \ingroup    core
 *  \brief      Add Ecotax for products into specific category
 */


/**
 *  Class of triggers for module Ecotaxdeee
 */
class InterfaceEcotaxdeee
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

		$this->name = preg_replace('/^Interface/i','',get_class($this));
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
	 *      D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
	 *
	 *      @param	string		$action     Code of event
	 *      @param 	Action		$object     Objet concerne
	 *      @param  User		$user       Objet user
	 *      @param  Translate	$lang       Objet lang
	 *      @param  Conf		$conf       Objet conf
	 *      @return int         			<0 if KO, 0 if nothing is done, >0 if OK
	 */
	function run_trigger($action, $object, $user, $langs, $conf) {

		// Création / Mise à jour / Suppression d'un évènement dans Google contact

		if (empty($conf->ecotaxdeee->enabled)) return 0;
		if (isset($object->special_code) && $object->special_code == 2) return 0;			// To avoid infinite loop

		if (! empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_ORDER))
		{
			if ($action == 'LINEORDER_INSERT')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEORDER_UPDATE' || $action == 'LINEORDER_MODIFY')
			{
					return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEORDER_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}
		if (! empty($conf->global->ECOTAXDEEE_USE_ON_PROPOSAL))
		{
			if ($action == 'LINEPROPAL_INSERT')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEPROPAL_UPDATE' || $action == 'LINEPROPAL_MODIFY')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEPROPAL_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}
		if (! empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE))
		{
			if ($action == 'LINEBILL_INSERT')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEBILL_UPDATE' || $action == 'LINEBILL_MODIFY')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEBILL_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}

		// Renvoi 0 car aucune action de faite
		return 0;
	}


	/**
	 * Calculate ecotax
	 * $object is a line of object (->element, ->table_element must be defined)
	 */
	function _add_replace_ecotax($action,$object,$user,$langs,$conf)
	{
		global $mysoc;

		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// The next 3 parameters can be replaced at will:
		$desc = empty($conf->global->ECOTAXDEEE_LABEL_LINE)?$langs->trans("EcoTaxDeee"):$conf->global->ECOTAXDEEE_LABEL_LINE; // label for ecotax
		//$ecocat = empty($conf->global->ECOTAXDEEE_CATEGORY_REF)?"Ecotax":$conf->global->ECOTAXDEEE_CATEGORY_REF; // the category products must be in, for ecotax to apply

		// Add a line EcoTax automatically
		dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id." rowid=".$object->rowid);

		$idlineecotax=array();
		$amountlineecotax_ht=array();
		$amountlineecotax_ttc=array();
		$amountlineecotax_vat=array();
		$keylineecotax=array();
		$tmpecotaxline=array();

		if ($object->special_code == 2 && in_array($action,array('LINEORDER_DELETE','LINEPROPAL_DELETE','LINEBILL_DELETE'))) return 0;

		/*
		 * Calculate the EcoTax DEEE and try to find idlineecotax
		 */
		$ecoamount = array();
		$fieldparentid='';
		$parentobject=null;
		if ($object->element == 'facturedet' || get_class($object) == 'FactureLigne')
		{
			$fieldparentid='fk_facture';
			$parentobject=new Facture($this->db);
		}
		if ($object->element == 'propaldet' || get_class($object) == 'PropaleLigne')
		{
			$fieldparentid='fk_propal';
			$parentobject=new Propal($this->db);
		}
		if ($object->element == 'commandedet' || get_class($object) == 'OrderLine')
		{
			$fieldparentid='fk_commande';
			$parentobject=new Commande($this->db);
		}
		if (empty($fieldparentid))
		{
			dol_syslog('Object not supported', LOG_WARNING);
			return;
		}
		$parentid=$object->$fieldparentid;
		if (empty($parentid)) $parentid=$object->oldline->$fieldparentid;	// When trigger is LINEXXX_UPDATE, only new value are set into $object, rest of old line is into $object->oldline
		$parentobject->fetch($parentid);	// fetch_lines included into fetch

		// If we are creating an object from an other one, we forget adding eco tax.
		if (! empty($_POST['origin']) && ! empty($_POST['originid']))
		{
			return 0;
		}

		$lines=$parentobject->lines;

		// To work with version <= 3.6.0, get eco tax deee amount from extra field
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $optionsArray = $extrafields->fetch_name_optionals_label('product');

        $nboflineswithpossibleecotax=0;
		foreach($lines as $key => $line)
		{
			$ecocateg='NOCATEG';

			if ($line->special_code == 2)				// This line is an already existing service line ecotax
			{
				$idlineecotax[$ecocateg]=$line->rowid;
				$amountlineecotax_ht[$ecocateg]=$line->total_ht;
				$amountlineecotax_ttc[$ecocateg]=$line->total_ttc;
				$amountlineecotax_vat[$ecocateg]=$line->total_vat;
				$keylineecotax[$ecocateg]=$key;
				$tmpecotaxline[$ecocateg]=$line;
				continue;
			}

			if (empty($line->fk_product)) continue;	// This line is not a predefined product, so we suppose there is no eco tax deee

			if ($line->special_code != 1 && $line->special_code != 2)	// Discard shipping line and ecotax lines
			{
				$nboflineswithpossibleecotax++;

				$tmpproduct=new Product($this->db);
				$tmpproduct->fetch($line->fk_product);
				include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
				if (versioncompare(versiondolibarrarray(),array(3,6,-3)) < 999)	// <0 to test if we are 3.6.0 alpha or -
				{
					// If version < 3.6.0, get eco tax deee amount from extra field
					$result=$tmpproduct->fetch_optionals($tmpproduct->id, $optionsArray);
					if (! empty($tmpproduct->array_options['options_ecotaxdeee']) && $line->qty) $ecoamount[$ecocateg] += ($tmpproduct->array_options['options_ecotaxdeee'] * $line->qty);
				}
				else
				{
					// Get it from product desc
					if (! empty($tmpproduct->ecotaxdeee) && $line->qty) $ecoamount[$ecocateg] += ($tmpproduct->ecotaxdeee * $line->qty);
				}
			}
		}

		// Delete ecotax
		foreach ($tmpecotaxline as $ecocateg => $value)
		{
			if (empty($ecoamount[$ecocateg]))
			{
				// Do nothing
				if (is_object($tmpecotaxline[$ecocateg])) $result=$tmpecotaxline[$ecocateg]->delete();
			}
		}

		// Update/insert ecotax
		$result=0;
		$error='';
		foreach ($ecoamount as $ecocateg => $value)
		{
			if (is_object($tmpecotaxline[$ecocateg]) && $idlineecotax[$ecocateg] > 0)	// If ecotax line already exists for ecocateg
			{
				$seller=$mysoc;

				if ($ecoamount[$ecocateg])
				{
					// Update line
					$tmpecotaxline[$ecocateg]->oldline=dol_clone($tmpecotaxline[$ecocateg]);
					$tmpecotaxline[$ecocateg]->qty=1;
					$tmpecotaxline[$ecocateg]->subprice=$ecoamount[$ecocateg];
					$remise_percent_ligne=0;

					$localtaxarray=getLocalTaxesFromRate($tmpecotaxline[$ecocateg]->tva_tx, 0, $seller);
					$tmparray=calcul_price_total($tmpecotaxline[$ecocateg]->qty, $tmpecotaxline[$ecocateg]->subprice, $tmpecotaxline[$ecocateg]->remise_percent, $tmpecotaxline[$ecocateg]->tva_tx, 0, 0, 0, 'HT', $tmpecotaxline[$ecocateg]->info_bits, $tmpecotaxline[$ecocateg]->type, $seller, $localtaxarray);

					$tmpecotaxline[$ecocateg]->total_ht = $tmparray[0];
					$tmpecotaxline[$ecocateg]->total_tva = $tmparray[1];
					$tmpecotaxline[$ecocateg]->total_ttc = $tmparray[2];
					$tmpecotaxline[$ecocateg]->total_localtax1 = $tmparray[9];
					$tmpecotaxline[$ecocateg]->total_localtax2 = $tmparray[10];

					if ($parentobject->table_element == 'facture')  $result=$tmpecotaxline[$ecocateg]->update($user,0);
					if ($parentobject->table_element == 'commande') $result=$tmpecotaxline[$ecocateg]->update(0);
					if ($parentobject->table_element == 'propal')   $result=$tmpecotaxline[$ecocateg]->update(0);
				}
				else
				{
					$result=$tmpecotaxline[$ecocateg]->delete();
				}

				if ($result <= 0)
				{
					$error = $tmpecotaxline[$ecocateg]->error;
				}
			}
			else	// If ecotax line does not yet exists for ecocateg and we need it
			{
				$seller=$mysoc;

				$buyer=new Societe($this->db);
				$buyer->fetch($parentobject->socid);

				// Insert line
				$rang = count($lines) + 1;
				$special_code = 2;
				$txtva=get_default_tva($seller, $buyer, 0, 0);	// Get default VAT for generic product id=0 (highest vat rate)

				include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

				if (versioncompare(versiondolibarrarray(),array(3,5,-3)) >= 0)	// We are 3.5.0 alpha or +
				{
					// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits=0, $fk_remise_except='', $price_base_type='HT', $pu_ttc=0, $type=0, $rang=-1, $special_code=0, $origin='', $origin_id=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_option=0)
					if ($parentobject->table_element == 'facture')  $result=$parentobject->addline($desc, $ecoamount[$ecocateg], 1, $txtva, 0, 0, 0, 0, '', '', 0, 0, '', 'HT', 0, 1, $rang, $special_code, '', 0, 0, null, 0, '', 0);
					// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $info_bits=0, $fk_remise_except=0, $price_base_type='HT', $pu_ttc=0, $date_start='', $date_end='', $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_option=0)
					if ($parentobject->table_element == 'commande') $result=$parentobject->addline($desc, $ecoamount[$ecocateg], 1, $txtva, 0, 0, 0, 0, 0, 0, 'HT', '', '', '', 1, $rang, $special_code, '', 0, 0, null, 0, 0);
					// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $price_base_type='HT', $pu_ttc=0, $info_bits=0, $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=0, $pa_ht=0, $label='',$date_start='', $date_end='',$array_option=0)
					if ($parentobject->table_element == 'propal')   $result=$parentobject->addline($desc, $ecoamount[$ecocateg], 1, $txtva, 0, 0, 0, 0, 'HT', 0, 0, 1, $rang, $special_code, '', 0, 0, null, '', '', 0);
				}
				else
				{
					// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits=0, $fk_remise_except='', $price_base_type='HT', $pu_ttc=0, $type=0, $rang=-1, $special_code=0, $origin='', $origin_id=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_option=0)
					if ($parentobject->table_element == 'facture')  $result=$parentobject->addline($parentobject->id, $desc, $ecoamount[$ecocateg], 1, $txtva, 0, 0, 0, 0, '', '', 0, 0, '', 'HT', 0, 1, $rang, $special_code, '', 0, 0, null, 0, '', 0);
					// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $info_bits=0, $fk_remise_except=0, $price_base_type='HT', $pu_ttc=0, $date_start='', $date_end='', $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_option=0)
					if ($parentobject->table_element == 'commande') $result=$parentobject->addline($parentobject->id, $desc, $ecoamount[$ecocateg], 1, $txtva, 0, 0, 0, 0, 0, 0, 'HT', '', '', '', 1, $rang, $special_code, '', 0, 0, null, 0, 0);
					// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $price_base_type='HT', $pu_ttc=0, $info_bits=0, $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=0, $pa_ht=0, $label='',$date_start='', $date_end='',$array_option=0)
					if ($parentobject->table_element == 'propal')   $result=$parentobject->addline($parentobject->id, $desc, $ecoamount[$ecocateg], 1, $txtva, 0, 0, 0, 0, 'HT', 0, 0, 1, $rang, $special_code, '', 0, 0, null, '', '', 0);
				}

				//var_dump($result);exit;
				if ($result <= 0)
				{
					$error = $parentobject->error;
				}
			}
		}

		if (! $error)
		{
			return 1;
		}
		else
		{
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
		require_once(DOL_DOCUMENT_ROOT."/categories/categorie.class.php");

		if (!isset($product_id) || empty($product_id))
		{
			return 0;
		}

		$c = new Categorie($this->db);
		$cats = array();
		$cats = $c->containing($product_id,0);
		$found=0;
		if (sizeof($cats)==0) return 0;
		foreach ($cats as $cat)
		{
			if ($cat->label===$ecocat)
			{
				$found=1;
				break;
			}
		}
		if ($found==0) return 0;

		return 1;
	}
}
?>
