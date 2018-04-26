<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * Call can be done with
 * reusecontractid=id of contract
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOIPCHECK'))      define('NOIPCHECK','1');				// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				    // If this page is public (can be called outside logged session)

// Add specific definition to allow a dedicated session management
include ('./mainmyaccount.inc.php');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
require_once DOL_DOCUMENT_ROOT.'/cron/class/cronjob.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/sellyoursaas/lib/sellyoursaas.lib.php');

// Re set variables specific to new environment
$conf->global->SYSLOG_FILE_ONEPERSESSION=1;
$langs=new Translate('', $conf);
$langs->setDefaultLang('auto');
$langs->loadLangs(array("sellyoursaas@sellyoursaas","errors"));


// Force user
if (empty($user->id))
{
	$user->fetch($conf->global->SELLYOURSAAS_ANONYMOUSUSER);
	// Set $user to the anonymous user
	if (empty($user->id))
	{
		dol_print_error_email('SETUPANON', 'Error setup of module not complete or wrong. Missing the anonymous user.', null, 'alert alert-error');
		exit;
	}

	$user->getrights();
}

$orgname = trim(GETPOST('orgName','alpha'));
$email = trim(GETPOST('username','alpha'));
$password = GETPOST('password','alpha');
$password2 = GETPOST('password2','alpha');
$country_code = GETPOST('address_country','alpha');
$sldAndSubdomain = GETPOST('sldAndSubdomain','alpha');
$tldid = GETPOST('tldid','alpha');
$domainname = preg_replace('/^\./', '', $tldid);
$remoteip = $_SERVER['REMOTE_ADDRESS'];
$origin = GETPOST('origin','aZ09');
$generateduniquekey=getRandomPassword(true);
$partner=GETPOST('partner','alpha');
$partnerkey=GETPOST('partnerkey','alpha');		// md5 of partner name alias

$plan=GETPOST('plan','alpha');
$service=GETPOST('service','alpha');

$fromsocid=GETPOST('fromsocid','int');
$reusecontractid = GETPOST('reusecontractid','int');
$reusesocid = GETPOST('reusesocid','int');

$productid=GETPOST('service','int');
$productref=(GETPOST('productref','alpha')?GETPOST('productref','alpha'):($plan?$plan:'DOLICLOUD-PACK-Dolibarr'));

// Load main product
$tmpproduct = new Product($db);
$result = $tmpproduct->fetch($productid, $productref);
if (empty($tmpproduct->id))
{
	print 'Service/Plan (Product id / ref) '.$productid.' / '.$productref.' was not found.';
	exit;
}

// We have the main product, we are searching the package

if (! preg_match('/^DOLICLOUD-PACK-(.+)$/', $tmpproduct->ref, $reg))
{
	print 'Service/Plan name (Product ref) is invalid. Name must be DOLICLOUD-PACK-...';
	exit;
}
if (empty($tmpproduct->duration_value) || empty($tmpproduct->duration_unit))
{
	print 'Service/Plan name (Product ref) '.$productref.' has no default duration';
	exit;
}

dol_include_once('/sellyoursaas/class/packages.class.php');
$tmppackage = new Packages($db);
$tmppackage->fetch($tmpproduct->array_options['options_package']);
if (empty($tmppackage->id))
{
	print 'Package with id '.$tmpproduct->array_options['options_package'].' was not found.';
	exit;
}

$now = dol_now();


/*
 * Actions
 */

//print "partner=".$partner." plan=".$plan." orgname = ".$orgname." email=".$email." password=".$password." password2=".$password2." country_code=".$country_code." remoteip=".$remoteip." sldAndSubdomain=".$sldAndSubdomain." tldid=".$tldid;

// Back to url
$newurl=preg_replace('/register_instance\.php/', 'register.php', $_SERVER["PHP_SELF"]);

