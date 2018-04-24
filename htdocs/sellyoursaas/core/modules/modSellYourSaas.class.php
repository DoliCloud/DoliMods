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
		$this->module_parts = array('triggers' => 1,
									'substitutions' => 1,
									'menus' => 0,
									'models' => 1,
									'login' => 1,
									'css' => array(),
									'hooks' => array('thirdpartycard','thirdpartycomm','thirdpartycontact','contactthirdparty','thirdpartyticket','thirdpartynote','thirdpartydocument',
													'projectthirdparty','consumptionthirdparty','thirdpartybancard','thirdpartymargins','ticketsuplist','thirdpartynotification','agendathirdparty',
													'thirdpartydao','formmail','searchform','thirdpartylist','customerlist','prospectlist','contractcard','contractlist','odtgeneration'));

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('SELLYOURSAAS_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('SELLYOURSAAS_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(
		    0=>array('NLTECHNO_NOTE', 'chaine',
		        'Welcome on SellYourSaas Home page<br><br>
		        Link to the specification: https://framagit.org/eldy/sell-your-saas<br><br>
		        ...You can enter content on this page to save any notes/information of your choices.', 'This is another constant to add', 0, 'allentities', 0),
			1=>array('CONTRACT_SYNC_PLANNED_DATE_OF_SERVICES', 'chaine', 1, 'Sync planned date of services in same contract', 0, 'current', 1),
			2=>array('THIRDPARTY_LOGO_ALLOW_EXTERNAL_DOWNLOAD', 'chaine', 1, 'Allow to access thirdparty logo from external link', 0, 'current', 0),
			3=>array('PRODUIT_SOUSPRODUITS', 'chaine', 1, 'Enable virtual products', 0, 'current', 0),
			4=>array('STRIPE_ALLOW_LOCAL_CARD', 'chaine', 1, 'Allow to save stripe credit card locally', 0, 'current', 1)
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
        $this->dictionaries=array();


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
		    // Generation of draft invoices is done with priority 50

			0=>array('priority'=>61, 'label'=>'SellYourSaasValidateDraftInvoices',             'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doValidateDraftInvoices',             'parameters'=>'',      'comment'=>'Check account is not closed. Validate drat invoice if not, delete if closed', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			1=>array('priority'=>62, 'label'=>'SellYourSaasAlertSoftEndTrial',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doAlertSoftEndTrial',                 'parameters'=>'',      'comment'=>'Send warning before trial expire', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			2=>array('priority'=>65, 'label'=>'SellYourSaasAlertCreditCardExpiration',         'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doAlertCreditCardExpiration',         'parameters'=>'1, 20', 'comment'=>'Send warning credit card will expire', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
			3=>array('priority'=>66, 'label'=>'SellYourSaasAlertPaypalExpiration',             'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doAlertPaypalExpiration',             'parameters'=>'1, 20', 'comment'=>'Send warning paypal will expire', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			6=>array('priority'=>75, 'label'=>'SellYourSaasTakePaymentStripe',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doTakePaymentStripe',                 'parameters'=>'',      'comment'=>'Loop on invoice for customer with default payment mode Stripe and take payment. Unsuspend if it was suspended.', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
			7=>array('priority'=>76, 'label'=>'SellYourSaasTakePaymentPaypal',                 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doTakePaymentPaypal',                 'parameters'=>'',      'comment'=>'Loop on invoice for customer with default payment mode Paypal and take payment. Unsuspend if it was suspended.', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			4=>array('priority'=>78, 'label'=>'SellYourSaasRenewalContracts',                  'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doRenewalContracts',                  'parameters'=>'',      'comment'=>'Loop on each contract. If it is a contract with payment mode, and there is no unpaid invoice for contract, and end date < in 2 days (so expired or soon expired), we update to contract service end date to end of next period', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

			8=>array('priority'=>81, 'label'=>'SellYourSaasSuspendExpiredTestInstances',       'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doSuspendExpiredTestInstances',       'parameters'=>'',      'comment'=>'Suspend expired services of test instances if we are after planned end date (+ grace offset)', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
			9=>array('priority'=>82, 'label'=>'SellYourSaasUndeployOldSuspendedTestInstances', 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doUndeployOldSuspendedTestInstances', 'parameters'=>'',      'comment'=>'Undeploy old suspended test instances', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
		   10=>array('priority'=>85, 'label'=>'SellYourSaasSuspendExpiredRealInstances',       'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doSuspendExpiredRealInstances',       'parameters'=>'',      'comment'=>'Suspend expired services of paid instances if we are after planned end date (+ grace offset)', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),
		   11=>array('priority'=>86, 'label'=>'SellYourSaasUndeployOldSuspendedRealInstances', 'jobtype'=>'method', 'class'=>'/sellyoursaas/class/sellyoursaasutils.class.php', 'objectname'=>'SellYourSaasUtils', 'method'=>'doUndeployOldSuspendedRealInstances', 'parameters'=>'',      'comment'=>'Undeploy old suspended paid instances', 'frequency'=>1, 'unitfrequency'=>86400, 'status'=>$statusatinstall, 'test'=>'$conf->sellyoursaas->enabled'),

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
		'enabled'=>'$conf->sellyoursaas->enabled && $conf->global->SELLYOURSAAS_DOLICLOUD_ON',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
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
		'url'=>'__[SELLYOURSAAS_REFS_URL]__',
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
		'url'=>'/product/card.php?type=1&action=create&categories[]=__[SELLYOURSAAS_DEFAULT_PRODUCT_CATEG]__',
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
		'leftmenu'=>'mysaas_productold',
		'url'=>'/product/list.php?type=1&search_categ=6',
		'langs'=>'',
		'position'=>222,
		'enabled'=>'$conf->sellyoursaas->enabled && $conf->global->SELLYOURSAAS_DOLICLOUD_ON',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->write',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_products',
		'type'=>'left',
		'titre'=>'Services __[SELLYOURSAAS_DOLICLOUD_ON]__',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_product',
		'url'=>'/product/list.php?type=1&search_categ=__[SELLYOURSAAS_DEFAULT_PRODUCT_CATEG]__',
		'langs'=>'',
		'position'=>223,
		'enabled'=>'$conf->sellyoursaas->enabled && $conf->global->SELLYOURSAAS_DOLICLOUD_ON',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
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
		    'leftmenu'=>'mysaas_customers',
			//'url'=>'/societe/list.php?search_options_dolicloud=v',
			'url'=>'/societe/list.php?search_categ_cus=__[SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG]__',
			'langs'=>'',
		    'position'=>230,
		    'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		    'perms'=>'$user->rights->sellyoursaas->read',
		    'target'=>'',
		    'user'=>0);
		$r++;

		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_customers',
			'type'=>'left',
			'titre'=>'NewCustomer',
			'mainmenu'=>'sellyoursaas',
			'leftmenu'=>'mysaas_createcustomer',
			'url'=>'/societe/card.php?action=create&type=c&custcats[]=__[SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG]__',
			'langs'=>'sellyoursaas@sellyoursaas',
			'position'=>231,
			'enabled'=>'$conf->sellyoursaas->enabled',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->sellyoursaas->write',
			'target'=>'',
			'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_customers',
		'type'=>'left',
		'titre'=>'Customers V1',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_customersold',
		'url'=>'/societe/list.php?search_options_dolicloud=v1',
		'langs'=>'',
		'position'=>232,
		'enabled'=>'$conf->sellyoursaas->enabled && $conf->global->SELLYOURSAAS_DOLICLOUD_ON',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->write',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_customers',
		'type'=>'left',
		'titre'=>'Customers __[SELLYOURSAAS_DOLICLOUD_ON]__',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_customers',
		'url'=>'/societe/list.php?&search_options_dolicloud=v2',
		'langs'=>'',
		'position'=>233,
		'enabled'=>'$conf->sellyoursaas->enabled && $conf->global->SELLYOURSAAS_DOLICLOUD_ON',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
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
		'url'=>'/societe/list.php?search_categ_sup=__[SELLYOURSAAS_DEFAULT_RESELLER_CATEG]__',
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
		'url'=>'/societe/card.php?action=create&type=f&options_dolicloud=no&suppcats[]=__[SELLYOURSAAS_DEFAULT_RESELLER_CATEG]__',
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
		'leftmenu'=>'mysaas_list_old',
		'url'=>'/contrat/list.php?leftmenu=contracts&contextpage=dolicloudinstancesv1&search_product_category=6',
		'langs'=>'sellyoursaas@sellyoursaas',
		'position'=>245,
		'enabled'=>'$conf->sellyoursaas->enabled && $conf->global->SELLYOURSAAS_DOLICLOUD_ON',         // Define condition to show or hide menu entry. Use '$conf->NewsSubmitter->enabled' if entry must be visible if module is enabled.
		'perms'=>'$user->rights->sellyoursaas->read',
		'target'=>'',
		'user'=>0);
		$r++;

		$this->menu[$r]=array(
		'fk_menu'=>'fk_mainmenu=sellyoursaas,fk_leftmenu=mysaas_list',
		'type'=>'left',
		'titre'=>'List of Instance __[SELLYOURSAAS_DOLICLOUD_ON]__',
		'mainmenu'=>'sellyoursaas',
		'leftmenu'=>'mysaas_list',
		'url'=>'/contrat/list.php?leftmenu=contracts&contextpage=dolicloudinstancesv2&search_product_category=__[SELLYOURSAAS_DEFAULT_PRODUCT_CATEG]__',
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
		'url'=>'__[SELLYOURSAAS_ACCOUNT_URL]__/register.php?plan=DOLICLOUD-PACK-Dolibarr&partner=',
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
		'url'=>'__[SELLYOURSAAS_ACCOUNT_URL]__',
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
		$resultx=$extrafields->addExtraField('separatorproduct',          "SELLYOURSAAS_NAME",'separate',   100,     '',  'product', 0, 1, '',       '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('app'=>'Application','system'=>'System','option'=>'Option'));
		$resultx=$extrafields->addExtraField('app_or_option',                   "AppOrOption",  'select',   110,     '',  'product', 0, 0,   '', $param, 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('Packages:sellyoursaas/class/packages.class.php'=>null));
		$resultx=$extrafields->addExtraField('package', 	        	            "Package",    'link',   111,     '',  'product', 0, 0,   '', $param, 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('resource_formula', "QuantityCalculationFormula",    'text',   112, '8192',  'product', 0, 0,   '',     '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('freeperioddays', 	          "DaysForFreePeriod",     'int',   113,    '6',  'product', 0, 0,   '',     '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('directaccess', 	          "AccessToResources", 'boolean',   114,     '',  'product', 0, 0,   '',     '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('basic'=>'Basic','premium'=>'Premium','none'=>'None'));
		$resultx=$extrafields->addExtraField('typesupport', 	              "TypeOfSupport",  'select',   115,     '',  'product', 0, 0,   '', $param, 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('separatorproductend',                   "Other",'separate',   199,     '',  'product', 0, 1,   '',     '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		// Thirdparty
		$resultx=$extrafields->addExtraField('separatorthirdparty',       "SELLYOURSAAS_NAME", 'separate',100,    '', 'thirdparty', 0, 1, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('no'=>'No','yesv1'=>'V1','yesv2'=>'V2'));
		$resultx=$extrafields->addExtraField('dolicloud',                       "SaasCustomer",   'select',102,   '3', 'thirdparty', 0, 1, '', $param, 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('date_registration',           "RegistrationDate", 'datetime',103,    '', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('source',                                "Source",  'varchar',104,  '64', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('firstname',                          "FirstName",  'varchar',105,  '64', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('lastname',                            "LastName",  'varchar',106,  '64', 'thirdparty', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('auto'=>null));
		$resultx=$extrafields->addExtraField('password',                   "DashboardPassword", 'password',190, '128', 'thirdparty', 0, 0, '', $param, 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('pass_temp',               "HashForPasswordReset",  'varchar',191, '128', 'thirdparty', 0, 0, '',     '', 1, '', 0, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('commission',                 "PartnerCommission",      'int',195,   '3', 'thirdparty', 0, 0, '', $param, 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('separatorthirdpartyend',                 "Other", 'separate',199,    '', 'thirdparty', 0, 1, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		// Contract
		$resultx=$extrafields->addExtraField('separatorcontract',               "SELLYOURSAAS_NAME", 'separate', 100,    '',    'contrat', 0, 1,    '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('plan',                                         "Plan",  'varchar', 102,  '64',    'contrat', 0, 0,    '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('processing'=>'Processing','done'=>'Done','undeployed'=>'Undeployed'));
		$resultx=$extrafields->addExtraField('deployment_init_adminpass',  "DeploymentInitPassword",  'varchar', 103,  '64',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_status',                "DeploymentStatus",   'select', 105,    '',    'contrat', 0, 0,    '',  $param, 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_date_start',         "DeploymentDateStart", 'datetime', 106,    '',    'contrat', 0, 0,    '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_date_end',             "DeploymentDateEnd", 'datetime', 106,    '',    'contrat', 0, 0,    '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('deployment_ip',                        "DeploymentIP",  'varchar', 107, '128',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('date_endfreeperiod',                 "Date end trial", 'datetime', 108,    '',    'contrat', 0, 0,    '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('undeployment_date',                "UndeploymentDate", 'datetime', 109,    '',    'contrat', 0, 0,    '',      '', 1, '',  1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('undeployment_ip',                    "UndeploymentIP",  'varchar', 110, '128',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('hostname_os',                           "Hostname OS",  'varchar', 120,  '32',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('username_os',                           "Username OS",  'varchar', 121,  '32',    'contrat', 1, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('password_os',                           "Password OS",  'varchar', 122,  '32',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('hostname_db',                           "Hostname DB",  'varchar', 123,  '32',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('database_db',                           "Database DB",  'varchar', 124,  '32',    'contrat', 1, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('port_db',                                   "Port DB",  'varchar', 125,  '32',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('username_db',                           "Username DB",  'varchar', 126,  '32',    'contrat', 1, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('password_db',                           "Password DB",  'varchar', 127,  '64',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('prefix_db',                 "Special table prefix DB",  'varchar', 128,  '64',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('fileauthorizekey',             "DateFileauthorizekey", 'datetime', 129,    '',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('filelock',                             "DateFilelock", 'datetime', 130,    '',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('latestbackup_date',                "LatestBackupDate", 'datetime', 140,    '',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('latestbackup_status',            "LatestBackupStatus",  'varchar', 141,   '2',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('cookieregister_counter',                    "RegistrationCounter",     'int', 150,   '',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('cookieregister_previous_instance', "RegistrationPreviousInstance", 'varchar', 151,'128',    'contrat', 0, 0,    '',      '', 1, '', -1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

		// Invoice
		$resultx=$extrafields->addExtraField('separatorinvoice',        "SELLYOURSAAS_NAME", 'separate', 10,    '', 'facture', 0, 1, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$resultx=$extrafields->addExtraField('commission',              "PartnerCommission",      'int', 20,   '3', 'facture', 0, 0, '',     '', 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');
		$param=array('options'=>array('Societe:societe/class/societe.class.php'=>null));
		$resultx=$extrafields->addExtraField('reseller',                         "Reseller",     'link', 30,   '3', 'facture', 0, 0, '', $param, 1, '', 1, 0, '', '', 'sellyoursaas@sellyoursaas', '$conf->sellyoursaas->enabled');

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
