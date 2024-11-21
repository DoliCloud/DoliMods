<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**     \defgroup   scanner     Module Scanner
 *      \brief      Module descriptor of scanner module.
 */

/**
 *      \file       htdocs/scanner/core/modules/modScanner.class.php
 *      \ingroup    scanner
 *      \brief      Description and activation file for module Scanner
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 *  Description and activation class for module Scanner
 */
class modScanner extends DolibarrModules
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
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 101300;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'scanner';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "interface";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Add menu entry to pilot a scanner plugged to a Dolibarr server (Linux server only)";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com?origin=dolimods';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='scanner@scanner';

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/scanner/temp");
		$this->dirs = array("/scanner/temp");
		$r=0;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("scannersetuppage.php@scanner");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(17, 0, -4);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("scanner@scanner");

		// Constants
		$this->const = array();			// List of particular constants to add when module is enabled
		//Example: $this->const=array(0=>array('MODULE_MY_NEW_CONST1','chaine','myvalue','This is a constant to add',0),
		//                            1=>array('MODULE_MY_NEW_CONST2','chaine','myvalue','This is another constant to add',0) );

		// Array to add new pages in new tabs
		//$this->tabs = array('entity:Title:@scanner:/scanner/mynewtab.php?id=__ID__');
		// where entity can be
		// 'thirdparty'       to add a tab in third party view
		// 'intervention'     to add a tab in intervention view
		// 'supplier_order'   to add a tab in supplier order view
		// 'supplier_invoice' to add a tab in supplier invoice view
		// 'invoice'          to add a tab in customer invoice view
		// 'order'            to add a tab in customer order view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'member'           to add a tab in fundation member view


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
		$this->menu = array();          // List of menus to add
		$r=0;

		// Add here entries to declare new menus
		// Example to declare the Top Menu entry:
		$this->menu[$r]=array(		'fk_menu'=>0,			                // Put 0 if this is a top menu
									'type'=>'top',			                // This is a Top menu entry
									'titre'=>'Scanner',
									'mainmenu'=>'scanner',
									'url'=>'/scanner/index.php',
									'langs'=>'scanner@scanner',	            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>100,
									'enabled'=>'$conf->scanner->enabled',	// Define condition to show or hide menu entry. Use '$conf->scanner->enabled' if entry must be visible if module is enabled.
									'perms'=>'1',			                // Use 'perms'=>'$user->rights->scanner->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Example to declare a Left Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'r=0',		                // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Scanner left menu 1',
		//							'mainmenu'=>'scanner',
		//							'url'=>'/scanner/pagelevel1.php',
		//							'langs'=>'mylangfile',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'1',			                // Define condition to show or hide menu entry. Use '$conf->scanner->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->scanner->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		//
		// Example to declare another Left Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',		                // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Scanner left menu 2',
		//							'mainmenu'=>'scanner',
		//							'url'=>'/scanner/pagelevel2.php',
		//							'langs'=>'mylangfile',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'1',			                // Define condition to show or hide menu entry. Use '$conf->scanner->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->scanner->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;


		// Exports
		$r=1;
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

		return $this->_remove($sql, $options);
	}


	/**
	 *	Create tables, keys and data required by module
	 * 	Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 	and create data commands must be stored in directory /scanner/sql/
	 *	This function is called by this->init.
	 *
	 * 	@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/scanner/sql/');
	}
}
