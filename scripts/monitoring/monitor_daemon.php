#!/usr/bin/php
<?php
/* Copyright (C) 2011-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**
 *	\file       	scripts/monitoring/monitor_daemon.php
 *	\ingroup    	monitor
 *	\brief      	Script to execute monitor daemon
 */

if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1'); // If there is no menu to show
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1'); // If we don't need to load the html.form.class.php
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');       // If this page is public (can be called outside logged session)

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
	echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
	exit;
}

// Global variables
$version='$Revision: 1.17 $';
$error=0;
// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include($path."../../htdocs/master.inc.php");
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../master.inc.php")) $res=@include("../../../master.inc.php");
if (! $res && file_exists($path."../../../dolibarr/htdocs/master.inc.php")) $res=@include($path."../../../dolibarr/htdocs/master.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../dolibarr/htdocs/master.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../../dolibarr/htdocs/master.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../../../dolibarr/htdocs/master.inc.php");   // Used on dev env only
if (! $res) die("Include of master fails");
// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.


// -------------------- START OF YOUR CODE HERE --------------------
include_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
dol_include_once("/monitoring/lib/monitoring.lib.php");
dol_include_once("/monitoring/class/monitoring_probes.class.php");

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
//$langs->load("main");				// To load language file for default language
//$langs->load("monitoring");				// To load language file for default language
@set_time_limit(0);					// No timeout for this script

// Load user and its permissions
/*
$result=$user->fetch('','admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
if (! $result > 0) { dol_print_error('',$user->error); exit; }
$user->getrights();
*/

// Activate error interceptions
function traitementErreur($code, $message, $fichier, $ligne, $contexte)
{
    if (error_reporting() & $code) {
        throw new Exception($message, $code);
    }
}
set_error_handler('traitementErreur');

$nbok=0;
$nbko=0;
$frequency=5;   // seconds
$maxloops=0;




print "***** ".$script_file." (".$version.") *****\n";
if (! isset($argv[1])) {	// Check parameters
	print "Usage: ".$script_file." start [-maxloops=x]\n";
	exit;
}

if (! function_exists('pcntl_fork')) die('PCNTL functions not available on this PHP installation');


print '--- start'."\n";
//print 'Argument 1='.$argv[1]."\n";
//print 'Argument 2='.$argv[2]."\n";

$verbose = 0;
for ($i = 1 ; $i < count($argv) ; $i++)
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
    if (preg_match('/-maxloops=(\d+)/i',$argv[$i],$reg))
    {
        $maxloops=$reg[1];
    }
}

$dir = $conf->monitoring->dir_output;
$result=dol_mkdir($dir);
if ($result < 0)
{
	dol_print_error('','Failed to create dir '.$dir);
	exit;
}

// Define url to scan
$listofurls=getListOfProbes(1);
if (! count($listofurls))
{
    print 'No enabled probe found. Please define at least one probe before running probe process.'."\n";
    exit;
}

print 'Data will be saved into: '.$conf->monitoring->dir_output."\n";


// Create rrd if not exists
foreach($listofurls as $object)
{
	$fname = $conf->monitoring->dir_output.'/'.$object['code'].'/monitoring.rrd';

	$error=0;
	dol_mkdir($conf->monitoring->dir_output.'/'.$object['code']);

	if (! dol_is_file($conf->monitoring->dir_output.'/'.$object['code'].'/monitoring.rrd'))
	{
		$step=$object['frequency'];
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
			$mesg='<div class="ok">File '.$fname.' created.</div>';
		}
		else
		{
			$error++;
			$err = rrd_error($fname);
			$mesg="Create error: $err\n";
		}
	}
}


$pid=0;

