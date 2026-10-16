/* This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Module OpenStreetMap: address autofill on third party, contact and member forms.
 * - Netherlands: street and town from postcode and house number, PDOK Locatieserver (BAG), via ajax/pdok.php.
 * - Other countries: town from postcode (Nominatim) and street suggestions while typing (Photon), via ajax/address.php.
 *   A postcode there covers many streets, so the street is chosen from suggestions instead of being guessed.
 * All requests go through the Dolibarr server. Only active on forms with the fields zipcode, address, town and country_id.
 */

(function () {
	var COUNTRY_ID_NL = '17';	// rowid of 'NL' in llx_c_country

	var script = document.currentScript;
	var endpoint = script ? script.src.replace(/js\/pdokautofill\.js.*$/, 'ajax/pdok.php') : '';
	var endpointeu = script ? script.src.replace(/js\/pdokautofill\.js.*$/, 'ajax/address.php') : '';
	var dutch = (document.documentElement.lang || navigator.language || '').toLowerCase().indexOf('nl') === 0;
	var txt = {
		nr: dutch ? 'Huisnr.' : 'House no.',
		help: dutch ? 'Vul postcode en huisnummer in: straat en plaats worden ingevuld vanuit de BAG (PDOK)' : 'Enter postcode and house number: street and town are filled from the Dutch BAG (PDOK)',
		notfound: dutch ? 'Adres niet gevonden in de BAG' : 'Address not found in the BAG',
		unavailable: dutch ? 'PDOK niet bereikbaar' : 'PDOK not reachable',
		othertowns: dutch ? 'Andere plaatsen bij deze postcode:' : 'Other towns for this postcode:',
		streethelp: dutch ? 'Begin de straatnaam te typen voor suggesties' : 'Start typing the street name for suggestions'
	};

	function init() {
		if (!endpoint || typeof jQuery === 'undefined') {
			return;
		}
		var $ = jQuery;
		var $zip = $('input[name="zipcode"]').first();
		var $address = $('textarea[name="address"]').first();
		var $town = $('input[name="town"]').first();
		var $country = $('select[name="country_id"]').first();
		if (!$zip.length || !$address.length || !$town.length || !$country.length) {
			return;
		}

		var $nr = $('<input type="text" id="osm_pdok_housenumber" class="flat maxwidth75" autocomplete="off">')
			.attr('placeholder', txt.nr).attr('title', txt.help);
		var $msg = $('<span id="osm_pdok_message" class="opacitymedium small paddingleft"></span>');
		// Visible label, so the field is not mistaken for a second part of the zip code
		var $label = $('<label for="osm_pdok_housenumber" class="paddingleft paddingright"></label>').text(txt.nr).attr('title', txt.help);
		var $wrap = $('<span id="osm_pdok_housenumber_wrap" class="nowraponall"></span>').append($label).append($nr).append($msg);
		$zip.after($wrap);

		// Prefill house number from an existing address ("Straatnaam 10A")
		var lines = ($address.val() || '').split(/\r?\n/);
		var m = lines[lines.length - 1].match(/\s(\d{1,5}(?:\s*[-]?\s*[A-Za-z0-9]{1,4})?)\s*$/);
		if (m) {
			$nr.val(m[1]);
		}

		var disabled = false;
		var timer = null;
		var lastquery = '';

		function isNL() {
			return String($country.val()) === COUNTRY_ID_NL;
		}

		function toggle() {
			$wrap.toggle(isNL() && !disabled);
		}

		function lookup() {
			if (disabled || !isNL()) {
				return;
			}
			var postcode = ($zip.val() || '').toUpperCase().replace(/\s+/g, '');
			var nr = ($nr.val() || '').trim();
			if (!/^[1-9][0-9]{3}[A-Z]{2}$/.test(postcode) || !/^[0-9]{1,5}/.test(nr)) {
				return;
			}
			var query = postcode + '|' + nr;
			if (query === lastquery) {
				return;
			}
			lastquery = query;
			$msg.text('...');
			$.ajax({url: endpoint, data: {postcode: postcode, huisnummer: nr}, dataType: 'json'})
				.done(function (data) {
					$msg.text('');
					if (!data || !data.address) {
						return;
					}
					// Replace the last line of the address, keep previous lines (building, department...)
					var lines = ($address.val() || '').split(/\r?\n/);
					lines[lines.length - 1] = data.address;
					$address.val(lines.join('\n')).trigger('change');
					$zip.val(data.zip).trigger('change');
					$town.val(data.town).trigger('change');
				})
				.fail(function (xhr) {
					var code = xhr.responseJSON && xhr.responseJSON.error;
					if (xhr.status === 403) {
						disabled = true;
						toggle();
						return;
					}
					$msg.text(code === 'NOT_FOUND' ? txt.notfound : (code === 'BAD_HOUSENUMBER' || code === 'BAD_POSTCODE' ? '' : txt.unavailable));
					lastquery = '';
				});
		}

		function schedule() {
			clearTimeout(timer);
			timer = setTimeout(lookup, 400);
		}

		$zip.on('change keyup', schedule);
		$nr.on('change keyup', schedule);
		$country.on('change', function () {
			toggle();
			schedule();
		});
		toggle();

		initOtherCountries($, $zip, $address, $town, $country, isNL);
	}

	/*
	 * Other countries than the Netherlands
	 */
	function initOtherCountries($, $zip, $address, $town, $country, isNL) {
		if (!endpointeu) {
			return;
		}
		var disabled = false;
		var timer = null;
		var lastzip = '';
		var autotown = '';	// Last town filled by us, so we may replace it but never a town typed by the user
		var $msg = $('<span id="osm_eu_message" class="opacitymedium small paddingleft"></span>');
		$town.after($msg);

		function active() {
			return !disabled && !isNL() && String($country.val() || '') !== '' && String($country.val()) !== '0';
		}

		function fail(xhr) {
			if (xhr.status === 403) {
				disabled = true;
				$msg.text('');
			}
		}

		// Town from postcode
		function lookupZip() {
			var zip = ($zip.val() || '').trim();
			if (!active() || zip.length < 2 || zip + '|' + $country.val() === lastzip) {
				return;
			}
			lastzip = zip + '|' + $country.val();
			$.ajax({url: endpointeu, data: {lookup: 'zip', zip: zip, country_id: $country.val()}, dataType: 'json'})
				.done(function (data) {
					$msg.empty();
					if (!data || !data.towns || !data.towns.length) {
						return;
					}
					var current = ($town.val() || '').trim();
					if (current === '' || current === autotown) {
						autotown = data.towns[0];
						$town.val(autotown).trigger('change');
					}
					if (data.towns.length > 1) {
						$msg.append(document.createTextNode(txt.othertowns + ' '));
						data.towns.slice(0, 6).forEach(function (t) {
							$('<a href="#" class="paddingright"></a>').text(t).on('click', function (e) {
								e.preventDefault();
								autotown = t;
								$town.val(t).trigger('change');
							}).appendTo($msg);
						});
					}
				})
				.fail(function (xhr) {
					lastzip = '';
					fail(xhr);
				});
		}
		$zip.on('change keyup', function () {
			if (($zip.val() || '').trim() + '|' + $country.val() !== lastzip) {
				$msg.empty();
			}
			clearTimeout(timer);
			timer = setTimeout(lookupZip, 800);
		});
		$country.on('change', function () {
			lastzip = '';
			$msg.empty();
		});

		// Street suggestions while typing, on the last line of the address
		if (typeof $.fn.autocomplete !== 'function') {
			return;
		}
		function splitLastLine() {
			var lines = ($address.val() || '').split(/\r?\n/);
			var last = lines[lines.length - 1];
			var m = last.match(/^(.*?)(?:\s+(\d{1,5}\s*[A-Za-z]{0,3}(?:\s*[-\/]\s*\w{1,4})?))?\s*$/);
			return {lines: lines, street: m ? m[1] : last, number: (m && m[2]) ? m[2] : ''};
		}
		$address.attr('title', txt.streethelp).autocomplete({
			minLength: 3,
			delay: 400,
			source: function (request, response) {
				var parts = splitLastLine();
				if (!active() || parts.street.trim().length < 3) {
					response([]);
					return;
				}
				$.ajax({url: endpointeu, data: {lookup: 'street', q: parts.street.trim(), zip: ($zip.val() || '').trim(), town: ($town.val() || '').trim(), country_id: $country.val()}, dataType: 'json'})
					.done(function (data) {
						response((data && data.streets ? data.streets : []).map(function (s) {
							return {label: s.street + ((s.zip || s.town) ? ' (' + [s.zip, s.town].filter(Boolean).join(' ') + ')' : ''), value: s.street, item: s};
						}));
					})
					.fail(function (xhr) {
						fail(xhr);
						response([]);
					});
			},
			focus: function () {
				return false;
			},
			select: function (event, ui) {
				var parts = splitLastLine();
				parts.lines[parts.lines.length - 1] = ui.item.item.street + (parts.number ? ' ' + parts.number : ' ');
				$address.val(parts.lines.join('\n')).trigger('change');
				var curzip = ($zip.val() || '').trim();
				if (!curzip && ui.item.item.zip) {
					curzip = ui.item.item.zip;
					$zip.val(curzip).trigger('change');
				}
				// Change the town only when it stays consistent with the postcode
				var samezip = !ui.item.item.zip || curzip.replace(/\s+/g, '').toUpperCase() === ui.item.item.zip.replace(/\s+/g, '').toUpperCase();
				if (samezip && (!($town.val() || '').trim() || $town.val() === autotown) && ui.item.item.town) {
					autotown = ui.item.item.town;
					$town.val(autotown).trigger('change');
				}
				return false;
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
