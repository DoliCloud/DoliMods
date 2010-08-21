<?php
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis@dolibarr.fr>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *		\file       htdocs/viewimage.php
 *		\brief      Wrapper permettant l'affichage de fichiers images Dolibarr
 *      \remarks    L'appel est viewimage.php?file=pathrelatifdufichier&modulepart=repfichierconcerne
 *		\version    $Id: viewimage.php,v 1.1 2010/08/21 17:26:08 eldy Exp $
 */

// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$original_file = isset($_GET["file"])?$_GET["file"]:'';
$modulepart = isset($_GET["modulepart"])?$_GET["modulepart"]:'';
$urlsource = isset($_GET["urlsource"])?$_GET["urlsource"]:'';

//if (! defined('NOREQUIREUSER'))   define('NOREQUIREUSER','1');	// Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');		// Not disabled cause need to load personalized language
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC','1');
if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK','1');
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL','1');
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
// Pour autre que companylogo, on charge environnement + info issus de logon comme le user
if (($modulepart == 'companylogo') && ! defined("NOLOGIN")) define("NOLOGIN",'1');


// C'est un wrapper, donc header vierge
function llxHeader() { }


if (file_exists("../main.inc.php")) require("../main.inc.php"); // Load $user and permissions
else require("../../../dolibarr/htdocs/main.inc.php");    // Load $user and permissions
require_once(DOL_DOCUMENT_ROOT.'/lib/files.lib.php');

// Define mime type
$type = 'application/octet-stream';
if (! empty($_GET["type"])) $type=$_GET["type"];
else $type=dol_mimetype($original_file);

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../","/", $original_file);

$accessallowed=0;
if ($modulepart)
{
	// Check permissions and define directory

	// Wrapping pour les photo utilisateurs
	if ($modulepart == 'filemanager')
	{
		$accessallowed=1;
	}
}

// Security:
// Limit access if permissions are wrong
if (! $accessallowed)
{
	accessforbidden();
}

// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans
// les noms de fichiers.
if (preg_match('/\.\./',$original_file) || preg_match('/[<>|]/',$original_file))
{
	dol_syslog("Refused to deliver file ".$original_file, LOG_WARNING);
	// Do no show plain path in shown error message
	dol_print_error(0,'Error: File '.$_GET["file"].' does not exists');
	exit;
}



clearstatcache();

// Output files on browser
dol_syslog("viewimage.php return file $original_file content-type=$type");
$original_file_osencoded=dol_osencode($original_file);

// This test if file exists should be useless. We keep it to find bug more easily
if (! file_exists($original_file_osencoded))
{
	dol_print_error(0,'Error: File '.$_GET["file"].' does not exists');
	exit;
}

// Les drois sont ok et fichier trouve
if ($type)
{
	header('Content-type: '.$type);
}
else
{
	header('Content-type: image/png');
}

readfile($original_file_osencoded);

?>
