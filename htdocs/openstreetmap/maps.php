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

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/company.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/member.lib.php";
require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";

$langs->load("openstreetmap@openstreetmap");

// url is:  gmaps.php?mode=thirdparty|contact|member&id=id
//avoid mixing protocol on modern browsers
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protocol = 'https://';
}
else {
  $protocol = 'http://';
}

$mode=GETPOST('mode');
$address='';

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

if (isset($object->logo) && !is_null($object->logo)){
	$showlogo = true;
}else{
	$showlogo = false;
}
if (isset($object->barcode_type) && !is_null($object->barcode_type)){
	$showbarcode = true;
}else{  
	$showbarcode = false;
}

/*
 * View
 */

llxheader();

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
	print '<br><div align="center">';
	print '<div id="map" class="divmap" style="width: 90%; height: 500px; text-align: center; align: center">';

	$url='http://nominatim.openstreetmap.org/search?format=json&polygon=1&addressdetails=1&q='.urlencode($address);

	// Protocol HTTP or HTTPS
	if (preg_match('/^http/i', $url)) {
		list($usec, $sec) = explode(" ", microtime());
		$micro_start_time=((float) $usec + (float) $sec);

		include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

		$result = getURLContent($url);

		list($usec, $sec) = explode(" ", microtime());
		$micro_end_time=((float) $usec + (float) $sec);
		$end_time=((float) $sec);

		$delay=($micro_end_time-$micro_start_time);

		if (! function_exists('json_decode')) {    // Test with no response
			print 'Error: function json_decode does not exists. Check PHP module json is loaded.';
			$error++;
		}

		if (! empty($result['curl_error_no'])) {
			print 'Error result of getURLContent: '.$result['curl_error_no'];
			$error++;
		}

		if (! $error) {
			//var_dump($result['content']);
			$array = json_decode($result['content'], true);
			$lat=isset($array[0]['lat']) ? $array[0]['lat'] : false;
            $lon=isset($array[0]['lon']) ? $array[0]['lon'] : false;
			if ($lat && $lon) {
				// See example on page http://wiki.openstreetmap.org/wiki/OpenLayers_Marker
				print '<script src="'.$protocol.'openlayers.org/api/OpenLayers.js"></script>
                    <script>

             		map = new OpenLayers.Map("map", {
						controls:[
                            new OpenLayers.Control.Navigation(),
                            new OpenLayers.Control.PanZoomBar(),
                            //new OpenLayers.Control.Permalink(),
                            new OpenLayers.Control.ScaleLine({geodesic: true}),
                            //new OpenLayers.Control.Permalink(\'permalink\'),
                            new OpenLayers.Control.MousePosition(),
                            //new OpenLayers.Control.Attribution()
                            ],
                        units: \'m\',
        	            //maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
    	                //maxResolution: 156543.0339,
	                    //numZoomLevels: 19,
                		projection: new OpenLayers.Projection("EPSG:900913"),
                        displayProjection: new OpenLayers.Projection("EPSG:4326")
					} );

                    var layer = new OpenLayers.Layer.OSM();
                    map.addLayer(layer);

                    // Set marker
					var markers = new OpenLayers.Layer.Markers( "Markers" );
    				map.addLayer(markers);
                    var lonLat = new OpenLayers.LonLat('.$lon.','.$lat.')
                              .transform(
                                new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                                new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
                              );
                	markers.addMarker(new OpenLayers.Marker(lonLat));

                	// Set center and zoom
                    map.setCenter(lonLat, '.($conf->global->OPENSTREETMAP_MAPS_ZOOM_LEVEL?$conf->global->OPENSTREETMAP_MAPS_ZOOM_LEVEL:15).');
					//map.zoomToMaxExtent();

                    </script>';

				//print '<iframe width="600" height="500" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://cartosm.eu/map?lon='.$array[0]['lon'].'&lat='.$array[0]['lat'].'&zoom=13&width=600&height=500&mark=true&nav=true&pan=true&zb=bar&style=default&icon=down">';
				//print '</iframe>';
			} else {
				print $langs->trans('OpenStreetMapMapsAddressNotFound');
			}
		}
	}

	print '</div>';
	print '</div>';
} else {
	print '<br>'.$langs->trans("OpenStreetMapAddressNotDefined").'<br>';
}

dol_fiche_end();

llxfooter();

$db->close();
