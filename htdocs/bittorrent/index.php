<?php

include "./pre.inc.php";
require_once "funcsv2.php";


/*
*	View
*/

$tracker_url = $website_url . substr($_SERVER['REQUEST_URI'], 0, -15) . "announce.php";

llxHeader('', 'BitTorrent', $website_url.'/bittorrent/docs/help.html');

$form=new Form($db);

//variables for column totals
$total_disk_usage = 0;
$total_seeders = 0;
$total_leechers = 0;
$total_downloads = 0;
$total_bytes_transferred = 0;
$total_speed = 0;

$scriptname = $_SERVER["PHP_SELF"] . "?";
if (!isset($GLOBALS["countbytes"]))
	$GLOBALS["countbytes"] = true;

//display total stats as header on page
if ($GLOBALS["persist"])
	$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
else $db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - " . mysql_error() . "</p>");

$query = "SELECT SUM(".$prefix."namemap.size), SUM(".$prefix."summary.seeds), SUM(".$prefix."summary.leechers), SUM(".$prefix."summary.finished), SUM(".$prefix."summary.dlbytes), SUM(".$prefix."summary.speed) FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash";
$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
$data = mysql_fetch_row($results);


if ($GLOBALS["title"] != "") $titre=$GLOBALS["title"];
else $titre="Tracker Statistics";

print_fiche_titre($titre);
?>

<center>
<table>
<tr>
<th class="subheader">Total Space Used</th>
<th class="subheader">Seeders</th>
<th class="subheader">Leechers</th>
<th class="subheader">Completed D/Ls</th>
<th class="subheader">Bytes Transferred</th>
<th class="subheader">Speed (rough estimate)</th>
</tr>
<tr>
<?php
if ($data[0] != null) { //if there are no torrents in database, don't show anything
	echo "<td align=\"center\">" . bytesToString($data[0]) . "</td>\n";
	echo "<td align=\"center\">" . $data[1] . "</td>\n";
	echo "<td align=\"center\">" . $data[2] . "</td>\n";
	echo "<td align=\"center\">" . $data[3] . "</td>\n";
	echo "<td align=\"center\">" . bytesToString($data[4]) . "</td>\n";
	if ($GLOBALS["countbytes"]) { //stop count bytes OFF, OK to do speed calculation
		if ($data[5] > 2097152)
			echo "<td align=\"center\">" . round($data[5] / 1048576, 2) . " MB/sec</td>\n";
		else echo "<td align=\"center\">" . round($data[5] / 1024, 2) . " KB/sec</td>\n";
	} else echo "<td align=\"center\">No Info Available</td>\n";
}
?>
</tr>
</table>
</center>
<br>

<table width="100%">
<tr>
<td width="25%">
</td>
<td align="center">
</td>
<td align="right" width="25%">

<?php
if (file_exists("rss/rss.xml")) {
	echo "<a href='rss/rss.xml'><img src='images/rss-logo.png' border='0' class='icon' alt='RSS 2.0 Feed' title='RSS 2.0 Feed' /></a><a href='rss/rss.xml'>RSS 2.0 Feed</a>";
}
?>
</td>
</tr>
</table>


<table width="100%">
<tr>
	<?php
	if (!isset($_GET["activeonly"]))
		$scriptname = $scriptname . "activeonly=	yes&amp;";
	if (isset($_GET["seededonly"]) && !isset($_GET["activeonly"])) {
		$scriptname = $scriptname . "seededonly=yes&";
		$_GET["page_number"] = 1;
	}
	if (isset($_GET["page_number"]))
		$scriptname = $scriptname . "page_number=" . $_GET["page_number"] . "&amp;";

	if (isset($_GET["activeonly"]))
		echo "<td><a href=\"$scriptname\">Show all torrents</a></td>\n";
	else echo "<td><a href=\"$scriptname\">Show only active torrents</a></td>\n";

	$scriptname = $_SERVER["PHP_SELF"] . "?";

	if (!isset($_GET["seededonly"]))
		$scriptname = $scriptname . "seededonly=yes&amp;";
	if (isset($_GET["activeonly"]) && !isset($_GET["seededonly"])) {
		$scriptname = $scriptname . "activeonly=yes&";
		$_GET["page_number"] = 1;
	}
	if (isset($_GET["page_number"]))
		$scriptname = $scriptname . "page_number=" . $_GET["page_number"] . "&amp;";

	if (isset($_GET["seededonly"]))
		echo "<td align=\"right\"><a href=\"$scriptname\">Show all torrents</a></td>\n";
	else echo "<td align=\"right\"><a href=\"$scriptname\">Show only seeded torrents</a></td>\n";

	$scriptname = $_SERVER["PHP_SELF"] . "?";

	?>
