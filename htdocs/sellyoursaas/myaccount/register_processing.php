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

require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/cron/class/cronjob.class.php';
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
	}

	$user->getrights();
}

$orgname = trim(GETPOST('orgName','alpha'));
$email = trim(GETPOST('username','alpha'));
$password = GETPOST('password','alpha');
$password2 = GETPOST('password2','alpha');
$country_code = GETPOST('address.country','alpha');
$sldAndSubdomain = GETPOST('sldAndSubdomain','alpha');
$remoteip = $_SERVER['REMOTE_ADDRESS'];



/*
 * Actions
 */

//print "orgname = ".$orgname." email=".$email." password=".$password." password2=".$password2." country_code=".$country_code." remoteip=".$remoteip." sldAndSubdomain=".$sldAndSubdomain;


// Back to url
$newurl=$_SERVER["PHP_SELF"];
$newurl=preg_replace('/register_processing/', 'register', $newurl);
//exit;
//$newurl='myaccount.'.$conf->global->SELLYOURSAAS_MAIN_DOMAIN_NAME.'/register.php';

if (! preg_match('/\?/', $newurl)) $newurl.='?';
if (! preg_match('/orgName/i', $newurl)) $newurl.='&orgName='.urlencode($orgname);
if (! preg_match('/username/i', $newurl)) $newurl.='&username='.urlencode($email);
if (! preg_match('/address.country/i', $newurl)) $newurl.='&address.country='.urlencode($country_code);
if (! preg_match('/sldAndSubdomain/i', $sldAndSubdomain)) $newurl.='&sldAndSubdomain='.urlencode($sldAndSubdomain);

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
	header("Location: ".$newurl);
	exit;
}


// Generate credentials

$generatedunixlogin = 'usr'.substr(getRandomPassword(true), 9);		// This is result of dol_hash, there is no special chars
$generatedunixpassword = substr(getRandomPassword(true), 12);		// This is result of dol_hash, there is no special chars


// Start creation of instance

$error = 0;

$db->begin();	// Start transaction


$tmpthirdparty->nom = $orgname;
$tmpthirdparty->email = $email;
$tmpthirdparty->client = 3;
$tmpthirdparty->array_options['options_dolicloud'] = 'yesv2';
$tmpthirdparty->array_options['options_date_registration'] = dol_now();

$result = $tmpthirdparty->create($user);
if ($result <= 0)
{
	$error++;
}

if (! empty($conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG))
{
	$result = $tmpthirdparty->setCategories(array($conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG => $conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG), 'customer');
	if ($result < 0)
	{
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
	$command = 'sudo /usr/bin/create_user_instance.sh '.$generatedunixlogin.' '.$generatedunixpassword;
	//$command = '/usr/bin/aaa.sh';
	$outputfile = $conf->sellyoursaas->dir_temp.'/register.'.dol_getmypid().'.out';

	$cronjob = new CronJob($db);
	$cronjob->executeCLI($command, $outputfile, 2);

	var_dump($cronjob);


	// Create account to dashboard
	$websiteaccount = new WebsiteAccount($db);


}


// Go to dashboard with login session forced
if (! $error)
{


}




