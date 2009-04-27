<?php
// ADD LDR TO WORK WITH DOLIBARR
include("../pre.inc.php");
$ret=include_once(DOL_DOCUMENT_ROOT."/html.formfile.class.php");
if (! $ret) include_once(DOL_DOCUMENT_ROOT_BIS."/html.formfile.class.php");
global $conf;
define('DISABLE_PROTOTYPE',1);
define('DISABLE_SCRIPTACULOUS',1);


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

	// includes
  require_once "config.php";
  require_once "clsAWStats.php";

  // external include files
  if ((isset($g_aConfig["includes"]) == true) && (strlen($g_aConfig["includes"]) > 0)) {
    $aIncludes = explode(",", $g_aConfig["includes"]);
    foreach ($aIncludes as $sInclude) {
      include $sInclude;
    }
  }

  // select configuraton
  $g_sConfig = GetConfig();
  $g_aConfig = $aConfig[$g_sConfig];

  // get date range and valid log file
  $g_dtStatsMonth = ValidateDate($_GET["year"], $_GET["month"]);
  $g_aLogFiles = GetLogList($g_sConfig,
                            $g_aConfig["statspath"]);

  // create xml
  $aXML = array();
  $aXML[] = "<data>";
  $iYear = date("Y", $g_aLogFiles[count($g_aLogFiles) - 1][0]);
  $iMonth = date("n", $g_aLogFiles[count($g_aLogFiles) - 1][0]);
  $iMaxLastUpdate = 0;
  for ($iIndex = (count($g_aLogFiles) - 1); $iIndex >= 0; $iIndex--) {
    $dtNextMonth = mktime (0, 0, 0, ($iMonth + 1), 0, $iYear);
    $clsAWStats = new clsAWStats($g_sConfig,
                                 $g_aConfig["statspath"],
                                 date("Y", $g_aLogFiles[$iIndex][0]),
                                 date("n", $g_aLogFiles[$iIndex][0]));

    if ($clsAWStats->dtLastUpdate > $iMaxLastUpdate) {
      $iMaxLastUpdate = $clsAWStats->dtLastUpdate;
    }

    // sum pages, hits & bandwidth
    $aTemp  = $clsAWStats->GetSection("DAY");
    $iPages = 0;
    $iHits  = 0;
    $iBW    = 0;
    for ($iIndexItem = 0; $iIndexItem < count($aTemp); $iIndexItem++) {
      $iHits  += $aTemp[$iIndexItem][2];
      $iPages += $aTemp[$iIndexItem][1];
      $iBW    += $aTemp[$iIndexItem][3];
    }

    // days in month
    $iDaysInMonth = date("d", $dtNextMonth);
    if ($iIndex == 0) {
      $iPartDay = abs(date("s", $clsAWStats->dtLastUpdate));
      $iPartDay += (abs(date("i", $clsAWStats->dtLastUpdate)) * 60);
      $iPartDay += (abs(date("H", $clsAWStats->dtLastUpdate)) * 60 * 60);
      $iPartDay = $iPartDay / (60 * 60 * 24);
      $iDaysInMonth = number_format((abs(date("d", $clsAWStats->dtLastUpdate)) - 1) + $iPartDay, 3);
    }

    // create xml body
    if ($g_aLogFiles[$iIndex][1] == true) {
      $aXML[] = "<month month=\"" . date("n", $g_aLogFiles[$iIndex][0]) . "\" " .
                "year=\"" . date("Y", $g_aLogFiles[$iIndex][0]) . "\" " .
                "daysinmonth=\"" . $iDaysInMonth . "\" " .
                "visits=\"" . $clsAWStats->iTotalVisits . "\" " .
                "uniques=\"" . $clsAWStats->iTotalUnique . "\" " .
                "pages=\"" . $iPages . "\" " .
                "hits=\"" . $iHits . "\" " .
                "bw=\"" . $iBW . "\" " .
                "/>\n";
    } else {
      $aXML[] = "<month month=\"" . date("n", $g_aLogFiles[$iIndex][0]) . "\" " .
                "year=\"" . date("Y", $g_aLogFiles[$iIndex][0]) . "\" " .
                "daysinmonth=\"" . $iDaysInMonth . "\" " .
                "visits=\"0\" uniques=\"0\" pages=\"0\" hits=\"0\" bw=\"0\" />\n";
    }

    // increment month
    $iMonth++;
  }

  // output
  $aXML[] = "</data>";
  $aXML[] = "<info lastupdate=\"" . $iMaxLastUpdate . "\" />\n";
  $clsAWStats->OutputXML(implode("", $aXML));
?>