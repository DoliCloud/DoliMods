<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-François FERRY  <jfefe@aternatik.fr>
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
 *   	\file       htdocs/ovh/admin/ovh_setup.php
 *		\ingroup    ovh
 *		\brief      Setup of module OVH
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
dol_include_once("/ovh/class/ovhsms.class.php");
dol_include_once("/ovh/lib/ovh.lib.php");
require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;

// Load traductions files requiredby by page
$langs->load("admin");
$langs->load("companies");
$langs->load("ovh@ovh");
$langs->load("sms");

if (!$user->admin)
accessforbidden();
// Get parameters

$action=GETPOST('action');

// Protection if external user
if ($user->societe_id > 0)
{
    //accessforbidden();
}

$substitutionarrayfortest=array(
'__ID__' => 'TESTIdRecord',
'__LASTNAME__' => 'TESTLastname',
'__FIRSTNAME__' => 'TESTFirstname'
);


// Activate error interceptions
if (! empty($conf->global->MAIN_ENABLE_EXCEPTION))
{
    function traitementErreur($code, $message, $fichier, $ligne, $contexte)
    {
        if (error_reporting() & $code) {
            throw new Exception($message, $code);
        }
    }
    set_error_handler('traitementErreur');
}

//$urlexample='https://www.ovh.com/soapi/soapi-re-1.32.wsdl';
$urlexample='http://www.ovh.com/soapi/soapi-re-latest.wsdl';

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;



