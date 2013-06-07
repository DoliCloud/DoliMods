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
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/google/class/googlemaps.class.php");
dol_include_once("/google/includes/GoogleMapAPIv3.class.php");

$langs->load("google@google");

// url is:  gmaps.php?mode=thirdparty|contact|member&id=id


$mode=GETPOST('mode');
$address='';

// Load third party
if (empty($mode) || $mode=='thirdparty')
{
	include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
	$id = GETPOST('id','int');
	$object = new Societe($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1,', ');
}
else if ($mode=='contact')
{
	include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
	$id = GETPOST('id','int');
	$object = new Contact($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1,', ');
}
else if ($mode=='member')
{
	include_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');
	$id = GETPOST('id','int');
	$object = new Adherent($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1,', ');
}
else 
{
	dol_print_error('','Bad value for mode param into url');
	exit;
}


/*
 * View
 */

// TODO Add on body ' onunload="GUnload()"'
llxheader();

$form=new Form($db);

$content = "Default content";
$act = "";

//On fabrique les onglets
$head=array();
$title='';
$picto='';
if (empty($mode) || $mode=='thirdparty')
{
	$title=$langs->trans("ThirdParty");
	$picto='company';
	$sql="SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.libelle as country,";
	$sql.= " g.latitude, g.longitude";
	$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_pays as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='company'";
	$sql.= " WHERE s.status = 1";
}
else if ($mode=='contact')
{
	$title=$langs->trans("ContactsAddresses");
	$picto='contact';
}
else if ($mode=='member')
{
	$title=$langs->trans("Member");
	$picto='user';
}


print_fiche_titre($langs->trans("MapOfThirdparties"));

dol_fiche_head(array(), 'gmaps', '', 0);


// Fill array of contacts
$MAXADDRESS=1;
$addresses = array();
$googlemaps = new Googlemaps($db);

// Loop
dol_syslog("Search addresses sql=".$sql);
$resql=$db->query($sql);
if ($resql)
{
	$num=$db->num_rows($resql);
	$i=0;
	while ($i < $num && (empty($MAXADDRESS) || $i < $MAXADDRESS))
	{
		$obj=$db->fetch_object($resql);
		if (empty($obj->country_code)) $obj->country_code=$mysoc->country_code;

		$error='';
		
		$object=new stdClass();
		$object->name=$obj->name;
		$object->latitude = $obj->latitude;
		$object->longitude = $obj->longitude;
		$object->address=dol_format_address($obj,1," ");
		
		if (empty($object->latitude) && empty($object->longitude))
		{
			$point = geocoding($object->address);
			if (is_array($point)) 
			{
				$object->latitude=$point['lat'];
				$object->longitude=$point['lng'];
			}
			else $error=$point;
		}
					 
		if (! $error)
		{
			$addresses[]=$object;
		}
		else
		{
			print 'Failed to get position for '.$object->name.' address='.$object->address.': '.$error.'<br>'."\n";
		}
		
		$i++;
	}
}
else
{
	dol_print_error($db);
}

// If no addresses
if (count($addresses) == 0) print $langs->trans("NoAddressDefined").'<br><br>';

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
$gmap->addArrayMarker($addresses,$langs,$mode);


$gmap->generate();
echo $gmap->getGoogleMap();


/*
$result_sql['adresse'] = 'Grand Place';
$result_sql['cp'] = '7000';
$result_sql['ville'] = 'Mons';

print '    
    <script type="text/javascript">//<![CDATA[
      function load()
        {
        if (GBrowserIsCompatible())
          {
          var map = new GMap2(document.getElementById("map"));
          // Coordonnees de l adresse provenant dans la base de données MySQL
          var adresse = \''.$result_sql['adresse'] . ' ' . $result_sql['cp'] . ' ' . $result_sql['ville'].'\';
          // Recherche des coordonnées d un point dont on connait l adresse :
          var geocoder = new google.maps.ClientGeocoder();
          geocoder.getLatLng(adresse, function (coord)
            {
            // Et centrage de la map sur les coordonnées renvoyées par Google :
            map.setCenter(coord, 15);
            // Affichage du marker
            map.addOverlay(new GMarker(coord));
            });
          // ajout de la propriété d affichage des boutons "type de carte" (3 boutons par défaut)
          map.addControl(new GMapTypeControl());
          // ajout de la propriété ajout d un bouton "type de carte" (Relief)
          map.addMapType(G_PHYSICAL_MAP);
          // ajout de la propriété zoom à la carte "map"
          map.addControl(new GSmallMapControl);
          }
        }
    //]]></script>
';

if (count($result_sql))
{
	print '
	
	<br>
	<div align="center">
	<div id="map" class="divmap" style="width: 90%; height: 500px;" ></div>
	</div>
	<br>
	
	';
}
*/

dol_fiche_end();

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
	else 
	{
		$returnstring='Failed to json_decode result '.$response['content'];
		echo "<!-- geocoding : failure to geocode : " . dol_escape_htmltag($returnstring) . " -->\n";
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

	dol_syslog("getURLContent response=".$response);

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