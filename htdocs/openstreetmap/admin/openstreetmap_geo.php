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
dol_include_once("/openstreetmap/lib/postcodeeu.lib.php");

if (!$user->admin) {
	accessforbidden();
}

$langs->loadLangs(array("admin", "other", "openstreetmap@openstreetmap"));

$action = GETPOST('action', 'aZ09');

$textconsts = array(
	'OPENSTREETMAP_NOMINATIM_EMAIL' => 'alphanohtml',
	'OPENSTREETMAP_NOMINATIM_URL' => 'alphanohtml',
	'OPENSTREETMAP_PHOTON_URL' => 'alphanohtml',
	'OPENSTREETMAP_TILE_URL' => 'alphanohtml',
	'OPENSTREETMAP_TILE_ATTRIBUTION' => 'restricthtml',
	'OPENSTREETMAP_POSTCODEEU_KEY' => 'alphanohtml',
);

// Numeric settings: min, max, default (an empty field restores the default; null default = automatic)
$numconsts = array(
	'OPENSTREETMAP_GEOCODE_PER_VISIT' => array(1, 1000, 25),
	'OPENSTREETMAP_NOMINATIM_DELAY_MS' => array(0, 10000, null),
	'OPENSTREETMAP_CONNECT_TIMEOUT' => array(1, 30, 3),
	'OPENSTREETMAP_RESPONSE_TIMEOUT' => array(1, 120, 8),
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
	if (dolibarr_set_const($db, 'OPENSTREETMAP_EU_AUTOFILL', GETPOST('OPENSTREETMAP_EU_AUTOFILL', 'int') ? '1' : '0', 'chaine', 0, '', $conf->entity) < 0) {
		$error++;
	}
	foreach ($textconsts as $key => $check) {
		$value = trim(GETPOST($key, $check));
		if (in_array($key, array('OPENSTREETMAP_NOMINATIM_URL', 'OPENSTREETMAP_PHOTON_URL', 'OPENSTREETMAP_TILE_URL')) && $value != '' && !preg_match('/^https:\/\//i', $value)) {
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
	foreach ($numconsts as $key => $limits) {
		$value = trim(GETPOST($key, 'alphanohtml'));
		if ($value === '') {
			$res = dolibarr_del_const($db, $key, $conf->entity);
		} elseif (!ctype_digit($value) || (int) $value < $limits[0] || (int) $value > $limits[1]) {
			setEventMessages($langs->trans("OpenStreetMapErrorNumberBetween", $langs->transnoentitiesnoconv($key), $limits[0], $limits[1]), null, 'errors');
			$error++;
			continue;
		} else {
			$res = dolibarr_set_const($db, $key, (string) ((int) $value), 'chaine', 0, '', $conf->entity);
		}
		if ($res < 0) {
			$error++;
		}
	}
	// Languages known by the Photon server: 2 letter codes separated by commas
	$photonlangs = strtolower(str_replace(' ', '', GETPOST('OPENSTREETMAP_PHOTON_LANGS', 'alphanohtml')));
	if ($photonlangs === '') {
		if (dolibarr_del_const($db, 'OPENSTREETMAP_PHOTON_LANGS', $conf->entity) < 0) {
			$error++;
		}
	} elseif (!preg_match('/^[a-z]{2}(,[a-z]{2})*$/', $photonlangs)) {
		setEventMessages($langs->trans("OpenStreetMapErrorPhotonLangs"), null, 'errors');
		$error++;
	} elseif (dolibarr_set_const($db, 'OPENSTREETMAP_PHOTON_LANGS', implode(',', array_unique(explode(',', $photonlangs))), 'chaine', 0, '', $conf->entity) < 0) {
		$error++;
	}

	// Secret of Postcode.eu: an empty field keeps the stored secret (it is never shown), the checkbox removes it
	$secret = trim(GETPOST('OPENSTREETMAP_POSTCODEEU_SECRET', 'none'));
	if (GETPOSTINT('OPENSTREETMAP_POSTCODEEU_SECRET_CLEAR')) {
		if (dolibarr_del_const($db, 'OPENSTREETMAP_POSTCODEEU_SECRET', $conf->entity) < 0) {
			$error++;
		}
	} elseif ($secret !== '') {
		if (!preg_match('/^[\x21-\x7E]{1,128}$/', $secret)) {
			setEventMessages($langs->trans("ErrorBadValueForParameter", '', 'OPENSTREETMAP_POSTCODEEU_SECRET'), null, 'errors');
			$error++;
		} elseif (dolibarr_set_const($db, 'OPENSTREETMAP_POSTCODEEU_SECRET', $secret, 'chaine', 0, '', $conf->entity) < 0) {
			$error++;
		}
	}
	$pccountries = GETPOST('OPENSTREETMAP_POSTCODEEU_COUNTRIES', 'array:aZ09');
	$pccountries = array_values(array_intersect(array_keys(openstreetmap_postcodeeu_countries()), is_array($pccountries) ? $pccountries : array()));
	if (dolibarr_set_const($db, 'OPENSTREETMAP_POSTCODEEU_COUNTRIES', implode(',', $pccountries), 'chaine', 0, '', $conf->entity) < 0) {
		$error++;
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
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."openstreetmap_geo WHERE entity = ".((int) $conf->entity);
	if ($db->query($sql)) {
		setEventMessages($langs->trans("OpenStreetMapCacheCleared"), null, 'mesgs');
	} else {
		dol_syslog("openstreetmap clear cache: ".$db->lasterror(), LOG_ERR);
		setEventMessages($langs->trans("Error"), null, 'errors');
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

// What the module can do in each country
print '<div class="info">'.$langs->trans("OpenStreetMapQualityTitle").'<ul>';
print '<li>'.$langs->trans("OpenStreetMapQualityOSM").'</li>';
print '<li>'.$langs->trans("OpenStreetMapQualityFairUse").'</li>';
print '<li>'.$langs->trans("OpenStreetMapQualityNL").'</li>';
print '<li>'.$langs->trans("OpenStreetMapQualityDictionary").'</li>';
print '<li>'.$langs->trans("OpenStreetMapQualityPaid").'</li>';
print '</ul></div><br>';

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPdokAutofill").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapPdokAutofillDesc").'</span></td><td>';
print $form->selectyesno('OPENSTREETMAP_PDOK_AUTOFILL', getDolGlobalInt('OPENSTREETMAP_PDOK_AUTOFILL'), 1);
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapEuAutofill").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEuAutofillDesc").'</span></td><td>';
print $form->selectyesno('OPENSTREETMAP_EU_AUTOFILL', getDolGlobalInt('OPENSTREETMAP_EU_AUTOFILL'), 1);
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapNominatimEmail").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapNominatimEmailDesc").'</span></td><td>';
print '<input type="email" class="flat minwidth300" name="OPENSTREETMAP_NOMINATIM_EMAIL" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_NOMINATIM_EMAIL')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapNominatimUrl").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_NOMINATIM_URL" placeholder="https://nominatim.openstreetmap.org/search" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_NOMINATIM_URL')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPhotonUrl").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_PHOTON_URL" placeholder="https://photon.komoot.io/api" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_PHOTON_URL')).'">';
print '</td></tr>';

