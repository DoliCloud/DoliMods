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
 *
 *  A file is not imported at once: it is put in a queue (DOL_DATA_ROOT/openstreetmap/ziptown), extracted on disk,
 *  then read line by line in small parts, by the scheduled job of the module and while the setup page is open.
 *  So the memory used does not depend on the size of the file, and no request of the web server runs for long.
 */

include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


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
 *  Return the directory of the queue of imports (created if needed)
 *
 *  @return		string		Directory, or '' if it can not be created
 */
function openstreetmap_ziptown_queue_dir()
{
	$dir = DOL_DATA_ROOT.'/openstreetmap/ziptown';
	if (!is_dir($dir) && dol_mkdir($dir) < 0) {
		return '';
	}
	return $dir;
}

/**
 *  Download the GeoNames postal codes file of a country into a file
 *
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @param		string		$destfile		File to write
 *  @return		int							1 if OK, -1 if KO
 */
function openstreetmap_ziptown_download_geonames($countrycode, $destfile)
{
	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	dol_include_once('/openstreetmap/lib/openstreetmap.lib.php');

	$url = openstreetmap_ziptown_geonames_url($countrycode);
	if (!preg_match('/^https:\/\//i', $url)) {
		return -1;
	}
	$result = openstreetmap_url_content($url, array('User-Agent: Dolibarr-module-OpenStreetMap'), getDolGlobalInt('OPENSTREETMAP_CONNECT_TIMEOUT', 3), 60);
	if (!empty($result['curl_error_no']) || empty($result['http_code']) || $result['http_code'] != 200 || empty($result['content'])) {
		dol_syslog("openstreetmap_ziptown_download_geonames: download of ".$url." failed: ".(!empty($result['curl_error_msg']) ? $result['curl_error_msg'] : 'HTTP '.(empty($result['http_code']) ? '' : $result['http_code'])), LOG_WARNING);
		return -1;
	}
	return (file_put_contents($destfile, $result['content']) === false) ? -1 : 1;
}

/**
 *  Put a file of zip codes and towns in the queue of imports.
 *  A zip archive is extracted on disk (by streaming, so a large file does not use memory), the file is checked
 *  (GeoNames gazetteer refused, number of lines limited by OPENSTREETMAP_ZIPTOWN_MAX_ROWS) and a job is created.
 *
 *  @param		DoliDB		$db				Database handler
 *  @param		int			$countryid		Id of country in llx_c_country
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @param		string		$file			File on disk (zip archive or text file), not changed
 *  @param		string		$name			Name of the file to show
 *  @param		string		$mode			'add': add the missing zip codes and towns; 'replace': also remove, at the end, the
 *  										rows added before by this module for this country that are not in the file anymore
 *  @return		array<string,mixed>|int		Job if OK, or <0 if KO: -1 file, -2 no ZipArchive, -3 bad zip,
 *  										-4 GeoNames gazetteer, -5 too many lines (see openstreetmap_ziptown_max_rows())
 */
