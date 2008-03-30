<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * This script was built from:
 * Multiple Site Statistics Viewer
 * Copyright (C)2002-2005 Jason Reid
 */

/**     \defgroup   awstats     Module AWStats
        \brief      Module to AWStats tools integration.
*/

/**
	\file       htdocs/awstats/index.php
	\brief      Page accueil module AWStats
	\version    $Id: index.php,v 1.2 2008/03/30 18:50:14 eldy Exp $
*/

include("./pre.inc.php");


//$conf->global->BETTERAWSTATS_AWSTATS_LIB='e:/Mes Developpements/awstats/wwwroot/cgi-bin/lib';
//$conf->global->BETTERAWSTATS_AWSTATS_LANG='e:/Mes Developpements/awstats/wwwroot/cgi-bin/lang';
//$conf->global->BETTERAWSTATS_AWSTATS_ICON='e:/Mes Developpements/awstats/wwwroot/cgi-bin/icon';

if (empty($conf->global->AWSTATS_DATA_DIR))
{
	llxHeader();
	print '<div class="error">'.$langs->trans("AWStatsSetupNotComplete").'</div>';	
	llxFooter;
	exit;
}



/*
*	View
*/

llxHeader();

$form=new Form($db);


# CONFIGURATION
$title				=	"AWStats Statistics";	# Page Title
$history_dir 		= 	$conf->global->AWSTATS_DATA_DIR;		# Location of history files
$filter_year		=	isset($_GET["filter_year"])?$_GET["filter_year"]:date("Y");						# Show only current year statistics
$filter_domains		=	false;						# Show only certain domains
$domain_list		=	array();					# List of domains to show if filter_domains is true
$build_domains		=	true;						# Show domain by domain statistics
$build_system		=	true;						# Show system statistics
$accept_query		=	true;						# Accept domain list via query string (adds to $domain_list)
													# in format ?domains=domain1.com,domain2.com,domain3.com
$format_numbers		=	true;						# Format numbers with comma's to indicate thousands

$gzip_compression	=	true;						# Enable gzip compression

$system_stats_top	=	false;						# Show system statistics above domain statistics

$stylesheet			=	$_SERVER['PHP_SELF']."?get=css";	# Default StyleSheet.

$table_width		=	"75%";						# Content table width
$table_align		=	"center";					# Content table alignment

$logo_file			=	"/icons/other/awstats_logo6.png";	# Path to the AWSTATS logo

#################################
# DEFAULT STYLESHEET SETTINGS	#
#################################
#  CAN BE IGNORED IF USING AN	#
#     EXTERNAL STYLESHEET		#
#################################
#    Use color name or hex		#
#################################

# Text Colors 
$domain_color		=	"black";
$pages_color		=	"red";
$hits_color			=	"blue";
$bandwidth_color	=	"green";
$visits_color		=	"navy";

# Row Colors (Lighter and Darker)
$first_bg			=	"#EEEEEE";	
$second_bg			=	"#DCDCDC";
$header_bg			=	"#CCCCCC";

# Fonts
$font_family		=	"Arial,helvetica,sans-serif";
$font_size			=	"10pt";
$head_font_size		=	"16pt";
$head_font_color	=	"black";
$font_color			=	"black";


###########################################################
# YOU SHOULD NOT NEED TO EDIT ANYTHING BELOW THIS LINE    #
###########################################################

# Script Information
$version 	=	"0.6";

# Check and enable gzip if requested
if($gzip_compression == true && function_exists("gzopen")) {
	ob_start("ob_gzhandler");	# Start output buffering with gzip compression
} else {
	ob_start();					# Start output buffering without compression
}

