<?php
/* Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file			htdocs/openstreetmap/lib/openstreetmap.lib.php
 *  \brief			Library of admin functions for openstreetmap module
 */


/**
 *  Define head array for tabs of openstreetmap tools setup pages
 *
 *  @return			Array of head
 */
function openstreetmapadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	/*  $head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap.php",1);
	$head[$h][1] = $langs->trans("AgendaView");
	$head[$h][2] = 'agenda';
	$h++;

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_calsync.php",1);
	$head[$h][1] = $langs->trans("AgendaSync");
	$head[$h][2] = 'agendasync';
	$h++;
	*/
	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_maps.php", 1);
	$head[$h][1] = $langs->trans("Maps");
	$head[$h][2] = 'maps';
	$h++;
	/*
	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_ad.php",1);
	$head[$h][1] = $langs->trans("Adsense");
	$head[$h][2] = 'adsense';
	$h++;

	include_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
	$dolibarrversionarray=preg_split('/[\.-]/',version_dolibarr());
	$dolibarrversionok=array(3,1,-2);
	if (versioncompare($dolibarrversionarray,$dolibarrversionok) >= 0)
	{
		$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_an.php",1);
		$head[$h][1] = $langs->trans("Analitycs");
		$head[$h][2] = 'analytics';
		$h++;
	}
	*/

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_geo.php", 1);
	$head[$h][1] = $langs->trans("OpenStreetMapGeocoding");
	$head[$h][2] = 'geocoding';
	$h++;

	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'tababout';
	$h++;

	return $head;
}


/**
 *  Return URL of tile server to use
 *
 *  @return		string		URL template with {z}, {x} and {y}
 */
function openstreetmap_get_tile_url()
{
	return getDolGlobalString('OPENSTREETMAP_TILE_URL', 'https://tile.openstreetmap.org/{z}/{x}/{y}.png');
}

/**
 *  Return attribution to show on maps (required by the OpenStreetMap license)
 *
 *  @return		string		HTML attribution
 */
function openstreetmap_get_tile_attribution()
{
	return getDolGlobalString('OPENSTREETMAP_TILE_ATTRIBUTION', '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors');
}

/**
 *  Return HTML to include the Leaflet library (served from the module, no external CDN)
 *
 *  @param		int		$withcluster	1=Include also the marker cluster plugin
 *  @return		string					HTML
 */
function openstreetmap_include_leaflet($withcluster = 0)
{
	$url = dol_buildpath('/openstreetmap/includes', 1);
	$out = '<link rel="stylesheet" href="'.$url.'/leaflet/leaflet.css">'."\n";
	$out .= '<script src="'.$url.'/leaflet/leaflet.js"></script>'."\n";
	if ($withcluster) {
		$out .= '<link rel="stylesheet" href="'.$url.'/markercluster/MarkerCluster.css">'."\n";
		$out .= '<link rel="stylesheet" href="'.$url.'/markercluster/MarkerCluster.Default.css">'."\n";
		$out .= '<script src="'.$url.'/markercluster/leaflet.markercluster.js"></script>'."\n";
	}
	$out .= '<script>L.Icon.Default.imagePath = "'.dol_escape_js($url.'/leaflet/images/').'";</script>'."\n";
	return $out;
}

/**
 *  Call an external geocoding service and return decoded JSON
 *
 *  @param		string		$url		URL to call
 *  @return		array|string			Decoded JSON array if OK, error message string if KO
 */
function openstreetmap_get_json($url)
{
	global $dolibarr_main_url_root;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

	// Services of OpenStreetMap foundation require an identifying User-Agent
	$host = parse_url((string) $dolibarr_main_url_root, PHP_URL_HOST);
	$headers = array('User-Agent: Dolibarr-module-OpenStreetMap'.($host ? ' ('.$host.')' : ''), 'Accept: application/json');

	$result = getURLContent($url, 'GET', '', 1, $headers, array('https'), 0, -1, 5, 10);
	if (!empty($result['curl_error_no'])) {
		return 'CURL_'.$result['curl_error_no'].' '.$result['curl_error_msg'];
	}
	if (!empty($result['http_code']) && $result['http_code'] != 200) {
		return 'HTTP_'.$result['http_code'];
	}
	$data = json_decode($result['content'], true);
	if (!is_array($data)) {
		return 'BAD_JSON';
	}
	return $data;
}

/**
 *  Convert a PDOK point "POINT(lon lat)" into an array
 *
 *  @param		string		$point		Point in WKT format
 *  @return		array|null				array('lat'=>, 'lon'=>) or null
 */
function openstreetmap_pdok_parse_point($point)
{
	if (preg_match('/POINT\(\s*([\-0-9\.]+)\s+([\-0-9\.]+)\s*\)/i', (string) $point, $reg)) {
		return array('lat' => (float) $reg[2], 'lon' => (float) $reg[1]);
	}
	return null;
}

