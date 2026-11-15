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
 *    \file       htdocs/autoblog/blogtopic_card.php
 *    \ingroup    autoblog
 *    \brief      Page to create/edit/view a blog topic, generate, correct and publish its article
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
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';
dol_include_once('/autoblog/class/blogtopic.class.php');
dol_include_once('/autoblog/class/autoblogwebsite.class.php');
dol_include_once('/autoblog/lib/autoblog.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("autoblog@autoblog", "website", "other"));

// Get parameters
$id = GETPOSTINT('id');
$ref = GETPOST('ref', 'alpha');

$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'blogtopiccard';
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');

// Initialize technical objects
$object = new BlogTopic($db);
$extrafields = new ExtraFields($db);
$hookmanager->initHooks(array($object->element.'card', 'globalcard'));

// Fill the choices of website and language with the websites of the Website module
$websitestatic = new Website($db);
$websites = $websitestatic->fetchAll('ASC', 'position');
if (!is_array($websites)) {
	$websites = array();
}
autoblogCompleteTopicFields($object, $websites);
if (getDolGlobalInt('AUTOBLOG_DEFAULT_WEBSITE') > 0) {
	$object->fields['fk_website']['default'] = (string) getDolGlobalInt('AUTOBLOG_DEFAULT_WEBSITE');
}
// In forms, the page alias comes after the title (from which it is filled) and the website
$object->fields['pageurl']['position'] = 35;

if (empty($action) && empty($id) && empty($ref)) {
	$action = 'view';
}

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be 'include', not 'include_once'.

$permissiontoread = $user->hasRight('autoblog', 'blogtopic', 'read');
$permissiontoadd = $user->hasRight('autoblog', 'blogtopic', 'write'); // Used by the include of actions_addupdatedelete.inc.php
$permissiontodelete = $user->hasRight('autoblog', 'blogtopic', 'delete');
$permissiontopublish = ($user->hasRight('autoblog', 'blogtopic', 'publish') && $user->hasRight('website', 'write'));

// Security check
if ($user->socid > 0) {
	accessforbidden();
}
if (!isModEnabled('autoblog')) {
	accessforbidden("Module autoblog not enabled");
}
if (!$permissiontoread) {
	accessforbidden();
}
// Check also that the topic is in an entity of the user
restrictedArea($user, 'autoblog', $object, $object->table_element.'&'.$object->element, 'blogtopic', '', 'rowid');

$error = 0;


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	$backurlforlist = dol_buildpath('/autoblog/blogtopic_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
				$backtopage = $backurlforlist;
			} else {
				$backtopage = dol_buildpath('/autoblog/blogtopic_card.php', 1).'?id='.((!empty($id) && $id > 0) ? $id : '__ID__');
			}
		}
	}

	$triggermodname = $object->TRIGGER_PREFIX.'_MODIFY'; // Name of trigger action code to execute when we modify record

	// Actions cancel, add, update, confirm_validate (put in queue), confirm_setdraft, confirm_reopen (put back in queue), confirm_delete
	include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';

	// Generate the article now with AI
	if ($action == 'confirm_generate' && $confirm == 'yes' && $permissiontoadd && $object->id > 0) {
		$result = $object->generateArticle($user);
		if ($result > 0) {
			setEventMessages($langs->trans("AutoBlogArticleGenerated"), null, 'mesgs');
		} else {
			setEventMessages($object->error, $object->errors, 'errors');
		}
		$action = '';
	}

	// Append instructions and generate the article again
	if ($action == 'confirm_correct' && $confirm == 'yes' && $permissiontoadd && $object->id > 0) {
		$result = $object->correctArticle($user, GETPOST('extrainstructions', 'nohtml'));
		if ($result > 0) {
			setEventMessages($langs->trans("AutoBlogArticleGenerated"), null, 'mesgs');
		} else {
			setEventMessages($object->error, $object->errors, 'errors');
		}
		$action = '';
	}

	// Save the content of the article edited manually (the cancel button is managed by actions_addupdatedelete.inc.php)
	if ($action == 'updatecontent' && $permissiontoadd && $object->id > 0) {
		$result = $object->updateContent($user, GETPOST('autoblogcontent', 'restricthtml'));
		if ($result > 0) {
			setEventMessages($langs->trans("RecordSaved"), null, 'mesgs');
			header("Location: ".$_SERVER["PHP_SELF"].'?id='.((int) $object->id));
			exit;
		}
		setEventMessages($object->error, $object->errors, 'errors');
		$action = 'editcontent';
	}

	// Publish the generated article on the website
	if ($action == 'confirm_publish' && $confirm == 'yes' && $permissiontopublish && $object->id > 0) {
		$result = $object->publishArticle($user);
		if ($result > 0) {
			setEventMessages($langs->trans("AutoBlogArticlePublished"), null, 'mesgs');
		} else {
			setEventMessages($object->error, $object->errors, 'errors');
		}
		$action = '';
	}
}


