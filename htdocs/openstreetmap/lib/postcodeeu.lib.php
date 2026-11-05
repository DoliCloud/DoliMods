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
 *  @param		string		$lang			Language of the user (2 letters)
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
	if (preg_match('/^[a-z]{2}$/', (string) $lang)) {
		$path .= '/'.rawurlencode($lang);
	}
	$data = openstreetmap_postcodeeu_call($path);
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
