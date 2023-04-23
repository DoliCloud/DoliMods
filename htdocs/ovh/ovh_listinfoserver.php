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

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

dol_include_once('/ovh/class/ovhserver.class.php');
dol_include_once("/ovh/lib/ovh.lib.php");
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP

require __DIR__ . '/includes/autoload.php';
use \Ovh\Api;
use GuzzleHttp\Client as GClient;


$langs->load("ovh@ovh");
$langs->load("sms");

$error=0;

// Get parameters
$socid = (GETPOST('id', 'int') ? GETPOST('id', 'int') : GETPOST('socid', 'int'));
// For backward compatibility
$action = GETPOST('action', 'aZ09');
$sendto = GETPOST("sendto") ? GETPOST('sendto') : '';

$project = GETPOST('project', 'aZ09');
$mode = GETPOST('mode', 'aZ09');
if (empty($mode)) $mode='publiccloud';
$server = GETPOST('server', 'aZ09');

// for bandwitch stats
if (!empty($_GET['type'])) {
	$type = $_GET['type'];
} else {
	$type = 'day';
}

// Protection if external user
if ($user->socid > 0) accessforbidden();

if (empty($user->rights->ovh->sysadmin)) accessforbidden();

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;    // Can be "soyoustart-eu" or "kimsufi-eu"

$WS_DOL_URL = ! empty($conf->global->OVHSMS_SOAPURL) ? strval($conf->global->OVHSMS_SOAPURL) : '';
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$login = ! empty($conf->global->OVHSMS_NICK) ? strval($conf->global->OVHSMS_NICK) : '';
$password = ! empty($conf->global->OVH_SMS_PASS) ? strval($conf->global->OVH_SMS_PASS) : '';

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
$params=getSoapParams();
ini_set('default_socket_timeout', $params['response_timeout']);



/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin) {
	//$result=dolibarr_set_const($db, "PAYBOX_IBS_DEVISE",$_POST["PAYBOX_IBS_DEVISE"],'chaine',0,'',$conf->entity);
	$result=dolibarr_set_const($db, "OVHSMS_NICK", $_POST["OVHSMS_NICK"], 'chaine', 0, '', $conf->entity);
	$result=dolibarr_set_const($db, "OVHSMS_PASS", $_POST["OVHSMS_PASS"], 'chaine', 0, '', $conf->entity);
	$result=dolibarr_set_const($db, "OVHSMS_SOAPURL", $_POST["OVHSMS_SOAPURL"], 'chaine', 0, '', $conf->entity);


	if ($result >= 0) {
		$mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
	} else {
		dol_print_error($db);
	}
}

if ($action == 'createsnapshot' && $user->admin) {
	$server=GETPOST('server', 'aZ09');
	$name=GETPOST('name', 'aZ09');

	$ovhserver=new OvhServer($db);
	$result = $ovhserver->createSnapshot($project, $server, $name);

	if ($result == 0) {
		setEventMessages($ovhserver->msg, null);
	} else {
		setEventMessages($ovhserver->error, $ovhserver->errors, 'errors');
	}

	$action='';
	$server='';
}


/*
 * View
 */

$form = new Form($db);

$morejs = '';
llxHeader('', $langs->trans('OvhServers'), '', '', '', '', $morejs, '', 0, 0);

$linkback='';

//print_fiche_titre($langs->trans('OvhServers'),$linkback,'setup');

print '<!-- OVH_OLDAPI = '.(isset($conf->global->OVH_OLDAPI) ? var_export($conf->global->OVH_OLDAPI, true) : '(unset)').' -->';

$head=ovhsysadmin_prepare_head();

dol_fiche_head($head, $mode, $langs->trans('OvhServers'), -1);

