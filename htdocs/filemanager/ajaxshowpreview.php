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
 *	\file       htdocs/filemanager/ajaxshowpreview.php
 *  \brief      Service to return a HTML preview of a file
 *  \version    $Id: ajaxshowpreview.php,v 1.6 2010/08/26 01:20:32 eldy Exp $
 *  \remarks    Call of this service is made with URL:
 * 				ajaxpreview.php?action=preview&modulepart=repfichierconcerne&file=pathrelatifdufichier
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

// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$action = isset($_GET["action"])?$_GET["action"]:'';
$original_file = isset($_GET["file"])?$_GET["file"]:'';
$modulepart = isset($_GET["modulepart"])?$_GET["modulepart"]:'';
$urlsource = isset($_GET["urlsource"])?$_GET["urlsource"]:'';
$rootpath = isset($_GET["rootpath"])?$_GET["rootpath"]:'';

$langs->load("filemanager@filemanager");

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
    dol_syslog(__FILE__." Refused to deliver file ".$original_file);
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

if ($action == 'remove_file')   // Remove a file
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

if (dol_is_dir($original_file))
{
    $type='directory';
}
else
{
    // Define mime type
    $type = 'application/octet-stream';
    if (! empty($_GET["type"]) && $_GET["type"] != 'auto') $type=$_GET["type"];
    else $type=dol_mimetype($original_file,'text/plain');
    //print 'X'.$type.'-'.$original_file;exit;
}

clearstatcache();

$filename = basename($original_file);

print '<!-- TYPE='.$type.' -->'."\n";
print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

// Output file on browser
dol_syslog("document.php download $original_file $filename content-type=$type");
$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset

// This test if file exists should be useless. We keep it to find bug more easily
if (! file_exists($original_file_osencoded))
{
    dol_print_error(0,$langs->trans("ErrorFileDoesNotExists",$original_file));
    exit;
}

// Les drois sont ok et fichier trouve, et fichier texte, on l'envoie
print '<b><font class="liste_titre">'.$langs->trans("Information").'</font></b><br>';
print '<hr>';

