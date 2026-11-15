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
 *   	\file       htdocs/autoblog/autoblogwebsite_list.php
 *		\ingroup    autoblog
 *		\brief      List of the websites of the Website module with their editorial setup for AutoBlog
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
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
dol_include_once('/autoblog/class/blogtopic.class.php');
dol_include_once('/autoblog/class/autoblogwebsite.class.php');

// Load translation files required by the page
$langs->loadLangs(array("autoblog@autoblog", "website", "languages", "other"));

$hookmanager->initHooks(array('autoblogwebsitelist'));

// Security check
if ($user->socid > 0) {
	accessforbidden();
}
if (!isModEnabled('autoblog')) {
	accessforbidden('Module autoblog not enabled');
}
if (!$user->hasRight('autoblog', 'blogtopic', 'read')) {
	accessforbidden();
}


/*
 * Actions
 */

// Nothing to do: the editorial setup of a website is edited on its card.


/*
 * View
 */

$websitestatic = new Website($db);
$websites = $websitestatic->fetchAll('ASC', 'position');
if (!is_array($websites)) {
	dol_print_error($db, $websitestatic->error);
	$websites = array();
}

// Load the editorial setups of all websites in one request
$setupstatic = new AutoBlogWebsite($db);
$setups = array();
$sql = "SELECT ".$setupstatic->getFieldList('t');
$sql .= " FROM ".$db->prefix().$setupstatic->table_element." as t";
$sql .= " WHERE t.entity IN (".getEntity($setupstatic->element).")";
$resql = $db->query($sql);
if ($resql) {
	while ($obj = $db->fetch_object($resql)) {
		$setup = new AutoBlogWebsite($db);
		$setup->setVarsFromFetchObj($obj);
		$setups[(int) $setup->fk_website] = $setup;
	}
	$db->free($resql);
} else {
	dol_print_error($db);
}

// Load the labels of the target countries in one request
$countries = array();
$countryids = array();
foreach ($setups as $setup) {
	if ((int) $setup->fk_country > 0) {
		$countryids[] = (int) $setup->fk_country;
	}
}
if (count($countryids)) {
	$sql = "SELECT c.rowid, c.code, c.label FROM ".$db->prefix()."c_country as c";
	$sql .= " WHERE c.rowid IN (".$db->sanitize(implode(',', array_unique($countryids))).")";
	$resql = $db->query($sql);
	if ($resql) {
		while ($obj = $db->fetch_object($resql)) {
			$countrylabel = $langs->trans("Country".$obj->code);
			$countries[(int) $obj->rowid] = ($countrylabel != "Country".$obj->code ? $countrylabel : $obj->label);
		}
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

// Count the topics of each website by status in one request
$topicstatic = new BlogTopic($db);
$counts = array();
$sql = "SELECT t.fk_website, t.status, COUNT(t.rowid) as nb";
$sql .= " FROM ".$db->prefix().$topicstatic->table_element." as t";
$sql .= " WHERE t.entity IN (".getEntity($topicstatic->element).")";
$sql .= " GROUP BY t.fk_website, t.status";
$resql = $db->query($sql);
if ($resql) {
	while ($obj = $db->fetch_object($resql)) {
		$counts[(int) $obj->fk_website][(int) $obj->status] = (int) $obj->nb;
	}
	$db->free($resql);
} else {
	dol_print_error($db);
}

$title = $langs->trans("WebsitesEditorialSetup");

llxHeader('', $title, '', '', 0, 0, '', '', '', 'mod-autoblog page-list');

print_barre_liste($title, 0, $_SERVER["PHP_SELF"], '', '', '', '', count($websites), count($websites), 'website', 0, '', '', 0, 0, 0, 1);

print '<span class="opacitymedium">'.$langs->trans("WebsitesEditorialSetupDesc").'</span><br><br>';

print '<div class="div-table-responsive">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<th>'.$langs->trans("WebSite").'</th>';
print '<th>'.$langs->trans("Language").'</th>';
print '<th class="center">'.$langs->trans("Status").'</th>';
print '<th>'.$langs->trans("AutoBlogTargetLocation").'</th>';
print '<th class="center">'.$langs->trans("AutoBlogEditorialSetup").'</th>';
print '<th class="right">'.$langs->trans("AutoBlogStatusQueued").'</th>';
print '<th class="right">'.$langs->trans("AutoBlogStatusGenerated").'</th>';
print '<th class="right">'.$langs->trans("AutoBlogStatusPublished").'</th>';
print '<th></th>';
print '</tr>';

foreach ($websites as $website) {
	$setup = ($setups[(int) $website->id] ?? null);
	$urlcard = dol_buildpath('/autoblog/autoblogwebsite_card.php', 1).'?websiteid='.((int) $website->id);

	print '<tr class="oddeven">';

	// Website
	print '<td class="tdoverflowmax200"><a href="'.$urlcard.'">'.img_picto('', 'website', 'class="pictofixedwidth"').dol_escape_htmltag((string) $website->ref).'</a>';
	if (!empty($website->description)) {
		print '<br><span class="opacitymedium small">'.dol_escape_htmltag(dol_trunc((string) $website->description, 60)).'</span>';
	}
	print '</td>';

	// Languages
	print '<td class="tdoverflowmax150">'.dol_escape_htmltag((string) $website->lang);
	if (!empty($website->otherlang)) {
		print ' <span class="opacitymedium">('.dol_escape_htmltag((string) $website->otherlang).')</span>';
	}
	print '</td>';

	// Status of the website
	print '<td class="center">'.($website->status == Website::STATUS_VALIDATED ? img_picto($langs->trans("AutoBlogWebsiteOnline"), 'switch_on') : img_picto($langs->trans("AutoBlogWebsiteOffline"), 'switch_off')).'</td>';

	// Target location
	print '<td class="tdoverflowmax150">';
	if ($setup !== null && (int) $setup->fk_country > 0) {
		print dol_escape_htmltag($countries[(int) $setup->fk_country] ?? '');
	} elseif ($setup !== null) {
		print '<span class="opacitymedium">'.$langs->trans("AutoBlogAllCountries").'</span>';
	}
	print '</td>';

	// Editorial setup filled or not
	print '<td class="center">'.yn(($setup !== null && $setup->isFilled()) ? 1 : 0).'</td>';

	// Number of topics
	foreach (array(BlogTopic::STATUS_QUEUED, BlogTopic::STATUS_GENERATED, BlogTopic::STATUS_PUBLISHED) as $status) {
		$nb = ($counts[(int) $website->id][$status] ?? 0);
		print '<td class="right">';
		if ($nb > 0) {
			print '<a href="'.dol_buildpath('/autoblog/blogtopic_list.php', 1).'?search_fk_website='.((int) $website->id).'&search_status='.((int) $status).'">'.$nb.'</a>';
		} else {
			print '<span class="opacitymedium">0</span>';
		}
		print '</td>';
	}

	// Link to edit
	print '<td class="center">';
	if ($user->hasRight('autoblog', 'websitesetup', 'write')) {
		print '<a class="editfielda" href="'.$urlcard.'&action=edit&token='.newToken().'">'.img_edit().'</a>';
	}
	print '</td>';

	print '</tr>';
}

if (empty($websites)) {
	print '<tr><td colspan="9"><span class="opacitymedium">'.$langs->trans("AutoBlogNoWebsite").'</span></td></tr>';
}

print '</table>';
print '</div>';

// End of page
llxFooter();
$db->close();
