<?php
include "./pre.inc.php";
require_once "funcsv2.php";

$tracker_url = $website_url . substr($_SERVER['REQUEST_URI'], 0, -15) . "announce.php";

llxHeader('', 'BitTorrent', $website_url.'/bittorrent/docs/help.html');

$form=new Form($db);

print_fiche_titre('Tracker user statistics');

?>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="POST">
Filename Search: <input type="text" name="filename_search" size="40"<?php if (isset($_POST["filename_search"]))echo " value=\"" . $_POST["filename_search"] . "\"";?>> &nbsp;
<input type="submit" class="button" value="<?php echo $langs->trans("Filter"); ?>">
</form>
<br>

<?php
require_once "config.php";
require_once "funcsv2.php";

//connect to database and grab each torrent in database
if ($GLOBALS["persist"])
	$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
else $db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - " . mysql_error() . "</p>");

//Display search information
if (isset($_POST["filename_search"]) && $_POST["filename_search"] != "") {
	echo "<h2 align=\"center\">Search Results:</h2>";
	$query = "SELECT * FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash WHERE ".$prefix."namemap.filename REGEXP \"$_POST[filename_search]\" ORDER BY ".$prefix."namemap.filename";
} else //display everything
{
	$scriptname = $_SERVER["PHP_SELF"] . "?";

	if (!isset($_GET["activeonly"]))
		echo "<a href=\"$scriptname" . "activeonly=yes\">Show only torrents with seeders/leechers</a>\n";
	else {
		echo "<a href=\"$scriptname\">Show all torrents</a>\n";
		$scriptname = $scriptname . "activeonly=yes&";
	}

	if (isset($_GET["activeonly"]))
		$where = " WHERE leechers+seeds > 0";
	else $where = " ";

	$query = "SELECT COUNT(*) FROM ".$prefix."summary $where";
	$results = mysql_query($query);
	$res = mysql_result($results, 0, 0);

	echo "<p align='center'>Page: \n";
	$count = 0;
	$page = 1;
	while ($count < $res) {
		if (isset($_GET["page_number"]) && $page == $_GET["page_number"])
			echo "<b><a href=\"$scriptname" . "page_number=$page\">($page)</a></b> &nbsp; \n";
		elseif (!isset($_GET["page_number"]) && $page == 1)
			echo "<b><a href=\"$scriptname" . "page_number=$page\">($page)</a></b> &nbsp; \n";
		else echo "<a href=\"$scriptname" . "page_number=$page\">$page</a> &nbsp; \n";
		$page++;
		$count = $count + 5;
	}
	echo "</p>\n";

	if (!isset($_GET["page_number"]))
		$query = "SELECT * FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash $where ORDER BY ".$prefix."namemap.filename LIMIT 0,5";
	else {
		$page_limit = ($_GET["page_number"] - 1) * 5;
		$query = "SELECT * FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash $where ORDER BY ".$prefix."namemap.filename LIMIT $page_limit,5";
	}
}

$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");

while ($data = mysql_fetch_row($results)) {
	$xhash = "x" . $data[0];
	$query2 = "SELECT * FROM ".$prefix."$xhash";
	$results2 = mysql_query($query2) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");

	if (mysql_num_rows($results2) == 0 && isset($_GET["activeonly"]))
		break;
	else {
		echo "<hr><table>\n";
		echo "<tr><th>Info Hash</th><th>Filename</th><th>URL</th><th>File Size</th><th>Publication Date</th></tr>\n";
		echo "<tr><td>" . $data[0] . "</td><td>" . $data[11] . "</td><td>\n";
		if (Substr($data[12], 0, 7) == "http://")
			echo "<a href=\"" . $data[12] . "\">" . $data[12] . "</a>\n";
		else echo $data[12];
		echo "</td><td>" . bytesToString($data[13]) . "</td>\n";
		echo "<td>" . $data[14] . "</td></tr>\n";
		echo "</table>\n";
	}

	echo "<table>\n";
	echo "<tr><th class=\"subheader\">IP Address</th><th class=\"subheader\">Data Left to Download</th><th class=\"subheader\" width=200>Percent Finished</th><th class=\"subheader\">Port</th><th class=\"subheader\">Last Update</th><th class=\"subheader\">NAT User</th></tr>\n";
	while ($data2 = mysql_fetch_row($results2)) {
		//grab information on each user
		echo "<tr><td>" . $data2[2] . "</td>\n";
		echo "<td align=\"right\">" . bytesToString($data2[1]) . "</td>\n";

		//calculate percent done for user
		$percent_done = 1.00;
		if ($data2[1] != 0) { //only run calculation if they are still downloading
			$size_in_bytes = $data[13];
			if ($size_in_bytes == 0) //thou shalt not divide by zero
				$percent_done = 0;
			else $percent_done = round(($size_in_bytes - $data2[1]) / $size_in_bytes, 3);
		}

		?>
		<td>
		<table class="percentages" cellspacing="0">
		<tr>
		<td align="right" class="percent" width="<?php echo round($percent_done * 200, 0); ?>" height="15">
		<?php if ($percent_done > .5) echo $percent_done * 100 . "%"; ?>
		</td>
		<td align="right" class="percentleft" width="<?php echo 200 - round($percent_done * 200, 0); ?>" height="15">
		<?php if ($percent_done <= .5) echo $percent_done * 100 . "%"; ?>
		</td>
		</tr>
		</table>
		</td>
		<?php
		echo "<td>" . $data2[3] . "</td>\n"; //port
		echo "<td>" . date('g:ia m-d-Y', $data2[5]) . "</td>\n"; //last time check-in
		echo "<td align=\"center\">" . $data2[7] . "</td>\n"; //NAT user
		echo "</tr>\n";
	}
	echo "</table><br>\n";
}
echo "<hr>";
if (!isset($_POST["filename_search"])) {
	echo "<p align='center'>Page: \n";
	$count = 0;
	$page = 1;
	while ($count < $res) {
		if (isset($_GET["page_number"]) && $page == $_GET["page_number"])
		echo "<b><a href=\"$scriptname" . "page_number=$page\">($page)</a></b> &nbsp; \n";
		elseif (!isset($_GET["page_number"]) && $page == 1)
		echo "<b><a href=\"$scriptname" . "page_number=$page\">($page)</a></b> &nbsp; \n";
		else echo "<a href=\"$scriptname" . "page_number=$page\">$page</a> &nbsp; \n";
		$page++;
		$count = $count + 5;
	}
	echo "</p>\n";
}
?>

<a href="admin.php"><img src="images/admin.png" border="0" class="icon" alt="Admin Page" title="Admin Page" /></a><a href="admin.php">Return to Admin Page</a>

<?php
llxFooter();
?>
