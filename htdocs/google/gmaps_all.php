<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       \file       htdocs/google/gmaps.php
 *       \ingroup    google
 *       \brief      Page to show a map
 *       \author     Laurent Destailleur
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/google/class/googlemaps.class.php");
dol_include_once("/google/includes/GoogleMapAPIv3.class.php");

$langs->load("google@google");

// url is:  gmaps.php?mode=thirdparty|contact|member&id=id&max=max


$mode=GETPOST('mode');
$id = GETPOST('id','int');
$MAXADDRESS=GETPOST('max','int')?GETPOST('max','int'):'25';	// Set packet size to 25 if no forced from url
$address='';

// Load third party
if (empty($mode) || $mode=='thirdparty')
{
	include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
	if ($id > 0)
	{
		$object = new Societe($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1,', ');
		$url = $object->url;
	}
}
else if ($mode=='contact')
{
	include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
	if ($id > 0)
	{
		$object = new Contact($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1,', ');
		$url = '';
	}
}
else if ($mode=='member')
{
	include_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');
	if ($id > 0)
	{
		$object = new Adherent($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1,', ');
		$url = '';
	}
}
else if ($mode=='patient')
{
	dol_include_once('/cabinetmed/class/patient.class.php');
	if ($id > 0)
	{
		$object = new Patient($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1,', ');
		$url = '';
	}
}
else
{
	dol_print_error('','Bad value for mode param into url');
	exit;
}


/*
 * View
 */

llxheader();

$form=new Form($db);

$content = "Default content";
$act = "";

//On fabrique les onglets
$head=array();
$title='';
$picto='';
$type='';
if (empty($mode) || $mode=='thirdparty')
{
	$socid = GETPOST('socid','int');
	if ($user->societe_id) $socid=$user->societe_id;

	$search_sale=empty($conf->global->GOOGLE_MAPS_FORCE_FILTER_BY_SALE_REPRESENTATIVES)?0:1;

	$title=$langs->trans("MapOfThirdparties");
	$picto='company';
	$type='company';
	$sql="SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town, s.url,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.libelle as country,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label";
	$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_pays as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	if ($search_sale || (!$user->rights->societe->client->voir && !$socid)) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	$sql.= " WHERE s.status = 1";
	$sql.= " AND s.entity IN (".getEntity('societe', 1).")";
	if ($search_sale || (! $user->rights->societe->client->voir && ! $socid))	$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
	if ($socid) $sql.= " AND s.rowid = ".$socid;	// protect for external user
	$sql.= " ORDER BY s.rowid";
}
else if ($mode=='contact')
{
	$title=$langs->trans("MapOfContactsAddresses");
	$picto='contact';
	$type='contact';
	$sql="SELECT s.rowid as id, s.lastname, s.firstname, s.address, s.zip, s.town, '' as url,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.libelle as country,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label";
	$sql.= " FROM ".MAIN_DB_PREFIX."socpeople as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_pays as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	$sql.= " WHERE s.entity IN (".getEntity('societe', 1).")";
	//$sql.= " WHERE s.status = 1";
	$sql.= " ORDER BY s.rowid";
}
else if ($mode=='member')
{
	$title=$langs->trans("MapOfMembers");
	$picto='user';
	$type='member';
	$sql="SELECT s.rowid as id, s.lastname, s.firstname, s.address, s.zip, s.town, '' as url,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.libelle as country,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label";
	$sql.= " FROM ".MAIN_DB_PREFIX."adherent as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_pays as c ON s.country = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	$sql.= " WHERE s.statut = 1";
	$sql.= " AND s.entity IN (".getEntity('adherent', 1).")";
	$sql.= " ORDER BY s.rowid";
}
else if ($mode=='patient')
{
	$title=$langs->trans("MapOfPatients");
	$picto='user';
	$type='patient';
	$sql="SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.libelle as country, s.url,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label";
	$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_pays as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	$sql.= " WHERE s.canvas='patient@cabinetmed'";
	$sql.= " AND s.entity IN (".getEntity('societe', 1).")";
	$sql.= " ORDER BY s.rowid";
}
//print $sql;

