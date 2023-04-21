<?php
/* Copyright (C) 2019 Laurent Destailleur		<eldy@users.sourceforge.net>
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
 * 	\defgroup   ifttt     Module IFTTT
 *  \brief      Descriptor file for IFTTT module
 *  \file       htdocs/core/modules/modIFTTT.class.php
 *  \ingroup    ifttt
 *  \brief      Description and activation file for module IFTTT
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module IFTTT
 */
class modIFTTT extends DolibarrModules
{
	/**
	 *  Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *  @param      DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $langs,$conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 2620;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'ifttt';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "interface";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "IFTTT interface";
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or 'dolibarr_deprecated' or version
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Can be enabled / disabled only in the main company with superadmin account
		$this->core_enabled = 1;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='technic';

		$this->module_parts = array();

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/api/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into api/admin directory, to use to setup module.
		$this->config_page_url = array("index.php@ifttt");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,4);		// Minimum version of PHP required by module
		$this->langfiles = array("other");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@api:$user->rights->ifttt->read:/api/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
		//                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@api:$user->rights->othermodule->read:/api/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		//                              'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view
		$this->tabs = array();

		// Dictionaries
		$this->dictionaries=array();

		// Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
		$this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus
		//
		// Example to declare a new Top Menu entry and its Left menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			                // Put 0 if this is a top menu
		//							'type'=>'top',			                // This is a Top menu entry
		//							'titre'=>'Api top menu',
		//							'mainmenu'=>'api',
		//							'leftmenu'=>'api',
		//							'url'=>'/api/pagetop.php',
		//							'langs'=>'mylangfile@api',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'isModEnabled("ifttt")',
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->ifttt->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		//
		// Example to declare a Left Menu entry into an existing Top menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=xxx',		    // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Api left menu',
		//							'mainmenu'=>'xxx',
		//							'leftmenu'=>'api',
		//							'url'=>'/api/pagelevel2.php',
		//							'langs'=>'mylangfile@api',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'isModEnabled("ifttt")',
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->ifttt->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;


		// Exports
		$r=1;
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$sql = array();

		$result=$this->_load_tables('/ifttt/sql/');

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted.
	 *
	 *  @param string $options Options when enabling module ('', 'noboxes')
	 *  @return int 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		// Remove old constants with entity fields different of 0
		$sql = array(
			"DELETE FROM ".MAIN_DB_PREFIX."const WHERE name = ".$this->db->encrypt('MAIN_MODULE_IFTTT', 1),
			"DELETE FROM ".MAIN_DB_PREFIX."const WHERE name = ".$this->db->encrypt('IFTTT_PRODUCTION_MODE', 1)
		);

		return $this->_remove($sql, $options);
	}
}
