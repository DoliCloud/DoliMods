<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**     \defgroup   bittorrent   Module BitTorrent
 *      \brief      Module descriptor ofr Bittorrent module.
 */

/**
 *      \file       htdocs/bittorrent/core/modules/modBitTorrent.class.php
 *      \ingroup    bittorrent
 *      \brief      Description and activation file for module MyModule
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 *		Description and activation class for module RiverBitTorrent
 */
class modBitTorrent extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		global $user;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 101000;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'bittorrent';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Make your Dolibarr able to be a BitTorrent tracker (and serve .torrent files)";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of png file (without png) used for this module.
		// Png file must be in theme/yourtheme/img directory under name object_pictovalue.png.
		$this->picto='bittorrent@bittorrent';

		// Data directories to create when module is enabled.
		$this->dirs = array("/bittorrent/torrents","/bittorrent/rss","/bittorrent/files");

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array();

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,3);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("bittorrent");

		// Constants
		$this->const = array();			// List of particular constants to add when module is enabled
		//Example: $this->const=array(0=>array('MODULE_MY_NEW_CONST1','chaine','myvalue','This is a constant to add',0),
		//                            1=>array('MODULE_MY_NEW_CONST2','chaine','myvalue','This is another constant to add',0) );

		// New pages on tabs
		$this->tabs = array();


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
		$this->menu[$r]=array(	'fk_menu'=>0,			// Put 0 if this is a top menu
									'type'=>'top',			// This is a Top menu entry
									'titre'=>'BitTorrent',
									'mainmenu'=>'bittorrent',
									'url'=>'/bittorrent/admin.php',
									'langs'=>'bittorrent',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>100,
									'enabled'=>'isModEnabled("bittorent")',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
									'perms'=>'1',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>2);				// 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Example to declare a Left Menu entry:
		$this->menu[$r]=array(	'fk_menu'=>'r=0',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
									'type'=>'left',			// This is a Left menu entry
									'titre'=>'Admin',
									'mainmenu'=>'bittorrent',
									'url'=>'/bittorrent/admin.php',
									'langs'=>'bittorrent',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'enabled'=>'isModEnabled("bittorent")',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
									'position'=>100,
									'perms'=>$user->admin,			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>2);				// 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Example to declare another Left Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		//							'type'=>'left',			// This is a Left menu entry
		//							'titre'=>'MyModule left menu 2',
		//							'mainmenu'=>'mymodule',
		//							'url'=>'/mymodule/pagelevel2.php',
		//							'langs'=>'mylangfile',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'perms'=>'1',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				// 0=Menu for internal users, 1=external users, 2=both
		// $r++;


		// Exports
		$r=1;
	}

	/**
	 *	Function called when module is enabled.
	 *	The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *	It also creates data directories
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	function init($options = '')
	{
		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql, $options);
	}

	/**
	 *	Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *	Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}


	/**
	 *	Create tables and keys required by module
	 * 	Files mymodule.sql and mymodule.key.sql with create table and create keys
	 * 	commands must be stored in directory /mymodule/sql/
	 *	This function is called by this->init.
	 *
	 * 	@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/bittorrent/sql/');
	}
}
