<?php
/*
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 *
 * @author         CERDAN Yohann <cerdanyohann@yahoo.fr>
 * @copyright      (c) 2011  CERDAN Yohann, All rights reserved
 * @copyright      (c) 2013  Laurent Destailleur
 * @version        2013-07-23
 */


require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

class GoogleMapAPI
{
	protected $version = 3.7;
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
	protected $lang = 'en';
	/**Center of the gmap **/
	protected $center = '';
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
	protected $clustererLibrarypath = 'todefine';
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
	 * @return void
	 */
	public function setClusterer($useClusterer, $gridSize = 100, $maxZoom = 9, $clustererLibraryPath = '')
	{
		$this->useClusterer = $useClusterer;
		$this->gridSize = $gridSize;
		$this->maxZoom = $maxZoom;
		($clustererLibraryPath == '') ? $this->clustererLibraryPath = dol_buildpath('/google/includes/markerclusterer.js', 2) : $clustererLibraryPath;
		//print 'clustererlib = '.$this->clustererLibraryPath;
	}

	/**
	 * Set the type of map, can be :
	 * HYBRID, TERRAIN, ROADMAP, SATELLITE
	 *
	 * @param  string  $type   Type
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
	 * @return void
	 */
	public function setZoom($zoom)
	{
		$this->zoom = $zoom;
	}

	/**
	 * Set the zoom of the infowindow
	 *
	 * @param int $infoWindowZoom  GoogleMap  zoom.
	 * @return void
	 */
	public function setInfoWindowZoom($infoWindowZoom)
	{
		$this->infoWindowZoom = $infoWindowZoom;
	}

	/**
	 * Enable the zoom on the marker when you click on it
	 *
	 * @param int $enableWindowZoom    GoogleMap  zoom.
	 * @return void
	 */
	public function setEnableWindowZoom($enableWindowZoom)
	{
		$this->enableWindowZoom = $enableWindowZoom;
	}

	/**
	 * Enable theautomatic center/zoom at the gmap load
	 *
	 * @param int $enableAutomaticCenterZoom   GoogleMap  zoom.
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
	 * @return string the html code
	 *
	 * @todo add proxy settings
	 */
	public function getContent($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}

	/**
	 * Remove accentued characters
	 *
	 * @param string $str		   The string to treat
	 * @param string $replaceBy    The replacement character
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
	 * @param  string  $address        An address
	 * @return array array with precision, lat & lng
	 */
	public function geocoding($address)
	{
		global $conf;

		$encodeAddress = urlencode($this->withoutSpecialChars($address));
		// URL to geoencode
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$encodeAddress;
		if (! empty($conf->global->GOOGLE_API_SERVERKEY)) $url.="&key=".$conf->global->GOOGLE_API_SERVERKEY;

		ini_set("allow_url_open", "1");
		$data = file_get_contents($url);

		$data = json_decode($data);

		if ($data->status == "OK") {
			$return[0] = 0; // plus utilisé
			$return[1] = 0; // plus utilisé
			$return[2] = $data->results[0]->geometry->location->lat;
			$return[3] = $data->results[0]->geometry->location->lng;
		} else {
			echo "<!-- geocoding : failure to geocode : " . $status . " -->\n";
			$return = null; // failure to geocode
		}

		return $return;
	}

