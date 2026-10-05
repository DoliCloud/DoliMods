/* This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Module OpenStreetMap: fill street and town of a Dutch address from postcode and house number.
 * Data comes from the PDOK Locatieserver (BAG) through the Dolibarr server (ajax/pdok.php).
 * Only active on forms that have the fields zipcode, address and country_id, when the country is the Netherlands.
 */

(function () {
	var COUNTRY_ID_NL = '17';	// rowid of 'NL' in llx_c_country

	var script = document.currentScript;
	var endpoint = script ? script.src.replace(/js\/pdokautofill\.js.*$/, 'ajax/pdok.php') : '';
	var dutch = (document.documentElement.lang || navigator.language || '').toLowerCase().indexOf('nl') === 0;
	var txt = {
		nr: dutch ? 'Huisnr.' : 'House no.',
		help: dutch ? 'Vul postcode en huisnummer in: straat en plaats worden ingevuld vanuit de BAG (PDOK)' : 'Enter postcode and house number: street and town are filled from the Dutch BAG (PDOK)',
		notfound: dutch ? 'Adres niet gevonden in de BAG' : 'Address not found in the BAG',
		unavailable: dutch ? 'PDOK niet bereikbaar' : 'PDOK not reachable'
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
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
