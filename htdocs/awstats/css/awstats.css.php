<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * $Id: awstats.css.php,v 1.7 2011/03/29 23:17:18 eldy Exp $
 */

//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');  // Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');    // Not disabled to increase speed. Language code is found on url.
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC', '1');
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');  // Not disabled cause need to do translations
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK', 1);
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL', 1);
if (! defined('NOLOGIN'))         define('NOLOGIN', 1);
if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU', 1);
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML', 1);
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX', '1');

session_cache_limiter('public');

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include "../../../dolibarr/htdocs/main.inc.php";     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include "../../../../dolibarr/htdocs/main.inc.php";   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include "../../../../../dolibarr/htdocs/main.inc.php";   // Used on dev env only
if (! $res) die("Include of main fails");

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at
// each Dolibarr page access.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=10800, public, must-revalidate');
else header('Cache-Control: no-cache');

if (! empty($_GET["lang"])) $langs->setDefaultLang($_GET["lang"]);  // If language was forced on URL by the main.inc.php
$langs->load("main", 0, 1);
$right=($langs->trans("DIRECTION")=='rtl'?'left':'right');
$left=($langs->trans("DIRECTION")=='rtl'?'right':'left');
$fontsize='12';
$fontsizesmaller='11';

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


<?php
$db->close();