if($_GET['get'] == "css") {
	header('Content-type: text/css');
	echo '* { 
	font-family: '.$font_family.'; 
	font-size: '.$font_size.'; 
}
h1 { 
	font-size: '.$head_font_size.'; 
	color: '.$head_font_color.';
}
a, a:visited,a:link { 
	color: black; 
	text-decoration: none;
	font-weight: bold;
}
a:hover { 
	color: navy; 
}
tr.header {
	background-color: '.$header_bg.';
}
tr.first {
	background-color: '.$first_bg.';
}
tr.second {
	background-color: '.$second_bg.';
}	
td.pages {
	color: '.$pages_color.';
	text-align: right;
}
td.hits {
	color: '.$hits_color.';
	text-align: right;
}
td.visits {
	color: '.$visits_color.';
	text-align: right;
}
td.bandwidth {
	color: '.$bandwidth_color.';
	text-align: right;
}
td.domain {
	color: '.$domain_color.';
}

td.pages-bold {
	color: '.$pages_color.';
	text-align: right;
	font-weight: bold;
}
td.hits-bold {
	color: '.$hits_color.';
	text-align: right;
	font-weight: bold;
}
td.visits-bold {
	color: '.$visits_color.';
	text-align: right;
	font-weight: bold;
}
td.bandwidth-bold {
	color: '.$bandwidth_color.';
	text-align: right;
	font-weight: bold;
}
td.domain-bold {
	color: '.$domain_color.';
	font-weight: bold;
}';
	exit;
}

# Record Starting Time
$stime = gettime();

# Replace title if needed
$title = str_replace('%DOMAINS',$_GET['domains'],$title);
$title = str_replace('%CURRENT',$_SERVER['HTTP_HOST'],$title);
$title = str_replace('%DATE',date("D F Y, g:ia"),$title);

# Build Domain List from Query (if enabled)
if($accept_query == true && strlen($_GET['domains']) > 0) {
	$get_domain_list = explode(",",$_GET['domains']);
	$domain_list = array_merge($domain_list, $get_domain_list);
}
# Clear 'reserved' variables
unset($domains);

# Initialize Sessions
session_start();

# Initialize all arrays
$files 			= 		array();
$sites 			=		array();
$stats 			= 		array();
$total			=		array();
$domaininfo		=		array();
$sitecount		=		0;

# Declare functions

#####################
# Timer Function	#
#####################
function gettime() { 
   list($usec, $sec) = explode(" ", microtime()); 
   return ((float)$usec + (float)$sec); 
}

#########################
# Text Functions		#
#########################

function read_file($file,$domain)
{
	global $history_dir; 												
	global $domaininfo;
	global $total;	
	global $BIGVAR;
	
	$filename = $history_dir."/".$file;
	$fd = fopen ($filename, "r");
	$contents = fread ($fd, filesize ($filename));
	$use = strpos($contents, "BEGIN_DAY"); 
	$newline = strpos($contents, "\n", $use);
	$end = strpos($contents, "END_DAY", $newline);
	$data .= substr($contents,$newline+1,$end-$newline-2);
	$data = explode("\n",$data);
	fclose ($fd);
	if($data[0] == "END_DAY") {
		unset($data);
	} else {
		foreach($data as $info)	{
			$dom_info = explode(" ",$info);
			if(count($dom_info) < 5) {
				break;
			}

			$yyyy = substr($dom_info[0],0,4);
			$mm = substr($dom_info[0],4,2);
			$dd = substr($dom_info[0],6,2); 

			# Record number of page views		
			$domaininfo[$domain][$yyyy][$mm]['pages'] += $dom_info[1];
			$total[$domain]['pages'] += $dom_info[1];
			# Record number of hits
			$domaininfo[$domain][$yyyy][$mm]['hits'] += $dom_info[2];
			$total[$domain]['hits'] += $dom_info[2];
			# Record ammount of traffic
			$domaininfo[$domain][$yyyy][$mm]['traffic'] += $dom_info[3];
			$total[$domain]['traffic'] += $dom_info[3];
			# Record number of visits
			$domaininfo[$domain][$yyyy][$mm]['visits'] += $dom_info[4];
			$total[$domain]['visits'] += $dom_info[4];	
		}	
	}
}

#########################
# XML Functions			#
#########################

# Initialize arrays
$xmldata = array();
$bigarray = array();

