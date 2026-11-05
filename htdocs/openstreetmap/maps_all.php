<?php
/* Copyright (C) 2011-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       \file       htdocs/openstreetmap/maps_all.php
 *       \ingroup    openstreetmap
 *       \brief      Page to show all third parties, contacts or members on an OpenStreetMap map
 */

if (!defined('MAIN_SECURITY_FORCERP')) {
	// https://wiki.openstreetmap.org/wiki/Referer
	define('MAIN_SECURITY_FORCERP', 'strict-origin-when-cross-origin');
}

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
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var User $user
 * @var Translate $langs
 * @var Societe $mysoc
 */
require_once DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php";
require_once DOL_DOCUMENT_ROOT."/societe/class/societe.class.php";
require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";
dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

$langs->loadLangs(array("openstreetmap@openstreetmap", "companies", "commercial", "categories", "members"));

// url is:  maps_all.php?mode=thirdparty|contact|member

$mode = GETPOST('mode', 'aZ09');
if (empty($mode)) {
	$mode = 'thirdparty';
}
$socid = GETPOSTINT('socid');

$search_sale = GETPOSTINT('search_sale');
$search_tag_customer = GETPOSTINT('search_tag_customer');
$search_tag_member = GETPOSTINT('search_tag_member');
$search_customer = GETPOST('search_customer', 'alpha');
$search_supplier = GETPOST('search_supplier', 'alpha');
$search_status = GETPOST('search_status', 'intcomma');
if ($search_status == '') {
	$search_status = '1';
}

// Security check
if (!isModEnabled('openstreetmap')) {
	accessforbidden();
}
if (!empty($user->socid)) {
	$socid = $user->socid;
}
if ($mode == 'thirdparty') {
	restrictedArea($user, 'societe', $socid, '&societe');
} elseif ($mode == 'contact') {
	restrictedArea($user, 'contact', 0, 'socpeople&societe', '', '', 'rowid');
} elseif ($mode == 'member') {
	restrictedArea($user, 'adherent');
} else {
	accessforbidden('Bad value for mode param into url');
}

$filters = array(
	'socid' => $socid,
	'search_sale' => $search_sale,
	'search_tag_customer' => $search_tag_customer,
	'search_tag_member' => $search_tag_member,
	'search_customer' => $search_customer,
	'search_supplier' => $search_supplier,
	'search_status' => $search_status,
);


/*
 * View
 */

$form = new Form($db);
$formother = new FormOther($db);

if ($mode == 'thirdparty') {
	$title = $langs->trans("OpenStreetMapMapOfThirdparties");
} elseif ($mode == 'contact') {
	$title = $langs->trans("OpenStreetMapMapOfContacts");
} else {
	$title = $langs->trans("OpenStreetMapMapOfMembers");
}

llxHeader('', $title);

print load_fiche_titre($title, '', ($mode == 'member' ? 'member' : ($mode == 'contact' ? 'contact' : 'company')));

// Filters
print '<form name="formsearch" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="mode" value="'.dol_escape_htmltag($mode).'">';
if ($mode == 'thirdparty') {
	print '<div class="divsearchfield">'.$langs->trans('ProspectCustomer').': ';
	print '<select class="flat" name="search_customer">';
	print '<option value="-1">&nbsp;</option>';
	print '<option value="2"'.($search_customer == 2 ? ' selected' : '').'>'.$langs->trans('Prospect').'</option>';
	print '<option value="1"'.($search_customer == 1 ? ' selected' : '').'>'.$langs->trans('Customer').'</option>';
	print '</select></div>';
	if (isModEnabled('fournisseur') || isModEnabled('supplier_order') || isModEnabled('supplier_invoice')) {
		print '<div class="divsearchfield">'.$langs->trans('Supplier').': ';
		print $form->selectyesno("search_supplier", $search_supplier, 1, false, 1);
		print '</div>';
	}
	if ($user->hasRight('societe', 'client', 'voir')) {
		print '<div class="divsearchfield">';
		print img_picto($langs->trans("ThirdPartiesOfSaleRepresentative"), 'company', 'class="paddingrightonly"');
		print $formother->select_salesrepresentatives($search_sale, 'search_sale', $user, 0, $langs->trans('ThirdPartiesOfSaleRepresentative'), 'maxwidth250');
		print '</div>';
	}
	if (isModEnabled('category') || !empty($conf->modules['categorie'])) {	// 'category' is known by isModEnabled() only on recent versions
		print '<div class="divsearchfield">';
		print img_picto($langs->trans("CustomersCategoriesShort"), 'category', 'class="paddingrightonly"');
		print $formother->select_categories(2, $search_tag_customer, 'search_tag_customer', 0, $langs->trans("CustomersCategoriesShort"), 'maxwidth250');
		print '</div>';
	}
}
if ($mode == 'thirdparty' || $mode == 'contact') {
	print '<div class="divsearchfield">'.$langs->trans('Status').': ';
	print $form->selectarray('search_status', array('0' => $langs->trans('ActivityCeased'), '1' => $langs->trans('InActivity')), $search_status, 1, 0, 0, '', 0, 0, 0, '', 'maxwidth150');
	print '</div>';
}
if ($mode == 'member' && (isModEnabled('category') || !empty($conf->modules['categorie']))) {
	print '<div class="divsearchfield">';
	print img_picto($langs->trans("MembersCategoriesShort"), 'category', 'class="paddingrightonly"');
	print $formother->select_categories(3, $search_tag_member, 'search_tag_member', 0, $langs->trans("MembersCategoriesShort"), 'maxwidth250');
	print '</div>';
}
print '<input type="submit" class="button smallpaddingimp" value="'.$langs->trans("Search").'">';
print '</form><br>';

