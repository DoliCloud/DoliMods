<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/awstats/admin/awstats.php
 *      \ingroup    awstats
 *      \brief      Page de configuration du module AWStats
 */

define('NOCSRFCHECK', 1);

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
dol_include_once("/awstats/lib/awstats.lib.php");	// We still use old writing to be compatible with old version


if (!$user->admin)
	accessforbidden();


$langs->load("admin");
$langs->load("awstats@awstats");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

// Save parameters
if ($actionsave) {
	$error=0;
	$i=0;

	$db->begin();

	if (! preg_match('|[\\\/]$|', $_POST["AWSTATS_DATA_DIR"])) {
		$mesg="<div class=\"error\">".$langs->trans("ErrorAWStatsDataDirMustEndWithASlash")."</div>";
		$error++;
	}

	if (! $error) {
		if ($i >= 0) $i+=dolibarr_set_const($db, 'AWSTATS_DATA_DIR', trim($_POST["AWSTATS_DATA_DIR"]), 'chaine', 0, '', $conf->entity);
		if ($i >= 0) $i+=dolibarr_set_const($db, 'AWSTATS_CGI_PATH', trim($_POST["AWSTATS_CGI_PATH"]), 'chaine', 0, '', $conf->entity);
		if ($i >= 0) $i+=dolibarr_set_const($db, 'AWSTATS_PROG_PATH', trim($_POST["AWSTATS_PROG_PATH"]), 'chaine', 0, '', $conf->entity);
		if ($i >= 0) $i+=dolibarr_set_const($db, 'AWSTATS_LIMIT_CONF', trim($_POST["AWSTATS_LIMIT_CONF"]), 'chaine', 0, '', $conf->entity);

		if ($i >= 3) {
			$db->commit();
			$mesg = "<div class=\"ok\">".$langs->trans("SetupSaved")."</div>";
		} else {
			$db->rollback();
			$mesg=$db->lasterror();
			//header("Location: ".$_SERVER["PHP_SELF"]);
			//exit;
		}
	}
}



/**
 * View
 */

$help_url='EN:Module_AWStats_EN|FR:Module_AWStats|ES:Modulo_AWStats';
llxHeader('', 'AWStats', $help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("AWStatsSetup"), $linkback, 'setup');
print '<br>';

$h=0;
$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("Setup");
$head[$h][2] = 'tabsetup';
$h++;

$head[$h][0] = 'about.php';
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;


print '<form name="awstatsform" action="'.$_SERVER["PHP_SELF"].'" method="post">';

dol_fiche_head($head, 'tabsetup', '', -1);


print "<table class=\"noborder centpercent\">";
$var=true;

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("AWSTATS_DATA_DIR")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_DATA_DIR\" value=\"". ($_POST["AWSTATS_DATA_DIR"]?$_POST["AWSTATS_DATA_DIR"]:$conf->global->AWSTATS_DATA_DIR) . "\" size=\"50\"></td>";
print "<td>/usr/local/awstats/data/<br>/var/lib/awstats/";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("AWSTATS_CGI_PATH")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_CGI_PATH\" value=\"". ($_POST["AWSTATS_CGI_PATH"]?$_POST["AWSTATS_CGI_PATH"]:$conf->global->AWSTATS_CGI_PATH) . "\" size=\"50\"></td>";
print "<td>http://myserver/awstats/awstats.pl<br>http://myserver/cgi-bin/awstats.pl?configdir=/home/awstats/conf";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("AWSTATS_PROG_PATH")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_PROG_PATH\" value=\"". ($_POST["AWSTATS_PROG_PATH"]?$_POST["AWSTATS_PROG_PATH"]:$conf->global->AWSTATS_PROG_PATH) . "\" size=\"50\"></td>";
print "<td>/usr/local/awstats/wwwroot/cgi-bin/awstats.pl<br>/usr/lib/cgi-bin/awstats.pl<br>c:\MyDir\awstats.pl";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("AWSTATS_LIMIT_CONF")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_LIMIT_CONF\" value=\"". ($_POST["AWSTATS_LIMIT_CONF"]?$_POST["AWSTATS_LIMIT_CONF"]:$conf->global->AWSTATS_LIMIT_CONF) . "\" size=\"50\"></td>";
print "<td>myconf1,myconf2";
print "</td>";
print "</tr>";

print "</table>";

dol_fiche_end();

print '<center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


clearstatcache();

dol_htmloutput_mesg($mesg);


llxFooter();

$db->close();
