-- Copyright (C) 2023 Alice Adminson <contact@doliasso.org>
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
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_alumni_survey(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	description text, 
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	import_key varchar(14), 
	firstname varchar(64) NOT NULL, 
	lastname varchar(64) NOT NULL, 
	lastname2 varchar(64), 
	email varchar(128), 
	phone varchar(20), 
	comwhatsapp integer, 
	comemail integer, 
	promodesortie integer, 
	lienavecpromo varchar(128), 
	preferencejour varchar(128), 
	optionsur2jours varchar(3), 
	activiteassociees varchar(32), 
	activiteassocieesnat varchar(32), 
	prtorganiser integer, 
	region varchar(32), 
	choixperimetre varchar(8), 
	budgetmaxactivitepar integer, 
	budgetmaxrepasparpers integer, 
	lieu varchar(24), 
	motivation varchar(16)
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
