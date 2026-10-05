# ChangeLog MODULE OPENSTREETMAP FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


## 4.0

* Maps displayed with Leaflet (served from the module) instead of OpenLayers 2 loaded over http.
* New page with all third parties, contacts or members on one map, with filters and marker clustering.
* Geocoding with PDOK Locatieserver for the Netherlands and Nominatim (https, rate limited) for other countries.
* Coordinates are stored in table llx_openstreetmap_geo and searched again only when the address changes.
* Dutch address autofill: street and town from postcode and house number (PDOK), on third party, contact and member forms.
* New setup tab "Geocoding and autofill". Support of user cards. Dutch translation.

## 1.0

Initial version
