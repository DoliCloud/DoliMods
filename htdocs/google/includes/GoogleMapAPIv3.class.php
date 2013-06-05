<?php
/*
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 *
 *  @author            CERDAN Yohann <cerdanyohann@yahoo.fr>
 *  @copyright      (c) 20101  CERDAN Yohann, All rights reserved
 *  @ version         11/02/2011
 */

class GoogleMapAPI
{
	/** GoogleMap ID for the HTML DIV  **/
	protected $googleMapId = 'googlemapapi';
	/** GoogleMap  Direction ID for the HTML DIV **/
	protected $googleMapDirectionId = 'route';
	/** Width of the gmap **/
	protected $width = '';
	/** Height of the gmap **/
	protected $height = '';
	/** Icon width of the gmarker **/
	protected $iconWidth = 57;
	/** Icon height of the gmarker **/
	protected $iconHeight = 34;
	/** Infowindow width of the gmarker **/
	protected $infoWindowWidth = 250;
	/** Default zoom of the gmap **/
	protected $zoom = 9;
	/** Enable the zoom of the Infowindow **/
	protected $enableWindowZoom = false;
	/** Default zoom of the Infowindow **/
	protected $infoWindowZoom = 3;
	/** Lang of the gmap **/
	protected $lang = 'fr';
	/**Center of the gmap **/
	protected $center = 'Paris France';
	/** Content of the HTML generated **/
	protected $content = '';
	/** Add the direction button to the infowindow **/
	protected $displayDirectionFields = false;
	/** Hide the marker by default **/
	protected $defaultHideMarker = false;
	/** Extra content (marker, etc...) **/
	protected $contentMarker = '';
	/** Use clusterer to display a lot of markers on the gmap **/
	protected $useClusterer = false;
	protected $gridSize = 100;
	protected $maxZoom = 9;
	protected $clustererLibrarypath = 'http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer_packed.js';
	/** Enable automatic center/zoom **/
	protected $enableAutomaticCenterZoom = false;
	/** maximum longitude of all markers **/
	protected $maxLng = -1000000;
	/** minimum longitude of all markers **/
	protected $minLng = 1000000;
	/** max latitude of all markers **/
	protected $maxLat = -1000000;
	/** min latitude of all markers **/
	protected $minLat = 1000000;
	/** map center latitude (horizontal), calculated automatically as markers are added to the map **/
	protected $centerLat = null;
	/** map center longitude (vertical),  calculated automatically as markers are added to the map **/
	protected $centerLng = null;
	/** factor by which to fudge the boundaries so that when we zoom encompass, the markers aren't too close to the edge **/
	protected $coordCoef = 0.01;
	/** Type of map to display **/
	protected $mapType = 'ROADMAP';	
	protected $langs;
	
	/**
	 * Class constructor
	 *
	 * @return void
	 */

	public function __construct()
	{
	
	}

	/**
	 * Set the useClusterer parameter (optimization to display a lot of marker)
	 *
	 * @param boolean $useClusterer use cluster or not
	 * @param int $gridSize grid size (The grid size of a cluster in pixel. Each cluster will be a square. If you want the algorithm to run faster, you can set this value larger. The default value is 100.)
	 * @param int $maxZoom maxZoom (The max zoom level monitored by a marker cluster. If not given, the marker cluster assumes the maximum map zoom level. When maxZoom is reached or exceeded all markers will be shown without cluster.)
	 * @param int $clustererLibraryPath clustererLibraryPath
	 *
	 * @return void
	 */

	public function setClusterer($useClusterer, $gridSize = 100, $maxZoom = 9, $clustererLibraryPath = '')
	{
		$this->useClusterer = $useClusterer;
		$this->gridSize = $gridSize;
		$this->maxZoom = $maxZoom;
		($clustererLibraryPath == '') ? $this->clustererLibraryPath = 'http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer_packed.js' : $clustererLibraryPath;
	}

	/**
	 * Set the type of map, can be :
	 * HYBRID, TERRAIN, ROADMAP, SATELLITE
	 *
	 * @param string $type
	 * @return void
	 */
	public function setMapType($type)
	{
		$mapsType = array('ROADMAP', 'HYBRID', 'TERRAIN', 'SATELLITE');
		if (!in_array(strtoupper($type), $mapsType)) {
			$this->mapType = $mapsType[0];
		} else {
			$this->mapType = strtoupper($type);
		}
	}


