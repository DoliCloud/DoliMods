# ChangeLog MODULE OPENSTREETMAP FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


## 4.3.0

* Zip codes and towns: a file is not imported at once anymore. It is put in a queue (documents/openstreetmap/ziptown), extracted on disk and read line by line by parts, so the memory used does not depend on the size of the file (16 MB for the 1.8 million lines of GB_full) and no request of the web server runs for long. Duplicates are checked in the database by groups.
* The queue is imported in the background by a scheduled job of the module (enabled only while files are waiting, disabled again when the queue is empty), and by the setup page while it is open. The page shows the progress in percent; the list of scheduled jobs of Dolibarr shows it too.
* New setting: maximum number of lines of a file (default 500000), with the effect on the autocompletion of towns of Dolibarr.
* Disable and enable the module after the upgrade, so the scheduled job is created.

## 4.2.2

* Zip codes and towns: the size of the file inside a zip is checked before it is read, so a very large file (for example GB_full of GeoNames, 178 MB) is refused with a message instead of exhausting the memory of PHP.
* Zip codes and towns: when a full postcode is not in the dictionary, its first part is searched (for example AL3 for AL3 8QE), so the lists of GeoNames that only have the first part (GB, CA) can be used.

## 4.2.1

* Zip codes and towns: a file of the GeoNames gazetteer (export/dump, all geographical names without postal codes) was read as a list of zip codes. It is now recognised and refused, with a link to the postal codes files (export/zip).
* Zip codes and towns: uploading a zip file failed.
* Zip codes and towns: the max size of an upload on the server is shown and checked before sending; above it the form arrived empty.

## 4.2.0

* Map of all objects: the page shows at once the addresses already located. The other ones are located afterwards by the browser, one at a time (ajax/geocode.php), with the session released and short timeouts, and the search stops after 3 errors of the service. Before, up to 25 addresses were located while the page was loading, which could make Dolibarr unresponsive with a slow service.
* Address suggestions for all countries while typing the address: street, house number, postcode and town, with Photon on OpenStreetMap data (default, free), PDOK for the Netherlands, or Postcode.eu.
* Photon: the language is only sent when Photon supports it (en, de, fr); for the other languages (for example Greek) the public server answered with an error, so no suggestion was shown.
* New optional connector to Postcode.eu (paid, own key) for the selected countries. The key and secret are stored encrypted; answers are not cached.
* New tab "Zip codes and towns": complete the dictionary of zip codes and towns of Dolibarr per country with GeoNames postal codes (download or file). The town of a postcode is taken from this dictionary first.
* Requests to external services use short timeouts (3 s to connect, 8 s to answer, constants OPENSTREETMAP_CONNECT_TIMEOUT and OPENSTREETMAP_RESPONSE_TIMEOUT).
* The setup and the README say what to expect in each country.

## 4.1.3

* Map of all objects: the map now fills the remaining height of the window (at least 400px) and follows when the window is resized, instead of a fixed height of 600px.

## 4.1.2

* Clean code: removed commented code and unused translation keys left from the Google module this module was derived from, unused includes and variables.
* Setup and about pages, card map page: use dol_get_fiche_head/dol_get_fiche_end, load_fiche_titre and isModEnabled() instead of deprecated functions.
* Card map page: the navigation to previous/next record now uses $user->socid (the old societe_id property no longer exists), and the tabs are also shown with mode=societe.
* The tab "Maps" of the setup has its own translation key.

## 4.1.1

* The entry "Map" in the menu of contacts is hidden by default (contacts usually share the address of their third party). New setup option to show it. The map pictogram on contact cards is not changed. Disable and enable the module so the menu is updated.

## 4.1.0

* Address autofill for countries other than the Netherlands: town from the postcode (Nominatim, one request per postcode) and street suggestions while typing the address (Photon). A postcode outside the Netherlands covers many streets, so the street is chosen from suggestions.
* Requests go through the Dolibarr server (ajax/address.php) and are cached in documents/openstreetmap/cache; Nominatim requests are limited to 1 per second for the whole server.
* New setup options: autofill for other countries on/off, URL of the Photon service.

## 4.0.4

* Multicompany: the table llx_openstreetmap_geo has a column entity; coordinates are read, written and cleared per company. Disable and enable the module after the upgrade so the column is added.
* Map of all records refused when the module is disabled.
* Setup of maps: values read with GETPOST and getDolGlobalInt, saved per company, zoom level limited to 2-18.
* No technical error text shown to the user (SQL errors and PDOK errors are written to the log).
* JSON of the markers encoded with JSON_HEX_* flags.

## 4.0.3

* The About page of the module setup works again on Dolibarr 18: dolPrintHTMLForAttribute() exists only from Dolibarr 19, dol_escape_htmltag() is used instead.

## 4.0.2

* Security fixes of DoliCloud master (e6f8112f) on maps.php: page refused when the module is disabled, permission check of the third party, contact or member, escaping of zip, town, country and state.
* Fixes reported by phpcs and phpstan (Dolibarr 18 and 24): category filter also on Dolibarr 18, getURLContent() called with the parameters of Dolibarr 18, typed PHPDoc.

## 4.0.1

* Dutch address autofill: visible label "Huisnr." before the house number field, so it is not mistaken for part of the zip code.

## 4.0

* Maps displayed with Leaflet (served from the module) instead of OpenLayers 2 loaded over http.
* New page with all third parties, contacts or members on one map, with filters and marker clustering.
* Geocoding with PDOK Locatieserver for the Netherlands and Nominatim (https, rate limited) for other countries.
* Coordinates are stored in table llx_openstreetmap_geo and searched again only when the address changes.
* Dutch address autofill: street and town from postcode and house number (PDOK), on third party, contact and member forms.
* New setup tab "Geocoding and autofill". Support of user cards. Dutch translation.

## 1.0

Initial version
