<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-Francois FERRY  <jfefe@aternatik.fr>
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
 * https://www.ovh.com/fr/soapi-to-apiv6-migration/
 */

/**
 *   \file      htdocs/ovh/admin/ovh_listinfoserver.php
 *	 \ingroup   ovh
 *	 \brief		Setup page to edit dedicated ovh servers
 */
$res=0;
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
dol_include_once('/ovh/class/ovh.class.php');
dol_include_once("/ovh/lib/ovh.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php');
require_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP

require __DIR__ . '/includes/autoload.php';
use \Ovh\Api;
use GuzzleHttp\Client as GClient;


$langs->load("ovh@ovh");
$langs->load("sms");

$error=0;

// Get parameters
$socid = (GETPOST('id','int') ? GETPOST('id','int') : GETPOST('socid','int'));
// For backward compatibility
$action = GETPOST('action');
$sendto = GETPOST("sendto") ? GETPOST('sendto') : '';

$project = GETPOST('project','aZ09');
$mode = GETPOST('mode','aZ09');
if (empty($mode)) $mode='dedicated';

// for bandwitch stats
if (!empty($_GET['type']))
{
	$type = $_GET['type'];
}
else
{
	$type = 'day';
}

// Protection if external user
if ($user->societe_id > 0) accessforbidden();

if (empty($user->rights->ovh->sysadmin)) accessforbidden();

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;


/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin)
{
    //$result=dolibarr_set_const($db, "PAYBOX_IBS_DEVISE",$_POST["PAYBOX_IBS_DEVISE"],'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSMS_NICK",$_POST["OVHSMS_NICK"],'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSMS_PASS",$_POST["OVHSMS_PASS"],'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSMS_SOAPURL",$_POST["OVHSMS_SOAPURL"],'chaine',0,'',$conf->entity);


    if ($result >= 0)
    {
        $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
    }
    else
    {
        dol_print_error($db);
    }
}



/*
 * View
 */

$WS_DOL_URL = $conf->global->OVHSMS_SOAPURL;
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$login = $conf->global->OVHSMS_NICK;
$password = $conf->global->OVH_SMS_PASS;

$morejs = '';
llxHeader('', $langs->trans('OvhServers'), '', '', '', '', $morejs, '', 0, 0);

$linkback='';

print_fiche_titre($langs->trans('OvhServers'),$linkback,'setup');

print '<!-- OVH_OLDAPI = '.$conf->global->OVH_OLDAPI.' -->';

$head=ovhsysadmin_prepare_head();

dol_fiche_head($head, $mode, '', -1);

