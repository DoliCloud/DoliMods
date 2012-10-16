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

$dirroot=isset($argv[2])?$argv[2]:'';
$instance=isset($argv[1])?$argv[1]:'';

// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include($path."../../htdocs/master.inc.php");
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../master.inc.php")) $res=@include("../../../master.inc.php");
if (! $res) die ("Failed to include master.inc.php file\n");
dol_include_once("/nltechno/core/lib/dolicloud.lib.php");
dol_include_once('/nltechno/class/dolicloudcustomer.class.php');

$object = new DoliCloudCustomer($db);



/*
 *	Main
 */

if (! isset($argv[2]) || ! $argv[2])
{
	print "Usage: $script_file dolicloud_instance backup_dir\n";
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
if (! is_dir($dirroot))
{
	print "Error: Target directory ".$dirroot." to store backup does not exist.\n";
	exit;
}

$dirdb=preg_replace('/_dolibarr/','',$object->database_db);
$login=$object->username_web;
$password=$object->password_web;
$sourcedir='/home/'.$login.'/'.$dirdb;
$server=$object->instance.'.on.dolicloud.com';

print 'Backup instance '.$instance.' to '.$dirroot."/".$instance."\n";


print "- Backup documents dir ".$dirroot."/".$instance."/documents\n";
$command="rsync";
$output=array();
if (empty($confirm)) $output[]="-n";
//$output[]="-a";
$output[]="-rlt";
$output[]="-v";
$output[]="--exclude .buildpath";
$output[]="--exclude .git";
$output[]="--exclude .gitignore";
$output[]="--exclude .settings";
$output[]="--exclude .project";
$output[]="--exclude build/";
$output[]="--exclude doc/";
$output[]="--exclude dev/";
$output[]="--exclude documents/";
$output[]="--exclude test/";
$output[]="--exclude htdocs/conf/conf.php*";
$output[]="--exclude htdocs/custom";
$output[]="--exclude htdocs/customfields/";
$output[]="--exclude htdocs/bootstrap/";
$output[]="--stats";
$output[]="-e ssh";
$output[]=$login.'@'.$server.":".$sourcedir."/documents";
$output[]=$dirroot."/".$instance."/documents";
$return_var=0;

//var_dump($output);
print $command." ".join(" ",$output)."\n";
//exec($command, &$output, &$return_var);


print "- Backup programs dir ".$dirroot."/".$instance."/htdocs\n";



print "- Backup programs dir ".$dirroot."/".$instance."/scripts\n";


print "- Make mysqldump ".$dirroot."/".$instance."/documents/admin/backup\n";




?>