</tr>
</table>

<?php
if ($GLOBALS["persist"])
	$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
else $db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - " . mysql_error() . "</p>");

if (isset($_GET["seededonly"]))
	$where = " WHERE seeds > 0";
elseif (isset($_GET["activeonly"]))
	$where = " WHERE leechers+seeds > 0";
else $where = " ";

$query = "SELECT COUNT(*) FROM ".$prefix."summary $where";
$results = mysql_query($query);
$res = mysql_result($results, 0, 0);

if (isset($_GET["activeonly"]))
	$scriptname = $scriptname . "activeonly=yes&";
if (isset($_GET["seededonly"]))
	$scriptname = $scriptname . "seededonly=yes&";

echo "<p align='center'>Page: \n";
$count = 0;
$page = 1;
while ($count < $res) {
	if (isset($_GET["page_number"]) && $page == $_GET["page_number"])
		echo "<b><a href=\"$scriptname" . "page_number=$page\">($page)</a></b> &nbsp;\n";
	elseif (!isset($_GET["page_number"]) && $page == 1)
		echo "<b><a href=\"$scriptname" . "page_number=$page\">($page)</a></b> &nbsp;\n";
	else echo "<a href=\"$scriptname" . "page_number=$page\">$page</a> &nbsp;\n";
	$page++;
	$count = $count + 10;
}
echo "</p>\n";
?>

<table width="100%">
<tr>
	<td>
	<table class="torrentlist" width="100%">

	<!-- Column Headers -->
	<tr>
		<th>Name/Info Hash</th>
		<th>Size</th>
		<th>Seeders</th>
		<th>Leechers</th>
		<th>Completed D/Ls</th>
		<?php
		// Bytes mode off? Ignore the columns
		if ($GLOBALS["countbytes"])
			echo '<th>Bytes Transferred</th><th>Speed (rough estimate)</th>';
		?>
	</tr>

<?php
if (!isset($_GET["page_number"]))
	$query = "SELECT ".$prefix."summary.info_hash, ".$prefix."summary.seeds, ".$prefix."summary.leechers, format(".$prefix."summary.finished,0), ".$prefix."summary.dlbytes, ".$prefix."namemap.filename, ".$prefix."namemap.url, ".$prefix."namemap.size, ".$prefix."summary.speed FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash $where ORDER BY ".$prefix."namemap.filename LIMIT 0,10";
else {
	if ($_GET["page_number"] <= 0) //account for possible negative number entry by user
		$_GET["page_number"] = 1;

	$page_limit = ($_GET["page_number"] - 1) * 10;
	$query = "SELECT ".$prefix."summary.info_hash, ".$prefix."summary.seeds, ".$prefix."summary.leechers, format(".$prefix."summary.finished,0), ".$prefix."summary.dlbytes, ".$prefix."namemap.filename, ".$prefix."namemap.url, ".$prefix."namemap.size, ".$prefix."summary.speed FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash $where ORDER BY ".$prefix."namemap.filename LIMIT $page_limit,10";
}

$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
$i = 0;

