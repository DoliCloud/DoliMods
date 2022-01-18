<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2014     Mário Batista     <mariorbatista@gmail.com> ISCTE-UL Moss
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup	saftpt	 module
 * 	\brief		definition of saf-t module.
 * 	\file		core/modules/modSaftPt.class.php
 * 	\ingroup	saftpt
 * 	\brief		Description and activation file for module SaftPt
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module SaftPt
 */
class modSaftPt extends DolibarrModules
{

    /**
     * 	Constructor. Define names, constants, directories, boxes, permissions
     *
     * 	@param	DoliDB		$db	Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;

        // Id for module (must be unique).
        // Use a free id here
        // (See http://wiki.dolibarr.org/index.php/List_of_modules_id for available ranges).
        $this->numero = 60101; //verificado numeração livre
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'saftpt';

        // Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
        // It is used to group modules in module setup page
        $this->family = "financial";
        // Module label (no space allowed)
        // used if translation string 'ModuleXXXName' not found
        // (where XXX is value of numeric property 'numero' of module)
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description
        // used if translation string 'ModuleXXXDesc' not found
        // (where XXX is value of numeric property 'numero' of module)
        $this->description = "Saf-T PT MOSS"; //entry in admin.lang tag Module60101Desc= Module60101Name
        // Possible values for version are: 'development', 'experimental' or version
        $this->version = '1.0.3';
        // Key used in llx_const table to save module status enabled/disabled
        // (where SaftPT is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        // Where to store the module in setup page
        // (0=common,1=interface,2=others,3=very specific)
        $this->special = 0;
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png
        // use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png
        // use this->picto='pictovalue@module'
        $this->picto = 'generic'; // mypicto@SaftPT
        // Defined all module parts (triggers, login, substitutions, menus, css, etc...)
        // for default path (eg: /saftpt/core/xxxxx) (0=disable, 1=enable)
        // for specific path of parts (eg: /saftpt/core/modules/barcode)
        // for specific css file (eg: /saftpt/css/saftpt.css.php)
        $this->module_parts = array(
            // Set this to 1 if module has its own trigger directory
            //'triggers' => 1,
            // Set this to 1 if module has its own login method directory
            //'login' => 0,
            // Set this to 1 if module has its own substitution function file
            //'substitutions' => 0,
            // Set this to 1 if module has its own menus handler directory
            //'menus' => 0,
            // Set this to 1 if module has its own barcode directory
            //'barcode' => 0,
            // Set this to 1 if module has its own models directory
            //'models' => 0,
            // Set this to relative path of css if module has its own css file
            //'css' => 'saftpt/css/mycss.css.php',
            // Set here all hooks context managed by module
            //'hooks' => array('hookcontext1','hookcontext2')
            // Set here all workflow context managed by module
            //'workflow' => array('order' => array('WORKFLOW_ORDER_AUTOCREATE_INVOICE'))
        );

        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/saftpt/temp");
        $this->dirs = array("/saftpt/temp");

        // Config pages. Put here list of php pages
        // stored into saftpt/admin directory, used to setup module.
        $this->config_page_url = array("saftpt.php@saftpt");

        // Dependencies
        // List of modules class name as string that must be enabled if this module is enabled
        // Example : $this->depends('modAnotherModule', 'modYetAnotherModule')
        $this->depends = array("modFacture","modProduct","modSociete");
        // List of modules id to disable if this one is disabled
        $this->requiredby = array();
        // Minimum version of PHP required by module
        $this->phpmin = array(5, 3);
        // Minimum version of Dolibarr required by module
        $this->need_dolibarr_version = array(3, 2);
        $this->langfiles = array("saftpt@saftpt"); // langfiles@saftpt
        // Constants
        // List of particular constants to add when module is enabled
        // (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example:
        $this->const = array(
            //	0=>array(
            //		'MYMODULE_MYNEWCONST1',
            //		'chaine',
            //		'myvalue',
            //		'This is a constant to add',
            //		1
            //	),
            //	1=>array(
            //		'MYMODULE_MYNEWCONST2',
            //		'chaine',
            //		'myvalue',
            //		'This is another constant to add',
            //		0
            //	)
        );

        // Array to add new pages in new tabs
        // Example:
        $this->tabs = array(
            //	// To add a new tab identified by code tabname1
            //	'objecttype:+tabname1:Title1:langfile@saftpt:$user->rights->saftpt->read:/saftpt/mynewtab1.php?id=__ID__',
            //	// To add another new tab identified by code tabname2
            //	'objecttype:+tabname2:Title2:langfile@saftpt:$user->rights->othermodule->read:/saftpt/mynewtab2.php?id=__ID__',
            //	// To remove an existing tab identified by code tabname
            //	'objecttype:-tabname'
        );
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

        // Dictionnaries
        if (! isset($conf->saftpt->enabled)) {
            $conf->saftpt=new stdClass();
            $conf->saftpt->enabled = 0;
        }
        //$this->dictionnaries = array();
          // This is to avoid warnings
          if (! isset($conf->saftpt->enabled)) $conf->saftpt->enabled=0;
        $this->dictionnaries=array(
          'langs'=>'saftpt@saftpt',
          // List of tables we want to see into dictonnary editor
          'tabname'=>array(MAIN_DB_PREFIX."c_taxexemption", MAIN_DB_PREFIX."c_taxtype"),
          // Label of tables
          'tablib'=>array("Motivos de isenção do IVA","Classificação Saf-t taxas de IVA"),
          // Request to select fields
          'tabsql'=>array(
          'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'c_taxexemption as f',
		  'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'c_taxtype as f'
          ),
          // Sort order
          'tabsqlsort'=>array("code ASC","code ASC"),
          // List of fields (result of select to show dictionnary)
          'tabfield'=>array("code,label","code,label"),
          // List of fields (list of fields to edit a record)
          'tabfieldvalue'=>array("code,label","code,label"),
          // List of fields (list of fields for insert)
          'tabfieldinsert'=>array("code,label","code,label"),
          // Name of columns with primary key (try to always name it 'rowid')
          'tabrowid'=>array("rowid","rowid"),
          // Condition to show each dictionnary
          'tabcond'=>array($conf->saftpt->enabled,$conf->saftpt->enabled)
          );
         

        // Boxes
        // Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array(); // Boxes list
        $r = 0;
       /* // Example:

        $this->boxes[$r][1] = "MyBox@saftpt";
        $r ++;
        /*
          $this->boxes[$r][1] = "myboxb.php";
          $r++;
         */