// Languages of Photon and delay of Nominatim, useful with own servers
print '<tr class="oddeven"><td>'.$langs->trans("OPENSTREETMAP_PHOTON_LANGS").'<br><span class="opacitymedium small">'.$langs->trans("OPENSTREETMAP_PHOTON_LANGSDesc").'</span></td><td>';
print '<input type="text" class="flat width200" name="OPENSTREETMAP_PHOTON_LANGS" placeholder="en,de,fr" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_PHOTON_LANGS')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OPENSTREETMAP_NOMINATIM_DELAY_MS").'<br><span class="opacitymedium small">'.$langs->trans("OPENSTREETMAP_NOMINATIM_DELAY_MSDesc").'</span></td><td>';
print '<input type="number" class="flat width100" min="0" max="10000" step="100" name="OPENSTREETMAP_NOMINATIM_DELAY_MS" placeholder="'.dol_escape_htmltag($langs->trans("OpenStreetMapAutomatic")).'" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_NOMINATIM_DELAY_MS')).'"> ms';
print ' <span class="opacitymedium small">('.$langs->trans("OpenStreetMapDelayInUse", openstreetmap_nominatim_delay()).')</span>';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapTileUrl").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_TILE_URL" placeholder="https://tile.openstreetmap.org/{z}/{x}/{y}.png" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_TILE_URL')).'">';
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapTileAttribution").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapEmptyForDefault").'</span></td><td>';
print '<input type="text" class="flat minwidth500" name="OPENSTREETMAP_TILE_ATTRIBUTION" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_TILE_ATTRIBUTION')).'">';
print '</td></tr>';

