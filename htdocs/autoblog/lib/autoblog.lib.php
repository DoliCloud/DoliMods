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
 * \file    autoblog/lib/autoblog.lib.php
 * \ingroup autoblog
 * \brief   Library files with common functions for AutoBlog
 */


/**
 * Prepare admin pages header
 *
 * @return	array<array{0:string,1:string,2:string}>	Array of tabs
 */
function autoblogAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("autoblog@autoblog");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/autoblog/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	$head[$h][0] = dol_buildpath("/autoblog/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	complete_head_from_modules($conf, $langs, null, $head, $h, 'autoblog@autoblog');
	complete_head_from_modules($conf, $langs, null, $head, $h, 'autoblog@autoblog', 'remove');

	return $head;
}

/**
 * Prepare array of tabs for BlogTopic
 *
 * @param	BlogTopic	$object		BlogTopic
 * @return	array<array{0:string,1:string,2:string}>	Array of tabs
 */
function blogtopicPrepareHead($object)
{
	global $langs, $conf;

	$langs->load("autoblog@autoblog");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/autoblog/blogtopic_card.php", 1).'?id='.((int) $object->id);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'blogtopic@autoblog');
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'blogtopic@autoblog', 'remove');

	return $head;
}

/**
 * Fill the list of choices of the fields fk_website and lang of a BlogTopic with the websites and their languages
 *
 * @param	BlogTopic				$object		BlogTopic whose fields definition is completed
 * @param	array<int,Website>		$websites	Websites, as returned by Website::fetchAll()
 * @return	void
 */
function autoblogCompleteTopicFields($object, $websites)
{
	$websiteoptions = array();
	$langoptions = array();
	foreach ($websites as $website) {
		$websiteoptions[$website->id] = $website->ref;
		$langoptions = array_merge($langoptions, autoblogGetWebsiteLanguages($website));
	}

	$object->fields['fk_website']['arrayofkeyval'] = $websiteoptions;
	$object->fields['lang']['arrayofkeyval'] = $langoptions;
}

/**
 * Return the languages (long codes like fr_FR) of a website: the ones of its main language first, then the ones of its other languages.
 * A short code of the website (fr) gives its main variant first (fr_FR), then its other variants (fr_BE, fr_CA...).
 *
 * @param	Website		$website	Website
 * @return	array<string,string>	Array of language code => label
 */
function autoblogGetWebsiteLanguages($website)
{
	global $conf, $langs;

	// Available languages are read once (all variants, and main variant of each language)
	if (!isset($conf->cache['autoblog_languages'])) {
		$conf->cache['autoblog_languages'] = array(
			'all' => $langs->get_available_languages(DOL_DOCUMENT_ROOT),
			'main' => $langs->get_available_languages(DOL_DOCUMENT_ROOT, 0, 0, 1),
		);
	}
	$alllangs = $conf->cache['autoblog_languages']['all'];
	$mainlangs = $conf->cache['autoblog_languages']['main'];

	$result = array();
	$websitelangs = array_merge(array((string) $website->lang), explode(',', (string) $website->otherlang));
	foreach ($websitelangs as $websitelang) {
		$websitelang = trim($websitelang);
		if ($websitelang === '') {
			continue;
		}
		$mainvariants = array();
		$othervariants = array();
		foreach ($alllangs as $code => $label) {
			if ($code != $websitelang && strtolower((string) preg_replace('/_.*$/', '', $code)) != strtolower($websitelang)) {
				continue;
			}
			$label = ($label != 'Language_'.$code ? $label.' ('.$code.')' : $code);	// Some codes have no translation
			if (isset($mainlangs[$code])) {
				$mainvariants[$code] = $label;
			} else {
				$othervariants[$code] = $label;
			}
		}
		asort($othervariants);
		$result += $mainvariants + $othervariants;
	}

	return $result;
}

/**
 * Split a definition of fields into the fields before and the fields after a given field (sorted by position)
 *
 * @param	array<string,array<string,mixed>>	$fields		Definition of fields ($object->fields)
 * @param	string								$key		Field used to split (it is in none of the result arrays)
 * @return	array{0:array<string,array<string,mixed>>,1:array<string,array<string,mixed>>}	Fields before, fields after
 */
function autoblogSplitFields($fields, $key)
{
	$before = array();
	$after = array();
	foreach ($fields as $fieldkey => $val) {
		if ($fieldkey == $key) {
			continue;
		}
		if ($val['position'] < $fields[$key]['position']) {
			$before[$fieldkey] = $val;
		} else {
			$after[$fieldkey] = $val;
		}
	}

	return array($before, $after);
}

/**
 * Return the form row to choose the language of a blog topic among the languages of its website.
 * The combo list is rebuilt by js/autoblog.js when another website is selected.
 *
 * @param	Form				$form			Form object
 * @param	array<int,Website>	$websites		Websites, as returned by Website::fetchAll()
 * @param	int					$fkwebsite		ID of the selected website
 * @param	string				$selected		Selected language code
 * @return	string								HTML of the row
 */
function autoblogTopicLangRow($form, $websites, $fkwebsite, $selected)
{
	global $langs;

	// Languages of each website, as arrays to keep the order (main language first)
	$langsbywebsite = array();
	foreach ($websites as $website) {
		$langsbywebsite[$website->id] = array();
		foreach (autoblogGetWebsiteLanguages($website) as $code => $label) {
			$langsbywebsite[$website->id][] = array($code, $label);
		}
	}
	$options = (isset($websites[$fkwebsite]) ? autoblogGetWebsiteLanguages($websites[$fkwebsite]) : array());
	// A language saved with a short code (fr) preselects its first variant (fr_FR)
	if ($selected !== '' && !isset($options[$selected])) {
		foreach (array_keys($options) as $code) {
			if (strpos($code, $selected.'_') === 0) {
				$selected = $code;
				break;
			}
		}
	}

	$out = '<tr class="field_lang"><td class="titlefieldcreate">';
	$out .= '<label for="lang" class="block">'.$form->textwithpicto($langs->trans("Language"), $langs->trans("AutoBlogLangHelp")).'</label>';
	$out .= '</td><td class="valuefieldcreate">';
	$out .= img_picto('', 'language', 'class="pictofixedwidth"');
	$out .= $form->selectarray('lang', $options, $selected, 0, 0, 0, ' data-langs-by-website="'.dol_escape_htmltag((string) json_encode($langsbywebsite)).'"', 0, 0, 0, '', 'minwidth200');
	$out .= '</td></tr>';

	return $out;
}
