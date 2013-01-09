<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *      \file       htdocs/filemanager/ajaxFileTree.php
 *      \ingroup    filemanager
 *      \brief      This script returns content of a directory for filetree
 *      \version    $Id: ajaxFileTree.php,v 1.8 2011/07/06 17:03:41 eldy Exp $
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

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
dol_include_once("/filemanager/class/filemanagerroots.class.php");
include_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');

// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$selecteddir = urldecode(GETPOST('dir'));
$openeddir = GETPOST('openeddir');

// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans
// les noms de fichiers.
if (preg_match('/\.\./',$selecteddir) || preg_match('/[<>|]/',$selecteddir))
{
    dol_syslog("Refused to deliver file ".$original_file);
    // Do no show plain path in shown error message
    dol_print_error(0,$langs->trans("ErrorFileNameInvalid",GETPOST("file")));
    exit;
}

// Check permissions
if (! $user->rights->filemanager->read)
{
    accessforbidden();
}



/*
 * View
 */

if( file_exists($selecteddir) )
{
	$files = @scandir($selecteddir);
    if ($files)
    {
    	natcasesort($files);
    	if ( count($files) > 2 )	// The 2 accounts for . and ..
    	{
    		// $selecteddir = '/tmp/'
    		// $file = 'subdir'

    		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">\n";
    		// All dirs
    		foreach( $files as $file ) {
    			if( file_exists($selecteddir . $file) && $file != '.' && $file != '..' && is_dir($selecteddir . $file) ) {
    				print "<li id=\"li_".(md5($selecteddir . $file))."\" class=\"directory collapsed\">";
    				print "<a id=\"a_".(md5($selecteddir . $file))."\" idparent=\"a_".(md5($selecteddir))."\" class=\"fmdirlia jqft\" href=\"#\" rel=\"" . dol_escape_htmltag($selecteddir . $file . '/') . "\"";
    				print " onClick=\"loadandshowpreview('".dol_escape_js($selecteddir . $file)."', this)\"";
    				print ">" . dol_escape_htmltag($file) . "</a>";
    				print "</li>"."\n";
    			}
    		}
    		// All files
    		foreach( $files as $file ) {
    			if( file_exists($selecteddir . $file) && $file != '.' && $file != '..' && !is_dir($selecteddir . $file) ) {
    				$ext = preg_replace('/^.*\./', '', $file);
    				print "<li id=\"li_".(md5($selecteddir . $file))."\" class=\"file ext_".$ext."\">";
    				print "<a id=\"a_".(md5($selecteddir . $file))."\" idparent=\"a_".(md5($selecteddir))."\" class=\"fmfilelia jqft\" href=\"#\" rel=\"" . dol_escape_htmltag($selecteddir . $file) . "\"";
    				print " onClick=\"loadandshowpreview('".dol_escape_js($selecteddir . $file)."', this)\"";
    				print ">" . dol_escape_htmltag($file) . "</a>";
    				print "</li>"."\n";
    			}
    		}
    		echo "</ul>\n";
    	}
    }
    else print "PermissionDenied";
}

// This ajax service is called only when a directory $selecteddir is opened but not closed.
//print '<script language="javascript">';
//print "loadandshowpreview('".dol_escape_js($selecteddir)."');";
//print '</script>';

if (is_object($db)) $db->close();
?>