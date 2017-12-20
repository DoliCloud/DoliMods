<?php

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				    // If this page is public (can be called outside logged session)


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
require_once DOL_DOCUMENT_ROOT.'/cron/class/cronjob.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/websiteaccount.class.php';

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
		dol_print_error('', 'Error setup of module not complete or wrong. Missing the anonymous user.');
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
$remoteip = $_SERVER['REMOTE_ADDRESS'];
$generateduniquekey=getRandomPassword(true);
$partner=GETPOST('partner','alpha');
$plan=GETPOST('plan','alpha');

include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
$productref='DOLICLOUD-PACK-Dolibarr';
if ($plan)	// Plan is a product/service
{
	$productref=$plan;
}
$tmpproduct = new Product($db);
$result = $tmpproduct->fetch(0, $productref);
if (empty($tmpproduct->id))
{
	print 'Service/Plan (Product ref) '.$productref.' was not found.';
	exit;
}
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

$packageref = $reg[1];

dol_include_once('/sellyoursaas/class/packages.class.php');
$tmppackage = new Packages($db);
$tmppackage->fetch(0, $packageref);
if (empty($tmppackage->id))
{
	print 'Package name '.$packageref.' was not found.';
	exit;
}



/*
 * Actions
 */

//print "partner=".$partner." plan=".$plan." orgname = ".$orgname." email=".$email." password=".$password." password2=".$password2." country_code=".$country_code." remoteip=".$remoteip." sldAndSubdomain=".$sldAndSubdomain;


// Back to url
$newurl=$_SERVER["PHP_SELF"];
$newurl=preg_replace('/register_processing/', 'register', $newurl);
//exit;
//$newurl='myaccount.'.$conf->global->SELLYOURSAAS_MAIN_DOMAIN_NAME.'/register.php';

if (! preg_match('/\?/', $newurl)) $newurl.='?';
if (! preg_match('/orgName/i', $newurl)) $newurl.='&orgName='.urlencode($orgname);
if (! preg_match('/username/i', $newurl)) $newurl.='&username='.urlencode($email);
if (! preg_match('/address_country/i', $newurl)) $newurl.='&address_country='.urlencode($country_code);
if (! preg_match('/sldAndSubdomain/i', $sldAndSubdomain)) $newurl.='&sldAndSubdomain='.urlencode($sldAndSubdomain);
if (! preg_match('/plan/i', $newurl)) $newurl.='&plan='.urlencode($plan);
//if (! preg_match('/service/i', $newurl)) $newurl.='&orgName='.urlencode($orgname);
if (! preg_match('/partner/i', $newurl)) $newurl.='&partner='.urlencode($partner);

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
if (empty($sldAndSubdomain))
{
	setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("NameForYourApplication")), null, 'errors');
	header("Location: ".$newurl);
	exit;
}
if ($password != $password2)
{
    setEventMessages($langs->trans("ErrorPasswordMismatch"), null, 'errors');
    header("Location: ".$newurl);
    exit;
}



/*
 * View
 */

print $langs->trans("PleaseWait");		// Message if redirection after this page fails


// Create thirdparty (if it already exist, return warning)
dol_syslog("Fetch thirdparty from email ".$email);
$tmpthirdparty=new Societe($db);
$result = $tmpthirdparty->fetch(0, '', '', '', '', '', '', '', '', '', $email);
if ($result < 0)
{
	dol_print_error_email('FETCHTP'.$email, $tmpthirdparty->error, $tmpthirdparty->errors);
	exit;
}
else if ($result > 0)	// Found one record
{
	setEventMessages($langs->trans("AccountAlreadyExistsForEmail", $conf->global->SELLYOURSAAS_ACCOUNT_URL), null, 'errors');
	// TODO Restore this
	//	header("Location: ".$newurl);
	//exit;
}
else dol_syslog("Not found");

dol_syslog("Fetch contract from domain name ".$sldAndSubdomain);
$contract = new Contrat($db);
$result = $contract->fetch(0, '', $sldAndSubdomain);
if ($result > 0)
{
	setEventMessages($langs->trans("InstanceNameAlreadyExists", $sldAndSubdomain), null, 'errors');
	// TODO Restore this
	//header("Location: ".$newurl);
	//exit;
}
else dol_syslog("Not found");


// Generate credentials

$generatedunixlogin = strtolower('osu'.substr(getRandomPassword(true), 0, 9));		// Must be lowercase as it can be used for default email
$generatedunixpassword = substr(getRandomPassword(true), 0, 10);

