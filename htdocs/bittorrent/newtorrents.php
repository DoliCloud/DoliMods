<?php
include "./pre.inc.php";
require_once "funcsv2.php";



/*
 *	View
 */

$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', $dolibarr_main_url_root);
$tracker_url=$urlwithouturlroot.dol_buildpath('/bittorrent/announce.php', 1);
$helpurl=$urlwithouturlroot.dol_buildpath('/bittorrent/docs/help.html', 1);

llxHeader('', 'BitTorrent', $helpurl);

$form=new Form($db);



if (isset($_FILES["torrent"])) {
	addTorrent();

	llxFooter();
	exit;
} else {
	endOutput();
}


function addTorrent()
{
	global $urlwithouturlroot,$website_url,$dbhost,$dbuser,$dbpass,$database,$prefix;
	global $enablerss,$rss_title,$rss_link,$rss_description,$timezone;

	$tracker_url = $urlwithouturlroot . dol_buildpath('/bittorrent/announce.php', 1);

	$tracker_url_http  = preg_replace('/^https:/', 'http:', $tracker_url);
	$tracker_url_https = preg_replace('/^http:/', 'https:', $tracker_url);

	$hash = strtolower($_POST["hash"]);

	$db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Couldn't connect to the database, contact the administrator</p>");
	mysql_select_db($database) or die(errorMessage() . "Can't open the database.</p>");

	require_once "funcsv2.php";
	require_once "BDecode.php";
	require_once "BEncode.php";

	if ($_FILES["torrent"]["error"] != 4) {
		$fd = fopen($_FILES["torrent"]["tmp_name"], "rb") or die(errorMessage() . "File upload error 1</p>\n");
		is_uploaded_file($_FILES["torrent"]["tmp_name"]) or die(errorMessage() . "File upload error 2</p>\n");
		$alltorrent = fread($fd, filesize($_FILES["torrent"]["tmp_name"]));

		$array = BDecode($alltorrent);
		if (!$array) {
			echo errorMessage() . "Error: The parser was unable to load your torrent.  Please re-create and re-upload the torrent.</p>\n";
			endOutput();
			exit;
		}
		if (strtolower($array["announce"]) != $tracker_url_http && strtolower($array["announce"]) != $tracker_url_https) {
			echo errorMessage() . "Error: The tracker announce URL in .torrent (".$array["announce"].") does not match this tracker (".$tracker_url.")<br>Please re-create and re-upload the torrent.</p>\n";
			endOutput();
			exit;
		}
		if ($_POST["httpseed"] == "enabled" && $_POST["relative_path"] == "") {
			echo errorMessage() . "Error: HTTP seeding was checked however no relative path was given.</p>\n";
			endOutput();
			exit;
		}
		if ($_POST["httpseed"] == "enabled" && $_POST["relative_path"] != "") {
			if (Substr($_POST["relative_path"], -1) == "/") {
				if (!is_dir($_POST["relative_path"])) {
					echo errorMessage() . "Error: HTTP seeding relative path ends in / but is not a valid directory.</p>\n";
					endOutput();
					exit;
				}
			} else {
				if (!is_file($_POST["relative_path"])) {
					echo errorMessage() . "Error: HTTP seeding relative path is not a valid file.</p>\n";
					endOutput();
					exit;
				}
			}
		}
		if ($_POST["getrightseed"] == "enabled" && $_POST["httpftplocation"] == "") {
			echo errorMessage() . "Error: GetRight HTTP seeding was checked however no URL was given.</p>\n";
			endOutput();
			exit;
		}
		if ($_POST["getrightseed"] == "enabled" && (Substr($_POST["httpftplocation"], 0, 7) != "http://" && Substr($_POST["httpftplocation"], 0, 6) != "ftp://")) {
			echo errorMessage() . "Error: GetRight HTTP seeding URL must start with http:// or ftp://</p>\n";
			endOutput();
			exit;
		}
		$hash = @sha1(BEncode($array["info"]));
		fclose($fd);

		$target_path = DOL_DATA_ROOT."/bittorrent/torrents/";
		$target_path = $target_path . basename(clean($_FILES['torrent']['name']));
		$move_torrent = move_uploaded_file($_FILES["torrent"]["tmp_name"], $target_path);
		if ($move_torrent == false) {
			echo errorMessage() . "Unable to move " . $_FILES["torrent"]["tmp_name"] . " to ".DOL_DATA_ROOT."/bittorrent/torrents/</p>\n";
		}
	}


	if (isset($_POST["filename"]))
		$filename = clean($_POST["filename"]);
	else $filename = "";

	if (isset($_POST["url"]))
		$url = clean($_POST["url"]);
	else $url = "";

	if (isset($_POST["autoset"]))
	if (strcmp($_POST["autoset"], "enabled") == 0) {
		if (strlen($filename) == 0 && isset($array["info"]["name"]))
			$filename = $array["info"]["name"];
	}


	//figure out total size of all files in torrent
	$info = $array["info"];
	$total_size = 0;
	if (isset($info["files"])) {
		foreach ($info["files"] as $file) {
			$total_size = $total_size + $file["length"];
		}
	} else {
		$total_size = $info["length"];
	}

	//Validate torrent file, make sure everything is correct

	$filename = mysql_escape_string($filename);
	$filename = htmlspecialchars(clean($filename));
	$url = '';


	if ((strlen($hash) != 40) || !verifyHash($hash)) {
		echo errorMessage() . "Error: Info hash must be exactly 40 hex bytes.</p>\n";
		endOutput();
	}

	$query = "INSERT INTO ".$prefix."namemap (info_hash, filename, url, size, pubDate) VALUES (\"$hash\", \"$filename\", \"$url\", \"$total_size\", \"" . date('D, j M Y h:i:s') . "\")";
	$status = makeTorrent($hash, true);
	quickQuery($query);
	if ($status) {
		echo "<p class=\"success\">Torrent was added successfully.</p>\n";
		//rename torrent file to match filename
		rename(DOL_DATA_ROOT."/bittorrent/torrents/" . clean($_FILES['torrent']['name']), DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent");
		//make torrent file readable by all
		chmod(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent", 0644);

		//run RSS generator
		require_once "rss_generator.php";
		//Display information from DumpTorrentCGI.php
		require_once "torrent_functions.php";
	} else {
		echo errorMessage() . "There were some errors. Check if this torrent has been added previously.</p>\n";
		//delete torrent file if it doesn't exist in database
		$query = "SELECT COUNT(*) FROM ".$prefix."summary WHERE info_hash = '$hash'";
		$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
		$data = mysql_fetch_row($results);
		if ($data[0] == 0) {
			if (file_exists(DOL_DATA_ROOT."/bittorrent/torrents/" . $_FILES['torrent']['name']))
				unlink(DOL_DATA_ROOT."/bittorrent/torrents/" . $_FILES['torrent']['name']);
		}
		//make torrent file readable by all
		chmod(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent", 0644);
		endOutput();
	}
}

function endOutput()
{
	global $urlwithouturlroot,$website_url;

	$tracker_url = $urlwithouturlroot . dol_buildpath('/bittorrent/announce.php', 1);
	?>
	<div class="center">
	<?php
	print_fiche_titre('Add Torrent to Tracker Database');
	?>
	<h3>Tracker URL: <?php echo $tracker_url;?></h3>
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table>
	<tr>
		<td class="right">Torrent file:</td>
		<td class="left"><?php
		if (function_exists("sha1"))
			echo "<input type=\"file\" name=\"torrent\" size=\"50\"/>";
		else echo '<i>File uploading not available - no SHA1 function.</i>';
		?></td>
	</tr>
	<tr><td colspan="2"><hr></td></tr>
	<tr>
	<td class="center" colspan="2"><input type="checkbox" name="httpseed" value="enabled" disabled>Use BitTornado HTTP seeding specification (optional)</td>
	</tr>
	<tr>
	<td class="right" valign="top">Relative location of file or directory:<br></td>
	<td class="left"><input disabled type="text" name="relative_path" size="70"/><br>
	Example: ../../files/file.zip</td>
	</tr>
	<tr><td colspan="2"><hr></td></tr>
	<tr>
	<td class="center" colspan="2"><input type="checkbox" name="getrightseed" checked="checked" value="enabled">Use GetRight HTTP seeding specification (optional but highly recommanded to avoid to have a client running as a seed)</td>
	</tr>
	<tr>
	<td class="right" valign="top">FTP/HTTP URL of file or directory:<br>
	</td>
	<td class="left"><input type="text" name="httpftplocation" size="70"/><br>
	For example if file is myfile.zip on an external server:<br>
	http://myserver/myfile.zip<br>
	For example if file is myfile.zip inside <?php echo DOL_DATA_ROOT.'/bittorrent/files'; ?> directory:<br>
	<?php echo $urlwithouturlroot.dol_buildpath('/document.php', 1).'?modulepart=bittorrent&file=myfile.zip'; ?>
	</td>
	</tr>
	<tr><td colspan="2"><hr></td></tr>
	<?php if (function_exists("sha1"))
		echo "<tr><td class=\"center\" colspan=\"2\"><input type=\"checkbox\" name=\"autoset\" value=\"enabled\" checked=\"checked\" /> Fill in fields below automatically using data from the torrent file.</td></tr>\n";
	?>
	<tr>
		<td class="right">Info Hash:</td>
		<td class="left"><input type="text" name="hash" size="40"/></td>
	</tr>
	<tr>
		<td class="right">File name (optional): </td>
		<td class="left"><input type="text" name="filename" size="60" maxlength="200"/></td>
	</tr>
	<tr>
		<td class="right">Torrent's URL (optional): </td>
		<td class="left"><input type="text" name="url" size="60" maxlength="200"/></td>
	</tr>
	<tr><td colspan="2"><hr></td></tr>
	<tr>
		<td class="center" colspan="2"><input type="submit" value="Add Torrent to Database" class="button"/> - <input type="reset" value="Clear Settings" class="button"/></td>
	</tr>
	</table>
	<br>
	<input type="hidden" name="username" value="<?php echo $_POST['username']; ?>"/>
	<input type="hidden" name="password" value="<?php echo $_POST['password']; ?>"/>
	</form>
	<a href="admin.php"><img src="images/admin.png" border="0" class="icon" alt="Admin Page" title="Admin Page" /></a><a href="admin.php">Return to Admin Page</a>
	</div>
	<?php
	llxFooter();

	// Still in function endOutput()
	exit;
}
?>