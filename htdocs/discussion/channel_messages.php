<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024		Alice Adminson				<myemail@mycompany.com>
 *
 * This program is free software; you can redistribute it and/or modify
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
 *  \file       channel_messages.php
 *  \ingroup    discussion
 *  \brief      Tab channel messages
 */


// General defined Options
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');					// Force use of CSRF protection with tokens even for GET
//if (! defined('MAIN_AUTHENTICATION_MODE')) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined('MAIN_LANG_DEFAULT'))        define('MAIN_LANG_DEFAULT', 'auto');					// Force LANG (language) to a particular value
//if (! defined('MAIN_SECURITY_FORCECSP'))   define('MAIN_SECURITY_FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');					// Disable browser notification
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');						// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined('NOLOGIN'))                  define('NOLOGIN', '1');						// Do not use login - if this page is public (can be called outside logged session). This includes the NOIPCHECK too.
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  		// Do not load ajax.lib.php library
//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');					// Do not create database handler $db
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');					// Do not load html.form.class.php
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');					// Do not load and show top and left menu
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');					// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');					// Do not load object $langs
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');					// Do not load object $user
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');			// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');			// Do not check injection attack on POST parameters
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');					// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');					// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
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
dol_include_once('/discussion/lib/discussion_channel.lib.php');
include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

// Load translation files required by the page
$langs->loadLangs(array("discussion@discussion", "companies"));

// Get parameters
$id = GETPOSTINT('id');
$ref        = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$cancel     = GETPOST('cancel', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$limit = GETPOSTINT('limit') ? GETPOSTINT('limit') : $conf->liste_limit;

// Initialize a technical objects
$object = new Channel($db);
$messagestatic = new Message($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->discussion->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array($object->element.'messages', 'discussionmessages')); // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be 'include', not 'include_once'. Include fetch and fetch_thirdparty but not fetch_optionals
if ($id > 0 || !empty($ref)) {
	$upload_dir = $conf->discussion->multidir_output[empty($object->entity) ? $conf->entity : $object->entity]."/".$object->id;
}

$res = $object->channelInitUserArray();
if ($res <= 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
	$permissiontoread = $user->hasRight('discussion', 'channel', 'read');
	$permissiontoadd = $user->hasRight('discussion', 'channel', 'write');
	$permissionnote = $user->hasRight('discussion', 'channel', 'write'); // Used by the include of actions_setnotes.inc.php
} else {
	$permissiontoread = 1;
	$permissiontoadd = 1;
	$permissionnote = 1;
}

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->module, $object->id, $object->table_element, $object->element, 'fk_soc', 'rowid', $isdraft);
if (!isModEnabled("discussion")) {
	accessforbidden();
}
if (!$permissiontoread) {
	accessforbidden();
}
if (!$object->isChannelContact($user->id)) {
  accessforbidden();
}


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}
if (empty($reshook)) {
	include DOL_DOCUMENT_ROOT.'/core/actions_setnotes.inc.php'; // Must be 'include', not 'include_once'
}

if ($action == 'addmessage') {
	$message = GETPOST('channelmessage');
	if ($message == "") {
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Message')), null, 'errors');
		$error++;
	}
	if (!$error) {
		$chanmessage = new Message($db);
		$chanmessage->message = $db->escape($message);
		$chanmessage->fk_discussion_channel = ((int) $id);
		$result = $chanmessage->create($user);
	}
	if (!$error && $result <= 0) {
		setEventMessages($chanmessage->error, $chanmessage->errors, 'errors');
		$action = '';
	} else {
		$action = 'view';
	}
}

/*
 * View
 */


$form = new Form($db);

$title = $langs->trans('Channel').' - '.$langs->trans("Messages");
//$title = $object->ref." - ".$langs->trans("Notes");
$help_url = '';
//$help_url='EN:Customers_Orders|FR:Commandes_Clients|ES:Pedidos de clientes';

llxHeader('', $title, $help_url, '', 0, 0, '', '', '', 'mod-discussion page-card_messages');