/*
 * View
 */

if ($action != 'create' && !$object->id) {
	recordNotFound();
}

$form = new Form($db);

$title = $langs->trans("BlogTopic")." - ".$langs->trans('Card');
if ($action == 'create') {
	$title = $langs->trans("NewBlogTopic");
}
$help_url = '';

$morejs = array();
$morecss = array();
if ($action == 'create' || $action == 'edit') {
	$morejs[] = '/autoblog/js/autoblog.js';	// Autofill of the page alias from the title, languages of the selected website
	$morecss[] = '/autoblog/css/autoblog.css';	// Higher field for instructions
}

llxHeader('', $title, $help_url, '', 0, 0, $morejs, $morecss, '', 'mod-autoblog page-card');

// Part to create
if ($action == 'create') {
	if (empty($permissiontoadd)) {
		accessforbidden('NotEnoughPermissions', 0, 1);
	}

	print load_fiche_titre($title, '', $object->picto);

	if (empty($websites)) {
		print info_admin($langs->trans("AutoBlogNoWebsite"), 0, 0, 'warning');
	}

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.dol_escape_htmltag($backtopage).'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.dol_escape_htmltag($backtopageforcancel).'">';
	}

	print dol_get_fiche_head(array(), '');

	print '<table class="border centpercent tableforfieldcreate">'."\n";

	// Common attributes. The core template shows all languages for a field 'lang', so the language is shown
	// by us between the fields before and after it, with the languages of the selected website only.
	$fkwebsite = (GETPOSTISSET('fk_website') ? GETPOSTINT('fk_website') : (int) ($object->fields['fk_website']['default'] ?? 0));
	if ($fkwebsite <= 0 && count($websites) == 1) {
		$fkwebsite = (int) key($websites);
	}
	$allfields = $object->fields;
	list($fieldsbefore, $fieldsafter) = autoblogSplitFields($allfields, 'lang');
	$object->fields = $fieldsbefore;
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_add.tpl.php';
	print autoblogTopicLangRow($form, $websites, $fkwebsite, GETPOST('lang', 'aZ09'));
	$object->fields = $fieldsafter;
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_add.tpl.php';
	$object->fields = $allfields;

	print '</table>'."\n";

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel("Create");

	print '</form>';
}

