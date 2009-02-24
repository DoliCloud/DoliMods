<?php
include("./pre.inc.php");
require_once("funcsv2.php");

$tracker_url = $website_url . substr($_SERVER['REQUEST_URI'], 0, -15) . "announce.php";

llxHeader('','BitTorrent',$website_url.'/bittorrent/docs/help.html');

$form=new Form($db);


//open up config file and display for editing
if (!isset($_POST["saveconfig"]))
{
	?>
	<h1>View Config File</h1>
	<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="POST">
	<input type="hidden" name="saveconfig" value="1">
	This page allows you to view the "config.php" settings.  This file stores all the necessary
	settings for your tracker.<br>
	<span class="notice">*</span> - required value<br>
	<table border="1" cellpadding="3">
	<?php
	//open up config file
	$fr = fopen("config.php", "r") or die(errorMessage() . "Error: couldn't read config.php!</p>");
	$temp = fgets($fr);
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td>Enable or disable scraping by clients.  Generally it is safe to leave this on unless
	you have a large number of torrents or users which can lead to increased bandwidth usage.  Also, scraping
	can possibily be used maliciously by abusive clients.</td>
	<td><input type="checkbox" value="<?php if($temp == "true") echo "on"; else echo "off"?>" name="scrape"<?php if ($temp == "true") echo " checked";?>></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td><span class="notice">*</span> Maximum reannounce interval (in seconds) 1800 == 30 minutes</td>
	<td><input type="text" name="report_interval" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td><span class="notice">*</span> Minimum reannounce interval (also in seconds) 300 == 5 minutes</td>
	<td><input type="text" name="min_interval" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td><span class="notice">*</span> Number of peers to send in one request.  Some logic will break if you set this to more than 300,
	so please don't do that. 100 is the most you should set anyway.</td>
	<td><input type="text" name="maxpeers" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td>If set, NAT checking will be performed.
	This may cause trouble with some providers, so it's
	off by default.</td>
	<td><input type="checkbox" value="<?php if($temp == "true") echo "on"; else echo "off"?>" name="NAT"<?php if ($temp == "true") echo " checked";?>></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td>Persistent MySQL connections:
	Check with your webmaster to see if you're allowed to use these.
	Highly recommended, especially for higher loads, but generally
	not allowed unless it's a dedicated machine.</td>
	<td><input type="checkbox" value="<?php if($temp == "true") echo "on"; else echo "off"?>" name="persist"<?php if ($temp == "true") echo " checked";?>></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td>Allow users to override ip address.
	Enable this if you know people have a legit reason to use
	this function. Leave disabled otherwise.</td>
	<td><input type="checkbox" value="<?php if($temp == "true") echo "on"; else echo "off"?>" name="ip_override"<?php if ($temp == "true") echo " checked";?>></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td>For heavily loaded trackers, uncheck this. It will stop count the number
	of downloaded bytes and the speed of the torrent, but will significantly reduce
	the load.</td>
	<td><input type="checkbox" value="<?php if($temp == "true") echo "on"; else echo "off"?>" name="countbytes"<?php if ($temp == "true") echo " checked";?>></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Username for individual who can add torrents to tracker database.
	This user is only able to create, and not delete torrents to the tracker.
	For full privileges, see the admin user.</td>
	<td><input type="text" name="upload_username" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Password for individual who can add torrents to tracker database.
	Again, this user is only able to create, and not delete torrents to the tracker.
	For full privileges, see the admin user.<br><br>
	<input type="hidden" name="old_upload_password" value="<?php echo $temp;?>">
	<b>Current MD5 hashed username+password: <?php echo $temp;?></b></td>
	<td><input type="password" name="upload_password" size="40" value=""></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Admin username. The admin is able to go to the admin page and show detailed
	information about the tracker as well as access a few other important tools.
	The admin is also able to upload torrents to the database
	just like the previous account.</td>
	<td><input type="text" name="admin_username" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Password for admin.  Again, The admin is able to go to the admin page and show detailed
	information about the tracker as well as access a few other important tools.
	The admin is also able to upload torrents to the database.<br><br>
	<input type="hidden" name="old_admin_password" value="<?php echo $temp;?>">
	<b>Current MD5 hashed username+password: <?php echo $temp;?></b></td>
	<td><input type="password" name="admin_password" size="40" value=""></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = clean(substr($temp, strpos($temp, "=")+3, -3));
	?>
	<tr><td>Title on index.php statistics page, if not set, defaults to "Tracker Statistics"</td>
	<td><input type="text" name="title" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Database Hostname: This is the MySQL database hostname, if it is the local machine, it should
	be set to localhost.</td>
	<td><input type="text" name="dbhost" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Database Username: This is the user who has access to the database table.  If you are unsure,
	check with your system administrator.</td>
	<td><input type="text" name="dbuser" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Database Password: This is the password for the user who has access to the database table.
	If you are unsure, check with your system administrator.</td>
	<td><input type="text" name="dbpass" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Database name: This is the name of the database.  If you are unsure, check with
	your system administrator.</td>
	<td><input type="text" name="database" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td>Enable RSS feed:
	If you do not want the RSS feed to be created for privacy reasons or do not need it disable this checkbox.</td>
	<td><input type="checkbox" value="<?php if($temp == "true") echo "on"; else echo "off"?>" name="enablerss"<?php if ($temp == "true") echo " checked";?>></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = clean(substr($temp, strpos($temp, "=")+3, -3));
	?>
	<tr><td>RSS Title: In the rss.xml file, this is the main <pre>&lt;title&gt;</pre> tag.</td>
	<td><input type="text" name="rss_title" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td>RSS link to main website: In the rss.xml file, this is the main <pre>&lt;link&gt;</pre> tag.</td>
	<td><input type="text" name="rss_link" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = clean(substr($temp, strpos($temp, "=")+3, -3));
	?>
	<tr><td>RSS description: In the rss.xml file, this is the main <pre>&lt;description&gt;</pre> tag.</td>
	<td><input type="text" name="rss_description" size="60" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> For HTTP seeding, this is the maximum total upload rate per second in kilobytes, for example 100 would be 100 KB/s</td>
	<td><input type="text" name="max_upload_rate" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+2, -2);
	?>
	<tr><td><span class="notice">*</span> For HTTP seeding, this is the maximum number of uploads to run at a time</td>
	<td><input type="text" name="max_uploads" size="40" value="<?php echo $temp;?>"></td></tr>
	<?php
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<tr><td><span class="notice">*</span> Timezone that the server runs on</td>
	<td>
	<select name="timezone" id="timezone">
	<option title="[UTC - 12] Baker Island Time" value="-1200"<?php if($temp == "-1200") echo " selected=\"selected\"";?>>[UTC - 12] Baker Island Time</option>
	<option title="[UTC - 11] Niue Time, Samoa Standard Time" value="-1100"<?php if($temp == "-1100") echo " selected=\"selected\"";?>>[UTC - 11] Niue Time, Samoa Standard Time</option>
	<option title="[UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time" value="-1000"<?php if($temp == "-1000") echo " selected=\"selected\"";?>>[UTC - 10] Hawaii-Aleutian Standard Time, Cook Isl...</option>
	<option title="[UTC - 9:30] Marquesas Islands Time" value="-0930"<?php if($temp == "-0930") echo " selected=\"selected\"";?>>[UTC - 9:30] Marquesas Islands Time</option>
	<option title="[UTC - 9] Alaska Standard Time, Gambier Island Time" value="-0900"<?php if($temp == "-0900") echo " selected=\"selected\"";?>>[UTC - 9] Alaska Standard Time, Gambier Island Tim...</option>
	<option title="[UTC - 8] Pacific Standard Time" value="-0800"<?php if($temp == "-0800") echo " selected=\"selected\"";?>>[UTC - 8] Pacific Standard Time</option>
	<option title="[UTC - 7] Mountain Standard Time" value="-0700"<?php if($temp == "-0700") echo " selected=\"selected\"";?>>[UTC - 7] Mountain Standard Time</option>
	<option title="[UTC - 6] Central Standard Time" value="-0600"<?php if($temp == "-0600") echo " selected=\"selected\"";?>>[UTC - 6] Central Standard Time</option>
	<option title="[UTC - 5] Eastern Standard Time" value="-0500"<?php if($temp == "-0500") echo " selected=\"selected\"";?>>[UTC - 5] Eastern Standard Time</option>
	<option title="[UTC - 4] Atlantic Standard Time" value="-0400"<?php if($temp == "-0400") echo " selected=\"selected\"";?>>[UTC - 4] Atlantic Standard Time</option>
	<option title="[UTC - 3:30] Newfoundland Standard Time" value="-0330"<?php if($temp == "-0330") echo " selected=\"selected\"";?>>[UTC - 3:30] Newfoundland Standard Time</option>
	<option title="[UTC - 3] Amazon Standard Time, Central Greenland Time" value="-0300"<?php if($temp == "-0300") echo " selected=\"selected\"";?>>[UTC - 3] Amazon Standard Time, Central Greenland ...</option>
	<option title="[UTC - 2] Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time" value="-0200"<?php if($temp == "-0200") echo " selected=\"selected\"";?>>[UTC - 2] Fernando de Noronha Time, South Georgia ...</option>
	<option title="[UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time" value="-0100"<?php if($temp == "-0100") echo " selected=\"selected\"";?>>[UTC - 1] Azores Standard Time, Cape Verde Time, E...</option>
	<option title="[UTC] Western European Time, Greenwich Mean Time" value="+0000"<?php if($temp == "+0000") echo " selected=\"selected\"";?>>[UTC] Western European Time, Greenwich Mean Time</option>
	<option title="[UTC + 1] Central European Time, West African Time" value="+0100"<?php if($temp == "+0100") echo " selected=\"selected\"";?>>[UTC + 1] Central European Time, West African Time</option>
	<option title="[UTC + 2] Eastern European Time, Central African Time" value="+0200"<?php if($temp == "+0200") echo " selected=\"selected\"";?>>[UTC + 2] Eastern European Time, Central African T...</option>
	<option title="[UTC + 3] Moscow Standard Time, Eastern African Time" value="+0300"<?php if($temp == "+0300") echo " selected=\"selected\"";?>>[UTC + 3] Moscow Standard Time, Eastern African Ti...</option>
	<option title="[UTC + 3:30] Iran Standard Time" value="+0330"<?php if($temp == "+0330") echo " selected=\"selected\"";?>>[UTC + 3:30] Iran Standard Time</option>
	<option title="[UTC + 4] Gulf Standard Time, Samara Standard Time" value="+0400"<?php if($temp == "+0400") echo " selected=\"selected\"";?>>[UTC + 4] Gulf Standard Time, Samara Standard Time</option>
	<option title="[UTC + 4:30] Afghanistan Time" value="+0430"<?php if($temp == "+0430") echo " selected=\"selected\"";?>>[UTC + 4:30] Afghanistan Time</option>
	<option title="[UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time" value="+0500"<?php if($temp == "+0500") echo " selected=\"selected\"";?>>[UTC + 5] Pakistan Standard Time, Yekaterinburg St...</option>
	<option title="[UTC + 5:30] Indian Standard Time, Sri Lanka Time" value="+0530"<?php if($temp == "+0530") echo " selected=\"selected\"";?>>[UTC + 5:30] Indian Standard Time, Sri Lanka Time</option>
	<option title="[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time" value="+0600"<?php if($temp == "+0600") echo " selected=\"selected\"";?>>[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirs...</option>
	<option title="[UTC + 6:30] Cocos Islands Time, Myanmar Time" value="+0630"<?php if($temp == "+0630") echo " selected=\"selected\"";?>>[UTC + 6:30] Cocos Islands Time, Myanmar Time</option>
	<option title="[UTC + 7] Indochina Time, Krasnoyarsk Standard Time" value="+0700"<?php if($temp == "+0700") echo " selected=\"selected\"";?>>[UTC + 7] Indochina Time, Krasnoyarsk Standard Tim...</option>
	<option title="[UTC + 8] Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time" value="+0800"<?php if($temp == "+0800") echo " selected=\"selected\"";?>>[UTC + 8] Chinese Standard Time, Australian Wester...</option>
	<option title="[UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time" value="+0900"<?php if($temp == "+0900") echo " selected=\"selected\"";?>>[UTC + 9] Japan Standard Time, Korea Standard Time...</option>
	<option title="[UTC + 9:30] Australian Central Standard Time" value="+0930"<?php if($temp == "+0930") echo " selected=\"selected\"";?>>[UTC + 9:30] Australian Central Standard Time</option>
	<option title="[UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time" value="+1000"<?php if($temp == "+1000") echo " selected=\"selected\"";?>>[UTC + 10] Australian Eastern Standard Time, Vladi...</option>
	<option title="[UTC + 10:30] Lord Howe Standard Time" value="+1030"<?php if($temp == "+1030") echo " selected=\"selected\"";?>>[UTC + 10:30] Lord Howe Standard Time</option>
	<option title="[UTC + 11] Solomon Island Time, Magadan Standard Time" value="+1100"<?php if($temp == "+1100") echo " selected=\"selected\"";?>>[UTC + 11] Solomon Island Time, Magadan Standard T...</option>
	<option title="[UTC + 11:30] Norfolk Island Time" value="+1130"<?php if($temp == "+1130") echo " selected=\"selected\"";?>>[UTC + 11:30] Norfolk Island Time</option>
	<option title="[UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time" value="+1200"<?php if($temp == "+1200") echo " selected=\"selected\"";?>>[UTC + 12] New Zealand Time, Fiji Time, Kamchatka ...</option>
	<option title="[UTC + 13] Tonga Time, Phoenix Islands Time" value="+1300"<?php if($temp == "+1300") echo " selected=\"selected\"";?>>[UTC + 13] Tonga Time, Phoenix Islands Time</option>
	<option title="[UTC + 14] Line Island Time" value="+1400"<?php if($temp == "+1400") echo " selected=\"selected\"";?>>[UTC + 14] Line Island Time</option>
	</select>
	<?php

	//get MySQL table prefix, store in hidden form field
	$temp = fgets($fr);
	$temp = substr($temp, strpos($temp, "=")+3, -3);
	?>
	<input type="hidden" name="prefix" value="<?php echo $temp;?>" />

	<?php
	fclose($fr);

	?>
	</td>
	</tr>
	</table>
	<!--  <input type="submit" value="Save Config"> -->
	</form>

	<?php
}


