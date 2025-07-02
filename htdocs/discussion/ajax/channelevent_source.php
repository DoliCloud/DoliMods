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
 *	\file       /htdocs/webhook/ajax/webhook.php
 *	\brief      File to make Ajax action on webhook
 */


ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 'off'); // Disable zlib output compression, if enabled

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

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
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
dol_include_once('/discussion/lib/discussion_channel.lib.php');
include_once DOL_DOCUMENT_ROOT.'/core/lib/memory.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 */

$lastdate = GETPOSTINT("lastdate");
$lastuser = GETPOSTINT("lastuser");
$limit = GETPOSTINT('limit') ? GETPOSTINT('limit') : $conf->liste_limit;
$channelid = GETPOSTINT('channel');
$channel = new Channel($db);
$res = $channel->fetch($channelid);
if ($res <= 0) {
  httponly_accessforbidden('Channel '.$channelid.' doesn\'t exists', 403);
}
if (!$channel->isChannelContact($user->id)) {
  httponly_accessforbidden('User doesn\'t have the permission to access this channel', 403);
}
$messagestatic = new Message($db);

/*
 * Actions
 */

// None


/*
 * View
 */
top_httphead('text/event-stream', 1);

// Create a DestructTester
// It'll log to our file on PHP shutdown and __destruct().
dol_syslog("---STARTING SSE SERVER FOR USER ".$user->id."---");

ignore_user_abort(true);

// Remove any buffers so that PHP attempts to send data on flush();
ob_end_clean();
ob_implicit_flush();

$discussioncachename = getDolGlobalString("DISCUSSION_CACHE_NAME_PREFIX",'DISCUSSION_CACHE_CHANNEL')."_".((int) $channelid) ;
$lastdateverification = dol_now();
$userarray = array();
session_write_close();
while (true) {
  if (connection_aborted()) {
    break;
  }
  $channel->channelConnectedUser($user, 1);
  $nbuserconnected = 0;
  $datastring = "data: ";
  $connAbortedStr = connection_aborted() ? "YES" : "NO";
  $now = dol_now();

  $data = dol_getcache($discussioncachename, 1);
  if (!empty($data) && $data < 0) {
    $error++;
  }
  $msg = "";
  $userarray = array();
  if ($error) {
    $msg .= "'Failed to get cache'";
  } else {
    if (empty($data)) {
      $msg .= '"HEARTBEAT"';
    } else {
      $cacheobj = json_decode($data);
      $msgarray = $cacheobj->messages;
      $selfuser = 0;
      $i = 0;
      $newmsgarray = array();
      foreach ($msgarray as $key => $value) {
        if ($value->date > $lastdateverification) {
          $tmpdata = $value->data;
          $datec = $tmpdata->datec;
          $displaydate = $datec > dol_time_plus_duree($lastdate, 1, 'h');
          $tmpdata->displaydate = $displaydate;
          $lastdate = $datec;
          $newmsgarray[] = $tmpdata;
        }
      }

      if (!empty($newmsgarray)) {
        $lastdateverification = $now;
        $msg .= json_encode($newmsgarray);
      } else {
        $msg .= '"HEARTBEAT"';
      }
      if (!empty($cacheobj->users)) {
        $userarray = json_encode($cacheobj->users);
        foreach ($cacheobj->users as $key => $usertmp) {
          if ($usertmp->status == "connected") {
            $nbuserconnected++;
          }
        }
      }
    }
  }
  $datastring .= '{"newmsg": '.$msg.', "nbuserconnected" : '.$nbuserconnected.',"users":'.$userarray.'}';
  echo $datastring."\n\n";
  flush();
  sleep(1);
}
$channel->channelConnectedUser($user, 1, "disconnected");
dol_syslog("---ENDING SSE SERVER FOR ".$user->id."---");
exit;
