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
	global $langs;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_maps.php", 1);
	$head[$h][1] = $langs->trans("OpenStreetMapMaps");
	$head[$h][2] = 'maps';
	$h++;

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_geo.php", 1);
	$head[$h][1] = $langs->trans("OpenStreetMapGeocoding");
	$head[$h][2] = 'geocoding';
	$h++;

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_ziptown.php", 1);
	$head[$h][1] = $langs->trans("OpenStreetMapZiptown");
	$head[$h][2] = 'ziptown';
	$h++;

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/about.php", 1);
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
 *  The request has short timeouts (OPENSTREETMAP_CONNECT_TIMEOUT, OPENSTREETMAP_RESPONSE_TIMEOUT), so a slow
 *  or unreachable service can not keep a page of Dolibarr waiting for a long time.
 *
 *  @param		string			$url			URL to call
 *  @param		array<int,string>	$extraheaders	Additional HTTP headers (for example an authorization)
 *  @param		int				$timeoutconnect		Timeout to connect in seconds, or 0 for OPENSTREETMAP_CONNECT_TIMEOUT (3)
 *  @param		int				$timeoutresponse	Timeout of the request in seconds, or 0 for OPENSTREETMAP_RESPONSE_TIMEOUT (8)
 *  @return		array<mixed>|string		Decoded JSON array if OK, error message string if KO
 */