function openstreetmap_ziptown_job_create($db, $countryid, $countrycode, $file, $name, $mode = 'add')
{
	$dir = openstreetmap_ziptown_queue_dir();
	if ($dir === '' || !is_readable($file)) {
		return -1;
	}
	$id = dol_print_date(dol_now(), '%Y%m%d%H%M%S').'_'.bin2hex(random_bytes(4));
	$txt = $dir.'/'.$id.'.txt';

	$fh = fopen($file, 'rb');
	$magic = $fh ? (string) fread($fh, 4) : '';
	if ($fh) {
		fclose($fh);
	}
	if ($magic === "PK\x03\x04") {
		$result = openstreetmap_ziptown_extract_zip($file, strtoupper($countrycode).'.txt', $txt);
		if ($result < 0) {
			return $result;
		}
	} elseif (!copy($file, $txt)) {
		return -1;
	}

	$fh = fopen($txt, 'rb');
	if (!$fh) {
		dol_delete_file($txt, 0, 1);
		return -1;
	}
	$first = (string) fgets($fh, 65536);
	if (openstreetmap_ziptown_is_gazetteer($first)) {
		fclose($fh);
		dol_delete_file($txt, 0, 1);
		return -4;
	}
	// Count the lines (read by blocks, the file is never loaded in memory)
	$lines = ($first !== '') ? 1 : 0;
	while (!feof($fh)) {
		$block = fread($fh, 1048576);
		$lines += substr_count((string) $block, "\n");
	}
	fclose($fh);
	if ($lines > openstreetmap_ziptown_max_rows()) {
		dol_delete_file($txt, 0, 1);
		return -5;
	}

	$job = array(
		'id' => $id,
		'countryid' => (int) $countryid,
		'countrycode' => strtoupper($countrycode),
		'name' => dol_trunc(basename((string) $name), 100),
		'lines' => $lines,
		'offset' => 0,
		'done' => 0,
		'added' => 0,
		'existing' => 0,
		'removed' => 0,
		'mode' => ($mode == 'replace' && openstreetmap_ziptown_tracking_ok($db)) ? 'replace' : 'add',
		'status' => 'queued',
		'created' => dol_now(),
		'updated' => dol_now(),
	);
	if (!openstreetmap_ziptown_job_save($job)) {
		dol_delete_file($txt, 0, 1);
		return -1;
	}
	openstreetmap_ziptown_cron_enable($db, 1);
	return $job;
}

/**
 *  Enable or disable the scheduled job of the import. It is only enabled while files are waiting in the queue,
 *  so it does not run for nothing the rest of the time.
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		int			$enable		1 to enable (run as soon as possible), 0 to disable
 *  @return		void
 */
function openstreetmap_ziptown_cron_enable($db, $enable)
{
	global $conf, $user;

	include_once DOL_DOCUMENT_ROOT.'/cron/class/cronjob.class.php';

	$status = ($enable ? 1 : 0);	// Cronjob::STATUS_ENABLED / Cronjob::STATUS_DISABLED
	$cronjob = new Cronjob($db);
	if ($cronjob->fetch(0, 'OpenStreetMapZiptownJob', 'runQueue') > 0 && !empty($cronjob->id)) {
		if ((int) $cronjob->status == $status) {
			return;
		}
		$cronjob->status = $status;
		if ($enable) {
			$cronjob->datenextrun = dol_now();	// Run as soon as possible
		}
		// Cronjob::update() needs a user (fk_user_mod); without one, only the status is changed below
		if (is_object($user) && !empty($user->id)) {
			if ($cronjob->update($user) > 0) {
				return;
			}
			dol_syslog("openstreetmap_ziptown_cron_enable: update of scheduled job failed: ".$cronjob->error.' '.implode(', ', (array) $cronjob->errors), LOG_WARNING);
		}
	}

	// Versions of Dolibarr where the scheduled job can not be found or saved with the class: change only its status
	$sql = "UPDATE ".MAIN_DB_PREFIX."cronjob SET status = ".((int) $status);
	if ($enable) {
		$sql .= ", datenextrun = '".$db->idate(dol_now())."'";
	}
	$sql .= " WHERE classesname = '/openstreetmap/class/ziptownjob.class.php' AND methodename = 'runQueue'";
	$sql .= " AND entity IN (0, ".((int) $conf->entity).") AND status <> ".((int) $status);
	if (!$db->query($sql)) {
		dol_syslog("openstreetmap_ziptown_cron_enable: ".$db->lasterror(), LOG_WARNING);
	}
}

/**
 *  Return the maximum number of lines of a file of zip codes and towns. Larger files are refused, so the dictionary
 *  of Dolibarr stays small enough for its own autocompletion (OPENSTREETMAP_ZIPTOWN_MAX_ROWS, default 500000).
 *
 *  @return		int
 */
function openstreetmap_ziptown_max_rows()
{
	return getDolGlobalInt('OPENSTREETMAP_ZIPTOWN_MAX_ROWS', 500000);
}

