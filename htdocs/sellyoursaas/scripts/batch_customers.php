#!/usr/bin/php
<?php
/* Copyright (C) 2007-2018 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \brief      Main master SellYouSaas batch
 *      			backup_instance.php (payed customers rsync + databases backup)
 *      			update database info for customer
 *      			update stats
 */

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname($_SERVER['PHP_SELF']).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
    echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
    exit;
}

// Global variables
$version='1.0';
$error=0;


// -------------------- START OF YOUR CODE HERE --------------------
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
    print "Usage: ".$script_file." (backuptestrsync|backuptestdatabase|backup|updatedatabase|updatestatsonly) [old|instancefilter]\n";
    print "\n";
    print "- backuptestrsync     test rsync backup\n";
    print "- backuptestdatabase  test mysqldump backup\n";
    print "- backuprsync         creates backup (rsync)\n";
    print "- backupdatabase      creates backup (mysqldump)\n";
    print "- backup              creates backup (rsync + mysqldump)\n";
    print "- updatedatabase      (=updatecountsonly+updatestatsonly) updates list and nb of users, modules and version and stats\n";
    print "- updatecountsonly    updates counters of instances only (only nb of user for instances)\n";
    print "- updatestatsonly     updates stats only (only table dolicloud_stats)\n";
    exit;
}
print '--- start'."\n";
//print 'Argument 1='.$argv[1]."\n";
//print 'Argument 2='.$argv[2]."\n";



/*
 * Main
 */

$now = dol_now();

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

// Use instancefilter to detect if v1 or v2 or instance
$v=2;
if ($instancefilter == 'old')
{
	$instancefilter='';
	$instancefiltercomplete='';
	$v=1;
}
else
{
	// Force $v according to hard coded values (keep v2 in default case)
	if (! empty($instancefiltercomplete) && ! preg_match('/(\.on|\.with)\.dolicloud\.com$/',$instancefiltercomplete) && ! preg_match('/\.home\.lan$/',$instancefiltercomplete))
	{
		// TODO Manage several domains
		$instancefiltercomplete=$instancefiltercomplete.".".$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES;
	}
	if (! empty($instancefiltercomplete) && preg_match('/\.on\.dolicloud\.com$/',$instancefiltercomplete)) {
		$v=1;
	}
	if (! empty($instancefiltercomplete) && preg_match('/\.with\.dolicloud\.com$/',$instancefiltercomplete)) {
		$v=2;
	}
}

$instances=array();
$instancesactivebutsuspended=array();
$instancesbackuperror=array();
$instancesupdateerror=array();


if ($v==1)
{
	$object=new Dolicloud_customers($db,$db2);

	// Get list of instance
	$sql = "SELECT i.id, i.name as instance, i.status as instance_status,";
	$sql.= " c.status as status,";
	$sql.= " s.payment_status,";
	$sql.= " s.status as subscription_status";
	$sql.= " FROM app_instance as i, subscription as s, customer as c";
	$sql.= " WHERE i.customer_id = c.id AND c.id = s.customer_id";
	if ($instancefiltercomplete) $sql.= " AND i.name = '".$instancefiltercomplete."'";

	$dbtousetosearch = $db2;
}
else
{
	include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
	$object=new Contrat($db);

	// Get list of instance
	$sql = "SELECT c.rowid as id, c.ref, c.ref_customer as instance,";
	$sql.= " ce.deployment_status as instance_status";
	$sql.= " FROM ".MAIN_DB_PREFIX."contrat as c LEFT JOIN ".MAIN_DB_PREFIX."contrat_extrafields as ce ON c.rowid = ce.fk_object";
	$sql.= " WHERE c.ref_customer <> '' AND c.ref_customer IS NOT NULL";
	if ($instancefiltercomplete) $sql.= " AND c.ref_customer = '".$instancefiltercomplete."'";
	else $sql.= " AND ce.deployment_status <> 'undeployed'";		// Exclude undeployed only if we don't request a specific instance
	$sql.= " AND ce.deployment_status IS NOT NULL";

	$dbtousetosearch = $db;
}


dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
$resql=$dbtousetosearch->query($sql);
if ($resql)
{
	$num = $dbtousetosearch->num_rows($resql);
	$i = 0;
	if ($num)
	{
		while ($i < $num)
		{
			$obj = $dbtousetosearch->fetch_object($resql);
			if ($obj)
			{
				$instance = $obj->instance;
				$payment_status='PAID';
				$subscription_status = 'OPEN';

				$found = true;
				if ($v == 1)
				{
					$instance_status = $obj->status;
					$instance_status_bis = $obj->instance_status;
					$payment_status = $obj->payment_status;
					$subscription_status = $obj->subscription_status;
				}
				else
				{
					dol_include_once('/sellyoursaas/lib/sellyoursaas.lib.php');

					$instance_status_bis = '';
					$result = $object->fetch($obj->id);
					if ($result <= 0) $found=false;
					else
					{
						if ($object->array_options['options_deployment_status'] == 'processing') { $instance_status = 'PROCESSING'; }
						elseif ($object->array_options['options_deployment_status'] == 'undeployed') { $instance_status = 'CLOSED'; $instance_status_bis = 'UNDEPLOYED'; }
						elseif ($object->array_options['options_deployment_status'] == 'done')       { $instance_status = 'DEPLOYED'; }
						else { $instance_status = 'UNKNOWN'; }
					}

					$issuspended = sellyoursaasIsSuspended($object);
					if ($issuspended)
					{
						$subscription_status = 'CLOSED';
						$instance_status = 'SUSPENDED';
					}
					else
					{
						$subscription_status = 'OPEN';
					}

					$ispaid = sellyoursaasIsPaidInstance($object);
					if (! $ispaid) $payment_status='TRIAL';
					else
					{
						$ispaymentko = sellyoursaasIsPaymentKo($object);
						if ($ispaymentko) $payment_status='FAILURE';
					}
				}
				if (empty($instance_status_bis)) $instance_status_bis=$instance_status;
				print "Analyze instance ".($i+1)." V".$v." ".$instance." status=".$instance_status." instance_status=".$instance_status_bis." payment_status=".$payment_status." subscription_status=".$subscription_status."\n";

				// Count
				if (! in_array($payment_status,array('TRIAL','TRIALING','TRIAL_EXPIRED')))
				{
					$nbofalltime++;
					if (! in_array($instance_status,array('PROCESSING')) && ! in_array($instance_status,array('CLOSED')) && ! in_array($instance_status_bis,array('UNDEPLOYED')))		// Nb of active
					{
						$nbofactive++;

						if (in_array($instance_status,array('SUSPENDED')))
						{
							$nbofactivesusp++;
							$instancesactivebutsuspended[$obj->id]=$obj->ref.' ('.$instance.')';
						}
						else if (in_array($instance_status,array('CLOSE_QUEUED','CLOSURE_REQUESTED')) ) $nbofactiveclosurerequest++;
						else if (in_array($payment_status,array('FAILURE','PAST_DUE'))) $nbofactivepaymentko++;
						else $nbofactiveok++; // not suspended, not close request

						$instances[$obj->id]=$instance;
						print "Qualify instance V".$v." ".$instance." with instance_status=".$instance_status." instance_status_bis=".$instance_status_bis." payment_status=".$payment_status." subscription_status(not used)=".$subscription_status."\n";
					}
					else
					{
						//print "Found instance ".$instance." with instance_status=".$instance_status." instance_status_bis=".$instance_status_bis." payment_status=".$payment_status." subscription_status(not used)=".$obj->subscription_status."\n";
					}
				}
				elseif ($instancefiltercomplete)
				{
					$instances[$obj->id]=$instance;
					print "Qualify instance V".$v." ".$instance." with instance_status=".$instance_status." instance_status_bis=".$instance_status_bis." payment_status=".$payment_status." subscription_status(not used)=".$obj->subscription_status."\n";
				}
				else
				{
					//print "Found instance ".$instance." with instance_status=".$instance_status." instance_status_bis=".$instance_status_bis." payment_status=".$payment_status." subscription_status(not used)=".$obj->subscription_status."\n";
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
	dol_print_error($dbtousetosearch);
}
print "Found ".count($instances)." not trial instances including ".$nbofactivesusp." suspended + ".$nbofactiveclosurerequest." active with closure request + ".$nbofactivepaymentko." active with payment ko\n";


//print "----- Start loop for backup_instance\n";
if ($action == 'backup' || $action == 'backuprsync' || $action == 'backupdatabase' || $action == 'backuptestrsync' || $action == 'backuptestdatabase')
{
	if (empty($conf->global->DOLICLOUD_BACKUP_PATH))
	{
		print "Error: Setup of module SellYourSaas not complete. Path to backup not defined.\n";
		exit -1;
	}

	// Loop on each instance
	if (! $error)
	{
		$i = 0;
		foreach($instances as $instance)
		{
			$now=dol_now();

			$return_val=0; $error=0; $errors=array();	// No error by default into each loop

			// Run backup
			print "***** Process backup of instance ".($i+1)." V".$v." ".$instance.' - '.strftime("%Y%m%d-%H%M%S")."\n";

			$mode = 'unknown';
			$mode = ($action == 'backup'?'confirm':$mode);
			$mode = ($action == 'backuprsync'?'confirmrsync':$mode);
			$mode = ($action == 'backupdatabase'?'confirmdatabase':$mode);
			$mode = ($action == 'backuptestdatabase'?'testdatabase':$mode);
			$mode = ($action == 'backuptestrsync'?'testrsync':$mode);

			$command=($path?$path:'')."backup_instance.php ".escapeshellarg($instance)." ".escapeshellarg($conf->global->DOLICLOUD_BACKUP_PATH)." ".$mode;
			echo $command."\n";

			if ($action == 'backup' || $action == 'backuprsync' || $action == 'backupdatabase')
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

			// Return
			if (! $error)
			{
				$nbofok++;
				print '-> Backup process success for '.$instance."\n";
			}
			else
			{
				$nboferrors++;
				$instancesbackuperror[]=$instance;
				print '-> Backup process fails for '.$instance."\n";
			}

			$i++;
		}
	}
}


$today=dol_now();

$error=''; $errors=array();
$servicetouse='old';
if ($v != 1) $servicetouse=strtolower($conf->global->SELLYOURSAAS_NAME);

if ($action == 'updatedatabase' || $action == 'updatestatsonly' || $action == 'updatecountsonly')
{
	print "----- Start updatedatabase\n";

	dol_include_once('sellyoursaas/class/sellyoursaasutils.class.php');
	$sellyoursaasutils = new SellYourSaasUtils($db);

	// Loop on each instance
	if (! $error && $action != 'updatestatsonly')
	{
		$i=0;
		foreach($instances as $instance)
		{
			$return_val=0; $error=0; $errors=array();

			// Run database update
			print "Process update database info (nb of user) of instance ".($i+1)." V".$v." ".$instance.' - '.strftime("%Y%m%d-%H%M%S")." : ";

			$db->begin();

			if ($v == 1)	// $object is DolicloudCustomer
			{
				$result=$object->fetch('',$instance);
				if ($result < 0) dol_print_error('',$object->error);

				$object->oldcopy=dol_clone($object, 1);

				// Files refresh (does not update lastcheck field)
				//$ret=dolicloud_files_refresh($conf,$db,$object,$errors);

				// Database refresh (also update lastcheck field)
				$ret=dolicloud_database_refresh($conf,$db,$object,$errors);		// Update database (or not if error)
			}
			else			// $object is Contrat
			{
				$result=$object->fetch('','',$instance);
				if ($result < 0) dol_print_error('',$object->error);

				$object->oldcopy=dol_clone($object, 1);

				$result = $sellyoursaasutils->sellyoursaasRemoteAction('refresh', $object);
				if ($result < 0)
				{
					$errors[] = 'Failed to do sellyoursaasRemoteAction(refresh) '.$sellyoursaasutils->error.(is_array($sellyoursaasutils->errors)?' '.join(',',$sellyoursaasutils->errors):'');
				}
			}

			if (count($errors) == 0)
			{
				if ($v == 1)	// $object is DolicloudCustomer
					print "OK nbofusers=".$object->nbofusers."\n";
				else
					print "OK";

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

			$i++;
		}
	}


	if (! $error && $action != 'updatecountsonly')
	{
		$stats=array();

		// Get list of existing stats
		$sql ="SELECT name, x, y";
		$sql.=" FROM ".MAIN_DB_PREFIX."dolicloud_stats";
		$sql.=" WHERE service = '".$servicetouse."'";

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
		if (empty($serverprice))
		{
			print 'ERROR Value of variable $serverprice is not defined.';
			exit;
		}

		$YEARSTART = 2012;
		if ($v != 1) $YEARSTART = 2018;

		// Update all missing stats
		for($year = $YEARSTART; $year <= $endyear; $year++)
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

						$rep = null;
						$part = 0;

						if ($v == 1)
						{
							$rep=dolicloud_calculate_stats($db2,$datelastday);
							$part = 0.3;
						}
						else
						{
							$rep=sellyoursaas_calculate_stats($db,$datelastday);	// Get qty and amount into template invoices linked to active contracts
							$part = (empty($conf->global->SELLYOURSAAS_PERCENTAGE_FEE) ? 0 : $conf->global->SELLYOURSAAS_PERCENTAGE_FEE);
						}

						if ($rep)
						{
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
								$sql.=" AND service = '".$servicetouse."'";
								dol_syslog("sql=".$sql);
								$resql=$db->query($sql);
								if (! $resql) dol_print_error($db,'');
							}

							$sql ="INSERT INTO ".MAIN_DB_PREFIX."dolicloud_stats(service, name, x, y)";
							$sql.=" VALUES('".$servicetouse."', '".$statkey."', '".$x."', ".$y.")";
							dol_syslog("sql=".$sql);
							$resql=$db->query($sql);
							//if (! $resql) dol_print_error($db,'');		// Ignore error, we may have duplicate record here if record already exists and not deleted
						}
					}
				}
			}
		}
	}
}




