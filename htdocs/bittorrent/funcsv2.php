<?php


//////////////////////////////////////////////////////////////////
// Worker functions

if (function_exists("bcadd")) {
	function sqlAdd($left, $right)
	{
		return bcadd($left, $right, 0);
	}
	function sqlSubtract($left, $right)
	{
		return bcsub($left, $right, 0);
	}
	function sqlMultiply($left, $right)
	{
		return bcmul($left, $right, 0);
	}
	function sqlDivide($left, $right)
	{
		return bcdiv($left, $right, 0);
	}
} else // BC vs SQL math
{
	// Uses the mysql database connection to perform string math. :)
	// Used by byte counting functions
	// No error handling as we assume nothing can go wrong. :|
	function sqlAdd($left, $right)
	{
		$query = 'SELECT '.$left.'+'.$right;
		$results = mysql_query($query) or showError("Database error.");
		return mysql_result($results, 0, 0);
	}

	// Ditto
	function sqlSubtract($left, $right)
	{
		$query = 'SELECT '.$left.'-'.$right;
		$results = mysql_query($query) or showError("Database error");
		return mysql_result($results, 0, 0);
	}

	function sqlDivide($left, $right)
	{
		$query = 'SELECT '.$left.'/'.$right;
		$results = mysql_query($query) or showError("Database error");
		return mysql_result($results, 0, 0);
	}

	function sqlMultiply($left, $right)
	{
		$query = 'SELECT '.$left.'*'.$right;
		$results = mysql_query($query) or showError("Database error");
		return mysql_result($results, 0, 0);
	}


} // End of BC vs SQL

// Runs a query with no regard for the result
function quickQuery($query)
{
	$results = @mysql_query($query);
	if (!is_bool($results))
		mysql_free_result($results);
	else return $results;
	return true;
}

function bt_hex2bin($input, $assume_safe = true)
{
	if ($assume_safe !== true && ! ((strlen($input) % 2) === 0 || preg_match('/^[0-9a-f]+$/i', $input)))
		return "";
	return pack('H*', $input);
}

// Reports an error to the client in $message.
// Any other output will confuse the client, so please don't do that.
function showError($message, $log = false)
{
	if ($log)
	  error_log("RivetTracker: Sent error ($message)");
	echo "d14:failure reason".strlen($message).":$message"."e";
	exit(0);
}


function errorMessage()
{
	echo "<center><img src=\"images/important.png\" border=\"0\" class=\"icon\" alt=\"Critical Message\" title=\"Critical Message\" /></center>\n<p class=\"error\">";
}



// Used by newtorrents.php
// Returns true/false, depending on if there were errors.
function makeTorrent($hash, $tolerate = false)
{
	global $prefix;

	if (strlen($hash) != 40)
		showError("makeTorrent: Received an invalid hash");
	$result = true;
	$query = "CREATE TABLE ".$prefix."x$hash (peer_id char(40) NOT NULL default '', bytes bigint NOT NULL default 0, ip char(50) NOT NULL default 'error.x', port smallint UNSIGNED NOT NULL default \"0\", status enum('leecher','seeder') NOT NULL, lastupdate int unsigned NOT NULL default 0, sequence int unsigned AUTO_INCREMENT NOT NULL, natuser enum('N', 'Y') not null default 'N', primary key(sequence), unique(peer_id)) ENGINE = innodb";
	if (!@mysql_query($query))
		$result = false;
	if (!$result && !$tolerate)
		return false;
	//peercaching is ALWAYS on
	$query = "CREATE TABLE ".$prefix."y$hash (sequence int unsigned NOT NULL default 0, with_peerid char(101) NOT NULL default '', without_peerid char(40) NOT NULL default '', compact char(6) NOT NULL DEFAULT '', unique k (sequence)) DELAY_KEY_WRITE=1 CHECKSUM=0 ENGINE = innodb";
	mysql_query($query);

	$query = "INSERT INTO ".$prefix."summary set info_hash=\"".$hash."\", lastSpeedCycle=UNIX_TIMESTAMP()";
	if (!@mysql_query($query))
		$result = false;
	return $result;
}

// Returns true if the torrent exists.
// Currently checks by locating the row in "summary"
function verifyTorrent($hash)
{
	global $prefix;

	$query = "SELECT COUNT(*) FROM ".$prefix."summary where info_hash=\"$hash\"";
	dol_syslog("funcsv2::verifyTorrent sql=".$query);

	$results = mysql_query($query);

	$res = mysql_result($results, 0, 0);

	if ($res == 1) {
		dol_syslog("funcsv3::verifyTorrent OK", LOG_DEBUG);
		return true;
	} else {
		dol_syslog("funcsv3::verifyTorrent KO res=".$res, LOG_DEBUG);
		return false;
	}
}

