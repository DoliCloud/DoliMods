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
 *  \file       channel_contact.php
 *  \ingroup    discussion
 *  \brief      Tab for contacts linked to Channel
 */

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

require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
dol_include_once('/discussion/class/channel.class.php');
dol_include_once('/discussion/lib/discussion_channel.lib.php');
dol_include_once('/discussion/core/modules/modDiscussion.class.php');
// Load translation files required by the page
$langs->loadLangs(array("discussion@discussion", "companies", "other", "mails"));

$id     = (GETPOST('id') ? GETPOSTINT('id') : GETPOSTINT('facid')); // For backward compatibility
$ref    = GETPOST('ref', 'alpha');
$lineid = GETPOSTINT('lineid');
$socid  = GETPOSTINT('socid');
$action = GETPOST('action', 'aZ09');

// Initialize a technical objects
$object = new Channel($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->discussion->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array($object->element.'contact', 'globalcard')); // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be 'include', not 'include_once'. Include fetch and fetch_thirdparty but not fetch_optionals

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 1;
$ischanneladmin = $object->isChannelAdmin($user->id);
if ($enablepermissioncheck) {
	$permissiontoread = $user->hasRight('discussion', 'channel', 'read') && $ischanneladmin;
	$permissiontoadd  = $user->hasRight('discussion', 'channel', 'write') && $ischanneladmin;
} else {
	$permissiontoread = 1;
	$permissiontoadd = 1;
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
/*
 * Add a new contact
 */

if ($action == 'addcontact' && $permissiontoadd) {
	$contactid = (GETPOST('userid') ? GETPOSTINT('userid') : GETPOSTINT('contactid'));
	$typeid = (GETPOST('typecontact') ? GETPOST('typecontact') : GETPOST('type'));
	$source = GETPOST("source", 'aZ09');
	$rights = $object->getRights();
	$module = "discussion";
	$result = $object->add_contact($contactid, $typeid, $source);
	if ($result >= 0) {
		if ($source == "internal") {
			$edituser = new User($db);
			$edituser->fetch($contactid);
			//SET rights for every contacttype
			if ($typeid == "CHANNELADMIN") {
				$rights = "read, write, delete";
				$result = $edituser->addrights(0, $module, $rights, $entity);
			} else {
				$rights = $tmpmodule->numero;
				$result = $edituser->addrights(0, $module, $rights, $entity);
			}
			if ($result < 0) {
				setEventMessages($edituser->error, $edituser->errors, 'errors');
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
		exit;
	} else {
		if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS') {
			$langs->load("errors");
			setEventMessages($langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType"), null, 'errors');
		} else {
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}
} elseif ($action == 'swapstatut' && $permissiontoadd) {
	// Toggle the status of a contact
	$result = $object->swapContactStatus(GETPOSTINT('ligne'));
} elseif ($action == 'deletecontact' && $permissiontoadd) {	// Permission to add on object because this is an update of a link of object, not a deletion of data
	// Deletes a contact
	$result = $object->delete_contact($lineid);

	if ($result >= 0) {
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
		exit;
	} else {
		dol_print_error($db);
	}
}


/*
 * View
 */

$title = $langs->trans("Channel")." - ".$langs->trans('ContactsAddresses');
//$title = $object->ref." - ".$langs->trans('ContactsAddresses');
$help_url = '';
//$help_url='EN:Module_Third_Parties|FR:Module_Tiers|ES:Empresas';
llxHeader('', $title, $help_url, '', 0, 0, '', '', '', 'mod-discussion page-card_contact');

$form = new Form($db);
$formcompany = new FormCompany($db);
$contactstatic = new Contact($db);
$userstatic = new User($db);


/* *************************************************************************** */
/*                                                                             */
/* View and edit mode                                                         */
/*                                                                             */
/* *************************************************************************** */

if ($object->id) {
	/*
	 * Show tabs
	 */
	$head = channelPrepareHead($object);

	print dol_get_fiche_head($head, 'contact', $langs->trans("Channel"), -1, $object->picto);

	$linkback = '<a href="'.dol_buildpath('/discussion/channel_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

	$morehtmlref = '<div class="refidno">';
	/*
	 // Ref customer
	 $morehtmlref.=$form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
	 $morehtmlref.=$form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
	 // Thirdparty
	 $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . (is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
	 // Project
	 if (isModEnabled('project')) {
	 $langs->load("projects");
	 $morehtmlref.='<br>'.$langs->trans('Project') . ' ';
	 if ($permissiontoadd)
	 {
	 if ($action != 'classify')
	 //$morehtmlref.='<a class="editfielda" href="' . $_SERVER['PHP_SELF'] . '?action=classify&token='.newToken().'&id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> : ';
	 $morehtmlref.=' : ';
	 if ($action == 'classify') {
	 //$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
	 $morehtmlref.='<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
	 $morehtmlref.='<input type="hidden" name="action" value="classin">';
	 $morehtmlref.='<input type="hidden" name="token" value="'.newToken().'">';
	 $morehtmlref.=$formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
	 $morehtmlref.='<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
	 $morehtmlref.='</form>';
	 } else {
	 $morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
	 }
	 } else {
	 if (!empty($object->fk_project)) {
	 $proj = new Project($db);
	 $proj->fetch($object->fk_project);
	 $morehtmlref .= ': '.$proj->getNomUrl();
	 } else {
	 $morehtmlref .= '';
	 }
	 }
	 }*/
	$morehtmlref .= '</div>';

	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref, '', 0, '', '', 1);

	print dol_get_fiche_end();

	print '<br>';

	$hideaddcontactforthirdparty = 1;

	// Contacts lines (modules that overwrite templates must declare this into descriptor)
	$dirtpls = array_merge($conf->modules_parts['tpl'], array('/core/tpl'));
	foreach ($dirtpls as $reldir) {
		$res = @include dol_buildpath($reldir.'/contacts.tpl.php');
		if ($res) {
			break;
		}
	}
}

// End of page
llxFooter();
$db->close();
