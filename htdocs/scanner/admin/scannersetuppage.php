<?php
/* Copyright (C) 20010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/scanner/admin/scanneradminsetuppage.php
 *      \ingroup    scanner
 *      \brief      Page de configuration du module Scanner
 *		\version    $Id: scannersetuppage.php,v 1.5 2011/03/29 23:17:21 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");


if (!$user->admin)
    accessforbidden();


$langs->load("admin");
$langs->load("scanner@scanner");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

// Save parameters
if ($actionsave)
{
    $error=0;
	$i=0;

    $db->begin();

    /*if (! preg_match('|[\\\/]$|',$_POST["xxx"]))
    {
    	$mesg="<div class=\"error\">".$langs->trans("ErrorAWStatsDataDirMustEndWithASlash")."</div>";
    	$error++;
    }*/

    if (! $error)
    {
	    if ($i >= 0) $i+=dolibarr_set_const($db,'PHPSANE_SCANIMAGE',trim($_POST["PHPSANE_SCANIMAGE"]),'chaine',0);
	    if ($i >= 0) $i+=dolibarr_set_const($db,'PHPSANE_PNMTOJPEG',trim($_POST["PHPSANE_PNMTOJPEG"]),'chaine',0);
	    if ($i >= 0) $i+=dolibarr_set_const($db,'PHPSANE_PNMTOTIFF',trim($_POST["PHPSANE_PNMTOTIFF"]),'chaine',0);
	    if ($i >= 0) $i+=dolibarr_set_const($db,'PHPSANE_OCR',trim($_POST["PHPSANE_OCR"]),'chaine',0);

	    if ($i >= 3)
	    {
	        $db->commit();
	        $mesg = "<div class=\"ok\">".$langs->trans("SetupSaved")."</div>";
	    }
	    else
	    {
	        $db->rollback();
	        $mesg=$db->lasterror();
	        //header("Location: ".$_SERVER["PHP_SELF"]);
	        //exit;
	    }
    }
}



/**
 * View
 */

$help_url='EN:Module_PHPSane_EN|FR:Module_PHPSane|ES:Modulo_PHPSane';
llxHeader('','Scanner',$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ScannerSetup"),$linkback,'setup');

print $langs->trans("ScannerDesc").'<br>';
print '<br>';

$os=PHP_OS;
if (! preg_match('/linux/i',$os))
{
	print '<div class="warning">Sorry this module can works only on Linux or linux like OS (need command line "scanner" tools).</div><br>';
}

print '<form name="phpsaneform" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print "<table class=\"noborder\" width=\"100%\" summary=\"parameters\">";
$var=true;

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("PHPSANE_SCANIMAGE")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"PHPSANE_SCANIMAGE\" value=\"". ($_POST["PHPSANE_SCANIMAGE"]?$_POST["PHPSANE_SCANIMAGE"]:$conf->global->PHPSANE_SCANIMAGE) . "\" size=\"50\"></td>";
print "<td>/usr/bin/scanimage";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("PHPSANE_PNMTOJPEG")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"PHPSANE_PNMTOJPEG\" value=\"". ($_POST["PHPSANE_PNMTOJPEG"]?$_POST["PHPSANE_PNMTOJPEG"]:$conf->global->PHPSANE_PNMTOJPEG) . "\" size=\"50\"></td>";
print "<td>/usr/bin/pnmtojpeg";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("PHPSANE_PNMTOTIFF")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"PHPSANE_PNMTOTIFF\" value=\"". ($_POST["PHPSANE_PNMTOTIFF"]?$_POST["PHPSANE_PNMTOTIFF"]:$conf->global->PHPSANE_PNMTOTIFF) . "\" size=\"50\"></td>";
print "<td>/usr/bin/pnmtotiff";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("PHPSANE_OCR")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"PHPSANE_OCR\" value=\"". ($_POST["PHPSANE_OCR"]?$_POST["PHPSANE_OCR"]:$conf->global->PHPSANE_OCR) . "\" size=\"50\"></td>";
print "<td>/usr/bin/gocr";
print "</td>";
print "</tr>";


print "</table>";
print "<br>";

print '<br><center>';
print "<input type=\"submit\"";
if (! preg_match('/linux/i',$os)) print ' disabled="disabled"';
print " name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


clearstatcache();

if ($mesg) print "<br>$mesg<br>";
print "<br>";

$db->close();

llxFooter('$Date: 2011/03/29 23:17:21 $ - $Revision: 1.5 $');
?>
