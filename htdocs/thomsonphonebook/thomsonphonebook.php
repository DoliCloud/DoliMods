<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008      Eric Seigne          <eric.seigne@ryxeo.com>
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
 *	\file       htdocs/thomsonphonebook/thomsonphonebook.php
 *  \ingroup    thomsonphonebook
 *	\brief      Recherche dans l'annuaire pour les telephones SIP Thomson
 *				You configure your phones to call URL
 *				http://mydolibarr/thomsonphonebook/thomsonphonebook.php?search=...
 */

define('NOCSRFCHECK',1);
define('NOLOGIN',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

$search=GETPOST("search");



/*
 * View
 */

// Check parameters
if (empty($search) && $search == '')
{
	dol_print_error($db,'Parameter "search" not provided');
	exit;
}


if (empty($conf->thomsonphonebook->enabled))
{
	dol_print_error($db,'Module was not enabled');
    exit;
}

//$sql = "select p.name,p.firstname,p.phone from llx_socpeople as p,llx_societe as s WHERE p.fk_soc=s.rowid AND (p.name LIKE '%$search' OR p.firstname LIKE '%$search');";
$sql = "select p.lastname,p.firstname,p.phone from llx_socpeople as p,llx_societe as s WHERE p.fk_soc=s.rowid AND (p.lastname LIKE '".$db->escape($search)."%' OR p.firstname LIKE '".$db->escape($search)."%')";
if (! empty($conf->global->THOMSONPHONEBOOK_DOSEARCH_ANYWHERE)) $sql = "select p.lastname,p.firstname,p.phone from llx_socpeople as p,llx_societe as s WHERE p.fk_soc=s.rowid AND (p.lastname LIKE '%".$db->escape($search)."%' OR p.firstname LIKE '%".$db->escape($search)."%')";

//print $sql;
dol_syslog("thomsonphonebook sql=".$sql);
$resql=$db->query($sql);
if ($resql)
{
	$num=$db->num_rows($resql);
	$i = 0;
	print("<ThomsonPhoneBook>\n");
	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);
		//debug
		//var_dump($obj);
		print("<DirectoryEntry>\n");
		print("\t<Name>");
		print($obj->lastname.", ".$obj->firstname );
		print("</Name>\n");
		print("\t<Telephone>");
		print($obj->phone);
		print("</Telephone>\n");
		print("</DirectoryEntry>\n");
		$i++;
	}
	print("</ThomsonPhoneBook>\n");
	$db->free($result);
}
else dol_print_error($db);

$db->close();
?>