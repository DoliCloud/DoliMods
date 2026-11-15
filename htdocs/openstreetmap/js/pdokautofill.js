/* This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Module OpenStreetMap: address autofill on third party, contact and member forms.
 * - Netherlands: street and town from postcode and house number, PDOK Locatieserver (BAG), via ajax/pdok.php.
 * - All countries: address suggestions while typing on the last line of the address, via ajax/address.php, with the
 *   service chosen for the country (Photon on OpenStreetMap data by default, PDOK for the Netherlands, Postcode.eu if set up).
 * - Other countries: town from postcode (dictionary of Dolibarr, then Nominatim), via ajax/address.php.
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
		streethelp: dutch ? 'Begin het adres te typen voor suggesties' : 'Start typing the address for suggestions',
		kept: dutch ? 'Postcode en plaats zoals ingevuld behouden; suggestie:' : 'Postcode and town kept as typed; suggested:'
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
	 * Town from postcode (countries other than the Netherlands) and address suggestions (all countries)
	 */
	function initOtherCountries($, $zip, $address, $town, $country, isNL) {
		if (!endpointeu) {
			return;
		}
		var zipdisabled = false;
		var disabledcountries = {};	// Countries for which the server answered that suggestions are disabled
		var timer = null;
		var lastzip = '';
		var autotown = '';	// Last town filled by us, so we may replace it but never a town typed by the user
		var autozip = '';	// Last postcode filled by us, same rule
		var context = '';	// Postcode.eu: context of the street or town chosen, to search further in it
		var contextvalue = '';
		var $msg = $('<span id="osm_eu_message" class="opacitymedium small paddingleft"></span>');
		$town.after($msg);

		function countryId() {
			var c = String($country.val() || '');
			return (c === '' || c === '0' || c === '-1') ? '' : c;
		}

		// Town from postcode
		function lookupZip() {
			var zip = ($zip.val() || '').trim();
			if (zipdisabled || isNL() || !countryId() || zip.length < 2 || zip + '|' + countryId() === lastzip) {
				return;
			}
			lastzip = zip + '|' + countryId();
			$.ajax({url: endpointeu, data: {lookup: 'zip', zip: zip, country_id: countryId()}, dataType: 'json'})
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
					if (xhr.status === 403) {
						zipdisabled = true;
						$msg.empty();
					}
				});
		}
		$zip.on('change keyup', function () {
			if (($zip.val() || '').trim() + '|' + countryId() !== lastzip) {
				$msg.empty();
			}
			clearTimeout(timer);
			timer = setTimeout(lookupZip, 800);
		});
		$country.on('change', function () {
			lastzip = '';
			context = '';
			$msg.empty();
		});

		// Address suggestions while typing, on the last line of the address
		if (typeof $.fn.autocomplete !== 'function') {
			return;
		}
		function splitLastLine() {
			var lines = ($address.val() || '').split(/\r?\n/);
			var last = lines[lines.length - 1];
			var m = last.match(/^(.*?)(?:\s+(\d{1,5}\s*[A-Za-z]{0,3}(?:\s*[-\/]\s*\w{1,4})?))?\s*$/);
			return {lines: lines, last: last, street: m ? m[1] : last, number: (m && m[2]) ? m[2] : ''};
		}
		function setLastLine(text) {
			var parts = splitLastLine();
			parts.lines[parts.lines.length - 1] = text;
			$address.val(parts.lines.join('\n')).trigger('change');
		}
		// Fill the form with a complete address chosen in the suggestions
		// A postcode or a town typed by the user is never replaced: they are filled only when empty, when filled
		// before by the module, or when they are the same (written in another way). The town is changed only when
		// the postcode of the form is the one of the suggestion, so postcode and town stay consistent.
		function normZip(z) {
			return String(z || '').replace(/[\s\-]+/g, '').toUpperCase();
		}
		function fill(a) {
			var parts = splitLastLine();
			var number = a.housenumber ? a.housenumber : parts.number;
			setLastLine(a.street + (number ? ' ' + number : ' '));
			$msg.empty();
			var curzip = ($zip.val() || '').trim();
			var curtown = ($town.val() || '').trim();
			var zipok = true;
			if (a.zip) {
				if (curzip === '' || curzip === autozip || normZip(curzip) === normZip(a.zip)) {
					autozip = a.zip;
					lastzip = a.zip + '|' + countryId();
					if (curzip !== a.zip) {
						$zip.val(a.zip).trigger('change');
					}
				} else {
					zipok = false;
				}
			}
			if (a.town) {
				if (zipok && (curtown === '' || curtown === autotown || curtown.toLowerCase() === String(a.town).toLowerCase())) {
					autotown = a.town;
					if (curtown !== a.town) {
						$town.val(a.town).trigger('change');
					}
				} else if (curtown.toLowerCase() !== String(a.town).toLowerCase()) {
					zipok = false;
				}
			}
			if (!zipok) {
				$msg.text(txt.kept + ' ' + [a.zip, a.town].filter(Boolean).join(' '));
			}
			context = '';
		}
		$address.attr('title', txt.streethelp).autocomplete({
			minLength: 3,
			delay: 400,
			source: function (request, response) {
				var parts = splitLastLine();
				var cid = countryId();
				var q = parts.last.trim();
				if (!cid || disabledcountries[cid] || (q.length < 3 && !context)) {
					response([]);
					return;
				}
				// The context of Postcode.eu is only valid while the line still starts with the value chosen
				if (context && q.indexOf(contextvalue.trim()) !== 0) {
					context = '';
				}
				$.ajax({url: endpointeu, data: {lookup: 'suggest', q: q, context: context, zip: ($zip.val() || '').trim(), town: ($town.val() || '').trim(), country_id: cid}, dataType: 'json'})
					.done(function (data) {
						response((data && data.items ? data.items : []).map(function (s) {
							return {label: s.label, value: s.label, item: s};
						}));
					})
					.fail(function (xhr) {
						if (xhr.status === 403) {
							disabledcountries[cid] = true;
						}
						response([]);
					});
			},
			focus: function () {
				return false;
			},
			select: function (event, ui) {
				var s = ui.item.item;
				if (!s.final) {
					// Postcode.eu: a street or a town, search further in it
					context = s.context;
					contextvalue = s.value || '';
					setLastLine(contextvalue);
					setTimeout(function () {
						$address.autocomplete('search', contextvalue);
					}, 0);
					return false;
				}
				if (s.street) {
					fill(s);
					return false;
				}
				// Postcode.eu: ask the details of the address
				$.ajax({url: endpointeu, data: {lookup: 'details', context: s.context, country_id: countryId()}, dataType: 'json'})
					.done(function (data) {
						if (data && data.address) {
							fill(data.address);
						}
					});
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
