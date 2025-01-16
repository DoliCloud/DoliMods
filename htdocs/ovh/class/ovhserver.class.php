<?php
/* Copyright (C) 2007-2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2016  Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * \file        htdocs/ovh/class/ovhserver.class.php
 * \ingroup     mymodule
 * \brief       This file is a CRUD class file for MyObject (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;
use GuzzleHttp\Client as GClient;


/**
 * Class for MyObject
 */
class OvhServer extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'myobject';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'myobject';

	/**
	 * @var array  Does myobject support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	public $ismultientitymanaged = 1;
	/**
	 * @var string String with name of icon for myobject
	 */
	public $picto = 'server.svg@ovh';


	/**
	 *             'type' if the field format, 'label' the translation key, 'enabled' is a condition when the filed must be managed,
	 *             'visible' says if field is visible in list (-1 means not shown by default but can be aded into list to be viewed)
	 *             'notnull' if not null in database
	 *             'index' if we want an index in database
	 *             'position' is the sort order of field
	 *             'searchall' is 1 if we want to search in this field when making a search from the quick search button
	 *             'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *             'comment' is not used. You can store here any text of your choice.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property
	 */
	public $fields=array(
		'rowid'         =>array('type'=>'integer',      'label'=>'TechnicalID',      'enabled'=>1, 'visible'=>-1, 'notnull'=>1, 'index'=>1, 'position'=>1,  'comment'=>'Id'),
		'ref'           =>array('type'=>'varchar(64)',  'label'=>'Ref',              'enabled'=>1, 'visible'=>1,  'notnull'=>1, 'index'=>1, 'position'=>10, 'searchall'=>1, 'comment'=>'Reference of object'),
		'entity'        =>array('type'=>'integer',      'label'=>'Entity',           'enabled'=>1, 'visible'=>0,  'notnull'=>1, 'index'=>1, 'position'=>20),
		'label'         =>array('type'=>'varchar(255)', 'label'=>'Label',            'enabled'=>1, 'visible'=>1,  'position'=>30,  'searchall'=>1),
		'qty'           =>array('type'=>'double(24,8)', 'label'=>'Qty',              'enabled'=>1, 'visible'=>1,  'position'=>40,  'searchall'=>0, 'isameasure'=>1),
		'status'        =>array('type'=>'integer',      'label'=>'Status',           'enabled'=>1, 'visible'=>1,  'index'=>1,   'position'=>1000),
		'date_creation' =>array('type'=>'datetime',     'label'=>'DateCreation',     'enabled'=>1, 'visible'=>-1, 'notnull'=>1, 'position'=>500),
		'tms'           =>array('type'=>'timestamp',    'label'=>'DateModification', 'enabled'=>1, 'visible'=>-1, 'notnull'=>1, 'position'=>500),
		'import_key'    =>array('type'=>'varchar(14)',  'label'=>'ImportId',         'enabled'=>1, 'visible'=>-1, 'notnull'=>-1, 'index'=>1,  'position'=>1000),
	);
	// END MODULEBUILDER PROPERTIES



	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'myobjectdet';
	/**
	 * @var int    Field with ID of parent key if this field has a parent
	 */
	//public $fk_element = 'fk_myobject';
	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'MyObjectline';
	/**
	 * @var array  Array of child tables (child tables to delete before deleting a record)
	 */
	//protected $childtables=array('myobjectdet');
	/**
	 * @var MyObjectLine[]     Array of subtable lines
	 */
	//public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}


	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
	 *  @param	int  	$notooltip			1=Disable tooltip
	 *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Server") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Id') . ':</b> ' . $this->id.'<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		//print '<a href="?mode=publiccloud&server=' . $serverobj['id'] . '&project='.$projectname.'">' . $serverobj['name'] . '</a>';
		$url = dol_buildpath('/ovh/ovh_listinfoserver.php', 1).'?mode=publiccloud&server=' . $this->id . '&project='.$this->projectname;

		$linkclose='';
		if (empty($notooltip)) {
			if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) {
				$label=$langs->trans("ShowServer");
				$linkclose.=' alt="'.dolPrintHTMLForAttribute($label).'"';
			}
			$linkclose.=' title="'.dolPrintHTMLForAttribute($label).'"';
			$linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
		} else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		if ($withpicto) {
			$result.=($linkstart.img_object(($notooltip?'':$label), 'server.svg@ovh', ($notooltip?'':'class="classfortooltip"')).$linkend);
			if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode = 0)
	{
		return $this->LibStatut($this->status, $mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	string	$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status, $mode = 0)
	{
		global $langs;

		if ($mode == 0) {
			$prefix='';
			if ($status == 'ACTIVE') return $langs->trans('Active');
			if ($status == 'INACTIVE') return $langs->trans('Disabled');
			if ($status == 'SNAPSHOTTING') return $langs->trans('Snapshotting');
		}
		if ($mode == 1) {
			if ($status == 'ACTIVE') return $langs->trans('Active');
			if ($status == 'INACTIVE') return $langs->trans('Disabled');
			if ($status == 'SNAPSHOTTING') return $langs->trans('Snapshotting');
		}
		if ($mode == 2) {
			if ($status == 'ACTIVE') return img_picto($langs->trans('Active'), 'statut4').' '.$langs->trans('Active');
			if ($status == 'INACTIVE') return img_picto($langs->trans('Disabled'), 'statut5').' '.$langs->trans('Disabled');
			if ($status == 'SNAPSHOTTING') return img_picto($langs->trans('Snapshotting'), 'statut1').' '.$langs->trans('Snapshotting');
		}
		if ($mode == 3) {
			if ($status == 'ACTIVE') return img_picto($langs->trans('Active'), 'statut4');
			if ($status == 'INACTIVE') return img_picto($langs->trans('Disabled'), 'statut5');
			if ($status == 'SNAPSHOTTING') return img_picto($langs->trans('Snapshotting'), 'statut1');
		}
		if ($mode == 4) {
			if ($status == 'ACTIVE') return img_picto($langs->trans('Active'), 'statut4').' '.$langs->trans('Active');
			if ($status == 'INACTIVE') return img_picto($langs->trans('Disabled'), 'statut5').' '.$langs->trans('Disabled');
			if ($status == 'SNAPSHOTTING') return img_picto($langs->trans('Snapshotting'), 'statut1').' '.$langs->trans('Snapshotting');
		}
		if ($mode == 5) {
			if ($status == 'ACTIVE') return $langs->trans('Active').' '.img_picto($langs->trans('Active'), 'statut4');
			if ($status == 'INACTIVE') return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'), 'statut5');
			if ($status == 'SNAPSHOTTING') return $langs->trans('Snapshotting').' '.img_picto($langs->trans('Snapshotting'), 'statut1');
		}
		if ($mode == 6) {
			if ($status == 'ACTIVE') return $langs->trans('Active').' '.img_picto($langs->trans('Active'), 'statut4');
			if ($status == 'INACTIVE') return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'), 'statut5');
			if ($status == 'SNAPSHOTTING') return $langs->trans('Snapshotting').' '.img_picto($langs->trans('Snapshotting'), 'statut1');
		}
	}


	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}

	/**
	 * Launch creation of a snapshot
	 *
	 * @param	string	$project	Project
	 * @param	string	$server		Server ref
	 * @param	string	$name		Server label
	 * @return	int					<0 if KO, 0 if OK (This is standard for methods called by crons)
	 */
	public function createSnapshot($project, $server, $name = '')
	{
		global $conf, $langs;

		$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;
		$connect_timeout = empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT;
		$timeout = empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT;

		if ('guzzle7.3' == 'guzzle7.3') {
			$arrayconfig = array(
				'connect_timeout'=>$connect_timeout,
				'timeout'=>$timeout
			);
			$http_client = new GClient($arrayconfig);
		} else {
			$http_client = new GClient();
			$http_client->setDefaultOption('connect_timeout',$connect_timeout);  // Timeout by default of OVH is 5 and it is not enough
			$http_client->setDefaultOption('timeout', $timeout);
		}

		dol_syslog("createSnapshot endpoint=".$endpoint." connect_timeout=".$connect_timeout." timeout=".$timeout);

		$conn = new Api($conf->global->OVHAPPKEY, $conf->global->OVHAPPSECRET, $endpoint, $conf->global->OVHCONSUMERKEY, $http_client);

		$resultcreatesnapshot=null;
		try {
			$snapshotName='Snapshot from Dolibarr '.($name?$name.' ':'').dol_print_date(dol_now(), 'dayhour');
			$content = (object) array('snapshotName'=>$snapshotName);
			$resultcreatesnapshot = $conn->post('/cloud/project/'.$project.'/instance/'.$server.'/snapshot', $content);
			$resultcreatesnapshot = json_decode(json_encode($resultcreatesnapshot), false);
			$this->msg = $langs->trans("SnapshotRequestSent", $snapshotName);
			return 0;
		} catch (Exception $e) {
			$this->error = 'Error '.$e->getMessage().'<br>If there is an error to connect to OVH host, check your firewall does not block port required to reach OVH manager/api (for example port 443).';
		}
		return -1;
	}
}

/**
 * Class MyModuleObjectLine
 */
class MyModuleObjectLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	public $prop1;
	/**
	 * @var mixed Sample line property 2
	 */
	public $prop2;
}
