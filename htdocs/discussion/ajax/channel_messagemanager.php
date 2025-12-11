<?php
/* This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       /htdocs/custom/discussion/ajax/channel_messagemanager.php
 *	\brief      File to make Ajax action on webhook
 */

if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1'); // Disables token renewal
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
// Do not check anti CSRF attack test
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined('NOIPCHECK')) {
	define('NOIPCHECK', '1'); // Do not check IP defined into conf $dolibarr_main_restrict_ip
}
if (!defined('NOBROWSERNOTIF')) {
	define('NOBROWSERNOTIF', '1');
}

// Try main.inc.php using relative path
$res = 0;
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
}

// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

dol_include_once('/discussion/class/channel.class.php');
dol_include_once('/discussion/class/message.class.php');
include_once DOL_DOCUMENT_ROOT.'/core/lib/memory.lib.php';

$action = GETPOST('action', 'aZ09');
$limit = getDolGlobalInt("DISCUSSION_LIMIT_MESSAGE_FETCH", $conf->liste_limit);
$datetimeolder = GETPOSTINT('datetimeolder');
$lastuser = 0;
$channelid = GETPOSTINT('channelid');
$userid = $user->id;

$response = "";
$error = 0;
$chanmessage = new Message($db);
$channel = new Channel($db);


/*
 * Actions
 */

if ($action == 'addmessage') {
	$db->begin();
	$message = GETPOST('channelmessage');
	if (empty($channelid) || empty($userid) || empty($message)) {
		httponly_accessforbidden('Param channelid and message are required', 403);
	}
	$res = $channel->fetch($channelid);
	if ($res <= 0) {
		httponly_accessforbidden('Channel '.((int) $channelid).' doesn\'t exists', 403);
	}
	
	if (!$channel->isChannelContact($userid)) {
		httponly_accessforbidden('User doesn\'t have the permission to access this channel', 403);
	}
	if (!$error) {
		$discussioncachename = getDolGlobalString("DISCUSSION_CACHE_NAME_PREFIX",'DISCUSSION_CACHE_CHANNEL')."_".((int) $channelid) ;
		$now = dol_now();
		$chanmessage->message = $db->escape($message);
		$chanmessage->fk_discussion_channel = ((int) $channelid);
		$result = $chanmessage->create($user);
		if ($result <= 0) {
			$error++;
			dol_print_error(null, $chanmessage->error, $chanmessage->errors);
		}
		if (!$error) {
			$data = dol_getcache($discussioncachename, 1);
			if (!empty($data) && $data < 0) {
				$error++;
				$response = "Failed to get cache";
			}
			if (!$error) {
				// manage json
				$newmsg = new stdClass();
				$newmsg->id = (int) $result;
				$newmsg->userid = (int) $userid;
				$newmsg->firstname = $db->escape($user->firstname);
				$newmsg->datec = (int) $now;
				$newmsg->datec_text = dol_print_date($now, "dayhourtext");
				$newmsg->message = $db->escape($message);

				if (empty($data)) {
					$msgarray = array();
					$cacheobj = new stdClass();
					$cacheobj->messages = array();
					$cacheobj->users = array();
				} else {
					$cacheobj = json_decode($data);
					if (empty($cacheobj->messages)) {
						$msgarray = array();
						$cacheobj->messages = array();
					} else{
						$tmparray = $msgarray = $cacheobj->messages;
						foreach ($tmparray as $key => $msg) {
							$ttl = $msg->ttl;
							$datemsg = $msg->date;
							if ($now > dol_time_plus_duree($datemsg, $ttl, "s")) {
								array_shift($msgarray);
							} else {
								break;
							}
						}
						$lenmsgarray = count($msgarray);
						$i = getDolGlobalInt("DISCUSSION_CACHE_SIZE", 50);
						while ($lenmsgarray >= getDolGlobalInt("DISCUSSION_CACHE_SIZE", 50)) {
							if ($i <= 0) {
								// Should not append
								break;
							}
							array_shift($msgarray);
							$lenmsgarray = count($msgarray);
							$i--;
						}
					}
				}
				$msgarray[] = array("ttl" => getDolGlobalInt("DISCUSSION_CACHE_TTL", 2000), "date" => $now, "data" => $newmsg);
				$cacheobj->messages = $msgarray;
				$json = json_encode($cacheobj);
				$res = dol_setcache($discussioncachename, $json, getDolGlobalInt("DISCUSSION_CACHE_TTL", 2000), 1, 1);
				if ($res <= 0) {
					$error++;
					$response = "Failed to set cache";
				}
			}
		}
	}
	if (!$error) {
		$db->commit();
		$response = "Message saved";
	} else {
		$db->rollback();
	}
} elseif ($action == 'getoldermessages') {
	$message = GETPOST('channelmessage');
	if (empty($channelid) || empty($userid) || empty($datetimeolder)) {
		httponly_accessforbidden('Param channelid, datetimeolder and message are required', 403);
	}
	$res = $channel->fetch($channelid);
	if ($res <= 0) {
		httponly_accessforbidden('Channel '.$channelid.' doesn\'t exists', 403);
	}
	if (!$channel->isChannelContact($userid)) {
		httponly_accessforbidden('User doesn\'t have the permission to access this channel', 403);
	}
	$sql = "SELECT t.rowid as id, t.date_creation as datec, t.fk_user_creat as userid, t.message, u.firstname";
	$sql .= " FROM ".MAIN_DB_PREFIX."discussion_message as t";
	$sql .= " JOIN ".MAIN_DB_PREFIX."user as u";
	$sql .= " ON u.rowid = t.fk_user_creat";
	$sql .= " WHERE fk_discussion_channel = ".((int) $channel->id)."";
	$sql .= " AND date_creation < '".$db->idate($datetimeolder)."'";
	$sql .= $db->order('date_creation', 'DESC');
	$sql .= $db->plimit($limit + 1);
	$resql = $db->query($sql);
	if ($resql) {
		$nomessageleft = 0;
		$responsearray = array();
		$num = $db->num_rows($resql);
		$imaxinloop = ($limit ? min($num, $limit) : $num);
		$i = 0;
		while ($i < $imaxinloop) {
			$obj = $db->fetch_object($resql);
			if (empty($obj)) {
				break; // Should not happen
			}
			$obj->datec = $db->jdate($obj->datec);
			if ($i > 0) {
				if ($datetimeolder > dol_time_plus_duree($obj->datec, 1, 'h')) {
					$responsearray[$i-1]->displaydate = true;
				}
			}
			$obj->datec_text = dol_print_date($obj->datec, "dayhourtext");
			$responsearray[$i] = $obj;
			$datetimeolder = $obj->datec;
			$i++;
		}
		$responsearray[$i-1]->displaydate = true;
		if ($num <= $limit) {
			$nomessageleft = 1;
		}
		$responsearray = array_reverse($responsearray);
		$response = array("nomessageleft" => $nomessageleft, "datetimeolder" => $datetimeolder, "datamsg" => $responsearray);
	} else {
		dol_print_error($db);
		$error++;
	}
}

/*
 * View
 */
top_httphead('application/json');

echo json_encode($response);