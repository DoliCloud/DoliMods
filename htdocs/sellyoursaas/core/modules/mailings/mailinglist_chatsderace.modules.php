<?php
/* Copyright (C) 2005-2006 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This file is an example to follow to add your own email selector inside
 * the Dolibarr email tool.
 * Follow instructions given in README file to know what to change to build
 * your own emailing list selector.
 * Code that need to be changed in this file are marked by "CHANGE THIS" tag.
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/mailings/modules_mailings.php';


/**
 * mailing_mailinglist_chatsderace
 */
class mailing_mailinglist_chatsderace extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	var $name='mailinglist_chatsderace';
	// CHANGE THIS: Put here a description of your selector module
	var $desc='Inscrits ML ChatsDeRace';
	// CHANGE THIS: Set to 1 if selector is available for admin users only
	var $require_admin=0;

	var $enabled=0;
	var $require_module=array();
	var $picto='sellyoursaas.gif@sellyoursaas';
	var $db;


	/**
     *	Constructor
     *
     * 	@param	DoliDB	$db		Database handler
     */
	function __construct($db)
	{
		global $conf;

		$this->db=$db;
		if (is_array($conf->modules))
		{
			$this->enabled=in_array('sellyoursaas',$conf->modules);
		}
	}


	/**
	 *  This is the main function that returns the array of emails
	 *
	 *  @param	int		$mailing_id    	Id of mailing. No need to use it.
	 *  @param  array	$filtersarray  	If you used the formFilter function. Empty otherwise.
	 *  @return int           			<0 if error, number of emails added if ok
	 */
	function add_to_target($mailing_id,$filtersarray=array())
	{
		$target = array();

		// CHANGE THIS
		// ----- Your code start here -----

		$cibles = array();
		$j = 0;

		$sitedb='chatsderace_db';

		// ICI on fait la requete sur T_ADRESSES
		// La requete doit retourner: id, email, nom
		$sql = " select ID_ADRES as id, NOMRESP_ADRES as name, EMAIL_ADRES as email";
		$sql.= " from ".$sitedb.".T_ADRESSES";
		$sql.= " where EMAIL_ADRES IS NOT NULL and ML_ADRES = 1";
		$sql.= " ORDER BY EMAIL_ADRES";

		// Stocke destinataires dans cibles
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			dol_syslog("mailinglist_chatsderace.modules.php: mailing $num cibles trouvees");

			$old = '';
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				if ($old <> $obj->email)
				{
					$cibles[$j] = array(
					'email' => $obj->email,
					'name' => $obj->name,
					'id' => $obj->id
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		}
		else
		{
			dol_syslog($this->db->error());
			$this->error=$this->db->error();
			return -1;
		}

		// ICI on fait la requete sur T_PERSONNES
		// La requete doit retourner: id, name, email
		$sql = " select ID_PERSO as id, NOM_PERSO as name, EMAIL_PERSO as email";
		$sql.= " from ".$sitedb.".T_PERSONNES as p";
		$sql.= " LEFT JOIN ".$sitedb.".T_ADRESSES as a ON p.EMAIL_PERSO = a.EMAIL_ADRES where EMAIL_ADRES IS NULL";
		$sql.= " and EMAIL_PERSO IS NOT NULL AND EMAIL_PERSO != '' and ML_PERSO = 1";
		$sql.= " ORDER BY EMAIL_PERSO";

		// Stocke destinataires dans cibles
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			dol_syslog("mailinglist_chatsderace.modules.php: mailing $num cibles trouvees");

			$old = '';
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				if ($old <> $obj->email)
				{
					$cibles[$j] = array(
					'email' => $obj->email,
					'name' => $obj->name,
					'id' => $obj->id
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		}
		else
		{
			dol_syslog($this->db->error());
			$this->error=$this->db->error();
			return -1;
		}

		// You must fill the $target array with record like this
		// $target[0]=array('email'=>'email_0','name'=>'name_0','firstname'=>'firstname_0');
		// ...
		// $target[n]=array('email'=>'email_n','name'=>'name_n','firstname'=>'firstname_n');

		// Example: $target[0]=array('email'=>'myemail@mydomain.com','name'=>'Doe','firstname'=>'John');

		// ----- Your code end here -----

		return parent::add_to_target($mailing_id, $cibles);
	}


	/**
	 *	On the main mailing area, there is a box with statistics.
	 *	If you want to add a line in this report you must provide an
	 *	array of SQL request that returns two field:
	 *	One called "label", One called "nb".
	 *
	 *	@return		array
	 */
	function getSqlArrayForStats()
	{
		// CHANGE THIS: Optionnal

		//var $statssql=array();
		//$this->statssql[0]="SELECT field1 as label, count(distinct(email)) as nb FROM mytable WHERE email IS NOT NULL";

		return array();
	}


	/**
	 *	Return here number of distinct emails returned by your selector.
	 *	For example if this selector is used to extract 500 different
	 *	emails from a text file, this function must return 500.
	 *
	 *	@param	string	$filter		Filter
	 *	@param	string	$option		Options
	 *	@return	int					Nb of recipients
     */
	function getNbOfRecipients($filter=1,$option='')
	{
		// CHANGE THIS: Optionnal

		// Example: return parent::getNbOfRecipients("SELECT count(*) as nb from dolibarr_table");
		// Example: return 500;
		$sitedb='chatsderace_db';
		$a=parent::getNbOfRecipients("select count(distinct(EMAIL_ADRES)) as nb from ".$sitedb.".T_ADRESSES  as p where EMAIL_ADRES IS NOT NULL AND EMAIL_ADRES != ''".($filter?" and ML_ADRES = ".$filter:""));
		$b=parent::getNbOfRecipients("select count(distinct(EMAIL_PERSO)) as nb from ".$sitedb.".T_PERSONNES as p LEFT JOIN ".$sitedb.".T_ADRESSES as a ON p.EMAIL_PERSO = a.EMAIL_ADRES where EMAIL_ADRES IS NULL and EMAIL_PERSO IS NOT NULL".($filter?" and ML_PERSO = ".$filter:""));

		if ($a < 0 || $b < 0) return -1;
		if ($option == 'adresses') return $a;
		if ($option == 'personnes') return $b;
		return ($a+$b);
	}

	/**
	 *  This is to add a form filter to provide variant of selector
	 *	If used, the HTML select must be called "filter"
	 *
	 *  @return     string      A html select zone
	 */
	function formFilter()
	{
		// CHANGE THIS: Optionnal

		$s='';
		return $s;
	}


	/**
	 *  Can include an URL link on each record provided by selector
	 *	shown on target page.
	 *
	 *  @param	int		$id		Id
	 *  @return string      	Url link
	 */
	function url($id)
	{
		// CHANGE THIS: Optionnal

		return '';
	}

}

