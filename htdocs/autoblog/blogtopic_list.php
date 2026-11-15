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
 *   	\file       htdocs/autoblog/blogtopic_list.php
 *		\ingroup    autoblog
 *		\brief      List page for blog topics
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
dol_include_once('/autoblog/lib/autoblog.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("autoblog@autoblog", "website", "other", "languages"));

// Get parameters
$action     = GETPOST('action', 'aZ09') ? GETPOST('action', 'aZ09') : 'view';
$massaction = GETPOST('massaction', 'alpha');
$confirm    = GETPOST('confirm', 'alpha');
$cancel     = GETPOST('cancel', 'alpha');
$toselect   = GETPOST('toselect', 'array:int');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'blogtopiclist';
$optioncss  = GETPOST('optioncss', 'aZ');

// Load variable for pagination
$limit = GETPOSTINT('limit') ? GETPOSTINT('limit') : $conf->liste_limit;
$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
$page = GETPOSTISSET('pageplusone') ? (GETPOSTINT('pageplusone') - 1) : GETPOSTINT('page');
if (empty($page) || $page < 0 || GETPOST('button_search', 'alpha') || GETPOST('button_removefilter', 'alpha')) {
	// If $page is not defined, or '' or -1 or if we click on clear filters
	$page = 0;
}
$offset = $limit * $page;

// Initialize technical objects
$object = new BlogTopic($db);
$extrafields = new ExtraFields($db);
$hookmanager->initHooks(array($contextpage));

// Fill the choices of website and language with the websites of the Website module
$websitestatic = new Website($db);
$websites = $websitestatic->fetchAll('ASC', 'position');
if (!is_array($websites)) {
	$websites = array();
}
autoblogCompleteTopicFields($object, $websites);

// Default sort order (if not yet defined by previous GETPOST)
if (!$sortfield) {
	$sortfield = "t.date_creation";
}
if (!$sortorder) {
	$sortorder = "DESC";
}

// Initialize array of search criteria
$search_all = trim(GETPOST('search_all', 'alphanohtml'));
$search = array();
foreach ($object->fields as $key => $val) {
	if (GETPOST('search_'.$key, 'alpha') !== '') {
		$search[$key] = GETPOST('search_'.$key, 'alpha');
	}
	if (preg_match('/^(date|timestamp|datetime)/', $val['type'])) {
		$search[$key.'_dtstart'] = dol_mktime(0, 0, 0, GETPOSTINT('search_'.$key.'_dtstartmonth'), GETPOSTINT('search_'.$key.'_dtstartday'), GETPOSTINT('search_'.$key.'_dtstartyear'));
		$search[$key.'_dtend'] = dol_mktime(23, 59, 59, GETPOSTINT('search_'.$key.'_dtendmonth'), GETPOSTINT('search_'.$key.'_dtendday'), GETPOSTINT('search_'.$key.'_dtendyear'));
	}
}

// List of fields to search into when doing a "search in all"
$fieldstosearchall = array();
foreach ($object->fields as $key => $val) {
	if (!empty($val['searchall'])) {
		$fieldstosearchall['t.'.$key] = $val['label'];
	}
}

// Definition of array of fields for columns from ->fields
$arrayfields = array();
foreach ($object->fields as $key => $val) {
	// If $val['visible']==0, then we never show the field
	if (!empty($val['visible'])) {
		$visible = (int) dol_eval((string) $val['visible'], 1);
		$arrayfields['t.'.$key] = array(
			'label' => $val['label'],
			'checked' => (($visible < 0) ? '0' : '1'),
			'enabled' => (string) (int) (abs($visible) != 3 && (bool) dol_eval((string) $val['enabled'], 1)),
			'position' => $val['position'],
			'help' => isset($val['help']) ? $val['help'] : ''
		);
	}
}
// Change position of fields in arrayfield and object, with the order saved by the user with drag and drop
include DOL_DOCUMENT_ROOT.'/core/tpl/arrayfield_object_position_fields.tpl.php';

$object->fields = dol_sort_array($object->fields, 'position');
$arrayfields = dol_sort_array($arrayfields, 'position');

$permissiontoread = $user->hasRight('autoblog', 'blogtopic', 'read');
$permissiontoadd = $user->hasRight('autoblog', 'blogtopic', 'write');
$permissiontodelete = $user->hasRight('autoblog', 'blogtopic', 'delete');

// Security check
if ($user->socid > 0) {
	accessforbidden();
}
if (!isModEnabled("autoblog")) {
	accessforbidden('Module autoblog not enabled');
}
if (!$permissiontoread) {
	accessforbidden();
}


/*
 * Actions
 */

if (GETPOST('cancel', 'alpha')) {
	$action = 'list';
	$massaction = '';
}
if (!GETPOST('confirmmassaction', 'alpha') && $massaction != 'predelete') {
	$massaction = '';
}

$parameters = array('arrayfields' => &$arrayfields);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')) { // All tests are required to be compatible with all browsers
		foreach ($object->fields as $key => $val) {
			$search[$key] = '';
			if (preg_match('/^(date|timestamp|datetime)/', $val['type'])) {
				$search[$key.'_dtstart'] = '';
				$search[$key.'_dtend'] = '';
			}
		}
		$search_all = '';
		$toselect = array();
	}
	if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')
		|| GETPOST('button_search_x', 'alpha') || GETPOST('button_search.x', 'alpha') || GETPOST('button_search', 'alpha')) {
		$massaction = ''; // Protection to avoid mass action if we force a new search during a mass action confirmation
	}

	// Mass actions
	$objectclass = 'BlogTopic';
	$objectlabel = 'BlogTopic';
	$uploaddir = $conf->autoblog->dir_output;

	global $error;
	include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
}


