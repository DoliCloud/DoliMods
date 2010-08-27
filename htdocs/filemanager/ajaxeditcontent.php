<?php
/* Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2010	   Pierre Morin         <pierre.morin@auguria.net>
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
 *	\file       htdocs/filemanager/ajaxeditcontent.php
 *  \brief      Service to return a HTML view of a file
 *  \version    $Id: ajaxeditcontent.php,v 1.2 2010/08/27 00:09:55 eldy Exp $
 *  \remarks    Call of this service is made with URL:
 *              ajaxpreview.php?action=preview&modulepart=repfichierconcerne&file=pathrelatifdufichier
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU')) define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX')) define('NOREQUIREAJAX','1');

// C'est un wrapper, donc header vierge
function llxHeader() { }

if (file_exists("../main.inc.php")) require("../main.inc.php");	// Load $user and permissions
else require("../../../dolibarr/htdocs/main.inc.php");    // Load $user and permissions
require_once(DOL_DOCUMENT_ROOT.'/lib/files.lib.php');
require_once(DOL_DOCUMENT_ROOT.'/lib/doleditor.class.php');


// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$action = isset($_GET["action"])?$_GET["action"]:'';
$original_file = isset($_GET["file"])?$_GET["file"]:'';
$modulepart = isset($_GET["modulepart"])?$_GET["modulepart"]:'';
$urlsource = isset($_GET["urlsource"])?$_GET["urlsource"]:'';
$rootpath = isset($_GET["rootpath"])?$_GET["rootpath"]:'';

// Define mime type
$type = 'application/octet-stream';
if (! empty($_GET["type"]) && $_GET["type"] != 'auto') $type=$_GET["type"];
else $type=dol_mimetype($original_file,'text/plain');
//print 'X'.$type.'-'.$original_file;exit;

// Define attachment (attachment=true to force choice popup 'open'/'save as')
$attachment = true;

//print "XX".$attachment;exit;

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../","/", $original_file);

// find the subdirectory name as the reference
$refname=basename(dirname($original_file)."/");

$accessallowed=0;
$sqlprotectagainstexternals='';
if ($modulepart)
{
	// On fait une verification des droits et on definit le repertoire concerne

	// Wrapping for filemanager
	if ($modulepart == 'filemanager')
	{
		$accessallowed=1;
		// TODO Test on $rootpath
		//$original_file=$conf->societe->dir_output.'/'.$original_file;
		//$sqlprotectagainstexternals = "SELECT rowid as fk_soc FROM ".MAIN_DB_PREFIX."societe WHERE rowid='".$refname."' AND entity=".$conf->entity;
	}
}

// Basic protection (against external users only)
if ($user->societe_id > 0)
{
	if ($sqlprotectagainstexternals)
	{
		$resql = $db->query($sqlprotectagainstexternals);
		if ($resql)
		{
			$num=$db->num_rows($resql);
			$i=0;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if ($user->societe_id != $obj->fk_soc)
				{
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
if (! $accessallowed)
{
	accessforbidden();
}

// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans
// les noms de fichiers.
if (preg_match('/\.\./',$original_file) || preg_match('/[<>|]/',$original_file))
{
	dol_syslog("Refused to deliver file ".$original_file);
	// Do no show plain path in shown error message
	dol_print_error(0,$langs->trans("ErrorFileNameInvalid",$_GET["file"]));
	exit;
}

// Check permissions
if (! $user->rights->filemanager->read)
{
    accessforbidden();
}



/*
 * Action
 */

if ($action == 'remove_file')	// Remove a file
{
	clearstatcache();

	dol_syslog(__FILE__." remove $original_file $urlsource", LOG_DEBUG);

	// This test should be useless. We keep it to find bug more easily
	$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset
	if (! file_exists($original_file_osencoded))
	{
		dol_print_error(0,$langs->trans("ErrorFileDoesNotExists",$_GET["file"]));
		exit;
	}

	dol_delete_file($original_file);

	dol_syslog(__FILE__." back to ".urldecode($urlsource), LOG_DEBUG);

	header("Location: ".urldecode($urlsource));

	return;
}



/*
 * View
 */

// Ajout directives pour resoudre bug IE
header('Cache-Control: Public, must-revalidate');
header('Pragma: public');


if ($action == 'edit')   // Return file content
{
    print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

    $langs->load("filemanager@filemanager");

    if (dol_is_dir($original_file))
    {
        print $langs->trans("YouMustSelectAFileToUseFileEditorTool");
        return;
    }

    clearstatcache();

	$filename = basename($original_file);

	// Output file on browser
	dol_syslog(__FILE__." download $original_file $filename content-type=$type");
	$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset

	// This test if file exists should be useless. We keep it to find bug more easily
	if (! file_exists($original_file_osencoded))
	{
		dol_print_error(0,$langs->trans("ErrorFileDoesNotExists",$original_file));
		exit;
	}

	// Les drois sont ok et fichier trouve, et fichier texte, on l'envoie

	if (preg_match('/text/i',$type))
	{
		$maxsize=50000;

        $handle = fopen($original_file_osencoded, "r");
        $content = fread($handle, $maxsize);
        fclose($handle);

        $doleditor=new DolEditor('fmeditor',$content,700,'Basic','In',true,true,false,36,120);
        $doleditor->Create();

        //print $content;
	}
	else if (preg_match('/image/i',$type))
	{
		print "Image file with type ".$type.'<br>';
        print $langs->trans("NoEditorForThisFormat");
	}
	else
	{
		print "Binary file with type ".$type.'<br>';
        print $langs->trans("NoEditorForThisFormat");
	}

}

?>
