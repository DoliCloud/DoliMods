<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/admin/awstats.php
 *      \ingroup    awstats
 *      \brief      Page de configuration du module AWStats
 *		\version    $Id: awstats.php,v 1.4 2009/05/05 14:02:04 eldy Exp $
 */

$res=@include("./pre.inc.php");
include("../awstats/awstats.lib.php");
if (! $res) include("../../../dolibarr/htdocs/admin/pre.inc.php");	// Used on dev env only

require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/html.formadmin.class.php');


if (!$user->admin)
    accessforbidden();


$langs->load("admin");
$langs->load("awstats");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

// Sauvegardes parametres
if ($actionsave)
{
    $i=0;

    $db->begin();

    $i+=dolibarr_set_const($db,'AWSTATS_DATA_DIR',trim($_POST["AWSTATS_DATA_DIR"]),'chaine',0);
    $i+=dolibarr_set_const($db,'AWSTATS_CGI_PATH',trim($_POST["AWSTATS_CGI_PATH"]),'chaine',0);
    $i+=dolibarr_set_const($db,'AWSTATS_PROG_PATH',trim($_POST["AWSTATS_PROG_PATH"]),'chaine',0);
    $i+=dolibarr_set_const($db,'AWSTATS_LIMIT_CONF',trim($_POST["AWSTATS_LIMIT_CONF"]),'chaine',0);

    if ($i >= 3)
    {
        $db->commit();
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $db->rollback();
        header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
}



/**
 * View
 */

llxHeader('AWStats','AWStats',$linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("AWStatsSetup"),$linkback,'setup');
print '<br>';


print '<form name="awstatsform" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print "<table class=\"noborder\" width=\"100%\">";
$var=true;

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("AWSTATS_DATA_DIR")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_DATA_DIR\" value=\"". ($_POST["AWSTATS_DATA_DIR"]?$_POST["AWSTATS_DATA_DIR"]:$conf->global->AWSTATS_DATA_DIR) . "\" size=\"50\"></td>";
print "<td>/usr/local/awstats/data/";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("AWSTATS_CGI_PATH")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_CGI_PATH\" value=\"". ($_POST["AWSTATS_CGI_PATH"]?$_POST["AWSTATS_CGI_PATH"]:$conf->global->AWSTATS_CGI_PATH) . "\" size=\"50\"></td>";
print "<td>http://myserver/awstats/awstats.pl<br>http://myserver/cgi-bin/awstats.pl?configdir=/home/awstats/conf";
print "</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("AWSTATS_PROG_PATH")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"AWSTATS_PROG_PATH\" value=\"". ($_POST["AWSTATS_CGI_PATH"]?$_POST["AWSTATS_PROG_PATH"]:$conf->global->AWSTATS_PROG_PATH) . "\" size=\"50\"></td>";
print "<td>/usr/local/awstats/wwwroot/cgi-bin/awstats.pl<br>c:\MyDir\awstats.pl";
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
print "<br>";

print '<br><center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


clearstatcache();

if ($mesg) print "<br>$mesg<br>";
print "<br>";

$db->close();

llxFooter('$Date: 2009/05/05 14:02:04 $ - $Revision: 1.4 $');
?>
