<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 	   Jean-François FERRY <jfefe@aternatik.fr>
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

/**     \defgroup   ovh     Module Ovh
 *      \brief      Permet de s'interface avec les service fourni par OVH (SMS, API,...)
 */

/**
 *      \file       htdocs/ovh/core/modules/modOvh.class.php
 *      \ingroup    ovhsms
 *      \brief      Description and activation file for module Ovh
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *	Description and activation class for module Ovh
 */
class modOvh extends DolibarrModules
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
		$this->numero = 101330;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'ovh';

		// It is used to group modules in module setup page
		$this->family = "interface";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Add some features to use OVH interfaces (Send SMS with a subscription to OVH SMS API and make Click2Dial with OVH SIP server)";
        $this->editor_name = 'NLTechno';
        $this->editor_url = 'https://www.nltechno.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '4.0.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto = 'ovh@ovh';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		$this->module_parts = array(
		    'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		    'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		    'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		    'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		    'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		    'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		    'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		    'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		    'css' => array('/ovh/css/ovh.css.php'),	            // Set this to relative path of css file if module has its own css file
		    'js' => array(),                                    // Set this to relative path of js file if module must load a js on all pages
		    'hooks' => array() 	                                // Set here all hooks context managed by module. You can also set hook context 'all'
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();
		$r=0;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("ovh_setup.php@ovh");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,4);					    // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(6,0,-3);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("ovh@ovh");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add', 1, 'allentities', 1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add', 0, 'current', 0)
		$this->const = array(0=>array('MAIN_MODULE_OVH_SMS','chaine','ovh','This is to enable OVH SMS module',0,'current',1),
		                     1=>array('MAIN_SMS_SENDMODE','chaine','ovh','This is to enable OVH SMS engine',0,'current',1),
		                     2=>array('MAIN_SMS_DEBUG','chaine','1','This is to enable OVH SMS debug',1,'allentities',0),
							 3=>array('MAIN_MENU_ENABLE_MODULETOOLS','chaine','1','To enable module tools entry',0,'allentities',1)
		);			// List of particular constants to add when module is enabled

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:langfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  // To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:langfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  // To add another new tab identified by code tabname2
        //                              'objecttype:-tabname');                                                     // To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'thirdparty'       to add a tab in third party view
		// 'intervention'     to add a tab in intervention view
		// 'order_supplier'   to add a tab in supplier order view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'invoice'          to add a tab in customer invoice view
		// 'order'            to add a tab in customer order view
		// 'product'          to add a tab in product view
		// 'stock'            to add a tab in stock view
		// 'propal'           to add a tab in propal view
		// 'member'           to add a tab in fundation member view
		// 'contract'         to add a tab in contract view
		// 'user'             to add a tab in user view
		// 'group'            to add a tab in group view
		// 'contact'          to add a tab in contact view
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		$this->tabs = array('thirdparty:+tabSMS:SMS:ovh@ovh:$user->rights->ovh->send:/ovh/sms_thirdparty.php?id=__ID__',
		                    //'contact:+tabSMS:SMS:ovh@ovh:$user->rights->ovh->send:/ovh/sms_contact.php?id=__ID__',  // This is done from thirdparty tab
		                    'member:+tabSMS:SMS:ovh@ovh:$user->rights->ovh->send:/ovh/sms_member.php?id=__ID__');




		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Add here list of php file(s) stored in includes/boxes that contains class to show a box.
		// Example:
		//$this->boxes[$r][1] = "myboxa.php";
		//$r++;
		//$this->boxes[$r][1] = "myboxb.php";
		//$r++;


		// Cronjobs (List of cron jobs entries to add when module is enabled)
		$this->cronjobs = array(
			0=>array('label'=>'Snapshot OVH', 'jobtype'=>'method', 'class'=>'/ovh/class/ovhserver.class.php', 'objectname'=>'OvhServer', 'method'=>'createSnapshot', 'parameters'=>'projectid, serverid, serverlabel', 'comment'=>'Ask a snaspshot request of a server. "projectid" and "serverid" are defined by OVH. "serverlabel" is a free text. Warning: This increase your OVH monthly billing.', 'frequency'=>4, 'unitfrequency'=>604800, 'status'=>0, 'test'=>true)
		);

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;


		$this->rights[$r][0] = 101331;
		$this->rights[$r][1] = 'Send a SMS';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'send';
		$r++;

		$this->rights[$r][0] = 101332;
		$this->rights[$r][1] = 'Import Invoice';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'importinvoice';
		$r++;

		$this->rights[$r][0] = 101333;
		$this->rights[$r][1] = 'Administration of OVH servers';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'sysadmin';
		$r++;

		// Main menu entries
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=tools',		    // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
									'type'=>'left',			                // This is a Left menu entry
									'titre'=>'OvhInvoiceImportShort',
									'url'=>'/ovh/importovhinvoice.php',
									'langs'=>'ovh@ovh',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>500,
									'enabled'=>'$conf->ovh->enabled',  // Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
									'perms'=>'$user->rights->ovh->importinvoice',	// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=tools',		    // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
									'type'=>'left',			                // This is a Left menu entry
									'titre'=>'OvhServers',
									'url'=>'/ovh/ovh_listinfoserver.php',
									'langs'=>'ovh@ovh',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>500,
									'enabled'=>'$conf->ovh->enabled',  // Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
									'perms'=>'$user->rights->ovh->sysadmin',	// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Exports
		$r=1;


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

		//$result=$this->load_tables();

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
		return $this->_load_tables('/ovh/sql/');
	}

}

