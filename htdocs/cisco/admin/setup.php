<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/cisco/admin/cisco.php
 *  \ingroup    cisco
 *	\brief      Page to setup module cisco
 *				You configure your phones to call URL
 *				http://mydolibarr/cisco/cisco.php?search=#SEARCH
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");


/*
 * View
 */

$help_url='EN:Module_ThomsonPhoneBook_EN|FR:Module_ThomsonPhoneBook|ES:M&oacute;dulo_ThomsonPhoneBook';

llxHeader('','',$help_url);

if (empty($conf->cisco->enabled))
{
	dol_print_error($db,'Module was not enabled');
    exit;
}

print "Module is enabled. To use it, you must setup your phone to call following URL:<br><br>\n";
$url=dol_buildpath('/cisco/cisco.php',1);
$url=DOL_MAIN_URL_ROOT.(preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'/', '', $url)).'?search=#SEARCH';
print '<a href="'.$url.'">'.$url."<br>\n";


llxFooter();

$db->close();
?>