$domainname = 'with.dolicloud.com';


$generateddbname = 'dbn'.substr(getRandomPassword(true), 0, 8);
$generateddbusername = 'dbu'.substr(getRandomPassword(true), 0, 9);
$generateddbpassword = substr(getRandomPassword(true), 0, 10);
$generateddbhostname = $sldAndSubdomain.'.'.$domainname;
$generatedunixhostname = $sldAndSubdomain.'.'.$domainname;


// Start creation of instance

$error = 0;

$db->begin();	// Start transaction


$tmpthirdparty->name = $orgname;
$tmpthirdparty->email = $email;
$tmpthirdparty->client = 3;
$tmpthirdparty->array_options['options_dolicloud'] = 'yesv2';
$tmpthirdparty->array_options['options_date_registration'] = dol_now();
if ($country_code)
{
	$tmpthirdparty->country_id = getCountry($country_code, 3, $db);
}

if ($tmpthirdparty->id > 0)
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
	dol_print_error('', 'Setup of module not complete. The default customer tag is not defined.');
	$error++;
	exit;
}

// Create contract/instance
if (! $error)
{
	dol_syslog("Create contract with deployment status 'Processing'");

	$now = dol_now();

	$contract->ref_customer = $sldAndSubdomain;
	$contract->socid = $tmpthirdparty->id;
	$contract->commercial_signature_id = $user->id;
	$contract->commercial_suivi_id = $user->id;
	$contract->date_contrat = $now;
	$contract->note_private = 'Created from web interface';

	$contract->array_options['options_plan'] = $tmppackage->ref;
	$contract->array_options['options_deployment_status'] = 'processing';
	$contract->array_options['options_deployment_date_start'] = $now;
	$contract->array_options['options_date_endfreeperiod'] = dol_time_plus_duree($now, 15, 'd');
	$contract->array_options['options_hostname_os'] = $generatedunixhostname;
	$contract->array_options['options_username_os'] = $generatedunixlogin;
	$contract->array_options['options_password_os'] = $generatedunixpassword;
	$contract->array_options['options_hostname_db'] = $generateddbhostname;
	$contract->array_options['options_database_db'] = $generateddbname;
	$contract->array_options['options_port_db'] = 3306;
	$contract->array_options['options_username_db'] = $generateddbusername;
	$contract->array_options['options_password_db'] = $generateddbpassword;
	$contract->array_options['options_nb_users'] = 1;
	$contract->array_options['options_nb_gb'] = 0.01;
	$contract->array_options['options_deployment_ip'] = $_SERVER["REMOTE_ADDR"];

	$result = $contract->create($user);
	if ($result <= 0)
	{
		dol_print_error_email('FETCHTP', $contract->error, $contract->errors);
		exit;
	}
}

if (! $error)
{
	$website = new Website($db);
	$website->fetch(0, 'sellyoursaas');
	//var_dump($website);

	// Create account to dashboard
	$websiteaccount = new WebsiteAccount($db);
	$websiteaccount->fk_website = $website->id;
	$websiteaccount->fk_soc = $tmpthirdparty->id;
	$websiteaccount->login = $email;
	$websiteaccount->pass_encoding = 'sha1md5';
	$websiteaccount->pass_crypted = dol_hash($password, 2);
	$websiteaccount->note_private = 'Initial pass = '.$password;
	$websiteaccount->status = 1;
	$result = $websiteaccount->create($user);
	if ($result < 0)
	{
		// We ignore errors. This should not happen in real life.
		//setEventMessages($websiteaccount->error, $websiteaccount->errors, 'errors');
	}
}

$object = $tmpthirdparty;

// Create contract line for INSTANCE
if (! $error)
{
	if (empty($object->country_code))
	{
		$object->country_code = dol_getIdFromCode($db, $object->country_id, 'c_country', 'rowid', 'code');
	}
	$qty = 1;
	//if (! empty($contract->array_options['options_nb_users'])) $qty = $contract->array_options['options_nb_users'];
	$vat = get_default_tva($mysoc, $object, $product->id);
	$localtax1_tx = get_default_localtax($mysoc, $object, 1, 0);
	$localtax2_tx = get_default_localtax($mysoc, $object, 2, 0);
	//var_dump($mysoc->country_code);
	//var_dump($object->country_code);
	//var_dump($product->tva_tx);
	//var_dump($vat);exit;

	$price = $product->price;
	if ($dolicloudcustomer->id > 0)
	{
		$price = $dolicloudcustomer->price_instance;
		if (! preg_match('/yearly/', $dolicloudcustomer->plan)) $price = $price * 12;
	}

	if ($price == 0) $discount = 0;

	$contactlineid = $contract->addline('', $price, $qty, $vat, $localtax1_tx, $localtax2_tx, $productidtocreate, $discount, $date_start, $date_end, 'HT', 0);
	if ($contactlineid < 0)
	{
		$error++;
		setEventMessages($contract->error, $contract->errors, 'errors');
	}
}

