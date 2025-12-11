<?php
/* Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2010	   Pierre Morin         <pierre.morin@auguria.net>
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
 *	\file       htdocs/filemanager/ajaxeditcontent.php
 *  \brief      Service to return a HTML view of a file
 *              Call of this service is made with URL:
 *              ajaxpreview.php?action=preview&modulepart=repfichierconcerne&file=pathrelatifdufichier
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1); // Disables token renewal
if (! defined('NOREQUIREMENU')) define('NOREQUIREMENU', '1');
if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML', '1');
if (! defined('NOREQUIREAJAX')) define('NOREQUIREAJAX', '1');

// C'est un wrapper, donc header vierge
function llxHeader()
{ }

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
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

include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
include_once DOL_DOCUMENT_ROOT."/core/lib/security2.lib.php";
dol_include_once("/filemanager/class/filemanagerroots.class.php");


// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$action = GETPOST("action");
$original_file = isset($_GET["file"])?$_GET["file"]:'';
$modulepart = isset($_GET["modulepart"])?$_GET["modulepart"]:'';
$urlsource = isset($_GET["urlsource"])?$_GET["urlsource"]:'';
$rootpath = isset($_GET["rootpath"])?$_GET["rootpath"]:'';

// Define mime type
$type = 'application/octet-stream';
if (! empty($_GET["type"]) && $_GET["type"] != 'auto') $type=$_GET["type"];
else $type=dol_mimetype($original_file, 'text/plain');
//print 'X'.$type.'-'.$original_file;exit;

// Define attachment (attachment=true to force choice popup 'open'/'save as')
$attachment = true;

//print "XX".$attachment;exit;

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../", "/", $original_file);
$original_file_osencoded=dol_osencode($original_file);  // New file name encoded in OS encoding charset

// find the subdirectory name as the reference
$refname=basename(dirname($original_file)."/");

// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if (! empty($rootpath) && is_numeric($rootpath)) {
	$result=$filemanagerroots->fetch($rootpath);
	//var_dump($filemanagerroots);
	$rootpath=$filemanagerroots->rootpath;
}

// Security checks
$accessallowed=0;
$sqlprotectagainstexternals='';
if ($modulepart) {
	// On fait une verification des droits et on definit le repertoire concerne

	// Wrapping for filemanager
	if ($modulepart == 'filemanager') {
		$dirnameslash=str_replace(array("\\","/"), "/", dirname($original_file));
		$rootpathslash=str_replace(array("\\","/"), "/", $rootpath);
		//print 'dirnameslash='.$dirnameslash.' rootpathslash='.$rootpathslash;
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
if (! $accessallowed) accessforbidden();


// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans
// les noms de fichiers.
if (preg_match('/\.\./', $original_file) || preg_match('/[<>|]/', $original_file)) {
	dol_syslog("Refused to deliver file ".$original_file);
	// Do no show plain path in shown error message
	dol_print_error(0, $langs->trans("ErrorFileNameInvalid", $_GET["file"]));
	exit;
}

// Check permissions
if (! $user->hasRight('filemanager', 'read')) accessforbidden();



/*
 * Action
 */


if ($action == 'newdir') {   // Create a dir
	clearstatcache();

	$original_dir=dirname($original_file);
	//$newdir=$original_dir.'/new_dir';
	$newdir=$original_file;

	//print 'original_dir='.$orignal_dir.' newdir='.$newdir;
	dol_syslog(__FILE__." newdir ".$newdir." ", LOG_DEBUG);

	// This test should be useless. We keep it to find bug more easily
	$original_newdir_osencoded=dol_osencode($newdir);     // New file name encoded in OS encoding charset
	$original_dir_osencoded=dol_osencode($original_dir);  // New file name encoded in OS encoding charset
	if (! is_dir($original_dir_osencoded)) {
		dol_print_error(0, $langs->trans("ErrorDirDoesNotExists", $originale_dir));
		exit;
	}

	if (is_dir($original_newdir_osencoded)) {
		$langs->load("errors");
		print '<div class="error">'.$langs->trans("ErrorDirAlreadyExists", $newdir).'</div>';
		return -2;
	}

	$result=dol_mkdir($original_newdir_osencoded);
	if ($result <= 0) {
		$langs->load("errors");
		dol_syslog("Failed to write into file ".$newdir);
		print '<div class="error">'.$langs->trans("ErrorFailToCreateDir", $newdir).'</div>';
		return -1;
	} else {
		dol_syslog("Created dir ".$newdir);
		print '<div class="ok">'.$langs->trans("Success").'</div>';
		return 1;
	}
}


