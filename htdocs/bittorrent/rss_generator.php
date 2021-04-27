<?php

require_once "funcsv2.php";

//This script runs whenever:
//1) a torrent is added to the database
//2) a torrent is deleted from the database
//3) the config.php file is edited
//This is to ensure that the correct rss.xml file is available and generated

//connect to database
$db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Cannot connect to database. Check your username and password in the config file.</p>");
mysql_select_db($database) or die(errorMessage() . "Error selecting database.</p>");
$query = "SELECT filename,url,size,pubDate FROM ".$prefix."namemap";
$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");

//if there are no entries in database or RSS feed is disabled in config.php file, delete rss.xml file
if (mysql_num_rows($results) == 0 || $enablerss == false) {
	if (file_exists(DOL_DATA_ROOT."/bittorrent/rss/rss.xml")) //make sure file exists before trying to delete
		unlink(DOL_DATA_ROOT."/bittorrent/rss/rss.xml") or die("Can't delete rss.xml file using unlink().  Are you running the server under Windows?");
} else //otherwise, generate new rss.xml file
{
	$fd = fopen(DOL_DATA_ROOT."/bittorrent/rss/rss.xml", "w") or die(errorMessage() . "Error: Unable to write to rss.xml file!</p>");
	$start_text =
	"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
	"<rss version=\"2.0\">\n" .
	"<channel>\n" .
	"<title>" . clean($rss_title) . "</title>\n" .
	"<link>" . $rss_link . "</link>\n" .
	"<description>" . clean($rss_description) . "</description>\n" .
	"<lastBuildDate>" . date('D, j M Y h:i:s') . " " . $timezone . "</lastBuildDate>\n";

	while ($row = mysql_fetch_row($results)) {
		//figure out full torrent URL
		$url = $website_url;
		$url = str_replace("newtorrents.php", "", $url);
		$url = str_replace("editconfig.php", "", $url);
		$url = str_replace("deleter.php", "", $url);
		$url = $url . "/bittorrent/torrents/" . $row[0] . ".torrent";
		$url = str_replace(" ", "%20", $url);

		//figure out file(s) size
		$file_size = bytesToString($row[2]);

		//go through each entry in database
		$middle_text = $middle_text . "<item>\n" .
		"<title>" . $row[0] . " (" . $file_size . ")</title>\n" .
		"<description>" . $row[0] . " (" . $file_size . ") " . $row[1] . "</description>\n" .
		"<pubDate>" . $row[3] . " " . $timezone . "</pubDate>\n" .
		"<guid>" . $url . "</guid>\n" .
		"<link>" . $url . "</link>\n" .
		"<enclosure url=\"" . $url . "\" length=\"" . filesize(DOL_DATA_ROOT."/bittorrent/torrents/" . $row[0] . ".torrent") . "\" type=\"application/x-bittorrent\" />\n" .
		"</item>\n";
	}

	$end_text = "</channel>\n</rss>";

	fwrite($fd, $start_text . $middle_text . $end_text);
	fclose($fd);
}
