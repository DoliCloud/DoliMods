<?php
/* Copyright (C) 2001-2002	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2019	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2009-2012	Regis Houssin			<regis.houssin@inodbox.com>
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
 *
 * For paypal test: https://developer.paypal.com/
 * For paybox test: ???
 */

/**
 *     	\file       htdocs/captureserver/public/index.php
 *		\ingroup    core
 *		\brief      Endpoint provided by module captureserver
 */

define("NOLOGIN", '1');		   // This means this output page does not require to be logged.
define('NOREQUIRETRAN', '1');  // Do not load object $langs
define("NOCSRFCHECK", '1');	   // We accept to go on this page from external web site.
define('NOTOKENRENEWAL', '1');
define('NOIPCHECK', '1');
define('NOREQUIREMENU', '1');
define('NOSESSION', '1');

// For MultiCompany module.
// Do not use GETPOST here, function is not defined and define must be done before including main.inc.php
// TODO This should be useless. Because entity must be retreive from object ref and not from url.
$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_numeric($entity)) define("DOLENTITY", $entity);

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once '../class/captureserver.class.php';

// Security check
// No check on module enabled. Done later according to $validpaymentmethod

$action=GETPOST('action', 'aZ09');

// Input are:
// type ('invoice','order','contractline'),
// id (object id),
// amount (required if id is empty),
// tag (a free text, required if type is empty)
// currency (iso code)

if (! $action) {
	print "ErrorBadParameters - action missing";
	exit;
}


// Complete urls for post treatment
$SECUREKEY=GETPOST("securekey", 'alpha');	        // Secure key


/*
 * Actions
 */



/*
 * View
 */

header("Access-Control-Allow-Origin: *");

print 'Capture server was called with action='.$action;

if ($action == 'dolibarrping') {
	$hash_algo = GETPOST('hash_algo', 'aZ09');
	$hash_unique_id = GETPOST('hash_unique_id', 'aZ09');
	$version = GETPOST('version', 'aZ09');

	if (empty($hash_algo) || empty($hash_unique_id)) {
		print "\n".'<br>Bad value for parameter hash_algo or hash_unique_id';
	} else {
		$maxsize = (empty($conf->global->CAPTURE_SERVER_MAX_SIZE_OF_CAPTURED_CONTENT) ? 1024 : $conf->global->CAPTURE_SERVER_MAX_SIZE_OF_CAPTURED_CONTENT);
		if (is_array($_POST) && strlen(join('', $_POST)) > $maxsize) {
			$contenttoinsert = 'Content larger than limit of '.$maxsize;
		} else {
			$contenttoinsert = json_encode($_POST);
		}

		// Insert into database using implicit Transactions
		$captureserver = new CaptureServer($db);
		$result = $captureserver->fetch(0, $action.'_'.$hash_unique_id);

		if ($result > 0) {
			$captureserver->comment = 'Ping received for update at '.dol_print_date(dol_now(), 'dayhourlog').' - from hash '.$hash_unique_id.' - version '.$version;
			$captureserver->content = $contenttoinsert;
			$captureserver->label = $action.' '.$hash_unique_id.' '.$version;
			$captureserver->update($user);

			// Send to DataDog (metric + event)
			if (! empty($conf->global->CAPTURESERVER_DATADOG_ENABLED)) {
				try {
					dol_include_once('/captureserver/core/includes/php-datadogstatsd/src/DogStatsd.php');

					$arrayconfig=array();
					if (! empty($conf->global->CAPTURESERVER_DATADOG_APIKEY)) {
						$arrayconfig=array('apiKey'=>$conf->global->CAPTURESERVER_DATADOG_APIKEY, 'app_key' => $conf->global->CAPTURESERVER_DATADOG_APPKEY);
					}

					$statsd = new DataDog\DogStatsd($arrayconfig);

					$phpversion = join('.', array_slice(explode('.', GETPOST('php_version', 'alphanohtml')), 0, 2));
					$dolversion = GETPOST('version', 'alphanohtml');

					$arraytags=array('version'=>$dolversion, 'dbtype'=>GETPOST('dbtype', 'alphanohtml'), 'country_code'=>GETPOST('country_code', 'aZ09'), 'php_version'=>$phpversion);

					dol_syslog("Send info to datadog");

					$statsd->increment('captureserver.dolibarrping-update', 1, $arraytags);
				} catch (Exception $e) {
					dol_syslog("Error in sending info to datadog", LOG_WARNING);
				}
			}

			print "<br>\n".'Event updated';
		} elseif ($result == 0) {
			$captureserver->type = $action;
			$captureserver->ref = $action.'_'.$hash_unique_id;
			$captureserver->label = $action.' '.$hash_unique_id.' '.$version;
			$captureserver->content = $contenttoinsert;
			$captureserver->qty = 1;
			$captureserver->status = 1;
			$captureserver->comment = 'Ping received at '.dol_print_date(dol_now(), 'dayhourlog').' - from hash '.$hash_unique_id.' - version '.$version;
			$result = $captureserver->create($user);

			// Send to DataDog (metric + event)
			if (! empty($conf->global->CAPTURESERVER_DATADOG_ENABLED)) {
				try {
					dol_include_once('/captureserver/core/includes/php-datadogstatsd/src/DogStatsd.php');

					$arrayconfig=array();
					if (! empty($conf->global->CAPTURESERVER_DATADOG_APIKEY)) {
						$arrayconfig=array('apiKey'=>$conf->global->CAPTURESERVER_DATADOG_APIKEY, 'app_key' => $conf->global->CAPTURESERVER_DATADOG_APPKEY);
					}

					$statsd = new DataDog\DogStatsd($arrayconfig);

					$phpversion = join('.', array_slice(explode('.', GETPOST('php_version', 'alphanohtml')), 0, 2));
					$dolversion = GETPOST('version', 'alphanohtml');
					$dbversion = GETPOST('db_version', 'alphanohtml');
					$distrib = GETPOST('distrib', 'alphanohtml');

					// Protection against too accurate versions
					$dbversion = preg_replace('/[\.\-]\d*ubuntu.*/i', '', $dbversion);
					$dbversion = preg_replace('/([\.\-]\d*mariadb).*/i', '\1', $dbversion);

					// Protection against too accurate versions
					$osversionarray = preg_split('/\.\-/', GETPOST('os_version', 'alphanohtml'));
					$osversion = '';
					$i = 0;
					foreach($osversionarray as $osversioncursor) {
						if ($i >= 4) {
							break;
						}
						$osversion .= (($i > 1) ? '.' : '').$osversioncursor;
						$i++;
					}
					$arraytags=array('version'=>$dolversion, 'dbtype'=>GETPOST('dbtype', 'alphanohtml'), 'country_code'=>GETPOST('country_code', 'aZ09'), 'php_version'=>$phpversion, 'db_version'=>$dbversion, 'os_version'=>$osversion, 'distrib'=>$distrib);

					dol_syslog("Send info to datadog");

					$statsd->increment('captureserver.dolibarrping-add', 1, $arraytags);
				} catch (Exception $e) {
					dol_syslog("Error in sending info to datadog", LOG_WARNING);
				}
			}

			print "<br>\n".'Event added';
		} else {
		}
	}
} else {
	print "<br>\n".'Action not supported';
}

$db->close();