	/**
	 * Set the ID of the default gmap DIV
	 *
	 * @param string $googleMapId the google div ID
	 *
	 * @return void
	 */

	public function setDivId($googleMapId)
	{
		$this->googleMapId = $googleMapId;
	}

	/**
	 * Set the ID of the default gmap direction DIV
	 *
	 * @param string $googleMapDirectionId GoogleMap  Direction ID for the HTML DIV
	 *
	 * @return void
	 */

	public function setDirectionDivId($googleMapDirectionId)
	{
		$this->googleMapDirectionId = $googleMapDirectionId;
	}

	/**
	 * Set the size of the gmap
	 *
	 * @param int $width GoogleMap  width
	 * @param int $height GoogleMap  height
	 *
	 * @return void
	 */

	public function setSize($width, $height)
	{
		$this->width = $width;
		$this->height = $height;
	}

	/**
	 * Set the with of the gmap infowindow (on marker clik)
	 *
	 * @param int $infoWindowWidth GoogleMap  info window width
	 *
	 * @return void
	 */

	public function setInfoWindowWidth($infoWindowWidth)
	{
		$this->infoWindowWidth = $infoWindowWidth;
	}

	/**
	 * Set the size of the icon markers
	 *
	 * @param int $iconWidth GoogleMap  marker icon width
	 * @param int $iconHeight GoogleMap  marker icon height
	 *
	 * @return void
	 */

	public function setIconSize($iconWidth, $iconHeight)
	{
		$this->iconWidth = $iconWidth;
		$this->iconHeight = $iconHeight;
	}

	/**
	 * Set the lang of the gmap
	 *
	 * @param string $lang GoogleMap  lang : fr,en,..
	 *
	 * @return void
	 */

	public function setLang($lang)
	{
		$this->lang = $lang;
	}

	/**
	 * Set the zoom of the gmap
	 *
	 * @param int $zoom GoogleMap  zoom.
	 *
	 * @return void
	 */

	public function setZoom($zoom)
	{
		$this->zoom = $zoom;
	}

	/**
	 * Set the zoom of the infowindow
	 *
	 * @param int $zoom GoogleMap  zoom.
	 *
	 * @return void
	 */

	public function setInfoWindowZoom($infoWindowZoom)
	{
		$this->infoWindowZoom = $infoWindowZoom;
	}

	/**
	 * Enable the zoom on the marker when you click on it
	 *
	 * @param int $zoom GoogleMap  zoom.
	 *
	 * @return void
	 */

	public function setEnableWindowZoom($enableWindowZoom)
	{
		$this->enableWindowZoom = $enableWindowZoom;
	}

	/**
	 * Enable theautomatic center/zoom at the gmap load
	 *
	 * @param int $zoom GoogleMap  zoom.
	 *
	 * @return void
	 */

	public function setEnableAutomaticCenterZoom($enableAutomaticCenterZoom)
	{
		$this->enableAutomaticCenterZoom = $enableAutomaticCenterZoom;
	}

	/**
	 * Set the center of the gmap (an address)
	 *
	 * @param string $center GoogleMap  center (an address)
	 *
	 * @return void
	 */

	public function setCenter($center)
	{
		$this->center = $center;
	}

	/**
	 * Set the center of the gmap
	 *
	 * @param boolean $displayDirectionFields display directions or not in the info window
	 *
	 * @return void
	 */

	public function setDisplayDirectionFields($displayDirectionFields)
	{
		$this->displayDirectionFields = $displayDirectionFields;
	}

	/**
	 * Set the defaultHideMarker
	 *
	 * @param boolean $defaultHideMarker hide all the markers on the map by default
	 *
	 * @return void
	 */

	public function setDefaultHideMarker($defaultHideMarker)
	{
		$this->defaultHideMarker = $defaultHideMarker;
	}

	/**
	 * Get the google map content
	 *
	 * @return string the google map html code
	 */

	public function getGoogleMap()
	{
		return $this->content;
	}

	/**
	 * Get URL content using cURL.
	 *
	 * @param string $url the url
	 *
	 * @return string the html code
	 *
	 * @todo add proxy settings
	 */

