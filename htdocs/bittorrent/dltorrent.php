<?php

$res=@include "../master.inc.php";
if (! $res) @include "../../../dolibarr/htdocs/master.inc.php";	// Used on dev env only

require_once "config.php";


//if hash isn't of length 40, don't even bother connecting to database
if (strlen($_GET['hash']) != 40) {
	header("index.php");
	exit();
}

require_once "funcsv2.php"; //required for errorMessage()


//connect to database and turn hash value into a filename
if ($GLOBALS["persist"])
	$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
else $db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - " . mysql_error() . "</p>");
$query = "SELECT filename FROM ".$prefix."namemap WHERE info_hash = '" . $_GET['hash'] . "'";
$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
$row = mysql_fetch_row($results);

if ($row[0] == null) {
	//hash doesn't exist in database, error out
	header("Location: index.php");
	exit();
} else $filename = $row[0];



if (!file_exists(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent")) {
	header("Location: index.php");
	exit();
}

$stat = stat(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent");
header("Content-Type: application/x-bittorrent");
header("Content-Length: " . $stat[7]);
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $stat[9]) . " GMT");
header("Content-Disposition: attachment; filename=\"" . $filename . ".torrent\"");
header('Pragma: no-cache');
header('Cache-Control: no-cache, no-store, must-revalidate');
readfile(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent");
