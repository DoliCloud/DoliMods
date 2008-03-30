<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 Eric Seigne          <eric.seigne@ryxeo.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
	\file       htdocs/thomsonphonebook.php
    \ingroup    thomsonphonebook
	\brief      Recherche dans l'annuaire pour les telephones SIP Thomson
				You configure your phones to call URL
				http://mydolibarr/thomsonphonebook/thomsonphonebook.php?search=...
	\version    $Revision: 1.1 $
*/
 
$res=@include("../master.inc.php");
if (! $res) @include("../../../dolibarr/htdocs/master.inc.php");	// Used on dev env only

$search=isset($_GET["search"])?$_GET["search"]:$_POST["search"];


// Check parameters
if (! $search)
{
	dolibarr_print_error($db,'Parameter "search" not provided');
	exit;
}



$sql = "select p.name,p.firstname,p.phone from llx_socpeople as p,llx_societe as s WHERE p.fk_soc=s.rowid AND (p.name LIKE '%$search' OR p.firstname LIKE '%$search');";
//print $req;
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
		print($obj->name.", ".$obj->firstname );
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

?>
