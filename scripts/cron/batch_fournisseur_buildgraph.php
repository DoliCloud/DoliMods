<?php
/* Copyright (C) 2007-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       	scripts/cron/batch_fournisseur_buildgraph.php
 *	\ingroup    	fournisseur
 *	\brief      	Script de generation graph ca fournisseur depuis tables fournisseur_ca
 *	\deprecated	Ces graph ne sont pas utilises car sont generes dynamiquement maintenant.
 *	\version		$Id: batch_fournisseur_buildgraph.php,v 1.5 2011/08/16 11:03:06 eldy Exp $
 */

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
    echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
    exit;
}

// Global variables
$version='$Revision: 1.5 $';
$error=0;
// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include($path."../../master.inc.php");
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include($path."../../htdocs/master.inc.php");
if (! $res && file_exists("../master.inc.php")) $res=@include("../master.inc.php");
if (! $res && file_exists("../../master.inc.php")) $res=@include("../../master.inc.php");
if (! $res && file_exists("../../../master.inc.php")) $res=@include("../../../master.inc.php");
if (! $res && file_exists($path."../../../dolibarr/htdocs/master.inc.php")) $res=@include($path."../../../dolibarr/htdocs/master.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../dolibarr/htdocs/master.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../../dolibarr/htdocs/master.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/master.inc.php")) $res=@include("../../../../../dolibarr/htdocs/master.inc.php");   // Used on dev env only
if (! $res) die("Include of master fails");
require_once(DOL_DOCUMENT_ROOT."/core/dolgraph.class.php");
require_once(DOL_DOCUMENT_ROOT."/cron/functions_cron.lib.php");

print '***** '.$script_file.' ('.$version.') *****'."\n";
print '--- start'."\n";


$error=0;
$verbose = 0;

for ($i = 1 ; $i < sizeof($argv) ; $i++)
{
	if ($argv[$i] == "-v")
	{
		$verbose = 1;
	}
	if ($argv[$i] == "-vv")
	{
		$verbose = 2;
	}
	if ($argv[$i] == "-vvv")
	{
		$verbose = 3;
	}
}

$dir = $conf->fournisseur->dir_temp;
$result=create_exdir($dir);
if ($result < 0)
{
	dol_print_error('','Failed to create dir '.$dir);
	exit;
}



$sql  = "SELECT distinct(fk_societe)";
$sql .= " FROM ".MAIN_DB_PREFIX."fournisseur_ca";

$resql = $db->query($sql) ;
$fournisseurs = array();
if ($resql)
{
	while ($row = $db->fetch_row($resql))
	{
		$fdir = $dir.'/'.get_exdir($row[0],3);

		if ($verbose) print $fdir."\n";

		//print 'Create fdir='.$fdir;
		$result=create_exdir($fdir);

		$fournisseurs[$row[0]] = $fdir;
	}
	$db->free($resql);
}
else
{
	print $sql;
}



foreach ($fournisseurs as $id => $fdir)
{
	$values_gen = array();
	$values_ach = array();
	$legends = array();
	$sql  = "SELECT year, ca_genere, ca_achat";
	$sql .= " FROM ".MAIN_DB_PREFIX."fournisseur_ca";
	$sql .= " WHERE fk_societe = $id";
	$sql .= " ORDER BY year ASC";

	$resql = $db->query($sql) ;

	if ($resql)
	{
		$i = 0;
		while ($row = $db->fetch_row($resql))
		{
		  $values_gen[$i]  = $row[1];
		  $values_ach[$i]  = $row[2];
		  $legends[$i] = $row[0];

		  $i++;
		}
		$db->free($resql);
	}
	else
	{
		print $sql;
	}

	$graph = new DolGraph();

	$file = $fdir ."ca_genere-".$id.".png";
	$title = "CA genere par ce fournisseur (euros HT)";

	$graph->SetTitle($title);
	$graph->BarAnnualArtichow($file, $values_ach, $legends);

	if ($verbose)
	print "$file\n";

	$file = $fdir ."ca_achat-".$id.".png";
	$title = "Charges pour ce fournisseur (euros HT)";

	$graph->SetTitle($title);
	$graph->BarAnnualArtichow($file, $values_ach, $legends);

	if ($verbose)
	print "$file\n";
}


if (! $error)
{
	print '--- end ok'."\n";
}
else
{
	print '--- end error code='.$error."\n";
}

?>