if ($action == 'newfile') {   // Create a file
	clearstatcache();

	$original_dir=dirname($original_file);
	//$newfile=$original_dir.'/new_file.txt';
	$newfile=$original_file;

	//print 'original_dir='.$orignal_dir.' newfile='.$newfile;
	dol_syslog(__FILE__." newfile ".$newfile." ", LOG_DEBUG);

	// This test should be useless. We keep it to find bug more easily
	$original_newfile_osencoded=dol_osencode($newfile);     // New file name encoded in OS encoding charset
	$original_dir_osencoded=dol_osencode($original_dir);  // New file name encoded in OS encoding charset
	if (! is_dir($original_dir_osencoded)) {
		dol_print_error(0, $langs->trans("ErrorDirDoesNotExists", $originale_dir));
		exit;
	}

	if (file_exists($original_newfile_osencoded)) {
		$langs->load("errors");
		print '<div class="error">'.$langs->trans("ErrorFileAlreadyExists", $newfile).'</div>';
		return -2;
	}

	$f=fopen($original_newfile_osencoded, 'w');    // 'w'
	if (fwrite($f, $content) === false) {
		$langs->load("errors");
		fclose($f);
		dol_syslog("Failed to write into file ".$newfile);
		print '<div class="error">'.$langs->trans("ErrorFailToCreateFile", $newfile).'</div>';
		return -1;
	} else {
		fclose($f);
		dol_syslog("Saved file ".$newfile);
		print '<div class="ok">'.$langs->trans("Success").'</div>';
		return 1;
	}
}

if ($action == 'save') {   // Remove a file
	clearstatcache();

	dol_syslog(__FILE__." save ".$original_file." ", LOG_DEBUG);

	// This test should be useless. We keep it to find bug more easily
	$original_file_osencoded=dol_osencode($original_file);  // New file name encoded in OS encoding charset
	if (! file_exists($original_file_osencoded)) {
		dol_print_error(0, $langs->trans("ErrorFileDoesNotExists", $_GET["file"]));
		exit;
	}

	$sizeofcontent=GETPOST('sizeofcontent');
	$textformat=GETPOST('textformat');
	$content=GETPOST('str', 2);
	if ($textformat == 'ISO' && utf8_check($content)) $content=utf8_decode($content);

	if (strlen($content) != $sizeofcontent) {
		dol_syslog("Size of content (".strlen($content).") for new file differs of size expected (".$sizeofcontent."). May be a limit in POST/GET request. We ignore save to keep file integrity.", LOG_ERR);
		print 'KO SIZENOTEXPECTED';
		return -2;
	} else {
		$f=@fopen($original_file_osencoded, 'w');    // 'w'
		if ($f) {
			dol_syslog("original_file_osencoded=".$original_file_osencoded." content=".$content);
			// If original format was ISO, we kepp this format

			if (fwrite($f, $content) === false) {
				$langs->load("errors");
				fclose($f);
				dol_syslog("Failed to write into file ".$original_file);
				print '<div class="error">'.$langs->trans("ErrorFailToWriteIntoFile").'</div>';
				return -3;
			} else {
				fclose($f);
				dol_syslog("Saved file ".$original_file);
				print '<div class="ok">'.$langs->trans("Success").'</div>';
				return 1;
			}
		} else {
			$langs->load("errors");
			dol_syslog("Failed to write into file ".$original_file);
			print '<div class="error">'.$langs->trans("ErrorFailToCreateFile", $original_file).'<br>'.$langs->trans("ErrorWebServerUserHasNotPermission", dol_getwebuser('user')).'</div>';
			return -1;
		}
	}
}



/*
 * View
 */

// Ajout directives pour resoudre bug IE
header('Cache-Control: Public, must-revalidate');
header('Pragma: public');


if ($action == 'edit') {   // Return file content
	print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

	$langs->load("filemanager@filemanager");

	if (dol_is_dir($original_file)) {
		print $langs->trans("YouMustSelectAFileToUseFileEditorTool");
		return;
	}

	clearstatcache();

	$filename = basename($original_file);

	// Output file on browser
	dol_syslog(__FILE__." download ".$original_file." ".$filename." content-type=".$type);
	$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset

	// This test if file exists should be useless. We keep it to find bug more easily
	if (! file_exists($original_file_osencoded)) {
		dol_print_error(0, $langs->trans("ErrorFileDoesNotExists", $original_file));
		exit;
	}

	// Les drois sont ok et fichier trouve, et fichier texte, on l'envoie

	if (preg_match('/text/i', $type)) {
		$maxsize=500000;

		$size=dol_filesize($original_file_osencoded);

		$handle = fopen($original_file_osencoded, "r");
		$content = fread($handle, $maxsize);
		if (! utf8_check($content)) { $isoutf='iso'; $content=utf8_encode($content); }
		fclose($handle);

		print 'Autodetect text format: '.($isoutf?'ISO':'UTF-8');
		print '<input type="hidden" id="textformat" name="textformat" value="'.($isoutf?'ISO':'UTF-8').'">'."\n";
		print '<br><br>'."\n";

		$okforextandededitor=false;
		$doleditor=new DolEditor('fmeditor', $content, 640, 0, 'dolibarr_notes', 'In', true, true, $okforextandededitor, 36, '90%');
		$doleditor->Create();

		//print $content;
	} elseif (preg_match('/image/i', $type)) {
		print "Image file with type ".$type.'<br>';
		print $langs->trans("NoEditorForThisFormat");
	} else {
		print "Binary file with type ".$type.'<br>';
		print $langs->trans("NoEditorForThisFormat");
	}
}

if (is_object($db)) $db->close();