if ($id > 0 || !empty($ref)) {
	$object->fetch_thirdparty();

	$head = channelPrepareHead($object);

	print dol_get_fiche_head($head, 'messages', $langs->trans("Channel"), -1, $object->picto);

	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="'.dol_buildpath('/discussion/channel_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

	$onlineusers = $object->channelGetOnlineUsers($user);
	$nbonlineusers = count($onlineusers);
	$tooltip = "";
	foreach ($onlineusers as $key => $usr) {
		$tooltip .= '<div id="onlineuser_'.$key.'">'.$usr->name.'</div>';
	}
	$param["tooltip"] = $tooltip;
	$morehtmlref = '<span id="channelonline" class="'.($nbonlineusers > 0 ? '': 'hidden').'">'.dolGetStatus('', '', '', 'status4', 3, '', $param)."</span>";
	$morehtmlref .= '<span id="channeloffline" class="' .($nbonlineusers == 0 ? '': 'hidden').'">'.dolGetStatus('', '', '', 'status0', 3)."</span>";
	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

	$arraymessage = $messagestatic->fetchAll('DESC', 'date_creation', $limit, 0, '((fk_discussion_channel:=:'.((int) $id).'))');
	$arraymessage = array_reverse($arraymessage, true);

// Loop on record
// --------------------------------------------------------------------
	print '<div class="fichecenter">';
	print '<div class="underbanner clearboth"></div>';
	print '<div id="channelmessagediv" style="max-height: 700px; overflow: auto;min-height:500px">';
	print '<ul class="timeline" id="timelineul">';

	$selfuser = 0;
	$lastdate = 0;
	$lastuser = 0;
	foreach ($arraymessage as $key => $obj) {
		$datec = $obj->date_creation;
		if (array_keys($arraymessage)[0] == $key) {
			$firstdate = $datec;
		}
		$displaydate = $datec > dol_time_plus_duree($lastdate, 1, 'h');
		if ($displaydate) {
			print '<li id="msgtime_'.$obj->id.'" class="time-label center" data-date = "'.$datec.'">';
			print '<span class="timeline-badge-date">'.dol_print_date($datec, "dayhourtext").'</span>';
			print '</li>';
			$lastdate = $datec;
		}
		if ($user->id != $obj->fk_user_creat) {
			$tmpuser = new User($db);
			$tmpuser->fetch($obj->fk_user_creat);
			$selfuser = 0;
		} else {
			$tmpuser = $user;
			$selfuser = 1;
		}

		
		print '<li class="timeline-message" id="msg_'.$obj->id.'">';
		print '<div class="timeline-item">';
		if ($displaydate || $lastuser != $tmpuser->id){
			print '<h3 class="timeline-header '.($selfuser == 1 ? 'right' : '').'">';
			print '<div class="inline-block valignmiddle marginrightonly" id="msgauthor_' . $obj->id . '">';
			print $tmpuser->getNomUrl(-1, 'nolink', 0, 0, 30);
			print '</div>';
			print '</h3>';
			$lastuser = $tmpuser->id;
		}
		print '<div class="timeline-body wordbreak small">';
		print '<div class="'.($selfuser == 1 ? 'right' : '').'">';
		print $obj->message;
		print '</div>';
		print '</div>';
		print '</div>';
		print '</li>';
	}
	if (empty($arraymessage)) {
		print '<li>';
		print '<div class="center opacitymedium noborderbottom">';
		print $langs->trans("StartConversation");
		print '</div>';
		print '</li>';
	}

	print '<li id="lastrowmessage" data-rowid="-1">';
	print '<div id="lastrow" class="">';
	print '</div>';
	print '</li>';
	
	print "</table>";
	print '</div>';

	print '<div class="fichecenter ">';
	print '<form action="'.$_SERVER["PHP_SELF"].'?id='.((int) $id).'" method="POST">';
	print '<input type="hidden" name="action" value="addmessage">';

	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	$doleditor = new DolEditor('channelmessage', '', '', '100', 'dolibarr_notes', 'In', false, false, false, ROWS_5, '80%');
	$doleditor->Create(0, '', true, '', '', '', $morecss);
	print '<input type="button" id="sendmessage" class="button valignmiddle" value="'.$langs->trans("SendMessage").'">';
	print '</div>';

	// Chat message send
	print '<script>
	var arrayoldmessage = [];
	var lastuser = "'.((int) $lastuser).'";
	var firstmessage = "'.((int) $firstdate).'";
	var selfuserid = '.((int) $user->id).';
	var userarray = [];

	function forgemessage(data, arr, userarray){
		userid = data.userid;
		displaydate = data.displaydate;
		msgid = data.id;
		datec = data.datec;
		username = data.firstname;
		let user;
		userarray.forEach(function (element) {
			if(element.id == userid){
				user = element;
			}
		})
		if (displaydate) {
			lidate = $("<li>").addClass("time-label center");
			lidate.addClass("center");
			lidate.attr("id","msgtime_" + msgid);
			lidate.data("date",datec);
	
			lidate.append($("<span>").addClass("timeline-badge-date").text(data.datec_text))
		}

		limessage = $("<li>").addClass("timeline-message").attr("id","msg_" + msgid);
		divtimelineitem = $("<div>").addClass("timeline-item")
		if(displaydate || lastuser != userid){
			tlheader = $("<h3>").addClass("timeline-header")
			divauthor = $("<div>").addClass("inline-block valignmiddle marginrightonly");
			divauthor.attr("id","msgauthor_" + msgid);
			divauthor.attr("authid", userid);
			if(selfuserid == userid){
				tlheader.addClass("right");
			}
			spanimg = $("<span>").addClass("nopadding userimg valignmiddle");
			imgsrc = "'.DOL_URL_ROOT.'";
			if (user.imgmode == "gravatar"){
				imgsrc = "https://www.gravatar.com/avatar/";
			} else if (user.imgmode == "viewimage") {
				imgsrc = imgsrc + "/viewimage.php";
			}
			img = $("<img>").attr("src", imgsrc + user.img)
			img.addClass("photouserphoto userphoto");
			spanimg.append(img);
			spanimg.attr("style", "margin-right: 3px")
			spanauthor = $("<span>").addClass("nopadding usertext valignmiddle");
			spanauthor.text(user.name);
			divauthor.append(spanimg);
			divauthor.append(spanauthor);
			tlheader.append(divauthor)
			divtimelineitem.append(tlheader)
		}

		divbody = $("<div>").addClass("timeline-body wordbreak small")
		if(selfuserid == userid){
			divbody.addClass("right");
		}
		divmsg = $("<div>").text(data.message)
		divbody.append(divmsg)
		divtimelineitem.append(divbody)
		limessage.append(divtimelineitem)

		elementoinsertBefore = $("#timelineul :first-child").first();
		if (displaydate) {
			arr.push(lidate)
		}
		arr.push(limessage)
		lastuser = userid;
	}

	$(document).ready(function(){
		$("#sendmessage").on("click", function(){
			msg = $("#channelmessage").val();
			data = { action : "addmessage", channelmessage : msg, channelid : '.((int) $id).' , token : "'.currentToken().'"};

			$.ajax({
				type : "POST",
				url : "'.dol_buildpath('/discussion/ajax/channel_messagemanager.php', 1).'",
				data : data,
				success : function(r){
					$("#channelmessage").val("");
					console.log(r);
				}
			});
		});
	})
	</script>';

	//Scroll the div to the top of message list and display older messages
	print '<script>
	$(document).ready(function(){
		var nomessagesleft = 0;
		d = $("#channelmessagediv");
		d.scrollTop(d.prop("scrollHeight"));
		d.on("scroll", function(){
			scrollTop = $(this).scrollTop();
			if (scrollTop == 0 && nomessagesleft == 0){
				console.log("We have scroll to the top of div so we fetch older messages");
				//Call ajax to get older messages
				data = { action : "getoldermessages", "datetimeolder" : firstmessage, channelid : '.((int) $id).' , token : "'.currentToken().'"};
				$.ajax({
					type : "GET",
					url : "'.dol_buildpath('/discussion/ajax/channel_messagemanager.php', 1).'",
					data : data,
					success : function(data){
						if (data.nomessageleft != undefined && data.datamsg != undefined){
							arraymsg = data.datamsg;
							console.log("We forge every message to display");
							arraymsg.forEach(function (element){
								forgemessage(element, arrayoldmessage, userarray);
							});
							nomessagesleft = data.nomessageleft;
							firstmessage = data.datetimeolder;
							var elementoinsertBefore = $("#timelineul :first-child").first();
							elementid = elementoinsertBefore.attr("id");
							elementid = elementid.split("_")[1];
							arrayoldmessage.forEach(function (element){
								element.insertBefore(elementoinsertBefore);
							});
							arrayoldmessage = [];
							console.log("We scroll to last seen msg "+elementid);
							$("#channelmessagediv").scrollTop($("#msgtime_"+elementid).offset().top - $("#channelmessagediv").offset().top);
						} else {
						 	console.log("Error: Bad response when trying to get older messages");
						}
					}
				});
			}
		});
	})
	</script>';

	print '</div>';

	// Server send event script
	print '<script>
	$(document).ready(function(){
		var arraynewmessage = [];
		const sse = new EventSource("'.dol_buildpath('/discussion/ajax/channelevent_source.php', 1).'?channel='.$id.'&lastuser='.$lastuser.'&lastdate='.$lastdate.'");
		sse.onmessage = function (event) {
			data = JSON.parse(event.data);

			if (JSON.stringify(data.users) != JSON.stringify(userarray)){
				userarray = data.users;
				console.log(userarray);
			}

			if (data.nbuserconnected > 1){
				$("#channeloffline").hide();
				$("#channelonline").show();
			} else {
			 	$("#channeloffline").show();
				$("#channelonline").hide();
			}

			if (data.newmsg == "HEARTBEAT"){
				console.log("SSE Still alive: "+data.newmsg);
			} else {
				newmsg = data.newmsg;
				newmsg.forEach(function (element){
					forgemessage(element, arraynewmessage, userarray);
				});
				arraynewmessage.forEach(function (element){
					$(element).insertBefore("#lastrowmessage");
				});
				$("#channelmessagediv").scrollTop($("#channelmessagediv").prop("scrollHeight"));
				console.log(event.data);
			}
		};
		sse.error = function (event) {
			console.log(event.data);
		};
	})
	</script>';

	print dol_get_fiche_end();
}

// End of page
llxFooter();
$db->close();
