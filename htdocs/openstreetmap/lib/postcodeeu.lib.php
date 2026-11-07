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
 *	\file			htdocs/openstreetmap/lib/postcodeeu.lib.php
 *  \brief			Optional connector to the international address API of Postcode.eu (paid service, own key).
 *
 *  Only used when the administrator enters a key and selects the countries for it in the setup.
 *  The requests are done by the Dolibarr server; the answers are not stored (no cache), because the
 *  terms of a paid service may not allow it.
 */


/**
 *  Return the countries supported by the international address API of Postcode.eu,
 *  as ISO 3166-1 alpha-2 code => alpha-3 code (the API uses the alpha-3 code as first search context)
 *
 *  @return		array<string,string>
 */
function openstreetmap_postcodeeu_countries()
{
	return array(
		'AT' => 'aut', 'BE' => 'bel', 'CH' => 'che', 'DE' => 'deu', 'DK' => 'dnk', 'ES' => 'esp', 'FI' => 'fin',
		'FR' => 'fra', 'GB' => 'gbr', 'IT' => 'ita', 'LU' => 'lux', 'NL' => 'nld', 'NO' => 'nor', 'SE' => 'swe',
	);
}

/**
 *  Return the countries selected in the setup for Postcode.eu
 *
 *  @return		array<int,string>		ISO 3166-1 alpha-2 codes
 */
function openstreetmap_postcodeeu_selected_countries()
{
	$selected = array();
	$supported = openstreetmap_postcodeeu_countries();
	foreach (explode(',', getDolGlobalString('OPENSTREETMAP_POSTCODEEU_COUNTRIES')) as $code) {
		$code = strtoupper(trim($code));
		if (isset($supported[$code])) {
			$selected[] = $code;
		}
	}
	return $selected;
}

/**
 *  Tell if Postcode.eu must be used for the address suggestions of a country
 *
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @return		bool
 */
function openstreetmap_postcodeeu_enabled_for($countrycode)
{
	if (!getDolGlobalString('OPENSTREETMAP_POSTCODEEU_KEY') || !getDolGlobalString('OPENSTREETMAP_POSTCODEEU_SECRET')) {
		return false;
	}
	return in_array(strtoupper((string) $countrycode), openstreetmap_postcodeeu_selected_countries());
}

/**
 *  Return the language tag to send to Postcode.eu for the language of the user.
 *  The API only knows some languages (en-GB, en-US, nl-NL, de-DE, da-DK, es-ES, fi-FI, fr-BE, fr-FR, no-NO). For any
 *  other language (for example Greek), no language is sent and the API answers in its default language (en-GB).
 *
 *  @param		string		$lang		Language of the user (for example fr_BE, el_GR, or 2 letters)
 *  @return		string					Language tag, or '' to send none
 */
function openstreetmap_postcodeeu_lang($lang)
{
	$supported = array('en-GB', 'en-US', 'nl-NL', 'de-DE', 'da-DK', 'es-ES', 'fi-FI', 'fr-BE', 'fr-FR', 'no-NO');
	$lang = str_replace('_', '-', trim((string) $lang));
	if (preg_match('/^([a-z]{2})-([a-z]{2})$/i', $lang, $reg)) {
		$tag = strtolower($reg[1]).'-'.strtoupper($reg[2]);
		if (in_array($tag, $supported)) {
			return $tag;
		}
	}
	// Same language, other country (for example de_AT -> de-DE, nb_NO -> no-NO)
	$short = strtolower(substr($lang, 0, 2));
	if ($short == 'nb' || $short == 'nn') {
		$short = 'no';
	}
	foreach ($supported as $tag) {
		if (strpos($tag, $short.'-') === 0) {
			return $tag;
		}
	}
	return '';
}

/**
 *  Tell if the key and the secret of Postcode.eu are stored encrypted in the database.
 *  Dolibarr encrypts the settings with a sensitive name (ending with _KEY, _SECRET...) only in recent versions;
 *  older versions store them as typed. The setup page shows the result, so the administrator knows.
 *
 *  @param		DoliDB		$db			Database handler
 *  @return		int						1 if all stored values are encrypted, 0 if one is not, -1 if nothing is stored
 */
function openstreetmap_postcodeeu_stored_encrypted($db)
{
	global $conf;

	$names = array("'OPENSTREETMAP_POSTCODEEU_KEY'", "'OPENSTREETMAP_POSTCODEEU_SECRET'");
	$sql = "SELECT ".$db->decrypt('value')." as value FROM ".MAIN_DB_PREFIX."const";
	$sql .= " WHERE ".$db->decrypt('name')." IN (".implode(',', $names).")";
	$sql .= " AND entity IN (0, ".((int) $conf->entity).")";
	$resql = $db->query($sql);
	if (!$resql) {
		dol_syslog("openstreetmap_postcodeeu_stored_encrypted: ".$db->lasterror(), LOG_WARNING);
		return -1;
	}
	$found = 0;
	while ($obj = $db->fetch_object($resql)) {
		$found++;
		if (strpos((string) $obj->value, 'dolcrypt:') !== 0) {
			return 0;
		}
	}
	return $found ? 1 : -1;
}

/**
 *  Return the identifier of the autocomplete session, required by Postcode.eu for the billing of a search
 *  (all requests of a user while filling one address belong to the same session)
 *
 *  @return		string		8 to 64 characters among a-z, 0-9, '-', '_' and '.'
 */
function openstreetmap_postcodeeu_session()
{
	if (empty($_SESSION['openstreetmap_postcodeeu_session']) || !preg_match('/^[a-z0-9]{32}$/', (string) $_SESSION['openstreetmap_postcodeeu_session'])) {
		$_SESSION['openstreetmap_postcodeeu_session'] = bin2hex(random_bytes(16));
	}
	return $_SESSION['openstreetmap_postcodeeu_session'];
}

