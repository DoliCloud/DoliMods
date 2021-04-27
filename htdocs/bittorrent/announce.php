<?php

/// Use this file as an alternative to tracker.php/announce
/// for TorrentSpy and other /scrape support.

$_SERVER["PATH_INFO"] = "/announce";

$res=@include "../master.inc.php";
if (! $res) @include "../../../dolibarr/htdocs/master.inc.php";	// Used on dev env only

require "tracker.php";
exit;
