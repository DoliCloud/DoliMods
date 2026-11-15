<?php
/* This program is free software; you can redistribute it and/or modify
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
 */

/**
 *       \file       htdocs/openstreetmap/ajax/geocode.php
 *       \ingroup    openstreetmap
 *       \brief      Locate one object of the map of all objects (maps_all.php) and return its marker.
 *                   Called by the browser one object at a time, after the map is shown, so the page of the map
 *                   never waits for an external service. The object is read with the same request and the same
 *                   permissions as the map. The session is released at once, so the other pages of the user
 *                   are not blocked while the external service answers.
 */

if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', '1');
if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', '1');
if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', '1');
if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
// @phpstan-ignore booleanNot.alwaysTrue (standard header of module pages, $res is still 0 here)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 * @var Societe $mysoc
 */

dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

top_httphead('application/json');

/**
 * Print a JSON answer and exit
 *
 * @param	array<string,mixed>	$data		Data to return
 * @param	int		$httpcode	HTTP code
 * @return	void
 */
function openstreetmap_geocode_answer($data, $httpcode = 200)
{
	http_response_code($httpcode);
	print json_encode($data);
	exit;
}

$mode = GETPOST('mode', 'aZ09');
$id = GETPOSTINT('id');
$socid = GETPOSTINT('socid');

// Security check (same as maps_all.php)
if (!isModEnabled('openstreetmap')) {
	openstreetmap_geocode_answer(array('error' => 'DISABLED'), 403);
}
if (!empty($user->socid)) {
	$socid = $user->socid;
}
if ($mode == 'thirdparty') {
	$allowed = $user->hasRight('societe', 'lire');
} elseif ($mode == 'contact') {
	$allowed = $user->hasRight('societe', 'contact', 'lire');
} elseif ($mode == 'member') {
	$allowed = $user->hasRight('adherent', 'lire');
} else {
	openstreetmap_geocode_answer(array('error' => 'BAD_MODE'), 400);
}
if (empty($allowed)) {
	openstreetmap_geocode_answer(array('error' => 'FORBIDDEN'), 403);
}
if ($id <= 0) {
	openstreetmap_geocode_answer(array('error' => 'BAD_ID'), 400);
}

if (session_status() == PHP_SESSION_ACTIVE) {
	session_write_close();
}
@set_time_limit(60);

$filters = array(
	'socid' => $socid,
	'search_sale' => GETPOSTINT('search_sale'),
	'search_tag_customer' => GETPOSTINT('search_tag_customer'),
	'search_tag_member' => GETPOSTINT('search_tag_member'),
	'search_customer' => GETPOST('search_customer', 'alpha'),
	'search_supplier' => GETPOST('search_supplier', 'alpha'),
	'search_status' => GETPOST('search_status', 'intcomma'),
);
$resql = $db->query(openstreetmap_list_sql($db, $user, $mode, $filters, $id));
if (!$resql) {
	dol_syslog("openstreetmap ajax/geocode.php: ".$db->lasterror(), LOG_ERR);
	openstreetmap_geocode_answer(array('error' => 'ERROR'), 500);
}
$obj = $db->fetch_object($resql);
if (!$obj) {
	openstreetmap_geocode_answer(array('error' => 'NOT_FOUND'), 404);
}
if (empty($obj->country_code)) {
	$obj->country_code = $mysoc->country_code;
}

$cached = array('rowid' => $obj->gid, 'latitude' => $obj->latitude, 'longitude' => $obj->longitude, 'address' => $obj->gaddress, 'result_code' => $obj->result_code, 'result_label' => $obj->result_label);
$point = openstreetmap_get_coordinates($db, $mode, $obj->id, $obj, $cached, 0);

if (isset($point['lat'])) {
	openstreetmap_geocode_answer(array('id' => (int) $obj->id, 'lat' => (float) $point['lat'], 'lon' => (float) $point['lon'], 'popup' => openstreetmap_list_popup($mode, $obj)));
}
$answer = array('id' => (int) $obj->id, 'error' => $point['error'], 'label' => $point['label'], 'name' => openstreetmap_list_name($obj), 'address' => dol_format_address($obj, 1, ', '));
if ($point['error'] != 'ZERO_RESULTS' && $point['error'] != 'NO_ADDRESS') {
	// The service did not answer: the address will be searched again on a next visit
	$answer['error'] = 'SERVICE_UNAVAILABLE';
}
openstreetmap_geocode_answer($answer);
