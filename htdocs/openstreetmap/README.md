# OPENSTREETMAP MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>

## Features
This module provides OpenStreet Map integration into Dolibarr, without any Google service and without API key:

* Add a pictogram near all addresses. A click on it show address using Openstreetmap.
* Add a view of location of all your third parties, contacts or members on Openstreetmap (menu entry "Map").
* Addresses in the Netherlands are located with the PDOK Locatieserver (Dutch government service using the BAG, free, no key). Other countries use Nominatim (OpenStreetMap), at most 1 request per second as required by its usage policy.
* Coordinates are stored in the database and searched again only when the address changes.
* For Dutch addresses, enter postcode and house number on third party, contact or member forms: street and town are filled automatically. The request is done by the Dolibarr server, the browser of the user never contacts an external service.
* For all countries: addresses (street, house number, postcode, town) are suggested while typing the address, with Photon on OpenStreetMap data by default (free), PDOK for the Netherlands, or optionally Postcode.eu with your own key. Outside the Netherlands the town is also filled from the postcode. Also through the Dolibarr server, with a cache.
* The dictionary of zip codes and towns of Dolibarr can be completed per country with the free postal codes of GeoNames, or with your own file. The module takes the town of a postcode from it first. Files are imported in the background by parts (scheduled job of the module, or while the setup page is open), with the progress in percent, so large files like NL_full (470 000 lines) do not block Dolibarr. A new version of a file can replace the previous one: the zip codes added before by the module and not in the new file are then removed, rows of other sources are kept.
* The map of all objects shows at once the addresses already located; the other ones are located afterwards in the background, one at a time, so Dolibarr stays responsive.
* Maps are displayed with Leaflet, served from the module itself (no external CDN).

## What to expect in each country
* Outside the Netherlands, addresses come from OpenStreetMap. How complete they are depends on the country: good in most of Western Europe, while house numbers or postcodes may be missing in some countries (for example Greece and parts of Eastern Europe). Then only the street is suggested and the house number is typed by hand.
* The public servers of Photon and Nominatim are free for fair use, without guarantee. Answers are stored, so each address is asked only once, and the map searches at most 25 new addresses per visit (OPENSTREETMAP_GEOCODE_PER_VISIT) unless "Locate all" is used. With many users, set the URL of your own Photon or Nominatim server in the setup: an own Nominatim server is asked without delay (OPENSTREETMAP_NOMINATIM_DELAY_MS to set one), and OPENSTREETMAP_PHOTON_LANGS lists the languages an own Photon server knows.
* In the Netherlands, PDOK (Dutch government, BAG) gives complete and up to date addresses.
* Postcode.eu is a paid service (own subscription and key, billed per search) covering 14 European countries. The addresses typed are then sent to Postcode.eu: a data processing agreement with this provider is needed (GDPR). Its answers are not stored by the module.
* Without any external service, the towns of postcodes can come from the dictionary of zip codes and towns of Dolibarr (tab "Zip codes and towns" of the setup).

## Upgrade from a previous version
Disable and enable the module again after the upgrade, so the table of stored coordinates is created.

## Third party libraries
* Leaflet 1.9.4 (BSD 2-Clause), includes/leaflet
* Leaflet.markercluster 1.5.3 (MIT), includes/markercluster

## Data
* Postal codes of GeoNames (www.geonames.org), Creative Commons Attribution 4.0, downloaded on request of the administrator.
