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
 *	    \file       htdocs/admin/rrd.php
 *      \ingroup    rrd
 *      \brief      Page de configuration du module Rrd
 *		\version    $Id: rrd.php,v 1.1 2011/01/22 11:30:16 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
dol_include_once("/rrd/lib/rrd.lib.php");	// We still use old writing to be compatible with old version


if (!$user->admin)
accessforbidden();


$langs->load("admin");
$langs->load("rrd@rrd");
$langs->load("other");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");

// Save parameters
if ($actionsave)
{
    $error=0;
    $i=0;

    $db->begin();

    /*    if (! preg_match('|[\\\/]$|',$_POST["RRD_COMMANDLINE_TOOL"]))
     {
    	$mesg="<div class=\"error\">".$langs->trans("ErrorRrdDataDirMustEndWithASlash")."</div>";
    	$error++;
    	}
    	*/
    if (! $error)
    {
        if ($i >= 0) $i+=dolibarr_set_const($db,'RRD_COMMANDLINE_TOOL',trim($_POST["RRD_COMMANDLINE_TOOL"]),'chaine',0);

        if ($i >= 1)
        {
            $db->commit();
            $mesg = "<div class=\"ok\">".$langs->trans("SetupSaved")."</div>";
        }
        else
        {
            $db->rollback();
            $mesg=$db->lasterror();
            //header("Location: ".$_SERVER["PHP_SELF"]);
            //exit;
        }
    }
}

if ($action == 'create')
{
    $fname = $conf->rrd->dir_temp."/net.rrd";

    $opts = array( "–step", "300", "–start", 0,
           "DS:input:COUNTER:600:U:U",
           "DS:output:COUNTER:600:U:U",
           "RRA:AVERAGE:0.5:1:600",
           "RRA:AVERAGE:0.5:6:700",
           "RRA:AVERAGE:0.5:24:775",
           "RRA:AVERAGE:0.5:288:797",
           "RRA:MAX:0.5:1:600",
           "RRA:MAX:0.5:6:700",
           "RRA:MAX:0.5:24:775",
           "RRA:MAX:0.5:288:797"
           );

           $ret = rrd_create($fname, $opts, count($opts));

           if( $ret == 0 )
           {
               $err = rrd_error();
               echo "Create error: $err\n";
           }
}




/**
 * View
 */

$help_url='EN:Module_Rrd_EN|FR:Module_Rrd|ES:Modulo_Rrd';
llxHeader('','RRd',$help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("RrdSetup"),$linkback,'setup');
print '<br>';


print '<form name="rrdform" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print "<table class=\"noborder\" width=\"100%\">";
$var=true;

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("RRD_COMMANDLINE_TOOL")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"RRD_COMMANDLINE_TOOL\" value=\"". ($_POST["RRD_COMMANDLINE_TOOL"]?$_POST["RRD_COMMANDLINE_TOOL"]:$conf->global->RRD_COMMANDLINE_TOOL) . "\" size=\"50\"></td>";
print "<td>/usr/bin/rrdtool";
print "</td>";
print "</tr>";

print "</table>";

print '<br><center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

print '<br>';


clearstatcache();

if ($mesg) print "<br>$mesg<br>";
print "<br>";

if (function_exists('rrd_create'))
{
    // Buttons
    print '<div class="tabsAction">';
    print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create">'.$langs->trans("CreateATestGraph").'</a>';
    print '</div>';
}
else
{
    print $langs->trans("RrdFunctionsNotEnabledOnYourPHP");
}


$db->close();

llxFooter('$Date: 2011/01/22 11:30:16 $ - $Revision: 1.1 $');
?>