/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin)
{
    //$result=dolibarr_set_const($db, "PAYBOX_IBS_DEVISE",$_POST["PAYBOX_IBS_DEVISE"],'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSMS_NICK",trim(GETPOST("OVHSMS_NICK")),'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSMS_PASS",trim(GETPOST("OVHSMS_PASS")),'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHSMS_SOAPURL",trim(GETPOST("OVHSMS_SOAPURL")),'chaine',0,'',$conf->entity);

    $result=dolibarr_set_const($db, "OVHAPPNAME",trim(GETPOST("OVHAPPNAME")),'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHAPPDESC",trim(GETPOST("OVHAPPDESC")),'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHAPPKEY",trim(GETPOST("OVHAPPKEY")),'chaine',0,'',$conf->entity);
    $result=dolibarr_set_const($db, "OVHAPPSECRET",trim(GETPOST("OVHAPPSECRET")),'chaine',0,'',$conf->entity);
    
    if ($result >= 0)
    {
        $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
    }
    else
    {
        dol_print_error($db);
    }
}

if ($action == 'setvalue_account' && $user->admin)
{
    $result=dolibarr_set_const($db, "OVHSMS_ACCOUNT",$_POST["OVHSMS_ACCOUNT"],'chaine',0,'',$conf->entity);

    if ($result >= 0)
    {
        $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
    }
    else
    {
        dol_print_error($db);
    }
}

if ($action == 'requestcredential')
{
    // Informations about your application
    $applicationKey = $conf->global->OVHAPPKEY;
    $applicationSecret = $conf->global->OVHAPPSECRET;
    $redirect_uri=dol_buildpath('/ovh/admin/ovh_setup.php?action=backfromauth', 2);

    // Information about API and rights asked
    $rights = array(
        (object) ['method'    => 'GET', 'path'      => '/me*' ],        // This include /me/bill
        (object) ['method'    => 'GET', 'path'      => '/sms*' ],
        (object) ['method'    => 'GET', 'path'      => '/telephony*' ],
        (object) ['method'    => 'GET', 'path'      => '/dedicated/server*' ],
        (object) ['method'    => 'GET', 'path'      => '/ip*' ],
        (object) ['method'    => 'POST', 'path'      => '/sms*' ],
        (object) ['method'    => 'POST', 'path'      => '/telephony*' ],
    );
    /*
    $rights = array( (object) [
        'method'    => 'GET',
        'path'      => '/me*'
    ]);*/
    
    // Get credentials
    try {
        $conn = new Api($applicationKey, $applicationSecret, $endpoint);
        $credentials = $conn->requestCredentials($rights, $redirect_uri);
        
        $_SESSION['ovh_consumer_key']=$credentials["consumerKey"];
        header('location: '. $credentials["validationUrl"]);
    }
    catch(Exception $e)
    {
        setEventMessages($e->getMessage(), null, 'errors');
        $action='';
    }
}

if ($action == 'backfromauth' && ! empty($_SESSION["ovh_consumer_key"]))
{
    // Save 
    $result=dolibarr_set_const($db, "OVHCONSUMERKEY",$_SESSION["ovh_consumer_key"],'chaine',0,'',$conf->entity);

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

llxHeader('',$langs->trans('OvhSmsSetup'),'','');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans("OvhSmsSetup"),$linkback,'setup');

$head=ovhadmin_prepare_head();


print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';

if (empty($conf->global->OVH_NEWAPI))
{
    if (!extension_loaded('soap'))
    {
        print '<div class="error">'.$langs->trans("PHPExtensionSoapRequired").'</div>';
    }
}

$var=true;

dol_fiche_head($head, 'common', $langs->trans("Ovh"));

if (! empty($conf->global->OVH_NEWAPI))
{
    print $langs->trans("GoOnPageToCreateYourAPIKey", 'https://eu.api.ovh.com/createApp/', 'https://eu.api.ovh.com/createApp/').'<br>';
    print $langs->trans("ListOfExistingAPIApp", 'https://eu.api.ovh.com/console/#/me/api/application#GET', 'https://eu.api.ovh.com/console/#/me/api/application#GET').' (first log in on top right corner)<br><br>';
}

print '<table class="noborder" width="100%">';

if (empty($conf->global->OVH_NEWAPI))
{
    // Old API
    
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Parameter").'</td>';
    print '<td>'.$langs->trans("Value").'</td>';
    print '<td></td>';
    print "</tr>\n";
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td width="200px" class="fieldrequired">';
    print $langs->trans("OvhSmsNick").'</td><td>';
    print '<input size="64" type="text" name="OVHSMS_NICK" value="'.$conf->global->OVHSMS_NICK.'">';
    print '</td><td>'.$langs->trans("Example").': AA123-OVH';
    print '</td></tr>';
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("OvhSmsPass").'</td><td>';
    print '<input size="64" type="password" name="OVHSMS_PASS" value="'.$conf->global->OVHSMS_PASS.'">';
    print '</td><td></td></tr>';
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("OvhSmsSoapUrl").'</td><td>';
    print '<input size="64" type="text" name="OVHSMS_SOAPURL" value="'.$conf->global->OVHSMS_SOAPURL.'">';
    print '</td><td>'.$langs->trans("Example").': '.$urlexample;
    print '</td></tr>';
}
else
{
    // New API
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td width="200px" class="fieldrequired">';
    print $langs->trans("OvhApplicationName").'</td><td>';
    print '<input size="64" type="text" name="OVHAPPNAME" value="'.$conf->global->OVHAPPNAME.'">';
    print '</td><td>'.$langs->trans("Example").': My App';
    print '</td></tr>';
    
    /*
    $var=!$var;
    print '<tr '.$bc[$var].'><td>';
    print $langs->trans("OvhApplicationDescription").'</td><td>';
    print '<input size="64" type="text" name="OVHAPPDESC" value="'.$conf->global->OVHAPPDESC.'">';
    print '</td><td>'.$langs->trans("Example").': My App description';
    print '</td></tr>';
    */
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("OvhApplicationKey").'</td><td>';
    print '<input size="64" type="text" name="OVHAPPKEY" value="'.$conf->global->OVHAPPKEY.'">';
    print '</td><td>'.$langs->trans("Example").': Ld9GQ3AfaXDyZdsM';
    print '</td></tr>';
    
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("OvhApplicationSecret").'</td><td>';
    print '<input size="64" type="text" name="OVHAPPSECRET" value="'.$conf->global->OVHAPPSECRET.'">';
    print '</td><td>'.$langs->trans("Example").': V3dTtzY4PCMUYp2dURlGyIkI67C54S67';
    print '</td></tr>';
    
    
    if (! empty($conf->global->OVHAPPNAME) && ! empty($conf->global->OVHAPPKEY) && ! empty($conf->global->OVHAPPSECRET))
    {
        $var=!$var;
        print '<tr '.$bc[$var].'><td class="fieldrequired">';
        print $langs->trans("OvhConsumerkey").'</td><td>';
        print '<input size="64" type="text" name="OVHCONSUMERKEY" value="'.$conf->global->OVHCONSUMERKEY.'">';
        print '</td><td>';
        //if (empty($conf->global->OVHCONSUMERKEY))
        //{
            if (empty($conf->global->OVHCONSUMERKEY)) print img_warning().' ';
            print $langs->trans("ClickHereToLoginAndGetYourConsumerKey", $_SERVER["PHP_SELF"].'?action=requestcredential');
        //}
        print '</td></tr>';
    }    
}

print '</table>';

dol_fiche_end();

print '<div align="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';

print '</form>';



dol_htmloutput_mesg($mesg);



if (empty($conf->global->OVH_NEWAPI))
{
    $WS_DOL_URL = $conf->global->OVHSMS_SOAPURL;
    dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);
    
    if (empty($conf->global->OVHSMS_NICK) || empty($WS_DOL_URL))
    {
        echo '<br>'.'<div class="warning">'.$langs->trans("OvhSmsNotConfigured").'</div>';
    }
    else
    {
    	print '<br>';
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=test">'.$langs->trans("TestLoginToAPI").'</a><br><br>';
    
        if ($action == 'test')
        {
            require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
            $params=getSoapParams();
            ini_set('default_socket_timeout', $params['response_timeout']);
    
    		print $langs->trans("ConnectionParameters").':<br>';
            if ($params['proxy_use']) print $langs->trans("TryToUseProxy").': '.$params['proxy_host'].':'.$params['proxy_port'].($params['proxy_login']?(' - '.$params['proxy_login'].':'.$params['proxy_password']):'').'<br>';
            print 'URL: '.$WS_DOL_URL.'<br>';
            //print $langs->trans("ConnectionTimeout").': '.$params['connection_timeout'].'<br>';
            //print $langs->trans("ResponseTimeout").': '.$params['response_timeout'].'<br>';
    		$i=0;
    		foreach ($params as $key => $val)
    		{
    			$i++;
    			if ($i > 1) print ', ';
    			print $key.': '.($key == 'proxy_password'?preg_replace('/./','*',$val):$val);
    		}
    		print '<br><br>'."\n";
    
    		// Set error handler to trap FATAL errors
    		set_error_handler('my_error_handler');
    
    		try {
    			$soap = new SoapClient($WS_DOL_URL,$params);
    
            	$language="en";
                $multisession=false;
    
                //login
                $session = $soap->login($conf->global->OVHSMS_NICK, $conf->global->OVHSMS_PASS, $language, $multisession);
                if ($session) print '<div class="ok">'.$langs->trans("OvhSmsLoginSuccessFull").'</div><br>';
                else print '<div class="error">Error login did not return a session id</div><br>';
    
                //logout
                if (empty($conf->global->OVH_NEWAPI)) $soap->logout($session);
                //  echo "logout successfull\n";
    
            }
            catch(Exception $e)
            {
                print '<div class="error">';
                print 'Error '.$e->getMessage().'<br>';
                print 'If this is an error to connect to OVH host, check your firewall does not block port required to reach OVH manager (for example port 1664).<br>';
                print '</div>';
    
                // Write dump
    			if (@is_writeable($dolibarr_main_data_root))	// Avoid fatal error on fopen with open_basedir
    			{
    				if (! empty($conf->global->MAIN_SOAP_DEBUG))
    				{
    					print "\n";
    					var_dump($e);	// This provide more info than __get functions
    					$outputfile=$dolibarr_main_data_root."/dolibarr_soap.log";
    		            $fp = fopen($outputfile,"w");
    		            fputs($fp, 'Last SOAP header request:'."\n".$soap->__getLastRequestHeaders()."\n");
    		            fputs($fp, 'Last SOAP body request:'."\n".$soap->__getLastRequest()."\n");
    		            fputs($fp, 'Last SOAP header response:'."\n".$soap->__getLastResponseHeaders()."\n");
    		            fputs($fp, 'Last SOAP body response:'."\n".$soap->__getLastResponse()."\n");
    		            fclose($fp);
    		            if (! empty($conf->global->MAIN_UMASK))
    		            	@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
    				}
    			}
            }
        }
    
        print '<br>';
    }
}


// End of page

llxFooter();

$db->close();

/**
 * Function to trap FATAL errors
 *
 * @param string	$no
 * @param string	$str
 * @param string	$file
 * @param string	$line
 */
function my_error_handler($no,$str,$file,$line)
{
	$e = new ErrorException($str,$no,0,$file,$line);
	print $e;
}