/*
 * View
 */

$form = new Form($db);

$title = $langs->trans("BlogTopics");
$help_url = '';

// Build and execute select
// --------------------------------------------------------------------
$sql = "SELECT";
$sql .= " ".$object->getFieldList('t');
// Add fields from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListSelect', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;
$sql = preg_replace('/,\s*$/', '', $sql);

$sqlfields = $sql; // $sql fields to remove for count total

$sql .= " FROM ".$db->prefix().$object->table_element." as t";
// Add table from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListFrom', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;
$sql .= " WHERE t.entity IN (".getEntity($object->element).")";
foreach ($search as $key => $val) {
	if (array_key_exists($key, $object->fields)) {
		if ($key == 'status' && $search[$key] == -1) {
			continue;
		}
		$field_spec = $object->fields[$key];
		$mode_search = (($object->isInt($field_spec) || $object->isFloat($field_spec)) ? 1 : 0);
		if (!empty($field_spec['arrayofkeyval'])) {
			if ($search[$key] == '-1' || ($search[$key] === '0' && !array_key_exists('0', $field_spec['arrayofkeyval']))) {
				$search[$key] = '';
			}
			$mode_search = 2;
		}
		if ($search[$key] != '') {
			$sql .= natural_search("t.".$db->escape($key), $search[$key], (($key == 'status') ? 2 : $mode_search));
		}
	} else {
		if (preg_match('/(_dtstart|_dtend)$/', $key) && $search[$key] != '') {
			$columnName = preg_replace('/(_dtstart|_dtend)$/', '', $key);
			if (preg_match('/^(date|timestamp|datetime)/', $object->fields[$columnName]['type'])) {
				if (preg_match('/_dtstart$/', $key)) {
					$sql .= " AND t.".$db->sanitize($columnName)." >= '".$db->idate($search[$key])."'";
				}
				if (preg_match('/_dtend$/', $key)) {
					$sql .= " AND t.".$db->sanitize($columnName)." <= '".$db->idate($search[$key])."'";
				}
			}
		}
	}
}
if ($search_all) {
	$sql .= natural_search(array_keys($fieldstosearchall), $search_all);
}
// Add where from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListWhere', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;

