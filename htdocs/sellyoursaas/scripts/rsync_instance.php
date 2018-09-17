#!/usr/bin/php
<?php
/* Copyright (C) 2012 Laurent Destailleur	<eldy@users.sourceforge.net>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 *
 * Update an instance on stratus5 server with new ref version.
 */

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
	echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
	exit;
}

// Global variables
$version='1.0';
$error=0;

$dirroot=isset($argv[1])?$argv[1]:'';
$instance=isset($argv[2])?$argv[2]:'';
$mode=isset($argv[3])?$argv[3]:'';
$v=isset($argv[4])?$argv[4]:'';

// Include Dolibarr environment
@set_time_limit(0);							// No timeout for this script
define('EVEN_IF_ONLY_LOGIN_ALLOWED',1);		// Set this define to 0 if you want to lock your script when dolibarr setup is "locked to admin user only".

// Load Dolibarr environment
$res=0;
// Try master.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/master.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/master.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/master.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/master.inc.php");
// Try master.inc.php using relative path
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../master.inc.php")) $res=@include("../../../master.inc.php");
if (! $res) die("Include of master fails");
// After this $db, $mysoc, $langs, $conf and $hookmanager are defined (Opened $db handler to database will be closed at end of file).
// $user is created but empty.

dol_include_once("/sellyoursaas/core/lib/dolicloud.lib.php");
dol_include_once('/sellyoursaas/class/dolicloud_customers.class.php');
include_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');



/*
 *	Main
 */

if (empty($dirroot) || empty($instance) || empty($mode))
{
	print "Update an instance on remote server with new ref version.\n";
	print "Usage: $script_file source_root_dir sellyoursaas_instance (test|confirm|confirmunlock|diff|diffadd|diffchange|clean|confirmclean) (old)\n";
	print "Return code: 0 if success, <>0 if error\n";
	exit(-1);
}


// Use instance to detect if v1 or v2 or instance
if ($v == 'old')
{
	$v=1;
}
else
{
	$v=2;
	// Force $v according to hard coded values (keep v2 in default case)
	if (! empty($instance) && ! preg_match('/(\.on|\.with)\.dolicloud\.com$/',$instance) && ! preg_match('/\.home\.lan$/',$instance))
	{
		// TODO Manage several domains
		$instance=$instance.".".$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES;
	}
	if (! empty($instance) && preg_match('/\.on\.dolicloud\.com$/',$instance)) {
		$v=1;
	}
	if (! empty($instance) && preg_match('/\.with\.dolicloud\.com$/',$instance)) {
		$v=2;
	}
}

if ($v == 1)
{
	$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
	if ($db2->error)
	{
		dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
		exit;
	}

	$object = new Dolicloud_customers($db, $db2);
	$result=$object->fetch('',$instance);
}
else
{
	include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
	$object = new Contrat($db);
	$result=$object->fetch('', '', $instance);
}

if ($result <= 0)
{
	print "Error: instance ".$instance." for v".$v." not found.\n";
	exit(-2);
}

if ($v != 1)
{
	$object->instance = $object->ref_customer;
	$object->username_web = $object->array_options['options_username_os'];
	$object->password_web = $object->array_options['options_password_os'];
	$object->username_db = $object->array_options['options_username_db'];
	$object->password_db = $object->array_options['options_password_db'];
	$object->database_db = $object->array_options['options_database_db'];
}


if (empty($object->instance) || empty($object->username_web) || empty($object->password_web) || empty($object->database_db))
{
	print "Error: Some properties for instance ".$instance." was not registered into database.\n";
	exit(-3);
}
if (! is_dir($dirroot.'/htdocs'))
{
	print "Error: Source directory to synchronize must contains a htdocs directory.\n";
	exit(-4);
}

$dirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$object->database_db);
$login=$object->username_web;
$password=$object->password_web;
if ($v != 1)
{
	$targetdir=$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$login.'/'.$dirdb;
	$server=$object->array_options['options_hostname_os'];
}
else
{
	$targetdir=$conf->global->DOLICLOUD_EXT_HOME.'/'.$login.'/'.$dirdb;
	$server=$object->instance.'.on.dolicloud.com';
}

