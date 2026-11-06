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
$mode = (GETPOST('mode', 'aZ09') == 'replace') ? 'replace' : 'add';


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
	$dir = openstreetmap_ziptown_queue_dir();
	$file = '';
	$name = '';
	$todelete = '';
	if ($countrycode == '') {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Country")), null, 'errors');
	} elseif ($dir === '') {
		setEventMessages($langs->trans("Error"), null, 'errors');
	} elseif ($source == 'geonames') {
		$file = $todelete = $dir.'/download_'.bin2hex(random_bytes(8)).'.zip';
		$name = basename(openstreetmap_ziptown_geonames_url($countrycode));
		if (openstreetmap_ziptown_download_geonames($countrycode, $file) < 0) {
			setEventMessages($langs->trans("OpenStreetMapZiptownDownloadFailed"), null, 'errors');
			$file = '';
		}
	} elseif ($source == 'file') {
		$upload = isset($_FILES['ziptownfile']) && is_array($_FILES['ziptownfile']) ? $_FILES['ziptownfile'] : array();
		if (empty($upload['tmp_name']) || !is_uploaded_file($upload['tmp_name']) || !empty($upload['error'])) {
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("File")), null, 'errors');
		} elseif ($upload['size'] > 100 * 1024 * 1024) {	// Larger files are refused before by the browser and by PHP
			setEventMessages($langs->trans("OpenStreetMapZiptownFileTooLarge"), null, 'errors');
		} else {
			$file = $upload['tmp_name'];
			$name = $upload['name'];
		}
	}
	if ($file !== '') {
		// The file is put in the queue: extracted on disk and imported afterwards by parts, in the background
		$job = openstreetmap_ziptown_job_create($db, $countryid, $countrycode, $file, $name, $mode);
		if (is_array($job)) {
			setEventMessages($langs->trans("OpenStreetMapZiptownQueued", $job['name'], $job['lines']), null, 'mesgs');
		} elseif ($job == -4) {
			setEventMessages($langs->trans("OpenStreetMapZiptownGazetteer"), null, 'errors');
		} elseif ($job == -5) {
			setEventMessages($langs->trans("OpenStreetMapZiptownTooManyLines", openstreetmap_ziptown_max_rows()), null, 'errors');
		} else {
			setEventMessages($langs->trans("OpenStreetMapZiptownBadFile"), null, 'errors');
		}
	}
	if ($todelete !== '' && is_file($todelete)) {
		dol_delete_file($todelete, 0, 1);
	}
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

if ($action == 'setmaxrows') {
	$maxrows = max(1000, min(5000000, GETPOSTINT('OPENSTREETMAP_ZIPTOWN_MAX_ROWS')));
	if (dolibarr_set_const($db, 'OPENSTREETMAP_ZIPTOWN_MAX_ROWS', (string) $maxrows, 'chaine', 0, '', $conf->entity) > 0) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

if ($action == 'canceljob') {
	openstreetmap_ziptown_job_delete(GETPOST('job', 'aZ09'));
	openstreetmap_ziptown_run_queue($db, 0);	// Disables the scheduled job if nothing is waiting anymore
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
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
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapZiptownUseDictionary").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownUseDictionaryDesc").'</span></td><td class="nowraponall">';
print (getDolGlobalString('MAIN_USE_ZIPTOWN_DICTIONNARY') ? $langs->trans("Yes") : $langs->trans("No")).' ';
print '<input type="submit" class="button smallpaddingimp" value="'.(getDolGlobalString('MAIN_USE_ZIPTOWN_DICTIONNARY') ? $langs->trans("Disable") : $langs->trans("Activate")).'">';
print '</td></tr>';
print '</table>';
print '</div>';
print '</form>';

// Max number of lines of a file
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="setmaxrows">';
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapZiptownMaxRows").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownMaxRowsDesc").'</span></td><td class="nowraponall">';
print '<input type="number" class="flat width100" min="1000" max="5000000" step="1000" name="OPENSTREETMAP_ZIPTOWN_MAX_ROWS" value="'.openstreetmap_ziptown_max_rows().'"> ';
print '<input type="submit" class="button smallpaddingimp" value="'.$langs->trans("Save").'">';
print '</td></tr>';
print '</table>';
print '</div>';
print '</form>';
print '<br>';

