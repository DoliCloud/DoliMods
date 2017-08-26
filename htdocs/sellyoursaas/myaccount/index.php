<?php

$res=0;
if (! $res && file_exists("./common.inc.php")) $res=@include("./common.inc.php");
if (! $res && file_exists("../common.inc.php")) $res=@include("../common.inc.php");
if (! $res && file_exists("../../common.inc.php")) $res=@include("../../common.inc.php");
if (! $res && file_exists("../../../common.inc.php")) $res=@include("../../../common.inc.php");
if (! $res && file_exists("../../../../common.inc.php")) $res=@include("../../../../common.inc.php");
if (! $res && file_exists("../../../../../common.inc.php")) $res=@include("../../../../../common.inc.php");
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
