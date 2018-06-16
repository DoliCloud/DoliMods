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

$oldinstance=isset($argv[1])?$argv[1]:'';
$newinstance=isset($argv[2])?$argv[2]:'';
$mode=isset($argv[3])?$argv[3]:'';

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
include_once(DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php');


$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
	exit;
}


$objectv1 = new Dolicloud_customers($db,$db2);
$objectv2 = new Contrat($db);



/*
 *	Main
 */

print "***** ".$script_file." *****\n";

if (empty($oldinstance) || empty($newinstance) || empty($mode))
{
	print "Migrate an old instance on new server. Script must be ran with root.\n";
	print "Usage: $script_file oldinstance newinstance (test|confirm)\n";
	print "Return code: 0 if success, <>0 if error\n";
	exit(-1);
}

if (0 != posix_getuid()) {
	echo "Script must be ran with root.\n";
	exit(-1);
}

if (! empty($oldinstance) && ! preg_match('/\.on\.dolicloud\.com$/',$oldinstance) && ! preg_match('/\.home\.lan$/',$oldinstance))
{
	$oldinstance=$oldinstance.".on.dolicloud.com";
}
if (! empty($newinstance) && ! preg_match('/\.with\.dolicloud\.com$/',$newinstance) && ! preg_match('/\.home\.lan$/',$newinstance))
{
	// TODO Manage serveral domains
	$newinstance=$newinstance.".".$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES;
}

$oldobject = new Dolicloud_customers($db, $db2);
$result=$oldobject->fetch('',$oldinstance);
if ($result <= 0)
{
	print "Error: old instance ".$oldinstance." not found.\n";
	exit(-2);
}

include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
$newobject = new Contrat($db);
$result=$newobject->fetch('', '', $newinstance);
if ($result <= 0)
{
	print "Error: newinstance ".$newinstance." not found.\n";
	exit(-2);
}
$newobject->instance = $newinstance;
$newobject->username_web = $newobject->array_options['options_username_os'];
$newobject->password_web = $newobject->array_options['options_password_os'];
$newobject->hostname_web = $newobject->array_options['options_hostname_os'];
$newobject->username_db  = $newobject->array_options['options_username_db'];
$newobject->password_db  = $newobject->array_options['options_password_db'];
$newobject->database_db  = $newobject->array_options['options_database_db'];


if (empty($oldobject->instance) || empty($oldobject->username_web) || empty($oldobject->password_web) || empty($oldobject->database_db))
{
	print "Error: Some properties for instance ".$oldinstance." was not registered into database.\n";
	exit(-3);
}
if (empty($newobject->instance) || empty($newobject->username_web) || empty($newobject->password_web) || empty($newobject->database_db))
{
	print "Error: Some properties for instance ".$newinstance." was not registered into database.\n";
	exit(-3);
}

$olddirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$oldobject->database_db);
$oldlogin=$oldobject->username_web;
$oldpassword=$oldobject->password_web;
$oldloginbase=$oldobject->username_db;
$oldpasswordbase=$oldobject->password_db;
$newdirdb=$newobject->database_db;
$newlogin=$newobject->username_web;
$newpassword=$newobject->password_web;
$newloginbase=$newobject->username_db;
$newpasswordbase=$newobject->password_db;

$sourcedir=$conf->global->DOLICLOUD_EXT_HOME.'/'.$oldlogin.'/'.$olddirdb;
$targetdir=$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$newlogin.'/'.$newdirdb;
$oldserver=$oldobject->hostname_web;
$newserver=$newobject->array_options['options_hostname_os'];

if (empty($oldlogin) || empty($olddirdb))
{
	print "Error: properties for instance ".$oldinstance." are not registered completely (missing at least login or database name).\n";
	exit(-5);
}

$oldsftpconnectstring=$oldobject->username_web.'@'.$oldobject->hostname_web.':'.$conf->global->DOLICLOUD_EXT_HOME.'/'.$oldlogin.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$olddirdb);
$newsftpconnectstring=$newobject->username_web.'@'.$newobject->hostname_web.':'.$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$newlogin.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$newdirdb);

print '--- Synchro of files '.$sourcedir.' to '.$targetdir."\n";
print 'SFTP connect string : '.$oldsftpconnectstring."\n";
print 'SFTP connect string : '.$newsftpconnectstring."\n";
print 'SFTP old password '.$oldobject->password_web."\n";
//print 'SFTP new password '.$newobject->password_web."\n";

$command="rsync";
$param=array();
if (! in_array($mode,array('confirm'))) $param[]="-n";
//$param[]="-a";
if (! in_array($mode,array('diff','diffadd','diffchange'))) $param[]="-rlt";
else { $param[]="-rlD"; $param[]="--modify-window=1000000000"; $param[]="--delete -n"; }
$param[]="-v";
$param[]="--exclude .buildpath";
$param[]="--exclude .git";
$param[]="--exclude .gitignore";
$param[]="--exclude .settings";
$param[]="--exclude .project";
$param[]="--exclude htdocs/conf/conf.php";
if (! in_array($mode,array('diff','diffadd','diffchange'))) $param[]="--stats";
if (in_array($mode,array('clean','confirmclean'))) $param[]="--delete";
$param[]="-e 'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no'";

$param[]=$oldlogin.'@'.$oldserver.":".$sourcedir.'/*';
//$param[]=$newlogin.'@'.$newserver.":".$targetdir;
$param[]=$targetdir;

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

	$newserver=$newobject->instance.'.with.dolicloud.com';
	$connection = ssh2_connect($newserver, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $newobject->username_web, $newobject->password_web))
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

