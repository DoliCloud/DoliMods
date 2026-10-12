# ChangeLog OVH MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


## 5.1 Unreleased

* FIX Compatibility Dolibarr 24 and PHP 8 for SMS sending:
  * Priority, class and delay are sent with a valid value or not sent (since PHP 8, an empty value was no more converted).
  * Recipients are converted into the international format (French national numbers become +33...) and several
    recipients separated with a comma or a semicolon are sent as several receivers.
  * Mass action "Send SMS" on contacts sends one SMS per contact (all numbers were sent as one invalid receiver).
  * Only one agenda event per SMS (the event recorded by the module trigger was a duplicate of the one recorded by
    Dolibarr), now linked to the contact of the thirdparty, to the member, and with a label not HTML encoded.
  * Invalid recipients returned by OVH are reported with a clear message.
  * Catch the error when no SMS engine is defined (error 500 on the SMS tabs of thirdparty and member).
  * The test page of SMS setup no more reports a success when the sending failed (Dolibarr 17 to 19).
  * Fix PHP warnings and fatal errors in SMS setup and SMS account pages (history of the selected account, errors of API).

## 5.0

* FIX OVH APIs make sometimes a response that is not correctly UTF8 encoded. A compensation has been introduce to ignore the OVH bug.
* FIX If dtfrom and dtto are inverted by OVH, we restore correct order
* FIX Compatibility Dolibarr 18
* FIX round replaced with price2num
* NEW Add a select all checkbox in the list
* NEW Add supplier and product selection in form
* NEW Add mass action send SMS
* NEW Can set end date for invoice import
* NEW OVH_FIX_API_BUGS_OF_QTY_IN_PRORATAS
* Better PHP 8.2 compatibility
* QUAL CLean code to follow Dolibarr precommit rules

## 4.1

* Update ovh lib to v2.1.0 (php 5.6+) - guzzle v6.5.5
* Add option OVH_DEBUG
* Support several OVH projects
* Fix blank page with Dolidroid
* Link the event to thirdparty history with Dolibarr v12 
* Compatibility with v11 (newToken).
* Add checkbox to exclude lines with null amount
* Can enable log into agenda of automatic action "Sent by SMS"

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
