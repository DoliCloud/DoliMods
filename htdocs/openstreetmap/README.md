# OPENSTREETMAP MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>

## Features
This module provides OpenStreet Map integration into Dolibarr, without any Google service and without API key:

* Add a pictogram near all addresses. A click on it show address using Openstreetmap.
* Add a view of location of all your third parties, contacts or members on Openstreetmap (menu entry "Map").
* Addresses in the Netherlands are located with the PDOK Locatieserver (Dutch government service using the BAG, free, no key). Other countries use Nominatim (OpenStreetMap), at most 1 request per second as required by its usage policy.
* Coordinates are stored in the database and searched again only when the address changes.
* For Dutch addresses, enter postcode and house number on third party, contact or member forms: street and town are filled automatically. The request is done by the Dolibarr server, the browser of the user never contacts an external service.
* Maps are displayed with Leaflet, served from the module itself (no external CDN).

## Upgrade from a previous version
Disable and enable the module again after the upgrade, so the table of stored coordinates is created.

## Third party libraries
* Leaflet 1.9.4 (BSD 2-Clause), includes/leaflet
* Leaflet.markercluster 1.5.3 (MIT), includes/markercluster