# Declare function
function read_xml($file,$domain) {
	global $xmldata;
	global $domaininfo;
	global $total;
	$xml_parser = xml_parser_create();
	
	if (!($fp = fopen($file, "r"))) {
		die("Could not open history file!");
	}
	
	$data = fread($fp, filesize($file));
	fclose($fp);
	
	# Move data into array
	xml_parse_into_struct($xml_parser, $data, $vals, $index);
	xml_parser_free($xml_parser);
	
	$level = array();
	foreach ($vals as $xml_elem) {
		if ($xml_elem['type'] == 'open') {
			if (array_key_exists('attributes',$xml_elem)) {
				list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
			} else {
				if($xml_elem['tag'] == "TR") {
				$level[$xml_elem['level']] = $countr;
				$prev = "";
				$countr++;
				$count3 = 0;			
				} 
				if($xml_elem['tag'] == "TD") {
					if(strlen($prev) > 0 ){ 
						$level[$xml_elem['level']] = $prev;
					} else {
						$level[$xml_elem['level']] = $count;
					}
					$prev = $xml_elem['tag'];
					$count++;
				}
				if($xml_elem['tag'] != "TD" && $xml_elem['tag'] != "TR"){
					$count = 0;
					$countr = 0;
					$prev = "";
					$level[$xml_elem['level']] = $xml_elem['tag'];
				}							
			}
		}
		if ($xml_elem['type'] == 'complete') {
			$start_level = 1;
			$php_stmt = '$xmldata';
			while($start_level < $xml_elem['level']) {
				$php_stmt .= '[$level['.$start_level.']]';
				$start_level++;
			}
			if($xml_elem['tag'] == "TD") {
				$php_stmt .= '[$count3] = $xml_elem[\'value\'];';
				$count3++;
			} else {
				$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
			}
			eval($php_stmt);
		}		
	}
	$month = substr($xmldata['XML']['day']['TABLE']['0']['1'],4,2);
	$year = substr($xmldata['XML']['day']['TABLE']['0']['1'],0,4);
	
	foreach($xmldata['XML']['day']['TABLE'] as $data) {
		$domaininfo[$domain][$year][$month]['pages'] += $data[2];
		$total[$domain]['pages'] += $data[2];
		$domaininfo[$domain][$year][$month]['hits'] += $data[3];
		$total[$domain]['hits'] += $data[3];
		$domaininfo[$domain][$year][$month]['traffic'] += $data[4];
		$total[$domain]['traffic'] += $data[4];
		$domaininfo[$domain][$year][$month]['visits'] += $data[5];
		$total[$domain]['visits'] += $data[5];
	}
}

#################################
# Number formatting function	#
#################################
function format($number) {
	global $format_numbers;
	
	if($format_numbers == false) {
		return $number;
	} else {
		return number_format($number);
	}
}

#############################
# Begin Processing Files	#
#############################

# Open History Directory
$dir = @opendir($history_dir);	

# Check if History Directory Exists
if(!$dir) { 
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Error Occured</title>
<link href="'.$stylesheet.'" rel="stylesheet" type="text/css"> 
</head>
<body>
<h1>Error</h1>
<br>Incorrect History File Path. Remember to set $history_dir to the correct path.
</body>
</html>'; 
	exit; 
} 

# Begin sorting and analyzing history files
while(($file = readdir($dir)) !== false) { 
    if(substr_count($file, "awstats") == 0 && strlen($file) >= 14 || substr_count($file,".") == 0 || $file == "." || $file == "..") continue;				# Drop all files except history files 
    { 
        $domname = substr($file,14);								# Find Domain Name
		$domname = substr($domname,0,-4);							# And remove trailing

		//print($filter_year);
		if(! $filter_year && $filter_domains == true && in_array($domname,$domain_list) == true) {
			# CHECKED
			$files[] = $file; 
		}		
		elseif($filter_year && $filter_domains == false && substr_count($file,$filter_year) == 1) {
			# CHECKED
			$files[] = $file; 
		} 
		elseif($filter_year && $filter_domains == true && substr_count($file,$filter_year) == 1 && in_array($domname,$domain_list) == true) {
			# CHECKED
			$files[] = $file; 
		}
		elseif($filter_year && substr_count($file,$filter_year) == 1 && $filter_domains == false) {		
			# CHECKED
			$files[] = $file; 	
		}
    } 
}