function verifyHash($input)
{
	if (strlen($input) === 40 && preg_match('/^[0-9a-f]+$/', $input))
		return true;
	else return false;
}




// Returns info on one peer
function getPeerInfo($user, $hash)
{
	global $prefix;

	// If "trackerid" is set, let's try that
	if (isset($GLOBALS["trackerid"])) {
		$query = "SELECT peer_id,bytes,ip,port,status,lastupdate,sequence FROM ".$prefix."x$hash WHERE sequence=${GLOBALS["trackerid"]}";
		$results = mysql_query($query) or showError("Tracker error: invalid torrent");
		$data = mysql_fetch_assoc($results);
		if (!$data || $data["peer_id"] != $user) {
			// Damn, but don't crash just yet.
			$query = "SELECT peer_id,bytes,ip,port,status,lastupdate,sequence FROM ".$prefix."x$hash WHERE peer_id=\"$user\"";
			$results = mysql_query($query) or showError("Tracker error: invalid torrent");
			$data = mysql_fetch_assoc($results);
			$GLOBALS["trackerid"] = $data["sequence"];
		}
	} else {
		$query = "SELECT peer_id,bytes,ip,port,status,lastupdate,sequence FROM ".$prefix."x$hash WHERE peer_id=\"$user\"";
		$results = mysql_query($query) or showError("Tracker error: invalid torrent");
		$data = mysql_fetch_assoc($results);
		$GLOBALS["trackerid"] = $data["sequence"];
	}

	if (!($data))
		return false;

	return $data;
}

// Slight redesign of loadPeers
function getRandomPeers($hash, $where = "")
{
	global $prefix;

	// Don't want to send a bad "num peers" for new seeds
	if ($GLOBALS["NAT"])
		$results = mysql_query("SELECT COUNT(*) FROM ".$prefix."x$hash WHERE natuser = 'N'");
	else $results = mysql_query("SELECT COUNT(*) FROM ".$prefix."x$hash");

	$peercount = mysql_result($results, 0, 0);

	// ORDER BY RAND() is expensive. Don't do it when the load gets too high
	if ($peercount < 500)
		$query = "SELECT ".((isset($_GET["no_peer_id"]) && $_GET["no_peer_id"] == 1) ? "" : "peer_id,")."ip, port, status FROM ".$prefix."x$hash ".$where." ORDER BY RAND() LIMIT ${GLOBALS['maxpeers']}";
	else $query = "SELECT ".((isset($_GET["no_peer_id"]) && $_GET["no_peer_id"] == 1) ? "" : "peer_id,")."ip, port, status FROM ".$prefix."x$hash LIMIT ".@mt_rand(0, $peercount - $GLOBALS["maxpeers"]).", ${GLOBALS['maxpeers']}";

	$results = mysql_query($query);
	if (!$results)
		return false;

	$peerno = 0;
	while ($return[] = mysql_fetch_assoc($results))
		$peerno++;

	array_pop($return);
	mysql_free_result($results);
	$return['size'] = $peerno;

	return $return;
}

//  Deletes a peer from the system and performs all cleaning up
//
//  $assumepeer contains the result of getPeerInfo, or false
//  if we should grab it ourselves.
function killPeer($userid, $hash, $left, $assumepeer = false)
{
	global $prefix;

	if (!$assumepeer) {
		$peer = getPeerInfo($userid, $hash);
		if (!$peer)
			return;
		if ($left != $peer["bytes"])
			$bytes = sqlSubtract($peer["bytes"], $left);
		else $bytes = 0;
	} else {
		$bytes = 0;
		$peer = $assumepeer;
	}

	quickQuery("DELETE FROM ".$prefix."x$hash WHERE peer_id=\"$userid\"");
	if (mysql_affected_rows() == 1) {
		//peercaching ALWAYS on
		quickQuery("DELETE FROM ".$prefix."y$hash WHERE sequence=" . $peer["sequence"]);
		if ($peer["status"] == "leecher")
			summaryAdd("leechers", -1);
		else summaryAdd("seeds", -1);
		if ($GLOBALS["countbytes"] && ((float) $bytes) > 0)
			summaryAdd("dlbytes", $bytes);
		if ($peer["bytes"] != 0 && $left == 0)
			summaryAdd("finished", 1);
	}
}

