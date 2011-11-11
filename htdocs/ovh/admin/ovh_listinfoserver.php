<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-Francois FERRY  <jfefe@aternatik.fr>
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
 */

/**
 *   \file       htdocs/ovh/admin/ovh_listinfoserver.php
 *	 \ingroup    ovh
 *	 \brief
 *   \version    $Id: sms.php,v 1.8 2009/03/09 11:28:12 eldy Exp $
 */
$res=0;
if (! $res && file_exists($path."../../main.inc.php")) $res=@include($path."../../main.inc.php");
if (! $res && file_exists($path."../../htdocs/main.inc.php")) $res=@include($path."../../htdocs/main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main.inc.php fails");
dol_include_once('/ovh/class/ovh.class.php');
require_once (DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php');
require_once (DOL_DOCUMENT_ROOT . '/lib/company.lib.php');
require_once (DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php');

$langs->load("ovh@ovh");

$error=0;

// Get parameters
$socid = (GETPOST('id') ? GETPOST('id') : GETPOST('socid'));
// For backward compatibility
$action = GETPOST('action');
$sendto = GETPOST("sendto") ? GETPOST('sendto') : '';

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

/*
 * Actions
 */



/*
 * View
 */

$login = $conf->global-> OVHSMS_NICK;
$password = $conf->global->OVH_SMS_PASS;
try {
    require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
    $params=getSoapParams();
    ini_set('default_socket_timeout', $params['response_timeout']);

    if (empty($conf->global->OVHSMS_SOAPURL))
    {
        print 'Error: '.$langs->trans("ModuleSetupNotComplete")."\n";
        exit;
    }
    echo "Wait...";

    $soap = new SoapClient($conf->global->OVHSMS_SOAPURL,$params);
    
	//login
	$session = $soap->login("$login", "$password", "fr", false);
}
catch(SoapFault $fault)
{
	print $fault;
	exit ;
}

$morejs = '';
llxHeader('', 'OvhDedie', '', '', '', '', $morejs, '', 0, 0);

$serveur = GETPOST('server');

if ($serveur)
{

	$resultinfo = $soap->dedicatedInfo($session, $serveur);

	$resultrev = $soap->dedicatedReverseList($session, $serveur);

	$resultnetboot = $soap->dedicatedNetbootInfo($session, $serveur);

	$resultcapa = $soap->dedicatedCapabilitiesGet($session, $serveur);

	$typesrv = substr($serveur, 0, 1);

	print_fiche_titre("Détails d'un serveur OVH");

	print '<table width="80%;">';
	print '<tr><td valign="top" width="50%">';

	print '<table width="100%;">';
	print '<tr><td  class="liste_titre" colspan="2">';
	print '<strong>Recapitulatif</strong> </td></tr>';
	print '<tr><td>Serveur </td><td> ' . $serveur . '</td></tr>';

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
	print '<tr><td>Distribution </td><td> ' . $resultinfo->os . '</td></tr>';
	print '<tr><td>IP Principale</td><td> ' . gethostbyname($serveur) . '</td></tr>';
	print '</table>';
	

	/*
	 * Network infos
	 */
	print '<table width="100%;">';
	print '<tr><td  class="liste_titre" colspan="2">';
	print '<strong>Reseaux</strong> </td></tr>';

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

	print '</table>';
	
	
	print '</td>';

	print '<td valign="top"><font size="2">';

	$lasteupdate = $resultinfo->network->traffic->lastUpdate;
	if (!empty($lasteupdate))
	{
		print '<table width="100%;">';
		print '<tr><td  class="liste_titre" colspan="2">';
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
	print 'Vous possedez ' . $nb . ' IP Failover';
	
	
	print '</td></tr></table>';

	print_titre("Monitoring");
	print '<div><a href="?server=' . $serveur . '&type=day">Jour</a> / <a href="?server=' . $serveur . '&type=week">Semaine</a> / <a href="?server=' . $serveur . '&type=month">Mois</a> / <a href="?server=' . $serveur . '&type=year">Annee</a></div>';

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
else
{
	print_fiche_titre("Liste des serveurs");
	//dedicatedList
	$result = $soap->dedicatedList($session);

	print '<ul>';
	foreach ($result as $dedie)
	{
		print '<li><a href="?server=' . $dedie . '">"' . $dedie . '</a>';
	}
	print '<§ul>';

}

//logout
$soap->logout($session);

// End of page
$db->close();
llxFooter('');
?>
