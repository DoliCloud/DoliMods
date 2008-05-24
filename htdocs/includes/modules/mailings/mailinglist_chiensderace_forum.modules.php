<?php
/* Copyright (C) 2005-2006 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This file is an example to follow to add your own email selector inside
 * the Dolibarr email tool.
 * Follow instructions given in README file to know what to change to build
 * your own emailing list selector.
 * Code that need to be changed in this file are marked by "CHANGE THIS" tag.
 */

include_once DOL_DOCUMENT_ROOT.'/includes/modules/mailings/modules_mailings.php';


// CHANGE THIS: Class name must be called mailing_xxx with xxx=name of your selector
class mailing_mailinglist_chiensderace_forum extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	var $name='mailinglist_chiensderace_forum';
	// CHANGE THIS: Put here a description of your selector module
	var $desc='Inscrits mailings list ChiensDeRace Forum';
	// CHANGE THIS: Set to 1 if selector is available for admin users only
	var $require_admin=0;

	var $require_module=array();
	var $picto='';
	var $db;


	// CHANGE THIS: Constructor name must be called mailing_xxx with xxx=name of your selector
	function mailing_mailinglist_chiensderace_forum($DB)
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
		// ICI on fait la requete
		// La requete doit retourner: id, name, email
		$sql = " select user_id as id, concat(user_nom,' ',user_prenom) as name, user_email as email from chiensderace_db.forum_users ";
		$sql.= " where 1 = 1";
	    foreach($filtersarray as $key)
        {
            if ($key == '0') return "Error: You must choose a filter";
            if ($key == '1')  $sql.= " AND user_accept_parlonschat = 1";
            if ($key == '-1')  $sql.= " AND user_accept_parlonschat = 0";
            if ($key == '2')  $sql.= " AND user_accept_whiskas = 1";
            if ($key == '-2')  $sql.= " AND user_accept_whiskas = 0";
        }		
		$sql.= " ORDER BY user_email";
		
		// Stocke destinataires dans cibles
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			$j = 0;

			dolibarr_syslog("mailinglist_chiensderace_forum.modules.php: mailing $num cibles trouvées");

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
			dolibarr_syslog($this->db->error());
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
	function getNbOfRecipients($filter=0,$option='')
	{
		// CHANGE THIS: Optionnal

		// Example: return parent::getNbOfRecipients("SELECT count(*) as nb from dolibarr_table");
		// Example: return 500;
		$sql="select count(*) as nb from chiensderace_db.forum_users where 1 = 1";
		if ($filter == 1) $sql.=" AND user_accept_parlonschat = 1";
		if ($filter == 2) $sql.=" AND user_accept_whiskas = 1";
		if ($filter == -1) $sql.=" AND user_accept_parlonschat = 0";
		if ($filter == -2) $sql.=" AND user_accept_whiskas = 0";
		if (! $filter)    $sql.=" AND (user_accept_parlonschat = 1 OR user_accept_whiskas = 1)";
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
        $s.='<option value="1">Inscrits newsletter</option>';
        $s.='<option value="2">Inscrits offres commerciales</option>';
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
