<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *	    \file       htdocs/monitoring/admin/monitoring.php
 *      \ingroup    monitoring
 *      \brief      Page to setup module Monitoring
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
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
dol_include_once("/monitoring/lib/monitoring.lib.php");	// We still use old writing to be compatible with old version


if (!$user->admin)
accessforbidden();


$langs->load("admin");
$langs->load("monitoring@monitoring");
$langs->load("other");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");

$fname = $conf->monitoring->dir_temp."/test/monitoring.rrd";
$fileimage[0]='test/monitoring-1h.png';
$fileimage[1]='test/monitoring-1d.png';
$fileimage[2]='test/monitoring-1w.png';
$fileimage[3]='test/monitoring-1m.png';
$fileimage[4]='test/monitoring-1y.png';


/*
 * Actions
 */

// Save parameters
if ($actionsave) {
	$error=0;
	$i=0;

	$db->begin();

	/*    if (! preg_match('|[\\\/]$|',$_POST["RRD_COMMANDLINE_TOOL"]))
	 {
	 $mesg="<div class=\"error\">".$langs->trans("ErrorRrdDataDirMustEndWithASlash")."</div>";
	 $error++;
	 }
	 */
	if (! $error) {
		if ($i >= 0) $i+=dolibarr_set_const($db, 'MONITORING_COMMANDLINE_TOOL', trim($_POST["MONITORING_COMMANDLINE_TOOL"]), 'chaine', 0);

		if ($i >= 1) {
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

if ($action == 'create') {
	$error=0;
	dol_mkdir($conf->monitoring->dir_temp.'/test');

	$step=5;
	$opts = array( "--step", $step,
		   "DS:ds1:GAUGE:".($step*2).":0:100",
		   "DS:ds2:GAUGE:".($step*2).":0:100",
		   "RRA:AVERAGE:0.5:1:".(3600/$step),
			   "RRA:AVERAGE:0.5:".(60/$step).":1440",
			   "RRA:AVERAGE:0.5:".(3600/$step).":168",
			   "RRA:AVERAGE:0.5:".(3600/$step).":744",
			   "RRA:AVERAGE:0.5:".(86400/$step).":365",
			   "RRA:MAX:0.5:1:".(3600/$step),
			   "RRA:MAX:0.5:".(60/$step).":1440",
			   "RRA:MAX:0.5:".(3600/$step).":168",
			   "RRA:MAX:0.5:".(3600/$step).":744",
			   "RRA:MAX:0.5:".(86400/$step).":365",
			   "RRA:MIN:0.5:1:".(3600/$step),
			   "RRA:MIN:0.5:".(60/$step).":1440",
			   "RRA:MIN:0.5:".(3600/$step).":168",
			   "RRA:MIN:0.5:".(3600/$step).":744",
			   "RRA:MIN:0.5:".(86400/$step).":365",
				);

	$ret = rrd_create($fname, $opts, count($opts));
	$resout=file_get_contents($fname.'.out');
	if (strlen($resout) < 10) {
		$mesg='<div class="ok">'.$langs->trans("File ".$fname.' created').'</div>';
		$action='graph';	// To rebuild graph
	} else {
		$error++;
		$err = rrd_error($fname);
		$mesg="Create error: $err\n";
	}
}

if ($action == 'update') {
	$error=0;
	$val1=rand(0, 100);
	$val2=25;
	$ret = rrd_update($fname, "N:$val1:$val2");

	if ( $ret > 0) {
		$mesg='<div class="ok">'.$langs->trans("File ".$fname.' completed with random values '.$val1.' for graph 1 and '.$val2.' for graph 2').'</div>';
	} else {
		$error++;
		$err = rrd_error($fname);
		$mesg="Update error: $err\n";
	}
}

if ($action == 'graph') {
	$error=0;
	$mesg='';

	$newfname=preg_replace('/^[a-z]:/i', '', $fname);	// Removed C:, D: for windows path to avoid error in def string

	$opts = array(
			'--start','-1h',
			"--vertical-label=%",
		   "DEF:ds1=\"".$newfname."\":ds1:AVERAGE",
		   "DEF:ds2=\"".$newfname."\":ds2:AVERAGE",
			"LINE1:ds1#0000FF:Graph1",
			"LINE1:ds2#00FF00:Graph2",
			"CDEF:cdef1=ds1,1,*",
		   "CDEF:cdef2=ds2,1,*",
		   'COMMENT:\\\n ',
		   "GPRINT:cdef1:MIN:Minval1%6.2lf ",
		   "GPRINT:cdef1:AVERAGE:Avgval1%6.2lf ",
		   "GPRINT:cdef1:MAX:Maxval1%6.2lf ",
		   'COMMENT:\\\n ',
		   "GPRINT:cdef2:MIN:Minval2%6.2lf ",
		   "GPRINT:cdef2:AVERAGE:Avgval2%6.2lf ",
		   "GPRINT:cdef2:MAX:Maxval2%6.2lf ",
		   'COMMENT:\\\n ',
		);
	$ret = rrd_graph($conf->monitoring->dir_temp.'/'.$fileimage[0], $opts, count($opts));
	$resout=file_get_contents($conf->monitoring->dir_temp.'/'.$fileimage[0].'.out');
	if (strlen($resout) < 10) {
		$mesg.='<div class="ok">'.$langs->trans("File ".$fileimage[0].' created').'</div>';
	} else {
		$error++;
		$err = rrd_error($conf->monitoring->dir_temp.'/'.$fileimage[0]);
		$mesg.="Graph error: $err\n";
	}

	if (! $error) $mesg='';
}



/**
 * View
 */

llxHeader('', 'RRd', $linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("RrdSetup"), $linkback, 'setup');
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

dol_fiche_head($head, 'tabsetup', '');


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
print '<td>/usr/bin/rrdtool<br>
c:\Program Files\rrdtool\rrdtool.exe';
print "</td>";
print "</tr>";

print "</table>";

print '<br><center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

dol_fiche_end();


print '<br>';

clearstatcache();

dol_htmloutput_mesg($mesg);

print '<hr>';

print $langs->trans("ManualTestDesc").'<br><br>';

// Buttons
//print '<div class="tabsAction">';
if ($conf->global->MONITORING_COMMANDLINE_TOOL) {
	print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create">'.$langs->trans("CreateATestGraph").'</a>';
	if (dol_is_file($fname)) {
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=update">'.$langs->trans("AddValueToTestGraph").'</a>';
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=graph">'.$langs->trans("BuildTestGraph").'</a>';
	} else {
		print '<a class="butActionRefused" href="#">'.$langs->trans("AddValueToTestGraph").'</a>';
		print '<a class="butActionRefused" href="#">'.$langs->trans("BuildTestGraph").'</a>';
	}
} else {
	print '<a class="butActionRefused" href="#">'.$langs->trans("CreateATestGraph").'</a>';
	print '<a class="butActionRefused" href="#">'.$langs->trans("AddValueToTestGraph").'</a>';
	print '<a class="butActionRefused" href="#">'.$langs->trans("BuildTestGraph").'</a>';
}
//print '</div>';


print '<br><br>';
if (dol_is_file($conf->monitoring->dir_temp."/".$fileimage[0])) {
	print $langs->trans("LastHour").'<br>';
	print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring_temp&file='.$fileimage[0].'">';
}
/*	print '<br>';
 print $langs->trans("LastDay").'<br>';
 print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring_temp&file='.$fileimage[1].'">';
 print '<br>';
 print $langs->trans("LastWeek").'<br>';
 print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring_temp&file='.$fileimage[2].'">';
 print '<br>';
 print $langs->trans("LastMonth").'<br>';
 print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring_temp&file='.$fileimage[3].'">';
 print '<br>';
 print $langs->trans("LastYear").'<br>';
 print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring_temp&file='.$fileimage[4].'">';
 */


$db->close();

llxFooter();
