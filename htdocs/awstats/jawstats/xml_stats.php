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

  // create class
  $clsAWStats = new clsAWStats($g_sConfig,
                               $g_aConfig["statspath"],
                               $_GET["year"],
                               $_GET["month"]);

  // create xml
  $sSection = strtoupper($_GET["section"]);
  switch ($sSection) {
    case "BROWSER":
    case "DAY":
    case "DOMAIN":
    case "ERRORS":
    case "FILETYPES":
    case "KEYWORDS":
    case "PAGEREFS":
    case "OS":
    case "ROBOT":
    case "SEARCHWORDS":
    case "SEREFERRALS":
    case "SESSION":
    case "SIDER":
    case "SIDER_404":
    case "TIME":
      $clsAWStats->OutputXML($clsAWStats->CreateXMLString($sSection));
      break;
  }

?>