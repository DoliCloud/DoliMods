<?php

include("./pre.inc.php");
require_once("funcsv2.php");

llxHeader();

$form=new Form($db);


?>

<h1>Upload Statistics</h1>
<h2>This may be wildly inaccurate because when torrents are deleted, the bittorrent traffic is removed yet the HTTP traffic stays the same.</h2>
<?php
if ($GLOBALS["persist"])
	$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
else
	$db = mysql_connect($dbhost, $dbuser, $dbpass) or die(errorMessage() . "Tracker error: can't connect to database - " . mysql_error() . "</p>");
mysql_select_db($database) or die(errorMessage() . "Tracker error: can't open database $database - " . mysql_error() . "</p>");

$query = "SELECT SUM(".$prefix."summary.dlbytes) FROM ".$prefix."summary";
$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
$data = mysql_fetch_row($results);
if ($data[0] == null)
	$btuploaded = 0;
else
	$btuploaded = $data[0];

$query = "SELECT total_uploaded FROM ".$prefix."speedlimit";
$results = mysql_query($query) or die(errorMessage() . "Can't do SQL query - " . mysql_error() . "</p>");
$data = mysql_fetch_row($results);
$httpuploaded = $data[0];
?>
<br>
<center>
<table>
<tr><th>HTTP Seeding Uploaded<span class="notice">*</span></th>
<th>Bittorrent P2P Seeding Uploaded</th></tr>
<tr>
<td align="center">
<?php
echo bytesToString($httpuploaded);
?>
</td>
<td align="center">
<?php
echo bytesToString($btuploaded);
?>
</td>
</tr>
<tr>
<td align="center">
<?php
if ($httpuploaded + $btuploaded != 0)
	echo round(($httpuploaded / ($httpuploaded + $btuploaded))*100, 2) . "%";
else
	echo "0%";
?>
</td>
<td align="center">
<?php
if ($httpuploaded + $btuploaded != 0)
	echo round(($btuploaded / ($httpuploaded + $btuploaded))*100, 2) . "%";
else
	echo "0%";
?>
</td>
</tr>
</table>
</center>
<p align="center">
<?php
echo "Total Uploaded: " . bytesToString($httpuploaded + $btuploaded);
?>
</p>
<br>
<span class="notice">* - This does not include the GetRight HTTP seeding format which links directly to files.</span>
<br><br>
<a href="admin.php"><img src="images/admin.png" border="0" class="icon" alt="Admin Page" title="Admin Page" /></a><a href="admin.php">Return to Admin Page</a>

<?php
llxFooter('$Date: 2009/02/23 22:54:51 $ - $Revision: 1.1 $');
?>