/**
 *  Search with the PDOK Locatieserver (Dutch government service based on the BAG, free, no key)
 *
 *  @param		string		$q			Query (Solr syntax allowed)
 *  @param		string		$type		Type of object to search ('adres', 'postcode', 'woonplaats')
 *  @param		int			$rows		Max number of results
 *  @return		array|string			Array of documents if OK, error message string if KO
 */
function openstreetmap_pdok_search($q, $type = 'adres', $rows = 1)
{
	$url = getDolGlobalString('OPENSTREETMAP_PDOK_URL', 'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free');
	$url .= '?q='.urlencode($q).'&fq='.urlencode('type:'.$type).'&rows='.((int) $rows);
	$url .= '&fl='.urlencode('weergavenaam type straatnaam huisnummer huisletter huisnummertoevoeging postcode woonplaatsnaam gemeentenaam provincienaam centroide_ll score');

	$data = openstreetmap_get_json($url);
	if (!is_array($data)) {
		return $data;
	}
	if (!isset($data['response']['docs']) || !is_array($data['response']['docs'])) {
		return 'BAD_RESPONSE';
	}
	return $data['response']['docs'];
}

/**
 *  Search an address with Nominatim (OpenStreetMap). Usage policy: max 1 request per second,
 *  no autocomplete, results must be cached. See https://operations.osmfoundation.org/policies/nominatim/
 *
 *  @param		string		$q				Address to search
 *  @param		string		$countrycode	ISO country code to restrict search (optional)
 *  @return		array|string				array('lat'=>, 'lon'=>) if found, 'ZERO_RESULTS' or error message string if KO
 */