print_fiche_titre($title);

dol_fiche_head(array(), 'gmaps', '', 0);


// Fill array of contacts
$addresses = array();
$adderrors = array();
$googlemaps = new Googlemaps($db);
$countgeoencoding=0;
$countgeoencodedok=0;
$countgeoencodedall=0;

// Loop
dol_syslog("Search addresses sql=".$sql);
$resql=$db->query($sql);
if ($resql)
{
	$num=$db->num_rows($resql);
	$i=0;
	while ($i < $num)
	{
		$obj=$db->fetch_object($resql);
		if (empty($obj->country_code)) $obj->country_code=$mysoc->country_code;

		$error='';

		$addresstosearch=dol_format_address($obj,1," ");
		$address=dol_format_address($obj,1,", ");	// address to show

		$object=new stdClass();
		$object->id=$obj->id;
		$object->name=$obj->name?$obj->name:($obj->lastname.' '.$obj->firstname);
		$object->latitude = $obj->latitude;
		$object->longitude = $obj->longitude;
		$object->address = $address;
		$object->url = $obj->url;

		$geoencodingtosearch=false;
		if ($obj->gaddress != $addresstosearch) $geoencodingtosearch=true;
		else if ((empty($object->latitude) || empty($object->longitude)) && (empty($obj->result_code) || in_array($obj->result_code, array('OK','OVER_QUERY_LIMIT')))) $geoencodingtosearch=true;

		if ($geoencodingtosearch && (empty($MAXADDRESS) || $countgeoencoding < $MAXADDRESS))
		{
			if ($countgeoencoding && ($countgeoencoding % 10 == 0))
			{
				dol_syslog("Add a delay of 1");
				sleep(1);
			}

			$countgeoencoding++;

			$point = geocoding($addresstosearch);
			if (is_array($point))
			{
				$object->latitude=$point['lat'];
				$object->longitude=$point['lng'];

				// Update/insert database
				$googlemaps->id=$obj->gid;
				$googlemaps->latitude=$object->latitude;
				$googlemaps->longitude=$object->longitude;
				$googlemaps->address=$addresstosearch;
				$googlemaps->fk_object=$obj->id;
				$googlemaps->type_object=$type;
				$googlemaps->result_code='OK';
				$googlemaps->result_label='';

				if ($googlemaps->id > 0) $result=$googlemaps->update();
				else $result=$googlemaps->create($user);
				if ($result < 0) dol_print_error('',$googlemaps->error);

				$countgeoencodedok++;
				$countgeoencodedall++;
			}
			else
			{
				$error=$point;

				// Update/insert database
				$googlemaps->id=$obj->gid;
				$googlemaps->latitude=$object->latitude;
				$googlemaps->longitude=$object->longitude;
				$googlemaps->address=$addresstosearch;
				$googlemaps->fk_object=$obj->id;
				$googlemaps->type_object=$type;
				if ($error == 'ZERO_RESULTS')
				{
					$error='Address not complete or unknown';
					$googlemaps->result_code='ZERO_RESULTS';
					$googlemaps->result_label=$error;
				}
				else if ($error == 'OVER_QUERY_LIMIT')
				{
					$error='Quota reached';
					$googlemaps->result_code='OVER_QUERY_LIMIT';
					$googlemaps->result_label=$error;
				}
				else
				{
					$googlemaps->result_code='ERROR';
					$googlemaps->result_label=$error;
				}

				if ($googlemaps->id > 0) $result=$googlemaps->update();
				else $result=$googlemaps->create($user);
				if ($result < 0) dol_print_error('',$googlemaps->error);

				$object->error=$error;
				$adderrors[]=$object;

				$countgeoencodedall++;
			}
		}
		else
		{
			if ($obj->result_code == 'OK')	// A success
			{
				$countgeoencodedok++;
				$countgeoencodedall++;
			}
			else if (! empty($obj->result_code))	// An error
			{
				$error=$obj->result_label;
				$object->error=$error;
				$adderrors[]=$object;

				$countgeoencodedall++;
			}
			else 	// No geoencoding done yet
			{

			}
		}

		if (! $error)
		{
			$addresses[]=$object;
		}

		$i++;
	}

	// Summary of data represented
	if ($num > $countgeoencodedall) print $langs->trans("OnlyXAddressesAmongYWereGeoencoded",$MAXADDRESS,$countgeoencodedok).'<br>'."\n";
	print $langs->trans("CountGeoTotal",$num,($num-$countgeoencodedall),($countgeoencodedall-$countgeoencodedok),$countgeoencodedok).'<br>'."\n";
	if ($num > $countgeoencodedall)
	{
		print $langs->trans("ClickHereToIncludeXMore").': &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=25">'.$langs->trans("By25").'</a> &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=50">'.$langs->trans("By50").'</a> &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=100">'.$langs->trans("By100").'</a> &nbsp;';
		//,min($num-$countgeoencodedall,$MAXADDRESS)).'</a>';
		print '<br>';
	}
	print '<br>'."\n";
}
else
{
	dol_print_error($db);
}

