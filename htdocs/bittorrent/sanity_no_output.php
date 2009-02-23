<?php
require_once("config.php");
require_once("funcsv2.php");

$summaryupdate = array();

// Non-persistant: we lock tables!
$db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - ".mysql_error() . "</p>");
mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - ".mysql_error() . "</p>");


quickQuery("LOCK TABLES ".$prefix."summary WRITE, ".$prefix."namemap READ");

$results = mysql_query("SELECT ".$prefix."summary.info_hash, seeds, leechers, dlbytes, ".$prefix."namemap.filename FROM ".$prefix."summary LEFT JOIN ".$prefix."namemap ON ".$prefix."summary.info_hash = ".$prefix."namemap.info_hash");

$i = 0;

while ($row = mysql_fetch_row($results))
{
	$writeout = "row" . $i % 2;
	list($hash, $seeders, $leechers, $bytes, $filename) = $row;
	if ($locking)
	{
		//peercaching ALWAYS on
		quickQuery("LOCK TABLES ".$prefix."x$hash WRITE, ".$prefix."y$hash WRITE, ".$prefix."summary WRITE");
	}
	$results2 = mysql_query("SELECT status, COUNT(status) FROM ".$prefix."x$hash GROUP BY status");

	if (!$results2)
	{
		//unable to process
		continue;
	}

	$counts = array();
	while ($row = mysql_fetch_row($results2))
		$counts[$row[0]] = $row[1];	
	if (!isset($counts["leecher"]))
		$counts["leecher"] = 0;
	if (!isset($counts["seeder"]))
		$counts["seeder"] = 0;

	if ($counts["leecher"] != $leechers)
		quickQuery("UPDATE ".$prefix."summary SET leechers=".$counts["leecher"]." WHERE info_hash=\"$hash\"");

	if ($counts["seeder"] != $seeders)
		quickQuery("UPDATE ".$prefix."summary SET seeds=".$counts["seeder"]." WHERE info_hash=\"$hash\"");
		
	if ($counts["leecher"] == 0)
	{
		//If there are no leechers, set the speed to zero
		quickQuery("UPDATE ".$prefix."summary set speed=0 WHERE info_hash=\"$hash\"");
	}
	

	if ($bytes < 0)
		quickQuery("UPDATE ".$prefix."summary SET dlbytes=0 WHERE info_hash=\"$hash\"");

	myTrashCollector($hash, $report_interval, time(), $writeout);

	$result = mysql_query("SELECT ".$prefix."x$hash.sequence FROM ".$prefix."x$hash LEFT JOIN ".$prefix."y$hash ON ".$prefix."x$hash.sequence = ".$prefix."y$hash.sequence WHERE ".$prefix."y$hash.sequence IS NULL") or die(errorMessage() . "" . mysql_error() . "</p>");
	if (mysql_num_rows($result) > 0)
	{
		$row = array();
		
		while ($data = mysql_fetch_row($result))
				$row[] = "sequence=\"${data[0]}\"";
		$where = implode(" OR ", $row);
		$query = mysql_query("SELECT * FROM ".$prefix."x$hash WHERE $where");
		
		while ($row = mysql_fetch_assoc($query))
		{
			$compact = mysql_escape_string(pack('Nn', ip2long($row["ip"]), $row["port"]));
				$peerid = mysql_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . '7:peer id20:' . hex2bin($row["peer_id"]) . "4:porti{$row["port"]}e");
			$no_peerid = mysql_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . "4:porti{$row["port"]}e");
			mysql_query("INSERT INTO ".$prefix."y$hash SET sequence=\"{$row["sequence"]}\", compact=\"$compact\", with_peerid=\"$peerid\", without_peerid=\"$no_peerid\"");
		}
	}	

	$result = mysql_query("SELECT ".$prefix."y$hash.sequence FROM ".$prefix."y$hash LEFT JOIN ".$prefix."x$hash ON ".$prefix."y$hash.sequence = ".$prefix."x$hash.sequence WHERE ".$prefix."x$hash.sequence IS NULL");
	if (mysql_num_rows($result) > 0)
	{
		$row = array();
		
		while ($data = mysql_fetch_row($result))
			$row[] = "sequence=\"${data[0]}\"";
		$where = implode(" OR ", $row);
		$query = mysql_query("DELETE FROM ".$prefix."y$hash WHERE $where");
	}


	$i ++;

	quickQuery("UNLOCK TABLES");
	
	//Repair tables, is this necessary?  Sometimes the tables crash...
	//Can't repair table if locked?
	//quickQuery("REPAIR Table x$hash");
	//quickQuery("REPAIR Table y$hash");

	// Finally, it's time to do stuff to the summary table.
	if (!empty($summaryupdate))
	{
		$stuff = "";
		foreach ($summaryupdate as $column => $value)
		{
			$stuff .= ', '.$column. ($value[1] ? "=" : "=$column+") . $value[0];
		}
		mysql_query("UPDATE ".$prefix."summary SET ".substr($stuff, 1)." WHERE info_hash=\"$hash\"");
		$summaryupdate = array();
	}
		
}


function myTrashCollector($hash, $timeout, $now, $writeout)
{
 	$peers = loadLostPeers($hash, $timeout);
 	for ($i=0; $i < $peers["size"]; $i++)
	        killPeer($peers[$i]["peer_id"], $hash, $peers[$i]["bytes"], $peers[$i]);
 	quickQuery("UPDATE ".$prefix."summary SET lastcycle='$now' WHERE info_hash='$hash'");
}

?>