// Count total nb of records
$nbtotalofrecords = '';
if (!getDolGlobalInt('MAIN_DISABLE_FULL_SCANLIST')) {
	/* The fast and low memory method to get and count full list converts the sql into a sql count */
	$sqlforcount = preg_replace('/^'.preg_quote($sqlfields, '/').'/', 'SELECT COUNT(*) as nbtotalofrecords', $sql);

	$resql = $db->query($sqlforcount);
	if ($resql) {
		$objforcount = $db->fetch_object($resql);
		$nbtotalofrecords = $objforcount->nbtotalofrecords;
		$db->free($resql);
	} else {
		dol_print_error($db);
	}

	if (($page * $limit) > (int) $nbtotalofrecords) {	// if total resultset is smaller than the paging size (filtering), goto and load page 0
		$page = 0;
		$offset = 0;
	}
}

// Complete request and execute it with limit
$sql .= $db->order($sortfield, $sortorder);
if ($limit) {
	$sql .= $db->plimit($limit + 1, $offset);
}

$resql = $db->query($sql);
if (!$resql) {
	dol_print_error($db);
	exit;
}

$num = $db->num_rows($resql);

$arrayofselected = is_array($toselect) ? $toselect : array();

$param = '';
if (!empty($contextpage) && $contextpage != 'blogtopiclist') {
	$param .= '&contextpage='.urlencode($contextpage);
}
if ($limit > 0 && $limit != $conf->liste_limit) {
	$param .= '&limit='.((int) $limit);
}
if ($optioncss != '') {
	$param .= '&optioncss='.urlencode($optioncss);
}
if ($search_all != '') {
	$param .= '&search_all='.urlencode($search_all);
}
foreach ($search as $key => $val) {
	if (preg_match('/(_dtstart|_dtend)$/', $key) && !empty($val)) {
		$param .= '&search_'.$key.'month='.GETPOSTINT('search_'.$key.'month');
		$param .= '&search_'.$key.'day='.GETPOSTINT('search_'.$key.'day');
		$param .= '&search_'.$key.'year='.GETPOSTINT('search_'.$key.'year');
	} elseif (!is_array($val) && $val != '') {
		$param .= '&search_'.$key.'='.urlencode((string) $val);
	}
}
// Add $param from hooks
$parameters = array('param' => &$param);
$reshook = $hookmanager->executeHooks('printFieldListSearchParam', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
$param .= $hookmanager->resPrint;

// Output page
// --------------------------------------------------------------------

llxHeader('', $title, $help_url, '', 0, 0, '', '', '', 'mod-autoblog page-list bodyforlist');

// List of mass actions available
$arrayofmassactions = array();
if (!empty($permissiontodelete)) {
	$arrayofmassactions['predelete'] = img_picto('', 'delete', 'class="pictofixedwidth"').$langs->trans("Delete");
}
if (GETPOSTINT('nomassaction') || in_array($massaction, array('presend', 'predelete'))) {
	$arrayofmassactions = array();
}
$massactionbutton = $form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">'."\n";
if ($optioncss != '') {
	print '<input type="hidden" name="optioncss" value="'.dol_escape_htmltag($optioncss).'">';
}
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.dol_escape_htmltag($sortfield).'">';
print '<input type="hidden" name="sortorder" value="'.dol_escape_htmltag($sortorder).'">';
print '<input type="hidden" name="page" value="'.((int) $page).'">';
print '<input type="hidden" name="contextpage" value="'.dol_escape_htmltag($contextpage).'">';

$newcardbutton = dolGetButtonTitle($langs->trans('New'), '', 'fa fa-plus-circle', dol_buildpath('/autoblog/blogtopic_card.php', 1).'?action=create&backtopage='.urlencode($_SERVER['PHP_SELF']), '', $permissiontoadd);

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, $object->picto, 0, $newcardbutton, '', $limit, 0, 0, 1);