$gmap = new GoogleMapAPI();
$gmap->setDivId('test1');
$gmap->setDirectionDivId('route');
$gmap->setEnableWindowZoom(true);
$gmap->setEnableAutomaticCenterZoom(true);
$gmap->setDisplayDirectionFields(false);
$gmap->setClusterer(true);
$gmap->setSize('100%','500px');
$gmap->setZoom(11);
$gmap->setLang('fr');
$gmap->setDefaultHideMarker(false);

// Convert array of addresses into the output gmap string 
$gmap->addArrayMarker($addresses, $langs, $mode);


$gmap->generate();
echo $gmap->getGoogleMap();


dol_fiche_end();


// If no addresses
if (count($addresses) == 0 && count($adderrors) == 0) print $langs->trans("NoAddressDefined").'<br><br>';


// Show error
if (count($adderrors))
{
	if (empty($mode) || $mode=='thirdparty') $objectstatic=new Societe($db);
	else if ($mode=='contact') $objectstatic=new Contact($db);
	else if ($mode=='member') $objectstatic=new Adherent($db);
	else if ($mode=='patient') $objectstatic=new Patient($db);

	print $langs->trans("FollowingAddressCantBeLocalized",($countgeoencodedall-$countgeoencodedok)).':<br>'."\n";
	foreach($adderrors as $object)
	{
		$objectstatic->id=$object->id;
		$objectstatic->name=$object->name;
		print $langs->trans("Name").": ".$objectstatic->getNomUrl(1).", ".$langs->trans("Address").": ".$object->address." -> ".$object->error."<br>\n";
	}
}


llxfooter();

$db->close();



/**
 * Geocoding an address (address -> lat,lng)
 *
 * @param 	string 	$address 	An address
 * @return 	mixed				Array(lat, lng) if OK, error message string if KO
 */
function geocoding($address)
{
	$encodeAddress = urlencode(withoutSpecialChars($address));
	//$url = "http://maps.google.com/maps/geo?q=".$encodeAddress."&output=csv";
	$url = "http://maps.google.com/maps/api/geocode/json?address=".$encodeAddress."&sensor=false";
	ini_set("allow_url_open", "1");
	$response = googlegetURLContent($url,'GET');

	if ($response['curl_error_no'])
	{
		$returnstring=$response['curl_error_no'].' '.$response['curl_error_msg'];
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	}

	$data = json_decode($response['content']);
	if ($data->status == "OK")
	{
		$return=array();
		$return['lat']=$data->results[0]->geometry->location->lat;
		$return['lng']=$data->results[0]->geometry->location->lng;
		return $return;
	}
	else if ($data->status == "OVER_QUERY_LIMIT")
	{
		$returnstring='OVER_QUERY_LIMIT';
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	}
	else if ($data->status == "ZERO_RESULTS")
	{
		$returnstring='ZERO_RESULTS';
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	}
	else {
		$returnstring='Failed to json_decode result '.$response['content'];
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	}
}

