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
 *       \file       htdocs/openstreetmap/ajax/pdok.php
 *       \ingroup    openstreetmap
 *       \brief      Return street and town of a Dutch address from postcode and house number (PDOK Locatieserver).
 *                   The request is done by the Dolibarr server, so the browser of the user never contacts an external service.
 */

if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', '1');
if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', '1');
if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', '1');
if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
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

dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

top_httphead('application/json');

/**
 * Print a JSON answer and exit
 *
 * @param	array	$data		Data to return
 * @param	int		$httpcode	HTTP code
 * @return	void
 */
function openstreetmap_pdok_answer($data, $httpcode = 200)
{
	http_response_code($httpcode);
	print json_encode($data);
	exit;
}

if (!isModEnabled('openstreetmap') || !getDolGlobalString('OPENSTREETMAP_PDOK_AUTOFILL')) {
	openstreetmap_pdok_answer(array('error' => 'DISABLED'), 403);
}
if (!$user->hasRight('societe', 'lire') && !$user->hasRight('societe', 'contact', 'lire') && !$user->hasRight('adherent', 'lire')) {
	openstreetmap_pdok_answer(array('error' => 'FORBIDDEN'), 403);
}

$postcode = strtoupper(str_replace(' ', '', GETPOST('postcode', 'alphanohtml')));
$huisnummer = GETPOST('huisnummer', 'alphanohtml');

if (!preg_match('/^[1-9][0-9]{3}[A-Z]{2}$/', $postcode)) {
	openstreetmap_pdok_answer(array('error' => 'BAD_POSTCODE'), 400);
}
// House number with optional letter or addition, for example "10", "10A", "10-2", "10 bis"
if (!preg_match('/^([0-9]{1,5})\s*[-\s]?\s*([A-Za-z0-9]{0,4})$/', trim($huisnummer), $reg)) {
	openstreetmap_pdok_answer(array('error' => 'BAD_HOUSENUMBER'), 400);
}
$nr = (int) $reg[1];
$suffix = strtoupper($reg[2]);

$docs = openstreetmap_pdok_search('postcode:'.$postcode.' and huisnummer:'.$nr, 'adres', 50);
if (!is_array($docs)) {
	openstreetmap_pdok_answer(array('error' => 'SERVICE_UNAVAILABLE', 'detail' => $docs), 502);
}

// Keep only exact matches on postcode and house number, then prefer the matching letter/addition
$best = null;
foreach ($docs as $doc) {
	if (empty($doc['postcode']) || strtoupper($doc['postcode']) != $postcode || (int) $doc['huisnummer'] != $nr) {
		continue;
	}
	$docsuffix = strtoupper((isset($doc['huisletter']) ? $doc['huisletter'] : '').(isset($doc['huisnummertoevoeging']) ? $doc['huisnummertoevoeging'] : ''));
	if ($docsuffix == $suffix) {
		$best = $doc;
		break;
	}
	if ($best === null) {
		$best = $doc;
	}
}
if ($best === null) {
	openstreetmap_pdok_answer(array('error' => 'NOT_FOUND'), 404);
}

$number = $nr.(!empty($best['huisletter']) ? $best['huisletter'] : '').(!empty($best['huisnummertoevoeging']) ? '-'.$best['huisnummertoevoeging'] : '');
if ($suffix && empty($best['huisletter']) && empty($best['huisnummertoevoeging'])) {
	// Keep the addition typed by the user when the BAG has none
	$number = $nr.$suffix;
}
$point = openstreetmap_pdok_parse_point(isset($best['centroide_ll']) ? $best['centroide_ll'] : '');

openstreetmap_pdok_answer(array(
	'street' => $best['straatnaam'],
	'number' => $number,
	'address' => $best['straatnaam'].' '.$number,
	'zip' => substr($postcode, 0, 4).' '.substr($postcode, 4),
	'town' => $best['woonplaatsnaam'],
	'municipality' => isset($best['gemeentenaam']) ? $best['gemeentenaam'] : '',
	'province' => isset($best['provincienaam']) ? $best['provincienaam'] : '',
	'lat' => $point ? $point['lat'] : null,
	'lon' => $point ? $point['lon'] : null,
));
