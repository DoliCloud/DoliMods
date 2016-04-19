<?php
/* Copyright (C) 2013-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**     \defgroup   ecotaxdeee		Module ecotax deee
 *		\brief      Module ecotax deee.
 */

/**
 *		\file       htdocs/ecotaxdeee/core/modules/modEcoTaxDeee.class.php
 *		\ingroup    exotaxdee
 *		\brief      Description and activation file for module EcoTax
 */

include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *	Description and activation class for module EcoTax
 */
class modEcoTaxDeee extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param		DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		global $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 101380;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'ecotaxdeee';

		// Family can be 'crm','financial','hr','projects','product','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "products";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Module to add ecotax lines automatically";
        $this->editor_name = 'NLTechno';
        $this->editor_url = 'http://www.nltechno.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.7.0';
		// Key used in llx_const table to save module status enabled/disabled
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=other)
		$this->special = 0;
		// Name of png file (without png) used for this module.
		// Png file must be in theme/yourtheme/img directory under name object_pictovalue.png.
		$this->picto='product';

        $this->module_parts = array(
        						'triggers' => 1,
        						'hooks' => array('pdfgeneration')
        						);

        // Data directories to create when module is enabled.
		$this->dirs = array();

		// Relative path to module style sheet if exists. Example: '/Composition/mycss.css'.
		$this->style_sheet = '';

		// Config pages. Put here list of php page names stored in admin directory used to setup module.
		$this->config_page_url = array('index.php@ecotaxdeee');

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,1);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,4,2);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("ecotaxdeee@ecotaxdeee");

		// Constants
		// List of particular constants to add when module is enabled
		//Example: $this->const=array(0=>array('MODULE_MY_NEW_CONST1','chaine','myvalue','This is a constant to add',0),
		//                            1=>array('MODULE_MY_NEW_CONST2','chaine','myvalue','This is another constant to add',0) );
		$this->const=array();

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

		// Exports
		$r=1;


		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		// Example:
		// This is to declare the Top Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			// Put 0 if this is a top menu
		//							'type'=>'top',			// This is a Top menu entry
		//							'titre'=>'Title top menu',
		//							'mainmenu'=>'Composition',
		//							'leftmenu'=>'1',		// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
		//							'url'=>'/comm/action/index.php',
		//							'langs'=>'mylangfile',	// Lang file to use (without .lang) by module
		//							'position'=>100,
		//							'perms'=>'$user->rights->Composition->level1->level2',		// Use 'perms'=>'1' if you want your menu with no permission rules
		//							'target'=>'',
		//							'user'=>0);				// 0=menu for all users
		// $r++;
		//
		// This is to declare a Left Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'r=0',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		//							'type'=>'left',			// This is a Left menu entry
		//							'titre'=>'Title left menu',
		//							'mainmenu'=>'Composition',
		//							'url'=>'/comm/action/index2.php',
		//							'langs'=>'mylangfile',	// Lang file to use (without .lang) by module
		//							'position'=>100,
		//							'perms'=>'$user->rights->Composition->level1->level2',		// Use 'perms'=>'1' if you want your menu with no permission rules
		//							'target'=>'',
		//							'user'=>0);				// 0=menu for all users
		// $r++;

	}

	/**
	 *		\brief      Function called when module is enabled.
	 *					The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *					It also creates data directories.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function init()
	{
		global $langs;

		$sql = array();


	    // Create extrafields
    	include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$result1=$extrafields->addExtraField('ecotaxdeee', $langs->trans("Ecotax"), 'double', 1, '24,8', 'product', 0, 0, 0, '', 1);
		if (! $result1)
		{
			$this->error=$extrafields->error;
			return -1;
		}


		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *		\brief		Function called when module is disabled.
	 *              	Remove from database constants, boxes and permissions from Dolibarr database.
	 *					Data directories are not deleted.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}


	/**
	 *		\brief		Create tables and keys required by module
	 * 					Files Composition.sql and Composition.key.sql with create table and create keys
	 * 					commands must be stored in directory /composition/sql/
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/ecotax/sql/');
	}
}

?>
