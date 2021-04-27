<?php
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis@dolibarr.fr>
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
 */

/**
 *		\file       htdocs/filemanager/viewimage.php
 *		\brief      Wrapper permettant l'affichage de fichiers du filemanager
 *      \remarks    L'appel est viewimage.php?file=pathrelatifdufichier&modulepart=repfichierconcerne
 */

// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$original_file = isset($_GET["file"])?$_GET["file"]:'';
$modulepart = isset($_GET["modulepart"])?$_GET["modulepart"]:'';
$urlsource = isset($_GET["urlsource"])?$_GET["urlsource"]:'';
$rootpath = isset($_GET["rootpath"])?$_GET["rootpath"]:'';

//if (! defined('NOREQUIREUSER'))   define('NOREQUIREUSER','1');	// Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');		// Not disabled cause need to load personalized language
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC', '1');
if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN', '1');
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK', '1');
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL', '1');
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU', '1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML', '1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');
// Pour autre que mycompany, on charge environnement + info issus de logon comme le user
if (($modulepart == 'companylogo' || $modulepart == 'mycompany') && ! defined("NOLOGIN")) define("NOLOGIN", '1');

// C'est un wrapper, donc header vierge
function llxHeader()
{ }

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

dol_include_once("/filemanager/class/filemanagerroots.class.php");
include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


// Security check
if (empty($modulepart)) accessforbidden('Bad value for parameter modulepart');


/*
 * Actions
 */

// None



/*
 * View
 */

if (GETPOST("cache")) {
	// Important: Following code is to avoid page request by browser and PHP CPU at
	// each Dolibarr page access.
	if (empty($dolibarr_nocache)) {
		header('Cache-Control: max-age=3600, public, must-revalidate');
		header('Pragma: cache');       // This is to avoid having Pragma: no-cache
	} else header('Cache-Control: no-cache');
	//print $dolibarr_nocache; exit;
}

// Define mime type
$type = 'application/octet-stream';
if (! empty($_GET["type"])) $type=$_GET["type"];
else $type=dol_mimetype($original_file);

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../", "/", $original_file);

// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if (! empty($rootpath) && is_numeric($rootpath)) {
	$result=$filemanagerroots->fetch($rootpath);
	//var_dump($filemanagerroots);
	$rootpath=$filemanagerroots->rootpath;
}

// Security checks
if (empty($modulepart)) accessforbidden('Bad value for parameter modulepart');
$accessallowed=0;
$sqlprotectagainstexternals='';
if ($modulepart) {
	// On fait une verification des droits et on definit le repertoire concerne

	// Wrapping for filemanager
	if ($modulepart == 'filemanager') {
		$dirnameslash=str_replace(array("\\","/"), "/", dirname($original_file));
		$rootpathslash=str_replace(array("\\","/"), "/", $rootpath);
		//print "x".$dirnameslash." - ".preg_quote($rootpathslash,'/');
		if (preg_match('/^'.preg_quote($rootpathslash, '/').'/', $dirnameslash)) {
			$accessallowed=1;
		}
	}
}

// Basic protection (against external users only)
if ($user->societe_id > 0) {
	if ($sqlprotectagainstexternals) {
		$resql = $db->query($sqlprotectagainstexternals);
		if ($resql) {
			$num=$db->num_rows($resql);
			$i=0;
			while ($i < $num) {
				$obj = $db->fetch_object($resql);
				if ($user->societe_id != $obj->fk_soc) {
					$accessallowed=0;
					break;
				}
				$i++;
			}
		}
	}
}

// Security:
// Limite acces si droits non corrects
if (! $accessallowed) {
	accessforbidden();
}

// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans les noms de fichiers.
if (preg_match('/\.\./', $original_file) || preg_match('/[<>|]/', $original_file)) {
	dol_syslog("Refused to deliver file ".$original_file, LOG_WARNING);
	// Do no show plain path in shown error message
	dol_print_error(0, 'Error: File '.$_GET["file"].' does not exists');
	exit;
}



clearstatcache();


// Output files on browser
dol_syslog("viewimage.php return file $original_file content-type=$type");
$original_file_osencoded=dol_osencode($original_file);

// This test if file exists should be useless. We keep it to find bug more easily
if (! dol_is_file($original_file_osencoded)) {
	dol_print_error(0, 'Error: File '.$_GET["file"].' does not exists or filesystems permissions are not allowed');
	exit;
}

// Les drois sont ok et fichier trouve
if ($type) {
	//print "eeee".$type;exit;
	header('Content-Disposition: inline; filename="'.basename($original_file).'"');
	header('Content-type: '.$type);
} else {
	header('Content-Disposition: inline; filename="'.basename($original_file).'"');
	header('Content-type: image/png');
}

$result=readfile($original_file_osencoded);

if (is_object($db)) $db->close();