// Load the objects with their stored coordinates. No external service is called here: addresses not located
// yet are located afterwards by the browser, one by one (ajax/geocode.php), so this page shows at once.
$markers = array();
$errors = array();
$pending = array();
$num = 0;

dol_syslog("openstreetmap maps_all search addresses", LOG_DEBUG);
$resql = $db->query(openstreetmap_list_sql($db, $user, $mode, $filters));
if (!$resql) {
	dol_syslog("openstreetmap maps_all: ".$db->lasterror(), LOG_ERR);
	setEventMessages($langs->trans("Error"), null, 'errors');
} else {
	$num = $db->num_rows($resql);
	while ($obj = $db->fetch_object($resql)) {
		if (empty($obj->country_code)) {
			$obj->country_code = $mysoc->country_code;
		}
		$cached = array('rowid' => $obj->gid, 'latitude' => $obj->latitude, 'longitude' => $obj->longitude, 'address' => $obj->gaddress, 'result_code' => $obj->result_code, 'result_label' => $obj->result_label);
		$point = openstreetmap_get_coordinates($db, $mode, $obj->id, $obj, $cached, 1);

		if (isset($point['lat'])) {
			$markers[] = array('lat' => (float) $point['lat'], 'lon' => (float) $point['lon'], 'popup' => openstreetmap_list_popup($mode, $obj));
		} elseif (!empty($point['pending'])) {
			$pending[] = (int) $obj->id;
		} elseif ($point['error'] != 'NO_ADDRESS') {
			$errors[] = array('name' => openstreetmap_list_name($obj), 'address' => dol_format_address($obj, 1, ', '), 'label' => $point['label']);
		}
	}
}

print '<div class="info">';
// The counters are updated by the script below while the addresses are located
print '<span id="osm_count">'.str_replace(
	array('__NBMARKERS__', '__NBERRORS__', '__NBPENDING__'),
	array('<span id="osm_nbmarkers">'.count($markers).'</span>', '<span id="osm_nberrors">'.count($errors).'</span>', '<span id="osm_nbpending">'.count($pending).'</span>'),
	$langs->trans("OpenStreetMapCountGeo", $num, '__NBMARKERS__', '__NBERRORS__', '__NBPENDING__')
).'</span>';
if (count($pending)) {
	print '<div id="osm_progress" class="paddingtop">';
	print '<span id="osm_progress_text">'.$langs->trans("OpenStreetMapGeocodingInProgress").'</span>';
	print ' <a href="#" id="osm_stop" class="button smallpaddingimp">'.$langs->trans("OpenStreetMapStopGeocoding").'</a>';
	print '<br><span class="opacitymedium small">'.$langs->trans("OpenStreetMapNominatimSlow").'</span>';
	print '</div>';
}
print '</div>';
print '<br>';

print openstreetmap_include_leaflet(1);
print '<div id="map" class="divmap" style="width: 100%; height: 600px; min-height: 400px;"></div>';
print '<div id="osm_errors_title"'.(count($errors) ? '' : ' class="hidden"').'><br>'.$langs->trans("OpenStreetMapFollowingAddressCantBeLocalized").'</div>';
print '<ul id="osm_errors">';
foreach ($errors as $err) {
	print '<li>'.dol_escape_htmltag($err['name']).' - '.dol_escape_htmltag($err['address']).' <span class="opacitymedium">('.dol_escape_htmltag($err['label']).')</span></li>';
}
print '</ul>';

