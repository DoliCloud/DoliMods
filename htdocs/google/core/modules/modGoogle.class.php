<?php
/* Copyright (C) 2008-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   google     Module Google
 *      \brief      Module to Google tools integration.
 */

/**
 *      \file       htdocs/google/core/modules/modGoogle.class.php
 *      \ingroup    google
 *      \brief      Description and activation file for module Google
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 *	Description and activation class for module Google
 */
class modGoogle extends DolibarrModules
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
		$this->numero = 101110;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'google';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "interface";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate Google tools in dolibarr";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com?origin=dolimods';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '6.8.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='google@google';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		$this->module_parts = array(
			'triggers' => 1,
			'hooks' => array('main','agenda','agendalist','login'),
			// Set this to 1 if the module provides a captcha driver
			'captcha' => 1
		);

		// Data directories to create when module is enabled
		$this->dirs = array();
		//$this->dirs[0] = DOL_DATA_ROOT.'/mymodule;
		//$this->dirs[1] = DOL_DATA_ROOT.'/mymodule/temp;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array('google_contactsync.php@google');

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(7,2);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(17, 0, -4);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("google@google");

		// Constants
		$this->const = array(0=>array('GOOGLE_DEBUG', 'chaine', '0', 'This is to enable Google debug', 1, 'allentities', 1));



		// Tabs
		$this->tabs = array();
		/*$this->tabs = array('thirdparty:+gmaps:GMaps:google@google:$conf->google->enabled&&$conf->global->GOOGLE_ENABLE_GMAPS:/google/gmaps.php?mode=thirdparty&id=__ID__',
							'contact:+gmaps:GMaps:google@google:$conf->google->enabled&&$conf->global->GOOGLE_ENABLE_GMAPS_CONTACTS:/google/gmaps.php?mode=contact&id=__ID__',
							'member:+gmaps:GMaps:google@google:$conf->google->enabled&&$conf->global->GOOGLE_ENABLE_GMAPS_MEMBERS:/google/gmaps.php?mode=member&id=__ID__',
						);*/
		//$this->tabs = array('agenda:+gcal:MenuAgendaGoogle:google@google:$conf->google->enabled && $conf->global->GOOGLE_ENABLE_AGENDA:/google/index.php');
		$this->tabs = array('agenda:+gcal:MenuAgendaGoogle:google@google:$conf->google->enabled && $conf->global->GOOGLE_ENABLE_AGENDA:/google/index.php'
							,'user:+gsetup:GoogleUserConf:google@google:$conf->google->enabled && $conf->global->GOOGLE_DUPLICATE_INTO_GCAL:/google/admin/google_calsync_user.php?id=__ID__'
							);

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
		//							'perms'=>'1',			// Use 'perms'=>'$user->hasRight("mymodule","level1","level2")' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				// 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		/*$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'MenuAgendaGoogle',
								'mainmenu'=>'google',
								'url'=>'/google/index.php',
								'langs'=>'google',
								'position'=>100,
								'enabled'=>'$conf->google->enabled && $conf->global->GOOGLE_ENABLE_AGENDA',
								'perms'=>'',
								'target'=>'',
								'user'=>0);
		*/
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
		$sql = array();

		// Declare box
		$this->boxes[0]['file'] = "box_googlemaps@google";
		$this->boxes[0]['enabledbydefaulton'] = 1;

		$result=$this->load_tables();

		return $this->_init($sql, $options);
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

		$this->boxes[0]['file'] = "box_googlemaps.php@google";

		return $this->_remove($sql, $options);
	}

	/**
	 *		Create tables, keys and data required by module
	 * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 		and create data commands must be stored in directory /mymodule/sql/
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/google/sql/');
	}
}
