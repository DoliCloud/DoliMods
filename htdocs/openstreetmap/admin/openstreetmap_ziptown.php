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
 *	    \file       htdocs/openstreetmap/admin/openstreetmap_ziptown.php
 *      \ingroup    openstreetmap
 *      \brief      Setup page for openstreetmap module: import of zip codes and towns into the dictionary of Dolibarr
 */

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

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");
dol_include_once("/openstreetmap/lib/ziptownimport.lib.php");

if (!$user->admin) {
	accessforbidden();
}

$langs->loadLangs(array("admin", "other", "companies", "openstreetmap@openstreetmap"));

$action = GETPOST('action', 'aZ09');
$countryid = GETPOSTINT('country_id');
$source = GETPOST('source', 'aZ09');


/*
 * Actions
 */

if ($action == 'setdictionary') {
	$value = GETPOSTINT('value') ? '1' : '0';
	if (dolibarr_set_const($db, 'MAIN_USE_ZIPTOWN_DICTIONNARY', $value, 'chaine', 0, '', $conf->entity) > 0) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

if ($action == 'import') {
	$countrycode = openstreetmap_country_code($db, $countryid);
	$content = '';
	if ($countrycode == '') {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Country")), null, 'errors');
	} elseif ($source == 'geonames') {
		$content = openstreetmap_ziptown_download_geonames($countrycode);
		if (!is_string($content)) {
			setEventMessages($langs->trans("OpenStreetMapZiptownDownloadFailed"), null, 'errors');
			$content = '';
		}
	} elseif ($source == 'file') {
		$file = isset($_FILES['ziptownfile']) && is_array($_FILES['ziptownfile']) ? $_FILES['ziptownfile'] : array();
		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error'])) {
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("File")), null, 'errors');
		} elseif ($file['size'] > 20 * 1024 * 1024) {	// Larger files are refused before by the browser and by PHP
			setEventMessages($langs->trans("OpenStreetMapZiptownFileTooLarge"), null, 'errors');
		} else {
			$data = (string) file_get_contents($file['tmp_name']);
			if (strpos($data, "PK\x03\x04") === 0) {
				$content = openstreetmap_ziptown_read_zip($data, $countrycode.'.txt');
				if (!is_string($content)) {
					setEventMessages($langs->trans("OpenStreetMapZiptownBadFile"), null, 'errors');
					$content = '';
				}
			} else {
				$content = $data;
			}
		}
	}
	if ($content !== '' && openstreetmap_ziptown_is_gazetteer($content)) {
		setEventMessages($langs->trans("OpenStreetMapZiptownGazetteer"), null, 'errors');
		$content = '';
	} elseif (strlen($content) > 30 * 1024 * 1024) {
		setEventMessages($langs->trans("OpenStreetMapZiptownFileTooLarge"), null, 'errors');
		$content = '';
	}
	if ($content !== '') {
		if (!mb_check_encoding($content, 'UTF-8')) {
			$content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
		}
		@set_time_limit(300);
		$rows = openstreetmap_ziptown_parse($content, $countrycode);
		if (!count($rows)) {
			setEventMessages($langs->trans("OpenStreetMapZiptownBadFile"), null, 'errors');
		} else {
			$result = openstreetmap_ziptown_import($db, $countryid, $rows);
			if (is_array($result)) {
				setEventMessages($langs->trans("OpenStreetMapZiptownImported", $result['added'], $result['existing']), null, 'mesgs');
			} else {
				setEventMessages($langs->trans("Error"), null, 'errors');
			}
		}
	}
}


/*
 * View
 */

$form = new Form($db);

llxHeader('', $langs->trans("OpenStreetMapSetup"));

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("OpenStreetMapSetup"), $linkback, 'setup');

$head = openstreetmapadmin_prepare_head();

print dol_get_fiche_head($head, 'ziptown', $langs->trans("OpenStreetMapTools"), -1);

print '<span class="opacitymedium">'.$langs->trans("OpenStreetMapZiptownDesc").'</span><br><br>';

