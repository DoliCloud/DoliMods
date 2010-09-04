<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   webmail     Module WebMail
 *      \brief      Module to WebMail tools integration.
 */

/**
 *      \file       htdocs/includes/modules/modWebMail.class.php
 *      \ingroup    webmail
 *      \brief      Description and activation file for module WebMail
 *		\version	$Id: modWebMail.class.php,v 1.3 2010/09/04 16:16:35 eldy Exp $
 */
include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**		\class      modWebMail
 *      \brief      Description and activation class for module WebMail
 */
class modWebMail extends DolibarrModules
{

	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	function modWebMail($DB)
	{
		$this->db = $DB;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 9000;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'webmail';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "projects";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate the emmbended WebMail (RoundCube)";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '2.9';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/images directory, use this->picto=DOL_URL_ROOT.'/module/images/file.png'
		$this->picto='email';

		// Data directories to create when module is enabled
		$this->dirs = array();
		//$this->dirs[0] = DOL_DATA_ROOT.'/mymodule;
		//$this->dirs[1] = DOL_DATA_ROOT.'/mymodule/temp;

		// Relative path to module style sheet if exists
		$this->style_sheet = '/webmail/css/webmail.css.php';

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array('webmail.php@webmail');

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0,-1);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("webmail@webmail");

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
		// $this->rights[$r][0] = 2000; 				// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'MenuWebMail',
								'mainmenu'=>'webmail',
								'leftmenu'=>'0',	// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
								'url'=>'/webmail/index.php',
								'langs'=>'webmail',
								'position'=>200,
								'enabled'=>'$conf->webmail->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
								'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>0);
		$r++;

		/*
		 $this->menu[$r]=array(	'fk_menu'=>'tools',
		 'type'=>'left',
		 'titre'=>'MenuLeftWebMail',
		 'mainmenu'=>'webmail',
		 'leftmenu'=>'1',	// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
		 'url'=>'/webmail/xxx.php',
		 'langs'=>'webmail',
		 'position'=>200,
		 'enabled'=>'$conf->webmail->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
		 'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
		 'target'=>'',
		 'user'=>0);
		 $r++;
		 */
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
