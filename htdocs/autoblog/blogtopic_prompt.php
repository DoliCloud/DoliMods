<?php
/* Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
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

declare(strict_types=1);

/**
 *    \file       htdocs/autoblog/blogtopic_prompt.php
 *    \ingroup    autoblog
 *    \brief      Page (shown into a dialog popup) with the request that will be sent to the AI service to generate the article of a topic
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;  // @phan-suppress-current-line DolibarrForbiddenFunctionPlugin
$j = strlen((string) $tmp2) - 1;  // @phan-suppress-current-line DolibarrForbiddenFunctionPlugin
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
/**
 * The main.inc.php has been included so the following variable are now defined:
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */
dol_include_once('/autoblog/class/blogtopic.class.php');

// Load translation files required by the page
$langs->loadLangs(array("autoblog@autoblog"));

// Get parameters
$id = GETPOSTINT('id');

// Security check
if (!isModEnabled('autoblog')) {
	accessforbidden('Module autoblog not enabled');
}
if ($user->socid > 0) {
	accessforbidden();
}
$object = new BlogTopic($db);
if ($id <= 0 || $object->fetch($id) <= 0) {
	recordNotFound();
}
restrictedArea($user, 'autoblog', $object, $object->table_element.'&'.$object->element, 'blogtopic', '', 'rowid');


/*
 * Actions
 */

// None


/*
 * View
 */

$request = $object->getAIRequestPreview();

llxHeader('', $langs->trans("AutoBlogAIRequest"), '', '', 0, 0, '', '', '', 'mod-autoblog page-prompt');

if ($request['error']) {
	print '<div class="error">'.dol_escape_htmltag($request['error']).'</div>';
} else {
	print '<div class="opacitymedium marginbottomonly">';
	print $langs->trans("AutoBlogAIRequestDesc", dol_escape_htmltag($request['service']), dol_escape_htmltag($request['model'] ? $request['model'] : '?'));
	print '</div>';
	if (!$request['fullpayload']) {
		print info_admin($langs->trans("AutoBlogAIRequestOnlyPrompt"), 0, 0, 'warning');
	}
	$payloadtoshow = $request['payload'];
	if ($request['fullpayload']) {
		// Show the line feeds of the prompts (\n into the JSON strings) as new lines, so the request is readable
		$payloadtoshow = (string) preg_replace('/(?<!\\\\)(\\\\r)?\\\\n/', "\n", $payloadtoshow);
	}
	// keepn=1 to keep the new lines (else they are replaced with the text \n)
	print '<pre class="autoblog-airequest" style="white-space: pre-wrap; word-break: break-word;">'.dol_escape_htmltag($payloadtoshow, 0, 1).'</pre>';
}

// End of page
llxFooter();
$db->close();
