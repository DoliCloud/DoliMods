<?php

require_once "BDecode.php";
require_once "BEncode.php";

function classicoutput($array, $infohash)
{

	if (isset($array["info"]["pieces"]))
		$array["info"]["pieces"] = "<i>Checksum data (" . strlen($array["info"]["pieces"]) / 20 . " pieces)</i>";

	echo "Info hash: <TT>$infohash</TT><br>";
	echo "<pre>";
	print_r(cleaner($array));
	echo "</pre>";
}

function announceoutput($array)
{
	if (!isset($array["peers"][0])) {
		echo "Not a tracker announce block. Falling back on classic.<br><br>";
		classicoutput($array, "(Not checked)");
		exit;
	}
	echo "<h2>Client configuration options</h2>";
	echo "<table border=0 cellpadding=2 cellspacing=2>";
	foreach ($array as $left => $right) {
		if ($left == "peers")
			continue;
		if (is_array($right))
			$myright = "<I>Error</I>";
		else $myright = $right;
		echo "<tr><td align=right>".$left."</td><td>=</td><td>".$myright."</td></tr>\n";
	}
	echo "</table><br><h2>Peers</h2><pre>";
	foreach ($array["peers"] as $data) {
		if (!is_array($data)) { // special case: [0] == true  means empty list
			echo "(Empty results)\n";
			break;
		}
		echo 		bin2hex($data["peer id"])." at ".$data["ip"].":".$data["port"]."\n";
	}
	echo "</pre>";
}


function stringcleaner($str)
{
	/* WARNING:

	It appears PHP doesn't handle null bytes in the key portion
	of string-indexed arrays. $array["abcd\0e"] = $something
	will find itself with only 4 letters in the key. This may
	cause some confusion when using /scrape, for example.

	*/

	$len = strlen($str);
	for ($i=0; $i < $len; $i ++) {
		if (ord($str[$i]) < 32 || ord($str[$i]) > 128)
			return "<B>".bin2hex($str)."</B>";
	}
	return $str;
}

function cleaner($array)
{
	if (!is_array($array))
		return $array;
	$newarray = array();
	foreach ($array as $left => $right) {
		if (is_string($left))
			$newleft = stringcleaner(stripslashes($left));
		else $newleft = $left;

		if (is_string($right))
			$newright = stringcleaner($right);
		elseif (is_array($right))
			$newright = cleaner($right);
		else $newright = $right;

		$newarray[$newleft] = $newright;
	}
	return $newarray;
}


if (isset($_POST["output"])) {
	if (!is_numeric($_POST["output"]))
		$output = -1;
	else $output = $_POST["output"];
	if ($output > 3 || $output < -1)
		$output = -1;
} elseif (isset($_GET["style"]))
	$output = $_GET["style"];
else $output = -1;

//used by index.php to show information on torrent
if (isset($_POST["hash"])) {
	if (!isset($status)) { //not coming from newtorrents page
		include "./pre.inc.php";
		require_once "funcsv2.php";

		llxHeader();

		$form=new Form($db);
	}
	//lookup file
	require_once "funcsv2.php";
	//connect to DB
	if ($GLOBALS["persist"])
		$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
	else $db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
	mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - " . mysql_error() . "</p>");
	$query = "SELECT filename FROM ".$prefix."namemap WHERE info_hash = '" . $_POST["hash"] . "'";
	$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
	$data = mysql_fetch_row($results);
	//find filename and set it
	$_FILES["torrent"]["tmp_name"] = DOL_DATA_ROOT."/bittorrent/torrents/" . $data[0] . ".torrent";
	if (!isset($status))
		echo "<h1>" . $data[0] . "</h1>";
}

