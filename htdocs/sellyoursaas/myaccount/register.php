<?php

require('../common.inc.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';


//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				    // If this page is public (can be called outside logged session)


$res=0;
if (! $res && file_exists("./common.inc.php")) $res=@include("./common.inc.php");
if (! $res && file_exists("../common.inc.php")) $res=@include("../common.inc.php");
if (! $res && file_exists("../../common.inc.php")) $res=@include("../../common.inc.php");
if (! $res && file_exists("../../../common.inc.php")) $res=@include("../../../common.inc.php");
if (! $res && file_exists("../../../../common.inc.php")) $res=@include("../../../../common.inc.php");
if (! $res && file_exists("../../../../../common.inc.php")) $res=@include("../../../../../common.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/common.inc.php"); // Used on dev env only
if (! $res) die("Include of common fails");
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';


/*
 * View
 */

llxHeader();


print 'login';


llxFooter();
$db->close();