// Add code for pre mass action (confirmation)
$topicmail = "";
$modelmail = "";
$objecttmp = new BlogTopic($db);
$trackid = 'blogtopic'.$object->id;
include DOL_DOCUMENT_ROOT.'/core/tpl/massactions_pre.tpl.php';

if ($search_all) {
	foreach ($fieldstosearchall as $key => $val) {
		$fieldstosearchall[$key] = $langs->trans($val);
	}
	print '<div class="divsearchfieldfilter">'.$langs->trans("FilterOnInto", $search_all).implode(', ', $fieldstosearchall).'</div>'."\n";
}

$varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
$htmlofselectarray = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage, $conf->main_checkbox_left_column, 1);  // This also change content of $arrayfields with user setup
$selectedfields = $htmlofselectarray;
$selectedfields .= (count($arrayofmassactions) ? $form->showCheckAddButtons('checkforselect', 1) : '');

print '<div class="div-table-responsive">';
print '<table class="tagtable nobottomiftotal noborder liste">'."\n";

// Fields title search
// --------------------------------------------------------------------
print '<tr class="liste_titre_filter">';
// Action column
if ($conf->main_checkbox_left_column) {
	print '<td class="liste_titre center maxwidthsearch">';
	print $form->showFilterButtons('left');
	print '</td>';
}
foreach ($object->fields as $key => $val) {
	$cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
	if ($key == 'status' || in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
		$cssforfield .= ($cssforfield ? ' ' : '').'center';
	}
	if (!empty($arrayfields['t.'.$key]['checked'])) {
		print '<td class="liste_titre'.($cssforfield ? ' '.$cssforfield : '').($key == 'status' ? ' parentonrightofpage' : '').'">';
		if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
			print $form->selectarray('search_'.$key, $val['arrayofkeyval'], (isset($search[$key]) ? $search[$key] : ''), 1, 0, 0, '', 1, 0, 0, '', 'maxwidth100'.($key == 'status' ? ' search_status width100 onrightofpage' : ''), 1);
		} elseif (preg_match('/^(date|timestamp|datetime)/', $val['type'])) {
			print '<div class="nowrap">';
			print $form->selectDate($search[$key.'_dtstart'] ? $search[$key.'_dtstart'] : '', "search_".$key."_dtstart", 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('From'));
			print '</div>';
			print '<div class="nowrap">';
			print $form->selectDate($search[$key.'_dtend'] ? $search[$key.'_dtend'] : '', "search_".$key."_dtend", 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('to'));
			print '</div>';
		} elseif (strpos($val['type'], 'integer:') !== 0) {
			print '<input type="text" class="flat maxwidth75" name="search_'.$key.'" value="'.dol_escape_htmltag(isset($search[$key]) ? (string) $search[$key] : '').'">';
		}
		print '</td>';
	}
}
// Fields from hook
$parameters = array('arrayfields' => $arrayfields);
$reshook = $hookmanager->executeHooks('printFieldListOption', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
// Action column
if (!$conf->main_checkbox_left_column) {
	print '<td class="liste_titre center maxwidthsearch">';
	print $form->showFilterButtons();
	print '</td>';
}
print '</tr>'."\n";

$totalarray = array();
$totalarray['nbfield'] = 0;

// Fields title label
// --------------------------------------------------------------------
print '<tr class="liste_titre">';
// Action column
if ($conf->main_checkbox_left_column) {
	print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
	$totalarray['nbfield']++;
}
foreach ($object->fields as $key => $val) {
	$cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
	if ($key == 'status' || in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
		$cssforfield .= ($cssforfield ? ' ' : '').'center';
	}
	if (!empty($arrayfields['t.'.$key]['checked'])) {
		print getTitleFieldOfList($arrayfields['t.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 't.'.$key, '', $param, ($cssforfield ? 'class="'.$cssforfield.'"' : ''), $sortfield, $sortorder, ($cssforfield ? $cssforfield.' ' : ''))."\n";
		$totalarray['nbfield']++;
	}
}
// Hook fields
$parameters = array('arrayfields' => $arrayfields, 'param' => $param, 'sortfield' => $sortfield, 'sortorder' => $sortorder, 'totalarray' => &$totalarray);
$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
// Action column
if (!$conf->main_checkbox_left_column) {
	print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
	$totalarray['nbfield']++;
}
print '</tr>'."\n";


// Loop on record
// --------------------------------------------------------------------
$i = 0;
$imaxinloop = ($limit ? min($num, $limit) : $num);
while ($i < $imaxinloop) {
	$obj = $db->fetch_object($resql);
	if (empty($obj)) {
		break; // Should not happen
	}

	// Store properties in $object
	$object->setVarsFromFetchObj($obj);

	print '<tr data-rowid="'.$object->id.'" class="oddeven row-with-select">';

	// Action column
	if ($conf->main_checkbox_left_column) {
		print '<td class="nowrap center">';
		if ($massactionbutton || $massaction) {
			$selected = (in_array($object->id, $arrayofselected) ? 1 : 0);
			print '<input id="cb'.$object->id.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$object->id.'"'.($selected ? ' checked="checked"' : '').'>';
		}
		print '</td>';
	}
	// Fields
	foreach ($object->fields as $key => $val) {
		$cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
		if ($key == 'status' || in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
			$cssforfield .= ($cssforfield ? ' ' : '').'center';
		}
		if ($key == 'ref') {
			$cssforfield .= ($cssforfield ? ' ' : '').'nowraponall';
		}

		if (!empty($arrayfields['t.'.$key]['checked'])) {
			print '<td'.($cssforfield ? ' class="'.$cssforfield.'"' : '').'>';
			if ($key == 'status') {
				print $object->getLibStatut(5);
			} elseif ($key == 'ref') {
				print $object->getNomUrl(1);
			} elseif ($key == 'fk_website' && isset($websites[$object->fk_website])) {
				print $websites[$object->fk_website]->getNomUrl(1);
			} elseif ($key == 'lang') {
				// Flag and label of the language, its code is shown into the tooltip only
				if (!empty($object->lang)) {
					$labellang = $langs->trans('Language_'.$object->lang);
					print '<span title="'.dolPrintHTMLForAttribute((string) $object->lang).'">';
					print picto_from_langcode((string) $object->lang, 'class="paddingrightonly saturatemedium opacitylow"', 1);
					print dolPrintHTML($labellang != 'Language_'.$object->lang ? $labellang : (string) $object->lang);
					print '</span>';
				}
			} else {
				print $object->showOutputField($val, $key, (string) $object->$key, '');
			}
			print '</td>';
		}
	}
	// Fields from hook
	$parameters = array('arrayfields' => $arrayfields, 'object' => $object, 'obj' => $obj, 'i' => $i, 'totalarray' => &$totalarray);
	$reshook = $hookmanager->executeHooks('printFieldListValue', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	// Action column
	if (empty($conf->main_checkbox_left_column)) {
		print '<td class="nowrap center">';
		if ($massactionbutton || $massaction) {
			$selected = (in_array($object->id, $arrayofselected) ? 1 : 0);
			print '<input id="cb'.$object->id.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$object->id.'"'.($selected ? ' checked="checked"' : '').'>';
		}
		print '</td>';
	}

	print '</tr>'."\n";

	$i++;
}

// If no record found
if ($num == 0) {
	$colspan = 1;
	foreach ($arrayfields as $key => $val) {
		if (!empty($val['checked'])) {
			$colspan++;
		}
	}
	print '<tr><td colspan="'.$colspan.'"><span class="opacitymedium">'.$langs->trans("NoRecordFound").'</span></td></tr>';
}

$db->free($resql);

$parameters = array('arrayfields' => $arrayfields, 'sql' => $sql);
$reshook = $hookmanager->executeHooks('printFieldListFooter', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";

// End of page
llxFooter();
$db->close();