$jsconf = array(
	'markers' => $markers,
	'pending' => $pending,
	'url' => dol_buildpath('/openstreetmap/ajax/geocode.php', 1),
	'mode' => $mode,
	'filters' => $filters,
	'lat' => (float) getDolGlobalString('OPENSTREETMAP_DEFAULT_LAT', '52.2'),
	'lon' => (float) getDolGlobalString('OPENSTREETMAP_DEFAULT_LON', '5.3'),
	'tileurl' => openstreetmap_get_tile_url(),
	'attribution' => openstreetmap_get_tile_attribution(),
	'txt' => array(
		'done' => $langs->transnoentitiesnoconv("OpenStreetMapGeocodingDone"),
		'stopped' => $langs->transnoentitiesnoconv("OpenStreetMapGeocodingStopped"),
		'unavailable' => $langs->transnoentitiesnoconv("OpenStreetMapGeocodingServiceUnavailable"),
		'inprogress' => $langs->transnoentitiesnoconv("OpenStreetMapGeocodingInProgress"),
	),
);
print '<script>
(function() {
	var cfg = '.openstreetmap_json_for_script($jsconf).';

	// Fit the map to the remaining height of the window (at least 400px), also after resizing the window
	var el = document.getElementById("map");
	function fitHeight() {
		var top = el.getBoundingClientRect().top + window.pageYOffset;
		var height = window.innerHeight - top - 20;
		if (height < 400) {
			height = Math.max(400, window.innerHeight - 40);
		}
		el.style.height = height + "px";
	}
	fitHeight();

	var map = L.map("map").setView([cfg.lat, cfg.lon], 7);
	L.tileLayer(cfg.tileurl, {maxZoom: 19, attribution: cfg.attribution}).addTo(map);
	L.control.scale().addTo(map);
	var group = (typeof L.markerClusterGroup === "function") ? L.markerClusterGroup() : L.featureGroup();
	cfg.markers.forEach(function(m) {
		L.marker([m.lat, m.lon]).bindPopup(m.popup).addTo(group);
	});
	map.addLayer(group);
	if (cfg.markers.length > 0) {
		map.fitBounds(group.getBounds(), {padding: [20, 20], maxZoom: 15});
	}

	var timer = null;
	window.addEventListener("resize", function() {
		clearTimeout(timer);
		timer = setTimeout(function() {
			fitHeight();
			map.invalidateSize();
		}, 150);
	});

	// Locate the addresses not located yet, one request at a time, so the server and the external
	// services are never asked more than one address at once. Stop after 3 errors of the service in a row.
	var queue = cfg.pending.slice(0);
	var stopped = false;
	var serviceerrors = 0;
	var nbmarkers = cfg.markers.length;
	var nberrors = parseInt(document.getElementById("osm_nberrors").textContent, 10) || 0;
	function setText(id, value) {
		var e = document.getElementById(id);
		if (e) {
			e.textContent = value;
		}
	}
	function finish(message) {
		setText("osm_progress_text", message);
		var stop = document.getElementById("osm_stop");
		if (stop) {
			stop.style.display = "none";
		}
	}
	function next() {
		if (stopped) {
			finish(cfg.txt.stopped);
			return;
		}
		if (!queue.length) {
			finish(cfg.txt.done);
			return;
		}
		var id = queue.shift();
		var params = new URLSearchParams(cfg.filters);
		params.set("mode", cfg.mode);
		params.set("id", id);
		fetch(cfg.url + "?" + params.toString(), {credentials: "same-origin"})
			.then(function(r) { return r.json().catch(function() { return {error: "BAD_JSON"}; }); })
			.then(function(data) {
				if (data && typeof data.lat === "number") {
					serviceerrors = 0;
					L.marker([data.lat, data.lon]).bindPopup(data.popup).addTo(group);
					nbmarkers++;
				} else if (data && data.error === "SERVICE_UNAVAILABLE") {
					serviceerrors++;
				} else if (data && data.error && data.error !== "NO_ADDRESS") {
					serviceerrors = 0;
					nberrors++;
					var li = document.createElement("li");
					li.textContent = (data.name || "") + " - " + (data.address || "") + " (" + (data.label || data.error) + ")";
					document.getElementById("osm_errors").appendChild(li);
					document.getElementById("osm_errors_title").classList.remove("hidden");
				}
				setText("osm_nbmarkers", nbmarkers);
				setText("osm_nberrors", nberrors);
				setText("osm_nbpending", queue.length);
				if (serviceerrors >= 3) {
					finish(cfg.txt.unavailable);
					return;
				}
				next();
			})
			.catch(function() {
				serviceerrors++;
				if (serviceerrors >= 3) {
					finish(cfg.txt.unavailable);
					return;
				}
				next();
			});
	}
	var stop = document.getElementById("osm_stop");
	if (stop) {
		stop.addEventListener("click", function(e) {
			e.preventDefault();
			stopped = true;
		});
	}
	if (queue.length) {
		next();
	}
})();
</script>';

llxFooter();

$db->close();