	/**
	 * Add marker by his coord
	 *
	 * @param string $lat 			lat
	 * @param string $lng 			lngs
	 * @param string $title         Title
	 * @param string $html 			html code display in the info window
	 * @param string $category 		marker category
	 * @param string $icon 			an icon url
	 * @return void
	 */
	public function addMarkerByCoords($lat, $lng, $title, $html = '', $category = '', $icon = '')
	{
		if (empty($icon)) {
			// Detect if we use https
			$sforhttps=(((empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') && (empty($_SERVER["SERVER_PORT"])||$_SERVER["SERVER_PORT"]!=443))?'':'s');
		}

		// Save the lat/lon to enable the automatic center/zoom
		$this->maxLng = (float) max((float) $lng, $this->maxLng);
		$this->minLng = (float) min((float) $lng, $this->minLng);
		$this->maxLat = (float) max((float) $lat, $this->maxLat);
		$this->minLat = (float) min((float) $lat, $this->minLat);
		$this->centerLng = (float) ($this->minLng + $this->maxLng) / 2;
		$this->centerLat = (float) ($this->minLat + $this->maxLat) / 2;

		$this->contentMarker .= "\t" . 'addMarker(new google.maps.LatLng("' . $lat . '","' . $lng . '"),"' . $this->g_dol_escape_js($title, 2) . '","' . $this->g_dol_escape_js($html, 2) . '","' . $this->g_dol_escape_js($category, 2) . '","' . $icon . '");' . "\n";
	}

	/**
	 *  Returns text escaped for inclusion into javascript code
	 *
	 *  @param      string		$stringtoescape		String to escape
	 *  @param		string		$mode				0=Escape also ' and " into ', 1=Escape ' but not " for usage into 'string', 2=Escape " but not ' for usage into "string"
	 *  @return     string     		 				Escaped string. Both ' and " are escaped into ' if they are escaped.
	 */
	private function g_dol_escape_js($stringtoescape, $mode = 0)
	{
		// escape quotes and backslashes, newlines, etc.
		$substitjs=array("&#039;"=>"\\'",'\\'=>'\\\\',"\r"=>'\\r',"\n"=>'\\n');
		//$substitjs['</']='<\/';	// We removed this. Should be useless.
		if (empty($mode)) { $substitjs["'"]="\\'"; $substitjs['"']="\\'"; } elseif ($mode == 1) $substitjs["'"]="\\'";
		elseif ($mode == 2) { $substitjs['"']='\\"'; }
		return strtr($stringtoescape, $substitjs);
	}

	/**
	 * Add marker by his address
	 *
	 * @param  string  $address    An ddress
	 * @param  string  $title      Title
	 * @param  string  $content    html code display in the info window
	 * @param  string  $category   marker category
	 * @param  string  $icon       an icon url
	 * @param  string  $idSoc      Id of thirdparty
	 * @return void
	 */
	public function addMarkerByAddress($address, $title = '', $content = '', $category = '', $icon = '', $idSoc = '')
	{
		$point = $this->geocoding($address);
		if ($point !== null) {
			$this->addMarkerByCoords($point[2], $point[3], $title, $content, $category, $icon);
		} else {
			$pagename=(((float) DOL_VERSION >= 6.0)?'/societe/card.php':'/societe/soc.php');
			echo '<span style="font-size:9px">'.$this->langs->trans("CouldNotFindLatitudeLongitudeFor").' <a href="'.DOL_URL_ROOT.$pagename.'?socid='.$idSoc.'">'.$title.'</a></span><br/>';
		}
	}

	/**
	 * Add marker by an array
	 *
	 * @param string $tabAddresses 	An array of address
	 * @param string $langs 		Language
	 * @param string $mode 			Mode
	 * @return void
	 */
	public function addArrayMarker($tabAddresses, $langs, $mode)
	{
		global $conf;

		$this->langs = $langs;

		// Detect if we use https
		$sforhttps=(((empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') && (empty($_SERVER["SERVER_PORT"])||$_SERVER["SERVER_PORT"]!=443))?'':'s');

		$i=0;
		foreach ($tabAddresses as $elem) {
			$i++;
			//if ($i != 9) continue;	// Output only eleme i = 9

			$icon='';
			/*if($elem->client == 1) $icon = "http://www.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
			else $icon = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
			if ($sforhttps) $icon=preg_replace('/^http:/','https:',$icon);
			*/

			// ajout de la modification des icones en fonction du tiers et de son status
			if (! empty($conf->global->GOOGLE_ENABLE_GMAPS_TICON)) {
				if (empty($mode) || $mode == 'company' || $mode == 'thirdparty') {
					switch ($elem->client) {
						case 0:
							$icon = DOL_URL_ROOT.'/custom/google/images/red-dot.png';
							break;
						case 1:
							$icon = DOL_URL_ROOT.'/custom/google/images/blue-dot.png';	// customer
							break;
						case 2:
							if (! empty($conf->global->GOOGLE_CAN_USE_PROSPECT_ICONS)) {
								switch ($elem->statusprospet) {
									case -1:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm-1.png';
										break;
									case 0:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm0.png';
										break;
									case 1:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm1.png';
										break;
									case 2:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm2.png';
										break;
									case 3:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm3.png';
										break;
									case 4:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm4.png';
										break;
									default:
										$icon = DOL_URL_ROOT.'/custom/google/images/stcomm0.png';
										break;
								}
							} else {
								$icon = DOL_URL_ROOT.'/custom/google/images/blue-dot.png';
							}
							break;
						case 3:
							$icon = DOL_URL_ROOT.'/custom/google/images/green-dot.png';
							break;
						default:
							$icon = DOL_URL_ROOT.'/custom/google/images/red-dot.png';
							break;
					}
				} else $icon = DOL_URL_ROOT.'/custom/google/images/red-dot.png';
				if ($sforhttps) $icon=preg_replace('/^http:/', 'https:', $icon);
			}

			$address=dol_string_nospecial($elem->address, ', ', array("\r\n","\n","\r"));

			$addresscleaned = $this->g_dol_escape_js($this->no_special_character_v2($address));
			//$lienGmaps = ' <a href="http'.$sforhttps.'://maps.google.com/maps?q='.urlencode($this->withoutSpecialChars($address)).'">Google Maps</a>';
			$lienGmaps = ' <a href="https://maps.google.com/maps?q='.urlencode($this->withoutSpecialChars($address)).'">Google Maps</a>';

			$html='';
			if (versioncompare(versiondolibarrarray(), array(3,7,-3)) >= 0) {	// >= 0 if we are 3.6.0 alpha or +
				$pagename=(((float) DOL_VERSION >= 6.0)?'/societe/card.php':'/societe/soc.php');

				if ($mode == 'company' || $mode == 'thirdparty') $html.= '<a href="'.DOL_URL_ROOT.$pagename.'?socid='.$elem->id.'">';
				elseif ($mode == 'contact') $html.= '<a href="'.DOL_URL_ROOT.'/contact/card.php?id='.$elem->id.'">';
				elseif ($mode == 'member') $html.= '<a href="'.DOL_URL_ROOT.'/adherents/card.php?rowid='.$elem->id.'">';
				elseif ($mode == 'patient') $html.= '<a href="'.DOL_URL_ROOT.$pagename.'?socid='.$elem->id.'">';
				else $html.='<a>';
			} else {
				$pagename=(((float) DOL_VERSION >= 6.0)?'/societe/card.php':'/societe/soc.php');

				if ($mode == 'company' || $mode == 'thirdparty') $html.= '<a href="'.DOL_URL_ROOT.$pagename.'?socid='.$elem->id.'">';
				elseif ($mode == 'contact') $html.= '<a href="'.DOL_URL_ROOT.'/contact/fiche.php?id='.$elem->id.'">';
				elseif ($mode == 'member') $html.= '<a href="'.DOL_URL_ROOT.'/adherents/fiche.php?rowid='.$elem->id.'">';
				elseif ($mode == 'patient') $html.= '<a href="'.DOL_URL_ROOT.$pagename.'?socid='.$elem->id.'">';
				else $html.='<a>';
			}
			$html.= '<b>'.$elem->name.'</b>';
			$html.= '</a>';
			$html.= '<br/>'.$addresscleaned.'<br/>';
			$urlforlink=$elem->url;
			if (! preg_match('/^http/i', $urlforlink)) $urlforlink='http://'.$urlforlink;
			if (! empty($elem->url)) $html.= '<a href="'.$urlforlink.'">'.$elem->url.'</a><br/>';
			if (! empty($elem->phone)) $html.= $elem->phone.'<br/>';
			if (! empty($elem->email)) $html.= $elem->email.'<br/>';
			$html.= '<br/>'.$lienGmaps.'<br/>';

			if (isset($elem->latitude) && isset($elem->longitude)) {
				$this->addMarkerByCoords($elem->latitude, $elem->longitude, $elem->name, $html, '', $icon);
			} elseif (isset($elem->address)) {
				//$this->addMarkerByAddress($elem->address, $elem->name, $html, '', $icon, $elem->id);
			}
		}
	}


	function no_special_character_v2($chaine)
	{
		$str=trim($chaine);
		if ($this->utf8_check($str)) {
				$string = rawurlencode($str);
				$replacements = array(
				'%C3%80' => 'A','%C3%81' => 'A',
				'%C3%88' => 'E','%C3%89' => 'E',
				'%C3%8C' => 'I','%C3%8D' => 'I',
				'%C3%92' => 'O','%C3%93' => 'O',
				'%C3%99' => 'U','%C3%9A' => 'U',
				'%C3%A0' => 'a','%C3%A1' => 'a','%C3%A2' => 'a',
				'%C3%A8' => 'e','%C3%A9' => 'e','%C3%AA' => 'e','%C3%AB' => 'e',
				'%C3%AC' => 'i','%C3%AD' => 'i','%C3%AE' => 'i',
				'%C3%B2' => 'o','%C3%B3' => 'o',
				'%C3%B9' => 'u','%C3%BA' => 'u'
				);
				$string=strtr($string, $replacements);
				return rawurldecode($string);
		} else {
				$string = strtr(
						$str,
						"\xC0\xC1\xC2\xC3\xC5\xC7
                        \xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1
                        \xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD
                        \xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB
                        \xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8
                        \xF9\xFA\xFB\xFD\xFF",
						"AAAAAC
                        EEEEIIIIDN
                        OOOOOUUUY
                        aaaaaceeee
                        iiiidnooooo
                        uuuyy"
				);
				$string = strtr($string, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
				return $string;
		}
	}

	/**
	 *      Check if a string is in UTF8
	 *
	 *      @param  string  $str        String to check
	 *              @return boolean                         True if string is UTF8 or ISO compatible with UTF8, False if not (ISO with special char or Binary)
	 */
	function utf8_check($str)
	{
			// We must use here a binary strlen function (so not dol_strlen)
			$strLength = $this->dol_strlen($str);
		for ($i=0; $i<$strLength; $i++) {
				if (ord($str[$i]) < 0x80) continue; // 0bbbbbbb
				elseif ((ord($str[$i]) & 0xE0) == 0xC0) $n=1; // 110bbbbb
				elseif ((ord($str[$i]) & 0xF0) == 0xE0) $n=2; // 1110bbbb
				elseif ((ord($str[$i]) & 0xF8) == 0xF0) $n=3; // 11110bbb
				elseif ((ord($str[$i]) & 0xFC) == 0xF8) $n=4; // 111110bb
				elseif ((ord($str[$i]) & 0xFE) == 0xFC) $n=5; // 1111110b
			else return false; // Does not match any model
			for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
					if ((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
			return true;
	}

	/**
	 * Make a strlen call. Works even if mbstring module not enabled
	 *
	 * @param   string              $string                         String to calculate length
	 * @param   string              $stringencoding         Encoding of string
	 * @return  int                                                         Length of string
	 */
	function dol_strlen($string, $stringencoding = 'UTF-8')
	{
			if (function_exists('mb_strlen')) return mb_strlen($string, $stringencoding);
		else return strlen($string);
	}

	/**
	 * Initialize the javascript code
	 *
	 * @return void
	 */
	public function init()
	{
		global $conf;

		// Google map DIV
		if (($this->width != '') && ($this->height != '')) {
			$this->content .= "\t" . '<div id="' . $this->googleMapId . '" style="width:' . $this->width . ';height:' . $this->height . '"></div>' . "\n";
		} else {
			$this->content .= "\t" . '<div id="' . $this->googleMapId . '"></div>' . "\n";
		}

		// Detect if we use https
		$sforhttps=(((empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') && (empty($_SERVER["SERVER_PORT"])||$_SERVER["SERVER_PORT"]!=443))?'':'s');

		// URL to include javascript map
		// https://developers.google.com/maps/documentation/javascript/tutorial?hl=fr
		// http://code.google.com/apis/maps/documentation/javascript/reference.html
		$url = "https://maps.googleapis.com/maps/api/js?language=" . $this->lang;
		if (empty($conf->global->GOOGLE_API_SERVERKEY)) $url.="&sensor=true";
		else $url.="&key=".$conf->global->GOOGLE_API_SERVERKEY;

		$this->content .= '<!-- GoogleMapAPIv3.init(): Include Google javascript map -->'."\n";
		$this->content .= '<script type="text/javascript" src="'.$url.'">';
		$this->content .= '</script>' . "\n";

		// Add library for clustering
		if ($this->useClusterer == true) {
			$jsgmapculster=$this->clustererLibraryPath;
			if ($sforhttps) $jsgmapculster=preg_replace('/http:/', 'https:', $jsgmapculster);
			$this->content .= '<script src="'.$jsgmapculster.'?version='.$this->version.'" type="text/javascript"></script>' . "\n";
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
		// We do not use the marker with the shadow, if we do so, we must set position of the sprite we want to extract from the image
		if (! empty($conf->global->GOOGLE_ENABLE_GMAPS_TICON)) {
			if ((empty($mode) || $mode == 'company' || $mode == 'thirdparty')) {
				$this->content .= "\t\t\t" . 'icon:  new google.maps.MarkerImage(icon, new google.maps.Size(' . $this->iconWidth . ',' . $this->iconHeight . ')),' . "\n";
			}
		}
		$this->content .= "\t\t\t" . 'position: latlng' . "\n";
		$this->content .= "\t\t" . '});' . "\n";

		// Display direction inputs in the info window
		if ($this->displayDirectionFields == true) {
			$this->content .= "\t\t" . 'content += \'<div style="clear:both;height:30px;"></div>\';' . "\n";
			$this->content .= "\t\t" . 'id_name = \'marker_\'+gmarkers.length;' . "\n";
		}

		$this->content .= "\t\t" . 'var html = \'<div style="float:left;text-align:left;width:' . $this->infoWindowWidth . ';height: 120px">\'+content+\'</div>\'' . "\n";
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

	/**
	 * Output map
	 *
	 * @return void
	 */
	public function generate()
	{
		$this->init();

		//Fonction init()
		$this->content .= "\t" . 'function initialize() {' . "\n";
		//$this->content .= "\t" . 'var myLatlng = new google.maps.LatLng(48.8792,2.34778);' . "\n";
		$this->content .= "\t" . 'var myOptions = {' . "\n";
		$this->content .= "\t\t" . 'zoom: ' . $this->zoom . ',' . "\n";
		//$this->content .= "\t\t" . 'center: myLatlng,' . "\n";
		$this->content .= "\t\t" . 'fullscreenControl: true,' . "\n";
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
			$this->content .= "\t" . 'var markerCluster = new MarkerClusterer(map, gmarkers, {gridSize: ' . $this->gridSize . ', maxZoom: ' . $this->maxZoom . '});' . "\n";
		}

		$this->content .= '}' . "\n";

		// Chargement de la map a la fin du HTML
		$this->content .= "\t" . 'window.onload=initialize;' . "\n";

		//Fermeture du javascript
		$this->content .= '</script>' . "\n";
	}
}
