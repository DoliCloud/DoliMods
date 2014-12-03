<?php
/* Copyright (C) 2009-2014 Regis Houssin  <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Herve Prot     <herve.prot@symeos.com>
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
 *      \defgroup   multicompany     Module multicompany
 *      \brief      Descriptor file for module multicompany
 *      \file       htdocs/core/modules/modMultiCompany.class.php
 *      \ingroup    multicompany
 *      \brief      Description and activation file for module MultiCompany
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *      \class      modMultiCompany
 *      \brief      Description and activation class for module MultiCompany
 */
class modMultiCompany extends DolibarrModules
{
	/**
	 *  Constructor.
	 *
	 *  @param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 5000;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'multicompany';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "base";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Multi-Company Management";
		// Can be enabled / disabled only in the main company with superadmin account
		$this->core_enabled = 1;
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '3.6.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of png file (without png) used for this module.
		// Png file must be in theme/yourtheme/img directory under name object_pictovalue.png.
		$this->picto='multicompany@multicompany';

		// Data directories to create when module is enabled.
		$this->dirs = array();

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("multicompany.php@multicompany");

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
				'login' => 1,
				'hooks' => array(
						'data' => array(
								'mainloginpage',
								'passwordforgottenpage',
								'toprightmenu'
						),
						'entity' => '0'
				),
				'css' => array(
						'/multicompany/css/multicompany.css.php'
				)
		);

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,5);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("multicompany@multicompany");

		// Constants
		// List of particular constants to add when module is enabled
		$this->const=array();

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();
		$r=0;

		$r++;
		$this->rights[$r][0] = 5001;
		$this->rights[$r][1] = 'Consulter les infos société';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';

		$r++;
		$this->rights[$r][0] = 5002;
		$this->rights[$r][1] = 'Modifier la fiche société';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

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

		$result=$this->setSuperAdmin();

		$result=$this->setFirstEntity();

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

		$result = $this->destroy_entityCookie();

		return $this->_remove($sql);
	}

	/**
	 *		Create tables and keys required by module
	 *		This function is called by this->init.
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/multicompany/sql/');
	}

	/**
	*	Set the first entity
	*
	*	@return void
	*/
	function setSuperAdmin()
	{
		global $user;

		$sql = 'SELECT count(rowid) FROM '.MAIN_DB_PREFIX.'user';
		$sql.= ' WHERE admin = 1 AND entity = 0';
		$res = $this->db->query($sql);
		if ($res) $num = $this->db->fetch_array($res);
		else dol_print_error($this->db);

		if (empty($num[0]))
		{
			$this->db->begin();

			$sql = 'UPDATE '.MAIN_DB_PREFIX.'user SET entity = 0';
			$sql.= ' WHERE admin = 1 AND rowid = '.$user->id;
			if ($this->db->query($sql))
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				$this->db->rollback();
				return -1;
			}
		}
		else
		{
			return 0;
		}
	}

   /**
	*	Set the first entity
	*
	*	@return void
	*/
	function setFirstEntity()
	{
		global $user, $langs;

		$langs->load('multicompany@multicompany');

		$sql = 'SELECT count(rowid) FROM '.MAIN_DB_PREFIX.'entity';
		$res = $this->db->query($sql);
		if ($res) $num = $this->db->fetch_array($res);
		else dol_print_error($this->db);

		if (empty($num[0]))
		{
			$this->db->begin();

			$now = dol_now();

			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'entity (';
			$sql.= 'label';
			$sql.= ', description';
			$sql.= ', datec';
			$sql.= ', fk_user_creat';
			$sql.= ') VALUES (';
			$sql.= '"'.$langs->trans("MasterEntity").'"';
			$sql.= ', "'.$langs->trans("MasterEntityDesc").'"';
			$sql.= ', "'.$this->db->idate($now).'"';
			$sql.= ', '.$user->id;
			$sql.= ')';

			if ($this->db->query($sql))
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				$this->db->rollback();
				return -1;
			}
		}
		else
		{
			return 0;
		}
	}

   /**
	*   Destroy entity cookie
	*/
	function destroy_entityCookie()
	{
		$prefix=dol_getprefix();
		$entityCookieName = 'DOLENTITYID_'.$prefix;
		setcookie($entityCookieName, '', 1, "/");
	}
}

?>
