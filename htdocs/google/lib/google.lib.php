<?php
/* Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */


/**
 *	\file			htdocs/google/lib/google.lib.php
 *  \brief			Library of admin functions for google module
 */


/**
 * Pumps all child elements of second SimpleXML object into first one.
 *
 * @param    object      $xml1   SimpleXML object
 * @param    object      $xml2   SimpleXML object
 * @return   void
 */
function simplexml_merge(SimpleXMLElement &$xml1, SimpleXMLElement $xml2)
{
	// convert SimpleXML objects into DOM ones
	$dom1 = new DomDocument();
	$dom2 = new DomDocument();
	$dom1->loadXML($xml1->asXML());
	$dom2->loadXML($xml2->asXML());

	// pull all child elements of second XML
	$xpath = new domXPath($dom2);
	$xpathQuery = $xpath->query('/*/*');
	for ($i = 0; $i < $xpathQuery->length; $i++) {
		// and pump them into first one
		$dom1->documentElement->appendChild(
			$dom1->importNode($xpathQuery->item($i), true));
	}
	$xml1 = simplexml_import_dom($dom1);
}


/**
 *  Return array of menu entries
 *
 *  @return			Array of head
 */
function googleadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/google/admin/google_contactsync.php", 1);
	$head[$h][1] = $langs->trans("ContactSync");
	$head[$h][2] = 'tabcontactsync';
	$h++;

	$head[$h][0] = dol_buildpath("/google/admin/google_calsync.php", 1);
	$head[$h][1] = $langs->trans("AgendaSync");
	$head[$h][2] = 'tabagendasync';
	$h++;

	if (! empty($conf->global->GOOGLE_OLD_AGENDAVIEW)) {
		$head[$h][0] = dol_buildpath("/google/admin/google.php", 1);
		$head[$h][1] = $langs->trans("AgendaView");
		$head[$h][2] = 'tabagenda';
		$h++;
	}

	$head[$h][0] = dol_buildpath("/google/admin/google_gmaps.php", 1);
	$head[$h][1] = $langs->trans("GMaps");
	$head[$h][2] = 'tabgmaps';
	$h++;

	$head[$h][0] = dol_buildpath("/google/admin/google_ad.php", 1);
	$head[$h][1] = $langs->trans("Adsense");
	$head[$h][2] = 'tabadsense';
	$h++;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
	$dolibarrversionarray=preg_split('/[\.-]/', version_dolibarr());
	$dolibarrversionok=array(3,1,-2);
	if (versioncompare($dolibarrversionarray, $dolibarrversionok) >= 0) {
		$head[$h][0] = dol_buildpath("/google/admin/google_an.php", 1);
		$head[$h][1] = $langs->trans("Analytics");
		$head[$h][2] = 'tabanalytics';
		$h++;
	}

	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'tababout';
	$h++;


	return $head;
}



/**
 * Convert all text entities in a string to numeric entities
 * With XML, only &lt; &gt; and &amp; are allowed.
 *
 * @param	string	$string		String to encode
 * @return	string				Modified string
 */
function google_html_convert_entities($string)
{
	return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/S', 'dol_google_convert_entity', $string);
}

/**
 * Swap HTML named entity with its numeric equivalent. If the entity
 * isn't in the lookup table, this function returns a blank, which
 * destroys the character in the output - this is probably the desired behaviour when producing XML.
 * List available on page http://www.w3.org/TR/REC-html40/sgml/entities.html
 *
 * @param	string	$matches	String to check and modify
 * @return	string				Modified string
 */