// Use of the dictionary by Dolibarr itself
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="setdictionary">';
print '<input type="hidden" name="value" value="'.(getDolGlobalString('MAIN_USE_ZIPTOWN_DICTIONNARY') ? '0' : '1').'">';
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapZiptownUseDictionary").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownUseDictionaryDesc").'</span></td><td>';
print (getDolGlobalString('MAIN_USE_ZIPTOWN_DICTIONNARY') ? $langs->trans("Yes") : $langs->trans("No")).' ';
print '<input type="submit" class="button smallpaddingimp" value="'.(getDolGlobalString('MAIN_USE_ZIPTOWN_DICTIONNARY') ? $langs->trans("Disable") : $langs->trans("Activate")).'">';
print '</td></tr>';
print '</table>';
print '</div>';
print '</form>';
print '<br>';

// Import. The size of an upload is limited by PHP (upload_max_filesize, post_max_size): above it the form arrives
// empty and is refused, so the size is shown and checked in the browser before sending.
$maxsizes = getMaxFileSizeArray();
$maxkb = (int) $maxsizes['maxmin'];
if ($maxkb <= 0 || $maxkb > 20480) {
	$maxkb = 20480;
}
print load_fiche_titre($langs->trans("OpenStreetMapZiptownImport"), '', '');
print '<form id="ziptownimport" action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="import">';
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans("Country").'</td><td>';
print $form->select_country($countryid, 'country_id');
print '</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans("Source").'</td><td>';
print '<input type="radio" id="source_geonames" name="source" value="geonames"'.($source != 'file' ? ' checked' : '').'> <label for="source_geonames">'.$langs->trans("OpenStreetMapZiptownSourceGeonames").'</label><br>';
print '<input type="radio" id="source_file" name="source" value="file"'.($source == 'file' ? ' checked' : '').'> <label for="source_file">'.$langs->trans("OpenStreetMapZiptownSourceFile").'</label> ';
print '<input type="file" name="ziptownfile" accept=".zip,.txt,.csv,.tsv">';
print '<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownFileFormat").'</span>';
print '<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownMaxSize", dol_print_size($maxkb * 1024, 1)).'</span>';
print '</td></tr>';
print '</table>';
print '</div>';
print '<div class="center"><input type="submit" class="button" value="'.$langs->trans("OpenStreetMapZiptownImportButton").'"></div>';
print '</form>';
print '<script>
document.getElementById("ziptownimport").addEventListener("submit", function(e) {
	var input = this.querySelector("input[name=ziptownfile]");
	if (document.getElementById("source_file").checked && input.files.length && input.files[0].size > '.((int) $maxkb * 1024).') {
		e.preventDefault();
		alert("'.dol_escape_js($langs->transnoentitiesnoconv("OpenStreetMapZiptownFileTooLarge").' '.$langs->transnoentitiesnoconv("OpenStreetMapZiptownMaxSize", dol_print_size($maxkb * 1024, 1))).'");
	}
});
</script>';
print '<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownGeonamesLicense").'</span><br><br>';

// Content of the dictionary
print load_fiche_titre($langs->trans("OpenStreetMapZiptownContent"), '', '');
$counts = openstreetmap_ziptown_counts($db);
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans("Country").'</td><td class="right">'.$langs->trans("Nb").'</td></tr>';
if (!count($counts)) {
	print '<tr class="oddeven"><td colspan="2"><span class="opacitymedium">'.$langs->trans("None").'</span></td></tr>';
}
foreach ($counts as $count) {
	print '<tr class="oddeven"><td>'.dol_escape_htmltag($langs->transnoentitiesnoconv("Country".$count['code']) != "Country".$count['code'] ? $langs->transnoentitiesnoconv("Country".$count['code']) : $count['label']).'</td><td class="right">'.$count['nb'].'</td></tr>';
}
print '</table>';
print '</div>';

print dol_get_fiche_end();

llxFooter();

$db->close();