# Check if there are any valid files, otherwise exit
if(count($files) == 0) {
	$output_table = '
	<div align="'.$table_align.'">Sorry, No Domains Found.</div>';
} else {
	# Sort the files in ascending order and then reset the list of sites
	sort($files);														
	reset($sites);														
	
	# Check for file type
	$curr = 0;
	while($curr < count($files)) {
		$file = $history_dir.'/'.$files[$curr];
		$check = substr(file_get_contents($file),0,4);
		if($check == "<xml") {
			# hmm, looks like its xml
			$domain = substr($files[$curr],14,-4);
			$domains[] = $domain;
			
			# Read the xml data file
			//read_xml($file,$domain);
		} else {
			# Scan all files
			$month = substr($files[$curr], 7, 2);
			$year = substr($files[$curr],9,4);	
			$domain = substr($files[$curr],14,-4);				
			
			$domains[] = $domain;
			# Check if we are filtering the domains
			if($filter_domains == true && array_search($domain,$domain_list) == false) {
				# Read the source file
				read_file($files[$curr],$domain);
			} else {
				# Read the source file
				read_file($files[$curr],$domain);
			}
			# Remove Duplicate Domains and resort
			$domains = array_unique($domains);
			sort($domains);
		}
		$curr++;
	}
	
	##############################
	# Start building the report  #
	##############################
	if($build_domains == true) {
		if(! $filter_year) {
			ksort($domaininfo);
			foreach($domaininfo as $key => $ddata) {
				ksort($ddata);
				$output_table .= '<table width="'.$table_width.'" cellspacing="0" cellpadding="1" align="'.$table_align.'">
  <tr class="header">
    <td width="40%" class="domain-bold">Domain: '.$key.'</td>
    <td width="15%" class="pages-bold">Pages:</td>
    <td width="15%" class="hits-bold">Hits:</td>
    <td width="15%" class="visits-bold">Visits:</td>
    <td width="15%" class="bandwidth-bold">Bandwidth:</td>
  </tr>';
				foreach($ddata as $key2 => $data) {
					foreach($data as $key3 => $ata) {
					if($i % 2) {			# Alternate colors
						$bgc = "first";		# Use first bg color
					} else {
						$bgc = "second";	# Use second bg color
					}	
					if($domaininfo[$key][$key2][$key3]['traffic'] > 1073741824) {	# Over 1GB
						$traffic = sprintf("%.2f",$domaininfo[$key][$key2][$key3]['traffic']/1024/1024/1024).' GB';
					} elseif($domaininfo[$key][$key2][$key3]['traffic'] > 1048576) { # Over 1MB
						$traffic = sprintf("%.2f",$domaininfo[$key][$key2][$key3]['traffic']/1024/1024).' MB';
					} else { # Under 1MB
						$traffic = sprintf("%.2f",$domaininfo[$key][$key2][$key3]['traffic']/1024).' KB';			
					}					
					$output_table .= '  <tr class="'.$bgc.'">
    <td class="domain">&nbsp;'.date("F", mktime (0,0,0,$key3+1,0,$key2)).' '.$key2.'</td>
    <td class="pages">'.format($domaininfo[$key][$key2][$key3]['pages']).'</td>
    <td class="hits">'.format($domaininfo[$key][$key2][$key3]['hits']).'</td>
    <td class="visits">'.format($domaininfo[$key][$key2][$key3]['visits']).'</td>
    <td class="bandwidth">'.$traffic.'</td>
  </tr>';
					$i++;
					}
				}
				if($total[$key]['traffic'] > 1073741824) {	# Over 1GB
					$traffic = sprintf("%.2f",$total[$key]['traffic']/1024/1024/1024).' GB';
				} elseif($total[$key]['traffic'] > 1048576) { # Over 1MB
					$traffic = sprintf("%.2f",$total[$key]['traffic']/1024/1024).' MB';
				} else { # Under 1MB
					$traffic = sprintf("%.2f",$total[$key]['traffic']/1024).' KB';			
				}
				
				$output_table .= '  <tr><td colspan=5><hr></td>
  </tr>
  <tr class="header">
    <td class="domain-bold">TOTAL:</td>
    <td class="pages-bold">'.format($total[$key]['pages']).'</td>
    <td class="hits-bold">'.format($total[$key]['hits']).'</td>
    <td class="visits-bold">'.format($total[$key]['visits']).'</td>
    <td class="bandwidth-bold">'.$traffic.'</td>
  </tr>
  <tr>
    <td colspan="5"><BR><BR><BR></td>
  </tr>
</table>';
			}
		} else {
			$thisyear = $filter_year;
			ksort($domaininfo);
			foreach($domaininfo as $key => $ddata) {
				ksort($ddata);
				$output_table .= '<table width="'.$table_width.'" cellspacing="0" cellpadding="1" align="'.$table_align.'">
  <tr class="header">
    <td width="40%" class="domain-bold">Domain: '.$key.'</td>
    <td width="15%" class="pages-bold">Pages:</td>
    <td width="15%" class="hits-bold">Hits:</td>
    <td width="15%" class="visits-bold">Visits:</td>
    <td width="15%" class="bandwidth-bold">Bandwidth:</td>
  </tr>';
				foreach($ddata as $key2 => $data) {
					foreach($data as $key3 => $ata) {
					if($i % 2) {
						$bgc = "first";
					} else {
						$bgc = "second";
					}
					if($domaininfo[$key][$thisyear][$key3]['traffic'] > 1073741824) {	# Over 1GB
						$traffic = sprintf("%.2f",$domaininfo[$key][$thisyear][$key3]['traffic']/1024/1024/1024).' GB';
					} elseif($domaininfo[$key][$thisyear][$key3]['traffic'] > 1048576) { # Over 1MB
						$traffic = sprintf("%.2f",$domaininfo[$key][$thisyear][$key3]['traffic']/1024/1024).' MB';
					} else { # Under 1MB
						$traffic = sprintf("%.2f",$domaininfo[$key][$thisyear][$key3]['traffic']/1024).' KB';			
					}						
					$output_table .= '  <tr class="'.$bgc.'">
    <td class="domain">&nbsp;'.date("F", mktime (0,0,0,$key3+1,0,$thisyear)).' '.$thisyear.'</td>
    <td class="pages">'.format($domaininfo[$key][$thisyear][$key3]['pages']).'</td>
    <td class="hits">'.format($domaininfo[$key][$thisyear][$key3]['hits']).'</td>
    <td class="visits">'.format($domaininfo[$key][$thisyear][$key3]['visits']).'</td>
    <td class="bandwidth">'.$traffic.'</td>
  </tr>';
					$i++;
					}
				}
				if($total[$key]['traffic'] > 1073741824) {	# Over 1GB
					$traffic = sprintf("%.2f",$total[$key]['traffic']/1024/1024/1024).' GB';
				} elseif($total[$key]['traffic'] > 1048576) { # Over 1MB
					$traffic = sprintf("%.2f",$total[$key]['traffic']/1024/1024).' MB';
				} else { # Under 1MB
					$traffic = sprintf("%.2f",$total[$key]['traffic']/1024).' KB';			
				}				
				$output_table .= '  <tr><td colspan=5><hr></td></tr>
  <tr class="header">
    <td class="domain-bold">TOTAL:</td>
    <td class="pages-bold">'.format($total[$key]['pages']).'</td>
    <td class="hits-bold">'.format($total[$key]['hits']).'</td>
    <td class="visits-bold">'.format($total[$key]['visits']).'</td>
    <td class="bandwidth-bold">'.$traffic.'</td>
  </tr>
  <tr>
    <td colspan="5"><BR><BR><BR></td>
  </tr>
</table>';
			}
		}
	}
	#####################################
	# Check and build system statistics table.  #
	#####################################
	if($build_system == true) {
		# Calculate totals
		$server = array();
		ksort($domaininfo);
		foreach($domaininfo as $key1 => $data1) {
			ksort($data1);
			foreach($data1 as $key2 => $data2) {
				foreach($data2 as $key3 => $data3) {
					foreach($data3 as $key4 => $data4) {
						$server[$key2][$key3][$key4] += (float)$data4;
						$total2[$key4] += (float)$data4;
					}
				}
			}
		}
		
		$system_table = '<table width="'.$table_width.'" cellspacing="0" cellpadding="1" align="'.$table_align.'">
  <tr class="header">
    <td width="40%" class="domain-bold">System Statistics</td>
    <td width="15%" class="pages-bold">Pages:</td>
    <td width="15%" class="hits-bold">Hits:</td>
    <td width="15%" class="visits-bold">Visits:</td>
    <td width="15%" class="bandwidth-bold">Bandwidth:</td>
  </tr>';
		ksort($server);	
		foreach($server as $key1 => $data1) {
			ksort($data1);
			foreach($data1 as $key2 => $data2) {
				if($i % 2) {
					$bgc = "first";
				} else {
					$bgc = "second";
				}
				if($server[$key1][$key2]['traffic'] > 1073741824) {	# Over 1GB
					$traffic = sprintf("%.2f",$server[$key1][$key2]['traffic']/1024/1024/1024).' GB';
				} elseif($server[$key1][$key2]['traffic'] > 1048576) { # Over 1MB
					$traffic = sprintf("%.2f",$server[$key1][$key2]['traffic']/1024/1024).' MB';
				} else { # Under 1MB
					$traffic = sprintf("%.2f",$server[$key1][$key2]['traffic']/1024).' KB';			
				}				
				$system_table .= '  <tr class="'.$bgc.'">
    <td class="domain">&nbsp;'.date("F", mktime (0,0,0,$key2+1,0,$key1)).' '.$key1.'</td>
    <td class="pages">'.format($server[$key1][$key2]['pages']).'</td>
    <td class="hits">'.format($server[$key1][$key2]['hits']).'</td>
    <td class="visits">'.format($server[$key1][$key2]['visits']).'</td>
    <td class="bandwidth">'.$traffic.'</td>
  </tr>';
		$i++;
			}
		}
		if($total2['traffic'] > 1073741824) {	# Over 1GB
			$traffic = sprintf("%.2f",$total2['traffic']/1024/1024/1024).' GB';
		} elseif($total2['traffic'] > 1048576) { # Over 1MB
			$traffic = sprintf("%.2f",$total2['traffic']/1024/1024).' MB';
		} else { # Under 1MB
			$traffic = sprintf("%.2f",$total2['traffic']/1024).' KB';			
		}		
		$system_table .= '  <tr><td colspan=5><hr></td></tr>
  <tr class="header">
    <td class="domain-bold">TOTAL:</td>
    <td class="pages-bold">'.format($total2['pages']).'</td>
    <td class="hits-bold">'.format($total2['hits']).'</td>
    <td class="visits-bold">'.format($total2['visits']).'</td>
    <td class="bandwidth-bold">'.$traffic.'</td>
  </tr>
  <tr>
    <td colspan="5"><BR><BR><BR></td>
  </tr>
</table>';
	}
}

# Record Completion Time
$etime = gettime();

# Format HTML
$html =	'';
$title = '<div align="'.$table_align.'"><h1>'.$title.'</h1></div>';
# Format CSS
# Piece it all together
if($system_stats_top == true) {
	$statistics = $html.$title.$system_table.$output_table.$html2;
} else {
	$statistics = $html.$title.$output_table.$system_table.$html2;
}
#	Output to the screen
echo $statistics;

llxFooter('$Date: 2008/03/30 18:50:14 $ - $Revision: 1.2 $');
?>