if (! empty($conf->global->OVH_OLDAPI) && (empty($conf->global->OVHSMS_NICK) || empty($WS_DOL_URL)))
{
    echo '<div class="warning">'.$langs->trans("OvhSmsNotConfigured").'</div>';
}
elseif (empty($conf->global->OVH_OLDAPI) && (empty($conf->global->OVHAPPKEY) || empty($conf->global->OVHAPPSECRET) || empty($conf->global->OVHCONSUMERKEY)))
{
    echo '<div class="warning">'.$langs->trans("OvhAuthenticationPartNotConfigured").'</div>';
}
else
{
    $serveur = GETPOST('server');

    require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
    $params=getSoapParams();
    ini_set('default_socket_timeout', $params['response_timeout']);

    if (! empty($conf->global->OVH_OLDAPI))
    {
        $soap = new SoapClient($WS_DOL_URL,$params);
        try {
            $language="en";
            $multisession=false;

            //login
            $session = $soap->login($conf->global->OVHSMS_NICK, $conf->global->OVHSMS_PASS, $language, $multisession);
            //if ($session) print '<div class="ok">'.$langs->trans("OvhSmsLoginSuccessFull").'</div><br>';
            if (! $session) print '<div class="error">Error login did not return a session id</div><br>';

            //logout
            //$soap->logout($session);
            //  echo "logout successfull\n";

        }
        catch(Exception $e)
        {
            print '<div class="error">';
            print 'Error '.$e->getMessage().'<br>';
            print 'If this is an error to connect to OVH host, check your firewall does not block port required to reach OVH manager (for example port 1664).<br>';
            print '</div>';
        }
    }

    if ($serveur)
    {
        if (! empty($conf->global->OVH_OLDAPI))
        {
        	$resultinfo = $soap->dedicatedInfo($session, $serveur);

        	$resultrev = $soap->dedicatedReverseList($session, $serveur);

        	$resultnetboot = $soap->dedicatedNetbootInfo($session, $serveur);

        	//$resultcapa = $soap->dedicatedCapabilitiesGet($session, $serveur);
        }
        else
        {
            try
            {
                $http_client = new GClient();
                $http_client->setDefaultOption('connect_timeout', empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT);  // Timeout by default of OVH is 5 and it is not enough
                $http_client->setDefaultOption('timeout', empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);

                // Get servers list
                $conn = new Api($conf->global->OVHAPPKEY, $conf->global->OVHAPPSECRET, $endpoint, $conf->global->OVHCONSUMERKEY, $http_client);

                if ($mode == 'publiccloud')
                {
                    $resultinfo = $conn->get('/cloud/project/'.$project.'/instance/'.$serveur);
                    $resultinfo = json_decode(json_encode($resultinfo), false);
                }
                else
                {
                    $resultinfo = $conn->get('/dedicated/server/'.$serveur);
                    $resultinfo = json_decode(json_encode($resultinfo), false);

                    $resultinfo2 = $conn->get('/dedicated/server/'.$serveur.'/specifications/network');
                    $resultinfo2 = json_decode(json_encode($resultinfo2), false);

                    $resultrev = $conn->get('/ip/');
                    $resultrev = json_decode(json_encode($resultrev), false);

                    $resultnetboot = $conn->get('/dedicated/server/'.$serveur.'/boot/'.$resultinfo->bootId);
                    $resultnetboot = json_decode(json_encode($resultnetboot), false);
                }

                /*$resultcapa = $conn->get('/ip/');
                $resultcapa = json_decode(json_encode($resultcapa), false);*/
            }
            catch(Exception $e)
            {
                setEventMessages($e->getMessage(), null, 'errors');
            }
        }

    	$typesrv = substr($serveur, 0, 1);

    	print_fiche_titre($serveur,'','');

    	print '<table class="border" width="100%;">';
    	print '<tr><td class="titlefield liste_titre">';
    	print '<strong>'.$langs->trans("Summary").'</strong> </td><td class="liste_titre"></td></tr>';
    	print '<tr><td>'.$langs->trans("Server").'</td><td> ' . $serveur . '</td></tr>';

    	if ($mode == 'publiccloud')
    	{
            var_dump($resultinfo);

    	}
    	else
    	{
        	$reverse1 = gethostbyname($serveur);
        	$reverse = gethostbyaddr($reverse1);
        	print '<tr><td>Reverse </td><td>  ' . $reverse . '</td></tr>';
        	print '<tr><td>NetBoot </td><td>  ';
        	if ($resultnetboot->kernel == 'hd')
        	{
        		print 'Hard Drive';
        	}
        	else
        	{
        		print $resultnetboot->kernel;
        	}
        	print '</td></tr>';

        	print '<tr><td>Datacenter </td><td> ';
        	$data = $resultinfo->datacenter;

        	if ($data == 'p19')
        	{
        		print 'Paris';
        	}
        	else if ($data == 'rbx')
        	{
        		print 'Roubaix';
        	}
        	else if ($data == 'rbx2')
        	{
        		print 'Roubaix 2';
        	}
        	else if ($data == 'rbx3')
        	{
        		print 'Roubaix 3';
        	}
        	else print $data;

        	print '</td></tr>';
        	print '<tr><td>Rack </td><td> ' . $resultinfo->rack . '</td></tr>';
        	print '<tr><td>Distribution </td><td> ' . $resultinfo->os . '</td></tr>';
        	print '<tr><td>IP</td><td> ' . gethostbyname($serveur) . '</td></tr>';
        	print '<tr><td>Rescue email</td><td> ' . $resultinfo->rescueMail . '</td></tr>';
        	print '</table>';


        	/*
        	 * Network infos
        	 */
        	print '<table class="border" width="100%;">';
        	print '<tr><td class="titlefield liste_titre">';
        	print '<strong>'.$langs->trans("Network").'</strong> </td><td></td></tr>';

    		if (! empty($conf->global->OVH_OLDAPI))
    		{
    			print '<tr><td>Ovh to Ovh </td><td> ';
    		    if ($resultinfo->network->bandwidthOvhToOvh == 100000)
    		    {
    		        print '100 Mbps';
    		    }
    		    else
    		    {
    		        if ($resultinfo->network->bandwidthOvhToOvh == 1000000)
    		        {
    		            print '1 Gbps';
    		        }
    		        else
    		        {
    		            print $resultinfo->network->bandwidthOvhToOvh . ' Kbps ';
    		        }
    		    }
    		    print '</td></tr>';
    		    print '<tr><td>Ovh to Internet </td><td> ';
    		    if ($resultinfo->network->bandwidthOvhToInternet == 100000)
    		    {
    		        print '100 Mbps';
    		    }
    		    else
    		    {
    		        if ($resultinfo->network->bandwidthOvhToInternet == 10000000)
    		        {
    		            print '1 Gbps';
    		        }
    		        else
    		        {
    		            print $resultinfo->network->bandwidthOvhToInternet . ' Kbps ';
    		        }
    		    }
    		    print '</td></tr>';
    		    print '<tr><td>Internet to Ovh </td><td>';
    		    if ($resultinfo->network->bandwidthInternetToOvh == 100000)
    		    {
    		        print '100 Mbps';
    		    }
    		    else
    		    {
    		        if ($resultinfo->network->bandwidthInternetToOvh == 1000000)
    		        {
    		            print '1 Gbps';
    		        }
    		        else
    		        {
    		            print $resultinfo->network->bandwidthInternetToOvh . ' Kbps ';
    		        }
    		    }
    		    print '</td></tr>';
    		}
    		else
    		{
    		    print '<tr><td>Ovh to Ovh </td><td> ';
            	print $resultinfo2->bandwidth['OvhToOvh']['value'].' '.$resultinfo2->bandwidth['OvhToOvh']['unit'];
            	print '</td></tr>';
            	print '<tr><td>Ovh to Internet </td><td> ';
            	print $resultinfo2->bandwidth['OvhToInternet']['value'].' '.$resultinfo2->bandwidth['OvhToInternet']['unit'];
            	print '</td></tr>';
            	print '<tr><td>Internet to Ovh </td><td>';
            	print $resultinfo2->bandwidth['InternetToOvh']['value'].' '.$resultinfo2->bandwidth['InternetToOvh']['unit'];
            	print '</td></tr>';
    		}
    	}
    	print '</table>';


    	if (! empty($conf->global->OVH_OLDAPI))
    	{
        	$lasteupdate = $resultinfo->network->traffic->lastUpdate;
        	if (!empty($lasteupdate))
        	{
        		print '<table width="100%;">';
        		print '<tr><td class="liste_titre" colspan="2">';
        		print '<strong>Quota Reseau</strong> </td></tr>';
        		print '<tr><td>Last Update </td><td> ' . $resultinfo->network->traffic->lastUpdate . '</td></tr>';
        		print '<tr><td>Quota In </td><td> ' . $resultinfo->network->traffic->monthlyTraffic->in . '</td></tr>';
        		print '<tr><td>Quota Out </td><td> ' . $resultinfo->network->traffic->monthlyTraffic->out . '</td></tr>';
        		print '</table>';
        	}

        	print '<table width="100%;">';
        	print '<tr><td  class="liste_titre" colspan="2">';
        	print '<strong>Interfaces</strong> </td></tr>';
        	$i = 0;

        	while ($resultinfo->network->interfaces[$i])
        	{
        		if ($i == 0)
        		{
        			print '<tr><td>Switch </td><td> ' . $resultinfo->network->interfaces[$i]->switch . '</td></tr>';
        			print '<tr><td>Mac </td><td> ' . $resultinfo->network->interfaces[$i]->mac . '</td></tr>';
        			print '<tr><td>IP </td><td> ' . $resultinfo->network->interfaces[$i]->ip . '</td></tr>';
        		}

        		$i++;
        		$nb = $i - 1;
        	}

        	print '</table>';
        	print 'Vous possedez ' . ($nb ? $nb : 0) . ' IP Failover';
    	}


    	if (! empty($conf->global->OVH_OLDAPI))
    	{
    	   print '<br><br>';
    	   print '<div><a href="?server=' . $serveur . '&type=day">'.$langs->trans("Day").'</a> / <a href="?server=' . $serveur . '&type=week">'.$langs->trans("Week").'</a> / <a href="?server=' . $serveur . '&type=month">'.$langs->trans("Month").'</a> / <a href="?server=' . $serveur . '&type=year">'.$langs->trans("Year").'</a></div>';

        	$ip = gethostbyname($serveur);
        	$result = $soap->dedicatedMrtgInfo($session, $serveur, 'traffic', $type, $ip);
        	print '<img src="' . $result->image . '"><br>';

        	$image = $result->image;
        	if (empty($image))
        	{
        		print 'vide';
        	}

        	print 'Out Max : ' . $result->max->out . ' Moy : ' . $result->average->out . ' Cur : ' . $result->current->out . '<br>';
        	print 'In Max : ' . $result->max->in . ' Moy : ' . $result->average->in . ' Cur : ' . $result->current->out;
    	}
    }
    else
    {
        $titlekey="ListOfDedicatedServers";
        if ($mode == 'publiccloud') $titlekey="ListOfPublicCloudServers";

    	print_fiche_titre($langs->trans($titlekey),"","");


    	//dedicatedList
    	if (! empty($conf->global->OVH_OLDAPI))
    	{
    	   $result = $soap->dedicatedList($session);
    	}
    	else
    	{
            $http_client = new GClient();
            $http_client->setDefaultOption('connect_timeout', empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT);  // Timeout by default of OVH is 5 and it is not enough
            $http_client->setDefaultOption('timeout', empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);

    	    // Get servers list
        	$conn = new Api(
        	    $conf->global->OVHAPPKEY,
        	    $conf->global->OVHAPPSECRET,
        	    $endpoint,
        	    $conf->global->OVHCONSUMERKEY,
        	    $http_client);
        	if ($mode == 'publiccloud')
        	{
        	    $result = $conn->get('/cloud/project');

        	    if ($result[0])
        	    {
        	        $projectname=$result[0];
        	        $result = $conn->get('/cloud/project/'.$projectname.'/instance', array('region' => NULL));
        	    }
        	}
        	else
        	{
        	   $result = $conn->get('/dedicated/server/');
        	}

    	}

    	if (count($result))
    	{
        	print '<ul>';
        	foreach ($result as $serverobj)
        	{
        	    if ($mode == 'publiccloud')
        	    {
        	        print '<li><a href="?mode=publiccloud&server=' . $serverobj['id'] . '&project='.$projectname.'">' . $serverobj['name'] . '</a>';
        	    }
        	    else
        	    {
        	        print '<li><a href="?server=' . $serverobj . '">' . $serverobj . '</a>';
        	    }
        	}
        	print '</ul>';
    	}
    	else
    	{
    	     print $langs->trans("None");
    	}
    }

    //logout
    if (! empty($conf->global->OVH_OLDAPI)) $soap->logout($session);
}


// End of page
llxFooter();

$db->close();
