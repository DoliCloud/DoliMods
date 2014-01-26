<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *	    \file       htdocs/cabinetmed/admin/about.php
 *      \ingroup    cabinetmed
 *      \brief      Page about
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");


if (!$user->admin) accessforbidden();


$langs->load("admin");
$langs->load("other");
$langs->load("cabinetmed@cabinetmed");


/**
 * View
 */

$help_url='';
llxHeader('','',$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("CabinetMedSetup"),$linkback,'setup');
print '<br>';

$h=0;
$head[$h][0] = 'admin.php';
$head[$h][1] = $langs->trans("Setup");
$head[$h][2] = 'tabsetup';
$h++;

$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;

dol_fiche_head($head, 'tababout', '');

print $langs->trans("AboutInfo").'<br>';
print '<br>';
//$url='http://www.nltechno.com';
//print '<a href="'.$url.'" target="_blank"><img border="0" width="60" src="../img/nltechno.gif"></a><br><br>';
print '<br>';

print $langs->trans("MoreModules").'<br>';
print '&nbsp; &nbsp; &nbsp; '.$langs->trans("MoreModulesLink").'<br>';
$url='http://www.dolistore.com/search.php?search_query=nltechno';
print '<a href="'.$url.'" target="_blank"><img border="0" width="180" src="'.DOL_URL_ROOT.'/theme/dolistore_logo.png"></a><br><br><br>';

print '<br>';
print $langs->trans("MoreCloudHosting").'<br>';
print '&nbsp; &nbsp; &nbsp; '.$langs->trans("MoreCloudHostingLink").'<br>';
$url='http://www.dolicloud.com';
print '<a href="'.$url.'" target="_blank"><img border="0" width="180" src="../img/dolicloud_logo.png"></a><br><br><br>';

print '<br>';
print $langs->trans("CompatibleWithDoliDroid").'<br>';
$url='https://play.google.com/store/apps/details?id=com.nltechno.dolidroidpro';
print '<a href="'.$url.'" target="_blank"><img border="0" width="180" src="../img/dolidroid_512x512_en.png"></a><br><br>';

print '<br>';

dol_fiche_end();


llxFooter();

$db->close();
?>