function openstreetmap_nominatim_search($q, $countrycode = '')
{
	static $lastcall = 0;

	// Respect the rate limit of 1 request per second
	$wait = 1.1 - (microtime(true) - $lastcall);
	if ($lastcall && $wait > 0) {
		usleep((int) ($wait * 1000000));
	}

	$url = getDolGlobalString('OPENSTREETMAP_NOMINATIM_URL', 'https://nominatim.openstreetmap.org/search');
	$url .= '?format=jsonv2&limit=1&q='.urlencode($q);
	if ($countrycode) {
		$url .= '&countrycodes='.urlencode(strtolower($countrycode));
	}
	if (getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL')) {
		$url .= '&email='.urlencode(getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL'));
	}

	$data = openstreetmap_get_json($url);
	$lastcall = microtime(true);

	if (!is_array($data)) {
		return $data;
	}
	if (empty($data[0]['lat']) || empty($data[0]['lon'])) {
		return 'ZERO_RESULTS';
	}
	return array('lat' => (float) $data[0]['lat'], 'lon' => (float) $data[0]['lon']);
}

/**
 *  Geocode an address (address -> latitude/longitude).
 *  Dutch addresses are searched with PDOK, other countries with Nominatim.
 *
 *  @param		object		$obj		Object with properties address, zip, town, country_code, country
 *  @return		array					array('lat'=>, 'lon'=>, 'source'=>, 'degraded'=>0|1) if OK,
 *  									array('error'=>code, 'source'=>) if KO
 */
function openstreetmap_geocode($obj)
{
	$address = trim(preg_replace('/\s+/', ' ', (string) $obj->address));
	$zip = trim((string) $obj->zip);
	$town = trim((string) $obj->town);
	$countrycode = strtoupper(trim((string) $obj->country_code));

	if ($countrycode == 'NL' && !getDolGlobalString('OPENSTREETMAP_DISABLE_PDOK')) {
		$tries = array();
		if ($address && ($zip || $town)) {
			$tries[] = array(trim($address.' '.$zip.' '.$town), 'adres', 0);
		}
		if ($zip) {
			$tries[] = array(str_replace(' ', '', $zip), 'postcode', 1);
		}
		if ($town) {
			$tries[] = array($town, 'woonplaats', 1);
		}
		$error = 'ZERO_RESULTS';
		foreach ($tries as $try) {
			$docs = openstreetmap_pdok_search($try[0], $try[1], 1);
			if (!is_array($docs)) {
				$error = $docs;
				break;
			}
			if (!empty($docs[0]['centroide_ll'])) {
				$point = openstreetmap_pdok_parse_point($docs[0]['centroide_ll']);
				if ($point) {
					return array('lat' => $point['lat'], 'lon' => $point['lon'], 'source' => 'pdok', 'degraded' => $try[2]);
				}
			}
		}
		return array('error' => $error, 'source' => 'pdok');
	}

	// Other countries: Nominatim
	$full = dol_format_address($obj, 1, ', ');
	$point = openstreetmap_nominatim_search($full, $countrycode);
	$degraded = 0;
	if ($point === 'ZERO_RESULTS' && ($zip || $town)) {
		// Try with a degraded address (zip and town only)
		$point = openstreetmap_nominatim_search(dol_format_address($obj, 1, ', ', null, 1), $countrycode);
		$degraded = 1;
	}
	if (is_array($point)) {
		return array('lat' => $point['lat'], 'lon' => $point['lon'], 'source' => 'nominatim', 'degraded' => $degraded);
	}
	return array('error' => $point, 'source' => 'nominatim');
}

/**
 *  Return the coordinates of an object, using the cache table when the address has not changed.
 *
 *  @param		DoliDB		$db				Database handler
 *  @param		string		$type			Type of object ('thirdparty', 'contact', 'member', 'user')
 *  @param		int			$id				Id of object
 *  @param		object		$obj			Object with properties address, zip, town, country_code, country
 *  @param		array|null	$cached			Cached row if already loaded (array with keys latitude, longitude, address, result_code, result_label, rowid) or null to load it
 *  @param		int			$nosearch		1=Never call an external service (only read cache)
 *  @return		array						array('lat'=>, 'lon'=>, 'cached'=>0|1) if OK, array('error'=>, 'label'=>, 'cached'=>0|1) if KO, array('pending'=>1) if not searched
 */
function openstreetmap_get_coordinates($db, $type, $id, $obj, $cached = null, $nosearch = 0)
{
	$addresstosearch = dol_format_address($obj, 1, ' ');
	if (trim($addresstosearch) == '' || trim($addresstosearch) == trim((string) $obj->country)) {
		return array('error' => 'NO_ADDRESS', 'label' => 'Address not defined', 'cached' => 0);
	}

	if ($cached === null) {
		$cached = array();
		$sql = "SELECT rowid, latitude, longitude, address, result_code, result_label FROM ".MAIN_DB_PREFIX."openstreetmap_geo";
		$sql .= " WHERE fk_object = ".((int) $id)." AND type_object = '".$db->escape($type)."'";
		$resql = $db->query($sql);
		if ($resql && ($o = $db->fetch_object($resql))) {
			$cached = (array) $o;
		}
	}

	// Use cache if address has not changed (errors that may be temporary are searched again)
	if (!empty($cached['rowid']) && $cached['address'] == dol_trunc($addresstosearch, 255, 'right', 'UTF-8', 1)) {
		if ($cached['result_code'] == 'OK' && $cached['latitude'] !== null && $cached['longitude'] !== null) {
			return array('lat' => (float) $cached['latitude'], 'lon' => (float) $cached['longitude'], 'cached' => 1);
		}
		if (in_array($cached['result_code'], array('ZERO_RESULTS', 'NO_ADDRESS'))) {
			return array('error' => $cached['result_code'], 'label' => $cached['result_label'], 'cached' => 1);
		}
	}

	if ($nosearch) {
		return array('pending' => 1);
	}

	$res = openstreetmap_geocode($obj);

	$lat = isset($res['lat']) ? (float) $res['lat'] : null;
	$lon = isset($res['lon']) ? (float) $res['lon'] : null;
	$code = isset($res['error']) ? dol_trunc((string) $res['error'], 16, 'right', 'UTF-8', 1) : 'OK';
	$label = '';
	if (isset($res['error'])) {
		$label = ($res['error'] == 'ZERO_RESULTS' ? 'Address not complete or unknown' : 'Geocoding failed: '.$res['error']);
	} elseif (!empty($res['degraded'])) {
		$label = 'Located on zip or town only';
	}

	$sqladdress = "'".$db->escape(dol_trunc($addresstosearch, 255, 'right', 'UTF-8', 1))."'";
	$sqllat = ($lat === null ? 'NULL' : "'".$db->escape((string) $lat)."'");
	$sqllon = ($lon === null ? 'NULL' : "'".$db->escape((string) $lon)."'");
	if (!empty($cached['rowid'])) {
		$sql = "UPDATE ".MAIN_DB_PREFIX."openstreetmap_geo SET";
		$sql .= " latitude = ".$sqllat.", longitude = ".$sqllon.", address = ".$sqladdress.",";
		$sql .= " source = '".$db->escape($res['source'])."', result_code = '".$db->escape($code)."', result_label = '".$db->escape($label)."'";
		$sql .= " WHERE rowid = ".((int) $cached['rowid']);
	} else {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."openstreetmap_geo (fk_object, type_object, latitude, longitude, address, source, result_code, result_label)";
		$sql .= " VALUES (".((int) $id).", '".$db->escape($type)."', ".$sqllat.", ".$sqllon.", ".$sqladdress.",";
		$sql .= " '".$db->escape($res['source'])."', '".$db->escape($code)."', '".$db->escape($label)."')";
	}
	if (!$db->query($sql)) {
		dol_syslog("openstreetmap_get_coordinates: failed to save cache ".$db->lasterror(), LOG_WARNING);
	}

	if ($code == 'OK') {
		return array('lat' => $lat, 'lon' => $lon, 'cached' => 0);
	}
	return array('error' => $code, 'label' => $label, 'cached' => 0);
}

