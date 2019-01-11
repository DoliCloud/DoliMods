<?php
/* Copyright (C) 2001-2002	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2017	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2009-2012	Regis Houssin			<regis.houssin@capnetworks.com>
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
 */

/**
 *     	\file       htdocs/sellyoursaas/public/spamreport.php
 *		\ingroup    sellyoursaas
 *		\brief      Page to report SPAM
 */

define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.

// For MultiCompany module.
// Do not use GETPOST here, function is not defined and get of entity must be done before including main.inc.php
$entity=(! empty($_GET['entity']) ? (int) $_GET['entity'] : (! empty($_POST['entity']) ? (int) $_POST['entity'] : (! empty($_GET['e']) ? (int) $_GET['e'] : (! empty($_POST['e']) ? (int) $_POST['e'] : 1))));
if (is_numeric($entity)) define("DOLENTITY", $entity);

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/payments.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societeaccount.class.php';


$tmpfile = '/tmp/spamreport.log';
$date = strftime("%Y-%m-%d %H:%M:%S" ,time());

file_put_contents($tmpfile, "\n***** Spam report received ".$date."*****\n", FILE_APPEND);
file_put_contents($tmpfile, var_export($_SERVER, true), FILE_APPEND);
echo "Spam report received at ".$date."<br>\n";

$body = file_get_contents('php://input');
file_put_contents($tmpfile, $body, FILE_APPEND);

echo "Content of alert message<br>\n";
echo $body;

file_put_contents($tmpfile, "\n", FILE_APPEND);
echo "<br>\n";



// Send email
file_put_contents($tmpfile, "Now we send an email to supervisor ".$conf->global->SELLYOURSAAS_SUPERVISION_EMAIL."\n", FILE_APPEND);

$headers = 'From: <'.$conf->global->SELLYOURSAAS_NOREPLY_EMAIL.">\r\n";
$success=mail($conf->global->SELLYOURSAAS_SUPERVISION_EMAIL, '[Alert] Spam report received from SendGrid', 'Spam was reported by SendGrid:'."\r\n".$body, $headers);
if (!$success) {
	$errorMessage = error_get_last()['message'];
	print $errorMessage;
}
else
{
    echo "Email sent to ".$conf->global->SELLYOURSAAS_SUPERVISION_EMAIL."<br>\n";
}

// Send to datadog (metric + event)
if (! empty($conf->global->SELLYOURSAAS_DATADOG_ENABLED))
{
    file_put_contents($tmpfile, "Now we send ping to DataDog\n", FILE_APPEND);
    echo "Now we send ping to DataDog<br>\n";

    dol_include_once('/sellyoursaas/core/includes/php-datadogstatsd/src/DogStatsd.php');

    $arrayconfig=array();
    if (! empty($conf->global->SELLYOURSAAS_DATADOG_APIKEY))
    {
        $arrayconfig=array('apiKey'=>$conf->global->SELLYOURSAAS_DATADOG_APIKEY, 'app_key' => $conf->global->SELLYOURSAAS_DATADOG_APPKEY);
    }

    $statsd = new DataDog\DogStatsd($arrayconfig);

    $arraytags=null;

    $statsd->increment('sellyoursaas.spamreported', 1, $arraytags);

    $statsd->event($conf->global->SELLYOURSAAS_NAME.' - Spam of a customer detected',
        array(
            'text'       => $conf->global->SELLYOURSAAS_NAME.' - Spam of a customer detected',
            'alert_type' => 'warning'
        )
    );

    echo "Ping sent to DataDog<br>\n";
}