if (empty($login) || empty($dirdb))
{
	print "Error: properties for instance ".$instance." are not registered completely (missing at least login or database name).\n";
	exit(-5);
}

$sftpconnectstring=$object->username_web.'@'.$server.':'.$targetdir;

print 'Synchro of files '.$dirroot.' to '.$targetdir."\n";
print 'SFTP connect string : '.$sftpconnectstring."\n";
print 'SFTP password '.$object->password_web."\n";

$command="rsync";
$param=array();
if (! in_array($mode,array('confirm','confirmunlock','confirmclean'))) $param[]="-n";
//$param[]="-a";
if (! in_array($mode,array('diff','diffadd','diffchange'))) $param[]="-rlt";
else { $param[]="-rlD"; $param[]="--modify-window=1000000000"; $param[]="--delete -n"; }
$param[]="-v";
$param[]="--exclude .buildpath";
$param[]="--exclude .git";
$param[]="--exclude .gitignore";
$param[]="--exclude .settings";
$param[]="--exclude .project";
$param[]="--exclude build/exe/";
//$param[]="--exclude doc/";	// To keep files into htdocs/core/module/xxx/doc dir
$param[]="--exclude dev/";
$param[]="--exclude documents/";
$param[]="--include htdocs/modulebuilder/template/test/";
$param[]="--exclude test/";
$param[]="--exclude htdocs/conf/conf.php*";
$param[]="--exclude htdocs/custom";
$param[]="--exclude htdocs/customfields/";
$param[]="--exclude htdocs/bootstrap/";
$param[]="--exclude htdocs/agefodd/";
$param[]="--exclude htdocs/memcached/";
$param[]="--exclude htdocs/cabinetmed/";
if (! in_array($mode,array('diff','diffadd','diffchange'))) $param[]="--stats";
if (in_array($mode,array('clean','confirmclean'))) $param[]="--delete";
$param[]="-e 'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o PasswordAuthentication=no'";

$param[]=$dirroot.'/';
$param[]=$login.'@'.$server.":".$targetdir;

//var_dump($param);
$fullcommand=$command." ".join(" ",$param);
$output=array();
$return_var=0;
print $fullcommand."\n";
exec($fullcommand, $output, $return_var);

// Output result
foreach($output as $outputline)
{
	print $outputline."\n";
}

// Remove install.lock file if mode )) confirmunlock
if ($mode == 'confirmunlock')
{
	// SFTP connect
	if (! function_exists("ssh2_connect")) { dol_print_error('','ssh2_connect function does not exists'); exit(1); }

	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".preg_replace('/./', '*', $password), LOG_ERR);
			exit(-5);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			// Check if install.lock exists
			$dir=preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
			$fileinstalllock=$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';

			print 'Remove file '.$fileinstalllock."\n";

			ssh2_sftp_unlink($sftp, $fileinstalllock);
		}
	}
	else
	{
		print 'Failed to connect to ssh2 to '.$server;
		exit(-6);
	}
}

if ($mode != 'test')
{
	print "Create event into database\n";
	dol_syslog("Add event into database");

	$user = new User($db);
	$user->fetch('', 'ldestailleur');

	$actioncomm=new ActionComm($db);
	$actioncomm->datep=dol_now('tzserver');
	$actioncomm->percentage=100;
	$actioncomm->label='Upgrade instance='.$instance.' dirroot='.$dirroot.' mode='.$mode;
	$actioncomm->fk_element=$object->id;
	$actioncomm->elementtype='dolicloudcustomers';
	$actioncomm->type_code='AC_OTH_AUTO';
	$actioncomm->userassigned[$user->id]=array('id'=>$user->id);
	$actioncomm->userownerid=$user->id;
	$actioncomm->create($user);
}

exit($return_var);
