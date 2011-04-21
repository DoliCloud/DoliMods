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
 *       \version    $Id: gmaps.php,v 1.2 2011/04/21 19:09:50 eldy Exp $
 *       \author     Laurent Destailleur
 */

include("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");

// Load third party
$socid = $_GET['id'];
$objsoc = new Societe($db);
$objsoc->id = $socid;
$objsoc->fetch($socid);

$adresse = $objsoc->address . " " . $objsoc->adresse . " " . $objsoc->cp . " " . $objsoc->ville;


/*
 * View
 */

llxheader();

$content = "Default content";
$act = "";

//On fabrique les onglets
$head = societe_prepare_head($objsoc);
dol_fiche_head($head, 'gmaps', $langs->trans("ThirdParty"),0,'company');
//dol_fiche_head( $head, 8, "Tiers",0,'thirdparty' );
//On affiche le contenu
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>

<script type="text/javascript">
  var geocoder;
  var map;
  var marker;
  function initialize() {

    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map"), myOptions);
	geocoder = new google.maps.Geocoder();
	}

  function codeAddress() {
    var address = "<?php print($adresse); ?>";
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });


		var infowindow = new google.maps.InfoWindow({content: '<?php echo addslashes($objsoc->nom); ?><br /><?php echo addslashes($objsoc->adresse) . "<br />" . addslashes($objsoc->cp) . " " . addslashes($objsoc->ville); ?>'});

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
llxfooter();
?>