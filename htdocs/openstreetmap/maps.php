<?php
/* Copyright (C) 2011 Jonathan
 * Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       \file       htdocs/openstreetmap/maps.php
 *       \ingroup    openstreetmap
 *       \brief      Main openstreetmap area page
 *       \author     Laurent Destailleur
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
 * @var DoliDb $db
 */
require_once DOL_DOCUMENT_ROOT."/core/lib/company.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/member.lib.php";
require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";
dol_include_once("/openstreetmap/lib/openstreetmap.lib.php");

$langs->load("openstreetmap@openstreetmap");

// url is:  maps.php?mode=thirdparty|contact|member|user&id=id

$mode=GETPOST('mode', 'aZ09');
$address='';
$object=null;

// Load third party
if (empty($mode) || $mode=='societe' || $mode=='thirdparty') {
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	$id = GETPOST('id', 'int');
	$object = new Societe($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
}
if ($mode=='contact') {
	include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
	$id = GETPOST('id', 'int');
	$object = new Contact($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
}
if ($mode=='member') {
	include_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
	$id = GETPOST('id', 'int');
	$object = new Adherent($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
}
if ($mode=='user') {
	include_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
	$id = GETPOST('id', 'int');
	$object = new User($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
}
if (!is_object($object) || $object->id <= 0) {
	accessforbidden('Bad value for mode or id');
}

// Security check
if ($mode == 'contact') {
	$result = restrictedArea($user, 'contact', $object->id, 'socpeople&societe', '', '', 'rowid');
} elseif ($mode == 'member') {
	$result = restrictedArea($user, 'adherent', $object->id);
} elseif ($mode == 'user') {
	if ($object->id != $user->id && !$user->hasRight('user', 'user', 'lire') && !$user->admin) {
		accessforbidden();
	}
} else {
	$result = restrictedArea($user, 'societe', $object->id, '&societe');
}

if (isset($object->logo) && !is_null($object->logo)) {
	$showlogo = true;
} else {
	$showlogo = false;
}
if (isset($object->barcode_type) && !is_null($object->barcode_type)) {
	$showbarcode = true;
} else {
	$showbarcode = false;
}

/*
 * View
 */

llxHeader();

$form=new Form($db);

$content = "Default content";
$act = "";

//On fabrique les onglets
$head=array();
$title='';
$picto='';
if (empty($mode) || $mode=='thirdparty') {
	$head = societe_prepare_head($object);
	$title=$langs->trans("ThirdParty");
	$picto='company';
}
if ($mode=='contact') {
	$head = contact_prepare_head($object);
	$title=$langs->trans("ContactsAddresses");
	$picto='contact';
}
if ($mode=='member') {
	$head = member_prepare_head($object);
	$title=$langs->trans("Member");
	$picto='user';
}
if ($mode=='user') {
	require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
	$head = user_prepare_head($object);
	$title=$langs->trans("User");
	$picto='user';
}

dol_fiche_head($head, 'gmaps', $title, 0, $picto);


print '<table class="border" width="100%">';

// Name
print '<tr><td width="20%">'.$langs->trans('ThirdPartyName').'</td>';
print '<td colspan="3">';
print $form->showrefnav($object, 'id', '', (isset($user->societe_id) && $user->societe_id ? 0:1), 'rowid', 'nom', '', '&mode='.$mode);
print '</td>';
print '</tr>';


// Status
print '<tr><td>'.$langs->trans("Status").'</td>';
print '<td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
print $object->getLibStatut(2);
print '</td>';
// print $htmllogobar; $htmllogobar='';
print '</tr>';

// Address
print "<tr><td valign=\"top\">".$langs->trans('Address').'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
dol_print_address($object->address, 'gmap', $mode, $object->id);
print "</td></tr>";

// Zip / Town
print '<tr><td width="25%">'.$langs->trans('Zip').' / '.$langs->trans("Town").'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
print $object->zip.($object->zip && $object->town?" / ":"").$object->town;
print "</td>";
print '</tr>';

// Country
print '<tr><td>'.$langs->trans("Country").'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'" nowrap="nowrap">';
$img=picto_from_langcode($object->country_code);
print ($img?$img.' ':'').$object->country;
print '</td></tr>';

// State
if (empty($conf->global->SOCIETE_DISABLE_STATE)) print '<tr><td>'.$langs->trans('State').'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">'.$object->state.'</td>';

print '</table>';


// Show maps

if ($address && $address != $object->country) {
	$obj = new stdClass();
	$obj->address = $object->address;
	$obj->zip = $object->zip;
	$obj->town = $object->town;
	$obj->country_code = $object->country_code;
	$obj->country = $object->country;
	$obj->state = isset($object->state) ? $object->state : '';

	$typeobject = (empty($mode) || $mode == 'societe') ? 'thirdparty' : $mode;
	$point = openstreetmap_get_coordinates($db, $typeobject, $object->id, $obj);

	print '<br><div class="center">';
	if (isset($point['lat'])) {
		$zoom = getDolGlobalInt('OPENSTREETMAP_MAPS_ZOOM_LEVEL', 15);

		print openstreetmap_include_leaflet(0);
		print '<div id="map" class="divmap" style="width: 90%; height: 500px; margin: 0 auto;"></div>';
		print '<script>
		(function() {
			var map = L.map("map").setView(['.((float) $point['lat']).', '.((float) $point['lon']).'], '.((int) $zoom).');
			L.tileLayer("'.dol_escape_js(openstreetmap_get_tile_url()).'", {
				maxZoom: 19,
				attribution: "'.dol_escape_js(openstreetmap_get_tile_attribution()).'"
			}).addTo(map);
			L.control.scale().addTo(map);
			L.marker(['.((float) $point['lat']).', '.((float) $point['lon']).']).addTo(map)
				.bindPopup("'.dol_escape_js('<b>'.dol_escape_htmltag(isset($object->name) && $object->name ? $object->name : $object->getFullName($langs)).'</b><br>'.dol_escape_htmltag($address)).'");
		})();
		</script>';
	} else {
		print $langs->trans('OpenStreetMapMapsAddressNotFound');
		if (!empty($point['label'])) {
			print ' <span class="opacitymedium">('.dol_escape_htmltag($point['label']).')</span>';
		}
	}
	print '</div>';
} else {
	print '<br>'.$langs->trans("OpenStreetMapAddressNotDefined").'<br>';
}

dol_fiche_end();

llxfooter();

$db->close();