	public function getContent($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_URL, $url);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}

	/**
	 * Remove accentued characters
	 *
	 * @param string $chaine		The string to treat
	 * @param string $remplace_par	The replacement character
	 * @return string
	 */
	public function withoutSpecialChars($str, $replaceBy = '_')
	{ 
		$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');		
		$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
		$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères		
		return $str;
		/*
		$accents = "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ";
		$sansAccents = "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy";
		$text = strtr($text, $accents, $sansAccents);
		$text = preg_replace('/([^.a-z0-9]+)/i', $replaceBy, $text);
		return $text;*/
	}


	/**
	 * Geocoding an address (address -> lat,lng)
	 *
	 * @param string $address an address
	 *
	 * @return array array with precision, lat & lng
	 */

	public function geocoding($address)
	{
		$encodeAddress = urlencode($this->withoutSpecialChars($address));
		//$url = "http://maps.google.com/maps/geo?q=".$encodeAddress."&output=csv";
		$url = "http://maps.google.com/maps/api/geocode/json?address=".$encodeAddress."&sensor=false";
		ini_set("allow_url_open", "1");
		$data = file_get_contents($url);
		
		$data = json_decode($data);

		if ($data->status == "OK") {
			$return[0] = 0; // plus utilisé
			$return[1] = 0; // plus utilisé
			$return[2] = $data->results[0]->geometry->location->lat;
			$return[3] = $data->results[0]->geometry->location->lng;
			
			// TODO Update table
			
			
		} else {
			echo "<!-- geocoding : failure to geocode : " . $status . " -->\n";
			$return = null; // failure to geocode
		}

		return $return;
	}

	/**
	 * Add marker by his coord
	 *
	 * @param string $lat lat
	 * @param string $lng lngs
	 * @param string $html html code display in the info window
	 * @param string $category marker category
	 * @param string $icon an icon url
	 *
	 * @return void
	 */

	public function addMarkerByCoords($lat, $lng, $title, $html = '', $category = '', $icon = '')
	{
		if ($icon == '') {
			$icon = 'http://maps.gstatic.com/intl/fr_ALL/mapfiles/markers/marker_sprite.png';
		}

		// Save the lat/lon to enable the automatic center/zoom
		$this->maxLng = (float)max((float)$lng, $this->maxLng);
		$this->minLng = (float)min((float)$lng, $this->minLng);
		$this->maxLat = (float)max((float)$lat, $this->maxLat);
		$this->minLat = (float)min((float)$lat, $this->minLat);
		$this->centerLng = (float)($this->minLng + $this->maxLng) / 2;
		$this->centerLat = (float)($this->minLat + $this->maxLat) / 2;

		$this->contentMarker .= "\t" . 'addMarker(new google.maps.LatLng("' . $lat . '","' . $lng . '"),"' . $title . '","' . $html . '","' . $category . '","' . $icon . '");' . "\n";
	}

	/**
	 * Add marker by his address
	 *
	 * @param string $address an ddress
	 * @param string $content html code display in the info window
	 * @param string $category marker category
	 * @param string $icon an icon url
	 *
	 * @return void
	 */

	public function addMarkerByAddress($address, $title = '', $content = '', $category = '', $icon = '',$idSoc = '')
	{
		$point = $this->geocoding($address);
		if ($point !== null) {
			$this->addMarkerByCoords($point[2], $point[3], $title, $content, $category, $icon);
		} else {
			echo '<span style="font-size:9px">'.$this->langs->trans("CouldNotFindLatitudeLongitudeFor").' <a href=../societe/soc.php?socid='.$idSoc.'>'.$title.'</a></span><br/>';
		}
	}

	/**
	 * Add marker by an array 
	 *
	 * @param string $tabAdresses an array of address
	 * @param string $langs marker category
	 * @param string $type an icon url
	 *
	 * @return void
	 */

	public function addArrayMarker($tabAdresses, $langs, $mode)
	{
		$this->langs = $langs;
		foreach ($tabAdresses as $adresse) {
			if($adresse->client == 1)
				$icon = "http://www.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
			else
				$icon = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
			$addPropre = $this->no_special_character_v2($adresse->address);
			$lienGmaps = ' <a href=\"http://maps.google.fr/maps?q='.urlencode($this->withoutSpecialChars($adresse->address)).'\" style=\"font-weight:normal\" class=button >Google Maps</a>';
			$html = '<a href=../societe/soc.php?socid='.$adresse->id.'><b>'.$adresse->nom.'</b><br/>'.$addPropre.'<br/><i>'.$adresse->tel.'</i></a>';
			$html.= " <br/> ".$lienGmaps. "<br/><br/>";
			if(isset($adresse->latitude) && isset($adresse->longitude)) {
				$this->addMarkerByCoords($adresse->latitude, $adresse->longitude, $adresse->name, $html, '', $icon);
			}
			else if (isset($adresse->address)) {
				//$this->addMarkerByAddress($adresse->address, $adresse->name, $html, '', $icon, $adresse->id);
			}
		}
	}
	
	function no_special_character_v2($chaine){ 
		//  les accents
		$chaine=trim($chaine);
		$chaine= strtr($chaine,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
		$chaine = preg_replace('/([^. a-z0-9]+)/i', '-', $chaine);
	 
		return $chaine;
	 
	}

	/**
	 * Initialize the javascript code
	 *
	 * @return void
	 */

	public function init()
	{
		// Google map DIV
		if (($this->width != '') && ($this->height != '')) {
			$this->content .= "\t" . '<div id="' . $this->googleMapId . '" style="width:' . $this->width . ';height:' . $this->height . '"></div>' . "\n";
		} else {
			$this->content .= "\t" . '<div id="' . $this->googleMapId . '"></div>' . "\n";
		}


		// Google map JS
		$this->content .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=' . $this->lang . '">';
		$this->content .= '</script>' . "\n";

		// Clusterer JS
		if ($this->useClusterer == true) {
			$this->content .= '<script src="' . $this->clustererLibraryPath . '" type="text/javascript"></script>' . "\n";
		}

		$this->content .= '<script type="text/javascript">' . "\n";

		//global variables
		$this->content .= 'var geocoder = new google.maps.Geocoder();' . "\n";
		$this->content .= 'var map;' . "\n";
		$this->content .= 'var gmarkers = [];' . "\n";
		$this->content .= 'var infowindow;' . "\n";
		$this->content .= 'var directions = new google.maps.DirectionsRenderer();' . "\n";
		$this->content .= 'var directionsService = new google.maps.DirectionsService();' . "\n";
		$this->content .= 'var current_lat = 0;' . "\n";
		$this->content .= 'var current_lng = 0;' . "\n";

		// JS public function to get current Lat & Lng
		$this->content .= "\t" . 'function getCurrentLat() {' . "\n";
		$this->content .= "\t\t" . 'return current_lat;' . "\n";
		$this->content .= "\t" . '}' . "\n";
		$this->content .= "\t" . 'function getCurrentLng() {' . "\n";
		$this->content .= "\t\t" . 'return current_lng;' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to add a  marker 
		$this->content .= "\t" . 'function addMarker(latlng,title,content,category,icon) {' . "\n";
		$this->content .= "\t\t" . 'var marker = new google.maps.Marker({' . "\n";
		$this->content .= "\t\t\t" . 'map: map,' . "\n";
		$this->content .= "\t\t\t" . 'title : title,' . "\n";
		$this->content .= "\t\t\t" . 'icon:  new google.maps.MarkerImage(icon, new google.maps.Size(' . $this->iconWidth . ',' . $this->iconHeight . ')),' . "\n";
		$this->content .= "\t\t\t" . 'position: latlng' . "\n";
		$this->content .= "\t\t" . '});' . "\n";

		// Display direction inputs in the info window
		if ($this->displayDirectionFields == true) {
			$this->content .= "\t\t" . 'content += \'<div style="clear:both;height:30px;"></div>\';' . "\n";
			$this->content .= "\t\t" . 'id_name = \'marker_\'+gmarkers.length;' . "\n";
			/*$this->content .= "\t\t" . 'content += \'<input type="text" id="\'+id_name+\'"/>\';' . "\n";
			$this->content .= "\t\t" . 'var from = ""+latlng.lat()+","+latlng.lng();' . "\n";
			*//*$this->content .= "\t\t" . 'content += \'<br /><input type="button" class="butAction" onClick="" value="Voir la fiche tiers"/>\';' . "\n";*/
		}

		$this->content .= "\t\t" . 'var html = \'<div style="float:left;text-align:left;width:' . $this->infoWindowWidth . ';">\'+content+\'</div>\'' . "\n";
		$this->content .= "\t\t" . 'google.maps.event.addListener(marker, "click", function() {' . "\n";
		$this->content .= "\t\t\t" . 'if (infowindow) infowindow.close();' . "\n";
		$this->content .= "\t\t\t" . 'infowindow = new google.maps.InfoWindow({content: html});' . "\n";
		$this->content .= "\t\t\t" . 'infowindow.open(map,marker);' . "\n";

		// Enable the zoom when you click on a marker
		if ($this->enableWindowZoom == true) {
			$this->content .= "\t\t" . 'map.setCenter(new google.maps.LatLng(latlng.lat(),latlng.lng()),' . $this->infoWindowZoom . ');' . "\n";
		}

		$this->content .= "\t\t" . '});' . "\n";
		$this->content .= "\t\t" . 'marker.mycategory = category;' . "\n";
		$this->content .= "\t\t" . 'gmarkers.push(marker);' . "\n";

		// Hide marker by default
		if ($this->defaultHideMarker == true) {
			$this->content .= "\t\t" . 'marker.setVisible(false);' . "\n";
		}
		$this->content .= "\t" . '}' . "\n";

		// JS public function to add a geocode marker 
		$this->content .= "\t" . 'function geocodeMarker(address,title,content,category,icon) {' . "\n";
		$this->content .= "\t\t" . 'if (geocoder) {' . "\n";
		$this->content .= "\t\t\t" . 'geocoder.geocode( { "address" : address}, function(results, status) {' . "\n";
		$this->content .= "\t\t\t\t" . 'if (status == google.maps.GeocoderStatus.OK) {' . "\n";
		$this->content .= "\t\t\t\t\t" . 'var latlng = 	results[0].geometry.location;' . "\n";
		$this->content .= "\t\t\t\t\t" . 'addMarker(results[0].geometry.location,title,content,category,icon)' . "\n";
		$this->content .= "\t\t\t\t" . '}' . "\n";
		$this->content .= "\t\t\t" . '});' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to center the gmaps dynamically
		$this->content .= "\t" . 'function geocodeCenter(address) {' . "\n";
		$this->content .= "\t\t" . 'if (geocoder) {' . "\n";
		$this->content .= "\t\t\t" . 'geocoder.geocode( { "address": address}, function(results, status) {' . "\n";
		$this->content .= "\t\t\t\t" . 'if (status == google.maps.GeocoderStatus.OK) {' . "\n";
		$this->content .= "\t\t\t\t" . 'map.setCenter(results[0].geometry.location);' . "\n";
		$this->content .= "\t\t\t\t" . '} else {' . "\n";
		$this->content .= "\t\t\t\t" . 'alert("Geocode was not successful for the following reason: " + status);' . "\n";
		$this->content .= "\t\t\t\t" . '}' . "\n";
		$this->content .= "\t\t\t" . '});' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to set direction
		$this->content .= "\t" . 'function addDirection(from,to) {' . "\n";
		$this->content .= "\t\t" . 'var request = {' . "\n";
		$this->content .= "\t\t" . 'origin:from, ' . "\n";
		$this->content .= "\t\t" . 'destination:to,' . "\n";
		$this->content .= "\t\t" . 'travelMode: google.maps.DirectionsTravelMode.DRIVING' . "\n";
		$this->content .= "\t\t" . '};' . "\n";
		$this->content .= "\t\t" . 'directionsService.route(request, function(response, status) {' . "\n";
		$this->content .= "\t\t" . 'if (status == google.maps.DirectionsStatus.OK) {' . "\n";
		$this->content .= "\t\t" . 'directions.setDirections(response);' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t\t" . '});' . "\n";

		$this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to show a category of marker
		$this->content .= "\t" . 'function showCategory(category) {' . "\n";
		$this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
		$this->content .= "\t\t\t" . 'if (gmarkers[i].mycategory == category) {' . "\n";
		$this->content .= "\t\t\t\t" . 'gmarkers[i].setVisible(true);' . "\n";
		$this->content .= "\t\t\t" . '}' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to hide a category of marker
		$this->content .= "\t" . 'function hideCategory(category) {' . "\n";
		$this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
		$this->content .= "\t\t\t" . 'if (gmarkers[i].mycategory == category) {' . "\n";
		$this->content .= "\t\t\t\t" . 'gmarkers[i].setVisible(false);' . "\n";
		$this->content .= "\t\t\t" . '}' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to hide all the markers
		$this->content .= "\t" . 'function hideAll() {' . "\n";
		$this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
		$this->content .= "\t\t\t" . 'gmarkers[i].setVisible(false);' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to show all the markers
		$this->content .= "\t" . 'function showAll() {' . "\n";
		$this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
		$this->content .= "\t\t\t" . 'gmarkers[i].setVisible(true);' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function to hide/show a category of marker - TODO BUG
		$this->content .= "\t" . 'function toggleHideShow(category) {' . "\n";
		$this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
		$this->content .= "\t\t\t" . 'if (gmarkers[i].mycategory === category) {' . "\n";
		$this->content .= "\t\t\t\t" . 'if (gmarkers[i].getVisible()===true) { gmarkers[i].setVisible(false); }' . "\n";
		$this->content .= "\t\t\t\t" . 'else gmarkers[i].setVisible(true);' . "\n";
		$this->content .= "\t\t\t" . '}' . "\n";
		$this->content .= "\t\t" . '}' . "\n";
		$this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
		$this->content .= "\t" . '}' . "\n";

		// JS public function add a KML
		$this->content .= "\t" . 'function addKML(file) {' . "\n";
		$this->content .= "\t\t" . 'var ctaLayer = new google.maps.KmlLayer(file);' . "\n";
		$this->content .= "\t\t" . 'ctaLayer.setMap(map);' . "\n";
		$this->content .= "\t" . '}' . "\n";
	}

	public function generate()
	{
		$this->init();

		//Fonction init()
		$this->content .= "\t" . 'function initialize() {' . "\n";
		$this->content .= "\t" . 'var myLatlng = new google.maps.LatLng(48.8792,2.34778);' . "\n";
		$this->content .= "\t" . 'var myOptions = {' . "\n";
		$this->content .= "\t\t" . 'zoom: ' . $this->zoom . ',' . "\n";
		$this->content .= "\t\t" . 'center: myLatlng,' . "\n";
		$this->content .= "\t\t" . 'mapTypeId: google.maps.MapTypeId.' . $this->mapType . "\n";
		$this->content .= "\t" . '}' . "\n";

		//Goole map Div Id
		$this->content .= "\t" . 'map = new google.maps.Map(document.getElementById("' . $this->googleMapId . '"), myOptions);' . "\n";

		// Center
		if ($this->enableAutomaticCenterZoom == true) {
			$lenLng = $this->maxLng - $this->minLng;
			$lenLat = $this->maxLat - $this->minLat;
			$this->minLng -= $lenLng * $this->coordCoef;
			$this->maxLng += $lenLng * $this->coordCoef;
			$this->minLat -= $lenLat * $this->coordCoef;
			$this->maxLat += $lenLat * $this->coordCoef;

			$minLat = number_format(floatval($this->minLat), 12, '.', '');
			$minLng = number_format(floatval($this->minLng), 12, '.', '');
			$maxLat = number_format(floatval($this->maxLat), 12, '.', '');
			$maxLng = number_format(floatval($this->maxLng), 12, '.', '');
			$this->content .= "\t\t\t" . 'var bds = new google.maps.LatLngBounds(new google.maps.LatLng(' . $minLat . ',' . $minLng . '),new google.maps.LatLng(' . $maxLat . ',' . $maxLng . '));' . "\n";
			$this->content .= "\t\t\t" . 'map.fitBounds(bds);' . "\n";
		} else {
			$this->content .= "\t" . 'geocodeCenter("' . $this->center . '");' . "\n";
		}

		$this->content .= "\t" . 'google.maps.event.addListener(map,"click",function(event) { if (event) { current_lat=event.latLng.lat();current_lng=event.latLng.lng(); }}) ;' . "\n";

		$this->content .= "\t" . 'directions.setMap(map);' . "\n";
		$this->content .= "\t" . 'directions.setPanel(document.getElementById("' . $this->googleMapDirectionId . '"))' . "\n";

		// add all the markers
		$this->content .= $this->contentMarker;

		// Clusterer JS
		if ($this->useClusterer == true) {
			$this->content .= "\t" . 'var markerCluster = new MarkerClusterer(map, gmarkers,{gridSize: ' . $this->gridSize . ', maxZoom: ' . $this->maxZoom . '});' . "\n";
		}

		$this->content .= '}' . "\n";

		// Chargement de la map a la fin du HTML
		$this->content .= "\t" . 'window.onload=initialize;' . "\n";

		//Fermeture du javascript
		$this->content .= '</script>' . "\n";

	}

}        
