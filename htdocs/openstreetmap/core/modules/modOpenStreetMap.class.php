<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   openstreetmap     Module OpenStreetMap
 *      \brief      Module to OpenStreetMap tools integration.
 */

/**
 *      \file       htdocs/openstreetmap/core/modules/modOpenStreetMap.class.php
 *      \ingroup    openstreetmap
 *      \brief      Description and activation file for module OpenStreetMap
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 *	Description and activation class for module OpenStreetMap
 */
class modOpenStreetMap extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 101160;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'openstreetmap';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "interface";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate OpenStreetMap tools in dolibarr (maps, geocoding with PDOK for the Netherlands and Nominatim, Dutch address autofill)";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com?origin=dolimods';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '4.0.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='openstreetmap@openstreetmap';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		$this->module_parts = array(
			'triggers' => 0,
			// Fill street and town of Dutch addresses from postcode and house number (only active when OPENSTREETMAP_PDOK_AUTOFILL is on)
			'js' => array('/openstreetmap/js/pdokautofill.js'),
		);

		// Data directories to create when module is enabled
		$this->dirs = array();
		//$this->dirs[0] = DOL_DATA_ROOT.'/mymodule;
		//$this->dirs[1] = DOL_DATA_ROOT.'/mymodule/temp;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array('openstreetmap_maps.php@openstreetmap');

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,6);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(17, 0, -4);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("openstreetmap@openstreetmap");

		// Constants
		$this->const = array(
			0 => array('OPENSTREETMAP_PDOK_AUTOFILL', 'chaine', '1', 'Fill street and town of Dutch addresses from postcode and house number (PDOK)', 0, 'current', 0),
		);

		// Tabs
		$this->tabs = array();
		/*$this->tabs = array('thirdparty:+gmaps:Maps:@openstreetmap:$conf->openstreetmap->enabled&&$conf->global->OPENSTREETMAP_ENABLE_MAPS:/openstreetmap/gmaps.php?mode=thirdparty&id=__ID__',
							'contact:+gmaps:Maps:@openstreetmap:$conf->openstreetmap->enabled&&$conf->global->OPENSTREETMAP_ENABLE_MAPS_CONTACTS:/openstreetmap/gmaps.php?mode=contact&id=__ID__',
							'member:+gmaps:Maps:@openstreetmap:$conf->openstreetmap->enabled&&$conf->global->OPENSTREETMAP_ENABLE_MAPS_MEMBERS:/openstreetmap/gmaps.php?mode=member&id=__ID__',
						);*/

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Add here list of php file(s) stored in includes/boxes that contains class to show a box.
		// Example:
		//$this->boxes[$r][1] = "myboxa.php";
		//$r++;
		//$this->boxes[$r][1] = "myboxb.php";
		//$r++;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = 2000; 				// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus
		// Example to declare the Top Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			// Put 0 if this is a top menu
		//							'type'=>'top',			// This is a Top menu entry
		//							'titre'=>'MyModule top menu',
		//							'mainmenu'=>'mymodule',
		//							'url'=>'/mymodule/pagetop.php',
		//							'langs'=>'mylangfile',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				// 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=companies,fk_leftmenu=thirdparties',
			'type' => 'left',
			'titre' => 'OpenStreetMapMenuMap',
			'mainmenu' => 'companies',
			'leftmenu' => 'openstreetmap_thirdparties',
			'url' => '/openstreetmap/maps_all.php?mode=thirdparty',
			'langs' => 'openstreetmap@openstreetmap',
			'position' => 1000,
			'enabled' => 'isModEnabled("openstreetmap") && isModEnabled("societe") && getDolGlobalString("OPENSTREETMAP_ENABLE_MAPS")',
			'perms' => '$user->hasRight("societe", "lire")',
			'target' => '',
			'user' => 0
		);
		$r++;
		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=companies,fk_leftmenu=contacts',
			'type' => 'left',
			'titre' => 'OpenStreetMapMenuMap',
			'mainmenu' => 'companies',
			'leftmenu' => 'openstreetmap_contacts',
			'url' => '/openstreetmap/maps_all.php?mode=contact',
			'langs' => 'openstreetmap@openstreetmap',
			'position' => 1000,
			'enabled' => 'isModEnabled("openstreetmap") && isModEnabled("societe") && getDolGlobalString("OPENSTREETMAP_ENABLE_MAPS_CONTACTS")',
			'perms' => '$user->hasRight("societe", "contact", "lire")',
			'target' => '',
			'user' => 0
		);
		$r++;
		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=members,fk_leftmenu=members',
			'type' => 'left',
			'titre' => 'OpenStreetMapMenuMap',
			'mainmenu' => 'members',
			'leftmenu' => 'openstreetmap_members',
			'url' => '/openstreetmap/maps_all.php?mode=member',
			'langs' => 'openstreetmap@openstreetmap',
			'position' => 1000,
			'enabled' => 'isModEnabled("openstreetmap") && isModEnabled("adherent") && getDolGlobalString("OPENSTREETMAP_ENABLE_MAPS_MEMBERS")',
			'perms' => '$user->hasRight("adherent", "lire")',
			'target' => '',
			'user' => 0
		);
		$r++;
	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
	 *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options = '')
	{
		$result = $this->load_tables();
		if ($result < 0) {
			return -1;
		}

		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *		Create tables, keys and data required by module (files llx_*.sql in /openstreetmap/sql/)
	 *
	 *		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/openstreetmap/sql/');
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
	 *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}
}
