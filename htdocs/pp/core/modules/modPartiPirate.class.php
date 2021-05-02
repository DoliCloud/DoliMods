<?php
/* Copyright (C) 2010-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**     \defgroup   partipirate		Module PartiPirate
 *      \brief      Module for partipirate server
 */

/**
 *       \file       htdocs/partipirate/core/modules/modpartipirate.class.php
 *       \ingroup    ftp
 *       \brief      Description and activation file for module partipirate
 */

include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 * 	Description and activation class for module PartiPirate
 */
class modPartiPirate extends DolibarrModules
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
		$this->numero = 101480;

		// Family can be 'crm','financial','hr','projects','product','ecm','technic','other'
		// It is used to sort modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is id value)
		$this->description = "Add specific features to PartiPirate";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4';
		// Key used in llx_const table to save module status enabled/disabled (XXX is id value)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of png file (without png) used for this module
		$this->picto='partipirate@pp';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		$this->module_parts = array(
				//						'triggers' => 0,                                 // Set this to 1 if module has its own trigger directory
				//						'login' => 0,                                    // Set this to 1 if module has its own login method directory
				//						'substitutions' => 0,                            // Set this to 1 if module has its own substitution function file
				//						'menus' => 0,                                    // Set this to 1 if module has its own menus handler directory
				//						'barcode' => 0,                                  // Set this to 1 if module has its own barcode directory
				//						'models' => 0,                                   // Set this to 1 if module has its own models directory
				//						'css' => '/filemanager/css/partipirate.css.php',   // Set this to relative path of css if module has its own css file
										'hooks' => array()  // Set here all hooks context managed by module
		);

		// Data directories to create when module is enabled
		$this->dirs = array('/partipirate/invoices','/partipirate/orders','/partipirate/proposals','/partipirate/supplier_orders','/partipirate/supplier_invoices','/partipirate/temp');

		// Config pages. Put here list of php page names stored in admin directory used to setup module
		$this->config_page_url = array('partipirate.php@pp');

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);                 // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,2,-3);  // Minimum version of Dolibarr required by module
		$this->langfiles = array("partipirate@pp");

		// Constants
		// Example: $this->const=array(0=>array('MODULE_MY_NEW_CONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MODULE_MY_NEW_CONST2','chaine','myvalue','This is another constant to add',1) );
		$this->const = array();

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
		$this->rights_class = 'partipirate';	// Permission key
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		$this->rights[$r][0] = 101481; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Lire adhérents/cotisations';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'read';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		//$this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		$this->rights[$r][0] = 101482; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Saisir adhérent/cotisation';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'write';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		//$this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		// Menus
		//------
		$this->menus = array();			// List of menus to add
		$r=0;

		// Top menu
		$this->menu[$r]=array('fk_menu'=>0,
							  'type'=>'top',
							  'titre'=>'PartiPirate',
							  'mainmenu'=>'partipirate',
							  'url'=>'/partipirate/index.php',
							  'langs'=>'partipirate@pp',
							  'position'=>100,
							  'perms'=>'$user->rights->partipirate->read || $user->rights->partipirate->write',
							  'enabled'=>1,
							  'target'=>'',
							  'user'=>2);			// 0=Menu for internal users, 1=external users, 2=both
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
}
