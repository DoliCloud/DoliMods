# ChangeLog MODULE OPENSTREETMAP FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


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
