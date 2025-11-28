<?php
/* Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2010	   Pierre Morin         <pierre.morin@auguria.net>
 * Copyright (C) 2010	   Juanjo Menent        <jmenent@2byte.es>
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
 *	\file       htdocs/document.php
 *  \brief      Wrapper to download data files
 *  \version    $Id: document.php,v 1.1 2011/06/15 11:35:03 eldy Exp $
 *  \remarks    Call of this wrapper is made with URL:
 * 				document.php?modulepart=repfichierconcerne&file=pathrelatifdufichier
 */

define('NOTOKENRENEWAL', 1); // Disables token renewal
// Pour autre que bittorrent, on charge environnement + info issus de logon (comme le user)
if (isset($_GET["modulepart"]) && $_GET["modulepart"] == 'bittorrent' && ! defined("NOLOGIN")) {
	define("NOLOGIN", 1);
	define("NOCSRFCHECK", 1);	// We accept to go on this page from external web site.
}
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

dol_include_once("/filemanager/class/filemanagerroots.class.php");
include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$encoding = '';
$action = GETPOST("action");
$original_file = GETPOST("file");	// Do not use urldecode here ($_GET are already decoded by PHP).
$modulepart = GETPOST("modulepart");
$urlsource = GETPOST("urlsource");
$rootpath = GETPOST('rootpath');
$id=GETPOST('id', 'int');

// Security check
if (empty($modulepart)) accessforbidden('Bad value for parameter modulepart');


/*
 * Action
 */

// None



/*
 * View
 */

// Define mime type
$type = 'application/octet-stream';
if (GETPOST('type')) $type=GETPOST('type');
else $type=dol_mimetype($original_file);
//print 'X'.$type.'-'.$original_file;exit;

// Define attachment (attachment=true to force choice popup 'open'/'save as')
$attachment = true;
// Text files
if (preg_match('/\.txt$/i', $original_file)) { $attachment = false; }
if (preg_match('/\.csv$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.tsv$/i', $original_file)) { $attachment = true; }
// Documents MS office
if (preg_match('/\.doc(x)?$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.dot(x)?$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.mdb$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.ppt(x)?$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.xls(x)?$/i', $original_file)) { $attachment = true; }
// Documents Open office
if (preg_match('/\.odp$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.ods$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.odt$/i', $original_file)) { $attachment = true; }
// Misc
if (preg_match('/\.(html|htm)$/i', $original_file)) { $attachment = false; }
if (preg_match('/\.pdf$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.sql$/i', $original_file)) { $attachment = true; }
// Images
if (preg_match('/\.jpg$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.jpeg$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.png$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.gif$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.bmp$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.tiff$/i', $original_file)) { $attachment = true; }
// Calendar
if (preg_match('/\.vcs$/i', $original_file)) { $attachment = true; }
if (preg_match('/\.ics$/i', $original_file)) { $attachment = true; }
if (GETPOST("attachment")) { $attachment = true; }
if (! empty($conf->global->MAIN_DISABLE_FORCE_SAVEAS)) $attachment=false;
//print "XX".$attachment;exit;

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../", "/", $original_file);

// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if ($id) {
	$result=$filemanagerroots->fetch($id);
	//var_dump($filemanagerroots);
	$rootpath=$filemanagerroots->rootpath;
	//print preg_quote($rootpath,'/').'<br>';
	if (! preg_match('/'.preg_quote($rootpath, '/').'/', $original_file)) {
		//print 'id='.$id.' rootpath='.$rootpath.' original_file='.$original_file;
		accessforbidden('Value for id '.$id.' is not a root path matching root path of file to download');
	}
} else accessforbidden('Bad value for parameter id');

// Security check
$accessallowed=0;
$sqlprotectagainstexternals='';
if ($modulepart) {
	// On fait une verification des droits et on definit le repertoire concerne

	// Wrapping for third parties
	if ($modulepart == 'filemanager') {
		if ($user->hasRight('filemanager', 'read') || preg_match('/^specimen/i', $original_file)) {
			$accessallowed=1;
		}
		$original_file=$original_file;
		//$sqlprotectagainstexternals = "SELECT rowid as fk_soc FROM ".MAIN_DB_PREFIX."societe WHERE rowid='".$refname."' AND entity=".$conf->entity;
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
// On interdit les remontees de repertoire ainsi que les pipe dans
// les noms de fichiers.
if (preg_match('/\.\./', $original_file) || preg_match('/[<>|]/', $original_file)) {
	dol_syslog("Refused to deliver file ".$original_file);
	$file=basename($original_file);		// Do no show plain path of original_file in shown error message
	dol_print_error(0, $langs->trans("ErrorFileNameInvalid", $file));
	exit;
}


if ($action == 'remove_file') {	// Remove a file
	clearstatcache();

	dol_syslog("document.php remove $original_file $urlsource", LOG_DEBUG);

	// This test should be useless. We keep it to find bug more easily
	$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset
	if (! file_exists($original_file_osencoded)) {
		$file=basename($original_file);		// Do no show plain path of original_file in shown error message
		dol_print_error(0, $langs->trans("ErrorFileDoesNotExists", $file));
		exit;
	}

	dol_delete_file($original_file);

	dol_syslog("document.php back to ".urldecode($urlsource), LOG_DEBUG);

	header("Location: ".urldecode($urlsource));

	return;
} else // Open and return file
{
	clearstatcache();

	$filename = basename($original_file);

	// Output file on browser
	dol_syslog("document.php download $original_file $filename content-type=$type");
	$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset

	// This test if file exists should be useless. We keep it to find bug more easily
	if (! file_exists($original_file_osencoded)) {
		print 'Failed to locate file '.$original_file_osencoded;
		exit;
	}

	// Les drois sont ok et fichier trouve, on l'envoie

	if ($encoding)   header('Content-Encoding: '.$encoding);
	if ($type)       header('Content-Type: '.$type.(preg_match('/text/', $type)?'; charset="'.$conf->file->character_set_client:''));
	if ($attachment) header('Content-Disposition: attachment; filename="'.$filename.'"');
	else header('Content-Disposition: inline; filename="'.$filename.'"');

	// Ajout directives pour resoudre bug IE
	header('Cache-Control: Public, must-revalidate');
	header('Pragma: public');

	readfile($original_file_osencoded);
}

if (is_object($db)) $db->close();
