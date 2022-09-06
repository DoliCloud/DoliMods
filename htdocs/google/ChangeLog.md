# ChangeLog GOOGLE MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a> 


## 6.5

- Compatible with API People for contact sync.
- Need Dolibarr 14+, PHP 7.0+


## 6.4

- NEW Use Google PHP API v2.9.1
- Better error message on error when generating the Oauth token
- Fix confusion between note/note_public/note_private into agenda events.


## 6.3

- NEW Add filter on status "Active" for map of thirdparties.
- NEW Compatibility with v12 (Need v11+)
- NEW Use json file for synchro of contacts instead of p12 file.
- NEW Use Google PHP API v2.7.0
- Min PHP is now 7.2


## 6.2

- NEW Add filter on status "Active" for thirdparties
- NEW Compatibility with v11
- NEW GOOGLE_DEBUG add also a file for each request
- Use the public note as note sync for events and contacts.
- FIX encoding of ' when pushing a google event


## 6.1.1

- NEW Add filter on type of thirdparty (customer/prospect/supplier) on maps.
- NEW Add filter on categories for members too.
- NEW Can sync contacts for thirdparties "Customers only".
- Need Dolibarr 3.9+


## 6.1

- NEW Add 2 variables to hange TZ offset between Dolibarr and Google
- NEW Add option GOOGLE_ENABLE_GMAPS_TICON to use different picto for customers and vendors
- NEW Add option GOOGLE_CAN_USE_PROSPECT_ICONS to use different picto for each prospect status
- FIX list of maps visible in widget.
- FIX link in popup of addresses in map.
- FIX Compatibility with multicompany module
- FIX Process record by rowid order in the mass push feature
- FIX The thirdparty added into label when pushing from Dolibarr to Google must be 
  removed when pulling from Google to Dolibarr.
- FIX option GOOGLE_ENABLE_GMAPS_TICON
- FIX Remove error gContact:groupMembershipInfo during update of thirdparty or contact


## 6.0

- FIX save of setup for agenda sync per user.


## 3.7

- Compatibility with Dolibarr 3.7 and +
- Add filter on sale representative.


## 1.0

Initial version.