if (! empty($conf->global->OVH_OLDAPI) && (empty($conf->global->OVHSMS_NICK) || empty($WS_DOL_URL))) {
	echo '<div class="warning">'.$langs->trans("OvhSmsNotConfigured").'</div>';
} elseif (empty($conf->global->OVH_OLDAPI) && (empty($conf->global->OVHAPPKEY) || empty($conf->global->OVHAPPSECRET) || empty($conf->global->OVHCONSUMERKEY))) {
	echo '<div class="warning">'.$langs->trans("OvhAuthenticationPartNotConfigured").'</div>';
} else {
	if (! empty($conf->global->OVH_OLDAPI)) {
		$soap = new SoapClient($WS_DOL_URL, $params);
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
		} catch (Exception $e) {
			print '<div class="error">';
			print 'Error '.$e->getMessage().'<br>';
			print 'If there is an error to connect to OVH host, check your firewall does not block port required to reach OVH manager (for example port 443).<br>';
			print '</div>';
		}
	}

	if ($server) {
		if (! empty($conf->global->OVH_OLDAPI)) {
			$resultinfo = $soap->dedicatedInfo($session, $server);

			$resultrev = $soap->dedicatedReverseList($session, $server);

			$resultnetboot = $soap->dedicatedNetbootInfo($session, $server);

			//$resultcapa = $soap->dedicatedCapabilitiesGet($session, $server);
		} else {
			try {
				if ('guzzle7.3' == 'guzzle7.3') {
					$arrayconfig = array(
						'connect_timeout'=>(empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT),
						'timeout'=>(empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT)
					);
					$http_client = new GClient($arrayconfig);
				} else {
					$http_client = new GClient();
					$http_client->setDefaultOption('connect_timeout', empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT);  // Timeout by default of OVH is 5 and it is not enough
					$http_client->setDefaultOption('timeout', empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);
				}

				$conn = new Api($conf->global->OVHAPPKEY, $conf->global->OVHAPPSECRET, $endpoint, $conf->global->OVHCONSUMERKEY, $http_client);

				// Get servers list
				if ($mode == 'publiccloud') {
					$resultinfo = $conn->get('/cloud/project/'.$project.'/instance/'.$server);
					$resultinfo = json_decode(json_encode($resultinfo), false);

					$resultinfosnapshot = $conn->get('/cloud/project/'.$project.'/snapshot');
					$resultinfosnapshot = json_decode(json_encode($resultinfosnapshot), false);
				} else {
					$resultinfo = $conn->get('/dedicated/server/'.$server);
					$resultinfo = json_decode(json_encode($resultinfo), false);

					$resultinfo2 = $conn->get('/dedicated/server/'.$server.'/specifications/network');
					$resultinfo2 = json_decode(json_encode($resultinfo2), false);

					$resultrev = $conn->get('/ip/');
					$resultrev = json_decode(json_encode($resultrev), false);

					$resultnetboot = $conn->get('/dedicated/server/'.$server.'/boot/'.$resultinfo->bootId);
					$resultnetboot = json_decode(json_encode($resultnetboot), false);
				}

				/*$resultcapa = $conn->get('/ip/');
				$resultcapa = json_decode(json_encode($resultcapa), false);*/
			} catch (Exception $e) {
				setEventMessages($e->getMessage(), null, 'errors');
			}
		}

		$typesrv = substr($server, 0, 1);

		if ($mode == 'publiccloud') {
			$object = new OvhServer($db);
			$object->id = $server;
			$object->ref = $resultinfo->name;

			$linkback = '<a href="' . dol_buildpath('/ovh/ovh_listinfoserver.php', 1).'?mode=publiccloud' . '">' . $langs->trans("BackToList") . '</a>';

			dol_banner_tab($object, 'ref', $linkback, 1, 'none');

			print '<div class="fichecenter">';
			print '<div class="underbanner clearboth"></div>';
			print '<table class="border centpercent">'."\n";

			print '<tr><td class="titlefield">'.$langs->trans("Id").'</td><td class="wrap wordbreak"> ' . $server . '</td></tr>';
			print '<tr><td>'.$langs->trans("Ref").'</td><td>' . $resultinfo->name . '</td></tr>';
			print '<tr><td>'.$langs->trans("Created").'</td><td>' . $resultinfo->created . '</td></tr>';
			print '<tr><td>'.$langs->trans("Region").'</td><td>' . $resultinfo->flavor->region . '</td></tr>';
			print '<tr><td>'.$langs->trans("Type").'</td><td>' . $resultinfo->flavor->name . '</td></tr>';
			print '<tr><td>'.$langs->trans("OS").'</td><td>' . $resultinfo->flavor->osType . '</td></tr>';
			print '<tr><td>'.$langs->trans("Created").'</td><td>' . $resultinfo->created . '</td></tr>';
			print '<tr><td>'.$langs->trans("Vcpus").'</td><td>' . $resultinfo->flavor->vcpus . '</td></tr>';
			print '<tr><td>'.$langs->trans("Disk").'</td><td>' . $resultinfo->flavor->disk . '</td></tr>';
			print '<tr><td>'.$langs->trans("Ram").'</td><td>' . $resultinfo->flavor->ram . '</td></tr>';
			print '<tr><td>'.$langs->trans("inboundBandwidth").'</td><td>' . $resultinfo->flavor->inboundBandwidth . '</td></tr>';
			print '<tr><td>'.$langs->trans("outboundBandwidth").'</td><td>' . $resultinfo->flavor->outboundBandwidth . '</td></tr>';
			print '<tr><td>'.$langs->trans("SSHKey").'</td><td class="wrap wordbreak">' . dol_trunc($resultinfo->sshKey->publicKey, 80, 'middle') . '</td></tr>';
			print '<tr><td>'.$langs->trans("monthlyBilling").'</td><td>' . $resultinfo->monthlyBilling->status . ' ('.$resultinfo->monthlyBilling->since.')</td></tr>';
			print '<tr><td>'.$langs->trans("IPAddresses").'</td><td>';
			if (is_array($resultinfo->ipAddresses)) {
				foreach ($resultinfo->ipAddresses as $val) {
					print '* '.$val->ip.' ('.$val->type.' '.$val->version.')<br>';
					print $langs->trans("GatewayIp").' '.$val->gatewayIp;
					print '<br>';
				}
			}
			print '</td></tr>';
			print '</table>';

			print '</div>';

			print '<br>';

			//var_dump($resultinfosnapshot);
			print '<div class="div-table-responsive-no-min">';
			print '<table class="noborder centpercent">';
			print '<tr><td class="titlefield">'.$langs->trans("Snapshot").'</td><td>'.$langs->trans("Date").'</td><td>'.$langs->trans("Region").'</td><td>'.$langs->trans("Size").'</td></tr>';
			foreach ($resultinfosnapshot as $val) {
				 print '<tr>';
				 print '<td>';
				 print $val->name;
				 print '</td>';
				 print '<td>';
				 print $val->creationDate;
				 print '</td>';
				 print '<td>';
				 print $val->region;
				 print '</td>';
				 print '<td>';
				 print $val->size;
				 print '</td>';
				 print '</tr>';
			}
			print '</table>';
			print '</div>';
		} else // mode = dedicated
		{
			$object = new OvhServer($db);
			$object->id = $server;
			$object->ref = empty($resultinfo->name)?$server:$resultinfo->name;

			$linkback = '<a href="' . dol_buildpath('/ovh/ovh_listinfoserver.php', 1).'?mode=dedicated' . '">' . $langs->trans("BackToList") . '</a>';

			dol_banner_tab($object, 'ref', $linkback, 1, 'none');

			$reverse1 = gethostbyname($server);
			$reverse = gethostbyaddr($reverse1);

			print '<div class="fichecenter">';
			print '<div class="underbanner clearboth"></div>';
			print '<table class="border centpercent">'."\n";

			print '<tr><td class="titlefield">'.$langs->trans("Server").'</td><td> ' . $server . '</td></tr>';

			print '<tr><td>Reverse </td><td>  ' . $reverse . '</td></tr>';
			print '<tr><td>NetBoot </td><td>  ';
			if ($resultnetboot->kernel == 'hd') {
				print 'Hard Drive';
			} else {
				print $resultnetboot->kernel;
			}
			print '</td></tr>';

			print '<tr><td>Datacenter </td><td> ';
			$data = $resultinfo->datacenter;

			if ($data == 'p19') {
				print 'Paris';
			} elseif ($data == 'rbx') {
				print 'Roubaix';
			} elseif ($data == 'rbx2') {
				print 'Roubaix 2';
			} elseif ($data == 'rbx3') {
				print 'Roubaix 3';
			} else print $data;

			print '</td></tr>';
			print '<tr><td>Rack </td><td> ' . $resultinfo->rack . '</td></tr>';
			print '<tr><td>Distribution </td><td> ' . $resultinfo->os . '</td></tr>';
			print '<tr><td>IP</td><td> ' . gethostbyname($server) . '</td></tr>';
			print '<tr><td>Rescue email</td><td> ' . $resultinfo->rescueMail . '</td></tr>';
			print '</table>';

			print '</div>';


			if (! empty($conf->global->OVH_OLDAPI)) {
				/*
				 * Network infos
				 */
				print '<table class="border" width="100%;">';
				print '<tr><td class="titlefield liste_titre">';
				print '<strong>'.$langs->trans("Network").'</strong> </td><td></td></tr>';

				print '<tr><td>Ovh to Ovh </td><td> ';
				if ($resultinfo->network->bandwidthOvhToOvh == 100000) {
					print '100 Mbps';
				} else {
					if ($resultinfo->network->bandwidthOvhToOvh == 1000000) {
						print '1 Gbps';
					} else {
						print $resultinfo->network->bandwidthOvhToOvh . ' Kbps ';
					}
				}
				print '</td></tr>';
				print '<tr><td>Ovh to Internet </td><td> ';
				if ($resultinfo->network->bandwidthOvhToInternet == 100000) {
					print '100 Mbps';
				} else {
					if ($resultinfo->network->bandwidthOvhToInternet == 10000000) {
						print '1 Gbps';
					} else {
						print $resultinfo->network->bandwidthOvhToInternet . ' Kbps ';
					}
				}
				print '</td></tr>';
				print '<tr><td>Internet to Ovh </td><td>';
				if ($resultinfo->network->bandwidthInternetToOvh == 100000) {
					print '100 Mbps';
				} else {
					if ($resultinfo->network->bandwidthInternetToOvh == 1000000) {
						print '1 Gbps';
					} else {
						print $resultinfo->network->bandwidthInternetToOvh . ' Kbps ';
					}
				}
				print '</td></tr>';
				print '</table>';
			}
		}


		if (! empty($conf->global->OVH_OLDAPI)) {
			$lasteupdate = $resultinfo->network->traffic->lastUpdate;
			if (!empty($lasteupdate)) {
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

			while ($resultinfo->network->interfaces[$i]) {
				if ($i == 0) {
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


		if (! empty($conf->global->OVH_OLDAPI)) {
			print '<br><br>';
			print '<div><a href="?server=' . $server . '&type=day">'.$langs->trans("Day").'</a> / <a href="?server=' . $server . '&type=week">'.$langs->trans("Week").'</a> / <a href="?server=' . $server . '&type=month">'.$langs->trans("Month").'</a> / <a href="?server=' . $server . '&type=year">'.$langs->trans("Year").'</a></div>';

			$ip = gethostbyname($server);
			$result = $soap->dedicatedMrtgInfo($session, $server, 'traffic', $type, $ip);
			print '<img src="' . $result->image . '"><br>';

			$image = $result->image;
			if (empty($image)) {
				print 'vide';
			}

			print 'Out Max : ' . $result->max->out . ' Moy : ' . $result->average->out . ' Cur : ' . $result->current->out . '<br>';
			print 'In Max : ' . $result->max->in . ' Moy : ' . $result->average->in . ' Cur : ' . $result->current->out;
		}
	} else {
		$titlekey="ListOfDedicatedServers";
		if ($mode == 'publiccloud') $titlekey="ListOfPublicCloudServers";

		print_fiche_titre($langs->trans($titlekey), "", "");

		print '<br>';

		//dedicatedList
		if (! empty($conf->global->OVH_OLDAPI)) {
			$resultofproject = array(1);
		} else {
			if ('guzzle7.3' == 'guzzle7.3') {
				$arrayconfig = array(
					'connect_timeout'=>(empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT),
					'timeout'=>(empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT)
				);
				$http_client = new GClient($arrayconfig);
			} else {
				$http_client = new GClient();
				$http_client->setDefaultOption('connect_timeout', empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?20:$conf->global->MAIN_USE_CONNECT_TIMEOUT);  // Timeout by default of OVH is 5 and it is not enough
				$http_client->setDefaultOption('timeout', empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?30:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);
			}

			// Get servers list
			$conn = new Api(
				$conf->global->OVHAPPKEY,
				$conf->global->OVHAPPSECRET,
				$endpoint,
				$conf->global->OVHCONSUMERKEY,
				$http_client);

			if ($mode == 'publiccloud') {
				$resultofproject = $conn->get('/cloud/project');
			} else {
				$resultofproject = array(1);
			}
		}

		$tableshown = 0;

		if (count($resultofproject)) {
			foreach ($resultofproject as $projectname) {
				$result = null;

				//dedicatedList
				if (! empty($conf->global->OVH_OLDAPI)) {
					$result = $soap->dedicatedList($session);
				} else {
					if ($mode == 'publiccloud') {
						if ($projectname) {
							$result = $conn->get('/cloud/project/'.$projectname.'/instance', array('region' => null));
						}
					} else {
						$result = $conn->get('/dedicated/server/');
					}
				}

				if (count($result)) {
					if (empty($tableshown)) {
						print '<div class="div-table-responsive-no-min">';
						print '<table class="noborder tableovh centpercent">';
						$tableshown = 1;
					}

					foreach ($result as $serverobj) {
						if ($mode == 'publiccloud') {
							$ovhserver=new OvhServer($db);
							$ovhserver->id = $serverobj['id'];
							$ovhserver->ref = $serverobj['name'];
							$ovhserver->projectname = $projectname;
							$ovhserver->status = $serverobj['status'];

							print '<tr class="oddeven">';
							print '<td>';
							print $ovhserver->getNomUrl(1);
							print '<br>'.$serverobj['region'];
							print ' - '.$serverobj['id'];
							print '</td>';
							print '<td>OVH Project: '.$projectname.'</td>';
							print '<td>';
							if (is_array($serverobj['ipAddresses'])) {
								foreach ($serverobj['ipAddresses'] as $val) {
									print '* '.$val['ip'].' ('.$val['type'].' '.$val['version'].')<br>';
									print $langs->trans("GatewayIp").' '.$val['gatewayIp'];
									print '<br>';
								}
							}
							print '</td>';
							print '<td class="center">';
							print $ovhserver->getLibStatut(5);
							print '</td>';
							print '<td class="center">';
							print '<a href="?mode=publiccloud&server=' . $serverobj['id'] . '&project='.$projectname.'&action=createsnapshot&name='.urlencode($serverobj['name']).'">'.$langs->trans("CreateSnapshot").'</a>';
							print '</td>';
							print '</tr>';
						} else // dedicated
						{
							print '<tr class="oddeven">';
							print '<td><a href="?mode=dedicated&server=' . $serverobj . '">' . img_object('', 'server.svg@ovh', 'class="classfortooltip"') . ' ' .$serverobj . '</a></td>';
							print '</tr>';
						}
					}
				} else {
					print $langs->trans("None");
				}
			}
		} else {
			print $langs->trans("None");
		}

		if (! empty($tableshown)) {
			print '</table>';
			print '</div>';
		}
	}

	//logout
	if (! empty($conf->global->OVH_OLDAPI)) $soap->logout($session);
}


// End of page
llxFooter();

$db->close();
