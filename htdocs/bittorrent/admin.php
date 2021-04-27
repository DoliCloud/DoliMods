<?php

include "./pre.inc.php";
require_once "funcsv2.php";



/*
 * View
 */

$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', $dolibarr_main_url_root);

$tracker_url=$urlwithouturlroot.dol_buildpath('/bittorrent/announce.php', 1);
$helpurl=$urlwithouturlroot.dol_buildpath('/bittorrent/docs/help.html', 1);

//$helpurl='EN:'.$helpurl.'|FR:'.$helpurl.'|ES:'.$helpurl;
llxHeader('', 'BitTorrent', $helpurl);

$form=new Form($db);

print_fiche_titre('BitTorrent admin page');

print '<br>';
print '<b>Tracker URL:</b> '.$tracker_url.'<br>';
print '<br>';

?>
<br>

<table class="border" style="width: 100%"><tr><td class="tdtop">
<a href="newtorrents.php"><img src="images/add.png" border="0" class="icon" alt="Add Torrent" title="Add Torrent" /></a><a href="newtorrents.php">Add Torrent to tracker database</a><br>
<a href="batch_upload.php"><img src="images/batch_upload.png" border="0" class="icon" alt="Batch Upload Torrents" title="Batch Upload Torrents" /></a><a href="batch_upload.php">Add Torrents to Tracker database (batch mode)</a><br>
<a href="edit_database.php"><img src="images/database.png" border="0" class="icon" alt="Edit Torrent in Database" title="Edit Torrent in Database" /></a><a href="edit_database.php">Edit Torrent Already in Database</a><br>
<a href="deleter.php"><img src="images/delete.png" border="0" class="icon" alt="Delete Torrent" title="Delete Torrent" /></a><a href="deleter.php">Delete Torrent from Tracker Database</a><br>
</td></tr></table>
<br>
<table class="border" style="width: 100%"><tr><td class="tdtop">
<a href="index.php"><img src="images/download.png" border="0" class="icon" alt="Tracker Statistics" title="Tracker Statistics" /></a><a href="index.php">Show current Tracker Statistics</a><br>
<a href="uploadstats.php"><img src="images/download.png" border="0" class="icon" alt="Upload Statistics" title="Upload Statistics" /></a><a href="uploadstats.php">Show upload Statistics</a><br>
<a href="statistics.php"><img src="images/userstats.png" border="0" class="icon" alt="User Statistics" title="User Statistics" /></a><a href="statistics.php">Detailed User Statistics from Tracker</a><br>
<a href="sanity.php"><img src="images/check.png" border="0" class="icon" alt="Check for Expired Peers" title="Check for Expired Peers" /></a><a href="sanity.php">Check Tracker for Expired Peers</a><br>
</td></tr></table>
<br>
<table class="border" style="width: 100%"><tr><td class="tdtop">
<a href="DumpTorrentCGI.php"><img src="images/torrent.png" border="0" class="icon" alt="Show Information on Torrent" title="Show Information on Torrent" /></a><a href="DumpTorrentCGI.php">Show Information on a Torrent File</a><br>
<a href="editconfig.php"><img src="images/edit.png" border="0" class="icon" alt="View Config File" title="View Config File" /></a><a href="editconfig.php">View Configuration Settings</a><br>
</td></tr></table>

<?php
//Check for install.php file, security risk if still available
if (file_exists("install.php")) {
	echo errorMessage() . "Your install.php file has NOT been deleted.  This is a security risk, please delete it immediately.</p>\n";
}

llxFooter();
?>