print "-> Files were sync into dir of instance ".$newobject->ref_customer.": ".$targetdir."\n";
print "\n";



print "--- Set permissions with chmod -R ".$newlogin.".".$newlogin." ".$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$newlogin.'/'.$newdirdb."\n";
$output=array();
$return_varchmod=0;
if ($mode == 'confirm')
{
	if (empty($conf->global->DOLICLOUD_INSTANCES_PATH) || empty($newlogin) || empty($newdirdb))
	{
		print 'Bad value for data. We stop to avoid drama';
		exit(-7);
	}
	exec("chown -R ".$newlogin.".".$newlogin." ".$conf->global->DOLICLOUD_INSTANCES_PATH.'/'.$newlogin.'/'.$newdirdb, $output, $return_varchmod);
}

// Output result
foreach($output as $outputline)
{
	print $outputline."\n";
}

print "\n";

print "-> Files were sync into dir of instance ".$newobject->ref_customer.": ".$targetdir."\n";


print '--- Dump database '.$oldobject->database_db.' into /tmp/mysqldump_'.$oldobject->database_db.'_'.gmstrftime('%d').".sql\n";

$command="mysqldump";
$param=array();
$param[]=$oldobject->database_db;
$param[]="-h";
$param[]=$oldserver;
$param[]="-u";
$param[]=$oldobject->username_db;
$param[]='-p"'.str_replace(array('"','`'),array('\"','\`'),$oldobject->password_db).'"';
$param[]="--compress";
$param[]="-l";
$param[]="--single-transaction";
$param[]="-K";
$param[]="--tables";
$param[]="-c";
$param[]="-e";
$param[]="--hex-blob";
$param[]="--default-character-set=utf8";

$fullcommand=$command." ".join(" ",$param);
$fullcommand.=' > /tmp/mysqldump_'.$oldobject->database_db.'_'.gmstrftime('%d').'.sql';
$output=array();
$return_varmysql=0;
print strftime("%Y%m%d-%H%M%S").' '.$fullcommand."\n";
exec($fullcommand, $output, $return_varmysql);
print strftime("%Y%m%d-%H%M%S").' mysqldump done (return='.$return_varmysql.')'."\n";

// Output result
foreach($output as $outputline)
{
	print $outputline."\n";
}



print '--- Load database '.$newobject->database_db.' from /tmp/mysqldump_'.$oldobject->database_db.'_'.gmstrftime('%d').".sql\n";
//print "If the load fails, try to run mysql -u".$newloginbase." -p".$newpasswordbase." -D ".$newobject->database_db."\n";

$fullcommanda='echo "drop table llx_accounting_account;" | mysql -u'.$newloginbase.' -p'.$newpasswordbase.' -D '.$newobject->database_db;
$output=array();
$return_var=0;
print strftime("%Y%m%d-%H%M%S").' Drop table to prevent load error with '.$fullcommanda."\n";
if ($mode == 'confirm' || $mode == 'confirmrm')
{
	exec($fullcommanda, $output, $return_var);
	foreach($output as $line) print $line."\n";
}

$fullcommandb='echo "drop table llx_accounting_system;" | mysql -u'.$newloginbase.' -p'.$newpasswordbase.' -D '.$newobject->database_db;
$output=array();
$return_var=0;
print strftime("%Y%m%d-%H%M%S").' Drop table to prevent load error with '.$fullcommandb."\n";
if ($mode == 'confirm' || $mode == 'confirmrm')
{
	exec($fullcommandb, $output, $return_var);
	foreach($output as $line) print $line."\n";
}

$fullcommand="cat /tmp/mysqldump_".$oldobject->database_db.'_'.gmstrftime('%d').".sql | mysql -u".$newloginbase." -p".$newpasswordbase." -D ".$newobject->database_db;
print strftime("%Y%m%d-%H%M%S")." Load dump with ".$fullcommand."\n";
if ($mode == 'confirm' || $mode == 'confirmrm')
{
	$output=array();
	$return_var=0;
	print strftime("%Y%m%d-%H%M%S").' '.$fullcommand."\n";
	exec($fullcommand, $output, $return_var);
	foreach($output as $line) print $line."\n";
}

$fullcommandc='echo "UPDATE llx_const set value = \''.$newlogin.'\' WHERE name = \'CRON_KEY\';" | mysql -u'.$newloginbase.' -p'.$newpasswordbase.' -D '.$newobject->database_db;
$output=array();
$return_var=0;
print strftime("%Y%m%d-%H%M%S").' Update cron key '.$fullcommandc."\n";
if ($mode == 'confirm' || $mode == 'confirmrm')
{
	exec($fullcommandc, $output, $return_var);
	foreach($output as $line) print $line."\n";
}



print "\n";

if ($mode == 'confirm')
{
	print '-> Dump loaded into database '.$newobject->database_db.'. You can test instance on URL https://'.$newobject->ref_customer."\n";
	print "Finished.\n";
}
else
{
	print '-> Dump NOT loaded (test mode) into database '.$newobject->database_db.'. You can test instance on URL https://'.$newobject->ref_customer."\n";
	print "Finished. DON'T FORGET TO DISABLE INVOICING ON OLD SYSTEM !!!\n";
}

exit($return_var + $return_varmysql);