// Transfers bytes from "left" to "dlbytes" when a peer reports in.
function collectBytes($peer, $hash, $left)
{
	global $prefix;

	$peerid=$peer["peer_id"];

	if (!$GLOBALS["countbytes"]) {
		quickQuery("UPDATE ".$prefix."x$hash SET lastupdate=UNIX_TIMESTAMP() where " . (isset($GLOBALS["trackerid"]) ? "sequence=\"${GLOBALS["trackerid"]}\"" : "peer_id=\"$peerid\""));
		return;
	}
	$diff = sqlSubtract($peer["bytes"], $left);
	quickQuery("UPDATE ".$prefix."x$hash set " . (($diff != 0) ? "bytes=\"$left\"," : ""). " lastupdate=UNIX_TIMESTAMP() where " . (isset($GLOBALS["trackerid"]) ? "sequence=\"${GLOBALS["trackerid"]}\"" : "peer_id=\"$peerid\""));

	// Anti-negative clause
	if (((float) $diff) > 0)
		summaryAdd("dlbytes", $diff);
}

// Transmits the actual data to the peer. No other output is permitted if
// this function is called, as that would break BEncoding.
// I don't use the bencode library, so watch out! If you add data,
// rules such as dictionary sorting are enforced by the remote side.
function sendPeerList($peers)
{
	echo "d";
	echo "8:intervali".$GLOBALS["report_interval"]."e";
	if (isset($GLOBALS["min_interval"]))
		echo "12:min intervali".$GLOBALS["min_interval"]."e";
	echo "5:peers";
	$size=$peers["size"];
	if (isset($_GET["compact"]) && $_GET["compact"] == '1') {
		$p = '';
		for ($i=0; $i < $size; $i++)
			$p .= pack("Nn", ip2long($peers[$i]['ip']), $peers[$i]['port']);
		echo strlen($p).':'.$p;
	} else // no_peer_id or no feature supported
	{
		echo 'l';
		for ($i=0; $i < $size; $i++) {
			echo "d2:ip".strlen($peers[$i]["ip"]).":".$peers[$i]["ip"];
			if (isset($peers[$i]["peer_id"]))
				echo "7:peer id20:".bt_hex2bin($peers[$i]["peer_id"]);
			echo "4:porti".$peers[$i]["port"]."ee";
		}
		echo "e";
	}
	if (isset($GLOBALS["trackerid"])) {
		// Now it gets annoying. trackerid is a string
		echo "10:tracker id".strlen($GLOBALS["trackerid"]).":".$GLOBALS["trackerid"];
	}

	echo "e";
}


// Faster pass-through version of getRandompeers => sendPeerList
// It's the only way to use cache tables. In fact, it only uses it.
function sendRandomPeers($info_hash)
{
	global $prefix;

	$result = mysql_query("SELECT COUNT(*) FROM ".$prefix."y$info_hash");
	$count = mysql_result($result, 0, 0);

	if (isset($_GET["compact"]) && $_GET["compact"] == '1')
		$column = "compact";
	elseif (isset($_GET["no_peer_id"]) && $_GET["no_peer_id"] == '1')
		$column = "without_peerid";
	else $column = "with_peerid";

	if ($count < $GLOBALS["maxpeers"])
		$query = "SELECT $column FROM ".$prefix."y$info_hash";
	elseif ($count > 500) {
		do {
			$rand1 = mt_rand(0, $count-$GLOBALS["maxpeers"]);
			$rand2 = mt_rand(0, $count-$GLOBALS["maxpeers"]);
		} while (abs($rand1 - $rand2) < $GLOBALS["maxpeers"]/2);
		$query = "(SELECT $column FROM ".$prefix."y$info_hash LIMIT $rand1, ".($GLOBALS["maxpeers"]/2). ") UNION (SELECT $column FROM ".$prefix."y$info_hash LIMIT $rand2, ".($GLOBALS["maxpeers"]/2). ")";
	} else $query = "SELECT $column FROM ".$prefix."y$info_hash ORDER BY RAND() LIMIT ".$GLOBALS["maxpeers"];



	echo "d";
	echo "8:intervali".$GLOBALS["report_interval"]."e";
	if (isset($GLOBALS["min_interval"]))
		echo "12:min intervali".$GLOBALS["min_interval"]."e";
	echo "5:peers";

	$result = mysql_query($query);
	if ($column == "compact") {
		echo (mysql_num_rows($result) * 6) . ":";
		while ($row = mysql_fetch_row($result))
			echo str_pad($row[0], 6, chr(32));
	} else {
		echo "l";
		while ($row = mysql_fetch_row($result))
			echo "d".$row[0]."e";
		echo "e";
	}
	if (isset($GLOBALS["trackerid"]))
		echo "10:tracker id".strlen($GLOBALS["trackerid"]).":".$GLOBALS["trackerid"];
	echo "e";
}


