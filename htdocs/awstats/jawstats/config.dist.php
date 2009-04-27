<?php

  // core config parameters
  $sDefaultLanguage      = "en-gb";
  $sConfigDefaultView    = "thismonth.all";
  $bConfigChangeSites    = true;
  $bConfigUpdateSites    = true;
  $sUpdateSiteFilename   = "xml_update.php";

  // individual site configuration
  $aConfig["site1"] = array(
    "statspath"   => "/path/to/data/",
    "updatepath"  => "/path/to/awstats.pl/",
    "siteurl"     => "http://www.my-1st-domain.com",
    "sitename"    => "",
    "theme"       => "default",
    "fadespeed"   => 250,
    "password"    => "my-1st-password",
    "includes"    => "",
    "language"    => "en-gb"
  );

  $aConfig["site2"] = array(
    "statspath"   => "/path/to/data/",
    "updatepath"  => "/path/to/awstats.pl/",
    "siteurl"     => "http://www.my-2nd-domain.com",
    "sitename"    => "",
    "theme"       => "default",
    "fadespeed"   => 250,
    "password"    => "my-2nd-password",
    "includes"    => "",
    "language"    => "en-gb"
  );

?>
