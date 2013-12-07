<?php

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/agenda.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/project.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/cactioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/comm/action/class/actioncomm.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formactions.class.php");
require_once(DOL_DOCUMENT_ROOT."/projet/class/project.class.php");

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
$sql.= ", fk_user_done=".($event->userdone->id > 0 ? "'".$event->userdone->id."'":"null");
$sql.= " WHERE id=".$event->id;

if ($db->query($sql))
{
	echo '<pre>AFTER';
	print_r($event);
	echo '</pre>';
	$db->commit();
	echo '<pre>AFTER 2';
	print_r($event);
	echo '</pre>';
}

?>
