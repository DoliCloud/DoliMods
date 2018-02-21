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
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/websiteaccount.class.php';
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
$remoteip = $_SERVER['REMOTE_ADDRESS'];
$generateduniquekey=getRandomPassword(true);
$partner=GETPOST('partner','alpha');
$plan=GETPOST('plan','alpha');

$reusecontactid = GETPOST('reusecontractid','int');


$productref=(GETPOST('productref','alpha')?GETPOST('productref','alpha'):'DOLICLOUD-PACK-Dolibarr');



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

// TODO Use package from a dedicated field
$packageref = $reg[1];

dol_include_once('/sellyoursaas/class/packages.class.php');
$tmppackage = new Packages($db);
$tmppackage->fetch(0, $packageref);
if (empty($tmppackage->id))
{
	print 'Package name '.$packageref.' was not found.';
	exit;
}

$now = dol_now();



/*
 * Actions
 */

//print "partner=".$partner." plan=".$plan." orgname = ".$orgname." email=".$email." password=".$password." password2=".$password2." country_code=".$country_code." remoteip=".$remoteip." sldAndSubdomain=".$sldAndSubdomain." tldid=".$tldid;

// Back to url
$newurl=$_SERVER["PHP_SELF"];
//exit;
//$newurl='myaccount.'.$conf->global->SELLYOURSAAS_MAIN_DOMAIN_NAME.'/register.php';

