#!/usr/bin/php
<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 * or see http://www.gnu.org/*
 */

/**
 *      \file       sellyoursaas/scripts/batch_customers.php
 *		\ingroup    sellyoursaas
 *      \brief      Main master Dolicloud batch
 *      			backup_instance.php (payed customers rsync + databases backup)
 *      			update database info for customer
 *      			update stats
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


// -------------------- START OF YOUR CODE HERE --------------------
@set_time_limit(0);							// No timeout for this script
define('EVEN_IF_ONLY_LOGIN_ALLOWED',1);		// Set this define to 0 if you want to lock your script when dolibarr setup is "locked to admin user only".

// Include and load Dolibarr environment variables
$res=0;
if (! $res && file_exists($path."master.inc.php")) $res=@include($path."master.inc.php");
if (! $res && file_exists($path."../master.inc.php")) $res=@include($path."../master.inc.php");
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../../master.inc.php")) $res=@include($path."../../../master.inc.php");
if (! $res) die("Include of master fails");
// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

dol_include_once('/sellyoursaas/class/dolicloud_customers.class.php');
include_once dol_buildpath("/sellyoursaas/backoffice/lib/refresh.lib.php");		// do not use dol_buildpth to keep global declaration working


$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
	exit;
}


//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs->load("main");				// To load language file for default language
@set_time_limit(0);					// No timeout for this script

// Load user and its permissions
//$result=$user->fetch('','admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
//if (! $result > 0) { dol_print_error('',$user->error); exit; }
//$user->getrights();


print "***** ".$script_file." (".$version.") - ".strftime("%Y%m%d-%H%M%S")." *****\n";
if (! isset($argv[1])) {	// Check parameters
    print "Usage: ".$script_file." (backuptestrsync|backuptestdatabase|backup|updatedatabase|updatestatsonly) [instancefilter]\n";
    print "\n";
    print "- backuptestrsync  	  test rsync backup\n";
    print "- backuptestdatabase  test mysqldump backup\n";
    print "- backup           creates backup (rsync + mysqldump)\n";
    print "- updatedatabase   (=updatecountsonly+updatestatsonly) updates list and nb of users, modules and version and stats\n";
    print "- updatecountsonly updates counters of instances only (only nb of user for instances)\n";
    print "- updatestatsonly  updates stats only (only table dolicloud_stats)\n";
    exit;
}
print '--- start'."\n";
//print 'Argument 1='.$argv[1]."\n";
//print 'Argument 2='.$argv[2]."\n";



/*
 * Main
 */

$action=$argv[1];
$nbofko=0;
$nbofok=0;
$nbofactive=0;
$nbofactivesusp=0;
$nbofactiveclosurerequest=0;
$nbofactivepaymentko=0;
$nbofalltime=0;
$nboferrors=0;
$instancefilter=(isset($argv[2])?$argv[2]:'');
$instancefiltercomplete=$instancefilter;
// Add on.dolicloud.com to have a complete instance id
if (! empty($instancefiltercomplete) && ! preg_match('/\.on\.dolicloud\.com$/',$instancefiltercomplete)) $instancefiltercomplete=$instancefiltercomplete.'.on.dolicloud.com';


$object=new Dolicloud_customers($db,$db2);


$instances=array();
$instancesbackuperror=array();
$instancesupdateerror=array();

// Get list of instance
//$sql = "SELECT c.rowid, c.instance, c.status, c.lastrsync";
//$sql.= " FROM ".MAIN_DB_PREFIX."dolicloud_customers as c";
$sql = "SELECT i.id, i.name as instance, i.status as instance_status,";
$sql.= " c.status as status,";
$sql.= " s.payment_status,";
$sql.= " s.status as subscription_status";
$sql.= " FROM app_instance as i, subscription as s, customer as c";
$sql.= " WHERE i.customer_id = c.id AND c.id = s.customer_id";
if ($instancefiltercomplete) $sql.= " AND i.name = '".$instancefiltercomplete."'";

dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
$resql=$db2->query($sql);
if ($resql)
{
	$num = $db2->num_rows($resql);
	$i = 0;
	if ($num)
	{
		while ($i < $num)
		{
			$obj = $db2->fetch_object($resql);
			if ($obj)
			{
				//print "status=".$obj->status." instance_status=".$obj->instance_status." payment_status=".$obj->payment_status."\n";
				// Count
				if (! in_array($obj->payment_status,array('TRIAL','TRIALING','TRIAL_EXPIRED')))
				{
					$nbofalltime++;
					if (! in_array($obj->status,array('CLOSED')) && ! in_array($obj->instance_status,array('UNDEPLOYED')))		// Nb of active
					{
						$nbofactive++;
						if (in_array($obj->status,array('SUSPENDED'))) $nbofactivesusp++;
						else if (in_array($obj->status,array('CLOSE_QUEUED','CLOSURE_REQUESTED')) ) $nbofactiveclosurerequest++;
						else if (in_array($obj->payment_status,array('FAILURE','PAST_DUE'))) $nbofactivepaymentko++;
						else $nbofactiveok++; // not suspended, not close request

						$instance=preg_replace('/\.on\.dolicloud\.com$/', '', $obj->instance);
						$instances[]=$instance;
						print "Found instance ".$obj->instance." with status=".$obj->status." instance_status=".$obj->instance_status." payment_status=".$obj->payment_status." subscription_status(not used)=".$obj->subscription_status."\n";
					}
				}
			}
			$i++;
		}
	}
}
else
{
	$error++;
	$nboferrors++;
	dol_print_error($db2);
}
print "Found ".count($instances)." actives instances.\n";


//print "----- Start loop for backup_instance\n";
if ($action == 'backup' || $action == 'backuptestrsync' || $action == 'backuptestdatabase')
{
	if (empty($conf->global->DOLICLOUD_BACKUP_PATH))
	{
		print "Error: Setup of module SellYourSaas not complete. Path to backup not defined.\n";
		exit -1;
	}

	// Loop on each instance
	if (! $error)
	{
		foreach($instances as $instance)
		{
			$now=dol_now();

			$return_val=0; $error=0; $errors=array();	// No error by default into each loop

			// Run backup
			print "Process backup of instance ".$instance.' - '.strftime("%Y%m%d-%H%M%S")."\n";

			$command=($path?$path.'/':'')."backup_instance.php ".escapeshellarg($instance)." ".escapeshellarg($conf->global->DOLICLOUD_BACKUP_PATH)." ".($action == 'backup'?'confirm':($action == 'backuptestdatabase'?'testdatabase':'testrsync'));
			echo $command."\n";

			if ($action == 'backup')
			{
				//$output = shell_exec($command);
				ob_start();
				passthru($command, $return_val);
				$content_grabbed=ob_get_contents();
				ob_end_clean();

				echo "Result: ".$return_val."\n";
				echo "Output: ".$content_grabbed."\n";
			}

			if ($return_val != 0) $error++;

			// Update database
			if (! $error)
			{
				$db->begin();

				$result=$object->fetch('',$instance);

				if ($action == 'backup')
				{
					$object->date_lastrsync=$now;	// date last files and database rsync backup
					$object->backup_status='OK';
					$object->update();
				}

				$db->commit();
			}
			else
			{
				$db->begin();

				$result=$object->fetch('',$instance);

				if ($action == 'backup')
				{
					//$object->date_lastrsync=$now;	// date last files and database rsync backup
					$object->backup_status='KO '.strftime("%Y%m%d-%H%M%S");
					$object->update();
				}

				$db->commit();
			}


			//
			if (! $error)
			{
				$nbofok++;
				print 'Process success for '.$instance."\n";
			}
			else
			{
				$nboferrors++;
				$instancesbackuperror[]=$instance;
				print 'Process fails for '.$instance."\n";
			}
		}
	}
}


$today=dol_now();

$error=''; $errors=array();

