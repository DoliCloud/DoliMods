<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2012      Juanjo Menent		<jmenent@2byte.es>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 		\file       modLabelPrint.class.php
 * 		\defgroup   LabelPrint     Module Labels
 *      \brief      File of construction class of label print
 */

include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modLabelPrint
 *      \brief      Description and activation class for module LabelPrint
 */
class modLabelPrint extends DolibarrModules
{
	/**
	 *	Constructor. Define names, constants, directories, boxes, permissions
	 *	@param      DoliDB 	$DB      Database handler
	 */
	function modLabelPrint($DB)
	{
        global $langs,$conf;
		
        $this->db = $DB;

		// Id for modul.
		$this->numero = 40007;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'labelprint';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		$this->family = "products";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Labels print for products";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.3';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='barcode';

		// Defined if the directory /mymodule/includes/triggers/ contains triggers or not
		$this->triggers = 0;

		// Data directories to create when module is enabled.
		$this->dirs = array();
		$r=0;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("labelprint.php@labelprint");

		// Dependencies
		$this->depends = array();					// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();				// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,2);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("labelprint@labelprint");

		// Constants
		$this->const = array(0=>array('MAIN_MODULE_LABELPRINT_TABS_0','chaine','product:+labelprint:Labels:labelprint@labelprint::/labelprint/product.php?id=__ID__','',0),
		                     1=>array('MAIN_MODULE_LABELPRINT_LABELS_0','chaine','1','',0) );

		// Array to add new pages in new tabs
        $this->tabs = array('supplier_invoice:+labelprint:Labels:labelprint@labelprint:$user->rights->fournisseur->facture->lire:/labelprint/invoice_supplier.php?id=__ID__',  
        					'supplier_order:+labelprint:Labels:labelprint@labelprint:$user->rights->fournisseur->commande->lire:/labelprint/order_supplier.php?id=__ID__',
                            'product:+labelprint:Labels:labelprint@labelprint:$user->rights->produit->lire:/labelprint/product.php?id=__ID__');
        // Boxes
		// Add here list of php file(s) stored in includes/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		//Menu left into products
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=products',	
								'type'=>'left',
								'titre'=>'Labels',
								'mainmenu'=>'products',
								'leftmenu'=>'labelprint',
								'url'=>'/labelprint/product_list.php',
								'langs'=>'labelprint@labelprint',
								'position'=>100,
								'enabled'=>'$conf->global->MAIN_MODULE_LABELPRINT_TABS_3',
								'perms'=>'1',
								'target'=>'',
								'user'=>0);

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories.
	 *      @return     int             1 if OK, 0 if KO
	 */
	function init()
	{
		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted.
	 *      @return     int             1 if OK, 0 if KO
	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}


	/**
	 *		Create tables, keys and data required by module
	 * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 		and create data commands must be stored in directory /mymodule/sql/
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/labelprint/sql/');
	}
}

?>
