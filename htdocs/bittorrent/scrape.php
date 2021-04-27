<?php

$_SERVER["PATH_INFO"] = "/scrape";

$res=@include "../master.inc.php";
if (! $res) @include "../../../dolibarr/htdocs/master.inc.php";	// Used on dev env only

require "tracker.php";
exit;