// Import. The size of an upload is limited by PHP (upload_max_filesize, post_max_size): above it the form arrives
// empty and is refused, so the size is shown and checked in the browser before sending.
$maxsizes = getMaxFileSizeArray();
$maxkb = (int) $maxsizes['maxmin'];
if ($maxkb <= 0 || $maxkb > 102400) {
	$maxkb = 102400;
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
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapZiptownMode").'</td><td>';
print '<input type="radio" id="mode_add" name="mode" value="add" checked> <label for="mode_add">'.$langs->trans("OpenStreetMapZiptownModeAdd").'</label><br>';
if (openstreetmap_ziptown_tracking_ok($db)) {
	print '<input type="radio" id="mode_replace" name="mode" value="replace"> <label for="mode_replace">'.$langs->trans("OpenStreetMapZiptownModeReplace").'</label>';
	print '<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownModeReplaceDesc").'</span>';
} else {
	print '<span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownModeReplaceNeedsReload").'</span>';
}
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

// Queue of imports, with their progress. While this page is open, the browser asks the server to import the
// next parts; the scheduled job of the module does the same when the page is closed.
$jobs = openstreetmap_ziptown_jobs();
if (count($jobs)) {
	print load_fiche_titre($langs->trans("OpenStreetMapZiptownQueue"), '', '');
	print '<span class="opacitymedium small">'.$langs->trans("OpenStreetMapZiptownQueueDesc").'</span><br>';
	print '<div id="ziptownerror" class="error hidden"></div>';
	print '<div class="div-table-responsive-no-min">';
	print '<table class="noborder centpercent" id="ziptownjobs">';
	print '<tr class="liste_titre"><td>'.$langs->trans("Country").'</td><td>'.$langs->trans("File").'</td><td>'.$langs->trans("OpenStreetMapZiptownProgress").'</td>';
	print '<td class="right">'.$langs->trans("OpenStreetMapZiptownLines").'</td><td class="right">'.$langs->trans("OpenStreetMapZiptownAdded").'</td><td class="right">'.$langs->trans("OpenStreetMapZiptownExisting").'</td><td class="right">'.$langs->trans("OpenStreetMapZiptownRemoved").'</td><td>'.$langs->trans("Status").'</td><td></td></tr>';
	foreach ($jobs as $job) {
		$percent = openstreetmap_ziptown_job_percent($job);
		print '<tr class="oddeven" data-job="'.dol_escape_htmltag($job['id']).'">';
		print '<td>'.dol_escape_htmltag($job['countrycode']).'</td>';
		print '<td>'.dol_escape_htmltag($job['name']).(isset($job['mode']) && $job['mode'] == 'replace' ? ' <span class="opacitymedium">('.$langs->trans("OpenStreetMapZiptownModeReplaceShort").')</span>' : '').'</td>';
		print '<td class="minwidth200"><div style="background: #ddd; border-radius: 3px; height: 14px; width: 100%;"><div class="osm-bar" style="background: #3c8dbc; border-radius: 3px; height: 14px; width: '.((int) $percent).'%;"></div></div>';
		print '<span class="osm-percent">'.((int) $percent).'</span>%</td>';
		print '<td class="right"><span class="osm-done">'.((int) $job['done']).'</span> / '.((int) $job['lines']).'</td>';
		print '<td class="right osm-added">'.((int) $job['added']).'</td>';
		print '<td class="right osm-existing">'.((int) $job['existing']).'</td>';
		print '<td class="right osm-removed">'.(isset($job['mode']) && $job['mode'] == 'replace' ? (int) (isset($job['removed']) ? $job['removed'] : 0) : '').'</td>';
		print '<td class="osm-status" data-status="'.dol_escape_htmltag($job['status']).'">'.$langs->trans("OpenStreetMapZiptownStatus".ucfirst($job['status'])).'</td>';
		print '<td class="right">';
		if (in_array($job['status'], array('queued', 'running'))) {
			print '<form action="'.$_SERVER["PHP_SELF"].'" method="post" class="inline-block">';
			print '<input type="hidden" name="token" value="'.newToken().'">';
			print '<input type="hidden" name="action" value="canceljob">';
			print '<input type="hidden" name="job" value="'.dol_escape_htmltag($job['id']).'">';
			print '<input type="submit" class="button smallpaddingimp" value="'.$langs->trans("Cancel").'">';
			print '</form>';
		}
		print '</td></tr>';
	}
	print '</table>';
	print '</div><br>';

	print '<script>
(function() {
	var url = "'.dol_escape_js(dol_buildpath('/openstreetmap/ajax/ziptown.php', 1)).'";
	// Token of the current page: the background request does not renew the token (NOTOKENRENEWAL)
	var token = "'.dol_escape_js(currentToken()).'";
	var txtstopped = "'.dol_escape_js($langs->transnoentitiesnoconv("OpenStreetMapZiptownRefreshStopped")).'";
	var finished = false;
	var networkerrors = 0;
	function stopWithError(detail) {
		var e = document.getElementById("ziptownerror");
		e.textContent = txtstopped + (detail ? " (" + detail + ")" : "");
		e.classList.remove("hidden");
	}
	function running() {
		return document.querySelectorAll("#ziptownjobs td.osm-status[data-status=queued], #ziptownjobs td.osm-status[data-status=running]").length > 0;
	}
	function update(jobs) {
		jobs.forEach(function(j) {
			var tr = document.querySelector("#ziptownjobs tr[data-job=\"" + j.id + "\"]");
			if (!tr) {
				return;
			}
			tr.querySelector(".osm-bar").style.width = j.percent + "%";
			tr.querySelector(".osm-percent").textContent = j.percent;
			tr.querySelector(".osm-done").textContent = j.done;
			tr.querySelector(".osm-added").textContent = j.added;
			tr.querySelector(".osm-existing").textContent = j.existing;
			if (j.mode === "replace") {
				tr.querySelector(".osm-removed").textContent = j.removed;
			}
			var st = tr.querySelector(".osm-status");
			st.setAttribute("data-status", j.status);
			st.textContent = j.statuslabel;
			if (j.status === "done" || j.status === "error") {
				finished = true;
			}
		});
	}
	function next() {
		if (!running()) {
			if (finished) {
				window.location.reload();	// Show the new content of the dictionary
			}
			return;
		}
		var body = new URLSearchParams();
		body.set("op", "run");
		body.set("token", token);
		fetch(url, {method: "POST", body: body, credentials: "same-origin", headers: {"Accept": "application/json"}})
			.then(function(r) {
				return r.text().then(function(text) {
					var data = null;
					try {
						data = JSON.parse(text);
					} catch (e) {
						data = null;
					}
					return {ok: r.ok, status: r.status, data: data};
				});
			})
			.then(function(res) {
				networkerrors = 0;
				if (!res.ok || !res.data || !res.data.jobs) {
					/* Session ended (login page), access refused or error of the server: stop asking. The scheduled
						job goes on in the background; the page shows the progress again when it is reloaded. */
					stopWithError(res.data && res.data.error ? res.data.error : "HTTP " + res.status);
					return;
				}
				update(res.data.jobs);
				setTimeout(next, 500);
			})
			.catch(function() {
				/* Network error: try again a few times, then stop */
				networkerrors++;
				if (networkerrors >= 5) {
					stopWithError("");
					return;
				}
				setTimeout(next, 10000);
			});
	}
	next();
})();
</script>';
}

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
