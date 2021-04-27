<?php
/* Copyright (C) 2005-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This file is an example to follow to add your own email selector inside
 * the Dolibarr email tool.
 * Follow instructions given in README file to know what to change to build
 * your own emailing list selector.
 * Code that need to be changed in this file are marked by "CHANGE THIS" tag.
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/mailings/modules_mailings.php';
dol_include_once("/prestashopget/class/myobject.class.php");


/**
 * mailing_mailinglist_prestashopget
 */
class mailing_mailinglist_prestashopget_myobject extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	var $name='mailinglist_prestashopget_myobject';
	// CHANGE THIS: Put here a description of your selector module
	var $desc='Customers in PrestaShop';
	// CHANGE THIS: Set to 1 if selector is available for admin users only
	var $require_admin=0;

	var $enabled=0;
	var $require_module=array();
	var $picto='prestashopget@prestashopget';

	/**
	 * @var DoliDB Database handler.
	 */
	public $db;


	/**
	 *	Constructor
	 *
	 * 	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		global $conf;

		$this->db=$db;
		if (is_array($conf->modules)) {
			$this->enabled=in_array('prestashopget', $conf->modules)?1:0;
		}
	}


	/**
	 *   Affiche formulaire de filtre qui apparait dans page de selection des destinataires de mailings
	 *
	 *   @return     string      Retourne zone select
	 */
	function formFilter()
	{
		global $langs;
		$langs->load("members");

		$form=new Form($this->db);

		$arraystatus=array(1=>'1 - English', 2=>'2 - French', 3=>'3 - Spanish', 4=>'4 - Italian', 5=>'5 - German');        // TODO Get id of lang from Prestashop

		$s='';

		$s.=$langs->trans("Language").': ';
		$s.='<select name="filter" class="flat">';
		$s.='<option value="none">&nbsp;</option>';
		foreach ($arraystatus as $status) {
			$s.='<option value="'.$status.'">'.$status.'</option>';
		}
		$s.='</select>';
		$s.='<br>';

		$arraystatus=array(0=>'0', 1=>'1');

		$s.=$langs->trans("Newsletter").': ';
		$s.='<select name="newsletter" class="flat">';
		$s.='<option value="none">&nbsp;</option>';
		foreach ($arraystatus as $status) {
			$s.='<option value="'.$status.'">'.$status.'</option>';
		}
		$s.='</select>';
		$s.='<br>';

		$arraystatus=array(0=>'0', 1=>'1');

		$s.=$langs->trans("Optin").': ';
		$s.='<select name="optin" class="flat">';
		$s.='<option value="none">&nbsp;</option>';
		foreach ($arraystatus as $status) {
			$s.='<option value="'.$status.'">'.$status.'</option>';
		}
		$s.='</select>';
		$s.='<br>';

		return $s;
	}


	/**
	 *  Renvoie url lien vers fiche de la source du destinataire du mailing
	 *
	 *  @param		int			$id		ID
	 *  @return     string      		Url lien
	 */
	function url($id)
	{
		return '<a href="'.dol_buildpath('/prestashopget/myobject_card.php', 1).'?id='.$id.'">'.img_object('', "generic").'</a>';
	}


	/**
	 *  This is the main function that returns the array of emails
	 *
	 *  @param	int		$mailing_id    	Id of emailing
	 *  @param	array	$filtersarray   Requete sql de selection des destinataires
	 *  @return int           			<0 if error, number of emails added if ok
	 */
	function addTargetsToDatabase($mailing_id, $filtersarray = array())
	{
		return $this->add_to_target($mailing_id, $filtersarray);
	}

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.NotCamelCaps
	/**
	 *  This is the main function that returns the array of emails
	 *
	 *  @param	int		$mailing_id    	Id of emailing
	 *  @param	array	$filtersarray   Requete sql de selection des destinataires
	 *  @return int           			<0 if error, number of emails added if ok
	 */
	function add_to_target($mailing_id, $filtersarray = array())
	{
		global $conf;

        // phpcs:enable
		$target = array();
		$cibles = array();
		$j = 0;

		$db2=getDoliDBInstance('mysqli', $conf->global->PRESTASHOPGET_DB_SERVER, $conf->global->PRESTASHOPGET_DB_USER, $conf->global->PRESTASHOPGET_DB_PASS, 'dolistore', 3306);
		if (! $db2->connected) {
			$this->error = 'Failed to connect to PrestaShop server';
			return -1;
		}

		$sql = 'SELECT
		a.id_customer,
		firstname,
		lastname,
		email,
		a.id_lang,
		a.active,
		newsletter,
		optin,
		a.date_add,
		gl.name as title,
		( SELECT SUM(total_paid_real / conversion_rate) FROM ps_orders o WHERE o.id_customer = a.id_customer AND o.id_shop IN (1) AND o.valid = 1 ) as total_spent,
		(
		    SELECT
		    c.date_add
		    FROM ps_guest g
		    LEFT JOIN ps_connections c ON c.id_guest = g.id_guest
		    WHERE g.id_customer = a.id_customer
		    ORDER BY c.date_add DESC LIMIT 1
		    )
		    as connect
		    FROM ps_customer a
		    LEFT JOIN ps_gender_lang gl ON (a.id_gender = gl.id_gender AND gl.id_lang = 2)
		    WHERE 1
		    AND a.deleted = 0';
		if (! empty($_POST['newsletter']) && $_POST['newsletter'] != 'none') $sql.= " AND newsletter = 1";
		if (! empty($_POST['optin']) && $_POST['optin'] != 'none') $sql.= " AND optin = 1";


		$sql.= " AND email IS NOT NULL AND email != ''";
		if (! empty($_POST['filter']) && $_POST['filter'] != 'none') $sql.= " AND a.id_lang = '".$this->db->escape($_POST['filter'])."'";
		$sql.= " ORDER BY email";

		// Stocke destinataires dans cibles
		$result=$db2->query($sql);
		if ($result) {
			$num = $db2->num_rows($result);
			$i = 0;

			dol_syslog("mailinglist_prestashopget_myobject.modules.php: mailing ".$num." targets found");

			$old = '';
			while ($i < $num) {
				$obj = $db2->fetch_object($result);
				if ($old <> $obj->email) {
					$cibles[$j] = array(
						'email' => $obj->email,
						'lastname' => $obj->lastname,
						'id' => $obj->id_csutomer,
						'firstname' => $obj->firstname,
						'other' => $obj->newsletter.';'.$obj->optin,
						'source_url' => '',
						'source_id' => $obj->id_customer,
						'source_type' => 'prestashop'
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		} else {
			$this->error=$db2->lasterror();
			dol_syslog($this->error);
			return -1;
		}

		// You must fill the $target array with record like this
		// $target[0]=array('email'=>'email_0','name'=>'name_0','firstname'=>'firstname_0');
		// ...
		// $target[n]=array('email'=>'email_n','name'=>'name_n','firstname'=>'firstname_n');

		// Example: $target[0]=array('email'=>'myemail@mydomain.com','name'=>'Doe','firstname'=>'John');

		// ----- Your code end here -----
		if (method_exists(get_parent_class($this), 'addTargetsToDatabase')) {
			return parent::addTargetsToDatabase($mailing_id, $cibles);
		} else {
			return parent::add_to_target($mailing_id, $cibles);
		}
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
	 *	@return	int					Nb of recipients or -1 if KO
	 */
	function getNbOfRecipients($filter = 1, $option = '')
	{
		//$a=parent::getNbOfRecipients("select count(distinct(email)) as nb from ".MAIN_DB_PREFIX."ps_customer as p where email IS NOT NULL AND email != ''");
		$a=0;

		if ($a < 0) return -1;
		return '';
	}
}