// Result
$out = '';
$out.= "Nb of paying instances (all time): ".$nbofalltime."\n";
$out.= "Nb of paying instances (active with or without payment error, close request or not): ".$nbofactive."\n";
$out.= "Nb of paying instances (active but close request): ".$nbofactiveclosurerequest."\n";
$out.= "Nb of paying instances (active but suspended): ".$nbofactivesusp;
$out.= (count($instancesactivebutsuspended)?", suspension on ".join(', ',$instancesactivebutsuspended):"");
$out.= "\n";
$out.= "Nb of paying instances (active but payment ko, not yet suspended): ".$nbofactivepaymentko."\n";
if ($action != 'updatestatsonly')
{
	$out.= "Nb of paying instances processed ok: ".$nbofok."\n";
	$out.= "Nb of paying instances processed ko: ".$nboferrors;
}
$out.= (count($instancesbackuperror)?", error for backup on ".join(', ',$instancesbackuperror):"");
$out.= (count($instancesupdateerror)?", error for update on ".join(', ',$instancesupdateerror):"");
$out.= "\n";
print $out;
if (! $nboferrors)
{
	print '--- end OK - '.strftime("%Y%m%d-%H%M%S")."\n";

	if ($action == 'backup' || $action == 'backuprsync' || $action == 'backupdatabase' || $action == 'backuptestrsync' || $action == 'backuptestdatabase')
	{
		$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
		$to = $conf->global->SELLYOURSAAS_SUPERVISION_EMAIL;
		$msg = 'Backup done without errors by '.$script_file." ".$argv[1]." ".$argv[2]."\n\n".$out;

		include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
		$cmail = new CMailFile('['.$conf->global->SELLYOURSAAS_NAME.'] Success for backup', $to, $from, $msg);
		$result = $cmail->sendfile();
	}
}
else
{
	print '--- end ERROR nb='.$nboferrors.' - '.strftime("%Y%m%d-%H%M%S")."\n";

	if ($action == 'backup' || $action == 'backuprsync' || $action == 'backupdatabase' || $action == 'backuptestrsync' || $action == 'backuptestdatabase')
	{
		$from = $conf->global->SELLYOURSAAS_NOREPLY_EMAIL;
		$to = $conf->global->SELLYOURSAAS_SUPERVISION_EMAIL;
		$msg = 'Error in '.$script_file." ".$argv[1]." ".$argv[2]."\n\n".$out;

		include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
		$cmail = new CMailFile('[Alert] Error in backups - '.dol_print_date(dol_now(), 'dayrfc'), $to, $from, $msg, array(), array(), array(), '', '', 0, 0, '', '', '', '', 'emailing');
		$result = $cmail->sendfile();
	}
}

$db->close();	// Close database opened handler

exit($nboferrors);