/**
 *  Extract the text file of a zip archive into a file, by streaming (constant memory)
 *
 *  @param		string		$zipfile		Zip archive
 *  @param		string		$entry			Name of the entry to read; if not found, the first .txt or .csv entry
 *  @param		string		$destfile		File to write
 *  @return		int							1 if OK, <0 if KO (-1 file, -2 no ZipArchive, -3 bad zip)
 */
function openstreetmap_ziptown_extract_zip($zipfile, $entry, $destfile)
{
	if (!class_exists('ZipArchive')) {
		return -2;
	}
	$zip = new ZipArchive();
	if ($zip->open($zipfile) !== true) {
		return -3;
	}
	if ($zip->locateName($entry) === false) {
		$entry = '';
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$name = (string) $zip->getNameIndex($i);
			if (preg_match('/\.(txt|csv|tsv)$/i', $name) && !preg_match('/readme/i', $name) && strpos($name, '/') === false) {
				$entry = $name;
				break;
			}
		}
	}
	$result = -3;
	if ($entry !== '') {
		$in = $zip->getStream($entry);
		$out = fopen($destfile, 'wb');
		if ($in && $out) {
			$result = (stream_copy_to_stream($in, $out) === false) ? -1 : 1;
		}
		if ($in) {
			fclose($in);
		}
		if ($out) {
			fclose($out);
		}
	}
	$zip->close();
	return $result;
}

/**
 *  Tell if the first line of a file is from a GeoNames gazetteer extract (export/dump/XX.zip: all geographical names
 *  of a country, 19 tab separated columns starting with the geonameid, without postal codes) instead of the postal
 *  codes (export/zip/XX.zip)
 *
 *  @param		string		$content		Content of the file, or its first line
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
 *  Read the zip code and town of one line.
 *  Accepted formats: GeoNames postal codes (tab separated, country code in first column),
 *  or a simple list with the zip code and the town separated by a tab, a semicolon or a comma.
 *
 *  @param		string		$line			Line of the file (UTF-8)
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code (lines of GeoNames for other countries are ignored)
 *  @return		array{0:string,1:string}|null	array(zip, town), or null if the line has none
 */
