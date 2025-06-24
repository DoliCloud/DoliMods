<?php
/* Copyright (C) 2024		Alice Adminson				<myemail@mycompany.com>
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

/**
 * \file    lib/discussion_channel.lib.php
 * \ingroup discussion
 * \brief   Library files with common functions for Channel
 */

/**
 * Prepare array of tabs for Channel
 *
 * @param	Channel	$object					Channel
 * @return 	array<array{string,string,string}>	Array of tabs
 */
function channelPrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("discussion@discussion");

	$showtabofpagecontact = 1;
	$showtabofpagenote = 1;
	$showtabofpagedocument = 1;
	$showtabofpageagenda = 1;

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/discussion/channel_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Channel");
	$head[$h][2] = 'card';
	$h++;

	if ($showtabofpagecontact) {
		$head[$h][0] = dol_buildpath("/discussion/channel_contact.php", 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Contacts");
		$head[$h][2] = 'contact';
		$h++;
	}

	if ($showtabofpagenote) {
		if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
			$nbNote = 0;
			if (!empty($object->note_private)) {
				$nbNote++;
			}
			if (!empty($object->note_public)) {
				$nbNote++;
			}
			$head[$h][0] = dol_buildpath('/discussion/channel_note.php', 1).'?id='.$object->id;
			$head[$h][1] = $langs->trans('Notes');
			if ($nbNote > 0) {
				$head[$h][1] .= (!getDolGlobalInt('MAIN_OPTIMIZEFORTEXTBROWSER') ? '<span class="badge marginleftonlyshort">'.$nbNote.'</span>' : '');
			}
			$head[$h][2] = 'note';
			$h++;
		}
	}

	if ($showtabofpagedocument) {
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
		$upload_dir = $conf->discussion->dir_output."/channel/".dol_sanitizeFileName($object->ref);
		$nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
		$nbLinks = Link::count($db, $object->element, $object->id);
		$head[$h][0] = dol_buildpath("/discussion/channel_document.php", 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans('Documents');
		if (($nbFiles + $nbLinks) > 0) {
			$head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
		}
		$head[$h][2] = 'document';
		$h++;
	}

	if ($showtabofpageagenda) {
		$head[$h][0] = dol_buildpath("/discussion/channel_agenda.php", 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Events");
		$head[$h][2] = 'agenda';
		$h++;
	}

	if ($object->status == $object::STATUS_VALIDATED) {
		$head[$h][0] = dol_buildpath("/discussion/channel_messages.php", 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Messages");
		$head[$h][2] = 'messages';
		$h++;
	}
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@discussion:/discussion/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@discussion:/discussion/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'channel@discussion');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'channel@discussion', 'remove');

	return $head;
}

function channelGetUserPhoto($user, $cache = 0){
	global $conf;
	$dir = $conf->user->dir_output;
	if (!empty($user->photo)) {
		if (dolIsAllowedForPreview($user->photo)) {
			$file = get_exdir(0, 0, 0, 0, $user, 'user') . 'photos/' . getImageFileNameForSize($user->photo, '_mini');
		}
	}
	if (getDolGlobalString('MAIN_OLD_IMAGE_LINKS')) {
		$altfile = $user->id . ".jpg"; // For backward compatibility
	}

	$email = $user->email;
	$entity = (empty($user->entity) ? $conf->entity : $user->entity);
	$nophoto = '/public/theme/common/nophoto.png';
	$img = $nophoto;
	$imgmode = "generic"; 
	if ($dir) {
		if ($file && file_exists($dir . "/" . $file)) {
			$img = '?modulepart=userphoto&entity=' . $entity . '&file=' . urlencode($file) . '&cache='.$cache;
			$imgmode = "viewimage";
		} elseif ($altfile && file_exists($dir . "/" . $altfile)) {
			$img = '?modulepart=userphoto&entity=' . $entity . '&file=' . urlencode($altfile) . '&cache='.$cache;
			$imgmode = "viewimage";
		} else {
			if (!empty($object->gender) && $user->gender == 'man') {
				$img = '/public/theme/common/user_man.png';
			}
			if (!empty($object->gender) && $user->gender == 'woman') {
				$img = '/public/theme/common/user_woman.png';
			}
			if (isModEnabled('gravatar') && $email) {
				// see https://gravatar.com/site/implement/images/php/
				$img = dol_hash(strtolower(trim($email)), 'sha256', 1) . '?s=0&d=identicon'; // gravatar need md5 hash
				$imgmode = "gravatar"; 
			}
		}
	}
	return array("img" => $img, "imgmode" => $imgmode);
}