/**
 * Remove accentued characters
 *
 * @param string $chaine		The string to treat
 * @param string $remplace_par	The replacement character
 * @return string
 */
function withoutSpecialChars($str, $replaceBy = '_')
{
	$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
	$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	return $str;
	/*
		$accents = "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ";
	$sansAccents = "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy";
	$text = strtr($text, $accents, $sansAccents);
	$text = preg_replace('/([^.a-z0-9]+)/i', $replaceBy, $text);
	return $text;*/
}


/**
 * Function get content from an URL (use proxy if proxy defined)
 *
 * @param	string	$url 			URL to call.
 * @param	string	$postorget		'post' = POST, 'get='GET'
 * @return	array					returns an associtive array containing the response from the server.
 */
function googlegetURLContent($url,$postorget='GET',$param='')
{
	//declaring of global variables
	global $conf, $langs;
    $USE_PROXY=empty($conf->global->MAIN_PROXY_USE)?0:$conf->global->MAIN_PROXY_USE;
    $PROXY_HOST=empty($conf->global->MAIN_PROXY_HOST)?0:$conf->global->MAIN_PROXY_HOST;
    $PROXY_PORT=empty($conf->global->MAIN_PROXY_PORT)?0:$conf->global->MAIN_PROXY_PORT;
    $PROXY_USER=empty($conf->global->MAIN_PROXY_USER)?0:$conf->global->MAIN_PROXY_USER;
    $PROXY_PASS=empty($conf->global->MAIN_PROXY_PASS)?0:$conf->global->MAIN_PROXY_PASS;

	dol_syslog("getURLContent postorget=".$postorget." URL=".$url." param=".$param);

	//setting the curl parameters.
	$ch = curl_init();

	/*print $API_Endpoint."-".$API_version."-".$PAYPAL_API_USER."-".$PAYPAL_API_PASSWORD."-".$PAYPAL_API_SIGNATURE."<br>";
	 print $USE_PROXY."-".$gv_ApiErrorURL."<br>";
	print $nvpStr;
	exit;*/
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSLVERSION, 3); // Force SSLv3

	//turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?5:$conf->global->MAIN_USE_CONNECT_TIMEOUT);
    curl_setopt($ch, CURLOPT_TIMEOUT, empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?5:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	if ($postorget == 'POST') curl_setopt($ch, CURLOPT_POST, 1);
	else curl_setopt($ch, CURLOPT_POST, 0);

	//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
	if ($USE_PROXY)
	{
		dol_syslog("getURLContent set proxy to ".$PROXY_HOST. ":" . $PROXY_PORT." - ".$PROXY_USER. ":" . $PROXY_PASS);
		//curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); // Curl 7.10
		curl_setopt($ch, CURLOPT_PROXY, $PROXY_HOST. ":" . $PROXY_PORT);
		if ($PROXY_USER) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $PROXY_USER. ":" . $PROXY_PASS);
	}

	//setting the nvpreq as POST FIELD to curl
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

	//getting response from server
	$response = curl_exec($ch);

	$rep=array();
	$rep['content']=$response;
	$rep['curl_error_no']='';
	$rep['curl_error_msg']='';

	//dol_syslog("getURLContent response=".$response);

	if (curl_errno($ch))
	{
		// moving to display page to display curl errors
		$rep['curl_error_no']=curl_errno($ch);
		$rep['curl_error_msg']=curl_error($ch);

		dol_syslog("getURLContent curl_error array is ".join(',',$rep));
	}
	else
	{
		//closing the curl
		curl_close($ch);
	}

	return $rep;
}
?>