//var_dump('user:'.$dolicloudcustomer->price_user);
//var_dump('instance:'.$dolicloudcustomer->price_instance);
//exit;

// Create contract line for USERS
if (! $error)
{
	$qty = 0;
	if (! empty($contract->array_options['options_nb_users'])) $qty = $contract->array_options['options_nb_users'];
	$vat = get_default_tva($mysoc, $object, 0);
	$localtax1_tx = get_default_localtax($mysoc, $object, 1, 0);
	$localtax2_tx = get_default_localtax($mysoc, $object, 2, 0);

	$price = $product->array_options['options_price_per_user'];
	if ($dolicloudcustomer->id > 0)
	{
		$price = $dolicloudcustomer->price_user;
		if (! preg_match('/yearly/', $dolicloudcustomer->plan)) $price = $price * 12;
	}

	if ($price > 0 && $qty > 0)
	{
		$contactlineid = $contract->addline('Additional users', $price, $qty, $vat, $localtax1_tx, $localtax2_tx, 0, $discount, $date_start, $date_end, 'HT', 0);
		if ($contactlineid < 0)
		{
			$error++;
			setEventMessages($contract->error, $contract->errors, 'errors');
		}
	}
}

if (! $error)
{
	$db->commit();
}
else
{
	$db->rollback();
}



// Create unix user and directories and DNS
// Check the user www-data is allowed to "sudo /usr/bin/create_test_instance.sh"
// If you get error "sudo: PERM_ROOT: setresuid(0, -1, -1): Operation not permitted", check module mpm_itk
//<IfModule mpm_itk_module>
//LimitUIDRange 0 5000
//LimitGIDRange 0 5000
//</IfModule>
// If you get error "sudo: sorry, you must have a tty to run sudo", disable key "Defaults requiretty" from /etc/sudoers

