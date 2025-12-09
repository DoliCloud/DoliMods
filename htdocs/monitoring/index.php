<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/monitoring/index.php
 *      \ingroup    monitoring
 *      \brief      Page to setup module Monitoring
 */

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
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
dol_include_once("/monitoring/lib/monitoring.lib.php"); // We still use old writing to be compatible with old version
dol_include_once("/monitoring/class/monitoring_probes.class.php"); // We still use old writing to be compatible with old version


if (!$user->hasRight('monitoring', 'read')) accessforbidden();


$langs->load("admin");
$langs->load("monitoring@monitoring");
$langs->load("other");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");
$id=GETPOST('id', 'int');

$fname = $conf->monitoring->dir_output."/".$id."/monitoring.rrd";
$fileimage[0]=$id.'/monitoring-1h.png';
$fileimage[1]=$id.'/monitoring-1d.png';
$fileimage[2]=$id.'/monitoring-1w.png';
$fileimage[3]=$id.'/monitoring-1m.png';
$fileimage[4]=$id.'/monitoring-1y.png';


/*
 * Actions
 */

if ($action == 'create') {
	$probe=new Monitoring_probes($db);
	$result=$probe->fetch($id);

	$result=dol_delete_file($fname);
	//print 'xx'.$result;

	$error=0;
	dol_mkdir($conf->monitoring->dir_output.'/'.$id);

	$step=$probe->frequency;
	$opts = array( "--step", $step,
		   "DS:ds1:GAUGE:".($step*2).":0:".$probe->maxvalue,
		   "DS:ds2:GAUGE:".($step*2).":0:".$probe->maxvalue,
		   "RRA:AVERAGE:0.5:1:".(3600/$step),              // hour      RRA:AVERAGE:0.5:nb of point to make on point:total nb of point on graph
			   "RRA:AVERAGE:0.5:".(60/$step).":1440",      // day
			   "RRA:AVERAGE:0.5:".(3600/$step).":168",     // week
			   "RRA:AVERAGE:0.5:".(3600/$step).":744",     // month
			   "RRA:AVERAGE:0.5:".(86400/$step).":365",    // year
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

if ($action == 'graph') {
	$probe=new Monitoring_probes($db);
	$result=$probe->fetch($id);

	//print dirname($conf->monitoring->dir_output.'/'.$fileimage[0]);
	dol_mkdir(dirname($conf->monitoring->dir_output.'/'.$fileimage[0]));

	$error=0;
	$mesg='';

	$newfname=preg_replace('/^[a-z]:/i', '', $fname);	// Removed C:, D: for windows path to avoid error in def string
	/*if (! dol_is_file($newfname))
	{
		 $error++;
		 $mesg=$langs->trans("ProbeNeverLaunchedLong");
	}*/

	// Hour graph
	if (! $error) {
		if (empty($conf->global->MONITORING_DISABLE_HOUR_VIEW)) {
			$opts = array(
				'--start','-1h',
				"--vertical-label=ms",
			   "DEF:ds1=\"".$newfname."\":ds1:AVERAGE",
			   "DEF:ds2=\"".$newfname."\":ds2:AVERAGE",
			   "LINE1:ds1#0000FF:Graph",
			   "LINE1:ds2#FF0000:Errors",
			   "CDEF:cdef1=ds1,1,*",
			   "CDEF:cdef2=ds2,1,*",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef1:MIN:Minval_graph%8.2lf ",
			   "GPRINT:cdef1:AVERAGE:Avgval_graph%8.2lf ",
			   "GPRINT:cdef1:MAX:Maxval_graph%8.2lf ",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef2:MIN:Minval_error%8.2lf ",
			   "GPRINT:cdef2:AVERAGE:Avgval_error%8.2lf ",
			   "GPRINT:cdef2:MAX:Maxval_error%8.2lf ",
			   'COMMENT:\\\n '
			);
			$ret = rrd_graph($conf->monitoring->dir_output.'/'.$fileimage[0], $opts, count($opts));
			$resout=file_get_contents($conf->monitoring->dir_output.'/'.$fileimage[0].'.out');
			if (strlen($resout) < 10) {
				$mesg.='<div class="ok">'.$langs->trans("File ".$fileimage[0].' created').'</div>';
			} else {
				$error++;
				$err = rrd_error($conf->monitoring->dir_output.'/'.$fileimage[0]);
				$mesg.="Graph error: ".$err."<br>\n";
			}
		}
		if (empty($conf->global->MONITORING_DISABLE_DAY_VIEW)) {
			// Day graph
			$opts = array(
				'--start','-1d',
				"--vertical-label=ms",
			   "DEF:ds1=\"".$newfname."\":ds1:AVERAGE",
			   "DEF:ds2=\"".$newfname."\":ds2:AVERAGE",
			   "LINE1:ds1#0000FF:Graph",
			   "LINE1:ds2#FF0000:Errors",
			   "CDEF:cdef1=ds1,1,*",
			   "CDEF:cdef2=ds2,1,*",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef1:MIN:Minval_graph%8.2lf ",
			   "GPRINT:cdef1:AVERAGE:Avgval_graph%8.2lf ",
			   "GPRINT:cdef1:MAX:Maxval_graph%8.2lf ",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef2:MIN:Minval_error%8.2lf ",
			   "GPRINT:cdef2:AVERAGE:Avgval_error%8.2lf ",
			   "GPRINT:cdef2:MAX:Maxval_error%8.2lf ",
			   'COMMENT:\\\n '
			);
			$ret = rrd_graph($conf->monitoring->dir_output.'/'.$fileimage[1], $opts, count($opts));
			$resout=file_get_contents($conf->monitoring->dir_output.'/'.$fileimage[1].'.out');
			if (strlen($resout) < 10) {
				$mesg.='<div class="ok">'.$langs->trans("File ".$fileimage[1].' created').'</div>';
			} else {
				$error++;
				$err = rrd_error($conf->monitoring->dir_output.'/'.$fileimage[1]);
				$mesg.="Graph error: ".$err."<br>\n";
			}
		}
		if (empty($conf->global->MONITORING_DISABLE_WEEK_VIEW)) {
			// Week graph
			$opts = array(
				'--start','-1w',
				"--vertical-label=ms",
			   "DEF:ds1=\"".$newfname."\":ds1:AVERAGE",
			   "DEF:ds2=\"".$newfname."\":ds2:AVERAGE",
			   "LINE1:ds1#0000FF:Graph",
			   "LINE1:ds2#FF0000:Errors",
			   "CDEF:cdef1=ds1,1,*",
			   "CDEF:cdef2=ds2,1,*",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef1:MIN:Minval_graph%8.2lf ",
			   "GPRINT:cdef1:AVERAGE:Avgval_graph%8.2lf ",
			   "GPRINT:cdef1:MAX:Maxval_graph%8.2lf ",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef2:MIN:Minval_error%8.2lf ",
			   "GPRINT:cdef2:AVERAGE:Avgval_error%8.2lf ",
			   "GPRINT:cdef2:MAX:Maxval_error%8.2lf ",
			   'COMMENT:\\\n '
			);
			$ret = rrd_graph($conf->monitoring->dir_output.'/'.$fileimage[2], $opts, count($opts));
			$resout=file_get_contents($conf->monitoring->dir_output.'/'.$fileimage[2].'.out');
			if (strlen($resout) < 10) {
				$mesg.='<div class="ok">'.$langs->trans("File ".$fileimage[2].' created').'</div>';
			} else {
				$error++;
				$err = rrd_error($conf->monitoring->dir_output.'/'.$fileimage[2]);
				$mesg.="Graph error: ".$err."<br>\n";
			}
		}
		if (empty($conf->global->MONITORING_DISABLE_MONTH_VIEW)) {
			// Month graph
			$opts = array(
				'--start','-1m',
				"--vertical-label=ms",
			   "DEF:ds1=\"".$newfname."\":ds1:AVERAGE",
			   "DEF:ds2=\"".$newfname."\":ds2:AVERAGE",
			   "LINE1:ds1#0000FF:Graph",
			   "LINE1:ds2#FF0000:Errors",
			   "CDEF:cdef1=ds1,1,*",
			   "CDEF:cdef2=ds2,1,*",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef1:MIN:Minval_graph%8.2lf ",
			   "GPRINT:cdef1:AVERAGE:Avgval_graph%8.2lf ",
			   "GPRINT:cdef1:MAX:Maxval_graph%8.2lf ",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef2:MIN:Minval_error%8.2lf ",
			   "GPRINT:cdef2:AVERAGE:Avgval_error%8.2lf ",
			   "GPRINT:cdef2:MAX:Maxval_error%8.2lf ",
			   'COMMENT:\\\n '
			);
			$ret = rrd_graph($conf->monitoring->dir_output.'/'.$fileimage[3], $opts, count($opts));
			$resout=file_get_contents($conf->monitoring->dir_output.'/'.$fileimage[3].'.out');
			if (strlen($resout) < 10) {
				$mesg.='<div class="ok">'.$langs->trans("File ".$fileimage[3].' created').'</div>';
			} else {
				$error++;
				$err = rrd_error($conf->monitoring->dir_output.'/'.$fileimage[3]);
				$mesg.="Graph error: ".$err."<br>\n";
			}
		}
		if (empty($conf->global->MONITORING_DISABLE_YEAR_VIEW)) {
			// Year graph
			$opts = array(
				'--start','-1y',
				"--vertical-label=ms",
			   "DEF:ds1=\"".$newfname."\":ds1:AVERAGE",
			   "DEF:ds2=\"".$newfname."\":ds2:AVERAGE",
			   "LINE1:ds1#0000FF:Graph",
			   "LINE1:ds2#FF0000:Errors",
			   "CDEF:cdef1=ds1,1,*",
			   "CDEF:cdef2=ds2,1,*",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef1:MIN:Minval_graph%8.2lf ",
			   "GPRINT:cdef1:AVERAGE:Avgval_graph%8.2lf ",
			   "GPRINT:cdef1:MAX:Maxval_graph%8.2lf ",
			   'COMMENT:\\\n ',
			   "GPRINT:cdef2:MIN:Minval_error%8.2lf ",
			   "GPRINT:cdef2:AVERAGE:Avgval_error%8.2lf ",
			   "GPRINT:cdef2:MAX:Maxval_error%8.2lf ",
			   'COMMENT:\\\n '
			);
			$ret = rrd_graph($conf->monitoring->dir_output.'/'.$fileimage[4], $opts, count($opts));
			$resout=file_get_contents($conf->monitoring->dir_output.'/'.$fileimage[4].'.out');
			if (strlen($resout) < 10) {
				$mesg.='<div class="ok">'.$langs->trans("File ".$fileimage[4].' created').'</div>';
			} else {
				$error++;
				$err = rrd_error($conf->monitoring->dir_output.'/'.$fileimage[4]);
				$mesg.="Graph error: ".$err."<br>\n";
			}
		}
	}

	if (! $error) $mesg='';
}

if ($action == 'reinit' && $id > 0) {
	$probe=new Monitoring_probes($db);
	$probe->fetch($id);

	$now=dol_now();
	$result=$probe->updateStatus(0, $now, '');

	$mesg='<div class="warning">'.$langs->trans("YouMustRestartProbeDaemon").'</div>';
}



/**
 * View
 */

$form=new Form($db);
$probestatic=new Monitoring_probes($db);


llxHeader('', 'Monitoring', $linktohelp);


if (empty($id)) {
	print_fiche_titre($langs->trans("Reports"));

	// Run probes
	print $langs->trans("RunProbeDesc").'<br><br>';

	// Confirmation de la suppression d'une ligne produit
	if ($action == 'swapstatus') {
		print $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$_GET["id"], $langs->trans('SwapStatus'), $langs->trans('ConfirmSwapStatus'), 'confirm_swapstatus', '', 'yes', 1);
	}

	print '<br>';

	$sql ="SELECT rowid, title, typeprot, url, url_params, useproxy, checkkey, maxval, frequency, status, active, lastreset, oldesterrortext, oldesterrordate";
	$sql.=" FROM ".MAIN_DB_PREFIX."monitoring_probes";
	$sql.=" ORDER BY rowid";

	dol_syslog("probes", LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql) {
		$num =$db->num_rows($resql);
		$i=0; $group='none';
		$var=true;

		while ($i < $num) {
			$obj = $db->fetch_object($resql);

			if ($i==0 || ($obj->groupname != $group)) {
				if ($obj->groupname != $group) {  // Break on group
					if ($i > 0) print '</table>';
					$group = $obj->groupname;
					print $langs->trans("ProbeGroup").': '.($group?$group:$langs->trans("Default"));
					print '<table class="liste" width="100%">';
				}

				print '<tr class="liste_titre">';
				print "<td>".$langs->trans("Id")."</td>";
				print "<td>".$langs->trans("Title")."</td>";
				print "<td>".$langs->trans("Type")."</td>";
				print "<td>".$langs->trans("URL")."</td>";
				print "<td>".$langs->trans("Proxy")."</td>";
				//print "<td>".$langs->trans("CheckKey")."</td>";
				print "<td>".$langs->trans("Frequency")."</td>";
				print "<td>".$langs->trans("MaxValue")."</td>";
				print '<td class="center">'.$langs->trans("Activable")."</td>";
				print '<td class="center">'.$langs->trans("LastStatus")."</td>";
				print '<td class="center">'.$langs->trans("StatusSince")."</td>";
				print '<td class="center">'.$langs->trans("FirstErrorDate")."</td>";
				print '<td>'.$langs->trans("FirstErrorText")."</td>";
				print '<td class="center">'.$langs->trans("Graphics")."</td>";
				//print '<td width="80px">&nbsp;</td>';
				print '</tr>';
			}

			print "<form name=\"externalrssconfig\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">";
			print '<input type="hidden" name="token" value="'.newToken().'">';

			$var=!$var;
			print "<tr ".$bc[$var].">";
			print "<td>".$obj->rowid."</td>";
			print "<td>".$obj->title."</td>";
			print "<td>".$obj->typeprot."</td>";
			print '<td><a href="'.$obj->url.'" target="_blank">'.dol_trunc($obj->url, 32, 'middle')."</a></td>";
			print "<td>".yn($obj->useproxy)."</td>";
			//print "<td>".$obj->checkkey."</td>";
			print "<td>".$obj->frequency."</td>";
			print "<td>".$obj->maxval."</td>";
			print '<td class="center">'.yn($obj->active).'</td>';
			print '<td class="center">';
			$probestatic->id=$obj->rowid;
			$probestatic->status=$obj->status;
			$probestatic->active=$obj->active;
			print $probestatic->getLibStatut(3);
			print "</td>";
			print '<td class="center">';
			print dol_print_date($obj->lastreset, '%Y-%m-%d %H:%M:%S');
			print "</td>";
			// First error date
			print '<td class="center">';
			//if ($obj->status == 0) print $langs->trans('ProbeNeverLaunched');
			//else
			if ($obj->status != 0) print dol_print_date($obj->oldesterrordate, '%Y-%m-%d %H:%M:%S');
			print "</td>";
			// First error text
			print "<td>";
			//if ($obj->status == 0) print $langs->trans('ProbeNeverLaunched');
			//else
			if ($obj->status != 0) print $form->textwithtooltip(dol_trunc($obj->oldesterrortext, 20), $obj->oldesterrortext, 1);
			print "</td>";
			// Graphics
			print '<td class="center">';
			print '<a href="index.php?id='.$obj->rowid.'">'.img_picto($langs->trans("Show"), 'graph@monitoring', 'height="24"').'</a>';
			print '</td>';
			/*print '<td align="right">';
			 print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$obj->rowid.'&amp;action=edit">';
			print img_edit();
			print '</a>';
			print '&nbsp;';
			print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$obj->rowid.'&amp;action=ask_deleteline">';
			print img_delete();
			print '</a>';
			print '</td>';*/
			print "</tr>";

			print "</form>";

			$i++;
		}
	} else {
		dol_print_error($db);
	}

	print '</table>'."\n";
} else {
	$probe=new Monitoring_probes($db);
	$result=$probe->fetch($id);

	$head = monitoring_prepare_head($probe);

	dol_fiche_head($head, 'probe', $langs->trans('Probe'), 0, 'technic');

	if (! $error) dol_htmloutput_mesg($mesg);
	else dol_htmloutput_errors($mesg);

	print '<table class="border" width="100%">';
	//print $langs->trans("ReportForProbeX");
	print '<tr><td width="20%">'.$langs->trans("Id").'</td><td>';
	print $form->showrefnav($probe, 'id', '', 1, 'rowid', 'id', '');
	print '</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("Title").'</td><td>'.$probe->title.'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("Type").'</td><td>'.$probe->typeprot.'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("Url").'</td><td><a href="'.$probe->url.'">'.$probe->url.'</a></td></tr>'."\n";
	print '<tr><td>'.$langs->trans("Parameters").'</td><td>'.$probe->url_params.'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("CheckKey").'</td><td>'.$probe->checkkey.'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("Frequency").'</td><td>'.$probe->frequency.'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("MaxValue").'</td><td>'.$probe->maxvalue.'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("Activable").'</td><td>'.yn($probe->active).'</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("RrdFile").'</td><td>'.$conf->monitoring->dir_output."/".$id.'/monitoring.rrd</td></tr>'."\n";
	print '</table><br>';

	print '<table class="border" width="100%">';
	print '<tr><td width="20%">'.$langs->trans("LastStatus").'</td><td>';
	print $probe->getLibStatut(4);
	print '</td></tr>'."\n";
	// Status since
	print '<tr><td>'.$langs->trans("StatusSince").'</td>';
	print '<td>';
	print dol_print_date($probe->lastreset, '%Y-%m-%d %H:%M:%S');
	print '</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("FirstErrorDate").'</td><td>';
	if ($probe->status == 0) print $form->textwithpicto($langs->trans('ProbeNeverLaunchedLong'), $langs->trans("YouMustRestartProbeDaemon").':<br>'.$langs->trans("RunProbeDesc"));
	else print dol_print_date($probe->oldesterrordate, '%Y-%m-%d %H:%M:%S');
	print '</td></tr>'."\n";
	print '<tr><td>'.$langs->trans("FirstErrorText").'</td><td>';
	if ($probe->status == 0) print $form->textwithpicto($langs->trans('ProbeNeverLaunchedLong'), $langs->trans("YouMustRestartProbeDaemon").':<br>'.$langs->trans("RunProbeDesc"));
	else print $probe->oldesterrortext;

	print '</td></tr>'."\n";
	print '</table>';

	dol_fiche_end();

	$butt='';

	if (! dol_is_file($fname)) {
		if ($conf->global->MONITORING_COMMANDLINE_TOOL) $butt.='<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create&id='.$probe->id.'">'.$langs->trans("CreateAGraph").'</a>';
		else $butt.='<a class="butActionRefused" href="#">'.$langs->trans("CreateAGraph").'</a>';
	}

	if ($conf->global->MONITORING_COMMANDLINE_TOOL && dol_is_file($fname)) $butt.='<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=graph&id='.$probe->id.'">'.$langs->trans("BuildGraph").'</a>';
	else $butt.='<a class="butActionRefused" href="#">'.$langs->trans("BuildGraph").'</a>';

	$butt.='<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?action=reinit&id='.$probe->id.'">'.$langs->trans("ReinitStatus").'</a>';

	if (dol_is_file($fname)) {
		if ($conf->global->MONITORING_COMMANDLINE_TOOL) $butt.='<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?action=create&id='.$probe->id.'">'.$langs->trans("CreateAGraph").'</a>';
		else $butt.='<a class="butActionRefused" href="#">'.$langs->trans("CreateAGraph").'</a>';
	}

	print '<div class="tabsAction">';
	print $butt;
	print '</div>';


	print '<br>';
	print_fiche_titre($langs->trans("Reports"), '', '').'<br>';
	print '<hr>';

	if (empty($conf->global->MONITORING_DISABLE_HOUR_VIEW) && ! dol_is_file($conf->monitoring->dir_output."/".$fileimage[0])) {
		print $langs->trans("GraphicsNotGeneratedYet");
	} else {
		if (empty($conf->global->MONITORING_DISABLE_HOUR_VIEW)) {
			print '<div class="float">';
			print $langs->trans("LastHour").'<br>';
			if (dol_is_file($conf->monitoring->dir_output."/".$fileimage[0])) print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring&file='.$fileimage[0].'">';
			else print 'PngFileNotAvailable<br>';
			print '</div>'."\n";
		}
		if (empty($conf->global->MONITORING_DISABLE_DAY_VIEW)) {
			print '<div class="float">';
			print $langs->trans("LastDay").'<br>';
			if (dol_is_file($conf->monitoring->dir_output."/".$fileimage[1])) print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring&file='.$fileimage[1].'">';
			else print 'PngFileNotAvailable<br>';
			print '</div>'."\n";
		}
		if (empty($conf->global->MONITORING_DISABLE_WEEK_VIEW)) {
			print '<div class="float">';
			print $langs->trans("LastWeek").'<br>';
			if (dol_is_file($conf->monitoring->dir_output."/".$fileimage[2])) print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring&file='.$fileimage[2].'">';
			else print 'PngFileNotAvailable<br>';
			print '</div>'."\n";
		}
		if (empty($conf->global->MONITORING_DISABLE_MONTH_VIEW)) {
			print '<div class="float">';
			print $langs->trans("LastMonth").'<br>';
			if (dol_is_file($conf->monitoring->dir_output."/".$fileimage[3])) print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring&file='.$fileimage[3].'">';
			else print 'PngFileNotAvailable<br>';
			print '</div>'."\n";
		}
		if (empty($conf->global->MONITORING_DISABLE_YEAR_VIEW)) {
			print '<div class="float">';
			print $langs->trans("LastYear").'<br>';
			if (dol_is_file($conf->monitoring->dir_output."/".$fileimage[4])) print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=monitoring&file='.$fileimage[4].'">';
			else print 'PngFileNotAvailable<br>';
			print '</div>'."\n";
		}
	}
}

print '<br>';

llxFooter();

$db->close();