if ($action == 'updatedatabase' || $action == 'updatestatsonly' || $action == 'updatecountsonly')
{
	print "----- Start updatedatabase\n";

	// Loop on each instance
	if (! $error && $action != 'updatestatsonly')
	{
		foreach($instances as $instance)
		{
			$return_val=0; $error=0; $errors=array();

			// Run database update
			print "Process update database info (nb of user) of instance ".$instance.' - '.strftime("%Y%m%d-%H%M%S")."\n";

			$db->begin();

			$result=$object->fetch('',$instance);
			if ($result < 0) dol_print_error('',$object->error);

			$object->oldcopy=dol_clone($object, 1);

			// Files refresh (does not update lastcheck field)
			//$ret=dolicloud_files_refresh($conf,$db,$object,$errors);

			// Database refresh (also update lastcheck field)
			$ret=dolicloud_database_refresh($conf,$db,$object,$errors);		// Update database (or not if error)

			if (count($errors) == 0)
			{
				print "OK nbofusers=".$object->nbofusers."\n";

				$nbofok++;
				$db->commit();
			}
			else
			{
				$nboferrors++;
				$instancesupdateerror[]=$instance;
				print 'KO. '.join(',',$errors)."\n";
				$db->rollback();
			}
		}
	}


	if (! $error && $action != 'updatecountsonly')
	{
		$stats=array();

		// Get list of existing stats
		$sql ="SELECT name, x, y";
		$sql.=" FROM ".MAIN_DB_PREFIX."dolicloud_stats";

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
						$stats[$obj->name][$obj->x]=$obj->y;
						print "Found stats for ".$obj->name." x=".$obj->x." y=".$obj->y."\n";
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
		//print "Found already existing stats entries.\n";

		$tmp=dol_getdate(dol_now('tzserver'));
		$endyear=$tmp['year'];

		// Update all missing stats
		for($year = 2012; $year <= $endyear; $year++)
		{
			for($m = 1; $m <= 12; $m++)
			{
				$datefirstday=dol_get_first_day($year, $m, 1);
				$datelastday=dol_get_last_day($year, $m, 1);
				if ($datefirstday > $today) continue;

				$x=sprintf("%04d%02d",$year,$m);

				$statkeylist=array('total','totalcommissions','totalinstancespaying','totalinstances','totalusers','benefit','totalcustomerspaying','totalcustomers');
				foreach($statkeylist as $statkey)
				{
					if (! isset($stats[$statkey][$x]) || ($today <= $datelastday))
					{
						// Calculate stats fro this key
						print "Calculate and update stats for ".$statkey." x=".$x.' datelastday='.dol_print_date($datelastday, 'dayhour', 'gmt');

						$rep=dolicloud_calculate_stats($db2,$datelastday);

						$total=$rep['total'];
						$totalcommissions=$rep['totalcommissions'];
						$totalinstancespaying=$rep['totalinstancespaying'];
						$totalinstances=$rep['totalinstances'];
						$totalusers=$rep['totalusers'];
						$totalcustomerspaying=$rep['totalcustomerspaying'];
						$totalcustomers=$rep['totalcustomers'];
						$benefit=($total * (1 - $part) - $serverprice - $totalcommissions);

						$y=0;
						if ($statkey == 'total') $y=$total;
						if ($statkey == 'totalcommissions') $y=$totalcommissions;
						if ($statkey == 'totalinstancespaying') $y=$totalinstancespaying;
						if ($statkey == 'totalinstances') $y=$totalinstances;
						if ($statkey == 'totalusers') $y=$totalusers;
						if ($statkey == 'benefit') $y=$benefit;
						if ($statkey == 'totalcustomerspaying') $y=$totalcustomerspaying;
						if ($statkey == 'totalcustomers') $y=$totalcustomers;

						print " -> ".$y."\n";

						if ($today <= $datelastday)	// Remove if current month
						{
							$sql ="DELETE FROM ".MAIN_DB_PREFIX."dolicloud_stats";
							$sql.=" WHERE name = '".$statkey."' AND x='".$x."'";
							dol_syslog("sql=".$sql);
							$resql=$db->query($sql);
							if (! $resql) dol_print_error($db,'');
						}

						$sql ="INSERT INTO ".MAIN_DB_PREFIX."dolicloud_stats(name, x, y)";
						$sql.=" VALUES('".$statkey."', '".$x."', ".$y.")";
						dol_syslog("sql=".$sql);
						$resql=$db->query($sql);
						if (! $resql) dol_print_error($db,'');
					}
				}
			}
		}
	}
}


//print "----- Start calculate amount\n";
// TODO Add more batch here



// Result
print "Nb of instances (all time): ".$nbofalltime."\n";
print "Nb of instances (active with or without payment error, close request or not): ".$nbofactive."\n";
print "Nb of instances (active but close request): ".$nbofactiveclosurerequest."\n";
print "Nb of instances (active but suspended): ".$nbofactivesusp."\n";
print "Nb of instances (active but payment ko, not yet suspended): ".$nbofactivepaymentko."\n";
print "Nb of instances process ok: ".$nbofok."\n";
print "Nb of instances process ko: ".$nboferrors;
print (count($instancesbackuperror)?", error for backup on ".join(',',$instancesbackuperror):"");
print (count($instancesupdateerror)?", error for update on ".join(',',$instancesupdateerror):"");
print "\n";
if (! $nboferrors)
{
	print '--- end ok - '.strftime("%Y%m%d-%H%M%S")."\n";
}
else
{
	print '--- end error code='.$nboferrors.' - '.strftime("%Y%m%d-%H%M%S")."\n";
}

$db->close();	// Close database opened handler

exit($nboferrors);
