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

// url is:  maps_all.php?mode=thirdparty|contact|member&max=max

$mode = GETPOST('mode', 'aZ09');
if (empty($mode)) {
	$mode = 'thirdparty';
}
$max = GETPOSTISSET('max') ? GETPOST('max', 'int') : 25;	// Max number of new addresses to geocode on this page load
$socid = GETPOST('socid', 'int');

$search_sale = GETPOST('search_sale', 'int');
$search_tag_customer = GETPOST('search_tag_customer', 'int');
$search_tag_member = GETPOST('search_tag_member', 'int');
$search_customer = GETPOST('search_customer', 'alpha');
$search_supplier = GETPOST('search_supplier', 'alpha');
$search_status = GETPOST('search_status', 'intcomma');
if ($search_status == '') {
	$search_status = '1';
}

// Security check
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


/*
 * View
 */

// Geocoding with Nominatim is limited to 1 request per second
@set_time_limit(600);

$form = new Form($db);
$formother = new FormOther($db);

$sqlselect = "c.code as country_code, c.label as country,";
$sqlselect .= " g.rowid as gid, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label";

if ($mode == 'thirdparty') {
	$title = $langs->trans("OpenStreetMapMapOfThirdparties");
	$sql = "SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town, s.phone, s.email, ".$sqlselect;
	$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON s.fk_pays = c.rowid";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."openstreetmap_geo as g ON s.rowid = g.fk_object AND g.type_object = 'thirdparty'";
	$sql .= " WHERE s.entity IN (".getEntity('societe').")";
	if (!$user->hasRight('societe', 'client', 'voir') && !$socid) {
		$sql .= " AND s.rowid IN (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_user = ".((int) $user->id).")";
	}
	if ($search_sale > 0) {
		$sql .= " AND s.rowid IN (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_user = ".((int) $search_sale).")";
	}
	if ($socid > 0) {
		$sql .= " AND s.rowid = ".((int) $socid);
	}
	if ($search_tag_customer > 0) {
		$sql .= " AND s.rowid IN (SELECT cs.fk_soc FROM ".MAIN_DB_PREFIX."categorie_societe as cs WHERE cs.fk_categorie = ".((int) $search_tag_customer).")";
	}
	if ($search_customer != '' && $search_customer != '-1') {
		$filterclient = '1,2,3';
		if ($search_customer == 2) {
			$filterclient = '2,3';
		}
		if ($search_customer == 1) {
			$filterclient = '1,3';
		}
		$sql .= " AND s.client IN (".$db->sanitize($filterclient).")";
	}
	if ($search_supplier != '' && $search_supplier != '-1') {
		$sql .= " AND s.fournisseur = ".((int) $search_supplier);
	}
	if ($search_status != '' && $search_status != '-1') {
		$sql .= " AND s.status = ".((int) $search_status);
	}
} elseif ($mode == 'contact') {
	$title = $langs->trans("OpenStreetMapMapOfContacts");
	$sql = "SELECT s.rowid as id, s.lastname, s.firstname, s.address, s.zip, s.town, s.phone, s.email, ".$sqlselect;
	$sql .= " FROM ".MAIN_DB_PREFIX."socpeople as s";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON s.fk_pays = c.rowid";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."openstreetmap_geo as g ON s.rowid = g.fk_object AND g.type_object = 'contact'";
	$sql .= " WHERE s.entity IN (".getEntity('contact').")";
	$sql .= " AND (s.priv = 0 OR s.fk_user_creat = ".((int) $user->id).")";
	if (!$user->hasRight('societe', 'client', 'voir') && !$socid) {
		$sql .= " AND (s.fk_soc IS NULL OR s.fk_soc IN (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_user = ".((int) $user->id)."))";
	}
	if ($socid > 0) {
		$sql .= " AND s.fk_soc = ".((int) $socid);
	}
	if ($search_status != '' && $search_status != '-1') {
		$sql .= " AND s.statut = ".((int) $search_status);
	}
} else {
	$title = $langs->trans("OpenStreetMapMapOfMembers");
	$sql = "SELECT s.rowid as id, s.lastname, s.firstname, s.societe, s.address, s.zip, s.town, s.phone, s.email, ".$sqlselect;
	$sql .= " FROM ".MAIN_DB_PREFIX."adherent as s";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON s.country = c.rowid";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."openstreetmap_geo as g ON s.rowid = g.fk_object AND g.type_object = 'member'";
	$sql .= " WHERE s.statut = 1";
	$sql .= " AND s.entity IN (".getEntity('adherent').")";
	if ($search_tag_member > 0) {
		$sql .= " AND s.rowid IN (SELECT cs.fk_member FROM ".MAIN_DB_PREFIX."categorie_member as cs WHERE cs.fk_categorie = ".((int) $search_tag_member).")";
	}
}
// Addresses never searched first, so each page load completes the map
$sql .= " ORDER BY g.rowid ASC, s.rowid ASC";

llxHeader('', $title);

print load_fiche_titre($title, '', ($mode == 'member' ? 'member' : ($mode == 'contact' ? 'contact' : 'company')));

