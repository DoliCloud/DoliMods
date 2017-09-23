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
 * Make a backup of files (rsync) or database (mysqdump) of instance. There is no
 * report/tracking done into any database. This must be done by a parent script.
 *
 * ssh keys must be authorized to have testrsync and confirmrsync working
 * no requirement for testdatabase or confirmdatabase
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
$error=0;

$instance=isset($argv[1])?$argv[1]:'';
$dirroot=isset($argv[2])?$argv[2]:'';
$mode=isset($argv[3])?$argv[3]:'';

@set_time_limit(0);							// No timeout for this script
define('EVEN_IF_ONLY_LOGIN_ALLOWED',1);		// Set this define to 0 if you want to lock your script when dolibarr setup is "locked to admin user only".

// Include and load Dolibarr environment variables
$res=0;
if (! $res && file_exists($path."master.inc.php")) $res=@include($path."master.inc.php");
if (! $res && file_exists($path."../master.inc.php")) $res=@include($path."../master.inc.php");
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../../master.inc.php")) $res=@include($path."../../../master.inc.php");
if (! $res) die("Include of master fails");

dol_include_once("/sellyoursaas/core/lib/dolicloud.lib.php");
dol_include_once('/sellyoursaas/class/dolicloud_customer.class.php');


$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
	exit;
}


$object = new Dolicloud_customer($db, $db2);



/*
 *	Main
 */

if (empty($dirroot) || empty($instance) || empty($mode))
{
	print "Usage:   $script_file instance    backup_dir  (testrsync|testdatabase|confirmrsync|confirmdatabase|confirm)\n";
	print "Example: $script_file myinstance  ".$conf->global->DOLICLOUD_BACKUP_PATH."  testrsync\n";
	print "Note:    ssh keys must be authorized to have testrsync and confirmrsync working\n";
	print "Return code: 0 if success, <>0 if error\n";
	exit(-1);
}


$result=$object->fetch('',$instance);
if ($result < 0)
{
	print "Error: instance ".$instance." not found.\n";
	exit(-2);
}
if (empty($object->instance) && empty($object->username_web) && empty($object->password_web) && empty($object->database_db))
{
	print "Error: properties for instance ".$instance." was not registered into database.\n";
	exit(-3);
}
if (! is_dir($dirroot))
{
	print "Error: Target directory ".$dirroot." to store backup does not exist.\n";
	exit(-4);
}

$dirdb=preg_replace('/_([a-zA-Z0-9]+)/','',$object->database_db);
$login=$object->username_web;
$password=$object->password_web;
$sourcedir=$conf->global->DOLICLOUD_EXT_HOME.'/'.$login.'/'.$dirdb;
$server=$object->instance.'.on.dolicloud.com';

if (empty($login) || empty($dirdb))
{
	print "Error: properties for instance ".$instance." are not registered completely (missing at least login or database name).\n";
	exit(-5);
}

print 'Backup instance '.$instance.' to '.$dirroot.'/'.$login."\n";
print 'SFTP password '.$object->password_web."\n";
print 'Database password '.$object->password_db."\n";

//$listofdir=array($dirroot.'/'.$login, $dirroot.'/'.$login.'/documents', $dirroot.'/'.$login.'/system', $dirroot.'/'.$login.'/htdocs', $dirroot.'/'.$login.'/scripts');
if ($mode == 'confirm' || $mode == 'confirmrsync' || $mode == 'confirmdatabase')
{
	$listofdir=array();
	$listofdir[]=$dirroot.'/'.$login;
	if ($mode == 'confirm' || $mode == 'confirmdatabase')
	{
		$listofdir[]=$dirroot.'/'.$login.'/documents';
		$listofdir[]=$dirroot.'/'.$login.'/documents/admin';
		$listofdir[]=$dirroot.'/'.$login.'/documents/admin/backup';
	}
	foreach($listofdir as $dirtocreate)
	{
		if (! is_dir($dirtocreate))
		{
			$res=@mkdir($dirtocreate);
			if (! $res)
			{
				print 'Failed to create dir '.$dirtocreate."\n";
				exit(-6);
			}
		}
	}
}

