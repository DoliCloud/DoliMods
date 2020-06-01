# ChangeLog OVH MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>

## 4.0.1 Unreleased

* Support several OVH projects
* Fix blank page with Dolidroid
* Link the event ot thirdparty history with Dolibarr v12 
* Compatibility with v11 (newToken).
* Add checkbox to exclude lines with null amount

## 4.0

* Compatibility PHP 7.1
* Record an event into agenda (except if MAIN_AGENDA_ACTIONAUTO_SENTBYSMS is set to 0)

## 3.9

* Add constant OVH_DEFAULT_BANK_ACCOUNT to set default bank account
* Payment condition and terms on imported invoices use the value on supplier if defined.
* Fix: can also select product that are not on sell as product for imported invoice lines.

## 3.8.4

* Can attach imported invoices to a project
* Add option OVH_VAT_RATE_ON_ONE_DIGIT to force rounding of vat rate on 1 digit and get 20 instead of 19.99
* Can set the OVHCONSUMERKEY from setup page

## 3.8.3

* Add view of cloud servers with ability to make snapshots.
* Add cron jobs to make snapshots frequently.
* Support option NOSTOP on SMS.

## 3.8.2

* More help.
* Set constant to log module.
* Add option OVH_CLICKTODIAL_NO_INTERCOM.

## 1.0

* Initial version.