<?php
// LDR Change TO WORK WITH DOLIBARR
if (! defined('NOCSRFCHECK')) define('NOCSRFCHECK',1);
if (! defined('DISABLE_PROTOTYPE')) define('DISABLE_PROTOTYPE',1);
if (! defined('DISABLE_SCRIPTACULOUS')) define('DISABLE_SCRIPTACULOUS',1);
include("../pre.inc.php");
$ret=0;
if (! $ret && file_exists(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php")) $ret=include_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
if (! $ret) include_once(DOL_DOCUMENT_ROOT_BIS."/core/class/html.formfile.class.php");
global $conf;


/*
 * JAWStats 0.7 Web Statistics
 *
 * Copyright (c) 2009 Jon Combe (jawstats.com)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

  // LDR Change TO WORK WITH DOLIBARR
  //header('Content-Type: text/html; charset="utf-8"', true);
  llxHeader();
  //error_reporting(0);
  error_reporting(E_ALL);
  set_error_handler("ErrorHandler");

  // javascript caching
  $gc_sJavascriptVersion = "200901251254";
  $g_aTranslation = array();
  $g_aCurrentTranslation = array();

	// includes
  require_once "clsAWStats.php";
  require_once "languages/translations.php";
  require_once "config.php";

  ValidateConfig();

  // select configuraton and translations
  $g_sConfig = GetConfig();
  $g_aConfig = $aConfig[$g_sConfig];
  $sLanguageCode = SetTranslation();

  // external include files
  if ((isset($g_aConfig["includes"]) == true) && (strlen($g_aConfig["includes"]) > 0)) {
    $aIncludes = explode(",", $g_aConfig["includes"]);
    foreach ($aIncludes as $sInclude) {
      include $sInclude;
    }
  }

  // get date range and valid log file
  $g_dtStatsMonth = ValidateDate($_GET["year"], $_GET["month"]);
  $g_aLogFiles = GetLogList($g_sConfig, $g_aConfig["statspath"]);
  $g_iThisLog = -1;
  for ($iIndex = 0; $iIndex < count($g_aLogFiles); $iIndex++) {
    if (($g_dtStatsMonth == $g_aLogFiles[$iIndex][0]) && ($g_aLogFiles[$iIndex][1] == true)) {
      $g_iThisLog = $iIndex;
      break;
    }
  }
  if ($g_iThisLog < 0) {
    if (count($g_aLogFiles) > 0) {
      $g_iThisLog = 0;
    } else {
      Error("NoLogsFound");
    }
  }

  // validate current view
  if (ValidateView($_GET["view"]) == true) {
    $sCurrentView = $_GET["view"];
  } else {
    $sCurrentView = $sConfigDefaultView;
  }

  // create class
  $clsAWStats = new clsAWStats($g_sConfig,
                               $g_aConfig["statspath"],
                               date("Y", $g_aLogFiles[$g_iThisLog][0]),
                               date("n", $g_aLogFiles[$g_iThisLog][0]));
  if ($clsAWStats->bLoaded != true) {
    Error("CannotOpenLog");
  }

  // days in month
  if (($clsAWStats->iYear == date("Y")) && ($clsAWStats->iMonth == date("n"))) {
    $iDaysInMonth = abs(date("s", $clsAWStats->dtLastUpdate));
    $iDaysInMonth += (abs(date("i", $clsAWStats->dtLastUpdate)) * 60);
    $iDaysInMonth += (abs(date("H", $clsAWStats->dtLastUpdate)) * 60 * 60);
    $iDaysInMonth = abs(date("j", $clsAWStats->dtLastUpdate) - 1) + ($iDaysInMonth / (60 * 60 * 24));
  } else {
    $iDaysInMonth = date("d", mktime (0, 0, 0, date("n", $clsAWStats->dtLastUpdate), 0, date("Y", $clsAWStats->dtLastUpdate)));
  }

  // start of the month
  $dtStartOfMonth = mktime(0, 0, 0, $clsAWStats->iMonth, 1, $clsAWStats->iYear);
  $iDailyVisitAvg = ($clsAWStats->iTotalVisits / $iDaysInMonth);
  $iDailyUniqueAvg = ($clsAWStats->iTotalUnique / $iDaysInMonth);

?>
<!-- LDR

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo str_replace("[SITE]", GetSiteName(), str_replace("[MONTH]", Lang(date("F", $g_aLogFiles[$g_iThisLog][0])), str_replace("[YEAR]", date("Y", $g_aLogFiles[$g_iThisLog][0]), Lang("Statistics for [SITE] in [MONTH] [YEAR]")))) ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="themes/<?php echo $g_aConfig["theme"] ?>/style.css" type="text/css" />
-->


  <script type="text/javascript" src="js/packed.js?<?php echo $gc_sJavascriptVersion ?>"></script>

  <!--
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
  <script type="text/javascript" src="js/swfobject.js"></script>
  -->

  <script type="text/javascript" src="js/constants.js?<?php echo $gc_sJavascriptVersion ?>"></script>
  <script type="text/javascript" src="js/jawstats.js?<?php echo $gc_sJavascriptVersion ?>"></script>
  <script type="text/javascript">
    var g_sConfig = "<?php echo $g_sConfig ?>";
    var g_iYear = <?php echo date("Y", $g_aLogFiles[$g_iThisLog][0]) ?>;
    var g_iMonth = <?php echo date("n", $g_aLogFiles[$g_iThisLog][0]) ?>;
    var g_sCurrentView = "<?php echo $sCurrentView ?>";
    var g_dtLastUpdate = <?php echo $clsAWStats->dtLastUpdate ?>;
    var g_iFadeSpeed = <?php echo $g_aConfig["fadespeed"] ?>;
    var g_bUseStaticXML = <?php echo BooleanToText($g_aConfig["staticxml"]) ?>;
    var g_sLanguage = "<?php echo $sLanguageCode ?>";
    var sThemeDir = "<?php echo $g_aConfig["theme"] ?>";
    var sUpdateFilename = "<?php echo $sUpdateSiteFilename ?>";
  </script>
  <script type="text/javascript" src="themes/<?php echo $g_aConfig["theme"] ?>/style.js?<?php echo $gc_sJavascriptVersion ?>"></script>
<?php
  if ($sLanguageCode != "en-gb") {
    echo "  <script type=\"text/javascript\" src=\"languages/" . $sLanguageCode . ".js\"></script>\n";
  }
?>

<!-- LDR Change TO WORK WITH DOLIBARR
  <script type="text/javascript" src="http://version.jawstats.com/version.js"></script>

</head>

<body>
-->

  <div id="tools">
<?php

  echo ToolChangeMonth();
  echo ToolChangeSite();
  echo ToolUpdateSite();
  echo ToolChangeLanguage();

?>
  </div>

  <div id="toolmenu">
    <div class="container">
<?php

  // change month
  echo "<span>";
  if ($g_iThisLog < (count($g_aLogFiles) - 1)) {
    echo "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/first.gif\" onmouseover=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/first_on.gif'\" onmouseout=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/first.gif'\" class=\"changemonth\" onclick=\"ChangeMonth(" . date("Y,n", $g_aLogFiles[count($g_aLogFiles) - 1][0]) . ")\" />" .
         "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/prev.gif\" onmouseover=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/prev_on.gif'\" onmouseout=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/prev.gif'\" class=\"changemonth\" onclick=\"ChangeMonth(" . date("Y,n", $g_aLogFiles[$g_iThisLog + 1][0]) . ")\" />";
  } else {
    echo "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/first_off.gif\" class=\"changemonthOff\" />" .
         "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/prev_off.gif\" class=\"changemonthOff\" />";
  }
  echo "<span onclick=\"ShowTools('toolMonth');\">" . Lang("Change Month") . "</span>";
  if ($g_iThisLog > 0) {
    echo "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/next.gif\" onmouseover=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/next_on.gif'\" onmouseout=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/next.gif'\" class=\"changemonth\" onclick=\"ChangeMonth(" . date("Y,n", $g_aLogFiles[$g_iThisLog - 1][0]) . ")\" />" .
         "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/last.gif\" onmouseover=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/last_on.gif'\" onmouseout=\"this.src='themes/" . $g_aConfig["theme"] . "/changemonth/last.gif'\" class=\"changemonth\" onclick=\"ChangeMonth(" . date("Y,n", $g_aLogFiles[0][0]) . ")\" /> ";
  } else {
    echo "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/next_off.gif\" class=\"changemonthOff\" />" .
         "<img src=\"themes/" . $g_aConfig["theme"] . "/changemonth/last_off.gif\" class=\"changemonthOff\" />";
  }
  echo "</span>\n";

  // change site (if available)
  if (($bConfigChangeSites == true) && (count($aConfig) > 1)) {
    echo "<span onclick=\"ShowTools('toolSite')\">" . Lang("Change Site") . "</span>\n";
  }

  // update site (if available)
  if ($bConfigUpdateSites == true) {
    echo "<span onclick=\"ShowTools('toolUpdate')\">" . Lang("Update Site") . "</span>\n";
  }

  // change language
  echo "<span id=\"toolLanguageButton\" onclick=\"ShowTools('toolLanguage')\">" . Lang("Change Language") .
       "<img src=\"themes/" . $g_aConfig["theme"] . "/images/change_language.gif\" /></span>\n";

?>
    </div>
  </div>

  <div id="header">
    <div class="container">
      <?php echo DrawHeader($g_aLogFiles[$g_iThisLog][0]) ?>


      <div id="summary">
<?php

  $sTemp = Lang("Last updated [DAYNAME], [DATE] [MONTH] [YEAR] at [TIME] [ELAPSEDTIME]. A total of [TOTALVISITORS] visitors ([UNIQUEVISITORS] unique) this month, an average of [DAILYAVERAGE] per day ([DAILYUNIQUE] unique).");
  $sTemp = str_replace("[DAYNAME]", "<span>" . Lang(date("l", $clsAWStats->dtLastUpdate)), $sTemp);
  $sTemp = str_replace("[YEAR]", date("Y", $clsAWStats->dtLastUpdate) . "</span>", $sTemp);
  $sTemp = str_replace("[DATE]", Lang(date("jS", $clsAWStats->dtLastUpdate)), $sTemp);
  $sTemp = str_replace("[MONTH]", Lang(date("F", $clsAWStats->dtLastUpdate)), $sTemp);
  $sTemp = str_replace("[TIME]", "<span>" . date("H:i", $clsAWStats->dtLastUpdate) . "</span>", $sTemp);
  $sTemp = str_replace("[ELAPSEDTIME]", ElapsedTime(time() - $clsAWStats->dtLastUpdate), $sTemp);
  $sTemp = str_replace("[TOTALVISITORS]", "<span>" . number_format($clsAWStats->iTotalVisits) . "</span>", $sTemp);
  $sTemp = str_replace("[UNIQUEVISITORS]", number_format($clsAWStats->iTotalUnique), $sTemp);
  $sTemp = str_replace("[DAILYAVERAGE]", "<span>" . number_format($iDailyVisitAvg, 1) . "</span>", $sTemp);
  $sTemp = str_replace("[DAILYUNIQUE]", number_format($iDailyUniqueAvg, 1), $sTemp);
  echo $sTemp;

?>
      </div>
      <div id="menu">
        <ul>
          <li id="tabthismonth"><span onclick="ChangeTab(this, 'thismonth.all')"><?php echo Lang("This Month"); ?></span></li>
          <li id="taballmonths"><span onclick="ChangeTab(this, 'allmonths.all')"><?php echo Lang("All Months"); ?></span></li>
          <li id="tabtime"><span onclick="ChangeTab(this, 'time')"><?php echo Lang("Hours"); ?></span></li>
          <li id="tabbrowser"><span onclick="ChangeTab(this, 'browser.family')"><?php echo Lang("Browsers"); ?></span></li>
          <li id="tabcountry"><span onclick="ChangeTab(this, 'country.all')"><?php echo Lang("Countries"); ?></span></li>
          <li id="tabfiletypes"><span onclick="ChangeTab(this, 'filetypes')"><?php echo Lang("Filetypes"); ?></span></li>
          <li id="tabos"><span onclick="ChangeTab(this, 'os.family')"><?php echo Lang("Operating Systems"); ?></span></li>
          <li id="tabpages"><span onclick="ChangeTab(this, 'pages.topPages')"><?php echo Lang("Pages"); ?></span></li>
          <li id="tabpagerefs"><span onclick="ChangeTab(this, 'pagerefs.se')"><?php echo Lang("Referrers"); ?></span></li>
          <li id="tabrobots"><span onclick="ChangeTab(this, 'robots')"><?php echo Lang("Spiders"); ?></span></li>
          <li id="tabsearches"><span onclick="ChangeTab(this, 'searches.keywords')"><?php echo Lang("Searches"); ?></span></li>
          <li id="tabsession"><span onclick="ChangeTab(this, 'session')"><?php echo Lang("Sessions"); ?></span></li>
          <li id="tabstatus"><span onclick="ChangeTab(this, 'status')"><?php echo Lang("Status"); ?></span></li>
        </ul>
      </div>
      <br style="clear: both" />
      <div id="loading">&nbsp;</div>
    </div>
  </div>
  <div id="main">
    <div class="container">
      <div id="content">&nbsp;</div>
      <div id="footer">
        <?php echo DrawFooter(); ?>
        <span id="version">&nbsp;</span>
      </div>
    </div>
  </div>


<!--  LDR Change TO WORK WITH DOLIBARR -->
<?php
print '</div>';
print '</td></tr></table>';
print '</body>';
print '</html>';
?>
<!--
</body>
</html>
-->

<?php
    // error display
  function Error($sReason, $sExtra="") {
    // echo "ERROR!<br />" . $sReason;
  	switch ($sReason) {
      case "BadConfig":
        $sProblem     = str_replace("[FILENAME]", "\"config.php\"", Lang("There is an error in [FILENAME]"));
        $sResolution  = "<p>" . str_replace("[VARIABLE]", ("<i>" . $sExtra . "</i>"), Lang("The variable [VARIABLE] is missing or invalid.")) . "</p>";
        break;
      case "BadConfigNoSites":
        $sProblem     = str_replace("[FILENAME]", "\"config.php\"", Lang("There is an error in [FILENAME]"));
        $sResolution  = "<p>" . Lang("No individual AWStats configurations have been defined.") . "</p>";
        break;
      case "CannotLoadClass":
        $sProblem     = str_replace("[FILENAME]", "\"clsAWStats.php\"", Lang("Cannot find required file [FILENAME]"));
        $sResolution  = "<p>" . Lang("At least one file required by JAWStats has been deleted, renamed or corrupted.") . "</p>";
        break;
      case "CannotLoadConfig":
        $sProblem     = str_replace("[FILENAME]", "\"config.php\"", Lang("Cannot find required file [FILENAME]"));
        $sResolution = "<p>" . str_replace("[CONFIGDIST]", "<i>config.dist.php</i>", str_replace("[CONFIG]", "<i>config.php</i>", Lang("JAWStats cannot find it's configuration file, [CONFIG]. Did you successfully copy and rename the [CONFIGDIST] file?"))) . "</p>";
        break;
      case "CannotLoadLanguage":
        $sProblem     = str_replace("[FILENAME]", "\"languages/translations.php\"", Lang("Cannot find required file [FILENAME]"));
        $sResolution  = "<p>" . Lang("At least one file required by JAWStats has been deleted, renamed or corrupted.") . "</p>";
        break;
      case "CannotOpenLog":
        $sStatsPath = $GLOBALS["aConfig"][$GLOBALS["g_sConfig"]]["statspath"];
        $sProblem     = Lang("JAWStats could not open an AWStats log file");
        $sResolution  = "<p>" . Lang("Is the specified AWStats log file directory correct? Does it have a trailing slash?") . "<br />" .
                        str_replace("[VARIABLE]", "<strong>\"statspath\"</strong>", str_replace("[CONFIG]", "<i>config.php</i>", Lang("The problem may be the variable [VARIABLE] in your [CONFIG] file."))) . "</p>" .
                        "<p>" . str_replace("[FOLDER]", "<strong>" . $sStatsPath . "</strong>\n", str_replace("[FILE]", "<strong>awstats" . date("Yn") . "." . $GLOBALS["g_sConfig"] . ".txt</strong>", Lang("The data file being looked for is [FILE] in folder [FOLDER]")));
        if (substr($sStatsPath, -1) != "/") {
          $sResolution  .= "<br />" . str_replace("[FOLDER]", "<strong>" . $sStatsPath . "</strong>", Lang("Try changing the folder to [FOLDER]"));
        }
        $sResolution  .= "</p>";
        break;
      case "NoLogsFound":
        $sStatsPath = $GLOBALS["aConfig"][$GLOBALS["g_sConfig"]]["statspath"];
        $sProblem     = Lang("No AWStats Log Files Found");
        $sResolution  = "<p>JAWStats cannot find any AWStats data files in the specified directory: <strong>" . $sStatsPath . "</strong><br />" .
                        "Is this the correct folder? Is your config name, <i>" . $GLOBALS["g_sConfig"] . "</i>, correct?</p>\n";
        break;
      case "Unknown":
        $sProblem     = "";
        $sResolution  = "<p>" . $sExtra . "</p>\n";
        break;
    }
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" " .
         "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n" .
         "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n" .
         "<head>\n" .
         "<title>JAWStats</title>\n" .
         "<style type=\"text/css\">\n" .
         "html, body { background: #33332d; border: 0; color: #eee; font-family: arial, helvetica, sans-serif; font-size: 15px; margin: 20px; padding: 0; }\n" .
         "a { color: #9fb4cc; text-decoration: none; }\n" .
         "a:hover { color: #fff; text-decoration: underline; }\n" .
         "h1 { border-bottom: 1px solid #cccc9f; color: #eee; font-size: 22px; font-weight: normal; } \n" .
         "h1 span { color: #cccc9f !important; font-size: 16px; } \n" .
         "p { margin: 20px 30px; }\n" .
         "</style>\n" .
         "</head>\n<body>\n" .
         "<h1><span>" . Lang("An error has occured") . ":</span><br />" . $sProblem . "</h1>\n" . $sResolution .
         "<p>" . str_replace("[LINKSTART]", "<a href=\"http://www.jawstats.com/documentation\" target=\"_blank\">", str_replace("[LINKEND]", "</a>", Lang("Please refer to the [LINKSTART]installation instructions[LINKEND] for more information."))) . "</p>\n" .
         "</body>\n</html>";
    exit;
  }


  // translator
  function Lang($sString) {

    if (isset($GLOBALS["g_aCurrentTranslation"][$sString]) == true) {
      return $GLOBALS["g_aCurrentTranslation"][$sString];
    } else {
      return $sString;
    }
  }

  // output booleans for javascript
  function BooleanToText($bValue) {
    if ($bValue == true) {
      return "true";
    } else {
      return "false";
    }
  }

  // error handler
  function ErrorHandler ($errno, $errstr, $errfile, $errline, $errcontext) {
    if (strpos($errfile, "index.php") != false) {
      switch ($errline) {
        case 39:
          Error("CannotLoadClass");
          break;
        case 40:
          Error("CannotLoadLanguage");
          break;
        case 41:
          Error("CannotLoadConfig");
          break;
        default:
          //Error("Unknown", ("Line #" . $errline . "<br />" . $errstr));
      }
    }
  }

?>
