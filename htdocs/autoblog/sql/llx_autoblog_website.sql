-- Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
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


CREATE TABLE llx_autoblog_website(
	rowid INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
	entity INTEGER DEFAULT 1 NOT NULL,
	fk_website INTEGER NOT NULL,
	description TEXT,
	differentiators TEXT,
	target_audience TEXT,
	fk_country INTEGER,
	url_competitors TEXT,
	url_product_feed VARCHAR(255),
	url_sitemap VARCHAR(255),
	url_style_example VARCHAR(255),
	instructions TEXT,
	date_creation DATETIME NOT NULL,
	tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	fk_user_creat INTEGER NOT NULL,
	fk_user_modif INTEGER
) ENGINE=innodb;
