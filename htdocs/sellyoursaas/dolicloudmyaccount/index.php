<?php

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

print 'backoffice';


llxFooter();
$db->close();
