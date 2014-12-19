<?php
/* Copyright (C) 2010-2014 Regis Houssin  <regis.houssin@capnetworks.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *	\defgroup   milestone       Milestone module
 *	\brief      Module to manage milestones
 *	\file       htdocs/core/modules/modMilestone.class.php
 *	\ingroup    milestone
 *	\brief      Fichier de description et activation du module Milestone
 */
include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 *	\class      modMilestone
 *	\brief      Classe de description et activation du module Milestone
 */
class modMilestone extends DolibarrModules
{
	/**
	 *	Constructor
	 *
	 *	@param	DB	handler d'acces base
	 */
	function __construct($db)
	{
		$this->db = $db;
		$this->numero = 1790;

		$this->family = "technic";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion des jalons (projets, contrats, propales, ...)";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.6.2';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 2;
		$this->picto = 'milestone@milestone';

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Dependencies
		$this->depends = array();

		// Config pages
		$this->config_page_url = array('milestone.php@milestone');
		$this->langfiles = array('milestone@milestone');

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array('triggers' => 1,
									'hooks' => array(
											'propalcard',
											'ordercard',
											'invoicecard'
											//'ordersuppliercard',
											//'invoicesuppliercard'
											)
									);

		// Constantes
		$this->const=array(1=>array('MAIN_FORCE_RELOAD_PAGE',"chaine",1,'',0));

		// Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();
		$this->rights_class = 'milestone';

		$r=0;

		$this->rights[$r][0] = 1791; // id de la permission
		$this->rights[$r][1] = 'Read milestones'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecated)
		$this->rights[$r][3] = 1; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'lire';
		$r++;

		$this->rights[$r][0] = 1792; // id de la permission
		$this->rights[$r][1] = 'Create/update milestones'; // libelle de la permission
		$this->rights[$r][2] = 'w'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'creer';
		$r++;

		$this->rights[$r][0] = 1793; // id de la permission
		$this->rights[$r][1] = 'Delete milestones'; // libelle de la permission
		$this->rights[$r][2] = 'd'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par defaut
		$this->rights[$r][4] = 'supprimer';
		$r++;

	}


	/**
     *		Function called when module is enabled.
     *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *		It also creates data directories.
     *
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
 	 *
	 *      @return     int             1 if OK, 0 if KO
 	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}

	/**
	 *		Create tables and keys required by module
	 *		This function is called by this->init.
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/milestone/sql/');
	}

}
?>
