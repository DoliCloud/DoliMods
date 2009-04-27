<?php

  // core config parameters
  $sDefaultLanguage      = "en-gb";
  $sConfigDefaultView    = "thismonth.all";
  $bConfigChangeSites    = true;
  $bConfigUpdateSites    = true;
  $sUpdateSiteFilename   = "xml_update.php";

  //$val="E:/Mes Developpements/awstats/test/awstats/result/";
  //$val='E:\\Mes Developpements\\awstats\\test\\awstats\\result\\';

  // individual site configuration

  // Change LDR to work with Dolibarr
  $aConfig[$_GET["config"]] = array(
	"statspath"   => $conf->global->AWSTATS_DATA_DIR,
    "updatepath"  => $conf->global->AWSTATS_PROG_DIR,
    "siteurl"     => "",
    "sitename"    => "",
    "theme"       => "default",
    "fadespeed"   => 250,
    "password"    => "",
    "includes"    => "",
    "language"    => ""
  );

/*
  $aConfig["site2"] = array(
    "statspath"   => $conf->global->AWSTATS_DATA_DIR,
    "updatepath"  => "/path/to/awstats.pl/",
    "siteurl"     => "http://www.my-2nd-domain.com",
    "sitename"    => "",
    "theme"       => "default",
    "fadespeed"   => 250,
    "password"    => "my-2nd-password",
    "includes"    => "",
    "language"    => "en-gb"
  );
*/
?>
