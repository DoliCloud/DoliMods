<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\defgroup   zipautofill     Module ZiopAutoFill
 * 	\brief      Module to add zip codes
 *	\version	$Id: modZipAutoFillFr.class.php,v 1.3 2011/04/10 21:53:32 eldy Exp $
 */

/**
 *	\file       htdocs/includes/modules/modZipAutoFillFr.class.php
 *	\ingroup    zipautofillfr
 *	\brief      Fichier de description et activation du module ZipAutoFill
 */

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**
 *	\class 		modZipAutoFillFr
 *	\brief      Classe de description et activation du module Energie
 */

class modZipAutoFillFr extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modZipAutoFillFr($DB)
	{
        global $langs,$conf;

        $this->db = $DB ;
		$this->numero = 12200;

		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Add zip codes and towns into database (France)";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 2;
		$this->picto='generic';

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Dependances
		$this->depends = array();

		// Config pages
		$this->config_page_url = array();

		// Constantes
		$this->const = array(0=>array('MAIN_USE_ZIPTOWN_DICTIONNARY','chaine','1','Constant to enable usage of zip-town table',0));
        //Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',0),
        //                            1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );

        // Dictionnaries
        $this->dictionnaries=array(
            'langs'=>'',
            'tabname'=>array(MAIN_DB_PREFIX."c_ziptown"),
            'tablib'=>array("Zip and town"),
            'tabsql'=>array('SELECT f.rowid, f.code, f.zip, f.town FROM '.MAIN_DB_PREFIX.'c_ziptown as f'),
            'tabsqlsort'=>array("zip ASC, town ASC"),
            'tabfield'=>array("code,zip,town"),
            'tabfieldvalue'=>array("code,zip,town"),
            'tabfieldinsert'=>array("code,zip,town"),
            'tabrowid'=>array(),
            'tabcond'=>array($conf->zipautofillfr->enabled)
        );

        // Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();

	}


	/**
	 *   \brief      Fonction appelee lors de l'activation du module. Insere en base les constantes, boites, permissions du module.
	 *               Definit egalement les repertoires de donnees a creer pour ce module.
	 */
	function init()
	{
		global $conf;
		// Permissions et valeurs par defaut
		$this->remove();

		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *    \brief      Fonction appelee lors de la desactivation d'un module.
	 *                Supprime de la base les constantes, boites et permissions du module.
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
		return $this->_load_tables('/zipautofillfr/sql/');
	}
}
?>
