-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see http://www.gnu.org/licenses/.


CREATE TABLE llx_sellyoursaas_paymentmode(
   -- BEGIN MODULEBUILDER FIELDS
   rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
   fk_soc integer NOT NULL,
   --For credit card
   last_four varchar(4),
   card_type varchar(255),
   cvn varchar(255),
   exp_date timestamp,
   name varchar(100),
   unique_reference varchar(255),
   country_code varchar(10)
   --For paypal
   approved bit NOT NULL,
   email varchar(255),
   ending_date timestamp NOT NULL,
   max_total_amount_of_all_payments decimal(19,2),
   preapproval_key varchar(255),
   starting_date timestamp,
   total_amount_of_all_payments decimal(19,2)
   -- END MODULEBUILDER FIELDS
   status varchar(8) NOT NULL,
) ENGINE=innodb;

