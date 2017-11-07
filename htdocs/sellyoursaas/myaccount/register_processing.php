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


include ('./common.inc.php');

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
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/websiteaccount.class.php';

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
	print 'Service/Plan '.$productref.' was not found.';
	exit;
}
if (! preg_match('/^DOLICLOUD-PACK-(.+)$/', $tmpproduct->ref, $reg))
{
	print 'Service/Plan name is invalid. Name must be DOLICLOUD-PACK-...';
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


print $langs->trans("PleaseWait");

// Create thirdparty (if it already exist, return warning)
$tmpthirdparty=new Societe($db);
$tmpthirdparty->fetch(0, '', '', '', '', '', '', '', '', '', $email);
if ($tmpthirdparty->id)
{
	setEventMessages($langs->trans("AccountAlreadyExistsForEmail", 'https://myaccount.dolicloud.com'), null, 'errors');
	// TODO Restore this
	//	header("Location: ".$newurl);
	//	exit;
}


// Generate credentials

$generatedunixlogin = strtolower('osu'.substr(getRandomPassword(true), 0, 9));		// Must be lowercase as it can be used for default email
$generatedunixpassword = substr(getRandomPassword(true), 0, 10);

$generateddbname = 'dbn'.substr(getRandomPassword(true), 0, 8);
$generateddbusername = 'dbu'.substr(getRandomPassword(true), 0, 9);
$generateddbpassword = substr(getRandomPassword(true), 0, 10);

$domainname = 'with.dolicloud.com';


// Start creation of instance

$error = 0;

$db->begin();	// Start transaction


$tmpthirdparty->nom = $orgname;
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
		$error++;
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
		$error++;
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
	dol_print_error('', 'Setup of module not complete. Missing the default customer tag');
	$error++;
}

if (! $error)
{
	$db->commit();
}
else
{
	$db->rollback();
}

//print ini_get('safe_mode');exit;

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
	// TODO create tmp config file from $tmppackage->conffile1
	// Replace __INSTANCEDIR__, __INSTALLHOURS__, __INSTALLMINUTES__, __OSUSERNAME__, __APPUNIQUEKEY__, __APPDOMAIN__, __APPWEBROOTPATH__,
	$tmppackage->srcconffile1 = '/tmp/aaa';
	$tmppackage->srccronfile = '/tmp/bbb';

	//$command = 'sudo /usr/bin/create_user_instance.sh '.$generatedunixlogin.' '.$generatedunixpassword;
	$command = '/usr/bin/create_user_instance.sh '.$generatedunixlogin.' '.$generatedunixpassword.' '.$sldAndSubdomain.' '.$domainname;
	$command.= ' '.$generateddbname.' '.$generateddbusername.' '.$generateddbpassword;
	$command.= ' "'.$tmppackage->srcconffile1.'" "'.$tmppackage->targetconffile1.'" "'.$tmppackage->datafile1.'"';
	$command.= ' "'.$tmppackage->srcfile1.'" "'.$tmppackage->targetsrcfile1.'" "'.$tmppackage->srcfile2.'" "'.$tmppackage->targetsrcfile2.'" "'.$tmppackage->srcfile3.'" "'.$tmppackage->targetsrcfile3.'"';
	$command.= ' "'.$tmppackage->srccronfile.'"';

	//$command = '/usr/bin/aaa.sh';
	$outputfile = $conf->sellyoursaas->dir_temp.'/register.'.dol_getmypid().'.out';

	include_once DOL_DOCUMENT_ROOT.'/core/class/utils.class.php';
	$utils = new Utils($db);
	$retarray = $utils->executeCLI($command, $outputfile, 1);


	//var_dump($cronjob);
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


// Go to dashboard with login session forced
if (! $error)
{


}




