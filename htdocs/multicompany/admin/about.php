<?php
/* Copyright (C) 2011-2015 Regis Houssin  <regis.houssin@capnetworks.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		/multicompany/admin/about.php
 * 	\ingroup	multicompany
 * 	\brief		About Page
 */

$res=@include("../../main.inc.php");					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../main.inc.php");		// For "custom" directory


// Libraries
require_once("../lib/multicompany.lib.php");
require_once("../lib/PHP_Markdown/markdown.php");


// Translations
$langs->load("multicompany@multicompany");

// Access control
if (!$user->admin)
	accessforbidden();

/*
 * View
 */

llxHeader('', $langs->trans("Module5000Name"));

// Subheader
$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MultiCompanySetup"), $linkback, 'multicompany@multicompany');

print '<br>';

// Configuration header
$head = multicompany_prepare_head();
dol_fiche_head($head, 'about', $langs->trans("Module5000Name"));

// About page goes here

print '<br>';

$buffer = file_get_contents(dol_buildpath('/multicompany/README.md',0));
print Markdown($buffer);

print '<br>';
print $langs->trans("MulticompanyMoreModules").'<br>';
$url='https://www.inodbox.com/';
print '<a href="'.$url.'" target="_blank"><img border="0" width="250" src="'.dol_buildpath('/dcloud/img/inodbox.png',1).'"></a>';
print '<br><br><br>';

print '<a target="_blank" href="'.dol_buildpath('/multicompany/COPYING',1).'"><img src="'.dol_buildpath('/multicompany/img/gplv3.png',1).'"/></a>';

llxFooter();

$db->close();
