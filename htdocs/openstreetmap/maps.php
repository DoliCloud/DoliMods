<?php
/* Copyright (C) 2011 Jonathan
 * Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       \file       htdocs/openstreetmap/gmaps.php
 *       \ingroup    openstreetmap
 *       \brief      Main openstreetmap area page
 *       \version    $Id: gmaps.php,v 1.13 2011/05/12 19:00:05 eldy Exp $
 *       \author     Laurent Destailleur
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");

$langs->load("openstreetmap@openstreetmap");

// url is:  gmaps.php?mode=thirdparty|contact|member&id=id


$mode=GETPOST('mode');
$address='';

// Load third party
if (empty($mode) || $mode=='thirdparty')
{
	include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
	$id = GETPOST('id','int');
	$object = new Societe($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1,', ');
}
if ($mode=='contact')
{
	include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
	$id = GETPOST('id','int');
	$object = new Contact($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1,', ');
}
if ($mode=='member')
{
	include_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');
	$id = GETPOST('id','int');
	$object = new Adherent($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1,', ');
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
if (empty($mode) || $mode=='thirdparty')
{
	$head = societe_prepare_head($object);
	$title=$langs->trans("ThirdParty");
	$picto='company';
}
if ($mode=='contact')
{
	$head = contact_prepare_head($object);
	$title=$langs->trans("ContactsAddresses");
	$picto='contact';
}
if ($mode=='member')
{
	$head = member_prepare_head($object);
	$title=$langs->trans("Member");
	$picto='user';
}

dol_fiche_head($head, 'gmaps', $title, 0, $picto);


print '<table class="border" width="100%">';

// Name
print '<tr><td width="20%">'.$langs->trans('ThirdPartyName').'</td>';
print '<td colspan="3">';
print $form->showrefnav($object,'id','',($user->societe_id?0:1),'rowid','nom','','&mode='.$mode);
print '</td>';
print '</tr>';


// Status
print '<tr><td>'.$langs->trans("Status").'</td>';
print '<td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
print $object->getLibStatut(2);
print '</td>';
print $htmllogobar; $htmllogobar='';
print '</tr>';

// Address
print "<tr><td valign=\"top\">".$langs->trans('Address').'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
dol_print_address($object->address,'gmap',$mode,$object->id);
print "</td></tr>";

// Zip / Town
print '<tr><td width="25%">'.$langs->trans('Zip').' / '.$langs->trans("Town").'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
print $object->zip.($object->zip && $object->town?" / ":"").$object->town;
print "</td>";
print '</tr>';

// Country
print '<tr><td>'.$langs->trans("Country").'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'" nowrap="nowrap">';
$img=picto_from_langcode($object->country_code);
if ($object->isInEEC()) print $form->textwithpicto(($img?$img.' ':'').$object->country,$langs->trans("CountryIsInEEC"),1,0);
else print ($img?$img.' ':'').$object->country;
print '</td></tr>';

// State
if (empty($conf->global->SOCIETE_DISABLE_STATE)) print '<tr><td>'.$langs->trans('State').'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">'.$object->state.'</td>';

print '</table>';


// Show maps

if ($address && $address != $object->country)
{
    print '<br><div align="center">';
    print '<div id="map" class="divmap" style="width: 90%; height: 500px; text-align: center; align: center">';

    $url='http://nominatim.openstreetmap.org/search?format=json&polygon=1&addressdetails=1&q='.urlencode($address);

    $ch = curl_init();
    //turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 0);

    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if (! empty($conf->global->MAIN_PROXY_USE))
    {
        curl_setopt ($ch, CURLOPT_PROXY, $conf->global->MAIN_PROXY_HOST. ":" . $conf->global->MAIN_PROXY_PORT);
        if (! empty($conf->global->MAIN_PROXY_USER)) curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $conf->global->MAIN_PROXY_USER. ":" . $conf->global->MAIN_PROXY_PASS);
    }

    if (preg_match('/^tcp/i',$url))
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    // Protocol HTTP or HTTPS
    if (preg_match('/^http/i',$url))
    {
        curl_setopt($ch, CURLOPT_URL,$url);

        list($usec, $sec) = explode(" ", microtime());
        $micro_start_time=((float)$usec + (float)$sec);

        $result = curl_exec($ch);

        list($usec, $sec) = explode(" ", microtime());
        $micro_end_time=((float)$usec + (float)$sec);
        $end_time=((float)$sec);

        $delay=($micro_end_time-$micro_start_time);

        if (curl_error($ch))    // Test with no response
        {
            print 'Error';
        }
        else
        {
            //print $result;
            $array = json_decode($result, true);
            $lat=$array[0]['lat'];
            $lon=$array[0]['lon'];
            if ($lat && $lon)
            {
                // See example on page http://wiki.openstreetmap.org/wiki/OpenLayers_Marker
                print '<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
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
            }
            else
            {
            	print $langs->trans('AddressNotFound');
            }
        }
    }

    print '</div>';
    print '</div>';
}
else
{
	print '<br>'.$langs->trans("OpenStreetMapAddressNotDefined").'<br>';
}

dol_fiche_end();

llxfooter();

$db->close();
?>