if ($reusecontactid)
{
	$newurl=preg_replace('/register_instance/', 'index', $newurl);
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

dol_syslog("Fetch contract (id = ".$reusecontactid.", domain name  = ".$fqdninstance.")");

$contract = new Contrat($db);
if ($reusecontactid)
{
	// Get contract
	$result = $contract->fetch($reusecontactid);
	if ($result < 0)
	{
		setEventMessages($langs->trans("NotFound"), null, 'errors');
		header("Location: ".$newurl);
		exit;
	}

	$contract->fetch_thirdparty();

	$tmpthirdparty = $contract->thirdparty;

	$generatedunixhostname = $contract->array_options['options_hostname_os'];
	$generatedunixlogin = $contract->array_options['options_username_os'];
	$generatedunixpassword = $contract->array_options['options_password_os'];
	$generateddbhostname = $contract->array_options['options_hostname_db'];
	$generateddbname = $contract->array_options['options_database_db'];
	$generateddbport = 3306;
	$generateddbusername = $contract->array_options['options_username_db'];
	$generateddbpassword = $contract->array_options['options_password_db'];

	$tmparray = explode('.', $contract->ref_customer, 2);
	$sldAndSubdomain = $tmparray[0];
	$tldid = $tmparray[1];
	$fqdninstance = $sldAndSubdomain.$tldid;
}
else
{
	// Create thirdparty (if it already exist, do nothing and return a warning to user)
	dol_syslog("Fetch thirdparty from email ".$email);
	$tmpthirdparty=new Societe($db);
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

	$domainname = preg_replace('/^\./', '', $tldid);

	$generateddbname = 'dbn'.substr(getRandomPassword(true), 0, 8);
	$generateddbusername = 'dbu'.substr(getRandomPassword(true), 0, 9);
	$generateddbpassword = substr(getRandomPassword(true), 0, 10);
	$generateddbhostname = $sldAndSubdomain.'.'.$domainname;
	$generatedunixhostname = $sldAndSubdomain.'.'.$domainname;


	// Create contract

	$db->begin();	// Start transaction

	$password_encoding = 'sha1md5';
	$password_crypted = dol_hash($password, 2);

	$tmpthirdparty->name = $orgname;
	$tmpthirdparty->email = $email;
	$tmpthirdparty->client = 3;
	$tmpthirdparty->tva_assuj = 1;
	$tmpthirdparty->array_options['options_dolicloud'] = 'yesv2';
	$tmpthirdparty->array_options['options_date_registration'] = dol_now();
	$tmpthirdparty->array_options['options_password'] = $password_crypted;
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
		//$contract->array_options['options_nb_users'] = 1;
		//$contract->array_options['options_nb_gb'] = 0.01;
		$contract->array_options['options_deployment_ip'] = $_SERVER["REMOTE_ADDR"];

		$result = $contract->create($user);
		if ($result <= 0)
		{
			dol_print_error_email('CREATECONTRACT', $contract->error, $contract->errors, 'alert alert-error');
			exit;
		}
	}

	if (! $error)
	{
	/*	$website = new Website($db);
		$website->fetch(0, 'sellyoursaas');
		//var_dump($website);

		// Create account to dashboard
		$websiteaccount = new WebsiteAccount($db);
		$websiteaccount->fk_website = $website->id;
		$websiteaccount->fk_soc = $tmpthirdparty->id;
		$websiteaccount->login = $email;
		$websiteaccount->pass_encoding = $password_encoding;
		$websiteaccount->pass_crypted = $password_crypted;
		$websiteaccount->note_private = 'Initial pass = '.$password;
		$websiteaccount->status = 1;
		$result = $websiteaccount->create($user);
		if ($result < 0)
		{
			// We ignore errors. This should not happen in real life.
			//setEventMessages($websiteaccount->error, $websiteaccount->errors, 'errors');
		}
	*/
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
		$vat = get_default_tva($mysoc, $object, $tmpproduct->id);
		$localtax1_tx = get_default_localtax($mysoc, $object, 1, 0);
		$localtax2_tx = get_default_localtax($mysoc, $object, 2, 0);
		//var_dump($mysoc->country_code);
		//var_dump($object->country_code);
		//var_dump($tmpproduct->tva_tx);
		//var_dump($vat);exit;

		$price = $tmpproduct->price;
		if ($dolicloudcustomer->id > 0)
		{
			$price = $dolicloudcustomer->price_instance;
			if (! preg_match('/yearly/', $dolicloudcustomer->plan)) $price = $price * 12;
		}

		if ($price == 0) $discount = 0;

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

	// Create contract line for USERS
	if (! $error)
	{
		$qty = 1;
		//if (! empty($contract->array_options['options_nb_users'])) $qty = $contract->array_options['options_nb_users'];
		$vat = get_default_tva($mysoc, $object, 0);
		$localtax1_tx = get_default_localtax($mysoc, $object, 1, 0);
		$localtax2_tx = get_default_localtax($mysoc, $object, 2, 0);

		$price = $tmpproduct->array_options['options_price_per_user'];
		if ($dolicloudcustomer->id > 0)
		{
			$price = $dolicloudcustomer->price_user;
			if (! preg_match('/yearly/', $dolicloudcustomer->plan)) $price = $price * 12;
		}
		/*var_dump($tmpproduct);
		var_dump('qty:'.$qty.'-price:'.$price);
		var_dump('instance:'.$dolicloudcustomer->price_instance);
		exit;*/

		if ($price > 0 && $qty > 0)
		{
			$contractlineid = $contract->addline('Users', $price, $qty, $vat, $localtax1_tx, $localtax2_tx, 0, $discount, $date_start, $date_end, 'HT', 0);
			if ($contractlineid < 0)
			{
				dol_print_error_email('CREATECONTRACTLINE2', $contract->error, $contract->errors, 'alert alert-error');
				exit;
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
}




// -----------------------------------------------------------------------------------------------------------------------
// Create unix user and directories and DNS
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
	'__APPDOMAIN__'=>$sldAndSubdomain.$tldid,
	'__DBHOSTNAME__'=>$generateddbhostname,
	'__DBNAME__'=>$generateddbname,
	'__DBPORT__'=>$generateddbport,
	'__DBUSER__'=>$generateddbusername,
	'__DBPASSWORD__'=>$generateddbpassword
	);

	$tmppackage->srcconffile1 = '/tmp/conf.php.'.$sldAndSubdomain.$tldid.'.tmp';
	$tmppackage->srccronfile = '/tmp/cron.'.$sldAndSubdomain.$tldid.'.tmp';

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

	// Remote action : deploy all
	$commandurl = $generatedunixlogin.'&'.$generatedunixpassword.'&'.$sldAndSubdomain.'&'.$domainname;
	$commandurl.= '&'.$generateddbname.'&'.$generateddbport.'&'.$generateddbusername.'&'.$generateddbpassword;
	$commandurl.= '&'.$tmppackage->srcconffile1.'&'.$tmppackage->targetconffile1.'&'.$tmppackage->datafile1;
	$commandurl.= '&'.$tmppackage->srcfile1.'&'.$tmppackage->targetsrcfile1.'&'.$tmppackage->srcfile2.'&'.$tmppackage->targetsrcfile2.'&'.$tmppackage->srcfile3.'&'.$tmppackage->targetsrcfile3;
	$commandurl.= '&'.$tmppackage->srccronfile.'&'.$targetdir;

	$outputfile = $conf->sellyoursaas->dir_temp.'/action_deploy_undeploy-deployall-'.dol_getmypid().'.out';

	$serverdeployement = getRemoveServerDeploymentIp();

	$urltoget='http://'.$serverdeployement.':8080/deployall?'.urlencode($commandurl);
	include DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	$retarray = getURLContent($urltoget);

	if ($retarray['curl_error_no'] != '')
	{
		$error++;
		$errormessages[] = $retarray['curl_error_msg'];
	}
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
		setEventMessages($dbinstance->error, $dbinstance->errors, 'errors');
		header("Location: ".$newurl);
		exit;

		//dol_print_error_email('GETDOLIDBI'.$generateddbhostname, $dbinstance->error, $dbinstance->errors, 'alert alert-error');
		//exit;
	}
	else
	{
		dol_syslog("Execute sql=".$sqltoexecute);
		$dbinstance->query($sqtoexecute);
	}
}

// End of deployment is no OK / Complete
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



// Go to dashboard with login session forced

if (! $error)
{
	$newurl=$_SERVER["PHP_SELF"];
	$newurl=preg_replace('/register_instance\.php/', 'index\.php?welcomecid='.$contract->id, $newurl);

	$_SESSION['initialapplogin']='admin';
	$_SESSION['initialapppassword']=$password;

	dol_syslog("Deployment successful");
	header("Location: ".$newurl);
	exit;
}



// If we are here, there was an error

$errormessages[] = 'Deployement of instance '.$sldAndSubdomain.$tldid.' started but failed.';
$errormessages[] = 'Our team was alerted. You will receive an email as soon as deployment is complete.';

dol_syslog("Error in deployment", LOG_ERR);
$email = new CMailFile('WARNING: Deployment error', 'supervision@dolicloud.com', $conf->global->MAIN_MAIL_EMAIL_FROM, join("\n",$errormessages)."\n\nParameters of command used:\n".$commandurl);
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
            $result = $tmpthirdparty->fetch(0, GETPOST('partner','alpha'));
            $logo = $tmpthirdparty->logo;
        }
        print '<img style="center" class="logoheader"  src="'.$linklogo.'" id="logo" />';
        ?>
      </div>
      <div class="block medium">

        <header class="inverse">
          <h1><?php echo $langs->trans("Registration") ?> <small><?php echo ($tmpproduct->label?'('.$tmpproduct->label.')':''); ?></small></h1>
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
