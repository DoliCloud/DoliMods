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
 *	\file			htdocs/openstreetmap/lib/ziptownimport.lib.php
 *  \brief			Import of zip codes and towns into the dictionary of Dolibarr (llx_c_ziptown).
 *
 *  The dictionary belongs to Dolibarr: it is also used by Dolibarr itself for the autocompletion of zip codes and
 *  towns (option MAIN_USE_ZIPTOWN_DICTIONNARY). Only missing zip code / town pairs are added, existing rows (for
 *  example those of the module zipautofillfr) are never changed. Source: GeoNames postal codes (CC BY 4.0), as a
 *  download or as an uploaded file, or a simple CSV file "zip;town".
 */


/**
 *  Return the URL of the GeoNames postal codes file of a country
 *
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @return		string						URL
 */
function openstreetmap_ziptown_geonames_url($countrycode)
{
	return getDolGlobalString('OPENSTREETMAP_GEONAMES_URL', 'https://download.geonames.org/export/zip/').strtoupper($countrycode).'.zip';
}

/**
 *  Download the GeoNames postal codes of a country and return the content of its text file
 *
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @return		string|int					Content of the file, or <0 if KO (-1 download, -2 file, -3 zip)
 */
function openstreetmap_ziptown_download_geonames($countrycode)
{
	global $conf;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

	$url = openstreetmap_ziptown_geonames_url($countrycode);
	if (!preg_match('/^https:\/\//i', $url)) {
		return -1;
	}
	$saved = isset($conf->global->MAIN_USE_RESPONSE_TIMEOUT) ? $conf->global->MAIN_USE_RESPONSE_TIMEOUT : null;
	$conf->global->MAIN_USE_RESPONSE_TIMEOUT = 60;
	$result = getURLContent($url, 'GET', '', 1, array('User-Agent: Dolibarr-module-OpenStreetMap'), array('https'), 0, -1);
	if ($saved === null) {
		unset($conf->global->MAIN_USE_RESPONSE_TIMEOUT);
	} else {
		$conf->global->MAIN_USE_RESPONSE_TIMEOUT = $saved;
	}
	if (!empty($result['curl_error_no']) || empty($result['http_code']) || $result['http_code'] != 200 || empty($result['content'])) {
		dol_syslog("openstreetmap_ziptown_download_geonames: download of ".$url." failed: ".(!empty($result['curl_error_msg']) ? $result['curl_error_msg'] : 'HTTP '.(empty($result['http_code']) ? '' : $result['http_code'])), LOG_WARNING);
		return -1;
	}
	return openstreetmap_ziptown_read_zip($result['content'], strtoupper($countrycode).'.txt');
}

/**
 *  Return the content of the text file in a zip archive (in memory)
 *
 *  @param		string		$zipcontent		Content of the zip archive
 *  @param		string		$entry			Name of the entry to read, or '' for the first .txt or .csv entry
 *  @return		string|int					Content, or <0 if KO (-2 file, -3 zip, -4 content larger than 30 MB)
 */
function openstreetmap_ziptown_read_zip($zipcontent, $entry = '')
{
	if (!class_exists('ZipArchive')) {
		return -3;
	}
	include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$dir = DOL_DATA_ROOT.'/openstreetmap/temp';
	if (!is_dir($dir) && dol_mkdir($dir) < 0) {
		return -2;
	}
	$file = $dir.'/ziptown_'.bin2hex(random_bytes(8)).'.zip';
	if (file_put_contents($file, $zipcontent) === false) {
		return -2;
	}
	$content = -3;
	$zip = new ZipArchive();
	if ($zip->open($file) === true) {
		if ($entry === '' || $zip->locateName($entry) === false) {
			$entry = '';
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$name = (string) $zip->getNameIndex($i);
				if (preg_match('/\.(txt|csv)$/i', $name) && !preg_match('/readme/i', $name)) {
					$entry = $name;
					break;
				}
			}
		}
		if ($entry !== '') {
			// Check the size before reading: a very large file (for example GB_full, 178 MB) would exhaust the memory
			$stat = $zip->statName($entry);
			if (is_array($stat) && $stat['size'] > 30 * 1024 * 1024) {
				$content = -4;
			} else {
				$data = $zip->getFromName($entry);
				if ($data !== false) {
					$content = $data;
				}
			}
		}
		$zip->close();
	}
	dol_delete_file($file, 0, 1);
	return $content;
}

/**
 *  Read the zip code / town pairs of a file.
 *  Accepted formats: GeoNames postal codes (tab separated, country code in first column),
 *  or a simple list with the zip code and the town separated by a tab, a semicolon or a comma.
 *
 *  @param		string		$content		Content of the file (UTF-8)
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code (rows of GeoNames for other countries are ignored)
 *  @return		array<int,array{0:string,1:string}>	List of array(zip, town), without duplicates
 */
