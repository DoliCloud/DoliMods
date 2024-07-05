<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\defgroup   zipautofill     Module ZiopAutoFill
 * 	\brief      Module to add zip codes
 */

/**
 *	\file       htdocs/zipautofillfr/core/modules/modZipAutoFillFr.class.php
 *	\ingroup    zipautofillfr
 *	\brief      Fichier de description et activation du module ZipAutoFill
 */

include_once DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php";


/**
 * Classe de description et activation du module Energie
 */

class modZipAutoFillFr extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param		DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		global $langs,$conf;

		$this->db = $db;
		$this->numero = 101270;

		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com?origin=dolimods';
		$this->description = "Add zip codes and towns into database (France and Belgium)";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '12.0';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto='zipautofillfr@zipautofillfr';

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Dependances
		$this->depends = array();
		$this->need_dolibarr_version = array(12,0,-3);   // Minimum version of Dolibarr required by module
		$this->langfiles = array('zipautofillfr@zipautofillfr');

		// Config pages
		$this->config_page_url = array('zipautofillfr.php@zipautofillfr');

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );
		//                             2=>array('MAIN_MODULE_MYMODULE_NEEDSMARTY','chaine',1,'Constant to say module need smarty',1)
		$this->const = array(0=>array('MAIN_USE_ZIPTOWN_DICTIONNARY','chaine','1','Constant to enable usage of zip-town table',0,'current',1));

		// Dictionnaries
		$this->dictionaries=array(
			'langs'=>'',
			'tabname'=>array("c_ziptown"),
			'tablib'=>array("Zip and town"),
			'tabsql'=>array('SELECT f.rowid, f.zip, f.town, f.fk_pays, c.code as country_code, c.label as country, f.active FROM '.MAIN_DB_PREFIX.'c_ziptown as f, '.MAIN_DB_PREFIX.'c_country as c WHERE f.fk_pays=c.rowid'),
			'tabsqlsort'=>array("zip ASC, town ASC"),
			'tabfield'=>array("zip,town,country"),
			'tabfieldvalue'=>array("zip,town,country"),
			'tabfieldinsert'=>array("zip,town,fk_pays"),
			'tabrowid'=>array(),
			'tabcond'=>array(isModEnabled("zipautofillfr")?1:0)
		);

		// Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();
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
		global $conf;
		// Permissions et valeurs par defaut
		$this->remove($options);

		$sql = array();

		$result=$this->load_tables();

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

	/**
	 *	Create tables and keys required by module
	 *	This function is called by this->init.
	 *
	 *	@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/zipautofillfr/sql/');
	}
}