function openstreetmap_get_json($url, $extraheaders = array(), $timeoutconnect = 0, $timeoutresponse = 0)
{
	global $dolibarr_main_url_root;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

	// Services of OpenStreetMap foundation require an identifying User-Agent
	$host = parse_url((string) $dolibarr_main_url_root, PHP_URL_HOST);
	$headers = array_merge(array('User-Agent: Dolibarr-module-OpenStreetMap'.($host ? ' ('.$host.')' : ''), 'Accept: application/json'), $extraheaders);

	$timeoutconnect = ($timeoutconnect > 0 ? (int) $timeoutconnect : getDolGlobalInt('OPENSTREETMAP_CONNECT_TIMEOUT', 3));
	$timeoutresponse = ($timeoutresponse > 0 ? (int) $timeoutresponse : getDolGlobalInt('OPENSTREETMAP_RESPONSE_TIMEOUT', 8));
	$result = openstreetmap_url_content($url, $headers, $timeoutconnect, $timeoutresponse);

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
 *  Call getURLContent() with timeouts for this request only.
 *  Recent versions of Dolibarr accept the timeouts as parameters. Older ones (before the parameters existed) only
 *  read MAIN_USE_CONNECT_TIMEOUT and MAIN_USE_RESPONSE_TIMEOUT: for them only, these settings are changed during
 *  the call and restored after it.
 *
 *  @param		string				$url				URL to call (GET)
 *  @param		array<int,string>	$headers			HTTP headers
 *  @param		int					$timeoutconnect		Timeout to connect (seconds)
 *  @param		int					$timeoutresponse	Timeout for the whole request (seconds)
 *  @return		array<string,mixed>						Result of getURLContent()
 */
function openstreetmap_url_content($url, $headers, $timeoutconnect, $timeoutresponse)
{
	global $conf;

	static $withtimeoutparams = null;
	if ($withtimeoutparams === null) {
		$withtimeoutparams = false;
		try {
			$reflection = new ReflectionFunction('getURLContent');
			foreach ($reflection->getParameters() as $parameter) {
				if ($parameter->getName() == 'timeoutconnect') {
					$withtimeoutparams = true;
				}
			}
		} catch (Exception $e) {
			dol_syslog("openstreetmap_url_content: ".$e->getMessage(), LOG_WARNING);
		}
	}
	if ($withtimeoutparams) {
		// Called by name: the static analysis of older versions does not know the 2 parameters added later
		$params = array($url, 'GET', '', 1, $headers, array('https'), 0, -1, $timeoutconnect, $timeoutresponse);
		return call_user_func_array('getURLContent', $params);
	}

	$saved = array();
	foreach (array('MAIN_USE_CONNECT_TIMEOUT' => $timeoutconnect, 'MAIN_USE_RESPONSE_TIMEOUT' => $timeoutresponse) as $key => $value) {
		$saved[$key] = isset($conf->global->$key) ? $conf->global->$key : null;
		$conf->global->$key = $value;
	}
	$result = getURLContent($url, 'GET', '', 1, $headers, array('https'), 0, -1);
	foreach ($saved as $key => $value) {
		if ($value === null) {
			unset($conf->global->$key);
		} else {
			$conf->global->$key = $value;
		}
	}
	return $result;
}

/**
 *  Convert a PDOK point "POINT(lon lat)" into an array
 *
 *  @param		string		$point		Point in WKT format
 *  @return		array{lat:float,lon:float}|null		array('lat'=>, 'lon'=>) or null
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
 *  @return		array<int,array<string,mixed>>|string	Array of documents if OK, error message string if KO
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
 *  @return		array{lat:float,lon:float}|string	array('lat'=>, 'lon'=>) if found, 'ZERO_RESULTS' or error message string if KO
 */
function openstreetmap_nominatim_search($q, $countrycode = '')
{
	openstreetmap_nominatim_throttle();

	$url = getDolGlobalString('OPENSTREETMAP_NOMINATIM_URL', 'https://nominatim.openstreetmap.org/search');
	$url .= '?format=jsonv2&limit=1&q='.urlencode($q);
	if ($countrycode) {
		$url .= '&countrycodes='.urlencode(strtolower($countrycode));
	}
	if (getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL')) {
		$url .= '&email='.urlencode(getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL'));
	}

	$data = openstreetmap_get_json($url);

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
 *  @return		array<string,mixed>		array('lat'=>, 'lon'=>, 'source'=>, 'degraded'=>0|1) if OK,
 *  									array('error'=>code, 'source'=>) if KO
 */
function openstreetmap_geocode($obj)
{
	global $langs;

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
		$point = openstreetmap_nominatim_search(dol_format_address($obj, 1, ', ', $langs, 1), $countrycode);
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
 *  @param		array<string,mixed>|null	$cached	Cached row if already loaded (array with keys latitude, longitude, address, result_code, result_label, rowid) or null to load it
 *  @param		int			$nosearch		1=Never call an external service (only read cache)
 *  @return		array<string,mixed>			array('lat'=>, 'lon'=>, 'cached'=>0|1) if OK, array('error'=>, 'label'=>, 'cached'=>0|1) if KO, array('pending'=>1) if not searched
 */
function openstreetmap_get_coordinates($db, $type, $id, $obj, $cached = null, $nosearch = 0)
{
	global $conf;

	$addresstosearch = dol_format_address($obj, 1, ' ');
	if (trim($addresstosearch) == '' || trim($addresstosearch) == trim((string) $obj->country)) {
		return array('error' => 'NO_ADDRESS', 'label' => 'Address not defined', 'cached' => 0);
	}

	if ($cached === null) {
		$cached = array();
		$sql = "SELECT rowid, latitude, longitude, address, result_code, result_label FROM ".MAIN_DB_PREFIX."openstreetmap_geo";
		$sql .= " WHERE fk_object = ".((int) $id)." AND type_object = '".$db->escape($type)."' AND entity = ".((int) $conf->entity);
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
		if ($res['error'] == 'ZERO_RESULTS') {
			$label = 'Address not complete or unknown';
		} else {
			// Technical detail goes to the log only, the user sees a plain message
			dol_syslog("openstreetmap_get_coordinates: geocoding of ".$type." ".$id." failed: ".$res['error'], LOG_WARNING);
			$label = 'Geocoding service not reachable, will be retried';
		}
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
		$sql .= " WHERE rowid = ".((int) $cached['rowid'])." AND entity = ".((int) $conf->entity);
	} else {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."openstreetmap_geo (entity, fk_object, type_object, latitude, longitude, address, source, result_code, result_label)";
		$sql .= " VALUES (".((int) $conf->entity).", ".((int) $id).", '".$db->escape($type)."', ".$sqllat.", ".$sqllon.", ".$sqladdress.",";
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

/**
 *  Return the minimum delay between two requests to Nominatim, in milliseconds.
 *  The public server of the OpenStreetMap foundation allows at most 1 request per second (1100 ms is used).
 *  For an own Nominatim server (OPENSTREETMAP_NOMINATIM_URL), there is no delay by default.
 *  OPENSTREETMAP_NOMINATIM_DELAY_MS sets another delay; it can not be lower than 1100 ms for the public server.
 *
 *  @return		int		Delay in milliseconds
 */
function openstreetmap_nominatim_delay()
{
	$url = getDolGlobalString('OPENSTREETMAP_NOMINATIM_URL', 'https://nominatim.openstreetmap.org/search');
	$public = (bool) preg_match('/^https?:\/\/([a-z0-9\-]+\.)*openstreetmap\.org(\/|:|$)/i', $url);
	$delay = getDolGlobalString('OPENSTREETMAP_NOMINATIM_DELAY_MS') !== '' ? max(0, getDolGlobalInt('OPENSTREETMAP_NOMINATIM_DELAY_MS')) : ($public ? 1100 : 0);
	if ($public) {
		$delay = max(1100, $delay);
	}
	return $delay;
}

/**
 *  Wait so that Nominatim gets at most one request per delay (openstreetmap_nominatim_delay()) from this server,
 *  also between concurrent PHP processes (a lock file holds the time of the last request).
 *
 *  @return		void
 */
function openstreetmap_nominatim_throttle()
{
	$delay = openstreetmap_nominatim_delay() / 1000;
	if ($delay <= 0) {
		return;
	}
	$dir = openstreetmap_cache_dir();
	$fp = ($dir ? @fopen($dir.'/nominatim.lock', 'c+') : false);
	if (!$fp) {
		usleep((int) ($delay * 1000000));	// No lock file possible: always wait, never exceed the limit
		return;
	}
	flock($fp, LOCK_EX);
	$last = (float) stream_get_contents($fp);
	$wait = $delay - (microtime(true) - $last);
	if ($wait > 0) {
		usleep((int) ($wait * 1000000));
	}
	ftruncate($fp, 0);
	rewind($fp);
	fwrite($fp, (string) microtime(true));
	flock($fp, LOCK_UN);
	fclose($fp);
}

/**
 *  Return the directory used to cache answers of external services (created if needed)
 *
 *  @return		string		Directory, or '' if it can not be created
 */
function openstreetmap_cache_dir()
{
	$dir = DOL_DATA_ROOT.'/openstreetmap/cache';
	if (!is_dir($dir) && dol_mkdir($dir) < 0) {
		return '';
	}
	return $dir;
}

/**
 *  Read an answer of an external service from the cache
 *
 *  @param		string		$key		Key of the request
 *  @param		int			$maxage		Max age in seconds
 *  @return		array<mixed>|null		Cached data or null
 */
function openstreetmap_cache_get($key, $maxage = 2592000)
{
	$dir = openstreetmap_cache_dir();
	$file = $dir.'/'.md5($key).'.json';
	if (!$dir || !is_file($file) || (time() - filemtime($file)) > $maxage) {
		return null;
	}
	$data = json_decode((string) file_get_contents($file), true);
	return is_array($data) ? $data : null;
}

/**
 *  Store an answer of an external service in the cache
 *
 *  @param		string			$key		Key of the request
 *  @param		array<mixed>	$data		Data to store
 *  @return		void
 */
function openstreetmap_cache_set($key, $data)
{
	$dir = openstreetmap_cache_dir();
	if ($dir) {
		@file_put_contents($dir.'/'.md5($key).'.json', json_encode($data), LOCK_EX);
	}
}

/**
 *  Return the ISO code of a country from its id in llx_c_country
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		int			$countryid	Id of country
 *  @return		string					ISO code (2 letters) or ''
 */
function openstreetmap_country_code($db, $countryid)
{
	$resql = $db->query("SELECT code FROM ".MAIN_DB_PREFIX."c_country WHERE rowid = ".((int) $countryid));
	if ($resql && ($obj = $db->fetch_object($resql)) && preg_match('/^[A-Z]{2}$/', (string) $obj->code)) {
		return $obj->code;
	}
	return '';
}

/**
 *  Return the towns of a postcode. The dictionary of zip codes and towns of Dolibarr (llx_c_ziptown) is read
 *  first; Nominatim is asked only when the postcode is not in it (one request per postcode, answers cached).
 *
 *  @param		DoliDB		$db				Database handler
 *  @param		int			$countryid		Id of country in llx_c_country
 *  @param		string		$countrycode	ISO country code
 *  @param		string		$zip			Postcode
 *  @return		array<int,string>|string	List of town names, or error message string if KO
 */
function openstreetmap_postcode_towns($db, $countryid, $countrycode, $zip)
{
	$towns = openstreetmap_ziptown_towns($db, $countryid, $zip);
	if (count($towns)) {
		return $towns;
	}

	$key = 'postcode|'.strtoupper($countrycode).'|'.strtoupper($zip);
	$cached = openstreetmap_cache_get($key);
	if ($cached !== null) {
		return $cached;
	}

	openstreetmap_nominatim_throttle();

	$url = getDolGlobalString('OPENSTREETMAP_NOMINATIM_URL', 'https://nominatim.openstreetmap.org/search');
	$url .= '?format=jsonv2&addressdetails=1&limit=10&postalcode='.urlencode($zip).'&countrycodes='.urlencode(strtolower($countrycode));
	if (getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL')) {
		$url .= '&email='.urlencode(getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL'));
	}
	$data = openstreetmap_get_json($url);
	if (!is_array($data)) {
		return $data;
	}

	$towns = array();
	foreach ($data as $place) {
		if (!is_array($place) || empty($place['address']) || !is_array($place['address'])) {
			continue;
		}
		foreach (array('city', 'town', 'village', 'municipality', 'hamlet') as $k) {
			if (!empty($place['address'][$k]) && is_string($place['address'][$k])) {
				$towns[] = $place['address'][$k];
				break;
			}
		}
	}
	$towns = array_values(array_unique($towns));
	openstreetmap_cache_set($key, $towns);
	return $towns;
}

/**
 *  Return the towns of a postcode found in the dictionary of zip codes and towns of Dolibarr (llx_c_ziptown).
 *  The postcode is compared without spaces and dashes, so "123 45" matches "12345".
 *
 *  @param		DoliDB		$db				Database handler
 *  @param		int			$countryid		Id of country in llx_c_country
 *  @param		string		$zip			Postcode
 *  @return		array<int,string>			List of town names (empty if none)
 */
function openstreetmap_ziptown_towns($db, $countryid, $zip)
{
	$towns = array();
	$zipclean = openstreetmap_clean_zip($zip);
	if ($countryid <= 0 || $zipclean === '') {
		return $towns;
	}
	// Some lists only have the first part of the postcodes (for example GeoNames for Great Britain and Canada:
	// "AL3" for "AL3 8QE"): when the full postcode is not found, its first part is searched.
	$parts = preg_split('/[\s\-]+/', trim((string) $zip));
	$outward = (count($parts) > 1) ? openstreetmap_clean_zip($parts[0]) : '';
	$searched = array("'".$db->escape($zipclean)."'");
	if ($outward !== '' && $outward !== $zipclean) {
		$searched[] = "'".$db->escape($outward)."'";
	}
	// The prefix lets the database use the index on zip; the exact comparison (without spaces and dashes) is done
	// by the database too, so only the matching rows are returned, whatever the size of the dictionary.
	$prefix = substr($zipclean, 0, 2);
	$prefix = method_exists($db, 'escapeforlike') ? $db->escapeforlike($db->escape($prefix)) : $db->escape($prefix);
	$sql = "SELECT zip, town FROM ".MAIN_DB_PREFIX."c_ziptown";
	$sql .= " WHERE fk_pays = ".((int) $countryid)." AND active = 1";
	$sql .= " AND zip LIKE '".$prefix."%'";
	$sql .= " AND UPPER(REPLACE(REPLACE(zip, ' ', ''), '-', '')) IN (".implode(',', $searched).")";
	$sql .= " ORDER BY town";
	$sql .= $db->plimit(200);
	$resql = $db->query($sql);
	if (!$resql) {
		dol_syslog("openstreetmap_ziptown_towns: ".$db->lasterror(), LOG_WARNING);
		return $towns;
	}
	$outwardtowns = array();
	while ($obj = $db->fetch_object($resql)) {
		$rowzip = openstreetmap_clean_zip($obj->zip);
		if ($rowzip === $zipclean && !in_array($obj->town, $towns)) {
			$towns[] = $obj->town;
		} elseif ($outward !== '' && $rowzip === $outward && !in_array($obj->town, $outwardtowns)) {
			$outwardtowns[] = $obj->town;
		}
	}
	return count($towns) ? $towns : $outwardtowns;
}

/**
 *  Return a postcode in upper case without spaces and dashes, to compare postcodes written in different ways
 *
 *  @param		string		$zip		Postcode
 *  @return		string					Cleaned postcode (letters and digits only)
 */
function openstreetmap_clean_zip($zip)
{
	return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', (string) $zip));
}

/**
 *  Return the service used for address suggestions in a country:
 *  - 'postcodeeu': Postcode.eu, only when a key is set and the country is selected for it in the setup;
 *  - 'pdok': PDOK Locatieserver for the Netherlands (free, Dutch government), when OPENSTREETMAP_PDOK_AUTOFILL is on;
 *  - 'photon': Photon on OpenStreetMap data, the free default for all countries, when OPENSTREETMAP_EU_AUTOFILL is on;
 *  - '': no address suggestions.
 *
 *  @param		string		$countrycode	ISO country code
 *  @return		string						'postcodeeu', 'pdok', 'photon' or ''
 */
function openstreetmap_address_provider($countrycode)
{
	$countrycode = strtoupper((string) $countrycode);
	if (!preg_match('/^[A-Z]{2}$/', $countrycode)) {
		return '';
	}
	dol_include_once('/openstreetmap/lib/postcodeeu.lib.php');
	if (openstreetmap_postcodeeu_enabled_for($countrycode)) {
		return 'postcodeeu';
	}
	if ($countrycode == 'NL' && getDolGlobalString('OPENSTREETMAP_PDOK_AUTOFILL')) {
		return 'pdok';
	}
	if (getDolGlobalString('OPENSTREETMAP_EU_AUTOFILL')) {
		return 'photon';
	}
	return '';
}

/**
 *  Return address suggestions for a text typed by the user, with the service chosen for the country.
 *  Each suggestion is an array with:
 *  - 'label': text shown in the list,
 *  - 'final': 1 if the suggestion is a complete address (street, house number when known, postcode, town),
 *             0 if it is a step to search further in (Postcode.eu: a street or a town),
 *  - 'context': for a step or for Postcode.eu, the value to send back (to search further, or to get the details),
 *  - 'street', 'housenumber', 'zip', 'town': parts of the address (for final suggestions of PDOK and Photon).
 *
 *  @param		string		$q				Text typed by the user
 *  @param		string		$countrycode	ISO country code
 *  @param		string		$lang			Language of the user (for example el_GR or 2 letters)
 *  @param		string		$context		Context to search in (Postcode.eu only, from a previous suggestion)
 *  @param		string		$hint			Postcode or town already filled in the form, added to the search of PDOK and
 *  										Photon when the text does not contain it yet (Postcode.eu works with its context)
 *  @return		array{provider:string,items:array<int,array<string,mixed>>}|string	Result, or error message string if KO
 */
function openstreetmap_address_suggest($q, $countrycode, $lang = '', $context = '', $hint = '')
{
	$provider = openstreetmap_address_provider($countrycode);
	$hint = trim((string) $hint);
	$qwithhint = ($hint !== '' && mb_stripos($q, $hint) === false) ? trim($q.' '.$hint) : $q;
	if ($provider == 'postcodeeu') {
		$items = openstreetmap_postcodeeu_suggest($q, $countrycode, $lang, $context);
	} elseif ($provider == 'pdok') {
		$items = openstreetmap_pdok_suggest($qwithhint);
	} elseif ($provider == 'photon') {
		$items = openstreetmap_photon_suggest($qwithhint, $countrycode, $lang);
	} else {
		return 'DISABLED';
	}
	if (!is_array($items)) {
		return $items;
	}
	return array('provider' => $provider, 'items' => $items);
}

/**
 *  Return address suggestions from the PDOK Locatieserver (Netherlands, free search on BAG addresses, answers cached)
 *
 *  @param		string		$q		Text typed by the user
 *  @return		array<int,array<string,mixed>>|string	Suggestions, or error message string if KO
 */
function openstreetmap_pdok_suggest($q)
{
	$key = 'pdoksuggest|'.mb_strtolower(trim($q));
	$cached = openstreetmap_cache_get($key, 86400);
	if ($cached !== null) {
		return $cached;
	}
	$docs = openstreetmap_pdok_search($q, 'adres', 10);
	if (!is_array($docs)) {
		return $docs;
	}
	$items = array();
	foreach ($docs as $doc) {
		if (!is_array($doc) || empty($doc['straatnaam']) || empty($doc['woonplaatsnaam'])) {
			continue;
		}
		$number = (isset($doc['huisnummer']) ? (string) $doc['huisnummer'] : '').(!empty($doc['huisletter']) ? $doc['huisletter'] : '').(!empty($doc['huisnummertoevoeging']) ? '-'.$doc['huisnummertoevoeging'] : '');
		$zip = isset($doc['postcode']) ? (string) $doc['postcode'] : '';
		if (preg_match('/^([0-9]{4})([A-Z]{2})$/', $zip, $reg)) {
			$zip = $reg[1].' '.$reg[2];
		}
		$items[] = array(
			'label' => isset($doc['weergavenaam']) ? (string) $doc['weergavenaam'] : trim($doc['straatnaam'].' '.$number.', '.$zip.' '.$doc['woonplaatsnaam']),
			'final' => 1,
			'street' => (string) $doc['straatnaam'],
			'housenumber' => $number,
			'zip' => $zip,
			'town' => (string) $doc['woonplaatsnaam'],
		);
	}
	openstreetmap_cache_set($key, $items);
	return $items;
}

/**
 *  Return the language to send to Photon. The public Photon server only knows a few languages (en, de, fr) and
 *  answers with an error for the others, so any other language is not sent: Photon then returns the local names.
 *  An own Photon server may support more: OPENSTREETMAP_PHOTON_LANGS lists them (comma separated, default "en,de,fr").
 *
 *  @param		string		$lang		Language of the user (2 letters, or a code like el_GR)
 *  @return		string					Language to send, or '' (local names)
 */
function openstreetmap_photon_lang($lang)
{
	$lang = strtolower(substr((string) $lang, 0, 2));
	$supported = array();
	foreach (explode(',', getDolGlobalString('OPENSTREETMAP_PHOTON_LANGS', 'en,de,fr')) as $code) {
		$code = strtolower(trim($code));
		if (preg_match('/^[a-z]{2}$/', $code)) {
			$supported[] = $code;
		}
	}
	return in_array($lang, $supported) ? $lang : '';
}

/**
 *  Return address suggestions from Photon (search-as-you-type service on OpenStreetMap data; answers cached).
 *  House numbers are returned where OpenStreetMap knows them, otherwise the street only.
 *
 *  @param		string		$q				Text typed by the user (street, house number, postcode or town)
 *  @param		string		$countrycode	ISO country code
 *  @param		string		$lang			Language of the user (for example el_GR or 2 letters)
 *  @return		array<int,array<string,mixed>>|string	Suggestions, or error message string if KO
 */
function openstreetmap_photon_suggest($q, $countrycode, $lang = '')
{
	$lang = openstreetmap_photon_lang($lang);
	$query = trim(preg_replace('/\s+/', ' ', (string) $q));
	$key = 'photon|'.strtoupper($countrycode).'|'.$lang.'|'.mb_strtolower($query);
	$cached = openstreetmap_cache_get($key, 604800);
	if ($cached !== null) {
		return $cached;
	}

	$base = getDolGlobalString('OPENSTREETMAP_PHOTON_URL', 'https://photon.komoot.io/api');
	$base .= '?q='.urlencode($query).'&limit=10'.($lang ? '&lang='.$lang : '');
	// Filters of the request, from the most to the least precise. Versions of Photon that do not know a filter answer
	// HTTP 400: the next level is then used, and remembered for one day. The country and the types of results are
	// checked again on the answer (openstreetmap_photon_parse()), so the suggestions are the same at each level.
	$levels = array(
		'&layer=house&layer=street&countrycode='.urlencode(strtoupper($countrycode)),
		'&layer=house&layer=street',
		'',
	);
	$levelkey = 'photonlevel|'.md5(getDolGlobalString('OPENSTREETMAP_PHOTON_URL', 'https://photon.komoot.io/api'));
	$cachedlevel = openstreetmap_cache_get($levelkey, 86400);
	$level = (isset($cachedlevel['level']) && isset($levels[(int) $cachedlevel['level']])) ? (int) $cachedlevel['level'] : 0;
	$data = 'HTTP_400';
	$nblevels = count($levels);
	$i = $level;
	for (; $i < $nblevels; $i++) {
		$data = openstreetmap_get_json($base.$levels[$i]);
		if ($data !== 'HTTP_400') {
			break;
		}
		dol_syslog("openstreetmap_photon_suggest: filter ".$levels[$i]." refused by Photon (HTTP 400), next level", LOG_DEBUG);
	}
	if (is_array($data) && $i != $level) {
		openstreetmap_cache_set($levelkey, array('level' => $i));
	}
	if (!is_array($data)) {
		return $data;
	}
	$items = openstreetmap_photon_parse($data, $countrycode);
	if (!is_array($items)) {
		return $items;
	}
	openstreetmap_cache_set($key, $items);
	return $items;
}

/**
 *  Convert an answer of Photon (GeoJSON) into address suggestions
 *
 *  @param		array<mixed>	$data			Decoded answer of Photon
 *  @param		string			$countrycode	ISO country code (results of other countries are ignored)
 *  @return		array<int,array<string,mixed>>|string	Suggestions, or 'BAD_RESPONSE'
 */
function openstreetmap_photon_parse($data, $countrycode)
{
	if (!isset($data['features']) || !is_array($data['features'])) {
		return 'BAD_RESPONSE';
	}
	$items = array();
	foreach ($data['features'] as $feature) {
		$prop = (is_array($feature) && isset($feature['properties']) && is_array($feature['properties'])) ? $feature['properties'] : array();
		if (!empty($prop['countrycode']) && strtoupper((string) $prop['countrycode']) != strtoupper($countrycode)) {
			continue;
		}
		$isstreet = (isset($prop['type']) && $prop['type'] == 'street');
		$street = ($isstreet && isset($prop['name'])) ? $prop['name'] : (isset($prop['street']) ? $prop['street'] : '');
		if (!is_string($street) || $street === '') {
			continue;
		}
		$number = (!$isstreet && isset($prop['housenumber']) && is_scalar($prop['housenumber'])) ? (string) $prop['housenumber'] : '';
		$zip = (isset($prop['postcode']) && is_scalar($prop['postcode'])) ? (string) $prop['postcode'] : '';
		$town = '';
		foreach (array('city', 'town', 'village', 'district', 'county') as $k) {
			if (!empty($prop[$k]) && is_string($prop[$k])) {
				$town = $prop[$k];
				break;
			}
		}
		$label = trim($street.' '.$number);
		if ($zip !== '' || $town !== '') {
			$label .= ', '.trim($zip.' '.$town);
		}
		$items[$label] = array(
			'label' => $label,
			'final' => 1,
			'street' => $street,
			'housenumber' => $number,
			'zip' => $zip,
			'town' => $town,
		);
	}
	return array_values($items);
}

/**
 *  Return the SQL request listing the objects shown on the map of all objects, with their cached coordinates.
 *  The same request is used by maps_all.php and ajax/geocode.php, so both apply the same permissions.
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		User		$user		User
 *  @param		string		$mode		'thirdparty', 'contact' or 'member'
 *  @param		array<string,mixed>	$filters	Filters: socid, search_sale, search_tag_customer, search_tag_member,
 *  											search_customer, search_supplier, search_status
 *  @param		int			$onlyid		Id of one object to return, or 0 for all
 *  @return		string					SQL request
 */
function openstreetmap_list_sql($db, $user, $mode, $filters, $onlyid = 0)
{
	global $conf;

	$socid = empty($filters['socid']) ? 0 : (int) $filters['socid'];
	$search_status = isset($filters['search_status']) ? (string) $filters['search_status'] : '';

	$sqlselect = "c.code as country_code, c.label as country,";
	$sqlselect .= " g.rowid as gid, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label";

	if ($mode == 'thirdparty') {
		$sql = "SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town, s.phone, s.email, ".$sqlselect;
		$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON s.fk_pays = c.rowid";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."openstreetmap_geo as g ON s.rowid = g.fk_object AND g.type_object = 'thirdparty' AND g.entity = ".((int) $conf->entity);
		$sql .= " WHERE s.entity IN (".getEntity('societe').")";
		if (!$user->hasRight('societe', 'client', 'voir') && !$socid) {
			$sql .= " AND s.rowid IN (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_user = ".((int) $user->id).")";
		}
		if (!empty($filters['search_sale']) && $filters['search_sale'] > 0) {
			$sql .= " AND s.rowid IN (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_user = ".((int) $filters['search_sale']).")";
		}
		if ($socid > 0) {
			$sql .= " AND s.rowid = ".((int) $socid);
		}
		if (!empty($filters['search_tag_customer']) && $filters['search_tag_customer'] > 0) {
			$sql .= " AND s.rowid IN (SELECT cs.fk_soc FROM ".MAIN_DB_PREFIX."categorie_societe as cs WHERE cs.fk_categorie = ".((int) $filters['search_tag_customer']).")";
		}
		$search_customer = isset($filters['search_customer']) ? (string) $filters['search_customer'] : '';
		if ($search_customer != '' && $search_customer != '-1') {
			$filterclient = '1,2,3';
			if ($search_customer == '2') {
				$filterclient = '2,3';
			}
			if ($search_customer == '1') {
				$filterclient = '1,3';
			}
			$sql .= " AND s.client IN (".$db->sanitize($filterclient).")";
		}
		$search_supplier = isset($filters['search_supplier']) ? (string) $filters['search_supplier'] : '';
		if ($search_supplier != '' && $search_supplier != '-1') {
			$sql .= " AND s.fournisseur = ".((int) $search_supplier);
		}
		if ($search_status != '' && $search_status != '-1') {
			$sql .= " AND s.status = ".((int) $search_status);
		}
	} elseif ($mode == 'contact') {
		$sql = "SELECT s.rowid as id, s.lastname, s.firstname, s.address, s.zip, s.town, s.phone, s.email, ".$sqlselect;
		$sql .= " FROM ".MAIN_DB_PREFIX."socpeople as s";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON s.fk_pays = c.rowid";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."openstreetmap_geo as g ON s.rowid = g.fk_object AND g.type_object = 'contact' AND g.entity = ".((int) $conf->entity);
		$sql .= " WHERE s.entity IN (".getEntity('contact').")";
		$sql .= " AND (s.priv = 0 OR s.fk_user_creat = ".((int) $user->id).")";
		if (!$user->hasRight('societe', 'client', 'voir') && !$socid) {
			$sql .= " AND (s.fk_soc IS NULL OR s.fk_soc IN (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_user = ".((int) $user->id)."))";
		}
		if ($socid > 0) {
			$sql .= " AND s.fk_soc = ".((int) $socid);
		}
		if ($search_status != '' && $search_status != '-1') {
			$sql .= " AND s.statut = ".((int) $search_status);
		}
	} else {
		$sql = "SELECT s.rowid as id, s.lastname, s.firstname, s.societe, s.address, s.zip, s.town, s.phone, s.email, ".$sqlselect;
		$sql .= " FROM ".MAIN_DB_PREFIX."adherent as s";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON s.country = c.rowid";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."openstreetmap_geo as g ON s.rowid = g.fk_object AND g.type_object = 'member' AND g.entity = ".((int) $conf->entity);
		$sql .= " WHERE s.statut = 1";
		$sql .= " AND s.entity IN (".getEntity('adherent').")";
		if (!empty($filters['search_tag_member']) && $filters['search_tag_member'] > 0) {
			$sql .= " AND s.rowid IN (SELECT cs.fk_member FROM ".MAIN_DB_PREFIX."categorie_member as cs WHERE cs.fk_categorie = ".((int) $filters['search_tag_member']).")";
		}
	}
	if ($onlyid > 0) {
		$sql .= " AND s.rowid = ".((int) $onlyid);
	}
	// Addresses never searched first (CASE because NULL values are sorted first by MySQL but last by PostgreSQL)
	$sql .= " ORDER BY (CASE WHEN g.rowid IS NULL THEN 0 ELSE 1 END) ASC, g.rowid ASC, s.rowid ASC";
	return $sql;
}

/**
 *  Return the name of an object of the map of all objects (row of openstreetmap_list_sql())
 *
 *  @param		object		$obj		Row
 *  @return		string					Name
 */
function openstreetmap_list_name($obj)
{
	if (!empty($obj->name)) {
		return (string) $obj->name;
	}
	return trim((!empty($obj->societe) ? $obj->societe.' - ' : '').(isset($obj->firstname) ? $obj->firstname : '').' '.(isset($obj->lastname) ? $obj->lastname : ''));
}

/**
 *  Return the HTML of the popup of a marker on the map of all objects (all values escaped)
 *
 *  @param		string		$mode		'thirdparty', 'contact' or 'member'
 *  @param		object		$obj		Row of openstreetmap_list_sql()
 *  @return		string					HTML
 */
function openstreetmap_list_popup($mode, $obj)
{
	if ($mode == 'thirdparty') {
		$url = DOL_URL_ROOT.'/societe/card.php?socid='.((int) $obj->id);
	} elseif ($mode == 'contact') {
		$url = DOL_URL_ROOT.'/contact/card.php?id='.((int) $obj->id);
	} else {
		$url = DOL_URL_ROOT.'/adherents/card.php?rowid='.((int) $obj->id);
	}
	$popup = '<a href="'.dol_escape_htmltag($url).'"><b>'.dol_escape_htmltag(openstreetmap_list_name($obj)).'</b></a>';
	$popup .= '<br>'.dol_escape_htmltag(dol_format_address($obj, 1, ', '));
	if (!empty($obj->phone)) {
		$popup .= '<br>'.dol_escape_htmltag($obj->phone);
	}
	if (!empty($obj->email)) {
		$popup .= '<br>'.dol_escape_htmltag($obj->email);
	}
	return $popup;
}

/**
 *  Return the JSON of data to put in a script tag, escaped so it can not close the tag
 *  (same escaping as JSON_HEX_TAG and JSON_HEX_AMP)
 *
 *  @param		mixed		$data		Data
 *  @return		string					JSON
 */
function openstreetmap_json_for_script($data)
{
	return str_replace(array('<', '>', '&'), array('\\u003C', '\\u003E', '\\u0026'), (string) json_encode($data));
}
