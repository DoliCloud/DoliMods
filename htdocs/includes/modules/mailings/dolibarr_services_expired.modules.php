<?php
/* Copyright (C) 2005-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This file is an example to follow to add your own email selector inside
 * the Dolibarr email tool.
 * Follow instructions given in README file to know what to change to build
 * your own emailing list selector.
 * Code that need to be changed in this file are marked by "CHANGE THIS" tag.
 */

include_once DOL_DOCUMENT_ROOT.'/includes/modules/mailings/modules_mailings.php';


// CHANGE THIS: Class name must be called mailing_xxx with xxx=name of your selector
class mailing_dolibarr_services_expired extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	var $name='dolibarr_services_expired';
	// CHANGE THIS: Put here a description of your selector module
	var $desc='Tiers avec service expiré';
	// CHANGE THIS: Set to 1 if selector is available for admin users only
	var $require_admin=0;

	var $require_module=array();
	var $picto='';
	var $db;


	// CHANGE THIS: Constructor name must be called mailing_xxx with xxx=name of your selector
	function mailing_dolibarr_services_expired($DB)
	{
		$this->db=$DB;
	}


	/**
	*    \brief      This is the main function that returns the array of emails
	*    \param      mailing_id    Id of mailing. No need to use it.
	*    \param      filterarray   If you used the formFilter function. Empty otherwise.
	*    \return     int           <0 if error, number of emails added if ok
	*/
	function add_to_target($mailing_id,$filtersarray=array())
	{
		$target = array();

		// CHANGE THIS
		// ----- Your code start here -----

		$cibles = array();
		$j = 0;

		$product='';
	    foreach($filtersarray as $key)
        {
            if ($key == '0') return "Error: You must choose a filter";
            if ($key == '1') $product= "PUBADRESCHIEN";
            if ($key == '2') $product= "PUBADRESCHAT";
            if ($key == '3') $product= "HEBERGDOMWEB";
        }

		// La requete doit retourner: id, email, name
		$sql = " select s.rowid, s.email, s.nom as name, cd.rowid as cdid, cd.date_ouverture, cd.date_fin_validite, cd.fk_contrat";
		$sql.= " from ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."contrat as c,";
		$sql.= " ".MAIN_DB_PREFIX."contratdet as cd, ".MAIN_DB_PREFIX."product as p";
		$sql.= " where s.rowid = c.fk_soc AND cd.fk_contrat = c.rowid AND s.email != ''";
		$sql.= " AND cd.statut= 4 AND cd.fk_product=p.rowid AND p.ref = '".$product."'";
		$sql.= " AND cd.date_fin_validite < '".$this->db->idate(gmmktime())."'";
		$sql.= " ORDER BY s.email";

		// Stocke destinataires dans cibles
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			dolibarr_syslog("dolibarr_services_expired.modules.php: mailing $num cibles trouv�es");

			$old = '';
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				if ($old <> $obj->email)
				{
					$cibles[$j] = array(
					'email' => $obj->email,
					'name' => $obj->name,
					'id' => $obj->id,
					'other' => dolibarr_print_date($this->db->jdate($obj->date_ouverture),'day').';'.dolibarr_print_date($this->db->jdate($obj->date_fin_validite),'day').';'.$obj->fk_contrat.';'.$obj->cdid
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		}
		else
		{
			dolibarr_syslog($this->db->error());
			$this->error=$this->db->error();
			return -1;
		}

		// ----- Your code end here -----

		return parent::add_to_target($mailing_id, $cibles);
	}


	/**
	*		\brief		On the main mailing area, there is a box with statistics.
	*					If you want to add a line in this report you must provide an
	*					array of SQL request that returns two field:
	*					One called "label", One called "nb".
	*		\return		array
	*/
	function getSqlArrayForStats()
	{
		// CHANGE THIS: Optionnal

		//var $statssql=array();
		//$this->statssql[0]="SELECT field1 as label, count(distinct(email)) as nb FROM mytable WHERE email IS NOT NULL";

		return array();
	}


	/**
	*		\brief		Return here number of distinct emails returned by your selector.
	*					For example if this selector is used to extract 500 different
	*					emails from a text file, this function must return 500.
	*		\return		int
	*/
	function getNbOfRecipients($filter=1,$option='')
	{
		// CHANGE THIS: Optionnal

        // Example: return parent::getNbOfRecipients("SELECT count(*) as nb from dolibarr_table");
		// Example: return 500;
		$sql = " select count(*) as nb";
		$sql.= " from ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."contrat as c,";
		$sql.= " ".MAIN_DB_PREFIX."contratdet as cd, ".MAIN_DB_PREFIX."product as p";
		$sql.= " where s.rowid = c.fk_soc AND cd.fk_contrat = c.rowid AND s.email != ''";
		$sql.= " AND cd.statut= 4 AND cd.fk_product=p.rowid AND p.ref in ('PUBADRESCHIEN','PUBADRESCHAT','HEBERGDOMWEB')";
		$sql.= " AND cd.date_fin_validite < '".$this->db->idate(gmmktime())."'";
		$sql.= " ORDER BY s.email";
		//print $sql;
		$a=parent::getNbOfRecipients($sql);

		return $a;
	}

	/**
	*      \brief      This is to add a form filter to provide variant of selector
	*					If used, the HTML select must be called "filter"
	*      \return     string      A html select zone
	*/
	function formFilter()
	{
		// CHANGE THIS: Optionnal

		$s='';
        $s.='<select name="filter" class="flat">';
        $s.='<option value="0">&nbsp;</option>';
        $s.='<option value="1">PUBADRESCHIEN</option>';
        $s.='<option value="2">PUBADRESCHAT</option>';
        $s.='<option value="3">HEBERGDOMWEB</option>';
        $s.='</select>';
		return $s;
	}


	/**
	*      \brief      Can include an URL link on each record provided by selector
	*					shown on target page.
	*      \return     string      Url link
	*/
	function url($id)
	{
		// CHANGE THIS: Optionnal

		return '';
	}

}

?>