/**
 *  Call the API of Postcode.eu
 *
 *  @param		string		$path		Path after the base URL, with each part already encoded
 *  @return		array<mixed>|string		Decoded JSON if OK, error message string if KO
 */
function openstreetmap_postcodeeu_call($path)
{
	$base = getDolGlobalString('OPENSTREETMAP_POSTCODEEU_URL', 'https://api.postcode.eu/');
	if (!preg_match('/^https:\/\//i', $base)) {
		return 'BAD_URL';
	}
	$headers = array(
		'Authorization: Basic '.base64_encode(getDolGlobalString('OPENSTREETMAP_POSTCODEEU_KEY').':'.getDolGlobalString('OPENSTREETMAP_POSTCODEEU_SECRET')),
		'X-Autocomplete-Session: '.openstreetmap_postcodeeu_session(),
	);
	return openstreetmap_get_json(rtrim($base, '/').'/'.$path, $headers);
}

/**
 *  Return address suggestions from Postcode.eu.
 *  A suggestion with 'final' = 0 is a street or a town: searching again with its 'context' lists what is in it.
 *  A suggestion with 'final' = 1 is an address: its 'context' gives the details with openstreetmap_postcodeeu_details().
 *
 *  @param		string		$q				Text typed by the user
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @param		string		$lang			Language of the user (for example fr_BE or el_GR)
 *  @param		string		$context		Context of a previous suggestion, or '' to search in the whole country
 *  @return		array<int,array<string,mixed>>|string	Suggestions, or error message string if KO
 */
function openstreetmap_postcodeeu_suggest($q, $countrycode, $lang = '', $context = '')
{
	$countries = openstreetmap_postcodeeu_countries();
	$countrycode = strtoupper((string) $countrycode);
	if (!isset($countries[$countrycode])) {
		return 'BAD_COUNTRY';
	}
	if ($context === '' || !preg_match('/^[^\s\/\\\\]{1,300}$/', $context)) {
		$context = $countries[$countrycode];
	}
	$path = 'international/v1/autocomplete/'.rawurlencode($context).'/'.rawurlencode(trim((string) $q));
	$tag = openstreetmap_postcodeeu_lang($lang);
	$data = openstreetmap_postcodeeu_call($path.($tag !== '' ? '/'.rawurlencode($tag) : ''));
	if ($data === 'HTTP_400' && $tag !== '') {
		// Language refused: search again in the default language of the API
		dol_syslog("openstreetmap_postcodeeu_suggest: language ".$tag." refused (HTTP 400), search again without language", LOG_DEBUG);
		$data = openstreetmap_postcodeeu_call($path);
	}
	if (!is_array($data)) {
		return $data;
	}
	return openstreetmap_postcodeeu_parse_suggest($data);
}

/**
 *  Convert an autocomplete answer of Postcode.eu into address suggestions
 *
 *  @param		array<mixed>	$data		Decoded answer
 *  @return		array<int,array<string,mixed>>|string	Suggestions, or 'BAD_RESPONSE'
 */
function openstreetmap_postcodeeu_parse_suggest($data)
{
	if (!isset($data['matches']) || !is_array($data['matches'])) {
		return 'BAD_RESPONSE';
	}
	$items = array();
	foreach ($data['matches'] as $match) {
		if (!is_array($match) || empty($match['context']) || !is_string($match['context']) || !isset($match['label'])) {
			continue;
		}
		$label = (string) $match['label'];
		if (!empty($match['description']) && is_string($match['description'])) {
			$label .= ' - '.$match['description'];
		}
		$items[] = array(
			'label' => $label,
			'value' => isset($match['value']) ? (string) $match['value'] : (string) $match['label'],
			'final' => (isset($match['precision']) && $match['precision'] == 'Address') ? 1 : 0,
			'context' => $match['context'],
		);
	}
	return $items;
}

/**
 *  Return the details of an address chosen in the suggestions of Postcode.eu
 *
 *  @param		string		$context		Context of the chosen suggestion
 *  @return		array<string,string>|string	array('street'=>, 'housenumber'=>, 'zip'=>, 'town'=>), or error message string if KO
 */
function openstreetmap_postcodeeu_details($context)
{
	if (!preg_match('/^[^\s\/\\\\]{1,300}$/', (string) $context)) {
		return 'BAD_CONTEXT';
	}
	$data = openstreetmap_postcodeeu_call('international/v1/address/'.rawurlencode($context));
	if (!is_array($data)) {
		return $data;
	}
	return openstreetmap_postcodeeu_parse_details($data);
}

/**
 *  Convert a details answer of Postcode.eu into the parts of an address
 *
 *  @param		array<mixed>	$data		Decoded answer
 *  @return		array<string,string>|string	array('street'=>, 'housenumber'=>, 'zip'=>, 'town'=>), or 'BAD_RESPONSE'
 */
function openstreetmap_postcodeeu_parse_details($data)
{
	if (!isset($data['address']) || !is_array($data['address']) || empty($data['address']['street'])) {
		return 'BAD_RESPONSE';
	}
	$a = $data['address'];
	$number = trim((isset($a['buildingNumber']) && is_scalar($a['buildingNumber']) ? (string) $a['buildingNumber'] : '').' '.(isset($a['buildingNumberAddition']) && is_scalar($a['buildingNumberAddition']) ? (string) $a['buildingNumberAddition'] : ''));
	return array(
		'street' => (string) $a['street'],
		'housenumber' => $number,
		'zip' => (isset($a['postcode']) && is_scalar($a['postcode'])) ? (string) $a['postcode'] : '',
		'town' => (isset($a['locality']) && is_scalar($a['locality'])) ? (string) $a['locality'] : '',
	);
}