// Part to edit record
if ($object->id > 0 && $action == 'edit') {
	print load_fiche_titre($langs->trans("BlogTopic"), '', $object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="'.dol_escape_htmltag($backtopage).'">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="'.dol_escape_htmltag($backtopageforcancel).'">';
	}

	print dol_get_fiche_head();

	print '<table class="border centpercent tableforfieldedit">'."\n";

	// Common attributes, with the language shown by us (see the create form)
	$fkwebsite = (GETPOSTISSET('fk_website') ? GETPOSTINT('fk_website') : (int) $object->fk_website);
	$allfields = $object->fields;
	list($fieldsbefore, $fieldsafter) = autoblogSplitFields($allfields, 'lang');
	$object->fields = $fieldsbefore;
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';
	print autoblogTopicLangRow($form, $websites, $fkwebsite, (GETPOSTISSET('lang') ? GETPOST('lang', 'aZ09') : (string) $object->lang));
	$object->fields = $fieldsafter;
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';
	$object->fields = $allfields;

	print '</table>';

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel();

	print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
	$formconfirm = '';

	// Confirmation to delete (using preloaded confirm popup)
	if ($action == 'delete' || ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))) {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('AutoBlogDeleteTopic'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 'action-delete');
	}
	// Confirmation to generate the article
	if ($action == 'generate') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('AutoBlogGenerateNow'), $langs->trans('AutoBlogConfirmGenerate'), 'confirm_generate', '', 'yes', 1);
	}
	// Popup to enter the correction instructions
	if ($action == 'correct') {
		$formquestion = array(
			array('type' => 'textarea', 'name' => 'extrainstructions', 'label' => $langs->trans('AutoBlogExtraInstructions'), 'value' => '', 'morecss' => 'centpercent', 'moreattr' => ' rows="6"'),
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('AutoBlogCorrectArticle'), $langs->trans('AutoBlogConfirmCorrect'), 'confirm_correct', $formquestion, 'yes', 1, 360, 800);
	}
	// Confirmation to publish the article
	if ($action == 'publish') {
		$websitelabel = (isset($websites[$object->fk_website]) ? $websites[$object->fk_website]->ref : '');
		$text = (!empty($object->date_publication) ? $langs->trans('AutoBlogConfirmUpdatePublication', $websitelabel) : $langs->trans('AutoBlogConfirmPublish', $websitelabel));
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('AutoBlogPublish'), $text, 'confirm_publish', '', 'yes', 1);
	}

	// Call Hook formConfirm
	$parameters = array('formConfirm' => $formconfirm);
	$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) {
		$formconfirm .= $hookmanager->resPrint;
	} elseif ($reshook > 0) {
		$formconfirm = $hookmanager->resPrint;
	}

	// Print form confirm
	print $formconfirm;

	$head = blogtopicPrepareHead($object);

	print dol_get_fiche_head($head, 'card', $langs->trans("BlogTopic"), -1, $object->picto, 0, '', '', 0, '', 1);

	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="'.dol_buildpath('/autoblog/blogtopic_list.php', 1).'?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

	// The title is shown under the ref
	$morehtmlref = '<div class="refidno">'.dolPrintHTML((string) $object->label).'</div>';

	// The navigation is on the ID (the ref is the ID as a string)
	dol_banner_tab($object, 'id', $linkback, 1, 'rowid', 'ref', $morehtmlref);

	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">'."\n";

	// Common attributes. The template shows them in the left column, then opens the right column (table still open).
	// The instructions of the article are shown last in the left column.
	$object->fields['instructions']['position'] = 450;
	// With this type, the website is shown with Website::getNomUrl() (the forms keep the combo list of websites)
	$object->fields['fk_website']['type'] = 'integer:Website:website/class/website.class.php';
	// Load the page linked to the topic, so its status is shown with the publication date
	$object->fetchPublishedPage();

	// The title and the page alias are shown first by us
	print '<tr class="field_label"><td class="titlefieldmiddle fieldname_label">';
	print $form->editfieldkey($langs->trans($object->fields['label']['label']), 'label', $object->label, $object, $permissiontoadd, 'string');
	print '</td><td class="valuefield fieldname_label">';
	print $form->editfieldval($langs->trans($object->fields['label']['label']), 'label', $object->label, $object, $permissiontoadd, 'string');
	print '</td></tr>';
	print '<tr class="field_pageurl"><td class="titlefieldmiddle fieldname_pageurl">'.$form->textwithpicto($langs->trans("AutoBlogPageAlias"), $langs->trans("AutoBlogPageAliasHelp")).'</td>';
	print '<td class="valuefield fieldname_pageurl">'.dol_escape_htmltag((string) $object->pageurl).'</td></tr>';
	$allfields = $object->fields;
	unset($object->fields['label'], $object->fields['pageurl']);
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';
	$object->fields = $allfields;

	// Published page
	$websiteref = (isset($websites[$object->fk_website]) ? $websites[$object->fk_website]->ref : '');
	if ($object->fk_websitepage > 0 && $websiteref) {
		print '<tr><td class="titlefield">'.$langs->trans("AutoBlogPublishedPage").'</td><td>';
		print '<a href="'.DOL_URL_ROOT.'/website/index.php?website='.urlencode($websiteref).'&pageid='.((int) $object->fk_websitepage).'">'.img_picto('', 'edit', 'class="pictofixedwidth"').$langs->trans("AutoBlogEditInWebsiteEditor").'</a>';
		print ' &nbsp; <a href="'.DOL_URL_ROOT.'/public/website/index.php?website='.urlencode($websiteref).'&pageref='.urlencode((string) $object->pageurl).'" target="_blank" rel="noopener noreferrer">'.img_picto('', 'globe', 'class="pictofixedwidth"').$langs->trans("ViewPageInNewTab").'</a>';
		print '</td></tr>';
	}

	// Last error
	if (!empty($object->last_error)) {
		print '<tr><td class="titlefield">'.$langs->trans("Error").'</td><td class="error wordbreak">'.dol_nl2br(dol_escape_htmltag((string) $object->last_error, 0, 1)).'</td></tr>';
	}

	print '</table>';

	// Editorial setup of the website, sent to the AI with the instructions of the topic (into the right column)
	$setup = new AutoBlogWebsite($db);
	$resultsetup = ($object->fk_website > 0 ? $setup->fetchByWebsite((int) $object->fk_website) : 0);
	print '<table class="border centpercent tableforfield margintoponly">';
	print '<tr><td colspan="2"><span class="opacitymedium">'.$langs->trans("AutoBlogWebsiteSetupReminder").'</span>';
	if ($object->fk_website > 0) {
		// The eye opens a dialog popup with the full request that will be sent to the AI (url must be relative to DOL_URL_ROOT)
		$urlairequest = preg_replace('/^'.preg_quote(DOL_URL_ROOT, '/').'/', '', dol_buildpath('/autoblog/blogtopic_prompt.php', 1)).'?id='.((int) $object->id);
		print ' '.dolButtonToOpenUrlInDialogPopup('autoblogairequest', $langs->transnoentitiesnoconv("AutoBlogAIRequest"), img_picto($langs->trans("AutoBlogShowAIRequest"), 'eye', 'class="paddingleft"'), $urlairequest, '', '');
		print ' <a href="'.dol_buildpath('/autoblog/autoblogwebsite_card.php', 1).'?websiteid='.((int) $object->fk_website).'">'.img_picto($langs->trans("AutoBlogEditorialSetup"), 'website', 'class="paddingleft"').'</a>';
	}
	print '</td></tr>';
	if ($resultsetup > 0 && $setup->isFilled()) {
		foreach (array('instructions', 'description', 'differentiators', 'target_audience') as $field) {
			if (trim((string) $setup->$field) !== '') {
				print '<tr><td class="titlefield">'.$langs->trans($setup->fields[$field]['label']).'</td>';
				print '<td class="wordbreak small">'.dol_nl2br(dol_escape_htmltag(dol_trunc((string) $setup->$field, 300), 0, 1)).'</td></tr>';
			}
		}
		print '<tr><td class="titlefield">'.$langs->trans("AutoBlogTargetLocation").'</td>';
		print '<td>'.((int) $setup->fk_country > 0 ? dol_escape_htmltag(getCountry((int) $setup->fk_country, '', $db, null, 0)) : $langs->trans("AutoBlogAllCountries")).'</td></tr>';
	} else {
		print '<tr><td colspan="2"><span class="opacitymedium">'.$langs->trans("AutoBlogWebsiteSetupEmpty").'</span></td></tr>';
	}
	print '</table>';
	print '</div></div>'; // Close fichehalfright and fichecenter

	print '<div class="clearboth"></div>';

	print dol_get_fiche_end();

	// Buttons for actions (not shown while the article is edited)
	if ($action != 'editcontent') {
		print '<div class="tabsAction">'."\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) {
			setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
		}

		if (empty($reshook)) {
			$urlcard = $_SERVER["PHP_SELF"].'?id='.$object->id;

			// Back to draft
			if (in_array($object->status, array($object::STATUS_QUEUED, $object::STATUS_GENERATED, $object::STATUS_ERROR))) {
				print dolGetButtonAction('', $langs->trans('SetToDraft'), 'default', $urlcard.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);
			}

			// Modify
			print dolGetButtonAction('', $langs->trans('Modify'), 'default', $urlcard.'&action=edit&token='.newToken(), '', $permissiontoadd);

			// Put in queue of the scheduled job
			if ($object->status == $object::STATUS_DRAFT) {
				print dolGetButtonAction('', $langs->trans('AutoBlogPutInQueue'), 'default', $urlcard.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $permissiontoadd);
			}
			// Put back in queue a topic in error
			if ($object->status == $object::STATUS_ERROR) {
				print dolGetButtonAction('', $langs->trans('AutoBlogPutBackInQueue'), 'default', $urlcard.'&action=confirm_reopen&confirm=yes&token='.newToken(), '', $permissiontoadd);
			}

			// Generate now
			if (in_array($object->status, array($object::STATUS_DRAFT, $object::STATUS_QUEUED, $object::STATUS_ERROR))) {
				print dolGetButtonAction('', $langs->trans('AutoBlogGenerateNow'), 'default', $urlcard.'&action=generate&token='.newToken(), '', $permissiontoadd);
			}

			// Regenerate: append instructions and generate again
			if (in_array($object->status, array($object::STATUS_GENERATED, $object::STATUS_PUBLISHED))) {
				print dolGetButtonAction('', $langs->trans('AutoBlogCorrect'), 'default', $urlcard.'&action=correct&token='.newToken(), '', $permissiontoadd);
			}

			// A draft that already has a generated content can also be edited and published
			$draftwithcontent = ($object->status == $object::STATUS_DRAFT && !empty($object->content));

			// Edit the article manually
			if ((in_array($object->status, array($object::STATUS_GENERATED, $object::STATUS_PUBLISHED)) && !empty($object->content)) || $draftwithcontent) {
				print dolGetButtonAction('', $langs->trans('AutoBlogEditArticle'), 'default', $urlcard.'&action=editcontent&token='.newToken().'#autoblogarticle', '', $permissiontoadd);
			}

			// Publish or update the publication
			if ($object->status == $object::STATUS_GENERATED || $draftwithcontent) {
				$labelpublish = (!empty($object->date_publication) ? $langs->trans('AutoBlogUpdatePublication') : $langs->trans('AutoBlogPublish'));
				print dolGetButtonAction($permissiontopublish ? '' : $langs->trans('NotEnoughPermissions'), $labelpublish, 'default', $urlcard.'&action=publish&token='.newToken(), '', $permissiontopublish);
			}

			// Delete (with preloaded confirm popup)
			$deleteUrl = $urlcard.'&action=delete&token='.newToken();
			$buttonId = 'action-delete-no-ajax';
			if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile)) {	// We can use preloaded confirm if not jmobile
				$buttonId = 'action-delete';
			}
			print dolGetButtonAction($langs->trans("Delete"), $langs->trans("Delete"), 'delete', $deleteUrl, $buttonId, $permissiontodelete);
		}
		print '</div>'."\n";
	}

	// Preview of the generated article
	if (!empty($object->content)) {
		print load_fiche_titre($langs->trans("AutoBlogGeneratedArticle"), '', 'fa-newspaper');

		print '<div class="div-table-responsive-no-min">';
		print '<table class="border centpercent tableforfield">';
		print '<tr><td class="titlefield">'.$langs->trans("Title").'</td><td><strong>'.dol_escape_htmltag((string) $object->label).'</strong></td></tr>';
		print '<tr><td class="titlefield">'.$langs->trans("Description").'</td><td>'.dol_escape_htmltag((string) $object->description).'</td></tr>';
		print '</table>';
		print '</div>';

		// Content of the article into the WYSIWYG editor on the full width of the page, read only or in edit mode. Its HTML
		// is cleaned at generation and at save, so the content filter of the editor is disabled (it may remove some tags).
		require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
		print '<div class="margintoponly" id="autoblogarticle">';
		if ($action == 'editcontent' && $permissiontoadd) {
			print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="token" value="'.newToken().'">';
			print '<input type="hidden" name="action" value="updatecontent">';
			print '<input type="hidden" name="id" value="'.((int) $object->id).'">';
			$contenttoedit = (GETPOSTISSET('autoblogcontent') ? GETPOST('autoblogcontent', 'restricthtml') : (string) $object->content);
			$doleditor = new DolEditor('autoblogcontent', $contenttoedit, '', 1000, 'dolibarr_notes', '', true, 0, true, 0, '100%', 0);
			print $doleditor->Create(1, '', false);
			// Some space between the editor and the buttons
			print '<br><div class="margintoponly">'.$form->buttonsSaveCancel().'</div>';
			print '</form>';
		} else {
			$doleditor = new DolEditor('autoblogcontent', dolPrintHTML((string) $object->content), '', 1000, 'dolibarr_readonly', '', false, 0, true, 0, '100%', 1);
			print $doleditor->Create(1, '', false);
		}
		print '</div>';
	}
}

// End of page
llxFooter();
$db->close();