// Dir
if ($type == 'directory')
{

    print $langs->trans("FullPath").': '.$original_file_osencoded.'<br>';
    //print $langs->trans("Mime-type").': '.$type.'<br>';

    $info=stat($original_file_osencoded);
    //print '<br>'."\n";
    //print $langs->trans("Owner").": ".$info['udi']."<br>\n";
    //print $langs->trans("Group").": ".$info['gdi']."<br>\n";
    //print $langs->trans("Size").": ".dol_print_size($info['size'])."<br>\n";
    print $langs->trans("DateLastAccess").": ".dol_print_date($info['atime'],'%Y-%m-%d %H:%M:%S')."<br>\n";
    print $langs->trans("DateLastChange").": ".dol_print_date($info['mtime'],'%Y-%m-%d %H:%M:%S')."<br>\n";
    //print $langs->trans("Ctime").": ".$info['ctime']."<br>\n";


    print '<br><br>';
    print '<b>'.$langs->trans("Content")."</b><br>\n";
    print '<hr><br>';

    print '<div class="filedirelem"><ul class="filedirelem">'."\n";

    // Return content of dir
    $dircontent=dol_dir_list($original_file,'all',0,'','','name',SORT_ASC,0);
    foreach($dircontent as $key => $val)
    {
        if (dol_is_dir($val['name'])) $mimeimg='other.png';
        else $mimeimg=dol_mimetype($val['name'],'application/octet-stream',2);

        print '<li class="filedirelem">';
        print '<br><br>';
        print '<img src="'.DOL_URL_ROOT.'/theme/common/mime/'.$mimeimg.'"><br>';
        print dol_nl2br(dol_trunc($val['name'],24,'wrap'),1);
        print '</li>'."\n";
    }

    print '</ul></div>'."\n";
}
else {
    print $langs->trans("FullPath").': '.$original_file_osencoded.'<br>';
    print $langs->trans("Mime-type").': '.$type.'<br>';

    $info=stat($original_file_osencoded);
    //print '<br>'."\n";
    //print $langs->trans("Owner").": ".$info['udi']."<br>\n";
    //print $langs->trans("Group").": ".$info['gdi']."<br>\n";
    print $langs->trans("Size").": ".dol_print_size($info['size'])."<br>\n";
    print $langs->trans("DateLastAccess").": ".dol_print_date($info['atime'],'%Y-%m-%d %H:%M:%S')."<br>\n";
    print $langs->trans("DateLastChange").": ".dol_print_date($info['mtime'],'%Y-%m-%d %H:%M:%S')."<br>\n";
    //print $langs->trans("Ctime").": ".$info['ctime']."<br>\n";


    // Flush content before preview generation
    flush();    // This send all data to browser. Browser however may wait to have message complete or aborted before showing it.


    // File
    if (preg_match('/text/i',$type))
    {
        // Define memmax (memory_limit in bytes)
        $memmaxorig=@ini_get("memory_limit");
        $memmax=@ini_get("memory_limit");
        if ($memmaxorig != '')
        {
            preg_match('/([0-9]+)([a-zA-Z]*)/i',$memmax,$reg);
            if ($reg[2])
            {
                if (strtoupper($reg[2]) == 'M') $memmax=$reg[1]*1024*1024;
                if (strtoupper($reg[2]) == 'K') $memmax=$reg[1]*1024;
            }
        }


        $out='';
        $srclang=dol_mimetype($original_file,'text/plain',3);

        if (preg_match('/html/i',$type))
        {
            print '<br><br>';
            print '<b>'.$langs->trans("Preview")."</b><br>\n";
            print '<hr>';

            readfile($original_file_osencoded);
            //$out=file_get_contents($original_file_osencoded);
            //print $out;
        }
        else
        {
            $warn='';

            // Check if enouch memory for Geshi
            $minmem=64;
            if ($memmax < $minmem*1024*1024)
            {
                $warn=img_warning().' '.$langs->trans("NotEnoughMemoryForSyntaxColor");
                $srclang='';    // We disable geshi
            }

            if (! empty($srclang))
            {
                print '<br><br>';
                print '<b>'.$langs->trans("Preview")."</b> (".$srclang.")<br>\n";
                print '<hr>';

                // Translate with Geshi
                include_once('inc/geshi/geshi.php');

                $res='';
                $out=file_get_contents($original_file_osencoded);
                if ($srclang=='php') $srclang='php-brief';
                $geshi = new GeSHi($out, $srclang);
                $geshi->enable_strict_mode(false);
                $res=$geshi->parse_code();

                print $res;
            }
            else
            {
                print '<br><br>';
                print '<b>'.$langs->trans("Preview")."</b>";
                if ($warn) print ' '.$warn;
                print "<br>\n";
                print '<hr>';

                $maxsize=4096;
                $maxlines=25;
                $i=0;$more=0;
                $handle = fopen($original_file_osencoded, "r");
                if ($handle)
                {
                    while (!feof($handle) && $i < $maxlines) {
                        $buffer = fgets($handle, $maxsize);
                        $out.=dol_htmlentities($buffer,ENT_COMPAT,'UTF-8')."<br>\n";
                        $i++;
                    }
                    if (!feof($handle)) $more=1;
                }
                fclose($handle);

                print $out;

                print '<br>';

                if ($more)
                {
                    print '<b>...'.$langs->trans("More").'...</b><br>'."\n";
                }
            }
        }
    }
    else if (preg_match('/image/i',$type))
    {
        print '<br><br>';
        print '<b>'.$langs->trans("Preview")."</b><br>\n";
        print '<hr><br>';

        print '<center><img src="'.DOL_URL_ROOT.'/filemanager/viewimage.php?modulepart=filemanager&file='.urlencode($original_file).'"></center>';
    }
    else
    {
        print '<br><br>';
        print '<b>'.$langs->trans("Preview")."</b><br>\n";
        print '<hr>';

        print $langs->trans("PreviewNotAvailableForThisType");
    }
}


?>