// Returns a $peers array of all peers that have timed out (2* report interval seems fair
// for any reasonable report interval (900 or larger))
function loadLostPeers($hash, $timeout)
{
	global $prefix;

	$results = mysql_query("SELECT peer_id,bytes,ip,port,status,lastupdate,sequence from ".$prefix."x$hash where lastupdate < (UNIX_TIMESTAMP() - 2 * $timeout)");
	$peerno = 0;
	if (!$results)
		return false;

	while ($return[] = mysql_fetch_assoc($results))
		$peerno++;
	array_pop($return);
	$return["size"] = $peerno;
	mysql_free_result($results);
	return $return;
}

function trashCollector($hash, $timeout)
{
	global $prefix;

	if (isset($GLOBALS["trackerid"]))
		unset($GLOBALS["trackerid"]);

	if (!Lock($hash))
		return;

	$results = mysql_query("SELECT lastcycle FROM ".$prefix."summary WHERE info_hash='$hash'");
	$lastcheck = (mysql_fetch_row($results));

	// Check once every re-announce cycle
	if (($lastcheck[0] + $timeout) < time()) {
		$peers = loadLostPeers($hash, $timeout);
		for ($i=0; $i < $peers["size"]; $i++)
			killPeer($peers[$i]["peer_id"], $hash, $peers[$i]["bytes"]);
		summaryAdd("lastcycle", "UNIX_TIMESTAMP()", true);
	}
	Unlock($hash);
}

// Attempts to aquire a lock by name.
// Returns true on success, false on failure
function Lock($hash, $time = 0)
{
	$results = mysql_query("SELECT GET_LOCK('$hash', $time)");
	$string = mysql_fetch_row($results);
	if (strcmp($string[0], "1") == 0)
		return true;
	return false;
}

// Releases a lock. Ignores errors.
function Unlock($hash)
{
	quickQuery("SELECT RELEASE_LOCK('$hash')");
}

// Returns true if the lock is available
function isFreeLock($lock)
{
	if (Lock($lock, 0)) {
		Unlock($lock);
		return true;
	}
	return false;
}


/* Returns true if the user is firewalled, NAT'd, or whatever.
 * The original tracker had its --nat_check parameter, so
 * here is my version.
 *
 * This code has proven itself to be sufficiently correct,
 * but will consume system resources when a lot of httpd processes
 * are lingering around trying to connect to remote hosts.
 * Consider disabling it under higher loads.
 */
function isFireWalled($hash, $peerid, $ip, $port)
{

	// NAT checking off?
	if (!$GLOBALS["NAT"])
		return false;

	$protocol_name = 'BitTorrent protocol';
	$theError = "";
	// Hoping 10 seconds will be enough
	$fd = fsockopen($ip, $port, $errno, $theError, 10);
	if (!$fd)
		return true;

	stream_set_timeout($fd, 5, 0);
	fwrite($fd, chr(strlen($protocol_name)).$protocol_name.bt_hex2bin("0000000000000000").
		bt_hex2bin($hash));

	$data = fread($fd, strlen($protocol_name)+1+20+20+8); // ideally...

	fclose($fd);
	$offset = 0;

	// First byte: strlen($protocol_name), then the protocol string itself
	if (ord($data[$offset]) != strlen($protocol_name))
		return true;

	$offset++;
	if (substr($data, $offset, strlen($protocol_name)) != $protocol_name)
		return true;

	$offset += strlen($protocol_name);
	// 8 bytes reserved, ignore
	$offset += 8;

	// Download ID (hash)
	if (substr($data, $offset, 20) != bt_hex2bin($hash))
		return true;

	$offset+=20;

	// Peer ID
	if (substr($data, $offset, 20) != bt_hex2bin($peerid))
		return true;


	return false;
}


