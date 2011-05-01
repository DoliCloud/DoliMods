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
 *       \version    $Id: gmaps.php,v 1.9 2011/05/01 19:31:24 eldy Exp $
 *       \author     Laurent Destailleur
 */

include("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/contact.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");

$mode=GETPOST('mode');
$adresse='';

// Load third party
if (empty($mode) || $mode=='thirdparty')
{
	include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
	$id = GETPOST('id');
	$obj = new Societe($db);
	$obj->id = $id;
	$obj->fetch($id);
	$adresse = $obj->getFullAddress(1,', ');
}
if ($mode=='contact')
{
	include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
	$id = GETPOST('id');
	$obj = new Contact($db);
	$obj->id = $id;
	$obj->fetch($id);
	$adresse = $obj->getFullAddress(1,', ');
}
if ($mode=='member')
{
	include_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');
	$id = GETPOST('id');
	$obj = new Adherent($db);
	$obj->id = $id;
	$obj->fetch($id);
	$adresse = $obj->getFullAddress(1,', ');
}


/*
 * View
 */

llxheader();

$content = "Default content";
$act = "";

//On fabrique les onglets
$head=array();
$title='';
$picto='';
if (empty($mode) || $mode=='thirdparty')
{
	$head = societe_prepare_head($obj);
	$title=$langs->trans("ThirdParty");
	$picto='company';
}
if ($mode=='contact')
{
	$head = contact_prepare_head($obj);
	$title=$langs->trans("Contact");
	$picto='contact';
}
if ($mode=='member')
{
	$head = member_prepare_head($obj);
	$title=$langs->trans("Member");
	$picto='user';
}
dol_fiche_head($head, 'gmaps', $title, 0, $picto);
//dol_fiche_head( $head, 8, "Tiers",0,'thirdparty' );
//On affiche le contenu

if ($adresse && $adresse != $obj->pays)
{
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>

<script type="text/javascript">
  var geocoder;
  var map;
  var marker;
  function initialize() {

    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: <?php echo ($conf->global->GOOGLE_GMAPS_ZOOM_LEVEL >= 1 && $conf->global->GOOGLE_GMAPS_ZOOM_LEVEL <= 10)?$conf->global->GOOGLE_GMAPS_ZOOM_LEVEL:8; ?>,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map"), myOptions);
	geocoder = new google.maps.Geocoder();
	}

  function codeAddress() {
    var address = '<?php print dol_escape_js(dol_string_nospecial($adresse,' ',array("\n","\r"))); ?>';
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });


		var infowindow = new google.maps.InfoWindow({content: '<?php echo addslashes($obj->nom); ?><br /><?php echo addslashes($obj->adresse) . "<br />" . addslashes($obj->cp) . " " . addslashes($obj->ville); ?>'});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});


      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }

  $(document).ready(function(){
		initialize();
		codeAddress();
	}
  );
</script>
<div id="map" style="width: 100%; height: 500px;" ></div>

<?php
}
else
{
	print $langs->trans("GoogleAddressNotDefined");
}

dol_fiche_end();

llxfooter();
?>