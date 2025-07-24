<?php
/* Copyright (C) 2011 Jonathan
 * Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       \file       htdocs/google/gmaps.php
 *       \ingroup    google
 *       \brief      Main google area page
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

$langs->loadLangs(array("companies", "google@google"));

// url is:  gmaps.php?mode=thirdparty|contact|member&id=id


$mode=GETPOST('mode', 'alpha');
$address='';

// Load third party
if (empty($mode) || $mode=='thirdparty' || $mode=='societe') {
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	$id = GETPOST('id', 'int');
	$object = new Societe($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
	$url = $object->url;
}
if ($mode=='contact') {
	include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
	$id = GETPOST('id', 'int');
	$object = new Contact($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
	$url = '';
}
if ($mode=='member') {
	include_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
	$id = GETPOST('id', 'int');
	$object = new Adherent($db);
	$object->id = $id;
	$object->fetch($id);
	$address = $object->getFullAddress(1, ', ');
	$url = '';
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
if (empty($mode) || $mode=='thirdparty' || $mode=='societe') {
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


if (function_exists('dol_banner_tab')) { // 3.9+
	dol_banner_tab($object, 'id', '', 1);
} else {
	print '<table class="border" width="100%">';

	// Name
	print '<tr><td class="titlefield">'.$langs->trans('ThirdPartyName').'</td>';
	print '<td colspan="3">';
	print $form->showrefnav($object, 'id', '', ($user->societe_id?0:1), 'rowid', 'nom', '', '&mode='.$mode);
	print '</td>';
	print '</tr>';


	// Status
	print '<tr><td>'.$langs->trans("Status").'</td>';
	print '<td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
	print $object->getLibStatut(2);
	print '</td>';
	print '</tr>';

	// Address
	print "<tr><td class=\"tdtop\">".$langs->trans('Address').'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
	dol_print_address($object->address, 'gmap', $mode, $object->id);
	print "</td></tr>";

	// Zip / Town
	print '<tr><td>'.$langs->trans('Zip').' / '.$langs->trans("Town").'</td><td colspan="'.(2+(($showlogo || $showbarcode)?0:1)).'">';
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
}


dol_fiche_end();

// Show maps
if ($address && $address != $object->country) {		// $address != $object->country to exclude address of country only
	// Detect if we use https
	//$sforhttps=(((empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') && (empty($_SERVER["SERVER_PORT"])||$_SERVER["SERVER_PORT"]!=443))?'':'s');

	// URL to include javascript map
	$urlforjsmap='https://maps.googleapis.com/maps/api/js';
	if (!getDolGlobalString('GOOGLE_API_SERVERKEY')) {
		$urlforjsmap.="?sensor=true";
	} else {
		$urlforjsmap.="?key=".getDolGlobalString('GOOGLE_API_SERVERKEY');
	}

	$urlforjsmap .= '&loading=async&callback=initMap';
	?>

<!--gmaps.php: Include Google javascript map -->
<script type="text/javascript" src="<?php echo $urlforjsmap; ?>" async defer></script>

<script type="text/javascript">
  var geocoder;
  var map;
  var marker;

  // GMaps v3 API
  function initialize() {
	var latlng = new google.maps.LatLng(0, 0);
	var myOptions = {
	  zoom: <?php echo (getDolGlobalInt('GOOGLE_GMAPS_ZOOM_LEVEL') >= 1 && getDolGlobalInt('GOOGLE_GMAPS_ZOOM_LEVEL') <= 10) ? getDolGlobalInt('GOOGLE_GMAPS_ZOOM_LEVEL') : 8; ?>,
	  center: latlng,
	  mapTypeId: google.maps.MapTypeId.ROADMAP,  // ROADMAP, SATELLITE, HYBRID, TERRAIN
	  fullscreenControl: true
	  /*zoomControl: true,
	  mapTypeControl: true,
	  scaleControl: true,
	  streetViewControl: true,
	  rotateControl: false */
	}
	map = new google.maps.Map(document.getElementById("map"), myOptions);
	geocoder = new google.maps.Geocoder();
	}

  function codeAddress() {
	var address = '<?php print dol_escape_js(dol_string_nospecial($address, ', ', array("\r\n","\n","\r"))); ?>';
	geocoder.geocode( { 'address': address}, function(results, status) {
	  if (status == google.maps.GeocoderStatus.OK) {
		map.setCenter(results[0].geometry.location);
		marker = new google.maps.Marker({
			map: map,
			position: results[0].geometry.location
		});

		var infowindow = new google.maps.InfoWindow({ content: '<div style="width:250px; height:80px;" class="divdolibarrgoogleaddress"><?php echo dol_escape_js($object->name); ?><br><?php echo dol_escape_js(dol_string_nospecial($address, '<br>', array("\r\n","\n","\r"))).(empty($url)?'':'<br><a href="'.$url.'">'.$url.'</a>'); ?></div>' });

		google.maps.event.addListener(marker, 'click', function() {
		  infowindow.open(map,marker);
		});


	  } else {
		  if (status == google.maps.GeocoderStatus.ZERO_RESULTS) alert('<?php echo dol_escape_js($langs->transnoentitiesnoconv("GoogleMapsAddressNotFound")); ?>');
		  else alert('Error '+status);
	  }
	});
  }

	/* function called when the js file for google maps api has been loaded */
  	function initMap() {
		initialize();
		codeAddress();
	}

</script>

<br>
<div align="center">
<div id="map" class="divmap" style="width: 90%; height: 500px;" ></div>
</div>
<br>

	<?php
} else {
	print '<br>'.$langs->trans("GoogleAddressNotDefined").'<br>';
}


llxfooter();

$db->close();
