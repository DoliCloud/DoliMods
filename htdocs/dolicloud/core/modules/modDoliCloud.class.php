<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   dolicloud     Module DoliCloud
 *      \brief      Module to DoliCloud tools integration.
 */

/**
 *      \file       htdocs/google/core/modules/modDoliCloud.class.php
 *      \ingroup    dolicloud
 *      \brief      Description and activation file for module DoliCloud
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 *	Description and activation class for module DoliCloud
 */
class modDoliCloud extends DolibarrModules
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
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 101900;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'dolicloud';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate DoliCloud tools in dolibarr";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com?origin=dolimods';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='technic';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		$this->module_parts = array('triggers' => 1, 'hooks' => array('toprightmenu'));

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
		$this->need_dolibarr_version = array(17, 0, -4);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("dolicloud@dolicloud");

		// Constants
		$this->const = array();			// List of parameters

		// Tabs
		$this->tabs = array();
		/*$this->tabs = array('thirdparty:+gmaps:GMaps:@google:$conf->google->enabled&&$conf->global->GOOGLE_ENABLE_GMAPS:/google/gmaps.php?mode=thirdparty&id=__ID__',
							'contact:+gmaps:GMaps:@google:$conf->google->enabled&&$conf->global->GOOGLE_ENABLE_GMAPS_CONTACTS:/google/gmaps.php?mode=contact&id=__ID__',
							'member:+gmaps:GMaps:@google:$conf->google->enabled&&$conf->global->GOOGLE_ENABLE_GMAPS_MEMBERS:/google/gmaps.php?mode=member&id=__ID__',
						);*/
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

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
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
