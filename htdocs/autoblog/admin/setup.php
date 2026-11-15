<?php
/* Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

/**
 * \file    autoblog/admin/setup.php
 * \ingroup autoblog
 * \brief   AutoBlog setup page.
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
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formsetup.class.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
require_once DOL_DOCUMENT_ROOT.'/ai/class/ai.class.php';
dol_include_once('/autoblog/lib/autoblog.lib.php');

// Translations
$langs->loadLangs(array("admin", "website", "autoblog@autoblog"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('autoblogsetup', 'globalsetup'));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$formSetup = new FormSetup($db);

// List of websites for the default website of new topics
$websiteoptions = array();
$websitestatic = new Website($db);
$websites = $websitestatic->fetchAll('ASC', 'position');
if (is_array($websites)) {
	foreach ($websites as $website) {
		$websiteoptions[$website->id] = $website->ref;
	}
}

$item = $formSetup->newItem('AUTOBLOG_DEFAULT_WEBSITE')->setAsSelect($websiteoptions);
$item->helpText = $langs->transnoentities('AUTOBLOG_DEFAULT_WEBSITEHelp');

$item = $formSetup->newItem('AUTOBLOG_AUTHOR_ALIAS');
$item->helpText = $langs->transnoentities('AUTOBLOG_AUTHOR_ALIASHelp');

$item = $formSetup->newItem('AUTOBLOG_TARGET_WORDS')->setAsNumber(100, 5000, 50);
$item->defaultFieldValue = '800';
$item->cssClass = 'maxwidth100';

$item = $formSetup->newItem('AUTOBLOG_MAX_ATTEMPTS')->setAsNumber(1, 20, 1);
$item->defaultFieldValue = '3';
$item->cssClass = 'maxwidth100';
$item->helpText = $langs->transnoentities('AUTOBLOG_MAX_ATTEMPTSHelp');

$item = $formSetup->newItem('AUTOBLOG_AI_TIMEOUT')->setAsNumber(10, 600, 10);
$item->defaultFieldValue = '120';
$item->cssClass = 'maxwidth100';
$item->helpText = $langs->transnoentities('AUTOBLOG_AI_TIMEOUTHelp');


/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';


/*
 * View
 */

$help_url = '';
$page_name = "AutoBlogSetup";

llxHeader('', $langs->trans($page_name), $help_url, '', 0, 0, '', '', '', 'mod-autoblog page-admin');

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.img_picto($langs->trans("BackToModuleList"), 'back', 'class="pictofixedwidth"').'<span class="hideonsmartphone">'.$langs->trans("BackToModuleList").'</span></a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = autoblogAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, "fa-blog");

print '<span class="opacitymedium">'.$langs->trans("AutoBlogSetupDesc").'</span><br><br>';

// The AI service, its key and its prompts are managed by the AI module
$ai = new Ai($db);
$aiservice = $ai->getApiService();
$aisetuplink = '<a href="'.DOL_URL_ROOT.'/ai/admin/setup.php">'.$langs->trans("AutoBlogAISetup").'</a>';
if ($aiservice != 'custom' && !getDolGlobalString('AI_API_'.dol_strtoupper($aiservice).'_KEY')) {
	print info_admin($langs->trans("AutoBlogAINotConfigured", $aiservice).' '.$aisetuplink, 0, 0, 'warning');
} else {
	print info_admin($langs->trans("AutoBlogAIServiceUsed", $aiservice).' '.$aisetuplink, 0, 0, '1');
}
print '<br>';

if ($action == 'edit') {
	print $formSetup->generateOutput(true);
	print '<br>';
} else {
	print $formSetup->generateOutput();
	print '<div class="tabsAction">';
	print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&token='.newToken().'">'.$langs->trans("Modify").'</a>';
	print '</div>';
}

print '<br>';
print info_admin($langs->trans("AutoBlogCronInfo", DOL_URL_ROOT.'/cron/list.php'), 0, 0, '1');

// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();