if ($reusecontractid)		// When we use the "Restart deploy" after error from account backoffice
{
	$newurl=preg_replace('/register_instance/', 'index', $newurl);
	if (! preg_match('/\?/', $newurl)) $newurl.='?';
	$newurl.='&mode=instances';
	$newurl.='&reusecontractid='.$reusecontractid;
}
elseif ($reusesocid)		// When we use the "Add another instance" from account backoffice
{
	$newurl=preg_replace('/register_instance/', 'index', $newurl);
	if (! preg_match('/\?/', $newurl)) $newurl.='?';
	$newurl.='&reusesocid='.$reusesocid;
	$newurl.='&mode=instances';
	if (! preg_match('/sldAndSubdomain/i', $sldAndSubdomain)) $newurl.='&sldAndSubdomain='.urlencode($sldAndSubdomain);
	if (! preg_match('/tldid/i', $tldid)) $newurl.='&tldid='.urlencode($tldid);
	if (! preg_match('/service/i', $newurl)) $newurl.='&service='.urlencode($orgname);
	if (! preg_match('/partner/i', $newurl)) $newurl.='&partner='.urlencode($partner);
	if (! preg_match('/partnerkey/i', $newurl)) $newurl.='&partnerkey='.urlencode($partnerkey);		// md5 of partner name alias
	if (! preg_match('/origin/i', $newurl)) $newurl.='&origin='.urlencode($origin);

	if (empty($sldAndSubdomain))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("NameForYourApplication")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (! preg_match('/^[a-zA-Z0-9\-]+$/', $sldAndSubdomain))
	{
		setEventMessages($langs->trans("ErrorOnlyCharAZAllowedFor", $langs->transnoentitiesnoconv("NameForYourApplication")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (empty($password) || empty($password2))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Password")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if ($password != $password2)
	{
		setEventMessages($langs->trans("ErrorPasswordMismatch"), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
}
else
{
	if (! preg_match('/\?/', $newurl)) $newurl.='?';
	if (! preg_match('/orgName/i', $newurl)) $newurl.='&orgName='.urlencode($orgname);
	if (! preg_match('/username/i', $newurl)) $newurl.='&username='.urlencode($email);
	if (! preg_match('/address_country/i', $newurl)) $newurl.='&address_country='.urlencode($country_code);
	if (! preg_match('/sldAndSubdomain/i', $sldAndSubdomain)) $newurl.='&sldAndSubdomain='.urlencode($sldAndSubdomain);
	if (! preg_match('/tldid/i', $tldid)) $newurl.='&tldid='.urlencode($tldid);
	if (! preg_match('/plan/i', $newurl)) $newurl.='&plan='.urlencode($plan);
	//if (! preg_match('/service/i', $newurl)) $newurl.='&orgName='.urlencode($orgname);
	if (! preg_match('/partner/i', $newurl)) $newurl.='&partner='.urlencode($partner);
	if (! preg_match('/partnerkey/i', $newurl)) $newurl.='&partnerkey='.urlencode($partnerkey);		// md5 of partner name alias
	if (! preg_match('/origin/i', $newurl)) $newurl.='&origin='.urlencode($origin);

	if (empty($sldAndSubdomain))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("NameForYourApplication")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (! preg_match('/^[a-zA-Z0-9\-]+$/', $sldAndSubdomain))
	{
		setEventMessages($langs->trans("ErrorOnlyCharAZAllowedFor", $langs->transnoentitiesnoconv("NameForYourApplication")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (empty($orgname))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("CompanyName")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (empty($email))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Email")), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (! isValidEmail($email))
	{
		setEventMessages($langs->trans("ErrorBadEMail"), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	if (empty($password) || empty($password2))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Password")), null, 'errors');
	    header("Location: ".$newurl);
	    exit;
	}
	if ($password != $password2)
	{
	    setEventMessages($langs->trans("ErrorPasswordMismatch"), null, 'errors');
	    header("Location: ".$newurl);
	    exit;
	}
}



/*
 * View
 */

$errormessages = array();

//print '<center>'.$langs->trans("PleaseWait").'</center>';		// Message if redirection after this page fails


$error = 0;

dol_syslog("Fetch contract (id = ".$reusecontractid.", domain name  = ".$fqdninstance.")");

$contract = new Contrat($db);
if ($reusecontractid)
{
	// Get contract
	$result = $contract->fetch($reusecontractid);
	if ($result < 0)
	{
		setEventMessages($langs->trans("NotFound"), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}

	$contract->fetch_thirdparty();

	$tmpthirdparty = $contract->thirdparty;

	$email = $tmpthirdparty->email;
	$password = substr(getRandomPassword(true), 0, 9);		// Password is no more known (no more in memory) when we make a retry/restart of deply

	$generatedunixhostname = $contract->array_options['options_hostname_os'];
	$generatedunixlogin = $contract->array_options['options_username_os'];
	$generatedunixpassword = $contract->array_options['options_password_os'];
	$generateddbhostname = $contract->array_options['options_hostname_db'];
	$generateddbname = $contract->array_options['options_database_db'];
	$generateddbport = ($contract->array_options['options_port_db']?$contract->array_options['options_port_db']:3306);
	$generateddbusername = $contract->array_options['options_username_db'];
	$generateddbpassword = $contract->array_options['options_password_db'];

	$tmparray = explode('.', $contract->ref_customer, 2);
	$sldAndSubdomain = $tmparray[0];
	$domainname = $tmparray[1];
	$tldid = '.'.$domainname;
	$fqdninstance = $sldAndSubdomain.'.'.$domainname;
}
else
{
	$tmpthirdparty=new Societe($db);
	if ($reusesocid > 0)
	{
		$result = $tmpthirdparty->fetch($reusesocid);
		if ($result < 0)
		{
			dol_print_error_email('FETCHTP'.$reusesocid, $tmpthirdparty->error, $tmpthirdparty->errors, 'alert alert-error');
			exit;
		}
	}
	else
	{
		// Create thirdparty (if it already exists, do nothing and return a warning to user)
		dol_syslog("Fetch thirdparty from email ".$email);
		$result = $tmpthirdparty->fetch(0, '', '', '', '', '', '', '', '', '', $email);
		if ($result < 0)
		{
			dol_print_error_email('FETCHTP'.$email, $tmpthirdparty->error, $tmpthirdparty->errors, 'alert alert-error');
			exit;
		}
		else if ($result > 0)	// Found one record
		{
			setEventMessages($langs->trans("AccountAlreadyExistsForEmail", $conf->global->SELLYOURSAAS_ACCOUNT_URL), null, 'errors');
			header("Location: ".$newurl);
			exit;
		}
		else dol_syslog("Not found");
	}

	$fqdninstance = $sldAndSubdomain.$tldid;

	$result = $contract->fetch(0, '', $fqdninstance);
	if ($result > 0)
	{
		setEventMessages($langs->trans("InstanceNameAlreadyExists", $fqdninstance), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}
	else dol_syslog("Not found");


	// Generate credentials

	$generatedunixlogin = strtolower('osu'.substr(getRandomPassword(true), 0, 9));		// Must be lowercase as it can be used for default email
	$generatedunixpassword = substr(getRandomPassword(true), 0, 10);

	$generateddbname = 'dbn'.substr(getRandomPassword(true), 0, 8);
	$generateddbusername = 'dbu'.substr(getRandomPassword(true), 0, 9);
	$generateddbpassword = substr(getRandomPassword(true), 0, 10);
	$generateddbhostname = $sldAndSubdomain.'.'.$domainname;
	$generateddbport = 3306;
	$generatedunixhostname = $sldAndSubdomain.'.'.$domainname;


	// Create thirdparty

	$db->begin();	// Start transaction

	$tmpthirdparty->oldcopy = dol_clone($tmpthirdparty);

	$password_encoding = 'password_hash';
	$password_crypted = dol_hash($password);

	$tmpthirdparty->name = $orgname;
	$tmpthirdparty->email = $email;
	$tmpthirdparty->client = 3;
	$tmpthirdparty->tva_assuj = 1;
	$tmpthirdparty->array_options['options_dolicloud'] = 'yesv2';
	$tmpthirdparty->array_options['options_date_registration'] = dol_now();
	$tmpthirdparty->array_options['options_source']='REGISTERFORM'.($origin?'-'.$origin:'');
	$tmpthirdparty->array_options['options_password'] = $password;
	if (is_object($partnerthirdparty)) $tmpthirdparty->parent = $partnerthirdparty->id;		// Add link to parent/reseller
	if ($country_code)
	{
		$tmpthirdparty->country_id = getCountry($country_code, 3, $db);
	}

	if ($tmpthirdparty->id > 0)
	{
		if (empty($reusesocid))
		{
			$result = $tmpthirdparty->update(0, $user);
			if ($result <= 0)
			{
				$db->rollback();
				setEventMessages($tmpthirdparty->error, $tmpthirdparty->errors, 'errors');
				header("Location: ".$newurl);
				exit;
			}
		}
	}
	else
	{
		$tmpthirdparty->code_client = -1;
		$result = $tmpthirdparty->create($user);
		if ($result <= 0)
		{
			$db->rollback();
			setEventMessages($tmpthirdparty->error, $tmpthirdparty->errors, 'errors');
			header("Location: ".$newurl);
			exit;
		}
	}

	if (! empty($conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG))
	{
		$result = $tmpthirdparty->setCategories(array($conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG => $conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG), 'customer');
		if ($result < 0)
		{
			$db->rollback();
			setEventMessages($tmpthirdparty->error, $tmpthirdparty->errors, 'errors');
			header("Location: ".$newurl);
			exit;
		}
	}
	else
	{
		dol_print_error_email('SETUPTAG', 'Setup of module not complete. The default customer tag is not defined.', null, 'alert alert-error');
		exit;
	}

	// Create contract/instance
	if (! $error)
	{
		dol_syslog("Create contract with deployment status 'Processing'");

		$contract->ref_customer = $sldAndSubdomain.$tldid;
		$contract->socid = $tmpthirdparty->id;
		$contract->commercial_signature_id = $user->id;
		$contract->commercial_suivi_id = $user->id;
		$contract->date_contrat = $now;
		$contract->note_private = 'Contact created from the online instance registration form';

		$contract->array_options['options_plan'] = $tmppackage->ref;
		$contract->array_options['options_deployment_status'] = 'processing';
		$contract->array_options['options_deployment_date_start'] = $now;
		$contract->array_options['options_deployment_init_email'] = $email;
		$contract->array_options['options_deployment_init_adminpass'] = $password;
		$contract->array_options['options_date_endfreeperiod'] = dol_time_plus_duree($now, 15, 'd');
		$contract->array_options['options_undeployment_date'] = '';
		$contract->array_options['options_undeployment_ip'] = '';
		$contract->array_options['options_hostname_os'] = $generatedunixhostname;
		$contract->array_options['options_username_os'] = $generatedunixlogin;
		$contract->array_options['options_password_os'] = $generatedunixpassword;
		$contract->array_options['options_hostname_db'] = $generateddbhostname;
		$contract->array_options['options_database_db'] = $generateddbname;
		$contract->array_options['options_port_db'] = $generateddbport;
		$contract->array_options['options_username_db'] = $generateddbusername;
		$contract->array_options['options_password_db'] = $generateddbpassword;
		//$contract->array_options['options_nb_users'] = 1;
		//$contract->array_options['options_nb_gb'] = 0.01;
		$contract->array_options['options_deployment_ip'] = $_SERVER["REMOTE_ADDR"];

		$prefix=dol_getprefix('');
		$cookieregistrationa='DOLREGISTERA_'.$prefix;
		$cookieregistrationb='DOLREGISTERB_'.$prefix;
		$nbregistration = (int) $_COOKIE[$cookieregistrationa];
		if (! empty($_COOKIE[$cookieregistrationa]))
		{
			$contract->array_options['options_cookieregister_counter'] = ($nbregistration ? $nbregistration : 1);
		}
		if (! empty($_COOKIE[$cookieregistrationb]))
		{
			$contract->array_options['options_cookieregister_previous_instance'] = dol_decode($_COOKIE[$cookieregistrationb]);
		}

		$result = $contract->create($user);
		if ($result <= 0)
		{
			dol_print_error_email('CREATECONTRACT', $contract->error, $contract->errors, 'alert alert-error');
			exit;
		}
	}

	$object = $tmpthirdparty;

	// Create contract line for INSTANCE
	if (! $error)
	{
		dol_syslog("Add line to contract for INSTANCE");

		if (empty($object->country_code))
		{
			$object->country_code = dol_getIdFromCode($db, $object->country_id, 'c_country', 'rowid', 'code');
		}

		$qty = 1;
		//if (! empty($contract->array_options['options_nb_users'])) $qty = $contract->array_options['options_nb_users'];
		$vat = get_default_tva($mysoc, $object, $tmpproduct->id);
		$localtax1_tx = get_default_localtax($mysoc, $object, 1, 0);
		$localtax2_tx = get_default_localtax($mysoc, $object, 2, 0);
		//var_dump($mysoc->country_code);
		//var_dump($object->country_code);
		//var_dump($tmpproduct->tva_tx);
		//var_dump($vat);exit;

		$price = $tmpproduct->price;
		$discount = 0;

		$productidtocreate = $tmpproduct->id;

		$contractlineid = $contract->addline('', $price, $qty, $vat, $localtax1_tx, $localtax2_tx, $productidtocreate, $discount, $date_start, $date_end, 'HT', 0);
		if ($contractlineid < 0)
		{
			dol_print_error_email('CREATECONTRACTLINE1', $contract->error, $contract->errors, 'alert alert-error');
			exit;
		}
	}

	//var_dump('user:'.$dolicloudcustomer->price_user);
	//var_dump('instance:'.$dolicloudcustomer->price_instance);
	//exit;

	// Create contract line for other products
	if (! $error)
	{
		dol_syslog("Add line to contract for depending products like USERS");

		$prodschild = $tmpproduct->getChildsArbo($tmpproduct->id,1);

		$tmpsubproduct = new Product($db);
		$j=2;
		foreach($prodschild as $prodid => $arrayprodid)
		{
			$tmpsubproduct->fetch($prodid);	// To load the price

			$qty = 1;
			//if (! empty($contract->array_options['options_nb_users'])) $qty = $contract->array_options['options_nb_users'];
			$vat = get_default_tva($mysoc, $object, $prodid);
			$localtax1_tx = get_default_localtax($mysoc, $object, 1, $prodid);
			$localtax2_tx = get_default_localtax($mysoc, $object, 2, $prodid);

			$price = $tmpsubproduct->price;
			$discount = 0;

			if ($price > 0 && $qty > 0)
			{
				$contractlineid = $contract->addline('', $price, $qty, $vat, $localtax1_tx, $localtax2_tx, $prodid, $discount, $date_start, $date_end, 'HT', 0);
				if ($contractlineid < 0)
				{
					dol_print_error_email('CREATECONTRACTLINE'.$j, $contract->error, $contract->errors, 'alert alert-error');
					exit;
				}
			}

			$j++;
		}
	}

	dol_syslog("Reload all lines after creation to have contract->lines ok");
	$contract->fetch_lines();

	if (! $error)
	{
		$db->commit();
	}
	else
	{
		$db->rollback();
	}
}



// -----------------------------------------------------------------------------------------------------------------------
// Create unix user and directories, DNS, virtual host and database
//
// With old method:
// Check the user www-data is allowed to "sudo /usr/bin/create_test_instance.sh"
// If you get error "sudo: PERM_ROOT: setresuid(0, -1, -1): Operation not permitted", check module mpm_itk
//<IfModule mpm_itk_module>
//LimitUIDRange 0 5000
//LimitGIDRange 0 5000
//</IfModule>
// If you get error "sudo: sorry, you must have a tty to run sudo", disable key "Defaults requiretty" from /etc/sudoers
//
// With new method, call the deploy server
// -----------------------------------------------------------------------------------------------------------------------

if (! $error)
{
	dol_include_once('/sellyoursaas/class/sellyoursaasutils.class.php');
	$sellyoursaasutils = new SellYourSaasUtils($db);

	$result = $sellyoursaasutils->sellyoursaasRemoteAction('deployall', $contract, 'admin', $email, $password);
	if ($result <= 0)
	{
		$error++;
		$errormessages=$sellyoursaasutils->errors;
		if ($sellyoursaasutils->error) $errormessages[]=$sellyoursaasutils->error;
	}
}


// Finish deployall
if ($fromsocid) $comment = 'Activation after deployment from instance creation by reseller id='.$fromsocid;
else $comment = 'Activation after deployment from online registration or dashboard';

// Activate all lines
if (! $error)
{
	dol_syslog("Activate all lines - by register_instance");

	$contract->context['deployallwasjustdone']=1;		// Add a key so trigger into activateAll will know we have just made a "deployall"

	$result = $contract->activateAll($user, dol_now(), 1, $comment);			// This may execute the triggers
	if ($result <= 0)
	{
		$error++;
		$errormessages[]=$contract->error;
		$errormessages[]=array_merge($contract->errors, $errormessages);
	}
}

// End of deployment is now OK / Complete
if (! $error)
{
	$contract->array_options['options_deployment_status'] = 'done';
	$contract->array_options['options_deployment_date_end'] = dol_now('tzserver');
	$contract->array_options['options_undeployment_date'] = '';
	$contract->array_options['options_undeployment_ip'] = '';

	// Set cookie to store last registered instance
	$prefix=dol_getprefix('');
	$cookieregistrationa='DOLREGISTERA_'.$prefix;
	$cookieregistrationb='DOLREGISTERB_'.$prefix;
	$nbregistration = ((int) $_COOKIE[$cookieregistrationa] + 1);
	setcookie($cookieregistrationa, $nbregistration, 0, "/", null, false, true);	// Cookie to count nb of registration from this computer
	setcookie($cookieregistrationb, dol_encode($contract->ref_customer), 0, "/", null, false, true);					// Cookie to save previous registered instance

	$result = $contract->update($user);
	if ($result < 0)
	{
		// We ignore errors. This should not happen in real life.
		//setEventMessages($contract->error, $contract->errors, 'errors');
	}
}



// Go to dashboard with login session forced

if (! $error)
{
	// Deployement is complete and finished.
	// First time we go at end of process, so we send en email.

	$newurl=$_SERVER["PHP_SELF"];
	$newurl=preg_replace('/register_instance\.php/', 'index.php?welcomecid='.$contract->id.(($fromsocid > 0)?'&fromsocid='.$fromsocid:''), $newurl);

	$anonymoususer=new User($db);
	$anonymoususer->fetch($conf->global->SELLYOURSAAS_ANONYMOUSUSER);
	$_SESSION['dol_login']=$anonymoususer->login;				// Set dol_login so next page index.php we found we are already logged.

	if ($fromsocid > 0) $_SESSION['dol_loginsellyoursaas']=$fromsocid;
	else $_SESSION['dol_loginsellyoursaas']=$contract->thirdparty->id;

	$_SESSION['initialapplogin']='admin';
	$_SESSION['initialapppassword']=$password;

	// Send deployment email
	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
	include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
	$formmail=new FormMail($db);

	$arraydefaultmessage=$formmail->getEMailTemplate($db, 'contract', $user, $langs, 0, 1, 'InstanceDeployed');		// Templates are init into data.sql

	$substitutionarray=getCommonSubstitutionArray($langs, 0, null, $contract);
	$substitutionarray['__PACKAGELABEL__']=$tmppackage->label;
	$substitutionarray['__APPUSERNAME__']=$_SESSION['initialapplogin'];
	$substitutionarray['__APPPASSWORD__']=$password;

	complete_substitutions_array($substitutionarray, $langs, $contract);

	$subject = make_substitutions($arraydefaultmessage->topic, $substitutionarray, $langs);
	$msg     = make_substitutions($arraydefaultmessage->content, $substitutionarray, $langs);
	$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
	$to = $contract->thirdparty->email;

	$cmail = new CMailFile($subject, $to, $from, $msg, array(), array(), array(), '', '', 0, 1);
	$result = $cmail->sendfile();
	if (! $result)
	{
		$error++;
		setEventMessages($cmail->error, $cmail->errors, 'warning');
	}

	dol_syslog("Deployment successful");
	header("Location: ".$newurl);
	exit;
}


// Error

dol_syslog("Deployment error");

if ($reusecontractid > 0)
{
	setEventMessages('', $errormessages, 'errors');
	header("Location: ".$newurl);
	exit();
}


// If we are here, there was an error

$errormessages[] = 'Deployement of instance '.$sldAndSubdomain.$tldid.' started but failed.';
$errormessages[] = 'Our team was alerted. You will receive an email as soon as deployment is complete.';

dol_syslog("Error in deployment", LOG_ERR);
$email = new CMailFile('[Alert] Registration/deployment error', $conf->global->SELLYOURSAAS_SUPERVISION_EMAIL, $conf->global->SELLYOURSAAS_NOREPLY_EMAIL, join("\n",$errormessages)."\n\nParameters of command used:\n".$commandurl);
$email->sendfile();


$conf->dol_hide_topmenu = 1;
$conf->dol_hide_leftmenu = 1;


$head='<link rel="icon" href="img/favicon.ico">
<!-- Bootstrap core CSS -->
<!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.css" rel="stylesheet">-->
<link href="dist/css/bootstrap.css" rel="stylesheet">
<link href="dist/css/myaccount.css" rel="stylesheet">';

llxHeader($head, $langs->trans("ERPCRMOnlineSubscription"), '', '', 0, 0, array(), array('../dist/css/myaccount.css'));

?>

<div id="waitMask" style="display:none;">
    <font size="3em" style="color:#888; font-weight: bold;"><?php echo $langs->trans("InstallingInstance") ?><br><?php echo $langs->trans("PleaseWait") ?><br></font>
    <img id="waitMaskImg" width="100px" src="<?php echo 'ajax-loader.gif'; ?>" alt="Loading" />
</div>

<div class="signup">

      <div style="text-align: center;">
        <?php
        $linklogo = DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&file='.urlencode('/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_SMALL);

        if (GETPOST('partner','alpha'))
        {
            $tmpthirdparty = new Societe($db);
            $result = $tmpthirdparty->fetch(GETPOST('partner','alpha'));
            $logo = $tmpthirdparty->logo;
        }
        print '<img style="center" class="logoheader"  src="'.$linklogo.'" id="logo" />';
        ?>
      </div>
      <div class="block medium">

        <header class="inverse">
          <h1><?php echo $langs->trans("Registration") ?> <small><?php echo ($tmpproduct->label?' - '.$tmpproduct->label:''); ?></small></h1>
        </header>


      <form action="register_instance" method="post" id="formregister">
        <div class="form-content">
    	  <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
          <input type="hidden" name="service" value="<?php echo dol_escape_htmltag($tmpproduct->ref); ?>" />
          <input type="hidden" name="package" value="<?php echo dol_escape_htmltag($tmppackage->ref); ?>" />
          <input type="hidden" name="partner" value="<?php echo dol_escape_htmltag($partner); ?>" />

          <section id="enterUserAccountDetails">

			<center>OOPS...</center>
			<?php
			dol_print_error_email('DEPLOY'.$generateddbhostname, '', $errormessages, 'alert alert-error');
			?>

		  </section>
		</div>
	   </form>
	   </div>
</div>

<?php
llxFooter();
