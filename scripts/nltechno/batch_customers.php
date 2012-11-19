#!/usr/bin/php
<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 */

/**
 *      \file       dev/skeletons/dolicloudcustomers_script.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a command line script
 *					Initialy built by build_class_from_table on 2012-06-26 21:03
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
$version='1.29';
$error=0;


// -------------------- START OF YOUR CODE HERE --------------------
// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include($path."../../htdocs/master.inc.php");
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../master.inc.php")) $res=@include("../../../master.inc.php");
if (! $res && file_exists($dirroot."/htdocs/master.inc.php")) $res=@include($dirroot."/htdocs/master.inc.php");
if (! $res) die ("Failed to include master.inc.php file\n");
// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs->load("main");				// To load language file for default language
@set_time_limit(0);					// No timeout for this script

// Load user and its permissions
//$result=$user->fetch('','admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
//if (! $result > 0) { dol_print_error('',$user->error); exit; }
//$user->getrights();


print "***** ".$script_file." (".$version.") *****\n";
if (! isset($argv[1])) {	// Check parameters
    print "Usage: ".$script_file." [backup|backuptest] param2 ...\n";
    exit;
}
print '--- start'."\n";
//print 'Argument 1='.$argv[1]."\n";
//print 'Argument 2='.$argv[2]."\n";



/*
 * Main
 */

$action=$argv[1];
$nbofok=0;
$nboferrors=0;

// Start of transaction
$db->begin();


// Examples for manipulating class skeleton_class
dol_include_once('/nltechno/class/dolicloudcustomer.class.php');
$object=new Dolicloudcustomer($db);


if ($action == 'backup' || $action == 'backuptest')
{
	$instances=array();

	if (empty($conf->global->DOLICLOUD_INSTANCES_PATH))
	{
		print "Error: Setup of module NLTechno not complete\n";
		exit -1;
	}

	$sql = 'SELECT c.rowid, c.instance, c.status, c.lastrsync';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'dolicloud_customers as c';
	$sql.= ' WHERE status = \'ACTIVE\'';

	dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if ($obj)
				{
					$instances[]=$obj->instance;
					print "Found instance ".$obj->instance."\n";
				}
				$i++;
			}
		}
	}
	else
	{
		$error++;
		$nboferrors++;
		dol_print_error($db);
	}


	// Loop on each instance
	if (! $error)
	{
		foreach($instances as $instance)
		{
			$now=dol_now();

			// Run backup
			print "Process backup of instance ".$instance."\n";

			$command=($path?$path.'/':'')."backup_instance.php ".escapeshellarg($instance)." ".escapeshellarg($conf->global->DOLICLOUD_INSTANCES_PATH)." ".($action == 'backup'?'test':'confirm');

			//$output = shell_exec($command);
			if ($action == 'backup')
			{
				ob_start();
				passthru($command, $return_val);
				$content_grabbed=ob_get_contents();
				ob_end_clean();
			}

			echo "Result: ".$return_val."\n";
			echo "Output: ".$content_grabbed."\n";

			if ($return_val != 0) $error++;

			// Update database
			if (! $error)
			{
				$db->begin();

				$result=$object->fetch('',$instance);

				if ($action == 'backup')
				{
					$object->date_lastrsync=$now;
					$object->update();
				}

				$db->commit();
			}

			//
			if (! $error)
			{
				$nbofok++;
				print 'Process success'."\n";
			}
			else
			{
				$nboferrors++;
				print 'Process fails'."\n";
			}
		}
	}
}


// Result
print "Nb of instances ok: ".$nbofok."\n";
print "Nb of instances ko: ".$nboferrors."\n";
if (! $nboferrors)
{
	print '--- end ok'."\n";
}
else
{
	print '--- end error code='.$nboferrors."\n";
}

$db->close();	// Close database opened handler

return $nboferrors;
?>
