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
 *       \file       htdocs/openstreetmap/ajax/ziptown.php
 *       \ingroup    openstreetmap
 *       \brief      Progress of the imports of zip codes and towns, for the setup page.
 *                   op=run  -> import the next parts of the queue for a few seconds, then return the progress
 *                   op=list -> return the progress only
 *                   Called by the setup page while it is open (the scheduled job of the module does the same in the
 *                   background). The session is released at once, so the other pages of the user are not blocked.
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
dol_include_once("/openstreetmap/lib/ziptownimport.lib.php");

top_httphead('application/json');

/**
 * Print a JSON answer and exit
 *
 * @param	array<string,mixed>	$data		Data to return
 * @param	int		$httpcode	HTTP code
 * @return	void
 */
function openstreetmap_ziptown_answer($data, $httpcode = 200)
{
	http_response_code($httpcode);
	print json_encode($data);
	exit;
}

if (!isModEnabled('openstreetmap') || !$user->admin) {
	openstreetmap_ziptown_answer(array('error' => 'FORBIDDEN'), 403);
}
$op = GETPOST('op', 'aZ09');
if ($op == 'run' && $_SERVER['REQUEST_METHOD'] != 'POST') {
	openstreetmap_ziptown_answer(array('error' => 'BAD_METHOD'), 405);
}

$langs->loadLangs(array("openstreetmap@openstreetmap"));

if (session_status() == PHP_SESSION_ACTIVE) {
	session_write_close();
}
@set_time_limit(60);

$jobs = array();
if ($op == 'run') {
	$jobs = openstreetmap_ziptown_run_queue($db, getDolGlobalInt('OPENSTREETMAP_ZIPTOWN_PAGE_SECONDS', 15));
} elseif ($op == 'list') {
	$jobs = openstreetmap_ziptown_jobs();
} else {
	openstreetmap_ziptown_answer(array('error' => 'BAD_OP'), 400);
}

$answer = array();
foreach ($jobs as $job) {
	$answer[] = array(
		'id' => $job['id'],
		'percent' => openstreetmap_ziptown_job_percent($job),
		'done' => (int) $job['done'],
		'lines' => (int) $job['lines'],
		'added' => (int) $job['added'],
		'existing' => (int) $job['existing'],
		'removed' => isset($job['removed']) ? (int) $job['removed'] : 0,
		'mode' => isset($job['mode']) ? $job['mode'] : 'add',
		'status' => $job['status'],
		'statuslabel' => $langs->transnoentitiesnoconv("OpenStreetMapZiptownStatus".ucfirst($job['status'])),
	);
}
openstreetmap_ziptown_answer(array('jobs' => $answer));