function openstreetmap_ziptown_parse_line($line, $countrycode)
{
	$line = rtrim((string) $line, "\r\n");
	if (trim($line) === '' || strpos($line, '#') === 0) {
		return null;
	}
	if (strpos($line, "\t") !== false) {
		$cols = explode("\t", $line);
		if (count($cols) >= 3) {
			// GeoNames postal codes: country code, postal code, place name, ... Other files with more than
			// 2 columns (for example the GeoNames gazetteer, which has no postal code) are not read.
			if (!preg_match('/^[A-Z]{2}$/', $cols[0]) || $cols[0] != strtoupper($countrycode)) {
				return null;
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
		return null;
	}
	if (!mb_check_encoding($town, 'UTF-8')) {
		$town = mb_convert_encoding($town, 'UTF-8', 'ISO-8859-1');
	}
	return array($zip, $town);
}

/**
 *  Read the zip code / town pairs of a content (small files and tests; large files go through the queue)
 *
 *  @param		string		$content		Content of the file (UTF-8)
 *  @param		string		$countrycode	ISO 3166-1 alpha-2 code
 *  @return		array<int,array{0:string,1:string}>	List of array(zip, town), without duplicates
 */
function openstreetmap_ziptown_parse($content, $countrycode)
{
	$rows = array();
	$seen = array();
	foreach (preg_split('/\r\n|\r|\n/', (string) $content) as $line) {
		$row = openstreetmap_ziptown_parse_line($line, $countrycode);
		if ($row === null) {
			continue;
		}
		$key = $row[0].'|'.mb_strtolower($row[1]);
		if (!isset($seen[$key])) {
			$seen[$key] = 1;
			$rows[] = $row;
		}
	}
	return $rows;
}

/**
 *  Add the zip code / town pairs that are not yet in the dictionary of a country.
 *  The pairs already present are searched in the database by groups of 500 zip codes, so the memory used does not
 *  depend on the size of the dictionary.
 *  With an import key, the rows added are recorded in llx_openstreetmap_ziptown, and the rows added before by the
 *  module that are in the file again get this key: an import in mode "replace" can then remove, at the end, the
 *  rows of the module that were not in the file. Rows of other sources are never recorded nor changed.
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		int			$countryid	Id of country in llx_c_country
 *  @param		array<int,array{0:string,1:string}>	$rows	List of array(zip, town)
 *  @param		string		$importkey	Id of the import, or '' to record nothing
 *  @return		array{added:int,existing:int}|int	Numbers of rows added and already present, or -1 if KO
 */
function openstreetmap_ziptown_import($db, $countryid, $rows, $importkey = '')
{
	if ($countryid <= 0) {
		return -1;
	}
	$track = ($importkey !== '' && openstreetmap_ziptown_tracking_ok($db));
	$added = 0;
	$nbexisting = 0;
	$seen = array();
	foreach (array_chunk($rows, 500) as $group) {
		$zips = array();
		foreach ($group as $row) {
			$zips[$row[0]] = "'".$db->escape($row[0])."'";
		}
		// Pairs already in the dictionary (compared without case, so "Athina" and "ATHINA" are the same town)
		$existing = openstreetmap_ziptown_rowids($db, $countryid, $zips);
		if ($existing === null) {
			return -1;
		}
		$values = array();
		$keysadded = array();
		$rowidsseen = array();
		foreach ($group as $row) {
			$key = $row[0].'|'.mb_strtolower($row[1]);
			if (isset($existing[$key]) || isset($seen[$key])) {
				$nbexisting++;
				if (isset($existing[$key])) {
					$rowidsseen[] = $existing[$key];
				}
				continue;
			}
			$seen[$key] = 1;
			$keysadded[$key] = 1;
			$values[] = "('".$db->escape($row[0])."', '".$db->escape($row[1])."', '".$db->escape(dol_strtoupper($row[1]))."', ".((int) $countryid).", 1)";
		}
		if (count($values)) {
			$nb = openstreetmap_ziptown_insert($db, $values);
			$added += $nb;
			$nbexisting += count($values) - $nb;
		}
		if ($track) {
			// Rows of the module seen again: they get the key of this import
			if (count($rowidsseen)) {
				$sql = "UPDATE ".MAIN_DB_PREFIX."openstreetmap_ziptown SET import_key = '".$db->escape($importkey)."'";
				$sql .= " WHERE fk_ziptown IN (".implode(',', array_map('intval', $rowidsseen)).")";
				if (!$db->query($sql)) {
					dol_syslog("openstreetmap_ziptown_import: ".$db->lasterror(), LOG_WARNING);
				}
			}
			// Rows added now: recorded as rows of the module
			if (count($keysadded)) {
				$after = openstreetmap_ziptown_rowids($db, $countryid, $zips);
				$tracking = array();
				foreach (array_keys($keysadded) as $key) {
					if ($after !== null && isset($after[$key])) {
						$tracking[] = "(".((int) $after[$key]).", ".((int) $countryid).", '".$db->escape($importkey)."')";
					}
				}
				if (count($tracking)) {
					$sql = "INSERT INTO ".MAIN_DB_PREFIX."openstreetmap_ziptown (fk_ziptown, fk_pays, import_key) VALUES ".implode(', ', $tracking);
					if (!$db->query($sql)) {
						dol_syslog("openstreetmap_ziptown_import: ".$db->lasterror(), LOG_WARNING);
					}
				}
			}
		}
	}
	return array('added' => $added, 'existing' => $nbexisting);
}

/**
 *  Return the rows of the dictionary of a country for some zip codes
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		int			$countryid	Id of country
 *  @param		array<string,string>	$zips	Zip codes, already escaped and quoted
 *  @return		array<string,int>|null	'zip|town in lower case' => rowid, or null if KO
 */
function openstreetmap_ziptown_rowids($db, $countryid, $zips)
{
	$rowids = array();
	if (!count($zips)) {
		return $rowids;
	}
	$sql = "SELECT rowid, zip, town FROM ".MAIN_DB_PREFIX."c_ziptown WHERE fk_pays = ".((int) $countryid)." AND zip IN (".implode(',', $zips).")";
	$resql = $db->query($sql);
	if (!$resql) {
		dol_syslog("openstreetmap_ziptown_rowids: ".$db->lasterror(), LOG_ERR);
		return null;
	}
	while ($obj = $db->fetch_object($resql)) {
		$rowids[$obj->zip.'|'.mb_strtolower($obj->town)] = (int) $obj->rowid;
	}
	return $rowids;
}

/**
 *  Tell if the table recording the rows added by the module exists (created when the module is enabled)
 *
 *  @param		DoliDB		$db			Database handler
 *  @return		bool
 */
function openstreetmap_ziptown_tracking_ok($db)
{
	static $ok = null;
	if ($ok === null) {
		$ok = (bool) $db->query("SELECT rowid FROM ".MAIN_DB_PREFIX."openstreetmap_ziptown WHERE 1 = 0");
	}
	return $ok;
}

/**
 *  End of an import in mode "replace": remove the rows added before by the module for this country that were not
 *  in the file (they do not have the key of this import). Rows of other sources are not concerned.
 *
 *  @param		DoliDB		$db			Database handler
 *  @param		int			$countryid	Id of country
 *  @param		string		$importkey	Id of the import
 *  @return		int						Number of rows removed, or -1 if KO
 */
function openstreetmap_ziptown_remove_old($db, $countryid, $importkey)
{
	$removed = 0;
	while (true) {
		$ids = array();
		$sql = "SELECT fk_ziptown FROM ".MAIN_DB_PREFIX."openstreetmap_ziptown";
		$sql .= " WHERE fk_pays = ".((int) $countryid)." AND import_key <> '".$db->escape($importkey)."'".$db->plimit(2000);
		$resql = $db->query($sql);
		if (!$resql) {
			dol_syslog("openstreetmap_ziptown_remove_old: ".$db->lasterror(), LOG_ERR);
			return -1;
		}
		while ($obj = $db->fetch_object($resql)) {
			$ids[] = (int) $obj->fk_ziptown;
		}
		if (!count($ids)) {
			return $removed;
		}
		$in = implode(',', $ids);
		$db->begin();
		$ok = $db->query("DELETE FROM ".MAIN_DB_PREFIX."c_ziptown WHERE rowid IN (".$in.") AND fk_pays = ".((int) $countryid));
		$nb = $ok ? $db->affected_rows($ok) : 0;
		if ($ok) {
			$ok = $db->query("DELETE FROM ".MAIN_DB_PREFIX."openstreetmap_ziptown WHERE fk_ziptown IN (".$in.")");
		}
		if (!$ok) {
			$db->rollback();
			dol_syslog("openstreetmap_ziptown_remove_old: ".$db->lasterror(), LOG_ERR);
			return -1;
		}
		$db->commit();
		$removed += (int) $nb;
	}
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
 *  Save a job of the queue
 *
 *  @param		array<string,mixed>		$job		Job
 *  @return		bool
 */
function openstreetmap_ziptown_job_save($job)
{
	$dir = openstreetmap_ziptown_queue_dir();
	if ($dir === '' || !preg_match('/^[0-9]{14}_[0-9a-f]{8}$/', (string) $job['id'])) {
		return false;
	}
	$job['updated'] = dol_now();
	return (file_put_contents($dir.'/'.$job['id'].'.json', json_encode($job), LOCK_EX) !== false);
}

/**
 *  Return a job of the queue
 *
 *  @param		string		$id		Id of job
 *  @return		array<string,mixed>|null
 */
function openstreetmap_ziptown_job_get($id)
{
	$dir = openstreetmap_ziptown_queue_dir();
	if ($dir === '' || !preg_match('/^[0-9]{14}_[0-9a-f]{8}$/', (string) $id) || !is_file($dir.'/'.$id.'.json')) {
		return null;
	}
	$job = json_decode((string) file_get_contents($dir.'/'.$id.'.json'), true);
	return is_array($job) ? $job : null;
}

/**
 *  Return the jobs of the queue, oldest first
 *
 *  @return		array<int,array<string,mixed>>
 */
function openstreetmap_ziptown_jobs()
{
	$jobs = array();
	$dir = openstreetmap_ziptown_queue_dir();
	if ($dir === '') {
		return $jobs;
	}
	$files = glob($dir.'/*.json');
	sort($files);
	foreach ($files as $file) {
		$job = openstreetmap_ziptown_job_get(basename($file, '.json'));
		if ($job) {
			$jobs[] = $job;
		}
	}
	return $jobs;
}

/**
 *  Remove a job and its file from the queue (a job still running stops at its next part)
 *
 *  @param		string		$id		Id of job
 *  @return		void
 */
function openstreetmap_ziptown_job_delete($id)
{
	$dir = openstreetmap_ziptown_queue_dir();
	if ($dir === '' || !preg_match('/^[0-9]{14}_[0-9a-f]{8}$/', (string) $id)) {
		return;
	}
	foreach (array('.txt', '.json', '.lock') as $ext) {
		if (is_file($dir.'/'.$id.$ext)) {
			dol_delete_file($dir.'/'.$id.$ext, 0, 1);
		}
	}
}

/**
 *  Return the progress of a job in percent
 *
 *  @param		array<string,mixed>		$job		Job
 *  @return		int							0 to 100
 */
function openstreetmap_ziptown_job_percent($job)
{
	if ($job['status'] == 'done') {
		return 100;
	}
	return ($job['lines'] > 0) ? min(99, (int) floor(100 * $job['done'] / $job['lines'])) : 0;
}

/**
 *  Import the next part of a job: read the file from where the previous part stopped, line by line, for at most
 *  $maxlines lines or $maxseconds seconds. A lock makes sure two processes (scheduled job and setup page) never
 *  import the same part.
 *
 *  @param		DoliDB		$db				Database handler
 *  @param		string		$id				Id of job
 *  @param		int			$maxseconds		Max duration
 *  @param		int			$maxlines		Max number of lines
 *  @return		array<string,mixed>|null	Job after this part, or null if the job does not exist or is busy
 */
function openstreetmap_ziptown_job_run($db, $id, $maxseconds = 15, $maxlines = 20000)
{
	$dir = openstreetmap_ziptown_queue_dir();
	$job = openstreetmap_ziptown_job_get($id);
	if (!$job || !in_array($job['status'], array('queued', 'running'))) {
		return $job;
	}
	$lock = fopen($dir.'/'.$id.'.lock', 'c');
	if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
		if ($lock) {
			fclose($lock);
		}
		return null;
	}
	// Read again under the lock: another process may have imported a part meanwhile
	$job = openstreetmap_ziptown_job_get($id);
	$fh = $job ? fopen($dir.'/'.$id.'.txt', 'rb') : false;
	if (!$job || !$fh) {
		if ($job) {
			$job['status'] = 'error';
			openstreetmap_ziptown_job_save($job);
		}
		flock($lock, LOCK_UN);
		fclose($lock);
		return $job;
	}
	$start = microtime(true);
	fseek($fh, (int) $job['offset']);
	$job['status'] = 'running';
	while (!feof($fh) && (microtime(true) - $start) < $maxseconds) {
		$rows = array();
		$nblines = 0;
		while ($nblines < min(2000, $maxlines) && ($line = fgets($fh)) !== false) {
			$nblines++;
			$row = openstreetmap_ziptown_parse_line($line, $job['countrycode']);
			if ($row !== null) {
				$rows[] = $row;
			}
		}
		$result = openstreetmap_ziptown_import($db, $job['countryid'], $rows, $job['id']);
		if (!is_array($result)) {
			$job['status'] = 'error';
			break;
		}
		$job['offset'] = ftell($fh);
		$job['done'] += $nblines;
		$job['added'] += $result['added'];
		$job['existing'] += $result['existing'];
		$maxlines -= $nblines;
		openstreetmap_ziptown_job_save($job);
		if ($maxlines <= 0 || !openstreetmap_ziptown_job_get($id)) {
			break;	// Enough for this part, or job cancelled meanwhile
		}
	}
	$eof = feof($fh);
	fclose($fh);
	if (!openstreetmap_ziptown_job_get($id)) {
		$job = null;	// Cancelled
	} elseif ($eof && $job['status'] == 'running') {
		$job['status'] = 'done';
		$job['done'] = $job['lines'];
		if (isset($job['mode']) && $job['mode'] == 'replace' && $job['added'] + $job['existing'] == 0) {
			// No zip code of this country in the file (wrong file or wrong country): nothing is removed
			$job['status'] = 'error';
		} elseif (isset($job['mode']) && $job['mode'] == 'replace') {
			$removed = openstreetmap_ziptown_remove_old($db, $job['countryid'], $job['id']);
			if ($removed < 0) {
				$job['status'] = 'error';
			} else {
				$job['removed'] = $removed;
			}
		}
		dol_delete_file($dir.'/'.$id.'.txt', 0, 1);
		openstreetmap_ziptown_job_save($job);
	}
	flock($lock, LOCK_UN);
	fclose($lock);
	return $job;
}

/**
 *  Disable the scheduled job if nothing is waiting in the queue (called at the end of the script of the scheduler)
 *
 *  @param		DoliDB		$db				Database handler
 *  @return		void
 */
function openstreetmap_ziptown_cron_disable_if_empty($db)
{
	foreach (openstreetmap_ziptown_jobs() as $job) {
		if (in_array($job['status'], array('queued', 'running'))) {
			return;
		}
	}
	openstreetmap_ziptown_cron_enable($db, 0);
}

/**
 *  Import parts of the jobs of the queue, oldest first, for at most $maxseconds seconds.
 *  Finished jobs older than 7 days are removed from the list. When nothing is waiting anymore, the scheduled job
 *  is disabled.
 *
 *  @param		DoliDB		$db				Database handler
 *  @param		int			$maxseconds		Max duration
 *  @param		int			$fromcron		1 if called by the scheduled job (it is then disabled at the end of the script)
 *  @return		array<int,array<string,mixed>>	Jobs of the queue after this run
 */
function openstreetmap_ziptown_run_queue($db, $maxseconds = 15, $fromcron = 0)
{
	$start = microtime(true);
	// One process at a time imports the queue (scheduled job or setup page), in the order of the files: an import in
	// mode "replace" must not run at the same time as another import of the same country
	$dir = openstreetmap_ziptown_queue_dir();
	$lock = ($dir !== '' && $maxseconds > 0) ? fopen($dir.'/queue.lock', 'c') : false;
	if ($maxseconds > 0 && (!$lock || !flock($lock, LOCK_EX | LOCK_NB))) {
		if ($lock) {
			fclose($lock);
		}
		return openstreetmap_ziptown_jobs();
	}
	foreach (openstreetmap_ziptown_jobs() as $job) {
		if (in_array($job['status'], array('done', 'error')) && $job['updated'] < dol_now() - 7 * 86400) {
			openstreetmap_ziptown_job_delete($job['id']);
			continue;
		}
		while (in_array($job['status'], array('queued', 'running'))) {
			$left = $maxseconds - (microtime(true) - $start);
			if ($left < 1) {
				break 2;
			}
			$job = openstreetmap_ziptown_job_run($db, $job['id'], (int) ceil($left), 100000);
			if (!$job) {
				break;	// Busy in another process, or cancelled
			}
		}
	}
	$jobs = openstreetmap_ziptown_jobs();
	$waiting = 0;
	foreach ($jobs as $job) {
		if (in_array($job['status'], array('queued', 'running'))) {
			$waiting++;
		}
	}
	if (!$waiting) {
		if ($fromcron) {
			// The scheduler of Dolibarr saves its own copy of the job (still enabled) after this method returns, which
			// would enable it again: the job is disabled at the end of the script, if the queue is still empty then.
			register_shutdown_function('openstreetmap_ziptown_cron_disable_if_empty', $db);
		} else {
			openstreetmap_ziptown_cron_enable($db, 0);
		}
	}
	if ($lock) {
		flock($lock, LOCK_UN);
		fclose($lock);
	}
	return $jobs;
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