if (isset($_POST["saveconfig"]))
{
	//check required entries for values, if blank: error out
	if (!is_numeric($_POST["report_interval"]) || $_POST["report_interval"] == "" || $_POST["report_interval"] <= 0)
	{
		echo errorMessage() . "Error: The maximum reannounce interval is not an integer, a negative number, or is blank.</p>";
		exit();
	}
	if (!is_numeric($_POST["min_interval"]) || $_POST["min_interval"] == "" || $_POST["min_interval"] <= 0)
	{
		echo errorMessage() . "Error: The minimum reannounce interval is not an integer, a negative number, or is blank.</p>";
		exit();
	}
	if (!is_numeric($_POST["maxpeers"]) || $_POST["maxpeers"] == "" || $_POST["maxpeers"] > 300 || $_POST["maxpeers"] <= 0)
	{
		echo errorMessage() . "Error: The number of peers to send in one request is not an integer, over 300, a negative number, zero, or blank.</p>";
		exit();
	}
	if ($_POST["upload_username"] == "")
	{
		echo errorMessage() . "Error: The upload username is blank.</p>";
		exit();
	}
	if ($_POST["admin_username"] == "")
	{
		echo errorMessage() . "Error: The admin username is blank.</p>";
		exit();
	}
	if ($_POST["dbhost"] == "")
	{
		echo errorMessage() . "Error: The database hostname is blank.</p>";
		exit();
	}
	if ($_POST["dbuser"] == "")
	{
		echo errorMessage() . "Error: The database username is blank.</p>";
		exit();
	}
	if ($_POST["dbpass"] == "")
	{
		echo errorMessage() . "Error: The database password is blank.</p>";
		exit();
	}
	if ($_POST["database"] == "")
	{
		echo errorMessage() . "Error: The database name is blank.</p>";
		exit();
	}
	if ($_POST["rss_link"] != "" && Substr($_POST["rss_link"], 0, 7) != "http://")
	{
		echo errorMessage() . "Error: The RSS website URL does not start with http://</p>";
		exit();
	}
	if ($_POST["website_url"] == "" || Substr($_POST["website_url"], 0, 7) != "http://")
	{
		echo errorMessage() . "Error: The website URL does not start with http:// or is blank.</p>";
		exit();
	}
	if (!is_numeric($_POST["max_upload_rate"]) || $_POST["max_upload_rate"] == "" || $_POST["max_upload_rate"] <= 0)
	{
		echo errorMessage() . "Error: The maximum upload rate is not an integer, a negative number, or is blank.</p>";
		exit();
	}
	if (!is_numeric($_POST["max_uploads"]) || $_POST["max_uploads"] == "" || $_POST["max_uploads"] <= 0)
	{
		echo errorMessage() . "Error: The maximum uploads is not an integer, a negative number, or is blank.</p>";
		exit();
	}
	if ($_POST["timezone"] == "")
	{
		echo errorMessage() . "Error: The timezone is blank.</p>";
		exit();
	}
	if ($_POST["upload_username"] == $_POST["admin_username"])
	{
		echo errorMessage() . "Error: The admin username cannot be the same as the upload username.</p>";
		exit();
	}

	//calculate new MD5 password if needed
	if ($_POST["upload_password"] != "")
	{
		$_POST["upload_password"] = md5($_POST["upload_username"].$_POST["upload_password"]);
	}
	else
		$_POST["upload_password"] = $_POST["old_upload_password"];
	if ($_POST["admin_password"] != "")
	{
		$_POST["admin_password"] = md5($_POST["admin_username"].$_POST["admin_password"]);
	}
	else
		$_POST["admin_password"] = $_POST["old_admin_password"];

	//check if config.php has write access
	if (is_writable("config.php"))
	{
		if (isset($_POST["enablerss"]))
			$enablerss = "true";
		else
			$enablerss = "false";
		if (isset($_POST["scrape"]))
			$scrape = "true";
		else
			$scrape = "false";
		if (isset($_POST["NAT"]))
			$NAT = "true";
		else
			$NAT = "false";
		if (isset($_POST["persist"]))
			$persist = "true";
		else
			$persist = "false";
		if (isset($_POST["ip_override"]))
			$ip_override = "true";
		else
			$ip_override = "false";
		if (isset($_POST["countbytes"]))
			$countbytes = "true";
		else
			$countbytes = "false";

		//write config.php file
		$fd = fopen("config.php", "w") or die(errorMessage() . "Warning: write to config.php!</p>");
		fwrite($fd,
		"<?php //Please do NOT edit this file, use the admin page for changes.\n" .
		"\$GLOBALS['scrape'] = " . $scrape . ";\n" .
		"\$GLOBALS['report_interval'] = " . htmlspecialchars($_POST["report_interval"]) . ";\n" .
		"\$GLOBALS['min_interval'] = " . htmlspecialchars($_POST["min_interval"]) . ";\n" .
		"\$GLOBALS['maxpeers'] = " . htmlspecialchars($_POST["maxpeers"]) . ";\n" .
		"\$GLOBALS['NAT'] = " . $NAT . ";\n" .
		"\$GLOBALS['persist'] = " . $persist . ";\n" .
		"\$GLOBALS['ip_override'] = " . $ip_override . ";\n" .
		"\$GLOBALS['countbytes'] = " . $countbytes . ";\n" .
		"\$upload_username = '" . htmlspecialchars($_POST["upload_username"]) . "';\n" .
		"\$upload_password = '" . htmlspecialchars($_POST["upload_password"]) . "';\n" .
		"\$admin_username = '" . htmlspecialchars($_POST["admin_username"]) . "';\n" .
		"\$admin_password = '" . htmlspecialchars($_POST["admin_password"]) . "';\n" .
		"\$GLOBALS['title'] = '" . htmlspecialchars(addquotes($_POST["title"])) . "';\n" .
		"\$dbhost = '" . htmlspecialchars($_POST["dbhost"]) . "';\n" .
		"\$dbuser = '" . htmlspecialchars($_POST["dbuser"]) . "';\n" .
		"\$dbpass = '" . htmlspecialchars($_POST["dbpass"]) . "';\n" .
		"\$database = '" . htmlspecialchars($_POST["database"]) . "';\n" .
		"\$enablerss = " . $enablerss . ";\n" .
		"\$rss_title = '" . htmlspecialchars(addquotes($_POST["rss_title"])) . "';\n" .
		"\$rss_link = '" . htmlspecialchars($_POST["rss_link"]) . "';\n" .
		"\$rss_description = '" . htmlspecialchars(addquotes($_POST["rss_description"])) . "';\n" .
		"\$website_url = '" . htmlspecialchars($_POST["website_url"]) . "';\n" .
		"\$GLOBALS['max_upload_rate'] = " . htmlspecialchars($_POST['max_upload_rate']) . ";\n" .
		"\$GLOBALS['max_uploads'] = " . htmlspecialchars($_POST['max_uploads']) . ";\n" .
		"\$timezone = '" . htmlspecialchars($_POST["timezone"]) . "';\n" .
		"\$prefix = '" . htmlspecialchars($_POST["prefix"]) . "';\n" .
		"?>"
		);

		fclose($fd);
		echo "<br><p class=\"success\">config.php file was edited successfully!</p>\n";

		//run RSS generator
		require_once("rss_generator.php");
	}
	else
	{
		echo errorMessage() . "config.php was not able to be written.  Please check the permissions and try again.</p>\n";
	}
}

?>
<br>
<br>
<a href="admin.php"><img src="images/admin.png" border="0" class="icon" alt="Admin Page" title="Admin Page" /></a><a href="admin.php">Return to Admin Page</a>

<?php
llxFooter('$Date: 2009/02/24 21:57:54 $ - $Revision: 1.4 $');
?>