if (! $error)
{
    // Reload sometimes list of urls
	//$listofurls=getListOfUrls(1);
    $pid_arr = array();
	foreach($listofurls as $key => $object)
	{
		$pid = pcntl_fork();
        if ($pid == 0)
        {
             // @child: Include() misbehaving code here
             print "FORK: Child probe id ".$object['code']." preparing to nuke...\n";
             $resarray=process_probe_x($object,$maxloops); //generate_fatal_error(); // Undefined function
             $nbok+=$resarray['nbok'];
             $nbko+=$resarray['nbko'];
             break;
        }
		if ($pid == -1)
		{
             // @fail
             die('Fork failed for process '.$key);
             continue;
		}
		if ($pid > 0)
		{
             // @parent
             print "FORK: Parent, letting the child with pid ".$pid." run amok...\n";
             $pid_arr[$key] = $pid;
             continue;
        }
	}

}

if ($pid != 0)
{
    usleep(1000);

    print 'Parent process has launched '.sizeof($pid_arr)." processes. Waiting the end...\n";

    // Loop until end of all processes (array is empty for childs end)
    while (count($pid_arr) > 0)
    {
            $myId = pcntl_waitpid(-1, $status, WNOHANG);
            foreach($pid_arr as $key => $pid)
            {
                    if($myId == $pid) unset($pid_arr[$key]);
            }
            usleep(100);
    }


    if (! $error)
    {
    	//print "--- end ok:".$nbok.' ko:'.$nbko."\n";
    	print "--- end\n";
    }
    else
    {
    	print '--- end error code='.$error."\n";
    }
}

exit(0);



/**
 *
 */
