<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * This script was built from:
 * Multiple Site Statistics Viewer
 * Copyright (C)2002-2005 Jason Reid
 */

/**     \defgroup   awstats     Module AWStats
 *		\brief      Module to AWStats tools integration.
 */

/**
 *	\file       htdocs/awstats/index.php
 *	\brief      Home page of AWStats module
 *	\version    $Id: index.php,v 1.23 2011/08/16 09:28:25 eldy Exp $
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

include_once "./lib/awstats.lib.php";
include_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";

$user->getrights('awstats');

$langs->load("awstats@awstats");
$langs->load("others");

//$conf->global->BETTERAWSTATS_AWSTATS_LIB='e:/Mes Developpements/awstats/wwwroot/cgi-bin/lib';
//$conf->global->BETTERAWSTATS_AWSTATS_LANG='e:/Mes Developpements/awstats/wwwroot/cgi-bin/lang';
//$conf->global->BETTERAWSTATS_AWSTATS_ICON='e:/Mes Developpements/awstats/wwwroot/cgi-bin/icon';

if (empty($conf->global->AWSTATS_DATA_DIR)) {
	llxHeader();
	print '<div class="error">'.$langs->trans("AWStatsSetupNotComplete").'</div>';
	llxFooter();
	exit;
}

$AWSTATS_CGI_PATH=$conf->global->AWSTATS_CGI_PATH;
if (! preg_match('/\?/', $AWSTATS_CGI_PATH)) { $AWSTATS_CGI_PATH.='?'; } else $AWSTATS_CGI_PATH.='&amp;';

$history_dir 		= 	$conf->global->AWSTATS_DATA_DIR;		// Location of history files
$filter_year		=	isset($_REQUEST["filter_year"])?$_REQUEST["filter_year"]:'';		// year or all
if (empty($filter_year)) $filter_year=date("Y");	// Show only current year statistics
$filter_domains		=	isset($_REQUEST["filter_domains"])?$_REQUEST["filter_domains"]:'';
$limittoconf=array();
if (! empty($conf->global->AWSTATS_LIMIT_CONF)) $limittoconf=explode(',', $conf->global->AWSTATS_LIMIT_CONF);


$domain_list		=	array();					// List of domains to show if filter_domains is true
$build_domains		=	true;						// Show domain by domain statistics
$build_system		=	false;						// Show system statistics
$accept_query		=	true;						// Accept domain list via query string (adds to $domain_list)
// in format ?domains=domain1.com,domain2.com,domain3.com
$format_numbers		=	true;						// Format numbers with comma's to indicate thousands
$gzip_compression	=	false;						// Enable gzip compression
$system_stats_top	=	false;						// Show system statistics above domain statistics
$table_width		=	"100%";						// Content table width
$table_align		=	"center";					// Content table alignment




/*
 *	View
 */

$help_url="EN:Module_AWStats_En|FR:Module_AWStats|ES:M&oacute;dulo_AWStats";
llxHeader('', 'AWStats', $help_url);

$form=new Form($db);

// Check and enable gzip if requested
if ($gzip_compression == true && function_exists("gzopen")) {
	ob_start("ob_gzhandler");	// Start output buffering with gzip compression
} else {
	ob_start();					// Start output buffering without compression
}

// Record Starting Time
$stime = dol_now();

// Build Domain List from Query (if enabled)
if ($accept_query == true && strlen($_GET['domains']) > 0) {
	$get_domain_list = explode(",", $_GET['domains']);
	$domain_list = array_merge($domain_list, $get_domain_list);
}
// Clear 'reserved' variables
unset($domains);

// Initialize all arrays
$files 			= 		array();
$sites 			=		array();
$stats 			= 		array();
$domaininfo		=		array();
$total			=		array();
$max     		=		array();
$sitecount		=		0;

// Declare functions


// Timer Function	#
function gettime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float) $usec + (float) $sec);
}

// Text Functions		#

