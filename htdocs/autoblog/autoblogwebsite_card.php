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
 *   	\file       htdocs/autoblog/autoblogwebsite_card.php
 *		\ingroup    autoblog
 *		\brief      View and edit the editorial setup of a website, used to generate its blog articles
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
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
dol_include_once('/autoblog/class/autoblogwebsite.class.php');

// Load translation files required by the page
$langs->loadLangs(array("autoblog@autoblog", "website", "errors", "other"));

// Get parameters
$websiteid = GETPOSTINT('websiteid');
$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel', 'alpha');

$hookmanager->initHooks(array('autoblogwebsitecard', 'globalcard'));

$permissiontoread = $user->hasRight('autoblog', 'blogtopic', 'read');
$permissiontoedit = $user->hasRight('autoblog', 'websitesetup', 'write');

// Security check
if ($user->socid > 0) {
	accessforbidden();
}
if (!isModEnabled('autoblog')) {
	accessforbidden('Module autoblog not enabled');
}
if (!$permissiontoread) {
	accessforbidden();
}

// Load the website (fetch is restricted to the websites of the current entity) and its editorial setup
$website = new Website($db);
if ($websiteid <= 0 || $website->fetch($websiteid) <= 0) {
	recordNotFound();
}
$object = new AutoBlogWebsite($db);
$result = $object->fetchByWebsite((int) $website->id);
if ($result < 0) {
	dol_print_error($db, $object->error);
	exit;
}

// Multi-line text sections, then single URL sections
$textfields = array('description', 'differentiators', 'target_audience', 'url_competitors', 'instructions');
$urlfields = array('url_product_feed', 'url_sitemap', 'url_style_example');


/*
 * Actions
 */

$parameters = array('websiteid' => $websiteid);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	if ($cancel) {
		$action = '';
	}

	if ($action == 'update' && $permissiontoedit) {
		$error = 0;

		foreach ($textfields as $field) {
			$object->$field = trim(GETPOST($field, 'alphanohtml'));
		}
		foreach ($urlfields as $field) {
			$object->$field = trim(GETPOST($field, 'url'));
		}
		$object->fk_country = (GETPOSTINT('fk_country') > 0 ? GETPOSTINT('fk_country') : null);

		// Check the URLs
		$urlstocheck = $object->getLines('url_competitors');
		foreach ($urlfields as $field) {
			$urlstocheck[] = (string) $object->$field;
		}
		foreach ($urlstocheck as $url) {
			if ($url !== '' && !isValidUrl($url, 1)) {
				$error++;
				setEventMessages($langs->trans("ErrorBadUrl", $url), null, 'errors');
			}
		}

		if (!$error) {
			if ($object->id > 0) {
				$result = $object->update($user);
			} else {
				$object->fk_website = (int) $website->id;
				$result = $object->create($user);
			}
			if ($result > 0) {
				setEventMessages($langs->trans("RecordSaved"), null, 'mesgs');
				header("Location: ".$_SERVER["PHP_SELF"].'?websiteid='.((int) $website->id));
				exit;
			}
			setEventMessages($object->error, $object->errors, 'errors');
		}
		$action = 'edit';
	}
}


/*
 * View
 */

$form = new Form($db);

$title = $langs->trans("AutoBlogEditorialSetup").' - '.$website->ref;

llxHeader('', $title, '', '', 0, 0, '', '', '', 'mod-autoblog page-card');

$linkback = '<a href="'.dol_buildpath('/autoblog/autoblogwebsite_list.php', 1).'">'.$langs->trans("BackToList").'</a>';
print load_fiche_titre($langs->trans("AutoBlogEditorialSetup"), $linkback, 'website');

// Website summary
print '<div class="fichecenter">';
print '<table class="border centpercent tableforfield">';
print '<tr><td class="titlefieldcreate">'.$langs->trans("WebSite").'</td><td>';
print '<a href="'.DOL_URL_ROOT.'/website/index.php?website='.urlencode((string) $website->ref).'">'.img_picto('', 'website', 'class="pictofixedwidth"').dol_escape_htmltag((string) $website->ref).'</a>';
print '</td></tr>';
print '<tr><td>'.$langs->trans("Language").'</td><td>'.dol_escape_htmltag((string) $website->lang).(empty($website->otherlang) ? '' : ' <span class="opacitymedium">('.dol_escape_htmltag((string) $website->otherlang).')</span>').'</td></tr>';
print '</table>';
print '</div>';
print '<br>';

if ($action == 'edit' && $permissiontoedit) {
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="websiteid" value="'.((int) $website->id).'">';
}

// One section per part of the editorial setup
$sections = array(
	'instructions' => 'AutoBlogGenericInstructionsHelp',
	'description' => '',
	'differentiators' => '',
	'target_audience' => '',
	'fk_country' => 'AutoBlogTargetLocationHelp',
	'url_competitors' => 'AutoBlogOneUrlPerLine',
	'url_product_feed' => 'AutoBlogNotUsedYet',
	'url_sitemap' => 'AutoBlogNotUsedYet',
	'url_style_example' => 'AutoBlogUrlStyleExampleHelp',
);
foreach ($sections as $field => $helpkey) {
	print load_fiche_titre($langs->trans($object->fields[$field]['label']), '', '', 0, '', 'margintoponly');
	if ($helpkey) {
		print '<span class="opacitymedium">'.$langs->trans($helpkey).'</span><br>';
	}

	if ($action == 'edit' && $permissiontoedit) {
		if ($field == 'fk_country') {
			print $form->select_country((string) ((int) $object->fk_country > 0 ? $object->fk_country : ''), 'fk_country', '', 0, 'minwidth300', '', 1);
		} elseif (in_array($field, $urlfields)) {
			print '<input type="text" class="flat minwidth500 widthcentpercentminusx" name="'.$field.'" value="'.dol_escape_htmltag((string) $object->$field).'" placeholder="https://">';
		} else {
			print '<textarea class="flat centpercent" rows="'.($field == 'url_competitors' ? 3 : 5).'" name="'.$field.'">'.dolPrintText((string) $object->$field).'</textarea>';
		}
	} else {
		print '<div class="wordbreak">';
		if ($field == 'fk_country') {
			print ((int) $object->fk_country > 0 ? dol_escape_htmltag(getCountry((int) $object->fk_country, '', $db, null, 0)) : '<span class="opacitymedium">'.$langs->trans("AutoBlogAllCountries").'</span>');
		} elseif ($field == 'url_competitors' || in_array($field, $urlfields)) {
			foreach ($object->getLines($field) as $url) {
				print dol_print_url($url, '_blank', 64, 1).'<br>';
			}
		} else {
			print dol_nl2br((string) $object->$field);
		}
		print '</div>';
	}
}

if ($action == 'edit' && $permissiontoedit) {
	print '<br>';
	print $form->buttonsSaveCancel();
	print '</form>';
} else {
	print '<div class="tabsAction">';
	print dolGetButtonAction('', $langs->trans('Modify'), 'default', $_SERVER["PHP_SELF"].'?websiteid='.((int) $website->id).'&action=edit&token='.newToken(), '', $permissiontoedit);
	print '</div>';
}

// End of page
llxFooter();
$db->close();
