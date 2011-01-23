<?php
/* Copyright (C) 2006      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2007-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       	scripts/cron/batch_fournisseur_updateturnover.php
 *	\ingroup    	fournisseur
 *	\brief      	Update table Calcul le CA genere par chaque fournisseur et met a jour les tables fournisseur_ca et produit_ca
 *	\deprecated		Ce script et ces tables ne sont pas utilisees car graph generes dynamiquement maintenant.
 *	\version		$Id: batch_fournisseur_updateturnover.php,v 1.3 2011/01/23 01:05:41 eldy Exp $
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
$version='$Revision: 1.3 $';
$error=0;


/*
 if (! isset($argv[1]) || ! $argv[1]) {
 print "Usage: $script_file now\n";
 exit;
 }
 */

// Recupere env dolibarr
$version='$Revision: 1.3 $';
$path=preg_replace('/'.$script_file.'/','',$_SERVER["PHP_SELF"]);

require_once($path."../../htdocs/master.inc.php");
require_once(DOL_DOCUMENT_ROOT."/cron/functions_cron.lib.php");

print '***** '.$script_file.' ('.$version.') *****'."\n";
print '--- start'."\n";

$error=0;
$verbose = 0;

$now = gmmktime();
$year = strftime('%Y',$now);

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
	if ($argv[$i] == "-y")
	{
		$year = $argv[$i+1];
	}
}


$db->begin();

$result=batch_fournisseur_updateturnover($year);

if ($result > 0)
{
	$db->commit();
	print '--- end ok'."\n";
}
else
{
	print '--- end error code='.$result."\n";
	$db->rollback();
}

?>