function read_file($file, $domain)
{
	global $history_dir;
	global $domaininfo;
	global $total;
	global $max;

	$reg = array();
	if (! preg_match('/^awstats([0-9][0-9])([0-9][0-9][0-9][0-9])/', $file, $reg)) {
		return -1;
	}

	$yyyy = $reg[2];
	$mm = $reg[1];

	$filename = $history_dir."/".$file;
	$fd = fopen($filename, "r");
	$contents = fread($fd, 4096);	// Suppose TIME section is at beginning
	fclose($fd);

	if (preg_match('/^<xml/', $contents)) {
		// It's an xml file
		$domaininfo[$domain][$yyyy][$mm]['pages'] = 'XML file';
		$domaininfo[$domain][$yyyy][$mm]['hits'] = 'XML file';
		$domaininfo[$domain][$yyyy][$mm]['traffic'] = 'XML file';
		$domaininfo[$domain][$yyyy][$mm]['visits'] = 'XML file';
		$domaininfo[$domain][$yyyy][$mm]['visitors'] = 'XML file';
		return -2;
	}

	$use = strpos($contents, "BEGIN_TIME");
	$newline = strpos($contents, "\n", $use);
	$end = strpos($contents, "END_TIME", $newline);
	$data .= substr($contents, $newline+1, $end-$newline-2);
	$data = explode("\n", $data);
	if ($data[0] != "END_TIME") {
		foreach ($data as $info) {
			$dom_info = explode(" ", $info);
			if (count($dom_info) < 5) { break; }

			// Record number of page views
			$domaininfo[$domain][$yyyy][$mm]['pages'] += $dom_info[1];
			$total[$domain]['pages'] += $dom_info[1];
			// Record number of hits
			$domaininfo[$domain][$yyyy][$mm]['hits'] += $dom_info[2];
			$total[$domain]['hits'] += $dom_info[2];
			// Record ammount of traffic
			$domaininfo[$domain][$yyyy][$mm]['traffic'] += $dom_info[3];
			$total[$domain]['traffic'] += $dom_info[3];
		}
	}
	$max['pages']=max($max['pages'], $domaininfo[$domain][$yyyy][$mm]['pages']);
	$max['hits']=max($max['hits'], $domaininfo[$domain][$yyyy][$mm]['hits']);
	$max['traffic']=max($max['traffic'], $domaininfo[$domain][$yyyy][$mm]['traffic']);
	//print "<br>\n".$domain.' '.$dom_info[1].' '.$yyyy.$mm."<br>\n";
	//var_dump($max);

	$use = strpos($contents, "BEGIN_GENERAL");
	$newline = strpos($contents, "\n", $use);
	$end = strpos($contents, "END_GENERAL", $newline);
	$data .= substr($contents, $newline+1, $end-$newline-2);
	$data = explode("\n", $data);
	if ($data[0] != "END_GENERAL") {
		foreach ($data as $info) {
			$dom_info = explode(" ", $info);

			if ($dom_info[0] == 'TotalVisits') {
				// Record number of visits
				$domaininfo[$domain][$yyyy][$mm]['visits'] += $dom_info[1];
				$total[$domain]['visits'] += $dom_info[1];
				$max['visits']=max($max['visits'], $dom_info[1]);
			}
			if ($dom_info[0] == 'TotalUnique') {
				// Record number of visitors
				$domaininfo[$domain][$yyyy][$mm]['visitors'] += $dom_info[1];
				$total[$domain]['visitors'] += $dom_info[1];
				$max['visitors']=max($max['visitors'], $dom_info[1]);
			}
		}
	}
}

// XML Functions			#

// Initialize arrays
$xmldata = array();
$bigarray = array();


// Number formatting function	#
function format($number)
{
	global $format_numbers;

	if ($format_numbers == false) {
		return $number;
	} else {
		if (preg_match('/^XML/', $number)) return $number;
		return number_format($number);
	}
}



// Begin Processing Files	#

// Open History Directory
$dir = @opendir($history_dir);

// Check if History Directory Exists
if (!$dir) {
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Error Occured</title>
</head>
<body>
<h1>Error</h1><br>
Failed to open directory defined in AWStats config page (AWSTATS_DATA_DIR = '.$history_dir.')<br>
Check your <a href="'.dol_buildpath('/awstats/admin/awstats.php', 1).'">AWStats setup</a> and open_basedir PHP setup.
</body>
</html>';
	exit;
}

// Define list of qualified files
while (($file = readdir($dir)) !== false) {
	if ((substr_count($file, "awstats") == 0 && strlen($file) >= 14) || substr_count($file, ".") == 0 || $file == "." || $file == "..") continue;				// Drop all files except history files
	{
		$domname = substr($file, 14);								// Find Domain Name
		$domname = substr($domname, 0, -4);							// And remove trailing

		// If a limit has been set
	if (count($limittoconf)) {
		if (! in_array($domname, $limittoconf)) continue;	// Not qualified
	}

		//print($file."-".$filter_year."-".$filer_domains."-".$domname."<br>\n");
	if ($filter_year == 'all' && ! empty($filter_domains) && preg_match('/'.$filter_domains.'/', $domname)) {
		$files[] = $file;
	} elseif ($filter_year == 'all' && empty($filter_domains)) {
		$files[] = $file;
	} elseif ($filter_year != 'all' && empty($filter_domains) && substr_count($file, $filter_year) == 1) {
		$files[] = $file;
	} elseif ($filter_year != 'all' && ! empty($filter_domains) && substr_count($file, $filter_year) == 1 && preg_match('/'.$filter_domains.'/', $domname)) {
		$files[] = $file;
	} elseif ($filter_year != 'all' && substr_count($file, $filter_year) == 1 && empty($filter_domains)) {
		$files[] = $file;
	}
	}
}

