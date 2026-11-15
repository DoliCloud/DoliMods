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

/*
 * Fill the page alias of a new blog topic from its title, like the website editor does for a new page.
 * The autofill stops when the user types an alias, and starts again if the alias is emptied.
 */
jQuery(document).ready(function () {
	var form = jQuery('input[name="action"][value="add"]').closest('form');
	var title = form.find('#label');
	var alias = form.find('#pageurl');
	if (title.length === 0 || alias.length === 0) {
		return;
	}

	var disableautofillofalias = (alias.val() !== '');

	// Same rule as BlogTopic::buildPageAlias(): lower case, no accent, only a-z, 0-9, - and _
	function buildPageAlias(text) {
		var result = text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
		result = result.replace(/[^a-z0-9_\-]+/g, '-').replace(/\-+/g, '-');
		return result.substring(0, 128).replace(/^\-+|\-+$/g, '');
	}

	title.on('keyup change', function () {
		if (!disableautofillofalias) {
			alias.val(buildPageAlias(title.val()));
		}
	});
	alias.on('keyup', function () {
		disableautofillofalias = (alias.val() !== '');
	});
	alias.on('blur', function () {
		if (alias.val() === '') {
			disableautofillofalias = false;
			title.trigger('keyup');
		}
	});
});

/*
 * Limit the combo list of languages of a blog topic to the languages of the selected website.
 * The languages of each website are provided by the attribute data-langs-by-website of the combo list.
 */
jQuery(document).ready(function () {
	var website = jQuery('select#fk_website');
	var lang = jQuery('select#lang[data-langs-by-website]');
	if (website.length === 0 || lang.length === 0) {
		return;
	}

	var langsbywebsite = lang.data('langs-by-website') || {};

	function refreshLanguages() {
		var langs = langsbywebsite[website.val()] || [];
		var selected = lang.val();
		lang.empty();
		jQuery.each(langs, function (i, langdef) {
			lang.append(jQuery('<option></option>').val(langdef[0]).text(langdef[1]));
		});
		// Keep the selected language if the website has it, else a variant of the same language, else the first one
		var codes = jQuery.map(langs, function (langdef) {
			return langdef[0];
		});
		var newselected = (codes.length ? codes[0] : null);
		if (jQuery.inArray(selected, codes) >= 0) {
			newselected = selected;
		} else if (selected) {
			var prefix = selected.split('_')[0] + '_';
			jQuery.each(codes, function (i, code) {
				if (code.indexOf(prefix) === 0) {
					newselected = code;
					return false;
				}
			});
		}
		lang.val(newselected);
		lang.trigger('change');
	}

	website.on('change', refreshLanguages);
	refreshLanguages();
});
