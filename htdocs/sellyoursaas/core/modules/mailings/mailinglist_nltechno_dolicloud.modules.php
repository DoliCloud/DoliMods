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
include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';


/**
 * mailing_mailinglist_nltechno_dolicloud
 */
class mailing_mailinglist_nltechno_dolicloud extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	var $name='mailinglist_nltechno_dolicloud';
	// CHANGE THIS: Put here a description of your selector module
	var $desc='Clients DoliCloud';
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

        $arraysource=array('yesv1'=>'V1','yesv2'=>'V2');
        $arraystatus=array('TRIALING'=>'TRIALING','TRIAL_EXPIRED'=>'TRIAL_EXPIRED','ACTIVE'=>'ACTIVE','ACTIVE_PAY_ERR'=>'ACTIVE_PAY_ERR','SUSPENDED'=>'SUSPENDED','UNDEPLOYED'=>'UNDEPLOYED','CLOSURE_REQUESTED'=>'CLOSURE_REQUESTED','CLOSED'=>'CLOSED');

        $s='';
        $s.=$langs->trans("Source").': ';
        $s.='<select name="filter" class="flat">';
        $s.='<option value="none">&nbsp;</option>';
        foreach($arraysource as $status)
        {
	        $s.='<option value="'.$status.'">'.$status.'</option>';
        }
        $s.='</select>';

        $s.=' ';

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
        $s.=$formother->select_language('', 'lang_id', 0, 'null', 1);

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
		return '<a href="'.DOL_URL_ROOT.'/societe/card.php?socid='.$id.'">'.img_object('',"company").'</a>';
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


		$sql = " select s.rowid as id, email, nom as lastname, '' as firstname";
		$sql.= " from ".MAIN_DB_PREFIX."societe as s LEFT JOIN ".MAIN_DB_PREFIX."societe_extrafields as se on se.fk_object = s.rowid";
		$sql.= " where email IS NOT NULL AND email != ''";
		if (! empty($_POST['filter']) && $_POST['filter'] != 'none') $sql.= " AND status = '".$this->db->escape($_POST['filter'])."'";
		if (! empty($_POST['lang_id']) && $_POST['lang_id'] != 'none') $sql.= " AND default_lang = '".$this->db->escape($_POST['lang_id'])."'";
		$sql.= " ORDER BY email";

		// Stocke destinataires dans cibles
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			dol_syslog("mailinglist_nltechno_dolicloud.modules.php: mailing $num target found");

			$old = '';
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				if ($old <> $obj->email)
				{
					$cibles[$j] = array(
						'email' => $obj->email,
						'lastname' => $obj->lastname,
						'id' => $obj->id,
						'firstname' => $obj->firstname,
						'other' => '',
						'source_url' => $this->url($obj->id),
						'source_id' => $obj->id,
						'source_type' => 'thirdparty'
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
		$a=parent::getNbOfRecipients("select count(distinct(email)) as nb from ".MAIN_DB_PREFIX."societe as s LEFT JOIN ".MAIN_DB_PREFIX."societe_extrafields as se on se.fk_object = s.rowid where email IS NOT NULL AND email != ''");
		if ($a < 0) return -1;
		return $a;
	}

}

