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
 *       \version    $Id: gmaps.php,v 1.13 2011/05/12 19:00:05 eldy Exp $
 *       \author     Laurent Destailleur
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");

$langs->load("google@google");

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
dol_print_address($object->address,'gmap','thirdparty',$object->id);
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
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>

<script type="text/javascript">
  var geocoder;
  var map;
  var marker;

  // GMaps v3 API
  function initialize() {
    var latlng = new google.maps.LatLng(0, 0);
    var myOptions = {
      zoom: <?php echo ($conf->global->GOOGLE_GMAPS_ZOOM_LEVEL >= 1 && $conf->global->GOOGLE_GMAPS_ZOOM_LEVEL <= 10)?$conf->global->GOOGLE_GMAPS_ZOOM_LEVEL:8; ?>,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP  // ROADMAP, SATELLITE, HYBRID, TERRAIN
    }
    map = new google.maps.Map(document.getElementById("map"), myOptions);
	geocoder = new google.maps.Geocoder();
	}

  function codeAddress() {
    var address = '<?php print dol_escape_js(dol_string_nospecial($address,' ',array("\n","\r"))); ?>';
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });


		var infowindow = new google.maps.InfoWindow({content: '<?php echo dol_escape_js($object->name); ?><br /><?php echo dol_escape_js(dol_string_nospecial($object->getFullAddress(1,', '),' ',array("\n","\r"))); ?>'});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});


      } else {
          if (status == google.maps.GeocoderStatus.ZERO_RESULTS) alert('<?php echo dol_escape_js($langs->transnoentitiesnoconv("GoogleMapsAddressNotFound")); ?>');
          else alert('Error '+status);
      }
    });
  }

  $(document).ready(function(){
		initialize();
		codeAddress();
	}
  );
</script>

<br>
<div align="center">
<div id="map" class="divmap" style="width: 90%; height: 500px;" ></div>
</div>
<br>

<?php
}
else
{
	print '<br>'.$langs->trans("GoogleAddressNotDefined").'<br>';
}

dol_fiche_end();

llxfooter();

$db->close();
?>