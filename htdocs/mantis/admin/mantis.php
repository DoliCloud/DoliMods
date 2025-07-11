<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
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
 *
 */

/**
 *   \file       htdocs/mantis/admin/mantis.php
 *   \ingroup    mantis
 *   \brief      Page de configuration du module mantis
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
dol_include_once('/mantis/class/mantis.class.php');


if (!$user->admin) accessforbidden();


$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

// Sauvegardes parametres
if ($actionsave) {
	$i=0;

	$db->begin();

	$i+=dolibarr_set_const($db, 'PHPMANTIS_URL', trim($_POST["phpmantis_url"]), 'chaine', 0, '', $conf->entity);
	$i+=dolibarr_set_const($db, 'PHPMANTIS_HOST', trim($_POST["phpmantis_host"]), 'chaine', 0, '', $conf->entity);
	$i+=dolibarr_set_const($db, 'PHPMANTIS_DBNAME', trim($_POST["phpmantis_dbname"]), 'chaine', 0, '', $conf->entity);
	$i+=dolibarr_set_const($db, 'PHPMANTIS_USER', trim($_POST["phpmantis_user"]), 'chaine', 0, '', $conf->entity);
	$i+=dolibarr_set_const($db, 'PHPMANTIS_PASS', trim($_POST["phpmantis_pass"]), 'chaine', 0, '', $conf->entity);

	if ($i >= 5) {
		$db->commit();
		$mesg = "<font class=\"ok\">".$langs->trans("MantisSetupSaved")."</font>";
	} else {
		$db->rollback();
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
} elseif ($actiontest) {
	//$resql=$db->query("select count(*) from llx_const");
	//print "< ".$db." - ".$db->db." - ".$resql." - ".$db->error()."><br>\n";

	// Test de la connexion a la database mantis
	$conf->mantis->db->type=$dolibarr_main_db_type;
	$conf->mantis->db->host=$_POST["phpmantis_host"];
	$conf->mantis->db->port=$_POST["phpmantis_port"];
	$conf->mantis->db->user=$_POST["phpmantis_user"];
	$conf->mantis->db->pass=$_POST["phpmantis_pass"];
	$conf->mantis->db->name=$_POST["phpmantis_dbname"];

	$mantis=new Mantis();

	//print "D ".$db." - ".$db->db."<br>\n";
	//print "W ".$mantis->localdb." - ".$mantis->localdb->db."<br>\n";

	if ($mantis->localdb->connected == 1 && $mantis->localdb->database_selected == 1) {
		// V�rifie si bonne base
		$sql="SELECT value FROM mantis_config_table WHERE config_id='database_version'";
		$resql=$mantis->localdb->query($sql);
		if ($resql) {
			$mesg ="<div class=\"ok\">";
			$mesg.=$langs->trans("MantisTestOk", $_POST["phpmantis_host"], $_POST["phpmantis_dbname"], $_POST["phpmantis_user"]);
			$mesg.="</div>";
		} else {
			$mesg ="<div class=\"error\">";
			$mesg.=$langs->trans("MantisErrorConnectOkButWrongDatabase");
			$mesg.="</div>";
		}

		//$mantis->localdb->close();    Ne pas fermer car la conn de mantis est la meme que dolibarr si parametre host/user/pass identique
	} elseif ($mantis->connected == 1 && $mantis->database_selected != 1) {
		$mesg ="<div class=\"error\">".$langs->trans("MantisTestKo1", $_POST["phpmantis_host"], $_POST["phpmantis_dbname"]);
		$mesg.="<br>".$mantis->localdb->error();
		$mesg.="</div>";
		//$mantis->localdb->close();    Ne pas fermer car la conn de mantis est la meme que dolibarr si parametre host/user/pass identique
	} else {
		$mesg ="<div class=\"error\">".$langs->trans("MantisTestKo2", $_POST["phpmantis_host"], $_POST["phpmantis_user"]);
		$mesg.="<br>".$mantis->localdb->error();
		$mesg.="</div>";
	}

	//$resql=$db->query("select count(*) from llx_const");
	//print "< ".$db." - ".$db->db." - ".$resql." - ".$db->error()."><br>\n";
}


/**
 * Affichage du formulaire de saisie
 */

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MantisSetup"), $linkback, 'setup');
print '<br>';


print '<form name="phpmantisconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("MantisURL")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"phpmantis_url\" value=\"". ($_POST["phpmantis_url"]?$_POST["phpmantis_url"]:$conf->global->PHPMANTIS_URL) . "\" size=\"40\"></td>";
print "<td>http://localhost/mantis/";
print "<br>https://mantisserver/";
print "</td>";
print "</tr>";

print '<tr class="oddeven">';
print "<td>".$langs->trans("MantisServer")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"phpmantis_host\" value=\"". ($_POST["phpmantis_host"]?$_POST["phpmantis_host"]:$conf->global->PHPMANTIS_HOST) . "\" size=\"30\"></td>";
print "<td>localhost";
//print "<br>__dolibarr_main_db_host__ <i>(".$dolibarr_main_db_host.")</i>"
print "</td>";
print "</tr>";

print '<tr class="oddeven">';
print "<td>".$langs->trans("MantisDatabaseName")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"phpmantis_dbname\" value=\"". ($_POST["phpmantis_dbname"]?$_POST["phpmantis_dbname"]:$conf->global->PHPMANTIS_DBNAME) . "\" size=\"30\"></td>";
print "<td>bugtracker";
//print "<br>__dolibarr_main_db_name__ <i>(".$dolibarr_main_db_name.")</i>";
print "</td>";
print "</tr>";

print '<tr class="oddeven">';
print "<td>".$langs->trans("MantisUser")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"phpmantis_user\" value=\"". ($_POST["phpmantis_user"]?$_POST["phpmantis_user"]:$conf->global->PHPMANTIS_USER) . "\" size=\"30\"></td>";
print "<td>mantis";
//print "<br>__dolibarr_main_db_user__ <i>(".$dolibarr_main_db_user.")</i>";
print "</td>";
print "</tr>";

print '<tr class="oddeven">';
print "<td>".$langs->trans("Password")."</td>";
print "<td><input type=\"password\" class=\"flat\" name=\"phpmantis_pass\" value=\"" . ($_POST["phpmantis_pass"]?$_POST["phpmantis_pass"]:$conf->global->PHPMANTIS_PASS) . "\" size=\"30\"></td>";
print '<td>';
//if ($dolibarr_main_db_pass) print '__dolibarr_main_db_pass__ <i>('.preg_replace('/./i','*',$dolibarr_main_db_pass).')</i>';
print '&nbsp;</td>';
print "</tr>";

print "</table>";


print '<br><center>';
print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


clearstatcache();

if ($mesg) print "<br>$mesg<br>";
print "<br>";

llxFooter();

$db->close();
