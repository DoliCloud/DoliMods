<?php

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/agenda.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/project.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";
require_once DOL_DOCUMENT_ROOT."/user/class/user.class.php";
require_once DOL_DOCUMENT_ROOT."/comm/action/class/cactioncomm.class.php";
require_once DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formactions.class.php";
require_once DOL_DOCUMENT_ROOT."/projet/class/project.class.php";

$event = new ActionComm($db);
$event->fetch(53);
echo '<pre>BEFORE';
print_r($event);
echo '</pre>';

$sql = "UPDATE ".MAIN_DB_PREFIX."actioncomm ";
$sql.= " SET percent='".$event->percentage."'";
$sql.= ", fk_soc =". ($event->societe->id > 0 ? "'".$event->societe->id."'":"null");
$sql.= ", fk_project =". ($event->fk_project > 0 ? "'".$event->fk_project."'":"null");
$sql.= ", fk_contact =". ($event->contact->id > 0 ? "'".$event->contact->id."'":"null");
$sql.= ", priority = '".$event->priority."'";
$sql.= ", fulldayevent = '".$event->fulldayevent."'";
$sql.= ", fk_user_mod = '".$user->id."'";
$sql.= ", fk_user_action=2";
$sql.= " WHERE id=".$event->id;

if ($db->query($sql)) {
	echo '<pre>AFTER';
	print_r($event);
	echo '</pre>';
	$db->commit();
	echo '<pre>AFTER 2';
	print_r($event);
	echo '</pre>';
}
