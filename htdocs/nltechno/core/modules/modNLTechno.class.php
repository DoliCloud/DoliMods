<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   nltechno     Module NLTechno
 *       \brief      Module to NLTechno tools integration.
 */

/**
 *      \file       htdocs/nltechno/core/modules/modNLTechno.class.php
 *      \ingroup    nltechno
 *      \brief      Description and activation file for module NLTechno
 *		\version	$Id: modNLTechno.class.php,v 1.15 2011/01/22 10:18:00 eldy Exp $
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**     \class      modNLTechno
 *      \brief      Description and activation class for module MyModule
 */
class modNLTechno extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param		DoliDB		$db		Database handler
	 */
	function modNLTechno($db)
	{
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 101050;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'nltechno';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate NLTechno tools in dolibarr";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.2';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of png file (without png) used for this module.
		// Png file must be in theme/yourtheme/img directory under name object_pictovalue.png.
		$this->picto='generic';

		// Data directories to create when module is enabled
		$this->dirs = array();
		//$this->dirs[0] = DOL_DATA_ROOT.'/mymodule;
		//$this->dirs[1] = DOL_DATA_ROOT.'/mymodule/temp;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array();

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,1);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(2,4);	// Minimum version of Dolibarr required by module

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
		$this->rights[$r][0] = 101051; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page liens';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'liens';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 101052; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page annonces';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'annonces';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 101053; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page emailings';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'emailings';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		$this->rights[$r][0] = 101060; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Read DoliCloud informations';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'dolicloud';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'read';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = 101061; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/edit DoliCloud data';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'dolicloud';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'create';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'Admin NLTechno',
								'mainmenu'=>'nltechno',
								'url'=>'/nltechno/index.php',
								'langs'=>'',
								'position'=>200,
                				'enabled'=>'$conf->nltechno->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->liens->voir||$user->rights->nltechno->annonces->voir||$user->rights->nltechno->emailings->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'Liens externes',
								'mainmenu'=>'nltechno',
								'url'=>'/nltechno/index.php',
								'langs'=>'',
								'position'=>200,
                				'enabled'=>'$conf->nltechno->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->liens->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'Outils Petites annonces',
								'mainmenu'=>'nltechno',
								'url'=>'/nltechno/statsannonces.php',
								'langs'=>'',
								'position'=>201,
                				'enabled'=>'$conf->nltechno->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->annonces->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'Outils EMailings',
								'mainmenu'=>'nltechno',
								'url'=>'/nltechno/statsemailings.php',
								'langs'=>'',
								'position'=>202,
                				'enabled'=>'$conf->nltechno->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->emailings->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		// Example to declare a Left Menu entry:
		$this->menu[$r]=array(   'fk_menu'=>'r=0',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
                				'type'=>'left',         // This is a Left menu entry
                				'titre'=>'DoliCloud customers',
                				'mainmenu'=>'dolicloud',
                				'leftmenu'=>'dolicloud',
                				'url'=>'/nltechno/dolicloud_customers.php',
                				'langs'=>'nltechno@nltechno',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
                				'position'=>110,
                				'enabled'=>'$conf->nltechno->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
							  'perms'=>'$user->rights->nltechno->dolicloud->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
                              'target'=>'',
                              'user'=>0);             // 0=Menu for internal users, 1=external users, 2=both
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
	function init($options='')
	{
		$sql = array();

		return $this->_init($sql,$options);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql,$options);
	}

}

?>
