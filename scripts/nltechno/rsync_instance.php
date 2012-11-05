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
$version='$Revision: 1.4 $';
$error=0;

$dirroot=isset($argv[1])?$argv[1]:'';
$instance=isset($argv[2])?$argv[2]:'';
$mode=isset($argv[3])?$argv[3]:'';

// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include($path."../../htdocs/master.inc.php");
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../master.inc.php")) $res=@include("../../../master.inc.php");
if (! $res && file_exists($dirroot."/htdocs/master.inc.php")) $res=@include($dirroot."/htdocs/master.inc.php");
if (! $res) die ("Failed to include master.inc.php file\n");
dol_include_once("/nltechno/core/lib/dolicloud.lib.php");
dol_include_once('/nltechno/class/dolicloudcustomer.class.php');

$object = new DoliCloudCustomer($db);



/*
 *	Main
 */

if (empty($dirroot) || empty($instance) || empty($mode))
{
	print "Usage: $script_file dolibarr_root_dir dolicloud_instance (test|confirm)\n";
	exit;
}



$result=$object->fetch('',$instance);
if ($result < 0)
{
	print "Error: instance ".$instance." not found.\n";
	exit;
}
if (empty($object->instance) && empty($object->username_web) && empty($object->password_web) && empty($object->database_db))
{
	print "Error: properties for instance ".$instance." was not registered.\n";
	exit;
}
if (! is_dir($dirroot.'/htdocs'))
{
	print "Error: Source directory to synchronize must contains a htdocs directory.\n";
	exit;
}

$dirdb=preg_replace('/_dolibarr/','',$object->database_db);
$login=$object->username_web;
$password=$object->password_web;
$targetdir='/home/'.$login.'/'.$dirdb;
$server=$object->instance.'.on.dolicloud.com';

print 'Synchro of files '.$dirroot.' to '.$targetdir."\n";

$sftpconnectstring=$object->username_web.'@'.$object->hostname_web.':/home/'.$object->username_web.'/'.preg_replace('/_dolibarr$/','',$object->database_db);
print 'SFTP connect string : '.$sftpconnectstring."\n";
print 'SFTP password '.$object->password_web."\n";

$command="rsync";
$param=array();
if ($mode != 'confirm') $param[]="-n";
//$param[]="-a";
$param[]="-rlt";
$param[]="-v";
$param[]="--exclude .buildpath";
$param[]="--exclude .git";
$param[]="--exclude .gitignore";
$param[]="--exclude .settings";
$param[]="--exclude .project";
$param[]="--exclude build/";
//$param[]="--exclude doc/";	// To keep files into htdocs/core/module/xxx/doc dir
$param[]="--exclude dev/";
$param[]="--exclude documents/";
$param[]="--exclude test/";
$param[]="--exclude htdocs/conf/conf.php*";
$param[]="--exclude htdocs/custom";
$param[]="--exclude htdocs/customfields/";
$param[]="--exclude htdocs/bootstrap/";
$param[]="--stats";
$param[]="-e ssh";
$param[]=$dirroot.'/';
$param[]=$login.'@'.$server.":".$targetdir;

//var_dump($param);
$fullcommand=$command." ".join(" ",$param);
$output=array();
$return_var=0;
print $fullcommand."\n";
exec($fullcommand, &$output, &$return_var);

// Output result
foreach($output as $outputline)
{
	print $outputline."\n";
}

// SFTP connect
/*
if (function_exists("ssh2_connect"))
{
	$server=$object->instance.'.on.dolicloud.com';
	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".$password,LOG_ERR);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			$dir=preg_replace('/_dolibarr$/','',$object->database_db);
			$file="ssh2.sftp://".$sftp."/home/".$object->username_web.'/'.$dir.'/htdocs/conf/conf.php';

			//print $file;
			$stream = fopen($file, 'r');
			$fstat=fstat($stream);
			fclose($stream);
			//var_dump($fstat);

			if (empty($object->date_registration) || empty($object->date_endfreeperiod))
			{
				// Overwrite only if not defined
				$object->date_registration=$fstat['mtime'];
				//$object->date_endfreeperiod=dol_time_plus_duree($object->date_registration,1,'m');
				$object->date_endfreeperiod=dol_time_plus_duree($object->date_registration,15,'d');
			}
			$object->gid=$fstat['gid'];
			$uid=$fstat['uid'];
			$size=$fstat['size'];
		}
	}
	else {
		$errors[]='Failed to connect to ssh2 to '.$server;
	}
}
else {
	$errors[]='ssh2_connect not supported by this PHP';
}
*/

return $return_var;
?>