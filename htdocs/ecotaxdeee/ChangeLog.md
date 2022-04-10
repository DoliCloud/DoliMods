# ChangeLog MODULE EXTERNALBACKUP FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


## 4.1.3

- Dolibarr v15 compatibility: Fix error CSRF in setup.


## 4.1.2

- Avoid duplicate line of ecotax when cloning an object with ecotax.
- Autofill the margin to zero if module margin is enabled on ecotax. When we update a quantity or price, we must also update the buyprice of the ecotax or the margin will be wrong.


## 4.1.1

- Use the VAT rate of the eco tax product instead of highest vat rate when a predefined product
  is defined for the eco tax line.


## 4.1.0

- Compatibility with Dolibarr 7.0


## 4.0.0

- Compatibility with Dolibarr 6.0


## 1.0

- Initial version