//main displaying and processing
if (isset($_FILES["torrent"]) || isset($_POST["url"]) || isset($_GET["url"])) {
	if (strlen($_FILES["torrent"]["tmp_name"]) > 0 && file_exists($_FILES["torrent"]["tmp_name"])) { //for DumpTorrentCGI.php, and index.php
		$fd = fopen($_FILES["torrent"]["tmp_name"], "rb") or die(errorMessage() . "File upload error 1</p>");
		if (!isset($_POST["hash"]))
			is_uploaded_file($_FILES["torrent"]["tmp_name"]) or die(errorMessage() . "File upload error 2</p>");
		$alltorrent = fread($fd, filesize($_FILES["torrent"]["tmp_name"]));
		fclose($fd);
	} elseif (file_exists(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent")) { //for newtorrents.php
		$fd = fopen(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent", "rb") or die(errorMessage() . "File upload error 1</p>");
		$alltorrent = fread($fd, filesize(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent"));
		fclose($fd);
	} elseif (isset($_POST["url"])) {
		(strlen($_POST["url"]) > 0) or die(errorMessage() . "Logic error in script.</p>");
		if (strtolower(substr($_POST["url"], 0, 7)) != "http://")
			die(errorMessage() . "Error: you must specify \"http://\" as part of the URL.</p>");
		$fd = fopen($_POST["url"], "rb") or die(errorMessage() . "File download error.</p>");
		$alltorrent = "";
		while (!feof($fd)) {
			$alltorrent .= fread($fd, 4096);
			if (strlen($alltorrent) > 50000)
				die(errorMessage() . "File too large to download.</p>");
		}
		fclose($fd);
	} elseif (isset($_GET["url"])) {
		(strlen($_GET["url"]) > 0) or die(errorMessage() . "Logic error in script.</p>");
		if (strtolower(substr($_GET["url"], 0, 7)) != "http://")
				die(errorMessage() . "Error: you must specify \"http://\" as part of the URL</p>");
		$fd = fopen($_GET["url"], "rb") or die(errorMessage() . "File download error.</p>");
		$alltorrent = "";
		while (!feof($fd)) {
			$alltorrent .= fread($fd, 4096);
			if (strlen($alltorrent) > 50000)
					die(errorMessage() . "File too large to download.</p>");
		}
		fclose($fd);
	}
	$array = BDecode($alltorrent);
	if (!isset($array)) {
		echo errorMessage() . "There was an error handling your uploaded torrent. It may be corrupted. Are you sure it's of type .torrent?</p>";
		exit;
	}

	if ($array == false) {
		echo errorMessage() . "There was an error handling your uploaded torrent. It may be corrupted. Are you sure it's of type .torrent?</p>";
		exit;
	}

	// Making torrents look nice: If $array["info"] exists, it is used to calculate
	// an Info_hash value.

	$infohash = "<I>Not applicable</I>";
	if (isset($array["info"]))
	if (is_array($array["info"])) {
		if (function_exists("sha1"))
			$infohash = @sha1(BEncode($array["info"]));
		else $infohash = "(No SHA1 available to calculate info_hash)</TT><br>";

		// If the "pieces" section exists, it is replaced by some nice text.
		// The alternative is pages of garbage.
	}

	// Auto-detect file type
	if ($output == -1) {
		if (isset($array["announce"]) && isset($array["info"]))
			$output = 1;
		elseif (isset($array["files"]))
			$output = 2;
		elseif (isset($array["peers"]))
			$output = 3;
		else $output = 0;
	}

	// Output information.
	if ($output == 0) {
		classicoutput($array, $infohash);
	}

	if ($output == 1) {
		if (!isset($array["info"])) {
			echo "Error: not a torrent file. Falling back on classic.<br><br>";

			classicoutput($array, "<I>Not applicable</I>");
			exit;
		}

		echo "<br><h2>Non-file data:</h2>";
		echo "<table border=0 cellpadding=2 cellspacing=2><tr>";
		echo "<td align=right>Info hash</td><td>=</td><td><TT>$infohash</TT></td></tr>\n";

		echo "<tr><td align=right>Announce URL</td><td>=</td><td>".$array["announce"]."</td></tr>\n";

		//echo "<tr><td align=right>Announce URL</td><td>=</td><td>".$array["announce"]."</td></tr>\n";

		if (isset($array["creation date"])) {
			echo "<tr><td align=right>Creation date</td><td>=</td><td>";
			if (is_numeric($array["creation date"]))
				echo date("F j, Y", $array["creation date"]);
			else echo $array["creation date"];
			echo "</td></tr>";
		}
		if ($array["info"]["private"] == 1)
			echo "<tr><td align=right>Private (No DHT Allowed)</td><td>=</td><td>yes</td></tr>\n";
		else echo "<tr><td align=right>Private (No DHT Allowed)</td><td>=</td><td>no</td></tr>\n";

		$foundurl=false;
		foreach ($array as $left => $right) {
			if ($left == "announce" || $left == "info" || $left == "creation date")
				continue; // skip
			if ($left == "url-list" || $left == "httpseeds") {
				$foundurl=true;
				echo "<tr><td align=right>$left</td><td>=</td><td>";
				print_r(cleaner($array[$left]));
				echo "</td></tr>\n";
				continue;
			}
			echo "<tr><td align=right>$left</td><td>=</td><td>".$array[$left]."</td></tr>\n";
		}
		if (! $foundurl) {
				echo "<tr><td align=right>url-list / httpseeds not defined</td><td>=</td><td>";
				echo 'This means this file could be downloaded only if a first client is opened with file already present as a seeed';
				echo "</td></tr>\n";
		}

		echo "</table><br><br><h2>File data:</h2><pre>";
		$info = $array["info"];

		$total_size = 0;
		if (isset($info["files"])) {
			echo "Directory: ".$info["name"]."\nFiles:\n";
			foreach ($info["files"] as $file) {
				if (isset($file["path"][1])) {
					echo "    " . $file["path"][0];
					for ($i=1; isset($file["path"][$i]); $i++)
						echo "/".$file["path"][$i];
				} else echo "    " . $file["path"][0];
				echo "  (".$file["length"]." bytes)\n";
				$total_size = $total_size + $file["length"];
			}
			echo "\n";
		} else {
			echo "File: ".$info["name"]. " (".$info["length"]." bytes)\n\n";
			$total_size = $info["length"];
		}

		echo "Piece length: ".$info["piece length"]."\nNumber of pieces: ". strlen($array["info"]["pieces"])/20 . "\n\n";
		if ($total_size < 1024) //dealing with bytes
			echo "Total Size: " . $total_size . " bytes</pre>\n";
		elseif ($total_size < 1048576) //dealing with kilobytes
			echo "Total Size: " . round($total_size/1024, 2) . " kilobytes</pre>\n";
		elseif ($total_size < 1073741824) //dealing with megabytes
			echo "Total Size: " . round($total_size/1048576, 2) . " megabytes</pre>\n";
		elseif ($total_size >= 1073741824) //dealing with gigabytes
			echo "Total Size: " . round($total_size/1073741824, 2) . " gigabytes</pre>\n";
	}

	if ($output == 2) {
		if (!isset($array["files"])) {
			echo "Error: not /scrape data. Falling back on classic.<br><br>";
			classicoutput($array, $infohash);
			exit;
		}
		$files = $array["files"];

		// Copy and paste from python tracker output, with some
		// formatting changes
		echo '<table cellpadding=2 cellspacing=2 border=1 summary="files"><tr><th>info hash</th><th align="right">complete</th><th align="right">downloading</th><th>finished downloads</th><th>file name</th></tr>';


		foreach ($files as $hash => $data) {
			echo "<tr><td><TT>".bin2hex(stripslashes($hash))."</TT>";
			echo "</td><td>".$data["complete"]."</td><td>".$data["incomplete"]."</td><td>";
			if (isset($data["downloaded"]))
				echo $data["downloaded"];
			else echo "-";
			echo "</td><td>";
			if (isset($data["name"]))
				echo $data["name"];
			else echo "(unavailable)";
			echo "</td></tr>";
		}
		echo "</table>";
	}

	// http://tracker.com:6969/announce
	if ($output == 3) {
		announceoutput($array);
	}

	if (file_exists(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent")) { //for newtorrents.php
		echo "<a href=\"newtorrents.php\"><img src=\"images/add.png\" border=\"0\" class=\"icon\" alt=\"Add Torrent\" title=\"Add Torrent\" /></a><a href=\"newtorrents.php\">Add Another Torrent</a><br>\n";
		//add in Bittornado HTTP seeding spec
		if ($_POST["httpseed"] == "enabled") {
			//add information into database
			$info = $array["info"] or die("Invalid torrent file.");

			$fsbase = $_POST["relative_path"];

			if (isset($info["files"])) { // Multi-file
				if (substr($fsbase, -1) != '/')
					$fsbase .= '/';
				$pieceno = 0;
				$fileno = 0;
				$piecelen = 0;

				// Iterate for each file.
				while (isset($info["files"][$fileno])) {
					if ($piecelen == $info["piece length"]) {
						$pieceno++;
						$piecelen = 0;
					}
					$startoffset = $piecelen;
					$startpiece = $pieceno;
					$filesize = $info["files"][$fileno]["length"];
					while (true) {
						$sub = min($info["piece length"]-$piecelen, $filesize);
						$piecelen += $sub;
						$filesize -= $sub;

						if ($filesize == 0)
							break;
						if ($piecelen == $info["piece length"]) {
							$pieceno++;
							$piecelen = 0;
						}
						if ($piecelen > $info["piece length"])
							die("Logic error in script. Please report to the author.");
					}
					$filename = $fsbase;
					if (isset($info["files"][$fileno]["path"][1])) {
						$filename .= $file["path"][0];
						for ($i=1; isset($info["files"][$fileno]["path"][$i]); $i++)
							$filename .= "/".$info["files"][$fileno]["path"][$i];
					} else $filename .= $info["files"][$fileno]["path"][0];
					$filename = mysql_real_escape_string($filename);
					mysql_query("INSERT INTO ".$prefix."webseedfiles (info_hash,filename,startpiece,endpiece,startpieceoffset,fileorder) values (\"$hash\", \"$filename\", $startpiece, $pieceno, $startoffset, $fileno)");
					$fileno++;
				}
			} // end of multi-file section
			else //single file
				mysql_query("INSERT INTO ".$prefix."webseedfiles (info_hash,filename,startpiece,endpiece,startpieceoffset,fileorder) values (\"$hash\", \"".mysql_real_escape_string($fsbase)."\", 0, ". (strlen($array["info"]["pieces"])/20 - 1).", 0, 0)");
		}

		if ($_POST["getrightseed"] == "enabled" || $_POST["httpseed"] == "enabled") { //only do one write
			//edit torrent file
			$read_httpseed = fopen(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent", "rb");
			$binary_data = fread($read_httpseed, filesize(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent"));
			$data_array = BDecode($binary_data);

			if ($_POST["httpseed"] == "enabled")
				$data_array["httpseeds"][0] = $website_url . substr($_SERVER['REQUEST_URI'], 0, -15) . "seed.php";
			if ($_POST["getrightseed"] == "enabled")
				$data_array["url-list"][0] = $_POST["httpftplocation"];

			$to_write = BEncode($data_array);
			fclose($read_httpseed);
			//write torrent file
			$write_httpseed = fopen(DOL_DATA_ROOT."/bittorrent/torrents/" . $filename . ".torrent", "wb");
			fwrite($write_httpseed, $to_write);
			fclose($write_httpseed);
		}

		//add in piecelength and number of pieces
		$query = "UPDATE ".$prefix."summary SET piecelength=\"" . $info["piece length"] . "\", numpieces=\"" . strlen($array["info"]["pieces"])/20 . "\" WHERE info_hash=\"" . $hash . "\"";
		quickQuery($query);
	}

	if (!isset($_POST["hash"])) //don't display admin link if coming from index.php
		echo "<a href='admin.php'><img src='images/admin.png' border='0' class='icon' alt='Admin Page' title='Admin Page' /></a><a href='admin.php'>Return to Admin Page</a><br>";
}

if (isset($_POST["hash"])) {
	if (!isset($status)) { //not coming from newtorrents page
		llxFooter();
	}
}
