<?php
/* Copyright (C) 2001-2002	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2017	Laurent Destailleur		<eldy@users.sourceforge.net>
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

define("NOLOGIN", 1);		// This means this output page does not require to be logged.
define('NOREQUIRETRAN');	// Do not load object $langs
define("NOCSRFCHECK", 1);	// We accept to go on this page from external web site.
define('NOTOKENRENEWAL','1');
define('NOIPCHECK','1');

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
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
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

if (! $action)
{
   	print $langs->trans('ErrorBadParameters')." - action missing";
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

if ($action == 'dolibarrping')
{
    $hash_algo = GETPOST('hash_algo', 'aZ09');
    $hash_unique_id = GETPOST('hash_unique_id', 'aZ09');
    $version = GETPOST('version', 'aZ09');

    if (empty($hash_algo) || empty($hash_unique_id))
    {
        print "\n".'<br>Bad value for parameter hash_algo or hash_unique_id';
    }
    else
    {
        // Insert into database using implicit Transactions
        $captureserver = new CaptureServer($db);
        $captureserver->label = 'dolibarrping';
        $captureserver->label_unique = 'dolibarrping '.$hash_unique_id.' '.$version;
        $captureserver->content = $hash_unique_id;
        $captureserver->qty = 1;
        $captureserver->status = 1;
        $captureserver->comment = 'Ping received at '.dol_print_date(dol_now() , 'gmt');

        $result = $captureserver->create($user);
        // Ignore duplicates

        print "\n".'<br>Event added';
    }
}
else
{
    print "\n".'<br>action not supported';
}

$db->close();