// Backup files
if ($mode == 'testrsync' || $mode == 'confirmrsync' || $mode == 'confirm')
{
	$command="rsync";
	$param=array();
	if ($mode != 'confirm' && $mode != 'confirmrsync') $param[]="-n";
	//$param[]="-a";
	$param[]="-rltz";
	$param[]="-vv";
	$param[]="--exclude .buildpath";
	$param[]="--exclude .git";
	$param[]="--exclude .gitignore";
	$param[]="--exclude .settings";
	$param[]="--exclude .project";
	$param[]="--exclude '*.com*SSL'";
	$param[]="--exclude '*.log'";
	//$param[]="--exclude '*.old'";
	$param[]="--exclude '*/build/'";
	$param[]="--exclude '*/doc/images/'";	// To keep files into htdocs/core/module/xxx/doc/ dir
	$param[]="--exclude '*/doc/install/'";	// To keep files into htdocs/core/module/xxx/doc/ dir
	$param[]="--exclude '*/doc/user/'";		// To keep files into htdocs/core/module/xxx/doc/ dir
	$param[]="--exclude '*/dev/'";
	$param[]="--exclude '*/test/'";
	$param[]="--exclude '*/temp/'";
	$param[]="--exclude '*/documents/admin/backup/'";
	$param[]="--backup --suffix=.old --delete --delete-excluded";
	$param[]="--stats";
	$param[]="-e 'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no'";



	//var_dump($param);
	//print "- Backup documents dir ".$dirroot."/".$instance."\n";
	$param[]=$login.'@'.$server.":".$sourcedir;
	$param[]=$dirroot.'/'.$login;
	$fullcommand=$command." ".join(" ",$param);
	$output=array();
	$return_var=0;
	print strftime("%Y%m%d-%H%M%S").' '.$fullcommand."\n";
	exec($fullcommand, $output, $return_var);
	print strftime("%Y%m%d-%H%M%S").' rsync done'."\n";

	// Output result
	foreach($output as $outputline)
	{
		print $outputline."\n";
	}

	// Add file tag
	$handle=fopen($dirroot.'/'.$login.'/last_rsync_'.$instance.'.txt','w');
	fwrite($handle,'File created after rsync of '.$instance."\n");
	fclose($handle);
}

// Backup database
if ($mode == 'testdatabase' || $mode == 'confirmdatabase' || $mode == 'confirm')
{
	$command="mysqldump";
	$param=array();
	$param[]=$object->database_db;
	$param[]="-h";
	$param[]=$server;
	$param[]="-u";
	$param[]=$object->username_db;
	$param[]='-p"'.str_replace(array('"','`'),array('\"','\`'),$object->password_db).'"';
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
	if ($mode == 'testdatabase') $fullcommand.=" | bzip2 > /dev/null";
	else $fullcommand.=" | bzip2 > ".$dirroot.'/'.$login.'/documents/admin/backup/mysqldump_'.$object->database_db.'_'.gmstrftime('%d').'.sql.bz2';
	$output=array();
	$return_varmysql=0;
	print strftime("%Y%m%d-%H%M%S").' '.$fullcommand."\n";
	exec($fullcommand, $output, $return_varmysql);
	print strftime("%Y%m%d-%H%M%S").' mysqldump done'."\n";

	// Output result
	foreach($output as $outputline)
	{
		print $outputline."\n";
	}

	// Add file tag
	if ($mode == 'confirm' || $mode == 'confirmdatabase')
	{
		$handle=fopen($dirroot.'/'.$login.'/last_mysqldump_'.$instance.'.txt','w');
		fwrite($handle,'File created after mysqldump of '.$instance."\n");
		fclose($handle);
	}
}


// Update database
if (empty($return_var) && empty($return_varmysql))
{
	if ($mode == 'confirm')
	{
		$now=dol_now();
		print strftime("%Y%m%d-%H%M%S").' Update date of full backup (rsync+dump) for instance '.$object->instance.' to '.$now."\n";
		$object->date_lastrsync=$now;
		$object->update(0);
	}
}
else
{
	if (! empty($return_var)) print "ERROR into backup process of rsync: ".$return_var."\n";
	if (! empty($return_varmysql)) print "ERROR into backup process of mysqldump: ".$return_varmysql."\n";
	exit(1);
}

exit(0);
