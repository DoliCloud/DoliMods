# ChangeLog GOOGLE MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a> 


## 6.1.1 Not released

- NEW Add filter on type of thirdparty (customer/prospect/supplier) on maps.
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
