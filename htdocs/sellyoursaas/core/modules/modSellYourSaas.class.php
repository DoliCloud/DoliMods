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
		$this->rights_class = 'sellyoursaas';

		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value SellYourSaas)
		$this->description = "Module to sell SaaS application";
        $this->editor_name = 'NLTechno';
        $this->editor_url = 'https://www.nltechno.com';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where SELLYOURSAAS is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 2;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='generic';

		// Data directories to create when module is enabled
		$this->dirs = array('/sellyoursaas/temp','/sellyoursaas/packages','/sellyoursaas/data','/sellyoursaas/git','/sellyoursaas/sqldump');

		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array("setup.php@sellyoursaas");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,1);						// Minimum version of PHP required by module
		$this->langfiles = array("sellyoursaas@sellyoursaas");
		$this->need_dolibarr_version = array(7,0,-5);	// Minimum version of Dolibarr required by module

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array('triggers' => 0,
									'substitutions' => 0,
									'menus' => 0,
									'models' => 1,
									'login' => 1,
									'css' => array(),
									'hooks' => array('formmail','searchform','thirdpartylist','customerlist','prospectlist','contractlist'));

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('SELLYOURSAAS_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('SELLYOURSAAS_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(
		    0=>array('NLTECHNO_NOTE', 'chaine',
		        'Welcome on SellYourSaas Home page<br><br>
		        Link to the specification: https://framagit.org/eldy/sell-your-saas<br><br>
		        ...You can enter content on this page to save any notes/information of your choices.', 'This is another constant to add', 0, 'allentities', 0)
		);


		if (! isset($conf->sellyoursaas) || ! isset($conf->sellyoursaas->enabled))
		{
			$conf->sellyoursaas=new stdClass();
			$conf->sellyoursaas->enabled=0;
		}


		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@sellyoursaas:$user->rights->sellyoursaas->read:/sellyoursaas/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
		//                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@sellyoursaas:$user->rights->othermodule->read:/sellyoursaas/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		//                              'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		// Can also be:	$this->tabs = array('data'=>'...', 'entity'=>0);
		//
		// where objecttype can be
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
		$this->tabs = array();
		$this->tabs[] = array('data'=>'contract:+infoinstance:InfoInstance:sellyoursaas@sellyoursaas:$user->rights->sellyoursaas->read:/sellyoursaas/backoffice/instance_info.php?id=__ID__');
		$this->tabs[] = array('data'=>'contract:+upgrade:UsefulLinks:sellyoursaas@sellyoursaas:$user->rights->sellyoursaas->read:/sellyoursaas/backoffice/instance_links.php?id=__ID__');
		$this->tabs[] = array('data'=>'contract:+users:Users:sellyoursaas@sellyoursaas:$user->rights->sellyoursaas->read:/sellyoursaas/backoffice/instance_users.php?id=__ID__');
		$this->tabs[] = array('data'=>'contract:+backup:BackupInstance:sellyoursaas@sellyoursaas:$user->rights->sellyoursaas->read:/sellyoursaas/backoffice/instance_backup.php?id=__ID__');
		//$this->tabs[] = array('data'=>'contract:+payments:Payments:sellyoursaas@sellyoursaas:$user->rights->sellyoursaas->sellyoursaas->read:/sellyoursaas/backoffice/dolicloud_card_payments.php?id=__ID__');


		// Dictionaries
        /*$this->dictionaries=array(
		'langs'=>'sellyoursaas@sellyoursaaschno',
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
		$statusatinstall=0;
		$this->cronjobs = array(
			0=>array('priority'=>21, 'label'=>'SellYourSaasAlertSoftEndTrial',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doAlertSoftEndTrial',                 'parameters'=>'', 'comment'=>'Send warning before trial expire', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			1=>array('priority'=>25, 'label'=>'SellYourSaasAlertCreditCardExpiration',         'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doAlertCreditCardExpiration',         'parameters'=>'', 'comment'=>'Send warning credit card will expire', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
			2=>array('priority'=>26, 'label'=>'SellYourSaasAlertPaypalExpiration',             'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'AdolertPaypalExpiration',             'parameters'=>'', 'comment'=>'Send warning paypal will expire', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			3=>array('priority'=>31, 'label'=>'SellYourSaasRenewalContracts',                  'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doRenewalContracts',                  'parameters'=>'', 'comment'=>'Update contract and templates (nb of users and amount) with due date = tomorrow', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

		    // Generation of draft invoices is done with priority 50

		    4=>array('priority'=>61, 'label'=>'SellYourSaasValidateDraftInvoices',             'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doValidateDraftInvoices',             'parameters'=>'', 'comment'=>'Check account is not closed. Validate drat invoice if not, delete if closed', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			6=>array('priority'=>75, 'label'=>'SellYourSaasTakePaymentPaypal',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doTakePaymentPaypal',                 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
			7=>array('priority'=>76, 'label'=>'SellYourSaasTakePaymentStripe',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doTakePaymentStripe',                 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			8=>array('priority'=>81, 'label'=>'SellYourSaasSuspendNotPaidTestInstances',       'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doSuspendNotPaidTestInstances',       'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
			9=>array('priority'=>82, 'label'=>'SellYourSaasUndeployOldSuspendedTestInstances', 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doUndeployOldSuspendedTestInstances', 'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
		   10=>array('priority'=>83, 'label'=>'SellYourSaasSuspendNotPaidRealInstances',       'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaas', 'method'=>'doSuspendNotPaidRealInstances',       'parameters'=>'', 'comment'=>'Suspend expired services', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

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
		$this->rights[$r][1] = 'Read SellYourSaaS data';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'read';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = 101061; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/edit SellYourSaaS data (package, ...)';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'write';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;

		$this->rights[$r][0] = 101062; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete SellYourSaaS data (package, ...)';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'delete';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$this->rights[$r][5] = '';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$this->menu[$r]=array(	'fk_menu'=>0,
								'type'=>'top',
								'titre'=>'SellYourSaas',
								'mainmenu'=>'sellyoursaas',
								'url'=>'/sellyoursaas/backoffice/index.php',
								'langs'=>'',
								'position'=>200,
                				'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->sellyoursaas->liens->voir',
								'target'=>'',
								'user'=>0);
		$r++;

		// Summary
		$this->menu[$r]=array(
			'fk_menu'=>'fk_mainmenu=sellyoursaas',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
			'type'=>'left',         // This is a Left menu entry
			'titre'=>'Summary',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_summary',
			'url'=>'/sellyoursaas/backoffice/index.php',
			'langs'=>'sellyoursaas@sellyoursaas',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>0);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Old DoliCloud
		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas',        // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		'type'=>'left',         // This is a Left menu entry
		'titre'=>'Summary (dolicloud old)',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'dolicloud',
		'url'=>'/sellyoursaas/backoffice/dolicloudold_index.php',
		'langs'=>'sellyoursaas@sellyoursaas',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		'position'=>105,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',           // Use 'perms'=>'$user->rights->NewsSubmitter->level1->level2' if you want your menu with a permission rules
		'target'=>'',
		'user'=>0);             // 0=Menu for internal users, 1=external users, 2=both
		$r++;

		// Packages
		$this->menu[$r]=array(
			'fk_menu'=>'fk_mainmenu=sellyoursaas',
			'type'=>'left',
			'titre'=>'Packages',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_packages',
			'url'=>'/sellyoursaas/packages_list.php?search_options_dolicloud=y',
			'langs'=>'',
			'position'=>210,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->read',
			'target'=>'',
			'user'=>0);
		$r++;

		$this->menu[$r]=array(
			'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_packages',
			'type'=>'left',
			'titre'=>'NewPackage',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_createpackage',
			'url'=>'/sellyoursaas/packages_card.php?action=create',
			'langs'=>'sellyoursaas@sellyoursaas',
			'position'=>211,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->write',
			'target'=>'',
			'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_packages',
		'type'=>'left',
		'titre'=>'LiveRefsInstances',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_live',
		'url'=>'https://dolibarr.nltechno.com/refs',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>212,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'_refs',
		'user'=>0);
		$r++;


		// Products
		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas',
		'type'=>'left',
		'titre'=>'Services',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_products',
		'url'=>'/product/list.php?type=1',
		'langs'=>'',
		'position'=>220,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_products',
		'type'=>'left',
		'titre'=>'NewService',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_createproduct',
		'url'=>'/product/card.php?type=1&action=create',
		'langs'=>'',
		'position'=>221,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->write',
		'target'=>'',
		'user'=>0);
		$r++;


		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_products',
		'type'=>'left',
		'titre'=>'Services V1',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_productv1',
		'url'=>'/product/list.php?type=1&search_categ=6',
		'langs'=>'',
		'position'=>222,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->write',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_products',
		'type'=>'left',
		'titre'=>'Services V2',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_productv2',
		'url'=>'/product/list.php?type=1&search_categ=7',
		'langs'=>'',
		'position'=>223,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->write',
		'target'=>'',
		'user'=>0);
		$r++;

		// Customers
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',
		    'type'=>'left',
		    'titre'=>'Customers',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'mysaas_customerlist',
			//'url'=>'/societe/list.php?search_options_dolicloud=v',
			'url'=>'/societe/list.php?search_categ_cus=5',
			'langs'=>'',
		    'position'=>230,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->sellyoursaas->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_customerlist',
			'type'=>'left',
			'titre'=>'NewCustomer',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_createcustomer',
			'url'=>'/societe/card.php?action=create&type=c&custcats[]=5',
			'langs'=>'sellyoursaas@sellyoursaas',
			'position'=>231,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->write',
			'target'=>'',
			'user'=>0);
		$r++;

		// Reseller
		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas',
		'type'=>'left',
		'titre'=>'Resellers',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_resellerlist',
		'url'=>'/societe/list.php?search_categ_sup=9',
		'langs'=>'',
		'position'=>233,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_resellerlist',
		'type'=>'left',
		'titre'=>'NewReseller',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_createreseller',
		'url'=>'/societe/card.php?action=create&type=f&options_dolicloud=no&suppcats[]=9',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>234,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->write',
		'target'=>'',
		'user'=>0);
		$r++;

		// Instances
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',
		    'type'=>'left',
		    'titre'=>'Instances',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'mysaas_list',
		    'url'=>'/contrat/list.php?leftmenu=contracts&contextpage=dolicloudinstances',
		    'langs'=>'sellyoursaas@sellyoursaas',
		    'position'=>240,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->sellyoursaas->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_list',
			'type'=>'left',
			'titre'=>'NewInstance',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_createinstance',
			'url'=>'/sellyoursaas/backoffice/newcustomerinstance.php?action=create',
			'langs'=>'sellyoursaas@sellyoursaas',
			'position'=>241,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->write',
			'target'=>'',
			'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_list',
		'type'=>'left',
		'titre'=>'List of Instance V1',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_list_v1',
		'url'=>'/contrat/list.php?leftmenu=contracts&contextpage=dolicloudinstances&search_product_category=6',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>245,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_list',
		'type'=>'left',
		'titre'=>'List of Instance V2',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_list_v2',
		'url'=>'/contrat/list.php?leftmenu=contracts&contextpage=dolicloudinstances&search_product_category=7',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>246,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'',
		'user'=>0);
		$r++;


		// Cancellation questions
		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas',
		    'type'=>'left',
		    'titre'=>'CancellationForms',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'mysaas_cancellation_list',
		    'url'=>'/sellyoursaas/cancellation_list.php?leftmenu=contracts&contextpage=dolicloudinstances',
		    'langs'=>'sellyoursaas@sellyoursaas',
		    'position'=>240,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->sellyoursaas->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_cancellation_list',
			'type'=>'left',
			'titre'=>'NewCancellationForm',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_cancellation_create',
			'url'=>'/sellyoursaas/cancellation_card.php?action=create',
			'langs'=>'sellyoursaas@sellyoursaas',
			'position'=>241,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->write',
			'target'=>'',
			'user'=>0);
		$r++;


		// Link to website pages

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas',
		'type'=>'left',
		'titre'=>'Registration page',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'website',
		//'url'=>'/public/website/index.php?website=sellyoursaas&pageref=register&plan=abc',
		'url'=>'http://localhostmyaccount/register.php?plan=DOLICLOUD-PACK-Dolibarr&partner=',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>500,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'_sellyoursaas_register',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas',
		'type'=>'left',
		'titre'=>'Customer portal',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'website',
		'url'=>'http://localhostmyaccount',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>501,
		'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'_sellyoursaas_customer',
		'user'=>0);
		$r++;


		$this->menu[$r]=array(
		    'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=dolicloud',
		    'type'=>'left',
		    'titre'=>'Instances (dolicloud old)',
		    'mainmenu'=>'sellyoursaas',
		    'leftmenu'=>'dolicloud_list',
		    'url'=>'/sellyoursaas/backoffice/dolicloud_list.php',
		    'langs'=>'sellyoursaas@sellyoursaas',
		    'position'=>3100,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->sellyoursaas->read',
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
	    global $conf, $langs;

		$result=$this->_load_tables('/sellyoursaas/sql/');

		// Create extrafields
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);

		// Product
		$resultx=$extrafields->addExtraField('price_per_user', 	       "Price per user",     'price',  1, '24,8',  'product', 0, 0, '0',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		// Thirdparty
		$param=array('options'=>array('no'=>'No','yesv1'=>'V1','yesv2'=>'V2'));
		$resultx=$extrafields->addExtraField('dolicloud',            "DoliCloudCustomer",   'select',  1,   '3', 'thirdparty', 0, 1, '', $param, 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('partner',              "Reseller",           'varchar',  2,  '32', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('date_registration',    "RegistrationDate",  'datetime',  3,    '', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('password',    "CustomerDashboardPassword",   'varchar',  4,  '64', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('pass_temp',         "HashForPasswordReset",  'varchar',  4, '128', 'thirdparty', 0, 0, '',     '', 1, '', 0, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		//$resultx=$extrafields->addExtraField('cb_info',            "Credit Card info",   'varchar', 91, '255', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		//$resultx=$extrafields->addExtraField('paypal_info',             "Paypal info",   'varchar', 92, '255', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		// Invoice
		//$resultx=$extrafields->addExtraField('manual_collect',    "Manual collection",  'boolean',  1,   '2',      'facture', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		// Contract
		$resultx=$extrafields->addExtraField('plan',                              "Plan",  'varchar',   2,  '64',    'contrat', 0, 0, '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('processing'=>'Processing','done'=>'Done','undeployed'=>'Undeployed'));
		$resultx=$extrafields->addExtraField('deployment_status',     "DeploymentStatus",   'select',   5,    '',    'contrat', 0, 0, '',  $param, 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_date_start', "DeploymentDateStart", 'datetime',   6,  '',   'contrat', 0, 0, '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_date_end',     "DeploymentDateEnd", 'datetime',   6,  '',   'contrat', 0, 0, '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_ip',             "DeploymentIP",  'varchar',   7, '128',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('date_endfreeperiod',      "Date end trial", 'datetime',   8,    '',    'contrat', 0, 0, '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('undeployment_date',     "UndeploymentDate", 'datetime',   9,    '',    'contrat', 0, 0, '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('undeployment_ip',         "UndeploymentIP",  'varchar',  10, '128',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('hostname_os',                "Hostname OS",  'varchar',  20,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('username_os',                "Username OS",  'varchar',  21,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('password_os',                "Password OS",  'varchar',  22,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('hostname_db',                "Hostname DB",  'varchar',  23,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('database_db',                "Database DB",  'varchar',  24,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('port_db',                        "Port DB",  'varchar',  25,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('username_db',                "Username DB",  'varchar',  26,  '32',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('password_db',                "Password DB",  'varchar',  27,  '64',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('fileauthorizekey',  "DateFileauthorizekey", 'datetime',  28,    '',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('filelock',                  "DateFilelock", 'datetime',  29,    '',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		$resultx=$extrafields->addExtraField('nb_users',            "LastNbEnabledUsers",      'int',  40,   '8',    'contrat', 0, 0, '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('nb_gb',                       "LastNbOfGb",   'double',  41,'10,2',    'contrat', 0, 0, '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');


		// Create/import website called 'sellyoursaas'
		include_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
		$tmpwebsite = new WebSite($this->db);
		$result = $tmpwebsite->importWebSite('website_sellyoursaas-demo.zip');


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
