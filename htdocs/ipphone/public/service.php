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
 *	\file       htdocs/public/service.php
 *  \ingroup    ipphone
 *	\brief      Recherche dans l'annuaire pour les telephones SIP Thomson
 *				You configure your phones to call URL
 *				http://mydolibarr/ipphone/public/service.php?search=...
 */

define('NOCSRFCHECK',1);
define('NOLOGIN',1);

// C'est un wrapper, donc header vierge
/**
 * Header function
 *
 * @return	void
 */
function llxHeaderVierge() { print ''; }
/**
 * Footer function
 *
 * @return	void
 */
function llxFooterVierge() { print ''; }


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

$search=GETPOST("search");
$key=GETPOST("key");

if (empty($conf->ipphone->enabled)) accessforbidden('',1,1,1);

$langs->load("ipphone@ipphone");



/*
 * View
 */

if (! empty($conf->global->IPPHONE_EXPORTKEY))
{
	// Protection is on, we check key
	if ($key != $conf->global->IPPHONE_EXPORTKEY)
	{
		$user->getrights();

		llxHeaderVierge();
		print '<div class="error">Bad value for key.</div>';
		llxFooterVierge();
		exit;
	}
}

// Check parameters
/*if (empty($search) && $search == '')
{
	dol_print_error($db,'Parameter "search" not provided');
	exit;
}
*/




header("Content-type: text/xml");
header("Connection: close");
header("Expires: -1");

//$sql = "select p.name,p.firstname,p.phone from llx_socpeople as p,llx_societe as s WHERE p.fk_soc=s.rowid AND (p.name LIKE '%$search' OR p.firstname LIKE '%$search');";
$sql = "select s.rowid, s.nom as name, s.phone, p.rowid as contactid, p.lastname, p.firstname, p.phone as contactphone, phone_mobile as contactphonemobile";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s LEFT JOIN ".MAIN_DB_PREFIX."socpeople as p ON p.fk_soc = s.rowid";
$sql.= " WHERE p.rowid IS NULL or (p.lastname LIKE '".$db->escape($search)."%' OR p.firstname LIKE '".$db->escape($search)."%')";

//if (! empty($conf->global->THOMSONPHONEBOOK_DOSEARCH_ANYWHERE)) $sql = "select p.lastname,p.firstname,p.phone from llx_socpeople as p,llx_societe as s WHERE p.fk_soc=s.rowid AND (p.lastname LIKE '%".$db->escape($search)."%' OR p.firstname LIKE '%".$db->escape($search)."%')";


$phonetag='CiscoIPPhone';	// May be also 'Thompson'
$thirdpartyadded=array();

//print $sql;
dol_syslog("ipphone sql=".$sql);
$resql=$db->query($sql);
if ($resql)
{
	$num=$db->num_rows($resql);
	$i = 0;
	print("<".$phonetag."Directory>\n");
	print("<Title>Dolibarr Directory</Title>\n");
	print("<Prompt>".dolXMLEncodeipphone($langs->transnoentitiesnoconv("SelectTheUser"))."</Prompt>\n");

	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);
		//debug
		//var_dump($obj);
		if ($obj->phone || (! empty($conf->global->IPPHONE_SHOW_NO_PHONE) && (empty($obj->contactid) || (empty($obj->contactphone) && empty($obj->contactphonemobile)))))
		{
			// Record for thirdparty (only if not already output)
			if (empty($thirdpartyadded[$obj->rowid]))
			{
				print "<DirectoryEntry>\n";
				print "\t<Name>";
				print $obj->rowid.'/'.$obj->contactid.' ';
				print dolXMLEncodeipphone($obj->name);
				print "</Name>\n";
				print "\t<Telephone>";
				print dolXMLEncodeipphone($obj->phone);
				print "</Telephone>\n";
				print "</DirectoryEntry>\n";
			}
			$thirdpartyadded[$obj->rowid]=1;
		}
		if ($obj->contactphone)
		{
			print "<DirectoryEntry>\n";
			print "\t<Name>";
			print $obj->rowid.'/'.$obj->contactid.' ';
			print dolXMLEncodeipphone($obj->name." - ".dolGetFirstLastname($obj->firstname,$obj->lastname));
			print "</Name>\n";
			print "\t<Telephone>";
			print dolXMLEncodeipphone($obj->contactphone);
			print "</Telephone>\n";
			print "</DirectoryEntry>\n";
		}
		if ($obj->contactphonemobile)
		{
			print "<DirectoryEntry>\n";
			print "\t<Name>";
			print $obj->rowid.'/'.$obj->contactid.' ';
			print dolXMLEncodeipphone($obj->name." - ".dolGetFirstLastname($obj->firstname,$obj->lastname));
			print "</Name>\n";
			print "\t<Telephone>";
			print dolXMLEncodeipphone($obj->contactphonemobile);
			print "</Telephone>\n";
			print "</DirectoryEntry>\n";
		}

		$i++;
	}

/*
 			print "<DirectoryEntry>\n";
			print "\t<Name>";
			print 'eee';
			print "</Name>\n";
			print "\t<Telephone></Telephone>\n";
			print "</DirectoryEntry>\n";
*/

	print("</".$phonetag."Directory>\n");
	$db->free($resql);
}
else dol_print_error($db);

$db->close();


/**
 * Encode string for xml usage
 *
 * @param 	string	$string		String to encode
 * @return	string				String encoded
 */
function dolXMLEncodeipphone($string)
{
	return strtr($string, array('\''=>'&apos;','"'=>'&quot;','&'=>'&amp;','<'=>'&lt;','>'=>'&gt;'));
}
