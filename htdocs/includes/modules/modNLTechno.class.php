<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   nltechno     Module NLTechno
        \brief      Module to NLTechno tools integration.
*/

/**
        \file       htdocs/includes/modules/modNLTechno.class.php
        \ingroup    nltechno
        \brief      Description and activation file for module NLTechno
		\version	$Id: modNLTechno.class.php,v 1.7 2008/05/22 00:11:15 eldy Exp $
*/

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**     \class      modNLTechno
        \brief      Description and activation class for module MyModule
*/

class modNLTechno extends DolibarrModules
{

    /**
    *   \brief      Constructor. Define names, constants, directories, boxes, permissions
    *   \param      DB      Database handler
    */
	function modNLTechno($DB)
	{
		$this->db = $DB;
		
		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 11000;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'nltechno';
		
		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page 
		$this->family = "other";		
		// Module title used if translation string 'ModuleXXXName' not found (XXX is value MyModule)
		$this->name = "NLTechno";	
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate NLTechno tools in dolibarr";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0';    
		// Key used in llx_const table to save module status enabled/disabled (XXX is value MyModule)
		$this->const_name = 'MAIN_MODULE_NLTECHNO';
		// Where to store the module in setup page (0=common,1=interface,2=other)
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
		$this->rights[$r][0] = 11001; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page liens';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'liens';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 11002; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page annonces';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'annonces';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 11003; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page emailings';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'emailings';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		
		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'Admin NLTechno',
								'mainmenu'=>'nltechno',
								'leftmenu'=>'0',	// To say to not overwrite menu in pre.inc.php by dynamic database menu
								'url'=>'/nltechno/index.php',
								'langs'=>'',
								'position'=>100,
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
								'position'=>100,
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
								'position'=>101,
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
								'position'=>102,
								'perms'=>'$user->rights->nltechno->emailings->voir',
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
