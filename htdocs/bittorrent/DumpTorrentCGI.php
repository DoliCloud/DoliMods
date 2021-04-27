<?php
include "./pre.inc.php";
require_once "funcsv2.php";

$tracker_url = $website_url . substr($_SERVER['REQUEST_URI'], 0, -15) . "announce.php";

llxHeader('', 'BitTorrent', $website_url.'/bittorrent/docs/help.html');

$form=new Form($db);

print_fiche_titre('Informations of an external torrent file');

require_once "torrent_functions.php";
?>
<table width="50%" border=0><tr><td>
This script parses a torrent file and displays detailed information about it.
</td></tr>
</table><br>
<form enctype="multipart/form-data" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
Torrent file: <input type="file" name="torrent" size="40"><br>
<br>
OR
<br><br>
Torrent URL: <input type=text name="url" size="50"><br><br>
Output type: <select name="output">
<option value="-1">Auto-detect</option>
<option value="0">Classic (raw)</option>
<option value="1">.torrent file</option>
<option value="2">/scrape</option>
<option value="3">/announce</option>
</select><br><br>
<input type="submit" value="Decode" class="button">
</form>
<br>

<a href="admin.php"><img src="images/admin.png" border="0" class="icon" alt="Admin Page" title="Admin Page" /></a><a href="admin.php">Return to Admin Page</a>

<?php
llxFooter();
?>
