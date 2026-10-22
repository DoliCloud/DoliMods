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
 *       \file       htdocs/openstreetmap/ajax/address.php
 *       \ingroup    openstreetmap
 *       \brief      Address autofill for countries other than the Netherlands:
 *                   lookup=zip    -> towns of a postcode (Nominatim, one request per postcode, cached)
 *                   lookup=street -> streets matching the typed text (Photon, search-as-you-type, cached)
 *                   The request is done by the Dolibarr server, so the browser of the user never contacts an external service.
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
function openstreetmap_address_answer($data, $httpcode = 200)
{
	http_response_code($httpcode);
	print json_encode($data);
	exit;
}

if (!isModEnabled('openstreetmap') || !getDolGlobalString('OPENSTREETMAP_EU_AUTOFILL')) {
	openstreetmap_address_answer(array('error' => 'DISABLED'), 403);
}
if (!$user->hasRight('societe', 'lire') && !$user->hasRight('societe', 'contact', 'lire') && !$user->hasRight('adherent', 'lire')) {
	openstreetmap_address_answer(array('error' => 'FORBIDDEN'), 403);
}

$lookup = GETPOST('lookup', 'aZ09');
$countrycode = openstreetmap_country_code($db, GETPOSTINT('country_id'));
if ($countrycode == '' || $countrycode == 'NL') {
	// The Netherlands use PDOK (ajax/pdok.php)
	openstreetmap_address_answer(array('error' => 'BAD_COUNTRY'), 400);
}
$zip = trim(GETPOST('zip', 'alphanohtml'));
if ($zip !== '' && !preg_match('/^[A-Za-z0-9][A-Za-z0-9 \-]{1,9}$/', $zip)) {
	openstreetmap_address_answer(array('error' => 'BAD_POSTCODE'), 400);
}

if ($lookup == 'zip') {
	if ($zip === '') {
		openstreetmap_address_answer(array('error' => 'BAD_POSTCODE'), 400);
	}
	$towns = openstreetmap_postcode_towns($zip, $countrycode);
	if (!is_array($towns)) {
		dol_syslog("openstreetmap ajax/address.php: Nominatim request failed: ".$towns, LOG_WARNING);
		openstreetmap_address_answer(array('error' => 'SERVICE_UNAVAILABLE'), 502);
	}
	openstreetmap_address_answer(array('towns' => $towns));
}

if ($lookup == 'street') {
	$q = trim(GETPOST('q', 'alphanohtml'));
	$town = trim(GETPOST('town', 'alphanohtml'));
	if (dol_strlen($q) < 3 || dol_strlen($q) > 100 || dol_strlen($town) > 100) {
		openstreetmap_address_answer(array('error' => 'BAD_QUERY'), 400);
	}
	$streets = openstreetmap_photon_streets($q, $countrycode, $zip, $town, substr((string) $langs->defaultlang, 0, 2));
	if (!is_array($streets)) {
		dol_syslog("openstreetmap ajax/address.php: Photon request failed: ".$streets, LOG_WARNING);
		openstreetmap_address_answer(array('error' => 'SERVICE_UNAVAILABLE'), 502);
	}
	openstreetmap_address_answer(array('streets' => $streets));
}

openstreetmap_address_answer(array('error' => 'BAD_LOOKUP'), 400);
