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
 *  Classe des fonctions triggers des actions personalisees du module
 */
class InterfaceEcotaxdee
{
	var $db;
	var $error;

	/**
	 *   Constructeur.
	 *   
	 *   @param	DoliDB	$db      Handler d'acces base
	 */
	function InterfaceEcotax($db)
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

		if (empty($conf->ecotaxdee->enabled)) return 0;

		if (! empty($conf->global->ECOTAXDEE_USE_ON_CUSTOMER_ORDER))
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
		if (! empty($conf->global->ECOTAXDEE_USE_ON_PROPOSAL))
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
		if (! empty($conf->global->ECOTAXDEE_USE_ON_CUSTOMER_INVOICE))
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
	 */
	function _add_replace_ecotax($action,$object,$user,$langs,$conf)
	{
		global $mysoc;
		
		// The next 3 parameters can be replaced at will:
		$desc = $langs->trans("Ecotax"); // the description on line
		//$ecocat = empty($conf->global->ECOTAXDEEE_CATEGORY_REF)?"Ecotax":$conf->global->ECOTAXDEEE_CATEGORY_REF; // the category products must be in, for ecotax to apply

		// Add a line EcoTax automatically
		dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->fk_commande);

		/*
		 * Calculate the ecotax deee.
		 */
		$ecoamount = 0;
		$fieldid=$object->fk_element;
		$parentid=$object->$fieldid;

		$lines=$object->lines;
		foreach($lines as $line)
		{
			var_dump($line);
			if ($line->special_code != 1 && $line->special_code != 2)	// Discard shipping line and ecotax lines
			{
				$tmpproduct=new Product($this->db);
				$tmpproduct->fetch($line->fk_product);
				
				if (1 == 1)
				{
					// If version <= 3.5.0, get eco tax deee amount from extra field
					$optionsArray=array();
					$tmpproduct->fetch_optionals($line->fk_product, $optionsArray);
				
					var_dump($optionsArray);
					$ecoamount += $optionsArray['ecotax'];
				}
				else
				{
					// Get it from product desc
					$ecoamount += $tmpproduct->weee;
				}
			}
		}

		/*
		$sql = "SELECT fd.qty, fd.subprice as pu, fd.remise_percent as remise_percent_line, fd.product_type, fd.fk_product, fd.info_bits,";
		$sql.= " fd.tva_tx, fd.localtax1_tx, fd.localtax1_type, fd.localtax2_tx, fd.localtax2_type,";
		$sql.= " fd.total_ht, fd.total_ttc";
		$sql.= " FROM ".MAIN_DB_PREFIX.$object->table_element_line." as fd";
		$sql.= " WHERE fd.".$object->fk_element." = '".$parentid."'";
		$sql.= " AND special_code NOT INT (1,2)";	// Discard shipping line and ecotax lines 

		dol_syslog(get_class($this).'::_add_replace_ecotax', LOG_DEBUG);
		$resql = $this->db->query($sql) ;

		if ($resql)
		{
			while ( $obj = $this->db->fetch_object($resql) )
			{
				$tmparray=calcul_price_total($obj->qty, $obj->pu, $obj->remise_percent_line, $obj->txt_tx, $obj->localtax1_rate, $obj->localtax2_rate, 'HT', $obj->info_bits, $obj->product_type);
				$prod_id=$obj->fk_product;

				if ($this->_is_in_cat($ecocat, $prod_id))
				{
					//$total_withtax_withoutdiscount=$tmparray[8];
					
					$eco += 10;
				}
			}
		}
		else
		{
			dol_syslog("Trigger '".$this->name."' in action '$action' [2] SQL ERROR ");
		}
		*/

		/*
		 * Detect if line already exists
		 */
		$exists=0;
		$idline=0;
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$object->table_element_line;
		$sql.= " WHERE ".$object->fk_element." = '".$parentid."'";
		$sql.= " AND special_code = 2";

		dol_syslog(get_class($this).'::_add_replace_ecotax', LOG_DEBUG);
		$resql = $this->db->query($sql) ;

		if ($resql)
		{
			$exists = $this->db->num_rows($resql);
			if ($exists)
			{
				$obj=$this->db->fetch_object($resql);
				$idline=$obj->rowid;
			}
			$this->db->free($resql);
		}
		else
		{
			$this->error = $this->db->lasterror;
			$this->errors[] = $this->error;
			dol_syslog("Trigger '".$this->name."' in action '$action' [3] SQL ERROR ".$this->error, LOG_ERR);
		}

		// Update/insert ecotax
		$tmpobject=null;
		if ($object->table_element == 'facture')
		{
			$tmpobject=new FactureLigne($db);
		}
		if ($object->table_element == 'commande')
		{
			$tmpobject=new OrderLine($db);
		}
		if ($object->table_element == 'propal')
		{
			$tmpobject=new PropaleLigne($db);
		}
		if ($idline > 0)
		{
			$result=0;
			$tmpobject->fetch($idline);
			if ($ecoamount)
			{
				// Update line
				$tmpobject->qty=1;
				$tmpobject->subprice=$ecoamount;
			
				if ($object->table_element == 'facture') $result=$tmpobject->update($user,0);
				if ($object->table_element == 'commande') $result=$tmpobject->update(0);
				if ($object->table_element == 'propal') $result=$tmpobject->update(0);
			}
			else
			{
				$result=$tmpobject->delete();
			}
			
			if ($result > 0)
			{
				return 1;
			}
			else
			{
				$this->error = 'eee';
				$this->errors[] = $this->error;
				dol_syslog("Trigger '".$this->name."' in action '$action' [4] ERROR ".$this->error, LOG_ERR);
				return -1;
			}
		}
		else
		{
			$seller=$mysoc;
			$buyer=new Societe($db);
			$buyer->fetch($object->fk_soc);
			
			// Insert line
			$rang = $exists + 1;
			
			$special_code = 2;
			$txtva=get_default_tva($seller, $buyer, 0, 0);

			$result=$object->addline($desc, $ecoamount, 1, $txtva, 0, 0, 0, 0, '', '', 0, 0, '', 'HT', 0, 1, $rang, $special_code, '', 0, 0, null, 0, '', 0);

			if ($result > 0)
			{
				return 2;
			}
			else
			{
				$this->error = 'eee';
				$this->errors[] = $this->error;
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
