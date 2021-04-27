#!/usr/bin/php
<?php
/* Copyright (C) 2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *
 *
 * Mets a jour les prix fournisseurs des produits a partir d'un fichier XML
 *
 *
 * Pour recupere les infos de materiel.net
 *
 * wget "http://materiel.net/partenaire/search.php3?format=xml&nobanner=1"
 */
// Editer les 3 valeurs suivantes en conformite avec votre installation
$userid = 1;
$idfourn = 10;
$file = "/tmp/materiel.full.xml";
// Supprimez le die() une fois le script configur� :-)
die("!\n!\n! Configurez le script avant de le lancer\n!\n!\n");

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
	echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
	exit;
}

// Global variables
$version='$Revision: 1.3 $';
$error=0;

// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include $path."../../master.inc.php";
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include $path."../../htdocs/master.inc.php";
if (! $res && file_exists("../master.inc.php")) $res=@include "../master.inc.php";
if (! $res && file_exists("../../master.inc.php")) $res=@include "../../master.inc.php";
if (! $res && file_exists("../../../master.inc.php")) $res=@include "../../../master.inc.php";
if (! $res && preg_match('/\/nltechno([^\/]*)\//', $_SERVER["PHP_SELF"], $reg)) $res=@include $path."../../../dolibarr".$reg[1]."/htdocs/master.inc.php"; // Used on dev env only
if (! $res && preg_match('/\/nltechno([^\/]*)\//', $_SERVER["PHP_SELF"], $reg)) $res=@include "../../../dolibarr".$reg[1]."/htdocs/master.inc.php"; // Used on dev env only
if (! $res) die("Failed to include master.inc.php file\n");
require_once DOL_DOCUMENT_ROOT ."/product.class.php";


$user = new User($db);
$user->id = $userid;

$depth = array();
$index = 0;
$items = array();
$current = '';

/*
 * Parse le fichier XML et l'ins�re dans un tableau
 *
 */
$xml_parser = xml_parser_create();

xml_set_element_handler($xml_parser, "debutElement", "finElement");
xml_set_character_data_handler($xml_parser, "charData");

if (!($fp = fopen($file, "r"))) {
	die("Impossible d'ouvrir le fichier XML");
}

while ($data = fread($fp, 4096) ) {
	if (!xml_parse($xml_parser, $data, feof($fp))) {
		die(sprintf("erreur XML : %s � la ligne %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
	}
}
xml_parser_free($xml_parser);
/*
 * Traite les donn�es du tableau
 *
 */
if (count($items) > 0) {
	while ($item = array_pop($items) ) {
		$product = new Product($db);
		$product->UpdateBuyPriceByFournRef($idfourn, $item["code"], 1, $item["price"], $user);
	}
}

exit ;

/*
 * Fonctions
 *
 */

function charData($parser, $data)
{
	global $index, $current, $items;
	$char_data = trim($data);

	if ($char_data)
	$char_data = preg_replace('/  */', ' ', $data);

	if ($current <> '')
	$items[$index][$current] = $char_data;
}

/**
 * debutElement
 *
 * @param 	int		$parser		Parser
 * @param 	string	$name		Name
 * @param 	int		$attrs		Attrs
 * @return	void
 */
function debutElement($parser, $name, $attrs)
{
	global $depth, $index, $items, $current;

	$depth[$parser]++;

	if ($name == 'ITEM') {
		$index++;
		$current = '';
	} elseif ($name == 'NAME') {
		$current = "name";
	} elseif ($name == 'CODE') {
		$current = "code";
	} elseif ($name == 'PRICE') {
		$current = "price";
	} elseif ($name == 'GENRE') {
		$current = "genre";
	} else {
		$current = '';
	}
}

/**
 * finElement
 *
 * @param 	int		$parser		Parser
 * @param 	string	$name		Name
 * @return	void
 */
function finElement($parser, $name)
{
	global $depth;
	$depth[$parser]--;
}
