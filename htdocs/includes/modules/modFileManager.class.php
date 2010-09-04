<?php
/* Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *		\defgroup   FileManager     Module FileManager
 *      \brief      Module to get a file browser and manager into Dolibarr
 *		\brief		$Id: modFileManager.class.php,v 1.6 2010/09/04 15:49:00 eldy Exp $
 */

/**
 *      \file       htdocs/includes/modules/modFileManager.class.php
 *      \ingroup    FileManager
 *      \brief      Fichier de description et activation du module FileManager
 */
include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");

/**
 *      \class      modFileManager
 *      \brief      Classe de description et activation du module Adherent
 */
class modFileManager extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modFileManager($DB)
	{
		$this->db = $DB;
		$this->numero = 10600;

		$this->family = "ecm";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "A file manager";
		$this->version = '1.0';                        // 'experimental' or 'dolibarr' or version
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='dir';

		// Data directories to create when module is enabled
		$this->dirs = array("/filemanager/temp");

		// Relative path to module style sheet if exists. Example: '/mymodule/css/mycss.css'.
		$this->style_sheet = '/filemanager/css/filemanager.css.php';

		// Config pages
		//-------------
		$this->config_page_url = array("filemanager.php@filemanager");

		// Dependancies
		//-------------
		$this->depends = array();
		$this->requiredby = array();
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

		$this->rights[$r][0] = 10601;
		$this->rights[$r][1] = 'Read/Browse directories and files from the file manager';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';
		$this->rights[$r][5] = '';
		$r++;

        $this->rights[$r][0] = 10602;
        $this->rights[$r][1] = 'Delete directories or files from the file manager';
        $this->rights[$r][2] = 'w';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'delete';
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
		//							'leftmenu'=>'1',		// Use 1 if you also want to add left menu entries using this descriptor.
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
													'leftmenu'=>'0',		// Use 1 if you also want to add left menu entries using this descriptor.
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
	 *   \brief      Fonction appelee lors de l'activation du module. Insere en base les constantes, boites, permissions du module.
	 *               Definit egalement les repertoires de donnees a creer pour ce module.
	 *	\param		options		Options when enabling module
	 */
	function init($options='')
	{
		// Prevent pb of modules not correctly disabled
		//$this->remove($options);

		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql,$options);
	}

	/**
	 *	\brief      Fonction appelee lors de la desactivation d'un module.
	 *              Supprime de la base les constantes, boites et permissions du module.
	 *	\param		options		Options when disabling module
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql,$options);
	}

	/**
	 *		\brief		Create tables, keys and data required by module
	 * 					Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 					and create data commands must be stored in directory /mymodule/sql/
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/filemanager/sql/');
	}

}
?>