// It's cruel, but if people abuse my tracker, I just might do it.
// It pretends to accept the torrent, and reports that you are the
// only person connected.
function evilReject($ip, $peer_id, $port)
{

	// For those of you who are feeling evil, comment out this line.
	showError("Torrent is not authorized for use on this tracker.");

	$peers[0]["peer_id"] = $peer_id;
	$peers[0]["ip"] = $ip;
	$peers[0]["port"] = $port;
	$peers["size"] = 1;
	$GLOBALS["report_interval"] = 86400;
	$GLOBALS["min_interval"] = 86000;
	sendPeerList($peers);
	exit(0);
}


function runSpeed($info_hash, $delta)
{
	global $prefix;

	//stick in our latest data before we calc it out
	quickQuery("INSERT IGNORE INTO ".$prefix."timestamps (info_hash, bytes, delta, sequence) SELECT '$info_hash' AS info_hash, dlbytes, UNIX_TIMESTAMP() - lastSpeedCycle, NULL FROM ".$prefix."summary WHERE info_hash=\"$info_hash\"");

	// mysql blows sometimes so we have to read the data into php before updating it
	$results = mysql_query('SELECT (MAX(bytes)-MIN(bytes))/SUM(delta), COUNT(*), MIN(sequence) FROM '.$prefix.'timestamps WHERE info_hash="'.$info_hash.'"');
	$data = mysql_fetch_row($results);

	$results2 = mysql_query('SELECT '.$prefix.'summary.leechers FROM '.$prefix.'summary WHERE info_hash="'.$info_hash.'"');
	$data2 = mysql_fetch_row($results2);
	if ($data2[0] == 0) //if no leechers, speed is zero
		$data[0] = 0;

	$results3 = mysql_query("SELECT MIN(d1.bytes), MAX(d1.bytes) FROM (SELECT bytes FROM ".$prefix."timestamps WHERE info_hash='".$info_hash."' ORDER BY sequence DESC LIMIT 5) AS d1");
	$data3 = mysql_fetch_row($results3);
	//if the last 5 updates from clients show the same bytes, it's probably stalled, set speed to zero
	if ($data3[0] == $data3[1])
		$data[0] = 0;

	summaryAdd("speed", $data[0], true);
	summaryAdd("lastSpeedCycle", "UNIX_TIMESTAMP()", true);

	// if we have more than 20 drop the rest
	//if ($data[1] == 21)
		//quickQuery("DELETE FROM timestamps WHERE info_hash=\"$info_hash\" AND sequence=${data[2]}");
	if ($data[1] > 21)
		// This query requires MySQL 4.0.x, but should rarely be used.
		quickQuery('DELETE FROM '.$prefix.'timestamps WHERE info_hash="'.$info_hash.'" ORDER BY sequence LIMIT '.($data['1'] - 20));
}

// Schedules an update to the summary table. It gets so much traffic
// that we do all our changes at once.
// When called, the column $column for the current info_hash is incremented
// by $value, or set to exactly $value if $abs is true.
function summaryAdd($column, $value, $abs = false)
{
	if (isset($GLOBALS["summaryupdate"][$column])) {
		if (!$abs)
			$GLOBALS["summaryupdate"][$column][0] += $value;
		else showError("Tracker bug calling summaryAdd");
	} else {
		$GLOBALS["summaryupdate"][$column][0] = $value;
		$GLOBALS["summaryupdate"][$column][1] = $abs;
	}
}


//converts byte size to string format for display
function bytesToString($total_size)
{
	if ($total_size < 1024) //dealing with bytes
		return $total_size . " bytes";
	elseif ($total_size < 1048576) //dealing with kilobytes
		return round($total_size/1024, 2) . " KB";
	elseif ($total_size < 1073741824) //dealing with megabytes
		return round($total_size/1048576, 2) . " MB";
	elseif ($total_size >= 1073741824) //dealing with gigabytes
		return round($total_size/1073741824, 2) . " GB";
}


// Even if you're missing PHP 4.3.0, the MHASH extension might be of use.
// Someone was kind enought to email this code snippit in.
if (function_exists('mhash') && (!function_exists('sha1')) &&
defined('MHASH_SHA1')) {
	function sha1($str)
	{
		return bin2hex(mhash(MHASH_SHA1, $str));
	}
}

//If magic quotes are on, returns the cleaned (no single quotes) output string
function clean($input)
{
	if (get_magic_quotes_gpc())
		return stripslashes($input);
	return $input;
}

//If magic quotes are off, returns the added (single quotes) output string
function addquotes($input)
{
	if (!get_magic_quotes_gpc())
		return addslashes($input);
	return $input;
}
