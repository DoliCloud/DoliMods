<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *		\defgroup   FileManager     Module FileManager
 *      \brief      Module to get a file browser and manager into Dolibarr
 */

/**
 *      \file       htdocs/filemanager/core/modules/modFileManager.class.php
 *      \ingroup    filemanager
 *      \brief      Fichier de description et activation du module FileManager
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";

/**
 *		Class to describe moule filemanager
 */
class modFileManager extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		$this->numero = 101200;

		$this->family = "ecm";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "A file manager";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com';
		$this->version = '6.0.0';                        // 'experimental' or 'dolibarr' or version
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto='filemanager@filemanager';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		$this->module_parts = array(
		//                        	'triggers' => 0,                                 // Set this to 1 if module has its own trigger directory
		//							'login' => 0,                                    // Set this to 1 if module has its own login method directory
		//							'substitutions' => 0,                            // Set this to 1 if module has its own substitution function file
		//							'menus' => 0,                                    // Set this to 1 if module has its own menus handler directory
		//							'barcode' => 0,                                  // Set this to 1 if module has its own barcode directory
		//							'models' => 0,                                   // Set this to 1 if module has its own models directory
									'css' => '/filemanager/css/filemanager.css.php',       // Set this to relative path of css if module has its own css file
		//							'hooks' => array('hookcontext1','hookcontext2')  // Set here all hooks context managed by module
		);

		// Data directories to create when module is enabled
		$this->dirs = array("/filemanager/temp");

		// Config pages
		//-------------
		$this->config_page_url = array("filemanager.php@filemanager");

		// Dependancies
		//-------------
		$this->depends = array();
		$this->requiredby = array();
		$this->phpmin = array(4,1);                    // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,6,-2);  // Minimum version of Dolibarr required by module
		$this->langfiles = array("companies","filemanager@filemanager");

		// Constantes
		//-----------
		$this->const = array();

		// New pages on tabs
		// -----------------
		$this->tabs = array();

		// Boxes
		//------
		$this->boxes = array();

		// Permissions
		//------------
		$this->rights = array();
		$this->rights_class = 'filemanager';
		$r=0;

		// $this->rights[$r][0]     Id permission (unique tous modules confondus)
		// $this->rights[$r][1]     Libelle par defaut si traduction de cle "PermissionXXX" non trouvee (XXX = Id permission)
		// $this->rights[$r][2]     Non utilise
		// $this->rights[$r][3]     1=Permis par defaut, 0=Non permis par defaut
		// $this->rights[$r][4]     Niveau 1 pour nommer permission dans code
		// $this->rights[$r][5]     Niveau 2 pour nommer permission dans code
		// $r++;

		$this->rights[$r][0] = 101201;
		$this->rights[$r][1] = 'Read/Browse directories and files from the file manager';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';
		$this->rights[$r][5] = '';
		$r++;

		$this->rights[$r][0] = 101202;
		$this->rights[$r][1] = 'Can manage directories or files (upload/edit/delete) from the file manager';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'create';
		$this->rights[$r][5] = '';
		$r++;

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
		$this->menu[$r]=array('fk_menu'=>0,
													'type'=>'top',
													'titre'=>'FileManager',
													'mainmenu'=>'filemanager',
													'url'=>'/filemanager/index.php',
													'langs'=>'filemanager@filemanager',
													'position'=>100,
													'perms'=>'$user->rights->filemanager->read',
													'enabled'=>'$conf->filemanager->enabled',
													'target'=>'',
													'user'=>2);
		$r++;


		// Exports
		//--------
		$r=0;

		// $this->export_code[$r]          Code unique identifiant l'export (tous modules confondus)
		// $this->export_label[$r]         Libelle par defaut si traduction de cle "ExportXXX" non trouvee (XXX = Code)
		// $this->export_permission[$r]    Liste des codes permissions requis pour faire l'export
		// $this->export_fields_sql[$r]    Liste des champs exportables en codif sql
		// $this->export_fields_name[$r]   Liste des champs exportables en codif traduction
		// $this->export_sql[$r]           Requete sql qui offre les donnees a l'export
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
		// Prevent pb of modules not correctly disabled
		//$this->remove($options);

		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql, $options);
	}

	/**
	 *	Fonction appelee lors de la desactivation d'un module.
	 *  Supprime de la base les constantes, boites et permissions du module.
	 *
	 *	@param	string	$options		Options when disabling module
	 *	@return	void
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
		return $this->_load_tables('/filemanager/sql/');
	}
}
