<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**     \defgroup   sellyoursaas     Module SellYourSaas
 *      \brief      Module SellYourSaas
 */

/**
 *      \file       htdocs/sellyoursaas/core/modules/modSellYourSaas.class.php
 *      \ingroup    sellyoursaas
 *      \brief      Description and activation file for module SellYourSaas
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * Description and activation class for module SellYourSaas
 */
class modSellYourSaas extends DolibarrModules
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

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 101050;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'nltechno';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to sell SaaS application";
        $this->editor_name = 'NLTechno';
        $this->editor_url = 'https://www.nltechno.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='generic';

		// Data directories to create when module is enabled
		$this->dirs = array('/sellyoursaas/temp','/sellyoursaas/git','/sellyoursaas/sqldump');

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array("setup.php@sellyoursaas");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,1);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(6,0,-4);	// Minimum version of Dolibarr required by module

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array('triggers' => 0,
									'substitutions' => 0,
									'menus' => 0,
									'css' => array(),
									'hooks' => array('searchform','thirdpartylist','customerlist','prospectlist'));

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(
		    0=>array('NLTECHNO_NOTE', 'chaine',
		        'Welcome on SellYourSaas Home page<br><br>
		        Link to the specification: https://framagit.org/eldy/sell-your-saas<br><br>
		        ...You can enter content on this page to save any notes/information of your choices.', 'This is another constant to add', 0, 'allentities', 0)
		);

		// Dictionaries
	    if (! isset($conf->sellyoursaas->enabled))
        {
        	$conf->sellyoursaas=new stdClass();
        	$conf->sellyoursaas->enabled=0;
        }
        /*$this->dictionaries=array(
		'langs'=>'nltechno@sellyoursaaschno',
		'tabname'=>array(MAIN_DB_PREFIX."c_dolicloud_plans"),
		'tablib'=>array("DoliCloud plans"),
		'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.price_instance, f.price_user, f.price_gb, f.active FROM '.MAIN_DB_PREFIX.'c_dolicloud_plans as f'),
		'tabsqlsort'=>array("label ASC"),
		'tabfield'=>array("code,label,price_instance,price_user,price_gb"), // Nom des champs en resultat de select pour affichage du dictionnaire
		'tabfieldvalue'=>array("code,label,price_instance,price_user,price_gb"),  // Nom des champs d'edition pour modification d'un enregistrement
		'tabfieldinsert'=>array("code,label,price_instance,price_user,price_gb"),
		'tabrowid'=>array("rowid"),
		'tabcond'=>array($conf->sellyoursaas->enabled)
		);*/

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
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
			0=>array('label'=>'SendWelcomeMessage',                'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doSendWelcomeMessage',                'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'AlertSoftEndTrial',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doAlertSoftEndTrial',                 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'SuspendNotPaidTestInstances',       'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doSuspendNotPaidTestInstances',       'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'UndeployOldSuspendedTestInstances', 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doUndeployOldSuspendedTestInstances', 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'TakePaymentPaypal',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doTakePaymentPaypal',                 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'TakePaymentStripe',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doTakePaymentStripe',                 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'SuspendNotPaidRealInstances',       'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doSuspendNotPaidRealInstances',       'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'AlertCreditCardExpiration',         'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doAlertCreditCardExpiration',         'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
			0=>array('label'=>'AlertPaypalExpiration',             'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'AdolertPaypalExpiration',             'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>0, 'test'=>true),
		);
		// Example: $this->cronjobs=array(0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true),
		//                                1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>true)
		// );


		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		$this->rights[$r][0] = 101051; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'See SellYourSaas Home area';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'liens';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		/*
		$this->rights[$r][0] = 101052; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page annonces';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'annonces';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 101053; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Voir page emailings';	// Permission label
		$this->rights[$r][2] = 'r'; 					// Permission by default for new user (0/1)
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'emailings';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'voir';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
        */

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		$this->rights[$r][0] = 101060; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Read DoliCloud informations';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'dolicloud';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'read';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = 101061; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/edit DoliCloud data';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'dolicloud';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = 'write';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'SellYourSaas',
								'mainmenu'=>'sellyoursaas',
								'url'=>'/sellyoursaas/index.php',
								'langs'=>'',
								'position'=>200,
                				'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->liens->voir||$user->rights->nltechno->annonces->voir||$user->rights->nltechno->emailings->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=sellyoursaas',
								'type'=>'left',
								'titre'=>'Home',
								'mainmenu'=>'sellyoursaas',
								'url'=>'/sellyoursaas/index.php',
								'langs'=>'',
								'position'=>100,
                				'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->liens->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		// My Saas
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		    'type'=>'left',         // This is a Left menu entry
		    'titre'=>'MySaaS',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'mysaas',
		    'url'=>'/sellyoursaas/backoffice/index_new.php',
		    'langs'=>'nltechno@sellyoursaas',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		    'position'=>200,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->dolicloud->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
		    'target'=>'',
		    'user'=>0);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas',
		    'type'=>'left',
		    'titre'=>'ListOfCustomers',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'mysaas_customerlist',
		    'url'=>'/societe/list.php?search_options_dolicloud=y',
		    'langs'=>'',
		    'position'=>210,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->dolicloud->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas',
		    'type'=>'left',
		    'titre'=>'ListOfInstances',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'mysaas_list',
		    'url'=>'/contrat/list.php?leftmenu=contracts&contextpage=dolicloudinstances&search_product_category=6',
		    'langs'=>'',
		    'position'=>211,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->dolicloud->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		// Left menu DoliCloud
		/*
		 $this->menu[$r]=array( 'fk_menu'=>'r=0',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		 'type'=>'left',         // This is a Left menu entry
		 'titre'=>'DoliCloud (old)',
		 'mainmenu'=>'sellyoursaas',
		 'leftmenu'=>'dolicloudold',
		 'url'=>'/sellyoursaas/backoffice/index.php',
		 'langs'=>'nltechno@sellyoursaas',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		 'position'=>300,
		 'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		 'perms'=>'$user->rights->nltechno->dolicloud->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
		 'target'=>'',
		 'user'=>0);             // 0=Menu for internal users, 1=external users, 2=both
		 $r++;

		 $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=nltechno,fk_leftmenu=dolicloudold',
		 'type'=>'left',
		 'titre'=>'List',
		 'mainmenu'=>'sellyoursaas',
		 'leftmenu'=>'dolicloudold_list',
		 'url'=>'/sellyoursaas/backoffice/dolicloud_list.php',
		 'langs'=>'',
		 'position'=>200,
		 'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		 'perms'=>'$user->rights->nltechno->dolicloud->read',
		 'target'=>'',
		 'user'=>0);
		 $r++;

		 $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=nltechno,fk_leftmenu=dolicloudold',
		 'type'=>'left',
		 'titre'=>'New',
		 'mainmenu'=>'sellyoursaas',
		 'leftmenu'=>'dolicloudold_create',
		 'url'=>'/sellyoursaas/backoffice/dolicloud_card.php?action=create',
		 'langs'=>'',
		 'position'=>210,
		 'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		 'perms'=>'$user->rights->nltechno->dolicloud->write',
		 'target'=>'',
		 'user'=>0);
		 $r++;

		 $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=nltechno,fk_leftmenu=dolicloudold',
		 'type'=>'left',
		 'titre'=>'EMailsTemplates',
		 'mainmenu'=>'sellyoursaas',
		 'leftmenu'=>'dolicloudold_emailstemplates',
		 'url'=>'/sellyoursaas/backoffice/dolicloudemailstemplates_page.php?action=list',
		 'langs'=>'nltechno@sellyoursaas',
		 'position'=>220,
		 'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		 'perms'=>'$user->rights->nltechno->dolicloud->write',
		 'target'=>'',
		 'user'=>0);
		 $r++;

		 $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=nltechno,fk_leftmenu=dolicloudold',
		 'type'=>'left',
		 'titre'=>'ImportCustomers',
		 'mainmenu'=>'sellyoursaas',
		 'leftmenu'=>'dolicloudold_import_custmers',
		 'url'=>'/sellyoursaas/backoffice/dolicloud_import_customers.php',
		 'langs'=>'nltechno@sellyoursaas',
		 'position'=>220,
		 'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		 'perms'=>'$user->rights->nltechno->dolicloud->write',
		 'target'=>'',
		 'user'=>0);
		 $r++;

		 $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=nltechno,fk_leftmenu=dolicloudold',
		 'type'=>'left',
		 'titre'=>'ImportPayments',
		 'mainmenu'=>'sellyoursaas',
		 'leftmenu'=>'dolicloudold_import_payments',
		 'url'=>'/sellyoursaas/backoffice/dolicloud_import_payments.php',
		 'langs'=>'nltechno@sellyoursaas',
		 'position'=>220,
		 'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		 'perms'=>'$user->rights->nltechno->dolicloud->write',
		 'target'=>'',
		 'user'=>0);
		 $r++;
		 */