while ($data = mysql_fetch_row($results)) {
	// NULLs are such a pain at times. isset($nullvar) == false
	if (is_null($data[5]))
		$data[5] = $data[0];
	if (is_null($data[6]))
		$data[6] = "";
	if (is_null($data[7]))
		$data[7] = "";
	if (strlen($data[5]) == 0)
		$data[5] = $data[0];
	$myhash = $data[0];
	$writeout = "row" . $i % 2;
	echo "<tr class=\"$writeout\">\n";

	// File
	echo "\t<td>";
	echo "\t<table class=\"nopadding\" border=\"0\"><tr><td valign=\"top\" align=\"left\" width=\"10%\">\n";
	echo "\t<form method='post' action='torrent_functions.php'>\n";
	echo "\t<input type='hidden' name='hash' value='" . $data[0] . "'/>\n";
	echo "\t<input type='submit' value='".$langs->trans("Infos")."'/></form>\n";
	echo "\t</td><td valign=\"top\" align=\"left\">\n";
	if (strlen($data[6]) > 0)
		echo "<a href=\"${data[6]}\">${data[5]}</a> - ";
	else echo $data[5] . " - ";


	echo "<a href=\"dltorrent.php?hash=" . $myhash . "\">  (Download Torrent)</a></td></tr>";
	//echo "<a href=\"torrents/" . rawurlencode($data[5]) . ".torrent\">  (Download Torrent)</a></td></tr>";

	echo "</table></td>\n";

	echo "<td align=\"right\">";
	if (strlen($data[7]) > 0) { //show file size
		echo bytesToString($data[7]);
		$total_disk_usage = $total_disk_usage + $data[7]; //total up file sizes
	}
	echo "</td>";

	for ($j=1; $j < 4; $j++) { //show seeders, leechers, and completed downloads
		echo "\t<td align=\"right\">$data[$j]</td>\n";
		if ($j == 1) //add to total seeders
			$total_seeders = $total_seeders + $data[1];
		if ($j == 2) //add to total leechers
			$total_leechers = $total_leechers + $data[2];
		if ($j == 3) //add to completed downloads
			$total_downloads = $total_downloads + $data[3];
	}

	if ($GLOBALS["countbytes"]) {
		echo "\t<td align=\"right\">" . bytestoString($data[4]) . "</td>\n";
		$total_bytes_transferred = $total_bytes_transferred + $data[4]; //add to total GB transferred

		// The SPEED column calculations.
		if ($data[8] <= 0) {
			$speed = "0";
			$total_speed = $total_speed - $data[8]; //for total speed column
		} elseif ($data[8] > 2097152)
			$speed = round($data[8] / 1048576, 2) . " MB/sec";
		else $speed = round($data[8] / 1024, 2) . " KB/sec";
		echo "\t<td align=\"right\">$speed</td>\n";
		$total_speed = $total_speed + $data[8]; //add to total speed, in bytes
	}
	echo "</tr>\n";
	$i++;
}

if ($i == 0)
	echo "<tr class=\"row0\"><td style=\"text-align: center;\" colspan=\"6\">No torrents</td></tr>";

//show totals in last row
echo "<tr>";
echo "<th>".$langs->trans("Total")."</th>";
echo '<th align="right">' . bytesToString($total_disk_usage) . "</th>";
echo '<th align="right">' . $total_seeders . "</th>";
echo '<th align="right">' . $total_leechers . "</th>";
echo '<th align="right">' . $total_downloads . "</th>";
if ($GLOBALS["countbytes"]) { //stop count bytes variable
	echo '<th align="right">' . bytestoString($total_bytes_transferred) . "</th>";
	if ($total_speed > 2097152)
		echo '<th align="right">' . round($total_speed / 1048576, 2) . " MB/sec</th>";
	else echo '<th align="right">' . round($total_speed / 1024, 2) . " KB/sec</th>";
}
echo "</tr>";
?>
	</table></td></tr>
</table>
<h3>Notes</h3>
<?php
if ($GLOBALS["NAT"])
	echo "<ul><li>This tracker does NAT checking when users connect. If you receive a probe to port 6881, it's probably this tracker.</li></ul>\n";
else echo "<ul><li>NAT checking has been disabled on this tracker.</li></ul>\n";

echo "<ul><li>Even if there are no seeders, the download may still work because of HTTP seeding.</li></ul>\n";

if (rand(1, 10) == 1) {
	//10% of the time, run sanity_no_output.php to prune database and keep users fresh
	include "sanity_no_output.php";
}

?>
<a href="admin.php"><img src="images/admin.png" border="0" class="icon" alt="Admin Page" title="Admin Page" /></a><a href="admin.php">Return to Admin Page</a>
<?php

llxFooter();
?>
