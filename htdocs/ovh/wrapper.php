<?php
/* Copyright (C) 2009-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/ovh/wrapper.php
 *  \brief      File that is entry point to call an OVH SIP server
 *  \version    $Id: wrapper.php,v 1.2 2011/03/03 09:12:04 eldy Exp $
 *	\remarks	To be used, you must have an OVH account
 */

//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC','1');
if (! defined('NOREQUIRETRAN'))   define('NOREQUIRETRAN','1');
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK','1');
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL','1');
if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX','1');

// C'est un wrapper, donc header vierge
function llxHeader() {
	print '<html>'."\n";
	print '<head>'."\n";
	print '<title>OVH redirection from Dolibarr...</title>'."\n";
	print '</head>'."\n";
}
function llxFooter() {
	print "\n".'</html>'."\n";
}


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/lib/functions.lib.php");
include_once(DOL_DOCUMENT_ROOT."/lib/functions2.lib.php");


// Security check
if (! $conf->clicktodial->enabled)
{
    accessforbidden();
    exit;
}



$login = $_GET['login'];
$password = $_GET['password'];
$caller = $_GET['caller'];
$called = $_GET['called'];

# Adresse IP du serveur OVH
$wsdlovh = 'https://www.ovh.com/soapi/soapi-re-1.14.wsdl';

#Delai d'attente avant de raccrocher
$strWaitTime = "30";
#Priority
$strPriority = "1";
#Nomber of try
$strMaxRetry = "2";


/*
 * View
 */

llxHeader();

$number=strtolower($called) ;
$pos=strpos ($number,"local");
if (! empty($number))
{
	if ($pos===false) :
	$errno=0 ;
	$errstr=0 ;
	$strCallerId = "Dolibarr <".strtolower($caller).">" ;

	try {
		$soap = new SoapClient($wsdlovh);
	 
		//telephonyClick2CallDo
	 	$soap->telephonyClick2CallDo($login, $password, $caller, $number, $caller);
	
		$txt="Call OVH SIP dialer for caller: ".$caller.", called: ".$called." clicktodiallogin: ".$login;
		dol_syslog($txt);
		print '<body onload="javascript:history.go(-1);">'."\n";
		print '<!-- '.$txt.' -->';
		fputs($oSocket, "Username: $login\r\n" ) ;
		fputs($oSocket, "Secret: $password\r\n\r\n" ) ;
		fputs($oSocket, "Caller: $caller\r\n" ) ;
		fputs($oSocket, "Called: ".$number."\r\n" ) ;
		sleep(2) ;
	    print '</body>'."\n";
	}
	catch(SoapFault $fault) 
	{
	 	echo $fault;
	}
	endif;
}
else {
    print 'Bad parameters in URL. Must be '.$_SERVER['PHP_SELF'].'?caller=99999&called=99999&login=xxxxx&password=xxxxx';
}

llxFooter();
