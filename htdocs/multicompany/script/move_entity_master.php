#!/usr/bin/php
<?php
/* Copyright (C) 2015	Regis Houssin <regis.houssin@cap-networks.com>
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
 */

/**
 *      \file       multicompany/script/replace_entity_matser.php
 *		\ingroup    multicompany
 *      \brief      Script to replace master entity by one passed in arg
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
$version='1';
$error=0;

// Include Dolibarr environment
echo $path."../..//master.inc.php\n";
require_once $path."../../master.inc.php";

// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs->load("main");				// To load language file for default language
@set_time_limit(0);					// No timeout for this script

// Load user and its permissions
//$result=$user->fetch('','inoveroot');	// Load user for login 'admin'. Comment line to run as anonymous user.
//if (! $result > 0) { dol_print_error('',$user->error); exit; }
//$user->getrights();


print "***** ".$script_file." (".$version.") *****\n";
if (! isset($argv[1])) {	// Check parameters
    print "Usage: ".$script_file." <entity_id> ...\n";
    exit;
}
print '--- start'."\n";
print 'entity_id='.$argv[1]."\n";

$entity_new_master = $argv[1];
$entity_temp = 999;

// Start of transaction
$db->begin();

// Eviter les contraintes
$sql = "SET foreign_key_checks = 0";
$resql=$db->query($sql);

$list_tables = $db->DDLListTables($db->database_name);

$pattern = '/(user|usergroup|usergroup\_user|user\_param|rights\_def)+/';

if(is_array($list_tables))
{
	// Pour chaque table : vérif si présence d'un champ entity
	foreach($list_tables as $table)
	{
		if (!preg_match($pattern, $table))
		{
			$list_champ = $db->DDLInfoTable($table);
			foreach ($list_champ as $key => $champ)
			{
				if(is_array($champ) && in_array('entity',$champ))
				{
					print 'Traitement de la table '.$table.'... '."\n";

					 // Entity master become a temp entity
					$sql = "UPDATE " . $table;
					$sql.= " SET entity = " . $entity_temp;
					$sql.= " WHERE entity = 1";

					dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
					$resql=$db->query($sql);
					if ($resql)
					{
						// Entity in param become new master
						$sql = "UPDATE " . $table;
						$sql.= " SET entity = 1";
						$sql.= " WHERE entity = " . $entity_new_master;

						dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
						$resql=$db->query($sql);
						if ($resql)
						{
							// Old entity master become entity in param
							$sql = "UPDATE " . $table;
							$sql.= " SET entity = " . $entity_new_master;
							$sql.= " WHERE entity = " . $entity_temp;

							dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
							$resql=$db->query($sql);
							if ($resql)
							{
								print "... [OK]\n;";
							}
							else
							{
								print "... [KO !!]\n;";
								$error++;
								$errorcode = dol_print_error($db);
							}
						}
						else
						{
							print "... [KO !!]\n;";
							$error++;
							$errorcode = dol_print_error($db);
						}
					}
					else
					{
						print "... [KO !!]\n;";
						$error++;
						$errorcode = dol_print_error($db);
					}
				}
			}
		}
	}

	print 'Déplacement de l\'entité maitre dans la config du module multicompany... ';
	// Move master entity and replace by new
	$sql = "UPDATE " . MAIN_DB_PREFIX . "entity";
	$sql.= " SET rowid = " . $entity_temp;
	$sql.= " WHERE rowid = 1";

	$sql2 = "UPDATE " . MAIN_DB_PREFIX . "entity";
	$sql2.= " SET rowid = 1";
	$sql2.= " WHERE rowid = " . $entity_new_master;

	$sql3 = "UPDATE " . MAIN_DB_PREFIX . "entity";
	$sql3.= " SET rowid = " . $entity_new_master;
	$sql3.= " WHERE rowid = " . $entity_temp;

	dol_syslog($script_file." sql=".$sql." sql2=".$sql2." sql3=".$sql3, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$resql2=$db->query($sql2);
		if ($resql2)
		{
			$resql3=$db->query($sql3);
			if ($resql3)
			{
				print "... [OK]\n;";
			}
			else
			{
				print "... SQL 3 [KO !!]\n;";
				$error++;
				$errorcode = dol_print_error($db);
			}
		}
		else
		{
			print "... SQL 2 [KO !!]\n;";
			$error++;
			$errorcode = dol_print_error($db);
		}
	}
	else
	{
		print "... SQL 1 [KO !!]\n;";
		$error++;
		$errorcode = dol_print_error($db);
	}

}

// Put option as origin
$sql = "SET foreign_key_checks = 1";
$resql=$db->query($sql);


// -------------------- END OF YOUR CODE --------------------

if (! $error)
{
	$db->commit();
	print '--- end ok'."\n";
}
else
{
	print '--- end error code='.$errorcode."\n";
	$db->rollback();
}

$db->close();	// Close database opened handler

return $error;
?>
