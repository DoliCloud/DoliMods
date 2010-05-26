<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   google     Module Google
 *      \brief      Module to Google tools integration.
 */

/**
 *      \file       htdocs/includes/modules/modGoogle.class.php
 *      \ingroup    google
 *      \brief      Description and activation file for module Google
 *		\version	$Id: modGoogle.class.php,v 1.12 2010/05/26 13:06:08 eldy Exp $
 */
include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**     \class      modGoogle
 *       \brief     Description and activation class for module Google
 */
class modGoogle extends DolibarrModules
{
	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	function modGoogle($DB)
	{
		$this->db = $DB;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 11000;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'google';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "projects";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate Google tools in dolibarr";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '2.9';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 1;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/images directory, use this->picto=DOL_URL_ROOT.'/module/images/file.png'
		$this->picto=DOL_URL_ROOT.'/google/images/google.png';

		// Data directories to create when module is enabled
		$this->dirs = array();
		//$this->dirs[0] = DOL_DATA_ROOT.'/mymodule;
		//$this->dirs[1] = DOL_DATA_ROOT.'/mymodule/temp;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array('google.php@google');

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,1);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(2,4);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("google@google");

		// Constants
		$this->const = array();			// List of parameters

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
		$this->menus = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus
		// Example to declare the Top Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			// Put 0 if this is a top menu
		//							'type'=>'top',			// This is a Top menu entry
		//							'titre'=>'MyModule top menu',
		//							'mainmenu'=>'mymodule',
		//							'leftmenu'=>'1',		// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
		//							'url'=>'/mymodule/pagetop.php',
		//							'langs'=>'mylangfile',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				// 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'MenuAgendaGoogle',
								'mainmenu'=>'google',
								'leftmenu'=>'0',	// To say to not overwrite menu in pre.inc.php by dynamic database menu
								'url'=>'/google/index.php',
								'langs'=>'google',
								'position'=>100,
								'enabled'=>'$conf->google->enabled && $conf->global->GOOGLE_ENABLE_AGENDA',
								'perms'=>'',
								'target'=>'',
								'user'=>0);
		$r++;
	}

	/**
	 *		\brief      Function called when module is enabled.
	 *					The init function add previous constants, boxes and permissions into Dolibarr database.
	 *					It also creates data directories.
	 */
	function init()
	{
		$sql = array();

		return $this->_init($sql);
	}

	/**
	 *		\brief		Function called when module is disabled.
	 *              	Remove from database constants, boxes and permissions from Dolibarr database.
	 *					Data directories are not deleted.
	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}

}

?>
