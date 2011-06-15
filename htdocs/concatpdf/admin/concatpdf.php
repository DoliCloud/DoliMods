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
 *	    \file       htdocs/concatpdf/admin/concatpdf.php
 *      \ingroup    cabinetmed
 *      \brief      Page to setup module ConcatPdf
 *		\version    $Id: concatpdf.php,v 1.2 2011/06/15 17:30:32 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
dol_include_once("/monitoring/lib/monitoring.lib.php");	// We still use old writing to be compatible with old version


if (!$user->admin)
accessforbidden();


$langs->load("admin");
$langs->load("other");
$langs->load("concatpdf@concatpdf");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");


/*
 * Actions
 */

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
		if ($i >= 0) $i+=dolibarr_set_const($db,'MONITORING_COMMANDLINE_TOOL',trim($_POST["MONITORING_COMMANDLINE_TOOL"]),'chaine',0);

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


/**
 * View
 */

llxHeader('','ConcatPdf',$linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ConcatPdfSetup"),$linkback,'setup');
print '<br>';

/*
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
print "<td>".$langs->trans("MONITORING_COMMANDLINE_TOOL")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"MONITORING_COMMANDLINE_TOOL\" value=\"". ($_POST["MONITORING_COMMANDLINE_TOOL"]?$_POST["MONITORING_COMMANDLINE_TOOL"]:$conf->global->MONITORING_COMMANDLINE_TOOL) . "\" size=\"50\"></td>";
print "<td>/usr/bin/rrdtool";
print "</td>";
print "</tr>";

print "</table>";

print '<br><center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

print '<br>';
*/

clearstatcache();


print $langs->trans("ConcatPDfTakeFileFrom",$conf->concatpdf->dir_output.'/invoices');
print '<br><br>';

print $langs->trans("ConcatPDfPutFileManually");

$db->close();

llxFooter('$Date: 2011/06/15 17:30:32 $ - $Revision: 1.2 $');
?>
