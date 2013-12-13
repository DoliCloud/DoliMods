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
		if ($object->special_code == 2) return 0;			// To avoid infinite loop
		
		if (! empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_ORDER))
		{
			if ($action == 'LINEORDER_INSERT')
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
		
		// The next 3 parameters can be replaced at will:
		$desc = $langs->trans("Ecotax"); // the description on line
		//$ecocat = empty($conf->global->ECOTAXDEEE_CATEGORY_REF)?"Ecotax":$conf->global->ECOTAXDEEE_CATEGORY_REF; // the category products must be in, for ecotax to apply

		// Add a line EcoTax automatically
		dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

		$idlineecotax=0;
		$keylineecotax=0;
		$tmpline=null;
		
		/*
		 * Calculate the EcoTax DEEE and try to find idlineecotax
		 */
		$ecoamount = 0;
		$fieldparentid='';
		$parentobject=null;
		if ($object->element == 'facturedet')  
		{
			$fieldparentid='fk_facture';
			$parentobject=new Facture($this->db);
		}
		if ($object->element == 'propaldet')   
		{
			$fieldparentid='fk_propal';
			$parentobject=new Propal($this->db);
		}
		if ($object->element == 'commandedet') 
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

		$parentobject->fetch($parentid);	// TODO fetch_lines ?
		
		$lines=$parentobject->lines;

		// To work with version <= 3.6.0, get eco tax deee amount from extra field
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $optionsArray = $extrafields->fetch_name_optionals_label('product');

        $nboflineswithoutecotax=0;
		foreach($lines as $key => $line)
		{
			if ($line->special_code == 2) 
			{
				$idlineecotax=$line->id;
				$keylineecotax=$key;
				$tmpline=$line;
				continue;
			}

			$nboflineswithoutecotax++;
			
			if ($line->special_code != 1 && $line->special_code != 2)	// Discard shipping line and ecotax lines
			{
				$tmpproduct=new Product($this->db);
				$tmpproduct->fetch($line->fk_product);

				if (1 == 1)
				{
					// If version <= 3.6.0, get eco tax deee amount from extra field
					$result=$tmpproduct->fetch_optionals($tmpproduct->id, $optionsArray);
					$ecoamount += $tmpproduct->array_options['options_ecotaxdeee'];
				}
				else
				{
					// Get it from product desc
					$ecoamount += $tmpproduct->ecotaxdeee;
				}
			}
		}
	
		// Update/insert ecotax
		if ($nboflineswithoutecotax == 0)
		{
			// Do nothing	
		}
		else if (is_object($tmpline) && $idlineecotax > 0)	// If ecotax line already exists
		{
			$result=0;
			if ($ecoamount)
			{
				// Update line
				$tmpline->qty=1;
				$tmpline->subprice=$ecoamount;
			
				if ($parentobject->table_element == 'facture')  $result=$tmpline->update($user,0);
				if ($parentobject->table_element == 'commande') $result=$tmpline->update(0);
				if ($parentobject->table_element == 'propal')   $result=$tmpline->update(0);
			}
			else
			{
				$result=$tmpline->delete();
			}
			
			if ($result > 0)
			{
				return 1;
			}
			else
			{
				$this->error = $tmpline->lasterror;
				$this->errors[] = $tmpline->lasterror;
				dol_syslog("Trigger '".$this->name."' in action '$action' [4] ERROR ".$this->error, LOG_ERR);
				return -1;
			}
		}
		else
		{
			$seller=$mysoc;
			
			$buyer=new Societe($db);
			$buyer->fetch($parentobject->fk_soc);
			
			// Insert line
			$rang = $exists + 1;
			
			$special_code = 2;
			$txtva=get_default_tva($seller, $buyer, 0, 0);	// Get default VAT for generic product id=0 (highest vat rate)
var_dump($txtva);exit;	
			// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits=0, $fk_remise_except='', $price_base_type='HT', $pu_ttc=0, $type=0, $rang=-1, $special_code=0, $origin='', $origin_id=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_option=0)
			if ($parentobject->table_element == 'facture')  $result=$parentobject->addline($desc, $ecoamount, 1, $txtva, 0, 0, 0, 0, '', '', 0, 0, '', 'HT', 0, 1, $rang, $special_code, '', 0, 0, null, 0, '', 0);
			// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $info_bits=0, $fk_remise_except=0, $price_base_type='HT', $pu_ttc=0, $date_start='', $date_end='', $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_option=0)
			if ($parentobject->table_element == 'commande') $result=$parentobject->addline($desc, $ecoamount, 1, $txtva, 0, 0, 0, 0, 0, 0, 'HT', '', '', '', 1, $rang, $special_code, '', 0, 0, null, 0, 0);
			// addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $price_base_type='HT', $pu_ttc=0, $info_bits=0, $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=0, $pa_ht=0, $label='',$date_start='', $date_end='',$array_option=0)
			if ($parentobject->table_element == 'propal')   $result=$parentobject->addline($desc, $ecoamount, 1, $txtva, 0, 0, 0, 0, 'HT', 0, 0, 1, $rang, $special_code, '', 0, 0, null, '', '', 0);

			//var_dump($result);exit;

			if ($result > 0)
			{
				return 2;
			}
			else
			{
				$this->error = $parentobject->error;
				$this->errors[] = $parentobject->error;
				dol_syslog("Trigger '".$this->name."' in action '$action' [5] ERROR ".$this->error, LOG_ERR);
				return -2;
			}
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