// Filters
$param = '';
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
	if (isModEnabled('categorie')) {
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
if ($mode == 'member' && isModEnabled('categorie')) {
	print '<div class="divsearchfield">';
	print img_picto($langs->trans("MembersCategoriesShort"), 'category', 'class="paddingrightonly"');
	print $formother->select_categories(3, $search_tag_member, 'search_tag_member', 0, $langs->trans("MembersCategoriesShort"), 'maxwidth250');
	print '</div>';
}
print '<input type="submit" class="button smallpaddingimp" value="'.$langs->trans("Search").'">';
print '</form><br>';

foreach (array('search_customer', 'search_supplier', 'search_sale', 'search_tag_customer', 'search_tag_member', 'search_status') as $key) {
	if ($$key != '' && $$key != '-1') {
		$param .= '&'.$key.'='.urlencode($$key);
	}
}

// Load and geocode addresses
$markers = array();
$errors = array();
$nbnew = 0;
$nbpending = 0;
$num = 0;

dol_syslog("openstreetmap maps_all search addresses", LOG_DEBUG);
$resql = $db->query($sql);
if (!$resql) {
	dol_print_error($db);
} else {
	$num = $db->num_rows($resql);
	while ($obj = $db->fetch_object($resql)) {
		if (empty($obj->country_code)) {
			$obj->country_code = $mysoc->country_code;
		}
		$name = !empty($obj->name) ? $obj->name : trim((!empty($obj->societe) ? $obj->societe.' - ' : '').$obj->firstname.' '.$obj->lastname);
		$cached = array('rowid' => $obj->gid, 'latitude' => $obj->latitude, 'longitude' => $obj->longitude, 'address' => $obj->gaddress, 'result_code' => $obj->result_code, 'result_label' => $obj->result_label);

		$nosearch = ($max > 0 && $nbnew >= $max) ? 1 : 0;
		$point = openstreetmap_get_coordinates($db, $mode, $obj->id, $obj, $cached, $nosearch);
		if (empty($point['cached']) && empty($point['pending'])) {
			$nbnew++;
		}

		if (isset($point['lat'])) {
			if ($mode == 'thirdparty') {
				$url = DOL_URL_ROOT.'/societe/card.php?socid='.$obj->id;
			} elseif ($mode == 'contact') {
				$url = DOL_URL_ROOT.'/contact/card.php?id='.$obj->id;
			} else {
				$url = DOL_URL_ROOT.'/adherents/card.php?rowid='.$obj->id;
			}
			$popup = '<a href="'.dol_escape_htmltag($url).'"><b>'.dol_escape_htmltag($name).'</b></a>';
			$popup .= '<br>'.dol_escape_htmltag(dol_format_address($obj, 1, ', '));
			if (!empty($obj->phone)) {
				$popup .= '<br>'.dol_escape_htmltag($obj->phone);
			}
			if (!empty($obj->email)) {
				$popup .= '<br>'.dol_escape_htmltag($obj->email);
			}
			$markers[] = array('lat' => (float) $point['lat'], 'lon' => (float) $point['lon'], 'popup' => $popup);
		} elseif (!empty($point['pending'])) {
			$nbpending++;
		} elseif ($point['error'] != 'NO_ADDRESS') {
			$errors[] = array('id' => $obj->id, 'name' => $name, 'address' => dol_format_address($obj, 1, ', '), 'label' => $point['label']);
		}
	}
}

print '<div class="info">'.$langs->trans("OpenStreetMapCountGeo", $num, count($markers), count($errors), $nbpending).'</div>';
if ($nbpending > 0) {
	print $langs->trans("OpenStreetMapClickToGeocodeMore").': ';
	foreach (array(25, 50, 100, 250) as $nb) {
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.urlencode($mode).'&max='.$nb.$param.'">'.$nb.'</a>';
	}
	print '<br><span class="opacitymedium">'.$langs->trans("OpenStreetMapNominatimSlow").'</span><br>';
}
print '<br>';

print openstreetmap_include_leaflet(1);
print '<div id="map" class="divmap" style="width: 100%; height: 600px;"></div>';
print '<script>
(function() {
	var markers = '.json_encode($markers).';
	var map = L.map("map").setView(['.(float) getDolGlobalString('OPENSTREETMAP_DEFAULT_LAT', '52.2').', '.(float) getDolGlobalString('OPENSTREETMAP_DEFAULT_LON', '5.3').'], 7);
	L.tileLayer("'.dol_escape_js(openstreetmap_get_tile_url()).'", {
		maxZoom: 19,
		attribution: "'.dol_escape_js(openstreetmap_get_tile_attribution()).'"
	}).addTo(map);
	L.control.scale().addTo(map);
	var group = (typeof L.markerClusterGroup === "function") ? L.markerClusterGroup() : L.featureGroup();
	markers.forEach(function(m) {
		L.marker([m.lat, m.lon]).bindPopup(m.popup).addTo(group);
	});
	map.addLayer(group);
	if (markers.length > 0) {
		map.fitBounds(group.getBounds(), {padding: [20, 20], maxZoom: 15});
	}
})();
</script>';

// Addresses that could not be located
if (count($errors)) {
	print '<br>'.$langs->trans("OpenStreetMapFollowingAddressCantBeLocalized", count($errors)).':<br>';
	print '<ul>';
	foreach ($errors as $err) {
		print '<li>'.dol_escape_htmltag($err['name']).' - '.dol_escape_htmltag($err['address']).' <span class="opacitymedium">('.dol_escape_htmltag($err['label']).')</span></li>';
	}
	print '</ul>';
}

llxFooter();

$db->close();