// Check if there are any valid files, otherwise exit
if (count($files) == 0) {
	$output_table = '<div align="'.$table_align.'">';
	$output_table.= 'Sorry, No AWStats data files (awstatsYYMMDD.*) found into directory <b>'.$history_dir.'</b> for the selected filters.';
	if (! empty($conf->global->AWSTATS_LIMIT_CONF)) $output_table.='<br>Note that search is restricted to config name <b>' . getDolGlobalString('AWSTATS_LIMIT_CONF').'</b>';
	$output_table.= '</div>';
} else {
	// Sort the files in ascending order and then reset the list of sites
	sort($files);
	reset($sites);

	// Check for file type
	$curr = 0;
	while ($curr < count($files)) {
		$file = $history_dir.'/'.$files[$curr];
		$month = substr($files[$curr], 7, 2);
		$year = substr($files[$curr], 9, 4);
		$domain = substr($files[$curr], 14, -4);

		$domains[] = $domain;

		//print dol_print_date(mktime(),'Y%m%d%H%M%S')." Process file ".$files[$curr]."<br>\n";
		// Check if we are filtering the domains
		if ($filter_domains == true && array_search($domain, $domain_list) == false) {
			// Read the source file
			read_file($files[$curr], $domain);
		} else {
			// Read the source file
			read_file($files[$curr], $domain);
		}
		$curr++;
	}
	// Remove Duplicate Domains and resort
	$domains = array_unique($domains);
	sort($domains);


	// Start building the report  #
	$maxwidth=160;
	if ($build_domains == true) {
		ksort($domaininfo);
		foreach ($domaininfo as $key => $ddata) {
			//if (empty($key)) continue;

			ksort($ddata);

			$output_table .= '<table width="'.$table_width.'" cellspacing="0" cellpadding="1" align="'.$table_align.'">
<tr>
<td>';
			//$output_table .= '&nbsp;';
			$output_table .= '<b>'.(empty($key)?'No name':$key).'</b> ';
			$output_table .= '</td>
<td width="80" class="visitors-bold" nowrap="nowrap" style="text-align: right">'.$langs->trans("Visitors").'</td>
<td width="80" class="visits-bold" style="text-align: right">'.$langs->trans("Visits").'</td>
<td width="80" class="pages-bold" style="text-align: right">'.$langs->trans("Pages").'</td>
<td width="80" class="hits-bold" style="text-align: right">'.$langs->trans("Hits").'</td>
<td width="80" class="bandwidth-bold" style="text-align: right">'.$langs->trans("Bandwidth").'</td>
<td width="'.$maxwidth.'" align="center">';
			$output_table .= '<a href="'.$AWSTATS_CGI_PATH.($key?'config='.$key:'').'" alt="AWStats" title="AWStats" target="_blank">';
			$output_table .= '<img src="'.dol_buildpath('/awstats/images/awstats_screen.png', 1).'" border="0">';
			$output_table .= '</td>';
			$output_table .= '</tr>';
			foreach ($ddata as $key2 => $data) {
				$i=0;
				// List of month
				foreach ($data as $key3 => $ata) {
					if ($i % 2) {			// Alternate colors
						$bgc = "first";		// Use first bg color
					} else {
						$bgc = "second";	// Use second bg color
					}

					// Define traffic
					if (preg_match('/^XML/', $domaininfo[$key][$key2][$key3]['traffic'])) $traffic=$domaininfo[$key][$key2][$key3]['traffic'];
					else {
						if ($domaininfo[$key][$key2][$key3]['traffic'] > 1073741824) {	// Over 1GB
							$traffic = sprintf("%.2f", $domaininfo[$key][$key2][$key3]['traffic']/1024/1024/1024).' GB';
						} elseif ($domaininfo[$key][$key2][$key3]['traffic'] > 1048576) { // Over 1MB
							$traffic = sprintf("%.2f", $domaininfo[$key][$key2][$key3]['traffic']/1024/1024).' MB';
						} else { // Under 1MB
							$traffic = sprintf("%.2f", $domaininfo[$key][$key2][$key3]['traffic']/1024).' KB';
						}
					}

					$output_table .= '  <tr class="'.$bgc.'">
<td class="domain">'.$key3.' '.$key2.'</td>
<td class="visitors" style="text-align: right">'.format($domaininfo[$key][$key2][$key3]['visitors']).'</td>
<td class="visits" style="text-align: right">'.format($domaininfo[$key][$key2][$key3]['visits']).'</td>
<td class="pages" style="text-align: right">'.format($domaininfo[$key][$key2][$key3]['pages']).'</td>
<td class="hits" style="text-align: right">'.format($domaininfo[$key][$key2][$key3]['hits']).'</td>
<td class="bandwidth" style="text-align: right">'.$traffic.'</td>
<td>';

					$width['visitors']=$maxwidth*$domaininfo[$key][$key2][$key3]['visitors']/$max['visitors'];
					$width['visits']=$maxwidth*$domaininfo[$key][$key2][$key3]['visits']/$max['visits'];
					$width['pages']=$maxwidth*$domaininfo[$key][$key2][$key3]['pages']/$max['pages'];
					$width['hits']=$maxwidth*$domaininfo[$key][$key2][$key3]['hits']/$max['hits'];
					$width['traffic']=$maxwidth*$domaininfo[$key][$key2][$key3]['traffic']/$max['traffic'];

					$output_table .= '<table class="nobordernopadding">';
					$output_table .= '<tr class="nobordernopadding" height="2"><td class="nobordernopadding" align="left"><img src="'.dol_buildpath('/awstats/images/hu.png', 1).'" height="3" width="'.ceil($width['visitors']).'"></td></tr>';
					$output_table .= '<tr class="nobordernopadding" height="2"><td class="nobordernopadding" align="left"><img src="'.dol_buildpath('/awstats/images/hv.png', 1).'" height="3" width="'.ceil($width['visits']).'"></td></tr>';
					$output_table .= '<tr class="nobordernopadding" height="2"><td class="nobordernopadding" align="left"><img src="'.dol_buildpath('/awstats/images/hp.png', 1).'" height="3" width="'.ceil($width['pages']).'"></td></tr>';
					$output_table .= '<tr class="nobordernopadding" height="2"><td class="nobordernopadding" align="left"><img src="'.dol_buildpath('/awstats/images/hh.png', 1).'" height="3" width="'.ceil($width['hits']).'"></td></tr>';
					$output_table .= '<tr class="nobordernopadding" height="2"><td class="nobordernopadding" align="left"><img src="'.dol_buildpath('/awstats/images/hk.png', 1).'" height="3" width="'.ceil($width['traffic']).'"></td></tr>';
					$output_table .= '</table>';

					$output_table .= '</td>	</tr>';
					$i++;
				}
			}

			// Define traffic
			if (preg_match('/^XML/', $total[$key]['traffic'])) $traffic=$total[$key]['traffic'];
			else {
				if ($total[$key]['traffic'] > 1073741824) {	// Over 1GB
					$traffic = sprintf("%.2f", $total[$key]['traffic']/1024/1024/1024).' GB';
				} elseif ($total[$key]['traffic'] > 1048576) { // Over 1MB
					$traffic = sprintf("%.2f", $total[$key]['traffic']/1024/1024).' MB';
				} else { // Under 1MB
					$traffic = sprintf("%.2f", $total[$key]['traffic']/1024).' KB';
				}
			}

			$output_table .= '
<tr class="liste_total">
<td class="domain-bold">'.$langs->trans("Total").':</td>
<td class="domain-bold" style="text-align: right">'.format($total[$key]['visitors']).'</td>
<td class="domain-bold" style="text-align: right">'.format($total[$key]['visits']).'</td>
<td class="domain-bold" style="text-align: right">'.format($total[$key]['pages']).'</td>
<td class="domain-bold" style="text-align: right">'.format($total[$key]['hits']).'</td>
<td class="domain-bold" style="text-align: right">'.$traffic.'</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan="6"><br></td>
</tr>
</table>';
		}
	}


	// Check and build system statistics table.  #
	if ($build_system == true) {
		// Calculate totals
		$server = array();
		ksort($domaininfo);
		foreach ($domaininfo as $key1 => $data1) {
			ksort($data1);
			foreach ($data1 as $key2 => $data2) {
				foreach ($data2 as $key3 => $data3) {
					foreach ($data3 as $key4 => $data4) {
						$server[$key2][$key3][$key4] += (float) $data4;
						$total2[$key4] += (float) $data4;
					}
				}
			}
		}

		$system_table = '<table width="'.$table_width.'" cellspacing="0" cellpadding="1" align="'.$table_align.'">
  <tr class="liste_titre">
    <td width="40%" class="domain-bold">System Statistics</td>
    <td width="12%" class="visitors-bold">Visitors</td>
    <td width="12%" class="visits-bold">Visits</td>
    <td width="12%" class="pages-bold">Pages</td>
    <td width="12%" class="hits-bold">Hits</td>
    <td width="12%" class="bandwidth-bold">Bandwidth</td>
	<td>&nbsp;</td>
  </tr>';
		ksort($server);
		foreach ($server as $key1 => $data1) {
			ksort($data1);
			foreach ($data1 as $key2 => $data2) {
				if ($i % 2) {
					$bgc = "first";
				} else {
					$bgc = "second";
				}

				// Define traffic
				if (preg_match('/^XML/', $server[$key1][$key2]['traffic'])) $traffic=$server[$key1][$key2]['traffic'];
				else {
					if ($server[$key1][$key2]['traffic'] > 1073741824) {	// Over 1GB
						$traffic = sprintf("%.2f", $server[$key1][$key2]['traffic']/1024/1024/1024).' GB';
					} elseif ($server[$key1][$key2]['traffic'] > 1048576) { // Over 1MB
						$traffic = sprintf("%.2f", $server[$key1][$key2]['traffic']/1024/1024).' MB';
					} else { // Under 1MB
						$traffic = sprintf("%.2f", $server[$key1][$key2]['traffic']/1024).' KB';
					}
				}

				$system_table .= '  <tr class="'.$bgc.'">
    <td class="domain">'.$key2.' '.$key1.'</td>
    <td class="visitors">'.format($server[$key1][$key2]['visitors']).'</td>
    <td class="visits">'.format($server[$key1][$key2]['visits']).'</td>
    <td class="pages">'.format($server[$key1][$key2]['pages']).'</td>
    <td class="hits">'.format($server[$key1][$key2]['hits']).'</td>
    <td class="bandwidth">'.$traffic.'</td>
	<td>&nbsp;</td>
  </tr>';
				$i++;
			}
		}
		if ($total2['traffic'] > 1073741824) {	// Over 1GB
			$traffic = sprintf("%.2f", $total2['traffic']/1024/1024/1024).' GB';
		} elseif ($total2['traffic'] > 1048576) { // Over 1MB
			$traffic = sprintf("%.2f", $total2['traffic']/1024/1024).' MB';
		} else { // Under 1MB
			$traffic = sprintf("%.2f", $total2['traffic']/1024).' KB';
		}
		$system_table .= '
  <tr class="liste_total">
    <td class="domain-bold">'.$langs->trans("Total").':</td>
    <td class="visitors-bold">'.format($total2['visitors']).'</td>
    <td class="visits-bold">'.format($total2['visits']).'</td>
    <td class="pages-bold">'.format($total2['pages']).'</td>
    <td class="hits-bold">'.format($total2['hits']).'</td>
    <td class="bandwidth-bold">'.$traffic.'</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5"><BR></td>
  </tr>
</table>';
	}
}



