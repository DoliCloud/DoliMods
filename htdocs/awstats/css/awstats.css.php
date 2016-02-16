<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * $Id: awstats.css.php,v 1.7 2011/03/29 23:17:18 eldy Exp $
 */

//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');  // Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');    // Not disabled to increase speed. Language code is found on url.
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');  // Not disabled cause need to do translations
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK',1);
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL',1);
if (! defined('NOLOGIN'))         define('NOLOGIN',1);
if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU',1);
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML',1);
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX','1');

session_cache_limiter(FALSE);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at
// each Dolibarr page access.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');

// On the fly GZIP compression for all pages (if browser support it). Must set the bit 3 of constant to 1.
if (isset($conf->global->MAIN_OPTIMIZE_SPEED) && ($conf->global->MAIN_OPTIMIZE_SPEED & 0x04)) { ob_start("ob_gzhandler"); }

if (! empty($_GET["lang"])) $langs->setDefaultLang($_GET["lang"]);  // If language was forced on URL by the main.inc.php
$langs->load("main",0,1);
$right=($langs->trans("DIRECTION")=='rtl'?'left':'right');
$left=($langs->trans("DIRECTION")=='rtl'?'right':'left');
$fontsize=empty($conf->browser->phone)?'12':'12';
$fontsizesmaller=empty($conf->browser->phone)?'11':'11';

$fontlist='arial,tahoma,verdana,helvetica';
//$fontlist='Verdana,Helvetica,Arial,sans-serif';

?>


