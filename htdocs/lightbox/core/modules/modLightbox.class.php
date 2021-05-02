<?php
/* Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**
 * 	\defgroup   lightbox		Module Ligthbox
 * 	\brief      Module for lightbox
 * 	\file       htdocs/lightbox/core/modules/modLightbox.class.php
 * 	\ingroup    lightbox
 * 	\brief      Description and activation file for module Lightbox
 */

include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 * 	Description and activation class for module Memcached
 */
class modLightbox extends DolibarrModules
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
		// Use here a free id.
		$this->numero = 101230;

		// Family can be 'crm','financial','hr','projects','product','ecm','technic','other'
		// It is used to sort modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is id value)
		$this->description = "Add lightbox onto images";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'development';
		// Key used in llx_const table to save module status enabled/disabled (XXX is id value)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of png file (without png) used for this module
		$this->picto='technic';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 // Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    // Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            // Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    // Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    // Set this to 1 if module has its own theme directory (core/theme)
		//                        	'tpl' => 0,                                      // Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  // Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   // Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => '/mymodule/css/mymodule.css.php',       // Set this to relative path of css if module has its own css file
		//							'js' => '/mymodule/js/mymodule.js',              // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  // Set here all hooks context managed by module
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@mymodule')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
			'css' => '/lightbox/includes/jquery/plugins/lightbox/css/jquery.lightbox-0.5.css',
			'js' => array('/lightbox/includes/jquery/plugins/lightbox/js/jquery.lightbox-0.5.min.js','/lightbox/core/js/dollightbox.js')
		);

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Config pages. Put here list of php page names stored in admin directory used to setup module
		$this->config_page_url = array();

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);                 // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,2,-2);  // Minimum version of Dolibarr required by module
		$this->langfiles = array();

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
		$this->rights_class = 'lightbox';	// Permission key
		$this->rights = array();		// Permission array used by this module


		// Menus
		//------
		$this->menus = array();			// List of menus to add
		$r=0;

		// Top menu
		/*$this->menu[$r]=array('fk_menu'=>0,
							  'type'=>'top',
							  'titre'=>'FTP',
							  'mainmenu'=>'ftp',
							  'url'=>'/ftp/index.php',
							  'langs'=>'ftp',
							  'position'=>100,
							  'perms'=>'$user->rights->ftp->read || $user->rights->ftp->write || $user->rights->ftp->setup',
							  'enabled'=>1,
							  'target'=>'',
							  'user'=>2);			// 0=Menu for internal users, 1=external users, 2=both
		$r++;
		*/
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
