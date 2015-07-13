<?php
/* Copyright (C) 2010-2011 Regis Houssin  <regis@dolibarr.fr>
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
 *      \defgroup   compta       Ventilation module
 *      \brief      Module to manage breakdown
 *       \file       htdocs/includes/modules/modVentilation.class.php
 *       \ingroup    compta
 *       \brief      Fichier de description et activation du module Ventilation
 */

include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *       \class      modVentilation
 *       \brief      Classe de description et activation du module Ventilation
 */
class modVentilation extends DolibarrModules
{
	/**
	 *		\brief	Constructeur. definit les noms, constantes et boites
	 * 		\param	DB	handler d'acces base
	 */
	function __construct ($db)
	{
		$this->db = $db;
		$this->numero = 61000;

		$this->family = "financial";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion des ventilations";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.4.0';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		//$this->picto = '';
		
		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		//$this->triggers = 1;

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Config pages
		$this->config_page_url = array('index.php@ventilation');
		
		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,2);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,3);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("ventilation@ventilation");

		// Constantes
		$this->const = array();
		$this->const[2] = array("VENTILATION_ACCOUNT_SUSPENSE","chaine","47100000");
		$this->const[3] = array("VENTILATION_SELL_JOURNAL","chaine","VTE");
		$this->const[4] = array("VENTILATION_PURCHASE_JOURNAL","chaine","ACH");
		$this->const[5] = array("VENTILATION_BANK_JOURNAL","chaine","BNK");
		$this->const[6] = array("VENTILATION_SOCIAL_JOURNAL","chaine","SOC");

		// Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();
		
		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$this->menu[$r]=array('fk_menu'=>0,
								'type'=>'top',
								'titre'=>'Ventilation',
								'mainmenu'=>'ventilation',
								'leftmenu'=>'1',
								'url'=>'/ventilation/index.php',
								'langs'=>'ventilation@ventilation',
								'position'=>100,
								'perms'=>1,
								'enabled'=>'$conf->ventilation->enabled',
								'target'=>'',
								'user'=>0);
		$r++;
		
				$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'Account',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/param/index.php',
								'langs'=>'ventilation@ventilation',
								'position'=>101,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		$this->menu[$r]=array(  'fk_menu'=>'r=1',
		                        'type'=>'left',
		                        'titre'=>'List',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/param/liste.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>102,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
      		$r++;
		$this->menu[$r]=array(  'fk_menu'=>'r=1',
		                        'type'=>'left',
		                        'titre'=>'Create',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/param/fiche.php?action=create',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>103,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
      		$r++;
      						$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'ThirdPartyAccount',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/thirdpartyaccount/index.php',
								'langs'=>'ventilation@ventilation',
								'position'=>110,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
				$this->menu[$r]=array(  'fk_menu'=>'r=4',
		                        'type'=>'left',
		                        'titre'=>'List',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/thirdpartyaccount/liste.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>111,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
      		$r++;
		
	
		
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'CustomersVentilation',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/index.php',
								'langs'=>'ventilation@ventilation',
								'position'=>120,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
		$this->menu[$r]=array(	'fk_menu'=>'r=6',
								'type'=>'left',
								'titre'=>'ToDispatch',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/liste2.php',
								'langs'=>'ventilation@ventilation',
								'position'=>121,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
		$this->menu[$r]=array(	'fk_menu'=>'r=6',
								'type'=>'left',
								'titre'=>'Dispatched',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/lignes.php',
								'langs'=>'ventilation@ventilation',
								'position'=>122,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;

	
		
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'SuppliersVentilation',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/fournisseur/index.php',
								'langs'=>'ventilation@ventilation',
								'position'=>130,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
		$this->menu[$r]=array(	'fk_menu'=>'r=9',
								'type'=>'left',
								'titre'=>'ToDispatch',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/fournisseur/liste.php',
								'langs'=>'ventilation@ventilation',
								'position'=>131,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
		$this->menu[$r]=array(  'fk_menu'=>'r=9',
								'type'=>'left',
								'titre'=>'Dispatched',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/fournisseur/lignes.php',
								'langs'=>'ventilation@ventilation',
								'position'=>132,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
				
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'Journaux',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/journal/index.php',
								'langs'=>'ventilation@ventilation',
								'position'=>140,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
		
		
				$this->menu[$r]=array(  'fk_menu'=>'r=12',
		                        'type'=>'left',
		                        'titre'=>'Journal des ventes',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/journal/sellsjournal.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>141,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
      		$r++;
		$this->menu[$r]=array(  'fk_menu'=>'r=12',
		                        'type'=>'left',
		                        'titre'=>'Journal des achats',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/journal/purchasesjournal.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>142,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
      		$r++;
      		$this->menu[$r]=array(  'fk_menu'=>'r=12',
		                        'type'=>'left',
		                        'titre'=>'Journal banque',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/journal/bankjournal.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>143,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
      		$r++;
		
		
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
								'type'=>'left',
								'titre'=>'Bookkeeping',
								'mainmenu'=>'ventilation',
								'url'=>'/ventilation/bookkeeping/liste.php',
								'langs'=>'ventilation@ventilation',
								'position'=>150,
								'enabled'=>1,
								'perms'=>1,
								'target'=>'',
								'user'=>0);
		$r++;
      
		$this->menu[$r]=array(  'fk_menu'=>'r=16',
								'type'=>'left',
								'titre'=>'ByYear',
								'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/bookkeeping/listebyyear.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>151,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
		$r++;
      
    
      	
     	 	$this->menu[$r]=array(  'fk_menu'=>'r=16',
		                        'type'=>'left',
		                        'titre'=>'Balance mensuelle',
		                        'mainmenu'=>'ventilation',
		                        'url'=>'/ventilation/bookkeeping/balancebymonth.php',
		                        'langs'=>'ventilation@ventilation',
		                        'position'=>152,
		                        'enabled'=>1,
		                        'perms'=>1,
		                        'target'=>'',
		                        'user'=>0);
     	 	$r++;



	}


	/**
     *		\brief      Function called when module is enabled.
     *					The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *					It also creates data directories.
	 *      \return     int             1 if OK, 0 if KO
     */
	function init()
	{
		$sql = array();
		
		if ($this->load_tables() < 0)
		    return -1;
		
		

		return $this->_init($sql);
	}

	/**
	 *		\brief		Function called when module is disabled.
 	 *              	Remove from database constants, boxes and permissions from Dolibarr database.
 	 *					Data directories are not deleted.
	 *      \return     int             1 if OK, 0 if KO
 	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}
	
	/**
	 *		\brief		Create tables and keys required by module
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/ventilation/sql/');
	}

}
?>
