#/usr/bin/php
<?php
/* Copyright (C) 2006      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2007-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *	\file       	scripts/monitor/monitor_daemon.php
 *	\ingroup    	monitor
 *	\brief      	Script to execute monitor daemon
 *	\version		$Id: monitor_daemon.php,v 1.1 2011/01/23 01:05:41 eldy Exp $
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
$version='$Revision: 1.1 $';
$error=0;

// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include($path."../../htdocs/master.inc.php");
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../dolibarr/htdocs/master.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../../dolibarr/htdocs/master.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../../../dolibarr/htdocs/master.inc.php");   // Used on dev env only
if (! $res) die("Include of master fails");
// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

// -------------------- START OF YOUR CODE HERE --------------------
dol_include_once("/monitoring/lib/monitoring.lib.php");

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs->load("main");				// To load language file for default language
$langs->load("monitoring");				// To load language file for default language
@set_time_limit(0);					// No timeout for this script

// Load user and its permissions
$result=$user->fetch('','admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
if (! $result > 0) { dol_print_error('',$user->error); exit; }
$user->getrights();


print "***** ".$script_file." (".$version.") *****\n";
if (! isset($argv[1])) {	// Check parameters
	print "Usage: ".$script_file." start\n";
	exit;
}
print '--- start'."\n";
//print 'Argument 1='.$argv[1]."\n";
//print 'Argument 2='.$argv[2]."\n";

$verbose = 0;

for ($i = 1 ; $i < sizeof($argv) ; $i++)
{
	if ($argv[$i] == "-v")
	{
		$verbose = 1;
	}
	if ($argv[$i] == "-vv")
	{
		$verbose = 2;
	}
	if ($argv[$i] == "-vvv")
	{
		$verbose = 3;
	}
}

$dir = $conf->monitor->dir_output;
$result=create_exdir($dir);
if ($result < 0)
{
	dol_print_error('','Failed to create dir '.$dir);
	exit;
}

// Define url to scan
$listofurls=array(
0=>array('code'=>'test1','url'=>'http://localhost','keytovalidate'=>'rr'),
);


$frequency=5;
// Create rrd if not exists
foreach($listofurls as $object)
{
	$fname = $conf->monitoring->dir_output.'/'.$object['code'].'/monitoring.rrd';

	$error=0;
	create_exdir($conf->monitoring->dir_output.'/'.$object['code']);

	$step=$frequency;
	$opts = array( "--step", $step,
           "DS:ds1:GAUGE:".($step*2).":0:100",
           "DS:ds2:GAUGE:".($step*2).":0:100",
           "RRA:AVERAGE:0.5:1:".(3600/$step),
           "RRA:AVERAGE:0.5:".(60/$step).":1440",
           "RRA:AVERAGE:0.5:".(3600/$step).":168",
           "RRA:AVERAGE:0.5:".(3600/$step).":744",
           "RRA:AVERAGE:0.5:".(86400/$step).":365",
           "RRA:MAX:0.5:1:".(3600/$step),
           "RRA:MAX:0.5:".(60/$step).":1440",
           "RRA:MAX:0.5:".(3600/$step).":168",
           "RRA:MAX:0.5:".(3600/$step).":744",
           "RRA:MAX:0.5:".(86400/$step).":365",
           "RRA:MIN:0.5:1:".(3600/$step),
           "RRA:MIN:0.5:".(60/$step).":1440",
           "RRA:MIN:0.5:".(3600/$step).":168",
           "RRA:MIN:0.5:".(3600/$step).":744",
           "RRA:MIN:0.5:".(86400/$step).":365",
	);

	$ret = rrd_create($fname, $opts, count($opts));
	$resout=file_get_contents($fname.'.out');
	if (strlen($resout) < 10)
	{
		$mesg='<div class="ok">'.$langs->trans("File ".$fname.' created').'</div>';
	}
	else
	{
		$error++;
		$err = rrd_error($fname);
		$mesg="Create error: $err\n";
	}
}

if (! $error)
{
	foreach($listofurls as $object)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$object['url']);
		//curl_setopt($ch, CURLOPT_URL,"http://www.j1b.org/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		//curl_setopt($ch, CURLOPT_POST, 0);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, "a=3&b=5");
		//--- Start buffering
		//ob_start();
		$result=curl_exec ($ch);
		dol_syslog($result);
		//--- End buffering and clean output
		//ob_end_clean();
		if (curl_error($ch) > 0)
		{
			// error
			return 0;
		}
		curl_close ($ch);
	}
}

if (! $error)
{
	print '--- end ok'."\n";
}
else
{
	print '--- end error code='.$error."\n";
}

?>
