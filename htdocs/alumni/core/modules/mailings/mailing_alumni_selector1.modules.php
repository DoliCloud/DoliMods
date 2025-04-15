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
dol_include_once("/alumni/class/myobject.class.php");


/**
 * mailing_mailinglist_alumni
 */
class mailing_mailing_alumni_selector1 extends MailingTargets
{
	// CHANGE THIS: Put here a name not already used
	public $name = 'mailing_alumni_selector1';
	// CHANGE THIS: Put here a description of your selector module
	public $desc = 'List of email from results of the survey';
	// CHANGE THIS: Set to 1 if selector is available for admin users only
	public $require_admin = 0;

	public $enabled = 'isModEnabled("alumni")';

	public $require_module = array();

	/**
	 * @var string 	String with the name of icon for myobject. Can be an image filename like 'object_myobject.png' of a font awesome code 'fa-...'.
	 */
	public $picto = 'fa-graduation-cap';

	/**
	 * @var DoliDB Database handler.
	 */
	public $db;


	/**
	 *  Constructor
	 *
	 *  @param  DoliDB  $db     Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
		//$this->enabled = ...
	}


	/**
	 *  Displays the filter form that appears in the mailing recipient selection page
	 *
	 *  @return     string      Return select zone
	 */
	public function formFilter()
	{
		global $langs;
		$langs->load("alumni@alumni");

		dol_include_once('/alumni/class/survey.class.php');
		$tmp = new Survey($this->db);
		$arrayfields = $tmp->fields['motivation']['arrayofkeyval'];

		$s = '';
		$s .= $langs->trans($tmp->fields['motivation']['label']).' ';
		$s .= '<select name="filter_motivation" id="filter_motivation" class="flat maxwidth200">';
		$s .= '<option value="none">&nbsp;</option>';
		foreach ($arrayfields as $key => $val) {
			$s .= '<option value="'.$key.'">'.$val.'</option>';
		}
		$s .= '</select>';
		$s .= ajax_combobox('filter_motivation');
		$s .= '<br>';

		$extrafields = new ExtraFields($this->db);
		$extrafields->fetch_name_optionals_label($tmp->table_element);
		
		foreach($extrafields->attributes[$tmp->table_element]['label'] as $key => $label) {
			$s .= $langs->trans($label).' ';
			$s .= '<input type="text" value="'.GETPOST($key).'" name="'.$key.'">';
			$s .= '<br>';
		}
		
		return $s;
	}


	/**
	 *  Returns url link to file of the source of the recipient of the mailing
	 *
	 *  @param      int         $id     ID
	 *  @return     string              Url lien
	 */
	public function url($id)
	{
		return '<a href="'.dol_buildpath('/alumni/survey_card.php', 1).'?id='.$id.'">'.img_object('', "generic").'</a>';
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  This is the main function that returns the array of emails
	 *
	 *  @param  int     $mailing_id     Id of emailing
	 *  @return int                     Return integer <0 if error, number of emails added if ok
	 */
	public function add_to_target($mailing_id)
	{
		// phpcs:enable
		$target = array();
		$j = 0;

		dol_include_once('/alumni/class/survey.class.php');
		$tmp = new Survey($this->db);
		
		$sql = "SELECT asu.rowid as id, asu.firstname, asu.lastname, asu.email";
		$sql .= " FROM ".MAIN_DB_PREFIX."alumni_survey as asu";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."alumni_survey_extrafields as ase ON asu.rowid = ase.fk_object";
		$sql .= " WHERE asu.email IS NOT NULL AND asu.email <> ''";
		/*if (GETPOSTISSET('filter') && GETPOST('filter', 'alphanohtml') != 'none') {
			$sql .= " AND status = '".$this->db->escape(GETPOST('filter', 'alphanohtml'))."'";
		}*/
		if (GETPOSTISSET('filter_motivation') && GETPOSTINT('filter_motivation') > 0) {
			$sql .= " AND asu.motivation = ".((int) GETPOSTINT('filter_motivation'));
		}
		$extrafields = new ExtraFields($this->db);
		$extrafields->fetch_name_optionals_label($tmp->table_element);
		
		foreach($extrafields->attributes[$tmp->table_element]['label'] as $key => $label) {
			if (GETPOSTISSET($key) && GETPOST($key) == '0') {
				$sql .= " AND IFNULL(ase.".$this->db->sanitize($key).",0) = 0";
			}
			if (GETPOSTISSET($key) && GETPOST($key) == '-1' && GETPOST($key) == '1') {
				$sql .= " AND ase.".$this->db->sanitize($key)." LIKE '".$this->db->escape(GETPOST($key))."'";
			}
		}
		
		$sql .= " ORDER BY asu.email";

		// Store recipients in target
		$result = $this->db->query($sql);
		if ($result) {
			$num = $this->db->num_rows($result);
			$i = 0;

			dol_syslog(__METHOD__.":add_to_target ".$num." targets found");

			$old = '';
			while ($i < $num) {
				$obj = $this->db->fetch_object($result);
				if ($old != $obj->email) {
					$target[$j] = array(
						'email' => $obj->email,
						'id' => $obj->id,
						'firstname' => $obj->firstname,
						'lastname' => $obj->lastname,
						//'other' => $obj->label,
						'source_url' => $this->url($obj->id),
						'source_id' => $obj->id,
						'source_type' => 'survey@alumni'
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		} else {
			dol_syslog($this->db->error());
			$this->error = $this->db->error();
			return -1;
		}

		// You must fill the $target array with record like this
		// $target[0]=array('email'=>'email_0','name'=>'name_0','firstname'=>'firstname_0');
		// ...
		// $target[n]=array('email'=>'email_n','name'=>'name_n','firstname'=>'firstname_n');

		// Example: $target[0]=array('email'=>'myemail@mydomain.com','name'=>'Doe','firstname'=>'John');

		// ----- Your code end here -----

		return parent::addTargetsToDatabase($mailing_id, $target);
	}


	/**
	 *  On the main mailing area, there is a box with statistics.
	 *  If you want to add a line in this report you must provide an
	 *  array of SQL request that returns two field:
	 *  One called "label", One called "nb".
	 *
	 *  @return array
	 */
	public function getSqlArrayForStats()
	{
		// CHANGE THIS: Optional

		//var $statssql=array();
		//$this->statssql[0]="SELECT field1 as label, count(distinct(email)) as nb FROM mytable WHERE email IS NOT NULL";

		return array();
	}


	/**
	 *  Return here number of distinct emails returned by your selector.
	 *  For example if this selector is used to extract 500 different
	 *  emails from a text file, this function must return 500.
	 *
	 *  @param		string			$sql 		Not use here
	 *  @return 	int                 		Nb of recipients or -1 if KO
	 */
	public function getNbOfRecipients($sql = '')
	{
		$sql = "SELECT COUNT(DISTINCT(email)) as nb";
		$sql .= " FROM ".MAIN_DB_PREFIX."alumni_survey as p";
		$sql .= " WHERE email IS NOT NULL AND email <> ''";

		$a = parent::getNbOfRecipients($sql);

		if ($a < 0) {
			return -1;
		}
		return $a;
	}
}
