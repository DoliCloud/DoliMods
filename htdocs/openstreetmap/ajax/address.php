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
 *       \brief      Address autofill on the forms of third parties, contacts and members:
 *                   lookup=zip     -> towns of a postcode (dictionary of Dolibarr, then Nominatim, cached)
 *                   lookup=suggest -> addresses matching the typed text, with the service chosen for the country
 *                                     (Photon on OpenStreetMap data by default, PDOK for the Netherlands, Postcode.eu if set up)
 *                   lookup=details -> parts of an address chosen in the suggestions of Postcode.eu
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
dol_include_once("/openstreetmap/lib/postcodeeu.lib.php");

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

if (!isModEnabled('openstreetmap')) {
	openstreetmap_address_answer(array('error' => 'DISABLED'), 403);
}
if (!$user->hasRight('societe', 'lire') && !$user->hasRight('societe', 'contact', 'lire') && !$user->hasRight('adherent', 'lire')) {
	openstreetmap_address_answer(array('error' => 'FORBIDDEN'), 403);
}

$lookup = GETPOST('lookup', 'aZ09');
$countryid = GETPOSTINT('country_id');
$countrycode = openstreetmap_country_code($db, $countryid);
if ($countrycode == '') {
	openstreetmap_address_answer(array('error' => 'BAD_COUNTRY'), 400);
}
$lang = substr((string) $langs->defaultlang, 0, 2);
$provider = openstreetmap_address_provider($countrycode);

// The answer may take a few seconds: release the session so the other pages of the user are not blocked
if ($provider == 'postcodeeu') {
	openstreetmap_postcodeeu_session();
}
if (session_status() == PHP_SESSION_ACTIVE) {
	session_write_close();
}

if ($lookup == 'zip') {
	if (!getDolGlobalString('OPENSTREETMAP_EU_AUTOFILL')) {
		openstreetmap_address_answer(array('error' => 'DISABLED'), 403);
	}
	$zip = trim(GETPOST('zip', 'alphanohtml'));
	if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9 \-]{1,9}$/', $zip)) {
		openstreetmap_address_answer(array('error' => 'BAD_POSTCODE'), 400);
	}
	$towns = openstreetmap_postcode_towns($db, $countryid, $countrycode, $zip);
	if (!is_array($towns)) {
		dol_syslog("openstreetmap ajax/address.php: Nominatim request failed: ".$towns, LOG_WARNING);
		openstreetmap_address_answer(array('error' => 'SERVICE_UNAVAILABLE'), 502);
	}
	openstreetmap_address_answer(array('towns' => $towns));
}

if ($lookup == 'suggest') {
	if ($provider == '') {
		openstreetmap_address_answer(array('error' => 'DISABLED'), 403);
	}
	$q = trim(GETPOST('q', 'alphanohtml'));
	$context = GETPOST('context', 'alphanohtml');
	if (($context === '' && dol_strlen($q) < 3) || dol_strlen($q) > 100 || dol_strlen($context) > 300) {
		openstreetmap_address_answer(array('error' => 'BAD_QUERY'), 400);
	}
	$hint = trim(GETPOST('zip', 'alphanohtml'));
	if ($hint === '' || dol_strlen($hint) > 10) {
		$hint = trim(GETPOST('town', 'alphanohtml'));
	}
	$result = openstreetmap_address_suggest($q, $countrycode, $lang, $context, dol_trunc($hint, 100, 'right', 'UTF-8', 1));
	if (!is_array($result)) {
		dol_syslog("openstreetmap ajax/address.php: ".$provider." request failed: ".$result, LOG_WARNING);
		openstreetmap_address_answer(array('error' => 'SERVICE_UNAVAILABLE', 'provider' => $provider), 502);
	}
	openstreetmap_address_answer($result);
}

if ($lookup == 'details') {
	if ($provider != 'postcodeeu') {
		openstreetmap_address_answer(array('error' => 'DISABLED'), 403);
	}
	$details = openstreetmap_postcodeeu_details(GETPOST('context', 'alphanohtml'));
	if (!is_array($details)) {
		dol_syslog("openstreetmap ajax/address.php: Postcode.eu details failed: ".$details, LOG_WARNING);
		openstreetmap_address_answer(array('error' => 'SERVICE_UNAVAILABLE', 'provider' => $provider), 502);
	}
	openstreetmap_address_answer(array('address' => $details));
}

openstreetmap_address_answer(array('error' => 'BAD_LOOKUP'), 400);