        // Permissions
        $this->rights = array(); // Permission array used by this module
        $r = 0;
		
		$this->rights[$r][0] = 60111;
		$this->rights[$r][1] = 'Download Ficheiro Saf-t';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'exesaftpt';
		$this->rights[$r][5] = 'read';
		$r++;

		$this->rights[$r][0] = 60112;
		$this->rights[$r][1] = 'Gerar Ficheiro Saf-t';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'exesaftpt';
		$this->rights[$r][5] = 'write';
		$r++;
		//translation in admin.lang tag Permission60111 and Permission60112
		
		// Left-Menu of saf-t
		$r=0;
		$r=1;
        $this->menus = array(); // List of menus to add
        
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=accountancy',		  
								'type'=>'left',
								'titre'=>'MenuSaft',
								'mainmenu'=>'accountancy',
								'leftmenu'=>'toolssaft',
								'url'=>'/saftpt/index.php?mainmenu=accountancy&leftmenu=toolssaft',
								'langs'=>'saftpt@saftpt',
								'position'=>200,
                				'enabled'=>'$conf->saftpt->enabled',       
								'perms'=>'',
								'target'=>'',
								'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=accountancy,fk_leftmenu=toolssaft',		  
								'type'=>'left',
								'titre'=>'MenuSaftExport',
								'mainmenu'=>'accountancy',
								'leftmenu'=>'toolssaft_new',
								'url'=>'/saftpt/exportsaft.php?mainmenu=accountancy&leftmenu=toolssaft',
								'langs'=>'saftpt@saftpt',
								'position'=>210,
                				'enabled'=>'$conf->saftpt->enabled',   
								'perms'=>'',
								'target'=>'',
								'user'=>0);
		$r++;
    }

    /**
     * Function called when module is enabled.
     * The init function add constants, boxes, permissions and menus
     * (defined in constructor) into Dolibarr database.
     * It also creates data directories
     *
     * 	@param		string	$options	Options when enabling module ('', 'noboxes')
     * 	@return		int					1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        $sql = array();

        $result = $this->loadTables();
		
        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted
     *
     * 	@param		string	$options	Options when enabling module ('', 'noboxes')
     * 	@return		int					1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();

        return $this->_remove($sql, $options);
    }

    /**
     * Create tables, keys and data required by module
     * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     * and create data commands must be stored in directory /saftpt/sql/
     * This function is called by this->init
     *
     * 	@return		int		<=0 if KO, >0 if OK
     */
    private function loadTables()
    {
        return $this->_load_tables('/saftpt/sql/');
    }
}
