<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *      \file       htdocs/filemanager/ajaxFileTree.php
 *      \ingroup    filemanager
 *      \brief      This script returns content of a directory for filetree
 *      \version    $Id: ajaxFileTree.php,v 1.1 2010/08/21 16:39:00 eldy Exp $
 */


// This script is called with a POST method.
// Directory to scan (full path) is inside POST['dir'].

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');
if (! defined('NOREQUIREMENU')) define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX')) define('NOREQUIREAJAX','1');

// C'est un wrapper, donc header vierge
function llxHeader() { }

if (file_exists("../main.inc.php")) require("../main.inc.php"); // Load $user and permissions
else require("../../../dolibarr/htdocs/main.inc.php");    // Load $user and permissions
require_once(DOL_DOCUMENT_ROOT.'/lib/files.lib.php');

// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$selecteddir = urldecode($_POST['dir']);


if( file_exists($selecteddir) ) {
	$files = scandir($selecteddir);
	natcasesort($files);
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		// All dirs
		foreach( $files as $file ) {
			if( file_exists($selecteddir . $file) && $file != '.' && $file != '..' && is_dir($selecteddir . $file) ) {
				print "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($selecteddir . $file) . "/\"";
				print " onClick=\"loadanshowpreview('".dol_escape_js($selecteddir . $file)."')\"";
				print ">" . htmlentities($file) . "</a></li>";
			}
		}
		// All files
		foreach( $files as $file ) {
			if( file_exists($selecteddir . $file) && $file != '.' && $file != '..' && !is_dir($selecteddir . $file) ) {
				$ext = preg_replace('/^.*\./', '', $file);
				print "<li class=\"file ext_".$ext."\"><a href=\"#\" rel=\"" . htmlentities($selecteddir . $file) . "\">" . htmlentities($file) . "</a></li>";
			}
		}
		echo "</ul>";
	}
}

// This ajax service is called only when a directory $selecteddir is opened.
print '<script language="javascript">';
//print "loadanshowpreview('".dol_escape_js($selecteddir)."');";
print '</script>';

?>