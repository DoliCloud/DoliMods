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
dol_include_once("/sellyoursaas/class/dolicloud_customers.class.php");
include_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';


/**
 * mailing_mailinglist_nltechno_dolicloud
 */
class mailing_mailinglist_nltechno_dolicloudold extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	var $name='mailinglist_nltechno_dolicloudold';
	// CHANGE THIS: Put here a description of your selector module
	var $desc='Clients DoliCloud V1';
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
     *   Affiche formulaire de filtre qui apparait dans page de selection des destinataires de mailings
     *
     *   @return     string      Retourne zone select
     */
    function formFilter()
    {
        global $langs;
        $langs->load("members");

        $form=new Form($this->db);

        $arraystatus=Dolicloud_customers::$listOfStatusNewShort;

        $s='';
        $s.=$langs->trans("Status").': ';
        $s.='<select name="filter" class="flat">';
        $s.='<option value="none">&nbsp;</option>';
        foreach($arraystatus as $status)
        {
	        $s.='<option value="'.$status.'">'.$status.'</option>';
        }
        $s.='</select>';

        $s.=' ';

        $s.=$langs->trans("Language").': ';
        $formother=new FormAdmin($db);
        $s.=$formother->select_language('', 'lang_idv1', 0, 'null', 1);

        $s.=$langs->trans("NotLanguage").': ';
        $formother=new FormAdmin($db);
        $s.=$formother->select_language('', 'not_lang_idv1', 0, 'null', 1);

        $s.=$langs->trans("Country").': ';
        $formother=new FormAdmin($db);
        $s.=$form->select_country('', 'country_codev1','',0,'minwidth300','code2');

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
		return '<a href="'.dol_buildpath('/sellyoursaas/backoffice/instance_info.php',1).'?id='.$id.'">'.img_object('',"user").'</a>';
	}


	/**
	 *  This is the main function that returns the array of emails
	 *
	 *  @param	int		$mailing_id    	Id of emailing
	 *  @param	array	$filtersarray   Requete sql de selection des destinataires
	 *  @return int           			<0 if error, number of emails added if ok
	 */
	function add_to_target($mailing_id,$filtersarray=array())
	{
		global $conf;

		$target = array();
		$cibles = array();
		$j = 0;


		$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
		if ($db2->error)
		{
			dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
			exit;
		}

		$sql = "SELECT i.id, i.name as instance, i.status as instance_status,";
		$sql.= " c.status as status,";
		$sql.= " addr.country as country_code,";
		$sql.= " s.payment_status,";
		$sql.= " s.status as subscription_status,";
		$sql.= " per.username as email,";
		$sql.= " per.first_name as firstname,";
		$sql.= " per.last_name as lastname,";
		$sql.= " per.locale";
		$sql.= " FROM app_instance as i, subscription as s, customer as c";
		$sql.= " LEFT JOIN address as addr ON c.address_id = addr.id";
		$sql.= " LEFT JOIN person as per ON c.primary_contact_id = per.id";
		$sql.= " WHERE i.customer_id = c.id AND c.id = s.customer_id";
		$sql.= " AND per.username IS NOT NULL AND per.username != ''";
		if (! empty($_POST['filter']) && $_POST['filter'] != 'none')
		{
			if ($_POST['filter'] == 'ACTIVE') $sql.=" AND i.status = 'DEPLOYED' AND s.payment_status = 'PAID'";

			if ($_POST['filter'] == 'TRIALING') $sql.=" AND s.payment_status = 'TRIAL' AND c.status LIKE '%ACTIVE%' AND s.status = 'ACTIVE'";
			elseif ($_POST['filter'] == 'TRIAL_EXPIRED') $sql.=" AND s.payment_status = 'TRIAL' AND c.status LIKE '%ACTIVE%' AND s.status = 'EXPIRED'";
			elseif ($_POST['filter'] == 'ACTIVE_PAY_ERR') $sql.=" AND i.status = 'DEPLOYED' AND s.payment_status = 'PAST_DUE' AND c.status LIKE '%ACTIVE%'";
			else
			{
				$sql.=" AND c.status LIKE '%".$db2->escape($_POST['filter'])."%'";
			}
		}
		$tmp=preg_split('/_/',$_POST['lang_idv1']);
		$nottmp=preg_split('/_/',$_POST['not_lang_idv1']);
		$shortlocale = $_POST['lang_idv1'];
		$notshortlocale = $_POST['not_lang_idv1'];
		if ($tmp[0] == $tmp[1]) $shortlocale = $tmp[0];
		if ($nottmp[0] == $nottmp[1]) $notshortlocale = $nottmp[0];
		if (! empty($_POST['lang_idv1']) && $_POST['lang_idv1'] != 'none') $sql.= " AND (locale = '".$this->db->escape($_POST['lang_idv1'])."' OR locale = '".$this->db->escape($shortlocale)."')";
		if (! empty($_POST['not_lang_idv1']) && $_POST['not_lang_idv1'] != 'none') $sql.= " AND (locale <> '".$this->db->escape($_POST['not_lang_idv1'])."' AND locale <> '".$this->db->escape($notshortlocale)."')";
		if (! empty($_POST['country_codev1']) && $_POST['country_codev1'] != 'none') $sql.= " AND addr.country = '".$this->db->escape($_POST['country_codev1'])."'";

		$sql.= " ORDER BY per.username";
		//print $sql;exit;

		// Stocke destinataires dans cibles
		//$result=$this->db->query($sql);
		$result=$db2->query($sql);
		if ($result)
		{
			$num = $db2->num_rows($result);
			$i = 0;

			dol_syslog("mailinglist_nltechno_dolicloud.modules.php: mailing $num targets found");

			$old = '';
			while ($i < $num)
			{
				$obj = $db2->fetch_object($result);
				if ($old <> $obj->email)
				{
					$cibles[$j] = array(
						'email' => $obj->email,
						'lastname' => $obj->lastname,
						'id' => $obj->id,
						'firstname' => $obj->firstname,
						'other' => 'instance='.$obj->instance.';lang='.$obj->locale.';country_code='.$obj->country_code,
						'source_url' => $this->url($obj->id),
						'source_id' => $obj->id,
						'source_type' => 'dolicloud'
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		}
		else
		{
			dol_syslog($db2->error());
			$this->error=$db2->error();
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
		global $conf;

		$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
		if ($db2->error)
		{
			dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
			exit;
		}

		$sql = "SELECT count(distinct(per.username)) as nb";
		$sql.= " FROM app_instance as i, subscription as s, customer as c";
		$sql.= " LEFT JOIN person as per ON c.primary_contact_id = per.id";
		$sql.= " WHERE i.customer_id = c.id AND c.id = s.customer_id";
		$sql.= " AND per.username IS NOT NULL AND per.username != ''";
		if (! empty($_POST['filter']) && $_POST['filter'] != 'none')
		{
			if ($_POST['filter'] == 'ACTIVE') $sql.=" AND i.status = 'DEPLOYED' AND s.payment_status = 'PAID'";

			if ($_POST['filter'] == 'TRIALING') $sql.=" AND s.payment_status = 'TRIAL' AND c.status LIKE '%ACTIVE%' AND s.status = 'ACTIVE'";
			elseif ($_POST['filter'] == 'TRIAL_EXPIRED') $sql.=" AND s.payment_status = 'TRIAL' AND c.status LIKE '%ACTIVE%' AND s.status = 'EXPIRED'";
			elseif ($_POST['filter'] == 'ACTIVE_PAY_ERR') $sql.=" AND i.status = 'DEPLOYED' AND s.payment_status = 'PAST_DUE' AND c.status LIKE '%ACTIVE%'";
			else
			{
				$sql.=" AND c.status LIKE '%".$db2->escape($_POST['filter'])."%'";
			}
		}
		$sql.= " ORDER BY per.username";

		$result=$db2->query($sql);
		if ($result)
		{
			$obj = $db2->fetch_object($result);
			return $obj->nb;
		}
		else
		{
			$this->error=$db2->lasterror();
			return -1;
		}
		if ($a < 0) return -1;
		return $a;
	}

}