function process_probe_x($object,$maxloops=0)
{
    global $conf, $db;

    $nbok=$nbko=0;
    $nbloop=0;
    $timeout=10;    // TODO Manage this

    $fname = $conf->monitoring->dir_output.'/'.$object['code'].'/monitoring.rrd';

    // Init objects ($ch, $socket or $client)
    if (($object['typeprot'] == 'GET' || $object['typeprot'] == 'POST') && preg_match('/^http/i',$object['url']))
    {
        $ch = curl_init();
        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 0);

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if ($object['useproxy'])
        {
            curl_setopt ($ch, CURLOPT_PROXY, $conf->global->MAIN_PROXY_HOST. ":" . $conf->global->MAIN_PROXY_PORT);
            if (! empty($conf->global->MAIN_PROXY_USER)) curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $conf->global->MAIN_PROXY_USER. ":" . $conf->global->MAIN_PROXY_PASS);
        }

        if ($object['typeprot'] == 'GET')
        {
            curl_setopt($ch, CURLOPT_POST, 0);
        }
        if ($object['typeprot'] == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $object['url_params']);
        }

        curl_setopt($ch, CURLOPT_URL,$object['url']);
    }
    if ($object['typeprot'] == 'SOCKET' && preg_match('/^tcp/i',$object['url']))
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }
    if ($object['typeprot'] == 'SOAP' && preg_match('/^http/i',$object['url']))
    {
        $arrayoption=array(
        	'location' => $object['url'],
			'soap_version'=>SOAP_1_2,
            'exceptions'=>true,
            'trace'=>1
        );
        // $arrayoption['keep_alive']=1;  // PHP 5.4
        if (1 == 2)    // Mode WSDL
        {
            // TODO
        }
        else           // Mode non WSDL
        {
            $arrayoption['uri']  ="http://www.dolibarr.org/ns/";
            $arrayoption['style']=SOAP_DOCUMENT;
            $arrayoption['use']  =SOAP_LITERAL;
        }
        if ($object['useproxy'])
        {
            $arrayoption['proxy_host']    = $conf->global->MAIN_PROXY_HOST;
            $arrayoption['proxy_port']    = $conf->global->MAIN_PROXY_PORT;
            if (! empty($conf->global->MAIN_PROXY_USER))
            {
                $arrayoption['proxy_login']   = $conf->global->MAIN_PROXY_USER;
                $arrayoption['proxy_password']= $conf->global->MAIN_PROXY_PASS;
            }
        }
        //$arrayoption['authentication']=SOAP_AUTHENTICATION_BASIC;
        //$arrayoption['login']='';
        //$arrayoption['password']='';

        $client = new SoapClient(null, $arrayoption);
    }



    // Loops
    while(! $error && (empty($maxloops) || ($nbloop < $maxloops)))
    {
        $nbloop++;

        $value1='U';
        $value2='U';
        $errortext='';
        $done=0;
        $micro_start_time=$micro_end_time=$end_time='';
        $delay=0;

        // Each managed protocol must define $end_time, $delay, $value1, $value2 and increase $nbok or $nbko

        // Protocol HTTP or HTTPS
        if (($object['typeprot'] == 'GET' || $object['typeprot'] == 'POST') && preg_match('/^http/i',$object['url']))
        {
            //ob_start();
            list($usec, $sec) = explode(" ", microtime());
            $micro_start_time=((float)$usec + (float)$sec);

            $result = curl_exec($ch);

            list($usec, $sec) = explode(" ", microtime());
            $micro_end_time=((float)$usec + (float)$sec);
            $end_time=((float)$sec);

            $delay=($micro_end_time-$micro_start_time);

            if (! $result)
            {
                print dol_print_date($end_time,'dayhourlog').' Error for id='.$object['code'].': '.curl_error($ch)."\n";
            }

            if (curl_error($ch))    // Test with no response
            {
                $value1='U';
                $value2=max(round($object['max']),1);
                $nbko++;
                $errortext='Failed to get response. Curl return: '.curl_error($ch);
            }
            else
            {
                //var_dump($result);
                if (preg_match('/'.preg_quote($object['checkkey']).'/',$result))
                {   // Test ok
                    $value1=max(round($delay*1000),1);
                    $value2='U';
                    $nbok++;
                    $errortext='';
                }
                else
                {   // Test ko
                    $value1='U';
                    $value2=max(round($object['max']),1);
                    $nbko++;
                    $errortext='Failed to find string "'.$object['checkkey'].'" into reponse string.\nResponse string is '.$result;
                }
            }

            //curl_close ($ch); unset($ch);

            $done=1;
        }

        // Protocol TCPIP
        if ($object['typeprot'] == 'SOCKET' && preg_match('/^tcp/i',$object['url']))
        {
            $resultat=0;

            list($usec, $sec) = explode(" ", microtime());
            $micro_start_time=((float)$usec + (float)$sec);

            if ($socket)
            {
                $tmparray=explode(':',$object['url']);
                $adresse=preg_replace('/\//','',$tmparray[1]);
                $service_port=$tmparray[2];
                //print 'adress='.$adresse.' port='.$service_port."\n";
                try
                {
                    $resultat = socket_connect($socket, $adresse, $service_port);
                }
                catch(Exception $e)
                {
                    $errortext.='Failed to connect to address='.$adresse.' port='.$service_port.', reason is: '.$e->getMessage()."\n";
                }
                if ($resultat < 0)
                {
                    $errortext='Failed to connect using socket. Reason is: '.socket_strerror ($resultat);
                }
                /*
                $envoi = "HEAD / HTTP/1.0\r\n\r\n";
                $envoi .= "Host: www.siteduzero.com\r\n";
                $envoi .= "Connection: Close\r\n\r\n";
                $reception = '';

                echo "Envoi de la requête HTTP HEAD...";
                socket_write($socket, $envoi, strlen($envoi));
                echo "OK.<br>";

                echo "Lire la réponse : <br><br>";
                while ($reception = socket_read($socket, 2048))
                   echo $reception;
                */

                //socket_close($socket); unset($socket);
            }
            else
            {
                $errortext='Failed to create locally a socket. Reason is: '.socket_strerror ($socket);
            }

            list($usec, $sec) = explode(" ", microtime());
            $micro_end_time=((float)$usec + (float)$sec);
            $end_time=((float)$sec);

            $delay=($micro_end_time-$micro_start_time);

            if ($errortext)
            {
                $value1='U';
                $value2=max(round($object['max']),1);
                $nbok++;
                print dol_print_date($end_time,'dayhourlog').' '.$errortext;
            }
            else
            {
                $value1=max(round($delay*1000),1);
                $value2='U';
                $nbok++;
                $errortext='';
            }

            $done=1;
        }

        if ($object['typeprot'] == 'SOAP' && preg_match('/^http/i',$object['url']))
        {
            list($usec, $sec) = explode(" ", microtime());
            $micro_start_time=((float)$usec + (float)$sec);

            if ($client)
            {
                try
                {
                    $args=array();
                    $ops=array('soapaction' => 'http://www.Nanonull.com/TimeService/getVersions');
                    $result = $client->__soapCall('getVersions',$args,$ops);
                }
                catch(Exception $e)
                {
                    $errortext='Failed to create locally a client WS. Reason is '.$e->getMessage();
                }
            }
            else
            {
                $errortext='Failed to create locally a client WS.';
            }


            list($usec, $sec) = explode(" ", microtime());
            $micro_end_time=((float)$usec + (float)$sec);
            $end_time=((float)$sec);

            $delay=($micro_end_time-$micro_start_time);

            if ($errortext)    // Test with no response
            {
                $value1='U';
                $value2=max(round($object['max']),1);
                $nbko++;
            }
            else
            {
                var_dump($result);
                if (preg_match('/'.preg_quote($object['checkkey']).'/',$result))
                {
                    // Test ok
                    $value1=max(round($delay*1000),1);
                    $value2='U';
                    $nbok++;
                    $errortext='';
                }
                else
                {   // Test ko
                    $value1='U';
                    $value2=max(round($object['max']),1);
                    $nbko++;
                    $errortext='Failed to find string "'.$object['checkkey'].'" into reponse string.\nResponse string is '.$result;
                }
            }

            //curl_close ($ch); unset($ch);

            $done=1;

        }

        // If no protocol found
        if (! $done)
        {
            $value1='U';
            $value2=round($object['max']);
            $nbko++;
            $errortext='Url of probe has a not supported protocol';

            list($usec, $sec) = explode(" ", microtime());
            $micro_start_time=$micro_end_time=((float)$usec + (float)$sec);
            $delay=0;
            $end_time=((float)$sec);
        }

        // Manage result
        $newstatus=(empty($errortext)?1:-1);

        // Update RRD file
        $ret = rrd_update($fname, $stringupdate);
        if ($ret <= 0)
        {
            $nbko++;
            $error++;
            $err = rrd_error($fname);
            $mesg="Update error: ".$err;
            $errortext='Failed to update RRD file.\nRRD functions returns: '.$err;
        }

        print dol_print_date($end_time,'dayhourlog').' Probe id='.$object['code'].' prot='.$object['typeprot'].' loop='.$nbloop.': '.$micro_start_time.'-'.$micro_end_time.'='.$delay.' -> '.($newstatus==1?'OK':'KO').' -> '.$value1.':'.$value2." - now we wait ".$object['frequency']."\n";
        $stringupdate='N:'.$value1.':'.$value2;

        // Update database if status has changed
        if ($object['status'] != $newstatus)
        {
            print dol_print_date($end_time,'dayhourlog').' Status change for probe '.$object['code'].' old='.$object['status'].' new='.$newstatus."\n";
            if (! $newstatus == -1 || ! empty($object['oldesterrortext'])) $errortext='';
            if ($errortext) print dol_print_date($end_time,'dayhourlog').' We also set a new error text'."\n";

            $probestatic=new Monitoring_probes($db);
            $probestatic->id=$object['code'];
            $result=$probestatic->updateStatus($newstatus,$end_time,$errortext);
            if ($result > 0)
            {
                $object['status']=$newstatus;
                $object['lastreset']=$end_time;
                if ($errortext) $object['oldesterrortext']=$errortext;
            }
            else
            {
                print dol_print_date($end_time,'dayhourlog').' Error to update database: '.$probestatic->error."\n";
            }
            unset($probestatic);
        }

        // Add delay
        sleep($object['frequency']);
    }

    return array('nbok'=>$nbok,'nbko'=>$nbko);
}