function dol_google_convert_entity($matches)
{
	static $table = array('quot'    => '&#34;',
						'amp'      => '&#38;',
						'lt'       => '&#60;',
						'gt'       => '&#62;',
						'OElig'    => '&#338;',
						'oelig'    => '&#339;',
						'Scaron'   => '&#352;',
						'scaron'   => '&#353;',
						'Yuml'     => '&#376;',
						'circ'     => '&#710;',
						'tilde'    => '&#732;',
						'ensp'     => '&#8194;',
						'emsp'     => '&#8195;',
						'thinsp'   => '&#8201;',
						'zwnj'     => '&#8204;',
						'zwj'      => '&#8205;',
						'lrm'      => '&#8206;',
						'rlm'      => '&#8207;',
						'ndash'    => '&#8211;',
						'mdash'    => '&#8212;',
						'lsquo'    => '&#8216;',
						'rsquo'    => '&#8217;',
						'sbquo'    => '&#8218;',
						'ldquo'    => '&#8220;',
						'rdquo'    => '&#8221;',
						'bdquo'    => '&#8222;',
						'dagger'   => '&#8224;',
						'Dagger'   => '&#8225;',
						'permil'   => '&#8240;',
						'lsaquo'   => '&#8249;',
						'rsaquo'   => '&#8250;',
						'euro'     => '&#8364;',
						'fnof'     => '&#402;',
						'Alpha'    => '&#913;',
						'Beta'     => '&#914;',
						'Gamma'    => '&#915;',
						'Delta'    => '&#916;',
						'Epsilon'  => '&#917;',
						'Zeta'     => '&#918;',
						'Eta'      => '&#919;',
						'Theta'    => '&#920;',
						'Iota'     => '&#921;',
						'Kappa'    => '&#922;',
						'Lambda'   => '&#923;',
						'Mu'       => '&#924;',
						'Nu'       => '&#925;',
						'Xi'       => '&#926;',
						'Omicron'  => '&#927;',
						'Pi'       => '&#928;',
						'Rho'      => '&#929;',
						'Sigma'    => '&#931;',
						'Tau'      => '&#932;',
						'Upsilon'  => '&#933;',
						'Phi'      => '&#934;',
						'Chi'      => '&#935;',
						'Psi'      => '&#936;',
						'Omega'    => '&#937;',
						'alpha'    => '&#945;',
						'beta'     => '&#946;',
						'gamma'    => '&#947;',
						'delta'    => '&#948;',
						'epsilon'  => '&#949;',
						'zeta'     => '&#950;',
						'eta'      => '&#951;',
						'theta'    => '&#952;',
						'iota'     => '&#953;',
						'kappa'    => '&#954;',
						'lambda'   => '&#955;',
						'mu'       => '&#956;',
						'nu'       => '&#957;',
						'xi'       => '&#958;',
						'omicron'  => '&#959;',
						'pi'       => '&#960;',
						'rho'      => '&#961;',
						'sigmaf'   => '&#962;',
						'sigma'    => '&#963;',
						'tau'      => '&#964;',
						'upsilon'  => '&#965;',
						'phi'      => '&#966;',
						'chi'      => '&#967;',
						'psi'      => '&#968;',
						'omega'    => '&#969;',
						'thetasym' => '&#977;',
						'upsih'    => '&#978;',
						'piv'      => '&#982;',
						'bull'     => '&#8226;',
						'hellip'   => '&#8230;',
						'prime'    => '&#8242;',
						'Prime'    => '&#8243;',
						'oline'    => '&#8254;',
						'frasl'    => '&#8260;',
						'weierp'   => '&#8472;',
						'image'    => '&#8465;',
						'real'     => '&#8476;',
						'trade'    => '&#8482;',
						'alefsym'  => '&#8501;',
						'larr'     => '&#8592;',
						'uarr'     => '&#8593;',
						'rarr'     => '&#8594;',
						'darr'     => '&#8595;',
						'harr'     => '&#8596;',
						'crarr'    => '&#8629;',
						'lArr'     => '&#8656;',
						'uArr'     => '&#8657;',
						'rArr'     => '&#8658;',
						'dArr'     => '&#8659;',
						'hArr'     => '&#8660;',
						'forall'   => '&#8704;',
						'part'     => '&#8706;',
						'exist'    => '&#8707;',
						'empty'    => '&#8709;',
						'nabla'    => '&#8711;',
						'isin'     => '&#8712;',
						'notin'    => '&#8713;',
						'ni'       => '&#8715;',
						'prod'     => '&#8719;',
						'sum'      => '&#8721;',
						'minus'    => '&#8722;',
						'lowast'   => '&#8727;',
						'radic'    => '&#8730;',
						'prop'     => '&#8733;',
						'infin'    => '&#8734;',
						'ang'      => '&#8736;',
						'and'      => '&#8743;',
						'or'       => '&#8744;',
						'cap'      => '&#8745;',
						'cup'      => '&#8746;',
						'int'      => '&#8747;',
						'there4'   => '&#8756;',
						'sim'      => '&#8764;',
						'cong'     => '&#8773;',
						'asymp'    => '&#8776;',
						'ne'       => '&#8800;',
						'equiv'    => '&#8801;',
						'le'       => '&#8804;',
						'ge'       => '&#8805;',
						'sub'      => '&#8834;',
						'sup'      => '&#8835;',
						'nsub'     => '&#8836;',
						'sube'     => '&#8838;',
						'supe'     => '&#8839;',
						'oplus'    => '&#8853;',
						'otimes'   => '&#8855;',
						'perp'     => '&#8869;',
						'sdot'     => '&#8901;',
						'lceil'    => '&#8968;',
						'rceil'    => '&#8969;',
						'lfloor'   => '&#8970;',
						'rfloor'   => '&#8971;',
						'lang'     => '&#9001;',
						'rang'     => '&#9002;',
						'loz'      => '&#9674;',
						'spades'   => '&#9824;',
						'clubs'    => '&#9827;',
						'hearts'   => '&#9829;',
						'diams'    => '&#9830;',
						'nbsp'     => '&#160;',
						'iexcl'    => '&#161;',
						'cent'     => '&#162;',
						'pound'    => '&#163;',
						'curren'   => '&#164;',
						'yen'      => '&#165;',
						'brvbar'   => '&#166;',
						'sect'     => '&#167;',
						'uml'      => '&#168;',
						'copy'     => '&#169;',
						'ordf'     => '&#170;',
						'laquo'    => '&#171;',
						'not'      => '&#172;',
						'shy'      => '&#173;',
						'reg'      => '&#174;',
						'macr'     => '&#175;',
						'deg'      => '&#176;',
						'plusmn'   => '&#177;',
						'sup2'     => '&#178;',
						'sup3'     => '&#179;',
						'acute'    => '&#180;',
						'micro'    => '&#181;',
						'para'     => '&#182;',
						'middot'   => '&#183;',
						'cedil'    => '&#184;',
						'sup1'     => '&#185;',
						'ordm'     => '&#186;',
						'raquo'    => '&#187;',
						'frac14'   => '&#188;',
						'frac12'   => '&#189;',
						'frac34'   => '&#190;',
						'iquest'   => '&#191;',
						'Agrave'   => '&#192;',
						'Aacute'   => '&#193;',
						'Acirc'    => '&#194;',
						'Atilde'   => '&#195;',
						'Auml'     => '&#196;',
						'Aring'    => '&#197;',
						'AElig'    => '&#198;',
						'Ccedil'   => '&#199;',
						'Egrave'   => '&#200;',
						'Eacute'   => '&#201;',
						'Ecirc'    => '&#202;',
						'Euml'     => '&#203;',
						'Igrave'   => '&#204;',
						'Iacute'   => '&#205;',
						'Icirc'    => '&#206;',
						'Iuml'     => '&#207;',
						'ETH'      => '&#208;',
						'Ntilde'   => '&#209;',
						'Ograve'   => '&#210;',
						'Oacute'   => '&#211;',
						'Ocirc'    => '&#212;',
						'Otilde'   => '&#213;',
						'Ouml'     => '&#214;',
						'times'    => '&#215;',
						'Oslash'   => '&#216;',
						'Ugrave'   => '&#217;',
						'Uacute'   => '&#218;',
						'Ucirc'    => '&#219;',
						'Uuml'     => '&#220;',
						'Yacute'   => '&#221;',
						'THORN'    => '&#222;',
						'szlig'    => '&#223;',
						'agrave'   => '&#224;',
						'aacute'   => '&#225;',
						'acirc'    => '&#226;',
						'atilde'   => '&#227;',
						'auml'     => '&#228;',
						'aring'    => '&#229;',
						'aelig'    => '&#230;',
						'ccedil'   => '&#231;',
						'egrave'   => '&#232;',
						'eacute'   => '&#233;',
						'ecirc'    => '&#234;',
						'euml'     => '&#235;',
						'igrave'   => '&#236;',
						'iacute'   => '&#237;',
						'icirc'    => '&#238;',
						'iuml'     => '&#239;',
						'eth'      => '&#240;',
						'ntilde'   => '&#241;',
						'ograve'   => '&#242;',
						'oacute'   => '&#243;',
						'ocirc'    => '&#244;',
						'otilde'   => '&#245;',
						'ouml'     => '&#246;',
						'divide'   => '&#247;',
						'oslash'   => '&#248;',
						'ugrave'   => '&#249;',
						'uacute'   => '&#250;',
						'ucirc'    => '&#251;',
						'uuml'     => '&#252;',
						'yacute'   => '&#253;',
						'thorn'    => '&#254;',
						'yuml'     => '&#255;'

						);
	// Entity not found? Destroy it.
	return isset($table[$matches[1]]) ? $table[$matches[1]] : '';
}