function openstreetmap_ziptown_parse($content, $countrycode)
{
	$rows = array();
	$seen = array();
	$countrycode = strtoupper($countrycode);
	foreach (preg_split('/\r\n|\r|\n/', (string) $content) as $line) {
		if (trim($line) === '' || strpos($line, '#') === 0) {
			continue;
		}
		if (strpos($line, "\t") !== false) {
			$cols = explode("\t", $line);
			if (count($cols) >= 3) {
				// GeoNames postal codes: country code, postal code, place name, ... Other files with more than
				// 2 columns (for example the GeoNames gazetteer, which has no postal code) are not read.
				if (!preg_match('/^[A-Z]{2}$/', $cols[0]) || $cols[0] != $countrycode) {
					continue;
				}
				$zip = $cols[1];
				$town = $cols[2];
			} else {
				$zip = $cols[0];
				$town = $cols[1];
			}
		} else {
			$cols = str_getcsv($line, (strpos($line, ';') !== false ? ';' : ','), '"', '\\');
			$zip = isset($cols[0]) ? (string) $cols[0] : '';
			$town = isset($cols[1]) ? (string) $cols[1] : '';
		}
		$zip = trim($zip);
		$town = trim(preg_replace('/\s+/', ' ', $town));
		// The dictionary of Dolibarr has 10 characters for a zip code and 180 for a town
		if ($zip === '' || $town === '' || dol_strlen($zip) > 10 || dol_strlen($town) > 180 || !preg_match('/^[A-Za-z0-9][A-Za-z0-9 \-]*$/', $zip)) {
			continue;
		}
		$key = $zip.'|'.mb_strtolower($town);
		if (isset($seen[$key])) {
			continue;
		}
		$seen[$key] = 1;
		$rows[] = array($zip, $town);
	}
	return $rows;
}

/**
 *  Tell if a file is a GeoNames gazetteer extract (export/dump/XX.zip: all geographical names of a country,
 *  19 tab separated columns starting with the geonameid, without postal codes) instead of the postal codes
 *  (export/zip/XX.zip)
 *
 *  @param		string		$content		Content of the file
 *  @return		bool
 */
function openstreetmap_ziptown_is_gazetteer($content)
{
	$line = strtok(ltrim((string) substr((string) $content, 0, 65536)), "\r\n");
	if ($line === false) {
		return false;
	}
	$cols = explode("\t", $line);
	return (count($cols) >= 19 && ctype_digit($cols[0]));
}

/**
 *  Add the zip code / town pairs that are not yet in the dictionary of a country
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		int			$countryid	Id of country in llx_c_country
 *  @param		array<int,array{0:string,1:string}>	$rows	List of array(zip, town)
 *  @return		array{added:int,existing:int}|int	Numbers of rows added and already present, or -1 if KO
 */
function openstreetmap_ziptown_import($db, $countryid, $rows)
{
	if ($countryid <= 0) {
		return -1;
	}
	// Pairs already in the dictionary (compared without case, so "Athina" and "ATHINA" are the same town)
	$existing = array();
	$resql = $db->query("SELECT zip, town FROM ".MAIN_DB_PREFIX."c_ziptown WHERE fk_pays = ".((int) $countryid));
	if (!$resql) {
		dol_syslog("openstreetmap_ziptown_import: ".$db->lasterror(), LOG_ERR);
		return -1;
	}
	while ($obj = $db->fetch_object($resql)) {
		$existing[$obj->zip.'|'.mb_strtolower($obj->town)] = 1;
	}

	$added = 0;
	$nbexisting = 0;
	$values = array();
	foreach ($rows as $row) {
		$key = $row[0].'|'.mb_strtolower($row[1]);
		if (isset($existing[$key])) {
			$nbexisting++;
			continue;
		}
		$existing[$key] = 1;
		$values[] = "('".$db->escape($row[0])."', '".$db->escape($row[1])."', '".$db->escape(dol_strtoupper($row[1]))."', ".((int) $countryid).", 1)";
		if (count($values) >= 200) {
			$nb = openstreetmap_ziptown_insert($db, $values);
			$added += $nb;
			$nbexisting += count($values) - $nb;
			$values = array();
		}
	}
	if (count($values)) {
		$nb = openstreetmap_ziptown_insert($db, $values);
		$added += $nb;
		$nbexisting += count($values) - $nb;
	}
	return array('added' => $added, 'existing' => $nbexisting);
}

/**
 *  Insert rows into the dictionary of zip codes and towns.
 *  The rows are inserted together; if that fails (a pair already present in another writing, for example with
 *  accents, is refused by the unique index of the dictionary), they are inserted one by one and the refused ones
 *  are skipped.
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		array<int,string>	$values	SQL values, already escaped: ('zip', 'town', 'TOWN', fk_pays, active)
 *  @return		int						Number of rows inserted
 */
function openstreetmap_ziptown_insert($db, $values)
{
	$sqlstart = "INSERT INTO ".MAIN_DB_PREFIX."c_ziptown (zip, town, town_up, fk_pays, active) VALUES ";
	if ($db->query($sqlstart.implode(', ', $values))) {
		return count($values);
	}
	$nb = 0;
	foreach ($values as $value) {
		if ($db->query($sqlstart.$value)) {
			$nb++;
		}
	}
	return $nb;
}

/**
 *  Return the number of zip code / town pairs in the dictionary, by country
 *
 *  @param		DoliDB		$db			Database handler
 *  @return		array<int,array{code:string,label:string,nb:int}>	List of countries that have rows
 */
function openstreetmap_ziptown_counts($db)
{
	$counts = array();
	$sql = "SELECT c.code, c.label, COUNT(z.rowid) as nb FROM ".MAIN_DB_PREFIX."c_ziptown as z";
	$sql .= " INNER JOIN ".MAIN_DB_PREFIX."c_country as c ON c.rowid = z.fk_pays";
	$sql .= " GROUP BY c.code, c.label ORDER BY c.label";
	$resql = $db->query($sql);
	if ($resql) {
		while ($obj = $db->fetch_object($resql)) {
			$counts[] = array('code' => (string) $obj->code, 'label' => (string) $obj->label, 'nb' => (int) $obj->nb);
		}
	}
	return $counts;
}
