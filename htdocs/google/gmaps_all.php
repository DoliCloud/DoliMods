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

// TODO Add on body ' onunload="GUnload()"'
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


// Fill array of contacts
$result_sql['adresse'] = 'Grand Place';
$result_sql['cp'] = '7000';
$result_sql['ville'] = 'Mons';

    
print '    
    <script type="text/javascript">//<![CDATA[
      function load()
        {
        if (GBrowserIsCompatible())
          {
          var map = new GMap2(document.getElementById("map"));
          // Coordonnees de l adresse provenant dans la base de données MySQL
          var adresse = '.$result_sql['adresse'] . ' ' . $result_sql['cp'] . ' ' . $result_sql['ville'].';
          // Recherche des coordonnées d un point dont on connait l adresse :
          var geocoder = new google.maps.ClientGeocoder();
          geocoder.getLatLng(adresse, function (coord)
            {
            // Et centrage de la map sur les coordonnées renvoyées par Google :
            map.setCenter(coord, 15);
            // Affichage du marker
            map.addOverlay(new GMarker(coord));
            });
          // ajout de la propriété d affichage des boutons "type de carte" (3 boutons par défaut)
          map.addControl(new GMapTypeControl());
          // ajout de la propriété ajout d un bouton "type de carte" (Relief)
          map.addMapType(G_PHYSICAL_MAP);
          // ajout de la propriété zoom à la carte "map"
          map.addControl(new GSmallMapControl);
          }
        }
    //]]></script>
';

if (count($result_sql))
{
	print '
	
	<br>
	<div align="center">
	<div id="map" class="divmap" style="width: 90%; height: 500px;" ></div>
	</div>
	<br>
	
	';
}
else
{
	print '<br>'.$langs->trans("GoogleAddressNotDefined").'<br>';
}

dol_fiche_end();

llxfooter();

$db->close();
?>