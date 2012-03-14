<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
     Desarrollado en el mes de enero de 2012
     Correo electrónico: alexturruella@gmail.com
     Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
	 Fichero modTopTen.class.php
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modTopTen
 *      \brief      Muestra el top ten de clientes, productos y facturas
 */
class modTopTen extends DolibarrModules
{
	/**
	 *   Constructor.
	 *
	 *   @param		DoliDB		$db      Database handler
	 */
	function modTopTen($db)
	{
		$this->db = $db;

		// Id for module (must be unique).
		$this->numero = 10007;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'topten';
		// It is used to group modules in module setup page
		$this->family = "other";

		$this->name = preg_replace('/^mod/i','',get_class($this));

		$this->description = "Top ten de clientes, productos y facturas ";

		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='topten.png@topten';

		$this->triggers = 0;

		$this->dirs = array("/topten/factura","/topten/cliente","/topten/producto");
		$r=0;

		// Relative path to module style sheet if exists. Example: '/mymodule/css/mycss.css'.
		$this->style_sheet = '/topten/css/topten.css';

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		//"regaliasconfiguracion.php@regalias"
		$this->config_page_url = array();

		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->depends = array("modSociete","modCommande","modFacture","modProduct");
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,2,-4);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("toptenlang@topten");

		$this->const = array();			// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 0 or 'allentities')

		$this->tabs = array();

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Main menu entries
		$this->menus = array();			// List of menus to add

		$r=0;
        $this->menu[$r]=array('fk_menu'=>0,
													'type'=>'top',
													'titre'=>'TTtopten',
													'mainmenu'=>'topten',
													'leftmenu'=>'0',
													'url'=>'/topten/index.php',
													'langs'=>'toptenlang@topten',
													'position'=>100,
													'perms'=>'',
													'enabled'=>'$conf->topten->enabled',
													'target'=>'',
													'user'=>0
													);
		$r++;
//----------------------------------------------------------------------------------------------------------------------------------------------
			$this->menu[$r]=array(	'fk_menu'=>'r=0',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTCliente',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttindexcliente.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTClienteDinero',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttclientedinero.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTClienteFactura',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttclientefactura.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;

		$this->menu[$r]=array(	'fk_menu'=>'r=0',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTProducto',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttindexproducto.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=4',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTProductoDinero',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttproductodinero.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=4',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTProductoCantidad',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttproductocantidad.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTFactura',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttindexfactura.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=7',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTFacturaDinero',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttfacturadinero.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=7',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',			// This is a Left menu entry
			'titre'=>'TTFacturaProducto',
			'mainmenu'=>'topten',
			'url'=>'/topten/ttfacturaproducto.php',
			'langs'=>'toptenlang@topten',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->topten->enabled',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=>'',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);				// 0=Menu for internal users,1=external users, 2=both
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

		//$result=$this->load_tables();

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

	function load_tables()
	{
		return $this->_load_tables('');
	}
}
?>