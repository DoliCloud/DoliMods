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
 * or see http://www.gnu.org/
 */

/**
 * 		\defgroup   SubmitEveryWhere     Module SubmitEveryWhere
 *      \brief      Descriptor of module SubmitEveryWhere.
 */

/**
 *      \file       htdocs/submiteverywhere/core/modules/modSubmitEveryWhere.class.php
 *      \ingroup    submiteverywhere
 *      \brief      Description and activation file for module SubmitEveryWhere
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *		Description and activation class for module SubmitEveryWhere
 */
class modSubmitEveryWhere extends DolibarrModules
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
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 101260;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'submiteverywhere';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "A manager to help to submit an article/scoop towards several targets (Twitter, Facebook, Digg, EMail, Major news web sites...)";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4';
		// Key used in llx_const table to save module status enabled/disabled (where NewsSubmitter is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 1;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='globe';

		// Defined if the directory /NewsSubmitter/inc/triggers/ contains triggers or not
		$this->triggers = 0;

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/NewsSubmitter/temp");
		$this->dirs = array();
		$r=0;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("submiteverywheresetuppage.php@submiteverywhere");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,1,-3);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("submiteverywhere@submiteverywhere");

		// Constants
		// Example: $this->const=array(0=>array('NewsSubmitter_MYNEWCONST1','chaine','myvalue','This is a constant to add',0),
		//                             1=>array('NewsSubmitter_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );
		$this->const = array();			// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 0 or 'allentities')

		// Array to add new pages in new tabs
		$this->tabs = array();
		// where entity can be
		// 'thirdparty'       to add a tab in third party view
		// 'intervention'     to add a tab in intervention view
		// 'supplier_order'   to add a tab in supplier order view
		// 'supplier_invoice' to add a tab in supplier invoice view
		// 'invoice'          to add a tab in customer invoice view
		// 'order'            to add a tab in customer order view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'member'           to add a tab in fundation member view
		// 'contract'         to add a tab in contract view


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
		$this->rights[$r][0] = 101261; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Read submited news';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'read';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		//$this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

        $this->rights[$r][0] = 101262;              // Permission id (must not be already used)
        $this->rights[$r][1] = 'Create/Edit/Submit news';    // Permission label
        $this->rights[$r][3] = 0;                   // Permission by default for new user (0/1)
        $this->rights[$r][4] = 'create';              // In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
        //$this->rights[$r][5] = 'level2';              // In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
        $r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=tools',		// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
									'type'=>'left',			// This is a Left menu entry
									'titre'=>'Submit Everywhere',
									'mainmenu'=>'tools',
									'leftmenu'=>'submiteverywhere',
									'url'=>'/submiteverywhere/index.php',
									'langs'=>'submiteverywhere@submiteverywhere',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>100,
									'enabled'=>'$conf->submiteverywhere->enabled',			// Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
									'perms'=>'1',			// Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>0);				// 0=Menu for internal users, 1=external users, 2=both
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=submiteverywhere',
									'type'=>'left',			// This is a Left menu entry
									'titre'=>'NewMessage',
									'url'=>'/submiteverywhere/card.php?action=create',
									'langs'=>'submiteverywhere@submiteverywhere',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>110,
									'enabled'=>'$conf->submiteverywhere->enabled',			// Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
									'perms'=>'1',			// Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>0);				// 0=Menu for internal users, 1=external users, 2=both
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=submiteverywhere',
									'type'=>'left',			// This is a Left menu entry
									'titre'=>'List',
									'url'=>'/submiteverywhere/list.php',
									'langs'=>'submiteverywhere@submiteverywhere',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>120,
									'enabled'=>'$conf->submiteverywhere->enabled',			// Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
									'perms'=>'1',			// Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>0);				// 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Exports
		$r=1;

		// Example:
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='CustomersInvoicesAndInvoiceLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		// $this->export_permission[$r]=array(array("facture","facture","export"));
		// $this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.zip'=>'Zip','s.town'=>'Town','s.fk_pays'=>'Country','s.phone'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4','s.code_compta'=>'CustomerAccountancyCode','s.code_compta_fournisseur'=>'SupplierAccountancyCode','f.rowid'=>"InvoiceId",'f.facnumber'=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"InvoiceNote",'fd.rowid'=>'LineId','fd.description'=>"LineDescription",'fd.price'=>"LineUnitPrice",'fd.tva_tx'=>"LineVATRate",'fd.qty'=>"LineQty",'fd.total_ht'=>"LineTotalHT",'fd.total_tva'=>"LineTotalTVA",'fd.total_ttc'=>"LineTotalTTC",'fd.date_start'=>"DateStart",'fd.date_end'=>"DateEnd",'fd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
		// $this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.zip'=>'company','s.town'=>'company','s.fk_pays'=>'company','s.phone'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_tx'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM ('.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'facturedet as fd, '.MAIN_DB_PREFIX.'societe as s)';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product as p on (fd.fk_product = p.rowid)';
		// $this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.rowid = fd.fk_facture';
		// $r++;
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

		$result=$this->load_tables();
        if ($result <= 0) return $result;

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
	 *		\brief		Create tables, keys and data required by module
	 * 					Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 					and create data commands must be stored in directory /NewsSubmitter/sql/
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/submiteverywhere/sql/');
	}
}

?>
