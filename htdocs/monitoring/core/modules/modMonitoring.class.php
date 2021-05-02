<?php
/* Copyright (C) 2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**     \defgroup   monitoring     Module Monitoring
 *      \brief      Can use Monitoring features
 */

/**
 *      \file       htdocs/monitoring/core/modules/modMonitoring.class.php
 *      \ingroup    monitoring
 *      \brief      Description and activation file for module Monitoring
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 * Description and activation class for module Monitoring
 */
class modMonitoring extends DolibarrModules
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
		$this->numero = 101310;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'monitoring';

		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Une interface et des fonctions pour realiser une supervision avec rrdtool";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto = 'generic';

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array('/monitoring/temp');
		$r=0;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("monitoring.php@monitoring");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0,-2);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("monitoring@monitoring");

		// Constants
		$this->const = array();			// List of particular constants to add when module is enabled
		//Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',0),
		//                            1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );

		// Array to add new pages in new tabs
		//$this->tabs = array('thirdparty:SMS:@ovh:/ovh/sms.php?id=__ID__');




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


		$this->rights[$r][0] = 101311;
		$this->rights[$r][1] = 'Read monitoring';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';
		$r++;
		$this->rights[$r][0] = 101312;
		$this->rights[$r][1] = 'Add/Delete probes';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'create';
		$r++;

		// Main menu entries
		$this->menus = array();         // List of menus to add
		$r=0;

		// Add here entries to declare new menus
		// Example to declare the Top Menu entry:
		$this->menu[$r]=array(   'fk_menu'=>0,            // Put 0 if this is a top menu
								  'type'=>'top',          // This is a Top menu entry
								  'titre'=>'Monitoring',
								  'mainmenu'=>'monitoring',
								  'url'=>'/monitoring/index.php',
								  'langs'=>'monitoring@monitoring',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								  'position'=>100,
								  'enabled'=>'$conf->monitoring->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								  'perms'=>'$user->rights->monitoring->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
								  'target'=>'',
								  'user'=>2);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Example to declare a Left Menu entry:
		$this->menu[$r]=array(   'fk_menu'=>'r=0',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
								  'type'=>'left',         // This is a Left menu entry
								  'titre'=>'ProbeSetup',
								  'mainmenu'=>'monitoring',
								  'url'=>'/monitoring/probes.php',
								  'langs'=>'monitoring@monitoring',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								  'position'=>100,
								  'enabled'=>'$conf->monitoring->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								  'perms'=>'$user->rights->monitoring->create',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
								  'target'=>'',
								  'user'=>2);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Example to declare a Left Menu entry:
		$this->menu[$r]=array(   'fk_menu'=>'r=0',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
								  'type'=>'left',         // This is a Left menu entry
								  'titre'=>'Reports',
								  'mainmenu'=>'monitoring',
								  'url'=>'/monitoring/index.php',
								  'langs'=>'monitoring@monitoring',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								  'position'=>100,
								  'enabled'=>'$conf->monitoring->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								  'perms'=>'$user->rights->monitoring->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
								  'target'=>'',
								  'user'=>2);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;




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
		if ($result <= 0) return $result;

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
	 * 	and create data commands must be stored in directory /mymodule/sql/
	 *	This function is called by this->init.
	 *
	 * 	@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/monitoring/sql/');
	}
}
