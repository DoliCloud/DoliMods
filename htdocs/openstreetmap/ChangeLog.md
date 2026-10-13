# ChangeLog MODULE OPENSTREETMAP FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


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