/*
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=nltechno,fk_leftmenu=dolicloud',
								'type'=>'left',
								'titre'=>'EMailsTemplates',
								'mainmenu'=>'sellyoursaas',
								'leftmenu'=>'dolicloud_emailstemplates',
								'url'=>'/sellyoursaas/dolicloud/dolicloudemailstemplates_page_new.php?action=list',
								'langs'=>'nltechno@sellyoursaas',
								'position'=>220,
                				'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->nltechno->dolicloud->write',
								'target'=>'',
								'user'=>0);
		$r++;*/

		/*
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',
		    'type'=>'left',
		    'titre'=>'Outils Petites annonces',
		    'mainmenu'=>'sellyoursaas',
		    'url'=>'/sellyoursaas/statsannonces.php',
		    'langs'=>'',
		    'position'=>400,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->annonces->voir',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',
		    'type'=>'left',
		    'titre'=>'Outils EMailings',
		    'mainmenu'=>'sellyoursaas',
		    'url'=>'/sellyoursaas/statsemailings.php',
		    'langs'=>'',
		    'position'=>500,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->emailings->voir',
		    'target'=>'',
		    'user'=>0);
		$r++;
        */


		// Old DoliCloud
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		    'type'=>'left',         // This is a Left menu entry
		    'titre'=>'DoliCloud (old)',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'dolicloud',
		    'url'=>'/sellyoursaas/dolicloud/index_new.php',
		    'langs'=>'nltechno@sellyoursaas',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		    'position'=>300,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->dolicloud->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
		    'target'=>'',
		    'user'=>0);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=dolicloud',
		    'type'=>'left',
		    'titre'=>'ListOfInstances',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'dolicloud_list',
		    'url'=>'/sellyoursaas/dolicloud/dolicloud_list_new.php',
		    'langs'=>'',
		    'position'=>310,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->nltechno->dolicloud->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

	}

	/**
	 *	Function called when module is enabled.
	 *	The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *	It also creates data directories
	 *
     *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
	    global $langs;

		$result=$this->_load_tables('/sellyoursaas/sql/');

		// Create extrafields
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);

		$param=array('options'=>array('no'=>'No','yesv2'=>'V2','yesv1'=>'V1'));
		$result1=$extrafields->addExtraField('dolicloud', "DoliCloudCustomer", 'select', 1, 3, 'thirdparty', 0, 1, '', $param, 1);

		$sql = array();

		return $this->_init($sql,$options);
	}

	/**
	 *	Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *	Data directories are not deleted
	 *
     *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql,$options);
	}

}