if (! $error)
{
	$targetdir = $conf->global->DOLICLOUD_INSTANCES_PATH;

	// Replace __INSTANCEDIR__, __INSTALLHOURS__, __INSTALLMINUTES__, __OSUSERNAME__, __APPUNIQUEKEY__, __APPDOMAIN__, ...
	$substitarray=array(
	'__INSTANCEDIR__'=>$targetdir.'/'.$generatedunixlogin.'/'.$generateddbname,
	'__DOL_DATA_ROOT__'=>DOL_DATA_ROOT,
	'__INSTALLHOURS__'=>dol_print_date($now, '%H'),
	'__INSTALLMINUTES__'=>dol_print_date($now, '%M'),
	'__OSHOSTNAME__'=>$generatedunixhostname,
	'__OSUSERNAME__'=>$generatedunixlogin,
	'__OSPASSWORD__'=>$generatedunixpassword,
	'__APPUSERNAME__'=>'admin',
	'__APPPASSWORD__'=>$password,
	'__APPUNIQUEKEY__'=>$generateduniquekey,
	'__APPDOMAIN__'=>$domainname.'.'.$sldAndSubdomain,
	'__DBHOSTNAME__'=>$generateddbhostname,
	'__DBNAME__'=>$generateddbname,
	'__DBUSER__'=>$generateddbusername,
	'__DBPASSWORD__'=>$generateddbpassword
	);

	$tmppackage->srcconffile1 = '/tmp/config.php.'.$sldAndSubdomain.'.tmp';
	$tmppackage->srccronfile = '/tmp/cron.'.$sldAndSubdomain.'.tmp';

	$conffile = make_substitutions($tmppackage->conffile1, $substitarray);
	$cronfile = make_substitutions($tmppackage->crontoadd, $substitarray);

	$tmppackage->targetconffile1 = make_substitutions($tmppackage->targetconffile1, $substitarray);
	$tmppackage->datafile1 = make_substitutions($tmppackage->datafile1, $substitarray);
	$tmppackage->srcfile1 = make_substitutions($tmppackage->srcfile1, $substitarray);
	$tmppackage->srcfile2 = make_substitutions($tmppackage->srcfile2, $substitarray);
	$tmppackage->srcfile3 = make_substitutions($tmppackage->srcfile3, $substitarray);
	$tmppackage->targetsrcfile1 = make_substitutions($tmppackage->targetsrcfile1, $substitarray);
	$tmppackage->targetsrcfile2 = make_substitutions($tmppackage->targetsrcfile2, $substitarray);
	$tmppackage->targetsrcfile3 = make_substitutions($tmppackage->targetsrcfile3, $substitarray);


	dol_syslog("Create conf file ".$tmppackage->srcconffile1);
	file_put_contents($tmppackage->srcconffile1, $conffile);
	dol_syslog("Create cron file ".$tmppackage->srccronfile1);
	file_put_contents($tmppackage->srccronfile, $cronfile);

	//$command = 'sudo /usr/bin/create_user_instance.sh '.$generatedunixlogin.' '.$generatedunixpassword;
	$command = '/usr/bin/create_user_instance.sh all '.$generatedunixlogin.' '.$generatedunixpassword.' '.$sldAndSubdomain.' '.$domainname;
	$command.= ' '.$generateddbname.' '.$generateddbusername.' '.$generateddbpassword;
	$command.= ' "'.$tmppackage->srcconffile1.'" "'.$tmppackage->targetconffile1.'" "'.$tmppackage->datafile1.'"';
	$command.= ' "'.$tmppackage->srcfile1.'" "'.$tmppackage->targetsrcfile1.'" "'.$tmppackage->srcfile2.'" "'.$tmppackage->targetsrcfile2.'" "'.$tmppackage->srcfile3.'" "'.$tmppackage->targetsrcfile3.'"';
	$command.= ' "'.$tmppackage->srccronfile.'" "'.$targetdir.'"';

	//$command = '/usr/bin/aaa.sh';
	$outputfile = $conf->sellyoursaas->dir_temp.'/register.'.dol_getmypid().'.out';

	// To remove
	print "<br>Command: ".$command.'<br>';
	var_dump($retarray);
	sleep(10);

	include_once DOL_DOCUMENT_ROOT.'/core/class/utils.class.php';
	$utils = new Utils($db);
	$retarray = $utils->executeCLI($command, $outputfile, 1);

	if ($retarra['result'] != 0)
	{
		$error++;
	}
	//var_dump($cronjob);

}



if (! $error)
{
	$contract->array_options['options_deployment_status'] = 'done';
	$contract->array_options['options_deployment_date_end'] = dol_now();

	$result = $contract->update($user);
	if ($result < 0)
	{
		// We ignore errors. This should not happen in real life.
		//setEventMessages($contract->error, $contract->errors, 'errors');
	}

	$discount = 0;
}

// Activate all lines
if (! $error)
{
	$result = $contract->activateAll($user);
	if ($result <= 0)
	{
		$error++;
		setEventMessages($contract->error, $contract->errors, 'errors');
	}
}

// Execute personalized SQL requests
if (! $error)
{
	$sqltoexecute = make_substitutions($tmppackage->sqlafter, $substitarray);

	//var_dump($generateddbhostname);
	$dbinstance = @getDoliDBInstance('mysqli', $generateddbhostname, $generateddbusername, $generateddbpassword, $generateddbname, 3306);
	if (! $dbinstance || ! $dbinstance->connected)
	{
		$error++;
		dol_print_error_email('GETDOLIDBI'.$generateddbhostname, $dbinstance->error, $dbinstance->errors);
		exit;
	}
	else
	{
		$dbinstance->query($sqtoexecute);

	}
}





// Go to dashboard with login session forced
if (! $error)
{
	$newurl=$_SERVER["PHP_SELF"];
	$newurl=preg_replace('/register_processing\.php/', 'index\.php?welcomecid='.$contract->id, $newurl);

	$_SESSION['initialapplogin']='admin';
	$_SESSION['initialapppassword']=$password;

	dol_syslog("Deployment successful");
	header("Location: ".$newurl);
	exit;
}
else
{
	dol_syslog("Error in deployment", LOG_ERR);
	$email = new CMailFile('WARNING: Deployment error', 'supervision@dolicloud.com', $conf->global->MAIN_MAIL_EMAIL_FROM, 'Deployement of instance '.$sldAndSubdomain.' failed.'."\nCommand = ".$command);
	$email->sendfile();
}