tr.header { background-color: #CCCCCC; }
tr.first { background-color: #EEEEEE; } tr.second { background-color: #DCDCDC; }

td.visitors { color: #000000; text-align: right; }
td.visits { color: #000000; text-align: right; }
td.pages { color: #000000; text-align: right; }
td.hits { color: #000000; text-align: right; }
td.bandwidth { color: #000000; text-align: right; }
td.domain { color: #000000; }

td.visitors-bold { background-color: #FFB055; color: #000000; text-align: right; font-weight: bold; }
td.visits-bold { background-color: #F8E880; color: #000000; text-align: right; font-weight: bold; }
td.pages-bold { background-color: #4477DD; color: #000000; text-align: right; font-weight: bold; }
td.hits-bold { background-color: #66F0FF; color: #000000; text-align: right; font-weight: bold; }
td.bandwidth-bold { background-color: #2EA495; color: #000000; text-align: right; font-weight: bold; }
td.domain-bold { background-color: #AAAAAA; color: #000000; font-weight: bold; }



/* For JAWStats */

h1,h2,div,img,span { border: 0; margin: 0; padding: 0; }

div, img, span { border: 0; padding: 0; margin: 0; }

h1 { font-size: 22px; font-weight: normal; }
h1 span { color: #cccc9f !important; font-size: 16px; }
h1.modal { color: #33332d; font-size: 22px; font-weight: normal; }

h2 { color: #33332d; font-size: 20px; font-weight: normal; margin-bottom: 2px; }
h2 span { color: #345678 !important; font-size: 15px; }

img.externallink { padding: 0 0 10px 2px; }

span.submenu { color: #0e1875 !important; cursor: pointer; }
span.submenu:hover { text-decoration: underline; }
span.submenuselect { color: #0e1875 !important; font-weight: bold; cursor: pointer; text-decoration: underline; }

td.countryflag { border: 0; text-align: center; width: 16px; }
td.countryflag img { height: 11px; width: 16px; }

.center { text-align: center; }
.change { color: #9fb4cc !important; cursor: pointer; font-size: 11px; }
.change:hover { color: #fff !important; text-decoration: underline; }
.changemonth { cursor: pointer; height: 9px; margin: 0 1px -1px 1px; width: 9px; }
.changemonthOff { height: 9px; margin: 0 1px -1px 1px; width: 9px; }
.changedivider { color: #71716c !important; font-size: 11px; }
.fauxlink { color: #333 !important; cursor: pointer; }
.fauxlink:hover { color: #000; text-decoration: underline; }
.hidden { display: none; }
.negative { color: #b00; }
.positive { color: #060; }
.right { text-align: right; }
.tiny { font-size: 11px; }

#changesitecontainer { height: 350px; overflow: auto; _overflow: none; _overflow-y: auto; padding: 0 20px 0 0; }
#footer { clear: both; padding: 6px 12px 12px 0; text-align: right; }
#footer span { font-size: 11px; }
#main { clear: both; padding: 15px 0 20px 0; }
#main .container { background: transparent; margin: 0 auto; max-width: 950px; padding: 0; position: relative; }
#content { background: transparent; }
#header .container { background: #888888; margin: 0 auto; max-width: 950px; padding: 5px 0 0 0; position: relative; }
#summary { color: #cccc9f; padding: 3px 0; }
#summary span { color: #fff; font-weight: bold; }
#menu { padding: 4px 0 0 0; }
#loading { background: #fff url('images/loading.gif'); display: none; height: 16px; overflow: hidden; padding: 0 !important; position: absolute; top: 10px; right: 10px; width: 14px; }
#submenu { padding: 0 0 20px 0; }
#toolmenu {color: #fc0; font-size: 11px !important; line-height: 20px; }
#toolmenu .container { margin: 0 auto; max-width: 950px; text-align: right; }
#toolmenu .container span { border-left: 1px solid #33332d; color: #333333; cursor: pointer; margin: 0 0 0 1px; padding: 0 11px; }
#toolmenu .container span:hover { color: #000000; }
#toolmenu .container span:last-child { border-right: 1px solid #33332d; }
#toolmenu .container span img { padding: 0 1px; }
#toolmenu .container span span { border: 0; padding: 0 6px; }
#tools h1 { color: #33332d; margin-bottom: 4px; }
#tools .tool { display: none; padding:  0; margin: 0 auto; }
#tools .tool div { color: #515356; padding: 10px 0 18px 0; }
#tools .tool div h1 span { color: #33332d !important; cursor: pointer; font-size: 11px; padding: 0 0 0 10px; }
#version { background: #ff0; border: 1px solid #9e9e7d; display: none; padding: 3px 6px; }

#menu ul { margin:0; padding:0; list-style:none; }
#menu li { background: #fff url("images/tab_right.gif") no-repeat right top; border-bottom: 1px solid #fff; cursor: pointer; font-weight: bold; float:left; margin: 1px 1px 0 1px; }
#menu span { background: url("images/tab_left.gif") no-repeat left top; display: block; padding: 5px 5px 4px 5px; }
#menu li.off { background: #ffffff url("images/taboff_right.gif") no-repeat right top; border-bottom: 1px solid #33332d; cursor: pointer; float:left; font-weight: normal; margin: 1px 0 0 0; }
#menu li.off span { background: url("images/taboff_left.gif") no-repeat left top; color: #012345; display: block; padding: 5px 5px 4px 5px;  }

#main .graph { height: 150px; margin: 0 0 7px 0; padding: 4px; width: 98%; }
#main .pie { float: left; height: 380px; margin-right: 1%; padding: 4px; vertical-align: top; width: 21%; }
#main .tableFull { border: 1px solid #9e9e7d; clear: both; padding: 4px; width: 98%;}
#main .tablePie { border: 1px solid #9e9e7d; float: right; padding: 4px; width: 75%;}

/* tools content */
#datepicker { width: 100%; }
#datepicker tr td { border-top: 1px solid #aab9d3; color: #444444; }
#datepicker tr td:first-child { font-size: 18px; line-height: 30px; text-align: center; width: 10%; }
#datepicker tr:first-child td { border-top: 0; }
#datepicker td.date { cursor: pointer; text-align: center; width: 7.5%; }
#datepicker td.date:hover { background: #dee5ed; text-decoration: underline; }
#datepicker td.selected { background: #dee5ed; cursor: pointer; text-align: center; width: 7.5%; }
#datepicker td.fade { color: #a0acba; text-align: center; width: 7.5%; }

#langpicker, #sitepicker { width: 100%; }
#langpicker ul, #sitepicker ul { margin: 0 12px; padding: 0; }
#langpicker ul li, #sitepicker ul li { border-top: 1px solid #aab9d3; cursor: pointer; line-height: 28px; list-style: none; margin: 0; padding: 0 0 0 10px; }
#langpicker ul li:first-child, #sitepicker ul li:first-child { border-top: 0; }
#langpicker ul li.selected, #sitepicker ul li.selected { background: #dee5ed; }
#langpicker ul li:hover, #sitepicker ul li:hover { background: #dee5ed; text-decoration: underline; }
#langpicker tr td, #sitepicker tr td { border-left: 1px solid #aab9d3; vertical-align: top; width: 33%; }
#langpicker tr td:first-child, #sitepicker tr td:first-child { border-left: 0; }

#siteupdate { margin: 12px 0 0 0 !important; padding: 0 !important; text-align: center; }
#siteupdate input[type=password] { border: 1px solid #444444; font-size: 19px; line-height: 34px; width: 300px; }
#siteupdate input[type=button] { border: 1px solid #444444; font-size: 19px; line-height: 34px; }

#toolLanguageButton img { padding: 0 0 0 8px !important; }

/* calendar */
table.calendar { width: 100%; }
table.calendar td { text-align: left; vertical-align: top;}
table.calendar td.labelSide { font-size: 11px; padding-right: 4px; text-align: right; vertical-align: middle; width: 1% !important; }
table.calendar td.labelTop { font-size: 11px; height: 13px; text-align: center; }
table.calendar td.labelTopSpacer { font-size: 11px; height: 13px; text-align: center; width: 2%; }
td.calDay { background: #f5f5f2; height: 52px; width: 11%; }
td.calDay div.date { color: #ccc; font-size: 15px; font-weight: bold; }
td.calDayPopulated { background: #e2e2d8; height: 52px; padding: 0 2px; width: 11%;}
td.calDayPopulated div.date { color: #aaa; font-size: 15px; font-weight: bold; height: 15px; }
td.calDayPopulated div.value { color: #333; font-size: 15px; font-weight: bold; text-align: center; }
td.calTotWk { background: #cccc9f; vertical-align: middle !important; width: 9%; }
td.calTotWk div { color: #333; font-size: 15px; font-weight: bold; text-align: center; }
td.calAvgWk { background: #b7bad6; vertical-align: middle !important; width: 9%; }
td.calAvgWk div { color: #333; font-size: 15px; font-weight: bold; text-align: center; }
td.calTotDay { background: #cccc9f; height: 40px; vertical-align: middle !important; }
td.calTotDay div { color: #333; font-size: 15px; font-weight: bold; text-align: center; }
td.calTotDay div span { font-size: 11px; font-weight: normal; }
td.calAvgDay { background: #b7bad6; height: 40px; vertical-align: middle !important; }
td.calAvgDay div { color: #333; font-size: 15px; font-weight: bold; text-align: center; }
td.calAvgDay div span { font-size: 11px; font-weight: normal; }
td.calGraph { height: 130px; vertical-align: bottom !important; }

/* logos */
td.browserlogo { text-align: center; }
td.browserlogo img { height: 15px; width: 15px; }
td.oslogo { text-align: center; }
td.oslogo img { height: 15px; width: 15px; }
td.searchenginelogo { text-align: center; }
td.searchenginelogo img { height: 16px; width: 16px; }

/* paging */
#paging { padding: 7px 20px 3px 0; text-align: right; }
#paging img { padding: 0 3px; }
#paging input { border: 1px solid #333; padding: 1px 4px; text-align: right; width: 40px; }
#paging span { padding: 0 7px; }
#paging span:first-child { padding: 0 20px 0 0; }

/* tablesorter */
table.tablesorter { font-family: arial, helvetica, sans-serif; background-color: #789abc; margin: 0; font-size: 12px; text-align: left; width: 98% }
table.tablesorter thead tr th { background-color: #cccc9f; border-right: 1px solid #eee; font-size: 12px; font-weight: normal; padding: 7px 30px 7px 7px; }
table.tablesorter thead tr .header { background-image: url('images/tablesorter_bg.gif'); background-repeat: no-repeat; background-position: center right; cursor: pointer; }
table.tablesorter tbody td { background: #f5f5f2 !important; color: #333; padding: 5px; background-color: #fff; border-right: 1px solid #eee; vertical-align: top; }
table.tablesorter tbody tr.odd td { background: #e2e2d8 !important; }
table.tablesorter tbody tr.saturday td { background: #d3d389 !important; }
table.tablesorter tbody tr.sunday td { background: #d3d389 !important; }
table.tablesorter tbody tr.highlight td { background: #fc3 !important; }
table.tablesorter thead tr .headerSortUp { background-image: url('images/tablesorter_asc.gif'); }
table.tablesorter thead tr .headerSortDown { background-image: url('images/tablesorter_desc.gif'); }
table.tablesorter thead tr .headerSortDown,
table.tablesorter thead tr .headerSortUp { background-color: #9e9e7d; color: #fff; }
table.tablesorter tfoot tr td { background-color: #9fb4cc; border-right: 1px solid #eee; color: #333; font-size: 13px; font-weight: bold; padding: 7px 5px; }
table.tablesorter .noborder { border: 0; }

/* tag cloud */
.tagcloud { margin-bottom: 20px; text-align: center; }
.tagcloud span { color: #333; padding: 0 16px; white-space: nowrap; }


<?php
$db->close();
