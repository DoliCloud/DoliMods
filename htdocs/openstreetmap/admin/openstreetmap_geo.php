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
 *	    \file       htdocs/openstreetmap/admin/openstreetmap_geo.php
 *      \ingroup    openstreetmap
 *      \brief      Setup page for openstreetmap module (Geocoding, address autofill and map tiles)
 */

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

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

if (!$user->admin) {
	accessforbidden();
}

$langs->loadLangs(array("admin", "other", "openstreetmap@openstreetmap"));

$action = GETPOST('action', 'aZ09');

$textconsts = array(
	'OPENSTREETMAP_NOMINATIM_EMAIL' => 'alphanohtml',
	'OPENSTREETMAP_NOMINATIM_URL' => 'alphanohtml',
	'OPENSTREETMAP_TILE_URL' => 'alphanohtml',
	'OPENSTREETMAP_TILE_ATTRIBUTION' => 'restricthtml',
);


/*
 * Actions
 */

if ($action == 'save') {
	$error = 0;
	$db->begin();
	if (dolibarr_set_const($db, 'OPENSTREETMAP_PDOK_AUTOFILL', GETPOST('OPENSTREETMAP_PDOK_AUTOFILL', 'int') ? '1' : '0', 'chaine', 0, '', $conf->entity) < 0) {
		$error++;
	}
	foreach ($textconsts as $key => $check) {
		$value = trim(GETPOST($key, $check));
		if (in_array($key, array('OPENSTREETMAP_NOMINATIM_URL', 'OPENSTREETMAP_TILE_URL')) && $value != '' && !preg_match('/^https:\/\//i', $value)) {
			setEventMessages($langs->trans("OpenStreetMapErrorUrlMustBeHttps", $key), null, 'errors');
			$error++;
			continue;
		}
		if ($value == '') {
			$res = dolibarr_del_const($db, $key, $conf->entity);
		} else {
			$res = dolibarr_set_const($db, $key, $value, 'chaine', 0, '', $conf->entity);
		}
		if ($res < 0) {
			$error++;
		}
	}
	if (!$error) {
		$db->commit();
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		$db->rollback();
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

if ($action == 'clearcache') {
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."openstreetmap_geo";
	if ($db->query($sql)) {
		setEventMessages($langs->trans("OpenStreetMapCacheCleared"), null, 'mesgs');
	} else {
		setEventMessages($db->lasterror(), null, 'errors');
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

print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="save">';

print dol_get_fiche_head($head, 'geocoding', $langs->trans("OpenStreetMapTools"), -1);

print '<span class="opacitymedium">'.$langs->trans("OpenStreetMapGeocodingDesc").'</span><br><br>';

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPdokAutofill").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapPdokAutofillDesc").'</span></td><td>';
print $form->selectyesno('OPENSTREETMAP_PDOK_AUTOFILL', getDolGlobalInt('OPENSTREETMAP_PDOK_AUTOFILL'), 1);
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapNominatimEmail").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapNominatimEmailDesc").'</span></td><td>';
print '<input type="email" class="flat minwidth300" name="OPENSTREETMAP_NOMINATIM_EMAIL" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapNominatimUrl").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_NOMINATIM_URL" placeholder="https://nominatim.openstreetmap.org/search" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_NOMINATIM_URL')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapTileUrl").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_TILE_URL" placeholder="https://tile.openstreetmap.org/{z}/{x}/{y}.png" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_TILE_URL')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapTileAttribution").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_TILE_ATTRIBUTION" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_TILE_ATTRIBUTION')).'">';
print '</td></tr>';

print '</table>';
print '</div>';

print dol_get_fiche_end();

print '<div class="center"><input type="submit" class="button button-save" value="'.$langs->trans("Save").'"></div>';
print '</form>';

// Cache of coordinates
$nb = 0;
$resql = $db->query("SELECT COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."openstreetmap_geo");
if ($resql && ($obj = $db->fetch_object($resql))) {
	$nb = $obj->nb;
}
print '<br>';
print load_fiche_titre($langs->trans("OpenStreetMapCache"), '', '');
print $langs->trans("OpenStreetMapCacheDesc", $nb).'<br><br>';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="clearcache">';
print '<input type="submit" class="button" value="'.$langs->trans("OpenStreetMapClearCache").'">';
print '</form>';

llxFooter();

$db->close();