// Record Completion Time
$etime = dol_now();

// Format HTML
$html =	'';

print_fiche_titre(' &nbsp; '.$langs->trans("AWStatsSummary"), '', dol_buildpath('/awstats/images/awstats.png', 1), 1);

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.newToken().'">';

print '<table class="border" width="100%"><tr><td>'.$langs->trans("Year").':</td><td>';
$yearsarray=array('all'=>$langs->trans("All"));
$currentyear = date('Y');
$i = 2000;
while($i <= $currentyear) {
	$currentyear[$i] = $i;
	$i++;
}
print $form->selectarray('filter_year', $yearsarray, ($filter_year?$filter_year:'all'), 0);
print '</td>';
print '<td rowspan="2" align="center"><input class="button" type="submit" value="'.$langs->trans("Update").'"></td>';
print '</tr>';
print '<tr><td>'.$langs->trans("FilterDomain").':</td><td><input class="flat" type="text" name="filter_domains" value="'.$filter_domains.'"></td>';
print '</table>';
print '</form>';
print '<br>';

// Format CSS
// Piece it all together
if ($system_stats_top == true) {
	$statistics = $html.$title.$system_table.$output_table.$html2;
} else {
	$statistics = $html.$title.$output_table.$system_table.$html2;
}
// Output to the screen
echo $statistics;

llxFooter();

$db->close();
