# MODULE SELLYOURSAAS FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


## Features
SellYourSaas is a module to complete your ERP CRM so it is able to manage and sell Saas application on line.
It covers definition of packages to sell, deployement of application on a remote server, a customer dashboard for
your subscribers and automatic invoicing and renewal.

This is a list of some features supported by this application:

- Can create profiles of packages to define what to deploy when a subscription to this package is done: files/directories to deploy, databases dump to load, cron entry to add,
SSH public keys of admin to deploy and any other command lines to launch.
- Create services that define the plan (which package and option) and price policy to use for invoicing: per application, per user, per Gigabytes or any combination of this.
- Support free trial period with no credit card required on some plans.
- Can define the SQL or CLI command for each service to define the quantity to bill (For example a sql request to count the number of customers).
- Provides URLs for the online subscription of a service.
- Can decide if customer has MySQL/MariaDB and/or restricted (or not) SSH access to its instance.
- Each customer has its own system and data environment (jail)
- Add a system layer to replace the php mail function to track and stop evil users using their instance to try to make Spams.  
- Autofill and autodetect country in the subscription page using Geoip.
- Include a probability of VPN usage for each subscriber (to fight against spammer).
- Manage a network of reseller with commission dedicated to each reseller (a reseller has its own URL to register/create a new instances of an application and any customer that use it to create its instance is linked to the reseller. Reseller will gain a commission for each invoice paid by the customer). 
- Provide a customer dashboard for customers to manage their subscription, download their invoice.
- Each customer can deploy more applications/services with their existing account.
- All customer, subscriptions (contracts), invoices are Dolibarr common documents shared with your existing workflow.
- Payment of customer can be done automatically by credit card using Stripe or by SEPA mandate.
- Billing rules (date, amount, frequency of next payment) can be modified differently for each customer.
- Provide a lot of predefined email templates in server languages for the subscription management (subscription, trial expiration,
cancellation, ...)
- Can manage each customer/subscription from Dolibarr backoffice (for example deploy, suspend, unsuspend, undeploy an instance).
- Provide statistics reports on trial instances, customers, etc.



Licenses
--------

### Main code

![GPLv3 logo](img/gplv3.png)

GPLv3 or (at your option) any later version.

See [COPYING](COPYING) for more information.


#### Documentation

All texts and readmes.

![GFDL logo](img/gfdl.png)