// Limits
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("OpenStreetMapLimits").'</td></tr>';
foreach (array('OPENSTREETMAP_GEOCODE_PER_VISIT' => '', 'OPENSTREETMAP_CONNECT_TIMEOUT' => 's', 'OPENSTREETMAP_RESPONSE_TIMEOUT' => 's') as $key => $unit) {
	print '<tr class="oddeven"><td>'.$langs->trans($key).'<br><span class="opacitymedium small">'.$langs->trans($key.'Desc').'</span></td><td>';
	print '<input type="number" class="flat width100" min="'.((int) $numconsts[$key][0]).'" max="'.((int) $numconsts[$key][1]).'" name="'.$key.'" placeholder="'.((int) $numconsts[$key][2]).'" value="'.dol_escape_htmltag(getDolGlobalString($key)).'">'.($unit ? ' '.$unit : '');
	print '</td></tr>';
}

// Optional paid service: Postcode.eu
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("OpenStreetMapPostcodeEu").'</td></tr>';
print '<tr class="oddeven"><td colspan="2"><span class="opacitymedium small">'.$langs->trans("OpenStreetMapPostcodeEuDesc").'</span>';
print '<br><span class="warning small">'.$langs->trans("OpenStreetMapPostcodeEuPrivacy").'</span></td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPostcodeEuKey").'</td><td>';
print '<input type="text" class="flat minwidth300" name="OPENSTREETMAP_POSTCODEEU_KEY" autocomplete="off" value="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_POSTCODEEU_KEY')).'">';
print '</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPostcodeEuSecret").'</td><td>';
print '<input type="password" class="flat minwidth300" name="OPENSTREETMAP_POSTCODEEU_SECRET" autocomplete="new-password" value="" placeholder="'.dol_escape_htmltag(getDolGlobalString('OPENSTREETMAP_POSTCODEEU_SECRET') ? $langs->trans("OpenStreetMapPostcodeEuSecretSet") : '').'">';
if (getDolGlobalString('OPENSTREETMAP_POSTCODEEU_SECRET')) {
	print ' <input type="checkbox" id="pcclear" name="OPENSTREETMAP_POSTCODEEU_SECRET_CLEAR" value="1"> <label for="pcclear">'.$langs->trans("Delete").'</label>';
}
print '</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPostcodeEuCountries").'<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapPostcodeEuCountriesDesc").'</span></td><td>';
$pcchoices = array();
foreach (array_keys(openstreetmap_postcodeeu_countries()) as $code) {
	$label = $langs->transnoentitiesnoconv("Country".$code);
	$pcchoices[$code] = ($label != "Country".$code ? $label : $code);
}
asort($pcchoices);
print $form->multiselectarray('OPENSTREETMAP_POSTCODEEU_COUNTRIES', $pcchoices, openstreetmap_postcodeeu_selected_countries(), 0, 0, 'minwidth300');
print '</td></tr>';
$pcencrypted = openstreetmap_postcodeeu_stored_encrypted($db);
if ($pcencrypted >= 0) {
	print '<tr class="oddeven"><td>'.$langs->trans("OpenStreetMapPostcodeEuStorage").'</td><td>';
	if ($pcencrypted) {
		print $langs->trans("OpenStreetMapPostcodeEuStoredEncrypted");
	} else {
		print '<span class="warning">'.$langs->trans("OpenStreetMapPostcodeEuStoredPlain").'</span>';
	}
	print '</td></tr>';
}

print '</table>';
print '</div>';

print dol_get_fiche_end();

print '<div class="center"><input type="submit" class="button button-save" value="'.$langs->trans("Save").'"></div>';
print '</form>';

// Cache of coordinates
$nb = 0;
$resql = $db->query("SELECT COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."openstreetmap_geo WHERE entity = ".((int) $conf->entity));
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
